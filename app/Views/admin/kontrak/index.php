<?php
// C:\xampp\htdocs\cdwnet\app\Views\admin\kontrak\index.php
$title = 'Manajemen Kontrak Kerja';
$active = 'kontrak';
$css = [
    'https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css',
    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
];
$scripts = [
    'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js',
    'https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js',
    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
];
?>

<?= $this->include('admin/templates/header') ?>
<?= $this->include('admin/templates/sidebar') ?>
<?= $this->include('admin/templates/navbar') ?>

<!-- Main Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Kontrak Kerja</h1>
        <a href="<?= base_url('admin/karyawan/kontrak/create'); ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Buat Kontrak Baru
        </a>
    </div>

    <!-- Search and Filter -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Kontrak</h6>
        </div>
        <div class="card-body">
            <form method="get" action="<?= base_url('admin/karyawan/kontrak'); ?>">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="search">Cari (Nama/NIK/Nomor Kontrak):</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?= esc($search ?? ''); ?>" placeholder="Cari...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="jenis">Jenis Kontrak:</label>
                            <select class="form-control" id="jenis" name="jenis">
                                <option value="">Semua Jenis</option>
                                <?php if(isset($jenisOptions)): ?>
                                <?php foreach ($jenisOptions as $key => $value): ?>
                                    <option value="<?= $key; ?>" <?= (isset($jenis) && $jenis == $key) ? 'selected' : ''; ?>>
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
                                    <option value="<?= $key; ?>" <?= (isset($status) && $status == $key) ? 'selected' : ''; ?>>
                                        <?= $value; ?>
                                    </option>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col">
                        <a href="<?= base_url('admin/karyawan/kontrak'); ?>" class="btn btn-secondary">Reset Filter</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Kontrak
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total'] ?? 0; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-contract fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Kontrak Aktif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['aktif'] ?? 0; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Kontrak Draft
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['draft'] ?? 0; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-edit fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Kontrak Selesai
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['selesai'] ?? 0; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-flag-checkered fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-8 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Jenis Kontrak
                            </div>
                            <div class="row">
                                <?php if(isset($stats['jenis'])): ?>
                                <?php foreach ($stats['jenis'] as $jenis): ?>
                                <div class="col-4">
                                    <small class="text-muted"><?= $jenis['jenis_kontrak'] ?></small>
                                    <div class="h6 mb-0 font-weight-bold text-gray-800"><?= $jenis['total'] ?></div>
                                </div>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Kontrak Kerja</h6>
            <div>
                <span class="badge bg-secondary me-2">Total: <?= $total ?? 0; ?></span>
                <a href="<?= base_url('admin/karyawan/kontrak/create'); ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i> Buat Baru
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0" id="kontrakTable">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Nomor Kontrak</th>
                            <th>Karyawan</th>
                            <th>Jabatan</th>
                            <th>Jenis</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th>Gaji Pokok</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($kontrak) && !empty($kontrak)): ?>
                            <?php $no = 1; ?>
                            <?php foreach ($kontrak as $item): ?>
                            <?php 
                                // Format currency
                                $gaji_pokok = number_format($item['gaji_pokok'] ?? 0, 0, ',', '.');
                                
                                // Format periode
                                $periode = date('d/m/Y', strtotime($item['tanggal_mulai']));
                                if ($item['tanggal_selesai']) {
                                    $periode .= ' - ' . date('d/m/Y', strtotime($item['tanggal_selesai']));
                                }
                                
                                // Status badge
                                $statusClass = [
                                    'Draft' => 'secondary',
                                    'Aktif' => 'success',
                                    'Selesai' => 'info',
                                    'Diperpanjang' => 'warning',
                                    'Diputus' => 'danger'
                                ];
                                
                                // Jenis badge
                                $jenisClass = [
                                    'Probation' => 'warning',
                                    'Kontrak' => 'primary',
                                    'Tetap' => 'success',
                                    'Magang' => 'info'
                                ];
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td>
                                    <strong><?= esc($item['nomor_kontrak']); ?></strong>
                                    <br>
                                    <small class="text-muted"><?= date('d/m/Y H:i', strtotime($item['created_at'])); ?></small>
                                </td>
                                <td>
                                    <strong><?= esc($item['nama_lengkap']); ?></strong>
                                    <br>
                                    <small class="text-muted">NIK: <?= esc($item['nik']); ?></small>
                                </td>
                                <td><?= esc($item['jabatan']); ?></td>
                                <td>
                                    <span class="badge bg-<?= $jenisClass[$item['jenis_kontrak']] ?? 'secondary'; ?>">
                                        <?= $item['jenis_kontrak']; ?>
                                    </span>
                                    <?php if($item['masa_percobaan_bulan']): ?>
                                    <br>
                                    <small class="text-muted">Probation: <?= $item['masa_percobaan_bulan']; ?> bulan</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $periode; ?>
                                    <?php if($item['masa_kerja_bulan']): ?>
                                    <br>
                                    <small class="text-muted"><?= $item['masa_kerja_bulan']; ?> bulan</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $statusClass[$item['status']] ?? 'secondary'; ?>">
                                        <?= $item['status']; ?>
                                    </span>
                                </td>
                                <td>Rp <?= $gaji_pokok; ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('admin/karyawan/kontrak/show/' . $item['id']); ?>" 
                                           class="btn btn-sm btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('admin/karyawan/kontrak/edit/' . $item['id']); ?>" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('admin/karyawan/kontrak/karyawan/' . $item['karyawan_id']); ?>" 
                                           class="btn btn-sm btn-primary" title="Lihat Semua Kontrak Karyawan">
                                            <i class="fas fa-user"></i>
                                        </a>
                                        <form action="<?= base_url('admin/karyawan/kontrak/delete/' . $item['id']); ?>" 
                                              method="post" class="d-inline" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus kontrak ini?')">
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
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-file-contract fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Tidak ada data kontrak ditemukan.</p>
                                <a href="<?= base_url('admin/karyawan/kontrak/create'); ?>" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Buat Kontrak Pertama
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
                <h5 class="modal-title">Konfirmasi Hapus Kontrak</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus kontrak ini?</p>
                <p class="text-danger"><small>Kontrak yang telah dihapus tidak dapat dikembalikan.</small></p>
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
        $('#kontrakTable').DataTable({
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
                { "orderable": false, "targets": [0, 8] }
            ],
            "order": [[5, 'desc']] // Sort by tanggal mulai descending
        });

        // Initialize Select2
        $('#jenis, #status').select2({
            theme: 'bootstrap-5',
            width: '100%'
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
                    alert('Terjadi kesalahan saat menghapus kontrak');
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