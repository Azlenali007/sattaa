<?php
/**
 * Shared Header Navigation
 * Clean, accessible header with direct links, brand identity, and fast search
 */
$currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$activeHome = is_active_route('index.php') || $currentUri === '/' || $currentUri === '/index.php';
$activeMarkets = is_active_route('markets.php');
$activeResults = is_active_route('results.php');
$activeCharts = is_active_route('chart.php');
$activeHistory = is_active_route('history.php');
$activeSearch = is_active_route('search.php');
$activeAbout = is_active_route('about.php');
$activeContact = is_active_route('contact.php');
?>
<header class="bg-white border-b border-[#EAE8F5] sticky top-0 z-40 relative shadow-2xs">
  <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-20">
      
      <!-- Brand Logo -->
      <a href="<?= e(url('index.php')) ?>" class="flex items-center gap-3 group focus:outline-none focus:ring-2 focus:ring-[#6425D9] rounded-xl p-1">
        <div class="w-11 h-11 rounded-xl bg-[#6425D9] flex items-center justify-center text-white shadow-sm group-hover:scale-105 transition-transform shrink-0">
          <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
            <path d="M2.5 19h19v2.5H2.5V19zm1.75-2l1.9-9.5 4.35 4.5L12 4.5l1.5 7.5 4.35-4.5 1.9 9.5H4.25zm7.75-10a1.25 1.25 0 100-2.5 1.25 1.25 0 000 2.5zm-6 2.5A1.25 1.25 0 106 7a1.25 1.25 0 000 2.5zm12 0a1.25 1.25 0 100-2.5 1.25 1.25 0 000 2.5z"/>
          </svg>
        </div>
        <div>
          <span class="block text-xl font-black tracking-tight text-[#191747] leading-none">SATKA MATKA</span>
          <span class="block text-xs font-semibold text-[#737493] mt-1 tracking-wide">Live Results &amp; Charts</span>
        </div>
      </a>

      <!-- Desktop Direct Links -->
      <nav class="hidden lg:flex items-center space-x-6 text-xs font-bold" aria-label="Quick Navigation">
        <a href="<?= e(url('index.php')) ?>" class="py-2 transition <?= $activeHome ? 'text-[#6425D9]' : 'text-[#737493] hover:text-[#191747]' ?>">Home</a>
        <a href="<?= e(url('markets.php')) ?>" class="py-2 transition <?= $activeMarkets ? 'text-[#6425D9]' : 'text-[#737493] hover:text-[#191747]' ?>">Markets</a>
        <a href="<?= e(url('results.php')) ?>" class="py-2 transition <?= $activeResults ? 'text-[#6425D9]' : 'text-[#737493] hover:text-[#191747]' ?>">Latest Results</a>
        <a href="<?= e(url('chart.php')) ?>" class="py-2 transition <?= $activeCharts ? 'text-[#6425D9]' : 'text-[#737493] hover:text-[#191747]' ?>">Jodi Charts</a>
        <a href="<?= e(url('history.php')) ?>" class="py-2 transition <?= $activeHistory ? 'text-[#6425D9]' : 'text-[#737493] hover:text-[#191747]' ?>">History</a>
        <a href="<?= e(url('about.php')) ?>" class="py-2 transition <?= $activeAbout ? 'text-[#6425D9]' : 'text-[#737493] hover:text-[#191747]' ?>">About</a>
        <a href="<?= e(url('contact.php')) ?>" class="py-2 transition <?= $activeContact ? 'text-[#6425D9]' : 'text-[#737493] hover:text-[#191747]' ?>">Contact</a>
      </nav>

      <!-- Right Header Search & Admin Shortcut -->
      <div class="flex items-center space-x-3">
        <!-- Search Box -->
        <form action="<?= e(url('search.php')) ?>" method="GET" class="relative hidden sm:block">
          <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#737493]">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
          </span>
          <input 
            type="text" 
            name="q" 
            placeholder="Search market or date..." 
            class="w-48 lg:w-56 pl-9 pr-3 py-2 text-xs rounded-full bg-[#FAF9FF] border border-[#EAE8F5] text-[#191747] placeholder-[#737493] focus:outline-none focus:border-[#6425D9] focus:bg-white transition"
          >
        </form>

        <!-- Admin Portal Link -->
        <a href="<?= e(url('admin/login.php')) ?>" class="hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-[#EAE8F5] bg-[#FAF9FF] hover:bg-[#F3EDFF] text-[#6425D9] text-[11px] font-bold transition">
          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
          <span>Admin</span>
        </a>

        <!-- Mobile Drawer Toggle -->
        <button 
          id="mobile-menu-btn"
          type="button" 
          class="lg:hidden p-2 rounded-xl text-[#737493] hover:text-[#6425D9] hover:bg-[#F3EDFF] focus:outline-none"
          aria-label="Toggle Mobile Menu"
          aria-expanded="false"
        >
          <svg id="hamburger-icon" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
          <svg id="close-icon" class="w-6 h-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

    </div>
  </div>

  <!-- Mobile Menu Drawer (Direct links with real href attributes) -->
  <div 
    id="mobile-menu" 
    class="hidden lg:hidden border-t border-[#EAE8F5] bg-white px-4 pt-3 pb-6 space-y-1.5 shadow-lg max-h-[calc(100vh-5rem)] overflow-y-auto"
  >
    <a href="<?= e(url('index.php')) ?>" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold <?= $activeHome ? 'text-[#6425D9] bg-[#F3EDFF]' : 'text-[#191747] hover:bg-[#FAF9FF]' ?>">
      Home
    </a>
    <a href="<?= e(url('markets.php')) ?>" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold <?= $activeMarkets ? 'text-[#6425D9] bg-[#F3EDFF]' : 'text-[#191747] hover:bg-[#FAF9FF]' ?>">
      All Markets
    </a>
    <a href="<?= e(url('results.php')) ?>" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold <?= $activeResults ? 'text-[#6425D9] bg-[#F3EDFF]' : 'text-[#191747] hover:bg-[#FAF9FF]' ?>">
      Latest Results
    </a>
    <a href="<?= e(url('chart.php')) ?>" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold <?= $activeCharts ? 'text-[#6425D9] bg-[#F3EDFF]' : 'text-[#191747] hover:bg-[#FAF9FF]' ?>">
      Jodi Charts
    </a>
    <a href="<?= e(url('history.php')) ?>" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold <?= $activeHistory ? 'text-[#6425D9] bg-[#F3EDFF]' : 'text-[#191747] hover:bg-[#FAF9FF]' ?>">
      History
    </a>
    <a href="<?= e(url('search.php')) ?>" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold <?= $activeSearch ? 'text-[#6425D9] bg-[#F3EDFF]' : 'text-[#191747] hover:bg-[#FAF9FF]' ?>">
      Search
    </a>
    <a href="<?= e(url('about.php')) ?>" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold <?= $activeAbout ? 'text-[#6425D9] bg-[#F3EDFF]' : 'text-[#191747] hover:bg-[#FAF9FF]' ?>">
      About
    </a>
    <a href="<?= e(url('contact.php')) ?>" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold <?= $activeContact ? 'text-[#6425D9] bg-[#F3EDFF]' : 'text-[#191747] hover:bg-[#FAF9FF]' ?>">
      Contact
    </a>
    <a href="<?= e(url('admin/login.php')) ?>" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold text-[#6425D9] bg-[#FAF9FF] border border-[#EAE8F5] mt-2">
      Admin Portal &rarr;
    </a>

    <!-- Mobile Search Form -->
    <div class="pt-3">
      <form action="<?= e(url('search.php')) ?>" method="GET">
        <input 
          type="text" 
          name="q" 
          placeholder="Search market or date..." 
          class="w-full px-4 py-2.5 text-xs rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-[#191747] placeholder-[#737493] focus:outline-none focus:border-[#6425D9]"
        >
      </form>
    </div>
  </div>
</header>
