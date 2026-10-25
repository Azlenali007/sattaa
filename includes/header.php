<?php
/**
 * Shared Header Navigation
 * Production-ready white and purple header with interactive dropdowns & responsive mobile drawer
 */
$currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$activeHome = is_active_route('index.php') || $currentUri === '/' || $currentUri === '/index.php';
$activeMarkets = is_active_route('markets.php') || str_contains($currentUri, 'market');
$activeResults = is_active_route('results.php');
$activeCharts = is_active_route('chart.php') || str_contains($currentUri, 'chart');
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

      <!-- Desktop Navigation Menu with Interactive Dropdowns -->
      <nav class="hidden xl:flex items-center space-x-6 text-sm" aria-label="Main Navigation">
        
        <!-- Home Link -->
        <a href="<?= e(url('index.php')) ?>" class="nav-link py-2 <?= $activeHome ? 'active' : '' ?>">Home</a>

        <!-- Markets Dropdown Menu -->
        <div class="relative nav-dropdown-wrapper">
          <button 
            type="button" 
            id="nav-dropdown-markets-btn"
            class="nav-link py-2 flex items-center gap-1.5 focus:outline-none <?= $activeMarkets ? 'active' : '' ?>"
            aria-expanded="false"
            aria-haspopup="true"
          >
            <span>Markets</span>
            <svg class="w-4 h-4 text-[#737493] transition-transform duration-200 dropdown-arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>

          <!-- Dropdown Panel -->
          <div 
            id="nav-dropdown-markets" 
            class="hidden absolute top-full left-0 w-64 mt-2 bg-white rounded-2xl shadow-xl border border-[#EAE8F5] py-2 z-50 transition-all"
            role="menu"
            aria-labelledby="nav-dropdown-markets-btn"
          >
            <div class="px-4 py-2 border-b border-[#FAF9FF]">
              <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#6425D9]">Popular Markets</span>
            </div>
            <div class="py-1">
              <a href="<?= e(url('market.php?slug=kalyan')) ?>" class="flex items-center justify-between px-4 py-2 text-xs text-[#191747] hover:bg-[#F3EDFF] hover:text-[#6425D9] transition font-semibold" role="menuitem">
                <span>Kalyan</span>
                <span class="text-[10px] text-[#737493] font-normal">10:00 AM - 12:00 PM</span>
              </a>
              <a href="<?= e(url('market.php?slug=main-mumbai')) ?>" class="flex items-center justify-between px-4 py-2 text-xs text-[#191747] hover:bg-[#F3EDFF] hover:text-[#6425D9] transition font-semibold" role="menuitem">
                <span>Main Mumbai</span>
                <span class="text-[10px] text-[#737493] font-normal">11:00 AM - 01:00 PM</span>
              </a>
              <a href="<?= e(url('market.php?slug=rajdhani')) ?>" class="flex items-center justify-between px-4 py-2 text-xs text-[#191747] hover:bg-[#F3EDFF] hover:text-[#6425D9] transition font-semibold" role="menuitem">
                <span>Rajdhani</span>
                <span class="text-[10px] text-[#737493] font-normal">01:00 PM - 03:00 PM</span>
              </a>
              <a href="<?= e(url('market.php?slug=sridevi')) ?>" class="flex items-center justify-between px-4 py-2 text-xs text-[#191747] hover:bg-[#F3EDFF] hover:text-[#6425D9] transition font-semibold" role="menuitem">
                <span>Sridevi</span>
                <span class="text-[10px] text-[#737493] font-normal">03:00 PM - 05:00 PM</span>
              </a>
              <a href="<?= e(url('market.php?slug=milan-day')) ?>" class="flex items-center justify-between px-4 py-2 text-xs text-[#191747] hover:bg-[#F3EDFF] hover:text-[#6425D9] transition font-semibold" role="menuitem">
                <span>Milan Day</span>
                <span class="text-[10px] text-[#737493] font-normal">05:00 PM - 07:00 PM</span>
              </a>
              <a href="<?= e(url('market.php?slug=milan-night')) ?>" class="flex items-center justify-between px-4 py-2 text-xs text-[#191747] hover:bg-[#F3EDFF] hover:text-[#6425D9] transition font-semibold" role="menuitem">
                <span>Milan Night</span>
                <span class="text-[10px] text-[#737493] font-normal">09:00 PM - 11:00 PM</span>
              </a>
            </div>
            <div class="px-3 pt-2 mt-1 border-t border-[#EAE8F5]">
              <a href="<?= e(url('markets.php')) ?>" class="block w-full py-2 px-3 rounded-xl bg-[#FAF9FF] hover:bg-[#F3EDFF] text-[#6425D9] font-bold text-xs text-center transition" role="menuitem">
                View All Markets &rarr;
              </a>
            </div>
          </div>
        </div>

        <!-- Latest Results Link -->
        <a href="<?= e(url('results.php')) ?>" class="nav-link py-2 <?= $activeResults ? 'active' : '' ?>">Latest Results</a>

        <!-- Charts Dropdown Menu -->
        <div class="relative nav-dropdown-wrapper">
          <button 
            type="button" 
            id="nav-dropdown-charts-btn"
            class="nav-link py-2 flex items-center gap-1.5 focus:outline-none <?= $activeCharts ? 'active' : '' ?>"
            aria-expanded="false"
            aria-haspopup="true"
          >
            <span>Charts</span>
            <svg class="w-4 h-4 text-[#737493] transition-transform duration-200 dropdown-arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>

          <!-- Dropdown Panel -->
          <div 
            id="nav-dropdown-charts" 
            class="hidden absolute top-full left-0 w-64 mt-2 bg-white rounded-2xl shadow-xl border border-[#EAE8F5] py-2 z-50 transition-all"
            role="menu"
            aria-labelledby="nav-dropdown-charts-btn"
          >
            <div class="px-4 py-2 border-b border-[#FAF9FF]">
              <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#6425D9]">Historical Charts</span>
            </div>
            <div class="py-1">
              <a href="<?= e(url('chart.php?slug=kalyan')) ?>" class="block px-4 py-2 text-xs text-[#191747] hover:bg-[#F3EDFF] hover:text-[#6425D9] transition font-semibold" role="menuitem">
                Kalyan Jodi Chart
              </a>
              <a href="<?= e(url('chart.php?slug=main-mumbai')) ?>" class="block px-4 py-2 text-xs text-[#191747] hover:bg-[#F3EDFF] hover:text-[#6425D9] transition font-semibold" role="menuitem">
                Main Mumbai Chart
              </a>
              <a href="<?= e(url('chart.php?slug=rajdhani')) ?>" class="block px-4 py-2 text-xs text-[#191747] hover:bg-[#F3EDFF] hover:text-[#6425D9] transition font-semibold" role="menuitem">
                Rajdhani Chart
              </a>
              <a href="<?= e(url('chart.php?slug=milan-day')) ?>" class="block px-4 py-2 text-xs text-[#191747] hover:bg-[#F3EDFF] hover:text-[#6425D9] transition font-semibold" role="menuitem">
                Milan Day Chart
              </a>
              <a href="<?= e(url('chart.php?slug=milan-night')) ?>" class="block px-4 py-2 text-xs text-[#191747] hover:bg-[#F3EDFF] hover:text-[#6425D9] transition font-semibold" role="menuitem">
                Milan Night Chart
              </a>
            </div>
            <div class="px-3 pt-2 mt-1 border-t border-[#EAE8F5]">
              <a href="<?= e(url('chart.php')) ?>" class="block w-full py-2 px-3 rounded-xl bg-[#FAF9FF] hover:bg-[#F3EDFF] text-[#6425D9] font-bold text-xs text-center transition" role="menuitem">
                All Market Charts &rarr;
              </a>
            </div>
          </div>
        </div>

        <!-- History Link -->
        <a href="<?= e(url('history.php')) ?>" class="nav-link py-2 <?= $activeHistory ? 'active' : '' ?>">History</a>

        <!-- Search Link -->
        <a href="<?= e(url('search.php')) ?>" class="nav-link py-2 <?= $activeSearch ? 'active' : '' ?>">Search</a>

        <!-- About Link -->
        <a href="<?= e(url('about.php')) ?>" class="nav-link py-2 <?= $activeAbout ? 'active' : '' ?>">About</a>

        <!-- Contact Link -->
        <a href="<?= e(url('contact.php')) ?>" class="nav-link py-2 <?= $activeContact ? 'active' : '' ?>">Contact</a>

      </nav>

      <!-- Right Header Search Box -->
      <div class="hidden md:flex items-center relative">
        <form action="<?= e(url('search.php')) ?>" method="GET" class="relative">
          <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#737493]">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
          </span>
          <input 
            type="text" 
            name="q" 
            placeholder="Search market or date..." 
            class="w-56 lg:w-64 pl-10 pr-4 py-2 text-xs rounded-full bg-[#FAF9FF] border border-[#EAE8F5] text-[#191747] placeholder-[#737493] focus:outline-none focus:border-[#6425D9] focus:bg-white transition"
          >
        </form>
      </div>

      <!-- Mobile Hamburger Button -->
      <div class="flex items-center xl:hidden">
        <button 
          id="mobile-menu-btn"
          type="button" 
          class="p-2.5 rounded-xl text-[#737493] hover:text-[#6425D9] hover:bg-[#F3EDFF] focus:outline-none focus:ring-2 focus:ring-[#6425D9]"
          aria-label="Toggle Navigation Menu"
          aria-expanded="false"
          aria-controls="mobile-menu"
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

  <!-- Mobile Navigation Drawer -->
  <div 
    id="mobile-menu" 
    class="hidden xl:hidden border-t border-[#EAE8F5] bg-white px-4 pt-3 pb-6 space-y-1.5 shadow-lg max-h-[calc(100vh-5rem)] overflow-y-auto"
  >
    <!-- Direct Links -->
    <a href="<?= e(url('index.php')) ?>" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold <?= $activeHome ? 'text-[#6425D9] bg-[#F3EDFF]' : 'text-[#191747] hover:bg-[#FAF9FF]' ?>">
      Home
    </a>

    <!-- Mobile Markets Accordion -->
    <div class="rounded-xl border border-[#EAE8F5] overflow-hidden">
      <button 
        type="button" 
        id="mobile-markets-toggle"
        class="w-full flex items-center justify-between px-3.5 py-2.5 text-xs font-bold text-[#191747] bg-[#FAF9FF] hover:bg-[#F3EDFF] transition"
        aria-expanded="false"
      >
        <span class="flex items-center gap-2">
          <span>All Markets</span>
          <span class="text-[10px] font-semibold text-[#6425D9] bg-[#F3EDFF] px-2 py-0.5 rounded-full">6</span>
        </span>
        <svg class="w-4 h-4 text-[#737493] transition-transform duration-200 mobile-arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>
      <div id="mobile-markets-list" class="hidden px-2 py-1.5 bg-white space-y-1 border-t border-[#EAE8F5]">
        <a href="<?= e(url('market.php?slug=kalyan')) ?>" class="block px-3 py-1.5 rounded-lg text-xs font-semibold text-[#737493] hover:text-[#6425D9] hover:bg-[#FAF9FF]">Kalyan</a>
        <a href="<?= e(url('market.php?slug=main-mumbai')) ?>" class="block px-3 py-1.5 rounded-lg text-xs font-semibold text-[#737493] hover:text-[#6425D9] hover:bg-[#FAF9FF]">Main Mumbai</a>
        <a href="<?= e(url('market.php?slug=rajdhani')) ?>" class="block px-3 py-1.5 rounded-lg text-xs font-semibold text-[#737493] hover:text-[#6425D9] hover:bg-[#FAF9FF]">Rajdhani</a>
        <a href="<?= e(url('market.php?slug=sridevi')) ?>" class="block px-3 py-1.5 rounded-lg text-xs font-semibold text-[#737493] hover:text-[#6425D9] hover:bg-[#FAF9FF]">Sridevi</a>
        <a href="<?= e(url('market.php?slug=milan-day')) ?>" class="block px-3 py-1.5 rounded-lg text-xs font-semibold text-[#737493] hover:text-[#6425D9] hover:bg-[#FAF9FF]">Milan Day</a>
        <a href="<?= e(url('market.php?slug=milan-night')) ?>" class="block px-3 py-1.5 rounded-lg text-xs font-semibold text-[#737493] hover:text-[#6425D9] hover:bg-[#FAF9FF]">Milan Night</a>
        <a href="<?= e(url('markets.php')) ?>" class="block px-3 py-2 rounded-lg text-xs font-bold text-[#6425D9] bg-[#F3EDFF] mt-1 text-center">View All Markets &rarr;</a>
      </div>
    </div>

    <!-- Latest Results Link -->
    <a href="<?= e(url('results.php')) ?>" class="block px-3.5 py-2.5 rounded-xl text-xs font-bold <?= $activeResults ? 'text-[#6425D9] bg-[#F3EDFF]' : 'text-[#191747] hover:bg-[#FAF9FF]' ?>">
      Latest Results
    </a>

    <!-- Mobile Charts Accordion -->
    <div class="rounded-xl border border-[#EAE8F5] overflow-hidden">
      <button 
        type="button" 
        id="mobile-charts-toggle"
        class="w-full flex items-center justify-between px-3.5 py-2.5 text-xs font-bold text-[#191747] bg-[#FAF9FF] hover:bg-[#F3EDFF] transition"
        aria-expanded="false"
      >
        <span>Historical Charts</span>
        <svg class="w-4 h-4 text-[#737493] transition-transform duration-200 mobile-arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>
      <div id="mobile-charts-list" class="hidden px-2 py-1.5 bg-white space-y-1 border-t border-[#EAE8F5]">
        <a href="<?= e(url('chart.php?slug=kalyan')) ?>" class="block px-3 py-1.5 rounded-lg text-xs font-semibold text-[#737493] hover:text-[#6425D9] hover:bg-[#FAF9FF]">Kalyan Jodi Chart</a>
        <a href="<?= e(url('chart.php?slug=main-mumbai')) ?>" class="block px-3 py-1.5 rounded-lg text-xs font-semibold text-[#737493] hover:text-[#6425D9] hover:bg-[#FAF9FF]">Main Mumbai Chart</a>
        <a href="<?= e(url('chart.php?slug=rajdhani')) ?>" class="block px-3 py-1.5 rounded-lg text-xs font-semibold text-[#737493] hover:text-[#6425D9] hover:bg-[#FAF9FF]">Rajdhani Chart</a>
        <a href="<?= e(url('chart.php?slug=milan-day')) ?>" class="block px-3 py-1.5 rounded-lg text-xs font-semibold text-[#737493] hover:text-[#6425D9] hover:bg-[#FAF9FF]">Milan Day Chart</a>
        <a href="<?= e(url('chart.php?slug=milan-night')) ?>" class="block px-3 py-1.5 rounded-lg text-xs font-semibold text-[#737493] hover:text-[#6425D9] hover:bg-[#FAF9FF]">Milan Night Chart</a>
        <a href="<?= e(url('chart.php')) ?>" class="block px-3 py-2 rounded-lg text-xs font-bold text-[#6425D9] bg-[#F3EDFF] mt-1 text-center">All Historical Charts &rarr;</a>
      </div>
    </div>

    <!-- History, Search, About, Contact -->
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
