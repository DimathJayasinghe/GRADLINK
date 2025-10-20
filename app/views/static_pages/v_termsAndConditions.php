<?php
// Terms and Conditions - static page
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Terms & Conditions | GRADLINK</title>
	<link rel="icon" type="image/x-icon" href="<?php echo URLROOT?>/img/favicon_white.png" />
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
	<style>
		.page { max-width: 960px; margin: 0 auto; padding: 4rem 1.5rem 3rem; }
		.page h1 { font-size: 2rem; margin-bottom: 1rem; }
		.page p, .page li { color: var(--muted); line-height: 1.8; }
		.card { background: var(--card); border-radius: 8px; padding: 2rem; }
		.breadcrumb { margin-bottom: 1rem; font-size: .95rem; color: var(--muted); }
		.breadcrumb a { color: var(--link); text-decoration: none; }
		.section-title { margin-top: 1.5rem; font-size: 1.25rem; }
		.header { position: sticky; top:0; z-index:10; padding: 1rem 1.5rem; background: rgba(15,21,24,.9); backdrop-filter: blur(5px); }
		.header a { color: var(--btn-text); text-decoration: none; font-weight: 600; background: var(--btn); padding: .45rem .8rem; border-radius: 6px; display: inline-flex; align-items: center; gap: .35rem; }
		.header a:hover { background: var(--link); transform: translateY(-1px); }
		.footer { background: var(--bg); padding: 2rem 1.5rem; text-align:center; margin-top: 3rem; }
		.footer-links { display:flex; flex-wrap: wrap; gap: 1rem 1.5rem; justify-content:center; }
		.footer-links a { color: var(--muted); text-decoration:none; }
		.footer-links a:hover { color: var(--link); }
			/* Content link styling */
			.page a { color: var(--link); text-decoration: none; }
			.page a:hover { text-decoration: underline; }
	</style>
	<script>
		document.title = 'Terms & Conditions | GRADLINK';
	</script>
	<meta name="robots" content="noindex">
</head>
<body>
	<header class="header">
		<a href="<?php echo URLROOT; ?>/">← Back to Home</a>
	</header>

	<main class="page">
		<div class="breadcrumb">
			<a href="<?php echo URLROOT; ?>/">Home</a> / Terms & Conditions
		</div>
		<div class="card">
			<h1>Terms & Conditions</h1>
			<p>Welcome to GRADLINK. By accessing or using our platform, you agree to the following terms. Please read them carefully.</p>

			<h2 class="section-title">1. Acceptance of Terms</h2>
			<p>By creating an account or using GRADLINK, you accept these Terms and our Privacy Policy. If you do not agree, do not use the Service.</p>

			<h2 class="section-title">2. Eligibility</h2>
			<p>You must be a university student, alumni, or authorized community member to use the Service. You are responsible for maintaining the confidentiality of your account.</p>

			<h2 class="section-title">3. User Conduct</h2>
			<ul>
				<li>Do not post unlawful, harassing, or misleading content.</li>
				<li>Respect others' privacy and intellectual property.</li>
				<li>Do not attempt to disrupt or misuse the platform.</li>
			</ul>

			<h2 class="section-title">4. Content Ownership</h2>
			<p>You retain ownership of content you post; by posting, you grant GRADLINK a non-exclusive license to display and distribute such content within the platform.</p>

			<h2 class="section-title">5. Termination</h2>
			<p>We may suspend or terminate access if Terms are violated or for other legitimate reasons.</p>

			<h2 class="section-title">6. Disclaimers</h2>
			<p>The Service is provided "as is" without warranties of any kind. We do not guarantee accuracy or availability at all times.</p>

			<h2 class="section-title">7. Changes to Terms</h2>
			<p>We may update these Terms from time to time. We will notify users of material changes where appropriate.</p>

			<p style="margin-top:1rem; font-size:.95rem; color:var(--muted)">Last updated: <?php echo date('F j, Y'); ?></p>
		</div>
	</main>

	<footer class="footer">
		<div class="footer-links">
			<a href="<?php echo URLROOT; ?>/Hero/termsofservice">Terms of Service</a>
			<a href="<?php echo URLROOT; ?>/Hero/privacypolicy">Privacy Policy</a>
			<a href="<?php echo URLROOT; ?>/Hero/support">Support</a>
			<a href="<?php echo URLROOT; ?>/Hero/contactus">Contact Us</a>
		</div>
		<p class="copyright">&copy; <?php echo date('Y'); ?> GRADLINK. All rights reserved.</p>
	</footer>
</body>
</html>
