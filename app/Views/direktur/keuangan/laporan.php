<?php
$title = $title ?? 'Executive Summary Laporan Keuangan';
$templateData = [
    'title' => $title,
    'user'  => session()->get('user') ?? ['name' => 'Direktur', 'role' => 'direktur'],
    'active' => 'keuangan'
];
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
    .laporan-card-modern {
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
    
    .laporan-card-modern:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 32px rgba(30, 60, 114, 0.12), 0 4px 10px rgba(0, 0, 0, 0.04) !important;
    }

    .stat-card-laporan {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(30, 60, 114, 0.05);
        transition: transform 0.2s ease;
    }

    .stat-card-laporan:hover {
        transform: translateY(-2px);
    }

    .stat-number-responsive {
        font-size: clamp(1rem, 2.5vw, 1.35rem);
        font-weight: 700;
        line-height: 1.25;
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
    .table-scroll-wrapper::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
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
        .stat-card-laporan {
            padding: 12px !important;
        }
        .stat-icon-wrapper {
            width: 40px !important;
            height: 40px !important;
            min-width: 40px;
        }
        .text-responsive-title {
            font-size: 1.1rem !important;
        }
    }
</style>

<div class="container-fluid py-3 py-md-4">

    <!-- 1. Header Section -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-3 border border-light header-mobile-flex gap-3">
        <div class="d-flex align-items-center overflow-hidden">
            <div class="bg-gradient-primary text-white rounded-3 p-2 me-2.5 me-md-3 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 42px; height: 42px; background: linear-gradient(135deg, #2563eb, #1d4ed8);">
                <i class="fas fa-chart-line fs-5"></i>
            </div>
            <div class="overflow-hidden">
                <h4 class="mb-0 fw-bold text-dark text-responsive-title text-truncate" style="font-size: 1.25rem;">Laporan Keuangan Eksekutif (Real Database)</h4>
                <small class="text-muted d-none d-sm-inline">Ringkasan performa finansial, arus kas 12 bulan, pemasukan, pengeluaran & laba bersih CDW Engineering.</small>
            </div>
        </div>
        <div class="header-btn-group">
            <a href="<?= base_url('direktur/keuangan/laporan/export-excel?tahun='.$tahun.'&bulan='.$bulan) ?>" class="btn btn-outline-success rounded-pill shadow-sm d-inline-flex align-items-center fw-semibold">
                <i class="fas fa-file-excel me-1.5"></i> <span>Export Excel</span>
            </a>
            <a href="<?= base_url('direktur/keuangan/laporan/cetak?tahun='.$tahun.'&bulan='.$bulan) ?>" target="_blank" class="btn btn-primary rounded-pill shadow-sm d-inline-flex align-items-center fw-semibold">
                <i class="fas fa-file-pdf me-1.5"></i> <span>Export PDF</span>
            </a>
        </div>
    </div>

    <!-- 2. Filter Bar (Tahun & Bulan) -->
    <div class="card stat-card-laporan mb-3">
        <div class="card-body p-3 p-md-4">
            <form action="" method="GET" class="row g-2 g-md-3 align-items-end">
                <div class="col-12 col-sm-6 col-lg-5">
                    <label class="form-label fw-semibold text-xs text-dark mb-1"><i class="fas fa-calendar text-primary me-1.5"></i> Pilih Tahun Buku</label>
                    <input type="number" name="tahun" class="form-control form-control-sm rounded-3" value="<?= esc($tahun) ?>" placeholder="Contoh: 2026">
                </div>
                <div class="col-12 col-sm-6 col-lg-5">
                    <label class="form-label fw-semibold text-xs text-dark mb-1"><i class="fas fa-filter text-primary me-1.5"></i> Filter Bulan</label>
                    <select name="bulan" class="form-select form-select-sm rounded-3">
                        <option value="all" <?= $bulan === 'all' ? 'selected' : '' ?>>Semua Bulan (Januari - Desember)</option>
                        <option value="01" <?= $bulan === '01' ? 'selected' : '' ?>>Januari</option>
                        <option value="02" <?= $bulan === '02' ? 'selected' : '' ?>>Februari</option>
                        <option value="03" <?= $bulan === '03' ? 'selected' : '' ?>>Maret</option>
                        <option value="04" <?= $bulan === '04' ? 'selected' : '' ?>>April</option>
                        <option value="05" <?= $bulan === '05' ? 'selected' : '' ?>>Mei</option>
                        <option value="06" <?= $bulan === '06' ? 'selected' : '' ?>>Juni</option>
                        <option value="07" <?= $bulan === '07' ? 'selected' : '' ?>>Juli</option>
                        <option value="08" <?= $bulan === '08' ? 'selected' : '' ?>>Agustus</option>
                        <option value="09" <?= $bulan === '09' ? 'selected' : '' ?>>September</option>
                        <option value="10" <?= $bulan === '10' ? 'selected' : '' ?>>Oktober</option>
                        <option value="11" <?= $bulan === '11' ? 'selected' : '' ?>>November</option>
                        <option value="12" <?= $bulan === '12' ? 'selected' : '' ?>>Desember</option>
                    </select>
                </div>
                <div class="col-12 col-lg-2">
                    <button type="submit" class="btn btn-dark btn-sm w-100 rounded-pill py-2 fw-semibold">
                        <i class="fas fa-filter me-1.5"></i> Terapkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. Ringkasan 6 KPI Statistik Finansial -->
    <div class="row g-2 g-md-3 mb-3">
        <!-- 1. Pendapatan Client -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card stat-card-laporan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center stat-icon-wrapper" style="width: 44px; height: 44px;">
                        <i class="fas fa-arrow-down text-success fs-5"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Pendapatan Client (Inflow)</small>
                        <div class="stat-number-responsive text-success text-truncate">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 2. Pembelian Barang (PR) -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card stat-card-laporan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center stat-icon-wrapper" style="width: 44px; height: 44px;">
                        <i class="fas fa-shopping-cart text-warning fs-5"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Beban Pembelian (PR)</small>
                        <div class="stat-number-responsive text-warning text-truncate">Rp <?= number_format($totalPembelian, 0, ',', '.') ?></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 3. Gaji Karyawan (Payroll) -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card stat-card-laporan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center stat-icon-wrapper" style="width: 44px; height: 44px;">
                        <i class="fas fa-users text-info fs-5"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Beban Gaji Karyawan</small>
                        <div class="stat-number-responsive text-info text-truncate">Rp <?= number_format($totalGaji, 0, ',', '.') ?></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 4. Kasbon Karyawan -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card stat-card-laporan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-secondary bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center stat-icon-wrapper" style="width: 44px; height: 44px;">
                        <i class="fas fa-hand-holding-usd text-secondary fs-5"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Pencairan Kasbon</small>
                        <div class="stat-number-responsive text-secondary text-truncate">Rp <?= number_format($totalKasbon, 0, ',', '.') ?></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 5. Total Outflow -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card stat-card-laporan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-danger bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center stat-icon-wrapper" style="width: 44px; height: 44px;">
                        <i class="fas fa-arrow-up text-danger fs-5"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Total Outflow Operasional</small>
                        <div class="stat-number-responsive text-danger text-truncate">Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 6. Laba Bersih -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card stat-card-laporan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center stat-icon-wrapper" style="width: 44px; height: 44px;">
                        <i class="fas fa-balance-scale text-primary fs-5"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Estimasi Laba/Rugi Bersih</small>
                        <div class="stat-number-responsive <?= $labaBersih >= 0 ? 'text-primary' : 'text-danger' ?> text-truncate">Rp <?= number_format($labaBersih, 0, ',', '.') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Cashflow Table -->
    <div class="card laporan-card-modern mb-3 p-3 p-md-4">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-0 fs-6 fs-md-5 d-flex align-items-center">
                    <i class="fas fa-table text-primary me-2"></i> Rincian Arus Kas Per Bulan (Januari - Desember <?= esc($tahun) ?>)
                </h5>
                <small class="text-muted text-xs d-block mt-0.5">Tabel terakumulasi dari data real database sistem PT CDW Engineering.</small>
            </div>
            <span class="badge bg-primary bg-opacity-10 text-primary px-2.5 py-1 rounded-pill text-xs fw-semibold">
                <i class="fas fa-database me-1"></i> Data Real System
            </span>
        </div>
        
        <div class="table-scroll-wrapper">
            <table class="table table-hover table-bordered align-middle text-sm mb-0">
                <thead class="table-light text-center text-nowrap">
                    <tr>
                        <th width="12%">Bulan</th>
                        <th width="16%">Pendapatan Client (Rp)</th>
                        <th width="15%">Pembelian (PR)</th>
                        <th width="15%">Gaji Karyawan</th>
                        <th width="14%">Kasbon</th>
                        <th width="14%">Total Outflow (Rp)</th>
                        <th width="14%">Surplus / Defisit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($monthlyData as $m): ?>
                        <tr>
                            <td class="fw-semibold text-dark text-center text-nowrap"><?= esc($m['bulan_name']) ?></td>
                            <td class="text-end fw-bold text-success text-nowrap">Rp <?= number_format($m['pendapatan'], 0, ',', '.') ?></td>
                            <td class="text-end text-nowrap">Rp <?= number_format($m['pembelian'], 0, ',', '.') ?></td>
                            <td class="text-end text-nowrap">Rp <?= number_format($m['gaji'], 0, ',', '.') ?></td>
                            <td class="text-end text-nowrap">Rp <?= number_format($m['kasbon'], 0, ',', '.') ?></td>
                            <td class="text-end fw-bold text-danger text-nowrap">Rp <?= number_format($m['total_pengeluaran'], 0, ',', '.') ?></td>
                            <td class="text-end fw-bold <?= $m['surplus'] >= 0 ? 'text-primary' : 'text-danger' ?> text-nowrap">
                                <?= $m['surplus'] >= 0 ? '+' : '' ?>Rp <?= number_format($m['surplus'], 0, ',', '.') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-dark text-nowrap">
                    <tr class="fw-bold text-end">
                        <td class="text-center">TOTAL TAHUNAN</td>
                        <td class="text-success">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></td>
                        <td>Rp <?= number_format($totalPembelian, 0, ',', '.') ?></td>
                        <td>Rp <?= number_format($totalGaji, 0, ',', '.') ?></td>
                        <td>Rp <?= number_format($totalKasbon, 0, ',', '.') ?></td>
                        <td class="text-danger">Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?></td>
                        <td class="<?= $labaBersih >= 0 ? 'text-info' : 'text-warning' ?>">
                            Rp <?= number_format($labaBersih, 0, ',', '.') ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- 5. Real Transactions Log Table from Database -->
    <div class="card laporan-card-modern mb-3 p-3 p-md-4">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-0 fs-6 fs-md-5 d-flex align-items-center">
                    <i class="fas fa-list-alt text-primary me-2"></i> Log Transaksi Keuangan Terbaru (Data Real System)
                </h5>
                <small class="text-muted text-xs d-block mt-0.5">Daftar transaksi pengadaan, kasbon, dan pendapatan yang disetujui dalam database.</small>
            </div>
            <span class="badge bg-success bg-opacity-10 text-success px-2.5 py-1 rounded-pill text-xs fw-semibold">
                <i class="fas fa-check-circle me-1"></i> Terverifikasi Database
            </span>
        </div>

        <div class="table-scroll-wrapper">
            <table class="table table-hover table-bordered align-middle text-sm mb-0">
                <thead class="table-light text-center text-nowrap">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Kategori / Modul</th>
                        <th>No. Referensi</th>
                        <th>Pemohon / Klien</th>
                        <th>Keterangan</th>
                        <th>Arus Kas</th>
                        <th>Nominal (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($realTransactions)): ?>
                        <?php $no = 1; foreach ($realTransactions as $tx): ?>
                            <tr>
                                <td class="text-center font-monospace text-xs"><?= $no++ ?></td>
                                <td class="text-center text-nowrap font-monospace text-xs"><?= date('d/m/Y', strtotime($tx['tanggal'])) ?></td>
                                <td class="text-nowrap"><span class="badge <?= $tx['badge'] ?> text-xs px-2 py-1"><?= esc($tx['jenis']) ?></span></td>
                                <td class="font-monospace text-xs text-nowrap fw-bold"><?= esc($tx['nomor']) ?></td>
                                <td class="fw-semibold text-truncate" style="max-width: 150px;"><?= esc($tx['pemohon']) ?></td>
                                <td class="text-truncate" style="max-width: 250px;"><?= esc($tx['keterangan']) ?></td>
                                <td class="text-center text-nowrap">
                                    <?php if ($tx['tipe'] === 'Pemasukan'): ?>
                                        <span class="badge bg-success text-white text-xs px-2 py-0.5"><i class="fas fa-arrow-down me-1"></i> Inflow</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger text-white text-xs px-2 py-0.5"><i class="fas fa-arrow-up me-1"></i> Outflow</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end fw-bold text-nowrap <?= $tx['tipe'] === 'Pemasukan' ? 'text-success' : 'text-danger' ?>">
                                    Rp <?= number_format($tx['nominal'], 0, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-info-circle me-1"></i> Belum ada log transaksi disetujui untuk periode tahun/bulan ini di database.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 6. Grid Detail Ikhtisar & Tautan Modul (Responsive 2-Columns) -->
    <div class="row g-2 g-md-3">
        <!-- Ikhtisar Laba Rugi -->
        <div class="col-12 col-lg-6">
            <div class="card laporan-card-modern h-100 p-3 p-md-4">
                <h5 class="fw-bold text-dark border-bottom pb-2.5 mb-2.5 fs-6 fs-md-5 d-flex align-items-center">
                    <i class="fas fa-receipt text-primary me-2"></i> Ikhtisar Laba Rugi Komprehensif
                </h5>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom flex-wrap gap-1">
                    <span class="text-secondary fw-semibold text-xs text-md-sm">1. Total Pendapatan Operasional</span>
                    <strong class="text-success text-xs text-md-sm">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></strong>
                </div>
                <div class="ps-2 ps-md-3 py-1.5 text-xs text-muted border-bottom d-flex justify-content-between">
                    <span>• Realisasi Project & Invoice Klien</span>
                    <span>Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></span>
                </div>

                <div class="d-flex justify-content-between align-items-center py-2 border-bottom mt-2 flex-wrap gap-1">
                    <span class="text-secondary fw-semibold text-xs text-md-sm">2. Total Outflow Operasional</span>
                    <strong class="text-danger text-xs text-md-sm">(Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?>)</strong>
                </div>
                <div class="ps-2 ps-md-3 py-1.5 text-xs text-muted border-bottom d-flex justify-content-between">
                    <span>• Beban Pembelian Barang (PR)</span>
                    <span>Rp <?= number_format($totalPembelian, 0, ',', '.') ?></span>
                </div>
                <div class="ps-2 ps-md-3 py-1.5 text-xs text-muted border-bottom d-flex justify-content-between">
                    <span>• Beban Gaji Karyawan</span>
                    <span>Rp <?= number_format($totalGaji, 0, ',', '.') ?></span>
                </div>
                <div class="ps-2 ps-md-3 py-1.5 text-xs text-muted border-bottom d-flex justify-content-between">
                    <span>• Pencairan Kasbon Karyawan</span>
                    <span>Rp <?= number_format($totalKasbon, 0, ',', '.') ?></span>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-2.5 mt-3 bg-light p-2.5 p-md-3 rounded-3 border flex-wrap gap-2">
                    <span class="fw-bold text-dark text-xs text-md-sm">ESTIMASI LABA BERSIH OPERASIONAL</span>
                    <span class="fw-bold fs-6 fs-md-5 <?= $labaBersih >= 0 ? 'text-primary' : 'text-danger' ?>">
                        Rp <?= number_format($labaBersih, 0, ',', '.') ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Tautan Laporan Detail -->
        <div class="col-12 col-lg-6">
            <div class="card laporan-card-modern h-100 p-3 p-md-4">
                <h5 class="fw-bold text-dark border-bottom pb-2.5 mb-2.5 fs-6 fs-md-5 d-flex align-items-center">
                    <i class="fas fa-link text-primary me-2"></i> Akses Modul Keuangan Terkait
                </h5>
                <p class="text-muted small mb-2.5">Akses cepat ke masing-masing modul rincian pengadaan, penggajian, kasbon, dan piutang:</p>
                <div class="d-grid gap-2">
                    <a href="<?= base_url('direktur/keuangan/pembelian') ?>" class="btn btn-outline-warning text-start rounded-3 p-2.5 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center overflow-hidden me-2">
                            <i class="fas fa-shopping-cart me-2 fs-5 text-warning flex-shrink-0"></i>
                            <strong class="text-dark text-xs text-md-sm text-truncate">Pencatatan & Persetujuan Pembelian (PR)</strong>
                        </div>
                        <i class="fas fa-chevron-right text-muted flex-shrink-0"></i>
                    </a>
                    <a href="<?= base_url('direktur/keuangan/penggajian') ?>" class="btn btn-outline-success text-start rounded-3 p-2.5 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center overflow-hidden me-2">
                            <i class="fas fa-money-check-alt me-2 fs-5 text-success flex-shrink-0"></i>
                            <strong class="text-dark text-xs text-md-sm text-truncate">Penggajian & Slip Gaji Karyawan</strong>
                        </div>
                        <i class="fas fa-chevron-right text-muted flex-shrink-0"></i>
                    </a>
                    <a href="<?= base_url('direktur/keuangan/kasbon') ?>" class="btn btn-outline-secondary text-start rounded-3 p-2.5 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center overflow-hidden me-2">
                            <i class="fas fa-hand-holding-usd me-2 fs-5 text-secondary flex-shrink-0"></i>
                            <strong class="text-dark text-xs text-md-sm text-truncate">Manajemen & Potongan Kasbon Karyawan</strong>
                        </div>
                        <i class="fas fa-chevron-right text-muted flex-shrink-0"></i>
                    </a>
                    <a href="<?= base_url('direktur/monitoring/invoice-piutang') ?>" class="btn btn-outline-info text-start rounded-3 p-2.5 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center overflow-hidden me-2">
                            <i class="fas fa-file-contract me-2 fs-5 text-info flex-shrink-0"></i>
                            <strong class="text-dark text-xs text-md-sm text-truncate">Monitoring Invoice & Piutang Klien</strong>
                        </div>
                        <i class="fas fa-chevron-right text-muted flex-shrink-0"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('direktur/templates/footer', $templateData) ?>
