<?php require APPROOT . '/views/inc/header.php';?>
<!-- TOP NAVIGATION -->
<?php require APPROOT . '\views\inc\commponents\topnavbar.php';?>

<h1>users Login</h1>
<div class="form-container">
    <b>Fill the credentials to login</b><hr>
    
    <!-- Display errors if any -->
    <?php if(!empty($data['errors'])): ?>
        <div style="color: red; margin: 10px 0;">
            <?php foreach($data['errors'] as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form action="<?php echo URLROOT; ?>/users/loginSubmit" method="POST">
        <!-- Email -->
        <label for="email">Email</label><br>
        <input type="email" name="email" id="email" placeholder="Email" value="<?php echo isset($data['email']) ? htmlspecialchars($data['email']) : ''; ?>" required>
        <span class="form-invalid hide">Error</span>

        <!-- Password -->
         <br><br>
        <label for="password">Password</label><br>
        <input type="password" name="password" id="password" placeholder="Password" required>
        <span class="form-invalid hide">Error</span>

        <!-- Remember Me -->
        <br><br>
        <label>
            <input type="checkbox" name="remember_me" value="1"> Remember Me
        </label>

        <br><hr>
        <!-- Submit Button -->
         <br>
        <input type="submit" value="login"><br>
        <p>Don't have an account? <a href="<?php echo URLROOT; ?>/users/signup">Sign Up</a></p>
    </form>
</div>
<?php require APPROOT . '/views/inc/footer.php';?>