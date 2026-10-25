<?php
/**
 * Shared Head Include
 */
require_once __DIR__ . '/../app/config/app.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <?php require __DIR__ . '/seo-meta.php'; ?>
  
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

  <!-- Alpine.js -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

  <!-- Custom App Styles -->
  <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="bg-[#FAF9FF] text-[#191747] min-h-screen flex flex-col antialiased">
