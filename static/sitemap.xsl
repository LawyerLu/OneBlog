<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:s="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    <xsl:output method="html" encoding="UTF-8" indent="yes" />

    <xsl:template match="/">
        <html lang="zh-CN">
            <head>
                <meta charset="UTF-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <title>Sitemap - OneBlog</title>
                <style>
                    :root {
                        color-scheme: light dark;
                        --bg: #f6f8fb;
                        --card: #ffffff;
                        --text: #1f2937;
                        --muted: #64748b;
                        --border: #e5e7eb;
                        --accent: #2563eb;
                        --accent-soft: #dbeafe;
                    }
                    @media (prefers-color-scheme: dark) {
                        :root {
                            --bg: #0f172a;
                            --card: #111827;
                            --text: #e5e7eb;
                            --muted: #94a3b8;
                            --border: #243244;
                            --accent: #60a5fa;
                            --accent-soft: rgba(96, 165, 250, .18);
                        }
                    }
                    * { box-sizing: border-box; }
                    body {
                        margin: 0;
                        padding: 32px 18px;
                        background: radial-gradient(circle at top left, var(--accent-soft), transparent 32rem), var(--bg);
                        color: var(--text);
                        font: 15px/1.65 -apple-system, BlinkMacSystemFont, "Segoe UI", "PingFang SC", "Microsoft YaHei", sans-serif;
                    }
                    .wrap { max-width: 1180px; margin: 0 auto; }
                    .hero {
                        margin-bottom: 22px;
                        padding: 28px;
                        border: 1px solid var(--border);
                        border-radius: 24px;
                        background: color-mix(in srgb, var(--card) 88%, transparent);
                        box-shadow: 0 20px 50px rgba(15, 23, 42, .08);
                    }
                    h1 { margin: 0 0 8px; font-size: clamp(28px, 5vw, 44px); letter-spacing: -.04em; }
                    p { margin: 0; color: var(--muted); }
                    .stats { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 18px; }
                    .pill {
                        display: inline-flex;
                        align-items: center;
                        gap: 8px;
                        padding: 8px 12px;
                        border-radius: 999px;
                        background: var(--accent-soft);
                        color: var(--accent);
                        font-weight: 700;
                    }
                    .card {
                        overflow: hidden;
                        border: 1px solid var(--border);
                        border-radius: 20px;
                        background: var(--card);
                        box-shadow: 0 12px 34px rgba(15, 23, 42, .06);
                    }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { padding: 14px 16px; border-bottom: 1px solid var(--border); text-align: left; vertical-align: top; }
                    th { background: color-mix(in srgb, var(--accent-soft) 55%, transparent); color: var(--muted); font-size: 12px; letter-spacing: .08em; text-transform: uppercase; }
                    tr:last-child td { border-bottom: 0; }
                    tr:hover td { background: color-mix(in srgb, var(--accent-soft) 35%, transparent); }
                    a { color: var(--accent); text-decoration: none; word-break: break-all; }
                    a:hover { text-decoration: underline; }
                    .num { width: 70px; color: var(--muted); font-variant-numeric: tabular-nums; }
                    .date, .freq, .priority, .images { white-space: nowrap; color: var(--muted); }
                    .badge { display: inline-block; min-width: 28px; padding: 2px 8px; border-radius: 999px; background: var(--accent-soft); color: var(--accent); text-align: center; font-weight: 700; }
                    @media (max-width: 760px) {
                        body { padding: 18px 10px; }
                        .hero { padding: 20px; border-radius: 18px; }
                        table, thead, tbody, th, td, tr { display: block; }
                        thead { display: none; }
                        tr { padding: 12px 14px; border-bottom: 1px solid var(--border); }
                        tr:last-child { border-bottom: 0; }
                        td { padding: 4px 0; border: 0; }
                        td::before { content: attr(data-label); display: block; color: var(--muted); font-size: 12px; font-weight: 700; }
                    }
                </style>
            </head>
            <body>
                <main class="wrap">
                    <section class="hero">
                        <h1>Sitemap</h1>
                        <p>这是为搜索引擎生成的网站地图；样式仅用于浏览器阅读，不影响搜索引擎抓取。</p>
                        <div class="stats">
                            <span class="pill">URL 总数：<xsl:value-of select="count(s:urlset/s:url)" /></span>
                            <span class="pill">图片条目：<xsl:value-of select="count(s:urlset/s:url/image:image)" /></span>
                        </div>
                    </section>
                    <section class="card">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>URL</th>
                                    <th>最后更新</th>
                                    <th>频率</th>
                                    <th>权重</th>
                                    <th>图片</th>
                                </tr>
                            </thead>
                            <tbody>
                                <xsl:for-each select="s:urlset/s:url">
                                    <tr>
                                        <td class="num" data-label="#"><xsl:value-of select="position()" /></td>
                                        <td data-label="URL"><a href="{s:loc}"><xsl:value-of select="s:loc" /></a></td>
                                        <td class="date" data-label="最后更新"><xsl:value-of select="s:lastmod" /></td>
                                        <td class="freq" data-label="频率"><xsl:value-of select="s:changefreq" /></td>
                                        <td class="priority" data-label="权重"><xsl:value-of select="s:priority" /></td>
                                        <td class="images" data-label="图片"><span class="badge"><xsl:value-of select="count(image:image)" /></span></td>
                                    </tr>
                                </xsl:for-each>
                            </tbody>
                        </table>
                    </section>
                </main>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
