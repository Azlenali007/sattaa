<?php
/**
 * Terms and Conditions
 */
require_once __DIR__ . '/app/config/app.php';

$pageTitle = 'Terms & Conditions';
$pageDescription = 'Terms of Service for Satka Matka results and historical charts.';

require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>

<main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
  <div class="max-w-3xl mx-auto bg-white rounded-3xl border border-[#EAE8F5] p-8 sm:p-12 shadow-xs">
    <h1 class="text-3xl font-black text-[#191747] mb-6">Terms &amp; Conditions</h1>
    <div class="prose prose-sm text-xs text-[#737493] leading-relaxed space-y-4">
      <p>Last updated: April 2025</p>
      <h2 class="text-sm font-bold text-[#191747]">1. Informational Purpose</h2>
      <p>All information provided on SATKA MATKA is published strictly for informational, educational, and statistical charting reference. We do not operate, endorse, or host gambling activities.</p>
      
      <h2 class="text-sm font-bold text-[#191747]">2. Accuracy of Results</h2>
      <p>While every reasonable effort is made to synchronize and verify results with official sources without delay, users should verify numbers independently. We make no warranty that service will be continuous or error-free.</p>

      <h2 class="text-sm font-bold text-[#191747]">3. Acceptable Use</h2>
      <p>Automated scraping or denial of service attacks that impair server availability are strictly prohibited.</p>
    </div>
  </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
