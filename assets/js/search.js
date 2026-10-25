/**
 * Search & Filter Functionality
 */

function setupLiveSearch(inputId, resultsContainerId) {
  const input = document.getElementById(inputId);
  const container = document.getElementById(resultsContainerId);
  if (!input || !container) return;

  let debounceTimer;

  input.addEventListener('input', (e) => {
    clearTimeout(debounceTimer);
    const query = e.target.value.trim();

    if (query.length < 2) {
      return;
    }

    debounceTimer = setTimeout(() => {
      fetch(`/api/search.php?q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
          if (data.results && data.results.length > 0) {
            // Render fast suggestions
            let html = '<div class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-xl border border-purple-100 z-50 overflow-hidden">';
            data.results.forEach(item => {
              html += `
                <a href="/market.php?id=${item.market_id}" class="flex items-center justify-between p-3.5 hover:bg-purple-50 transition border-b border-gray-100 last:border-b-0">
                  <div>
                    <div class="font-bold text-gray-900">${item.market_name}</div>
                    <div class="text-xs text-gray-500">${item.open_time} - ${item.close_time}</div>
                  </div>
                  <div class="font-mono font-bold text-purple-700 bg-purple-100 px-2.5 py-1 rounded-lg text-sm">
                    ${item.open_display || '--'} - ${item.close_display || '--'}
                  </div>
                </a>
              `;
            });
            html += '</div>';
            container.innerHTML = html;
          } else {
            container.innerHTML = '';
          }
        })
        .catch(() => {});
    }, 250);
  });

  // Close when clicking outside
  document.addEventListener('click', (e) => {
    if (!input.contains(e.target) && !container.contains(e.target)) {
      container.innerHTML = '';
    }
  });
}
