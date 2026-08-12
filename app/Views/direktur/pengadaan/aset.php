<?php
$title = $title ?? 'Pengadaan Aset';
$templateData = [
    'title' => $title,
    'user'  => session()->get('user') ?? ['name' => 'Direktur', 'role' => 'direktur'],
    'active' => 'pengadaan'
];

$totalAset = count($aset ?? []);
$menungguCount = 0;
$disetujuiCount = 0;
$ditolakCount = 0;
$totalInvestasi = 0;

foreach($aset as $item) {
    $st = strtolower($item['status'] ?? '');
    if($st === 'menunggu') $menungguCount++;
    elseif($st === 'disetujui') $disetujuiCount++;
    elseif($st === 'ditolak') $ditolakCount++;
    
    $totalInvestasi += floatval($item['estimasi_harga'] ?? 0) * intval($item['jumlah'] ?? 1);
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
        font-size: clamp(1rem, 2.5vw, 1.35rem);
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
        min-width: 780px !important;
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
            display: flex;
            gap: 8px;
        }
        .header-btn-group .btn {
            flex: 1;
            justify-content: center;
            font-size: 0.8rem;
            padding: 8px 10px;
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
                <i class="fas fa-desktop fs-5"></i>
            </div>
            <div class="overflow-hidden">
                <h4 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 1.25rem;">Pengadaan Aset Perusahaan</h4>
                <small class="text-muted d-none d-sm-inline">Kelola persetujuan usulan barang aset baru dan inventaris perangkat kerja CDW.</small>
            </div>
        </div>
        <div class="header-btn-group">
            <a href="<?= base_url('direktur/pengadaan/aset/cetak') ?>" target="_blank" class="btn btn-outline-secondary rounded-pill shadow-sm d-inline-flex align-items-center fw-semibold">
                <i class="fas fa-print me-1.5"></i> <span>Cetak Daftar</span>
            </a>
            <button class="btn btn-primary rounded-pill shadow-sm d-inline-flex align-items-center fw-semibold" data-bs-toggle="modal" data-bs-target="#tambahAsetModal">
                <i class="fas fa-plus me-1.5"></i> <span>Usulkan Aset Baru</span>
            </button>
        </div>
    </div>

    <!-- 2. KPI Statistik Cards -->
    <div class="row g-2 g-md-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card stat-card-pengadaan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center stat-icon-wrapper" style="width: 44px; height: 44px;">
                        <i class="fas fa-desktop text-primary"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Total Usulan</small>
                        <div class="stat-number-responsive text-primary text-truncate"><?= number_format($totalAset) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card-pengadaan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center stat-icon-wrapper" style="width: 44px; height: 44px;">
                        <i class="fas fa-coins text-info"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Estimasi Invesatasi</small>
                        <div class="stat-number-responsive text-info text-truncate">Rp <?= number_format($totalInvestasi, 0, ',', '.') ?></div>
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
    </div>

    <!-- 3. Tabel Data Pengadaan Aset -->
    <div class="card pengadaan-card-modern p-3 p-md-4 mb-3">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-0 fs-6 fs-md-5 d-flex align-items-center">
                    <i class="fas fa-list-alt text-primary me-2"></i> Daftar Usulan Pengadaan Aset
                </h5>
                <small class="text-muted text-xs d-block mt-0.5">Geser tabel kesamping untuk melihat rincian aset di mobile.</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <input type="text" id="searchTableAset" class="form-control form-control-sm rounded-pill px-3" style="max-width: 200px;" placeholder="Cari nama aset / kode...">
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold text-nowrap" data-bs-toggle="modal" data-bs-target="#tambahAsetModal">
                    <i class="fas fa-plus me-1"></i> Usulkan Aset Baru
                </button>
            </div>
        </div>

        <div class="table-scroll-wrapper">
            <table class="table table-hover align-middle text-sm mb-0" id="asetTable">
                <thead class="table-light text-nowrap">
                    <tr>
                        <th width="24%">Kode & Nama Aset</th>
                        <th width="14%">Kategori</th>
                        <th width="18%">Estimasi Harga Satuan</th>
                        <th width="10%">Jumlah</th>
                        <th width="14%">Status Approval</th>
                        <th width="20%" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($aset)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data pengadaan aset terdaftar.</td></tr>
                    <?php else: ?>
                        <?php foreach($aset as $a): ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark"><?= esc($a['nama_aset']) ?></div>
                                <small class="text-muted text-xs"><i class="fas fa-barcode me-1"></i><?= esc($a['kode_pengadaan'] ?: 'AST-00'.$a['id']) ?></small>
                            </td>
                            <td class="text-nowrap"><span class="badge bg-light text-dark border"><?= esc($a['kategori'] ?: 'Elektronik') ?></span></td>
                            <td class="fw-bold text-primary text-nowrap">Rp <?= number_format($a['estimasi_harga'], 0, ',', '.') ?></td>
                            <td class="fw-semibold text-dark text-nowrap"><?= esc($a['jumlah']) ?> Unit</td>
                            <td>
                                <?php
                                    $st = strtolower($a['status']);
                                    $pillClass = 'status-pill-menunggu';
                                    if ($st === 'disetujui') $pillClass = 'status-pill-disetujui';
                                    if ($st === 'ditolak') $pillClass = 'status-pill-ditolak';
                                ?>
                                <span class="status-pill <?= $pillClass ?>">
                                    <i class="fas fa-circle fs-6 me-1.5" style="font-size: 0.4rem !important;"></i>
                                    <?= ucfirst(esc($a['status'])) ?>
                                </span>
                            </td>
                            <td class="text-end text-nowrap">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-outline-info rounded-pill px-2 py-1 text-xs fw-semibold me-1" title="Detail Aset" onclick="showDetailAset(<?= esc(json_encode($a)) ?>)">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>
                                     <button type="button" 
                                             class="btn btn-xs btn-outline-warning rounded-pill px-2 py-1 text-xs fw-semibold me-1 btn-edit-aset" 
                                             data-bs-toggle="modal" 
                                             data-bs-target="#editAsetModal"
                                             data-id="<?= $a['id'] ?>"
                                             data-nama="<?= esc($a['nama_aset']) ?>"
                                             data-kategori="<?= esc($a['kategori'] ?? 'Elektronik & IT') ?>"
                                             data-harga="<?= number_format($a['estimasi_harga'], 0, ',', '.') ?>"
                                             data-jumlah="<?= esc($a['jumlah'] ?? 1) ?>"
                                             data-status="<?= esc($a['status'] ?? 'menunggu') ?>"
                                             data-alasan="<?= esc($a['alasan_pengadaan'] ?? '') ?>"
                                             title="Edit Aset">
                                         <i class="fas fa-edit"></i> Edit
                                     </button>
                                     <a href="<?= base_url('direktur/pengadaan/aset/review/'.$a['id']) ?>" class="btn btn-xs <?= $st === 'menunggu' ? 'btn-success' : 'btn-outline-primary' ?> rounded-pill px-2 py-1 me-1 text-xs fw-semibold" title="Review Status">
                                         <i class="fas fa-check-circle"></i> Review
                                     </a>
                                     <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2 py-1 text-xs fw-semibold" title="Hapus Aset" onclick="confirmDeleteAset('<?= base_url('direktur/pengadaan/aset/delete/'.$a['id']) ?>', '<?= esc(addslashes($a['nama_aset'])) ?>')">
                                         <i class="fas fa-trash"></i> Hapus
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

<!-- Single Reusable Modal Edit Aset Outside Table -->
<div class="modal fade text-start" id="editAsetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white rounded-top-4 py-3">
                <h5 class="modal-title fs-6 fw-bold mb-0"><i class="fas fa-edit me-2"></i> Edit Pengadaan Aset</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('direktur/pengadaan/aset/update') ?>" method="POST" onsubmit="const btn = this.querySelector('button[type=submit]'); if(btn){ btn.disabled = true; btn.innerHTML = '<i class=\'fas fa-spinner fa-spin me-1\'></i> Menyimpan...'; }">
                <input type="hidden" name="id" id="edit_direktur_aset_id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Nama Aset *</label>
                        <input type="text" class="form-control rounded-3" name="nama_aset" id="edit_direktur_aset_nama" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-xs text-dark">Kategori</label>
                            <select name="kategori" id="edit_direktur_aset_kategori" class="form-select rounded-3">
                                <option value="Elektronik & IT">Elektronik & IT</option>
                                <option value="Peralatan Kantor">Peralatan Kantor</option>
                                <option value="Furniture">Furniture / Meubel</option>
                                <option value="Kendaraan">Kendaraan</option>
                                <option value="Perkakas Kerja">Perkakas Kerja</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-xs text-dark">Jumlah Unit *</label>
                            <input type="number" class="form-control rounded-3 fw-bold" name="jumlah" id="edit_direktur_aset_jumlah" min="1" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Estimasi Harga Satuan (Rp) *</label>
                        <input type="text" class="form-control rounded-3 input-rupiah" name="estimasi_harga" id="edit_direktur_aset_harga" oninput="formatRupiah(this)" required placeholder="Rp 15.000.000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Status Approval</label>
                        <select name="status" id="edit_direktur_aset_status" class="form-select rounded-3">
                            <option value="menunggu">Menunggu</option>
                            <option value="disetujui">Disetujui</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Alasan Pengadaan Aset</label>
                        <textarea class="form-control rounded-3" name="alasan_pengadaan" id="edit_direktur_aset_alasan" rows="3"></textarea>
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

<!-- Modal Detail Aset -->
<div class="modal fade" id="detailAsetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-primary text-white rounded-top-4 py-3">
                <h5 class="modal-title fs-6 fw-bold mb-0"><i class="fas fa-info-circle me-2"></i> Detail Usulan Pengadaan Aset</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4 pb-3 border-bottom">
                    <div class="badge bg-primary bg-opacity-10 text-primary px-3 py-1.5 rounded-pill mb-2 fw-bold" id="detailKodeAset">-</div>
                    <h5 class="fw-bold text-dark mb-1" id="detailNamaAset">-</h5>
                    <span class="badge bg-secondary rounded-pill px-3 py-1 text-xs" id="detailKategoriAset">-</span>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <small class="text-muted text-xs uppercase d-block fw-semibold mb-1">Harga Satuan</small>
                        <h5 class="fw-bold text-primary mb-0" id="detailHargaSatuan">-</h5>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-xs uppercase d-block fw-semibold mb-1">Jumlah Unit</small>
                        <h5 class="fw-bold text-dark mb-0" id="detailJumlahUnit">-</h5>
                    </div>
                    <div class="col-12 border-top pt-2">
                        <small class="text-muted text-xs uppercase d-block fw-semibold mb-1">Total Estimasi Investasi</small>
                        <h4 class="fw-bold text-success mb-0" id="detailTotalInvestasi">-</h4>
                    </div>
                    <div class="col-12 border-top pt-2">
                        <small class="text-muted text-xs uppercase d-block fw-semibold mb-1">Status Approval</small>
                        <div id="detailStatusAsetPill">-</div>
                    </div>
                    <div class="col-12 border-top pt-2">
                        <small class="text-muted text-xs uppercase d-block fw-semibold mb-1">Alasan Kebutuhan Pengadaan</small>
                        <p class="text-dark bg-light rounded-3 p-3 mb-0 text-sm" id="detailAlasanAset">-</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light rounded-bottom-4 py-2">
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Aset -->
<div class="modal fade" id="tambahAsetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white rounded-top-4 py-3">
                <h5 class="modal-title fs-6 fw-bold mb-0"><i class="fas fa-plus-circle me-2"></i> Usulkan Pengadaan Aset Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('direktur/pengadaan/aset/simpan') ?>" method="POST" onsubmit="const btn = this.querySelector('button[type=submit]'); if(btn){ btn.disabled = true; btn.innerHTML = '<i class=\'fas fa-spinner fa-spin me-1\'></i> Menyimpan...'; }">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Nama Aset *</label>
                        <input type="text" class="form-control rounded-3" name="nama_aset" required placeholder="Cth: Laptop Core i7, Printer Laserjet, AC 1.5 PK">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-xs text-dark">Kategori</label>
                            <select name="kategori" class="form-select rounded-3">
                                <option value="Elektronik & IT">Elektronik & IT</option>
                                <option value="Peralatan Kantor">Peralatan Kantor</option>
                                <option value="Furniture">Furniture / Meubel</option>
                                <option value="Kendaraan">Kendaraan</option>
                                <option value="Perkakas Kerja">Perkakas Kerja</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-xs text-dark">Jumlah Unit *</label>
                            <input type="number" class="form-control rounded-3" name="jumlah" value="1" min="1" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Estimasi Harga Satuan (Rp) *</label>
                        <input type="text" class="form-control rounded-3 input-rupiah" name="estimasi_harga" oninput="formatRupiah(this)" required placeholder="Rp 15.000.000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Alasan Pengadaan Aset</label>
                        <textarea class="form-control rounded-3" name="alasan_pengadaan" rows="3" placeholder="Alasan kebutuhan dan urgensi aset..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4 py-2">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-semibold">Kirim Usulan Aset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SweetAlert2 CDN & Custom Interactivity Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (session()->getFlashdata('success')): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '<?= esc(session()->getFlashdata('success')) ?>',
        confirmButtonColor: '#0d6efd',
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
        title: 'Gagal!',
        text: '<?= esc(session()->getFlashdata('error')) ?>',
        confirmButtonColor: '#dc3545',
        customClass: { popup: 'rounded-4 shadow-lg' }
    });
});
</script>
<?php endif; ?>

<script>
function formatRupiah(input) {
    let value = input.value.replace(/[^,\d]/g, '').toString();
    let split = value.split(',');
    let sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
    input.value = rupiah ? 'Rp ' + rupiah : '';
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchTableAset');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#asetTable tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }

    $(document).on('click', '.btn-edit-aset', function() {
        $('#edit_direktur_aset_id').val($(this).data('id'));
        $('#edit_direktur_aset_nama').val($(this).data('nama'));
        $('#edit_direktur_aset_kategori').val($(this).data('kategori'));
        let hg = $(this).data('harga') || '0';
        $('#edit_direktur_aset_harga').val('Rp ' + hg);
        $('#edit_direktur_aset_jumlah').val($(this).data('jumlah'));
        $('#edit_direktur_aset_status').val(($(this).data('status') || 'menunggu').toLowerCase());
        $('#edit_direktur_aset_alasan').val($(this).data('alasan'));
    });
});

function showDetailAset(item) {
    document.getElementById('detailKodeAset').innerText = item.kode_pengadaan || '-';
    document.getElementById('detailNamaAset').innerText = item.nama_aset || '-';
    document.getElementById('detailKategoriAset').innerText = item.kategori || 'Elektronik';
    
    const harga = parseFloat(item.estimasi_harga || 0);
    const qty = parseInt(item.jumlah || 1);
    const total = harga * qty;

    document.getElementById('detailHargaSatuan').innerText = 'Rp ' + harga.toLocaleString('id-ID');
    document.getElementById('detailJumlahUnit').innerText = qty + ' Unit';
    document.getElementById('detailTotalInvestasi').innerText = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('detailAlasanAset').innerText = item.alasan_pengadaan || '- Tidak ada alasan dispesifikasikan -';

    const st = (item.status || '').toLowerCase();
    let statusHtml = '<span class="status-pill status-pill-menunggu"><i class="fas fa-circle me-1" style="font-size:0.4rem;"></i> Menunggu</span>';
    if (st === 'disetujui') {
        statusHtml = '<span class="status-pill status-pill-disetujui"><i class="fas fa-circle me-1" style="font-size:0.4rem;"></i> Disetujui</span>';
    } else if (st === 'ditolak') {
        statusHtml = '<span class="status-pill status-pill-ditolak"><i class="fas fa-circle me-1" style="font-size:0.4rem;"></i> Ditolak</span>';
    }
    document.getElementById('detailStatusAsetPill').innerHTML = statusHtml;

    const modal = new bootstrap.Modal(document.getElementById('detailAsetModal'));
    modal.show();
}

function confirmDeleteAset(url, name) {
    Swal.fire({
        title: 'Apakah Anda Yakin?',
        text: "Anda akan menghapus usulan pengadaan aset '" + name + "'. Tindakan ini tidak dapat dibatalkan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash me-1"></i> Ya, Hapus!',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-4 shadow-lg' }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>

<?= view('direktur/templates/footer', $templateData) ?>
