<?php
$data = [
    'title'  => 'Tambah Laporan Kerja Harian',
    'active' => 'laporan-kerja',
    'user'   => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
];

echo view('admin/templates/header', $data);
echo view('admin/templates/sidebar', $data);
echo view('admin/templates/navbar', $data);
?>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>" class="text-decoration-none text-muted">Laporan & Keluhan</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/laporan/kerja-harian') ?>" class="text-decoration-none text-muted">Laporan Kerja Harian</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Buat Laporan</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-plus-circle text-primary me-2"></i> Buat / Edit Pratinjau Laporan Kerja Harian</h4>
            <small class="text-muted">Isi aktivitas dan rincian pekerjaan harian Anda untuk dilaporkan ke Direktur.</small>
        </div>
        <div>
            <a href="<?= base_url('admin/laporan/kerja-harian') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Batal / Kembali
            </a>
        </div>
    </div>

    <?php if(!empty($fromTaskId)): ?>
    <!-- Banner Info Konversi Penugasan -->
    <div class="alert alert-info border-0 rounded-4 shadow-sm p-3 mb-4 d-flex align-items-center gap-3">
        <div class="bg-info bg-opacity-20 text-info rounded-circle p-2.5 d-flex align-items-center justify-content-center">
            <i class="fas fa-magic fs-4"></i>
        </div>
        <div>
            <h6 class="fw-bold text-dark mb-1">Dikonversi Otomatis dari Tugas Direktur</h6>
            <small class="text-muted text-xs">Sistem telah menyiapkan rincian tugas dan item checklist yang telah selesai. Anda dapat memeriksa, menambah catatan tambahan, atau mengedit teks sebelum dikirim ke Direktur.</small>
        </div>
    </div>
    <?php endif; ?>

    <!-- Form Card -->
    <div class="card border-0 rounded-4 shadow-sm overflow-hidden mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom border-light">
            <h5 class="card-title fs-6 fw-bold mb-0 text-dark"><i class="fas fa-edit text-primary me-2"></i> Form Pratinjau & Edit Laporan Kerja Harian</h5>
        </div>
        <div class="card-body p-4">
            <form action="<?= base_url('admin/laporan/kerja-harian/simpan') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="from_task_id" value="<?= esc($fromTaskId) ?>">
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark text-sm">Tanggal Pelaporan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control rounded-3" value="<?= $todayDate ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark text-sm">Judul Laporan / Aktivitas <span class="text-danger">*</span></label>
                        <input type="text" name="judul" class="form-control rounded-3" value="<?= esc($prefilledJudul) ?>" placeholder="Contoh: Pemrosesan Surat Masuk & Inventaris Stok ATK" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark text-sm">Deskripsi & Rincian Pekerjaan (Dapat Diedit / Ditambah Catatan) <span class="text-danger">*</span></label>
                        <textarea name="deskripsi" class="form-control rounded-3 font-monospace" rows="8" placeholder="Tuliskan rincian lengkap tugas dan pekerjaan yang telah diselesaikan hari ini..." required><?= esc($prefilledDeskripsi) ?></textarea>
                        <small class="text-muted text-xs">Anda dapat menambahkan keterangan atau catatan pekerjaan tambahan pada kotak di atas sebelum mengirim ke Direktur.</small>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= base_url('admin/laporan/kerja-harian') ?>" class="btn btn-secondary rounded-pill px-4 font-semibold">Batal</a>
                    <button type="submit" class="btn btn-success rounded-pill px-4 font-semibold shadow-sm">
                        <i class="fas fa-paper-plane me-1.5"></i> Periksa & Kirim Laporan Ke Direktur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= view('admin/templates/footer', $data) ?>
