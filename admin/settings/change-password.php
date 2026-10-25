<?php
/**
 * Admin: Change Password
 */
require_once __DIR__ . '/../includes/auth-check.php';

$pageTitle = 'Change Password';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    enforce_csrf();
    $currentPass = $_POST['current_password'] ?? '';
    $newPass = $_POST['new_password'] ?? '';
    $confirmPass = $_POST['confirm_password'] ?? '';

    $admin = Admin::authenticate($currentAdmin['username'], $currentPass);

    if (!$admin) {
        $error = 'Your current password was incorrect.';
    } elseif (strlen($newPass) < 8) {
        $error = 'New password must be at least 8 characters.';
    } elseif ($newPass !== $confirmPass) {
        $error = 'New password confirmation does not match.';
    } else {
        Admin::updatePassword($currentAdmin['id'], $newPass);
        Admin::logActivity($currentAdmin['id'], 'Change Password', 'Security', null, 'Super Admin password updated');
        set_flash('success', 'Your password has been changed successfully!');
        redirect('/admin/dashboard.php');
    }
}

require __DIR__ . '/../includes/head.php';
require __DIR__ . '/../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../includes/header.php'; ?>
  <?php require __DIR__ . '/../../includes/flash-message.php'; ?>

  <main class="p-6 sm:p-8 space-y-6 flex-1 max-w-xl">
    <div>
      <h1 class="text-2xl font-black text-[#191747]">Change Password</h1>
      <p class="text-xs text-[#737493] mt-0.5">Secure your Super Admin control panel credentials.</p>
    </div>

    <?php if ($error): ?>
      <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold"><?= e($error) ?></div>
    <?php endif; ?>

    <form action="/admin/settings/change-password.php" method="POST" class="bg-white rounded-2xl border border-[#EAE8F5] p-6 sm:p-8 shadow-xs space-y-4">
      <?= csrf_field() ?>

      <div>
        <label class="block text-xs font-bold text-[#191747] mb-1.5">Current Password *</label>
        <input type="password" name="current_password" required class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
      </div>

      <div>
        <label class="block text-xs font-bold text-[#191747] mb-1.5">New Password *</label>
        <input type="password" name="new_password" required minlength="8" class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
        <span class="text-[10px] text-[#737493] mt-1 block">Minimum 8 characters</span>
      </div>

      <div>
        <label class="block text-xs font-bold text-[#191747] mb-1.5">Confirm New Password *</label>
        <input type="password" name="confirm_password" required class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
      </div>

      <div class="pt-4 border-t border-[#EAE8F5] flex justify-end space-x-3">
        <a href="/admin/settings/index.php" class="px-4 py-2.5 rounded-xl border border-[#EAE8F5] text-xs font-bold text-[#737493] hover:bg-[#FAF9FF]">Cancel</a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#6425D9] hover:bg-[#521ec2] text-white text-xs font-bold shadow-xs transition">Update Password</button>
      </div>
    </form>
  </main>

  <?php require __DIR__ . '/../includes/footer.php'; ?>
</div>
