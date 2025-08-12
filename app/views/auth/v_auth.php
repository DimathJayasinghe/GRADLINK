<?php require APPROOT . '/views/inc/header.php';?>
<!-- TOP NAVIGATION -->
<!-- <?php require APPROOT . '\views\inc\commponents\topnavbar.php';?> -->
<style>
.container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: var(--bg);
    /* width: 80vw; */

}

.container a {
    text-decoration: none;
    color: var(--text);
}

.card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 30px 10px;
    margin: 10px 40px;
    height: 80vh;
    border: 1px solid var(--border);
    border-radius: 10px;
    background-color: var(--card);

    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.4s cubic-bezier(0.4, 0, 0.2, 1), transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);

}

.card p {
    text-align: center;
}

.card p>a {
    color: var(--primary);
    text-decoration: none;
}

.alumni_card {
    margin-right: 0px;
}

.card:hover {
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
    transform: translateY(-10px);
}
</style>
<div class="container">
    <div class="card alumni_card">
        <h1>For Alumni</h1>
        <p>Reconnect with your UCSC family, share experiences, mentor the next generation, and explore new opportunities
            together.</p>
        <button class="btn btn-primary"><a href="<?php echo URLROOT?>/auth/login">Login</a></button>
        <p>Don't have an account?<a href="<?php echo URLROOT?>/auth/signup?role=alumni">Signup</a></p>
    </div>
    <div class="card undergrad_card">
        <h1>For Undergraduates</h1>
        <p>Join our vibrant undergraduate community, sharpen your skills, collaborate on projects, and build a
            future-ready network.</p>
        <button class="btn btn-primary"><a href="<?php echo URLROOT?>/auth/login">Login</a></button>
        <p>Don't have an account?<a href="<?php echo URLROOT?>/auth/signup?role=undergrad">Signup</a></p>
    </div>
</div>
<?php require APPROOT . '/views/inc/footer.php';?>