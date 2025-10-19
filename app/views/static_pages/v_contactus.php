<?php
// Contact Us - static page
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Contact Us | GRADLINK</title>
	<link rel="icon" type="image/x-icon" href="<?php echo URLROOT?>/img/favicon_white.png" />
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
	<style>
		.page { max-width: 960px; margin: 0 auto; padding: 4rem 1.5rem 3rem; }
		.grid { display: grid; grid-template-columns: 1.2fr .8fr; gap: 1.5rem; align-items: start; }
		.grid > * { min-width: 0; }
		.card { background: var(--card); border-radius: 8px; padding: 1.5rem; border: 1px solid rgba(255,255,255,0.06); }
		.card form { display: grid; gap: .75rem; }
		label { display:block; margin: .25rem 0 .25rem; font-weight: 500; }
		input, textarea { width:100%; max-width: 100%; box-sizing: border-box; background: var(--input); border: 1px solid rgba(255,255,255,0.08); border-radius: 8px; padding: .8rem .95rem; color: var(--text); font-family: 'Poppins', sans-serif; transition: border-color .2s ease, box-shadow .2s ease, transform .1s ease; }
		input::placeholder, textarea::placeholder { color: var(--muted); }
		input:hover, textarea:hover { border-color: rgba(255,255,255,0.16); }
		input:focus, textarea:focus { outline: none; border-color: var(--link); box-shadow: 0 0 0 3px rgba(158, 212, 220, 0.18); }
		textarea{ min-height: 148px; resize: vertical; }
		.btn { margin-top: .75rem; padding: 0.78rem 1.2rem; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; background: var(--btn); color: var(--btn-text); transition: background .2s ease, transform .1s ease, box-shadow .2s ease; min-height: 44px; }
		.btn:hover { background: var(--link); transform: translateY(-1px); box-shadow: 0 8px 20px rgba(0,0,0,.25); }
		.muted { color: var(--muted); }
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
		@media (max-width: 900px){ .grid{ grid-template-columns: 1fr; } }
		@media (max-width: 480px){ .btn{ width: 100%; } }
	</style>
	<script>document.title = 'Contact Us | GRADLINK';</script>
	<meta name="robots" content="noindex">
</head>
<body>
	<header class="header">
		<a href="<?php echo URLROOT; ?>/">← Back to Home</a>
	</header>

	<main class="page">
		<h1>Contact Us</h1>
		<p class="muted">Questions, feedback, or issues? Send us a message and we’ll respond shortly.</p>

		<section class="grid">
			<div class="card">
				<form method="post" action="#" onsubmit="event.preventDefault(); alert('Thanks! This demo form does not submit to a backend yet.');">
					<label for="name">Full Name</label>
					<input id="name" name="name" type="text" placeholder="Your name" required>

					<label for="email">Email</label>
					<input id="email" name="email" type="email" placeholder="you@example.com" required>

					<label for="subject">Subject</label>
					<input id="subject" name="subject" type="text" placeholder="How can we help?" required>

					<label for="message">Message</label>
					<textarea id="message" name="message" placeholder="Write your message..." required></textarea>

					<button class="btn" type="submit">Send Message</button>
				</form>
			</div>

			<aside class="card">
				<h3>Support Details</h3>
				<p class="muted">Email: <a href="mailto:support@gradlink.example">support@gradlink.example</a></p>
				<p class="muted">We aim to respond within 2–3 business days.</p>
				<hr style="border-color: rgba(255,255,255,0.06); margin: 1rem 0;">
				<p class="muted">Looking for quick help? Visit the <a href="<?php echo URLROOT; ?>/Hero/support">Support</a> page for FAQs.</p>
			</aside>
		</section>
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
