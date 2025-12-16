// assets/js/admin-order-view.js
document.addEventListener("DOMContentLoaded", () => {
  const cfg = window.ADMIN_ORDER_VIEW || {};
  const wrap = document.getElementById("js-order-view-wrapper");
  if (!wrap || !cfg.ajaxUrl || !cfg.nextStatusUrl) return;

  async function refreshView() {
    try {
      const u = new URL(cfg.ajaxUrl, window.location.href);
      u.searchParams.set("ajax", "1");

      const res = await fetch(u.toString(), {
        cache: "no-store",
        headers: { "X-Requested-With": "XMLHttpRequest" },
        credentials: "same-origin"
      });

      wrap.innerHTML = await res.text();
    } catch (e) {}
  }

  async function postNextStatus(orderId, btn) {
    const oldHtml = btn ? btn.innerHTML : "";
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin me-2"></i>Updating...`;
    }

    try {
      const body = new URLSearchParams({ order_id: String(orderId) });

      const res = await fetch(cfg.nextStatusUrl, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body,
        credentials: "same-origin"
      });

      const data = await res.json();
      if (!data || !data.success) throw new Error(data?.message || "Update failed");

      await refreshView();
    } catch (e) {
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = oldHtml;
      }
      console.warn("[order-view] next status failed:", e.message || e);
    }
  }

  async function postCancel(orderId, btn) {
    if (!cfg.cancelUrl) {
      console.warn("[order-view] cancelUrl missing");
      return;
    }

    const ok = confirm("Cancel this order?\n\nThis will set status to CANCELLED and add a history entry.");
    if (!ok) return;

    const oldHtml = btn ? btn.innerHTML : "";
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin me-2"></i>Cancelling...`;
    }

    try {
      const body = new URLSearchParams({
        order_id: String(orderId),
        note: "Cancelled by admin"
      });

      const res = await fetch(cfg.cancelUrl, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body,
        credentials: "same-origin"
      });

      const data = await res.json();
      if (!data || !data.success) throw new Error(data?.message || "Cancel failed");

      await refreshView();
    } catch (e) {
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = oldHtml;
      }
      console.warn("[order-view] cancel failed:", e.message || e);
      alert(e?.message || "Cancel failed");
    }
  }

  // Click handler for Next Status (inside wrapper)
  document.addEventListener("click", (e) => {
    const btn = e.target.closest("#js-order-view-wrapper .js-next-status");
    if (!btn) return;

    const orderId = btn.dataset.orderId;
    if (!orderId) return;

    postNextStatus(orderId, btn);
  });

  // Click handler for Cancel (inside wrapper)
  document.addEventListener("click", (e) => {
    const btn = e.target.closest("#js-order-view-wrapper .js-cancel-order");
    if (!btn) return;

    const orderId = btn.dataset.orderId;
    if (!orderId) return;

    postCancel(orderId, btn);
  });

  // Auto-refresh like Orders page
  setInterval(refreshView, 5000);
});
