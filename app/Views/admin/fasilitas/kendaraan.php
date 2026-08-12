<?php
$data = [
    'title'     => 'Koordinasi Kendaraan Dinas',
    'subtitle'  => 'Kelola Penggunaan Mobil Dinas & Logistik CDW Engineering',
    'active'    => 'kendaraan',
    'user'      => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
];

$totalKendaraan = count($kendaraan ?? []);
$berjalanCount = 0;
$disetujuiCount = 0;
$pendingCount = 0;

foreach ($kendaraan as $k) {
    $st = strtolower($k['status'] ?? '');
    if ($st === 'sedang berjalan') $berjalanCount++;
    elseif ($st === 'disetujui') $disetujuiCount++;
    elseif ($st === 'pending') $pendingCount++;
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
    .fasilitas-card-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(30, 60, 114, 0.06), 0 2px 6px rgba(0, 0, 0, 0.02) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .fasilitas-card-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 32px rgba(30, 60, 114, 0.12) !important;
    }
    .stat-card-fasilitas {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(30, 60, 114, 0.05);
        transition: transform 0.2s ease;
    }
    .stat-card-fasilitas:hover {
        transform: translateY(-2px);
    }
    .stat-number-responsive {
        font-size: clamp(1.1rem, 2.5vw, 1.4rem);
        font-weight: 700;
        line-height: 1.2;
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
        min-width: 880px !important;
        width: 100% !important;
        margin-bottom: 0 !important;
    }
</style>

<div class="container-fluid py-3 py-md-4">

    <!-- 1. Header Section -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-3 border border-light flex-wrap gap-3">
        <div class="d-flex align-items-center overflow-hidden">
            <div class="bg-primary text-white rounded-3 p-2 me-3 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 44px; height: 44px; background: linear-gradient(135deg, #1565c0, #1e88e5) !important;">
                <i class="fas fa-car fs-5"></i>
            </div>
            <div class="overflow-hidden">
                <h4 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 1.25rem;">Jadwal Kendaraan Operasional</h4>
                <small class="text-muted d-none d-sm-inline">Koordinasi Penggunaan Mobil Dinas & Logistik CDW Engineering.</small>
            </div>
        </div>
        <div>
            <button type="button" class="btn btn-primary rounded-pill shadow-sm d-inline-flex align-items-center text-sm fw-semibold px-3" data-bs-toggle="modal" data-bs-target="#modalKendaraan" style="background: #1565c0; border-color: #1565c0;">
                <i class="fas fa-plus me-1.5"></i> <span>Pengajuan Kendaraan</span>
            </button>
        </div>
    </div>

    <!-- 2. KPI Statistik Cards -->
    <div class="row g-2 g-md-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card stat-card-fasilitas p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-car-side text-primary"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Total Armada / Jadwal</small>
                        <div class="stat-number-responsive text-primary text-truncate"><?= number_format($totalKendaraan) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card-fasilitas p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-route text-info"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Sedang Berjalan</small>
                        <div class="stat-number-responsive text-info text-truncate"><?= number_format($berjalanCount) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card-fasilitas p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Disetujui / Ready</small>
                        <div class="stat-number-responsive text-success text-truncate"><?= number_format($disetujuiCount) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card-fasilitas p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-clock text-warning"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Pending Konfirmasi</small>
                        <div class="stat-number-responsive text-warning text-truncate"><?= number_format($pendingCount) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Tabel Koordinasi Kendaraan -->
    <div class="card fasilitas-card-modern p-3 p-md-4 mb-3">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-0 fs-6 fs-md-5 d-flex align-items-center">
                    <i class="fas fa-shuttle-van text-primary me-2"></i> Log Penugasan Mobil Dinas & Transportasi
                </h5>
                <small class="text-muted text-xs d-block mt-0.5">Geser tabel kesamping untuk melihat rincian driver & lokasi di mobile.</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <input type="text" id="searchTableKendaraan" class="form-control form-control-sm rounded-pill px-3" style="max-width: 220px;" placeholder="Cari unit / driver / tujuan...">
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold text-nowrap shadow-sm" data-bs-toggle="modal" data-bs-target="#modalKendaraan" style="background: #1565c0; border-color: #1565c0;">
                    <i class="fas fa-plus me-1"></i> Pengajuan
                </button>
            </div>
        </div>

        <div class="table-scroll-wrapper">
            <table class="table table-hover align-middle text-sm mb-0" id="kendaraanTable">
                <thead class="table-light text-nowrap">
                    <tr>
                        <th width="20%">Unit Kendaraan</th>
                        <th width="16%">Driver / Pengemudi</th>
                        <th width="22%">Pengguna & Tujuan</th>
                        <th width="18%">Waktu Operasional</th>
                        <th width="12%">Status</th>
                        <th width="12%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($kendaraan)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pengajuan operasional kendaraan terdata.</td></tr>
                    <?php else: ?>
                        <?php foreach($kendaraan as $k): ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark"><i class="fas fa-car me-1 text-primary"></i><?= esc($k['nama_kendaraan']) ?></div>
                                <span class="badge bg-secondary bg-opacity-10 text-dark border px-2 py-0.5 rounded-pill text-xs fw-semibold">
                                    <i class="fas fa-id-card me-1"></i><?= esc($k['plat_nomor'] ?: '-') ?>
                                </span>
                            </td>
                            <td class="fw-semibold text-dark text-nowrap">
                                <i class="fas fa-user-tie text-info me-1"></i><?= esc($k['driver'] ?: 'Tanpa Driver') ?>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><i class="fas fa-users me-1 text-success"></i><?= esc($k['pengguna']) ?></div>
                                <small class="text-muted text-xs d-block text-truncate" style="max-width: 240px;" title="<?= esc($k['tujuan']) ?>">
                                    <i class="fas fa-map-marker-alt me-1 text-danger"></i><?= esc($k['tujuan']) ?>
                                </small>
                            </td>
                            <td class="text-nowrap">
                                <div class="fw-bold text-dark"><i class="fas fa-calendar-alt me-1 text-primary"></i><?= !empty($k['tanggal_mulai']) ? date('d M Y', strtotime($k['tanggal_mulai'])) : '-' ?></div>
                                <small class="text-muted text-xs">
                                    <i class="fas fa-clock me-1"></i><?= !empty($k['tanggal_mulai']) ? date('H:i', strtotime($k['tanggal_mulai'])) : '' ?> - <?= !empty($k['tanggal_selesai']) ? date('H:i', strtotime($k['tanggal_selesai'])) : '' ?> WIB
                                </small>
                            </td>
                            <td class="text-nowrap">
                                <?php
                                    $st = strtolower($k['status'] ?? 'sedang berjalan');
                                    $badge = 'bg-primary text-white';
                                    if ($st === 'disetujui') $badge = 'bg-success text-white';
                                    if ($st === 'pending') $badge = 'bg-warning text-dark';
                                    if ($st === 'selesai') $badge = 'bg-secondary text-white';
                                    if ($st === 'ditolak') $badge = 'bg-danger text-white';
                                ?>
                                <span class="badge <?= $badge ?> px-2.5 py-1 rounded-pill text-xs fw-semibold">
                                    <?= strtoupper(esc($k['status'])) ?>
                                </span>
                            </td>
                            <td class="text-center text-nowrap">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <a href="<?= base_url('admin/fasilitas/kendaraan/detail/'.$k['id']) ?>" class="btn btn-xs btn-outline-info rounded-pill px-2.5 py-1 text-xs fw-semibold" title="Detail Kendaraan">
                                        <i class="fas fa-eye me-1"></i> Detail
                                    </a>
                                    <a href="<?= base_url('admin/fasilitas/kendaraan/edit/'.$k['id']) ?>" class="btn btn-xs btn-outline-warning rounded-pill px-2.5 py-1 text-xs fw-semibold" title="Edit Kendaraan">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1 text-xs fw-semibold" onclick="updateStatusKendaraan(<?= $k['id'] ?>, '<?= esc($k['status'], 'js') ?>')" title="Ubah Status">
                                        <i class="fas fa-sync-alt me-1"></i> Status
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2 py-1 text-xs fw-semibold" onclick="confirmDeleteKendaraan(<?= $k['id'] ?>, '<?= esc($k['nama_kendaraan'], 'js') ?>')" title="Hapus Data">
                                        <i class="fas fa-trash me-1"></i>
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

<!-- Modal Pengajuan Kendaraan -->
<div class="modal fade" id="modalKendaraan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header text-white rounded-top-4 py-3 px-4" style="background: linear-gradient(135deg, #1565c0, #1e88e5);">
                <h5 class="modal-title fs-6 fw-bold mb-0"><i class="fas fa-car-side me-2"></i> Form Pengajuan Operational Kendaraan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/fasilitas/kendaraan/simpan') ?>" method="POST" onsubmit="const btn = this.querySelector('button[type=submit]'); if(btn){ btn.disabled = true; btn.innerHTML = '<i class=\'fas fa-spinner fa-spin me-1\'></i> Menyimpan...'; }">
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold text-xs text-dark">Nama / Jenis Kendaraan *</label>
                            <input type="text" class="form-control rounded-3" name="nama_kendaraan" required placeholder="Cth: Toyota Avanza Veloz / Triton Double Cab">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold text-xs text-dark">Plat Nomor Kendaraan</label>
                            <input type="text" class="form-control rounded-3" name="plat_nomor" placeholder="Cth: B 1234 CDW">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-xs text-dark">Pengemudi / Driver</label>
                            <input type="text" class="form-control rounded-3" name="driver" placeholder="Cth: Pak Joko / Pak Hendra (Driver Operational)">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-xs text-dark">Pengguna / Tim Pemakai *</label>
                            <input type="text" class="form-control rounded-3" name="pengguna" required placeholder="Cth: Tim Teknisi Lapangan / Direksi">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Tujuan & Lokasi Kunjungan *</label>
                        <input type="text" class="form-control rounded-3" name="tujuan" required placeholder="Cth: Kunjungan Site Installation - Karawang Plant">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-xs text-dark">Waktu Keberangkatan *</label>
                            <input type="datetime-local" class="form-control rounded-3" name="tanggal_mulai" value="<?= date('Y-m-d\TH:i') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-xs text-dark">Estimasi Kepulangan *</label>
                            <input type="datetime-local" class="form-control rounded-3" name="tanggal_selesai" value="<?= date('Y-m-d\T17:00') ?>" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-xs text-dark">Status Operasional</label>
                            <select name="status" class="form-select rounded-3">
                                <option value="Sedang Berjalan" selected>Sedang Berjalan</option>
                                <option value="Disetujui">Disetujui / Ready</option>
                                <option value="Pending">Pending / Menunggu</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-xs text-dark">Catatan / Barang Bawaan</label>
                            <input type="text" class="form-control rounded-3" name="catatan" placeholder="Cth: Membawa perlengkapan instrumen ukur & K3">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4 py-2.5 px-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm" style="background: #1565c0; border-color: #1565c0;">
                        <i class="fas fa-save me-1.5"></i> Simpan Jadwal Operasional
                    </button>
                </div>
            </form>
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
        confirmButtonColor: '#1565c0',
        timer: 3000,
        timerProgressBar: true,
        customClass: { popup: 'rounded-4 shadow-lg' }
    });
});
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchTableKendaraan');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#kendaraanTable tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }
});

function updateStatusKendaraan(id, currentStatus) {
    Swal.fire({
        title: 'Ubah Status Operasional Kendaraan',
        input: 'select',
        inputOptions: {
            'Sedang Berjalan': 'Sedang Berjalan',
            'Disetujui': 'Disetujui / Ready',
            'Pending': 'Pending / Menunggu',
            'Selesai': 'Selesai',
            'Ditolak': 'Ditolak'
        },
        inputValue: currentStatus,
        showCancelButton: true,
        confirmButtonColor: '#1565c0',
        confirmButtonText: 'Update Status',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-4 shadow-lg' }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= base_url('admin/fasilitas/kendaraan/status') ?>';
            
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'id';
            idInput.value = id;
            form.appendChild(idInput);

            const stInput = document.createElement('input');
            stInput.type = 'hidden';
            stInput.name = 'status';
            stInput.value = result.value;
            form.appendChild(stInput);

            document.body.appendChild(form);
            form.submit();
        }
    });
}

function confirmDeleteKendaraan(id, nama) {
    Swal.fire({
        title: 'Hapus Jadwal Kendaraan?',
        text: 'Pengajuan kendaraan "' + nama + '" akan dihapus permanen.',
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
            form.action = '<?= base_url('admin/fasilitas/kendaraan/delete') ?>/' + id;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>

<?= view('admin/templates/footer', $data) ?>
