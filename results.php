<?php
/**
 * Latest Results Page
 */
require_once __DIR__ . '/app/config/app.php';

$pageTitle = 'Latest Published Results';
$pageDescription = 'Live and historical published Satka Matka results, open and close digits, and daily panel records.';

$selectedDate = $_GET['date'] ?? date('Y-m-d');
$marketId = !empty($_GET['market_id']) ? (int)$_GET['market_id'] : null;

$filters = [];
if ($marketId) $filters['market_id'] = $marketId;
if (!empty($_GET['date'])) $filters['date'] = $selectedDate;

$allMarkets = Market::getActive();
$results = Result::allPaginated(25, 0, $filters);

require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
require __DIR__ . '/includes/flash-message.php';
?>

<main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
      <h1 class="text-2xl sm:text-3xl font-black text-[#191747]">Latest Published Results</h1>
      <p class="text-xs sm:text-sm text-[#737493] mt-1">Verified live numbers, open-close panels, and daily archives.</p>
    </div>

    <!-- Filter Form -->
    <form action="/results.php" method="GET" class="flex flex-wrap items-center gap-3">
      <select name="market_id" class="px-3.5 py-2.5 rounded-xl bg-white border border-[#EAE8F5] text-xs font-semibold text-[#191747] focus:outline-none focus:border-[#6425D9]">
        <option value="">All Markets</option>
        <?php foreach ($allMarkets as $m): ?>
          <option value="<?= $m['id'] ?>" <?= $marketId === $m['id'] ? 'selected' : '' ?>><?= e($m['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <input 
        type="date" 
        name="date" 
        value="<?= e($_GET['date'] ?? '') ?>" 
        class="px-3.5 py-2.5 rounded-xl bg-white border border-[#EAE8F5] text-xs font-semibold text-[#191747] focus:outline-none focus:border-[#6425D9]"
      >

      <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#6425D9] hover:bg-[#521ec2] text-white text-xs font-bold transition">
        Filter
      </button>

      <?php if (!empty($_GET['market_id']) || !empty($_GET['date'])): ?>
        <a href="/results.php" class="px-3 py-2.5 rounded-xl bg-white border border-[#EAE8F5] text-xs font-bold text-[#737493] hover:text-[#6425D9]">
          Clear
        </a>
      <?php endif; ?>
    </form>
  </div>

  <?php if (empty($results)): ?>
    <div class="bg-white rounded-3xl border border-[#EAE8F5] p-12 text-center max-w-lg mx-auto">
      <div class="w-14 h-14 rounded-2xl bg-[#F3EDFF] text-[#6425D9] flex items-center justify-center mx-auto mb-4">
        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
      <h3 class="text-base font-bold text-[#191747]">No Results Found</h3>
      <p class="text-xs text-[#737493] mt-1 mb-5">There are no published records matching your selected filter criteria.</p>
      <a href="/results.php" class="inline-block px-4 py-2 rounded-xl bg-[#6425D9] text-white text-xs font-bold">View Today Results</a>
    </div>
  <?php else: ?>
    <div class="bg-white rounded-3xl border border-[#EAE8F5] p-6 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead>
            <tr class="text-[#737493] border-b border-[#EAE8F5] text-[11px]">
              <th class="py-3.5 px-4 font-semibold">Date</th>
              <th class="py-3.5 px-4 font-semibold">Market Name</th>
              <th class="py-3.5 px-4 font-semibold text-center">Open Display</th>
              <th class="py-3.5 px-4 font-semibold text-center">Close Display</th>
              <th class="py-3.5 px-4 font-semibold text-center">Jodi</th>
              <th class="py-3.5 px-4 font-semibold text-right">Full Result</th>
              <th class="py-3.5 px-4 font-semibold text-center">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#F3EDFF] text-[#191747]">
            <?php foreach ($results as $r): ?>
              <tr class="hover:bg-[#FAF9FF] transition">
                <td class="py-3.5 px-4 font-medium text-[#737493] whitespace-nowrap"><?= format_app_date($r['result_date'], 'd M Y') ?></td>
                <td class="py-3.5 px-4 font-extrabold text-[#191747]"><?= e($r['market_name']) ?></td>
                <td class="py-3.5 px-4 text-center font-mono font-bold text-[#191747]"><?= e($r['open_display'] ?: '-') ?></td>
                <td class="py-3.5 px-4 text-center font-mono font-bold text-[#191747]"><?= e($r['close_display'] ?: '-') ?></td>
                <td class="py-3.5 px-4 text-center">
                  <span class="inline-block px-2.5 py-1 rounded-lg bg-[#F3EDFF] text-[#6425D9] font-black text-xs font-mono">
                    <?= e($r['jodi'] ?: '--') ?>
                  </span>
                </td>
                <td class="py-3.5 px-4 text-right font-mono font-black text-base text-[#6425D9] tracking-wider">
                  <?= e($r['full_result']) ?>
                </td>
                <td class="py-3.5 px-4 text-center">
                  <a href="/chart.php?id=<?= $r['market_id'] ?>" class="text-[11px] font-bold text-[#6425D9] hover:underline">
                    Chart &rarr;
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
