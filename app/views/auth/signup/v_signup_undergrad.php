<?php require APPROOT . '/views/inc/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/auth/login_signup_styles.css">
<style>
    .auth-card {
        height: 650px;
    }

    .auth-card {
        overflow: auto;
        /* allow scroll */
        scrollbar-width: none;
        /* Firefox */
    }

    .auth-card::-webkit-scrollbar {
        display: none;
        /* Chrome, Safari, Edge */
    }

    .card-title {
        margin-top: 60px;
    }

    .signup {
        margin-top: 30px;
        font-size: 14px;
    }
    .card-number-container{
        font-weight: bold;
        color: var(--color-primary);
        margin-bottom: 20px;
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
        <h2 class="card-title">Signup - Undergraduate</h2>

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
            <input type="hidden" name="role" value="undergrad">

            <!-- Slide 1: Basic Info -->
            <div class="slide slide_undergrad_1">
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
            </div>

            <!-- Slide 2: Academic Info -->
            <div class="slide slide_undergrad_2">
                <label class="field">
                    <span class="sr-only">Student ID</span>
                    <input type="text" name="student_id" class="input" placeholder="Student ID"
                        value="<?php echo isset($data['student_id']) ? htmlspecialchars($data['student_id']) : ''; ?>" required>
                </label>

                <label class="field">
                    <span class="sr-only">Batch No</span>
                    <input type="text" name="batch_no" class="input" placeholder="Batch No"
                        value="<?php echo isset($data['batch_no']) ? htmlspecialchars($data['batch_no']) : ''; ?>" required>
                </label>

                <label class="field">
                    <span class="sr-only">NIC</span>
                    <input type="text" name="nic" class="input" placeholder="National ID"
                        value="<?php echo isset($data['nic']) ? htmlspecialchars($data['nic']) : ''; ?>" required>
                </label>

                <label class="field">
                    <span class="sr-only">Display Name</span>
                    <input type="text" name="display_name" class="input" placeholder="Display Name"
                        value="<?php echo isset($data['display_name']) ? htmlspecialchars($data['display_name']) : ''; ?>" required>
                </label>
            </div>

            <!-- Slide 3: Profile Picture -->
            <div class="slide slide_undergrad_3">
                <label class="field">
                    <p>Upload a profile picture:</p>
                    <input type="file" id="profilePic" name="profilePic" class="input" accept="image/*" required>
                </label>
            </div>

            <div class="actions">
                <button type="button" class="btn btn-secondary btn-hidden" data-btn="previous" onclick="handlePrevious()">Previous</button>
                <button type="button" class="btn btn-primary" data-btn="next" onclick="handleNext()">Next</button>
                <button type="submit" class="btn btn-success btn-hidden" data-btn="submit">Complete Signup</button>
            </div>
            <p class="card-number-container"><span class="cardNo">1</span>/3</p>
        </form>

        <p class="signup">Already have an account? <a href="<?php echo URLROOT; ?>/auth/login" class="btn-link">Login</a></p>
    </section>
</main>

<script src="<?php echo URLROOT; ?>/js/signup.script.js"></script>
<?php require APPROOT . '/views/inc/footer.php'; ?>