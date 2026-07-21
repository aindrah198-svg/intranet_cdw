<?= $this->include('teknisi/templates/header') ?>
<?= $this->include('teknisi/templates/sidebar') ?>
<?= $this->include('teknisi/templates/navbar') ?>

<!-- Konten utama -->
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="mb-1"><?= esc($title ?? 'Tambah Client Baru') ?></h4>
            <p class="text-muted mb-0"><?= esc($subtitle ?? 'Isi form untuk menambahkan client baru') ?></p>
        </div>
        <div>
            <a href="<?= base_url('teknisi/tugas-proyek/tambah-client') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Form Tambah Client -->
    <div class="dashboard-card">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="fas fa-building me-2 text-primary"></i>Form Tambah Client</h5>
        </div>
        <div class="card-body">
            <form action="<?= base_url('teknisi/tugas-proyek/tambah-client/store') ?>" method="post" id="formTambahClient">
                <?= csrf_field() ?>
                
                <!-- Alert untuk error validasi -->
                <?php if(session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Terdapat kesalahan:</h6>
                        <ul class="mb-0">
                            <?php foreach(session()->getFlashdata('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <!-- Kolom Kiri -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="kode_client" class="form-label">Kode Client <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                <input type="text" 
                                       class="form-control <?= (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['kode_client'])) ? 'is-invalid' : '' ?>" 
                                       id="kode_client" 
                                       name="kode_client" 
                                       value="<?= old('kode_client', $kode_client ?? '') ?>" 
                                       readonly
                                       placeholder="Otomatis">
                            </div>
                            <small class="text-muted">Kode client akan digenerate otomatis</small>
                            <?php if(session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['kode_client'])): ?>
                                <div class="invalid-feedback">
                                    <?= session()->getFlashdata('errors')['kode_client'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="nama_perusahaan" class="form-label">Nama Perusahaan/Client <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-building"></i></span>
                                <input type="text" 
                                       class="form-control <?= (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['nama_perusahaan'])) ? 'is-invalid' : '' ?>" 
                                       id="nama_perusahaan" 
                                       name="nama_perusahaan" 
                                       value="<?= old('nama_perusahaan') ?>" 
                                       placeholder="Masukkan nama perusahaan atau client"
                                       required>
                            </div>
                            <?php if(session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['nama_perusahaan'])): ?>
                                <div class="invalid-feedback">
                                    <?= session()->getFlashdata('errors')['nama_perusahaan'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="nama_kontak" class="form-label">Nama Kontak Person</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" 
                                       class="form-control" 
                                       id="nama_kontak" 
                                       name="nama_kontak" 
                                       value="<?= old('nama_kontak') ?>" 
                                       placeholder="Masukkan nama kontak person">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email_client" class="form-label">Email Client</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" 
                                       class="form-control <?= (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['email_client'])) ? 'is-invalid' : '' ?>" 
                                       id="email_client" 
                                       name="email_client" 
                                       value="<?= old('email_client') ?>" 
                                       placeholder="contoh@email.com">
                            </div>
                            <?php if(session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['email_client'])): ?>
                                <div class="invalid-feedback">
                                    <?= session()->getFlashdata('errors')['email_client'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="telepon" class="form-label">Nomor Telepon</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="text" 
                                       class="form-control" 
                                       id="telepon" 
                                       name="telepon" 
                                       value="<?= old('telepon') ?>" 
                                       placeholder="081234567890">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="client_kontak" class="form-label">Kontak Alternatif</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-address-book"></i></span>
                                <input type="text" 
                                       class="form-control" 
                                       id="client_kontak" 
                                       name="client_kontak" 
                                       value="<?= old('client_kontak') ?>" 
                                       placeholder="Kontak alternatif (jika ada)">
                            </div>
                            <small class="text-muted">Bisa berupa nomor WA, Telegram, dll</small>
                        </div>
                    </div>

                    <!-- Kolom Kanan -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                <textarea class="form-control" 
                                          id="alamat" 
                                          name="alamat" 
                                          rows="2" 
                                          placeholder="Masukkan alamat lengkap"><?= old('alamat') ?></textarea>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="client_alamat" class="form-label">Alamat Alternatif</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-map-pin"></i></span>
                                <textarea class="form-control" 
                                          id="client_alamat" 
                                          name="client_alamat" 
                                          rows="2" 
                                          placeholder="Alamat alternatif (jika berbeda)"><?= old('client_alamat') ?></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kategori" class="form-label">Kategori</label>
                                    <select class="form-select" id="kategori" name="kategori">
                                        <option value="perusahaan" <?= old('kategori') == 'perusahaan' ? 'selected' : '' ?>>Perusahaan</option>
                                        <option value="pemerintah" <?= old('kategori') == 'pemerintah' ? 'selected' : '' ?>>Pemerintah</option>
                                        <option value="perorangan" <?= old('kategori') == 'perorangan' ? 'selected' : '' ?>>Perorangan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="active" <?= old('status') == 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="inactive" <?= old('status') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                        <option value="potensial" <?= old('status') == 'potensial' ? 'selected' : '' ?>>Potensial</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="karyawan_id" class="form-label">Ditangani Oleh</label>
                            <select class="form-select select2" id="karyawan_id" name="karyawan_id">
                                <option value="">-- Pilih Karyawan --</option>
                                <?php if(!empty($karyawan)): ?>
                                    <?php foreach($karyawan as $k): ?>
                                        <option value="<?= $k['id'] ?>" <?= old('karyawan_id') == $k['id'] ? 'selected' : '' ?>>
                                            <?= esc($k['nama_lengkap']) ?> <?= !empty($k['jabatan']) ? '- ' . esc($k['jabatan']) : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <small class="text-muted">Pilih karyawan yang menangani client ini</small>
                        </div>

                        <div class="mb-3">
                            <label for="keperluan_client" class="form-label">Keperluan/Project</label>
                            <textarea class="form-control" 
                                      id="keperluan_client" 
                                      name="keperluan_client" 
                                      rows="2" 
                                      placeholder="Masukkan keperluan atau project yang akan dikerjakan"><?= old('keperluan_client') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="catatan_client" class="form-label">Catatan Khusus</label>
                            <textarea class="form-control" 
                                      id="catatan_client" 
                                      name="catatan_client" 
                                      rows="2" 
                                      placeholder="Catatan khusus tentang client"><?= old('catatan_client') ?></textarea>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Tombol Submit -->
                <div class="d-flex justify-content-end gap-2">
                    <button type="reset" class="btn btn-light">
                        <i class="fas fa-undo me-2"></i>Reset
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan Client
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Informasi Tambahan -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="dashboard-card">
                <div class="card-body text-center p-4">
                    <i class="fas fa-info-circle fa-3x text-primary mb-3"></i>
                    <h6>Informasi</h6>
                    <p class="text-muted small mb-0">Field dengan tanda <span class="text-danger">*</span> wajib diisi</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card">
                <div class="card-body text-center p-4">
                    <i class="fas fa-building fa-3x text-success mb-3"></i>
                    <h6>Kode Client</h6>
                    <p class="text-muted small mb-0">Kode client akan digenerate otomatis oleh sistem</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card">
                <div class="card-body text-center p-4">
                    <i class="fas fa-tags fa-3x text-warning mb-3"></i>
                    <h6>Kategori & Status</h6>
                    <p class="text-muted small mb-0">Pilih kategori dan status yang sesuai untuk client</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 & Select2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Inisialisasi Select2
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: '-- Pilih Karyawan --',
        allowClear: true
    });

    // Validasi form sebelum submit
    $('#formTambahClient').on('submit', function(e) {
        let nama = $('#nama_perusahaan').val().trim();
        
        if(nama === '') {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: 'Nama perusahaan/client harus diisi!',
                confirmButtonText: 'OK'
            });
            return false;
        }

        // Konfirmasi sebelum submit
        Swal.fire({
            title: 'Simpan Client?',
            text: 'Apakah data yang diisi sudah benar?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Cek Kembali'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading
                Swal.fire({
                    title: 'Menyimpan...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                        // Submit form
                        document.getElementById('formTambahClient').submit();
                    }
                });
            }
        });
        
        return false; // Tunda submit sampai konfirmasi
    });

    // Format nomor telepon otomatis
    $('#telepon').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        $(this).val(value);
    });

    // Auto-generate kode client berdasarkan input (opsional)
    $('#nama_perusahaan').on('keyup', function() {
        // Bisa ditambahkan fitur generate kode otomatis berdasarkan nama
        // Tapi untuk sekarang biarkan readonly
    });
});

// Preview data sebelum submit (opsional)
function previewData() {
    let data = {
        'Kode Client': $('#kode_client').val(),
        'Nama Perusahaan': $('#nama_perusahaan').val(),
        'Kontak Person': $('#nama_kontak').val() || '-',
        'Email': $('#email_client').val() || '-',
        'Telepon': $('#telepon').val() || '-',
        'Kategori': $('#kategori option:selected').text(),
        'Status': $('#status option:selected').text()
    };
    
    let html = '<table class="table table-sm">';
    for(let key in data) {
        html += `<tr><th width="40%">${key}</th><td>${data[key]}</td></tr>`;
    }
    html += '</table>';
    
    Swal.fire({
        title: 'Preview Data',
        html: html,
        icon: 'info',
        confirmButtonText: 'OK'
    });
}

// Tampilkan pesan dari session (jika ada redirect dengan pesan)
<?php if(session()->getFlashdata('success')): ?>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '<?= session()->getFlashdata('success') ?>',
        showConfirmButton: false,
        timer: 3000
    }).then(() => {
        window.location.href = '<?= base_url('teknisi/tugas-proyek/tambah-client') ?>';
    });
<?php endif; ?>

<?php if(session()->getFlashdata('error')): ?>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '<?= session()->getFlashdata('error') ?>',
        confirmButtonText: 'OK'
    });
<?php endif; ?>
</script>

<style>
/* Custom styles */
.dashboard-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    overflow: hidden;
    margin-bottom: 20px;
}

.card-header {
    border-bottom: 1px solid #eaeaea;
    background-color: white;
}

/* Form styles */
.form-label {
    font-weight: 500;
    color: #333;
    font-size: 0.9rem;
    margin-bottom: 0.3rem;
}

.input-group-text {
    background-color: #f8f9fc;
    border-right: none;
}

.form-control, .form-select {
    border-left: none;
}

.form-control:focus, .form-select:focus {
    border-color: #dee2e6;
    box-shadow: none;
    border-left: none;
}

.input-group:focus-within .input-group-text {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Select2 custom */
.select2-container--bootstrap-5 .select2-selection {
    border-left: none;
    min-height: 38px;
}

.select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
    padding-left: 12px;
}

/* Required field indicator */
.text-danger {
    font-weight: bold;
}

/* Responsive */
@media (max-width: 768px) {
    .dashboard-card {
        margin-bottom: 15px;
    }
    
    .btn {
        width: 100%;
        margin-bottom: 5px;
    }
    
    .d-flex {
        flex-direction: column;
    }
    
    .d-flex .btn {
        margin-left: 0 !important;
    }
}

/* Info cards */
.info-card {
    transition: transform 0.2s;
}

.info-card:hover {
    transform: translateY(-5px);
}

/* Loading spinner */
.swal2-loading {
    border-color: #28a745 !important;
}

/* Validation styling */
.is-invalid ~ .invalid-feedback {
    display: block;
}
</style>

<?= $this->include('teknisi/templates/footer') ?>