<?php
require_once('../includes/auth.php');
$pageTitle  = 'Borrowings';
$activePage = 'borrowings';
$topbarRole = 'admin';
$footerSearchAction = "location.href='admin-borrowings.php'";
// No page-specific CSS
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
      <div><p class="page-eyebrow">Admin / Borrowings</p><h1 class="page-title">Borrowings</h1><p class="page-subtitle">Track all active and past book borrowings</p></div>
      <button class="btn btn-primary" onclick="openIssueModal()">📋 Issue New Book</button>
    </div>

    <div class="stats-grid" style="grid-template-columns:repeat(4,1fr)">
      <div class="stat-card" style="animation-delay:.05s"><div class="stat-card-top"><span class="stat-label">Active</span><div class="stat-icon-wrap" style="background:var(--accent-light)">🔄</div></div><div class="stat-number" id="s-active">0</div><div class="stat-bar" style="background:linear-gradient(90deg,var(--accent),#2563eb)"></div></div>
      <div class="stat-card" style="animation-delay:.1s"><div class="stat-card-top"><span class="stat-label">Overdue</span><div class="stat-icon-wrap" style="background:var(--danger-light)">⚠️</div></div><div class="stat-number" id="s-overdue">0</div><div class="stat-bar" style="background:linear-gradient(90deg,var(--danger),#e74c3c)"></div></div>
      <div class="stat-card" style="animation-delay:.15s"><div class="stat-card-top"><span class="stat-label">Returned</span><div class="stat-icon-wrap" style="background:var(--success-light)">✅</div></div><div class="stat-number" id="s-returned">0</div><div class="stat-bar" style="background:linear-gradient(90deg,var(--success),#27ae60)"></div></div>
      <div class="stat-card" style="animation-delay:.2s"><div class="stat-card-top"><span class="stat-label">Total Fines</span><div class="stat-icon-wrap" style="background:var(--accent-2-light)">💰</div></div><div class="stat-number" id="s-fines">Rs.0</div><div class="stat-bar" style="background:linear-gradient(90deg,var(--accent-2),#f39c12)"></div></div>
    </div>

    <div class="table-card">
      <div class="table-toolbar">
        <span class="table-title">🔄 All Borrowings</span>
        <div class="table-filters">
          <button class="filter-btn active" data-f="all">All</button>
          <button class="filter-btn" data-f="active">Active</button>
          <button class="filter-btn" data-f="overdue">Overdue</button>
          <button class="filter-btn" data-f="returned">Returned</button>
        </div>
      </div>
      <div style="overflow-x:auto"><table>
        <thead><tr><th>#</th><th>Member</th><th>Book</th><th>Issued</th><th>Due</th><th>Fine</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody id="borrow-tbody"></tbody>
      </table></div>
      <div id="empty-borrow" class="empty-state" style="display:none"><div class="empty-state-icon">📋</div><h3>No borrowings found</h3><p>Try a different filter.</p></div>
    </div>
  </main>

<!-- ISSUE BOOK MODAL -->
<div class="modal-backdrop" id="issue-modal">
  <div class="modal">
    <div class="modal-header"><span class="modal-title">📋 Issue Book</span><button class="modal-close" onclick="closeIssue()">✕</button></div>
    <div class="modal-body">
      <div class="form-group"><label>Member *</label>
        <select class="form-control" id="f-member"><option value="">Select member…</option></select>
      </div>
      <div class="form-group"><label>Book *</label>
        <select class="form-control" id="f-book"><option value="">Select available book…</option></select>
      </div>
      <div class="form-group"><label>Due Date *</label>
        <input class="form-control" type="date" id="f-due" />
      </div>
      <div id="f-error" style="color:var(--danger);font-size:.85rem;display:none"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeIssue()">Cancel</button>
      <button class="btn btn-primary" onclick="issueBook()">✅ Issue Book</button>
    </div>
  </div>
</div>


<!-- RETURN CONFIRM -->
<div class="modal-backdrop" id="return-modal">
  <div class="modal" style="max-width:420px">
    <div class="modal-header"><span class="modal-title">↩ Process Return</span><button class="modal-close" onclick="document.getElementById('return-modal').classList.remove('open')">✕</button></div>
    <div class="modal-body" id="return-body"></div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="document.getElementById('return-modal').classList.remove('open')">Cancel</button>
      <button class="btn btn-success" onclick="confirmReturn()">↩ Confirm Return</button>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
<script>
  /* ── State ── */
  let borrowings = []; // Initialized as empty; populated by event listener
  let filterState = 'all', searchQ = '', returningId = null;

  /* ── 1. DATA SYNC (The Fix) ── */
  // This ensures the script only runs AFTER the database data is ready
  document.addEventListener('lmsDataLoaded', () => {
    borrowings = [...LMS.borrowings];
    render();
  });

  /* ── 2. UI INITIALIZATION ── */
  const sb = document.getElementById('sidebar'), ov = document.getElementById('overlay');
  
  // Menu Toggle
  const menuBtn = document.getElementById('menu-btn');
  if(menuBtn) {
    menuBtn.addEventListener('click', () => { sb.classList.toggle('open'); ov.classList.toggle('show'); });
  }
  
  if(ov) {
    ov.addEventListener('click', () => { sb.classList.remove('open'); ov.classList.remove('show'); });
  }

  // Search
  document.getElementById('search-input').addEventListener('input', e => { 
    searchQ = e.target.value.toLowerCase(); 
    render(); 
  });

  // Filters
  document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      filterState = btn.dataset.f;
      render();
    });
  });

  /* ── 3. CORE FUNCTIONS (Fixed Property Names) ── */
  function fmtDate(d) { 
    return d ? new Date(d).toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'}) : '—'; 
  }

  function daysOverdue(d) { 
    if(!d) return 0;
    const diff = Math.floor((new Date() - new Date(d)) / (1000 * 60 * 60 * 24)); 
    return diff; 
  }

  function getStatus(b) {
    if(b.returned_date) return 'returned';
    // Fixed: using b.due_date from DB
    if(new Date(b.due_date) < new Date()) return 'overdue';
    return 'active';
  }

  /* ── 4. RENDER ENGINE ── */
  function render() {
    // Stats Update
    document.getElementById('s-active').textContent   = borrowings.filter(b => getStatus(b) === 'active').length;
    document.getElementById('s-overdue').textContent  = borrowings.filter(b => getStatus(b) === 'overdue').length;
    document.getElementById('s-returned').textContent = LMS.allHistory.length;
    
    const userId = LMS.session.userId;

  const totalActiveFine = LMS.borrowings
    .filter(b => Number(b.member_id) === Number(userId)) // Filter for this specific user
    .reduce((total, record) => {
      // Use Number() to ensure math works even if DB returns a string
      return total + Number(record.fine || 0);
    }, 0);

    // Update the UI
    const fineDisplay = document.getElementById('s-fines');
    if (fineDisplay) {
      fineDisplay.textContent = 'Rs. ' + totalActiveFine;
      
      // Optional: Visual indicator for debt
      fineDisplay.style.color = totalActiveFine > 0 ? 'var(--danger)' : 'var(--text)';
    }

    let data = [];
    if (filterState === 'all') {
      data = borrowings.map(b => ({ ...b, status: getStatus(b) })).concat(LMS.allHistory.map(h => ({ ...h, status: 'returned' })));
    } else if (filterState === 'returned') {
      data = LMS.allHistory.map(h => ({ ...h, status: 'returned' }));
    } else {
      data = borrowings.map(b => ({ ...b, status: getStatus(b) }));
    }

    data = data.filter(b => {
      if (filterState !== 'all' && b.status !== filterState) return false;
      
      // Fixed: mapping user_id/book_id to your search variables
      const m = LMS.members.find(x => x.id == b.member_id);
      const bk = LMS.books.find(x => x.id == b.book_id);
      const q = `${m?.name || ''} ${bk?.title || ''} ${b.id}`.toLowerCase();
      if(searchQ && !q.includes(searchQ)) return false;
      return true;
    });

    const emptyEl = document.getElementById('empty-borrow');
    if(emptyEl) emptyEl.style.display = data.length ? 'none' : 'block';

    document.getElementById('borrow-tbody').innerHTML = data.map(b => {
      // Fixed: mapping to correct database keys
      const m = LMS.members.find(x => x.id == b.member_id);
      const bk = LMS.books.find(x => x.id == b.book_id);
      const s = b.status;
      
      const statusBadge = s === 'returned' ? `<span class="badge badge-success">✅ Returned</span>`
        : s === 'overdue' ? `<span class="badge badge-danger">⚠️ Overdue</span>`
        : `<span class="badge badge-warning">📖 Active</span>`;
      
      const dueStyle = s === 'overdue' ? 'color:var(--danger);font-weight:700' : 'color:var(--text-muted)';
      const fine = b.fine > 0 ? `<span style="color:var(--danger);font-weight:600">Rs.${b.fine}</span>` : `<span style="color:var(--text-muted)">—</span>`;

      return `<tr>
        <td style="color:var(--text-muted);font-family:monospace">#${b.id}</td>
        <td><div style="display:flex;align-items:center;gap:.6rem"><div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#1a4a8a,#2563eb);display:grid;place-items:center;font-size:.75rem;font-weight:700;color:#fff">${m?.name?.[0] || '?'}</div>${m?.name || 'Unknown'}</div></td>
        <td><div class="td-book" style="gap:.5rem"><div class="book-cover" style="width:28px;height:38px;font-size:1rem">${bk?.cover || '📚'}</div><div><div class="book-title" style="font-size:.85rem">${bk?.title || 'Unknown'}</div><div class="book-author">${bk?.author || ''}</div></div></div></td>
        <td style="color:var(--text-muted)">${fmtDate(b.issue_date)}</td>
        <td style="${dueStyle}">${fmtDate(b.due_date)}</td>
        <td>${fine}</td>
        <td>${statusBadge}</td>
        <td><div class="action-btns">
          ${!b.returned_date ? `<button class="action-btn ok" onclick="openReturn(${b.id})" title="Process Return">↩</button>` : ''}
          <button class="action-btn del" onclick="deleteBorrowing(${b.id})" title="Delete">🗑</button>
        </div></td>
      </tr>`;
    }).join('');
  }

  /* ── 5. MODAL FUNCTIONS ── */
  function openIssueModal() {
    const mSel = document.getElementById('f-member');
    mSel.innerHTML = '<option value="">Select member…</option>' + 
      LMS.members.map(m => `<option value="${m.id}">${m.name} (#${m.id})</option>`).join('');
    
    const bSel = document.getElementById('f-book');
    bSel.innerHTML = '<option value="">Select available book…</option>' + 
      LMS.books.filter(b => parseInt(b.copies_available) > 0).map(b => `<option value="${b.id}">${b.title}</option>`).join('');
    
    const due = new Date(); 
    due.setDate(due.getDate() + 14);
    document.getElementById('f-due').value = due.toISOString().split('T')[0];
    document.getElementById('f-error').style.display = 'none';
    document.getElementById('issue-modal').classList.add('open');
  }

  function closeIssue() { document.getElementById('issue-modal').classList.remove('open'); }

  function issueBook() {
    const userId = document.getElementById('f-member').value;
    const bookId = document.getElementById('f-book').value;
    const due = document.getElementById('f-due').value;
    const errEl = document.getElementById('f-error');

    if(!userId || !bookId || !due) {
      errEl.textContent = 'Please fill in all fields.';
      errEl.style.display = 'block';
      return;
    }

    const newB = {
      id: Date.now().toString().slice(-5),
      user_id: userId,
      book_id: bookId,
      issue_date: new Date().toISOString().split('T')[0],
      due_date: due,
      returned_date: null,
      fine: 0
    };
    
    borrowings.unshift(newB);
    LMS.borrowings = borrowings;
    closeIssue(); render(); toast('success','📋','Book issued successfully!');
  }

  function openReturn(id) {
    returningId = id;
    const b = borrowings.find(x => x.id == id);
    const m = LMS.members.find(x => x.id == b.user_id);
    const bk = LMS.books.find(x => x.id == b.book_id);
    const days = daysOverdue(b.due_date);
    const fine = Math.max(0, days) * 10;

    document.getElementById('return-body').innerHTML = `
      <p style="margin-bottom:1rem;color:var(--text-muted)">Process return for:</p>
      <div style="background:var(--surface-2);border-radius:12px;padding:1rem;margin-bottom:1rem">
        <div style="font-weight:700;font-size:1rem;margin-bottom:.25rem">${bk?.title || 'Unknown Book'}</div>
        <div style="font-size:.85rem;color:var(--text-muted)">Borrowed by ${m?.name || 'Unknown Member'}</div>
      </div>
      ${days > 0 ? `<div style="background:var(--danger-light);border-radius:10px;padding:.85rem;color:var(--danger);font-weight:600;font-size:.9rem">⚠️ ${days} days overdue — Fine: Rs.${fine}</div>` 
                 : '<div style="background:var(--success-light);border-radius:10px;padding:.85rem;color:var(--success);font-weight:600;font-size:.9rem">✅ On time — No fine.</div>'}`;
    document.getElementById('return-modal').classList.add('open');
  }

  function confirmReturn() {
    const b = borrowings.find(x => x.id == returningId);
    if(b) {
      const days = daysOverdue(b.due_date);
      b.returned_date = new Date().toISOString().split('T')[0];
      b.fine = Math.max(0, days) * 10;
      LMS.borrowings = borrowings;
      document.getElementById('return-modal').classList.remove('open');
      render(); toast('success','↩','Book returned successfully!');
    }
  }

  function deleteBorrowing(id) { 
    if(confirm('Delete record?')) {
      borrowings = borrowings.filter(x => x.id != id); 
      LMS.borrowings = borrowings; 
      render(); 
      toast('warning','🗑','Record deleted.'); 
    }
  }

  /* ── 6. UTILITIES ── */
  function toast(type,icon,msg){
    const container = document.getElementById('toast-container');
    if(!container) return;
    const el = document.createElement('div');
    el.className = `toast ${type}`;
    el.innerHTML = `<span class="toast-icon">${icon}</span><span class="toast-msg">${msg}</span>`;
    container.appendChild(el);
    setTimeout(() => { el.classList.add('removing'); setTimeout(() => el.remove(), 300); }, 3200);
  }

  ['issue-modal', 'return-modal'].forEach(id => {
    const el = document.getElementById(id);
    if(el) el.addEventListener('click', e => { if(e.target === e.currentTarget) e.currentTarget.classList.remove('open'); });
  });

  document.addEventListener('keydown', e => {
    if(e.key === 'Escape') ['issue-modal', 'return-modal'].forEach(id => {
      const el = document.getElementById(id);
      if(el) el.classList.remove('open');
    });
  });
</script>
</body>
</html>
