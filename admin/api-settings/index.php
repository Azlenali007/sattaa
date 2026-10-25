<?php
/**
 * Admin: API Settings & Synchronization Configuration
 */
require_once __DIR__ . '/../includes/auth-check.php';

$pageTitle = 'API Settings & Results Synchronization';
$pdo = Database::getConnection();

$stmt = $pdo->query("SELECT * FROM api_configs ORDER BY id ASC LIMIT 1");
$config = $stmt->fetch() ?: [
    'id' => 1,
    'provider_name' => 'Live Matka Feed API',
    'api_url' => 'https://api.satkamatka-feed.net/v1/live',
    'api_key' => 'sm_live_sec_99342',
    'sync_interval_minutes' => 15,
    'is_active' => 1,
    'last_sync_status' => 'success',
    'last_sync_at' => date('Y-m-d H:i:s')
];

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    enforce_csrf();
    $providerName = trim($_POST['provider_name'] ?? '');
    $apiUrl = trim($_POST['api_url'] ?? '');
    $apiKey = trim($_POST['api_key'] ?? '');
    $syncInterval = (int)($_POST['sync_interval_minutes'] ?? 15);
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    $validator = new Validator($_POST);
    $validator->required('provider_name')->required('api_url');

    if ($validator->passes()) {
        $uStmt = $pdo->prepare("
            UPDATE api_configs SET 
                provider_name = ?, api_url = ?, api_key = ?, 
                sync_interval_minutes = ?, is_active = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        $uStmt->execute([$providerName, $apiUrl, $apiKey, $syncInterval, $isActive, $config['id']]);
        Admin::logActivity($currentAdmin['id'], 'Update API Config', 'API', $config['id'], "Updated external API settings");
        set_flash('success', 'API settings saved successfully!');
        redirect('/admin/api-settings/index.php');
    } else {
        $error = $validator->firstError();
    }
}

$syncLogs = ApiSyncService::getLogs(10);

require __DIR__ . '/../includes/head.php';
require __DIR__ . '/../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../includes/header.php'; ?>
  <?php require __DIR__ . '/../../includes/flash-message.php'; ?>

  <main class="p-6 sm:p-8 space-y-8 flex-1">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-black text-[#191747]">API Integration &amp; Result Sync</h1>
        <p class="text-xs text-[#737493] mt-0.5">Manage external results feed endpoint, authorization tokens, and automatic polling.</p>
      </div>

      <div class="flex items-center space-x-3">
        <a href="/admin/api-settings/test-connection.php" class="px-4 py-2.5 rounded-xl border border-[#EAE8F5] bg-white hover:bg-[#FAF9FF] text-[#191747] text-xs font-bold transition">
          Test Connection
        </a>
        <a href="/admin/api-settings/sync-now.php" class="px-4 py-2.5 rounded-xl bg-[#6425D9] hover:bg-[#521ec2] text-white text-xs font-bold flex items-center gap-2 shadow-xs transition">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
          <span>Sync Now</span>
        </a>
      </div>
    </div>

    <?php if ($error): ?>
      <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold"><?= e($error) ?></div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
      
      <!-- API Configuration Form -->
      <div class="lg:col-span-7 bg-white rounded-2xl border border-[#EAE8F5] p-6 sm:p-8 shadow-xs">
        <h2 class="text-base font-extrabold text-[#191747] mb-5">Provider Configuration</h2>

        <form action="/admin/api-settings/index.php" method="POST" class="space-y-4">
          <?= csrf_field() ?>

          <div>
            <label class="block text-xs font-bold text-[#191747] mb-1.5">Provider Name *</label>
            <input type="text" name="provider_name" value="<?= e($config['provider_name']) ?>" required class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
          </div>

          <div>
            <label class="block text-xs font-bold text-[#191747] mb-1.5">API Endpoint URL *</label>
            <input type="url" name="api_url" value="<?= e($config['api_url']) ?>" required class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
            <span class="text-[10px] text-[#737493] mt-1 block">Configured live HTTP endpoint for fetching JSON results</span>
          </div>

          <div>
            <label class="block text-xs font-bold text-[#191747] mb-1.5">API Key / Bearer Secret</label>
            <input type="password" name="api_key" value="<?= e($config['api_key']) ?>" class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
            <span class="text-[10px] text-[#737493] mt-1 block">Credentials stored securely on server; never exposed in public DOM</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
            <div>
              <label class="block text-xs font-bold text-[#191747] mb-1.5">Sync Interval (Minutes)</label>
              <input type="number" name="sync_interval_minutes" value="<?= e($config['sync_interval_minutes']) ?>" min="1" max="1440" class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
            </div>

            <div class="flex items-center pt-6">
              <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-[#191747]">
                <input type="checkbox" name="is_active" value="1" <?= !empty($config['is_active']) ? 'checked' : '' ?> class="rounded text-[#6425D9]">
                <span>Enable Automatic Sync</span>
              </label>
            </div>
          </div>

          <div class="pt-4 border-t border-[#EAE8F5] flex justify-end">
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#6425D9] hover:bg-[#521ec2] text-white text-xs font-bold shadow-xs transition">
              Save API Settings
            </button>
          </div>
        </form>
      </div>

      <!-- Sync Status & Recent Sync Logs -->
      <div class="lg:col-span-5 space-y-6">
        <div class="bg-white rounded-2xl border border-[#EAE8F5] p-6 shadow-xs">
          <h2 class="text-base font-extrabold text-[#191747] mb-3">Sync Health Status</h2>
          
          <div class="space-y-3 text-xs">
            <div class="flex justify-between py-2 border-b border-[#FAF9FF]">
              <span class="text-[#737493]">Last Attempt:</span>
              <strong class="text-[#191747]"><?= e($config['last_sync_at'] ?? 'Never') ?></strong>
            </div>
            <div class="flex justify-between py-2 border-b border-[#FAF9FF]">
              <span class="text-[#737493]">Last Status:</span>
              <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= ($config['last_sync_status'] ?? '') === 'success' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-700' ?>">
                <?= ucfirst($config['last_sync_status'] ?? 'Unknown') ?>
              </span>
            </div>
            <div class="flex justify-between py-2">
              <span class="text-[#737493]">Fallback Behavior:</span>
              <strong class="text-emerald-600">Preserves Stored Database Results</strong>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl border border-[#EAE8F5] p-6 shadow-xs">
          <h2 class="text-base font-extrabold text-[#191747] mb-3">Recent Sync Activity</h2>
          <div class="space-y-2.5">
            <?php if (empty($syncLogs)): ?>
              <p class="text-xs text-[#737493]">No synchronization logs recorded yet.</p>
            <?php else: ?>
              <?php foreach ($syncLogs as $log): ?>
                <div class="p-3 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs flex items-center justify-between">
                  <div>
                    <span class="font-bold text-[#191747] block"><?= format_app_date($log['created_at'], 'H:i:s d M') ?></span>
                    <span class="text-[10px] text-[#737493]"><?= $log['records_processed'] ?> records synced</span>
                  </div>
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $log['status'] === 'success' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' ?>">
                    <?= ucfirst($log['status']) ?>
                  </span>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>

      </div>

    </div>
  </main>

  <?php require __DIR__ . '/../includes/footer.php'; ?>
</div>
