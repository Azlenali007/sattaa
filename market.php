<?php
/**
 * Single Market Details Page
 */
require_once __DIR__ . '/app/config/app.php';

$market = null;
if (!empty($_GET['slug'])) {
    $market = Market::findBySlug(trim($_GET['slug']));
} elseif (!empty($_GET['id'])) {
    $market = Market::findById((int)$_GET['id']);
}

if (!$market) {
    $market = Market::findById(1);
}

if (!$market) {
    $all = Market::getActive();
    $market = $all[0] ?? null;
}

if (!$market) {
    redirect(url('markets.php'));
}

$pageTitle = $market['name'] . ' Market Details & Charts';
$pageDescription = $market['seo_description'] ?: "Check daily live results, timing schedule, and full Jodi charts for {$market['name']}.";

$selectedMarketId = $market['id'];
$latestResult = Result::findLatestForMarket($market['id']);
$recentRecords = Result::getJodiChart($market['id'], 15, 0);

require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
require __DIR__ . '/includes/flash-message.php';
?>

<main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
  <div class="flex flex-col xl:flex-row gap-8 items-start">
    
    <!-- Left Main Section -->
    <div class="flex-1 w-full space-y-8 min-w-0">

      <!-- Breadcrumbs & Market Title Banner -->
      <div class="bg-white rounded-3xl border border-[#EAE8F5] p-6 sm:p-8 shadow-xs">
        <nav class="flex items-center space-x-2 text-xs text-[#737493] mb-4">
          <a href="/index.php" class="hover:text-[#6425D9]">Home</a>
          <span>/</span>
          <a href="/markets.php" class="hover:text-[#6425D9]">Markets</a>
          <span>/</span>
          <span class="text-[#191747] font-semibold"><?= e($market['name']) ?></span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[#EAE8F5] pb-6">
          <div>
            <h1 class="text-3xl font-black text-[#191747] tracking-tight"><?= e($market['name']) ?></h1>
            <p class="text-xs sm:text-sm text-[#737493] mt-1"><?= e($market['seo_description'] ?: 'Daily verified results & chart history.') ?></p>
          </div>
          <div>
            <?php if (!empty($market['is_live'])): ?>
              <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse-dot"></span>
                Live Market
              </span>
            <?php else: ?>
              <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                Upcoming
              </span>
            <?php endif; ?>
          </div>
        </div>

        <!-- Timings & Highlights -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-6 text-xs">
          <div class="p-3.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5]">
            <span class="text-[#737493] block font-medium">Open Time</span>
            <span class="text-base font-extrabold text-[#191747] block mt-1"><?= e($market['open_time']) ?></span>
          </div>
          <div class="p-3.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5]">
            <span class="text-[#737493] block font-medium">Close Time</span>
            <span class="text-base font-extrabold text-[#191747] block mt-1"><?= e($market['close_time']) ?></span>
          </div>
          <div class="p-3.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5]">
            <span class="text-[#737493] block font-medium">Today Result</span>
            <span class="text-base font-extrabold text-[#6425D9] block mt-1"><?= e($latestResult['full_result'] ?? '--') ?></span>
          </div>
          <div class="p-3.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5]">
            <span class="text-[#737493] block font-medium">Jodi</span>
            <span class="text-base font-extrabold text-[#191747] block mt-1"><?= e($latestResult['jodi'] ?? '--') ?></span>
          </div>
        </div>
      </div>

      <!-- Comprehensive History Records Table -->
      <div class="bg-white rounded-3xl border border-[#EAE8F5] p-6 shadow-sm">
        <div class="flex items-center justify-between mb-5">
          <h2 class="text-lg font-black text-[#191747]"><?= e($market['name']) ?> Historical Records</h2>
          <a href="/chart.php?id=<?= $market['id'] ?>" class="text-xs font-bold text-[#6425D9] hover:underline">Full Chart View &rarr;</a>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead>
              <tr class="text-[#737493] border-b border-[#EAE8F5] text-[11px]">
                <th class="py-3 px-3 font-semibold">Date</th>
                <th class="py-3 px-3 font-semibold text-center">Jodi 1</th>
                <th class="py-3 px-3 font-semibold text-center">Jodi 2</th>
                <th class="py-3 px-3 font-semibold text-center">Jodi 3</th>
                <th class="py-3 px-3 font-semibold text-center">Jodi 4</th>
                <th class="py-3 px-3 font-semibold text-right">Result (Open - Close)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#F3EDFF] text-[#191747]">
              <?php foreach ($recentRecords as $rec): ?>
                <tr class="hover:bg-[#FAF9FF] transition">
                  <td class="py-3 px-3 font-medium text-[#737493] whitespace-nowrap"><?= e($rec['date_formatted']) ?></td>
                  <td class="py-3 px-3 text-center font-bold text-[#191747]"><?= e($rec['jodi_1']) ?></td>
                  <td class="py-3 px-3 text-center font-bold text-[#191747]"><?= e($rec['jodi_2']) ?></td>
                  <td class="py-3 px-3 text-center font-bold text-[#191747]"><?= e($rec['jodi_3']) ?></td>
                  <td class="py-3 px-3 text-center font-bold text-[#191747]"><?= e($rec['jodi_4']) ?></td>
                  <td class="py-3 px-3 text-right font-mono font-black text-[#6425D9]"><?= e($rec['full_result']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>

    <!-- Right Sidebar matching reference -->
    <?php require __DIR__ . '/includes/sidebar-market.php'; ?>

  </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
