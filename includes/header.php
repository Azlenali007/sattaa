<?php
/**
 * Shared Header Navigation
 */
$currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
?>
<header class="bg-white border-b border-[#EAE8F5] sticky top-0 z-40">
  <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-20">
      
      <!-- Logo -->
      <a href="/index.php" class="flex items-center gap-3 group">
        <div class="w-11 h-11 rounded-xl bg-[#6425D9] flex items-center justify-center text-white shadow-sm group-hover:scale-105 transition-transform">
          <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
            <path d="M2.5 19h19v2.5H2.5V19zm1.75-2l1.9-9.5 4.35 4.5L12 4.5l1.5 7.5 4.35-4.5 1.9 9.5H4.25zm7.75-10a1.25 1.25 0 100-2.5 1.25 1.25 0 000 2.5zm-6 2.5A1.25 1.25 0 106 7a1.25 1.25 0 000 2.5zm12 0a1.25 1.25 0 100-2.5 1.25 1.25 0 000 2.5z"/>
          </svg>
        </div>
        <div>
          <span class="block text-xl font-black tracking-tight text-[#191747] leading-none">SATKA MATKA</span>
          <span class="block text-xs font-semibold text-[#737493] mt-1 tracking-wide">Live Results &amp; Charts</span>
        </div>
      </a>

      <!-- Desktop Navigation Menu -->
      <nav class="hidden xl:flex items-center space-x-7">
        <a href="/index.php" class="nav-link py-2 text-sm <?= ($currentUri === '/' || $currentUri === '/index.php') ? 'active' : '' ?>">Home</a>
        <a href="/markets.php" class="nav-link py-2 text-sm <?= str_contains($currentUri, 'markets') ? 'active' : '' ?>">All Markets</a>
        <a href="/results.php" class="nav-link py-2 text-sm <?= str_contains($currentUri, 'results') ? 'active' : '' ?>">Latest Results</a>
        <a href="/chart.php" class="nav-link py-2 text-sm <?= str_contains($currentUri, 'chart') ? 'active' : '' ?>">Charts</a>
        <a href="/history.php" class="nav-link py-2 text-sm <?= str_contains($currentUri, 'history') ? 'active' : '' ?>">History</a>
        <a href="/search.php" class="nav-link py-2 text-sm <?= str_contains($currentUri, 'search') ? 'active' : '' ?>">Search</a>
        <a href="/about.php" class="nav-link py-2 text-sm <?= str_contains($currentUri, 'about') ? 'active' : '' ?>">About</a>
        <a href="/contact.php" class="nav-link py-2 text-sm <?= str_contains($currentUri, 'contact') ? 'active' : '' ?>">Contact</a>
      </nav>

      <!-- Right Header Search Box -->
      <div class="hidden md:flex items-center relative">
        <form action="/search.php" method="GET" class="relative">
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
          class="p-2.5 rounded-lg text-[#737493] hover:text-[#6425D9] hover:bg-[#F3EDFF] focus:outline-none"
          aria-label="Toggle Navigation"
        >
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>

    </div>
  </div>

  <!-- Mobile Dropdown Menu -->
  <div id="mobile-menu" class="hidden xl:hidden border-t border-[#EAE8F5] bg-white px-4 pt-3 pb-5 space-y-1">
    <a href="/index.php" class="block px-3 py-2 rounded-lg text-sm font-semibold <?= ($currentUri === '/' || $currentUri === '/index.php') ? 'text-[#6425D9] bg-[#F3EDFF]' : 'text-[#737493]' ?>">Home</a>
    <a href="/markets.php" class="block px-3 py-2 rounded-lg text-sm font-semibold <?= str_contains($currentUri, 'markets') ? 'text-[#6425D9] bg-[#F3EDFF]' : 'text-[#737493]' ?>">All Markets</a>
    <a href="/results.php" class="block px-3 py-2 rounded-lg text-sm font-semibold <?= str_contains($currentUri, 'results') ? 'text-[#6425D9] bg-[#F3EDFF]' : 'text-[#737493]' ?>">Latest Results</a>
    <a href="/chart.php" class="block px-3 py-2 rounded-lg text-sm font-semibold <?= str_contains($currentUri, 'chart') ? 'text-[#6425D9] bg-[#F3EDFF]' : 'text-[#737493]' ?>">Charts</a>
    <a href="/history.php" class="block px-3 py-2 rounded-lg text-sm font-semibold <?= str_contains($currentUri, 'history') ? 'text-[#6425D9] bg-[#F3EDFF]' : 'text-[#737493]' ?>">History</a>
    <a href="/search.php" class="block px-3 py-2 rounded-lg text-sm font-semibold <?= str_contains($currentUri, 'search') ? 'text-[#6425D9] bg-[#F3EDFF]' : 'text-[#737493]' ?>">Search</a>
    <a href="/about.php" class="block px-3 py-2 rounded-lg text-sm font-semibold <?= str_contains($currentUri, 'about') ? 'text-[#6425D9] bg-[#F3EDFF]' : 'text-[#737493]' ?>">About</a>
    <a href="/contact.php" class="block px-3 py-2 rounded-lg text-sm font-semibold <?= str_contains($currentUri, 'contact') ? 'text-[#6425D9] bg-[#F3EDFF]' : 'text-[#737493]' ?>">Contact</a>
    <div class="pt-2">
      <form action="/search.php" method="GET">
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
