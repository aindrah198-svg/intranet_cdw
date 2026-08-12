<?php
$data = [
    'title'    => 'Pengajuan Cuti',
    'subtitle' => 'Daftar Pengajuan Cuti & Izin Karyawan CDW Engineering',
    'active'   => 'pengajuan-cuti',
    'user'     => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
];

$totalCuti = count($cutiList ?? []);
$approvedCuti = 0;
$pendingCuti = 0;
foreach($cutiList as $c) {
    if(strtolower($c['status'] ?? '') === 'disetujui') $approvedCuti++;
    if(strtolower($c['status'] ?? '') === 'menunggu') $pendingCuti++;
}
?>

<?= view('admin/templates/header', $data) ?>
<?= view('admin/templates/sidebar', $data) ?>
<?= view('admin/templates/navbar', $data) ?>

<style>
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
        overflow-x: hidden !important;
    }
    .row {
        margin-left: -6px !important;
        margin-right: -6px !important;
    }
    .row > [class*="col-"] {
        padding-left: 6px !important;
        padding-right: 6px !important;
    }
    .card {
        max-width: 100% !important;
        box-sizing: border-box !important;
    }
    .cuti-card-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(30, 60, 114, 0.06), 0 2px 6px rgba(0, 0, 0, 0.02) !important;
        transition: all 0.3s ease;
    }
    .stat-card-cuti {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(30, 60, 114, 0.05);
    }
    .table-scroll-wrapper {
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
        display: block !important;
        margin-bottom: 0;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
    }
    .table-scroll-wrapper table {
        min-width: 860px !important;
        width: 100% !important;
        margin-bottom: 0 !important;
    }
</style>

<div class="container-fluid py-3 py-md-4">

    <!-- 1. Header Section -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-3 border border-light flex-wrap gap-3">
        <div class="d-flex align-items-center overflow-hidden">
            <div class="bg-info text-white rounded-3 p-2 me-3 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 44px; height: 44px; background: linear-gradient(135deg, #0288d1, #01579b) !important;">
                <i class="fas fa-umbrella-beach fs-5"></i>
            </div>
            <div class="overflow-hidden">
                <h4 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 1.25rem;">Pengajuan Cuti Karyawan</h4>
                <small class="text-muted d-none d-sm-inline">Manajemen permohonan cuti tahunan, sakit, dan izin khusus karyawan.</small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <?php if($canAddCuti): ?>
                <a href="<?= base_url('admin/pengajuan/cuti/tambah') ?>" class="btn btn-info text-white rounded-pill shadow-sm d-inline-flex align-items-center text-sm fw-semibold px-3" style="background: #0288d1; border-color: #0288d1;">
                    <i class="fas fa-plus me-1.5"></i> <span>Ajukan Cuti Baru</span>
                </a>
            <?php else: ?>
                <button type="button" onclick="alertNoQuota()" class="btn btn-secondary rounded-pill shadow-sm d-inline-flex align-items-center text-sm fw-semibold px-3 opacity-75">
                    <i class="fas fa-lock me-1.5"></i> <span>Ajukan Cuti Baru (Dikunci)</span>
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- 1.5 Card Status Kuota Cuti Saya -->
    <div class="card p-3 mb-3 border-0 rounded-4 shadow-sm" style="background: linear-gradient(135deg, #e0f7fa, #e1f5fe); border-left: 5px solid #0288d1 !important;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center">
                <div class="rounded-circle text-white me-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 46px; height: 46px; background: #0288d1 !important;">
                    <i class="fas fa-chart-pie fs-5"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0 fs-6">Jatah Kuota Cuti Tahunan Saya (Tahun <?= date('Y') ?>)</h6>
                    <small class="text-muted text-xs">
                        <?php if(!empty($kuotaInfo)): ?>
                            Status Kuota: <strong class="text-dark"><?= (int)$kuotaInfo['kuota_tahunan'] ?> Hari</strong> Total &nbsp;|&nbsp; Terpakai: <strong class="text-danger"><?= (int)$kuotaInfo['terpakai'] ?> Hari</strong> &nbsp;|&nbsp; Sisa Kuota: <strong class="text-success fs-6"><?= (int)$sisaKuota ?> Hari</strong>
                        <?php else: ?>
                            <span class="text-danger font-bold"><i class="fas fa-exclamation-triangle me-1"></i> Jatah kuota cuti tahunan belum ditambahkan oleh Direktur!</span>
                        <?php endif; ?>
                    </small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <?php if(!$canAddCuti): ?>
                    <span class="badge bg-danger px-3 py-2 rounded-pill text-xs fw-semibold">
                        <i class="fas fa-lock me-1"></i> Tidak Bisa Tambah Cuti (0 Hari)
                    </span>
                <?php else: ?>
                    <span class="badge bg-success px-3 py-2 rounded-pill text-xs fw-semibold">
                        <i class="fas fa-check-circle me-1"></i> Kuota Tersedia (<?= $sisaKuota ?> Hari)
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <?php if(!$canAddCuti): ?>
            <div class="alert alert-danger mb-0 mt-3 py-2 px-3 text-xs rounded-3 d-flex align-items-center border-danger bg-white text-danger fw-semibold">
                <i class="fas fa-exclamation-circle fs-5 me-2.5 text-danger"></i>
                <div>
                    <?php if(!empty($kuotaInfo)): ?>
                        <strong>Perhatian:</strong> Sisa kuota cuti tahunan Anda saat ini <strong>0 Hari</strong>. Anda tidak dapat mengajukan permohonan cuti baru sampai jatah kuota ditambahkan kembali oleh Direktur.
                    <?php else: ?>
                        <strong>Perhatian:</strong> Anda belum memiliki jatah kuota cuti tahunan. Silakan minta Direktur untuk menambahkan kuota cuti pada menu <strong>Karyawan & SDM -> Cuti Karyawan</strong> di portal Direktur.
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- 2. KPI Ringkasan Cuti -->
    <div class="row g-2 g-md-3 mb-3">
        <div class="col-6 col-md-4">
            <div class="card stat-card-cuti p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-umbrella-beach text-info"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Total Permohonan Cuti</small>
                        <div class="fs-4 fw-bold text-info text-truncate"><?= number_format($totalCuti) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card stat-card-cuti p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-hourglass-half text-warning"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Menunggu Persetujuan</small>
                        <div class="fs-4 fw-bold text-warning text-truncate"><?= number_format($pendingCuti) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card stat-card-cuti p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Cuti Disetujui</small>
                        <div class="fs-4 fw-bold text-success text-truncate"><?= number_format($approvedCuti) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Tabel Pengajuan Cuti -->
    <div class="card cuti-card-modern p-3 p-md-4 mb-3">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-0 fs-6 fs-md-5 d-flex align-items-center">
                    <i class="fas fa-list me-2 text-info"></i> Data Pengajuan Cuti Karyawan
                </h5>
                <small class="text-muted text-xs d-block mt-0.5">Daftar lengkap cuti yang diajukan ke Direktur/Manajemen.</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <input type="text" id="searchTableCuti" class="form-control form-control-sm rounded-pill px-3" style="max-width: 220px;" placeholder="Cari nama / nomor cuti...">
                <?php if($canAddCuti): ?>
                    <a href="<?= base_url('admin/pengajuan/cuti/tambah') ?>" class="btn btn-sm btn-info text-white rounded-pill px-3 fw-semibold text-nowrap shadow-sm" style="background: #0288d1; border-color: #0288d1;">
                        <i class="fas fa-plus me-1"></i> Tambah Cuti
                    </a>
                <?php else: ?>
                    <button type="button" onclick="alertNoQuota()" class="btn btn-sm btn-secondary text-white rounded-pill px-3 fw-semibold text-nowrap shadow-sm opacity-75">
                        <i class="fas fa-lock me-1"></i> Tambah Cuti (0 Hari)
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="table-scroll-wrapper">
            <table class="table table-hover align-middle text-sm mb-0" id="cutiTable">
                <thead class="table-light text-nowrap">
                    <tr>
                        <th width="18%">No. Cuti / Tgl</th>
                        <th width="22%">Nama Karyawan & Divisi</th>
                        <th width="15%">Jenis & Durasi</th>
                        <th width="18%">Periode Cuti</th>
                        <th width="12%">Status</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($cutiList)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pengajuan cuti terdaftar.</td></tr>
                    <?php else: ?>
                        <?php foreach($cutiList as $c): ?>
                        <tr>
                            <td class="text-nowrap">
                                <div class="fw-bold text-dark"><i class="fas fa-umbrella-beach text-info me-1"></i><?= esc($c['nomor_cuti']) ?></div>
                                <small class="text-muted text-xs"><i class="fas fa-calendar me-1"></i><?= date('d M Y', strtotime($c['tanggal_pengajuan'])) ?></small>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= esc($c['nama_lengkap'] ?: 'Admin User') ?></div>
                                <small class="text-muted text-xs"><?= esc($c['divisi'] ?: 'Administrasi') ?> - <?= esc($c['jabatan'] ?: 'Admin') ?></small>
                            </td>
                            <td class="text-nowrap">
                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2.5 py-0.5 rounded-pill text-xs fw-semibold">
                                    <?= esc($c['jenis_cuti']) ?>
                                </span>
                                <small class="text-dark fw-bold d-block mt-0.5"><?= (int)$c['lama_hari'] ?> Hari</small>
                            </td>
                            <td class="text-nowrap">
                                <small class="text-dark fw-semibold d-block"><?= date('d M Y', strtotime($c['tanggal_mulai'])) ?></small>
                                <small class="text-muted text-xs">s/d <?= date('d M Y', strtotime($c['tanggal_selesai'])) ?></small>
                            </td>
                            <td class="text-nowrap">
                                <?php
                                    $st = strtolower($c['status'] ?? 'menunggu');
                                    $badge = 'bg-warning text-dark';
                                    if ($st === 'disetujui') $badge = 'bg-success text-white';
                                    if ($st === 'ditolak') $badge = 'bg-danger text-white';
                                ?>
                                <span class="badge <?= $badge ?> px-2.5 py-1 rounded-pill text-xs fw-semibold">
                                    <?= strtoupper(esc($c['status'] ?? 'Menunggu')) ?>
                                </span>
                            </td>
                            <td class="text-center text-nowrap">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <a href="<?= base_url('admin/pengajuan/cuti/detail/'.$c['id']) ?>" class="btn btn-xs btn-outline-info rounded-pill px-2.5 py-1 text-xs fw-semibold">
                                        <i class="fas fa-eye me-1"></i> Detail
                                    </a>
                                    <?php if ($st === 'menunggu'): ?>
                                        <a href="<?= base_url('admin/pengajuan/cuti/edit/'.$c['id']) ?>" class="btn btn-xs btn-outline-warning rounded-pill px-2.5 py-1 text-xs fw-semibold">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </a>
                                        <button type="button" onclick="confirmDeleteCuti(<?= $c['id'] ?>, '<?= esc($c['nomor_cuti'], 'js') ?>')" class="btn btn-xs btn-outline-danger rounded-pill px-2 py-1 text-xs fw-semibold">
                                            <i class="fas fa-trash me-1"></i>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (session()->getFlashdata('success')): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '<?= esc(session()->getFlashdata('success')) ?>',
        confirmButtonColor: '#0288d1',
        timer: 3000,
        timerProgressBar: true,
        customClass: { popup: 'rounded-4 shadow-lg' }
    });
});
</script>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'error',
        title: 'Perhatian!',
        text: '<?= esc(session()->getFlashdata('error')) ?>',
        confirmButtonColor: '#dc3545',
        customClass: { popup: 'rounded-4 shadow-lg' }
    });
});
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchTableCuti');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#cutiTable tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }
});

function alertNoQuota() {
    Swal.fire({
        icon: 'error',
        title: 'Pengajuan Cuti Dibatasi',
        html: '<?= !empty($kuotaInfo) ? "Sisa kuota cuti tahunan Anda saat ini <strong>0 Hari</strong>." : "Jatah kuota cuti tahunan Anda <strong>belum ditambahkan oleh Direktur</strong>." ?><br><br>Silakan minta Direktur untuk menambahkan/memperbarui jatah kuota cuti Anda pada menu <strong>Karyawan & SDM -> Cuti Karyawan</strong> di portal Direktur.',
        confirmButtonColor: '#d32f2f',
        customClass: { popup: 'rounded-4 shadow-lg' }
    });
}

function confirmDeleteCuti(id, nomor) {
    Swal.fire({
        title: 'Hapus Pengajuan Cuti?',
        text: 'Pengajuan cuti "' + nomor + '" akan dihapus.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-4 shadow-lg' }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= base_url('admin/pengajuan/cuti/delete') ?>/' + id;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>

<?= view('admin/templates/footer', $data) ?>
