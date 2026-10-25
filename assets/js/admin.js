/**
 * Admin Panel Helpers
 */

function confirmAction(message, formId) {
  if (confirm(message)) {
    document.getElementById(formId).submit();
  }
  return false;
}

function copyToClipboard(text) {
  navigator.clipboard.writeText(text).then(() => {
    alert('Copied to clipboard!');
  }).catch(() => {
    prompt('Copy this value:', text);
  });
}
