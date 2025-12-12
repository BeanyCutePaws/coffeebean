// assets/js/menu/menu.checkout-gate.js
(function () {
  const S = window.DMMenuStorage;

  function fail(dom, msg) {
    if (!dom?.gateMsg) return;
    dom.gateMsg.textContent = msg;
    dom.gateMsg.classList.remove("d-none");
  }

  function validateCart(cart) {
    if (!Array.isArray(cart) || cart.length === 0) return "Cart is empty.";

    for (const it of cart) {
      const pid = Number(it?.product_id);
      const qty = Number(it?.qty);
      const price = Number(it?.price);

      if (!Number.isFinite(pid) || pid <= 0) return "Invalid cart item. Please re-add your items.";
      if (!Number.isFinite(qty) || qty <= 0) return "Invalid quantity in cart. Please re-add your items.";
      if (!Number.isFinite(price) || price < 0) return "Invalid price in cart. Please re-add your items.";
    }
    return "";
  }

  async function proceedToCheckout(ctx) {
    const { recaptchaEnabled, dom } = ctx;
    const { btnProceed } = dom;

    dom?.gateMsg?.classList.add("d-none");

    // --- Cart checks ---
    const cart = window.DMCart.readCart();
    const cartErr = validateCart(cart);
    if (cartErr) return fail(dom, cartErr);

    // --- Location + branch checks ---
    const sb = S.getBranch(); // {branch_id, name}
    const loc = S.getLoc();   // {lat,lng}
    if (!sb || !loc) return fail(dom, "Please pin your location and confirm a branch first.");

    const branchId = Number(sb.branch_id);
    if (!Number.isFinite(branchId) || branchId <= 0) return fail(dom, "Invalid branch selected. Please select again.");

    const lat = Number(loc.lat), lng = Number(loc.lng);
    if (!Number.isFinite(lat) || !Number.isFinite(lng)) return fail(dom, "Invalid pinned location. Please pin again.");

    // --- Mode check (pickup/delivery) ---
    const mode = S.getMode ? S.getMode() : (sessionStorage.getItem("dm_order_mode_v1") || "");
    if (mode !== "pickup" && mode !== "delivery") {
      return fail(dom, "Please choose Pickup or Delivery first.");
    }

    // If your location module forced pickup, make sure mode matches
    // (If you store forcedPickup in storage, check it here; otherwise enforce minimal rule)
    if (mode === "delivery") {
      // If menu UI allowed delivery, okay. If not, user could have stale session.
      // safest: require that delivery is still allowed by checking a stored flag, if you have it.
      // If you don't store it, keep delivery allowed here and enforce again in checkout.php.
    }

    // --- reCAPTCHA gate ---
    if (!recaptchaEnabled) {
      window.location.href = "checkout.php";
      return;
    }

    const token = (window.grecaptcha && window.grecaptcha.getResponse) ? window.grecaptcha.getResponse() : "";
    if (!token) return fail(dom, "Please complete the reCAPTCHA before proceeding.");

    try {
      if (btnProceed) {
        btnProceed.disabled = true;
        btnProceed.textContent = "Verifying...";
      }

      const res = await fetch("actions/verify-recaptcha.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ token })
      });

      const out = await res.json();

      // Recommended: verify endpoint should set a PHP session flag
      // so checkout.php can refuse direct visits when recaptcha is enabled.
      if (out && out.ok) {
        window.location.href = "checkout.php";
        return;
      }

      fail(dom, (out && out.message) ? out.message : "reCAPTCHA verification failed.");
      if (window.grecaptcha && window.grecaptcha.reset) window.grecaptcha.reset();

    } catch (err) {
      console.error(err);
      fail(dom, "Network error. Please try again.");
    } finally {
      if (btnProceed) {
        btnProceed.disabled = false;
        btnProceed.textContent = "Proceed to Checkout";
      }
    }
  }

  function initCheckoutGate(ctx) {
    const { dom } = ctx;
    dom.btnProceed?.addEventListener("click", () => proceedToCheckout(ctx));
  }

  window.DMMenuCheckoutGate = { initCheckoutGate };
})();
