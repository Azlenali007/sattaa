<?php
/**
 * Result Card Component (for 4-card grid matching reference)
 */
$card = $card ?? [];
$openParts = format_digits($card['open_display'] ?? '');
$closeParts = format_digits($card['close_display'] ?? '');
$isLive = !empty($card['is_live']);
?>
<div class="bg-white rounded-2xl border border-[#EAE8F5] p-5 shadow-sm card-hover-effect flex flex-col justify-between" data-market-card="<?= e($card['market_id']) ?>">
  <!-- Card Header -->
  <div>
    <div class="flex items-center justify-between">
      <h3 class="font-extrabold text-base text-[#191747]"><?= e($card['market_name']) ?></h3>
      <?php if ($isLive): ?>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse-dot"></span>
          Live
        </span>
      <?php else: ?>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
          Upcoming
        </span>
      <?php endif; ?>
    </div>
    <p class="text-xs text-[#737493] mt-0.5 font-medium"><?= e($card['open_time']) ?> - <?= e($card['close_time']) ?></p>
  </div>

  <!-- Big Result Numbers Display -->
  <div class="my-5 flex items-center justify-center gap-4">
    <!-- Open Digits -->
    <div class="text-center">
      <div class="flex items-center gap-2">
        <div class="result-digit-box w-11 h-12 text-xl font-black text-[#191747]">
          <?= e($openParts[0] !== '-' ? $openParts[0] : '-') ?>
        </div>
        <div class="result-digit-box w-11 h-12 text-xl font-black text-[#191747]">
          <?= e($openParts[1] !== '' ? $openParts[1] : '-') ?>
        </div>
      </div>
      <span class="text-[11px] font-semibold text-[#737493] mt-1.5 block">Open</span>
    </div>

    <!-- Close Digits -->
    <div class="text-center">
      <div class="flex items-center gap-2">
        <div class="result-digit-box w-11 h-12 text-xl font-black text-[#191747]">
          <?= e($closeParts[0] !== '-' ? $closeParts[0] : '-') ?>
        </div>
        <div class="result-digit-box w-11 h-12 text-xl font-black text-[#191747]">
          <?= e($closeParts[1] !== '' ? $closeParts[1] : '-') ?>
        </div>
      </div>
      <span class="text-[11px] font-semibold text-[#737493] mt-1.5 block">Close</span>
    </div>
  </div>

  <!-- Card Footer Link -->
  <a href="/market.php?id=<?= e($card['market_id']) ?>" class="block text-center text-xs font-bold text-[#6425D9] hover:text-[#521ec2] transition mt-1">
    View Market Details &rarr;
  </a>
</div>
