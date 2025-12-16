// assets/js/track/track.page.js
(function () {
  const wrapper = document.getElementById("js-track-status-wrapper");
  if (!wrapper) return;

  const live = document.getElementById("js-track-live");
  // Only run auto-refresh when there's a live panel (i.e., after a successful search)
  if (!live) return;

  async function postFormUrlEncoded(url, data) {
    const body = new URLSearchParams();
    Object.keys(data || {}).forEach((k) => body.append(k, data[k]));
    const res = await fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body,
      // credentials: "include", // keep commented unless API is cross-subdomain
    });
    return res.json();
  }

  function isValidEmail(v) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(v || "").trim());
  }

  function setCancelUiAllowed(isAllowed, statusText) {
    const pendingWrap = document.getElementById("cancelPendingWrap");
    const lockedWrap = document.getElementById("cancelLockedWrap");
    const lockedText = document.getElementById("cancelLockedText");

    const emailEl = document.getElementById("cancel_email");
    const sendBtn = document.getElementById("btnSendCancelOtp");
    const codeEl = document.getElementById("cancel_otp_code");
    const verifyBtn = document.getElementById("btnVerifyCancelOtp");
    const cancelFinalBtn = document.getElementById("btnCancelOrderFinal");

    if (pendingWrap && lockedWrap) {
      if (isAllowed) {
        pendingWrap.classList.remove("d-none");
        lockedWrap.classList.add("d-none");
      } else {
        pendingWrap.classList.add("d-none");
        lockedWrap.classList.remove("d-none");
      }
    }

    if (lockedText) lockedText.textContent = statusText || "";

    // hard-disable buttons/inputs if not allowed
    const disabled = !isAllowed;
    if (emailEl) emailEl.disabled = disabled;
    if (sendBtn) sendBtn.disabled = disabled;
    if (codeEl) codeEl.disabled = disabled;
    if (verifyBtn) verifyBtn.disabled = disabled;
    if (cancelFinalBtn) cancelFinalBtn.disabled = true; // always lock until OTP verify
  }

  function attachCancelOtpHandlers() {
    const emailEl = document.getElementById("cancel_email");
    const hEmailEl = document.getElementById("h_cancel_email");
    const sendBtn = document.getElementById("btnSendCancelOtp");
    const statusEl = document.getElementById("cancelOtpStatus");

    const verifyWrap = document.getElementById("cancelVerifyWrap");
    const codeEl = document.getElementById("cancel_otp_code");
    const verifyBtn = document.getElementById("btnVerifyCancelOtp");
    const verifyStatus = document.getElementById("cancelVerifyStatus");

    const cancelFinalBtn = document.getElementById("btnCancelOrderFinal");

    if (
      !emailEl ||
      !sendBtn ||
      !statusEl ||
      !verifyWrap ||
      !codeEl ||
      !verifyBtn ||
      !verifyStatus ||
      !cancelFinalBtn ||
      !hEmailEl
    ) {
      return;
    }

    // ✅ prevent double-binding if called again
    if (sendBtn.dataset.bound === "1") return;
    sendBtn.dataset.bound = "1";
    verifyBtn.dataset.bound = "1";

    // keep hidden email in sync
    function syncEmail() {
      hEmailEl.value = String(emailEl.value || "").trim();
      cancelFinalBtn.disabled = true; // lock again if email changes
      verifyStatus.textContent = "";
    }
    emailEl.addEventListener("input", syncEmail);
    syncEmail();

    sendBtn.addEventListener("click", async () => {
      const email = String(emailEl.value || "").trim();
      syncEmail();

      statusEl.textContent = "";
      statusEl.className = "small text-white-50";

      if (!isValidEmail(email)) {
        statusEl.textContent = "Enter a valid email first.";
        statusEl.className = "small text-danger mt-2";
        return;
      }

      statusEl.textContent = "Sending code...";
      statusEl.className = "small text-white-50 mt-2";

      try {
        const data = await postFormUrlEncoded("api/otp/send-email-otp.php", { email, scope: "cancel" })
        if (data && data.success) {
          statusEl.textContent = data.message || "Code sent. Check your email.";
          statusEl.className = "small text-success mt-2";
          verifyWrap.classList.remove("d-none");
        } else {
          statusEl.textContent = data?.message || "Failed to send code.";
          statusEl.className = "small text-danger mt-2";
        }
      } catch (e) {
        statusEl.textContent = "Network error while sending code.";
        statusEl.className = "small text-danger mt-2";
      }
    });

    verifyBtn.addEventListener("click", async () => {
      const email = String(emailEl.value || "").trim();
      const code = String(codeEl.value || "").trim();
      syncEmail();

      if (!isValidEmail(email)) {
        verifyStatus.textContent = "Please enter a valid email.";
        verifyStatus.className = "small text-danger mt-2";
        cancelFinalBtn.disabled = true;
        return;
      }
      if (!/^\d{6}$/.test(code)) {
        verifyStatus.textContent = "Enter the 6-digit code.";
        verifyStatus.className = "small text-danger mt-2";
        cancelFinalBtn.disabled = true;
        return;
      }

      verifyStatus.textContent = "Verifying...";
      verifyStatus.className = "small text-white-50 mt-2";
      cancelFinalBtn.disabled = true;

      try {
        const data = await postFormUrlEncoded("api/otp/verify-email-otp.php", { email, code, scope: "cancel" })
        if (data && data.success) {
          verifyStatus.textContent = "Verified. You can now cancel the order.";
          verifyStatus.className = "small text-success mt-2";
          cancelFinalBtn.disabled = false;
        } else {
          verifyStatus.textContent = data?.message || "Incorrect code.";
          verifyStatus.className = "small text-danger mt-2";
          cancelFinalBtn.disabled = true;
        }
      } catch (e) {
        verifyStatus.textContent = "Network error while verifying code.";
        verifyStatus.className = "small text-danger mt-2";
        cancelFinalBtn.disabled = true;
      }
    });
  }

  async function refreshTrackOrder() {
    try {
      const res = await fetch(window.location.href, { cache: "no-store" });
      const html = await res.text();

      const doc = new DOMParser().parseFromString(html, "text/html");
      const newLive = doc.getElementById("js-track-live");

      if (newLive) {
        live.innerHTML = newLive.innerHTML;

        // update cancel allowed state based on new status
        const newStatus = newLive.getAttribute("data-order-status") || "";
        const allowed = newStatus === "pending";
        setCancelUiAllowed(
          allowed,
          allowed ? "" : `Cancellation is only available while the order is Pending. Current status: ${newStatus || "—"}`
        );
      }
    } catch (e) {
      // silent
    }
  }

  // ✅ attach OTP handlers once (cancel area is NOT replaced anymore)
  attachCancelOtpHandlers();

  // initial toggle based on current status in live container
  const initialStatus = live.getAttribute("data-order-status") || "";
  setCancelUiAllowed(
    initialStatus === "pending",
    initialStatus === "pending"
      ? ""
      : `Cancellation is only available while the order is Pending. Current status: ${initialStatus || "—"}`
  );

  setInterval(refreshTrackOrder, 5000);
})();
