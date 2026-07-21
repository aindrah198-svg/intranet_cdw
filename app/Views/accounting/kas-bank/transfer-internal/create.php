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
                        <i class="fas fa-exchange-alt text-primary me-2"></i> Transfer Internal Baru
                    </h2>
                    <p class="page-subtitle text-muted mb-0">
                        Catat perpindahan dana antar rekening perusahaan (Kas ke Bank / Bank ke Bank)
                    </p>
                </div>
                <div>
                    <a href="<?= site_url('accounting/kas-bank/transfer-internal') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Saldo per Bank Cards - Menggunakan data saldo yang sudah dikirim dari controller -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="modern-card">
                <h5 class="card-title mb-3">
                    <i class="fas fa-wallet me-2"></i> Saldo per Bank (Real-time)
                </h5>
                <div class="row">
                    <?php if (isset($saldoAkun) && !empty($saldoAkun)): ?>
                        <?php foreach ($saldoAkun as $akun): 
                            $saldoClass = $akun['saldo'] >= 0 ? 'text-success' : 'text-danger';
                        ?>
                        <div class="col-md-3 mb-3">
                            <div class="card h-100 border-0 shadow-sm" data-akun-id="<?= $akun['id'] ?>" data-saldo="<?= $akun['saldo'] ?>">
                                <div class="card-body">
                                    <h6 class="card-title text-muted mb-2"><?= esc($akun['kode_akun']) ?></h6>
                                    <h5 class="card-text mb-1"><?= esc($akun['nama_akun']) ?></h5>
                                    <h4 class="<?= $saldoClass ?> fw-bold mt-2">
                                        Rp <?= number_format($akun['saldo'], 2) ?>
                                    </h4>
                                    <?php if ($akun['saldo'] < 1000000): ?>
                                        <small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Saldo terbatas</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> Tidak ada data saldo akun.
                            </div>
                        </div>
                    <?php endif; ?>
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
                        <i class="fas fa-pencil-alt me-2"></i> Form Transfer Internal
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?= site_url('accounting/kas-bank/transfer-internal/store') ?>" 
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
                                       value="<?= old('tanggal', date('Y-m-d')) ?>"
                                       required>
                                <?php if (session('errors.tanggal')): ?>
                                    <div class="invalid-feedback"><?= session('errors.tanggal') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Nomor Referensi (Opsional) -->
                            <div class="col-md-6 mb-3">
                                <label for="no_referensi" class="form-label">
                                    Nomor Referensi
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="no_referensi" 
                                       name="no_referensi" 
                                       value="<?= old('no_referensi') ?>"
                                       placeholder="No. Bukti Transfer (opsional)">
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
                                    <?php if (isset($saldoAkun) && !empty($saldoAkun)): ?>
                                        <?php foreach ($saldoAkun as $akun): ?>
                                            <option value="<?= $akun['id'] ?>" 
                                                    data-saldo="<?= $akun['saldo'] ?>"
                                                    data-nama="<?= esc($akun['nama_akun']) ?>"
                                                    data-kode="<?= esc($akun['kode_akun']) ?>"
                                                <?= old('coa_id_sumber') == $akun['id'] ? 'selected' : '' ?>>
                                                <?= esc($akun['kode_akun']) ?> - <?= esc($akun['nama_akun']) ?> (Rp <?= number_format($akun['saldo'], 0) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <?php if (session('errors.coa_id_sumber')): ?>
                                    <div class="invalid-feedback"><?= session('errors.coa_id_sumber') ?></div>
                                <?php endif; ?>
                                
                                <!-- Info saldo sumber (akan muncul setelah pilih akun) -->
                                <div id="infoSaldoSumber" class="mt-2 p-2 bg-light rounded" style="display: none;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Saldo tersedia:</span>
                                        <span id="saldoSumber" class="fw-bold text-primary">Rp 0</span>
                                    </div>
                                    <div class="progress mt-2" style="height: 5px;" id="progressSaldo" style="display: none;">
                                        <div id="progressBar" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <small id="maxTransferInfo" class="text-muted mt-1 d-block"></small>
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
                                    <?php if (isset($saldoAkun) && !empty($saldoAkun)): ?>
                                        <?php foreach ($saldoAkun as $akun): ?>
                                            <option value="<?= $akun['id'] ?>" 
                                                    data-saldo="<?= $akun['saldo'] ?>"
                                                    data-nama="<?= esc($akun['nama_akun']) ?>"
                                                <?= old('coa_id_tujuan') == $akun['id'] ? 'selected' : '' ?>>
                                                <?= esc($akun['kode_akun']) ?> - <?= esc($akun['nama_akun']) ?> (Rp <?= number_format($akun['saldo'], 0) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <?php if (session('errors.coa_id_tujuan')): ?>
                                    <div class="invalid-feedback"><?= session('errors.coa_id_tujuan') ?></div>
                                <?php endif; ?>
                                
                                <!-- Info saldo tujuan -->
                                <div id="infoSaldoTujuan" class="mt-2 small text-muted" style="display: none;">
                                    <i class="fas fa-info-circle"></i> Saldo saat ini: <span id="saldoTujuan">Rp 0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Hapus bagian bank asal dan bank tujuan karena sudah otomatis dari akun yang dipilih -->

                        <!-- Jumlah Transfer dengan slider dan max value -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="jumlah" class="form-label">
                                    Jumlah Transfer <span class="text-danger">*</span>
                                </label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" 
                                           class="form-control <?= session('errors.jumlah') ? 'is-invalid' : '' ?>" 
                                           id="jumlah" 
                                           name="jumlah_display" 
                                           value="<?= old('jumlah_display', '0') ?>"
                                           placeholder="0"
                                           required>
                                    <span class="input-group-text">,00</span>
                                </div>
                                <?php if (session('errors.jumlah')): ?>
                                    <div class="invalid-feedback d-block"><?= session('errors.jumlah') ?></div>
                                <?php endif; ?>
                                
                                <!-- Range slider untuk memudahkan input -->
                                <input type="range" class="form-range" id="jumlahRange" min="0" max="0" step="10000" value="0">
                                
                                <!-- Quick amount buttons -->
                                <div class="d-flex gap-2 mt-2 flex-wrap" id="quickAmounts" style="display: none !important;">
                                    <button type="button" class="btn btn-sm btn-outline-primary quick-amount" data-percent="25">25%</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary quick-amount" data-percent="50">50%</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary quick-amount" data-percent="75">75%</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary quick-amount" data-percent="100">100%</button>
                                    <button type="button" class="btn btn-sm btn-outline-info quick-amount" data-amount="100000">100K</button>
                                    <button type="button" class="btn btn-sm btn-outline-info quick-amount" data-amount="500000">500K</button>
                                    <button type="button" class="btn btn-sm btn-outline-info quick-amount" data-amount="1000000">1JT</button>
                                </div>
                                
                                <!-- Teks terbilang dan max info -->
                                <div id="terbilang" class="mt-2 text-muted fst-italic" style="display: none;">
                                    <small><i class="fas fa-quote-right me-1"></i> <span id="terbilangText"></span></small>
                                </div>
                                <div id="maxInfo" class="mt-1 small text-warning" style="display: none;">
                                    <i class="fas fa-exclamation-triangle"></i> Maksimal transfer: <span id="maxAmount">Rp 0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Upload Lampiran -->
                        <div class="mb-3">
                            <label for="lampiran" class="form-label">
                                Lampiran Bukti Transfer (Opsional)
                            </label>
                            <input type="file" 
                                   class="form-control" 
                                   id="lampiran" 
                                   name="lampiran"
                                   accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Format: PDF, JPG, PNG (Max: 2MB)</small>
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
                                      placeholder="Contoh: Transfer dari Kas Kecil ke Bank BCA / Transfer antar rekening..."
                                      required><?= old('keterangan') ?></textarea>
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
                                        <li>Pilih akun sumber (dana akan dikurangi) dan akun tujuan (dana akan ditambah).</li>
                                        <li>Jumlah transfer <strong>tidak boleh melebihi saldo akun sumber</strong>.</li>
                                        <li>Transaksi akan disimpan sebagai <strong>Draft</strong> terlebih dahulu.</li>
                                        <li>Setelah yakin, Anda dapat <strong>Posting</strong> ke jurnal dari halaman daftar.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="d-flex justify-content-end mt-4">
                            <button type="reset" class="btn btn-secondary me-2" onclick="resetForm()">
                                <i class="fas fa-undo me-1"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save me-1"></i> Simpan Draft
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Side Panel -->
        <div class="col-md-4">
            <!-- Informasi Transfer Internal -->
            <div class="modern-card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i> Tentang Transfer Internal
                    </h5>
                </div>
                <div class="card-body">
                    <h6 class="text-primary">
                        <i class="fas fa-exchange-alt me-1"></i> Perpindahan Dana Internal
                    </h6>
                    <p class="small text-muted">
                        Transfer internal digunakan untuk mencatat perpindahan dana antar rekening perusahaan.
                    </p>
                    
                    <hr>
                    
                    <h6 class="text-success">Contoh Transfer:</h6>
                    <ul class="small">
                        <?php if (isset($saldoAkun) && !empty($saldoAkun)): ?>
                            <?php foreach (array_slice($saldoAkun, 0, 3) as $akun): ?>
                                <li><strong><?= esc($akun['nama_akun']) ?> (Rp <?= number_format($akun['saldo'], 0) ?>)</strong></li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                    
                    <hr>
                    
                    <h6 class="text-primary">Preview Jurnal:</h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-1"><strong>Debit:</strong> <span id="previewAkunTujuan">Akun Tujuan</span></p>
                        <p class="mb-0"><strong>Kredit:</strong> <span id="previewAkunSumber">Akun Sumber</span></p>
                    </div>
                </div>
            </div>

            <!-- Daftar Akun Kas/Bank dengan Saldo -->
            <div class="modern-card">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-university me-2"></i> Saldo Akun Tersedia
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <?php if (isset($saldoAkun) && !empty($saldoAkun)): ?>
                            <?php foreach ($saldoAkun as $akun): 
                                $saldoClass = $akun['saldo'] >= 0 ? 'text-success' : 'text-danger';
                            ?>
                            <li class="mb-2 pb-2 border-bottom">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="fw-bold"><?= esc($akun['kode_akun']) ?></span>
                                        <span class="text-muted d-block small"><?= esc($akun['nama_akun'] ?? '') ?></span>
                                    </div>
                                    <div class="text-end">
                                        <span class="<?= $saldoClass ?> fw-bold">Rp <?= number_format($akun['saldo'], 0) ?></span>
                                    </div>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="text-muted text-center">Tidak ada data akun</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const jumlahInput = document.getElementById('jumlah');
    const jumlahRange = document.getElementById('jumlahRange');
    const coaSumber = document.getElementById('coa_id_sumber');
    const coaTujuan = document.getElementById('coa_id_tujuan');
    const infoSaldoSumber = document.getElementById('infoSaldoSumber');
    const saldoSumberSpan = document.getElementById('saldoSumber');
    const infoSaldoTujuan = document.getElementById('infoSaldoTujuan');
    const saldoTujuanSpan = document.getElementById('saldoTujuan');
    const saldoWarning = document.getElementById('saldoWarning');
    const saldoWarningMessage = document.getElementById('saldoWarningMessage');
    const terbilangDiv = document.getElementById('terbilang');
    const terbilangText = document.getElementById('terbilangText');
    const previewAkunSumber = document.getElementById('previewAkunSumber');
    const previewAkunTujuan = document.getElementById('previewAkunTujuan');
    const maxInfo = document.getElementById('maxInfo');
    const maxAmount = document.getElementById('maxAmount');
    const progressBar = document.getElementById('progressBar');
    const progressSaldo = document.getElementById('progressSaldo');
    const quickAmounts = document.getElementById('quickAmounts');
    const submitBtn = document.getElementById('submitBtn');

    let maxSaldoSumber = 0;

    // Format currency input
    if (jumlahInput) {
        jumlahInput.addEventListener('input', function(e) {
            let value = this.value.replace(/[^\d]/g, '');
            
            if (value) {
                let angka = parseInt(value);
                if (angka > maxSaldoSumber) {
                    angka = maxSaldoSumber;
                    this.value = formatRupiah(angka);
                } else {
                    this.value = formatRupiah(angka);
                }
                updateJumlahRange(angka);
                getTerbilang(angka);
                validateSaldo(angka);
                updateProgressBar(angka);
            } else {
                this.value = '0';
                updateJumlahRange(0);
                terbilangDiv.style.display = 'none';
                validateSaldo(0);
                updateProgressBar(0);
            }
        });

        jumlahInput.addEventListener('blur', function() {
            let value = this.value.replace(/[^\d]/g, '');
            if (value) {
                let angka = parseInt(value);
                if (angka > maxSaldoSumber) {
                    angka = maxSaldoSumber;
                }
                this.value = formatRupiah(angka);
                updateJumlahRange(angka);
            } else {
                this.value = '0';
                updateJumlahRange(0);
            }
        });
    }

    // Range slider
    if (jumlahRange) {
        jumlahRange.addEventListener('input', function() {
            let value = parseInt(this.value);
            if (value > maxSaldoSumber) {
                value = maxSaldoSumber;
            }
            jumlahInput.value = formatRupiah(value);
            getTerbilang(value);
            validateSaldo(value);
            updateProgressBar(value);
        });
    }

    // Quick amount buttons
    document.querySelectorAll('.quick-amount').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (maxSaldoSumber <= 0) return;
            
            let value = 0;
            if (this.dataset.percent) {
                value = Math.floor(maxSaldoSumber * parseInt(this.dataset.percent) / 100);
            } else if (this.dataset.amount) {
                value = parseInt(this.dataset.amount);
                if (value > maxSaldoSumber) {
                    value = maxSaldoSumber;
                }
            }
            
            jumlahInput.value = formatRupiah(value);
            updateJumlahRange(value);
            getTerbilang(value);
            validateSaldo(value);
            updateProgressBar(value);
        });
    });

    function updateJumlahRange(value) {
        if (jumlahRange) {
            jumlahRange.value = value;
        }
    }

    function updateProgressBar(value) {
        if (progressSaldo && maxSaldoSumber > 0) {
            progressSaldo.style.display = 'block';
            let percent = (value / maxSaldoSumber) * 100;
            progressBar.style.width = percent + '%';
            
            if (percent >= 90) {
                progressBar.className = 'progress-bar bg-danger';
            } else if (percent >= 70) {
                progressBar.className = 'progress-bar bg-warning';
            } else {
                progressBar.className = 'progress-bar bg-success';
            }
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

    // Saat pilih akun sumber
    coaSumber.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (selected.value) {
            const saldo = parseFloat(selected.dataset.saldo || 0);
            const nama = selected.dataset.nama || '';
            maxSaldoSumber = saldo;
            
            saldoSumberSpan.textContent = 'Rp ' + formatRupiah(saldo);
            infoSaldoSumber.style.display = 'block';
            
            // Update range slider
            if (jumlahRange) {
                jumlahRange.max = saldo;
                jumlahRange.value = 0;
            }
            
            // Update max info
            maxAmount.textContent = 'Rp ' + formatRupiah(saldo);
            maxInfo.style.display = 'block';
            
            // Show quick amounts
            quickAmounts.style.display = 'flex';
            
            // Reset jumlah input
            jumlahInput.value = '0';
            updateJumlahRange(0);
            validateSaldo(0);
            updateProgressBar(0);
            
            // Update preview
            previewAkunSumber.textContent = nama;
        } else {
            infoSaldoSumber.style.display = 'none';
            maxInfo.style.display = 'none';
            quickAmounts.style.display = 'none';
            progressSaldo.style.display = 'none';
            maxSaldoSumber = 0;
            previewAkunSumber.textContent = 'Akun Sumber';
        }
        
        // Cek apakah sumber dan tujuan sama
        checkSameAccount();
    });

    // Saat pilih akun tujuan
    coaTujuan.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (selected.value) {
            const saldo = parseFloat(selected.dataset.saldo || 0);
            const nama = selected.dataset.nama || '';
            
            saldoTujuanSpan.textContent = 'Rp ' + formatRupiah(saldo);
            infoSaldoTujuan.style.display = 'block';
            
            // Update preview
            previewAkunTujuan.textContent = nama;
        } else {
            infoSaldoTujuan.style.display = 'none';
            previewAkunTujuan.textContent = 'Akun Tujuan';
        }
        
        // Cek apakah sumber dan tujuan sama
        checkSameAccount();
    });

    function checkSameAccount() {
        if (coaSumber.value && coaTujuan.value && coaSumber.value === coaTujuan.value) {
            saldoWarningMessage.innerHTML = 'Akun sumber dan tujuan tidak boleh sama!';
            saldoWarning.style.display = 'block';
            submitBtn.disabled = true;
        } else {
            saldoWarning.style.display = 'none';
            submitBtn.disabled = false;
        }
    }

    function validateSaldo(jumlah) {
        if (jumlah > maxSaldoSumber) {
            saldoWarningMessage.innerHTML = `Jumlah transfer (Rp ${formatRupiah(jumlah)}) melebihi saldo tersedia (Rp ${formatRupiah(maxSaldoSumber)})!`;
            saldoWarning.style.display = 'block';
            submitBtn.disabled = true;
            return false;
        } else if (jumlah <= 0) {
            saldoWarningMessage.innerHTML = 'Jumlah transfer harus lebih dari 0!';
            saldoWarning.style.display = 'block';
            submitBtn.disabled = true;
            return false;
        } else {
            saldoWarning.style.display = 'none';
            submitBtn.disabled = false;
            return true;
        }
    }

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
        if (jumlahBersih > maxSaldoSumber) {
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

    // Reset form
    window.resetForm = function() {
        jumlahInput.value = '0';
        updateJumlahRange(0);
        terbilangDiv.style.display = 'none';
        infoSaldoSumber.style.display = 'none';
        infoSaldoTujuan.style.display = 'none';
        saldoWarning.style.display = 'none';
        maxInfo.style.display = 'none';
        quickAmounts.style.display = 'none';
        progressSaldo.style.display = 'none';
        submitBtn.disabled = false;
        previewAkunSumber.textContent = 'Akun Sumber';
        previewAkunTujuan.textContent = 'Akun Tujuan';
    };

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
});

// Fungsi format rupiah
function formatRupiah(angka) {
    if (!angka || isNaN(angka)) return '0';
    let number = parseInt(angka);
    if (isNaN(number)) return '0';
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
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.input-group-text {
    border-radius: 8px 0 0 8px;
    background-color: #f8f9fa;
}

.alert-info {
    background-color: #e7f3ff;
    border: none;
    border-left: 4px solid #4e73df;
    border-radius: 8px;
}

.alert-warning {
    background-color: #fff3cd;
    border: none;
    border-left: 4px solid #ffc107;
    border-radius: 8px;
}

.btn {
    padding: 0.6rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
}

.btn-primary {
    background-color: #4e73df;
    border: none;
}

.btn-primary:hover {
    background-color: #2e59d9;
}

.btn-primary:disabled {
    background-color: #b7c3f0;
    cursor: not-allowed;
}

.btn-outline-primary, .btn-outline-info {
    border-radius: 20px;
    padding: 0.25rem 1rem;
    font-size: 0.85rem;
}

/* Progress bar */
.progress {
    background-color: #e9ecef;
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
    transition: width 0.3s ease;
}

/* Range slider */
.form-range {
    width: 100%;
    height: 1.5rem;
    padding: 0;
    background-color: transparent;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}

.form-range:focus {
    outline: 0;
}

.form-range:focus::-webkit-slider-thumb {
    box-shadow: 0 0 0 1px #fff, 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.form-range:focus::-moz-range-thumb {
    box-shadow: 0 0 0 1px #fff, 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.form-range::-webkit-slider-thumb {
    width: 1rem;
    height: 1rem;
    margin-top: -0.25rem;
    background-color: #4e73df;
    border: 0;
    border-radius: 1rem;
    -webkit-transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    -webkit-appearance: none;
    appearance: none;
}

.form-range::-webkit-slider-thumb:active {
    background-color: #b7c3f0;
}

.form-range::-webkit-slider-runnable-track {
    width: 100%;
    height: 0.5rem;
    color: transparent;
    cursor: pointer;
    background-color: #dee2e6;
    border-color: transparent;
    border-radius: 1rem;
}

/* Info boxes */
#infoSaldoSumber, #infoSaldoTujuan {
    background-color: #f8f9fc;
    border-radius: 6px;
    padding: 10px 12px;
}

#infoSaldoSumber {
    border-left: 3px solid #4e73df;
}

#infoSaldoTujuan {
    border-left: 3px solid #28a745;
}

#terbilang {
    padding: 8px 12px;
    background-color: #f8f9fa;
    border-radius: 6px;
    font-size: 0.9em;
    border-left: 3px solid #6c757d;
}

/* Quick amount buttons container */
#quickAmounts {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-top: 0.5rem;
}

/* Card styling */
.card.border-0.shadow-sm {
    transition: transform 0.2s;
}

.card.border-0.shadow-sm:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}

/* List styling */
.list-unstyled li:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}
</style>

<?= $this->include('accounting/templates/footer') ?>