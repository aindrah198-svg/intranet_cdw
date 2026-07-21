<?php
$title = 'Edit Karyawan';
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
                <i class="fas fa-edit me-2"></i>Edit Data Karyawan
            </h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/karyawan') ?>">Karyawan</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/karyawan/show/' . $karyawan['id']) ?>">Detail</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('admin/karyawan/show/' . $karyawan['id']) ?>" class="btn btn-outline-secondary me-2">
                <i class="fas fa-eye me-1"></i> Lihat Detail
            </a>
            <a href="<?= base_url('admin/karyawan') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
    
    <form action="<?= base_url('admin/karyawan/update/' . $karyawan['id']) ?>" method="post" enctype="multipart/form-data">
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
                                   value="<?= old('nik', $karyawan['nik']) ?>" required>
                            <?php if (session('errors.nik')): ?>
                                <div class="invalid-feedback"><?= session('errors.nik') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Nama Lengkap -->
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control <?= session('errors.nama_lengkap') ? 'is-invalid' : '' ?>" 
                                   value="<?= old('nama_lengkap', $karyawan['nama_lengkap']) ?>" required>
                        </div>
                        
                        <!-- Nama Panggilan -->
                        <div class="mb-3">
                            <label class="form-label">Nama Panggilan</label>
                            <input type="text" name="nama_panggilan" class="form-control" 
                                   value="<?= old('nama_panggilan', $karyawan['nama_panggilan']) ?>">
                        </div>
                        
                        <!-- Jenis Kelamin -->
                        <div class="mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="L" <?= old('jenis_kelamin', $karyawan['jenis_kelamin']) == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="P" <?= old('jenis_kelamin', $karyawan['jenis_kelamin']) == 'P' ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                        </div>
                        
                        <!-- Tempat & Tanggal Lahir -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control" 
                                       value="<?= old('tempat_lahir', $karyawan['tempat_lahir']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control" 
                                       value="<?= old('tanggal_lahir', $karyawan['tanggal_lahir']) ?>">
                            </div>
                        </div>
                        
                        <!-- Agama -->
                        <div class="mb-3">
                            <label class="form-label">Agama</label>
                            <select name="agama" class="form-select">
                                <option value="">Pilih Agama</option>
                                <option value="Islam" <?= old('agama', $karyawan['agama']) == 'Islam' ? 'selected' : '' ?>>Islam</option>
                                <option value="Kristen" <?= old('agama', $karyawan['agama']) == 'Kristen' ? 'selected' : '' ?>>Kristen</option>
                                <option value="Katolik" <?= old('agama', $karyawan['agama']) == 'Katolik' ? 'selected' : '' ?>>Katolik</option>
                                <option value="Hindu" <?= old('agama', $karyawan['agama']) == 'Hindu' ? 'selected' : '' ?>>Hindu</option>
                                <option value="Buddha" <?= old('agama', $karyawan['agama']) == 'Buddha' ? 'selected' : '' ?>>Buddha</option>
                                <option value="Konghucu" <?= old('agama', $karyawan['agama']) == 'Konghucu' ? 'selected' : '' ?>>Konghucu</option>
                            </select>
                        </div>
                        
                        <!-- Status Pernikahan -->
                        <div class="mb-3">
                            <label class="form-label">Status Pernikahan</label>
                            <select name="status_pernikahan" class="form-select">
                                <option value="Belum Menikah" <?= old('status_pernikahan', $karyawan['status_pernikahan']) == 'Belum Menikah' ? 'selected' : '' ?>>Belum Menikah</option>
                                <option value="Menikah" <?= old('status_pernikahan', $karyawan['status_pernikahan']) == 'Menikah' ? 'selected' : '' ?>>Menikah</option>
                                <option value="Janda/Duda" <?= old('status_pernikahan', $karyawan['status_pernikahan']) == 'Janda/Duda' ? 'selected' : '' ?>>Janda/Duda</option>
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
                                   value="<?= old('kontak_darurat_nama', $karyawan['kontak_darurat_nama']) ?>">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Hubungan</label>
                                <input type="text" name="kontak_darurat_hubungan" class="form-control" 
                                       value="<?= old('kontak_darurat_hubungan', $karyawan['kontak_darurat_hubungan']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telepon</label>
                                <input type="text" name="kontak_darurat_telepon" class="form-control" 
                                       value="<?= old('kontak_darurat_telepon', $karyawan['kontak_darurat_telepon']) ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Kolom Kanan: Data Pekerjaan & Administrasi -->
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
                                   value="<?= old('jabatan', $karyawan['jabatan']) ?>">
                        </div>
                        
                        <!-- Departemen & Divisi -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Departemen</label>
                                <input type="text" name="departemen" class="form-control" 
                                       value="<?= old('departemen', $karyawan['departemen']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Divisi</label>
                                <input type="text" name="divisi" class="form-control" 
                                       value="<?= old('divisi', $karyawan['divisi']) ?>">
                            </div>
                        </div>
                        
                        <!-- Tanggal Masuk & Status -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Masuk</label>
                                <input type="date" name="tanggal_masuk" class="form-control" 
                                       value="<?= old('tanggal_masuk', $karyawan['tanggal_masuk']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status Karyawan</label>
                                <select name="status_karyawan" class="form-select">
                                    <option value="Tetap" <?= old('status_karyawan', $karyawan['status_karyawan']) == 'Tetap' ? 'selected' : '' ?>>Tetap</option>
                                    <option value="Kontrak" <?= old('status_karyawan', $karyawan['status_karyawan']) == 'Kontrak' ? 'selected' : '' ?>>Kontrak</option>
                                    <option value="Probation" <?= old('status_karyawan', $karyawan['status_karyawan']) == 'Probation' ? 'selected' : '' ?>>Probation</option>
                                    <option value="Magang" <?= old('status_karyawan', $karyawan['status_karyawan']) == 'Magang' ? 'selected' : '' ?>>Magang</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Tanggal Keluar jika sudah keluar -->
                        <?php if (!empty($karyawan['tanggal_keluar'])): ?>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Keluar</label>
                                    <input type="date" name="tanggal_keluar" class="form-control" 
                                           value="<?= old('tanggal_keluar', $karyawan['tanggal_keluar']) ?>" readonly>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alasan Keluar</label>
                                <textarea name="alasan_keluar" class="form-control" rows="2"><?= old('alasan_keluar', $karyawan['alasan_keluar']) ?></textarea>
                            </div>
                        <?php endif; ?>
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
                                   value="<?= old('no_npwp', $karyawan['no_npwp']) ?>">
                        </div>
                        
                        <!-- BPJS -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">BPJS Kesehatan</label>
                                <input type="text" name="no_bpjs_kes" class="form-control" 
                                       value="<?= old('no_bpjs_kes', $karyawan['no_bpjs_kes']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">BPJS Ketenagakerjaan</label>
                                <input type="text" name="no_bpjs_tk" class="form-control" 
                                       value="<?= old('no_bpjs_tk', $karyawan['no_bpjs_tk']) ?>">
                            </div>
                        </div>
                        
                        <!-- Rekening Bank -->
                        <div class="mb-3">
                            <label class="form-label">Bank</label>
                            <input type="text" name="bank" class="form-control" 
                                   value="<?= old('bank', $karyawan['bank']) ?>">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Rekening</label>
                                <input type="text" name="no_rekening" class="form-control" 
                                       value="<?= old('no_rekening', $karyawan['no_rekening']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama di Rekening</label>
                                <input type="text" name="nama_rekening" class="form-control" 
                                       value="<?= old('nama_rekening', $karyawan['nama_rekening']) ?>">
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
                                    <option value="SD" <?= old('pendidikan_terakhir', $karyawan['pendidikan_terakhir']) == 'SD' ? 'selected' : '' ?>>SD</option>
                                    <option value="SMP" <?= old('pendidikan_terakhir', $karyawan['pendidikan_terakhir']) == 'SMP' ? 'selected' : '' ?>>SMP</option>
                                    <option value="SMA/SMK" <?= old('pendidikan_terakhir', $karyawan['pendidikan_terakhir']) == 'SMA/SMK' ? 'selected' : '' ?>>SMA/SMK</option>
                                    <option value="D1" <?= old('pendidikan_terakhir', $karyawan['pendidikan_terakhir']) == 'D1' ? 'selected' : '' ?>>D1</option>
                                    <option value="D2" <?= old('pendidikan_terakhir', $karyawan['pendidikan_terakhir']) == 'D2' ? 'selected' : '' ?>>D2</option>
                                    <option value="D3" <?= old('pendidikan_terakhir', $karyawan['pendidikan_terakhir']) == 'D3' ? 'selected' : '' ?>>D3</option>
                                    <option value="D4" <?= old('pendidikan_terakhir', $karyawan['pendidikan_terakhir']) == 'D4' ? 'selected' : '' ?>>D4</option>
                                    <option value="S1" <?= old('pendidikan_terakhir', $karyawan['pendidikan_terakhir']) == 'S1' ? 'selected' : '' ?>>S1</option>
                                    <option value="S2" <?= old('pendidikan_terakhir', $karyawan['pendidikan_terakhir']) == 'S2' ? 'selected' : '' ?>>S2</option>
                                    <option value="S3" <?= old('pendidikan_terakhir', $karyawan['pendidikan_terakhir']) == 'S3' ? 'selected' : '' ?>>S3</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Jurusan</label>
                                <input type="text" name="jurusan" class="form-control" 
                                       value="<?= old('jurusan', $karyawan['jurusan']) ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Institusi</label>
                                <input type="text" name="institusi" class="form-control" 
                                       value="<?= old('institusi', $karyawan['institusi']) ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tahun Lulus</label>
                                <input type="number" name="tahun_lulus" class="form-control" 
                                       value="<?= old('tahun_lulus', $karyawan['tahun_lulus']) ?>" min="1950" max="2030">
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
                                   value="<?= old('email', $karyawan['email']) ?>">
                        </div>
                        
                        <!-- Telepon -->
                        <div class="mb-3">
                            <label class="form-label">Telepon</label>
                            <input type="text" name="telepon" class="form-control" 
                                   value="<?= old('telepon', $karyawan['telepon']) ?>">
                        </div>
                        
                        <!-- Alamat -->
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3"><?= old('alamat', $karyawan['alamat']) ?></textarea>
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
                                <?php if (!empty($karyawan['foto'])): ?>
                                    <img src="<?= base_url($karyawan['foto']) ?>" alt="Foto" 
                                         style="width: 100%; height: 150px; object-fit: cover; border-radius: 5px;">
                                    <p class="text-muted small mt-2 mb-0">Foto saat ini</p>
                                <?php else: ?>
                                    <i class="fas fa-user-circle fa-3x text-muted mb-2"></i>
                                    <p class="text-muted small mb-0">Belum ada foto</p>
                                <?php endif; ?>
                                <p class="text-muted small">(Max: 2MB, Format: JPG/PNG)</p>
                            </div>
                            <input type="file" name="foto" class="form-control" accept="image/*">
                            <?php if (!empty($karyawan['foto'])): ?>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="hapus_foto" value="1" id="hapusFoto">
                                    <label class="form-check-label text-danger" for="hapusFoto">
                                        Hapus foto saat ini
                                    </label>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- CV -->
                        <div class="mb-3">
                            <label class="form-label">CV/Resume</label>
                            <div class="border rounded p-3 text-center mb-2" style="background-color: #f8f9fa;">
                                <?php if (!empty($karyawan['cv_path'])): ?>
                                    <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                                    <p class="text-muted small mb-0">CV sudah diupload</p>
                                    <a href="<?= base_url($karyawan['cv_path']) ?>" target="_blank" class="small">Lihat CV</a>
                                <?php else: ?>
                                    <i class="fas fa-file-pdf fa-3x text-muted mb-2"></i>
                                    <p class="text-muted small mb-0">Belum ada CV</p>
                                <?php endif; ?>
                                <p class="text-muted small">(Format: PDF/DOC/DOCX)</p>
                            </div>
                            <input type="file" name="cv" class="form-control" accept=".pdf,.doc,.docx">
                            <?php if (!empty($karyawan['cv_path'])): ?>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="hapus_cv" value="1" id="hapusCV">
                                    <label class="form-check-label text-danger" for="hapusCV">
                                        Hapus CV saat ini
                                    </label>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Info Update -->
                <div class="card mb-4" style="border: 1px solid #eaeaea;">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i> Informasi</h6>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-1">
                            <i class="fas fa-calendar-plus me-1"></i>
                            Dibuat: <?= date('d/m/Y H:i', strtotime($karyawan['created_at'])) ?>
                        </p>
                        <p class="small text-muted mb-0">
                            <i class="fas fa-calendar-edit me-1"></i>
                            Terakhir diupdate: <?= date('d/m/Y H:i', strtotime($karyawan['updated_at'])) ?>
                        </p>
                    </div>
                </div>
                
                <!-- Tombol Simpan -->
                <div class="card" style="border: 1px solid #eaeaea;">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i> Update Data
                            </button>
                            <a href="<?= base_url('admin/karyawan/show/' . $karyawan['id']) ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i> Batal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Preview foto baru sebelum upload
        const fotoInput = document.querySelector('input[name="foto"]');
        if (fotoInput) {
            fotoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const container = this.closest('.mb-4').querySelector('.border.rounded');
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.width = '100%';
                        img.style.height = '150px';
                        img.style.objectFit = 'cover';
                        img.style.borderRadius = '5px';
                        
                        // Clear container and add new image
                        container.innerHTML = '';
                        container.appendChild(img);
                        
                        // Add text
                        const text = document.createElement('p');
                        text.className = 'text-muted small mt-2 mb-0';
                        text.textContent = 'Foto baru';
                        container.appendChild(text);
                    }.bind(this);
                    reader.readAsDataURL(file);
                }
            });
        }
        
        // Format date inputs
        const dateFields = ['tanggal_lahir', 'tanggal_masuk', 'tanggal_keluar'];
        dateFields.forEach(field => {
            const input = document.querySelector(`input[name="${field}"]`);
            if (input && input.value) {
                const date = new Date(input.value);
                input.value = date.toISOString().split('T')[0];
            }
        });
        
        // Validasi NIK harus angka
        const nikInput = document.querySelector('input[name="nik"]');
        if (nikInput) {
            nikInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/\D/g, '');
            });
        }
    });
</script>

<?= $this->include('admin/templates/footer') ?>