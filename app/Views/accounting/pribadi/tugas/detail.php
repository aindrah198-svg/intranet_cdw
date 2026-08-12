<?php
// View: accounting/pribadi/tugas/detail.php
// Identik dengan admin/pribadi/tugas_detail.php, dengan URL accounting
$t = $task;
$totalItems = count($t['items'] ?? []);
$completedItems = 0;
if (!empty($t['items'])) {
    foreach ($t['items'] as $it) {
        if ($it['status_item'] === 'selesai') $completedItems++;
    }
}
$isAllCompleted = ($totalItems > 0 && $completedItems === $totalItems)
               || ($totalItems === 0 && $t['status'] === 'selesai');
?>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/dashboard') ?>" class="text-decoration-none text-muted">Menu Pribadi</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/pribadi/tugas-saya') ?>" class="text-decoration-none text-muted">Tugas Hari Ini</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Detail &amp; Edit Checklist</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-tasks text-primary me-2"></i> Detail Penugasan Dari Direktur</h4>
            <small class="text-muted">Update status sub-item tugas satu per satu atau sekaligus dari pending menjadi selesai.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= site_url('accounting/pribadi/tugas-saya') ?>" class="btn btn-outline-secondary rounded-pill px-3 text-sm fw-semibold">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <?php if ($isAllCompleted): ?>
                <a href="<?= site_url('admin/tugas-saya/buat-laporan/' . $t['id']) ?>" class="btn btn-success rounded-pill px-4 text-sm fw-semibold shadow-sm">
                    <i class="fas fa-file-export me-1"></i> Buat Menjadi Laporan Harian
                </a>
            <?php else: ?>
                <button type="button" class="btn btn-secondary rounded-pill px-4 text-sm fw-semibold opacity-60" onclick="alertNotCompleted()">
                    <i class="fas fa-lock me-1"></i> Buat Menjadi Laporan Harian
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row g-4">
        <!-- Left: Checklist -->
        <div class="col-lg-8">
            <div class="card border-0 rounded-4 shadow-sm overflow-hidden mb-4">
                <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between border-bottom border-light">
                    <h5 class="card-title fs-6 fw-bold mb-0 text-dark"><?= esc($t['judul_tugas']) ?></h5>
                    <span class="badge bg-primary px-3 py-1 rounded-pill text-xs fw-semibold">
                        STATUS: <?= strtoupper(esc($t['status'] === 'proses' ? 'Sedang Dikerjakan' : $t['status'])) ?>
                    </span>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label class="text-muted text-xs fw-semibold text-uppercase mb-1">Catatan &amp; Arahan Direktur</label>
                        <div class="p-3 bg-light rounded-3 text-dark text-sm border">
                            <?= nl2br(esc($t['deskripsi_tugas'] ?: 'Tidak ada catatan tambahan khusus dari Direktur.')) ?>
                        </div>
                    </div>

                    <!-- Sub-Item Checklist Form -->
                    <form action="<?= site_url('accounting/pribadi/tugas-saya/update-subitem/' . $t['id']) ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <div>
                                <h6 class="fw-bold text-dark mb-0"><i class="fas fa-list-check text-primary me-2"></i> Rincian Sub-Item Tugas Checklist</h6>
                                <small class="text-muted text-xs">Ubah status masing-masing sub-item satu per satu atau sekaligus.</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" name="mark_all_completed" value="1" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-semibold text-xs">
                                    <i class="fas fa-check-double me-1"></i> Tandai Semua Selesai (Sekaligus)
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive mb-3">
                            <table class="table table-bordered align-middle text-sm">
                                <thead class="table-light text-xs text-muted text-uppercase">
                                    <tr>
                                        <th style="width:40px;" class="text-center">No</th>
                                        <th>Judul Sub-Item</th>
                                        <th>Deskripsi Sub-Item</th>
                                        <th style="width:180px;" class="text-center">Status Sub-Item</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($t['items'])): ?>
                                        <tr><td colspan="4" class="text-center py-3 text-muted">Belum ada rincian sub-item checklist.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($t['items'] as $idx => $item): ?>
                                        <tr>
                                            <td class="text-center fw-bold"><?= $idx + 1 ?></td>
                                            <td class="fw-semibold text-dark"><?= esc($item['judul_item']) ?></td>
                                            <td class="text-muted text-xs"><?= esc($item['deskripsi_item'] ?: '-') ?></td>
                                            <td class="text-center">
                                                <select name="subitem_status[<?= $item['id'] ?>]"
                                                        class="form-select form-select-sm rounded-pill text-xs fw-bold text-center <?= $item['status_item'] === 'selesai' ? 'border-success text-success' : 'border-warning text-dark' ?>">
                                                    <option value="pending"  <?= $item['status_item'] === 'pending'  ? 'selected' : '' ?>>Pending (Belum)</option>
                                                    <option value="proses"   <?= $item['status_item'] === 'proses'   ? 'selected' : '' ?>>Sedang Dikerjakan</option>
                                                    <option value="selesai"  <?= $item['status_item'] === 'selesai'  ? 'selected' : '' ?>>Selesai</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                            <div class="text-xs text-muted">
                                <i class="fas fa-info-circle text-primary me-1"></i> Setelah mengubah status sub-item, klik Simpan Perubahan di samping.
                            </div>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm text-sm">
                                <i class="fas fa-save me-1"></i> Simpan Perubahan Sub-Item
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right: Status & Info -->
        <div class="col-lg-4">
            <div class="card border-0 rounded-4 shadow-sm overflow-hidden mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom border-light">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-clock text-primary me-2"></i> Status Utama &amp; Waktu</h6>
                </div>
                <div class="card-body p-4 text-sm">
                    <div class="mb-3">
                        <small class="text-muted text-xs fw-semibold text-uppercase d-block">Progres Pemenuhan Checklist</small>
                        <div class="fw-bold text-dark fs-6 mt-1 mb-1"><?= $completedItems ?> / <?= $totalItems ?> Sub-Item Selesai</div>
                        <div class="progress rounded-pill" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: <?= $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 0 ?>%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted text-xs fw-semibold text-uppercase d-block">Prioritas</small>
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1 rounded-pill fw-semibold text-xs mt-1">
                            <?= strtoupper(esc($t['prioritas'] ?? 'Normal')) ?>
                        </span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted text-xs fw-semibold text-uppercase d-block">Tanggal Tugas</small>
                        <div class="fw-semibold text-dark mt-1">
                            <i class="far fa-calendar-alt text-primary me-1"></i><?= date('d F Y', strtotime($t['tanggal_tugas'])) ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted text-xs fw-semibold text-uppercase d-block">Tenggat Jam</small>
                        <div class="fw-bold text-danger mt-1">
                            <i class="far fa-clock me-1"></i><?= date('H:i', strtotime($t['tenggat_waktu'] ?: '17:00')) ?> WIB
                        </div>
                    </div>

                    <hr>

                    <!-- Ubah Status Utama -->
                    <form action="<?= site_url('accounting/pribadi/tugas-saya/update-status/' . $t['id']) ?>" method="POST" class="mb-3">
                        <?= csrf_field() ?>
                        <label class="form-label fw-bold text-dark text-xs mb-1">Ubah Status Utama Tugas</label>
                        <select name="status" class="form-select form-select-sm rounded-3 mb-2" onchange="this.form.submit()">
                            <option value="pending" <?= $t['status'] === 'pending' ? 'selected' : '' ?>>Pending / Belum Dikerjakan</option>
                            <option value="proses"  <?= $t['status'] === 'proses'  ? 'selected' : '' ?>>Sedang Dikerjakan</option>
                            <option value="selesai" <?= $t['status'] === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                        </select>
                    </form>

                    <div class="d-grid mt-3">
                        <?php if ($isAllCompleted): ?>
                            <a href="<?= site_url('admin/tugas-saya/buat-laporan/' . $t['id']) ?>" class="btn btn-success rounded-pill fw-semibold shadow-sm text-sm">
                                <i class="fas fa-file-export me-1"></i> Buat Laporan Harian
                            </a>
                        <?php else: ?>
                            <button type="button" class="btn btn-secondary rounded-pill fw-semibold text-sm opacity-60" onclick="alertNotCompleted()">
                                <i class="fas fa-lock me-1"></i> Buat Laporan Harian (Terkunci)
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function alertNotCompleted() {
    Swal.fire({
        icon: 'warning',
        title: 'Belum Selesai Semua!',
        text: 'Seluruh Rincian Sub-Item Tugas Checklist harus diubah dari PENDING menjadi SELESAI terlebih dahulu sebelum Anda dapat membuat Laporan Kerja Harian ke Direktur.',
        confirmButtonColor: '#0d6efd',
        customClass: { popup: 'rounded-4 shadow-lg' }
    });
}
</script>

<?php if (session()->getFlashdata('success')): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success', title: 'Berhasil!',
        text: '<?= esc(session()->getFlashdata('success')) ?>',
        timer: 3500, showConfirmButton: false,
        customClass: { popup: 'rounded-4 shadow-lg' }
    });
});
</script>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'error', title: 'Perhatian!',
        text: '<?= esc(session()->getFlashdata('error')) ?>',
        confirmButtonColor: '#dc3545',
        customClass: { popup: 'rounded-4 shadow-lg' }
    });
});
</script>
<?php endif; ?>
