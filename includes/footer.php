<?php
/**
 * includes/footer.php
 * Closes </div><!-- .main -->, renders toast container, loads shared JS, then inline page JS.
 * Usage:
 *   <?php
 *     ob_start(); // capture inline JS written after footer include
 *   ?>
 *   <?php include 'includes/footer.php'; ?>
 *   <script>
 *     // page-specific JS here
 *   </script>
 *   </body></html>
 *
 * The footer itself just provides the toast div + lms-shared.js load + sidebar/dark wiring.
 * $footerSearchAction — JS string to run on Enter in top-search (e.g. "location.href='admin-books.php'")
 */
$searchAction = $footerSearchAction ?? "location.href='#'";
?>
  </main><!-- /.content -->
</div><!-- /.main -->

<div id="toast-container"></div>
<script src="../assets/js/lms-shared.js"></script>
<script>
/* ── Shared init (sidebar, dark mode, topbar search) ── */
(function () {
  const sb  = document.getElementById('sidebar');
  const ov  = document.getElementById('overlay');
  // menu button may be named `menu-btn` on most pages, or `hamburger` on
  // the public homepage – make sure we find whichever exists.
  const mb  = document.getElementById('menu-btn') || document.getElementById('hamburger');
  const dt  = document.getElementById('dark-toggle');
  const ts  = document.getElementById('top-search');

  if (mb && sb && ov) {
    mb.addEventListener('click', () => { sb.classList.toggle('open'); ov.classList.toggle('show'); });
    ov.addEventListener('click', () => { sb.classList.remove('open');  ov.classList.remove('show'); });
  }
  if (dt) dt.addEventListener('click', LMS.toggleTheme);
  if (ts) ts.addEventListener('keydown', e => { if (e.key === 'Enter') { <?= $searchAction ?>; } });

  /* Restore saved theme (use same storage key as LMS.initTheme) */
  const saved = localStorage.getItem('lms-theme');
  if (saved) document.documentElement.setAttribute('data-theme', saved);
})();

  LMS.initPage();
</script>
