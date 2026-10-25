<?php
/**
 * Admin: Create New Market
 */
require_once __DIR__ . '/../includes/auth-check.php';

$pageTitle = 'Add New Market';
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
            $id = Market::create([
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
            Admin::logActivity($currentAdmin['id'], 'Create Market', 'Markets', $id, "Created market {$name}");
            set_flash('success', "Market '{$name}' created successfully!");
            redirect('/admin/markets/index.php');
        } catch (Exception $e) {
            $error = 'Failed to create market: ' . $e->getMessage();
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
        <h1 class="text-2xl font-black text-[#191747]">Add New Market</h1>
        <p class="text-xs text-[#737493] mt-0.5">Configure opening and closing schedules and display priority.</p>
      </div>
      <a href="/admin/markets/index.php" class="text-xs font-bold text-[#6425D9] hover:underline">&larr; Back to Markets</a>
    </div>

    <?php if ($error): ?>
      <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold"><?= e($error) ?></div>
    <?php endif; ?>

    <form action="/admin/markets/create.php" method="POST" class="bg-white rounded-2xl border border-[#EAE8F5] p-6 sm:p-8 shadow-xs space-y-5">
      <?= csrf_field() ?>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Market Name *</label>
          <input type="text" name="name" value="<?= e($_POST['name'] ?? '') ?>" required placeholder="e.g. Kalyan Night" class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747] focus:outline-none focus:border-[#6425D9]">
        </div>

        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Market Slug (Unique URL identifier)</label>
          <input type="text" name="slug" value="<?= e($_POST['slug'] ?? '') ?>" placeholder="e.g. kalyan-night (auto-generated if empty)" class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747] focus:outline-none focus:border-[#6425D9]">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Open Time *</label>
          <input type="text" name="open_time" value="<?= e($_POST['open_time'] ?? '') ?>" required placeholder="e.g. 10:00 AM" class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747] focus:outline-none focus:border-[#6425D9]">
        </div>

        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Close Time *</label>
          <input type="text" name="close_time" value="<?= e($_POST['close_time'] ?? '') ?>" required placeholder="e.g. 12:00 PM" class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747] focus:outline-none focus:border-[#6425D9]">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Status</label>
          <select name="status" class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
            <option value="active" <?= (($_POST['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= (($_POST['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Display Order</label>
          <input type="number" name="display_order" value="<?= e($_POST['display_order'] ?? '0') ?>" class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
        </div>

        <div class="flex items-center space-x-6 pt-6">
          <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-[#191747]">
            <input type="checkbox" name="is_live" value="1" <?= !empty($_POST['is_live']) ? 'checked' : '' ?> class="rounded text-[#6425D9]">
            <span>Live Now</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-[#191747]">
            <input type="checkbox" name="is_featured" value="1" <?= (!isset($_POST['is_featured']) || !empty($_POST['is_featured'])) ? 'checked' : '' ?> class="rounded text-[#6425D9]">
            <span>Featured</span>
          </label>
        </div>
      </div>

      <div class="space-y-4 pt-4 border-t border-[#EAE8F5]">
        <h3 class="text-xs font-black text-[#191747] uppercase tracking-wider">SEO Metadata</h3>
        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">SEO Meta Title</label>
          <input type="text" name="seo_title" value="<?= e($_POST['seo_title'] ?? '') ?>" placeholder="Custom title tag for this market" class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
        </div>
        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">SEO Meta Description</label>
          <textarea name="seo_description" rows="2" placeholder="Custom meta description for search engines" class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]"><?= e($_POST['seo_description'] ?? '') ?></textarea>
        </div>
      </div>

      <div class="pt-4 flex items-center justify-end space-x-3">
        <a href="/admin/markets/index.php" class="px-4 py-2.5 rounded-xl border border-[#EAE8F5] text-xs font-bold text-[#737493] hover:bg-[#FAF9FF]">Cancel</a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#6425D9] hover:bg-[#521ec2] text-white text-xs font-bold shadow-xs transition">Create Market</button>
      </div>
    </form>
  </main>

  <?php require __DIR__ . '/../includes/footer.php'; ?>
</div>
