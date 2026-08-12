<?php
$title = $title ?? 'Pencatatan & Tracking Pembelian (PR)';
$templateData = [
    'title'  => $title,
    'user'   => session()->get('user') ?? ['name' => session()->get('name') ?? 'Direktur', 'role' => 'direktur'],
    'active' => 'keuangan'
];
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

    .pr-card-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(30, 60, 114, 0.06) !important;
        transition: all 0.3s ease;
    }
    
    .pr-card-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 32px rgba(30, 60, 114, 0.12) !important;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 5px 12px;
        border-radius: 30px;
        font-size: 0.76rem;
        font-weight: 600;
    }

    .table-scroll-wrapper {
        width: 100% !important;
        overflow-x: auto !important;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
    }

    .table-scroll-wrapper table {
        min-width: 900px !important;
        width: 100% !important;
    }
</style>

<div class="container-fluid py-3 py-md-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-3 border border-light flex-wrap gap-3">
        <div class="d-flex align-items-center">
            <div class="bg-gradient-primary text-white rounded-3 p-2 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px; background: linear-gradient(135deg, #1e3c72, #2a5298);">
                <i class="fas fa-shopping-cart fs-5"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark fs-5">Pencatatan & Tracking Pembelian (PR)</h4>
                <small class="text-muted d-none d-sm-inline">Pantau pengadaan barang, status persetujuan Purchase Requisition & pengiriman.</small>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?= base_url('direktur/keuangan/pembelian/tambah') ?>" class="btn btn-primary rounded-pill shadow-sm d-inline-flex align-items-center fw-semibold">
                <i class="fas fa-plus me-1.5"></i> Buat Pencatatan PR Baru
            </a>
            <a href="<?= base_url('direktur/keuangan/pembelian/export-excel') ?>" class="btn btn-outline-success rounded-pill shadow-sm d-inline-flex align-items-center fw-semibold">
                <i class="fas fa-file-excel me-1.5"></i> Export Excel
            </a>
            <a href="<?= base_url('direktur/keuangan/pembelian/cetak') ?>" target="_blank" class="btn btn-outline-info rounded-pill shadow-sm d-inline-flex align-items-center fw-semibold">
                <i class="fas fa-print me-1.5"></i> Cetak Rekap
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

    <!-- KPI Row -->
    <div class="row g-2 g-md-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card pr-card-modern p-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-file-invoice text-primary fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5 text-dark"><?= count($pembelian) ?></div>
                        <div class="text-muted text-xs">Total Pembelian (PR)</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card pr-card-modern p-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-sack-dollar text-success fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-6 text-success">Rp <?= number_format($totalNominal, 0, ',', '.') ?></div>
                        <div class="text-muted text-xs">Total Nominal PR</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card pr-card-modern p-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-clock text-warning fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5 text-warning"><?= number_format($totalMenunggu) ?></div>
                        <div class="text-muted text-xs">Menunggu Persetujuan</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card pr-card-modern p-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fas fa-cart-flatbed text-info fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-6 text-dark"><?= number_format($totalOnline) ?> Online / <?= number_format($totalOffline) ?> Offline</div>
                        <div class="text-muted text-xs">Metode Transaksi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card pr-card-modern p-3 p-md-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h5 class="fw-bold text-dark mb-0 fs-6">
                <i class="fas fa-list text-primary me-2"></i> Daftar Transaksi & Tracking PR
            </h5>
        </div>

        <div class="table-scroll-wrapper">
            <table class="table table-hover align-middle mb-0" id="tablePembelian">
                <thead class="bg-light text-secondary">
                    <tr style="font-size: 0.78rem; text-transform: uppercase;">
                        <th class="ps-3 py-3" style="width: 40px;">No</th>
                        <th class="py-3">Nomor PR & Tanggal</th>
                        <th class="py-3">Pemohon</th>
                        <th class="py-3">Ringkasan Barang / Alasan</th>
                        <th class="py-3 text-end">Total Estimasi</th>
                        <th class="py-3 text-center">Status Pembayaran</th>
                        <th class="py-3 text-center">Status Barang</th>
                        <th class="py-3 text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.85rem;">
                    <?php if (!empty($pembelian)): ?>
                        <?php $no = 1; foreach ($pembelian as $p): ?>
                            <tr>
                                <td class="ps-3 fw-bold text-secondary"><?= $no++ ?></td>
                                <td>
                                    <strong class="text-primary d-block"><?= esc($p['nomor_pr']) ?></strong>
                                    <small class="text-muted d-block" style="font-size: 0.72rem;"><?= date('d M Y', strtotime($p['tanggal_pengajuan'])) ?></small>
                                </td>
                                <td>
                                    <strong class="text-dark d-block"><?= esc($p['nama_lengkap'] ?? 'Direktur Panel') ?></strong>
                                    <small class="text-muted d-block" style="font-size: 0.72rem;"><?= esc($p['departemen'] ?? $p['jabatan'] ?? '-') ?></small>
                                </td>
                                <td>
                                    <span class="text-dark d-inline-block text-truncate" style="max-width: 220px;" title="<?= esc($p['alasan_pembelian']) ?>">
                                        <?= esc($p['alasan_pembelian'] ?: '-') ?>
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-dark">
                                    Rp <?= number_format($p['total_estimasi'], 0, ',', '.') ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                        $bayar = strtolower($p['status_pembayaran'] ?? 'belum dibayar');
                                        $badgeBayar = 'bg-warning text-dark';
                                        if (strpos($bayar, 'lunas') !== false || strpos($bayar, 'sudah') !== false || strpos($bayar, 'dibayar') !== false) $badgeBayar = 'bg-success text-white';
                                    ?>
                                    <span class="badge <?= $badgeBayar ?> px-2.5 py-1 rounded-pill">
                                        <?= ucfirst(esc($p['status_pembayaran'] ?? 'Belum Dibayar')) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php
                                        $terima = strtolower($p['status_penerimaan'] ?? 'belum');
                                        $badgeTerima = 'bg-secondary text-white';
                                        if (strpos($terima, 'diterima') !== false || strpos($terima, 'selesai') !== false) $badgeTerima = 'bg-success text-white';
                                        elseif (strpos($terima, 'proses') !== false || strpos($terima, 'dikirim') !== false || strpos($terima, 'dipesan') !== false) $badgeTerima = 'bg-info text-white';
                                    ?>
                                    <span class="badge <?= $badgeTerima ?> px-2.5 py-1 rounded-pill">
                                        <?= ucfirst(esc($p['status_penerimaan'] ?? 'Belum Dibeli')) ?>
                                    </span>
                                </td>
                                <td class="text-center pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('direktur/keuangan/pembelian/detail/' . $p['id']) ?>" class="btn btn-outline-primary rounded-start px-2" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('direktur/keuangan/pembelian/edit/' . $p['id']) ?>" class="btn btn-outline-secondary px-2" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('direktur/keuangan/pembelian/cetak/' . $p['id']) ?>" target="_blank" class="btn btn-outline-info px-2" title="Cetak PR">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <form action="<?= base_url('direktur/keuangan/pembelian/delete/' . $p['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus pencatatan PR ini?')">
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
            $('#tablePembelian').DataTable({
                responsive: false,
                language: {
                    search: "Cari PR:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    emptyTable: "Belum ada pencatatan pembelian (PR).",
                    zeroRecords: "Tidak ada PR yang cocok.",
                    paginate: { first: "Awal", last: "Akhir", next: "→", previous: "←" }
                }
            });
        }
    });
</script>

<?= view('direktur/templates/footer', $templateData) ?>
