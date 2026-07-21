<?php
// C:\xampp\htdocs\cdwnet\app\Views\admin\kontrak\by_karyawan.php
$title = 'Kontrak Kerja: ' . esc($karyawan['nama_lengkap'] ?? '');
$active = 'kontrak';
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
            <h1 class="h3 mb-0 text-gray-800">Kontrak Kerja Karyawan</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/karyawan') ?>">Karyawan</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/karyawan/show/' . ($karyawan['id'] ?? '')) ?>"><?= esc($karyawan['nama_lengkap'] ?? '') ?></a></li>
                    <li class="breadcrumb-item active">Kontrak</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('admin/karyawan/kontrak/create-for/' . ($karyawan['id'] ?? '')) ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Buat Kontrak Baru
            </a>
        </div>
    </div>

    <!-- Informasi Karyawan -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Karyawan</h6>
            <a href="<?= base_url('admin/karyawan/show/' . ($karyawan['id'] ?? '')) ?>" class="btn btn-sm btn-info">
                <i class="fas fa-user me-1"></i> Lihat Profil Lengkap
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center mb-4">
                    <div class="avatar-circle mb-3" style="
                        width: 80px; 
                        height: 80px; 
                        background: linear-gradient(45deg, #4e73df, #1cc88a);
                        border-radius: 50%;
                        margin: 0 auto;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-size: 2rem;
                        font-weight: bold;
                    ">
                        <?= strtoupper(substr($karyawan['nama_lengkap'] ?? 'A', 0, 1)) ?>
                    </div>
                    <h5 class="mb-1"><?= esc($karyawan['nama_lengkap'] ?? '-') ?></h5>
                    <p class="text-muted mb-0"><?= esc($karyawan['jabatan'] ?? '-') ?></p>
                </div>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th width="40%">NIK</th>
                                    <td width="5%">:</td>
                                    <td><?= esc($karyawan['nik'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>:</td>
                                    <td><?= esc($karyawan['email'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <th>Telepon</th>
                                    <td>:</td>
                                    <td><?= esc($karyawan['telepon'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <th>Tanggal Masuk</th>
                                    <td>:</td>
                                    <td><?= $karyawan['tanggal_masuk'] ? date('d/m/Y', strtotime($karyawan['tanggal_masuk'])) : '-' ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <th width="40%">Status Karyawan</th>
                                    <td width="5%">:</td>
                                    <td>
                                        <span class="badge bg-<?= 
                                            ($karyawan['status_karyawan'] == 'Tetap') ? 'success' : 
                                            (($karyawan['status_karyawan'] == 'Kontrak') ? 'primary' : 
                                            (($karyawan['status_karyawan'] == 'Probation') ? 'warning' : 'info')) 
                                        ?>">
                                            <?= $karyawan['status_karyawan'] ?? '-' ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Departemen</th>
                                    <td>:</td>
                                    <td><?= esc($karyawan['departemen'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <th>Divisi</th>
                                    <td>:</td>
                                    <td><?= esc($karyawan['divisi'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <th>Total Kontrak</th>
                                    <td>:</td>
                                    <td><span class="badge bg-secondary"><?= count($kontrak) ?> kontrak</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
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
                                Kontrak Aktif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= 
                                    array_reduce($kontrak, function($carry, $item) {
                                        return $carry + ($item['status'] == 'Aktif' ? 1 : 0);
                                    }, 0) 
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Kontrak Selesai
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= 
                                    array_reduce($kontrak, function($carry, $item) {
                                        return $carry + ($item['status'] == 'Selesai' ? 1 : 0);
                                    }, 0) 
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-flag-checkered fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Kontrak Draft
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= 
                                    array_reduce($kontrak, function($carry, $item) {
                                        return $carry + ($item['status'] == 'Draft' ? 1 : 0);
                                    }, 0) 
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-edit fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Gaji Rata-rata
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php 
                                    $totalGaji = 0;
                                    $countGaji = 0;
                                    foreach ($kontrak as $k) {
                                        if ($k['gaji_pokok'] > 0) {
                                            $totalGaji += $k['gaji_pokok'];
                                            $countGaji++;
                                        }
                                    }
                                    $avgGaji = $countGaji > 0 ? $totalGaji / $countGaji : 0;
                                    echo 'Rp ' . number_format($avgGaji, 0, ',', '.');
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Kontrak Kerja</h6>
            <div>
                <a href="<?= base_url('admin/karyawan/kontrak/create-for/' . ($karyawan['id'] ?? '')) ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i> Kontrak Baru
                </a>
                <a href="<?= base_url('admin/karyawan/kontrak') ?>" class="btn btn-sm btn-secondary">
                    <i class="fas fa-list me-1"></i> Semua Kontrak
                </a>
            </div>
        </div>
        <div class="card-body">
            <?php if(isset($kontrak) && !empty($kontrak)): ?>
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0" id="kontrakTable">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Nomor Kontrak</th>
                            <th>Jenis</th>
                            <th>Jabatan</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th>Gaji Pokok</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
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
                                <small class="text-muted"><?= date('d/m/Y', strtotime($item['created_at'])); ?></small>
                            </td>
                            <td>
                                <span class="badge bg-<?= $jenisClass[$item['jenis_kontrak']] ?? 'secondary'; ?>">
                                    <?= $item['jenis_kontrak']; ?>
                                </span>
                                <?php if($item['masa_percobaan_bulan']): ?>
                                <br>
                                <small class="text-muted">Probation: <?= $item['masa_percobaan_bulan']; ?> bulan</small>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($item['jabatan']); ?></td>
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
                                    <?php if($item['status'] == 'Aktif'): ?>
                                    <a href="<?= base_url('admin/karyawan/kontrak/karyawan/' . $item['karyawan_id']); ?>" 
                                       class="btn btn-sm btn-primary" title="Semua Kontrak">
                                        <i class="fas fa-list"></i>
                                    </a>
                                    <?php endif; ?>
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
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-file-contract fa-4x text-muted mb-4"></i>
                <h4 class="text-muted">Belum Ada Kontrak</h4>
                <p class="text-muted mb-4">Karyawan ini belum memiliki riwayat kontrak kerja.</p>
                <a href="<?= base_url('admin/karyawan/kontrak/create-for/' . ($karyawan['id'] ?? '')) ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Buat Kontrak Pertama
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Kontrak Aktif Saat Ini -->
    <?php 
    $kontrakAktif = array_filter($kontrak, function($item) {
        return $item['status'] == 'Aktif';
    });
    
    if (!empty($kontrakAktif)): 
        $aktif = current($kontrakAktif);
    ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-success text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-star me-2"></i>Kontrak Aktif Saat Ini
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">Nomor Kontrak</th>
                            <td width="5%">:</td>
                            <td><strong><?= esc($aktif['nomor_kontrak']) ?></strong></td>
                        </tr>
                        <tr>
                            <th>Jenis Kontrak</th>
                            <td>:</td>
                            <td>
                                <span class="badge bg-<?= $jenisClass[$aktif['jenis_kontrak']] ?? 'secondary'; ?>">
                                    <?= $aktif['jenis_kontrak'] ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Periode</th>
                            <td>:</td>
                            <td>
                                <?= date('d/m/Y', strtotime($aktif['tanggal_mulai'])) ?>
                                <?php if($aktif['tanggal_selesai']): ?>
                                    - <?= date('d/m/Y', strtotime($aktif['tanggal_selesai'])) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Gaji Pokok</th>
                            <td>:</td>
                            <td><strong>Rp <?= number_format($aktif['gaji_pokok'], 0, ',', '.') ?></strong></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-4 text-center">
                    <?php 
                    if ($aktif['tanggal_selesai']) {
                        $today = new DateTime();
                        $endDate = new DateTime($aktif['tanggal_selesai']);
                        $interval = $today->diff($endDate);
                        $daysLeft = $interval->days;
                        
                        if ($daysLeft <= 30) {
                            $progressClass = 'danger';
                            $progressValue = 100;
                        } else {
                            $totalDays = (new DateTime($aktif['tanggal_mulai']))->diff($endDate)->days;
                            $daysPassed = $totalDays - $daysLeft;
                            $progressValue = ($daysPassed / $totalDays) * 100;
                            $progressClass = $progressValue >= 80 ? 'warning' : 'success';
                        }
                    } else {
                        $progressValue = 0;
                        $progressClass = 'info';
                    }
                    ?>
                    <div class="progress mb-3" style="height: 20px;">
                        <div class="progress-bar bg-<?= $progressClass ?>" 
                             role="progressbar" 
                             style="width: <?= $progressValue ?>%">
                            <?php if($aktif['tanggal_selesai']): ?>
                            <?= round($progressValue) ?>%
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if($aktif['tanggal_selesai']): ?>
                    <p class="mb-1">
                        <?php if($daysLeft <= 30): ?>
                        <span class="text-danger">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Berakhir dalam <?= $daysLeft ?> hari
                        </span>
                        <?php else: ?>
                        <span class="text-success">
                            <i class="fas fa-clock me-1"></i>
                            <?= $daysLeft ?> hari tersisa
                        </span>
                        <?php endif; ?>
                    </p>
                    <?php else: ?>
                    <p class="text-info">
                        <i class="fas fa-infinity me-1"></i>
                        Tidak terbatas
                    </p>
                    <?php endif; ?>
                    <a href="<?= base_url('admin/karyawan/kontrak/show/' . $aktif['id']) ?>" class="btn btn-success mt-2">
                        <i class="fas fa-external-link-alt me-1"></i> Lihat Detail
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Timeline Kontrak -->
    <?php if(!empty($kontrak)): ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-history me-2"></i>Timeline Kontrak
            </h6>
        </div>
        <div class="card-body">
            <div class="timeline">
                <?php 
                // Urutkan kontrak berdasarkan tanggal mulai (terbaru ke terlama)
                usort($kontrak, function($a, $b) {
                    return strtotime($b['tanggal_mulai']) - strtotime($a['tanggal_mulai']);
                });
                
                foreach ($kontrak as $index => $item): 
                    $statusColor = ($item['status'] == 'Aktif') ? 'success' : 
                                  (($item['status'] == 'Selesai') ? 'info' : 
                                  (($item['status'] == 'Diputus') ? 'danger' : 'secondary'));
                ?>
                <div class="timeline-item <?= $index % 2 == 0 ? 'left' : 'right' ?>">
                    <div class="timeline-date">
                        <?= date('M Y', strtotime($item['tanggal_mulai'])) ?>
                        <?php if($item['tanggal_selesai']): ?>
                        - <?= date('M Y', strtotime($item['tanggal_selesai'])) ?>
                        <?php else: ?>
                        - Sekarang
                        <?php endif; ?>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-header">
                            <h6><?= esc($item['jabatan']) ?></h6>
                            <span class="badge bg-<?= $statusColor ?>"><?= $item['status'] ?></span>
                        </div>
                        <p class="mb-1">
                            <small class="text-muted">
                                <i class="fas fa-hashtag me-1"></i><?= esc($item['nomor_kontrak']) ?>
                            </small>
                        </p>
                        <p class="mb-1">
                            <small>
                                <i class="fas fa-money-bill-wave me-1"></i>
                                Rp <?= number_format($item['gaji_pokok'], 0, ',', '.') ?>
                            </small>
                        </p>
                        <div class="timeline-actions mt-2">
                            <a href="<?= base_url('admin/karyawan/kontrak/show/' . $item['id']) ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
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
                <p class="text-danger">
                    <small>
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Kontrak yang telah dihapus tidak dapat dikembalikan.
                    </small>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="#" id="confirmDeleteForm" method="post" class="d-inline">
                    <?= csrf_field(); ?>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize DataTable
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
                { "orderable": false, "targets": [0, 7] }
            ],
            "order": [[4, 'desc']] // Sort by periode descending
        });

        // Konfirmasi delete dengan modal
        $(document).on('click', 'form[onsubmit*="confirm"] button[type="submit"]', function(e) {
            e.preventDefault();
            
            var form = $(this).closest('form');
            var actionUrl = form.attr('action');
            var kontrakNomor = form.closest('tr').find('td:nth-child(2) strong').text();
            
            // Update modal content
            $('#deleteModal .modal-body').html(`
                <p>Apakah Anda yakin ingin menghapus kontrak ini?</p>
                <p class="text-danger">
                    <small>
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Kontrak yang telah dihapus tidak dapat dikembalikan.
                    </small>
                </p>
                <div class="alert alert-warning">
                    <strong>Detail Kontrak:</strong><br>
                    Nomor: ${kontrakNomor}<br>
                    Karyawan: <?= esc($karyawan['nama_lengkap']) ?>
                </div>
            `);
            
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
    });
</script>

<style>
    /* Timeline Styles */
    .timeline {
        position: relative;
        padding: 20px 0;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 50%;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e0e0e0;
        transform: translateX(-50%);
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 30px;
        width: 45%;
    }
    
    .timeline-item.left {
        left: 0;
        text-align: right;
    }
    
    .timeline-item.right {
        left: 55%;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        top: 20px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #4e73df;
    }
    
    .timeline-item.left::before {
        right: -41px;
    }
    
    .timeline-item.right::before {
        left: -41px;
    }
    
    .timeline-content {
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border: 1px solid #eaeaea;
    }
    
    .timeline-date {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 5px;
        font-weight: 500;
    }
    
    .timeline-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    
    .timeline-header h6 {
        margin: 0;
        font-size: 1rem;
    }
    
    .breadcrumb {
        background: transparent;
        padding: 0;
        margin-bottom: 0;
    }
    
    .breadcrumb-item + .breadcrumb-item::before {
        content: ">";
    }
    
    .avatar-circle {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .progress {
        border-radius: 10px;
    }
    
    .progress-bar {
        border-radius: 10px;
        font-weight: 500;
    }
    
    @media (max-width: 768px) {
        .timeline::before {
            left: 20px;
        }
        
        .timeline-item {
            width: calc(100% - 50px);
            margin-left: 50px;
        }
        
        .timeline-item.left,
        .timeline-item.right {
            left: 0;
            text-align: left;
        }
        
        .timeline-item::before {
            left: -32px;
            right: auto;
        }
        
        .col-md-3.text-center {
            order: -1;
            margin-bottom: 20px;
        }
    }
</style>

<?= $this->include('admin/templates/footer') ?>