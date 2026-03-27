<?php
require_once('../includes/auth.php');
$pageTitle  = 'My Profile';
$activePage = 'profile';
$topbarRole = 'user';
$footerSearchAction = "location.href='user-catalogue.php'";
$extraCss = <<<'CSS'
.pwd-toggle-btn{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;font-size:1.1rem;cursor:pointer;padding:.35rem;border-radius:6px;transition:background var(--transition);color:var(--text-muted)}
.pwd-toggle-btn:hover{background:var(--surface-2);color:var(--text)}
.profile-hero{background:linear-gradient(135deg,var(--accent) 0%,#1e3a7a 100%);border-radius:var(--radius-lg);padding:2rem 2.5rem;display:flex;align-items:center;gap:2rem;margin-bottom:1.75rem;position:relative;overflow:hidden;animation:fadeUp .5s both}
.profile-hero::before{content:'';position:absolute;inset:0;background:url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Ccircle cx='20' cy='20' r='1.5'/%3E%3C/g%3E%3C/svg%3E")}
.profile-avatar-wrap{position:relative;z-index:1;flex-shrink:0}
.profile-avatar-big{width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,rgba(255,255,255,.3),rgba(255,255,255,.1));border:3px solid rgba(255,255,255,.4);display:grid;place-items:center;font-size:1.8rem;font-weight:900;color:#fff;cursor:pointer;transition:opacity .2s;user-select:none}
.profile-avatar-big:hover{opacity:.85}
.profile-hero-info{position:relative;z-index:1;flex:1}
.profile-name{font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:900;color:#fff;margin-bottom:.2rem}
.profile-role-line{font-size:.875rem;color:rgba(255,255,255,.7);margin-bottom:.75rem}
.profile-badges{display:flex;gap:.5rem;flex-wrap:wrap}
.p-badge{display:inline-flex;align-items:center;gap:.35rem;padding:.25rem .7rem;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);border-radius:99px;font-size:.75rem;color:rgba(255,255,255,.85);font-weight:600}
.p-badge-guest{background:rgba(255,200,50,.2);border-color:rgba(255,200,50,.45);color:#ffe066}
.profile-hero-stats{display:flex;gap:2rem;position:relative;z-index:1;flex-shrink:0}
.ph-stat{text-align:center}
.ph-num{font-family:'Playfair Display',serif;font-size:1.8rem;font-weight:900;color:#fff}
.ph-label{font-size:.72rem;color:rgba(255,255,255,.6);margin-top:.1rem}
.membership-banner{border-radius:var(--radius-lg);padding:1.4rem 1.75rem;margin-bottom:1.75rem;display:flex;align-items:center;gap:1.25rem;animation:fadeUp .4s .08s both;background:linear-gradient(135deg,#7c3aed18,#9333ea0e);border:1.5px solid #9333ea40}
.membership-banner.pending{background:linear-gradient(135deg,#f39c1218,#f39c120e);border-color:#f39c1240}
.mb-icon{font-size:2.2rem;flex-shrink:0}
.mb-info{flex:1}
.mb-title{font-family:'Playfair Display',serif;font-size:1rem;font-weight:800;color:var(--text);margin-bottom:.2rem}
.mb-sub{font-size:.84rem;color:var(--text-muted);line-height:1.55}
.profile-grid{display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.75rem}
.profile-section{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;animation:fadeUp .5s .15s both}
.profile-section.full-width{grid-column:span 2}
.ps-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem}
.ps-title{font-family:'Playfair Display',serif;font-size:1rem;font-weight:800;color:var(--text)}
.ps-edit{font-size:.8rem;font-weight:600;color:var(--accent);cursor:pointer;padding:.35rem .85rem;border-radius:8px;border:1.5px solid var(--border);transition:all var(--transition);background:none}
.ps-edit:hover{background:var(--accent-light);border-color:var(--accent)}
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
.info-field label{display:block;font-size:.75rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:.3rem}
.info-field input,.info-field select,.info-field textarea{width:100%;padding:.65rem .9rem;background:var(--surface-2);border:1.5px solid var(--border);border-radius:10px;font-size:.9rem;color:var(--text);outline:none;transition:border-color var(--transition),box-shadow var(--transition);font-family:inherit}
.info-field input:focus,.info-field select:focus,.info-field textarea:focus{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-light)}
.info-field input[readonly]{background:var(--surface-2);cursor:default;border-color:transparent}
.info-field select:disabled{background:var(--surface-2);cursor:default;border-color:transparent;opacity:1}
.info-field textarea{resize:vertical;min-height:80px}
.info-field.span-2{grid-column:1/-1}
.activity-strip{list-style:none;display:flex;flex-direction:column;gap:.75rem;margin:0;padding:0}
.as-item{display:flex;align-items:center;gap:.85rem;padding:.65rem;border-radius:10px;transition:background var(--transition);cursor:pointer}
.as-item:hover{background:var(--surface-2)}
.as-dot{width:34px;height:34px;border-radius:9px;display:grid;place-items:center;font-size:.9rem;flex-shrink:0}
.as-meta{flex:1}
.as-title{font-size:.875rem;font-weight:600;color:var(--text)}
.as-sub{font-size:.75rem;color:var(--text-muted)}
.achievement-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:.85rem}
.achievement{display:flex;flex-direction:column;align-items:center;gap:.45rem;padding:.85rem;border-radius:12px;border:1px solid var(--border);background:var(--surface-2);text-align:center;transition:all var(--transition)}
.achievement.locked{opacity:.35}
.achievement:not(.locked):hover{border-color:var(--accent-2);box-shadow:var(--shadow-sm)}
.ach-icon{font-size:1.8rem}
.ach-name{font-size:.75rem;font-weight:700;color:var(--text)}
.ach-desc{font-size:.68rem;color:var(--text-muted);line-height:1.3}
.pref-row{display:flex;align-items:center;justify-content:space-between;padding:.7rem 0;border-bottom:1px solid var(--border)}
.pref-row:last-child{border-bottom:none}
.pref-label{font-size:.9rem;font-weight:500;color:var(--text)}
.pref-sub{font-size:.75rem;color:var(--text-muted);margin-top:.1rem}
.toggle-switch{position:relative;width:42px;height:24px;flex-shrink:0}
.toggle-switch input{opacity:0;width:0;height:0;position:absolute}
.toggle-track{display:block;width:100%;height:100%;background:var(--surface-3);border-radius:99px;cursor:pointer;transition:background var(--transition);position:relative}
.toggle-switch input:checked+.toggle-track{background:var(--accent)}
.toggle-track::after{content:'';position:absolute;top:3px;left:3px;width:18px;height:18px;border-radius:50%;background:#fff;transition:transform var(--transition)}
.toggle-switch input:checked+.toggle-track::after{transform:translateX(18px)}
.type-btns{display:flex;gap:.6rem;flex-wrap:wrap;margin-bottom:.85rem}
.type-btn{padding:.45rem 1rem;border-radius:99px;font-size:.82rem;font-weight:600;border:1.5px solid var(--border);background:var(--surface-2);color:var(--text-muted);cursor:pointer;transition:all var(--transition)}
.type-btn.active{border-color:var(--accent);color:var(--accent);background:var(--accent-light)}
@media(max-width:800px){.profile-grid{grid-template-columns:1fr}.profile-section.full-width{grid-column:span 1}.profile-hero-stats{display:none}.info-grid{grid-template-columns:1fr}}
@media(max-width:600px){.profile-hero{flex-direction:column;text-align:center}.membership-banner{flex-direction:column;text-align:center}}
CSS;
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?php echo $currentTheme; ?>">
<head>
<?php include '../includes/head.php'; ?>
</head>
<body>

<?php include '../includes/user-sidebar.php'; ?>

<div class="main">
<?php include '../includes/topbar.php'; ?>

<main class="content">

  <div class="page-header">
    <div><p class="page-eyebrow">Account</p><h1 class="page-title">My Profile</h1><p class="page-subtitle">Manage your personal information and preferences</p></div>
    <button class="btn btn-primary" id="save-all-btn" style="display:none" onclick="saveProfile()">💾 Save Changes</button>
  </div>

  <!-- Hero Banner -->
  <div class="profile-hero">
    <div class="profile-avatar-wrap">
      <div class="profile-avatar-big" id="hero-avatar" title="Click to change avatar" onclick="document.getElementById('avatar-modal').classList.add('open')"><?php if($userName) {echo $initials;} else {echo "?";}?></div>
    </div>
    <div class="profile-hero-info">
      <div class="profile-name" id="hero-name"><?php if($userName) {echo $userName;} else {echo "Visitor";}?></div>
      <div class="profile-role-line" id="hero-role-line"><?php echo $userRole; ?></div>
      <div class="profile-badges" id="hero-badges">
        <span class="p-badge">📚 Active Reader</span>
        <span class="p-badge" id="badge-read">✅ 0 Books Read</span>
        <span class="p-badge" id="borrow-badge">📖 0 Active Loans</span>
      </div>
    </div>
    <div class="profile-hero-stats">
      <div class="ph-stat"><div class="ph-num" id="stat-read">0</div><div class="ph-label">Books Read</div></div>
      <div class="ph-stat"><div class="ph-num" id="hero-borrowed">0</div><div class="ph-label">Active Loans</div></div>
      <div class="ph-stat"><div class="ph-num" id="stat-wish">0</div><div class="ph-label">Wishlist</div></div>
    </div>
  </div>

  <!-- application banner -->
  <div id="membership-banner" class="apply-banner" style="display: none; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: white; padding: 1.5rem; border-radius: 12px; align-items: center; gap: 1.5rem; margin-bottom: 1.5rem;">
    <div style="font-size: 2rem;">🎓</div>
    <div style="flex: 1;">
        <h4 style="margin: 0; font-size: 1.1rem;">Become a Library Member</h4>
        <p style="margin: 0; font-size: 0.85rem; opacity: 0.9;">Apply now to borrow books and track your reading history.</p>
    </div>
    <button onclick="openApplicationModal()" style="background: white; color: #4f46e5; border: none; padding: 0.6rem 1.2rem; border-radius: 8px; font-weight: 700; cursor: pointer;">Apply Now</button>
</div>

  <!-- Pending Application Banner -->
  <div class="membership-banner pending" id="pending-banner" style="display:none">
    <div class="mb-icon">⏳</div>
    <div class="mb-info">
      <div class="mb-title">Application Under Review</div>
      <div class="mb-sub">Your membership application is awaiting admin approval. You'll be notified once a decision has been made.</div>
    </div>
    <span style="background:var(--warning-light);color:var(--warning);padding:.4rem .9rem;font-size:.8rem;font-weight:700;border-radius:8px;flex-shrink:0;white-space:nowrap">⏳ Pending Review</span>
  </div>

  <!-- Profile Grid -->
  <div class="profile-grid">

    <!-- Personal Information -->
    <div class="profile-section full-width">
      <div class="ps-header">
        <span class="ps-title">👤 Personal Information</span>
        <button class="ps-edit" id="edit-personal-btn" onclick="toggleEdit()">✏️ Edit</button>
      </div>
      <div class="info-grid">
        <div class="info-field"><label>First Name</label><input type="text" id="f-fname" readonly value="<?php echo $words[0] ?>"/></div>
        <div class="info-field"><label>Last Name</label><input type="text" id="f-lname" readonly value="<?php echo $words[1] ?>"/></div>
        <div class="info-field"><label>Email</label><input type="email" id="f-email" readonly value="<?php echo $user_email ?>"/></div>
        <div class="info-field">
          <label>Password</label>
          <div style="position:relative">
            <input type="password" id="f-password" placeholder="••••••••" readonly />
          </div>
        </div>
        <div class="info-field">
          <label>Account Role</label>
          <input type="text" id="f-role-display" value="<?php echo $_SESSION['role'] ?? 'User'; ?>" readonly style="background:var(--surface-3); opacity:0.8;" />
        </div>
        <div class="info-field"><label>Member Since</label><input type="text" id="f-joined" value="<?php echo (new DateTime($_SESSION['member_since']))->format('M d, Y'); ?>" readonly /></div>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="profile-section">
      <div class="ps-header">
        <span class="ps-title">🕐 Recent Activity</span>
        <button class="ps-edit" onclick="location.href='user-history.php'">View All →</button>
      </div>
      <ul class="activity-strip" id="activity-strip">
        <li style="text-align:center;padding:1.5rem;color:var(--text-muted);font-size:.875rem">Loading…</li>
      </ul>
    </div>

    <!-- Achievements -->
    <div class="profile-section">
      <div class="ps-header"><span class="ps-title">🏆 Achievements</span></div>
      <div class="achievement-grid" id="achievement-grid"></div>
    </div>

    <!-- Preferences -->
    <div class="profile-section full-width">
      <div class="ps-header"><span class="ps-title">⚙️ Preferences</span></div>
      <div class="pref-row">
        <div><div class="pref-label">Email Notifications</div><div class="pref-sub">Get due-date and overdue alerts</div></div>
        <label class="toggle-switch"><input type="checkbox" checked id="pref-email" onchange="savePref()"><span class="toggle-track"></span></label>
      </div>
      <div class="pref-row">
        <div><div class="pref-label">Book Recommendations</div><div class="pref-sub">Personalised weekly picks</div></div>
        <label class="toggle-switch"><input type="checkbox" checked id="pref-recs" onchange="savePref()"><span class="toggle-track"></span></label>
      </div>
      <div class="pref-row">
        <div><div class="pref-label">Dark Mode</div><div class="pref-sub">Switch to dark theme across all pages</div></div>
        <label class="toggle-switch"><input type="checkbox" id="pref-dark" onchange="LMS.toggleTheme()"><span class="toggle-track"></span></label>
      </div>
      <div class="pref-row">
        <div><div class="pref-label">Public Reading List</div><div class="pref-sub">Let others see your reading history</div></div>
        <label class="toggle-switch"><input type="checkbox" id="pref-public" onchange="savePref()"><span class="toggle-track"></span></label>
      </div>
    </div>

  </div><!-- /.profile-grid -->

  <!-- Change Password -->
  <div class="profile-section" style="margin-bottom:1.75rem;animation:fadeUp .5s .3s both">
    <div class="ps-header">
      <span class="ps-title">🔒 Change Password</span>
      <button class="ps-edit" onclick="document.getElementById('pwd-modal').classList.add('open')">✏️ Change</button>
    </div>
    <p style="font-size:.875rem;color:var(--text-muted)" id="pw-hint">Keep your account secure with a strong, unique password.</p>
  </div>

  <!-- Danger Zone -->
  <div class="profile-section" style="border-color:var(--danger-light);animation:fadeUp .5s .35s both">
    <div class="ps-header"><span class="ps-title" style="color:var(--danger)">🚨 Danger Zone</span></div>
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem">
      <div>
        <div style="font-size:.9rem;font-weight:600;color:var(--text)">Delete Account</div>
        <div style="font-size:.8rem;color:var(--text-muted);margin-top:.2rem">Irreversible — all your data will be permanently removed.</div>
      </div>
      <button class="btn" style="background:var(--danger-light);color:var(--danger)" onclick="document.getElementById('delete-account-modal').classList.add('open')">Delete My Account</button>
    </div>
  </div>

</main>

<!-- ══ APPLY FOR MEMBERSHIP MODAL ══ -->
<div class="modal-backdrop" id="apply-modal">
  <div class="modal" style="max-width:500px">
    <div class="modal-header">
      <span class="modal-title">🎓 Apply for Membership</span>
      <button class="modal-close" onclick="closeModal('apply-modal')">✕</button>
    </div>
    <div class="modal-body">
      <p style="color:var(--text-muted);font-size:.875rem;line-height:1.6;margin-bottom:1.25rem">
        Fill in your details and an admin will review your application within 1–2 business days.
      </p>

      <div style="margin-bottom:1rem">
        <label style="display:block;font-size:.75rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:.5rem">Membership Type</label>
        <div class="type-btns">
          <button class="type-btn active" data-type="General" onclick="setAppType(this,'General')">👤 General</button>
          <button class="type-btn" data-type="Student" onclick="setAppType(this,'Student')">🎓 Student</button>
          <button class="type-btn" data-type="Faculty" onclick="setAppType(this,'Faculty')">👨‍🏫 Faculty</button>
        </div>
      </div>

      <div class="info-grid" style="margin-bottom:.85rem">
        <div class="info-field"><label>Phone Number</label><input type="tel" id="app-phone" placeholder="+977 98XXXXXXXX" /></div>
        <div class="info-field"><label>Address / Location</label><input type="text" id="app-address" placeholder="Kathmandu, Nepal" /></div>
        <div class="info-field span-2">
          <label>Reason for Applying <span style="color:var(--danger)">*</span></label>
          <textarea id="app-reason" placeholder="Briefly explain why you'd like library membership…"></textarea>
        </div>
      </div>

      <div id="apply-error" style="display:none;color:var(--danger);font-size:.82rem;padding:.6rem .85rem;background:var(--danger-light);border-radius:8px;margin-top:.25rem"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('apply-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="submitApplication()">🎓 Submit Application</button>
    </div>
  </div>
</div>

<!-- ══ SAVE PROFILE MODAL ══ -->
<div class="modal-backdrop" id="save-modal">
  <div class="modal" style="max-width:380px">
    <div class="modal-header">
      <span class="modal-title">💾 Save Changes</span>
      <button class="modal-close" onclick="closeModal('save-modal')">✕</button>
    </div>
    <div class="modal-body">
      <p style="color:var(--text-muted);line-height:1.6">Save your profile changes?</p>
      <div style="background:var(--success-light);border-radius:10px;padding:.85rem;color:var(--success);font-weight:600;font-size:.875rem;margin-top:.75rem">✅ Your profile will be updated immediately.</div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('save-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="confirmSaveProfile()">💾 Save</button>
    </div>
  </div>
</div>

<!-- ══ CHANGE PASSWORD MODAL ══ -->
<div class="modal-backdrop" id="pwd-modal">
  <div class="modal" style="max-width:400px">
    <div class="modal-header">
      <span class="modal-title">🔒 Change Password</span>
      <div style="display:flex;gap:.5rem;align-items:center">
        <button type="button" class="pwd-toggle-btn" id="toggle-all-pwd" onclick="toggleAllPwdVisibility()" style="position:static;margin:0;transform:none;top:auto;right:auto">👁 Show</button>
        <button class="modal-close" onclick="closeModal('pwd-modal')">✕</button>
      </div>
    </div>
    <div class="modal-body">
      <div class="form-group" style="position:relative">
        <label>Current Password</label>
        <input type="password" class="form-control pwd-input" id="pwd-current" placeholder="Enter current password" />
      </div>
      <div class="form-group" style="position:relative">
        <label>New Password</label>
        <input type="password" class="form-control pwd-input" id="pwd-new" placeholder="Min. 8 characters" />
      </div>
      <div class="form-group" style="position:relative">
        <label>Confirm New Password</label>
        <input type="password" class="form-control pwd-input" id="pwd-confirm" placeholder="Repeat new password" />
      </div>
      <div id="pwd-error" class="form-error" style="display:none"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('pwd-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="confirmChangePwd()">🔒 Update Password</button>
    </div>
  </div>
</div>

<!-- ══ AVATAR MODAL ══ -->
<div class="modal-backdrop" id="avatar-modal">
  <div class="modal" style="max-width:380px">
    <div class="modal-header">
      <span class="modal-title">🖼 Update Avatar</span>
      <button class="modal-close" onclick="closeModal('avatar-modal')">✕</button>
    </div>
    <div class="modal-body" style="text-align:center">
      <div id="avatar-preview" style="width:90px;height:90px;border-radius:50%;background:linear-gradient(135deg,var(--accent),#2563eb);display:grid;place-items:center;font-size:2rem;font-weight:700;color:#fff;margin:0 auto 1.25rem">?</div>
      <p style="color:var(--text-muted);font-size:.875rem;margin-bottom:1rem">Choose your initials and a colour.</p>
      <div class="form-group" style="text-align:left">
        <label>Display Initials</label>
        <input class="form-control" id="avatar-initials" maxlength="2" style="text-align:center;font-size:1.1rem;font-weight:700;letter-spacing:.1em" oninput="document.getElementById('avatar-preview').textContent=this.value.toUpperCase()||'?'" />
      </div>
      <div style="display:flex;gap:.5rem;justify-content:center;flex-wrap:wrap;margin-top:.75rem">
        <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#1a4a8a,#2563eb);cursor:pointer;border:3px solid transparent" onclick="setAvatarColor(this,'linear-gradient(135deg,#1a4a8a,#2563eb)')"></div>
        <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#7c3aed,#9333ea);cursor:pointer;border:3px solid transparent" onclick="setAvatarColor(this,'linear-gradient(135deg,#7c3aed,#9333ea)')"></div>
        <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#c0392b,#e74c3c);cursor:pointer;border:3px solid transparent" onclick="setAvatarColor(this,'linear-gradient(135deg,#c0392b,#e74c3c)')"></div>
        <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#1a7a4a,#27ae60);cursor:pointer;border:3px solid transparent" onclick="setAvatarColor(this,'linear-gradient(135deg,#1a7a4a,#27ae60)')"></div>
        <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#c0780a,#f39c12);cursor:pointer;border:3px solid transparent" onclick="setAvatarColor(this,'linear-gradient(135deg,#c0780a,#f39c12)')"></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('avatar-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="confirmAvatarUpdate()">💾 Save Avatar</button>
    </div>
  </div>
</div>

<!-- ══ DELETE ACCOUNT MODAL ══ -->
<div class="modal-backdrop" id="delete-account-modal">
  <div class="modal" style="max-width:400px">
    <div class="modal-header">
      <span class="modal-title">⚠️ Delete Account</span>
      <button class="modal-close" onclick="closeModal('delete-account-modal')">✕</button>
    </div>
    <div class="modal-body">
      <p style="color:var(--text-muted);line-height:1.6;margin-bottom:1rem">This will permanently delete your account and all reading history. This action <strong>cannot be undone</strong>.</p>
      <div style="background:var(--danger-light);border-radius:10px;padding:.85rem;color:var(--danger);font-weight:600;font-size:.875rem">⚠️ All your data will be lost forever.</div>
      <div class="form-group" style="margin-top:1rem"><label>Type <strong>DELETE</strong> to confirm</label><input class="form-control" id="delete-confirm-input" placeholder="Type DELETE here" /></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('delete-account-modal')">Cancel</button>
      <button class="btn" style="background:linear-gradient(135deg,var(--danger),#e74c3c);color:#fff;padding:.55rem 1.25rem;border-radius:10px;font-weight:600;border:none;cursor:pointer" onclick="confirmDeleteAccount()">🗑 Delete My Account</button>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
<script>

  /* ── Sync dark toggle pref ── */
  document.getElementById('pref-dark').checked = document.documentElement.getAttribute('data-theme')==='dark';

  // toggleDarkPref function is replaced by direct call to LMS.toggleTheme()

  /* ── Membership Application Logic ── */
  let selectedAppType = 'General';
  function setAppType(btn, type) {
    selectedAppType = type;
    document.querySelectorAll('.type-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
  }

  function openApplicationModal() {
    const modal = document.getElementById('apply-modal');
    if (modal) {
        modal.classList.add('open');
        // Optional: Pre-fill the email from your session
        const emailInput = document.getElementById('app-email');
        if (emailInput && LMS.session) {
            emailInput.value = LMS.session.email;
        }
    }
  }

  function closeModal(id) {
    document.getElementById(id).classList.remove('open');
  }

  function submitApplication() {
    const phone = document.getElementById('app-phone').value.trim();
    const address = document.getElementById('app-address').value.trim();
    const reason = document.getElementById('app-reason').value.trim();
    const errorDiv = document.getElementById('apply-error');

    if (!reason) {
      errorDiv.textContent = "Please provide a reason for applying.";
      errorDiv.style.display = 'block';
      return;
    }

    fetch('../actions/apply.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        user_id: <?php echo $userId; ?>,
        type: selectedAppType,
        phone: phone,
        address: address,
        reason: reason
      })
    })
    .then(r => r.json())
    .then(data => {
      if (data.status === 'success') {
        LMS.toast('success', '🎓', 'Application submitted!');
        closeModal('apply-modal');
        document.getElementById('membership-banner').style.display = 'none';
        document.getElementById('pending-banner').style.display = 'flex';
      } else {
        errorDiv.textContent = data.message || "Submission failed.";
        errorDiv.style.display = 'block';
      }
    }).catch(err => LMS.toast('error', '⚠️', 'Network error.'));
  }

  /* ── Edit personal toggle ── */
  let editingPersonal=false;
  function toggleEdit(section = 'personal'){
    if(section==='personal'){
      editingPersonal=!editingPersonal;
      ['f-fname','f-lname','f-email','f-phone'].forEach(id=>{
        const el=document.getElementById(id);
        if(editingPersonal){ el.removeAttribute('readonly'); el.style.borderColor='var(--border)'; }
        else { el.setAttribute('readonly',''); el.style.borderColor='transparent'; }
      });
      document.getElementById('f-role').disabled=!editingPersonal;
      document.getElementById('edit-personal-btn').textContent=editingPersonal?'✕ Cancel':'✏️ Edit';
      document.getElementById('save-all-btn').style.display=editingPersonal?'inline-flex':'none';
    }
  }

  function saveProfile(){
    const fname=document.getElementById('f-fname').value.trim();
    const lname=document.getElementById('f-lname').value.trim();
    if(!fname||!lname){ LMS.toast('error','⚠','Name cannot be empty.'); return; }
    document.getElementById('save-modal-desc').textContent=`Save changes to your profile?`;
    document.getElementById('save-modal').classList.add('open');
  }
  function confirmSaveProfile(){
    const id = document.getElementById('f-id').value;
    const fname = document.getElementById('f-fname').value.trim();
    const lname = document.getElementById('f-lname').value.trim();
    const email = document.getElementById('f-email').value.trim();
    fetch('actions/edit_profile.php',{
      method:'POST',
      headers:{'Content-Type':'application/json'},
      body:JSON.stringify({'id': id,
        'fname': fname,
        'lname': lname,
        'email': email
      })})
      .then(r=>r.json()).then(d=>{
        if(d.success){
          document.getElementById('hero-name').textContent=d.name||fname+' '+lname;
          LMS.session.name=d.name||fname+' '+lname;
          LMS.toast('success','💾','Profile updated successfully!');
        } else {
          LMS.toast('error','⚠️',d.message||'Update failed.');
        }
      }).catch(()=>LMS.toast('error','⚠️','Network error.'));
    document.getElementById('save-modal').classList.remove('open');
    toggleEdit('personal');
  }

  function savePref(){ LMS.toast('info','⚙️','Preferences saved.'); }

  /* ── Password toggle in modal ── */
  let pwdShowAll=false;
  function toggleAllPwdVisibility(){
    const inputs = document.querySelectorAll('.pwd-input');
    const btn = document.getElementById('toggle-all-pwd');
    pwdShowAll = !pwdShowAll;
    inputs.forEach(input => {
      input.type = pwdShowAll ? 'text' : 'password';
    });
    btn.textContent = pwdShowAll ? '🙈 Hide' : '👁 Show';
  }

  /* ── Password via modal ── */
  function confirmChangePwd(){
    const cur=document.getElementById('pwd-current').value;
    const nw=document.getElementById('pwd-new').value;
    const cf=document.getElementById('pwd-confirm').value;
    const err=document.getElementById('pwd-error');

    const pwd_data = {
      id: <?php echo $userId ?>,
      current_pwd: cur,
      new_pwd: nw
    }
    if(!cur){ err.textContent='Enter your current password.'; err.style.display='block'; return; }
    if(nw.length<8){ err.textContent='New password must be at least 8 characters.'; err.style.display='block'; return; }
    if(nw!==cf){ err.textContent='Passwords do not match.'; err.style.display='block'; return; }
    err.style.display='none';
    fetch('../actions/change_password.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(pwd_data)})
      .then(r=>r.json()).then(d=>{
        if(d.success){
          ['pwd-current','pwd-new','pwd-confirm'].forEach(id=>document.getElementById(id).value='');
          document.getElementById('pwd-modal').classList.remove('open');
          document.getElementById('pw-hint').textContent='Your password was last changed just now.';
          LMS.toast('success','🔒','Password updated successfully!');
        } else {
          err.textContent=d.message||'Update failed.'; err.style.display='block';
        }
      }).catch(()=>{ err.textContent='Network error.'; err.style.display='block'; });
  }

  /* ── Avatar modal ── */
  let selectedAvatarColor='linear-gradient(135deg,var(--accent),#2563eb)';
  function setAvatarColor(el,color) {
    selectedAvatarColor=color;
    document.querySelectorAll('#avatar-modal div[onclick^="setAvatarColor"]').forEach(d=>d.style.outline='');
    el.style.outline='3px solid var(--accent)';
  }
  function confirmAvatarUpdate(){
    const initials=document.getElementById('avatar-initials').value.trim().toUpperCase().slice(0,2)||'JD';
    document.querySelectorAll('.hero-avatar-el,.sidebar-avatar-el').forEach(el=>{
      el.style.background=selectedAvatarColor; el.textContent=initials;
    });
    document.getElementById('avatar-modal').classList.remove('open');
    LMS.toast('success','🖼','Avatar updated!');
  }

  /* ── Delete account modal ── */
  function confirmDeleteAccount(){
    const val=document.getElementById('delete-confirm-input').value.trim();
    if(val!=='DELETE'){ LMS.toast('error','⚠️','Type DELETE exactly to confirm.'); return; }
    document.getElementById('delete-account-modal').classList.remove('open');
    fetch('../actions/delete_account.php',{
      method:"POST",
      headers:{
        "Content-Type":"application/json"
      },
      body:JSON.stringify({id:<?php echo $userId ?>})
    }).then(response => response.json())
    .then(data => {
      LMS.toast(data.status," ",data.message);

      if (data.status === "success"){
        window.location.href = '../index.php';
      }
    }).catch(err => {
      console.error("Fetch error:", err);
      LMS.toast('error', '❌', 'Failed to connect to server.');
    });
  }

  ['save-modal','pwd-modal','avatar-modal','delete-account-modal'].forEach(id=>{
    document.getElementById(id).addEventListener('click',e=>{if(e.target===e.currentTarget)e.currentTarget.classList.remove('open');});
  });
  document.addEventListener('keydown',e=>{if(e.key==='Escape')['save-modal','pwd-modal','avatar-modal','delete-account-modal'].forEach(id=>document.getElementById(id).classList.remove('open'));});

  /* ── Activity strip ── */
  const acts=[
    {dot:'📗',bg:'var(--success-light)',title:'Borrowed The Great Gatsby',sub:'2 days ago'},
    {dot:'↩️',bg:'var(--accent-light)', title:'Returned 1984',sub:'10 days ago'},
    {dot:'🔖',bg:'var(--accent-2-light)',title:'Added Deep Work to wishlist',sub:'12 days ago'},
    {dot:'⭐',bg:'var(--warning-light)', title:'Rated Dune — 5 stars',sub:'20 days ago'},
  ];
  document.getElementById('activity-strip').innerHTML=acts.map(a=>`
    <li class="as-item" onclick="location.href='user-history.php'">
      <div class="as-dot" style="background:${a.bg}">${a.dot}</div>
      <div class="as-meta"><div class="as-title">${a.title}</div><div class="as-sub">${a.sub}</div></div>
    </li>`).join('');

  /* ── Live stats from backend session ── */
  function applyProfileData(){
    const borrows = LMS.user_borrowed_ids.length;
    const history = LMS.history.length;
    const banner = document.getElementById('membership-banner');
    const pendingBanner = document.getElementById('pending-banner'); // Make sure this ID exists in your HTML
    
    // 1. Get the status from your shared LMS object
    const appStatus = LMS.app_status; // This comes from load_data.php

    // 2. FIX: Use lowercase 'user' to match your DB
    const isBasicUser = '<?php echo $userRole ?>' === 'user';
    console.log("isBasicUser:", isBasicUser);
console.log("appStatus:", appStatus);

    // 3. Logic for Banners
    if (isBasicUser) {
        if (!appStatus) {
            // No application exists: Show the "Apply Now" banner
            if (banner) banner.style.display = 'flex';
            if (pendingBanner) pendingBanner.style.display = 'none';
        } else if (appStatus === 'pending') {
            // Application is sent: Show the "Pending" notice
            if (banner) banner.style.display = 'none';
            if (pendingBanner) pendingBanner.style.display = 'flex';
        } else {
            // If status is 'approved' (they are now a member) or 'rejected', hide banners
            if (banner) banner.style.display = 'none';
            if (pendingBanner) pendingBanner.style.display = 'none';
        }
    } else {
        // Not a basic user (Admin/Member): Hide all application banners
        if (banner) banner.style.display = 'none';
        if (pendingBanner) pendingBanner.style.display = 'none';
    }
    document.getElementById('hero-borrowed').textContent = borrows;
    document.getElementById('borrow-badge').textContent  = `📖 ${borrows} Active Loan${borrows!==1?'s':''}`;
    // hero name/email from session
    if(LMS.session.name){
      document.getElementById('hero-name').textContent = LMS.session.name;
      const parts = LMS.session.name.trim().split(' ');
      document.getElementById('f-fname').value = parts[0]||'';
      document.getElementById('f-lname').value = parts.slice(1).join(' ')||'';
    }
    if(LMS.session.email) document.getElementById('f-email').value = LMS.session.email;
    if(LMS.session.role)  document.getElementById('f-role').value  = LMS.session.role;
    // stats badges
    const readEl = document.querySelector('.profile-badges .p-badge:nth-child(2)');
    if(readEl) readEl.textContent = `✅ ${history} Book${history!==1?'s':''} Read`;
    // activity strip from real history
    const strip = document.getElementById('activity-strip');
    if(strip && LMS.history.length){
      // Merge history with book data
      const mergedHistory = LMS.history.map(h => {
        const book = LMS.books.find(b => Number(b.id) === Number(h.book_id)) || {};
        return { ...h, ...book };
      });
      strip.innerHTML = mergedHistory.slice(0,4).map(h=>`
        <li class="as-item" onclick="location.href='user-history.php'">
          <div class="as-dot" style="background:var(--success-light)">↩️</div>
          <div class="as-meta">
            <div class="as-title">Returned ${h.title||'Unknown Book'}</div>
            <div class="as-sub">${h.returned_date||''}</div>
          </div>
        </li>`).join('');
    }
  }
  document.addEventListener('lmsDataLoaded', applyProfileData);
</script>
</body>
</html>
