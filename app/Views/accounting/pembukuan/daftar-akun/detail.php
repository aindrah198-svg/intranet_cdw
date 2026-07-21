<?php

$title = $title ?? 'Detail Akun';
$active = $active ?? 'bookkeeping';
$subtitle = $subtitle ?? 'Detail Informasi Akun';

// Format untuk display
$coa['created_at_formatted'] = $coa['created_at_formatted'] ?? date('d/m/Y H:i', strtotime($coa['created_at']));
$coa['updated_at_formatted'] = $coa['updated_at_formatted'] ?? date('d/m/Y H:i', strtotime($coa['updated_at']));
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
                    <h2 class="page-title mb-1">Detail Akun</h2>
                    <p class="page-subtitle text-muted mb-0"><?= $subtitle ?></p>
                </div>
                <div class="btn-group">
                    <a href="<?= site_url('accounting/pembukuan/daftar-akun') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                    <a href="<?= site_url('accounting/pembukuan/daftar-akun/edit/' . $coa['id']) ?>" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-1"></i> Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Breadcrumb Navigation -->
    <?php if (!empty($accountPath) && count($accountPath) > 1): ?>
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-light p-3 rounded">
                    <li class="breadcrumb-item">
                        <a href="<?= site_url('accounting/pembukuan/daftar-akun') ?>" class="text-decoration-none">
                            <i class="fas fa-home"></i> Chart of Accounts
                        </a>
                    </li>
                    <?php foreach ($accountPath as $index => $pathAccount): ?>
                        <?php if ($index < count($accountPath) - 1): ?>
                        <li class="breadcrumb-item">
                            <a href="<?= site_url('accounting/pembukuan/daftar-akun/detail/' . $pathAccount['id']) ?>" class="text-decoration-none">
                                <?= $pathAccount['kode_akun'] ?> - <?= htmlspecialchars($pathAccount['nama_akun']) ?>
                            </a>
                        </li>
                        <?php else: ?>
                        <li class="breadcrumb-item active" aria-current="page">
                            <strong><?= $pathAccount['kode_akun'] ?> - <?= htmlspecialchars($pathAccount['nama_akun']) ?></strong>
                        </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </nav>
        </div>
    </div>
    <?php endif; ?>

    <!-- Alerts Section -->
    <?php if (session()->getFlashdata('success')): ?>
    <div class="row">
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <?php endif ?>

    <!-- Main Information Card -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="modern-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i> Informasi Akun</h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-<?= $coa['is_active'] ? 'success' : 'danger' ?>">
                            <?= $coa['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                        </span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="copyCodeBtn" 
                                data-bs-toggle="tooltip" title="Salin kode akun">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Kode Akun</small>
                        <div class="d-flex align-items-center">
                            <h4 class="mb-0 text-primary me-2"><?= htmlspecialchars($coa['kode_akun']) ?></h4>
                            <input type="hidden" id="accountCode" value="<?= htmlspecialchars($coa['kode_akun']) ?>">
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Nama Akun</small>
                        <h4 class="mb-0"><?= htmlspecialchars($coa['nama_akun']) ?></h4>
                    </div>
                    
                    <!-- Account Classification -->
                    <div class="col-md-4 mb-3">
                        <small class="text-muted d-block">Tipe Akun</small>
                        <span class="badge bg-<?= 
                            $coa['tipe_akun'] == 'Aset' ? 'primary' : 
                            ($coa['tipe_akun'] == 'Kewajiban' ? 'warning' : 
                            ($coa['tipe_akun'] == 'Ekuitas' ? 'success' : 
                            ($coa['tipe_akun'] == 'Pendapatan' ? 'info' : 'danger'))) 
                        ?> fs-6">
                            <?= htmlspecialchars($coa['tipe_akun']) ?>
                        </span>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <small class="text-muted d-block">Saldo Normal</small>
                        <span class="badge bg-<?= $coa['saldo_normal'] == 'Debit' ? 'success' : 'warning' ?> fs-6">
                            <i class="fas fa-<?= $coa['saldo_normal'] == 'Debit' ? 'arrow-down' : 'arrow-up' ?> me-1"></i>
                            <?= htmlspecialchars($coa['saldo_normal']) ?>
                        </span>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <small class="text-muted d-block">Jenis Akun</small>
                        <span class="badge bg-<?= $coa['is_header'] ? 'info' : 'warning' ?> fs-6">
                            <i class="fas fa-<?= $coa['is_header'] ? 'folder' : 'file' ?> me-1"></i>
                            <?= $coa['is_header'] ? 'Header (Grup)' : 'Detail (Transaksi)' ?>
                        </span>
                    </div>
                    
                    <!-- Hierarchy Information -->
                    <div class="col-md-3 mb-3">
                        <small class="text-muted d-block">Level</small>
                        <strong class="fs-5">Level <?= $coa['level'] ?></strong>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <small class="text-muted d-block">Kategori</small>
                        <strong class="fs-6"><?= $coa['kategori'] ? htmlspecialchars($coa['kategori']) : '-' ?></strong>
                    </div>
                    
                    <?php if (!empty($parent)): ?>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Parent Akun</small>
                        <a href="<?= site_url('accounting/pembukuan/daftar-akun/detail/' . $parent['id']) ?>" class="text-decoration-none">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-level-up-alt text-primary me-2"></i>
                                <div>
                                    <strong class="d-block"><?= htmlspecialchars($parent['kode_akun']) ?> - <?= htmlspecialchars($parent['nama_akun']) ?></strong>
                                    <small class="text-muted"><?= htmlspecialchars($parent['tipe_akun']) ?> | Level <?= $parent['level'] ?></small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Description -->
                    <?php if (!empty($coa['deskripsi'])): ?>
                    <div class="col-12 mt-2">
                        <small class="text-muted d-block">Deskripsi</small>
                        <div class="border rounded p-3 bg-light">
                            <?= nl2br(htmlspecialchars($coa['deskripsi'])) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Timestamps -->
                    <div class="col-12 mt-4 pt-3 border-top">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">Dibuat pada</small>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar-plus text-muted me-2"></i>
                                    <div>
                                        <strong><?= $coa['created_at_formatted'] ?></strong>
                                        <?php if (!empty($creator)): ?>
                                        <br><small class="text-muted">oleh <?= htmlspecialchars($creator['name'] ?? $creator['username'] ?? 'Sistem') ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <small class="text-muted">Diperbarui pada</small>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar-alt text-muted me-2"></i>
                                    <div>
                                        <strong><?= $coa['updated_at_formatted'] ?></strong>
                                        <?php if (!empty($updater)): ?>
                                        <br><small class="text-muted">oleh <?= htmlspecialchars($updater['name'] ?? $updater['username'] ?? 'Sistem') ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions Card -->
        <div class="col-lg-4">
            <div class="modern-card mb-4">
                <h5 class="card-title mb-3"><i class="fas fa-bolt me-2"></i> Aksi Cepat</h5>
                <div class="d-grid gap-2">
                    <?php if ($coa['is_active']): ?>
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deactivateModal">
                        <i class="fas fa-ban me-1"></i> Nonaktifkan Akun
                    </button>
                    <?php else: ?>
                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#activateModal">
                        <i class="fas fa-check me-1"></i> Aktifkan Akun
                    </button>
                    <?php endif; ?>
                    
                    <?php if ($coa['is_header']): ?>
                    <a href="<?= site_url('accounting/pembukuan/daftar-akun/create?parent=' . $coa['id']) ?>" class="btn btn-outline-primary">
                        <i class="fas fa-plus me-1"></i> Tambah Sub-Akun
                    </a>
                    <?php endif; ?>
                    
                    <a href="<?= site_url('accounting/pembukuan/daftar-akun/tree?highlight=' . $coa['id']) ?>" class="btn btn-outline-info">
                        <i class="fas fa-sitemap me-1"></i> Lihat Struktur
                    </a>
                    
                    <button type="button" id="printBtn" class="btn btn-outline-secondary">
                        <i class="fas fa-print me-1"></i> Cetak Detail
                    </button>
                </div>
            </div>
            
            <!-- Statistics Card -->
            <div class="modern-card">
                <h5 class="card-title mb-3"><i class="fas fa-chart-bar me-2"></i> Statistik</h5>
                <div class="row">
                    <div class="col-6 mb-3">
                        <div class="text-center">
                            <div class="display-6 fw-bold"><?= $coa['level'] ?></div>
                            <small class="text-muted">Level</small>
                        </div>
                    </div>
                    
                    <?php if ($coa['is_header'] && !empty($children)): ?>
                    <div class="col-6 mb-3">
                        <div class="text-center">
                            <div class="display-6 fw-bold"><?= count($children) ?></div>
                            <small class="text-muted">Sub-Akun</small>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="col-6">
                        <div class="text-center">
                            <div class="display-6 fw-bold text-<?= $coa['saldo_normal'] == 'Debit' ? 'success' : 'warning' ?>">
                                <?= $coa['saldo_normal'] == 'Debit' ? 'Dr' : 'Cr' ?>
                            </div>
                            <small class="text-muted">Saldo Normal</small>
                        </div>
                    </div>
                    
                    <div class="col-6">
                        <div class="text-center">
                            <div class="display-6 fw-bold text-<?= $coa['is_active'] ? 'success' : 'danger' ?>">
                                <i class="fas fa-<?= $coa['is_active'] ? 'check' : 'times' ?>"></i>
                            </div>
                            <small class="text-muted">Status</small>
                        </div>
                    </div>
                </div>
                
                <?php if (!$coa['is_header']): ?>
                <div class="mt-3 pt-3 border-top">
                    <small class="text-muted d-block mb-1">Digunakan untuk transaksi</small>
                    <div class="d-grid gap-1">
                        <a href="<?= site_url('accounting/pembukuan/jurnal-umum?coa=' . $coa['id']) ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-book me-1"></i> Lihat Jurnal
                        </a>
                        <a href="<?= site_url('accounting/pembukuan/buku-besar?coa=' . $coa['id']) ?>" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-book-open me-1"></i> Lihat Buku Besar
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Children Accounts Section (for Header accounts) -->
    <?php if ($coa['is_header'] && !empty($children)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="modern-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-sitemap me-2"></i> Sub-Akun (<?= count($children) ?>)
                    </h5>
                    <a href="<?= site_url('accounting/pembukuan/daftar-akun/create?parent=' . $coa['id']) ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i> Tambah Sub-Akun
                    </a>
                </div>
                
                <?php if (empty($children)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum ada sub-akun</h5>
                    <p class="text-muted">Tambahkan sub-akun untuk mengelompokkan akun detail</p>
                    <a href="<?= site_url('accounting/pembukuan/daftar-akun/create?parent=' . $coa['id']) ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Tambah Sub-Akun Pertama
                    </a>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="15%">Kode Akun</th>
                                <th width="25%">Nama Akun</th>
                                <th width="15%">Tipe</th>
                                <th width="15%">Saldo Normal</th>
                                <th width="10%">Status</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($children as $child): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($child['kode_akun']) ?></strong>
                                </td>
                                <td>
                                    <?php if ($child['is_header'] == 1): ?>
                                        <strong class="text-primary">
                                            <i class="fas fa-folder me-1"></i>
                                            <?= htmlspecialchars($child['nama_akun']) ?>
                                        </strong>
                                    <?php else: ?>
                                        <i class="fas fa-file me-1 text-muted"></i>
                                        <?= htmlspecialchars($child['nama_akun']) ?>
                                    <?php endif ?>
                                </td>
                                <td>
                                    <?php
                                    $badgeClass = [
                                        'Aset' => 'primary',
                                        'Kewajiban' => 'warning',
                                        'Ekuitas' => 'success',
                                        'Pendapatan' => 'info',
                                        'Beban' => 'danger'
                                    ][$child['tipe_akun']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?>">
                                        <?= htmlspecialchars($child['tipe_akun']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $child['saldo_normal'] == 'Debit' ? 'success' : 'warning' ?>">
                                        <?= htmlspecialchars($child['saldo_normal']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($child['is_active'] == 1): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Nonaktif</span>
                                    <?php endif ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= site_url('accounting/pembukuan/daftar-akun/detail/' . $child['id']) ?>" 
                                           class="btn btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= site_url('accounting/pembukuan/daftar-akun/edit/' . $child['id']) ?>" 
                                           class="btn btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger delete-child-btn" 
                                                data-id="<?= $child['id'] ?>" 
                                                data-name="<?= htmlspecialchars($child['nama_akun']) ?>"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Transaction History (if applicable) -->
    <?php if (!$coa['is_header']): ?>
    <div class="row">
        <div class="col-12">
            <div class="modern-card">
                <h5 class="card-title mb-3"><i class="fas fa-history me-2"></i> Informasi Transaksi</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i> Akun Detail (Transaksi)</h6>
                            <p class="mb-0">Akun ini digunakan untuk pencatatan transaksi keuangan. Data transaksi dapat dilihat di modul Jurnal dan Buku Besar.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-grid gap-2">
                            <a href="<?= site_url('accounting/pembukuan/jurnal-umum?coa=' . $coa['id']) ?>" class="btn btn-outline-primary">
                                <i class="fas fa-book me-1"></i> Lihat Jurnal
                            </a>
                            <a href="<?= site_url('accounting/pembukuan/buku-besar?coa=' . $coa['id']) ?>" class="btn btn-outline-info">
                                <i class="fas fa-book-open me-1"></i> Lihat Buku Besar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus akun <strong><?= htmlspecialchars($coa['nama_akun']) ?></strong> (<?= htmlspecialchars($coa['kode_akun']) ?>)?</p>
                
                <?php if ($coa['is_header'] && !empty($children)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>PERINGATAN!</strong> Akun ini adalah Header dengan <?= count($children) ?> sub-akun. 
                    Semua sub-akun juga akan dihapus!
                </div>
                <?php endif; ?>
                
                <p class="text-danger"><small>Tindakan ini tidak dapat dibatalkan.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="<?= site_url('accounting/pembukuan/daftar-akun/delete/' . $coa['id']) ?>" method="post" style="display: inline;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Deactivate Modal -->
<div class="modal fade" id="deactivateModal" tabindex="-1" aria-labelledby="deactivateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deactivateModalLabel">Nonaktifkan Akun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menonaktifkan akun <strong><?= htmlspecialchars($coa['nama_akun']) ?></strong>?</p>
                
                <?php if ($coa['is_header'] && !empty($children)): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Akun ini adalah Header dengan <?= count($children) ?> sub-akun. 
                    Semua sub-akun juga akan dinonaktifkan.
                </div>
                <?php endif; ?>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Akun yang dinonaktifkan tidak akan muncul dalam transaksi baru, 
                    tetapi data historis akan tetap tersimpan.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="<?= site_url('accounting/pembukuan/daftar-akun/update/' . $coa['id']) ?>" method="post" style="display: inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="is_active" value="0">
                    <button type="submit" class="btn btn-danger">Ya, Nonaktifkan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Activate Modal -->
<div class="modal fade" id="activateModal" tabindex="-1" aria-labelledby="activateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="activateModalLabel">Aktifkan Akun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin mengaktifkan akun <strong><?= htmlspecialchars($coa['nama_akun']) ?></strong>?</p>
                
                <?php if ($coa['is_header'] && !empty($children)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Akun ini adalah Header dengan <?= count($children) ?> sub-akun. 
                    Semua sub-akun juga akan diaktifkan.
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="<?= site_url('accounting/pembukuan/daftar-akun/update/' . $coa['id']) ?>" method="post" style="display: inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="is_active" value="1">
                    <button type="submit" class="btn btn-success">Ya, Aktifkan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Child Delete Modal -->
<div class="modal fade" id="childDeleteModal" tabindex="-1" aria-labelledby="childDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="childDeleteModalLabel">Hapus Sub-Akun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus sub-akun <strong id="childAccountName"></strong>?</p>
                <p class="text-danger"><small>Tindakan ini tidak dapat dibatalkan.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="childDeleteForm" method="post" style="display: inline;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Child delete buttons
    document.querySelectorAll('.delete-child-btn').forEach(button => {
        button.addEventListener('click', function() {
            const childId = this.getAttribute('data-id');
            const childName = this.getAttribute('data-name');
            
            document.getElementById('childAccountName').textContent = childName;
            document.getElementById('childDeleteForm').action = '<?= site_url("accounting/pembukuan/daftar-akun/delete") ?>/' + childId;
            
            const childDeleteModal = new bootstrap.Modal(document.getElementById('childDeleteModal'));
            childDeleteModal.show();
        });
    });

    // Copy account code to clipboard
    const copyCodeBtn = document.getElementById('copyCodeBtn');
    if (copyCodeBtn) {
        copyCodeBtn.addEventListener('click', function() {
            const code = document.getElementById('accountCode').value;
            navigator.clipboard.writeText(code).then(() => {
                // Show success message
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check me-1"></i> Tersalin!';
                this.classList.remove('btn-outline-secondary');
                this.classList.add('btn-success');
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.classList.remove('btn-success');
                    this.classList.add('btn-outline-secondary');
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy: ', err);
                alert('Gagal menyalin kode akun');
            });
        });
    }

    // Print functionality
    const printBtn = document.getElementById('printBtn');
    if (printBtn) {
        printBtn.addEventListener('click', function() {
            window.print();
        });
    }

    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<style>
.breadcrumb {
    background-color: #f8f9fa;
}

.breadcrumb-item a {
    color: #6c757d;
}

.breadcrumb-item a:hover {
    color: #4e73df;
}

.breadcrumb-item.active {
    color: #343a40;
    font-weight: 500;
}

.modern-card .table td, .modern-card .table th {
    vertical-align: middle;
}

.badge.fs-6 {
    font-size: 0.9em !important;
    padding: 0.5em 0.8em;
}

.display-6 {
    font-size: 2rem;
}

@media print {
    .btn-group, .breadcrumb, .modal, 
    .modern-card .d-grid, .alert,
    .table-responsive .btn-group {
        display: none !important;
    }
    
    .modern-card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
        page-break-inside: avoid;
    }
    
    h2, h4, h5, h6 {
        color: #000 !important;
    }
    
    .badge {
        border: 1px solid #000 !important;
        background-color: #fff !important;
        color: #000 !important;
    }
    
    .text-primary {
        color: #000 !important;
    }
    
    .border-top, .border {
        border-color: #000 !important;
    }
    
    a[href]:after {
        content: none !important;
    }
}
</style>

<?= $this->include('accounting/templates/footer') ?>