/**
 * الطاقة الإيجابية - Positive Energy
 * Main JavaScript File
 */

document.addEventListener('DOMContentLoaded', function() {
    // Variables
    const header = document.getElementById('header');
    const menuToggle = document.querySelector('.menu-toggle');
    const menu = document.querySelector('.menu');
    const testimonialSlides = document.querySelectorAll('.testimonial-slide');
    const dots = document.querySelectorAll('.dot');
    let currentSlide = 0;
    
    // Sticky Header on Scroll
    window.addEventListener('scroll', function() {
        if (window.scrollY > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
    
    // Mobile Menu Toggle
    menuToggle.addEventListener('click', function() {
        menu.classList.toggle('active');
        
        // Change icon based on menu state
        if (menu.classList.contains('active')) {
            menuToggle.innerHTML = '<i class="fas fa-times"></i>';
        } else {
            menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
        }
    });
    
    // Close menu when clicking on a link (mobile)
    document.querySelectorAll('.menu a').forEach(function(link) {
        link.addEventListener('click', function() {
            if (menu.classList.contains('active')) {
                menu.classList.remove('active');
                menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
            }
        });
    });
    
    // Testimonial Slider
    function showSlide(n) {
        // Hide all slides
        testimonialSlides.forEach(slide => {
            slide.classList.remove('active');
        });
        
        // Remove active class from all dots
        dots.forEach(dot => {
            dot.classList.remove('active');
        });
        
        // Show current slide and activate current dot
        testimonialSlides[n].classList.add('active');
        dots[n].classList.add('active');
        currentSlide = n;
    }
    
    // Initialize slider
    showSlide(currentSlide);
    
    // Dot controls
    dots.forEach(dot => {
        dot.addEventListener('click', function() {
            const slideIndex = parseInt(this.getAttribute('data-slide'));
            showSlide(slideIndex);
        });
    });
    
    // Auto slide change
    setInterval(function() {
        currentSlide = (currentSlide + 1) % testimonialSlides.length;
        showSlide(currentSlide);
    }, 5000);
    
    // Animate elements on scroll
    const animateElements = document.querySelectorAll('.service-card, .testimonial-card, .instagram-item');
    
    function checkIfInView() {
        animateElements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const elementBottom = element.getBoundingClientRect().bottom;
            const isVisible = (elementTop >= 0) && (elementBottom <= window.innerHeight);
            
            if (isVisible) {
                element.classList.add('animated');
            }
        });
    }
    
    // Check on load
    checkIfInView();
    
    // Check on scroll
    window.addEventListener('scroll', checkIfInView);
    
    // Form validation for contact form (if exists on page)
    const contactForm = document.querySelector('.contact-form');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Basic validation
            const nameField = document.getElementById('name');
            const emailField = document.getElementById('email');
            const messageField = document.getElementById('message');
            let isValid = true;
            
            // Reset error messages
            document.querySelectorAll('.error-message').forEach(error => {
                error.remove();
            });
            
            // Validate name
            if (!nameField.value.trim()) {
                showError(nameField, 'الرجاء إدخال الاسم');
                isValid = false;
            }
            
            // Validate email
            if (!emailField.value.trim() || !isValidEmail(emailField.value)) {
                showError(emailField, 'الرجاء إدخال بريد إلكتروني صحيح');
                isValid = false;
            }
            
            // Validate message
            if (!messageField.value.trim()) {
                showError(messageField, 'الرجاء إدخال رسالتك');
                isValid = false;
            }
            
            // If valid, show success message (in real environment, submit to server)
            if (isValid) {
                const successMessage = document.createElement('div');
                successMessage.className = 'success-message';
                successMessage.textContent = 'تم إرسال رسالتك بنجاح! سنتواصل معك قريباً.';
                contactForm.appendChild(successMessage);
                
                // Reset form
                contactForm.reset();
                
                // Remove success message after 5 seconds
                setTimeout(() => {
                    successMessage.remove();
                }, 5000);
            }
        });
        
        // Helper function to show error messages
        function showError(field, message) {
            const errorMessage = document.createElement('div');
            errorMessage.className = 'error-message';
            errorMessage.textContent = message;
            field.parentNode.appendChild(errorMessage);
            field.classList.add('error');
            
            // Remove error class when user types
            field.addEventListener('input', function() {
                field.classList.remove('error');
                const errorElement = field.parentNode.querySelector('.error-message');
                if (errorElement) {
                    errorElement.remove();
                }
            });
        }
        
        // Helper function to validate email
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
    }
});
