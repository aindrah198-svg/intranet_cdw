<?php
// C:\xampp\htdocs\cdwnet\app\Views\admin\kontrak\create.php
$title = 'Buat Kontrak Baru';
$active = 'kontrak';
$css = [
    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
    'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css'
];
$scripts = [
    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
    'https://cdn.jsdelivr.net/npm/flatpickr',
    'https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js'
];
?>

<?= $this->include('admin/templates/header') ?>
<?= $this->include('admin/templates/sidebar') ?>
<?= $this->include('admin/templates/navbar') ?>

<!-- Main Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Buat Kontrak Baru</h1>
        <a href="<?= base_url('admin/karyawan/kontrak'); ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <!-- Form Create -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Kontrak Kerja</h6>
        </div>
        <div class="card-body">
            <form method="post" action="<?= base_url('admin/karyawan/kontrak/store'); ?>" id="kontrakForm">
                <?= csrf_field(); ?>
                
                <div class="row">
                    <!-- Kolom Kiri -->
                    <div class="col-md-6">
                        <!-- Data Umum -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Data Umum</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="karyawan_id" class="form-label">Karyawan <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="karyawan_id" name="karyawan_id" required>
                                            <option value="">Pilih Karyawan</option>
                                            <?php if(isset($karyawan) && !empty($karyawan)): ?>
                                                <?php foreach ($karyawan as $k): ?>
                                                    <option value="<?= $k['id']; ?>" 
                                                        <?= (isset($selectedKaryawanId) && $selectedKaryawanId == $k['id']) ? 'selected' : '' ?>>
                                                        <?= esc($k['nik']) . ' - ' . esc($k['nama_lengkap']) . ' (' . esc($k['jabatan'] ?? '-') . ')' ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <div class="form-text">Pilih karyawan yang akan dibuat kontrak</div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="nomor_kontrak" class="form-label">Nomor Kontrak <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nomor_kontrak" name="nomor_kontrak" 
                                               value="<?= old('nomor_kontrak') ?>" 
                                               placeholder="Contoh: KK/CDW/01/2025/001" required>
                                        <div class="form-text">Kosongkan untuk generate otomatis</div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="jenis_kontrak" class="form-label">Jenis Kontrak <span class="text-danger">*</span></label>
                                        <select class="form-control" id="jenis_kontrak" name="jenis_kontrak" required>
                                            <option value="">Pilih Jenis</option>
                                            <?php if(isset($jenisOptions)): ?>
                                                <?php foreach ($jenisOptions as $key => $value): ?>
                                                    <option value="<?= $key; ?>" <?= old('jenis_kontrak') == $key ? 'selected' : '' ?>>
                                                        <?= $value; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-12 mb-3">
                                        <label for="jabatan" class="form-label">Jabatan <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="jabatan" name="jabatan" 
                                               value="<?= old('jabatan') ?>" 
                                               placeholder="Contoh: Account Manager / Sales Engineer" required>
                                    </div>
                                    
                                    <div class="col-md-12 mb-3">
                                        <label for="lokasi_kerja" class="form-label">Lokasi Kerja</label>
                                        <textarea class="form-control" id="lokasi_kerja" name="lokasi_kerja" 
                                                  rows="2" placeholder="Alamat lengkap tempat kerja"><?= old('lokasi_kerja') ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Masa Kerja -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Masa Kerja</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control datepicker" id="tanggal_mulai" 
                                               name="tanggal_mulai" value="<?= old('tanggal_mulai') ? date('d/m/Y', strtotime(old('tanggal_mulai'))) : '' ?>" 
                                               placeholder="DD/MM/YYYY" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="masa_kerja_bulan" class="form-label">Masa Kerja (bulan)</label>
                                        <input type="number" class="form-control" id="masa_kerja_bulan" 
                                               name="masa_kerja_bulan" value="<?= old('masa_kerja_bulan') ?>" 
                                               min="1" max="120" placeholder="Contoh: 12">
                                        <div class="form-text">Kosongkan untuk tidak terbatas</div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="masa_percobaan_bulan" class="form-label">Masa Percobaan (bulan)</label>
                                        <input type="number" class="form-control" id="masa_percobaan_bulan" 
                                               name="masa_percobaan_bulan" value="<?= old('masa_percobaan_bulan') ?>" 
                                               min="0" max="12" placeholder="Contoh: 3">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="pemberitahuan_pemutusan_hari" class="form-label">Pemberitahuan PHK (hari)</label>
                                        <input type="number" class="form-control" id="pemberitahuan_pemutusan_hari" 
                                               name="pemberitahuan_pemutusan_hari" value="<?= old('pemberitahuan_pemutusan_hari') ?? '30' ?>" 
                                               min="1" max="90">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Gaji & Tunjangan -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Gaji & Tunjangan</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="gaji_pokok" class="form-label">Gaji Pokok</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control" id="gaji_pokok" 
                                                   name="gaji_pokok" value="<?= old('gaji_pokok') ?>" 
                                                   placeholder="0" data-mask="#.##0" data-mask-reverse="true">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="tunjangan_bpjs" 
                                                   name="tunjangan_bpjs" value="1" <?= old('tunjangan_bpjs') ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="tunjangan_bpjs">
                                                Tunjangan BPJS
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="reimburse_transport" 
                                                   name="reimburse_transport" value="1" <?= old('reimburse_transport') ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="reimburse_transport">
                                                Reimburse Transport
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="reimburse_entertaint" 
                                                   name="reimburse_entertaint" value="1" <?= old('reimburse_entertaint') ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="reimburse_entertaint">
                                                Reimburse Entertaint
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="cuti_bersama_disesuaikan" 
                                                   name="cuti_bersama_disesuaikan" value="1" <?= old('cuti_bersama_disesuaikan') ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="cuti_bersama_disesuaikan">
                                                Cuti Bersama Disesuaikan
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Kolom Kanan -->
                    <div class="col-md-6">
                        <!-- Tunjangan Uang Makan & Penginapan -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Tunjangan Perjalanan</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="tunjangan_makan_lokal" class="form-label">Uang Makan (Lokal)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control" id="tunjangan_makan_lokal" 
                                                   name="tunjangan_makan_lokal" value="<?= old('tunjangan_makan_lokal') ?>" 
                                                   placeholder="125.000" data-mask="#.##0" data-mask-reverse="true">
                                        </div>
                                        <div class="form-text">Per hari, dalam kota Jawa</div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="tunjangan_makan_luar_jawa" class="form-label">Uang Makan (Luar Jawa)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control" id="tunjangan_makan_luar_jawa" 
                                                   name="tunjangan_makan_luar_jawa" value="<?= old('tunjangan_makan_luar_jawa') ?>" 
                                                   placeholder="175.000" data-mask="#.##0" data-mask-reverse="true">
                                        </div>
                                        <div class="form-text">Per hari, luar pulau Jawa</div>
                                    </div>
                                    
                                    <div class="col-md-12 mb-3">
                                        <label for="tunjangan_penginapan_max" class="form-label">Maksimal Penginapan</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control" id="tunjangan_penginapan_max" 
                                                   name="tunjangan_penginapan_max" value="<?= old('tunjangan_penginapan_max') ?>" 
                                                   placeholder="400.000" data-mask="#.##0" data-mask-reverse="true">
                                        </div>
                                        <div class="form-text">Maksimal per hari saat dinas</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Cuti -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Hak Cuti</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="hak_cuti_setelah_tahun" class="form-label">Hak Cuti Setelah (tahun)</label>
                                        <input type="number" class="form-control" id="hak_cuti_setelah_tahun" 
                                               name="hak_cuti_setelah_tahun" value="<?= old('hak_cuti_setelah_tahun') ?? '1' ?>" 
                                               min="0" max="5">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="jumlah_cuti_tahunan_hari" class="form-label">Jumlah Cuti Tahunan (hari)</label>
                                        <input type="number" class="form-control" id="jumlah_cuti_tahunan_hari" 
                                               name="jumlah_cuti_tahunan_hari" value="<?= old('jumlah_cuti_tahunan_hari') ?? '12' ?>" 
                                               min="0" max="30">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Target & Komisi -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Target & Komisi</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="target_penjualan_bulanan" class="form-label">Target Penjualan Bulanan</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control" id="target_penjualan_bulanan" 
                                                   name="target_penjualan_bulanan" value="<?= old('target_penjualan_bulanan') ?>" 
                                                   placeholder="500.000.000" data-mask="#.##0" data-mask-reverse="true">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12 mb-3">
                                        <label for="komisi_aturan" class="form-label">Aturan Komisi</label>
                                        <textarea class="form-control" id="komisi_aturan" name="komisi_aturan" 
                                                  rows="4" placeholder="Contoh: 
- 0-250jt: 0%
- 250-500jt: 2.5% dari profit
- >500jt: 5% dari profit"><?= old('komisi_aturan') ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pihak Penandatangan -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Pihak Penandatangan</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Pihak Pertama (Perusahaan)</label>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <input type="text" class="form-control" id="pihak_pertama_nama" 
                                               name="pihak_pertama_nama" value="<?= old('pihak_pertama_nama') ?>" 
                                               placeholder="Nama">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <input type="text" class="form-control" id="pihak_pertama_jabatan" 
                                               name="pihak_pertama_jabatan" value="<?= old('pihak_pertama_jabatan') ?>" 
                                               placeholder="Jabatan">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <textarea class="form-control" id="pihak_pertama_alamat" name="pihak_pertama_alamat" 
                                                  rows="2" placeholder="Alamat"><?= old('pihak_pertama_alamat') ?></textarea>
                                    </div>
                                    
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Pihak Kedua (Karyawan)</label>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <input type="text" class="form-control" id="pihak_kedua_nama" 
                                               name="pihak_kedua_nama" value="<?= old('pihak_kedua_nama') ?>" 
                                               placeholder="Nama">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <input type="text" class="form-control" id="pihak_kedua_jabatan" 
                                               name="pihak_kedua_jabatan" value="<?= old('pihak_kedua_jabatan') ?>" 
                                               placeholder="Jabatan">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <textarea class="form-control" id="pihak_kedua_alamat" name="pihak_kedua_alamat" 
                                                  rows="2" placeholder="Alamat"><?= old('pihak_kedua_alamat') ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Status Kontrak</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="form-control" id="status" name="status" required>
                                            <option value="">Pilih Status</option>
                                            <?php if(isset($statusOptions)): ?>
                                                <?php foreach ($statusOptions as $key => $value): ?>
                                                    <option value="<?= $key; ?>" <?= old('status') == $key ? 'selected' : '' ?>>
                                                        <?= $value; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-12 mb-3">
                                        <label for="lampiran_path" class="form-label">File Lampiran</label>
                                        <input type="text" class="form-control" id="lampiran_path" 
                                               name="lampiran_path" value="<?= old('lampiran_path') ?>" 
                                               placeholder="Path/file/kontrak.pdf">
                                        <div class="form-text">Path file PDF kontrak (opsional)</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tombol Aksi -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('admin/karyawan/kontrak'); ?>" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Kontrak
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script untuk form handling -->
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Pilih Karyawan'
        });
        
        // Initialize Flatpickr untuk datepicker
        flatpickr('.datepicker', {
            dateFormat: 'd/m/Y',
            locale: 'id',
            allowInput: true
        });
        
        // Format angka dengan titik pemisah ribuan
        function formatNumber(input) {
            if (!input) return '';
            // Hapus semua karakter selain angka
            let value = input.toString().replace(/[^0-9]/g, '');
            // Format dengan titik pemisah ribuan
            return value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
        
        // Parse angka dari format dengan titik
        function parseNumber(formatted) {
            if (!formatted) return 0;
            return parseInt(formatted.toString().replace(/\./g, ''), 10) || 0;
        }
        
        // Apply mask untuk input uang
        $('[data-mask]').each(function() {
            $(this).on('input', function() {
                let value = $(this).val();
                let formatted = formatNumber(value);
                $(this).val(formatted);
            });
            
            // Format awal jika ada value
            if ($(this).val()) {
                let formatted = formatNumber($(this).val());
                $(this).val(formatted);
            }
        });
        
        // Auto-fill pihak kedua ketika karyawan dipilih
        $('#karyawan_id').change(function() {
            let karyawanId = $(this).val();
            if (karyawanId) {
                // Ambil data karyawan via AJAX (opsional)
                // Untuk sekarang, kita bisa mengisi secara manual
                // atau membuat AJAX call ke endpoint karyawan
            }
        });
        
        // Auto-calculate tanggal selesai
        $('#tanggal_mulai, #masa_kerja_bulan').change(function() {
            let tanggalMulai = $('#tanggal_mulai').val();
            let masaKerja = $('#masa_kerja_bulan').val();
            
            if (tanggalMulai && masaKerja) {
                // Parse tanggal dari format DD/MM/YYYY
                let parts = tanggalMulai.split('/');
                if (parts.length === 3) {
                    let startDate = new Date(parts[2], parts[1] - 1, parts[0]);
                    startDate.setMonth(startDate.getMonth() + parseInt(masaKerja));
                    startDate.setDate(startDate.getDate() - 1); // Kurangi 1 hari
                    
                    let endDate = startDate.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    });
                    
                    // Tampilkan info (opsional)
                    $('#tanggal_selesai_info').remove();
                    $('<small id="tanggal_selesai_info" class="text-muted d-block mt-1">' +
                      'Tanggal selesai: ' + endDate + 
                      '</small>').insertAfter('#masa_kerja_bulan');
                }
            }
        });
        
        // Form validation
        $('#kontrakForm').submit(function(e) {
            let isValid = true;
            let errors = [];
            
            // Validasi required fields
            $('#kontrakForm [required]').each(function() {
                if (!$(this).val().trim()) {
                    $(this).addClass('is-invalid');
                    isValid = false;
                    errors.push($(this).attr('name') + ' harus diisi');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang wajib diisi');
                return false;
            }
            
            // Konversi format uang sebelum submit
            $('[data-mask]').each(function() {
                let value = parseNumber($(this).val());
                $(this).val(value);
            });
            
            return true;
        });
        
        // Remove error class on input
        $('input, select, textarea').on('input change', function() {
            if ($(this).val().trim()) {
                $(this).removeClass('is-invalid');
            }
        });
    });
</script>

<?= $this->include('admin/templates/footer') ?>