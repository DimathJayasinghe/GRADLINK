<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GRADLINK</title>
  <link rel="icon" type="image/x-icon" href="<?php echo URLROOT?>/img/favicon_white.png" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
  <style>
    a {
      text-decoration: none;
    }
    
    /* Adjust hero content for better spacing */
    .hero-content {
        max-width: 800px;
        text-align: center;
        padding: 2rem;
        padding-bottom: 120px; /* Increased bottom padding */
        z-index: 1;
        opacity: 0;
        transform: translateY(-20px); /* Move content up slightly */
        animation: fadeIn 1s forwards 0.5s;
    }

    @keyframes fadeIn {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    /* Logo styling in hero section */
    .hero-logo {
      width: 120px;
      height: auto;
      margin-bottom: 2rem;
      animation: logoGlow 3s infinite alternate;
    }
    
    @keyframes logoGlow {
      from { filter: drop-shadow(0 0 5px rgba(158, 212, 220, 0.2)); }
      to { filter: drop-shadow(0 0 15px rgba(158, 212, 220, 0.5)); }
    }

    .hero-title {
      font-size: 3rem; /* Reduced from 3.5rem */
      font-weight: 700;
      margin-bottom: 1rem; /* Reduced margin */
      line-height: 1.2;
    }

    .hero-description {
      font-size: 1.1rem; /* Reduced from 1.2rem */
      color: var(--muted);
      margin-bottom: 1.5rem; /* Reduced margin */
      max-width: 550px; /* Slightly narrower */
      margin-left: auto;
      margin-right: auto;
    }
    
    .parallax-section {
      position: relative;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }

    .parallax-bg {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: url('<?php echo URLROOT?>/img/hero-bg.jpg');
      background-size: cover;
      background-position: center;
      transform: translateZ(-1px) scale(2);
      z-index: -1;
      filter: brightness(0.3);
    }

    /* Empty header preserved for styling consistency */
    .header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 100;
      padding: 1.5rem 2rem;
      background-color: rgba(15, 21, 24, 0.9);
      backdrop-filter: blur(5px);
      transition: all 0.3s ease;
      height: 20px;
    }

    .btn {
      padding: 0.6rem 1.2rem;
      border: none;
      border-radius: 4px;
      font-family: 'Poppins', sans-serif;
      font-weight: 500;
      cursor: pointer;
      transition: var(--transition);
    }

    .btn-primary {
      background-color: var(--btn);
      color: var(--btn-text);
    }

    .btn-primary:hover {
      background-color: var(--link);
      transform: translateY(-2px);
    }
    
    .btn-xl {
      padding: 0.8rem 2.5rem;
      font-size: 1.1rem;
      letter-spacing: 0.5px;
    }

    /* Features section */
    .features {
      position: relative;
      background-color: var(--card);
      padding: 6rem 2rem;
      z-index: 1;
    }

    .section-title {
      text-align: center;
      font-size: 2.2rem;
      margin-bottom: 4rem;
    }

    .feature-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 3rem;
      max-width: 1200px;
      margin: 0 auto;
    }

    .feature-card {
      background-color: var(--input);
      border-radius: 8px;
      padding: 2rem;
      text-align: center;
      transition: all 0.3s ease;
      transform: translateY(0);
    }

    .feature-card:hover {
      transform: translateY(-10px);
    }

    .feature-icon {
      height: 60px;
      width: 60px;
      margin: 0 auto 1.5rem;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: rgba(158, 212, 220, 0.1);
      border-radius: 50%;
      color: var(--link);
      font-size: 1.8rem;
    }

    .feature-title {
      font-size: 1.5rem;
      margin-bottom: 1rem;
    }

    .feature-text {
      color: var(--muted);
      line-height: 1.6;
    }

    /* About section with parallax */
    .about {
      position: relative;
      padding: 8rem 2rem;
      background-attachment: fixed;
      background-image: url('<?php echo URLROOT?>/img/about-bg.jpg');
      background-size: cover;
      background-position: center;
    }

    .about::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(15, 21, 24, 0.85);
      z-index: 0;
    }

    .about-content {
      position: relative;
      z-index: 1;
      max-width: 800px;
      margin: 0 auto;
      text-align: center;
    }

    .about-text {
      color: var(--muted);
      line-height: 1.8;
      margin-bottom: 2rem;
    }

    /* Footer */
    .footer {
      background-color: var(--bg);
      padding: 3rem 2rem;
      text-align: center;
    }

    .footer-links {
      display: flex;
      justify-content: center;
      gap: 2rem;
      margin-bottom: 1.5rem;
    }

    .footer-links a {
      color: var(--muted);
      text-decoration: none;
      transition: var(--transition);
    }

    .footer-links a:hover {
      color: var(--link);
    }

    .copyright {
      color: var(--muted);
      font-size: 0.9rem;
    }

    /* Scroll indicator */
    .scroll-down {
      position: absolute;
      bottom: 25px; /* Position lower */
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      flex-direction: column;
      align-items: center;
      color: var(--text);
      animation: bounce 2s infinite;
      z-index: 5; /* Ensure it's above other elements */
    }

    .scroll-down span {
      font-size: 0.95rem;
      margin-bottom: 10px;
      text-transform: uppercase;
      letter-spacing: 1px;
      font-weight: 500;
    }

    .scroll-down-arrow {
      width: 40px; /* Larger arrow */
      height: 40px; /* Larger arrow */
      border: 2px solid var(--link);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: rgba(15, 21, 24, 0.7);
      box-shadow: 0 0 15px rgba(158, 212, 220, 0.3);
    }
    
    /* Improved bounce animation */
    @keyframes bounce {
      0%, 20%, 50%, 80%, 100% {
        transform: translateY(0) translateX(-50%);
      }
      40% {
        transform: translateY(-20px) translateX(-50%);
      }
      60% {
        transform: translateY(-10px) translateX(-50%);
      }
    }
    
    /* Make the arrow inside larger */
    .scroll-down-arrow svg {
      width: 18px;
      height: 18px;
    }

    @media (max-width: 768px) {
      .header {
        padding: 1rem;
      }
      
      .hero-title {
        font-size: 2.2rem;
      }
      
      .feature-grid {
        grid-template-columns: 1fr;
      }
      
      .btn {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
      }
      
      .hero-content {
          padding-bottom: 100px;
      }
      
      .hero-description {
          font-size: 1rem;
          margin-bottom: 1.2rem;
      }
    }
  </style>
</head>
<body>
  <!-- Minimal header without logo or buttons -->
  <header class="header"></header>

  <section class="parallax-section">
    <div class="parallax-bg"></div>
    <div class="hero-content">
      <!-- Added brand logo to the center of the page -->
      <img src="<?php echo URLROOT?>/img/logo_white.png" alt="GRADLINK Logo" class="hero-logo">
      <h2 class="hero-title">Connect & Thrive with GRADLINK</h2>
      <p class="hero-description">Join our network connecting alumni and undergraduates to expand your professional horizons and access valuable resources.</p>
      <a href="<?php echo URLROOT; ?>/auth" class="btn btn-primary btn-xl">Get Started</a>
    </div>
    <div class="scroll-down">
      <span>Discover More</span>
      <div class="scroll-down-arrow">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 20L12 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          <path d="M5 13L12 20L19 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
    </div>
  </section>

  <section id="features" class="features">
    <h3 class="section-title">What We Offer</h3>
    <div class="feature-grid">
      <div class="feature-card">
        <div class="feature-icon">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M16 3H8C6.89543 3 6 3.89543 6 5V19C6 20.1046 6.89543 21 8 21H16C17.1046 21 18 20.1046 18 19V5C18 3.89543 17.1046 3 16 3Z" stroke="currentColor" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M12 18H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </div>
        <h4 class="feature-title">Network Building</h4>
        <p class="feature-text">Connect with alumni and undergraduates across different batches to build meaningful professional relationships.</p>
      </div>
      
      <div class="feature-card">
        <div class="feature-icon">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 14C13.1046 14 14 13.1046 14 12C14 10.8954 13.1046 10 12 10C10.8954 10 10 10.8954 10 12C10 13.1046 10.8954 14 12 14Z" stroke="currentColor" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M22 12C19.333 16.667 16 19 12 19C8 19 4.667 16.667 2 12C4.667 7.333 8 5 12 5C16 5 19.333 7.333 22 12Z" stroke="currentColor" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <h4 class="feature-title">Opportunity Sharing</h4>
        <p class="feature-text">Discover and share job opportunities, internships, and projects with fellow members.</p>
      </div>
      
      <div class="feature-card">
        <div class="feature-icon">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M17 8L21 12M21 12L17 16M21 12H7M13 4H9C7.89543 4 7 4.89543 7 6V18C7 19.1046 7.89543 20 9 20H13" stroke="currentColor" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <h4 class="feature-title">Knowledge Exchange</h4>
        <p class="feature-text">Share experiences, insights, and learning resources to help others advance in their careers.</p>
      </div>
    </div>
  </section>

  <section id="about" class="about">
    <div class="about-content">
      <h3 class="section-title">About GRADLINK</h3>
      <p class="about-text">
        GRADLINK is a platform designed to bridge the gap between current students and alumni. Our mission is to create a supportive community where knowledge, opportunities, and mentorship can flow freely between generations of graduates.
      </p>
      <p class="about-text">
        Whether you're looking for career guidance, industry insights, or simply to reconnect with university peers, GRADLINK provides the tools and connections you need to succeed.
      </p>
      <a href="#" class="btn btn-primary">Learn More</a>
    </div>
  </section>

  <footer id="contact" class="footer">
    <div class="footer-links">
      <a href="<?php echo URLROOT; ?>/Hero/termsofservice">Terms of Service</a>
      <a href="<?php echo URLROOT; ?>/Hero/privacypolicy">Privacy Policy</a>
      <a href="<?php echo URLROOT; ?>/Hero/support">Support</a>
      <a href="<?php echo URLROOT; ?>/Hero/contactus">Contact Us</a>
    </div>
    <p class="copyright">&copy; <?php echo date('Y'); ?> GRADLINK. All rights reserved.</p>
  </footer>

  <script>
    // Parallax scroll effect
    window.addEventListener('scroll', function() {
      const scrolled = window.pageYOffset;
      
      // Header shrink on scroll
      if (scrolled > 50) {
        document.querySelector('.header').classList.add('scrolled');
      } else {
        document.querySelector('.header').classList.remove('scrolled');
      }
      
      // Parallax effect for background
      const parallaxBg = document.querySelector('.parallax-bg');
      if (parallaxBg) {
        parallaxBg.style.transform = 'translateY(' + (scrolled * 0.5) + 'px)';
      }
      
      // Show elements on scroll
      const features = document.querySelectorAll('.feature-card');
      features.forEach((feature, index) => {
        const featurePosition = feature.getBoundingClientRect().top;
        const screenPosition = window.innerHeight / 1.3;
        
        if (featurePosition < screenPosition) {
          setTimeout(() => {
            feature.style.opacity = 1;
            feature.style.transform = 'translateY(0)';
          }, index * 200);
        }
      });
    });

    // Initialize features with opacity 0
    document.addEventListener('DOMContentLoaded', function() {
      const features = document.querySelectorAll('.feature-card');
      features.forEach(feature => {
        feature.style.opacity = 0;
        feature.style.transform = 'translateY(30px)';
        feature.style.transition = 'all 0.8s ease';
      });
    });

    // Smooth scroll for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
          behavior: 'smooth'
        });
      });
    });
  </script>
</body>
</html>
