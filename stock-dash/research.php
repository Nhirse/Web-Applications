<?php
session_start();

if (!isset($_SESSION['admin_id']))
  {
    header('location: login.php');
    exit();
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Research • Stock Pilot</title>
  <link rel="stylesheet" href="assets/css/styles.css" />
</head>
<body>
  <div class="app-shell">
    <aside class="sidebar">
      <div class="brand">
        <div class="brand-badge">SP</div>
        <div class="brand-text">
          <div class="brand-title">Stock Pilot</div>
          <div class="brand-subtitle">Portfolio Dashboard</div>
        </div>
      </div>

      <nav class="side-nav">
        <a class="side-link" href="index.php">
          <span class="dot"></span> <span class="link-text">Home</span>
        </a>
        <a class="side-link" href="portfolio.php">
          <span class="dot"></span> <span class="link-text">Portfolio</span>
        </a>
        <a class="side-link active" href="research.php">
          <span class="dot"></span> <span class="link-text">Research</span>
        </a>
      </nav>

      <div class="side-card">
        <div class="side-card-title">Filters</div>
        <ul class="side-card-list">
          <li>Search a ticker or company name.</li>
          <li>Filter news by keyword or source.</li>
          <li>Sort newest → oldest.</li>
        </ul>
      </div>

    </aside>

    <main class="main">
      <header class="topbar">
        <div class="topbar-left">
          <button id="sidebarToggle" class="icon-btn" aria-label="Toggle sidebar">☰</button>

          <div>
            <div class="page-title">Research</div>
            <div class="page-subtitle">Search stocks + filter news.</div>
          </div>
        </div>

        <div class="topbar-right">
          <div class="search">
            <input id="queryInput" type="text" placeholder="Ticker or company" />
            <button id="searchBtn" class="btn btn-primary">Search</button>
          </div>
        </div>
      </header>

      <section class="content">
        <div class="grid grid-2">
          <div class="card">
            <div class="card-header">
              <div class="card-title">News Filters</div>
              <!-- Optional API -->
            </div>

            <div class="form">
              <label class="label">Keyword filter</label>
              <input id="keywordFilter" class="input" placeholder="earnings, lawsuit, guidance..." />

              <label class="label">Source filter</label>
              <input id="sourceFilter" class="input" placeholder="the-verge, bloomberg, reuters..." />

              <div class="form-row">
                <div class="form-col">
                  <label class="label">Sort</label>
                  <select id="sortSelect" class="input select">
                    <option value="publishedAt">Newest</option>
                    <option value="relevancy">Relevancy</option>
                    <option value="popularity">Popularity</option>
                  </select>
                </div>
                <div class="form-col">
                  <label class="label">Max results</label>
                  <select id="pageSizeSelect" class="input select">
                    <option value="10">10</option>
                    <option value="20" selected>20</option>
                    <option value="50">50</option>
                  </select>
                </div>
              </div>

              <div class="form-actions">
                <button id="applyFiltersBtn" class="btn btn-ghost" type="button">Apply</button>
                <button id="clearFiltersBtn" class="btn btn-ghost" type="button">Clear</button>
              </div>

              <!-- add api in research.js -->
              <div class="muted small" id="apiStatus"></div>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <div class="card-title">Snapshot</div>
              <div class="chip chip-green">Query</div>
            </div>
            <div class="snapshot">
              <div><span class="muted">Current Query:</span> <b id="currentQuery">—</b></div>
              <div><span class="muted">Articles:</span> <b id="articleCount">0</b></div>
              <div class="muted small"></div>
            </div>
          </div>
        </div>

        <div class="grid grid-1">
          <div class="card">
            <div class="card-header">
              <div class="card-title">News Results</div>
              <div class="actions">
                <button id="refreshBtn" class="btn btn-ghost">Refresh</button>
              </div>
            </div>

            <div id="newsList" class="news-list">
              <div class="muted">Search to load news…</div>
            </div>
          </div>
        </div>
      </section>

      <footer class="footer">
        <span>Stock Pilot • Research</span>
      </footer>
    </main>
  </div>

  <script type="module" src="assets/js/sidebar.js"></script>
  <script type="module" src="assets/js/research.js"></script>
</body>
</html>