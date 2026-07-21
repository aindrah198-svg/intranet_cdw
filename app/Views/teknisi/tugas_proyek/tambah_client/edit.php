<?= $this->include('teknisi/templates/header') ?>
<?= $this->include('teknisi/templates/sidebar') ?>
<?= $this->include('teknisi/templates/navbar') ?>

<!-- Konten utama -->
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><?= esc($title ?? 'Edit Client') ?></h4>
            <p class="text-muted mb-0"><?= esc($subtitle ?? 'Perbaharui data client') ?></p>
        </div>
        <div>
            <a href="<?= base_url('teknisi/tugas-proyek/tambah-client/detail/' . $client->id) ?>" class="btn btn-info me-2">
                <i class="fas fa-eye me-2"></i>Detail
            </a>
            <a href="<?= base_url('teknisi/tugas-proyek/tambah-client') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
    
    <!-- Form Edit Client -->
    <div class="dashboard-card">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="fas fa-edit me-2 text-warning"></i>Form Edit Client</h5>
        </div>
        <div class="card-body">
            <form action="<?= base_url('teknisi/tugas-proyek/tambah-client/update/' . $client->id) ?>" method="post" id="formClient">
                <?= csrf_field() ?>
                
                <!-- Informasi Dasar -->
                <div class="section-title mb-3">
                    <h6 class="fw-bold"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi Dasar</h6>
                    <hr class="mt-1">
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <!-- Kode Client -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kode Client <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">CLT-</span>
                                <input type="text" name="kode_client_custom" id="kode_client_custom" class="form-control <?= (session('errors.kode_client')) ? 'is-invalid' : '' ?>" 
                                       value="<?= old('kode_client_custom', substr($client->kode_client, 4)) ?>" 
                                       placeholder="YYYYMMDD-001" required>
                                <input type="hidden" name="kode_client" id="kode_client" value="<?= old('kode_client', $client->kode_client) ?>">
                                <button type="button" class="btn btn-outline-primary" id="btnGenerateKode">
                                    <i class="fas fa-sync-alt"></i> Generate
                                </button>
                            </div>
                            <?php if(session('errors.kode_client')): ?>
                                <div class="invalid-feedback d-block"><?= session('errors.kode_client') ?></div>
                            <?php endif; ?>
                            <small class="text-muted">Format: CLT-TAHUNBULANTANGGAL-NOURUT (contoh: CLT-20240224-001)</small>
                            <div id="kodeClientStatus" class="mt-1"></div>
                        </div>
                        
                        <!-- Nama Perusahaan -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Perusahaan / Client <span class="text-danger">*</span></label>
                            <input type="text" name="nama_perusahaan" class="form-control <?= (session('errors.nama_perusahaan')) ? 'is-invalid' : '' ?>" 
                                   value="<?= old('nama_perusahaan', $client->nama_perusahaan) ?>" placeholder="Masukkan nama perusahaan/client" required>
                            <?php if(session('errors.nama_perusahaan')): ?>
                                <div class="invalid-feedback"><?= session('errors.nama_perusahaan') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Nama Kontak -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Kontak Person</label>
                            <input type="text" name="nama_kontak" class="form-control <?= (session('errors.nama_kontak')) ? 'is-invalid' : '' ?>" 
                                   value="<?= old('nama_kontak', $client->nama_kontak) ?>" placeholder="Nama contact person">
                            <?php if(session('errors.nama_kontak')): ?>
                                <div class="invalid-feedback"><?= session('errors.nama_kontak') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email_client" class="form-control <?= (session('errors.email_client')) ? 'is-invalid' : '' ?>" 
                                   value="<?= old('email_client', $client->email_client) ?>" placeholder="email@domain.com">
                            <?php if(session('errors.email_client')): ?>
                                <div class="invalid-feedback"><?= session('errors.email_client') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Telepon -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Telepon</label>
                            <input type="text" name="telepon" class="form-control <?= (session('errors.telepon')) ? 'is-invalid' : '' ?>" 
                                   value="<?= old('telepon', $client->telepon) ?>" placeholder="021-xxxxxxx / 0812xxxxxx">
                            <?php if(session('errors.telepon')): ?>
                                <div class="invalid-feedback"><?= session('errors.telepon') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Kontak Lainnya -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kontak Lainnya</label>
                            <input type="text" name="client_kontak" class="form-control" 
                                   value="<?= old('client_kontak', $client->client_kontak) ?>" placeholder="Informasi kontak tambahan (opsional)">
                        </div>
                    </div>
                </div>
                
                <!-- Alamat & Kategori -->
                <div class="section-title mb-3">
                    <h6 class="fw-bold"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Alamat & Kategori</h6>
                    <hr class="mt-1">
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <!-- Alamat Utama -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Alamat Utama</label>
                            <textarea name="alamat" class="form-control" rows="3" placeholder="Alamat utama client"><?= old('alamat', $client->alamat) ?></textarea>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <!-- Alamat Alternatif -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Alamat Alternatif / Lokasi Proyek</label>
                            <textarea name="client_alamat" class="form-control" rows="3" placeholder="Alamat alternatif atau lokasi proyek"><?= old('client_alamat', $client->client_alamat) ?></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-4">
                        <!-- Kategori -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori" class="form-select <?= (session('errors.kategori')) ? 'is-invalid' : '' ?>" required>
                                <option value="perusahaan" <?= (old('kategori', $client->kategori) == 'perusahaan') ? 'selected' : '' ?>>Perusahaan</option>
                                <option value="pemerintah" <?= (old('kategori', $client->kategori) == 'pemerintah') ? 'selected' : '' ?>>Pemerintah</option>
                                <option value="perorangan" <?= (old('kategori', $client->kategori) == 'perorangan') ? 'selected' : '' ?>>Perorangan</option>
                            </select>
                            <?php if(session('errors.kategori')): ?>
                                <div class="invalid-feedback"><?= session('errors.kategori') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <!-- Status -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select <?= (session('errors.status')) ? 'is-invalid' : '' ?>" required>
                                <option value="active" <?= (old('status', $client->status) == 'active') ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= (old('status', $client->status) == 'inactive') ? 'selected' : '' ?>>Inactive</option>
                                <option value="potensial" <?= (old('status', $client->status) == 'potensial') ? 'selected' : '' ?>>Potensial</option>
                            </select>
                            <?php if(session('errors.status')): ?>
                                <div class="invalid-feedback"><?= session('errors.status') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <!-- Ditangani Oleh -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ditangani Oleh</label>
                            <select name="karyawan_id" class="form-select <?= (session('errors.karyawan_id')) ? 'is-invalid' : '' ?>">
                                <option value="">-- Pilih Karyawan --</option>
                                <?php if(!empty($karyawan)): ?>
                                    <?php foreach($karyawan as $k): ?>
                                        <?php 
                                        $karyawanId = is_object($k) ? $k->id : $k['id'];
                                        $karyawanNama = is_object($k) ? $k->nama_lengkap : $k['nama_lengkap'];
                                        $karyawanJabatan = is_object($k) ? ($k->jabatan ?? '') : ($k['jabatan'] ?? '');
                                        ?>
                                        <option value="<?= $karyawanId ?>" <?= (old('karyawan_id', $client->karyawan_id) == $karyawanId) ? 'selected' : '' ?>>
                                            <?= esc($karyawanNama) ?> (<?= esc($karyawanJabatan) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php if(session('errors.karyawan_id')): ?>
                                <div class="invalid-feedback"><?= session('errors.karyawan_id') ?></div>
                            <?php endif; ?>
                            <small class="text-muted">Kosongkan jika belum ditentukan</small>
                        </div>
                    </div>
                </div>
                
                <!-- Informasi Tambahan -->
                <div class="section-title mb-3">
                    <h6 class="fw-bold"><i class="fas fa-clipboard-list me-2 text-primary"></i>Informasi Tambahan</h6>
                    <hr class="mt-1">
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <!-- Keperluan Client -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Keperluan / Kebutuhan Client</label>
                            <textarea name="keperluan_client" class="form-control" rows="4" placeholder="Contoh: Flowmeter Tominaga, pompa Corken, jasa kalibrasi, dll."><?= old('keperluan_client', $client->keperluan_client) ?></textarea>
                            <small class="text-muted">Jelaskan kebutuhan atau produk yang biasa diminta client</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <!-- Catatan Client -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Catatan Khusus</label>
                            <textarea name="catatan_client" class="form-control" rows="4" placeholder="Catatan penting tentang client (kebiasaan, preferensi, dll.)"><?= old('catatan_client', $client->catatan_client) ?></textarea>
                            <small class="text-muted">Catatan internal tentang client</small>
                        </div>
                    </div>
                </div>
                
                <!-- Informasi Sistem (Readonly) -->
                <div class="section-title mb-3">
                    <h6 class="fw-bold"><i class="fas fa-clock me-2 text-primary"></i>Informasi Sistem</h6>
                    <hr class="mt-1">
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Dibuat</label>
                            <input type="text" class="form-control bg-light" value="<?= date('d/m/Y H:i', strtotime($client->created_at)) ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Terakhir Diupdate</label>
                            <input type="text" class="form-control bg-light" value="<?= date('d/m/Y H:i', strtotime($client->updated_at)) ?>" readonly>
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= base_url('teknisi/tugas-proyek/tambah-client') ?>" class="btn btn-secondary px-4">
                        <i class="fas fa-times me-2"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-warning px-5" id="btnSubmit">
                        <i class="fas fa-save me-2"></i>Update Client
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Generate kode client otomatis
    $('#btnGenerateKode').on('click', function() {
        $.ajax({
            url: '<?= base_url("teknisi/tugas-proyek/tambah-client/ajax-generate") ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    let kodeClient = response.kode_client;
                    let customPart = kodeClient.substring(4); // Hapus "CLT-" dari awal
                    $('#kode_client_custom').val(customPart);
                    $('#kode_client').val(kodeClient);
                    cekKodeClient(); // Cek ketersediaan
                }
            }
        });
    });
    
    // Cek ketersediaan kode client saat input berubah
    $('#kode_client_custom').on('keyup change', function() {
        generateKodeClient();
        cekKodeClient();
    });
    
    // Inisialisasi tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Validasi form sebelum submit
    $('#formClient').on('submit', function(e) {
        let valid = true;
        let errors = [];
        
        // Generate kode client terlebih dahulu
        generateKodeClient();
        
        // Validasi kode client
        let kodeClient = $('#kode_client').val().trim();
        if (kodeClient === '' || kodeClient === 'CLT-') {
            valid = false;
            errors.push('Kode client harus diisi');
        }
        
        // Validasi nama perusahaan
        let namaPerusahaan = $('input[name="nama_perusahaan"]').val().trim();
        if (namaPerusahaan === '') {
            valid = false;
            errors.push('Nama perusahaan/client harus diisi');
        }
        
        // Validasi email (jika diisi)
        let email = $('input[name="email_client"]').val().trim();
        if (email !== '') {
            let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                valid = false;
                errors.push('Format email tidak valid');
            }
        }
        
        // Cek status kode client
        let kodeClientStatus = $('#kodeClientStatus').find('.text-danger').length > 0;
        if (kodeClientStatus) {
            valid = false;
            errors.push('Kode client sudah digunakan, silakan generate yang baru');
        }
        
        if (!valid) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Validasi Gagal',
                html: errors.join('<br>'),
                confirmButtonText: 'OK'
            });
        } else {
            // Tampilkan loading
            Swal.fire({
                title: 'Menyimpan...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }
    });
    
    // Panggil cekKodeClient saat halaman dimuat
    setTimeout(function() {
        cekKodeClient();
    }, 500);
});

// Fungsi cek ketersediaan kode client
function cekKodeClient() {
    let kode_client = $('#kode_client').val();
    let id = <?= $client->id ?>;
    
    $.ajax({
        url: '<?= base_url("teknisi/tugas-proyek/tambah-client/cek-kode") ?>',
        type: 'GET',
        data: {
            kode_client: kode_client,
            id: id
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                if (response.available) {
                    $('#kodeClientStatus').html('<small class="text-success"><i class="fas fa-check-circle me-1"></i>' + response.message + '</small>');
                } else {
                    $('#kodeClientStatus').html('<small class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>' + response.message + '</small>');
                }
            }
        }
    });
}

// Fungsi generate kode client
function generateKodeClient() {
    let customPart = $('#kode_client_custom').val().trim();
    
    // Validasi format
    if (customPart === '') {
        // Default ke tanggal hari ini + 001
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        customPart = yyyy + mm + dd + '-001';
        $('#kode_client_custom').val(customPart);
    }
    
    // Hapus karakter yang tidak diinginkan
    customPart = customPart.replace(/[^0-9\-]/g, '');
    
    // Pastikan ada format YYYYMMDD-XXX
    if (!customPart.includes('-')) {
        customPart = customPart + '-001';
    }
    
    let kodeClient = 'CLT-' + customPart;
    $('#kode_client').val(kodeClient);
    
    return kodeClient;
}

// Tampilkan pesan error dari session jika ada
<?php if(session()->getFlashdata('error')): ?>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '<?= session()->getFlashdata('error') ?>',
        confirmButtonText: 'OK'
    });
<?php endif; ?>

<?php if(session()->getFlashdata('success')): ?>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '<?= session()->getFlashdata('success') ?>',
        confirmButtonText: 'OK'
    });
<?php endif; ?>

<?php if(session()->getFlashdata('errors')): ?>
    Swal.fire({
        icon: 'error',
        title: 'Validasi Gagal',
        html: '<?= implode('<br>', session()->getFlashdata('errors')) ?>',
        confirmButtonText: 'OK'
    });
<?php endif; ?>
</script>

<style>
.section-title h6 {
    color: #4e73df;
    margin-bottom: 0;
}
.section-title hr {
    margin-top: 5px;
    margin-bottom: 15px;
    border-top: 2px solid #4e73df;
    opacity: 0.2;
}
.dashboard-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    overflow: hidden;
}
.card-header {
    border-bottom: 1px solid #eaeaea;
    background-color: white;
}
.form-label.fw-semibold {
    font-weight: 500;
    color: #5a5c69;
}
.input-group-text {
    background-color: #f8f9fc;
}
.required:after {
    content: " *";
    color: red;
}
#kodeClientStatus {
    min-height: 24px;
}
</style>

<?= $this->include('teknisi/templates/footer') ?>