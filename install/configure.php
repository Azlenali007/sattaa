<?php
/**
 * Step 6: Website Configuration
 */
require_once __DIR__ . '/includes/installer-functions.php';
installer_init();
check_installer_guard();

if (empty($_SESSION['installer']['step_admin_passed']) || empty($_SESSION['installer']['db_config'])) {
    header('Location: /install/create-admin.php');
    exit;
}

$pageTitle = 'Step 6: Website Configuration';
$currentStep = 6;

$dbConfig = $_SESSION['installer']['db_config'];
$conn = test_database_connection($dbConfig);

if (!$conn['success'] || !$conn['pdo']) {
    header('Location: /install/database.php');
    exit;
}

// Auto-detect URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? 80) == 443 ? "https://" : "http://";
$detectedHost = $_SERVER['HTTP_HOST'] ?? 'localhost:3000';
$defaultUrl = $protocol . $detectedHost;

$siteName = $_POST['site_name'] ?? 'SATKA MATKA';
$siteTagline = $_POST['site_tagline'] ?? 'Live Results & Charts';
$siteUrl = $_POST['site_url'] ?? $defaultUrl;
$timezone = $_POST['timezone'] ?? 'Asia/Kolkata';
$metaDesc = $_POST['meta_description'] ?? 'Get the latest Satka Matka results, market charts and historical data all in one place. Fast, accurate and easy to use.';
$contactEmail = $_POST['contact_email'] ?? ($_SESSION['installer']['admin_email'] ?? 'support@satkamatka.com');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!installer_verify_csrf()) {
        $error = 'Security check failed. Please refresh and try again.';
    } else {
        $res = save_site_configuration($conn['pdo'], [
            'site_name' => $siteName,
            'site_tagline' => $siteTagline,
            'site_url' => $siteUrl,
            'timezone' => $timezone,
            'meta_description' => $metaDesc,
            'contact_email' => $contactEmail
        ], $dbConfig);

        if ($res['success']) {
            $_SESSION['installer']['step_config_passed'] = true;
            header('Location: /install/verify.php');
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
      <h1 class="text-2xl font-black text-[#191747]">Website Configuration</h1>
      <p class="text-xs text-[#737493] mt-0.5">Customize your platform name, URL, time zone, and primary contact email.</p>
    </div>
    <span class="text-xs font-bold text-[#6425D9] bg-[#F3EDFF] px-3 py-1 rounded-full">
      Step 6 of 8
    </span>
  </div>

  <?php if ($error): ?>
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs mb-6">
      <strong class="font-bold block mb-1">Configuration Error:</strong>
      <span><?= e($error) ?></span>
    </div>
  <?php endif; ?>

  <form action="/install/configure.php" method="POST" class="space-y-5">
    <?= installer_csrf_field() ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
      <div>
        <label class="block text-xs font-bold text-[#191747] mb-1.5">Platform Brand Name *</label>
        <input type="text" name="site_name" value="<?= e($siteName) ?>" required placeholder="SATKA MATKA" class="form-input">
      </div>

      <div>
        <label class="block text-xs font-bold text-[#191747] mb-1.5">Tagline / Subtitle *</label>
        <input type="text" name="site_tagline" value="<?= e($siteTagline) ?>" required placeholder="Live Results & Charts" class="form-input">
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
      <div>
        <label class="block text-xs font-bold text-[#191747] mb-1.5">Website Public URL *</label>
        <input type="url" name="site_url" value="<?= e($siteUrl) ?>" required placeholder="https://yourdomain.com" class="form-input">
        <span class="text-[10px] text-[#737493] mt-1 block">Full canonical URL with http:// or https:// (no trailing slash)</span>
      </div>

      <div>
        <label class="block text-xs font-bold text-[#191747] mb-1.5">Time Zone *</label>
        <select name="timezone" class="form-input">
          <option value="Asia/Kolkata" <?= ($timezone === 'Asia/Kolkata') ? 'selected' : '' ?>>Asia/Kolkata (IST +05:30) - Official Standard</option>
          <option value="UTC" <?= ($timezone === 'UTC') ? 'selected' : '' ?>>UTC</option>
          <option value="Asia/Dubai" <?= ($timezone === 'Asia/Dubai') ? 'selected' : '' ?>>Asia/Dubai (+04:00)</option>
          <option value="Europe/London" <?= ($timezone === 'Europe/London') ? 'selected' : '' ?>>Europe/London (GMT)</option>
          <option value="America/New_York" <?= ($timezone === 'America/New_York') ? 'selected' : '' ?>>America/New_York (EST)</option>
        </select>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
      <div>
        <label class="block text-xs font-bold text-[#191747] mb-1.5">Default Language</label>
        <input type="text" readonly value="English (en)" class="form-input bg-gray-100 cursor-not-allowed text-gray-500">
      </div>

      <div>
        <label class="block text-xs font-bold text-[#191747] mb-1.5">Public Support Email *</label>
        <input type="email" name="contact_email" value="<?= e($contactEmail) ?>" required class="form-input">
      </div>
    </div>

    <div>
      <label class="block text-xs font-bold text-[#191747] mb-1.5">SEO Meta Description</label>
      <textarea name="meta_description" rows="2" class="form-input"><?= e($metaDesc) ?></textarea>
    </div>

    <div class="flex items-center justify-between pt-4 border-t border-[#EAE8F5]">
      <a href="/install/create-admin.php" class="btn-secondary text-xs">
        &larr; Super Admin
      </a>

      <button type="submit" class="btn-primary text-xs flex items-center gap-2">
        <span>Save Configuration &rarr;</span>
      </button>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
