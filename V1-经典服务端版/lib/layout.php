<?php
/**
 * 页面骨架 —— 孜然超级外链工具（V1 经典服务端版）
 * 作者：孜然  QQ：2937978586  微信：ZR293797
 */

/**
 * 输出 <head> 与顶栏
 *
 * @param string $title  页面标题（不含站点名）
 * @param string $desc   页面描述
 * @param string $active 当前导航：index / run
 */
function zr_page_start($title, $desc = '', $active = 'index')
{
    $site    = zr('site_name', '孜然超级外链工具');
    $full    = $title ? $title . ' - ' . $site : $site;
    $desc    = $desc !== '' ? $desc : (string)zr('site_desc');
    $author  = zr('author');
    $version = zr('version');
    ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="renderer" content="webkit">
<meta name="force-rendering" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title><?php echo zr_e($full); ?></title>
<meta name="keywords" content="超级外链,外链工具,SEO外链,批量外链,外链发布,蜘蛛池,孜然工具箱">
<meta name="description" content="<?php echo zr_e($desc); ?>">
<meta name="author" content="<?php echo zr_e($author); ?>">
<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Cdefs%3E%3ClinearGradient id='g' x1='0' y1='0' x2='1' y2='1'%3E%3Cstop offset='0' stop-color='%232563eb'/%3E%3Cstop offset='1' stop-color='%2306b6d4'/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width='64' height='64' rx='16' fill='url(%23g)'/%3E%3Cpath d='M22 41h20M25 33h14M28 25h8' stroke='%23fff' stroke-width='5' stroke-linecap='round'/%3E%3C/svg%3E">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="topbar">
  <div class="topbar-inner">
    <a class="brand" href="./" title="<?php echo zr_e($site); ?>">
      <span class="brand-mark">ZR</span>
      <span class="brand-text">
        <strong>孜然<em>超级外链</em></strong>
        <small>SEO 外链批量提交 · V<?php echo zr_e($version); ?></small>
      </span>
    </a>
    <nav class="nav">
      <a class="<?php echo $active === 'index' ? 'active' : ''; ?>" href="./">外链工具</a>
      <a href="<?php echo zr_e(zr('toolbox_url')); ?>" target="_blank" rel="noopener">更多工具</a>
      <a href="<?php echo zr_e(zr('whois_url')); ?>" target="_blank" rel="noopener">Whois 查询</a>
      <a href="#author">联系作者</a>
    </nav>
    <a class="topbar-cta" href="<?php echo zr_e(zr('qq_chat_url')); ?>" target="_blank" rel="nofollow noopener">QQ <?php echo zr_e(zr('qq')); ?></a>
  </div>
</header>
<?php
}

/**
 * 截取前 N 个字符（无 mbstring 时按字节兜底）
 */
function zr_short($text, $len = 4)
{
    $text = (string)$text;
    if (function_exists('mb_substr')) {
        return mb_substr($text, 0, $len, 'UTF-8');
    }
    return substr($text, 0, $len * 3);
}

/**
 * 单个二维码卡片（图片缺失时自动显示占位块）
 */
function zr_qr_card($src, $caption, $tip)
{
    ?>
<div class="qr-card">
  <div class="qr-img">
    <span class="qr-ph"><?php echo zr_e(zr_short($caption, 4)); ?></span>
    <img src="<?php echo zr_e($src); ?>" alt="<?php echo zr_e($caption); ?>二维码" loading="lazy"
         onload="this.parentNode.classList.add('has-img')"
         onerror="this.remove()">
  </div>
  <b><?php echo zr_e($caption); ?></b>
  <small><?php echo zr_e($tip); ?></small>
</div>
    <?php
}

/**
 * 输出页脚与收尾标签
 */
function zr_page_end()
{
    $site = zr('site_name', '孜然超级外链工具');
    ?>
<footer class="footer" id="author">
  <div class="footer-inner">
    <div class="footer-brand">
      <div class="footer-brand-top">
        <span class="brand-mark">ZR</span>
        <strong><?php echo zr_e($site); ?></strong>
      </div>
      <p>
        把网址批量提交到上千个查询类站点，自动留下查询记录，供搜索引擎蜘蛛抓取收录。
        工具只是辅助，新站建议每天 1~2 次、老站以优质内容与友情链接为主。
      </p>
      <div class="footer-author">
        <span>作者：<b><?php echo zr_e(zr('author')); ?></b></span>
        <span>QQ：<a href="<?php echo zr_e(zr('qq_chat_url')); ?>" target="_blank" rel="nofollow noopener"><b><?php echo zr_e(zr('qq')); ?></b></a></span>
        <span>微信：<b><?php echo zr_e(zr('wechat')); ?></b></span>
        <?php if (zr('qq_group') !== '') : ?>
        <span>Q群：<b><?php echo zr_e(zr('qq_group')); ?></b></span>
        <?php endif; ?>
        <span>邮箱：<a href="mailto:<?php echo zr_e(zr('email')); ?>"><?php echo zr_e(zr('email')); ?></a></span>
      </div>
    </div>
    <div class="footer-qr">
      <?php
        zr_qr_card(zr('qr_wechat'), zr('qr_wechat_text'), zr('qr_wechat_tip'));
        zr_qr_card(zr('qr_group'), zr('qr_group_text'), zr('qr_group_tip'));
      ?>
    </div>
  </div>
  <div class="footer-bottom">
    <span>© <?php echo date('Y'); ?> <?php echo zr_e($site); ?> · 保留所有权利</span>
    <span>作者 <?php echo zr_e(zr('author')); ?> · QQ <?php echo zr_e(zr('qq')); ?> · 微信 <?php echo zr_e(zr('wechat')); ?></span>
    <?php if (zr('icp') !== '') : ?>
    <a href="<?php echo zr_e(zr('icp_url')); ?>" target="_blank" rel="nofollow noopener"><?php echo zr_e(zr('icp')); ?></a>
    <?php endif; ?>
  </div>
</footer>
<div class="toast-box" id="toastBox"></div>
</body>
</html>
    <?php
}
