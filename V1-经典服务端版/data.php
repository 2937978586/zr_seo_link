<?php
/**
 * 孜然超级外链工具 —— 批量提交页（V1 经典服务端版）
 * 作者：孜然  QQ：2937978586  微信：ZR293797
 *
 * 参数：
 *   dn    目标域名（必填）
 *   p     当前批次，从 1 开始
 *   size  每批条数（可选，默认取 config.php 的 page_size）
 *   fmt   fmt=json 时输出 JSON 数据（便于二次开发）
 */

require __DIR__ . '/lib/common.php';
require __DIR__ . '/lib/layout.php';

$rawDomain = isset($_GET['dn']) ? $_GET['dn'] : '';
list($domainOk, $domain) = zr_check_domain($rawDomain);

$links = zr_links();
$size  = isset($_GET['size']) ? (int)$_GET['size'] : (int)zr('page_size', 12);
list($page, $size, $total, $pages, $offset, $slice) = zr_paginate(
    isset($_GET['p']) ? $_GET['p'] : 1,
    $size,
    $links
);

$wait = max(3, (int)zr('page_wait', 12));

if ($total === 0 && !(isset($_GET['fmt']) && $_GET['fmt'] === 'json')) {
    zr_page_start('外链库为空', '', 'run');
    echo '<main class="wrap"><section class="card"><div class="done"><div class="done-icon" style="background:linear-gradient(135deg,#f97316,#ef4444)">!</div>'
       . '<h2>外链库为空</h2><p>没有读到任何外链模板，请检查 <code>links.txt</code> 是否存在、是否有读取权限，以及每行是否包含 <code>***</code> 占位符。</p>'
       . '<div class="done-actions"><a class="btn" href="./">返回首页</a></div></div></section></main>';
    zr_page_end();
    exit;
}
$done = ($page > $pages);

/* ---------------- JSON 输出 ---------------- */
if (isset($_GET['fmt']) && $_GET['fmt'] === 'json') {
    if (!$domainOk) {
        zr_json(['ok' => false, 'msg' => $domain]);
    }
    $items = [];
    foreach ($slice as $i => $tpl) {
        $url = zr_build_url($tpl, $domain);
        $items[] = ['id' => $offset + $i + 1, 'url' => $url, 'host' => zr_host_of($url)];
    }
    zr_json([
        'ok'     => true,
        'domain' => $domain,
        'page'   => $page,
        'size'   => $size,
        'total'  => $total,
        'pages'  => $pages,
        'done'   => $done,
        'items'  => $items,
    ]);
}

/* ---------------- 域名非法 ---------------- */
if (!$domainOk) {
    zr_page_start('域名有误', '', 'run');
    ?>
<main class="wrap">
  <section class="card">
    <div class="done" style="padding:56px 24px">
      <div class="done-icon" style="background:linear-gradient(135deg,#f97316,#ef4444);box-shadow:0 18px 36px -14px rgba(239,68,68,.8)">!</div>
      <h2>域名填写有误</h2>
      <p><?php echo zr_e($domain); ?></p>
      <div class="done-actions">
        <a class="btn" href="./">返回重新输入</a>
      </div>
    </div>
  </section>
</main>
    <?php
    zr_page_end();
    exit;
}

/* ---------------- 跑完 ---------------- */
if ($done) {
    zr_page_start('推广完成', '', 'run');
    $cost = (int)round($pages * $wait / 60);
    ?>
<main class="wrap">
  <section class="card">
    <div class="done">
      <div class="done-icon">✓</div>
      <h2>本例外链已全部提交完成</h2>
      <p>目标域名 <strong><?php echo zr_e($domain); ?></strong> 已访问完外链库中的全部站点，接下来交给搜索引擎抓取即可。</p>
      <div class="done-stats">
        <div class="done-stat"><b><?php echo number_format($total); ?></b><span>已提交外链</span></div>
        <div class="done-stat"><b><?php echo number_format($pages); ?></b><span>完成批次</span></div>
        <div class="done-stat"><b><?php echo (int)$cost; ?></b><span>耗时（分钟）</span></div>
      </div>
      <div class="done-actions">
        <a class="btn" href="data.php?dn=<?php echo urlencode($domain); ?>&amp;p=1&amp;size=<?php echo (int)$size; ?>">再来一轮</a>
        <a class="btn btn-ghost" href="./">换个域名</a>
        <a class="btn btn-soft" href="<?php echo zr_e(zr('toolbox_url')); ?>" target="_blank" rel="noopener">更多工具</a>
      </div>
      <p class="tips" style="margin-top:22px">建议：新站每天 1~2 次，连续一周左右观察收录变化；不要短时间反复跑同一个域名。</p>
    </div>
  </section>
</main>
    <?php
    zr_page_end();
    exit;
}

/* ---------------- 正常批次 ---------------- */
$percent  = $total > 0 ? min(100, (int)round($offset / $total * 100)) : 0;
$nextUrl  = 'data.php?dn=' . urlencode($domain) . '&p=' . ($page + 1) . '&size=' . $size;

zr_page_start($domain . ' · 第 ' . $page . ' / ' . $pages . ' 批', '', 'run');
?>
<main class="wrap">

  <section class="card">
    <div class="run-head">
      <span class="run-title">正在推广</span>
      <span class="run-domain"><?php echo zr_e($domain); ?></span>
      <span class="pill pill-wait" id="statePill">运行中</span>
      <div class="spacer">
        <button class="btn btn-sm btn-soft" type="button" id="pauseBtn">暂停倒计时</button>
        <button class="btn btn-sm btn-ghost" type="button" id="nextBtn">立即下一批</button>
        <a class="btn btn-sm btn-danger" href="./" id="stopBtn">停止</a>
      </div>
    </div>

    <div class="progress-line">
      <div class="progress-top">
        <span>总进度：第 <b><?php echo (int)$page; ?></b> / <?php echo (int)$pages; ?> 批 · 已提交 <b><?php echo (int)min($offset + count($slice), $total); ?></b> / <?php echo number_format($total); ?> 条</span>
        <span><b><?php echo (int)$percent; ?>%</b></span>
      </div>
      <div class="bar"><i style="width:<?php echo (int)$percent; ?>%"></i></div>
    </div>

    <div class="notice" id="countdownNotice">
      <span>本批 <?php echo count($slice); ?> 条链接正在后台打开，</span>
      <span><b id="countdown"><?php echo (int)$wait; ?></b> 秒后自动进入下一批</span>
      <span class="tips">（请不要关闭或切换到后台标签页）</span>
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
        <?php foreach ($slice as $i => $tpl):
            $url = zr_build_url($tpl, $domain);
            if (!zr_is_http_url($url)) {
                continue;
            }
            $host = zr_host_of($url);
            $id   = $offset + $i + 1;
        ?>
          <tr>
            <td class="col-id"><?php echo (int)$id; ?></td>
            <td class="col-url">
              <a href="<?php echo zr_e($url); ?>" target="_blank" rel="nofollow noopener noreferrer" title="<?php echo zr_e($url); ?>"><?php echo zr_e($url); ?></a>
              <iframe class="probe" src="<?php echo zr_e($url); ?>" data-id="<?php echo (int)$id; ?>"
                      loading="eager" referrerpolicy="no-referrer" tabindex="-1" aria-hidden="true" title=""></iframe>
            </td>
            <td class="col-host"><?php echo zr_e($host); ?></td>
            <td class="col-state"><span class="pill pill-wait js-state" data-id="<?php echo (int)$id; ?>">发送中</span></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="card-body" style="border-top:1px solid var(--line)">
      <p class="tips" style="margin:0">
        说明：后台会真实打开每一条查询地址，站点记录下你的域名之后，搜索引擎抓到即为一条外链。
        若浏览器提示「已阻止加载不安全内容」，属于 HTTPS 页面加载 HTTP 站点的正常拦截，跳过即可。
      </p>
    </div>
  </section>

  <div class="layout" style="margin-top:22px">
    <div class="card">
      <div class="card-head">
        <span class="icon">提</span>
        <h2>本轮提示</h2>
      </div>
      <div class="card-body">
        <ul style="margin-bottom:0">
          <li>进度以批次为单位保存，刷新会从当前批次重新开始，不会丢失已提交的记录。</li>
          <li>「立即下一批」可跳过倒计时，网络较快时用它提速。</li>
          <li>跑完后建议换一批时间再来，避免同一时间段重复提交同一域名。</li>
        </ul>
      </div>
    </div>
    <aside class="side">
      <section class="card">
        <div class="card-head"><span class="icon">码</span><h2>扫码关注</h2></div>
        <div class="card-body">
          <div class="qr-grid">
            <?php
              zr_qr_card(zr('qr_wechat'), zr('qr_wechat_text'), zr('qr_wechat_tip'));
              zr_qr_card(zr('qr_group'), zr('qr_group_text'), zr('qr_group_tip'));
            ?>
          </div>
        </div>
      </section>
    </aside>
  </div>

</main>

<script>
window.ZR_RUN = {
    nextUrl: <?php echo json_encode($nextUrl); ?>,
    wait: <?php echo (int)$wait; ?>,
    last: <?php echo ($page >= $pages) ? 'true' : 'false'; ?>
};
</script>
<script src="assets/js/run.js"></script>

<?php zr_page_end(); ?>
