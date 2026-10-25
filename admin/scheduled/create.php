<?php
/**
 * Admin: Create Scheduled Publication
 */
require_once __DIR__ . '/../includes/auth-check.php';

$pageTitle = 'Schedule Future Result';
$markets = Market::getActive();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    enforce_csrf();
    $marketId = (int)($_POST['market_id'] ?? 0);
    $scheduledDate = trim($_POST['scheduled_date'] ?? '');
    $scheduledTime = trim($_POST['scheduled_time'] ?? '');
    $openDisplay = trim($_POST['open_display'] ?? '');
    $closeDisplay = trim($_POST['close_display'] ?? '');

    $validator = new Validator($_POST);
    $validator->required('market_id')->required('scheduled_date')->required('scheduled_time');

    if ($validator->passes()) {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                INSERT INTO scheduled_publications (market_id, scheduled_date, scheduled_time, open_display, close_display, status)
                VALUES (?, ?, ?, ?, ?, 'pending')
            ");
            $stmt->execute([$marketId, $scheduledDate, $scheduledTime, $openDisplay, $closeDisplay]);
            Admin::logActivity($currentAdmin['id'], 'Create Schedule', 'Scheduled', (int)$pdo->lastInsertId(), "Scheduled release for market ID {$marketId}");
            set_flash('success', 'Publication scheduled successfully!');
            redirect('/admin/scheduled/index.php');
        } catch (Exception $e) {
            $error = 'Failed to create schedule: ' . $e->getMessage();
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
        <h1 class="text-2xl font-black text-[#191747]">Schedule Result Release</h1>
        <p class="text-xs text-[#737493] mt-0.5">Automate publication at a specific future date and time.</p>
      </div>
      <a href="/admin/scheduled/index.php" class="text-xs font-bold text-[#6425D9] hover:underline">&larr; Back to Scheduled</a>
    </div>

    <?php if ($error): ?>
      <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold"><?= e($error) ?></div>
    <?php endif; ?>

    <form action="/admin/scheduled/create.php" method="POST" class="bg-white rounded-2xl border border-[#EAE8F5] p-6 sm:p-8 shadow-xs space-y-5">
      <?= csrf_field() ?>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Market *</label>
          <select name="market_id" required class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
            <?php foreach ($markets as $m): ?>
              <option value="<?= $m['id'] ?>"><?= e($m['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Scheduled Date *</label>
          <input type="date" name="scheduled_date" value="<?= date('Y-m-d') ?>" required class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
        </div>

        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Scheduled Time *</label>
          <input type="time" name="scheduled_time" value="12:05" required class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-3">
        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Open Numbers</label>
          <input type="text" name="open_display" placeholder="e.g. 8 2" class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
        </div>

        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Close Numbers</label>
          <input type="text" name="close_display" placeholder="e.g. 7 5" class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
        </div>
      </div>

      <div class="pt-4 border-t border-[#EAE8F5] flex items-center justify-end space-x-3">
        <a href="/admin/scheduled/index.php" class="px-4 py-2.5 rounded-xl border border-[#EAE8F5] text-xs font-bold text-[#737493] hover:bg-[#FAF9FF]">Cancel</a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#6425D9] hover:bg-[#521ec2] text-white text-xs font-bold shadow-xs transition">Schedule Publication</button>
      </div>
    </form>
  </main>

  <?php require __DIR__ . '/../includes/footer.php'; ?>
</div>
