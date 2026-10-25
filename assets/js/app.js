/**
 * Satka Matka Frontend Application Scripts
 * Manages responsive navigation, dropdown menus, keyboard accessibility, and UI components
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Mobile Menu Toggle
  const mobileBtn = document.getElementById('mobile-menu-btn');
  const mobileMenu = document.getElementById('mobile-menu');
  const hamburgerIcon = document.getElementById('hamburger-icon');
  const closeIcon = document.getElementById('close-icon');

  if (mobileBtn && mobileMenu) {
    mobileBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      const isHidden = mobileMenu.classList.contains('hidden');
      if (isHidden) {
        mobileMenu.classList.remove('hidden');
        mobileBtn.setAttribute('aria-expanded', 'true');
        if (hamburgerIcon) hamburgerIcon.classList.add('hidden');
        if (closeIcon) closeIcon.classList.remove('hidden');
      } else {
        mobileMenu.classList.add('hidden');
        mobileBtn.setAttribute('aria-expanded', 'false');
        if (hamburgerIcon) hamburgerIcon.classList.remove('hidden');
        if (closeIcon) closeIcon.classList.add('hidden');
      }
    });

    // Close mobile menu on resize to desktop
    window.addEventListener('resize', () => {
      if (window.innerWidth >= 1280 && !mobileMenu.classList.contains('hidden')) {
        mobileMenu.classList.add('hidden');
        mobileBtn.setAttribute('aria-expanded', 'false');
        if (hamburgerIcon) hamburgerIcon.classList.remove('hidden');
        if (closeIcon) closeIcon.classList.add('hidden');
      }
    });
  }

  // 2. Desktop Dropdowns (Markets & Charts)
  const dropdownConfigs = [
    { btnId: 'nav-dropdown-markets-btn', menuId: 'nav-dropdown-markets' },
    { btnId: 'nav-dropdown-charts-btn', menuId: 'nav-dropdown-charts' }
  ];

  function closeAllDesktopDropdowns() {
    dropdownConfigs.forEach(({ btnId, menuId }) => {
      const btn = document.getElementById(btnId);
      const menu = document.getElementById(menuId);
      if (menu && !menu.classList.contains('hidden')) {
        menu.classList.add('hidden');
      }
      if (btn) {
        btn.setAttribute('aria-expanded', 'false');
        const arrow = btn.querySelector('.dropdown-arrow');
        if (arrow) arrow.style.transform = 'rotate(0deg)';
      }
    });
  }

  dropdownConfigs.forEach(({ btnId, menuId }) => {
    const btn = document.getElementById(btnId);
    const menu = document.getElementById(menuId);

    if (btn && menu) {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const isOpen = !menu.classList.contains('hidden');

        // Close other dropdowns first
        closeAllDesktopDropdowns();

        if (!isOpen) {
          menu.classList.remove('hidden');
          btn.setAttribute('aria-expanded', 'true');
          const arrow = btn.querySelector('.dropdown-arrow');
          if (arrow) arrow.style.transform = 'rotate(180deg)';
        }
      });

      // Keyboard support (Enter or Space)
      btn.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          btn.click();
        }
      });
    }
  });

  // 3. Mobile Accordion Sub-menus (Markets & Charts)
  const mobileAccordions = [
    { toggleId: 'mobile-markets-toggle', listId: 'mobile-markets-list' },
    { toggleId: 'mobile-charts-toggle', listId: 'mobile-charts-list' }
  ];

  mobileAccordions.forEach(({ toggleId, listId }) => {
    const toggle = document.getElementById(toggleId);
    const list = document.getElementById(listId);
    if (toggle && list) {
      toggle.addEventListener('click', (e) => {
        e.stopPropagation();
        const isClosed = list.classList.contains('hidden');
        list.classList.toggle('hidden');
        toggle.setAttribute('aria-expanded', isClosed ? 'true' : 'false');
        const arrow = toggle.querySelector('.mobile-arrow');
        if (arrow) {
          arrow.style.transform = isClosed ? 'rotate(180deg)' : 'rotate(0deg)';
        }
      });
    }
  });

  // 4. Global Outside Click and Escape Handler
  document.addEventListener('click', (e) => {
    // Check if clicked outside desktop dropdowns
    const isInsideDropdown = e.target.closest('.nav-dropdown-wrapper');
    if (!isInsideDropdown) {
      closeAllDesktopDropdowns();
    }

    // Check if clicked outside mobile menu
    if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
      const isInsideMobileMenu = mobileMenu.contains(e.target);
      const isMobileBtn = mobileBtn && mobileBtn.contains(e.target);
      if (!isInsideMobileMenu && !isMobileBtn) {
        mobileMenu.classList.add('hidden');
        if (mobileBtn) mobileBtn.setAttribute('aria-expanded', 'false');
        if (hamburgerIcon) hamburgerIcon.classList.remove('hidden');
        if (closeIcon) closeIcon.classList.add('hidden');
      }
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeAllDesktopDropdowns();
      if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
        mobileMenu.classList.add('hidden');
        if (mobileBtn) mobileBtn.setAttribute('aria-expanded', 'false');
        if (hamburgerIcon) hamburgerIcon.classList.remove('hidden');
        if (closeIcon) closeIcon.classList.add('hidden');
      }
    }
  });

  // 5. Auto-dismiss alerts after 5 seconds
  const flashAlerts = document.querySelectorAll('.flash-alert');
  flashAlerts.forEach((alert) => {
    setTimeout(() => {
      alert.style.transition = 'opacity 0.4s ease';
      alert.style.opacity = '0';
      setTimeout(() => alert.remove(), 400);
    }, 5000);
  });
});
