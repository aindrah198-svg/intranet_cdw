<?php
$title = $title ?? 'Buat Surat Jalan dari Invoice';
$active = $active ?? 'surat_jalan';

// Helper untuk prefill data
$invoice = $invoice ?? [];
$invoiceItems = $invoiceItems ?? [];
$clientAddress = $clientAddress ?? '';
$prefill = session()->getFlashdata('form_data') ?? [];

// Status options
$statusOptions = [
    'draft' => 'Draft',
    'diproses' => 'Diproses',
    'dikirim' => 'Dikirim'
];

// Satuan options
$satuanOptions = [
    'unit' => 'Unit',
    'pcs' => 'Pcs',
    'set' => 'Set',
    'meter' => 'Meter',
    'roll' => 'Roll',
    'buah' => 'Buah',
    'pack' => 'Pack',
    'lot' => 'Lot',
    'box' => 'Box',
    'kg' => 'Kg',
    'liter' => 'Liter',
    'lainnya' => 'Lainnya'
];

// Satuan berat options
$satuanBeratOptions = [
    'kg' => 'Kg',
    'gram' => 'Gram',
    'ton' => 'Ton',
    'lbs' => 'Lbs',
    'ons' => 'Ons',
    'lainnya' => 'Lainnya'
];
?>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <h1 class="display-5 fw-bold text-primary mb-3">
                    <i class="fas fa-file-invoice me-3"></i>
                    <?= $title ?>
                </h1>
                <p class="lead text-muted">
                    <?= $subtitle ?? 'Buat Surat Jalan dari Invoice - Format Manual' ?>
                </p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Informasi Invoice:</strong> Surat jalan akan dibuat berdasarkan data invoice yang dipilih
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice Info Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-file-invoice-dollar me-2"></i>
                        Informasi Invoice
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <small class="text-muted">Nomor Invoice</small>
                            <p class="mb-0 fw-bold"><?= htmlspecialchars($invoice['nomor_invoice'] ?? '') ?></p>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Tanggal Invoice</small>
                            <p class="mb-0"><?= !empty($invoice['tanggal_invoice']) ? date('d/m/Y', strtotime($invoice['tanggal_invoice'])) : '' ?></p>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Jatuh Tempo</small>
                            <p class="mb-0"><?= !empty($invoice['tanggal_jatuh_tempo']) ? date('d/m/Y', strtotime($invoice['tanggal_jatuh_tempo'])) : '' ?></p>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Status Pembayaran</small>
                            <p class="mb-0">
                                <?php 
                                $statusColors = [
                                    'belum_bayar' => 'danger',
                                    'sebagian' => 'warning',
                                    'lunas' => 'success',
                                    'overdue' => 'dark'
                                ];
                                $statusText = [
                                    'belum_bayar' => 'Belum Bayar',
                                    'sebagian' => 'Sebagian',
                                    'lunas' => 'Lunas',
                                    'overdue' => 'Overdue'
                                ];
                                $status = $invoice['status_pembayaran'] ?? 'belum_bayar';
                                ?>
                                <span class="badge bg-<?= $statusColors[$status] ?? 'secondary' ?>">
                                    <?= $statusText[$status] ?? $status ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">Project</small>
                            <p class="mb-0 fw-bold"><?= htmlspecialchars($invoice['kode_project'] ?? '') ?> - <?= htmlspecialchars($invoice['nama_project'] ?? '') ?></p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Client</small>
                            <p class="mb-0 fw-bold"><?= htmlspecialchars($invoice['nama_perusahaan'] ?? '') ?></p>
                            <p class="mb-0 text-muted"><?= htmlspecialchars($invoice['nama_kontak'] ?? '') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Form Surat Jalan
                    </h5>
                </div>
                <div class="card-body">
                    <form id="suratJalanForm" action="<?= base_url('sales/surat-jalan/store') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <!-- Hidden fields untuk auto-fill -->
                        <input type="hidden" name="invoice_id" value="<?= $invoice['id'] ?? '' ?>">
                        <input type="hidden" name="project_id" value="<?= $invoice['project_id'] ?? '' ?>">
                        
                        <!-- Error/Success Messages -->
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger">
                                <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (session()->getFlashdata('errors')): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <div class="row">
                            <!-- Left Column - Informasi Surat Jalan -->
                            <div class="col-md-6">
                                <!-- Informasi Dasar -->
                                <div class="card mb-4">
                                    <div class="card-header bg-info text-white py-2">
                                        <h6 class="mb-0">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Informasi Surat Jalan
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Nomor Surat Jalan -->
                                        <div class="mb-3">
                                            <label for="nomor_surat_jalan" class="form-label">
                                                <strong>Nomor Surat Jalan</strong> <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="text" 
                                                       class="form-control <?= $validation->hasError('nomor_surat_jalan') ? 'is-invalid' : '' ?>" 
                                                       id="nomor_surat_jalan" 
                                                       name="nomor_surat_jalan" 
                                                       value="<?= old('nomor_surat_jalan', $nomorSJ ?? '') ?>" 
                                                       required
                                                       readonly>
                                                <button type="button" class="btn btn-outline-secondary" onclick="generateNomor()">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                            </div>
                                            <?php if ($validation->hasError('nomor_surat_jalan')): ?>
                                                <div class="invalid-feedback d-block">
                                                    <?= $validation->getError('nomor_surat_jalan') ?>
                                                </div>
                                            <?php endif; ?>
                                            <small class="text-muted">Format: XXX/DN-CDW/Bulan/Tahun (Auto-generate)</small>
                                        </div>
                                        
                                        <!-- Project (Auto-filled) -->
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <strong>Project</strong>
                                            </label>
                                            <input type="text" 
                                                   class="form-control bg-light" 
                                                   value="<?= htmlspecialchars($invoice['kode_project'] ?? '') ?> - <?= htmlspecialchars($invoice['nama_project'] ?? '') ?>" 
                                                   readonly>
                                            <small class="text-muted">Diambil dari invoice</small>
                                        </div>
                                        
                                        <!-- Invoice (Auto-filled) -->
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <strong>Invoice</strong>
                                            </label>
                                            <input type="text" 
                                                   class="form-control bg-light" 
                                                   value="<?= htmlspecialchars($invoice['nomor_invoice'] ?? '') ?>" 
                                                   readonly>
                                            <small class="text-muted">Invoice yang dipilih</small>
                                        </div>
                                        
                                        <!-- Tanggal Kirim -->
                                        <div class="mb-3">
                                            <label for="tanggal_kirim" class="form-label">
                                                <strong>Tanggal Kirim</strong> <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" 
                                                   class="form-control <?= $validation->hasError('tanggal_kirim') ? 'is-invalid' : '' ?>" 
                                                   id="tanggal_kirim" 
                                                   name="tanggal_kirim" 
                                                   value="<?= old('tanggal_kirim', date('Y-m-d')) ?>" 
                                                   required>
                                            <?php if ($validation->hasError('tanggal_kirim')): ?>
                                                <div class="invalid-feedback d-block">
                                                    <?= $validation->getError('tanggal_kirim') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Status -->
                                        <div class="mb-3">
                                            <label for="status" class="form-label">
                                                <strong>Status</strong> <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control <?= $validation->hasError('status') ? 'is-invalid' : '' ?>" 
                                                    id="status" 
                                                    name="status" 
                                                    required>
                                                <?php foreach ($statusOptions as $value => $label): ?>
                                                    <option value="<?= $value ?>" 
                                                        <?= old('status', $prefill['status'] ?? 'diproses') == $value ? 'selected' : '' ?>>
                                                        <?= $label ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <?php if ($validation->hasError('status')): ?>
                                                <div class="invalid-feedback d-block">
                                                    <?= $validation->getError('status') ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Informasi Pengiriman -->
                                <div class="card mb-4">
                                    <div class="card-header bg-warning text-dark py-2">
                                        <h6 class="mb-0">
                                            <i class="fas fa-truck me-2"></i>
                                            Informasi Pengiriman
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Sopir -->
                                        <div class="mb-3">
                                            <label for="sopir" class="form-label">
                                                <strong>Sopir</strong>
                                            </label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="sopir" 
                                                   name="sopir" 
                                                   value="<?= old('sopir', $prefill['sopir'] ?? '') ?>"
                                                   placeholder="Nama sopir">
                                        </div>
                                        
                                        <!-- Telepon Sopir -->
                                        <div class="mb-3">
                                            <label for="sopir_telepon" class="form-label">
                                                <strong>Telepon Sopir</strong>
                                            </label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="sopir_telepon" 
                                                   name="sopir_telepon" 
                                                   value="<?= old('sopir_telepon', $prefill['sopir_telepon'] ?? '') ?>"
                                                   placeholder="No. telepon sopir">
                                        </div>
                                        
                                        <!-- No. Kendaraan -->
                                        <div class="mb-3">
                                            <label for="no_kendaraan" class="form-label">
                                                <strong>No. Kendaraan</strong>
                                            </label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="no_kendaraan" 
                                                   name="no_kendaraan" 
                                                   value="<?= old('no_kendaraan', $prefill['no_kendaraan'] ?? '') ?>"
                                                   placeholder="Nomor polisi kendaraan">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Right Column - Informasi Penerima & Penandatanganan -->
                            <div class="col-md-6">
                                <!-- Informasi Penerima (Auto-filled dari Invoice) -->
                                <div class="card mb-4">
                                    <div class="card-header bg-success text-white py-2">
                                        <h6 class="mb-0">
                                            <i class="fas fa-user-tie me-2"></i>
                                            Informasi Penerima
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Nama Perusahaan Penerima -->
                                        <div class="mb-3">
                                            <label for="penerima_perusahaan" class="form-label">
                                                <strong>Perusahaan Penerima</strong> <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control <?= $validation->hasError('penerima_perusahaan') ? 'is-invalid' : '' ?>" 
                                                   id="penerima_perusahaan" 
                                                   name="penerima_perusahaan" 
                                                   value="<?= old('penerima_perusahaan', $prefill['penerima_perusahaan'] ?? $invoice['nama_perusahaan'] ?? '') ?>"
                                                   required
                                                   placeholder="Nama perusahaan penerima">
                                            <?php if ($validation->hasError('penerima_perusahaan')): ?>
                                                <div class="invalid-feedback d-block">
                                                    <?= $validation->getError('penerima_perusahaan') ?>
                                                </div>
                                            <?php endif; ?>
                                            <small class="text-muted">Diambil dari invoice client</small>
                                        </div>
                                        
                                        <!-- UP Penerima -->
                                        <div class="mb-3">
                                            <label for="penerima_up" class="form-label">
                                                <strong>UP Penerima</strong> <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control <?= $validation->hasError('penerima_up') ? 'is-invalid' : '' ?>" 
                                                   id="penerima_up" 
                                                   name="penerima_up" 
                                                   value="<?= old('penerima_up', $prefill['penerima_up'] ?? $invoice['nama_kontak'] ?? '') ?>"
                                                   required
                                                   placeholder="Nama penanggung jawab penerima">
                                            <?php if ($validation->hasError('penerima_up')): ?>
                                                <div class="invalid-feedback d-block">
                                                    <?= $validation->getError('penerima_up') ?>
                                                </div>
                                            <?php endif; ?>
                                            <small class="text-muted">Diambil dari kontak client</small>
                                        </div>
                                        
                                        <!-- Telepon Penerima -->
                                        <div class="mb-3">
                                            <label for="penerima_telepon" class="form-label">
                                                <strong>Telepon Penerima</strong>
                                            </label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="penerima_telepon" 
                                                   name="penerima_telepon" 
                                                   value="<?= old('penerima_telepon', $prefill['penerima_telepon'] ?? $invoice['telepon'] ?? '') ?>"
                                                   placeholder="No. telepon penerima">
                                            <small class="text-muted">Diambil dari telepon client</small>
                                        </div>
                                        
                                        <!-- Alamat Pengiriman -->
                                        <div class="mb-3">
                                            <label for="alamat_pengiriman" class="form-label">
                                                <strong>Alamat Pengiriman</strong> <span class="text-danger">*</span>
                                            </label>
                                            <textarea class="form-control <?= $validation->hasError('alamat_pengiriman') ? 'is-invalid' : '' ?>" 
                                                      id="alamat_pengiriman" 
                                                      name="alamat_pengiriman" 
                                                      rows="3" 
                                                      required
                                                      placeholder="Alamat lengkap pengiriman"><?= old('alamat_pengiriman', $prefill['alamat_pengiriman'] ?? $clientAddress) ?></textarea>
                                            <?php if ($validation->hasError('alamat_pengiriman')): ?>
                                                <div class="invalid-feedback d-block">
                                                    <?= $validation->getError('alamat_pengiriman') ?>
                                                </div>
                                            <?php endif; ?>
                                            <small class="text-muted">Diambil dari alamat client</small>
                                        </div>
                                        
                                        <!-- Lokasi Proyek -->
                                        <div class="mb-3">
                                            <label for="lokasi_proyek" class="form-label">
                                                <strong>Lokasi Proyek</strong> <small class="text-muted">(Opsional)</small>
                                            </label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="lokasi_proyek" 
                                                   name="lokasi_proyek" 
                                                   value="<?= old('lokasi_proyek', $prefill['lokasi_proyek'] ?? '') ?>"
                                                   placeholder="Lokasi spesifik proyek (jika ada)">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Penandatanganan -->
                                <div class="card mb-4">
                                    <div class="card-header bg-primary text-white py-2">
                                        <h6 class="mb-0">
                                            <i class="fas fa-signature me-2"></i>
                                            Penandatanganan
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Disiapkan Oleh -->
                                            <div class="col-md-6 mb-3">
                                                <label for="disiapkan_oleh" class="form-label">
                                                    <strong>Disiapkan Oleh</strong>
                                                </label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="disiapkan_oleh" 
                                                       name="disiapkan_oleh" 
                                                       value="<?= old('disiapkan_oleh', $prefill['disiapkan_oleh'] ?? $perusahaanCDW['nama_perusahaan'] ?? 'PT. Cipta Duta Wacana') ?>"
                                                       placeholder="Nama penyiap">
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label for="disiapkan_telepon" class="form-label">
                                                    <strong>Telepon</strong>
                                                </label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="disiapkan_telepon" 
                                                       name="disiapkan_telepon" 
                                                       value="<?= old('disiapkan_telepon', $prefill['disiapkan_telepon'] ?? '') ?>"
                                                       placeholder="No. telepon">
                                            </div>
                                            
                                            <div class="col-12 mb-3">
                                                <label for="disiapkan_jabatan" class="form-label">
                                                    <strong>Jabatan</strong>
                                                </label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="disiapkan_jabatan" 
                                                       name="disiapkan_jabatan" 
                                                       value="<?= old('disiapkan_jabatan', $prefill['disiapkan_jabatan'] ?? '') ?>"
                                                       placeholder="Jabatan">
                                            </div>
                                        </div>
                                        
                                        <hr>
                                        
                                        <div class="row">
                                            <!-- Dikirim Oleh -->
                                            <div class="col-md-6 mb-3">
                                                <label for="dikirim_oleh" class="form-label">
                                                    <strong>Dikirim Oleh</strong>
                                                </label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="dikirim_oleh" 
                                                       name="dikirim_oleh" 
                                                       value="<?= old('dikirim_oleh', $prefill['dikirim_oleh'] ?? '') ?>"
                                                       placeholder="Nama pengirim">
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label for="dikirim_telepon" class="form-label">
                                                    <strong>Telepon</strong>
                                                </label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="dikirim_telepon" 
                                                       name="dikirim_telepon" 
                                                       value="<?= old('dikirim_telepon', $prefill['dikirim_telepon'] ?? '') ?>"
                                                       placeholder="No. telepon">
                                            </div>
                                        </div>
                                        
                                        <hr>
                                        
                                        <div class="row">
                                            <!-- Diterima Oleh -->
                                            <div class="col-md-6 mb-3">
                                                <label for="diterima_oleh" class="form-label">
                                                    <strong>Diterima Oleh</strong>
                                                </label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="diterima_oleh" 
                                                       name="diterima_oleh" 
                                                       value="<?= old('diterima_oleh', $prefill['diterima_oleh'] ?? '') ?>"
                                                       placeholder="Nama penerima">
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label for="diterima_telepon" class="form-label">
                                                    <strong>Telepon</strong>
                                                </label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="diterima_telepon" 
                                                       name="diterima_telepon" 
                                                       value="<?= old('diterima_telepon', $prefill['diterima_telepon'] ?? '') ?>"
                                                       placeholder="No. telepon">
                                            </div>
                                            
                                            <div class="col-12 mb-3">
                                                <label for="diterima_perusahaan" class="form-label">
                                                    <strong>Perusahaan Penerima (Tanda Tangan)</strong>
                                                </label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="diterima_perusahaan" 
                                                       name="diterima_perusahaan" 
                                                       value="<?= old('diterima_perusahaan', $prefill['diterima_perusahaan'] ?? $invoice['nama_perusahaan'] ?? '') ?>"
                                                       placeholder="Nama perusahaan untuk tanda tangan">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Barang dari Invoice -->
                        <div class="card mb-4">
                            <div class="card-header bg-danger text-white py-2">
                                <h6 class="mb-0">
                                    <i class="fas fa-boxes me-2"></i>
                                    Barang dari Invoice
                                </h6>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($invoiceItems)): ?>
                                <div class="alert alert-success mb-3">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong><?= count($invoiceItems) ?> item</strong> ditemukan dari invoice
                                </div>
                                
                                <!-- Catatan Barang (Naratif) -->
                                <div class="mb-4">
                                    <label for="catatan_barang" class="form-label">
                                        <strong>Catatan Barang (Deskripsi Naratif)</strong> 
                                        <small class="text-muted">(Opsional - untuk format manual seperti contoh)</small>
                                    </label>
                                    <textarea class="form-control" 
                                              id="catatan_barang" 
                                              name="catatan_barang" 
                                              rows="3"
                                              placeholder="Contoh: Rangkalan Skid berikut metery system terdiri dari :&#10;1 SKID 1 (Product Pertainte dan Pertadex)&#10;- Gate Valve&#10;- Basket Strainer With Air Eleminator&#10;- TCS Flow Meter 700-40&quot; SPA&#10;- Check Valve&#10;- Digital Preset Valve/Control Valve"><?= old('catatan_barang', $prefill['catatan_barang'] ?? '') ?></textarea>
                                    <small class="text-muted">Gunakan format naratif seperti contoh surat jalan manual</small>
                                </div>
                                
                                <!-- Tabel Barang dari Invoice -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Detail Barang dari Invoice</h6>
                                        <button type="button" class="btn btn-sm btn-success" id="addCustomItemBtn">
                                            <i class="fas fa-plus me-1"></i> Tambah Barang Manual
                                        </button>
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="itemsTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="5%">No</th>
                                                    <th width="30%">Nama Barang</th>
                                                    <th width="10%">Qty</th>
                                                    <th width="10%">Satuan</th>
                                                    <th width="10%">Harga Satuan</th>
                                                    <th width="15%">Subtotal</th>
                                                    <th width="10%">Berat</th>
                                                    <th width="10%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="itemsBody">
                                                <!-- Load items dari invoice -->
                                                <?php $itemIndex = 1; ?>
                                                <?php foreach ($invoiceItems as $item): ?>
                                                <tr class="item-row" data-from-invoice="true">
                                                    <td class="align-middle">
                                                        <span class="item-number"><?= $itemIndex ?></span>
                                                        <input type="hidden" name="items[<?= $itemIndex ?>][no]" value="<?= $itemIndex ?>">
                                                    </td>
                                                    <td>
                                                        <input type="text" 
                                                               class="form-control form-control-sm" 
                                                               name="items[<?= $itemIndex ?>][nama_barang]" 
                                                               value="<?= htmlspecialchars($item['nama_item']) ?>" 
                                                               placeholder="Nama barang" 
                                                               required
                                                               readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" 
                                                               class="form-control form-control-sm qty-input" 
                                                               name="items[<?= $itemIndex ?>][qty]" 
                                                               value="<?= number_format($item['qty'], 0, ',', '.') ?>" 
                                                               placeholder="0" 
                                                               required 
                                                               onkeyup="formatNumber(this)">
                                                    </td>
                                                    <td>
                                                        <select class="form-control form-control-sm" name="items[<?= $itemIndex ?>][satuan]">
                                                            <?php foreach ($satuanOptions as $value => $label): ?>
                                                                <option value="<?= $value ?>" <?= ($item['satuan'] ?? 'unit') == $value ? 'selected' : '' ?>>
                                                                    <?= $label ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </td>
                                                    <td class="align-middle text-end">
                                                        Rp <?= number_format($item['harga_satuan'], 0, ',', '.') ?>
                                                    </td>
                                                    <td class="align-middle text-end fw-bold">
                                                        Rp <?= number_format($item['subtotal'], 0, ',', '.') ?>
                                                    </td>
                                                    <td>
                                                        <input type="text" 
                                                               class="form-control form-control-sm berat-input" 
                                                               name="items[<?= $itemIndex ?>][berat]" 
                                                               value="0"
                                                               placeholder="0"
                                                               onkeyup="formatNumber(this)">
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <button type="button" class="btn btn-sm btn-danger remove-item-btn" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php $itemIndex++; ?>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="alert alert-warning mt-3">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Perhatian:</strong> Data barang diambil dari invoice. Anda dapat mengedit qty dan menambahkan berat.
                                    </div>
                                </div>
                                <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                    <p class="text-warning mb-3">Tidak ada item pada invoice ini</p>
                                    <button type="button" class="btn btn-primary" id="addFirstItemBtn">
                                        <i class="fas fa-plus me-2"></i> Tambah Barang Manual
                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Keterangan Lainnya -->
                        <div class="card mb-4">
                            <div class="card-header bg-secondary text-white py-2">
                                <h6 class="mb-0">
                                    <i class="fas fa-sticky-note me-2"></i>
                                    Keterangan Lainnya
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="keterangan" class="form-label">
                                        <strong>Keterangan Tambahan</strong>
                                    </label>
                                    <textarea class="form-control" 
                                              id="keterangan" 
                                              name="keterangan" 
                                              rows="3"
                                              placeholder="Catatan tambahan tentang pengiriman"><?= old('keterangan', $prefill['keterangan'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="<?= base_url('sales/invoice/detail/' . ($invoice['id'] ?? '')) ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i> Kembali ke Invoice
                                </a>
                                <a href="<?= base_url('sales/surat-jalan/create') ?>" class="btn btn-outline-primary ms-2">
                                    <i class="fas fa-plus me-2"></i> Buat Manual
                                </a>
                            </div>
                            
                            <div>
                                <button type="submit" name="status" value="draft" class="btn btn-secondary">
                                    <i class="fas fa-save me-2"></i> Simpan sebagai Draft
                                </button>
                                <button type="submit" name="status" value="diproses" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i> Simpan & Proses
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Template untuk custom item row -->
<template id="customItemTemplate">
    <tr class="item-row custom-item">
        <td class="align-middle">
            <span class="item-number">1</span>
            <input type="hidden" name="items[{{index}}][no]" value="{{index}}">
        </td>
        <td>
            <input type="text" 
                   class="form-control form-control-sm" 
                   name="items[{{index}}][nama_barang]" 
                   placeholder="Nama barang" 
                   required>
        </td>
        <td>
            <input type="text" 
                   class="form-control form-control-sm qty-input" 
                   name="items[{{index}}][qty]" 
                   placeholder="0" 
                   required 
                   onkeyup="formatNumber(this)">
        </td>
        <td>
            <select class="form-control form-control-sm" name="items[{{index}}][satuan]">
                <?php foreach ($satuanOptions as $value => $label): ?>
                    <option value="<?= $value ?>"><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td class="align-middle text-end">
            -
        </td>
        <td class="align-middle text-end">
            -
        </td>
        <td>
            <input type="text" 
                   class="form-control form-control-sm berat-input" 
                   name="items[{{index}}][berat]" 
                   placeholder="0"
                   onkeyup="formatNumber(this)">
        </td>
        <td class="align-middle text-center">
            <button type="button" class="btn btn-sm btn-danger remove-item-btn" title="Hapus">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>

<style>
.card {
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 20px;
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
}

.form-label strong {
    font-weight: 600;
}

.table th {
    font-size: 0.85rem;
    background-color: #f8f9fa;
}

.item-row:hover {
    background-color: #f8f9fa;
}

.qty-input, .berat-input {
    text-align: right;
}

.custom-item {
    background-color: #f0f9ff;
}

.bg-light {
    background-color: #f8f9fa !important;
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Item counter - start from existing items count + 1
    let itemCounter = <?= count($invoiceItems) ?>;
    const itemsBody = document.getElementById('itemsBody');
    const customItemTemplate = document.getElementById('customItemTemplate');
    const addCustomItemBtn = document.getElementById('addCustomItemBtn');
    const addFirstItemBtn = document.getElementById('addFirstItemBtn');
    
    // Add custom item function
    function addCustomItemRow() {
        itemCounter++;
        const templateContent = customItemTemplate.innerHTML;
        const compiled = templateContent.replace(/{{index}}/g, itemCounter);
        
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = compiled;
        const newRow = tempDiv.firstElementChild;
        
        itemsBody.appendChild(newRow);
        updateItemNumbers();
    }
    
    // Update item numbers
    function updateItemNumbers() {
        const itemNumbers = itemsBody.querySelectorAll('.item-number');
        itemNumbers.forEach((span, index) => {
            span.textContent = index + 1;
            // Update hidden input value
            const hiddenInput = span.nextElementSibling;
            if (hiddenInput) {
                hiddenInput.name = `items[${index + 1}][no]`;
                hiddenInput.value = index + 1;
            }
        });
    }
    
    // Remove item
    itemsBody.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item-btn')) {
            const row = e.target.closest('.item-row');
            const isFromInvoice = row.getAttribute('data-from-invoice') === 'true';
            
            if (isFromInvoice) {
                if (!confirm('Item ini berasal dari invoice. Yakin ingin menghapus?')) {
                    return;
                }
            } else {
                if (!confirm('Yakin ingin menghapus barang ini?')) {
                    return;
                }
            }
            
            row.remove();
            updateItemNumbers();
        }
    });
    
    // Add custom item button
    if (addCustomItemBtn) {
        addCustomItemBtn.addEventListener('click', addCustomItemRow);
    }
    
    // Add first item button (jika tidak ada item dari invoice)
    if (addFirstItemBtn) {
        addFirstItemBtn.addEventListener('click', function() {
            addCustomItemRow();
            this.style.display = 'none';
        });
    }
    
    // Auto-fill features
    const penerimaPerusahaanInput = document.getElementById('penerima_perusahaan');
    const diterimaPerusahaanInput = document.getElementById('diterima_perusahaan');
    const sopirInput = document.getElementById('sopir');
    const dikirimOlehInput = document.getElementById('dikirim_oleh');
    const sopirTeleponInput = document.getElementById('sopir_telepon');
    const dikirimTeleponInput = document.getElementById('dikirim_telepon');
    const penerimaUpInput = document.getElementById('penerima_up');
    const diterimaOlehInput = document.getElementById('diterima_oleh');
    const penerimaTeleponInput = document.getElementById('penerima_telepon');
    const diterimaTeleponInput = document.getElementById('diterima_telepon');
    
    // Copy penerima_perusahaan to diterima_perusahaan if empty
    if (penerimaPerusahaanInput && diterimaPerusahaanInput && !diterimaPerusahaanInput.value) {
        penerimaPerusahaanInput.addEventListener('change', function() {
            if (!diterimaPerusahaanInput.value) {
                diterimaPerusahaanInput.value = this.value;
            }
        });
    }
    
    // Copy sopir to dikirim_oleh if empty
    if (sopirInput && dikirimOlehInput && !dikirimOlehInput.value) {
        sopirInput.addEventListener('change', function() {
            if (!dikirimOlehInput.value) {
                dikirimOlehInput.value = this.value;
            }
        });
    }
    
    // Copy sopir_telepon to dikirim_telepon if empty
    if (sopirTeleponInput && dikirimTeleponInput && !dikirimTeleponInput.value) {
        sopirTeleponInput.addEventListener('change', function() {
            if (!dikirimTeleponInput.value) {
                dikirimTeleponInput.value = this.value;
            }
        });
    }
    
    // Copy penerima_up to diterima_oleh if empty
    if (penerimaUpInput && diterimaOlehInput && !diterimaOlehInput.value) {
        penerimaUpInput.addEventListener('change', function() {
            if (!diterimaOlehInput.value) {
                diterimaOlehInput.value = this.value;
            }
        });
    }
    
    // Copy penerima_telepon to diterima_telepon if empty
    if (penerimaTeleponInput && diterimaTeleponInput && !diterimaTeleponInput.value) {
        penerimaTeleponInput.addEventListener('change', function() {
            if (!diterimaTeleponInput.value) {
                diterimaTeleponInput.value = this.value;
            }
        });
    }
    
    // Generate nomor surat jalan
    window.generateNomor = function() {
        fetch('<?= base_url("sales/surat-jalan") ?>/generate-nomor')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('nomor_surat_jalan').value = data.nomor;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal generate nomor surat jalan');
            });
    }
    
    // Format number with thousand separators
    window.formatNumber = function(input) {
        let value = input.value.replace(/[^\d,]/g, '');
        // Remove existing formatting
        value = value.replace(/\./g, '').replace(',', '.');
        if (value) {
            // Check if it's a number
            if (!isNaN(value)) {
                // Format with thousand separators
                value = parseFloat(value).toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2
                });
            }
        }
        input.value = value;
    }
    
    // Initialize number formatting on existing inputs
    document.querySelectorAll('.qty-input, .berat-input').forEach(input => {
        formatNumber(input);
    });
    
    // Form validation
    const form = document.getElementById('suratJalanForm');
    form.addEventListener('submit', function(e) {
        // Basic validation
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Harap lengkapi semua field yang wajib diisi!');
            return false;
        }
        
        // Check if there are items
        const itemRows = form.querySelectorAll('.item-row');
        if (itemRows.length === 0) {
            e.preventDefault();
            alert('Harap tambahkan minimal satu barang!');
            return false;
        }
        
        // Validate item quantities
        const qtyInputs = form.querySelectorAll('.qty-input');
        let hasInvalidQty = false;
        
        qtyInputs.forEach(input => {
            const value = input.value.replace(/[^\d]/g, '');
            if (!value || parseInt(value) <= 0) {
                input.classList.add('is-invalid');
                hasInvalidQty = true;
            } else {
                input.classList.remove('is-invalid');
            }
        });
        
        if (hasInvalidQty) {
            e.preventDefault();
            alert('Qty barang harus diisi dengan angka yang valid!');
            return false;
        }
        
        // Confirm before submitting
        if (!confirm('Buat surat jalan dari invoice ini?')) {
            e.preventDefault();
            return false;
        }
    });
    
    // Update item numbers on load
    updateItemNumbers();
});
</script>