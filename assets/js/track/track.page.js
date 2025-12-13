// assets/js/track/track.page.js
document.addEventListener('DOMContentLoaded', () => {
  const wrapper = document.getElementById('js-track-status-wrapper');
  if (!wrapper) return;

  // Auto-refresh ONLY the results wrapper
  function refreshTrack() {
    fetch(window.location.href, { cache: 'no-store' })
      .then(r => r.text())
      .then(html => {
        const doc = new DOMParser().parseFromString(html, 'text/html');
        const fresh = doc.getElementById('js-track-status-wrapper');
        if (fresh) wrapper.innerHTML = fresh.innerHTML;
      })
      .catch(() => {});
  }

  setInterval(refreshTrack, 5000);

  // Double-confirm cancel
  let cancelArmed = false;

  document.addEventListener('click', (e) => {
    const btn = e.target.closest('#btnCancelOrder');
    if (!btn) return;

    const hint = document.getElementById('cancelHint');
    const form = btn.closest('form');
    if (!form) return;

    if (!cancelArmed) {
      cancelArmed = true;
      if (hint) hint.style.display = 'block';

      btn.classList.remove('btn-outline-danger');
      btn.classList.add('btn-danger');
      btn.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-2"></i>Confirm Cancel';

      setTimeout(() => {
        cancelArmed = false;
        if (hint) hint.style.display = 'none';
        btn.classList.remove('btn-danger');
        btn.classList.add('btn-outline-danger');
        btn.innerHTML = '<i class="fa-solid fa-ban me-2"></i>Cancel Order';
      }, 5000);
    } else {
      form.submit();
    }
  });
});
