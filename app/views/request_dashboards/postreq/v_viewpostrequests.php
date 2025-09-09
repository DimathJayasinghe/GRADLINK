<?php ob_start(); ?>
<!-- Additional styles for the dashboard layout -->

<?php $styles = ob_get_clean(); ?>



<?php ob_start(); ?>
<!-- content for the left side bar goes here -->

<?php $sidebar_left = ob_get_clean(); ?>




<?php ob_start(); ?>
<!-- Main content goes here -->

<?php $content = ob_get_clean(); ?>
<?php require APPROOT . '/views/request_dashboards/dashboard_layout.php';?>