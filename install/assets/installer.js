/**
 * Satka Matka Web Installer JavaScript Helpers
 */

function togglePasswordVisibility(fieldId, btnEl) {
  const input = document.getElementById(fieldId);
  if (!input) return;
  if (input.type === 'password') {
    input.type = 'text';
    btnEl.textContent = 'Hide';
  } else {
    input.type = 'password';
    btnEl.textContent = 'Show';
  }
}

function evaluatePasswordStrength(password) {
  let score = 0;
  if (password.length >= 8) score += 25;
  if (password.length >= 12) score += 15;
  if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score += 25;
  if (/\d/.test(password)) score += 20;
  if (/[^a-zA-Z0-9]/.test(password)) score += 15;
  return Math.min(score, 100);
}

function updatePasswordStrengthMeter(inputId, meterBarId, textLabelId) {
  const input = document.getElementById(inputId);
  const bar = document.getElementById(meterBarId);
  const label = document.getElementById(textLabelId);
  if (!input || !bar) return;

  input.addEventListener('input', (e) => {
    const val = e.target.value;
    const score = evaluatePasswordStrength(val);
    bar.style.width = score + '%';

    if (score < 40) {
      bar.className = 'h-1.5 rounded-full transition-all duration-300 bg-rose-500';
      if (label) label.textContent = 'Weak';
    } else if (score < 75) {
      bar.className = 'h-1.5 rounded-full transition-all duration-300 bg-amber-500';
      if (label) label.textContent = 'Moderate';
    } else {
      bar.className = 'h-1.5 rounded-full transition-all duration-300 bg-emerald-500';
      if (label) label.textContent = 'Strong';
    }
  });
}

function testDatabaseConnectionAsync(formId, resultContainerId, btnId) {
  const form = document.getElementById(formId);
  const container = document.getElementById(resultContainerId);
  const btn = document.getElementById(btnId);
  if (!form || !container) return;

  const originalBtnText = btn ? btn.innerHTML : 'Test Connection';
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = `
      <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      Testing Connection...
    `;
  }

  container.innerHTML = `
    <div class="p-3.5 rounded-xl bg-purple-50 text-purple-700 text-xs font-semibold flex items-center gap-2">
      <span class="w-2 h-2 rounded-full bg-[#6425D9] animate-pulse"></span>
      <span>Attempting PDO connection to MySQL server...</span>
    </div>
  `;

  const formData = new FormData(form);
  formData.append('ajax_test', '1');

  fetch('/install/database.php', {
    method: 'POST',
    body: formData,
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
  .then(res => res.json())
  .then(data => {
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = originalBtnText;
    }

    if (data.success) {
      container.innerHTML = `
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs">
          <div class="font-bold flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            <span>${data.message}</span>
          </div>
          <p class="text-[11px] text-emerald-700 mt-1">${data.details || ''}</p>
        </div>
      `;
      const submitBtn = document.getElementById('btn-submit-db');
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
      }
    } else {
      container.innerHTML = `
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
          <div class="font-bold flex items-center gap-2">
            <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <span>${data.message}</span>
          </div>
          <p class="text-[11px] text-rose-700 mt-1">${data.details || ''}</p>
        </div>
      `;
    }
  })
  .catch(err => {
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = originalBtnText;
    }
    container.innerHTML = `
      <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
        Connection test failed: Unable to reach installer backend.
      </div>
    `;
  });
}
