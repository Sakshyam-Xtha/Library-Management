<?php
require_once('../includes/auth.php');
$pageTitle  = 'Books';
$activePage = 'books';
$topbarRole = 'admin';
$footerSearchAction = "location.href='admin-books.php'";
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
        <p class="page-eyebrow">Admin / Catalogue</p>
        <h1 class="page-title">Books</h1>
        <p class="page-subtitle" id="book-count-label">Loading…</p>
      </div>
      <div style="display:flex;gap:.75rem;flex-wrap:wrap">
        <button class="btn btn-outline" onclick="exportCSV()">📥 Export CSV</button>
        <button class="btn btn-primary" onclick="openAddModal()">➕ Add New Book</button>
      </div>
    </div>

    <!-- Stats row -->
    <div class="stats-grid" style="grid-template-columns:repeat(4,1fr)">
      <div class="stat-card" style="animation-delay:.05s">
        <div class="stat-card-top"><span class="stat-label">Total Books</span><div class="stat-icon-wrap" style="background:var(--accent-light)">📚</div></div>
        <div class="stat-number" id="s-total">0</div>
        <div class="stat-bar" style="background:linear-gradient(90deg,var(--accent),#2563eb)"></div>
      </div>
      <div class="stat-card" style="animation-delay:.1s">
        <div class="stat-card-top"><span class="stat-label">Available</span><div class="stat-icon-wrap" style="background:var(--success-light)">✅</div></div>
        <div class="stat-number" id="s-avail">0</div>
        <div class="stat-bar" style="background:linear-gradient(90deg,var(--success),#27ae60)"></div>
      </div>
      <div class="stat-card" style="animation-delay:.15s">
        <div class="stat-card-top"><span class="stat-label">Borrowed</span><div class="stat-icon-wrap" style="background:var(--accent-2-light)">🔄</div></div>
        <div class="stat-number" id="s-borrow">0</div>
        <div class="stat-bar" style="background:linear-gradient(90deg,var(--accent-2),#f39c12)"></div>
      </div>
      <div class="stat-card" style="animation-delay:.2s">
        <div class="stat-card-top"><span class="stat-label">Overdue</span><div class="stat-icon-wrap" style="background:var(--danger-light)">⚠️</div></div>
        <div class="stat-number" id="s-overdue">0</div>
        <div class="stat-bar" style="background:linear-gradient(90deg,var(--danger),#e74c3c)"></div>
      </div>
    </div>

    <!-- Table -->
    <div class="table-card">
      <div class="table-toolbar">
        <span class="table-title">📚 Book Catalogue</span>
        <div class="table-filters" id="filter-btns">
          <button class="filter-btn active" data-filter="all">All</button>
          <button class="filter-btn" data-filter="available">Available</button>
          <button class="filter-btn" data-filter="borrowed">Borrowed</button>
          <button class="filter-btn" data-filter="overdue">Overdue</button>
        </div>
      </div>
      <div style="overflow-x:auto">
        <table>
          <thead>
            <tr>
              <th>Book</th><th>Genre</th><th>ISBN</th><th>Copies</th><th>Status</th><th>Due Date</th><th>Actions</th>
            </tr>
          </thead>
          <tbody id="books-tbody"></tbody>
        </table>
      </div>
      <div id="empty-books" class="empty-state" style="display:none">
        <div class="empty-state-icon">📭</div>
        <h3>No books found</h3>
        <p>Try adjusting your search or filter.</p>
      </div>
    </div>
  </main>

<!-- ADD/EDIT MODAL -->
<div class="modal-backdrop" id="book-modal">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title" id="modal-title">Add New Book</span>
      <button class="modal-close" onclick="closeModal()">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-row">
        <div class="form-group"><label>Title *</label><input class="form-control" id="f-title" placeholder="Book title" /></div>
        <div class="form-group"><label>Author *</label><input class="form-control" id="f-author" placeholder="Author name" /></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Genre *</label>
          <select class="form-control" id="f-genre">
            <option value="">Select genre</option>
            <option>Fiction</option><option>Non-Fiction</option><option>Self-Help</option>
            <option>Productivity</option><option>History</option><option>Science</option>
            <option>Technology</option><option>Fantasy</option><option>Sci-Fi</option>
            <option>Dystopia</option><option>Biography</option><option>Business</option><option>Psychology</option>
          </select>
        </div>
        <div class="form-group"><label>Cover Emoji</label>
          <select class="form-control" id="f-cover">
            <option value="📘">📘 Blue Book</option><option value="📗">📗 Green Book</option>
            <option value="📕">📕 Red Book</option><option value="📙">📙 Orange Book</option>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>ISBN</label><input class="form-control" id="f-isbn" placeholder="978-XXXXXXXXXX" /></div>
        <div class="form-group"><label>Copies</label><input class="form-control" type="number" id="f-copies" min="1" value="1" /></div>
      </div>
      <div id="f-error" style="color:var(--danger);font-size:.85rem;margin-top:.5rem;display:none"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal()">Cancel</button>
      <button class="btn btn-primary" onclick="saveBook()">💾 Save Book</button>
    </div>
  </div>
</div>


<!-- DELETE CONFIRM -->
<div class="modal-backdrop" id="delete-modal">
  <div class="modal" style="max-width:380px">
    <div class="modal-header">
      <span class="modal-title">Delete Book</span>
      <button class="modal-close" onclick="closeDeleteModal()">✕</button>
    </div>
    <div class="modal-body">
      <p style="color:var(--text-muted);line-height:1.6">Are you sure you want to delete <strong id="delete-book-name"></strong>? This action cannot be undone.</p>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeDeleteModal()">Cancel</button>
      <button class="btn" style="background:var(--danger);color:#fff" onclick="confirmDelete()">🗑 Delete</button>
    </div>
  </div>
</div>


<!-- VIEW MODAL -->
<div class="modal-backdrop" id="view-modal">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">Book Details</span>
      <button class="modal-close" onclick="closeViewModal()">✕</button>
    </div>
    <div class="modal-body" id="view-modal-body"></div>
    <div class="modal-footer"><button class="btn btn-outline" onclick="closeViewModal()">Close</button></div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
<script>
/* ─ State ─ */
  let filter = 'all';
  let searchQ = '';
  let editingId = null;
  let deletingId = null;
  let books = []; // Initialized as empty, filled by the event below

  // 1. DATA SYNC: This wakes up the page once load_data.php finishes
  document.addEventListener('lmsDataLoaded', () => {
    books = [...LMS.books];
    render();
  });

  /* ─ Sidebar / Dark ─ */
  const sb = document.getElementById('sidebar'), ov = document.getElementById('overlay');
  const menuBtn = document.getElementById('menu-btn');
  if (menuBtn) {
    menuBtn.addEventListener('click', () => { sb.classList.toggle('open'); ov.classList.toggle('show'); });
  }
  if (ov) {
    ov.addEventListener('click', () => { sb.classList.remove('open'); ov.classList.remove('show'); });
  }

  /* ─ Search ─ */
  const searchInput = document.getElementById('search-input');
  if (searchInput) {
    searchInput.addEventListener('input', e => { searchQ = e.target.value.toLowerCase(); render(); });
  }

  /* ─ Filters ─ */
  document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      filter = btn.dataset.filter;
      render();
    });
  });

  /* ─ Render (Kept All Logic) ─ */
  function render() {
    const tbody = document.getElementById('books-tbody');
    if (!tbody) return;

    let data = books.filter(b => {
      // Map 'category' from DB to 'genre' for your logic if needed
      const genre = b.genre || b.category || 'General';
      const status = b.status || (parseInt(b.copies_available) > 0 ? 'available' : 'overdue');
      
      if (filter !== 'all' && status !== filter) return false;
      if (searchQ && !(`${b.title} ${b.author} ${b.isbn} ${genre}`).toLowerCase().includes(searchQ)) return false;
      return true;
    });

    const emptyMsg = document.getElementById('empty-books');
    if (emptyMsg) emptyMsg.style.display = data.length ? 'none' : 'block';
    
    document.getElementById('book-count-label').textContent = `${books.length} books in catalogue`;

    /* Stats Update */
    const sTotal = document.getElementById('s-total');
    if (sTotal) {
        sTotal.textContent = books.length;
        document.getElementById('s-avail').textContent = books.filter(b => parseInt(b.copies_available) > 0).length;
        const totalBorrowed = books.reduce((sum, b) => sum + (parseInt(b.copies || b.quantity) - parseInt(b.copies_available)), 0);
        document.getElementById('s-borrow').textContent = totalBorrowed;
        document.getElementById('s-overdue').textContent = books.filter(b => b.status === 'overdue').length;
    }

    const hl = t => {
      if (!t) return '—';
      return searchQ ? t.toString().replace(new RegExp(searchQ.replace(/[.*+?^${}()|[\]\\]/g,'\\$&'),'gi'), m=>`<mark>${m}</mark>`) : t;
    };

    tbody.innerHTML = data.map(b => {
      const isAvail = parseInt(b.copies_available) > 0;
      const statusBadge = isAvail ? `<span class="badge badge-success">✅ Available</span>` : `<span class="badge badge-danger">⚠️ Out of Stock</span>`;
      const genre = b.genre || b.category || 'General';
      
      return `<tr>
        <td><div class="td-book"><div class="book-cover">${b.cover || '📘'}</div><div><div class="book-title">${hl(b.title)}</div><div class="book-author">${hl(b.author)}</div></div></div></td>
        <td><span class="badge badge-accent">${genre}</span></td>
        <td style="color:var(--text-muted);font-family:monospace;font-size:.8rem">${hl(b.isbn)}</td>
        <td style="color:var(--text-muted)">${b.copies_available} / ${b.copies || b.quantity}</td>
        <td>${statusBadge}</td>
        <td style="color:var(--text-muted)">${b.dueDate || '—'}</td>
        <td><div class="action-btns">
          <button class="action-btn" onclick="viewBook(${b.id})" title="View">👁</button>
          <button class="action-btn" onclick="openEditModal(${b.id})" title="Edit">✏️</button>
          <button class="action-btn del" onclick="openDeleteModal(${b.id})" title="Delete">🗑</button>
        </div></td>
      </tr>`;
    }).join('');
  }

  /* ─ View ─ */
  function viewBook(id) {
    const b = books.find(x => Number(x.id) === Number(id));
    if (!b) return;
    document.getElementById('view-modal-body').innerHTML = `
      <div style="text-align:center;font-size:4rem;margin-bottom:1rem">${b.cover || '📘'}</div>
      <h2 style="font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:900;text-align:center;margin-bottom:.25rem">${b.title}</h2>
      <p style="text-align:center;color:var(--text-muted);margin-bottom:1.5rem">${b.author}</p>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
        ${[['Genre', b.genre || b.category], ['ISBN', b.isbn || '—'], ['Available', b.copies_available], ['Total', b.copies || b.quantity]].map(([k,v])=>`
          <div style="background:var(--surface-2);border-radius:10px;padding:.75rem">
            <div style="font-size:.72rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:.2rem">${k}</div>
            <div style="font-weight:600;color:var(--text)">${v}</div>
          </div>`).join('')}
      </div>`;
    document.getElementById('view-modal').classList.add('open');
  }
  function closeViewModal() { document.getElementById('view-modal').classList.remove('open'); }

  /* ─ Add/Edit Modal (Kept unchanged) ─ */
  function openAddModal() {
    editingId = null;
    document.getElementById('modal-title').textContent = 'Add New Book';
    ['title','author','isbn','genre','cover','copies'].forEach(id => {
        const el = document.getElementById('f-'+id);
        if(el) el.value = id === 'copies' ? 1 : (id === 'cover' ? '📘' : '');
    });
    document.getElementById('f-error').style.display = 'none';
    document.getElementById('book-modal').classList.add('open');
  }

  function openEditModal(id) {
    editingId = id;
    const b = books.find(x => Number(x.id) === Number(id));
    document.getElementById('modal-title').textContent = 'Edit Book';
    document.getElementById('f-title').value  = b.title;
    document.getElementById('f-author').value = b.author;
    document.getElementById('f-genre').value  = b.genre || b.category;
    document.getElementById('f-isbn').value   = b.isbn || '';
    document.getElementById('f-cover').value  = b.cover || '📘';
    document.getElementById('f-copies').value = b.copies || b.quantity;
    document.getElementById('f-error').style.display = 'none';
    document.getElementById('book-modal').classList.add('open');
  }

  function closeModal() { document.getElementById('book-modal').classList.remove('open'); }

  /* ─ Save Book (Added sync to LMS state) ─ */
  function saveBook() {
    const title = document.getElementById('f-title').value.trim();
    const author = document.getElementById('f-author').value.trim();
    const genre = document.getElementById('f-genre').value;
    const isbn = document.getElementById('f-isbn').value.trim();
    const cover = document.getElementById('f-cover').value;
    const copies = parseInt(document.getElementById('f-copies').value) || 1;
    
    if (!title || !author || !genre) {
      const errEl = document.getElementById('f-error');
      errEl.textContent = 'Please fill in Title, Author and Genre.';
      errEl.style.display = 'block'; return;
    }

    // In a real app, you would fetch() to a PHP script here.
    if (editingId) {
      const b = books.find(x => Number(x.id) === Number(editingId));
      Object.assign(b, {title, author, genre, isbn, cover, copies});
      toast('success','📗','Book updated successfully.');
    } else {
      let form_data = { title, author, genre, isbn, cover, copies_available: copies, copies: copies };
      fetch ('../actions/add_books.php',{
        method: "POST",
        header:{"Content-Type":"application/json"},
        body: JSON.stringify(form_data)
      }).then(r=> r.json())
      .then(data=>{
        if (data.success === true){
        toast('success','➕','Book added to catalogue.');
        }else{
          toast('failed','➕','Book was not added to catalogue.');
        }
      })
      
    }
    
    LMS.books = books; // Sync back to global engine
    closeModal(); render();
  }

  /* ─ Delete (Kept unchanged) ─ */
  function openDeleteModal(id) {
    deletingId = id;
    const b = books.find(x => Number(x.id) === Number(id));
    document.getElementById('delete-book-name').textContent = `"${b.title}"`;
    document.getElementById('delete-modal').classList.add('open');
  }
  function closeDeleteModal() { document.getElementById('delete-modal').classList.remove('open'); }
  
  function confirmDelete() {
    books = books.filter(x => Number(x.id) !== Number(deletingId));
    LMS.books = books;
    closeDeleteModal(); render();
    toast('warning','🗑','Book removed from catalogue.');
  }

  /* ─ Export CSV (Kept unchanged) ─ */
  function exportCSV() {
    const rows = [['Title','Author','Genre','ISBN','Status','Copies'],...books.map(b=>[b.title,b.author,b.genre||b.category,b.isbn||'',b.status,b.copies])];
    const csv = rows.map(r=>r.join(',')).join('\n');
    const a = document.createElement('a');
    a.href = 'data:text/csv;charset=utf-8,'+encodeURIComponent(csv);
    a.download = 'lms-books.csv'; a.click();
    toast('info','📥','CSV exported!');
  }

  /* ─ Toast (Standardized to use container) ─ */
  function toast(type, icon, msg) {
    let container = document.getElementById('toast-container');
    if(!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }
    const el = document.createElement('div');
    el.className = `toast ${type}`;
    el.innerHTML = `<span class="toast-icon">${icon}</span><span class="toast-msg">${msg}</span>`;
    container.appendChild(el);
    setTimeout(() => { el.classList.add('removing'); setTimeout(()=>el.remove(),300); }, 3200);
  }

  // Final Event Listeners for closing modals
  ['book-modal','delete-modal','view-modal'].forEach(id => {
    const el = document.getElementById(id);
    if(el) el.addEventListener('click', e => { if(e.target===e.currentTarget) e.currentTarget.classList.remove('open'); });
  });

  document.addEventListener('keydown', e => {
    if(e.key==='Escape') ['book-modal','delete-modal','view-modal'].forEach(id => {
        const el = document.getElementById(id);
        if(el) el.classList.remove('open');
    });
  });
</script>
</body>
</html>
