// assets/js/contact-branches.js
(function () {
  const dataEl = document.getElementById("branches-data");
  if (!dataEl) return; // not the contact page

  let branches = [];
  try {
    branches = JSON.parse(dataEl.textContent || "[]");
  } catch (e) {
    console.error("branches-data JSON parse failed", e);
    return;
  }

  const input = document.getElementById("branchSearch");
  const sugg = document.getElementById("branchSuggestions");

  // If these aren't present, stop (prevents your error)
  if (!input || !sugg) return;

  const info = document.getElementById("branchInfo");
  const nameEl = document.getElementById("branchName");
  const addrEl = document.getElementById("branchAddress");

  const plusRow = document.getElementById("branchPlusCodeRow");
  const plusEl = document.getElementById("branchPlusCode");

  const phoneRow = document.getElementById("branchPhoneRow");
  const phoneEl = document.getElementById("branchPhone");

  const coordsRow = document.getElementById("branchCoordsRow");
  const coordsEl = document.getElementById("branchCoords");

  const hoursEl = document.getElementById("branchHours");
  const embedEl = document.getElementById("mapEmbed");

  const btnMaps = document.getElementById("btnMaps");
  const btnFb = document.getElementById("btnFacebook");

  function escapeHtml(str) {
    return String(str ?? "")
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");
  }

  function mapsLink(b) {
    if (typeof b.lat === "number" && typeof b.lng === "number") {
      return `https://www.google.com/maps?q=${b.lat},${b.lng}`;
    }
    return `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(b.name || "Don Macchiatos")}`;
  }

  function renderHours(hours) {
    if (!hoursEl) return;
    if (!hours || typeof hours !== "object") {
      hoursEl.innerHTML = `<div>Hours not available.</div>`;
      return;
    }
    const days = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];
    hoursEl.innerHTML = days.map(d => {
      const v = hours[d] ? String(hours[d]) : "—";
      return `<div class="d-flex justify-content-between gap-3"><span>${d}</span><span class="text-white">${escapeHtml(v)}</span></div>`;
    }).join("");
  }

  function renderEmbed(html) {
    if (!embedEl) return;
    embedEl.innerHTML = "";
    if (!html) {
      embedEl.innerHTML = `
        <div class="d-flex align-items-center justify-content-center text-white-50 p-4 text-center w-100">
          No map embed available.
        </div>
      `;
      return;
    }
    embedEl.innerHTML = html;

    const iframe = embedEl.querySelector("iframe");
    if (iframe) {
      iframe.removeAttribute("width");
      iframe.removeAttribute("height");
      iframe.style.width = "100%";
      iframe.style.height = "100%";
      iframe.style.border = "0";
    }
  }

  function hideSuggestions() {
    sugg.classList.add("d-none");
  }

  function showSuggestions(items) {
    sugg.innerHTML = "";

    // Force readable dropdown style (won't get eaten by CSS)
    sugg.classList.remove("d-none");
    sugg.style.background = "#111";
    sugg.style.border = "1px solid rgba(255,255,255,0.15)";
    sugg.style.borderRadius = "12px";

    if (!items.length) {
      sugg.innerHTML = `<div style="padding:12px; color:rgba(255,255,255,0.7);">No matches.</div>`;
      return;
    }

    items.forEach(b => {
      const item = document.createElement("button");
      item.type = "button";

      item.style.display = "block";
      item.style.width = "100%";
      item.style.textAlign = "left";
      item.style.padding = "12px 14px";
      item.style.border = "0";
      item.style.background = "transparent";
      item.style.color = "#fff";

      item.innerHTML = `
        <div style="font-weight:700;">${escapeHtml(b.name)}</div>
        <div style="font-size:12px; color:rgba(255,255,255,0.7);">${escapeHtml(b.address)}</div>
      `;

      item.addEventListener("mouseenter", () => item.style.background = "rgba(255,255,255,0.08)");
      item.addEventListener("mouseleave", () => item.style.background = "transparent");
      item.addEventListener("click", () => setSelected(b));

      sugg.appendChild(item);
    });
  }

  function search(term) {
    const t = (term || "").trim().toLowerCase();
    if (!t) return [];
    return branches
      .filter(b => {
        const hay = `${b.name || ""} ${b.address || ""}`.toLowerCase();
        return hay.includes(t);
      })
      .slice(0, 10);
  }

  function setSelected(b) {
    if (!b) return;

    // show info box
    if (info) info.classList.remove("d-none");

    if (nameEl) nameEl.textContent = b.name || "";
    if (addrEl) addrEl.textContent = b.address || "";

    if (plusRow && plusEl) {
      if (b.plus_code) { plusRow.style.display = ""; plusEl.textContent = b.plus_code; }
      else { plusRow.style.display = "none"; plusEl.textContent = ""; }
    }

    if (phoneRow && phoneEl) {
      if (b.phone) { phoneRow.style.display = ""; phoneEl.textContent = b.phone; }
      else { phoneRow.style.display = "none"; phoneEl.textContent = ""; }
    }

    if (coordsRow && coordsEl) {
      if (typeof b.lat === "number" && typeof b.lng === "number") {
        coordsRow.style.display = "";
        coordsEl.textContent = `${b.lat}, ${b.lng}`;
      } else {
        coordsRow.style.display = "none";
        coordsEl.textContent = "";
      }
    }

    if (btnMaps) btnMaps.href = mapsLink(b);

    if (btnFb) {
      if (b.facebook) {
        btnFb.classList.remove("d-none");
        btnFb.href = b.facebook;
      } else {
        btnFb.classList.add("d-none");
        btnFb.removeAttribute("href");
      }
    }

    renderHours(b.hours);
    renderEmbed(b.embed || null);

    input.value = b.name || "";
    hideSuggestions();
  }

  // EVENTS (safe, because input exists)
  input.addEventListener("input", () => {
    const term = input.value.trim();
    const results = term ? search(term) : branches.slice(0, 10);
    showSuggestions(results);
  });

  input.addEventListener("focus", () => {
    const term = input.value.trim();
    const results = term ? search(term) : branches.slice(0, 10);
    showSuggestions(results);
  });

  document.addEventListener("click", (e) => {
    if (!sugg.contains(e.target) && e.target !== input) hideSuggestions();
  });

  input.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      const results = search(input.value);
      if (results.length) setSelected(results[0]);
    }
    if (e.key === "Escape") hideSuggestions();
  });

  hideSuggestions();
})();
