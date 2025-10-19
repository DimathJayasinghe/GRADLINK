<?php
// Support - static page
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Support | GRADLINK</title>
	<link rel="icon" type="image/x-icon" href="<?php echo URLROOT?>/img/favicon_white.png" />
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
	<style>
		.page { max-width: 960px; margin: 0 auto; padding: 4rem 1.5rem 3rem; }
		.page h1 { font-size: 2rem; margin-bottom: .5rem; }
		.muted { color: var(--muted); }
		.faq { margin-top: 1.5rem; display: grid; gap: 1rem; }
		.faq-item { background: var(--card); border-radius: 8px; padding: 1.25rem 1.25rem; }
		.faq-item h3 { margin: 0 0 .5rem; font-size: 1.05rem; }
		.faq-item p { margin: 0; color: var(--muted); line-height: 1.7; }
		.header { position: sticky; top:0; z-index:10; padding: 1rem 1.5rem; background: rgba(15,21,24,.9); backdrop-filter: blur(5px); }
		.header a { color: var(--btn-text); text-decoration: none; font-weight: 600; background: var(--btn); padding: .45rem .8rem; border-radius: 6px; display: inline-flex; align-items: center; gap: .35rem; }
		.header a:hover { background: var(--link); transform: translateY(-1px); }
		.cta { margin-top: 2rem; background: var(--input); border-radius: 8px; padding: 1.25rem; display:flex; align-items:center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
		.btn { padding: 0.6rem 1.2rem; border: none; border-radius: 4px; font-family: 'Poppins', sans-serif; font-weight: 500; cursor: pointer; transition: var(--transition); }
		.btn-primary { background-color: var(--btn); color: var(--btn-text); }
		.btn-primary:hover { background-color: var(--link); transform: translateY(-2px); }
		.footer { background: var(--bg); padding: 2rem 1.5rem; text-align:center; margin-top: 3rem; }
		.footer-links { display:flex; flex-wrap: wrap; gap: 1rem 1.5rem; justify-content:center; }
		.footer-links a { color: var(--muted); text-decoration:none; }
		.footer-links a:hover { color: var(--link); }
			/* Content link styling */
			.page a { color: var(--link); text-decoration: none; }
			.page a:hover { text-decoration: underline; }
	</style>
	<script>document.title = 'Support | GRADLINK';</script>
	<meta name="robots" content="noindex">
</head>
<body>
	<header class="header">
		<a href="<?php echo URLROOT; ?>/">← Back to Home</a>
	</header>

	<main class="page">
		<h1>Support</h1>
		<p class="muted">Find answers to common questions or reach out to our team.</p>

		<section class="faq">
			<div class="faq-item">
				<h3>How do I create an account?</h3>
				<p>Go to the <a href="<?php echo URLROOT; ?>/auth">Auth</a> page and select Sign Up. Complete the form and verify your email if required.</p>
			</div>
			<div class="faq-item">
				<h3>Forgot password—what now?</h3>
				<p>On the login page, click “Forgot password” and follow the instructions to reset it via email.</p>
			</div>
			<div class="faq-item">
				<h3>How can I report inappropriate content?</h3>
				<p>Use the in-app report option where available or contact us with details. We take reports seriously and act promptly.</p>
			</div>
			<div class="faq-item">
				<h3>Can I delete my account?</h3>
				<p>Yes. Visit Settings → Account and choose “Delete Account.” Note that deletion is permanent.</p>
			</div>
		</section>

		<div class="cta">
			<div>
				<strong>Still need help?</strong>
				<div class="muted">Reach out to our support team and we'll get back to you.</div>
			</div>
			<a class="btn btn-primary" href="<?php echo URLROOT; ?>/Hero/contactus">Contact Support</a>
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
