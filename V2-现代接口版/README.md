# 孜然超级外链工具 · V2 现代接口版

作者：孜然　QQ：2937978586　微信：ZR293797

## 部署

1. 把本目录全部文件上传到站点根目录或二级目录；
2. 直接访问即可，无需数据库、无需安装；
3. 需要 PHP 7.0+；Nginx 需自行加禁止访问 `links.txt`、`config.php` 的规则。

本地预览：

```bash
php -S 127.0.0.1:8000
```

## 文件说明

| 文件 | 作用 |
| --- | --- |
| `index.php` | 推广台单页：侧栏 + 控制台 + 实时表格 + 运行日志 |
| `api.php` | JSON 接口：`?action=meta` 元信息，`?action=batch&dn=&page=&size=` 取一批外链 |
| `config.php` | 站点名、作者、QQ、微信、二维码、批次参数与下拉选项 |
| `lib/common.php` | 域名校验、外链库读取、分页 |
| `assets/js/app.js` | 无刷新调度、进度环、暂停 / 继续 / 跳过 / 停止 |
| `images/` | 两张二维码，同名覆盖即可替换 |
| `links.txt` | 外链库，一行一条，用 `***` 作域名占位符 |

## 接口示例

```bash
curl "https://你的域名/api.php?action=meta"
curl "https://你的域名/api.php?action=batch&dn=www.example.com&page=1&size=15"
```

返回：

```json
{"ok":true,"domain":"www.example.com","page":1,"size":15,"offset":0,
 "total":3112,"pages":208,"done":false,
 "items":[{"id":1,"url":"https://...","host":"..."}]}
```

## 可调参数（config.php）

```php
'page_size'    => 15,              // 默认每批条数
'page_wait'    => 5,               // 默认批次间隔（秒）
'size_options' => [10, 15, 20, 30],
'wait_options' => [3, 5, 8, 12, 20],
```

## 注意

- 「加载模式」选「标准」会在后台真实打开每个查询页（推荐）；选「仅链接」只生成地址不打开，适合省流量场景；
- 保持标签页在前台，浏览器会限制后台定时器。
