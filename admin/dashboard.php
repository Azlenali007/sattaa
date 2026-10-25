<?php
/**
 * Super Admin Dashboard
 */
require_once __DIR__ . '/includes/auth-check.php';

$pageTitle = 'Dashboard Overview';

$totalMarkets = Market::count();
$activeMarkets = Market::countActive();
$totalResults = Result::count();
$lastSyncTime = Setting::get('last_sync_time', '25 Apr 2025 10:32 AM');
$recentLogs = Admin::getRecentLogs(8);
$latestResults = Result::allPaginated(5, 0);

require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/includes/header.php'; ?>
  <?php require __DIR__ . '/../../includes/flash-message.php'; ?>

  <main class="p-6 sm:p-8 space-y-8 flex-1">
    
    <!-- Welcome Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-black text-[#191747]">Welcome, Super Admin</h1>
        <p class="text-xs text-[#737493] mt-0.5">Control and monitor Satka Matka markets, live result feeds, and historical charts.</p>
      </div>

      <!-- Quick Actions -->
      <div class="flex flex-wrap items-center gap-2">
        <a href="/admin/markets/create.php" class="px-3.5 py-2 rounded-xl bg-[#6425D9] hover:bg-[#521ec2] text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          <span>Add Market</span>
        </a>
        <a href="/admin/results/create.php" class="px-3.5 py-2 rounded-xl bg-white border border-[#EAE8F5] hover:bg-[#FAF9FF] text-[#191747] text-xs font-bold flex items-center gap-1.5 shadow-xs transition">
          <svg class="w-4 h-4 text-[#6425D9]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <span>Publish Result</span>
        </a>
        <a href="/admin/api-settings/sync-now.php" class="px-3.5 py-2 rounded-xl bg-[#F3EDFF] hover:bg-[#e7d8ff] text-[#6425D9] text-xs font-bold flex items-center gap-1.5 transition">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
          <span>Sync API Now</span>
        </a>
      </div>
    </div>

    <!-- 4 Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-2xl border border-[#EAE8F5] p-5 shadow-xs">
        <div class="flex items-center justify-between text-[#737493] mb-2 text-xs font-semibold">
          <span>Total Markets</span>
          <div class="w-7 h-7 rounded-lg bg-[#F3EDFF] text-[#6425D9] flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
          </div>
        </div>
        <div class="text-2xl font-black text-[#191747]"><?= $totalMarkets ?></div>
        <div class="text-[11px] text-emerald-600 font-bold mt-1">Configured in Database</div>
      </div>

      <div class="bg-white rounded-2xl border border-[#EAE8F5] p-5 shadow-xs">
        <div class="flex items-center justify-between text-[#737493] mb-2 text-xs font-semibold">
          <span>Active Markets</span>
          <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          </div>
        </div>
        <div class="text-2xl font-black text-[#191747]"><?= $activeMarkets ?></div>
        <div class="text-[11px] text-[#737493] font-medium mt-1">Accepting live schedules</div>
      </div>

      <div class="bg-white rounded-2xl border border-[#EAE8F5] p-5 shadow-xs">
        <div class="flex items-center justify-between text-[#737493] mb-2 text-xs font-semibold">
          <span>Published Results</span>
          <div class="w-7 h-7 rounded-lg bg-[#F3EDFF] text-[#6425D9] flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          </div>
        </div>
        <div class="text-2xl font-black text-[#191747]"><?= $totalResults ?></div>
        <div class="text-[11px] text-[#737493] font-medium mt-1">Daily records archived</div>
      </div>

      <div class="bg-white rounded-2xl border border-[#EAE8F5] p-5 shadow-xs">
        <div class="flex items-center justify-between text-[#737493] mb-2 text-xs font-semibold">
          <span>Last API Sync</span>
          <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
          </div>
        </div>
        <div class="text-sm font-extrabold text-[#191747] mt-1"><?= e($lastSyncTime) ?></div>
        <div class="text-[11px] text-emerald-600 font-bold mt-1">Status: Operational</div>
      </div>
    </div>

    <!-- Two-column Dashboard Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
      
      <!-- Recent Results Table -->
      <div class="lg:col-span-7 bg-white rounded-2xl border border-[#EAE8F5] p-6 shadow-xs">
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-extrabold text-sm text-[#191747]">Latest Published Results</h2>
          <a href="/admin/results/index.php" class="text-xs font-bold text-[#6425D9] hover:underline">Manage All</a>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead>
              <tr class="text-[#737493] border-b border-[#EAE8F5] text-[11px]">
                <th class="pb-2.5 font-semibold">Date</th>
                <th class="pb-2.5 font-semibold">Market</th>
                <th class="pb-2.5 font-semibold text-center">Jodi</th>
                <th class="pb-2.5 font-semibold text-right">Result</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#FAF9FF] text-[#191747]">
              <?php foreach ($latestResults as $lr): ?>
                <tr class="hover:bg-[#FAF9FF]">
                  <td class="py-2.5 text-[#737493] whitespace-nowrap"><?= format_app_date($lr['result_date'], 'd M Y') ?></td>
                  <td class="py-2.5 font-bold"><?= e($lr['market_name']) ?></td>
                  <td class="py-2.5 text-center font-mono font-bold text-[#6425D9]"><?= e($lr['jodi'] ?: '--') ?></td>
                  <td class="py-2.5 text-right font-mono font-black"><?= e($lr['full_result']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Activity Logs Feed -->
      <div class="lg:col-span-5 bg-white rounded-2xl border border-[#EAE8F5] p-6 shadow-xs">
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-extrabold text-sm text-[#191747]">Recent Activity Logs</h2>
          <a href="/admin/logs/index.php" class="text-xs font-bold text-[#6425D9] hover:underline">View All</a>
        </div>

        <div class="space-y-3">
          <?php if (empty($recentLogs)): ?>
            <p class="text-xs text-[#737493] py-4 text-center">No logged activity records yet.</p>
          <?php else: ?>
            <?php foreach ($recentLogs as $log): ?>
              <div class="flex items-start gap-3 text-xs border-b border-[#FAF9FF] pb-2.5 last:border-b-0">
                <div class="w-6 h-6 rounded-full bg-[#F3EDFF] text-[#6425D9] flex items-center justify-center shrink-0 text-[10px] font-bold">
                  ✓
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between">
                    <span class="font-bold text-[#191747] truncate"><?= e($log['action']) ?> (<?= e($log['module']) ?>)</span>
                    <span class="text-[10px] text-[#737493] whitespace-nowrap"><?= format_app_date($log['created_at'], 'H:i d M') ?></span>
                  </div>
                  <p class="text-[11px] text-[#737493] truncate mt-0.5"><?= e($log['details'] ?: 'Activity executed') ?></p>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

    </div>

  </main>
  <?php require __DIR__ . '/includes/footer.php'; ?>
</div>
