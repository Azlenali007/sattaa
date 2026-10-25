<?php
/**
 * Market, Date & Jodi Search
 */
require_once __DIR__ . '/app/config/app.php';

$query = trim($_GET['q'] ?? '');
$date = trim($_GET['date'] ?? '');

$pageTitle = 'Search Results';
if ($query) $pageTitle .= " for \"{$query}\"";

$pageDescription = 'Search live and historical Satka Matka results, markets, and charts.';

$results = ($query !== '' || $date !== '') ? Result::search($query, $date, 30) : [];

require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
require __DIR__ . '/includes/flash-message.php';
?>

<main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
  <div class="max-w-2xl mx-auto mb-10 text-center">
    <h1 class="text-3xl font-black text-[#191747]">Search Results</h1>
    <p class="text-xs sm:text-sm text-[#737493] mt-2 mb-6">Find verified records by market title, result date or exact jodi digits.</p>
    
    <form action="/search.php" method="GET" class="flex flex-col sm:flex-row gap-3">
      <div class="relative flex-1">
        <input 
          type="text" 
          name="q" 
          placeholder="Market name, jodi or digits..." 
          value="<?= e($query) ?>"
          class="w-full px-5 py-3.5 rounded-2xl bg-white border border-[#EAE8F5] text-xs font-semibold text-[#191747] placeholder-[#737493] focus:outline-none focus:border-[#6425D9] shadow-xs"
        >
      </div>
      <input 
        type="date" 
        name="date" 
        value="<?= e($date) ?>" 
        class="px-4 py-3.5 rounded-2xl bg-white border border-[#EAE8F5] text-xs font-semibold text-[#191747] focus:outline-none focus:border-[#6425D9]"
      >
      <button type="submit" class="px-6 py-3.5 rounded-2xl bg-[#6425D9] hover:bg-[#521ec2] text-white text-xs font-bold transition shadow-xs">
        Search
      </button>
    </form>
  </div>

  <?php if ($query !== '' || $date !== ''): ?>
    <div class="mb-5 flex items-center justify-between">
      <span class="text-xs text-[#737493]">Showing <strong><?= count($results) ?></strong> results found</span>
    </div>

    <?php if (empty($results)): ?>
      <div class="bg-white rounded-3xl border border-[#EAE8F5] p-12 text-center max-w-md mx-auto">
        <h3 class="text-base font-bold text-[#191747]">No Matches Found</h3>
        <p class="text-xs text-[#737493] mt-1">Please try searching with another market name, jodi number or date.</p>
      </div>
    <?php else: ?>
      <div class="bg-white rounded-3xl border border-[#EAE8F5] p-6 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead>
              <tr class="text-[#737493] border-b border-[#EAE8F5] text-[11px] bg-[#FAF9FF]">
                <th class="py-3 px-4 font-bold rounded-l-xl">Date</th>
                <th class="py-3 px-4 font-bold">Market</th>
                <th class="py-3 px-4 font-bold text-center">Open</th>
                <th class="py-3 px-4 font-bold text-center">Close</th>
                <th class="py-3 px-4 font-bold text-center">Jodi</th>
                <th class="py-3 px-4 font-bold text-right rounded-r-xl">Full Result</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#F3EDFF] text-[#191747]">
              <?php foreach ($results as $r): ?>
                <tr class="hover:bg-[#FAF9FF] transition">
                  <td class="py-3.5 px-4 font-semibold text-[#737493] whitespace-nowrap"><?= format_app_date($r['result_date'], 'd M Y') ?></td>
                  <td class="py-3.5 px-4 font-bold text-[#191747]">
                    <a href="/market.php?id=<?= $r['market_id'] ?>" class="hover:text-[#6425D9]"><?= e($r['market_name']) ?></a>
                  </td>
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
      </div>
    <?php endif; ?>
  <?php endif; ?>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
