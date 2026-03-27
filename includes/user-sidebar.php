<?php
/**
 * includes/user-sidebar.php
 * Usage: <?php $activePage = 'dashboard'; include 'includes/user-sidebar.php'; ?>
 * $activePage values: dashboard | catalogue | books | history | wishlist | profile | notifications
 */
require_once 'auth.php';
$userNav = [
  ['id' => 'dashboard',     'icon' => '🏠', 'label' => 'Dashboard',      'href' => 'user-dashboard.php'],
  ['id' => 'catalogue',     'icon' => '🔍', 'label' => 'Browse Catalogue','href' => 'user-catalogue.php'],
  ['id' => 'books',         'icon' => '📖', 'label' => 'My Books',        'href' => 'user-books.php'],
  ['id' => 'history',       'icon' => '🕰️', 'label' => 'Reading History', 'href' => 'user-history.php'],
  ['id' => 'wishlist',      'icon' => '❤️', 'label' => 'Wishlist',        'href' => 'user-wishlist.php'],
];
$userAccount = [
  ['id' => 'notifications', 'icon' => '🔔', 'label' => 'Notifications',  'href' => 'user-notifications.php', 'badge' => 'sb-notifs', 'badgeColor' => 'var(--accent)'],
  ['id' => 'profile',       'icon' => '👤', 'label' => 'My Profile',      'href' => 'user-profile.php'],
];
$ap = $activePage ?? '';
?>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-logo">
    <span style="font-size:1.3rem">📚</span>
    <span class="wordmark">L<span>M</span>S</span>
    <span class="role-badge member">Member</span>
  </div>
  <nav class="sidebar-nav">
    <p class="sidebar-section-label">Library</p>
    <?php foreach ($userNav as $item): ?>
    <div class="nav-item<?= $ap === $item['id'] ? ' active' : '' ?>" onclick="location.href='<?= $item['href'] ?>'">
      <span class="nav-icon"><?= $item['icon'] ?></span> <?= $item['label'] ?>
      <?php if (!empty($item['badge'])): ?>
      <span class="nav-badge" id="<?= $item['badge'] ?>" style="background:<?= $item['badgeColor'] ?>">0</span>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>

    <p class="sidebar-section-label">Account</p>
    <?php foreach ($userAccount as $item): ?>
    <div class="nav-item<?= $ap === $item['id'] ? ' active' : '' ?>" onclick="location.href='<?= $item['href'] ?>'">
      <span class="nav-icon"><?= $item['icon'] ?></span> <?= $item['label'] ?>
      <?php if (!empty($item['badge'])): ?>
      <span class="nav-badge" id="<?= $item['badge'] ?>" style="background:<?= $item['badgeColor'] ?>">0</span>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </nav>
  <div class="sidebar-footer">
    <div class="sidebar-user">
      <div class="avatar" style="background:linear-gradient(135deg,var(--accent),#2563eb)"><?php echo $initials; ?></div>
      <div class="sidebar-user-info">
        <div class="sidebar-user-name"><?php echo $userName ?></div>
        <div class="sidebar-user-role"><?php if ($userRole === "member") {
          echo "Library Member";
        }elseif ($userRole === "staff") {
          echo "Faculty / Staff";
        }elseif ($userRole === "student") {
          echo "Student";
        }else{
          echo "Guest";
        } ?></div>
      </div>
      <button class="logout-btn" onclick="location.href='../actions/logout.php'" title="Logout">↪</button>
    </div>
  </div>
</aside>
<div class="overlay" id="overlay"></div>
