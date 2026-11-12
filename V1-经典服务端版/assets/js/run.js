/*!
 * 孜然超级外链工具 · 运行页脚本（V1 经典服务端版）
 * 作者：孜然  QQ：2937978586  微信：ZR293797
 */
(function () {
    'use strict';

    var RUN = window.ZR_RUN || {};
    var wait = parseInt(RUN.wait, 10);
    if (isNaN(wait) || wait < 1) {
        wait = 12;
    }

    var cdEl = document.getElementById('countdown');
    var notice = document.getElementById('countdownNotice');
    var pill = document.getElementById('statePill');
    var pauseBtn = document.getElementById('pauseBtn');
    var nextBtn = document.getElementById('nextBtn');
    var stopBtn = document.getElementById('stopBtn');

    var remain = wait;
    var paused = false;
    var jumping = false;
    var timer = null;

    /* ---------- 状态标记 ---------- */
    function setState(id, text, cls) {
        var el = document.querySelector('.js-state[data-id="' + id + '"]');
        if (!el) {
            return;
        }
        el.textContent = text;
        el.className = 'pill js-state ' + cls;
    }

    var probes = document.querySelectorAll('iframe.probe');
    Array.prototype.forEach.call(probes, function (frame) {
        var id = frame.getAttribute('data-id');
        var settled = false;
        var mark = function (text, cls) {
            if (settled) {
                return;
            }
            settled = true;
            setState(id, text, cls);
        };
        frame.addEventListener('load', function () {
            mark('已发送', 'pill-ok');
        });
        frame.addEventListener('error', function () {
            mark('已发送', 'pill-ok');
        });
        // 部分站点会长时间挂起，超时也按已发送处理（请求确实已发出）
        setTimeout(function () {
            mark('已发出', 'pill-ok');
        }, 7000);
    });

    /* ---------- 倒计时 ---------- */
    function render() {
        if (cdEl) {
            cdEl.textContent = remain;
        }
    }

    function goNext() {
        if (jumping) {
            return;
        }
        jumping = true;
        if (pill) {
            pill.textContent = '即将翻页';
        }
        document.body.style.opacity = '0.75';
        window.location.href = RUN.nextUrl || 'data.php';
    }

    function tick() {
        if (paused || jumping) {
            return;
        }
        remain -= 1;
        render();
        if (remain <= 0) {
            window.clearInterval(timer);
            goNext();
        } else if (remain <= 3 && notice) {
            notice.classList.add('is-warn');
        }
    }

    render();
    timer = window.setInterval(tick, 1000);

    /* ---------- 交互 ---------- */
    if (pauseBtn) {
        pauseBtn.addEventListener('click', function () {
            paused = !paused;
            pauseBtn.textContent = paused ? '继续倒计时' : '暂停倒计时';
            pauseBtn.classList.toggle('btn-soft', !paused);
            pauseBtn.classList.toggle('btn-ghost', paused);
            if (pill) {
                pill.textContent = paused ? '已暂停' : '运行中';
                pill.className = 'pill ' + (paused ? 'pill-err' : 'pill-wait');
            }
            if (notice && paused) {
                notice.classList.remove('is-warn');
            }
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            window.clearInterval(timer);
            goNext();
        });
    }

    if (stopBtn) {
        stopBtn.addEventListener('click', function (e) {
            if (!window.confirm('确定要停止本轮推广吗？已提交的记录会保留，但进度将不再继续。')) {
                e.preventDefault();
                return;
            }
            window.clearInterval(timer);
        });
    }

    /* 页面切回前台时，如果倒计时早该结束，立即翻页 */
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden && !paused && !jumping) {
            var elapsed = Math.max(0, wait - remain);
            if (elapsed >= wait) {
                window.clearInterval(timer);
                goNext();
            }
        }
    });

    window.addEventListener('beforeunload', function () {
        window.clearInterval(timer);
    });
})();
