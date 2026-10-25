<?php
/**
 * Admin: Market Management Listing
 */
require_once __DIR__ . '/../includes/auth-check.php';

$pageTitle = 'Market Management';
$markets = Market::all();

require __DIR__ . '/../includes/head.php';
require __DIR__ . '/../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../includes/header.php'; ?>
  <?php require __DIR__ . '/../../includes/flash-message.php'; ?>

  <main class="p-6 sm:p-8 space-y-6 flex-1">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-black text-[#191747]">Market Management</h1>
        <p class="text-xs text-[#737493] mt-0.5">Manage daily operating markets, schedules, and display settings.</p>
      </div>

      <a href="/admin/markets/create.php" class="px-4 py-2.5 rounded-xl bg-[#6425D9] hover:bg-[#521ec2] text-white text-xs font-bold flex items-center gap-2 shadow-xs transition w-fit">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span>Add New Market</span>
      </a>
    </div>

    <div class="bg-white rounded-2xl border border-[#EAE8F5] p-6 shadow-xs overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead>
            <tr class="text-[#737493] border-b border-[#EAE8F5] text-[11px] bg-[#FAF9FF]">
              <th class="py-3 px-4 font-bold rounded-l-xl">Order</th>
              <th class="py-3 px-4 font-bold">Market Name</th>
              <th class="py-3 px-4 font-bold">Slug</th>
              <th class="py-3 px-4 font-bold">Open Time</th>
              <th class="py-3 px-4 font-bold">Close Time</th>
              <th class="py-3 px-4 font-bold text-center">Status</th>
              <th class="py-3 px-4 font-bold text-center">Live Indicator</th>
              <th class="py-3 px-4 font-bold text-right rounded-r-xl">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#F3EDFF] text-[#191747]">
            <?php foreach ($markets as $m): ?>
              <tr class="hover:bg-[#FAF9FF] transition">
                <td class="py-3.5 px-4 font-bold text-[#737493]"><?= e($m['display_order']) ?></td>
                <td class="py-3.5 px-4 font-black text-sm"><?= e($m['name']) ?></td>
                <td class="py-3.5 px-4 text-[#737493] font-mono"><?= e($m['slug']) ?></td>
                <td class="py-3.5 px-4 font-semibold"><?= e($m['open_time']) ?></td>
                <td class="py-3.5 px-4 font-semibold"><?= e($m['close_time']) ?></td>
                <td class="py-3.5 px-4 text-center">
                  <a href="/admin/markets/status.php?id=<?= $m['id'] ?>&toggle=status" class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold <?= $m['status'] === 'active' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-gray-100 text-gray-500 border border-gray-200' ?>">
                    <?= ucfirst($m['status']) ?>
                  </a>
                </td>
                <td class="py-3.5 px-4 text-center">
                  <a href="/admin/markets/status.php?id=<?= $m['id'] ?>&toggle=live" class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold <?= !empty($m['is_live']) ? 'bg-purple-100 text-[#6425D9] border border-purple-200' : 'bg-gray-50 text-gray-400 border border-gray-200' ?>">
                    <?= !empty($m['is_live']) ? '● Live Now' : 'Upcoming' ?>
                  </a>
                </td>
                <td class="py-3.5 px-4 text-right">
                  <div class="flex items-center justify-end space-x-2">
                    <a href="/admin/markets/edit.php?id=<?= $m['id'] ?>" class="px-2.5 py-1 rounded-lg bg-[#FAF9FF] border border-[#EAE8F5] text-[#6425D9] font-bold text-[11px] hover:bg-[#F3EDFF]">
                      Edit
                    </a>
                    <form action="/admin/markets/delete.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this market and its associated results?');" class="inline">
                      <?= csrf_field() ?>
                      <input type="hidden" name="id" value="<?= $m['id'] ?>">
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
    </div>
  </main>

  <?php require __DIR__ . '/../includes/footer.php'; ?>
</div>
