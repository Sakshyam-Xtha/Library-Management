<?php 
session_start();
$currentTheme = isset($_SESSION['theme']) ? $_SESSION['theme'] : 'light';
 ?>
<!doctype html>
<html lang="en" data-theme="<?php echo $currentTheme; ?>">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register — LMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="/assets/css/register.css">
  </head>
  <body>
    <!-- Top Bar -->
    <header class="topbar">
      <a href="index.php" class="logo">📚 <span>LMS</span></a>
      <div class="topbar-right">
        <a href="login.php" class="topbar-link pill">Sign in</a>
        <a href="index.php" class="topbar-link">← Back to Home</a>
        <button id="dark-toggle" aria-label="Toggle dark mode"></button>
      </div>
    </header>

    <!-- Main -->
    <main class="page">
      <!-- Left decorative panel -->
      <div class="panel-left">
        <div class="deco-arc arc-1"></div>
        <div class="deco-arc arc-2"></div>
        <div class="deco-arc arc-3"></div>

        <div class="panel-left-content">
          <div class="panel-badge">✦ Free to Join</div>
          <h2 class="panel-title">
            Start your<br /><em>reading</em><br />journey today
          </h2>
          <p class="panel-desc">
            Create a free account and unlock access to our full catalogue,
            borrow books, and track your reading history.
          </p>

          <div class="steps">
            <div class="step">
              <div class="step-num">1</div>
              <div class="step-body">
                <h4>Create your profile</h4>
                <p>Fill in your details to set up your member account.</p>
              </div>
            </div>
            <div class="step">
              <div class="step-num">2</div>
              <div class="step-body">
                <h4>Browse the catalogue</h4>
                <p>Explore 1,200+ books across every genre and subject.</p>
              </div>
            </div>
            <div class="step">
              <div class="step-num">3</div>
              <div class="step-body">
                <h4>Borrow & enjoy</h4>
                <p>
                  Reserve and borrow books with automated due-date reminders.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right form panel -->
      <div class="panel-right">
        <div class="form-box">
          <p class="form-eyebrow">New Member</p>
          <h1 class="form-title">Create Account</h1>
          <p class="form-subtitle">
            Already have an account? <a href="login.php">Sign in →</a>
          </p>

          <!-- Progress indicator -->
          <div class="form-progress">
            <div class="progress-step">
              <div class="progress-dot active" id="dot-1">1</div>
            </div>
            <div class="progress-line"></div>
            <div class="progress-step">
              <div class="progress-dot" id="dot-2">2</div>
            </div>
            <div class="progress-line"></div>
            <div class="progress-step">
              <div class="progress-dot" id="dot-3">✓</div>
            </div>
          </div>

          <!-- Alert -->
          <div class="alert" id="form-alert" role="alert"></div>

          <form id="reg-form" novalidate autocomplete="off" action="actions/register_process.php" method="post">
            <!-- Name row -->
            <div class="form-row">
              <div class="field" id="field-fname">
                <label for="fname">First Name</label>
                <div class="input-wrap">
                  <span class="input-icon">👤</span>
                  <input
                    type="text"
                    id="fname"
                    name="fname"
                    placeholder="Jane"
                    autocomplete="given-name"
                  />
                </div>
                <p class="field-msg" id="msg-fname"></p>
              </div>
              <div class="field" id="field-lname">
                <label for="lname">Last Name</label>
                <div class="input-wrap">
                  <span class="input-icon">👤</span>
                  <input
                    type="text"
                    id="lname"
                    name="lname"
                    placeholder="Doe"
                    autocomplete="family-name"
                  />
                </div>
                <p class="field-msg" id="msg-lname"></p>
              </div>
            </div>

            <!-- Email -->
            <div class="field" id="field-email">
              <label for="email">Email Address</label>
              <div class="input-wrap">
                <span class="input-icon">✉</span>
                <input
                  type="email"
                  id="email"
                  name="email"
                  placeholder="you@example.com"
                  autocomplete="email"
                />
              </div>
              <p class="field-msg" id="msg-email"></p>
            </div>

            <!-- Role -->
            <div class="field" id="field-role">
              <label for="role">Account Type</label>
              <div class="input-wrap">
                <span class="input-icon">🎓</span>
                <select id="role" name="role">
                  <option value="" disabled selected>Select your role</option>
                  <option value="user">Guest</option>
                  <option value="member">Library Member</option>
                  <option value="student">Student</option>
                  <option value="staff">Faculty / Staff</option>
                  <option value="admin">Administrator</option>
                </select>
              </div>
              <p class="field-msg" id="msg-role"></p>
            </div>

            <!-- Password -->
            <div class="field" id="field-password">
              <label for="password">Password</label>
              <div class="input-wrap">
                <span class="input-icon">🔒</span>
                <input
                  type="password"
                  id="password"
                  name="password"
                  placeholder="Create a strong password"
                  autocomplete="new-password"
                />
                <button
                  type="button"
                  class="pw-toggle"
                  id="pw-toggle-1"
                  aria-label="Show password"
                >
                  👁
                </button>
              </div>
              <!-- Strength bar -->
              <div class="strength-bar">
                <div class="strength-segment" id="seg-1"></div>
                <div class="strength-segment" id="seg-2"></div>
                <div class="strength-segment" id="seg-3"></div>
                <div class="strength-segment" id="seg-4"></div>
              </div>
              <p class="strength-label" id="strength-label"></p>
              <p class="field-msg" id="msg-password"></p>
            </div>

            <!-- Confirm Password -->
            <div class="field" id="field-confirm">
              <label for="confirm">Confirm Password</label>
              <div class="input-wrap">
                <span class="input-icon">🔑</span>
                <input
                  type="password"
                  id="confirm"
                  name="confirm"
                  placeholder="Repeat your password"
                  autocomplete="new-password"
                />
                <button
                  type="button"
                  class="pw-toggle"
                  id="pw-toggle-2"
                  aria-label="Show password"
                >
                  👁
                </button>
              </div>
              <p class="field-msg" id="msg-confirm"></p>
            </div>

            <!-- Terms -->
            <div class="terms-row">
              <input type="checkbox" id="terms" name="terms" />
              <label for="terms">
                I agree to the <a href="terms.php">Terms of Service</a> and
                <a href="privacy.php">Privacy Policy</a>. I understand my borrowing
                history may be used to improve recommendations.
              </label>
            </div>

            <input type="hidden" name="register_btn" value="1">

            <!-- Submit -->
            <button type="submit" class="btn-submit" id="btn-submit" name="register_btn">
              <span class="btn-text">Create Account</span>
              <div class="spinner"></div>
            </button>
          </form>
        </div>
      </div>
    </main>

    <script>
      /* ─── Dark mode ─── */
      document.getElementById("dark-toggle").addEventListener("click", () => {
        const t = document.documentElement;
        t.setAttribute(
          "data-theme",
          t.getAttribute("data-theme") === "dark" ? "light" : "dark",
        );
      });

      /* ─── Password toggles ─── */
      function makePwToggle(inputId, btnId) {
        const input = document.getElementById(inputId);
        const btn = document.getElementById(btnId);
        btn.addEventListener("click", () => {
          const show = input.type === "password";
          input.type = show ? "text" : "password";
          btn.textContent = show ? "🙈" : "👁";
        });
      }
      makePwToggle("password", "pw-toggle-1");
      makePwToggle("confirm", "pw-toggle-2");

      /* ─── Password strength ─── */
      const segments = [1, 2, 3, 4].map((i) =>
        document.getElementById(`seg-${i}`),
      );
      const strengthLabel = document.getElementById("strength-label");
      const colors = ["#e74c3c", "#e67e22", "#f1c40f", "#27ae60"];
      const labels = ["Weak", "Fair", "Good", "Strong"];

      function calcStrength(pw) {
        let s = 0;
        if (pw.length >= 8) s++;
        if (/[A-Z]/.test(pw)) s++;
        if (/[0-9]/.test(pw)) s++;
        if (/[^A-Za-z0-9]/.test(pw)) s++;
        return s;
      }

      document
        .getElementById("password")
        .addEventListener("input", function () {
          const pw = this.value;
          const strength = pw ? calcStrength(pw) : 0;
          segments.forEach((seg, i) => {
            seg.style.background =
              i < strength ? colors[strength - 1] : "var(--border)";
          });
          strengthLabel.textContent = pw
            ? `Strength: ${labels[strength - 1] || "Very Weak"}`
            : "";
        });

      /* ─── Validation helpers ─── */
      function setField(fieldId, msgId, state, msg) {
        const input = document.querySelector(
          `#${fieldId} input, #${fieldId} select`,
        );
        const el = document.getElementById(msgId);
        input.classList.remove("error", "success");
        el.className = "field-msg";
        if (state) {
          input.classList.add(state);
          el.classList.add(state);
          el.textContent = msg;
        } else {
          el.textContent = "";
        }
      }

      function showAlert(type, msg) {
        const el = document.getElementById("form-alert");
        el.className = `alert ${type} show`;
        el.innerHTML = (type === "error" ? "⚠ " : "✓ ") + msg;
      }

      function validateEmail(v) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
      }

      /* ─── Live validation ─── */
      document.getElementById("fname").addEventListener("blur", function () {
        if (!this.value.trim())
          setField(
            "field-fname",
            "msg-fname",
            "error",
            "First name is required.",
          );
        else setField("field-fname", "msg-fname", "success", "");
      });
      document.getElementById("lname").addEventListener("blur", function () {
        if (!this.value.trim())
          setField(
            "field-lname",
            "msg-lname",
            "error",
            "Last name is required.",
          );
        else setField("field-lname", "msg-lname", "success", "");
      });
      document.getElementById("email").addEventListener("blur", function () {
        if (!this.value)
          return setField(
            "field-email",
            "msg-email",
            "error",
            "Email is required.",
          );
        if (!validateEmail(this.value))
          return setField(
            "field-email",
            "msg-email",
            "error",
            "Enter a valid email.",
          );
        setField("field-email", "msg-email", "success", "");
      });
      document.getElementById("role").addEventListener("change", function () {
        if (!this.value)
          setField("field-role", "msg-role", "error", "Please select a role.");
        else setField("field-role", "msg-role", "success", "");
      });
      document.getElementById("password").addEventListener("blur", function () {
        if (!this.value)
          return setField(
            "field-password",
            "msg-password",
            "error",
            "Password is required.",
          );
        if (this.value.length < 8)
          return setField(
            "field-password",
            "msg-password",
            "error",
            "Must be at least 8 characters.",
          );
        setField("field-password", "msg-password", "success", "");
      });
      document.getElementById("confirm").addEventListener("blur", function () {
        const pw = document.getElementById("password").value;
        if (!this.value)
          return setField(
            "field-confirm",
            "msg-confirm",
            "error",
            "Please confirm your password.",
          );
        if (this.value !== pw)
          return setField(
            "field-confirm",
            "msg-confirm",
            "error",
            "Passwords do not match.",
          );
        setField(
          "field-confirm",
          "msg-confirm",
          "success",
          "Passwords match ✓",
        );
      });

      /* ─── Progress dots update on scroll ─── */
      const fields = ["fname", "email", "password"];
      function updateProgress() {
        const vals = {
          fname: !!document.getElementById("fname").value.trim(),
          email: validateEmail(document.getElementById("email").value),
          password: document.getElementById("password").value.length >= 8,
        };
        const filled = Object.values(vals).filter(Boolean).length;
        ["dot-1", "dot-2", "dot-3"].forEach((id, i) => {
          const dot = document.getElementById(id);
          dot.className = "progress-dot";
          if (i < filled) dot.classList.add("done");
          else if (i === filled) dot.classList.add("active");
        });
      }
      ["fname", "email", "password"].forEach((id) => {
        document.getElementById(id).addEventListener("input", updateProgress);
      });

      /* ─── Form Submit ─── */
      document
        .getElementById("reg-form")
        .addEventListener("submit", async function (e) {
          e.preventDefault();

          const fname = document.getElementById("fname").value.trim();
          const lname = document.getElementById("lname").value.trim();
          const email = document.getElementById("email").value.trim();
          const role = document.getElementById("role").value;
          const pw = document.getElementById("password").value;
          const confirm = document.getElementById("confirm").value;
          const terms = document.getElementById("terms").checked;

          const form_data = {
            fname,
            lname,
            email,
            pw
          }

          let valid = true;

          if (!fname) {
            setField(
              "field-fname",
              "msg-fname",
              "error",
              "First name is required.",
            );
            valid = false;
          }
          if (!lname) {
            setField(
              "field-lname",
              "msg-lname",
              "error",
              "Last name is required.",
            );
            valid = false;
          }
          if (!email || !validateEmail(email)) {
            setField(
              "field-email",
              "msg-email",
              "error",
              "Valid email required.",
            );
            valid = false;
          }
          if (!role) {
            setField(
              "field-role",
              "msg-role",
              "error",
              "Please select a role.",
            );
            valid = false;
          }
          if (!pw || pw.length < 8) {
            setField(
              "field-password",
              "msg-password",
              "error",
              "Password must be at least 8 characters.",
            );
            valid = false;
          }
          if (pw !== confirm) {
            setField(
              "field-confirm",
              "msg-confirm",
              "error",
              "Passwords do not match.",
            );
            valid = false;
          }
          if (!terms) {
            showAlert(
              "error",
              "You must agree to the Terms of Service to continue.",
            );
            valid = false;
          }

          if (!valid) return;

          const btn = document.getElementById("btn-submit");
          btn.classList.add("loading");
          btn.disabled = true;

          try {
              const response = await fetch('actions/register_process.php', {
                  method: "POST",
                  headers: { "Content-Type": "application/json" },
                  body: JSON.stringify(form_data)
              });

              const result = await response.json(); // Parse the JSON from PHP

              if (result.status === "success") {
                  showAlert("success", result.message);
                  // Mark progress as done
                  ["dot-1", "dot-2", "dot-3"].forEach(id => {
                      document.getElementById(id).className = "progress-dot done";
                  });
                  // Redirect after a short delay
                  setTimeout(() => { window.location.href = 'login.php'; }, 2000);
              } else {
                  showAlert("error", result.message);
              }
          } catch (error) {
              showAlert("error", "Network error. Please try again.");
          } finally {
              btn.classList.remove("loading");
              btn.disabled = false;
          }     
        });
    </script>
  </body>
</html>
