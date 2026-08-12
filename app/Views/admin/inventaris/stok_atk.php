<?php
$title = $title ?? 'Monitoring Stok ATK';
$templateData = [
    'title'  => $title,
    'user'   => session()->get('user') ?? ['name' => session()->get('name') ?? 'Administrator', 'role' => 'admin'],
    'active' => 'inventaris'
];

$totalJenis = count($stok ?? []);
$amanCount = 0;
$menipisCount = 0;
$habisCount = 0;

foreach($stok as $item) {
    $st = strtolower($item['status_stok'] ?? '');
    if($st === 'menipis') $menipisCount++;
    elseif($st === 'habis' || $st === 'kosong') $habisCount++;
    else $amanCount++;
}
?>

<?= view('admin/templates/header', $templateData) ?>
<?= view('admin/templates/sidebar', $templateData) ?>
<?= view('admin/templates/navbar', $templateData) ?>

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

    .pengadaan-card-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(30, 60, 114, 0.06), 0 2px 6px rgba(0, 0, 0, 0.02) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
    
    .status-pill-aman {
        background: rgba(25, 135, 84, 0.12);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.25);
    }

    .status-pill-menipis {
        background: rgba(255, 193, 7, 0.15);
        color: #b58100;
        border: 1px solid rgba(255, 193, 7, 0.3);
    }

    .status-pill-habis {
        background: rgba(220, 53, 69, 0.12);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.25);
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
        min-width: 720px !important;
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
            <div class="bg-gradient-primary text-white rounded-3 p-2 me-2.5 me-md-3 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 44px; height: 44px; background: linear-gradient(135deg, #1e3c72, #2a5298);">
                <i class="fas fa-boxes fs-5"></i>
            </div>
            <div class="overflow-hidden">
                <h4 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 1.25rem;">Monitoring Stok ATK</h4>
                <small class="text-muted d-none d-sm-inline">Pantau ketersediaan dan saldo inventaris Alat Tulis Kantor secara real-time.</small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary rounded-pill shadow-sm d-inline-flex align-items-center text-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#tambahStokModal">
                <i class="fas fa-plus me-1.5"></i> <span>Tambah Stok Barang</span>
            </button>
            <a href="<?= base_url('admin/inventaris/pengajuan-atk') ?>" class="btn btn-outline-primary rounded-pill shadow-sm d-inline-flex align-items-center text-sm fw-semibold">
                <i class="fas fa-pen-nib me-1.5"></i> <span>Pengajuan ATK</span>
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-3" role="alert">
            <i class="fas fa-check-circle me-1.5"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- 2. KPI Statistik Cards -->
    <div class="row g-2 g-md-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card stat-card-pengadaan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center stat-icon-wrapper" style="width: 44px; height: 44px;">
                        <i class="fas fa-cubes text-primary"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="stat-number-responsive text-dark"><?= number_format($totalJenis) ?></div>
                        <div class="text-muted text-truncate" style="font-size: 0.72rem; font-weight: 500;">Total Item ATK</div>
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
                        <div class="stat-number-responsive text-success"><?= number_format($amanCount) ?></div>
                        <div class="text-muted text-truncate" style="font-size: 0.72rem; font-weight: 500;">Stok Aman</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card-pengadaan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center stat-icon-wrapper" style="width: 44px; height: 44px;">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="stat-number-responsive text-warning"><?= number_format($menipisCount) ?></div>
                        <div class="text-muted text-truncate" style="font-size: 0.72rem; font-weight: 500;">Stok Menipis</div>
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
                        <div class="stat-number-responsive text-danger"><?= number_format($habisCount) ?></div>
                        <div class="text-muted text-truncate" style="font-size: 0.72rem; font-weight: 500;">Stok Habis</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Tabel Data Stok ATK -->
    <div class="card pengadaan-card-modern p-3 p-md-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="fas fa-clipboard-list text-primary me-2"></i> Inventory & Saldo Barang ATK
            </h5>
        </div>

        <div class="table-scroll-wrapper">
            <table class="table table-hover align-middle mb-0" id="tableStokAtk">
                <thead class="bg-light text-secondary">
                    <tr style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <th class="ps-3 py-3" style="width: 50px;">No</th>
                        <th class="py-3">Kode & Nama Barang</th>
                        <th class="py-3">Kategori</th>
                        <th class="py-3 text-center">Jumlah Stok</th>
                        <th class="py-3">Lokasi Simpan</th>
                        <th class="py-3 text-center">Status Stok</th>
                        <th class="py-3 text-center" style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.85rem;">
                    <?php if(!empty($stok)): ?>
                        <?php $no=1; foreach($stok as $item): ?>
                            <tr>
                                <td class="ps-3 fw-bold text-secondary"><?= $no++ ?></td>
                                <td>
                                    <strong class="text-dark d-block"><?= esc($item['nama_barang']) ?></strong>
                                    <small class="text-muted d-block" style="font-size: 0.72rem;"><?= esc($item['kode_barang'] ?: '-') ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2.5 py-1.5 rounded-pill">
                                        <?= esc($item['kategori'] ?? 'Umum') ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold fs-6 text-dark"><?= number_format($item['stok']) ?></span>
                                    <small class="text-muted text-xs ms-1"><?= esc($item['satuan']) ?></small>
                                </td>
                                <td>
                                    <span class="text-dark d-inline-block text-truncate" style="max-width: 180px;">
                                        <i class="fas fa-map-marker-alt text-danger me-1" style="font-size: 0.75rem;"></i>
                                        <?= esc($item['lokasi'] ?: 'Gudang Utama') ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php
                                        $st = strtolower($item['status_stok'] ?? '');
                                        $badgeClass = 'status-pill-aman';
                                        $icon = 'fa-check';
                                        if ($st === 'menipis') { $badgeClass = 'status-pill-menipis'; $icon = 'fa-exclamation-triangle'; }
                                        elseif ($st === 'habis' || $st === 'kosong') { $badgeClass = 'status-pill-habis'; $icon = 'fa-times'; }
                                    ?>
                                    <span class="status-pill <?= $badgeClass ?>">
                                        <i class="fas <?= $icon ?> me-1"></i> <?= ucfirst(esc($item['status_stok'])) ?>
                                    </span>
                                </td>
                                <td class="text-center pe-3">
                                    <div class="btn-group btn-group-sm">
                                         <button type="button" 
                                                 class="btn btn-outline-primary rounded-start px-2 btn-edit-stok" 
                                                 data-bs-toggle="modal" 
                                                 data-bs-target="#editStokModal"
                                                 data-id="<?= $item['id'] ?>"
                                                 data-nama="<?= esc($item['nama_barang']) ?>"
                                                 data-kategori="<?= esc($item['kategori'] ?? '') ?>"
                                                 data-satuan="<?= esc($item['satuan'] ?? 'Pcs') ?>"
                                                 data-stok="<?= esc($item['stok'] ?? 0) ?>"
                                                 data-lokasi="<?= esc($item['lokasi'] ?? '') ?>"
                                                 title="Edit">
                                             <i class="fas fa-edit"></i>
                                         </button>
                                         <form action="<?= base_url('admin/inventaris/stok-atk/delete/' . $item['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus item stok ATK ini?')">
                                             <button type="submit" class="btn btn-outline-danger rounded-end px-2" title="Hapus">
                                                 <i class="fas fa-trash"></i>
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

<!-- Single Reusable Edit Stok Modal Outside Table -->
<div class="modal fade" id="editStokModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-edit text-primary me-2"></i> Edit Stok Barang ATK</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/inventaris/stok-atk/update') ?>" method="POST">
                <div class="modal-body py-3">
                    <input type="hidden" name="id" id="edit_stok_id">
                    <div class="mb-3">
                        <label class="form-label text-xs fw-semibold text-dark">Nama Barang ATK *</label>
                        <input type="text" name="nama_barang" id="edit_stok_nama" class="form-control rounded-3" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label text-xs fw-semibold text-dark">Kategori</label>
                            <input type="text" name="kategori" id="edit_stok_kategori" class="form-control rounded-3">
                        </div>
                        <div class="col-6">
                            <label class="form-label text-xs fw-semibold text-dark">Satuan</label>
                            <input type="text" name="satuan" id="edit_stok_satuan" class="form-control rounded-3">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label text-xs fw-semibold text-dark">Jumlah Stok Saat Ini *</label>
                            <input type="number" name="stok" id="edit_stok_jumlah" class="form-control rounded-3" min="0" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-xs fw-semibold text-dark">Lokasi Penyimpanan</label>
                            <input type="text" name="lokasi" id="edit_stok_lokasi" class="form-control rounded-3">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Stok ATK -->
<div class="modal fade" id="tambahStokModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-plus-circle text-primary me-2"></i> Tambah Item Stok ATK Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/inventaris/stok-atk/simpan') ?>" method="POST">
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label text-xs fw-semibold text-dark">Nama Barang ATK *</label>
                        <input type="text" name="nama_barang" class="form-control rounded-3" placeholder="Contoh: Kertas HVS A4 70gr / Pulpen Gel 0.5mm" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label text-xs fw-semibold text-dark">Kategori</label>
                            <input type="text" name="kategori" class="form-control rounded-3" placeholder="Kertas / Alat Tulis / Tinta">
                        </div>
                        <div class="col-6">
                            <label class="form-label text-xs fw-semibold text-dark">Satuan</label>
                            <input type="text" name="satuan" class="form-control rounded-3" value="Pcs" placeholder="Pcs / Rim / Box">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label text-xs fw-semibold text-dark">Jumlah Stok Awal *</label>
                            <input type="number" name="stok" class="form-control rounded-3" value="10" min="0" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-xs fw-semibold text-dark">Lokasi Penyimpanan</label>
                            <input type="text" name="lokasi" class="form-control rounded-3" value="Gudang ATK Lt. 2">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">Simpan Stok Baru</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof $.fn.DataTable !== 'undefined') {
            $('#tableStokAtk').DataTable({
                responsive: false,
                language: {
                    search: "Cari Stok:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    emptyTable: "Belum ada data stok ATK.",
                    zeroRecords: "Tidak ada data stok ATK yang cocok.",
                    paginate: { first: "Awal", last: "Akhir", next: "→", previous: "←" }
                }
            });
        }

        // Dynamic Edit Stok Modal Populator
        $(document).on('click', '.btn-edit-stok', function() {
            $('#edit_stok_id').val($(this).data('id'));
            $('#edit_stok_nama').val($(this).data('nama'));
            $('#edit_stok_kategori').val($(this).data('kategori'));
            $('#edit_stok_satuan').val($(this).data('satuan'));
            $('#edit_stok_jumlah').val($(this).data('stok'));
            $('#edit_stok_lokasi').val($(this).data('lokasi'));
        });
    });
</script>

<?= view('admin/templates/footer', $templateData) ?>
