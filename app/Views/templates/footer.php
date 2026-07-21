<!-- Footer -->
<footer class="bg-dark text-white py-5 mt-auto">
    <div class="container">
        <div class="row g-4">
            <!-- Company Info -->
            <div class="col-lg-3 mb-4 mb-lg-0">
                <h4 class="fw-bold mb-3">
                    <i class="fas fa-hard-hat text-accent"></i> 
                    <span class="text-gradient">CDW ENGINEERING</span>
                </h4>
                <p class="mb-3 text-white-50">
                    Leading engineering solutions for Indonesia's future. 
                    Providing tomorrow's solutions today.
                </p>
                <div class="d-flex gap-3">
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle p-2">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle p-2">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle p-2">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle p-2">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="col-lg-2 mb-4 mb-lg-0">
                <h5 class="fw-bold mb-3">Quick Links</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="<?= base_url() ?>" class="text-white-50 text-decoration-none hover-orange">Home</a></li>
                    <li class="mb-2"><a href="<?= base_url('about') ?>" class="text-white-50 text-decoration-none hover-orange">About Us</a></li>
                    <li class="mb-2"><a href="<?= base_url('services') ?>" class="text-white-50 text-decoration-none hover-orange">Services</a></li>
                    <li class="mb-2"><a href="<?= base_url('projects') ?>" class="text-white-50 text-decoration-none hover-orange">Projects</a></li>
                    <li class="mb-2"><a href="<?= base_url('products') ?>" class="text-white-50 text-decoration-none hover-orange">Products</a></li>
                </ul>
            </div>
            
            <!-- Contact Info -->
            <div class="col-lg-3 mb-4 mb-lg-0">
                <h5 class="fw-bold mb-3">Visit Us</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-building text-accent me-2"></i>
                        <span class="text-white-50">Head Office</span>
                    </li>
                    <li class="mb-2 ps-3">
                        <small class="text-white-50">
                            PT. CIPTA DUTA WACANA<br>
                            Beltway Office Park, Ragunan<br>
                            Jakarta Selatan
                        </small>
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-chart-line text-accent me-2"></i>
                        <span class="text-white-50">Marketing Office</span>
                    </li>
                    <li class="mb-2 ps-3">
                        <small class="text-white-50">
                            Villa Bintaro Regency Blok K1 No 2<br>
                            Pondok Kacang Timur<br>
                            Tangerang Selatan 15226
                        </small>
                    </li>
                </ul>
            </div>
            
            <!-- Contact Details -->
            <div class="col-lg-4">
                <h5 class="fw-bold mb-3">Contact Us</h5>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-phone text-accent mt-1 me-2"></i>
                            <div>
                                <p class="mb-1 fw-semibold">Call Us</p>
                                <p class="mb-1 text-white-50">
                                    Head Office: <a href="tel:+622112345678" class="text-white-50 text-decoration-none hover-orange">+62 21 1234 5678</a>
                                </p>
                                <p class="mb-0 text-white-50">
                                    Marketing: <a href="tel:+622187654321" class="text-white-50 text-decoration-none hover-orange">+62 21 8765 4321</a>
                                </p>
                            </div>
                        </div>
                    </li>
                    
                    <li class="mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-envelope text-accent mt-1 me-2"></i>
                            <div>
                                <p class="mb-1 fw-semibold">Email Us</p>
                                <p class="mb-1 text-white-50">
                                    <a href="mailto:corporate@cdw-engineering.co.id" class="text-white-50 text-decoration-none hover-orange">
                                        corporate@cdw-engineering.co.id
                                    </a>
                                </p>
                                <p class="mb-0 text-white-50">
                                    <a href="mailto:sales@cdw-engineering.co.id" class="text-white-50 text-decoration-none hover-orange">
                                        sales@cdw-engineering.co.id
                                    </a>
                                </p>
                            </div>
                        </div>
                    </li>
                    
                    <li>
                        <div class="d-flex align-items-start">
                            <i class="fas fa-clock text-accent mt-1 me-2"></i>
                            <div>
                                <p class="mb-1 fw-semibold">Business Hours</p>
                                <p class="mb-1 text-white-50">Monday - Friday</p>
                                <p class="mb-0 text-white-50">8:00 AM - 5:00 PM</p>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        
        <hr class="my-4 border-light opacity-25">
        
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="mb-0">
                    <i class="fas fa-copyright text-accent me-1"></i>
                    <?= date('Y') ?> PT. Cipta Duta Wacana. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-0">
                    <i class="fas fa-code text-accent me-1"></i>
                    Powered by CodeIgniter 4
                </p>
            </div>
        </div>
    </div>
</footer>

<style>
/* Footer Specific Styles */
.bg-dark {
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%) !important;
}

.text-gradient {
    background: var(--cdw-orange-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.text-accent {
    color: var(--cdw-orange-primary) !important;
}

.hover-orange:hover {
    color: var(--cdw-orange-primary) !important;
    text-decoration: underline !important;
}

.btn-outline-light:hover {
    background: var(--cdw-orange-gradient) !important;
    border-color: var(--cdw-orange-primary) !important;
}

/* Responsive Footer */
@media (max-width: 991.98px) {
    .footer .row > div {
        margin-bottom: 2rem;
    }
    
    .footer .row > div:last-child {
        margin-bottom: 0;
    }
}

@media (max-width: 767.98px) {
    .footer {
        text-align: center;
    }
    
    .footer .d-flex {
        justify-content: center;
    }
    
    .footer .ps-3 {
        padding-left: 0 !important;
    }
    
    .footer .text-md-end {
        text-align: center !important;
        margin-top: 1rem;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Smooth scroll untuk semua anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if(target) {
                target.scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Navbar scroll effect
    let lastScroll = 0;
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
        
        if (currentScroll > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
        
        lastScroll = currentScroll;
    });

    // Initialize navbar scroll on load
    document.addEventListener('DOMContentLoaded', function() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        }
        
        // Active link highlight
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            // Pastikan handling untuk root path
            if (currentPath === '/' && href === '/') {
                link.classList.add('active');
            } else if (href !== '/' && currentPath.includes(href.replace('/', ''))) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    });

    // Footer social media interaction
    document.querySelectorAll('.btn-outline-light').forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
            this.style.transition = 'all 0.3s ease';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Back to top button functionality
    const backToTopButton = document.createElement('button');
    backToTopButton.innerHTML = '<i class="fas fa-chevron-up"></i>';
    backToTopButton.className = 'btn btn-orange btn-lg rounded-circle position-fixed';
    backToTopButton.style.cssText = 'bottom: 20px; right: 20px; z-index: 1000; display: none; width: 50px; height: 50px;';
    backToTopButton.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
    document.body.appendChild(backToTopButton);

    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            backToTopButton.style.display = 'block';
        } else {
            backToTopButton.style.display = 'none';
        }
    });

    // Footer link hover effects
    document.querySelectorAll('footer a').forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.3s ease';
        });
    });

    // Current year update
    document.addEventListener('DOMContentLoaded', function() {
        const yearElement = document.querySelector('.current-year');
        if (yearElement) {
            yearElement.textContent = new Date().getFullYear();
        }
    });
</script>
</body>
</html>