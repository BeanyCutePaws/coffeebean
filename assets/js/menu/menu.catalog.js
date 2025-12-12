// assets/js/menu/menu.catalog.js
(function () {
  const U = window.DMMenuUtils;
  const S = window.DMMenuStorage;

  function isAvailableForBranch(availability, branchId, productId) {
    if (!branchId) return true;
    const b = availability[String(branchId)];
    if (!b) return true;
    const flag = b[String(productId)];
    if (typeof flag === "undefined") return true;
    return Number(flag) === 1;
  }

  function initCatalog(ctx) {
    const { products, availability, dom } = ctx;
    const { grid, tabs, searchInput } = dom;

    let currentCategory = "all";

    function renderGrid(categoryFilter) {
      if (!grid) return;

      const sb = S.getBranch();
      const branchId = sb ? sb.branch_id : null;
      const term = (searchInput?.value || "").trim().toLowerCase();

      const filtered = products.filter(p => {
        const catOk = (!categoryFilter || categoryFilter === "all")
          ? true
          : (String(p.category_id) === String(categoryFilter));
        if (!catOk) return false;

        if (term) {
          const hay = `${p.name||""} ${p.category_name||""} ${p.description||""}`.toLowerCase();
          if (!hay.includes(term)) return false;
        }
        return true;
      });

      grid.innerHTML = "";

      if (!filtered.length) {
        grid.innerHTML = `
          <div class="col-12">
            <div class="panel-card rounded-4 p-4 text-white-50">
              No items found.
            </div>
          </div>
        `;
        return;
      }

      filtered.forEach(p => {
        const ok = branchId ? isAvailableForBranch(availability, branchId, p.product_id) : true;
        const img = p.image_path ? String(p.image_path) : "assets/img/logo.png";

        const col = document.createElement("div");
        col.className = "col-12 col-sm-6 col-lg-4";

        col.innerHTML = `
          <div class="menu-item-card rounded-4 p-3 h-100">
            <div class="menu-item-imgwrap rounded-4 mb-3">
              <img src="${U.escapeHtml(img)}" alt="${U.escapeHtml(p.name)}">
            </div>

            <div class="d-flex justify-content-between align-items-start gap-2">
              <div>
                <div class="fw-bold">${U.escapeHtml(p.name)}</div>
                <div class="small text-white-50">${U.escapeHtml(p.category_name || "")}</div>
              </div>
              <div class="price-pill">₱${U.money(p.price)}</div>
            </div>

            ${p.description ? `<div class="small text-white-50 mt-2">${U.escapeHtml(p.description)}</div>` : ""}

            <button class="btn btn-primary w-100 mt-3 addToCartBtn"
                    data-product="${p.product_id}"
                    ${ok ? "" : "disabled"}>
              ${ok ? "Add to Cart" : "Unavailable for this branch"}
            </button>
          </div>
        `;

        grid.appendChild(col);
      });
    }

    // Tabs
    tabs.forEach(t => {
      t.addEventListener("click", () => {
        tabs.forEach(x => x.classList.remove("active"));
        t.classList.add("active");
        currentCategory = t.getAttribute("data-category") || "all";
        renderGrid(currentCategory);
      });
    });

    // Search
    searchInput?.addEventListener("input", () => renderGrid(currentCategory));

    return { renderGrid };
  }

  window.DMMenuCatalog = { initCatalog };
})();
