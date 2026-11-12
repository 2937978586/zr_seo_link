/*!
 * 孜然超级外链工具 · 推广台脚本（V2 现代接口版）
 * 作者：孜然  QQ：2937978586  微信：ZR293797
 */
(function () {
    'use strict';

    var CFG = window.ZR || {};
    var $ = function (id) { return document.getElementById(id); };

    var el = {
        dn: $('dn'), size: $('size'), wait: $('wait'), mode: $('speed'),
        start: $('startBtn'), pause: $('pauseBtn'), skip: $('skipBtn'), stop: $('stopBtn'),
        state: $('runState'),
        ringPct: $('ringPct'), ringValue: $('ringValue'), ringCap: $('ringCap'),
        statTotal: $('statTotal'), statSent: $('statSent'), statPage: $('statPage'), statLeft: $('statLeft'),
        rows: $('rows'), logs: $('logs'), logCount: $('logCount'), tableTag: $('tableTag'),
        toastBox: $('toastBox')
    };

    var CIRC = 2 * Math.PI * 72;
    var S = {
        running: false,
        paused: false,
        finished: false,
        domain: '',
        page: 1,
        size: parseInt(CFG.size, 10) || 15,
        wait: parseInt(CFG.wait, 10) || 5,
        total: parseInt(CFG.total, 10) || 0,
        pages: 0,
        sent: 0,
        timer: null,
        ticker: null,
        remain: 0,
        mode: '1',
        logs: 0,
        loading: false
    };

    if (el.ringValue) {
        el.ringValue.setAttribute('stroke-dasharray', CIRC.toFixed(2));
        el.ringValue.setAttribute('stroke-dashoffset', CIRC.toFixed(2));
    }

    /* ---------------- 工具函数 ---------------- */
    function pad(n) { return n < 10 ? '0' + n : '' + n; }

    function nowText() {
        var d = new Date();
        return pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
    }

    function num(n) {
        return String(n).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    function toast(msg, type) {
        if (!el.toastBox) {
            return;
        }
        var node = document.createElement('div');
        node.className = 'toast' + (type ? ' ' + type : '');
        node.textContent = msg;
        el.toastBox.appendChild(node);
        setTimeout(function () {
            node.style.opacity = '0';
            node.style.transition = 'opacity .3s ease';
            setTimeout(function () { node.remove(); }, 320);
        }, 2800);
    }

    function log(text, type) {
        if (!el.logs) {
            return;
        }
        var item = document.createElement('div');
        item.className = 'log-item' + (type ? ' ' + type : '');
        var time = document.createElement('time');
        time.textContent = nowText();
        var span = document.createElement('span');
        span.textContent = text;
        item.appendChild(time);
        item.appendChild(span);
        el.logs.insertBefore(item, el.logs.firstChild);
        while (el.logs.children.length > 80) {
            el.logs.removeChild(el.logs.lastChild);
        }
        S.logs += 1;
        if (el.logCount) {
            el.logCount.textContent = S.logs + ' 条';
        }
    }

    function checkDomain(raw) {
        var d = String(raw || '').trim().replace(/^[a-z][a-z0-9+.\-]*:\/\//i, '').replace(/\/+$/, '');
        if (!d) {
            return '请输入需要推广的域名';
        }
        if (/[\/\\?#@\s]/.test(d)) {
            return '只填域名即可，不要带路径、参数或空格';
        }
        var labels = d.split('.');
        if (labels.length < 2) {
            return '域名格式不正确，例如：www.example.com';
        }
        for (var i = 0; i < labels.length; i++) {
            if (!labels[i] || labels[i].length > 63 || !/^[a-z0-9]([a-z0-9\-]*[a-z0-9])?$/i.test(labels[i])) {
                return '域名只能包含字母、数字和短横线';
            }
        }
        if (!/^(?:[a-z]{2,24}|xn--[a-z0-9\-]{2,59})$/i.test(labels[labels.length - 1])) {
            return '域名后缀不正确，例如：.com / .cn';
        }
        return '';
    }

    /* ---------------- 视图 ---------------- */
    function setState(text, live) {
        if (!el.state) {
            return;
        }
        el.state.textContent = text;
        var chip = el.state.parentNode;
        if (chip) {
            chip.style.opacity = live === false ? '.65' : '1';
        }
    }

    function setRing(percent) {
        var pct = Math.max(0, Math.min(100, percent || 0));
        if (el.ringValue) {
            el.ringValue.setAttribute('stroke-dashoffset', (CIRC * (1 - pct / 100)).toFixed(2));
        }
        if (el.ringPct) {
            el.ringPct.innerHTML = Math.round(pct) + '<span>%</span>';
        }
    }

    function updateStats() {
        if (el.statTotal) {
            el.statTotal.textContent = num(S.total || 0);
        }
        if (el.statSent) {
            el.statSent.textContent = num(S.sent);
        }
        if (el.statPage) {
            el.statPage.innerHTML = (S.pages ? S.page : '—') + ' <em>/ ' + (S.pages || '—') + '</em>';
        }
        if (el.statLeft) {
            el.statLeft.textContent = S.pages ? num(Math.max(0, S.pages - S.page + 1)) + ' 批' : '—';
        }
        if (el.tableTag) {
            el.tableTag.textContent = S.running ? ('已提交 ' + num(S.sent) + ' / ' + num(S.total)) : (S.sent ? ('共 ' + num(S.sent) + ' 条') : '尚未开始');
        }
        setRing(S.total ? (S.sent / S.total * 100) : 0);
        if (el.ringCap) {
            el.ringCap.textContent = S.finished ? '已完成本轮推广' : (S.running ? (S.paused ? '已暂停' : '正在提交') : '等待开始');
        }
    }

    function setControls() {
        if (el.start) {
            el.start.disabled = S.running;
            el.start.textContent = S.running ? '推广中…' : '开始推广';
        }
        if (el.pause) {
            el.pause.disabled = !S.running;
            el.pause.textContent = S.paused ? '继续' : '暂停';
        }
        if (el.skip) {
            el.skip.disabled = !S.running || S.paused;
        }
        if (el.stop) {
            el.stop.disabled = !S.running;
        }
        if (el.dn) {
            el.dn.disabled = S.running;
        }
        if (el.size) {
            el.size.disabled = S.running;
        }
        if (el.wait) {
            el.wait.disabled = S.running;
        }
        if (el.mode) {
            el.mode.disabled = S.running;
        }
    }

    function clearTimers() {
        if (S.timer) {
            window.clearTimeout(S.timer);
            S.timer = null;
        }
        if (S.ticker) {
            window.clearInterval(S.ticker);
            S.ticker = null;
        }
    }

    function clearRows(message) {
        if (!el.rows) {
            return;
        }
        el.rows.innerHTML = '';
        var tr = document.createElement('tr');
        var td = document.createElement('td');
        td.colSpan = 4;
        var box = document.createElement('div');
        box.className = 'empty';
        box.innerHTML = message || '<b>还没有开始</b>填写域名后点击「开始推广」，这里会实时显示每一条外链的提交状态。';
        td.appendChild(box);
        tr.appendChild(td);
        el.rows.appendChild(tr);
    }

    function markState(id, text, cls) {
        var node = el.rows ? el.rows.querySelector('.js-state[data-id="' + id + '"]') : null;
        if (node) {
            node.textContent = text;
            node.className = 'pill js-state ' + cls;
        }
    }

    /* ---------------- 渲染一批 ---------------- */
    function render(data) {
        if (!el.rows || !data.items || !data.items.length) {
            return;
        }
        el.rows.innerHTML = '';

        data.items.forEach(function (item) {
            var tr = document.createElement('tr');

            var tdId = document.createElement('td');
            tdId.className = 'col-id';
            tdId.textContent = item.id;
            tr.appendChild(tdId);

            var tdUrl = document.createElement('td');
            tdUrl.className = 'col-url';
            var a = document.createElement('a');
            a.href = item.url;
            a.target = '_blank';
            a.rel = 'nofollow noopener noreferrer';
            a.title = item.url;
            a.textContent = item.url;
            tdUrl.appendChild(a);

            if (S.mode === '1') {
                var frame = document.createElement('iframe');
                frame.className = 'probe';
                frame.src = item.url;
                frame.setAttribute('loading', 'eager');
                frame.setAttribute('referrerpolicy', 'no-referrer');
                frame.setAttribute('tabindex', '-1');
                frame.setAttribute('aria-hidden', 'true');
                frame.setAttribute('title', '');
                tdUrl.appendChild(frame);

                var settled = false;
                var settle = function (text, cls) {
                    if (settled) {
                        return;
                    }
                    settled = true;
                    markState(item.id, text, cls);
                };
                frame.addEventListener('load', function () { settle('已发送', 'pill-ok'); });
                frame.addEventListener('error', function () { settle('已发送', 'pill-ok'); });
                window.setTimeout(function () { settle('已发出', 'pill-ok'); }, 8000);
            }
            tr.appendChild(tdUrl);

            var tdHost = document.createElement('td');
            tdHost.className = 'col-host';
            tdHost.textContent = item.host;
            tr.appendChild(tdHost);

            var tdState = document.createElement('td');
            tdState.className = 'col-state';
            var pill = document.createElement('span');
            pill.className = 'pill js-state' + (S.mode === '1' ? ' pill-wait' : '');
            pill.setAttribute('data-id', item.id);
            pill.textContent = S.mode === '1' ? '发送中' : '已生成';
            tdState.appendChild(pill);
            tr.appendChild(tdState);

            el.rows.appendChild(tr);
        });
    }

    /* ---------------- 取一批 ---------------- */
    function loadBatch() {
        if (!S.running || S.loading) {
            return;
        }
        S.loading = true;
        setState('正在获取第 ' + S.page + ' 批…');

        var params = 'action=batch'
            + '&dn=' + encodeURIComponent(S.domain)
            + '&page=' + S.page
            + '&size=' + S.size;

        fetch((CFG.api || 'api.php') + '?' + params, { cache: 'no-store', credentials: 'same-origin' })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                S.loading = false;
                if (!data || data.ok !== true) {
                    throw new Error((data && data.msg) ? data.msg : '接口返回异常');
                }
                S.total = data.total;
                S.pages = data.pages;

                if (data.done) {
                    finish();
                    return;
                }

                render(data);
                S.sent += (data.items || []).length;
                updateStats();
                log('第 ' + data.page + ' / ' + data.pages + ' 批已提交（' + (data.items || []).length + ' 条）');
                scheduleNext();
            })
            .catch(function (err) {
                S.loading = false;
                setState('出错，已暂停');
                S.paused = true;
                setControls();
                log('请求失败：' + (err && err.message ? err.message : '网络错误'), 'err');
                toast((err && err.message) ? err.message : '网络错误，请检查 api.php 是否可访问', 'err');
            });
    }

    /* ---------------- 调度 ---------------- */
    function scheduleNext() {
        if (!S.running || S.paused) {
            return;
        }
        clearTimers();
        S.remain = S.wait;
        setState('第 ' + S.page + ' 批完成，' + S.remain + ' 秒后继续');

        S.ticker = window.setInterval(function () {
            if (S.paused || !S.running) {
                return;
            }
            S.remain -= 1;
            if (S.remain > 0) {
                setState('第 ' + S.page + ' 批完成，' + S.remain + ' 秒后继续');
            }
        }, 1000);

        S.timer = window.setTimeout(function () {
            clearTimers();
            if (!S.running || S.paused) {
                return;
            }
            S.page += 1;
            loadBatch();
        }, S.wait * 1000);
    }

    /* ---------------- 开始 / 完成 / 停止 ---------------- */
    function start() {
        if (S.running) {
            return;
        }
        var msg = checkDomain(el.dn ? el.dn.value : '');
        if (msg) {
            toast(msg, 'err');
            if (el.dn) {
                el.dn.focus();
            }
            return;
        }

        S.domain = String(el.dn.value).trim().replace(/^[a-z][a-z0-9+.\-]*:\/\//i, '').replace(/\/+$/, '').toLowerCase();
        S.size = parseInt(el.size.value, 10) || 15;
        S.wait = parseInt(el.wait.value, 10) || 5;
        S.mode = el.mode ? el.mode.value : '1';
        S.page = 1;
        S.sent = 0;
        S.pages = 0;
        S.paused = false;
        S.finished = false;
        S.running = true;
        S.remain = S.wait;

        if (el.dn) {
            el.dn.value = S.domain;
        }
        clearRows('<b>正在准备第 1 批…</b>稍候，正在从外链库读取链接。');
        setRing(0);
        setState('启动中');
        setControls();
        updateStats();
        log('开始推广 ' + S.domain + '（每批 ' + S.size + ' 条，间隔 ' + S.wait + ' 秒）', 'ok');
        toast('已开始推广 ' + S.domain, 'ok');

        loadBatch();
    }

    function finish() {
        clearTimers();
        S.running = false;
        S.paused = false;
        S.finished = true;
        S.sent = S.total;
        setState('本轮已完成');
        setRing(100);
        updateStats();
        setControls();
        clearRows('<b>本轮推广已完成</b>共提交 ' + num(S.total) + ' 条外链，接下来交给搜索引擎抓取即可。建议隔几个小时再来一轮。');
        log('全部完成，共提交 ' + num(S.total) + ' 条外链', 'ok');
        toast('推广完成，共提交 ' + num(S.total) + ' 条外链', 'ok');
    }

    function stop() {
        if (!S.running) {
            return;
        }
        clearTimers();
        S.running = false;
        S.paused = false;
        setState('已停止');
        setControls();
        updateStats();
        log('已手动停止，共提交 ' + num(S.sent) + ' 条', 'err');
        toast('已停止本轮推广', 'err');
    }

    function togglePause() {
        if (!S.running) {
            return;
        }
        S.paused = !S.paused;
        if (S.paused) {
            clearTimers();
            setState('已暂停');
            log('已暂停（剩余 ' + S.remain + ' 秒后本应继续）');
        } else {
            log('继续推广');
            scheduleNext();
        }
        setControls();
        updateStats();
    }

    function skip() {
        if (!S.running || S.paused) {
            return;
        }
        clearTimers();
        S.page += 1;
        loadBatch();
    }

    function reset() {
        stop();
        clearRows();
        setRing(0);
        S.sent = 0;
        S.page = 1;
        S.pages = 0;
        S.finished = false;
        setState('待启动');
        updateStats();
    }

    /* ---------------- 事件 ---------------- */
    if (el.start) {
        el.start.addEventListener('click', function () {
            if (S.finished) {
                reset();
                start();
                return;
            }
            start();
        });
    }
    if (el.pause) {
        el.pause.addEventListener('click', togglePause);
    }
    if (el.skip) {
        el.skip.addEventListener('click', skip);
    }
    if (el.stop) {
        el.stop.addEventListener('click', function () {
            if (window.confirm('确定要停止本轮推广吗？已提交的记录会保留。')) {
                stop();
            }
        });
    }
    if (el.dn) {
        el.dn.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (!S.running) {
                    start();
                }
            }
        });
    }

    document.addEventListener('visibilitychange', function () {
        if (document.hidden && S.running && !S.paused) {
            log('标签页切到后台，浏览器可能限制定时器，建议切回前台', 'err');
        }
    });

    window.addEventListener('beforeunload', function () {
        clearTimers();
    });

    /* 初始化 */
    setControls();
    updateStats();
})();
