<?php
/**
 * Slider-Style Public Navigation Menu
 * Responsive horizontal navigation slider with smooth transitions, touch/keyboard support, and active state tracking
 */

$currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$slug = $_GET['slug'] ?? '';
$isRoot = ($currentUri === '' || $currentUri === '/' || $currentUri === '/index.php' || str_ends_with($currentUri, '/index.php'));

$navItems = [
    [
        'label' => 'Home',
        'url' => url('index.php'),
        'active' => $isRoot && empty($slug),
        'icon' => '<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>'
    ],
    [
        'label' => 'All Markets',
        'url' => url('markets.php'),
        'active' => is_active_route('markets.php'),
        'icon' => '<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>'
    ],
    [
        'label' => 'Kalyan',
        'url' => url('market.php?slug=kalyan'),
        'active' => (str_contains($currentUri, 'market') || str_contains($currentUri, 'chart')) && $slug === 'kalyan',
        'badge' => 'Live',
        'icon' => '<span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse-dot"></span>'
    ],
    [
        'label' => 'Main Mumbai',
        'url' => url('market.php?slug=main-mumbai'),
        'active' => (str_contains($currentUri, 'market') || str_contains($currentUri, 'chart')) && $slug === 'main-mumbai',
        'badge' => 'Live',
        'icon' => '<span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse-dot"></span>'
    ],
    [
        'label' => 'Rajdhani',
        'url' => url('market.php?slug=rajdhani'),
        'active' => (str_contains($currentUri, 'market') || str_contains($currentUri, 'chart')) && $slug === 'rajdhani',
        'icon' => '<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>'
    ],
    [
        'label' => 'Sridevi',
        'url' => url('market.php?slug=sridevi'),
        'active' => (str_contains($currentUri, 'market') || str_contains($currentUri, 'chart')) && $slug === 'sridevi',
        'icon' => '<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
    ],
    [
        'label' => 'Milan Day',
        'url' => url('market.php?slug=milan-day'),
        'active' => (str_contains($currentUri, 'market') || str_contains($currentUri, 'chart')) && $slug === 'milan-day',
        'icon' => '<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>'
    ],
    [
        'label' => 'Milan Night',
        'url' => url('market.php?slug=milan-night'),
        'active' => (str_contains($currentUri, 'market') || str_contains($currentUri, 'chart')) && $slug === 'milan-night',
        'icon' => '<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>'
    ],
    [
        'label' => 'Latest Results',
        'url' => url('results.php'),
        'active' => is_active_route('results.php'),
        'icon' => '<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
    ],
    [
        'label' => 'Jodi Charts',
        'url' => url('chart.php'),
        'active' => is_active_route('chart.php') && empty($slug),
        'icon' => '<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>'
    ],
    [
        'label' => 'History',
        'url' => url('history.php'),
        'active' => is_active_route('history.php'),
        'icon' => '<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
    ],
    [
        'label' => 'Search',
        'url' => url('search.php'),
        'active' => is_active_route('search.php'),
        'icon' => '<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>'
    ],
    [
        'label' => 'About Us',
        'url' => url('about.php'),
        'active' => is_active_route('about.php'),
        'icon' => '<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
    ],
    [
        'label' => 'Contact',
        'url' => url('contact.php'),
        'active' => is_active_route('contact.php'),
        'icon' => '<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>'
    ]
];
?>

<!-- Slider-Style Navigation Bar -->
<div class="bg-white border-b border-[#EAE8F5] relative z-20 shadow-2xs select-none">
  <div class="max-w-[1440px] mx-auto px-2 sm:px-4 lg:px-8 py-2 relative flex items-center">
    
    <!-- Left Navigation Control -->
    <button 
      type="button" 
      id="nav-slider-prev" 
      aria-label="Slide Left"
      class="shrink-0 w-8 h-8 rounded-full bg-white border border-[#EAE8F5] text-[#191747] hover:text-[#6425D9] hover:bg-[#F3EDFF] shadow-xs flex items-center justify-center transition disabled:opacity-25 disabled:cursor-not-allowed z-10 mr-1 sm:mr-2 focus:outline-none focus:ring-2 focus:ring-[#6425D9]"
    >
      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
      </svg>
    </button>

    <!-- Horizontal Scroll Track -->
    <div 
      id="nav-slider-container" 
      class="flex-1 overflow-x-auto no-scrollbar scroll-smooth flex items-center gap-2 py-1 px-1 whitespace-nowrap focus:outline-none"
      role="navigation"
      aria-label="Slider Navigation Track"
      tabindex="0"
    >
      <?php foreach ($navItems as $item): ?>
        <a 
          href="<?= e($item['url']) ?>" 
          class="nav-slider-item <?= !empty($item['active']) ? 'active' : '' ?>"
          <?= !empty($item['active']) ? 'aria-current="page"' : '' ?>
        >
          <?= $item['icon'] ?>
          <span><?= e($item['label']) ?></span>
          <?php if (!empty($item['badge'])): ?>
            <span class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded-full <?= !empty($item['active']) ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-700' ?>">
              <?= e($item['badge']) ?>
            </span>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- Right Navigation Control -->
    <button 
      type="button" 
      id="nav-slider-next" 
      aria-label="Slide Right"
      class="shrink-0 w-8 h-8 rounded-full bg-white border border-[#EAE8F5] text-[#191747] hover:text-[#6425D9] hover:bg-[#F3EDFF] shadow-xs flex items-center justify-center transition disabled:opacity-25 disabled:cursor-not-allowed z-10 ml-1 sm:mr-0 sm:ml-2 focus:outline-none focus:ring-2 focus:ring-[#6425D9]"
    >
      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
      </svg>
    </button>

  </div>
</div>

<!-- Vanilla JS Slider Controller -->
<script>
(function() {
  function initNavSlider() {
    const container = document.getElementById('nav-slider-container');
    const prevBtn = document.getElementById('nav-slider-prev');
    const nextBtn = document.getElementById('nav-slider-next');

    if (!container || !prevBtn || !nextBtn) return;

    function updateArrowState() {
      const scrollLeft = Math.ceil(container.scrollLeft);
      const maxScroll = container.scrollWidth - container.clientWidth;
      prevBtn.disabled = scrollLeft <= 2;
      nextBtn.disabled = scrollLeft >= maxScroll - 2;
    }

    // Scroll step in pixels
    const step = 260;

    prevBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      container.scrollBy({ left: -step, behavior: 'smooth' });
    });

    nextBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      container.scrollBy({ left: step, behavior: 'smooth' });
    });

    // Keyboard navigation (Arrow keys on slider)
    container.addEventListener('keydown', function(e) {
      if (e.key === 'ArrowLeft') {
        container.scrollBy({ left: -100, behavior: 'smooth' });
      } else if (e.key === 'ArrowRight') {
        container.scrollBy({ left: 100, behavior: 'smooth' });
      }
    });

    // Mouse drag scrolling support
    let isDown = false;
    let startX = 0;
    let scrollStart = 0;
    let hasDragged = false;

    container.addEventListener('mousedown', function(e) {
      isDown = true;
      hasDragged = false;
      startX = e.pageX - container.offsetLeft;
      scrollStart = container.scrollLeft;
    });

    window.addEventListener('mouseup', function() {
      isDown = false;
      setTimeout(function() { hasDragged = false; }, 50);
    });

    container.addEventListener('mousemove', function(e) {
      if (!isDown) return;
      const x = e.pageX - container.offsetLeft;
      const walk = (x - startX);
      if (Math.abs(walk) > 5) {
        hasDragged = true;
        container.scrollLeft = scrollStart - walk;
      }
    });

    // Prevent accidental navigation if mouse dragged significantly
    container.querySelectorAll('a').forEach(function(link) {
      link.addEventListener('click', function(e) {
        if (hasDragged) {
          e.preventDefault();
        }
      });
    });

    container.addEventListener('scroll', updateArrowState, { passive: true });
    window.addEventListener('resize', updateArrowState, { passive: true });

    // Center the active element on initial load
    setTimeout(function() {
      const activeItem = container.querySelector('.nav-slider-item.active');
      if (activeItem) {
        activeItem.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
      }
      updateArrowState();
    }, 150);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNavSlider);
  } else {
    initNavSlider();
  }
})();
</script>
