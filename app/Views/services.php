<!-- Services Hero Section with Background Image -->
<section class="services-hero-section" style="background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1581092160562-40aa08e78837?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80') center/cover fixed; color: white; padding: 120px 0; position: relative;">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-4 animate__animated animate__fadeInDown">Our <span class="text-gradient-light">Services</span></h1>
        <p class="lead mb-5 animate__animated animate__fadeInUp">
            Comprehensive engineering solutions tailored to your industrial needs. 
            From conceptual design to turnkey implementation.
        </p>
        
        <!-- Services Stats with Gradient Cards -->
        <div class="services-stats animate__animated animate__fadeInUp animate__delay-1s">
            <div class="row g-4 justify-content-center">
                <div class="col-6 col-md-3">
                    <div class="stat-card gradient-card">
                        <div class="stat-number display-6 fw-bold text-white">4</div>
                        <div class="stat-label text-white-50">Service Categories</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card gradient-card">
                        <div class="stat-number display-6 fw-bold text-white">15+</div>
                        <div class="stat-label text-white-50">Years Experience</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card gradient-card">
                        <div class="stat-number display-6 fw-bold text-white">150+</div>
                        <div class="stat-label text-white-50">Projects Completed</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card gradient-card">
                        <div class="stat-number display-6 fw-bold text-white">100%</div>
                        <div class="stat-label text-white-50">Quality Assurance</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Overview -->
<section class="py-5">
    <div class="container">
        <div class="section-header mb-5">
            <h2 class="section-title">Comprehensive <span class="text-gradient">Solutions</span></h2>
            <p class="section-subtitle">
                We provide end-to-end engineering services to meet your project requirements
            </p>
        </div>
        
        <!-- Services Grid -->
        <div class="row g-4" id="servicesGrid">
            <?php foreach($services as $index => $service): ?>
            <div class="col-lg-3 col-md-6">
                <a href="#<?= $service['slug'] ?>" class="service-card-link text-decoration-none">
                    <div class="service-card gradient-card" data-service="<?= $service['slug'] ?>">
                        <div class="service-icon bg-white">
                            <i class="<?= $service['icon'] ?> fa-3x text-gradient"></i>
                        </div>
                        <div class="service-content">
                            <h4 class="service-title text-white"><?= $service['short_name'] ?></h4>
                            <p class="service-description text-white-50"><?= $service['short_description'] ?></p>
                            <div class="service-action">
                                <span class="btn-service text-white">Learn More <i class="fas fa-arrow-right ms-2"></i></span>
                            </div>
                        </div>
                        <div class="service-number text-white-50">0<?= $index + 1 ?></div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Service Details Sections -->
<?php foreach($services as $service): ?>
<section id="<?= $service['slug'] ?>" class="service-detail-section py-5 <?= $service['slug'] == 'engineering' ? '' : 'bg-gray-light' ?>">
    <div class="container">
        <div class="row align-items-center">
            <!-- Service Content -->
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="service-header mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="service-icon-sm gradient-card me-3">
                            <i class="<?= $service['icon'] ?> fa-2x text-white"></i>
                        </div>
                        <h2 class="mb-0"><?= $service['name'] ?></h2>
                    </div>
                    <p class="lead"><?= $service['short_description'] ?></p>
                </div>
                
                <div class="service-description-full mb-4">
                    <p><?= $service['full_description'] ?></p>
                </div>
                
                <!-- Service Features -->
                <div class="service-features mb-4">
                    <h4 class="mb-3">Key Features</h4>
                    <div class="row">
                        <?php foreach(array_chunk($service['features'], 2) as $features): ?>
                        <div class="col-md-6">
                            <?php foreach($features as $feature): ?>
                            <div class="feature-item gradient-card mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="feature-icon-wrapper bg-white me-3">
                                        <i class="<?= $feature['icon'] ?> text-gradient"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 text-white"><?= $feature['title'] ?></h6>
                                        <p class="mb-0 text-white-50 small"><?= $feature['description'] ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Service Projects -->
                <div class="service-projects">
                    <h4 class="mb-3">Related Projects</h4>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach($service['projects'] as $project): ?>
                        <span class="badge gradient-card text-white"><?= $project ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- CTA Buttons -->
                <div class="service-cta mt-4">
                    <a href="#contact" class="btn btn-orange me-3">
                        <i class="fas fa-phone me-2"></i>Request Quote
                    </a>
                    <a href="/projects" class="btn btn-outline-orange">
                        <i class="fas fa-project-diagram me-2"></i>View Projects
                    </a>
                </div>
            </div>
            
            <!-- Service Image -->
            <div class="col-lg-6">
                <div class="service-image-wrapper">
                    <img src="<?= $service['image'] ?>" 
                         alt="<?= $service['image_alt'] ?>" 
                         class="img-fluid rounded-xl">
                    <div class="service-image-overlay gradient-card">
                        <div class="overlay-content">
                            <h5 class="text-white mb-2">Expertise in <?= $service['short_name'] ?></h5>
                            <p class="text-white-50 mb-0">Years of experience delivering quality results</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endforeach; ?>

<!-- Why Choose Us -->
<section class="py-5 bg-dark text-white">
    <div class="container">
        <div class="section-header mb-5 text-center">
            <h2 class="section-title text-white">Why <span class="text-gradient-light">Choose Us</span></h2>
            <p class="section-subtitle text-white-50">
                Our commitment to excellence sets us apart in the engineering industry
            </p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-3">
                <div class="why-card gradient-card text-center p-4">
                    <div class="why-icon mb-3 bg-white">
                        <i class="fas fa-award fa-3x text-gradient"></i>
                    </div>
                    <h5 class="mb-3 text-white">Quality Assurance</h5>
                    <p class="text-white-50 mb-0">Rigorous quality control and testing procedures</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="why-card gradient-card text-center p-4">
                    <div class="why-icon mb-3 bg-white">
                        <i class="fas fa-clock fa-3x text-gradient"></i>
                    </div>
                    <h5 class="mb-3 text-white">Timely Delivery</h5>
                    <p class="text-white-50 mb-0">On-time project completion with efficient scheduling</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="why-card gradient-card text-center p-4">
                    <div class="why-icon mb-3 bg-white">
                        <i class="fas fa-users fa-3x text-gradient"></i>
                    </div>
                    <h5 class="mb-3 text-white">Expert Team</h5>
                    <p class="text-white-50 mb-0">Experienced engineers and technicians</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="why-card gradient-card text-center p-4">
                    <div class="why-icon mb-3 bg-white">
                        <i class="fas fa-shield-alt fa-3x text-gradient"></i>
                    </div>
                    <h5 class="mb-3 text-white">Safety First</h5>
                    <p class="text-white-50 mb-0">Comprehensive safety protocols and training</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Process Section -->
<section class="py-5">
    <div class="container">
        <div class="section-header mb-5">
            <h2 class="section-title">Our <span class="text-gradient">Process</span></h2>
            <p class="section-subtitle">
                A systematic approach to ensure project success
            </p>
        </div>
        
        <div class="process-timeline">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="process-step text-center">
                        <div class="step-number gradient-card">01</div>
                        <h5 class="mt-3 mb-2">Consultation</h5>
                        <p class="text-muted">Understanding your requirements and objectives</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="process-step text-center">
                        <div class="step-number gradient-card">02</div>
                        <h5 class="mt-3 mb-2">Planning</h5>
                        <p class="text-muted">Detailed project planning and design</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="process-step text-center">
                        <div class="step-number gradient-card">03</div>
                        <h5 class="mt-3 mb-2">Execution</h5>
                        <p class="text-muted">Precise implementation with quality control</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="process-step text-center">
                        <div class="step-number gradient-card">04</div>
                        <h5 class="mt-3 mb-2">Support</h5>
                        <p class="text-muted">Ongoing maintenance and technical support</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-gradient-orange text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h2 class="display-5 fw-bold mb-3">Ready to Start Your Project?</h2>
                <p class="lead mb-0">Contact us today for a free consultation and quote</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="/contact" class="btn btn-light btn-lg">
                    <i class="fas fa-envelope me-2"></i>Contact Us
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Services CSS - Updated -->
<style>
    :root {
        --cdw-orange-primary: #ce7303;
        --cdw-orange-gradient: linear-gradient(135deg, #ce7303 0%, #ccad01 100%);
        --cdw-gray-light: #f8f9fa;
        --cdw-gray-medium: #6c757d;
        --cdw-gray-dark: #343a40;
    }

    /* Gradient Card Class */
    .gradient-card {
        background: var(--cdw-orange-gradient) !important;
        border: none !important;
        box-shadow: 0 10px 30px rgba(206, 115, 3, 0.3) !important;
        transition: all 0.3s ease !important;
    }

    .gradient-card:hover {
        box-shadow: 0 15px 40px rgba(206, 115, 3, 0.5) !important;
        transform: translateY(-5px);
    }

    /* Services Hero with Background Image */
    .services-hero-section {
        padding: 150px 0 100px;
        position: relative;
        color: white;
        overflow: hidden;
    }

    .services-hero-section h1 {
        text-shadow: 2px 2px 15px rgba(0, 0, 0, 0.5);
        animation: fadeInDown 1s ease-out;
    }

    .services-hero-section .lead {
        text-shadow: 1px 1px 10px rgba(0, 0, 0, 0.5);
        font-size: 1.3rem;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
        animation: fadeInUp 1s ease-out 0.3s both;
    }

    .services-hero-section .services-stats {
        animation: fadeInUp 1s ease-out 0.6s both;
    }

    /* Light text gradient untuk hero section */
    .text-gradient-light {
        background: linear-gradient(135deg, #ffffff 0%, #ffd966 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-shadow: none;
    }

    /* Stat Cards */
    .stat-card {
        padding: 2rem 1.5rem;
        border-radius: 15px;
        text-align: center;
        height: 100%;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        background: var(--cdw-orange-gradient) !important;
        border: none !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
    }

    .stat-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4) !important;
    }

    .stat-number {
        line-height: 1;
        margin-bottom: 0.75rem;
    }

    .stat-label {
        font-weight: 500;
        font-size: 0.95rem;
    }

    /* Text Colors */
    .text-white-50 {
        color: rgba(255, 255, 255, 0.8) !important;
    }

    /* Service Cards */
    .service-card {
        border-radius: 15px;
        padding: 2rem 1.5rem;
        height: 100%;
        position: relative;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        text-align: center;
        cursor: pointer;
    }

    .service-card-link:hover .service-card {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(206, 115, 3, 0.4) !important;
    }

    .service-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    .service-card:hover .service-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .service-title {
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .service-description {
        font-size: 0.95rem;
        margin-bottom: 1.5rem;
        line-height: 1.5;
    }

    .service-action .btn-service {
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        transition: all 0.3s ease;
    }

    .service-card:hover .btn-service {
        transform: translateX(8px);
    }

    .service-number {
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 2.5rem;
        font-weight: 800;
        opacity: 0.2;
        line-height: 1;
    }

    /* Service Detail Sections */
    .service-detail-section {
        scroll-margin-top: 100px;
    }

    .service-header h2 {
        color: var(--cdw-gray-dark);
    }

    .service-icon-sm {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 15px rgba(206, 115, 3, 0.3);
    }

    .service-image-wrapper {
        position: relative;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
    }

    .service-image-wrapper img {
        width: 100%;
        height: 400px;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .service-image-wrapper:hover img {
        transform: scale(1.05);
    }

    .service-image-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 2rem;
        border-radius: 0 0 15px 15px;
    }

    /* Feature Items */
    .feature-item {
        padding: 1rem;
        border-radius: 10px;
        border-left: 4px solid white;
        transition: all 0.3s ease;
    }

    .feature-item:hover {
        transform: translateX(5px);
    }

    .feature-icon-wrapper {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    }

    /* Why Choose Us */
    .why-card {
        border-radius: 15px;
        transition: all 0.4s ease;
        height: 100%;
    }

    .why-card:hover {
        transform: translateY(-8px);
    }

    .why-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    .why-card:hover .why-icon {
        transform: rotate(15deg) scale(1.1);
    }

    /* Process Timeline */
    .process-step {
        position: relative;
    }

    .step-number {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0 auto;
        color: white;
        box-shadow: 0 10px 25px rgba(206, 115, 3, 0.4);
        transition: all 0.3s ease;
    }

    .process-step:hover .step-number {
        transform: scale(1.1);
        box-shadow: 0 15px 35px rgba(206, 115, 3, 0.6);
    }

    /* Badge Styles */
    .badge.gradient-card {
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 500;
        color: white;
    }

    /* Gradient Background */
    .bg-gradient-orange {
        background: var(--cdw-orange-gradient) !important;
    }

    /* Button Styles */
    .btn-orange {
        background: var(--cdw-orange-gradient);
        border: none;
        color: white;
        font-weight: 600;
        padding: 12px 28px;
        border-radius: 10px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-orange:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(206, 115, 3, 0.4);
        color: white;
    }

    .btn-outline-orange {
        border: 2px solid var(--cdw-orange-primary);
        color: var(--cdw-orange-primary);
        font-weight: 600;
        padding: 12px 28px;
        border-radius: 10px;
        transition: all 0.3s ease;
        background: transparent;
    }

    .btn-outline-orange:hover {
        background: var(--cdw-orange-gradient);
        color: white;
        border-color: transparent;
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(206, 115, 3, 0.3);
    }

    /* Animations */
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Scroll Animation */
    .service-detail-section {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.6s ease;
    }

    .service-detail-section.visible {
        opacity: 1;
        transform: translateY(0);
    }

    /* Responsive Design */
    @media (max-width: 1199.98px) {
        .services-hero-section {
            padding: 130px 0 80px;
        }
    }

    @media (max-width: 991.98px) {
        .services-hero-section {
            padding: 120px 0 70px;
        }

        .services-hero-section .lead {
            font-size: 1.2rem;
        }

        .service-card {
            padding: 1.5rem;
        }

        .service-image-wrapper {
            margin-top: 2rem;
        }

        .service-image-wrapper img {
            height: 350px;
        }
    }

    @media (max-width: 767.98px) {
        .services-hero-section {
            padding: 100px 0 60px;
            background-attachment: scroll;
        }

        .services-hero-section .lead {
            font-size: 1.1rem;
            padding: 0 15px;
        }

        .stat-card {
            padding: 1.5rem 1rem;
        }

        .stat-number {
            font-size: 2.5rem;
        }

        .service-card {
            margin-bottom: 1.5rem;
        }

        .service-icon-sm {
            width: 50px;
            height: 50px;
        }

        .step-number {
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
        }

        .service-image-wrapper img {
            height: 300px;
        }
    }

    @media (max-width: 575.98px) {
        .services-hero-section {
            padding: 80px 0 50px;
        }

        .service-cta .btn {
            display: block;
            width: 100%;
            margin-bottom: 1rem;
            margin-right: 0 !important;
        }

        .service-cta .btn:last-child {
            margin-bottom: 0;
        }

        .why-card {
            margin-bottom: 1.5rem;
        }

        .service-image-wrapper img {
            height: 250px;
        }

        .step-number {
            width: 50px;
            height: 50px;
            font-size: 1.25rem;
        }
    }
</style>

<!-- Services JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll to service sections
    document.querySelectorAll('.service-card-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.hash;
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                const navbar = document.querySelector('.navbar');
                const navbarHeight = navbar ? navbar.offsetHeight : 0;
                const offset = navbarHeight + 30;
                
                window.scrollTo({
                    top: targetElement.offsetTop - offset,
                    behavior: 'smooth'
                });
                
                // Add active class to clicked card
                document.querySelectorAll('.service-card').forEach(card => {
                    card.classList.remove('active');
                });
                
                const card = this.querySelector('.service-card');
                if (card) {
                    card.classList.add('active');
                }
                
                // Highlight section
                document.querySelectorAll('.service-detail-section').forEach(section => {
                    section.classList.remove('highlighted');
                });
                targetElement.classList.add('highlighted');
                
                // Add highlight animation
                const highlightStyle = document.createElement('style');
                highlightStyle.textContent = `
                    .service-detail-section.highlighted {
                        animation: highlightSection 2s ease;
                    }
                    @keyframes highlightSection {
                        0% { background-color: transparent; }
                        50% { background-color: rgba(206, 115, 3, 0.1); }
                        100% { background-color: transparent; }
                    }
                `;
                document.head.appendChild(highlightStyle);
                
                setTimeout(() => {
                    document.head.removeChild(highlightStyle);
                    targetElement.classList.remove('highlighted');
                }, 2000);
            }
        });
    });
    
    // Scroll animation for service sections
    function checkVisibility() {
        const sections = document.querySelectorAll('.service-detail-section');
        
        sections.forEach(section => {
            const rect = section.getBoundingClientRect();
            const windowHeight = window.innerHeight;
            
            if (rect.top <= windowHeight * 0.75) {
                section.classList.add('visible');
            }
        });
    }
    
    // Initial check
    checkVisibility();
    
    // Check on scroll
    window.addEventListener('scroll', checkVisibility);
    
    // URL hash handling
    function handleHash() {
        if (window.location.hash) {
            const targetElement = document.querySelector(window.location.hash);
            if (targetElement) {
                setTimeout(() => {
                    const navbar = document.querySelector('.navbar');
                    const navbarHeight = navbar ? navbar.offsetHeight : 0;
                    const offset = navbarHeight + 30;
                    
                    window.scrollTo({
                        top: targetElement.offsetTop - offset,
                        behavior: 'smooth'
                    });
                }, 500);
            }
        }
    }
    
    handleHash();
    window.addEventListener('hashchange', handleHash);
    
    // Service cards hover effect
    const serviceCards = document.querySelectorAll('.service-card');
    serviceCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            const icon = this.querySelector('.service-icon i');
            if (icon) {
                icon.style.transform = 'scale(1.1) rotate(5deg)';
                icon.style.transition = 'transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            const icon = this.querySelector('.service-icon i');
            if (icon) {
                icon.style.transform = 'scale(1) rotate(0)';
            }
        });
    });
    
    // Feature items hover effect
    const featureItems = document.querySelectorAll('.feature-item');
    featureItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            const icon = this.querySelector('.feature-icon-wrapper i');
            if (icon) {
                icon.style.transform = 'scale(1.2)';
                icon.style.transition = 'transform 0.3s ease';
            }
        });
        
        item.addEventListener('mouseleave', function() {
            const icon = this.querySelector('.feature-icon-wrapper i');
            if (icon) {
                icon.style.transform = 'scale(1)';
            }
        });
    });
    
    // Update active nav link based on scroll
    const serviceSections = document.querySelectorAll('.service-detail-section');
    const navLinks = document.querySelectorAll('.nav-link');
    
    window.addEventListener('scroll', function() {
        let current = '';
        const scrollPosition = window.scrollY;
        
        serviceSections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            
            if (scrollPosition >= (sectionTop - 200) && 
                scrollPosition < (sectionTop + sectionHeight - 200)) {
                current = section.getAttribute('id');
            }
        });
        
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === `#${current}` || 
                link.getAttribute('href') === `/services#${current}`) {
                link.classList.add('active');
            }
        });
    });
    
    // Notification function (optional)
    function showNotification(message) {
        const notification = document.createElement('div');
        notification.innerHTML = `
            <div style="position: fixed; top: 20px; right: 20px; background: var(--cdw-orange-gradient); color: white; padding: 12px 20px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.2); z-index: 9999; animation: slideIn 0.3s ease;">
                <i class="fas fa-check-circle me-2"></i>${message}
            </div>
        `;
        
        document.body.appendChild(notification.firstChild);
        
        setTimeout(() => {
            const notif = document.body.lastChild;
            notif.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                if (notif && notif.parentNode) {
                    document.body.removeChild(notif);
                }
            }, 300);
        }, 3000);
    }
    
    // Add CSS for notifications
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
});
</script>