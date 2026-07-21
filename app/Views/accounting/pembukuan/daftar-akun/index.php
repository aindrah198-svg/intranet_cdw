<?php

$title = $title ?? 'Chart of Accounts';
$active = $active ?? 'bookkeeping';
$subtitle = $subtitle ?? 'Manajemen Daftar Akun Perusahaan';

// Get request parameters
$search = service('request')->getGet('search');
$tipeAkun = service('request')->getGet('tipe_akun');
$status = service('request')->getGet('status');
?>

<?= $this->include('accounting/templates/header') ?>
<?= $this->include('accounting/templates/sidebar') ?>
<?= $this->include('accounting/templates/navbar') ?>


    
<div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title mb-1">Chart of Accounts</h1>
                        <p class="page-subtitle text-muted mb-0"><?= $subtitle ?></p>
                    </div>
                    <div class="btn-group">
                        <a href="<?= site_url('accounting/pembukuan/daftar-akun/create') ?>" class="btn btn-accounting">
                            <i class="fas fa-plus me-1"></i> Tambah Akun
                        </a>
                        <a href="<?= site_url('accounting/pembukuan/daftar-akun/tree') ?>" class="btn btn-accounting-outline">
                            <i class="fas fa-sitemap me-1"></i> Struktur
                        </a>
                        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-download me-1"></i> Export
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= site_url('accounting/pembukuan/daftar-akun/export') ?>">
                                <i class="fas fa-file-excel me-2"></i> Excel
                            </a></li>
                            <li><a class="dropdown-item" href="<?= site_url('accounting/pembukuan/daftar-akun/print') ?>" target="_blank">
                                <i class="fas fa-print me-2"></i> Print
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts Section -->
        <?php if (session()->getFlashdata('success')): ?>
        <div class="row">
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-left: 4px solid #28a745;">
                    <div class="d-flex align-items-center">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 32px; height: 32px;">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Sukses!</h6>
                            <p class="mb-0"><?= session()->getFlashdata('success') ?></p>
                        </div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        </div>
        <?php endif ?>
        
        <?php if (session()->getFlashdata('error')): ?>
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-left: 4px solid #dc3545;">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 32px; height: 32px;">
                            <i class="fas fa-exclamation"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Error!</h6>
                            <p class="mb-0"><?= session()->getFlashdata('error') ?></p>
                        </div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        </div>
        <?php endif ?>
        
        <?php if (session()->getFlashdata('errors')): ?>
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-left: 4px solid #dc3545;">
                    <div class="d-flex">
                        <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" 
                             style="width: 32px; height: 32px;">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <h6 class="mb-2">Harap perbaiki kesalahan berikut:</h6>
                            <ul class="mb-0 ps-3" style="list-style: disc;">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach ?>
                            </ul>
                        </div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        </div>
        <?php endif ?>
        
        <?php if (isset($error_message)): ?>
        <div class="row">
            <div class="col-12">
                <div class="alert alert-warning alert-dismissible fade show" role="alert" style="border-left: 4px solid #ffc107;">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 32px; height: 32px;">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Perhatian!</h6>
                            <p class="mb-0"><?= $error_message ?></p>
                        </div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        </div>
        <?php endif ?>

        <!-- COA Statistics Section -->
        <div class="row mb-4">
            <?php 
            // Statistik default jika tidak ada data
            $coaStatsSummary = $coaStatsSummary ?? [
                'total_akun' => ['value' => 0, 'label' => 'Total Akun', 'icon' => 'fas fa-list', 'color' => 'primary', 'trend' => 'Memuat...'],
                'akun_aktif' => ['value' => 0, 'label' => 'Akun Aktif', 'icon' => 'fas fa-check-circle', 'color' => 'success', 'trend' => 'Memuat...'],
                'akun_header' => ['value' => 0, 'label' => 'Akun Header', 'icon' => 'fas fa-folder', 'color' => 'info', 'trend' => 'Memuat...'],
                'akun_detail' => ['value' => 0, 'label' => 'Akun Detail', 'icon' => 'fas fa-file', 'color' => 'warning', 'trend' => 'Memuat...']
            ];
            
            foreach ($coaStatsSummary as $key => $stat): 
                $color = $stat['color'] ?? 'primary';
                $icon = $stat['icon'] ?? 'fas fa-list';
            ?>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="financial-card financial-card-<?= $color ?> shadow-hover">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2"><?= $stat['label'] ?></h6>
                            <h4 class="mb-0"><?= number_format($stat['value']) ?></h4>
                            <small class="text-<?= $color ?>">
                                <i class="<?= $icon ?> me-1"></i>
                                <?= $stat['trend'] ?>
                            </small>
                        </div>
                        <div class="bg-<?= $color ?> text-white rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 40px; height: 40px;">
                            <i class="<?= $icon ?>"></i>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Search and Filter Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3 text-accounting-primary">
                            <i class="fas fa-search me-2"></i>Filter & Pencarian
                        </h5>
                        <form method="get" action="<?= site_url('accounting/pembukuan/daftar-akun') ?>" class="row g-3">
                            <div class="col-md-5">
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control border-start-0" 
                                           placeholder="Cari kode atau nama akun..." 
                                           value="<?= esc($search ?? '') ?>"
                                           style="border-radius: var(--border-radius-sm);">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="tipe_akun" class="form-select" style="border-radius: var(--border-radius-sm);">
                                    <option value="">Semua Tipe Akun</option>
                                    <option value="Aset" <?= ($tipeAkun ?? '') == 'Aset' ? 'selected' : '' ?>>Aset</option>
                                    <option value="Kewajiban" <?= ($tipeAkun ?? '') == 'Kewajiban' ? 'selected' : '' ?>>Kewajiban</option>
                                    <option value="Ekuitas" <?= ($tipeAkun ?? '') == 'Ekuitas' ? 'selected' : '' ?>>Ekuitas</option>
                                    <option value="Pendapatan" <?= ($tipeAkun ?? '') == 'Pendapatan' ? 'selected' : '' ?>>Pendapatan</option>
                                    <option value="Beban" <?= ($tipeAkun ?? '') == 'Beban' ? 'selected' : '' ?>>Beban</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select" style="border-radius: var(--border-radius-sm);">
                                    <option value="">Semua Status</option>
                                    <option value="1" <?= ($status ?? '') == '1' ? 'selected' : '' ?>>Aktif</option>
                                    <option value="0" <?= ($status ?? '') == '0' ? 'selected' : '' ?>>Nonaktif</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-accounting flex-grow-1">
                                        <i class="fas fa-search me-1"></i> Cari
                                    </button>
                                    <a href="<?= site_url('accounting/pembukuan/daftar-akun') ?>" class="btn btn-accounting-outline">
                                        <i class="fas fa-redo"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tipe Akun Statistics -->
        <?php if (!empty($coaStats['by_type'])): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3 text-accounting-primary">
                            <i class="fas fa-chart-pie me-2"></i>Statistik Berdasarkan Tipe Akun
                        </h5>
                        <div class="row">
                            <?php foreach ($coaStats['by_type'] as $tipe => $typeStats): 
                                $tipeColor = [
                                    'Aset' => 'primary',
                                    'Kewajiban' => 'warning',
                                    'Ekuitas' => 'success',
                                    'Pendapatan' => 'info',
                                    'Beban' => 'danger'
                                ][$tipe] ?? 'secondary';
                            ?>
                            <div class="col-md-4 col-sm-6 mb-3">
                                <div class="card border-<?= $tipeColor ?> shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="card-title text-<?= $tipeColor ?> mb-1">
                                                    <i class="fas fa-cube me-1"></i> <?= $tipe ?>
                                                </h6>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <small class="text-muted">Total</small>
                                                        <h5 class="mb-0"><?= number_format($typeStats['total']) ?></h5>
                                                    </div>
                                                    <div class="vr"></div>
                                                    <div class="ms-3">
                                                        <small class="text-muted">Aktif</small>
                                                        <h5 class="mb-0"><?= number_format($typeStats['active']) ?></h5>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-<?= $tipeColor ?> opacity-50">
                                                <i class="fas fa-chart-pie fa-2x"></i>
                                            </div>
                                        </div>
                                        <div class="progress mt-2" style="height: 6px;">
                                            <div class="progress-bar bg-<?= $tipeColor ?>" 
                                                 style="width: <?= $typeStats['total'] > 0 ? round(($typeStats['active'] / $typeStats['total']) * 100) : 0 ?>%">
                                            </div>
                                        </div>
                                        <small class="text-muted mt-2 d-block">
                                            <?= $typeStats['header'] ?> header, <?= $typeStats['detail'] ?> detail
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- COA Table -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0 text-accounting-primary">
                                <i class="fas fa-list-alt me-2"></i>Daftar Akun
                            </h5>
                            <div>
                                <small class="text-muted">
                                    <?php if (!empty($coa) && is_array($coa)): ?>
                                        Menampilkan <?= count($coa) ?> akun
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>
                        
                        <?php if (!empty($coa) && is_array($coa)): ?>
                        <div class="table-responsive">
                            <table id="coa-table" class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="12%">Kode Akun</th>
                                        <th width="28%">Nama Akun</th>
                                        <th width="12%">Tipe Akun</th>
                                        <th width="12%">Saldo Normal</th>
                                        <th width="8%">Level</th>
                                        <th width="8%">Jenis</th>
                                        <th width="10%">Status</th>
                                        <th width="10%" class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($coa as $account): ?>
                                        <tr class="<?= $account['is_active'] == 0 ? 'table-secondary opacity-75' : '' ?>">
                                            <td>
                                                <strong class="coa-code"><?= $account['kode_akun'] ?></strong>
                                                <?php if ($account['indent'] ?? false): ?>
                                                    <?= $account['indent'] ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($account['is_header'] == 1): ?>
                                                    <strong class="text-primary">
                                                        <i class="fas fa-folder me-1"></i>
                                                        <?= $account['nama_akun'] ?>
                                                    </strong>
                                                <?php else: ?>
                                                    <i class="fas fa-file me-1 text-muted"></i>
                                                    <?= $account['nama_akun'] ?>
                                                <?php endif ?>
                                                <?php if (!empty($account['kategori'])): ?>
                                                    <br><small class="text-muted"><?= $account['kategori'] ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $badgeClass = [
                                                    'Aset' => 'primary',
                                                    'Kewajiban' => 'warning',
                                                    'Ekuitas' => 'success',
                                                    'Pendapatan' => 'info',
                                                    'Beban' => 'danger'
                                                ][$account['tipe_akun']] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?= $badgeClass ?>">
                                                    <?= $account['tipe_akun'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $account['saldo_normal'] == 'Debit' ? 'success' : 'warning' ?>">
                                                    <i class="fas fa-<?= $account['saldo_normal'] == 'Debit' ? 'arrow-down' : 'arrow-up' ?> me-1"></i>
                                                    <?= $account['saldo_normal'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?= $account['level'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($account['is_header'] == 1): ?>
                                                    <span class="badge bg-info">Header</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Detail</span>
                                                <?php endif ?>
                                            </td>
                                            <td>
                                                <?php if ($account['is_active'] == 1): ?>
                                                    <span class="badge bg-success">Aktif</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Nonaktif</span>
                                                <?php endif ?>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= site_url('accounting/pembukuan/daftar-akun/detail/' . $account['id']) ?>" 
                                                       class="btn btn-info" title="Detail" data-bs-toggle="tooltip">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= site_url('accounting/pembukuan/daftar-akun/edit/' . $account['id']) ?>" 
                                                       class="btn btn-primary" title="Edit" data-bs-toggle="tooltip">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-danger delete-btn" 
                                                            data-id="<?= $account['id'] ?>" 
                                                            data-name="<?= $account['nama_akun'] ?>" 
                                                            data-kode="<?= $account['kode_akun'] ?>"
                                                            title="Hapus" data-bs-toggle="tooltip">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if (isset($pager) && $pager): ?>
                        <div class="card-footer border-top-0 bg-transparent pt-3">
                            <?= $this->include('accounting/templates/pagination') ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php else: ?>
                        <div class="text-center py-5">
                            <div class="text-muted mb-4">
                                <i class="fas fa-database fa-3x mb-3 opacity-50"></i>
                                <h4 class="text-muted">Belum ada data Chart of Accounts</h4>
                                <p class="mb-4">Mulai dengan menambahkan akun pertama Anda</p>
                                <a href="<?= site_url('accounting/pembukuan/daftar-akun/create') ?>" class="btn btn-accounting">
                                    <i class="fas fa-plus me-1"></i> Tambah Akun Pertama
                                </a>
                            </div>
                        </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-gradient-accounting text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus akun <strong id="deleteAccountName"></strong> (<code id="deleteAccountCode"></code>)?</p>
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <small>Tindakan ini tidak dapat dibatalkan. Akun akan dinonaktifkan jika sudah digunakan dalam transaksi.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="post" style="display: inline;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom CSS untuk COA Index */
.modal-header.bg-gradient-accounting {
    background: linear-gradient(135deg, var(--accounting-primary), var(--accounting-secondary)) !important;
}

.coa-code {
    font-family: 'Courier New', monospace;
    background: #f8f9fa;
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 600;
    color: var(--accounting-primary);
}

.financial-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 20px;
    margin-bottom: 0;
    box-shadow: var(--shadow-sm);
    border: none;
    transition: var(--transition);
    border-left: 5px solid transparent;
    height: 100%;
}

.financial-card-income {
    border-left-color: var(--accounting-success) !important;
}

.financial-card-expense {
    border-left-color: var(--accounting-danger) !important;
}

.financial-card-asset {
    border-left-color: var(--accounting-info) !important;
}

.financial-card-liability {
    border-left-color: var(--accounting-warning) !important;
}

.financial-card-primary {
    border-left-color: var(--accounting-primary) !important;
}

.financial-card-success {
    border-left-color: var(--accounting-success) !important;
}

.financial-card-info {
    border-left-color: var(--accounting-info) !important;
}

.financial-card-warning {
    border-left-color: var(--accounting-warning) !important;
}

.shadow-hover:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

/* Table styling */
#coa-table {
    border-radius: var(--border-radius-sm);
    overflow: hidden;
}

#coa-table thead th {
    background: linear-gradient(135deg, var(--accounting-primary) 0%, var(--accounting-secondary) 100%);
    color: white;
    border: none;
    padding: 12px 16px;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
}

#coa-table tbody tr {
    transition: var(--transition);
}

#coa-table tbody tr:hover {
    background: rgba(76, 123, 217, 0.05) !important;
}

.table-secondary.opacity-75 {
    opacity: 0.75;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    if ($('#coa-table').length) {
        const table = $('#coa-table').DataTable({
            "paging": true,
            "lengthChange": true,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "order": [[0, 'asc']],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
            },
            "columnDefs": [
                { "orderable": false, "targets": [7] } // Disable sorting for action column
            ],
            "dom": '<"row"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
            "initComplete": function() {
                // Add custom search box
                this.api().columns([1, 2, 5, 6]).every(function() {
                    var column = this;
                    var select = $('<select class="form-select form-select-sm"><option value="">Semua</option></select>')
                        .appendTo($(column.footer()).empty())
                        .on('change', function() {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                            column.search(val ? '^' + val + '$' : '', true, false).draw();
                        });
                    
                    column.data().unique().sort().each(function(d, j) {
                        select.append('<option value="' + d + '">' + d + '</option>');
                    });
                });
            }
        });
    }

    // Delete button click handler
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        const accountId = $(this).data('id');
        const accountName = $(this).data('name');
        const accountCode = $(this).data('kode');
        
        $('#deleteAccountName').text(accountName);
        $('#deleteAccountCode').text(accountCode);
        $('#deleteForm').attr('action', '<?= site_url("accounting/pembukuan/daftar-akun/delete") ?>/' + accountId);
        
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    });

    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Add search functionality to filter columns
    function addColumnSearch(table) {
        // Add search inputs for each column
        $('#coa-table thead tr').clone(true).addClass('filters').appendTo('#coa-table thead');
        
        table.columns().every(function() {
            var column = this;
            var title = $(column.header()).text();
            var $input = $('<input type="text" class="form-control form-control-sm" placeholder="Search ' + title + '" />')
                .appendTo($(column.header()).empty())
                .on('keyup change', function() {
                    if (column.search() !== this.value) {
                        column.search(this.value).draw();
                    }
                });
        });
    }
    
    // Update card colors based on status
    function updateCardColors() {
        $('.financial-card').each(function() {
            var $card = $(this);
            var colorClass = $card.attr('class').match(/financial-card-(\w+)/);
            if (colorClass) {
                var color = colorClass[1];
                $card.css('border-left-color', getComputedStyle(document.documentElement).getPropertyValue('--accounting-' + color));
            }
        });
    }
    
    // Initialize card colors
    updateCardColors();
    
    // Handle form changes warning
    let formChanged = false;
    $(document).on('change', 'input, select, textarea', function() {
        formChanged = true;
    });
    
    $(document).on('submit', 'form', function() {
        formChanged = false;
    });
    
    $(window).on('beforeunload', function() {
        if (formChanged) {
            return 'Anda memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman?';
        }
    });
});

// AJAX untuk validasi kode akun
function validateKodeAkun(kode, tipe, exceptId = null) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: '<?= site_url("accounting/pembukuan/daftar-akun/ajax-validate-kode") ?>',
            type: 'GET',
            data: {
                kode: kode,
                tipe: tipe,
                except_id: exceptId
            },
            success: function(response) {
                resolve(response);
            },
            error: function(error) {
                reject(error);
            }
        });
    });
}

// AJAX untuk mendapatkan info parent
function getParentInfo(parentId) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: '<?= site_url("accounting/pembukuan/daftar-akun/ajax-get-parent-info") ?>',
            type: 'GET',
            data: { parent_id: parentId },
            success: function(response) {
                resolve(response);
            },
            error: function(error) {
                reject(error);
            }
        });
    });
}
</script>

<?= $this->include('accounting/templates/footer') ?>