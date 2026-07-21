<nav class="navbar navbar-expand-lg fixed-top navbar-custom">
    <div class="container">
        <!-- Brand Logo -->
        <a class="navbar-brand" href="<?= base_url() ?>">
            <div class="d-flex align-items-center">
                <div class="logo-container me-3">
                    <img src="<?= base_url('assets/img/logo/logo_cdw.jpg') ?>" 
                         alt="CDW Engineering Logo" 
                         class="cdw-logo"
                         onerror="handleLogoError(this)">
                </div>
                <div class="brand-text d-none d-sm-flex">
                    <div class="brand-cdw">CDW</div>
                    <div class="brand-engineering">ENGINEERING</div>
                </div>
            </div>
        </a>

        <!-- Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                data-bs-target="#navbarMain" aria-controls="navbarMain" 
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-custom">
                <i class="fas fa-bars"></i>
            </span>
        </button>

        <!-- Navbar Content -->
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav ms-lg-auto me-lg-0 mx-auto">
                <li class="nav-item">
                    <a class="nav-link <?= (isset($active) && $active == 'home') ? 'active' : '' ?>" 
                       href="<?= base_url() ?>">
                        <i class="fas fa-home me-1"></i>Home
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?= (isset($active) && $active == 'about') ? 'active' : '' ?>" 
                       href="<?= base_url('about') ?>">
                        <i class="fas fa-info-circle me-1"></i>About
                    </a>
                </li>
                
       <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle <?= (isset($active) && $active == 'services') ? 'active' : '' ?>" 
       href="#" id="servicesDropdown" role="button" 
       data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-cogs me-1"></i>Services
    </a>
    <ul class="dropdown-menu" aria-labelledby="servicesDropdown">
        <li><a class="dropdown-item <?= (isset($section) && $section == 'engineering') ? 'active' : '' ?>" href="<?= base_url('services#engineering') ?>">
            <i class="fas fa-cog me-2 text-orange"></i>Engineering</a></li>
        <li><hr class="dropdown-divider my-1"></li>
        <li><a class="dropdown-item <?= (isset($section) && $section == 'construction') ? 'active' : '' ?>" href="<?= base_url('services#construction') ?>">
            <i class="fas fa-building me-2 text-orange"></i>Construction</a></li>
        <li><hr class="dropdown-divider my-1"></li>
        <li><a class="dropdown-item <?= (isset($section) && $section == 'mechanical') ? 'active' : '' ?>" href="<?= base_url('services#mechanical') ?>">
            <i class="fas fa-bolt me-2 text-orange"></i>Mechanical & Electrical</a></li>
        <li><hr class="dropdown-divider my-1"></li>
        <li><a class="dropdown-item <?= (isset($section) && $section == 'it') ? 'active' : '' ?>" href="<?= base_url('services#it') ?>">
            <i class="fas fa-server me-2 text-orange"></i>IT & Integration</a></li>
    </ul>
</li>
                
                <li class="nav-item">
                    <a class="nav-link <?= (isset($active) && $active == 'projects') ? 'active' : '' ?>" 
                       href="<?= base_url('projects') ?>">
                        <i class="fas fa-project-diagram me-1"></i>Projects
                    </a>
                </li>
                
             <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle <?= (isset($active) && $active == 'products') ? 'active' : '' ?>" 
       href="#" id="productDropdown" role="button" 
       data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-box me-1"></i>Product
    </a>
    <ul class="dropdown-menu" aria-labelledby="productDropdown">
        <li><a class="dropdown-item <?= (isset($section) && $section == 'petroleum') ? 'active' : '' ?>" href="<?= base_url('products#petroleum') ?>">
            <i class="fas fa-gas-pump me-2 text-orange"></i>Petroleum Equipment</a></li>
        <li><hr class="dropdown-divider my-1"></li>
        <li><a class="dropdown-item <?= (isset($section) && $section == 'manufacturing') ? 'active' : '' ?>" href="<?= base_url('products#manufacturing') ?>">
            <i class="fas fa-industry me-2 text-orange"></i>Manufacturing</a></li>
        <li><hr class="dropdown-divider my-1"></li>
        <li><a class="dropdown-item <?= (isset($section) && $section == 'electrical') ? 'active' : '' ?>" href="<?= base_url('products#electrical') ?>">
            <i class="fas fa-plug me-2 text-orange"></i>Electrical Components</a></li>
        <li><hr class="dropdown-divider my-1"></li>
        <li><a class="dropdown-item <?= (isset($section) && $section == 'it-products') ? 'active' : '' ?>" href="<?= base_url('products#it-products') ?>">
            <i class="fas fa-laptop-code me-2 text-orange"></i>IT & System Integration</a></li>
    </ul>
</li>
                
                <li class="nav-item">
                    <a class="nav-link <?= (isset($active) && $active == 'gallery') ? 'active' : '' ?>" 
                       href="<?= base_url('gallery') ?>">
                        <i class="fas fa-images me-1"></i>Gallery
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?= (isset($active) && $active == 'contact') ? 'active' : '' ?>" 
                       href="<?= base_url('contact') ?>">
                        <i class="fas fa-envelope me-1"></i>Contact
                    </a>
                </li>
            </ul>
            
            <div class="d-flex align-items-center mt-3 mt-lg-0 ms-lg-3">
                <a href="<?= base_url('login') ?>" class="btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </a>
            </div>
        </div>
    </div>
</nav>

<div style="height: 80px;"></div>

<style>
/* ===== NAVBAR BASE STYLES ===== */
.navbar-custom {
    background: linear-gradient(135deg, var(--cdw-white) 0%, #fff8f0 100%);
    border-bottom: 3px solid var(--cdw-orange-primary);
    box-shadow: 0 2px 20px rgba(255, 107, 0, 0.1);
    padding: 0.8rem 0;
    transition: all 0.3s ease;
}

.navbar-custom.scrolled {
    padding: 0.5rem 0;
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(10px);
}

/* ===== LOGO & BRAND ===== */
.logo-container {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.cdw-logo {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 8px;
}

.brand-text {
    display: flex;
    flex-direction: column;
    line-height: 1;
    margin-left: 10px;
}

.brand-cdw {
    color: var(--cdw-gray-dark);
    font-weight: 800;
    font-size: 1.4rem;
    line-height: 1;
}

.brand-engineering {
    color: var(--cdw-orange-primary);
    font-weight: 700;
    font-size: 1.2rem;
    background: var(--cdw-orange-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1;
    margin-top: 2px;
}

/* ===== TOGGLER BUTTON ===== */
.navbar-toggler {
    border: none !important;
    padding: 8px 10px !important;
    background: transparent !important;
    box-shadow: none !important;
    outline: none !important;
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.navbar-toggler-custom {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
}

.navbar-toggler-custom i {
    color: var(--cdw-orange-primary);
    font-size: 1.8rem;
}

/* Hilangkan default Bootstrap icon */
.navbar-toggler-icon {
    display: none !important;
    background-image: none !important;
}

/* ===== NAV LINKS ===== */
.navbar-nav .nav-link {
    font-weight: 600;
    color: var(--cdw-gray-dark) !important;
    padding: 10px 15px !important;
    margin: 0 2px;
    border-radius: 8px;
    transition: all 0.3s ease;
    position: relative;
    display: flex;
    align-items: center;
}

.navbar-nav .nav-link i {
    margin-right: 8px;
    color: var(--cdw-orange-primary);
    font-size: 0.9em;
}

.navbar-nav .nav-link:hover {
    color: var(--cdw-orange-primary) !important;
    background-color: rgba(255, 107, 0, 0.08);
    transform: translateY(-2px);
}

.navbar-nav .nav-link.active {
    color: var(--cdw-orange-primary) !important;
    background-color: rgba(255, 107, 0, 0.12);
    font-weight: 700;
}

.navbar-nav .nav-link.active::after {
    content: '';
    position: absolute;
    bottom: -3px;
    left: 20%;
    right: 20%;
    height: 3px;
    background: var(--cdw-orange-gradient);
    border-radius: 3px;
}

/* ===== DROPDOWN MENU ===== */
.dropdown-menu {
    border: 1px solid rgba(255, 107, 0, 0.1) !important;
    box-shadow: 0 10px 30px rgba(255, 107, 0, 0.15) !important;
    border-radius: 10px !important;
    padding: 10px 0 !important;
    margin-top: 5px !important;
}

.dropdown-item {
    padding: 10px 20px !important;
    color: var(--cdw-gray-dark) !important;
    font-weight: 500 !important;
    transition: all 0.2s ease !important;
    border-radius: 6px !important;
    margin: 2px 8px !important;
    display: flex;
    align-items: center;
}

.dropdown-item:hover {
    background: linear-gradient(135deg, rgba(255, 107, 0, 0.1), rgba(255, 138, 0, 0.1)) !important;
    color: var(--cdw-orange-primary) !important;
}

.dropdown-item i {
    color: var(--cdw-orange-primary) !important;
    margin-right: 10px;
    width: 18px;
}

/* ===== LOGIN BUTTON ===== */
.btn-login {
    background: var(--cdw-orange-gradient) !important;
    border: none !important;
    border-radius: 8px !important;
    padding: 10px 20px !important;
    color: white !important;
    font-weight: 600 !important;
    font-size: 0.9rem !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 4px 12px rgba(255, 107, 0, 0.3) !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    text-decoration: none !important;
    min-width: 90px;
}

.btn-login:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(255, 107, 0, 0.4) !important;
    color: white !important;
}

/* ===== RESPONSIVE STYLES ===== */

/* Desktop */
@media (min-width: 992px) {
    .navbar-nav {
        gap: 5px;
    }
    
    /* Hover dropdown untuk desktop */
    .dropdown:hover .dropdown-menu {
        display: block;
        margin-top: 0;
    }
}

/* Mobile & Tablet */
@media (max-width: 991.98px) {
    .navbar-custom {
        padding: 0.5rem 0;
    }
    
    .logo-container {
        width: 45px;
        height: 45px;
    }
    
    .navbar-toggler {
        width: 40px;
        height: 40px;
    }
    
    .navbar-toggler-custom i {
        font-size: 1.5rem;
    }
    
    /* Mobile Menu */
    .navbar-collapse {
        background: white !important;
        border-radius: 15px !important;
        padding: 1rem !important;
        margin-top: 0.5rem !important;
        box-shadow: 0 10px 30px rgba(255, 107, 0, 0.15) !important;
        border: 1px solid rgba(255, 107, 0, 0.1) !important;
        max-height: 80vh;
        overflow-y: auto;
        position: absolute;
        top: 100%;
        left: 15px;
        right: 15px;
        z-index: 1050;
    }
    
    .navbar-nav {
        width: 100%;
        text-align: left;
    }
    
    .nav-item {
        margin: 5px 0;
        width: 100%;
    }
    
    .navbar-nav .nav-link {
        padding: 12px 15px !important;
        font-size: 1rem !important;
        border-radius: 8px !important;
        margin: 0 !important;
        width: 100%;
        justify-content: flex-start;
    }
    
    .navbar-nav .nav-link.active::after {
        display: none;
    }
    
    .navbar-nav .nav-link.active {
        background-color: rgba(255, 107, 0, 0.15) !important;
        border-left: 4px solid var(--cdw-orange-primary);
    }
    
    /* Dropdown di mobile */
    .dropdown-menu {
        background-color: rgba(249, 249, 249, 0.98) !important;
        border: 1px solid rgba(255, 107, 0, 0.15) !important;
        margin: 5px 0 5px 15px !important;
        padding: 8px 0 !important;
        width: calc(100% - 15px) !important;
        position: static !important;
        float: none !important;
        box-shadow: 0 5px 15px rgba(255, 107, 0, 0.1) !important;
    }
    
    .dropdown-item {
        padding: 10px 20px !important;
        font-size: 0.95rem !important;
    }
    
    /* Login button di mobile */
    .btn-login {
        width: 100% !important;
        margin: 10px 0 5px !important;
        padding: 12px 20px !important;
        font-size: 1rem !important;
        justify-content: center;
    }
    
    /* Brand text di mobile kecil */
    .brand-text.d-none.d-sm-flex {
        display: none !important;
    }
}

/* Mobile Kecil */
@media (max-width: 576px) {
    .logo-container {
        width: 40px;
        height: 40px;
    }
    
    .navbar-collapse {
        left: 10px;
        right: 10px;
        padding: 0.75rem !important;
    }
    
    .navbar-nav .nav-link {
        padding: 10px 12px !important;
        font-size: 0.95rem !important;
    }
    
    .navbar-toggler {
        width: 36px;
        height: 36px;
    }
    
    .navbar-toggler-custom i {
        font-size: 1.3rem;
    }
}

/* Logo Fallback */
.logo-fallback {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--cdw-orange-gradient);
    color: white;
    font-weight: 800;
    font-size: 0.9rem;
    border-radius: 8px;
}
</style>

<script>
// Logo error handler
function handleLogoError(img) {
    const container = img.parentElement;
    container.innerHTML = '<div class="logo-fallback"><span>CDW</span></div>';
}

// Simple initialization
document.addEventListener('DOMContentLoaded', function() {
    console.log('Navbar initialized');
    
    // Scroll effect
    const navbar = document.querySelector('.navbar-custom');
    
    function updateNavbar() {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    }
    
    updateNavbar();
    window.addEventListener('scroll', updateNavbar);
    
    // Preload logo
    const logoImg = document.querySelector('.cdw-logo');
    if (logoImg) {
        const tempImage = new Image();
        tempImage.src = logoImg.src;
        tempImage.onerror = function() {
            handleLogoError(logoImg);
        };
    }
    
    // Fix untuk Bootstrap dropdown di mobile
    if (window.innerWidth < 992) {
        // Initialize Bootstrap dropdowns
        const dropdowns = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
        dropdowns.forEach(function(dropdownToggle) {
            new bootstrap.Dropdown(dropdownToggle);
        });
    }
    
    // Auto-close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        const navbarCollapse = document.getElementById('navbarMain');
        const navbarToggler = document.querySelector('.navbar-toggler');
        
        if (navbarCollapse && navbarCollapse.classList.contains('show')) {
            const isClickInsideNavbar = event.target.closest('.navbar-custom') !== null;
            const isClickOnToggler = event.target.closest('.navbar-toggler') !== null;
            
            // Jika klik di luar navbar dan bukan pada toggler, tutup navbar
            if (!isClickInsideNavbar && !isClickOnToggler) {
                const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                if (bsCollapse) {
                    bsCollapse.hide();
                }
            }
        }
    });
    
    // Close menu when clicking on nav links (for mobile)
    document.querySelectorAll('.nav-link:not(.dropdown-toggle), .dropdown-item').forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 992) {
                const navbarCollapse = document.getElementById('navbarMain');
                if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                    const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                    if (bsCollapse) {
                        bsCollapse.hide();
                    }
                }
            }
        });
    });
});

// Handle window resize
window.addEventListener('resize', function() {
    // Re-initialize dropdowns jika diperlukan
    if (window.innerWidth >= 992) {
        // Reset untuk desktop
    } else {
        // Reset untuk mobile
    }
});
// Tambahkan script ini di bagian bawah setelah script yang sudah ada
document.addEventListener('DOMContentLoaded', function() {
    // Handle active state untuk dropdown items berdasarkan hash di URL
    function setActiveDropdownItem() {
        const hash = window.location.hash;
        if (hash) {
            // Hapus class active dari semua dropdown items
            document.querySelectorAll('.dropdown-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Cari dropdown item yang href-nya mengandung hash yang sama
            const activeItem = document.querySelector(`.dropdown-item[href$="${hash}"]`);
            if (activeItem) {
                activeItem.classList.add('active');
                
                // Dapatkan parent dropdown dan set active state
                const parentDropdown = activeItem.closest('.dropdown-menu');
                if (parentDropdown) {
                    const dropdownToggle = parentDropdown.previousElementSibling;
                    if (dropdownToggle && dropdownToggle.classList.contains('dropdown-toggle')) {
                        dropdownToggle.classList.add('active');
                    }
                }
            }
        }
    }
    
    // Panggil saat halaman dimuat
    setActiveDropdownItem();
    
    // Panggil saat hash berubah
    window.addEventListener('hashchange', setActiveDropdownItem);
    
    // Untuk link yang menggunakan anchor, pastikan tidak mengganggu state
    document.querySelectorAll('.dropdown-item[href*="#"]').forEach(link => {
        link.addEventListener('click', function(e) {
            // Biarkan default behavior untuk anchor links
            setTimeout(setActiveDropdownItem, 100); // Setelah navigasi
        });
    });
});
</script>