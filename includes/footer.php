<?php
/**
 * Shared Footer with Bottom CTA & Reference Styling
 */
?>
<!-- Bottom CTA Banner matching reference image -->
<section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 mt-12 mb-10 w-full">
  <div class="bg-[#6425D9] rounded-2xl p-6 sm:p-8 flex flex-col md:flex-row items-center justify-between gap-6 shadow-lg shadow-purple-500/10">
    <div class="flex items-center gap-4 text-white">
      <div class="w-12 h-12 rounded-xl bg-white/15 flex items-center justify-center shrink-0">
        <svg class="w-7 h-7 fill-white" viewBox="0 0 24 24">
          <path d="M2.5 19h19v2.5H2.5V19zm1.75-2l1.9-9.5 4.35 4.5L12 4.5l1.5 7.5 4.35-4.5 1.9 9.5H4.25zm7.75-10a1.25 1.25 0 100-2.5 1.25 1.25 0 000 2.5zm-6 2.5A1.25 1.25 0 106 7a1.25 1.25 0 000 2.5zm12 0a1.25 1.25 0 100-2.5 1.25 1.25 0 000 2.5z"/>
        </svg>
      </div>
      <div>
        <h3 class="text-xl font-bold tracking-tight">Get the Latest Satka Matka Results</h3>
        <p class="text-xs sm:text-sm text-purple-200 mt-0.5">Stay updated with live results, charts and historical data.</p>
      </div>
    </div>
    
    <div class="w-full md:w-auto">
      <form action="/search.php" method="GET" class="relative max-w-md w-full">
        <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-400">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
          </svg>
        </span>
        <input 
          type="text" 
          name="q" 
          placeholder="Search market or date..." 
          class="w-full sm:w-80 pl-11 pr-12 py-3 bg-white rounded-full text-xs text-[#191747] placeholder-gray-400 focus:outline-none shadow-sm"
        >
        <button 
          type="submit" 
          class="absolute right-1.5 top-1.5 bottom-1.5 w-9 bg-[#6425D9] rounded-full flex items-center justify-center text-white hover:bg-[#521ec2] transition"
          aria-label="Search"
        >
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
          </svg>
        </button>
      </form>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="bg-[#191747] text-white pt-14 pb-12 mt-auto">
  <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col lg:flex-row items-center justify-between gap-8 pb-10 border-b border-white/10">
      
      <!-- Brand Logo in Footer -->
      <a href="/index.php" class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-[#6425D9] flex items-center justify-center text-white">
          <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
            <path d="M2.5 19h19v2.5H2.5V19zm1.75-2l1.9-9.5 4.35 4.5L12 4.5l1.5 7.5 4.35-4.5 1.9 9.5H4.25zm7.75-10a1.25 1.25 0 100-2.5 1.25 1.25 0 000 2.5zm-6 2.5A1.25 1.25 0 106 7a1.25 1.25 0 000 2.5zm12 0a1.25 1.25 0 100-2.5 1.25 1.25 0 000 2.5z"/>
          </svg>
        </div>
        <div>
          <span class="block text-lg font-black tracking-tight text-white leading-none">SATKA MATKA</span>
          <span class="block text-[11px] font-medium text-purple-200 mt-1">Live Results &amp; Charts</span>
        </div>
      </a>

      <!-- Footer Nav Links -->
      <nav class="flex flex-wrap justify-center gap-x-8 gap-y-3 text-xs font-medium text-purple-200">
        <a href="/index.php" class="hover:text-white transition">Home</a>
        <a href="/markets.php" class="hover:text-white transition">All Markets</a>
        <a href="/results.php" class="hover:text-white transition">Latest Results</a>
        <a href="/chart.php" class="hover:text-white transition">Charts</a>
        <a href="/history.php" class="hover:text-white transition">History</a>
        <a href="/about.php" class="hover:text-white transition">About</a>
        <a href="/contact.php" class="hover:text-white transition">Contact</a>
      </nav>

      <!-- Social Media Icons matching reference -->
      <div class="flex items-center space-x-3 text-purple-200">
        <!-- Facebook -->
        <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-full bg-white/5 hover:bg-white/15 flex items-center justify-center transition" aria-label="Facebook">
          <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
        </a>
        <!-- YouTube -->
        <a href="https://youtube.com" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-full bg-white/5 hover:bg-white/15 flex items-center justify-center transition" aria-label="YouTube">
          <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
        </a>
        <!-- Telegram -->
        <a href="<?= e(Setting::get('telegram_channel', 'https://t.me/satkamatka_live')) ?>" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-full bg-white/5 hover:bg-white/15 flex items-center justify-center transition" aria-label="Telegram">
          <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
        </a>
        <!-- Globe -->
        <a href="/sitemap.php" class="w-8 h-8 rounded-full bg-white/5 hover:bg-white/15 flex items-center justify-center transition" aria-label="Sitemap">
          <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.477 2 12c0 5.523 4.477 10 10 10s10-4.477 10-10c0-5.523-4.477-10-10-10zm0 1.5c1.884 0 3.523 2.705 3.906 6.5H8.094C8.477 6.205 10.116 3.5 12 3.5zm-5.467 6.5c.348-2.672 1.488-4.839 2.978-5.753A8.528 8.528 0 0 0 3.655 10h2.878zm10.934 0h2.878a8.528 8.528 0 0 0-5.856-5.753c1.49.914 2.63 3.081 2.978 5.753zM3.5 12c0-.52.05-1.026.144-1.5h3.298c-.063.49-.096.99-.096 1.5 0 .51.033 1.01.096 1.5H3.644A8.448 8.448 0 0 1 3.5 12zm4.594 1.5c0-.493.033-.993.096-1.5h7.62c.063.507.096 1.007.096 1.5 0 .493-.033.993-.096 1.5H8.19c-.063-.507-.096-1.007-.096-1.5zm8.81 1.5h3.298c.094-.474.144-.98.144-1.5 0-.52-.05-1.026-.144-1.5h-3.298c.063.49.096.99.096 1.5 0 .51-.033 1.01-.096 1.5zm-3.41 6.5c-1.884 0-3.523-2.705-3.906-6.5h7.812c-.383 3.795-2.022 6.5-3.906 6.5zm-5.011-.747c-1.49-.914-2.63-3.081-2.978-5.753H3.655a8.528 8.528 0 0 0 5.856 5.753zm10.022 0a8.528 8.528 0 0 0 5.856-5.753h-2.878c-.348 2.672-1.488 4.839-2.978 5.753z"/></svg>
        </a>
      </div>

    </div>

    <!-- Bottom sub-footer -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 text-[11px] text-purple-300">
      <div class="flex items-center space-x-4">
        <a href="/privacy-policy.php" class="hover:underline">Privacy Policy</a>
        <span>|</span>
        <a href="/terms.php" class="hover:underline">Terms &amp; Conditions</a>
      </div>
      <div>
        <span>&copy; 2025 <?= e(Setting::get('site_name', 'Satka Matka')) ?>. All rights reserved.</span>
      </div>
    </div>
  </div>
</footer>

<script src="/assets/js/app.js"></script>
<script src="/assets/js/search.js"></script>
<script src="/assets/js/results.js"></script>
</body>
</html>
