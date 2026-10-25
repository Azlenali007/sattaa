<?php
/**
 * Step 1: Welcome & Overview
 */
$pageTitle = 'Step 1: Welcome';
$currentStep = 1;

require_once __DIR__ . '/includes/header.php';
?>

<div class="installer-card p-6 sm:p-10">
  <div class="flex items-center gap-4 mb-6 pb-6 border-b border-[#EAE8F5]">
    <div class="w-14 h-14 rounded-2xl bg-[#F3EDFF] text-[#6425D9] flex items-center justify-center shrink-0">
      <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24">
        <path d="M2.5 19h19v2.5H2.5V19zm1.75-2l1.9-9.5 4.35 4.5L12 4.5l1.5 7.5 4.35-4.5 1.9 9.5H4.25zm7.75-10a1.25 1.25 0 100-2.5 1.25 1.25 0 000 2.5zm-6 2.5A1.25 1.25 0 106 7a1.25 1.25 0 000 2.5zm12 0a1.25 1.25 0 100-2.5 1.25 1.25 0 000 2.5z"/>
      </svg>
    </div>
    <div>
      <h1 class="text-2xl sm:text-3xl font-black text-[#191747]">Welcome to Satka Matka Setup</h1>
      <p class="text-xs sm:text-sm text-[#737493] mt-0.5">Automated web installation and deployment wizard.</p>
    </div>
  </div>

  <div class="prose text-xs text-[#737493] space-y-4 leading-relaxed mb-8">
    <p>
      This wizard will guide you through installing and configuring the <strong>SATKA MATKA Live Results &amp; Historical Charts</strong> application on your server.
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 my-6">
      <div class="p-4 rounded-2xl bg-[#FAF9FF] border border-[#EAE8F5]">
        <div class="flex items-center gap-2 text-xs font-bold text-[#191747] mb-1">
          <span class="w-2 h-2 rounded-full bg-[#6425D9]"></span>
          <span>1. Server Audit</span>
        </div>
        <p class="text-[11px] text-[#737493]">Verifies PHP version (8.0+), PDO MySQL, JSON, Sessions, and directory write permissions.</p>
      </div>

      <div class="p-4 rounded-2xl bg-[#FAF9FF] border border-[#EAE8F5]">
        <div class="flex items-center gap-2 text-xs font-bold text-[#191747] mb-1">
          <span class="w-2 h-2 rounded-full bg-[#6425D9]"></span>
          <span>2. Database Setup</span>
        </div>
        <p class="text-[11px] text-[#737493]">Tests MySQL connection credentials and safely generates required database tables and baseline data.</p>
      </div>

      <div class="p-4 rounded-2xl bg-[#FAF9FF] border border-[#EAE8F5]">
        <div class="flex items-center gap-2 text-xs font-bold text-[#191747] mb-1">
          <span class="w-2 h-2 rounded-full bg-[#6425D9]"></span>
          <span>3. Super Admin Account</span>
        </div>
        <p class="text-[11px] text-[#737493]">Creates your custom administrator credentials with secure bcrypt password hashing.</p>
      </div>

      <div class="p-4 rounded-2xl bg-[#FAF9FF] border border-[#EAE8F5]">
        <div class="flex items-center gap-2 text-xs font-bold text-[#191747] mb-1">
          <span class="w-2 h-2 rounded-full bg-[#6425D9]"></span>
          <span>4. Configuration &amp; Lock</span>
        </div>
        <p class="text-[11px] text-[#737493]">Configures site settings, generates the environment configuration, and locks the installer.</p>
      </div>
    </div>

    <div class="p-4 rounded-2xl bg-purple-50 border border-purple-200 text-[#191747] text-xs flex items-start gap-3">
      <svg class="w-5 h-5 text-[#6425D9] shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <div>
        <strong class="font-bold block">Before you proceed:</strong>
        <span>Ensure you have your MySQL database details (host, database name, username, and password) ready from your hosting control panel.</span>
      </div>
    </div>
  </div>

  <div class="flex items-center justify-end pt-4 border-t border-[#EAE8F5]">
    <a href="/install/requirements.php" class="btn-primary text-xs font-bold flex items-center gap-2">
      <span>Check Requirements &rarr;</span>
    </a>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
