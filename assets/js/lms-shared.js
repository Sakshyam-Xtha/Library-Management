window.LMS = {
  /* ── State ── */
  books:            [],
  members:          [],
  borrowings:       [],   // all active borrowings (admin use)
  history:          [],   // returned borrowings for current user
  notifications:    [],   // notifications for current user
  user_borrowed_ids:[],   // book IDs currently borrowed by logged-in user
  session:          { userId: null, name: '', role: '', email: '', avatar: '', theme: 'light' },
  wishlist:         JSON.parse(sessionStorage.getItem('lms-wishlist') || '[]'),
  appstatus: null,
  applications:     [],

  /* ── 1. Data Synchronization ── */
  async syncData() {
    try {
      const res  = await fetch('../actions/load_data.php');
      if (!res.ok) throw new Error('load_data failed: ' + res.status);
      const data = await res.json();

      this.books             = data.books             || [];
      this.members           = data.members           || [];
      this.borrowings        = data.borrowings        || [];
      this.history           = data.history           || [];
      this.allHistory        = data.all_history       || [];
      this.notifications     = data.notifications     || [];
      this.user_borrowed_ids = data.user_borrowed_ids || [];
      this.app_status = data.app_status || null;
      this.applications = data.applications           || [];
      console.log("Borrow data",this.borrowings);
      
      /* Get session from PHP variables (set by auth.php) */
      if (window.phpSession) {
        Object.assign(this.session, window.phpSession);
      }
      
      if (data.session) Object.assign(this.session, data.session);

      /* update topbar avatar/name if elements exist */
      this._applySession();

      document.dispatchEvent(new CustomEvent('lmsDataLoaded'));
    } catch (err) {
      console.error('LMS syncData error:', err);
      /* still fire the event so pages render (empty state) */
      document.dispatchEvent(new CustomEvent('lmsDataLoaded'));
    }
  },

  _applySession() {
    /* topbar avatar initials */
    const av = document.querySelector('.topbar-avatar');
    if (av && this.session.name) {
      const parts = this.session.name.trim().split(' ');
      av.textContent = (parts[0][0] + (parts[1]?.[0] || '')).toUpperCase();
    }
    /* sidebar user block */
    const sbName = document.querySelector('.sidebar-user-name');
    const sbRole = document.querySelector('.sidebar-user-role');
    if (sbName && this.session.name) sbName.textContent = this.session.name;
    if (sbRole && this.session.role) sbRole.textContent = 'Library ' + this.session.role;
    /* notification dot */
    const dot = document.getElementById('notif-dot');
    if (dot) dot.style.display = this.notifications.some(n => !n.is_read) ? 'block' : 'none';
  },

  /* ── 2. Theme ── */
  initTheme() {
    const saved = localStorage.getItem('lms-theme') || document.documentElement.getAttribute('data-theme') || 'light';
    document.documentElement.setAttribute('data-theme', saved);
  },

  async toggleTheme() {
    const root    = document.documentElement;
    const current = root.getAttribute('data-theme');
    const next    = current === 'dark' ? 'light' : 'dark';
    root.setAttribute('data-theme', next);
    localStorage.setItem('lms-theme', next);
    try {
      const fd = new FormData();
      fd.append('theme', next);
      await fetch('../actions/save_theme.php', { method: 'POST', body: fd });
    } catch (e) { console.error("Theme sync failed", e); }
  },

  /* ── 3. Helpers ── */
  getBook(id) {
    return this.books.find(b => Number(b.id) === Number(id)) || null;
  },

  getMember(id) {
    return this.members.find(m => Number(m.id) === Number(id)) || null;
  },

  isBorrowedByUser(bookId) {
    return this.user_borrowed_ids.map(Number).includes(Number(bookId));
  },

  getOverdue() {
    const now = new Date();
    return this.borrowings.filter(b => !b.returned_date && b.due_date && new Date(b.due_date) < now);
  },

  getUserBorrowedBooks(){
  if(!this.session.userId) return [];

  return this.borrowings
    .filter(b =>
      Number(b.member_id) === Number(this.session.userId) &&
      !b.returned_date
    )
    .map(b => {
      const book = this.getBook(b.book_id);
      return book ? { ...book, ...b, borrowing_id: b.id } : null;
    })
    .filter(Boolean);
},

  /* ── 4. Page Init ── */
  initPage() {
    this.initTheme();

    document.addEventListener('click', (e) => {
      if (e.target.closest('#dark-toggle')) {
        e.preventDefault();
        this.toggleTheme();
      }
    });

    // sidebar toggle button may be called `menu-btn` (modern pages) or
    // `hamburger` (home page uses the same element for mobile nav). look for
    // either so the listener doesn't silently fail.
    const mb = document.getElementById('menu-btn') || document.getElementById('hamburger');
    const sb = document.getElementById('sidebar');
    const ov = document.getElementById('overlay');
    if (mb && sb && ov) {
      mb.onclick = () => { sb.classList.toggle('open'); ov.classList.toggle('show'); };
      ov.onclick = () => { sb.classList.remove('open'); ov.classList.remove('show'); };
    }

    this.syncData();
  },

  /* ── 5. Toast ── */
  toast(type, icon, msg) {
    let tc = document.getElementById('toast-container');
    if (!tc) { tc = document.createElement('div'); tc.id = 'toast-container'; document.body.appendChild(tc); }
    const el = document.createElement('div');
    el.className = `toast ${type}`;
    el.innerHTML = `<span class="toast-icon">${icon}</span><span class="toast-msg">${msg}</span>`;
    tc.appendChild(el);
    setTimeout(() => { el.classList.add('removing'); setTimeout(() => el.remove(), 300); }, 3000);
  }
};


document.addEventListener('DOMContentLoaded', () => LMS.initPage());
