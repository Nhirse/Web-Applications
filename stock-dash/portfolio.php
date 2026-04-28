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
  <title>Portfolio • Stock Pilot</title>
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
        <a class="side-link active" href="portfolio.php">
          <span class="dot"></span> <span class="link-text">Portfolio</span>
        </a>
        <a class="side-link" href="research.php">
          <span class="dot"></span> <span class="link-text">Research</span>
        </a>
      </nav>

      <div class="side-card">
        <div class="side-card-title">Portfolio Actions</div>
        <ul class="side-card-list">
          <li>Add holdings you own.</li>
          <li>Edit shares / average cost.</li>
          <li>Archive closed positions.</li>
        </ul>
      </div>
    </aside>

    <main class="main">
      <header class="topbar">
        <div class="topbar-left">
          <!-- ✅ Hamburger added -->
          <button id="sidebarToggle" class="icon-btn" aria-label="Toggle sidebar">☰</button>

          <div>
            <div class="page-title">Portfolio</div>
            <div class="page-subtitle">Add, edit, delete, or archive holdings.</div>
          </div>
        </div>

        <div class="topbar-right">
          <div class="search">
            <input id="portfolioSearch" type="text" placeholder="Filter by ticker" />
            <button id="portfolioSearchClear" class="btn btn-ghost">Clear</button>
          </div>
        </div>
      </header>

      <section class="content">
        <div class="grid grid-2">

          <div class="card">
            <div class="card-header">
              <div class="card-title">Add / Edit Holding</div>
              <div class="chip chip-blue" id="formModeChip">Add</div>
            </div>

            <form id="holdingForm" class="form">
              <input type="hidden" id="holdingId" />

              <label class="label">Ticker</label>
              <input id="ticker" class="input" placeholder="AAPL" maxlength="10" required />

              <div class="form-row">
                <div class="form-col">
                  <label class="label">Shares</label>
                  <input id="shares" class="input" type="number" step="0.0001" min="0" placeholder="10" required />
                </div>
                <div class="form-col">
                  <label class="label">Average Cost</label>
                  <input id="avgCost" class="input" type="number" step="0.01" min="0" placeholder="150.00" required />
                </div>
              </div>

              <label class="label">Notes</label>
              <textarea id="notes" class="input textarea" placeholder="Reason for buying, thesis, etc."></textarea>

              <div class="form-actions">
                <button type="submit" class="btn btn-primary" id="saveBtn">Save</button>
                <button type="button" class="btn btn-ghost" id="resetBtn">Reset</button>
              </div>

              <div class="muted small">Stored locally in your browser.</div>
            </form>
          </div>

          <div class="card">
            <div class="card-header">
              <div class="card-title">Summary</div>
              <div class="chip chip-green">Totals</div>
            </div>

            <div class="summary-grid">
              <div class="summary-item">
                <div class="summary-label">Manual Value</div>
                <div class="summary-value" id="summaryValue">$0.00</div>
              </div>
              <div class="summary-item">
                <div class="summary-label">Active</div>
                <div class="summary-value" id="summaryActive">0</div>
              </div>
              <div class="summary-item">
                <div class="summary-label">Archived</div>
                <div class="summary-value" id="summaryArchived">0</div>
              </div>
            </div>

            <!-- add live api -->
          </div>

        </div> <!-- closes  -->

        <div class="grid grid-1">
          <div class="card">
            <div class="card-header">
              <div class="card-title">Holdings</div>
              <div class="actions">
                <button class="btn btn-ghost" id="showActiveBtn">Active</button>
                <button class="btn btn-ghost" id="showArchivedBtn">Archived</button>
                <button class="btn btn-danger" id="dangerClearAllBtn" title="Deletes ALL holdings permanently">
                  Danger: Clear All
                </button>
              </div>
            </div>

            <div class="table-wrap">
              <table class="table" id="holdingsTable">
                <thead>
                  <tr>
                    <th>Ticker</th>
                    <th>Shares</th>
                    <th>Avg Cost</th>
                    <th>Status</th>
                    <th>Notes</th>
                    <th class="right">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr><td class="muted" colspan="6">No holdings yet. Add one above.</td></tr>
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </section>

      <footer class="footer">
        <span>Stock Pilot • Portfolio</span>
      </footer>
    </main>
  </div>

  <script type="module" src="assets/js/sidebar.js"></script>
  <script type="module" src="assets/js/portfolio.js"></script>
</body>
</html>