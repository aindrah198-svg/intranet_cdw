<?php
/**
 * LAPORAN ARUS KAS - VERSI FIXED
 * Sesuai dengan:
 * - Model: LaporanArusKasModel.php v2.0.1
 * - Controller: ArusKas.php v2.0.1
 * 
 * Perubahan:
 * 1. Struktur data menggunakan $laporan['arus_kas'] bukan $laporan['aktivitas_operasi']
 * 2. Filter saldo awal sudah ditangani oleh model dengan parameter exclude_saldo_awal
 * 3. Verifikasi menggunakan $laporan['verifikasi'] dari model
 * 4. Detail per akun menggunakan $detail_per_akun dari model
 * 5. Semua variabel sudah dicek dengan ?? [] untuk menghindari error
 */
?>

<?= $this->include('accounting/templates/header') ?>
<?= $this->include('accounting/templates/sidebar') ?>
<?= $this->include('accounting/templates/navbar') ?>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="page-title mb-1">Laporan Arus Kas</h2>
                    <p class="page-subtitle text-muted mb-0"><?= $subtitle ?? 'Cash Flow Statement - Metode Langsung' ?></p>
                    <small class="text-info">
                        <i class="fas fa-calendar-alt me-1"></i> 
                        Periode: <?= date('d M Y', strtotime($filters['tanggal_mulai'] ?? date('Y-m-01'))) ?> - <?= date('d M Y', strtotime($filters['tanggal_selesai'] ?? date('Y-m-t'))) ?>
                    </small>
                    <br>
                    <small class="text-success">
                        <i class="fas fa-check-circle me-1"></i>
                        <?= ($filters['exclude_saldo_awal'] ?? '1') == '1' ? 'Metode: Langsung - Tanpa Transaksi Saldo Awal' : 'Metode: Langsung - Termasuk Saldo Awal' ?>
                    </small>
                    <?php if ($metadata['version'] ?? false): ?>
                    <br>
                    <small class="text-primary">
                        <i class="fas fa-code-branch me-1"></i>
                        Version: <?= $metadata['version'] ?? '2.0.1' ?>
                    </small>
                    <?php endif; ?>
                </div>
                <div class="d-flex gap-2">
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-secondary" onclick="toggleFullscreen()" data-bs-toggle="tooltip" title="Fullscreen (Ctrl+F)">
                            <i class="fas fa-expand me-1"></i> Fullscreen
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="validateArusKas()" data-bs-toggle="tooltip" title="Validasi (Ctrl+V)">
                            <i class="fas fa-check-circle me-1"></i> Validasi
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="refreshSummary()" data-bs-toggle="tooltip" title="Refresh (Ctrl+R)">
                            <i class="fas fa-sync-alt me-1"></i> Refresh
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="showHelpModal()" data-bs-toggle="tooltip" title="Bantuan (Ctrl+H)">
                            <i class="fas fa-question-circle me-1"></i> Bantuan
                        </button>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-download me-1"></i> Export
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" onclick="exportLaporan('csv')"><i class="fas fa-file-csv me-2"></i> CSV</a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportLaporan('excel')"><i class="fas fa-file-excel me-2"></i> Excel</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" onclick="printLaporan()"><i class="fas fa-print me-2"></i> Print</a></li>
                        </ul>
                    </div>
                    <?php if ($is_development ?? false): ?>
                    <button type="button" class="btn btn-danger" onclick="showDebugInfo()" data-bs-toggle="tooltip" title="Debug Info">
                        <i class="fas fa-bug me-1"></i> Debug
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="modern-card filter-section">
                <form id="filterForm" method="GET" action="<?= current_url() ?>" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="tanggal_mulai" class="form-label fw-bold">Tanggal Mulai</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                            <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" 
                                   value="<?= $filters['tanggal_mulai'] ?? date('Y-m-01') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="tanggal_selesai" class="form-label fw-bold">Tanggal Selesai</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                            <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" 
                                   value="<?= $filters['tanggal_selesai'] ?? date('Y-m-t') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label for="exclude_saldo_awal" class="form-label fw-bold">Filter Saldo Awal</label>
                        <select class="form-select" id="exclude_saldo_awal" name="exclude_saldo_awal">
                            <option value="1" <?= ($filters['exclude_saldo_awal'] ?? '1') == '1' ? 'selected' : '' ?>>Ya (Tidak Termasuk)</option>
                            <option value="0" <?= ($filters['exclude_saldo_awal'] ?? '1') == '0' ? 'selected' : '' ?>>Tidak (Termasuk)</option>
                        </select>
                        <small class="text-muted">Saldo awal bukan transaksi periode berjalan</small>
                    </div>
                    <div class="col-md-2">
                        <label for="quick_period" class="form-label fw-bold">Periode Cepat</label>
                        <select class="form-select" id="quick_period" onchange="applyQuickPeriod(this.value)">
                            <option value="">-- Pilih Periode --</option>
                            <option value="today">Hari Ini</option>
                            <option value="yesterday">Kemarin</option>
                            <option value="this_week">Minggu Ini</option>
                            <option value="last_week">Minggu Lalu</option>
                            <option value="this_month">Bulan Ini</option>
                            <option value="last_month">Bulan Lalu</option>
                            <option value="this_quarter">Kuartal Ini</option>
                            <option value="last_quarter">Kuartal Lalu</option>
                            <option value="this_year">Tahun Ini</option>
                            <option value="last_year">Tahun Lalu</option>
                            <?php foreach ($recentPeriods ?? [] as $date => $period): ?>
                            <option value="period_<?= $period['start'] ?>"><?= $period['label'] ?? $date ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-search me-1"></i> Tampilkan
                        </button>
                        <a href="<?= current_url() ?>" class="btn btn-secondary">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- NOTIFICATION: INFO SALDO AWAL (DARI MODEL) -->
    <?php if (($filters['exclude_saldo_awal'] ?? '1') == '1' && ($metadata['exclude_saldo_awal'] ?? true)): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-info-circle fa-2x me-3"></i>
            <div>
                <strong>INFO: Transaksi saldo awal telah difilter.</strong>
                <p class="mb-0">Transaksi saldo awal tidak dimasukkan dalam perhitungan arus kas periode berjalan.</p>
                <small class="text-muted">Total <?= $statistik['total_transaksi'] ?? 0 ?> transaksi kas periode berjalan, <?= $statistik['akun_kas_aktif'] ?? 0 ?> akun kas aktif.</small>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <!-- NOTIFICATION: TRANSAKSI NON-KAS -->
    <?php if (!empty($laporan['transaksi_non_kas'] ?? [])): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
            <div>
                <strong>INFO: <?= count($laporan['transaksi_non_kas'] ?? []) ?> transaksi non-kas terdeteksi.</strong>
                <p class="mb-0">Jurnal penyesuaian internal tidak mempengaruhi arus kas dan tidak ditampilkan.</p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <!-- Stats Cards - SESUAI DENGAN MODEL -->
    <div class="row mb-4">
        <?php foreach ($stats as $key => $stat): ?>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start-<?= $stat['color'] ?> border-start-3 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-<?= $stat['color'] ?> text-uppercase mb-1">
                                <?= $stat['label'] ?>
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                <?= $stat['value'] ?>
                            </div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-<?= $stat['color'] ?> me-2">
                                    <i class="fas <?= $stat['icon'] ?>"></i>
                                </span>
                                <span><?= $stat['trend'] ?? '' ?></span>
                                <?php if (isset($stat['penerimaan']) && isset($stat['pengeluaran'])): ?>
                                <br>
                                <small class="text-muted">
                                    In: <?= $stat['penerimaan'] ?? 'Rp 0' ?> | 
                                    Out: <?= $stat['pengeluaran'] ?? 'Rp 0' ?>
                                </small>
                                <?php endif; ?>
                                <?php if (isset($stat['saldo_awal'])): ?>
                                <br>
                                <small class="text-muted">Awal: <?= $stat['saldo_awal'] ?? 'Rp 0' ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas <?= $stat['icon'] ?? 'fa-chart-bar' ?> fa-2x text-<?= $stat['color'] ?>"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Main Laporan Content -->
    <div class="row">
        <div class="col-12">
            <div class="modern-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i> 
                        Laporan Arus Kas - Metode Langsung
                    </h4>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-<?= ($summary['perubahan_kas'] ?? 0) >= 0 ? 'success' : 'danger' ?> me-3 p-2">
                            <i class="fas fa-<?= ($summary['perubahan_kas'] ?? 0) >= 0 ? 'arrow-up' : 'arrow-down' ?> me-1"></i>
                            Kas <?= ($summary['perubahan_kas'] ?? 0) >= 0 ? 'Bertambah' : 'Berkurang' ?>
                        </span>
                        <span class="badge bg-<?= ($verifikasi['is_valid'] ?? false) ? 'success' : 'danger' ?> p-2">
                            <i class="fas fa-<?= ($verifikasi['is_valid'] ?? false) ? 'check-circle' : 'exclamation-triangle' ?> me-1"></i>
                            <?= ($verifikasi['is_valid'] ?? false) ? 'VALID' : 'TIDAK VALID' ?>
                        </span>
                    </div>
                </div>

                <!-- Company Header -->
                <div class="text-center mb-4">
                    <h2 class="text-primary">PT. CIPTA DUTA WACANA</h2>
                    <h4 class="text-dark">LAPORAN ARUS KAS</h4>
                    <p class="text-muted">
                        Periode: <?= date('d F Y', strtotime($periode['start'] ?? $filters['tanggal_mulai'])) ?> - <?= date('d F Y', strtotime($periode['end'] ?? $filters['tanggal_selesai'])) ?>
                    </p>
                    <div class="alert alert-success d-inline-block">
                        <small>
                            <i class="fas fa-check-circle me-1"></i>
                            <strong>Versi Diperbaiki:</strong> Perhitungan hanya mencakup transaksi yang mempengaruhi kas dalam periode berjalan
                        </small>
                    </div>
                </div>

                <!-- Saldo Awal Kas -->
                <div class="laporan-section mb-4">
                    <div class="alert alert-primary">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-0">
                                    <i class="fas fa-piggy-bank me-2"></i>
                                    Saldo Kas Awal Periode
                                </h5>
                                <small class="text-muted">Saldo kas dan setara kas pada awal periode</small>
                                <br>
                                <small class="text-muted">(<?= $statistik['akun_kas_aktif'] ?? 0 ?> dari <?= $statistik['total_akun_kas'] ?? 0 ?> akun kas aktif)</small>
                            </div>
                            <div class="col-md-4 text-end">
                                <h4 class="mb-0 fw-bold">
                                    <?= $saldo_kas['saldo_awal_formatted'] ?? 'Rp 0' ?>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- AKTIVITAS OPERASI -->
                <div class="laporan-section mb-4">
                    <h5 class="section-title bg-info text-white p-2 rounded">
                        <i class="fas fa-industry me-2"></i> ARUS KAS DARI AKTIVITAS OPERASI
                        <span class="badge bg-light text-dark float-end">
                            <?= count($transaksi_operasi ?? []) ?> transaksi
                        </span>
                    </h5>
                    
                    <?php if (!empty($transaksi_operasi)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="8%">Tanggal</th>
                                    <th width="15%">No. Jurnal</th>
                                    <th width="12%">Akun Kas</th>
                                    <th width="35%">Keterangan</th>
                                    <th width="15%" class="text-end">Penerimaan (Rp)</th>
                                    <th width="15%" class="text-end">Pengeluaran (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $totalPenerimaanOperasi = 0;
                                $totalPengeluaranOperasi = 0;
                                ?>
                                <?php foreach ($transaksi_operasi as $item): ?>
                                <?php
                                $penerimaan = $item['penerimaan'] ?? 0;
                                $pengeluaran = $item['pengeluaran'] ?? 0;
                                $totalPenerimaanOperasi += $penerimaan;
                                $totalPengeluaranOperasi += $pengeluaran;
                                
                                // Counterpart info
                                $counterpartText = '';
                                if (!empty($item['counterpart'])) {
                                    $counterparts = [];
                                    foreach ($item['counterpart'] as $cp) {
                                        $counterparts[] = $cp['kode_akun'] . ' - ' . $cp['nama_akun'];
                                    }
                                    $counterpartText = implode('; ', $counterparts);
                                }
                                ?>
                                <tr class="highlight-row activity-operasi">
                                    <td><?= date('d/m/Y', strtotime($item['tanggal'])) ?></td>
                                    <td>
                                        <span class="badge bg-secondary"><?= $item['nomor_jurnal'] ?? '-' ?></span>
                                        <br>
                                        <small><?= $item['referensi'] ?? '' ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?= $item['kode_akun_kas'] ?? '' ?></span>
                                        <br>
                                        <small><?= $item['nama_akun_kas'] ?? '' ?></small>
                                    </td>
                                    <td>
                                        <div class="fw-medium"><?= $item['keterangan'] ?? '' ?></div>
                                        <?php if ($counterpartText): ?>
                                        <small class="text-muted">
                                            <i class="fas fa-link text-success me-1"></i>
                                            Counterpart: <?= $counterpartText ?>
                                        </small>
                                        <?php endif; ?>
                                        <br>
                                        <small class="badge bg-light text-dark">Aktivitas: <?= $item['aktivitas'] ?? 'OPERASI' ?></small>
                                    </td>
                                    <td class="text-end <?= $penerimaan > 0 ? 'text-success fw-bold' : '' ?>">
                                        <?= $penerimaan > 0 ? 'Rp ' . number_format($penerimaan, 0, ',', '.') : '-' ?>
                                    </td>
                                    <td class="text-end <?= $pengeluaran > 0 ? 'text-danger fw-bold' : '' ?>">
                                        <?= $pengeluaran > 0 ? 'Rp ' . number_format($pengeluaran, 0, ',', '.') : '-' ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="table-active fw-bold">
                                    <td colspan="4" class="text-end">Total Arus Kas dari Aktivitas Operasi</td>
                                    <td class="text-end text-success">
                                        Rp <?= number_format($totalPenerimaanOperasi, 0, ',', '.') ?>
                                    </td>
                                    <td class="text-end text-danger">
                                        Rp <?= number_format($totalPengeluaranOperasi, 0, ',', '.') ?>
                                    </td>
                                </tr>
                                <tr class="fw-bold bg-light">
                                    <td colspan="5" class="text-end">
                                        <i class="fas fa-<?= ($total_operasi ?? 0) >= 0 ? 'arrow-up text-success' : 'arrow-down text-danger' ?> me-1"></i>
                                        Arus Kas Bersih dari Operasi
                                    </td>
                                    <td class="text-center <?= ($total_operasi ?? 0) >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <h5 class="mb-0">
                                            <?= ($total_operasi ?? 0) >= 0 ? '+' : '' ?>
                                            Rp <?= number_format($total_operasi ?? 0, 0, ',', '.') ?>
                                        </h5>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Tidak ada transaksi aktivitas operasi yang mempengaruhi kas untuk periode ini.
                        <?php if (($filters['exclude_saldo_awal'] ?? '1') == '1'): ?>
                        <small class="d-block mt-1">
                            (Transaksi saldo awal telah difilter dan tidak dimasukkan)
                        </small>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- AKTIVITAS INVESTASI -->
                <div class="laporan-section mb-4">
                    <h5 class="section-title bg-warning text-dark p-2 rounded">
                        <i class="fas fa-chart-line me-2"></i> ARUS KAS DARI AKTIVITAS INVESTASI
                        <span class="badge bg-light text-dark float-end">
                            <?= count($transaksi_investasi ?? []) ?> transaksi
                        </span>
                    </h5>
                    
                    <?php if (!empty($transaksi_investasi)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="8%">Tanggal</th>
                                    <th width="15%">No. Jurnal</th>
                                    <th width="12%">Akun Kas</th>
                                    <th width="35%">Keterangan</th>
                                    <th width="15%" class="text-end">Penerimaan (Rp)</th>
                                    <th width="15%" class="text-end">Pengeluaran (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $totalPenerimaanInvestasi = 0;
                                $totalPengeluaranInvestasi = 0;
                                ?>
                                <?php foreach ($transaksi_investasi as $item): ?>
                                <?php
                                $penerimaan = $item['penerimaan'] ?? 0;
                                $pengeluaran = $item['pengeluaran'] ?? 0;
                                $totalPenerimaanInvestasi += $penerimaan;
                                $totalPengeluaranInvestasi += $pengeluaran;
                                
                                $counterpartText = '';
                                if (!empty($item['counterpart'])) {
                                    $counterparts = [];
                                    foreach ($item['counterpart'] as $cp) {
                                        $counterparts[] = $cp['kode_akun'] . ' - ' . $cp['nama_akun'];
                                    }
                                    $counterpartText = implode('; ', $counterparts);
                                }
                                ?>
                                <tr class="highlight-row activity-investasi">
                                    <td><?= date('d/m/Y', strtotime($item['tanggal'])) ?></td>
                                    <td>
                                        <span class="badge bg-secondary"><?= $item['nomor_jurnal'] ?? '-' ?></span>
                                        <br>
                                        <small><?= $item['referensi'] ?? '' ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning"><?= $item['kode_akun_kas'] ?? '' ?></span>
                                        <br>
                                        <small><?= $item['nama_akun_kas'] ?? '' ?></small>
                                    </td>
                                    <td>
                                        <div class="fw-medium"><?= $item['keterangan'] ?? '' ?></div>
                                        <?php if ($counterpartText): ?>
                                        <small class="text-muted">
                                            <i class="fas fa-link text-success me-1"></i>
                                            Counterpart: <?= $counterpartText ?>
                                        </small>
                                        <?php endif; ?>
                                        <br>
                                        <small class="badge bg-light text-dark">Aktivitas: <?= $item['aktivitas'] ?? 'INVESTASI' ?></small>
                                    </td>
                                    <td class="text-end <?= $penerimaan > 0 ? 'text-success fw-bold' : '' ?>">
                                        <?= $penerimaan > 0 ? 'Rp ' . number_format($penerimaan, 0, ',', '.') : '-' ?>
                                    </td>
                                    <td class="text-end <?= $pengeluaran > 0 ? 'text-danger fw-bold' : '' ?>">
                                        <?= $pengeluaran > 0 ? 'Rp ' . number_format($pengeluaran, 0, ',', '.') : '-' ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="table-active fw-bold">
                                    <td colspan="4" class="text-end">Total Arus Kas dari Aktivitas Investasi</td>
                                    <td class="text-end text-success">
                                        Rp <?= number_format($totalPenerimaanInvestasi, 0, ',', '.') ?>
                                    </td>
                                    <td class="text-end text-danger">
                                        Rp <?= number_format($totalPengeluaranInvestasi, 0, ',', '.') ?>
                                    </td>
                                </tr>
                                <tr class="fw-bold bg-light">
                                    <td colspan="5" class="text-end">
                                        <i class="fas fa-<?= ($total_investasi ?? 0) >= 0 ? 'arrow-up text-success' : 'arrow-down text-danger' ?> me-1"></i>
                                        Arus Kas Bersih dari Investasi
                                    </td>
                                    <td class="text-center <?= ($total_investasi ?? 0) >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <h5 class="mb-0">
                                            <?= ($total_investasi ?? 0) >= 0 ? '+' : '' ?>
                                            Rp <?= number_format($total_investasi ?? 0, 0, ',', '.') ?>
                                        </h5>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Tidak ada transaksi aktivitas investasi yang mempengaruhi kas untuk periode ini.
                    </div>
                    <?php endif; ?>
                </div>

                <!-- AKTIVITAS PENDANAAN -->
                <div class="laporan-section mb-4">
                    <h5 class="section-title bg-success text-white p-2 rounded">
                        <i class="fas fa-hand-holding-usd me-2"></i> ARUS KAS DARI AKTIVITAS PENDANAAN
                        <span class="badge bg-light text-dark float-end">
                            <?= count($transaksi_pendanaan ?? []) ?> transaksi
                        </span>
                    </h5>
                    
                    <?php if (!empty($transaksi_pendanaan)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="8%">Tanggal</th>
                                    <th width="15%">No. Jurnal</th>
                                    <th width="12%">Akun Kas</th>
                                    <th width="35%">Keterangan</th>
                                    <th width="15%" class="text-end">Penerimaan (Rp)</th>
                                    <th width="15%" class="text-end">Pengeluaran (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $totalPenerimaanPendanaan = 0;
                                $totalPengeluaranPendanaan = 0;
                                ?>
                                <?php foreach ($transaksi_pendanaan as $item): ?>
                                <?php
                                $penerimaan = $item['penerimaan'] ?? 0;
                                $pengeluaran = $item['pengeluaran'] ?? 0;
                                $totalPenerimaanPendanaan += $penerimaan;
                                $totalPengeluaranPendanaan += $pengeluaran;
                                
                                $counterpartText = '';
                                if (!empty($item['counterpart'])) {
                                    $counterparts = [];
                                    foreach ($item['counterpart'] as $cp) {
                                        $counterparts[] = $cp['kode_akun'] . ' - ' . $cp['nama_akun'];
                                    }
                                    $counterpartText = implode('; ', $counterparts);
                                }
                                ?>
                                <tr class="highlight-row activity-pendanaan">
                                    <td><?= date('d/m/Y', strtotime($item['tanggal'])) ?></td>
                                    <td>
                                        <span class="badge bg-secondary"><?= $item['nomor_jurnal'] ?? '-' ?></span>
                                        <br>
                                        <small><?= $item['referensi'] ?? '' ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-success"><?= $item['kode_akun_kas'] ?? '' ?></span>
                                        <br>
                                        <small><?= $item['nama_akun_kas'] ?? '' ?></small>
                                    </td>
                                    <td>
                                        <div class="fw-medium"><?= $item['keterangan'] ?? '' ?></div>
                                        <?php if ($counterpartText): ?>
                                        <small class="text-muted">
                                            <i class="fas fa-link text-success me-1"></i>
                                            Counterpart: <?= $counterpartText ?>
                                        </small>
                                        <?php endif; ?>
                                        <br>
                                        <small class="badge bg-light text-dark">Aktivitas: <?= $item['aktivitas'] ?? 'PENDANAAN' ?></small>
                                    </td>
                                    <td class="text-end <?= $penerimaan > 0 ? 'text-success fw-bold' : '' ?>">
                                        <?= $penerimaan > 0 ? 'Rp ' . number_format($penerimaan, 0, ',', '.') : '-' ?>
                                    </td>
                                    <td class="text-end <?= $pengeluaran > 0 ? 'text-danger fw-bold' : '' ?>">
                                        <?= $pengeluaran > 0 ? 'Rp ' . number_format($pengeluaran, 0, ',', '.') : '-' ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="table-active fw-bold">
                                    <td colspan="4" class="text-end">Total Arus Kas dari Aktivitas Pendanaan</td>
                                    <td class="text-end text-success">
                                        Rp <?= number_format($totalPenerimaanPendanaan, 0, ',', '.') ?>
                                    </td>
                                    <td class="text-end text-danger">
                                        Rp <?= number_format($totalPengeluaranPendanaan, 0, ',', '.') ?>
                                    </td>
                                </tr>
                                <tr class="fw-bold bg-light">
                                    <td colspan="5" class="text-end">
                                        <i class="fas fa-<?= ($total_pendanaan ?? 0) >= 0 ? 'arrow-up text-success' : 'arrow-down text-danger' ?> me-1"></i>
                                        Arus Kas Bersih dari Pendanaan
                                    </td>
                                    <td class="text-center <?= ($total_pendanaan ?? 0) >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <h5 class="mb-0">
                                            <?= ($total_pendanaan ?? 0) >= 0 ? '+' : '' ?>
                                            Rp <?= number_format($total_pendanaan ?? 0, 0, ',', '.') ?>
                                        </h5>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Tidak ada transaksi aktivitas pendanaan yang mempengaruhi kas untuk periode ini.
                    </div>
                    <?php endif; ?>
                </div>

                <!-- RINGKASAN PERUBAHAN KAS - SESUAI MODEL -->
                <div class="laporan-section">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="card border-dark shadow-lg">
                                <div class="card-header bg-dark text-white">
                                    <h4 class="mb-0">
                                        <i class="fas fa-calculator me-2"></i> 
                                        RINGKASAN PERUBAHAN KAS
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <td width="70%">
                                                        <i class="fas fa-industry text-info me-2"></i>
                                                        Arus Kas dari Aktivitas Operasi
                                                    </td>
                                                    <td width="30%" class="text-end <?= ($total_operasi ?? 0) >= 0 ? 'text-success' : 'text-danger' ?> fw-bold">
                                                        <?= ($total_operasi ?? 0) >= 0 ? '+' : '' ?>
                                                        Rp <?= number_format($total_operasi ?? 0, 0, ',', '.') ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <i class="fas fa-chart-line text-warning me-2"></i>
                                                        Arus Kas dari Aktivitas Investasi
                                                    </td>
                                                    <td class="text-end <?= ($total_investasi ?? 0) >= 0 ? 'text-success' : 'text-danger' ?> fw-bold">
                                                        <?= ($total_investasi ?? 0) >= 0 ? '+' : '' ?>
                                                        Rp <?= number_format($total_investasi ?? 0, 0, ',', '.') ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <i class="fas fa-hand-holding-usd text-success me-2"></i>
                                                        Arus Kas dari Aktivitas Pendanaan
                                                    </td>
                                                    <td class="text-end <?= ($total_pendanaan ?? 0) >= 0 ? 'text-success' : 'text-danger' ?> fw-bold">
                                                        <?= ($total_pendanaan ?? 0) >= 0 ? '+' : '' ?>
                                                        Rp <?= number_format($total_pendanaan ?? 0, 0, ',', '.') ?>
                                                    </td>
                                                </tr>
                                                <tr class="border-top border-dark">
                                                    <td>
                                                        <h5 class="mb-0">
                                                            <i class="fas fa-exchange-alt text-primary me-2"></i>
                                                            Perubahan Kas Bersih
                                                        </h5>
                                                        <small class="text-muted">(Tanpa Saldo Awal)</small>
                                                    </td>
                                                    <td class="text-end">
                                                        <h4 class="mb-0 fw-bold <?= ($total_arus_kas ?? 0) >= 0 ? 'text-success' : 'text-danger' ?>">
                                                            <?= ($total_arus_kas ?? 0) >= 0 ? '+' : '' ?>
                                                            Rp <?= number_format($total_arus_kas ?? 0, 0, ',', '.') ?>
                                                        </h4>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <i class="fas fa-piggy-bank text-primary me-2"></i>
                                                        Saldo Kas Awal
                                                    </td>
                                                    <td class="text-end fw-bold">
                                                        <?= $saldo_kas['saldo_awal_formatted'] ?? 'Rp 0' ?>
                                                    </td>
                                                </tr>
                                                <tr class="border-top border-dark">
                                                    <td>
                                                        <h5 class="mb-0">
                                                            <i class="fas fa-piggy-bank text-success me-2"></i>
                                                            Saldo Kas Akhir (Perhitungan)
                                                        </h5>
                                                        <small class="text-muted">
                                                            Awal + Perubahan = Akhir
                                                        </small>
                                                    </td>
                                                    <td class="text-end">
                                                        <h3 class="mb-0 fw-bold text-success">
                                                            <?= $saldo_kas['saldo_akhir_formatted'] ?? 'Rp 0' ?>
                                                        </h3>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <!-- VERIFIKASI - LANGSUNG DARI MODEL -->
                            <div class="card border-<?= ($verifikasi['is_valid'] ?? false) ? 'success' : 'danger' ?> h-100">
                                <div class="card-header bg-<?= ($verifikasi['is_valid'] ?? false) ? 'success' : 'danger' ?> text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-<?= ($verifikasi['is_valid'] ?? false) ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i> 
                                        VERIFIKASI KAS
                                    </h5>
                                </div>
                                <div class="card-body d-flex flex-column justify-content-center">
                                    <div class="text-center mb-3">
                                        <i class="fas fa-<?= ($verifikasi['is_valid'] ?? false) ? 'check-circle' : 'times-circle' ?> fa-4x mb-3 text-<?= ($verifikasi['is_valid'] ?? false) ? 'success' : 'danger' ?>"></i>
                                        <h4 class="<?= ($verifikasi['is_valid'] ?? false) ? 'text-success' : 'text-danger' ?>">
                                            <?= ($verifikasi['is_valid'] ?? false) ? 'VALID' : 'TIDAK VALID' ?>
                                        </h4>
                                        <p class="mb-0"><?= $verifikasi['keterangan'] ?? ($verifikasi['is_valid'] ?? false ? 'Laporan valid' : 'Laporan tidak valid') ?></p>
                                    </div>
                                    
                                    <div class="mt-auto">
                                        <div class="alert alert-light">
                                            <small class="text-muted d-block">Formula Verifikasi:</small>
                                            <code class="text-dark">
                                                <?= number_format($verifikasi['saldo_awal'] ?? $saldo_kas['saldo_awal'] ?? 0, 0, ',', '.') ?> 
                                                + 
                                                (<?= number_format($verifikasi['total_arus_kas'] ?? $total_arus_kas ?? 0, 0, ',', '.') ?>)
                                                = 
                                                <?= number_format($verifikasi['saldo_hitung'] ?? ($saldo_kas['saldo_awal'] + $total_arus_kas), 0, ',', '.') ?>
                                            </code>
                                            <div class="mt-2 small">
                                                <strong>Data Buku Besar:</strong><br>
                                                Awal: <?= $saldo_kas['saldo_awal_formatted'] ?? 'Rp 0' ?><br>
                                                Akhir: <?= $saldo_kas['saldo_akhir_formatted'] ?? 'Rp 0' ?><br>
                                                <?php if (($verifikasi['selisih'] ?? 0) > 0): ?>
                                                <span class="text-danger">Selisih: <?= $verifikasi['selisih_formatted'] ?? 'Rp 0' ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DETAIL PER AKUN KAS - DARI MODEL -->
                <?php if (!empty($detail_per_akun)): ?>
                <div class="laporan-section mt-4">
                    <h5 class="section-title bg-secondary text-white p-2 rounded">
                        <i class="fas fa-university me-2"></i> DETAIL PER AKUN KAS
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="12%">Kode Akun</th>
                                    <th width="28%">Nama Akun</th>
                                    <th width="15%" class="text-end">Saldo Awal</th>
                                    <th width="15%" class="text-end">Penerimaan</th>
                                    <th width="15%" class="text-end">Pengeluaran</th>
                                    <th width="15%" class="text-end">Saldo Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $totalSaldoAwalAkun = 0;
                                $totalPenerimaanAkun = 0;
                                $totalPengeluaranAkun = 0;
                                $totalSaldoAkhirAkun = 0;
                                ?>
                                <?php foreach ($detail_per_akun as $akun): ?>
                                <?php
                                $saldoAwal = $akun['saldo_awal'] ?? 0;
                                $penerimaan = $akun['debit_periode'] ?? 0;
                                $pengeluaran = $akun['kredit_periode'] ?? 0;
                                $saldoAkhir = $akun['saldo_akhir'] ?? 0;
                                
                                $totalSaldoAwalAkun += $saldoAwal;
                                $totalPenerimaanAkun += $penerimaan;
                                $totalPengeluaranAkun += $pengeluaran;
                                $totalSaldoAkhirAkun += $saldoAkhir;
                                ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-primary"><?= $akun['kode_akun'] ?? '' ?></span>
                                    </td>
                                    <td><?= $akun['nama_akun'] ?? '' ?></td>
                                    <td class="text-end">
                                        <?= $akun['saldo_awal_formatted'] ?? 'Rp ' . number_format($saldoAwal, 0, ',', '.') ?>
                                    </td>
                                    <td class="text-end text-success">
                                        <?= $akun['debit_periode_formatted'] ?? 'Rp ' . number_format($penerimaan, 0, ',', '.') ?>
                                    </td>
                                    <td class="text-end text-danger">
                                        <?= $akun['kredit_periode_formatted'] ?? 'Rp ' . number_format($pengeluaran, 0, ',', '.') ?>
                                    </td>
                                    <td class="text-end fw-bold">
                                        <?= $akun['saldo_akhir_formatted'] ?? 'Rp ' . number_format($saldoAkhir, 0, ',', '.') ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="table-active fw-bold">
                                    <td colspan="2" class="text-end">TOTAL KAS & BANK</td>
                                    <td class="text-end">
                                        Rp <?= number_format($totalSaldoAwalAkun, 0, ',', '.') ?>
                                    </td>
                                    <td class="text-end text-success">
                                        Rp <?= number_format($totalPenerimaanAkun, 0, ',', '.') ?>
                                    </td>
                                    <td class="text-end text-danger">
                                        Rp <?= number_format($totalPengeluaranAkun, 0, ',', '.') ?>
                                    </td>
                                    <td class="text-end">
                                        Rp <?= number_format($totalSaldoAkhirAkun, 0, ',', '.') ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <!-- CHART ARUS KAS -->
                <?php if (!empty($chart_data['labels'] ?? [])): ?>
                <div class="laporan-section mt-4">
                    <h5 class="section-title bg-primary text-white p-2 rounded">
                        <i class="fas fa-chart-line me-2"></i> TREN ARUS KAS BULANAN
                    </h5>
                    <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
                        <canvas id="arusKasChart"></canvas>
                    </div>
                </div>
                <?php endif; ?>

                <!-- FOOTER INFORMASI -->
                <div class="mt-4 p-3 bg-light rounded">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Transaksi:</span>
                                <span class="fw-bold">
                                    <?= $statistik['total_transaksi'] ?? $total_transaksi ?? 0 ?> transaksi
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Akun Kas:</span>
                                <span class="fw-bold">
                                    <?= $statistik['total_akun_kas'] ?? count($akun_kas ?? []) ?> akun
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Akun Kas Aktif:</span>
                                <span class="fw-bold text-success">
                                    <?= $statistik['akun_kas_aktif'] ?? 0 ?> akun
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Transaksi Operasi:</span>
                                <span class="fw-bold"><?= $total_transaksi_operasi ?? 0 ?> transaksi</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Transaksi Investasi:</span>
                                <span class="fw-bold"><?= $total_transaksi_investasi ?? 0 ?> transaksi</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Transaksi Pendanaan:</span>
                                <span class="fw-bold"><?= $total_transaksi_pendanaan ?? 0 ?> transaksi</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Status Verifikasi:</span>
                                <span class="fw-bold <?= ($verifikasi['is_valid'] ?? false) ? 'text-success' : 'text-danger' ?>">
                                    <?= ($verifikasi['is_valid'] ?? false) ? 'VALID ✓' : 'TIDAK VALID ✗' ?>
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Tanggal Generate:</span>
                                <span class="fw-bold">
                                    <?= date('d/m/Y H:i:s') ?>
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Metode:</span>
                                <span class="fw-bold">
                                    Langsung <?= ($filters['exclude_saldo_awal'] ?? '1') == '1' ? '(Tanpa Saldo Awal)' : '' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- HELP MODAL -->
<div class="modal fade" id="helpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-question-circle me-2"></i> Bantuan Laporan Arus Kas
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 class="fw-bold">Tentang Laporan Arus Kas</h6>
                <p>Laporan Arus Kas menunjukkan aliran kas masuk dan keluar selama periode tertentu, diklasifikasikan ke dalam 3 aktivitas:</p>
                
                <div class="mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-info p-2 me-2">①</span>
                        <strong>Aktivitas Operasi</strong>
                    </div>
                    <p class="text-muted ms-4">Arus kas dari kegiatan utama perusahaan: penjualan, pembelian, beban operasional, dll.</p>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-warning p-2 me-2">②</span>
                        <strong>Aktivitas Investasi</strong>
                    </div>
                    <p class="text-muted ms-4">Arus kas dari pembelian/penjualan aset tetap: peralatan, kendaraan, mesin, dll.</p>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-success p-2 me-2">③</span>
                        <strong>Aktivitas Pendanaan</strong>
                    </div>
                    <p class="text-muted ms-4">Arus kas dari transaksi modal, pinjaman, dan pembayaran dividen.</p>
                </div>
                
                <hr>
                
                <h6 class="fw-bold mt-3">Filter Saldo Awal</h6>
                <p>Transaksi saldo awal <strong class="text-danger">bukan merupakan arus kas periode berjalan</strong>. Gunakan opsi "Filter Saldo Awal = Ya" untuk mendapatkan laporan yang akurat.</p>
                
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Verifikasi:</strong> Saldo Awal + Arus Kas Bersih = Saldo Akhir
                </div>
                
                <h6 class="fw-bold mt-3">Shortcut Keyboard</h6>
                <div class="row">
                    <div class="col-6">
                        <ul class="list-unstyled">
                            <li><span class="badge bg-secondary">Ctrl + P</span> Print</li>
                            <li><span class="badge bg-secondary">Ctrl + E</span> Export CSV</li>
                            <li><span class="badge bg-secondary">Ctrl + V</span> Validasi</li>
                        </ul>
                    </div>
                    <div class="col-6">
                        <ul class="list-unstyled">
                            <li><span class="badge bg-secondary">Ctrl + R</span> Refresh</li>
                            <li><span class="badge bg-secondary">Ctrl + H</span> Bantuan</li>
                            <li><span class="badge bg-secondary">Ctrl + F</span> Fullscreen</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="#" onclick="downloadGuide()" class="btn btn-primary">
                    <i class="fas fa-download me-1"></i> Download Panduan
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// ===== DOM LOADED =====
document.addEventListener('DOMContentLoaded', function() {
    console.log('Laporan Arus Kas - Version: <?= $metadata['version'] ?? '2.0.1' ?>');
    
    // Date validation
    const tanggalMulai = document.getElementById('tanggal_mulai');
    const tanggalSelesai = document.getElementById('tanggal_selesai');
    
    if (tanggalMulai && tanggalSelesai) {
        tanggalMulai.addEventListener('change', function() {
            if (this.value && tanggalSelesai.value && this.value > tanggalSelesai.value) {
                showNotification('Tanggal mulai tidak boleh lebih besar dari tanggal selesai', 'error');
                tanggalSelesai.value = this.value;
            }
        });
        
        tanggalSelesai.addEventListener('change', function() {
            if (this.value && tanggalMulai.value && this.value < tanggalMulai.value) {
                showNotification('Tanggal selesai tidak boleh lebih kecil dari tanggal mulai', 'error');
                tanggalMulai.value = this.value;
            }
        });
    }
    
    // Initialize Chart
    initializeArusKasChart();
    
    // Auto-dismiss alerts
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(alert => {
            if (alert.classList.contains('alert-dismissible')) {
                const bsAlert = new bootstrap.Alert(alert);
                setTimeout(() => bsAlert.close(), 5000);
            }
        });
    }, 5000);
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// ===== CHART INITIALIZATION =====
function initializeArusKasChart() {
    const chartData = <?= json_encode($chart_data ?? []) ?>;
    
    if (chartData && chartData.labels && chartData.labels.length > 0) {
        const ctx = document.getElementById('arusKasChart');
        if (!ctx) {
            console.log('Chart element not found');
            return;
        }
        
        try {
            new Chart(ctx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: chartData.datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { grid: { display: false } },
                        y: { 
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            },
                            grid: { borderDash: [2, 2] }
                        }
                    },
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) label += ': ';
                                    label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
            console.log('Chart initialized successfully');
        } catch (e) {
            console.error('Chart initialization error:', e);
        }
    } else {
        console.log('No chart data available');
    }
}

// ===== QUICK PERIOD =====
function applyQuickPeriod(period) {
    const today = new Date();
    let startDate = '';
    let endDate = '';
    
    switch(period) {
        case 'today':
            startDate = endDate = formatDate(today);
            break;
        case 'yesterday':
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);
            startDate = endDate = formatDate(yesterday);
            break;
        case 'this_week':
            const firstDay = new Date(today.setDate(today.getDate() - today.getDay() + 1));
            const lastDay = new Date(today.setDate(today.getDate() - today.getDay() + 7));
            startDate = formatDate(firstDay);
            endDate = formatDate(lastDay);
            break;
        case 'last_week':
            const lastWeekFirst = new Date(today);
            lastWeekFirst.setDate(lastWeekFirst.getDate() - lastWeekFirst.getDay() - 6);
            const lastWeekLast = new Date(today);
            lastWeekLast.setDate(lastWeekLast.getDate() - lastWeekLast.getDay());
            startDate = formatDate(lastWeekFirst);
            endDate = formatDate(lastWeekLast);
            break;
        case 'this_month':
            startDate = formatDate(new Date(today.getFullYear(), today.getMonth(), 1));
            endDate = formatDate(new Date(today.getFullYear(), today.getMonth() + 1, 0));
            break;
        case 'last_month':
            startDate = formatDate(new Date(today.getFullYear(), today.getMonth() - 1, 1));
            endDate = formatDate(new Date(today.getFullYear(), today.getMonth(), 0));
            break;
        case 'this_quarter':
            const quarter = Math.floor((today.getMonth() / 3));
            const quarterStartMonth = quarter * 3;
            startDate = formatDate(new Date(today.getFullYear(), quarterStartMonth, 1));
            endDate = formatDate(new Date(today.getFullYear(), quarterStartMonth + 3, 0));
            break;
        case 'last_quarter':
            const lastQuarter = Math.floor((today.getMonth() / 3)) - 1;
            const lastQuarterStartMonth = lastQuarter * 3;
            startDate = formatDate(new Date(today.getFullYear(), lastQuarterStartMonth, 1));
            endDate = formatDate(new Date(today.getFullYear(), lastQuarterStartMonth + 3, 0));
            break;
        case 'this_year':
            startDate = formatDate(new Date(today.getFullYear(), 0, 1));
            endDate = formatDate(new Date(today.getFullYear(), 11, 31));
            break;
        case 'last_year':
            startDate = formatDate(new Date(today.getFullYear() - 1, 0, 1));
            endDate = formatDate(new Date(today.getFullYear() - 1, 11, 31));
            break;
        default:
            if (period && period.startsWith('period_')) {
                const periodDate = period.replace('period_', '');
                startDate = periodDate;
                endDate = new Date(new Date(periodDate).getFullYear(), new Date(periodDate).getMonth() + 1, 0).toISOString().split('T')[0];
            }
    }
    
    if (startDate && endDate) {
        document.getElementById('tanggal_mulai').value = startDate;
        document.getElementById('tanggal_selesai').value = endDate;
        document.getElementById('quick_period').value = '';
        document.getElementById('filterForm').submit();
    }
}

// ===== FORMAT DATE =====
function formatDate(date) {
    const d = new Date(date);
    const month = '' + (d.getMonth() + 1);
    const day = '' + d.getDate();
    const year = d.getFullYear();
    return [year, month.padStart(2, '0'), day.padStart(2, '0')].join('-');
}

// ===== REFRESH SUMMARY =====
function refreshSummary() {
    const startDate = document.getElementById('tanggal_mulai').value;
    const endDate = document.getElementById('tanggal_selesai').value;
    const excludeSaldoAwal = document.getElementById('exclude_saldo_awal').value;
    
    showNotification('Memperbarui summary...', 'info');
    
    fetch('<?= site_url("accounting/laporan-keuangan/arus-kas/ajax-get-summary") ?>?tanggal_mulai=' + startDate + '&tanggal_selesai=' + endDate + '&exclude_saldo_awal=' + excludeSaldoAwal, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Summary berhasil diperbarui', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('Gagal: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Refresh Summary Error:', error);
        showNotification('Terjadi kesalahan saat memperbarui summary', 'error');
    });
}

// ===== VALIDATE ARUS KAS =====
function validateArusKas() {
    const startDate = document.getElementById('tanggal_mulai').value;
    const endDate = document.getElementById('tanggal_selesai').value;
    const excludeSaldoAwal = document.getElementById('exclude_saldo_awal').value;
    
    showNotification('Memvalidasi data arus kas...', 'info');
    
    fetch('<?= site_url("accounting/laporan-keuangan/arus-kas/ajax-validate") ?>?tanggal_mulai=' + startDate + '&tanggal_selesai=' + endDate + '&exclude_saldo_awal=' + excludeSaldoAwal, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.is_valid) {
                showNotification('✓ ' + data.message, 'success');
            } else {
                showNotification('✗ ' + data.message, 'warning');
            }
            
            if (data.has_saldo_awal_error) {
                showNotification('⚠ Terdeteksi transaksi saldo awal dalam laporan', 'warning');
            }
        } else {
            showNotification('Gagal validasi: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Validate Error:', error);
        showNotification('Terjadi kesalahan saat validasi', 'error');
    });
}

// ===== EXPORT =====
function exportLaporan(type) {
    const startDate = document.getElementById('tanggal_mulai').value;
    const endDate = document.getElementById('tanggal_selesai').value;
    const excludeSaldoAwal = document.getElementById('exclude_saldo_awal').value;
    
    const exportUrl = '<?= site_url("accounting/laporan-keuangan/arus-kas/export") ?>' + 
        '?tanggal_mulai=' + startDate + 
        '&tanggal_selesai=' + endDate + 
        '&exclude_saldo_awal=' + excludeSaldoAwal + 
        '&type=' + type;
    
    window.location.href = exportUrl;
    showNotification('Mengekspor laporan ke ' + type.toUpperCase() + '...', 'info');
}

// ===== PRINT =====
function printLaporan() {
    const startDate = document.getElementById('tanggal_mulai').value;
    const endDate = document.getElementById('tanggal_selesai').value;
    const excludeSaldoAwal = document.getElementById('exclude_saldo_awal').value;
    
    const printUrl = '<?= site_url("accounting/laporan-keuangan/arus-kas/print") ?>' + 
        '?tanggal_mulai=' + startDate + 
        '&tanggal_selesai=' + endDate + 
        '&exclude_saldo_awal=' + excludeSaldoAwal;
    
    window.open(printUrl, '_blank');
    showNotification('Membuka laporan untuk dicetak...', 'info');
}

// ===== FULLSCREEN =====
function toggleFullscreen() {
    const elem = document.documentElement;
    
    if (!document.fullscreenElement) {
        if (elem.requestFullscreen) {
            elem.requestFullscreen();
        } else if (elem.webkitRequestFullscreen) {
            elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) {
            elem.msRequestFullscreen();
        }
        showNotification('Mode fullscreen diaktifkan', 'info');
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
        showNotification('Mode fullscreen dinonaktifkan', 'info');
    }
}

// ===== HELP MODAL =====
function showHelpModal() {
    const helpModal = new bootstrap.Modal(document.getElementById('helpModal'));
    helpModal.show();
}

// ===== DOWNLOAD GUIDE =====
function downloadGuide() {
    showNotification('Panduan akan segera tersedia', 'info');
}

// ===== DEBUG INFO =====
function showDebugInfo() {
    const debugInfo = <?= json_encode($debug_info ?? [], JSON_HEX_TAG) ?>;
    
    if (debugInfo && Object.keys(debugInfo).length > 0) {
        const debugModal = document.createElement('div');
        debugModal.className = 'modal fade';
        debugModal.id = 'debugModal';
        debugModal.innerHTML = `
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-bug me-2"></i> Arus Kas Debug Info (${debugInfo.version || '2.0.1'})
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <pre class="bg-light p-3" style="max-height: 500px; overflow-y: auto;">${JSON.stringify(debugInfo, null, 2)}</pre>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <a href="<?= site_url('accounting/laporan-keuangan/arus-kas/debug') ?>?tanggal_mulai=<?= $filters['tanggal_mulai'] ?? date('Y-m-01') ?>&tanggal_selesai=<?= $filters['tanggal_selesai'] ?? date('Y-m-t') ?>&exclude_saldo_awal=<?= $filters['exclude_saldo_awal'] ?? '1' ?>" 
                           class="btn btn-primary" target="_blank">
                            <i class="fas fa-external-link-alt me-1"></i> Open Detailed Debug
                        </a>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(debugModal);
        const bsModal = new bootstrap.Modal(debugModal);
        bsModal.show();
        
        debugModal.addEventListener('hidden.bs.modal', function() {
            document.body.removeChild(debugModal);
        });
    } else {
        showNotification('Tidak ada informasi debug', 'info');
    }
}

// ===== NOTIFICATION =====
function showNotification(message, type = 'info') {
    const alertClass = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    }[type] || 'alert-info';
    
    const icon = {
        'success': 'fa-check-circle',
        'error': 'fa-exclamation-circle',
        'warning': 'fa-exclamation-triangle',
        'info': 'fa-info-circle'
    }[type] || 'fa-info-circle';
    
    const existingAlerts = document.querySelectorAll('.notification-alert');
    existingAlerts.forEach(alert => alert.remove());
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert ${alertClass} alert-dismissible fade show notification-alert position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px; animation: slideIn 0.3s ease-out;';
    alertDiv.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas ${icon} fa-lg me-2"></i>
            <div>${message}</div>
            <button type="button" class="btn-close ms-3" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.classList.add('fade');
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 300);
        }
    }, 5000);
}

// ===== KEYBOARD SHORTCUTS =====
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'p') {
        e.preventDefault();
        printLaporan();
    }
    
    if (e.ctrlKey && e.key === 'e') {
        e.preventDefault();
        exportLaporan('csv');
    }
    
    if (e.ctrlKey && e.key === 'v') {
        e.preventDefault();
        validateArusKas();
    }
    
    if (e.ctrlKey && e.key === 'h') {
        e.preventDefault();
        showHelpModal();
    }
    
    if (e.ctrlKey && e.key === 'f') {
        e.preventDefault();
        toggleFullscreen();
    }
    
    if (e.ctrlKey && e.key === 'r') {
        e.preventDefault();
        refreshSummary();
    }
});

// ===== FULLSCREEN CHANGE =====
document.addEventListener('fullscreenchange', handleFullscreenChange);
document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
document.addEventListener('mozfullscreenchange', handleFullscreenChange);
document.addEventListener('MSFullscreenChange', handleFullscreenChange);

function handleFullscreenChange() {
    const fullscreenBtn = document.querySelector('[onclick="toggleFullscreen()"]');
    if (fullscreenBtn) {
        if (document.fullscreenElement || document.webkitFullscreenElement || 
            document.mozFullScreenElement || document.msFullscreenElement) {
            fullscreenBtn.innerHTML = '<i class="fas fa-compress me-1"></i> Exit Fullscreen';
            fullscreenBtn.classList.remove('btn-outline-secondary');
            fullscreenBtn.classList.add('btn-warning');
        } else {
            fullscreenBtn.innerHTML = '<i class="fas fa-expand me-1"></i> Fullscreen';
            fullscreenBtn.classList.remove('btn-warning');
            fullscreenBtn.classList.add('btn-outline-secondary');
        }
    }
}
</script>

<style>
/* Arus Kas Specific Styles */
.modern-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    border: none;
    transition: all 0.3s;
}

.modern-card:hover {
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.card-title {
    color: #2c5aa0;
    font-weight: 600;
    border-bottom: 2px solid #f8f9fa;
    padding-bottom: 10px;
}

.laporan-section {
    margin-bottom: 30px;
    animation: fadeIn 0.5s ease-out;
}

.section-title {
    font-weight: 600;
    margin-bottom: 15px;
    border-left: 4px solid currentColor;
    padding-left: 15px;
}

/* Activity colors */
.activity-operasi {
    border-left: 3px solid #17a2b8 !important;
}

.activity-investasi {
    border-left: 3px solid #ffc107 !important;
}

.activity-pendanaan {
    border-left: 3px solid #28a745 !important;
}

/* Table hover effects */
.highlight-row {
    transition: all 0.2s;
}

.highlight-row:hover {
    background-color: rgba(44, 90, 160, 0.05);
    transform: translateX(3px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

/* Border utilities */
.border-start-3 {
    border-left-width: 3px !important;
}

/* Animations */
@keyframes slideIn {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Chart container */
.chart-container {
    background: white;
    border-radius: 8px;
    padding: 15px;
    border: 1px solid #eaeaea;
}

/* Responsive */
@media (max-width: 768px) {
    .btn-group {
        flex-wrap: wrap;
        margin-top: 10px;
    }
    
    .btn-group .btn {
        margin-bottom: 5px;
    }
    
    .table-responsive {
        font-size: 0.85rem;
    }
    
    .modern-card {
        padding: 15px;
    }
}

/* Print styles */
@media print {
    .modern-card, .card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
    
    .btn, .dropdown, .modal, .alert:not(.alert-permanent), 
    .filter-section, .notification-alert, .btn-group,
    [onclick], .d-print-none {
        display: none !important;
    }
    
    .table {
        border: 1px solid #000;
    }
    
    .table th, .table td {
        border: 1px solid #000;
    }
    
    .laporan-section {
        page-break-inside: avoid;
    }
    
    .container-fluid {
        padding: 0 !important;
    }
}

/* Custom scrollbar */
.table-responsive::-webkit-scrollbar {
    height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Text colors for cash flow */
.text-cash-in {
    color: #28a745 !important;
}

.text-cash-out {
    color: #dc3545 !important;
}

.bg-cash-in {
    background-color: rgba(40, 167, 69, 0.1) !important;
}

.bg-cash-out {
    background-color: rgba(220, 53, 69, 0.1) !important;
}

/* Validation status */
.validation-success {
    animation: pulseSuccess 2s infinite;
}

.validation-error {
    animation: pulseError 2s infinite;
}

@keyframes pulseSuccess {
    0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4); }
    70% { box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
    100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
}

@keyframes pulseError {
    0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); }
    70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
    100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
}
</style>

<?= $this->include('accounting/templates/footer') ?>