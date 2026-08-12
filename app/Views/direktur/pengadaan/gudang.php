<?php
$title = $title ?? 'Monitoring Gudang';
$templateData = [
    'title' => $title,
    'user'  => session()->get('user') ?? ['name' => 'Direktur', 'role' => 'direktur'],
    'active' => 'pengadaan'
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

    /* Inner Table Scroll Container */
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
            <div class="bg-gradient-primary text-white rounded-3 p-2 me-2.5 me-md-3 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 44px; height: 44px;">
                <i class="fas fa-warehouse fs-5"></i>
            </div>
            <div class="overflow-hidden">
                <h4 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 1.25rem;">Monitoring Gudang & Stok Material</h4>
                <small class="text-muted d-none d-sm-inline">Monitoring stok fisik, foto barang terkompresi, dan lokasi gudang (Kantor, Gudang Blok K, Gudang Blok I).</small>
            </div>
        </div>
        <div>
            <a href="<?= base_url('direktur/pengadaan/gudang/tambah') ?>" class="btn btn-primary rounded-pill shadow-sm d-inline-flex align-items-center text-sm fw-semibold">
                <i class="fas fa-plus me-1.5"></i> <span>Tambah Barang Stok</span>
            </a>
        </div>
    </div>

    <!-- 2. KPI Statistik Cards -->
    <div class="row g-2 g-md-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card stat-card-pengadaan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center stat-icon-wrapper" style="width: 44px; height: 44px;">
                        <i class="fas fa-boxes text-primary"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Total Material</small>
                        <div class="stat-number-responsive text-primary text-truncate"><?= number_format($totalGudang) ?></div>
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
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Stok Tersedia</small>
                        <div class="stat-number-responsive text-success text-truncate"><?= number_format($tersediaCount) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card-pengadaan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center stat-icon-wrapper" style="width: 44px; height: 44px;">
                        <i class="fas fa-truck-loading text-warning"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Stok Indent</small>
                        <div class="stat-number-responsive text-warning text-truncate"><?= number_format($indentCount) ?></div>
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
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Stok Kosong</small>
                        <div class="stat-number-responsive text-danger text-truncate"><?= number_format($kosongCount) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Tabel Monitoring Stok Gudang -->
    <div class="card pengadaan-card-modern p-3 p-md-4 mb-3">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-0 fs-6 fs-md-5 d-flex align-items-center">
                    <i class="fas fa-dolly text-primary me-2"></i> Inventaris Stok Barang & Material Gudang
                </h5>
                <small class="text-muted text-xs d-block mt-0.5">Kode barang di-generate otomatis oleh sistem. Foto terkompresi otomatis.</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <input type="text" id="searchTableGudang" class="form-control form-control-sm rounded-pill px-3" style="max-width: 220px;" placeholder="Cari barang / gudang / rak...">
                <a href="<?= base_url('direktur/pengadaan/gudang/tambah') ?>" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold text-nowrap shadow-sm">
                    <i class="fas fa-plus me-1"></i> Tambah Barang
                </a>
            </div>
        </div>

        <div class="table-scroll-wrapper">
            <table class="table table-hover align-middle text-sm mb-0" id="gudangTable">
                <thead class="table-light text-nowrap">
                    <tr>
                        <th width="6%">Foto</th>
                        <th width="14%">Kode Barang</th>
                        <th width="24%">Nama Barang / Material</th>
                        <th width="18%">Lokasi Gudang & Rak</th>
                        <th width="12%">Stok Tersedia</th>
                        <th width="10%">Status Stock</th>
                        <th width="16%" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($gudang)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada inventaris barang gudang terdata.</td></tr>
                    <?php else: ?>
                        <?php foreach($gudang as $g): ?>
                        <tr>
                            <td>
                                <?php if (!empty($g['foto_barang']) && file_exists(ROOTPATH . 'public/uploads/gudang/' . $g['foto_barang'])): ?>
                                    <img src="<?= base_url('uploads/gudang/' . $g['foto_barang']) ?>" alt="Foto" class="rounded-3 shadow-sm border" style="width: 42px; height: 42px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="rounded-3 bg-light d-flex align-items-center justify-content-center text-muted border" style="width: 42px; height: 42px;">
                                        <i class="fas fa-image text-secondary opacity-50"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="fw-bold text-dark text-nowrap">
                                <i class="fas fa-barcode me-1 text-primary"></i><?= esc($g['kode_barang']) ?>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark"><?= esc($g['nama_barang']) ?></div>
                                <small class="text-muted text-xs"><i class="fas fa-tags me-1"></i><?= esc($g['kategori'] ?: 'Material') ?></small>
                            </td>
                            <td>
                                <small class="text-dark fw-bold d-block text-nowrap">
                                    <i class="fas fa-building text-primary me-1"></i> <?= esc($g['lokasi_gudang'] ?: 'Gudang Blok K') ?>
                                </small>
                                <small class="text-muted text-xs d-block text-nowrap">
                                    <i class="fas fa-layer-group text-danger me-1"></i> <?= esc($g['lokasi_rak'] ?: 'Rak A-1') ?>
                                </small>
                            </td>
                            <td class="fw-bold text-primary text-nowrap"><?= number_format($g['stok_tersedia']) ?> <?= esc($g['satuan']) ?></td>
                            <td>
                                <?php
                                    $st = strtolower($g['status'] ?? '');
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
                            <td class="text-end text-nowrap">
                                <div class="btn-group">
                                    <a href="<?= base_url('direktur/pengadaan/gudang/detail/'.$g['id']) ?>" class="btn btn-xs btn-outline-info rounded-pill px-2 py-1 text-xs fw-semibold me-1" title="Detail Barang">
                                        <i class="fas fa-eye me-1"></i> Detail
                                    </a>
                                    <a href="<?= base_url('direktur/pengadaan/gudang/edit/'.$g['id']) ?>" class="btn btn-xs btn-outline-warning rounded-pill px-2 py-1 text-xs fw-semibold me-1" title="Edit Barang">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2 py-1 text-xs fw-semibold" title="Hapus Barang" onclick="confirmDeleteGudang('<?= base_url('direktur/pengadaan/gudang/delete/'.$g['id']) ?>', '<?= esc(addslashes($g['nama_barang'])) ?>')">
                                        <i class="fas fa-trash me-1"></i> Hapus
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

<!-- SweetAlert2 CDN & Scripts -->
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
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchTableGudang');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#gudangTable tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }
});

function confirmDeleteGudang(url, name) {
    Swal.fire({
        title: 'Apakah Anda Yakin?',
        text: "Anda akan menghapus barang gudang '" + name + "'. Tindakan ini tidak dapat dibatalkan!",
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
