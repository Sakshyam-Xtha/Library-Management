<?php
require_once('../includes/auth.php');
$pageTitle  = 'Overdue Books';
$activePage = 'overdue';
$topbarRole = 'admin';
$footerSearchAction = "location.href='admin-overdue.php'";
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
    <div>
      <p class="page-eyebrow">Admin / Overdue</p>
      <h1 class="page-title">Overdue Books</h1>
      <p class="page-subtitle" id="overdue-subtitle">Loading…</p>
    </div>
    <button class="btn btn-primary" onclick="sendAllReminders()">📬 Send All Reminders</button>
  </div>

  <!-- Search toolbar -->
  <div class="table-card" style="margin-bottom:1.5rem;padding:.75rem 1.25rem">
    <div class="table-toolbar" style="margin:0">
      <span class="table-title">⚠️ Overdue Records</span>
      <div class="table-search">
        <span>🔍</span>
        <input type="text" id="search-input" placeholder="Search by member or book…" oninput="render()" style="background: none; border:none;"/>
      </div>
    </div>
  </div>

  <!-- Overdue cards -->
  <div id="overdue-grid" style="display:flex;flex-direction:column;gap:1rem;margin-bottom:1.75rem"></div>

  <!-- Empty state -->
  <div id="empty-overdue" style="display:none;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:4rem 2rem;text-align:center;animation:fadeUp .4s both">
    <div style="font-size:3.5rem;margin-bottom:1rem">✅</div>
    <h3 style="font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:900;color:var(--text);margin-bottom:.5rem">No Overdue Books</h3>
    <p style="color:var(--text-muted);font-size:.9rem;max-width:340px;margin:0 auto;line-height:1.6">All borrowed books are within their due dates. Keep up the great work!</p>
  </div>

</main>

<!-- PROCESS RETURN MODAL -->
<div class="modal-backdrop" id="return-modal">
  <div class="modal" style="max-width:460px">
    <div class="modal-header">
      <span class="modal-title">↩ Process Return</span>
      <button class="modal-close" onclick="closeModal('return-modal')">✕</button>
    </div>
    <div class="modal-body" id="return-modal-body"></div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('return-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="confirmReturn()">✅ Confirm Return</button>
    </div>
  </div>
</div>

<!-- SEND REMINDER MODAL -->
<div class="modal-backdrop" id="reminder-modal">
  <div class="modal" style="max-width:420px">
    <div class="modal-header">
      <span class="modal-title">📬 Send Reminder</span>
      <button class="modal-close" onclick="closeModal('reminder-modal')">✕</button>
    </div>
    <div class="modal-body">
      <div id="reminder-modal-body"></div>
      <div class="form-group" style="margin-top:1rem">
        <label>Message</label>
        <textarea class="form-control" id="reminder-msg" rows="4" style="resize:vertical"></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('reminder-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="confirmReminder()">📬 Send</button>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
<script>
  let searchQ = '';

  function closeModal(id){ document.getElementById(id).classList.remove('open'); }
  function daysOver(d){ return Math.max(0, Math.floor((new Date() - new Date(d)) / 86400000)); }

  function render() {
    searchQ = (document.getElementById('search-input').value || '').toLowerCase();

    // Admin borrowings from load_data use: b.due, b.returned, b.memberId, b.bookId
    const overdues = LMS.borrowings.filter(b => !b.returned_date && b.due_date && new Date(b.due_date) < new Date());

    const data = overdues.filter(b => {
      if (!searchQ) return true;
      const m  = LMS.getMember(b.member_id);
      const bk = LMS.getBook(b.book_id);
      return `${m?.name||''} ${bk?.title||''}`.toLowerCase().includes(searchQ);
    });

    document.getElementById('overdue-subtitle').textContent =
      overdues.length
        ? `${overdues.length} book${overdues.length !== 1 ? 's' : ''} past due date`
        : 'No overdue books right now';

    document.getElementById('empty-overdue').style.display  = data.length ? 'none' : 'block';
    document.getElementById('overdue-grid').innerHTML = data.map(b => {
      const m   = LMS.getMember(b.member_id);
      const bk  = LMS.getBook(b.book_id);
      const days = daysOver(b.due_date);
      const fine = days * 10;
      const color   = days > 7 ? 'var(--danger)'       : days > 3 ? 'var(--warning)'       : 'var(--accent-2)';
      const colorBg = days > 7 ? 'var(--danger-light)'  : days > 3 ? 'var(--warning-light)'  : 'var(--accent-2-light)';
      const parts   = m?.name?.trim().split(' ') || ['?'];
      const initials= (parts[0][0] + (parts[1]?.[0] || '')).toUpperCase();
      return `<div style="background:var(--surface);border:1px solid var(--border);border-left:4px solid ${color};border-radius:var(--radius-lg);padding:1.25rem 1.5rem;display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;animation:fadeUp .4s both">
        <div style="width:44px;height:60px;border-radius:8px;background:var(--surface-2);border:1px solid var(--border);display:grid;place-items:center;font-size:1.6rem;flex-shrink:0">${bk?.cover||'📚'}</div>
        <div style="flex:1;min-width:160px">
          <div style="font-weight:700;font-size:1rem;margin-bottom:.15rem">${bk?.title||'Unknown'}</div>
          <div style="font-size:.8rem;color:var(--text-muted)">${bk?.author||''}</div>
          <div style="font-size:.75rem;color:var(--text-muted);margin-top:.15rem">Due: ${b.due}</div>
        </div>
        <div style="display:flex;align-items:center;gap:.6rem">
          <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#1a4a8a,#2563eb);display:grid;place-items:center;font-size:.75rem;font-weight:700;color:#fff">${initials}</div>
          <div><div style="font-weight:600;font-size:.875rem">${m?.name||'Unknown'}</div><div style="font-size:.75rem;color:var(--text-muted)">${m?.role||''}</div></div>
        </div>
        <div style="text-align:center;padding:.6rem 1rem;background:${colorBg};border-radius:10px;min-width:72px">
          <div style="font-size:1.4rem;font-weight:900;color:${color};font-family:'Playfair Display',serif">${days}</div>
          <div style="font-size:.7rem;color:${color};font-weight:700;text-transform:uppercase;letter-spacing:.05em">days late</div>
        </div>
        <div style="text-align:center;min-width:56px">
          <div style="font-size:1rem;font-weight:700;color:${color}">Rs.${fine}</div>
          <div style="font-size:.72rem;color:var(--text-muted)">Fine</div>
        </div>
        <div style="display:flex;gap:.5rem">
          <button class="btn btn-outline" style="padding:.4rem .9rem;font-size:.8rem" onclick="openReminder(${b.id})">📬 Remind</button>
          <button class="btn" style="padding:.4rem .9rem;font-size:.8rem;background:linear-gradient(135deg,var(--success),#27ae60);color:#fff;border:none;border-radius:8px;cursor:pointer;font-weight:600" onclick="openReturn(${b.id})">↩ Return</button>
        </div>
      </div>`;
    }).join('');
  }

  /* ── Return ── */
  let returningId = null;
  function openReturn(bid) {
    returningId = bid;
    const b   = LMS.borrowings.find(x => x.id === bid);
    const m   = LMS.getMember(b.member_id);
    const bk  = LMS.getBook(b.book_id);
    const days = daysOver(b.due_date);
    const fine = days * 10;
    document.getElementById('return-modal-body').innerHTML = `
      <p style="margin-bottom:1rem;color:var(--text-muted)">Confirm return for:</p>
      <div style="background:var(--surface-2);border-radius:12px;padding:1rem 1.2rem;margin-bottom:1rem;display:flex;gap:.85rem;align-items:center">
        <span style="font-size:2rem">${bk?.cover||'📚'}</span>
        <div>
          <div style="font-weight:700;font-size:1rem">${bk?.title||'—'}</div>
          <div style="font-size:.8rem;color:var(--text-muted)">Borrowed by <strong>${m?.name||'—'}</strong></div>
        </div>
      </div>
      <div style="background:var(--danger-light);border-radius:10px;padding:.85rem;color:var(--danger);font-weight:600;font-size:.9rem">
        ⚠️ ${days} day${days!==1?'s':''} overdue — Fine: Rs.${fine}
      </div>`;
    document.getElementById('return-modal').classList.add('open');
  }

  function confirmReturn() {
    const b = LMS.borrowings.find(x => x.id === returningId);
    if (!b) return;
    fetch('actions/return_book.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ borrowing_id: b.id })
    }).then(r => r.json()).then(d => {
      LMS.toast(d.success ? 'success' : 'error', d.success ? '↩' : '⚠️', d.message || 'Done');
      if (d.success) LMS.syncData();
    }).catch(() => LMS.toast('error', '⚠️', 'Network error.'));
    closeModal('return-modal');
  }

  /* ── Reminder ── */
  let remindingId = null;
  function openReminder(bid) {
    remindingId = bid;
    const b   = LMS.borrowings.find(x => x.id === bid);
    const m   = LMS.getMember(b.member_id);
    const bk  = LMS.getBook(b.book_id);
    const days = daysOver(b.due_date);
    document.getElementById('reminder-modal-body').innerHTML = `
      <div style="background:var(--surface-2);border-radius:10px;padding:.85rem;margin-bottom:.25rem">
        <div style="font-weight:700">${m?.name||'—'}</div>
        <div style="font-size:.8rem;color:var(--text-muted)">${m?.email||''}</div>
      </div>`;
    document.getElementById('reminder-msg').value =
      `Dear ${m?.name||'Member'},\n\nThis is a reminder that "${bk?.title||'your book'}" is ${days} day${days!==1?'s':''} overdue. Please return it as soon as possible to avoid additional fines.\n\nThank you,\nLibrary Admin`;
    document.getElementById('reminder-modal').classList.add('open');
  }

  function confirmReminder() {
    const b = LMS.borrowings.find(x => x.id === remindingId);
    const m = LMS.getMember(b?.member_id);
    closeModal('reminder-modal');
    LMS.toast('success', '📬', `Reminder sent to ${m?.name||'member'}!`);
  }

  function sendAllReminders() {
    const n = LMS.borrowings.filter(b => !b.returned_date && b.due_date && new Date(b.due_date) < new Date()).length;
    if (!n) { LMS.toast('info', '✅', 'No overdue books to remind.'); return; }
    LMS.toast('info', '📬', `${n} reminder${n!==1?'s':''} sent to all overdue members!`);
  }

  /* ── Modal backdrop / ESC ── */
  ['return-modal','reminder-modal'].forEach(id => {
    document.getElementById(id).addEventListener('click', e => { if (e.target === e.currentTarget) closeModal(id); });
  });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') ['return-modal','reminder-modal'].forEach(closeModal); });

  document.addEventListener('lmsDataLoaded', render);
</script>
</body>
</html>
