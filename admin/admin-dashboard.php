<?php
require_once('../includes/auth.php');
require_once('../actions/fine_calculation.php');
$pageTitle  = 'Admin Dashboard';
$activePage = 'dashboard';
$topbarRole = 'admin';
$footerSearchAction = "location.href='admin-books.php'";
$extraCss = <<<'CSS'
.mini-chart{display:flex;align-items:flex-end;gap:6px;height:80px;padding-top:.5rem}
    .bar-wrap{flex:1;display:flex;flex-direction:column;align-items:center;gap:4px}
    .bar{width:100%;border-radius:4px 4px 0 0;background:var(--accent-light);transition:height .6s cubic-bezier(.4,0,.2,1),background .2s;min-height:4px;cursor:pointer}
    .bar:hover{background:var(--accent)!important}
    .bar-label{font-size:.65rem;color:var(--text-light)}
    .activity-list{list-style:none;display:flex;flex-direction:column;gap:.85rem}
    .activity-item{display:flex;align-items:flex-start;gap:.9rem}
    .activity-dot{width:32px;height:32px;border-radius:9px;display:grid;place-items:center;font-size:.9rem;flex-shrink:0;margin-top:2px}
    .activity-text{font-size:.875rem;color:var(--text);line-height:1.4}
    .activity-text strong{font-weight:600}
    .activity-time{font-size:.75rem;color:var(--text-light);margin-top:.15rem}
    .member-list{list-style:none;display:flex;flex-direction:column;gap:.75rem}
    .member-item{display:flex;align-items:center;gap:.85rem;cursor:pointer;padding:.4rem .6rem;border-radius:10px;transition:background var(--transition)}
    .member-item:hover{background:var(--surface-2)}
    .member-avatar{width:36px;height:36px;border-radius:50%;display:grid;place-items:center;font-size:.8rem;font-weight:700;color:#fff;flex-shrink:0}
    .member-info{flex:1;min-width:0}
    .member-name{font-size:.875rem;font-weight:600;color:var(--text)}
    .member-meta{font-size:.75rem;color:var(--text-muted)}
    .member-tag{font-size:.78rem;font-weight:600;padding:.15rem .5rem;border-radius:6px;white-space:nowrap}
    .quick-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:1rem;margin-bottom:1.75rem;animation:fadeUp .5s .15s both}
    .quick-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.2rem;display:flex;flex-direction:column;align-items:center;gap:.55rem;cursor:pointer;text-align:center;transition:box-shadow var(--transition),transform var(--transition),border-color var(--transition)}
    .quick-card:hover{box-shadow:var(--shadow-md);transform:translateY(-3px);border-color:rgba(26,74,138,.2)}
    .quick-icon{font-size:1.6rem}
    .quick-label{font-size:.8rem;font-weight:600;color:var(--text)}
    .table-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;margin-bottom:1.75rem;animation:fadeUp .5s .3s both}
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
        <p class="page-eyebrow">Admin Panel</p>
        <h1 class="page-title">Dashboard Overview</h1>
        <p class="page-subtitle" id="dash-date">Saturday, 28 February 2026</p>
      </div>
      <div style="display:flex;gap:.75rem;flex-wrap:wrap">
        <button class="btn btn-outline" onclick="location.href='admin-reports.php'">📊 View Reports</button>
        <button class="btn btn-primary" onclick="location.href='admin-books.php'">➕ Add New Book</button>
      </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
      <div class="stat-card" style="cursor:pointer" onclick="location.href='admin-books.php'">
        <div class="stat-card-top"><span class="stat-label">Total Books</span><div class="stat-icon-wrap" style="background:var(--accent-light)">📚</div></div>
        <div class="stat-number" id="s-books">0</div>
        <div class="stat-change up" id="s-avail-tag">0 available</div>
        <div class="stat-bar" style="background:linear-gradient(90deg,var(--accent),#2563eb)"></div>
      </div>
      <div class="stat-card" style="cursor:pointer" onclick="location.href='admin-members.php'">
        <div class="stat-card-top"><span class="stat-label">Active Members</span><div class="stat-icon-wrap" style="background:var(--success-light)">👥</div></div>
        <div class="stat-number" id="s-members">0</div>
        <div class="stat-change up">↑ 8 new this week</div>
        <div class="stat-bar" style="background:linear-gradient(90deg,var(--success),#27ae60)"></div>
      </div>
      <div class="stat-card" style="cursor:pointer" onclick="location.href='admin-borrowings.php'">
        <div class="stat-card-top"><span class="stat-label">Active Borrowings</span><div class="stat-icon-wrap" style="background:var(--accent-2-light)">🔄</div></div>
        <div class="stat-number" id="s-borrow">0</div>
        <div class="stat-change neutral">→ Steady pace</div>
        <div class="stat-bar" style="background:linear-gradient(90deg,var(--accent-2),#f39c12)"></div>
      </div>
      <div class="stat-card" style="cursor:pointer" onclick="location.href='admin-overdue.php'">
        <div class="stat-card-top"><span class="stat-label">Overdue Returns</span><div class="stat-icon-wrap" style="background:var(--danger-light)">⚠️</div></div>
        <div class="stat-number" id="s-overdue">0</div>
        <div class="stat-change down">↓ Was 9 last week</div>
        <div class="stat-bar" style="background:linear-gradient(90deg,var(--danger),#e74c3c)"></div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-grid">
      <div class="quick-card" onclick="openQuickAdd()"><span class="quick-icon">📗</span><span class="quick-label">Add Book</span></div>
      <div class="quick-card" onclick="location.href='admin-members.php'"><span class="quick-icon">👤</span><span class="quick-label">Add Member</span></div>
      <div class="quick-card" onclick="openQuickIssue()"><span class="quick-icon">📋</span><span class="quick-label">Issue Book</span></div>
      <div class="quick-card" onclick="location.href='admin-borrowings.php'"><span class="quick-icon">↩️</span><span class="quick-label">Process Return</span></div>
      <div class="quick-card" onclick="location.href='admin-reports.php'"><span class="quick-icon">📊</span><span class="quick-label">View Reports</span></div>
      <div class="quick-card" onclick="location.href='admin-notifications.php'"><span class="quick-icon">📬</span><span class="quick-label">Send Notice</span></div>
    </div>

    <!-- Chart + Activity -->
    <div class="two-col">
      <div class="card">
        <div class="card-header">
          <span class="card-title">Borrowings This Week</span>
          <span class="card-action" onclick="location.href='admin-borrowings.php'">View All →</span>
        </div>
        <div class="card-body">
          <div style="display:flex;justify-content:space-between;margin-bottom:.5rem">
            <span style="font-size:.78rem;color:var(--text-muted)">Books borrowed per day</span>
            <span style="font-size:.78rem;font-weight:700;color:var(--accent)">Total: 87</span>
          </div>
          <div class="mini-chart" id="borrow-chart"></div>
        </div>
      </div>
      <div class="card">
        <div class="card-header">
          <span class="card-title">Recent Activity</span>
          <span class="card-action" onclick="location.href='admin-borrowings.php'">View All →</span>
        </div>
        <div class="card-body">
          <ul class="activity-list" id="activity-feed"></ul>
        </div>
      </div>
    </div>

    <!-- Recent Books Table -->
    <div class="table-card">
      <div class="table-toolbar">
        <span class="table-title">📚 Recent Books</span>
        <span class="card-action" style="margin-left:auto" onclick="location.href='admin-books.php'">Manage All →</span>
      </div>
      <div style="overflow-x:auto">
        <table>
          <thead><tr><th>Book</th><th>Genre</th><th>Status</th><th>Due Date</th><th>Action</th></tr></thead>
          <tbody id="dash-books-tbody"></tbody>
        </table>
      </div>
    </div>

    <!-- Members + Overdue -->
    <div class="two-col">
      <div class="card">
        <div class="card-header">
          <span class="card-title">Recent Members</span>
          <span class="card-action" onclick="location.href='admin-members.php'">Manage →</span>
        </div>
        <div class="card-body">
          <ul class="member-list" id="member-list"></ul>
        </div>
      </div>
      <div class="card">
        <div class="card-header">
          <span class="card-title">⚠️ Overdue Alerts</span>
          <span class="card-action" onclick="location.href='admin-overdue.php'">View All →</span>
        </div>
        <div class="card-body">
          <ul class="activity-list" id="overdue-list"></ul>
        </div>
      </div>
    </div>

  </main>

<!-- QUICK ADD BOOK MODAL -->
<div class="modal-backdrop" id="quick-add-modal">
  <div class="modal" style="max-width:480px">
    <div class="modal-header">
      <span class="modal-title">➕ Quick Add Book</span>
      <button class="modal-close" onclick="document.getElementById('quick-add-modal').classList.remove('open')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-row">
        <div class="form-group"><label>Title <span style="color:var(--danger)">*</span></label><input class="form-control" id="qa-title" placeholder="Book title" /></div>
        <div class="form-group"><label>Author <span style="color:var(--danger)">*</span></label><input class="form-control" id="qa-author" placeholder="Author name" /></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Genre <span style="color:var(--danger)">*</span></label>
          <select class="form-control" id="qa-genre">
            <option value="">Select genre…</option>
            <option>Fiction</option><option>Non-Fiction</option><option>Science</option>
            <option>History</option><option>Biography</option><option>Technology</option>
            <option>Philosophy</option><option>Self-Help</option><option>Fantasy</option>
          </select>
        </div>
        <div class="form-group"><label>Cover Emoji</label><input class="form-control" id="qa-cover" value="📘" maxlength="2" style="font-size:1.5rem;text-align:center" /></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>ISBN</label><input class="form-control" id="qa-isbn" placeholder="978-…" /></div>
        <div class="form-group"><label>Copies</label><input type="number" class="form-control" id="qa-copies" value="1" min="1" max="99" /></div>
      </div>
      <div id="qa-error" class="form-error" style="display:none"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="document.getElementById('quick-add-modal').classList.remove('open')">Cancel</button>
      <button class="btn btn-primary" onclick="quickAddBook()">➕ Add to Catalogue</button>
    </div>
  </div>
</div>

<!-- QUICK ISSUE BOOK MODAL -->
<div class="modal-backdrop" id="quick-issue-modal">
  <div class="modal" style="max-width:460px">
    <div class="modal-header">
      <span class="modal-title">📋 Quick Issue Book</span>
      <button class="modal-close" onclick="document.getElementById('quick-issue-modal').classList.remove('open')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group"><label>Member</label>
        <select class="form-control" id="qi-member"></select>
      </div>
      <div class="form-group"><label>Book</label>
        <select class="form-control" id="qi-book"></select>
      </div>
      <div class="form-group"><label>Due Date</label>
        <input type="date" class="form-control" id="qi-due" />
      </div>
      <div id="qi-error" class="form-error" style="display:none"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="document.getElementById('quick-issue-modal').classList.remove('open')">Cancel</button>
      <button class="btn btn-primary" onclick="quickIssueBook()">📋 Issue Book</button>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
<script>
/* ── Sidebar + Dark ── */
  const sb=document.getElementById('sidebar'),ov=document.getElementById('overlay');
  document.getElementById('menu-btn').addEventListener('click',()=>{sb.classList.toggle('open');ov.classList.toggle('show');});
  ov.addEventListener('click',()=>{sb.classList.remove('open');ov.classList.remove('show');});

  /* ── Top search ── */
  document.getElementById('top-search').addEventListener('keydown',e=>{if(e.key==='Enter') location.href='admin-books.php';});

  /* ── Animated counter ── */
  function animCount(el, target) {
    const dur=1200, start=performance.now();
    const run=now=>{
      const p=Math.min((now-start)/dur,1), ease=1-Math.pow(1-p,3);
      el.textContent=Math.floor(ease*target).toLocaleString();
      if(p<1) requestAnimationFrame(run); else el.textContent=target.toLocaleString();
    };
    requestAnimationFrame(run);
  }

  /* ── Stats ── */
  function populateStats() {
    const books=LMS.books, members=LMS.members;
    const active=LMS.borrowings.filter(b=>!b.returned_date), overdue=LMS.getOverdue();
    animCount(document.getElementById('s-books'),   books.length);
    animCount(document.getElementById('s-members'), members.length);
    animCount(document.getElementById('s-borrow'),  active.length);
    animCount(document.getElementById('s-overdue'), overdue.length);
    document.getElementById('s-avail-tag').textContent = `↑ ${books.filter(b=>b.copies_available > 0).length} available`;
    document.getElementById('dash-date').textContent  = new Date().toLocaleDateString('en-US',{weekday:'long',year:'numeric',month:'long',day:'numeric'});
  }

  /* ── Books table ── */
  function populateBooksTable() {
    document.getElementById('dash-books-tbody').innerHTML = LMS.books.slice(0,6).map(b=>{
      const borrowingsForBook = LMS.borrowings.filter(bw => bw.book_id == b.id && !bw.returned_date);
      const isOverdue = borrowingsForBook.some(bw => new Date(bw.due_date) < new Date());
      const status = isOverdue ? 'overdue' : (b.copies_available > 0 ? 'available' : 'borrowed');
      const statusBadge = status === 'available' ? `<span class="badge badge-success">✅ Available</span>`
        : status === 'borrowed' ? `<span class="badge badge-warning">📖 Borrowed</span>`
        : `<span class="badge badge-danger">⚠️ Overdue</span>`;
      const due = borrowingsForBook.length ? new Date(Math.min(...borrowingsForBook.map(bw => new Date(bw.due_date)))).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'}) : '—';
      return `<tr>
        <td><div style="display:flex;align-items:center;gap:.75rem"><div class="book-cover">${b.cover}</div><div><div class="book-title">${b.title}</div><div class="book-author">${b.author}</div></div></div></td>
        <td><span class="badge badge-accent">${b.genre}</span></td>
        <td>${statusBadge}</td>
        <td style="${status==='overdue'?'color:var(--danger);font-weight:600':'color:var(--text-muted)'}">${due}</td>
        <td><button class="action-btn" onclick="location.href='admin-books.php'" title="Edit">✏️</button></td>
      </tr>`;
    }).join('');
  }

  /* ── Members ── */
  const MEM_COLORS = ['#1a4a8a,#2563eb','#1a7a4a,#27ae60','#c0392b,#e74c3c','#6b21a8,#9333ea','#0369a1,#0ea5e9'];
  function populateMembers() {
    document.getElementById('member-list').innerHTML = LMS.members.slice(0,5).map((m,i)=>{
      const borrowedCount = LMS.borrowings.filter(b => b.member_id == m.id && !b.returned_date).length;
      const isOv = LMS.getOverdue().some(b => b.member_id == m.id);
      return `<li class="member-item" onclick="location.href='admin-members.php'">
        <div class="member-avatar" style="background:linear-gradient(135deg,${MEM_COLORS[i%MEM_COLORS.length]})">${m.initials}</div>
        <div class="member-info"><div class="member-name">${m.name}</div><div class="member-meta">${m.role} · Joined ${m.joined}</div></div>
        <span class="member-tag" style="${isOv?'color:var(--danger);background:var(--danger-light)':'color:var(--accent);background:var(--accent-light)'}">${isOv?'OVERDUE':borrowedCount+' book'+(borrowedCount!==1?'s':'')}</span>
      </li>`;
    }).join('');
  }

  /* ── Overdue ── */
  function populateOverdue() {
    const overdues = LMS.getOverdue();
    if (!overdues.length) {
      document.getElementById('overdue-list').innerHTML = '<li style="color:var(--success);font-size:.875rem;padding:1rem;text-align:center">✅ No overdue borrowings!</li>';
      return;
    }
    document.getElementById('overdue-list').innerHTML = overdues.slice(0,5).map(bw=>{
      const m=LMS.getMember(bw.memberId), bk=LMS.getBook(bw.bookId);
      const days=Math.max(0,Math.floor((new Date()-new Date(bw.due))/86400000));
      const color=days>5?'var(--danger-light)':'var(--warning-light)', tc=days>5?'var(--danger)':'var(--warning)';
      return `<li class="activity-item" style="cursor:pointer" onclick="location.href='admin-overdue.php'">
        <div class="activity-dot" style="background:${color}">⏰</div>
        <div><p class="activity-text"><strong>${m?.name||'—'}</strong> — <em>${bk?.title||'—'}</em></p>
        <p class="activity-time" style="color:${tc};font-weight:600">${days} day${days!==1?'s':''} overdue</p></div>
      </li>`;
    }).join('');
  }

  /* ── Activity feed ── */
  function populateActivity() {
    const activities = [];
    // Recent borrowings
    LMS.borrowings.slice(-5).reverse().forEach(b => {
      const member = LMS.getMember(b.member_id);
      const book = LMS.getBook(b.book_id);
      if (member && book) {
        activities.push({
          dot: '📗',
          bg: 'var(--success-light)',
          text: `<strong>${member.name}</strong> borrowed <strong>${book.title}</strong>`,
          time: timeAgo(new Date(b.issue_date))
        });
      }
    });
    // If less than 5, add some static
    while (activities.length < 5) {
      activities.push({
        dot: '📗',
        bg: 'var(--success-light)',
        text: 'New book added to catalogue',
        time: 'Recently'
      });
    }
    document.getElementById('activity-feed').innerHTML = activities.slice(0,5).map(f => `
      <li class="activity-item">
        <div class="activity-dot" style="background:${f.bg}">${f.dot}</div>
        <div><p class="activity-text">${f.text}</p><p class="activity-time">${f.time}</p></div>
      </li>`).join('');
  }

  function timeAgo(date) {
    const now = new Date();
    const diff = now - date;
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);
    if (minutes < 60) return `${minutes} min ago`;
    if (hours < 24) return `${hours} hour${hours > 1 ? 's' : ''} ago`;
    return `${days} day${days > 1 ? 's' : ''} ago`;
  }

  /* ── Chart ── */
  function buildChart() {
    const now = new Date();
    const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    const data = [];
    for (let i = 6; i >= 0; i--) {
      const date = new Date(now);
      date.setDate(now.getDate() - i);
      const dayName = days[date.getDay()];
      const count = LMS.borrowings.filter(b => {
        const issueDate = new Date(b.issue_date);
        return issueDate.toDateString() === date.toDateString();
      }).length;
      data.push({ day: dayName, val: count });
    }
    const maxVal = Math.max(...data.map(d => d.val)) || 1;
    const el = document.getElementById('borrow-chart');
    el.innerHTML = '';
    data.forEach(d => {
      const wrap = document.createElement('div'); wrap.className = 'bar-wrap';
      const bar = document.createElement('div'); bar.className = 'bar';
      bar.style.height = '0px'; bar.title = `${d.day}: ${d.val} books`;
      const lbl = document.createElement('div'); lbl.className = 'bar-label'; lbl.textContent = d.day;
      wrap.appendChild(bar); wrap.appendChild(lbl); el.appendChild(wrap);
      setTimeout(() => {
        bar.style.height = `${(d.val / maxVal) * 100}%`;
        bar.style.background = d.val === maxVal && maxVal > 0 ? 'var(--accent)' : 'var(--accent-light)';
      }, 350);
    });
  }

  /* ── Quick Add Book Modal ── */
  function openQuickAdd() {
    ['qa-title','qa-author','qa-isbn'].forEach(id=>document.getElementById(id).value='');
    document.getElementById('qa-genre').value='';
    document.getElementById('qa-cover').value='📘';
    document.getElementById('qa-copies').value=1;
    document.getElementById('qa-error').style.display='none';
    document.getElementById('quick-add-modal').classList.add('open');
  }
  function quickAddBook() {
    const title  = document.getElementById('qa-title').value.trim();
    const author = document.getElementById('qa-author').value.trim();
    const genre  = document.getElementById('qa-genre').value;
    const errEl  = document.getElementById('qa-error');
    if(!title||!author||!genre){errEl.textContent='Please fill in Title, Author, and Genre.';errEl.style.display='block';return;}
    LMS.books.push({id:Date.now(),title,author,genre,isbn:document.getElementById('qa-isbn').value,cover:document.getElementById('qa-cover').value,copies:parseInt(document.getElementById('qa-copies').value)||1,status:'available',borrowedBy:null,dueDate:null});
    document.getElementById('quick-add-modal').classList.remove('open');
    populateStats(); populateBooksTable();
    LMS.toast('success','➕',`"${title}" added to catalogue!`);
  }

  /* ── Quick Issue Book Modal ── */
  function openQuickIssue() {
    const mSel=document.getElementById('qi-member');
    mSel.innerHTML='<option value="">Select member…</option>'+LMS.members.map(m=>`<option value="${m.id}">${m.name} (${m.role})</option>`).join('');
    const bSel=document.getElementById('qi-book');
    bSel.innerHTML='<option value="">Select available book…</option>'+LMS.books.filter(b=>b.status==='available').map(b=>`<option value="${b.id}">${b.cover} ${b.title}</option>`).join('');
    const due=new Date(); due.setDate(due.getDate()+14);
    document.getElementById('qi-due').value=due.toISOString().split('T')[0];
    document.getElementById('qi-error').style.display='none';
    document.getElementById('quick-issue-modal').classList.add('open');
  }
  function quickIssueBook() {
    const memberId=parseInt(document.getElementById('qi-member').value);
    const bookId=parseInt(document.getElementById('qi-book').value);
    const due=document.getElementById('qi-due').value;
    const errEl=document.getElementById('qi-error');
    if(!memberId||!bookId||!due){errEl.textContent='Please fill in all fields.';errEl.style.display='block';return;}
    const book=LMS.books.find(b=>b.id===bookId);
    book.status='borrowed'; book.borrowedBy=memberId; book.dueDate=due;
    const member=LMS.members.find(m=>m.id===memberId);
    member.borrowedBooks.push(bookId);
    LMS.borrowings.push({id:Date.now(),memberId,bookId,issued:new Date().toISOString().split('T')[0],due,returned:null,fine:0});
    document.getElementById('quick-issue-modal').classList.remove('open');
    populateStats(); populateBooksTable();
    LMS.toast('success','📋',`Book issued to ${member.name}!`);
  }

  /* ── Modal backdrop/ESC ── */
  ['quick-add-modal','quick-issue-modal'].forEach(id=>{
    document.getElementById(id).addEventListener('click',e=>{if(e.target===e.currentTarget)e.currentTarget.classList.remove('open');});
  });
  document.addEventListener('keydown',e=>{
    if(e.key==='Escape')['quick-add-modal','quick-issue-modal'].forEach(id=>document.getElementById(id).classList.remove('open'));
  });

</script>
<script>
  document.addEventListener('lmsDataLoaded', () => {
    populateStats();
    populateBooksTable();
    populateMembers();
    populateOverdue();
    populateActivity();
    buildChart();
  });
</script>
</body>
</html>
