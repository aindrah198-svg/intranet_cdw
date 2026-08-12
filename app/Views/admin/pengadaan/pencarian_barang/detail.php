<?php
$title = $title ?? 'Detail Penugasan Pencarian Barang & RAB';
$templateData = [
    'title' => $title,
    'user'  => $user ?? ['name' => 'Admin', 'role' => 'admin'],
    'active' => 'pengadaan'
];
?>

<?= view('admin/templates/header', $templateData) ?>
<?= view('admin/templates/sidebar', $templateData) ?>
<?= view('admin/templates/navbar', $templateData) ?>

<div class="container-fluid py-3 py-md-4 fade-in pb-5">
    <!-- Back Header -->
    <div class="d-flex align-items-center mb-4">
        <a href="<?= base_url('admin/pengadaan/pencarian-barang') ?>" class="btn btn-outline-secondary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h4 class="mb-0 fw-bold text-dark"><?= esc($p['judul']) ?></h4>
            <small class="text-muted">Instruksi Pencarian Barang & Penentuan Harga RAB dari Direktur</small>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Informasi Penugasan (Left Column) -->
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                <h5 class="fw-bold text-dark mb-3">
                    <i class="fas fa-info-circle text-primary me-2"></i> Rincian Instruksi Direktur
                </h5>
                
                <div class="mb-3">
                    <label class="text-muted small fw-bold text-uppercase">Dibuat Oleh:</label>
                    <div class="fw-bold text-dark"><?= esc($p['pembuat_tugas'] ?? 'Direktur') ?></div>
                </div>

                <div class="mb-3">
                    <label class="text-muted small fw-bold text-uppercase">Ditugaskan Kepada:</label>
                    <div class="fw-bold text-dark"><?= esc($p['ditugaskan_kepada'] ?? 'Tim Admin / Pengadaan') ?></div>
                </div>

                <div class="mb-3">
                    <label class="text-muted small fw-bold text-uppercase">Deskripsi & Catatan Barang:</label>
                    <div class="p-3 bg-light rounded-3 border text-dark" style="font-size: 0.92rem; white-space: pre-wrap;"><?= esc($p['deskripsi']) ?></div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="text-muted small fw-bold text-uppercase">Tanggal Mulai:</label>
                        <div class="fw-semibold text-dark"><i class="fas fa-calendar-alt text-primary me-1"></i> <?= !empty($p['tanggal_mulai']) ? date('d M Y', strtotime($p['tanggal_mulai'])) : '-' ?></div>
                    </div>
                    <div class="col-6">
                        <label class="text-muted small fw-bold text-uppercase">Batas Waktu / Deadline:</label>
                        <div class="fw-semibold text-danger"><i class="fas fa-clock text-danger me-1"></i> <?= !empty($p['batas_waktu']) ? date('d M Y', strtotime($p['batas_waktu'])) : '-' ?></div>
                    </div>
                </div>

                <div class="mt-auto pt-3 border-top">
                    <label class="text-muted small fw-bold text-uppercase me-2">Status Saat Ini:</label>
                    <span class="badge bg-primary px-3 py-1.5 rounded-pill uppercase fw-semibold"><?= strtoupper(esc($p['status'])) ?></span>
                </div>
            </div>
        </div>

        <!-- Form Update Hasil Pencarian (Right Column) -->
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <h5 class="fw-bold text-dark mb-3">
                    <i class="fas fa-edit text-success me-2"></i> Input Hasil Pencarian Barang & Harga (RAB)
                </h5>

                <form action="<?= base_url('admin/pengadaan/pencarian-barang/update-hasil/' . $p['id']) ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Status Pencarian</label>
                        <select name="status" class="form-select rounded-3">
                            <option value="proses" <?= $p['status'] === 'proses' ? 'selected' : '' ?>>Sedang Dalam Proses Pencarian</option>
                            <option value="selesai" <?= ($p['status'] === 'selesai' || empty($p['status']) || $p['status'] === 'baru') ? 'selected' : '' ?>>Selesai / Terkirim ke Direktur</option>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Tipe Pembelian / Sumber</label>
                            <select name="tipe_pembelian" class="form-select rounded-3">
                                <option value="online_marketplace" <?= $p['tipe_pembelian'] === 'online_marketplace' ? 'selected' : '' ?>>Online Marketplace (Tokopedia/Shopee/dll)</option>
                                <option value="toko_offline" <?= $p['tipe_pembelian'] === 'toko_offline' ? 'selected' : '' ?>>Toko Offline / Supplier Direct</option>
                                <option value="import_distributor" <?= $p['tipe_pembelian'] === 'import_distributor' ? 'selected' : '' ?>>Distributor Resmi / Import</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Nama Toko / Seller / Marketplace</label>
                            <input type="text" name="nama_toko_marketplace" class="form-control rounded-3" value="<?= esc($p['nama_toko_marketplace'] ?? '') ?>" placeholder="Cth: Tokopedia Official Store X">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Nominal Estimasi RAB (Rp)</label>
                        <input type="text" name="nominal_estimasi" class="form-control rounded-3 fw-bold text-success" value="<?= !empty($p['nominal_estimasi']) ? number_format($p['nominal_estimasi'], 0, ',', '.') : '' ?>" placeholder="Cth: 1.500.000">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Hasil Pencarian & Detail Perbandingan Harga</label>
                        <textarea name="hasil_pencarian" class="form-control rounded-3" rows="5" placeholder="Tuliskan link toko, spek barang, opsi perbandingan harga, atau garansi..."><?= esc($p['hasil_pencarian'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small">Upload Lampiran / Screenshot Hasil Pencarian (Opsional)</label>
                        <input type="file" name="lampiran_hasil" class="form-control rounded-3">
                        <?php if (!empty($p['lampiran_hasil'])): ?>
                            <?php
                                $admFile = $p['lampiran_hasil'];
                                $admExt = strtolower(pathinfo($admFile, PATHINFO_EXTENSION));
                                $admIsImage = in_array($admExt, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                $admUrl = base_url('uploads/pencarian_barang/' . $admFile);
                            ?>
                            <div class="mt-3 p-3 bg-light border rounded-3 text-start">
                                <label class="fw-bold text-dark text-xs text-uppercase mb-2 d-block"><i class="fas fa-paperclip me-1 text-primary"></i> Lampiran Terupload Saat Ini:</label>
                                <?php if ($admIsImage): ?>
                                    <div class="mb-2 text-center bg-white p-2 rounded border">
                                        <a href="<?= $admUrl ?>" target="_blank" title="Klik untuk memperbesar">
                                            <img src="<?= $admUrl ?>" alt="Lampiran Admin" class="img-fluid rounded" style="max-height: 250px; object-fit: contain;">
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <a href="<?= $admUrl ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                                    <i class="fas fa-external-link-alt me-1"></i> Buka Gambar / File Full
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('admin/pengadaan/pencarian-barang') ?>" class="btn btn-secondary rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm fw-semibold">
                            <i class="fas fa-paper-plane me-1"></i> Simpan & Kirim ke Direktur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= view('admin/templates/footer', $templateData) ?>
