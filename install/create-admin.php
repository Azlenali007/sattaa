<?php
/**
 * Step 5: First Super Administrator Account Creation
 */
require_once __DIR__ . '/includes/installer-functions.php';
installer_init();
check_installer_guard();

if (empty($_SESSION['installer']['step_schema_passed']) || empty($_SESSION['installer']['db_config'])) {
    header('Location: /install/install-database.php');
    exit;
}

$pageTitle = 'Step 5: Create Super Administrator';
$currentStep = 5;

$dbConfig = $_SESSION['installer']['db_config'];
$conn = test_database_connection($dbConfig);

if (!$conn['success'] || !$conn['pdo']) {
    header('Location: /install/database.php');
    exit;
}

$error = '';
$fullName = $_POST['full_name'] ?? 'Super Administrator';
$username = $_POST['username'] ?? 'admin';
$email = $_POST['email'] ?? 'admin@satkamatka.com';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!installer_verify_csrf()) {
        $error = 'Security check failed. Please refresh and try again.';
    } else {
        $res = create_super_admin_account($conn['pdo'], [
            'full_name' => $_POST['full_name'] ?? '',
            'username' => $_POST['username'] ?? '',
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? ''
        ]);

        if ($res['success']) {
            $_SESSION['installer']['step_admin_passed'] = true;
            $_SESSION['installer']['admin_username'] = $res['username'];
            $_SESSION['installer']['admin_email'] = $res['email'];
            header('Location: /install/configure.php');
            exit;
        } else {
            $error = $res['message'];
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="installer-card p-6 sm:p-10">
  <div class="flex items-center justify-between mb-6 pb-6 border-b border-[#EAE8F5]">
    <div>
      <h1 class="text-2xl font-black text-[#191747]">Create Super Administrator</h1>
      <p class="text-xs text-[#737493] mt-0.5">Setup the initial master administrative account for platform management.</p>
    </div>
    <span class="text-xs font-bold text-[#6425D9] bg-[#F3EDFF] px-3 py-1 rounded-full">
      Step 5 of 8
    </span>
  </div>

  <?php if ($error): ?>
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs mb-6 flex items-start gap-3">
      <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <div>
        <strong class="font-bold block">Validation Error:</strong>
        <span><?= e($error) ?></span>
      </div>
    </div>
  <?php endif; ?>

  <div class="p-4 rounded-2xl bg-purple-50 border border-purple-200 text-[#191747] text-xs mb-6">
    <strong>Admin Rules:</strong> Exactly ONE Super Admin account controls the platform. No public registration is enabled. Keep these credentials in a secure password manager.
  </div>

  <form action="/install/create-admin.php" method="POST" class="space-y-5">
    <?= installer_csrf_field() ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
      <div>
        <label class="block text-xs font-bold text-[#191747] mb-1.5">Full Name</label>
        <input type="text" name="full_name" value="<?= e($fullName) ?>" placeholder="Super Administrator" class="form-input">
      </div>

      <div>
        <label class="block text-xs font-bold text-[#191747] mb-1.5">Username *</label>
        <input type="text" name="username" value="<?= e($username) ?>" required placeholder="admin" autocomplete="username" class="form-input">
        <span class="text-[10px] text-[#737493] mt-1 block">Letters, numbers, underscores (3-30 chars)</span>
      </div>
    </div>

    <div>
      <label class="block text-xs font-bold text-[#191747] mb-1.5">Administrator Email Address *</label>
      <input type="email" name="email" value="<?= e($email) ?>" required placeholder="admin@satkamatka.com" class="form-input">
      <span class="text-[10px] text-[#737493] mt-1 block">Used for critical security alerts and password recovery</span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-2">
      <div>
        <label class="block text-xs font-bold text-[#191747] mb-1.5">Password *</label>
        <div class="relative">
          <input type="password" id="admin-pass" name="password" required minlength="8" placeholder="Minimum 8 characters" autocomplete="new-password" class="form-input pr-14">
          <button type="button" onclick="togglePasswordVisibility('admin-pass', this)" class="absolute right-3 top-3 text-[11px] font-bold text-[#6425D9] hover:underline">
            Show
          </button>
        </div>
        <!-- Password Strength Meter -->
        <div class="mt-2">
          <div class="w-full bg-[#EAE8F5] rounded-full h-1.5 overflow-hidden">
            <div id="pass-strength-bar" class="h-1.5 rounded-full bg-rose-500 w-0 transition-all duration-300"></div>
          </div>
          <div class="flex justify-between items-center text-[10px] text-[#737493] mt-1">
            <span>Strength: <strong id="pass-strength-text" class="text-[#191747]">None</strong></span>
            <span>Min 8 characters</span>
          </div>
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold text-[#191747] mb-1.5">Confirm Password *</label>
        <div class="relative">
          <input type="password" id="admin-pass-confirm" name="confirm_password" required minlength="8" placeholder="Re-enter your password" autocomplete="new-password" class="form-input pr-14">
          <button type="button" onclick="togglePasswordVisibility('admin-pass-confirm', this)" class="absolute right-3 top-3 text-[11px] font-bold text-[#6425D9] hover:underline">
            Show
          </button>
        </div>
      </div>
    </div>

    <div class="flex items-center justify-between pt-6 border-t border-[#EAE8F5]">
      <a href="/install/install-database.php" class="btn-secondary text-xs">
        &larr; Database Schema
      </a>

      <button type="submit" class="btn-primary text-xs flex items-center gap-2">
        <span>Save Super Admin &rarr;</span>
      </button>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  updatePasswordStrengthMeter('admin-pass', 'pass-strength-bar', 'pass-strength-text');
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
