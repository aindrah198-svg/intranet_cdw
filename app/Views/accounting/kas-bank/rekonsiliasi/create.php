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
                        <i class="fas fa-balance-scale me-2"></i> Tambah Rekonsiliasi Bank
                    </h2>
                    <p class="page-subtitle text-muted mb-0">
                        Proses pencocokan catatan transaksi bank dengan rekening koran
                    </p>
                </div>
                <div>
                    <a href="<?= site_url('accounting/kas-bank/rekonsiliasi') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                    </a>
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
                        <i class="fas fa-pencil-alt me-2"></i> Form Rekonsiliasi Bank
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?= site_url('accounting/kas-bank/rekonsiliasi/store') ?>" 
                          method="post" 
                          enctype="multipart/form-data"
                          id="rekonsiliasiForm">
                        
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

                        <div class="row">
                            <!-- Periode -->
                            <div class="col-md-6 mb-3">
                                <label for="periode" class="form-label">
                                    Periode <span class="text-danger">*</span>
                                </label>
                                <input type="month" 
                                       class="form-control <?= session('errors.periode') ? 'is-invalid' : '' ?>" 
                                       id="periode" 
                                       name="periode" 
                                       value="<?= old('periode', date('Y-m', strtotime($rekonsiliasi['periode']))) ?>"
                                       required>
                                <small class="text-muted">Pilih bulan dan tahun rekonsiliasi</small>
                                <?php if (session('errors.periode')): ?>
                                    <div class="invalid-feedback"><?= session('errors.periode') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Bank -->
                            <div class="col-md-6 mb-3">
                                <label for="coa_bank_id" class="form-label">
                                    Akun Bank <span class="text-danger">*</span>
                                </label>
                                <select class="form-select <?= session('errors.coa_bank_id') ? 'is-invalid' : '' ?>" 
                                        id="coa_bank_id" 
                                        name="coa_bank_id" 
                                        required>
                                    <option value="">-- Pilih Bank --</option>
                                    <?php foreach ($bankOptions as $bank): ?>
                                        <option value="<?= $bank['id'] ?>" 
                                            data-kode="<?= $bank['kode_akun'] ?>"
                                            data-nama="<?= $bank['nama_akun'] ?>"
                                            <?= old('coa_bank_id') == $bank['id'] ? 'selected' : '' ?>>
                                            <?= esc($bank['kode_akun']) ?> - <?= esc($bank['nama_akun']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (session('errors.coa_bank_id')): ?>
                                    <div class="invalid-feedback"><?= session('errors.coa_bank_id') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Tanggal Rekonsiliasi -->
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_rekonsiliasi" class="form-label">
                                    Tanggal Rekonsiliasi <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control <?= session('errors.tanggal_rekonsiliasi') ? 'is-invalid' : '' ?>" 
                                       id="tanggal_rekonsiliasi" 
                                       name="tanggal_rekonsiliasi" 
                                       value="<?= old('tanggal_rekonsiliasi', $rekonsiliasi['tanggal_rekonsiliasi']) ?>"
                                       required>
                                <?php if (session('errors.tanggal_rekonsiliasi')): ?>
                                    <div class="invalid-feedback"><?= session('errors.tanggal_rekonsiliasi') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Upload Rekening Koran -->
                            <div class="col-md-6 mb-3">
                                <label for="lampiran_rekening_koran" class="form-label">
                                    Upload Rekening Koran
                                </label>
                                <input type="file" 
                                       class="form-control" 
                                       id="lampiran_rekening_koran" 
                                       name="lampiran_rekening_koran"
                                       accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Format: PDF, JPG, PNG (Max: 5MB)</small>
                            </div>
                        </div>

                        <!-- Saldo Section -->
                        <div class="card bg-light mb-4">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0"><i class="fas fa-calculator me-2"></i> Saldo Bank vs Buku</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Saldo Bank -->
                                    <div class="col-md-6">
                                        <h6 class="text-primary">Menurut Bank</h6>
                                        <div class="mb-3">
                                            <label for="saldo_awal_bank" class="form-label">
                                                Saldo Awal Bank
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="saldo_awal_bank" 
                                                       name="saldo_awal_bank_display" 
                                                       value="<?= old('saldo_awal_bank_display', '0') ?>"
                                                       placeholder="0">
                                            </div>
                                            <small class="text-muted">Saldo awal periode menurut rekening koran</small>
                                        </div>
                                        <div class="mb-3">
                                            <label for="saldo_akhir_bank" class="form-label">
                                                Saldo Akhir Bank <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="text" 
                                                       class="form-control <?= session('errors.saldo_akhir_bank') ? 'is-invalid' : '' ?>" 
                                                       id="saldo_akhir_bank" 
                                                       name="saldo_akhir_bank_display" 
                                                       value="<?= old('saldo_akhir_bank_display', '0') ?>"
                                                       placeholder="0">
                                            </div>
                                            <small class="text-muted">Saldo akhir periode menurut rekening koran</small>
                                            <?php if (session('errors.saldo_akhir_bank')): ?>
                                                <div class="invalid-feedback d-block"><?= session('errors.saldo_akhir_bank') ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Saldo Buku -->
                                    <div class="col-md-6">
                                        <h6 class="text-success">Menurut Buku</h6>
                                        <div class="mb-3">
                                            <label for="saldo_awal_buku" class="form-label">
                                                Saldo Awal Buku
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="saldo_awal_buku" 
                                                       name="saldo_awal_buku_display" 
                                                       value="<?= old('saldo_awal_buku_display', '0') ?>"
                                                       readonly>
                                            </div>
                                            <small class="text-muted">Saldo awal periode menurut sistem (otomatis)</small>
                                        </div>
                                        <div class="mb-3">
                                            <label for="saldo_akhir_buku" class="form-label">
                                                Saldo Akhir Buku
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="saldo_akhir_buku" 
                                                       name="saldo_akhir_buku_display" 
                                                       value="<?= old('saldo_akhir_buku_display', '0') ?>"
                                                       readonly>
                                            </div>
                                            <small class="text-muted">Saldo akhir periode menurut sistem (otomatis)</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Selisih -->
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="alert" id="selisihAlert">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span><strong>Selisih:</strong></span>
                                                <span class="fw-bold" id="selisihValue">Rp 0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">
                                Keterangan
                            </label>
                            <textarea class="form-control" 
                                      id="keterangan" 
                                      name="keterangan" 
                                      rows="3"
                                      placeholder="Catatan tambahan tentang rekonsiliasi..."><?= old('keterangan') ?></textarea>
                        </div>

                        <!-- Info Box -->
                        <div class="alert alert-info">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="fas fa-info-circle fa-2x"></i>
                                </div>
                                <div>
                                    <strong>Informasi:</strong>
                                    <ul class="mb-0 mt-1">
                                        <li>Rekonsiliasi akan disimpan sebagai <strong>Draft</strong> terlebih dahulu.</li>
                                        <li>Setelah semua transaksi dicocokkan, Anda dapat <strong>Selesaikan</strong> dari halaman detail.</li>
                                        <li>Saldo awal dan akhir buku akan terisi otomatis berdasarkan data mutasi bank.</li>
                                        <li>Selisih harus 0 sebelum rekonsiliasi dapat diselesaikan.</li>
                                    </ul>
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
            <!-- Informasi Rekonsiliasi -->
            <div class="modern-card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i> Tentang Rekonsiliasi
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted">
                        Rekonsiliasi bank adalah proses mencocokkan catatan transaksi bank di sistem internal dengan catatan resmi dari bank (rekening koran).
                    </p>
                    <hr>
                    <h6>Komponen Rekonsiliasi:</h6>
                    <ul class="small">
                        <li><strong class="text-info">Setoran dalam perjalanan</strong>: Setoran yang sudah dicatat perusahaan tapi belum masuk bank</li>
                        <li><strong class="text-warning">Cek dalam edar</strong>: Cek yang sudah dikeluarkan tapi belum dicairkan</li>
                        <li><strong class="text-success">Penyesuaian bank</strong>: Biaya admin, bunga, dll dari bank</li>
                        <li><strong class="text-primary">Penyesuaian buku</strong>: Koreksi kesalahan pencatatan</li>
                    </ul>
                </div>
            </div>

            <!-- Langkah-langkah -->
            <div class="modern-card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-list-ol me-2"></i> Langkah-langkah
                    </h5>
                </div>
                <div class="card-body">
                    <ol class="small mb-0">
                        <li class="mb-2">Isi periode dan pilih bank yang akan direkonsiliasi</li>
                        <li class="mb-2">Masukkan saldo akhir bank dari rekening koran</li>
                        <li class="mb-2">Setelah disimpan, buka halaman detail</li>
                        <li class="mb-2">Cocokkan transaksi satu per satu (setoran dalam perjalanan, cek dalam edar)</li>
                        <li class="mb-2">Tambahkan penyesuaian jika ada (biaya admin, bunga, dll)</li>
                        <li class="mb-2">Pastikan selisih = 0, lalu selesaikan rekonsiliasi</li>
                    </ol>
                </div>
            </div>

            <!-- Daftar Bank -->
            <div class="modern-card">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-university me-2"></i> Akun Bank Tersedia
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">Akun kas/bank yang aktif:</p>
                    <ul class="list-unstyled">
                        <?php foreach ($bankOptions as $bank): ?>
                        <li class="mb-2 pb-2 border-bottom">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <span class="fw-bold"><?= esc($bank['kode_akun']) ?></span>
                                    <span class="text-muted"> - <?= esc($bank['nama_akun']) ?></span>
                                </div>
                                <small class="text-primary">Aktif</small>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const periodeInput = document.getElementById('periode');
    const bankSelect = document.getElementById('coa_bank_id');
    const saldoAwalBankInput = document.getElementById('saldo_awal_bank');
    const saldoAkhirBankInput = document.getElementById('saldo_akhir_bank');
    const saldoAwalBukuInput = document.getElementById('saldo_awal_buku');
    const saldoAkhirBukuInput = document.getElementById('saldo_akhir_buku');
    const selisihValue = document.getElementById('selisihValue');
    const selisihAlert = document.getElementById('selisihAlert');

    // Format currency input
    function formatRupiah(angka) {
        if (!angka || isNaN(angka)) return '0';
        let number = parseInt(angka);
        if (isNaN(number)) return '0';
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    // Bersihkan format currency
    function cleanCurrency(value) {
        if (!value) return 0;
        return parseInt(value.replace(/\./g, '')) || 0;
    }

    // Format input saldo
    [saldoAwalBankInput, saldoAkhirBankInput].forEach(input => {
        if (input) {
            // Set initial value jika ada
            let initialValue = input.value.replace(/[^\d]/g, '');
            if (initialValue && parseInt(initialValue) > 0) {
                input.value = formatRupiah(parseInt(initialValue));
            }

            // Event input untuk format
            input.addEventListener('input', function(e) {
                let value = this.value.replace(/[^\d]/g, '');
                if (value) {
                    let angka = parseInt(value);
                    this.value = formatRupiah(angka);
                } else {
                    this.value = '0';
                }
                hitungSelisih();
            });

            // Handle blur
            input.addEventListener('blur', function() {
                let value = this.value.replace(/[^\d]/g, '');
                if (value) {
                    let angka = parseInt(value);
                    this.value = formatRupiah(angka);
                } else {
                    this.value = '0';
                }
            });
        }
    });

    // Hitung selisih
    function hitungSelisih() {
        const saldoAkhirBank = cleanCurrency(saldoAkhirBankInput.value);
        const saldoAkhirBuku = cleanCurrency(saldoAkhirBukuInput.value);
        const selisih = saldoAkhirBank - saldoAkhirBuku;
        
        selisihValue.textContent = 'Rp ' + formatRupiah(Math.abs(selisih).toString()) + (selisih < 0 ? ' (Negatif)' : '');
        
        if (selisih === 0) {
            selisihAlert.className = 'alert alert-success';
            selisihValue.className = 'fw-bold text-success';
        } else {
            selisihAlert.className = 'alert alert-warning';
            selisihValue.className = 'fw-bold text-warning';
        }
    }

    // Load saldo buku berdasarkan periode dan bank
    function loadSaldoBuku() {
        const periode = periodeInput.value;
        const bankId = bankSelect.value;
        
        if (!periode || !bankId) {
            saldoAwalBukuInput.value = '0';
            saldoAkhirBukuInput.value = '0';
            hitungSelisih();
            return;
        }

        // Konversi periode (YYYY-MM) ke tanggal pertama bulan (YYYY-MM-01)
        const tanggalPeriode = periode + '-01';
        
        fetch('<?= site_url('accounting/kas-bank/rekonsiliasi/ajaxGetSaldoBank') ?>/' + bankId + '?tanggal=' + tanggalPeriode)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    saldoAwalBukuInput.value = formatRupiah(data.saldo_awal_raw.toString());
                    saldoAkhirBukuInput.value = formatRupiah(data.saldo_raw.toString());
                    hitungSelisih();
                }
            })
            .catch(error => {
                console.error('Error loading saldo buku:', error);
            });
    }

    // Event change pada periode dan bank
    if (periodeInput) {
        periodeInput.addEventListener('change', loadSaldoBuku);
    }

    if (bankSelect) {
        bankSelect.addEventListener('change', loadSaldoBuku);
    }

    // Form validation sebelum submit
    document.getElementById('rekonsiliasiForm')?.addEventListener('submit', function(e) {
        // Validasi periode
        const periode = periodeInput.value;
        if (!periode) {
            e.preventDefault();
            alert('Periode harus diisi');
            return false;
        }

        // Validasi bank
        if (!bankSelect.value) {
            e.preventDefault();
            alert('Pilih akun bank');
            return false;
        }

        // Validasi tanggal rekonsiliasi
        const tanggalRekonsiliasi = document.getElementById('tanggal_rekonsiliasi');
        if (!tanggalRekonsiliasi.value) {
            e.preventDefault();
            alert('Tanggal rekonsiliasi harus diisi');
            return false;
        }

        // Validasi saldo akhir bank
        const saldoAkhirBank = cleanCurrency(saldoAkhirBankInput.value);
        if (saldoAkhirBank < 0) {
            e.preventDefault();
            alert('Saldo akhir bank tidak boleh negatif');
            return false;
        }

        // Hapus input hidden yang lama jika ada
        const oldSaldoAwalBank = document.querySelector('input[name="saldo_awal_bank"]');
        if (oldSaldoAwalBank) {
            oldSaldoAwalBank.remove();
        }
        const oldSaldoAkhirBank = document.querySelector('input[name="saldo_akhir_bank"]');
        if (oldSaldoAkhirBank) {
            oldSaldoAkhirBank.remove();
        }
        const oldSaldoAkhirBuku = document.querySelector('input[name="saldo_akhir_buku"]');
        if (oldSaldoAkhirBuku) {
            oldSaldoAkhirBuku.remove();
        }

        // Buat input hidden dengan nilai bersih
        const saldoAwalBankClean = document.createElement('input');
        saldoAwalBankClean.type = 'hidden';
        saldoAwalBankClean.name = 'saldo_awal_bank';
        saldoAwalBankClean.value = cleanCurrency(saldoAwalBankInput.value);
        
        const saldoAkhirBankClean = document.createElement('input');
        saldoAkhirBankClean.type = 'hidden';
        saldoAkhirBankClean.name = 'saldo_akhir_bank';
        saldoAkhirBankClean.value = saldoAkhirBank;
        
        const saldoAkhirBukuClean = document.createElement('input');
        saldoAkhirBukuClean.type = 'hidden';
        saldoAkhirBukuClean.name = 'saldo_akhir_buku';
        saldoAkhirBukuClean.value = cleanCurrency(saldoAkhirBukuInput.value);
        
        this.appendChild(saldoAwalBankClean);
        this.appendChild(saldoAkhirBankClean);
        this.appendChild(saldoAkhirBukuClean);
        
        return true;
    });

    // Preview file lampiran
    document.getElementById('lampiran_rekening_koran')?.addEventListener('change', function(e) {
        const file = this.files[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file maksimal 5MB');
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

    // Validasi periode tidak boleh lebih dari bulan berjalan
    if (periodeInput) {
        const today = new Date();
        const currentMonth = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0');
        
        periodeInput.setAttribute('max', currentMonth);
        
        periodeInput.addEventListener('change', function() {
            if (this.value > currentMonth) {
                alert('Periode tidak boleh lebih dari bulan berjalan');
                this.value = currentMonth;
                loadSaldoBuku();
            }
        });
    }

    // Set default periode ke bulan lalu jika belum diisi
    if (periodeInput && !periodeInput.value) {
        const today = new Date();
        const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        const lastMonthStr = lastMonth.getFullYear() + '-' + String(lastMonth.getMonth() + 1).padStart(2, '0');
        periodeInput.value = lastMonthStr;
        loadSaldoBuku();
    }
});

// Fungsi format rupiah global
function formatRupiah(angka) {
    if (!angka || isNaN(angka)) return '0';
    let number = parseInt(angka);
    if (isNaN(number)) return '0';
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
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
.card.bg-light {
    background-color: #f8f9fa !important;
    border: none;
}

.card-header.bg-secondary {
    background-color: #6c757d !important;
    border-radius: 8px 8px 0 0 !important;
}

/* Alert styling */
.alert-info {
    background-color: #e7f3ff;
    border: none;
    border-left: 4px solid #4dabf7;
    border-radius: 8px;
}

.alert-success {
    background-color: #d4edda;
    border: none;
    border-left: 4px solid #28a745;
    border-radius: 8px;
}

.alert-warning {
    background-color: #fff3cd;
    border: none;
    border-left: 4px solid #ffc107;
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

/* List styling */
.list-unstyled li:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}

/* Readonly input */
input[readonly] {
    background-color: #e9ecef;
    cursor: not-allowed;
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