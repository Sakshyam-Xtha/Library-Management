<?php
session_start();
$pageTitle  = 'Terms & Conditions';
$activePage = 'terms'; // Set this to a valid page or empty
$topbarRole = 'user'; // Change to 'admin' if accessed from admin side
$footerSearchAction = "location.href='user-catalogue.php'";

$extraCss = <<<'CSS'
.legal-hero{background:linear-gradient(135deg,var(--accent) 0%,#1e3a7a 100%);border-radius:var(--radius-lg);padding:2.5rem;margin-bottom:2rem;position:relative;overflow:hidden;animation:fadeUp .5s both}
.legal-hero::before{content:'';position:absolute;inset:0;background:url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Ccircle cx='20' cy='20' r='1.5'/%3E%3C/g%3E%3C/svg%3E")}
.legal-eyebrow{font-size:.8rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.6);margin-bottom:.5rem;position:relative;z-index:1}
.legal-title{font-family:'Playfair Display',serif;font-size:2.2rem;font-weight:900;color:#fff;position:relative;z-index:1}
.legal-layout{display:grid;grid-template-columns:280px 1fr;gap:2rem;align-items:start}
.toc-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;position:sticky;top:5rem}
.toc-title{font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text-muted);margin-bottom:1rem;display:block}
.toc-list{list-style:none;padding:0;margin:0}
.toc-list li{margin-bottom:.25rem}
.toc-list a{display:block;padding:.6rem .85rem;border-radius:8px;font-size:.9rem;color:var(--text-muted);text-decoration:none;transition:all .2s}
.toc-list a:hover{background:var(--surface-2);color:var(--accent)}
.toc-list a.active{background:var(--accent-light);color:var(--accent);font-weight:600}
.legal-body{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:3rem;animation:fadeUp .6s both}
.legal-section{margin-bottom:3rem;scroll-margin-top:6rem}
.legal-section:last-child{margin-bottom:0}
.section-num{display:inline-block;padding:.2rem .6rem;background:var(--accent-light);color:var(--accent);border-radius:6px;font-size:.75rem;font-weight:700;margin-bottom:1rem}
.legal-section h2{font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:800;color:var(--text);margin-bottom:1.25rem}
.legal-section p, .legal-section li{color:var(--text-muted);line-height:1.75;font-size:.95rem;margin-bottom:1rem}
.legal-section strong{color:var(--text)}
.legal-footer-card{margin-top:2rem;background:var(--surface-2);border-radius:var(--radius-lg);padding:2rem;text-align:center;border:1px dashed var(--border)}
@media (max-width:992px){.legal-layout{grid-template-columns:1fr}.toc-card{display:none}}
CSS;

include 'includes/head.php';
?>
<body data-theme="<?php echo $_SESSION['theme'] ?? 'light'; ?>">

<?php 
// Dynamically include sidebar based on role
if ($topbarRole === 'admin') {
    include 'includes/admin-sidebar.php'; 
} else {
    include 'includes/user-sidebar.php';
}
?>

<div class="main">
    <?php include 'includes/topbar.php'; ?>

    <main class="content">
        <div class="legal-hero">
            <p class="legal-eyebrow">Legal Framework</p>
            <h1 class="legal-title">Terms & Conditions</h1>
            <p style="color:rgba(255,255,255,.7);margin-top:1rem;font-size:.95rem">Please read these rules carefully. They govern your use of the Library Management System and your responsibilities as a member.</p>
        </div>

        <div class="legal-layout">
            <aside class="toc-card">
                <span class="toc-title">On this page</span>
                <ul class="toc-list">
                    <li><a href="#acceptance" class="active">Acceptance of Terms</a></li>
                    <li><a href="#eligibility">Membership Eligibility</a></li>
                    <li><a href="#borrowing">Borrowing Rules</a></li>
                    <li><a href="#fines">Fines & Penalties</a></li>
                    <li><a href="#conduct">Member Conduct</a></li>
                    <li><a href="#termination">Account Termination</a></li>
                </ul>
            </aside>

            <div class="legal-body">
                <section id="acceptance" class="legal-section">
                    <span class="section-num">01</span>
                    <h2>Acceptance of Terms</h2>
                    <p>By accessing the Library Management System (LMS), registering an account, or applying for membership, you agree to be bound by these Terms and Conditions and all applicable laws and regulations.</p>
                    <p>If you do not agree with any of these terms, you are prohibited from using or accessing this system. The materials contained in this website are protected by applicable copyright and trademark law.</p>
                </section>

                <section id="eligibility" class="legal-section">
                    <span class="section-num">02</span>
                    <h2>Membership Eligibility</h2>
                    <p>To become a member of the Library, you must be a registered <strong>Student</strong>, <strong>Faculty Member</strong>, or <strong>Staff</strong> of the institution. External memberships may be granted at the discretion of the Library Administration.</p>
                    <p>You agree to provide accurate, current, and complete information during the registration process and to update such information to keep it accurate and complete.</p>
                </section>

                <section id="borrowing" class="legal-section">
                    <span class="section-num">03</span>
                    <h2>Borrowing Rules</h2>
                    <p>Your membership entitles you to borrow library materials under the following conditions:</p>
                    <ul>
                        <li><strong>Loan Period:</strong> Standard loan periods vary by item type. It is your responsibility to check the <strong>Due Date</strong> at the time of borrowing.</li>
                        <li><strong>Renewals:</strong> Items may be renewed once, provided no other member has placed a hold on the item.</li>
                        <li><strong>Care of Items:</strong> You are responsible for maintaining items in the condition they were borrowed. Do not write in, highlight, or damage library property.</li>
                    </ul>
                </section>

                <section id="fines" class="legal-section">
                    <span class="section-num">04</span>
                    <h2>Fines & Penalties</h2>
                    <p>To ensure fair access for all members, the library enforces a strict overdue policy:</p>
                    <div style="background:var(--danger-light); border-radius:12px; padding:1.25rem; border-left:4px solid var(--danger); margin:1rem 0">
                        <p style="color:var(--danger); font-weight:700; margin-bottom:0.5rem">Late Return Policy</p>
                        <p style="margin:0; font-size:.9rem">A fine of <strong>Rs. 10.00 per day</strong> is applied to each overdue item. Borrowing privileges are suspended once unpaid fines exceed Rs. 500.00.</p>
                    </div>
                    <p>In the event of lost or significantly damaged items, the member will be charged the <strong>full replacement cost</strong> plus a processing fee.</p>
                </section>

                <section id="conduct" class="legal-section">
                    <span class="section-num">05</span>
                    <h2>Member Conduct</h2>
                    <p>Members must respect the library environment and other users. Disruptive behavior, misuse of library technology, or unauthorized removal of library materials will result in disciplinary action.</p>
                </section>

                <section id="termination" class="legal-section">
                    <span class="section-num">06</span>
                    <h2>Account Termination</h2>
                    <p>We may terminate or suspend your access to the LMS immediately, without prior notice or liability, for any reason whatsoever, including without limitation if you breach the Terms.</p>
                </section>
            </div>
        </div>

        <div class="legal-footer-card">
            <p style="margin-bottom:1.5rem">By using the Library Management System, you acknowledge that you have read and agree to these Terms.</p>
            <div style="display:flex; gap:1rem; justify-content:center">
                <a href="privacy.php" class="btn btn-outline">🔒 Privacy Policy</a>
                <a href="user-dashboard.php" class="btn btn-primary">← Back to Dashboard</a>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</div>

<script>
/* TOC Scroll Spy */
const sections = document.querySelectorAll('.legal-section');
const navLinks = document.querySelectorAll('.toc-list a');

window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.clientHeight;
        if (pageYOffset >= (sectionTop - 150)) {
            current = section.getAttribute('id');
        }
    });

    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href').includes(current)) {
            link.classList.add('active');
        }
    });
});
</script>
</body>
</html>