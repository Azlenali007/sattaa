<?php
/**
 * Admin Top Header Bar
 */
?>
<header class="h-20 bg-white border-b border-[#EAE8F5] px-6 flex items-center justify-between shrink-0 sticky top-0 z-30">
  <div class="flex items-center gap-4">
    <button class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100" onclick="document.querySelector('aside').classList.toggle('hidden')">
      <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
    <h2 class="font-extrabold text-base text-[#191747]"><?= e($pageTitle ?? 'Control Center') ?></h2>
  </div>

  <div class="flex items-center space-x-4 text-xs font-semibold">
    <a href="/index.php" target="_blank" class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-[#EAE8F5] bg-[#FAF9FF] text-[#6425D9] hover:bg-[#F3EDFF] transition">
      <span>View Public Site</span>
      <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
    </a>

    <div class="flex items-center gap-2">
      <span class="w-8 h-8 rounded-full bg-[#6425D9] text-white flex items-center justify-center font-bold text-xs">A</span>
      <span class="hidden md:inline font-bold text-[#191747]">Super Admin</span>
    </div>
  </div>
</header>
