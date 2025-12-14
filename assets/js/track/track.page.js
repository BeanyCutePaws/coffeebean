// assets/js/track/track.page.js
(function () {
  const wrapper = document.getElementById('js-track-status-wrapper');
  if (!wrapper) return;

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

  function isValidEmail(v) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(v || "").trim());
  }

  function attachCancelOtpHandlers() {
    const emailEl = document.getElementById('cancel_email');
    const hEmailEl = document.getElementById('h_cancel_email');
    const sendBtn = document.getElementById('btnSendCancelOtp');
    const statusEl = document.getElementById('cancelOtpStatus');

    const verifyWrap = document.getElementById('cancelVerifyWrap');
    const codeEl = document.getElementById('cancel_otp_code');
    const verifyBtn = document.getElementById('btnVerifyCancelOtp');
    const verifyStatus = document.getElementById('cancelVerifyStatus');

    const cancelFinalBtn = document.getElementById('btnCancelOrderFinal');

    if (!emailEl || !sendBtn || !statusEl || !verifyWrap || !codeEl || !verifyBtn || !verifyStatus || !cancelFinalBtn || !hEmailEl) {
      return;
    }

    // keep hidden email in sync
    function syncEmail() {
      hEmailEl.value = String(emailEl.value || "").trim();
      cancelFinalBtn.disabled = true; // lock again if email changes
      verifyStatus.textContent = "";
    }
    emailEl.addEventListener('input', syncEmail);
    syncEmail();

    sendBtn.addEventListener('click', async () => {
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
        const data = await postFormUrlEncoded("api/otp/send-email-otp.php", { email });
        if (data && data.success) {
          statusEl.textContent = data.message || "Code sent. Check your email.";
          statusEl.className = "small text-success mt-2";
          verifyWrap.classList.remove('d-none');
        } else {
          statusEl.textContent = data?.message || "Failed to send code.";
          statusEl.className = "small text-danger mt-2";
        }
      } catch (e) {
        statusEl.textContent = "Network error while sending code.";
        statusEl.className = "small text-danger mt-2";
      }
    });

    verifyBtn.addEventListener('click', async () => {
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
        const data = await postFormUrlEncoded("api/otp/verify-email-otp.php", { email, code });
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

  // auto-refresh the right panel
  function refreshTrackOrder() {
    fetch(window.location.href, { cache: 'no-store' })
      .then(r => r.text())
      .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newWrapper = doc.getElementById('js-track-status-wrapper');
        if (newWrapper) {
          wrapper.innerHTML = newWrapper.innerHTML;
        }
      })
      .catch(() => {});
  }

  // attach once + re-attach after refresh
  attachCancelOtpHandlers();

  setInterval(() => {
    refreshTrackOrder();
  }, 5000);

  const observer = new MutationObserver(() => {
    attachCancelOtpHandlers();
  });
  observer.observe(wrapper, { childList: true, subtree: true });
})();
