const COLLAPSE_KEY = "stock_pilot_sidebar_collapsed_v1";

export function initSidebarToggle() {
  const btn = document.getElementById("sidebarToggle");
  if (!btn) return;

  // Restore collapsed 
  const saved = localStorage.getItem(COLLAPSE_KEY);
  if (saved === "1") document.body.classList.add("sidebar-collapsed");

  const toggle = () => {
    // Mobile: open/close 
    if (window.matchMedia("(max-width: 900px)").matches) {
      document.body.classList.toggle("sidebar-open");
      return;
    }

    // Desktop: collapse/expand sidebar
    document.body.classList.toggle("sidebar-collapsed");
    localStorage.setItem(
      COLLAPSE_KEY,
      document.body.classList.contains("sidebar-collapsed") ? "1" : "0"
    );
  };

  //  onclick to avoid 
  btn.onclick = toggle;

  //  closes mobile drawer
  document.addEventListener("click", (e) => {
    if (!document.body.classList.contains("sidebar-open")) return;
    const sidebar = document.querySelector(".sidebar");
    const clickedInsideSidebar = sidebar && sidebar.contains(e.target);
    const clickedToggle = btn.contains(e.target);
    if (!clickedInsideSidebar && !clickedToggle) {
      document.body.classList.remove("sidebar-open");
    }
  });

  // ESC closes 
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") document.body.classList.remove("sidebar-open");
  });
}

initSidebarToggle();