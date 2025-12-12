// assets/js/cart.js
// Shared cart module (sessionStorage) used across menu, checkout, etc.
(function () {
  const CART_KEY = "dm_cart_v1";

  function readCart() {
    try { return JSON.parse(sessionStorage.getItem(CART_KEY) || "[]"); }
    catch { return []; }
  }

  function writeCart(cart) {
    sessionStorage.setItem(CART_KEY, JSON.stringify(cart));
  }

  function count(cart = readCart()) {
    return cart.reduce((s, it) => s + Number(it.qty || 0), 0);
  }

  function subtotal(cart = readCart()) {
    return cart.reduce((s, it) => s + Number(it.qty || 0) * Number(it.price || 0), 0);
  }

  function addItem(product, qty = 1) {
    const cart = readCart();

    // default variant = with coffee true; can toggle later in cart UI
    const existing = cart.find(it =>
      String(it.product_id) === String(product.product_id) &&
      Boolean(it.with_coffee) === true
    );

    if (existing) {
      existing.qty = Number(existing.qty || 1) + Math.max(1, Number(qty || 1));
    } else {
      cart.push({
        product_id: Number(product.product_id),
        name: String(product.name || ""),
        price: Number(product.price || 0),
        image_path: String(product.image_path || ""),
        allow_no_coffee: !!product.allow_no_coffee,
        with_coffee: true,
        qty: Math.max(1, Number(qty || 1)),
        });

    }

    writeCart(cart);
    return cart;
  }

  function inc(index) {
    const cart = readCart();
    if (!cart[index]) return cart;
    cart[index].qty = Number(cart[index].qty || 1) + 1;
    writeCart(cart);
    return cart;
  }

  function dec(index) {
    const cart = readCart();
    if (!cart[index]) return cart;
    cart[index].qty = Math.max(1, Number(cart[index].qty || 1) - 1);
    writeCart(cart);
    return cart;
  }

  function remove(index) {
    const cart = readCart();
    cart.splice(index, 1);
    writeCart(cart);
    return cart;
  }

  function setCoffee(index, withCoffee) {
    const cart = readCart();
    if (!cart[index]) return cart;
    cart[index].with_coffee = !!withCoffee;
    writeCart(cart);
    return cart;
  }

  function clear() {
    writeCart([]);
    return [];
  }

  window.DMCart = {
    KEY: CART_KEY,
    readCart, writeCart,
    count, subtotal,
    addItem, inc, dec, remove, setCoffee, clear,
  };
})();
