<?php
$title = $title ?? 'Detail Laporan Kerusakan Alat';
$templateData = [
    'title'  => $title,
    'user'   => session()->get('user') ?? ['name' => session()->get('name') ?? 'Administrator', 'role' => 'admin'],
    'active' => 'inventaris'
];
?>

<?= view('admin/templates/header', $templateData) ?>
<?= view('admin/templates/sidebar', $templateData) ?>
<?= view('admin/templates/navbar', $templateData) ?>

<div class="container-fluid py-3 py-md-4">
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light flex-wrap gap-3">
        <div class="d-flex align-items-center">
            <a href="<?= base_url('admin/inventaris/kerusakan') ?>" class="btn btn-outline-secondary rounded-pill me-3 px-3 py-1.5 text-xs fw-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali
            </a>
            <div>
                <h4 class="mb-0 fw-bold text-dark fs-5">Detail Laporan Kerusakan #<?= esc($k['kode_laporan']) ?></h4>
                <small class="text-muted">Tracking status dan informasi lengkap alat yang dilaporkan rusak.</small>
            </div>
        </div>
        <div>
            <a href="<?= base_url('admin/inventaris/kerusakan/edit/' . $k['id']) ?>" class="btn btn-primary rounded-pill shadow-sm">
                <i class="fas fa-edit me-1.5"></i> Edit Laporan
            </a>
        </div>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <div class="border-bottom pb-3 mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-tools text-primary me-2"></i> <?= esc($k['nama_alat']) ?></h5>
                    <?php
                        $st = strtolower($k['status_tindakan'] ?? 'dilaporkan');
                        $badgeClass = 'bg-secondary text-white';
                        if ($st === 'selesai' || $st === 'diperbaiki') $badgeClass = 'bg-success text-white';
                        elseif ($st === 'dalam_perbaikan' || $st === 'proses') $badgeClass = 'bg-warning text-dark';
                    ?>
                    <span class="badge <?= $badgeClass ?> px-3 py-1.5 rounded-pill text-xs">
                        <?= ucfirst(str_replace('_', ' ', esc($k['status_tindakan']))) ?>
                    </span>
                </div>

                <div class="bg-light p-3.5 rounded-3 border mb-4">
                    <div class="row g-3 text-sm">
                        <div class="col-12 col-sm-6">
                            <small class="text-muted text-xs uppercase d-block fw-semibold">Pelapor Kerusakan</small>
                            <strong class="text-dark fs-6"><?= esc($k['pelapor'] ?? 'Karyawan / Staf') ?></strong>
                            <small class="text-muted d-block"><?= esc($k['pelapor_jabatan'] ?? '-') ?> (<?= esc($k['pelapor_divisi'] ?? '-') ?>)</small>
                        </div>
                        <div class="col-12 col-sm-6">
                            <small class="text-muted text-xs uppercase d-block fw-semibold">Lokasi Alat</small>
                            <strong class="text-dark fs-6"><i class="fas fa-map-marker-alt text-danger me-1"></i><?= esc($k['lokasi_alat'] ?: '-') ?></strong>
                            <small class="text-muted d-block">Tingkat Kerusakan: <strong><?= ucfirst(esc($k['tingkat_kerusakan'])) ?></strong></small>
                        </div>
                        <div class="col-12 border-top pt-2 mt-2">
                            <small class="text-muted text-xs uppercase d-block fw-semibold">Deskripsi Kendala & Kerusakan</small>
                            <p class="mb-0 text-dark"><?= nl2br(esc($k['deskripsi_kerusakan'] ?: '-')) ?></p>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-user-gear text-primary me-2"></i> Penanganan & Perbaikan</h6>
                <div class="row g-3 text-sm mb-3">
                    <div class="col-12 col-sm-4">
                        <small class="text-muted text-xs uppercase d-block">Teknisi Pengurus</small>
                        <span class="fw-bold text-dark"><?= esc($k['teknisi_pengurus'] ?: '-') ?></span>
                    </div>
                    <div class="col-12 col-sm-4">
                        <small class="text-muted text-xs uppercase d-block">Lokasi Perbaikan</small>
                        <span class="fw-bold text-dark"><?= esc($k['lokasi_perbaikan'] ?: '-') ?></span>
                    </div>
                    <div class="col-12 col-sm-4">
                        <small class="text-muted text-xs uppercase d-block">Petugas Pembawa</small>
                        <span class="fw-bold text-dark"><?= esc($k['petugas_pembawa'] ?: '-') ?></span>
                    </div>
                </div>

                <div class="mb-2">
                    <small class="text-muted text-xs uppercase d-block mb-1">Catatan Perbaikan</small>
                    <div class="p-3 bg-white rounded-3 border text-dark">
                        <?= nl2br(esc($k['catatan_perbaikan'] ?: 'Belum ada catatan perbaikan.')) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('admin/templates/footer', $templateData) ?>
