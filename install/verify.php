<?php
/**
 * Step 7: Final Automated Verification & Installer Lock
 */
require_once __DIR__ . '/includes/installer-functions.php';
installer_init();
check_installer_guard();

if (empty($_SESSION['installer']['step_config_passed']) || empty($_SESSION['installer']['db_config'])) {
    header('Location: /install/configure.php');
    exit;
}

$pageTitle = 'Step 7: Installation Verification';
$currentStep = 7;

$dbConfig = $_SESSION['installer']['db_config'];
$conn = test_database_connection($dbConfig);

if (!$conn['success'] || !$conn['pdo']) {
    header('Location: /install/database.php');
    exit;
}

$audit = verify_installation_state($conn['pdo']);
$canLock = $audit['passed'];

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!installer_verify_csrf()) {
        $error = 'Security check failed. Please refresh and try again.';
    } elseif (!$canLock) {
        $error = 'Cannot complete installation: some verification checks have not passed.';
    } else {
        $locked = lock_installer([
            'admin_username' => $_SESSION['installer']['admin_username'] ?? 'admin',
            'driver' => $dbConfig['driver'] ?? 'mysql',
            'host' => $dbConfig['host'] ?? '127.0.0.1',
            'database' => $dbConfig['database'] ?? ''
        ]);

        if ($locked) {
            $_SESSION['installer']['completed'] = true;
            header('Location: /install/complete.php');
            exit;
        } else {
            $error = 'Failed to write installation lock file to storage/installed.lock. Please check permissions.';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="installer-card p-6 sm:p-10">
  <div class="flex items-center justify-between mb-6 pb-6 border-b border-[#EAE8F5]">
    <div>
      <h1 class="text-2xl font-black text-[#191747]">Installation Verification</h1>
      <p class="text-xs text-[#737493] mt-0.5">Automated diagnostic verification of database schema, administrator account, and environment.</p>
    </div>
    <span class="text-xs font-bold text-[#6425D9] bg-[#F3EDFF] px-3 py-1 rounded-full">
      Step 7 of 8
    </span>
  </div>

  <?php if ($error): ?>
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs mb-6">
      <strong class="font-bold block mb-1">Verification Alert:</strong>
      <span><?= e($error) ?></span>
    </div>
  <?php endif; ?>

  <div class="space-y-3 mb-8">
    <?php foreach ($audit['checks'] as $c): ?>
      <div class="p-4 rounded-2xl bg-[#FAF9FF] border border-[#EAE8F5] flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-xl <?= $c['passed'] ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-rose-50 text-rose-600 border border-rose-200' ?> flex items-center justify-center font-bold text-xs shrink-0">
            <?= $c['passed'] ? '✓' : '✕' ?>
          </div>
          <div>
            <span class="font-bold text-xs text-[#191747] block"><?= e($c['name']) ?></span>
            <span class="text-[11px] text-[#737493] block mt-0.5"><?= e($c['message']) ?></span>
          </div>
        </div>
        <span class="text-xs font-extrabold <?= $c['passed'] ? 'text-emerald-600' : 'text-rose-600' ?>">
          <?= $c['passed'] ? 'Passed' : 'Failed' ?>
        </span>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="p-4 rounded-2xl bg-purple-50 border border-purple-200 text-[#191747] text-xs mb-6 flex items-start gap-3">
    <svg class="w-5 h-5 text-[#6425D9] shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
    </svg>
    <div>
      <strong class="font-bold block">Security Lock Notice:</strong>
      <span>Clicking "Lock &amp; Complete Installation" will create the permanent lock file <code class="bg-white px-1 py-0.5 rounded text-[#6425D9]">storage/installed.lock</code>, securing your server against unauthorized reinstallation.</span>
    </div>
  </div>

  <div class="flex items-center justify-between pt-4 border-t border-[#EAE8F5]">
    <a href="/install/configure.php" class="btn-secondary text-xs">
      &larr; Website Settings
    </a>

    <?php if ($canLock): ?>
      <form action="/install/verify.php" method="POST">
        <?= installer_csrf_field() ?>
        <button type="submit" class="btn-primary text-xs flex items-center gap-2">
          <span>Lock &amp; Complete Installation &rarr;</span>
        </button>
      </form>
    <?php else: ?>
      <button disabled class="btn-primary opacity-50 cursor-not-allowed text-xs">
        Fix Failing Checks to Proceed
      </button>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
