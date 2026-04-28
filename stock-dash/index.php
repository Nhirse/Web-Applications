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
  <title>Stock Portfolio Dashboard</title>
  <link rel="stylesheet" href="assets/css/styles.css" />
</head>
<body class="home-page">
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
        <a class="side-link active" href="index.php">
		  <span class="dot"></span> <span class="link-text">Home</span>
		</a>
        <a class="side-link" href="portfolio.php">
          <span class="dot"></span> <span class="link-text">Portfolio</span>
        </a>
        <a class="side-link" href="research.php">
          <span class="dot"></span> <span class="link-text">Research</span>
        </a>
      </nav>
    </aside>

    <main class="main">
      <header class="topbar">
        <div class="topbar-left">
          <button id="sidebarToggle" class="icon-btn" aria-label="Toggle sidebar">☰</button>

          <div>
            <div class="page-title">Home</div>
            <div class="page-subtitle">Track holdings & research stocks in one place.</div>
          </div>
        </div>

        <div class="topbar-right">
          <div class="search">
            <input id="globalSearch" type="text" placeholder="Search ticker" />
            <button id="globalSearchBtn" class="btn btn-primary">Search</button>
          </div>
        </div>
      </header>

      <section class="content">
        <div class="grid grid-3">
          <div class="card">
            <div class="card-header">
              <div class="card-title">Portfolio Value</div>
              <div class="chip chip-blue">Overview</div>
            </div>
            <div class="metric" id="metricTotalValue">$0.00</div>
            <div class="muted">Based on your stored holdings (shares × avg cost).</div>
          </div>

          <div class="card">
            <div class="card-header">
              <div class="card-title">Active Positions</div>
              <div class="chip chip-green">Holdings</div>
            </div>
            <div class="metric" id="metricActiveCount">0</div>
            <div class="muted">Archived holdings aren’t counted as active.</div>
          </div>

          <div class="card">
            <div class="card-header">
              <div class="card-title">Archived Positions</div>
              <div class="chip chip-gray">History</div>
            </div>
            <div class="metric" id="metricArchivedCount">0</div>
            <div class="muted">Keep records without cluttering your view.</div>
          </div>
        </div>

        <div class="grid grid-2">
          <div class="card">
            <div class="card-header">
              <div class="card-title">Recent Holdings</div>
              <div class="actions">
                <a class="btn btn-ghost" href="portfolio.html">Manage</a>
              </div>
            </div>

            <div class="table-wrap">
              <table class="table" id="recentHoldingsTable">
                <thead>
                  <tr>
                    <th>Ticker</th>
                    <th>Shares</th>
                    <th>Avg Cost</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr><td class="muted" colspan="4">No holdings yet. Add one in Portfolio.</td></tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <div class="card-title">Quick Research</div>
              <div class="actions">
                <a class="btn btn-ghost" href="research.html">Open</a>
              </div>
            </div>

            <div class="muted">
              Type a ticker above (global search) to jump to Research.
              <br /><br />
              Example queries:
              <div class="tag-row">
                <span class="tag">AAPL</span>
                <span class="tag">TSLA</span>
                <span class="tag">MSFT</span>
                <span class="tag">NVDA</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <footer class="footer">
        <span>Do Not take advice from this page • This is a Demo</span>
      </footer>
    </main>

  </div>

<script type="module" src="assets/js/sidebar.js">
  </script>
<script type="module" src="assets/js/app.js"></script>
</body>
</html>
