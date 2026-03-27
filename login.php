<?php 
session_start();
$currentTheme = isset($_SESSION['theme']) ? $_SESSION['theme'] : 'light';
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?php echo $currentTheme ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login — LMS</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/assets/css/login.css">
  
</head>
<body>

  <!-- Top Bar -->
  <header class="topbar">
    <a href="index.php" class="logo">📚 <span>LMS</span></a>
    <div class="topbar-right">
      <a href="register.php" class="topbar-link pill">Create account</a>
      <a href="index.php"    class="topbar-link">← Back to Home</a>
      <button id="dark-toggle" aria-label="Toggle dark mode"></button>
    </div>
  </header>

  <!-- Main -->
  <main class="page">

    <!-- Left decorative panel -->
    <div class="panel-left">
      <div class="deco-circle deco-circle-1"></div>
      <div class="deco-circle deco-circle-2"></div>
      <div class="deco-circle deco-circle-3"></div>

      <div class="panel-left-content">
        <div class="panel-badge">📚 Library Portal</div>
        <h2 class="panel-title">Welcome<br />back to your<br /><em>Library</em></h2>
        <p class="panel-desc">Sign in to access your personal reading dashboard, manage borrowings, and explore our catalogue.</p>

        <ul class="panel-features">
          <li>Track all your borrowed books</li>
          <li>Reserve titles in advance</li>
          <li>Get due-date notifications</li>
          <li>Browse 1,200+ catalogued books</li>
        </ul>

        <div class="panel-quote">
          <p>"A library is not a luxury but one of the necessities of life."</p>
          <div class="panel-quote-author">— Henry Ward Beecher</div>
        </div>
      </div>
    </div>

    <!-- Right form panel -->
    <div class="panel-right">
      <div class="form-box">
        <p class="form-eyebrow">Member Access</p>
        <h1 class="form-title">Sign In</h1>
        <p class="form-subtitle">
          Don't have an account? <a href="register.html">Create one free →</a>
        </p>

        <!-- Alert -->
        <div class="alert" id="form-alert" role="alert"></div>

        <form id="login-form" novalidate action="actions/login_process.php" method="post">
          <!-- Email -->
          <div class="field" id="field-email">
            <label for="email">Email Address</label>
            <div class="input-wrap">
              <span class="input-icon">✉</span>
              <input type="email" id="email" name="email"
                     placeholder="you@example.com" autocomplete="email" />
            </div>
            <p class="field-msg" id="msg-email"></p>
          </div>

          <!-- Password -->
          <div class="field" id="field-password">
            <label for="password">Password</label>
            <div class="input-wrap">
              <span class="input-icon">🔒</span>
              <input type="password" id="password" name="password"
                     placeholder="Enter your password" autocomplete="current-password" />
              <button type="button" class="pw-toggle" id="pw-toggle" aria-label="Show password">👁</button>
            </div>
            <p class="field-msg" id="msg-password"></p>
          </div>

          <!-- Options -->
          <div class="options-row">
            <label class="checkbox-wrap">
              <input type="checkbox" id="remember" name="remember" />
              <span>Remember me</span>
            </label>
            <a href="#" class="forgot-link">Forgot password?</a>
          </div>

          <input type="hidden" name="login_btn" value="1">
          <!-- Submit -->
          <button type="submit" class="btn-submit" id="btn-submit" name="login_btn">
            <span class="btn-text">Sign In</span>
            <div class="spinner"></div>
          </button>
        </form>

        <div class="or-divider"><span>or continue with</span></div>

        <div class="social-row">
          <a href="#" class="btn-social">
            <svg width="18" height="18" viewBox="0 0 48 48" fill="none">
              <path d="M44.5 20H24v8.5h11.8C34.7 33.9 29.1 37 24 37c-7.2 0-13-5.8-13-13s5.8-13 13-13c3.1 0 5.9 1.1 8.1 2.9l6-6C34.6 5.1 29.6 3 24 3 12.4 3 3 12.4 3 24s9.4 21 21 21c10.5 0 20-7.6 20-21 0-1.3-.2-2.7-.5-4z" fill="#FFC107"/>
              <path d="M6.3 14.7l7 5.1C15.1 16 19.3 13 24 13c3.1 0 5.9 1.1 8.1 2.9l6-6C34.6 5.1 29.6 3 24 3c-7.6 0-14.2 4.3-17.7 10.7z" fill="#FF3D00"/>
              <path d="M24 45c5.5 0 10.4-1.9 14.3-5.1l-6.6-5.6C29.6 36 26.9 37 24 37c-5 0-9.3-3-11.8-7.5l-7 5.4C8.8 41.1 15.8 45 24 45z" fill="#4CAF50"/>
              <path d="M44.5 20H24v8.5h11.8c-.6 2.2-1.9 4.1-3.7 5.4l6.6 5.6C43.1 36.2 45 30.4 45 24c0-1.3-.2-2.7-.5-4z" fill="#1976D2"/>
            </svg>
            Google
          </a>
          <a href="#" class="btn-social">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 0C5.37 0 0 5.37 0 12c0 5.3 3.44 9.8 8.2 11.39.6.11.82-.26.82-.58v-2.03c-3.34.72-4.04-1.61-4.04-1.61-.54-1.38-1.33-1.74-1.33-1.74-1.08-.74.08-.73.08-.73 1.2.08 1.83 1.23 1.83 1.23 1.06 1.82 2.79 1.3 3.47.99.1-.77.42-1.3.76-1.6-2.66-.3-5.47-1.33-5.47-5.93 0-1.31.47-2.38 1.24-3.22-.12-.3-.54-1.52.12-3.17 0 0 1.01-.32 3.3 1.23a11.5 11.5 0 0 1 3-.4c1.02 0 2.04.14 3 .4 2.28-1.55 3.29-1.23 3.29-1.23.66 1.65.24 2.87.12 3.17.77.84 1.24 1.91 1.24 3.22 0 4.61-2.81 5.63-5.48 5.92.43.37.81 1.1.81 2.22v3.29c0 .32.22.7.83.58C20.57 21.79 24 17.3 24 12c0-6.63-5.37-12-12-12z"/>
            </svg>
            GitHub
          </a>
        </div>
      </div>
    </div>
  </main>

  <script>
    /* ─── Dark mode ─── */
    document.getElementById('dark-toggle').addEventListener('click', () => {
      const t = document.documentElement;
      t.setAttribute('data-theme', t.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
    });

    /* ─── Password toggle ─── */
    const pwInput  = document.getElementById('password');
    const pwToggle = document.getElementById('pw-toggle');
    pwToggle.addEventListener('click', () => {
      const show = pwInput.type === 'password';
      pwInput.type   = show ? 'text' : 'password';
      pwToggle.textContent = show ? '🙈' : '👁';
    });

    /* ─── Validation helpers ─── */
    function setFieldState(fieldId, msgId, state, msg) {
      const input = document.querySelector(`#${fieldId} input`);
      const msgEl = document.getElementById(msgId);
      input.classList.remove('error', 'success');
      msgEl.className = 'field-msg';
      if (state) {
        input.classList.add(state);
        msgEl.classList.add(state);
        msgEl.textContent = msg;
      } else {
        msgEl.textContent = '';
      }
    }

    function showAlert(type, msg) {
      const el = document.getElementById('form-alert');
      el.className = `alert ${type} show`;
      el.innerHTML = (type === 'error' ? '⚠ ' : '✓ ') + msg;
    }
    function hideAlert() {
      document.getElementById('form-alert').className = 'alert';
    }

    function validateEmail(v) {
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
    }

    /* ─── Live validation ─── */
    document.getElementById('email').addEventListener('blur', function() {
      if (!this.value) return setFieldState('field-email','msg-email','error','Email is required.');
      if (!validateEmail(this.value)) return setFieldState('field-email','msg-email','error','Please enter a valid email.');
      setFieldState('field-email','msg-email','success','');
    });

    document.getElementById('password').addEventListener('blur', function() {
      if (!this.value) return setFieldState('field-password','msg-password','error','Password is required.');
      if (this.value.length < 6) return setFieldState('field-password','msg-password','error','At least 6 characters required.');
      setFieldState('field-password','msg-password','success','');
    });

    /* ─── Form submit ─── */
    document.getElementById('login-form').addEventListener('submit', async function(e) {
      e.preventDefault();
      hideAlert();

      const email = document.getElementById('email').value.trim();
      const pass  = document.getElementById('password').value;
      let valid = true;

      if (!email || !validateEmail(email)) {
        setFieldState('field-email','msg-email','error','Enter a valid email address.');
        valid = false;
      }
      if (!pass || pass.length < 6) {
        setFieldState('field-password','msg-password','error','Password must be at least 6 characters.');
        valid = false;
      }
      if (!valid) return;

      const btn = document.getElementById('btn-submit');
      btn.classList.add('loading');
      btn.disabled = true;

      // Simulate async auth
      await new Promise(r => setTimeout(r, 1800));

      const form_data = {email,pass}

      btn.classList.remove('loading');
      await fetch('../actions/login_process.php',{
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify(form_data)
      }).then(response => response.json())
      .then(data => {showAlert(data['status'], data['message']);
          if (data['status'] === 'success') {
            // Wait a moment so the user can actually read the success message
            setTimeout(() => {
                if (data['role'] === 'admin') {
                    window.location.href = '../admin/admin-dashboard.php';
                } else {
                    window.location.href = '../user/user-dashboard.php';
                }
            }, 1500);
          }else{
            btn.classList.remove('loading');
            btn.disabled = false;
            const passField = document.getElementById('password');
            if (passField) {
                passField.value = '';
                passField.focus(); 
            }
          }})
      .catch(error => {
        console.log("error occurred",error);
        btn.classList.remove('loading');
        showAlert('error', 'Network error. Please try again.');
        btn.disabled = false;
      });
    });
  </script>
</body>
</html>
