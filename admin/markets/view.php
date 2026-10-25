<?php
/**
 * Admin: View Market
 */
require_once __DIR__ . '/../includes/auth-check.php';

$id = (int)($_GET['id'] ?? 0);
$market = Market::findById($id);

if (!$market) {
    set_flash('error', 'Market not found.');
    redirect('/admin/markets/index.php');
}

$pageTitle = "Market Details: {$market['name']}";
$latest = Result::findLatestForMarket($id);
$results = Result::getJodiChart($id, 10, 0);

require __DIR__ . '/../includes/head.php';
require __DIR__ . '/../includes/sidebar.php';
?>

<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../includes/header.php'; ?>
  <?php require __DIR__ . '/../../includes/flash-message.php'; ?>

  <main class="p-6 sm:p-8 space-y-6 flex-1 max-w-5xl">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-black text-[#191747]"><?= e($market['name']) ?></h1>
        <p class="text-xs text-[#737493] mt-0.5">Market configuration, live timings, and history.</p>
      </div>
      <div class="flex items-center space-x-2">
        <a href="/admin/markets/edit.php?id=<?= $market['id'] ?>" class="px-3 py-1.5 rounded-xl bg-[#6425D9] text-white text-xs font-bold">Edit Market</a>
        <a href="/admin/markets/index.php" class="text-xs font-bold text-[#737493] hover:underline">&larr; Back</a>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
      <div class="bg-white p-5 rounded-2xl border border-[#EAE8F5]">
        <span class="text-xs text-[#737493] block">Open Time</span>
        <strong class="text-base text-[#191747] block mt-1"><?= e($market['open_time']) ?></strong>
      </div>
      <div class="bg-white p-5 rounded-2xl border border-[#EAE8F5]">
        <span class="text-xs text-[#737493] block">Close Time</span>
        <strong class="text-base text-[#191747] block mt-1"><?= e($market['close_time']) ?></strong>
      </div>
      <div class="bg-white p-5 rounded-2xl border border-[#EAE8F5]">
        <span class="text-xs text-[#737493] block">Status</span>
        <strong class="text-base text-emerald-600 block mt-1"><?= ucfirst($market['status']) ?></strong>
      </div>
      <div class="bg-white p-5 rounded-2xl border border-[#EAE8F5]">
        <span class="text-xs text-[#737493] block">Latest Result</span>
        <strong class="text-base text-[#6425D9] block mt-1"><?= e($latest['full_result'] ?? 'None') ?></strong>
      </div>
    </div>
  </main>

  <?php require __DIR__ . '/../includes/footer.php'; ?>
</div>
