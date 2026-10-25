<?php
/**
 * Admin: Declare / Add Result
 */
require_once __DIR__ . '/../includes/auth-check.php';

$pageTitle = 'Declare Market Result';
$markets = Market::getActive();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    enforce_csrf();
    $marketId = (int)($_POST['market_id'] ?? 0);
    $resultDate = trim($_POST['result_date'] ?? date('Y-m-d'));
    $openDisplay = trim($_POST['open_display'] ?? '');
    $closeDisplay = trim($_POST['close_display'] ?? '');
    $status = $_POST['status'] ?? 'published';

    $validator = new Validator($_POST);
    $validator->required('market_id')->required('result_date');

    if ($validator->passes()) {
        try {
            // Compute jodi from last digit of open and last digit of close
            $openParts = format_digits($openDisplay);
            $closeParts = format_digits($closeDisplay);
            $j1 = $openParts[1] !== '' ? $openParts[1] : substr(preg_replace('/\D/', '', $openDisplay), -1);
            $j2 = $closeParts[1] !== '' ? $closeParts[1] : substr(preg_replace('/\D/', '', $closeDisplay), -1);
            $jodi = ($j1 !== '' && $j2 !== '') ? ($j1 . $j2) : '';

            $resId = Result::create([
                'market_id' => $marketId,
                'result_date' => $resultDate,
                'open_digit' => $j1,
                'close_digit' => $j2,
                'open_display' => $openDisplay,
                'close_display' => $closeDisplay,
                'jodi' => $jodi,
                'status' => $status
            ]);

            Admin::logActivity($currentAdmin['id'], 'Declare Result', 'Results', $resId, "Declared result for market ID {$marketId} on {$resultDate}");
            set_flash('success', 'Market result declared and published successfully!');
            redirect('/admin/results/index.php');
        } catch (Exception $e) {
            $error = 'Failed to save result: A result for this market on this date may already exist. (' . $e->getMessage() . ')';
        }
    } else {
        $error = $validator->firstError();
    }
}

require __DIR__ . '/../includes/head.php';
require __DIR__ . '/../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../includes/header.php'; ?>
  <?php require __DIR__ . '/../../includes/flash-message.php'; ?>

  <main class="p-6 sm:p-8 space-y-6 flex-1 max-w-3xl">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-black text-[#191747]">Declare Result</h1>
        <p class="text-xs text-[#737493] mt-0.5">Input verified open and close digits to update live boards and charts.</p>
      </div>
      <a href="/admin/results/index.php" class="text-xs font-bold text-[#6425D9] hover:underline">&larr; Back to Results</a>
    </div>

    <?php if ($error): ?>
      <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold"><?= e($error) ?></div>
    <?php endif; ?>

    <form action="/admin/results/create.php" method="POST" class="bg-white rounded-2xl border border-[#EAE8F5] p-6 sm:p-8 shadow-xs space-y-5">
      <?= csrf_field() ?>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Select Market *</label>
          <select name="market_id" required class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747] focus:outline-none focus:border-[#6425D9]">
            <option value="">-- Choose Market --</option>
            <?php foreach ($markets as $m): ?>
              <option value="<?= $m['id'] ?>" <?= (($_POST['market_id'] ?? '') == $m['id']) ? 'selected' : '' ?>>
                <?= e($m['name']) ?> (<?= e($m['open_time']) ?> - <?= e($m['close_time']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Result Date *</label>
          <input type="date" name="result_date" value="<?= e($_POST['result_date'] ?? date('Y-m-d')) ?>" required class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747] focus:outline-none focus:border-[#6425D9]">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-3">
        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Open Numbers (e.g. 8 2 or 128-1)</label>
          <input type="text" name="open_display" value="<?= e($_POST['open_display'] ?? '') ?>" placeholder="e.g. 8 2" class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747] focus:outline-none focus:border-[#6425D9]">
          <span class="text-[10px] text-[#737493] mt-1 block">Displayed in the left Open box</span>
        </div>

        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Close Numbers (e.g. 7 5 or 5-456)</label>
          <input type="text" name="close_display" value="<?= e($_POST['close_display'] ?? '') ?>" placeholder="e.g. 7 5" class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747] focus:outline-none focus:border-[#6425D9]">
          <span class="text-[10px] text-[#737493] mt-1 block">Displayed in the right Close box</span>
        </div>
      </div>

      <div class="pt-2">
        <label class="block text-xs font-bold text-[#191747] mb-1.5">Publication Status</label>
        <select name="status" class="w-full sm:w-1/2 px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
          <option value="published">Published (Visible immediately)</option>
          <option value="draft">Draft (Hidden from public)</option>
        </select>
      </div>

      <div class="pt-4 border-t border-[#EAE8F5] flex items-center justify-end space-x-3">
        <a href="/admin/results/index.php" class="px-4 py-2.5 rounded-xl border border-[#EAE8F5] text-xs font-bold text-[#737493] hover:bg-[#FAF9FF]">Cancel</a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#6425D9] hover:bg-[#521ec2] text-white text-xs font-bold shadow-xs transition">
          Publish Result
        </button>
      </div>
    </form>
  </main>

  <?php require __DIR__ . '/../includes/footer.php'; ?>
</div>
