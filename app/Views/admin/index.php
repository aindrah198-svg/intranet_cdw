<?php
// Jika $data tidak dikirim dari controller, buat data default dari session
if (!isset($data)) {
    $data = [
        'title' => 'Admin Dashboard - CDW Engineering',
        'subtitle' => 'Dashboard Overview',
        'user' => [
            'name' => session()->get('name') ?: 'Administrator',
            'role' => session()->get('role') ?: 'admin'
        ],
        'active' => 'dashboard'
    ];
}
?>

<?= view('admin/templates/header', $data) ?>
<?= view('admin/templates/sidebar', $data) ?>
<?= view('admin/templates/navbar', $data) ?>

<!-- Welcome Card -->
<div class="welcome-card">
    <h3>Selamat Datang, <?= $data['user']['name'] ?>!</h3>
    <p>Anda login sebagai <?= ucfirst($data['user']['role']) ?> di CDW Engineering Admin Panel</p>
</div>

<!-- Stats Cards -->
<div class="stats-row">
    <div class="dashboard-card">
        <div class="card-icon blue">
            <i class="fas fa-project-diagram"></i>
        </div>
        <div class="card-value">24</div>
        <div class="card-label">Total Projects</div>
        <small class="text-success"><i class="fas fa-arrow-up me-1"></i> 12% from last month</small>
    </div>
    
    <div class="dashboard-card">
        <div class="card-icon green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="card-value">18</div>
        <div class="card-label">Completed</div>
        <small class="text-success"><i class="fas fa-arrow-up me-1"></i> 8% from last month</small>
    </div>
    
    <div class="dashboard-card">
        <div class="card-icon orange">
            <i class="fas fa-spinner"></i>
        </div>
        <div class="card-value">5</div>
        <div class="card-label">In Progress</div>
        <small class="text-warning"><i class="fas fa-minus me-1"></i> No change</small>
    </div>
    
    <div class="dashboard-card">
        <div class="card-icon purple">
            <i class="fas fa-users"></i>
        </div>
        <div class="card-value">156</div>
        <div class="card-label">Total Clients</div>
        <small class="text-success"><i class="fas fa-arrow-up me-1"></i> 23% from last month</small>
    </div>
</div>

<!-- Recent Projects Table -->
<div class="dashboard-card">
    <h5 class="mb-4"><i class="fas fa-list me-2"></i>Recent Projects</h5>
    <div class="data-table">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Project ID</th>
                    <th>Project Name</th>
                    <th>Client</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#PRJ-001</td>
                    <td>SPBU Construction - Jakarta</td>
                    <td>Pertamina</td>
                    <td><span class="badge bg-success">Completed</span></td>
                    <td>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: 100%"></div>
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary">View</button>
                    </td>
                </tr>
                <tr>
                    <td>#PRJ-002</td>
                    <td>Fuel Management System</td>
                    <td>Shell Indonesia</td>
                    <td><span class="badge bg-warning">In Progress</span></td>
                    <td>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-warning" style="width: 75%"></div>
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary">View</button>
                    </td>
                </tr>
                <tr>
                    <td>#PRJ-003</td>
                    <td>Electrical Installation</td>
                    <td>BP Indonesia</td>
                    <td><span class="badge bg-primary">Planning</span></td>
                    <td>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: 30%"></div>
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary">View</button>
                    </td>
                </tr>
                <tr>
                    <td>#PRJ-004</td>
                    <td>Manufacturing Equipment</td>
                    <td>Jayopetro</td>
                    <td><span class="badge bg-success">Completed</span></td>
                    <td>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: 100%"></div>
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary">View</button>
                    </td>
                </tr>
                <tr>
                    <td>#PRJ-005</td>
                    <td>IT Infrastructure</td>
                    <td>Tominaga Mfg.</td>
                    <td><span class="badge bg-warning">In Progress</span></td>
                    <td>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-warning" style="width: 60%"></div>
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary">View</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="dashboard-card">
            <h5><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
            <div class="row mt-3">
                <div class="col-6 mb-3">
                    <a href="<?= base_url('admin/karyawan/create') ?>" class="btn btn-outline-primary w-100">
                        <i class="fas fa-plus me-2"></i>Add Employee
                    </a>
                </div>
                <div class="col-6 mb-3">
                    <button class="btn btn-outline-success w-100">
                        <i class="fas fa-file-export me-2"></i>Export Report
                    </button>
                </div>
                <div class="col-6 mb-3">
                    <a href="<?= base_url('admin/register') ?>" class="btn btn-outline-info w-100">
                        <i class="fas fa-user-plus me-2"></i>Add User
                    </a>
                </div>
                <div class="col-6 mb-3">
                    <a href="<?= base_url('admin/settings') ?>" class="btn btn-outline-warning w-100">
                        <i class="fas fa-cog me-2"></i>Settings
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="dashboard-card">
            <h5><i class="fas fa-chart-line me-2"></i>System Status</h5>
            <div class="mt-3">
                <div class="d-flex justify-content-between mb-2">
                    <span>Server Load</span>
                    <span class="text-success">45% <i class="fas fa-arrow-up"></i></span>
                </div>
                <div class="progress mb-3" style="height: 8px;">
                    <div class="progress-bar bg-success" style="width: 45%"></div>
                </div>
                
                <div class="d-flex justify-content-between mb-2">
                    <span>Database Usage</span>
                    <span class="text-warning">68% <i class="fas fa-minus"></i></span>
                </div>
                <div class="progress mb-3" style="height: 8px;">
                    <div class="progress-bar bg-warning" style="width: 68%"></div>
                </div>
                
                <div class="d-flex justify-content-between mb-2">
                    <span>Active Users</span>
                    <span class="text-info">12 <i class="fas fa-user"></i></span>
                </div>
                <div class="progress mb-3" style="height: 8px;">
                    <div class="progress-bar bg-info" style="width: 60%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="dashboard-card mt-4">
    <h5 class="mb-4"><i class="fas fa-history me-2"></i>Recent Activities</h5>
    <div class="list-group list-group-flush">
        <div class="list-group-item border-0 px-0">
            <div class="d-flex w-100 justify-content-between">
                <small class="text-success"><i class="fas fa-user-plus me-2"></i>New employee added</small>
                <small class="text-muted">2 hours ago</small>
            </div>
            <p class="mb-1">Budi Santoso joined as Project Manager</p>
        </div>
        <div class="list-group-item border-0 px-0">
            <div class="d-flex w-100 justify-content-between">
                <small class="text-primary"><i class="fas fa-file-signature me-2"></i>Contract signed</small>
                <small class="text-muted">5 hours ago</small>
            </div>
            <p class="mb-1">New contract with Pertamina for SPBU project</p>
        </div>
        <div class="list-group-item border-0 px-0">
            <div class="d-flex w-100 justify-content-between">
                <small class="text-warning"><i class="fas fa-exclamation-triangle me-2"></i>System update</small>
                <small class="text-muted">1 day ago</small>
            </div>
            <p class="mb-1">Employee management system updated to v2.0</p>
        </div>
        <div class="list-group-item border-0 px-0">
            <div class="d-flex w-100 justify-content-between">
                <small class="text-info"><i class="fas fa-chart-line me-2"></i>Report generated</small>
                <small class="text-muted">2 days ago</small>
            </div>
            <p class="mb-1">Monthly performance report for Q1 2024</p>
        </div>
    </div>
</div>

<?= view('admin/templates/footer') ?>