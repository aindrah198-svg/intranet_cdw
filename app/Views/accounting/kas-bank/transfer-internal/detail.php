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
                        <i class="fas fa-exchange-alt text-primary me-2"></i> Detail Transfer Internal
                    </h2>
                    <p class="page-subtitle text-muted mb-0">
                        Informasi lengkap transfer <?= $transfer['kode_transfer'] ?>
                    </p>
                </div>
                <div>
                    <a href="<?= site_url('accounting/kas-bank/transfer-internal') ?>" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                    <?php if ($transfer['status'] == 'Draft'): ?>
                        <a href="<?= site_url('accounting/kas-bank/transfer-internal/edit/' . $transfer['id']) ?>" class="btn btn-warning me-2">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <button type="button" class="btn btn-success" onclick="postTransfer(<?= $transfer['id'] ?>)">
                            <i class="fas fa-check-double me-1"></i> Posting
                        </button>
                    <?php endif; ?>
                    <?php if ($transfer['status'] == 'Posted'): ?>
                        <a href="<?= site_url('accounting/kas-bank/transfer-internal/print/' . $transfer['id']) ?>" class="btn btn-info" target="_blank">
                            <i class="fas fa-print me-1"></i> Print
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Bar -->
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
                            ][$transfer['status']] ?? 'bg-secondary';
                            ?>
                            <span class="badge <?= $statusClass ?> fs-6 p-2"><?= $transfer['status'] ?></span>
                        </div>
                        <div class="me-4 mb-2">
                            <span class="text-muted">Tipe Transfer:</span>
                            <span class="badge bg-primary fs-6 p-2">
                                <i class="fas fa-exchange-alt me-1"></i> Internal
                            </span>
                        </div>
                        <?php if (!empty($transfer['jurnal_id'])): ?>
                        <div class="mb-2">
                            <span class="text-muted">Jurnal:</span>
                            <a href="<?= site_url('accounting/jurnal/detail/' . $transfer['jurnal_id']) ?>" class="text-primary fw-bold">
                                <?= $transfer['nomor_jurnal'] ?? 'Lihat Jurnal' ?>
                                <?php if (isset($transfer['jurnal_status']) && $transfer['jurnal_status'] == 'posted'): ?>
                                    <span class="badge bg-success ms-1">Posted</span>
                                <?php endif; ?>
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
                        <i class="fas fa-info-circle me-2"></i> Informasi Transfer
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="40%" class="text-muted">Kode Transfer</td>
                                    <td width="10%">:</td>
                                    <td class="fw-bold"><?= esc($transfer['kode_transfer']) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tanggal Transfer</td>
                                    <td>:</td>
                                    <td><?= date('d F Y', strtotime($transfer['tanggal'])) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Nomor Referensi</td>
                                    <td>:</td>
                                    <td><?= !empty($transfer['no_referensi']) ? esc($transfer['no_referensi']) : '-' ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Jumlah Transfer</td>
                                    <td>:</td>
                                    <td>
                                        <span class="fw-bold text-primary" style="font-size: 1.2rem;">
                                            Rp <?= number_format($transfer['jumlah'], 0, ',', '.') ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Terbilang</td>
                                    <td>:</td>
                                  <td><em class="text-muted"><?= $transfer['terbilang'] ?? 'Rp ' . number_format($transfer['jumlah'], 0, ',', '.') ?></em></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6 mb-3">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="40%" class="text-muted">Dibuat Oleh</td>
                                    <td width="10%">:</td>
                                    <td><?= !empty($transfer['creator_fullname']) ? esc($transfer['creator_fullname']) : (!empty($transfer['creator_name']) ? esc($transfer['creator_name']) : '-') ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Dibuat Tanggal</td>
                                    <td>:</td>
                                    <td><?= date('d F Y H:i', strtotime($transfer['created_at'])) ?></td>
                                </tr>
                                <?php if (!empty($transfer['posted_at'])): ?>
                                <tr>
                                    <td class="text-muted">Diposting Tanggal</td>
                                    <td>:</td>
                                    <td><?= date('d F Y H:i', strtotime($transfer['posted_at'])) ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if (!empty($transfer['posted_by_name'])): ?>
                                <tr>
                                    <td class="text-muted">Diposting Oleh</td>
                                    <td>:</td>
                                    <td><?= esc($transfer['posted_by_name']) ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-12">
                            <h6 class="text-muted mb-2">Keterangan:</h6>
                            <div class="p-3 bg-light rounded">
                                <?php 
                                $keterangan = $transfer['keterangan'] ?? '';
                                
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

            <!-- Informasi Akun Sumber dan Tujuan -->
            <div class="modern-card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-exchange-alt me-2"></i> Detail Perpindahan Dana
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box sumber-box">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-circle bg-danger bg-opacity-10 me-3">
                                        <i class="fas fa-arrow-right text-danger"></i>
                                    </div>
                                    <h6 class="mb-0 fw-bold">Akun Sumber</h6>
                                </div>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%" class="text-muted">Kode Akun</td>
                                        <td width="10%">:</td>
                                        <td class="fw-bold"><?= esc($transfer['kode_akun_sumber'] ?? '-') ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Nama Akun</td>
                                        <td>:</td>
                                        <td><?= esc($transfer['nama_akun_sumber'] ?? '-') ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Tipe Akun</td>
                                        <td>:</td>
                                        <td>
                                            <span class="badge bg-info"><?= esc($transfer['tipe_akun_sumber'] ?? '-') ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Saldo Normal</td>
                                        <td>:</td>
                                        <td>
                                            <span class="badge bg-secondary"><?= esc($transfer['saldo_normal_sumber'] ?? '-') ?></span>
                                        </td>
                                    </tr>
                                    <?php if (!empty($transfer['bank_asal'])): ?>
                                    <tr>
                                        <td class="text-muted">Bank Asal</td>
                                        <td>:</td>
                                        <td><?= esc($transfer['bank_asal']) ?></td>
                                    </tr>
                                    <?php endif; ?>
                                </table>
                                <div class="mt-2 text-center">
                                    <span class="badge bg-danger bg-opacity-10 text-danger p-2">
                                        <i class="fas fa-minus-circle me-1"></i> Mengurangi Saldo
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-box tujuan-box">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-circle bg-success bg-opacity-10 me-3">
                                        <i class="fas fa-arrow-left text-success"></i>
                                    </div>
                                    <h6 class="mb-0 fw-bold">Akun Tujuan</h6>
                                </div>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%" class="text-muted">Kode Akun</td>
                                        <td width="10%">:</td>
                                        <td class="fw-bold"><?= esc($transfer['kode_akun_tujuan'] ?? '-') ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Nama Akun</td>
                                        <td>:</td>
                                        <td><?= esc($transfer['nama_akun_tujuan'] ?? '-') ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Tipe Akun</td>
                                        <td>:</td>
                                        <td>
                                            <span class="badge bg-info"><?= esc($transfer['tipe_akun_tujuan'] ?? '-') ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Saldo Normal</td>
                                        <td>:</td>
                                        <td>
                                            <span class="badge bg-secondary"><?= esc($transfer['saldo_normal_tujuan'] ?? '-') ?></span>
                                        </td>
                                    </tr>
                                    <?php if (!empty($transfer['bank_tujuan'])): ?>
                                    <tr>
                                        <td class="text-muted">Bank Tujuan</td>
                                        <td>:</td>
                                        <td><?= esc($transfer['bank_tujuan']) ?></td>
                                    </tr>
                                    <?php endif; ?>
                                </table>
                                <div class="mt-2 text-center">
                                    <span class="badge bg-success bg-opacity-10 text-success p-2">
                                        <i class="fas fa-plus-circle me-1"></i> Menambah Saldo
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Jurnal yang dibuat -->
                    <?php if (!empty($transfer['jurnal_id'])): ?>
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-success">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-journal-whills fa-2x"></i>
                                    </div>
                                    <div>
                                        <strong>Jurnal yang dibuat:</strong><br>
                                        Debit: <?= esc($transfer['kode_akun_tujuan'] ?? '') ?> - <?= esc($transfer['nama_akun_tujuan'] ?? '') ?> (Rp <?= number_format($transfer['jumlah'], 0, ',', '.') ?>)<br>
                                        Kredit: <?= esc($transfer['kode_akun_sumber'] ?? '') ?> - <?= esc($transfer['nama_akun_sumber'] ?? '') ?> (Rp <?= number_format($transfer['jumlah'], 0, ',', '.') ?>)
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Side Panel -->
        <div class="col-md-4">
            <!-- Lampiran -->
            <div class="modern-card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-paperclip me-2"></i> Lampiran Bukti Transfer
                    </h5>
                </div>
                <div class="card-body text-center">
                    <?php if (!empty($transfer['lampiran']) && file_exists(FCPATH . $transfer['lampiran'])): ?>
                        <?php 
                        $ext = pathinfo($transfer['lampiran'], PATHINFO_EXTENSION);
                        $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif']);
                        ?>
                        
                        <?php if ($isImage): ?>
                            <img src="<?= base_url($transfer['lampiran']) ?>" class="img-fluid rounded mb-3" style="max-height: 200px;" alt="Lampiran">
                            <div>
                                <a href="<?= base_url($transfer['lampiran']) ?>" target="_blank" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye me-1"></i> Lihat Full
                                </a>
                            </div>
                        <?php else: ?>
                            <i class="fas fa-file-pdf fa-4x text-danger mb-3"></i>
                            <p class="mb-2">File <?= strtoupper($ext) ?></p>
                            <a href="<?= base_url($transfer['lampiran']) ?>" target="_blank" class="btn btn-sm btn-primary">
                                <i class="fas fa-download me-1"></i> Download
                            </a>
                        <?php endif; ?>
                        
                    <?php else: ?>
                        <i class="fas fa-paperclip fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Tidak ada lampiran</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Info Ringkas -->
            <div class="modern-card">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i> Ringkasan
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted">Status</td>
                            <td class="text-end">
                                <?php
                                $statusClass = [
                                    'Draft' => 'bg-secondary',
                                    'Posted' => 'bg-success',
                                    'Dibatalkan' => 'bg-danger'
                                ][$transfer['status']] ?? 'bg-secondary';
                                ?>
                                <span class="badge <?= $statusClass ?>"><?= $transfer['status'] ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jumlah Transfer</td>
                            <td class="text-end fw-bold text-primary">
                                Rp <?= number_format($transfer['jumlah'], 0, ',', '.') ?>
                            </td>
                        </tr>
                        <?php if (!empty($transfer['jurnal_id'])): ?>
                        <tr>
                            <td class="text-muted">No. Jurnal</td>
                            <td class="text-end">
                                <a href="<?= site_url('accounting/jurnal/detail/' . $transfer['jurnal_id']) ?>" class="text-primary">
                                    <?= $transfer['nomor_jurnal'] ?? '#' . $transfer['jurnal_id'] ?>
                                </a>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php if (!empty($transfer['bank_asal'])): ?>
                        <tr>
                            <td class="text-muted">Bank Asal</td>
                            <td class="text-end"><?= esc($transfer['bank_asal']) ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if (!empty($transfer['bank_tujuan'])): ?>
                        <tr>
                            <td class="text-muted">Bank Tujuan</td>
                            <td class="text-end"><?= esc($transfer['bank_tujuan']) ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if (!empty($transfer['no_referensi'])): ?>
                        <tr>
                            <td class="text-muted">No. Referensi</td>
                            <td class="text-end"><?= esc($transfer['no_referensi']) ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>

                    <hr>

                    <div class="d-grid gap-2">
                        <?php if ($transfer['status'] == 'Draft'): ?>
                            <button type="button" class="btn btn-danger" onclick="batalkanTransfer(<?= $transfer['id'] ?>)">
                                <i class="fas fa-times-circle me-1"></i> Batalkan Transfer
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Informasi Tambahan -->
            <div class="modern-card mt-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i> Informasi
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i>
                            Transfer internal hanya memindahkan dana antar rekening
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i>
                            Tidak mempengaruhi laba/rugi perusahaan
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i>
                            Mengubah komposisi aset di neraca
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i>
                            Pastikan saldo sumber mencukupi sebelum transfer
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Posting -->
<div class="modal fade" id="postModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Posting Transfer Internal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin memposting transfer <strong><?= $transfer['kode_transfer'] ?></strong>?</p>
                <p class="text-muted small">Data akan diposting ke jurnal dan tidak dapat diedit lagi.</p>
                
                <!-- Preview Jurnal -->
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Jurnal yang akan dibuat:</strong>
                    <p class="mb-0 small mt-2">
                        <span class="text-success fw-bold">Debit:</span> <?= esc($transfer['nama_akun_tujuan'] ?? 'Akun Tujuan') ?> (Rp <?= number_format($transfer['jumlah'], 0, ',', '.') ?>)<br>
                        <span class="text-danger fw-bold">Kredit:</span> <?= esc($transfer['nama_akun_sumber'] ?? 'Akun Sumber') ?> (Rp <?= number_format($transfer['jumlah'], 0, ',', '.') ?>)
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="confirmPost">Ya, Posting</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Batalkan -->
<div class="modal fade" id="batalkanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Batalkan Transfer Internal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin membatalkan transfer <strong><?= $transfer['kode_transfer'] ?></strong>?</p>
                <?php if ($transfer['status'] == 'Posted' && !empty($transfer['jurnal_id'])): ?>
                <p class="text-danger small">Jurnal terkait (<?= $transfer['nomor_jurnal'] ?? '#' . $transfer['jurnal_id'] ?>) akan di-void!</p>
                <?php endif; ?>
                <p class="text-danger small">Data yang dibatalkan tidak dapat dikembalikan!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger" id="confirmBatalkan">Ya, Batalkan</button>
            </div>
        </div>
    </div>
</div>

<script>
function postTransfer(id) {
    const modal = new bootstrap.Modal(document.getElementById('postModal'));
    modal.show();
    
    document.getElementById('confirmPost').onclick = function() {
        // Tampilkan loading
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        this.disabled = true;
        
        fetch('<?= site_url("accounting/kas-bank/transfer-internal/post/") ?>' + id, {
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
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    window.location.reload();
                }
            } else {
                alert('Error: ' + data.message);
                this.innerHTML = originalText;
                this.disabled = false;
                bootstrap.Modal.getInstance(document.getElementById('postModal')).hide();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memposting: ' + error.message);
            this.innerHTML = originalText;
            this.disabled = false;
            bootstrap.Modal.getInstance(document.getElementById('postModal')).hide();
        });
    };
}

function batalkanTransfer(id) {
    const modal = new bootstrap.Modal(document.getElementById('batalkanModal'));
    modal.show();
    
    document.getElementById('confirmBatalkan').onclick = function() {
        // Tampilkan loading
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        this.disabled = true;
        
        fetch('<?= site_url("accounting/kas-bank/transfer-internal/batalkan/") ?>' + id, {
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
                    window.location.reload();
                }
            } else {
                alert('Error: ' + data.message);
                this.innerHTML = originalText;
                this.disabled = false;
                bootstrap.Modal.getInstance(document.getElementById('batalkanModal')).hide();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat membatalkan: ' + error.message);
            this.innerHTML = originalText;
            this.disabled = false;
            bootstrap.Modal.getInstance(document.getElementById('batalkanModal')).hide();
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
    padding: 20px;
    height: 100%;
    transition: all 0.3s ease;
}

.info-box.sumber-box {
    border-left: 4px solid #dc3545;
}

.info-box.tujuan-box {
    border-left: 4px solid #28a745;
}

.info-box label {
    font-size: 0.85rem;
    margin-bottom: 5px;
}

.icon-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.icon-circle.bg-danger {
    background-color: rgba(220, 53, 69, 0.1) !important;
}

.icon-circle.bg-success {
    background-color: rgba(40, 167, 69, 0.1) !important;
}

.icon-circle i {
    font-size: 1.2rem;
}

.bg-light {
    background-color: #f8f9fa !important;
}

/* Status badges */
.badge {
    padding: 0.5rem 1rem;
    font-weight: 500;
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

/* Alert styling */
.alert-success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}

/* Animation */
.modal.fade .modal-dialog {
    transition: transform 0.3s ease-out;
}
</style>

<?= $this->include('accounting/templates/footer') ?>