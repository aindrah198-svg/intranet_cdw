<?php
// Data dari controller atau session
if (!isset($data)) {
    $data = [
        'title' => 'Dashboard Direktur',
        'subtitle' => 'Executive Dashboard Overview',
        'user' => [
            'name' => session()->get('name') ?: 'Direktur',
            'role' => session()->get('role') ?: 'direktur',
            'email' => session()->get('email') ?: 'direktur@cdw-engineering.com'
        ],
        'active' => 'dashboard'
    ];
}

// Kirim data ke template
$templateData = [
    'title' => $data['title'],
    'subtitle' => $data['subtitle'],
    'user' => $data['user'],
    'active' => $data['active']
];
?>

<?= view('direktur/templates/header', $templateData) ?>
<?= view('direktur/templates/sidebar', $templateData) ?>
<?= view('direktur/templates/navbar', $templateData) ?>

<!-- Content -->
<div class="container-fluid px-4 py-4">
    <!-- Welcome Card -->
    <div class="dashboard-card modern-card fade-in">
        <div class="d-flex align-items-center mb-4">
            <div class="bg-gradient-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                 style="width: 60px; height: 60px;">
                <i class="fas fa-user-tie fa-2x"></i>
            </div>
            <div>
                <h3 class="mb-1">Selamat Datang, <?= htmlspecialchars($data['user']['name']) ?>!</h3>
                <p class="text-muted mb-0">
                    Anda login sebagai <strong><?= ucfirst($data['user']['role']) ?></strong> 
                    di CDW Engineering Executive Dashboard
                </p>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="info-item d-flex align-items-center p-3 bg-light rounded mb-2">
                    <i class="fas fa-calendar-alt text-primary me-3 fa-lg"></i>
                    <div>
                        <small class="text-muted">Tanggal</small>
                        <p class="mb-0 fw-bold"><?= date('l, d F Y') ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-item d-flex align-items-center p-3 bg-light rounded mb-2">
                    <i class="fas fa-clock text-primary me-3 fa-lg"></i>
                    <div>
                        <small class="text-muted">Jam</small>
                        <p class="mb-0 fw-bold">
                            <span id="liveTime"><?= date('H:i:s') ?></span> WIB
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Selamat datang di dashboard direktur. Gunakan menu di samping untuk mengakses berbagai fitur.
            </div>
        </div>
        
        
    </div>
</div>

<script>
function updateLiveTime() {
    const now = new Date();
    const timeStr = now.toLocaleTimeString('id-ID', { 
        hour: '2-digit', 
        minute: '2-digit',
        second: '2-digit'
    });
    const timeElement = document.getElementById('liveTime');
    if (timeElement) {
        timeElement.textContent = timeStr;
    }
}
setInterval(updateLiveTime, 1000);
updateLiveTime();
</script>

<?= view('direktur/templates/footer', $templateData) ?>