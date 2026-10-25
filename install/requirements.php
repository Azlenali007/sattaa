<?php
/**
 * Step 2: Server Requirements & Permissions Audit
 */
$pageTitle = 'Step 2: Server Requirements';
$currentStep = 2;

require_once __DIR__ . '/includes/header.php';

$audit = check_server_requirements();
$canProceed = $audit['passed'];

if ($canProceed) {
    $_SESSION['installer']['step_requirements_passed'] = true;
} else {
    unset($_SESSION['installer']['step_requirements_passed']);
}
?>

<div class="installer-card p-6 sm:p-10">
  <div class="flex items-center justify-between mb-6 pb-6 border-b border-[#EAE8F5]">
    <div>
      <h1 class="text-2xl font-black text-[#191747]">Server Requirements &amp; Permissions</h1>
      <p class="text-xs text-[#737493] mt-0.5">Auditing your PHP environment, extensions, and directory write permissions.</p>
    </div>
    <div>
      <?php if ($canProceed): ?>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
          <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
          Ready to Install
        </span>
      <?php else: ?>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-600 border border-rose-200">
          <span class="w-2 h-2 rounded-full bg-rose-500"></span>
          Attention Required
        </span>
      <?php endif; ?>
    </div>
  </div>

  <?php if (!$canProceed): ?>
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs mb-6">
      <strong class="font-bold block mb-1">One or more critical server requirements failed:</strong>
      <span>Please review the highlighted items below and update your PHP version, install the missing extension, or grant write permissions to the listed directories.</span>
    </div>
  <?php endif; ?>

  <div class="overflow-x-auto mb-8">
    <table class="w-full text-left text-xs">
      <thead>
        <tr class="text-[#737493] border-b border-[#EAE8F5] text-[11px] bg-[#FAF9FF]">
          <th class="py-3 px-4 font-bold rounded-l-xl">Requirement</th>
          <th class="py-3 px-4 font-bold">Required</th>
          <th class="py-3 px-4 font-bold">Current Server Status</th>
          <th class="py-3 px-4 font-bold text-right rounded-r-xl">Status</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#F3EDFF] text-[#191747]">
        <?php foreach ($audit['checks'] as $check): ?>
          <tr class="hover:bg-[#FAF9FF] transition">
            <td class="py-3 px-4">
              <span class="font-bold block"><?= e($check['name']) ?></span>
              <?php if (!$check['passed'] && !empty($check['help'])): ?>
                <span class="text-[10px] text-rose-600 font-medium block mt-0.5"><?= e($check['help']) ?></span>
              <?php endif; ?>
            </td>
            <td class="py-3 px-4 text-[#737493] font-semibold"><?= e($check['required']) ?></td>
            <td class="py-3 px-4 font-mono font-medium"><?= e($check['current']) ?></td>
            <td class="py-3 px-4 text-right">
              <?php if ($check['passed']): ?>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                  <svg class="w-3 h-3 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                  </svg>
                  <span>Pass</span>
                </span>
              <?php else: ?>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200">
                  <svg class="w-3 h-3 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                  <span>Fail</span>
                </span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="flex items-center justify-between pt-4 border-t border-[#EAE8F5]">
    <a href="/install/index.php" class="btn-secondary text-xs">
      &larr; Previous Step
    </a>

    <?php if ($canProceed): ?>
      <a href="/install/database.php" class="btn-primary text-xs flex items-center gap-2">
        <span>Configure Database &rarr;</span>
      </a>
    <?php else: ?>
      <a href="/install/requirements.php" class="btn-primary text-xs flex items-center gap-2">
        <span>Recheck Requirements</span>
      </a>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
