<?php
/**
 * 公共函数库 —— 孜然超级外链工具
 * 作者：孜然  QQ：2937978586  微信：ZR293797
 *
 * 兼容 PHP 7.0+，不依赖任何框架与扩展（mbstring 可选）。
 */

if (!defined('ZR_EXT_LINK')) {
    define('ZR_EXT_LINK', true);
}

/**
 * 读取配置（静态缓存，只解析一次）
 */
function zr_config()
{
    static $cfg = null;
    if ($cfg === null) {
        $file = dirname(__DIR__) . '/config.php';
        $cfg  = is_file($file) ? require $file : [];
    }
    return $cfg;
}

/**
 * 取配置项
 */
function zr($key, $default = '')
{
    $cfg = zr_config();
    return array_key_exists($key, $cfg) ? $cfg[$key] : $default;
}

/**
 * HTML 转义
 */
function zr_e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * 读取外链库，返回模板数组（含 *** 占位符）
 */
function zr_links()
{
    static $cache = null;
    if (is_array($cache)) {
        return $cache;
    }

    $cache = [];
    $file  = (string)zr('links_file');

    if (!is_file($file) || !is_readable($file)) {
        return $cache;
    }

    $fp = fopen($file, 'rb');
    if (!$fp) {
        return $cache;
    }

    while (($line = fgets($fp)) !== false) {
        $line = trim($line);
        if ($line === '' || stripos($line, 'http') !== 0) {
            continue;
        }
        if (strpos($line, '***') === false) {
            continue;
        }
        $cache[] = $line;
    }
    fclose($fp);

    return $cache;
}

/**
 * 校验并规范化域名
 *
 * @return array [bool $ok, string $domainOrMessage]
 */
function zr_check_domain($raw)
{
    $domain = trim((string)$raw);

    // 宽容处理：用户复制了带协议的地址也认
    $domain = preg_replace('#^[a-z][a-z0-9+.\-]*://#i', '', $domain);
    $domain = trim($domain);

    // 去掉末尾斜杠
    $domain = rtrim($domain, '/');

    if ($domain === '') {
        return [false, '请输入需要推广的域名'];
    }
    if (strpbrk($domain, "/\\?#@ \t\n\r") !== false) {
        return [false, '只填域名即可，不要带路径、参数、空格或 @'];
    }
    if (strlen($domain) > 253) {
        return [false, '域名长度超出限制'];
    }

    $labels = explode('.', $domain);
    if (count($labels) < 2) {
        return [false, '域名格式不正确，例如：www.example.com'];
    }
    foreach ($labels as $label) {
        if ($label === '' || strlen($label) > 63) {
            return [false, '域名格式不正确，例如：www.example.com'];
        }
        if (!preg_match('/^[a-z0-9]([a-z0-9\-]*[a-z0-9])?$/i', $label)) {
            return [false, '域名只能包含字母、数字和短横线，且不能以短横线开头或结尾'];
        }
    }

    $tld = strtolower(end($labels));
    if (!preg_match('/^(?:[a-z]{2,24}|xn--[a-z0-9\-]{2,59})$/', $tld)) {
        return [false, '域名后缀不正确，例如：.com / .cn / .net'];
    }

    return [true, strtolower($domain)];
}

/**
 * 把模板里的 *** 替换成目标域名
 */
function zr_build_url($template, $domain)
{
    return str_replace('***', rawurlencode($domain), (string)$template);
}

/**
 * 是否是可安全渲染的 http(s) 地址
 */
function zr_is_http_url($url)
{
    $url = (string)$url;
    if (!preg_match('#^https?://#i', $url)) {
        return false;
    }
    $parts = parse_url($url);
    if (!$parts || empty($parts['host'])) {
        return false;
    }
    if (!empty($parts['user']) || !empty($parts['pass'])) {
        return false;
    }
    return true;
}

/**
 * 取 URL 主机名（展示用）
 */
function zr_host_of($url)
{
    $host = parse_url($url, PHP_URL_HOST);
    return $host ? strtolower($host) : '';
}

/**
 * 分页计算：返回 [page, size, total, pages, offset, slice]
 */
function zr_paginate($page, $size, array $items)
{
    $size  = (int)$size;
    $max   = (int)zr('max_size', 30);
    if ($size < 1) {
        $size = (int)zr('page_size', 12);
    }
    if ($size > $max) {
        $size = $max;
    }

    $total = count($items);
    $pages = $total > 0 ? (int)ceil($total / $size) : 1;

    $page = (int)$page;
    if ($page < 1) {
        $page = 1;
    }

    $offset = ($page - 1) * $size;
    $slice  = array_slice($items, $offset, $size);

    return [$page, $size, $total, $pages, $offset, $slice];
}

/**
 * 输出 JSON 并结束
 */
function zr_json($payload)
{
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
