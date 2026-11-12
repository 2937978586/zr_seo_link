<?php
/**
 * 孜然超级外链工具 —— 数据接口（V2 现代接口版）
 * 作者：孜然  QQ：2937978586  微信：ZR293797
 *
 * GET api.php?action=batch&dn=example.com&page=1&size=15   取一批外链
 * GET api.php?action=meta                                  取元信息（总数 / 可选参数）
 */

require __DIR__ . '/lib/common.php';

header('X-Content-Type-Options: nosniff');

$action = isset($_GET['action']) ? (string)$_GET['action'] : 'batch';

/* ---------------- 元信息 ---------------- */
if ($action === 'meta') {
    $links = zr_links();
    $hosts = [];
    foreach ($links as $tpl) {
        $h = zr_host_of($tpl);
        if ($h !== '') {
            $hosts[$h] = true;
        }
    }
    zr_json([
        'ok'           => true,
        'name'         => zr('site_name'),
        'author'       => zr('author'),
        'contact'      => [
            'qq'     => zr('qq'),
            'wechat' => zr('wechat'),
        ],
        'version'      => zr('version'),
        'total'        => count($links),
        'hosts'        => count($hosts),
        'size_options' => array_values((array)zr('size_options', [10, 15, 20, 30])),
        'wait_options' => array_values((array)zr('wait_options', [3, 5, 8, 12, 20])),
        'max_size'     => (int)zr('max_size', 30),
    ]);
}

/* ---------------- 批次数据 ---------------- */
if ($action !== 'batch') {
    zr_json(['ok' => false, 'msg' => '未知的 action 参数']);
}

list($domainOk, $domain) = zr_check_domain(isset($_GET['dn']) ? $_GET['dn'] : '');
if (!$domainOk) {
    zr_json(['ok' => false, 'msg' => $domain]);
}

$links = zr_links();
if (!$links) {
    zr_json(['ok' => false, 'msg' => '外链库为空：请检查 links.txt 是否存在且每行包含 *** 占位符']);
}
$size  = isset($_GET['size']) ? (int)$_GET['size'] : (int)zr('page_size', 15);
list($page, $size, $total, $pages, $offset, $slice) = zr_paginate(
    isset($_GET['page']) ? $_GET['page'] : 1,
    $size,
    $links
);

$done  = ($page > $pages);
$items = [];
foreach ($slice as $i => $tpl) {
    $url = zr_build_url($tpl, $domain);
    if (!zr_is_http_url($url)) {
        continue;
    }
    $items[] = [
        'id'   => $offset + $i + 1,
        'url'  => $url,
        'host' => zr_host_of($url),
    ];
}

zr_json([
    'ok'     => true,
    'domain' => $domain,
    'page'   => $page,
    'size'   => $size,
    'offset' => $offset,
    'total'  => $total,
    'pages'  => $pages,
    'done'   => $done,
    'items'  => $items,
]);
