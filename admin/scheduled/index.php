<?php
/**
 * Admin: Scheduled Publications Listing
 */
require_once __DIR__ . '/../includes/auth-check.php';

$pageTitle = 'Scheduled Publications';
$pdo = Database::getConnection();
$stmt = $pdo->query("
    SELECT s.*, m.name as market_name 
    FROM scheduled_publications s 
    JOIN markets m ON s.market_id = m.id 
    ORDER BY s.scheduled_date DESC, s.scheduled_time DESC
");
$schedules = $stmt->fetchAll();

require __DIR__ . '/../includes/head.php';
require __DIR__ . '/../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../includes/header.php'; ?>
  <?php require __DIR__ . '/../../includes/flash-message.php'; ?>

  <main class="p-6 sm:p-8 space-y-6 flex-1">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-black text-[#191747]">Scheduled Publications</h1>
        <p class="text-xs text-[#737493] mt-0.5">Pre-schedule automated release of results based on market close time.</p>
      </div>

      <a href="/admin/scheduled/create.php" class="px-4 py-2.5 rounded-xl bg-[#6425D9] hover:bg-[#521ec2] text-white text-xs font-bold flex items-center gap-2 shadow-xs transition w-fit">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span>Schedule Publication</span>
      </a>
    </div>

    <div class="bg-white rounded-2xl border border-[#EAE8F5] p-6 shadow-xs overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead>
            <tr class="text-[#737493] border-b border-[#EAE8F5] text-[11px] bg-[#FAF9FF]">
              <th class="py-3 px-4 font-bold rounded-l-xl">Market</th>
              <th class="py-3 px-4 font-bold">Scheduled Date</th>
              <th class="py-3 px-4 font-bold">Scheduled Time</th>
              <th class="py-3 px-4 font-bold text-center">Open Display</th>
              <th class="py-3 px-4 font-bold text-center">Close Display</th>
              <th class="py-3 px-4 font-bold text-center">Status</th>
              <th class="py-3 px-4 font-bold text-right rounded-r-xl">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#F3EDFF] text-[#191747]">
            <?php if (empty($schedules)): ?>
              <tr>
                <td colspan="7" class="py-8 text-center text-xs text-[#737493]">No pending or past scheduled publications.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($schedules as $s): ?>
                <tr class="hover:bg-[#FAF9FF] transition">
                  <td class="py-3.5 px-4 font-black"><?= e($s['market_name']) ?></td>
                  <td class="py-3.5 px-4 font-medium text-[#737493]"><?= format_app_date($s['scheduled_date'], 'd M Y') ?></td>
                  <td class="py-3.5 px-4 font-semibold"><?= e($s['scheduled_time']) ?></td>
                  <td class="py-3.5 px-4 text-center font-mono font-bold"><?= e($s['open_display']) ?></td>
                  <td class="py-3.5 px-4 text-center font-mono font-bold"><?= e($s['close_display']) ?></td>
                  <td class="py-3.5 px-4 text-center">
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold <?= $s['status'] === 'published' ? 'bg-emerald-50 text-emerald-600' : ($s['status'] === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-gray-100 text-gray-500') ?>">
                      <?= ucfirst($s['status']) ?>
                    </span>
                  </td>
                  <td class="py-3.5 px-4 text-right">
                    <?php if ($s['status'] === 'pending'): ?>
                      <form action="/admin/scheduled/cancel.php" method="POST" onsubmit="return confirm('Cancel this scheduled release?');" class="inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= $s['id'] ?>">
                        <button type="submit" class="text-xs font-bold text-rose-600 hover:underline">Cancel</button>
                      </form>
                    <?php else: ?>
                      <span class="text-xs text-[#737493]">-</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <?php require __DIR__ . '/../includes/footer.php'; ?>
</div>
