const KEY = "stock_pilot_holdings_v1";

export function loadHoldings() {
  try {
    const raw = localStorage.getItem(KEY);
    const parsed = raw ? JSON.parse(raw) : [];
    return Array.isArray(parsed) ? parsed : [];
  } catch {
    return [];
  }
}

export function saveHoldings(holdings) {
  localStorage.setItem(KEY, JSON.stringify(holdings));
}

export function upsertHolding(holding) {
  const holdings = loadHoldings();
  const idx = holdings.findIndex(h => h.id === holding.id);
  if (idx >= 0) holdings[idx] = holding;
  else holdings.unshift(holding);
  saveHoldings(holdings);
  return holdings;
}

export function deleteHolding(id) {
  const holdings = loadHoldings().filter(h => h.id !== id);
  saveHoldings(holdings);
  return holdings;
}

export function clearAllHoldings() {
  saveHoldings([]);
}