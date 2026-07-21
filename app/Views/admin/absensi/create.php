<?php
$title = 'Tambah Absensi Manual';
$active = 'absensi';
?>

<style>
    .create-card {
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
    }
    
    .create-header {
        background: linear-gradient(135deg, #4e73df 0%, #2e59d9 100%);
        color: white;
        padding: 25px 30px;
        border-radius: 15px 15px 0 0;
    }
    
    .create-body {
        padding: 30px;
        background: white;
    }
    
    .form-section {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .form-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .section-title {
        color: #4e73df;
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .section-title i {
        margin-right: 10px;
    }
    
    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
    }
    
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #ced4da;
        padding: 10px 15px;
        transition: all 0.3s;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
    }
    
    .required-field::after {
        content: " *";
        color: #dc3545;
    }
    
    .form-hint {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 5px;
    }
    
    .time-inputs {
        display: flex;
        gap: 15px;
        align-items: center;
    }
    
    .time-inputs .form-control {
        flex: 1;
    }
    
    .status-options {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 10px;
        margin-top: 10px;
    }
    
    .status-option {
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        padding: 15px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        background: white;
    }
    
    .status-option:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .status-option.selected {
        border-color: #4e73df;
        background: linear-gradient(135deg, rgba(78, 115, 223, 0.05), rgba(78, 115, 223, 0.1));
    }
    
    .status-icon {
        font-size: 24px;
        margin-bottom: 10px;
    }
    
    .status-option.hadir .status-icon { color: #28a745; }
    .status-option.izin .status-icon { color: #17a2b8; }
    .status-option.sakit .status-icon { color: #ffc107; }
    .status-option.cuti .status-icon { color: #6f42c1; }
    .status-option.alpha .status-icon { color: #dc3545; }
    
    .shift-options {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 10px;
        margin-top: 10px;
    }
    
    .shift-option {
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        padding: 15px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        background: white;
    }
    
    .shift-option:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .shift-option.selected {
        border-color: #4e73df;
        background: linear-gradient(135deg, rgba(78, 115, 223, 0.05), rgba(78, 115, 223, 0.1));
    }
    
    .shift-icon {
        font-size: 20px;
        margin-bottom: 8px;
    }
    
    .shift-option.pagi .shift-icon { color: #ffc107; }
    .shift-option.siang .shift-icon { color: #28a745; }
    .shift-option.sore .shift-icon { color: #17a2b8; }
    .shift-option.malam .shift-icon { color: #6f42c1; }
    
    .shift-time {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 5px;
    }
    
    .action-buttons {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #f0f0f0;
    }
    
    .action-buttons .btn {
        min-width: 120px;
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 600;
    }
    
    @media (max-width: 768px) {
        .create-body {
            padding: 20px;
        }
        
        .time-inputs {
            flex-direction: column;
            gap: 10px;
        }
        
        .status-options, .shift-options {
            grid-template-columns: 1fr;
        }
        
        .action-buttons {
            flex-direction: column;
        }
        
        .action-buttons .btn {
            width: 100%;
            margin-bottom: 10px;
        }
    }
    
    /* Error styling */
    .is-invalid {
        border-color: #dc3545 !important;
    }
    
    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 5px;
    }
    
    .location-inputs .row {
        margin-bottom: 10px;
    }
    
    .location-hint {
        font-size: 0.8rem;
        color: #6c757d;
        font-style: italic;
    }
</style>

<div class="container-fluid py-4">
    <!-- Back Navigation -->
    <div class="mb-4">
        <a href="<?= base_url('admin/absensi') ?>" class="text-decoration-none text-primary">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Absensi
        </a>
    </div>

    <!-- Main Create Form Card -->
    <div class="create-card">
        <!-- Header -->
        <div class="create-header">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="fas fa-plus-circle fa-2x"></i>
                </div>
                <div>
                    <h4 class="mb-1">Tambah Absensi Manual</h4>
                    <p class="mb-0 opacity-75">
                        Tambahkan data absensi karyawan secara manual
                    </p>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="create-body">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <form method="post" action="<?= base_url('admin/absensi/store') ?>" id="createAbsensiForm">
                <?= csrf_field() ?>
                
                <!-- Section 1: Basic Information -->
                <div class="form-section">
                    <h5 class="section-title">
                        <i class="fas fa-user-circle"></i>Informasi Dasar
                    </h5>
                    
                    <div class="row">
                        <!-- Karyawan Selection -->
                        <div class="col-md-6 mb-3">
                            <label for="karyawan_id" class="form-label required-field">Pilih Karyawan</label>
                            <select class="form-select <?= session()->getFlashdata('karyawan_id_error') ? 'is-invalid' : '' ?>" 
                                    id="karyawan_id" name="karyawan_id" required>
                                <option value="">-- Pilih Karyawan --</option>
                                <?php if (!empty($karyawanList)): ?>
                                    <?php foreach ($karyawanList as $karyawan): ?>
                                        <option value="<?= $karyawan['id'] ?>" 
                                            <?= old('karyawan_id') == $karyawan['id'] ? 'selected' : '' ?>>
                                            <?= esc($karyawan['nama_lengkap']) ?> (<?= esc($karyawan['nik']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php if (session()->getFlashdata('karyawan_id_error')): ?>
                                <div class="invalid-feedback">
                                    <?= session()->getFlashdata('karyawan_id_error') ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-hint">Pilih karyawan yang akan ditambahkan absensinya</div>
                        </div>
                        
                        <!-- Tanggal Absensi -->
                        <div class="col-md-6 mb-3">
                            <label for="tanggal" class="form-label required-field">Tanggal Absensi</label>
                            <input type="date" 
                                   class="form-control <?= session()->getFlashdata('tanggal_error') ? 'is-invalid' : '' ?>" 
                                   id="tanggal" 
                                   name="tanggal" 
                                   value="<?= old('tanggal', date('Y-m-d')) ?>" 
                                   required>
                            <?php if (session()->getFlashdata('tanggal_error')): ?>
                                <div class="invalid-feedback">
                                    <?= session()->getFlashdata('tanggal_error') ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-hint">Tanggal saat absensi dilakukan</div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Status & Shift -->
                <div class="form-section">
                    <h5 class="section-title">
                        <i class="fas fa-calendar-check"></i>Status & Shift
                    </h5>
                    
                    <div class="row">
                        <!-- Status Selection -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label required-field">Status Absensi</label>
                            <div class="status-options">
                                <!-- Hadir -->
                                <div class="status-option hadir <?= old('status') == 'Hadir' || old('status') == '' ? 'selected' : '' ?>" 
                                     onclick="selectStatus('Hadir')">
                                    <div class="status-icon">
                                        <i class="fas fa-user-check"></i>
                                    </div>
                                    <div class="status-name">Hadir</div>
                                    <input type="radio" name="status" value="Hadir" 
                                           <?= old('status') == 'Hadir' || old('status') == '' ? 'checked' : '' ?> 
                                           style="display: none;">
                                </div>
                                
                                <!-- Izin -->
                                <div class="status-option izin <?= old('status') == 'Izin' ? 'selected' : '' ?>" 
                                     onclick="selectStatus('Izin')">
                                    <div class="status-icon">
                                        <i class="fas fa-file-contract"></i>
                                    </div>
                                    <div class="status-name">Izin</div>
                                    <input type="radio" name="status" value="Izin" 
                                           <?= old('status') == 'Izin' ? 'checked' : '' ?> 
                                           style="display: none;">
                                </div>
                                
                                <!-- Sakit -->
                                <div class="status-option sakit <?= old('status') == 'Sakit' ? 'selected' : '' ?>" 
                                     onclick="selectStatus('Sakit')">
                                    <div class="status-icon">
                                        <i class="fas fa-procedures"></i>
                                    </div>
                                    <div class="status-name">Sakit</div>
                                    <input type="radio" name="status" value="Sakit" 
                                           <?= old('status') == 'Sakit' ? 'checked' : '' ?> 
                                           style="display: none;">
                                </div>
                                
                                <!-- Cuti -->
                                <div class="status-option cuti <?= old('status') == 'Cuti' ? 'selected' : '' ?>" 
                                     onclick="selectStatus('Cuti')">
                                    <div class="status-icon">
                                        <i class="fas fa-umbrella-beach"></i>
                                    </div>
                                    <div class="status-name">Cuti</div>
                                    <input type="radio" name="status" value="Cuti" 
                                           <?= old('status') == 'Cuti' ? 'checked' : '' ?> 
                                           style="display: none;">
                                </div>
                                
                                <!-- Alpha -->
                                <div class="status-option alpha <?= old('status') == 'Alpha' ? 'selected' : '' ?>" 
                                     onclick="selectStatus('Alpha')">
                                    <div class="status-icon">
                                        <i class="fas fa-user-times"></i>
                                    </div>
                                    <div class="status-name">Alpha</div>
                                    <input type="radio" name="status" value="Alpha" 
                                           <?= old('status') == 'Alpha' ? 'checked' : '' ?> 
                                           style="display: none;">
                                </div>
                            </div>
                            <?php if (session()->getFlashdata('status_error')): ?>
                                <div class="invalid-feedback d-block">
                                    <?= session()->getFlashdata('status_error') ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-hint mt-2">Pilih status absensi karyawan</div>
                        </div>
                        
                        <!-- Shift Selection -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label required-field">Shift Kerja</label>
                            <div class="shift-options">
                                <!-- Pagi -->
                                <div class="shift-option pagi <?= old('shift') == 'pagi' || old('shift') == '' ? 'selected' : '' ?>" 
                                     onclick="selectShift('pagi')">
                                    <div class="shift-icon">
                                        <i class="fas fa-sun"></i>
                                    </div>
                                    <div class="shift-name">Pagi</div>
                                    <div class="shift-time">07:00 - 16:00</div>
                                    <input type="radio" name="shift" value="pagi" 
                                           <?= old('shift') == 'pagi' || old('shift') == '' ? 'checked' : '' ?> 
                                           style="display: none;">
                                </div>
                                
                                <!-- Siang -->
                                <div class="shift-option siang <?= old('shift') == 'siang' ? 'selected' : '' ?>" 
                                     onclick="selectShift('siang')">
                                    <div class="shift-icon">
                                        <i class="fas fa-cloud-sun"></i>
                                    </div>
                                    <div class="shift-name">Siang</div>
                                    <div class="shift-time">08:00 - 17:00</div>
                                    <input type="radio" name="shift" value="siang" 
                                           <?= old('shift') == 'siang' ? 'checked' : '' ?> 
                                           style="display: none;">
                                </div>
                                
                                <!-- Sore -->
                                <div class="shift-option sore <?= old('shift') == 'sore' ? 'selected' : '' ?>" 
                                     onclick="selectShift('sore')">
                                    <div class="shift-icon">
                                        <i class="fas fa-moon"></i>
                                    </div>
                                    <div class="shift-name">Sore</div>
                                    <div class="shift-time">09:00 - 18:00</div>
                                    <input type="radio" name="shift" value="sore" 
                                           <?= old('shift') == 'sore' ? 'checked' : '' ?> 
                                           style="display: none;">
                                </div>
                                
                                <!-- Malam -->
                                <div class="shift-option malam <?= old('shift') == 'malam' ? 'selected' : '' ?>" 
                                     onclick="selectShift('malam')">
                                    <div class="shift-icon">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="shift-name">Malam</div>
                                    <div class="shift-time">20:00 - 05:00</div>
                                    <input type="radio" name="shift" value="malam" 
                                           <?= old('shift') == 'malam' ? 'checked' : '' ?> 
                                           style="display: none;">
                                </div>
                            </div>
                            <?php if (session()->getFlashdata('shift_error')): ?>
                                <div class="invalid-feedback d-block">
                                    <?= session()->getFlashdata('shift_error') ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-hint mt-2">Pilih shift kerja karyawan</div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Time Information -->
                <div class="form-section" id="timeSection">
                    <h5 class="section-title">
                        <i class="fas fa-clock"></i>Waktu Kerja
                    </h5>
                    
                    <div class="row">
                        <!-- Waktu Masuk -->
                        <div class="col-md-6 mb-3">
                            <label for="waktu_masuk" class="form-label">Waktu Masuk</label>
                            <div class="time-inputs">
                                <input type="time" 
                                       class="form-control <?= session()->getFlashdata('waktu_masuk_error') ? 'is-invalid' : '' ?>" 
                                       id="waktu_masuk" 
                                       name="waktu_masuk" 
                                       value="<?= old('waktu_masuk', '08:00') ?>">
                                <button type="button" class="btn btn-outline-secondary" onclick="setCurrentTime('waktu_masuk')">
                                    <i class="fas fa-clock"></i> Sekarang
                                </button>
                            </div>
                            <?php if (session()->getFlashdata('waktu_masuk_error')): ?>
                                <div class="invalid-feedback">
                                    <?= session()->getFlashdata('waktu_masuk_error') ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-hint">Waktu check-in karyawan (format: HH:MM)</div>
                        </div>
                        
                        <!-- Waktu Pulang -->
                        <div class="col-md-6 mb-3">
                            <label for="waktu_pulang" class="form-label">Waktu Pulang</label>
                            <div class="time-inputs">
                                <input type="time" 
                                       class="form-control <?= session()->getFlashdata('waktu_pulang_error') ? 'is-invalid' : '' ?>" 
                                       id="waktu_pulang" 
                                       name="waktu_pulang" 
                                       value="<?= old('waktu_pulang', '17:00') ?>">
                                <button type="button" class="btn btn-outline-secondary" onclick="setCurrentTime('waktu_pulang')">
                                    <i class="fas fa-clock"></i> Sekarang
                                </button>
                            </div>
                            <?php if (session()->getFlashdata('waktu_pulang_error')): ?>
                                <div class="invalid-feedback">
                                    <?= session()->getFlashdata('waktu_pulang_error') ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-hint">Waktu check-out karyawan (format: HH:MM)</div>
                        </div>
                    </div>
                    
                    <!-- Terlambat & Lembur (auto-calculated) -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Terlambat</label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control" 
                                       id="terlambat" 
                                       name="terlambat" 
                                       value="<?= old('terlambat', 0) ?>" 
                                       min="0" 
                                       step="1">
                                <span class="input-group-text">menit</span>
                            </div>
                            <div class="form-hint">Menit keterlambatan (otomatis terhitung)</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam Lembur</label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control" 
                                       id="jam_lembur" 
                                       name="jam_lembur" 
                                       value="<?= old('jam_lembur', 0) ?>" 
                                       min="0" 
                                       step="0.1">
                                <span class="input-group-text">jam</span>
                            </div>
                            <div class="form-hint">Jam lembur (otomatis terhitung)</div>
                        </div>
                    </div>
                    
                    <!-- Calculate Button -->
                    <div class="mb-3">
                        <button type="button" class="btn btn-outline-info" onclick="calculateWorkingHours()">
                            <i class="fas fa-calculator me-1"></i> Hitung Otomatis
                        </button>
                        <div class="form-hint mt-2">
                            Klik untuk menghitung jam kerja, terlambat, dan lembur otomatis berdasarkan waktu masuk/pulang
                        </div>
                    </div>
                </div>

                <!-- Section 4: Additional Information -->
                <div class="form-section">
                    <h5 class="section-title">
                        <i class="fas fa-info-circle"></i>Informasi Tambahan
                    </h5>
                    
                    <!-- Keterangan -->
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control <?= session()->getFlashdata('keterangan_error') ? 'is-invalid' : '' ?>" 
                                  id="keterangan" 
                                  name="keterangan" 
                                  rows="3" 
                                  placeholder="Tambahkan keterangan atau catatan..."><?= old('keterangan', 'di absen oleh admin') ?></textarea>
                        <?php if (session()->getFlashdata('keterangan_error')): ?>
                            <div class="invalid-feedback">
                                <?= session()->getFlashdata('keterangan_error') ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-hint">Keterangan tambahan untuk absensi ini</div>
                    </div>
                    
                    <!-- Location Information -->
                    <div class="mb-3 location-inputs">
                        <label class="form-label">Informasi Lokasi</label>
                        
                        <!-- Lokasi Masuk -->
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <input type="text" 
                                       class="form-control" 
                                       name="lokasi_masuk" 
                                       value="<?= old('lokasi_masuk', 'Kantor CDW Engineering') ?>" 
                                       placeholder="Lokasi Masuk">
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-6">
                                        <input type="hidden" 
                                               id="latitude_masuk" 
                                               name="latitude_masuk" 
                                               value="<?= old('latitude_masuk', '-6.262569490423196') ?>">
                                        <input type="text" 
                                               class="form-control" 
                                               value="-6.262569490423196" 
                                               readonly>
                                    </div>
                                    <div class="col-6">
                                        <input type="hidden" 
                                               id="longitude_masuk" 
                                               name="longitude_masuk" 
                                               value="<?= old('longitude_masuk', '106.70090840363204') ?>">
                                        <input type="text" 
                                               class="form-control" 
                                               value="106.70090840363204" 
                                               readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="location-hint">Default: Kantor CDW Engineering (Lat: -6.262569490423196, Long: 106.70090840363204)</div>
                            </div>
                        </div>
                        
                        <!-- Lokasi Pulang -->
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" 
                                       class="form-control" 
                                       name="lokasi_pulang" 
                                       value="<?= old('lokasi_pulang', 'Kantor CDW Engineering') ?>" 
                                       placeholder="Lokasi Pulang">
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-6">
                                        <input type="hidden" 
                                               id="latitude_pulang" 
                                               name="latitude_pulang" 
                                               value="<?= old('latitude_pulang', '-6.262569490423196') ?>">
                                        <input type="text" 
                                               class="form-control" 
                                               value="-6.262569490423196" 
                                               readonly>
                                    </div>
                                    <div class="col-6">
                                        <input type="hidden" 
                                               id="longitude_pulang" 
                                               name="longitude_pulang" 
                                               value="<?= old('longitude_pulang', '106.70090840363204') ?>">
                                        <input type="text" 
                                               class="form-control" 
                                               value="106.70090840363204" 
                                               readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="location-hint">Default: Kantor CDW Engineering (Lat: -6.262569490423196, Long: 106.70090840363204)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="<?= base_url('admin/absensi') ?>" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Default coordinates for CDW Office
const DEFAULT_LATITUDE = "-6.262569490423196";
const DEFAULT_LONGITUDE = "106.70090840363204";
const DEFAULT_LOCATION = "Kantor CDW Engineering";
const DEFAULT_KETERANGAN = "di absen oleh admin";

// Status Selection
function selectStatus(status) {
    // Remove selected class from all options
    document.querySelectorAll('.status-option').forEach(option => {
        option.classList.remove('selected');
    });
    
    // Add selected class to clicked option
    const selectedOption = document.querySelector(`.status-option.${status.toLowerCase()}`);
    selectedOption.classList.add('selected');
    
    // Check the radio button
    const radio = selectedOption.querySelector('input[type="radio"]');
    radio.checked = true;
    
    // Show/hide time section based on status
    toggleTimeSection();
}

// Shift Selection
function selectShift(shift) {
    // Remove selected class from all options
    document.querySelectorAll('.shift-option').forEach(option => {
        option.classList.remove('selected');
    });
    
    // Add selected class to clicked option
    const selectedOption = document.querySelector(`.shift-option.${shift}`);
    selectedOption.classList.add('selected');
    
    // Check the radio button
    const radio = selectedOption.querySelector('input[type="radio"]');
    radio.checked = true;
    
    // Set default times based on shift
    setDefaultTimes(shift);
}

// Set default times based on shift
function setDefaultTimes(shift) {
    const timeDefaults = {
        'pagi': { masuk: '07:00', pulang: '16:00' },
        'siang': { masuk: '08:00', pulang: '17:00' },
        'sore': { masuk: '09:00', pulang: '18:00' },
        'malam': { masuk: '20:00', pulang: '05:00' }
    };
    
    const defaults = timeDefaults[shift] || { masuk: '08:00', pulang: '17:00' };
    
    document.getElementById('waktu_masuk').value = defaults.masuk;
    document.getElementById('waktu_pulang').value = defaults.pulang;
}

// Set current time
function setCurrentTime(fieldId) {
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    document.getElementById(fieldId).value = `${hours}:${minutes}`;
}

// Toggle time section visibility based on status
function toggleTimeSection() {
    const status = document.querySelector('input[name="status"]:checked').value;
    const timeSection = document.getElementById('timeSection');
    
    if (status === 'Hadir') {
        timeSection.style.display = 'block';
    } else {
        timeSection.style.display = 'none';
    }
}

// Calculate working hours, late time, and overtime
function calculateWorkingHours() {
    const waktuMasuk = document.getElementById('waktu_masuk').value;
    const waktuPulang = document.getElementById('waktu_pulang').value;
    const shift = document.querySelector('input[name="shift"]:checked').value;
    
    if (!waktuMasuk || !waktuPulang) {
        alert('Harap isi waktu masuk dan waktu pulang terlebih dahulu');
        return;
    }
    
    // Convert time strings to minutes
    const masukMins = timeToMinutes(waktuMasuk);
    let pulangMins = timeToMinutes(waktuPulang);
    
    // Handle overnight shifts (pulang < masuk)
    if (pulangMins < masukMins) {
        pulangMins += 1440; // Add 24 hours in minutes
    }
    
    // Get shift start and end times
    const shiftTimes = getShiftTimes(shift);
    const shiftStartMins = timeToMinutes(shiftTimes.start);
    let shiftEndMins = timeToMinutes(shiftTimes.end);
    
    // Handle overnight shift end time
    if (shiftEndMins < shiftStartMins) {
        shiftEndMins += 1440;
    }
    
    // Calculate total working minutes
    const totalMinutes = pulangMins - masukMins;
    
    // Calculate late minutes
    let lateMinutes = 0;
    if (masukMins > shiftStartMins) {
        // 30 minutes tolerance
        const tolerance = 30;
        if (masukMins > shiftStartMins + tolerance) {
            lateMinutes = masukMins - (shiftStartMins + tolerance);
        }
    }
    
    // Calculate overtime minutes
    let overtimeMinutes = 0;
    if (pulangMins > shiftEndMins) {
        overtimeMinutes = pulangMins - shiftEndMins;
    }
    
    // Calculate break deduction (1 hour for regular shifts, 30 mins for night shift)
    const breakMinutes = shift === 'malam' ? 30 : 60;
    const effectiveMinutes = Math.max(0, totalMinutes - breakMinutes);
    
    // Convert to hours and update fields
    const effectiveHours = (effectiveMinutes / 60).toFixed(2);
    const overtimeHours = (overtimeMinutes / 60).toFixed(2);
    
    // Update form fields
    document.getElementById('terlambat').value = Math.round(lateMinutes);
    document.getElementById('jam_lembur').value = parseFloat(overtimeHours);
    
    // Show results
    alert(`Hasil perhitungan:\n\n` +
          `Jam kerja efektif: ${effectiveHours} jam\n` +
          `Terlambat: ${Math.round(lateMinutes)} menit\n` +
          `Lembur: ${overtimeHours} jam`);
}

// Convert time string (HH:MM) to minutes
function timeToMinutes(timeStr) {
    if (!timeStr) return 0;
    const [hours, minutes] = timeStr.split(':').map(Number);
    return hours * 60 + minutes;
}

// Get shift times based on shift type
function getShiftTimes(shift) {
    const shiftMap = {
        'pagi': { start: '07:00', end: '16:00' },
        'siang': { start: '08:00', end: '17:00' },
        'sore': { start: '09:00', end: '18:00' },
        'malam': { start: '20:00', end: '05:00' }
    };
    return shiftMap[shift] || { start: '08:00', end: '17:00' };
}

// Form validation
document.getElementById('createAbsensiForm').addEventListener('submit', function(e) {
    const status = document.querySelector('input[name="status"]:checked').value;
    const waktuMasuk = document.getElementById('waktu_masuk').value;
    const waktuPulang = document.getElementById('waktu_pulang').value;
    
    // If status is "Hadir", check if times are filled
    if (status === 'Hadir') {
        if (!waktuMasuk) {
            e.preventDefault();
            alert('Waktu masuk harus diisi untuk status Hadir');
            document.getElementById('waktu_masuk').focus();
            return false;
        }
        
        // Check if check-out is earlier than check-in (not for night shift)
        if (waktuPulang) {
            const masukMins = timeToMinutes(waktuMasuk);
            let pulangMins = timeToMinutes(waktuPulang);
            
            // Handle night shift separately
            const shift = document.querySelector('input[name="shift"]:checked').value;
            if (shift !== 'malam' && pulangMins < masukMins) {
                e.preventDefault();
                alert('Waktu pulang tidak boleh lebih awal dari waktu masuk');
                document.getElementById('waktu_pulang').focus();
                return false;
            }
        }
    }
    
    // Set default values if empty
    const keterangan = document.getElementById('keterangan');
    if (!keterangan.value.trim()) {
        keterangan.value = DEFAULT_KETERANGAN;
    }
    
    const lokasiMasuk = document.querySelector('input[name="lokasi_masuk"]');
    if (!lokasiMasuk.value.trim()) {
        lokasiMasuk.value = DEFAULT_LOCATION;
    }
    
    const lokasiPulang = document.querySelector('input[name="lokasi_pulang"]');
    if (!lokasiPulang.value.trim()) {
        lokasiPulang.value = DEFAULT_LOCATION;
    }
    
    // Show loading indicator
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';
    submitBtn.disabled = true;
    
    return true;
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set default status if not set
    if (!document.querySelector('input[name="status"]:checked')) {
        selectStatus('Hadir');
    }
    
    // Set default shift if not set
    if (!document.querySelector('input[name="shift"]:checked')) {
        selectShift('siang');
    }
    
    // Toggle time section based on initial status
    toggleTimeSection();
    
    // Set today's date as default if not set
    if (!document.getElementById('tanggal').value) {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('tanggal').value = today;
    }
    
    // Set default keterangan if empty
    const keterangan = document.getElementById('keterangan');
    if (!keterangan.value.trim()) {
        keterangan.value = DEFAULT_KETERANGAN;
    }
    
    // Set default location if empty
    const lokasiMasuk = document.querySelector('input[name="lokasi_masuk"]');
    const lokasiPulang = document.querySelector('input[name="lokasi_pulang"]');
    
    if (!lokasiMasuk.value.trim()) {
        lokasiMasuk.value = DEFAULT_LOCATION;
    }
    
    if (!lokasiPulang.value.trim()) {
        lokasiPulang.value = DEFAULT_LOCATION;
    }
});
</script>