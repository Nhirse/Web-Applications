import { loadHoldings, upsertHolding, deleteHolding, clearAllHoldings } from "./storage.js";
import { computeManualValue, fmtMoney, safeText } from "./ui.js";
import { initSidebarToggle } from "./sidebar.js";

initSidebarToggle();

let holdings = [];
let viewArchived = false;
let filterText = "";

const form = document.getElementById("holdingForm");
const idEl = document.getElementById("holdingId");
const tickerEl = document.getElementById("ticker");
const sharesEl = document.getElementById("shares");
const avgCostEl = document.getElementById("avgCost");
const notesEl = document.getElementById("notes");
const formModeChip = document.getElementById("formModeChip");

const summaryValue = document.getElementById("summaryValue");
const summaryActive = document.getElementById("summaryActive");
const summaryArchived = document.getElementById("summaryArchived");

const table = document.getElementById("holdingsTable");
const tbody = table.querySelector("tbody");

const portfolioSearch = document.getElementById("portfolioSearch");
const portfolioSearchClear = document.getElementById("portfolioSearchClear");
const resetBtn = document.getElementById("resetBtn");

const showActiveBtn = document.getElementById("showActiveBtn");
const showArchivedBtn = document.getElementById("showArchivedBtn");
const dangerClearAllBtn = document.getElementById("dangerClearAllBtn");

function normalizeTicker(t) {
  return String(t || "").trim().toUpperCase();
}

function setFormMode(mode) {
  formModeChip.textContent = mode;
  formModeChip.className = "chip " + (mode === "Edit" ? "chip-gray" : "chip-blue");
}

function resetForm() {
  idEl.value = "";
  tickerEl.value = "";
  sharesEl.value = "";
  avgCostEl.value = "";
  notesEl.value = "";
  setFormMode("Add");
}

function renderSummary() {
  const active = holdings.filter(h => !h.archived);
  const archived = holdings.filter(h => !!h.archived);

  summaryValue.textContent = fmtMoney(computeManualValue(active));
  summaryActive.textContent = String(active.length);
  summaryArchived.textContent = String(archived.length);
}

function renderTable() {
  let rows = holdings
    .filter(h => (viewArchived ? !!h.archived : !h.archived))
    .filter(h => !filterText || (h.ticker || "").includes(filterText))
    .map(h => {
      const statusChip = h.archived
        ? `<span class="chip chip-gray">Archived</span>`
        : `<span class="chip chip-green">Active</span>`;

      const actions = `
        <div class="actions" style="justify-content:flex-end">
          <button class="btn btn-ghost" data-action="edit" data-id="${h.id}">Edit</button>
          ${
            h.archived
              ? `<button class="btn btn-ghost" data-action="unarchive" data-id="${h.id}">Unarchive</button>`
              : `<button class="btn btn-ghost" data-action="archive" data-id="${h.id}">Archive</button>`
          }
          <button class="btn btn-danger" data-action="delete" data-id="${h.id}">Delete</button>
        </div>
      `;

      return `
        <tr>
          <td><b>${safeText(h.ticker)}</b></td>
          <td>${Number(h.shares).toFixed(4)}</td>
          <td>${fmtMoney(h.avgCost)}</td>
          <td>${statusChip}</td>
          <td class="muted">${safeText(h.notes || "")}</td>
          <td class="right">${actions}</td>
        </tr>
      `;
    })
    .join("");

  if (!rows) {
    rows = `<tr><td class="muted" colspan="6">No holdings match this view.</td></tr>`;
  }

  tbody.innerHTML = rows;
  renderSummary();
}

async function saveHoldingToDatabase(holding) {
  const response = await fetch("api/store_holding.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify(holding)
  });

  const result = await response.json();

  if (!response.ok || !result.success) {
    throw new Error(result.message || "Failed to save holding");
  }

  return result;
}

async function loadFromDatabase() {
  const response = await fetch("api/get_holdings.php");
  const result = await response.json();

  if (!response.ok || !result.success) {
    throw new Error(result.message || "Failed to load holdings");
  }

  return result.holdings;
}

async function upsertFromForm() {
  console.log("upsertFromForm ran");

  const id = idEl.value || crypto.randomUUID();
  const ticker = normalizeTicker(tickerEl.value);
  const shares = Number(sharesEl.value);
  const avgCost = Number(avgCostEl.value);
  const notes = (notesEl.value || "").trim();

  if (!ticker) return alert("Ticker is required.");
  if (!(shares >= 0)) return alert("Shares must be 0 or more.");
  if (!(avgCost >= 0)) return alert("Avg cost must be 0 or more.");

  const existing = holdings.find(h => h.id === id);
  const archived = existing ? !!existing.archived : false;

  const holding = {
    id,
    ticker,
    shares,
    avgCost,
    notes,
    archived,
    updatedAt: new Date().toISOString()
  };

  try {
    await saveHoldingToDatabase(holding);
    holdings = await loadFromDatabase();
    renderTable();
    alert("Saved to database");
    resetForm();
  } catch (err) {
    console.error(err);
    alert(err.message);
  }
}

async function handleRowAction(e) {
  const btn = e.target.closest("button[data-action]");
  if (!btn) return;

  const action = btn.dataset.action;
  const id = btn.dataset.id;
  const holding = holdings.find(h => h.id === id);
  if (!holding) return;

  if (action === "edit") {
    idEl.value = holding.id;
    tickerEl.value = holding.ticker;
    sharesEl.value = holding.shares;
    avgCostEl.value = holding.avgCost;
    notesEl.value = holding.notes || "";
    setFormMode("Edit");
    window.scrollTo({ top: 0, behavior: "smooth" });
    return;
  }

  if (action === "archive") {
    holding.archived = true;

    try {
      await saveHoldingToDatabase(holding);
      holdings = await loadFromDatabase();
      renderTable();
    } catch (err) {
      console.error(err);
      alert(err.message);
    }
    return;
  }

  if (action === "unarchive") {
    holding.archived = false;

    try {
      await saveHoldingToDatabase(holding);
      holdings = await loadFromDatabase();
      renderTable();
    } catch (err) {
      console.error(err);
      alert(err.message);
    }
    return;
  }

  if (action === "delete") {
    const ok = confirm(`Delete ${holding.ticker}? This cannot be undone.`);
    if (!ok) return;

    holdings = deleteHolding(id);
    renderTable();
    return;
  }
}

function wireEvents() {
  console.log("wireEvents ran");

  form.addEventListener("submit", (e) => {
    e.preventDefault();
    upsertFromForm();
  });

  resetBtn.addEventListener("click", resetForm);

  tbody.addEventListener("click", handleRowAction);

  showActiveBtn.addEventListener("click", () => {
    viewArchived = false;
    renderTable();
  });

  showArchivedBtn.addEventListener("click", () => {
    viewArchived = true;
    renderTable();
  });

  portfolioSearch.addEventListener("input", () => {
    filterText = normalizeTicker(portfolioSearch.value);
    renderTable();
  });

  portfolioSearchClear.addEventListener("click", () => {
    portfolioSearch.value = "";
    filterText = "";
    renderTable();
  });

  dangerClearAllBtn.addEventListener("click", () => {
    alert("Clear All is still using old localStorage logic and needs a database version.");
  });
}

async function initPortfolioPage() {
  try {
    holdings = await loadFromDatabase();
    renderTable();
  } catch (err) {
    console.error("Failed to load holdings:", err);
    tbody.innerHTML = `<tr><td class="muted" colspan="6">Failed to load holdings from database.</td></tr>`;
  }
}

wireEvents();
initPortfolioPage();