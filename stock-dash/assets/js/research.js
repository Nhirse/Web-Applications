import { qs, setQS, safeText } from "./ui.js";
import { initSidebarToggle } from "./sidebar.js";

initSidebarToggle();

// maybe this one https://newsapi.org

const NEWS_API_KEY = ""; //keys

const queryInput = document.getElementById("queryInput");
const searchBtn = document.getElementById("searchBtn");

const keywordFilter = document.getElementById("keywordFilter");
const sourceFilter = document.getElementById("sourceFilter");
const sortSelect = document.getElementById("sortSelect");
const pageSizeSelect = document.getElementById("pageSizeSelect");

const applyFiltersBtn = document.getElementById("applyFiltersBtn");
const clearFiltersBtn = document.getElementById("clearFiltersBtn");
const refreshBtn = document.getElementById("refreshBtn");

const apiStatus = document.getElementById("apiStatus");
const currentQuery = document.getElementById("currentQuery");
const articleCount = document.getElementById("articleCount");
const newsList = document.getElementById("newsList");

let state = {
  q: "",
  keyword: "",
  source: "",
  sortBy: "publishedAt",
  pageSize: 20
};

function setStatus(msg) {
  apiStatus.innerHTML = msg;
}

function loadStateFromQS() {
  state.q = (qs("q") || "").trim();
  if (state.q) queryInput.value = state.q;

  currentQuery.textContent = state.q || "—";
}

function clearResults(msg) {
  newsList.innerHTML = `<div class="muted">${safeText(msg)}</div>`;
  articleCount.textContent = "0";
}

function renderArticles(articles) {
  articleCount.textContent = String(articles.length);

  if (!articles.length) {
    clearResults("No articles found for that query/filters.");
    return;
  }

  const html = articles.map(a => {
    const title = a.title || "Untitled";
    const source = (a.source && a.source.name) ? a.source.name : "Unknown source";
    const when = a.publishedAt ? new Date(a.publishedAt).toLocaleString() : "Unknown date";
    const desc = a.description || "";
    const url = a.url || "#";

    return `
      <div class="news-item">
        <div class="news-title">${safeText(title)}</div>
        <div class="news-meta">
          <span class="chip chip-gray">${safeText(source)}</span>
          <span>${safeText(when)}</span>
        </div>
        <div class="news-desc">${safeText(desc)}</div>
        <div class="news-actions">
          <a class="btn btn-primary" href="${url}" target="_blank" rel="noopener">Open</a>
        </div>
      </div>
    `;
  }).join("");

  newsList.innerHTML = html;
}

function buildNewsApiUrl() {
  const qParts = [];
  if (state.q) qParts.push(state.q);
  if (state.keyword) qParts.push(state.keyword);

  const q = qParts.join(" ");

  const params = new URLSearchParams({
    q,
    sortBy: state.sortBy,
    pageSize: String(state.pageSize),
    language: "en"
  });

  if (state.source)params.set("sources", state.source);
  
//  return `https://newsapi.org/v2/everything?${params.toString()}`;
}

async function fetchNews() {
  if (!state.q) {
    clearResults("Enter a ticker or company name to search.");
    return;
  }

  currentQuery.textContent = state.q;

  if (!NEWS_API_KEY) {
    setStatus(`add NewsAPI key in research.js.`);
    clearResults("No API key set.");
    return;
  }

  setStatus(`Using NewsAPI. If errors, run Live Server (http://localhost...).`);

  const url = buildNewsApiUrl();

  try {
    newsList.innerHTML = `<div class="muted">Loading news…</div>`;
    const res = await fetch(url, {
      headers: { "X-Api-Key": NEWS_API_KEY }
    });

    if (!res.ok) {
      const text = await res.text();
      throw new Error(`HTTP ${res.status}: ${text}`);
    }

    const data = await res.json();
    const articles = Array.isArray(data.articles) ? data.articles : [];
    renderArticles(articles);
  } catch (err) {
    setStatus(`<span style="color:#fb7185">Error:</span> ${safeText(err.message)}`);
    clearResults("Failed to load news. Check your API key and run via Live Server.");
  }
}

function applyFilters() {
  state.keyword = (keywordFilter.value || "").trim();
  state.source = (sourceFilter.value || "").trim();
  state.sortBy = sortSelect.value;
  state.pageSize = Number(pageSizeSelect.value) || 20;
  fetchNews();
}

function clearFilters() {
  keywordFilter.value = "";
  sourceFilter.value = "";
  sortSelect.value = "publishedAt";
  pageSizeSelect.value = "20";
  state.keyword = "";
  state.source = "";
  state.sortBy = "publishedAt";
  state.pageSize = 20;
  fetchNews();
}

function search() {
  state.q = (queryInput.value || "").trim();
  setQS("q", state.q);
  fetchNews();
}

function wireEvents() {
  searchBtn.addEventListener("click", search);
  queryInput.addEventListener("keydown", (e) => {
    if (e.key === "Enter") search();
  });

  applyFiltersBtn.addEventListener("click", applyFilters);
  clearFiltersBtn.addEventListener("click", clearFilters);
  refreshBtn.addEventListener("click", fetchNews);
}

wireEvents();
loadStateFromQS();
fetchNews();