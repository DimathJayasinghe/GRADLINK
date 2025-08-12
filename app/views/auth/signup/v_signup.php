<?php require APPROOT . '/views/inc/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/auth/login_signup_styles.css">
<style>
    .auth-card {
        height: 650px;
    }

    .card-title {
        margin-top: 100px;
    }

    .signup {
        margin-top: 90px;
        font-size: 14px;
    }
</style>

<main class="page">
    <section class="brand">
        <div class="logo-mark">
            <img src="<?php echo URLROOT ?>/img/logo_white.png" alt="Gradlink mark" />
        </div>
        <h1 class="brand-title">GRADLINK</h1>
        <p class="brand-copy">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard</p>
    </section>

    <section class="auth-card">
        <button class="card-close" aria-label="Close" onclick="handleClose()">×</button>
        <h2 class="card-title">Signup</h2>
        <?php
        if (isset($data['errors']) && !empty($data['errors'])) {
            foreach ($data['errors'] as $error) {
                echo "<p class='error-message'>$error</p>";
            }
        }
        ?>

        <form class="form" method="GET" action="<?php echo URLROOT; ?>/auth/signup">
            <div class="slide slide_1">
                <label class="field">
                    <p>Select your Role</p>
                    <span class="sr-only">Dropdown</span>
                    <select name="role" class="input select" required>
                        <option value="" disabled selected>Choose your role</option>
                        <option value="undergrad">Undergraduate Student</option>
                        <option value="alumni">Alumni</option>
                    </select>
                    <span class="chevron">▾</span>
                </label>
            </div>
            
            <div class="actions" style="justify-content: end">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>

        <p class="signup">Already have an account? <a href="<?php echo URLROOT; ?>/auth/login" class="btn-link">Login</a></p>
        <!-- <p><span class="cardNo"></span></p> -->
    </section>
</main>

<script src="<?php echo URLROOT; ?>/js/signup.script.js"></script>
<?php require APPROOT . '/views/inc/footer.php'; ?>