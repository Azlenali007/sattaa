<?php
/**
 * Historical Result Records
 */
require_once __DIR__ . '/app/config/app.php';

$pageTitle = 'Historical Results Archive';
$pageDescription = 'Search through historical Satka Matka results, daily open close numbers, and complete archives.';

$marketId = !empty($_GET['market_id']) ? (int)$_GET['market_id'] : null;
$date = $_GET['date'] ?? '';

$filters = [];
if ($marketId) $filters['market_id'] = $marketId;
if ($date) $filters['date'] = $date;

$allMarkets = Market::getActive();
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;

$results = Result::allPaginated($perPage, ($page - 1) * $perPage, $filters);
$totalRecords = Result::count();
$paginator = new Paginator($totalRecords, $perPage, $page);

require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
require __DIR__ . '/includes/flash-message.php';
?>

<main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
      <h1 class="text-2xl sm:text-3xl font-black text-[#191747]">Historical Results Archive</h1>
      <p class="text-xs sm:text-sm text-[#737493] mt-1">Search through past records by market, specific date, and jodi.</p>
    </div>

    <!-- Filter Form -->
    <form action="/history.php" method="GET" class="flex flex-wrap items-center gap-3">
      <select name="market_id" class="px-3.5 py-2.5 rounded-xl bg-white border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
        <option value="">All Markets</option>
        <?php foreach ($allMarkets as $m): ?>
          <option value="<?= $m['id'] ?>" <?= $marketId === $m['id'] ? 'selected' : '' ?>><?= e($m['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <input 
        type="date" 
        name="date" 
        value="<?= e($date) ?>" 
        class="px-3.5 py-2.5 rounded-xl bg-white border border-[#EAE8F5] text-xs font-semibold text-[#191747]"
      >

      <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#6425D9] hover:bg-[#521ec2] text-white text-xs font-bold transition">
        Search
      </button>

      <?php if ($marketId || $date): ?>
        <a href="/history.php" class="px-3 py-2.5 rounded-xl bg-white border border-[#EAE8F5] text-xs font-bold text-[#737493]">
          Reset
        </a>
      <?php endif; ?>
    </form>
  </div>

  <div class="bg-white rounded-3xl border border-[#EAE8F5] p-6 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead>
          <tr class="text-[#737493] border-b border-[#EAE8F5] text-[11px] bg-[#FAF9FF]">
            <th class="py-3 px-4 font-bold rounded-l-xl">Date</th>
            <th class="py-3 px-4 font-bold">Market</th>
            <th class="py-3 px-4 font-bold text-center">Open Display</th>
            <th class="py-3 px-4 font-bold text-center">Close Display</th>
            <th class="py-3 px-4 font-bold text-center">Jodi</th>
            <th class="py-3 px-4 font-bold text-right rounded-r-xl">Result (Open - Close)</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#F3EDFF] text-[#191747]">
          <?php foreach ($results as $r): ?>
            <tr class="hover:bg-[#FAF9FF] transition">
              <td class="py-3.5 px-4 font-semibold text-[#737493] whitespace-nowrap"><?= format_app_date($r['result_date'], 'd M Y') ?></td>
              <td class="py-3.5 px-4 font-black text-[#191747]"><?= e($r['market_name']) ?></td>
              <td class="py-3.5 px-4 text-center font-mono font-bold"><?= e($r['open_display']) ?></td>
              <td class="py-3.5 px-4 text-center font-mono font-bold"><?= e($r['close_display']) ?></td>
              <td class="py-3.5 px-4 text-center">
                <span class="inline-block px-2.5 py-1 rounded-lg bg-[#F3EDFF] text-[#6425D9] font-black text-xs font-mono">
                  <?= e($r['jodi'] ?: '--') ?>
                </span>
              </td>
              <td class="py-3.5 px-4 text-right font-mono font-black text-sm text-[#6425D9] tracking-wider"><?= e($r['full_result']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php 
      $baseUrl = "/history.php?" . ($marketId ? "market_id={$marketId}&" : "") . ($date ? "date={$date}&" : "");
      require __DIR__ . '/includes/pagination.php'; 
    ?>
  </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
