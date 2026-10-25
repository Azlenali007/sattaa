<?php
/**
 * Satka Matka - Homepage
 * Matches Reference Image Exactly
 */

require_once __DIR__ . '/app/config/app.php';

$pageTitle = 'Live Results & Historical Charts';
$pageDescription = 'Get the latest Satka Matka results, market charts and historical data all in one place. Fast, accurate and easy to use.';

// Load Data
$latestCards = Result::getLatestCards();
$popularMarkets = Market::getPopular(6);
$marketSchedule = Market::getActive();
$recentHistory = Result::getRecentHistory(6);
$announcements = Announcement::getActive();

require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
require __DIR__ . '/includes/flash-message.php';
?>

<!-- Main Content Area with 2 Columns: Main Content on Left, Market Details on Right -->
<main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

  <!-- Announcements Banner if Active -->
  <?php if (!empty($announcements)): ?>
    <div class="mb-6 space-y-2">
      <?php foreach ($announcements as $ann): ?>
        <div class="p-3.5 rounded-xl bg-purple-50 border border-purple-200 text-[#191747] text-xs flex items-center justify-between">
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-[#6425D9]"></span>
            <strong><?= e($ann['title']) ?>:</strong>
            <span><?= e($ann['content']) ?></span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div class="flex flex-col xl:flex-row gap-8 items-start">
    
    <!-- Left Main Column -->
    <div class="flex-1 w-full space-y-10 min-w-0">

      <!-- Hero Section matching reference -->
      <section class="bg-gradient-to-br from-white via-white to-[#F8F5FF] rounded-3xl border border-[#EAE8F5] p-6 sm:p-10 lg:p-12 relative overflow-hidden shadow-xs">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
          
          <div class="lg:col-span-7 z-10">
            <span class="text-xs font-bold uppercase tracking-wider text-[#6425D9] block mb-3">WELCOME TO SATKA MATKA</span>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-[#191747] tracking-tight leading-tight">
              Live Results<br>&amp; Historical Charts
            </h1>
            <p class="text-sm sm:text-base text-[#737493] mt-4 mb-8 max-w-lg leading-relaxed">
              Get the latest Satka Matka results, market charts and historical data all in one place. Fast, accurate and easy to use.
            </p>

            <!-- Search Form Component -->
            <?php require __DIR__ . '/includes/search-form.php'; ?>
          </div>

          <!-- Hero Illustration with Crown & Balls (8, 5, 9) -->
          <div class="lg:col-span-5 flex justify-center items-center">
            <div class="relative w-full max-w-[420px]">
              <img 
                src="/assets/images/hero-illustration.svg" 
                alt="Satka Matka Crown and 3D Numbers Illustration" 
                class="w-full h-auto drop-shadow-md transform hover:scale-102 transition duration-300"
              >
            </div>
          </div>

        </div>
      </section>

      <!-- Section: Latest Results matching reference -->
      <section>
        <div class="flex items-center justify-between mb-5">
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-md bg-[#F3EDFF] text-[#6425D9] flex items-center justify-center">
              <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
              </svg>
            </div>
            <h2 class="text-lg font-black text-[#191747] tracking-tight">Latest Results</h2>
          </div>
          <a href="/results.php" class="text-xs font-bold text-[#6425D9] hover:underline">View All</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <?php foreach ($latestCards as $card): ?>
            <?php require __DIR__ . '/includes/result-card.php'; ?>
          <?php endforeach; ?>
        </div>
      </section>

      <!-- Section: Popular Markets matching reference -->
      <section>
        <div class="flex items-center gap-2 mb-5">
          <div class="w-6 h-6 rounded-md bg-[#F3EDFF] text-[#6425D9] flex items-center justify-center">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
              <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
          </div>
          <h2 class="text-lg font-black text-[#191747] tracking-tight">Popular Markets</h2>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
          <?php foreach ($popularMarkets as $marketCard): ?>
            <?php require __DIR__ . '/includes/market-card.php'; ?>
          <?php endforeach; ?>
        </div>
      </section>

      <!-- Section: Two Columns (Market Schedule + Recent History) matching reference -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Column 1: Market Schedule -->
        <div class="bg-white rounded-2xl border border-[#EAE8F5] p-5 shadow-sm">
          <div class="flex items-center gap-2 mb-4">
            <svg class="w-4 h-4 text-[#6425D9]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <h3 class="font-extrabold text-sm text-[#191747]">Market Schedule</h3>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
              <thead>
                <tr class="text-[#737493] border-b border-[#EAE8F5] text-[11px]">
                  <th class="pb-2.5 font-semibold">Market Name</th>
                  <th class="pb-2.5 font-semibold">Open Time</th>
                  <th class="pb-2.5 font-semibold">Close Time</th>
                  <th class="pb-2.5 font-semibold text-right">Status</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-[#FAF9FF] text-[#191747]">
                <?php foreach ($marketSchedule as $ms): 
                  $isOpen = !empty($ms['is_live']);
                ?>
                  <tr class="hover:bg-[#FAF9FF] transition">
                    <td class="py-2.5 font-bold text-[#191747]"><?= e($ms['name']) ?></td>
                    <td class="py-2.5 text-[#737493]"><?= e($ms['open_time']) ?></td>
                    <td class="py-2.5 text-[#737493]"><?= e($ms['close_time']) ?></td>
                    <td class="py-2.5 text-right">
                      <?php if ($isOpen): ?>
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">Open</span>
                      <?php else: ?>
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">Upcoming</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Column 2: Recent History -->
        <div class="bg-white rounded-2xl border border-[#EAE8F5] p-5 shadow-sm">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 text-[#6425D9]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <h3 class="font-extrabold text-sm text-[#191747]">Recent History</h3>
            </div>
            <a href="/history.php" class="text-xs font-bold text-[#6425D9] hover:underline">View All</a>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
              <thead>
                <tr class="text-[#737493] border-b border-[#EAE8F5] text-[11px]">
                  <th class="pb-2.5 font-semibold">Date</th>
                  <th class="pb-2.5 font-semibold">Market</th>
                  <th class="pb-2.5 font-semibold text-right">Result (Open - Close)</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-[#FAF9FF] text-[#191747]">
                <?php foreach ($recentHistory as $rh): ?>
                  <tr class="hover:bg-[#FAF9FF] transition">
                    <td class="py-2.5 text-[11px] font-medium text-[#737493] whitespace-nowrap"><?= format_app_date($rh['result_date'], 'd M Y') ?></td>
                    <td class="py-2.5 font-bold text-[#191747]"><?= e($rh['market_name']) ?></td>
                    <td class="py-2.5 text-right font-mono font-extrabold text-[#191747]">
                      <?= e($rh['full_result'] ?: ($rh['open_display'] . ' - ' . $rh['close_display'])) ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>

    </div>

    <!-- Right-side Market Details & Charts Panel matching reference -->
    <?php require __DIR__ . '/includes/sidebar-market.php'; ?>

  </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
