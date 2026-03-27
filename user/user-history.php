<?php
require_once('../includes/auth.php');
$pageTitle  = 'Reading History';
$activePage = 'history';
$topbarRole = 'user';
$footerSearchAction = "location.href='user-catalogue.php'";
// No page-specific CSS
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
    <div><p class="page-eyebrow">My Library</p><h1 class="page-title">Reading History</h1><p class="page-subtitle" id="hist-count">Loading…</p></div>
  </div>

  <div class="stats-grid" style="grid-template-columns:repeat(3,1fr)">
    <div class="stat-card" style="animation-delay:.05s">
      <div class="stat-card-top"><span class="stat-label">Total Read</span><div class="stat-icon-wrap" style="background:var(--success-light)">✅</div></div>
      <div class="stat-number" id="s-read">—</div>
      <div class="stat-bar" style="background:linear-gradient(90deg,var(--success),#27ae60)"></div>
    </div>
    <div class="stat-card" style="animation-delay:.1s">
      <div class="stat-card-top"><span class="stat-label">Fines Paid</span><div class="stat-icon-wrap" style="background:var(--warning-light)">💰</div></div>
      <div class="stat-number" id="s-fines">Rs.0</div>
      <div class="stat-bar" style="background:linear-gradient(90deg,var(--warning),#f39c12)"></div>
    </div>
    <div class="stat-card" style="animation-delay:.15s">
      <div class="stat-card-top"><span class="stat-label">Avg Rating</span><div class="stat-icon-wrap" style="background:var(--accent-2-light)">⭐</div></div>
      <div class="stat-number" id="s-rating">—</div>
      <div class="stat-bar" style="background:linear-gradient(90deg,var(--accent-2),#f39c12)"></div>
    </div>
  </div>

  <div class="table-card">
    <div class="table-toolbar">
      <span class="table-title">📋 All Returned Books</span>
      <div style="display:flex;gap:.5rem;margin-left:auto">
        <input type="text" id="search-input" placeholder="Search books..." style="padding:.4rem .8rem;border:1px solid var(--border);border-radius:6px;font-size:.85rem;width:200px" />
      </div>
    </div>
    <div style="overflow-x:auto">
      <table>
        <thead><tr><th>Book</th><th>Genre</th><th>Issued</th><th>Returned</th><th>Fine</th><th>Rating</th></tr></thead>
        <tbody id="hist-tbody"><tr><td colspan="6" style="text-align:center;padding:2.5rem;color:var(--text-muted)">Loading…</td></tr></tbody>
      </table>
    </div>
    <div id="empty-hist" class="empty-state" style="display:none">
      <div class="empty-state-icon">📚</div><h3>No history yet</h3><p>Books you return will appear here.</p>
    </div>
  </div>

</main>

<!-- RATE MODAL -->
<div class="modal-backdrop" id="rate-modal">
  <div class="modal" style="max-width:380px">
    <div class="modal-header"><span class="modal-title">⭐ Rate This Book</span><button class="modal-close" onclick="closeModal('rate-modal')">✕</button></div>
    <div class="modal-body" style="text-align:center">
      <p style="font-weight:700;color:var(--text);margin-bottom:1.25rem;font-size:.95rem" id="rate-title"></p>
      <div style="display:flex;justify-content:center;gap:.5rem;font-size:2.2rem;margin-bottom:1rem">
        <span class="star" data-v="1" style="cursor:pointer">☆</span>
        <span class="star" data-v="2" style="cursor:pointer">☆</span>
        <span class="star" data-v="3" style="cursor:pointer">☆</span>
        <span class="star" data-v="4" style="cursor:pointer">☆</span>
        <span class="star" data-v="5" style="cursor:pointer">☆</span>
      </div>
      <p style="font-size:.82rem;color:var(--text-muted)">Click a star to rate</p>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('rate-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="saveRating()">⭐ Save Rating</button>
    </div>
  </div>
</div>

<!-- DETAIL MODAL -->
<div class="modal-backdrop" id="detail-modal">
  <div class="modal" style="max-width:440px">
    <div class="modal-header"><span class="modal-title">📚 Book Details</span><button class="modal-close" onclick="closeModal('detail-modal')">✕</button></div>
    <div class="modal-body" id="detail-body"></div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="location.href='user-catalogue.php'">🔍 Find in Catalogue</button>
      <button class="btn btn-primary" onclick="closeModal('detail-modal')">Close</button>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
<script>
const BG = ['linear-gradient(160deg,#e8f0fb,#c5d8f5)','linear-gradient(160deg,#fdf3e1,#f5dfa5)','linear-gradient(160deg,#fbeae8,#f5c5c2)','linear-gradient(160deg,#e6f5ed,#b3e0c5)','linear-gradient(160deg,#f3e8ff,#e5c5ff)'];
let searchQ='', ratings=JSON.parse(sessionStorage.getItem('lms-ratings')||'{}'), ratingId=null, chosenRating=0;

function closeModal(id){ document.getElementById(id).classList.remove('open'); }
function fmtDate(d){ 
  if(!d) return '—';
  try {
    // Handle different date formats
    const date = new Date(d);
    if(isNaN(date.getTime())) return '—';
    return date.toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'});
  } catch(e) {
    return '—';
  }
}

function render(){
  // LMS.history is provided by load_data.php — returned borrowings for this user
  // Merge with book data for each history item
  const mergedHistory = (LMS.history||[]).map(h => {
    const book = LMS.books.find(b => Number(b.id) === Number(h.book_id)) || {};
    return { ...h, ...book }; // Merge borrowing data with book data
  });
  
  const history = mergedHistory.filter(h=>!searchQ||`${h.title||''} ${h.author||''}`.toLowerCase().includes(searchQ));
  const all     = mergedHistory;

  document.getElementById('hist-count').textContent = `${all.length} book${all.length!==1?'s':''} returned`;
  document.getElementById('s-read').textContent     = all.length;
  document.getElementById('s-fines').textContent    = `Rs.${all.reduce((a,h)=>a+(Number(h.fine)||0),0)}`;
  const rated = Object.values(ratings).filter(Number);
  document.getElementById('s-rating').textContent   = rated.length?(rated.reduce((a,b)=>a+b,0)/rated.length).toFixed(1)+'★':'—';

  document.getElementById('empty-hist').style.display = all.length?'none':'block';
  document.getElementById('hist-tbody').innerHTML = history.length ? history.map((h,i)=>{
    const r=ratings[h.book_id]; // Use book_id for ratings since that's unique per book
    const stars=r?'★'.repeat(r)+'☆'.repeat(5-r):'☆☆☆☆☆';
    return `<tr style="animation:fadeUp .3s ${i*.04}s both">
      <td><div class="td-book" style="cursor:pointer" onclick="showDetail(${h.book_id})">
        <div class="book-cover" style="background:${BG[i%BG.length]}">${h.cover||'📚'}</div>
        <div><div class="book-title">${h.title||'Unknown'}</div><div class="book-author">${h.author||''}</div></div>
      </div></td>
      <td><span class="badge badge-accent">${h.genre||'—'}</span></td>
      <td style="color:var(--text-muted);font-size:.82rem">${fmtDate(h.issue_date)}</td>
      <td style="color:var(--text-muted);font-size:.82rem">${fmtDate(h.returned_date)}</td>
      <td>${h.fine?`<span style="color:var(--danger);font-weight:700">Rs.${h.fine}</span>`:`<span style="color:var(--text-muted)">—</span>`}</td>
      <td><button onclick="openRate(${h.book_id},'${(h.title||'').replace(/'/g,"\\'")}',${r||0})"
        style="font-size:1.1rem;background:none;border:none;cursor:pointer;color:var(--accent-2);letter-spacing:.05em" title="Rate">${stars}</button></td>
    </tr>`;
  }).join('') : `<tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--text-muted)">No results.</td></tr>`;
}

function openRate(bid,title,existing){
  ratingId=bid; chosenRating=existing||0;
  document.getElementById('rate-title').textContent=title;
  updateStars(chosenRating);
  document.getElementById('rate-modal').classList.add('open');
}
function updateStars(val){
  document.querySelectorAll('.star').forEach(s=>{
    s.textContent=parseInt(s.dataset.v)<=val?'★':'☆';
    s.style.color=parseInt(s.dataset.v)<=val?'var(--accent-2)':'var(--text-light)';
  });
}
document.querySelectorAll('.star').forEach(s=>{
  s.addEventListener('click',()=>{ chosenRating=parseInt(s.dataset.v); updateStars(chosenRating); });
  s.addEventListener('mouseover',()=>updateStars(parseInt(s.dataset.v)));
  s.addEventListener('mouseout', ()=>updateStars(chosenRating));
});
function saveRating(){
  if(!chosenRating){ LMS.toast('warning','⚠️','Please select a rating.'); return; }
  ratings[ratingId]=chosenRating;
  sessionStorage.setItem('lms-ratings',JSON.stringify(ratings));
  closeModal('rate-modal');
  render(); LMS.toast('success','⭐','Rating saved!');
}

function showDetail(bookId){
  const b=LMS.books.find(x=>Number(x.id)===Number(bookId));
  if(!b) return;
  document.getElementById('detail-body').innerHTML=`
    <div style="text-align:center;font-size:3.5rem;margin-bottom:.75rem">${b.cover||'📚'}</div>
    <h3 style="font-family:'Playfair Display',serif;text-align:center;font-size:1.2rem;font-weight:900;margin-bottom:.2rem">${b.title}</h3>
    <p style="text-align:center;color:var(--text-muted);margin-bottom:1.25rem">${b.author}</p>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.65rem">
      ${[['Genre',b.genre||'—'],['ISBN',b.isbn||'—'],['Copies',b.copies||'—'],['Status',b.status]].map(([k,v])=>`
      <div style="background:var(--surface-2);border-radius:9px;padding:.7rem">
        <div style="font-size:.68rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:.15rem">${k}</div>
        <div style="font-weight:600;color:var(--text)">${v}</div>
      </div>`).join('')}
    </div>`;
  document.getElementById('detail-modal').classList.add('open');
}

document.getElementById('search-input').addEventListener('input',e=>{ searchQ=e.target.value.toLowerCase(); render(); });
['rate-modal','detail-modal'].forEach(id=>{
  document.getElementById(id).addEventListener('click',e=>{ if(e.target===e.currentTarget) closeModal(id); });
});
document.addEventListener('keydown',e=>{ if(e.key==='Escape') ['rate-modal','detail-modal'].forEach(closeModal); });
document.addEventListener('lmsDataLoaded', render);
</script>
</body>
</html>
