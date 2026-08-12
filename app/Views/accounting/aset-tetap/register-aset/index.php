<div class="main-content">
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 text-gradient-accounting">Register Aset Tetap</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-2">
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Aset Tetap</li>
                    <li class="breadcrumb-item active">Register Aset</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-accounting me-2" data-bs-toggle="modal" data-bs-target="#modalTambahAset">
                <i class="fas fa-plus me-1"></i> Tambah Aset Baru
            </button>
            <button type="button" class="btn btn-accounting-outline">
                <i class="fas fa-sync-alt me-1"></i> Sinkron Pengadaan Direktur
            </button>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 border-left-primary">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary me-3">
                        <i class="fas fa-boxes fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Aset</div>
                        <h4 class="mb-0 fw-bold text-dark"><?= count($aset ?? []) ?> <small class="fs-6 text-muted">unit</small></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 border-left-success">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success me-3">
                        <i class="fas fa-coins fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Harga Perolehan</div>
                        <h4 class="mb-0 fw-bold text-success">Rp <?= number_format(array_sum(array_column($aset ?? [], 'harga_perolehan')), 0, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 border-left-warning">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 text-warning me-3">
                        <i class="fas fa-chart-line-down fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Akumulasi Penyusutan</div>
                        <h4 class="mb-0 fw-bold text-warning">Rp <?= number_format(array_sum(array_column($aset ?? [], 'akumulasi_penyusutan')), 0, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 border-left-info">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3 text-info me-3">
                        <i class="fas fa-wallet fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Nilai Buku</div>
                        <h4 class="mb-0 fw-bold text-info">Rp <?= number_format(array_sum(array_column($aset ?? [], 'nilai_buku')), 0, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table & Filters -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-list me-2 text-primary"></i> Daftar Register Aset Tetap</h6>
        </div>
        <div class="card-body">
            <?php if (empty($aset)): ?>
                <div class="alert alert-info text-center py-5">
                    <i class="fas fa-box-open fa-3x mb-3 d-block text-info"></i>
                    <h5>Belum Ada Data Aset Tetap</h5>
                    <p class="text-muted mb-0">Belum ada aset tetap yang terdaftar di sistem. Anda dapat menambah aset baru atau menyinkronkan pengadaan yang telah disetujui Direktur.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Kode Aset</th>
                                <th>Nama Aset</th>
                                <th>Tanggal Perolehan</th>
                                <th class="text-end">Harga Perolehan</th>
                                <th class="text-end">Akum. Penyusutan</th>
                                <th class="text-end">Nilai Buku</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($aset as $index => $row): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><span class="badge bg-secondary"><?= esc($row['kode_aset']) ?></span></td>
                                    <td class="fw-bold"><?= esc($row['nama_aset']) ?></td>
                                    <td><?= esc($row['tanggal_perolehan']) ?></td>
                                    <td class="text-end">Rp <?= number_format($row['harga_perolehan'], 0, ',', '.') ?></td>
                                    <td class="text-end text-danger">Rp <?= number_format($row['akumulasi_penyusutan'], 0, ',', '.') ?></td>
                                    <td class="text-end fw-bold text-success">Rp <?= number_format($row['nilai_buku'], 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-<?= $row['status'] === 'Aktif' ? 'success' : 'warning' ?>"><?= esc($row['status']) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-eye"></i></button>
                                        <button class="btn btn-sm btn-outline-warning me-1"><i class="fas fa-edit"></i></button>
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
