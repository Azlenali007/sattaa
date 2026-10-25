<?php
/**
 * Admin: Edit Result
 */
require_once __DIR__ . '/../includes/auth-check.php';

$id = (int)($_GET['id'] ?? 0);
$pdo = Database::getConnection();
$stmt = $pdo->prepare("SELECT * FROM results WHERE id = ?");
$stmt->execute([$id]);
$result = $stmt->fetch();

if (!$result) {
    set_flash('error', 'Result record not found.');
    redirect('/admin/results/index.php');
}

$pageTitle = 'Edit Result';
$markets = Market::getActive();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    enforce_csrf();
    $marketId = (int)($_POST['market_id'] ?? 0);
    $resultDate = trim($_POST['result_date'] ?? '');
    $openDisplay = trim($_POST['open_display'] ?? '');
    $closeDisplay = trim($_POST['close_display'] ?? '');
    $status = $_POST['status'] ?? 'published';

    $validator = new Validator($_POST);
    $validator->required('market_id')->required('result_date');

    if ($validator->passes()) {
        try {
            $openParts = format_digits($openDisplay);
            $closeParts = format_digits($closeDisplay);
            $j1 = $openParts[1] !== '' ? $openParts[1] : substr(preg_replace('/\D/', '', $openDisplay), -1);
            $j2 = $closeParts[1] !== '' ? $closeParts[1] : substr(preg_replace('/\D/', '', $closeDisplay), -1);
            $jodi = ($j1 !== '' && $j2 !== '') ? ($j1 . $j2) : '';

            Result::update($id, [
                'market_id' => $marketId,
                'result_date' => $resultDate,
                'open_digit' => $j1,
                'close_digit' => $j2,
                'open_display' => $openDisplay,
                'close_display' => $closeDisplay,
                'jodi' => $jodi,
                'status' => $status
            ]);

            Admin::logActivity($currentAdmin['id'], 'Update Result', 'Results', $id, "Updated result ID {$id}");
            set_flash('success', 'Market result updated successfully!');
            redirect('/admin/results/index.php');
        } catch (Exception $e) {
            $error = 'Failed to update result: ' . $e->getMessage();
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
        <h1 class="text-2xl font-black text-[#191747]">Edit Result</h1>
        <p class="text-xs text-[#737493] mt-0.5">Modify numbers, jodi, and publication status.</p>
      </div>
      <a href="/admin/results/index.php" class="text-xs font-bold text-[#6425D9] hover:underline">&larr; Back to Results</a>
    </div>

    <?php if ($error): ?>
      <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold"><?= e($error) ?></div>
    <?php endif; ?>

    <form action="/admin/results/edit.php?id=<?= $result['id'] ?>" method="POST" class="bg-white rounded-2xl border border-[#EAE8F5] p-6 sm:p-8 shadow-xs space-y-5">
      <?= csrf_field() ?>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Market *</label>
          <select name="market_id" required class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
            <?php foreach ($markets as $m): ?>
              <option value="<?= $m['id'] ?>" <?= $result['market_id'] == $m['id'] ? 'selected' : '' ?>>
                <?= e($m['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Result Date *</label>
          <input type="date" name="result_date" value="<?= e($result['result_date']) ?>" required class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-3">
        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Open Numbers</label>
          <input type="text" name="open_display" value="<?= e($result['open_display']) ?>" class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
        </div>

        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Close Numbers</label>
          <input type="text" name="close_display" value="<?= e($result['close_display']) ?>" class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
        </div>
      </div>

      <div class="pt-2">
        <label class="block text-xs font-bold text-[#191747] mb-1.5">Publication Status</label>
        <select name="status" class="w-full sm:w-1/2 px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
          <option value="published" <?= $result['status'] === 'published' ? 'selected' : '' ?>>Published</option>
          <option value="draft" <?= $result['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
        </select>
      </div>

      <div class="pt-4 border-t border-[#EAE8F5] flex items-center justify-end space-x-3">
        <a href="/admin/results/index.php" class="px-4 py-2.5 rounded-xl border border-[#EAE8F5] text-xs font-bold text-[#737493] hover:bg-[#FAF9FF]">Cancel</a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#6425D9] hover:bg-[#521ec2] text-white text-xs font-bold shadow-xs transition">Save Changes</button>
      </div>
    </form>
  </main>

  <?php require __DIR__ . '/../includes/footer.php'; ?>
</div>
