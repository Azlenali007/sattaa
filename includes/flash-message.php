<?php
/**
 * Flash Message Toast Notifications
 */
$flashes = get_flashes();
if (!empty($flashes)):
?>
  <div class="fixed top-24 right-5 z-50 space-y-2 max-w-sm w-full pointer-events-none">
    <?php foreach ($flashes as $f): 
      $isErr = $f['type'] === 'error';
    ?>
      <div class="flash-alert pointer-events-auto p-4 rounded-xl shadow-lg border flex items-center justify-between <?= $isErr ? 'bg-rose-50 border-rose-200 text-rose-800' : 'bg-emerald-50 border-emerald-200 text-emerald-800' ?>">
        <div class="flex items-center gap-3">
          <svg class="w-5 h-5 shrink-0 <?= $isErr ? 'text-rose-500' : 'text-emerald-500' ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <?php if ($isErr): ?>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            <?php else: ?>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            <?php endif; ?>
          </svg>
          <span class="text-xs font-semibold"><?= e($f['message']) ?></span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600 text-lg leading-none">&times;</button>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
