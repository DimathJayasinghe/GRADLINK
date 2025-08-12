<?php require APPROOT . '/views/inc/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/auth/login_styles.css">
<main class="page">
    <section class="brand">
        <div class="logo-mark">
            <img src="<?php echo URLROOT ?>/img/logo_white.png" alt="Gradlink mark" />
        </div>
        <h1 class="brand-title">GRADLINK</h1>
        <p class="brand-copy">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard</p>
    </section>

    <section class="auth-card">
        <button class="card-close" aria-label="Close">×</button>
        <h2 class="card-title">Login</h2>
        <?php
        if (isset($data['errors']) && !empty($data['errors'])) {
            foreach ($data['errors'] as $error) {
                echo "<p class='error-message'>$error</p>";
            }
        }
        ?>
        <form class="form" method="post" action="<?php echo URLROOT; ?>/auth/login">
            <label class="field">
                <span class="sr-only">Select Your Role</span>
                <select name="role" class="input select" required>
                    <option value="" disabled selected>Select Your Role</option>
                    <option value="undergrad">Undergrad</option>
                    <option value="alumni">Alumni</option>
                </select>
                <span class="chevron">▾</span>
            </label>

            <label class="field">
                <span class="sr-only">Email</span>
                <input type="email" name="email" class="input" placeholder="Email" required />
            </label>

            <label class="field">
                <span class="sr-only">Password</span>
                <input type="password" name="password" class="input" placeholder="Password" required />
            </label>

            <div class="actions">
                <button class="btn btn-primary" type="submit">Login</button>
            </div>
        </form>

        <p class="signup">Don't have an account? <a href="<?php echo URLROOT; ?>/users/signup">Signup</a></p>
    </section>
</main>

<?php require APPROOT . '/views/inc/footer.php'; ?>