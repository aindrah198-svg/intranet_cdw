<?= $this->include('accounting/templates/header') ?>
<?= $this->include('accounting/templates/sidebar') ?>
<?= $this->include('accounting/templates/navbar') ?>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="page-title mb-1">Jurnal Umum</h2>
                    <p class="page-subtitle text-muted mb-0"><?= $subtitle ?></p>
                </div>
                <div class="btn-group">
                    <a href="<?= site_url('accounting/pembukuan/jurnal-umum/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Buat Jurnal
                    </a>
                    
                    <!-- Export Button with Filters -->
                    <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" 
                            aria-expanded="false" id="exportDropdown">
                        <i class="fas fa-download me-1"></i> Export
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="exportDropdown">
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
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="exportCurrentPage()">
                                <i class="fas fa-filter me-2"></i> Halaman Saat Ini
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="exportAllData()">
                                <i class="fas fa-database me-2"></i> Semua Data
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <?php foreach ($stats as $stat): ?>
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
                                    <i class="fas fa-<?= $stat['icon'] ?>"></i>
                                </span>
                                <span><?= $stat['trend'] ?></span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-<?= $stat['icon'] ?> fa-2x text-<?= $stat['color'] ?>"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach ?>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="modern-card">
                <h5 class="card-title mb-3">
                    <i class="fas fa-filter me-2"></i> Filter Data
                </h5>
                <form method="get" action="<?= site_url('accounting/pembukuan/jurnal-umum') ?>" id="filterForm">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="search" class="form-label">Cari</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   placeholder="No Jurnal/Keterangan..." value="<?= $filters['search'] ?? '' ?>">
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
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <?php foreach ($statusOptions as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= ($filters['status'] ?? '') == $value ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="tipe_referensi" class="form-label">Tipe Referensi</label>
                            <input type="text" class="form-control" id="tipe_referensi" name="tipe_referensi"
                                   placeholder="Tipe referensi..." value="<?= $filters['tipe_referensi'] ?? '' ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i> Terapkan Filter
                                </button>
                                <a href="<?= site_url('accounting/pembukuan/jurnal-umum') ?>" class="btn btn-secondary">
                                    <i class="fas fa-redo me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    <div class="row">
        <div class="col-12">
            <div class="modern-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i> Daftar Jurnal
                        <span class="badge bg-info ms-2"><?= $pager['total'] ?? 0 ?> Data</span>
                    </h5>
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <small class="text-muted">Menampilkan <?= count($jurnal) ?> dari <?= $pager['total'] ?? 0 ?> data</small>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <?= $pager['per_page'] ?? 20 ?> per halaman
                            </button>
                            <ul class="dropdown-menu" id="perPageMenu">
                                <!-- Will be populated by JavaScript -->
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="15%">Nomor Jurnal</th>
                                <th width="10%">Tanggal</th>
                                <th width="25%">Keterangan</th>
                                <th width="15%">Referensi</th>
                                <th width="10%">Total</th>
                                <th width="10%">Status</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($jurnal)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-2x mb-3"></i>
                                        <h5>Tidak ada data jurnal</h5>
                                        <p>Silakan buat jurnal baru atau ubah filter pencarian.</p>
                                    </div>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($jurnal as $item): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?= $item['nomor_jurnal'] ?></div>
                                        <small class="text-muted">ID: <?= $item['id'] ?></small>
                                    </td>
                                    <td>
                                        <?= date('d/m/Y', strtotime($item['tanggal'])) ?>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 300px;" 
                                             title="<?= htmlspecialchars($item['keterangan']) ?>">
                                            <?= htmlspecialchars($item['keterangan']) ?>
                                        </div>
                                        <?php if ($item['tipe_referensi']): ?>
                                            <small class="text-muted">
                                                <i class="fas fa-tag me-1"></i><?= $item['tipe_referensi'] ?>
                                            </small>
                                        <?php endif ?>
                                    </td>
                                    <td>
                                        <?php if ($item['referensi']): ?>
                                            <span class="badge bg-light text-dark">
                                                <?= $item['referensi'] ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= number_format($item['total_debit'], 2) ?></div>
                                        <small class="text-muted">
                                            D: <?= number_format($item['total_debit'], 2) ?><br>
                                            K: <?= number_format($item['total_kredit'], 2) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php
                                        $statusColor = [
                                            'draft' => 'warning',
                                            'posted' => 'success',
                                            'void' => 'danger'
                                        ][$item['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $statusColor ?>">
                                            <i class="fas fa-<?= 
                                                $item['status'] == 'draft' ? 'edit' : 
                                                ($item['status'] == 'posted' ? 'check-circle' : 'ban') 
                                            ?> me-1"></i>
                                            <?= ucfirst($item['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= site_url('accounting/pembukuan/jurnal-umum/detail/' . $item['id']) ?>" 
                                               class="btn btn-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($item['status'] == 'draft'): ?>
                                                <a href="<?= site_url('accounting/pembukuan/jurnal-umum/edit/' . $item['id']) ?>" 
                                                   class="btn btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            <?php endif ?>
                                            <a href="<?= site_url('accounting/pembukuan/jurnal-umum/print/' . $item['id']) ?>" 
                                               target="_blank" class="btn btn-secondary" title="Cetak">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach ?>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($pager['total_pages'] > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center" id="pagination">
                        <!-- Will be populated by JavaScript -->
                    </ul>
                </nav>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Build filter query string from current URL
    function buildFilterQuery() {
        const urlParams = new URLSearchParams(window.location.search);
        let query = '';
        
        // Preserve all parameters except 'page'
        for (let [key, value] of urlParams.entries()) {
            if (key !== 'page') {
                query += (query ? '&' : '?') + key + '=' + encodeURIComponent(value);
            }
        }
        
        return query;
    }
    
    // Initialize filter query
    const filterQuery = buildFilterQuery();
    
    // Populate per page dropdown
    const perPageMenu = document.getElementById('perPageMenu');
    if (perPageMenu) {
        const options = [10, 20, 50, 100];
        const currentPerPage = <?= $pager['per_page'] ?? 20 ?>;
        
        options.forEach(option => {
            const li = document.createElement('li');
            const a = document.createElement('a');
            a.className = 'dropdown-item';
            a.href = filterQuery + (filterQuery ? '&' : '?') + 'per_page=' + option;
            a.textContent = option + ' per halaman';
            if (option === currentPerPage) {
                a.innerHTML += ' <i class="fas fa-check text-success float-end"></i>';
            }
            li.appendChild(a);
            perPageMenu.appendChild(li);
        });
    }
    
    // Populate pagination
    const pagination = document.getElementById('pagination');
    if (pagination) {
        const currentPage = <?= $pager['current_page'] ?? 1 ?>;
        const totalPages = <?= $pager['total_pages'] ?? 1 ?>;
        
        // Previous button
        const prevLi = document.createElement('li');
        prevLi.className = 'page-item ' + (currentPage === 1 ? 'disabled' : '');
        const prevA = document.createElement('a');
        prevA.className = 'page-link';
        prevA.href = currentPage > 1 ? filterQuery + (filterQuery ? '&' : '?') + 'page=' + (currentPage - 1) : '#';
        prevA.innerHTML = '<i class="fas fa-chevron-left"></i>';
        prevLi.appendChild(prevA);
        pagination.appendChild(prevLi);
        
        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                const li = document.createElement('li');
                li.className = 'page-item ' + (i === currentPage ? 'active' : '');
                const a = document.createElement('a');
                a.className = 'page-link';
                a.href = filterQuery + (filterQuery ? '&' : '?') + 'page=' + i;
                a.textContent = i;
                li.appendChild(a);
                pagination.appendChild(li);
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                const li = document.createElement('li');
                li.className = 'page-item disabled';
                const span = document.createElement('span');
                span.className = 'page-link';
                span.textContent = '...';
                li.appendChild(span);
                pagination.appendChild(li);
            }
        }
        
        // Next button
        const nextLi = document.createElement('li');
        nextLi.className = 'page-item ' + (currentPage === totalPages ? 'disabled' : '');
        const nextA = document.createElement('a');
        nextA.className = 'page-link';
        nextA.href = currentPage < totalPages ? filterQuery + (filterQuery ? '&' : '?') + 'page=' + (currentPage + 1) : '#';
        nextA.innerHTML = '<i class="fas fa-chevron-right"></i>';
        nextLi.appendChild(nextA);
        pagination.appendChild(nextLi);
    }
    
    // Export functions
    function exportWithFilters(format) {
        const baseUrl = '<?= site_url("accounting/pembukuan/jurnal-umum/export") ?>';
        const url = baseUrl + filterQuery;
        
        if (format === 'excel') {
            window.location.href = url;
        } else if (format === 'pdf') {
            alert('Export PDF sedang dalam pengembangan');
        }
    }
    
    function exportCurrentPage() {
        const baseUrl = '<?= site_url("accounting/pembukuan/jurnal-umum/export") ?>';
        const currentParams = window.location.search;
        window.location.href = baseUrl + currentParams;
    }
    
    function exportAllData() {
        const baseUrl = '<?= site_url("accounting/pembukuan/jurnal-umum/export") ?>';
        window.location.href = baseUrl;
    }
    
    // Auto-dismiss alerts
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            if (alert.classList.contains('alert-dismissible')) {
                const bsAlert = new bootstrap.Alert(alert);
                setTimeout(() => bsAlert.close(), 5000);
            }
        });
    }, 5000);
    
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
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl + F to focus search
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            const searchInput = document.getElementById('search');
            if (searchInput) {
                searchInput.focus();
            }
        }
        
        // Ctrl + E for export
        if (e.ctrlKey && e.key === 'e') {
            e.preventDefault();
            document.getElementById('exportDropdown').click();
        }
        
        // Ctrl + N for new jurnal
        if (e.ctrlKey && e.key === 'n') {
            e.preventDefault();
            window.location.href = '<?= site_url("accounting/pembukuan/jurnal-umum/create") ?>';
        }
    });
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<?= $this->include('accounting/templates/footer') ?>