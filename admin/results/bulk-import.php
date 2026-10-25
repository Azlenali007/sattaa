<?php
/**
 * Admin: Bulk Import Results
 */
require_once __DIR__ . '/../includes/auth-check.php';

$pageTitle = 'Bulk Import Results';
$error = '';
$importedCount = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    enforce_csrf();
    $rawLines = trim($_POST['import_data'] ?? '');
    if (empty($rawLines)) {
        $error = 'Please enter at least one line of CSV data.';
    } else {
        $lines = explode("\n", str_replace("\r", "", $rawLines));
        $pdo = Database::getConnection();

        foreach ($lines as $line) {
            $parts = str_getcsv(trim($line));
            if (count($parts) >= 4) {
                $date = trim($parts[0]);
                $slug = trim($parts[1]);
                $open = trim($parts[2]);
                $close = trim($parts[3]);

                $market = Market::findBySlug($slug);
                if ($market && $date) {
                    $openParts = format_digits($open);
                    $closeParts = format_digits($close);
                    $j1 = $openParts[1] !== '' ? $openParts[1] : substr(preg_replace('/\D/', '', $open), -1);
                    $j2 = $closeParts[1] !== '' ? $closeParts[1] : substr(preg_replace('/\D/', '', $close), -1);
                    $jodi = ($j1 !== '' && $j2 !== '') ? ($j1 . $j2) : '';
                    $full = trim($open . ' - ' . $close, ' -');

                    $stmt = $pdo->prepare("
                        INSERT INTO results (market_id, result_date, open_digit, close_digit, open_display, close_display, jodi, full_result, status)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'published')
                        ON CONFLICT(market_id, result_date) DO UPDATE SET
                            open_display = excluded.open_display,
                            close_display = excluded.close_display,
                            jodi = excluded.jodi,
                            full_result = excluded.full_result,
                            updated_at = CURRENT_TIMESTAMP
                    ");
                    $stmt->execute([$market['id'], $date, $j1, $j2, $open, $close, $jodi, $full]);
                    $importedCount++;
                }
            }
        }

        Admin::logActivity($currentAdmin['id'], 'Bulk Import Results', 'Results', null, "Imported {$importedCount} records");
        set_flash('success', "Successfully imported {$importedCount} result records!");
        redirect('/admin/results/index.php');
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
        <h1 class="text-2xl font-black text-[#191747]">Bulk Import Results</h1>
        <p class="text-xs text-[#737493] mt-0.5">Import multiple historical records in CSV format.</p>
      </div>
      <a href="/admin/results/index.php" class="text-xs font-bold text-[#6425D9] hover:underline">&larr; Back to Results</a>
    </div>

    <?php if ($error): ?>
      <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold"><?= e($error) ?></div>
    <?php endif; ?>

    <form action="/admin/results/bulk-import.php" method="POST" class="bg-white rounded-2xl border border-[#EAE8F5] p-6 sm:p-8 shadow-xs space-y-5">
      <?= csrf_field() ?>

      <div>
        <label class="block text-xs font-bold text-[#191747] mb-1.5">CSV Data (Date, Market Slug, Open Display, Close Display)</label>
        <textarea 
          name="import_data" 
          rows="8" 
          placeholder="2025-04-25,kalyan,8 2,7 5&#10;2025-04-25,main-mumbai,3 8,1 6" 
          required 
          class="w-full px-4 py-3 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-mono font-medium text-[#191747] focus:outline-none focus:border-[#6425D9]"
        ></textarea>
        <span class="text-[11px] text-[#737493] mt-2 block">Format: <code>YYYY-MM-DD,market-slug,open-value,close-value</code> (One record per line)</span>
      </div>

      <div class="pt-4 border-t border-[#EAE8F5] flex items-center justify-end space-x-3">
        <a href="/admin/results/index.php" class="px-4 py-2.5 rounded-xl border border-[#EAE8F5] text-xs font-bold text-[#737493] hover:bg-[#FAF9FF]">Cancel</a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#6425D9] hover:bg-[#521ec2] text-white text-xs font-bold shadow-xs transition">
          Import Records
        </button>
      </div>
    </form>
  </main>

  <?php require __DIR__ . '/../includes/footer.php'; ?>
</div>
