<?php require APPROOT . '/views/inc/header.php';?>
<?php require APPROOT . '/views/inc/commponents/topnavbar.php';?>

<style>
    body {overflow: hidden; margin: 0; padding: 0; height: 100vh;}
    .container {display: flex; background-color: var(--bg); color: var(--text); height: calc(100vh - 70px); /* Subtract topnav height */ width: 100%; overflow: hidden; position: fixed; top: 70px; /* Position below topnav */ left: 0; z-index: 100; /* Lower than topnav's z-index */}
    .leftsidebar {width: 240px; background-color: #1c1f23; color: #ffffff; padding: 20px; border-right: 1px solid #333; overflow-y: auto; height: 100%; box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);}
    .maincontent {flex: 1; padding: 20px; color: #ffffff; display: flex; flex-direction: column; overflow-y: auto; height: 100%; max-height: calc(100vh - 70px); /* Ensure it doesn't overflow */}
    .menu {display: flex; flex-direction: column; align-items: flex-start; list-style-type: none; padding: 0; margin: 0; width: 100%;}
    .menu-item {margin-bottom: 15px; width: 100%; transition: all 0.2s ease;}
    .menu-item a {color: #ffffff; text-decoration: none; font-weight: 500; display: block; padding: 8px 12px; border-radius: 6px; transition: all 0.2s ease;}
    .menu-item a:hover, .menu-item.active a {color: #1e90ff; background-color: rgba(30, 144, 255, 0.1);}
    .menu div {margin-top: 30px; border-top: 1px solid #333; padding-top: 15px; width: 100%;}
    .menu div .menu-item {margin-bottom: 0;}
    
    <?php echo $styles; ?> 
</style>

<div class="container">
    <div class="leftsidebar">
        <ul class="menu">
            <?php foreach($sidebar_left as $link): ?>
            <li class="menu-item <?php if($link['active']){echo "active";}?>">
                <a href="<?php echo URLROOT.$link['url'] ?>">
                    <i class="fas fa-<?php echo isset($link['icon']) ? $link['icon'] : 'layer-group'; ?>"></i>
                    <span><?php echo $link['label'] ?></span>
                </a>
            </li>
            <?php endforeach; ?>
            <div>
                <li class="menu-item">
                    <a href="<?php echo URLROOT; ?>/mainfeed">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to Main Feed</span>
                    </a>
                </li>
            </div>
        </ul>
    </div>

    <div class="maincontent">
        <?php echo $content; ?>
    </div>
</div>