<?php
$title = 'Dokumen Karyawan';
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
        <h1 class="h3 mb-0 text-gray-800">Manajemen Dokumen Karyawan</h1>
        <a href="<?= base_url('admin/karyawan/dokumen/create'); ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-upload fa-sm text-white-50"></i> Upload Dokumen
        </a>
    </div>

    <!-- Search and Filter -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Dokumen</h6>
        </div>
        <div class="card-body">
            <form method="get" action="<?= base_url('admin/karyawan/dokumen'); ?>">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="search">Cari (Nama/NIK/Jenis Dokumen):</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?= esc($search ?? ''); ?>" placeholder="Cari...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="jenis">Jenis Dokumen:</label>
                            <select class="form-control" id="jenis" name="jenis">
                                <option value="">Semua Jenis</option>
                                <?php if(isset($jenisOptions)): ?>
                                <?php foreach ($jenisOptions as $key => $value): ?>
                                    <option value="<?= $key; ?>" <?= (isset($_GET['jenis']) && $_GET['jenis'] == $key) ? 'selected' : ''; ?>>
                                        <?= $value; ?>
                                    </option>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">Semua Status</option>
                                <?php if(isset($statusOptions)): ?>
                                <?php foreach ($statusOptions as $key => $value): ?>
                                    <option value="<?= $key; ?>" <?= (isset($_GET['status']) && $_GET['status'] == $key) ? 'selected' : ''; ?>>
                                        <?= $value; ?>
                                    </option>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="<?= base_url('admin/karyawan/dokumen'); ?>" class="btn btn-secondary">Reset</a>
            </form>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Dokumen
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total ?? 0; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Dokumen</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0" id="dokumenTable">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Karyawan</th>
                            <th>NIK</th>
                            <th>Jenis Dokumen</th>
                            <th>Nomor Dokumen</th>
                            <th>Status</th>
                            <th>Tanggal Upload</th>
                            <th width="180">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($dokumen) && !empty($dokumen)): ?>
                            <?php $no = 1; ?>
                            <?php foreach ($dokumen as $item): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= esc($item['nama_lengkap']); ?></td>
                                <td><?= esc($item['nik']); ?></td>
                                <td>
                                    <span class="badge bg-info">
                                        <?= isset($jenisOptions[$item['jenis']]) ? $jenisOptions[$item['jenis']] : esc($item['jenis']); ?>
                                    </span>
                                </td>
                                <td><?= esc($item['nomor_dokumen'] ?? '-'); ?></td>
                                <td>
                                    <?php 
                                    $statusClass = [
                                        'pending' => 'warning',
                                        'diterima' => 'success',
                                        'ditolak' => 'danger'
                                    ];
                                    $statusLabel = [
                                        'pending' => 'Pending',
                                        'diterima' => 'Diterima',
                                        'ditolak' => 'Ditolak'
                                    ];
                                    ?>
                                    <span class="badge bg-<?= $statusClass[$item['status']] ?? 'secondary'; ?>">
                                        <?= $statusLabel[$item['status']] ?? $item['status']; ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($item['created_at'])); ?></td>
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
                                        <a href="<?= base_url('admin/karyawan/dokumen/' . $item['karyawan_id']); ?>" 
                                           class="btn btn-sm btn-primary" title="Lihat Semua Dokumen Karyawan">
                                            <i class="fas fa-folder"></i>
                                        </a>
                                        <form action="<?= base_url('admin/karyawan/dokumen/delete/' . $item['id']); ?>" 
                                              method="post" class="d-inline" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')">
                                            <?= csrf_field(); ?>
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Tidak ada data dokumen ditemukan.</p>
                                <a href="<?= base_url('admin/karyawan/dokumen/create'); ?>" class="btn btn-primary">
                                    <i class="fas fa-upload me-1"></i> Upload Dokumen Pertama
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

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus dokumen ini?</p>
                <p class="text-danger"><small>Dokumen yang telah dihapus tidak dapat dikembalikan.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="#" id="confirmDeleteForm" method="post" style="display: inline;">
                    <?= csrf_field(); ?>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize DataTable
    $(document).ready(function() {
        $('#dokumenTable').DataTable({
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
                { "orderable": false, "targets": [0, 7] }
            ],
            "order": [[6, 'desc']] // Sort by tanggal upload descending
        });

        // Konfirmasi delete dengan modal
        $(document).on('click', 'form[onsubmit*="confirm"] button[type="submit"]', function(e) {
            e.preventDefault();
            
            var form = $(this).closest('form');
            var actionUrl = form.attr('action');
            
            $('#confirmDeleteForm').attr('action', actionUrl);
            $('#deleteModal').modal('show');
        });

        // Submit delete form dari modal
        $('#confirmDeleteForm').submit(function(e) {
            e.preventDefault();
            var form = $(this);
            
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    $('#deleteModal').modal('hide');
                    location.reload();
                },
                error: function() {
                    alert('Terjadi kesalahan saat menghapus dokumen');
                }
            });
        });

        // Auto-hide alerts
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);

        // Search on enter
        $('input[name="search"]').keypress(function(e) {
            if (e.which == 13) {
                $(this).closest('form').submit();
            }
        });
    });
</script>

<?= $this->include('admin/templates/footer') ?>