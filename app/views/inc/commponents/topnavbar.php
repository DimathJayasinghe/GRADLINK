<div class="topnav">
    <style>
        .topnav {
            display: flex;
            background: #1a2a6c;
            padding: 15px 30px;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            color: white;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .topnav a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            transition: all 0.3s ease;
            font-weight: 500;
            margin: 0 5px;
        }

        .topnav a:hover {
            background-color: rgba(255, 255, 255, 0.09);
            transform: translateY(-1px);
        }

        .topnav a.active {
            background-color: rgba(255, 255, 255, 0.3);
            font-weight: 600;
        }

        .logo {
            display: flex;
            align-items: center;
            height: 40px;
            margin-right: auto;
        }

        .logo img {
            height: 100%;
            width: auto;
            margin-right: 10px;
        }

        .logo span {
            font-weight: bold;
            font-size: 1.4em;
            letter-spacing: 1px;
        }

        .nav-links {
            display: flex;
            align-items: center;
        }
    </style>
    <a href="<?php echo URLROOT; ?>/mainfeed" class="logo">
        <img src="<?php echo URLROOT ?>/img/logo_white.png" alt="GradLink Logo">
        <span>GRADLINK</span>
    </a>

    <div class="nav-links">
        <a href="<?php echo URLROOT;
                    if ($_SESSION['user_role'] === 'admin') {
                        echo '/admin';
                    } else {
                        echo '/profile';
                    } ?>" class="active">
            
            <?php 
            if ($_SESSION['user_role'] === 'admin'){
                echo '<i class="fas fa-home"></i> Home';
            }else{
                echo '<i class="fas fa-user"></i> Profile';
            }
            ?>
            
        </a>

        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="<?php echo URLROOT; ?>/auth/login">
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
            <a href="<?php echo URLROOT; ?>/auth/signup">
                <i class="fas fa-user-plus"></i> Signup
            </a>
        <?php else: ?>
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <a href="<?php echo URLROOT; ?>/admin/dashboard">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            <?php endif; ?>

            <a href="<?php echo URLROOT; ?>/explore">
                <i class="fas fa-compass"></i> Explore
            </a>

            <a href="<?php echo URLROOT; ?>/logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        <?php endif; ?>
    </div>
</div>