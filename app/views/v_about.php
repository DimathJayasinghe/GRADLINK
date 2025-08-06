<?php
require APPROOT . '/views/inc/commponents/header.php';
?>
<h1>This is the about view. Hi! <?php echo $data['username'];?></h1>
<h1>And I'm <?php echo $data['userage'];?> y/o</h1>
<h1>Approot <?php echo APPROOT ?></h1>

<?php
require APPROOT . '/views/inc/commponents/footer.php';
?>