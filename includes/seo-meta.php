<?php
/**
 * SEO Meta Template
 */
$pageMeta = $pageMeta ?? Seo::getMeta($pageTitle ?? '', $pageDescription ?? '');
?>
<title><?= e($pageMeta['title']) ?></title>
<meta name="description" content="<?= e($pageMeta['description']) ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta property="og:title" content="<?= e($pageMeta['title']) ?>">
<meta property="og:description" content="<?= e($pageMeta['description']) ?>">
<meta property="og:type" content="website">
<link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
