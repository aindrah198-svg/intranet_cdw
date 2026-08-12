<?= view('direktur/templates/header', $data) ?>
<?= view('direktur/templates/sidebar', $data) ?>
<?= view('direktur/templates/navbar', $data) ?>

<?php
$t = $data['task'];
$pk = $data['penerimaKaryawan'];
?>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= base_url('direktur') ?>" class="text-decoration-none text-muted">Direktur</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('direktur/penugasan') ?>" class="text-decoration-none text-muted">Penugasan Harian</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Detail Penugasan</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-file-alt text-primary me-2"></i> Detail Penugasan Harian</h4>
            <small class="text-muted">Rincian lengkap instruksi dan status pengerjaan tugas oleh karyawan.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('direktur/penugasan') ?>" class="btn btn-outline-secondary rounded-pill px-3 text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali
            </a>
            <a href="<?= base_url('direktur/penugasan/edit/'.$t['id']) ?>" class="btn btn-warning text-white rounded-pill px-3 text-sm font-semibold">
                <i class="fas fa-edit me-1.5"></i> Edit Penugasan
            </a>
        </div>
    </div>

    <!-- Main Detail Card -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 rounded-4 shadow-sm overflow-hidden mb-4">
                <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between border-bottom border-light">
                    <h5 class="card-title fs-6 fw-bold mb-0 text-dark"><?= esc($t['judul_tugas']) ?></h5>
                    <span class="badge bg-primary px-3 py-1.5 rounded-pill text-xs fw-semibold">
                        <?= strtoupper(esc($t['status'])) ?>
                    </span>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label class="text-muted text-xs font-semibold uppercase mb-1">Catatan Tambahan & Arahan Direktur</label>
                        <div class="p-3 bg-light rounded-3 text-dark text-sm border">
                            <?= nl2br(esc($t['deskripsi_tugas'] ?: 'Tidak ada catatan tambahan khusus.')) ?>
                        </div>
                    </div>

                    <!-- Checklist Item Tugas -->
                    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-tasks text-primary me-2"></i> Rincian Sub-Item Tugas Checklist</h6>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle text-sm">
                            <thead class="table-light text-xs text-muted uppercase">
                                <tr>
                                    <th style="width: 50px;" class="text-center">No</th>
                                    <th>Judul Sub-Item</th>
                                    <th>Keterangan</th>
                                    <th style="width: 120px;" class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($t['items'])): ?>
                                    <tr><td colspan="4" class="text-center py-3 text-muted">Belum ada item rincian.</td></tr>
                                <?php else: ?>
                                    <?php foreach($t['items'] as $idx => $item): 
                                        $ibadge = 'bg-secondary';
                                        if ($item['status_item']==='proses') $ibadge = 'bg-primary';
                                        if ($item['status_item']==='selesai') $ibadge = 'bg-success';
                                    ?>
                                    <tr>
                                        <td class="text-center fw-bold"><?= $idx+1 ?></td>
                                        <td class="fw-semibold text-dark"><?= esc($item['judul_item']) ?></td>
                                        <td class="text-muted text-xs"><?= esc($item['deskripsi_item'] ?: '-') ?></td>
                                        <td class="text-center">
                                            <span class="badge <?= $ibadge ?> px-2.5 py-1 rounded-pill text-xs">
                                                <?= strtoupper(esc($item['status_item'])) ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Info Card -->
        <div class="col-lg-4">
            <div class="card border-0 rounded-4 shadow-sm overflow-hidden mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom border-light">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-info-circle text-primary me-2"></i> Informasi Penerima & Waktu</h6>
                </div>
                <div class="card-body p-4 text-sm">
                    <div class="mb-3">
                        <small class="text-muted text-xs font-semibold uppercase d-block">Penerima Penugasan</small>
                        <div class="fw-bold text-dark fs-6 mt-1"><?= esc($pk['nama_lengkap'] ?? $t['penerima_nama'] ?? 'Karyawan CDW') ?></div>
                        <div class="text-muted text-xs">NIK: <?= esc($pk['nik'] ?? '-') ?> &bull; <?= esc($pk['jabatan'] ?? strtoupper($t['penerima_role'] ?? 'ALL')) ?></div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted text-xs font-semibold uppercase d-block">Prioritas</small>
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill font-semibold text-xs mt-1">
                            <?= strtoupper(esc($t['prioritas'])) ?>
                        </span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted text-xs font-semibold uppercase d-block">Tanggal Penugasan</small>
                        <div class="fw-semibold text-dark mt-1"><i class="far fa-calendar-alt text-primary me-1"></i><?= date('d F Y', strtotime($t['tanggal_tugas'])) ?></div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted text-xs font-semibold uppercase d-block">Tenggat Jam Pelaksanaan</small>
                        <div class="fw-bold text-danger mt-1"><i class="far fa-clock me-1"></i><?= date('H:i', strtotime($t['tenggat_waktu'] ?: '17:00')) ?> WIB</div>
                    </div>

                    <hr>

                    <div class="d-grid gap-2">
                        <a href="<?= base_url('direktur/penugasan/edit/'.$t['id']) ?>" class="btn btn-warning text-white rounded-pill font-semibold">
                            <i class="fas fa-edit me-1.5"></i> Edit Penugasan
                        </a>
                        <a href="<?= base_url('direktur/penugasan/delete/'.$t['id']) ?>" class="btn btn-outline-danger rounded-pill font-semibold" onclick="return confirm('Hapus penugasan ini?')">
                            <i class="fas fa-trash me-1.5"></i> Hapus Penugasan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('direktur/templates/footer', $data) ?>
