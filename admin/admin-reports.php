<?php
require_once('../includes/auth.php');
$pageTitle  = 'Reports';
$activePage = 'reports';
$topbarRole = 'admin';
$footerSearchAction = "location.href='admin-reports.php'";
$extraCss = <<<'CSS'
.chart-card { background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;animation:fadeUp .5s both; }
    .chart-title { font-family:'Playfair Display',serif;font-size:1rem;font-weight:700;color:var(--text);margin-bottom:1.25rem; }
    .bar-chart { display:flex;align-items:flex-end;gap:8px;height:120px; }
    .bc-col { flex:1;display:flex;flex-direction:column;align-items:center;gap:5px; }
    .bc-bar { width:100%;border-radius:6px 6px 0 0;min-height:4px;transition:height .8s cubic-bezier(.4,0,.2,1);cursor:pointer; }
    .bc-bar:hover { filter:brightness(1.15); }
    .bc-label { font-size:.65rem;color:var(--text-muted);font-weight:600; }
    .bc-val { font-size:.7rem;color:var(--text);font-weight:700; }
    .donut-wrap { display:flex;align-items:center;gap:2rem;flex-wrap:wrap; }
    .donut-legend { display:flex;flex-direction:column;gap:.6rem; }
    .legend-item { display:flex;align-items:center;gap:.6rem;font-size:.85rem; }
    .legend-dot { width:12px;height:12px;border-radius:50%;flex-shrink:0; }
    .charts-grid { display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.75rem; }
    canvas { display:block; }
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
      <div><p class="page-eyebrow">Admin / Reports</p><h1 class="page-title">Analytics & Reports</h1><p class="page-subtitle">Library performance overview</p></div>
      <button class="btn btn-outline" onclick="printReport()">🖨️ Print Report</button>
    </div>

    <div class="stats-grid" style="grid-template-columns:repeat(4,1fr)">
      <div class="stat-card" style="animation-delay:.05s"><div class="stat-card-top"><span class="stat-label">Total Books</span><div class="stat-icon-wrap" style="background:var(--accent-light)">📚</div></div><div class="stat-number" id="r-books">0</div><div class="stat-bar" style="background:linear-gradient(90deg,var(--accent),#2563eb)"></div></div>
      <div class="stat-card" style="animation-delay:.1s"><div class="stat-card-top"><span class="stat-label">Members</span><div class="stat-icon-wrap" style="background:var(--success-light)">👥</div></div><div class="stat-number" id="r-members">0</div><div class="stat-bar" style="background:linear-gradient(90deg,var(--success),#27ae60)"></div></div>
      <div class="stat-card" style="animation-delay:.15s"><div class="stat-card-top"><span class="stat-label">Active Borrows</span><div class="stat-icon-wrap" style="background:var(--accent-2-light)">🔄</div></div><div class="stat-number" id="r-borrows">0</div><div class="stat-bar" style="background:linear-gradient(90deg,var(--accent-2),#f39c12)"></div></div>
      <div class="stat-card" style="animation-delay:.2s"><div class="stat-card-top"><span class="stat-label">Total Fines</span><div class="stat-icon-wrap" style="background:var(--danger-light)">💰</div></div><div class="stat-number" id="r-fines">Rs.0</div><div class="stat-bar" style="background:linear-gradient(90deg,var(--danger),#e74c3c)"></div></div>
    </div>

    <div class="charts-grid">
      <!-- Borrowings by day -->
      <div class="chart-card" style="animation-delay:.25s">
        <div class="chart-title">📅 Borrowings This Week</div>
        <div class="bar-chart" id="weekly-chart"></div>
      </div>

      <!-- Genre distribution -->
      <div class="chart-card" style="animation-delay:.3s">
        <div class="chart-title">🏷️ Books by Genre</div>
        <div class="bar-chart" id="genre-chart"></div>
      </div>

      <!-- Book status -->
      <div class="chart-card" style="animation-delay:.35s">
        <div class="chart-title">📊 Book Status Breakdown</div>
        <div class="donut-wrap">
          <canvas id="donut-canvas" width="110" height="110"></canvas>
          <div class="donut-legend" id="donut-legend"></div>
        </div>
      </div>

      <!-- Member roles -->
      <div class="chart-card" style="animation-delay:.4s">
        <div class="chart-title">👥 Member Roles</div>
        <div class="donut-wrap">
          <canvas id="role-canvas" width="110" height="110"></canvas>
          <div class="donut-legend" id="role-legend"></div>
        </div>
      </div>
    </div>

    <!-- Top borrowed books -->
    <div class="card" style="animation-delay:.45s;margin-bottom:1.75rem">
      <div class="card-header"><span class="card-title">🏆 Top Borrowed Books</span></div>
      <div class="card-body">
        <div id="top-books" style="display:flex;flex-direction:column;gap:.75rem"></div>
      </div>
    </div>

    <!-- Fine collection table -->
    <div class="table-card" style="animation-delay:.5s">
      <div class="table-toolbar"><span class="table-title">💰 Outstanding Fines</span></div>
      <div style="overflow-x:auto"><table>
        <thead><tr><th>Member</th><th>Book</th><th>Days Overdue</th><th>Fine</th><th>Action</th></tr></thead>
        <tbody id="fines-tbody"></tbody>
      </table></div>
    </div>
  </main>

<!-- EXPORT REPORT MODAL -->
<div class="modal-backdrop" id="export-modal">
  <div class="modal" style="max-width:440px">
    <div class="modal-header">
      <span class="modal-title">📥 Export Report</span>
      <button class="modal-close" onclick="document.getElementById('export-modal').classList.remove('open')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label>Report Type</label>
        <select class="form-control" id="exp-type">
          <option value="full">📊 Full Library Report</option>
          <option value="books">📚 Books Catalogue</option>
          <option value="members">👥 Members List</option>
          <option value="borrowings">🔄 Borrowings History</option>
          <option value="overdue">⚠️ Overdue Summary</option>
          <option value="fines">💰 Fines Report</option>
        </select>
      </div>
      <div class="form-group">
        <label>Format</label>
        <div style="display:flex;gap:.75rem;margin-top:.35rem">
          <label style="display:flex;align-items:center;gap:.4rem;font-size:.875rem;cursor:pointer"><input type="radio" name="exp-fmt" value="csv" checked /> CSV</label>
          <label style="display:flex;align-items:center;gap:.4rem;font-size:.875rem;cursor:pointer"><input type="radio" name="exp-fmt" value="pdf" /> PDF (print)</label>
        </div>
      </div>
      <div class="form-group">
        <label>Date Range</label>
        <div style="display:flex;gap:.5rem">
          <input type="date" class="form-control" id="exp-from" style="flex:1" />
          <span style="align-self:center;color:var(--text-muted)">to</span>
          <input type="date" class="form-control" id="exp-to" style="flex:1" />
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="document.getElementById('export-modal').classList.remove('open')">Cancel</button>
      <button class="btn btn-primary" onclick="confirmExport()">📥 Export</button>
    </div>
  </div>
</div>

<!-- BOOK DETAIL MODAL (top borrowed) -->
<div class="modal-backdrop" id="book-detail-modal">
  <div class="modal" style="max-width:420px">
    <div class="modal-header">
      <span class="modal-title">📚 Book Stats</span>
      <button class="modal-close" onclick="document.getElementById('book-detail-modal').classList.remove('open')">✕</button>
    </div>
    <div class="modal-body" id="book-detail-body"></div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="location.href='admin-books.php'">✏️ Edit Book</button>
      <button class="btn btn-primary" onclick="document.getElementById('book-detail-modal').classList.remove('open')">Close</button>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
<script>
  const sb=document.getElementById('sidebar'),ov=document.getElementById('overlay');
  document.getElementById('menu-btn').addEventListener('click',()=>{sb.classList.toggle('open');ov.classList.toggle('show');});
  ov.addEventListener('click',()=>{sb.classList.remove('open');ov.classList.remove('show');});

  function animC(el,target,prefix='',suffix=''){
    const dur=1200,start=performance.now();
    const run=now=>{const p=Math.min((now-start)/dur,1);const e=1-Math.pow(1-p,3);el.textContent=prefix+Math.floor(e*target).toLocaleString()+suffix;if(p<1)requestAnimationFrame(run);else el.textContent=prefix+target.toLocaleString()+suffix;};
    requestAnimationFrame(run);
  }

  function drawDonut(canvas, segments) {
    const ctx=canvas.getContext('2d');
    const cx=55,cy=55,r=45,inner=28;
    let start=-Math.PI/2;
    ctx.clearRect(0,0,110,110);
    segments.forEach(s=>{
      const angle=2*Math.PI*s.pct/100;
      ctx.beginPath(); ctx.moveTo(cx,cy);
      ctx.arc(cx,cy,r,start,start+angle);
      ctx.closePath(); ctx.fillStyle=s.color; ctx.fill();
      start+=angle;
    });
    ctx.beginPath(); ctx.arc(cx,cy,inner,0,2*Math.PI);
    ctx.fillStyle=getComputedStyle(document.documentElement).getPropertyValue('--surface').trim()||'#fff';
    ctx.fill();
  }

  function buildBarChart(container, data, colorFn) {
    const max=Math.max(...data.map(d=>d.val),1);
    container.innerHTML='';
    data.forEach(d=>{
      const col=document.createElement('div'); col.className='bc-col';
      const val=document.createElement('div'); val.className='bc-val'; val.textContent=d.val;
      const bar=document.createElement('div'); bar.className='bc-bar';
      bar.style.height='0'; bar.style.background=colorFn(d);
      bar.title=`${d.label}: ${d.val}`;
      const lbl=document.createElement('div'); lbl.className='bc-label'; lbl.textContent=d.label;
      col.append(val,bar,lbl); container.appendChild(col);
      setTimeout(()=>{ bar.style.height=((d.val/max)*100)+'%'; },300);
    });
  }

  console.log("LMS object:", LMS);

  function render() {
    if(!window.LMS || !LMS.books || !LMS.members || !LMS.borrowings){
      console.warn("⏳ LMS data not ready yet");
      return;
    }
    const books=LMS.books, members=LMS.members, borrow=LMS.borrowings;

    console.log(`📊 Data Counts -> Books: ${books.length}, Members: ${members.length}, Borrows: ${borrow.length}`);
    if (books.length === 0){ console.warn("⚠️ Warning: Books array is empty."); return;} 
    animC(document.getElementById('r-books'),books.length);
    animC(document.getElementById('r-members'),members.length);
    animC(document.getElementById('r-borrows'),borrow.filter(b=>!b.returned_date).length);
    const fines=borrow.reduce((a,b)=>a+(b.fine||0),0);
    animC(document.getElementById('r-fines'),fines,'Rs.');

    /* Weekly borrowings */
    const days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
    const now = new Date();
    const last7Days = [];

    // Create labels for the last 7 days
    for(let i=6; i>=0; i--) {
        const d = new Date();
        d.setDate(now.getDate() - i);
        last7Days.push({
            label: days[d.getDay()],
            dateStr: d.toISOString().split('T')[0],
            val: 0
        });
    }

    // Count actual borrowings per day
    borrow.forEach(b => {
        const bDate = b.issue_date; // Ensure this matches your DB column
        const match = last7Days.find(d => d.dateStr === bDate);
        if(match) match.val++;
    });

    const maxW = Math.max(...last7Days.map(x => x.val), 1);
    buildBarChart(document.getElementById('weekly-chart'), last7Days, d => 
        d.val === maxW && d.val > 0 ? 'var(--accent)' : 'var(--accent-light)'
    );

    /* Genre chart */
    const genres={};
    books.forEach(b=>{genres[b.genre]=(genres[b.genre]||0)+1;});
    const genreData=Object.entries(genres).sort((a,b)=>b[1]-a[1]).slice(0,6).map(([label,val])=>({label,val}));
    const gColors=['var(--accent)','var(--success)','var(--accent-2)','var(--danger)','#6b21a8','#0369a1'];
    buildBarChart(document.getElementById('genre-chart'),genreData,(d,i)=>gColors[genreData.indexOf(d)%gColors.length]);
    document.querySelectorAll('#genre-chart .bc-bar').forEach((bar,i)=>{bar.style.background=gColors[i%gColors.length];});

    /* Donut - book status */
    // 1. Get IDs of books that are currently out (no return date)
    const activeBorrowings = borrow.filter(b => !b.returned_date);
    const activeBookIds = activeBorrowings.map(b => b.book_id);

    // 2. Identify which active borrowings are past their due date
    const overdueBookIds = activeBorrowings
      .filter(b => new Date(b.due_date) < new Date())
      .map(b => b.book_id);

    // 3. Calculate counts
    const overdueCount = overdueBookIds.length;
    const borrowedCount = activeBookIds.length - overdueCount; // Active but not overdue
    const availCount = books.length - activeBookIds.length;

    const total = books.length || 1;
    const statusSegs = [
      {label: 'Available', val: availCount, pct: (availCount/total)*100, color: '#1a7a4a'},
      {label: 'Borrowed',  val: borrowedCount, pct: (borrowedCount/total)*100, color: '#d4860a'},
      {label: 'Overdue',   val: overdueCount, pct: (overdueCount/total)*100, color: '#c0392b'},
    ];
    drawDonut(document.getElementById('donut-canvas'),statusSegs);
    document.getElementById('donut-legend').innerHTML=statusSegs.map(s=>`<div class="legend-item"><div class="legend-dot" style="background:${s.color}"></div><span>${s.label}: <strong>${s.val}</strong></span></div>`).join('');

    /* Donut - member roles */
    const roles={};
    members.forEach(m=>{roles[m.role]=(roles[m.role]||0)+1;});
    const rColors=['#1a4a8a','#1a7a4a','#c8a45a','#6b21a8'];
    const rTotal=members.length||1;
    const roleSegs=Object.entries(roles).map(([label,val],i)=>({label,val,pct:val/rTotal*100,color:rColors[i%rColors.length]}));
    drawDonut(document.getElementById('role-canvas'),roleSegs);
    document.getElementById('role-legend').innerHTML=roleSegs.map(s=>`<div class="legend-item"><div class="legend-dot" style="background:${s.color}"></div><span>${s.label}: <strong>${s.val}</strong></span></div>`).join('');

    /* Top books */
    const bookCount={};
    borrow.forEach(b=>{bookCount[b.book_id]=(bookCount[b.book_id]||0)+1;});
    const topBooks=Object.entries(bookCount).sort((a,b)=>b[1]-a[1]).slice(0,5);
    const maxBorrow=Math.max(...topBooks.map(x=>x[1]),1);
    document.getElementById('top-books').innerHTML=topBooks.map(([id,count])=>{
      const bk=books.find(x=>x.id==id);
      const pct=(count/maxBorrow*100).toFixed(0);
      return `<div style="display:flex;align-items:center;gap:.75rem;cursor:pointer;padding:.5rem;border-radius:10px;transition:background var(--transition)" onclick="viewBookDetail(${id})">
        <span>${bk?.cover||'📚'}</span>
        <div style="flex:1;min-width:0">
          <div style="display:flex;justify-content:space-between;margin-bottom:.3rem"><span style="font-size:.875rem;font-weight:600">${bk?.title||'Unknown'}</span><span style="font-size:.8rem;font-weight:700;color:var(--accent)">${count} borrows</span></div>
          <div style="height:6px;background:var(--surface-2);border-radius:3px"><div style="height:100%;width:${pct}%;background:linear-gradient(90deg,var(--accent),#2563eb);border-radius:3px;transition:width .8s"></div></div>
        </div>
        <span style="font-size:.75rem;color:var(--text-muted)">👁</span>
      </div>`;
    }).join('');

    /* Fines table */
    const fineRows=borrow.filter(b => !b.returned_date && new Date(b.due_date) < new Date());
    document.getElementById('fines-tbody').innerHTML=fineRows.map(b=>{
      const m=members.find(x=>x.id===b.member_id);
      const bk=books.find(x=>x.id===b.book_id);
      const days=Math.max(0,Math.floor((new Date()-new Date(b.due_date))/(1000*60*60*24)));
      const fine=parseFloat(b.fine || 0);
      return `<tr>
        <td><div style="display:flex;align-items:center;gap:.6rem"><div style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,${m?.color||'#1a4a8a,#2563eb'});display:grid;place-items:center;font-size:.7rem;font-weight:700;color:#fff">${m?.initials||'?'}</div>${m?.name||'?'}</div></td>
        <td>${bk?.title||'?'}</td>
        <td style="color:var(--danger);font-weight:700">${days} days</td>
        <td style="color:var(--danger);font-weight:700">Rs.${fine}</td>
        <td><button class="btn btn-outline" style="padding:.3rem .7rem;font-size:.78rem" onclick="toast('info','📬','Reminder sent!')">📬 Remind</button></td>
      </tr>`;
    }).join('');

    console.log("✅ Render complete.");
  }

  function printReport() {
    // Set dates to current month then open export modal
    const now = new Date();
    const y = now.getFullYear(), m = String(now.getMonth()+1).padStart(2,'0');
    document.getElementById('exp-from').value = `${y}-${m}-01`;
    document.getElementById('exp-to').value   = new Date(y, now.getMonth()+1, 0).toISOString().split('T')[0];
    document.getElementById('export-modal').classList.add('open');
  }
  function confirmExport() {
    const fmt  = document.querySelector('input[name="exp-fmt"]:checked').value;
    const typeSelect = document.getElementById('exp-type')
    const typeValue = typeSelect.value;
    const typeText = typeSelect.options[typeSelect.selectedIndex].text;

    document.getElementById('export-modal').classList.remove('open');
    if(fmt === 'pdf') { window.print(); toast('info','🖨️','Sending to print…'); return;}
    let rows = [];
    let fileName = `lms-${typeValue}-report.csv`;

    if(typeValue === 'books') {
        rows = [['Title','Author','Genre','Status','Copies'], 
                ...LMS.books.map(b => [b.title, b.author, b.genre, b.status, b.copies])];
    } else if(typeValue === 'borrowings' || typeValue === 'overdue') {
        rows = [['Book','Member','Borrow Date','Due Date','Status'],
                ...LMS.borrowings.map(b => {
                    const bk = LMS.books.find(x => x.id == b.book_id);
                    const m = LMS.members.find(x => x.id == b.member_id);
                    return [bk?.title || 'Unknown', m?.name || 'Unknown', b.issue_date, b.due_date, b.returned_date ? 'Returned' : 'Active'];
                })];
    } else {
        // Default Full Report
        rows = [['Type','Details'], ['Total Books', LMS.books.length], ['Total Members', LMS.members.length]];
    }

    const csv = rows.map(r => r.map(cell => `"${cell}"`).join(',')).join('\n');
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.setAttribute('download', fileName);
    link.click();
    
    toast('success','📥',`${typeText} exported successfully!`);
  
  }
  function viewBookDetail(book_id) {
    const b  = LMS.books.find(x=>x.id===book_id);
    const bw = LMS.borrowings.filter(x=>x.book_id===book_id);
    document.getElementById('book-detail-body').innerHTML = `
      <div style="text-align:center;font-size:3rem;margin-bottom:.75rem">${b?.cover||'📚'}</div>
      <h3 style="font-family:'Playfair Display',serif;font-size:1.2rem;font-weight:900;text-align:center;margin-bottom:.25rem">${b?.title}</h3>
      <p style="text-align:center;color:var(--text-muted);margin-bottom:1.25rem">${b?.author}</p>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.65rem">
        ${[['Genre',b?.genre],['Status',b?.status],['Copies',b?.copies],['Total Borrows',bw.length],['Currently Out',bw.filter(x=>!x.returned_date).length],['Total Fines','Rs.'+(bw.reduce((a,x)=>a+(x.fine||0),0))]].map(([k,v])=>`
        <div style="background:var(--surface-2);border-radius:9px;padding:.7rem">
          <div style="font-size:.68rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:.15rem">${k}</div>
          <div style="font-weight:600;color:var(--text)">${v}</div>
        </div>`).join('')}
      </div>`;
    document.getElementById('book-detail-modal').classList.add('open');
  }
  ['export-modal','book-detail-modal'].forEach(id=>{
    document.getElementById(id).addEventListener('click',e=>{if(e.target===e.currentTarget)e.currentTarget.classList.remove('open');});
  });
  document.addEventListener('keydown',e=>{if(e.key==='Escape')['export-modal','book-detail-modal'].forEach(id=>document.getElementById(id).classList.remove('open'));});

  function toast(type,icon,msg){const el=document.createElement('div');el.className=`toast ${type}`;el.innerHTML=`<span class="toast-icon">${icon}</span><span class="toast-msg">${msg}</span>`;document.getElementById('toast-container').appendChild(el);setTimeout(()=>{el.classList.add('removing');setTimeout(()=>el.remove(),300);},3200);}

  document.addEventListener("lmsDataLoaded", () => {
    console.log("LMS loaded", LMS);
    render();
  });
  
</script>
</body>
</html>
