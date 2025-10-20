<?php require APPROOT . '/views/inc/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/auth/login_signup_styles.css">

<main class="page">
    <section class="brand">
        <div class="logo-mark">
            <img src="<?php echo URLROOT ?>/img/logo_white.png" alt="Gradlink logo" />
        </div>
        <h1 class="brand-title">GRADLINK</h1>
        <p class="brand-copy">Reconnect with your UCSC community, mentor the next generation, and expand your professional network with fellow alumni.</p>
    </section>

    <section class="auth-card">
        <a href="<?php echo URLROOT;?>/auth"><button class="card-close" aria-label="Close">×</button></a>
        <h2 class="card-title">Alumni Login</h2>

        <?php
        if (isset($data['errors']) && !empty($data['errors'])) {
            foreach ($data['errors'] as $error) {
                echo "<p class='error-message'>$error</p>";
            }
        }
        ?>

        <?php if (!empty($data['pending_status'])): ?>
            <div class="status-popup" role="dialog" aria-live="assertive">
                <div class="popup-card">
                    <h3>Approval Pending</h3>
                    <p>Your alumni signup request is still under review. You’ll be able to log in once an admin approves your account.</p>
                    <a href="<?php echo URLROOT; ?>/auth" class="popup-btn">Back to Auth</a>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($data['rejected_status'])): ?>
            <div class="status-popup" role="dialog" aria-live="assertive">
                <div class="popup-card">
                    <h3>Request Rejected</h3>
                    <p>Sorry, your alumni signup request was rejected. If you believe this is a mistake, please contact support or resubmit.</p>
                    <a href="<?php echo URLROOT; ?>/signup/alumni" class="popup-btn secondary">Resubmit</a>
                    <a href="<?php echo URLROOT; ?>/auth" class="popup-btn">Back to Auth</a>
                </div>
            </div>
        <?php endif; ?>

        <form class="form" method="post" action="<?php echo URLROOT; ?>/login/alumni">
            <label class="field">
                <span class="sr-only">Email</span>
                <input type="email" name="email" class="input"
                       value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>"
                       placeholder="Email" required />
            </label>

            <label class="field">
                <span class="sr-only">Password</span>
                <input type="password" name="password" class="input" placeholder="Password" required />
            </label>

            <div class="actions">
                <button class="btn btn-primary" type="submit">Login</button>
            </div>
        </form>

        <p class="signup">Don't have an account? <a href="<?php echo URLROOT; ?>/signup/alumni">Sign Up</a></p>
    </section>
</main>

<?php require APPROOT . '/views/inc/footer.php'; ?>

<style>
.status-popup { position: fixed; inset: 0; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center; z-index: 1000; }
.status-popup .popup-card { background: var(--card); color: var(--text); padding: 22px; border-radius: 10px; max-width: 520px; width: 92%; text-align: center; box-shadow: 0 6px 20px rgba(0,0,0,0.35); }
.status-popup h3 { margin-bottom: 8px; }
.status-popup p { color: var(--muted); margin: 8px 0 12px; }
.status-popup .popup-btn { display: inline-block; margin: 6px 6px 0; padding: 10px 14px; background: var(--btn); color: var(--btn-text); text-decoration: none; border-radius: 6px; }
.status-popup .popup-btn.secondary { background: transparent; border: 1px solid var(--border); color: var(--text); }
</style>