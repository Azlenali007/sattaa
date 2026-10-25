<?php
/**
 * 404 Not Found Page
 */
require_once __DIR__ . '/app/config/app.php';

$pageTitle = 'Page Not Found';
http_response_code(404);

require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>

<main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-16 w-full text-center">
  <div class="max-w-md mx-auto bg-white rounded-3xl border border-[#EAE8F5] p-10 shadow-xs">
    <div class="w-16 h-16 rounded-2xl bg-[#F3EDFF] text-[#6425D9] flex items-center justify-center mx-auto mb-4 font-black text-2xl">
      404
    </div>
    <h1 class="text-2xl font-black text-[#191747] mb-2">Page Not Found</h1>
    <p class="text-xs text-[#737493] mb-6">The page or market chart you requested could not be located.</p>
    <a href="/index.php" class="inline-block px-5 py-2.5 rounded-xl bg-[#6425D9] hover:bg-[#521ec2] text-white text-xs font-bold transition">
      Return to Homepage
    </a>
  </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
