<?php
// C:\xampp\htdocs\cdwnet\app\Views\admin\kontrak\show.php

// Fungsi helper untuk menangani string/array/null
// Fungsi helper untuk menangani string/array/null dengan aman
function safe_nl2br($value, $default = '-') {
    if (is_null($value) || $value === '') {
        return nl2br(esc($default));
    }
    
    if (is_array($value)) {
        // Jika array, convert ke string JSON
        $json = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        // Jika json_encode gagal, gunakan default
        if ($json === false) {
            return nl2br(esc('Data array tidak valid'));
        }
        return nl2br(esc($json));
    }
    
    // Pastikan value adalah string
    $stringValue = strval($value);
    return nl2br(esc($stringValue));
}

// Fungsi helper alternatif yang lebih sederhana (tanpa nl2br untuk JSON)
function safe_escape($value, $default = '-') {
    if (is_null($value) || $value === '') {
        return esc($default);
    }
    
    if (is_array($value)) {
        $json = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            return esc('Data array tidak valid');
        }
        return esc($json);
    }
    
    return esc(strval($value));
}

$title = 'Detail Kontrak: ' . esc($kontrak['nomor_kontrak'] ?? '');
$active = 'kontrak';
?>

<?= $this->include('admin/templates/header') ?>
<?= $this->include('admin/templates/sidebar') ?>
<?= $this->include('admin/templates/navbar') ?>

<!-- Main Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Kontrak Kerja</h1>
        <div>
            <a href="<?= base_url('admin/karyawan/kontrak'); ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm me-2">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
            </a>
            <a href="<?= base_url('admin/karyawan/kontrak/edit/' . ($kontrak['id'] ?? '')); ?>" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm me-2">
                <i class="fas fa-edit fa-sm text-white-50"></i> Edit
            </a>
            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#printModal">
                <i class="fas fa-print fa-sm text-white-50"></i> Cetak
            </a>
        </div>
    </div>

    <!-- Header Kontrak -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Kontrak</h6>
            <div>
                <span class="badge bg-<?= 
                    ($kontrak['status'] == 'Aktif') ? 'success' : 
                    (($kontrak['status'] == 'Draft') ? 'warning' : 
                    (($kontrak['status'] == 'Selesai') ? 'info' : 
                    (($kontrak['status'] == 'Diperpanjang') ? 'primary' : 
                    (($kontrak['status'] == 'Diputus') ? 'danger' : 'secondary')))) 
                ?> fs-6">
                    <?= $kontrak['status'] ?? 'Draft' ?>
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Nomor Kontrak</th>
                            <td width="5%">:</td>
                            <td><strong><?= esc($kontrak['nomor_kontrak'] ?? '-') ?></strong></td>
                        </tr>
                        <tr>
                            <th>Karyawan</th>
                            <td>:</td>
                            <td>
                                <strong><?= esc($kontrak['nama_lengkap'] ?? '-') ?></strong>
                                <br>
                                <small class="text-muted">NIK: <?= esc($kontrak['nik'] ?? '-') ?></small>
                            </td>
                        </tr>
                        <tr>
                            <th>Jenis Kontrak</th>
                            <td>:</td>
                            <td>
                                <span class="badge bg-<?= 
                                    ($kontrak['jenis_kontrak'] == 'Probation') ? 'warning' : 
                                    (($kontrak['jenis_kontrak'] == 'Kontrak') ? 'primary' : 
                                    (($kontrak['jenis_kontrak'] == 'Tetap') ? 'success' : 'info')) 
                                ?>">
                                    <?= $kontrak['jenis_kontrak'] ?? '-' ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Jabatan</th>
                            <td>:</td>
                            <td><?= esc($kontrak['jabatan'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>Lokasi Kerja</th>
                            <td>:</td>
                            <td><?= safe_nl2br($kontrak['lokasi_kerja'] ?? '-') ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Periode Kontrak</th>
                            <td width="5%">:</td>
                            <td>
                                <?= date('d/m/Y', strtotime($kontrak['tanggal_mulai'] ?? '')) ?>
                                <?php if($kontrak['tanggal_selesai']): ?>
                                    - <?= date('d/m/Y', strtotime($kontrak['tanggal_selesai'])) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Masa Kerja</th>
                            <td>:</td>
                            <td>
                                <?= $kontrak['masa_kerja_bulan'] ? $kontrak['masa_kerja_bulan'] . ' bulan' : '-' ?>
                                <?php if($kontrak['masa_percobaan_bulan']): ?>
                                    <br>
                                    <small class="text-muted">(Masa percobaan: <?= $kontrak['masa_percobaan_bulan'] ?> bulan)</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Gaji Pokok</th>
                            <td>:</td>
                            <td><strong>Rp <?= number_format($kontrak['gaji_pokok'] ?? 0, 0, ',', '.') ?></strong></td>
                        </tr>
                        <tr>
                            <th>Dibuat Tanggal</th>
                            <td>:</td>
                            <td><?= date('d/m/Y H:i', strtotime($kontrak['created_at'] ?? '')) ?></td>
                        </tr>
                        <tr>
                            <th>Terakhir Diupdate</th>
                            <td>:</td>
                            <td><?= date('d/m/Y H:i', strtotime($kontrak['updated_at'] ?? '')) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Kontrak dalam Tabs -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <ul class="nav nav-tabs card-header-tabs" id="kontrakTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tunjangan-tab" data-bs-toggle="tab" data-bs-target="#tunjangan" type="button" role="tab">
                        <i class="fas fa-money-bill-wave me-1"></i> Tunjangan & Fasilitas
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="cuti-tab" data-bs-toggle="tab" data-bs-target="#cuti" type="button" role="tab">
                        <i class="fas fa-calendar-alt me-1"></i> Hak Cuti
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="target-tab" data-bs-toggle="tab" data-bs-target="#target" type="button" role="tab">
                        <i class="fas fa-bullseye me-1"></i> Target & Komisi
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pihak-tab" data-bs-toggle="tab" data-bs-target="#pihak" type="button" role="tab">
                        <i class="fas fa-signature me-1"></i> Pihak Penandatangan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="lampiran-tab" data-bs-toggle="tab" data-bs-target="#lampiran" type="button" role="tab">
                        <i class="fas fa-paperclip me-1"></i> Lampiran & Status
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="kontrakTabContent">
                <!-- Tab Tunjangan & Fasilitas -->
                <div class="tab-pane fade show active" id="tunjangan" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3"><i class="fas fa-check-circle me-2"></i>Tunjangan yang Berlaku:</h6>
                            <ul class="list-group list-group-flush">
                                <?php if($kontrak['tunjangan_bpjs']): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-heartbeat text-success me-2"></i>Tunjangan BPJS</span>
                                    <span class="badge bg-success">Ya</span>
                                </li>
                                <?php endif; ?>
                                
                                <?php if($kontrak['tunjangan_makan_lokal'] > 0): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-utensils text-primary me-2"></i>Uang Makan (Lokal)</span>
                                    <span class="badge bg-primary">Rp <?= number_format($kontrak['tunjangan_makan_lokal'], 0, ',', '.') ?>/hari</span>
                                </li>
                                <?php endif; ?>
                                
                                <?php if($kontrak['tunjangan_makan_luar_jawa'] > 0): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-plane text-info me-2"></i>Uang Makan (Luar Jawa)</span>
                                    <span class="badge bg-info">Rp <?= number_format($kontrak['tunjangan_makan_luar_jawa'], 0, ',', '.') ?>/hari</span>
                                </li>
                                <?php endif; ?>
                                
                                <?php if($kontrak['reimburse_transport']): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-car text-warning me-2"></i>Reimburse Transport</span>
                                    <span class="badge bg-warning">Ya</span>
                                </li>
                                <?php endif; ?>
                                
                                <?php if($kontrak['reimburse_entertaint']): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-glass-cheers text-danger me-2"></i>Reimburse Entertaint</span>
                                    <span class="badge bg-danger">Ya</span>
                                </li>
                                <?php endif; ?>
                                
                                <?php if($kontrak['tunjangan_penginapan_max'] > 0): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-hotel text-secondary me-2"></i>Maksimal Penginapan</span>
                                    <span class="badge bg-secondary">Rp <?= number_format($kontrak['tunjangan_penginapan_max'], 0, ',', '.') ?>/hari</span>
                                </li>
                                <?php endif; ?>
                                
                                <?php if(!$kontrak['tunjangan_bpjs'] && !$kontrak['tunjangan_makan_lokal'] && !$kontrak['tunjangan_makan_luar_jawa'] && !$kontrak['reimburse_transport'] && !$kontrak['reimburse_entertaint'] && !$kontrak['tunjangan_penginapan_max']): ?>
                                <li class="list-group-item">
                                    <span class="text-muted"><i class="fas fa-info-circle me-2"></i>Tidak ada tunjangan khusus</span>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Informasi Tambahan:</h6>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-clock text-info me-2"></i>Jam Kerja</span>
                                    <span>8 jam/hari (Senin-Jumat)</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-bell text-warning me-2"></i>Pemberitahuan PHK</span>
                                    <span><?= $kontrak['pemberitahuan_pemutusan_hari'] ?? '30' ?> hari kerja</span>
                                </li>
                                <?php if($kontrak['cuti_bersama_disesuaikan']): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-calendar-check text-success me-2"></i>Cuti Bersama</span>
                                    <span class="badge bg-success">Disesuaikan</span>
                                </li>
                                <?php endif; ?>
                            </ul>
                            
                            <!-- Alert untuk kontrak yang akan berakhir -->
                            <?php if($kontrak['status'] == 'Aktif' && $kontrak['tanggal_selesai']): ?>
                                <?php 
                                $today = new DateTime();
                                $endDate = new DateTime($kontrak['tanggal_selesai']);
                                $interval = $today->diff($endDate);
                                $daysLeft = $interval->days;
                                
                                if ($daysLeft <= 30): ?>
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Kontrak akan berakhir dalam <?= $daysLeft ?> hari</strong>
                                    <br>
                                    <small>Tanggal berakhir: <?= date('d/m/Y', strtotime($kontrak['tanggal_selesai'])) ?></small>
                                </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Tab Hak Cuti -->
                <div class="tab-pane fade" id="cuti" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-calendar-day me-2"></i>Ketentuan Cuti</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table">
                                        <tr>
                                            <th width="60%">Hak Cuti Timbul Setelah</th>
                                            <td><?= $kontrak['hak_cuti_setelah_tahun'] ?? '1' ?> tahun bekerja</td>
                                        </tr>
                                        <tr>
                                            <th>Jumlah Cuti Tahunan</th>
                                            <td><?= $kontrak['jumlah_cuti_tahunan_hari'] ?? '12' ?> hari kerja</td>
                                        </tr>
                                        <tr>
                                            <th>Cuti Bersama</th>
                                            <td>
                                                <?= $kontrak['cuti_bersama_disesuaikan'] ? 'Disesuaikan dengan ketentuan pemerintah' : 'Tidak diatur khusus' ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Prosedur Pengajuan Cuti</h6>
                                </div>
                                <div class="card-body">
                                    <ol class="mb-0">
                                        <li>Ajukan permohonan cuti secara tertulis</li>
                                        <li>Pengajuan minimal 7 hari sebelum tanggal cuti</li>
                                        <li>Harus mendapat persetujuan atasan langsung</li>
                                        <li>Cuti digunakan sesuai kebutuhan dan izin perusahaan</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tab Target & Komisi -->
                <div class="tab-pane fade" id="target" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <?php if(($kontrak['target_penjualan_bulanan'] ?? 0) > 0): ?>
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-bullseye me-2"></i>Target Penjualan</h6>
                                </div>
                                <div class="card-body">
                                    <div class="text-center">
                                        <h2 class="text-primary">Rp <?= number_format($kontrak['target_penjualan_bulanan'], 0, ',', '.') ?></h2>
                                        <p class="text-muted mb-0">Target penjualan bulanan</p>
                                        <small class="text-muted">*Sebelum PPN</small>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if(!empty($kontrak['komisi_aturan'])): ?>
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-percentage me-2"></i>Aturan Komisi</h6>
                                </div>
                                <div class="card-body">
                                    <div style="white-space: pre-line;"><?= safe_nl2br($kontrak['komisi_aturan']) ?></div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Catatan Target</h6>
                                </div>
                                <div class="card-body">
                                    <?php if(($kontrak['target_penjualan_bulanan'] ?? 0) > 0): ?>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            Target berlaku untuk penjualan bulanan
                                        </li>
                                        <li class="list-group-item">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            Perhitungan komisi berdasarkan profit
                                        </li>
                                        <li class="list-group-item">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            HPP dan Net sales dihitung bersama management
                                        </li>
                                        <li class="list-group-item">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            Komisi dibayarkan sesuai peraturan perpajakan
                                        </li>
                                    </ul>
                                    <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Tidak ada target penjualan khusus untuk jabatan ini</p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tab Pihak Penandatangan -->
                <div class="tab-pane fade" id="pihak" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fas fa-building me-2"></i>Pihak Pertama (Perusahaan)</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table">
                                        <tr>
                                            <th width="30%">Nama</th>
                                            <td><?= esc($kontrak['pihak_pertama_nama'] ?? 'PT. Cipta Duta Wacana') ?></td>
                                        </tr>
                                        <tr>
                                            <th>Jabatan</th>
                                            <td><?= esc($kontrak['pihak_pertama_jabatan'] ?? 'Direktur') ?></td>
                                        </tr>
                                        <tr>
                                            <th>Alamat</th>
                                            <td><?= safe_nl2br($kontrak['pihak_pertama_alamat'] ?? 'Villa Bintaro Regency Blok K1 No. 2 Pondok Kacang Timur, Tangerang Selatan 15226') ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-user me-2"></i>Pihak Kedua (Karyawan)</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table">
                                        <tr>
                                            <th width="30%">Nama</th>
                                            <td><?= esc($kontrak['pihak_kedua_nama'] ?? $kontrak['nama_lengkap'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <th>Jabatan</th>
                                            <td><?= esc($kontrak['pihak_kedua_jabatan'] ?? $kontrak['jabatan'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <th>Alamat</th>
                                            <td><?= safe_nl2br($kontrak['pihak_kedua_alamat'] ?? ($kontrak['alamat'] ?? '-')) ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Informasi Penandatanganan:</strong>
                        Kontrak ini dibuat dalam rangkap 2 (dua), masing-masing memiliki kekuatan hukum yang sama. 
                        Satu rangkap dipegang oleh Pihak Pertama dan satu rangkap lainnya oleh Pihak Kedua.
                    </div>
                </div>
                
                <!-- Tab Lampiran & Status -->
                <div class="tab-pane fade" id="lampiran" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-paperclip me-2"></i>Lampiran Kontrak</h6>
                                </div>
                                <div class="card-body">
                                    <?php if(!empty($kontrak['lampiran_path'])): ?>
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div>
                                            <i class="fas fa-file-pdf fa-2x text-danger me-3"></i>
                                            <span>File Kontrak PDF</span>
                                        </div>
                                        <div>
                                            <a href="<?= base_url('admin/karyawan/kontrak/download/' . $kontrak['id']) ?>" 
                                               class="btn btn-sm btn-success me-2">
                                                <i class="fas fa-download me-1"></i> Unduh
                                            </a>
                                            <a href="<?= base_url('admin/karyawan/kontrak/preview/' . $kontrak['id']) ?>" 
                                               target="_blank" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye me-1"></i> Preview
                                            </a>
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Path: <?= esc($kontrak['lampiran_path']) ?>
                                    </small>
                                    <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-file-upload fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Tidak ada file lampiran yang diunggah</p>
                                        <a href="<?= base_url('admin/karyawan/kontrak/edit/' . $kontrak['id']) ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-upload me-1"></i> Upload File
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Status</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table">
                                        <tr>
                                            <th width="40%">Status Saat Ini</th>
                                            <td>
                                                <span class="badge bg-<?= 
                                                    ($kontrak['status'] == 'Aktif') ? 'success' : 
                                                    (($kontrak['status'] == 'Draft') ? 'warning' : 
                                                    (($kontrak['status'] == 'Selesai') ? 'info' : 
                                                    (($kontrak['status'] == 'Diperpanjang') ? 'primary' : 
                                                    (($kontrak['status'] == 'Diputus') ? 'danger' : 'secondary')))) 
                                                ?>">
                                                    <?= $kontrak['status'] ?? 'Draft' ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Dibuat</th>
                                            <td><?= date('d/m/Y H:i', strtotime($kontrak['created_at'] ?? '')) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Terakhir Diupdate</th>
                                            <td><?= date('d/m/Y H:i', strtotime($kontrak['updated_at'] ?? '')) ?></td>
                                        </tr>
                                        <?php if(!empty($kontrak['alasan_berakhir'])): ?>
                                        <tr>
                                            <th>Alasan Berakhir</th>
                                            <td><?= safe_nl2br($kontrak['alasan_berakhir']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tombol Aksi Status -->
                    <?php if($kontrak['status'] != 'Selesai' && $kontrak['status'] != 'Diputus'): ?>
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-cogs me-2"></i>Aksi Status</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <button class="btn btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#statusModal" data-status="Aktif">
                                        <i class="fas fa-play me-1"></i> Set Aktif
                                    </button>
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-info w-100 mb-2" data-bs-toggle="modal" data-bs-target="#statusModal" data-status="Selesai">
                                        <i class="fas fa-flag-checkered me-1"></i> Set Selesai
                                    </button>
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-danger w-100 mb-2" data-bs-toggle="modal" data-bs-target="#statusModal" data-status="Diputus">
                                        <i class="fas fa-ban me-1"></i> Set Diputus
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Pilih aksi untuk mengubah status kontrak
                            </small>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tombol Aksi Footer -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div>
                    <a href="<?= base_url('admin/karyawan/kontrak/karyawan/' . $kontrak['karyawan_id']) ?>" class="btn btn-outline-primary">
                        <i class="fas fa-list me-1"></i> Lihat Semua Kontrak Karyawan Ini
                    </a>
                </div>
                <div>
                    <a href="<?= base_url('admin/karyawan/show/' . $kontrak['karyawan_id']) ?>" class="btn btn-outline-info me-2">
                        <i class="fas fa-user me-1"></i> Profil Karyawan
                    </a>
                    <a href="<?= base_url('admin/karyawan/kontrak/edit/' . $kontrak['id']) ?>" class="btn btn-warning me-2">
                        <i class="fas fa-edit me-1"></i> Edit Kontrak
                    </a>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-1"></i> Hapus Kontrak
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ubah Status -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('admin/karyawan/kontrak/update-status/' . $kontrak['id']) ?>" method="post">
                <?= csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Ubah Status Kontrak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="statusSelect" class="form-label">Status Baru</label>
                        <select class="form-control" id="statusSelect" name="status" required>
                            <option value="Draft">Draft</option>
                            <option value="Aktif">Aktif</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Diperpanjang">Diperpanjang</option>
                            <option value="Diputus">Diputus</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="alasan_berakhir" class="form-label">Alasan (Opsional)</label>
                        <textarea class="form-control" id="alasan_berakhir" name="alasan" rows="3" 
                                  placeholder="Isikan alasan perubahan status..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus Kontrak</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus kontrak ini?</p>
                <p class="text-danger">
                    <small>
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Kontrak yang telah dihapus tidak dapat dikembalikan.
                    </small>
                </p>
                <div class="alert alert-warning">
                    <strong>Detail Kontrak:</strong><br>
                    Nomor: <?= esc($kontrak['nomor_kontrak']) ?><br>
                    Karyawan: <?= esc($kontrak['nama_lengkap']) ?><br>
                    Jabatan: <?= esc($kontrak['jabatan']) ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="<?= base_url('admin/karyawan/kontrak/delete/' . $kontrak['id']) ?>" method="post" class="d-inline">
                    <?= csrf_field(); ?>
                    <button type="submit" class="btn btn-danger">Hapus Kontrak</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Print -->
<div class="modal fade" id="printModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cetak Kontrak</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Pilih format untuk mencetak kontrak:</p>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <a href="<?= base_url('admin/karyawan/kontrak/print/' . $kontrak['id']) ?>" 
                           target="_blank" class="btn btn-outline-primary w-100">
                            <i class="fas fa-print me-1"></i> Format Standar
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <button class="btn btn-outline-secondary w-100" onclick="printAsPDF()">
                            <i class="fas fa-file-pdf me-1"></i> Save as PDF
                        </button>
                    </div>
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i>
                    Pastikan printer tersambung sebelum mencetak.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Handle status modal
        $('#statusModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var status = button.data('status');
            var modal = $(this);
            
            modal.find('#statusSelect').val(status);
            
            // Update modal title based on status
            var statusText = status;
            switch(status) {
                case 'Aktif': statusText = 'Aktifkan Kontrak'; break;
                case 'Selesai': statusText = 'Tandai Selesai'; break;
                case 'Diputus': statusText = 'Putuskan Kontrak'; break;
            }
            modal.find('.modal-title').text(statusText);
        });
        
        // Print as PDF (simulate)
        function printAsPDF() {
            alert('Fitur Save as PDF akan segera tersedia');
            // window.open('<?= base_url('admin/karyawan/kontrak/pdf/' . $kontrak['id']) ?>', '_blank');
        }
        
        // Auto-hide alerts
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
        
        // Tab activation
        $('#kontrakTab button').click(function() {
            $(this).tab('show');
        });
        
        // Smooth scroll to active tab on page load
        $(window).on('load', function() {
            var hash = window.location.hash;
            if (hash) {
                $('button[data-bs-target="' + hash + '"]').tab('show');
                $('html, body').animate({
                    scrollTop: $(hash).offset().top - 100
                }, 500);
            }
        });
    });
</script>

<style>
    .nav-tabs .nav-link {
        color: #495057;
        border: 1px solid transparent;
        border-top-left-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
    }
    
    .nav-tabs .nav-link.active {
        color: #0d6efd;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
        font-weight: 600;
    }
    
    .tab-content {
        padding-top: 20px;
    }
    
    .list-group-item {
        border: none;
        padding: 10px 0;
    }
    
    .card-header.bg-light {
        background-color: #f8f9fa !important;
    }
</style>

<?= $this->include('admin/templates/footer') ?>