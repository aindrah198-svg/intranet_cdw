<?php
$title = 'Tambah Karyawan Baru';
$active = 'karyawan';
$css = [];
$scripts = [];
?>

<?= $this->include('admin/templates/header') ?>
<?= $this->include('admin/templates/sidebar') ?>
<?= $this->include('admin/templates/navbar') ?>

<!-- Main Content -->
<div class="dashboard-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-2" style="color: var(--cdw-dark); font-weight: 600;">
                <i class="fas fa-user-plus me-2"></i>Tambah Karyawan Baru
            </h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/karyawan') ?>">Karyawan</a></li>
                    <li class="breadcrumb-item active">Tambah Baru</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('admin/karyawan') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
    
    <form action="<?= base_url('admin/karyawan/store') ?>" method="post" enctype="multipart/form-data">
        <div class="row">
            <!-- Kolom Kiri: Data Pribadi -->
            <div class="col-lg-6">
                <div class="card mb-4" style="border: 1px solid #eaeaea;">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-user me-2"></i> Data Pribadi</h6>
                    </div>
                    <div class="card-body">
                        <!-- NIK -->
                        <div class="mb-3">
                            <label class="form-label">NIK <span class="text-danger">*</span></label>
                            <input type="text" name="nik" class="form-control <?= session('errors.nik') ? 'is-invalid' : '' ?>" 
                                   value="<?= old('nik') ?>" required placeholder="Masukkan NIK">
                            <?php if (session('errors.nik')): ?>
                                <div class="invalid-feedback"><?= session('errors.nik') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Nama Lengkap -->
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control <?= session('errors.nama_lengkap') ? 'is-invalid' : '' ?>" 
                                   value="<?= old('nama_lengkap') ?>" required placeholder="Masukkan nama lengkap">
                            <?php if (session('errors.nama_lengkap')): ?>
                                <div class="invalid-feedback"><?= session('errors.nama_lengkap') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Nama Panggilan -->
                        <div class="mb-3">
                            <label class="form-label">Nama Panggilan</label>
                            <input type="text" name="nama_panggilan" class="form-control" 
                                   value="<?= old('nama_panggilan') ?>" placeholder="Masukkan nama panggilan">
                        </div>
                        
                        <!-- Jenis Kelamin -->
                        <div class="mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="L" <?= old('jenis_kelamin') == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="P" <?= old('jenis_kelamin') == 'P' ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                        </div>
                        
                        <!-- Tempat & Tanggal Lahir -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control" 
                                       value="<?= old('tempat_lahir') ?>" placeholder="Tempat lahir">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control" 
                                       value="<?= old('tanggal_lahir') ?>">
                            </div>
                        </div>
                        
                        <!-- Agama -->
                        <div class="mb-3">
                            <label class="form-label">Agama</label>
                            <select name="agama" class="form-select">
                                <option value="">Pilih Agama</option>
                                <option value="Islam" <?= old('agama') == 'Islam' ? 'selected' : '' ?>>Islam</option>
                                <option value="Kristen" <?= old('agama') == 'Kristen' ? 'selected' : '' ?>>Kristen</option>
                                <option value="Katolik" <?= old('agama') == 'Katolik' ? 'selected' : '' ?>>Katolik</option>
                                <option value="Hindu" <?= old('agama') == 'Hindu' ? 'selected' : '' ?>>Hindu</option>
                                <option value="Buddha" <?= old('agama') == 'Buddha' ? 'selected' : '' ?>>Buddha</option>
                                <option value="Konghucu" <?= old('agama') == 'Konghucu' ? 'selected' : '' ?>>Konghucu</option>
                            </select>
                        </div>
                        
                        <!-- Status Pernikahan -->
                        <div class="mb-3">
                            <label class="form-label">Status Pernikahan</label>
                            <select name="status_pernikahan" class="form-select">
                                <option value="Belum Menikah" <?= old('status_pernikahan') == 'Belum Menikah' ? 'selected' : '' ?>>Belum Menikah</option>
                                <option value="Menikah" <?= old('status_pernikahan') == 'Menikah' ? 'selected' : '' ?>>Menikah</option>
                                <option value="Janda/Duda" <?= old('status_pernikahan') == 'Janda/Duda' ? 'selected' : '' ?>>Janda/Duda</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Kontak Darurat -->
                <div class="card mb-4" style="border: 1px solid #eaeaea;">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-phone-alt me-2"></i> Kontak Darurat</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Kontak Darurat</label>
                            <input type="text" name="kontak_darurat_nama" class="form-control" 
                                   value="<?= old('kontak_darurat_nama') ?>" placeholder="Nama kontak darurat">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Hubungan</label>
                                <input type="text" name="kontak_darurat_hubungan" class="form-control" 
                                       value="<?= old('kontak_darurat_hubungan') ?>" placeholder="Hubungan (Istri/Suami/Orang Tua/dll)">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telepon</label>
                                <input type="text" name="kontak_darurat_telepon" class="form-control" 
                                       value="<?= old('kontak_darurat_telepon') ?>" placeholder="Nomor telepon darurat">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Kolom Kanan: Data Pekerjaan & Pendidikan -->
            <div class="col-lg-6">
                <!-- Data Pekerjaan -->
                <div class="card mb-4" style="border: 1px solid #eaeaea;">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-briefcase me-2"></i> Data Pekerjaan</h6>
                    </div>
                    <div class="card-body">
                        <!-- Jabatan -->
                        <div class="mb-3">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="jabatan" class="form-control" 
                                   value="<?= old('jabatan') ?>" placeholder="Jabatan">
                        </div>
                        
                        <!-- Departemen & Divisi -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Departemen</label>
                                <input type="text" name="departemen" class="form-control" 
                                       value="<?= old('departemen') ?>" placeholder="Departemen">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Divisi</label>
                                <input type="text" name="divisi" class="form-control" 
                                       value="<?= old('divisi') ?>" placeholder="Divisi">
                            </div>
                        </div>
                        
                        <!-- Tanggal Masuk & Status -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Masuk</label>
                                <input type="date" name="tanggal_masuk" class="form-control" 
                                       value="<?= old('tanggal_masuk') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status Karyawan</label>
                                <select name="status_karyawan" class="form-select">
                                    <option value="Tetap" <?= old('status_karyawan') == 'Tetap' ? 'selected' : '' ?>>Tetap</option>
                                    <option value="Kontrak" <?= old('status_karyawan') == 'Kontrak' ? 'selected' : '' ?>>Kontrak</option>
                                    <option value="Probation" <?= old('status_karyawan') == 'Probation' ? 'selected' : '' ?>>Probation</option>
                                    <option value="Magang" <?= old('status_karyawan') == 'Magang' ? 'selected' : '' ?>>Magang</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Data Administrasi -->
                <div class="card mb-4" style="border: 1px solid #eaeaea;">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-file-invoice me-2"></i> Data Administrasi</h6>
                    </div>
                    <div class="card-body">
                        <!-- NPWP -->
                        <div class="mb-3">
                            <label class="form-label">NPWP</label>
                            <input type="text" name="no_npwp" class="form-control" 
                                   value="<?= old('no_npwp') ?>" placeholder="Nomor NPWP">
                        </div>
                        
                        <!-- BPJS -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">BPJS Kesehatan</label>
                                <input type="text" name="no_bpjs_kes" class="form-control" 
                                       value="<?= old('no_bpjs_kes') ?>" placeholder="Nomor BPJS Kesehatan">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">BPJS Ketenagakerjaan</label>
                                <input type="text" name="no_bpjs_tk" class="form-control" 
                                       value="<?= old('no_bpjs_tk') ?>" placeholder="Nomor BPJS TK">
                            </div>
                        </div>
                        
                        <!-- Rekening Bank -->
                        <div class="mb-3">
                            <label class="form-label">Bank</label>
                            <input type="text" name="bank" class="form-control" 
                                   value="<?= old('bank') ?>" placeholder="Nama Bank">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Rekening</label>
                                <input type="text" name="no_rekening" class="form-control" 
                                       value="<?= old('no_rekening') ?>" placeholder="Nomor rekening">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama di Rekening</label>
                                <input type="text" name="nama_rekening" class="form-control" 
                                       value="<?= old('nama_rekening') ?>" placeholder="Nama pemilik rekening">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Baris Bawah: Pendidikan, Kontak, dan Upload -->
        <div class="row">
            <div class="col-lg-8">
                <!-- Data Pendidikan -->
                <div class="card mb-4" style="border: 1px solid #eaeaea;">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-graduation-cap me-2"></i> Pendidikan Terakhir</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Pendidikan Terakhir</label>
                                <select name="pendidikan_terakhir" class="form-select">
                                    <option value="">Pilih Pendidikan</option>
                                    <option value="SD" <?= old('pendidikan_terakhir') == 'SD' ? 'selected' : '' ?>>SD</option>
                                    <option value="SMP" <?= old('pendidikan_terakhir') == 'SMP' ? 'selected' : '' ?>>SMP</option>
                                    <option value="SMA/SMK" <?= old('pendidikan_terakhir') == 'SMA/SMK' ? 'selected' : '' ?>>SMA/SMK</option>
                                    <option value="D1" <?= old('pendidikan_terakhir') == 'D1' ? 'selected' : '' ?>>D1</option>
                                    <option value="D2" <?= old('pendidikan_terakhir') == 'D2' ? 'selected' : '' ?>>D2</option>
                                    <option value="D3" <?= old('pendidikan_terakhir') == 'D3' ? 'selected' : '' ?>>D3</option>
                                    <option value="D4" <?= old('pendidikan_terakhir') == 'D4' ? 'selected' : '' ?>>D4</option>
                                    <option value="S1" <?= old('pendidikan_terakhir') == 'S1' ? 'selected' : '' ?>>S1</option>
                                    <option value="S2" <?= old('pendidikan_terakhir') == 'S2' ? 'selected' : '' ?>>S2</option>
                                    <option value="S3" <?= old('pendidikan_terakhir') == 'S3' ? 'selected' : '' ?>>S3</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Jurusan</label>
                                <input type="text" name="jurusan" class="form-control" 
                                       value="<?= old('jurusan') ?>" placeholder="Jurusan">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Institusi</label>
                                <input type="text" name="institusi" class="form-control" 
                                       value="<?= old('institusi') ?>" placeholder="Nama sekolah/perguruan tinggi">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tahun Lulus</label>
                                <input type="number" name="tahun_lulus" class="form-control" 
                                       value="<?= old('tahun_lulus') ?>" placeholder="Tahun lulus" min="1950" max="2030">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Data Kontak -->
                <div class="card mb-4" style="border: 1px solid #eaeaea;">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-address-book me-2"></i> Kontak & Alamat</h6>
                    </div>
                    <div class="card-body">
                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>" 
                                   value="<?= old('email') ?>" placeholder="email@contoh.com">
                            <?php if (session('errors.email')): ?>
                                <div class="invalid-feedback"><?= session('errors.email') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Telepon -->
                        <div class="mb-3">
                            <label class="form-label">Telepon</label>
                            <input type="text" name="telepon" class="form-control" 
                                   value="<?= old('telepon') ?>" placeholder="Nomor telepon">
                        </div>
                        
                        <!-- Alamat -->
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3" 
                                      placeholder="Alamat lengkap"><?= old('alamat') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Upload File -->
                <div class="card mb-4" style="border: 1px solid #eaeaea;">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-upload me-2"></i> Upload File</h6>
                    </div>
                    <div class="card-body">
                        <!-- Foto -->
                        <div class="mb-4">
                            <label class="form-label">Foto Karyawan</label>
                            <div class="border rounded p-3 text-center mb-2" style="background-color: #f8f9fa;">
                                <i class="fas fa-user-circle fa-3x text-muted mb-2"></i>
                                <p class="text-muted small mb-0">Upload foto karyawan</p>
                                <p class="text-muted small">(Max: 2MB, Format: JPG/PNG)</p>
                            </div>
                            <input type="file" name="foto" class="form-control" accept="image/*">
                        </div>
                        
                        <!-- CV -->
                        <div class="mb-3">
                            <label class="form-label">CV/Resume</label>
                            <div class="border rounded p-3 text-center mb-2" style="background-color: #f8f9fa;">
                                <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                                <p class="text-muted small mb-0">Upload CV atau Resume</p>
                                <p class="text-muted small">(Format: PDF/DOC/DOCX)</p>
                            </div>
                            <input type="file" name="cv" class="form-control" accept=".pdf,.doc,.docx">
                        </div>
                    </div>
                </div>
                
                <!-- Tombol Simpan -->
                <div class="card" style="border: 1px solid #eaeaea;">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i> Simpan Data
                            </button>
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-2"></i> Reset Form
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // Set tanggal masuk default ke hari ini
    document.addEventListener('DOMContentLoaded', function() {
        var today = new Date().toISOString().split('T')[0];
        if (!document.querySelector('input[name="tanggal_masuk"]').value) {
            document.querySelector('input[name="tanggal_masuk"]').value = today;
        }
        
        // Preview foto sebelum upload
        const fotoInput = document.querySelector('input[name="foto"]');
        const fotoPreview = document.querySelector('.fa-user-circle').closest('div');
        
        fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '100%';
                    img.style.height = '150px';
                    img.style.objectFit = 'cover';
                    img.style.borderRadius = '5px';
                    
                    // Hapus icon dan teks
                    fotoPreview.innerHTML = '';
                    fotoPreview.appendChild(img);
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Validasi NIK harus angka
        const nikInput = document.querySelector('input[name="nik"]');
        nikInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '');
        });
        
        // Validasi telepon harus angka
        const teleponInput = document.querySelector('input[name="telepon"]');
        const teleponDaruratInput = document.querySelector('input[name="kontak_darurat_telepon"]');
        
        [teleponInput, teleponDaruratInput].forEach(input => {
            if (input) {
                input.addEventListener('input', function(e) {
                    this.value = this.value.replace(/\D/g, '');
                });
            }
        });
    });
</script>

<?= $this->include('admin/templates/footer') ?>