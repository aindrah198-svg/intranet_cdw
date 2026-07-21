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
                        <i class="fas fa-user-tie me-2"></i> Tambah Pengeluaran Pribadi
                    </h2>
                    <p class="page-subtitle text-muted mb-0">
                        Catat pengeluaran untuk kepentingan pribadi (Kasbon, Reimbursement, Prive, dll)
                    </p>
                </div>
                <div>
                    <a href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi') ?>" class="btn btn-secondary">
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
                        <i class="fas fa-pencil-alt me-2"></i> Form Pengeluaran Pribadi
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?= site_url('accounting/kas-bank/pengeluaran-pribadi/store') ?>" 
                          method="post" 
                          enctype="multipart/form-data"
                          id="pengeluaranForm">
                        
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
                            <!-- Tanggal -->
                            <div class="col-md-6 mb-3">
                                <label for="tanggal" class="form-label">
                                    Tanggal <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control <?= session('errors.tanggal') ? 'is-invalid' : '' ?>" 
                                       id="tanggal" 
                                       name="tanggal" 
                                       value="<?= old('tanggal', $pengeluaran['tanggal']) ?>"
                                       required>
                                <?php if (session('errors.tanggal')): ?>
                                    <div class="invalid-feedback"><?= session('errors.tanggal') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Jenis Pengeluaran -->
                            <div class="col-md-6 mb-3">
                                <label for="jenis" class="form-label">
                                    Jenis Pengeluaran <span class="text-danger">*</span>
                                </label>
                                <select class="form-select <?= session('errors.jenis') ? 'is-invalid' : '' ?>" 
                                        id="jenis" 
                                        name="jenis" 
                                        required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="Kasbon" <?= old('jenis', $jenis) == 'Kasbon' ? 'selected' : '' ?>>Kasbon</option>
                                    <option value="Reimbursement" <?= old('jenis', $jenis) == 'Reimbursement' ? 'selected' : '' ?>>Reimbursement</option>
                                    <option value="Dana Talangan" <?= old('jenis', $jenis) == 'Dana Talangan' ? 'selected' : '' ?>>Dana Talangan</option>
                                    <option value="Klaim Pribadi" <?= old('jenis', $jenis) == 'Klaim Pribadi' ? 'selected' : '' ?>>Klaim Pribadi</option>
                                    <option value="Prive" <?= old('jenis', $jenis) == 'Prive' ? 'selected' : '' ?>>Prive</option>
                                    <option value="Lainnya" <?= old('jenis', $jenis) == 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                </select>
                                <?php if (session('errors.jenis')): ?>
                                    <div class="invalid-feedback"><?= session('errors.jenis') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Karyawan -->
                            <div class="col-md-6 mb-3">
                                <label for="karyawan_id" class="form-label">
                                    Karyawan <span class="text-danger">*</span>
                                </label>
                                <select class="form-select <?= session('errors.karyawan_id') ? 'is-invalid' : '' ?>" 
                                        id="karyawan_id" 
                                        name="karyawan_id" 
                                        required>
                                    <option value="">-- Pilih Karyawan --</option>
                                    <?php foreach ($karyawanOptions as $karyawan): ?>
                                        <option value="<?= $karyawan['id'] ?>" 
                                            data-nik="<?= $karyawan['nik'] ?>"
                                            data-jabatan="<?= $karyawan['jabatan'] ?>"
                                            <?= old('karyawan_id') == $karyawan['id'] ? 'selected' : '' ?>>
                                            <?= esc($karyawan['nik']) ?> - <?= esc($karyawan['nama_lengkap']) ?> 
                                            (<?= esc($karyawan['jabatan'] ?? '-') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (session('errors.karyawan_id')): ?>
                                    <div class="invalid-feedback"><?= session('errors.karyawan_id') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- No Bukti -->
                            <div class="col-md-6 mb-3">
                                <label for="no_bukti" class="form-label">
                                    Nomor Bukti
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="no_bukti" 
                                       name="no_bukti" 
                                       value="<?= old('no_bukti') ?>"
                                       placeholder="No. Faktur / Kwitansi">
                                <small class="text-muted">Opsional, nomor bukti pendukung</small>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Akun Debit -->
                            <div class="col-md-6 mb-3">
                                <label for="coa_id_debit" class="form-label">
                                    Akun Debit <span class="text-danger">*</span>
                                </label>
                                <select class="form-select <?= session('errors.coa_id_debit') ? 'is-invalid' : '' ?>" 
                                        id="coa_id_debit" 
                                        name="coa_id_debit" 
                                        required>
                                    <option value="">-- Pilih Akun Debit --</option>
                                    <?php foreach ($coaDebitOptions as $akun): ?>
                                        <option value="<?= $akun['id'] ?>" 
                                            data-tipe="<?= $akun['tipe_akun'] ?>"
                                            data-kode="<?= $akun['kode_akun'] ?>"
                                            data-saldo="<?= $akun['saldo_normal'] ?? '' ?>"
                                            <?= old('coa_id_debit') == $akun['id'] ? 'selected' : '' ?>>
                                            <?= esc($akun['kode_akun']) ?> - <?= esc($akun['nama_akun']) ?> 
                                            (<?= $akun['tipe_akun'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Akun yang akan didebit (Beban/Aset untuk Reimbursement, Piutang untuk Kasbon)
                                </small>
                                <?php if (session('errors.coa_id_debit')): ?>
                                    <div class="invalid-feedback"><?= session('errors.coa_id_debit') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Akun Kredit -->
                            <div class="col-md-6 mb-3">
                                <label for="coa_id_kredit" class="form-label">
                                    Akun Kredit <span class="text-danger">*</span>
                                </label>
                                <select class="form-select <?= session('errors.coa_id_kredit') ? 'is-invalid' : '' ?>" 
                                        id="coa_id_kredit" 
                                        name="coa_id_kredit" 
                                        required>
                                    <option value="">-- Pilih Akun Kredit --</option>
                                    <?php foreach ($coaKreditOptions as $akun): ?>
                                        <option value="<?= $akun['id'] ?>" 
                                            data-tipe="<?= $akun['tipe_akun'] ?>"
                                            data-kode="<?= $akun['kode_akun'] ?>"
                                            data-saldo="<?= $akun['saldo_normal'] ?? '' ?>"
                                            <?= old('coa_id_kredit') == $akun['id'] ? 'selected' : '' ?>>
                                            <?= esc($akun['kode_akun']) ?> - <?= esc($akun['nama_akun']) ?> 
                                            (<?= $akun['tipe_akun'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Akun yang akan dikredit (Hutang Karyawan untuk Reimbursement, Kas/Bank untuk Kasbon)
                                </small>
                                <?php if (session('errors.coa_id_kredit')): ?>
                                    <div class="invalid-feedback"><?= session('errors.coa_id_kredit') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                     <!-- Pilih Proyek/SPK -->
<div class="row">
    <div class="col-md-12 mb-3">
        <label for="spk_id" class="form-label">
            <i class="fas fa-project-diagram me-1"></i> Proyek / SPK
        </label>
        <select class="form-select" id="spk_id" name="spk_id">
            <option value="">-- Pilih Proyek (Opsional) --</option>
            <?php if (isset($spkOptions) && !empty($spkOptions)): ?>
                <?php foreach ($spkOptions as $spk): ?>
                    <?php 
                    // Handle baik object maupun array
                    $spkId = is_object($spk) ? $spk->id : $spk['id'];
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
                                           name="jumlah_display" 
                                           value="<?= old('jumlah_display', '0') ?>"
                                           placeholder="0"
                                           required>
                                </div>
                                <div id="terbilang" class="small text-muted mt-1"></div>
                                <?php if (session('errors.jumlah')): ?>
                                    <div class="invalid-feedback d-block"><?= session('errors.jumlah') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Tanggal Jatuh Tempo -->
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_jatuh_tempo" class="form-label">
                                    Tanggal Jatuh Tempo
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="tanggal_jatuh_tempo" 
                                       name="tanggal_jatuh_tempo" 
                                       value="<?= old('tanggal_jatuh_tempo', $pengeluaran['tanggal_jatuh_tempo']) ?>">
                                <small class="text-muted">Opsional, untuk kasbon/pinjaman</small>
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
                                      rows="2"
                                      placeholder="Deskripsi pengeluaran..."
                                      required><?= old('keterangan') ?></textarea>
                            <?php if (session('errors.keterangan')): ?>
                                <div class="invalid-feedback"><?= session('errors.keterangan') ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Tujuan Penggunaan -->
                        <div class="mb-3">
                            <label for="tujuan_penggunaan" class="form-label">
                                Tujuan Penggunaan
                            </label>
                            <textarea class="form-control" 
                                      id="tujuan_penggunaan" 
                                      name="tujuan_penggunaan" 
                                      rows="2"
                                      placeholder="Detail tujuan penggunaan dana..."><?= old('tujuan_penggunaan') ?></textarea>
                            <small class="text-muted">Opsional, detail lebih lanjut tentang penggunaan dana</small>
                        </div>

                        <!-- Upload Lampiran -->
                        <div class="mb-3">
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

                        <!-- Catatan Internal -->
                        <div class="mb-3">
                            <label for="catatan_internal" class="form-label">
                                <i class="fas fa-lock me-1"></i> Catatan Internal
                            </label>
                            <textarea class="form-control" 
                                      id="catatan_internal" 
                                      name="catatan_internal" 
                                      rows="2"
                                      placeholder="Catatan untuk internal (tidak tampil di slip/kuitansi)"><?= old('catatan_internal') ?></textarea>
                            <small class="text-muted">Hanya untuk keperluan internal</small>
                        </div>

                        <!-- Info Box - Dinamis berdasarkan jenis -->
                        <div class="alert alert-info" id="infoBox">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="fas fa-info-circle fa-2x"></i>
                                </div>
                                <div id="infoContent">
                                    <strong>Informasi:</strong>
                                    <ul class="mb-0 mt-1">
                                        <li>Transaksi akan disimpan sebagai <strong>Draft</strong> terlebih dahulu.</li>
                                        <li>Setelah yakin dengan data, Anda dapat <strong>Posting</strong> ke jurnal dari halaman daftar.</li>
                                        <li>Data yang sudah diposting tidak dapat diedit/dihapus.</li>
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
            <!-- Informasi Jenis -->
            <div class="modern-card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-tag me-2"></i> Informasi Jenis
                    </h5>
                </div>
                <div class="card-body" id="jenisInfo">
                    <div id="infoKasbon" style="display: none;">
                        <h6 class="text-primary"><i class="fas fa-hand-holding-usd me-1"></i> Kasbon</h6>
                        <p class="small text-muted">Pinjaman uang kepada karyawan yang harus dikembalikan.</p>
                        <ul class="small">
                            <li><strong>Debit:</strong> Piutang Karyawan</li>
                            <li><strong>Kredit:</strong> Kas/Bank</li>
                            <li><strong>Status:</strong> Hutang karyawan ke perusahaan</li>
                        </ul>
                    </div>
                    <div id="infoReimbursement" style="display: none;">
                        <h6 class="text-success"><i class="fas fa-undo-alt me-1"></i> Reimbursement</h6>
                        <p class="small text-muted">Penggantian biaya yang sudah dikeluarkan karyawan.</p>
                        <ul class="small">
                            <li><strong>Debit:</strong> Beban/Aset (sesuai jenis pengeluaran)</li>
                            <li><strong>Kredit:</strong> Hutang Karyawan</li>
                            <li><strong>Status:</strong> Hutang perusahaan ke karyawan</li>
                        </ul>
                    </div>
                    <div id="infoDanaTalangan" style="display: none;">
                        <h6 class="text-info"><i class="fas fa-hand-holding-heart me-1"></i> Dana Talangan</h6>
                        <p class="small text-muted">Uang muka untuk keperluan tertentu.</p>
                        <ul class="small">
                            <li><strong>Debit:</strong> Piutang Karyawan</li>
                            <li><strong>Kredit:</strong> Kas/Bank</li>
                            <li><strong>Status:</strong> Hutang karyawan ke perusahaan</li>
                        </ul>
                    </div>
                    <div id="infoKlaimPribadi" style="display: none;">
                        <h6 class="text-warning"><i class="fas fa-file-invoice me-1"></i> Klaim Pribadi</h6>
                        <p class="small text-muted">Klaim biaya pribadi karyawan.</p>
                        <ul class="small">
                            <li><strong>Debit:</strong> Beban/Aset (jika terkait pekerjaan)</li>
                            <li><strong>Kredit:</strong> Hutang Karyawan</li>
                        </ul>
                    </div>
                    <div id="infoPrive" style="display: none;">
                        <h6 class="text-secondary"><i class="fas fa-user-tie me-1"></i> Prive</h6>
                        <p class="small text-muted">Pengambilan pribadi pemilik/direktur.</p>
                        <ul class="small">
                            <li><strong>Debit:</strong> Prive (3-1301)</li>
                            <li><strong>Kredit:</strong> Kas/Bank</li>
                            <li><strong>Catatan:</strong> Mengurangi modal perusahaan</li>
                        </ul>
                    </div>
                    <div id="infoLainnya" style="display: none;">
                        <h6 class="text-dark"><i class="fas fa-ellipsis-h me-1"></i> Lainnya</h6>
                        <p class="small text-muted">Jenis pengeluaran pribadi lainnya.</p>
                    </div>
                    <div id="infoDefault">
                        <p class="small text-muted">Pilih jenis pengeluaran untuk melihat informasi detail.</p>
                    </div>
                </div>
            </div>

            <!-- Akun Rekomendasi -->
            <div class="modern-card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-book me-2"></i> Rekomendasi Akun
                    </h5>
                </div>
                <div class="card-body">
                    <div id="rekomendasiAkun">
                        <p class="small text-muted">Pilih jenis untuk melihat rekomendasi akun.</p>
                    </div>
                    
                    <!-- Akun Hutang Karyawan -->
                    <div class="mt-3 p-2 bg-light rounded">
                        <h6 class="mb-2"><i class="fas fa-credit-card me-1"></i> Akun Hutang Karyawan</h6>
                        <p class="small mb-1">
                            <span class="badge bg-secondary">2-1500</span> Hutang Karyawan & Direktur
                        </p>
                        <p class="small text-muted">Gunakan akun ini untuk kredit pada Reimbursement</p>
                    </div>

                    <!-- Akun Piutang Karyawan -->
                    <div class="mt-2 p-2 bg-light rounded">
                        <h6 class="mb-2"><i class="fas fa-hand-holding-usd me-1"></i> Akun Piutang Karyawan</h6>
                        <p class="small mb-1">
                            <span class="badge bg-info">1-1200</span> Piutang Karyawan
                        </p>
                        <p class="small text-muted">Gunakan akun ini untuk debit pada Kasbon</p>
                    </div>

                    <!-- Akun Prive -->
                    <div class="mt-2 p-2 bg-light rounded">
                        <h6 class="mb-2"><i class="fas fa-user-tie me-1"></i> Akun Prive</h6>
                        <p class="small mb-1">
                            <span class="badge bg-secondary">3-1301</span> Prive
                        </p>
                        <p class="small text-muted">Gunakan akun ini untuk debit pada Prive</p>
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
                        <li class="mb-2"><strong>Kasbon:</strong> Gunakan akun debit Piutang Karyawan</li>
                        <li class="mb-2"><strong>Reimbursement:</strong> Gunakan akun debit Beban yang sesuai, akun kredit Hutang Karyawan</li>
                        <li class="mb-2"><strong>Prive:</strong> Gunakan akun debit Prive (3-1301)</li>
                        <li class="mb-2">Isi tanggal jatuh tempo untuk kasbon/pinjaman</li>
                        <li>Lampirkan bukti pendukung untuk memudahkan verifikasi</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const jenisSelect = document.getElementById('jenis');
    const infoBox = document.getElementById('infoBox');
    const infoContent = document.getElementById('infoContent');
    const rekomendasiDiv = document.getElementById('rekomendasiAkun');
    
    // Sembunyikan semua info jenis
    function hideAllJenisInfo() {
        document.getElementById('infoKasbon').style.display = 'none';
        document.getElementById('infoReimbursement').style.display = 'none';
        document.getElementById('infoDanaTalangan').style.display = 'none';
        document.getElementById('infoKlaimPribadi').style.display = 'none';
        document.getElementById('infoPrive').style.display = 'none';
        document.getElementById('infoLainnya').style.display = 'none';
        document.getElementById('infoDefault').style.display = 'none';
    }
    
    // Tampilkan info berdasarkan jenis
    function showJenisInfo(jenis) {
        hideAllJenisInfo();
        switch(jenis) {
            case 'Kasbon':
                document.getElementById('infoKasbon').style.display = 'block';
                break;
            case 'Reimbursement':
                document.getElementById('infoReimbursement').style.display = 'block';
                break;
            case 'Dana Talangan':
                document.getElementById('infoDanaTalangan').style.display = 'block';
                break;
            case 'Klaim Pribadi':
                document.getElementById('infoKlaimPribadi').style.display = 'block';
                break;
            case 'Prive':
                document.getElementById('infoPrive').style.display = 'block';
                break;
            case 'Lainnya':
                document.getElementById('infoLainnya').style.display = 'block';
                break;
            default:
                document.getElementById('infoDefault').style.display = 'block';
        }
    }
    
    // Update rekomendasi akun
    function updateRekomendasiAkun(jenis) {
        let html = '';
        switch(jenis) {
            case 'Kasbon':
                html = `
                    <h6 class="mb-2">Rekomendasi untuk Kasbon:</h6>
                    <div class="p-2 bg-light rounded mb-2">
                        <span class="badge bg-primary">Debit</span> Piutang Karyawan (1-120x)<br>
                        <span class="badge bg-success">Kredit</span> Kas/Bank (1-11xx)
                    </div>
                `;
                break;
            case 'Reimbursement':
                html = `
                    <h6 class="mb-2">Rekomendasi untuk Reimbursement:</h6>
                    <div class="p-2 bg-light rounded mb-2">
                        <span class="badge bg-primary">Debit</span> Beban (5-xxxx) / Aset (1-xxxx)<br>
                        <span class="badge bg-success">Kredit</span> Hutang Karyawan (2-1500)
                    </div>
                `;
                break;
            case 'Prive':
                html = `
                    <h6 class="mb-2">Rekomendasi untuk Prive:</h6>
                    <div class="p-2 bg-light rounded mb-2">
                        <span class="badge bg-primary">Debit</span> Prive (3-1301)<br>
                        <span class="badge bg-success">Kredit</span> Kas/Bank (1-11xx)
                    </div>
                `;
                break;
            default:
                html = '<p class="small text-muted">Pilih jenis untuk melihat rekomendasi akun.</p>';
        }
        rekomendasiDiv.innerHTML = html;
    }
    
    // Event change pada jenis
    if (jenisSelect) {
        showJenisInfo(jenisSelect.value);
        updateRekomendasiAkun(jenisSelect.value);
        
        jenisSelect.addEventListener('change', function() {
            showJenisInfo(this.value);
            updateRekomendasiAkun(this.value);
            
            // Load COA options via AJAX
            loadCoaOptions(this.value);
        });
    }
    
    // Load COA options via AJAX
    function loadCoaOptions(jenis) {
        // Load akun debit
        fetch('<?= site_url('accounting/kas-bank/pengeluaran-pribadi/ajaxGetCoaDebit') ?>?jenis=' + encodeURIComponent(jenis))
            .then(response => response.json())
            .then(data => {
                const coaDebitSelect = document.getElementById('coa_id_debit');
                if (coaDebitSelect && data.length > 0) {
                    const currentValue = coaDebitSelect.value;
                    coaDebitSelect.innerHTML = '<option value="">-- Pilih Akun Debit --</option>';
                    data.forEach(akun => {
                        const option = document.createElement('option');
                        option.value = akun.id;
                        option.textContent = akun.text;
                        if (currentValue && currentValue == akun.id) {
                            option.selected = true;
                        }
                        coaDebitSelect.appendChild(option);
                    });
                }
            });
        
        // Load akun kredit
        fetch('<?= site_url('accounting/kas-bank/pengeluaran-pribadi/ajaxGetCoaKredit') ?>?jenis=' + encodeURIComponent(jenis))
            .then(response => response.json())
            .then(data => {
                const coaKreditSelect = document.getElementById('coa_id_kredit');
                if (coaKreditSelect && data.length > 0) {
                    const currentValue = coaKreditSelect.value;
                    coaKreditSelect.innerHTML = '<option value="">-- Pilih Akun Kredit --</option>';
                    data.forEach(akun => {
                        const option = document.createElement('option');
                        option.value = akun.id;
                        option.textContent = akun.text;
                        if (currentValue && currentValue == akun.id) {
                            option.selected = true;
                        }
                        coaKreditSelect.appendChild(option);
                    });
                }
            });
    }

    const jumlahInput = document.getElementById('jumlah');
    const terbilangDiv = document.getElementById('terbilang');

    // Format currency input
    if (jumlahInput) {
        // Set initial value jika ada
        let initialValue = jumlahInput.value.replace(/[^\d]/g, '');
        if (initialValue && parseInt(initialValue) > 0) {
            jumlahInput.value = formatRupiah(parseInt(initialValue));
            getTerbilang(parseInt(initialValue));
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
            fetch('<?= site_url('accounting/kas-bank/pengeluaran-pribadi/ajaxGetTerbilang') ?>?jumlah=' + angka)
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
    document.getElementById('pengeluaranForm')?.addEventListener('submit', function(e) {
        const jumlahInput = document.getElementById('jumlah');
        const jumlahBersih = parseInt(jumlahInput.value.replace(/[^\d]/g, '')) || 0;
        
        // Validasi jumlah
        if (jumlahBersih <= 0) {
            e.preventDefault();
            alert('Jumlah harus lebih besar dari 0');
            return false;
        }

        // Validasi akun debit
        const coaDebit = document.getElementById('coa_id_debit');
        if (!coaDebit || !coaDebit.value) {
            e.preventDefault();
            alert('Pilih akun debit');
            return false;
        }

        // Validasi akun kredit
        const coaKredit = document.getElementById('coa_id_kredit');
        if (!coaKredit || !coaKredit.value) {
            e.preventDefault();
            alert('Pilih akun kredit');
            return false;
        }

        // Validasi akun sama
        if (coaDebit.value === coaKredit.value) {
            e.preventDefault();
            alert('Akun debit dan kredit harus berbeda');
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

/* Badge styling */
.badge {
    padding: 0.4rem 0.6rem;
    font-weight: 500;
}

/* Background light */
.bg-light {
    background-color: #f8f9fa !important;
}

/* List styling */
.list-unstyled li:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
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

/* Side panel info boxes */
#jenisInfo > div {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<?= $this->include('accounting/templates/footer') ?>