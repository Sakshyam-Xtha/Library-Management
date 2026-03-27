<?php
/**
 * includes/head.php
 * Usage: <?php $pageTitle = "Dashboard"; include 'includes/head.php'; ?>
 * Optional: $extraCss — inline <style> block content (without <style> tags)
 */
?>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?= htmlspecialchars($pageTitle ?? 'LMS') ?> — LMS</title>
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="../assets/css/lms-shared.css" />
<?php if (!empty($extraCss)): ?>
<style>
<?= $extraCss ?>
</style>
<?php endif; ?>
