<?php
$title = 'Monitoring Absensi';
$active = 'monitoring';
$subtitle = 'Dashboard Monitoring Absensi Karyawan';
?>

<?= $this->include('direktur/templates/header') ?>
<?= $this->include('direktur/templates/sidebar') ?>
<?= $this->include('direktur/templates/navbar') ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-clock me-2"></i>
                        Monitoring Absensi
                    </h4>
                </div>
                <div class="card-body">
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-tools fa-4x text-muted"></i>
                        </div>
                        <h3 class="text-muted mb-3">Fitur Sedang Dalam Pengembangan</h3>
                        <p class="text-muted">Halaman monitoring absensi sedang dalam proses pengembangan.</p>
                        <a href="<?= base_url('direktur') ?>" class="btn btn-primary mt-3">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('direktur/templates/footer') ?>