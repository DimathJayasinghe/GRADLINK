<?php
// Privacy Policy - static page
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Privacy Policy | GRADLINK</title>
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
		document.title = 'Privacy Policy | GRADLINK';
	</script>
	<meta name="robots" content="noindex">
</head>
<body>
	<header class="header">
		<a href="<?php echo URLROOT; ?>/">← Back to Home</a>
	</header>

	<main class="page">
		<div class="breadcrumb">
			<a href="<?php echo URLROOT; ?>/">Home</a> / Privacy Policy
		</div>
		<div class="card">
			<h1>Privacy Policy</h1>
			<p>Your privacy matters to us. This policy explains what data we collect, how we use it, and your rights.</p>

			<h2 class="section-title">1. Information We Collect</h2>
			<ul>
				<li>Account data such as name, email, profile details.</li>
				<li>Content you share on the platform.</li>
				<li>Usage data like log-ins, device, and interaction information.</li>
			</ul>

			<h2 class="section-title">2. How We Use Information</h2>
			<ul>
				<li>To provide and improve GRADLINK services.</li>
				<li>To personalize content and facilitate connections.</li>
				<li>For security, fraud prevention, and service maintenance.</li>
			</ul>

			<h2 class="section-title">3. Sharing and Disclosure</h2>
			<p>We do not sell personal information. We may share data with service providers under strict agreements or when required by law.</p>

			<h2 class="section-title">4. Data Retention</h2>
			<p>We retain information for as long as your account is active or as needed to provide the Service, comply with obligations, or resolve disputes.</p>

			<h2 class="section-title">5. Your Rights</h2>
			<ul>
				<li>Access, correct, or delete your information.</li>
				<li>Update privacy settings in your account.</li>
				<li>Contact us for assistance at <a href="mailto:support@gradlink.example">support@gradlink.example</a>.</li>
			</ul>

			<h2 class="section-title">6. Changes</h2>
			<p>We may update this policy from time to time. We will notify you of material changes where appropriate.</p>

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
