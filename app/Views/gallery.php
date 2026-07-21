<!-- app/Views/gallery.php -->

<!-- Gallery Header with Background Image -->
<div class="gallery-header" style="background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1504917595217-d4dc5ebe6122?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover fixed; color: white; padding: 120px 0; margin-bottom: 40px; position: relative;">
    <div class="container text-center">
        <h1 class="display-4 mb-3 animate__animated animate__fadeInDown">Our Project Gallery</h1>
        <p class="lead mb-0 animate__animated animate__fadeInUp">Explore our portfolio of successful engineering projects</p>
    </div>
</div>

<!-- Main Content -->
<div class="container mb-5">
    <!-- Filter Buttons with New Colors -->
    <div class="text-center mb-4">
        <button class="filter-btn active" data-filter="all">All Projects</button>
        <button class="filter-btn" data-filter="construction">Construction</button>
        <button class="filter-btn" data-filter="engineering">Engineering</button>
        <button class="filter-btn" data-filter="mechanical">Mechanical</button>
        <button class="filter-btn" data-filter="electrical">Electrical</button>
        <button class="filter-btn" data-filter="petroleum">Petroleum</button>
    </div>
    
    <!-- Gallery Grid -->
    <div class="row" id="galleryGrid">
        <?php foreach ($projects as $index => $project): ?>
        <div class="col-lg-4 col-md-6 mb-4 gallery-item" data-category="<?= $project['category'] ?>" data-aos="fade-up" data-aos-delay="<?= $index * 50 ?>">
            <div class="card gallery-card gradient-card h-100">
                <div class="position-relative overflow-hidden" style="height: 220px;">
                    <img src="<?= $project['image'] ?>" class="card-img-top" alt="<?= $project['title'] ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s;">
                    <div class="category-badge">
                        <span class="badge bg-white text-dark px-3 py-2 rounded-pill shadow-sm">
                            <i class="fas fa-tag me-1"></i><?= $project['category_display'] ?>
                        </span>
                    </div>
                </div>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold mb-3"><?= $project['title'] ?></h5>
                    <p class="card-text text-white-50 mb-3"><?= $project['description'] ?></p>
                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <small class="text-white-50">
                            <i class="fas fa-map-marker-alt me-1"></i> <?= $project['location'] ?>
                        </small>
                        <small class="text-white-50">
                            <i class="fas fa-calendar-alt me-1"></i> <?= $project['date'] ?>
                        </small>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pb-3">
                    <button class="btn btn-light w-100 view-detail-btn" data-id="<?= $project['id'] ?>">
                        <i class="fas fa-eye me-2"></i>View Details
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Load More Button -->
    <div class="text-center mt-5">
        <button class="load-more-btn" id="loadMoreBtn" onclick="loadMoreProjects()">
            <i class="fas fa-plus-circle me-2"></i>Load More Projects
        </button>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header gradient-card">
                <h5 class="modal-title text-white" id="modalTitle">Project Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="modalBody">
                <!-- Content will be filled by JavaScript -->
            </div>
        </div>
    </div>
</div>

<!-- CSS -->
<style>
/* Gallery Header with Background Image */
.gallery-header {
    background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1504917595217-d4dc5ebe6122?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover fixed;
    color: white;
    padding: 120px 0;
    margin-bottom: 40px;
    position: relative;
}

/* Filter Buttons with Orange Gradient */
.filter-btn {
    margin: 5px;
    padding: 10px 24px;
    border: none;
    background: transparent;
    color: #ce7303;
    border: 2px solid #ce7303;
    border-radius: 30px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.9rem;
}

.filter-btn:hover,
.filter-btn.active {
    background: linear-gradient(135deg, #ce7303 0%, #ccad01 100%);
    color: white;
    border-color: transparent;
    box-shadow: 0 5px 15px rgba(206, 115, 3, 0.4);
    transform: translateY(-2px);
}

/* Gradient Cards */
.gradient-card {
    background: linear-gradient(135deg, #ce7303 0%, #ccad01 100%) !important;
    border: none !important;
    box-shadow: 0 10px 30px rgba(255, 140, 0, 0.3) !important;
    transition: all 0.3s ease !important;
    border-radius: 15px !important;
    overflow: hidden;
}

.gradient-card:hover {
    background: linear-gradient(135deg, #FF9F1C 0%, #b69801 100%) !important;
    box-shadow: 0 15px 40px rgba(255, 140, 0, 0.5) !important;
    transform: translateY(-10px) scale(1.02);
}

.gradient-card:hover img {
    transform: scale(1.1);
}

.gradient-card .card-title {
    color: white;
    font-weight: 700;
    margin-bottom: 1rem;
}

.gradient-card .card-text {
    color: rgba(255, 255, 255, 0.9);
}

.gradient-card .text-white-50 {
    color: rgba(255, 255, 255, 0.8) !important;
}

.gradient-card .btn-light {
    background: white;
    border: none;
    color: #ce7303;
    font-weight: 600;
    padding: 10px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.gradient-card .btn-light:hover {
    background: rgba(255, 255, 255, 0.9);
    color: #FF9F1C;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.category-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    z-index: 2;
}

/* Load More Button */
.load-more-btn {
    background: linear-gradient(135deg, #ce7303 0%, #ccad01 100%);
    color: white;
    border: none;
    padding: 15px 40px;
    border-radius: 40px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 5px 20px rgba(206, 115, 3, 0.4);
}

.load-more-btn:hover {
    background: linear-gradient(135deg, #FF9F1C 0%, #b69801 100%);
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 10px 30px rgba(255, 140, 0, 0.6);
}

.load-more-btn:disabled {
    background: #ccc;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* Modal Styles */
.modal-content {
    border: none;
    border-radius: 20px;
    overflow: hidden;
}

.modal-header.gradient-card {
    padding: 1.5rem;
    margin: 0;
}

/* Animation for gallery items */
.gallery-item {
    opacity: 0;
    animation: fadeInUp 0.6s ease forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .gallery-header {
        padding: 80px 0;
    }
    
    .filter-btn {
        padding: 8px 16px;
        font-size: 0.8rem;
    }
    
    .load-more-btn {
        padding: 12px 30px;
        font-size: 1rem;
    }
}

@media (max-width: 576px) {
    .gallery-header {
        padding: 60px 0;
    }
    
    .filter-btn {
        padding: 6px 12px;
        font-size: 0.75rem;
        margin: 3px;
    }
}
</style>

<!-- JavaScript -->
<script>
let currentPage = 1;
let currentFilter = 'all';
let isLoading = false;

// Filter buttons
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        currentFilter = this.getAttribute('data-filter');
        currentPage = 1;
        
        filterItems(currentFilter);
    });
});

function filterItems(filter) {
    const items = document.querySelectorAll('.gallery-item');
    
    items.forEach(item => {
        if (filter === 'all' || item.dataset.category === filter) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
    
    // Reset load more button
    document.getElementById('loadMoreBtn').style.display = 'block';
    document.getElementById('loadMoreBtn').disabled = false;
    document.getElementById('loadMoreBtn').innerHTML = '<i class="fas fa-plus-circle me-2"></i>Load More Projects';
}

// View detail buttons
document.querySelectorAll('.view-detail-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        showProjectDetail(id);
    });
});

function showProjectDetail(id) {
    // Fetch project detail from server
    fetch(`/gallery/getProjectDetail/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const project = data.data;
                document.getElementById('modalBody').innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <img src="${project.image}" class="img-fluid rounded" alt="${project.title}">
                        </div>
                        <div class="col-md-6">
                            <h4 class="mb-3">${project.title}</h4>
                            <p class="text-muted mb-3">${project.description}</p>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-map-marker-alt text-warning me-2"></i> <strong>Location:</strong> ${project.location}</li>
                                <li class="mb-2"><i class="fas fa-calendar-alt text-warning me-2"></i> <strong>Date:</strong> ${project.date}</li>
                                <li class="mb-2"><i class="fas fa-tag text-warning me-2"></i> <strong>Category:</strong> ${project.category_display}</li>
                            </ul>
                        </div>
                    </div>
                `;
                new bootstrap.Modal(document.getElementById('detailModal')).show();
            }
        });
}

// Load more projects via AJAX
function loadMoreProjects() {
    if (isLoading) return;
    
    isLoading = true;
    const btn = document.getElementById('loadMoreBtn');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Loading...';
    btn.disabled = true;
    
    currentPage++;
    
    // Fetch data dari server
    fetch(`/gallery/getMoreProjects?page=${currentPage}&category=${currentFilter}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                // Add new projects to grid
                data.data.forEach(project => {
                    const html = `
                        <div class="col-lg-4 col-md-6 mb-4 gallery-item" data-category="${project.category}" data-aos="fade-up">
                            <div class="card gallery-card gradient-card h-100">
                                <div class="position-relative overflow-hidden" style="height: 220px;">
                                    <img src="${project.image}" class="card-img-top" alt="${project.title}" style="width: 100%; height: 100%; object-fit: cover;">
                                    <div class="category-badge">
                                        <span class="badge bg-white text-dark px-3 py-2 rounded-pill shadow-sm">
                                            <i class="fas fa-tag me-1"></i>${project.category_display}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title fw-bold mb-3 text-white">${project.title}</h5>
                                    <p class="card-text text-white-50 mb-3">${project.description}</p>
                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <small class="text-white-50">
                                            <i class="fas fa-map-marker-alt me-1"></i> ${project.location}
                                        </small>
                                        <small class="text-white-50">
                                            <i class="fas fa-calendar-alt me-1"></i> ${project.date}
                                        </small>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent border-0 pb-3">
                                    <button class="btn btn-light w-100 view-detail-btn" data-id="${project.id}">
                                        <i class="fas fa-eye me-2"></i>View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    document.getElementById('galleryGrid').insertAdjacentHTML('beforeend', html);
                });
                
                // Add event listeners to new view detail buttons
                document.querySelectorAll('.view-detail-btn').forEach(btn => {
                    btn.removeEventListener('click', showProjectDetail);
                    btn.addEventListener('click', function() {
                        const id = this.dataset.id;
                        showProjectDetail(id);
                    });
                });
                
                // Re-apply filter
                filterItems(currentFilter);
                
                btn.innerHTML = '<i class="fas fa-plus-circle me-2"></i>Load More Projects';
                btn.disabled = false;
                isLoading = false;
                
                // Hide button if no more projects
                if (!data.hasMore) {
                    btn.style.display = 'none';
                }
            } else {
                btn.innerHTML = '<i class="fas fa-check-circle me-2"></i>No More Projects';
                btn.disabled = true;
                isLoading = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btn.innerHTML = '<i class="fas fa-plus-circle me-2"></i>Load More Projects';
            btn.disabled = false;
            isLoading = false;
        });
}

// Initial filter
filterItems('all');

// Add AOS animation if available
if (typeof AOS !== 'undefined') {
    AOS.init({
        duration: 800,
        once: true
    });
}
</script>