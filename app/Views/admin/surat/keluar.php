<?php
$data = ['title' => 'Surat Keluar', 'subtitle' => 'Kelola Surat Keluar Perusahaan', 'active' => 'surat-keluar', 'user' => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']];
?>
<?= view('admin/templates/header', $data) ?>
<?= view('admin/templates/sidebar', $data) ?>
<?= view('admin/templates/navbar', $data) ?>

<div class="card border-0 shadow-sm rounded-3 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 style="color:#4a148c;font-weight:700;margin:0;"><i class="fas fa-paper-plane me-2"></i>Daftar Surat Keluar</h5>
        <button class="btn btn-sm" style="background:#7b1fa2;color:white;"><i class="fas fa-plus me-1"></i>Buat Surat Keluar</button>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr><th>No. Surat</th><th>Penerima</th><th>Perihal</th><th>Tanggal Kirim</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td>SK-2025-010</td><td>PT Shell Indonesia</td><td>Penawaran Kerjasama Pemeliharaan IT</td><td>2025-07-18</td>
                    <td><span class="badge bg-success">Terkirim</span></td>
                    <td><button class="btn btn-sm btn-outline-purple"><i class="fas fa-eye"></i></button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?= view('admin/templates/footer', $data) ?>
