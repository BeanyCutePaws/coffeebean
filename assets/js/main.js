// assets/js/main.js
(function () {
  function loadScript(src) {
    return new Promise((resolve, reject) => {
      const s = document.createElement("script");
      s.src = src;
      s.onload = () => resolve();
      s.onerror = () => reject(new Error("Failed to load: " + src));
      document.body.appendChild(s);
    });
  }

  async function boot() {
    // Contact page
    if (document.getElementById("branches-data")) {
      await loadScript("assets/js/contact-branches.js");
    }

    // Menu page (STRICT ORDER)
    if (document.getElementById("menu-data")) {
      await loadScript("assets/js/cart.js");

      await loadScript("assets/js/menu/menu.utils.js");
      await loadScript("assets/js/menu/menu.storage.js");
      await loadScript("assets/js/menu/menu.location.js");
      await loadScript("assets/js/menu/menu.catalog.js");
      await loadScript("assets/js/menu/menu.checkout-gate.js");
      await loadScript("assets/js/menu/menu.page.js");
    }

    // Checkout page
    if (document.getElementById("checkout-root")) {
      await loadScript("assets/js/cart.js");
      await loadScript("assets/js/menu/menu.utils.js");
      await loadScript("assets/js/menu/menu.storage.js");
      await loadScript("assets/js/checkout/checkout.page.js");
    }

  }

  boot().catch(err => console.error("[main.js loader]", err));
})();
