<?php require APPROOT . '/views/inc/commponents/header.php';?>

<?php foreach ($data['users'] as $user): ?>
    <div class="user-card">
        <h1>USER</h1>
        <h2><?php echo $user->name; ?></h2>
        <p>Age: <?php echo $user->age; ?></p>
    </div>
<?php endforeach;?>

<?php require APPROOT . '/views/inc/commponents/footer.php';?>