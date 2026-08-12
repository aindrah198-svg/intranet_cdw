<?php
$data = ['title' => 'Dokumen Legal', 'subtitle' => 'Kelola Izin Usaha, Akta, & Perjanjian Legal Perusahaan', 'active' => 'dokumen-legal', 'user' => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']];
?>
<?= view('admin/templates/header', $data) ?>
<?= view('admin/templates/sidebar', $data) ?>
<?= view('admin/templates/navbar', $data) ?>

<div class="card border-0 shadow-sm rounded-3 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 style="color:#4a148c;font-weight:700;margin:0;"><i class="fas fa-gavel me-2"></i>Dokumen Legal Perusahaan</h5>
        <button class="btn btn-sm" style="background:#7b1fa2;color:white;"><i class="fas fa-plus me-1"></i>Upload Dokumen</button>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr><th>Nama Dokumen</th><th>Nomor Legalitas</th><th>Masa Berlaku</th><th>Status Expired</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td>NIB (Nomor Induk Berusaha)</td><td>9120001234567</td><td>Seumur Hidup</td>
                    <td><span class="badge bg-success">Aktif</span></td>
                    <td><button class="btn btn-sm btn-outline-purple"><i class="fas fa-download"></i></button></td>
                </tr>
                <tr>
                    <td>SIUP / Izin Operasional Teknik</td><td>503/128/IU-TEK/2021</td><td>2026-12-31</td>
                    <td><span class="badge bg-info">Masih Berlaku</span></td>
                    <td><button class="btn btn-sm btn-outline-purple"><i class="fas fa-download"></i></button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?= view('admin/templates/footer', $data) ?>
