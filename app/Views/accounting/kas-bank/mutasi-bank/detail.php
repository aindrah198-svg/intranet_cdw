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
                        <i class="fas fa-file-invoice me-2"></i> Detail Mutasi Bank
                    </h2>
                    <p class="page-subtitle text-muted mb-0">
                        Informasi lengkap transaksi <?= $mutasi['kode_transaksi'] ?>
                    </p>
                </div>
                <div>
                    <a href="<?= site_url('accounting/kas-bank/mutasi-bank') ?>" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                    <?php if ($mutasi['status'] == 'Draft'): ?>
                        <a href="<?= site_url('accounting/kas-bank/mutasi-bank/edit/' . $mutasi['id']) ?>" class="btn btn-warning me-2">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <button type="button" class="btn btn-success" onclick="postMutasi(<?= $mutasi['id'] ?>)">
                            <i class="fas fa-check me-1"></i> Posting
                        </button>
                    <?php endif; ?>
                    <?php if ($mutasi['status'] == 'Posted'): ?>
                        <a href="<?= site_url('accounting/kas-bank/mutasi-bank/print/' . $mutasi['id']) ?>" class="btn btn-info" target="_blank">
                            <i class="fas fa-print me-1"></i> Print
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Bar - PERUBAHAN: Tampilkan Masuk/Keluar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="modern-card">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center flex-wrap">
                        <div class="me-4 mb-2">
                            <span class="text-muted">Status:</span>
                            <?php
                            $statusClass = [
                                'Draft' => 'bg-secondary',
                                'Posted' => 'bg-success',
                                'Dibatalkan' => 'bg-danger'
                            ][$mutasi['status']] ?? 'bg-secondary';
                            ?>
                            <span class="badge <?= $statusClass ?> fs-6 p-2"><?= $mutasi['status'] ?></span>
                        </div>
                        <div class="me-4 mb-2">
                            <span class="text-muted">Tipe:</span>
                            <?php 
                            // PERUBAHAN: Konversi tipe database ke user-friendly
                            $tipeUser = ($mutasi['tipe'] == 'Kredit') ? 'Masuk' : 'Keluar';
                            $tipeIcon = ($mutasi['tipe'] == 'Kredit') ? 'fa-arrow-down' : 'fa-arrow-up';
                            $tipeClass = ($mutasi['tipe'] == 'Kredit') ? 'bg-success' : 'bg-danger';
                            ?>
                            <span class="badge <?= $tipeClass ?> fs-6 p-2">
                                <i class="fas <?= $tipeIcon ?> me-1"></i> <?= $tipeUser ?> (Uang <?= $tipeUser ?>)
                            </span>
                        </div>
                        <?php if (!empty($mutasi['jurnal_id'])): ?>
                        <div class="mb-2">
                            <span class="text-muted">Jurnal:</span>
                            <a href="<?= site_url('accounting/jurnal/detail/' . $mutasi['jurnal_id']) ?>" class="text-primary">
                                <?= $mutasi['nomor_jurnal'] ?? 'Lihat Jurnal' ?>
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informasi Utama -->
        <div class="col-md-8">
            <div class="modern-card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i> Informasi Transaksi
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="40%" class="text-muted">Kode Transaksi</td>
                                    <td width="10%">:</td>
                                    <td class="fw-bold"><?= esc($mutasi['kode_transaksi']) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tanggal Transaksi</td>
                                    <td>:</td>
                                    <td><?= date('d F Y', strtotime($mutasi['tanggal'])) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Nomor Referensi</td>
                                    <td>:</td>
                                    <td><?= !empty($mutasi['no_referensi']) ? esc($mutasi['no_referensi']) : '-' ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Jumlah</td>
                                    <td>:</td>
                                    <td>
                                        <span class="fw-bold <?= $mutasi['tipe'] == 'Kredit' ? 'text-success' : 'text-danger' ?>" style="font-size: 1.2rem;">
                                            Rp <?= number_format($mutasi['jumlah'], 0, ',', '.') ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Proyek / SPK</td>
                                    <td>:</td>
                                    <td>
                                        <?php if (!empty($mutasi['nomor_spk'])): ?>
                                            <span class="badge bg-info p-2"><?= esc($mutasi['nomor_spk']) ?></span>
                                            <small class="d-block text-muted mt-1">
                                                <i class="fas fa-project-diagram me-1"></i>
                                                <?= esc($mutasi['judul_pekerjaan'] ?? '') ?>
                                            </small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6 mb-3">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="40%" class="text-muted">Dibuat Oleh</td>
                                    <td width="10%">:</td>
                                    <td><?= !empty($mutasi['creator_name']) ? esc($mutasi['creator_name']) : '-' ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Dibuat Tanggal</td>
                                    <td>:</td>
                                    <td><?= date('d F Y H:i', strtotime($mutasi['created_at'])) ?></td>
                                </tr>
                                <?php if (!empty($mutasi['updated_at']) && $mutasi['updated_at'] != $mutasi['created_at']): ?>
                                <tr>
                                    <td class="text-muted">Terakhir Update</td>
                                    <td>:</td>
                                    <td><?= date('d F Y H:i', strtotime($mutasi['updated_at'])) ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if (!empty($mutasi['posted_at'])): ?>
                                <tr>
                                    <td class="text-muted">Diposting Tanggal</td>
                                    <td>:</td>
                                    <td><?= date('d F Y H:i', strtotime($mutasi['posted_at'])) ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-align-left me-1"></i> Keterangan:
                            </h6>
                            <div class="p-3 bg-light rounded">
                                <?php 
                                $keterangan = $mutasi['keterangan'] ?? '';
                                
                                if (is_array($keterangan)) {
                                    echo '<pre class="mb-0" style="font-family: monospace; font-size: 0.9rem; background: #f8f9fa; padding: 10px; border-radius: 5px;">';
                                    print_r($keterangan);
                                    echo '</pre>';
                                } elseif (is_object($keterangan)) {
                                    echo '<pre class="mb-0" style="font-family: monospace; font-size: 0.9rem; background: #f8f9fa; padding: 10px; border-radius: 5px;">';
                                    print_r((array)$keterangan);
                                    echo '</pre>';
                                } else {
                                    echo nl2br(htmlspecialchars($keterangan, ENT_QUOTES, 'UTF-8'));
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Perbankan - PERUBAHAN: Label lebih user-friendly -->
            <div class="modern-card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-university me-2"></i> Informasi Perbankan
                    </h5>
                </div>
                <div class="card-body">
                    <?php if ($mutasi['tipe'] == 'Kredit'): ?>
                        <!-- Transaksi Masuk (Uang Masuk) -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-box">
                                    <label class="text-muted mb-1">
                                        <i class="fas fa-arrow-down text-success me-1"></i> Bank Tujuan (Penerima)
                                    </label>
                                    <p class="fw-bold fs-5"><?= !empty($mutasi['bank_tujuan']) ? esc($mutasi['bank_tujuan']) : '-' ?></p>
                                    <small class="text-muted">Rekening perusahaan yang menerima uang</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <label class="text-muted mb-1">
                                        <i class="fas fa-book me-1"></i> Akun Sumber Dana (Kredit)
                                    </label>
                                    <p class="fw-bold"><?= !empty($mutasi['kode_akun_kredit']) ? esc($mutasi['kode_akun_kredit']) : '' ?> - <?= !empty($mutasi['nama_akun_kredit']) ? esc($mutasi['nama_akun_kredit']) : '-' ?></p>
                                    <small class="text-muted">
                                        <i class="fas fa-tag me-1"></i> Tipe: <?= !empty($mutasi['tipe_akun_kredit']) ? esc($mutasi['tipe_akun_kredit']) : '-' ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-light small mt-3 mb-0">
                            <i class="fas fa-info-circle text-info me-1"></i>
                            <strong>Penjelasan Jurnal:</strong> Kas/Bank (Debit) | Akun Sumber Dana (Kredit)
                        </div>
                    <?php else: ?>
                        <!-- Transaksi Keluar (Uang Keluar) -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-box">
                                    <label class="text-muted mb-1">
                                        <i class="fas fa-arrow-up text-danger me-1"></i> Bank Asal (Pengirim)
                                    </label>
                                    <p class="fw-bold fs-5"><?= !empty($mutasi['bank_asal']) ? esc($mutasi['bank_asal']) : '-' ?></p>
                                    <small class="text-muted">Rekening perusahaan yang mengirim uang</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <label class="text-muted mb-1">
                                        <i class="fas fa-book me-1"></i> Akun Tujuan Dana (Debit)
                                    </label>
                                    <p class="fw-bold"><?= !empty($mutasi['kode_akun_debit']) ? esc($mutasi['kode_akun_debit']) : '' ?> - <?= !empty($mutasi['nama_akun_debit']) ? esc($mutasi['nama_akun_debit']) : '-' ?></p>
                                    <small class="text-muted">
                                        <i class="fas fa-tag me-1"></i> Tipe: <?= !empty($mutasi['tipe_akun_debit']) ? esc($mutasi['tipe_akun_debit']) : '-' ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-light small mt-3 mb-0">
                            <i class="fas fa-info-circle text-info me-1"></i>
                            <strong>Penjelasan Jurnal:</strong> Akun Tujuan Dana (Debit) | Kas/Bank (Kredit)
                        </div>
                    <?php endif; ?>
                    
                    <!-- Informasi bahwa semua akun bisa dipilih -->
                    <div class="alert alert-info small mt-3 mb-0">
                        <i class="fas fa-check-circle text-success me-1"></i>
                        <strong>Fleksibel:</strong> Sistem mendukung pemilihan semua jenis akun (Pendapatan, Beban, Aset, Hutang, Ekuitas) untuk transaksi ini.
                    </div>
                </div>
            </div>
        </div>

        <!-- Side Panel -->
        <div class="col-md-4">
            <!-- Lampiran -->
            <div class="modern-card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-paperclip me-2"></i> Lampiran
                    </h5>
                </div>
                <div class="card-body text-center">
                    <?php if (!empty($mutasi['lampiran']) && file_exists(FCPATH . $mutasi['lampiran'])): ?>
                        <?php 
                        $ext = pathinfo($mutasi['lampiran'], PATHINFO_EXTENSION);
                        $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif']);
                        ?>
                        
                        <?php if ($isImage): ?>
                            <img src="<?= base_url($mutasi['lampiran']) ?>" class="img-fluid rounded mb-3" style="max-height: 200px;" alt="Lampiran">
                            <div>
                                <a href="<?= base_url($mutasi['lampiran']) ?>" target="_blank" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye me-1"></i> Lihat Full
                                </a>
                            </div>
                        <?php else: ?>
                            <i class="fas fa-file-pdf fa-4x text-danger mb-3"></i>
                            <p class="mb-2">File <?= strtoupper($ext) ?></p>
                            <a href="<?= base_url($mutasi['lampiran']) ?>" target="_blank" class="btn btn-sm btn-primary">
                                <i class="fas fa-download me-1"></i> Download
                            </a>
                        <?php endif; ?>
                        
                    <?php else: ?>
                        <i class="fas fa-paperclip fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Tidak ada lampiran</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Info Ringkas - PERUBAHAN: Tampilkan Masuk/Keluar -->
            <div class="modern-card">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i> Ringkasan
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted">Tipe Transaksi</td>
                            <td class="text-end">
                                <?php 
                                $tipeUser = ($mutasi['tipe'] == 'Kredit') ? 'Masuk' : 'Keluar';
                                $tipeClass = ($mutasi['tipe'] == 'Kredit') ? 'bg-success' : 'bg-danger';
                                ?>
                                <span class="badge <?= $tipeClass ?>"><?= $tipeUser ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td class="text-end">
                                <?php
                                $statusClass = [
                                    'Draft' => 'bg-secondary',
                                    'Posted' => 'bg-success',
                                    'Dibatalkan' => 'bg-danger'
                                ][$mutasi['status']] ?? 'bg-secondary';
                                ?>
                                <span class="badge <?= $statusClass ?>"><?= $mutasi['status'] ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Proyek/SPK</td>
                            <td class="text-end">
                                <?php if (!empty($mutasi['nomor_spk'])): ?>
                                    <span class="badge bg-info"><?= esc($mutasi['nomor_spk']) ?></span>
                                    <?php if (!empty($mutasi['judul_pekerjaan'])): ?>
                                    <small class="d-block text-muted mt-1 text-end">
                                        <?= esc(substr($mutasi['judul_pekerjaan'], 0, 25)) ?>
                                        <?= strlen($mutasi['judul_pekerjaan']) > 25 ? '...' : '' ?>
                                    </small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jumlah</td>
                            <td class="text-end fw-bold <?= $mutasi['tipe'] == 'Kredit' ? 'text-success' : 'text-danger' ?>">
                                Rp <?= number_format($mutasi['jumlah'], 0, ',', '.') ?>
                            </td>
                        </tr>
                    </table>

                    <hr>

                    <div class="d-grid gap-2">
                        <?php if ($mutasi['status'] == 'Draft'): ?>
                            <button type="button" class="btn btn-danger" onclick="batalkanMutasi(<?= $mutasi['id'] ?>)">
                                <i class="fas fa-times me-1"></i> Batalkan Transaksi
                            </button>
                        <?php elseif ($mutasi['status'] == 'Posted'): ?>
                            <button type="button" class="btn btn-secondary" onclick="batalkanMutasi(<?= $mutasi['id'] ?>)">
                                <i class="fas fa-times-circle me-1"></i> Void Jurnal
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Posting - PERUBAHAN: Tampilan lebih user-friendly -->
<div class="modal fade" id="postModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle text-success me-2"></i> Posting Mutasi Bank
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin memposting transaksi <strong><?= $mutasi['kode_transaksi'] ?></strong>?</p>
                <p class="text-muted small">Data akan diposting ke jurnal dan tidak dapat diedit lagi.</p>
                
                <!-- Informasi Akun Bank -->
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Akun Bank akan ditentukan otomatis</strong>
                    <p class="mb-0 small mt-2">
                        <?php if ($mutasi['tipe'] == 'Debit'): ?>
                            Berdasarkan bank asal: <strong><?= esc($mutasi['bank_asal']) ?></strong>
                        <?php else: ?>
                            Berdasarkan bank tujuan: <strong><?= esc($mutasi['bank_tujuan']) ?></strong>
                        <?php endif; ?>
                    </p>
                </div>
                
                <!-- Informasi akun lawan -->
                <div class="alert alert-light small mt-2">
                    <i class="fas fa-book me-1"></i>
                    <strong>Akun lawan:</strong> 
                    <?php if ($mutasi['tipe'] == 'Debit'): ?>
                        <?= !empty($mutasi['kode_akun_debit']) ? esc($mutasi['kode_akun_debit']) : '' ?> - <?= !empty($mutasi['nama_akun_debit']) ? esc($mutasi['nama_akun_debit']) : '-' ?>
                    <?php else: ?>
                        <?= !empty($mutasi['kode_akun_kredit']) ? esc($mutasi['kode_akun_kredit']) : '' ?> - <?= !empty($mutasi['nama_akun_kredit']) ? esc($mutasi['nama_akun_kredit']) : '-' ?>
                    <?php endif; ?>
                </div>
                
                <!-- Hidden input untuk menyimpan selectedBankId jika diperlukan -->
                <?php if (!empty($selectedBankId)): ?>
                <input type="hidden" id="coa_bank_id" value="<?= $selectedBankId ?>">
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-success" id="confirmPost">
                    <i class="fas fa-check me-1"></i> Ya, Posting
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Batalkan -->
<div class="modal fade" id="batalkanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i> Batalkan Mutasi Bank
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin membatalkan transaksi <strong><?= $mutasi['kode_transaksi'] ?></strong>?</p>
                
                <?php if ($mutasi['status'] == 'Posted'): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Perhatian!</strong> Jurnal terkait akan di-void dan tidak dapat dikembalikan.
                </div>
                <?php else: ?>
                <p class="text-danger small">Data yang dibatalkan tidak dapat dikembalikan!</p>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-danger" id="confirmBatalkan">
                    <i class="fas fa-check me-1"></i> Ya, Batalkan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function postMutasi(id) {
    const modal = new bootstrap.Modal(document.getElementById('postModal'));
    modal.show();
    
    document.getElementById('confirmPost').onclick = function() {
        // Tampilkan loading
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        this.disabled = true;
        
        fetch('<?= site_url("accounting/kas-bank/mutasi-bank/post/") ?>' + id, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: '<?= csrf_token() ?>=<?= csrf_hash() ?>'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                alert('Error: ' + data.message);
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memposting: ' + error.message);
            location.reload();
        });
    };
}

function batalkanMutasi(id) {
    const modal = new bootstrap.Modal(document.getElementById('batalkanModal'));
    modal.show();
    
    document.getElementById('confirmBatalkan').onclick = function() {
        // Tampilkan loading
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        this.disabled = true;
        
        fetch('<?= site_url("accounting/kas-bank/mutasi-bank/batalkan/") ?>' + id, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    location.reload();
                }
            } else {
                alert('Error: ' + data.message);
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat membatalkan: ' + error.message);
            location.reload();
        });
    };
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

.table-borderless td {
    border: none;
    padding: 0.5rem 0;
}

.info-box {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    height: 100%;
    border-left: 4px solid #4dabf7;
}

.info-box label {
    font-size: 0.85rem;
    margin-bottom: 5px;
    color: #6c757d;
}

.bg-light {
    background-color: #f8f9fa !important;
}

/* Status badges */
.badge {
    padding: 0.5rem 1rem;
    font-weight: 500;
    font-size: 0.85rem;
}

/* Button styling */
.btn {
    padding: 0.5rem 1.2rem;
    border-radius: 8px;
    font-weight: 500;
}

.btn-sm {
    padding: 0.25rem 1rem;
}

/* Modal styling */
.modal-content {
    border-radius: 10px;
    border: none;
}

.modal-header {
    border-bottom: 1px solid #e0e0e0;
    background-color: #f8f9fa;
    border-radius: 10px 10px 0 0;
}

.modal-footer {
    border-top: 1px solid #e0e0e0;
}

/* Alert styling */
.alert-info {
    background-color: #e7f3ff;
    border: none;
    border-left: 4px solid #4dabf7;
    border-radius: 8px;
}

.alert-warning {
    background-color: #fff3cd;
    border: none;
    border-left: 4px solid #ffc107;
    border-radius: 8px;
}

.alert-light {
    background-color: #f8f9fa !important;
    border: 1px solid #e9ecef;
}
</style>

<?= $this->include('accounting/templates/footer') ?>