<?php
/**
 * Admin: Edit Market
 */
require_once __DIR__ . '/../includes/auth-check.php';

$id = (int)($_GET['id'] ?? 0);
$market = Market::findById($id);

if (!$market) {
    set_flash('error', 'Market record not found.');
    redirect('/admin/markets/index.php');
}

$pageTitle = "Edit Market: {$market['name']}";
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    enforce_csrf();
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '') ?: strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
    $openTime = trim($_POST['open_time'] ?? '');
    $closeTime = trim($_POST['close_time'] ?? '');
    $status = $_POST['status'] ?? 'active';
    $isLive = isset($_POST['is_live']) ? 1 : 0;
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $displayOrder = (int)($_POST['display_order'] ?? 0);
    $seoTitle = trim($_POST['seo_title'] ?? '');
    $seoDesc = trim($_POST['seo_description'] ?? '');

    $validator = new Validator($_POST);
    $validator->required('name')->required('open_time')->required('close_time');

    if ($validator->passes()) {
        try {
            Market::update($id, [
                'name' => $name,
                'slug' => $slug,
                'open_time' => $openTime,
                'close_time' => $closeTime,
                'status' => $status,
                'is_live' => $isLive,
                'is_featured' => $isFeatured,
                'display_order' => $displayOrder,
                'seo_title' => $seoTitle,
                'seo_description' => $seoDesc
            ]);
            Admin::logActivity($currentAdmin['id'], 'Update Market', 'Markets', $id, "Updated market {$name}");
            set_flash('success', "Market '{$name}' updated successfully!");
            redirect('/admin/markets/index.php');
        } catch (Exception $e) {
            $error = 'Failed to update market: ' . $e->getMessage();
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

  <main class="p-6 sm:p-8 space-y-6 flex-1 max-w-4xl">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-black text-[#191747]">Edit Market: <?= e($market['name']) ?></h1>
        <p class="text-xs text-[#737493] mt-0.5">Modify opening schedule, status, and display attributes.</p>
      </div>
      <a href="/admin/markets/index.php" class="text-xs font-bold text-[#6425D9] hover:underline">&larr; Back to Markets</a>
    </div>

    <?php if ($error): ?>
      <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold"><?= e($error) ?></div>
    <?php endif; ?>

    <form action="/admin/markets/edit.php?id=<?= $market['id'] ?>" method="POST" class="bg-white rounded-2xl border border-[#EAE8F5] p-6 sm:p-8 shadow-xs space-y-5">
      <?= csrf_field() ?>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Market Name *</label>
          <input type="text" name="name" value="<?= e($market['name']) ?>" required class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747] focus:outline-none focus:border-[#6425D9]">
        </div>

        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Market Slug (URL)</label>
          <input type="text" name="slug" value="<?= e($market['slug']) ?>" required class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747] focus:outline-none focus:border-[#6425D9]">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Open Time *</label>
          <input type="text" name="open_time" value="<?= e($market['open_time']) ?>" required class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747] focus:outline-none focus:border-[#6425D9]">
        </div>

        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Close Time *</label>
          <input type="text" name="close_time" value="<?= e($market['close_time']) ?>" required class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747] focus:outline-none focus:border-[#6425D9]">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Status</label>
          <select name="status" class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
            <option value="active" <?= ($market['status'] === 'active') ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= ($market['status'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Display Order</label>
          <input type="number" name="display_order" value="<?= e($market['display_order']) ?>" class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
        </div>

        <div class="flex items-center space-x-6 pt-6">
          <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-[#191747]">
            <input type="checkbox" name="is_live" value="1" <?= !empty($market['is_live']) ? 'checked' : '' ?> class="rounded text-[#6425D9]">
            <span>Live Now</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-[#191747]">
            <input type="checkbox" name="is_featured" value="1" <?= !empty($market['is_featured']) ? 'checked' : '' ?> class="rounded text-[#6425D9]">
            <span>Featured</span>
          </label>
        </div>
      </div>

      <div class="space-y-4 pt-4 border-t border-[#EAE8F5]">
        <h3 class="text-xs font-black text-[#191747] uppercase tracking-wider">SEO Metadata</h3>
        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">SEO Meta Title</label>
          <input type="text" name="seo_title" value="<?= e($market['seo_title'] ?? '') ?>" class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
        </div>
        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">SEO Meta Description</label>
          <textarea name="seo_description" rows="2" class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]"><?= e($market['seo_description'] ?? '') ?></textarea>
        </div>
      </div>

      <div class="pt-4 flex items-center justify-end space-x-3">
        <a href="/admin/markets/index.php" class="px-4 py-2.5 rounded-xl border border-[#EAE8F5] text-xs font-bold text-[#737493] hover:bg-[#FAF9FF]">Cancel</a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#6425D9] hover:bg-[#521ec2] text-white text-xs font-bold shadow-xs transition">Save Changes</button>
      </div>
    </form>
  </main>

  <?php require __DIR__ . '/../includes/footer.php'; ?>
</div>
