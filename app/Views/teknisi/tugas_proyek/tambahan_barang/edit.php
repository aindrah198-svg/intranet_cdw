<?= $this->include('teknisi/templates/header') ?>
<?= $this->include('teknisi/templates/sidebar') ?>
<?= $this->include('teknisi/templates/navbar') ?>

<!-- Konten utama -->
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><?= esc($title ?? 'Edit Pengeluaran') ?></h4>
            <p class="text-muted mb-0"><?= esc($subtitle ?? 'Ubah data pengeluaran yang sudah ada') ?></p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('teknisi/tugas-proyek/tambahan-barang/detail/' . $pengeluaran->id) ?>" class="btn btn-info">
                <i class="fas fa-eye me-2"></i>Detail
            </a>
            <a href="<?= base_url('teknisi/tugas-proyek/tambahan-barang') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
    
    <!-- Alert Messages -->
    <?php if(session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-1">
                <?php foreach(session()->getFlashdata('errors') as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <!-- Form Edit Pengeluaran -->
    <div class="dashboard-card">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="fas fa-edit me-2 text-warning"></i>Form Edit Pengeluaran</h5>
        </div>
        <div class="card-body">
<form action="<?= base_url('teknisi/tugas-proyek/tambahan-barang/update/' . $pengeluaran->id) ?>" method="POST" enctype="multipart/form-data" id="formPengeluaran">
    <?= csrf_field() ?>
                
                <!-- Informasi Dasar -->
                <div class="section-title mb-3">
                    <h6 class="fw-bold"><i class="fas fa-info-circle me-2 text-warning"></i>Informasi Pengeluaran</h6>
                    <hr class="mt-1">
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih Proyek / SPK <span class="text-danger">*</span></label>
                            <select name="spk_id" id="spk_id" class="form-select <?= (session('errors.spk_id')) ? 'is-invalid' : '' ?>" required>
                                <option value="">-- Pilih Proyek --</option>
                                <?php foreach($spk_list as $spk): ?>
                                    <option value="<?= $spk->id ?>" <?= (old('spk_id', $pengeluaran->spk_id) == $spk->id) ? 'selected' : '' ?>>
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
                                       value="<?= old('no_ref', $pengeluaran->no_ref) ?>" placeholder="Contoh: SPK-EXP-001 / INV-001">
                                <span class="input-group-text bg-light" id="cekNoRef" style="cursor: pointer;" title="Cek ketersediaan nomor">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                            </div>
                            <small class="text-muted">Nomor referensi (opsional, misal nomor invoice/nota)</small>
                            <div id="noRefStatus" class="mt-1"></div>
                            <?php if(session('errors.no_ref')): ?>
                                <div class="invalid-feedback d-block"><?= session('errors.no_ref') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Pengeluaran <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pengeluaran" class="form-control <?= (session('errors.nama_pengeluaran')) ? 'is-invalid' : '' ?>" 
                                   value="<?= old('nama_pengeluaran', $pengeluaran->nama_pengeluaran) ?>" placeholder="Contoh: Bensin perjalanan ke Cilacap" required>
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
                                            <option value="<?= $key ?>" <?= old('jenis', $pengeluaran->jenis) == $key ? 'selected' : '' ?>>
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
                                           value="<?= old('tanggal', $pengeluaran->tanggal) ?>" required>
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
                                       value="<?= old('jumlah', number_format($pengeluaran->jumlah, 0, ',', '.')) ?>" onkeyup="formatRupiah(this)" placeholder="0" required>
                            </div>
                            <?php if(session('errors.jumlah')): ?>
                                <div class="invalid-feedback d-block"><?= session('errors.jumlah') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Foto Nota / Bukti</label>
                            <input type="file" name="foto_nota" id="foto_nota" class="form-control" accept="image/jpg,image/jpeg,image/png,application/pdf">
                            <small class="text-muted">Format: JPG, PNG, PDF. Maks 5MB. Kosongkan jika tidak ingin mengubah</small>
                            
                            <?php if($pengeluaran->foto_nota && file_exists($pengeluaran->foto_nota)): ?>
                                <div class="mt-2 p-2 border rounded">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <i class="fas fa-file-image text-primary me-1"></i>
                                            <small>File saat ini: <?= basename($pengeluaran->foto_nota) ?></small>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="hapus_foto" id="hapus_foto" value="1">
                                            <label class="form-check-label text-danger" for="hapus_foto">
                                                <small>Hapus foto</small>
                                            </label>
                                        </div>
                                    </div>
                                    <?php 
                                    $file_ext = pathinfo($pengeluaran->foto_nota, PATHINFO_EXTENSION);
                                    if(in_array(strtolower($file_ext), ['jpg', 'jpeg', 'png'])): 
                                    ?>
                                        <a href="<?= base_url($pengeluaran->foto_nota) ?>" target="_blank" class="mt-2 d-block">
                                            <img src="<?= base_url($pengeluaran->foto_nota) ?>" alt="Preview" style="max-height: 100px; max-width: 100%;">
                                        </a>
                                    <?php elseif(strtolower($file_ext) == 'pdf'): ?>
                                        <a href="<?= base_url($pengeluaran->foto_nota) ?>" target="_blank" class="btn btn-sm btn-outline-danger mt-2">
                                            <i class="fas fa-file-pdf me-1"></i>Lihat PDF
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Keterangan -->
                <div class="section-title mb-3">
                    <h6 class="fw-bold"><i class="fas fa-pencil-alt me-2 text-warning"></i>Keterangan</h6>
                    <hr class="mt-1">
                </div>
                
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi / Keterangan</label>
                            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi detail pengeluaran (opsional)"><?= old('deskripsi', $pengeluaran->deskripsi) ?></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Informasi Riwayat -->
                <div class="section-title mb-3">
                    <h6 class="fw-bold"><i class="fas fa-history me-2 text-warning"></i>Informasi Riwayat</h6>
                    <hr class="mt-1">
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="40%">Dibuat Oleh</th>
                                <td>: <?= esc($pengeluaran->created_by_nama ?? 'System') ?></td>
                            </tr>
                            <tr>
                                <th>Tanggal Dibuat</th>
                                <td>: <?= date('d F Y H:i', strtotime($pengeluaran->created_at)) ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="40%">Terakhir Diupdate</th>
                                <td>: <?= $pengeluaran->updated_at ? date('d F Y H:i', strtotime($pengeluaran->updated_at)) : '-' ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= base_url('teknisi/tugas-proyek/tambahan-barang/detail/' . $pengeluaran->id) ?>" class="btn btn-secondary px-4">
                        <i class="fas fa-times me-2"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-warning px-5" id="btnSubmit">
                        <i class="fas fa-save me-2"></i>Update Pengeluaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var originalNoRef = $('#no_ref').val();
    
    // Cek ketersediaan nomor referensi
    $('#cekNoRef').on('click', function() {
        cekNoRef();
    });
    
    $('#no_ref').on('keyup', function() {
        // Debounce untuk menghindari terlalu banyak request
        clearTimeout(window.noRefTimer);
        window.noRefTimer = setTimeout(function() {
            cekNoRef();
        }, 500);
    });
    
    function cekNoRef() {
        var no_ref = $('#no_ref').val();
        var id = <?= $pengeluaran->id ?>;
        
        if (no_ref === '') {
            $('#noRefStatus').html('');
            return;
        }
        
        if (no_ref === originalNoRef) {
            $('#noRefStatus').html('<small class="text-success"><i class="fas fa-check-circle me-1"></i>Nomor referensi saat ini</small>');
            return;
        }
        
        $.ajax({
            url: '<?= base_url("teknisi/tugas-proyek/tambahan-barang/cekNoRef") ?>',
            type: 'GET',
            data: {
                no_ref: no_ref,
                id: id
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (response.available) {
                        $('#noRefStatus').html('<small class="text-success"><i class="fas fa-check-circle me-1"></i>' + response.message + '</small>');
                    } else {
                        $('#noRefStatus').html('<small class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>' + response.message + '</small>');
                    }
                }
            },
            error: function() {
                $('#noRefStatus').html('<small class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>Gagal cek ketersediaan</small>');
            }
        });
    }
    
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
    
    // Validasi form
 $('#formPengeluaran').on('submit', function(e) {
    // Hapus format Rupiah dari input jumlah
    let jumlahInput = $('#jumlah');
    let jumlahValue = jumlahInput.val().replace(/[^,\d]/g, ''); // Hapus semua kecuali angka dan koma
    jumlahInput.val(jumlahValue); // Set nilai yang sudah dibersihkan
    
    let jumlah = parseInt(jumlahValue);
    if (jumlah <= 0) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Validasi Gagal',
            text: 'Jumlah biaya harus lebih dari 0',
            confirmButtonText: 'OK'
        });
        return;
    }
        
        // Cek nomor referensi jika diubah
        var no_ref = $('#no_ref').val();
        if (no_ref !== originalNoRef && no_ref !== '') {
            var noRefStatus = $('#noRefStatus').find('.text-danger').length > 0;
            if (noRefStatus) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Validasi Gagal',
                    text: 'Nomor referensi sudah digunakan, silakan ganti dengan yang lain',
                    confirmButtonText: 'OK'
                });
                return;
            }
        }
    });
    
    // Konfirmasi jika mengubah foto
    var originalFoto = '<?= $pengeluaran->foto_nota ?>';
    $('#foto_nota, #hapus_foto').on('change', function() {
        if ($('#foto_nota').val() !== '' || $('#hapus_foto').is(':checked')) {
            // Tidak perlu konfirmasi, hanya informasi
        }
    });
});
function formatRupiah(input) {
    let value = input.value.replace(/[^,\d]/g, '').toString();
    // Simpan nilai bersih ke hidden input
    document.getElementById('jumlah_clean').value = value;
    
    let split = value.split(',');
    let sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
    
    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    
    input.value = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
}

// Tampilkan pesan sukses/error dari session
<?php if(session()->getFlashdata('success')): ?>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '<?= session()->getFlashdata('success') ?>',
        confirmButtonText: 'OK'
    });
<?php endif; ?>

<?php if(session()->getFlashdata('error')): ?>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '<?= session()->getFlashdata('error') ?>',
        confirmButtonText: 'OK'
    });
<?php endif; ?>
</script>

<style>
.section-title h6 {
    color: #f39c12;
    margin-bottom: 0;
}
.section-title hr {
    margin-top: 5px;
    margin-bottom: 15px;
    border-top: 2px solid #f39c12;
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
#cekNoRef:hover {
    background-color: #e9ecef !important;
}
</style>

<?= $this->include('teknisi/templates/footer') ?>