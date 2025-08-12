<?php require APPROOT . '/views/inc/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/auth/login_signup_styles.css">

<main class="page">
    <section class="brand">
        <div class="logo-mark">
            <img src="<?php echo URLROOT ?>/img/logo_white.png" alt="Gradlink mark" />
        </div>
        <h1 class="brand-title">GRADLINK</h1>
        <p class="brand-copy">Connect with your university community and fellow graduates.</p>
    </section>

    <section class="auth-card">
        <button class="card-close" aria-label="Close" onclick="handleClose()">×</button>
        <h2 class="card-title">Alumni Signup</h2>
        
        <?php
        if (isset($data['errors']) && !empty($data['errors'])) {
            echo '<div class="alert">';
            foreach ($data['errors'] as $error) {
                echo "<p class='error-message'>$error</p>";
            }
            echo '</div>';
        }
        ?>

        <form class="form" method="post" action="<?php echo URLROOT; ?>/auth/signup">
            <input type="hidden" name="role" value="alumni">

            <label class="field">
                <span class="sr-only">Full Name</span>
                <input type="text" name="full_name" class="input" placeholder="Full Name" 
                       value="<?php echo isset($data['full_name']) ? htmlspecialchars($data['full_name']) : ''; ?>" required>
            </label>

            <label class="field">
                <span class="sr-only">Email</span>
                <input type="email" name="email" class="input" placeholder="Email" 
                       value="<?php echo isset($data['email']) ? htmlspecialchars($data['email']) : ''; ?>" required>
            </label>

            <label class="field">
                <span class="sr-only">Password</span>
                <input type="password" name="password" class="input" placeholder="Password" required>
            </label>

            <label class="field">
                <span class="sr-only">Confirm Password</span>
                <input type="password" name="confirm_password" class="input" placeholder="Confirm Password" required>
            </label>

            <label class="field">
                <span class="sr-only">Graduation Year</span>
                <input type="number" name="graduation_year" class="input" placeholder="Graduation Year" 
                       value="<?php echo isset($data['graduation_year']) ? htmlspecialchars($data['graduation_year']) : ''; ?>" 
                       min="1950" max="2030" required>
            </label>

            <div class="actions">
                <button type="submit" class="btn btn-success btn-lg">Complete Alumni Signup</button>
            </div>
        </form>

        <p class="signup">Already have an account? <a href="<?php echo URLROOT; ?>/auth/login" class="btn-link">Login</a></p>
    </section>
</main>

<script src="<?php echo URLROOT; ?>/js/signup.script.js"></script>
<?php require APPROOT . '/views/inc/footer.php'; ?>