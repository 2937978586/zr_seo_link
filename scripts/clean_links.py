#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
外链库清洗脚本 —— 孜然超级外链工具
作者：孜然  QQ：2937978586  微信：ZR293797

用法：
    python3 scripts/clean_links.py links.txt
    python3 scripts/clean_links.py V2-现代接口版/links.txt

做的事：
    1. 去空白行、修 hhttp:// 这类笔误；
    2. 丢掉没有 *** 占位符、主机名写成 ***、缺协议的行；
    3. 剔除旧作者域名残留（lusongsong / qianliurl 等）；
    4. 同一条链接去重，同一地址的 http/https 只保留 https；
    5. 按站点轮询打散，让相邻两条尽量来自不同站点；
    6. 原文件自动备份为 links.txt.bak。
"""

import io
import os
import re
import sys
import collections

BAD_WORDS = ("lusongsong", "luosngsong", "qianliurl", "\u3010", "zhi66", "zhiyun", "wlhlauth")


def host_of(url):
    return re.sub(r"^https?://", "", url).split("/")[0].lower()


def clean_line(raw):
    s = raw.strip().lstrip("\ufeff")
    if not s:
        return None
    if s.lower().startswith("hhttp://"):
        s = s[1:]
    low = s.lower()
    if any(word in low for word in BAD_WORDS):
        return None
    if not (low.startswith("http://") or low.startswith("https://")):
        return None
    if re.search(r"[\s<>\"'\\]", s):
        return None
    m = re.match(r"^(https?://)([^/]+)(.*)$", s)
    if not m:
        return None
    host, rest = m.group(2).lower(), m.group(3)
    if "***" in host or "." not in host or host.startswith(".") or host.endswith("."):
        return None
    if "***" not in rest:
        return None
    return s


def main():
    path = sys.argv[1] if len(sys.argv) > 1 else "links.txt"
    if not os.path.isfile(path):
        print("[x] 找不到文件：%s" % path)
        return 1

    with io.open(path, encoding="utf-8", errors="replace") as fh:
        raw_lines = fh.readlines()

    seen = collections.OrderedDict()
    dropped = collections.Counter()

    for raw in raw_lines:
        url = clean_line(raw)
        if url is None:
            dropped["invalid"] += 1
            continue
        key = re.sub(r"^https?://", "", url).lower().rstrip("/")
        is_https = url.lower().startswith("https://")
        if key not in seen or (is_https and not seen[key].lower().startswith("https://")):
            if key in seen:
                dropped["scheme-dup"] += 1
            seen[key] = url
        else:
            dropped["dup"] += 1

    by_host = collections.OrderedDict()
    for url in seen.values():
        by_host.setdefault(host_of(url), []).append(url)

    final, index = [], 0
    while True:
        added = False
        for urls in by_host.values():
            if index < len(urls):
                final.append(urls[index])
                added = True
        if not added:
            break
        index += 1

    backup = path + ".bak"
    if not os.path.exists(backup):
        with io.open(backup, "w", encoding="utf-8", newline="\n") as fh:
            fh.write("".join(raw_lines))

    with io.open(path, "w", encoding="utf-8", newline="\n") as fh:
        fh.write("\n".join(final) + "\n")

    print("输入行数：%d" % len(raw_lines))
    print("丢弃：无效 %d / 重复 %d / 协议重复 %d" % (dropped["invalid"], dropped["dup"], dropped["scheme-dup"]))
    print("输出：%d 条，%d 个站点" % (len(final), len(by_host)))
    print("备份：%s" % backup)
    return 0


if __name__ == "__main__":
    sys.exit(main())
