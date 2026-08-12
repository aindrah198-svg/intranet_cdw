<?php
$title = $title ?? 'Review Pengajuan ATK';
$templateData = [
    'title' => $title,
    'user'  => session()->get('user') ?? ['name' => 'Direktur', 'role' => 'direktur'],
    'active' => 'pengadaan'
];
?>

<?= view('direktur/templates/header', $templateData) ?>
<?= view('direktur/templates/sidebar', $templateData) ?>
<?= view('direktur/templates/navbar', $templateData) ?>

<style>
    /* Prevent Any Horizontal Page Overflow */
    html, body {
        max-width: 100% !important;
        overflow-x: hidden !important;
    }

    .main-content {
        max-width: 100% !important;
        overflow-x: hidden !important;
    }

    .container-fluid {
        max-width: 100% !important;
        padding-left: 12px !important;
        padding-right: 12px !important;
        box-sizing: border-box !important;
    }

    .laporan-card-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(30, 60, 114, 0.06) !important;
    }
</style>

<div class="container-fluid py-3 py-md-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light flex-wrap gap-3">
        <div class="d-flex align-items-center">
            <a href="<?= base_url('direktur/pengadaan/pengajuan-atk') ?>" class="btn btn-outline-secondary rounded-pill me-3 px-3 py-1.5 text-xs fw-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali
            </a>
            <div>
                <h4 class="mb-0 fw-bold text-dark fs-5 fs-md-4">Review & Approval Pengajuan ATK</h4>
                <small class="text-muted d-none d-sm-inline">Tinjau rincian kebutuhan Alat Tulis Kantor dan berikan keputusan Direktur.</small>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card laporan-card-modern p-4">
                <div class="border-bottom pb-3 mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center">
                        <i class="fas fa-clipboard-check text-primary me-2"></i> Detail Pengajuan ATK #<?= esc($p['id']) ?>
                    </h5>
                    <?php
                        $st = strtolower($p['status'] ?? 'menunggu');
                        $badge = 'bg-warning text-dark';
                        if ($st === 'disetujui') $badge = 'bg-success text-white';
                        if ($st === 'ditolak') $badge = 'bg-danger text-white';
                    ?>
                    <span class="badge <?= $badge ?> px-3 py-1.5 rounded-pill text-xs fw-semibold">
                        Status saat ini: <?= ucfirst(esc($p['status'])) ?>
                    </span>
                </div>

                <!-- Detail Barang & Pemohon -->
                <div class="bg-light p-3.5 rounded-3 border mb-4">
                    <div class="row g-3 text-sm">
                        <div class="col-12 col-sm-6">
                            <small class="text-muted text-xs uppercase d-block fw-semibold">Nama Pemohon</small>
                            <strong class="text-dark fs-6"><?= esc($p['nama_lengkap'] ?? 'Direktur') ?></strong>
                            <small class="text-muted d-block"><?= esc($p['jabatan'] ?? '-') ?> (<?= esc($p['divisi'] ?? '-') ?>)</small>
                        </div>
                        <div class="col-12 col-sm-6">
                            <small class="text-muted text-xs uppercase d-block fw-semibold">Barang ATK yang Diajukan</small>
                            <strong class="text-primary fs-6"><?= esc($p['nama_barang']) ?></strong>
                            <small class="text-dark d-block fw-semibold">Jumlah: <?= esc($p['jumlah']) ?> <?= esc($p['satuan']) ?></small>
                        </div>
                        <div class="col-12 border-top pt-2 mt-2">
                            <small class="text-muted text-xs uppercase d-block fw-semibold">Alasan Kebutuhan / Urgensi</small>
                            <p class="mb-0 text-dark"><?= nl2br(esc($p['alasan'] ?: '- Tidak dicantumkan -')) ?></p>
                        </div>
                        <div class="col-12 text-xs text-muted pt-1">
                            <i class="far fa-clock me-1"></i> Tanggal Pengajuan: <?= date('d F Y H:i', strtotime($p['created_at'] ?? date('Y-m-d H:i:s'))) ?>
                        </div>
                    </div>
                </div>

                <!-- Form Keputusan Direktur -->
                <form action="<?= base_url('direktur/pengadaan/pengajuan-atk/approve') ?>" method="POST">
                    <input type="hidden" name="id" value="<?= esc($p['id']) ?>">

                    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-edit text-primary me-2"></i> Keputusan Direktur</h6>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Pilih Keputusan Status *</label>
                        <select name="status" class="form-select rounded-3 p-2.5">
                            <option value="disetujui" <?= strtolower($p['status']) === 'disetujui' ? 'selected' : '' ?>> Setujui Pengajuan ATK</option>
                            <option value="ditolak" <?= strtolower($p['status']) === 'ditolak' ? 'selected' : '' ?>> Tolak Pengajuan ATK</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-xs text-dark">Catatan / Komentar Direktur</label>
                        <textarea name="komentar" class="form-control rounded-3 p-3" rows="4" placeholder="Masukkan catatan atau instruksi tambahan untuk staf/HRD..."><?= esc($p['komentar_direktur'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="<?= base_url('direktur/pengadaan/pengajuan-atk') ?>" class="btn btn-outline-secondary rounded-pill px-4 py-2 text-sm">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 text-sm fw-semibold shadow-sm">
                            <i class="fas fa-save me-1.5"></i> Simpan Keputusan Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= view('direktur/templates/footer', $templateData) ?>
