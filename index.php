<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$currentTheme = isset($_SESSION['theme']) ? $_SESSION['theme'] : 'light';
$usrRole = $_SESSION['role'];
if ($usrRole === "admin"){
  header("Location: admin/admin-dashboard.php");
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?php echo $currentTheme; ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>LMS — Library Management System</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

  <!-- Scroll progress -->
  <div id="progress-bar"></div>

  <!-- ─── Navbar ─── -->
  <nav id="navbar">
    <a href="#home" class="nav-logo">📚 <span>LMS</span></a>

    <!-- Desktop links (injected by JS) -->
    <ul class="nav-links" id="nav-links"></ul>

    <div class="nav-right">
      <p><?php if ($isLoggedIn){
        echo $_SESSION['user_name'];
      }
        ?></p>
      <button id="dark-toggle" aria-label="Toggle dark mode"></button>
      <button class="hamburger" id="hamburger" aria-label="Open menu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </nav>

  <!-- Mobile menu -->
  <div class="mobile-menu" id="mobile-menu"></div>

  <!-- ─── Hero ─── -->
  <section id="home">
    <div class="hero-bg"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <div class="hero-content">
      <div class="hero-badge">✦ Next-Gen Library Platform</div>

      <h1 class="hero-title">
        Manage Your Library<br />with <em>Elegance</em>
      </h1>

      <p class="hero-desc">
        A powerful, intuitive platform for cataloguing books, managing members, and streamlining your borrowing workflow — all in one place.
      </p>

      <div class="hero-cta">
        <a href="register.php" class="btn btn-primary">Get Started →</a>
        <a href="login.php" class="btn btn-outline" id="cta-login">Login</a>
      </div>

      <div class="hero-visual">📖</div>
    </div>
  </section>

  <!-- ─── Features ─── -->
  <section id="features">
    <div style="max-width:1100px; margin:0 auto;">
      <div class="section-header">
        <span class="section-label">Why Choose LMS</span>
        <h2 class="section-title">Everything Your<br />Library Needs</h2>
        <p class="section-desc">From cataloguing to tracking, our platform handles every aspect of modern library management.</p>
      </div>

      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon">📚</div>
          <h3>Book Management</h3>
          <p>Catalog thousands of titles with rich metadata, cover images, genres, and availability status — all searchable in real time.</p>
          <span class="feature-tag">Core Feature</span>
        </div>
        <div class="feature-card">
          <div class="feature-icon">👤</div>
          <h3>User Accounts</h3>
          <p>Role-based access for administrators and members. Track borrowing history, reservations, and notifications per user.</p>
          <span class="feature-tag">Multi-Role</span>
        </div>
        <div class="feature-card">
          <div class="feature-icon">🔄</div>
          <h3>Borrow & Return</h3>
          <p>Automated due-date tracking, overdue alerts, and one-click renewals. Make lending seamless for staff and members alike.</p>
          <span class="feature-tag">Automated</span>
        </div>
      </div>
    </div>
  </section>

  <!-- ─── Stats ─── -->
  <section id="stats">
    <div class="stats-inner">
      <span class="section-label">By the Numbers</span>
      <h2 class="section-title">Trusted by Readers<br />Worldwide</h2>

      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon">📗</div>
          <div class="stat-number"><span class="counter" data-target="1200">0</span><span>+</span></div>
          <div class="stat-label">Books Catalogued</div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">🧑‍🤝‍🧑</div>
          <div class="stat-number"><span class="counter" data-target="300">0</span><span>+</span></div>
          <div class="stat-label">Registered Members</div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">📋</div>
          <div class="stat-number"><span class="counter" data-target="150">0</span><span>+</span></div>
          <div class="stat-label">Active Borrowings</div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">⭐</div>
          <div class="stat-number"><span class="counter" data-target="98">0</span><span>%</span></div>
          <div class="stat-label">Satisfaction Rate</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ─── Footer ─── -->
  <footer>
    <div class="footer-logo">📚 <span>LMS</span></div>
    <p class="footer-copy">© 2025 Library Management System. All rights reserved.</p>
    <nav class="footer-links">
      <a href="#home">Home</a>
      <a href="#features">Features</a>
      <a href="#">Privacy</a>
    </nav>
  </footer>

  <!-- ─── JavaScript ─── -->
  <script>
    /* ── Real auth state from PHP ── */
    let isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;// toggle to true to see Dashboard/Logout

    /* ── Build nav links ── */
    function buildNav() {
      const guestLinks = [
        { label: 'Home',     href: '#home' },
        { label: 'Features', href: '#features' },
        { label: 'Login',    href: '/login.php',    cls: '' },
        { label: 'Register', href: '/register.php',    cls: 'btn-nav' },
      ];
      const authLinks = [
        { label: 'Home',      href: '#home' },
        { label: 'Features',  href: '#features' },
        { label: 'Dashboard', href: 'user/user-dashboard.php',    cls: 'btn-nav' },
        { label: 'Logout',    href: 'actions/logout.php',    cls: '' },
      ];

      const links = isLoggedIn ? authLinks : guestLinks;
      const container = document.getElementById('nav-links');
      const mobile    = document.getElementById('mobile-menu');

      container.innerHTML = '';
      mobile.innerHTML    = '';

      links.forEach(l => {
        const li = document.createElement('li');
        const a  = document.createElement('a');
        a.href = l.href; a.textContent = l.label;
        if (l.cls) a.classList.add(l.cls);
        li.appendChild(a); container.appendChild(li);

        const ma = a.cloneNode(true);
        mobile.appendChild(ma);
      });

      // mobile dark toggle clone
      const dtClone = document.createElement('button');
      dtClone.id = 'dark-toggle-mobile';
      dtClone.setAttribute('aria-label','Toggle dark mode');
      dtClone.style.cssText = document.getElementById('dark-toggle').style.cssText;
      dtClone.textContent = '☀️ / 🌙';
      dtClone.style.background = 'none'; dtClone.style.border = 'none';
      dtClone.style.cursor = 'pointer'; dtClone.style.fontSize = '1.1rem';
      dtClone.addEventListener('click', toggleDark);
      mobile.appendChild(dtClone);
    }

    /* ── Dark mode ── */
    const root = document.documentElement;
    function toggleDark() {
      // 1. Determine the new theme
      const currentTheme = root.getAttribute('data-theme');
      const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

      // 2. Update the UI immediately (No lag for the user)
      root.setAttribute('data-theme', newTheme);

      // 3. Save to Session via AJAX
      // We send this to the current page ('') since the PHP is at the top
      fetch('actions/save_theme.php', { 
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'theme=' + encodeURIComponent(newTheme)
      })
      .then(response => {
        if (!response.ok) console.error('Failed to save theme to session');
      })
      .catch(err => console.error('Network error:', err));
    }
    document.getElementById('dark-toggle').addEventListener('click', toggleDark);

    /* ── Hamburger ── */
    const hamburger   = document.getElementById('hamburger');
    const mobileMenu  = document.getElementById('mobile-menu');

    hamburger.addEventListener('click', () => {
      hamburger.classList.toggle('open');
      mobileMenu.classList.toggle('open');
    });
    mobileMenu.addEventListener('click', e => {
      if (e.target.tagName === 'A') {
        hamburger.classList.remove('open');
        mobileMenu.classList.remove('open');
      }
    });

    /* ── Scroll progress ── */
    const progressBar = document.getElementById('progress-bar');
    window.addEventListener('scroll', () => {
      const scrolled = window.scrollY;
      const total    = document.documentElement.scrollHeight - window.innerHeight;
      progressBar.style.width = (scrolled / total * 100) + '%';
    }, { passive: true });

    /* ── Scroll reveal (IntersectionObserver) ── */
    const revealObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry, i) => {
        if (entry.isIntersecting) {
          setTimeout(() => {
            entry.target.classList.add('visible');
          }, i * 120);
          revealObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });

    document.querySelectorAll('.feature-card, .stat-card').forEach(el => {
      revealObserver.observe(el);
    });

    /* ── Animated counters ── */
    function animateCounter(el) {
      const target   = parseInt(el.dataset.target, 10);
      const duration = 1800;
      const start    = performance.now();

      function step(now) {
        const progress = Math.min((now - start) / duration, 1);
        const ease     = 1 - Math.pow(1 - progress, 3); // ease-out-cubic
        el.textContent = Math.floor(ease * target);
        if (progress < 1) requestAnimationFrame(step);
        else el.textContent = target;
      }
      requestAnimationFrame(step);
    }

    const counterObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.querySelectorAll('.counter').forEach(animateCounter);
          counterObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.3 });

    counterObserver.observe(document.getElementById('stats'));

    /* ── Init ── */
    buildNav();
  </script>
</body>
</html>