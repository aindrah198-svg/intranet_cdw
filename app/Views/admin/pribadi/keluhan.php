<?php
$data = [
    'title'       => 'Keluhan Saya',
    'subtitle'    => 'Penyampaian Keluhan, Aspirasi, & Kendala Kerja Admin',
    'active'      => 'keluhan-saya',
    'user'        => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
];

$totalKeluhan = count($keluhanList ?? []);
$waitingKeluhan = 0;
$resolvedKeluhan = 0;
foreach($keluhanList as $k) {
    if (strtolower($k['status'] ?? '') === 'menunggu') $waitingKeluhan++;
    if (strtolower($k['status'] ?? '') === 'selesai' || strtolower($k['status'] ?? '') === 'diproses') $resolvedKeluhan++;
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
    .keluhan-card-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(30, 60, 114, 0.06), 0 2px 6px rgba(0, 0, 0, 0.02) !important;
        transition: all 0.3s ease;
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
        min-width: 820px !important;
        width: 100% !important;
        margin-bottom: 0 !important;
    }
</style>

<div class="container-fluid py-3 py-md-4">

    <!-- 1. Header Section -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-3 border border-light flex-wrap gap-3">
        <div class="d-flex align-items-center overflow-hidden">
            <div class="bg-danger text-white rounded-3 p-2 me-3 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 44px; height: 44px; background: linear-gradient(135deg, #d32f2f, #b71c1c) !important;">
                <i class="fas fa-comment-dots fs-5"></i>
            </div>
            <div class="overflow-hidden">
                <h4 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 1.25rem;">Keluhan Saya & Aspirasi Kerja</h4>
                <small class="text-muted d-none d-sm-inline">Formulir & riwayat penyampaian keluhan fasilitas, beban kerja, atau lingkungan kerja.</small>
            </div>
        </div>
        <div>
            <button type="button" data-bs-toggle="modal" data-bs-target="#modalTambahKeluhan" class="btn btn-danger text-white rounded-pill shadow-sm d-inline-flex align-items-center text-sm fw-semibold px-3" style="background: #d32f2f; border-color: #d32f2f;">
                <i class="fas fa-plus me-1.5"></i> <span>Kirim Keluhan Baru</span>
            </button>
        </div>
    </div>

    <!-- 2. Ringkasan Stats -->
    <div class="row g-2 g-md-3 mb-3">
        <div class="col-6 col-md-4">
            <div class="card p-3 rounded-4 border-0 shadow-sm bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-danger bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-comment-alt text-danger"></i>
                    </div>
                    <div>
                        <small class="text-muted text-xs uppercase fw-bold d-block">Total Keluhan</small>
                        <div class="fs-4 fw-bold text-danger"><?= number_format($totalKeluhan) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card p-3 rounded-4 border-0 shadow-sm bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-clock text-warning"></i>
                    </div>
                    <div>
                        <small class="text-muted text-xs uppercase fw-bold d-block">Menunggu Tanggapan</small>
                        <div class="fs-4 fw-bold text-warning"><?= number_format($waitingKeluhan) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card p-3 rounded-4 border-0 shadow-sm bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-check-double text-success"></i>
                    </div>
                    <div>
                        <small class="text-muted text-xs uppercase fw-bold d-block">Ditanggapi / Diproses</small>
                        <div class="fs-4 fw-bold text-success"><?= number_format($resolvedKeluhan) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Tabel Keluhan Saya -->
    <div class="card keluhan-card-modern p-3 p-md-4 mb-3">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-0 fs-6 fs-md-5 d-flex align-items-center">
                    <i class="fas fa-list me-2 text-danger"></i> Daftar Keluhan Yang Pernah Dikirim
                </h5>
                <small class="text-muted text-xs d-block mt-0.5">Keluhan ini terhubung langsung ke Dashboard Direktur untuk penanganan.</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <input type="text" id="searchTableKeluhan" class="form-control form-control-sm rounded-pill px-3" style="max-width: 220px;" placeholder="Cari judul / kategori...">
            </div>
        </div>

        <div class="table-scroll-wrapper">
            <table class="table table-hover align-middle text-sm mb-0" id="keluhanTable">
                <thead class="table-light text-nowrap">
                    <tr>
                        <th width="15%">Tanggal</th>
                        <th width="18%">Kategori</th>
                        <th width="25%">Judul & Deskripsi</th>
                        <th width="15%">Status</th>
                        <th width="15%">Tanggapan Direktur</th>
                        <th width="12%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($keluhanList)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada keluhan yang dikirim.</td></tr>
                    <?php else: ?>
                        <?php foreach($keluhanList as $k): ?>
                        <tr>
                            <td class="text-nowrap">
                                <small class="text-dark fw-bold d-block"><i class="fas fa-calendar me-1 text-muted"></i><?= date('d M Y', strtotime($k['tanggal'])) ?></small>
                            </td>
                            <td class="text-nowrap">
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-0.5 rounded-pill text-xs fw-semibold">
                                    <?= esc($k['kategori']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark text-truncate" style="max-width: 240px;"><?= esc($k['judul']) ?></div>
                                <small class="text-muted d-block text-truncate" style="max-width: 240px;"><?= esc($k['deskripsi']) ?></small>
                            </td>
                            <td class="text-nowrap">
                                <?php
                                    $st = strtolower($k['status'] ?? 'menunggu');
                                    $badge = 'bg-warning text-dark';
                                    if ($st === 'diproses') $badge = 'bg-info text-white';
                                    if ($st === 'selesai') $badge = 'bg-success text-white';
                                    if ($st === 'ditolak') $badge = 'bg-danger text-white';
                                ?>
                                <span class="badge <?= $badge ?> px-2.5 py-1 rounded-pill text-xs fw-semibold">
                                    <?= strtoupper(esc($k['status'] ?? 'Menunggu')) ?>
                                </span>
                            </td>
                            <td>
                                <small class="text-dark d-block text-truncate" style="max-width: 180px;" title="<?= esc($k['tanggapan']) ?>">
                                    <?= esc($k['tanggapan'] ?: 'Belum ditanggapi') ?>
                                </small>
                            </td>
                            <td class="text-center text-nowrap">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <a href="<?= base_url('admin/keluhan-saya/detail/'.$k['id']) ?>" class="btn btn-xs btn-outline-info rounded-pill px-2 py-1 text-xs fw-semibold">
                                        <i class="fas fa-eye me-1"></i> Detail
                                    </a>
                                    <a href="<?= base_url('admin/keluhan-saya/edit/'.$k['id']) ?>" class="btn btn-xs btn-outline-warning rounded-pill px-2 py-1 text-xs fw-semibold">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>
                                    <button type="button" onclick="confirmDeleteKeluhan(<?= $k['id'] ?>, '<?= esc($k['judul'], 'js') ?>')" class="btn btn-xs btn-outline-danger rounded-pill px-2 py-1 text-xs fw-semibold">
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

<!-- Modal Tambah Keluhan -->
<div class="modal fade" id="modalTambahKeluhan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header text-white rounded-top-4" style="background: linear-gradient(135deg, #d32f2f, #b71c1c);">
                <h5 class="modal-title fs-6 fw-bold"><i class="fas fa-plus-circle me-2"></i> Form Penyampaian Keluhan Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/keluhan-saya/store') ?>" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Kategori Keluhan *</label>
                        <select name="kategori" class="form-select rounded-3" required>
                            <option value="Fasilitas">Fasilitas / Perangkat Kerja</option>
                            <option value="Lingkungan Kerja">Lingkungan Kerja / Kenyamanan</option>
                            <option value="Beban Kerja">Beban Kerja / Penugasan</option>
                            <option value="Hubungan Rekan Kerja">Hubungan Rekan Kerja</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Judul Keluhan / Kendala *</label>
                        <input type="text" name="judul" class="form-control rounded-3" required placeholder="Cth: Perbaikan Printer Kantor / AC Ruangan">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Rincian Deskripsi Keluhan *</label>
                        <textarea name="deskripsi" class="form-control rounded-3" rows="4" required placeholder="Tuliskan secara jelas kendala atau aspirasi yang ingin disampaikan ke manajemen..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4 px-4 py-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger text-white rounded-pill px-4 shadow-sm" style="background: #d32f2f; border-color: #d32f2f;">
                        <i class="fas fa-paper-plane me-1.5"></i> Kirim Keluhan
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
        confirmButtonColor: '#d32f2f',
        timer: 3000,
        timerProgressBar: true,
        customClass: { popup: 'rounded-4 shadow-lg' }
    });
});
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchTableKeluhan');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#keluhanTable tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }
});

function confirmDeleteKeluhan(id, judul) {
    Swal.fire({
        title: 'Hapus Keluhan?',
        text: 'Keluhan "' + judul + '" akan dihapus.',
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
            form.action = '<?= base_url('admin/keluhan-saya/delete') ?>/' + id;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>

<?= view('admin/templates/footer', $data) ?>
