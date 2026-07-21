<?php
// app/Views/accounting/penggajian/proses-pembayaran/detail.php
$data['active'] = 'proses-pembayaran';
$this->extend('accounting/templates/header');
?>

<?php $this->section('content'); ?>
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 text-gradient-accounting">Detail Proses Pembayaran Gaji</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-2">
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/penggajian') ?>">Penggajian</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/penggajian/proses-pembayaran') ?>">Proses Pembayaran</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= site_url('accounting/penggajian/proses-pembayaran') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <a href="<?= site_url('accounting/penggajian/proses-pembayaran/export-excel/' . $proses['id']) ?>" class="btn btn-success">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </a>
            <?php if ($proses['metode_pembayaran'] == 'Transfer Bank' && $proses['status'] == 'Selesai'): ?>
                <a href="<?= site_url('accounting/penggajian/proses-pembayaran/export-bank-transfer/' . $proses['id']) ?>" class="btn btn-primary">
                    <i class="fas fa-download me-1"></i> Export Bank Transfer
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-<?= $proses['status'] == 'Selesai' ? 'success' : ($proses['status'] == 'Diproses' ? 'info' : ($proses['status'] == 'Dibatalkan' ? 'danger' : 'secondary')) ?> d-flex align-items-center">
                <i class="fas fa-<?= $proses['status'] == 'Selesai' ? 'check-circle' : ($proses['status'] == 'Diproses' ? 'spinner fa-pulse' : ($proses['status'] == 'Dibatalkan' ? 'times-circle' : 'clock')) ?> fa-2x me-3"></i>
                <div>
                    <h5 class="mb-1">Status: <?= $proses['status'] ?></h5>
                    <?php if ($proses['status'] == 'Selesai' && $proses['selesai_at']): ?>
                        <p class="mb-0">Selesai oleh: <?= $proses['finisher_name'] ?? '-' ?> pada <?= date('d/m/Y H:i', strtotime($proses['selesai_at'])) ?></p>
                    <?php endif; ?>
                </div>
                <?php if ($proses['status'] == 'Diproses'): ?>
                <div class="ms-auto">
                    <button class="btn btn-success me-2" onclick="completePayment(<?= $proses['id'] ?>)">
                        <i class="fas fa-check-double me-1"></i> Selesaikan Pembayaran
                    </button>
                    <button class="btn btn-danger" onclick="cancelPayment(<?= $proses['id'] ?>)">
                        <i class="fas fa-ban me-1"></i> Batalkan
                    </button>
                </div>
                <?php endif; ?>
                <?php if ($proses['status'] == 'Draft'): ?>
                <div class="ms-auto">
                    <a href="<?= site_url('accounting/penggajian/proses-pembayaran/edit/' . $proses['id']) ?>" class="btn btn-warning me-2">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                    <button class="btn btn-success" onclick="processPayment(<?= $proses['id'] ?>)">
                        <i class="fas fa-play me-1"></i> Proses
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Informasi Proses -->
            <div class="card mb-4">
                <div class="card-header bg-gradient-accounting text-white">
                    <i class="fas fa-info-circle me-2"></i> Informasi Proses Pembayaran
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Nomor Proses</label>
                            <p class="mb-0 fw-bold"><code><?= $proses['nomor_proses'] ?></code></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Nama Proses</label>
                            <p class="mb-0"><?= $proses['nama_proses'] ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Periode</label>
                            <p class="mb-0">
                                <?php
                                $bulanNames = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                echo $bulanNames[$proses['periode_bulan']] . ' ' . $proses['periode_tahun'];
                                ?>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Tanggal Proses</label>
                            <p class="mb-0"><?= date('d/m/Y', strtotime($proses['tanggal_proses'])) ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Tanggal Pembayaran</label>
                            <p class="mb-0"><?= date('d/m/Y', strtotime($proses['tanggal_pembayaran'])) ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Metode Pembayaran</label>
                            <p class="mb-0">
                                <span class="badge bg-<?= $proses['metode_pembayaran'] == 'Transfer Bank' ? 'primary' : ($proses['metode_pembayaran'] == 'Tunai' ? 'success' : 'warning') ?>">
                                    <?= $proses['metode_pembayaran'] ?>
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Bank Pengirim</label>
                            <p class="mb-0"><?= $proses['bank_pengirim'] ?? '-' ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Akun Bank (COA)</label>
                            <p class="mb-0">
                                <?php if ($proses['kode_akun_bank']): ?>
                                    <code><?= $proses['kode_akun_bank'] ?></code> - <?= $proses['nama_akun_bank'] ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="text-muted small">Keterangan</label>
                            <p class="mb-0 bg-light p-2 rounded"><?= nl2br($proses['keterangan'] ?? '-') ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Jurnal & Mutasi Bank -->
            <?php if ($proses['status'] == 'Selesai'): ?>
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-book me-2"></i> Informasi Jurnal & Mutasi Bank
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Nomor Jurnal</label>
                            <p class="mb-0">
                                <?php if ($proses['nomor_jurnal']): ?>
                                    <code><?= $proses['nomor_jurnal'] ?></code>
                                    <a href="<?= site_url('accounting/pembukuan/jurnal-umum/detail/' . $proses['jurnal_id']) ?>" class="btn btn-sm btn-outline-info ms-2" target="_blank">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Status Jurnal</label>
                            <p class="mb-0">
                                <?php if ($proses['jurnal_status']): ?>
                                    <span class="badge bg-<?= $proses['jurnal_status'] == 'posted' ? 'success' : 'secondary' ?>">
                                        <?= ucfirst($proses['jurnal_status']) ?>
                                    </span>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Kode Mutasi Bank</label>
                            <p class="mb-0">
                                <?php if ($proses['kode_mutasi']): ?>
                                    <code><?= $proses['kode_mutasi'] ?></code>
                                    <a href="<?= site_url('accounting/kas-bank/mutasi-bank/detail/' . $proses['mutasi_bank_id']) ?>" class="btn btn-sm btn-outline-info ms-2" target="_blank">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Status Mutasi</label>
                            <p class="mb-0">
                                <?php if ($proses['mutasi_status']): ?>
                                    <span class="badge bg-<?= $proses['mutasi_status'] == 'Posted' ? 'success' : 'secondary' ?>">
                                        <?= $proses['mutasi_status'] ?>
                                    </span>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <!-- Ringkasan Pembayaran -->
            <div class="card mb-4 sticky-top" style="top: 20px;">
                <div class="card-header bg-gradient-accounting text-white">
                    <i class="fas fa-chart-pie me-2"></i> Ringkasan Pembayaran
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="bg-success bg-opacity-10 rounded p-3">
                            <h2 class="text-success mb-0">Rp <?= number_format($proses['total_nominal'], 0, ',', '.') ?></h2>
                            <small class="text-muted">Total Gaji Dibayarkan</small>
                        </div>
                    </div>
                    <hr>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td>Jumlah Karyawan</td>
                            <td class="text-end"><strong><?= $proses['total_karyawan'] ?></strong> orang</td>
                        </tr>
                        <tr>
                            <td>Periode</td>
                            <td class="text-end"><?= $bulanNames[$proses['periode_bulan']] ?> <?= $proses['periode_tahun'] ?></td>
                        </tr>
                        <tr>
                            <td>Metode Pembayaran</td>
                            <td class="text-end"><?= $proses['metode_pembayaran'] ?></td>
                        </tr>
                        <?php if ($proses['status'] == 'Selesai'): ?>
                        <tr>
                            <td>Tanggal Selesai</td>
                            <td class="text-end"><?= date('d/m/Y H:i', strtotime($proses['selesai_at'])) ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                    
                    <hr>
                    
                    <!-- Rekap Per Bank -->
                    <?php if (!empty($ringkasanPerBank)): ?>
                    <label class="form-label fw-bold">Rekap Per Bank</label>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Bank</th>
                                <th class="text-end">Jumlah</th>
                                <th class="text-end">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ringkasanPerBank as $bank): ?>
                            <tr>
                                <td><?= $bank['bank'] ?></td>
                                <td class="text-end"><?= $bank['jumlah_karyawan'] ?> org</td>
                                <td class="text-end">Rp <?= number_format($bank['total_nominal'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Karyawan -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-users me-2"></i> Daftar Karyawan yang Dibayar
            <span class="badge bg-light text-dark ms-2"><?= count($proses['details']) ?> karyawan</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="karyawanTable">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">NIK</th>
                            <th width="20%">Nama Karyawan</th>
                            <th width="10%">Bank</th>
                            <th width="15%">No Rekening</th>
                            <th width="8%">Gaji Pokok</th>
                            <th width="8%">Tunjangan</th>
                            <th width="8%">Upah Lembur</th>
                            <th width="8%">Potongan</th>
                            <th width="8%">Gaji Bersih</th>
                            <th width="8%">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($proses['details'] as $item): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= $item['nomor_karyawan'] ?? '-' ?></td>
                            <td>
                                <strong><?= $item['nama_karyawan'] ?></strong>
                            </td>
                            <td><?= $item['bank'] ?? '-' ?></td>
                            <td><?= $item['no_rekening'] ?? '-' ?></td>
                            <td class="text-end">Rp <?= number_format($item['gaji_pokok'], 0, ',', '.') ?></td>
                            <td class="text-end">Rp <?= number_format($item['total_tunjangan'], 0, ',', '.') ?></td>
                            <td class="text-end">Rp <?= number_format($item['upah_lembur'], 0, ',', '.') ?></td>
                            <td class="text-end text-danger">Rp <?= number_format($item['total_potongan'], 0, ',', '.') ?></td>
                            <td class="text-end text-primary">
                                <strong>Rp <?= number_format($item['gaji_bersih'], 0, ',', '.') ?></strong>
                            </td>
                            <td class="text-center">
                                <?php
                                $statusBadge = match($item['status_pembayaran']) {
                                    'Berhasil' => 'success',
                                    'Gagal' => 'danger',
                                    'Pending' => 'warning',
                                    'Dikembalikan' => 'info',
                                    default => 'secondary'
                                };
                                ?>
                                <span class="badge bg-<?= $statusBadge ?>"><?= $item['status_pembayaran'] ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-active">
                        <tr>
                            <td colspan="5" class="text-end"><strong>Total</strong></td>
                            <td class="text-end"><strong>Rp <?= number_format(array_sum(array_column($proses['details'], 'gaji_pokok')), 0, ',', '.') ?></strong></td>
                            <td class="text-end"><strong>Rp <?= number_format(array_sum(array_column($proses['details'], 'total_tunjangan')), 0, ',', '.') ?></strong></td>
                            <td class="text-end"><strong>Rp <?= number_format(array_sum(array_column($proses['details'], 'upah_lembur')), 0, ',', '.') ?></strong></td>
                            <td class="text-end"><strong>Rp <?= number_format(array_sum(array_column($proses['details'], 'total_potongan')), 0, ',', '.') ?></strong></td>
                            <td class="text-end"><strong>Rp <?= number_format($proses['total_nominal'], 0, ',', '.') ?></strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="mt-3 text-muted small">
                <i class="fas fa-info-circle me-1"></i>
                Data di atas adalah detail pembayaran gaji untuk periode <?= $bulanNames[$proses['periode_bulan']] ?> <?= $proses['periode_tahun'] ?>.
            </div>
        </div>
    </div>
</div>

<script>
function processPayment(id) {
    if (confirm('Proses pembayaran ini? Data akan diproses untuk pembayaran.')) {
        $.ajax({
            url: '<?= site_url('accounting/penggajian/proses-pembayaran/process') ?>/' + id,
            method: 'POST',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('Terjadi kesalahan saat memproses pembayaran');
            }
        });
    }
}

function completePayment(id) {
    if (confirm('Selesaikan pembayaran ini? Aksi ini akan membuat jurnal dan mutasi bank.')) {
        $.ajax({
            url: '<?= site_url('accounting/penggajian/proses-pembayaran/complete') ?>/' + id,
            method: 'POST',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('Terjadi kesalahan saat menyelesaikan pembayaran');
            }
        });
    }
}

function cancelPayment(id) {
    if (confirm('Batalkan proses pembayaran ini? Tindakan ini tidak dapat dibatalkan.')) {
        $.ajax({
            url: '<?= site_url('accounting/penggajian/proses-pembayaran/cancel') ?>/' + id,
            method: 'POST',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('Terjadi kesalahan saat membatalkan pembayaran');
            }
        });
    }
}

// Initialize DataTable
$(document).ready(function() {
    $('#karyawanTable').DataTable({
        "pageLength": 25,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "responsive": true,
        "language": {
            "search": "Cari:",
            "lengthMenu": "Tampilkan _MENU_ data",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Berikutnya",
                "previous": "Sebelumnya"
            }
        }
    });
});
</script>

<?php $this->endSection(); ?>