// assets/js/checkout/checkout.page.js
(function () {
  const root = document.getElementById("checkout-root");
  if (!root) return;

  const S = window.DMMenuStorage; // menu.storage.js
  const U = window.DMMenuUtils;
  const cart = window.DMCart.readCart();

  const banner = document.getElementById("checkoutGateBanner");

  function hardBackToMenu(msg) {
    if (banner) banner.classList.remove("d-none");
    console.warn("[checkout gate]", msg);
    setTimeout(() => (window.location.href = "menu.php"), 400);
  }

  // --- Gate: require pre-checkout data ---
  if (!cart || !cart.length) return hardBackToMenu("missing cart");

  const sb = S?.getBranch?.();
  const loc = S?.getLoc?.();
  const mode = (S?.getMode?.() || sessionStorage.getItem("dm_order_mode_v1") || "").trim();

  if (!sb || !loc) return hardBackToMenu("missing branch/loc");
  if (mode !== "pickup" && mode !== "delivery") return hardBackToMenu("missing mode");

  // --- Branch display ---
  const branchesEl = document.getElementById("branches-data");
  let branches = [];
  try { branches = JSON.parse(branchesEl?.textContent || "[]"); } catch { branches = []; }

  const b = branches.find(x => String(x.branch_id) === String(sb.branch_id));
  document.getElementById("ui_branch_name").textContent = b?.name || sb.name || "Selected branch";
  document.getElementById("ui_branch_addr").textContent = b?.address || "—";
  document.getElementById("ui_mode").textContent = mode === "delivery" ? "Delivery" : "Pickup";
  document.getElementById("ui_loc").textContent = `${Number(loc.lat).toFixed(5)}, ${Number(loc.lng).toFixed(5)}`;

  // --- Toggle delivery/pickup fields ---
  const deliveryWrap = document.getElementById("deliveryFields");
  const pickupWrap = document.getElementById("pickupFields");
  const deliveryAddr = document.getElementById("delivery_address");

  if (mode === "delivery") {
    deliveryWrap?.classList.remove("d-none");
    pickupWrap?.classList.add("d-none");
    deliveryAddr?.setAttribute("required", "required");
  } else {
    pickupWrap?.classList.remove("d-none");
    deliveryWrap?.classList.add("d-none");
    deliveryAddr?.removeAttribute("required");
  }

  // --- Totals ---
  const subtotal = cart.reduce((s, it) => s + (Number(it.qty || 0) * Number(it.price || 0)), 0);

  // For now: delivery fee = 0.00 (you can compute later based on distance/area)
  const deliveryFee = 0;

  const total = subtotal + deliveryFee;

  // --- Build summary ---
  const summaryList = document.getElementById("summaryList");
  const itemCount = cart.reduce((s, it) => s + Number(it.qty || 0), 0);

  document.getElementById("ui_item_count").textContent = String(itemCount);
  document.getElementById("ui_subtotal").textContent = U.money(subtotal);
  document.getElementById("ui_total").textContent = U.money(total);

  const deliveryFeeEl = document.getElementById("ui_delivery_fee");
  if (deliveryFeeEl) deliveryFeeEl.textContent = U.money(deliveryFee);

  summaryList.innerHTML = cart.map(it => {
    const qty = Number(it.qty || 0);
    const price = Number(it.price || 0);
    const lineTotal = qty * price;

    const coffeeTag = it.allow_no_coffee
      ? (it.with_coffee ? "With coffee" : "No coffee")
      : "Coffee required";

    return `
      <div class="rounded-4 p-3" style="background: rgba(0,0,0,.22); border: 1px solid rgba(255,255,255,.12);">
        <div class="d-flex justify-content-between gap-3">
          <div>
            <div class="fw-bold">${U.escapeHtml(it.name)}</div>
            <div class="small text-white-50">${coffeeTag} • ₱${U.money(price)} × ${qty}</div>
          </div>
          <div class="fw-bold">₱${U.money(lineTotal)}</div>
        </div>
      </div>
    `;
  }).join("");

  // --- Fill hidden inputs for backend ---
  document.getElementById("h_branch_id").value = String(sb.branch_id);
  document.getElementById("h_order_mode").value = mode;
  document.getElementById("h_customer_lat").value = String(loc.lat);
  document.getElementById("h_customer_lng").value = String(loc.lng);
  document.getElementById("h_cart_json").value = JSON.stringify(cart);
  document.getElementById("h_subtotal").value = String(subtotal.toFixed(2));
  document.getElementById("h_delivery_fee").value = String(deliveryFee.toFixed(2));
  document.getElementById("h_total_amount").value = String(total.toFixed(2));

  // --- Form validation ---
  const form = document.getElementById("checkoutForm");
  const errBox = document.getElementById("checkoutError");

  form.addEventListener("submit", (e) => {
    if (errBox) {
      errBox.classList.add("d-none");
      errBox.textContent = "";
    }

    // HTML5 validity
    if (!form.checkValidity()) {
      e.preventDefault();
      e.stopPropagation();
      form.classList.add("was-validated");
      return;
    }

    // delivery guard
    if (mode === "delivery") {
      const val = (deliveryAddr?.value || "").trim();
      if (!val) {
        e.preventDefault();
        e.stopPropagation();
        deliveryAddr?.classList.add("is-invalid");
        if (errBox) {
          errBox.textContent = "Delivery address is required for delivery orders.";
          errBox.classList.remove("d-none");
        }
        return;
      }
    }

    // sanity: prevent empty cart submit
    const nowCart = window.DMCart.readCart();
    if (!nowCart || !nowCart.length) {
      e.preventDefault();
      if (errBox) {
        errBox.textContent = "Your cart is empty. Please go back to Menu.";
        errBox.classList.remove("d-none");
      }
      return;
    }

        // ✅ PayMongo intercept: create checkout session then redirect
    const pm = form.querySelector('input[name="payment_method"]:checked')?.value;
    if (pm === "paymongo") {
      e.preventDefault();
      e.stopPropagation();

      const fd = new FormData(form);

      // UI: disable button to prevent double clicks
      const btn = document.getElementById("btnPlaceOrder");
      if (btn) btn.disabled = true;

      fetch("api/payments/paymongo-checkout.php", { method: "POST", body: fd })
        .then(r => r.json().then(j => ({ ok: r.ok, j })))
        .then(({ ok, j }) => {
          if (!ok || j.status !== "ok" || !j.checkout_url) {
            throw new Error(j.message || "PayMongo checkout failed.");
          }
          window.location.href = j.checkout_url;
        })
        .catch(err => {
          if (btn) btn.disabled = false;
          if (errBox) {
            errBox.textContent = err.message;
            errBox.classList.remove("d-none");
          } else {
            alert(err.message);
          }
        });

      return;
    }

  });
})();
