<?php
/**
 * Admin: Charts Management
 */
require_once __DIR__ . '/../includes/auth-check.php';

$pageTitle = 'Charts & Historical Data';
$markets = Market::getActive();
$selectedId = (int)($_GET['id'] ?? ($markets[0]['id'] ?? 1));
$market = Market::findById($selectedId) ?: ($markets[0] ?? null);

$records = $market ? Result::getJodiChart($market['id'], 25, 0) : [];

require __DIR__ . '/../includes/head.php';
require __DIR__ . '/../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../includes/header.php'; ?>
  <?php require __DIR__ . '/../../includes/flash-message.php'; ?>

  <main class="p-6 sm:p-8 space-y-6 flex-1">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-black text-[#191747]">Charts &amp; History Overview</h1>
        <p class="text-xs text-[#737493] mt-0.5">Audit historical jodi records and export datasets.</p>
      </div>

      <div class="flex items-center space-x-3">
        <a href="/admin/charts/export.php?id=<?= $selectedId ?>" class="px-4 py-2.5 rounded-xl border border-[#EAE8F5] bg-white hover:bg-[#FAF9FF] text-[#191747] text-xs font-bold flex items-center gap-2 shadow-xs transition">
          <svg class="w-4 h-4 text-[#6425D9]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
          <span>Export to CSV</span>
        </a>

        <select onchange="location.href='/admin/charts/index.php?id=' + this.value" class="px-3.5 py-2.5 rounded-xl bg-white border border-[#EAE8F5] text-xs font-bold text-[#191747]">
          <?php foreach ($markets as $m): ?>
            <option value="<?= $m['id'] ?>" <?= $m['id'] == $selectedId ? 'selected' : '' ?>><?= e($m['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-[#EAE8F5] p-6 shadow-xs overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead>
            <tr class="text-[#737493] border-b border-[#EAE8F5] text-[11px] bg-[#FAF9FF]">
              <th class="py-3 px-4 font-bold rounded-l-xl">Date</th>
              <th class="py-3 px-4 font-bold text-center">Jodi 1</th>
              <th class="py-3 px-4 font-bold text-center">Jodi 2</th>
              <th class="py-3 px-4 font-bold text-center">Jodi 3</th>
              <th class="py-3 px-4 font-bold text-center">Jodi 4</th>
              <th class="py-3 px-4 font-bold text-right rounded-r-xl">Result</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#F3EDFF] text-[#191747]">
            <?php foreach ($records as $r): ?>
              <tr class="hover:bg-[#FAF9FF] transition">
                <td class="py-3 px-4 font-medium text-[#737493] whitespace-nowrap"><?= e($r['date_formatted']) ?></td>
                <td class="py-3 px-4 text-center font-bold"><?= e($r['jodi_1']) ?></td>
                <td class="py-3 px-4 text-center font-bold"><?= e($r['jodi_2']) ?></td>
                <td class="py-3 px-4 text-center font-bold"><?= e($r['jodi_3']) ?></td>
                <td class="py-3 px-4 text-center font-bold"><?= e($r['jodi_4']) ?></td>
                <td class="py-3 px-4 text-right font-mono font-black text-[#6425D9]"><?= e($r['full_result']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <?php require __DIR__ . '/../includes/footer.php'; ?>
</div>
