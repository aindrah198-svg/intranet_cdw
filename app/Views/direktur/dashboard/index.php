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

<!-- Main Dashboard Container -->
<div class="dashboard-wrapper fade-in">
    
    <!-- Executive Hero Banner -->
    <div class="card border-0 text-white shadow-sm" style="
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        border-radius: var(--border-radius);
        overflow: hidden;
        position: relative;
    ">
        <!-- Decorative Background Icon -->
        <div style="
            position: absolute; right: -20px; bottom: -30px; 
            font-size: 10rem; color: rgba(255,255,255,0.05); 
            pointer-events: none;
        ">
            <i class="fas fa-crown"></i>
        </div>

        <div class="card-body p-4 p-md-5">
            <div class="row align-items-center g-3">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                        <span class="badge bg-white text-primary px-3 py-2 rounded-pill fw-semibold shadow-sm" style="font-size: 0.8rem;">
                            <i class="fas fa-shield-alt me-1 text-success"></i> Executive Access
                        </span>
                        <span class="badge bg-white text-primary px-3 py-2 rounded-pill fw-semibold shadow-sm" style="font-size: 0.8rem;">
                            <i class="fas fa-building me-1 text-primary"></i> CIPTA DUTA WACANA ENGINEERING
                        </span>
                    </div>
                    <h2 class="fw-bold mb-2 text-white">Selamat Datang Kembali, <?= htmlspecialchars($data['user']['name']) ?></h2>
                    <p class="mb-0 text-white-50" style="font-size: 0.95rem;">
                        Ringkasan aktivitas dan kontrol eksekutif CDW Intranet Sistem.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <div class="d-inline-flex flex-column gap-1 bg-white bg-opacity-10 p-3 rounded-3 border border-white border-opacity-10 w-100 w-lg-auto text-start text-lg-end">
                        <div class="small text-white-50"><i class="far fa-calendar-alt me-1"></i> <?= date('l, d F Y') ?></div>
                        <div class="fs-4 fw-bold text-white"><i class="far fa-clock me-1 text-warning"></i> <span id="liveTime"><?= date('H:i:s') ?></span> <span class="small fs-6 fw-normal">WIB</span></div>
                    </div>
                </div>
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