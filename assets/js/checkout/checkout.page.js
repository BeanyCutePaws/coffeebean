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

  // --- COD OTP UI refs ---
  const codOtpBlock = document.getElementById("codOtpBlock");
  const codEmail = document.getElementById("cod_email");
  const codEmailErr = document.getElementById("cod_email_err");
  const btnSendCodOtp = document.getElementById("btnSendCodOtp");
  const codOtpStatus = document.getElementById("codOtpStatus");
  const codOtpVerifyWrap = document.getElementById("codOtpVerifyWrap");
  const codOtpCode = document.getElementById("cod_otp_code");
  const btnVerifyCodOtp = document.getElementById("btnVerifyCodOtp");
  const codVerifyStatus = document.getElementById("codVerifyStatus");
  const hEmail = document.getElementById("h_customer_email");
  const hOtpVerified = document.getElementById("h_otp_verified");

  function isValidEmail(v) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(v || "").trim());
  }

  function setOtpVerified(ok) {
    hOtpVerified.value = ok ? "1" : "0";
    if (ok) {
      codVerifyStatus.textContent = "Verified. You can place the order.";
      codVerifyStatus.className = "small text-success mt-2";
    }
  }

  function selectedPayment() {
    const el = document.querySelector('input[name="payment_method"]:checked');
    return el ? String(el.value) : "cod";
  }

  function refreshPaymentUI() {
    const pm = selectedPayment();
    // reset OTP whenever payment method changes
    setOtpVerified(false);
    if (hEmail) hEmail.value = "";
    if (pm === "cod") {
      codOtpBlock?.classList.remove("d-none");
    } else {
      codOtpBlock?.classList.add("d-none");
    }
  }

  document.querySelectorAll('input[name="payment_method"]').forEach(r => {
    r.addEventListener("change", refreshPaymentUI);
  });
  refreshPaymentUI();

  async function postFormUrlEncoded(url, data) {
    const body = new URLSearchParams();
    Object.keys(data || {}).forEach(k => body.append(k, data[k]));
    const res = await fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body
    });
    return res.json();
  }

  btnSendCodOtp?.addEventListener("click", async () => {
    const email = String(codEmail?.value || "").trim();

    codEmail?.classList.remove("is-invalid");
    codOtpStatus.textContent = "";
    codOtpStatus.className = "small text-white-50";

    if (!isValidEmail(email)) {
      codEmail?.classList.add("is-invalid");
      if (codEmailErr) codEmailErr.style.display = "block";
      codOtpStatus.textContent = "Enter a valid email first.";
      codOtpStatus.className = "small text-danger";
      return;
    }

    codOtpStatus.textContent = "Sending code...";
    codOtpStatus.className = "small text-white-50";

    try {
      const data = await postFormUrlEncoded("api/otp/send-email-otp.php", { email });
      if (data && data.success) {
        codOtpStatus.textContent = data.message || "Code sent. Check your email.";
        codOtpStatus.className = "small text-success";
        codOtpVerifyWrap?.classList.remove("d-none");
        if (hEmail) hEmail.value = email;
      } else {
        codOtpStatus.textContent = data?.message || "Failed to send code. Try again.";
        codOtpStatus.className = "small text-danger";
      }
    } catch (e) {
      codOtpStatus.textContent = "Network error while sending code.";
      codOtpStatus.className = "small text-danger";
    }
  });

  btnVerifyCodOtp?.addEventListener("click", async () => {
    const email = String(codEmail?.value || "").trim();
    const code = String(codOtpCode?.value || "").trim();

    codVerifyStatus.textContent = "";
    codVerifyStatus.className = "small text-white-50 mt-2";

    if (!isValidEmail(email)) {
      codVerifyStatus.textContent = "Please enter a valid email.";
      codVerifyStatus.className = "small text-danger mt-2";
      return;
    }
    if (!/^\d{6}$/.test(code)) {
      codVerifyStatus.textContent = "Enter the 6-digit code.";
      codVerifyStatus.className = "small text-danger mt-2";
      return;
    }

    codVerifyStatus.textContent = "Verifying...";
    codVerifyStatus.className = "small text-white-50 mt-2";

    try {
      const data = await postFormUrlEncoded("api/otp/verify-email-otp.php", { email, code });
      if (data && data.success) {
        if (hEmail) hEmail.value = email;
        setOtpVerified(true);
      } else {
        setOtpVerified(false);
        codVerifyStatus.textContent = data?.message || "Incorrect code.";
        codVerifyStatus.className = "small text-danger mt-2";
      }
    } catch (e) {
      setOtpVerified(false);
      codVerifyStatus.textContent = "Network error while verifying code.";
      codVerifyStatus.className = "small text-danger mt-2";
    }
  });

  // --- Form validation + COD OTP gate ---
  const form = document.getElementById("checkoutForm");
  const errBox = document.getElementById("checkoutError");

  form.addEventListener("submit", (e) => {
    if (errBox) {
      errBox.classList.add("d-none");
      errBox.textContent = "";
    }

    if (!form.checkValidity()) {
      e.preventDefault();
      e.stopPropagation();
      form.classList.add("was-validated");
      return;
    }

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

    const nowCart = window.DMCart.readCart();
    if (!nowCart || !nowCart.length) {
      e.preventDefault();
      if (errBox) {
        errBox.textContent = "Your cart is empty. Please go back to Menu.";
        errBox.classList.remove("d-none");
      }
      return;
    }

    const pm = selectedPayment();

    // COD requires OTP verification before submit
    if (pm === "cod") {
      const ok = String(hOtpVerified.value || "0") === "1";
      const email = String(hEmail.value || "").trim();

      if (!ok || !isValidEmail(email)) {
        e.preventDefault();
        if (errBox) {
          errBox.textContent = "Cash orders require email OTP verification. Please verify first.";
          errBox.classList.remove("d-none");
        }
        codOtpBlock?.classList.remove("d-none");
        return;
      }
    }

    // PayMongo: no OTP gate here
  });

})();
