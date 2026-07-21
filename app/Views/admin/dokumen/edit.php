<?php
$title = 'Edit Dokumen';
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
        <h1 class="h3 mb-0 text-gray-800">Edit Dokumen</h1>
        <a href="<?= base_url('admin/karyawan/dokumen'); ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Form Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Form Edit Dokumen</h6>
                    <?php if(isset($dokumen) && !empty($dokumen)): ?>
                        <span class="badge bg-info">
                            <?= isset($jenisOptions[$dokumen['jenis']]) ? $jenisOptions[$dokumen['jenis']] : $dokumen['jenis']; ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if(isset($dokumen) && !empty($dokumen)): ?>
                        <!-- PENTING: Tambahkan enctype="multipart/form-data" untuk upload file -->
                        <form action="<?= base_url('admin/karyawan/dokumen/update/' . $dokumen['id']); ?>" 
                              method="post" 
                              id="editForm" 
                              enctype="multipart/form-data">
                            <?= csrf_field(); ?>
                            
                            <!-- Informasi Dokumen -->
                            <div class="alert alert-info mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-file-alt fa-2x"></i>
                                    </div>
                                    <div>
                                        <strong>Informasi Dokumen</strong><br>
                                        <small>
                                            File: <?= esc($dokumen['nama_file']) ?> | 
                                            Diupload: <?= date('d/m/Y H:i', strtotime($dokumen['created_at'])) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Karyawan (Read-only) -->
                            <div class="form-group mb-3">
                                <label class="form-label">Karyawan</label>
                                <input type="text" class="form-control bg-light" 
                                       value="<?= isset($dokumen['nama_lengkap']) ? esc($dokumen['nama_lengkap']) : 'Tidak diketahui' ?>" 
                                       readonly>
                                <small class="form-text text-muted">Tidak dapat mengganti karyawan. Buat dokumen baru jika perlu.</small>
                            </div>

                            <!-- Jenis Dokumen (Read-only) -->
                            <div class="form-group mb-3">
                                <label class="form-label">Jenis Dokumen</label>
                                <input type="text" class="form-control bg-light" 
                                       value="<?= isset($jenisOptions[$dokumen['jenis']]) ? $jenisOptions[$dokumen['jenis']] : $dokumen['jenis'] ?>" 
                                       readonly>
                            </div>

                            <!-- Nomor Dokumen -->
                            <div class="form-group mb-3">
                                <label for="nomor_dokumen" class="form-label">Nomor Dokumen *</label>
                                <input type="text" class="form-control <?= session('errors.nomor_dokumen') ? 'is-invalid' : '' ?>" 
                                       id="nomor_dokumen" name="nomor_dokumen" 
                                       value="<?= old('nomor_dokumen', $dokumen['nomor_dokumen'] ?? '') ?>" 
                                       required placeholder="Contoh: 1234567890123">
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
                                               value="<?= old('tanggal_berlaku', $dokumen['tanggal_berlaku'] ?? '') ?>">
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
                                               value="<?= old('tanggal_kadaluarsa', $dokumen['tanggal_kadaluarsa'] ?? '') ?>">
                                        <?php if(session('errors.tanggal_kadaluarsa')): ?>
                                            <div class="invalid-feedback">
                                                <?= session('errors.tanggal_kadaluarsa') ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Upload File Baru (Opsional) -->
                            <div class="form-group mb-3">
                                <label for="file_dokumen" class="form-label">Ubah File Dokumen (Opsional)</label>
                                <input type="file" class="form-control <?= session('errors.file_dokumen') ? 'is-invalid' : '' ?>" 
                                       id="file_dokumen" name="file_dokumen" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                <small class="form-text text-muted">
                                    Kosongkan jika tidak ingin mengubah file. Format yang diperbolehkan: PDF, JPG, JPEG, PNG, DOC, DOCX. Maksimal ukuran: 5MB.
                                </small>
                                <?php if(session('errors.file_dokumen')): ?>
                                    <div class="invalid-feedback">
                                        <?= session('errors.file_dokumen') ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Preview File Saat Ini -->
                                <div class="mt-3 p-3 bg-light border rounded">
                                    <strong>File Saat Ini:</strong><br>
                                    <div class="d-flex align-items-center mt-2">
                                        <i class="fas fa-file fa-2x text-primary me-3"></i>
                                        <div>
                                            <a href="<?= base_url('admin/karyawan/dokumen/download/' . $dokumen['id']) ?>" 
                                               target="_blank" class="text-decoration-none">
                                                <?= esc($dokumen['nama_file']) ?>
                                            </a>
                                            <br>
                                            <small class="text-muted">
                                                Ukuran: <?= number_format($dokumen['ukuran'] / 1024, 2) ?> KB
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Preview File Baru (akan muncul setelah pilih file) -->
                                <div id="newFilePreview"></div>
                            </div>

                            <!-- Keterangan -->
                            <div class="form-group mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea class="form-control <?= session('errors.keterangan') ? 'is-invalid' : '' ?>" 
                                          id="keterangan" name="keterangan" 
                                          rows="3" placeholder="Masukkan keterangan tambahan..."><?= old('keterangan', $dokumen['keterangan'] ?? '') ?></textarea>
                                <?php if(session('errors.keterangan')): ?>
                                    <div class="invalid-feedback">
                                        <?= session('errors.keterangan') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Status -->
                            <div class="form-group mb-4">
                                <label for="status" class="form-label">Status Dokumen *</label>
                                <select class="form-control <?= session('errors.status') ? 'is-invalid' : '' ?>" 
                                        id="status" name="status" required>
                                    <?php if(isset($statusOptions) && !empty($statusOptions)): ?>
                                        <?php foreach($statusOptions as $key => $value): ?>
                                            <option value="<?= $key ?>" <?= old('status', $dokumen['status'] ?? 'pending') == $key ? 'selected' : '' ?>>
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
                                <div>
                                    <a href="<?= base_url('admin/karyawan/dokumen'); ?>" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i> Batal
                                    </a>
                                    <a href="<?= base_url('admin/karyawan/dokumen/show/' . $dokumen['id']); ?>" class="btn btn-info">
                                        <i class="fas fa-eye me-1"></i> Lihat
                                    </a>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="fas fa-save me-1"></i> Update Dokumen
                                    </button>
                                </div>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Data dokumen tidak ditemukan.
                        </div>
                        <a href="<?= base_url('admin/karyawan/dokumen'); ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Informasi Edit -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-exclamation-circle me-2"></i>Catatan Edit</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            <strong>Karyawan dan Jenis Dokumen tidak dapat diubah</strong> untuk menjaga konsistensi data
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Untuk mengubah karyawan atau jenis dokumen, harap hapus dokumen ini dan buat yang baru
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Ubah status dokumen sesuai dengan verifikasi yang dilakukan
                        </li>
                        <li>
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Pastikan informasi nomor dokumen dan tanggal berlaku sudah benar
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Preview file baru sebelum upload
        $('#file_dokumen').change(function() {
            var file = this.files[0];
            var previewDiv = $('#newFilePreview');
            
            if (file) {
                var fileSize = (file.size / 1024 / 1024).toFixed(2); // MB
                var fileName = file.name;
                var fileType = file.type;
                
                // Validasi ukuran
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar! Maksimal 5MB.');
                    $(this).val('');
                    previewDiv.empty();
                    return;
                }
                
                // Validasi tipe file
                var allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png', 
                                   'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                if (!allowedTypes.includes(fileType)) {
                    alert('Format file tidak didukung! Gunakan PDF, JPG, PNG, DOC, atau DOCX.');
                    $(this).val('');
                    previewDiv.empty();
                    return;
                }
                
                var previewHtml = `
                    <div class="mt-3 p-3 bg-warning bg-opacity-10 border border-warning rounded">
                        <strong><i class="fas fa-exclamation-triangle text-warning"></i> File Baru (akan menggantikan file lama):</strong><br>
                        <div class="d-flex align-items-center mt-2">
                            <i class="fas fa-file-upload fa-2x text-warning me-3"></i>
                            <div>
                                <strong>${fileName}</strong><br>
                                <small class="text-muted">Ukuran: ${fileSize} MB</small>
                            </div>
                        </div>
                    </div>
                `;
                previewDiv.html(previewHtml);
            } else {
                previewDiv.empty();
            }
        });

        // Validasi form sebelum submit
        $('#editForm').submit(function(e) {
            var fileInput = $('#file_dokumen')[0];
            var maxSize = 5 * 1024 * 1024; // 5MB
            
            // Validasi file jika ada
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
            
            // Tampilkan loading
            $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');
            
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
            $('.alert-success, .alert-info').fadeOut('slow');
        }, 5000);
    });
</script>

<?= $this->include('admin/templates/footer') ?>