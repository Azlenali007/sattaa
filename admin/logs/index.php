<?php
/**
 * Admin: Activity Logs
 */
require_once __DIR__ . '/../includes/auth-check.php';

$pageTitle = 'Activity & Audit Logs';
$logs = Admin::getRecentLogs(50);

require __DIR__ . '/../includes/head.php';
require __DIR__ . '/../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../includes/header.php'; ?>
  <?php require __DIR__ . '/../../includes/flash-message.php'; ?>

  <main class="p-6 sm:p-8 space-y-6 flex-1">
    <div>
      <h1 class="text-2xl font-black text-[#191747]">Administrative Audit Logs</h1>
      <p class="text-xs text-[#737493] mt-0.5">Immutable record of logins, result updates, market changes, and system operations.</p>
    </div>

    <div class="bg-white rounded-2xl border border-[#EAE8F5] p-6 shadow-xs overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead>
            <tr class="text-[#737493] border-b border-[#EAE8F5] text-[11px] bg-[#FAF9FF]">
              <th class="py-3 px-4 font-bold rounded-l-xl">Timestamp</th>
              <th class="py-3 px-4 font-bold">Admin</th>
              <th class="py-3 px-4 font-bold">Action</th>
              <th class="py-3 px-4 font-bold">Module</th>
              <th class="py-3 px-4 font-bold">Details</th>
              <th class="py-3 px-4 font-bold text-right rounded-r-xl">IP Address</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#F3EDFF] text-[#191747]">
            <?php foreach ($logs as $l): ?>
              <tr class="hover:bg-[#FAF9FF]">
                <td class="py-3 px-4 text-[#737493] whitespace-nowrap"><?= format_app_date($l['created_at'], 'H:i:s d M Y') ?></td>
                <td class="py-3 px-4 font-bold"><?= e($l['username'] ?: 'admin') ?></td>
                <td class="py-3 px-4 font-extrabold text-[#6425D9]"><?= e($l['action']) ?></td>
                <td class="py-3 px-4 text-[#737493]"><?= e($l['module']) ?></td>
                <td class="py-3 px-4"><?= e($l['details'] ?: '-') ?></td>
                <td class="py-3 px-4 text-right font-mono text-[11px] text-[#737493]"><?= e($l['ip_address'] ?: '127.0.0.1') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <?php require __DIR__ . '/../includes/footer.php'; ?>
</div>
