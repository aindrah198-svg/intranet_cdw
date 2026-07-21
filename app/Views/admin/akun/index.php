<?php
$title = 'Manajemen Akun';
$active = 'akun';
?>

<?= $this->include('admin/templates/header') ?>
<?= $this->include('admin/templates/sidebar') ?>
<?= $this->include('admin/templates/navbar') ?>

<!-- Main Content -->
<div class="dashboard-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-2" style="color: var(--cdw-dark); font-weight: 600;">
                <i class="fas fa-user-cog me-2"></i><?= $title ?>
            </h5>
            <p class="text-muted mb-0">
                Manajemen akun login karyawan CDW Engineering
            </p>
        </div>
        <div>
            <div class="d-flex gap-2">
                <a href="<?= base_url('admin/karyawan/akun/create') ?>" class="btn btn-primary">
                    <i class="fas fa-user-plus me-1"></i> Buat Akun Baru
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
                <div class="card-label">Total Akun</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="dashboard-card" style="padding: 15px; text-align: center;">
                <div class="card-icon green">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="card-value"><?= $statistik['active'] ?? 0 ?></div>
                <div class="card-label">Akun Aktif</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="dashboard-card" style="padding: 15px; text-align: center;">
                <div class="card-icon orange">
                    <i class="fas fa-user-times"></i>
                </div>
                <div class="card-value"><?= $statistik['inactive'] ?? 0 ?></div>
                <div class="card-label">Nonaktif</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="dashboard-card" style="padding: 15px; text-align: center;">
                <div class="card-icon red">
                    <i class="fas fa-user-slash"></i>
                </div>
                <div class="card-value"><?= $statistik['suspended'] ?? 0 ?></div>
                <div class="card-label">Suspended</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="dashboard-card" style="padding: 15px; text-align: center;">
                <div class="card-icon purple">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="card-value"><?= $statistik['admin'] ?? 0 ?></div>
                <div class="card-label">Admin</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="dashboard-card" style="padding: 15px; text-align: center;">
                <div class="card-icon teal">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="card-value"><?= $statistik['manager'] ?? 0 ?></div>
                <div class="card-label">Manager</div>
            </div>
        </div>
    </div>
    
    <!-- Search and Filter -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card" style="border: 1px solid #eaeaea;">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Cari Username, Nama, atau Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" id="searchInput" class="form-control" placeholder="Cari data...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Role</label>
                            <select id="filterRole" class="form-select">
                                <option value="">Semua Role</option>
                                <option value="admin">Admin</option>
                                <option value="manager">Manager</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select id="filterStatus" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Data Table -->
    <div class="data-table-wrapper">
        <div class="table-responsive">
            <table class="table table-hover" id="akunTable">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Karyawan Terkait</th>
                        <th>Terakhir Login</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-user-slash fa-3x text-muted"></i>
                                </div>
                                <p class="text-muted mb-3">Belum ada data akun</p>
                                <a href="<?= base_url('admin/karyawan/akun/create') ?>" class="btn btn-primary">
                                    <i class="fas fa-user-plus me-1"></i> Buat Akun Pertama
                                </a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <strong><?= esc($user['username']) ?></strong>
                                    <?php if (!empty($user['karyawan_id'])): ?>
                                        <br>
                                        <small class="text-muted">ID: <?= $user['karyawan_id'] ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= esc($user['name']) ?></strong>
                                    <?php if (!empty($user['nama_lengkap'])): ?>
                                        <br>
                                        <small class="text-muted">Karyawan: <?= esc($user['nama_lengkap']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($user['email']) ?></td>
                                <td>
                                    <?php
                                    $roleClass = '';
                                    switch (strtolower($user['role'])) {
                                        case 'admin':
                                            $roleClass = 'bg-danger';
                                            break;
                                        case 'manager':
                                            $roleClass = 'bg-warning';
                                            break;
                                        case 'staff':
                                            $roleClass = 'bg-info';
                                            break;
                                        default:
                                            $roleClass = 'bg-secondary';
                                    }
                                    ?>
                                    <span class="badge <?= $roleClass ?>">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = '';
                                    switch ($user['status']) {
                                        case 'active':
                                            $statusClass = 'bg-success';
                                            break;
                                        case 'inactive':
                                            $statusClass = 'bg-secondary';
                                            break;
                                        case 'suspended':
                                            $statusClass = 'bg-danger';
                                            break;
                                    }
                                    ?>
                                    <span class="badge <?= $statusClass ?>">
                                        <?= ucfirst($user['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($user['nama_lengkap'])): ?>
                                        <div class="small">
                                            <div><i class="fas fa-id-card me-1"></i> <?= $user['nik'] ?? '-' ?></div>
                                            <div><i class="fas fa-briefcase me-1"></i> <?= $user['jabatan'] ?? '-' ?></div>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($user['last_login'])): ?>
                                        <?= date('d/m/Y H:i', strtotime($user['last_login'])) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Belum login</span>
                                    <?php endif; ?>
                                </td>
                               <!-- Di dalam loop foreach -->
<td class="text-center">
    <div class="btn-group btn-group-sm" role="group">
        <a href="<?= base_url('admin/karyawan/akun/show/' . $user['id']) ?>" 
           class="btn btn-info" 
           title="Detail">
            <i class="fas fa-eye"></i>
        </a>
        <a href="<?= base_url('admin/karyawan/akun/edit/' . $user['id']) ?>" 
           class="btn btn-warning" 
           title="Edit">
            <i class="fas fa-edit"></i>
        </a>
        <!-- PASTIKAN FORM INI METHOD POST -->
        <form action="<?= base_url('admin/karyawan/akun/delete/' . $user['id']) ?>" 
              method="POST" 
              class="d-inline"
              onsubmit="return confirm('Hapus akun <?= addslashes($user['name']) ?>?')">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="btn btn-danger" title="Hapus">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </div>
</td>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Initialize DataTable - SEDERHANA SAJA
    $(document).ready(function() {
        var table = $('#akunTable').DataTable({
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
            "order": [[1, 'asc']],
            "responsive": true
        });
        
        // Setup search input
        $('#searchInput').on('keyup', function() {
            table.search(this.value).draw();
        });
        
        // Setup filter role
        $('#filterRole').on('change', function() {
            var role = $(this).val();
            table.column(4).search(role ? role : '', true, false).draw();
        });
        
        // Setup filter status
        $('#filterStatus').on('change', function() {
            var status = $(this).val();
            table.column(5).search(status ? status : '', true, false).draw();
        });
    });
</script>

<?= $this->include('admin/templates/footer') ?>