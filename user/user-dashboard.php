<?php
require_once('../includes/auth.php');
$pageTitle  = 'My Library';
$activePage = 'dashboard';
$topbarRole = $_SESSION['role'];
$userId     = $_SESSION['user_id']; // Get the user ID from the session
$footerSearchAction = "location.href='user-catalogue.php'";
$extraCss = <<<'CSS'
.welcome-banner{background:linear-gradient(135deg,var(--accent) 0%,#1e3a7a 60%,#1a4a2a 100%);border-radius:var(--radius-lg);padding:2rem 2.5rem;display:flex;align-items:center;gap:2rem;margin-bottom:1.75rem;position:relative;overflow:hidden;animation:fadeUp .4s both}
.welcome-banner::before{content:'';position:absolute;inset:0;background:url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Ccircle cx='15' cy='15' r='1'/%3E%3C/g%3E%3C/svg%3E")}
.welcome-text{position:relative;z-index:1;flex:1}
.welcome-text h2{font-family:'Playfair Display',serif;font-size:1.5rem;font-weight:900;color:#fff;margin-bottom:.35rem}
.welcome-text p{font-size:.9rem;color:rgba(255,255,255,.75);line-height:1.5}
.welcome-stats{display:flex;gap:2rem;position:relative;z-index:1;flex-shrink:0}
.w-stat{text-align:center}
.w-stat-num{font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:900;color:#fff}
.w-stat-label{font-size:.72rem;color:rgba(255,255,255,.6)}
.welcome-visual{font-size:4rem;position:relative;z-index:1;animation:floatBook 4s ease-in-out infinite;flex-shrink:0}
@keyframes floatBook{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}
.due-ok    {color:var(--success);background:var(--success-light);padding:.18rem .55rem;border-radius:6px;font-size:.78rem;font-weight:700;white-space:nowrap}
.due-warn  {color:var(--warning);background:var(--warning-light);padding:.18rem .55rem;border-radius:6px;font-size:.78rem;font-weight:700;white-space:nowrap}
.due-danger{color:var(--danger); background:var(--danger-light); padding:.18rem .55rem;border-radius:6px;font-size:.78rem;font-weight:700;white-space:nowrap}
.prog-wrap{width:90px;height:5px;background:var(--surface-3);border-radius:99px;overflow:hidden}
.prog-fill{height:100%;border-radius:99px;transition:width 1s ease}
.genre-tag{padding:.3rem .85rem;border-radius:99px;font-size:.78rem;font-weight:600;border:1.5px solid var(--border);color:var(--text-muted);background:var(--surface);cursor:pointer;transition:all var(--transition)}
.genre-tag.active,.genre-tag:hover{border-color:var(--accent);color:var(--accent);background:var(--accent-light)}
@media(max-width:700px){.welcome-stats,.welcome-visual{display:none}}
CSS;
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?php $currentTheme; ?>">
<head>
<?php include '../includes/head.php'; ?>
</head>
<body>

<?php include '../includes/user-sidebar.php'; ?>

<div class="main">
<?php include '../includes/topbar.php'; ?>

<main class="content">

  <div class="page-header">
    <div>
      <p class="page-eyebrow">Member Portal</p>
      <h1 class="page-title">My Dashboard</h1>
      <p class="page-subtitle" id="dash-date"></p>
    </div>
    <button class="btn btn-primary" onclick="location.href='user-catalogue.php'">🔍 Browse Catalogue</button>
  </div>

  <!-- Welcome Banner -->
  <div class="welcome-banner">
    <div class="welcome-text">
      <h2 id="welcome-msg">Welcome back! <?php echo $userName; ?>👋</h2>
      <p id="banner-sub">Loading your library summary…</p>
    </div>
    <div class="welcome-stats">
      <div class="w-stat"><div class="w-stat-num" id="wb-borrowed">—</div><div class="w-stat-label">Borrowed</div></div>
      <div class="w-stat"><div class="w-stat-num" id="wb-due">—</div><div class="w-stat-label">Due Soon</div></div>
      <div class="w-stat"><div class="w-stat-num" id="wb-overdue">—</div><div class="w-stat-label">Overdue</div></div>
    </div>
    <div class="welcome-visual">📖</div>
  </div>

  <!-- Stat Cards -->
  <div class="stats-grid">
    <div class="stat-card" style="cursor:pointer" onclick="location.href='user-books.php'">
      <div class="stat-card-top"><span class="stat-label">Currently Borrowed</span><div class="stat-icon-wrap" style="background:var(--accent-light)">📚</div></div>
      <div class="stat-number" id="s-borrowed">—</div>
      <div class="stat-change neutral">of 5 allowed</div>
    </div>
    <div class="stat-card" style="cursor:pointer" onclick="location.href='user-books.php'">
      <div class="stat-card-top"><span class="stat-label">Due Soon</span><div class="stat-icon-wrap" style="background:var(--warning-light)">⏰</div></div>
      <div class="stat-number" id="s-due">—</div>
      <div class="stat-change neutral">within 7 days</div>
    </div>
    <div class="stat-card" style="cursor:pointer" onclick="location.href='user-books.php'">
      <div class="stat-card-top"><span class="stat-label">Overdue</span><div class="stat-icon-wrap" style="background:var(--danger-light)">⚠️</div></div>
      <div class="stat-number" id="s-overdue">—</div>
      <div class="stat-change neutral" id="s-fine-note">no fines</div>
    </div>
    <div class="stat-card" style="cursor:pointer" onclick="location.href='user-wishlist.php'">
      <div class="stat-card-top"><span class="stat-label">Wishlist</span><div class="stat-icon-wrap" style="background:var(--accent-2-light)">🔖</div></div>
      <div class="stat-number" id="s-wish">—</div>
      <div class="stat-change neutral">saved for later</div>
    </div>
  </div>

  <!-- Currently Borrowed Table -->
  <div style="margin-bottom:1.75rem;animation:fadeUp .4s .15s both">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem">
      <h2 style="font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:800;color:var(--text)">📖 Currently Borrowed</h2>
      <a href="user-books.php" style="font-size:.82rem;font-weight:600;color:var(--accent)">View All →</a>
    </div>
    <div class="table-card">
      <div style="overflow-x:auto">
        <table>
          <thead><tr><th>Book</th><th>Genre</th><th>Due Date</th><th>Progress</th><th>Actions</th></tr></thead>
          <tbody id="borrowed-tbody"><tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--text-muted)">Loading…</td></tr></tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Quick Search + Genre Tags -->
  <div style="margin-bottom:1.75rem;animation:fadeUp .4s .2s both">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem">
      <h2 style="font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:800;color:var(--text)">🔍 Quick Search</h2>
    </div>
    <div style="display:flex;gap:.75rem;margin-bottom:1rem">
      <div style="flex:1;display:flex;align-items:center;gap:.6rem;background:var(--surface);border:1.5px solid var(--border);border-radius:12px;padding:.7rem 1.2rem;transition:border-color var(--transition),box-shadow var(--transition)" id="srch-wrap">
        <span style="color:var(--text-muted)">🔍</span>
        <input type="text" id="cat-search" placeholder="Search by title, author, or genre…" style="flex:1;background:none;border:none;outline:none;font-size:.9rem;color:var(--text)" />
      </div>
      <button class="btn btn-primary" onclick="goSearch()">Search</button>
    </div>
    <div style="display:flex;gap:.5rem;flex-wrap:wrap" id="genre-tags"></div>
  </div>

  <!-- Two-col: Available Now + Notifications -->
  <div class="two-col" style="animation:fadeUp .4s .25s both">
    <div class="card">
      <div class="card-header">
        <span class="card-title">📚 Available to Borrow</span>
        <a class="card-action" href="user-catalogue.php" style="color:var(--accent);font-size:.82rem;font-weight:600">Browse All →</a>
      </div>
      <div class="card-body" style="padding:0">
        <div style="overflow-x:auto">
          <table>
            <thead><tr><th>Book</th><th>Genre</th><th></th></tr></thead>
            <tbody id="available-tbody"><tr><td colspan="3" style="text-align:center;padding:2rem;color:var(--text-muted)">Loading…</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        <span class="card-title">🔔 Recent Notifications</span>
        <a class="card-action" href="user-notifications.php" style="color:var(--accent);font-size:.82rem;font-weight:600">View All →</a>
      </div>
      <div class="card-body" id="notif-preview"><p style="color:var(--text-muted);font-size:.875rem">No notifications yet.</p></div>
    </div>
  </div>

</main>

<!-- RENEW MODAL -->
<div class="modal-backdrop" id="renew-modal">
  <div class="modal" style="max-width:420px">
    <div class="modal-header">
      <span class="modal-title">🔄 Renew Book</span>
      <button class="modal-close" onclick="document.getElementById('renew-modal').classList.remove('open')">✕</button>
    </div>
    <div class="modal-body" id="renew-body"></div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="document.getElementById('renew-modal').classList.remove('open')">Cancel</button>
      <button class="btn btn-primary" onclick="confirmRenew()">🔄 Confirm Renewal</button>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
<script>
document.getElementById('dash-date').textContent = new Date().toLocaleDateString('en-US',{weekday:'long',year:'numeric',month:'long',day:'numeric'});

const BG = ['linear-gradient(160deg,#e8f0fb,#c5d8f5)','linear-gradient(160deg,#fdf3e1,#f5dfa5)','linear-gradient(160deg,#fbeae8,#f5c5c2)','linear-gradient(160deg,#e6f5ed,#b3e0c5)','linear-gradient(160deg,#f3e8ff,#e5c5ff)'];

function daysUntil(d){ return Math.ceil((new Date(d) - new Date()) / 86400000); }

function dueBadge(due){
  const d = daysUntil(due);
  if(d < 0)  return `<span class="due-danger">${Math.abs(d)}d overdue</span>`;
  if(d === 0) return `<span class="due-warn">Due today</span>`;
  if(d <= 5)  return `<span class="due-warn">Due in ${d}d</span>`;
  return `<span class="due-ok">${new Date(due).toLocaleDateString('en-US',{month:'short',day:'numeric'})}</span>`;
}

let renewingId = null;

function renderDashboard(){
  const now = new Date();

  /* ── Build borrowed list ──
     Filter by userId from PHP session */
  const userId = <?php echo json_encode($userId); ?>; // Ensure userId is correctly passed from PHP
  const borrowed = LMS.borrowings
    .filter(b => b.member_id === userId && !b.returned_date) // Filter by userId and non-returned
    .map(b => {
      const book = LMS.getBook(b.book_id);
      return {
        ...b,
        title:  book ? book.title  : 'Unknown',
        author: book ? book.author : '',
        genre:  book ? book.genre  : '—',
        cover:  book ? book.cover  : '📚',
        borrowing_id: b.id
      };
    });

  const overdue  = borrowed.filter(b => b.due_date && daysUntil(b.due_date) < 0);
  const dueSoon  = borrowed.filter(b => b.due_date && daysUntil(b.due_date) >= 0 && daysUntil(b.due_date) <= 7);
  const overdueCount = overdue.length;
  const dueSoonCount = dueSoon.length;
  const fine     = overdue.reduce((sum, b) => sum + Math.abs(daysUntil(b.due_date)) * 10, 0);
  const wishlist = LMS.wishlist;

  /* ── Banner ── */
  document.getElementById('banner-sub').innerHTML = overdue.length
    ? `You have <strong style="color:#fff">${overdue.length} overdue book${overdue.length!==1?'s':''}</strong>. Please return them to avoid further fines.`
    : dueSoon.length
      ? `You have <strong style="color:#fff">${dueSoon.length} book${dueSoon.length!==1?'s':''} due soon</strong>. Keep up the great reading!`
      : `All books are on time. Explore the catalogue for your next read!`;

  /* ── Stat cards ── */
  document.getElementById('s-borrowed').textContent  = borrowed.length;
  document.getElementById('s-due').textContent        = dueSoonCount;
  document.getElementById('s-overdue').textContent    = overdueCount;
  document.getElementById('s-fine-note').textContent  = fine > 0 ? `Rs.${fine} outstanding` : 'no fines';
  document.getElementById('s-wish').textContent       = wishlist.length;

  /* ── Welcome banner mini-stats ── */
  document.getElementById('wb-borrowed').textContent = borrowed.length;
  document.getElementById('wb-due').textContent      = dueSoonCount;
  document.getElementById('wb-overdue').textContent  = overdueCount;

  /* ── Currently Borrowed table ── */
  const tbody = document.getElementById('borrowed-tbody');
  if(!borrowed.length){
    tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;padding:2.5rem;color:var(--text-muted)">
      <div style="font-size:2.5rem;margin-bottom:.5rem">📚</div>
      No active borrowings. <a href="user-catalogue.php" style="color:var(--accent);font-weight:600">Browse the catalogue →</a>
    </td></tr>`;
  } else {
    tbody.innerHTML = borrowed.map((b, i) => {
      const days = b.due_date ? daysUntil(b.due_date) : 99;
      /* Progress bar: fills as due date approaches over a 14-day loan */
      const pct  = b.due_date ? Math.min(100, Math.max(5, 100 - (days / 14 * 100))) : 50;
      const pCol = days < 0 ? 'var(--danger)' : days <= 5 ? 'var(--warning)' : 'var(--accent)';
      return `<tr>
        <td><div class="td-book">
          <div class="book-cover" style="background:${BG[i % BG.length]}">${b.cover || '📚'}</div>
          <div><div class="book-title">${b.title}</div><div class="book-author">${b.author}</div></div>
        </div></td>
        <td><span class="badge badge-accent">${b.genre || '—'}</span></td>
        <td>${b.due_date ? dueBadge(b.due_date) : '—'}</td>
        <td><div class="prog-wrap"><div class="prog-fill" style="width:${pct}%;background:${pCol}"></div></div></td>
        <td><div class="action-btns">
          <button class="btn btn-outline" style="font-size:.78rem;padding:.3rem .75rem"
            onclick="openRenew(${b.borrowing_id || b.id},'${b.title.replace(/'/g,"\\'")  }','${b.due_date||''}')">🔄 Renew</button>
        </div></td>
      </tr>`;
    }).join('');
  }

  /* ── Available Books table ── */
  /* copies_available comes from load_data.php (MySQL). Fall back to available for SQLite builds. */
  const avail = LMS.books
    .filter(b => (b.copies_available ?? b.available ?? 0) > 0 && !LMS.isBorrowedByUser(b.id))
    .slice(0, 6);
  document.getElementById('available-tbody').innerHTML = avail.length
    ? avail.map((b, i) => `<tr>
        <td><div class="td-book">
          <div class="book-cover" style="background:${BG[i % BG.length]}">${b.cover || '📚'}</div>
          <div><div class="book-title">${b.title}</div><div class="book-author">${b.author}</div></div>
        </div></td>
        <td><span class="badge badge-accent">${b.genre || '—'}</span></td>
        <td><a href="user-catalogue.php" class="btn btn-primary" style="font-size:.75rem;padding:.3rem .75rem">Borrow</a></td>
      </tr>`).join('')
    : `<tr><td colspan="3" style="text-align:center;padding:1.5rem;color:var(--text-muted)">No books available right now.</td></tr>`;

  /* ── Genre tags ── */
  const genres = ['All', ...new Set(LMS.books.map(b => b.genre).filter(Boolean))].slice(0, 10);
  document.getElementById('genre-tags').innerHTML = genres.map((g, i) =>
    `<span class="genre-tag${i===0?' active':''}" onclick="setGenre(this,'${g}')">${g}</span>`
  ).join('');

  /* ── Notification preview ── */
  const notifEl = document.getElementById('notif-preview');
  if(overdue.length){
    notifEl.innerHTML = overdue.map(b => `
      <div style="display:flex;gap:.75rem;align-items:flex-start;padding:.75rem;border-radius:10px;border:1px solid var(--danger-light);background:var(--danger-light);margin-bottom:.5rem;cursor:pointer" onclick="location.href='user-books.php'">
        <div style="width:30px;height:30px;border-radius:8px;display:grid;place-items:center;font-size:.9rem;background:rgba(255,255,255,.5);flex-shrink:0">⚠️</div>
        <div>
          <div style="font-size:.85rem;font-weight:600;color:var(--danger)">${b.title} is overdue</div>
          <div style="font-size:.72rem;color:var(--danger);opacity:.8;margin-top:.1rem">Fine: Rs.${Math.abs(daysUntil(b.due_date)) * 10}</div>
        </div>
      </div>`).join('');
  } else if(dueSoon.length){
    notifEl.innerHTML = dueSoon.map(b => `
      <div style="display:flex;gap:.75rem;align-items:flex-start;padding:.75rem;border-radius:10px;border:1px solid var(--warning-light);background:var(--warning-light);margin-bottom:.5rem;cursor:pointer" onclick="location.href='user-books.php'">
        <div style="width:30px;height:30px;border-radius:8px;display:grid;place-items:center;font-size:.9rem;background:rgba(255,255,255,.5);flex-shrink:0">⏰</div>
        <div>
          <div style="font-size:.85rem;font-weight:600;color:var(--warning)">${b.title}</div>
          <div style="font-size:.72rem;color:var(--warning);opacity:.8;margin-top:.1rem">${dueBadge(b.due_date)} — return soon</div>
        </div>
      </div>`).join('');
  } else {
    notifEl.innerHTML = `<p style="color:var(--text-muted);font-size:.875rem;padding:.25rem 0">✅ All caught up! No alerts right now.</p>`;
  }
}

function openRenew(bid, title, due){
  renewingId = bid;
  const newDue = new Date(due);
  newDue.setDate(newDue.getDate() + 14);
  document.getElementById('renew-body').innerHTML = `
    <p style="color:var(--text-muted);line-height:1.6">Renew <strong style="color:var(--text)">${title}</strong>?</p>
    <div style="background:var(--accent-light);border-radius:10px;padding:.85rem;margin-top:.75rem">
      <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.1rem">New due date</div>
      <div style="font-weight:700;color:var(--accent)">${newDue.toLocaleDateString('en-US',{month:'long',day:'numeric',year:'numeric'})}</div>
    </div>`;
  document.getElementById('renew-modal').classList.add('open');
}

function confirmRenew(){
  fetch('actions/renew_book.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({borrowing_id: renewingId})})
    .then(r => r.json())
    .then(d => { LMS.toast(d.success?'success':'error', d.success?'🔄':'⚠️', d.message||'Done'); if(d.success) LMS.syncData(); })
    .catch(() => LMS.toast('error','⚠️','Network error.'));
  document.getElementById('renew-modal').classList.remove('open');
}

function goSearch(){
  const q = document.getElementById('cat-search').value.trim();
  if(q) sessionStorage.setItem('lms-search', q);
  location.href = 'user-catalogue.php';
}
document.getElementById('cat-search').addEventListener('keydown', e => { if(e.key === 'Enter') goSearch(); });
document.getElementById('srch-wrap').addEventListener('focusin',  () => { document.getElementById('srch-wrap').style.borderColor = 'var(--accent)'; });
document.getElementById('srch-wrap').addEventListener('focusout', () => { document.getElementById('srch-wrap').style.borderColor = ''; });

function setGenre(el, genre){
  document.querySelectorAll('.genre-tag').forEach(t => t.classList.remove('active'));
  el.classList.add('active');
  sessionStorage.setItem('lms-genre', genre === 'All' ? '' : genre);
  location.href = 'user-catalogue.php';
}

document.getElementById('renew-modal').addEventListener('click', e => { if(e.target === e.currentTarget) e.currentTarget.classList.remove('open'); });
document.addEventListener('keydown', e => { if(e.key === 'Escape') document.getElementById('renew-modal').classList.remove('open'); });

document.addEventListener('lmsDataLoaded', renderDashboard);
</script>
</body>
</html>
