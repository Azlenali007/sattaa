<?php
/**
 * All Markets Directory
 */
require_once __DIR__ . '/app/config/app.php';

$pageTitle = 'All Markets';
$pageDescription = 'Browse all active Satka Matka markets, schedules, opening and closing times, and historical charts.';

$markets = Market::all();

require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
require __DIR__ . '/includes/flash-message.php';
?>

<main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
  <div class="flex items-center justify-between mb-8">
    <div>
      <h1 class="text-2xl sm:text-3xl font-black text-[#191747]">All Markets</h1>
      <p class="text-xs sm:text-sm text-[#737493] mt-1">Explore all scheduled daily markets, timings, and verified charts.</p>
    </div>
    <span class="px-3 py-1.5 rounded-full bg-[#F3EDFF] text-[#6425D9] text-xs font-bold">
      <?= count($markets) ?> Markets Available
    </span>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    <?php foreach ($markets as $m): 
      $latest = Result::findLatestForMarket($m['id']);
      $isOpen = !empty($m['is_live']);
    ?>
      <div class="bg-white rounded-2xl border border-[#EAE8F5] p-6 shadow-sm card-hover-effect flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-black text-[#191747]"><?= e($m['name']) ?></h2>
            <?php if ($isOpen): ?>
              <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">Open</span>
            <?php else: ?>
              <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">Upcoming</span>
            <?php endif; ?>
          </div>
          
          <div class="space-y-1.5 text-xs text-[#737493] mb-5">
            <div class="flex justify-between">
              <span>Open Time:</span>
              <strong class="text-[#191747]"><?= e($m['open_time']) ?></strong>
            </div>
            <div class="flex justify-between">
              <span>Close Time:</span>
              <strong class="text-[#191747]"><?= e($m['close_time']) ?></strong>
            </div>
          </div>

          <?php if ($latest): ?>
            <div class="bg-[#FAF9FF] border border-[#EAE8F5] rounded-xl p-3 text-center mb-5">
              <span class="text-[10px] text-[#737493] font-medium block">Latest Result (<?= format_app_date($latest['result_date'], 'd M') ?>)</span>
              <span class="text-base font-black text-[#191747] tracking-wider block mt-1"><?= e($latest['full_result']) ?></span>
            </div>
          <?php endif; ?>
        </div>

        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-[#FAF9FF]">
          <a href="/market.php?id=<?= $m['id'] ?>" class="py-2 px-3 rounded-xl bg-[#FAF9FF] hover:bg-[#F3EDFF] text-[#6425D9] text-xs font-bold text-center border border-[#EAE8F5] transition">
            Details
          </a>
          <a href="/chart.php?id=<?= $m['id'] ?>" class="py-2 px-3 rounded-xl bg-[#6425D9] hover:bg-[#521ec2] text-white text-xs font-bold text-center transition">
            View Chart
          </a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
