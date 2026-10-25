<?php
/**
 * Installer Stepper Component
 * Renders active, completed, and pending steps
 */

$currentStep = $currentStep ?? 1;

$steps = [
    1 => ['name' => 'Welcome', 'icon' => '1'],
    2 => ['name' => 'Requirements', 'icon' => '2'],
    3 => ['name' => 'Database', 'icon' => '3'],
    4 => ['name' => 'Install Schema', 'icon' => '4'],
    5 => ['name' => 'Super Admin', 'icon' => '5'],
    6 => ['name' => 'Settings', 'icon' => '6'],
    7 => ['name' => 'Verification', 'icon' => '7'],
    8 => ['name' => 'Complete', 'icon' => '8'],
];
?>
<div class="mb-8">
  <!-- Desktop Stepper -->
  <div class="hidden md:flex items-center justify-between max-w-4xl mx-auto px-4">
    <?php foreach ($steps as $num => $step): 
      $isDone = $num < $currentStep;
      $isActive = $num === $currentStep;
    ?>
      <div class="flex items-center flex-1 last:flex-none">
        <div class="flex flex-col items-center">
          <div class="step-circle <?= $isDone ? 'completed' : ($isActive ? 'active' : 'pending') ?>">
            <?php if ($isDone): ?>
              <svg class="w-4 h-4 fill-none stroke-current" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
              </svg>
            <?php else: ?>
              <?= $num ?>
            <?php endif; ?>
          </div>
          <span class="text-[10px] font-bold tracking-tight mt-1.5 <?= $isActive ? 'text-[#6425D9]' : ($isDone ? 'text-emerald-600' : 'text-[#737493]') ?>">
            <?= e($step['name']) ?>
          </span>
        </div>

        <?php if ($num < count($steps)): ?>
          <div class="step-connector <?= $isDone ? 'completed' : '' ?> mx-2 -mt-4"></div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Mobile Stepper Indicator -->
  <div class="flex md:hidden items-center justify-between bg-white px-4 py-3 rounded-2xl border border-[#EAE8F5]">
    <div class="flex items-center gap-2">
      <span class="w-7 h-7 rounded-full bg-[#6425D9] text-white flex items-center justify-center text-xs font-black">
        <?= $currentStep ?>
      </span>
      <div>
        <span class="block text-xs font-bold text-[#191747]">Step <?= $currentStep ?> of <?= count($steps) ?></span>
        <span class="block text-[11px] text-[#737493]"><?= e($steps[$currentStep]['name']) ?></span>
      </div>
    </div>
    <span class="text-xs font-bold text-[#6425D9]"><?= round(($currentStep / count($steps)) * 100) ?>% Complete</span>
  </div>
</div>
