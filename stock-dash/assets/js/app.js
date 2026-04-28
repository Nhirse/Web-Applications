import { loadHoldings } from "./storage.js";
import { computeManualValue, fmtMoney, safeText } from "./ui.js";
import { initSidebarToggle } from "./sidebar.js";

initSidebarToggle();

const holdings = loadHoldings();

const totalValueEl = document.getElementById("metricTotalValue");
const activeCountEl = document.getElementById("metricActiveCount");
const archivedCountEl = document.getElementById("metricArchivedCount");
const recentTable = document.getElementById("recentHoldingsTable");

function renderHome() {
  const active = holdings.filter(h => !h.archived);
  const archived = holdings.filter(h => !!h.archived);

  totalValueEl.textContent = fmtMoney(computeManualValue(active));
  activeCountEl.textContent = String(active.length);
  archivedCountEl.textContent = String(archived.length);

  const rows = active.slice(0, 6).map(h => `
    <tr>
      <td><b>${safeText(h.ticker)}</b></td>
      <td>${Number(h.shares).toFixed(4)}</td>
      <td>${fmtMoney(h.avgCost)}</td>
      <td><span class="chip chip-green">Active</span></td>
    </tr>
  `).join("");

  recentTable.querySelector("tbody").innerHTML =
    rows || `<tr><td class="muted" colspan="4">No holdings yet. Add one in Portfolio.</td></tr>`;
}

function wireGlobalSearch() {
  const input = document.getElementById("globalSearch");
  const btn = document.getElementById("globalSearchBtn");

  const go = () => {
    const q = (input.value || "").trim();
    if (!q) return;
    window.location.href = `research.html?q=${encodeURIComponent(q)}`;
  };

  btn.addEventListener("click", go);
  input.addEventListener("keydown", (e) => {
    if (e.key === "Enter") go();
  });
}

renderHome();
wireGlobalSearch();