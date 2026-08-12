<?php
$title = $title ?? 'Tambah Stok Barang Gudang Baru';
$templateData = [
    'title'  => $title,
    'user'   => session()->get('user') ?? ['name' => session()->get('name') ?? 'Administrator', 'role' => 'admin'],
    'active' => 'inventaris'
];
?>

<?= view('admin/templates/header', $templateData) ?>
<?= view('admin/templates/sidebar', $templateData) ?>
<?= view('admin/templates/navbar', $templateData) ?>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/inventaris/gudang') ?>" class="text-decoration-none text-muted">Monitoring Gudang</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Tambah Barang</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-boxes text-primary me-2"></i> Tambah Barang / Material Gudang</h4>
            <small class="text-muted">Kode barang otomatis oleh sistem. Pilih lokasi gudang dan upload foto barang.</small>
        </div>
        <div>
            <a href="<?= base_url('admin/inventaris/gudang') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                <div class="card-header bg-gradient-primary text-white py-3 px-4">
                    <h5 class="card-title fs-6 fw-bold mb-0"><i class="fas fa-plus-circle me-2"></i> Form Input Material Gudang Baru</h5>
                </div>
                <form action="<?= base_url('admin/inventaris/gudang/simpan') ?>" method="POST" enctype="multipart/form-data" onsubmit="const btn = this.querySelector('button[type=submit]'); if(btn){ btn.disabled = true; btn.innerHTML = '<i class=\'fas fa-spinner fa-spin me-1\'></i> Menyimpan...'; }">
                    <div class="card-body p-4">
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Kode Barang (Otomatis Sistem)</label>
                                <input type="text" class="form-control rounded-3 bg-light fw-bold text-primary" name="kode_barang" value="<?= esc($autoKode ?? 'MTR-AUTO') ?>" readonly title="Kode barang di-generate otomatis oleh sistem">
                                <small class="text-muted text-xs"><i class="fas fa-magic me-1"></i> Di-generate otomatis oleh sistem.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Nama Barang / Material *</label>
                                <input type="text" class="form-control rounded-3" name="nama_barang" placeholder="Cth: Semen Tiga Roda 50kg, Kabel NYM 3x2.5mm" required>
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
                                    <?php foreach(($categories ?? ['Material Konstruksi', 'Kelistrikan', 'Plumbing', 'Finishing', 'Sparepart Mesin', 'Lainnya']) as $cat): ?>
                                        <option value="<?= esc($cat) ?>"><?= esc($cat) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-xs text-dark">Lokasi Gudang *</label>
                                <select name="lokasi_gudang" class="form-select rounded-3 fw-semibold text-dark" required>
                                    <option value="Kantor">🏢 Kantor</option>
                                    <option value="Gudang Blok K" selected>🏭 Gudang Blok K</option>
                                    <option value="Gudang Blok I">🏭 Gudang Blok I</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-xs text-dark">Lokasi Rak / Sektor *</label>
                                <input type="text" class="form-control rounded-3" name="lokasi_rak" placeholder="Cth: Sektor A - Rak 02" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-xs text-dark">Jumlah Stok Fisik *</label>
                                <input type="number" min="0" class="form-control rounded-3" name="stok_tersedia" value="10" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-xs text-dark">Satuan Barang *</label>
                                <input type="text" class="form-control rounded-3" name="satuan" placeholder="Cth: Sak, Batang, Roll, Pcs, Pail" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-xs text-dark">Status Stok</label>
                                <select name="status" class="form-select rounded-3">
                                    <option value="tersedia" selected>Tersedia</option>
                                    <option value="indent">Indent (Dalam Pengiriman)</option>
                                    <option value="kosong">Kosong / Habis</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Foto Barang / Material</label>
                            <input type="file" class="form-control rounded-3" name="foto_barang" accept="image/*">
                            <small class="text-muted text-xs d-block mt-1">
                                <i class="fas fa-compress-alt me-1 text-success"></i> Foto akan dikompresi otomatis oleh sistem (GD/Resize 800px & 70% Quality) untuk menghemat ruang penyimpanan.
                            </small>
                        </div>

                    </div>
                    <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                        <a href="<?= base_url('admin/inventaris/gudang') ?>" class="btn btn-secondary rounded-pill px-4 me-2 font-semibold">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 font-semibold shadow-sm">
                            <i class="fas fa-save me-1.5"></i> Simpan Barang Gudang
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

<?= view('admin/templates/footer', $templateData) ?>
