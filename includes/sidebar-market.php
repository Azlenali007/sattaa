<?php
/**
 * Right-side Market Details & Charts Panel
 * Matches reference image right panel exactly
 */

$selectedMarketId = isset($selectedMarketId) ? (int)$selectedMarketId : 1;
$market = Market::findById($selectedMarketId) ?: Market::findById(1) ?: [
    'id' => 1,
    'name' => 'KALYAN',
    'open_time' => '10:00 AM',
    'close_time' => '12:00 PM',
    'is_live' => 1
];

$latestMarketResult = Result::findLatestForMarket($market['id']);
$openDigits = format_digits($latestMarketResult['open_display'] ?? '8 2');
$closeDigits = format_digits($latestMarketResult['close_display'] ?? '7 5');

// Historical chart records
$jodiRecords = Result::getJodiChart($market['id'], 6, 0);

// Related markets
$relatedMarkets = Market::getPopular(4);
$recentResultsList = Result::getRecentHistory(4);
$lastUpdated = Setting::get('last_sync_time', '25 Apr 2025 10:32 AM');
?>
<aside class="w-full xl:w-[410px] shrink-0 space-y-6">

  <!-- Market Details Header & Chart Card -->
  <div class="bg-white rounded-2xl border border-[#EAE8F5] shadow-sm overflow-hidden" x-data="{ activeTab: 'jodi' }">
    
    <!-- Top Header Bar -->
    <div class="bg-[#191747] text-white p-5 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <a href="/markets.php" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition" aria-label="Back">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
          </svg>
        </a>
        <div>
          <h2 class="text-base font-black tracking-wide leading-none uppercase"><?= e($market['name']) ?></h2>
          <span class="text-[11px] font-medium text-purple-200 mt-1 block">Market Details &amp; Charts</span>
        </div>
      </div>
      <span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse-dot"></span>
        Live
      </span>
    </div>

    <!-- Info Metrics Bar -->
    <div class="grid grid-cols-3 divide-x divide-[#EAE8F5] border-b border-[#EAE8F5] bg-[#FAF9FF] text-center p-3 text-xs">
      <div class="px-2">
        <span class="text-[10px] text-[#737493] block font-medium">Market Time</span>
        <span class="font-bold text-[#191747] text-[11px] block mt-0.5 truncate"><?= e($market['open_time']) ?> - <?= e($market['close_time']) ?></span>
      </div>
      <div class="px-2">
        <span class="text-[10px] text-[#737493] block font-medium">Open / Close</span>
        <span class="font-bold text-emerald-600 text-[11px] block mt-0.5">Open</span>
      </div>
      <div class="px-2">
        <span class="text-[10px] text-[#737493] block font-medium">Last Updated</span>
        <span class="font-semibold text-[#191747] text-[10px] block mt-0.5 truncate"><?= e($lastUpdated) ?></span>
      </div>
    </div>

    <!-- Latest Result Banner -->
    <div class="p-5 border-b border-[#EAE8F5]">
      <div class="flex items-center justify-between text-xs font-semibold mb-3">
        <span class="flex items-center gap-1.5 text-[#191747]">
          <svg class="w-4 h-4 text-[#6425D9]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          Latest Result
        </span>
        <span class="text-[#737493] text-[11px]"><?= format_app_date($latestMarketResult['result_date'] ?? '2025-04-25', 'd M Y') ?> &gt;</span>
      </div>

      <!-- Result Boxes (8 2 | 7 5) -->
      <div class="bg-[#FAF9FF] border border-[#EAE8F5] rounded-xl p-4 flex items-center justify-around">
        <div class="text-center">
          <div class="flex items-center gap-2">
            <span class="w-10 h-11 bg-white border border-[#EAE8F5] rounded-lg flex items-center justify-center font-black text-xl text-[#191747] shadow-xs"><?= e($openDigits[0]) ?></span>
            <span class="w-10 h-11 bg-white border border-[#EAE8F5] rounded-lg flex items-center justify-center font-black text-xl text-[#191747] shadow-xs"><?= e($openDigits[1]) ?></span>
          </div>
          <span class="text-[11px] font-semibold text-[#737493] mt-1.5 block">Open</span>
        </div>

        <div class="h-10 w-[1px] bg-[#EAE8F5]"></div>

        <div class="text-center">
          <div class="flex items-center gap-2">
            <span class="w-10 h-11 bg-white border border-[#EAE8F5] rounded-lg flex items-center justify-center font-black text-xl text-[#191747] shadow-xs"><?= e($closeDigits[0]) ?></span>
            <span class="w-10 h-11 bg-white border border-[#EAE8F5] rounded-lg flex items-center justify-center font-black text-xl text-[#191747] shadow-xs"><?= e($closeDigits[1]) ?></span>
          </div>
          <span class="text-[11px] font-semibold text-[#737493] mt-1.5 block">Close</span>
        </div>
      </div>
    </div>

    <!-- Chart Tabs -->
    <div class="p-4">
      <div class="flex rounded-xl bg-[#FAF9FF] p-1 border border-[#EAE8F5] mb-4 text-xs font-semibold">
        <button 
          type="button" 
          @click="activeTab = 'jodi'" 
          :class="activeTab === 'jodi' ? 'bg-[#6425D9] text-white shadow-xs' : 'text-[#737493] hover:text-[#191747]'"
          class="flex-1 py-2 text-center rounded-lg transition"
        >
          Jodi Chart
        </button>
        <button 
          type="button" 
          @click="activeTab = 'history'" 
          :class="activeTab === 'history' ? 'bg-[#6425D9] text-white shadow-xs' : 'text-[#737493] hover:text-[#191747]'"
          class="flex-1 py-2 text-center rounded-lg transition"
        >
          History
        </button>
        <button 
          type="button" 
          @click="activeTab = 'date'" 
          :class="activeTab === 'date' ? 'bg-[#6425D9] text-white shadow-xs' : 'text-[#737493] hover:text-[#191747]'"
          class="flex-1 py-2 text-center rounded-lg transition"
        >
          Date Wise
        </button>
      </div>

      <!-- Jodi Chart Table -->
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead>
            <tr class="text-[#737493] border-b border-[#EAE8F5] text-[11px]">
              <th class="pb-2.5 font-semibold">Date</th>
              <th class="pb-2.5 font-semibold text-center">Jodi 1</th>
              <th class="pb-2.5 font-semibold text-center">Jodi 2</th>
              <th class="pb-2.5 font-semibold text-center">Jodi 3</th>
              <th class="pb-2.5 font-semibold text-center">Jodi 4</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#F3EDFF] text-[#191747]">
            <?php foreach ($jodiRecords as $rec): ?>
              <tr class="hover:bg-[#FAF9FF] transition">
                <td class="py-2.5 text-[11px] font-medium text-[#737493] whitespace-nowrap"><?= e($rec['date_formatted']) ?></td>
                <td class="py-2.5 text-center font-bold text-[#191747]"><?= e($rec['jodi_1']) ?></td>
                <td class="py-2.5 text-center font-bold text-[#191747]"><?= e($rec['jodi_2']) ?></td>
                <td class="py-2.5 text-center font-bold text-[#191747]"><?= e($rec['jodi_3']) ?></td>
                <td class="py-2.5 text-center font-bold text-[#191747]"><?= e($rec['jodi_4']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination in Sidebar matching reference -->
      <div class="flex items-center justify-center space-x-1.5 pt-4 border-t border-[#EAE8F5] mt-3 text-xs">
        <a href="/chart.php?id=<?= $market['id'] ?>&page=1" class="w-6 h-6 rounded-md flex items-center justify-center text-[#737493] hover:bg-[#F3EDFF]">&lt;</a>
        <a href="/chart.php?id=<?= $market['id'] ?>&page=1" class="w-6 h-6 rounded-md bg-[#6425D9] text-white flex items-center justify-center font-bold">1</a>
        <a href="/chart.php?id=<?= $market['id'] ?>&page=2" class="w-6 h-6 rounded-md flex items-center justify-center text-[#737493] hover:bg-[#F3EDFF]">2</a>
        <a href="/chart.php?id=<?= $market['id'] ?>&page=3" class="w-6 h-6 rounded-md flex items-center justify-center text-[#737493] hover:bg-[#F3EDFF]">3</a>
        <a href="/chart.php?id=<?= $market['id'] ?>&page=4" class="w-6 h-6 rounded-md flex items-center justify-center text-[#737493] hover:bg-[#F3EDFF]">4</a>
        <a href="/chart.php?id=<?= $market['id'] ?>&page=5" class="w-6 h-6 rounded-md flex items-center justify-center text-[#737493] hover:bg-[#F3EDFF]">5</a>
        <a href="/chart.php?id=<?= $market['id'] ?>&page=2" class="w-6 h-6 rounded-md flex items-center justify-center text-[#737493] hover:bg-[#F3EDFF]">&gt;</a>
      </div>

    </div>
  </div>

  <!-- Related Markets Card -->
  <div class="bg-white rounded-2xl border border-[#EAE8F5] p-5 shadow-sm">
    <div class="flex items-center gap-2 mb-4">
      <svg class="w-4 h-4 text-[#6425D9]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
      </svg>
      <h3 class="font-bold text-sm text-[#191747]">Related Markets</h3>
    </div>
    
    <div class="space-y-2.5">
      <?php foreach ($relatedMarkets as $rm): ?>
        <a href="/market.php?id=<?= $rm['id'] ?>" class="flex items-center justify-between p-3 rounded-xl bg-[#FAF9FF] hover:bg-[#F3EDFF] border border-[#EAE8F5] transition group">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-white border border-[#EAE8F5] flex items-center justify-center text-[#6425D9] group-hover:scale-105 transition-transform">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <circle cx="12" cy="12" r="9" stroke-width="2" />
                <circle cx="12" cy="12" r="3" fill="#6425D9" />
              </svg>
            </div>
            <div>
              <span class="block font-bold text-xs text-[#191747]"><?= e($rm['name']) ?></span>
              <span class="block text-[11px] text-[#737493]"><?= e($rm['open_time']) ?> - <?= e($rm['close_time']) ?></span>
            </div>
          </div>
          <svg class="w-4 h-4 text-[#737493] group-hover:text-[#6425D9] group-hover:translate-x-0.5 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Quick Links Card -->
  <div class="bg-white rounded-2xl border border-[#EAE8F5] p-5 shadow-sm">
    <div class="flex items-center gap-2 mb-4">
      <svg class="w-4 h-4 text-[#6425D9]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
      </svg>
      <h3 class="font-bold text-sm text-[#191747]">Quick Links</h3>
    </div>
    
    <div class="grid grid-cols-4 gap-2">
      <a href="/markets.php" class="flex flex-col items-center justify-center p-3 rounded-xl bg-[#FAF9FF] hover:bg-[#F3EDFF] border border-[#EAE8F5] transition text-center group">
        <div class="w-9 h-9 rounded-lg bg-white flex items-center justify-center text-[#6425D9] shadow-2xs group-hover:scale-105 transition">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
          </svg>
        </div>
        <span class="text-[10px] font-bold text-[#191747] mt-2 block">All Markets</span>
      </a>

      <a href="/results.php" class="flex flex-col items-center justify-center p-3 rounded-xl bg-[#FAF9FF] hover:bg-[#F3EDFF] border border-[#EAE8F5] transition text-center group">
        <div class="w-9 h-9 rounded-lg bg-white flex items-center justify-center text-[#6425D9] shadow-2xs group-hover:scale-105 transition">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
        </div>
        <span class="text-[10px] font-bold text-[#191747] mt-2 block">Latest Results</span>
      </a>

      <a href="/chart.php" class="flex flex-col items-center justify-center p-3 rounded-xl bg-[#FAF9FF] hover:bg-[#F3EDFF] border border-[#EAE8F5] transition text-center group">
        <div class="w-9 h-9 rounded-lg bg-white flex items-center justify-center text-[#6425D9] shadow-2xs group-hover:scale-105 transition">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
          </svg>
        </div>
        <span class="text-[10px] font-bold text-[#191747] mt-2 block">Jodi Chart</span>
      </a>

      <a href="/history.php" class="flex flex-col items-center justify-center p-3 rounded-xl bg-[#FAF9FF] hover:bg-[#F3EDFF] border border-[#EAE8F5] transition text-center group">
        <div class="w-9 h-9 rounded-lg bg-white flex items-center justify-center text-[#6425D9] shadow-2xs group-hover:scale-105 transition">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <span class="text-[10px] font-bold text-[#191747] mt-2 block">History</span>
      </a>
    </div>
  </div>

  <!-- Recent Results Sidebar List -->
  <div class="bg-white rounded-2xl border border-[#EAE8F5] p-5 shadow-sm">
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center gap-2">
        <svg class="w-4 h-4 text-[#6425D9]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h3 class="font-bold text-sm text-[#191747]">Recent Results</h3>
      </div>
      <a href="/results.php" class="text-xs font-semibold text-[#6425D9] hover:underline flex items-center gap-1">
        View All &gt;
      </a>
    </div>

    <div class="space-y-3">
      <?php foreach ($recentResultsList as $rr): 
        $num = str_replace(' - ', ' ', $rr['full_result'] ?: '8 2 7 5');
      ?>
        <div class="flex items-center justify-between py-2 border-b border-[#FAF9FF] last:border-b-0 text-xs">
          <div class="flex items-center gap-2.5">
            <div class="w-2.5 h-2.5 rounded-full border-2 border-[#6425D9]"></div>
            <div>
              <span class="font-bold text-[#191747] block"><?= e($rr['market_name']) ?></span>
              <span class="text-[10px] text-[#737493]"><?= format_app_date($rr['result_date'], 'd M Y') ?></span>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <span class="font-mono font-extrabold text-[#191747] tracking-wider"><?= e($num) ?></span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">Open</span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

</aside>
