<?= $this->include('accounting/templates/header') ?>
<?= $this->include('accounting/templates/sidebar') ?>
<?= $this->include('accounting/templates/navbar') ?>

<?php
// Fungsi helper inline untuk keamanan
function safeString($value) {
    if (is_array($value) || is_object($value)) {
        return '';
    }
    return (string) $value;
}

function safeDate($date) {
    if (empty($date) || $date == '0000-00-00') {
        return '-';
    }
    try {
        return date('d/m/Y', strtotime($date));
    } catch (Exception $e) {
        return '-';
    }
}

function safeDateTime($datetime) {
    if (empty($datetime) || $datetime == '0000-00-00 00:00:00') {
        return '-';
    }
    try {
        return date('d/m/Y H:i', strtotime($datetime));
    } catch (Exception $e) {
        return '-';
    }
}

function formatRupiah($number) {
    $number = floatval($number);
    return number_format($number, 0, ',', '.');
}
?>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="page-title mb-1">
                        <i class="fas fa-coins me-2"></i> Detail Transaksi Kas Kecil
                    </h2>
                    <p class="page-subtitle text-muted mb-0">
                        Informasi lengkap transaksi <?= safeString($transaksi['kode_transaksi'] ?? '') ?>
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

    <!-- Status Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Status Transaksi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php 
                                $status = safeString($transaksi['status'] ?? '');
                                if ($status == 'Posted'): ?>
                                    <span class="badge bg-success">Posted</span>
                                <?php elseif ($status == 'Draft'): ?>
                                    <span class="badge bg-warning">Draft</span>
                                <?php elseif ($status == 'Dibatalkan'): ?>
                                    <span class="badge bg-danger">Dibatalkan</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tag fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Tipe Transaksi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php 
                                $tipe = safeString($transaksi['tipe'] ?? '');
                                if ($tipe == 'Pemasukan'): ?>
                                    <span class="badge bg-success">PEMASUKAN</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">PENGELUARAN</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Jumlah
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp <?= formatRupiah($transaksi['jumlah'] ?? 0) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Saldo Setelah
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp <?= formatRupiah($transaksi['saldo_setelah'] ?? 0) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Left Column - Detail Information -->
        <div class="col-md-8">
            <!-- Informasi Umum -->
            <div class="modern-card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i> Informasi Umum
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="40%">Kode Transaksi</th>
                                    <td width="10%">:</td>
                                    <td><strong><?= safeString($transaksi['kode_transaksi'] ?? '-') ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Tanggal</th>
                                    <td>:</td>
                                    <td><?= safeDate($transaksi['tanggal'] ?? '') ?></td>
                                </tr>
                                <tr>
                                    <th>Tipe</th>
                                    <td>:</td>
                                    <td>
                                        <?php if (($transaksi['tipe'] ?? '') == 'Pemasukan'): ?>
                                            <span class="badge bg-success">Pemasukan</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Pengeluaran</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>No. Bukti</th>
                                    <td>:</td>
                                    <td><?= safeString($transaksi['no_bukti'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <th>Metode</th>
                                    <td>:</td>
                                    <td>
                                        <?php if (($transaksi['metode_imprest'] ?? 1) == 1): ?>
                                            <span class="badge bg-info">Imprest (Dana Tetap)</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Fluktuasi</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6 mb-3">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="40%">Jumlah</th>
                                    <td width="10%">:</td>
                                    <td class="fw-bold <?= ($transaksi['tipe'] ?? '') == 'Pemasukan' ? 'text-success' : 'text-danger' ?>">
                                        Rp <?= formatRupiah($transaksi['jumlah'] ?? 0) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Saldo Setelah</th>
                                    <td>:</td>
                                    <td class="fw-bold text-primary">Rp <?= formatRupiah($transaksi['saldo_setelah'] ?? 0) ?></td>
                                </tr>
                                <tr>
                                    <th>Terbilang</th>
                                    <td>:</td>
                                    <td><em><?= safeString($transaksi['terbilang'] ?? '-') ?></em></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Keterangan -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Keterangan:</h6>
                                    <p class="card-text"><?= nl2br(safeString($transaksi['keterangan'] ?? '-')) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Akuntansi -->
            <div class="modern-card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-book me-2"></i> Informasi Akuntansi
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Akun Lawan:</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="30%">Kode Akun</th>
                                    <td width="5%">:</td>
                                    <td><?= safeString($transaksi['kode_akun_lawan'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <th>Nama Akun</th>
                                    <td>:</td>
                                    <td><?= safeString($transaksi['nama_akun_lawan'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <th>Tipe Akun</th>
                                    <td>:</td>
                                    <td><?= safeString($transaksi['tipe_akun_lawan'] ?? '-') ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-success">Jurnal:</h6>
                            <?php if (!empty($transaksi['jurnal_id'])): ?>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="30%">No. Jurnal</th>
                                        <td width="5%">:</td>
                                        <td><?= safeString($transaksi['nomor_jurnal'] ?? '-') ?></td>
                                    </tr>
                                    <tr>
                                        <th>Status Jurnal</th>
                                        <td>:</td>
                                        <td>
                                            <?php if (($transaksi['jurnal_status'] ?? '') == 'posted'): ?>
                                                <span class="badge bg-success">Posted</span>
                                            <?php elseif (($transaksi['jurnal_status'] ?? '') == 'draft'): ?>
                                                <span class="badge bg-warning">Draft</span>
                                            <?php elseif (($transaksi['jurnal_status'] ?? '') == 'void'): ?>
                                                <span class="badge bg-danger">Void</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </table>
                            <?php else: ?>
                                <p class="text-muted">Belum ada jurnal</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if (!empty($transaksi['karyawan'])): ?>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="text-info">Karyawan Terkait:</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="15%">NIK</th>
                                    <td width="2%">:</td>
                                    <td><?= safeString($transaksi['karyawan']['nik'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <th>Nama</th>
                                    <td>:</td>
                                    <td><?= safeString($transaksi['karyawan']['nama_lengkap'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <th>Jabatan</th>
                                    <td>:</td>
                                    <td><?= safeString($transaksi['karyawan']['jabatan'] ?? '-') ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($transaksi['spk'])): ?>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="text-info">Proyek/SPK Terkait:</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="15%">Nomor SPK</th>
                                    <td width="2%">:</td>
                                    <td><?= safeString($transaksi['spk']['nomor_spk'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <th>Judul Pekerjaan</th>
                                    <td>:</td>
                                    <td><?= safeString($transaksi['spk']['judul_pekerjaan'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <th>Lokasi</th>
                                    <td>:</td>
                                    <td><?= safeString($transaksi['spk']['lokasi'] ?? '-') ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column - Actions & Metadata -->
        <div class="col-md-4">
            <!-- Tombol Aksi -->
            <div class="modern-card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-cog me-2"></i> Aksi
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?php if (($transaksi['status'] ?? '') == 'Draft'): ?>
                            <a href="<?= site_url('accounting/kas-bank/kas-kecil/edit/' . ($transaksi['id'] ?? 0)) ?>" 
                               class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i> Edit Data
                            </a>
                            <button type="button" class="btn btn-success" id="btnPosting">
                                <i class="fas fa-check-double me-1"></i> Posting ke Jurnal
                            </button>
                            <button type="button" class="btn btn-danger" id="btnHapus">
                                <i class="fas fa-trash me-1"></i> Hapus Data
                            </button>
                            
                        <?php elseif (($transaksi['status'] ?? '') == 'Posted'): ?>
                            <button type="button" class="btn btn-secondary" id="btnBatalkan">
                                <i class="fas fa-times-circle me-1"></i> Batalkan Transaksi
                            </button>
                            <a href="<?= site_url('accounting/kas-bank/kas-kecil/print/' . ($transaksi['id'] ?? 0)) ?>" 
                               class="btn btn-info" target="_blank">
                                <i class="fas fa-print me-1"></i> Cetak / Print
                            </a>
                        <?php endif; ?>
                        
                        <a href="<?= site_url('accounting/kas-bank/kas-kecil') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Informasi Jurnal -->
            <?php if (!empty($transaksi['jurnal_id'])): ?>
            <div class="modern-card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-journal-whills me-2"></i> Informasi Jurnal
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="40%">Nomor Jurnal</th>
                            <td width="5%">:</td>
                            <td><?= safeString($transaksi['nomor_jurnal'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal Jurnal</th>
                            <td>:</td>
                            <td><?= safeDateTime($transaksi['jurnal_created_at'] ?? '') ?></td>
                        </tr>
                        <tr>
                            <th>Status Jurnal</th>
                            <td>:</td>
                            <td>
                                <?php if (($transaksi['jurnal_status'] ?? '') == 'posted'): ?>
                                    <span class="badge bg-success">Posted</span>
                                <?php elseif (($transaksi['jurnal_status'] ?? '') == 'draft'): ?>
                                    <span class="badge bg-warning">Draft</span>
                                <?php elseif (($transaksi['jurnal_status'] ?? '') == 'void'): ?>
                                    <span class="badge bg-danger">Void</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- Metadata -->
            <div class="modern-card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i> Metadata
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="40%">Dibuat Oleh</th>
                            <td width="5%">:</td>
                            <td><?= safeString($transaksi['creator_name'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>Dibuat Tanggal</th>
                            <td>:</td>
                            <td><?= safeDateTime($transaksi['created_at'] ?? '') ?></td>
                        </tr>
                        <?php if (!empty($transaksi['updated_at']) && $transaksi['updated_at'] != $transaksi['created_at']): ?>
                        <tr>
                            <th>Diupdate Oleh</th>
                            <td>:</td>
                            <td><?= safeString($transaksi['updater_name'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>Diupdate Tanggal</th>
                            <td>:</td>
                            <td><?= safeDateTime($transaksi['updated_at'] ?? '') ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if (!empty($transaksi['posted_at'])): ?>
                        <tr>
                            <th>Diposting Tanggal</th>
                            <td>:</td>
                            <td><?= safeDateTime($transaksi['posted_at'] ?? '') ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <!-- Lampiran -->
            <?php if (!empty($transaksi['lampiran'])): ?>
            <div class="modern-card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-paperclip me-2"></i> Lampiran
                    </h5>
                </div>
                <div class="card-body">
                    <?php 
                    $lampiran = safeString($transaksi['lampiran'] ?? '');
                    if (!empty($lampiran)):
                        $fileExt = pathinfo($lampiran, PATHINFO_EXTENSION);
                        $isImage = in_array(strtolower($fileExt), ['jpg', 'jpeg', 'png', 'gif']);
                    ?>
                        
                        <?php if ($isImage): ?>
                            <a href="<?= base_url($lampiran) ?>" target="_blank">
                                <img src="<?= base_url($lampiran) ?>" class="img-fluid rounded" alt="Lampiran">
                            </a>
                            <p class="text-center mt-2">
                                <a href="<?= base_url($lampiran) ?>" target="_blank" class="btn btn-sm btn-primary">
                                    <i class="fas fa-download me-1"></i> Download
                                </a>
                            </p>
                        <?php else: ?>
                            <div class="text-center">
                                <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                                <p>File: <?= basename($lampiran) ?></p>
                                <a href="<?= base_url($lampiran) ?>" target="_blank" class="btn btn-sm btn-primary">
                                    <i class="fas fa-download me-1"></i> Download
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Posting -->
<div class="modal fade" id="postingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Posting ke Jurnal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin akan memposting transaksi kas kecil ini ke jurnal?</p>
                <p class="text-muted small">Setelah diposting, transaksi tidak dapat diedit lagi.</p>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Jurnal yang akan dibuat:</strong>
                    <p class="mb-0 small mt-2">
                        <?php if (($transaksi['tipe'] ?? '') == 'Pemasukan'): ?>
                            Debit: Kas Kecil (1-1101)<br>
                            Kredit: <?= safeString($transaksi['nama_akun_lawan'] ?? '-') ?>
                        <?php else: ?>
                            Debit: <?= safeString($transaksi['nama_akun_lawan'] ?? '-') ?><br>
                            Kredit: Kas Kecil (1-1101)
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="confirmPosting">Ya, Posting</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="hapusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin akan menghapus transaksi kas kecil ini?</p>
                <p class="text-danger small">Data yang dihapus tidak dapat dikembalikan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmHapus">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Batalkan -->
<div class="modal fade" id="batalkanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Batalkan Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin akan membatalkan transaksi kas kecil ini?</p>
                <p class="text-warning small">Jurnal terkait akan di-void.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                <button type="button" class="btn btn-warning" id="confirmBatalkan">Ya, Batalkan</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const transaksiId = <?= $transaksi['id'] ?? 0 ?>;
    
    // Posting button
    document.getElementById('btnPosting')?.addEventListener('click', function() {
        var postingModal = new bootstrap.Modal(document.getElementById('postingModal'));
        postingModal.show();
    });
    
    // Confirm posting
    document.getElementById('confirmPosting')?.addEventListener('click', function() {
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        this.disabled = true;
        
        fetch('<?= site_url('accounting/kas-bank/kas-kecil/post') ?>/' + transaksiId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: '<?= csrf_token() ?>=<?= csrf_hash() ?>'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('postingModal')).hide();
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    location.reload();
                }
            } else {
                alert('Error: ' + data.message);
                this.innerHTML = originalText;
                this.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memposting');
            this.innerHTML = originalText;
            this.disabled = false;
        });
    });
    
    // Hapus button
    document.getElementById('btnHapus')?.addEventListener('click', function() {
        var hapusModal = new bootstrap.Modal(document.getElementById('hapusModal'));
        hapusModal.show();
    });
    
    // Confirm hapus
    document.getElementById('confirmHapus')?.addEventListener('click', function() {
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        this.disabled = true;
        
        fetch('<?= site_url('accounting/kas-bank/kas-kecil/delete') ?>/' + transaksiId, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('hapusModal')).hide();
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    window.location.href = '<?= site_url('accounting/kas-bank/kas-kecil') ?>';
                }
            } else {
                alert('Error: ' + (data.message || 'Gagal menghapus data'));
                this.innerHTML = originalText;
                this.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus: ' + error.message);
            this.innerHTML = originalText;
            this.disabled = false;
        });
    });
    
    // Batalkan button
    document.getElementById('btnBatalkan')?.addEventListener('click', function() {
        var batalkanModal = new bootstrap.Modal(document.getElementById('batalkanModal'));
        batalkanModal.show();
    });
    
    // Confirm batalkan
    document.getElementById('confirmBatalkan')?.addEventListener('click', function() {
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        this.disabled = true;
        
        fetch('<?= site_url('accounting/kas-bank/kas-kecil/batalkan') ?>/' + transaksiId, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('batalkanModal')).hide();
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    location.reload();
                }
            } else {
                alert('Error: ' + data.message);
                this.innerHTML = originalText;
                this.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat membatalkan');
            this.innerHTML = originalText;
            this.disabled = false;
        });
    });
});
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
.border-left-success {
    border-left: 4px solid #1cc88a !important;
}
.border-left-info {
    border-left: 4px solid #36b9cc !important;
}
.border-left-warning {
    border-left: 4px solid #f6c23e !important;
}

.table-borderless td, .table-borderless th {
    border: none;
    padding: 0.3rem;
}

.card.bg-light {
    background-color: #f8f9fc !important;
}

.badge {
    padding: 0.4rem 0.6rem;
    font-weight: 500;
}

.btn {
    border-radius: 8px;
    padding: 0.6rem 1rem;
}
</style>

<?= $this->include('accounting/templates/footer') ?>