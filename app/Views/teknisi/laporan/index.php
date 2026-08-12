<div class="content-wrapper p-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-chart-pie mr-2"></i>Dashboard Laporan & Keluhan</h4>
            <p class="text-muted mb-0">Pusat laporan pekerjaan harian, kendala lapangan, & mutasi stok</p>
        </div>
        <div>
            <a href="<?= site_url('teknisi/laporan/lapangan') ?>" class="btn btn-primary shadow-sm mr-2">
                <i class="fas fa-hard-hat mr-1"></i> Laporan Pekerjaan Harian
            </a>
            <a href="<?= site_url('teknisi/laporan/keluhan') ?>" class="btn btn-danger shadow-sm">
                <i class="fas fa-exclamation-triangle mr-1"></i> Sampaikan Keluhan Lapangan
            </a>
        </div>
    </div>

    <!-- Summary Box -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm bg-primary text-white p-3">
                <small class="text-uppercase font-weight-bold">Total Laporan Pekerjaan Harian</small>
                <h3 class="font-weight-bold mb-0 mt-2"><?= $totalLaporanHarian ?> Laporan</h3>
                <small class="text-white-50">Tersimpan dalam database laporan_harian</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm bg-danger text-white p-3">
                <small class="text-uppercase font-weight-bold">Total Keluhan & Kendala Lapangan</small>
                <h3 class="font-weight-bold mb-0 mt-2"><?= $totalKeluhan ?> Catatan</h3>
                <small class="text-white-50">Kendala fasilitas, K3, dan administrasi</small>
            </div>
        </div>
    </div>
</div>
