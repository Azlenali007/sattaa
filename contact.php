<?php
/**
 * Contact Us Page
 */
require_once __DIR__ . '/app/config/app.php';

$pageTitle = 'Contact Us';
$pageDescription = 'Get in touch with the Satka Matka administrative team for inquiries, corrections, or support.';

$submitted = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    enforce_csrf();
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $subject = sanitize_input($_POST['subject'] ?? '');
    $message = sanitize_input($_POST['message'] ?? '');

    $validator = new Validator($_POST);
    $validator->required('name')->required('email')->email('email')->required('message');

    if ($validator->passes()) {
        $submitted = true;
        set_flash('success', 'Thank you! Your message has been received. Our team will get back to you shortly.');
    } else {
        $error = $validator->firstError();
    }
}

require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
require __DIR__ . '/includes/flash-message.php';
?>

<main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
  <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-12 gap-8">
    
    <!-- Contact Info -->
    <div class="md:col-span-5 bg-[#191747] text-white rounded-3xl p-8 sm:p-10 flex flex-col justify-between">
      <div>
        <span class="text-xs font-bold uppercase tracking-wider text-purple-300 block mb-2">Get In Touch</span>
        <h1 class="text-3xl font-black mb-4">Contact Information</h1>
        <p class="text-xs text-purple-200 leading-relaxed mb-8">
          Reach out directly regarding market schedules, timing verifications, or technical questions.
        </p>

        <div class="space-y-4 text-xs">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center shrink-0">
              <svg class="w-4 h-4 fill-none stroke-current" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <div>
              <span class="text-purple-300 block text-[10px]">Email</span>
              <span class="font-bold"><?= e(Setting::get('contact_email', 'support@satkamatka.com')) ?></span>
            </div>
          </div>

          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center shrink-0">
              <svg class="w-4 h-4 fill-none stroke-current" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            </div>
            <div>
              <span class="text-purple-300 block text-[10px]">Phone</span>
              <span class="font-bold"><?= e(Setting::get('contact_phone', '+91 98765 43210')) ?></span>
            </div>
          </div>
        </div>
      </div>

      <div class="pt-8 border-t border-white/10 text-[11px] text-purple-300">
        Support operating hours: 09:00 AM – 11:30 PM daily.
      </div>
    </div>

    <!-- Contact Form -->
    <div class="md:col-span-7 bg-white rounded-3xl border border-[#EAE8F5] p-8 sm:p-10 shadow-xs">
      <h2 class="text-xl font-black text-[#191747] mb-6">Send an Inquiry</h2>

      <?php if ($error): ?>
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold mb-6">
          <?= e($error) ?>
        </div>
      <?php endif; ?>

      <?php if ($submitted): ?>
        <div class="p-6 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-center">
          <svg class="w-10 h-10 text-emerald-500 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <h3 class="text-sm font-bold">Message Submitted Successfully</h3>
          <p class="text-xs mt-1 text-emerald-700">Thank you for reaching out. We will review your message promptly.</p>
        </div>
      <?php else: ?>
        <form action="/contact.php" method="POST" class="space-y-4">
          <?= csrf_field() ?>
          <div>
            <label class="block text-xs font-bold text-[#191747] mb-1.5">Your Name *</label>
            <input type="text" name="name" required class="w-full px-4 py-3 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747] focus:outline-none focus:border-[#6425D9]">
          </div>
          <div>
            <label class="block text-xs font-bold text-[#191747] mb-1.5">Email Address *</label>
            <input type="email" name="email" required class="w-full px-4 py-3 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747] focus:outline-none focus:border-[#6425D9]">
          </div>
          <div>
            <label class="block text-xs font-bold text-[#191747] mb-1.5">Subject</label>
            <input type="text" name="subject" class="w-full px-4 py-3 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747] focus:outline-none focus:border-[#6425D9]">
          </div>
          <div>
            <label class="block text-xs font-bold text-[#191747] mb-1.5">Message *</label>
            <textarea name="message" rows="4" required class="w-full px-4 py-3 rounded-xl bg-[#FAF9FF] border border-[#EAE8F5] text-xs font-semibold text-[#191747] focus:outline-none focus:border-[#6425D9]"></textarea>
          </div>
          <button type="submit" class="w-full py-3.5 rounded-xl bg-[#6425D9] hover:bg-[#521ec2] text-white text-xs font-bold transition shadow-xs">
            Send Message
          </button>
        </form>
      <?php endif; ?>
    </div>

  </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
