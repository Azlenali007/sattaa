<?php
/**
 * Shared Installer Header
 */
require_once __DIR__ . '/installer-functions.php';
installer_init();
check_installer_guard();

$pageTitle = $pageTitle ?? 'Installation Wizard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= e($pageTitle) ?> | SATKA MATKA Installation Wizard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">

  <!-- Inter Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: {
              primary: '#6425D9',
              deep: '#35156F',
              light: '#F3EDFF',
              main: '#191747',
              muted: '#737493',
              border: '#EAE8F5',
              green: '#16A779',
              soft: '#FAF9FF'
            }
          },
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
          }
        }
      }
    }
  </script>

  <!-- Installer Styles & Scripts -->
  <link rel="stylesheet" href="/install/assets/installer.css">
  <script src="/install/assets/installer.js"></script>
</head>
<body class="bg-[#FAF9FF] text-[#191747] min-h-screen flex flex-col antialiased font-sans">

  <!-- Top Brand Navigation Bar -->
  <header class="bg-white border-b border-[#EAE8F5] py-4 px-6 sm:px-10 sticky top-0 z-30 shadow-xs">
    <div class="max-w-5xl mx-auto flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-[#6425D9] flex items-center justify-center text-white shadow-xs">
          <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
            <path d="M2.5 19h19v2.5H2.5V19zm1.75-2l1.9-9.5 4.35 4.5L12 4.5l1.5 7.5 4.35-4.5 1.9 9.5H4.25zm7.75-10a1.25 1.25 0 100-2.5 1.25 1.25 0 000 2.5zm-6 2.5A1.25 1.25 0 106 7a1.25 1.25 0 000 2.5zm12 0a1.25 1.25 0 100-2.5 1.25 1.25 0 000 2.5z"/>
          </svg>
        </div>
        <div>
          <span class="block text-base font-black text-[#191747] leading-none">SATKA MATKA</span>
          <span class="block text-[11px] font-bold text-[#6425D9] mt-0.5 tracking-wide uppercase">Web Installation Wizard</span>
        </div>
      </div>

      <div class="hidden sm:flex items-center gap-2 text-xs font-semibold text-[#737493]">
        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
        <span>Production Installer v1.0</span>
      </div>
    </div>
  </header>

  <!-- Main Wizard Container -->
  <main class="max-w-4xl mx-auto px-4 sm:px-6 py-8 flex-1 w-full">
    <?php require __DIR__ . '/progress.php'; ?>
