<?php
$title = $title ?? 'Detail Barang Gudang';
$templateData = [
    'title'  => $title,
    'user'   => session()->get('user') ?? ['name' => session()->get('name') ?? 'Administrator', 'role' => 'admin'],
    'active' => 'inventaris'
];

if (!function_exists('getGudangImgUrlAdmin')) {
    function getGudangImgUrlAdmin($foto) {
        if (empty($foto)) return '';
        $cleanFoto = str_replace(['uploads/gudang/', 'public/uploads/gudang/'], '', $foto);
        
        if (file_exists(FCPATH . 'uploads/gudang/' . $cleanFoto)) {
            return base_url('uploads/gudang/' . $cleanFoto);
        }
        if (file_exists(ROOTPATH . 'public/uploads/gudang/' . $cleanFoto)) {
            return base_url('uploads/gudang/' . $cleanFoto);
        }
        if (filter_var($foto, FILTER_VALIDATE_URL) || strpos($foto, 'http') === 0) {
            return $foto;
        }
        return base_url('uploads/gudang/' . $cleanFoto);
    }
}
$imgUrl = getGudangImgUrlAdmin($g['foto_barang'] ?? '');
?>

<?= view('admin/templates/header', $templateData) ?>
<?= view('admin/templates/sidebar', $templateData) ?>
<?= view('admin/templates/navbar', $templateData) ?>

<div class="container-fluid py-3 py-md-4">
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light flex-wrap gap-3">
        <div class="d-flex align-items-center">
            <a href="<?= base_url('admin/inventaris/gudang') ?>" class="btn btn-outline-secondary rounded-pill me-3 px-3 py-1.5 text-xs fw-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali
            </a>
            <div>
                <h4 class="mb-0 fw-bold text-dark fs-5">Detail Barang Gudang #<?= esc($g['kode_barang']) ?></h4>
                <small class="text-muted">Spesifikasi material, saldo stok & lokasi penyimpanan fisik.</small>
            </div>
        </div>
        <div>
            <a href="<?= base_url('admin/inventaris/gudang/edit/' . $g['id']) ?>" class="btn btn-primary rounded-pill shadow-sm">
                <i class="fas fa-edit me-1.5"></i> Edit Material
            </a>
        </div>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <div class="border-bottom pb-3 mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-box text-primary me-2"></i> <?= esc($g['nama_barang']) ?></h5>
                    <?php
                        $st = strtolower($g['status'] ?? 'tersedia');
                        $badgeClass = 'bg-success text-white';
                        if ($st === 'indent') $badgeClass = 'bg-warning text-dark';
                        elseif ($st === 'kosong' || $st === 'habis') $badgeClass = 'bg-danger text-white';
                    ?>
                    <span class="badge <?= $badgeClass ?> px-3 py-1.5 rounded-pill text-xs">
                        <?= ucfirst(esc($g['status'])) ?>
                    </span>
                </div>

                <div class="row g-3 text-sm mb-4">
                    <div class="col-12 col-sm-6">
                        <small class="text-muted text-xs uppercase d-block">Kode Material</small>
                        <strong class="text-primary fs-6"><?= esc($g['kode_barang']) ?></strong>
                    </div>
                    <div class="col-12 col-sm-6">
                        <small class="text-muted text-xs uppercase d-block">Kategori</small>
                        <span class="badge bg-light text-dark border px-2.5 py-1.5 rounded-pill"><?= esc($g['kategori'] ?: 'Material') ?></span>
                    </div>
                    <div class="col-12 col-sm-6">
                        <small class="text-muted text-xs uppercase d-block">Stok Fisik Tersedia</small>
                        <strong class="text-dark fs-5"><?= number_format($g['stok_tersedia']) ?> <?= esc($g['satuan']) ?></strong>
                    </div>
                    <div class="col-12 col-sm-6">
                        <small class="text-muted text-xs uppercase d-block">Lokasi Gudang & Rak</small>
                        <strong class="text-dark"><i class="fas fa-warehouse text-primary me-1"></i><?= esc($g['lokasi_gudang']) ?></strong>
                        <small class="text-muted d-block"><?= esc($g['lokasi_rak'] ?: '-') ?></small>
                    </div>
                </div>

                <?php if (!empty($imgUrl)): ?>
                    <div class="border-top pt-3">
                        <small class="text-muted text-xs uppercase d-block mb-2">Foto Fisik Barang</small>
                        <a href="<?= $imgUrl ?>" target="_blank">
                            <img src="<?= $imgUrl ?>" alt="Foto Material" class="img-fluid rounded-3 border shadow-sm" style="max-height: 320px; object-fit: contain;">
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= view('admin/templates/footer', $templateData) ?>
