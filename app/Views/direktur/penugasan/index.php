<?= view('direktur/templates/header', $data) ?>
<?= view('direktur/templates/sidebar', $data) ?>
<?= view('direktur/templates/navbar', $data) ?>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= base_url('direktur') ?>" class="text-decoration-none text-muted">Direktur</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Penugasan Harian</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-tasks text-primary me-2"></i> Penugasan Harian Karyawan</h4>
            <small class="text-muted">Kelola & delegasikan tugas harian kepada staf/karyawan yang memiliki akun aktif.</small>
        </div>
        <div>
            <a href="<?= base_url('direktur/penugasan/tambah') ?>" class="btn btn-primary rounded-pill px-4 font-semibold shadow-sm text-sm">
                <i class="fas fa-plus-circle me-1.5"></i> Buat Penugasan Baru
            </a>
        </div>
    </div>

    <!-- Stats Card Row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-primary bg-opacity-10 border-start border-4 border-primary">
                <div class="text-muted text-xs font-semibold uppercase">Total Penugasan</div>
                <div class="fs-4 fw-bold text-primary mt-1"><?= $data['stats']['total'] ?? 0 ?></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-warning bg-opacity-10 border-start border-4 border-warning">
                <div class="text-muted text-xs font-semibold uppercase">Menunggu (Pending)</div>
                <div class="fs-4 fw-bold text-warning mt-1"><?= $data['stats']['pending'] ?? 0 ?></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-info bg-opacity-10 border-start border-4 border-info">
                <div class="text-muted text-xs font-semibold uppercase">Sedang Dikerjakan</div>
                <div class="fs-4 fw-bold text-info mt-1"><?= $data['stats']['proses'] ?? 0 ?></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 rounded-4 shadow-sm p-3 bg-success bg-opacity-10 border-start border-4 border-success">
                <div class="text-muted text-xs font-semibold uppercase">Selesai</div>
                <div class="fs-4 fw-bold text-success mt-1"><?= $data['stats']['selesai'] ?? 0 ?></div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card border-0 rounded-4 shadow-sm overflow-hidden mb-4">
        <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2 border-bottom border-light">
            <h5 class="card-title fs-6 fw-bold mb-0 text-dark"><i class="fas fa-list-check me-2 text-primary"></i> Daftar Penugasan Harian</h5>
            
            <form action="<?= base_url('direktur/penugasan') ?>" method="GET" class="d-flex gap-2 align-items-center flex-wrap">
                <input type="date" name="tanggal" value="<?= esc($data['filterTanggal']) ?>" class="form-select form-select-sm rounded-pill text-xs" style="width: auto;" onchange="this.form.submit()">
                <select name="status" class="form-select form-select-sm rounded-pill text-xs" style="width: auto;" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="pending" <?= $data['filterStatus']==='pending'?'selected':'' ?>>Pending</option>
                    <option value="proses" <?= $data['filterStatus']==='proses'?'selected':'' ?>>Proses</option>
                    <option value="selesai" <?= $data['filterStatus']==='selesai'?'selected':'' ?>>Selesai</option>
                    <option value="ditunda" <?= $data['filterStatus']==='ditunda'?'selected':'' ?>>Ditunda</option>
                </select>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-sm">
                    <thead class="table-light text-uppercase text-xs text-muted">
                        <tr>
                            <th class="ps-4 py-3">Judul Penugasan</th>
                            <th class="py-3">Penerima / Karyawan</th>
                            <th class="py-3 text-center">Tanggal & Jam</th>
                            <th class="py-3 text-center">Prioritas</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="pe-4 py-3 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data['tasks'])): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-tasks fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                                Belum ada penugasan harian yang dibuat.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach($data['tasks'] as $t): 
                            $prioBadge = 'bg-secondary';
                            if ($t['prioritas']==='mendesak') $prioBadge = 'bg-danger';
                            if ($t['prioritas']==='tinggi') $prioBadge = 'bg-warning text-dark';
                            if ($t['prioritas']==='sedang') $prioBadge = 'bg-primary';
                            if ($t['prioritas']==='rendah') $prioBadge = 'bg-info';

                            $stBadge = 'bg-warning text-dark';
                            if ($t['status']==='proses') $stBadge = 'bg-primary';
                            if ($t['status']==='selesai') $stBadge = 'bg-success';
                            if ($t['status']==='ditunda') $stBadge = 'bg-secondary';
                        ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark fs-6"><?= esc($t['judul_tugas']) ?></div>
                                <small class="text-muted text-xs d-block text-truncate" style="max-width: 250px;">
                                    <?= esc($t['deskripsi_tugas'] ?: 'Tanpa catatan tambahan') ?>
                                </small>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark"><?= esc($t['penerima_nama'] ?? $t['penerima_role'] ?? 'Karyawan CDW') ?></div>
                                <small class="text-muted text-xs"><?= esc($t['penerima_jabatan'] ?? strtoupper($t['penerima_role'] ?? 'ALL')) ?></small>
                            </td>
                            <td class="text-center text-nowrap">
                                <span class="badge bg-light text-dark border px-2.5 py-1 text-xs mb-1 d-inline-block">
                                    <i class="far fa-calendar-alt text-primary me-1"></i><?= date('d M Y', strtotime($t['tanggal_tugas'])) ?>
                                </span>
                                <?php if(!empty($t['tenggat_waktu'])): ?>
                                    <div class="text-xs text-danger font-semibold"><i class="far fa-clock me-1"></i>Tenggat: <?= date('H:i', strtotime($t['tenggat_waktu'])) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $prioBadge ?> px-2.5 py-1 rounded-pill text-xs fw-semibold">
                                    <?= strtoupper(esc($t['prioritas'])) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $stBadge ?> px-3 py-1 rounded-pill text-xs fw-semibold">
                                    <?= strtoupper(esc($t['status'])) ?>
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= base_url('direktur/penugasan/detail/'.$t['id']) ?>" class="btn btn-outline-info rounded-pill px-2.5 me-1" title="Lihat Detail">
                                        <i class="fas fa-eye me-1"></i> Detail
                                    </a>
                                    <a href="<?= base_url('direktur/penugasan/edit/'.$t['id']) ?>" class="btn btn-outline-warning rounded-pill px-2.5 me-1" title="Edit Penugasan">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>
                                    <a href="<?= base_url('direktur/penugasan/delete/'.$t['id']) ?>" class="btn btn-outline-danger rounded-pill px-2.5" onclick="return confirm('Apakah Anda yakin ingin menghapus penugasan ini?')" title="Hapus">
                                        <i class="fas fa-trash me-1"></i> Hapus
                                    </a>
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
<?php if (session()->getFlashdata('success')): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '<?= esc(session()->getFlashdata('success')) ?>',
        timer: 3000,
        showConfirmButton: false,
        customClass: { popup: 'rounded-4 shadow-lg' }
    });
});
</script>
<?php endif; ?>

<?= view('direktur/templates/footer', $data) ?>
