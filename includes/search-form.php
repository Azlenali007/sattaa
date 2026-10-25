<?php
/**
 * Hero Search Form Component
 */
?>
<div class="relative max-w-xl w-full" id="hero-search-wrapper">
  <form action="/search.php" method="GET" class="relative flex items-center">
    <input 
      type="text" 
      id="hero-search-input"
      name="q" 
      placeholder="Search market, date or jodi..." 
      value="<?= e($_GET['q'] ?? '') ?>"
      class="w-full pl-6 pr-14 py-4 rounded-full bg-white border border-[#EAE8F5] text-sm text-[#191747] placeholder-[#737493] focus:outline-none focus:border-[#6425D9] shadow-sm transition"
      autocomplete="off"
    >
    <button 
      type="submit" 
      class="absolute right-2 top-2 bottom-2 w-11 h-11 bg-[#6425D9] hover:bg-[#521ec2] text-white rounded-full flex items-center justify-center shadow-sm transition-transform active:scale-95"
      aria-label="Search"
    >
      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
      </svg>
    </button>
  </form>
  <div id="hero-search-suggestions"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  setupLiveSearch('hero-search-input', 'hero-search-suggestions');
});
</script>
