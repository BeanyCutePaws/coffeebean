// assets/js/admin-orders.js
document.addEventListener("DOMContentLoaded", () => {
  const area = document.getElementById("js-orders-area");
  const form = document.getElementById("ordersFilterForm");
  if (!area || !form || !window.ADMIN_ORDERS) return;

  const ajaxUrl = window.ADMIN_ORDERS.ajaxUrl;
  const nextUrl = window.ADMIN_ORDERS.nextStatusUrl;

  // Turn a URL into same URL but with ajax=1
  function withAjaxParam(url) {
    const u = new URL(url, window.location.origin);
    u.searchParams.set("ajax", "1");
    return u.toString();
  }

  // Replace only the orders area (no full refresh)
  async function refreshOrdersArea() {
    try {
      const url = withAjaxParam(window.location.href);
      const res = await fetch(url, { cache: "no-store" });
      const html = await res.text();
      area.innerHTML = html;
    } catch (_) {}
  }

  // Intercept pagination clicks so it doesn't full refresh
  document.addEventListener("click", async (e) => {
    const a = e.target.closest("#js-orders-area .pagination a");
    if (!a) return;
    e.preventDefault();

    try {
      const res = await fetch(withAjaxParam(a.href), { cache: "no-store" });
      const html = await res.text();
      area.innerHTML = html;

      // update browser URL (nice UX)
      window.history.pushState({}, "", a.href);
    } catch (_) {}
  });

  // Next status button handler (works for urgent + list)
  document.addEventListener("click", async (e) => {
    const btn = e.target.closest(".js-next-status");
    if (!btn) return;

    const orderId = btn.dataset.orderId;
    if (!orderId) return;

    btn.disabled = true;
    const oldText = btn.innerHTML;
    btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin me-1"></i> Updating...`;

    try {
      const body = new URLSearchParams({ order_id: orderId });
      const res = await fetch(nextUrl, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body
      });
      const data = await res.json();

      if (!data.success) {
        btn.innerHTML = oldText;
        btn.disabled = false;
        return;
      }

      // refresh list area right away so you SEE it change
      await refreshOrdersArea();
    } catch (_) {
      btn.innerHTML = oldText;
      btn.disabled = false;
    }
  });

  // Auto refresh every 5 seconds (keeps filters)
  setInterval(refreshOrdersArea, 5000);

  // If they submit filters, let it do normal page load (fine)
  // but if you want it ajax too later, we can intercept that.
});
