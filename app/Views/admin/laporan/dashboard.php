<?php
$data = ['title' => 'Dashboard Laporan Admin', 'subtitle' => 'Ringkasan Laporan Operasional Administrasi', 'active' => 'laporan-dashboard', 'user' => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']];
?>
<?= view('admin/templates/header', $data) ?>
<?= view('admin/templates/sidebar', $data) ?>
<?= view('admin/templates/navbar', $data) ?>

<div class="card border-0 shadow-sm rounded-3 p-4">
    <h5 style="color:#4a148c;font-weight:700;margin-bottom:20px;"><i class="fas fa-chart-bar me-2"></i>Ringkasan Laporan Administrasi Perusahaan</h5>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="p-3 border rounded-3 text-center bg-light">
                <i class="fas fa-file-invoice fa-2x text-purple mb-2"></i>
                <h6>Laporan Penggunaan ATK Bulanan</h6>
                <small class="text-muted">Total Pengeluaran: Rp 3.200.000</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 border rounded-3 text-center bg-light">
                <i class="fas fa-car fa-2x text-purple mb-2"></i>
                <h6>Laporan Penggunaan Kendaraan</h6>
                <small class="text-muted">Total Kunjungan: 24 Perjalanan</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 border rounded-3 text-center bg-light">
                <i class="fas fa-tasks fa-2x text-purple mb-2"></i>
                <h6>Laporan Kerja Harian Staf Admin</h6>
                <small class="text-muted">Status Penyelesaian: 95%</small>
            </div>
        </div>
    </div>
</div>

<?= view('admin/templates/footer', $data) ?>
