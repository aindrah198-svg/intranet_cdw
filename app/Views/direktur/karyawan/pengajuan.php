<?php
$data = [
    'title'  => 'Kelola Permohonan & Izin Karyawan (Non-Cuti)',
    'active' => 'karyawan',
    'user'   => ['name' => session()->get('name') ?? 'Direktur', 'role' => 'direktur']
];

echo view('direktur/templates/header', $data);
echo view('direktur/templates/sidebar', $data);
echo view('direktur/templates/navbar', $data);
?>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= base_url('direktur') ?>" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item text-muted">Karyawan & SDM</li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Permohonan & Izin</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-clipboard-check text-primary me-2"></i> Permohonan & Izin Karyawan (Non-Cuti)</h4>
            <small class="text-muted">Daftar persetujuan permohonan Sakit, Kecelakaan, WFH, WFC, Dinas Luar, & Izin dari karyawan.</small>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card border-0 rounded-4 shadow-sm overflow-hidden mb-4">
        <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2 border-bottom border-light">
            <h5 class="card-title fs-6 fw-bold mb-0 text-dark"><i class="fas fa-list me-2 text-primary"></i> Daftar Permohonan & Izin Karyawan</h5>
            <div class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill font-semibold">
                Total: <?= count($pengajuan) ?> Permohonan
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-sm">
                    <thead class="table-light text-uppercase text-xs text-muted">
                        <tr>
                            <th class="ps-4 py-3">No. Pengajuan</th>
                            <th class="py-3">Pemohon / Karyawan</th>
                            <th class="py-3">Kategori</th>
                            <th class="py-3">Judul / Perihal</th>
                            <th class="py-3 text-center">Tanggal Mulai - Selesai</th>
                            <th class="py-3 text-center">Bukti Foto</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="pe-4 py-3 text-center">Aksi Direktur</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pengajuan)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                                Belum ada data permohonan atau izin non-cuti dari karyawan.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach($pengajuan as $p): 
                            $statusStr = strtolower($p['status'] ?? 'menunggu');
                            $badgeClass = 'bg-warning text-dark';
                            if ($statusStr === 'disetujui') $badgeClass = 'bg-success text-white';
                            if ($statusStr === 'ditolak') $badgeClass = 'bg-danger text-white';
                        ?>
                        <tr>
                            <td class="ps-4 fw-bold text-primary">
                                <?= esc($p['nomor_pengajuan'] ?? 'PGJ-'.$p['id']) ?>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= esc($p['nama_lengkap'] ?? 'Admin/Karyawan') ?></div>
                                <div class="text-xs text-muted"><?= esc($p['divisi'] ?? '-') ?> &bull; <?= esc($p['jabatan'] ?? '-') ?></div>
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-dark border border-secondary border-opacity-25 px-2.5 py-1 rounded-pill text-xs fw-semibold">
                                    <?= esc($p['kategori_pengajuan'] ?? 'Izin') ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark text-truncate" style="max-width: 200px;" title="<?= esc($p['judul_pengajuan']) ?>">
                                    <?= esc($p['judul_pengajuan']) ?>
                                </div>
                            </td>
                            <td class="text-center text-nowrap">
                                <span class="badge bg-light text-dark border px-2 py-1 rounded-2 text-xs">
                                    <i class="far fa-calendar-alt text-primary me-1"></i>
                                    <?= date('d/m/Y', strtotime($p['tanggal_mulai'])) ?> 
                                    <?php if($p['tanggal_mulai'] !== $p['tanggal_selesai']): ?>
                                        - <?= date('d/m/Y', strtotime($p['tanggal_selesai'])) ?>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if (!empty($p['bukti_foto'])): ?>
                                    <a href="<?= base_url($p['bukti_foto']) ?>" target="_blank" class="d-inline-block position-relative">
                                        <img src="<?= base_url($p['bukti_foto']) ?>" alt="Bukti" class="rounded-3 shadow-sm border" style="width: 42px; height: 42px; object-fit: cover;">
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted text-xs italic">Tanpa Foto</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $badgeClass ?> px-3 py-1.5 rounded-pill text-xs fw-semibold">
                                    <?= strtoupper(esc($p['status'] ?? 'Menunggu')) ?>
                                </span>
                            </td>
                            <td class="pe-4 text-center text-nowrap">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <a href="<?= base_url('direktur/karyawan/pengajuan/detail/'.$p['id']) ?>" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1 text-xs fw-semibold">
                                        <i class="fas fa-eye me-1"></i> Detail
                                    </a>
                                    <a href="<?= base_url('direktur/karyawan/pengajuan/edit/'.$p['id']) ?>" class="btn btn-xs btn-outline-warning rounded-pill px-2.5 py-1 text-xs fw-semibold">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>

                                    <?php if($statusStr === 'menunggu'): ?>
                                        <form action="<?= base_url('direktur/karyawan/pengajuan/approve/'.$p['id']) ?>" method="POST" class="d-inline">
                                            <button type="submit" class="btn btn-xs btn-success text-white rounded-pill px-2.5 py-1 text-xs fw-semibold">
                                                <i class="fas fa-check me-1"></i> Setuju
                                            </button>
                                        </form>
                                        <form action="<?= base_url('direktur/karyawan/pengajuan/reject/'.$p['id']) ?>" method="POST" class="d-inline">
                                            <button type="submit" class="btn btn-xs btn-danger text-white rounded-pill px-2.5 py-1 text-xs fw-semibold">
                                                <i class="fas fa-times me-1"></i> Tolak
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <button type="button" onclick="confirmDeletePengajuan(<?= $p['id'] ?>, '<?= esc($p['judul_pengajuan'], 'js') ?>')" class="btn btn-xs btn-outline-danger rounded-pill px-2 py-1 text-xs fw-semibold" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
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
function confirmDeletePengajuan(id, judul) {
    Swal.fire({
        title: 'Hapus Permohonan?',
        text: `Apakah Anda yakin ingin menghapus data "${judul}"?`,
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
            form.action = '<?= base_url('direktur/karyawan/pengajuan/delete') ?>/' + id;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>

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
