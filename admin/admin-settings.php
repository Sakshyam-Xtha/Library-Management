<?php
require_once('../includes/auth.php');
$pageTitle  = 'Settings';
$activePage = 'settings';
$topbarRole = 'admin';
$footerSearchAction = "location.href='admin-settings.php'";
$extraCss = <<<'CSS'
.settings-layout { display:grid; grid-template-columns:200px 1fr; gap:1.5rem; }
    .settings-nav { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-lg); padding:.75rem; height:fit-content; animation:fadeUp .4s both; }
    .settings-nav-item { padding:.6rem 1rem; border-radius:9px; font-size:.875rem; font-weight:500; color:var(--text-muted); cursor:pointer; transition:background var(--transition),color var(--transition); }
    .settings-nav-item:hover { background:var(--surface-2); color:var(--text); }
    .settings-nav-item.active { background:var(--accent-light); color:var(--accent); font-weight:600; }
    .settings-panel { display:none; animation:fadeUp .3s both; }
    .settings-panel.active { display:block; }
    .settings-section { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-lg); padding:1.5rem; margin-bottom:1.25rem; }
    .settings-section h3 { font-family:'Playfair Display',serif; font-size:1.05rem; font-weight:700; margin-bottom:1.25rem; padding-bottom:.75rem; border-bottom:1px solid var(--border); }
    .setting-row { display:flex; align-items:center; justify-content:space-between; padding:.75rem 0; border-bottom:1px solid var(--border); gap:1rem; }
    .setting-row:last-child { border-bottom:none; }
    .setting-info h4 { font-size:.9rem; font-weight:600; color:var(--text); margin-bottom:.1rem; }
    .setting-info p  { font-size:.78rem; color:var(--text-muted); }
    .toggle-switch { width:44px; height:24px; background:var(--surface-3); border:1px solid var(--border); border-radius:99px; cursor:pointer; position:relative; flex-shrink:0; transition:background var(--transition); }
    .toggle-switch.on { background:var(--accent); }
    .toggle-switch::after { content:''; position:absolute; top:3px; left:3px; width:16px; height:16px; border-radius:50%; background:#fff; transition:transform var(--transition); box-shadow:0 1px 4px rgba(0,0,0,.2); }
    .toggle-switch.on::after { transform:translateX(20px); }
    @media (max-width:700px) { .settings-layout { grid-template-columns:1fr; } }
CSS;
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?php echo $currentTheme; ?>">
<head>
<?php include '../includes/head.php'; ?>
</head>
<body>

<?php include '../includes/admin-sidebar.php'; ?>

<div class="main">
<?php include '../includes/topbar.php'; ?>

<main class="content">
    <div class="page-header">
      <div><p class="page-eyebrow">Admin / System</p><h1 class="page-title">Settings</h1><p class="page-subtitle">Configure your LMS system preferences</p></div>
    </div>

    <div class="settings-layout">
      <!-- Left nav -->
      <div class="settings-nav">
        <div class="settings-nav-item active" data-panel="general">⚙️ General</div>
        <div class="settings-nav-item" data-panel="library">📚 Library</div>
        <div class="settings-nav-item" data-panel="notifications">🔔 Notifications</div>
        <div class="settings-nav-item" data-panel="security">🔒 Security</div>
        <div class="settings-nav-item" data-panel="appearance">🎨 Appearance</div>
      </div>

      <!-- Panels -->
      <div>
        <!-- General -->
        <div class="settings-panel active" id="panel-general">
          <div class="settings-section">
            <h3>🏛 Library Information</h3>
            <div class="form-row"><div class="form-group"><label>Library Name</label><input class="form-control" id="lib-name" value="City Central Library" /></div><div class="form-group"><label>Contact Email</label><input class="form-control" value="admin@library.org" /></div></div>
            <div class="form-row"><div class="form-group"><label>Phone</label><input class="form-control" value="+1 (555) 000-0000" /></div><div class="form-group"><label>City</label><input class="form-control" value="Kathmandu" /></div></div>
            <div class="form-group"><label>Address</label><input class="form-control" value="123 Library Street, Knowledge District" /></div>
            <button class="btn btn-primary" onclick="saveGeneral()" style="margin-top:.5rem">💾 Save Changes</button>
          </div>
          <div class="settings-section">
            <h3>📋 System Information</h3>
            <div class="setting-row"><div class="setting-info"><h4>LMS Version</h4><p>Current build version</p></div><span class="badge badge-success">v2.5.0</span></div>
            <div class="setting-row"><div class="setting-info"><h4>Database</h4><p>Last synced</p></div><span style="font-size:.85rem;color:var(--text-muted)">Just now</span></div>
            <div class="setting-row"><div class="setting-info"><h4>Export Data</h4><p>Download full library backup</p></div><button class="btn btn-outline" style="padding:.35rem .9rem;font-size:.8rem" onclick="toast('success','📥','Backup exported!')">📥 Export</button></div>
          </div>
        </div>

        <!-- Library rules -->
        <div class="settings-panel" id="panel-library">
          <div class="settings-section">
            <h3>📚 Borrowing Rules</h3>
            <div class="form-row">
              <div class="form-group"><label>Default Loan Period (days)</label><input class="form-control" type="number" id="loan-days" value="14" min="1" max="90"/></div>
              <div class="form-group"><label>Max Books Per Member</label><input class="form-control" type="number" id="max-books" value="5" min="1" max="20"/></div>
            </div>
            <div class="form-row">
              <div class="form-group"><label>Fine Per Day (Rs.)</label><input class="form-control" type="number" id="fine-rate" value="10" min="0"/></div>
              <div class="form-group"><label>Max Renewals</label><input class="form-control" type="number" id="max-renew" value="2" min="0"/></div>
            </div>
            <button class="btn btn-primary" onclick="saveLibraryRules()">💾 Save Rules</button>
          </div>
          <div class="settings-section">
            <h3>🏷️ Book Settings</h3>
            <div class="setting-row"><div class="setting-info"><h4>Auto-mark overdue</h4><p>Automatically mark books overdue at midnight</p></div><div class="toggle-switch on" onclick="this.classList.toggle('on')"></div></div>
            <div class="setting-row"><div class="setting-info"><h4>Allow reservations</h4><p>Members can reserve unavailable books</p></div><div class="toggle-switch on" onclick="this.classList.toggle('on')"></div></div>
            <div class="setting-row"><div class="setting-info"><h4>Require ISBN</h4><p>Make ISBN mandatory when adding books</p></div><div class="toggle-switch" onclick="this.classList.toggle('on')"></div></div>
          </div>
        </div>

        <!-- Notifications -->
        <div class="settings-panel" id="panel-notifications">
          <div class="settings-section">
            <h3>🔔 Notification Preferences</h3>
            <div class="setting-row"><div class="setting-info"><h4>Overdue Reminders</h4><p>Send daily reminders for overdue books</p></div><div class="toggle-switch on" onclick="this.classList.toggle('on')"></div></div>
            <div class="setting-row"><div class="setting-info"><h4>Due Date Alerts</h4><p>Notify members 3 days before due date</p></div><div class="toggle-switch on" onclick="this.classList.toggle('on')"></div></div>
            <div class="setting-row"><div class="setting-info"><h4>New Member Welcome</h4><p>Send welcome email to new members</p></div><div class="toggle-switch on" onclick="this.classList.toggle('on')"></div></div>
            <div class="setting-row"><div class="setting-info"><h4>Book Availability</h4><p>Notify waiting members when book is returned</p></div><div class="toggle-switch" onclick="this.classList.toggle('on')"></div></div>
            <div class="setting-row"><div class="setting-info"><h4>Admin Digest</h4><p>Daily summary email to administrators</p></div><div class="toggle-switch on" onclick="this.classList.toggle('on')"></div></div>
          </div>
          <div class="settings-section">
            <h3>📧 Email Settings</h3>
            <div class="form-group"><label>SMTP Host</label><input class="form-control" value="smtp.library.org" /></div>
            <div class="form-row"><div class="form-group"><label>Port</label><input class="form-control" value="587" /></div><div class="form-group"><label>From Address</label><input class="form-control" value="noreply@library.org" /></div></div>
            <button class="btn btn-primary" onclick="toast('success','✅','Email settings saved!')">💾 Save</button>
            <button class="btn btn-outline" style="margin-left:.5rem" onclick="toast('info','📬','Test email sent!')">📬 Test Email</button>
          </div>
        </div>

        <!-- Security -->
        <div class="settings-panel" id="panel-security">
          <div class="settings-section">
            <h3>🔒 Security Settings</h3>
            <div class="setting-row"><div class="setting-info"><h4>Two-Factor Auth</h4><p>Require 2FA for admin accounts</p></div><div class="toggle-switch" onclick="this.classList.toggle('on')"></div></div>
            <div class="setting-row"><div class="setting-info"><h4>Session Timeout</h4><p>Auto-logout after inactivity</p></div><select class="form-control" style="width:140px"><option>30 minutes</option><option selected>1 hour</option><option>4 hours</option></select></div>
            <div class="setting-row"><div class="setting-info"><h4>Login Attempts</h4><p>Lock account after failed attempts</p></div><select class="form-control" style="width:140px"><option>3 attempts</option><option selected>5 attempts</option><option>10 attempts</option></select></div>
          </div>
          <div class="settings-section">
            <h3>🔑 Admin Password</h3>
            <div class="setting-row">
              <div class="setting-info"><h4>Change Password</h4><p>Update your administrator account password</p></div>
              <button class="btn btn-outline" style="padding:.4rem .9rem;font-size:.85rem" onclick="openChangePasswordModal()">🔒 Change Password</button>
            </div>
            <div class="setting-row">
              <div class="setting-info"><h4>Reset Security Settings</h4><p>Restore this section to defaults</p></div>
              <button class="btn btn-outline" style="padding:.4rem .9rem;font-size:.85rem;color:var(--danger);border-color:var(--danger)" onclick="openResetModal('Security Settings')">🔄 Reset</button>
            </div>
          </div>
        </div>

        <!-- Appearance -->
        <div class="settings-panel" id="panel-appearance">
          <div class="settings-section">
            <h3>🎨 Appearance</h3>
            <div class="setting-row"><div class="setting-info"><h4>Dark Mode</h4><p>Toggle between light and dark theme</p></div><div class="toggle-switch" id="dark-setting-toggle" onclick="LMS.toggleTheme();this.classList.toggle('on')"></div></div>
            <div class="setting-row"><div class="setting-info"><h4>Compact Sidebar</h4><p>Show icons only in sidebar</p></div><div class="toggle-switch" onclick="this.classList.toggle('on')"></div></div>
            <div class="setting-row"><div class="setting-info"><h4>Animations</h4><p>Enable UI animations and transitions</p></div><div class="toggle-switch on" onclick="this.classList.toggle('on')"></div></div>
          </div>
          <div class="settings-section">
            <h3>🖌️ Accent Color</h3>
            <div style="display:flex;gap:1rem;flex-wrap:wrap;margin-top:.5rem">
              ${['#1a4a8a','#1a7a4a','#c0392b','#6b21a8','#0369a1','#c8a45a'].map((c,i)=>`<div onclick="setAccent('${c}',this)" style="width:36px;height:36px;border-radius:50%;background:${c};cursor:pointer; border:3px solid ${i===0?c:'transparent'};outline:3px solid ${i===0?'white':'transparent'};transition:all .2s" title="${c}"></div>`).join('')}
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

<!-- SAVE CONFIRMATION MODAL -->
<div class="modal-backdrop" id="save-modal">
  <div class="modal" style="max-width:380px">
    <div class="modal-header">
      <span class="modal-title">💾 Save Changes</span>
      <button class="modal-close" onclick="document.getElementById('save-modal').classList.remove('open')">✕</button>
    </div>
    <div class="modal-body">
      <p style="color:var(--text-muted);margin-bottom:1rem" id="save-modal-desc">Save your settings changes?</p>
      <div style="background:var(--success-light);border-radius:10px;padding:.85rem;color:var(--success);font-weight:600;font-size:.875rem">
        ✅ Changes will take effect immediately.
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="document.getElementById('save-modal').classList.remove('open')">Cancel</button>
      <button class="btn btn-primary" onclick="confirmSave()">💾 Save</button>
    </div>
  </div>
</div>

<!-- RESET TO DEFAULTS MODAL -->
<div class="modal-backdrop" id="reset-modal">
  <div class="modal" style="max-width:380px">
    <div class="modal-header">
      <span class="modal-title">🔄 Reset to Defaults</span>
      <button class="modal-close" onclick="document.getElementById('reset-modal').classList.remove('open')">✕</button>
    </div>
    <div class="modal-body">
      <p style="color:var(--text-muted);margin-bottom:1rem">This will reset <strong id="reset-section-name">this section</strong> to default values. This cannot be undone.</p>
      <div style="background:var(--danger-light);border-radius:10px;padding:.85rem;color:var(--danger);font-weight:600;font-size:.875rem">
        ⚠️ All custom settings in this section will be lost.
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="document.getElementById('reset-modal').classList.remove('open')">Cancel</button>
      <button class="btn btn-danger" style="background:linear-gradient(135deg,var(--danger),#e74c3c);color:#fff;padding:.55rem 1.25rem;border-radius:10px;font-weight:600;border:none;cursor:pointer" onclick="confirmReset()">🔄 Reset</button>
    </div>
  </div>
</div>

<!-- CHANGE PASSWORD MODAL -->
<div class="modal-backdrop" id="pwd-modal">
  <div class="modal" style="max-width:400px">
    <div class="modal-header">
      <span class="modal-title">🔒 Change Admin Password</span>
      <button class="modal-close" onclick="document.getElementById('pwd-modal').classList.remove('open')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group"><label>Current Password</label><input type="password" class="form-control" id="pwd-current" placeholder="Enter current password" /></div>
      <div class="form-group"><label>New Password</label><input type="password" class="form-control" id="pwd-new" placeholder="Min. 8 characters" /></div>
      <div class="form-group"><label>Confirm New Password</label><input type="password" class="form-control" id="pwd-confirm" placeholder="Repeat new password" /></div>
      <div id="pwd-error" class="form-error" style="display:none"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="document.getElementById('pwd-modal').classList.remove('open')">Cancel</button>
      <button class="btn btn-primary" onclick="confirmChangePassword()">🔒 Update Password</button>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
<script>
  const sb=document.getElementById('sidebar'),ov=document.getElementById('overlay');
  document.getElementById('menu-btn').addEventListener('click',()=>{sb.classList.toggle('open');ov.classList.toggle('show');});
  ov.addEventListener('click',()=>{sb.classList.remove('open');ov.classList.remove('show');});

  /* Settings nav */
  document.querySelectorAll('.settings-nav-item').forEach(item=>{
    item.addEventListener('click',()=>{
      document.querySelectorAll('.settings-nav-item').forEach(i=>i.classList.remove('active'));
      document.querySelectorAll('.settings-panel').forEach(p=>p.classList.remove('active'));
      item.classList.add('active');
      document.getElementById('panel-'+item.dataset.panel).classList.add('active');
    });
  });

  /* ── Save modal ── */
  let pendingSaveSection = '';
  function saveGeneral()      { openSaveModal('General Settings'); }
  function saveLibraryRules() { openSaveModal('Library Rules'); }
  function savePanel(name)    { openSaveModal(name); }
  function openSaveModal(section) {
    pendingSaveSection = section;
    document.getElementById('save-modal-desc').textContent = `Save changes to "${section}"?`;
    document.getElementById('save-modal').classList.add('open');
  }
  function confirmSave() {
    document.getElementById('save-modal').classList.remove('open');
    toast('success','✅',`${pendingSaveSection} saved successfully!`);
  }

  /* ── Reset modal ── */
  let pendingResetSection = '';
  function openResetModal(section) {
    pendingResetSection = section;
    document.getElementById('reset-section-name').textContent = `"${section}"`;
    document.getElementById('reset-modal').classList.add('open');
  }
  function confirmReset() {
    document.getElementById('reset-modal').classList.remove('open');
    toast('warning','🔄',`${pendingResetSection} reset to defaults.`);
  }

  /* ── Change password modal ── */
  function openChangePasswordModal() { document.getElementById('pwd-modal').classList.add('open'); }
  function confirmChangePassword() {
    const cur  = document.getElementById('pwd-current').value;
    const nw   = document.getElementById('pwd-new').value;
    const conf = document.getElementById('pwd-confirm').value;
    const err  = document.getElementById('pwd-error');
    if(!cur)                   { err.textContent='Enter your current password.'; err.style.display='block'; return; }
    if(nw.length < 8)          { err.textContent='New password must be at least 8 characters.'; err.style.display='block'; return; }
    if(nw !== conf)            { err.textContent='Passwords do not match.'; err.style.display='block'; return; }
    document.getElementById('pwd-modal').classList.remove('open');
    ['pwd-current','pwd-new','pwd-confirm'].forEach(id=>document.getElementById(id).value='');
    toast('success','🔒','Password updated successfully!');
  }

  ['save-modal','reset-modal','pwd-modal'].forEach(id=>{
    document.getElementById(id).addEventListener('click',e=>{if(e.target===e.currentTarget)e.currentTarget.classList.remove('open');});
  });
  document.addEventListener('keydown',e=>{if(e.key==='Escape')['save-modal','reset-modal','pwd-modal'].forEach(id=>document.getElementById(id).classList.remove('open'));});

  function setAccent(color,el){document.querySelectorAll('.settings-section div[onclick^="setAccent"]').forEach(d=>{d.style.border='3px solid transparent';d.style.outline='3px solid transparent';});el.style.border=`3px solid ${color}`;el.style.outline='3px solid white';toast('info','🎨','Accent color updated (preview only).');}

  function toast(type,icon,msg){const el=document.createElement('div');el.className=`toast ${type}`;el.innerHTML=`<span class="toast-icon">${icon}</span><span class="toast-msg">${msg}</span>`;document.getElementById('toast-container').appendChild(el);setTimeout(()=>{el.classList.add('removing');setTimeout(()=>el.remove(),300);},3200);}
</script>
</body>
</html>
