<?php
/**
 * Popular Market Card Component
 */
$m = $marketCard ?? [];
?>
<div class="bg-white rounded-2xl border border-[#EAE8F5] p-5 text-center shadow-sm card-hover-effect flex flex-col items-center justify-between">
  <!-- Icon -->
  <div class="w-12 h-12 rounded-xl bg-[#F3EDFF] text-[#6425D9] flex items-center justify-center mb-3">
    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
      <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
    </svg>
  </div>

  <!-- Name & Schedule -->
  <div class="mb-4">
    <h3 class="font-extrabold text-sm sm:text-base text-[#191747]"><?= e($m['name']) ?></h3>
    <p class="text-[11px] text-[#737493] font-medium mt-0.5"><?= e($m['open_time']) ?> - <?= e($m['close_time']) ?></p>
  </div>

  <!-- View Chart Button -->
  <a 
    href="/chart.php?id=<?= e($m['id']) ?>" 
    class="w-full py-2 px-3 rounded-xl bg-[#6425D9] hover:bg-[#521ec2] text-white text-xs font-bold flex items-center justify-center gap-1.5 transition shadow-xs"
  >
    <span>View Chart</span>
    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
    </svg>
  </a>
</div>
