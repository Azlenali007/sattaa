<?php
/**
 * Admin: Homepage Management
 */
require_once __DIR__ . '/../includes/auth-check.php';

$pageTitle = 'Homepage Content Management';
$markets = Market::all();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    enforce_csrf();
    $featuredIds = $_POST['featured'] ?? [];
    $pdo = Database::getConnection();

    foreach ($markets as $m) {
        $isF = in_array((string)$m['id'], $featuredIds, true) ? 1 : 0;
        $order = (int)($_POST['order'][$m['id']] ?? $m['display_order']);
        $u = $pdo->prepare("UPDATE markets SET is_featured = ?, display_order = ? WHERE id = ?");
        $u->execute([$isF, $order, $m['id']]);
    }

    Admin::logActivity($currentAdmin['id'], 'Update Homepage', 'Homepage', null, 'Updated featured markets configuration');
    set_flash('success', 'Homepage market display settings updated successfully!');
    redirect('/admin/homepage/index.php');
}

require __DIR__ . '/../includes/head.php';
require __DIR__ . '/../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../includes/header.php'; ?>
  <?php require __DIR__ . '/../../includes/flash-message.php'; ?>

  <main class="p-6 sm:p-8 space-y-6 flex-1 max-w-4xl">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-black text-[#191747]">Homepage Management</h1>
        <p class="text-xs text-[#737493] mt-0.5">Control which markets appear in the 4-card Latest Results and 6-card Popular Markets grids.</p>
      </div>
      <a href="/admin/homepage/announcements.php" class="px-4 py-2.5 rounded-xl bg-[#F3EDFF] text-[#6425D9] text-xs font-bold hover:bg-[#e7d8ff] transition">
        Manage Notice Banners &rarr;
      </a>
    </div>

    <form action="/admin/homepage/index.php" method="POST" class="bg-white rounded-2xl border border-[#EAE8F5] p-6 sm:p-8 shadow-xs space-y-6">
      <?= csrf_field() ?>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead>
            <tr class="text-[#737493] border-b border-[#EAE8F5] text-[11px] bg-[#FAF9FF]">
              <th class="py-3 px-4 font-bold text-center rounded-l-xl">Featured</th>
              <th class="py-3 px-4 font-bold">Market Name</th>
              <th class="py-3 px-4 font-bold">Open - Close</th>
              <th class="py-3 px-4 font-bold text-center rounded-r-xl">Display Priority</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#F3EDFF] text-[#191747]">
            <?php foreach ($markets as $m): ?>
              <tr class="hover:bg-[#FAF9FF]">
                <td class="py-3 px-4 text-center">
                  <input type="checkbox" name="featured[]" value="<?= $m['id'] ?>" <?= !empty($m['is_featured']) ? 'checked' : '' ?> class="rounded text-[#6425D9]">
                </td>
                <td class="py-3 px-4 font-bold text-sm"><?= e($m['name']) ?></td>
                <td class="py-3 px-4 text-[#737493]"><?= e($m['open_time']) ?> - <?= e($m['close_time']) ?></td>
                <td class="py-3 px-4 text-center">
                  <input type="number" name="order[<?= $m['id'] ?>]" value="<?= e($m['display_order']) ?>" class="w-16 px-2 py-1 text-center rounded-lg bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-bold text-[#191747]">
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="pt-4 border-t border-[#EAE8F5] flex justify-end">
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#6425D9] hover:bg-[#521ec2] text-white text-xs font-bold shadow-xs transition">
          Update Homepage Layout
        </button>
      </div>
    </form>
  </main>

  <?php require __DIR__ . '/../includes/footer.php'; ?>
</div>
