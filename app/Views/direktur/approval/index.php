<?php
$title = 'Approval Berkas';
$active = 'approval';
$subtitle = 'Pusat persetujuan semua jenis dokumen';
?>

<?= $this->include('direktur/templates/header') ?>
<?= $this->include('direktur/templates/sidebar') ?>
<?= $this->include('direktur/templates/navbar') ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="font-weight-bolder mb-0">Approval Berkas</h4>
            <p class="text-sm mb-0">Pusat persetujuan semua jenis dokumen</p>
        </div>
    </div>

    <!-- Approval Kategori -->
    <div class="row mt-4">
        <?php
        $approvals = [
            ['title' => 'Approval Cuti', 'icon' => 'fa-calendar-alt', 'color' => 'primary', 'link' => 'direktur/approval/cuti', 'count' => $pending_counts['cuti']],
            ['title' => 'Approval SPK', 'icon' => 'fa-file-contract', 'color' => 'success', 'link' => 'direktur/approval/spk', 'count' => $pending_counts['spk']],
            ['title' => 'Approval Kasbon', 'icon' => 'fa-money-bill-wave', 'color' => 'warning', 'link' => 'direktur/approval/kasbon', 'count' => $pending_counts['kasbon']],
            ['title' => 'Approval Dokumen', 'icon' => 'fa-file-alt', 'color' => 'info', 'link' => 'direktur/approval/dokumen', 'count' => $pending_counts['dokumen']],
            ['title' => 'Approval Pembelian', 'icon' => 'fa-shopping-cart', 'color' => 'danger', 'link' => 'direktur/approval/pembelian', 'count' => $pending_counts['pembelian']],
            ['title' => 'Approval Surat Jalan', 'icon' => 'fa-truck', 'color' => 'secondary', 'link' => 'direktur/approval/surat-jalan', 'count' => $pending_counts['surat_jalan']],
            ['title' => 'Approval Izin', 'icon' => 'fa-user-edit', 'color' => 'dark', 'link' => 'direktur/approval/izin', 'count' => $pending_counts['izin']],
            ['title' => 'Approval BAST', 'icon' => 'fa-file-signature', 'color' => 'primary', 'link' => 'direktur/approval/bast', 'count' => $pending_counts['bast']],
        ];
        ?>

        <?php foreach ($approvals as $app): ?>
        <div class="col-xl-3 col-sm-6 mb-4">
            <a href="<?= base_url($app['link']) ?>" class="text-decoration-none">
                <div class="card shadow-sm h-100 modern-card fade-in" style="transition: transform 0.3s; cursor: pointer; border-left: 4px solid var(--bs-<?= $app['color'] ?>);" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-capitalize font-weight-bold text-muted"><?= $app['title'] ?></p>
                                    <h5 class="font-weight-bolder mb-0 mt-2">
                                        <?= $app['count'] ?> <span class="text-sm text-secondary font-weight-normal">Pending</span>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-<?= $app['color'] ?> text-white text-center rounded-circle" style="width: 48px; height: 48px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                                    <i class="fas <?= $app['icon'] ?>" aria-hidden="true"></i>
                                </div>
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