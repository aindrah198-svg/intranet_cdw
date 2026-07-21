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
                        <?php if ($tipe == 'Masuk'): ?>
                            <i class="fas fa-arrow-down text-success me-2"></i> Transaksi Masuk (Uang Masuk)
                        <?php else: ?>
                            <i class="fas fa-arrow-up text-danger me-2"></i> Transaksi Keluar (Uang Keluar)
                        <?php endif; ?>
                    </h2>
                    <p class="page-subtitle text-muted mb-0">
                        <?php if ($tipe == 'Masuk'): ?>
                            Catat penerimaan uang masuk ke rekening bank perusahaan
                        <?php else: ?>
                            Catat pengeluaran uang dari rekening bank perusahaan
                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <a href="<?= site_url('accounting/kas-bank/mutasi-bank') ?>" class="btn btn-secondary">
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
                        <i class="fas fa-pencil-alt me-2"></i> Form Transaksi
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?= site_url('accounting/kas-bank/mutasi-bank/store') ?>" 
                          method="post" 
                          enctype="multipart/form-data"
                          id="mutasiForm">
                        
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
                                       value="<?= old('tanggal', $mutasi['tanggal']) ?>"
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
                                       value="<?= old('no_referensi') ?>"
                                       placeholder="No. Cek / BG / Transfer">
                                <small class="text-muted">Opsional, nomor referensi dari bank</small>
                            </div>
                        </div>

                        <!-- ===== DROPDOWN AKUN - SEMUA AKUN BISA DIPILIH ===== -->
                        <div class="row">
                            <?php if ($tipe == 'Masuk'): ?>
                                <!-- Untuk Transaksi Masuk -->
                                <div class="col-md-6 mb-3">
                                    <label for="bank_tujuan" class="form-label">
                                        Bank Tujuan <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control <?= session('errors.bank_tujuan') ? 'is-invalid' : '' ?>" 
                                           id="bank_tujuan" 
                                           name="bank_tujuan" 
                                           value="<?= old('bank_tujuan', 'Mandiri') ?>"
                                           placeholder="BCA / Mandiri / dll"
                                           required>
                                    <small class="text-muted">Rekening bank perusahaan yang menerima uang</small>
                                    <?php if (session('errors.bank_tujuan')): ?>
                                        <div class="invalid-feedback"><?= session('errors.bank_tujuan') ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="coa_id_kredit" class="form-label">
                                        Akun Sumber Dana <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select <?= session('errors.coa_id_kredit') ? 'is-invalid' : '' ?>" 
                                            id="coa_id_kredit" 
                                            name="coa_id_kredit" 
                                            required>
                                        <option value="">-- Pilih Akun Sumber --</option>
                                        <?php foreach ($akunMasukOptions as $akun): ?>
                                            <option value="<?= $akun['id'] ?>" 
                                                data-tipe="<?= $akun['tipe_akun'] ?>"
                                                data-kode="<?= $akun['kode_akun'] ?>"
                                                <?= old('coa_id_kredit') == $akun['id'] ? 'selected' : '' ?>>
                                                <?= esc($akun['kode_akun']) ?> - <?= esc($akun['nama_akun']) ?> 
                                                (<?= $akun['tipe_akun'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Dari mana uang ini berasal? (Pendapatan, Piutang, Modal, Hutang, dll)
                                    </small>
                                    <?php if (session('errors.coa_id_kredit')): ?>
                                        <div class="invalid-feedback"><?= session('errors.coa_id_kredit') ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <!-- Untuk Transaksi Keluar -->
                                <div class="col-md-6 mb-3">
                                    <label for="bank_asal" class="form-label">
                                        Bank Asal <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control <?= session('errors.bank_asal') ? 'is-invalid' : '' ?>" 
                                           id="bank_asal" 
                                           name="bank_asal" 
                                           value="<?= old('bank_asal', 'Mandiri') ?>"
                                           placeholder="BCA / Mandiri / dll"
                                           required>
                                    <small class="text-muted">Rekening bank perusahaan yang mengeluarkan uang</small>
                                    <?php if (session('errors.bank_asal')): ?>
                                        <div class="invalid-feedback"><?= session('errors.bank_asal') ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="coa_id_debit" class="form-label">
                                        Akun Tujuan Dana <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select <?= session('errors.coa_id_debit') ? 'is-invalid' : '' ?>" 
                                            id="coa_id_debit" 
                                            name="coa_id_debit" 
                                            required>
                                        <option value="">-- Pilih Akun Tujuan --</option>
                                        <?php foreach ($akunKeluarOptions as $akun): ?>
                                            <option value="<?= $akun['id'] ?>" 
                                                data-tipe="<?= $akun['tipe_akun'] ?>"
                                                data-kode="<?= $akun['kode_akun'] ?>"
                                                <?= old('coa_id_debit') == $akun['id'] ? 'selected' : '' ?>>
                                                <?= esc($akun['kode_akun']) ?> - <?= esc($akun['nama_akun']) ?> 
                                                (<?= $akun['tipe_akun'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Untuk apa uang ini digunakan? (Beban, Pembelian Aset, Pembayaran Hutang, dll)
                                    </small>
                                    <?php if (session('errors.coa_id_debit')): ?>
                                        <div class="invalid-feedback"><?= session('errors.coa_id_debit') ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Pilih Proyek/SPK -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="spk_id" class="form-label">
                                    <i class="fas fa-project-diagram me-1"></i> Proyek / SPK
                                </label>
                                <select class="form-select" id="spk_id" name="spk_id">
                                    <option value="">-- Pilih Proyek (Opsional) --</option>
                                    <?php if (isset($spk_list) && !empty($spk_list)): ?>
                                        <?php foreach ($spk_list as $spk): ?>
                                            <option value="<?= $spk->id ?>" 
                                                    data-nomor="<?= $spk->nomor_spk ?>"
                                                    <?= old('spk_id') == $spk->id ? 'selected' : '' ?>>
                                                <?= esc($spk->nomor_spk) ?> - <?= esc($spk->judul_pekerjaan) ?>
                                                (<?= $spk->status ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <small class="text-muted">Opsional, pilih jika transaksi terkait proyek tertentu</small>
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
                                           name="jumlah_display" 
                                           value="<?= old('jumlah_display', '0') ?>"
                                           placeholder="0"
                                           required>
                                </div>
                                <small class="text-muted" id="terbilangPreview"></small>
                                <?php if (session('errors.jumlah')): ?>
                                    <div class="invalid-feedback d-block"><?= session('errors.jumlah') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Upload Lampiran -->
                            <div class="col-md-6 mb-3">
                                <label for="lampiran" class="form-label">
                                    Lampiran
                                </label>
                                <input type="file" 
                                       class="form-control" 
                                       id="lampiran" 
                                       name="lampiran"
                                       accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Format: PDF, JPG, PNG (Max: 2MB)</small>
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
                                      placeholder="Deskripsi transaksi..."
                                      required><?= old('keterangan') ?></textarea>
                            <?php if (session('errors.keterangan')): ?>
                                <div class="invalid-feedback"><?= session('errors.keterangan') ?></div>
                            <?php endif; ?>
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
                                        <li>Transaksi akan disimpan sebagai <strong>Draft</strong> terlebih dahulu.</li>
                                        <li>Setelah yakin dengan data, Anda dapat <strong>Posting</strong> ke jurnal dari halaman daftar.</li>
                                        <li>Data yang sudah diposting tidak dapat diedit/dihapus.</li>
                                        <li>Bank tujuan/asal default <strong>Mandiri</strong> (dapat diubah).</li>
                                        <li class="mt-1"><i class="fas fa-check-circle text-success me-1"></i> <strong>Fleksibel:</strong> Semua akun dapat dipilih untuk transaksi masuk maupun keluar.</li>
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
            <!-- Informasi Akun -->
            <div class="modern-card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i> Informasi Akun
                    </h5>
                </div>
                <div class="card-body">
                    <?php if ($tipe == 'Masuk'): ?>
                        <h6 class="text-success">
                            <i class="fas fa-arrow-down me-1"></i> Transaksi Masuk (Uang Masuk)
                        </h6>
                        <p class="small text-muted">
                            Mencatat penerimaan uang yang masuk ke rekening bank perusahaan.
                        </p>
                        <hr>
                        <h6>Jurnal yang akan dihasilkan:</h6>
                        <div class="bg-light p-2 rounded small mb-3">
                            <div><span class="text-success">[Debit]</span> Kas/Bank ......... Rp X</div>
                            <div><span class="text-danger">[Kredit]</span> Akun Sumber .... Rp X</div>
                        </div>
                        <h6>Akun yang digunakan:</h6>
                        <ul class="small">
                            <li><strong>Debit:</strong> Kas/Bank (otomatis saat posting)</li>
                            <li><strong>Kredit:</strong> Akun Sumber Dana (bisa dari semua akun)</li>
                        </ul>
                        
                        <!-- Contoh Akun -->
                        <div class="mt-3">
                            <h6>Contoh akun sumber yang tersedia:</h6>
                            <ul class="list-unstyled">
                                <?php 
                                $akunSering = array_slice($akunMasukOptions, 0, 8);
                                foreach ($akunSering as $akun): 
                                ?>
                                <li class="mb-1 small">
                                    <i class="fas fa-circle text-success me-1" style="font-size: 8px;"></i>
                                    <?= esc($akun['kode_akun']) ?> - <?= esc($akun['nama_akun']) ?>
                                    <span class="text-muted">(<?= $akun['tipe_akun'] ?>)</span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php if (count($akunMasukOptions) > 8): ?>
                            <p class="small text-muted">
                                <i class="fas fa-ellipsis-h me-1"></i> dan <?= count($akunMasukOptions) - 8 ?> akun lainnya
                            </p>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <h6 class="text-danger">
                            <i class="fas fa-arrow-up me-1"></i> Transaksi Keluar (Uang Keluar)
                        </h6>
                        <p class="small text-muted">
                            Mencatat pengeluaran uang dari rekening bank perusahaan.
                        </p>
                        <hr>
                        <h6>Jurnal yang akan dihasilkan:</h6>
                        <div class="bg-light p-2 rounded small mb-3">
                            <div><span class="text-success">[Debit]</span> Akun Tujuan .... Rp X</div>
                            <div><span class="text-danger">[Kredit]</span> Kas/Bank ......... Rp X</div>
                        </div>
                        <h6>Akun yang digunakan:</h6>
                        <ul class="small">
                            <li><strong>Debit:</strong> Akun Tujuan Dana (bisa dari semua akun)</li>
                            <li><strong>Kredit:</strong> Kas/Bank (otomatis saat posting)</li>
                        </ul>
                        
                        <!-- Contoh Akun -->
                        <div class="mt-3">
                            <h6>Contoh akun tujuan yang tersedia:</h6>
                            <ul class="list-unstyled">
                                <?php 
                                $akunSering = array_slice($akunKeluarOptions, 0, 8);
                                foreach ($akunSering as $akun): 
                                ?>
                                <li class="mb-1 small">
                                    <i class="fas fa-circle text-danger me-1" style="font-size: 8px;"></i>
                                    <?= esc($akun['kode_akun']) ?> - <?= esc($akun['nama_akun']) ?>
                                    <span class="text-muted">(<?= $akun['tipe_akun'] ?>)</span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php if (count($akunKeluarOptions) > 8): ?>
                            <p class="small text-muted">
                                <i class="fas fa-ellipsis-h me-1"></i> dan <?= count($akunKeluarOptions) - 8 ?> akun lainnya
                            </p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Akun Bank -->
            <div class="modern-card">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-university me-2"></i> Akun Bank Tersedia
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">Akun kas/bank yang aktif:</p>
                    <ul class="list-unstyled">
                        <?php foreach ($coaBankOptions as $bank): ?>
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
                    <p class="small text-muted mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        Akun bank akan dipilih secara otomatis saat proses posting
                    </p>
                </div>
            </div>

            <!-- Tips -->
            <div class="modern-card mt-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i> Tips
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="small mb-0 ps-3">
                        <li class="mb-2"><strong>Transaksi Masuk:</strong> Pilih akun sumber dana (misal: Pendapatan, Piutang, Modal)</li>
                        <li class="mb-2"><strong>Transaksi Keluar:</strong> Pilih akun tujuan dana (misal: Beban, Pembelian Aset, Hutang)</li>
                        <li class="mb-2">Gunakan nomor referensi untuk memudahkan pencocokan dengan mutasi bank.</li>
                        <li class="mb-2">Lampirkan bukti transaksi (foto/scan) untuk dokumentasi.</li>
                        <li>Pilih proyek jika transaksi terkait dengan proyek tertentu.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const jumlahInput = document.getElementById('jumlah');
    const terbilangPreview = document.getElementById('terbilangPreview');

    // Function to fetch terbilang via AJAX
    async function fetchTerbilang(jumlahBersih) {
        if (!jumlahBersih || jumlahBersih <= 0) {
            terbilangPreview.innerHTML = '';
            return;
        }
        
        try {
            const response = await fetch(`<?= site_url('accounting/kas-bank/mutasi-bank/ajaxGetTerbilang') ?>?jumlah=${jumlahBersih}`);
            const data = await response.json();
            
            if (data.success && data.terbilang) {
                terbilangPreview.innerHTML = `<i class="fas fa-language me-1"></i> Terbilang: ${data.terbilang}`;
                terbilangPreview.style.color = '#28a745';
            } else {
                terbilangPreview.innerHTML = '';
            }
        } catch (error) {
            console.error('Error fetching terbilang:', error);
            terbilangPreview.innerHTML = '';
        }
    }

    // Format currency input dengan debounce untuk terbilang
    let timeoutId;
    if (jumlahInput) {
        // Set initial value jika ada
        let initialValue = jumlahInput.value.replace(/[^\d]/g, '');
        if (initialValue && parseInt(initialValue) > 0) {
            jumlahInput.value = formatRupiah(parseInt(initialValue));
            fetchTerbilang(parseInt(initialValue));
        }

        // Event input untuk format dan terbilang
        jumlahInput.addEventListener('input', function(e) {
            let value = this.value.replace(/[^\d]/g, '');
            
            if (value) {
                let angka = parseInt(value);
                this.value = formatRupiah(angka);
                
                // Debounce untuk menghindari terlalu banyak request
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => {
                    fetchTerbilang(angka);
                }, 500);
            } else {
                this.value = '0';
                terbilangPreview.innerHTML = '';
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

    // Form validation sebelum submit
    document.getElementById('mutasiForm')?.addEventListener('submit', function(e) {
        const jumlahInput = document.getElementById('jumlah');
        const jumlahBersih = parseInt(jumlahInput.value.replace(/[^\d]/g, '')) || 0;
        
        // Validasi jumlah
        if (jumlahBersih <= 0) {
            e.preventDefault();
            alert('Jumlah harus lebih besar dari 0');
            return false;
        }

        // Validasi akun
        <?php if ($tipe == 'Masuk'): ?>
        const coaIdKredit = document.getElementById('coa_id_kredit');
        if (!coaIdKredit || !coaIdKredit.value) {
            e.preventDefault();
            alert('Pilih akun sumber dana');
            return false;
        }
        <?php else: ?>
        const coaIdDebit = document.getElementById('coa_id_debit');
        if (!coaIdDebit || !coaIdDebit.value) {
            e.preventDefault();
            alert('Pilih akun tujuan dana');
            return false;
        }
        <?php endif; ?>

        // Validasi bank
        <?php if ($tipe == 'Masuk'): ?>
        const bankTujuan = document.getElementById('bank_tujuan');
        if (!bankTujuan || !bankTujuan.value) {
            e.preventDefault();
            alert('Isi bank tujuan');
            return false;
        }
        <?php else: ?>
        const bankAsal = document.getElementById('bank_asal');
        if (!bankAsal || !bankAsal.value) {
            e.preventDefault();
            alert('Isi bank asal');
            return false;
        }
        <?php endif; ?>

        // Validasi keterangan
        const keterangan = document.getElementById('keterangan');
        if (!keterangan || !keterangan.value.trim()) {
            e.preventDefault();
            alert('Isi keterangan transaksi');
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
        
        // Tambahkan ke form
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

/* List styling */
.list-unstyled li:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}

/* Terbilang preview */
#terbilangPreview {
    font-size: 11px;
    margin-top: 5px;
    display: block;
}

/* Alert notification animation */
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