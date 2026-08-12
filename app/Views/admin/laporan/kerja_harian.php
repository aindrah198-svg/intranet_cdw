<?php
$data = [
    'title'  => 'Laporan Kerja Harian',
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
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Laporan Kerja Harian</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-tasks text-primary me-2"></i> Laporan Kerja Harian Staf / Admin</h4>
            <small class="text-muted">Kelola pencatatan aktivitas dan laporan harian yang dikirim ke Direktur/Manajemen.</small>
        </div>
        <div>
            <a href="<?= base_url('admin/laporan/kerja-harian/tambah') ?>" class="btn btn-primary rounded-pill px-4 font-semibold shadow-sm text-sm">
                <i class="fas fa-plus-circle me-1.5"></i> Buat Laporan Harian
            </a>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card border-0 rounded-4 shadow-sm overflow-hidden mb-4">
        <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2 border-bottom border-light">
            <h5 class="card-title fs-6 fw-bold mb-0 text-dark"><i class="fas fa-file-alt me-2 text-primary"></i> Daftar Laporan Kerja Harian</h5>
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1.5 rounded-pill font-semibold">
                Total: <?= count($laporanList) ?> Laporan
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-sm">
                    <thead class="table-light text-uppercase text-xs text-muted">
                        <tr>
                            <th class="ps-4 py-3">Pelapor / Karyawan</th>
                            <th class="py-3">Judul Laporan</th>
                            <th class="py-3 text-center">Tanggal</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="pe-4 py-3 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($laporanList)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                                Belum ada laporan kerja harian.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach($laporanList as $l): 
                            $stBadge = 'bg-primary';
                            if ($l['status']==='Disetujui') $stBadge = 'bg-success';
                            if ($l['status']==='Ditolak' || $l['status']==='Revisi') $stBadge = 'bg-danger';
                            if ($l['status']==='Draft') $stBadge = 'bg-secondary';
                        ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark"><?= esc($l['nama_lengkap'] ?: session()->get('name') ?: 'Admin') ?></div>
                                <small class="text-muted text-xs"><?= esc($l['jabatan'] ?: 'Administrator') ?></small>
                            </td>
                            <td>
                                <div class="fw-bold text-primary fs-6"><?= esc($l['judul']) ?></div>
                                <small class="text-muted text-xs d-block text-truncate" style="max-width: 300px;">
                                    <?= esc($l['deskripsi'] ?: 'Tanpa deskripsi') ?>
                                </small>
                            </td>
                            <td class="text-center text-nowrap">
                                <span class="badge bg-light text-dark border px-2.5 py-1 text-xs">
                                    <i class="far fa-calendar-alt text-primary me-1"></i><?= date('d M Y', strtotime($l['tanggal'])) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $stBadge ?> px-3 py-1 rounded-pill text-xs fw-semibold">
                                    <?= strtoupper(esc($l['status'] ?? 'Terkirim')) ?>
                                </span>
                            </td>
                            <td class="pe-4 text-end text-nowrap">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= base_url('admin/laporan/kerja-harian/detail/'.$l['id']) ?>" class="btn btn-outline-info rounded-pill px-2.5 me-1" title="Detail Laporan">
                                        <i class="fas fa-eye me-1"></i> Detail
                                    </a>
                                    <a href="<?= base_url('admin/laporan/kerja-harian/edit/'.$l['id']) ?>" class="btn btn-outline-warning rounded-pill px-2.5 me-1" title="Edit Laporan">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>
                                    <a href="<?= base_url('admin/laporan/kerja-harian/delete/'.$l['id']) ?>" class="btn btn-outline-danger rounded-pill px-2.5" onclick="return confirm('Apakah Anda yakin ingin menghapus laporan kerja harian ini?')" title="Hapus">
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
        timer: 3500,
        showConfirmButton: false,
        customClass: { popup: 'rounded-4 shadow-lg' }
    });
});
</script>
<?php endif; ?>

<?= view('admin/templates/footer', $data) ?>
