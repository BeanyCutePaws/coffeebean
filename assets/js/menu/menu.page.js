// assets/js/menu/menu.page.js
(function () {
  const dataEl = document.getElementById("menu-data");
  if (!dataEl) return;

  let payload = null;
  try { payload = JSON.parse(dataEl.textContent || "{}"); }
  catch (e) { console.error("menu-data parse failed", e); return; }

  const products     = payload.products || [];
  const branches     = payload.branches || [];
  const availability = payload.availability || {};
  const recaptchaEnabled = !!(payload.recaptcha && payload.recaptcha.enabled);

  const dom = {
    // menu
    grid: document.getElementById("menuGrid"),
    tabs: Array.from(document.querySelectorAll(".menu-tab")),
    searchInput: document.getElementById("menuSearch"),
    selectedBranchLabel: document.getElementById("selectedBranchLabel"),

    menuWrap: document.getElementById("menuWrap"),
    menuLockedWrap: document.getElementById("menuLockedWrap"),



    // location / branch
    mapEl: document.getElementById("leafletMap"),
    btnUseMyLocation: document.getElementById("btnUseMyLocation"),
    nearestNameEl: document.getElementById("nearestBranchName"),
    nearestDistEl: document.getElementById("nearestBranchDist"),
    orderModeBadge: document.getElementById("orderModeBadge"),
    branchResults: document.getElementById("branchResults"),
    btnConfirmBranch: document.getElementById("btnConfirmBranch"),
    branchConfirmMsg: document.getElementById("branchConfirmMsg"),

    // cart panel (SIDE)
    cartEmpty: document.getElementById("cartEmpty"),
    cartList: document.getElementById("cartList"),
    cartSummary: document.getElementById("cartSummary"),
    cartSubtotal: document.getElementById("cartSubtotal"),

    // checkout button + msg
    btnProceed: document.getElementById("btnProceedCheckout"),
    gateMsg: document.getElementById("checkoutGateMsg"),
  };

  // Catalog
  const catalog = window.DMMenuCatalog.initCatalog({
    products,
    availability,
    dom: { grid: dom.grid, tabs: dom.tabs, searchInput: dom.searchInput }
  });

  // Location module (unlocks menu)
  window.DMMenuLocation.initLocationUI({
    branches,
    dom,
    onBranchConfirmed: () => {
      catalog.renderGrid("all");
      updateCartUI();
    }
  });

  // --- CART UI (side panel) ---
  function updateCartUI() {
    const cart = window.DMCart.readCart();

    if (!dom.cartList || !dom.cartEmpty || !dom.cartSummary) return;

    dom.cartList.innerHTML = "";

    if (!cart.length) {
      dom.cartEmpty.classList.remove("d-none");
      dom.cartList.classList.add("d-none");
      dom.cartSummary.classList.add("d-none");

      // button should not proceed
        if (dom.btnProceed) {
            dom.btnProceed.disabled = !cart.length;
            dom.btnProceed.textContent = recaptchaEnabled ? "Proceed (reCAPTCHA required)" : "Proceed to Checkout";
        }
      return;
    }

    dom.cartEmpty.classList.add("d-none");
    dom.cartList.classList.remove("d-none");
    dom.cartSummary.classList.remove("d-none");

    let subtotal = 0;

    cart.forEach((it, idx) => {
      const price = Number(it.price || 0);
      const qty = Number(it.qty || 1);
      const itemTotal = price * qty;
      subtotal += itemTotal;

      dom.cartList.innerHTML += `
        <div class="cart-card">
          <div class="d-flex justify-content-between align-items-start gap-3">
            <div class="flex-grow-1">
              <div class="cart-title">${window.DMMenuUtils.escapeHtml(it.name)}</div>
              <div class="cart-meta mt-1">
                <span>₱${price.toFixed(2)}</span>
                <span>Item total: <strong>₱${itemTotal.toFixed(2)}</strong></span>
              </div>

              ${
                it.allow_no_coffee
                  ? `
                    <label class="coffee-switch">
                      <input type="checkbox" class="coffeeToggle" data-idx="${idx}" ${it.with_coffee ? "checked" : ""}>
                      <span>${it.with_coffee ? "With coffee" : "No coffee"}</span>
                    </label>
                  `
                  : `<div class="small text-white-50 mt-2">Coffee required</div>`
              }
            </div>

            <div class="text-end">
              <div class="cart-qty">
                <button class="cartDec" data-idx="${idx}" type="button">
                    ${qty <= 1 ? "🗑" : "−"}
                </button>
                <strong>${qty}</strong>
                <button class="cartInc" data-idx="${idx}" type="button">+</button>
              </div>
            </div>
          </div>
        </div>
      `;
    });

    if (dom.cartSubtotal) dom.cartSubtotal.textContent = subtotal.toFixed(2);
    
    // re-bind controls
    dom.cartList.querySelectorAll(".cartInc").forEach(btn =>
      btn.addEventListener("click", () => {
        window.DMCart.inc(Number(btn.dataset.idx));
        updateCartUI();
      })
    );

    dom.cartList.querySelectorAll(".cartDec").forEach(btn =>
        btn.addEventListener("click", () => {
            const i = Number(btn.dataset.idx);
            const cart = window.DMCart.readCart();

            if (!cart[i]) return;

            // If qty is 1 → remove item
            if (Number(cart[i].qty) <= 1) {
            window.DMCart.remove(i);
            } else {
            window.DMCart.dec(i);
            }

            updateCartUI();
        })
    );

    dom.cartList.querySelectorAll(".coffeeToggle").forEach(inp =>
      inp.addEventListener("change", () => {
        const i = Number(inp.dataset.idx);
        window.DMCart.setCoffee(i, inp.checked);
        updateCartUI();
      })
    );
  }

  // Add-to-cart
  document.addEventListener("click", (e) => {
    const btn = e.target.closest(".addToCartBtn");
    if (!btn) return;

    const id = btn.getAttribute("data-product");
    const p = products.find(x => String(x.product_id) === String(id));
    if (!p) return;

    window.DMCart.addItem({
      product_id: p.product_id,
      name: p.name,
      price: p.price,
      image_path: p.image_path || "",
      allow_no_coffee: Number(p.allow_no_coffee || 0) === 1,
    }, 1);

    updateCartUI();

    // optional: scroll cart panel into view on mobile
    document.getElementById("cartPanel")?.scrollIntoView({ behavior: "smooth", block: "start" });
  });

  // Initial
  updateCartUI();

  // ✅ Initialize checkout gate (server-verified reCAPTCHA + required storage checks)
window.DMMenuCheckoutGate?.initCheckoutGate({
  recaptchaEnabled,
  dom
});

})();
