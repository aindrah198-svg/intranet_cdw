<?php
$title = 'Dashboard Sales';
$active = 'dashboard';
$user = $user ?? ['name' => 'Sales', 'role' => 'sales'];
?>

<?= $this->include('sales/templates/header') ?>
<?= $this->include('sales/templates/sidebar') ?>
<?= $this->include('sales/templates/navbar') ?>

<div class="container-fluid py-4">
    <!-- Welcome Card -->
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-5">
                    <!-- Header -->
                    <div class="row align-items-center mb-5">
                        <div class="col-md-8">
                            <h1 class="display-5 fw-bold text-primary mb-3">
                                <i class="fas fa-chart-line me-2"></i>
                                Selamat Datang, <?= htmlspecialchars($user['name'] ?? 'Sales') ?>!
                            </h1>
                            <p class="lead text-muted mb-2">
                                Anda login sebagai <span class="badge bg-primary fs-6">SALES</span>
                            </p>
                            <div class="d-flex flex-wrap gap-3 mt-3">
                                <span class="text-muted">
                                    <i class="fas fa-user me-1"></i>
                                    Username: <?= htmlspecialchars($user['username'] ?? 'sales') ?>
                                </span>
                                <span class="text-muted">
                                    <i class="fas fa-envelope me-1"></i>
                                    Email: <?= htmlspecialchars($user['email'] ?? 'sales@example.com') ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="avatar-circle bg-primary text-white display-4 mb-3 mx-auto">
                                <?= strtoupper(substr($user['name'] ?? 'S', 0, 1)) ?>
                            </div>
                            <h5 class="mb-1"><?= htmlspecialchars($user['name'] ?? 'Sales') ?></h5>
                            <p class="text-muted small">Sales Department</p>
                        </div>
                    </div>
                    
                    <!-- Quick Stats -->
                    <div class="row mb-5">
                        <div class="col-md-3 mb-4">
                            <div class="card border-start border-primary border-4 h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-uppercase text-muted mb-2">Total Clients</h6>
                                            <h2 class="mb-0">0</h2>
                                            <p class="text-muted mb-0">Your clients</p>
                                        </div>
                                        <div class="icon-circle bg-primary text-white">
                                            <i class="fas fa-users fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-4">
                            <div class="card border-start border-success border-4 h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-uppercase text-muted mb-2">Active Clients</h6>
                                            <h2 class="mb-0">0</h2>
                                            <p class="text-muted mb-0">Active status</p>
                                        </div>
                                        <div class="icon-circle bg-success text-white">
                                            <i class="fas fa-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-4">
                            <div class="card border-start border-info border-4 h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-uppercase text-muted mb-2">Pending Deals</h6>
                                            <h2 class="mb-0">0</h2>
                                            <p class="text-muted mb-0">In progress</p>
                                        </div>
                                        <div class="icon-circle bg-info text-white">
                                            <i class="fas fa-clock fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-4">
                            <div class="card border-start border-warning border-4 h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-uppercase text-muted mb-2">Today's Date</h6>
                                            <h2 class="mb-0"><?= date('d') ?></h2>
                                            <p class="text-muted mb-0"><?= date('F Y') ?></p>
                                        </div>
                                        <div class="icon-circle bg-warning text-white">
                                            <i class="fas fa-calendar-alt fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="row mb-5">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <i class="fas fa-bolt me-2"></i>
                                        Quick Actions
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <a href="<?= base_url('sales/client') ?>" class="btn btn-primary btn-lg w-100 py-3">
                                                <i class="fas fa-user-tie me-2"></i> Manage Clients
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="<?= base_url('sales/client/create') ?>" class="btn btn-success btn-lg w-100 py-3">
                                                <i class="fas fa-plus-circle me-2"></i> Add New Client
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="<?= base_url('sales/absensi') ?>" class="btn btn-info btn-lg w-100 py-3">
                                                <i class="fas fa-clock me-2"></i> Attendance
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="<?= base_url('sales/profile') ?>" class="btn btn-warning btn-lg w-100 py-3">
                                                <i class="fas fa-user me-2"></i> My Profile
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- System Info -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        System Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p><strong>Current Time:</strong> <?= date('H:i:s') ?></p>
                                            <p><strong>Login Role:</strong> <span class="badge bg-primary"><?= htmlspecialchars($user['role'] ?? 'sales') ?></span></p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Session ID:</strong> <?= session_id() ?></p>
                                            <p><strong>Karyawan ID:</strong> <?= htmlspecialchars($user['karyawan_id'] ?? 'N/A') ?></p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>Application:</strong> CDW Sales System</p>
                                            <p><strong>Version:</strong> 1.0.0</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer Note -->
            <div class="text-center mt-4">
                <p class="text-muted">
                    <i class="fas fa-chart-line me-1"></i>
                    CDW Engineering - Simple Sales Dashboard
                    <span class="mx-2">•</span>
                    Ready for Client Management
                </p>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom styles untuk Simple Dashboard */
.card {
    border-radius: 15px;
    overflow: hidden;
    transition: transform 0.3s;
}

.card:hover {
    transform: translateY(-5px);
}

.avatar-circle {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.icon-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-lg {
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-lg:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

/* Responsive */
@media (max-width: 768px) {
    .display-5 {
        font-size: 1.8rem;
    }
    
    .avatar-circle {
        width: 70px;
        height: 70px;
        font-size: 1.5rem;
    }
    
    .btn-lg {
        padding: 0.75rem !important;
        font-size: 0.9rem;
    }
}
</style>

<script>
// Live Clock Update
function updateLiveTime() {
    const now = new Date();
    const timeStr = now.toLocaleTimeString('id-ID', { 
        hour: '2-digit', 
        minute: '2-digit',
        second: '2-digit'
    });
    
    // Update all elements with class 'live-time'
    document.querySelectorAll('.live-time').forEach(el => {
        el.textContent = timeStr;
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateLiveTime();
    
    // Update every second
    setInterval(updateLiveTime, 1000);
});
</script>

<?= $this->include('sales/templates/footer') ?>