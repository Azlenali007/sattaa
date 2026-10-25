<?php
/**
 * Admin: Database Backups
 */
require_once __DIR__ . '/../includes/auth-check.php';

$pageTitle = 'Database Backups';
$backups = BackupService::listBackups();

require __DIR__ . '/../includes/head.php';
require __DIR__ . '/../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../includes/header.php'; ?>
  <?php require __DIR__ . '/../../includes/flash-message.php'; ?>

  <main class="p-6 sm:p-8 space-y-6 flex-1 max-w-4xl">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-black text-[#191747]">Database Snapshots &amp; Backups</h1>
        <p class="text-xs text-[#737493] mt-0.5">Export full SQL schemas and records for cold storage and archival.</p>
      </div>

      <form action="/admin/backups/create.php" method="POST">
        <?= csrf_field() ?>
        <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#6425D9] hover:bg-[#521ec2] text-white text-xs font-bold flex items-center gap-2 shadow-xs transition">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
          <span>Create New Backup</span>
        </button>
      </form>
    </div>

    <div class="bg-white rounded-2xl border border-[#EAE8F5] p-6 shadow-xs overflow-hidden">
      <?php if (empty($backups)): ?>
        <div class="py-8 text-center text-xs text-[#737493]">
          No backups generated yet. Click "Create New Backup" above to export a full SQL database snapshot.
        </div>
      <?php else: ?>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead>
              <tr class="text-[#737493] border-b border-[#EAE8F5] text-[11px] bg-[#FAF9FF]">
                <th class="py-3 px-4 font-bold rounded-l-xl">File Name</th>
                <th class="py-3 px-4 font-bold">Created Date</th>
                <th class="py-3 px-4 font-bold">Size</th>
                <th class="py-3 px-4 font-bold text-right rounded-r-xl">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#F3EDFF] text-[#191747]">
              <?php foreach ($backups as $b): ?>
                <tr class="hover:bg-[#FAF9FF]">
                  <td class="py-3 px-4 font-mono font-bold text-[#191747]"><?= e($b['filename']) ?></td>
                  <td class="py-3 px-4 text-[#737493]"><?= e($b['created_at']) ?></td>
                  <td class="py-3 px-4 font-semibold"><?= round($b['size'] / 1024, 1) ?> KB</td>
                  <td class="py-3 px-4 text-right">
                    <a href="/admin/backups/download.php?file=<?= urlencode($b['filename']) ?>" class="px-3 py-1 rounded-lg bg-[#F3EDFF] text-[#6425D9] font-bold text-xs hover:bg-[#e7d8ff] transition">
                      Download SQL
                    </a>
                  </td>
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
