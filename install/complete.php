<?php
/**
 * Step 8: Installation Completed Screen
 */
require_once __DIR__ . '/includes/installer-functions.php';
installer_init();

// If not locked and not completed in session, redirect to index
if (!is_installed() && empty($_SESSION['installer']['completed'])) {
    header('Location: /install/index.php');
    exit;
}

$pageTitle = 'Step 8: Installation Complete';
$currentStep = 8;

// Clear installer session variables
$adminUser = $_SESSION['installer']['admin_username'] ?? 'admin';
unset($_SESSION['installer']);

// Render header without the guard redirecting
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= e($pageTitle) ?> | SATKA MATKA Installation Wizard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: {
              primary: '#6425D9',
              deep: '#35156F',
              light: '#F3EDFF',
              main: '#191747',
              muted: '#737493',
              border: '#EAE8F5',
              green: '#16A779',
              soft: '#FAF9FF'
            }
          },
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
          }
        }
      }
    }
  </script>
  <link rel="stylesheet" href="/install/assets/installer.css">
</head>
<body class="bg-[#FAF9FF] text-[#191747] min-h-screen flex flex-col antialiased font-sans">

  <header class="bg-white border-b border-[#EAE8F5] py-4 px-6 sm:px-10 sticky top-0 z-30 shadow-xs">
    <div class="max-w-5xl mx-auto flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-[#6425D9] flex items-center justify-center text-white shadow-xs">
          <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
            <path d="M2.5 19h19v2.5H2.5V19zm1.75-2l1.9-9.5 4.35 4.5L12 4.5l1.5 7.5 4.35-4.5 1.9 9.5H4.25zm7.75-10a1.25 1.25 0 100-2.5 1.25 1.25 0 000 2.5zm-6 2.5A1.25 1.25 0 106 7a1.25 1.25 0 000 2.5zm12 0a1.25 1.25 0 100-2.5 1.25 1.25 0 000 2.5z"/>
          </svg>
        </div>
        <div>
          <span class="block text-base font-black text-[#191747] leading-none">SATKA MATKA</span>
          <span class="block text-[11px] font-bold text-[#6425D9] mt-0.5 tracking-wide uppercase">Web Installation Wizard</span>
        </div>
      </div>

      <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">
        ✓ Installation Active &amp; Locked
      </span>
    </div>
  </header>

  <main class="max-w-4xl mx-auto px-4 sm:px-6 py-8 flex-1 w-full">
    <?php require __DIR__ . '/includes/progress.php'; ?>

    <div class="installer-card p-6 sm:p-12 text-center">
      <div class="w-20 h-20 rounded-3xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center mx-auto mb-6 shadow-sm">
        <svg class="w-10 h-10 fill-none stroke-current" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
        </svg>
      </div>

      <h1 class="text-3xl font-black text-[#191747]">Installation Successfully Completed!</h1>
      <p class="text-xs sm:text-sm text-[#737493] mt-2 max-w-lg mx-auto leading-relaxed">
        Your Satka Matka platform is now fully deployed, configured, and protected by the installation lock.
      </p>

      <!-- Launch Buttons -->
      <div class="flex flex-col sm:flex-row items-center justify-center gap-4 my-8">
        <a href="/index.php" class="w-full sm:w-auto py-3.5 px-6 rounded-xl bg-white border border-[#EAE8F5] text-[#191747] font-bold text-xs hover:bg-[#FAF9FF] transition flex items-center justify-center gap-2 shadow-xs">
          <span>Open Public Website</span>
          <svg class="w-4 h-4 text-[#6425D9]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
          </svg>
        </a>

        <a href="/admin/login.php" class="w-full sm:w-auto py-3.5 px-8 rounded-xl bg-[#6425D9] hover:bg-[#521ec2] text-white font-bold text-xs transition flex items-center justify-center gap-2 shadow-md">
          <span>Open Admin Login</span>
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
          </svg>
        </a>
      </div>

      <!-- Post Installation & Security Guidance -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left text-xs pt-8 border-t border-[#EAE8F5]">
        <div class="p-4 rounded-2xl bg-[#FAF9FF] border border-[#EAE8F5]">
          <h3 class="font-bold text-[#191747] flex items-center gap-2 mb-2">
            <svg class="w-4 h-4 text-[#6425D9]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            <span>Security Recommendations</span>
          </h3>
          <p class="text-[#737493] text-[11px] leading-relaxed mb-2">
            The installation lock file <code class="font-mono text-[#6425D9]">storage/installed.lock</code> has been activated.
          </p>
          <p class="text-[#737493] text-[11px] leading-relaxed">
            For maximum production security on shared hosting or VPS, you may safely delete the entire <code class="font-mono text-[#6425D9]">install/</code> folder from your server.
          </p>
        </div>

        <div class="p-4 rounded-2xl bg-[#FAF9FF] border border-[#EAE8F5]">
          <h3 class="font-bold text-[#191747] flex items-center gap-2 mb-2">
            <svg class="w-4 h-4 text-[#6425D9]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Automated Cron Jobs</span>
          </h3>
          <p class="text-[#737493] text-[11px] leading-relaxed mb-2">
            To enable automatic API result synchronization every 15 minutes, add this cron job in cPanel:
          </p>
          <pre class="bg-white p-2 rounded-lg border border-[#EAE8F5] text-[10px] font-mono text-[#191747] overflow-x-auto">*/15 * * * * php cron/sync-results.php</pre>
        </div>
      </div>

    </div>
  </main>

  <footer class="mt-auto py-6 border-t border-[#EAE8F5] bg-white text-center text-xs text-[#737493]">
    <div class="max-w-4xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
      <div>SATKA MATKA Automated Web Installer</div>
      <div>&copy; <?= date('Y') ?> All rights reserved</div>
    </div>
  </footer>

</body>
</html>
