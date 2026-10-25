<?php
/**
 * Quick Links Sub-navbar Component
 */
?>
<div class="bg-white border-b border-[#EAE8F5] py-2 px-4 hidden md:block">
  <div class="max-w-[1440px] mx-auto flex items-center justify-between text-xs text-[#737493]">
    <div class="flex items-center space-x-6">
      <span class="flex items-center gap-1.5 font-medium text-emerald-600">
        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse-dot"></span>
        Live Market Updates Active
      </span>
      <span>Sync Frequency: Every 15 Mins</span>
    </div>
    <div class="flex items-center space-x-4">
      <span>Current Date: <strong><?= date('d M Y') ?></strong></span>
      <span>•</span>
      <span>Timezone: <strong><?= e(Setting::get('timezone', 'Asia/Kolkata')) ?></strong></span>
    </div>
  </div>
</div>
