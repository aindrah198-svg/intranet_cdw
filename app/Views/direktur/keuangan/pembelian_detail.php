<?php
$title = $title ?? 'Detail Pencatatan Pembelian';
$templateData = [
    'title' => $title,
    'user'  => session()->get('user') ?? ['name' => 'Direktur', 'role' => 'direktur'],
    'active' => 'keuangan'
];
if (!function_exists('getPembelianImgUrl')) {
    function getPembelianImgUrl($fileName) {
        if (empty($fileName)) return '';
        $clean = str_replace(['uploads/pembelian/', 'public/uploads/pembelian/'], '', $fileName);
        return base_url('uploads/pembelian/' . $clean);
    }
}

if (!function_exists('renderAttachmentBox')) {
    function renderAttachmentBox($fileName, $label, $defaultIcon, $themeColor) {
        if (empty($fileName)) {
            echo '
            <div class="bg-light rounded-3 p-4 border text-muted">
                <i class="' . $defaultIcon . ' fa-2x mb-2 opacity-50"></i>
                <div class="text-xs">Belum di-upload</div>
            </div>';
            return;
        }

        $url = getPembelianImgUrl($fileName);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($ext === 'pdf') {
            echo '
            <div class="border rounded-3 p-3 bg-light shadow-sm d-flex flex-column align-items-center justify-content-center" style="min-height: 160px;">
                <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                <div class="fw-bold text-dark text-xs mb-1 text-truncate" style="max-width: 190px;" title="' . esc(basename($fileName)) . '">' . esc(basename($fileName)) . '</div>
                <small class="text-muted mb-2 text-xs">Dokumen PDF</small>
                <a href="' . $url . '" target="_blank" class="btn btn-xs btn-danger rounded-pill px-3 py-1.5 fw-bold shadow-sm">
                    <i class="fas fa-file-pdf me-1"></i> Buka / Download PDF
                </a>
            </div>';
        } else {
            echo '
            <div class="d-flex flex-column align-items-center">
                <a href="' . $url . '" target="_blank" class="d-block w-100 mb-2">
                    <img src="' . $url . '" class="proof-img-container shadow-sm" alt="' . esc($label) . '">
                </a>
                <a href="' . $url . '" target="_blank" class="btn btn-xs btn-outline-' . $themeColor . ' rounded-pill px-3 py-1">
                    <i class="fas fa-expand me-1"></i> Lihat Foto Full
                </a>
            </div>';
        }
    }
}
?>

<?= view('direktur/templates/header', $templateData) ?>
<?= view('direktur/templates/sidebar', $templateData) ?>
<?= view('direktur/templates/navbar', $templateData) ?>

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
    .pr-detail-card {
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(30, 60, 114, 0.06) !important;
    }
    .table-scroll-wrapper {
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
    }
    .table-scroll-wrapper table {
        min-width: 700px !important;
    }
    .proof-img-container {
        width: 100%;
        max-height: 250px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid #e9ecef;
        transition: transform 0.2s;
    }
    .proof-img-container:hover {
        transform: scale(1.02);
    }
</style>

<div class="container-fluid py-3 py-md-4">
    <!-- Header Navigation -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light flex-wrap gap-3">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <a href="<?= base_url('direktur/keuangan/pembelian') ?>" class="btn btn-outline-secondary rounded-pill me-2 px-3 py-1.5 text-xs fw-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali ke Pembelian
            </a>
            <div>
                <h4 class="mb-0 fw-bold text-dark fs-5 fs-md-4">Detail Purchase Requisition (PR)</h4>
                <small class="text-muted d-none d-sm-inline">Nomor PR: <strong class="text-primary"><?= esc($p['nomor_pr']) ?></strong></small>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?= base_url('direktur/keuangan/pembelian/edit/' . $p['id']) ?>" class="btn btn-warning rounded-pill px-3.5 py-1.5 text-xs fw-semibold shadow-sm">
                <i class="fas fa-edit me-1.5"></i> Edit Data
            </a>
            <a href="<?= base_url('direktur/keuangan/pembelian/cetak') ?>" target="_blank" class="btn btn-outline-primary rounded-pill px-3.5 py-1.5 text-xs fw-semibold shadow-sm">
                <i class="fas fa-print me-1.5"></i> Cetak
            </a>
        </div>
    </div>

    <!-- Alert Flashdata -->
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-3" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- 1. Left Card: Pemohon & Pengajuan Info -->
        <div class="col-12 col-lg-6">
            <div class="card pr-detail-card p-4 h-100">
                <div class="border-bottom pb-2 mb-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-primary mb-0"><i class="fas fa-user-tag me-2"></i> INFORMASI PEMOHON & PENGAJUAN</h6>
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 text-xs fw-bold"><?= esc($p['prioritas'] ?? 'Normal') ?></span>
                </div>
                <div class="row g-3 text-sm">
                    <div class="col-6">
                        <small class="text-muted text-xs uppercase d-block fw-semibold">Nomor PR</small>
                        <strong class="text-dark fs-6"><?= esc($p['nomor_pr']) ?></strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-xs uppercase d-block fw-semibold">Tanggal Pengajuan</small>
                        <strong class="text-dark"><?= date('d F Y', strtotime($p['tanggal_pengajuan'])) ?></strong>
                    </div>
                    <div class="col-12 col-sm-6">
                        <small class="text-muted text-xs uppercase d-block fw-semibold">Nama Pemohon</small>
                        <strong class="text-dark"><?= esc($p['nama_lengkap'] ?: 'Direktur') ?></strong>
                    </div>
                    <div class="col-12 col-sm-6">
                        <small class="text-muted text-xs uppercase d-block fw-semibold">NIK & Jabatan</small>
                        <span class="text-dark"><?= esc($p['nik'] ?? '-') ?> (<?= esc($p['jabatan'] ?? '-') ?>)</span>
                    </div>
                    <div class="col-12 border-top pt-2">
                        <small class="text-muted text-xs uppercase d-block fw-semibold mb-1">Alasan / Kebutuhan Pembelian</small>
                        <p class="mb-0 text-dark bg-light p-3 rounded-3 border fw-semibold"><?= nl2br(esc($p['alasan_pembelian'] ?: '-')) ?></p>
                    </div>
                    <div class="col-12 border-top pt-2">
                        <small class="text-muted text-xs uppercase d-block fw-semibold mb-1">Status Persetujuan Direktur</small>
                        <?php
                            $stDir = $p['status_direktur'] ?? 'Menunggu';
                            $stBadge = 'bg-warning text-dark';
                            if ($stDir === 'Disetujui') $stBadge = 'bg-success text-white';
                            if ($stDir === 'Ditolak') $stBadge = 'bg-danger text-white';
                        ?>
                        <span class="badge <?= $stBadge ?> px-3 py-1.5 rounded-pill text-xs fw-semibold">
                            <i class="fas fa-shield-alt me-1"></i> Status Direktur: <?= esc($stDir) ?>
                        </span>
                        <?php if(!empty($p['catatan'])): ?>
                            <small class="d-block text-muted mt-1">Catatan: <?= esc($p['catatan']) ?></small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Right Card: Tracking & Platform Beli -->
        <div class="col-12 col-lg-6">
            <div class="card pr-detail-card p-4 h-100">
                <div class="border-bottom pb-2 mb-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-success mb-0"><i class="fas fa-truck-loading me-2"></i> TRACKING & PLATFORM BELI</h6>
                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-1 text-xs fw-bold"><?= esc($p['tipe_pembelian'] ?? 'Online') ?></span>
                </div>
                <div class="row g-3 text-sm">
                    <div class="col-12 col-sm-6">
                        <small class="text-muted text-xs uppercase d-block fw-semibold">Platform / Toko Beli</small>
                        <strong class="text-dark fs-6"><?= esc($p['platform_pembelian'] ?? 'Tokopedia') ?></strong>
                    </div>
                    <div class="col-12 col-sm-6">
                        <small class="text-muted text-xs uppercase d-block fw-semibold">Metode Pembayaran</small>
                        <span class="text-dark fw-semibold"><?= esc($p['metode_pembayaran'] ?? 'Direct Transfer') ?></span>
                    </div>
                    <div class="col-12 col-sm-6">
                        <small class="text-muted text-xs uppercase d-block fw-semibold">Status Pembayaran</small>
                        <?php $stBayar = $p['status_pembayaran'] ?? 'Belum Dibayar'; ?>
                        <?php if($stBayar === 'Dibayar / Lunas' || $stBayar === 'Lunas' || $stBayar === 'Dibayar'): ?>
                            <span class="badge bg-success text-white rounded-pill px-3 py-1 text-xs"><i class="fas fa-check-circle me-1"></i> Lunas / Dibayar</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1 text-xs"><i class="fas fa-clock me-1"></i> Belum Dibayar</span>
                        <?php endif; ?>
                    </div>
                    <div class="col-12 col-sm-6">
                        <small class="text-muted text-xs uppercase d-block fw-semibold">Status Penerimaan Barang</small>
                        <?php $stTerima = $p['status_penerimaan'] ?? 'Belum'; ?>
                        <?php if($stTerima === 'Diterima Lengkap' || $stTerima === 'Diterima'): ?>
                            <span class="badge bg-info text-dark rounded-pill px-3 py-1 text-xs"><i class="fas fa-box-open me-1"></i> Barang Diterima</span>
                        <?php elseif($stTerima === 'Dipesan'): ?>
                            <span class="badge bg-primary text-white rounded-pill px-3 py-1 text-xs"><i class="fas fa-truck me-1"></i> Dipesan</span>
                        <?php else: ?>
                            <span class="badge bg-secondary text-white rounded-pill px-3 py-1 text-xs"><i class="fas fa-store-slash me-1"></i> Belum Dibeli</span>
                        <?php endif; ?>
                    </div>
                    <div class="col-12 col-sm-6 border-top pt-2">
                        <small class="text-muted text-xs uppercase d-block fw-semibold">No. Resi / Order ID</small>
                        <span class="text-dark fw-bold"><?= esc($p['no_resi_transaksi'] ?: '-') ?></span>
                    </div>
                    <div class="col-12 col-sm-6 border-top pt-2">
                        <small class="text-muted text-xs uppercase d-block fw-semibold">Link Produk Toko</small>
                        <?php if(!empty($p['link_produk'])): ?>
                            <a href="<?= esc($p['link_produk']) ?>" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1 text-xs">
                                <i class="fas fa-external-link-alt me-1"></i> Buka Link Produk
                            </a>
                        <?php else: ?>
                            <span class="text-muted">- Tidak ada link -</span>
                        <?php endif; ?>
                    </div>
                    <div class="col-12 border-top pt-2">
                        <small class="text-muted text-xs uppercase d-block fw-semibold">Total Nominal Estimasi / Transaksi</small>
                        <h4 class="fw-bold text-success mb-0">Rp <?= number_format($p['total_estimasi'] ?? 0, 0, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Full-width Table: Item Details -->
        <div class="col-12">
            <div class="card pr-detail-card p-4">
                <h6 class="fw-bold text-dark border-bottom pb-3 mb-3 d-flex align-items-center">
                    <i class="fas fa-boxes text-primary me-2"></i> DAFTAR RINCIAN ITEM BARANG
                </h6>
                <div class="table-scroll-wrapper">
                    <table class="table table-bordered align-middle text-sm mb-0 bg-white">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Barang</th>
                                <th>Spesifikasi</th>
                                <th class="text-center" width="120">Jumlah</th>
                                <th class="text-end" width="180">Harga Satuan (Rp)</th>
                                <th class="text-end" width="180">Subtotal (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($p['items'])): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">- Rincian item barang tidak spesifik -</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($p['items'] as $item): ?>
                                    <?php 
                                        $itemQty = $item['jumlah'] ?? $item['qty'] ?? $item['quantity'] ?? 1;
                                        $itemHarga = floatval($item['harga_estimasi'] ?? $item['harga_satuan'] ?? $item['harga'] ?? 0);
                                        $itemSubtotal = floatval($item['total_estimasi'] ?? $item['total_harga'] ?? $item['subtotal'] ?? ($itemQty * $itemHarga));
                                    ?>
                                    <tr>
                                        <td class="fw-semibold text-dark"><?= esc($item['nama_barang'] ?? '-') ?></td>
                                        <td><?= esc($item['spesifikasi'] ?? '-') ?></td>
                                        <td class="text-center fw-bold text-primary"><?= $itemQty ?> <?= esc($item['satuan'] ?? 'Pcs') ?></td>
                                        <td class="text-end">Rp <?= number_format($itemHarga, 0, ',', '.') ?></td>
                                        <td class="text-end fw-bold text-success">Rp <?= number_format($itemSubtotal, 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr class="fw-bold fs-6">
                                <td colspan="4" class="text-end">TOTAL ESTIMASI BELANJA:</td>
                                <td class="text-end text-success">Rp <?= number_format($p['total_estimasi'] ?? 0, 0, ',', '.') ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- 4. Lampiran Bukti 3 Uploads -->
        <div class="col-12">
            <div class="card pr-detail-card p-4 mb-3">
                <h6 class="fw-bold text-dark border-bottom pb-3 mb-3 d-flex align-items-center">
                    <i class="fas fa-paperclip text-primary me-2"></i> BUKTI LAMPIRAN PEMBELIAN & PEMBAYARAN
                </h6>
                <div class="row g-4">
                    <div class="col-12 col-md-4 text-center">
                        <small class="text-muted text-xs uppercase d-block fw-semibold mb-2">1. Bukti Invoice / Struk Pembelian</small>
                        <?php renderAttachmentBox($p['bukti_pembelian'] ?? '', 'Bukti Pembelian', 'fas fa-file-invoice', 'primary'); ?>
                    </div>

                    <div class="col-12 col-md-4 text-center">
                        <small class="text-muted text-xs uppercase d-block fw-semibold mb-2">2. Bukti Transfer / Pembayaran</small>
                        <?php renderAttachmentBox($p['bukti_pembayaran'] ?? '', 'Bukti Pembayaran', 'fas fa-receipt', 'success'); ?>
                    </div>

                    <div class="col-12 col-md-4 text-center">
                        <small class="text-muted text-xs uppercase d-block fw-semibold mb-2">3. Foto Barang Diterima</small>
                        <?php renderAttachmentBox($p['bukti_barang'] ?? '', 'Foto Barang Diterima', 'fas fa-box-open', 'info'); ?>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('direktur/templates/footer') ?>
