<?php
require_once('../includes/auth.php');
$pageTitle  = 'Browse Catalogue';
$activePage = 'catalogue';
$topbarRole = 'user';
$footerSearchAction = "location.href='user-catalogue.php'";
$extraCss = <<<'CSS'
.genre-pill{padding:.3rem .85rem;border-radius:99px;font-size:.78rem;font-weight:600;border:1.5px solid var(--border);color:var(--text-muted);background:var(--surface);cursor:pointer;transition:all var(--transition);white-space:nowrap}
.genre-pill.active,.genre-pill:hover{border-color:var(--accent);color:var(--accent);background:var(--accent-light)}
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
    <div><p class="page-eyebrow">Discover</p><h1 class="page-title">Browse Catalogue</h1><p class="page-subtitle" id="result-count">Loading…</p></div>
  </div>

  <!-- Search bar -->
  <div style="display:flex;gap:.75rem;margin-bottom:1.25rem">
    <div style="flex:1;display:flex;align-items:center;gap:.75rem;background:var(--surface);border:1.5px solid var(--border);border-radius:12px;padding:.75rem 1.2rem;transition:border-color var(--transition),box-shadow var(--transition)" id="srch-big">
      <span style="font-size:1.1rem;color:var(--text-muted)">🔍</span>
      <input type="text" id="main-search" placeholder="Search by title, author, ISBN, or genre…" style="flex:1;background:none;border:none;outline:none;font-size:.95rem;color:var(--text)" />
    </div>
    <button class="btn btn-primary" style="padding:.75rem 1.5rem" onclick="render()">Search</button>
  </div>

  <!-- Genre pills -->
  <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1.25rem" id="genre-pills"></div>

  <!-- Availability filter -->
  <div style="display:flex;gap:.5rem;margin-bottom:1.5rem">
    <button class="filter-btn active" data-avail="all">All</button>
    <button class="filter-btn" data-avail="available">✅ Available Now</button>
    <button class="filter-btn" data-avail="borrowed">📖 Borrowed</button>
  </div>

  <div class="table-card">
    <div class="table-toolbar"><span class="table-title">📚 All Books</span></div>
    <div style="overflow-x:auto">
      <table>
        <thead><tr><th>Book</th><th>Genre</th><th>ISBN</th><th>Copies</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody id="cat-tbody"><tr><td colspan="6" style="text-align:center;padding:2.5rem;color:var(--text-muted)">Loading…</td></tr></tbody>
      </table>
    </div>
    <div id="empty-cat" class="empty-state" style="display:none">
      <div class="empty-state-icon">🔍</div><h3>No books found</h3><p>Try different search terms or filters.</p>
    </div>
  </div>

</main>

<!-- DETAIL MODAL -->
<div class="modal-backdrop" id="detail-modal">
  <div class="modal" style="max-width:460px">
    <div class="modal-header"><span class="modal-title">📚 Book Details</span><button class="modal-close" onclick="closeModal('detail-modal')">✕</button></div>
    <div class="modal-body" id="detail-body"></div>
    <div class="modal-footer" id="detail-footer"></div>
  </div>
</div>

<!-- BORROW MODAL -->
<div class="modal-backdrop" id="borrow-modal">
  <div class="modal" style="max-width:400px">
    <div class="modal-header"><span class="modal-title">📋 Confirm Borrow</span><button class="modal-close" onclick="closeModal('borrow-modal')">✕</button></div>
    <div class="modal-body" id="borrow-body"></div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('borrow-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="confirmBorrow()">📋 Borrow Book</button>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
<script>
const BG = ['linear-gradient(160deg,#e8f0fb,#c5d8f5)','linear-gradient(160deg,#fdf3e1,#f5dfa5)','linear-gradient(160deg,#fbeae8,#f5c5c2)','linear-gradient(160deg,#e6f5ed,#b3e0c5)','linear-gradient(160deg,#f3e8ff,#e5c5ff)'];
let searchQ    = sessionStorage.getItem('lms-search')||'';
let genreFilter= sessionStorage.getItem('lms-genre') ||'all';
let availFilter= 'all';
let borrowingBookId = null;

function closeModal(id){ document.getElementById(id).classList.remove('open'); }

function buildGenrePills(){
  const genres = ['all',...new Set(LMS.books.map(b=>b.genre).filter(Boolean))];
  document.getElementById('genre-pills').innerHTML = genres.map(g=>
    `<span class="genre-pill${genreFilter===g?' active':''}" data-genre="${g}" onclick="setGenre('${g}',this)">${g==='all'?'📚 All':g}</span>`
  ).join('');
}

function setGenre(g,el){
  genreFilter=g;
  sessionStorage.setItem('lms-genre', g==='all'?'':g);
  document.querySelectorAll('.genre-pill').forEach(p=>p.classList.remove('active'));
  el.classList.add('active');
  render();
}

function render(){
  searchQ = document.getElementById('main-search').value.toLowerCase();
  sessionStorage.setItem('lms-search', searchQ);
  const data = LMS.books.filter(b=>{
    if(genreFilter!=='all' && b.genre!==genreFilter) return false;
    if(availFilter!=='all' && b.status!==availFilter) return false;
    if(searchQ && !`${b.title} ${b.author} ${b.isbn||''} ${b.genre}`.toLowerCase().includes(searchQ)) return false;
    return true;
  });
  document.getElementById('result-count').textContent = `${data.length} book${data.length!==1?'s':''} found`;
  document.getElementById('empty-cat').style.display  = data.length?'none':'block';

  document.getElementById('cat-tbody').innerHTML = data.length ? data.map((b,i)=>{
    const isBorrowed = LMS.isBorrowedByUser(b.id);
    const inWishlist = LMS.wishlist.includes(b.id)||LMS.wishlist.includes(String(b.id));
    const statusBadge = b.copies_available > 0 
      ? `<span class="badge badge-success">✅ Available</span>`
      : `<span class="badge badge-warning">⚠️ Unavailable</span>`;
    let actionBtn='';
    if(b.copies_available > 0 && !isBorrowed)
      actionBtn=`<button class="btn btn-primary" style="font-size:.75rem;padding:.3rem .75rem" onclick="initBorrow(${b.id})">📋 Borrow</button>`;
    else if(isBorrowed)
      actionBtn=`<span class="badge badge-success" style="padding:.3rem .7rem">✅ You have it</span>`;
    else if(inWishlist)
      actionBtn=`<span class="badge" style="background:var(--success-light);color:var(--success);padding:.3rem .7rem">🔖 Wishlisted</span>`;
    else
      actionBtn=`<button class="btn btn-outline" style="font-size:.75rem;padding:.3rem .75rem;color:var(--accent-2);border-color:var(--accent-2)" onclick="addWishlist(${b.id},'${b.title.replace(/'/g,"\\'")}',this)">🔖 Wishlist</button>`;
    return `<tr style="animation:fadeUp .25s ${i*.03}s both">
      <td><div class="td-book">
        <div class="book-cover" style="background:${BG[i%BG.length]}">${b.cover||'📚'}</div>
        <div><div class="book-title">${b.title}</div><div class="book-author">${b.author}</div></div>
      </div></td>
      <td><span class="badge badge-accent">${b.genre||'—'}</span></td>
      <td style="font-size:.8rem;color:var(--text-muted)">${b.isbn||'—'}</td>
      <td style="font-size:.85rem;font-weight:600;color:var(--text)">${b.copies||'—'}</td>
      <td>${statusBadge}</td>
      <td><div class="action-btns">
        <button class="action-btn" onclick="showDetail(${b.id})" title="Details">👁</button>
        ${actionBtn}
      </div></td>
    </tr>`;
  }).join('') : '';
}

function showDetail(id){
  const b = LMS.books.find(x=>Number(x.id)===Number(id));
  if(!b) return;
  const isBorrowed = LMS.isBorrowedByUser(b.id);
  const inWishlist = LMS.wishlist.includes(b.id)||LMS.wishlist.includes(String(b.id));
  document.getElementById('detail-body').innerHTML=`
    <div style="text-align:center;font-size:4.5rem;margin-bottom:.75rem">${b.cover||'📚'}</div>
    <h2 style="font-family:'Playfair Display',serif;text-align:center;font-size:1.4rem;font-weight:900;margin-bottom:.2rem">${b.title}</h2>
    <p style="text-align:center;color:var(--text-muted);margin-bottom:1.5rem">${b.author}</p>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
      ${[['Genre',b.genre||'—'],['ISBN',b.isbn||'—'],['Copies',b.copies||'—'],['Status',b.status]].map(([k,v])=>`
      <div style="background:var(--surface-2);padding:.75rem;border-radius:10px">
        <div style="font-size:.7rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:.2rem">${k}</div>
        <div style="font-weight:600;color:var(--text)">${v}</div>
      </div>`).join('')}
    </div>`;
  document.getElementById('detail-footer').innerHTML=`
    <button class="btn btn-outline" onclick="closeModal('detail-modal')">Close</button>
    ${b.status==='available'&&!isBorrowed?`<button class="btn btn-primary" onclick="initBorrow(${b.id});closeModal('detail-modal')">📋 Borrow Now</button>`:''}
    ${b.status!=='available'&&!inWishlist?`<button class="btn btn-outline" style="color:var(--accent-2);border-color:var(--accent-2)" onclick="addWishlist(${b.id},'${b.title.replace(/'/g,"\\'")}',null);closeModal('detail-modal')">🔖 Add to Wishlist</button>`:''}`;
  document.getElementById('detail-modal').classList.add('open');
}

function initBorrow(id){
  if(LMS.user_borrowed_ids.length>=5){ LMS.toast('error','⚠️','Borrow limit reached (5 books).'); return; }
  const b=LMS.books.find(x=>Number(x.id)===Number(id));
  const due=new Date(); due.setDate(due.getDate()+14);
  borrowingBookId=id;
  document.getElementById('borrow-body').innerHTML=`
    <div style="display:flex;gap:.85rem;align-items:center;background:var(--surface-2);border-radius:12px;padding:1rem;margin-bottom:1rem">
      <span style="font-size:2.5rem">${b.cover||'📚'}</span>
      <div><div style="font-weight:700;font-size:.95rem">${b.title}</div><div style="font-size:.8rem;color:var(--text-muted)">${b.author}</div></div>
    </div>
    <div style="background:var(--accent-light);border-radius:10px;padding:.85rem">
      <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.1rem">Due date</div>
      <div style="font-weight:700;color:var(--accent)">${due.toLocaleDateString('en-US',{month:'long',day:'numeric',year:'numeric'})}</div>
    </div>`;
  document.getElementById('borrow-modal').classList.add('open');
}
function confirmBorrow(){
  fetch('../actions/borrow_book.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({book_id:borrowingBookId})})
    .then(r=>r.json()).then(d=>{ LMS.toast(d.success?'success':'error',d.success?'📋':'⚠️',d.message||'Done'); if(d.success) LMS.syncData(); })
    .catch(()=>LMS.toast('error','⚠️','Network error.'));
  closeModal('borrow-modal');
}

function addWishlist(id,title,btn){
  if(!LMS.wishlist.includes(id)&&!LMS.wishlist.includes(String(id))){ LMS.wishlist.push(id); sessionStorage.setItem('lms-wishlist',JSON.stringify(LMS.wishlist)); }
  if(btn){ btn.textContent='🔖 Wishlisted'; btn.disabled=true; btn.style.color='var(--success)'; btn.style.borderColor='var(--success)'; }
  LMS.toast('info','🔖',`"${title}" added to wishlist!`);
}

// Sync topbar search with main search
document.getElementById('main-search').value=searchQ;
document.getElementById('main-search').addEventListener('input', render);
document.getElementById('top-search').addEventListener('input',e=>{ document.getElementById('main-search').value=e.target.value; render(); });
document.getElementById('srch-big').addEventListener('focusin', ()=>{ document.getElementById('srch-big').style.borderColor='var(--accent)'; document.getElementById('srch-big').style.boxShadow='0 0 0 4px var(--accent-light)'; });
document.getElementById('srch-big').addEventListener('focusout',()=>{ document.getElementById('srch-big').style.borderColor=''; document.getElementById('srch-big').style.boxShadow=''; });

document.querySelectorAll('[data-avail]').forEach(btn=>{
  btn.addEventListener('click',()=>{ document.querySelectorAll('[data-avail]').forEach(b=>b.classList.remove('active')); btn.classList.add('active'); availFilter=btn.dataset.avail; render(); });
});

['detail-modal','borrow-modal'].forEach(id=>{
  document.getElementById(id).addEventListener('click',e=>{ if(e.target===e.currentTarget) closeModal(id); });
});
document.addEventListener('keydown',e=>{ if(e.key==='Escape') ['detail-modal','borrow-modal'].forEach(closeModal); });

document.addEventListener('lmsDataLoaded',()=>{ buildGenrePills(); render(); });
</script>
</body>
</html>
