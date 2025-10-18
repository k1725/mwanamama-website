document.addEventListener('DOMContentLoaded', () => {
    
    // --- Configuration: List of Partials to Load ---
    // The order matters! They will be loaded and inserted sequentially.
    const partials = [
        { id: 'nav-placeholder', file: 'html-partials/nav.html' },
        { id: 'content-placeholder', file: 'html-partials/hero.html' },
        { id: 'content-placeholder', file: 'html-partials/why-choose-us.html' },
        { id: 'content-placeholder', file: 'html-partials/about.html' },
        { id: 'content-placeholder', file: 'html-partials/services.html' },
        { id: 'content-placeholder', file: 'html-partials/how-it-works.html' },
        { id: 'content-placeholder', file: 'html-partials/testimonials.html' },
        { id: 'content-placeholder', file: 'html-partials/faqs.html' },
        { id: 'content-placeholder', file: 'html-partials/contact.html' },
        { id: 'footer-placeholder', file: 'html-partials/footer.html' }
    ];

    // --- Core Function to Load Partials ---
    async function loadPartials() {
        for (const partial of partials) {
            try {
                const response = await fetch(partial.file);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status} for ${partial.file}`);
                }
                const content = await response.text();
                const container = document.getElementById(partial.id);
                
                if (container) {
                    // Append content to the placeholder (important for content-placeholder)
                    container.insertAdjacentHTML('beforeend', content);
                } else {
                    console.error(`Placeholder element #${partial.id} not found.`);
                }
            } catch (error) {
                console.error('Error loading partial:', error);
            }
        }
        
        // --- IMPORTANT: Run all other setup functions AFTER content is loaded ---
        initializeFeatures();
    }
    
    // --- Main Initialization Function ---
    function initializeFeatures() {
        // Selectors must be run after the content is injected
        const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
        const sections = document.querySelectorAll('section[id]');
        const navbar = document.querySelector('.navbar');
        const contactForm = document.getElementById('contactForm');
        
        // Check if essential elements exist
        if (!navbar || !contactForm) {
            console.error("Essential elements not found after partials loading.");
            return;
        }

        const navHeight = navbar.offsetHeight;
        const submitBtn = document.querySelector('#contactForm button[type="submit"]');
        const submitText = document.getElementById('submitText');
        const submitSpinner = document.getElementById('submitSpinner');
        const formMessage = document.getElementById('formMessage');

        // --- Smooth Scrolling for Navigation Links ---
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const targetPosition = target.offsetTop - navHeight;
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                    
                    // Close mobile menu if open
                    const navCollapse = document.querySelector('.navbar-collapse');
                    if (navCollapse && navCollapse.classList.contains('show')) {
                        const bsCollapse = new bootstrap.Collapse(navCollapse, { toggle: false });
                        bsCollapse.hide();
                    }
                }
            });
        });

        // --- Active Navigation Link on Scroll & Navbar Shadow ---
        const updateNavAndShadow = () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop - navHeight - 100;
                if (window.scrollY >= sectionTop) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });

            if (window.scrollY > 50) {
                navbar.style.boxShadow = '0 5px 20px rgba(0,0,0,0.15)';
            } else {
                navbar.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
            }
        };

        window.addEventListener('scroll', updateNavAndShadow);
        window.addEventListener('load', updateNavAndShadow);

        // --- Contact Form Submission (Simulated) ---
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            submitBtn.disabled = true;
            submitSpinner.style.display = 'inline-block';
            submitText.textContent = 'Sending...';
            formMessage.innerHTML = '';
            
            // Simulate AJAX request
            setTimeout(() => {
                formMessage.innerHTML = `
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <strong>Thank you!</strong> Your message has been sent successfully. We'll get back to you soon.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                
                contactForm.reset();
                
                // Reset button state
                submitBtn.disabled = false;
                submitSpinner.style.display = 'none';
                submitText.textContent = 'Send Message';
                
            }, 1500);
        });

        // --- On-Scroll Animations (Intersection Observer) ---
        const animatedElements = document.querySelectorAll('.animate-on-scroll');

        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        animatedElements.forEach(el => {
            observer.observe(el);
        });
        
        // --- Back to Top Functionality (Ensure the button is injected if not already) ---
        let backToTop = document.getElementById('backToTopButton');
        if (!backToTop) {
            backToTop = document.createElement('button');
            backToTop.setAttribute('id', 'backToTopButton');
            backToTop.innerHTML = '<i class="bi bi-arrow-up"></i>';
            document.body.appendChild(backToTop);
            
            // Set initial styles (styles for this button are in style.css)
            backToTop.style.position = 'fixed';
            backToTop.style.bottom = '30px';
            backToTop.style.right = '30px';
            backToTop.style.width = '50px';
            backToTop.style.height = '50px';
            backToTop.style.borderRadius = '50%';
            backToTop.style.border = 'none';
            backToTop.style.zIndex = '1000';
            backToTop.style.opacity = '0';
            backToTop.style.visibility = 'hidden';
            backToTop.style.transition = 'all 0.3s ease';
        }

        backToTop.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTop.style.opacity = '1';
                backToTop.style.visibility = 'visible';
            } else {
                backToTop.style.opacity = '0';
                backToTop.style.visibility = 'hidden';
            }
        });
    } // End of initializeFeatures

    // Start the process
    loadPartials();
});