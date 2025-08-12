<?php require APPROOT . '/views/inc/header.php';?>

<section class="hero-section">
    <div class="hero-background">
        <div class="hero-overlay"></div>
    </div>
    
    <div class="hero-content">
        <div class="container">
            <div class="hero-text">
                <h1 class="hero-headline">Connecting UCSC Minds, <span class="highlight">Across Generations</span></h1>
                <p class="hero-subtext">Where alumni and undergraduates share knowledge, opportunities, and growth</p>
                <button class="cta-button" onclick="joinNetwork()">
                    <span>Join the Network</span>
                    <div class="button-overlay"></div>
                </button>
            </div>
            
            <div class="hero-illustration">
                <svg class="network-svg" viewBox="0 0 400 300" xmlns="http://www.w3.org/2000/svg">
                    <!-- Network nodes and connections -->
                    <defs>
                        <linearGradient id="goldGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#FFD700;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#FFA500;stop-opacity:1" />
                        </linearGradient>
                    </defs>
                    
                    <!-- Connection lines -->
                    <path class="connection-line line-1" d="M80,80 L160,120" stroke="#ccc" stroke-width="2" opacity="0.6"/>
                    <path class="connection-line line-2" d="M160,120 L240,100" stroke="#ccc" stroke-width="2" opacity="0.6"/>
                    <path class="connection-line line-3" d="M240,100 L320,140" stroke="#ccc" stroke-width="2" opacity="0.6"/>
                    <path class="connection-line line-4" d="M160,120 L200,200" stroke="#ccc" stroke-width="2" opacity="0.6"/>
                    <path class="connection-line line-5" d="M240,100 L280,180" stroke="#ccc" stroke-width="2" opacity="0.6"/>
                    <path class="connection-line line-6" d="M80,80 L120,160" stroke="#ccc" stroke-width="2" opacity="0.6"/>
                    
                    <!-- Network nodes -->
                    <circle class="network-node node-1" cx="80" cy="80" r="12" fill="url(#goldGradient)"/>
                    <circle class="network-node node-2" cx="160" cy="120" r="10" fill="#333"/>
                    <circle class="network-node node-3" cx="240" cy="100" r="14" fill="url(#goldGradient)"/>
                    <circle class="network-node node-4" cx="320" cy="140" r="8" fill="#666"/>
                    <circle class="network-node node-5" cx="200" cy="200" r="10" fill="#333"/>
                    <circle class="network-node node-6" cx="280" cy="180" r="12" fill="url(#goldGradient)"/>
                    <circle class="network-node node-7" cx="120" cy="160" r="9" fill="#555"/>
                </svg>
            </div>
        </div>
    </div>
</section>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.hero-section {
    position: relative;
    height: 100vh;
    min-height: 600px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hero-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.5) 50%, rgba(0,0,0,0.8) 100%),
                url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 600"><rect fill="%23f5f5f5" width="1000" height="600"/><rect fill="%23e0e0e0" x="0" y="0" width="1000" height="200"/><rect fill="%23d0d0d0" x="200" y="100" width="600" height="300"/></svg>');
    background-size: cover;
    background-position: center;
    filter: blur(1px);
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, rgba(255,215,0,0.1) 0%, rgba(0,0,0,0.3) 100%);
}

.hero-content {
    position: relative;
    z-index: 2;
    width: 100%;
    color: white;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
}

.hero-text {
    opacity: 0;
    transform: translateY(30px);
    animation: fadeInUp 1s ease-out 0.5s forwards;
}

.hero-headline {
    font-size: 3.5rem;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 20px;
    color: #ffffff;
}

.highlight {
    color: #FFD700;
    position: relative;
}

.highlight::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 0;
    width: 0;
    height: 3px;
    background: linear-gradient(90deg, #FFD700, #FFA500);
    animation: underlineExpand 1s ease-out 1.5s forwards;
}

.hero-subtext {
    font-size: 1.3rem;
    line-height: 1.6;
    margin-bottom: 40px;
    color: #e0e0e0;
    max-width: 500px;
}

.cta-button {
    position: relative;
    background: linear-gradient(45deg, #FFD700, #FFA500);
    border: none;
    padding: 18px 40px;
    font-size: 1.2rem;
    font-weight: 600;
    color: #000;
    border-radius: 50px;
    cursor: pointer;
    overflow: hidden;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.cta-button span {
    position: relative;
    z-index: 2;
}

.button-overlay {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, #FFA500, #FF8C00);
    transition: left 0.3s ease;
}

.cta-button:hover .button-overlay {
    left: 0;
}

.cta-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(255, 215, 0, 0.3);
}

.hero-illustration {
    opacity: 0;
    transform: translateX(30px);
    animation: fadeInRight 1s ease-out 1s forwards;
    display: flex;
    justify-content: center;
    align-items: center;
}

.network-svg {
    width: 100%;
    max-width: 400px;
    height: auto;
}

.connection-line {
    stroke-dasharray: 100;
    stroke-dashoffset: 100;
    animation: drawLine 2s ease-out forwards;
}

.network-node {
    opacity: 0;
    transform-origin: center;
    animation: nodeAppear 0.5s ease-out forwards;
}

/* Staggered animations for lines and nodes */
.line-1 { animation-delay: 1.5s; }
.line-2 { animation-delay: 1.7s; }
.line-3 { animation-delay: 1.9s; }
.line-4 { animation-delay: 2.1s; }
.line-5 { animation-delay: 2.3s; }
.line-6 { animation-delay: 2.5s; }

.node-1 { animation-delay: 2s; }
.node-2 { animation-delay: 2.1s; }
.node-3 { animation-delay: 2.2s; }
.node-4 { animation-delay: 2.3s; }
.node-5 { animation-delay: 2.4s; }
.node-6 { animation-delay: 2.5s; }
.node-7 { animation-delay: 2.6s; }

/* Animations */
@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInRight {
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes underlineExpand {
    to {
        width: 100%;
    }
}

@keyframes drawLine {
    to {
        stroke-dashoffset: 0;
    }
}

@keyframes nodeAppear {
    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        grid-template-columns: 1fr;
        gap: 40px;
        text-align: center;
    }
    
    .hero-headline {
        font-size: 2.5rem;
    }
    
    .hero-subtext {
        font-size: 1.1rem;
        max-width: none;
    }
    
    .cta-button {
        padding: 16px 35px;
        font-size: 1.1rem;
    }
    
    .hero-illustration {
        order: -1;
    }
    
    .network-svg {
        max-width: 300px;
    }
}

@media (max-width: 480px) {
    .hero-headline {
        font-size: 2rem;
    }
    
    .hero-subtext {
        font-size: 1rem;
    }
    
    .container {
        padding: 0 15px;
    }
}
</style>

<script>
function joinNetwork() {
    // Add a subtle animation to the button
    const button = document.querySelector('.cta-button');
    button.style.transform = 'scale(0.95)';
    
    setTimeout(() => {
        button.style.transform = 'scale(1)';
        // Here you can add navigation logic or show a modal
        window.location.href = '<?php echo URLROOT?>/auth'; // Example redirect
    }, 150);
}

// Add floating animation to network nodes
document.addEventListener('DOMContentLoaded', function() {
    const nodes = document.querySelectorAll('.network-node');
    
    nodes.forEach((node, index) => {
        setTimeout(() => {
            node.style.animation += `, float 3s ease-in-out infinite`;
            node.style.animationDelay = `${2.5 + index * 0.2}s, ${index * 0.3}s`;
        }, 3000);
    });
});

// Add floating keyframe
const style = document.createElement('style');
style.textContent = `
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-5px); }
    }
`;
document.head.appendChild(style);
</script>

<?php require APPROOT . '/views/inc/footer.php';?>