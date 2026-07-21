<?= $this->include('accounting/templates/header') ?>
<?= $this->include('accounting/templates/sidebar') ?>
<?= $this->include('accounting/templates/navbar') ?>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="page-title mb-1">
                        <?php if ($tipe == 'Pemasukan'): ?>
                            <i class="fas fa-plus-circle text-success me-2"></i> Pemasukan Kas Kecil
                        <?php else: ?>
                            <i class="fas fa-minus-circle text-danger me-2"></i> Pengeluaran Kas Kecil
                        <?php endif; ?>
                    </h2>
                    <p class="page-subtitle text-muted mb-0">
                        <?php if ($tipe == 'Pemasukan'): ?>
                            Catat pemasukan (pengisian kembali) dana kas kecil
                        <?php else: ?>
                            Catat pengeluaran dana kas kecil untuk operasional rutin
                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <a href="<?= site_url('accounting/kas-bank/kas-kecil') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Saldo Kas Kecil Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-info text-white shadow-lg">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <h4 class="text-white mb-2">Saldo Kas Kecil Saat Ini</h4>
                            <h1 class="display-4 text-white fw-bold mb-0">Rp <?= $saldo_kas_kecil ?></h1>
                            <?php if ($tipe == 'Pengeluaran'): ?>
                                <div class="mt-2">
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Pastikan saldo mencukupi untuk pengeluaran
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-lg-4 text-end">
                            <i class="fas fa-coins fa-5x text-white opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="row">
        <div class="col-md-8">
            <div class="modern-card">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-pencil-alt me-2"></i> Form Transaksi Kas Kecil
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?= site_url('accounting/kas-bank/kas-kecil/store') ?>" 
                          method="post" 
                          enctype="multipart/form-data"
                          id="kasKecilForm">
                        
                        <?= csrf_field() ?>
                        
                        <!-- Alert untuk error validation -->
                        <?php if (session()->has('errors')): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Terjadi kesalahan!</strong> Silakan periksa form anda.
                                <ul class="mt-2 mb-0">
                                    <?php foreach (session('errors') as $error): ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Hidden Fields -->
                        <input type="hidden" name="tipe" value="<?= $tipe ?>">
                        
                        <div class="row">
                            <!-- Tanggal Transaksi -->
                            <div class="col-md-6 mb-3">
                                <label for="tanggal" class="form-label">
                                    Tanggal Transaksi <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control <?= session('errors.tanggal') ? 'is-invalid' : '' ?>" 
                                       id="tanggal" 
                                       name="tanggal" 
                                       value="<?= old('tanggal', $transaksi['tanggal']) ?>"
                                       required>
                                <?php if (session('errors.tanggal')): ?>
                                    <div class="invalid-feedback"><?= session('errors.tanggal') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Nomor Bukti -->
                            <div class="col-md-6 mb-3">
                                <label for="no_bukti" class="form-label">
                                    Nomor Bukti
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="no_bukti" 
                                       name="no_bukti" 
                                       value="<?= old('no_bukti') ?>"
                                       placeholder="No. Nota / Kwitansi">
                                <small class="text-muted">Opsional, nomor bukti transaksi</small>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Akun Lawan -->
                            <div class="col-md-12 mb-3">
                                <label for="coa_lawan_id" class="form-label">
                                    Akun Lawan <span class="text-danger">*</span>
                                </label>
                                <select class="form-select <?= session('errors.coa_lawan_id') ? 'is-invalid' : '' ?>" 
                                        id="coa_lawan_id" 
                                        name="coa_lawan_id" 
                                        required>
                                    <option value="">-- Pilih Akun Lawan --</option>
                                    <?php foreach ($coaLawanOptions as $akun): ?>
                                        <option value="<?= $akun['id'] ?>" 
                                            data-tipe="<?= $akun['tipe_akun'] ?>"
                                            data-kode="<?= $akun['kode_akun'] ?>"
                                            <?= old('coa_lawan_id') == $akun['id'] ? 'selected' : '' ?>>
                                            <?= esc($akun['kode_akun']) ?> - <?= esc($akun['nama_akun']) ?> 
                                            (<?= $akun['tipe_akun'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($tipe == 'Pemasukan'): ?>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Akun sumber dana (Kas/Bank) untuk pengisian kembali
                                    </small>
                                <?php else: ?>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Akun beban atau aset yang menjadi tujuan pengeluaran
                                    </small>
                                <?php endif; ?>
                                <?php if (session('errors.coa_lawan_id')): ?>
                                    <div class="invalid-feedback"><?= session('errors.coa_lawan_id') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Karyawan -->
                            <div class="col-md-6 mb-3">
                                <label for="karyawan_id" class="form-label">
                                    Karyawan
                                </label>
                                <select class="form-select" id="karyawan_id" name="karyawan_id">
                                    <option value="">-- Pilih Karyawan (Opsional) --</option>
                                    <?php foreach ($karyawanOptions as $karyawan): ?>
                                        <?php 
                                        $karyawanId = is_object($karyawan) ? $karyawan->id : ($karyawan['id'] ?? '');
                                        $karyawanNik = is_object($karyawan) ? ($karyawan->nik ?? '') : ($karyawan['nik'] ?? '');
                                        $karyawanNama = is_object($karyawan) ? ($karyawan->nama_lengkap ?? '') : ($karyawan['nama_lengkap'] ?? '');
                                        $karyawanJabatan = is_object($karyawan) ? ($karyawan->jabatan ?? '') : ($karyawan['jabatan'] ?? '');
                                        ?>
                                        <option value="<?= $karyawanId ?>" 
                                            data-nik="<?= $karyawanNik ?>"
                                            data-jabatan="<?= $karyawanJabatan ?>"
                                            <?= old('karyawan_id') == $karyawanId ? 'selected' : '' ?>>
                                            <?= esc($karyawanNik) ?> - <?= esc($karyawanNama) ?> 
                                            (<?= esc($karyawanJabatan) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Opsional, pilih jika terkait karyawan tertentu</small>
                            </div>

                            <!-- Pilih Proyek/SPK -->
                            <div class="col-md-6 mb-3">
                                <label for="spk_id" class="form-label">
                                    <i class="fas fa-project-diagram me-1"></i> Proyek / SPK
                                </label>
                                <select class="form-select" id="spk_id" name="spk_id">
                                    <option value="">-- Pilih Proyek (Opsional) --</option>
                                    <?php if (isset($spkOptions) && !empty($spkOptions)): ?>
                                        <?php foreach ($spkOptions as $spk): ?>
                                            <?php 
                                            // Handle baik object maupun array
                                            $spkId = is_object($spk) ? ($spk->id ?? '') : ($spk['id'] ?? '');
                                            $spkNomor = is_object($spk) ? ($spk->nomor_spk ?? '') : ($spk['nomor_spk'] ?? '');
                                            $spkJudul = is_object($spk) ? ($spk->judul_pekerjaan ?? '') : ($spk['judul_pekerjaan'] ?? '');
                                            $spkStatus = is_object($spk) ? ($spk->status ?? '') : ($spk['status'] ?? '');
                                            ?>
                                            <option value="<?= $spkId ?>" 
                                                    data-nomor="<?= $spkNomor ?>"
                                                    <?= old('spk_id') == $spkId ? 'selected' : '' ?>>
                                                <?= esc($spkNomor) ?> - <?= esc($spkJudul) ?>
                                                (<?= esc($spkStatus) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <small class="text-muted">Opsional, pilih jika pengeluaran terkait proyek tertentu</small>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Jumlah -->
                            <div class="col-md-6 mb-3">
                                <label for="jumlah" class="form-label">
                                    Jumlah <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" 
                                           class="form-control <?= session('errors.jumlah') ? 'is-invalid' : '' ?>" 
                                           id="jumlah" 
                                           name="jumlah" 
                                           value="<?= old('jumlah', $transaksi['jumlah'] > 0 ? number_format($transaksi['jumlah'], 0, ',', '.') : '0') ?>"
                                           placeholder="0"
                                           required>
                                </div>
                                <div id="terbilang" class="small text-muted mt-1"></div>
                                <?php if (session('errors.jumlah')): ?>
                                    <div class="invalid-feedback d-block"><?= session('errors.jumlah') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Metode Imprest (Hidden) -->
                            <input type="hidden" name="metode_imprest" value="1">
                        </div>

                        <!-- Keterangan -->
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">
                                Keterangan <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control <?= session('errors.keterangan') ? 'is-invalid' : '' ?>" 
                                      id="keterangan" 
                                      name="keterangan" 
                                      rows="3"
                                      placeholder="<?= $tipe == 'Pemasukan' ? 'Pengisian kembali kas kecil...' : 'Detail pengeluaran...' ?>"
                                      required><?= old('keterangan') ?></textarea>
                            <?php if (session('errors.keterangan')): ?>
                                <div class="invalid-feedback"><?= session('errors.keterangan') ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Upload Lampiran -->
                        <div class="mb-3">
                            <label for="lampiran" class="form-label">
                                Upload Bukti / Nota
                            </label>
                            <input type="file" 
                                   class="form-control" 
                                   id="lampiran" 
                                   name="lampiran"
                                   accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Format: PDF, JPG, PNG (Max: 2MB)</small>
                        </div>

                        <!-- Info Box - Dinamis berdasarkan tipe -->
                        <div class="alert alert-info" id="infoBox">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="fas fa-info-circle fa-2x"></i>
                                </div>
                                <div id="infoContent">
                                    <?php if ($tipe == 'Pemasukan'): ?>
                                        <strong>Informasi Pemasukan (Pengisian Kembali):</strong>
                                        <ul class="mb-0 mt-1">
                                            <li>Transaksi akan menambah saldo kas kecil.</li>
                                            <li>Akun lawan harus merupakan akun Kas/Bank (sumber dana).</li>
                                            <li>Setelah disimpan, Anda perlu <strong>Posting</strong> ke jurnal.</li>
                                            <li>Untuk pengisian kembali otomatis, gunakan menu <strong>Pengisian Kembali</strong>.</li>
                                        </ul>
                                    <?php else: ?>
                                        <strong>Informasi Pengeluaran:</strong>
                                        <ul class="mb-0 mt-1">
                                            <li>Transaksi akan mengurangi saldo kas kecil.</li>
                                            <li>Pastikan saldo mencukupi sebelum menyimpan.</li>
                                            <li>Akun lawan harus merupakan akun Beban atau Aset non-kas.</li>
                                            <li>Setelah disimpan, Anda perlu <strong>Posting</strong> ke jurnal.</li>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="d-flex justify-content-end mt-4">
                            <button type="reset" class="btn btn-secondary me-2">
                                <i class="fas fa-undo me-1"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Draft
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Side Panel -->
        <div class="col-md-4">
            <!-- Informasi Kas Kecil -->
            <div class="modern-card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i> Tentang Kas Kecil
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted">
                        Kas kecil menggunakan <strong>metode imprest (dana tetap)</strong>. 
                        Jumlah kas kecil selalu tetap dan akan diisi ulang jika hampir habis.
                    </p>
                    <hr>
                    <h6>Karakteristik:</h6>
                    <ul class="small">
                        <li><strong class="text-success">Pemasukan</strong>: Pengisian kembali dari Kas/Bank</li>
                        <li><strong class="text-danger">Pengeluaran</strong>: Untuk operasional rutin</li>
                        <li><strong>Saldo:</strong> Rp <?= $saldo_kas_kecil ?></li>
                    </ul>
                </div>
            </div>

            <!-- Contoh Penggunaan -->
            <div class="modern-card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i> Contoh Penggunaan
                    </h5>
                </div>
                <div class="card-body">
                    <?php if ($tipe == 'Pemasukan'): ?>
                        <h6 class="text-success">Pengisian Kembali:</h6>
                        <ul class="small">
                            <li>Setoran tunai dari Bank ke Kas Kecil</li>
                            <li>Pengambilan uang dari Bank Mandiri untuk kas kecil</li>
                            <li>Setelah akumulasi pengeluaran mencapai jumlah tertentu</li>
                        </ul>
                        <div class="bg-light p-2 rounded mt-2">
                            <small class="text-muted">
                                <i class="fas fa-arrow-right me-1"></i>
                                <strong>Akun Lawan:</strong> Kas di Bank (1-11xx)
                            </small>
                        </div>
                    <?php else: ?>
                        <h6 class="text-danger">Pengeluaran Rutin:</h6>
                        <ul class="small">
                            <li>Pembelian snack untuk rapat</li>
                            <li>Pembayaran parkir, tol, bensin</li>
                            <li>Pembelian ATK kecil-kecilan</li>
                            <li>Biaya transportasi mendadak</li>
                        </ul>
                        <div class="bg-light p-2 rounded mt-2">
                            <small class="text-muted">
                                <i class="fas fa-arrow-right me-1"></i>
                                <strong>Akun Lawan:</strong> Beban (5-xxxx) atau Perlengkapan (1-xxxx)
                            </small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Akun yang Sering Digunakan -->
            <div class="modern-card">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-book me-2"></i> Akun yang Sering Digunakan
                    </h5>
                </div>
                <div class="card-body">
                    <?php if ($tipe == 'Pemasukan'): ?>
                        <h6 class="text-primary">Akun Sumber Dana (Kas/Bank):</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2 pb-2 border-bottom">
                                <span class="badge bg-info">1-1101</span> Kas Kecil
                                <small class="text-muted d-block">(Debit saat pengisian)</small>
                            </li>
                            <li class="mb-2 pb-2 border-bottom">
                                <span class="badge bg-info">1-1102</span> Kas di Bank - BCA
                            </li>
                            <li class="mb-2 pb-2 border-bottom">
                                <span class="badge bg-info">1-1103</span> Kas di Bank - Mandiri
                            </li>
                        </ul>
                    <?php else: ?>
                        <h6 class="text-primary">Akun Beban Populer:</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2 pb-2 border-bottom">
                                <span class="badge bg-warning">5-1401</span> Bensin dan Tol
                            </li>
                            <li class="mb-2 pb-2 border-bottom">
                                <span class="badge bg-warning">5-1503</span> ATK dan Perlengkapan
                            </li>
                            <li class="mb-2 pb-2 border-bottom">
                                <span class="badge bg-warning">5-1301</span> Maintenance Kendaraan
                            </li>
                            <li class="mb-2 pb-2 border-bottom">
                                <span class="badge bg-warning">5-1602</span> Entertainment
                            </li>
                            <li class="mb-2">
                                <span class="badge bg-warning">5-1501</span> Listrik, Air, Telepon
                            </li>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const jumlahInput = document.getElementById('jumlah');
    const terbilangDiv = document.getElementById('terbilang');
    const saldoSaatIni = <?= $stats['saldo_terkini'] ?? 0 ?>;
    const tipe = '<?= $tipe ?>';

    // Fungsi format rupiah
    function formatRupiah(angka) {
        if (!angka || isNaN(angka)) return '0';
        let number = parseInt(angka);
        if (isNaN(number)) return '0';
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    // Fungsi bersihkan format rupiah
    function cleanRupiah(value) {
        if (!value) return 0;
        return parseInt(value.replace(/\./g, '')) || 0;
    }

    // Format currency input
    if (jumlahInput) {
        // Simpan nilai asli untuk referensi
        let rawValue = cleanRupiah(jumlahInput.value);
        
        // Set initial value jika ada
        if (rawValue > 0) {
            jumlahInput.value = formatRupiah(rawValue);
            getTerbilang(rawValue);
        }

        // Event input untuk format
        jumlahInput.addEventListener('input', function(e) {
            let value = this.value.replace(/[^\d]/g, '');
            
            if (value) {
                let angka = parseInt(value);
                this.value = formatRupiah(angka);
                getTerbilang(angka);
            } else {
                this.value = '0';
                terbilangDiv.innerHTML = '';
            }
        });

        // Handle blur
        jumlahInput.addEventListener('blur', function() {
            let value = this.value.replace(/[^\d]/g, '');
            if (value) {
                let angka = parseInt(value);
                this.value = formatRupiah(angka);
            } else {
                this.value = '0';
            }
        });
    }

    // Function get terbilang via AJAX
    function getTerbilang(angka) {
        if (angka > 0) {
            fetch('<?= site_url('accounting/kas-bank/kas-kecil/ajaxGetTerbilang') ?>?jumlah=' + angka)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        terbilangDiv.innerHTML = '<i class="fas fa-pencil-alt me-1"></i> ' + data.terbilang;
                    } else {
                        terbilangDiv.innerHTML = '';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        } else {
            terbilangDiv.innerHTML = '';
        }
    }

    // Form validation sebelum submit
    document.getElementById('kasKecilForm')?.addEventListener('submit', function(e) {
        const jumlahInput = document.getElementById('jumlah');
        const jumlahBersih = cleanRupiah(jumlahInput.value);
        
        // Validasi jumlah
        if (jumlahBersih <= 0) {
            e.preventDefault();
            alert('Jumlah harus lebih besar dari 0');
            return false;
        }

        // Validasi akun lawan
        const coaLawan = document.getElementById('coa_lawan_id');
        if (!coaLawan || !coaLawan.value) {
            e.preventDefault();
            alert('Pilih akun lawan');
            return false;
        }

        // Validasi saldo untuk pengeluaran
        if (tipe === 'Pengeluaran' && jumlahBersih > saldoSaatIni) {
            e.preventDefault();
            alert('Saldo kas kecil tidak mencukupi!\n\n' +
                  'Saldo saat ini: Rp ' + formatRupiah(saldoSaatIni.toString()) + '\n' +
                  'Jumlah pengeluaran: Rp ' + formatRupiah(jumlahBersih.toString()) + '\n\n' +
                  'Lakukan pengisian kembali terlebih dahulu.');
            return false;
        }

        // Set nilai bersih ke input sebelum submit
        jumlahInput.value = jumlahBersih;
        
        return true;
    });

    // Preview file lampiran
    document.getElementById('lampiran')?.addEventListener('change', function(e) {
        const file = this.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file maksimal 2MB');
                this.value = '';
                return;
            }
            
            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
            if (!allowedTypes.includes(file.type)) {
                alert('Tipe file harus PDF, JPG, atau PNG');
                this.value = '';
                return;
            }
        }
    });

    // Tampilkan info karyawan saat dipilih
    const karyawanSelect = document.getElementById('karyawan_id');
    if (karyawanSelect) {
        karyawanSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption && selectedOption.value) {
                const nik = selectedOption.dataset.nik;
                const jabatan = selectedOption.dataset.jabatan;
                console.log('Karyawan dipilih - NIK:', nik, 'Jabatan:', jabatan);
            }
        });
    }

    // Tampilkan info akun lawan saat dipilih
    const coaLawanSelect = document.getElementById('coa_lawan_id');
    if (coaLawanSelect) {
        coaLawanSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption && selectedOption.value) {
                const tipeAkun = selectedOption.dataset.tipe;
                const kodeAkun = selectedOption.dataset.kode;
                
                // Validasi untuk pengeluaran
                if (tipe === 'Pengeluaran') {
                    if (kodeAkun && kodeAkun.startsWith('1-11')) {
                        alert('Untuk pengeluaran, akun lawan tidak boleh merupakan akun Kas/Bank (1-11xx)');
                        this.value = '';
                    }
                }
            }
        });
    }
});
</script>

<style>
/* Custom styles untuk form */
.modern-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.modern-card .card-header {
    border-bottom: 1px solid #e0e0e0;
    background-color: white;
    border-radius: 10px 10px 0 0 !important;
}

.modern-card .card-body {
    padding: 20px;
}

/* Form styling */
.form-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #dee2e6;
    padding: 0.6rem 1rem;
}

.form-control:focus, .form-select:focus {
    border-color: #4dabf7;
    box-shadow: 0 0 0 0.2rem rgba(77, 171, 247, 0.25);
}

/* Input group styling */
.input-group-text {
    border-radius: 8px 0 0 8px;
    background-color: #f8f9fa;
}

/* Card styling */
.bg-gradient-info {
    background: linear-gradient(45deg, #17a2b8, #0d6efd);
}

/* Alert styling */
.alert-info {
    background-color: #e7f3ff;
    border: none;
    border-left: 4px solid #4dabf7;
    border-radius: 8px;
}

/* Button styling */
.btn {
    padding: 0.6rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
}

.btn-primary {
    background-color: #4dabf7;
    border: none;
}

.btn-primary:hover {
    background-color: #3b8cbf;
}

.btn-secondary {
    background-color: #6c757d;
    border: none;
}

/* Badge styling */
.badge {
    padding: 0.4rem 0.6rem;
    font-weight: 500;
}

/* Border bottom untuk list */
.border-bottom {
    border-bottom: 1px solid #dee2e6 !important;
}

/* Background light */
.bg-light {
    background-color: #f8f9fa !important;
}

/* Animation */
.alert-notification {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>

<?= $this->include('accounting/templates/footer') ?>