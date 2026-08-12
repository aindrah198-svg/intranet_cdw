<div class="main-content">
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 text-gradient-accounting">Penyusutan Aset Tetap</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-2">
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Aset Tetap</li>
                    <li class="breadcrumb-item active">Penyusutan</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <a href="<?= site_url('accounting/aset-tetap/penyusutan/proses') ?>" class="btn btn-accounting">
                <i class="fas fa-calculator me-1"></i> Hitung & Proses Penyusutan Bulanan
            </a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 border-left-primary">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary me-3">
                        <i class="fas fa-calculator fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Transaksi Penyusutan</div>
                        <h4 class="mb-0 fw-bold text-dark"><?= count($penyusutan ?? []) ?> <small class="fs-6 text-muted">catatan</small></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 border-left-danger">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3 text-danger me-3">
                        <i class="fas fa-minus-circle fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Nominal Disusutkan</div>
                        <h4 class="mb-0 fw-bold text-danger">Rp <?= number_format(array_sum(array_column($penyusutan ?? [], 'nominal')), 0, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 border-left-success">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success me-3">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Status Jurnal Otomatis</div>
                        <h4 class="mb-0 fw-bold text-success">Terintegrasi COA</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table & List -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-history me-2 text-primary"></i> Riwayat Penyusutan Aset</h6>
        </div>
        <div class="card-body">
            <?php if (empty($penyusutan)): ?>
                <div class="alert alert-info text-center py-5">
                    <i class="fas fa-chart-line-down fa-3x mb-3 d-block text-info"></i>
                    <h5>Belum Ada Riwayat Penyusutan</h5>
                    <p class="text-muted mb-0">Penyusutan aset belum dijalankan untuk periode ini. Klik tombol di atas untuk memproses penyusutan aset bulanan secara otomatis.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Kode Penyusutan</th>
                                <th>Periode</th>
                                <th>Tanggal</th>
                                <th>Kode Aset</th>
                                <th class="text-end">Nominal Penyusutan</th>
                                <th class="text-center">Status Jurnal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($penyusutan as $index => $row): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><span class="badge bg-secondary"><?= esc($row['kode_penyusutan']) ?></span></td>
                                    <td><?= esc($row['periode_bulan']) ?>/<?= esc($row['periode_tahun']) ?></td>
                                    <td><?= esc($row['tanggal_penyusutan']) ?></td>
                                    <td><?= esc($row['aset_id']) ?></td>
                                    <td class="text-end text-danger fw-bold">Rp <?= number_format($row['nominal'], 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-success"><?= esc($row['status'] ?? 'Posted') ?></span>
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
