<?= $this->include('accounting/templates/header') ?>
<?= $this->include('accounting/templates/sidebar') ?>
<?= $this->include('accounting/templates/navbar') ?>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="page-title mb-1">Kas Kecil</h2>
                    <p class="page-subtitle text-muted mb-0">Pengelolaan dana operasional rutin perusahaan</p>
                </div>
                <div class="btn-group">
                    <a href="<?= site_url('accounting/kas-bank/kas-kecil/create?tipe=Pengeluaran') ?>" class="btn btn-warning">
                        <i class="fas fa-minus-circle me-1"></i> Pengeluaran
                    </a>
                    <a href="<?= site_url('accounting/kas-bank/kas-kecil/create?tipe=Pemasukan') ?>" class="btn btn-success" id="btnPemasukan">
                        <i class="fas fa-plus-circle me-1"></i> Pemasukan
                    </a>
                    
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

    <!-- Saldo Kas Kecil Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white shadow-lg">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <h4 class="text-white mb-2">Saldo Kas Kecil Saat Ini</h4>
                            <h1 class="display-4 text-white fw-bold mb-3">Rp <?= $saldo_kas_kecil ?></h1>
                            <div class="d-flex gap-3">
                                <div class="bg-white bg-opacity-25 rounded p-2">
                                    <small>Total Pemasukan</small>
                                    <h5 class="text-white mb-0">Rp <?= number_format($stats['total_pemasukan'] ?? 0, 0, ',', '.') ?></h5>
                                </div>
                                <div class="bg-white bg-opacity-25 rounded p-2">
                                    <small>Total Pengeluaran</small>
                                    <h5 class="text-white mb-0">Rp <?= number_format($stats['total_pengeluaran'] ?? 0, 0, ',', '.') ?></h5>
                                </div>
                                <div class="bg-white bg-opacity-25 rounded p-2">
                                    <small>Total Transaksi</small>
                                    <h5 class="text-white mb-0"><?= number_format($stats['total_transaksi'] ?? 0, 0) ?></h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 text-end">
                            <i class="fas fa-coins fa-5x text-white opacity-50"></i>
                        </div>
                    </div>
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
                                Total Pemasukan
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                Rp <?= number_format($stats['total_pemasukan'] ?? 0, 0, ',', '.') ?>
                            </div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-success me-2">
                                    <i class="fas fa-arrow-up"></i>
                                </span>
                                <span><?= $stats['jumlah_pemasukan'] ?? 0 ?> transaksi</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start-danger border-start-3 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-danger text-uppercase mb-1">
                                Total Pengeluaran
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                Rp <?= number_format($stats['total_pengeluaran'] ?? 0, 0, ',', '.') ?>
                            </div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-danger me-2">
                                    <i class="fas fa-arrow-down"></i>
                                </span>
                                <span><?= $stats['jumlah_pengeluaran'] ?? 0 ?> transaksi</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-danger"></i>
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

    <!-- Quick Info Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-light border-0">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-info-circle text-info me-2"></i> Metode Imprest</h6>
                    <p class="card-text small text-muted mb-0">
                        Kas kecil menggunakan metode dana tetap (imprest). 
                        Jumlah kas kecil selalu tetap dan akan diisi ulang jika hampir habis.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light border-0">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-hand-holding-usd text-success me-2"></i> Pengisian Kembali</h6>
                    <p class="card-text small text-muted mb-0">
                        Lakukan pengisian kembali berdasarkan total pengeluaran periode tertentu.
                        <br><a href="<?= site_url('accounting/kas-bank/kas-kecil/pengisian-kembali') ?>" class="btn btn-sm btn-success mt-2">
                            <i class="fas fa-sync-alt me-1"></i> Pengisian Kembali
                        </a>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light border-0">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-book-open text-warning me-2"></i> Buku Kas Kecil</h6>
                    <p class="card-text small text-muted mb-0">
                        Lihat mutasi kas kecil dengan saldo berjalan.
                        <br><a href="<?= site_url('accounting/kas-bank/kas-kecil/buku-kas-kecil') ?>" class="btn btn-sm btn-warning mt-2">
                            <i class="fas fa-book me-1"></i> Buku Kas Kecil
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="modern-card">
                <h5 class="card-title mb-3">
                    <i class="fas fa-filter me-2"></i> Filter Data
                </h5>
                <form method="get" action="<?= site_url('accounting/kas-bank/kas-kecil') ?>" id="filterForm">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="search" class="form-label">Pencarian</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   placeholder="No. transaksi, keterangan..." value="<?= esc($filters['search'] ?? '') ?>">
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
                            <label for="tipe" class="form-label">Tipe</label>
                            <select class="form-select" id="tipe" name="tipe">
                                <option value="">Semua</option>
                                <?php foreach ($tipeOptions as $tipe): ?>
                                <option value="<?= $tipe ?>" <?= ($filters['tipe'] ?? '') == $tipe ? 'selected' : '' ?>><?= $tipe ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Semua</option>
                                <?php foreach ($statusOptions as $status): ?>
                                <option value="<?= $status ?>" <?= ($filters['status'] ?? '') == $status ? 'selected' : '' ?>><?= $status ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
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
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <div class="d-flex justify-content-end w-100">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i> Terapkan Filter
                                </button>
                                <a href="<?= site_url('accounting/kas-bank/kas-kecil') ?>" class="btn btn-secondary">
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
                    <a class="nav-link <?= (($filters['status'] ?? '') == '') ? 'active' : '' ?>" 
                       href="<?= site_url('accounting/kas-bank/kas-kecil') ?>">
                        <i class="fas fa-list me-1"></i> Semua
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (($filters['status'] ?? '') == 'Draft') ? 'active' : '' ?>" 
                       href="<?= site_url('accounting/kas-bank/kas-kecil/draft') ?>">
                        <i class="fas fa-pen me-1"></i> Draft
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (($filters['status'] ?? '') == 'Posted') ? 'active' : '' ?>" 
                       href="<?= site_url('accounting/kas-bank/kas-kecil/posted') ?>">
                        <i class="fas fa-check-circle me-1"></i> Posted
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (($filters['status'] ?? '') == 'Dibatalkan') ? 'active' : '' ?>" 
                       href="<?= site_url('accounting/kas-bank/kas-kecil/dibatalkan') ?>">
                        <i class="fas fa-times-circle me-1"></i> Dibatalkan
                    </a>
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
                        <i class="fas fa-coins me-2"></i> Daftar Transaksi Kas Kecil
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
                            <small class="text-muted">Menampilkan <?= count($transaksi) ?> dari <?= $total ?? 0 ?> data</small>
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
                                <th width="5%">No</th>
                                <th width="8%">Tanggal</th>
                                <th width="12%">Kode Transaksi</th>
                                <th width="5%">Tipe</th>
                                <th width="10%" class="text-end">Jumlah</th>
                                <th width="10%" class="text-end">Saldo Setelah</th>
                                <th width="12%">Akun Lawan</th>
                                <th width="10%">Karyawan</th>
                                <th width="8%">No Bukti</th>
                                <th width="12%">Keterangan</th>
                                <th width="6%">Status</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($transaksi)): ?>
                            <tr>
                                <td colspan="13" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-coins fa-2x mb-3"></i>
                                        <h5>Tidak ada data kas kecil</h5>
                                        <p>Silakan buat transaksi baru atau ubah filter pencarian.</p>
                                        <div class="btn-group">
                                            <a href="<?= site_url('accounting/kas-bank/kas-kecil/create?tipe=Pengeluaran') ?>" class="btn btn-warning">
                                                <i class="fas fa-minus-circle me-1"></i> Pengeluaran
                                            </a>
                                            <a href="<?= site_url('accounting/kas-bank/kas-kecil/create?tipe=Pemasukan') ?>" class="btn btn-success">
                                                <i class="fas fa-plus-circle me-1"></i> Pemasukan
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php 
                                $start = ($currentPage - 1) * $perPage + 1;
                                foreach ($transaksi as $i => $item): 
                                ?>
                                <tr id="row-<?= $item['id'] ?>">
                                    <td class="text-center">
                                        <input class="form-check-input row-select" type="checkbox" value="<?= $item['id'] ?>" 
                                               <?= $item['status'] != 'Draft' ? 'disabled' : '' ?>>
                                    </td>
                                    <td><?= $start + $i ?></td>
                                    <td><?= date('d/m/Y', strtotime($item['tanggal'])) ?></td>
                                    <td>
                                        <strong><?= esc($item['kode_transaksi']) ?></strong>
                                        <?php if (!empty($item['nomor_jurnal'])): ?>
                                        <br><small class="text-muted">Jurnal: <?= esc($item['nomor_jurnal']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($item['tipe'] == 'Pemasukan'): ?>
                                        <span class="badge bg-success">MASUK</span>
                                        <?php else: ?>
                                        <span class="badge bg-danger">KELUAR</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end <?= $item['tipe'] == 'Pemasukan' ? 'text-success' : 'text-danger' ?> fw-bold">
                                        Rp <?= number_format($item['jumlah'], 0, ',', '.') ?>
                                    </td>
                                    <td class="text-end fw-bold <?= ($item['saldo_setelah'] ?? 0) >= 0 ? 'text-info' : 'text-danger' ?>">
                                        Rp <?= number_format($item['saldo_setelah'] ?? 0, 0, ',', '.') ?>
                                    </td>
                                    <td>
                                        <small><?= esc($item['kode_akun_lawan'] ?? '') ?></small><br>
                                        <small class="text-muted"><?= esc($item['nama_akun_lawan'] ?? '-') ?></small>
                                    </td>
                                    <td>
                                        <?php if (!empty($item['nama_karyawan'])): ?>
                                            <span class="badge bg-secondary"><?= esc($item['nama_karyawan']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small><?= esc($item['no_bukti'] ?: '-') ?></small>
                                    </td>
                                    <td>
                                        <small><?= esc($item['keterangan']) ?></small>
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
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= site_url('accounting/kas-bank/kas-kecil/detail/' . $item['id']) ?>" 
                                               class="btn btn-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <?php if ($item['status'] == 'Draft'): ?>
                                                <a href="<?= site_url('accounting/kas-bank/kas-kecil/edit/' . $item['id']) ?>" 
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
                                                <button type="button" class="btn btn-secondary btn-batal" 
                                                        data-id="<?= $item['id'] ?>" title="Batalkan">
                                                    <i class="fas fa-times-circle"></i>
                                                </button>
                                                <a href="<?= site_url('accounting/kas-bank/kas-kecil/print/' . $item['id']) ?>" 
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
                        $baseUrl = site_url('accounting/kas-bank/kas-kecil');
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
                    <p>Apakah Anda yakin akan memposting transaksi kas kecil ini ke jurnal?</p>
                    <p class="text-muted small">Setelah diposting, transaksi tidak dapat diedit lagi.</p>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Jurnal yang akan dibuat:</strong>
                        <p class="mb-0 small mt-2">
                            <?php 
                            // Tampilkan preview jurnal berdasarkan tipe
                            if (isset($item) && $item['tipe'] == 'Pemasukan'): ?>
                                Debit: Kas Kecil<br>
                                Kredit: Akun Lawan (Kas/Bank)
                            <?php else: ?>
                                Debit: Akun Lawan (Beban/Aset)<br>
                                Kredit: Kas Kecil
                            <?php endif; ?>
                        </p>
                    </div>
                    
                    <input type="hidden" name="transaksi_id" id="transaksi_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Ya, Posting</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Peringatan Saldo -->
<div class="modal fade" id="saldoWarningModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-warning">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-dark">
                    <i class="fas fa-exclamation-triangle me-2"></i> Peringatan Saldo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-exclamation-circle fa-4x text-warning"></i>
                </div>
                <h6 class="text-center mb-3">Saldo kas kecil tidak mencukupi untuk melakukan pengeluaran!</h6>
                <div id="saldoWarningDetail" class="small"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="<?= site_url('accounting/kas-bank/kas-kecil/pengisian-kembali') ?>" class="btn btn-success">
                    <i class="fas fa-sync-alt me-1"></i> Pengisian Kembali
                </a>
            </div>
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
            document.getElementById('transaksi_id').value = id;
            
            var postModal = new bootstrap.Modal(document.getElementById('postModal'));
            postModal.show();
        });
    });
    
    // Handle post form submit
    document.getElementById('postForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        var id = document.getElementById('transaksi_id').value;
        
        // Tampilkan loading
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        submitBtn.disabled = true;
        
        fetch('<?= site_url('accounting/kas-bank/kas-kecil/post') ?>/' + id, {
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
            
            if (confirm('Apakah Anda yakin akan menghapus transaksi kas kecil ini?')) {
                // Tampilkan loading pada button
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                this.disabled = true;
                
                fetch('<?= site_url('accounting/kas-bank/kas-kecil/delete') ?>/' + id, {
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
            
            if (confirm('Apakah Anda yakin akan membatalkan transaksi kas kecil ini? Jurnal terkait akan di-void.')) {
                fetch('<?= site_url('accounting/kas-bank/kas-kecil/batalkan') ?>/' + id, {
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
        
        const url = '<?= site_url("accounting/kas-bank/kas-kecil/export") ?>?' + params + '&type=' + format;
        
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
        
        if (confirm(`Posting ${selectedIds.length} transaksi kas kecil ke jurnal?`)) {
            fetch('<?= site_url('accounting/kas-bank/kas-kecil/bulk-post') ?>', {
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
        
        if (confirm(`Hapus ${selectedIds.length} transaksi kas kecil?`)) {
            fetch('<?= site_url('accounting/kas-bank/kas-kecil/bulk-delete') ?>', {
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
    
    // Cek saldo sebelum tambah pengeluaran
    const btnPemasukan = document.getElementById('btnPemasukan');
    if (btnPemasukan) {
        btnPemasukan.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Ambil saldo kas kecil
            const saldoKasKecil = <?= $stats['saldo_terkini'] ?? 0 ?>;
            
            // Cek apakah perlu peringatan? (opsional)
            // Bisa ditambahkan logika tertentu
            
            window.location.href = this.href;
        });
    }
    
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
.bg-gradient-primary {
    background: linear-gradient(45deg, #4e73df, #224abe);
}
</style>

<?= $this->include('accounting/templates/footer') ?>