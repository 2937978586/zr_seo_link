<?php
/**
 * 孜然超级外链工具 —— 推广台首页（V2 现代接口版）
 * 作者：孜然  QQ：2937978586  微信：ZR293797
 */

require __DIR__ . '/lib/common.php';

$links = zr_links();
$total = count($links);

$hosts = [];
foreach ($links as $tpl) {
    $h = zr_host_of($tpl);
    if ($h !== '') {
        $hosts[$h] = true;
    }
}
$hostTotal = count($hosts);

$sizeOptions = (array)zr('size_options', [10, 15, 20, 30]);
$waitOptions = (array)zr('wait_options', [3, 5, 8, 12, 20]);
$defSize     = (int)zr('page_size', 15);
$defWait     = (int)zr('page_wait', 5);
$site        = zr('site_name', '孜然超级外链工具');

$jsConfig = [
    'api'      => 'api.php',
    'size'     => $defSize,
    'wait'     => $defWait,
    'maxSize'  => (int)zr('max_size', 30),
    'total'    => $total,
    'version'  => zr('version'),
];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="renderer" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title><?php echo zr_e($site); ?> · 在线批量外链推广台</title>
<meta name="keywords" content="超级外链,外链工具,SEO外链,批量外链,外链发布,蜘蛛池,孜然工具箱">
<meta name="description" content="<?php echo zr_e(zr('site_desc')); ?>">
<meta name="author" content="<?php echo zr_e(zr('author')); ?>">
<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Cdefs%3E%3ClinearGradient id='g' x1='0' y1='0' x2='1' y2='1'%3E%3Cstop offset='0' stop-color='%2322d3ee'/%3E%3Cstop offset='1' stop-color='%238b5cf6'/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width='64' height='64' rx='16' fill='url(%23g)'/%3E%3Cpath d='M22 41h20M25 33h14M28 25h8' stroke='%23041018' stroke-width='5' stroke-linecap='round'/%3E%3C/svg%3E">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="app">

  <aside class="rail">
    <a class="rail-brand" href="./">
      <span class="rail-mark">ZR</span>
      <span>
        <strong>孜然超级外链</strong>
        <small>V<?php echo zr_e(zr('version')); ?> · 接口驱动</small>
      </span>
    </a>

    <nav class="rail-nav">
      <a class="active" href="./"><span class="dot"></span>推广台</a>
      <a href="<?php echo zr_e(zr('toolbox_url')); ?>" target="_blank" rel="noopener"><span class="dot"></span>更多工具</a>
      <a href="<?php echo zr_e(zr('whois_url')); ?>" target="_blank" rel="noopener"><span class="dot"></span>Whois 查询</a>
      <a href="api.php?action=meta" target="_blank" rel="noopener"><span class="dot"></span>接口文档</a>
    </nav>

    <div class="rail-block rail-author-block">
      <h3>关于作者</h3>
      <div class="rail-author">
        <div class="rail-avatar">孜</div>
        <div>
          <b><?php echo zr_e(zr('author')); ?></b>
          <span>开发者 / 站长</span>
        </div>
      </div>
      <div class="rail-contact">
        <div><span>QQ</span><a href="<?php echo zr_e(zr('qq_chat_url')); ?>" target="_blank" rel="nofollow noopener"><b><?php echo zr_e(zr('qq')); ?></b></a></div>
        <div><span>微信</span><b><?php echo zr_e(zr('wechat')); ?></b></div>
        <div><span>邮箱</span><a href="mailto:<?php echo zr_e(zr('email')); ?>"><b><?php echo zr_e(zr('email')); ?></b></a></div>
      </div>
    </div>

    <div class="rail-block rail-qr-block">
      <h3>扫码关注</h3>
      <div class="rail-qr">
        <div class="qr-mini">
          <div class="qr-img">
            <span class="qr-ph">公众号</span>
            <img src="<?php echo zr_e(zr('qr_wechat')); ?>" alt="<?php echo zr_e(zr('qr_wechat_text')); ?>二维码" loading="lazy"
                 onload="this.parentNode.classList.add('has-img')" onerror="this.remove()">
          </div>
          <span class="cap"><?php echo zr_e(zr('qr_wechat_text')); ?></span>
        </div>
        <div class="qr-mini">
          <div class="qr-img">
            <span class="qr-ph">QQ群</span>
            <img src="<?php echo zr_e(zr('qr_group')); ?>" alt="<?php echo zr_e(zr('qr_group_text')); ?>二维码" loading="lazy"
                 onload="this.parentNode.classList.add('has-img')" onerror="this.remove()">
          </div>
          <span class="cap"><?php echo zr_e(zr('qr_group_text')); ?></span>
        </div>
      </div>
    </div>

    <div class="rail-foot">
      © <?php echo date('Y'); ?> <?php echo zr_e($site); ?><br>
      <?php if (zr('icp') !== '') : ?>
      <a href="<?php echo zr_e(zr('icp_url')); ?>" target="_blank" rel="nofollow noopener"><?php echo zr_e(zr('icp')); ?></a><br>
      <?php endif; ?>
      作者 <?php echo zr_e(zr('author')); ?> · 微信 <?php echo zr_e(zr('wechat')); ?>
    </div>
  </aside>

  <main class="workspace">

    <div class="topline">
      <div>
        <h1>SEO <em>超级外链</em> 推广台</h1>
        <p>输入域名，一键把网址批量投递到 <?php echo number_format($hostTotal); ?> 个站长查询站点，让蜘蛛自己找上门。</p>
      </div>
      <div class="topline-right">
        <span class="chip-live"><i></i><span id="runState">待启动</span></span>
      </div>
    </div>

    <section class="panel">
      <div class="panel-head">
        <h2>开始推广</h2>
        <span class="spacer">
          <span class="tag">外链库 <?php echo number_format($total); ?> 条</span>
          <span class="tag">站点 <?php echo number_format($hostTotal); ?> 个</span>
          <span class="tag">免费</span>
        </span>
      </div>
      <div class="panel-body">
        <div class="start-row">
          <div class="input-group">
            <span class="input-prefix">https://</span>
            <input type="text" id="dn" maxlength="253" autocomplete="off" spellcheck="false"
                   placeholder="www.example.com（只填域名，不带 http:// 和末尾斜杠）">
          </div>
          <button class="btn" id="startBtn" type="button">开始推广</button>
        </div>

        <div class="settings">
          <div class="field">
            <label>每批条数</label>
            <select id="size">
              <?php foreach ($sizeOptions as $opt) : ?>
              <option value="<?php echo (int)$opt; ?>"<?php echo (int)$opt === $defSize ? ' selected' : ''; ?>><?php echo (int)$opt; ?> 条 / 批</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="field">
            <label>批次间隔</label>
            <select id="wait">
              <?php foreach ($waitOptions as $opt) : ?>
              <option value="<?php echo (int)$opt; ?>"<?php echo (int)$opt === $defWait ? ' selected' : ''; ?>><?php echo (int)$opt; ?> 秒</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="field">
            <label>加载模式</label>
            <select id="speed">
              <option value="1">标准（后台打开）</option>
              <option value="2">仅链接（省流量）</option>
            </select>
          </div>
        </div>

        <div class="actions">
          <button class="btn btn-ghost btn-sm" id="pauseBtn" type="button" disabled>暂停</button>
          <button class="btn btn-ghost btn-sm" id="skipBtn" type="button" disabled>跳过等待</button>
          <button class="btn btn-danger btn-sm" id="stopBtn" type="button" disabled>停止</button>
          <span class="hint">保持标签页在前台，浏览器会限制后台定时器；跑完一轮建议隔几个小时再来。</span>
        </div>
      </div>
    </section>

    <div class="dash">
      <section class="panel ring-card">
        <div class="ring">
          <svg width="168" height="168" viewBox="0 0 168 168">
            <defs>
              <linearGradient id="ringGrad" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0" stop-color="#22d3ee"/>
                <stop offset="1" stop-color="#8b5cf6"/>
              </linearGradient>
            </defs>
            <circle class="track" cx="84" cy="84" r="72" fill="none" stroke-width="10"/>
            <circle class="value" id="ringValue" cx="84" cy="84" r="72" fill="none" stroke-width="10"
                    stroke-dasharray="452.39" stroke-dashoffset="452.39"/>
          </svg>
          <div class="ring-center">
            <b id="ringPct">0<span>%</span></b>
            <small>总进度</small>
          </div>
        </div>
        <div class="ring-cap" id="ringCap">等待开始</div>
      </section>

      <section class="panel" style="padding:16px">
        <div class="stat-grid">
          <div class="stat is-cyan"><span>外链总数</span><b id="statTotal"><?php echo number_format($total); ?></b></div>
          <div class="stat is-ok"><span>已提交</span><b id="statSent">0</b></div>
          <div class="stat is-violet"><span>当前批次</span><b id="statPage">— <em>/ —</em></b></div>
          <div class="stat"><span>剩余批次</span><b id="statLeft">—</b></div>
        </div>
      </section>
    </div>

    <section class="panel">
      <div class="panel-head">
        <h2>提交结果</h2>
        <span class="spacer"><span class="tag" id="tableTag">尚未开始</span></span>
      </div>
      <div class="table-wrap">
        <table class="tbl">
          <thead>
            <tr>
              <th class="col-id">ID</th>
              <th class="col-url">外链目标网址</th>
              <th class="col-host">站点</th>
              <th class="col-state">状态</th>
            </tr>
          </thead>
          <tbody id="rows">
            <tr>
              <td colspan="4">
                <div class="empty">
                  <b>还没有开始</b>
                  填写域名后点击「开始推广」，这里会实时显示每一条外链的提交状态。
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <section class="panel">
      <div class="panel-head">
        <h2>运行日志</h2>
        <span class="spacer"><span class="tag" id="logCount">0 条</span></span>
      </div>
      <div class="panel-body">
        <div class="log-list" id="logs">
          <div class="log-item"><time>--:--:--</time><span>等待启动任务</span></div>
        </div>
      </div>
    </section>

    <section class="panel">
      <div class="panel-head"><h2>使用说明</h2></div>
      <div class="panel-body">
        <div style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(260px,1fr))">
          <div>
            <h3 style="margin:0 0 8px;font-size:14px;color:var(--ink)">原理</h3>
            <p style="margin:0;font-size:13.5px">站长查询站会把「查过什么域名」记录下来并生成页面，搜索引擎抓到这些页面就形成了外链。本工具批量替你打开这些查询地址，省掉手工点击。</p>
          </div>
          <div>
            <h3 style="margin:0 0 8px;font-size:14px;color:var(--ink)">建议频率</h3>
            <p style="margin:0;font-size:13.5px">新站每天 1~2 次，连续一周左右观察收录；老站每天 1 次即可，重心仍应放在内容与优质友链上。短时间反复刷同一域名没有额外收益。</p>
          </div>
          <div>
            <h3 style="margin:0 0 8px;font-size:14px;color:var(--ink)">常见问题</h3>
            <p style="margin:0;font-size:13.5px">部分 http 站点会被浏览器的混合内容策略拦截，属于正常现象；切换「仅链接」模式可减少流量消耗，但不会在后台打开页面。</p>
          </div>
        </div>
      </div>
    </section>

    <footer class="foot">
      <span>© <?php echo date('Y'); ?> <?php echo zr_e($site); ?></span>
      <span class="sep"></span>
      <span>作者 <b style="color:var(--ink)"><?php echo zr_e(zr('author')); ?></b></span>
      <span>QQ <a href="<?php echo zr_e(zr('qq_chat_url')); ?>" target="_blank" rel="nofollow noopener"><?php echo zr_e(zr('qq')); ?></a></span>
      <span>微信 <?php echo zr_e(zr('wechat')); ?></span>
      <span class="sep"></span>
      <a href="<?php echo zr_e(zr('toolbox_url')); ?>" target="_blank" rel="noopener">更多工具</a>
      <?php if (zr('icp') !== '') : ?>
      <span class="sep"></span>
      <a href="<?php echo zr_e(zr('icp_url')); ?>" target="_blank" rel="nofollow noopener"><?php echo zr_e(zr('icp')); ?></a>
      <?php endif; ?>
    </footer>

  </main>
</div>

<div class="toast-box" id="toastBox"></div>

<script>
window.ZR = <?php echo json_encode($jsConfig, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
</script>
<script src="assets/js/app.js"></script>
</body>
</html>
