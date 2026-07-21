<?php
// Fungsi helper inline di awal view
function safeString($value) {
    if (is_array($value)) {
        return '';
    }
    if (is_null($value)) {
        return '';
    }
    if (is_object($value)) {
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

function safeNumber($number) {
    if (empty($number) || !is_numeric($number)) {
        return 0;
    }
    return (float) $number;
}

function formatRupiah($number) {
    $number = safeNumber($number);
    return number_format($number, 0, ',', '.');
}
?>

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
                        <i class="fas fa-user-tie me-2"></i> Detail Pengeluaran Pribadi
                    </h2>
                    <p class="page-subtitle text-muted mb-0">
                        Informasi lengkap pengeluaran pribadi <?= esc(safeString($pengeluaran['kode_pengeluaran'] ?? '')) ?>
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
                                $status = safeString($pengeluaran['status'] ?? '');
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
                                Status Hutang
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php 
                                $statusHutang = safeString($pengeluaran['status_hutang'] ?? 'Belum Dibayar');
                                $hutangClass = match($statusHutang) {
                                    'Lunas' => 'bg-success',
                                    'Sebagian' => 'bg-warning',
                                    'Belum Dibayar' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                                ?>
                                <span class="badge <?= $hutangClass ?>"><?= esc($statusHutang) ?></span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i>
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
                                Rp <?= formatRupiah($pengeluaran['jumlah'] ?? 0) ?>
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
                                Sisa Hutang
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp <?= formatRupiah($pengeluaran['sisa_hutang'] ?? 0) ?>
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
                                    <th width="40%">Kode Pengeluaran</th>
                                    <td width="10%">:</td>
                                    <td><strong><?= esc(safeString($pengeluaran['kode_pengeluaran'] ?? '-')) ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Tanggal</th>
                                    <td>:</td>
                                    <td><?= safeDate($pengeluaran['tanggal'] ?? '') ?></td>
                                </tr>
                                <tr>
                                    <th>Jenis</th>
                                    <td>:</td>
                                    <td>
                                        <?php 
                                        $jenis = safeString($pengeluaran['jenis'] ?? 'Lainnya');
                                        $badgeClass = match($jenis) {
                                            'Kasbon' => 'bg-primary',
                                            'Reimbursement' => 'bg-success',
                                            'Prive' => 'bg-secondary',
                                            'Dana Talangan' => 'bg-info',
                                            'Klaim Pribadi' => 'bg-warning',
                                            default => 'bg-dark'
                                        };
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= esc($jenis) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Karyawan</th>
                                    <td>:</td>
                                    <td>
                                        <strong><?= esc(safeString($pengeluaran['karyawan_nama'] ?? '-')) ?></strong>
                                        <br><small class="text-muted"><?= esc(safeString($pengeluaran['karyawan_nik'] ?? '')) ?> - <?= esc(safeString($pengeluaran['karyawan_jabatan'] ?? '')) ?></small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>No. Bukti</th>
                                    <td>:</td>
                                    <td><?= esc(safeString($pengeluaran['no_bukti'] ?? '-')) ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6 mb-3">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="40%">Jumlah</th>
                                    <td width="10%">:</td>
                                    <td class="fw-bold text-primary">Rp <?= formatRupiah($pengeluaran['jumlah'] ?? 0) ?></td>
                                </tr>
                                <tr>
                                    <th>Sudah Dibayar</th>
                                    <td>:</td>
                                    <td class="text-success">Rp <?= formatRupiah($pengeluaran['jumlah_dibayar'] ?? 0) ?></td>
                                </tr>
                                <tr>
                                    <th>Sisa Hutang</th>
                                    <td>:</td>
                                    <td class="fw-bold <?= ($pengeluaran['sisa_hutang'] ?? 0) > 0 ? 'text-danger' : 'text-success' ?>">
                                        Rp <?= formatRupiah($pengeluaran['sisa_hutang'] ?? 0) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tanggal Jatuh Tempo</th>
                                    <td>:</td>
                                    <td>
                                        <?php 
                                        $jatuhTempo = $pengeluaran['tanggal_jatuh_tempo'] ?? '';
                                        if (!empty($jatuhTempo) && $jatuhTempo != '0000-00-00'): 
                                        ?>
                                            <?= safeDate($jatuhTempo) ?>
                                            <?php 
                                            $today = date('Y-m-d');
                                            if ($jatuhTempo < $today && ($pengeluaran['status_hutang'] ?? '') != 'Lunas'):
                                            ?>
                                                <span class="badge bg-danger ms-2">Jatuh Tempo</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Terbilang</th>
                                    <td>:</td>
                                    <td><em><?= esc(safeString($pengeluaran['terbilang'] ?? '-')) ?></em></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Bagian Keterangan yang diperbaiki -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Keterangan:</h6>
                                    <p class="card-text">
                                        <?php 
                                        $keterangan = $pengeluaran['keterangan'] ?? '';
                                        if (is_array($keterangan)) {
                                            $keterangan = '';
                                        } elseif (is_object($keterangan)) {
                                            $keterangan = '';
                                        }
                                        echo nl2br(esc((string)$keterangan)); 
                                        ?>
                                    </p>
                                    
                                    <?php if (!empty($pengeluaran['tujuan_penggunaan'])): ?>
                                    <h6 class="card-title mt-3">Tujuan Penggunaan:</h6>
                                    <p class="card-text">
                                        <?php 
                                        $tujuan = $pengeluaran['tujuan_penggunaan'] ?? '';
                                        if (is_array($tujuan)) {
                                            $tujuan = '';
                                        } elseif (is_object($tujuan)) {
                                            $tujuan = '';
                                        }
                                        echo nl2br(esc((string)$tujuan)); 
                                        ?>
                                    </p>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($pengeluaran['catatan_internal'])): ?>
                                    <h6 class="card-title mt-3 text-muted">
                                        <i class="fas fa-lock me-1"></i> Catatan Internal:
                                    </h6>
                                    <p class="card-text text-muted">
                                        <?php 
                                        $catatan = $pengeluaran['catatan_internal'] ?? '';
                                        if (is_array($catatan)) {
                                            $catatan = '';
                                        } elseif (is_object($catatan)) {
                                            $catatan = '';
                                        }
                                        echo nl2br(esc((string)$catatan)); 
                                        ?>
                                    </p>
                                    <?php endif; ?>
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
                            <h6 class="text-primary">Akun Debit:</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="30%">Kode Akun</th>
                                    <td width="5%">:</td>
                                    <td><?= esc(safeString($pengeluaran['kode_akun_debit'] ?? '-')) ?></td>
                                </tr>
                                <tr>
                                    <th>Nama Akun</th>
                                    <td>:</td>
                                    <td><?= esc(safeString($pengeluaran['nama_akun_debit'] ?? '-')) ?></td>
                                </tr>
                                <tr>
                                    <th>Tipe Akun</th>
                                    <td>:</td>
                                    <td><?= esc(safeString($pengeluaran['tipe_akun_debit'] ?? '-')) ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-success">Akun Kredit:</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="30%">Kode Akun</th>
                                    <td width="5%">:</td>
                                    <td><?= esc(safeString($pengeluaran['kode_akun_kredit'] ?? '-')) ?></td>
                                </tr>
                                <tr>
                                    <th>Nama Akun</th>
                                    <td>:</td>
                                    <td><?= esc(safeString($pengeluaran['nama_akun_kredit'] ?? '-')) ?></td>
                                </tr>
                                <tr>
                                    <th>Tipe Akun</th>
                                    <td>:</td>
                                    <td><?= esc(safeString($pengeluaran['tipe_akun_kredit'] ?? '-')) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <?php if (!empty($pengeluaran['spk']) && is_array($pengeluaran['spk'])): ?>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="text-info">Proyek/SPK Terkait:</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="15%">Nomor SPK</th>
                                    <td width="2%">:</td>
                                    <td><?= esc(safeString($pengeluaran['spk']['nomor_spk'] ?? '-')) ?></td>
                                </tr>
                                <tr>
                                    <th>Judul Pekerjaan</th>
                                    <td>:</td>
                                    <td><?= esc(safeString($pengeluaran['spk']['judul_pekerjaan'] ?? '-')) ?></td>
                                </tr>
                                <tr>
                                    <th>Lokasi</th>
                                    <td>:</td>
                                    <td><?= esc(safeString($pengeluaran['spk']['lokasi'] ?? '-')) ?></td>
                                </tr>
                                <tr>
                                    <th>Client</th>
                                    <td>:</td>
                                    <td><?= esc(safeString($pengeluaran['spk']['client_nama'] ?? '-')) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Riwayat Pelunasan -->
            <?php if (($pengeluaran['jumlah_dibayar'] ?? 0) > 0): ?>
            <div class="modern-card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i> Riwayat Pelunasan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Metode</th>
                                    <th>Jumlah</th>
                                    <th>Referensi</th>
                                    <th>No. Jurnal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (($pengeluaran['jumlah_dibayar'] ?? 0) == ($pengeluaran['jumlah'] ?? 0)): ?>
                                <tr>
                                    <td><?= safeDate($pengeluaran['tanggal_pelunasan'] ?? '') ?></td>
                                    <td>
                                        <?php if (!empty($pengeluaran['mutasi_bank_id'])): ?>
                                            <span class="badge bg-info">Transfer Bank</span>
                                        <?php elseif (!empty($pengeluaran['kas_kecil_id'])): ?>
                                            <span class="badge bg-warning">Kas Kecil</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Langsung</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">Rp <?= formatRupiah($pengeluaran['jumlah_dibayar'] ?? 0) ?></td>
                                    <td>
                                        <?php if (!empty($pengeluaran['kode_mutasi_bank'])): ?>
                                            <?= esc(safeString($pengeluaran['kode_mutasi_bank'])) ?>
                                        <?php elseif (!empty($pengeluaran['kode_kas_kecil'])): ?>
                                            <?= esc(safeString($pengeluaran['kode_kas_kecil'])) ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($pengeluaran['nomor_jurnal_pelunasan'])): ?>
                                            <?= esc(safeString($pengeluaran['nomor_jurnal_pelunasan'])) ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        Pembayaran sebagian tidak dicatat detail (implementasi dapat dikembangkan)
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
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
                        <?php if (($pengeluaran['status'] ?? '') == 'Draft'): ?>
                            <a href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi/edit/' . ($pengeluaran['id'] ?? 0)) ?>" 
                               class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i> Edit Data
                            </a>
                            <button type="button" class="btn btn-success" id="btnPosting">
                                <i class="fas fa-check-double me-1"></i> Posting ke Jurnal
                            </button>
                            <button type="button" class="btn btn-danger" id="btnHapus">
                                <i class="fas fa-trash me-1"></i> Hapus Data
                            </button>
                            
                        <?php elseif (($pengeluaran['status'] ?? '') == 'Posted'): ?>
                            <?php if (($pengeluaran['status_hutang'] ?? '') != 'Lunas'): ?>
                                <a href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi/proses-pelunasan/' . ($pengeluaran['id'] ?? 0)) ?>" 
                                   class="btn btn-success">
                                    <i class="fas fa-money-bill-wave me-1"></i> Proses Pelunasan
                                </a>
                            <?php endif; ?>
                            
                            <button type="button" class="btn btn-secondary" id="btnBatalkan">
                                <i class="fas fa-times-circle me-1"></i> Batalkan Transaksi
                            </button>
                            
                            <a href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi/print/' . ($pengeluaran['id'] ?? 0)) ?>" 
                               class="btn btn-info" target="_blank">
                                <i class="fas fa-print me-1"></i> Cetak / Print
                            </a>
                        <?php endif; ?>
                        
                        <a href="<?= site_url('accounting/kas-bank/pengeluaran-pribadi') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Informasi Jurnal -->
            <?php if (!empty($pengeluaran['jurnal_id'])): ?>
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
                            <td><?= esc(safeString($pengeluaran['nomor_jurnal'] ?? '-')) ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal Jurnal</th>
                            <td>:</td>
                            <td><?= safeDateTime($pengeluaran['jurnal_created_at'] ?? '') ?></td>
                        </tr>
                        <tr>
                            <th>Keterangan Jurnal</th>
                            <td>:</td>
                            <td><small><?= esc(safeString($pengeluaran['jurnal_keterangan'] ?? '-')) ?></small></td>
                        </tr>
                        <tr>
                            <th>Status Jurnal</th>
                            <td>:</td>
                            <td>
                                <?php 
                                $jurnalStatus = safeString($pengeluaran['jurnal_status'] ?? '');
                                if ($jurnalStatus == 'posted'): ?>
                                    <span class="badge bg-success">Posted</span>
                                <?php elseif ($jurnalStatus == 'draft'): ?>
                                    <span class="badge bg-warning">Draft</span>
                                <?php elseif ($jurnalStatus == 'void'): ?>
                                    <span class="badge bg-danger">Void</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                    
                    <?php if (!empty($pengeluaran['jurnal_pelunasan_id'])): ?>
                    <hr>
                    <h6 class="mt-3">Jurnal Pelunasan:</h6>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="40%">Nomor Jurnal</th>
                            <td width="5%">:</td>
                            <td><?= esc(safeString($pengeluaran['nomor_jurnal_pelunasan'] ?? '-')) ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td>:</td>
                            <td><?= safeDate($pengeluaran['jurnal_pelunasan_created_at'] ?? '') ?></td>
                        </tr>
                    </table>
                    <?php endif; ?>
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
                            <td><?= esc(safeString($pengeluaran['creator_name'] ?? '-')) ?></td>
                        </tr>
                        <tr>
                            <th>Dibuat Tanggal</th>
                            <td>:</td>
                            <td><?= safeDateTime($pengeluaran['created_at'] ?? '') ?></td>
                        </tr>
                        <?php if (!empty($pengeluaran['updated_at']) && $pengeluaran['updated_at'] != $pengeluaran['created_at']): ?>
                        <tr>
                            <th>Diupdate Oleh</th>
                            <td>:</td>
                            <td><?= esc(safeString($pengeluaran['updater_name'] ?? '-')) ?></td>
                        </tr>
                        <tr>
                            <th>Diupdate Tanggal</th>
                            <td>:</td>
                            <td><?= safeDateTime($pengeluaran['updated_at'] ?? '') ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if (!empty($pengeluaran['posted_at'])): ?>
                        <tr>
                            <th>Diposting Tanggal</th>
                            <td>:</td>
                            <td><?= safeDateTime($pengeluaran['posted_at'] ?? '') ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <!-- Lampiran -->
            <?php if (!empty($pengeluaran['lampiran'])): ?>
            <div class="modern-card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-paperclip me-2"></i> Lampiran
                    </h5>
                </div>
                <div class="card-body">
                    <?php 
                    $lampiran = safeString($pengeluaran['lampiran'] ?? '');
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
                <p>Apakah Anda yakin akan memposting pengeluaran pribadi ini ke jurnal?</p>
                <p class="text-muted small">Setelah diposting, transaksi tidak dapat diedit lagi.</p>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Jurnal yang akan dibuat:</strong>
                    <p class="mb-0 small mt-2">
                        Debit: <?= esc(safeString($pengeluaran['nama_akun_debit'] ?? '-')) ?><br>
                        Kredit: <?= esc(safeString($pengeluaran['nama_akun_kredit'] ?? '-')) ?>
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
                <p>Apakah Anda yakin akan menghapus pengeluaran pribadi ini?</p>
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
                <p>Apakah Anda yakin akan membatalkan pengeluaran pribadi ini?</p>
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
    const pengeluaranId = <?= $pengeluaran['id'] ?? 0 ?>;
    
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
        
        fetch('<?= site_url('accounting/kas-bank/pengeluaran-pribadi/post') ?>/' + pengeluaranId, {
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
        
        fetch('<?= site_url('accounting/kas-bank/pengeluaran-pribadi/delete') ?>/' + pengeluaranId, {
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
                    window.location.href = '<?= site_url('accounting/kas-bank/pengeluaran-pribadi') ?>';
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
        
        fetch('<?= site_url('accounting/kas-bank/pengeluaran-pribadi/batalkan') ?>/' + pengeluaranId, {
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