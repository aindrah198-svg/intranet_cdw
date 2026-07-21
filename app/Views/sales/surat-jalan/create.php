<?php
$title = $title ?? 'Buat Surat Jalan Baru';
$active = $active ?? 'surat_jalan';

// Helper functions
function formatDate($dateString) {
    if (empty($dateString) || $dateString == '0000-00-00') return '';
    return date('Y-m-d', strtotime($dateString));
}

// Default values dengan data dummy untuk testing
$defaultTanggal = date('Y-m-d');

// Data dummy untuk testing
$dummyData = [
    'project_manual' => 'Instalasi Flow Meter TBBM Labuan Bajo',
    'invoice_manual' => 'INV/2024/VII/001',
    'penerima_perusahaan' => 'PT. Sholdan Radi Energi',
    'penerima_up' => 'Dede Budiana',
    'penerima_nama' => 'Dede Budiana',
    'penerima_telepon' => '085880881956',
    'alamat_pengiriman' => 'TBBM Labuan Bajo, No.12 Lancang Gang Golo Wae Kelambu Kec. Komodo Kab. Manggarai Barat, Nusa Tenggara Timur',
    'lokasi_proyek' => 'TBBM Labuan Bajo, Manggarai Barat, NTT',
    'sopir' => 'Abimanyu Pramudya Sakti',
    'no_kendaraan' => 'B 1234 CD',
    'dikirim_oleh' => 'Abimanyu Pramudya Sakti',
    'dikirim_telepon' => '081217442644',
    'catatan_barang' => "Rangkalan Skid berikut metering system terdiri dari:

1 SKID 1 (Product Pertamite dan Pertadex)
- Gate Valve
- Basket Strainer With Air Eliminator
- TCS Flow Meter 700-40\" SPA
- Check Valve
- Digital Preset Valve/Control Valve

2 SKID 2 (Product Pertamax dan Multi product)
- Gate Valve
- Basket Strainer With Air Eliminator
- TCS Flow Meter 700-40\" SPA
- Check Valve
- Digital Preset Valve/Control Valve",
    'keterangan' => 'Barang dikirim dalam kondisi baik dan lengkap. Harap dicek kembali oleh penerima.'
];

// ========== TAMBAHKAN KODE INI ==========
// Ambil data karyawan dari controller jika ada
$karyawanData = $karyawan ?? null;
if (isset($karyawanData) && is_object($karyawanData)) {
    // Jika berupa object, konversi ke array
    $karyawanData = method_exists($karyawanData, 'toArray') ? $karyawanData->toArray() : (array)$karyawanData;
}

// Tambahkan data disiapkan_oleh dari karyawan ke dummyData
if ($karyawanData && is_array($karyawanData) && !empty($karyawanData['nama_lengkap'])) {
    $dummyData['disiapkan_oleh'] = $karyawanData['nama_lengkap'] ?? 'Dwi Sales Pratama';
    $dummyData['disiapkan_telepon'] = $karyawanData['telepon'] ?? '081234567891';
    $dummyData['disiapkan_jabatan'] = $karyawanData['jabatan'] ?? 'Sales Executive';
} else {
    // Data fallback jika karyawan tidak ditemukan
    $dummyData['disiapkan_oleh'] = 'Dwi Sales Pratama';
    $dummyData['disiapkan_telepon'] = '081234567891';
    $dummyData['disiapkan_jabatan'] = 'Sales Executive';
}
// ========== AKHIR TAMBAHAN ==========

// Gunakan old data jika ada, jika tidak gunakan dummy data
function getFieldValue($fieldName) {
    $oldValue = old($fieldName);
    if (!empty($oldValue)) {
        return $oldValue;
    }
    
    global $dummyData;
    return $dummyData[$fieldName] ?? '';
}
?>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <h1 class="display-5 fw-bold text-primary mb-3">
                    <i class="fas fa-truck me-3"></i>
                    <?= $title ?>
                </h1>
                <p class="lead text-muted">
                    <?= $subtitle ?? 'Buat surat jalan pengiriman baru' ?>
                    <small class="d-block mt-2 text-info">
                        <i class="fas fa-vial me-1"></i>Mode Testing: Data dummy telah dimasukkan untuk uji coba
                    </small>
                </p>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Testing Info -->
    <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-vial me-2"></i>
        <strong>Mode Testing:</strong> Form ini telah diisi dengan data dummy untuk uji coba. 
        Anda bisa mengubah data sesuai kebutuhan atau menggunakan tombol "Preview" untuk melihat hasil.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <!-- Main Form -->
    <div class="row">
        <div class="col-12">
            <form action="<?= base_url('sales/surat-jalan/store') ?>" method="POST" id="suratJalanForm">
                <?= csrf_field() ?>
                
                <!-- ========== TAMBAHKAN HIDDEN FIELDS INI ========== -->
                <input type="hidden" name="disiapkan_oleh" value="<?= $dummyData['disiapkan_oleh'] ?>">
                <input type="hidden" name="disiapkan_telepon" value="<?= $dummyData['disiapkan_telepon'] ?>">
                <input type="hidden" name="disiapkan_jabatan" value="<?= $dummyData['disiapkan_jabatan'] ?>">
                <!-- ========== AKHIR HIDDEN FIELDS ========== -->
                
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Informasi Surat Jalan
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="fillDummyData()">
                            <i class="fas fa-magic me-1"></i> Isi Data Dummy
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Row 1: Nomor Surat Jalan & Tanggal -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nomor_surat_jalan" class="form-label fw-bold">
                                        <i class="fas fa-hashtag me-2"></i>Nomor Surat Jalan *
                                    </label>
                                    <input type="text" 
                                           class="form-control <?= ($validation->hasError('nomor_surat_jalan')) ? 'is-invalid' : '' ?>" 
                                           id="nomor_surat_jalan" 
                                           name="nomor_surat_jalan" 
                                           value="<?= old('nomor_surat_jalan', $nomorSuratJalan ?? '') ?>" 
                                           required>
                                    <?php if ($validation->hasError('nomor_surat_jalan')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('nomor_surat_jalan') ?>
                                        </div>
                                    <?php endif; ?>
                                    <small class="form-text text-muted">
                                        Format: XXX/DN-CDW/BULAN/TAHUN (contoh: 001/DN-CDW/VII/24)
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_kirim" class="form-label fw-bold">
                                        <i class="fas fa-calendar-alt me-2"></i>Tanggal Kirim *
                                    </label>
                                    <input type="date" 
                                           class="form-control <?= ($validation->hasError('tanggal_kirim')) ? 'is-invalid' : '' ?>" 
                                           id="tanggal_kirim" 
                                           name="tanggal_kirim" 
                                           value="<?= old('tanggal_kirim', $defaultTanggal) ?>" 
                                           required>
                                    <?php if ($validation->hasError('tanggal_kirim')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('tanggal_kirim') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Row 2: Project & Invoice (Manual Input) -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="project_manual" class="form-label fw-bold">
                                        <i class="fas fa-project-diagram me-2"></i>Nama Project *
                                    </label>
                                    <input type="text" 
                                           class="form-control <?= ($validation->hasError('project_manual')) ? 'is-invalid' : '' ?>" 
                                           id="project_manual" 
                                           name="project_manual" 
                                           value="<?= getFieldValue('project_manual') ?>" 
                                           placeholder="Masukkan nama project" 
                                           required>
                                    <?php if ($validation->hasError('project_manual')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('project_manual') ?>
                                        </div>
                                    <?php endif; ?>
                                    <small class="form-text text-muted">
                                        Masukkan nama project secara manual
                                    </small>
                                    <!-- Hidden field untuk project_id (optional) -->
                                    <input type="hidden" id="project_id" name="project_id" value="">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="invoice_manual" class="form-label fw-bold">
                                        <i class="fas fa-file-invoice me-2"></i>Nomor Invoice (Opsional)
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="invoice_manual" 
                                           name="invoice_manual" 
                                           value="<?= getFieldValue('invoice_manual') ?>"
                                           placeholder="Masukkan nomor invoice">
                                    <small class="form-text text-muted">
                                        Kosongkan jika tidak ada invoice
                                    </small>
                                    <!-- Hidden field untuk invoice_id (optional) -->
                                    <input type="hidden" id="invoice_id" name="invoice_id" value="">
                                </div>
                            </div>
                        </div>

                        <!-- Separator -->
                        <hr class="my-4">

                        <!-- Informasi Sales/Disiapkan Oleh -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="fw-bold text-primary mb-3">
                                    <i class="fas fa-user-tie me-2"></i>
                                    Informasi Disiapkan Oleh
                                </h5>
                                
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <h6 class="fw-bold">Nama Sales</h6>
                                                <p class="mb-1" id="displayDisiapkanOleh"><?= htmlspecialchars($dummyData['disiapkan_oleh']) ?></p>
                                                <small class="text-muted">(Diisi otomatis dari data karyawan)</small>
                                            </div>
                                            <div class="col-md-4">
                                                <h6 class="fw-bold">Telepon</h6>
                                                <p class="mb-1" id="displayDisiapkanTelepon"><?= htmlspecialchars($dummyData['disiapkan_telepon']) ?></p>
                                            </div>
                                            <div class="col-md-4">
                                                <h6 class="fw-bold">Jabatan</h6>
                                                <p class="mb-1" id="displayDisiapkanJabatan"><?= htmlspecialchars($dummyData['disiapkan_jabatan']) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pengirim Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="fw-bold text-primary mb-3">
                                    <i class="fas fa-building me-2"></i>
                                    Data Perusahaan Pengirim
                                </h5>
                                
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <h6 class="fw-bold">Nama Perusahaan</h6>
                                                <p class="mb-1"><?= htmlspecialchars($perusahaanPengirim['nama_perusahaan'] ?? 'PT. Cipta Duta Wacana') ?></p>
                                            </div>
                                            <div class="col-md-4">
                                                <h6 class="fw-bold">Alamat</h6>
                                                <p class="mb-1"><?= htmlspecialchars($perusahaanPengirim['alamat'] ?? 'Villa Bintaro Regency, Jl. Riau Blok K1 No. 2, Pondok Kacang Timur, Tangerang Selatan 15226') ?></p>
                                            </div>
                                            <div class="col-md-4">
                                                <h6 class="fw-bold">Website</h6>
                                                <p class="mb-1"><?= htmlspecialchars($perusahaanPengirim['website'] ?? 'www.cdw-engineering.com') ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Penerima Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="fw-bold text-primary mb-3">
                                    <i class="fas fa-user-friends me-2"></i>
                                    Data Penerima *
                                </h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="penerima_perusahaan" class="form-label fw-bold">
                                        Nama Perusahaan Penerima *
                                    </label>
                                    <input type="text" 
                                           class="form-control <?= ($validation->hasError('penerima_perusahaan')) ? 'is-invalid' : '' ?>" 
                                           id="penerima_perusahaan" 
                                           name="penerima_perusahaan" 
                                           value="<?= getFieldValue('penerima_perusahaan') ?>" 
                                           required>
                                    <?php if ($validation->hasError('penerima_perusahaan')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('penerima_perusahaan') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="penerima_up" class="form-label fw-bold">
                                        UP (Penanggung Jawab) *
                                    </label>
                                    <input type="text" 
                                           class="form-control <?= ($validation->hasError('penerima_up')) ? 'is-invalid' : '' ?>" 
                                           id="penerima_up" 
                                           name="penerima_up" 
                                           value="<?= getFieldValue('penerima_up') ?>" 
                                           required>
                                    <?php if ($validation->hasError('penerima_up')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('penerima_up') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="penerima_nama" class="form-label fw-bold">
                                        Nama Penerima
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="penerima_nama" 
                                           name="penerima_nama" 
                                           value="<?= getFieldValue('penerima_nama') ?>"
                                           placeholder="Kosongkan jika sama dengan UP">
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="penerima_telepon" class="form-label fw-bold">
                                        Telepon Penerima
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="penerima_telepon" 
                                           name="penerima_telepon" 
                                           value="<?= getFieldValue('penerima_telepon') ?>"
                                           placeholder="Contoh: 08123456789">
                                </div>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <div class="form-group">
                                    <label for="alamat_pengiriman" class="form-label fw-bold">
                                        Alamat Pengiriman *
                                    </label>
                                    <textarea class="form-control <?= ($validation->hasError('alamat_pengiriman')) ? 'is-invalid' : '' ?>" 
                                              id="alamat_pengiriman" 
                                              name="alamat_pengiriman" 
                                              rows="3" 
                                              required
                                              placeholder="Masukkan alamat lengkap pengiriman"><?= getFieldValue('alamat_pengiriman') ?></textarea>
                                    <?php if ($validation->hasError('alamat_pengiriman')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('alamat_pengiriman') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <div class="form-group">
                                    <label for="lokasi_proyek" class="form-label fw-bold">
                                        Lokasi Proyek (Opsional)
                                    </label>
                                    <textarea class="form-control" 
                                              id="lokasi_proyek" 
                                              name="lokasi_proyek" 
                                              rows="2"
                                              placeholder="Jika berbeda dengan alamat pengiriman"><?= getFieldValue('lokasi_proyek') ?></textarea>
                                    <small class="form-text text-muted">
                                        Jika berbeda dengan alamat pengiriman
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Pengiriman Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="fw-bold text-primary mb-3">
                                    <i class="fas fa-truck me-2"></i>
                                    Data Pengiriman
                                </h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="sopir" class="form-label fw-bold">
                                        Nama Sopir
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="sopir" 
                                           name="sopir" 
                                           value="<?= getFieldValue('sopir') ?>"
                                           placeholder="Nama sopir pengiriman">
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="no_kendaraan" class="form-label fw-bold">
                                        No. Kendaraan
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="no_kendaraan" 
                                           name="no_kendaraan" 
                                           value="<?= getFieldValue('no_kendaraan') ?>"
                                           placeholder="Contoh: B 1234 ABC">
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="dikirim_oleh" class="form-label fw-bold">
                                        Dikirim Oleh
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="dikirim_oleh" 
                                           name="dikirim_oleh" 
                                           value="<?= getFieldValue('dikirim_oleh') ?>"
                                           placeholder="Nama yang mengirim">
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="dikirim_telepon" class="form-label fw-bold">
                                        Telepon Pengirim
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="dikirim_telepon" 
                                           name="dikirim_telepon" 
                                           value="<?= getFieldValue('dikirim_telepon') ?>"
                                           placeholder="Telepon pengirim">
                                </div>
                            </div>
                        </div>

                        <!-- Barang Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="fw-bold text-primary mb-3">
                                    <i class="fas fa-boxes me-2"></i>
                                    Daftar Barang
                                </h5>
                                
                                <!-- Catatan Barang (Naratif) -->
                                <div class="mb-4">
                                    <div class="form-group">
                                        <label for="catatan_barang" class="form-label fw-bold">
                                            Catatan Barang (Deskripsi Naratif)
                                        </label>
                                        <textarea class="form-control" 
                                                  id="catatan_barang" 
                                                  name="catatan_barang" 
                                                  rows="4"
                                                  placeholder="Contoh: 
Rangkalan Skid berikut metering system terdiri dari:
1 SKID 1 (Product Pertamite dan Pertadex)
- Gate Valve
- Basket Strainer With Air Eliminator
- TCS Flow Meter 700-40&quot; SPA
- Check Valve
- Digital Preset Valve/Control Valve

2 SKID 2 (Product Pertamax dan Multi product)
- Gate Valve
- Basket Strainer With Air Eliminator
- TCS Flow Meter 700-40&quot; SPA
- Check Valve
- Digital Preset Valve/Control Valve"><?= getFieldValue('catatan_barang') ?></textarea>
                                        <small class="form-text text-muted">
                                            Deskripsi barang secara naratif (seperti pada contoh surat jalan)
                                        </small>
                                    </div>
                                </div>
                                
                               <!-- Tabel Item Barang -->
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Daftar Item Barang (Tabel)</h6>
        <button type="button" class="btn btn-sm btn-outline-info" onclick="fillDummyItems()">
            <i class="fas fa-magic me-1"></i> Isi Item Dummy
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="itemsTable">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="30%">Nama Barang *</th>
                        <th width="10%">Qty *</th>
                        <th width="10%">Satuan</th>
                        <th width="10%">Berat (kg)</th>
                        <th width="25%">Keterangan</th> <!-- TAMBAH KOLOM INI -->
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody id="itemsBody">
                    <!-- Default row sudah ada di bawah -->
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7"> <!-- UBAH COLSPAN DARI 6 MENJADI 7 -->
                            <button type="button" id="addItemBtn" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-2"></i>Tambah Item
                            </button>
                            <small class="text-muted ms-3">
                                Minimal 1 item harus diisi
                            </small>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
                            </div>
                        </div>

                        <!-- Keterangan Lainnya -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="fw-bold text-primary mb-3">
                                    <i class="fas fa-sticky-note me-2"></i>
                                    Keterangan Lainnya
                                </h5>
                                <div class="form-group">
                                    <label for="keterangan" class="form-label fw-bold">
                                        Keterangan Tambahan
                                    </label>
                                    <textarea class="form-control" 
                                              id="keterangan" 
                                              name="keterangan" 
                                              rows="3"
                                              placeholder="Keterangan tambahan tentang pengiriman"><?= getFieldValue('keterangan') ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-light py-3">
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('sales/surat-jalan') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <div>
                                <button type="button" class="btn btn-warning" onclick="fillAllDummyData()">
                                    <i class="fas fa-vial me-2"></i>Isi Semua Dummy
                                </button>
                                <button type="button" class="btn btn-success" onclick="previewSuratJalan()">
                                    <i class="fas fa-eye me-2"></i>Preview
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan Surat Jalan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Preview -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">
                    <i class="fas fa-eye me-2"></i>Preview Surat Jalan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="previewContent" class="p-3">
                    <!-- Preview akan diisi via JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('suratJalanForm').submit()">
                    <i class="fas fa-save me-2"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- CSS -->
<style>
    /* Tambahkan di bagian CSS */
.items-table th:nth-child(6), 
.items-table td:nth-child(6) {
    width: 25%;
}

.item-keterangan {
    width: 100%;
    min-width: 120px;
}

.item-row td {
    vertical-align: middle !important;
}

.form-label {
    font-weight: 600;
    color: #495057;
}

.card {
    border-radius: 10px;
    overflow: hidden;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}

.required-field::after {
    content: " *";
    color: #dc3545;
}

.testing-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 1000;
}

/* Tambahan untuk display info sales */
#displayDisiapkanOleh, #displayDisiapkanTelepon, #displayDisiapkanJabatan {
    font-weight: 500;
    color: #2c3e50;
}
</style>

<!-- JavaScript dengan data dummy -->
<script>
// Data dummy untuk testing
const dummyData = {
    nomor_surat_jalan: '<?= $nomorSuratJalan ?? "001/DN-CDW/VII/24" ?>',
    tanggal_kirim: '<?= $defaultTanggal ?>',
    project_manual: 'Instalasi Flow Meter TBBM Labuan Bajo',
    invoice_manual: 'INV/2024/VII/001',
    penerima_perusahaan: 'PT. Sholdan Radi Energi',
    penerima_up: 'Dede Budiana',
    penerima_nama: 'Dede Budiana',
    penerima_telepon: '085880881956',
    alamat_pengiriman: 'TBBM Labuan Bajo, No.12 Lancang Gang Golo Wae Kelambu Kec. Komodo Kab. Manggarai Barat, Nusa Tenggara Timur',
    lokasi_proyek: 'TBBM Labuan Bajo, Manggarai Barat, NTT',
    sopir: 'Abimanyu Pramudya Sakti',
    no_kendaraan: 'B 1234 CD',
    dikirim_oleh: 'Abimanyu Pramudya Sakti',
    dikirim_telepon: '081217442644',
    catatan_barang: `Rangkalan Skid berikut metering system terdiri dari:

1 SKID 1 (Product Pertamite dan Pertadex)
- Gate Valve
- Basket Strainer With Air Eliminator
- TCS Flow Meter 700-40" SPA
- Check Valve
- Digital Preset Valve/Control Valve

2 SKID 2 (Product Pertamax dan Multi product)
- Gate Valve
- Basket Strainer With Air Eliminator
- TCS Flow Meter 700-40" SPA
- Check Valve
- Digital Preset Valve/Control Valve`,
    keterangan: 'Barang dikirim dalam kondisi baik dan lengkap. Harap dicek kembali oleh penerima.',
    // ========== TAMBAHKAN DATA DISIAPKAN ==========
    disiapkan_oleh: '<?= $dummyData['disiapkan_oleh'] ?>',
    disiapkan_telepon: '<?= $dummyData['disiapkan_telepon'] ?>',
    disiapkan_jabatan: '<?= $dummyData['disiapkan_jabatan'] ?>'
    // ========== AKHIR TAMBAHAN ==========
};

// Dummy items untuk tabel barang
const dummyItems = [
    { nama: 'Gate Valve', qty: 2, satuan: 'unit', berat: 15.5, keterangan: 'Sudah di rakit' },
    { nama: 'Basket Strainer With Air Eliminator', qty: 2, satuan: 'unit', berat: 8.2, keterangan: 'Sudah di rakit' },
    { nama: 'TCS Flow Meter 700-40" SPA', qty: 2, satuan: 'unit', berat: 25.0, keterangan: 'Sudah di rakit' },
    { nama: 'Check Valve', qty: 2, satuan: 'unit', berat: 6.8, keterangan: 'Sudah di rakit' },
    { nama: 'Digital Preset Valve/Control Valve', qty: 2, satuan: 'unit', berat: 12.3, keterangan: 'Sudah di rakit' }
];
// Variabel global untuk tracking item index
let currentItemIndex = 0;

document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi dengan data dummy items saat halaman dimuat
    initDummyItems();
    
    // Event listener untuk tombol tambah item
    document.getElementById('addItemBtn').addEventListener('click', function() {
        addItemRow();
    });
    
    // Event delegation untuk tombol hapus item
    document.getElementById('itemsBody').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
            const button = e.target.closest('.remove-item');
            const row = button.closest('.item-row');
            row.remove();
            renumberItems();
        }
    });
    
    // Form validation saat submit
    document.getElementById('suratJalanForm').addEventListener('submit', function(e) {
        const requiredFields = this.querySelectorAll('[required]');
        let isValid = true;
        
        // Validasi semua field required
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        // Validasi minimal satu item
        const itemRows = document.querySelectorAll('.item-row');
        if (itemRows.length === 0) {
            alert('Minimal satu item barang harus diisi');
            isValid = false;
        }
        
        // Validasi setiap item harus punya nama barang
        itemRows.forEach(row => {
            const namaBarang = row.querySelector('.item-nama');
            if (!namaBarang.value.trim()) {
                namaBarang.classList.add('is-invalid');
                isValid = false;
            } else {
                namaBarang.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Mohon lengkapi semua field yang wajib diisi (ditandai dengan *)');
        }
    });
    
    // Auto-hide alerts setelah 5 detik
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});

// ========== FUNGSI UNTUK ITEMS ==========

// Fungsi untuk inisialisasi item dummy
function initDummyItems() {
    const tableBody = document.getElementById('itemsBody');
    
    // Clear existing items
    tableBody.innerHTML = '';
    currentItemIndex = 0;
    
    // Add dummy items
    dummyItems.forEach((item) => {
        addItemRow(item);
    });
}

// Fungsi untuk menambahkan row item dengan dropdown keterangan
function addItemRow(itemData = null) {
    const tableBody = document.getElementById('itemsBody');
    const newRow = document.createElement('tr');
    newRow.className = 'item-row';
    newRow.setAttribute('data-index', currentItemIndex);
    
    const itemName = itemData ? itemData.nama : '';
    const itemQty = itemData ? itemData.qty : 1;
    const itemSatuan = itemData ? itemData.satuan : 'unit';
    const itemBerat = itemData ? itemData.berat : '';
    const itemKeterangan = itemData ? itemData.keterangan : 'Sudah di rakit';
    
    newRow.innerHTML = `
        <td>${currentItemIndex + 1}</td>
        <td>
            <input type="text" 
                   class="form-control item-nama" 
                   name="items[${currentItemIndex}][nama_barang]" 
                   placeholder="Nama barang"
                   value="${itemName}"
                   required>
        </td>
        <td>
            <input type="number" 
                   class="form-control item-qty" 
                   name="items[${currentItemIndex}][qty]" 
                   value="${itemQty}" 
                   min="0.01" 
                   step="0.01" 
                   required>
        </td>
        <td>
            <select class="form-control item-satuan" name="items[${currentItemIndex}][satuan]">
                <option value="unit" ${itemSatuan === 'unit' ? 'selected' : ''}>Unit</option>
                <option value="pcs" ${itemSatuan === 'pcs' ? 'selected' : ''}>Pcs</option>
                <option value="set" ${itemSatuan === 'set' ? 'selected' : ''}>Set</option>
                <option value="pack" ${itemSatuan === 'pack' ? 'selected' : ''}>Pack</option>
                <option value="meter" ${itemSatuan === 'meter' ? 'selected' : ''}>Meter</option>
                <option value="liter" ${itemSatuan === 'liter' ? 'selected' : ''}>Liter</option>
                <option value="kg" ${itemSatuan === 'kg' ? 'selected' : ''}>Kg</option>
                <option value="buah" ${itemSatuan === 'buah' ? 'selected' : ''}>Buah</option>
                <option value="lembar" ${itemSatuan === 'lembar' ? 'selected' : ''}>Lembar</option>
                <option value="roll" ${itemSatuan === 'roll' ? 'selected' : ''}>Roll</option>
            </select>
        </td>
        <td>
            <input type="number" 
                   class="form-control item-berat" 
                   name="items[${currentItemIndex}][berat]" 
                   value="${itemBerat}"
                   min="0" 
                   step="0.01"
                   placeholder="0.00">
        </td>
        <td>
            <select class="form-control item-keterangan" name="items[${currentItemIndex}][keterangan]">
                <option value="Sudah di rakit" ${itemKeterangan === 'Sudah di rakit' ? 'selected' : ''}>Sudah di rakit</option>
                <option value="Belum di rakit" ${itemKeterangan === 'Belum di rakit' ? 'selected' : ''}>Belum di rakit</option>
                <option value="Perlu perakitan" ${itemKeterangan === 'Perlu perakitan' ? 'selected' : ''}>Perlu perakitan</option>
                <option value="Siap kirim" ${itemKeterangan === 'Siap kirim' ? 'selected' : ''}>Siap kirim</option>
                <option value="Dalam pengemasan" ${itemKeterangan === 'Dalam pengemasan' ? 'selected' : ''}>Dalam pengemasan</option>
                <option value="Terkemas rapi" ${itemKeterangan === 'Terkemas rapi' ? 'selected' : ''}>Terkemas rapi</option>
                <option value="Dalam pemeriksaan" ${itemKeterangan === 'Dalam pemeriksaan' ? 'selected' : ''}>Dalam pemeriksaan</option>
            </select>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger remove-item">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    
    tableBody.appendChild(newRow);
    currentItemIndex++;
}

// Fungsi untuk renumber items setelah penghapusan
function renumberItems() {
    const rows = document.querySelectorAll('.item-row');
    rows.forEach((row, index) => {
        row.setAttribute('data-index', index);
        row.cells[0].textContent = index + 1;
        
        // Update input names
        const inputs = row.querySelectorAll('input, select');
        inputs.forEach(input => {
            if (input.name.includes('items[')) {
                const name = input.name.replace(/items\[\d+\]/, `items[${index}]`);
                input.name = name;
            }
        });
    });
    
    currentItemIndex = rows.length;
}

// ========== FUNGSI UNTUK DATA DUMMY ==========

// Fungsi untuk mengisi data dummy
function fillDummyData() {
    document.getElementById('project_manual').value = dummyData.project_manual;
    document.getElementById('invoice_manual').value = dummyData.invoice_manual;
    document.getElementById('penerima_perusahaan').value = dummyData.penerima_perusahaan;
    document.getElementById('penerima_up').value = dummyData.penerima_up;
    document.getElementById('penerima_nama').value = dummyData.penerima_nama;
    document.getElementById('penerima_telepon').value = dummyData.penerima_telepon;
    document.getElementById('alamat_pengiriman').value = dummyData.alamat_pengiriman;
    document.getElementById('lokasi_proyek').value = dummyData.lokasi_proyek;
    document.getElementById('sopir').value = dummyData.sopir;
    document.getElementById('no_kendaraan').value = dummyData.no_kendaraan;
    document.getElementById('dikirim_oleh').value = dummyData.dikirim_oleh;
    document.getElementById('dikirim_telepon').value = dummyData.dikirim_telepon;
    document.getElementById('catatan_barang').value = dummyData.catatan_barang;
    document.getElementById('keterangan').value = dummyData.keterangan;
    
    // ========== UPDATE HIDDEN FIELDS ==========
    document.querySelector('input[name="disiapkan_oleh"]').value = dummyData.disiapkan_oleh;
    document.querySelector('input[name="disiapkan_telepon"]').value = dummyData.disiapkan_telepon;
    document.querySelector('input[name="disiapkan_jabatan"]').value = dummyData.disiapkan_jabatan;
    
    // Update display
    document.getElementById('displayDisiapkanOleh').textContent = dummyData.disiapkan_oleh;
    document.getElementById('displayDisiapkanTelepon').textContent = dummyData.disiapkan_telepon;
    document.getElementById('displayDisiapkanJabatan').textContent = dummyData.disiapkan_jabatan;
    // ========== AKHIR UPDATE ==========
    
    alert('Data dummy telah diisi! Silakan klik "Preview" untuk melihat hasil.');
}

// Fungsi untuk mengisi item dummy
function fillDummyItems() {
    initDummyItems();
    alert('Item dummy telah ditambahkan!');
}

// Fungsi untuk mengisi semua data dummy
function fillAllDummyData() {
    fillDummyData();
    initDummyItems();
    alert('Semua data dummy telah diisi! Silakan klik "Preview" untuk melihat hasil.');
}

// ========== FUNGSI PREVIEW ==========

// Preview surat jalan
function previewSuratJalan() {
    // Collect form data
    const formData = new FormData(document.getElementById('suratJalanForm'));
    const data = Object.fromEntries(formData.entries());
    
    // Get items data
// Di bagian get items data:
const items = [];
document.querySelectorAll('.item-row').forEach(row => {
    const item = {
        nama_barang: row.querySelector('.item-nama').value,
        qty: row.querySelector('.item-qty').value,
        satuan: row.querySelector('.item-satuan').value,
        berat: row.querySelector('.item-berat').value || '0',
        keterangan: row.querySelector('.item-keterangan').value || 'Sudah di rakit' // TAMBAH INI
    };
    if (item.nama_barang) {
        items.push(item);
    }
});
    
    // Format tanggal
    const tanggalKirim = new Date(data.tanggal_kirim).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: '2-digit'
    });
    
    // Build preview HTML
    let previewHTML = `
        <div class="preview-container" style="font-family: Arial, sans-serif;">
            <div class="header mb-4">
                <h3 class="text-center fw-bold">PREVIEW SURAT JALAN</h3>
                <hr>
            </div>
            
            <!-- Pengirim -->
            <div class="row mb-3">
                <div class="col-6">
                    <h6 class="fw-bold">Pengirim:</h6>
                    <p class="mb-1">PT. Cipta Duta Wacana</p>
                    <p class="mb-1">Villa Bintaro Regency, Jl. Riau Blok K1 No. 2</p>
                    <p class="mb-1">Pondok Kacang Timur, Tangerang Selatan 15226</p>
                    <p class="mb-0">www.cdw-engineering.com</p>
                </div>
                <div class="col-6 text-end">
                    <p class="mb-1"><strong>No. Surat Jalan:</strong> ${data.nomor_surat_jalan}</p>
                    <p class="mb-0"><strong>Tanggal:</strong> ${tanggalKirim}</p>
                </div>
            </div>
            
            <!-- Disiapkan Oleh -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="fw-bold">Disiapkan oleh:</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>Nama:</strong> ${data.disiapkan_oleh || dummyData.disiapkan_oleh}</p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>Telepon:</strong> ${data.disiapkan_telepon || dummyData.disiapkan_telepon}</p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>Jabatan:</strong> ${data.disiapkan_jabatan || dummyData.disiapkan_jabatan}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Penerima -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Dikirimkan kepada:</h6>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Perusahaan:</strong> ${data.penerima_perusahaan}</p>
                    <p class="mb-1"><strong>UP:</strong> ${data.penerima_up}</p>
                    <p class="mb-1"><strong>Telepon:</strong> ${data.penerima_telepon || '-'}</p>
                    <p class="mb-0"><strong>Alamat:</strong> ${data.alamat_pengiriman}</p>
                    ${data.lokasi_proyek ? `<p class="mb-0 mt-2"><strong>Lokasi Proyek:</strong> ${data.lokasi_proyek}</p>` : ''}
                </div>
            </div>
            
            <!-- Project & Invoice -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>Project:</strong> ${data.project_manual || '-'}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Invoice:</strong> ${data.invoice_manual || '-'}</p>
                </div>
            </div>
            
            <!-- Catatan Barang -->
            ${data.catatan_barang ? `
            <div class="mb-3">
                <h6 class="fw-bold">Catatan Barang:</h6>
                <div style="white-space: pre-line;">${data.catatan_barang}</div>
            </div>
            ` : ''}
            
                 <!-- Tabel Barang -->
        ${items.length > 0 ? `
        <div class="table-responsive mb-3">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Barang</th>
                        <th width="10%">Qty</th>
                        <th width="10%">Satuan</th>
                        <th width="10%">Berat (kg)</th>
                        <th width="15%">Keterangan</th> <!-- TAMBAH KOLOM INI -->
                    </tr>
                </thead>
                <tbody>
                    ${items.map((item, index) => `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.nama_barang}</td>
                        <td>${item.qty}</td>
                        <td>${item.satuan}</td>
                        <td>${item.berat ? item.berat : '-'}</td>
                        <td>${item.keterangan || 'Sudah di rakit'}</td> <!-- TAMBAH INI -->
                    </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
            ` : ''}
            
            <!-- Pengiriman -->
            <div class="row mb-3">
                <div class="col-md-6">
                    ${data.sopir ? `<p class="mb-1"><strong>Sopir:</strong> ${data.sopir}</p>` : ''}
                    ${data.no_kendaraan ? `<p class="mb-0"><strong>No. Kendaraan:</strong> ${data.no_kendaraan}</p>` : ''}
                </div>
                <div class="col-md-6">
                    ${data.dikirim_oleh ? `<p class="mb-1"><strong>Dikirim Oleh:</strong> ${data.dikirim_oleh}</p>` : ''}
                    ${data.dikirim_telepon ? `<p class="mb-0"><strong>Telepon:</strong> ${data.dikirim_telepon}</p>` : ''}
                </div>
            </div>
            
            <!-- Keterangan -->
            ${data.keterangan ? `
            <div class="mb-3">
                <h6 class="fw-bold">Keterangan:</h6>
                <p>${data.keterangan}</p>
            </div>
            ` : ''}
            
            <!-- Tanda Tangan -->
            <div class="row mt-4">
                <div class="col-4 text-center">
                    <p>Disiapkan oleh,</p>
                    <p><strong>${data.disiapkan_oleh || dummyData.disiapkan_oleh}</strong></p>
                    <p>${data.disiapkan_jabatan || dummyData.disiapkan_jabatan}</p>
                    <p>${data.disiapkan_telepon || dummyData.disiapkan_telepon}</p>
                </div>
                <div class="col-4 text-center">
                    <p>Dikirim oleh,</p>
                    <p><strong>${data.dikirim_oleh || data.sopir || '___________________'}</strong></p>
                    <p>${data.dikirim_telepon || ''}</p>
                </div>
                <div class="col-4 text-center">
                    <p>Diterima oleh,</p>
                    <p><strong>___________________</strong></p>
                    <p>${data.penerima_up || ''}</p>
                    <p>${data.penerima_perusahaan || ''}</p>
                </div>
            </div>
        </div>
    `;
    
    // Show preview in modal
    document.getElementById('previewContent').innerHTML = previewHTML;
    const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
    previewModal.show();
}
</script>