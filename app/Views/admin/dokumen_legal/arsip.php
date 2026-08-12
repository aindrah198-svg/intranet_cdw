<?php
$data = ['title' => 'Arsip Dokumen', 'subtitle' => 'Arsip & Digitalisasi Berkas Perusahaan', 'active' => 'arsip-dokumen', 'user' => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']];
?>
<?= view('admin/templates/header', $data) ?>
<?= view('admin/templates/sidebar', $data) ?>
<?= view('admin/templates/navbar', $data) ?>

<div class="card border-0 shadow-sm rounded-3 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 style="color:#4a148c;font-weight:700;margin:0;"><i class="fas fa-archive me-2"></i>Arsip Dokumen Digital</h5>
        <button class="btn btn-sm" style="background:#7b1fa2;color:white;"><i class="fas fa-upload me-1"></i>Arsipkan Dokumen</button>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr><th>Kategori Arsip</th><th>Judul Berkas</th><th>Tahun</th><th>Diarsip Oleh</th><th>File</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td>Laporan Keuangan AUDIT</td><td>Laporan Audit KAP Tahun 2023</td><td>2023</td><td>Admin Legal</td>
                    <td><button class="btn btn-sm btn-outline-danger"><i class="fas fa-file-pdf"></i> PDF</button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?= view('admin/templates/footer', $data) ?>
