<?= $this->include('accounting/templates/header') ?>
<?= $this->include('accounting/templates/sidebar') ?>
<?= $this->include('accounting/templates/navbar') ?>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="page-title mb-1">Rekonsiliasi Bank</h2>
                    <p class="page-subtitle text-muted mb-0">Pencocokan catatan transaksi bank dengan rekening koran</p>
                </div>
                <div class="btn-group">
                    <a href="<?= site_url('accounting/kas-bank/rekonsiliasi/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i> Rekonsiliasi Baru
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

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start-primary border-start-3 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                Total Rekonsiliasi
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                <?= number_format($stats['total_rekonsiliasi'] ?? 0, 0) ?>
                            </div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-primary me-2">
                                    <i class="fas fa-balance-scale"></i>
                                </span>
                                <span>Periode filter</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-balance-scale fa-2x text-primary"></i>
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
                                Selesai
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                <?= $stats['selesai'] ?? 0 ?>
                            </div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-success me-2">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                                <span>Rekonsiliasi selesai</span>
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
            <div class="card border-start-warning border-start-3 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                Draft
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                <?= $stats['draft'] ?? 0 ?>
                            </div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-warning me-2">
                                    <i class="fas fa-pen"></i>
                                </span>
                                <span>Perlu diselesaikan</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-pen fa-2x text-warning"></i>
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
                                Jumlah Bank
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                <?= $stats['jumlah_bank_direkonsiliasi'] ?? 0 ?>
                            </div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-info me-2">
                                    <i class="fas fa-building-columns"></i>
                                </span>
                                <span>Bank direkonsiliasi</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building-columns fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Info Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-light border-0">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-info-circle text-info me-2"></i> Apa itu Rekonsiliasi Bank?</h6>
                    <p class="card-text small text-muted mb-0">
                        Rekonsiliasi bank adalah proses mencocokkan catatan transaksi bank di sistem internal 
                        dengan catatan resmi dari bank (rekening koran). Tujuannya untuk memastikan tidak ada 
                        perbedaan (selisih) dan mendeteksi transaksi yang terlewat atau kesalahan pencatatan.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-light border-0">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-balance-scale text-warning me-2"></i> Komponen Rekonsiliasi</h6>
                    <p class="card-text small text-muted mb-0">
                        • <strong>Setoran dalam perjalanan</strong>: Setoran yang sudah dicatat perusahaan tapi belum masuk bank<br>
                        • <strong>Cek dalam edar</strong>: Cek yang sudah dikeluarkan tapi belum dicairkan<br>
                        • <strong>Penyesuaian bank</strong>: Biaya admin, bunga, dll dari bank<br>
                        • <strong>Penyesuaian buku</strong>: Koreksi kesalahan pencatatan
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan per Bank Cards -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="modern-card">
                <h5 class="card-title mb-3">
                    <i class="fas fa-building-columns me-2"></i> Ringkasan Rekonsiliasi per Bank
                </h5>
                <div class="row">
                    <?php if (!empty($ringkasanBank)): ?>
                        <?php foreach ($ringkasanBank as $bank): 
                            $progressClass = $bank['selesai'] == $bank['jumlah_rekonsiliasi'] ? 'bg-success' : ($bank['selesai'] > 0 ? 'bg-warning' : 'bg-secondary');
                            $progressWidth = $bank['jumlah_rekonsiliasi'] > 0 ? ($bank['selesai'] / $bank['jumlah_rekonsiliasi'] * 100) : 0;
                        ?>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="card-title text-muted mb-1"><?= esc($bank['kode_akun'] ?? '') ?></h6>
                                            <h6 class="card-text mb-2"><?= esc($bank['nama_akun'] ?? '') ?></h6>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-info"><?= $bank['jumlah_rekonsiliasi'] ?? 0 ?>x</span>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <div class="d-flex justify-content-between small mb-1">
                                            <span>Selesai: <?= $bank['selesai'] ?? 0 ?></span>
                                            <span>Draft: <?= $bank['draft'] ?? 0 ?></span>
                                        </div>
                                        <div class="progress" style="height: 5px;">
                                            <div class="progress-bar <?= $progressClass ?>" role="progressbar" 
                                                 style="width: <?= $progressWidth ?>%" 
                                                 aria-valuenow="<?= $progressWidth ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="mt-2 small text-muted">
                                            Periode terakhir: <?= !empty($bank['periode_terakhir']) ? date('M Y', strtotime($bank['periode_terakhir'])) : '-' ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i> Belum ada data rekonsiliasi.
                            </div>
                        </div>
                    <?php endif; ?>
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
                <form method="get" action="<?= site_url('accounting/kas-bank/rekonsiliasi') ?>" id="filterForm">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="search" class="form-label">Pencarian</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   placeholder="Bank, keterangan..." value="<?= esc($filters['search'] ?? '') ?>">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="tahun" class="form-label">Tahun</label>
                            <select class="form-select" id="tahun" name="tahun">
                                <option value="">Semua Tahun</option>
                                <?php foreach ($tahunOptions as $tahun): ?>
                                <option value="<?= $tahun ?>" <?= ($filters['tahun'] ?? '') == $tahun ? 'selected' : '' ?>><?= $tahun ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="bulan" class="form-label">Bulan</label>
                            <select class="form-select" id="bulan" name="bulan">
                                <option value="">Semua Bulan</option>
                                <?php foreach ($bulanOptions as $key => $bulan): ?>
                                <option value="<?= $key ?>" <?= ($filters['bulan'] ?? '') == $key ? 'selected' : '' ?>><?= $bulan ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="coa_bank_id" class="form-label">Bank</label>
                            <select class="form-select" id="coa_bank_id" name="coa_bank_id">
                                <option value="">Semua Bank</option>
                                <?php foreach ($bankOptions as $bank): ?>
                                <option value="<?= $bank['id'] ?>" <?= ($filters['coa_bank_id'] ?? '') == $bank['id'] ? 'selected' : '' ?>>
                                    <?= esc($bank['kode_akun']) ?> - <?= esc($bank['nama_akun']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
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
                        <div class="col-md-12 mb-3 d-flex align-items-end">
                            <div class="d-flex justify-content-end w-100">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i> Terapkan Filter
                                </button>
                                <a href="<?= site_url('accounting/kas-bank/rekonsiliasi') ?>" class="btn btn-secondary">
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
                       href="<?= site_url('accounting/kas-bank/rekonsiliasi') ?>">
                        <i class="fas fa-list me-1"></i> Semua
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (($filters['status'] ?? '') == 'Draft') ? 'active' : '' ?>" 
                       href="<?= site_url('accounting/kas-bank/rekonsiliasi/draft') ?>">
                        <i class="fas fa-pen me-1"></i> Draft
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (($filters['status'] ?? '') == 'Selesai') ? 'active' : '' ?>" 
                       href="<?= site_url('accounting/kas-bank/rekonsiliasi/selesai') ?>">
                        <i class="fas fa-check-circle me-1"></i> Selesai
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (($filters['status'] ?? '') == 'Dibatalkan') ? 'active' : '' ?>" 
                       href="<?= site_url('accounting/kas-bank/rekonsiliasi/dibatalkan') ?>">
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
                        <i class="fas fa-balance-scale me-2"></i> Daftar Rekonsiliasi Bank
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
                                    <a class="dropdown-item" href="#" onclick="bulkSelesaikan()">
                                        <i class="fas fa-check-circle text-success me-2"></i> Selesaikan Terpilih
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
                            <small class="text-muted">Menampilkan <?= count($rekonsiliasi) ?> dari <?= $total ?? 0 ?> data</small>
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
                                <th width="8%">Periode</th>
                                <th width="15%">Bank</th>
                                <th width="8%" class="text-end">Saldo Bank</th>
                                <th width="8%" class="text-end">Saldo Buku</th>
                                <th width="5%" class="text-end">Selisih</th>
                                <th width="5%">Setoran</th>
                                <th width="5%">Cek</th>
                                <th width="5%">Penyesuaian</th>
                                <th width="8%">Tgl Rekonsiliasi</th>
                                <th width="6%">Status</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($rekonsiliasi)): ?>
                            <tr>
                                <td colspan="13" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-balance-scale fa-2x mb-3"></i>
                                        <h5>Tidak ada data rekonsiliasi bank</h5>
                                        <p>Silakan buat rekonsiliasi baru atau ubah filter pencarian.</p>
                                        <a href="<?= site_url('accounting/kas-bank/rekonsiliasi/create') ?>" class="btn btn-primary">
                                            <i class="fas fa-plus-circle me-1"></i> Rekonsiliasi Baru
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php 
                                $start = ($currentPage - 1) * $perPage + 1;
                                foreach ($rekonsiliasi as $i => $item): 
                                    $selisih = ($item['saldo_akhir_bank'] ?? 0) - ($item['saldo_akhir_buku'] ?? 0);
                                    $selisihClass = $selisih == 0 ? 'text-success' : (abs($selisih) < 1000 ? 'text-warning' : 'text-danger');
                                ?>
                                <tr id="row-<?= $item['id'] ?>">
                                    <td class="text-center">
                                        <input class="form-check-input row-select" type="checkbox" value="<?= $item['id'] ?>" 
                                               <?= $item['status'] != 'Draft' ? 'disabled' : '' ?>>
                                    </td>
                                    <td><?= $start + $i ?></td>
                                    <td>
                                        <strong><?= date('M Y', strtotime($item['periode'])) ?></strong>
                                        <br><small class="text-muted"><?= date('d/m/Y', strtotime($item['periode'])) ?></small>
                                    </td>
                                    <td>
                                        <strong><?= esc($item['nama_akun_bank'] ?? '-') ?></strong>
                                        <br><small class="text-muted"><?= esc($item['kode_akun_bank'] ?? '') ?></small>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold">Rp <?= number_format($item['saldo_akhir_bank'] ?? 0, 0) ?></span>
                                        <br><small class="text-muted">Awal: Rp <?= number_format($item['saldo_awal_bank'] ?? 0, 0) ?></small>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold">Rp <?= number_format($item['saldo_akhir_buku'] ?? 0, 0) ?></span>
                                        <br><small class="text-muted">Awal: Rp <?= number_format($item['saldo_awal_buku'] ?? 0, 0) ?></small>
                                    </td>
                                    <td class="text-end <?= $selisihClass ?> fw-bold">
                                        Rp <?= number_format($selisih, 0) ?>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-info" title="Setoran dalam perjalanan">
                                            Rp <?= number_format($item['total_setoran_dalam_perjalanan'] ?? 0, 0) ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-warning" title="Cek dalam edar">
                                            Rp <?= number_format($item['total_cek_dalam_edar'] ?? 0, 0) ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="small">
                                            <span class="text-info" title="Penyesuaian Bank">B: Rp <?= number_format($item['total_penyesuaian_bank'] ?? 0, 0) ?></span><br>
                                            <span class="text-primary" title="Penyesuaian Buku">U: Rp <?= number_format($item['total_penyesuaian_buku'] ?? 0, 0) ?></span>
                                        </div>
                                    </td>
                                    <td class="text-center"><?= date('d/m/Y', strtotime($item['tanggal_rekonsiliasi'])) ?></td>
                                    <td>
                                        <?php if ($item['status'] == 'Selesai'): ?>
                                        <span class="badge bg-success">Selesai</span>
                                        <?php elseif ($item['status'] == 'Draft'): ?>
                                        <span class="badge bg-warning">Draft</span>
                                        <?php elseif ($item['status'] == 'Dibatalkan'): ?>
                                        <span class="badge bg-danger">Dibatalkan</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= site_url('accounting/kas-bank/rekonsiliasi/detail/' . $item['id']) ?>" 
                                               class="btn btn-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <?php if ($item['status'] == 'Draft'): ?>
                                                <a href="<?= site_url('accounting/kas-bank/rekonsiliasi/edit/' . $item['id']) ?>" 
                                                   class="btn btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-success btn-selesaikan" 
                                                        data-id="<?= $item['id'] ?>" title="Selesaikan Rekonsiliasi">
                                                    <i class="fas fa-check-double"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-delete" 
                                                        data-id="<?= $item['id'] ?>" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php elseif ($item['status'] == 'Selesai'): ?>
                                                <button type="button" class="btn btn-secondary btn-batal" 
                                                        data-id="<?= $item['id'] ?>" title="Batalkan">
                                                    <i class="fas fa-times-circle"></i>
                                                </button>
                                                <a href="<?= site_url('accounting/kas-bank/rekonsiliasi/print/' . $item['id']) ?>" 
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
                        $baseUrl = site_url('accounting/kas-bank/rekonsiliasi');
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

<!-- Modal Selesaikan Rekonsiliasi -->
<div class="modal fade" id="selesaikanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Selesaikan Rekonsiliasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="selesaikanForm">
                <div class="modal-body">
                    <p>Apakah Anda yakin akan menyelesaikan rekonsiliasi ini?</p>
                    <p class="text-muted small">Setelah diselesaikan, data rekonsiliasi tidak dapat diubah lagi.</p>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Pastikan:</strong>
                        <ul class="mb-0 small mt-2">
                            <li>Semua transaksi sudah dicocokkan (matched)</li>
                            <li>Saldo akhir bank dan buku sudah sesuai (selisih = 0)</li>
                            <li>Tidak ada transaksi yang terlewat</li>
                        </ul>
                    </div>
                    
                    <input type="hidden" name="rekonsiliasi_id" id="rekonsiliasi_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Ya, Selesaikan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Batalkan -->
<div class="modal fade" id="batalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Batalkan Rekonsiliasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin akan membatalkan rekonsiliasi ini?</p>
                <p class="text-danger small">Data rekonsiliasi akan dikembalikan ke status Draft dan dapat diedit kembali.</p>
                <input type="hidden" name="rekonsiliasi_batal_id" id="rekonsiliasi_batal_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                <button type="button" class="btn btn-danger" id="confirmBatal">Ya, Batalkan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Rekonsiliasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin akan menghapus rekonsiliasi ini?</p>
                <p class="text-danger small">Data yang dihapus tidak dapat dikembalikan.</p>
                <input type="hidden" name="rekonsiliasi_delete_id" id="rekonsiliasi_delete_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Ya, Hapus</button>
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

    // Handle selesaikan button click
    document.querySelectorAll('.btn-selesaikan').forEach(btn => {
        btn.addEventListener('click', function() {
            var id = this.dataset.id;
            document.getElementById('rekonsiliasi_id').value = id;
            
            var modal = new bootstrap.Modal(document.getElementById('selesaikanModal'));
            modal.show();
        });
    });
    
    // Handle selesaikan form submit
    document.getElementById('selesaikanForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        var id = document.getElementById('rekonsiliasi_id').value;
        
        // Tampilkan loading
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        submitBtn.disabled = true;
        
        fetch('<?= site_url('accounting/kas-bank/rekonsiliasi/selesaikan') ?>/' + id, {
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
                bootstrap.Modal.getInstance(document.getElementById('selesaikanModal')).hide();
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
            alert('Terjadi kesalahan saat menyelesaikan rekonsiliasi');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
    
    // Handle batalkan button click
    document.querySelectorAll('.btn-batal').forEach(btn => {
        btn.addEventListener('click', function() {
            var id = this.dataset.id;
            document.getElementById('rekonsiliasi_batal_id').value = id;
            
            var modal = new bootstrap.Modal(document.getElementById('batalModal'));
            modal.show();
        });
    });
    
    // Confirm batalkan
    document.getElementById('confirmBatal')?.addEventListener('click', function() {
        var id = document.getElementById('rekonsiliasi_batal_id').value;
        
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        this.disabled = true;
        
        fetch('<?= site_url('accounting/kas-bank/rekonsiliasi/batalkan') ?>/' + id, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('batalModal')).hide();
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    location.reload();
                }
            } else {
                alert('Error: ' + data.message);
                this.innerHTML = 'Ya, Batalkan';
                this.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat membatalkan');
            this.innerHTML = 'Ya, Batalkan';
            this.disabled = false;
        });
    });
    
    // Handle delete button click
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            var id = this.dataset.id;
            document.getElementById('rekonsiliasi_delete_id').value = id;
            
            var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        });
    });
    
    // Confirm delete
    document.getElementById('confirmDelete')?.addEventListener('click', function() {
        var id = document.getElementById('rekonsiliasi_delete_id').value;
        
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        this.disabled = true;
        
        fetch('<?= site_url('accounting/kas-bank/rekonsiliasi/delete') ?>/' + id, {
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
                bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    location.reload();
                }
            } else {
                alert('Error: ' + (data.message || 'Gagal menghapus data'));
                this.innerHTML = 'Ya, Hapus';
                this.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus: ' + error.message);
            this.innerHTML = 'Ya, Hapus';
            this.disabled = false;
        });
    });
    
    // Date validation (periode)
    const tahunSelect = document.getElementById('tahun');
    const bulanSelect = document.getElementById('bulan');
    
    // Export functions
    window.exportWithFilters = function(format) {
        const filterForm = document.getElementById('filterForm');
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData).toString();
        
        const url = '<?= site_url("accounting/kas-bank/rekonsiliasi/export") ?>?' + params + '&type=' + format;
        
        if (format === 'pdf') {
            window.open(url, '_blank');
        } else {
            window.location.href = url;
        }
    };
    
    // Bulk Selesaikan
    window.bulkSelesaikan = function() {
        const selectedIds = Array.from(document.querySelectorAll('.row-select:checked')).map(cb => cb.value);
        
        if (selectedIds.length === 0) {
            alert('Pilih minimal satu data');
            return;
        }
        
        if (confirm(`Selesaikan ${selectedIds.length} rekonsiliasi?`)) {
            fetch('<?= site_url('accounting/kas-bank/rekonsiliasi/bulk-selesaikan') ?>', {
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
        
        if (confirm(`Hapus ${selectedIds.length} rekonsiliasi?`)) {
            fetch('<?= site_url('accounting/kas-bank/rekonsiliasi/bulk-delete') ?>', {
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
.progress {
    border-radius: 10px;
}
</style>

<?= $this->include('accounting/templates/footer') ?>