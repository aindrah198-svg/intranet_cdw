<!-- Projects Hero Section with Background Image -->
<section class="projects-hero-section" style="background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80') center/cover fixed; color: white; padding: 120px 0; position: relative;">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-4 animate__animated animate__fadeInDown">Our <span class="text-gradient-light">Projects</span></h1>
        <p class="lead mb-5 animate__animated animate__fadeInUp">
            Showcasing our successful installations and engineering solutions across Indonesia. 
            We specialize in PERTASHOP installations and industrial projects.
        </p>
        
        <!-- Projects Stats with Gradient Cards -->
        <div class="projects-stats animate__animated animate__fadeInUp animate__delay-1s">
            <div class="row g-4 justify-content-center">
                <div class="col-6 col-md-3">
                    <div class="stat-card gradient-card">
                        <div class="stat-number display-6 fw-bold text-white">15+</div>
                        <div class="stat-label text-white-50">PERTASHOP Projects</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card gradient-card">
                        <div class="stat-number display-6 fw-bold text-white">30+</div>
                        <div class="stat-label text-white-50">Valued Clients</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card gradient-card">
                        <div class="stat-number display-6 fw-bold text-white">8</div>
                        <div class="stat-label text-white-50">Provinces Covered</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card gradient-card">
                        <div class="stat-number display-6 fw-bold text-white">100%</div>
                        <div class="stat-label text-white-50">Satisfaction Rate</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Projects Grid Section -->
<section class="py-5 bg-gray-light">
    <div class="container">
        <!-- Projects Filter - Fixed Design -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-xl">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-3 mb-3 mb-md-0">
                                <h5 class="mb-0 text-gradient fw-bold">Filter by Region</h5>
                            </div>
                            <div class="col-md-9">
                                <div class="projects-filter d-flex flex-wrap gap-2 justify-content-md-end">
                                    <button class="filter-btn active" data-filter="all">All Projects</button>
                                    <button class="filter-btn" data-filter="jawa-timur">Jawa Timur</button>
                                    <button class="filter-btn" data-filter="jawa-tengah">Jawa Tengah</button>
                                    <button class="filter-btn" data-filter="jawa-barat">Jawa Barat</button>
                                    <button class="filter-btn" data-filter="bali">Bali</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Projects Grid -->
        <div class="row g-4" id="projectsGrid">
            <!-- Project 1: Tempurejo Jawa Timur -->
            <div class="col-lg-4 col-md-6 project-item" data-category="jawa-timur" data-index="0">
                <div class="project-card">
                    <div class="project-number gradient-card">
                        <span>01</span>
                    </div>
                    <div class="project-image">
                        <img src="https://images.unsplash.com/photo-1621905251189-08b45d6a269e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             alt="PERTASHOP Tempurejo Jawa Timur" 
                             class="img-fluid">
                        <div class="project-overlay">
                            <button class="btn-view" data-bs-toggle="modal" data-bs-target="#projectModal">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                    <div class="project-info">
                        <div class="project-category">
                            <span class="badge gradient-card text-white">PERTASHOP Installation</span>
                            <span class="badge bg-light text-dark ms-2">Jawa Timur</span>
                        </div>
                        <h5 class="project-title">Instalasi PERTASHOP di Tempurejo</h5>
                        <p class="project-desc">Complete installation of PERTASHOP retail outlet with modern facilities</p>
                        <div class="project-meta">
                            <span><i class="fas fa-calendar-alt me-1"></i> Completed</span>
                            <span><i class="fas fa-map-marker-alt ms-3 me-1"></i> Tempurejo, Jawa Timur</span>
                        </div>
                        <div class="project-client">
                            <small class="text-muted">Client:</small>
                            <p class="mb-0 fw-semibold">PT. Pertamina (Persero)</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Project 2: Puger Jawa Timur -->
            <div class="col-lg-4 col-md-6 project-item" data-category="jawa-timur" data-index="1">
                <div class="project-card">
                    <div class="project-number gradient-card">
                        <span>02</span>
                    </div>
                    <div class="project-image">
                        <img src="https://images.unsplash.com/photo-1621905252507-b35492cc74b4?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             alt="PERTASHOP Puger Jawa Timur" 
                             class="img-fluid">
                        <div class="project-overlay">
                            <button class="btn-view" data-bs-toggle="modal" data-bs-target="#projectModal">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                    <div class="project-info">
                        <div class="project-category">
                            <span class="badge gradient-card text-white">PERTASHOP Installation</span>
                            <span class="badge bg-light text-dark ms-2">Jawa Timur</span>
                        </div>
                        <h5 class="project-title">Instalasi PERTASHOP di Puger</h5>
                        <p class="project-desc">Modern retail outlet installation with integrated fuel management system</p>
                        <div class="project-meta">
                            <span><i class="fas fa-calendar-alt me-1"></i> Completed</span>
                            <span><i class="fas fa-map-marker-alt ms-3 me-1"></i> Puger, Jawa Timur</span>
                        </div>
                        <div class="project-client">
                            <small class="text-muted">Client:</small>
                            <p class="mb-0 fw-semibold">PT. Pertamina (Persero)</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Project 3: Batu Marmar Jawa Timur -->
            <div class="col-lg-4 col-md-6 project-item" data-category="jawa-timur" data-index="2">
                <div class="project-card">
                    <div class="project-number gradient-card">
                        <span>03</span>
                    </div>
                    <div class="project-image">
                        <img src="https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             alt="PERTASHOP Batu Marmar Jawa Timur" 
                             class="img-fluid">
                        <div class="project-overlay">
                            <button class="btn-view" data-bs-toggle="modal" data-bs-target="#projectModal">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                    <div class="project-info">
                        <div class="project-category">
                            <span class="badge gradient-card text-white">PERTASHOP Installation</span>
                            <span class="badge bg-light text-dark ms-2">Jawa Timur</span>
                        </div>
                        <h5 class="project-title">Instalasi PERTASHOP di Batu Marmar</h5>
                        <p class="project-desc">Strategic location installation with enhanced customer facilities</p>
                        <div class="project-meta">
                            <span><i class="fas fa-calendar-alt me-1"></i> Completed</span>
                            <span><i class="fas fa-map-marker-alt ms-3 me-1"></i> Batu Marmar, Jawa Timur</span>
                        </div>
                        <div class="project-client">
                            <small class="text-muted">Client:</small>
                            <p class="mb-0 fw-semibold">PT. Pertamina (Persero)</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Project 4: Kemiri Jawa Tengah -->
            <div class="col-lg-4 col-md-6 project-item" data-category="jawa-tengah" data-index="3">
                <div class="project-card">
                    <div class="project-number gradient-card">
                        <span>04</span>
                    </div>
                    <div class="project-image">
                        <img src="https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             alt="PERTASHOP Kemiri Jawa Tengah" 
                             class="img-fluid">
                        <div class="project-overlay">
                            <button class="btn-view" data-bs-toggle="modal" data-bs-target="#projectModal">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                    <div class="project-info">
                        <div class="project-category">
                            <span class="badge gradient-card text-white">PERTASHOP Installation</span>
                            <span class="badge bg-light text-dark ms-2">Jawa Tengah</span>
                        </div>
                        <h5 class="project-title">Instalasi PERTASHOP di Kemiri</h5>
                        <p class="project-desc">Complete turnkey project including civil works and mechanical installation</p>
                        <div class="project-meta">
                            <span><i class="fas fa-calendar-alt me-1"></i> Completed</span>
                            <span><i class="fas fa-map-marker-alt ms-3 me-1"></i> Kemiri, Jawa Tengah</span>
                        </div>
                        <div class="project-client">
                            <small class="text-muted">Client:</small>
                            <p class="mb-0 fw-semibold">PT. Pertamina (Persero)</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Project 5: Klampis Jawa Timur -->
            <div class="col-lg-4 col-md-6 project-item" data-category="jawa-timur" data-index="4">
                <div class="project-card">
                    <div class="project-number gradient-card">
                        <span>05</span>
                    </div>
                    <div class="project-image">
                        <img src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             alt="PERTASHOP Klampis Jawa Timur" 
                             class="img-fluid">
                        <div class="project-overlay">
                            <button class="btn-view" data-bs-toggle="modal" data-bs-target="#projectModal">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                    <div class="project-info">
                        <div class="project-category">
                            <span class="badge gradient-card text-white">PERTASHOP Installation</span>
                            <span class="badge bg-light text-dark ms-2">Jawa Timur</span>
                        </div>
                        <h5 class="project-title">Instalasi PERTASHOP di Klampis</h5>
                        <p class="project-desc">Modern retail outlet with advanced fuel dispensing technology</p>
                        <div class="project-meta">
                            <span><i class="fas fa-calendar-alt me-1"></i> Completed</span>
                            <span><i class="fas fa-map-marker-alt ms-3 me-1"></i> Klampis, Jawa Timur</span>
                        </div>
                        <div class="project-client">
                            <small class="text-muted">Client:</small>
                            <p class="mb-0 fw-semibold">PT. Pertamina (Persero)</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Project 6: Cipeundeuy Jawa Barat -->
            <div class="col-lg-4 col-md-6 project-item" data-category="jawa-barat" data-index="5">
                <div class="project-card">
                    <div class="project-number gradient-card">
                        <span>06</span>
                    </div>
                    <div class="project-image">
                        <img src="https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             alt="PERTASHOP Cipeundeuy Jawa Barat" 
                             class="img-fluid">
                        <div class="project-overlay">
                            <button class="btn-view" data-bs-toggle="modal" data-bs-target="#projectModal">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                    <div class="project-info">
                        <div class="project-category">
                            <span class="badge gradient-card text-white">PERTASHOP Installation</span>
                            <span class="badge bg-light text-dark ms-2">Jawa Barat</span>
                        </div>
                        <h5 class="project-title">Instalasi PERTASHOP di Cipeundeuy</h5>
                        <p class="project-desc">Strategic highway location with high traffic volume</p>
                        <div class="project-meta">
                            <span><i class="fas fa-calendar-alt me-1"></i> Completed</span>
                            <span><i class="fas fa-map-marker-alt ms-3 me-1"></i> Cipeundeuy, Jawa Barat</span>
                        </div>
                        <div class="project-client">
                            <small class="text-muted">Client:</small>
                            <p class="mb-0 fw-semibold">PT. Pertamina (Persero)</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Project 7: Cilongok Jawa Tengah -->
            <div class="col-lg-4 col-md-6 project-item" data-category="jawa-tengah" data-index="6">
                <div class="project-card">
                    <div class="project-number gradient-card">
                        <span>07</span>
                    </div>
                    <div class="project-image">
                        <img src="https://images.unsplash.com/photo-1581092160562-40aa08e78837?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             alt="PERTASHOP Cilongok Jawa Tengah" 
                             class="img-fluid">
                        <div class="project-overlay">
                            <button class="btn-view" data-bs-toggle="modal" data-bs-target="#projectModal">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                    <div class="project-info">
                        <div class="project-category">
                            <span class="badge gradient-card text-white">PERTASHOP Installation</span>
                            <span class="badge bg-light text-dark ms-2">Jawa Tengah</span>
                        </div>
                        <h5 class="project-title">Instalasi PERTASHOP di Cilongok</h5>
                        <p class="project-desc">Complete facility including convenience store and service area</p>
                        <div class="project-meta">
                            <span><i class="fas fa-calendar-alt me-1"></i> Completed</span>
                            <span><i class="fas fa-map-marker-alt ms-3 me-1"></i> Cilongok, Jawa Tengah</span>
                        </div>
                        <div class="project-client">
                            <small class="text-muted">Client:</small>
                            <p class="mb-0 fw-semibold">PT. Pertamina (Persero)</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Project 8: Kintamani Bali -->
            <div class="col-lg-4 col-md-6 project-item" data-category="bali" data-index="7">
                <div class="project-card">
                    <div class="project-number gradient-card">
                        <span>08</span>
                    </div>
                    <div class="project-image">
                        <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             alt="PERTASHOP Kintamani Bali" 
                             class="img-fluid">
                        <div class="project-overlay">
                            <button class="btn-view" data-bs-toggle="modal" data-bs-target="#projectModal">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                    <div class="project-info">
                        <div class="project-category">
                            <span class="badge gradient-card text-white">PERTASHOP Installation</span>
                            <span class="badge bg-light text-dark ms-2">Bali</span>
                        </div>
                        <h5 class="project-title">Instalasi PERTASHOP di Kintamani</h5>
                        <p class="project-desc">Tourist area installation with enhanced facilities</p>
                        <div class="project-meta">
                            <span><i class="fas fa-calendar-alt me-1"></i> Completed</span>
                            <span><i class="fas fa-map-marker-alt ms-3 me-1"></i> Kintamani, Bali</span>
                        </div>
                        <div class="project-client">
                            <small class="text-muted">Client:</small>
                            <p class="mb-0 fw-semibold">PT. Pertamina (Persero)</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Project 9: Kedung Adem Jawa Timur -->
            <div class="col-lg-4 col-md-6 project-item" data-category="jawa-timur" data-index="8">
                <div class="project-card">
                    <div class="project-number gradient-card">
                        <span>09</span>
                    </div>
                    <div class="project-image">
                        <img src="https://images.unsplash.com/photo-1621905251189-08b45d6a269e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             alt="PERTASHOP Kedung Adem Jawa Timur" 
                             class="img-fluid">
                        <div class="project-overlay">
                            <button class="btn-view" data-bs-toggle="modal" data-bs-target="#projectModal">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                    <div class="project-info">
                        <div class="project-category">
                            <span class="badge gradient-card text-white">PERTASHOP Installation</span>
                            <span class="badge bg-light text-dark ms-2">Jawa Timur</span>
                        </div>
                        <h5 class="project-title">Instalasi PERTASHOP di Kedung Adem</h5>
                        <p class="project-desc">Rural area development project</p>
                        <div class="project-meta">
                            <span><i class="fas fa-calendar-alt me-1"></i> Completed</span>
                            <span><i class="fas fa-map-marker-alt ms-3 me-1"></i> Kedung Adem, Jawa Timur</span>
                        </div>
                        <div class="project-client">
                            <small class="text-muted">Client:</small>
                            <p class="mb-0 fw-semibold">PT. Pertamina (Persero)</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Project 10: Widang Jawa Timur -->
            <div class="col-lg-4 col-md-6 project-item" data-category="jawa-timur" data-index="9">
                <div class="project-card">
                    <div class="project-number gradient-card">
                        <span>10</span>
                    </div>
                    <div class="project-image">
                        <img src="https://images.unsplash.com/photo-1621905252507-b35492cc74b4?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             alt="PERTASHOP Widang Jawa Timur" 
                             class="img-fluid">
                        <div class="project-overlay">
                            <button class="btn-view" data-bs-toggle="modal" data-bs-target="#projectModal">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                    <div class="project-info">
                        <div class="project-category">
                            <span class="badge gradient-card text-white">PERTASHOP Installation</span>
                            <span class="badge bg-light text-dark ms-2">Jawa Timur</span>
                        </div>
                        <h5 class="project-title">Instalasi PERTASHOP di Widang</h5>
                        <p class="project-desc">High-volume commercial area installation</p>
                        <div class="project-meta">
                            <span><i class="fas fa-calendar-alt me-1"></i> Completed</span>
                            <span><i class="fas fa-map-marker-alt ms-3 me-1"></i> Kecamatan Widang, Jawa Timur</span>
                        </div>
                        <div class="project-client">
                            <small class="text-muted">Client:</small>
                            <p class="mb-0 fw-semibold">PT. Pertamina (Persero)</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Project 11: Tempurejo 2 Jawa Timur -->
            <div class="col-lg-4 col-md-6 project-item" data-category="jawa-timur" data-index="10">
                <div class="project-card">
                    <div class="project-number gradient-card">
                        <span>11</span>
                    </div>
                    <div class="project-image">
                        <img src="https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             alt="PERTASHOP Tempurejo Jawa Timur" 
                             class="img-fluid">
                        <div class="project-overlay">
                            <button class="btn-view" data-bs-toggle="modal" data-bs-target="#projectModal">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                    <div class="project-info">
                        <div class="project-category">
                            <span class="badge gradient-card text-white">PERTASHOP Installation</span>
                            <span class="badge bg-light text-dark ms-2">Jawa Timur</span>
                        </div>
                        <h5 class="project-title">Instalasi PERTASHOP di Tempurejo</h5>
                        <p class="project-desc">Second installation in strategic location</p>
                        <div class="project-meta">
                            <span><i class="fas fa-calendar-alt me-1"></i> Completed</span>
                            <span><i class="fas fa-map-marker-alt ms-3 me-1"></i> Tempurejo, Jawa Timur</span>
                        </div>
                        <div class="project-client">
                            <small class="text-muted">Client:</small>
                            <p class="mb-0 fw-semibold">PT. Pertamina (Persero)</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Project 12: Kubutambahan Bali -->
            <div class="col-lg-4 col-md-6 project-item" data-category="bali" data-index="11">
                <div class="project-card">
                    <div class="project-number gradient-card">
                        <span>12</span>
                    </div>
                    <div class="project-image">
                        <img src="https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             alt="PERTASHOP Kubutambahan Bali" 
                             class="img-fluid">
                        <div class="project-overlay">
                            <button class="btn-view" data-bs-toggle="modal" data-bs-target="#projectModal">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                    <div class="project-info">
                        <div class="project-category">
                            <span class="badge gradient-card text-white">PERTASHOP Installation</span>
                            <span class="badge bg-light text-dark ms-2">Bali</span>
                        </div>
                        <h5 class="project-title">Instalasi PERTASHOP di Kubutambahan</h5>
                        <p class="project-desc">Coastal area installation</p>
                        <div class="project-meta">
                            <span><i class="fas fa-calendar-alt me-1"></i> Completed</span>
                            <span><i class="fas fa-map-marker-alt ms-3 me-1"></i> Kubutambahan, Bali</span>
                        </div>
                        <div class="project-client">
                            <small class="text-muted">Client:</small>
                            <p class="mb-0 fw-semibold">PT. Pertamina (Persero)</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Project 13: Sawan Bali -->
            <div class="col-lg-4 col-md-6 project-item" data-category="bali" data-index="12">
                <div class="project-card">
                    <div class="project-number gradient-card">
                        <span>13</span>
                    </div>
                    <div class="project-image">
                        <img src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             alt="PERTASHOP Sawan Bali" 
                             class="img-fluid">
                        <div class="project-overlay">
                            <button class="btn-view" data-bs-toggle="modal" data-bs-target="#projectModal">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                    <div class="project-info">
                        <div class="project-category">
                            <span class="badge gradient-card text-white">PERTASHOP Installation</span>
                            <span class="badge bg-light text-dark ms-2">Bali</span>
                        </div>
                        <h5 class="project-title">Instalasi PERTASHOP di Sawan</h5>
                        <p class="project-desc">Modern facility with local architectural elements</p>
                        <div class="project-meta">
                            <span><i class="fas fa-calendar-alt me-1"></i> Completed</span>
                            <span><i class="fas fa-map-marker-alt ms-3 me-1"></i> Sawan, Bali</span>
                        </div>
                        <div class="project-client">
                            <small class="text-muted">Client:</small>
                            <p class="mb-0 fw-semibold">PT. Pertamina (Persero)</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Project 14: Rendang Bali -->
            <div class="col-lg-4 col-md-6 project-item" data-category="bali" data-index="13">
                <div class="project-card">
                    <div class="project-number gradient-card">
                        <span>14</span>
                    </div>
                    <div class="project-image">
                        <img src="https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             alt="PERTASHOP Rendang Bali" 
                             class="img-fluid">
                        <div class="project-overlay">
                            <button class="btn-view" data-bs-toggle="modal" data-bs-target="#projectModal">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                    <div class="project-info">
                        <div class="project-category">
                            <span class="badge gradient-card text-white">PERTASHOP Installation</span>
                            <span class="badge bg-light text-dark ms-2">Bali</span>
                        </div>
                        <h5 class="project-title">Instalasi PERTASHOP di Rendang</h5>
                        <p class="project-desc">Mountainous area installation</p>
                        <div class="project-meta">
                            <span><i class="fas fa-calendar-alt me-1"></i> Completed</span>
                            <span><i class="fas fa-map-marker-alt ms-3 me-1"></i> Rendang, Bali</span>
                        </div>
                        <div class="project-client">
                            <small class="text-muted">Client:</small>
                            <p class="mb-0 fw-semibold">PT. Pertamina (Persero)</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Project 15: Buleleng Bali -->
            <div class="col-lg-4 col-md-6 project-item" data-category="bali" data-index="14">
                <div class="project-card">
                    <div class="project-number gradient-card">
                        <span>15</span>
                    </div>
                    <div class="project-image">
                        <img src="https://images.unsplash.com/photo-1581092160562-40aa08e78837?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             alt="PERTASHOP Buleleng Bali" 
                             class="img-fluid">
                        <div class="project-overlay">
                            <button class="btn-view" data-bs-toggle="modal" data-bs-target="#projectModal">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                    <div class="project-info">
                        <div class="project-category">
                            <span class="badge gradient-card text-white">PERTASHOP Installation</span>
                            <span class="badge bg-light text-dark ms-2">Bali</span>
                        </div>
                        <h5 class="project-title">Instalasi PERTASHOP di Buleleng</h5>
                        <p class="project-desc">Urban commercial installation</p>
                        <div class="project-meta">
                            <span><i class="fas fa-calendar-alt me-1"></i> Completed</span>
                            <span><i class="fas fa-map-marker-alt ms-3 me-1"></i> Kecamatan Buleleng, Bali</span>
                        </div>
                        <div class="project-client">
                            <small class="text-muted">Client:</small>
                            <p class="mb-0 fw-semibold">PT. Pertamina (Persero)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Valued Customers -->
<section class="py-5">
    <div class="container">
        <div class="section-header mb-5">
            <h2 class="section-title">Our <span class="text-gradient">Valued Customers</span></h2>
            <p class="section-subtitle">
                We are proud to have served prestigious clients across various industries
            </p>
        </div>
        
        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm rounded-xl mb-5">
                    <div class="card-body p-4 p-lg-5">
                        <div class="row">
                            <!-- Column 1 -->
                            <div class="col-md-4 mb-4">
                                <div class="customer-group">
                                    <h5 class="mb-3 text-gradient">Major Corporations</h5>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><i class="fas fa-building text-orange me-2"></i>PT. Sapta Indra Sejati</li>
                                        <li class="mb-2"><i class="fas fa-building text-orange me-2"></i>PT. Rekayasa Industri (Persero)</li>
                                        <li class="mb-2"><i class="fas fa-building text-orange me-2"></i>PT. Patria (United Tractor Group)</li>
                                        <li class="mb-2"><i class="fas fa-building text-orange me-2"></i>PT. Sanggar Sarana Baja</li>
                                        <li class="mb-2"><i class="fas fa-building text-orange me-2"></i>PT. Sumberdaya Sewatama</li>
                                        <li class="mb-2"><i class="fas fa-building text-orange me-2"></i>PT. Fuchs Indonesia</li>
                                        <li class="mb-2"><i class="fas fa-building text-orange me-2"></i>PT. Tempo Scan Pacific</li>
                                        <li class="mb-2"><i class="fas fa-building text-orange me-2"></i>PT. Pama Persada (Astra Group)</li>
                                        <li class="mb-2"><i class="fas fa-building text-orange me-2"></i>PT. Medco Energy</li>
                                        <li class="mb-2"><i class="fas fa-building text-orange me-2"></i>PT. Pertamina (Persero)</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <!-- Column 2 -->
                            <div class="col-md-4 mb-4">
                                <div class="customer-group">
                                    <h5 class="mb-3 text-gradient">Energy & Resources</h5>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><i class="fas fa-gas-pump text-orange me-2"></i>PT. Cipta Niaga Gas (CNG)</li>
                                        <li class="mb-2"><i class="fas fa-gas-pump text-orange me-2"></i>PT. Gunung Bayan Group</li>
                                        <li class="mb-2"><i class="fas fa-gas-pump text-orange me-2"></i>PT. Petrosea TBK</li>
                                        <li class="mb-2"><i class="fas fa-gas-pump text-orange me-2"></i>PT. Thiess Contractor Indonesia</li>
                                        <li class="mb-2"><i class="fas fa-gas-pump text-orange me-2"></i>PT. AKR Corporindo</li>
                                        <li class="mb-2"><i class="fas fa-gas-pump text-orange me-2"></i>Chevron Indonesia</li>
                                        <li class="mb-2"><i class="fas fa-gas-pump text-orange me-2"></i>PT. Kideco Jaya Agung</li>
                                        <li class="mb-2"><i class="fas fa-gas-pump text-orange me-2"></i>PT. Mega Prima Persada</li>
                                        <li class="mb-2"><i class="fas fa-gas-pump text-orange me-2"></i>PT. Alam Raya Abadi</li>
                                        <li class="mb-2"><i class="fas fa-gas-pump text-orange me-2"></i>PT. Prima Sarana Gemilang</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <!-- Column 3 -->
                            <div class="col-md-4">
                                <div class="customer-group">
                                    <h5 class="mb-3 text-gradient">Industrial & Engineering</h5>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><i class="fas fa-industry text-orange me-2"></i>PT. Buana Andi Muda</li>
                                        <li class="mb-2"><i class="fas fa-industry text-orange me-2"></i>PT. Bukaka</li>
                                        <li class="mb-2"><i class="fas fa-industry text-orange me-2"></i>PT. Straight Consultant Services</li>
                                        <li class="mb-2"><i class="fas fa-industry text-orange me-2"></i>PT. Adani Global Group</li>
                                        <li class="mb-2"><i class="fas fa-industry text-orange me-2"></i>PT. Sugar Group</li>
                                        <li class="mb-2"><i class="fas fa-industry text-orange me-2"></i>PT. RAPP</li>
                                        <li class="mb-2"><i class="fas fa-industry text-orange me-2"></i>PT. Suryaciptateknik</li>
                                        <li class="mb-2"><i class="fas fa-industry text-orange me-2"></i>PT. Ancora Group</li>
                                        <li class="mb-2"><i class="fas fa-industry text-orange me-2"></i>PT. Trakindo Utama Group</li>
                                        <li class="mb-2"><i class="fas fa-industry text-orange me-2"></i>PT. Astra Group</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Customer Statistics with Gradient Cards -->
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="stat-card gradient-card text-center p-4">
                            <div class="display-6 fw-bold text-white">30+</div>
                            <div class="fw-semibold text-white-50">Major Clients</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="stat-card gradient-card text-center p-4">
                            <div class="display-6 fw-bold text-white">8</div>
                            <div class="fw-semibold text-white-50">Industries Served</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="stat-card gradient-card text-center p-4">
                            <div class="display-6 fw-bold text-white">15</div>
                            <div class="fw-semibold text-white-50">Years Experience</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="stat-card gradient-card text-center p-4">
                            <div class="display-6 fw-bold text-white">98%</div>
                            <div class="fw-semibold text-white-50">Client Retention</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Project Modal -->
<div class="modal fade" id="projectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 rounded-xl overflow-hidden">
            <div class="modal-header border-0 gradient-card">
                <h5 class="modal-title text-white">Project Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-lg-8">
                        <div class="modal-image-wrapper">
                            <img id="modalProjectImage" src="" alt="" class="img-fluid">
                            <div class="modal-nav">
                                <button class="modal-nav-btn gradient-card" id="projectPrevBtn">
                                    <i class="fas fa-chevron-left text-white"></i>
                                </button>
                                <button class="modal-nav-btn gradient-card" id="projectNextBtn">
                                    <i class="fas fa-chevron-right text-white"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="modal-info p-4 p-lg-5">
                            <div class="mb-4">
                                <span class="badge gradient-card text-white" id="modalProjectCategory">PERTASHOP Installation</span>
                                <div class="project-number-modal mt-2 text-gradient">#<span id="modalProjectNumber">01</span></div>
                                <h4 class="mt-2" id="modalProjectTitle">Project Title</h4>
                                <p class="text-muted" id="modalProjectDescription">Project description will appear here</p>
                            </div>
                            
                            <div class="project-details mb-4">
                                <h6 class="mb-3">Project Details</h6>
                                <ul class="list-unstyled">
                                    <li class="d-flex align-items-center mb-2">
                                        <i class="fas fa-map-marker-alt text-orange me-3"></i>
                                        <div>
                                            <small class="text-muted">Location</small>
                                            <p class="mb-0 fw-semibold" id="modalProjectLocation">Location</p>
                                        </div>
                                    </li>
                                    <li class="d-flex align-items-center mb-2">
                                        <i class="fas fa-calendar-alt text-orange me-3"></i>
                                        <div>
                                            <small class="text-muted">Status</small>
                                            <p class="mb-0 fw-semibold" id="modalProjectStatus">Completed</p>
                                        </div>
                                    </li>
                                    <li class="d-flex align-items-center mb-2">
                                        <i class="fas fa-user text-orange me-3"></i>
                                        <div>
                                            <small class="text-muted">Client</small>
                                            <p class="mb-0 fw-semibold" id="modalProjectClient">PT. Pertamina (Persero)</p>
                                        </div>
                                    </li>
                                    <li class="d-flex align-items-center">
                                        <i class="fas fa-industry text-orange me-3"></i>
                                        <div>
                                            <small class="text-muted">Project Type</small>
                                            <p class="mb-0 fw-semibold" id="modalProjectType">PERTASHOP Installation</p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            
                            <div class="project-description">
                                <h6 class="mb-3">Project Overview</h6>
                                <p id="modalProjectFullDescription">
                                    Complete installation of PERTASHOP retail outlet including civil works, mechanical installation of fuel dispensing systems, electrical works, and safety systems implementation.
                                </p>
                                <div class="mt-3">
                                    <h6 class="mb-2">Scope of Work:</h6>
                                    <ul class="list-unstyled" id="modalProjectScope">
                                        <li><i class="fas fa-check-circle text-success me-2"></i>Site Preparation & Civil Works</li>
                                        <li><i class="fas fa-check-circle text-success me-2"></i>Fuel Storage Tank Installation</li>
                                        <li><i class="fas fa-check-circle text-success me-2"></i>Dispensing System Setup</li>
                                        <li><i class="fas fa-check-circle text-success me-2"></i>Electrical & Control Systems</li>
                                        <li><i class="fas fa-check-circle text-success me-2"></i>Safety & Environmental Compliance</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="mt-4 pt-3 border-top">
                                <h6 class="mb-3">Share This Project</h6>
                                <div class="d-flex gap-2">
                                    <a href="#" class="btn btn-sm btn-outline-secondary">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-outline-secondary">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-outline-secondary">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-link"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Projects CSS - Updated -->
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

    /* Projects Hero with Background Image */
    .projects-hero-section {
        padding: 150px 0 100px;
        position: relative;
        color: white;
        overflow: hidden;
    }

    .projects-hero-section h1 {
        text-shadow: 2px 2px 15px rgba(0, 0, 0, 0.5);
        animation: fadeInDown 1s ease-out;
    }

    .projects-hero-section .lead {
        text-shadow: 1px 1px 10px rgba(0, 0, 0, 0.5);
        font-size: 1.3rem;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
        animation: fadeInUp 1s ease-out 0.3s both;
    }

    .projects-hero-section .projects-stats {
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

    /* Projects Filter - Updated */
    .filter-btn {
        background: white;
        border: 2px solid rgba(206, 115, 3, 0.2);
        color: var(--cdw-gray-medium);
        padding: 10px 24px;
        border-radius: 30px;
        font-weight: 600;
        transition: all 0.3s ease;
        white-space: nowrap;
        font-size: 0.95rem;
    }
    
    .filter-btn:hover,
    .filter-btn.active {
        background: var(--cdw-orange-gradient);
        color: white;
        border-color: transparent;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(206, 115, 3, 0.3);
    }
    
    /* Project Card */
    .project-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        height: 100%;
        transition: all 0.3s ease;
        border: 1px solid rgba(206, 115, 3, 0.1);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        position: relative;
    }
    
    .project-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(206, 115, 3, 0.15);
        border-color: rgba(206, 115, 3, 0.3);
    }
    
    .project-number {
        position: absolute;
        top: 15px;
        left: 15px;
        z-index: 2;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.2rem;
        color: white;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
    }
    
    .project-image {
        position: relative;
        overflow: hidden;
        aspect-ratio: 16/9;
    }
    
    .project-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .project-card:hover .project-image img {
        transform: scale(1.05);
    }
    
    .project-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .project-card:hover .project-overlay {
        opacity: 1;
    }
    
    .btn-view {
        width: 50px;
        height: 50px;
        background: white;
        border: none;
        border-radius: 50%;
        color: var(--cdw-orange-primary);
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .btn-view:hover {
        background: var(--cdw-orange-gradient);
        color: white;
        transform: scale(1.1);
    }
    
    .project-info {
        padding: 1.5rem;
    }
    
    .project-category {
        margin-bottom: 0.75rem;
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }
    
    .badge.gradient-card {
        color: white !important;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
    }
    
    .project-title {
        color: var(--cdw-gray-dark);
        font-weight: 600;
        margin-bottom: 0.5rem;
        line-height: 1.3;
    }
    
    .project-desc {
        color: var(--cdw-gray-medium);
        font-size: 0.9rem;
        margin-bottom: 1rem;
        line-height: 1.5;
    }
    
    .project-meta {
        color: var(--cdw-gray-medium);
        font-size: 0.85rem;
        margin-bottom: 1rem;
    }
    
    .project-client {
        padding-top: 0.75rem;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
    }
    
    /* Customers Section */
    .customer-group h5 {
        font-size: 1.1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--cdw-orange-primary);
    }
    
    .customer-group ul li {
        padding: 0.4rem 0;
        border-bottom: 1px dashed rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    .customer-group ul li:hover {
        padding-left: 10px;
        color: var(--cdw-orange-primary);
    }
    
    .customer-group ul li:last-child {
        border-bottom: none;
    }
    
    /* Project Modal */
    .modal-header.gradient-card {
        padding: 1.5rem;
        border: none;
    }
    
    .modal-image-wrapper {
        position: relative;
        height: 100%;
        min-height: 500px;
    }
    
    #modalProjectImage {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .modal-nav {
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        transform: translateY(-50%);
        display: flex;
        justify-content: space-between;
        padding: 0 1rem;
    }
    
    .modal-nav-btn {
        width: 50px;
        height: 50px;
        border: none;
        border-radius: 50%;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        color: white;
        cursor: pointer;
    }
    
    .modal-nav-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }
    
    .modal-info {
        background: white;
        height: 100%;
        overflow-y: auto;
    }
    
    .project-number-modal {
        font-size: 1.5rem;
        font-weight: 800;
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
    
    /* Animation for project items */
    .project-item {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.6s ease forwards;
    }
    
    .project-item:nth-child(1) { animation-delay: 0.1s; }
    .project-item:nth-child(2) { animation-delay: 0.15s; }
    .project-item:nth-child(3) { animation-delay: 0.2s; }
    .project-item:nth-child(4) { animation-delay: 0.25s; }
    .project-item:nth-child(5) { animation-delay: 0.3s; }
    .project-item:nth-child(6) { animation-delay: 0.35s; }
    .project-item:nth-child(7) { animation-delay: 0.4s; }
    .project-item:nth-child(8) { animation-delay: 0.45s; }
    .project-item:nth-child(9) { animation-delay: 0.5s; }
    .project-item:nth-child(10) { animation-delay: 0.55s; }
    .project-item:nth-child(11) { animation-delay: 0.6s; }
    .project-item:nth-child(12) { animation-delay: 0.65s; }
    .project-item:nth-child(13) { animation-delay: 0.7s; }
    .project-item:nth-child(14) { animation-delay: 0.75s; }
    .project-item:nth-child(15) { animation-delay: 0.8s; }
    
    /* Responsive */
    @media (max-width: 1199.98px) {
        .projects-hero-section {
            padding: 130px 0 80px;
        }
    }
    
    @media (max-width: 991.98px) {
        .projects-hero-section {
            padding: 120px 0 70px;
        }
        
        .projects-hero-section .lead {
            font-size: 1.2rem;
        }
        
        .modal-image-wrapper {
            min-height: 400px;
        }
        
        .customer-group {
            margin-bottom: 2rem;
        }
    }
    
    @media (max-width: 767.98px) {
        .projects-hero-section {
            padding: 100px 0 60px;
            background-attachment: scroll;
        }
        
        .projects-hero-section .lead {
            font-size: 1.1rem;
            padding: 0 15px;
        }
        
        .stat-card {
            padding: 1.5rem 1rem;
        }
        
        .filter-btn {
            padding: 8px 18px;
            font-size: 0.9rem;
        }
        
        .modal-image-wrapper {
            min-height: 300px;
        }
        
        .project-info {
            padding: 1.25rem;
        }
        
        .project-number {
            width: 35px;
            height: 35px;
            font-size: 1rem;
        }
    }
    
    @media (max-width: 575.98px) {
        .projects-hero-section {
            padding: 80px 0 50px;
        }
        
        .projects-filter {
            overflow-x: auto;
            padding-bottom: 10px;
        }
        
        .projects-filter .d-flex {
            flex-wrap: nowrap;
            width: max-content;
        }
        
        .modal-nav-btn {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
        
        .customer-group ul li {
            font-size: 0.9rem;
        }
    }
</style>

<!-- Projects JavaScript - Keep the same -->
<script>
// [JavaScript code tetap sama seperti sebelumnya]
document.addEventListener('DOMContentLoaded', function() {
    // Projects Data
    const projectsData = [
        {
            number: '01',
            image: 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            category: 'jawa-timur',
            title: 'Instalasi PERTASHOP di Tempurejo',
            description: 'Complete installation of PERTASHOP retail outlet with modern facilities',
            location: 'Tempurejo, Jawa Timur',
            status: 'Completed',
            client: 'PT. Pertamina (Persero)',
            type: 'PERTASHOP Installation',
            fullDescription: 'Complete turnkey project including civil works, mechanical installation of fuel dispensing systems, electrical works, and safety systems implementation. This project involved site preparation, foundation work, structural steel erection, installation of fuel storage tanks, dispensing units, and all associated piping systems.',
            scope: [
                'Site Preparation & Civil Works',
                'Fuel Storage Tank Installation',
                'Dispensing System Setup',
                'Electrical & Control Systems',
                'Safety & Environmental Compliance',
                'Convenience Store Setup',
                'Car Wash Facility'
            ]
        },
        {
            number: '02',
            image: 'https://images.unsplash.com/photo-1621905252507-b35492cc74b4?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            category: 'jawa-timur',
            title: 'Instalasi PERTASHOP di Puger',
            description: 'Modern retail outlet installation with integrated fuel management system',
            location: 'Puger, Jawa Timur',
            status: 'Completed',
            client: 'PT. Pertamina (Persero)',
            type: 'PERTASHOP Installation',
            fullDescription: 'Strategic location installation with integrated fuel management system for optimized operations. The project featured advanced monitoring systems for fuel inventory, automated reporting, and remote management capabilities.',
            scope: [
                'Integrated Fuel Management System',
                'Advanced Monitoring Technology',
                'Remote Management Setup',
                'Security Systems Installation',
                'Customer Service Area',
                'Parking Facility Development'
            ]
        },
        {
            number: '03',
            image: 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            category: 'jawa-timur',
            title: 'Instalasi PERTASHOP di Batu Marmar',
            description: 'Strategic location installation with enhanced customer facilities',
            location: 'Batu Marmar, Jawa Timur',
            status: 'Completed',
            client: 'PT. Pertamina (Persero)',
            type: 'PERTASHOP Installation',
            fullDescription: 'High-traffic location development with enhanced customer facilities including waiting lounge, prayer room, and children\'s play area. Designed for maximum customer convenience and satisfaction.',
            scope: [
                'Enhanced Customer Facilities',
                'Waiting Lounge Setup',
                'Prayer Room Construction',
                'Children\'s Play Area',
                'Landscaping Works',
                'Lighting & Signage'
            ]
        },
        {
            number: '04',
            image: 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            category: 'jawa-tengah',
            title: 'Instalasi PERTASHOP di Kemiri',
            description: 'Complete turnkey project including civil works and mechanical installation',
            location: 'Kemiri, Jawa Tengah',
            status: 'Completed',
            client: 'PT. Pertamina (Persero)',
            type: 'PERTASHOP Installation',
            fullDescription: 'Complete turnkey project from site preparation to operational handover. The project included all civil works, mechanical installations, electrical systems, and interior finishing for the retail outlet.',
            scope: [
                'Complete Turnkey Project',
                'Civil Engineering Works',
                'Mechanical Systems Installation',
                'Electrical Infrastructure',
                'Interior Finishing',
                'Operational Training'
            ]
        },
        {
            number: '05',
            image: 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            category: 'jawa-timur',
            title: 'Instalasi PERTASHOP di Klampis',
            description: 'Modern retail outlet with advanced fuel dispensing technology',
            location: 'Klampis, Jawa Timur',
            status: 'Completed',
            client: 'PT. Pertamina (Persero)',
            type: 'PERTASHOP Installation',
            fullDescription: 'Installation featuring latest generation fuel dispensing technology with automated payment systems and loyalty program integration. The outlet includes modern design elements and customer-friendly features.',
            scope: [
                'Advanced Fuel Dispensing Technology',
                'Automated Payment Systems',
                'Loyalty Program Integration',
                'Modern Architectural Design',
                'Energy Efficient Systems',
                'Digital Signage Installation'
            ]
        },
        {
            number: '06',
            image: 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            category: 'jawa-barat',
            title: 'Instalasi PERTASHOP di Cipeundeuy',
            description: 'Strategic highway location with high traffic volume',
            location: 'Cipeundeuy, Jawa Barat',
            status: 'Completed',
            client: 'PT. Pertamina (Persero)',
            type: 'PERTASHOP Installation',
            fullDescription: 'Highway location development designed for high-volume traffic with quick service turnaround. Features include multiple dispensing lanes, truck-friendly facilities, and 24/7 operations capability.',
            scope: [
                'Highway Location Development',
                'Multiple Dispensing Lanes',
                'Truck-Friendly Facilities',
                '24/7 Operations Setup',
                'Emergency Response Systems',
                'Traffic Management Solutions'
            ]
        },
        {
            number: '07',
            image: 'https://images.unsplash.com/photo-1581092160562-40aa08e78837?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            category: 'jawa-tengah',
            title: 'Instalasi PERTASHOP di Cilongok',
            description: 'Complete facility including convenience store and service area',
            location: 'Cilongok, Jawa Tengah',
            status: 'Completed',
            client: 'PT. Pertamina (Persero)',
            type: 'PERTASHOP Installation',
            fullDescription: 'Comprehensive facility development including fully stocked convenience store, service area for minor vehicle repairs, and customer amenities. Designed as a one-stop service center.',
            scope: [
                'Convenience Store Setup',
                'Vehicle Service Area',
                'Customer Amenities Installation',
                'Stock Management Systems',
                'Retail Display Setup',
                'Service Bay Construction'
            ]
        },
        {
            number: '08',
            image: 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            category: 'bali',
            title: 'Instalasi PERTASHOP di Kintamani',
            description: 'Tourist area installation with enhanced facilities',
            location: 'Kintamani, Bali',
            status: 'Completed',
            client: 'PT. Pertamina (Persero)',
            type: 'PERTASHOP Installation',
            fullDescription: 'Tourist destination installation featuring Balinese architectural elements, tourist information center, and enhanced rest facilities. Designed to serve both local community and tourists.',
            scope: [
                'Balinese Architectural Design',
                'Tourist Information Center',
                'Enhanced Rest Facilities',
                'Multi-language Signage',
                'Cultural Element Integration',
                'Tourist Amenities Setup'
            ]
        },
        {
            number: '09',
            image: 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            category: 'jawa-timur',
            title: 'Instalasi PERTASHOP di Kedung Adem',
            description: 'Rural area development project',
            location: 'Kedung Adem, Jawa Timur',
            status: 'Completed',
            client: 'PT. Pertamina (Persero)',
            type: 'PERTASHOP Installation',
            fullDescription: 'Rural development project bringing modern fuel services to underserved areas. The project included community engagement and local employment opportunities during construction.',
            scope: [
                'Rural Area Development',
                'Community Engagement Programs',
                'Local Employment Initiatives',
                'Basic Service Provision',
                'Community Facility Development',
                'Sustainable Design Implementation'
            ]
        },
        {
            number: '10',
            image: 'https://images.unsplash.com/photo-1621905252507-b35492cc74b4?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            category: 'jawa-timur',
            title: 'Instalasi PERTASHOP di Widang',
            description: 'High-volume commercial area installation',
            location: 'Kecamatan Widang, Jawa Timur',
            status: 'Completed',
            client: 'PT. Pertamina (Persero)',
            type: 'PERTASHOP Installation',
            fullDescription: 'Commercial area installation designed for high-volume business with multiple payment options, commercial vehicle facilities, and business customer services.',
            scope: [
                'Commercial Area Development',
                'Multiple Payment Systems',
                'Commercial Vehicle Facilities',
                'Business Customer Services',
                'Fleet Management Integration',
                'Bulk Purchase Facilities'
            ]
        },
        {
            number: '11',
            image: 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            category: 'jawa-timur',
            title: 'Instalasi PERTASHOP di Tempurejo',
            description: 'Second installation in strategic location',
            location: 'Tempurejo, Jawa Timur',
            status: 'Completed',
            client: 'PT. Pertamina (Persero)',
            type: 'PERTASHOP Installation',
            fullDescription: 'Follow-up installation in high-demand area, incorporating lessons learned from previous projects. Features improved layout and enhanced customer flow design.',
            scope: [
                'Follow-up Installation',
                'Improved Layout Design',
                'Enhanced Customer Flow',
                'Experience-Based Improvements',
                'Advanced Technology Integration',
                'Performance Optimization'
            ]
        },
        {
            number: '12',
            image: 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            category: 'bali',
            title: 'Instalasi PERTASHOP di Kubutambahan',
            description: 'Coastal area installation',
            location: 'Kubutambahan, Bali',
            status: 'Completed',
            client: 'PT. Pertamina (Persero)',
            type: 'PERTASHOP Installation',
            fullDescription: 'Coastal location installation with special considerations for salt air corrosion protection, weather-resistant materials, and marine environment compliance.',
            scope: [
                'Coastal Location Adaptation',
                'Corrosion Protection Systems',
                'Weather-resistant Materials',
                'Marine Environment Compliance',
                'Coastal Safety Measures',
                'Environmental Protection Systems'
            ]
        },
        {
            number: '13',
            image: 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            category: 'bali',
            title: 'Instalasi PERTASHOP di Sawan',
            description: 'Modern facility with local architectural elements',
            location: 'Sawan, Bali',
            status: 'Completed',
            client: 'PT. Pertamina (Persero)',
            type: 'PERTASHOP Installation',
            fullDescription: 'Modern facility incorporating traditional Balinese architectural elements, blending modern functionality with cultural aesthetics. Features local material usage and traditional design motifs.',
            scope: [
                'Traditional Balinese Architecture',
                'Local Material Utilization',
                'Cultural Design Integration',
                'Modern-Traditional Blend',
                'Community Cultural Respect',
                'Local Craftsmanship Support'
            ]
        },
        {
            number: '14',
            image: 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            category: 'bali',
            title: 'Instalasi PERTASHOP di Rendang',
            description: 'Mountainous area installation',
            location: 'Rendang, Bali',
            status: 'Completed',
            client: 'PT. Pertamina (Persero)',
            type: 'PERTASHOP Installation',
            fullDescription: 'Mountainous terrain installation requiring special engineering considerations for slope stability, drainage management, and access road development in challenging topography.',
            scope: [
                'Mountainous Terrain Engineering',
                'Slope Stability Solutions',
                'Drainage Management Systems',
                'Access Road Development',
                'Challenging Topography Adaptation',
                'Geotechnical Engineering Works'
            ]
        },
        {
            number: '15',
            image: 'https://images.unsplash.com/photo-1581092160562-40aa08e78837?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            category: 'bali',
            title: 'Instalasi PERTASHOP di Buleleng',
            description: 'Urban commercial installation',
            location: 'Kecamatan Buleleng, Bali',
            status: 'Completed',
            client: 'PT. Pertamina (Persero)',
            type: 'PERTASHOP Installation',
            fullDescription: 'Urban commercial district installation with space optimization, traffic flow management, and integration with existing urban infrastructure and services.',
            scope: [
                'Urban Space Optimization',
                'Traffic Flow Management',
                'Urban Infrastructure Integration',
                'Space-efficient Design',
                'Urban Compliance Solutions',
                'City Service Integration'
            ]
        }
    ];

    // Projects Filter
    const filterButtons = document.querySelectorAll('.filter-btn');
    const projectItems = document.querySelectorAll('.project-item');
    let currentFilter = 'all';
    let currentModalIndex = 0;
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Get filter value
            currentFilter = this.getAttribute('data-filter');
            
            // Filter items
            projectItems.forEach(item => {
                const category = item.getAttribute('data-category');
                
                if (currentFilter === 'all' || category === currentFilter) {
                    item.style.display = 'block';
                    // Add animation
                    item.style.animation = 'none';
                    setTimeout(() => {
                        item.style.animation = 'fadeInUp 0.6s ease forwards';
                    }, 10);
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
    
    // Project Modal
    const projectModal = new bootstrap.Modal(document.getElementById('projectModal'));
    const modalProjectImage = document.getElementById('modalProjectImage');
    const modalProjectNumber = document.getElementById('modalProjectNumber');
    const modalProjectTitle = document.getElementById('modalProjectTitle');
    const modalProjectDescription = document.getElementById('modalProjectDescription');
    const modalProjectLocation = document.getElementById('modalProjectLocation');
    const modalProjectStatus = document.getElementById('modalProjectStatus');
    const modalProjectClient = document.getElementById('modalProjectClient');
    const modalProjectType = document.getElementById('modalProjectType');
    const modalProjectFullDescription = document.getElementById('modalProjectFullDescription');
    const modalProjectScope = document.getElementById('modalProjectScope');
    
    const viewButtons = document.querySelectorAll('.btn-view');
    viewButtons.forEach((button, index) => {
        button.addEventListener('click', function() {
            const card = this.closest('.project-item');
            if (card) {
                const index = card.getAttribute('data-index');
                currentModalIndex = parseInt(index);
                updateProjectModal(currentModalIndex);
            }
        });
    });
    
    // Modal Navigation
    document.getElementById('projectPrevBtn').addEventListener('click', function() {
        currentModalIndex = (currentModalIndex - 1 + projectsData.length) % projectsData.length;
        updateProjectModal(currentModalIndex);
    });
    
    document.getElementById('projectNextBtn').addEventListener('click', function() {
        currentModalIndex = (currentModalIndex + 1) % projectsData.length;
        updateProjectModal(currentModalIndex);
    });
    
    function updateProjectModal(index) {
        const project = projectsData[index];
        
        modalProjectImage.src = project.image;
        modalProjectNumber.textContent = project.number;
        modalProjectTitle.textContent = project.title;
        modalProjectDescription.textContent = project.description;
        modalProjectLocation.textContent = project.location;
        modalProjectStatus.textContent = project.status;
        modalProjectClient.textContent = project.client;
        modalProjectType.textContent = project.type;
        modalProjectFullDescription.textContent = project.fullDescription;
        
        // Update category badge
        const categoryElement = document.getElementById('modalProjectCategory');
        if (categoryElement) {
            categoryElement.textContent = project.type;
        }
        
        // Update scope list
        if (modalProjectScope && project.scope) {
            modalProjectScope.innerHTML = '';
            project.scope.forEach(item => {
                const li = document.createElement('li');
                li.innerHTML = `<i class="fas fa-check-circle text-success me-2"></i>${item}`;
                modalProjectScope.appendChild(li);
            });
        }
    }
    
    // Keyboard navigation for modal
    document.addEventListener('keydown', function(e) {
        if (projectModal._isShown) {
            if (e.key === 'ArrowLeft') {
                currentModalIndex = (currentModalIndex - 1 + projectsData.length) % projectsData.length;
                updateProjectModal(currentModalIndex);
            } else if (e.key === 'ArrowRight') {
                currentModalIndex = (currentModalIndex + 1) % projectsData.length;
                updateProjectModal(currentModalIndex);
            } else if (e.key === 'Escape') {
                projectModal.hide();
            }
        }
    });
    
    // Initialize click on project images
    const projectImages = document.querySelectorAll('.project-image img');
    projectImages.forEach((img, index) => {
        img.addEventListener('click', function() {
            const card = this.closest('.project-item');
            const index = card.getAttribute('data-index');
            currentModalIndex = parseInt(index);
            updateProjectModal(currentModalIndex);
            projectModal.show();
        });
    });
});
</script>