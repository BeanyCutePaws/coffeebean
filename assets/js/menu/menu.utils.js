// assets/js/menu/menu.utils.js
(function () {
  const DMMenuUtils = {
    money(n) {
      return Number(n || 0).toFixed(2);
    },

    escapeHtml(str) {
      return String(str ?? "")
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
    },

    haversineKm(a, b) {
      const R = 6371;
      const toRad = (d) => (d * Math.PI) / 180;
      const dLat = toRad(b.lat - a.lat);
      const dLng = toRad(b.lng - a.lng);
      const s1 = Math.sin(dLat / 2) ** 2;
      const s2 = Math.cos(toRad(a.lat)) * Math.cos(toRad(b.lat)) * (Math.sin(dLng / 2) ** 2);
      return 2 * R * Math.asin(Math.sqrt(s1 + s2));
    },

    kmToMinutes(km, speedKmh) {
      const v = Number(speedKmh || 20);
      const mins = (Number(km || 0) / v) * 60;
      return Math.max(1, Math.round(mins));
    }
  };

  window.DMMenuUtils = DMMenuUtils;
})();
