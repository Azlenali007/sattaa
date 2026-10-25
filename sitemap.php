<?php
/**
 * XML and HTML Sitemap
 */
require_once __DIR__ . '/app/config/app.php';

if (isset($_GET['xml']) || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/xml'))) {
    header('Content-Type: application/xml; charset=utf-8');
    echo SeoService::generateSitemapXml();
    exit;
}

$pageTitle = 'Sitemap';
$pageDescription = 'Website sitemap index for markets, charts, and public result pages.';
$markets = Market::getActive();

require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>

<main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
  <div class="max-w-3xl mx-auto bg-white rounded-3xl border border-[#EAE8F5] p-8 sm:p-12 shadow-xs">
    <div class="flex items-center justify-between mb-8 pb-4 border-b border-[#EAE8F5]">
      <div>
        <h1 class="text-2xl font-black text-[#191747]">Website Sitemap</h1>
        <p class="text-xs text-[#737493] mt-1">Directory of all indexed pages and market charts.</p>
      </div>
      <a href="/sitemap.php?xml=1" target="_blank" class="px-3.5 py-1.5 rounded-xl bg-[#F3EDFF] text-[#6425D9] text-xs font-bold hover:bg-[#e7d8ff] transition">
        View XML Sitemap
      </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-xs">
      <div>
        <h2 class="font-bold text-sm text-[#191747] mb-3">Main Pages</h2>
        <ul class="space-y-2 text-[#737493]">
          <li><a href="/index.php" class="hover:text-[#6425D9]">Homepage</a></li>
          <li><a href="/markets.php" class="hover:text-[#6425D9]">All Markets Directory</a></li>
          <li><a href="/results.php" class="hover:text-[#6425D9]">Latest Results</a></li>
          <li><a href="/chart.php" class="hover:text-[#6425D9]">Jodi Charts</a></li>
          <li><a href="/history.php" class="hover:text-[#6425D9]">Historical Records</a></li>
          <li><a href="/search.php" class="hover:text-[#6425D9]">Market &amp; Date Search</a></li>
          <li><a href="/about.php" class="hover:text-[#6425D9]">About Platform</a></li>
          <li><a href="/contact.php" class="hover:text-[#6425D9]">Contact Us</a></li>
          <li><a href="/privacy-policy.php" class="hover:text-[#6425D9]">Privacy Policy</a></li>
          <li><a href="/terms.php" class="hover:text-[#6425D9]">Terms &amp; Conditions</a></li>
        </ul>
      </div>

      <div>
        <h2 class="font-bold text-sm text-[#191747] mb-3">Market Charts</h2>
        <ul class="space-y-2 text-[#737493]">
          <?php foreach ($markets as $m): ?>
            <li>
              <a href="/chart.php?id=<?= $m['id'] ?>" class="hover:text-[#6425D9]">
                <?= e($m['name']) ?> Jodi Chart
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
