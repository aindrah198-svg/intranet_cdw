<?= $this->include('accounting/templates/header') ?>
<?= $this->include('accounting/templates/sidebar') ?>
<?= $this->include('accounting/templates/navbar') ?>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="page-title mb-1">Pengeluaran Pribadi</h2>
                    <p class="page-subtitle text-muted mb-0">Pengeluaran untuk kepentingan pribadi (Kasbon, Reimbursement, Prive)</p>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" 
                            aria-expanded="false">
                        <i class="fas fa-plus-circle me-1"></i> Tambah Baru
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <h6 class="dropdown-header">Pilih Jenis</h6>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi/create?jenis=Kasbon') ?>">
                                <i class="fas fa-hand-holding-usd text-primary me-2"></i> Kasbon
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi/create?jenis=Reimbursement') ?>">
                                <i class="fas fa-undo-alt text-success me-2"></i> Reimbursement
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi/create?jenis=Dana Talangan') ?>">
                                <i class="fas fa-hand-holding-heart text-info me-2"></i> Dana Talangan
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi/create?jenis=Klaim Pribadi') ?>">
                                <i class="fas fa-file-invoice text-warning me-2"></i> Klaim Pribadi
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi/create?jenis=Prive') ?>">
                                <i class="fas fa-user-tie text-secondary me-2"></i> Prive
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi/create?jenis=Lainnya') ?>">
                                <i class="fas fa-ellipsis-h text-dark me-2"></i> Lainnya
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Export Button -->
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" 
                            aria-expanded="false">
                        <i class="fas fa-download me-1"></i> Export
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <h6 class="dropdown-header">Export Data</h6>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="exportWithFilters('excel')">
                                <i class="fas fa-file-excel text-success me-2"></i> Excel (.xlsx)
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="exportWithFilters('pdf')">
                                <i class="fas fa-file-pdf text-danger me-2"></i> PDF (.pdf)
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start-primary border-start-3 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                Total Transaksi
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                <?= number_format($stats['total_transaksi'] ?? 0, 0) ?>
                            </div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-primary me-2">
                                    <i class="fas fa-exchange-alt"></i>
                                </span>
                                <span>Periode filter</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exchange-alt fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start-success border-start-3 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                Total Nominal
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                Rp <?= number_format($stats['total_nominal'] ?? 0, 0, ',', '.') ?>
                            </div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-success me-2">
                                    <i class="fas fa-money-bill"></i>
                                </span>
                                <span>Nilai total</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start-warning border-start-3 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                Sisa Hutang
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                Rp <?= number_format($stats['total_sisa_hutang'] ?? 0, 0, ',', '.') ?>
                            </div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-warning me-2">
                                    <i class="fas fa-clock"></i>
                                </span>
                                <span>Belum dibayar</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start-info border-start-3 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                Transaksi Hari Ini
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                <?= $stats['transaksi_hari_ini'] ?? 0 ?>
                            </div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-info me-2">
                                    <i class="fas fa-calendar"></i>
                                </span>
                                <span><?= date('d M Y') ?></span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row Stats - Breakdown by Status -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                Belum Dibayar
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                <?= $stats['belum_dibayar'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hourglass-half fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                Sebagian
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                <?= $stats['sebagian'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-adjust fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                Lunas
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                <?= $stats['lunas'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-secondary text-uppercase mb-1">
                                Jml Kasbon
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                <?= $stats['jumlah_kasbon'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-usd fa-2x text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Hutang per Karyawan -->
    <?php if (!empty($ringkasanHutang)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="modern-card">
                <h5 class="card-title mb-3">
                    <i class="fas fa-users me-2"></i> Ringkasan Hutang per Karyawan
                    <span class="badge bg-warning ms-2">Belum Lunas</span>
                </h5>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">NIK</th>
                                <th width="20%">Nama Karyawan</th>
                                <th width="15%">Jabatan</th>
                                <th width="10%" class="text-end">Total Hutang</th>
                                <th width="10%" class="text-end">Total Dibayar</th>
                                <th width="10%" class="text-end">Sisa Hutang</th>
                                <th width="10%">Jml Transaksi</th>
                                <th width="5%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            $totalHutangAll = 0;
                            $totalDibayarAll = 0;
                            $totalSisaAll = 0;
                            foreach ($ringkasanHutang as $item): 
                                $totalHutangAll += $item['total_hutang'];
                                $totalDibayarAll += $item['total_dibayar'];
                                $totalSisaAll += $item['total_sisa'];
                            ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td><?= esc($item['nik'] ?? '-') ?></td>
                                <td><strong><?= esc($item['nama_lengkap'] ?? '-') ?></strong></td>
                                <td><?= esc($item['jabatan'] ?? '-') ?></td>
                                <td class="text-end">Rp <?= number_format($item['total_hutang'], 0, ',', '.') ?></td>
                                <td class="text-end">Rp <?= number_format($item['total_dibayar'], 0, ',', '.') ?></td>
                                <td class="text-end text-danger fw-bold">Rp <?= number_format($item['total_sisa'], 0, ',', '.') ?></td>
                                <td class="text-center"><?= $item['jumlah_transaksi'] ?></td>
                                <td>
                                    <a href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi?karyawan_id=' . $item['karyawan_id'] . '&status_hutang=Belum+Lunas') ?>" 
                                       class="btn btn-sm btn-info" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="4" class="text-end">TOTAL</th>
                                <th class="text-end">Rp <?= number_format($totalHutangAll, 0, ',', '.') ?></th>
                                <th class="text-end">Rp <?= number_format($totalDibayarAll, 0, ',', '.') ?></th>
                                <th class="text-end text-danger">Rp <?= number_format($totalSisaAll, 0, ',', '.') ?></th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="mt-2 text-end">
                    <a href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi/laporan-hutang-karyawan') ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-chart-pie me-1"></i> Laporan Hutang Lengkap
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="modern-card">
                <h5 class="card-title mb-3">
                    <i class="fas fa-filter me-2"></i> Filter Data
                </h5>
                <form method="get" action="<?= site_url('accounting/kas-bank/pengeluaran-pribadi') ?>" id="filterForm">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="search" class="form-label">Pencarian</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   placeholder="No. pengeluaran, keterangan..." value="<?= esc($filters['search'] ?? '') ?>">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai"
                                   value="<?= $filters['tanggal_mulai'] ?? '' ?>">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai"
                                   value="<?= $filters['tanggal_selesai'] ?? '' ?>">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="jenis" class="form-label">Jenis</label>
                            <select class="form-select" id="jenis" name="jenis">
                                <option value="">Semua</option>
                                <?php foreach ($jenisOptions as $key => $jenis): ?>
                                <option value="<?= $key ?>" <?= ($filters['jenis'] ?? '') == $key ? 'selected' : '' ?>><?= $jenis ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="status" class="form-label">Status Transaksi</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Semua</option>
                                <?php foreach ($statusOptions as $status): ?>
                                <option value="<?= $status ?>" <?= ($filters['status'] ?? '') == $status ? 'selected' : '' ?>><?= $status ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="status_hutang" class="form-label">Status Hutang</label>
                            <select class="form-select" id="status_hutang" name="status_hutang">
                                <option value="">Semua</option>
                                <?php foreach ($statusHutangOptions as $status): ?>
                                <option value="<?= $status ?>" <?= ($filters['status_hutang'] ?? '') == $status ? 'selected' : '' ?>><?= $status ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="karyawan_id" class="form-label">Karyawan</label>
                            <select class="form-select" id="karyawan_id" name="karyawan_id">
                                <option value="">Semua Karyawan</option>
                                <?php foreach ($karyawanOptions as $karyawan): ?>
                                <option value="<?= $karyawan['id'] ?>" <?= ($filters['karyawan_id'] ?? '') == $karyawan['id'] ? 'selected' : '' ?>>
                                    <?= esc($karyawan['nik']) ?> - <?= esc($karyawan['nama_lengkap']) ?> (<?= esc($karyawan['jabatan'] ?? '-') ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-5 mb-3 d-flex align-items-end">
                            <div class="d-flex justify-content-end w-100">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i> Terapkan Filter
                                </button>
                                <a href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi') ?>" class="btn btn-secondary">
                                    <i class="fas fa-redo me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Quick Filter Tabs -->
    <div class="row mb-3">
        <div class="col-12">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link <?= (($filters['status'] ?? '') == '' && ($filters['status_hutang'] ?? '') == '') ? 'active' : '' ?>" 
                       href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi') ?>">
                        <i class="fas fa-list me-1"></i> Semua
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (($filters['status'] ?? '') == 'Draft') ? 'active' : '' ?>" 
                       href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi/draft') ?>">
                        <i class="fas fa-pen me-1"></i> Draft
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (($filters['status'] ?? '') == 'Posted') ? 'active' : '' ?>" 
                       href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi/posted') ?>">
                        <i class="fas fa-check-circle me-1"></i> Posted
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (($filters['status_hutang'] ?? '') == 'Belum Dibayar') ? 'active' : '' ?>" 
                       href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi/hutang-belum-dibayar') ?>">
                        <i class="fas fa-hourglass-half me-1 text-warning"></i> Belum Dibayar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (($filters['status_hutang'] ?? '') == 'Lunas') ? 'active' : '' ?>" 
                       href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi/hutang-lunas') ?>">
                        <i class="fas fa-check-circle me-1 text-success"></i> Lunas
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button">
                        <i class="fas fa-filter me-1"></i> Filter Jenis
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi/kasbon') ?>">Kasbon</a></li>
                        <li><a class="dropdown-item" href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi/reimbursement') ?>">Reimbursement</a></li>
                        <li><a class="dropdown-item" href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi/prive') ?>">Prive</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>

    <!-- Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="modern-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-tie me-2"></i> Daftar Pengeluaran Pribadi
                        <span class="badge bg-info ms-2"><?= $total ?? 0 ?> Data</span>
                    </h5>
                    <div class="d-flex align-items-center">
                        <!-- Bulk Actions -->
                        <div class="dropdown me-2" id="bulkActions" style="display: none;">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-tasks me-1"></i> Aksi Massal
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="#" onclick="bulkPost()">
                                        <i class="fas fa-check-double text-success me-2"></i> Posting Terpilih
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="bulkDelete()">
                                        <i class="fas fa-trash text-danger me-2"></i> Hapus Terpilih
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="me-3">
                            <small class="text-muted">Menampilkan <?= count($pengeluaran) ?> dari <?= $total ?? 0 ?> data</small>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAll">
                            <label class="form-check-label" for="selectAll">
                                Pilih Semua
                            </label>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="3%">
                                    <input class="form-check-input" type="checkbox" id="selectAllHeader">
                                </th>
                                <th width="4%">No</th>
                                <th width="8%">Tanggal</th>
                                <th width="10%">Kode</th>
                                <th width="10%">Karyawan</th>
                                <th width="8%">Jenis</th>
                                <th width="8%" class="text-end">Jumlah</th>
                                <th width="8%" class="text-end">Dibayar</th>
                                <th width="8%" class="text-end">Sisa</th>
                                <th width="10%">Akun Debit</th>
                                <th width="10%">Akun Kredit</th>
                                <th width="8%">No Bukti</th>
                                <th width="6%">Status</th>
                                <th width="6%">Hutang</th>
                                <th width="8%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pengeluaran)): ?>
                            <tr>
                                <td colspan="15" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-user-tie fa-2x mb-3"></i>
                                        <h5>Tidak ada data pengeluaran pribadi</h5>
                                        <p>Silakan buat pengeluaran baru atau ubah filter pencarian.</p>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                                                <i class="fas fa-plus-circle me-1"></i> Tambah Baru
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi/create?jenis=Kasbon') ?>">Kasbon</a></li>
                                                <li><a class="dropdown-item" href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi/create?jenis=Reimbursement') ?>">Reimbursement</a></li>
                                                <li><a class="dropdown-item" href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi/create?jenis=Prive') ?>">Prive</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php 
                                $start = ($currentPage - 1) * $perPage + 1;
                                foreach ($pengeluaran as $i => $item): 
                                    $sisa = $item['jumlah'] - ($item['jumlah_dibayar'] ?? 0);
                                    $sisaClass = $sisa == 0 ? 'text-success' : ($sisa < $item['jumlah'] ? 'text-warning' : 'text-danger');
                                ?>
                                <tr id="row-<?= $item['id'] ?>" class="<?= $sisa > 0 ? 'table-warning' : '' ?>">
                                    <td class="text-center">
                                        <input class="form-check-input row-select" type="checkbox" value="<?= $item['id'] ?>" 
                                               <?= $item['status'] != 'Draft' ? 'disabled' : '' ?>>
                                    </td>
                                    <td><?= $start + $i ?></td>
                                    <td><?= date('d/m/Y', strtotime($item['tanggal'])) ?></td>
                                    <td>
                                        <strong><?= esc($item['kode_pengeluaran']) ?></strong>
                                        <?php if (!empty($item['nomor_jurnal'])): ?>
                                        <br><small class="text-muted">Jurnal: <?= esc($item['nomor_jurnal']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?= esc($item['nama_karyawan'] ?? '-') ?></span>
                                        <br><small class="text-muted"><?= esc($item['karyawan_nik'] ?? '') ?></small>
                                    </td>
                                    <td>
                                        <?php 
                                        $badgeClass = match($item['jenis']) {
                                            'Kasbon' => 'bg-primary',
                                            'Reimbursement' => 'bg-success',
                                            'Prive' => 'bg-secondary',
                                            'Dana Talangan' => 'bg-info',
                                            'Klaim Pribadi' => 'bg-warning',
                                            default => 'bg-dark'
                                        };
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= esc($item['jenis']) ?></span>
                                    </td>
                                    <td class="text-end fw-bold">Rp <?= number_format($item['jumlah'], 0, ',', '.') ?></td>
                                    <td class="text-end">Rp <?= number_format($item['jumlah_dibayar'] ?? 0, 0, ',', '.') ?></td>
                                    <td class="text-end <?= $sisaClass ?> fw-bold">Rp <?= number_format($sisa, 0, ',', '.') ?></td>
                                    <td>
                                        <small><?= esc($item['kode_akun_debit'] ?? '') ?></small><br>
                                        <small class="text-muted"><?= esc($item['nama_akun_debit'] ?? '-') ?></small>
                                    </td>
                                    <td>
                                        <small><?= esc($item['kode_akun_kredit'] ?? '') ?></small><br>
                                        <small class="text-muted"><?= esc($item['nama_akun_kredit'] ?? '-') ?></small>
                                    </td>
                                    <td>
                                        <small><?= esc($item['no_bukti'] ?: '-') ?></small>
                                    </td>
                                    <td>
                                        <?php if ($item['status'] == 'Posted'): ?>
                                        <span class="badge bg-success">Posted</span>
                                        <?php elseif ($item['status'] == 'Draft'): ?>
                                        <span class="badge bg-warning">Draft</span>
                                        <?php elseif ($item['status'] == 'Dibatalkan'): ?>
                                        <span class="badge bg-danger">Dibatalkan</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $hutangClass = match($item['status_hutang']) {
                                            'Lunas' => 'bg-success',
                                            'Sebagian' => 'bg-warning',
                                            'Belum Dibayar' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $hutangClass ?>"><?= esc($item['status_hutang']) ?></span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi/detail/' . $item['id']) ?>" 
                                               class="btn btn-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <?php if ($item['status'] == 'Draft'): ?>
                                                <a href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi/edit/' . $item['id']) ?>" 
                                                   class="btn btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-success btn-post" 
                                                        data-id="<?= $item['id'] ?>" title="Posting ke Jurnal">
                                                    <i class="fas fa-check-double"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-delete" 
                                                        data-id="<?= $item['id'] ?>" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php elseif ($item['status'] == 'Posted'): ?>
                                                <?php if ($item['status_hutang'] != 'Lunas'): ?>
                                                <a href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi/proses-pelunasan/' . $item['id']) ?>" 
                                                   class="btn btn-success" title="Proses Pelunasan">
                                                    <i class="fas fa-money-bill-wave"></i>
                                                </a>
                                                <?php endif; ?>
                                                <button type="button" class="btn btn-secondary btn-batal" 
                                                        data-id="<?= $item['id'] ?>" title="Batalkan">
                                                    <i class="fas fa-times-circle"></i>
                                                </button>
                                                <a href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi/print/' . $item['id']) ?>" 
                                                   class="btn btn-light" target="_blank" title="Print">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php 
                        $baseUrl = site_url('accounting/kas-bank/pengeluaran-pribadi');
                        $queryParams = $_GET;
                        unset($queryParams['page']);
                        $queryString = http_build_query($queryParams);
                        if ($queryString) {
                            $baseUrl .= '?' . $queryString . '&page=';
                        } else {
                            $baseUrl .= '?page=';
                        }
                        ?>
                        
                        <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $baseUrl . ($currentPage - 1) ?>">
                                <i class="fas fa-chevron-left"></i> Previous
                            </a>
                        </li>
                        
                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <li class="page-item <?= $p == $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="<?= $baseUrl . $p ?>"><?= $p ?></a>
                        </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $baseUrl . ($currentPage + 1) ?>">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Posting -->
<div class="modal fade" id="postModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Posting ke Jurnal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="postForm">
                <div class="modal-body">
                    <p>Apakah Anda yakin akan memposting pengeluaran pribadi ini ke jurnal?</p>
                    <p class="text-muted small">Setelah diposting, transaksi tidak dapat diedit lagi.</p>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Jurnal yang akan dibuat:</strong>
                        <p class="mb-0 small mt-2">
                            Debit: Akun yang dipilih (<?= isset($item) ? esc($item['nama_akun_debit'] ?? '') : 'Akun Debit' ?>)<br>
                            Kredit: Akun yang dipilih (<?= isset($item) ? esc($item['nama_akun_kredit'] ?? '') : 'Akun Kredit' ?>)
                        </p>
                    </div>
                    
                    <input type="hidden" name="pengeluaran_id" id="pengeluaran_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Ya, Posting</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select All functionality
    const selectAllHeader = document.getElementById('selectAllHeader');
    const selectAll = document.getElementById('selectAll');
    const rowSelects = document.querySelectorAll('.row-select');
    const bulkActions = document.getElementById('bulkActions');

    function toggleBulkActions() {
        const anyChecked = Array.from(rowSelects).some(cb => cb.checked);
        bulkActions.style.display = anyChecked ? 'inline-block' : 'none';
    }

    if (selectAllHeader) {
        selectAllHeader.addEventListener('change', function() {
            rowSelects.forEach(cb => {
                if (!cb.disabled) {
                    cb.checked = this.checked;
                }
            });
            if (selectAll) selectAll.checked = this.checked;
            toggleBulkActions();
        });
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            rowSelects.forEach(cb => {
                if (!cb.disabled) {
                    cb.checked = this.checked;
                }
            });
            if (selectAllHeader) selectAllHeader.checked = this.checked;
            toggleBulkActions();
        });
    }

    rowSelects.forEach(cb => {
        cb.addEventListener('change', toggleBulkActions);
    });

    // Handle posting button click
    document.querySelectorAll('.btn-post').forEach(btn => {
        btn.addEventListener('click', function() {
            var id = this.dataset.id;
            document.getElementById('pengeluaran_id').value = id;
            
            var postModal = new bootstrap.Modal(document.getElementById('postModal'));
            postModal.show();
        });
    });
    
    // Handle post form submit
    document.getElementById('postForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        var id = document.getElementById('pengeluaran_id').value;
        
        // Tampilkan loading
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        submitBtn.disabled = true;
        
        fetch('<?= site_url('accounting/kas-bank/pengeluaran-pribadi/post') ?>/' + id, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: '<?= csrf_token() ?>=<?= csrf_hash() ?>'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('postModal')).hide();
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    location.reload();
                }
            } else {
                alert('Error: ' + data.message);
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memposting');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
    
    // Handle delete button click
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            var id = this.dataset.id;
            
            if (confirm('Apakah Anda yakin akan menghapus pengeluaran pribadi ini?')) {
                // Tampilkan loading pada button
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                this.disabled = true;
                
                fetch('<?= site_url('accounting/kas-bank/pengeluaran-pribadi/delete') ?>/' + id, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert('Berhasil menghapus data');
                        location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Gagal menghapus data'));
                        this.innerHTML = originalText;
                        this.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus: ' + error.message);
                    this.innerHTML = originalText;
                    this.disabled = false;
                });
            }
        });
    });
    
    // Handle batalkan button click
    document.querySelectorAll('.btn-batal').forEach(btn => {
        btn.addEventListener('click', function() {
            var id = this.dataset.id;
            
            if (confirm('Apakah Anda yakin akan membatalkan pengeluaran pribadi ini? Jurnal terkait akan di-void.')) {
                fetch('<?= site_url('accounting/kas-bank/pengeluaran-pribadi/batalkan') ?>/' + id, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Berhasil membatalkan transaksi');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat membatalkan');
                });
            }
        });
    });
    
    // Date validation
    const tanggalMulai = document.getElementById('tanggal_mulai');
    const tanggalSelesai = document.getElementById('tanggal_selesai');
    
    if (tanggalMulai && tanggalSelesai) {
        tanggalMulai.addEventListener('change', function() {
            if (this.value && tanggalSelesai.value && this.value > tanggalSelesai.value) {
                alert('Tanggal mulai tidak boleh lebih besar dari tanggal selesai');
                this.value = '';
            }
        });
        
        tanggalSelesai.addEventListener('change', function() {
            if (this.value && tanggalMulai.value && this.value < tanggalMulai.value) {
                alert('Tanggal selesai tidak boleh lebih kecil dari tanggal mulai');
                this.value = '';
            }
        });
    }
    
    // Export functions
    window.exportWithFilters = function(format) {
        const filterForm = document.getElementById('filterForm');
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData).toString();
        
        const url = '<?= site_url("accounting/kas-bank/pengeluaran-pribadi/export") ?>?' + params + '&type=' + format;
        
        if (format === 'pdf') {
            window.open(url, '_blank');
        } else {
            window.location.href = url;
        }
    };
    
    // Bulk Post
    window.bulkPost = function() {
        const selectedIds = Array.from(document.querySelectorAll('.row-select:checked')).map(cb => cb.value);
        
        if (selectedIds.length === 0) {
            alert('Pilih minimal satu data');
            return;
        }
        
        if (confirm(`Posting ${selectedIds.length} pengeluaran pribadi ke jurnal?`)) {
            fetch('<?= site_url('accounting/kas-bank/pengeluaran-pribadi/bulk-post') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'ids=' + JSON.stringify(selectedIds) + '&<?= csrf_token() ?>=<?= csrf_hash() ?>'
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            });
        }
    };
    
    // Bulk Delete
    window.bulkDelete = function() {
        const selectedIds = Array.from(document.querySelectorAll('.row-select:checked')).map(cb => cb.value);
        
        if (selectedIds.length === 0) {
            alert('Pilih minimal satu data');
            return;
        }
        
        if (confirm(`Hapus ${selectedIds.length} pengeluaran pribadi?`)) {
            fetch('<?= site_url('accounting/kas-bank/pengeluaran-pribadi/bulk-delete') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'ids=' + JSON.stringify(selectedIds) + '&<?= csrf_token() ?>=<?= csrf_hash() ?>'
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            });
        }
    };
    
    // Fungsi format rupiah
    function formatRupiah(angka) {
        return angka.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }
});
</script>

<style>
.modern-card {
    background: #fff;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,.1);
    margin-bottom: 20px;
}
.border-start-primary {
    border-left: 4px solid #4e73df !important;
}
.border-start-success {
    border-left: 4px solid #1cc88a !important;
}
.border-start-danger {
    border-left: 4px solid #e74a3b !important;
}
.border-start-info {
    border-left: 4px solid #36b9cc !important;
}
.border-start-warning {
    border-left: 4px solid #f6c23e !important;
}
.border-left-info {
    border-left: 4px solid #36b9cc !important;
}
.border-left-warning {
    border-left: 4px solid #f6c23e !important;
}
.border-left-success {
    border-left: 4px solid #1cc88a !important;
}
.border-left-secondary {
    border-left: 4px solid #858796 !important;
}
.nav-pills .nav-link.active {
    background-color: #4e73df;
}
.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
.table th {
    white-space: nowrap;
    background-color: #f8f9fc;
}
.table-warning {
    background-color: #fff3cd !important;
}
</style>

<?= $this->include('accounting/templates/footer') ?>