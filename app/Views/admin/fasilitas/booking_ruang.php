<?php
$data = [
    'title'    => 'Booking Ruang Meeting',
    'subtitle' => 'Jadwal & Reservasi Ruang Rapat Kantor CDW Engineering',
    'active'   => 'booking-ruang',
    'user'     => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
];

$totalBooking = count($booking ?? []);
$disetujuiCount = 0;
$pendingCount = 0;
$totalPeserta = 0;

foreach ($booking as $b) {
    $st = strtolower($b['status'] ?? '');
    if ($st === 'disetujui') $disetujuiCount++;
    elseif ($st === 'pending') $pendingCount++;
    $totalPeserta += (int)($b['jumlah_peserta'] ?? 0);
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
        min-width: 860px !important;
        width: 100% !important;
        margin-bottom: 0 !important;
    }
</style>

<div class="container-fluid py-3 py-md-4">

    <!-- 1. Header Section -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-3 border border-light flex-wrap gap-3">
        <div class="d-flex align-items-center overflow-hidden">
            <div class="bg-primary text-white rounded-3 p-2 me-3 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 44px; height: 44px; background: linear-gradient(135deg, #2e7d32, #4caf50) !important;">
                <i class="fas fa-calendar-check fs-5"></i>
            </div>
            <div class="overflow-hidden">
                <h4 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 1.25rem;">Reservasi Ruang Rapat</h4>
                <small class="text-muted d-none d-sm-inline">Jadwal & Reservasi Ruang Rapat Kantor CDW Engineering.</small>
            </div>
        </div>
        <div>
            <button type="button" class="btn btn-success rounded-pill shadow-sm d-inline-flex align-items-center text-sm fw-semibold px-3" data-bs-toggle="modal" data-bs-target="#modalBookingRuang" style="background: #2e7d32; border-color: #2e7d32;">
                <i class="fas fa-plus me-1.5"></i> <span>Booking Ruang Meeting</span>
            </button>
        </div>
    </div>

    <!-- 2. KPI Statistik Cards -->
    <div class="row g-2 g-md-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card stat-card-fasilitas p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-door-open text-primary"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Total Booking</small>
                        <div class="stat-number-responsive text-primary text-truncate"><?= number_format($totalBooking) ?></div>
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
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Disetujui</small>
                        <div class="stat-number-responsive text-success text-truncate"><?= number_format($disetujuiCount) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card-fasilitas p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-hourglass-half text-warning"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Pending / Menunggu</small>
                        <div class="stat-number-responsive text-warning text-truncate"><?= number_format($pendingCount) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card-fasilitas p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-users text-info"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Total Peserta Rapat</small>
                        <div class="stat-number-responsive text-info text-truncate"><?= number_format($totalPeserta) ?> Orng</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Tabel Booking Ruang -->
    <div class="card fasilitas-card-modern p-3 p-md-4 mb-3">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-0 fs-6 fs-md-5 d-flex align-items-center">
                    <i class="fas fa-calendar-alt text-success me-2"></i> Jadwal Pemakaian Ruang Rapat
                </h5>
                <small class="text-muted text-xs d-block mt-0.5">Geser tabel kesamping untuk melihat rincian jam & peserta di mobile.</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <input type="text" id="searchTableBooking" class="form-control form-control-sm rounded-pill px-3" style="max-width: 220px;" placeholder="Cari ruangan / peminjam...">
                <button type="button" class="btn btn-sm btn-success rounded-pill px-3 fw-semibold text-nowrap shadow-sm" data-bs-toggle="modal" data-bs-target="#modalBookingRuang" style="background: #2e7d32; border-color: #2e7d32;">
                    <i class="fas fa-plus me-1"></i> Booking Ruang
                </button>
            </div>
        </div>

        <div class="table-scroll-wrapper">
            <table class="table table-hover align-middle text-sm mb-0" id="bookingTable">
                <thead class="table-light text-nowrap">
                    <tr>
                        <th width="20%">Ruangan</th>
                        <th width="18%">Waktu & Tanggal</th>
                        <th width="18%">Peminjam & Divisi</th>
                        <th width="20%">Agenda Meeting</th>
                        <th width="8%" class="text-center">Peserta</th>
                        <th width="8%">Status</th>
                        <th width="8%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($booking)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada reservasi ruang rapat terdata.</td></tr>
                    <?php else: ?>
                        <?php foreach($booking as $b): ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark"><i class="fas fa-door-closed me-1 text-success"></i><?= esc($b['nama_ruangan']) ?></div>
                            </td>
                            <td class="text-nowrap">
                                <div class="fw-bold text-dark"><i class="fas fa-calendar-day me-1 text-primary"></i><?= date('d M Y', strtotime($b['tanggal'])) ?></div>
                                <small class="text-muted text-xs"><i class="fas fa-clock me-1"></i><?= date('H:i', strtotime($b['jam_mulai'])) ?> - <?= date('H:i', strtotime($b['jam_selesai'])) ?> WIB</small>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= esc($b['peminjam']) ?></div>
                                <small class="text-muted text-xs"><i class="fas fa-sitemap me-1 text-info"></i><?= esc($b['divisi'] ?: 'General') ?></small>
                            </td>
                            <td>
                                <small class="text-dark d-block text-truncate" style="max-width: 220px;" title="<?= esc($b['agenda']) ?>">
                                    <?= esc($b['agenda']) ?>
                                </small>
                            </td>
                            <td class="text-center text-nowrap">
                                <span class="badge bg-secondary bg-opacity-10 text-dark border px-2.5 py-1 rounded-pill text-xs fw-semibold">
                                    <i class="fas fa-users me-1 text-primary"></i><?= (int)$b['jumlah_peserta'] ?> Orng
                                </span>
                            </td>
                            <td class="text-nowrap">
                                <?php
                                    $st = strtolower($b['status'] ?? 'disetujui');
                                    $badge = 'bg-success text-white';
                                    if ($st === 'pending') $badge = 'bg-warning text-dark';
                                    if ($st === 'ditolak') $badge = 'bg-danger text-white';
                                    if ($st === 'selesai') $badge = 'bg-secondary text-white';
                                ?>
                                <span class="badge <?= $badge ?> px-2.5 py-1 rounded-pill text-xs fw-semibold">
                                    <?= strtoupper(esc($b['status'])) ?>
                                </span>
                            </td>
                            <td class="text-center text-nowrap">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <a href="<?= base_url('admin/fasilitas/booking-ruang/detail/'.$b['id']) ?>" class="btn btn-xs btn-outline-info rounded-pill px-2.5 py-1 text-xs fw-semibold" title="Detail Booking">
                                        <i class="fas fa-eye me-1"></i> Detail
                                    </a>
                                    <a href="<?= base_url('admin/fasilitas/booking-ruang/edit/'.$b['id']) ?>" class="btn btn-xs btn-outline-warning rounded-pill px-2.5 py-1 text-xs fw-semibold" title="Edit Booking">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-1 text-xs fw-semibold" onclick="updateStatusBooking(<?= $b['id'] ?>, '<?= esc($b['status'], 'js') ?>')" title="Ubah Status">
                                        <i class="fas fa-sync-alt me-1"></i> Status
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2 py-1 text-xs fw-semibold" onclick="confirmDeleteBooking(<?= $b['id'] ?>, '<?= esc($b['nama_ruangan'], 'js') ?>')" title="Hapus Data">
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

<!-- Modal Booking Ruang Meeting -->
<div class="modal fade" id="modalBookingRuang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header text-white rounded-top-4 py-3 px-4" style="background: linear-gradient(135deg, #2e7d32, #4caf50);">
                <h5 class="modal-title fs-6 fw-bold mb-0"><i class="fas fa-calendar-plus me-2"></i> Form Reservasi Ruang Rapat</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/fasilitas/booking-ruang/simpan') ?>" method="POST" onsubmit="const btn = this.querySelector('button[type=submit]'); if(btn){ btn.disabled = true; btn.innerHTML = '<i class=\'fas fa-spinner fa-spin me-1\'></i> Menyimpan...'; }">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Pilih Ruangan Meeting *</label>
                        <select name="nama_ruangan" class="form-select rounded-3" required>
                            <option value="Ruang Rapat Utama (Lt 2)" selected>Ruang Rapat Utama (Lt 2 - Kapasitas 20 Orang)</option>
                            <option value="Ruang Diskusi Teknik (Lt 1)">Ruang Diskusi Teknik (Lt 1 - Kapasitas 8 Orang)</option>
                            <option value="Executive Boardroom (Lt 3)">Executive Boardroom (Lt 3 - Kapasitas 12 Orang)</option>
                            <option value="Ruang Meeting Hybrid / Zoom">Ruang Meeting Hybrid / Zoom Studio</option>
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-xs text-dark">Tanggal Meeting *</label>
                            <input type="date" class="form-control rounded-3" name="tanggal" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-xs text-dark">Jam Mulai *</label>
                            <input type="time" class="form-control rounded-3" name="jam_mulai" value="09:00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-xs text-dark">Jam Selesai *</label>
                            <input type="time" class="form-control rounded-3" name="jam_selesai" value="11:00" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold text-xs text-dark">Nama Peminjam / PIC *</label>
                            <input type="text" class="form-control rounded-3" name="peminjam" required placeholder="Cth: Budi Santoso">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-xs text-dark">Divisi / Unit Kerja *</label>
                            <input type="text" class="form-control rounded-3" name="divisi" required placeholder="Cth: Technical Engineering">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-xs text-dark">Jumlah Peserta</label>
                            <input type="number" class="form-control rounded-3" name="jumlah_peserta" value="6" min="1">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Agenda Meeting *</label>
                        <textarea class="form-control rounded-3" name="agenda" rows="3" required placeholder="Jelaskan topik rapat, pembahasan proyek, atau presentasi klien..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Status Reservasi</label>
                        <select name="status" class="form-select rounded-3">
                            <option value="Disetujui" selected>Disetujui</option>
                            <option value="Pending">Pending / Menunggu Konfirmasi</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4 py-2.5 px-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold shadow-sm" style="background: #2e7d32; border-color: #2e7d32;">
                        <i class="fas fa-save me-1.5"></i> Simpan Reservasi
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
        confirmButtonColor: '#2e7d32',
        timer: 3000,
        timerProgressBar: true,
        customClass: { popup: 'rounded-4 shadow-lg' }
    });
});
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchTableBooking');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#bookingTable tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }
});

function updateStatusBooking(id, currentStatus) {
    Swal.fire({
        title: 'Ubah Status Reservasi',
        input: 'select',
        inputOptions: {
            'Disetujui': 'Disetujui',
            'Pending': 'Pending / Menunggu Konfirmasi',
            'Selesai': 'Selesai',
            'Ditolak': 'Ditolak'
        },
        inputValue: currentStatus,
        showCancelButton: true,
        confirmButtonColor: '#2e7d32',
        confirmButtonText: 'Update Status',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-4 shadow-lg' }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= base_url('admin/fasilitas/booking-ruang/status') ?>';
            
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

function confirmDeleteBooking(id, nama) {
    Swal.fire({
        title: 'Hapus Reservasi Ruangan?',
        text: 'Jadwal reservasi "' + nama + '" akan dihapus permanen.',
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
            form.action = '<?= base_url('admin/fasilitas/booking-ruang/delete') ?>/' + id;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>

<?= view('admin/templates/footer', $data) ?>
