<?php
$data = ['title' => 'Laporan Kerja Harian Saya', 'subtitle' => 'Input Laporan Pekerjaan Harian', 'active' => 'laporan-harian-saya', 'user' => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']];
?>
<?= view('admin/templates/header', $data) ?>
<?= view('admin/templates/sidebar', $data) ?>
<?= view('admin/templates/navbar', $data) ?>

<div class="card border-0 shadow-sm rounded-3 p-4 mb-4">
    <h5 style="color:#4a148c;font-weight:700;" class="mb-3"><i class="fas fa-edit me-2"></i>Form Laporan Kerja Harian</h5>
    <form action="<?= base_url('admin/laporan-harian-saya/store') ?>" method="post">
        <div class="mb-3">
            <label class="form-label">Rincian Pekerjaan Hari Ini</label>
            <textarea name="rincian" class="form-control" rows="3" placeholder="Tuliskan aktivitas dan pencapaian Anda hari ini..." required></textarea>
        </div>
        <button type="submit" class="btn text-white" style="background:#7b1fa2;"><i class="fas fa-paper-plane me-1"></i>Kirim Laporan</button>
    </form>
</div>

<?= view('admin/templates/footer', $data) ?>
