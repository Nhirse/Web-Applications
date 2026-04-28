export function fmtMoney(n) {
  const num = Number(n || 0);
  return num.toLocaleString(undefined, { style: "currency", currency: "USD" });
}

export function safeText(s) {
  return String(s ?? "").replace(/[<>]/g, "");
}

export function byTicker(a, b) {
  return (a.ticker || "").localeCompare(b.ticker || "");
}

export function computeManualValue(holdings) {
  return holdings.reduce((sum, h) => {
    const shares = Number(h.shares || 0);
    const avg = Number(h.avgCost || 0);
    return sum + shares * avg;
  }, 0);
}

export function qs(name) {
  return new URLSearchParams(window.location.search).get(name);
}

export function setQS(name, value) {
  const url = new URL(window.location.href);
  if (!value) url.searchParams.delete(name);
  else url.searchParams.set(name, value);
  window.history.replaceState({}, "", url.toString());
}