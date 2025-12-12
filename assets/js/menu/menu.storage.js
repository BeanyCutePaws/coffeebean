// assets/js/menu/menu.storage.js
(function () {
  const LOC_KEY    = "dm_customer_loc_v1";      // {lat,lng}
  const BRANCH_KEY = "dm_selected_branch_v1";   // {branch_id,name}
  const MODE_KEY   = "dm_order_mode_v1";        // "pickup"|"delivery"

  function readJSON(key, fallback) {
    try { return JSON.parse(sessionStorage.getItem(key) || ""); }
    catch { return fallback; }
  }

  function writeJSON(key, val) {
    sessionStorage.setItem(key, JSON.stringify(val));
  }

  const DMMenuStorage = {
    LOC_KEY, BRANCH_KEY, MODE_KEY,

    getLoc() { return readJSON(LOC_KEY, null); },
    setLoc(loc) { writeJSON(LOC_KEY, loc); },

    getBranch() { return readJSON(BRANCH_KEY, null); },
    setBranch(b) { writeJSON(BRANCH_KEY, b); },

    getMode() { return sessionStorage.getItem(MODE_KEY) || ""; },
    setMode(m) { sessionStorage.setItem(MODE_KEY, m); },
  };

  window.DMMenuStorage = DMMenuStorage;
})();
