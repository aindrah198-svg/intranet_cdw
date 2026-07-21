<?php
$active = $active ?? 'dashboard';
$uri = service('uri');
$segments = $uri->getSegments();

// Ambil segment dengan cara yang aman
$segment1 = isset($segments[1]) ? $segments[1] : '';
$segment2 = isset($segments[2]) ? $segments[2] : '';

// Check active menu
$isDashboardActive = $active == 'dashboard' || $segment2 === 'dashboard';
$isClientActive = $segment2 === 'client';
$isPenawaranActive = $segment2 === 'penawaran';
$isInvoiceActive = $segment2 === 'invoice';
$isSuratJalanActive = $segment2 === 'surat-jalan' || $segment2 === 'suratjalan';
$isAbsensiActive = $segment2 === 'absensi';
$isProfileActive = $segment2 === 'profile';
?>

<!-- Sidebar -->
<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <h5 class="mb-0">
                <i class="fas fa-chart-line me-2"></i>
                SALES PANEL
            </h5>
        </div>
        
        <button class="sidebar-close-btn d-lg-none" onclick="toggleSidebar()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <div class="sidebar-user">
        <div class="sidebar-user-avatar">
            <?= strtoupper(substr($user['name'] ?? 'S', 0, 1)) ?>
        </div>
        <div class="sidebar-user-name"><?= htmlspecialchars($user['name'] ?? 'Sales') ?></div>
        <div class="sidebar-user-role">
            <span class="badge bg-primary"><?= ucfirst($user['role'] ?? 'sales') ?></span>
        </div>
    </div>
    
    <div class="sidebar-menu">
        <ul class="nav flex-column">
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link <?= $isDashboardActive ? 'active' : '' ?>" 
                   href="<?= base_url('sales/dashboard') ?>">
                    <i class="fas fa-tachometer-alt me-2"></i> 
                    <span class="menu-text">Dashboard</span>
                    <?php if($isDashboardActive): ?>
                        <span class="menu-active-indicator"></span>
                    <?php endif; ?>
                </a>
            </li>
            
            <!-- Sales Modules -->
            <li class="nav-item mt-3">
                <span class="nav-label text-uppercase text-muted small fw-bold px-3">
                    <i class="fas fa-briefcase me-1"></i> Sales Modules
                </span>
            </li>
            
            <!-- Clients -->
            <li class="nav-item">
                <a class="nav-link <?= $isClientActive ? 'active' : '' ?>" 
                   href="<?= base_url('sales/client') ?>">
                    <i class="fas fa-user-tie me-2"></i>
                    <span class="menu-text">Client Management</span>
                    <?php if($isClientActive): ?>
                        <span class="menu-active-indicator"></span>
                    <?php endif; ?>
                </a>
            </li>
            
            <!-- Penawaran Harga -->
            <li class="nav-item">
                <a class="nav-link <?= $isPenawaranActive ? 'active' : '' ?>" 
                   href="<?= base_url('sales/penawaran') ?>">
                    <i class="fas fa-file-invoice-dollar me-2"></i>
                    <span class="menu-text">Penawaran Harga</span>
                    <?php if($isPenawaranActive): ?>
                        <span class="menu-active-indicator"></span>
                    <?php endif; ?>
                </a>
            </li>
            
            <!-- Invoice -->
            <li class="nav-item">
                <a class="nav-link <?= $isInvoiceActive ? 'active' : '' ?>" 
                   href="<?= base_url('sales/invoice') ?>">
                    <i class="fas fa-file-invoice me-2"></i>
                    <span class="menu-text">Invoice Management</span>
                    <?php if($isInvoiceActive): ?>
                        <span class="menu-active-indicator"></span>
                    <?php endif; ?>
                </a>
            </li>

            <!-- Surat Jalan -->
            <li class="nav-item">
                <a class="nav-link <?= $isSuratJalanActive ? 'active' : '' ?>" 
                   href="<?= base_url('sales/surat-jalan') ?>">
                    <i class="fas fa-truck me-2"></i>
                    <span class="menu-text">Surat Jalan</span>
                    <?php if($isSuratJalanActive): ?>
                        <span class="menu-active-indicator"></span>
                    <?php endif; ?>
                </a>
            </li>
            
            <!-- Personal -->
            <li class="nav-item mt-3">
                <span class="nav-label text-uppercase text-muted small fw-bold px-3">
                    <i class="fas fa-user me-1"></i> Personal
                </span>
            </li>
            
            <!-- Attendance -->
            <li class="nav-item">
                <a class="nav-link <?= $isAbsensiActive ? 'active' : '' ?>" 
                   href="<?= base_url('sales/absensi') ?>">
                    <i class="fas fa-clock me-2"></i>
                    <span class="menu-text">Attendance</span>
                    <?php if($isAbsensiActive): ?>
                        <span class="menu-active-indicator"></span>
                    <?php endif; ?>
                </a>
            </li>
            
            <!-- Profile -->
            <li class="nav-item">
                <a class="nav-link <?= $isProfileActive ? 'active' : '' ?>" 
                   href="<?= base_url('sales/profile') ?>">
                    <i class="fas fa-user me-2"></i>
                    <span class="menu-text">Profile</span>
                    <?php if($isProfileActive): ?>
                        <span class="menu-active-indicator"></span>
                    <?php endif; ?>
                </a>
            </li>
            
            <!-- Separator -->
            <li class="nav-item mt-3">
                <hr class="sidebar-divider mx-3">
            </li>
            
            <!-- Logout -->
            <li class="nav-item">
                <a class="nav-link text-danger" href="<?= base_url('logout') ?>" onclick="return confirm('Are you sure you want to logout?')">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    <span class="menu-text">Logout</span>
                </a>
            </li>
        </ul>
    </div>
    
    <!-- Footer -->
    <div class="sidebar-footer text-center py-3">
        <small class="text-muted">v1.0.0</small>
    </div>
</nav>

<!-- Overlay for Mobile -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- Mobile Toggle Button -->
<button class="btn btn-primary sidebar-toggle d-lg-none" id="sidebarToggle" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</button>

<style>
/* Sidebar Styling */
.sidebar {
    width: 260px;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    background: linear-gradient(180deg, #1e3c72 0%, #2a5298 100%);
    color: white;
    z-index: 1050;
    display: flex;
    flex-direction: column;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
}

.sidebar-header {
    padding: 20px 15px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.sidebar-brand h5 {
    color: white;
    font-weight: 600;
}

.sidebar-close-btn {
    background: transparent;
    border: none;
    color: rgba(255, 255, 255, 0.7);
    font-size: 1.2rem;
    cursor: pointer;
    transition: color 0.3s ease;
}

.sidebar-close-btn:hover {
    color: white;
}

.sidebar-user {
    padding: 25px 20px;
    text-align: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-user-avatar {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: linear-gradient(45deg, #4a90e2, #63b3ed);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    font-weight: bold;
    margin: 0 auto 15px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

.sidebar-user-name {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 8px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.sidebar-user-role .badge {
    font-size: 11px;
    padding: 4px 10px;
    border-radius: 20px;
}

.sidebar-menu {
    flex: 1;
    padding: 20px 0;
    overflow-y: auto;
}

.nav-item {
    margin-bottom: 2px;
}

.nav-link {
    color: rgba(255, 255, 255, 0.8);
    padding: 14px 25px;
    border-left: 3px solid transparent;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    position: relative;
}

.nav-link:hover {
    color: white;
    background: rgba(255, 255, 255, 0.1);
    border-left-color: rgba(74, 144, 226, 0.5);
    padding-left: 28px;
}

.nav-link.active {
    color: white;
    background: rgba(255, 255, 255, 0.15);
    border-left-color: #ffd700;
    font-weight: 500;
}

.nav-link i {
    width: 20px;
    text-align: center;
    font-size: 16px;
}

.menu-text {
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.menu-active-indicator {
    width: 6px;
    height: 6px;
    background: #ffd700;
    border-radius: 50%;
    margin-left: 5px;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.nav-label {
    padding: 10px 0;
    display: block;
    font-size: 11px;
    letter-spacing: 1px;
    margin-top: 15px;
}

.sidebar-divider {
    border-color: rgba(255, 255, 255, 0.1);
    margin: 15px 0;
}

.sidebar-footer {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding: 15px;
}

/* Overlay for Mobile */
.sidebar-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1040;
    transition: opacity 0.3s ease;
}

/* Mobile Toggle Button */
.sidebar-toggle {
    position: fixed;
    top: 15px;
    left: 15px;
    z-index: 1030;
    width: 45px;
    height: 45px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

/* Responsive */
@media (max-width: 992px) {
    .sidebar {
        transform: translateX(-100%);
    }
    
    .sidebar.show {
        transform: translateX(0);
    }
    
    .sidebar-overlay.show {
        display: block;
    }
    
    .sidebar .sidebar-close-btn {
        display: block;
    }
}

@media (min-width: 993px) {
    .sidebar-toggle,
    .sidebar-overlay,
    .sidebar .sidebar-close-btn {
        display: none;
    }
    
    .main-content {
        margin-left: 260px;
    }
}

/* Scrollbar Styling */
.sidebar-menu::-webkit-scrollbar {
    width: 6px;
}

.sidebar-menu::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 3px;
}

.sidebar-menu::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 3px;
}

.sidebar-menu::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* Smooth transitions */
.sidebar * {
    transition: all 0.3s ease;
}
</style>

<script>
// Sidebar state management
let sidebarState = localStorage.getItem('sidebarState') || 'open';

// Initialize sidebar
function initSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    
    if (window.innerWidth >= 993) {
        // Desktop
        sidebar.classList.remove('show');
        document.getElementById('sidebarOverlay').classList.remove('show');
        
        if (sidebarState === 'collapsed') {
            collapseSidebar();
        } else {
            expandSidebar();
        }
    } else {
        // Mobile - default closed
        sidebar.classList.remove('show');
        document.getElementById('sidebarOverlay').classList.remove('show');
    }
}

// Toggle sidebar for mobile
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    sidebar.classList.toggle('show');
    overlay.classList.toggle('show');
    
    // Prevent body scrolling when sidebar is open on mobile
    if (window.innerWidth <= 992) {
        document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
    }
}

// Expand sidebar
function expandSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    
    sidebar.style.width = '260px';
    if (mainContent) {
        mainContent.style.marginLeft = '260px';
    }
    
    // Show all menu texts
    document.querySelectorAll('.menu-text').forEach(text => {
        text.style.opacity = '1';
        text.style.width = 'auto';
    });
    
    sidebarState = 'open';
    localStorage.setItem('sidebarState', 'open');
}

// Collapse sidebar (for future feature)
function collapseSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    
    sidebar.style.width = '80px';
    if (mainContent) {
        mainContent.style.marginLeft = '80px';
    }
    
    // Hide menu texts
    document.querySelectorAll('.menu-text').forEach(text => {
        text.style.opacity = '0';
        text.style.width = '0';
    });
    
    sidebarState = 'collapsed';
    localStorage.setItem('sidebarState', 'collapsed');
}

// Close sidebar when clicking outside on mobile
document.getElementById('sidebarOverlay').addEventListener('click', function() {
    if (window.innerWidth <= 992) {
        toggleSidebar();
    }
});

// Close sidebar when clicking a link on mobile
document.querySelectorAll('.sidebar .nav-link').forEach(link => {
    link.addEventListener('click', function() {
        if (window.innerWidth <= 992) {
            toggleSidebar();
        }
    });
});

// Handle window resize
window.addEventListener('resize', function() {
    initSidebar();
});

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
    initSidebar();
    
    // Add smooth transition to main content
    const mainContent = document.querySelector('.main-content');
    if (mainContent) {
        mainContent.style.transition = 'margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
    }
});

// Keyboard shortcut to toggle sidebar (Esc key)
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && window.innerWidth <= 992) {
        toggleSidebar();
    }
});
</script>