<?php
/**
 * SEO Meta Helpers
 */

class Seo {
    public static function getMeta(string $title = '', string $description = ''): array {
        $siteName = Setting::get('site_name', 'SATKA MATKA');
        $tagline = Setting::get('site_tagline', 'Live Results & Charts');

        $finalTitle = $title 
            ? "{$title} | {$siteName}" 
            : Setting::get('meta_title', "{$siteName} - {$tagline}");

        $finalDesc = $description 
            ?: Setting::get('meta_description', 'Get the latest Satka Matka results, market charts and historical data all in one place. Fast, accurate and easy to use.');

        return [
            'title' => $finalTitle,
            'description' => $finalDesc,
            'site_name' => $siteName,
        ];
    }
}
