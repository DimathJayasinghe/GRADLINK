<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/components/topNavbar_styles.css">
<div class="topnav">
    <a href="<?php echo URLROOT; ?>/mainfeed" class="logo">
        <img src="<?php echo URLROOT ?>/img/logo_white.png" alt="GradLink Logo">
        <span>GRADLINK</span>
    </a>

    <div class="nav-links">
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