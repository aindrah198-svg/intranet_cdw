<?= $this->include('accounting/templates/header') ?>
<?= $this->include('accounting/templates/sidebar') ?>
<?= $this->include('accounting/templates/navbar') ?>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="page-title mb-1">Mutasi Bank</h2>
                    <p class="page-subtitle text-muted mb-0">Daftar transaksi mutasi bank</p>
                </div>
                <div class="btn-group">
                    <a href="<?= site_url('accounting/kas-bank/mutasi-bank/create?tipe=Masuk') ?>" class="btn btn-success">
                        <i class="fas fa-plus-circle me-1"></i> Transaksi Masuk
                    </a>
                    <a href="<?= site_url('accounting/kas-bank/mutasi-bank/create?tipe=Keluar') ?>" class="btn btn-danger" id="btnTransaksiKeluar">
                        <i class="fas fa-minus-circle me-1"></i> Transaksi Keluar
                    </a>
                    <button type="button" class="btn btn-danger" id="btnExportPdf">
                        <i class="fas fa-file-pdf me-1"></i> Export PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Sistem -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <div class="d-flex">
                    <div class="me-3">
                        <i class="fas fa-sync-alt fa-2x"></i>
                    </div>
                    <div>
                        <strong><i class="fas fa-info-circle me-1"></i> Informasi Sistem Mutasi Bank:</strong>
                        <ul class="mb-0 mt-2">
                            <li>User menginput <strong class="text-success">"Masuk"</strong> untuk uang masuk ke perusahaan → Sistem menyimpan sebagai <strong class="text-success">"KREDIT"</strong> di database</li>
                            <li>User menginput <strong class="text-danger">"Keluar"</strong> untuk uang keluar dari perusahaan → Sistem menyimpan sebagai <strong class="text-danger">"DEBIT"</strong> di database</li>
                            <li class="mt-1"><em>Penjelasan: Secara akuntansi, uang masuk (Kredit) menambah saldo bank, uang keluar (Debit) mengurangi saldo bank.</em></li>
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
                                Total Masuk
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                Rp <?= number_format($stats['total_masuk'] ?? 0, 2) ?>
                            </div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-success me-2">
                                    <i class="fas fa-arrow-down"></i>
                                </span>
                                <span><?= $stats['jumlah_masuk'] ?? 0 ?> transaksi</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-success"></i>
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
                                Total Keluar
                            </div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                Rp <?= number_format($stats['total_keluar'] ?? 0, 2) ?>
                            </div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-danger me-2">
                                    <i class="fas fa-arrow-up"></i>
                                </span>
                                <span><?= $stats['jumlah_keluar'] ?? 0 ?> transaksi</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-danger"></i>
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

    <!-- Saldo per Bank Cards -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="modern-card">
                <h5 class="card-title mb-3">
                    <i class="fas fa-wallet me-2"></i> Saldo per Bank
                </h5>
                <div class="row">
                    <?php if (!empty($ringkasanBank)): ?>
                        <?php foreach ($ringkasanBank as $bank): 
                            $saldo = ($bank['total_masuk'] ?? 0) - ($bank['total_keluar'] ?? 0);
                            $saldoClass = $saldo >= 0 ? 'text-success' : 'text-danger';
                        ?>
                        <div class="col-md-3 mb-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <h6 class="card-title text-muted mb-2"><?= esc($bank['kode_bank'] ?? '') ?></h6>
                                    <h5 class="card-text mb-1"><?= esc($bank['nama_bank'] ?? '') ?></h5>
                                    <h4 class="<?= $saldoClass ?> fw-bold mt-2">
                                        Rp <?= number_format($saldo, 2) ?>
                                    </h4>
                                    <div class="small text-muted mt-2">
                                        <span class="text-success me-2">↑ Rp <?= number_format($bank['total_masuk'] ?? 0, 0) ?></span>
                                        <span class="text-danger">↓ Rp <?= number_format($bank['total_keluar'] ?? 0, 0) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i> Belum ada data saldo bank.
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
                <form method="get" action="<?= site_url('accounting/kas-bank/mutasi-bank') ?>" id="filterForm">
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
                                <option value="Masuk" <?= ($filters['tipe'] ?? '') == 'Masuk' ? 'selected' : '' ?>>Masuk</option>
                                <option value="Keluar" <?= ($filters['tipe'] ?? '') == 'Keluar' ? 'selected' : '' ?>>Keluar</option>
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
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <div class="d-flex justify-content-end w-100">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i> Terapkan Filter
                                </button>
                                <a href="<?= site_url('accounting/kas-bank/mutasi-bank') ?>" class="btn btn-secondary">
                                    <i class="fas fa-redo me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="modern-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i> Daftar Mutasi Bank
                        <span class="badge bg-info ms-2"><?= $total ?? 0 ?> Data</span>
                    </h5>
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <small class="text-muted">Menampilkan <?= count($mutasi) ?> dari <?= $total ?? 0 ?> data</small>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">Tanggal</th>
                                <th width="12%">Kode Transaksi</th>
                                <th width="8%">Tipe</th>
                                <th width="12%">Akun</th>
                                <th width="10%">Bank</th>
                                <th width="10%" class="text-end">Jumlah</th>
                                <th width="10%" class="text-end">Saldo</th>
                                <th width="8%">Referensi</th>
                                <th width="8%">Status</th>
                                <th width="7%">Proyek</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($mutasi)): ?>
                            <tr>
                                <td colspan="12" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-2x mb-3"></i>
                                        <h5>Tidak ada data mutasi bank</h5>
                                        <p>Silakan buat transaksi baru atau ubah filter pencarian.</p>
                                    </div>
                                </td>
                            </table>
                            <?php else: ?>
                                <?php 
                                $start = ($currentPage - 1) * $perPage + 1;
                                foreach ($mutasi as $i => $item): 
                                    $tipeUser = ($item['tipe'] == 'Kredit') ? 'Masuk' : 'Keluar';
                                    $tipeClass = ($item['tipe'] == 'Kredit') ? 'bg-success' : 'bg-danger';
                                    $tipeIcon = ($item['tipe'] == 'Kredit') ? 'fa-arrow-down' : 'fa-arrow-up';
                                ?>
                                <tr>
                                    <td><?= $start + $i ?></td>
                                    <td><?= date('d/m/Y', strtotime($item['tanggal'])) ?></td>
                                    <td>
                                        <strong><?= esc($item['kode_transaksi']) ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge <?= $tipeClass ?>">
                                            <i class="fas <?= $tipeIcon ?> me-1"></i> <?= $tipeUser ?>
                                        </span>
                                        <small class="d-block text-muted mt-1" style="font-size: 10px;">
                                            (DB: <?= $item['tipe'] ?>)
                                        </small>
                                    </td>
                                    <td>
                                        <?php if ($item['tipe'] == 'Kredit'): ?>
                                            <small><?= esc($item['kode_akun_kredit'] ?? '') ?></small><br>
                                            <small class="text-muted"><?= esc($item['nama_akun_kredit'] ?? '-') ?></small>
                                        <?php else: ?>
                                            <small><?= esc($item['kode_akun_debit'] ?? '') ?></small><br>
                                            <small class="text-muted"><?= esc($item['nama_akun_debit'] ?? '-') ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $bank = '-';
                                        if ($item['tipe'] == 'Debit' && !empty(trim($item['bank_asal'] ?? ''))) {
                                            $bank = trim($item['bank_asal']);
                                        } elseif ($item['tipe'] == 'Kredit' && !empty(trim($item['bank_tujuan'] ?? ''))) {
                                            $bank = trim($item['bank_tujuan']);
                                        }
                                        echo esc($bank);
                                        ?>
                                    </td>
                                    <td class="text-end <?= $item['tipe'] == 'Kredit' ? 'text-success' : 'text-danger' ?> fw-bold">
                                        Rp <?= number_format($item['jumlah'], 2) ?>
                                    </td>
                                    <td class="text-end fw-bold <?= ($item['saldo_berjalan'] ?? 0) >= 0 ? 'text-success' : 'text-danger' ?>">
                                        Rp <?= number_format($item['saldo_berjalan'] ?? 0, 2) ?>
                                    </td>
                                    <td>
                                        <small><?= esc($item['no_referensi'] ?: '-') ?></small>
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
                                        <?php if (!empty($item['nomor_spk'])): ?>
                                            <span class="badge bg-info" title="<?= esc($item['judul_pekerjaan'] ?? '') ?>">
                                                <?= esc($item['nomor_spk']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= site_url('accounting/kas-bank/mutasi-bank/detail/' . $item['id']) ?>" 
                                               class="btn btn-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <?php if ($item['status'] == 'Draft'): ?>
                                                <a href="<?= site_url('accounting/kas-bank/mutasi-bank/edit/' . $item['id']) ?>" 
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
                        $baseUrl = site_url('accounting/kas-bank/mutasi-bank');
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
                        
                        <?php 
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($totalPages, $currentPage + 2);
                        
                        if ($startPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= $baseUrl . '1' ?>">1</a>
                            </li>
                            <?php if ($startPage > 2): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php for ($p = $startPage; $p <= $endPage; $p++): ?>
                        <li class="page-item <?= $p == $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="<?= $baseUrl . $p ?>"><?= $p ?></a>
                        </li>
                        <?php endfor; ?>
                        
                        <?php if ($endPage < $totalPages): ?>
                            <?php if ($endPage < $totalPages - 1): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= $baseUrl . $totalPages ?>"><?= $totalPages ?></a>
                            </li>
                        <?php endif; ?>
                        
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
                    <p>Apakah Anda yakin akan memposting mutasi bank ini ke jurnal?</p>
                    <p class="text-muted small">Setelah diposting, transaksi tidak dapat diedit lagi.</p>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Akun Bank akan ditentukan otomatis</strong>
                        <p class="mb-0 small mt-2">
                            Sistem akan mencocokkan bank asal/tujuan dengan akun kas/bank yang tersedia.
                        </p>
                    </div>
                    
<div class="alert alert-secondary small mt-2 mb-0">
    <i class="fas fa-sync-alt me-2"></i>
    <strong>Informasi Mapping & Jurnal:</strong>
    <ul class="mb-0 mt-1">
        <li><strong class="text-success">"Masuk"</strong> → Database: <strong class="text-success">"KREDIT"</strong> → Jurnal: <strong>Debit (Kas Bank), Kredit (Akun Lawan)</strong></li>
        <li><strong class="text-danger">"Keluar"</strong> → Database: <strong class="text-danger">"DEBIT"</strong> → Jurnal: <strong>Debit (Akun Lawan), Kredit (Kas Bank)</strong></li>
    </ul>
</div>
                    
                    <input type="hidden" name="mutasi_id" id="mutasi_id">
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
                <h6 class="text-center mb-3">Saldo tidak mencukupi untuk melakukan transaksi keluar!</h6>
                <div id="saldoWarningDetail" class="small"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="<?= site_url('accounting/kas-bank/mutasi-bank/create?tipe=Masuk') ?>" class="btn btn-success">
                    <i class="fas fa-plus-circle me-1"></i> Tambah Saldo
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle posting button click
    document.querySelectorAll('.btn-post').forEach(btn => {
        btn.addEventListener('click', function() {
            var id = this.dataset.id;
            document.getElementById('mutasi_id').value = id;
            
            var postModal = new bootstrap.Modal(document.getElementById('postModal'));
            postModal.show();
        });
    });
    
    // Handle post form submit
    document.getElementById('postForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        var id = document.getElementById('mutasi_id').value;
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        submitBtn.disabled = true;
        
        fetch('<?= site_url('accounting/kas-bank/mutasi-bank/post') ?>/' + id, {
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
            
            if (confirm('Apakah Anda yakin akan menghapus mutasi bank ini?')) {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                this.disabled = true;
                
                fetch('<?= site_url('accounting/kas-bank/mutasi-bank/delete') ?>/' + id, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    }
                })
                .then(response => response.json())
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
            
            if (confirm('Apakah Anda yakin akan membatalkan mutasi bank ini? Jurnal terkait akan di-void.')) {
                fetch('<?= site_url('accounting/kas-bank/mutasi-bank/batalkan') ?>/' + id, {
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
    
    // Export PDF function
    const btnExportPdf = document.getElementById('btnExportPdf');
    if (btnExportPdf) {
        btnExportPdf.addEventListener('click', function() {
            const filterForm = document.getElementById('filterForm');
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData).toString();
            
            const url = '<?= site_url("accounting/kas-bank/mutasi-bank/export-pdf") ?>?' + params;
            window.open(url, '_blank');
        });
    }
    
    // Cek saldo sebelum tambah transaksi keluar
    const btnTransaksiKeluar = document.getElementById('btnTransaksiKeluar');
    if (btnTransaksiKeluar) {
        btnTransaksiKeluar.addEventListener('click', function(e) {
            e.preventDefault();
            
            const bankSaldo = <?= json_encode($ringkasanBank ?? []) ?>;
            const bankDenganSaldo = bankSaldo.filter(bank => (bank.total_masuk - bank.total_keluar) > 0);
            
            if (bankDenganSaldo.length === 0) {
                let detailHtml = '<p>Tidak ada bank dengan saldo yang mencukupi:</p><ul>';
                bankSaldo.forEach(bank => {
                    const saldo = (bank.total_masuk || 0) - (bank.total_keluar || 0);
                    detailHtml += `<li><strong>${bank.nama_bank || 'Unknown'}</strong>: Rp ${formatRupiah(saldo)}</li>`;
                });
                detailHtml += '</ul>';
                
                document.getElementById('saldoWarningDetail').innerHTML = detailHtml;
                const warningModal = new bootstrap.Modal(document.getElementById('saldoWarningModal'));
                warningModal.show();
            } else {
                window.location.href = this.href;
            }
        });
    }
    
    function formatRupiah(angka) {
        return angka.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }
});
</script>

<style>
.modern-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    padding: 20px;
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

.card {
    border: none;
    border-radius: 10px;
}

.table th, .table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.65rem;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.alert-info {
    background-color: #e7f3ff;
    border-left: 4px solid #4dabf7;
}
</style>

<?= $this->include('accounting/templates/footer') ?>