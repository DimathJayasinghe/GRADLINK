<?php require APPROOT . '/views/inc/header.php';?>
<?php $topnavbar_content = [
    ['url' => URLROOT . '/messages', 'label' => 'Messages', 'icon' => 'envelope', 'active' => false],
    ['url' => URLROOT . '/settings', 'label' => 'Settings', 'icon' => 'cog', 'active' => false],
]?>
<?php require APPROOT . '/views/inc/commponents/topnavbar.php';?>

<style>
    body {overflow: hidden; margin: 0; padding: 0; height: 100vh;}
    .container {display: flex; background-color: var(--bg); color: var(--text); height: calc(100vh - 70px); /* Subtract topnav height */ width: 100%; overflow: hidden; position: fixed; top: 70px; /* Position below topnav */ left: 0; z-index: 100; /* Lower than topnav's z-index */}
    .leftsidebar {width: 280px; background: var(--card); padding: 1.5rem 0; border-right: 1px solid #3a3a3a; overflow-y: auto; height: 100%; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);}
    .maincontent {flex: 1; padding: 2rem; color: var(--text); display: flex; flex-direction: column; overflow-y: auto; height: 100%; max-height: calc(100vh - 70px); /* Ensure it doesn't overflow */}
    .menu {display: flex; flex-direction: column; list-style-type: none; padding: 0; margin: 0; width: 100%; gap: .25rem;}
    .menu-item {width: 100%; transition: all 0.3s ease; display: flex;}
    .menu-item a {display: flex; align-items: center; gap: 1rem; padding: 1rem 1.5rem; text-decoration: none; color: rgb(255, 255, 255); transition: all 0.3s ease; border-left: 4px solid transparent; cursor: pointer; font-weight: 500; font-size: 14px; width: 100%;}
    .menu-item a:hover {background: rgba(74, 144, 226, 0.1); color: var(--text); border-left-color: var(--info);}
    .menu-item.active a {background: rgba(74, 144, 226, 0.15); color: var(--text); border-left-color: var(--info);}
    .menu div {margin-top: 1.5rem; border-top: 1px solid #3a3a3a; padding-top: 1.5rem; width: 100%;}
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