<?php
// C:\xampp\htdocs\cdwnet\app\Views\admin\manajemen_cuti\my_cuti.php

// Debug: check if data exists
if (!isset($karyawan)) {
    echo "<div class='alert alert-danger'>Error: Karyawan data not found!</div>";
    echo "<pre>Session Data: " . print_r(session()->get(), true) . "</pre>";
    return;
}

$title = 'Cuti Saya';
$active = 'cuti';
?>

<?= $this->include('admin/templates/header') ?>
<?= $this->include('admin/templates/sidebar') ?>
<?= $this->include('admin/templates/navbar') ?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Cuti Saya</h1>
        <a href="<?= base_url('admin/cuti'); ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <!-- Info Karyawan -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Informasi Karyawan</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Nama:</strong> <?= esc($karyawan['nama_lengkap']); ?></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>NIK:</strong> <?= esc($karyawan['nik']); ?></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Jabatan:</strong> <?= esc($karyawan['jabatan'] ?? '-'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Cuti -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pengajuan Cuti</h6>
            <a href="<?= base_url('admin/cuti/create'); ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Ajukan Cuti Baru
            </a>
        </div>
        <div class="card-body">
            <?php if (empty($cuti)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum ada pengajuan cuti</h5>
                    <p class="text-muted">Mulai ajukan cuti pertama Anda.</p>
                    <a href="<?= base_url('admin/cuti/create'); ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Ajukan Cuti Pertama
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nomor Cuti</th>
                                <th>Jenis</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Lama</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($cuti as $c): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= esc($c['nomor_cuti']); ?></td>
                                <td><?= esc($c['jenis_cuti']); ?></td>
                                <td><?= date('d/m/Y', strtotime($c['tanggal_mulai'])); ?></td>
                                <td><?= date('d/m/Y', strtotime($c['tanggal_selesai'])); ?></td>
                                <td><?= $c['lama_hari']; ?> hari</td>
                                <td>
                                    <?php 
                                    $badgeClass = 'secondary';
                                    if ($c['status'] === 'Disetujui HRD' || $c['status'] === 'Disetujui Atasan') {
                                        $badgeClass = 'success';
                                    } elseif ($c['status'] === 'Ditolak') {
                                        $badgeClass = 'danger';
                                    } elseif ($c['status'] === 'Menunggu') {
                                        $badgeClass = 'warning';
                                    }
                                    ?>
                                    <span class="badge bg-<?= $badgeClass; ?>"><?= $c['status']; ?></span>
                                </td>
                                <td>
                                    <a href="<?= base_url('admin/cuti/show/' . $c['id']); ?>" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    console.log('My Cuti Page Loaded');
    console.log('Karyawan:', '<?= $karyawan["nama_lengkap"] ?? "Unknown" ?>');
    console.log('Total Cuti:', <?= count($cuti) ?>);
});
</script>

<?= $this->include('admin/templates/footer') ?>