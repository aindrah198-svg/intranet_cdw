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
                        <i class="fas fa-edit me-2 text-warning"></i> Edit Mutasi Bank
                    </h2>
                    <p class="page-subtitle text-muted mb-0">
                        Edit transaksi <?= $mutasi['kode_transaksi'] ?>
                    </p>
                </div>
                <div>
                    <a href="<?= site_url('accounting/kas-bank/mutasi-bank/detail/' . $mutasi['id']) ?>" class="btn btn-info me-2">
                        <i class="fas fa-eye me-1"></i> Detail
                    </a>
                    <a href="<?= site_url('accounting/kas-bank/mutasi-bank') ?>" class="btn btn-secondary">
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
                        <strong>Perhatian!</strong> Anda sedang mengedit transaksi dengan status <strong>Draft</strong>.
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
                        <i class="fas fa-pencil-alt me-2"></i> Form Edit Transaksi
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?= site_url('accounting/kas-bank/mutasi-bank/update/' . $mutasi['id']) ?>" 
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

                        <!-- Hidden Fields - PERUBAHAN: Konversi tipe database ke tipe user -->
                        <?php 
                        // Konversi tipe database (Kredit/Debit) ke tipe user (Masuk/Keluar)
                        $tipeUser = ($mutasi['tipe'] == 'Kredit') ? 'Masuk' : 'Keluar';
                        ?>
                        <input type="hidden" name="tipe" value="<?= $tipeUser ?>">
                        
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
                                       value="<?= old('no_referensi', $mutasi['no_referensi']) ?>"
                                       placeholder="No. Cek / BG / Transfer">
                                <small class="text-muted">Opsional, nomor referensi dari bank</small>
                            </div>
                        </div>

                        <!-- ===== PERUBAHAN: DROPDOWN AKUN DENGAN LABEL YANG LEBIH USER-FRIENDLY ===== -->
                        <div class="row">
                            <?php if ($mutasi['tipe'] == 'Kredit'): ?>
                                <!-- Untuk Transaksi Masuk (Uang Masuk) -->
                                <div class="col-md-6 mb-3">
                                    <label for="bank_tujuan" class="form-label">
                                        Bank Tujuan <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control <?= session('errors.bank_tujuan') ? 'is-invalid' : '' ?>" 
                                           id="bank_tujuan" 
                                           name="bank_tujuan" 
                                           value="<?= old('bank_tujuan', $mutasi['bank_tujuan']) ?>"
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
                                                <?= old('coa_id_kredit', $mutasi['coa_id_kredit']) == $akun['id'] ? 'selected' : '' ?>>
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
                                <!-- Untuk Transaksi Keluar (Uang Keluar) -->
                                <div class="col-md-6 mb-3">
                                    <label for="bank_asal" class="form-label">
                                        Bank Asal <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control <?= session('errors.bank_asal') ? 'is-invalid' : '' ?>" 
                                           id="bank_asal" 
                                           name="bank_asal" 
                                           value="<?= old('bank_asal', $mutasi['bank_asal']) ?>"
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
                                                <?= old('coa_id_debit', $mutasi['coa_id_debit']) == $akun['id'] ? 'selected' : '' ?>>
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
                                                    <?= old('spk_id', $mutasi['spk_id'] ?? '') == $spk->id ? 'selected' : '' ?>>
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
                                           value="<?= old('jumlah_display', number_format($mutasi['jumlah'], 0, ',', '.')) ?>"
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
                                <small class="text-muted">Format: PDF, JPG, PNG (Max: 2MB). Kosongkan jika tidak ingin mengubah.</small>
                                
                                <?php if (!empty($mutasi['lampiran'])): ?>
                                    <div class="mt-2 p-2 bg-light rounded">
                                        <small>
                                            <i class="fas fa-paperclip me-1"></i>
                                            Lampiran saat ini: 
                                            <a href="<?= base_url($mutasi['lampiran']) ?>" target="_blank" class="text-primary">
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
                                      placeholder="Deskripsi transaksi..."
                                      required><?= old('keterangan', $mutasi['keterangan']) ?></textarea>
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
                                        <li>Transaksi ini berstatus <strong>Draft</strong> dan dapat diedit.</li>
                                        <li>Setelah selesai mengedit, Anda dapat <strong>Posting</strong> ulang ke jurnal.</li>
                                        <li>Akun bank akan ditentukan otomatis saat posting berdasarkan bank asal/tujuan.</li>
                                        <li>Proyek/SPK dapat dipilih jika transaksi terkait proyek tertentu.</li>
                                        <li class="mt-1"><i class="fas fa-check-circle text-success me-1"></i> <strong>Fleksibel:</strong> Semua akun dapat dipilih untuk transaksi masuk maupun keluar.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="d-flex justify-content-end mt-4">
                            <a href="<?= site_url('accounting/kas-bank/mutasi-bank/detail/' . $mutasi['id']) ?>" class="btn btn-secondary me-2">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-1"></i> Update Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Side Panel -->
        <div class="col-md-4">
            <!-- Informasi Transaksi -->
            <div class="modern-card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i> Informasi Transaksi
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted">Kode Transaksi</td>
                            <td class="text-end fw-bold"><?= $mutasi['kode_transaksi'] ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tipe Transaksi</td>
                            <td class="text-end">
                                <?php if ($mutasi['tipe'] == 'Kredit'): ?>
                                    <span class="badge bg-success">Masuk (Uang Masuk)</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Keluar (Uang Keluar)</span>
                                <?php endif; ?>
                             </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td class="text-end">
                                <span class="badge bg-secondary"><?= $mutasi['status'] ?></span>
                             </td>
                        </tr>
                        <?php if (!empty($mutasi['spk_id'])): ?>
                        <tr>
                            <td class="text-muted">Proyek/SPK</td>
                            <td class="text-end">
                                <span class="badge bg-info"><?= esc($mutasi['nomor_spk'] ?? '') ?></span>
                             </td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td class="text-muted">Dibuat Oleh</td>
                            <td class="text-end"><?= $mutasi['creator_name'] ?? '-' ?> </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Dibuat Tanggal</td>
                            <td class="text-end"><?= date('d/m/Y H:i', strtotime($mutasi['created_at'])) ?> </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Informasi Jurnal yang Akan Dihasilkan -->
            <div class="modern-card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-book me-2"></i> Jurnal yang Akan Dihasilkan
                    </h5>
                </div>
                <div class="card-body">
                    <?php if ($mutasi['tipe'] == 'Kredit'): ?>
                        <div class="bg-light p-2 rounded small mb-2">
                            <div class="text-success">[Debit] Kas/Bank</div>
                            <div class="text-danger">[Kredit] Akun Sumber Dana</div>
                        </div>
                        <p class="small text-muted mb-0">
                            Saat diposting, sistem akan mendebit akun bank dan mengkredit akun sumber yang Anda pilih.
                        </p>
                    <?php else: ?>
                        <div class="bg-light p-2 rounded small mb-2">
                            <div class="text-success">[Debit] Akun Tujuan Dana</div>
                            <div class="text-danger">[Kredit] Kas/Bank</div>
                        </div>
                        <p class="small text-muted mb-0">
                            Saat diposting, sistem akan mendebit akun tujuan dan mengkredit akun bank yang Anda pilih.
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Akun Bank Tersedia -->
            <div class="modern-card">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-university me-2"></i> Akun Bank Tersedia
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">Akun bank yang akan digunakan saat posting:</p>
                    <ul class="list-unstyled">
                        <?php foreach ($coaBankOptions as $bank): ?>
                        <li class="mb-2 pb-2 border-bottom">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <span class="fw-bold"><?= esc($bank['kode_akun']) ?></span>
                                    <span class="text-muted"> - <?= esc($bank['nama_akun']) ?></span>
                                </div>
                                <?php 
                                $isMatch = false;
                                if ($mutasi['tipe'] == 'Debit' && !empty($mutasi['bank_asal'])) {
                                    $isMatch = stripos($bank['nama_akun'], $mutasi['bank_asal']) !== false;
                                } elseif ($mutasi['tipe'] == 'Kredit' && !empty($mutasi['bank_tujuan'])) {
                                    $isMatch = stripos($bank['nama_akun'], $mutasi['bank_tujuan']) !== false;
                                }
                                ?>
                                <?php if ($isMatch): ?>
                                    <small class="text-success"><i class="fas fa-check"></i> Cocok</small>
                                <?php endif; ?>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <p class="small text-muted mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        Sistem akan mencocokkan bank asal/tujuan dengan nama akun saat posting.
                    </p>
                </div>
            </div>

            <!-- Tips Edit -->
            <div class="modern-card mt-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i> Tips Edit
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="small mb-0 ps-3">
                        <li class="mb-2"><strong>Transaksi Masuk:</strong> Pilih akun sumber dana (misal: Pendapatan, Piutang, Modal)</li>
                        <li class="mb-2"><strong>Transaksi Keluar:</strong> Pilih akun tujuan dana (misal: Beban, Pembelian Aset, Hutang)</li>
                        <li class="mb-2">Pastikan bank asal/tujuan sesuai dengan rekening yang digunakan.</li>
                        <li class="mb-2">Periksa kembali jumlah dan keterangan sebelum menyimpan.</li>
                        <li>Setelah update, Anda perlu memposting ulang ke jurnal.</li>
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
            if (terbilangPreview) terbilangPreview.innerHTML = '';
            return;
        }
        
        try {
            const response = await fetch(`<?= site_url('accounting/kas-bank/mutasi-bank/ajaxGetTerbilang') ?>?jumlah=${jumlahBersih}`);
            const data = await response.json();
            
            if (data.success && data.terbilang && terbilangPreview) {
                terbilangPreview.innerHTML = `<i class="fas fa-language me-1"></i> Terbilang: ${data.terbilang}`;
                terbilangPreview.style.color = '#28a745';
            } else if (terbilangPreview) {
                terbilangPreview.innerHTML = '';
            }
        } catch (error) {
            console.error('Error fetching terbilang:', error);
            if (terbilangPreview) terbilangPreview.innerHTML = '';
        }
    }

    // Format currency input dengan debounce untuk terbilang
    let timeoutId;
    if (jumlahInput) {
        // Ambil nilai awal untuk terbilang
        let initialValue = jumlahInput.value.replace(/[^\d]/g, '');
        if (initialValue && parseInt(initialValue) > 0) {
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
                if (terbilangPreview) terbilangPreview.innerHTML = '';
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
        <?php if ($mutasi['tipe'] == 'Kredit'): ?>
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
        <?php if ($mutasi['tipe'] == 'Kredit'): ?>
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
    border-left: 4px solid #4dabf7;
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

#terbilangPreview {
    font-size: 11px;
    margin-top: 5px;
    display: block;
}
</style>

<?= $this->include('accounting/templates/footer') ?>