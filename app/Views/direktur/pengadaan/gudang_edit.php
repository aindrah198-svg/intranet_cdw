<?php
// app/Views/direktur/pengadaan/gudang_edit.php

$title = $title ?? 'Edit Barang Gudang';
$templateData = [
    'title' => $title,
    'active' => 'pengadaan'
];

echo view('direktur/templates/header', $templateData);
echo view('direktur/templates/sidebar', $templateData);
echo view('direktur/templates/navbar', $templateData);
?>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= base_url('direktur/pengadaan/gudang') ?>" class="text-decoration-none text-muted">Monitoring Gudang</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Edit Barang #<?= esc($g['kode_barang']) ?></li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-edit text-warning me-2"></i> Edit Barang / Material Gudang</h4>
            <small class="text-muted">Perbarui stok fisik, lokasi gudang, rak, dan foto barang.</small>
        </div>
        <div>
            <a href="<?= base_url('direktur/pengadaan/gudang') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                <div class="card-header bg-gradient-primary text-white py-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fs-6 fw-bold mb-0"><i class="fas fa-edit me-2"></i> Edit Data #<?= esc($g['kode_barang']) ?></h5>
                    <span class="badge bg-white text-primary rounded-pill px-3 py-1 text-xs fw-bold"><?= esc($g['lokasi_gudang'] ?: 'Gudang Blok K') ?></span>
                </div>
                <form action="<?= base_url('direktur/pengadaan/gudang/update') ?>" method="POST" enctype="multipart/form-data" onsubmit="const btn = this.querySelector('button[type=submit]'); if(btn){ btn.disabled = true; btn.innerHTML = '<i class=\'fas fa-spinner fa-spin me-1\'></i> Menyimpan...'; }">
                    <input type="hidden" name="id" value="<?= $g['id'] ?>">
                    <div class="card-body p-4">
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Kode Barang (Sistem)</label>
                                <input type="text" class="form-control rounded-3 bg-light fw-bold text-primary" name="kode_barang" value="<?= esc($g['kode_barang']) ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Nama Barang / Material *</label>
                                <input type="text" class="form-control rounded-3" name="nama_barang" value="<?= esc($g['nama_barang']) ?>" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fw-semibold text-xs text-dark mb-0">Kategori Barang</label>
                                    <button type="button" class="btn btn-link text-primary text-xs p-0 text-decoration-none fw-bold" data-bs-toggle="modal" data-bs-target="#tambahKategoriModal">
                                        <i class="fas fa-plus-circle me-1"></i> + Kategori Baru
                                    </button>
                                </div>
                                <select name="kategori" id="selectKategori" class="form-select rounded-3">
                                    <?php foreach(($categories ?? []) as $cat): ?>
                                        <option value="<?= esc($cat) ?>" <?= strtolower($g['kategori'] ?? '') == strtolower($cat) ? 'selected' : '' ?>><?= esc($cat) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-xs text-dark">Lokasi Gudang *</label>
                                <select name="lokasi_gudang" class="form-select rounded-3 fw-semibold text-dark" required>
                                    <option value="Kantor" <?= strtolower($g['lokasi_gudang'] ?? '') == 'kantor' ? 'selected' : '' ?>>🏢 Kantor</option>
                                    <option value="Gudang Blok K" <?= strtolower($g['lokasi_gudang'] ?? '') == 'gudang blok k' || empty($g['lokasi_gudang']) ? 'selected' : '' ?>>🏭 Gudang Blok K</option>
                                    <option value="Gudang Blok I" <?= strtolower($g['lokasi_gudang'] ?? '') == 'gudang blok i' ? 'selected' : '' ?>>🏭 Gudang Blok I</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-xs text-dark">Lokasi Rak / Sektor *</label>
                                <input type="text" class="form-control rounded-3" name="lokasi_rak" value="<?= esc($g['lokasi_rak']) ?>" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-xs text-dark">Jumlah Stok Fisik *</label>
                                <input type="number" min="0" class="form-control rounded-3" name="stok_tersedia" value="<?= esc($g['stok_tersedia']) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-xs text-dark">Satuan Barang *</label>
                                <input type="text" class="form-control rounded-3" name="satuan" value="<?= esc($g['satuan']) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-xs text-dark">Status Stok</label>
                                <select name="status" class="form-select rounded-3">
                                    <option value="tersedia" <?= strtolower($g['status']) == 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                                    <option value="indent" <?= strtolower($g['status']) == 'indent' ? 'selected' : '' ?>>Indent (Dalam Pengiriman)</option>
                                    <option value="kosong" <?= strtolower($g['status']) == 'kosong' || strtolower($g['status']) == 'habis' ? 'selected' : '' ?>>Kosong / Habis</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Foto Barang / Material</label>
                            <?php if (!empty($g['foto_barang']) && file_exists(ROOTPATH . 'public/uploads/gudang/' . $g['foto_barang'])): ?>
                                <div class="mb-2 d-flex align-items-center gap-3 p-2 bg-light rounded-3 border">
                                    <img src="<?= base_url('uploads/gudang/' . $g['foto_barang']) ?>" alt="Foto Barang" class="rounded-3 shadow-sm" style="width: 70px; height: 70px; object-fit: cover;">
                                    <small class="text-muted text-xs">Foto terkini terkompresi. Upload baru di bawah jika ingin mengganti.</small>
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control rounded-3" name="foto_barang" accept="image/*">
                            <small class="text-muted text-xs d-block mt-1">
                                <i class="fas fa-compress-alt me-1 text-success"></i> Foto baru akan dikompresi otomatis oleh sistem (GD/Resize 800px & 70% Quality).
                            </small>
                        </div>

                    </div>
                    <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                        <a href="<?= base_url('direktur/pengadaan/gudang') ?>" class="btn btn-secondary rounded-pill px-4 me-2 font-semibold">Batal</a>
                        <button type="submit" class="btn btn-warning text-white rounded-pill px-4 font-semibold shadow-sm">
                            <i class="fas fa-save me-1.5"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Kategori Baru -->
<div class="modal fade" id="tambahKategoriModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white rounded-top-4 py-2.5 px-3">
                <h6 class="modal-title fw-bold mb-0 text-sm"><i class="fas fa-tags me-1.5"></i> Tambah Kategori Baru</h6>
                <button type="button" class="btn-close btn-close-white text-xs" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <label class="form-label text-xs fw-semibold text-dark">Nama Kategori Barang Baru *</label>
                <input type="text" id="inputKategoriBaru" class="form-control form-control-sm rounded-3" placeholder="Cth: Perkakas Tangan, Alat Ukur">
            </div>
            <div class="modal-footer bg-light rounded-bottom-4 py-2 px-3">
                <button type="button" class="btn btn-xs btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-xs btn-primary rounded-pill px-3 fw-semibold" onclick="tambahKategoriOption()">Gunakan Kategori</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function tambahKategoriOption() {
    const input = document.getElementById('inputKategoriBaru');
    const val = input.value.trim();
    if (!val) {
        Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Masukkan nama kategori baru!', timer: 2000, customClass: { popup: 'rounded-4' } });
        return;
    }
    
    const select = document.getElementById('selectKategori');
    let exists = false;
    for (let opt of select.options) {
        if (opt.value.toLowerCase() === val.toLowerCase()) {
            opt.selected = true;
            exists = true;
            break;
        }
    }
    
    if (!exists) {
        const newOpt = new Option(val, val, true, true);
        select.add(newOpt, select.options[0]);
    }
    
    input.value = '';
    const modalEl = document.getElementById('tambahKategoriModal');
    const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    modal.hide();
    
    Swal.fire({
        icon: 'success',
        title: 'Kategori Ditambahkan!',
        text: 'Kategori "' + val + '" siap digunakan.',
        timer: 1800,
        showConfirmButton: false,
        customClass: { popup: 'rounded-4' }
    });
}
</script>

<?= view('direktur/templates/footer', $templateData) ?>
