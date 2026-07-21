<?php
$title = 'Upload Dokumen Baru';
$active = 'dokumen';
$is_dokumen_page = true;
$css = [];
$scripts = [];
?>

<?= $this->include('admin/templates/header') ?>
<?= $this->include('admin/templates/sidebar') ?>
<?= $this->include('admin/templates/navbar') ?>

<!-- Main Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Upload Dokumen Baru</h1>
        <a href="<?= base_url('admin/karyawan/dokumen'); ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Form Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Upload Dokumen</h6>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/karyawan/dokumen/store'); ?>" method="post" enctype="multipart/form-data" id="uploadForm">
                        <?= csrf_field(); ?>
                        
                        <!-- Karyawan -->
                        <div class="form-group mb-3">
                            <label for="karyawan_id" class="form-label">Pilih Karyawan *</label>
                            <select class="form-control <?= session('errors.karyawan_id') ? 'is-invalid' : '' ?>" 
                                    id="karyawan_id" name="karyawan_id" required>
                                <option value="">-- Pilih Karyawan --</option>
                                <?php if(isset($karyawanList) && !empty($karyawanList)): ?>
                                    <?php foreach($karyawanList as $karyawan): ?>
                                        <option value="<?= $karyawan['id'] ?>" <?= old('karyawan_id') == $karyawan['id'] ? 'selected' : '' ?>>
                                            <?= esc($karyawan['nik']) ?> - <?= esc($karyawan['nama_lengkap']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php if(session('errors.karyawan_id')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.karyawan_id') ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Jenis Dokumen -->
                        <div class="form-group mb-3">
                            <label for="jenis" class="form-label">Jenis Dokumen *</label>
                            <select class="form-control <?= session('errors.jenis') ? 'is-invalid' : '' ?>" 
                                    id="jenis" name="jenis" required>
                                <option value="">-- Pilih Jenis Dokumen --</option>
                                <?php if(isset($jenisOptions) && !empty($jenisOptions)): ?>
                                    <?php foreach($jenisOptions as $key => $value): ?>
                                        <option value="<?= $key ?>" <?= old('jenis') == $key ? 'selected' : '' ?>>
                                            <?= $value ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php if(session('errors.jenis')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.jenis') ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Nomor Dokumen -->
                        <div class="form-group mb-3">
                            <label for="nomor_dokumen" class="form-label">Nomor Dokumen</label>
                            <input type="text" class="form-control <?= session('errors.nomor_dokumen') ? 'is-invalid' : '' ?>" 
                                   id="nomor_dokumen" name="nomor_dokumen" 
                                   value="<?= old('nomor_dokumen') ?>" 
                                   placeholder="Contoh: 1234567890123">
                            <?php if(session('errors.nomor_dokumen')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.nomor_dokumen') ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Tanggal Berlaku & Kadaluarsa -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_berlaku" class="form-label">Tanggal Berlaku</label>
                                    <input type="date" class="form-control <?= session('errors.tanggal_berlaku') ? 'is-invalid' : '' ?>" 
                                           id="tanggal_berlaku" name="tanggal_berlaku" 
                                           value="<?= old('tanggal_berlaku') ?>">
                                    <?php if(session('errors.tanggal_berlaku')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.tanggal_berlaku') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_kadaluarsa" class="form-label">Tanggal Kadaluarsa</label>
                                    <input type="date" class="form-control <?= session('errors.tanggal_kadaluarsa') ? 'is-invalid' : '' ?>" 
                                           id="tanggal_kadaluarsa" name="tanggal_kadaluarsa" 
                                           value="<?= old('tanggal_kadaluarsa') ?>">
                                    <?php if(session('errors.tanggal_kadaluarsa')): ?>
                                        <div class="invalid-feedback">
                                            <?= session('errors.tanggal_kadaluarsa') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- File Upload -->
                        <div class="form-group mb-3">
                            <label for="file_dokumen" class="form-label">File Dokumen *</label>
                            <input type="file" class="form-control <?= session('errors.file_dokumen') ? 'is-invalid' : '' ?>" 
                                   id="file_dokumen" name="file_dokumen" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
                            <small class="form-text text-muted">
                                Format yang diperbolehkan: PDF, JPG, JPEG, PNG, DOC, DOCX. Maksimal ukuran: 5MB.
                            </small>
                            <?php if(session('errors.file_dokumen')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.file_dokumen') ?>
                                </div>
                            <?php endif; ?>
                            <div id="filePreview" class="mt-2"></div>
                        </div>

                        <!-- Keterangan -->
                        <div class="form-group mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control <?= session('errors.keterangan') ? 'is-invalid' : '' ?>" 
                                      id="keterangan" name="keterangan" 
                                      rows="3" placeholder="Masukkan keterangan tambahan..."><?= old('keterangan') ?></textarea>
                            <?php if(session('errors.keterangan')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.keterangan') ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Status -->
                        <div class="form-group mb-4">
                            <label for="status" class="form-label">Status Dokumen</label>
                            <select class="form-control <?= session('errors.status') ? 'is-invalid' : '' ?>" 
                                    id="status" name="status">
                                <?php if(isset($statusOptions) && !empty($statusOptions)): ?>
                                    <?php foreach($statusOptions as $key => $value): ?>
                                        <option value="<?= $key ?>" <?= old('status', 'pending') == $key ? 'selected' : '' ?>>
                                            <?= $value ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php if(session('errors.status')): ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.status') ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('admin/karyawan/dokumen'); ?>" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Dokumen
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Informasi -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-info-circle me-2"></i>Informasi</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Pastikan file yang diupload jelas dan terbaca
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Untuk dokumen resmi (KTP, SIM, dll), pastikan nomor dokumen sesuai
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Setiap jenis dokumen hanya bisa diupload satu kali per karyawan
                        </li>
                        <li>
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Dokumen akan diverifikasi oleh admin sebelum dapat digunakan
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Preview file sebelum upload
        $('#file_dokumen').change(function() {
            var file = this.files[0];
            var preview = $('#filePreview');
            preview.empty();
            
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var fileType = file.type;
                    var fileName = file.name;
                    var fileSize = (file.size / 1024 / 1024).toFixed(2); // MB
                    
                    var previewHtml = `
                        <div class="alert alert-info">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-file fa-2x"></i>
                                </div>
                                <div>
                                    <strong>${fileName}</strong><br>
                                    <small>Tipe: ${fileType} | Ukuran: ${fileSize} MB</small>
                                </div>
                            </div>
                        </div>
                    `;
                    preview.html(previewHtml);
                }
                reader.readAsDataURL(file);
            }
        });

        // Validasi form
        $('#uploadForm').submit(function(e) {
            var fileInput = $('#file_dokumen')[0];
            var maxSize = 5 * 1024 * 1024; // 5MB
            
            if (fileInput.files.length > 0) {
                var fileSize = fileInput.files[0].size;
                if (fileSize > maxSize) {
                    e.preventDefault();
                    alert('Ukuran file terlalu besar. Maksimal 5MB.');
                    return false;
                }
            }
            
            // Validasi tanggal
            var berlaku = $('#tanggal_berlaku').val();
            var kadaluarsa = $('#tanggal_kadaluarsa').val();
            
            if (berlaku && kadaluarsa) {
                var dateBerlaku = new Date(berlaku);
                var dateKadaluarsa = new Date(kadaluarsa);
                
                if (dateKadaluarsa < dateBerlaku) {
                    e.preventDefault();
                    alert('Tanggal kadaluarsa tidak boleh lebih awal dari tanggal berlaku.');
                    return false;
                }
            }
            
            return true;
        });

        // Set tanggal minimal untuk kadaluarsa berdasarkan berlaku
        $('#tanggal_berlaku').change(function() {
            var berlaku = $(this).val();
            if (berlaku) {
                $('#tanggal_kadaluarsa').attr('min', berlaku);
            }
        });

        // Auto-hide alerts
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    });
</script>

<?= $this->include('admin/templates/footer') ?>