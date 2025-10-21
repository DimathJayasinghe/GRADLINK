<?php require APPROOT . '/views/inc/header.php';?>
<?php $topnavbar_content = [
    ['url' => URLROOT . '/messages', 'label' => 'Messages', 'icon' => 'envelope', 'active' => false],
    ['url' => URLROOT . '/settings', 'label' => 'Settings', 'icon' => 'cog', 'active' => false],
]?>
<?php require APPROOT . '/views/inc/commponents/topnavbar.php';?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/req_dashboard/dashboard_layout.css">
<?php /* $styles moved to the document <head> by header.php */ ?>

<div class="container">
    <div class="leftsidebar">
        <ul class="menu">
            <?php
                if(!isset($sidebar_left) || !is_array($sidebar_left)){
                    $sidebar_left = [
                        ['label'=>'Overview', 'url'=>'/admin','active'=>false, 'icon'=>'tachometer-alt'],
                        ['label'=>'User Management', 'url'=>'/admin/users','active'=>false, 'icon' => 'users'],
                        ['label'=>'Engagement Metrics', 'url'=>'/admin/engagement','active'=>false, 'icon' => 'chart-bar'],
                        ['label'=>'Event Moderation', 'url'=>'/admin/eventrequests','active'=>false, 'icon' => 'clipboard-list'],
                        ['label'=>'Content Management', 'url'=>'/admin/posts','active'=>false, 'icon' => 'pencil-alt'],
                        ['label'=>'Fundraisers', 'url'=>'/admin/fundraisers','active'=>false, 'icon' => 'donate'],
                        ['label'=>'Alumni Verifications', 'url'=>'/admin/verifications','active'=>false, 'icon' => 'check-circle']
                    ];
                }
            ?>
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