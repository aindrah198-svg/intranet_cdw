<?php
$title = 'Data Karyawan';
$active = 'karyawan';
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
<div class="dashboard-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-2" style="color: var(--cdw-dark); font-weight: 600;">
                <i class="fas fa-users me-2"></i><?= $title ?>
            </h5>
            <p class="text-muted mb-0">
                Manajemen data karyawan CDW Engineering
            </p>
        </div>
        <div>
            <div class="d-flex gap-2">
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-filter me-1"></i> Filter Status
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= base_url('admin/karyawan') ?>">Semua Karyawan</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/karyawan/aktif') ?>">Aktif</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/karyawan/keluar') ?>">Sudah Keluar</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/karyawan') . '?status=Tetap' ?>">Karyawan Tetap</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/karyawan') . '?status=Kontrak' ?>">Karyawan Kontrak</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/karyawan') . '?status=Probation' ?>">Probation</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/karyawan') . '?status=Magang' ?>">Magang</a></li>
                    </ul>
                </div>
                <a href="<?= base_url('admin/karyawan/export') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-file-export me-1"></i> Export
                </a>
                <a href="<?= base_url('admin/karyawan/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Tambah Karyawan
                </a>
            </div>
        </div>
    </div>
    
    <!-- Statistik Ringkas -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="dashboard-card" style="padding: 15px; text-align: center;">
                <div class="card-icon blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="card-value"><?= $statistik['total'] ?? 0 ?></div>
                <div class="card-label">Total Karyawan</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="dashboard-card" style="padding: 15px; text-align: center;">
                <div class="card-icon green">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="card-value"><?= $statistik['aktif'] ?? 0 ?></div>
                <div class="card-label">Karyawan Aktif</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="dashboard-card" style="padding: 15px; text-align: center;">
                <div class="card-icon orange">
                    <i class="fas fa-user-times"></i>
                </div>
                <div class="card-value"><?= $statistik['keluar'] ?? 0 ?></div>
                <div class="card-label">Sudah Keluar</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="dashboard-card" style="padding: 15px; text-align: center;">
                <div class="card-icon blue">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="card-value"><?= $statistik['tetap'] ?? 0 ?></div>
                <div class="card-label">Karyawan Tetap</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="dashboard-card" style="padding: 15px; text-align: center;">
                <div class="card-icon purple">
                    <i class="fas fa-file-contract"></i>
                </div>
                <div class="card-value"><?= $statistik['kontrak'] ?? 0 ?></div>
                <div class="card-label">Karyawan Kontrak</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="dashboard-card" style="padding: 15px; text-align: center;">
                <div class="card-icon purple">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="card-value"><?= ($statistik['probation'] ?? 0) + ($statistik['magang'] ?? 0) ?></div>
                <div class="card-label">Probation & Magang</div>
            </div>
        </div>
    </div>
    
    <!-- Search Box -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card" style="border: 1px solid #eaeaea;">
                <div class="card-body">
                    <form action="<?= base_url('admin/karyawan/search') ?>" method="get" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Cari NIK atau Nama</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" name="keyword" class="form-control" placeholder="Masukkan NIK atau nama..." value="<?= $keyword ?? '' ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Departemen</label>
                            <select name="departemen" class="form-select">
                                <option value="">Semua Departemen</option>
                                <?php
                                $departemenList = [];
                                foreach ($karyawan ?? [] as $k) {
                                    if (!empty($k['departemen'])) {
                                        $departemenList[] = $k['departemen'];
                                    }
                                }
                                $departemenList = array_unique($departemenList);
                                sort($departemenList);
                                foreach ($departemenList as $dept):
                                ?>
                                <option value="<?= $dept ?>" <?= isset($_GET['departemen']) && $_GET['departemen'] == $dept ? 'selected' : '' ?>>
                                    <?= $dept ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status Karyawan</label>
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="Tetap" <?= isset($_GET['status']) && $_GET['status'] == 'Tetap' ? 'selected' : '' ?>>Tetap</option>
                                <option value="Kontrak" <?= isset($_GET['status']) && $_GET['status'] == 'Kontrak' ? 'selected' : '' ?>>Kontrak</option>
                                <option value="Probation" <?= isset($_GET['status']) && $_GET['status'] == 'Probation' ? 'selected' : '' ?>>Probation</option>
                                <option value="Magang" <?= isset($_GET['status']) && $_GET['status'] == 'Magang' ? 'selected' : '' ?>>Magang</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i> Cari
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Data Table -->
    <div class="data-table-wrapper">
        <div class="table-responsive">
            <table class="table table-hover" id="karyawanTable">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th width="70">Foto</th>
                        <th>NIK</th>
                        <th>Nama Lengkap</th>
                        <th>Jabatan</th>
                        <th>Departemen/Divisi</th>
                        <th>Status</th>
                        <th>Tanggal Masuk</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($karyawan)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-users fa-3x text-muted"></i>
                                </div>
                                <p class="text-muted mb-3">Tidak ada data karyawan ditemukan</p>
                                <a href="<?= base_url('admin/karyawan/create') ?>" class="btn btn-primary">
                                    <i class="fas fa-plus-circle me-1"></i> Tambah Karyawan
                                </a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($karyawan as $k): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <?php if (!empty($k['foto'])): ?>
                                        <img src="<?= base_url($k['foto']) ?>" alt="Foto" 
                                             style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #eaeaea;">
                                    <?php else: ?>
                                        <div class="avatar-circle" style="background: linear-gradient(135deg, #6c757d, #495057);">
                                            <?= strtoupper(substr($k['nama_lengkap'] ?? '?', 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= $k['nik'] ?></strong>
                                    <?php if (!empty($k['tanggal_keluar'])): ?>
                                        <br>
                                        <small class="text-danger">
                                            <i class="fas fa-calendar-times"></i> 
                                            <?= date('d/m/Y', strtotime($k['tanggal_keluar'])) ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= $k['nama_lengkap'] ?></strong>
                                    <?php if (!empty($k['nama_panggilan'])): ?>
                                        <br>
                                        <small class="text-muted">(<?= $k['nama_panggilan'] ?>)</small>
                                    <?php endif; ?>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-<?= $k['jenis_kelamin'] == 'L' ? 'male' : 'female' ?> me-1"></i>
                                        <?= $k['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?>
                                        • <i class="fas fa-birthday-cake me-1"></i>
                                        <?= !empty($k['tanggal_lahir']) ? date('d/m/Y', strtotime($k['tanggal_lahir'])) : '-' ?>
                                    </small>
                                </td>
                                <td><?= $k['jabatan'] ?? '-' ?></td>
                                <td>
                                    <?= $k['departemen'] ?? '-' ?>
                                    <?php if (!empty($k['divisi'])): ?>
                                        <br>
                                        <small class="text-muted">Divisi: <?= $k['divisi'] ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $badgeClass = '';
                                    switch ($k['status_karyawan']) {
                                        case 'Tetap':
                                            $badgeClass = 'bg-success';
                                            break;
                                        case 'Kontrak':
                                            $badgeClass = 'bg-primary';
                                            break;
                                        case 'Probation':
                                            $badgeClass = 'bg-warning';
                                            break;
                                        case 'Magang':
                                            $badgeClass = 'bg-info';
                                            break;
                                    }
                                    ?>
                                    <span class="badge <?= $badgeClass ?> badge-custom">
                                        <?= $k['status_karyawan'] ?>
                                    </span>
                                    <?php if (!empty($k['tanggal_keluar'])): ?>
                                        <br>
                                        <small class="text-danger">
                                            <i class="fas fa-door-closed"></i> Keluar
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($k['tanggal_masuk'])): ?>
                                        <?= date('d/m/Y', strtotime($k['tanggal_masuk'])) ?>
                                        <br>
                                        <small class="text-muted">
                                            <?php 
                                            $masuk = new DateTime($k['tanggal_masuk']);
                                            $sekarang = new DateTime();
                                            $selisih = $sekarang->diff($masuk);
                                            echo $selisih->y . ' tahun ' . $selisih->m . ' bulan';
                                            ?>
                                        </small>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
<a href="<?= base_url('admin/karyawan/show/' . $k['id']) ?>" class="btn btn-sm btn-info" title="Detail">
    <i class="fas fa-eye"></i>
</a>
                                        <a href="<?= base_url('admin/karyawan/edit/' . $k['id']) ?>" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if (empty($k['tanggal_keluar'])): ?>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmKeluar(<?= $k['id'] ?>)" title="Tandai Keluar">
                                                <i class="fas fa-door-open"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-sm btn-secondary" onclick="confirmDelete(<?= $k['id'] ?>)" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
  <!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteForm" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus data karyawan ini?</p>
                    <p class="text-danger"><small>Data yang telah dihapus tidak dapat dikembalikan.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>
    
    <!-- Modal Tandai Keluar -->
    <div class="modal fade" id="keluarModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tandai Karyawan Keluar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="#" id="keluarForm" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tanggal Keluar *</label>
                            <input type="date" name="tanggal_keluar" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alasan Keluar</label>
                            <textarea name="alasan_keluar" class="form-control" rows="3" placeholder="Masukkan alasan keluar..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    
    // Initialize DataTable
    $(document).ready(function() {
        $('#karyawanTable').DataTable({
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
                { "orderable": false, "targets": [0, 1, 8] }
            ],
            "order": [[2, 'asc']]
        });
    });
    
    // Confirm Delete Function
    function confirmDelete(id) {
        $('#confirmDelete').attr('href', '<?= base_url('admin/karyawan/delete/') ?>' + id);
        $('#deleteModal').modal('show');
    }
    
    // Confirm Keluar Function
    function confirmKeluar(id) {
        $('#keluarForm').attr('action', '<?= base_url('admin/karyawan/update-keluar/') ?>' + id);
        $('#keluarModal').modal('show');
    }
    
    // Set today's date for keluar modal
    $('#keluarModal').on('show.bs.modal', function() {
        var today = new Date().toISOString().split('T')[0];
        $(this).find('input[type="date"]').val(today);
    });
    
    // Search on enter
    $('input[name="keyword"]').keypress(function(e) {
        if (e.which == 13) {
            $(this).closest('form').submit();
        }
    });
    
    // Filter status badge colors
    $(document).ready(function() {
        $('.badge').each(function() {
            var status = $(this).text().trim();
            switch(status) {
                case 'Tetap':
                    $(this).removeClass().addClass('badge bg-success badge-custom');
                    break;
                case 'Kontrak':
                    $(this).removeClass().addClass('badge bg-primary badge-custom');
                    break;
                case 'Probation':
                    $(this).removeClass().addClass('badge bg-warning badge-custom');
                    break;
                case 'Magang':
                    $(this).removeClass().addClass('badge bg-info badge-custom');
                    break;
            }
        });
    })

    // Confirm Delete Function
function confirmDelete(id) {
    // Set form action
    $('#deleteForm').attr('action', '<?= base_url('admin/karyawan/delete/') ?>' + id);
    $('#deleteModal').modal('show');
}

// Submit form saat konfirmasi
$(document).on('submit', '#deleteForm', function(e) {
    e.preventDefault();
    
    var form = $(this);
    var url = form.attr('action');
    
    $.ajax({
        url: url,
        type: 'POST',
        data: form.serialize(),
        success: function(response) {
            if (response.success) {
                $('#deleteModal').modal('hide');
                showToast('success', response.message);
                // Refresh halaman setelah 1 detik
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            } else {
                showToast('error', response.message);
            }
        },
        error: function() {
            showToast('error', 'Terjadi kesalahan saat menghapus data');
        }
    });
});

</script>

<?= $this->include('admin/templates/footer') ?>