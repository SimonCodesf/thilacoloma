let currentSlide = 0;
let slideInterval;
let isTransitioning = false;

// Initialize slideshow when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Set flag to prevent other slideshow scripts from initializing
    window.slideshowInitialized = true;
    initializeSlideshow();
});

function initializeSlideshow() {
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    
    if (slides.length === 0) return;
    
    // Set initial positions without transition
    slides.forEach((slide, i) => {
        slide.style.position = 'absolute';
        slide.style.top = '0';
        slide.style.left = '0';
        slide.style.width = '100%';
        slide.style.display = 'flex';
        
        if (i === 0) {
            slide.style.opacity = '1';
            slide.style.zIndex = '2';
            slide.style.transform = 'translateX(0) scale(1)';
        } else if (i === 1) {
            slide.style.opacity = '1';
            slide.style.zIndex = '1';
            slide.style.transform = 'translateX(20px) scale(0.95)';
        } else if (i === 2) {
            slide.style.opacity = '1';
            slide.style.zIndex = '0';
            slide.style.transform = 'translateX(40px) scale(0.9)';
        } else {
            slide.style.opacity = '0';
            slide.style.zIndex = '-1';
            slide.style.transform = 'translateX(0) scale(0.8)';
        }
    });
    
    // Initialize dots
    dots.forEach((dot, index) => {
        if (index === 0) {
            dot.classList.add('active');
        } else {
            dot.classList.remove('active');
        }
        dot.addEventListener('click', () => goToSlide(index));
    });
    
    // Set current slide to 0
    currentSlide = 0;
    
    // Start auto-rotation
    startAutoRotation();
    
    // Add hover pause functionality
    const slideshow = document.getElementById('slideshow');
    if (slideshow) {
        slideshow.addEventListener('mouseenter', stopAutoRotation);
        slideshow.addEventListener('mouseleave', startAutoRotation);
    }
    
    // Add keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowRight') {
            nextSlide();
        } else if (e.key === 'ArrowLeft') {
            prevSlide();
        }
    });
    
    console.log('Card stack slideshow initialized with', slides.length, 'slides');
}

function showSlide(index) {
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    
    if (slides.length === 0) return;
    
    slides.forEach((slide, i) => {
        if (i === index) {
            slide.style.transition = 'transform 0.6s ease-in-out, opacity 0.6s ease-out';
            slide.style.opacity = '1';
            slide.style.zIndex = '2';
            slide.style.transform = 'translateX(0) scale(1)';
        } else if (i === (index + 1) % slides.length) {
            slide.style.transition = 'transform 0.6s ease-out, opacity 0.6s ease-out';
            slide.style.opacity = '0.95';
            slide.style.zIndex = '1';
            slide.style.transform = 'translateX(20px) scale(0.95)';
        } else if (i === (index + 2) % slides.length) {
            slide.style.transition = 'transform 0.6s ease-out, opacity 0.6s ease-out';            
            slide.style.opacity = '0.9';
            slide.style.zIndex = '0';
            slide.style.transform = 'translateX(40px) scale(0.9)';
        } else {
            slide.style.transition = 'transform 0.6s ease-out, opacity 0.6s ease-out';
            slide.style.opacity = '0';
            slide.style.zIndex = '-1';
            slide.style.transform = 'translateX(60px) scale(0.85)';
        }
    });
    
    // Update dots
    dots.forEach((dot, i) => {
        dot.classList.toggle('active', i === index);
    });
    
    isTransitioning = true;
    setTimeout(() => {
        isTransitioning = false;
    }, 600); // Match the transition duration
}

function currentSlide(index) {
    if (!isTransitioning) {
        stopAutoRotation();
        currentSlide = index;
        showSlide(currentSlide);
        startAutoRotation();
    }
}

function nextSlide() {
    if (!isTransitioning) {
        currentSlide = (currentSlide + 1) % document.querySelectorAll('.slide').length;
        showSlide(currentSlide);
    }
}

function prevSlide() {
    if (!isTransitioning) {
        currentSlide = (currentSlide - 1 + document.querySelectorAll('.slide').length) % document.querySelectorAll('.slide').length;
        showSlide(currentSlide);
    }
}

function goToSlide(index) {
    if (!isTransitioning) {
        stopAutoRotation();
        currentSlide = index;
        showSlide(currentSlide);
        startAutoRotation();
    }
}

function startAutoRotation() {
    stopAutoRotation();
    slideInterval = setInterval(nextSlide, 8000); // Change slide every 8 seconds
}

function stopAutoRotation() {
    if (slideInterval) {
        clearInterval(slideInterval);
        slideInterval = null;
    }
}

// Add more interactivity to buttons
document.addEventListener('DOMContentLoaded', function() {
    // Add click handlers to "Meer info" buttons
    const meerButtons = document.querySelectorAll('.meer-button');
    meerButtons.forEach((button, index) => {
        button.addEventListener('click', function() {
            // Simple alert for now - in the future this could open a modal
            const slideTitle = button.closest('.slide').querySelector('h2').textContent;
            alert('Meer informatie over: ' + slideTitle + '\n\nDeze functionaliteit kan uitgebreid worden met een popup venster of doorverwijzing naar een detailpagina.');
        });
    });
});

// CSS styles for enhanced card stack effect
const style = document.createElement('style');
style.textContent = `
    .dot {
        cursor: pointer;
        transition: all 0.3s ease;
        transform: scale(1);
    }
    
    .dot.active {
        background-color: var(--primary-color) !important;
        transform: scale(1.2);
    }
    
    .dot:hover {
        background-color: var(--pink-color) !important;
        transform: scale(1.1);
    }
    
    /* Enhanced shadows for better card stacking effect */
    .slide {
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12) !important;
        border: 1px solid rgba(55, 71, 148, 0.08);
    }
    
    .slide:nth-child(1) {
        box-shadow: 0 12px 40px rgba(55, 71, 148, 0.15) !important;
    }
    
    .slide:nth-child(2) {
        box-shadow: 0 10px 35px rgba(192, 88, 154, 0.12) !important;
    }
    
    .slide:nth-child(3) {
        box-shadow: 0 8px 30px rgba(230, 166, 48, 0.1) !important;
    }
`;
document.head.appendChild(style);
