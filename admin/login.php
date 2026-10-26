<?php
/**
 * Super Admin Login
 * Authenticates against MySQL admins table using password_verify()
 */
require_once __DIR__ . '/../app/config/app.php';

// If already logged in, redirect to dashboard
if (!empty($_SESSION['admin_user'])) {
    redirect('/admin/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    enforce_csrf();
    $username = trim($_POST['username'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Both username and password are required.';
    } else {
        $admin = Admin::authenticate($username, $password);
        if ($admin) {
            session_regenerate_id(true);
            $_SESSION['admin_user'] = [
                'id' => (int)$admin['id'],
                'username' => $admin['username'],
                'email' => $admin['email'],
                'role' => $admin['role']
            ];
            Admin::logActivity($admin['id'], 'Login', 'Auth', null, 'Super Admin logged in successfully');
            set_flash('success', 'Welcome back, Administrator!');

            $redirectUrl = $_GET['redirect'] ?? '';
            if (empty($redirectUrl) || $redirectUrl === '/admin' || $redirectUrl === '/admin/' || $redirectUrl === '/admin/index.php' || $redirectUrl === '/admin/login.php' || !str_starts_with($redirectUrl, '/admin/')) {
                $redirectUrl = 'admin/dashboard.php';
            }
            redirect($redirectUrl);
        } else {
            $error = 'Invalid administrative credentials or account locked.';
        }
    }
}

$pageTitle = 'Super Admin Login';
require __DIR__ . '/includes/head.php';
?>

<div class="min-h-screen w-full flex items-center justify-center p-4 bg-[#FAF9FF]">
  <div class="max-w-md w-full bg-white rounded-3xl border border-[#EAE8F5] p-8 sm:p-10 shadow-lg">
    <div class="text-center mb-8">
      <div class="w-14 h-14 rounded-2xl bg-[#6425D9] text-white flex items-center justify-center mx-auto mb-4 shadow-md">
        <svg class="w-7 h-7 fill-current" viewBox="0 0 24 24">
          <path d="M2.5 19h19v2.5H2.5V19zm1.75-2l1.9-9.5 4.35 4.5L12 4.5l1.5 7.5 4.35-4.5 1.9 9.5H4.25zm7.75-10a1.25 1.25 0 100-2.5 1.25 1.25 0 000 2.5zm-6 2.5A1.25 1.25 0 106 7a1.25 1.25 0 000 2.5zm12 0a1.25 1.25 0 100-2.5 1.25 1.25 0 000 2.5z"/>
        </svg>
      </div>
      <h1 class="text-2xl font-black text-[#191747]">Super Admin Portal</h1>
      <p class="text-xs text-[#737493] mt-1">Authenticate to access master management center</p>
    </div>

    <?php if ($error): ?>
      <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold mb-6 flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span><?= e($error) ?></span>
      </div>
    <?php endif; ?>

    <form action="<?= e(url('admin/login.php' . (!empty($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''))) ?>" method="POST" class="space-y-4">
      <?= csrf_field() ?>
      <div>
        <label class="block text-xs font-bold text-[#191747] mb-1.5">Username or Email</label>
        <input 
          type="text" 
          name="username" 
          value="<?= e($_POST['username'] ?? '') ?>"
          required 
          autocomplete="username"
          placeholder="admin or admin@satkamatka.com"
          class="w-full px-4 py-3 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747] focus:outline-none focus:border-[#6425D9]"
        >
      </div>

      <div>
        <div class="flex items-center justify-between mb-1.5">
          <label class="block text-xs font-bold text-[#191747]">Password</label>
        </div>
        <div class="relative">
          <input 
            type="password" 
            id="admin-login-pass"
            name="password" 
            required 
            autocomplete="current-password"
            placeholder="••••••••••••"
            class="w-full px-4 py-3 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747] focus:outline-none focus:border-[#6425D9] pr-14"
          >
          <button 
            type="button" 
            onclick="const p = document.getElementById('admin-login-pass'); if(p.type==='password'){p.type='text'; this.textContent='Hide';}else{p.type='password'; this.textContent='Show';}" 
            class="absolute right-3 top-3 text-[11px] font-bold text-[#6425D9] hover:underline"
          >
            Show
          </button>
        </div>
      </div>

      <button 
        type="submit" 
        class="w-full py-3.5 rounded-xl bg-[#6425D9] hover:bg-[#521ec2] text-white text-xs font-bold tracking-wide transition shadow-sm mt-2"
      >
        Sign In to Control Center
      </button>
    </form>

    <div class="mt-8 pt-6 border-t border-[#EAE8F5] text-center">
      <a href="<?= e(url('index.php')) ?>" class="text-xs font-semibold text-[#6425D9] hover:underline">
        &larr; Back to Public Website
      </a>
    </div>
  </div>
</div>
</body>
</html>
