<?php require APPROOT . '/views/inc/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/auth/login_shared.css">

<div class="auth-container single-page">
    <section class="auth-section brand-section">
        <div class="logo-mark">
            <img src="<?php echo URLROOT ?>/img/logo_white.png" alt="Gradlink logo" />
        </div>
        <h1 class="brand-title">GRADLINK</h1>
        <p class="brand-copy">Reconnect with your UCSC community, mentor the next generation, and expand your professional network with fellow alumni.</p>
    </section>

    <section class="auth-section">
        <a href="<?php echo URLROOT;?>/auth" class="back-button" aria-label="Back to role selection">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
            Back
        </a>
        
        <div class="form-wrapper">
            <h2 class="card-title">Alumni Login</h2>
            
            <?php
            if (isset($data['errors']) && !empty($data['errors'])) {
                foreach ($data['errors'] as $error) {
                    echo "<p class='error-message'>$error</p>";
                }
            }
            ?>
            
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
                    <button class="btn" type="submit">Login</button>
                </div>
                
                <p class="signup-link">Don't have an account? <a href="<?php echo URLROOT; ?>/signup/alumni">Sign Up</a></p>
            </form>
        </div>
    </section>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>