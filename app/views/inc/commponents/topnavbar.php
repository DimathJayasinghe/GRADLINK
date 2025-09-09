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
            <!-- Dynamic buttons rendered here -->
        <?php foreach($topnavbar_content as $element):?>
        <a href="<?php echo $element['url']; ?>" class="<?php if($element['active']){echo "active";}?>">
            <?php 
            if(isset($element['icon'])){
                echo '<i class="fas fa-'.$element['icon'].'"></i> ';
            }
            echo $element['label']; 
            ?>
            
        </a>
        <?php endforeach; ?>

        <a href="<?php echo URLROOT; ?>/logout">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>

    </div>
</div>