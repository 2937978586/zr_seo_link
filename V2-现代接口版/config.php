<?php
/**
 * 孜然超级外链工具 · 全站配置（V2 现代接口版）
 *
 * 作者：孜然    QQ：2937978586    微信：ZR293797
 *
 * 品牌信息、二维码、批次参数统一放在本文件，改这里即全站生效。
 */

return [
    // ---------- 站点信息 ----------
    'site_name'    => '孜然超级外链工具',
    'site_short'   => '孜然外链',
    'site_tagline' => '接口驱动 · 无刷新批量提交',
    'site_desc'    => '孜然超级外链工具（接口版）：前端无刷新批量提交网址到上千个查询站点，实时进度、可暂停可调速，帮助搜索引擎更快发现你的网站。',

    // ---------- 作者信息 ----------
    'author'       => '孜然',
    'qq'           => '2937978586',
    'qq_chat_url'  => 'https://wpa.qq.com/msgrd?v=3&uin=2937978586&site=qq&menu=yes',
    'wechat'       => 'ZR293797',
    'email'        => '2937978586@qq.com',
    'qq_group'     => '',

    // ---------- 二维码（预留位，替换图片即可） ----------
    'qr_wechat'       => 'images/qr-wechat.png',
    'qr_wechat_text'  => '微信公众号',
    'qr_wechat_tip'   => '扫码关注，工具更新第一时间推送',
    'qr_group'        => 'images/qr-qqgroup.png',
    'qr_group_text'   => 'QQ 官方群',
    'qr_group_tip'    => '扫码进群，站长交流 / 问题反馈',

    // ---------- 关联站点 ----------
    'toolbox_url'  => 'https://tool.zrv7.com/',
    'whois_url'    => 'https://tool.zrv7.com/whois/',
    'icp'          => '桂ICP备2025055193号',
    'icp_url'      => 'https://beian.miit.gov.cn/',

    // ---------- 运行参数 ----------
    'links_file'   => __DIR__ . '/links.txt',
    'page_size'    => 15,      // 每批默认条数
    'max_size'     => 30,      // 每批上限
    'page_wait'    => 5,       // 每批默认停留秒数
    'wait_options' => [3, 5, 8, 12, 20],
    'size_options' => [10, 15, 20, 30],
    'version'      => '2.0.0',
];
