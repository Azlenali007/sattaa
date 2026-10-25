<?php
/**
 * Admin: SEO Management
 */
require_once __DIR__ . '/../includes/auth-check.php';

$pageTitle = 'SEO & Sitemap Settings';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    enforce_csrf();
    $metaTitle = trim($_POST['meta_title'] ?? '');
    $metaDesc = trim($_POST['meta_description'] ?? '');

    Setting::set('meta_title', $metaTitle, 'seo');
    Setting::set('meta_description', $metaDesc, 'seo');

    Admin::logActivity($currentAdmin['id'], 'Update SEO', 'SEO', null, 'Updated default meta title and description');
    set_flash('success', 'SEO configuration saved successfully!');
    redirect('/admin/seo/index.php');
}

$title = Setting::get('meta_title', 'Satka Matka - Live Results & Historical Charts');
$desc = Setting::get('meta_description', 'Get the latest Satka Matka results, market charts and historical data all in one place. Fast, accurate and easy to use.');

require __DIR__ . '/../includes/head.php';
require __DIR__ . '/../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../includes/header.php'; ?>
  <?php require __DIR__ . '/../../includes/flash-message.php'; ?>

  <main class="p-6 sm:p-8 space-y-6 flex-1 max-w-4xl">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-black text-[#191747]">SEO &amp; Search Engine Optimization</h1>
        <p class="text-xs text-[#737493] mt-0.5">Control search engine titles, snippets, robots crawling directives, and sitemaps.</p>
      </div>

      <a href="/sitemap.php?xml=1" target="_blank" class="px-4 py-2 rounded-xl bg-[#F3EDFF] text-[#6425D9] text-xs font-bold hover:bg-[#e7d8ff] transition">
        View Live XML Sitemap
      </a>
    </div>

    <form action="/admin/seo/index.php" method="POST" class="bg-white rounded-2xl border border-[#EAE8F5] p-6 sm:p-8 shadow-xs space-y-6">
      <?= csrf_field() ?>

      <div>
        <label class="block text-xs font-bold text-[#191747] mb-1.5">Global Meta Title *</label>
        <input type="text" name="meta_title" value="<?= e($title) ?>" required class="w-full px-4 py-3 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
        <span class="text-[10px] text-[#737493] mt-1 block">Recommended: under 65 characters</span>
      </div>

      <div>
        <label class="block text-xs font-bold text-[#191747] mb-1.5">Global Meta Description *</label>
        <textarea name="meta_description" rows="3" required class="w-full px-4 py-3 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]"><?= e($desc) ?></textarea>
        <span class="text-[10px] text-[#737493] mt-1 block">Recommended: between 120 and 160 characters</span>
      </div>

      <div class="p-4 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] space-y-2">
        <h3 class="text-xs font-bold text-[#191747]">Crawler &amp; Robot Directives</h3>
        <p class="text-xs text-[#737493]">
          <code>robots.txt</code> is pre-configured to allow search engine indexing on public charts and protect private directories (<code>/admin/</code>, <code>/storage/</code>).
        </p>
      </div>

      <div class="pt-4 border-t border-[#EAE8F5] flex justify-end">
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#6425D9] hover:bg-[#521ec2] text-white text-xs font-bold shadow-xs transition">Save SEO Settings</button>
      </div>
    </form>
  </main>

  <?php require __DIR__ . '/../includes/footer.php'; ?>
</div>
