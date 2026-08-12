<div class="main-content">
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 text-gradient-accounting">Pelepasan & Penjualan Aset Tetap</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-2">
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Aset Tetap</li>
                    <li class="breadcrumb-item active">Pelepasan Aset</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <a href="<?= site_url('accounting/aset-tetap/pelepasan-aset/create') ?>" class="btn btn-accounting">
                <i class="fas fa-plus me-1"></i> Ajukan Pelepasan / Penjualan Aset
            </a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 border-left-primary">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary me-3">
                        <i class="fas fa-hand-holding-usd fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Pelepasan Aset</div>
                        <h4 class="mb-0 fw-bold text-dark"><?= count($pelepasan ?? []) ?> <small class="fs-6 text-muted">transaksi</small></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 border-left-info">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3 text-info me-3">
                        <i class="fas fa-dollar-sign fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Realisasi Penjualan</div>
                        <h4 class="mb-0 fw-bold text-info">Rp <?= number_format(array_sum(array_column($pelepasan ?? [], 'harga_jual')), 0, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 border-left-success">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success me-3">
                        <i class="fas fa-balance-scale fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Laba / (Rugi) Pelepasan</div>
                        <h4 class="mb-0 fw-bold text-success">Rp <?= number_format(array_sum(array_column($pelepasan ?? [], 'laba_rugi_pelepasan')), 0, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table & List -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-file-invoice-dollar me-2 text-primary"></i> Register Pelepasan Aset</h6>
        </div>
        <div class="card-body">
            <?php if (empty($pelepasan)): ?>
                <div class="alert alert-info text-center py-5">
                    <i class="fas fa-archive fa-3x mb-3 d-block text-info"></i>
                    <h5>Belum Ada Data Pelepasan Aset</h5>
                    <p class="text-muted mb-0">Belum ada transaksi pelepasan, penjualan, hibah, atau pemusnahan aset yang dicatat.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Kode Pelepasan</th>
                                <th>Tanggal</th>
                                <th>Jenis Pelepasan</th>
                                <th class="text-end">Harga Jual</th>
                                <th class="text-end">Nilai Buku</th>
                                <th class="text-end">Laba / (Rugi)</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pelepasan as $index => $row): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><span class="badge bg-secondary"><?= esc($row['kode_pelepasan']) ?></span></td>
                                    <td><?= esc($row['tanggal_pelepasan']) ?></td>
                                    <td><span class="badge bg-info"><?= esc($row['jenis_pelepasan']) ?></span></td>
                                    <td class="text-end">Rp <?= number_format($row['harga_jual'], 0, ',', '.') ?></td>
                                    <td class="text-end">Rp <?= number_format($row['nilai_buku_saat_pelepasan'], 0, ',', '.') ?></td>
                                    <td class="text-end fw-bold <?= $row['laba_rugi_pelepasan'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                        Rp <?= number_format($row['laba_rugi_pelepasan'], 0, ',', '.') ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success"><?= esc($row['status']) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</div>
