// Simple working slideshow
let currentSlideIndex = 0;
let slideshowTimer;

function initSimpleSlideshow() {
    const slides = document.querySelectorAll('#slideshow .slide');
    const dots = document.querySelectorAll('.slideshow-dots .dot');
    
    if (slides.length === 0) {
        console.log('No slides found');
        return;
    }
    
    console.log('Initializing simple slideshow with', slides.length, 'slides');
    
    // Set up initial styles
    slides.forEach((slide, index) => {
        slide.style.position = 'absolute';
        slide.style.top = '0';
        slide.style.left = '0';
        slide.style.width = '100%';
        slide.style.height = '100%';
        slide.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        
        if (index === 0) {
            slide.style.opacity = '1';
            slide.style.zIndex = '10';
            slide.style.transform = 'scale(1)';
        } else {
            slide.style.opacity = '0';
            slide.style.zIndex = '1';
            slide.style.transform = 'scale(0.9)';
        }
    });
    
    // Set up dots
    dots.forEach((dot, index) => {
        dot.classList.remove('active');
        if (index === 0) {
            dot.classList.add('active');
        }
        
        dot.onclick = () => goToSlide(index);
    });
    
    // Start auto slideshow
    startSlideshow();
}

function goToSlide(index) {
    const slides = document.querySelectorAll('#slideshow .slide');
    const dots = document.querySelectorAll('.slideshow-dots .dot');
    
    // Hide current slide
    if (slides[currentSlideIndex]) {
        slides[currentSlideIndex].style.opacity = '0';
        slides[currentSlideIndex].style.zIndex = '1';
        slides[currentSlideIndex].style.transform = 'scale(0.9)';
    }
    
    // Show new slide
    currentSlideIndex = index;
    if (slides[currentSlideIndex]) {
        slides[currentSlideIndex].style.opacity = '1';
        slides[currentSlideIndex].style.zIndex = '10';
        slides[currentSlideIndex].style.transform = 'scale(1)';
    }
    
    // Update dots
    dots.forEach((dot, i) => {
        dot.classList.remove('active');
        if (i === currentSlideIndex) {
            dot.classList.add('active');
        }
    });
}

function nextSlide() {
    const slides = document.querySelectorAll('#slideshow .slide');
    const nextIndex = (currentSlideIndex + 1) % slides.length;
    goToSlide(nextIndex);
}

function startSlideshow() {
    clearInterval(slideshowTimer);
    slideshowTimer = setInterval(nextSlide, 5000); // Change slide every 5 seconds
}

// Global function for dot clicks
function currentSlide(index) {
    goToSlide(index);
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', initSimpleSlideshow);
