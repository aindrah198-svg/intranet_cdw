<?php
// app/Views/accounting/penggajian/perhitungan-gaji/detail.php
$data['active'] = 'perhitungan-gaji';
$this->extend('accounting/templates/header');
?>

<?php $this->section('content'); ?>
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 text-gradient-accounting">Detail Perhitungan Gaji</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-2">
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/penggajian') ?>">Penggajian</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('accounting/penggajian/perhitungan-gaji') ?>">Perhitungan Gaji</a></li>
                    <li class="breadcrumb-item active">Detail Perhitungan</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= site_url('accounting/penggajian/perhitungan-gaji') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <a href="<?= site_url('accounting/penggajian/slip-gaji/view/' . $perhitungan['id']) ?>" class="btn btn-accounting-outline" target="_blank">
                <i class="fas fa-print me-1"></i> Cetak Slip
            </a>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-<?= $perhitungan['status'] == 'Disetujui' ? 'success' : ($perhitungan['status'] == 'Dihitung' ? 'info' : ($perhitungan['status'] == 'Ditolak' ? 'danger' : 'secondary')) ?> d-flex align-items-center">
                <i class="fas fa-<?= $perhitungan['status'] == 'Disetujui' ? 'check-circle' : ($perhitungan['status'] == 'Dihitung' ? 'calculator' : ($perhitungan['status'] == 'Ditolak' ? 'times-circle' : 'clock')) ?> fa-2x me-3"></i>
                <div>
                    <h5 class="mb-1">Status: <?= $perhitungan['status'] ?></h5>
                    <?php if ($perhitungan['status'] == 'Disetujui' && $perhitungan['disetujui_at']): ?>
                        <p class="mb-0">Disetujui oleh: <?= $perhitungan['approver_name'] ?? '-' ?> pada <?= date('d/m/Y H:i', strtotime($perhitungan['disetujui_at'])) ?></p>
                    <?php elseif ($perhitungan['status'] == 'Ditolak' && $perhitungan['catatan']): ?>
                        <p class="mb-0">Alasan: <?= $perhitungan['catatan'] ?></p>
                    <?php endif; ?>
                </div>
                <?php if ($perhitungan['status'] == 'Dihitung'): ?>
                <div class="ms-auto">
                    <button class="btn btn-success me-2" onclick="approveGaji(<?= $perhitungan['id'] ?>)">
                        <i class="fas fa-check me-1"></i> Setujui
                    </button>
                    <button class="btn btn-danger" onclick="rejectGaji(<?= $perhitungan['id'] ?>)">
                        <i class="fas fa-times me-1"></i> Tolak
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Informasi Perhitungan -->
            <div class="card mb-4">
                <div class="card-header bg-gradient-accounting text-white">
                    <i class="fas fa-file-invoice me-2"></i> Informasi Perhitungan
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Nomor Perhitungan</label>
                            <p class="mb-0 fw-bold"><code><?= $perhitungan['nomor_perhitungan'] ?></code></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Tanggal Perhitungan</label>
                            <p class="mb-0"><?= date('d/m/Y', strtotime($perhitungan['tanggal_perhitungan'])) ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Periode</label>
                            <p class="mb-0">
                                <?php
                                $bulanNames = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                echo $bulanNames[$perhitungan['periode_bulan']] . ' ' . $perhitungan['periode_tahun'];
                                ?>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Dibuat Oleh</label>
                            <p class="mb-0"><?= $perhitungan['creator_name'] ?? '-' ?></p>
                        </div>
                        <?php if ($perhitungan['catatan']): ?>
                        <div class="col-12 mb-3">
                            <label class="text-muted small">Catatan</label>
                            <p class="mb-0 bg-light p-2 rounded"><?= nl2br($perhitungan['catatan']) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Informasi Karyawan -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-user me-2"></i> Informasi Karyawan
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">NIK</label>
                            <p class="mb-0 fw-bold"><?= $perhitungan['nomor_karyawan'] ?? $perhitungan['nik'] ?? '-' ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Nama Lengkap</label>
                            <p class="mb-0 fw-bold"><?= $perhitungan['nama_karyawan'] ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Jabatan</label>
                            <p class="mb-0"><?= $perhitungan['jabatan'] ?? '-' ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Departemen</label>
                            <p class="mb-0"><?= $perhitungan['departemen'] ?? '-' ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Tanggal Masuk</label>
                            <p class="mb-0"><?= $perhitungan['tanggal_masuk'] ?? '-' ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Status Karyawan</label>
                            <p class="mb-0">
                                <span class="badge bg-<?= $perhitungan['status_karyawan'] == 'Tetap' ? 'success' : 'info' ?>">
                                    <?= $perhitungan['status_karyawan'] ?? '-' ?>
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Bank & No Rekening</label>
                            <p class="mb-0"><?= ($perhitungan['bank'] ?? '-') . ' - ' . ($perhitungan['no_rekening'] ?? '-') ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Nama Rekening</label>
                            <p class="mb-0"><?= $perhitungan['nama_rekening'] ?? '-' ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Ringkasan Gaji -->
            <div class="card mb-4 sticky-top" style="top: 20px;">
                <div class="card-header bg-gradient-accounting text-white">
                    <i class="fas fa-chart-pie me-2"></i> Ringkasan Gaji
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h2 class="text-success mb-0">Rp <?= number_format($perhitungan['gaji_bersih'], 0, ',', '.') ?></h2>
                        <small class="text-muted">Gaji Bersih</small>
                    </div>
                    <hr>
                    <table class="table table-sm table-borderless">
                        <tr class="bg-light">
                            <td><strong>Total Pendapatan</strong></td>
                            <td class="text-end">Rp <?= number_format($perhitungan['total_pendapatan'], 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td><strong>Total Potongan</strong></td>
                            <td class="text-end text-danger">Rp <?= number_format($perhitungan['total_potongan'], 0, ',', '.') ?></td>
                        </tr>
                        <tr class="border-top">
                            <td><strong class="text-success">Gaji Bersih</strong></td>
                            <td class="text-end"><strong class="text-success">Rp <?= number_format($perhitungan['gaji_bersih'], 0, ',', '.') ?></strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Pendapatan dan Potongan -->
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-plus-circle me-2"></i> Detail Pendapatan
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Komponen</th>
                                <th class="text-end">Jumlah (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Gaji Pokok</td>
                                <td class="text-end"><?= number_format($perhitungan['gaji_pokok'], 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td>Tunjangan Jabatan</td>
                                <td class="text-end"><?= number_format($perhitungan['tunjangan_jabatan'], 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td>Tunjangan Makan</td>
                                <td class="text-end"><?= number_format($perhitungan['tunjangan_makan'], 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td>Tunjangan Transport</td>
                                <td class="text-end"><?= number_format($perhitungan['tunjangan_transport'], 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td>Tunjangan Lainnya</td>
                                <td class="text-end"><?= number_format($perhitungan['tunjangan_lainnya'], 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td>Upah Lembur</td>
                                <td class="text-end"><?= number_format($perhitungan['upah_lembur'], 0, ',', '.') ?></td>
                            </tr>
                            <tr class="table-active">
                                <td><strong>Total Pendapatan</strong></td>
                                <td class="text-end"><strong><?= number_format($perhitungan['total_pendapatan'], 0, ',', '.') ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <i class="fas fa-minus-circle me-2"></i> Detail Potongan
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Komponen</th>
                                <th class="text-end">Jumlah (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>BPJS Kesehatan</td>
                                <td class="text-end"><?= number_format($perhitungan['potongan_bpjs_kes'], 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td>BPJS Ketenagakerjaan</td>
                                <td class="text-end"><?= number_format($perhitungan['potongan_bpjs_tk'], 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td>PPh 21</td>
                                <td class="text-end"><?= number_format($perhitungan['potongan_pph21'], 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td>Potongan Absensi</td>
                                <td class="text-end"><?= number_format($perhitungan['potongan_absensi'], 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td>Potongan Kasbon</td>
                                <td class="text-end"><?= number_format($perhitungan['potongan_kasbon'], 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td>Potongan Lainnya</td>
                                <td class="text-end"><?= number_format($perhitungan['potongan_lainnya'], 0, ',', '.') ?></td>
                            </tr>
                            <tr class="table-active">
                                <td><strong>Total Potongan</strong></td>
                                <td class="text-end"><strong class="text-danger"><?= number_format($perhitungan['total_potongan'], 0, ',', '.') ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Kehadiran -->
    <?php if ($perhitungan['total_hari_kerja'] > 0 || $perhitungan['total_hadir'] > 0): ?>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-calendar-alt me-2"></i> Data Kehadiran
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="border rounded p-3">
                                <h5 class="mb-0"><?= $perhitungan['total_hari_kerja'] ?></h5>
                                <small class="text-muted">Hari Kerja</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="border rounded p-3 bg-success text-white">
                                <h5 class="mb-0"><?= $perhitungan['total_hadir'] ?></h5>
                                <small>Hadir</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="border rounded p-3 bg-warning">
                                <h5 class="mb-0"><?= $perhitungan['total_izin'] ?></h5>
                                <small>Izin</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="border rounded p-3 bg-info">
                                <h5 class="mb-0"><?= $perhitungan['total_sakit'] ?></h5>
                                <small>Sakit</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="border rounded p-3">
                                <h5 class="mb-0"><?= $perhitungan['total_cuti'] ?></h5>
                                <small>Cuti</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="border rounded p-3 bg-danger text-white">
                                <h5 class="mb-0"><?= $perhitungan['total_alpha'] ?></h5>
                                <small>Alpha</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="border rounded p-3">
                                <h5 class="mb-0"><?= $perhitungan['total_terlambat'] ?></h5>
                                <small>Terlambat (hari)</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="border rounded p-3">
                                <h5 class="mb-0"><?= number_format($perhitungan['jam_lembur'], 1) ?></h5>
                                <small>Jam Lembur</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Reject -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-ban me-2"></i> Tolak Perhitungan Gaji
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="catatan" class="form-control" rows="4" required placeholder="Masukkan alasan penolakan..."></textarea>
                        <small class="text-muted">Alasan ini akan dicatat untuk referensi</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function approveGaji(id) {
    if (confirm('Setujui perhitungan gaji ini? Gaji akan masuk ke proses pembayaran.')) {
        $.ajax({
            url: '<?= site_url('accounting/penggajian/perhitungan-gaji/approve') ?>/' + id,
            method: 'POST',
            data: { _method: 'POST' },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('Terjadi kesalahan saat menyetujui gaji');
            }
        });
    }
}

function rejectGaji(id) {
    $('#rejectModal').modal('show');
    $('#rejectForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        let catatan = $(this).find('textarea[name="catatan"]').val();
        
        if (!catatan.trim()) {
            toastr.warning('Alasan penolakan harus diisi');
            return;
        }
        
        $.ajax({
            url: '<?= site_url('accounting/penggajian/perhitungan-gaji/reject') ?>/' + id,
            method: 'POST',
            data: { catatan: catatan },
            success: function(response) {
                if (response.success) {
                    $('#rejectModal').modal('hide');
                    toastr.success(response.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('Terjadi kesalahan saat menolak gaji');
            }
        });
    });
}
</script>

<?php $this->endSection(); ?>