// Simple slideshow test
console.log('Testing slideshow...');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    
    console.log('Found slides:', slides.length);
    console.log('Found dots:', dots.length);
    
    // Add visual debug - make all slides visible first
    slides.forEach((slide, i) => {
        slide.style.border = '2px solid red';
        slide.style.minHeight = '200px';
        console.log('Slide', i, slide);
    });
    
    if (slides.length === 0) {
        console.error('No slides found!');
        return;
    }
    
    let currentSlide = 0;
    
    function showSlide(index) {
        console.log('Showing slide:', index);
        
        // Hide all slides
        slides.forEach((slide, i) => {
            if (i === index) {
                slide.style.display = 'flex';
                slide.style.opacity = '1';
                slide.style.border = '2px solid green';
            } else {
                slide.style.display = 'none';
                slide.style.opacity = '0';
                slide.style.border = '2px solid red';
            }
        });
        
        // Update dots
        dots.forEach((dot, i) => {
            if (i === index) {
                dot.style.backgroundColor = 'red';
            } else {
                dot.style.backgroundColor = 'gray';
            }
        });
    }
    
    // Initialize
    showSlide(0);
    
    // Auto advance
    setInterval(() => {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
    }, 3000); // Faster for testing
    
    // Make currentSlide function global for dots
    window.currentSlide = function(index) {
        console.log('Dot clicked:', index);
        currentSlide = index;
        showSlide(index);
    };
    
    console.log('Slideshow initialized');
});
