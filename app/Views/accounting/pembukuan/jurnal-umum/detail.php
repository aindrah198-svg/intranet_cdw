<?php

$title = $title ?? 'Detail Jurnal';
$active = $active ?? 'bookkeeping';
$subtitle = $subtitle ?? 'Detail Pencatatan Jurnal';

// Status colors mapping
$statusColors = [
    'draft' => 'secondary',
    'posted' => 'success',
    'void' => 'danger'
];

$statusIcons = [
    'draft' => 'edit',
    'posted' => 'check-circle',
    'void' => 'ban'
];
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
                    <h2 class="page-title mb-1">Detail Jurnal</h2>
                    <p class="page-subtitle text-muted mb-0"><?= $subtitle ?></p>
                </div>
                <div class="btn-group">
                    <a href="<?= site_url('accounting/pembukuan/jurnal-umum') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                    <?php if ($jurnal['status'] == 'draft'): ?>
                        <a href="<?= site_url('accounting/pembukuan/jurnal-umum/edit/' . $jurnal['id']) ?>" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <form action="<?= site_url('accounting/pembukuan/jurnal-umum/post/' . $jurnal['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Post jurnal ini? Jurnal tidak dapat diedit setelah diposting.');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check-circle me-1"></i> Post Jurnal
                            </button>
                        </form>
                    <?php endif ?>
                    <?php if ($jurnal['status'] == 'posted'): ?>
                        <form action="<?= site_url('accounting/pembukuan/jurnal-umum/void/' . $jurnal['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Void jurnal ini? Jurnal akan dinonaktifkan.');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-ban me-1"></i> Void Jurnal
                            </button>
                        </form>
                    <?php endif ?>
                    <?php if ($jurnal['status'] == 'draft'): ?>
                        <form action="<?= site_url('accounting/pembukuan/jurnal-umum/delete/' . $jurnal['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus jurnal ini? Tindakan ini tidak dapat dibatalkan.');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-1"></i> Hapus
                            </button>
                        </form>
                    <?php endif ?>
                    <a href="<?= site_url('accounting/pembukuan/jurnal-umum/print/' . $jurnal['id']) ?>" target="_blank" class="btn btn-info">
                        <i class="fas fa-print me-1"></i> Cetak
                    </a>
                </div>
            </div>
        </div>
    </div>

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
    
    <?php if (session()->getFlashdata('error')): ?>
    <div class="row">
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <?php endif ?>

    <!-- Jurnal Info Cards -->
    <div class="row mb-4">
        <!-- Jurnal Header Info -->
        <div class="col-md-8">
            <div class="modern-card mb-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="card-title mb-3">
                            <i class="fas fa-file-invoice me-2"></i> Informasi Jurnal
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Nomor Jurnal</label>
                                <h5 class="mb-0"><?= $jurnal['nomor_jurnal'] ?></h5>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Status</label>
                                <div>
                                    <span class="badge bg-<?= $statusColors[$jurnal['status']] ?? 'secondary' ?> fs-6">
                                        <i class="fas fa-<?= $statusIcons[$jurnal['status']] ?? 'question' ?> me-1"></i>
                                        <?= ucfirst($jurnal['status']) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Tanggal Jurnal</label>
                                <h6 class="mb-0"><?= date('d F Y', strtotime($jurnal['tanggal'])) ?></h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Balance Status</label>
                                <h6 class="mb-0 <?= $totalDebit == $totalKredit ? 'text-success' : 'text-danger' ?>">
                                    <?= $totalDebit == $totalKredit ? '✓ Balanced' : '✗ Unbalanced' ?>
                                </h6>
                            </div>
                        </div>
                        
                        <?php if ($jurnal['tipe_referensi'] || $jurnal['referensi']): ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Tipe Referensi</label>
                                <h6 class="mb-0"><?= $jurnal['tipe_referensi'] ? ucfirst($jurnal['tipe_referensi']) : '-' ?></h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Nomor Referensi</label>
                                <h6 class="mb-0"><?= $jurnal['referensi'] ?: '-' ?></h6>
                            </div>
                        </div>
                        <?php endif ?>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted">Keterangan</label>
                            <div class="border rounded p-3 bg-light">
                                <?= nl2br(htmlspecialchars($jurnal['keterangan'])) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Totals & Audit Info -->
        <div class="col-md-4">
            <!-- Totals Card -->
            <div class="modern-card modern-card-primary mb-4">
                <h5 class="card-title mb-3">
                    <i class="fas fa-calculator me-2"></i> Ringkasan Total
                </h5>
                
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label text-muted">Total Debit</label>
                        <h3 class="text-success"><?= number_format($totalDebit, 2) ?></h3>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted">Total Kredit</label>
                        <h3 class="text-warning"><?= number_format($totalKredit, 2) ?></h3>
                    </div>
                </div>
                
                <div class="text-center">
                    <div class="mb-2">
                        <span class="badge <?= $totalDebit == $totalKredit ? 'bg-success' : 'bg-danger' ?> fs-6">
                            <?= $totalDebit == $totalKredit ? 'BALANCED' : 'UNBALANCED' ?>
                        </span>
                    </div>
                    <?php if ($totalDebit != $totalKredit): ?>
                        <div class="text-danger small">
                            Selisih: <?= number_format(abs($totalDebit - $totalKredit), 2) ?>
                        </div>
                    <?php endif ?>
                </div>
            </div>
            
            <!-- Audit Info Card -->
            <div class="modern-card">
                <h5 class="card-title mb-3">
                    <i class="fas fa-history me-2"></i> Audit Trail
                </h5>
                
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-user-plus text-success me-2"></i>
                        <span class="text-muted">Dibuat oleh:</span>
                        <strong><?= $jurnal['creator_name'] ?? 'System' ?></strong>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-calendar-plus text-success me-2"></i>
                        <span class="text-muted">Tanggal dibuat:</span>
                        <strong><?= date('d/m/Y H:i', strtotime($jurnal['created_at'])) ?></strong>
                    </li>
                    
                    <?php if ($jurnal['status'] == 'posted'): ?>
                    <li class="mb-2">
                        <i class="fas fa-user-check text-primary me-2"></i>
                        <span class="text-muted">Diposting oleh:</span>
                        <strong><?= $jurnal['poster_name'] ?? 'System' ?></strong>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-calendar-check text-primary me-2"></i>
                        <span class="text-muted">Tanggal posting:</span>
                        <strong><?= date('d/m/Y H:i', strtotime($jurnal['posted_at'])) ?></strong>
                    </li>
                    <?php endif ?>
                    
                    <?php if ($jurnal['status'] == 'void'): ?>
                    <li class="mb-2">
                        <i class="fas fa-user-slash text-danger me-2"></i>
                        <span class="text-muted">Status:</span>
                        <strong class="text-danger">VOID</strong>
                    </li>
                    <?php endif ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Jurnal Details -->
    <div class="row">
        <div class="col-12">
            <div class="modern-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i> Detail Transaksi
                    </h5>
                    <div>
                        <span class="badge bg-light text-dark">
                            <i class="fas fa-layer-group me-1"></i>
                            <?= count($debitDetails) + count($kreditDetails) ?> Baris
                        </span>
                    </div>
                </div>
                
                <!-- Debit Section -->
                <?php if (!empty($debitDetails)): ?>
                <div class="mb-4">
                    <h6 class="text-success mb-3">
                        <i class="fas fa-arrow-down me-1"></i> Debit (<?= count($debitDetails) ?> Akun)
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="table-success">
                                <tr>
                                    <th width="15%">Kode Akun</th>
                                    <th width="25%">Nama Akun</th>
                                    <th width="15%">Tipe Akun</th>
                                    <th width="25%">Keterangan</th>
                                    <th width="10%" class="text-end">Jumlah</th>
                                    <th width="10%" class="text-end">Saldo Normal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($debitDetails as $detail): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-dark"><?= $detail['kode_akun'] ?></span>
                                    </td>
                                    <td>
                                        <strong><?= $detail['nama_akun'] ?></strong>
                                    </td>
                                    <td>
                                        <?php
                                        // PERBAIKAN: Seharusnya ada field tipe_akun di detail, jika tidak ada, tampilkan '-'
                                        $tipeAkun = $detail['tipe_akun'] ?? '-';
                                        $tipeBadge = [
                                            'Aset' => 'primary',
                                            'Kewajiban' => 'warning',
                                            'Ekuitas' => 'success',
                                            'Pendapatan' => 'info',
                                            'Beban' => 'danger'
                                        ][$tipeAkun] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $tipeBadge ?>">
                                            <?= $tipeAkun ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= $detail['keterangan'] ?: '-' ?>
                                    </td>
                                    <td class="text-end text-success fw-bold">
                                        <?= number_format($detail['debit'], 2) ?>
                                    </td>
                                    <td class="text-end">
                                        <?php
                                        // PERBAIKAN: Seharusnya ada field saldo_normal di detail
                                        $saldoNormal = $detail['saldo_normal'] ?? 'Debit';
                                        $saldoBadge = $saldoNormal == 'Debit' ? 'success' : 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $saldoBadge ?>">
                                            <?= $saldoNormal ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Subtotal Debit:</strong></td>
                                    <td class="text-end fw-bold text-success">
                                        <?= number_format($totalDebit, 2) ?>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <?php endif ?>
                
                <!-- Kredit Section -->
                <?php if (!empty($kreditDetails)): ?>
                <div>
                    <h6 class="text-warning mb-3">
                        <i class="fas fa-arrow-up me-1"></i> Kredit (<?= count($kreditDetails) ?> Akun)
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="table-warning">
                                <tr>
                                    <th width="15%">Kode Akun</th>
                                    <th width="25%">Nama Akun</th>
                                    <th width="15%">Tipe Akun</th>
                                    <th width="25%">Keterangan</th>
                                    <th width="10%" class="text-end">Jumlah</th>
                                    <th width="10%" class="text-end">Saldo Normal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($kreditDetails as $detail): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-dark"><?= $detail['kode_akun'] ?></span>
                                    </td>
                                    <td>
                                        <strong><?= $detail['nama_akun'] ?></strong>
                                    </td>
                                    <td>
                                        <?php
                                        // PERBAIKAN: Seharusnya ada field tipe_akun di detail, jika tidak ada, tampilkan '-'
                                        $tipeAkun = $detail['tipe_akun'] ?? '-';
                                        $tipeBadge = [
                                            'Aset' => 'primary',
                                            'Kewajiban' => 'warning',
                                            'Ekuitas' => 'success',
                                            'Pendapatan' => 'info',
                                            'Beban' => 'danger'
                                        ][$tipeAkun] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $tipeBadge ?>">
                                            <?= $tipeAkun ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= $detail['keterangan'] ?: '-' ?>
                                    </td>
                                    <td class="text-end text-warning fw-bold">
                                        <?= number_format($detail['kredit'], 2) ?>
                                    </td>
                                    <td class="text-end">
                                        <?php
                                        // PERBAIKAN: Seharusnya ada field saldo_normal di detail
                                        $saldoNormal = $detail['saldo_normal'] ?? 'Kredit';
                                        $saldoBadge = $saldoNormal == 'Kredit' ? 'warning' : 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $saldoBadge ?>">
                                            <?= $saldoNormal ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Subtotal Kredit:</strong></td>
                                    <td class="text-end fw-bold text-warning">
                                        <?= number_format($totalKredit, 2) ?>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <?php endif ?>
                
                <!-- Balance Summary -->
                <div class="alert <?= $totalDebit == $totalKredit ? 'alert-success' : 'alert-danger' ?> mt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="alert-heading mb-1">
                                <i class="fas fa-balance-scale me-1"></i>
                                Ringkasan Balance
                            </h6>
                            <div class="row">
                                <div class="col-6">
                                    <small>Total Debit:</small>
                                    <h5 class="text-success mb-0"><?= number_format($totalDebit, 2) ?></h5>
                                </div>
                                <div class="col-6">
                                    <small>Total Kredit:</small>
                                    <h5 class="text-warning mb-0"><?= number_format($totalKredit, 2) ?></h5>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="badge <?= $totalDebit == $totalKredit ? 'bg-success' : 'bg-danger' ?> fs-6">
                                <i class="fas fa-<?= $totalDebit == $totalKredit ? 'check' : 'times' ?>-circle me-1"></i>
                                <?= $totalDebit == $totalKredit ? 'BALANCED' : 'UNBALANCED' ?>
                            </div>
                            <?php if ($totalDebit != $totalKredit): ?>
                                <div class="small mt-1">
                                    Selisih: <?= number_format(abs($totalDebit - $totalKredit), 2) ?>
                                </div>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Action Buttons Footer -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="modern-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <a href="<?= site_url('accounting/pembukuan/jurnal-umum') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                        </a>
                    </div>
                    <div class="btn-group">
                        <?php if ($jurnal['status'] == 'draft'): ?>
                            <a href="<?= site_url('accounting/pembukuan/jurnal-umum/edit/' . $jurnal['id']) ?>" class="btn btn-primary">
                                <i class="fas fa-edit me-1"></i> Edit Jurnal
                            </a>
                            <form action="<?= site_url('accounting/pembukuan/jurnal-umum/post/' . $jurnal['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Post jurnal ini? Jurnal tidak dapat diedit setelah diposting.');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check-circle me-1"></i> Post Jurnal
                                </button>
                            </form>
                            <form action="<?= site_url('accounting/pembukuan/jurnal-umum/delete/' . $jurnal['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus jurnal ini? Tindakan ini tidak dapat dibatalkan.');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash me-1"></i> Hapus
                                </button>
                            </form>
                        <?php endif ?>
                        <?php if ($jurnal['status'] == 'posted'): ?>
                            <form action="<?= site_url('accounting/pembukuan/jurnal-umum/void/' . $jurnal['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Void jurnal ini? Jurnal akan dinonaktifkan.');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-ban me-1"></i> Void Jurnal
                                </button>
                            </form>
                        <?php endif ?>
                        <a href="<?= site_url('accounting/pembukuan/jurnal-umum/print/' . $jurnal['id']) ?>" target="_blank" class="btn btn-info">
                            <i class="fas fa-print me-1"></i> Cetak
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
    
    // Confirm actions
    const postForms = document.querySelectorAll('form[action*="post/"]');
    const voidForms = document.querySelectorAll('form[action*="void/"]');
    const deleteForms = document.querySelectorAll('form[action*="delete/"]');
    
    postForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Apakah Anda yakin ingin memposting jurnal ini?\n\nSetelah diposting, jurnal tidak dapat diedit.')) {
                e.preventDefault();
            }
        });
    });
    
    voidForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Apakah Anda yakin ingin meng-void jurnal ini?\n\nJurnal akan dinonaktifkan dan tidak dapat diposting kembali.')) {
                e.preventDefault();
            }
        });
    });
    
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Apakah Anda yakin ingin menghapus jurnal ini?\n\nTindakan ini tidak dapat dibatalkan.')) {
                e.preventDefault();
            }
        });
    });
});
</script>

<style>
/* Custom styles for detail page */
.modern-card-primary {
    border-left: 4px solid #0d6efd;
}

.table-success thead {
    background-color: rgba(25, 135, 84, 0.1) !important;
}

.table-warning thead {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.badge.fs-6 {
    font-size: 1em !important;
    padding: 0.5em 1em;
}

.alert-heading {
    font-size: 1.1rem;
    font-weight: 600;
}

/* Print-specific styles */
@media print {
    .btn-group,
    .alert,
    .form-label.text-muted,
    .modern-card {
        break-inside: avoid;
    }
    
    .btn {
        display: none !important;
    }
    
    .page-subtitle {
        display: none;
    }
}
</style>

<?= $this->include('accounting/templates/footer') ?>