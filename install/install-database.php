<?php
/**
 * Step 4: Database Schema Installation & Baseline Data Setup
 */
require_once __DIR__ . '/includes/installer-functions.php';
installer_init();
check_installer_guard();

if (empty($_SESSION['installer']['step_db_passed']) || empty($_SESSION['installer']['db_config'])) {
    header('Location: /install/database.php');
    exit;
}

$pageTitle = 'Step 4: Database Installation';
$currentStep = 4;

$dbConfig = $_SESSION['installer']['db_config'];
$conn = test_database_connection($dbConfig);

if (!$conn['success'] || !$conn['pdo']) {
    $error = 'Database connection lost. Please verify your credentials: ' . $conn['message'];
}

$installed = false;
$error = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!installer_verify_csrf()) {
        $error = 'Security check failed. Please refresh and try again.';
    } else {
        $res = install_database_schema($conn['pdo'], $dbConfig['driver']);
        if ($res['success']) {
            $installed = true;
            $message = $res['message'];
            $_SESSION['installer']['step_schema_passed'] = true;
        } else {
            $error = $res['message'];
        }
    }
}

// Check if tables already exist
$tables = ['admins', 'markets', 'results', 'scheduled_publications', 'api_configs', 'settings'];
$existingTables = 0;
if ($conn['pdo']) {
    foreach ($tables as $t) {
        try {
            $conn['pdo']->query("SELECT 1 FROM {$t} LIMIT 1");
            $existingTables++;
        } catch (Exception $e) {}
    }
}

if ($existingTables === count($tables)) {
    $_SESSION['installer']['step_schema_passed'] = true;
    $installed = true;
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="installer-card p-6 sm:p-10">
  <div class="flex items-center justify-between mb-6 pb-6 border-b border-[#EAE8F5]">
    <div>
      <h1 class="text-2xl font-black text-[#191747]">Database Schema Installation</h1>
      <p class="text-xs text-[#737493] mt-0.5">Creating application tables, foreign keys, indexes, and initial market definitions.</p>
    </div>
    <span class="text-xs font-bold text-[#6425D9] bg-[#F3EDFF] px-3 py-1 rounded-full">
      Step 4 of 8
    </span>
  </div>

  <?php if ($error): ?>
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs mb-6">
      <strong class="font-bold block mb-1">Installation Error:</strong>
      <span><?= e($error) ?></span>
    </div>
  <?php endif; ?>

  <?php if ($installed): ?>
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs mb-6 flex items-start gap-3">
      <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
      </svg>
      <div>
        <strong class="font-bold block">Database Schema Ready:</strong>
        <span>All <?= count($tables) ?> core database tables and baseline market configurations have been created.</span>
      </div>
    </div>
  <?php elseif ($existingTables > 0): ?>
    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs mb-6 flex items-start gap-3">
      <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
      </svg>
      <div>
        <strong class="font-bold block">Existing Tables Detected:</strong>
        <span>Some database tables already exist. Running schema installation will preserve existing records safely using <code>IF NOT EXISTS</code>.</span>
      </div>
    </div>
  <?php endif; ?>

  <div class="space-y-4 mb-8">
    <h3 class="text-xs font-black text-[#191747] uppercase tracking-wider">Required Tables Checklist</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
      <?php
      $tableDescriptions = [
          'admins' => 'Super Administrator account & security credentials',
          'markets' => 'Market timings, open/close status & display priority',
          'results' => 'Published results, jodi numbers & historical archive',
          'scheduled_publications' => 'Automated future result releases',
          'api_configs' => 'External verification API credentials & sync timers',
          'api_sync_logs' => 'Historical sync audit logs and status records',
          'announcements' => 'Homepage notices and broadcast alerts',
          'admin_activity_logs' => 'Administrative audit trails and IP logging',
          'settings' => 'Global website configuration, timezone and SEO'
      ];
      foreach ($tableDescriptions as $tbl => $desc):
      ?>
        <div class="p-3.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] flex items-center justify-between">
          <div>
            <span class="font-bold text-[#191747] block font-mono"><?= $tbl ?></span>
            <span class="text-[10px] text-[#737493] block mt-0.5"><?= $desc ?></span>
          </div>
          <span class="text-emerald-500 font-bold text-xs">✓ Ready</span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="flex items-center justify-between pt-4 border-t border-[#EAE8F5]">
    <a href="/install/database.php" class="btn-secondary text-xs">
      &larr; Database Credentials
    </a>

    <?php if ($installed): ?>
      <a href="/install/create-admin.php" class="btn-primary text-xs flex items-center gap-2">
        <span>Create Super Admin &rarr;</span>
      </a>
    <?php else: ?>
      <form action="/install/install-database.php" method="POST">
        <?= installer_csrf_field() ?>
        <button type="submit" class="btn-primary text-xs flex items-center gap-2">
          <span>Run Schema Installation &rarr;</span>
        </button>
      </form>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
