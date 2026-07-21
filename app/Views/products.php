<!-- Products Hero Section with Background Image -->
<section class="products-hero-section" style="background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1581092160562-40aa08e78837?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80') center/cover fixed; color: white; padding: 120px 0; position: relative;">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-4 animate__animated animate__fadeInDown">Our <span class="text-gradient-light">Products</span></h1>
        <p class="lead mb-5 animate__animated animate__fadeInUp">
            High-quality industrial products and solutions for various applications. 
            From petroleum equipment to advanced IT integration systems.
        </p>
        
        <!-- Product Stats with Cards -->
        <div class="products-stats animate__animated animate__fadeInUp animate__delay-1s">
            <div class="row g-4 justify-content-center">
                <div class="col-6 col-md-3">
                    <div class="stat-card gradient-card">
                        <div class="stat-number display-6 fw-bold text-white">4</div>
                        <div class="stat-label text-white-50">Product Categories</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card gradient-card">
                        <div class="stat-number display-6 fw-bold text-white">50+</div>
                        <div class="stat-label text-white-50">Product Variants</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card gradient-card">
                        <div class="stat-number display-6 fw-bold text-white">100%</div>
                        <div class="stat-label text-white-50">Quality Tested</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card gradient-card">
                        <div class="stat-number display-6 fw-bold text-white">ISO</div>
                        <div class="stat-label text-white-50">Certified Products</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Products Grid -->
<section class="py-5 bg-gray-light">
    <div class="container">
        <!-- Quick Overview Cards with Gradient -->
        <div class="row g-4 mb-5">
            <?php foreach($products as $index => $product): ?>
            <div class="col-lg-3 col-md-6">
                <div class="product-overview-card gradient-card" data-category="<?= $product['slug'] ?>">
                    <div class="product-icon-wrapper">
                        <div class="product-icon bg-white">
                            <i class="<?= $product['icon'] ?> fa-3x text-gradient"></i>
                        </div>
                        <div class="product-number gradient-card">0<?= $index + 1 ?></div>
                    </div>
                    <h4 class="product-title text-white"><?= $product['short_name'] ?></h4>
                    <p class="product-description text-white-50"><?= $product['short_description'] ?></p>
                    <div class="product-cta">
                        <a href="#<?= $product['slug'] ?>" class="btn-view-products bg-white">
                            Explore Products <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Product Details Sections -->
<div class="product-details-container">
    <?php foreach($products as $product): ?>
    <section id="<?= $product['slug'] ?>" class="product-detail-section">
        <div class="container">
            <div class="section-header mb-5">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="section-icon gradient-card me-3">
                            <i class="<?= $product['icon'] ?> fa-2x text-white"></i>
                        </div>
                        <div>
                            <h2 class="section-title mb-1"><?= $product['name'] ?></h2>
                            <p class="section-subtitle mb-0"><?= $product['short_description'] ?></p>
                        </div>
                    </div>
                    <a href="#contact" class="btn btn-orange d-none d-md-inline-flex">
                        <i class="fas fa-envelope me-2"></i>Request Quote
                    </a>
                </div>
            </div>
            
            <div class="row align-items-stretch">
                <!-- Product Image & Gallery -->
                <div class="col-lg-6 mb-4">
                    <div class="product-visual-section">
                        <div class="product-main-image gradient-card">
                            <img src="<?= $product['image'] ?>" 
                                 alt="<?= $product['image_alt'] ?>" 
                                 class="img-fluid rounded-xl">
                            <div class="image-badge gradient-card text-white">
                                <i class="fas fa-certificate me-2"></i>Quality Certified
                            </div>
                        </div>
                        
                        <?php if(isset($product['gallery']) && count($product['gallery']) > 0): ?>
                        <div class="product-gallery mt-4">
                            <h5 class="mb-3">Product Gallery</h5>
                            <div class="row g-3">
                                <?php foreach($product['gallery'] as $index => $galleryImage): ?>
                                <div class="col-4">
                                    <div class="gallery-item gradient-card position-relative">
                                        <img src="<?= $galleryImage ?>" 
                                             alt="<?= $product['name'] ?> Gallery <?= $index + 1 ?>" 
                                             class="img-fluid rounded gallery-thumb">
                                        <div class="gallery-overlay">
                                            <i class="fas fa-search-plus"></i>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Specifications moved under Gallery -->
                        <div class="product-specifications mt-4">
                            <h4 class="mb-3 text-gradient">Specifications</h4>
                            <div class="specs-table gradient-card text-white">
                                <?php foreach($product['specifications'] as $spec): ?>
                                <div class="spec-item d-flex align-items-center">
                                    <div class="spec-icon bg-white me-3">
                                        <i class="fas fa-check text-gradient"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="spec-label fw-semibold"><?= explode(':', $spec)[0] ?>:</div>
                                        <div class="spec-value text-white-50"><?= explode(':', $spec)[1] ?? '' ?></div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Product Details -->
                <div class="col-lg-6">
                    <div class="product-info-section h-100">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4 p-lg-5">
                                <!-- Overview -->
                                <div class="product-overview mb-5">
                                    <h4 class="mb-3 text-gradient">Overview</h4>
                                    <p class="mb-0"><?= $product['full_description'] ?></p>
                                </div>
                                
                                <!-- Applications -->
                                <div class="product-applications mb-5">
                                    <h4 class="mb-3 text-gradient">Applications</h4>
                                    <div class="row">
                                        <?php foreach($product['applications'] as $application): ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="app-card gradient-card">
                                                <i class="fas fa-check-circle text-white me-2"></i>
                                                <span class="fw-medium text-white"><?= $application ?></span>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                
                                <!-- Key Features -->
                                <div class="product-features mb-5">
                                    <h4 class="mb-3 text-gradient">Key Features</h4>
                                    <div class="row g-3">
                                        <?php foreach($product['features'] as $feature): ?>
                                        <div class="col-md-6">
                                            <div class="feature-card gradient-card">
                                                <div class="feature-header d-flex align-items-center mb-2">
                                                    <div class="feature-icon bg-white me-3">
                                                        <i class="<?= $feature['icon'] ?> text-gradient"></i>
                                                    </div>
                                                    <h6 class="mb-0 text-white"><?= $feature['title'] ?></h6>
                                                </div>
                                                <p class="text-white-50 small mb-0"><?= $feature['description'] ?></p>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                
                                <!-- Mobile CTA -->
                                <div class="mobile-cta mt-4 d-block d-md-none">
                                    <a href="#contact" class="btn btn-orange w-100">
                                        <i class="fas fa-envelope me-2"></i>Request Quote
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endforeach; ?>
</div>

<!-- Our Capabilities -->
<section class="py-5">
    <div class="container">
        <div class="section-header mb-5 text-center">
            <h2 class="section-title">Our <span class="text-gradient">Capabilities</span></h2>
            <p class="section-subtitle">
                What sets our products apart in the industry
            </p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="capability-card gradient-card">
                    <div class="capability-icon mb-4 bg-white">
                        <i class="fas fa-clipboard-check fa-3x text-gradient"></i>
                    </div>
                    <h4 class="mb-3 text-white">Quality Assurance</h4>
                    <p class="text-white-50 mb-0">All products undergo rigorous testing and quality control to ensure they meet international standards.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="capability-card gradient-card">
                    <div class="capability-icon mb-4 bg-white">
                        <i class="fas fa-cogs fa-3x text-gradient"></i>
                    </div>
                    <h4 class="mb-3 text-white">Custom Solutions</h4>
                    <p class="text-white-50 mb-0">We provide customized product solutions tailored to specific project requirements and applications.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="capability-card gradient-card">
                    <div class="capability-icon mb-4 bg-white">
                        <i class="fas fa-headset fa-3x text-gradient"></i>
                    </div>
                    <h4 class="mb-3 text-white">Technical Support</h4>
                    <p class="text-white-50 mb-0">Comprehensive technical support and after-sales service to ensure optimal product performance.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="capability-card gradient-card">
                    <div class="capability-icon mb-4 bg-white">
                        <i class="fas fa-truck fa-3x text-gradient"></i>
                    </div>
                    <h4 class="mb-3 text-white">Timely Delivery</h4>
                    <p class="text-white-50 mb-0">Efficient logistics and supply chain management for on-time delivery of products.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Certification & Standards -->
<section class="py-5 bg-gray-light">
    <div class="container">
        <div class="section-header mb-5 text-center">
            <h2 class="section-title">Certifications & <span class="text-gradient">Standards</span></h2>
            <p class="section-subtitle">
                Our products comply with international standards and certifications
            </p>
        </div>
        
        <div class="certification-grid">
            <div class="row g-4 justify-content-center">
                <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                    <div class="certification-card gradient-card">
                        <div class="cert-icon mb-3 bg-white">
                            <i class="fas fa-certificate fa-3x text-gradient"></i>
                        </div>
                        <h6 class="fw-bold mb-2 text-white">ISO 9001:2015</h6>
                        <p class="text-white-50 small mb-0">Quality Management</p>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                    <div class="certification-card gradient-card">
                        <div class="cert-icon mb-3 bg-white">
                            <i class="fas fa-shield-alt fa-3x text-gradient"></i>
                        </div>
                        <h6 class="fw-bold mb-2 text-white">CE Certified</h6>
                        <p class="text-white-50 small mb-0">European Standards</p>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                    <div class="certification-card gradient-card">
                        <div class="cert-icon mb-3 bg-white">
                            <i class="fas fa-bolt fa-3x text-gradient"></i>
                        </div>
                        <h6 class="fw-bold mb-2 text-white">IEC Standards</h6>
                        <p class="text-white-50 small mb-0">Electrical Safety</p>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                    <div class="certification-card gradient-card">
                        <div class="cert-icon mb-3 bg-white">
                            <i class="fas fa-fire-extinguisher fa-3x text-gradient"></i>
                        </div>
                        <h6 class="fw-bold mb-2 text-white">Safety Certified</h6>
                        <p class="text-white-50 small mb-0">Fire Safety</p>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                    <div class="certification-card gradient-card">
                        <div class="cert-icon mb-3 bg-white">
                            <i class="fas fa-globe fa-3x text-gradient"></i>
                        </div>
                        <h6 class="fw-bold mb-2 text-white">International Standards</h6>
                        <p class="text-white-50 small mb-0">Global Compliance</p>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                    <div class="certification-card gradient-card">
                        <div class="cert-icon mb-3 bg-white">
                            <i class="fas fa-industry fa-3x text-gradient"></i>
                        </div>
                        <h6 class="fw-bold mb-2 text-white">Industry Standards</h6>
                        <p class="text-white-50 small mb-0">Sector Specific</p>
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
                <h2 class="display-5 fw-bold mb-3">Ready to Get Our Products?</h2>
                <p class="lead mb-0">Contact us for product specifications, pricing, and custom requirements</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="d-flex flex-column flex-lg-row gap-3">
                    <a href="/contact" class="btn btn-light btn-lg">
                        <i class="fas fa-file-alt me-2"></i>Request Catalog
                    </a>
                    <a href="tel:+622112345678" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-phone me-2"></i>Call Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Products CSS - UPDATED -->
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

/* Products Hero with Background Image - Style seperti Gallery */
.products-hero-section {
    background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1581092160562-40aa08e78837?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80') center/cover fixed;
    color: white;
    padding: 120px 0;
    position: relative;
    margin-bottom: 0;
}

.products-hero-section h1 {
    text-shadow: 2px 2px 15px rgba(0, 0, 0, 0.5);
}

.products-hero-section .lead {
    text-shadow: 1px 1px 10px rgba(0, 0, 0, 0.5);
    font-size: 1.3rem;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}

/* Light text gradient untuk hero section */
.text-gradient-light {
    background: linear-gradient(135deg, #ffffff 0%, #ffd966 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-shadow: none;
}
    
/* Stat Cards - Tetap menggunakan gradient card yang sama */
.stat-card {
    padding: 2rem 1.5rem;
    border-radius: 15px;
    text-align: center;
    height: 100%;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    background: linear-gradient(135deg, #ce7303 0%, #ccad01 100%) !important;
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
    
/* Animations */
.animate__animated {
    animation-duration: 1s;
    animation-fill-mode: both;
}

.animate__fadeInDown {
    animation-name: fadeInDown;
}

.animate__fadeInUp {
    animation-name: fadeInUp;
}

.animate__delay-1s {
    animation-delay: 0.5s;
}

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

/* Responsive untuk hero section */
@media (max-width: 991.98px) {
    .products-hero-section {
        padding: 100px 0;
    }
    
    .products-hero-section .lead {
        font-size: 1.2rem;
    }
}

@media (max-width: 767.98px) {
    .products-hero-section {
        padding: 80px 0;
        background-attachment: scroll; /* Fixed background tidak bekerja baik di mobile */
    }
    
    .products-hero-section .lead {
        font-size: 1.1rem;
        padding: 0 15px;
    }
    
    .stat-card {
        padding: 1.5rem 1rem;
    }
}

@media (max-width: 575.98px) {
    .products-hero-section {
        padding: 60px 0;
    }
}

    /* Product Overview Cards */
    .product-overview-card {
        border-radius: 20px;
        padding: 2.5rem 2rem;
        text-align: center;
        height: 100%;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .product-overview-card:hover {
        transform: translateY(-12px);
    }
    
    .product-icon-wrapper {
        position: relative;
        margin-bottom: 2rem;
    }
    
    .product-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        transition: all 0.3s ease;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }
    
    .product-overview-card:hover .product-icon {
        transform: scale(1.1) rotate(5deg);
    }
    
    .product-number {
        position: absolute;
        top: -10px;
        right: -10px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.2rem;
        color: white;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    }
    
    .product-title {
        font-weight: 700;
        margin-bottom: 1rem;
        font-size: 1.5rem;
    }
    
    .product-description {
        font-size: 0.95rem;
        margin-bottom: 2rem;
        line-height: 1.6;
        min-height: 72px;
    }
    
    .btn-view-products {
        display: inline-flex;
        align-items: center;
        padding: 12px 24px;
        border-radius: 8px;
        color: var(--cdw-orange-primary);
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
    }
    
    .btn-view-products:hover {
        transform: translateX(5px);
        box-shadow: 0 5px 15px rgba(255, 255, 255, 0.3);
        color: var(--cdw-orange-primary);
    }
    
    /* Product Detail Sections */
    .product-details-container {
        position: relative;
    }
    
    .product-detail-section {
        padding: 100px 0;
        scroll-margin-top: 100px;
        position: relative;
    }
    
    .product-detail-section:nth-child(odd) {
        background: white;
    }
    
    .product-detail-section:nth-child(even) {
        background: var(--cdw-gray-light);
    }
    
    .section-header {
        padding-bottom: 2rem;
        border-bottom: 3px solid rgba(206, 115, 3, 0.1);
    }
    
    .section-icon {
        width: 70px;
        height: 70px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .section-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--cdw-gray-dark);
    }
    
    .section-subtitle {
        color: var(--cdw-gray-medium);
        font-size: 1.1rem;
    }
    
    /* Product Visual Section */
    .product-visual-section {
        position: relative;
    }
    
    .product-main-image {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 1.5rem;
        padding: 10px;
    }
    
    .product-main-image img {
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-radius: 15px;
        transition: transform 0.5s ease;
    }
    
    .product-main-image:hover img {
        transform: scale(1.05);
    }
    
    .image-badge {
        position: absolute;
        top: 20px;
        right: 20px;
        padding: 8px 16px;
        border-radius: 30px;
        font-weight: 600;
        display: flex;
        align-items: center;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }
    
    /* Gallery */
    .gallery-item {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        cursor: pointer;
        padding: 5px;
    }
    
    .gallery-thumb {
        width: 100%;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .gallery-overlay {
        position: absolute;
        top: 5px;
        left: 5px;
        right: 5px;
        bottom: 5px;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
        color: white;
        font-size: 1.5rem;
        border-radius: 8px;
    }
    
    .gallery-item:hover .gallery-overlay {
        opacity: 1;
    }
    
    .gallery-item:hover .gallery-thumb {
        transform: scale(1.1);
    }
    
    /* Specifications */
    .specs-table {
        border-radius: 15px;
        padding: 1.5rem;
    }
    
    .spec-item {
        padding: 1rem 0;
        border-bottom: 1px dashed rgba(255, 255, 255, 0.2);
    }
    
    .spec-item:last-child {
        border-bottom: none;
    }
    
    .spec-icon {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    
    .spec-label {
        font-size: 0.95rem;
        margin-bottom: 2px;
    }
    
    .spec-value {
        font-size: 0.9rem;
    }
    
    /* Application Cards */
    .app-card {
        padding: 12px 16px;
        border-radius: 10px;
        border-left: 4px solid white;
        transition: all 0.3s ease;
    }
    
    .app-card:hover {
        transform: translateX(5px);
        opacity: 0.9;
    }
    
    /* Feature Cards */
    .feature-card {
        padding: 1.5rem;
        border-radius: 15px;
        height: 100%;
        transition: all 0.3s ease;
    }
    
    .feature-card:hover {
        transform: translateY(-5px);
    }
    
    .feature-header {
        margin-bottom: 0.75rem;
    }
    
    .feature-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }
    
    /* Capability Cards */
    .capability-card {
        text-align: center;
        padding: 2.5rem 2rem;
        border-radius: 20px;
        height: 100%;
        transition: all 0.4s ease;
    }
    
    .capability-card:hover {
        transform: translateY(-10px);
    }
    
    .capability-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }
    
    .capability-card:hover .capability-icon {
        transform: rotate(15deg) scale(1.1);
    }
    
    /* Certifications */
    .certification-card {
        padding: 2rem 1.5rem;
        border-radius: 15px;
        transition: all 0.4s ease;
        text-align: center;
        height: 100%;
    }
    
    .certification-card:hover {
        transform: translateY(-8px);
    }
    
    .cert-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }
    
    .certification-card:hover .cert-icon {
        transform: scale(1.1);
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
        box-shadow: 0 10px 25px rgba(206, 115, 3, 0.3);
        color: white;
    }
    
    .btn-outline-light {
        border: 2px solid white;
        color: white;
        background: transparent;
        font-weight: 600;
        padding: 12px 28px;
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    
    .btn-outline-light:hover {
        background: white;
        color: var(--cdw-orange-primary);
        transform: translateY(-3px);
    }
    
    /* Gradient Background */
    .bg-gradient-orange {
        background: var(--cdw-orange-gradient) !important;
    }
    
    .text-gradient {
        background: var(--cdw-orange-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    /* Text Colors */
    .text-white-50 {
        color: rgba(255, 255, 255, 0.8) !important;
    }
    
    /* Scroll Animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .product-detail-section {
        animation: fadeInUp 0.8s ease-out forwards;
    }
    
    /* Responsive Design */
    @media (max-width: 1199.98px) {
        .section-title {
            font-size: 2rem;
        }
        
        .product-main-image img {
            height: 350px;
        }
    }
    
    @media (max-width: 991.98px) {
        .products-hero-section {
            padding: 100px 0 60px;
        }
        
        .product-overview-card {
            padding: 2rem 1.5rem;
        }
        
        .product-icon {
            width: 70px;
            height: 70px;
        }
        
        .section-title {
            font-size: 1.75rem;
        }
        
        .product-main-image img {
            height: 300px;
        }
    }
    
    @media (max-width: 767.98px) {
        .products-hero-section {
            padding: 80px 0 40px;
        }
        
        .stat-number {
            font-size: 2.5rem;
        }
        
        .product-overview-card {
            margin-bottom: 1.5rem;
        }
        
        .section-header .d-flex {
            flex-direction: column;
            text-align: center;
            gap: 1.5rem;
        }
        
        .section-icon {
            margin: 0 auto;
        }
        
        .product-detail-section {
            padding: 70px 0;
        }
        
        .product-main-image img {
            height: 250px;
        }
        
        .gallery-thumb {
            height: 80px;
        }
    }
    
    @media (max-width: 575.98px) {
        .product-title {
            font-size: 1.3rem;
        }
        
        .product-description {
            min-height: auto;
        }
        
        .product-detail-section {
            padding: 50px 0;
            scroll-margin-top: 80px;
        }
        
        .certification-card {
            margin-bottom: 1rem;
        }
        
        .feature-card {
            margin-bottom: 1rem;
        }
    }
    /* Products Hero with Background Image */
.products-hero-section {
    padding: 150px 0 100px;
    position: relative;
    background-image: url('https://images.unsplash.com/photo-1581092160562-40aa08e78837?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    color: white;
    overflow: hidden;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(0, 0, 0, 0.85) 0%, rgba(206, 115, 3, 0.7) 100%);
    z-index: 1;
}

.products-hero-section .container {
    position: relative;
    z-index: 2;
}

.products-hero-section h1 {
    text-shadow: 2px 2px 15px rgba(0, 0, 0, 0.5);
    animation: fadeInDown 1s ease-out;
}

.products-hero-section .lead {
    text-shadow: 1px 1px 10px rgba(0, 0, 0, 0.5);
    font-size: 1.3rem;
    animation: fadeInUp 1s ease-out 0.3s both;
}

.products-hero-section .products-stats {
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

/* Responsive untuk hero section */
@media (max-width: 991.98px) {
    .products-hero-section {
        padding: 130px 0 80px;
    }
    
    .products-hero-section .lead {
        font-size: 1.2rem;
    }
}

@media (max-width: 767.98px) {
    .products-hero-section {
        padding: 120px 0 60px;
        background-attachment: scroll; /* Fixed background tidak bekerja baik di mobile */
    }
    
    .products-hero-section .lead {
        font-size: 1.1rem;
    }
}

@media (max-width: 575.98px) {
    .products-hero-section {
        padding: 100px 0 50px;
    }
}
</style>

<!-- Products JavaScript - UPDATED -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll to product sections dari cards
    document.querySelectorAll('.btn-view-products').forEach(link => {
        link.addEventListener('click', function(e) {
            if (this.hash) {
                e.preventDefault();
                const targetId = this.hash;
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    const navbar = document.querySelector('.navbar');
                    const navbarHeight = navbar.offsetHeight;
                    const offset = navbarHeight + 30;
                    
                    window.scrollTo({
                        top: targetElement.offsetTop - offset,
                        behavior: 'smooth'
                    });
                    
                    // Highlight section
                    document.querySelectorAll('.product-detail-section').forEach(section => {
                        section.classList.remove('highlighted');
                    });
                    targetElement.classList.add('highlighted');
                    
                    const highlightStyle = document.createElement('style');
                    highlightStyle.textContent = `
                        .product-detail-section.highlighted {
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
            }
        });
    });
    
    // Gallery image modal functionality
    document.querySelectorAll('.gallery-item').forEach(item => {
        item.addEventListener('click', function() {
            const thumb = this.querySelector('.gallery-thumb');
            const mainImage = this.closest('.product-visual-section').querySelector('.product-main-image img');
            
            if (thumb && mainImage) {
                const currentSrc = mainImage.src;
                const currentAlt = mainImage.alt;
                
                mainImage.src = thumb.src;
                mainImage.alt = thumb.alt;
                thumb.src = currentSrc;
                thumb.alt = currentAlt;
                
                mainImage.style.opacity = '0';
                setTimeout(() => {
                    mainImage.style.opacity = '1';
                    mainImage.style.transition = 'opacity 0.5s ease';
                }, 50);
                
                showNotification('Image changed successfully');
            }
        });
    });
    
    // Scroll animation
    function checkVisibility() {
        const sections = document.querySelectorAll('.product-detail-section');
        
        sections.forEach(section => {
            const rect = section.getBoundingClientRect();
            const windowHeight = window.innerHeight;
            
            if (rect.top <= windowHeight * 0.75) {
                section.classList.add('visible');
            }
        });
    }
    
    checkVisibility();
    window.addEventListener('scroll', checkVisibility);
    
    // Handle hash
    function handleHash() {
        if (window.location.hash) {
            const targetElement = document.querySelector(window.location.hash);
            if (targetElement) {
                setTimeout(() => {
                    const navbar = document.querySelector('.navbar');
                    const navbarHeight = navbar.offsetHeight;
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
    
    // Product cards hover effect
    const productCards = document.querySelectorAll('.product-overview-card');
    productCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            const icon = this.querySelector('.product-icon i');
            if (icon) {
                icon.style.transform = 'scale(1.15) rotate(10deg)';
                icon.style.transition = 'transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            const icon = this.querySelector('.product-icon i');
            if (icon) {
                icon.style.transform = 'scale(1) rotate(0)';
            }
        });
    });
    
    // Feature cards hover effect
    const featureCards = document.querySelectorAll('.feature-card');
    featureCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            const icon = this.querySelector('.feature-icon i');
            if (icon) {
                icon.style.transform = 'scale(1.3)';
                icon.style.transition = 'transform 0.3s ease';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            const icon = this.querySelector('.feature-icon i');
            if (icon) {
                icon.style.transform = 'scale(1)';
            }
        });
    });
    
    // Notification function
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
                document.body.removeChild(notif);
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