<?php
/**
 * Admin: Announcement Banners
 */
require_once __DIR__ . '/../includes/auth-check.php';

$pageTitle = 'Announcement Banners';
$announcements = Announcement::all();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    enforce_csrf();
    $action = $_POST['action'] ?? 'create';

    if ($action === 'create') {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $type = $_POST['type'] ?? 'info';

        if ($title && $content) {
            Announcement::create([
                'title' => $title,
                'content' => $content,
                'type' => $type
            ]);
            set_flash('success', 'Announcement published on homepage.');
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        Announcement::delete($id);
        set_flash('success', 'Announcement removed.');
    }
    redirect('/admin/homepage/announcements.php');
}

require __DIR__ . '/../includes/head.php';
require __DIR__ . '/../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../includes/header.php'; ?>
  <?php require __DIR__ . '/../../includes/flash-message.php'; ?>

  <main class="p-6 sm:p-8 space-y-8 flex-1 max-w-4xl">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-black text-[#191747]">Homepage Notices &amp; Announcements</h1>
        <p class="text-xs text-[#737493] mt-0.5">Broadcast important market notices or holiday alerts.</p>
      </div>
      <a href="/admin/homepage/index.php" class="text-xs font-bold text-[#6425D9] hover:underline">&larr; Back to Homepage</a>
    </div>

    <!-- Create Notice Form -->
    <div class="bg-white rounded-2xl border border-[#EAE8F5] p-6 shadow-xs">
      <h2 class="text-sm font-bold text-[#191747] mb-4">Post New Notice</h2>
      <form action="/admin/homepage/announcements.php" method="POST" class="space-y-4">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="create">

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div class="sm:col-span-2">
            <label class="block text-xs font-bold text-[#191747] mb-1.5">Notice Title *</label>
            <input type="text" name="title" required placeholder="e.g. Market Timing Update" class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
          </div>
          <div>
            <label class="block text-xs font-bold text-[#191747] mb-1.5">Banner Type</label>
            <select name="type" class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]">
              <option value="info">Info (Purple)</option>
              <option value="warning">Warning (Amber)</option>
              <option value="success">Success (Emerald)</option>
            </select>
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-[#191747] mb-1.5">Content *</label>
          <textarea name="content" rows="2" required placeholder="Detailed message displayed to visitors..." class="w-full px-4 py-2.5 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747]"></textarea>
        </div>

        <div class="flex justify-end">
          <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#6425D9] hover:bg-[#521ec2] text-white text-xs font-bold shadow-xs transition">Post Notice</button>
        </div>
      </form>
    </div>

    <!-- Existing Notices -->
    <div class="bg-white rounded-2xl border border-[#EAE8F5] p-6 shadow-xs">
      <h2 class="text-sm font-bold text-[#191747] mb-4">Active &amp; Past Notices</h2>
      <div class="space-y-3">
        <?php if (empty($announcements)): ?>
          <p class="text-xs text-[#737493]">No announcements created yet.</p>
        <?php else: ?>
          <?php foreach ($announcements as $ann): ?>
            <div class="p-4 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] flex items-center justify-between gap-4">
              <div>
                <h3 class="font-bold text-xs text-[#191747]"><?= e($ann['title']) ?></h3>
                <p class="text-xs text-[#737493] mt-0.5"><?= e($ann['content']) ?></p>
              </div>
              <form action="/admin/homepage/announcements.php" method="POST" onsubmit="return confirm('Delete this announcement?');">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $ann['id'] ?>">
                <button type="submit" class="text-xs font-bold text-rose-600 hover:underline">Delete</button>
              </form>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </main>

  <?php require __DIR__ . '/../includes/footer.php'; ?>
</div>
