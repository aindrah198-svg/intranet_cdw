<?php
$title = 'Monitoring Kinerja & Absensi';
$active = 'monitoring';
$subtitle = 'Dashboard Monitoring Perusahaan';
?>

<?= $this->include('direktur/templates/header') ?>
<?= $this->include('direktur/templates/sidebar') ?>
<?= $this->include('direktur/templates/navbar') ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="font-weight-bolder mb-0">Monitoring Kinerja & Absensi</h4>
            <p class="text-sm mb-0">Pusat monitoring seluruh kegiatan dan performa perusahaan</p>
        </div>
    </div>

    <!-- Monitoring Kategori -->
    <div class="row mt-4">
        <?php
        $monitorings = [
            ['title' => 'Monitoring Absensi', 'icon' => 'fa-user-clock', 'color' => 'primary', 'link' => 'direktur/monitoring/absensi', 'desc' => 'Pantau kehadiran, keterlambatan, dan jam kerja karyawan'],
            ['title' => 'Monitoring Performansi', 'icon' => 'fa-chart-bar', 'color' => 'success', 'link' => 'direktur/monitoring/performansi', 'desc' => 'Analisa kinerja, produktivitas, dan KPI karyawan'],
            ['title' => 'Ringkasan Penggajian', 'icon' => 'fa-money-check-alt', 'color' => 'warning', 'link' => 'direktur/monitoring/ringkasan-penggajian', 'desc' => 'Rekapitulasi beban gaji dan tunjangan perusahaan'],
            ['title' => 'Invoice & Piutang', 'icon' => 'fa-file-invoice-dollar', 'color' => 'info', 'link' => 'direktur/monitoring/invoice-piutang', 'desc' => 'Pantau status pembayaran klien dan piutang jatuh tempo'],
        ];
        ?>

        <?php foreach ($monitorings as $mon): ?>
        <div class="col-md-6 mb-4">
            <a href="<?= base_url($mon['link']) ?>" class="text-decoration-none">
                <div class="card shadow-sm h-100 modern-card fade-in" style="transition: transform 0.3s; cursor: pointer; border-left: 4px solid var(--bs-<?= $mon['color'] ?>);" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="icon icon-shape bg-<?= $mon['color'] ?> text-white text-center rounded-circle" style="width: 60px; height: 60px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                                    <i class="fas <?= $mon['icon'] ?>" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div class="col">
                                <div class="numbers">
                                    <h5 class="font-weight-bolder mb-1 text-dark"><?= $mon['title'] ?></h5>
                                    <p class="text-sm mb-0 text-muted"><?= $mon['desc'] ?></p>
                                </div>
                            </div>
                            <div class="col-auto text-end">
                                <i class="fas fa-chevron-right text-muted opacity-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?= $this->include('direktur/templates/footer') ?>
