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
                        <i class="fas fa-money-bill-wave me-2"></i> Proses Pelunasan
                    </h2>
                    <p class="page-subtitle text-muted mb-0">
                        Pembayaran hutang pengeluaran pribadi - <?= esc($pengeluaran['kode_pengeluaran']) ?>
                    </p>
                </div>
                <div>
                    <a href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi/detail/' . $pengeluaran['id']) ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Detail
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Pengeluaran Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="modern-card">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i> Informasi Pengeluaran
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <small class="text-muted d-block">Kode Pengeluaran</small>
                            <strong><?= esc($pengeluaran['kode_pengeluaran']) ?></strong>
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted d-block">Tanggal</small>
                            <strong><?= date('d/m/Y', strtotime($pengeluaran['tanggal'])) ?></strong>
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted d-block">Jenis</small>
                            <?php 
                            $badgeClass = match($pengeluaran['jenis']) {
                                'Kasbon' => 'bg-primary',
                                'Reimbursement' => 'bg-success',
                                'Prive' => 'bg-secondary',
                                'Dana Talangan' => 'bg-info',
                                'Klaim Pribadi' => 'bg-warning',
                                default => 'bg-dark'
                            };
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= esc($pengeluaran['jenis']) ?></span>
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted d-block">Karyawan</small>
                            <strong><?= esc($pengeluaran['nama_karyawan'] ?? '-') ?></strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Status Hutang</small>
                            <?php 
                            $hutangClass = match($pengeluaran['status_hutang']) {
                                'Lunas' => 'bg-success',
                                'Sebagian' => 'bg-warning',
                                'Belum Dibayar' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?= $hutangClass ?>"><?= esc($pengeluaran['status_hutang']) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Hutang Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Hutang
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp <?= number_format($pengeluaran['jumlah'], 0, ',', '.') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Sudah Dibayar
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp <?= number_format($pengeluaran['jumlah_dibayar'] ?? 0, 0, ',', '.') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Sisa Hutang
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp <?= number_format($sisa_hutang, 0, ',', '.') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Pelunasan -->
    <div class="row">
        <div class="col-md-8">
            <div class="modern-card">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i> Form Pelunasan
                    </h5>
                </div>
                <div class="card-body">
                    <form id="pelunasanForm">
                        <?= csrf_field() ?>
                        
                        <input type="hidden" name="pengeluaran_id" id="pengeluaran_id" value="<?= $pengeluaran['id'] ?>">
                        
                        <!-- Alert untuk error -->
                        <div id="errorAlert" class="alert alert-danger d-none"></div>
                        
                        <!-- Pilihan Metode Pelunasan -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Pilih Metode Pelunasan <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card method-card <?= old('metode') == 'bank' ? 'border-primary' : '' ?>" id="methodBank">
                                        <div class="card-body text-center">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="metode" 
                                                       id="metodeBank" value="bank" checked>
                                                <label class="form-check-label fw-bold" for="metodeBank">
                                                    <i class="fas fa-university fa-2x d-block mb-2 text-primary"></i>
                                                    Transfer Bank
                                                </label>
                                            </div>
                                            <p class="small text-muted mt-2 mb-0">Pembayaran melalui transfer bank</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card method-card" id="methodKasKecil">
                                        <div class="card-body text-center">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="metode" 
                                                       id="metodeKasKecil" value="kas_kecil">
                                                <label class="form-check-label fw-bold" for="metodeKasKecil">
                                                    <i class="fas fa-coins fa-2x d-block mb-2 text-warning"></i>
                                                    Kas Kecil
                                                </label>
                                            </div>
                                            <p class="small text-muted mt-2 mb-0">Pembayaran melalui kas kecil</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Bank -->
                        <div id="formBank">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal_bank" class="form-label">
                                        Tanggal Transfer <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control" id="tanggal_bank" 
                                           name="tanggal" value="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="coa_bank_id" class="form-label">
                                        Akun Bank Sumber <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="coa_bank_id" name="coa_bank_id" required>
                                        <option value="">-- Pilih Akun Bank --</option>
                                        <?php foreach ($bankOptions as $bank): ?>
                                            <option value="<?= $bank['id'] ?>" 
                                                    data-saldo="<?= $this->getSaldoBank($bank['id']) ?>">
                                                <?= esc($bank['kode_akun']) ?> - <?= esc($bank['nama_akun']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted" id="infoSaldoBank"></small>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="no_referensi_bank" class="form-label">
                                    No. Referensi Transfer
                                </label>
                                <input type="text" class="form-control" id="no_referensi_bank" 
                                       name="no_referensi" placeholder="Nomor bukti transfer">
                                <small class="text-muted">Opsional, nomor referensi dari bank</small>
                            </div>
                        </div>

                        <!-- Form Kas Kecil -->
                        <div id="formKasKecil" style="display: none;">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal_kas_kecil" class="form-label">
                                        Tanggal Pembayaran <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control" id="tanggal_kas_kecil" 
                                           name="tanggal_kas_kecil" value="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Saldo Kas Kecil Saat Ini
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control" 
                                               value="<?= number_format($saldoKasKecil, 0, ',', '.') ?>" 
                                               readonly>
                                    </div>
                                    <small class="text-muted <?= $saldoKasKecil < $sisa_hutang ? 'text-danger' : '' ?>">
                                        <?php if ($saldoKasKecil < $sisa_hutang): ?>
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            Saldo kas kecil tidak mencukupi untuk pelunasan penuh!
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Jumlah Pembayaran -->
                        <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <label for="jumlah_bayar" class="form-label">
                                    Jumlah Pembayaran <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" id="jumlah_bayar" 
                                           name="jumlah_bayar_display" 
                                           value="<?= number_format($sisa_hutang, 0, ',', '.') ?>"
                                           required>
                                </div>
                                <div id="terbilangBayar" class="small text-muted mt-1"></div>
                                <small class="text-muted">Maksimal pembayaran: Rp <?= number_format($sisa_hutang, 0, ',', '.') ?></small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="keterangan_pelunasan" class="form-label">
                                    Keterangan
                                </label>
                                <textarea class="form-control" id="keterangan_pelunasan" 
                                          name="keterangan" rows="3"
                                          placeholder="Keterangan tambahan...">Pelunasan <?= $pengeluaran['jenis'] ?> - <?= $pengeluaran['kode_pengeluaran'] ?></textarea>
                            </div>
                        </div>

                        <!-- Info Box -->
                        <div class="alert alert-info mt-3">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="fas fa-info-circle fa-2x"></i>
                                </div>
                                <div>
                                    <strong>Informasi:</strong>
                                    <ul class="mb-0 mt-1">
                                        <li>Pembayaran akan mencatat transaksi di Mutasi Bank atau Kas Kecil</li>
                                        <li>Jurnal pelunasan akan dibuat otomatis setelah pembayaran</li>
                                        <li>Status hutang akan berubah menjadi Lunas jika dibayar penuh</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="d-flex justify-content-end mt-4">
                            <a href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi/detail/' . $pengeluaran['id']) ?>" 
                               class="btn btn-secondary me-2">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                            <button type="button" class="btn btn-success" id="btnProsesPelunasan">
                                <i class="fas fa-check-circle me-1"></i> Proses Pelunasan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Side Panel -->
        <div class="col-md-4">
            <!-- Ringkasan -->
            <div class="modern-card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-calculator me-2"></i> Ringkasan
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td>Total Hutang</td>
                            <td class="text-end">Rp <?= number_format($pengeluaran['jumlah'], 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td>Sudah Dibayar</td>
                            <td class="text-end">Rp <?= number_format($pengeluaran['jumlah_dibayar'] ?? 0, 0, ',', '.') ?></td>
                        </tr>
                        <tr class="fw-bold">
                            <td>Sisa Hutang</td>
                            <td class="text-end text-danger">Rp <?= number_format($sisa_hutang, 0, ',', '.') ?></td>
                        </tr>
                    </table>
                    
                    <hr>
                    
                    <div class="text-center">
                        <div class="display-6 mb-2" id="persentaseProgress">0%</div>
                        <div class="progress" style="height: 10px;">
                            <?php 
                            $persentase = ($pengeluaran['jumlah_dibayar'] / $pengeluaran['jumlah']) * 100;
                            ?>
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: <?= $persentase ?>%" 
                                 aria-valuenow="<?= $persentase ?>" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100"></div>
                        </div>
                        <small class="text-muted">Progress pembayaran</small>
                    </div>
                </div>
            </div>

            <!-- Informasi Akun -->
            <div class="modern-card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-book me-2"></i> Informasi Akun
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">Jurnal yang akan dibuat:</p>
                    
                    <div class="bg-light p-3 rounded">
                        <h6 class="text-primary">Debit:</h6>
                        <p class="mb-2">
                            <strong><?= esc($pengeluaran['nama_akun_debit'] ?? '-') ?></strong><br>
                            <small class="text-muted">(Mengurangi hutang)</small>
                        </p>
                        
                        <h6 class="text-success mt-3">Kredit:</h6>
                        <div id="infoAkunKredit">
                            <p class="mb-0">
                                <strong>Akun akan ditentukan berdasarkan metode</strong>
                            </p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="small">
                        <i class="fas fa-info-circle text-info me-1"></i>
                        <span class="text-muted">
                            Untuk pembayaran via bank, kredit ke akun Kas/Bank<br>
                            Untuk pembayaran via kas kecil, kredit ke akun Kas Kecil
                        </span>
                    </div>
                </div>
            </div>

            <!-- Tips -->
            <div class="modern-card">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i> Tips
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="small mb-0 ps-3">
                        <li class="mb-2">Pastikan saldo bank atau kas kecil mencukupi</li>
                        <li class="mb-2">Untuk pembayaran sebagian, masukkan jumlah sesuai yang dibayarkan</li>
                        <li class="mb-2">Lampirkan bukti transfer jika perlu (upload via menu Mutasi Bank)</li>
                        <li>Setelah pelunasan, status hutang akan berubah menjadi Lunas</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi -->
<div class="modal fade" id="konfirmasiModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Pelunasan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin akan memproses pelunasan ini?</p>
                <div class="alert alert-warning">
                    <strong>Ringkasan:</strong>
                    <table class="table table-sm table-borderless mt-2 mb-0">
                        <tr>
                            <td>Metode</td>
                            <td class="fw-bold" id="confirmMetode">-</td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td class="fw-bold" id="confirmTanggal">-</td>
                        </tr>
                        <tr>
                            <td>Jumlah</td>
                            <td class="fw-bold text-success" id="confirmJumlah">-</td>
                        </tr>
                    </table>
                </div>
                <p class="text-muted small mb-0">Setelah diproses, transaksi tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="confirmProses">Ya, Proses</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const metodeBank = document.getElementById('metodeBank');
    const metodeKasKecil = document.getElementById('metodeKasKecil');
    const formBank = document.getElementById('formBank');
    const formKasKecil = document.getElementById('formKasKecil');
    const methodBankCard = document.getElementById('methodBank');
    const methodKasKecilCard = document.getElementById('methodKasKecil');
    const jumlahBayarInput = document.getElementById('jumlah_bayar');
    const terbilangBayarDiv = document.getElementById('terbilangBayar');
    const sisaHutang = <?= $sisa_hutang ?>;
    const pengeluaranId = <?= $pengeluaran['id'] ?>;
    
    // Fungsi untuk toggle metode pembayaran
    function toggleMetode() {
        if (metodeBank.checked) {
            formBank.style.display = 'block';
            formKasKecil.style.display = 'none';
            methodBankCard.classList.add('border-primary');
            methodKasKecilCard.classList.remove('border-primary');
            
            document.getElementById('tanggal_bank').required = true;
            document.getElementById('coa_bank_id').required = true;
            document.getElementById('tanggal_kas_kecil').required = false;
        } else {
            formBank.style.display = 'none';
            formKasKecil.style.display = 'block';
            methodKasKecilCard.classList.add('border-primary');
            methodBankCard.classList.remove('border-primary');
            
            document.getElementById('tanggal_bank').required = false;
            document.getElementById('coa_bank_id').required = false;
            document.getElementById('tanggal_kas_kecil').required = true;
            
            // Update info akun kredit
            document.getElementById('infoAkunKredit').innerHTML = `
                <p class="mb-0">
                    <strong>Kas Kecil (1-1101)</strong><br>
                    <small class="text-muted">Saldo: Rp <?= number_format($saldoKasKecil, 0, ',', '.') ?></small>
                </p>
            `;
        }
    }
    
    metodeBank.addEventListener('change', toggleMetode);
    metodeKasKecil.addEventListener('change', toggleMetode);
    
    // Format currency untuk jumlah bayar
    if (jumlahBayarInput) {
        let initialValue = jumlahBayarInput.value.replace(/[^\d]/g, '');
        if (initialValue && parseInt(initialValue) > 0) {
            jumlahBayarInput.value = formatRupiah(parseInt(initialValue));
            getTerbilang(parseInt(initialValue));
        }
        
        jumlahBayarInput.addEventListener('input', function(e) {
            let value = this.value.replace(/[^\d]/g, '');
            
            if (value) {
                let angka = parseInt(value);
                if (angka > sisaHutang) {
                    angka = sisaHutang;
                    this.value = formatRupiah(angka);
                } else {
                    this.value = formatRupiah(angka);
                }
                getTerbilang(angka);
            } else {
                this.value = '0';
                terbilangBayarDiv.innerHTML = '';
            }
        });
        
        jumlahBayarInput.addEventListener('blur', function() {
            let value = this.value.replace(/[^\d]/g, '');
            if (value) {
                let angka = parseInt(value);
                if (angka > sisaHutang) {
                    angka = sisaHutang;
                    this.value = formatRupiah(angka);
                }
            } else {
                this.value = '0';
            }
        });
    }
    
    // Function get terbilang via AJAX
    function getTerbilang(angka) {
        if (angka > 0) {
            fetch('<?= site_url('accounting/kas-bank/pengeluaran-pribadi/ajaxGetTerbilang') ?>?jumlah=' + angka)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        terbilangBayarDiv.innerHTML = '<i class="fas fa-pencil-alt me-1"></i> ' + data.terbilang;
                    } else {
                        terbilangBayarDiv.innerHTML = '';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        } else {
            terbilangBayarDiv.innerHTML = '';
        }
    }
    
    // Get info saldo bank
    const coaBankSelect = document.getElementById('coa_bank_id');
    const infoSaldoBank = document.getElementById('infoSaldoBank');
    
    if (coaBankSelect) {
        coaBankSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption && selectedOption.value) {
                const saldo = selectedOption.dataset.saldo || 0;
                infoSaldoBank.innerHTML = `Saldo tersedia: Rp ${formatRupiah(saldo)}`;
                
                // Update info akun kredit
                document.getElementById('infoAkunKredit').innerHTML = `
                    <p class="mb-0">
                        <strong>${selectedOption.text}</strong><br>
                        <small class="text-muted">Saldo: Rp ${formatRupiah(saldo)}</small>
                    </p>
                `;
            } else {
                infoSaldoBank.innerHTML = '';
            }
        });
    }
    
    // Validasi sebelum submit
    document.getElementById('btnProsesPelunasan').addEventListener('click', function() {
        const metode = document.querySelector('input[name="metode"]:checked').value;
        const tanggal = metode === 'bank' 
            ? document.getElementById('tanggal_bank').value 
            : document.getElementById('tanggal_kas_kecil').value;
        const jumlah = parseInt(jumlahBayarInput.value.replace(/[^\d]/g, '')) || 0;
        
        if (!tanggal) {
            alert('Tanggal harus diisi');
            return;
        }
        
        if (jumlah <= 0) {
            alert('Jumlah pembayaran harus lebih dari 0');
            return;
        }
        
        if (jumlah > sisaHutang) {
            alert('Jumlah pembayaran melebihi sisa hutang');
            return;
        }
        
        if (metode === 'bank') {
            const coaBank = document.getElementById('coa_bank_id').value;
            if (!coaBank) {
                alert('Pilih akun bank sumber');
                return;
            }
        }
        
        // Tampilkan konfirmasi
        document.getElementById('confirmMetode').textContent = 
            metode === 'bank' ? 'Transfer Bank' : 'Kas Kecil';
        document.getElementById('confirmTanggal').textContent = tanggal;
        document.getElementById('confirmJumlah').textContent = 'Rp ' + formatRupiah(jumlah.toString());
        
        var konfirmasiModal = new bootstrap.Modal(document.getElementById('konfirmasiModal'));
        konfirmasiModal.show();
    });
    
    // Proses pelunasan
    document.getElementById('confirmProses').addEventListener('click', function() {
        const metode = document.querySelector('input[name="metode"]:checked').value;
        const tanggal = metode === 'bank' 
            ? document.getElementById('tanggal_bank').value 
            : document.getElementById('tanggal_kas_kecil').value;
        const jumlah = parseInt(jumlahBayarInput.value.replace(/[^\d]/g, '')) || 0;
        const keterangan = document.getElementById('keterangan_pelunasan').value;
        
        let formData = new URLSearchParams();
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        formData.append('metode', metode);
        formData.append('tanggal', tanggal);
        formData.append('jumlah_bayar', jumlah);
        formData.append('keterangan', keterangan);
        
        if (metode === 'bank') {
            const coaBank = document.getElementById('coa_bank_id').value;
            const noReferensi = document.getElementById('no_referensi_bank').value;
            formData.append('coa_bank_id', coaBank);
            formData.append('no_referensi', noReferensi);
        }
        
        const confirmBtn = this;
        const originalText = confirmBtn.innerHTML;
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        confirmBtn.disabled = true;
        
        fetch('<?= site_url('accounting/kas-bank/pengeluaran-pribadi/lunasi') ?>/' + pengeluaranId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('konfirmasiModal')).hide();
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    window.location.href = '<?= site_url('accounting/kas-bank/pengeluaran-pribadi/detail/' . $pengeluaran['id']) ?>';
                }
            } else {
                alert('Error: ' + data.message);
                confirmBtn.innerHTML = originalText;
                confirmBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memproses pelunasan');
            confirmBtn.innerHTML = originalText;
            confirmBtn.disabled = false;
        });
    });
    
    // Update progress bar saat jumlah berubah
    jumlahBayarInput.addEventListener('input', function() {
        const jumlahBayar = parseInt(this.value.replace(/[^\d]/g, '')) || 0;
        const totalDibayar = <?= $pengeluaran['jumlah_dibayar'] ?? 0 ?>;
        const totalHutang = <?= $pengeluaran['jumlah'] ?>;
        const totalSetelah = totalDibayar + jumlahBayar;
        const persentase = (totalSetelah / totalHutang) * 100;
        
        document.getElementById('persentaseProgress').textContent = 
            Math.min(persentase, 100).toFixed(0) + '%';
        document.querySelector('.progress-bar').style.width = 
            Math.min(persentase, 100) + '%';
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

.border-left-primary {
    border-left: 4px solid #4e73df !important;
}
.border-left-info {
    border-left: 4px solid #36b9cc !important;
}
.border-left-warning {
    border-left: 4px solid #f6c23e !important;
}

.method-card {
    cursor: pointer;
    transition: all 0.2s;
    border: 2px solid #dee2e6;
}

.method-card:hover {
    border-color: #4e73df;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.method-card.border-primary {
    border-color: #4e73df;
    background-color: #f8f9fc;
}

.method-card .form-check-input {
    position: relative;
    margin-left: 0;
    float: none;
}

.table-borderless td, .table-borderless th {
    border: none;
    padding: 0.3rem;
}

.progress {
    border-radius: 10px;
}

.badge {
    padding: 0.4rem 0.6rem;
    font-weight: 500;
}

.btn {
    border-radius: 8px;
    padding: 0.6rem 1.5rem;
}
</style>

<?= $this->include('accounting/templates/footer') ?>