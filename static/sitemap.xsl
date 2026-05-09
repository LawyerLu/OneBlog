<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    <xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes" />

    <xsl:template match="/">
        <html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
            <head>
                <title>XML Sitemap</title>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <style type="text/css">
                    body {
                        margin: 8px;
                        color: #333;
                        background: #fff;
                        font-family: "Lucida Grande", "Lucida Sans Unicode", Tahoma, Verdana, "PingFang SC", "Microsoft YaHei", sans-serif;
                        font-size: 13px;
                    }

                    h1 {
                        margin: 10px;
                        color: #222;
                        font-size: 24px;
                        font-weight: normal;
                    }

                    #intro {
                        margin: 10px;
                        padding: 5px 13px;
                        border: 1px solid #2580B2;
                        background-color: #CFEBF7;
                    }

                    #intro p {
                        margin: 8px 0;
                        line-height: 16.8667px;
                    }

                    #content {
                        margin: 10px;
                    }

                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }

                    th {
                        padding: 5px 30px 5px 5px;
                        border-bottom: 1px solid #000;
                        text-align: left;
                        font-size: 11px;
                    }

                    td {
                        padding: 5px;
                        font-size: 11px;
                        vertical-align: top;
                    }

                    tr.even {
                        background-color: #f5f5f5;
                    }

                    a {
                        color: #000;
                        text-decoration: underline;
                    }

                    .url {
                        word-break: break-all;
                    }

                    #footer {
                        margin: 10px;
                        padding: 2px;
                        color: gray;
                        font-size: 8pt;
                    }

                    #footer a {
                        color: gray;
                    }
                </style>
            </head>
            <body>
                <h1>XML Sitemap</h1>
                <div id="intro">
                    <p>
                        This is an XML Sitemap generated for search engines. It is styled only to make browser viewing easier.<br />
                        You can find more information about XML sitemaps on <a href="https://www.sitemaps.org/">sitemaps.org</a>.
                    </p>
                    <p>
                        Total URLs: <xsl:value-of select="count(sitemap:urlset/sitemap:url)" />
                        <xsl:if test="count(sitemap:urlset/sitemap:url/image:image) &gt; 0">
                            | Image entries: <xsl:value-of select="count(sitemap:urlset/sitemap:url/image:image)" />
                        </xsl:if>
                    </p>
                </div>
                <div id="content">
                    <table cellpadding="5">
                        <tr>
                            <th>URL</th>
                            <th>Priority</th>
                            <th>Change Frequency</th>
                            <th>LastChange</th>
                            <th>Images</th>
                        </tr>
                        <xsl:variable name="lower" select="'abcdefghijklmnopqrstuvwxyz'" />
                        <xsl:variable name="upper" select="'ABCDEFGHIJKLMNOPQRSTUVWXYZ'" />
                        <xsl:for-each select="sitemap:urlset/sitemap:url">
                            <tr>
                                <xsl:if test="position() mod 2 = 0">
                                    <xsl:attribute name="class">even</xsl:attribute>
                                </xsl:if>
                                <td class="url">
                                    <xsl:variable name="itemURL"><xsl:value-of select="sitemap:loc" /></xsl:variable>
                                    <a href="{$itemURL}"><xsl:value-of select="sitemap:loc" /></a>
                                </td>
                                <td><xsl:value-of select="concat(sitemap:priority * 100, '%')" /></td>
                                <td>
                                    <xsl:value-of select="concat(translate(substring(sitemap:changefreq, 1, 1), $lower, $upper), substring(sitemap:changefreq, 2))" />
                                </td>
                                <td>
                                    <xsl:value-of select="substring(sitemap:lastmod, 1, 10)" />
                                    <xsl:if test="string-length(sitemap:lastmod) &gt; 10">
                                        <xsl:value-of select="concat(' ', substring(sitemap:lastmod, 12, 5))" />
                                    </xsl:if>
                                </td>
                                <td><xsl:value-of select="count(image:image)" /></td>
                            </tr>
                        </xsl:for-each>
                    </table>
                </div>
                <div id="footer">
                    Generated by OneBlog.
                </div>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
