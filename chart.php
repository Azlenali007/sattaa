<?php
/**
 * Jodi & Historical Charts Page
 */
require_once __DIR__ . '/app/config/app.php';

$marketId = (int)($_GET['id'] ?? 1);
$markets = Market::getActive();
$selectedMarket = Market::findById($marketId) ?: ($markets[0] ?? null);

if (!$selectedMarket) {
    redirect('/markets.php');
}

$pageTitle = $selectedMarket['name'] . ' Jodi Chart';
$pageDescription = "Complete Jodi and panel chart records for {$selectedMarket['name']} market updated daily.";

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$totalRecords = Result::countJodiChart($selectedMarket['id']);
$paginator = new Paginator($totalRecords, $perPage, $page);

$chartRows = Result::getJodiChart($selectedMarket['id'], $perPage, $paginator->offset);

require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
require __DIR__ . '/includes/flash-message.php';
?>

<main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
  <!-- Chart Header & Selector -->
  <div class="bg-white rounded-3xl border border-[#EAE8F5] p-6 sm:p-8 shadow-xs mb-8">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
      <div>
        <div class="flex items-center gap-2 text-xs font-bold text-[#6425D9] uppercase tracking-wider mb-2">
          <span>Official Chart Archive</span>
          <span>•</span>
          <span>Verified Daily</span>
        </div>
        <h1 class="text-2xl sm:text-4xl font-black text-[#191747]"><?= e($selectedMarket['name']) ?> Jodi Chart</h1>
        <p class="text-xs sm:text-sm text-[#737493] mt-1">Complete historical jodi numbers, open and close panels.</p>
      </div>

      <!-- Quick Switcher for Markets -->
      <div class="flex items-center gap-3">
        <label for="market-select" class="text-xs font-bold text-[#737493]">Select Market:</label>
        <select 
          id="market-select"
          onchange="location.href='/chart.php?id=' + this.value"
          class="px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-bold text-[#191747] focus:outline-none focus:border-[#6425D9]"
        >
          <?php foreach ($markets as $m): ?>
            <option value="<?= $m['id'] ?>" <?= $m['id'] === $selectedMarket['id'] ? 'selected' : '' ?>><?= e($m['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <!-- Market Quick Timings Bar -->
    <div class="flex flex-wrap items-center gap-4 mt-6 pt-6 border-t border-[#EAE8F5] text-xs">
      <div class="flex items-center gap-2">
        <span class="text-[#737493]">Timings:</span>
        <strong class="text-[#191747]"><?= e($selectedMarket['open_time']) ?> - <?= e($selectedMarket['close_time']) ?></strong>
      </div>
      <span>•</span>
      <div class="flex items-center gap-2">
        <span class="text-[#737493]">Total Records:</span>
        <strong class="text-[#6425D9]"><?= $totalRecords ?> Dates</strong>
      </div>
    </div>
  </div>

  <!-- Chart Table Display -->
  <div class="bg-white rounded-3xl border border-[#EAE8F5] p-6 shadow-sm">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead>
          <tr class="text-[#737493] border-b border-[#EAE8F5] text-[11px] bg-[#FAF9FF]">
            <th class="py-3 px-4 font-bold rounded-l-xl">Date</th>
            <th class="py-3 px-4 font-bold text-center">Jodi 1</th>
            <th class="py-3 px-4 font-bold text-center">Jodi 2</th>
            <th class="py-3 px-4 font-bold text-center">Jodi 3</th>
            <th class="py-3 px-4 font-bold text-center">Jodi 4</th>
            <th class="py-3 px-4 font-bold text-right rounded-r-xl">Full Result</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#F3EDFF] text-[#191747]">
          <?php foreach ($chartRows as $row): ?>
            <tr class="hover:bg-[#FAF9FF] transition">
              <td class="py-3.5 px-4 font-semibold text-[#737493] whitespace-nowrap"><?= e($row['date_formatted']) ?></td>
              <td class="py-3.5 px-4 text-center font-black text-sm text-[#191747]"><?= e($row['jodi_1']) ?></td>
              <td class="py-3.5 px-4 text-center font-black text-sm text-[#191747]"><?= e($row['jodi_2']) ?></td>
              <td class="py-3.5 px-4 text-center font-black text-sm text-[#191747]"><?= e($row['jodi_3']) ?></td>
              <td class="py-3.5 px-4 text-center font-black text-sm text-[#191747]"><?= e($row['jodi_4']) ?></td>
              <td class="py-3.5 px-4 text-right font-mono font-extrabold text-[#6425D9] text-sm"><?= e($row['full_result']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <?php 
      $baseUrl = "/chart.php?id={$selectedMarket['id']}&";
      require __DIR__ . '/includes/pagination.php'; 
    ?>
  </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
