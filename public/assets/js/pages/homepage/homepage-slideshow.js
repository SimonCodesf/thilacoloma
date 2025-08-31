// Single homepage slideshow script for Craft CMS
let currentSlide = 0;
let slideInterval;
let isTransitioning = false;

document.addEventListener('DOMContentLoaded', function() {
    initializeSlideshow();
});

function initializeSlideshow() {
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    if (slides.length === 0) return;

    slides.forEach((slide, i) => {
        slide.style.position = 'absolute';
        slide.style.top = '0';
        slide.style.left = '0';
        slide.style.width = '100%';
        slide.style.display = 'flex';
        // Shift cards more for a stronger stack effect
        if (i === 0) {
            slide.style.opacity = '1';
            slide.style.zIndex = '2';
            slide.style.transform = 'translateX(0) scale(1)';
        } else if (i === 1) {
            slide.style.opacity = '1';
            slide.style.zIndex = '1';
            slide.style.transform = 'translateX(40px) scale(0.93)';
        } else if (i === 2) {
            slide.style.opacity = '1';
            slide.style.zIndex = '0';
            slide.style.transform = 'translateX(80px) scale(0.87)';
        } else {
            slide.style.opacity = '0';
            slide.style.zIndex = '-1';
            slide.style.transform = 'translateX(0) scale(0.8)';
        }
        // Clicking any card proceeds to the next one
        slide.addEventListener('click', function(e) {
            if (!isTransitioning && !e.target.classList.contains('meer-button')) {
                nextSlide();
            }
        });
    });

    dots.forEach((dot, index) => {
        dot.classList.toggle('active', index === 0);
        dot.addEventListener('click', () => goToSlide(index));
    });
    currentSlide = 0;
    startAutoRotation();
    const slideshow = document.getElementById('slideshow');
    if (slideshow) {
        slideshow.addEventListener('mouseenter', stopAutoRotation);
        slideshow.addEventListener('mouseleave', startAutoRotation);
    }
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowRight') nextSlide();
        else if (e.key === 'ArrowLeft') prevSlide();
    });
}

function showSlide(index) {
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    if (slides.length === 0) return;
    slides.forEach((slide, i) => {
        if (i === index) {
            slide.style.transition = 'transform 0.6s, opacity 0.6s';
            slide.style.opacity = '1';
            slide.style.zIndex = '2';
            slide.style.transform = 'translateX(0) scale(1)';
        } else if (i === (index + 1) % slides.length) {
            slide.style.transition = 'transform 0.6s, opacity 0.6s';
            slide.style.opacity = '0.95';
            slide.style.zIndex = '1';
            slide.style.transform = 'translateX(40px) scale(0.93)';
        } else if (i === (index + 2) % slides.length) {
            slide.style.transition = 'transform 0.6s, opacity 0.6s';
            slide.style.opacity = '0.9';
            slide.style.zIndex = '0';
            slide.style.transform = 'translateX(80px) scale(0.87)';
        } else {
            slide.style.transition = 'transform 0.6s, opacity 0.6s';
            slide.style.opacity = '0';
            slide.style.zIndex = '-1';
            slide.style.transform = 'translateX(0) scale(0.8)';
        }
    });
    dots.forEach((dot, i) => {
        dot.classList.toggle('active', i === index);
    });
    isTransitioning = true;
    setTimeout(() => { isTransitioning = false; }, 600);
}

function goToSlide(index) {
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

function startAutoRotation() {
    stopAutoRotation();
    slideInterval = setInterval(nextSlide, 8000);
}

function stopAutoRotation() {
    if (slideInterval) {
        clearInterval(slideInterval);
        slideInterval = null;
    }
}

// "Meer info" button interactivity

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.meer-button').forEach((button) => {
        button.addEventListener('click', function() {
            const slideTitle = button.closest('.slide').querySelector('h2').textContent;
            alert('Meer informatie over: ' + slideTitle);
        });
    });
});

// Add enhanced card stack styles
const style = document.createElement('style');
style.textContent = `
    .dot { cursor: pointer; transition: all 0.3s; transform: scale(1); }
    .dot.active { background-color: var(--primary-color) !important; transform: scale(1.2); }
    .dot:hover { background-color: var(--pink-color) !important; transform: scale(1.1); }
    .slide { box-shadow: 0 8px 32px rgba(0,0,0,0.12) !important; border: 1px solid rgba(55,71,148,0.08); }
    .slide:nth-child(1) { box-shadow: 0 12px 40px rgba(55,71,148,0.15) !important; }
    .slide:nth-child(2) { box-shadow: 0 10px 35px rgba(192,88,154,0.12) !important; }
    .slide:nth-child(3) { box-shadow: 0 8px 30px rgba(230,166,48,0.1) !important; }
`;
document.head.appendChild(style);
