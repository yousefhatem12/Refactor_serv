<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
                xmlns:html="http://www.w3.org/TR/REC-html40"
                xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
                xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
                xmlns:xhtml="http://www.w3.org/1999/xhtml"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>
  <xsl:template match="/">
    <html xmlns="http://www.w3.org/1999/xhtml" lang="en">
      <head>
        <title>XML Sitemap | Serv5</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <style type="text/css">
          body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #0a0a0a;
            color: #ededed;
            margin: 0;
            padding: 40px 20px;
          }
          .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
          }
          h1 {
            color: #ffffff;
            margin-top: 0;
            font-size: 28px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
          }
          h1 span {
            color: #ff8a00;
          }
          p.description {
            color: #a0a0a0;
            line-height: 1.6;
            margin-bottom: 30px;
            font-size: 15px;
          }
          .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 15px;
            margin-bottom: 40px;
          }
          .stat-card {
            background: rgba(255, 138, 0, 0.02);
            border: 1px solid rgba(255, 138, 0, 0.1);
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            transition: transform 0.2s ease;
          }
          .stat-card:hover {
            transform: translateY(-2px);
            border-color: rgba(255, 138, 0, 0.25);
            background: rgba(255, 138, 0, 0.04);
          }
          .stat-card h3 {
            margin: 0 0 8px 0;
            font-size: 12px;
            color: #a0a0a0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
          }
          .stat-card p {
            margin: 0;
            font-size: 26px;
            font-weight: 700;
            color: #ff8a00;
          }
          .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #ffffff;
            margin: 40px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(255, 138, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: space-between;
          }
          .section-count {
            font-size: 13px;
            background: rgba(255, 138, 0, 0.15);
            color: #ff8a00;
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: 600;
          }
          table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            text-align: left;
          }
          th {
            background: rgba(255, 255, 255, 0.02);
            color: #ff8a00;
            padding: 12px 16px;
            font-weight: 600;
            font-size: 12px;
            border-bottom: 1px solid rgba(255, 138, 0, 0.15);
            text-transform: uppercase;
            letter-spacing: 0.5px;
          }
          td {
            padding: 12px 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            font-size: 13.5px;
            word-break: break-all;
          }
          tr:hover td {
            background: rgba(255, 255, 255, 0.015);
          }
          a {
            color: #ffffff;
            text-decoration: none;
            transition: color 0.2s;
          }
          a:hover {
            color: #ff8a00;
          }
          .priority-badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-align: center;
          }
          .priority-high {
            background: rgba(46, 204, 113, 0.1);
            color: #2ecc71;
            border: 1px solid rgba(46, 204, 113, 0.2);
          }
          .priority-med {
            background: rgba(241, 196, 15, 0.1);
            color: #f1c40f;
            border: 1px solid rgba(241, 196, 15, 0.2);
          }
          .priority-low {
            background: rgba(189, 195, 199, 0.1);
            color: #bdc3c7;
            border: 1px solid rgba(189, 195, 199, 0.2);
          }
          .badge-lang {
            display: inline-block;
            margin-right: 6px;
            padding: 2px 5px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 4px;
            font-size: 10px;
            color: #dcdcdc;
            text-transform: uppercase;
            font-weight: 500;
          }
          .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #555555;
            border-top: 1px solid rgba(255, 255, 255, 0.04);
            padding-top: 20px;
          }
        </style>
      </head>
      <body>
        <div class="container">
          <h1><span>Serv5</span> XML Sitemap</h1>
          <p class="description">
            This XML Sitemap is dynamically generated to help search engines index pages on our website.
            Below, the sitemap is organized into clean, scannable categories for human visitors.
          </p>

          <!-- Categorized Counts Summary Card -->
          <div class="stats-grid">
            <div class="stat-card">
              <h3>Total URLs</h3>
              <p><xsl:value-of select="count(sitemap:urlset/sitemap:url)"/></p>
            </div>
            <div class="stat-card">
              <h3>Static Pages</h3>
              <p><xsl:value-of select="count(sitemap:urlset/sitemap:url[not(contains(sitemap:loc, '/services/')) and not(contains(sitemap:loc, '/blog/')) and not(contains(sitemap:loc, '/our-products/')) and not(contains(sitemap:loc, '/portfolio/'))])"/></p>
            </div>
            <div class="stat-card">
              <h3>Services</h3>
              <p><xsl:value-of select="count(sitemap:urlset/sitemap:url[contains(sitemap:loc, '/services/')])"/></p>
            </div>
            <div class="stat-card">
              <h3>Products</h3>
              <p><xsl:value-of select="count(sitemap:urlset/sitemap:url[contains(sitemap:loc, '/our-products/')])"/></p>
            </div>
            <div class="stat-card">
              <h3>Blogs</h3>
              <p><xsl:value-of select="count(sitemap:urlset/sitemap:url[contains(sitemap:loc, '/blog/')])"/></p>
            </div>
            <div class="stat-card">
              <h3>Portfolio</h3>
              <p><xsl:value-of select="count(sitemap:urlset/sitemap:url[contains(sitemap:loc, '/portfolio/')])"/></p>
            </div>
          </div>

          <!-- SECTION 1: Static Pages -->
          <xsl:variable name="staticCount" select="count(sitemap:urlset/sitemap:url[not(contains(sitemap:loc, '/services/')) and not(contains(sitemap:loc, '/blog/')) and not(contains(sitemap:loc, '/our-products/')) and not(contains(sitemap:loc, '/portfolio/'))])" />
          <xsl:if test="$staticCount &gt; 0">
            <div class="section-title">
              Main Static Pages
              <span class="section-count"><xsl:value-of select="$staticCount"/> Pages</span>
            </div>
            <table>
              <thead>
                <tr>
                  <th width="50%">URL</th>
                  <th width="10%">Priority</th>
                  <th width="15%">Change Freq.</th>
                  <th width="15%">Last Modified</th>
                  <th width="10%">Languages</th>
                </tr>
              </thead>
              <tbody>
                <xsl:for-each select="sitemap:urlset/sitemap:url[not(contains(sitemap:loc, '/services/')) and not(contains(sitemap:loc, '/blog/')) and not(contains(sitemap:loc, '/our-products/')) and not(contains(sitemap:loc, '/portfolio/'))]">
                  <xsl:sort select="sitemap:priority" order="descending"/>
                  <tr>
                    <td>
                      <a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc"/></a>
                    </td>
                    <td>
                      <xsl:variable name="p" select="sitemap:priority"/>
                      <span>
                        <xsl:attribute name="class">
                          <xsl:choose>
                            <xsl:when test="$p &gt;= 0.8">priority-badge priority-high</xsl:when>
                            <xsl:when test="$p &gt;= 0.5">priority-badge priority-med</xsl:when>
                            <xsl:otherwise>priority-badge priority-low</xsl:otherwise>
                          </xsl:choose>
                        </xsl:attribute>
                        <xsl:value-of select="$p"/>
                      </span>
                    </td>
                    <td><xsl:value-of select="sitemap:changefreq"/></td>
                    <td><xsl:value-of select="substring(sitemap:lastmod, 1, 10)"/></td>
                    <td>
                      <xsl:for-each select="xhtml:link[@rel='alternate']">
                        <span class="badge-lang"><xsl:value-of select="@hreflang"/></span>
                      </xsl:for-each>
                    </td>
                  </tr>
                </xsl:for-each>
              </tbody>
            </table>
          </xsl:if>

          <!-- SECTION 2: Services -->
          <xsl:variable name="servicesCount" select="count(sitemap:urlset/sitemap:url[contains(sitemap:loc, '/services/')])" />
          <xsl:if test="$servicesCount &gt; 0">
            <div class="section-title">
              Dynamic Services
              <span class="section-count"><xsl:value-of select="$servicesCount"/> Services</span>
            </div>
            <table>
              <thead>
                <tr>
                  <th width="50%">URL</th>
                  <th width="10%">Priority</th>
                  <th width="15%">Change Freq.</th>
                  <th width="15%">Last Modified</th>
                  <th width="10%">Languages</th>
                </tr>
              </thead>
              <tbody>
                <xsl:for-each select="sitemap:urlset/sitemap:url[contains(sitemap:loc, '/services/')]">
                  <xsl:sort select="sitemap:priority" order="descending"/>
                  <tr>
                    <td>
                      <a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc"/></a>
                    </td>
                    <td>
                      <xsl:variable name="p" select="sitemap:priority"/>
                      <span>
                        <xsl:attribute name="class">
                          <xsl:choose>
                            <xsl:when test="$p &gt;= 0.8">priority-badge priority-high</xsl:when>
                            <xsl:when test="$p &gt;= 0.5">priority-badge priority-med</xsl:when>
                            <xsl:otherwise>priority-badge priority-low</xsl:otherwise>
                          </xsl:choose>
                        </xsl:attribute>
                        <xsl:value-of select="$p"/>
                      </span>
                    </td>
                    <td><xsl:value-of select="sitemap:changefreq"/></td>
                    <td><xsl:value-of select="substring(sitemap:lastmod, 1, 10)"/></td>
                    <td>
                      <xsl:for-each select="xhtml:link[@rel='alternate']">
                        <span class="badge-lang"><xsl:value-of select="@hreflang"/></span>
                      </xsl:for-each>
                    </td>
                  </tr>
                </xsl:for-each>
              </tbody>
            </table>
          </xsl:if>

          <!-- SECTION 3: Products -->
          <xsl:variable name="productsCount" select="count(sitemap:urlset/sitemap:url[contains(sitemap:loc, '/our-products/')])" />
          <xsl:if test="$productsCount &gt; 0">
            <div class="section-title">
              Dynamic Products
              <span class="section-count"><xsl:value-of select="$productsCount"/> Products</span>
            </div>
            <table>
              <thead>
                <tr>
                  <th width="50%">URL</th>
                  <th width="10%">Priority</th>
                  <th width="15%">Change Freq.</th>
                  <th width="15%">Last Modified</th>
                  <th width="10%">Languages</th>
                </tr>
              </thead>
              <tbody>
                <xsl:for-each select="sitemap:urlset/sitemap:url[contains(sitemap:loc, '/our-products/')]">
                  <xsl:sort select="sitemap:priority" order="descending"/>
                  <tr>
                    <td>
                      <a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc"/></a>
                    </td>
                    <td>
                      <xsl:variable name="p" select="sitemap:priority"/>
                      <span>
                        <xsl:attribute name="class">
                          <xsl:choose>
                            <xsl:when test="$p &gt;= 0.8">priority-badge priority-high</xsl:when>
                            <xsl:when test="$p &gt;= 0.5">priority-badge priority-med</xsl:when>
                            <xsl:otherwise>priority-badge priority-low</xsl:otherwise>
                          </xsl:choose>
                        </xsl:attribute>
                        <xsl:value-of select="$p"/>
                      </span>
                    </td>
                    <td><xsl:value-of select="sitemap:changefreq"/></td>
                    <td><xsl:value-of select="substring(sitemap:lastmod, 1, 10)"/></td>
                    <td>
                      <xsl:for-each select="xhtml:link[@rel='alternate']">
                        <span class="badge-lang"><xsl:value-of select="@hreflang"/></span>
                      </xsl:for-each>
                    </td>
                  </tr>
                </xsl:for-each>
              </tbody>
            </table>
          </xsl:if>

          <!-- SECTION 4: Portfolio -->
          <xsl:variable name="portfolioCount" select="count(sitemap:urlset/sitemap:url[contains(sitemap:loc, '/portfolio/')])" />
          <xsl:if test="$portfolioCount &gt; 0">
            <div class="section-title">
              Dynamic Projects &amp; Portfolio
              <span class="section-count"><xsl:value-of select="$portfolioCount"/> Projects</span>
            </div>
            <table>
              <thead>
                <tr>
                  <th width="50%">URL</th>
                  <th width="10%">Priority</th>
                  <th width="15%">Change Freq.</th>
                  <th width="15%">Last Modified</th>
                  <th width="10%">Languages</th>
                </tr>
              </thead>
              <tbody>
                <xsl:for-each select="sitemap:urlset/sitemap:url[contains(sitemap:loc, '/portfolio/')]">
                  <xsl:sort select="sitemap:priority" order="descending"/>
                  <tr>
                    <td>
                      <a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc"/></a>
                    </td>
                    <td>
                      <xsl:variable name="p" select="sitemap:priority"/>
                      <span>
                        <xsl:attribute name="class">
                          <xsl:choose>
                            <xsl:when test="$p &gt;= 0.8">priority-badge priority-high</xsl:when>
                            <xsl:when test="$p &gt;= 0.5">priority-badge priority-med</xsl:when>
                            <xsl:otherwise>priority-badge priority-low</xsl:otherwise>
                          </xsl:choose>
                        </xsl:attribute>
                        <xsl:value-of select="$p"/>
                      </span>
                    </td>
                    <td><xsl:value-of select="sitemap:changefreq"/></td>
                    <td><xsl:value-of select="substring(sitemap:lastmod, 1, 10)"/></td>
                    <td>
                      <xsl:for-each select="xhtml:link[@rel='alternate']">
                        <span class="badge-lang"><xsl:value-of select="@hreflang"/></span>
                      </xsl:for-each>
                    </td>
                  </tr>
                </xsl:for-each>
              </tbody>
            </table>
          </xsl:if>

          <!-- SECTION 5: Blogs -->
          <xsl:variable name="blogCount" select="count(sitemap:urlset/sitemap:url[contains(sitemap:loc, '/blog/')])" />
          <xsl:if test="$blogCount &gt; 0">
            <div class="section-title">
              Dynamic Blog Posts
              <span class="section-count"><xsl:value-of select="$blogCount"/> Articles</span>
            </div>
            <table>
              <thead>
                <tr>
                  <th width="50%">URL</th>
                  <th width="10%">Priority</th>
                  <th width="15%">Change Freq.</th>
                  <th width="15%">Last Modified</th>
                  <th width="10%">Languages</th>
                </tr>
              </thead>
              <tbody>
                <xsl:for-each select="sitemap:urlset/sitemap:url[contains(sitemap:loc, '/blog/')]">
                  <xsl:sort select="sitemap:priority" order="descending"/>
                  <tr>
                    <td>
                      <a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc"/></a>
                    </td>
                    <td>
                      <xsl:variable name="p" select="sitemap:priority"/>
                      <span>
                        <xsl:attribute name="class">
                          <xsl:choose>
                            <xsl:when test="$p &gt;= 0.8">priority-badge priority-high</xsl:when>
                            <xsl:when test="$p &gt;= 0.5">priority-badge priority-med</xsl:when>
                            <xsl:otherwise>priority-badge priority-low</xsl:otherwise>
                          </xsl:choose>
                        </xsl:attribute>
                        <xsl:value-of select="$p"/>
                      </span>
                    </td>
                    <td><xsl:value-of select="sitemap:changefreq"/></td>
                    <td><xsl:value-of select="substring(sitemap:lastmod, 1, 10)"/></td>
                    <td>
                      <xsl:for-each select="xhtml:link[@rel='alternate']">
                        <span class="badge-lang"><xsl:value-of select="@hreflang"/></span>
                      </xsl:for-each>
                    </td>
                  </tr>
                </xsl:for-each>
              </tbody>
            </table>
          </xsl:if>
          
          <div class="footer">
            Generated by Serv5 Dynamic XML Sitemap System.
          </div>
        </div>
      </body>
    </html>
  </xsl:template>
</xsl:stylesheet>
