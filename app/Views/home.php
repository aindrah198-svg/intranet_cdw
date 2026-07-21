<?php
// Data dari controller sudah tersedia: $title, $active, $partners
// CSS dan JS sudah dipisah ke file eksternal
?>

<!-- Hero Section -->
<section class="hero-section" id="hero">
    <!-- Video Background -->
    <div class="video-background">
        <video autoplay muted loop playsinline id="heroVideo">
            <source src="<?= base_url('assets/vid/home/vid_home.mp4') ?>" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <!-- Loading indicator yang akan dihapus saat video ready -->
        <div class="video-loading">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
        </div>
        <div class="video-overlay"></div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <!-- Main Title -->
                <div class="text-center mb-6">
                    <h1 class="hero-title animate__animated animate__fadeInDown">
                        Welcome to 
                        <span class="cdw-highlight text-gradient">CDW Engineering</span>
                    </h1>
                    
                    <p class="hero-subtitle animate__animated animate__fadeInUp animate__delay-1s">
                        PT. Cipta Duta Wacana was established with <strong>one clear vision</strong>: 
                        To be the undisputed leader in Indonesia's engineering business competition
                    </p>
                </div>
                
                <!-- Vision Card -->
                <div class="hero-glass-card animate__animated animate__fadeInUp animate__delay-2s">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center mb-4 mb-md-0">
                            <div class="display-1 text-gradient fw-bold">01</div>
                            <h4 class="text-white mt-2">Our Vision</h4>
                        </div>
                        <div class="col-md-9">
                            <div class="hero-feature">
                                <i class="fas fa-bullseye"></i>
                                <p class="text-white">
                                    To become the <strong class="text-warning">premier engineering solutions provider</strong> 
                                    in Indonesia, recognized for innovation, quality, and customer satisfaction.
                                </p>
                            </div>
                            <div class="hero-feature">
                                <i class="fas fa-star"></i>
                                <p class="text-white">
                                    Committed to delivering <strong class="text-warning">"tomorrow's solutions today"</strong> 
                                    through cutting-edge technology and unparalleled expertise.
                                </p>
                            </div>
                            <div class="hero-feature">
                                <i class="fas fa-handshake"></i>
                                <p class="text-white">
                                    Building lasting partnerships based on <strong class="text-warning">trust, integrity, 
                                    and exceptional service</strong>.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Key Points -->
                <div class="row g-4 mb-6 animate__animated animate__fadeInUp animate__delay-3s">
                    <div class="col-md-4">
                        <div class="card bg-light border-orange h-100">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    <i class="fas fa-check-circle fa-3x text-orange"></i>
                                </div>
                                <h5 class="text-dark mb-3">Customer-Centric Approach</h5>
                                <p class="text-dark mb-0">
                                    We value customer satisfaction as the ultimate key to our success and growth.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card bg-light border-orange h-100">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    <i class="fas fa-award fa-3x text-orange"></i>
                                </div>
                                <h5 class="text-dark mb-3">Sole Distributor</h5>
                                <p class="text-dark mb-0">
                                    Official distributor of premium brands including Tominaga Japan, OPW, Tuthill, and Cimtek.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card bg-light border-orange h-100">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    <i class="fas fa-lightbulb fa-3x text-orange"></i>
                                </div>
                                <h5 class="text-dark mb-3">Innovation Driven</h5>
                                <p class="text-dark mb-0">
                                    Constantly evolving to provide innovative solutions for tomorrow's challenges.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Call to Action -->
                <div class="text-center animate__animated animate__fadeInUp animate__delay-4s">
                    <div class="hero-buttons">
                        <a href="<?= base_url('about') ?>" class="btn-hero-primary">
                            <i class="fas fa-book-open me-2"></i> Discover Our Story
                        </a>
                        <a href="<?= base_url('contact') ?>" class="btn-hero-secondary">
                            <i class="fas fa-phone-alt me-2"></i> Get In Touch
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="py-5 bg-gray-light">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Our <span class="text-gradient">Services</span></h2>
            <p class="section-subtitle">
                Comprehensive engineering solutions tailored to meet the evolving needs of modern industries
            </p>
        </div>
        
        <div class="row g-4 align-items-stretch">
            <!-- Engineering -->
            <div class="col-md-6 col-lg-3">
                <div class="service-card h-100">
                    <div class="service-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <div class="service-content">
                        <h4>ENGINEERING</h4>
                        <div class="service-text">
                            <p>
                                Specialized manufacturing work and provision of goods and services with international standards 
                                and quality assurance.
                            </p>
                        </div>
                        <div class="service-button">
                            <a href="<?= base_url('services#engineering') ?>" class="btn-service">
                                <i class="fas fa-arrow-right me-2"></i> Explore Service
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Construction -->
            <div class="col-md-6 col-lg-3">
                <div class="service-card h-100">
                    <div class="service-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="service-content">
                        <h4>CONSTRUCTION</h4>
                        <div class="service-text">
                            <p>
                                Specialized construction of gas stations, oil and gas facilities, and Pertashop stations using 
                                the latest technology.
                            </p>
                        </div>
                        <div class="service-button">
                            <a href="<?= base_url('services#construction') ?>" class="btn-service">
                                <i class="fas fa-arrow-right me-2"></i> Explore Service
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Mechanical & Electrical -->
            <div class="col-md-6 col-lg-3">
                <div class="service-card h-100">
                    <div class="service-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div class="service-content">
                        <h4>MECHANICAL & ELECTRICAL</h4>
                        <div class="service-text">
                            <p>
                                Specialization in materials and services for Pertamina and Shell gas stations with certified 
                                expertise.
                            </p>
                        </div>
                        <div class="service-button">
                            <a href="<?= base_url('services#mechanical') ?>" class="btn-service">
                                <i class="fas fa-arrow-right me-2"></i> Explore Service
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- IT & Integration -->
            <div class="col-md-6 col-lg-3">
                <div class="service-card h-100">
                    <div class="service-icon">
                        <i class="fas fa-server"></i>
                    </div>
                    <div class="service-content">
                        <h4>IT & INTEGRATION</h4>
                        <div class="service-text">
                            <p>
                                Provider of equipment and fuel management system services for gas stations, SPBG, and 
                                industrial applications.
                            </p>
                        </div>
                        <div class="service-button">
                            <a href="<?= base_url('services#it') ?>" class="btn-service">
                                <i class="fas fa-arrow-right me-2"></i> Explore Service
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Service Stats dengan Counter Animation -->
        <div class="row mt-6 pt-5">
            <div class="col-lg-10 mx-auto">
                <div class="stats-card card border-0 shadow-lg bg-white">
                    <div class="card-body p-5">
                        <div class="row text-center">
                            <div class="col-md-3 col-6 mb-4 mb-md-0">
                                <div class="display-4 fw-bold text-gradient counter" data-target="15" data-plus="true">0</div>
                                <p class="text-muted mb-0">Years Experience</p>
                            </div>
                            <div class="col-md-3 col-6 mb-4 mb-md-0">
                                <div class="display-4 fw-bold text-gradient counter" data-target="200" data-plus="true">0</div>
                                <p class="text-muted mb-0">Projects Completed</p>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="display-4 fw-bold text-gradient counter" data-target="50" data-plus="true">0</div>
                                <p class="text-muted mb-0">Expert Engineers</p>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="display-4 fw-bold text-gradient counter" data-target="100" data-plus="false">0</div>
                                <p class="text-muted mb-0">Client Satisfaction</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section id="testimonials" class="py-5 bg-white">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Client <span class="text-gradient">Testimonials</span></h2>
            <p class="section-subtitle">
                Hear what our clients say about our services and commitment to excellence
            </p>
        </div>
        
        <!-- Single Testimonial dengan Auto Rotate -->
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6">
                <div id="singleTestimonialCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
                    <!-- Indicators -->
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#singleTestimonialCarousel" data-bs-slide-to="0" class="active" 
                                aria-label="Testimonial 1" style="width: 12px; height: 12px; border-radius: 50%; background-color: var(--cdw-orange-primary);"></button>
                        <button type="button" data-bs-target="#singleTestimonialCarousel" data-bs-slide-to="1" 
                                aria-label="Testimonial 2" style="width: 12px; height: 12px; border-radius: 50%; background-color: var(--cdw-orange-primary);"></button>
                        <button type="button" data-bs-target="#singleTestimonialCarousel" data-bs-slide-to="2" 
                                aria-label="Testimonial 3" style="width: 12px; height: 12px; border-radius: 50%; background-color: var(--cdw-orange-primary);"></button>
                        <button type="button" data-bs-target="#singleTestimonialCarousel" data-bs-slide-to="3" 
                                aria-label="Testimonial 4" style="width: 12px; height: 12px; border-radius: 50%; background-color: var(--cdw-orange-primary);"></button>
                        <button type="button" data-bs-target="#singleTestimonialCarousel" data-bs-slide-to="4" 
                                aria-label="Testimonial 5" style="width: 12px; height: 12px; border-radius: 50%; background-color: var(--cdw-orange-primary);"></button>
                        <button type="button" data-bs-target="#singleTestimonialCarousel" data-bs-slide-to="5" 
                                aria-label="Testimonial 6" style="width: 12px; height: 12px; border-radius: 50%; background-color: var(--cdw-orange-primary);"></button>
                    </div>
                    
                    <!-- Carousel Items -->
                    <div class="carousel-inner">
                        <!-- Testimonial 1 -->
                        <div class="carousel-item active">
                            <div class="testimonial-single-card text-center">
                                <div class="testimonial-quote mb-4">
                                    <i class="fas fa-quote-left fa-3x text-orange opacity-25"></i>
                                </div>
                                <p class="testimonial-text lead mb-4">
                                    "CDW Engineering is our trusted partner for all engineering needs. Their team delivers 
                                    exceptional quality and professionalism. The project was completed ahead of schedule 
                                    with outstanding results."
                                </p>
                                <div class="testimonial-author mt-4">
                                    <div class="author-avatar mx-auto mb-3">A</div>
                                    <div class="author-info">
                                        <h5 class="mb-1">Anjal</h5>
                                        <p class="text-muted mb-0">Support Manager, Pertamina</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Testimonial 2 -->
                        <div class="carousel-item">
                            <div class="testimonial-single-card text-center">
                                <div class="testimonial-quote mb-4">
                                    <i class="fas fa-quote-left fa-3x text-orange opacity-25"></i>
                                </div>
                                <p class="testimonial-text lead mb-4">
                                    "Excellent service from start to finish. CDW Engineering provided innovative solutions 
                                    that significantly improved our operational efficiency. Their expertise in petroleum 
                                    equipment is unmatched."
                                </p>
                                <div class="testimonial-author mt-4">
                                    <div class="author-avatar mx-auto mb-3">A</div>
                                    <div class="author-info">
                                        <h5 class="mb-1">Alex</h5>
                                        <p class="text-muted mb-0">Director, Shell Indonesia</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Testimonial 3 -->
                        <div class="carousel-item">
                            <div class="testimonial-single-card text-center">
                                <div class="testimonial-quote mb-4">
                                    <i class="fas fa-quote-left fa-3x text-orange opacity-25"></i>
                                </div>
                                <p class="testimonial-text lead mb-4">
                                    "Professional team with deep industry knowledge. They understand our business needs 
                                    and deliver solutions that exceed expectations. Highly recommended for any engineering project."
                                </p>
                                <div class="testimonial-author mt-4">
                                    <div class="author-avatar mx-auto mb-3">D</div>
                                    <div class="author-info">
                                        <h5 class="mb-1">Dennys</h5>
                                        <p class="text-muted mb-0">Marketing Fuel Specialist</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Testimonial 4 -->
                        <div class="carousel-item">
                            <div class="testimonial-single-card text-center">
                                <div class="testimonial-quote mb-4">
                                    <i class="fas fa-quote-left fa-3x text-orange opacity-25"></i>
                                </div>
                                <p class="testimonial-text lead mb-4">
                                    "Outstanding partnership! CDW's technical expertise and commitment to quality set them 
                                    apart. They've been instrumental in our expansion projects across multiple locations."
                                </p>
                                <div class="testimonial-author mt-4">
                                    <div class="author-avatar mx-auto mb-3">R</div>
                                    <div class="author-info">
                                        <h5 class="mb-1">Rina</h5>
                                        <p class="text-muted mb-0">Project Coordinator, ExxonMobil</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Testimonial 5 -->
                        <div class="carousel-item">
                            <div class="testimonial-single-card text-center">
                                <div class="testimonial-quote mb-4">
                                    <i class="fas fa-quote-left fa-3x text-orange opacity-25"></i>
                                </div>
                                <p class="testimonial-text lead mb-4">
                                    "Reliable, professional, and results-driven. CDW Engineering consistently delivers 
                                    high-quality work on time and within budget. Their attention to detail is remarkable."
                                </p>
                                <div class="testimonial-author mt-4">
                                    <div class="author-avatar mx-auto mb-3">B</div>
                                    <div class="author-info">
                                        <h5 class="mb-1">Budi</h5>
                                        <p class="text-muted mb-0">Operations Head, Total Energies</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Testimonial 6 -->
                        <div class="carousel-item">
                            <div class="testimonial-single-card text-center">
                                <div class="testimonial-quote mb-4">
                                    <i class="fas fa-quote-left fa-3x text-orange opacity-25"></i>
                                </div>
                                <p class="testimonial-text lead mb-4">
                                    "The best engineering partner we've worked with. Their innovative solutions and 
                                    customer-centric approach make them stand out in the industry. Exceptional service!"
                                </p>
                                <div class="testimonial-author mt-4">
                                    <div class="author-avatar mx-auto mb-3">S</div>
                                    <div class="author-info">
                                        <h5 class="mb-1">Sari</h5>
                                        <p class="text-muted mb-0">Technical Director, BP Indonesia</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Navigation Arrows (Optional) -->
                    <button class="carousel-control-prev d-none d-md-flex" type="button" data-bs-target="#singleTestimonialCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next d-none d-md-flex" type="button" data-bs-target="#singleTestimonialCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Partners Section dengan Background Image -->
<section id="partners" class="py-5 partners-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title text-white">Our <span class="text-gradient">Partnerships</span></h2>
            <p class="section-subtitle text-white">
                Collaborating with industry leaders to deliver exceptional engineering solutions worldwide
            </p>
        </div>
        
        <div class="row g-4 justify-content-center align-items-center">
            <?php 
            // Daftar partner dari controller
            if (isset($partners) && is_array($partners)) {
                foreach ($partners as $partner): 
                    $initials = substr(strtoupper($partner['name']), 0, 2);
            ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="partner-card">
                    <div class="partner-logo-wrapper mb-3">
                        <img src="<?= $partner['logo'] ?>" 
                             alt="<?= htmlspecialchars($partner['name']) ?> Logo" 
                             class="img-fluid partner-logo"
                             style="max-height: 80px; width: auto;"
                             onerror="handleLogoError(this, '<?= $initials ?>')">
                    </div>
                    <h5 class="partner-name"><?= htmlspecialchars($partner['name']) ?></h5>
                    <p class="partner-country text-muted small"><?= htmlspecialchars($partner['country']) ?></p>
                </div>
            </div>
            <?php 
                endforeach;
            } else {
                echo '<div class="col-12 text-center"><p class="text-white">No partners available</p></div>';
            }
            ?>
        </div>
        
        <!-- CTA Section -->
        <div class="row mt-6">
            <div class="col-lg-8 mx-auto">
                <div class="card bg-white border-0 shadow-lg rounded-xl overflow-hidden">
                    <div class="card-body p-5 text-center">
                        <div class="mb-4">
                            <h3 class="mb-3">Ready to Start Your Project?</h3>
                            <p class="text-muted mb-4 lead">
                                Join hundreds of satisfied clients who trust CDW Engineering for their engineering needs.
                            </p>
                        </div>
                        <div class="d-flex flex-wrap justify-content-center gap-3">
                            <a href="<?= base_url('contact') ?>" class="btn btn-login btn-lg px-5 py-3">
                                <i class="fas fa-envelope me-2"></i> Contact Us
                            </a>
                            <a href="<?= base_url('projects') ?>" class="btn-cta-secondary btn-lg px-5 py-3">
                                <i class="fas fa-eye me-2"></i> View Projects
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Load CSS dan JS eksternal -->
<link rel="stylesheet" href="<?= base_url('assets/css/home.css') ?>">
<script src="<?= base_url('assets/js/home.js') ?>" defer></script>