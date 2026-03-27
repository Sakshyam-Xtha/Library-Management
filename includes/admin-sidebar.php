<?php
/**
 * includes/admin-sidebar.php
 * Usage: <?php $activePage = 'dashboard'; include 'includes/admin-sidebar.php'; ?>
 * $activePage values: dashboard | books | members | borrowings | overdue | reports | notifications | settings
 */
$adminNav = [
  ['id' => 'dashboard',     'icon' => '🏠', 'label' => 'Dashboard',     'href' => 'admin-dashboard.php'],
  ['id' => 'books',         'icon' => '📚', 'label' => 'Books',          'href' => 'admin-books.php',         'badge' => 'sb-books',   'badgeColor' => 'var(--accent)'],
  ['id' => 'members',       'icon' => '👥', 'label' => 'Members',        'href' => 'admin-members.php'],
  ['id' => 'borrowings',    'icon' => '🔄', 'label' => 'Borrowings',     'href' => 'admin-borrowings.php'],
  ['id' => 'overdue',       'icon' => '⏰', 'label' => 'Overdue',        'href' => 'admin-overdue.php',       'badge' => 'sb-overdue', 'badgeColor' => 'var(--danger)'],
];
$adminManage = [
  ['id' => 'reports',       'icon' => '📊', 'label' => 'Reports',        'href' => 'admin-reports.php'],
  ['id' => 'notifications', 'icon' => '📬', 'label' => 'Notifications',  'href' => 'admin-notifications.php'],
];
$adminSystem = [
  ['id' => 'settings',      'icon' => '⚙️', 'label' => 'Settings',       'href' => 'admin-settings.php'],
];
$ap = $activePage ?? '';
?>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-logo">
    <span style="font-size:1.3rem">📚</span>
    <span class="wordmark">L<span>M</span>S</span>
    <span class="role-badge admin">Admin</span>
  </div>
  <nav class="sidebar-nav">
    <p class="sidebar-section-label">Main</p>
    <?php foreach ($adminNav as $item): ?>
    <div class="nav-item<?= $ap === $item['id'] ? ' active' : '' ?>" onclick="location.href='<?= $item['href'] ?>'">
      <span class="nav-icon"><?= $item['icon'] ?></span> <?= $item['label'] ?>
      <?php if (!empty($item['badge'])): ?>
      <span class="nav-badge" id="<?= $item['badge'] ?>" style="background:<?= $item['badgeColor'] ?>">0</span>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>

    <p class="sidebar-section-label">Management</p>
    <?php foreach ($adminManage as $item): ?>
    <div class="nav-item<?= $ap === $item['id'] ? ' active' : '' ?>" onclick="location.href='<?= $item['href'] ?>'">
      <span class="nav-icon"><?= $item['icon'] ?></span> <?= $item['label'] ?>
    </div>
    <?php endforeach; ?>

    <p class="sidebar-section-label">System</p>
    <?php foreach ($adminSystem as $item): ?>
    <div class="nav-item<?= $ap === $item['id'] ? ' active' : '' ?>" onclick="location.href='<?= $item['href'] ?>'">
      <span class="nav-icon"><?= $item['icon'] ?></span> <?= $item['label'] ?>
    </div>
    <?php endforeach; ?>
  </nav>
  <div class="sidebar-footer">
    <div class="sidebar-user">
      <div class="avatar admin-av">AD</div>
      <div class="sidebar-user-info">
        <div class="sidebar-user-name">Admin User</div>
        <div class="sidebar-user-role">Super Administrator</div>
      </div>
      <button class="logout-btn" onclick="location.href='../actions/logout.php'" title="Logout">↪</button>
    </div>
  </div>
</aside>
<div class="overlay" id="overlay"></div>
