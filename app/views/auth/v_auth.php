<?php require APPROOT . '/views/inc/header.php';?>

<style>
/* Back button */
.back-button {
    position: fixed;
    top: 16px;
    left: 16px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: transparent;
    color: var(--muted);
    border: 1px solid var(--border);
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s ease, border-color 0.2s ease, background-color 0.2s ease;
    z-index: 100;
}

.back-button:hover {
    color: var(--text);
    border-color: var(--muted);
    background: var(--input);
}

/* Container layout */
.auth-container {
    max-width: 1200px;
    margin: 0 auto;
    min-height: 100vh;
    display: flex;
    background-color: var(--bg);
}

/* Section styling */
.auth-section {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem;
    position: relative;
}

/* Vertical divider */
.auth-section.alumni-section::after {
    content: '';
    position: absolute;
    top: 15%;
    right: 0;
    height: 70%;
    width: 1px;
    background-color: var(--border);
}

/* Logo styling */
.section-logo {
    width: 100px;
    height: 100px;
    margin-bottom: 1.5rem;
}

/* Typography */
.section-title {
    font-size: 2.2rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    color: var(--text);
    text-align: center;
}

.section-description {
    color: var(--muted);
    text-align: center;
    font-weight: 300;
    line-height: 1.6;
    margin-bottom: 3rem;
    max-width: 400px;
}

/* Button styling */
.btn {
    background-color: var(--btn);
    color: var(--btn-text);
    border: none;
    padding: 12px 30px;
    border-radius: 4px;
    cursor: pointer;
    font-family: 'Poppins', sans-serif;
    font-weight: 500;
    text-decoration: none;
    display: inline-block;
    transition: background-color 0.2s ease;
    margin-bottom: 2rem;
    text-align: center;
}

.btn:hover {
    background-color: var(--link);
}

/* Link styling */
a {
    text-decoration: none;
}

.signup-link {
    color: var(--text);
    font-size: 0.95rem;
}

.signup-link a {
    color: var(--link);
    font-weight: 600;
    transition: color 0.2s ease;
}

.signup-link a:hover {
    color: var(--link-hover);
}

/* Hover effects
.auth-section:hover .section-title {
    transform: translateY(-5px);
    transition: transform 0.3s ease;
} */

/* Responsive adjustments */
@media (max-width: 768px) {
    .auth-container {
        flex-direction: column;
    }
    
    .auth-section.alumni-section::after {
        content: '';
        position: absolute;
        top: auto;
        right: auto;
        bottom: 0;
        left: 15%;
        height: 1px;
        width: 70%;
        background-color: var(--border);
    }
    
    .auth-section {
        padding: 2rem 1.5rem;
    }
}
</style>
<a href="<?php echo URLROOT; ?>" class="back-button" aria-label="Back to Gradlink home">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
    Back
</a>
<div class="auth-container">
    <div class="auth-section alumni-section">
        <img src="<?php echo URLROOT?>/img/logo_white.png" alt="Alumni" class="section-logo">
        <h1 class="section-title">For Alumni</h1>
        <p class="section-description">Reconnect with your UCSC family, share experiences, mentor the next generation, and explore new opportunities together.</p>
        <a class="btn" href="<?php echo URLROOT?>/login/alumni">Log In</a>
        <p class="signup-link">Don't have an account? <a href="<?php echo URLROOT?>/signup/alumni">Sign Up</a></p>
    </div>
    
    <div class="auth-section undergrad-section">
        <img src="<?php echo URLROOT?>/img/logo_white.png" alt="Undergraduate" class="section-logo">
        <h1 class="section-title">For Undergraduates</h1>
        <p class="section-description">Join our vibrant undergraduate community, sharpen your skills,  and build a future-ready network.</p>
        <a class="btn" href="<?php echo URLROOT?>/login/undergrad">Log In</a>
        <p class="signup-link">Don't have an account? <a href="<?php echo URLROOT?>/signup/undergrad">Sign Up</a></p>
    </div>
</div>

<?php require APPROOT . '/views/inc/footer.php';?>