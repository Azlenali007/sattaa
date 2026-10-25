<?php
/**
 * Admin: Website General Settings
 */
require_once __DIR__ . '/../includes/auth-check.php';

$pageTitle = 'General Settings';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    enforce_csrf();
    $siteName = trim($_POST['site_name'] ?? 'SATKA MATKA');
    $tagline = trim($_POST['site_tagline'] ?? 'Live Results & Charts');
    $contactEmail = trim($_POST['contact_email'] ?? 'support@satkamatka.com');
    $contactPhone = trim($_POST['contact_phone'] ?? '');
    $timezone = trim($_POST['timezone'] ?? 'Asia/Kolkata');
    $telegram = trim($_POST['telegram_channel'] ?? '');

    Setting::set('site_name', $siteName);
    Setting::set('site_tagline', $tagline);
    Setting::set('contact_email', $contactEmail, 'contact');
    Setting::set('contact_phone', $contactPhone, 'contact');
    Setting::set('timezone', $timezone);
    Setting::set('telegram_channel', $telegram, 'social');

    Admin::logActivity($currentAdmin['id'], 'Update Settings', 'Settings', null, 'Updated website configuration');
    set_flash('success', 'General settings saved successfully!');
    redirect('/admin/settings/index.php');
}

$siteName = Setting::get('site_name', 'SATKA MATKA');
$tagline = Setting::get('site_tagline', 'Live Results & Charts');
$email = Setting::get('contact_email', 'support@satkamatka.com');
$phone = Setting::get('contact_phone', '+91 98765 43210');
$tz = Setting::get('timezone', 'Asia/Kolkata');
$telegram = Setting::get('telegram_channel', 'https://t.me/satkamatka_live');

require __DIR__ . '/../includes/head.php';
require __DIR__ . '/../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../includes/header.php'; ?>
  <?php require __DIR__ . '/../../includes/flash-message.php'; ?>

  <main class="p-6 sm:p-8 space-y-6 flex-1 max-w-4xl">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-black text-[#191747]">Platform Settings</h1>
        <p class="text-xs text-[#737493] mt-0.5">Customize brand name, timezones, and public communication channels.</p>
      </div>

      <a href="/admin/settings/change-password.php" class="px-4 py-2 rounded-xl bg-[#F3EDFF] text-[#6425D9] text-xs font-bold hover:bg-[#e7d8ff] transition">
        Change Password &rarr;
      </a>
    </div>

    <form action="/admin/settings/index.php" method="POST" class="bg-white rounded-2xl border border-[#EAE8F5] p-6 sm:p-8 shadow-xs space-y-5">
      <?= csrf_field() ?>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Brand / Site Name *</label>
          <input type="text" name="site_name" value="<?= e($siteName) ?>" required class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
        </div>

        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Tagline / Subtitle *</label>
          <input type="text" name="site_tagline" value="<?= e($tagline) ?>" required class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Public Support Email *</label>
          <input type="email" name="contact_email" value="<?= e($email) ?>" required class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
        </div>

        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Support Phone</label>
          <input type="text" name="contact_phone" value="<?= e($phone) ?>" class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Timezone *</label>
          <select name="timezone" class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
            <option value="Asia/Kolkata" <?= $tz === 'Asia/Kolkata' ? 'selected' : '' ?>>Asia/Kolkata (IST +05:30)</option>
            <option value="UTC" <?= $tz === 'UTC' ? 'selected' : '' ?>>UTC</option>
            <option value="America/New_York" <?= $tz === 'America/New_York' ? 'selected' : '' ?>>America/New_York (EST)</option>
            <option value="Europe/London" <?= $tz === 'Europe/London' ? 'selected' : '' ?>>Europe/London (GMT)</option>
            <option value="Asia/Dubai" <?= $tz === 'Asia/Dubai' ? 'selected' : '' ?>>Asia/Dubai (+04:00)</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Telegram Channel Link</label>
          <input type="url" name="telegram_channel" value="<?= e($telegram) ?>" class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
        </div>
      </div>

      <div class="pt-4 border-t border-[#EAE8F5] flex justify-end">
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#6425D9] hover:bg-[#521ec2] text-white text-xs font-bold shadow-xs transition">Save Settings</button>
      </div>
    </form>
  </main>

  <?php require __DIR__ . '/../includes/footer.php'; ?>
</div>
