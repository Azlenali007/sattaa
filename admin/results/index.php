<?php
/**
 * Admin: Results Listing & Management
 */
require_once __DIR__ . '/../includes/auth-check.php';

$pageTitle = 'Result Management';
$marketId = !empty($_GET['market_id']) ? (int)$_GET['market_id'] : null;
$date = $_GET['date'] ?? '';

$filters = [];
if ($marketId) $filters['market_id'] = $marketId;
if ($date) $filters['date'] = $date;

$allMarkets = Market::getActive();
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$results = Result::allPaginated($perPage, ($page - 1) * $perPage, $filters);
$totalRecords = Result::count();
$paginator = new Paginator($totalRecords, $perPage, $page);

require __DIR__ . '/../includes/head.php';
require __DIR__ . '/../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../includes/header.php'; ?>
  <?php require __DIR__ . '/../../includes/flash-message.php'; ?>

  <main class="p-6 sm:p-8 space-y-6 flex-1">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-black text-[#191747]">Result Management</h1>
        <p class="text-xs text-[#737493] mt-0.5">Declare, edit, or archive daily market results and jodi numbers.</p>
      </div>

      <div class="flex items-center space-x-2">
        <a href="/admin/results/bulk-import.php" class="px-3.5 py-2.5 rounded-xl border border-[#EAE8F5] bg-white hover:bg-[#FAF9FF] text-[#191747] text-xs font-bold transition">
          Bulk Import
        </a>
        <a href="/admin/results/create.php" class="px-4 py-2.5 rounded-xl bg-[#6425D9] hover:bg-[#521ec2] text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          <span>Declare New Result</span>
        </a>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl border border-[#EAE8F5] p-4 shadow-xs">
      <form action="/admin/results/index.php" method="GET" class="flex flex-wrap items-center gap-3">
        <select name="market_id" class="px-3 py-2 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
          <option value="">All Markets</option>
          <?php foreach ($allMarkets as $m): ?>
            <option value="<?= $m['id'] ?>" <?= $marketId === $m['id'] ? 'selected' : '' ?>><?= e($m['name']) ?></option>
          <?php endforeach; ?>
        </select>

        <input type="date" name="date" value="<?= e($date) ?>" class="px-3 py-2 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">

        <button type="submit" class="px-4 py-2 rounded-xl bg-[#6425D9] text-white text-xs font-bold">Filter</button>
        <?php if ($marketId || $date): ?>
          <a href="/admin/results/index.php" class="px-3 py-2 rounded-xl bg-gray-100 text-gray-600 text-xs font-bold">Clear</a>
        <?php endif; ?>
      </form>
    </div>

    <!-- Results Table -->
    <div class="bg-white rounded-2xl border border-[#EAE8F5] p-6 shadow-xs overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead>
            <tr class="text-[#737493] border-b border-[#EAE8F5] text-[11px] bg-[#FAF9FF]">
              <th class="py-3 px-4 font-bold rounded-l-xl">Date</th>
              <th class="py-3 px-4 font-bold">Market Name</th>
              <th class="py-3 px-4 font-bold text-center">Open Display</th>
              <th class="py-3 px-4 font-bold text-center">Close Display</th>
              <th class="py-3 px-4 font-bold text-center">Jodi</th>
              <th class="py-3 px-4 font-bold text-right">Full Result</th>
              <th class="py-3 px-4 font-bold text-center">Status</th>
              <th class="py-3 px-4 font-bold text-right rounded-r-xl">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#F3EDFF] text-[#191747]">
            <?php foreach ($results as $r): ?>
              <tr class="hover:bg-[#FAF9FF] transition">
                <td class="py-3.5 px-4 font-medium text-[#737493] whitespace-nowrap"><?= format_app_date($r['result_date'], 'd M Y') ?></td>
                <td class="py-3.5 px-4 font-black"><?= e($r['market_name']) ?></td>
                <td class="py-3.5 px-4 text-center font-mono font-bold"><?= e($r['open_display']) ?></td>
                <td class="py-3.5 px-4 text-center font-mono font-bold"><?= e($r['close_display']) ?></td>
                <td class="py-3.5 px-4 text-center">
                  <span class="inline-block px-2.5 py-0.5 rounded-lg bg-[#F3EDFF] text-[#6425D9] font-black font-mono">
                    <?= e($r['jodi'] ?: '--') ?>
                  </span>
                </td>
                <td class="py-3.5 px-4 text-right font-mono font-black text-[#6425D9] text-sm"><?= e($r['full_result']) ?></td>
                <td class="py-3.5 px-4 text-center">
                  <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                    <?= ucfirst($r['status']) ?>
                  </span>
                </td>
                <td class="py-3.5 px-4 text-right">
                  <div class="flex items-center justify-end space-x-2">
                    <a href="/admin/results/edit.php?id=<?= $r['id'] ?>" class="px-2.5 py-1 rounded-lg bg-[#FAF9FF] border border-[#EAE8F5] text-[#6425D9] font-bold text-[11px] hover:bg-[#F3EDFF]">
                      Edit
                    </a>
                    <form action="/admin/results/delete.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this result?');" class="inline">
                      <?= csrf_field() ?>
                      <input type="hidden" name="id" value="<?= $r['id'] ?>">
                      <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 font-bold text-[11px] hover:bg-rose-100">
                        Delete
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <?php 
        $baseUrl = "/admin/results/index.php?" . ($marketId ? "market_id={$marketId}&" : "") . ($date ? "date={$date}&" : "");
        require __DIR__ . '/../../includes/pagination.php'; 
      ?>
    </div>
  </main>

  <?php require __DIR__ . '/../includes/footer.php'; ?>
</div>
