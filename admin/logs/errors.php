<?php
/**
 * Admin: Error Logs Viewer
 */
require_once __DIR__ . '/../includes/auth-check.php';

$pageTitle = 'System & API Error Logs';
$pdo = Database::getConnection();
$stmt = $pdo->query("SELECT * FROM api_sync_logs WHERE status = 'error' ORDER BY created_at DESC LIMIT 30");
$errorLogs = $stmt->fetchAll();

require __DIR__ . '/../includes/head.php';
require __DIR__ . '/../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../includes/header.php'; ?>
  <?php require __DIR__ . '/../../includes/flash-message.php'; ?>

  <main class="p-6 sm:p-8 space-y-6 flex-1">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-black text-[#191747]">System Error Logs</h1>
        <p class="text-xs text-[#737493] mt-0.5">Diagnose synchronization network timeouts and runtime errors.</p>
      </div>
      <a href="/admin/logs/index.php" class="text-xs font-bold text-[#6425D9] hover:underline">&larr; Back to Activity Logs</a>
    </div>

    <div class="bg-white rounded-2xl border border-[#EAE8F5] p-6 shadow-xs overflow-hidden">
      <?php if (empty($errorLogs)): ?>
        <div class="py-8 text-center text-xs text-[#737493]">
          <span class="text-emerald-600 font-bold block mb-1">✓ No active errors logged</span>
          Everything is running smoothly without recorded exceptions.
        </div>
      <?php else: ?>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead>
              <tr class="text-[#737493] border-b border-[#EAE8F5] text-[11px] bg-[#FAF9FF]">
                <th class="py-3 px-4 font-bold rounded-l-xl">Timestamp</th>
                <th class="py-3 px-4 font-bold">Endpoint</th>
                <th class="py-3 px-4 font-bold text-right rounded-r-xl">Error Reason</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#F3EDFF] text-[#191747]">
              <?php foreach ($errorLogs as $err): ?>
                <tr>
                  <td class="py-3 px-4 text-[#737493] whitespace-nowrap"><?= format_app_date($err['created_at'], 'H:i:s d M Y') ?></td>
                  <td class="py-3 px-4 font-mono text-[11px]"><?= e($err['endpoint']) ?></td>
                  <td class="py-3 px-4 text-right text-rose-600 font-medium"><?= e($err['error_message']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <?php require __DIR__ . '/../includes/footer.php'; ?>
</div>
