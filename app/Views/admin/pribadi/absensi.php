<?php
$data = [
    'title'    => 'Absensi Saya',
    'subtitle' => 'Pencatatan Kehadiran Mandiri Administrator',
    'active'   => 'absensi-saya',
    'user'     => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
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
                    <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>" class="text-decoration-none text-muted">Menu Pribadi</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Absensi</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-fingerprint text-primary me-2"></i> Presensi & Absensi Mandiri</h4>
            <small class="text-muted">Pencatatan kehadiran harian karyawan/admin yang terhubung secara otomatis ke sistem Monitoring Direktur.</small>
        </div>
    </div>

    <div class="row g-4">
        <!-- Card Presensi Hari Ini -->
        <div class="col-lg-5 col-xl-4">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden h-100">
                <div class="card-header text-white text-center py-3.5 px-4" style="background: linear-gradient(135deg, #1e3c72, #2a5298);">
                    <h5 class="fs-6 fw-bold mb-0"><i class="fas fa-clock me-2"></i> Presensi Hari Ini</h5>
                    <small class="text-white-50"><?= date('l, d F Y') ?></small>
                </div>
                <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                    <div class="mb-3">
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1.5 rounded-pill font-semibold text-xs mb-2">
                            <i class="fas fa-user me-1"></i> <?= esc($currentKaryawan['nama_lengkap'] ?? session()->get('name') ?? 'Admin Staff') ?>
                        </span>
                        <div class="text-xs text-muted"><?= esc($currentKaryawan['divisi'] ?? 'Administrasi') ?> &bull; <?= esc($currentKaryawan['jabatan'] ?? 'Staff') ?></div>
                    </div>

                    <!-- Jam Digital Live -->
                    <div class="p-3 rounded-4 bg-light border mb-4 shadow-inner">
                        <h2 class="fw-bold text-dark mb-0 font-monospace" id="liveClock" style="font-size: 2.4rem; letter-spacing: 2px;">
                            <?= date('H:i:s') ?> <span class="fs-6 fw-normal text-muted">WIB</span>
                        </h2>
                        <small class="text-muted text-xs"><i class="fas fa-globe-asia me-1 text-success"></i> Waktu Server CDW</small>
                    </div>

                    <!-- Status Presensi Hari Ini -->
                    <div class="mb-4">
                        <?php if (empty($todayAbsen)): ?>
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-2 rounded-pill font-semibold text-xs">
                                <i class="fas fa-exclamation-circle me-1"></i> Belum Melakukan Absen Masuk
                            </span>
                        <?php elseif (empty($todayAbsen['waktu_pulang'])): ?>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill font-semibold text-xs">
                                <i class="fas fa-check-circle me-1"></i> Sudah Absen Masuk (Jam <?= date('H:i', strtotime($todayAbsen['waktu_masuk'])) ?> WIB)
                            </span>
                        <?php else: ?>
                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-2 rounded-pill font-semibold text-xs">
                                <i class="fas fa-user-check me-1"></i> Presensi Selesai (Pulang: <?= date('H:i', strtotime($todayAbsen['waktu_pulang'])) ?> WIB)
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Tombol Aksi Checkin / Checkout -->
                    <div class="d-grid gap-2">
                        <?php if (empty($todayAbsen)): ?>
                            <form action="<?= base_url('admin/absensi-saya/checkin') ?>" method="POST" onsubmit="const btn = this.querySelector('button'); if(btn){ btn.disabled = true; btn.innerHTML = '<i class=\'fas fa-spinner fa-spin me-1\'></i> Mencatat...'; }">
                                <button type="submit" class="btn btn-success rounded-pill py-2.5 fw-bold w-100 shadow-sm text-sm">
                                    <i class="fas fa-sign-in-alt me-2"></i> Absen Masuk (Check-In)
                                </button>
                            </form>
                            <button type="button" class="btn btn-secondary rounded-pill py-2.5 fw-bold w-100 text-sm opacity-50" disabled>
                                <i class="fas fa-sign-out-alt me-2"></i> Absen Pulang (Check-Out)
                            </button>
                        <?php elseif (empty($todayAbsen['waktu_pulang'])): ?>
                            <button type="button" class="btn btn-success rounded-pill py-2.5 fw-bold w-100 text-sm opacity-50" disabled>
                                <i class="fas fa-check me-2"></i> Sudah Check-In (<?= date('H:i', strtotime($todayAbsen['waktu_masuk'])) ?>)
                            </button>
                            <form action="<?= base_url('admin/absensi-saya/checkout') ?>" method="POST" onsubmit="const btn = this.querySelector('button'); if(btn){ btn.disabled = true; btn.innerHTML = '<i class=\'fas fa-spinner fa-spin me-1\'></i> Mencatat...'; }">
                                <button type="submit" class="btn btn-danger rounded-pill py-2.5 fw-bold w-100 shadow-sm text-sm">
                                    <i class="fas fa-sign-out-alt me-2"></i> Absen Pulang (Check-Out)
                                </button>
                            </form>
                        <?php else: ?>
                            <button type="button" class="btn btn-outline-success rounded-pill py-2.5 fw-bold w-100 text-sm" disabled>
                                <i class="fas fa-check-double me-2"></i> Presensi Selesai Hari Ini
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Riwayat Absensi -->
        <div class="col-lg-7 col-xl-8">
            <div class="card border-0 rounded-4 shadow-sm overflow-hidden h-100">
                <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2 border-bottom border-light">
                    <h5 class="card-title fs-6 fw-bold mb-0 text-dark"><i class="fas fa-history me-2 text-primary"></i> Riwayat Absensi 30 Hari Terakhir</h5>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1.5 rounded-pill font-semibold">
                        Terhubung ke Direktur
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-sm">
                            <thead class="table-light text-uppercase text-xs text-muted">
                                <tr>
                                    <th class="ps-4 py-3">Tanggal</th>
                                    <th class="py-3 text-center">Jam Masuk</th>
                                    <th class="py-3 text-center">Jam Pulang</th>
                                    <th class="py-3">Lokasi / Keterangan</th>
                                    <th class="pe-4 py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($riwayatAbsensi)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fas fa-calendar-times fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                                        Belum ada riwayat pencatatan absensi.
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach($riwayatAbsensi as $r): 
                                    $st = strtolower($r['status'] ?? 'hadir');
                                    $badgeClass = 'bg-success text-white';
                                    if ($st === 'terlambat') $badgeClass = 'bg-warning text-dark';
                                    if ($st === 'izin' || $st === 'sakit') $badgeClass = 'bg-info text-white';
                                    if ($st === 'alpha') $badgeClass = 'bg-danger text-white';
                                ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">
                                        <?= date('d/m/Y', strtotime($r['tanggal'])) ?>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <span class="badge bg-light text-dark border px-2.5 py-1 text-xs">
                                            <i class="far fa-clock text-primary me-1"></i>
                                            <?= !empty($r['waktu_masuk']) ? date('H:i', strtotime($r['waktu_masuk'])) . ' WIB' : '-' ?>
                                        </span>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <span class="badge bg-light text-dark border px-2.5 py-1 text-xs">
                                            <i class="far fa-clock text-danger me-1"></i>
                                            <?= !empty($r['waktu_pulang']) ? date('H:i', strtotime($r['waktu_pulang'])) . ' WIB' : '-' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-xs text-dark text-truncate" style="max-width: 200px;" title="<?= esc($r['lokasi_masuk'] ?: $r['keterangan']) ?>">
                                            <?= esc($r['lokasi_masuk'] ?: 'Kantor CDW Engineering') ?>
                                        </div>
                                    </td>
                                    <td class="pe-4 text-center">
                                        <span class="badge <?= $badgeClass ?> px-3 py-1 rounded-pill text-xs fw-semibold">
                                            <?= strtoupper(esc($r['status'] ?? 'Hadir')) ?>
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
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function updateClock() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    const clockEl = document.getElementById('liveClock');
    if (clockEl) {
        clockEl.innerHTML = `${hours}:${minutes}:${seconds} <span class="fs-6 fw-normal text-muted">WIB</span>`;
    }
}
setInterval(updateClock, 1000);
</script>

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

<?= view('admin/templates/footer', $data) ?>
