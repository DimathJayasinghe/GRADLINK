<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/components/settings_categories.css">

<!-- Vertical Settings Navigation -->
<div class="settings-nav">
    <div class="settings-nav-container">
        <ul class="settings-menu">
            <?php foreach($calender_categories as $category): ?>
            <li class="settings-menu-item <?php if($category['active']){echo "active";}?>">
                <a href="<?php echo $category['link'] ?>">
                    <i class="fas fa-<?php echo isset($category['icon']) ? $category['icon'] : 'layer-group'; ?>"></i>
                    <span><?php echo $category['label'] ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>