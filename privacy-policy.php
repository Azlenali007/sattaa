<?php
/**
 * Privacy Policy
 */
require_once __DIR__ . '/app/config/app.php';

$pageTitle = 'Privacy Policy';
$pageDescription = 'Privacy Policy for the Satka Matka live results and historical charts platform.';

require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>

<main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
  <div class="max-w-3xl mx-auto bg-white rounded-3xl border border-[#EAE8F5] p-8 sm:p-12 shadow-xs">
    <h1 class="text-3xl font-black text-[#191747] mb-6">Privacy Policy</h1>
    <div class="prose prose-sm text-xs text-[#737493] leading-relaxed space-y-4">
      <p>Last updated: April 2025</p>
      <h2 class="text-sm font-bold text-[#191747]">1. Information We Collect</h2>
      <p>This platform is purely an informational result indexing and charting service. We do not require public user registration or account creation. We only process anonymous server access logs for performance monitoring and security purposes.</p>
      
      <h2 class="text-sm font-bold text-[#191747]">2. Cookies &amp; Storage</h2>
      <p>We use temporary session cookies strictly to protect forms against cross-site request forgery (CSRF) and maintain secure administrative session tokens for authorized staff.</p>

      <h2 class="text-sm font-bold text-[#191747]">3. Data Security</h2>
      <p>We implement standard server protection measures including HTTPS encryption, parameterized database queries, and rate limiting to prevent unauthorized access and protect server infrastructure.</p>
    </div>
  </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
