<?php
/**
 * Step 3: MySQL Database Configuration & Connection Test
 * Exclusively supports MySQL 8+ / MariaDB via PDO (pdo_mysql)
 */
require_once __DIR__ . '/includes/installer-functions.php';
installer_init();
check_installer_guard();

// Enforce step sequence
if (empty($_SESSION['installer']['step_requirements_passed'])) {
    header('Location: /install/requirements.php');
    exit;
}

$pageTitle = 'Step 3: MySQL Database Configuration';
$currentStep = 3;

$error = '';

// Handle AJAX connection test
if (!empty($_POST['ajax_test'])) {
    header('Content-Type: application/json; charset=utf-8');
    if (!installer_verify_csrf()) {
        echo json_encode(['success' => false, 'message' => 'CSRF verification failed. Please refresh the page.']);
        exit;
    }

    $dbConfig = [
        'driver' => 'mysql',
        'host' => trim($_POST['host'] ?? '127.0.0.1'),
        'port' => (int)($_POST['port'] ?? 3306),
        'database' => trim($_POST['database'] ?? ''),
        'username' => trim($_POST['username'] ?? ''),
        'password' => (string)($_POST['password'] ?? '')
    ];

    $res = test_database_connection($dbConfig);
    echo json_encode([
        'success' => $res['success'],
        'message' => $res['message'],
        'details' => $res['details'] ?? ''
    ]);
    exit;
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST['ajax_test'])) {
    if (!installer_verify_csrf()) {
        $error = 'Security check failed: Invalid CSRF token. Please try again.';
    } else {
        $dbConfig = [
            'driver' => 'mysql',
            'host' => trim($_POST['host'] ?? '127.0.0.1'),
            'port' => (int)($_POST['port'] ?? 3306),
            'database' => trim($_POST['database'] ?? ''),
            'username' => trim($_POST['username'] ?? ''),
            'password' => (string)($_POST['password'] ?? '')
        ];

        $res = test_database_connection($dbConfig);
        if ($res['success']) {
            $_SESSION['installer']['db_config'] = $dbConfig;
            $_SESSION['installer']['step_db_passed'] = true;
            header('Location: /install/install-database.php');
            exit;
        } else {
            $error = $res['message'] . (!empty($res['details']) ? ' ' . $res['details'] : '');
        }
    }
}

$savedConfig = $_SESSION['installer']['db_config'] ?? [
    'driver' => 'mysql',
    'host' => getenv('DB_HOST') ?: '127.0.0.1',
    'port' => getenv('DB_PORT') ?: 3306,
    'database' => getenv('DB_DATABASE') ?: 'satkamatka',
    'username' => getenv('DB_USERNAME') !== false ? getenv('DB_USERNAME') : 'root',
    'password' => getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : ''
];

require_once __DIR__ . '/includes/header.php';
?>

<div class="installer-card p-6 sm:p-10">
  <div class="flex items-center justify-between mb-6 pb-6 border-b border-[#EAE8F5]">
    <div>
      <h1 class="text-2xl font-black text-[#191747]">MySQL Database Configuration</h1>
      <p class="text-xs text-[#737493] mt-0.5">Enter your MySQL database credentials and verify the live connection.</p>
    </div>
    <span class="text-xs font-bold text-[#6425D9] bg-[#F3EDFF] px-3 py-1 rounded-full">
      Step 3 of 8
    </span>
  </div>

  <?php if ($error): ?>
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs mb-6 flex items-start gap-3">
      <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <div>
        <strong class="font-bold block">MySQL Connection Error:</strong>
        <span><?= e($error) ?></span>
      </div>
    </div>
  <?php endif; ?>

  <div id="test-connection-result" class="mb-6"></div>

  <form id="db-config-form" action="/install/database.php" method="POST" class="space-y-5">
    <?= installer_csrf_field() ?>
    <input type="hidden" name="driver" value="mysql">

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
      <div>
        <label class="block text-xs font-bold text-[#191747] mb-1.5">Database Host *</label>
        <input type="text" name="host" value="<?= e($savedConfig['host']) ?>" required placeholder="127.0.0.1 or localhost" class="form-input">
        <span class="text-[10px] text-[#737493] mt-1 block">Usually 127.0.0.1 or localhost on most hosting providers</span>
      </div>

      <div>
        <label class="block text-xs font-bold text-[#191747] mb-1.5">Port *</label>
        <input type="number" name="port" value="<?= e($savedConfig['port']) ?>" required placeholder="3306" class="form-input">
        <span class="text-[10px] text-[#737493] mt-1 block">Standard MySQL port is 3306</span>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
      <div>
        <label class="block text-xs font-bold text-[#191747] mb-1.5">Database Name *</label>
        <input type="text" name="database" value="<?= e($savedConfig['database']) ?>" required placeholder="satkamatka" class="form-input">
        <span class="text-[10px] text-[#737493] mt-1 block">Created via cPanel or hosting database manager</span>
      </div>

      <div>
        <label class="block text-xs font-bold text-[#191747] mb-1.5">Database Username *</label>
        <input type="text" name="username" value="<?= e($savedConfig['username']) ?>" required placeholder="root" class="form-input">
      </div>

      <div>
        <label class="block text-xs font-bold text-[#191747] mb-1.5">Database Password</label>
        <div class="relative">
          <input type="password" id="db-password" name="password" value="<?= e($savedConfig['password']) ?>" placeholder="Leave blank if none" class="form-input pr-14">
          <button type="button" onclick="togglePasswordVisibility('db-password', this)" class="absolute right-3 top-3 text-[11px] font-bold text-[#6425D9] hover:underline">
            Show
          </button>
        </div>
      </div>
    </div>

    <div class="p-4 rounded-2xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs text-[#737493] flex flex-col sm:flex-row items-center justify-between gap-3">
      <span>Verify your MySQL connection parameters prior to saving and installing schema.</span>
      <button 
        type="button" 
        id="btn-test-db"
        onclick="testDatabaseConnectionAsync('db-config-form', 'test-connection-result', 'btn-test-db')" 
        class="py-2.5 px-4 rounded-xl border border-[#EAE8F5] bg-white text-[#6425D9] font-bold hover:bg-[#F3EDFF] transition shrink-0"
      >
        Test Database Connection
      </button>
    </div>

    <div class="flex items-center justify-between pt-4 border-t border-[#EAE8F5]">
      <a href="/install/requirements.php" class="btn-secondary text-xs">
        &larr; Previous Step
      </a>

      <button type="submit" id="btn-submit-db" class="btn-primary text-xs flex items-center gap-2">
        <span>Save &amp; Continue &rarr;</span>
      </button>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
