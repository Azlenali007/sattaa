<?php
/**
 * Admin: Full API Sync Logs Viewer
 */
require_once __DIR__ . '/../includes/auth-check.php';

$pageTitle = 'API Synchronization Logs';
$logs = ApiSyncService::getLogs(50);

require __DIR__ . '/../includes/head.php';
require __DIR__ . '/../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../includes/header.php'; ?>
  <?php require __DIR__ . '/../../includes/flash-message.php'; ?>

  <main class="p-6 sm:p-8 space-y-6 flex-1">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-black text-[#191747]">API Sync Audit History</h1>
        <p class="text-xs text-[#737493] mt-0.5">Chronological record of automated background and manual sync attempts.</p>
      </div>
      <a href="/admin/api-settings/index.php" class="text-xs font-bold text-[#6425D9] hover:underline">&larr; Back to Settings</a>
    </div>

    <div class="bg-white rounded-2xl border border-[#EAE8F5] p-6 shadow-xs overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead>
            <tr class="text-[#737493] border-b border-[#EAE8F5] text-[11px] bg-[#FAF9FF]">
              <th class="py-3 px-4 font-bold rounded-l-xl">Timestamp</th>
              <th class="py-3 px-4 font-bold">Endpoint</th>
              <th class="py-3 px-4 font-bold text-center">Status</th>
              <th class="py-3 px-4 font-bold text-center">Processed</th>
              <th class="py-3 px-4 font-bold text-right rounded-r-xl">Error Message</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#F3EDFF] text-[#191747]">
            <?php foreach ($logs as $l): ?>
              <tr class="hover:bg-[#FAF9FF]">
                <td class="py-3 px-4 text-[#737493] whitespace-nowrap"><?= format_app_date($l['created_at'], 'H:i:s d M Y') ?></td>
                <td class="py-3 px-4 font-mono text-[11px] text-[#191747]"><?= e($l['endpoint']) ?></td>
                <td class="py-3 px-4 text-center">
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $l['status'] === 'success' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' ?>">
                    <?= ucfirst($l['status']) ?>
                  </span>
                </td>
                <td class="py-3 px-4 text-center font-bold"><?= $l['records_processed'] ?></td>
                <td class="py-3 px-4 text-right text-rose-600 font-medium"><?= e($l['error_message'] ?: '-') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <?php require __DIR__ . '/../includes/footer.php'; ?>
</div>
