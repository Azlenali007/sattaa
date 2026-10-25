<?php
/**
 * SEO XML Sitemap and Robots Generator
 */

class SeoService {
    public static function generateSitemapXml(): string {
        $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . ($_SERVER['HTTP_HOST'] ?? 'localhost');
        $markets = Market::getActive();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Static routes
        $routes = ['', 'markets.php', 'results.php', 'chart.php', 'history.php', 'search.php', 'about.php', 'contact.php'];
        foreach ($routes as $route) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars("{$baseUrl}/{$route}") . "</loc>\n";
            $xml .= "    <changefreq>daily</changefreq>\n";
            $xml .= "    <priority>0.8</priority>\n";
            $xml .= "  </url>\n";
        }

        // Market dynamic URLs
        foreach ($markets as $m) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars("{$baseUrl}/market.php?id={$m['id']}") . "</loc>\n";
            $xml .= "    <changefreq>hourly</changefreq>\n";
            $xml .= "    <priority>0.9</priority>\n";
            $xml .= "  </url>\n";
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars("{$baseUrl}/chart.php?id={$m['id']}") . "</loc>\n";
            $xml .= "    <changefreq>daily</changefreq>\n";
            $xml .= "    <priority>0.9</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';
        return $xml;
    }
}
