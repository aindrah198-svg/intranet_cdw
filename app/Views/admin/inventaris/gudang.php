<?php
$title = $title ?? 'Monitoring Gudang & Stok Material';
$templateData = [
    'title'  => $title,
    'user'   => session()->get('user') ?? ['name' => session()->get('name') ?? 'Administrator', 'role' => 'admin'],
    'active' => 'inventaris'
];

$totalGudang = count($gudang ?? []);
$tersediaCount = 0;
$indentCount = 0;
$kosongCount = 0;

foreach($gudang as $item) {
    $st = strtolower($item['status'] ?? '');
    if($st === 'indent') $indentCount++;
    elseif($st === 'kosong' || $st === 'habis') $kosongCount++;
    else $tersediaCount++;
}

if (!function_exists('getGudangImgUrlAdmin')) {
    function getGudangImgUrlAdmin($foto) {
        if (empty($foto)) return '';
        $cleanFoto = str_replace(['uploads/gudang/', 'public/uploads/gudang/'], '', $foto);
        
        if (file_exists(FCPATH . 'uploads/gudang/' . $cleanFoto)) {
            return base_url('uploads/gudang/' . $cleanFoto);
        }
        if (file_exists(ROOTPATH . 'public/uploads/gudang/' . $cleanFoto)) {
            return base_url('uploads/gudang/' . $cleanFoto);
        }
        if (filter_var($foto, FILTER_VALIDATE_URL) || strpos($foto, 'http') === 0) {
            return $foto;
        }
        return base_url('uploads/gudang/' . $cleanFoto);
    }
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
    }

    .pengadaan-card-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(30, 60, 114, 0.06) !important;
        transition: all 0.3s ease;
    }

    .stat-card-pengadaan {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(30, 60, 114, 0.05);
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 5px 12px;
        border-radius: 30px;
        font-size: 0.76rem;
        font-weight: 600;
    }

    .status-pill-tersedia {
        background: rgba(25, 135, 84, 0.12);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.25);
    }

    .status-pill-indent {
        background: rgba(255, 193, 7, 0.15);
        color: #b58100;
        border: 1px solid rgba(255, 193, 7, 0.3);
    }

    .status-pill-kosong {
        background: rgba(220, 53, 69, 0.12);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.25);
    }

    .table-scroll-wrapper {
        width: 100% !important;
        overflow-x: auto !important;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
    }

    .table-scroll-wrapper table {
        min-width: 850px !important;
        width: 100% !important;
    }
</style>

<div class="container-fluid py-3 py-md-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-3 border border-light flex-wrap gap-3">
        <div class="d-flex align-items-center">
            <div class="bg-gradient-primary text-white rounded-3 p-2 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px; background: linear-gradient(135deg, #1e3c72, #2a5298);">
                <i class="fas fa-warehouse fs-5"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark fs-5">Monitoring Gudang & Stok Material</h4>
                <small class="text-muted d-none d-sm-inline">Monitoring persediaan material proyek, lokasi rak gudang & status stok fisik.</small>
            </div>
        </div>
        <div>
            <a href="<?= base_url('admin/inventaris/gudang/tambah') ?>" class="btn btn-primary rounded-pill shadow-sm d-inline-flex align-items-center fw-semibold">
                <i class="fas fa-plus me-1.5"></i> Tambah Barang Gudang
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3" role="alert">
            <i class="fas fa-check-circle me-1.5"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3" role="alert">
            <i class="fas fa-exclamation-circle me-1.5"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- KPI Ringkasan -->
    <div class="row g-2 g-md-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card stat-card-pengadaan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-boxes-packing text-primary fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5 text-dark"><?= number_format($totalGudang) ?></div>
                        <div class="text-muted text-xs">Total Item Material</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card-pengadaan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-check-circle text-success fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5 text-success"><?= number_format($tersediaCount) ?></div>
                        <div class="text-muted text-xs">Stok Tersedia</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card-pengadaan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-truck-loading text-warning fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5 text-warning"><?= number_format($indentCount) ?></div>
                        <div class="text-muted text-xs">Indent / Order</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card-pengadaan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-danger bg-opacity-10 me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-box-open text-danger fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5 text-danger"><?= number_format($kosongCount) ?></div>
                        <div class="text-muted text-xs">Stok Kosong</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table -->
    <div class="card pengadaan-card-modern p-3 p-md-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h5 class="fw-bold text-dark mb-0 fs-6">
                <i class="fas fa-list text-primary me-2"></i> Inventory Barang & Material Gudang
            </h5>
        </div>

        <div class="table-scroll-wrapper">
            <table class="table table-hover align-middle mb-0" id="tableGudang">
                <thead class="bg-light text-secondary">
                    <tr style="font-size: 0.78rem; text-transform: uppercase;">
                        <th class="ps-3 py-3" style="width: 40px;">No</th>
                        <th class="py-3" style="width: 60px;">Foto</th>
                        <th class="py-3">Kode & Nama Material</th>
                        <th class="py-3">Kategori</th>
                        <th class="py-3 text-center">Stok Tersedia</th>
                        <th class="py-3">Lokasi Gudang & Rak</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3 text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.85rem;">
                    <?php if (!empty($gudang)): ?>
                        <?php $no = 1; foreach ($gudang as $g): ?>
                            <?php $imgUrl = getGudangImgUrlAdmin($g['foto_barang'] ?? ''); ?>
                            <tr>
                                <td class="ps-3 fw-bold text-secondary"><?= $no++ ?></td>
                                <td>
                                    <?php if (!empty($imgUrl)): ?>
                                        <a href="<?= $imgUrl ?>" target="_blank">
                                            <img src="<?= $imgUrl ?>" alt="Foto" class="rounded-3 shadow-sm border" style="width: 42px; height: 42px; object-fit: cover;">
                                        </a>
                                    <?php else: ?>
                                        <div class="rounded-3 bg-light d-flex align-items-center justify-content-center text-muted border" style="width: 42px; height: 42px;">
                                            <i class="fas fa-image text-secondary opacity-50"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong class="text-primary d-block"><?= esc($g['nama_barang']) ?></strong>
                                    <small class="text-muted d-block" style="font-size: 0.72rem;"><i class="fas fa-barcode me-1"></i><?= esc($g['kode_barang']) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill">
                                        <?= esc($g['kategori'] ?: 'Material') ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold fs-6 text-dark"><?= number_format($g['stok_tersedia']) ?></span>
                                    <small class="text-muted text-xs ms-1"><?= esc($g['satuan'] ?: 'Pcs') ?></small>
                                </td>
                                <td>
                                    <strong class="text-dark d-block"><?= esc($g['lokasi_gudang'] ?: 'Gudang Utama') ?></strong>
                                    <small class="text-muted d-block" style="font-size: 0.72rem;"><?= esc($g['lokasi_rak'] ?: '-') ?></small>
                                </td>
                                <td class="text-center">
                                    <?php
                                        $st = strtolower($g['status'] ?? 'tersedia');
                                        $pillClass = 'status-pill-tersedia';
                                        $statusText = 'TERSEDIA';
                                        if ($st === 'indent') {
                                            $pillClass = 'status-pill-indent';
                                            $statusText = 'INDENT';
                                        } elseif ($st === 'kosong' || $st === 'habis') {
                                            $pillClass = 'status-pill-kosong';
                                            $statusText = 'KOSONG';
                                        }
                                    ?>
                                    <span class="status-pill <?= $pillClass ?>">
                                        <i class="fas fa-circle fs-6 me-1.5" style="font-size: 0.4rem !important;"></i>
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td class="text-center pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('admin/inventaris/gudang/detail/' . $g['id']) ?>" class="btn btn-outline-primary rounded-start px-2" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('admin/inventaris/gudang/edit/' . $g['id']) ?>" class="btn btn-outline-secondary px-2" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?= base_url('admin/inventaris/gudang/delete/' . $g['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus barang gudang ini?')">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof $.fn.DataTable !== 'undefined') {
            $('#tableGudang').DataTable({
                responsive: false,
                language: {
                    search: "Cari Material:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    emptyTable: "Belum ada barang gudang yang terdata.",
                    zeroRecords: "Tidak ada material yang cocok.",
                    paginate: { first: "Awal", last: "Akhir", next: "→", previous: "←" }
                }
            });
        }
    });
</script>

<?= view('admin/templates/footer', $templateData) ?>
