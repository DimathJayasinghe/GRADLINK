<?php require APPROOT . '/views/inc/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/auth/login_signup_styles.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin.css">

<main class="page">
    <section class="brand">
        <div class="logo-mark">
            <img src="<?php echo URLROOT ?>/img/logo_white.png" alt="Gradlink mark" />
        </div>
        <h1 class="brand-title">GRADLINK</h1>
        <div style="display: flex; align-items: center; justify-content: center;">
            <div class="admin-badge">ADMIN PANEL</div>
        </div>
    </section>

    <section class="auth-card">
        <a href="<?php echo URLROOT;?>"><button class="card-close" aria-label="Close">×</button></a>
        <h2 class="card-title">ADMINISTRATIVE LOGIN</h2>

        <!-- Flash Messages start-->
            <?php 
            $flashMessages = SessionManager::getFlash();
            if (!empty($flashMessages)): ?>
                <div class="flash-messages">
                    <?php foreach ($flashMessages as $message): ?>
                        <div class="flash-message <?php echo $message['type']; ?>">
                            <?php echo htmlspecialchars($message['message']); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <!-- Flash Messages end-->

        <!-- Admin Login Form start-->
            <form  class="form" method="POST" action="<?php echo URLROOT; ?>/adminlogin">
                <label class="field">
                    <span class="sr-only" for="email">Admin Email</span>
                    <input class="input"
                        type="email" id="email" name="email" required 
                        placeholder="admin@gradlink.com" 
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </label>

                <label class="field">
                    <span class="sr-only" for="password">Password</span>
                    <input class="input" type="password" id="password" name="password" required 
                           placeholder="Enter your password">
                    </label>
                <div class="actions">
                    <button type="submit" class="btn btn-primary">
                        Sign In as Admin
                    </button>
                </div>
            </form>
        <!-- Admin Login Form end-->
        <div class="brand-copy">
                <a href="<?php echo URLROOT;?>">← Back to Main Site</a>
        </div>
    </section>
</main>

<?php require APPROOT . '/views/inc/footer.php'; ?>
