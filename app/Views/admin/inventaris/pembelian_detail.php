<?php
$title = $title ?? 'Detail Pencatatan Pembelian (PR)';
$p = $p ?? $pr ?? [];
$templateData = [
    'title'  => $title,
    'user'   => session()->get('user') ?? ['name' => session()->get('name') ?? 'Administrator', 'role' => 'admin'],
    'active' => 'inventaris'
];

if (!function_exists('getPembelianImgUrlAdmin')) {
    function getPembelianImgUrlAdmin($fileName) {
        if (empty($fileName)) return '';
        $clean = str_replace(['uploads/pembelian/', 'public/uploads/pembelian/'], '', $fileName);
        return base_url('uploads/pembelian/' . $clean);
    }
}

if (!function_exists('renderAttachmentBoxAdmin')) {
    function renderAttachmentBoxAdmin($fileName, $label, $defaultIcon, $themeColor) {
        if (empty($fileName)) {
            echo '
            <div class="bg-light rounded-3 p-4 border text-muted">
                <i class="' . $defaultIcon . ' fa-2x mb-2 opacity-50"></i>
                <div class="text-xs">Belum di-upload</div>
            </div>';
            return;
        }

        $url = getPembelianImgUrlAdmin($fileName);
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
            <a href="<?= base_url('admin/inventaris/pembelian') ?>" class="btn btn-outline-secondary rounded-pill me-2 px-3 py-1.5 text-xs fw-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali ke Pembelian
            </a>
            <div>
                <h4 class="mb-0 fw-bold text-dark fs-5 fs-md-4">Detail Purchase Requisition (PR)</h4>
                <small class="text-muted d-none d-sm-inline">Nomor PR: <strong class="text-primary"><?= esc($p['nomor_pr'] ?? '-') ?></strong></small>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?= base_url('admin/inventaris/pembelian/edit/' . ($p['id'] ?? '')) ?>" class="btn btn-warning rounded-pill px-3.5 py-1.5 text-xs fw-semibold shadow-sm">
                <i class="fas fa-edit me-1.5"></i> Edit Data
            </a>
            <a href="<?= base_url('admin/inventaris/pembelian/cetak/' . ($p['id'] ?? '')) ?>" target="_blank" class="btn btn-outline-primary rounded-pill px-3.5 py-1.5 text-xs fw-semibold shadow-sm">
                <i class="fas fa-print me-1.5"></i> Cetak PR
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
                        <strong class="text-dark fs-6"><?= esc($p['nomor_pr'] ?? '-') ?></strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-xs uppercase d-block fw-semibold">Tanggal Pengajuan</small>
                        <strong class="text-dark"><?= !empty($p['tanggal_pengajuan']) ? date('d F Y', strtotime($p['tanggal_pengajuan'])) : '-' ?></strong>
                    </div>
                    <div class="col-12 col-sm-6">
                        <small class="text-muted text-xs uppercase d-block fw-semibold">Pemohon</small>
                        <strong class="text-dark"><?= esc($p['nama_lengkap'] ?? 'Admin Panel') ?></strong>
                        <div class="text-xs text-muted"><?= esc($p['jabatan'] ?? '-') ?> (<?= esc($p['departemen'] ?? '-') ?>)</div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <small class="text-muted text-xs uppercase d-block fw-semibold">Tanggal Dibutuhkan</small>
                        <span class="text-dark fw-semibold"><?= !empty($p['tanggal_dibutuhkan']) ? date('d F Y', strtotime($p['tanggal_dibutuhkan'])) : '-' ?></span>
                    </div>
                    <div class="col-12 mt-3 pt-2 border-top">
                        <small class="text-muted text-xs uppercase d-block fw-semibold mb-1">Alasan / Tujuan Pembelian</small>
                        <div class="p-3 bg-light rounded-3 text-dark text-sm border">
                            <?= nl2br(esc($p['alasan_pembelian'] ?? '-')) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Right Card: Tracking Pembelian & Status -->
        <div class="col-12 col-lg-6">
            <div class="card pr-detail-card p-4 h-100">
                <div class="border-bottom pb-2 mb-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-success mb-0"><i class="fas fa-truck-ramp-box me-2"></i> TRACKING & METODE TRANSAKSI</h6>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 text-xs fw-bold"><?= esc($p['tipe_pembelian'] ?? 'Online') ?></span>
                </div>
                <div class="row g-3 text-sm">
                    <div class="col-6">
                        <small class="text-muted text-xs uppercase d-block fw-semibold">Platform / Toko Beli</small>
                        <strong class="text-dark"><?= esc($p['platform_pembelian'] ?? '-') ?></strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-xs uppercase d-block fw-semibold">Metode Pembayaran</small>
                        <strong class="text-dark"><?= esc($p['metode_pembayaran'] ?? '-') ?></strong>
                    </div>
                    <div class="col-12">
                        <small class="text-muted text-xs uppercase d-block fw-semibold">No. Resi / Order ID / Invoice</small>
                        <?php if (!empty($p['no_resi_transaksi'])): ?>
                            <span class="badge bg-dark text-white font-monospace px-2.5 py-1 text-xs"><?= esc($p['no_resi_transaksi']) ?></span>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </div>
                    <div class="col-12">
                        <small class="text-muted text-xs uppercase d-block fw-semibold">Link Produk Toko</small>
                        <?php if (!empty($p['link_produk'])): ?>
                            <a href="<?= esc($p['link_produk']) ?>" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill mt-1">
                                <i class="fas fa-external-link-alt me-1"></i> Buka Link Produk Toko
                            </a>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Status Cards Grid -->
                    <div class="col-12 mt-3 pt-2 border-top">
                        <div class="row g-2 text-center">
                            <div class="col-4">
                                <div class="p-2 border rounded-3 bg-light">
                                    <small class="text-muted text-xs d-block">Direktur</small>
                                    <?php
                                    $sd = $p['status_direktur'] ?? 'Menunggu';
                                    $bdD = 'bg-warning text-dark';
                                    if ($sd === 'Disetujui') $bdD = 'bg-success text-white';
                                    if ($sd === 'Ditolak') $bdD = 'bg-danger text-white';
                                    ?>
                                    <span class="badge <?= $bdD ?> mt-1 text-xs"><?= esc($sd) ?></span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 border rounded-3 bg-light">
                                    <small class="text-muted text-xs d-block">Pembayaran</small>
                                    <?php
                                    $sb = $p['status_pembayaran'] ?? 'Belum Dibayar';
                                    $bdB = 'bg-warning text-dark';
                                    if (strpos(strtolower($sb), 'lunas') !== false || strpos(strtolower($sb), 'dibayar') !== false) $bdB = 'bg-success text-white';
                                    ?>
                                    <span class="badge <?= $bdB ?> mt-1 text-xs"><?= esc($sb) ?></span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 border rounded-3 bg-light">
                                    <small class="text-muted text-xs d-block">Penerimaan</small>
                                    <?php
                                    $sp = $p['status_penerimaan'] ?? 'Belum Dibeli';
                                    $bdP = 'bg-secondary text-white';
                                    if (strpos(strtolower($sp), 'terima') !== false || strpos(strtolower($sp), 'lengkap') !== false) $bdP = 'bg-success text-white';
                                    elseif (strpos(strtolower($sp), 'pesan') !== false || strpos(strtolower($sp), 'proses') !== false) $bdP = 'bg-info text-white';
                                    ?>
                                    <span class="badge <?= $bdP ?> mt-1 text-xs"><?= esc($sp) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Full Width Card: Rincian Barang -->
        <div class="col-12">
            <div class="card pr-detail-card p-4">
                <h6 class="fw-bold text-dark border-bottom pb-3 mb-3 d-flex align-items-center">
                    <i class="fas fa-boxes text-primary me-2"></i> RINCIAN DAFTAR BARANG YANG DIBELI
                </h6>
                <div class="table-scroll-wrapper">
                    <table class="table table-bordered table-hover align-middle text-sm mb-0">
                        <thead class="table-light text-uppercase text-xs">
                            <tr>
                                <th width="40" class="text-center">No</th>
                                <th>Nama Barang</th>
                                <th>Spesifikasi / Catatan</th>
                                <th width="100" class="text-center">Jumlah</th>
                                <th width="160" class="text-end">Harga Satuan (Rp)</th>
                                <th width="180" class="text-end">Total Estimasi (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($p['items'])): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-3 text-muted">Belum ada rincian item barang.</td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; $grandTotal = 0; ?>
                                <?php foreach ($p['items'] as $it): ?>
                                    <?php 
                                    $qty = $it['jumlah'] ?? $it['qty'] ?? 1;
                                    $harga = floatval($it['harga_estimasi'] ?? $it['harga_satuan'] ?? 0);
                                    $subtotal = floatval($it['total_estimasi'] ?? $it['subtotal'] ?? ($qty * $harga));
                                    $grandTotal += $subtotal;
                                    ?>
                                    <tr>
                                        <td class="text-center font-weight-bold"><?= $no++ ?></td>
                                        <td class="fw-semibold text-dark"><?= esc($it['nama_barang']) ?></td>
                                        <td class="text-muted"><?= esc($it['spesifikasi'] ?? '-') ?></td>
                                        <td class="text-center fw-bold"><?= esc($qty) ?> <?= esc($it['satuan'] ?? 'Pcs') ?></td>
                                        <td class="text-end">Rp <?= number_format($harga, 0, ',', '.') ?></td>
                                        <td class="text-end fw-bold text-primary">Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="bg-light fw-bold">
                                    <td colspan="5" class="text-end text-uppercase fs-6">TOTAL ESTIMASI KESELURUHAN:</td>
                                    <td class="text-end text-primary fs-5">Rp <?= number_format($grandTotal, 0, ',', '.') ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 4. Full Width Card: Uploaded Lampiran / Bukti -->
        <div class="col-12">
            <div class="card pr-detail-card p-4">
                <h6 class="fw-bold text-dark border-bottom pb-3 mb-3 d-flex align-items-center">
                    <i class="fas fa-paperclip text-primary me-2"></i> BUKTI LAMPIRAN TRANSAKSI & FOTO BARANG
                </h6>
                <div class="row g-3 text-center">
                    <div class="col-12 col-md-4">
                        <div class="p-3 border rounded-3 bg-white h-100">
                            <small class="fw-bold text-dark d-block mb-2 text-xs uppercase"><i class="fas fa-receipt me-1 text-primary"></i> 1. Bukti Invoice / Struk</small>
                            <?php renderAttachmentBoxAdmin($p['bukti_pembelian'] ?? '', 'Bukti Invoice', 'fas fa-file-invoice-dollar', 'primary'); ?>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="p-3 border rounded-3 bg-white h-100">
                            <small class="fw-bold text-dark d-block mb-2 text-xs uppercase"><i class="fas fa-money-check-alt me-1 text-success"></i> 2. Bukti Transfer / Bayar</small>
                            <?php renderAttachmentBoxAdmin($p['bukti_pembayaran'] ?? '', 'Bukti Transfer', 'fas fa-file-invoice', 'success'); ?>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="p-3 border rounded-3 bg-white h-100">
                            <small class="fw-bold text-dark d-block mb-2 text-xs uppercase"><i class="fas fa-camera me-1 text-info"></i> 3. Foto Fisik Barang Diterima</small>
                            <?php renderAttachmentBoxAdmin($p['bukti_barang'] ?? '', 'Foto Barang', 'fas fa-box-open', 'info'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('admin/templates/footer', $templateData) ?>
