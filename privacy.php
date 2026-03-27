<?php
require_once('includes/auth.php'); // Ensure user is logged in if needed
$pageTitle = 'Privacy Policy';
include 'includes/head.php';
?>
<body data-theme="<?php echo $currentTheme; ?>">
    <div class="main" style="margin-left: 0; padding: 2rem;">
        <div class="table-card" style="max-width: 850px; margin: 0 auto; padding: 3rem; line-height: 1.7;">
            <h1 style="font-family: 'Playfair Display', serif; color: var(--text); margin-bottom: 0.5rem;">Privacy Policy</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 2rem;">Last Updated: March 2026</p>
            
            <section style="margin-bottom: 2rem;">
                <h3 style="color: var(--accent); margin-bottom: 0.75rem;">1. Data Collection</h3>
                <p>To provide library services, we collect personal information including your full name, email address, and institutional role (Student/Faculty). We also track your borrowing history, including book titles, due dates, and any accumulated fines.</p>
            </section>

            <section style="margin-bottom: 2rem;">
                <h3 style="color: var(--accent); margin-bottom: 0.75rem;">2. Use of Information</h3>
                <p>Your data is used strictly for library operations: managing book circulation, notifying you of overdue items via email, and maintaining institutional records. We do not sell or share your data with third-party advertisers.</p>
            </section>

            <section style="margin-bottom: 2rem;">
                <h3 style="color: var(--accent); margin-bottom: 0.75rem;">3. Data Retention & Deletion</h3>
                <p>Borrowing records are kept as long as the account is active. Upon account deletion, personal identifiers are removed from our active directory. However, transaction logs may be archived for institutional audit purposes.</p>
            </section>

            <section style="margin-bottom: 2rem;">
                <h3 style="color: var(--accent); margin-bottom: 0.75rem;">4. Security Measures</h3>
                <p>We use industry-standard encryption for passwords and secure server protocols to protect your data. Access to the full member directory is restricted to authorized Administrators only.</p>
            </section>

            <div style="margin-top: 3rem; text-align: center;">
                <button class="btn btn-primary" onclick="window.history.back()">Go Back</button>
            </div>
        </div>
    </div>
</body>