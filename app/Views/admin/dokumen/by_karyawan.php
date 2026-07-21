<?php
$title = 'Dokumen Karyawan: ' . $karyawan['nama_lengkap'];
$active = 'dokumen';
$is_dokumen_page = true;
$css = ['https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css'];
$scripts = [
    'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js',
    'https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js'
];
?>

<?= $this->include('admin/templates/header') ?>
<?= $this->include('admin/templates/sidebar') ?>
<?= $this->include('admin/templates/navbar') ?>

<!-- Main Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                Dokumen Karyawan: <?= esc($karyawan['nama_lengkap']) ?>
            </h1>
            <p class="text-muted mb-0">
                NIK: <?= esc($karyawan['nik']) ?> • 
                <?= esc($karyawan['jabatan'] ?? '-') ?> • 
                <?= esc($karyawan['departemen'] ?? '-') ?>
            </p>
        </div>
        <div>
            <a href="<?= base_url('admin/karyawan/dokumen'); ?>" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Semua Dokumen
            </a>
            <a href="<?= base_url('admin/karyawan/dokumen/create'); ?>" class="btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Dokumen
            </a>
        </div>
    </div>

    <!-- Informasi Karyawan -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Karyawan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= esc($karyawan['nama_lengkap']) ?></div>
                            <div class="text-muted"><?= esc($karyawan['nik']) ?></div>
                        </div>
                        <div class="col-auto">
                            <?php if (!empty($karyawan['foto'])): ?>
                                <img src="<?= base_url($karyawan['foto']) ?>" alt="Foto" 
                                     style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid #eaeaea;">
                            <?php else: ?>
                                <div class="avatar-circle" style="width: 60px; height: 60px; background: linear-gradient(135deg, #6c757d, #495057); 
                                                                   border-radius: 50%; display: flex; align-items: center; justify-content: center; 
                                                                   color: white; font-size: 1.5rem; font-weight: bold;">
                                    <?= strtoupper(substr($karyawan['nama_lengkap'] ?? '?', 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Dokumen
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($dokumen) ?></div>
                            <div class="text-muted">Dokumen yang diupload</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-folder fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Status
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= esc($karyawan['status_karyawan'] ?? '-') ?>
                            </div>
                            <div class="text-muted">
                                <?= !empty($karyawan['tanggal_masuk']) ? 'Masuk: ' . date('d/m/Y', strtotime($karyawan['tanggal_masuk'])) : '' ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-folder me-2"></i>Daftar Dokumen
            </h6>
            <a href="<?= base_url('admin/karyawan/dokumen/create'); ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i> Tambah Dokumen
            </a>
        </div>
        <div class="card-body">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0" id="dokumenKaryawanTable">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Jenis Dokumen</th>
                            <th>Nomor Dokumen</th>
                            <th>Status</th>
                            <th>Tanggal Upload</th>
                            <th>Kadaluarsa</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($dokumen)): ?>
                            <?php $no = 1; ?>
                            <?php foreach ($dokumen as $item): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td>
                                    <span class="badge bg-info">
                                        <?= isset($jenisOptions[$item['jenis']]) ? $jenisOptions[$item['jenis']] : esc($item['jenis']); ?>
                                    </span>
                                    <br>
                                    <small><?= esc($item['nama_file']) ?></small>
                                </td>
                                <td><?= esc($item['nomor_dokumen'] ?? '-'); ?></td>
                                <td>
                                    <?php 
                                    $statusClass = [
                                        'pending' => 'warning',
                                        'diterima' => 'success',
                                        'ditolak' => 'danger'
                                    ];
                                    ?>
                                    <span class="badge bg-<?= $statusClass[$item['status']] ?? 'secondary'; ?>">
                                        <?= $statusOptions[$item['status']] ?? $item['status']; ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($item['created_at'])); ?></td>
                                <td>
                                    <?php if (!empty($item['tanggal_kadaluarsa'])): ?>
                                        <?php 
                                        $expired = strtotime($item['tanggal_kadaluarsa']) < time();
                                        ?>
                                        <span class="<?= $expired ? 'text-danger' : 'text-success' ?>">
                                            <?= date('d/m/Y', strtotime($item['tanggal_kadaluarsa'])) ?>
                                            <?php if ($expired): ?>
                                                <span class="badge bg-danger">Expired</span>
                                            <?php endif; ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('admin/karyawan/dokumen/show/' . $item['id']); ?>" 
                                           class="btn btn-sm btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('admin/karyawan/dokumen/edit/' . $item['id']); ?>" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('admin/karyawan/dokumen/download/' . $item['id']); ?>" 
                                           class="btn btn-sm btn-primary" title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Belum ada dokumen untuk karyawan ini.</p>
                                    <a href="<?= base_url('admin/karyawan/dokumen/create'); ?>" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i> Tambah Dokumen Pertama
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#dokumenKaryawanTable').DataTable({
            "pageLength": 10,
            "lengthMenu": [10, 25, 50, 100],
            "language": {
                "search": "Cari:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                "infoFiltered": "(disaring dari _MAX_ total data)",
                "zeroRecords": "Data tidak ditemukan",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Berikutnya",
                    "previous": "Sebelumnya"
                }
            },
            "columnDefs": [
                { "orderable": false, "targets": [0, 6] }
            ],
            "order": [[4, 'desc']] // Sort by tanggal upload descending
        });

        // Auto-hide alerts
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    });
</script>

<style>
    .badge {
        font-size: 0.85em;
        padding: 0.35em 0.65em;
    }
    .btn-group .btn {
        margin-right: 2px;
    }
</style>

<?= $this->include('admin/templates/footer') ?>