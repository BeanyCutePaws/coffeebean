// assets/js/main.js

(function () {
  // Load JS files conditionally based on DOM
  function loadScript(src) {
    const s = document.createElement("script");
    s.src = src;
    s.defer = true;
    document.body.appendChild(s);
  }

  // Contact page
  if (document.getElementById("branches-data")) {
    loadScript("assets/js/contact-branches.js");
  }

  // Future examples:
  // if (document.getElementById("cart-data")) loadScript("assets/js/cart.js");
  // if (document.getElementById("checkout-form")) loadScript("assets/js/checkout.js");
})();
