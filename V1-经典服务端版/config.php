<?php
/**
 * 孜然超级外链工具 · 全站配置（V1 经典服务端版）
 *
 * 作者：孜然    QQ：2937978586    微信：ZR293797
 *
 * 所有品牌信息、二维码、分页参数都集中在本文件，改这一处即可全站生效。
 * 注意：本文件被 PHP 包含执行，请勿直接通过浏览器访问（.htaccess 已做拦截）。
 */

return [
    // ---------- 站点信息 ----------
    'site_name'    => '孜然超级外链工具',
    'site_short'   => '孜然外链',
    'site_tagline' => '批量提交 · 自动留痕 · 引蜘蛛提收录',
    'site_desc'    => '孜然超级外链工具：一键把网址批量提交到上千个查询类站点，自动留下外链记录，帮助搜索引擎蜘蛛更快发现你的网站。',

    // ---------- 作者信息 ----------
    'author'       => '孜然',
    'qq'           => '2937978586',
    'qq_chat_url'  => 'https://wpa.qq.com/msgrd?v=3&uin=2937978586&site=qq&menu=yes',
    'wechat'       => 'ZR293797',
    'email'        => '2937978586@qq.com',
    'qq_group'     => '',                       // QQ 群号（可留空，二维码照样展示）

    // ---------- 二维码（预留位，替换图片即可） ----------
    'qr_wechat'       => 'images/qr-wechat.png',   // 微信公众号二维码
    'qr_wechat_text'  => '微信公众号',
    'qr_wechat_tip'   => '扫码关注，工具更新第一时间推送',
    'qr_group'        => 'images/qr-qqgroup.png',  // QQ 官方群二维码
    'qr_group_text'   => 'QQ 官方群',
    'qr_group_tip'    => '扫码进群，站长交流 / 问题反馈',

    // ---------- 关联站点 ----------
    'toolbox_url'  => 'https://tool.zrv7.com/',    // 更多工具
    'whois_url'    => 'https://tool.zrv7.com/whois/',
    'icp'          => '桂ICP备2025055193号',
    'icp_url'      => 'https://beian.miit.gov.cn/',

    // ---------- 运行参数 ----------
    'links_file'   => __DIR__ . '/links.txt',      // 外链库
    'page_size'    => 12,                          // 每批提交条数（建议 10~20）
    'max_size'     => 30,                          // 每批上限，防止一次性请求过多
    'page_wait'    => 12,                          // 每批停留秒数（倒计时结束自动翻页）
    'version'      => '1.0.0',
];
