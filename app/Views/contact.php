<!-- Contact Hero Section -->
<section class="contact-hero-section" style="background-image: url('https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80'); background-size: cover; background-position: center; position: relative;">
    <div class="container position-relative" style="z-index: 2;">
        <div class="row justify-content-center">
            <div class="col-lg-10 text-center text-white">
                <h1 class="display-4 fw-bold mb-4">Get in <span class="text-gradient">Touch</span></h1>
                <p class="lead mb-5">
                    Have a project in mind? Let's discuss how we can bring your vision to life with 
                    our engineering expertise and innovative solutions.
                </p>
                <div class="row g-4 justify-content-center">
                    <div class="col-md-4">
                        <div class="contact-info-card gradient-card">
                            <div class="contact-icon bg-white text-orange">
                                <i class="fas fa-phone"></i>
                            </div>
                            <h5 class="text-white">Call Us</h5>
                            <p class="mb-1 text-white">+62 31 5678 9012</p>
                            <p class="mb-1 text-white">+62 811 222 3333 (Emergency)</p>
                            <p class="text-white-50 small">Mon - Fri, 9:00 AM - 6:00 PM</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="contact-info-card gradient-card">
                            <div class="contact-icon bg-white text-orange">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <h5 class="text-white">Email Us</h5>
                            <p class="mb-1 text-white">info@cdw-engineering.co.id</p>
                            <p class="mb-1 text-white">sales@cdw-engineering.co.id</p>
                            <p class="text-white-50 small">Response within 24 hours</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="contact-info-card gradient-card">
                            <div class="contact-icon bg-white text-orange">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h5 class="text-white">Business Hours</h5>
                            <p class="mb-1 text-white">Monday - Friday</p>
                            <p class="mb-1 text-white">9:00 AM - 6:00 PM</p>
                            <p class="text-white-50 small">Saturday & Sunday: Closed</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Overlay gelap -->
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); z-index: 1;"></div>
</section>

<!-- Contact Form & Location Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Contact Form -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg rounded-xl">
                    <div class="card-body p-4 p-lg-5">
                        <h3 class="mb-4">Send Us a Message</h3>
                        <p class="text-muted mb-4">Fill out the form below and our team will get back to you shortly.</p>
                        
                        <form id="contactForm" method="POST" action="<?= base_url('contact/send') ?>">
                            <?= csrf_field() ?>
                            
                            <!-- Alert Message -->
                            <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <?= session()->getFlashdata('success') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?= session()->getFlashdata('error') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php endif; ?>
                            
                            <div class="row g-3">
                                <!-- Full Name -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="name" class="form-label fw-semibold">Full Name *</label>
                                        <input type="text" 
                                               class="form-control <?= session()->getFlashdata('errors.name') ? 'is-invalid' : '' ?>" 
                                               id="name" 
                                               name="name" 
                                               value="<?= old('name') ?>"
                                               placeholder="Your full name" 
                                               required>
                                        <?php if (session()->getFlashdata('errors.name')): ?>
                                            <div class="invalid-feedback">
                                                <?= session()->getFlashdata('errors.name') ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Company -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="company" class="form-label fw-semibold">Company</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="company" 
                                               name="company" 
                                               value="<?= old('company') ?>"
                                               placeholder="Your company name">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row g-3">
                                <!-- Email -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="email" class="form-label fw-semibold">Email Address *</label>
                                        <input type="email" 
                                               class="form-control <?= session()->getFlashdata('errors.email') ? 'is-invalid' : '' ?>" 
                                               id="email" 
                                               name="email" 
                                               value="<?= old('email') ?>"
                                               placeholder="your.email@example.com" 
                                               required>
                                        <?php if (session()->getFlashdata('errors.email')): ?>
                                            <div class="invalid-feedback">
                                                <?= session()->getFlashdata('errors.email') ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Phone -->
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="phone" class="form-label fw-semibold">Phone Number</label>
                                        <input type="tel" 
                                               class="form-control" 
                                               id="phone" 
                                               name="phone" 
                                               value="<?= old('phone') ?>"
                                               placeholder="+62 812 3456 7890">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Subject -->
                            <div class="form-group mb-3">
                                <label for="subject" class="form-label fw-semibold">Subject *</label>
                                <select class="form-select <?= session()->getFlashdata('errors.subject') ? 'is-invalid' : '' ?>" 
                                        id="subject" 
                                        name="subject" 
                                        required>
                                    <option value="" selected disabled>Select a subject</option>
                                    <option value="General Inquiry" <?= old('subject') == 'General Inquiry' ? 'selected' : '' ?>>General Inquiry</option>
                                    <option value="Engineering Services" <?= old('subject') == 'Engineering Services' ? 'selected' : '' ?>>Engineering Services</option>
                                    <option value="Construction Project" <?= old('subject') == 'Construction Project' ? 'selected' : '' ?>>Construction Project</option>
                                    <option value="Product Inquiry" <?= old('subject') == 'Product Inquiry' ? 'selected' : '' ?>>Product Inquiry</option>
                                    <option value="Partnership" <?= old('subject') == 'Partnership' ? 'selected' : '' ?>>Partnership</option>
                                    <option value="Career Opportunity" <?= old('subject') == 'Career Opportunity' ? 'selected' : '' ?>>Career Opportunity</option>
                                    <option value="Other" <?= old('subject') == 'Other' ? 'selected' : '' ?>>Other</option>
                                </select>
                                <?php if (session()->getFlashdata('errors.subject')): ?>
                                    <div class="invalid-feedback">
                                        <?= session()->getFlashdata('errors.subject') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Message -->
                            <div class="form-group mb-4">
                                <label for="message" class="form-label fw-semibold">Message *</label>
                                <textarea class="form-control <?= session()->getFlashdata('errors.message') ? 'is-invalid' : '' ?>" 
                                          id="message" 
                                          name="message" 
                                          rows="5" 
                                          placeholder="Tell us about your project or inquiry..." 
                                          required><?= old('message') ?></textarea>
                                <?php if (session()->getFlashdata('errors.message')): ?>
                                    <div class="invalid-feedback">
                                        <?= session()->getFlashdata('errors.message') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" class="btn-login btn-lg py-3">
                                    <i class="fas fa-paper-plane me-2"></i> Send Message
                                </button>
                            </div>
                            
                            <p class="text-muted small mt-3 mb-0">
                                <i class="fas fa-lock me-1"></i> Your information is secure and will never be shared with third parties.
                            </p>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Locations & Maps -->
            <div class="col-lg-6">
                <!-- Location Tabs -->
                <div class="card border-0 shadow-lg rounded-xl mb-4">
                    <div class="card-body p-0">
                        <!-- Tab Navigation -->
                        <ul class="nav nav-tabs location-tabs" id="locationTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="head-office-tab" data-bs-toggle="tab" data-bs-target="#head-office" type="button" role="tab">
                                    <i class="fas fa-building me-2"></i> Head Office
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="marketing-office-tab" data-bs-toggle="tab" data-bs-target="#marketing-office" type="button" role="tab">
                                    <i class="fas fa-chart-line me-2"></i> Marketing Office
                                </button>
                            </li>
                        </ul>
                        
                        <!-- Tab Content -->
                        <div class="tab-content p-4" id="locationTabsContent">
                            <!-- Head Office Tab -->
                            <div class="tab-pane fade show active" id="head-office" role="tabpanel">
                                <h4 class="mb-3">Head Office</h4>
                                <div class="location-details mb-4">
                                    <div class="d-flex align-items-start mb-3">
                                        <i class="fas fa-map-marker-alt text-orange mt-1 me-3"></i>
                                        <div>
                                            <p class="mb-1 fw-semibold">PT. CIPTA DUTA WACANA</p>
                                            <p class="mb-1">Beltway Office Park</p>
                                            <p class="mb-1">RT.7/RW.2, Ragunan</p>
                                            <p class="mb-1">Kota Jakarta Selatan</p>
                                            <p class="mb-0">Daerah Khusus Ibukota Jakarta</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fas fa-phone text-orange me-3"></i>
                                        <div>
                                            <p class="mb-0">+62 21 1234 5678</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-clock text-orange me-3"></i>
                                        <div>
                                            <p class="mb-0">Mon - Fri: 9:00 AM - 6:00 PM</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Head Office Map -->
                                <div class="map-container rounded mb-3" style="height: 250px;">
                                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.317217891317!2d106.8279203!3d-6.2916393!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f2296c6c4d3b%3A0x6e3b8e2b4c4b4b4b!2sBeltway%20Office%20Park%2C%20RT.7%2FRW.2%2C%20Ragunan%2C%20Kec.%20Ps.%20Minggu%2C%20Kota%20Jakarta%20Selatan%2C%20Daerah%20Khusus%20Ibukota%20Jakarta!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid" 
                                            width="100%" 
                                            height="100%" 
                                            style="border:0;" 
                                            allowfullscreen="" 
                                            loading="lazy" 
                                            referrerpolicy="no-referrer-when-downgrade">
                                    </iframe>
                                </div>
                                
                                <a href="https://maps.app.goo.gl/wugSpwJLnUqaiCmU7" target="_blank" class="btn btn-outline-orange btn-sm">
                                    <i class="fas fa-external-link-alt me-2"></i> Open in Google Maps
                                </a>
                            </div>
                            
                            <!-- Marketing Office Tab -->
                            <div class="tab-pane fade" id="marketing-office" role="tabpanel">
                                <h4 class="mb-3">Marketing Office</h4>
                                <div class="location-details mb-4">
                                    <div class="d-flex align-items-start mb-3">
                                        <i class="fas fa-map-marker-alt text-orange mt-1 me-3"></i>
                                        <div>
                                            <p class="mb-1 fw-semibold">PT. CIPTA DUTA WACANA</p>
                                            <p class="mb-1">Villa Bintaro Regency Blok K1 No 2</p>
                                            <p class="mb-1">Pondok Kacang Timur</p>
                                            <p class="mb-1">Tangerang Selatan 15226</p>
                                            <p class="mb-0">Banten, Indonesia</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fas fa-phone text-orange me-3"></i>
                                        <div>
                                            <p class="mb-0">+62 21 8765 4321</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-clock text-orange me-3"></i>
                                        <div>
                                            <p class="mb-0">Mon - Fri: 9:00 AM - 6:00 PM</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Marketing Office Map -->
                                <div class="map-container rounded mb-3" style="height: 250px;">
                                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.316437771233!2d106.7325673!3d-6.2560723!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f0b0b0b0b0b%3A0xb0b0b0b0b0b0b0b0!2sVilla%20Bintaro%20Regency%20Blok%20K1%20No%202%2C%20Pondok%20Kacang%20Timur%2C%20Tangerang%20Selatan%2C%20Banten%2015226!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid" 
                                            width="100%" 
                                            height="100%" 
                                            style="border:0;" 
                                            allowfullscreen="" 
                                            loading="lazy" 
                                            referrerpolicy="no-referrer-when-downgrade">
                                    </iframe>
                                </div>
                                
                                <a href="https://maps.app.goo.gl/FN4mo9A6DrAtyzts7" target="_blank" class="btn btn-outline-orange btn-sm">
                                    <i class="fas fa-external-link-alt me-2"></i> Open in Google Maps
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- FAQ Section -->
                <div class="card border-0 shadow-lg rounded-xl">
                    <div class="card-body p-4">
                        <h4 class="mb-4">Frequently Asked Questions</h4>
                        
                        <div class="accordion" id="faqAccordion">
                            <!-- FAQ 1 -->
                            <div class="accordion-item border-0 mb-3">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed rounded" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                        Which office should I contact?
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        <strong>Head Office:</strong> For general inquiries, partnership opportunities, and corporate matters.<br>
                                        <strong>Marketing Office:</strong> For sales inquiries, project proposals, and client meetings.
                                    </div>
                                </div>
                            </div>
                            
                            <!-- FAQ 2 -->
                            <div class="accordion-item border-0 mb-3">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed rounded" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                        How quickly can you respond to inquiries?
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        We typically respond within 24 hours during business days. For urgent matters, 
                                        please call the office directly.
                                    </div>
                                </div>
                            </div>
                            
                            <!-- FAQ 3 -->
                            <div class="accordion-item border-0">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed rounded" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                        Do you provide on-site consultations?
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Yes, we provide on-site consultations for project assessments. 
                                        Please schedule an appointment in advance through our contact form or phone.
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Contact -->
                        <div class="mt-4 pt-3 border-top">
                            <h5 class="mb-3">Need immediate assistance?</h5>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-headset text-orange me-3"></i>
                                        <div>
                                            <p class="mb-0 fw-semibold">Emergency Support</p>
                                            <p class="mb-0 text-muted">+62 811 222 3333 (24/7)</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-whatsapp text-success me-3"></i>
                                        <div>
                                            <p class="mb-0 fw-semibold">WhatsApp</p>
                                            <p class="mb-0 text-muted">+62 812 3456 7890</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<<!-- Additional Office Info -->
<section class="py-5 bg-gray-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="mb-3">Our <span class="text-gradient">Office Locations</span></h2>
            <p class="lead mb-0">Strategically located to serve you better across Indonesia</p>
        </div>
        
        <div class="row g-4">
            <!-- Head Office Card -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <!-- Foto Kantor Head Office -->
                    <div class="office-image-container" style="height: 250px; overflow: hidden; border-radius: 15px 15px 0 0;">
                        <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2069&q=80" 
                             alt="Head Office Building" 
                             class="w-100 h-100"
                             style="object-fit: cover; transition: transform 0.5s ease;">
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start mb-4">
                            <div class="office-badge bg-orange-light text-orange rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-building fa-lg"></i>
                            </div>
                            <div>
                                <h4 class="mb-1">Head Office</h4>
                                <p class="text-muted mb-0">Corporate & Administration Center</p>
                            </div>
                        </div>
                        
                        <ul class="list-unstyled mb-4">
                            <li class="d-flex align-items-start mb-2">
                                <i class="fas fa-map-marker-alt text-orange mt-1 me-2"></i>
                                <span>Beltway Office Park, Ragunan, Jakarta Selatan</span>
                            </li>
                            <li class="d-flex align-items-center mb-2">
                                <i class="fas fa-phone text-orange me-2"></i>
                                <span>+62 21 1234 5678</span>
                            </li>
                            <li class="d-flex align-items-center mb-2">
                                <i class="fas fa-envelope text-orange me-2"></i>
                                <span>corporate@cdw-engineering.co.id</span>
                            </li>
                            <li class="d-flex align-items-center">
                                <i class="fas fa-clock text-orange me-2"></i>
                                <span>Mon - Fri: 9:00 AM - 6:00 PM</span>
                            </li>
                        </ul>
                        
                        <div class="d-grid gap-2">
                            <a href="https://maps.app.goo.gl/wugSpwJLnUqaiCmU7" target="_blank" class="btn btn-outline-orange">
                                <i class="fas fa-directions me-2"></i> Get Directions
                            </a>
                            <!-- Tombol Lihat Foto -->
                            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#headOfficeModal">
                                <i class="fas fa-images me-2"></i> View Office Photos
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Marketing Office Card -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <!-- Foto Kantor Marketing Office -->
                    <div class="office-image-container" style="height: 250px; overflow: hidden; border-radius: 15px 15px 0 0;">
                        <img src="https://images.unsplash.com/photo-1497366811353-6870744d04b2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2069&q=80" 
                             alt="Marketing Office Building" 
                             class="w-100 h-100"
                             style="object-fit: cover; transition: transform 0.5s ease;">
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start mb-4">
                            <div class="office-badge bg-orange-light text-orange rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-chart-line fa-lg"></i>
                            </div>
                            <div>
                                <h4 class="mb-1">Marketing Office</h4>
                                <p class="text-muted mb-0">Sales & Client Relations Center</p>
                            </div>
                        </div>
                        
                        <ul class="list-unstyled mb-4">
                            <li class="d-flex align-items-start mb-2">
                                <i class="fas fa-map-marker-alt text-orange mt-1 me-2"></i>
                                <span>Villa Bintaro Regency, Tangerang Selatan 15226</span>
                            </li>
                            <li class="d-flex align-items-center mb-2">
                                <i class="fas fa-phone text-orange me-2"></i>
                                <span>+62 21 8765 4321</span>
                            </li>
                            <li class="d-flex align-items-center mb-2">
                                <i class="fas fa-envelope text-orange me-2"></i>
                                <span>sales@cdw-engineering.co.id</span>
                            </li>
                            <li class="d-flex align-items-center">
                                <i class="fas fa-clock text-orange me-2"></i>
                                <span>Mon - Fri: 9:00 AM - 6:00 PM</span>
                            </li>
                        </ul>
                        
                        <div class="d-grid gap-2">
                            <a href="https://maps.app.goo.gl/FN4mo9A6DrAtyzts7" target="_blank" class="btn btn-outline-orange">
                                <i class="fas fa-directions me-2"></i> Get Directions
                            </a>
                            <!-- Tombol Lihat Foto -->
                            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#marketingOfficeModal">
                                <i class="fas fa-images me-2"></i> View Office Photos
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal untuk Head Office Photos -->
<div class="modal fade" id="headOfficeModal" tabindex="-1" aria-labelledby="headOfficeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="headOfficeModalLabel">Head Office Gallery</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="headOfficeCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#headOfficeCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#headOfficeCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                        <button type="button" data-bs-target="#headOfficeCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                        <button type="button" data-bs-target="#headOfficeCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
                    </div>
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2069&q=80" class="d-block w-100" alt="Head Office Exterior" style="height: 400px; object-fit: cover;">
                            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded">
                                <h5>Head Office - Exterior</h5>
                                <p>Modern office building at Beltway Office Park</p>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="https://images.unsplash.com/photo-1497366754035-f200968a6a72?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2069&q=80" class="d-block w-100" alt="Head Office Lobby" style="height: 400px; object-fit: cover;">
                            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded">
                                <h5>Head Office - Lobby</h5>
                                <p>Welcoming reception area</p>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="https://images.unsplash.com/photo-1497366811353-6870744d04b2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2069&q=80" class="d-block w-100" alt="Head Office Workspace" style="height: 400px; object-fit: cover;">
                            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded">
                                <h5>Head Office - Workspace</h5>
                                <p>Modern and comfortable working environment</p>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="https://images.unsplash.com/photo-1497215842964-222b430dc094?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" class="d-block w-100" alt="Head Office Meeting Room" style="height: 400px; object-fit: cover;">
                            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded">
                                <h5>Head Office - Meeting Room</h5>
                                <p>Fully equipped meeting facilities</p>
                            </div>
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#headOfficeCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#headOfficeCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Marketing Office Photos -->
<div class="modal fade" id="marketingOfficeModal" tabindex="-1" aria-labelledby="marketingOfficeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="marketingOfficeModalLabel">Marketing Office Gallery</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="marketingOfficeCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#marketingOfficeCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#marketingOfficeCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                        <button type="button" data-bs-target="#marketingOfficeCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                        <button type="button" data-bs-target="#marketingOfficeCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
                    </div>
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="https://images.unsplash.com/photo-1497366811353-6870744d04b2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2069&q=80" class="d-block w-100" alt="Marketing Office Exterior" style="height: 400px; object-fit: cover;">
                            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded">
                                <h5>Marketing Office - Exterior</h5>
                                <p>Contemporary office at Villa Bintaro Regency</p>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="https://images.unsplash.com/photo-1497215842964-222b430dc094?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" class="d-block w-100" alt="Marketing Office Reception" style="height: 400px; object-fit: cover;">
                            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded">
                                <h5>Marketing Office - Reception</h5>
                                <p>Professional and welcoming atmosphere</p>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="https://images.unsplash.com/photo-1497366754035-f200968a6a72?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2069&q=80" class="d-block w-100" alt="Marketing Office Sales Area" style="height: 400px; object-fit: cover;">
                            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded">
                                <h5>Marketing Office - Sales Area</h5>
                                <p>Dedicated space for client meetings</p>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2069&q=80" class="d-block w-100" alt="Marketing Office Team Space" style="height: 400px; object-fit: cover;">
                            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded">
                                <h5>Marketing Office - Team Space</h5>
                                <p>Collaborative and productive environment</p>
                            </div>
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#marketingOfficeCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#marketingOfficeCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS Tambahan untuk Contact Page -->
<style>
    
    /* Contact Hero */
    .contact-hero-section {
        padding: 120px 0 80px;
    }
    
    /* Contact Info Cards */
    .contact-info-card {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        text-align: center;
        height: 100%;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(255, 107, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    .contact-info-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(255, 107, 0, 0.15);
        border-color: rgba(255, 107, 0, 0.3);
    }
    
    .contact-icon {
        width: 70px;
        height: 70px;
        background: var(--cdw-orange-gradient);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: white;
        font-size: 1.8rem;
    }
    
    .contact-info-card h5 {
        color: #212529 !important;
        margin-bottom: 0.75rem;
        font-weight: 600;
    }
    
    .contact-info-card p {
        color: #212529 !important;
    }
    
    .contact-info-card .text-muted {
        color: #6c757d !important;
    }
    
    /* Location Tabs */
    .location-tabs {
        background: var(--cdw-gray-light);
        border-radius: 12px 12px 0 0;
        padding: 0.5rem 0.5rem 0;
        border-bottom: 1px solid rgba(255, 107, 0, 0.1);
    }
    
    .location-tabs .nav-link {
        border: none;
        border-radius: 8px 8px 0 0;
        color: var(--cdw-gray-medium);
        font-weight: 500;
        padding: 0.75rem 1.25rem;
        margin-bottom: -1px;
        background: transparent;
    }
    
    .location-tabs .nav-link.active {
        background: white;
        color: var(--cdw-orange-primary);
        border-bottom: 3px solid var(--cdw-orange-primary);
    }
    
    .location-tabs .nav-link:hover:not(.active) {
        color: var(--cdw-orange-primary);
        background: rgba(255, 107, 0, 0.05);
    }
    
    /* Location Details */
    .location-details p {
        margin-bottom: 0.25rem;
        line-height: 1.5;
    }
    
    /* Map Container */
    .map-container {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.1);
    }
    
    /* Form Styling */
    .form-control, .form-select {
        padding: 12px 16px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--cdw-orange-primary);
        box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.1);
    }
    
    .form-label {
        color: var(--cdw-gray-dark);
        margin-bottom: 0.5rem;
    }
    
    /* Alert Styling */
    .alert {
        border-radius: 10px;
        border: none;
        padding: 1rem 1.25rem;
    }
    
    .alert-success {
        background-color: rgba(76, 175, 80, 0.1);
        color: #2e7d32;
        border-left: 4px solid #4caf50;
    }
    
    .alert-danger {
        background-color: rgba(244, 67, 54, 0.1);
        color: #c62828;
        border-left: 4px solid #f44336;
    }
    
    /* Accordion Styling */
    .accordion-button {
        background-color: white;
        color: var(--cdw-gray-dark);
        font-weight: 500;
        padding: 1rem 1.25rem;
        border: 1px solid rgba(255, 107, 0, 0.1);
    }
    
    .accordion-button:not(.collapsed) {
        background-color: rgba(255, 107, 0, 0.05);
        color: var(--cdw-orange-primary);
        box-shadow: none;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
    }
    
    .accordion-button:focus {
        border-color: var(--cdw-orange-primary);
        box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.1);
    }
    
    .accordion-button::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23FF6B00'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
    }
    
    .accordion-button:not(.collapsed)::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23FF6B00'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
    }
    
    .accordion-body {
        padding: 1rem 1.25rem;
        background-color: rgba(255, 107, 0, 0.02);
        border: 1px solid rgba(255, 107, 0, 0.1);
        border-top: none;
        border-bottom-left-radius: 8px;
        border-bottom-right-radius: 8px;
    }
    
    /* Outline Orange Button */
    .btn-outline-orange {
        border: 2px solid var(--cdw-orange-primary);
        color: var(--cdw-orange-primary);
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .btn-outline-orange:hover {
        background: var(--cdw-orange-gradient);
        color: white;
        border-color: transparent;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 107, 0, 0.2);
    }
    
    /* Office Badge */
    .office-badge {
        width: 50px;
        height: 50px;
        flex-shrink: 0;
    }
    
    /* Responsive */
    @media (max-width: 991.98px) {
        .contact-hero-section {
            padding: 100px 0 60px;
        }
    }
    
    @media (max-width: 767.98px) {
        .contact-hero-section {
            padding: 80px 0 40px;
        }
        
        .contact-info-card {
            padding: 1.5rem;
        }
        
        .contact-icon {
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .location-tabs .nav-link {
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
        }
    }
    
    @media (max-width: 575.98px) {
        .location-tabs .nav-link {
            font-size: 0.85rem;
        }
        
        .location-tabs .nav-link i {
            display: block;
            margin: 0 0 0.25rem 0;
            text-align: center;
        }
    }
    /* Office Image Container */
.office-image-container {
    position: relative;
    overflow: hidden;
    border-radius: 15px 15px 0 0;
}

.office-image-container:hover img {
    transform: scale(1.1);
}

.office-image-container::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,0.1), rgba(0,0,0,0.3));
    pointer-events: none;
}

/* Modal styling */
.modal-content {
    border: none;
    border-radius: 15px;
    overflow: hidden;
}

.modal-header {
    background: var(--cdw-orange-gradient);
    color: white;
    border: none;
}

.modal-header .btn-close {
    filter: brightness(0) invert(1);
}

.carousel-caption {
    background: rgba(0, 0, 0, 0.6);
    border-radius: 10px;
    padding: 10px 20px;
    bottom: 20px;
}

.carousel-indicators button {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin: 0 5px;
}

.carousel-control-prev,
.carousel-control-next {
    width: 10%;
    opacity: 0.8;
}

.carousel-control-prev:hover,
.carousel-control-next:hover {
    opacity: 1;
}

/* Button styling */
.btn-outline-secondary {
    border: 2px solid #6c757d;
    color: #6c757d;
    font-weight: 500;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    background: #6c757d;
    color: white;
    border-color: #6c757d;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(108, 117, 125, 0.2);
}
/* Gradient Cards */
.gradient-card {
    background: linear-gradient(135deg, #ce7303 0%, #ccad01 100%) !important;
    border: none !important;
    box-shadow: 0 10px 30px rgba(255, 140, 0, 0.3) !important;
}

.gradient-card:hover {
    background: linear-gradient(135deg, #FF9F1C 0%, #b69801 100%) !important;
    box-shadow: 0 15px 40px rgba(255, 140, 0, 0.5) !important;
}

.gradient-card .contact-icon {
    background: white !important;
    color: #d57806 !important;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.gradient-card h5 {
    color: white !important;
    font-weight: 600;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.gradient-card p {
    color: white !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.gradient-card .text-white-50 {
    color: rgba(255, 255, 255, 0.8) !important;
}

/* Update contact icon style */
.contact-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 1.8rem;
    transition: all 0.3s ease;
}

.contact-icon.bg-white {
    background: white;
    color: #b96703;
}

.gradient-card:hover .contact-icon {
    transform: scale(1.1) rotate(5deg);
}
</style>

<!-- JavaScript untuk Contact Page -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    
    if (contactForm) {
        // Real-time validation
        const inputs = contactForm.querySelectorAll('input, textarea, select');
        
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    validateField(this);
                }
            });
        });
        
        // Form submission
        contactForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            inputs.forEach(input => {
                if (!validateField(input)) {
                    isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                // Scroll to first error
                const firstError = contactForm.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
            }
        });
        
        // Field validation function
        function validateField(field) {
            const value = field.value.trim();
            const fieldName = field.name;
            let isValid = true;
            
            // Clear previous validation
            field.classList.remove('is-invalid', 'is-valid');
            
            // Required field validation
            if (field.hasAttribute('required') && !value) {
                field.classList.add('is-invalid');
                isValid = false;
            }
            
            // Email validation
            if (fieldName === 'email' && value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    field.classList.add('is-invalid');
                    isValid = false;
                }
            }
            
            // Phone validation (optional)
            if (fieldName === 'phone' && value) {
                const phoneRegex = /^[\d\s\-\+\(\)]{10,20}$/;
                if (!phoneRegex.test(value)) {
                    field.classList.add('is-invalid');
                    isValid = false;
                }
            }
            
            // If valid, add valid class
            if (isValid && value) {
                field.classList.add('is-valid');
            }
            
            return isValid;
        }
        
        // Auto-close alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    }
    
    // Save active tab to localStorage
    const locationTabs = document.getElementById('locationTabs');
    if (locationTabs) {
        const tabButtons = locationTabs.querySelectorAll('button[data-bs-toggle="tab"]');
        
        // Load saved tab
        const savedTab = localStorage.getItem('contactActiveTab');
        if (savedTab) {
            const tabElement = document.querySelector(savedTab);
            if (tabElement) {
                const tab = new bootstrap.Tab(tabElement);
                tab.show();
            }
        }
        
        // Save tab on change
        tabButtons.forEach(button => {
            button.addEventListener('shown.bs.tab', function(event) {
                localStorage.setItem('contactActiveTab', event.target.getAttribute('data-bs-target'));
            });
        });
    }
    
    // Smooth scroll for FAQ accordion
    const faqButtons = document.querySelectorAll('.accordion-button');
    faqButtons.forEach(button => {
        button.addEventListener('click', function() {
            setTimeout(() => {
                this.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }, 300);
        });
    });
    
    // Initialize Google Maps
    const maps = document.querySelectorAll('iframe[src*="google.com/maps"]');
    maps.forEach(map => {
        map.addEventListener('load', function() {
            console.log('Map loaded:', this.src);
        });
    });
});
</script>