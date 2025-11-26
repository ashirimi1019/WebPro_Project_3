/**
 * Animations Module
 * Handles visual effects including snowfall, confetti, and tile animations
 */

/**
 * Create snowfall effect
 */
function createSnowfall() {
    const container = document.querySelector('.snowfall-container');
    if (!container) return;
    
    const snowflakeCount = 50;
    
    for (let i = 0; i < snowflakeCount; i++) {
        const snowflake = document.createElement('div');
        snowflake.className = 'snowflake';
        snowflake.innerHTML = '❄';
        
        // Random properties
        const size = Math.random() * 0.5 + 0.5; // 0.5 - 1
        const left = Math.random() * 100; // 0 - 100%
        const duration = Math.random() * 10 + 10; // 10-20s
        const delay = Math.random() * 5; // 0-5s
        
        snowflake.style.fontSize = `${size}em`;
        snowflake.style.left = `${left}%`;
        snowflake.style.animationDuration = `${duration}s`;
        snowflake.style.animationDelay = `${delay}s`;
        snowflake.style.opacity = size;
        
        container.appendChild(snowflake);
    }
}

/**
 * Create confetti explosion
 */
function createConfetti() {
    const container = document.querySelector('.confetti-container');
    if (!container) {
        // Create temporary container
        const tempContainer = document.createElement('div');
        tempContainer.className = 'confetti-container';
        document.body.appendChild(tempContainer);
        
        setTimeout(() => {
            tempContainer.remove();
        }, 5000);
        
        return createConfettiIn(tempContainer);
    }
    
    return createConfettiIn(container);
}

function createConfettiIn(container) {
    container.innerHTML = '';
    const confettiCount = 100;
    const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#f9ca24', '#6c5ce7', '#a29bfe'];
    
    for (let i = 0; i < confettiCount; i++) {
        const confetti = document.createElement('div');
        confetti.className = 'confetti';
        
        const color = colors[Math.floor(Math.random() * colors.length)];
        const left = Math.random() * 100;
        const duration = Math.random() * 2 + 2;
        const delay = Math.random() * 0.5;
        const rotation = Math.random() * 360;
        const size = Math.random() * 10 + 5;
        
        confetti.style.backgroundColor = color;
        confetti.style.left = `${left}%`;
        confetti.style.animationDuration = `${duration}s`;
        confetti.style.animationDelay = `${delay}s`;
        confetti.style.transform = `rotate(${rotation}deg)`;
        confetti.style.width = `${size}px`;
        confetti.style.height = `${size}px`;
        
        container.appendChild(confetti);
    }
}

/**
 * Animate tile movement
 */
function animateTileMove(tile, direction) {
    tile.classList.add('moving');
    tile.style.transform = `translate(${direction.x * 100}%, ${direction.y * 100}%)`;
    
    setTimeout(() => {
        tile.classList.remove('moving');
        tile.style.transform = '';
    }, 300);
}

/**
 * Pulse animation for highlighted elements
 */
function pulseElement(element) {
    element.classList.add('pulse');
    setTimeout(() => {
        element.classList.remove('pulse');
    }, 1000);
}

/**
 * Shake animation for errors
 */
function shakeElement(element) {
    element.classList.add('shake');
    setTimeout(() => {
        element.classList.remove('shake');
    }, 500);
}

/**
 * Glow effect
 */
function glowElement(element, duration = 2000) {
    element.classList.add('glow');
    setTimeout(() => {
        element.classList.remove('glow');
    }, duration);
}

/**
 * Fade in element
 */
function fadeIn(element, duration = 300) {
    element.style.opacity = '0';
    element.style.display = 'block';
    
    let opacity = 0;
    const interval = 50;
    const increment = interval / duration;
    
    const fade = setInterval(() => {
        opacity += increment;
        element.style.opacity = opacity;
        
        if (opacity >= 1) {
            clearInterval(fade);
        }
    }, interval);
}

/**
 * Fade out element
 */
function fadeOut(element, duration = 300) {
    let opacity = 1;
    const interval = 50;
    const decrement = interval / duration;
    
    const fade = setInterval(() => {
        opacity -= decrement;
        element.style.opacity = opacity;
        
        if (opacity <= 0) {
            clearInterval(fade);
            element.style.display = 'none';
        }
    }, interval);
}
