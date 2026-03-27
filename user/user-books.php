<?php
require_once('../includes/auth.php');
$pageTitle  = 'My Books';
$activePage = 'books';
$topbarRole = 'user';
$footerSearchAction = "location.href='user-catalogue.php'";
$extraCss = <<<'CSS'
.due-ok    {color:var(--success);background:var(--success-light);padding:.18rem .55rem;border-radius:6px;font-size:.78rem;font-weight:700}
.due-warn  {color:var(--warning);background:var(--warning-light);padding:.18rem .55rem;border-radius:6px;font-size:.78rem;font-weight:700}
.due-danger{color:var(--danger); background:var(--danger-light); padding:.18rem .55rem;border-radius:6px;font-size:.78rem;font-weight:700}
.prog-wrap{width:80px;height:5px;background:var(--surface-3);border-radius:99px;overflow:hidden;display:inline-block;vertical-align:middle}
.prog-fill{height:100%;border-radius:99px;transition:width 1s ease}
CSS;
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?php echo $currentTheme; ?>">
<head><?php include '../includes/head.php'; ?></head>
<body>
<?php include '../includes/user-sidebar.php'; ?>
<div class="main">
<?php include '../includes/topbar.php'; ?>
<main class="content">

  <div class="page-header">
    <div><p class="page-eyebrow">My Library</p><h1 class="page-title">My Books</h1><p class="page-subtitle" id="borrow-subtitle">Loading…</p></div>
    <button class="btn btn-primary" onclick="location.href='user-catalogue.php'">🔍 Browse Catalogue</button>
  </div>

  <div class="stats-grid" style="grid-template-columns:repeat(3,1fr)">
    <div class="stat-card" style="animation-delay:.05s">
      <div class="stat-card-top"><span class="stat-label">Borrowed</span><div class="stat-icon-wrap" style="background:var(--accent-light)">📚</div></div>
      <div class="stat-number" id="s-borrowed">—</div>
      <div style="font-size:.78rem;color:var(--text-muted);margin-top:.3rem">Current active loans</div>
      <div class="stat-bar" style="background:linear-gradient(90deg,var(--accent),#2563eb)"></div>
    </div>
    <div class="stat-card" style="animation-delay:.1s">
      <div class="stat-card-top"><span class="stat-label">Due Soon</span><div class="stat-icon-wrap" style="background:var(--warning-light)">⏰</div></div>
      <div class="stat-number" id="s-due">—</div>
      <div style="font-size:.78rem;color:var(--text-muted);margin-top:.3rem">Within next 7 days</div>
      <div class="stat-bar" style="background:linear-gradient(90deg,var(--warning),#f39c12)"></div>
    </div>
    <div class="stat-card" style="animation-delay:.15s">
      <div class="stat-card-top"><span class="stat-label">Overdue</span><div class="stat-icon-wrap" style="background:var(--danger-light)">⚠️</div></div>
      <div class="stat-number" id="s-over">—</div>
      <div style="font-size:.78rem;color:var(--text-muted);margin-top:.3rem" id="s-fine-label">No fines pending</div>
      <div class="stat-bar" style="background:linear-gradient(90deg,var(--danger),#e74c3c)"></div>
    </div>
  </div>

  <div class="table-card">
    <div class="table-toolbar"><span class="table-title">📖 My Borrowed Books</span></div>
    <div style="overflow-x:auto">
      <table>
        <thead><tr><th>Book</th><th>Genre</th><th>Issued</th><th>Due Date</th><th>Progress</th><th>Fine</th><th>Actions</th></tr></thead>
        <tbody id="books-tbody"><tr><td colspan="7" style="text-align:center;padding:3rem;color:var(--text-muted)">
              <div class="spinner" style="margin-bottom:1rem"></div>
              Synchronizing with library database...
            </td></tr></tbody>
      </table>
    </div>
    <div id="empty-books" class="empty-state" style="display:none">
      <div class="empty-state-icon">📭</div><h3>No books borrowed</h3>
      <p>Browse the catalogue to find your next great read!</p>
      <button class="btn btn-primary" style="margin-top:1rem" onclick="location.href='user-catalogue.php'">🔍 Browse Catalogue</button>
    </div>
  </div>

</main>

<!-- RENEW MODAL -->
<div class="modal-backdrop" id="renew-modal">
  <div class="modal" style="max-width:420px">
    <div class="modal-header"><span class="modal-title">🔄 Renew Book</span><button class="modal-close" onclick="closeModal('renew-modal')">✕</button></div>
    <div class="modal-body" id="renew-body"></div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('renew-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="confirmRenew()">🔄 Renew Now</button>
    </div>
  </div>
</div>

<!-- RETURN MODAL -->
<div class="modal-backdrop" id="return-modal">
  <div class="modal" style="max-width:420px">
    <div class="modal-header"><span class="modal-title">↩ Return Book</span><button class="modal-close" onclick="closeModal('return-modal')">✕</button></div>
    <div class="modal-body" id="return-body"></div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('return-modal')">Cancel</button>
      <button class="btn btn-primary" id="return-confirm-btn" onclick="confirmReturn()">↩ Return Book</button>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
<script>
window.phpSession = {
  userId: <?php echo json_encode($userId); ?>,
  name: <?php echo json_encode($userName); ?>,
  role: <?php echo json_encode($userRole); ?>,
  theme: <?php echo json_encode($currentTheme); ?>
};
const BG = ['linear-gradient(160deg,#e8f0fb,#c5d8f5)','linear-gradient(160deg,#fdf3e1,#f5dfa5)','linear-gradient(160deg,#fbeae8,#f5c5c2)','linear-gradient(160deg,#e6f5ed,#b3e0c5)','linear-gradient(160deg,#f3e8ff,#e5c5ff)'];
let renewingId=null, returningId=null, currentBookId=null, searchQ='';

function closeModal(id){ document.getElementById(id).classList.remove('open'); }
function daysUntil(d){ return Math.ceil((new Date(d)-new Date())/86400000); }
function daysOver(d) { return Math.max(0,-daysUntil(d)); }
function fmtDate(d)  { return d ? new Date(d).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'}) : '—'; }

function render(){
  // 1. USE THE NEW SHARED HELPER
  // This function in your shared.js already filters by userId and merges book info
  const borrowed = LMS.getUserBorrowedBooks();
  console.log('Borrowed Books:', borrowed);
  console.log('LMS.borrowings:', LMS.borrowings);
  console.log('LMS.session:', LMS.session);
  const now      = new Date();
  const overdue  = borrowed.filter(b => b.due_date && new Date(b.due_date) < now);
  const dueSoon  = borrowed.filter(b => { const d = daysUntil(b.due_date); return d >= 0 && d <= 7; });
  const totalFine = overdue.reduce((a, b) => a + (daysOver(b.due_date) * 10), 0);

  // Update Stats
  document.getElementById('s-borrowed').textContent = borrowed.length;
  document.getElementById('s-due').textContent      = dueSoon.length;
  document.getElementById('s-over').textContent     = overdue.length;
  document.getElementById('s-fine-label').textContent = totalFine > 0 ? `Rs.${totalFine} fine` : 'no fines';
  
  const subtitle = document.getElementById('borrow-subtitle');
  if(subtitle) subtitle.textContent = `${borrowed.length} book${borrowed.length !== 1 ? 's' : ''} currently borrowed`;

  // Filter based on search input
  const data = borrowed.filter(b => !searchQ || `${b.title} ${b.author}`.toLowerCase().includes(searchQ));
  
  // Show/Hide Empty State
  document.getElementById('empty-books').style.display = borrowed.length ? 'none' : 'block';

  // Render Table
  document.getElementById('books-tbody').innerHTML = data.length ? data.map((b, i) => {
    const isOvd = b.due_date && new Date(b.due_date) < now;
    const days  = daysUntil(b.due_date);
    const over  = daysOver(b.due_date);
    const fine  = isOvd ? over * 10 : 0;
    
    const dueTag = isOvd
      ? `<span class="due-danger">${over}d overdue</span>`
      : days === 0 ? `<span class="due-warn">Due today</span>`
      : days <= 3  ? `<span class="due-warn">${days}d left</span>`
      : `<span class="due-ok">${fmtDate(b.due_date)}</span>`;
    
    const pct  = isOvd ? 100 : Math.min(100, Math.max(5, 100 - (days / 14 * 100)));
    const pCol = isOvd ? 'var(--danger)' : days <= 3 ? 'var(--warning)' : 'var(--accent)';

    return `<tr style="animation:fadeUp .3s ${i * .04}s both">
      <td><div class="td-book">
        <div class="book-cover" style="background:${BG[i % BG.length]}">${b.cover || '📚'}</div>
        <div><div class="book-title">${b.title}</div><div class="book-author">${b.author}</div></div>
      </div></td>
      <td><span class="badge badge-accent">${b.genre || '—'}</span></td>
      <td style="font-size:.82rem;color:var(--text-muted)">${fmtDate(b.issue_date)}</td>
      <td>${dueTag}</td>
      <td><div class="prog-wrap"><div class="prog-fill" style="width:${pct}%;background:${pCol}"></div></div></td>
      <td>${fine ? `<span style="color:var(--danger);font-weight:700">Rs.${fine}</span>` : `<span style="color:var(--text-muted)">—</span>`}</td>
      <td><div class="action-btns">
        ${!isOvd ? `<button class="action-btn" title="Renew" onclick="openRenew(${b.borrowing_id},'${b.title.replace(/'/g, "\\'")}', '${b.due_date}')">🔄</button>` : ''}
        <button class="action-btn ${isOvd ? 'del' : ''}" title="Return" onclick="openReturn(${b.borrowing_id},'${b.title.replace(/'/g, "\\'")}', '${b.due_date}')">↩</button>
      </div></td>
    </tr>`;
  }).join('') : `<tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--text-muted)">No results match your search.</td></tr>`;
}

/* ── MODAL ACTIONS (Now using borrowing_id as per shared.js) ── */
function openRenew(bid, title, due){
  renewingId = bid;
  const nd = new Date(due); nd.setDate(nd.getDate() + 14);
  document.getElementById('renew-body').innerHTML = `
    <p style="color:var(--text-muted);line-height:1.6">Renew <strong style="color:var(--text)">${title}</strong>?</p>
    <div style="background:var(--accent-light);border-radius:10px;padding:.85rem;margin-top:.75rem">
      <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.1rem">New due date</div>
      <div style="font-weight:700;color:var(--accent)">${nd.toLocaleDateString('en-US', {month: 'long', day: 'numeric', year: 'numeric'})}</div>
    </div>`;
  document.getElementById('renew-modal').classList.add('open');
}

function confirmRenew(){
  fetch('actions/renew_book.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ borrowing_id: renewingId })
  })
  .then(r => r.json()).then(d => { 
    LMS.toast(d.success ? 'success' : 'error', d.success ? '🔄' : '⚠️', d.message || 'Done'); 
    if(d.success) LMS.syncData(); 
  })
  .catch(() => LMS.toast('error', '⚠️', 'Network error.'));
  closeModal('renew-modal');
}

function openReturn(bid, title, due){
  returningId = bid;
  // Find the borrowing record to get the book_id
  const borrowing = LMS.borrowings.find(b => b.id == bid);
  currentBookId = borrowing ? borrowing.book_id : null;
  const over = Math.max(0, Math.ceil((new Date() - new Date(due)) / 86400000));
  const fine = over * 10, isLate = new Date(due) < new Date();
  document.getElementById('return-body').innerHTML = `
    <p style="color:var(--text-muted);line-height:1.6;margin-bottom:.75rem">Return <strong style="color:var(--text)">${title}</strong>?</p>
    ${isLate
      ? `<div style="background:var(--danger-light);border-radius:10px;padding:.85rem;color:var(--danger);font-weight:600">⚠️ ${over} day${over !== 1 ? 's' : ''} overdue — Fine: Rs.${fine}</div>`
      : `<div style="background:var(--success-light);border-radius:10px;padding:.85rem;color:var(--success);font-weight:600">✅ On time — No fine.</div>`}`;
  document.getElementById('return-confirm-btn').textContent = isLate ? `⚠️ Return & Pay Rs.${fine}` : '↩ Return Book';
  document.getElementById('return-modal').classList.add('open');
}

function confirmReturn(){
  const return_data = {
    id:returningId,
    book_id:currentBookId
  }
  fetch('../actions/return_book.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(return_data)
  })
  .then(r => r.json()).then(d => { 
    LMS.toast(d.success ? 'success' : 'error', d.success ? '↩' : '⚠️', d.message || 'Done'); 
    if(d.success) LMS.syncData(); 
  })
  .catch(() => LMS.toast('error', '⚠️', 'Network error.'));
  closeModal('return-modal');
}

// Event Listeners
const searchInput = document.getElementById('search-input');
if (searchInput) {
  searchInput.addEventListener('input', e => { 
    searchQ = e.target.value.toLowerCase(); 
    render(); 
  });
}

['renew-modal','return-modal'].forEach(id => {
  document.getElementById(id).addEventListener('click', e => { if(e.target === e.currentTarget) closeModal(id); });
});

document.addEventListener('keydown', e => { if(e.key === 'Escape') ['renew-modal', 'return-modal'].forEach(closeModal); });

// This ensures render only runs once the shared.js fetch is complete
document.addEventListener('lmsDataLoaded', render);
</script>
</body>
</html>
