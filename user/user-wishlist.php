<?php
require_once('../includes/auth.php');
$pageTitle  = 'Wishlist';
$activePage = 'wishlist';
$topbarRole = 'user';
$footerSearchAction = "location.href='user-catalogue.php'";
$extraCss = <<<'CSS'
.under-construction{display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:60vh;text-align:center;padding:2rem;animation:fadeUp .5s both}
.uc-icon{font-size:5rem;margin-bottom:1.5rem;animation:float 3s ease-in-out infinite}
@keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-12px)}}
.uc-title{font-family:'Playfair Display',serif;font-size:2rem;font-weight:900;color:var(--text);margin-bottom:.6rem}
.uc-sub{font-size:1rem;color:var(--text-muted);max-width:440px;line-height:1.65;margin-bottom:2rem}
.uc-badge{display:inline-flex;align-items:center;gap:.5rem;padding:.45rem 1.1rem;background:var(--warning-light);color:var(--warning);border-radius:99px;font-size:.82rem;font-weight:700;margin-bottom:1.5rem}
.uc-dots{display:flex;gap:.45rem;margin-bottom:2.5rem}
.uc-dot{width:10px;height:10px;border-radius:50%;background:var(--border);animation:pulse-dot 1.4s ease-in-out infinite}
.uc-dot:nth-child(2){animation-delay:.2s}
.uc-dot:nth-child(3){animation-delay:.4s}
@keyframes pulse-dot{0%,80%,100%{background:var(--border);transform:scale(1)}40%{background:var(--accent);transform:scale(1.3)}}
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
    <div>
      <p class="page-eyebrow">My Library</p>
      <h1 class="page-title">Wishlist</h1>
    </div>
  </div>

  <div class="under-construction">
    <div class="uc-icon">🔖</div>
    <div class="uc-badge">🚧 Under Construction</div>
    <h2 class="uc-title">Coming Soon</h2>
    <p class="uc-sub">
      The wishlist feature is on its way. You'll be able to save books,
      get notified when they become available, and borrow them in one tap.
    </p>
    <div class="uc-dots">
      <div class="uc-dot"></div>
      <div class="uc-dot"></div>
      <div class="uc-dot"></div>
    </div>
    <a href="user-dashboard.php" class="btn btn-primary">← Back to Dashboard</a>
  </div>

</main>

<?php include '../includes/footer.php'; ?>
</body>
</html>