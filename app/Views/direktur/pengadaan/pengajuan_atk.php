<?php
$title = $title ?? 'Pengajuan ATK';
$templateData = [
    'title' => $title,
    'user'  => session()->get('user') ?? ['name' => 'Direktur', 'role' => 'direktur'],
    'active' => 'pengadaan'
];

$totalPengajuan = count($pengajuan ?? []);
$menungguCount = 0;
$disetujuiCount = 0;
$ditolakCount = 0;

foreach($pengajuan as $item) {
    $st = strtolower($item['status'] ?? '');
    if($st === 'menunggu') $menungguCount++;
    elseif($st === 'disetujui') $disetujuiCount++;
    elseif($st === 'ditolak') $ditolakCount++;
}
?>

<?= view('direktur/templates/header', $templateData) ?>
<?= view('direktur/templates/sidebar', $templateData) ?>
<?= view('direktur/templates/navbar', $templateData) ?>

<style>
    /* Prevent Any Horizontal Page Overflow */
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

    /* Glassmorphism & Modern Card Styling */
    .pengadaan-card-modern {
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

    .pengadaan-card-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 32px rgba(30, 60, 114, 0.12) !important;
    }

    .stat-card-pengadaan {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(30, 60, 114, 0.05);
        transition: transform 0.2s ease;
    }

    .stat-card-pengadaan:hover {
        transform: translateY(-2px);
    }

    .stat-number-responsive {
        font-size: clamp(1.1rem, 2.5vw, 1.4rem);
        font-weight: 700;
        line-height: 1.2;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 5px 12px;
        border-radius: 30px;
        font-size: 0.76rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    
    .status-pill-menunggu {
        background: rgba(255, 193, 7, 0.15);
        color: #b58100;
        border: 1px solid rgba(255, 193, 7, 0.3);
    }

    .status-pill-disetujui {
        background: rgba(25, 135, 84, 0.12);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.25);
    }

    .status-pill-ditolak {
        background: rgba(220, 53, 69, 0.12);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.25);
    }

    /* Inner Table Scroll Container - Keeps Page Fit 100% */
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
        min-width: 750px !important;
        width: 100% !important;
        margin-bottom: 0 !important;
    }

    .table-scroll-wrapper::-webkit-scrollbar {
        height: 6px;
    }
    .table-scroll-wrapper::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    .table-scroll-wrapper::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    @media (max-width: 767.98px) {
        .header-mobile-flex {
            flex-direction: column;
            align-items: stretch !important;
        }
        .header-btn-group {
            width: 100%;
        }
        .header-btn-group .btn {
            width: 100%;
            justify-content: center;
            font-size: 0.85rem;
            padding: 9px 12px;
        }
        .stat-card-pengadaan {
            padding: 12px !important;
        }
        .stat-icon-wrapper {
            width: 40px !important;
            height: 40px !important;
            min-width: 40px;
        }
    }
</style>

<div class="container-fluid py-3 py-md-4">

    <!-- 1. Header Section -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-3 border border-light header-mobile-flex gap-3">
        <div class="d-flex align-items-center overflow-hidden">
            <div class="bg-gradient-primary text-white rounded-3 p-2 me-2.5 me-md-3 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 44px; height: 44px;">
                <i class="fas fa-pen-nib fs-5"></i>
            </div>
            <div class="overflow-hidden">
                <h4 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 1.25rem;">Pengajuan ATK Karyawan</h4>
                <small class="text-muted d-none d-sm-inline">Kelola dan tinjau persetujuan pengajuan Alat Tulis Kantor karyawan CDW.</small>
            </div>
        </div>
        <div class="header-btn-group">
            <button class="btn btn-primary rounded-pill shadow-sm d-inline-flex align-items-center fw-semibold" data-bs-toggle="modal" data-bs-target="#tambahAtkModal">
                <i class="fas fa-plus me-1.5"></i> <span>Buat Pengajuan ATK</span>
            </button>
        </div>
    </div>

    <!-- Alert Flashdata -->
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-3" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- 2. KPI Statistik Cards -->
    <div class="row g-2 g-md-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card stat-card-pengadaan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center stat-icon-wrapper" style="width: 44px; height: 44px;">
                        <i class="fas fa-boxes text-primary"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Total Pengajuan</small>
                        <div class="stat-number-responsive text-primary text-truncate"><?= number_format($totalPengajuan) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card-pengadaan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center stat-icon-wrapper" style="width: 44px; height: 44px;">
                        <i class="fas fa-clock text-warning"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Menunggu Review</small>
                        <div class="stat-number-responsive text-warning text-truncate"><?= number_format($menungguCount) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card-pengadaan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center stat-icon-wrapper" style="width: 44px; height: 44px;">
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
            <div class="card stat-card-pengadaan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-danger bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center stat-icon-wrapper" style="width: 44px; height: 44px;">
                        <i class="fas fa-times-circle text-danger"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Ditolak</small>
                        <div class="stat-number-responsive text-danger text-truncate"><?= number_format($ditolakCount) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Tabel Data Pengajuan ATK -->
    <div class="card pengadaan-card-modern p-3 p-md-4 mb-3">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-0 fs-6 fs-md-5 d-flex align-items-center">
                    <i class="fas fa-list-alt text-primary me-2"></i> Daftar Pengajuan ATK Karyawan
                </h5>
                <small class="text-muted text-xs d-block mt-0.5">Geser tabel kesamping untuk melihat rincian pengajuan di layar mobile.</small>
            </div>
            <input type="text" id="searchTable" class="form-control form-control-sm rounded-pill px-3" style="max-width: 200px;" placeholder="Cari barang / pemohon...">
        </div>

        <div class="table-scroll-wrapper">
            <table class="table table-hover align-middle text-sm mb-0" id="atkTable">
                <thead class="table-light text-nowrap">
                    <tr>
                        <th width="22%">Pemohon</th>
                        <th width="22%">Nama Barang ATK</th>
                        <th width="15%">Jumlah & Satuan</th>
                        <th width="23%">Alasan Kebutuhan</th>
                        <th width="10%">Status</th>
                        <th width="8%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($pengajuan)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pengajuan ATK terdaftar.</td></tr>
                    <?php else: ?>
                        <?php foreach($pengajuan as $p): ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark"><?= esc($p['nama_lengkap'] ?? 'Direktur') ?></div>
                                <small class="text-muted text-xs"><?= esc($p['jabatan'] ?? '-') ?></small>
                            </td>
                            <td class="fw-semibold text-dark text-nowrap"><?= esc($p['nama_barang']) ?></td>
                            <td class="fw-bold text-primary text-nowrap"><?= esc($p['jumlah']) ?> <?= esc($p['satuan']) ?></td>
                            <td>
                                <small class="text-muted d-inline-block text-truncate" style="max-width:220px;"><?= esc($p['alasan'] ?: '-') ?></small>
                            </td>
                            <td>
                                <?php
                                    $st = strtolower($p['status']);
                                    $pillClass = 'status-pill-menunggu';
                                    if ($st === 'disetujui') $pillClass = 'status-pill-disetujui';
                                    if ($st === 'ditolak') $pillClass = 'status-pill-ditolak';
                                ?>
                                <span class="status-pill <?= $pillClass ?>">
                                    <i class="fas fa-circle fs-6 me-1.5" style="font-size: 0.4rem !important;"></i>
                                    <?= ucfirst(esc($p['status'])) ?>
                                </span>
                            </td>
                            <td class="text-center text-nowrap">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <!-- Detail / Review -->
                                    <a href="<?= base_url('direktur/pengadaan/pengajuan-atk/review/'.$p['id']) ?>" class="btn btn-sm <?= $st === 'menunggu' ? 'btn-success' : 'btn-outline-primary' ?> rounded-pill px-2.5 py-1 text-xs fw-semibold" title="Review / Detail">
                                        <i class="fas <?= $st === 'menunggu' ? 'fa-clipboard-check' : 'fa-eye' ?> me-1"></i> <?= $st === 'menunggu' ? 'Review' : 'Detail' ?>
                                    </a>

                                    <!-- Edit -->
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-warning rounded-pill px-2.5 py-1 text-xs fw-semibold btn-edit-atk" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editAtkModal"
                                            data-id="<?= $p['id'] ?>"
                                            data-nama="<?= esc($p['nama_barang']) ?>"
                                            data-jumlah="<?= esc($p['jumlah']) ?>"
                                            data-satuan="<?= esc($p['satuan'] ?: 'Pcs') ?>"
                                            data-alasan="<?= esc($p['alasan']) ?>"
                                            data-status="<?= esc($p['status']) ?>"
                                            title="Edit Pengajuan">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </button>

                                    <!-- Hapus -->
                                    <form action="<?= base_url('direktur/pengadaan/pengajuan-atk/delete/'.$p['id']) ?>" method="POST" class="d-inline">
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 text-xs fw-semibold" onclick="return confirm('Apakah Anda yakin ingin menghapus pengajuan ATK <?= esc($p['nama_barang']) ?>?')" title="Hapus Pengajuan">
                                            <i class="fas fa-trash me-1"></i> Hapus
                                        </button>
                                    </form>
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

<!-- Modal Edit ATK (Single Reusable Modal) -->
<div class="modal fade" id="editAtkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white rounded-top-4 py-3">
                <h5 class="modal-title fs-6 fw-bold mb-0"><i class="fas fa-edit me-2"></i> Edit Pengajuan ATK</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('direktur/pengadaan/pengajuan-atk/update') ?>" method="POST">
                <input type="hidden" name="id" id="edit_atk_id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Nama Barang ATK *</label>
                        <input type="text" class="form-control rounded-3" name="nama_barang" id="edit_atk_nama" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-xs text-dark">Jumlah *</label>
                            <input type="number" class="form-control rounded-3" name="jumlah" id="edit_atk_jumlah" min="1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-xs text-dark">Satuan</label>
                            <input type="text" class="form-control rounded-3" name="satuan" id="edit_atk_satuan" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Alasan Kebutuhan</label>
                        <textarea class="form-control rounded-3" name="alasan" id="edit_atk_alasan" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Status Pengajuan</label>
                        <select name="status" class="form-select rounded-3" id="edit_atk_status">
                            <option value="menunggu">Menunggu</option>
                            <option value="disetujui">Disetujui</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4 py-2">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-semibold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah ATK -->
<div class="modal fade" id="tambahAtkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white rounded-top-4 py-3">
                <h5 class="modal-title fs-6 fw-bold mb-0"><i class="fas fa-plus-circle me-2"></i> Buat Pengajuan ATK Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('direktur/pengadaan/pengajuan-atk/simpan') ?>" method="POST" onsubmit="const btn = this.querySelector('button[type=submit]'); if(btn){ btn.disabled = true; btn.innerHTML = '<i class=\'fas fa-spinner fa-spin me-1\'></i> Mengirim...'; }">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Nama Barang ATK *</label>
                        <input type="text" class="form-control rounded-3" name="nama_barang" required placeholder="Cth: Kertas A4 70gr, Spidol Boardmarker">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-xs text-dark">Jumlah *</label>
                            <input type="number" class="form-control rounded-3" name="jumlah" value="1" min="1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-xs text-dark">Satuan</label>
                            <input type="text" class="form-control rounded-3" name="satuan" value="Pcs" placeholder="Pcs / Rim / Box">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Alasan Kebutuhan</label>
                        <textarea class="form-control rounded-3" name="alasan" rows="3" placeholder="Untuk kebutuhan operasional proyek / divisi..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4 py-2">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-semibold">Kirim Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchTable');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#atkTable tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }

    // Populate Edit Modal dynamically
    $(document).on('click', '.btn-edit-atk', function() {
        $('#edit_atk_id').val($(this).data('id'));
        $('#edit_atk_nama').val($(this).data('nama'));
        $('#edit_atk_jumlah').val($(this).data('jumlah'));
        $('#edit_atk_satuan').val($(this).data('satuan'));
        $('#edit_atk_alasan').val($(this).data('alasan'));
        $('#edit_atk_status').val(($(this).data('status') || 'menunggu').toLowerCase());
    });
});
</script>

<?= view('direktur/templates/footer', $templateData) ?>
