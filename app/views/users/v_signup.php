<?php require APPROOT . '/views/inc/header.php';?>
<!-- TOP NAVIGATION -->

<h1>users signup</h1>
<div class="form-container">
    <form action="#" method="POST">
        <!-- Name -->
        <label for="name">Name</label><br>
        <input type="text" name="name" id="name" placeholder="Name" required><br>
        <span class="form-invalid"></span>


        <!-- Email -->
        <label for="email">Email</label><br>
        <input type="email" name="email" id="email" placeholder="Email" required><br>
        <span class="form-invalid"></span>

        <!-- Password -->
        <label for="password">Password</label><br>
        <input type="password" name="password" id="password" placeholder="Password" required><br>
        <span class="form-invalid"></span>

        <!-- Confirm Password -->
        <label for="confirm_password">Confirm Password</label><br>
        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required><br>
        <span class="form-invalid"></span>

        <br><hr>
        <!-- Submit Button -->
        <input type="submit" value="Sign Up"><br>
        <p>Already have an account? <a href="<?php echo URLROOT; ?>/users/signin">Sign In</a></p>
        <span class="form-invalid"></span>
    </form>
</div>
<?php require APPROOT . '/views/inc/footer.php';?>