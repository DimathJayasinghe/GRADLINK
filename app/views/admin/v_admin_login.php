<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?php echo SITENAME; ?></title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/admin/admin.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/auth/login_signup_styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .admin-login-container {
            min-height: 100vh;
            background: #1a1a1a;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Poppins', sans-serif;
        }
        
        .admin-login-card {
            background: #2a2a2a;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            text-align: center;
            border: 1px solid #3a3a3a;
        }
        
        .admin-login-header {
            margin-bottom: 30px;
        }
        
        .admin-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: white;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: bold;
            color: #1a1a1a;
        }
        
        .admin-title {
            font-size: 2rem;
            color: white;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .admin-subtitle {
            color: #cccccc;
            font-size: 1rem;
            margin-bottom: 15px;
        }
        
        .admin-badge {
            background: #4a90e2;
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .form-group {
            margin-bottom: 25px;
            text-align: left;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: white;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .form-group input {
            width: 100%;
            padding: 15px 18px;
            border: 2px solid #3a3a3a;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            box-sizing: border-box;
            background: #1a1a1a;
            color: white;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #4a90e2;
            background: #222;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }
        
        .form-group input::placeholder {
            color: #666;
        }
        
        .admin-login-btn {
            width: 100%;
            background: #4a90e2;
            color: white;
            border: none;
            padding: 16px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .admin-login-btn:hover {
            background: #357abd;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(74, 144, 226, 0.3);
        }
        
        .flash-messages {
            margin-bottom: 25px;
        }
        
        .flash-message {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .flash-message.error {
            background: rgba(220, 53, 69, 0.1);
            color: #ff6b6b;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }
        
        .flash-message.success {
            background: rgba(40, 167, 69, 0.1);
            color: #51cf66;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }
        
        .back-to-site {
            margin-top: 25px;
            text-align: center;
        }
        
        .back-to-site a {
            color: #4a90e2;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .back-to-site a:hover {
            color: #357abd;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="admin-login-container">
        <div class="admin-login-card">
            <div class="admin-login-header">
                <div class="admin-logo">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
                <h1 class="admin-title">GRADLINK</h1>
                <p class="admin-subtitle">Administrative Access</p>
                <span class="admin-badge">ADMIN PANEL</span>
            </div>

            <!-- Flash Messages -->
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

            <form method="POST" action="<?php echo URLROOT; ?>/adminlogin">
                <div class="form-group">
                    <label for="email">Admin Email</label>
                    <input type="email" id="email" name="email" required 
                           placeholder="admin@gradlink.com" 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required 
                           placeholder="Enter your password">
                </div>

                <button type="submit" class="admin-login-btn">
                    🔐 Sign In as Admin
                </button>
            </form>

            <div class="back-to-site">
                <a href="<?php echo URLROOT; ?>">← Back to Main Site</a>
            </div>
        </div>
    </div>
</body>
</html>
