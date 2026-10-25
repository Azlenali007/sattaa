<?php
/**
 * Admin Panel Sidebar
 */
$currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
?>
<aside class="w-64 bg-white border-r border-[#EAE8F5] shrink-0 min-h-screen flex flex-col justify-between hidden lg:flex">
  <div>
    <!-- Logo -->
    <div class="h-20 flex items-center px-6 border-b border-[#EAE8F5]">
      <a href="/admin/dashboard.php" class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-[#6425D9] flex items-center justify-center text-white">
          <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
            <path d="M2.5 19h19v2.5H2.5V19zm1.75-2l1.9-9.5 4.35 4.5L12 4.5l1.5 7.5 4.35-4.5 1.9 9.5H4.25zm7.75-10a1.25 1.25 0 100-2.5 1.25 1.25 0 000 2.5zm-6 2.5A1.25 1.25 0 106 7a1.25 1.25 0 000 2.5zm12 0a1.25 1.25 0 100-2.5 1.25 1.25 0 000 2.5z"/>
          </svg>
        </div>
        <div>
          <span class="block text-sm font-black text-[#191747]">SATKA MATKA</span>
          <span class="block text-[10px] font-bold text-[#6425D9] uppercase tracking-wider">Super Admin</span>
        </div>
      </a>
    </div>

    <!-- Navigation Links -->
    <nav class="p-4 space-y-1 text-xs font-semibold">
      <a href="/admin/dashboard.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= str_contains($currentUri, 'dashboard') ? 'bg-[#F3EDFF] text-[#6425D9]' : 'text-[#737493] hover:bg-[#FAF9FF] hover:text-[#191747]' ?>">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        <span>Dashboard</span>
      </a>

      <a href="/admin/markets/index.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= str_contains($currentUri, 'markets') ? 'bg-[#F3EDFF] text-[#6425D9]' : 'text-[#737493] hover:bg-[#FAF9FF] hover:text-[#191747]' ?>">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        <span>Markets</span>
      </a>

      <a href="/admin/results/index.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= str_contains($currentUri, 'results') ? 'bg-[#F3EDFF] text-[#6425D9]' : 'text-[#737493] hover:bg-[#FAF9FF] hover:text-[#191747]' ?>">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <span>Results</span>
      </a>

      <a href="/admin/scheduled/index.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= str_contains($currentUri, 'scheduled') ? 'bg-[#F3EDFF] text-[#6425D9]' : 'text-[#737493] hover:bg-[#FAF9FF] hover:text-[#191747]' ?>">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>Scheduled Publish</span>
      </a>

      <a href="/admin/charts/index.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= str_contains($currentUri, 'charts') ? 'bg-[#F3EDFF] text-[#6425D9]' : 'text-[#737493] hover:bg-[#FAF9FF] hover:text-[#191747]' ?>">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
        <span>Charts &amp; History</span>
      </a>

      <a href="/admin/api-settings/index.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= str_contains($currentUri, 'api-settings') ? 'bg-[#F3EDFF] text-[#6425D9]' : 'text-[#737493] hover:bg-[#FAF9FF] hover:text-[#191747]' ?>">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        <span>API Sync Settings</span>
      </a>

      <a href="/admin/homepage/index.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= str_contains($currentUri, 'homepage') ? 'bg-[#F3EDFF] text-[#6425D9]' : 'text-[#737493] hover:bg-[#FAF9FF] hover:text-[#191747]' ?>">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
        <span>Homepage Notices</span>
      </a>

      <a href="/admin/seo/index.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= str_contains($currentUri, 'seo') ? 'bg-[#F3EDFF] text-[#6425D9]' : 'text-[#737493] hover:bg-[#FAF9FF] hover:text-[#191747]' ?>">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <span>SEO &amp; Sitemap</span>
      </a>

      <a href="/admin/settings/index.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= str_contains($currentUri, 'settings') ? 'bg-[#F3EDFF] text-[#6425D9]' : 'text-[#737493] hover:bg-[#FAF9FF] hover:text-[#191747]' ?>">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        <span>Settings</span>
      </a>

      <a href="/admin/logs/index.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= str_contains($currentUri, 'logs') ? 'bg-[#F3EDFF] text-[#6425D9]' : 'text-[#737493] hover:bg-[#FAF9FF] hover:text-[#191747]' ?>">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        <span>Activity Logs</span>
      </a>

      <a href="/admin/backups/index.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= str_contains($currentUri, 'backups') ? 'bg-[#F3EDFF] text-[#6425D9]' : 'text-[#737493] hover:bg-[#FAF9FF] hover:text-[#191747]' ?>">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
        <span>Database Backup</span>
      </a>
    </nav>
  </div>

  <!-- Bottom Admin Section -->
  <div class="p-4 border-t border-[#EAE8F5]">
    <div class="flex items-center justify-between p-3 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] mb-2">
      <div>
        <span class="block text-xs font-bold text-[#191747]">admin</span>
        <span class="block text-[10px] text-[#737493]">Single Super Admin</span>
      </div>
      <a href="/admin/logout.php" class="text-xs font-bold text-rose-600 hover:text-rose-800" title="Logout">
        Logout
      </a>
    </div>
    <a href="/index.php" target="_blank" class="block text-center text-xs font-semibold text-[#6425D9] hover:underline pt-1">
      &larr; View Public Website
    </a>
  </div>
</aside>
