<?php require APPROOT . '/views/inc/header.php';?>
<!-- TOP NAVIGATION -->
<?php require APPROOT . '\views\inc\commponents\topnavbar.php';?>

<h1>users signup</h1>
<div class="form-container">
    <b>Fill the form</b><hr>
    
    <!-- Display errors if any -->
    <?php if(!empty($data['errors'])): ?>
        <div class="form-invalid" style="display: block; color: red; margin: 10px 0; background: #ffe6e6; padding: 10px; border-radius: 5px;">
            <?php foreach($data['errors'] as $error): ?>
                <p style="margin: 5px 0;"><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <!-- Signup Form -->  
    <form action="<?php echo URLROOT; ?>/users/signupSubmit" method="POST">
        <!-- Name -->
        <label for="name">Name</label><br>
        <input type="text" name="name" id="name" placeholder="Name" value="<?php echo isset($data['name']) ? htmlspecialchars($data['name']) : ''; ?>" required>

        <!-- Email -->
        <br><br>
        <label for="email">Email</label><br>
        <input type="email" name="email" id="email" placeholder="Email" value="<?php echo isset($data['email']) ? htmlspecialchars($data['email']) : ''; ?>" required>

        <!-- Password -->
         <br><br>
        <label for="password">Password</label><br>
        <input type="password" name="password" id="password" placeholder="Password" required>

        <!-- Confirm Password -->
        <br><br>
        <label for="confirm_password">Confirm Password</label><br>
        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>

        <br><hr>
        <!-- Submit Button -->
         <br>
        <input type="submit" value="signUp"><br>
        <p>Already have an account? <a href="<?php echo URLROOT; ?>/users/login">Login</a>
    </form>
</div>
<?php require APPROOT . '/views/inc/footer.php';?>