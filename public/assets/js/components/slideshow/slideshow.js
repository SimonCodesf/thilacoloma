/**
 * Slideshow Component
 * Handles slideshow functionality with data-driven content
 */
class Slideshow {
    constructor(containerId, dataUrl = null) {
        this.container = document.getElementById(containerId);
        this.currentSlide = 0;
        this.slides = [];
        this.dots = [];
        this.isTransitioning = false;
        this.slideInterval = null;
        this.dataUrl = dataUrl;
        
        this.init();
    }

    async init() {
        try {
            if (this.dataUrl) {
                await this.loadData();
            } else {
                this.slides = document.querySelectorAll('#slideshow .slide');
                this.dots = document.querySelectorAll('.dot');
            }
            
            this.setupSlides();
            this.setupEventListeners();
            this.startSlideshow();
            this.showSlide(this.currentSlide);
        } catch (error) {
            console.error('Slideshow initialization failed:', error);
            // Fallback to existing HTML slides
            this.slides = document.querySelectorAll('#slideshow .slide');
            this.dots = document.querySelectorAll('.dot');
            this.setupSlides();
            this.setupEventListeners();
            this.startSlideshow();
        }
    }

    async loadData() {
        try {
            const response = await fetch(this.dataUrl);
            const data = await response.json();
            this.renderSlides(data.slides);
        } catch (error) {
            console.warn('Could not load slideshow data, using fallback:', error);
        }
    }

    renderSlides(slidesData) {
        // This method could be used when implementing CMS integration
        // For now, we'll work with existing HTML structure
        this.slides = document.querySelectorAll('#slideshow .slide');
        this.dots = document.querySelectorAll('.dot');
    }

    setupSlides() {
        // Set initial positions without transition
        this.slides.forEach((slide, i) => {
            slide.style.position = 'absolute';
            slide.style.top = '0';
            slide.style.left = '0';
            slide.style.width = '100%';
            
            if (i === 0) {
                slide.style.opacity = '1';
                slide.style.zIndex = '2';
                slide.style.transform = 'translateX(0) scale(1)';
            } else if (i === 1) {
                slide.style.opacity = '1';
                slide.style.zIndex = '1';
                slide.style.transform = 'translateX(20px) scale(0.9)';
            } else if (i === 2) {
                slide.style.opacity = '1';
                slide.style.zIndex = '0';
                slide.style.transform = 'translateX(40px) scale(0.8)';
            } else {
                slide.style.opacity = '0';
                slide.style.zIndex = '-1';
                slide.style.transform = 'translateX(0) scale(0.8)';
            }
        });
    }

    showSlide(index) {
        this.slides.forEach((slide, i) => {
            if (i === index) {
                slide.style.transition = 'transform 0.25s ease-in-out, opacity 0.5s ease-out';
                slide.style.opacity = '1';
                slide.style.zIndex = '2';
                slide.style.transform = 'translateX(0) scale(1)';
            } else if (i === (index + 1) % this.slides.length) {
                slide.style.transition = 'transform 0.6s ease-out, opacity 1s ease-out';
                slide.style.opacity = '0.95';
                slide.style.zIndex = '1';
                slide.style.transform = 'translateX(40px) scale(0.95)';
            } else if (i === (index + 2) % this.slides.length) {
                slide.style.transition = 'transform 0.8s ease-out, opacity 1s ease-out';            
                slide.style.opacity = '1';
                slide.style.zIndex = '0';
                slide.style.transform = 'translateX(75px) scale(0.9)';
            } else {
                slide.style.transition = 'none';
                slide.style.opacity = '0';
                slide.style.zIndex = '-1';
                slide.style.transform = 'translateX(0) scale(0.8)';
            }
        });
        
        this.dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
        });
        
        this.isTransitioning = true;
        setTimeout(() => {
            this.isTransitioning = false;
        }, 500);
    }

    nextSlide() {
        if (!this.isTransitioning) {
            this.currentSlide = (this.currentSlide + 1) % this.slides.length;
            this.showSlide(this.currentSlide);
        }
    }

    prevSlide() {
        if (!this.isTransitioning) {
            this.currentSlide = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
            this.showSlide(this.currentSlide);
        }
    }

    goToSlide(index) {
        if (!this.isTransitioning && index >= 0 && index < this.slides.length) {
            this.currentSlide = index;
            this.showSlide(this.currentSlide);
        }
    }

    startSlideshow() {
        this.slideInterval = setInterval(() => this.nextSlide(), 15000);
    }

    stopSlideshow() {
        if (this.slideInterval) {
            clearInterval(this.slideInterval);
            this.slideInterval = null;
        }
    }

    restartSlideshow() {
        this.stopSlideshow();
        this.startSlideshow();
    }

    setupEventListeners() {
        // Touch and click events for slides
        this.slides.forEach((slide) => {
            const slideImage = slide.querySelector('img');
            if (slideImage) {
                slideImage.addEventListener('dragstart', (e) => e.preventDefault());
            }

            slide.addEventListener('touchstart', () => this.nextSlide());
            slide.addEventListener('click', () => this.nextSlide());
        });

        // Pause on hover
        if (this.container) {
            this.container.addEventListener('mouseenter', () => this.stopSlideshow());
            this.container.addEventListener('mouseleave', () => this.restartSlideshow());
        }

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowRight') {
                this.nextSlide();
            } else if (e.key === 'ArrowLeft') {
                this.prevSlide();
            }
        });

        // Dot navigation
        this.dots.forEach((dot, index) => {
            dot.addEventListener('click', () => this.goToSlide(index));
        });
    }
}

// Initialize slideshow when DOM is loaded (only if news-slideshow hasn't already handled it)
document.addEventListener('DOMContentLoaded', () => {
    // Check if the simple slideshow from news-slideshow.js is already active
    if (window.slideshowInitialized) {
        return;
    }
    
    const slideshow = new Slideshow('slideshow', 'data/slideshow.json');
    
    // Make slideshow globally accessible for debugging
    window.slideshow = slideshow;
});