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
                        <i class="fas fa-edit me-2 text-warning"></i> Edit Transfer Internal
                    </h2>
                    <p class="page-subtitle text-muted mb-0">
                        Edit transfer <?= $transfer['kode_transfer'] ?>
                    </p>
                </div>
                <div>
                    <a href="<?= site_url('accounting/kas-bank/transfer-internal/detail/' . $transfer['id']) ?>" class="btn btn-info me-2">
                        <i class="fas fa-eye me-1"></i> Detail
                    </a>
                    <a href="<?= site_url('accounting/kas-bank/transfer-internal') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Warning -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning">
                <div class="d-flex">
                    <div class="me-3">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                    <div>
                        <strong>Perhatian!</strong> Anda sedang mengedit transfer dengan status <strong>Draft</strong>.
                        Setelah diedit, Anda perlu memposting ulang jika ingin memasukkan ke jurnal.
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
                        <i class="fas fa-pencil-alt me-2"></i> Form Edit Transfer Internal
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?= site_url('accounting/kas-bank/transfer-internal/update/' . $transfer['id']) ?>" 
                          method="post" 
                          enctype="multipart/form-data"
                          id="transferForm">
                        
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

                        <!-- Alert untuk warning (saldo tidak cukup) -->
                        <?php if (session()->has('warning')): ?>
                            <div class="alert alert-warning alert-dismissible fade show">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?= session('warning') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <!-- Tanggal Transfer -->
                            <div class="col-md-6 mb-3">
                                <label for="tanggal" class="form-label">
                                    Tanggal Transfer <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control <?= session('errors.tanggal') ? 'is-invalid' : '' ?>" 
                                       id="tanggal" 
                                       name="tanggal" 
                                       value="<?= old('tanggal', $transfer['tanggal']) ?>"
                                       required>
                                <?php if (session('errors.tanggal')): ?>
                                    <div class="invalid-feedback"><?= session('errors.tanggal') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Nomor Referensi -->
                            <div class="col-md-6 mb-3">
                                <label for="no_referensi" class="form-label">
                                    Nomor Referensi
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="no_referensi" 
                                       name="no_referensi" 
                                       value="<?= old('no_referensi', $transfer['no_referensi']) ?>"
                                       placeholder="No. Bukti Transfer">
                                <small class="text-muted">Opsional, nomor referensi internal</small>
                            </div>
                        </div>

                        <!-- Akun Sumber -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="coa_id_sumber" class="form-label">
                                    Akun Sumber <span class="text-danger">*</span>
                                </label>
                                <select class="form-select <?= session('errors.coa_id_sumber') ? 'is-invalid' : '' ?>" 
                                        id="coa_id_sumber" 
                                        name="coa_id_sumber" 
                                        required>
                                    <option value="">-- Pilih Akun Sumber --</option>
                                    <?php foreach ($coaOptions as $akun): ?>
                                        <option value="<?= $akun['id'] ?>" 
                                            <?= old('coa_id_sumber', $transfer['coa_id_sumber']) == $akun['id'] ? 'selected' : '' ?>>
                                            <?= esc($akun['kode_akun']) ?> - <?= esc($akun['nama_akun']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (session('errors.coa_id_sumber')): ?>
                                    <div class="invalid-feedback"><?= session('errors.coa_id_sumber') ?></div>
                                <?php endif; ?>
                                
                                <!-- Info saldo sumber -->
                                <div id="infoSaldoSumber" class="mt-2 small" style="display: none;">
                                    <span class="text-muted">Saldo tersedia:</span>
                                    <span id="saldoSumber" class="fw-bold text-primary">Rp 0</span>
                                </div>
                            </div>

                            <!-- Akun Tujuan -->
                            <div class="col-md-6 mb-3">
                                <label for="coa_id_tujuan" class="form-label">
                                    Akun Tujuan <span class="text-danger">*</span>
                                </label>
                                <select class="form-select <?= session('errors.coa_id_tujuan') ? 'is-invalid' : '' ?>" 
                                        id="coa_id_tujuan" 
                                        name="coa_id_tujuan" 
                                        required>
                                    <option value="">-- Pilih Akun Tujuan --</option>
                                    <?php foreach ($coaOptions as $akun): ?>
                                        <option value="<?= $akun['id'] ?>" 
                                            <?= old('coa_id_tujuan', $transfer['coa_id_tujuan']) == $akun['id'] ? 'selected' : '' ?>>
                                            <?= esc($akun['kode_akun']) ?> - <?= esc($akun['nama_akun']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (session('errors.coa_id_tujuan')): ?>
                                    <div class="invalid-feedback"><?= session('errors.coa_id_tujuan') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Bank Asal dan Tujuan (Opsional) -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="bank_asal" class="form-label">
                                    Bank Asal (Detail)
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="bank_asal" 
                                       name="bank_asal" 
                                       value="<?= old('bank_asal', $transfer['bank_asal']) ?>"
                                       placeholder="Contoh: BCA / Mandiri / Kas Kecil">
                                <small class="text-muted">Opsional, informasi tambahan</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="bank_tujuan" class="form-label">
                                    Bank Tujuan (Detail)
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="bank_tujuan" 
                                       name="bank_tujuan" 
                                       value="<?= old('bank_tujuan', $transfer['bank_tujuan']) ?>"
                                       placeholder="Contoh: BCA / Mandiri / Kas Kecil">
                                <small class="text-muted">Opsional, informasi tambahan</small>
                            </div>
                        </div>

                        <!-- Jumlah Transfer -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="jumlah" class="form-label">
                                    Jumlah Transfer <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" 
                                           class="form-control <?= session('errors.jumlah') ? 'is-invalid' : '' ?>" 
                                           id="jumlah" 
                                           name="jumlah_display" 
                                           value="<?= old('jumlah_display', number_format($transfer['jumlah'], 0, ',', '.')) ?>"
                                           placeholder="0"
                                           required>
                                </div>
                                <?php if (session('errors.jumlah')): ?>
                                    <div class="invalid-feedback d-block"><?= session('errors.jumlah') ?></div>
                                <?php endif; ?>
                                
                                <!-- Teks terbilang -->
                                <div id="terbilang" class="mt-2 text-muted fst-italic" <?= $transfer['jumlah'] > 0 ? '' : 'style="display: none;"' ?>>
                                    <small><i class="fas fa-quote-right me-1"></i> <span id="terbilangText"><?= $transfer['terbilang'] ?? '' ?></span></small>
                                </div>
                            </div>

                            <!-- Upload Lampiran -->
                            <div class="col-md-6 mb-3">
                                <label for="lampiran" class="form-label">
                                    Lampiran Bukti Transfer
                                </label>
                                <input type="file" 
                                       class="form-control" 
                                       id="lampiran" 
                                       name="lampiran"
                                       accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Format: PDF, JPG, PNG (Max: 2MB). Kosongkan jika tidak ingin mengubah.</small>
                                
                                <?php if (!empty($transfer['lampiran'])): ?>
                                    <div class="mt-2 p-2 bg-light rounded">
                                        <small>
                                            <i class="fas fa-paperclip me-1"></i>
                                            Lampiran saat ini: 
                                            <a href="<?= base_url($transfer['lampiran']) ?>" target="_blank" class="text-primary">
                                                Lihat File
                                            </a>
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
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
                                      placeholder="Deskripsi transfer internal..."
                                      required><?= old('keterangan', $transfer['keterangan']) ?></textarea>
                            <?php if (session('errors.keterangan')): ?>
                                <div class="invalid-feedback"><?= session('errors.keterangan') ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Peringatan Validasi Saldo -->
                        <div class="alert alert-warning" id="saldoWarning" style="display: none;">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <span id="saldoWarningMessage"></span>
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
                                        <li>Transfer internal ini berstatus <strong>Draft</strong> dan dapat diedit.</li>
                                        <li>Setelah selesai mengedit, Anda dapat <strong>Posting</strong> ulang ke jurnal.</li>
                                        <li><strong>Penting:</strong> Pastikan saldo akun sumber mencukupi.</li>
                                        <li>Jurnal yang akan dibuat: Debit (Akun Tujuan), Kredit (Akun Sumber)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="d-flex justify-content-end mt-4">
                            <a href="<?= site_url('accounting/kas-bank/transfer-internal/detail/' . $transfer['id']) ?>" class="btn btn-secondary me-2">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-warning" id="submitBtn">
                                <i class="fas fa-save me-1"></i> Update Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Side Panel -->
        <div class="col-md-4">
            <!-- Informasi Transfer -->
            <div class="modern-card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i> Informasi Transfer
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted">Kode Transfer</td>
                            <td class="text-end fw-bold"><?= $transfer['kode_transfer'] ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td class="text-end">
                                <span class="badge bg-secondary"><?= $transfer['status'] ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Dibuat Oleh</td>
                            <td class="text-end"><?= $transfer['creator_name'] ?? '-' ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Dibuat Tanggal</td>
                            <td class="text-end"><?= date('d/m/Y H:i', strtotime($transfer['created_at'])) ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Preview Jurnal -->
            <div class="modern-card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-journal-whills me-2"></i> Preview Jurnal
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">Jurnal yang akan dibuat setelah posting:</p>
                    <div class="bg-light p-3 rounded">
                        <div class="mb-2">
                            <span class="badge bg-success">Debit</span>
                            <span id="previewAkunTujuan" class="ms-2"><?= $transfer['nama_akun_tujuan'] ?? 'Akun Tujuan' ?></span>
                            <span class="float-end text-success">Rp <span id="previewJumlah"><?= number_format($transfer['jumlah'], 0, ',', '.') ?></span></span>
                        </div>
                        <div>
                            <span class="badge bg-danger">Kredit</span>
                            <span id="previewAkunSumber" class="ms-2"><?= $transfer['nama_akun_sumber'] ?? 'Akun Sumber' ?></span>
                            <span class="float-end text-danger">Rp <span id="previewJumlahKredit"><?= number_format($transfer['jumlah'], 0, ',', '.') ?></span></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Akun Kas/Bank -->
            <div class="modern-card">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-university me-2"></i> Akun Kas/Bank Tersedia
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">Pilih akun sumber dan tujuan dari daftar berikut:</p>
                    <ul class="list-unstyled" style="max-height: 300px; overflow-y: auto;">
                        <?php foreach ($coaOptions as $akun): ?>
                        <li class="mb-2 pb-2 border-bottom">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="fw-bold"><?= esc($akun['kode_akun']) ?></span>
                                    <span class="text-muted d-block small"><?= esc($akun['nama_akun']) ?></span>
                                </div>
                                <?php if ($akun['id'] == $transfer['coa_id_sumber']): ?>
                                    <span class="badge bg-danger">Sumber</span>
                                <?php elseif ($akun['id'] == $transfer['coa_id_tujuan']): ?>
                                    <span class="badge bg-success">Tujuan</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">Tersedia</span>
                                <?php endif; ?>
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
    const jumlahInput = document.getElementById('jumlah');
    const coaSumber = document.getElementById('coa_id_sumber');
    const coaTujuan = document.getElementById('coa_id_tujuan');
    const tanggal = document.getElementById('tanggal');
    const infoSaldoSumber = document.getElementById('infoSaldoSumber');
    const saldoSumberSpan = document.getElementById('saldoSumber');
    const saldoWarning = document.getElementById('saldoWarning');
    const saldoWarningMessage = document.getElementById('saldoWarningMessage');
    const terbilangDiv = document.getElementById('terbilang');
    const terbilangText = document.getElementById('terbilangText');
    const previewAkunSumber = document.getElementById('previewAkunSumber');
    const previewAkunTujuan = document.getElementById('previewAkunTujuan');
    const previewJumlah = document.getElementById('previewJumlah');
    const previewJumlahKredit = document.getElementById('previewJumlahKredit');

    // Format currency input
    if (jumlahInput) {
        jumlahInput.addEventListener('input', function(e) {
            let value = this.value.replace(/[^\d]/g, '');
            
            if (value) {
                let angka = parseInt(value);
                this.value = formatRupiah(angka);
                getTerbilang(angka);
                updatePreviewJumlah(angka);
                validateSaldo();
            } else {
                this.value = '0';
                terbilangDiv.style.display = 'none';
                updatePreviewJumlah(0);
                validateSaldo();
            }
        });

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

    // Update preview jumlah
    function updatePreviewJumlah(angka) {
        if (previewJumlah) {
            previewJumlah.textContent = formatRupiah(angka);
        }
        if (previewJumlahKredit) {
            previewJumlahKredit.textContent = formatRupiah(angka);
        }
    }

    // Get terbilang via AJAX
    function getTerbilang(angka) {
        if (angka <= 0) {
            terbilangDiv.style.display = 'none';
            return;
        }

        fetch('<?= site_url('accounting/kas-bank/transfer-internal/ajax-get-terbilang') ?>?jumlah=' + angka, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                terbilangText.textContent = data.terbilang;
                terbilangDiv.style.display = 'block';
            } else {
                terbilangDiv.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            terbilangDiv.style.display = 'none';
        });
    }

    // Get saldo sumber
    function getSaldoSumber() {
        if (!coaSumber.value || !tanggal.value) {
            infoSaldoSumber.style.display = 'none';
            return;
        }

        fetch('<?= site_url('accounting/kas-bank/transfer-internal/ajax-get-saldo-sumber') ?>?coa_sumber_id=' + coaSumber.value + '&tanggal=' + tanggal.value, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                saldoSumberSpan.textContent = 'Rp ' + data.saldo;
                infoSaldoSumber.style.display = 'block';
                
                // Simpan saldo untuk validasi
                saldoSumberSpan.dataset.saldo = data.saldo_raw;
                validateSaldo();
            } else {
                infoSaldoSumber.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            infoSaldoSumber.style.display = 'none';
        });
    }

    // Validasi saldo
    function validateSaldo() {
        if (!coaSumber.value || !jumlahInput.value) {
            saldoWarning.style.display = 'none';
            return;
        }

        const saldo = parseFloat(saldoSumberSpan.dataset.saldo || 0);
        const jumlahBersih = parseInt(jumlahInput.value.replace(/[^\d]/g, '')) || 0;

        if (jumlahBersih > 0 && saldo < jumlahBersih) {
            saldoWarningMessage.innerHTML = `Saldo akun sumber tidak mencukupi!<br>
                Saldo tersedia: <strong>Rp ${formatRupiah(saldo)}</strong><br>
                Jumlah transfer: <strong>Rp ${formatRupiah(jumlahBersih)}</strong>`;
            saldoWarning.style.display = 'block';
            
            // Disable submit button
            document.getElementById('submitBtn').disabled = true;
        } else {
            saldoWarning.style.display = 'none';
            document.getElementById('submitBtn').disabled = false;
        }
    }

    // Update preview akun
    function updatePreviewAkun() {
        if (coaSumber.value) {
            const selected = coaSumber.options[coaSumber.selectedIndex];
            previewAkunSumber.textContent = selected.text.split(' - ')[1] || 'Akun Sumber';
        } else {
            previewAkunSumber.textContent = 'Akun Sumber';
        }

        if (coaTujuan.value) {
            const selected = coaTujuan.options[coaTujuan.selectedIndex];
            previewAkunTujuan.textContent = selected.text.split(' - ')[1] || 'Akun Tujuan';
        } else {
            previewAkunTujuan.textContent = 'Akun Tujuan';
        }
    }

    // Event listeners
    coaSumber.addEventListener('change', function() {
        getSaldoSumber();
        updatePreviewAkun();
    });

    coaTujuan.addEventListener('change', updatePreviewAkun);
    tanggal.addEventListener('change', getSaldoSumber);

    // Form validation sebelum submit
    document.getElementById('transferForm')?.addEventListener('submit', function(e) {
        const jumlahBersih = parseInt(jumlahInput.value.replace(/[^\d]/g, '')) || 0;
        
        // Validasi jumlah
        if (jumlahBersih <= 0) {
            e.preventDefault();
            alert('Jumlah transfer harus lebih besar dari 0');
            return false;
        }

        // Validasi akun sumber dan tujuan
        if (!coaSumber.value) {
            e.preventDefault();
            alert('Pilih akun sumber');
            return false;
        }

        if (!coaTujuan.value) {
            e.preventDefault();
            alert('Pilih akun tujuan');
            return false;
        }

        if (coaSumber.value === coaTujuan.value) {
            e.preventDefault();
            alert('Akun sumber dan tujuan tidak boleh sama');
            return false;
        }

        // Validasi saldo
        const saldo = parseFloat(saldoSumberSpan.dataset.saldo || 0);
        if (jumlahBersih > saldo) {
            e.preventDefault();
            alert('Saldo akun sumber tidak mencukupi!');
            return false;
        }

        // Hapus input hidden yang lama jika ada
        const oldHidden = document.querySelector('input[name="jumlah"]');
        if (oldHidden) {
            oldHidden.remove();
        }

        // Buat input baru dengan nilai bersih
        const jumlahClean = document.createElement('input');
        jumlahClean.type = 'hidden';
        jumlahClean.name = 'jumlah';
        jumlahClean.value = jumlahBersih;
        
        this.appendChild(jumlahClean);
        
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

    // Inisialisasi
    updatePreviewAkun();
    if (coaSumber.value && tanggal.value) {
        getSaldoSumber();
    }
});

// Fungsi format rupiah - TANPA DESIMAL .00
function formatRupiah(angka) {
    if (!angka || isNaN(angka)) return '0';
    
    // Pastikan angka adalah integer
    let number = parseInt(angka);
    if (isNaN(number)) return '0';
    
    // Format dengan pemisah ribuan
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
</script>

<style>
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
    border-color: #ffc107;
    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
}

.input-group-text {
    border-radius: 8px 0 0 8px;
    background-color: #f8f9fa;
}

.alert-warning {
    background-color: #fff3cd;
    border: none;
    border-left: 4px solid #ffc107;
    border-radius: 8px;
}

.alert-info {
    background-color: #e7f3ff;
    border: none;
    border-left: 4px solid #4e73df;
    border-radius: 8px;
}

.btn {
    padding: 0.6rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
}

.btn-warning {
    background-color: #ffc107;
    border: none;
    color: #212529;
}

.btn-warning:hover {
    background-color: #e0a800;
}

.btn-warning:disabled {
    background-color: #ffe69c;
    cursor: not-allowed;
}

.table-borderless td {
    border: none;
    padding: 0.5rem 0;
}

.badge {
    padding: 0.5rem 1rem;
    font-weight: 500;
}

.list-unstyled li:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}

#infoSaldoSumber {
    padding: 8px 12px;
    background-color: #f8f9fc;
    border-radius: 6px;
    border-left: 3px solid #4e73df;
}

#terbilang {
    padding: 8px 12px;
    background-color: #f8f9fa;
    border-radius: 6px;
    font-size: 0.9em;
    border-left: 3px solid #6c757d;
}

.bg-light {
    background-color: #f8f9fc !important;
}

.list-unstyled {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f1f5f9;
}

.list-unstyled::-webkit-scrollbar {
    width: 6px;
}

.list-unstyled::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.list-unstyled::-webkit-scrollbar-thumb {
    background-color: #cbd5e0;
    border-radius: 3px;
}
</style>

<?= $this->include('accounting/templates/footer') ?>