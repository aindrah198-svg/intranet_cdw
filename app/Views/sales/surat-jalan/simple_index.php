<?php
$title = $title ?? 'Daftar Surat Jalan';
$active = $active ?? 'surat_jalan';
?>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-truck me-2"></i>
                <?= $title ?>
            </h1>
            <p class="text-muted">
                <?= $subtitle ?? 'Kelola surat jalan pengiriman' ?>
                <small class="d-block mt-1">
                    <i class="fas fa-file-alt me-1"></i>Total: <?= $totalCount ?? 0 ?> surat jalan
                </small>
            </p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Diproses
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $statusCount['diproses'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Dikirim
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $statusCount['dikirim'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Diterima
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $statusCount['diterima'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Dibatalkan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $statusCount['dibatalkan'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ban fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-2"></i>
                        Daftar Surat Jalan
                    </h6>
                    <a href="<?= base_url('sales/surat-jalan/create') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-2"></i> Buat Baru
                    </a>
                </div>
                <div class="card-body">
                    <?php if (empty($suratJalanList)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Belum ada surat jalan</p>
                            <a href="<?= base_url('sales/surat-jalan/create') ?>" class="btn btn-primary mt-3">
                                <i class="fas fa-plus me-2"></i> Buat Surat Jalan Pertama
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No. Surat Jalan</th>
                                        <th>Project</th>
                                        <th>Client</th>
                                        <th>Tanggal Kirim</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($suratJalanList as $sj): ?>
                                    <tr>
                                        <td>
                                            <strong class="text-primary"><?= htmlspecialchars($sj['nomor_surat_jalan']) ?></strong>
                                            <?php if (!empty($sj['nomor_invoice'])): ?>
                                            <br><small class="text-muted">Invoice: <?= htmlspecialchars($sj['nomor_invoice']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($sj['nama_project'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($sj['nama_perusahaan'] ?? '-') ?></td>
                                        <td>
                                            <?php 
                                            if (!empty($sj['tanggal_kirim']) && $sj['tanggal_kirim'] != '0000-00-00') {
                                                echo date('d/m/Y', strtotime($sj['tanggal_kirim']));
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $statusColors = [
                                                'diproses' => 'warning',
                                                'dikirim' => 'info',
                                                'diterima' => 'success',
                                                'dibatalkan' => 'danger'
                                            ];
                                            
                                            $statusText = [
                                                'diproses' => 'Diproses',
                                                'dikirim' => 'Dikirim',
                                                'diterima' => 'Diterima',
                                                'dibatalkan' => 'Dibatalkan'
                                            ];
                                            ?>
                                            <span class="badge bg-<?= $statusColors[$sj['status']] ?? 'secondary' ?>">
                                                <?= $statusText[$sj['status']] ?? $sj['status'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('sales/surat-jalan/detail/' . $sj['id']) ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
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
</div>

<!-- Simple JS -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Surat Jalan page loaded');
    
    // Auto-hide alerts after 5 seconds
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            if (alert.classList.contains('alert-dismissible')) {
                alert.remove();
            }
        });
    }, 5000);
});
</script>