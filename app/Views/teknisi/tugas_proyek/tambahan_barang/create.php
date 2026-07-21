<?= $this->include('teknisi/templates/header') ?>
<?= $this->include('teknisi/templates/sidebar') ?>
<?= $this->include('teknisi/templates/navbar') ?>

<!-- Konten utama -->
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><?= esc($title ?? 'Tambah Pengeluaran') ?></h4>
            <p class="text-muted mb-0"><?= esc($subtitle ?? 'Catat pengeluaran baru untuk proyek') ?></p>
        </div>
        <a href="<?= base_url('teknisi/tugas-proyek/tambahan-barang') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
    
    <!-- Form Tambah Pengeluaran -->
    <div class="dashboard-card">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="fas fa-plus-circle me-2 text-primary"></i>Form Tambah Pengeluaran</h5>
        </div>
        <div class="card-body">
            <form action="<?= base_url('teknisi/tugas-proyek/tambahan-barang/store') ?>" method="post" enctype="multipart/form-data" id="formPengeluaran">
                <?= csrf_field() ?>
                
                <!-- Hidden input untuk menyimpan nilai asli jumlah (tanpa format) -->
                <input type="hidden" name="jumlah_original" id="jumlah_original" value="<?= old('jumlah') ?>">
                
                <!-- Informasi Dasar -->
                <div class="section-title mb-3">
                    <h6 class="fw-bold"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi Pengeluaran</h6>
                    <hr class="mt-1">
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih Proyek / SPK <span class="text-danger">*</span></label>
                            <select name="spk_id" id="spk_id" class="form-select <?= (session('errors.spk_id')) ? 'is-invalid' : '' ?>" required>
                                <option value="">-- Pilih Proyek --</option>
                                <?php foreach($spk_list as $spk): ?>
                                    <option value="<?= $spk->id ?>" <?= ($selected_spk_id ?? old('spk_id')) == $spk->id ? 'selected' : '' ?>>
                                        <?= esc($spk->nomor_spk) ?> - <?= esc($spk->judul_pekerjaan) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if(session('errors.spk_id')): ?>
                                <div class="invalid-feedback"><?= session('errors.spk_id') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">No. Referensi</label>
                            <div class="input-group">
                                <input type="text" name="no_ref" id="no_ref" class="form-control <?= (session('errors.no_ref')) ? 'is-invalid' : '' ?>" 
                                       value="<?= old('no_ref', $no_ref_auto) ?>" readonly
                                       placeholder="Otomatis dari sistem">
                                <span class="input-group-text bg-light" id="refreshNoRef" style="cursor: pointer;" title="Generate ulang nomor referensi">
                                    <i class="fas fa-sync-alt"></i>
                                </span>
                            </div>
                            <small class="text-muted">Nomor referensi otomatis dari sistem (format: SPK-TANGGAL-NO_URUT)</small>
                            <?php if(session('errors.no_ref')): ?>
                                <div class="invalid-feedback d-block"><?= session('errors.no_ref') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Pengeluaran <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pengeluaran" class="form-control <?= (session('errors.nama_pengeluaran')) ? 'is-invalid' : '' ?>" 
                                   value="<?= old('nama_pengeluaran') ?>" placeholder="Contoh: Bensin perjalanan ke Cilacap" required>
                            <?php if(session('errors.nama_pengeluaran')): ?>
                                <div class="invalid-feedback"><?= session('errors.nama_pengeluaran') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Jenis <span class="text-danger">*</span></label>
                                    <select name="jenis" class="form-select <?= (session('errors.jenis')) ? 'is-invalid' : '' ?>" required>
                                        <option value="">-- Pilih Jenis --</option>
                                        <?php foreach($jenis_options as $key => $value): ?>
                                            <option value="<?= $key ?>" <?= old('jenis') == $key ? 'selected' : '' ?>>
                                                <?= $value ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if(session('errors.jenis')): ?>
                                        <div class="invalid-feedback"><?= session('errors.jenis') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal" class="form-control <?= (session('errors.tanggal')) ? 'is-invalid' : '' ?>" 
                                           value="<?= old('tanggal', date('Y-m-d')) ?>" required>
                                    <?php if(session('errors.tanggal')): ?>
                                        <div class="invalid-feedback"><?= session('errors.tanggal') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jumlah Biaya <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="jumlah" id="jumlah" class="form-control <?= (session('errors.jumlah')) ? 'is-invalid' : '' ?>" 
                                       value="<?= old('jumlah') ? old('jumlah') : '' ?>" onkeyup="formatRupiah(this)" onblur="updateOriginalValue(this)" placeholder="0" required>
                            </div>
                            <small class="text-muted">Format penulisan: Rp 40.000 (titik sebagai pemisah ribuan)</small>
                            <?php if(session('errors.jumlah')): ?>
                                <div class="invalid-feedback d-block"><?= session('errors.jumlah') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Foto Nota / Bukti</label>
                            <input type="file" name="foto_nota" id="foto_nota" class="form-control" accept="image/jpg,image/jpeg,image/png,application/pdf">
                            <small class="text-muted">Format: JPG, PNG, PDF. Maks 5MB. Opsional</small>
                        </div>
                    </div>
                </div>
                
                <!-- Keterangan -->
                <div class="section-title mb-3">
                    <h6 class="fw-bold"><i class="fas fa-pencil-alt me-2 text-primary"></i>Keterangan</h6>
                    <hr class="mt-1">
                </div>
                
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi / Keterangan</label>
                            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi detail pengeluaran (opsional)"><?= old('deskripsi') ?></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Informasi Proyek (Jika ada selected_spk_id) -->
                <?php if(isset($spk_detail) && $spk_detail): ?>
                <div class="section-title mb-3">
                    <h6 class="fw-bold"><i class="fas fa-project-diagram me-2 text-primary"></i>Informasi Proyek Terkait</h6>
                    <hr class="mt-1">
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="40%">Nomor SPK</th>
                                <td>: <strong><?= esc($spk_detail->nomor_spk) ?></strong></td>
                            </tr>
                            <tr>
                                <th>Judul Pekerjaan</th>
                                <td>: <?= esc($spk_detail->judul_pekerjaan) ?></td>
                            </tr>
                            <tr>
                                <th>Lokasi</th>
                                <td>: <?= esc($spk_detail->lokasi ?? '-') ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="40%">Client</th>
                                <td>: <?= esc($spk_detail->client_nama ?? '-') ?></td>
                            </tr>
                            <tr>
                                <th>Estimasi Biaya</th>
                                <td>: Rp <?= number_format($spk_detail->estimasi_biaya ?? 0, 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <th>Progress</th>
                                <td>: <?= $spk_detail->progress_persen ?>%</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
                
                <hr class="my-4">
                
                <div class="d-flex justify-content-end gap-2">
                    <button type="reset" class="btn btn-secondary px-4" onclick="return confirm('Reset form? Semua data yang sudah diisi akan hilang.')">
                        <i class="fas fa-undo me-2"></i>Reset
                    </button>
                    <button type="submit" class="btn btn-primary px-5" id="btnSubmit">
                        <i class="fas fa-save me-2"></i>Simpan Pengeluaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Refresh nomor referensi
    $('#refreshNoRef').on('click', function() {
        $.ajax({
            url: '<?= base_url("teknisi/tugas-proyek/tambahan-barang/ajaxGenerateNoRef") ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#no_ref').val(response.no_ref);
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Nomor referensi baru: ' + response.no_ref,
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal generate nomor referensi'
                });
            }
        });
    });
    
    // Preview file upload
    $('#foto_nota').on('change', function() {
        const file = this.files[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) { // 5MB
                Swal.fire({
                    icon: 'error',
                    title: 'File Terlalu Besar',
                    text: 'Ukuran file maksimal 5MB',
                    confirmButtonText: 'OK'
                });
                $(this).val('');
            }
        }
    });
    
    // Set initial original value jika ada old value
    <?php if(old('jumlah')): ?>
        let oldJumlah = '<?= old('jumlah') ?>';
        if (oldJumlah) {
            $('#jumlah_original').val(oldJumlah);
            // Format tampilan
            let formatted = formatNumber(parseInt(oldJumlah));
            $('#jumlah').val(formatted);
        }
    <?php endif; ?>
    
    // Validasi form sebelum submit
    $('#formPengeluaran').on('submit', function(e) {
        // Update nilai jumlah dengan nilai dari hidden input
        let originalValue = $('#jumlah_original').val();
        
        // Validasi
        if (!originalValue || parseInt(originalValue) <= 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Validasi Gagal',
                text: 'Jumlah biaya harus lebih dari 0',
                confirmButtonText: 'OK'
            });
            return false;
        }
    });
});

function formatRupiah(input) {
    // Hapus semua karakter non-digit
    let value = input.value.replace(/[^0-9]/g, '');
    
    // Simpan nilai asli (tanpa format) ke hidden input
    $('#jumlah_original').val(value);
    
    // Format untuk tampilan (tambah titik sebagai pemisah ribuan)
    if (value) {
        // Format dengan titik sebagai pemisah ribuan
        let formatted = formatNumber(value);
        input.value = formatted;
    } else {
        input.value = '';
    }
}

function formatNumber(number) {
    // Fungsi untuk memformat angka dengan titik sebagai pemisah ribuan
    let numberString = number.toString();
    let sisa = numberString.length % 3;
    let rupiah = numberString.substr(0, sisa);
    let ribuan = numberString.substr(sisa).match(/\d{3}/g);
    
    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    
    return rupiah;
}

function updateOriginalValue(input) {
    // Fungsi ini dipanggil saat input kehilangan fokus (onblur)
    // Pastikan nilai original sudah terisi
    let value = input.value.replace(/[^0-9]/g, '');
    if (value) {
        $('#jumlah_original').val(value);
    }
}

// Tampilkan pesan error dari session
<?php if(session()->getFlashdata('errors')): ?>
    <?php foreach(session()->getFlashdata('errors') as $error): ?>
        Swal.fire({
            icon: 'error',
            title: 'Validasi Gagal',
            text: '<?= $error ?>',
            confirmButtonText: 'OK'
        });
    <?php endforeach; ?>
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
.table-borderless td, .table-borderless th {
    padding: 0.3rem 0;
}
#refreshNoRef:hover {
    background-color: #e9ecef !important;
}
</style>

<?= $this->include('teknisi/templates/footer') ?>