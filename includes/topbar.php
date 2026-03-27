<?php
/**
 * includes/topbar.php
 * Usage: <?php $topbarRole = 'admin'; include 'includes/topbar.php'; ?>
 * $topbarRole: 'admin' | 'user'
 * $topbarSearch: placeholder text (optional, default "Search…")
 * $topbarSearchAction: JS or href for search (optional)
 * $topbarAvatar: initials shown in avatar (optional, default 'AD' for admin, 'JD' for user)
 * $topbarNotifHref: href for notification bell (optional)
 */
$role    = $topbarRole        ?? 'admin';
$srch    = $topbarSearch      ?? ($role === 'admin' ? 'Search books, members…' : 'Search catalogue…');
$notifHref = $topbarNotifHref ?? ($role === 'admin' ? 'admin-notifications.php' : 'user-notifications.php');
$words = explode(' ', trim($userName)); 
$initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
$avatar  = $topbarAvatar      ?? ($role === 'admin' ? 'AD' : $initials);
$avatarGrad = $role === 'admin'
  ? 'linear-gradient(135deg,var(--accent),#2563eb)'
  : 'linear-gradient(135deg,var(--accent),#2563eb)';
?>
<header class="topbar">
  <button class="topbar-menu-btn" id="menu-btn">☰</button>
  <div class="topbar-search">
    <span>🔍</span>
    <input type="text" id="top-search" placeholder="<?= htmlspecialchars($srch) ?>" />
  </div>
  <div class="topbar-right">
    <button class="icon-btn" onclick="location.href='<?= $notifHref ?>'" title="Notifications">
      🔔<span class="dot" id="notif-dot"></span>
    </button>
    <button id="dark-toggle" aria-label="Toggle dark mode"></button>
    <div class="topbar-avatar" style="background:<?= $avatarGrad ?>"><?= htmlspecialchars($avatar) ?></div>
  </div>
</header>
