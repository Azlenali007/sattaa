<?php
/**
 * Pagination Navigation Component
 */
$paginator = $paginator ?? null;
$baseUrl = $baseUrl ?? '';
if ($paginator && $paginator->hasPages()):
?>
  <div class="flex items-center justify-center space-x-2 my-6 text-xs">
    <?php if ($paginator->hasPrev()): ?>
      <a href="<?= e($baseUrl) ?>page=<?= $paginator->prevPage() ?>" class="px-3 py-2 rounded-lg border border-[#EAE8F5] bg-white text-[#737493] hover:bg-[#F3EDFF] hover:text-[#6425D9] transition font-bold">&larr; Previous</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $paginator->totalPages; $i++): ?>
      <?php if ($i === $paginator->currentPage): ?>
        <span class="w-8 h-8 rounded-lg bg-[#6425D9] text-white flex items-center justify-center font-bold shadow-xs"><?= $i ?></span>
      <?php else: ?>
        <a href="<?= e($baseUrl) ?>page=<?= $i ?>" class="w-8 h-8 rounded-lg border border-[#EAE8F5] bg-white text-[#737493] flex items-center justify-center hover:bg-[#F3EDFF] hover:text-[#6425D9] transition font-semibold"><?= $i ?></a>
      <?php endif; ?>
    <?php endfor; ?>

    <?php if ($paginator->hasNext()): ?>
      <a href="<?= e($baseUrl) ?>page=<?= $paginator->nextPage() ?>" class="px-3 py-2 rounded-lg border border-[#EAE8F5] bg-white text-[#737493] hover:bg-[#F3EDFF] hover:text-[#6425D9] transition font-bold">Next &rarr;</a>
    <?php endif; ?>
  </div>
<?php endif; ?>
