<?php
// View: accounting/pribadi/tugas/index.php
// Identik dengan admin/pribadi/tugas.php, dengan URL accounting
?>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/dashboard') ?>" class="text-decoration-none text-muted">Menu Pribadi</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Tugas Hari Ini</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-tasks text-primary me-2"></i> Penugasan Hari Ini Dari Direktur</h4>
            <small class="text-muted">Daftar tugas yang didelegasikan oleh Direktur. Selesaikan seluruh rincian sub-item tugas sebelum mengonversi menjadi Laporan Harian.</small>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card border-0 rounded-4 shadow-sm overflow-hidden mb-4">
        <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2 border-bottom border-light">
            <h5 class="card-title fs-6 fw-bold mb-0 text-dark"><i class="fas fa-list-check me-2 text-primary"></i> Daftar Penugasan Direktur</h5>
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold">
                Total: <?= count($tasks) ?> Tugas
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-sm">
                    <thead class="table-light text-uppercase text-xs text-muted">
                        <tr>
                            <th class="ps-4 py-3">Judul Tugas</th>
                            <th class="py-3 text-center">Progres Sub-Item Checklist</th>
                            <th class="py-3 text-center">Tanggal &amp; Tenggat</th>
                            <th class="py-3 text-center">Status Utama</th>
                            <th class="pe-4 py-3 text-end">Aksi &amp; Konversi Laporan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tasks)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-clipboard-check fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                                Belum ada tugas harian dari Direktur yang ditugaskan kepada Anda.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($tasks as $t):
                            $totalItems     = count($t['items'] ?? []);
                            $completedItems = 0;
                            if (!empty($t['items'])) {
                                foreach ($t['items'] as $it) {
                                    if ($it['status_item'] === 'selesai') $completedItems++;
                                }
                            }
                            $isAllCompleted = ($totalItems > 0 && $completedItems === $totalItems)
                                           || ($totalItems === 0 && $t['status'] === 'selesai');

                            $stBadge = 'bg-warning text-dark';
                            if ($t['status'] === 'proses')  $stBadge = 'bg-primary text-white';
                            if ($t['status'] === 'selesai') $stBadge = 'bg-success text-white';
                        ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark fs-6"><?= esc($t['judul_tugas']) ?></div>
                                <small class="text-muted text-xs d-block text-truncate" style="max-width: 250px;">
                                    <?= esc($t['deskripsi_tugas'] ?: 'Tanpa catatan tambahan') ?>
                                </small>
                            </td>
                            <td class="text-center">
                                <?php if ($totalItems > 0): ?>
                                    <div class="fw-bold text-dark text-xs mb-1"><?= $completedItems ?> / <?= $totalItems ?> Sub-Item Selesai</div>
                                    <div class="progress rounded-pill" style="height: 6px; width: 120px; margin: 0 auto;">
                                        <div class="progress-bar bg-success" style="width: <?= round(($completedItems / $totalItems) * 100) ?>%"></div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted text-xs">Tanpa sub-item</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center text-nowrap">
                                <span class="badge bg-light text-dark border px-2 py-1 text-xs mb-1 d-inline-block">
                                    <i class="far fa-calendar-alt text-primary me-1"></i><?= date('d M Y', strtotime($t['tanggal_tugas'])) ?>
                                </span>
                                <?php if (!empty($t['tenggat_waktu'])): ?>
                                    <div class="text-xs text-danger fw-semibold"><i class="far fa-clock me-1"></i><?= date('H:i', strtotime($t['tenggat_waktu'])) ?> WIB</div>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $stBadge ?> px-3 py-1 rounded-pill text-xs fw-semibold">
                                    <?= strtoupper(esc($t['status'] === 'proses' ? 'Sedang Dikerjakan' : $t['status'])) ?>
                                </span>
                            </td>
                            <td class="pe-4 text-end text-nowrap">
                                <div class="d-inline-flex gap-1 align-items-center flex-wrap justify-content-end">
                                    <!-- Detail & Edit Sub-item -->
                                    <a href="<?= site_url('accounting/pribadi/tugas-saya/detail/' . $t['id']) ?>"
                                       class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold text-xs"
                                       title="Lihat Detail & Edit Checklist">
                                        <i class="fas fa-edit me-1"></i> Detail &amp; Edit Checklist
                                    </a>

                                    <!-- Buat Laporan (terkunci jika belum selesai) -->
                                    <?php if ($isAllCompleted): ?>
                                        <a href="<?= site_url('admin/tugas-saya/buat-laporan/' . $t['id']) ?>"
                                           class="btn btn-success btn-sm rounded-pill px-3 shadow-sm fw-semibold text-xs"
                                           title="Konversi & Pratinjau Laporan Harian">
                                            <i class="fas fa-file-export me-1"></i> Buat Laporan Harian
                                        </a>
                                    <?php else: ?>
                                        <button type="button"
                                                class="btn btn-secondary btn-sm rounded-pill px-3 text-xs opacity-60"
                                                onclick="alertNotCompleted()"
                                                title="Selesaikan seluruh sub-item terlebih dahulu">
                                            <i class="fas fa-lock me-1"></i> Buat Laporan Harian
                                        </button>
                                    <?php endif; ?>
                                </div>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function alertNotCompleted() {
    Swal.fire({
        icon: 'warning',
        title: 'Belum Selesai Semua!',
        text: 'Seluruh Rincian Sub-Item Tugas Checklist harus diubah statusnya dari pending menjadi SELESAI terlebih dahulu sebelum dapat membuat Laporan Harian ke Direktur.',
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
