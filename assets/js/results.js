/**
 * Live Results Refresh Service (AJAX polling)
 */

function initResultsPolling(intervalSeconds = 30) {
  setInterval(() => {
    fetch('/api/results.php')
      .then(res => res.json())
      .then(data => {
        if (data.success && data.cards) {
          // If elements on page have data-market-id, update their digits
          data.cards.forEach(card => {
            const el = document.querySelector(`[data-market-card="${card.market_id}"]`);
            if (el) {
              const openEl = el.querySelector('.market-open-display');
              const closeEl = el.querySelector('.market-close-display');
              if (openEl && card.open_display) openEl.textContent = card.open_display;
              if (closeEl && card.close_display) closeEl.textContent = card.close_display;
            }
          });
        }
      })
      .catch(() => {});
  }, intervalSeconds * 1000);
}
