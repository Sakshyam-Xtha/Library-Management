<?php
require_once('../includes/auth.php');
$pageTitle  = 'Members';
$activePage = 'members';
$topbarRole = 'admin';
$footerSearchAction = "location.href='admin-members.php'";
$extraCss = <<<'CSS'
/* ── Pending application cards ── */
.app-card { background:var(--surface); border:1px solid var(--border); border-left:4px solid var(--accent-2); border-radius:var(--radius-lg); padding:1rem 1.25rem; display:flex; align-items:center; gap:1rem; flex-wrap:wrap; animation:fadeUp .4s both; transition:box-shadow var(--transition); }
.app-card:hover { box-shadow:var(--shadow-sm); }
.app-avatar { width:40px; height:40px; border-radius:50%; display:grid; place-items:center; font-size:.9rem; font-weight:700; color:#fff; flex-shrink:0; background:linear-gradient(135deg,#c8a45a,#f39c12); }
.app-info { flex:1; min-width:160px; }
.app-name  { font-weight:700; font-size:.9rem; color:var(--text); }
.app-meta  { font-size:.76rem; color:var(--text-muted); margin-top:.1rem; }
.app-actions { display:flex; gap:.5rem; flex-shrink:0; }
/* ── Table tweaks ── */
.member-avatar-sm { width:34px; height:34px; border-radius:50%; display:grid; place-items:center; font-size:.75rem; font-weight:700; color:#fff; flex-shrink:0; }
.td-member { display:flex; align-items:center; gap:.65rem; }
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
    <div>
      <p class="page-eyebrow">Admin / Members</p>
      <h1 class="page-title">Members</h1>
      <p class="page-subtitle" id="member-count">Loading…</p>
    </div>
    <div style="display:flex;gap:.75rem;flex-wrap:wrap">
      <button class="btn btn-outline" onclick="exportCSV()">📥 Export CSV</button>
      <button class="btn btn-primary" onclick="openApplicationModal()">📨 Review Applications <span id="app-badge" style="background:var(--accent-2);color:#fff;border-radius:99px;padding:.05rem .5rem;font-size:.75rem;margin-left:.3rem">0</span></button>
    </div>
  </div>

  <!-- Stats -->
  <div class="stats-grid" style="grid-template-columns:repeat(4,1fr)">
    <div class="stat-card" style="animation-delay:.05s">
      <div class="stat-card-top"><span class="stat-label">Total Members</span><div class="stat-icon-wrap" style="background:var(--accent-light)">👥</div></div>
      <div class="stat-number" id="s-total">0</div>
      <div class="stat-bar" style="background:linear-gradient(90deg,var(--accent),#2563eb)"></div>
    </div>
    <div class="stat-card" style="animation-delay:.1s">
      <div class="stat-card-top"><span class="stat-label">Active</span><div class="stat-icon-wrap" style="background:var(--success-light)">✅</div></div>
      <div class="stat-number" id="s-active">0</div>
      <div class="stat-bar" style="background:linear-gradient(90deg,var(--success),#27ae60)"></div>
    </div>
    <div class="stat-card" style="animation-delay:.15s">
      <div class="stat-card-top"><span class="stat-label">Overdue</span><div class="stat-icon-wrap" style="background:var(--danger-light)">⚠️</div></div>
      <div class="stat-number" id="s-overdue">0</div>
      <div class="stat-bar" style="background:linear-gradient(90deg,var(--danger),#e74c3c)"></div>
    </div>
    <div class="stat-card" style="animation-delay:.2s">
      <div class="stat-card-top"><span class="stat-label">Pending Applications</span><div class="stat-icon-wrap" style="background:var(--accent-2-light)">📨</div></div>
      <div class="stat-number" id="s-pending">0</div>
      <div class="stat-bar" style="background:linear-gradient(90deg,var(--accent-2),#f39c12)"></div>
    </div>
  </div>

  <!-- Pending Applications strip -->
  <div id="pending-section" style="margin-bottom:1.75rem;display:none">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.85rem">
      <h3 style="font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:800;color:var(--text)">📨 Pending Applications</h3>
      <span style="font-size:.8rem;color:var(--text-muted)" id="pending-label"></span>
    </div>
    <div id="pending-list" style="display:flex;flex-direction:column;gap:.65rem"></div>
  </div>

  <!-- Filters -->
  <div style="display:flex;gap:.5rem;margin-bottom:1.25rem;flex-wrap:wrap;align-items:center">
    <div id="role-filters" style="display:flex;gap:.5rem;flex-wrap:wrap;flex:1">
      <button class="filter-btn active" data-role="all">All</button>
      <button class="filter-btn" data-role="active">Active</button>
      <button class="filter-btn" data-role="overdue">Overdue</button>
      <button class="filter-btn" data-role="Member">Member</button>
      <button class="filter-btn" data-role="Student">Student</button>
      <button class="filter-btn" data-role="Faculty">Faculty</button>
    </div>
  </div>

  <!-- Members Table -->
  <div class="table-card">
    <div class="table-toolbar">
      <span class="table-title">👥 Member Directory</span>
    </div>
    <div style="overflow-x:auto">
      <table>
        <thead>
          <tr>
            <th>Member</th>
            <th>Email</th>
            <th>Role</th>
            <th>Joined</th>
            <th>Borrowed</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="members-tbody"></tbody>
      </table>
    </div>
    <div id="empty-members" class="empty-state" style="display:none">
      <div class="empty-state-icon">👥</div>
      <h3>No members found</h3>
      <p>Try a different search or filter.</p>
    </div>
  </div>
</main>


<!-- ── ACCEPT APPLICATION MODAL ── -->
<div class="modal-backdrop" id="application-modal">
  <div class="modal" style="max-width:580px">
    <div class="modal-header">
      <span class="modal-title">📨 Member Applications</span>
      <button class="modal-close" onclick="closeApplicationModal()">✕</button>
    </div>
    <div class="modal-body" style="padding:0">

      <!-- Application list view -->
      <div id="app-list-view">
        <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border);background:var(--surface-2)">
          <p style="font-size:.875rem;color:var(--text-muted)">Review and approve or reject membership applications from the list below.</p>
        </div>
        <div id="app-modal-list" style="display:flex;flex-direction:column;gap:0;max-height:420px;overflow-y:auto"></div>
        <div id="app-modal-empty" style="padding:3rem;text-align:center;display:none">
          <div style="font-size:2.5rem;margin-bottom:.75rem">📭</div>
          <div style="font-weight:600;color:var(--text)">No pending applications</div>
          <div style="font-size:.85rem;color:var(--text-muted);margin-top:.25rem">All applications have been reviewed.</div>
        </div>
      </div>

      <!-- Single application review view -->
      <div id="app-review-view" style="display:none;padding:1.5rem">
        <button onclick="backToList()" style="background:none;border:none;color:var(--accent);font-weight:600;font-size:.85rem;cursor:pointer;padding:0;margin-bottom:1.25rem">← Back to list</button>
        <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;padding:1rem;background:var(--surface-2);border-radius:12px">
          <div id="rv-avatar" style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#c8a45a,#f39c12);display:grid;place-items:center;font-size:1.2rem;font-weight:700;color:#fff;flex-shrink:0"></div>
          <div>
            <div id="rv-name" style="font-weight:800;font-size:1.1rem;font-family:'Playfair Display',serif"></div>
            <div id="rv-email" style="font-size:.82rem;color:var(--text-muted);margin-top:.1rem"></div>
          </div>
          <span id="rv-type-badge" class="badge badge-accent" style="margin-left:auto"></span>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:1.25rem" id="rv-details"></div>
        <div class="form-group">
          <label>Assign Membership Role</label>
          <select class="form-control" id="rv-role">
            <option value="Member">📚 Member</option>
            <option value="Student">🎓 Student</option>
            <option value="Faculty">🏛 Faculty</option>
          </select>
        </div>
        <div class="form-group">
          <label>Admin Note (optional)</label>
          <textarea class="form-control" id="rv-note" rows="2" placeholder="Internal note about this member…" style="resize:vertical"></textarea>
        </div>
        <div id="rv-error" class="form-error" style="display:none"></div>
      </div>
    </div>
    <div class="modal-footer" id="app-modal-footer">
      <button class="btn btn-outline" onclick="closeApplicationModal()">Close</button>
    </div>
  </div>
</div>


<!-- ── EDIT MEMBER MODAL ── -->
<div class="modal-backdrop" id="member-modal">
  <div class="modal" style="max-width:480px">
    <div class="modal-header">
      <span class="modal-title" id="modal-title">Edit Member</span>
      <button class="modal-close" onclick="closeModal()">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-row">
        <div class="form-group"><label>Full Name *</label><input class="form-control" id="f-name" placeholder="Jane Doe" /></div>
        <div class="form-group"><label>Email *</label><input class="form-control" id="f-email" type="email" placeholder="jane@example.com" /></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Role *</label>
          <select class="form-control" id="f-role">
            <option value="">Select role…</option>
            <option>Member</option><option>Student</option><option>Faculty</option><option>Admin</option>
          </select>
        </div>
        <div class="form-group"><label>Avatar Colour</label>
          <select class="form-control" id="f-color">
            <option value="#1a4a8a,#2563eb">🔵 Navy Blue</option>
            <option value="#1a7a4a,#27ae60">🟢 Forest Green</option>
            <option value="#c0392b,#e74c3c">🔴 Crimson</option>
            <option value="#6b21a8,#9333ea">🟣 Purple</option>
            <option value="#c8a45a,#f39c12">🟡 Gold</option>
          </select>
        </div>
      </div>
      <div id="f-error" class="form-error" style="display:none"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal()">Cancel</button>
      <button class="btn btn-primary" onclick="saveMember()">💾 Save Changes</button>
    </div>
  </div>
</div>


<!-- ── VIEW MEMBER MODAL ── -->
<div class="modal-backdrop" id="view-modal">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">Member Profile</span>
      <button class="modal-close" onclick="document.getElementById('view-modal').classList.remove('open')">✕</button>
    </div>
    <div class="modal-body" id="view-modal-body"></div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="document.getElementById('view-modal').classList.remove('open')">Close</button>
      <button class="btn btn-primary" id="view-edit-btn">✏️ Edit Member</button>
    </div>
  </div>
</div>


<!-- ── DELETE MODAL ── -->
<div class="modal-backdrop" id="delete-modal">
  <div class="modal" style="max-width:380px">
    <div class="modal-header">
      <span class="modal-title">🗑 Remove Member</span>
      <button class="modal-close" onclick="document.getElementById('delete-modal').classList.remove('open')">✕</button>
    </div>
    <div class="modal-body">
      <p style="color:var(--text-muted);line-height:1.6">Remove <strong id="del-name" style="color:var(--text)"></strong> from the system? This cannot be undone.</p>
      <div style="background:var(--danger-light);border-radius:10px;padding:.85rem;color:var(--danger);font-weight:600;font-size:.875rem;margin-top:.75rem">⚠️ All borrowing records for this member will be retained.</div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="document.getElementById('delete-modal').classList.remove('open')">Cancel</button>
      <button class="btn" style="background:linear-gradient(135deg,var(--danger),#e74c3c);color:#fff;padding:.55rem 1.25rem;border-radius:10px;font-weight:600;border:none;cursor:pointer" onclick="confirmDelete()">🗑 Remove</button>
    </div>
  </div>
</div>


<?php include '../includes/footer.php'; ?>
<script>
/* ── State ── */
let members     = [];
let searchQ     = '', roleFilter = 'all';
let editingId   = null, deletingId = null, viewingId = null;
let reviewingApp = null;

/* ── Pending applications ── */
let pendingApps = [];

/* ── Render stats ── */
function updateStats() {
  document.getElementById('s-total').textContent   = members.length;
  document.getElementById('s-active').textContent  = members.filter(m=>m.status==='active').length;
  document.getElementById('s-overdue').textContent = members.filter(m=>m.status==='overdue').length;
  document.getElementById('s-pending').textContent = pendingApps.length;
  document.getElementById('app-badge').textContent = pendingApps.length;
  document.getElementById('member-count').textContent = `${members.length} registered members · ${pendingApps.length} pending application${pendingApps.length!==1?'s':''}`;
}

/* ── Render pending applications strip ── */
function renderPendingStrip() {
  const sec = document.getElementById('pending-section');
  if (!pendingApps.length) { sec.style.display = 'none'; return; }
  sec.style.display = 'block';
  document.getElementById('pending-label').textContent = `${pendingApps.length} awaiting review`;
  document.getElementById('pending-list').innerHTML = pendingApps.slice(0,3).map(a => `
    <div class="app-card">
      <div class="app-avatar">${a.applicant_name.split(' ').map(n=>n[0]).join('').slice(0,2).toUpperCase()}</div>
      <div class="app-info">
        <div class="app-name">${a.applicant_name}</div>
        <div class="app-meta">${a.applicant_email} · Applied ${a.applied} · <span class="badge badge-accent" style="font-size:.7rem">${a.type}</span></div>
      </div>
      <div class="app-actions">
        <button class="btn btn-outline" style="padding:.35rem .85rem;font-size:.8rem" onclick="openReviewApp('${a.id}')">👁 Review</button>
        <button class="btn btn-primary" style="padding:.35rem .85rem;font-size:.8rem" onclick="acceptApp('${a.id}')">✅ Accept</button>
        <button class="action-btn del" onclick="rejectApp('${a.id}')" title="Reject">✕</button>
      </div>
    </div>`).join('');
}

/* ── Render members table ── */
function render() {
  const tbody = document.getElementById('members-tbody');
  let data = members.filter(m => {
    if (roleFilter === 'active'   && m.status !== 'active')  return false;
    if (roleFilter === 'overdue'  && m.status !== 'overdue') return false;
    if (['member','student','faculty','admin'].includes(roleFilter) && m.role !== roleFilter) return false;
    if (searchQ && !(`${m.name} ${m.email} ${m.role}`).toLowerCase().includes(searchQ)) return false;
    return true;
  });

  document.getElementById('empty-members').style.display = data.length ? 'none' : 'block';
  tbody.innerHTML = data.map((m, i) => {
    const statusBadge = m.status === 'overdue'
      ? `<span class="badge badge-danger">⚠️ Overdue</span>`
      : `<span class="badge badge-success">✅ Active</span>`;
    const borrowed = m.borrowedBooks.length;
    return `<tr style="animation:fadeUp .3s ${i*.04}s both">
      <td>
        <div class="td-member">
          <div class="member-avatar-sm" style="background:linear-gradient(135deg,${m.color})">${m.initials}</div>
          <div>
            <div style="font-weight:700;font-size:.875rem;color:var(--text)">${m.name}</div>
            <div style="font-size:.72rem;color:var(--text-muted)">#${m.id}</div>
          </div>
        </div>
      </td>
      <td style="font-size:.82rem;color:var(--text-muted)">${m.email}</td>
      <td><span class="badge badge-accent">${m.role}</span></td>
      <td style="font-size:.82rem;color:var(--text-muted)">${m.joined}</td>
      <td>
        <span style="font-weight:${borrowed>0?'700':'400'};color:${borrowed>0?'var(--accent)':'var(--text-muted)'}">${borrowed}</span>
        ${borrowed > 0 ? `<span style="font-size:.72rem;color:var(--text-muted)"> book${borrowed!==1?'s':''}</span>` : ''}
      </td>
      <td>${statusBadge}</td>
      <td>
        <div class="action-btns">
          <button class="action-btn" onclick="viewMember(${m.id})" title="View">👁</button>
          <button class="action-btn" onclick="openEditMember(${m.id})" title="Edit">✏️</button>
          <button class="action-btn del" onclick="openDeleteModal(${m.id})" title="Remove">🗑</button>
        </div>
      </td>
    </tr>`;
  }).join('');

  updateStats();
  renderPendingStrip();
}

/* ── View member ── */
function viewMember(id) {
  viewingId = id;
  const m = members.find(x => x.id === id);
  const bookList = m.borrowedBooks.map(bid => {
    const b = LMS.books.find(x => x.id === bid);
    return b ? `<div style="display:flex;align-items:center;gap:.65rem;padding:.5rem 0;border-bottom:1px solid var(--border)">
      <span style="font-size:1.2rem">${b.cover}</span>
      <span style="font-size:.85rem;flex:1">${b.title}</span>
      <span class="badge badge-${b.status==='overdue'?'danger':'warning'}">${b.status}</span>
    </div>` : '';
  }).join('');

  document.getElementById('view-modal-body').innerHTML = `
    <div style="display:flex;align-items:center;gap:1.1rem;margin-bottom:1.5rem;padding:1rem;background:var(--surface-2);border-radius:12px">
      <div style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,${m.color});display:grid;place-items:center;font-size:1.2rem;font-weight:700;color:#fff;flex-shrink:0">${m.initials}</div>
      <div>
        <div style="font-family:'Playfair Display',serif;font-size:1.2rem;font-weight:900;color:var(--text)">${m.name}</div>
        <div style="font-size:.82rem;color:var(--text-muted)">${m.email}</div>
      </div>
      <span class="badge badge-${m.status==='overdue'?'danger':'success'}" style="margin-left:auto">${m.status==='overdue'?'⚠️ Overdue':'✅ Active'}</span>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.65rem;margin-bottom:1.25rem">
      ${[['Role',m.role],['Joined',m.joined],['Books Borrowed',m.borrowedBooks.length],['Member ID','#'+m.id]].map(([k,v])=>`
      <div style="background:var(--surface-2);padding:.75rem;border-radius:10px">
        <div style="font-size:.68rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:.2rem">${k}</div>
        <div style="font-weight:600;color:var(--text)">${v}</div>
      </div>`).join('')}
    </div>
    ${m.borrowedBooks.length
      ? `<h4 style="font-weight:700;font-size:.875rem;margin-bottom:.6rem;color:var(--text)">Currently Borrowed</h4>${bookList}`
      : `<p style="color:var(--text-muted);font-size:.875rem">No active borrowings.</p>`}`;

  document.getElementById('view-edit-btn').onclick = () => {
    document.getElementById('view-modal').classList.remove('open');
    openEditMember(id);
  };
  document.getElementById('view-modal').classList.add('open');
}

/* ── Edit member ── */
function openEditMember(id) {
  editingId = id;
  const m = members.find(x => x.id === id);
  document.getElementById('modal-title').textContent = 'Edit Member';
  document.getElementById('f-name').value  = m.name;
  document.getElementById('f-email').value = m.email;
  document.getElementById('f-role').value  = m.role;
  document.getElementById('f-color').value = m.color || '#1a4a8a,#2563eb';
  document.getElementById('f-error').style.display = 'none';
  document.getElementById('member-modal').classList.add('open');
}
function closeModal() { document.getElementById('member-modal').classList.remove('open'); }
function saveMember() {
  const name  = document.getElementById('f-name').value.trim();
  const email = document.getElementById('f-email').value.trim();
  const role  = document.getElementById('f-role').value;
  const color = document.getElementById('f-color').value;
  const errEl = document.getElementById('f-error');
  if (!name || !email || !role) { errEl.textContent = 'Please fill in all required fields.'; errEl.style.display = 'block'; return; }
  const m = members.find(x => x.id === editingId);
  Object.assign(m, { name, email, role, color, initials: name.split(' ').map(n=>n[0]).join('').slice(0,2).toUpperCase() });
  LMS.members = members;
  closeModal(); render();
  toast('success', '✅', 'Member updated successfully.');
}

/* ── Delete ── */
function openDeleteModal(id) {
  deletingId = id;
  document.getElementById('del-name').textContent = members.find(x=>x.id===id).name;
  document.getElementById('delete-modal').classList.add('open');
}
function confirmDelete() {
  fetch('../actions/delete_account.php', {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id: deletingId }) 
  }).then(r=> r.json())
  .then(data=>{
    if (data.status === "success") {
      // Close the modal using the ID from your HTML
      document.getElementById('delete-modal').classList.remove('open');
      
      // Sync fresh data from the server so the table and stats update automatically
      LMS.syncData(); 
      
      LMS.toast('success', '🗑️', data.message || 'Member removed successfully.');
    } else {
      LMS.toast('error', '❌', data.message || 'Deletion failed.');
    }
  }).catch (err=> {
    console.error("Fetch error:", err);
    LMS.toast('error', '❌', 'Failed to connect to server.');
  });

}

/* ── Application modal ── */
function openApplicationModal() {
  renderAppModalList();
  showAppListView();
  document.getElementById('application-modal').classList.add('open');
}
function closeApplicationModal() { document.getElementById('application-modal').classList.remove('open'); }

function showAppListView() {
  document.getElementById('app-list-view').style.display  = 'block';
  document.getElementById('app-review-view').style.display = 'none';
  document.getElementById('app-modal-footer').innerHTML =
    `<button class="btn btn-outline" onclick="closeApplicationModal()">Close</button>`;
}
function backToList() { renderAppModalList(); showAppListView(); }

function renderAppModalList() {
  const list = document.getElementById('app-modal-list');
  const empty = document.getElementById('app-modal-empty');
  if (!pendingApps.length) { list.innerHTML = ''; empty.style.display = 'block'; return; }
  empty.style.display = 'none';
  list.innerHTML = pendingApps.map(a => `
    <div style="display:flex;align-items:center;gap:1rem;padding:1rem 1.5rem;border-bottom:1px solid var(--border);transition:background var(--transition)" onmouseover="this.style.background='var(--surface-2)'" onmouseout="this.style.background=''">
      <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#c8a45a,#f39c12);display:grid;place-items:center;font-size:.85rem;font-weight:700;color:#fff;flex-shrink:0">${a.applicant_name.split(' ').map(n=>n[0]).join('').slice(0,2)}</div>
      <div style="flex:1;min-width:0">
        <div style="font-weight:700;font-size:.875rem;color:var(--text)">${a.applicant_name}</div>
        <div style="font-size:.75rem;color:var(--text-muted)">${a.applicant_email} · <span class="badge badge-accent" style="font-size:.68rem">${a.type}</span> · ${a.applied}</div>
      </div>
      <div style="display:flex;gap:.4rem;flex-shrink:0">
        <button class="btn btn-outline" style="padding:.3rem .75rem;font-size:.78rem" onclick="openReviewApp('${a.id}')">Review</button>
        <button class="btn btn-primary" style="padding:.3rem .75rem;font-size:.78rem" onclick="acceptApp('${a.id}');renderAppModalList()">✅ Accept</button>
        <button class="action-btn del" onclick="rejectApp('${a.id}');renderAppModalList()" title="Reject">✕</button>
      </div>
    </div>`).join('');
}

function openReviewApp(appId) {
  reviewingApp = pendingApps.find(x => x.id === appId);
  if (!reviewingApp) return;
  const a = reviewingApp;
  const initials = a.applicant_name.split(' ').map(n=>n[0]).join('').slice(0,2).toUpperCase();

  document.getElementById('app-list-view').style.display   = 'none';
  document.getElementById('app-review-view').style.display = 'block';

  document.getElementById('rv-avatar').textContent  = initials;
  document.getElementById('rv-name').textContent    = a.applicant_name;
  document.getElementById('rv-email').textContent   = a.applicant_email;
  document.getElementById('rv-type-badge').textContent = a.type;

  document.getElementById('rv-details').innerHTML = [
    ['📞 Phone',    a.phone],
    ['🏠 Address',  a.address],
    ['📅 Applied',  a.applied],
    ['📋 Type',     a.type],
  ].map(([k,v]) => `
    <div style="background:var(--surface-2);border-radius:9px;padding:.7rem">
      <div style="font-size:.7rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:.2rem">${k}</div>
      <div style="font-weight:600;font-size:.875rem;color:var(--text)">${v}</div>
    </div>`).join('') +
  `<div style="background:var(--surface-2);border-radius:9px;padding:.7rem;grid-column:span 2">
    <div style="font-size:.7rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:.3rem">💬 Reason for Joining</div>
    <div style="font-size:.875rem;color:var(--text);line-height:1.5">${a.reason}</div>
  </div>`;

  // Pre-select role based on applicant type
  const roleMap = { Student:'Student', Faculty:'Faculty', General:'Member' };
  document.getElementById('rv-role').value = roleMap[a.type] || 'Member';
  document.getElementById('rv-note').value = '';
  document.getElementById('rv-error').style.display = 'none';

  document.getElementById('app-modal-footer').innerHTML = `
    <button class="btn btn-outline" style="color:var(--danger);border-color:var(--danger)" onclick="rejectApp('${a.id}');backToList()">✕ Reject</button>
    <button class="btn btn-primary" onclick="acceptApp('${a.id}')">✅ Accept & Create Member</button>`;
}

function acceptApp(appId) {
  const a = pendingApps.find(x => x.id === appId);
  if (!a) return;

  const rvRoleEl  = document.getElementById('rv-role');
  const rvNoteEl  = document.getElementById('rv-note');

  // Determine role: use selected role if available (from review view), otherwise default based on type
  const roleMap = { Student:'Student', Faculty:'Faculty', General:'Member' };
  const role    = rvRoleEl ? rvRoleEl.value : (roleMap[a.type] || 'Member');

  // Determine note: use entered note if available, otherwise empty string
  const note    = rvNoteEl ? rvNoteEl.value.trim() : '';

  let formdata = {
    status: "approved",
    id: a.id,
    role: role,
    note: note // Include note in formdata
  }

  fetch('../actions/review_application.php',{
    method:"POST",
    headers:{'Content-Type':'application/json'},
    body: JSON.stringify(formdata)
  }).then(r => r.json())
  .then(data => {
    if (data.status === 'success'){
      toast('success', '✅', `${a.applicant_name} accepted as ${role}!`);
      closeApplicationModal();
      LMS.syncData();
    }else{
      toast('error', '❌', data.message || 'Error occurred during acceptance.');
    }
  })
  .catch (err => {
    console.error("Fetch error:", err);
    toast('error', '❌', 'Failed to connect to server.');
  });
}

function rejectApp(appId) {
  const a = pendingApps.find(x => x.id === appId);
  if (!a) return;

  const rvNoteEl  = document.getElementById('rv-note');
  const note      = rvNoteEl ? rvNoteEl.value.trim() : '';

  let formdata = {
    status: "rejected",
    id: a.id,
    note: note // Include note in formdata
  }

  fetch('../actions/review_application.php',{
    method:"POST",
    headers:{'Content-Type':'application/json'},
    body: JSON.stringify(formdata)
  }).then(r => r.json())
  .then(data => {
    if (data.status === 'success'){
      toast('warning', '✕', `Application from ${a.applicant_name} rejected.`);
      closeApplicationModal();
      LMS.syncData();
    }else{
      toast('error', '❌', data.message || 'Error occurred during rejection.');
    }
  })
  .catch (err => {
    console.error("Fetch error:", err);
    toast('error', '❌', 'Failed to connect to server.');
  });
}

/* ── Export CSV ── */
function exportCSV() {
  const rows = [['Name','Email','Role','Status','Joined','Books Borrowed'],
    ...members.map(m=>[m.name,m.email,m.role,m.status,m.joined,m.borrowedBooks.length])];
  const a = document.createElement('a');
  a.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(rows.map(r=>r.join(',')).join('\n'));
  a.download = 'lms-members.csv'; a.click();
  toast('info', '📥', 'CSV exported!');
}

/* ── Filters & search ── */
document.querySelectorAll('#role-filters .filter-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('#role-filters .filter-btn').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active'); roleFilter = btn.dataset.role; render();
  });
});
// document.getElementById('search-input').addEventListener('input', e => { searchQ = e.target.value.toLowerCase(); render(); });

/* ── Modal backdrop / ESC ── */
['member-modal','delete-modal','view-modal','application-modal'].forEach(id => {
  document.getElementById(id).addEventListener('click', e => { if(e.target===e.currentTarget) e.currentTarget.classList.remove('open'); });
});
document.addEventListener('keydown', e => {
  if(e.key==='Escape') ['member-modal','delete-modal','view-modal','application-modal'].forEach(id=>document.getElementById(id).classList.remove('open'));
});

function toast(type,icon,msg){const el=document.createElement('div');el.className=`toast ${type}`;el.innerHTML=`<span class="toast-icon">${icon}</span><span class="toast-msg">${msg}</span>`;document.getElementById('toast-container').appendChild(el);setTimeout(()=>{el.classList.add('removing');setTimeout(()=>el.remove(),300);},3200);}

document.addEventListener('lmsDataLoaded', () => {
  members = [...LMS.members];
  members.forEach(m => {
    m.borrowedBooks = LMS.borrowings.filter(b => b.member_id == m.id && !b.returned_date).map(b => b.book_id);
    m.status = LMS.getOverdue().some(b => b.member_id == m.id) ? 'overdue' : 'active';
  });
  pendingApps = [...LMS.applications];
  updateStats();
  renderPendingStrip();
  render();
});
</script>
</body>
</html>
