<?php
/**
 * About Page
 */
require_once __DIR__ . '/app/config/app.php';

$pageTitle = 'About Us';
$pageDescription = 'Learn more about Satka Matka live results publication, verified timing schedules, and chart records.';

require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
require __DIR__ . '/includes/flash-message.php';
?>

<main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
  <div class="max-w-3xl mx-auto bg-white rounded-3xl border border-[#EAE8F5] p-8 sm:p-12 shadow-xs">
    <div class="w-12 h-12 rounded-2xl bg-[#F3EDFF] text-[#6425D9] flex items-center justify-center mb-6">
      <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
        <path d="M2.5 19h19v2.5H2.5V19zm1.75-2l1.9-9.5 4.35 4.5L12 4.5l1.5 7.5 4.35-4.5 1.9 9.5H4.25zm7.75-10a1.25 1.25 0 100-2.5 1.25 1.25 0 000 2.5zm-6 2.5A1.25 1.25 0 106 7a1.25 1.25 0 000 2.5zm12 0a1.25 1.25 0 100-2.5 1.25 1.25 0 000 2.5z"/>
      </svg>
    </div>

    <h1 class="text-3xl font-black text-[#191747] mb-4">About SATKA MATKA</h1>
    <p class="text-sm text-[#737493] leading-relaxed mb-6">
      SATKA MATKA is a dedicated results publishing portal providing real-time market updates, accurate opening and closing timing schedules, and complete historical jodi and panel charts. Our objective is to deliver fast, verified, and accessible market records with optimal transparency and precision.
    </p>

    <h2 class="text-lg font-bold text-[#191747] mt-8 mb-3">Key Features</h2>
    <ul class="space-y-3 text-xs text-[#737493] mb-8">
      <li class="flex items-start gap-2">
        <span class="w-2 h-2 rounded-full bg-[#6425D9] mt-1.5 shrink-0"></span>
        <span><strong>Live Synchronization:</strong> Automated result ingestion ensures immediate updates as soon as verified numbers are declared.</span>
      </li>
      <li class="flex items-start gap-2">
        <span class="w-2 h-2 rounded-full bg-[#6425D9] mt-1.5 shrink-0"></span>
        <span><strong>Historical Chart Archives:</strong> Complete daily jodi and panel histories allowing structured date-wise analysis and reference.</span>
      </li>
      <li class="flex items-start gap-2">
        <span class="w-2 h-2 rounded-full bg-[#6425D9] mt-1.5 shrink-0"></span>
        <span><strong>Mobile Optimized:</strong> Built with a clean, lightweight white and purple interface tailored for quick browsing on phones, tablets, and desktop displays.</span>
      </li>
    </ul>

    <div class="pt-6 border-t border-[#EAE8F5] flex items-center justify-between">
      <span class="text-xs text-[#737493]">Have questions or feedback?</span>
      <a href="/contact.php" class="px-4 py-2 rounded-xl bg-[#6425D9] hover:bg-[#521ec2] text-white text-xs font-bold transition">
        Contact Support
      </a>
    </div>
  </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
