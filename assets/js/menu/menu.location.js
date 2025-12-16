// assets/js/menu/menu.location.js
(function () {
  const U = window.DMMenuUtils;
  const S = window.DMMenuStorage;

  // Rules you requested
  const DELIVERY_RADIUS_KM = 3; // >3km => pickup only
  const MAX_SHOW_KM = 4;        // >4km => show NO branches at all

  const AVG_SPEED_KMH = 20;     // for time display only
  const DEFAULT_CENTER = { lat: 14.329, lng: 120.936 }; // fallback

  function computeBranchesForLoc(branches, loc) {
    const base = { lat: loc.lat, lng: loc.lng };

    const listAll = branches
      .filter(b => typeof b.lat !== "undefined" && typeof b.lng !== "undefined")
      .map(b => {
        const dist = U.haversineKm(base, { lat: Number(b.lat), lng: Number(b.lng) });
        return { ...b, dist_km: dist, dist_min: U.kmToMinutes(dist, AVG_SPEED_KMH) };
      })
      .sort((x, y) => x.dist_km - y.dist_km);

    const nearest = listAll.length ? listAll[0] : null;

    const forcedPickup = !nearest || nearest.dist_km > DELIVERY_RADIUS_KM;

    const visibleList = listAll.filter(x => x.dist_km <= MAX_SHOW_KM).slice(0, 8);
    const noOptions = !nearest || nearest.dist_km > MAX_SHOW_KM || visibleList.length === 0;

    return { nearest, forcedPickup, noOptions, list: visibleList };
  }

  function initLocationUI(ctx) {
    const {
      branches,
      dom,
      onBranchConfirmed, // callback
    } = ctx;

    const {
      mapEl,
      btnUseMyLocation,
      nearestNameEl,
      nearestDistEl,
      orderModeBadge,
      branchResults,
      btnConfirmBranch,
      branchConfirmMsg,
      menuWrap,
      menuLockedWrap,
      selectedBranchLabel,
    } = dom;

    let map = null;
    let marker = null;
    let computed = { nearest: null, list: [], forcedPickup: true, noOptions: false };

    function lockMenu() {
      menuWrap?.classList.add("d-none");
      menuLockedWrap?.classList.remove("d-none");
    }

    function unlockMenu() {
      const sb = S.getBranch();
      if (selectedBranchLabel) selectedBranchLabel.textContent = sb ? sb.name : "—";
      menuLockedWrap?.classList.add("d-none");
      menuWrap?.classList.remove("d-none");
    }

    function renderBranchResults() {
      if (!branchResults) return;

      if (computed.noOptions) {
        if (nearestNameEl) nearestNameEl.textContent = "—";
        if (nearestDistEl) nearestDistEl.textContent = "—";
        if (orderModeBadge) orderModeBadge.textContent = "Outside service area";

        branchResults.innerHTML = `
          <div class="p-3 rounded-4 bg-dark bg-opacity-25">
            <div class="fw-bold mb-1">No nearby branches (over ${MAX_SHOW_KM} km)</div>
            <div class="small text-white-50 mb-3">
              Try ordering via delivery apps instead.
            </div>
            <div class="d-grid gap-2">
              <a class="btn btn-outline-light rounded-pill" target="_blank" rel="noopener"
                 href="https://www.foodpanda.ph">
                Open Foodpanda
              </a>
              <a class="btn btn-outline-light rounded-pill" target="_blank" rel="noopener"
                 href="https://www.grab.com/ph/">
                Open Grab
              </a>
            </div>
          </div>
        `;

        btnConfirmBranch.disabled = true;
        return;
      }

      const forcedPickup = computed.forcedPickup;
      const mode = forcedPickup ? "pickup" : (S.getMode() || "delivery");
      S.setMode(mode);

      if (nearestNameEl) nearestNameEl.textContent = computed.nearest?.name || "—";
      if (nearestDistEl) nearestDistEl.textContent = `~${computed.nearest?.dist_min ?? "—"} mins`;
      if (orderModeBadge) orderModeBadge.textContent =
        forcedPickup ? "Pickup only (too far)" : `Pickup / Delivery (${mode})`;

      const modeRow = forcedPickup ? `
        <div class="p-3 rounded-4 bg-dark bg-opacity-25 mb-3">
          <div class="fw-bold">Pickup only</div>
          <div class="small text-white-50">You’re outside the delivery range.</div>
        </div>
      ` : `
        <div class="p-3 rounded-4 bg-dark bg-opacity-25 mb-3">
          <div class="fw-bold mb-2">Choose order mode</div>
          <div class="d-flex gap-2">
            <button class="btn btn-sm ${mode==="delivery"?"btn-primary":"btn-outline-light"} rounded-pill" data-mode="delivery" type="button">Delivery</button>
            <button class="btn btn-sm ${mode==="pickup"?"btn-primary":"btn-outline-light"} rounded-pill" data-mode="pickup" type="button">Pickup</button>
          </div>
          <div class="small text-white-50 mt-2">Delivery shows extra fields at checkout.</div>
        </div>
      `;

      const cards = computed.list.map(b => {
        const id = Number(b.branch_id);
        return `
          <button type="button" class="branch-card w-100 text-start" data-branch="${id}">
            <div class="d-flex justify-content-between gap-3">
              <div>
                <div class="fw-bold">${U.escapeHtml(b.name || "")}</div>
                <div class="small text-white-50">${U.escapeHtml(b.address || "")}</div>
              </div>
              <div class="text-end">
                <div class="badge bg-dark bg-opacity-75">~${b.dist_min} mins</div>
              </div>
            </div>
          </button>
        `;
      }).join("");

      branchResults.innerHTML = `
        ${modeRow}
        <div class="small text-white-50 mb-2">Nearby branches</div>
        <div class="d-grid gap-2">${cards}</div>
      `;

      branchResults.querySelectorAll("[data-mode]").forEach(btn => {
        btn.addEventListener("click", () => {
          const m = btn.getAttribute("data-mode");
          S.setMode(m);
          renderBranchResults();
        });
      });

      branchResults.querySelectorAll("[data-branch]").forEach(btn => {
        btn.addEventListener("click", () => {
          const id = btn.getAttribute("data-branch");
          const b = branches.find(x => String(x.branch_id) === String(id));
          if (!b) return;

          S.setBranch({ branch_id: Number(b.branch_id), name: b.name || "" });
          btnConfirmBranch.disabled = false;

          branchResults.querySelectorAll("[data-branch]").forEach(x => x.classList.remove("active"));
          btn.classList.add("active");
        });
      });
    }

    function applyLoc(lat, lng) {
      const loc = { lat: Number(lat), lng: Number(lng) };
      S.setLoc(loc);
      computed = computeBranchesForLoc(branches, loc);
      renderBranchResults();
    }

    function initMap() {
      if (!mapEl || !window.L) return;

      const savedLoc = S.getLoc();
      const start = savedLoc || DEFAULT_CENTER;

      map = L.map(mapEl, { zoomControl: true }).setView([start.lat, start.lng], 12);
      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 19,
        attribution: "&copy; OpenStreetMap",
      }).addTo(map);

      marker = L.marker([start.lat, start.lng], { draggable: true }).addTo(map);

      marker.on("dragend", () => {
        const p = marker.getLatLng();
        applyLoc(p.lat, p.lng);
      });

      applyLoc(start.lat, start.lng);

      btnUseMyLocation?.addEventListener("click", () => {
        if (!navigator.geolocation) return;
        navigator.geolocation.getCurrentPosition(
          (pos) => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            map.setView([lat, lng], 14);
            marker.setLatLng([lat, lng]);
            applyLoc(lat, lng);
          },
          () => {},
          { enableHighAccuracy: true, timeout: 8000 }
        );
      });
    }

    btnConfirmBranch?.addEventListener("click", () => {
      branchConfirmMsg?.classList.add("d-none");

      const sb = S.getBranch();
      const loc = S.getLoc();

      if (!loc) {
        branchConfirmMsg.textContent = "Please pin your location first.";
        branchConfirmMsg.classList.remove("d-none");
        return;
      }
      if (!sb) {
        branchConfirmMsg.textContent = "Please select a branch.";
        branchConfirmMsg.classList.remove("d-none");
        return;
      }

      if (computed.forcedPickup) S.setMode("pickup");

      unlockMenu();
      onBranchConfirmed?.(sb);
    });

    // Boot sequence for this module
    lockMenu();

    const leafletWait = setInterval(() => {
      if (window.L && mapEl) {
        clearInterval(leafletWait);
        initMap();

        const sb = S.getBranch();
        if (sb) {
          btnConfirmBranch.disabled = false;
          unlockMenu();
          onBranchConfirmed?.(sb);
        }
      }
    }, 80);

    return {
      lockMenu,
      unlockMenu,
      getComputed: () => computed,
    };
  }

  window.DMMenuLocation = { initLocationUI };
})();
