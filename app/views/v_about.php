<?php require APPROOT . '/views/inc/header.php';?>
<h1>ABOUT</h1>
<?php foreach ($data['users'] as $user): ?>
    <div class="user-card">
        <h1>USER</h1>
        <h2>Name: <?php echo $user->name; ?></h2>
        <p>Email: <?php echo $user->email; ?></p>
        <p>Created At: <?php echo $user->created_at; ?></p>
    </div>
<?php endforeach;?>

<?php require APPROOT . '/views/inc/footer.php';?>