document.addEventListener("DOMContentLoaded", () => {
  const cfg = window.ADMIN_MENU;
  if (!cfg) return;

  document.addEventListener("change", async (e) => {
    const sw = e.target.closest(".js-toggle-item");
    if (!sw) return;

    sw.disabled = true;

    try {
      const body = new URLSearchParams({
        product_id: sw.dataset.productId,
        branch_id: cfg.branchId,
        enabled: sw.checked ? 1 : 0
      });

      const res = await fetch(cfg.toggleUrl, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body
      });

      const json = await res.json();
      if (!json.success) throw new Error();
    } catch {
      sw.checked = !sw.checked;
    } finally {
      sw.disabled = false;
    }
  });
});
