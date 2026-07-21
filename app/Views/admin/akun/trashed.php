<?php
$title = 'Akun Terhapus';
$active = 'akun';
?>

<?= $this->include('admin/templates/header') ?>
<?= $this->include('admin/templates/sidebar') ?>
<?= $this->include('admin/templates/navbar') ?>

<div class="dashboard-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-2" style="color: var(--cdw-dark); font-weight: 600;">
                <i class="fas fa-trash-restore me-2"></i><?= $title ?>
            </h5>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('admin/karyawan/akun') ?>">Manajemen Akun</a></li>
                <li class="breadcrumb-item active">Akun Terhapus</li>
            </ol>
        </div>
        <div>
            <a href="<?= base_url('admin/karyawan/akun') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Akun
            </a>
        </div>
    </div>
    
    <!-- Alert Info -->
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        Halaman ini menampilkan akun yang telah dihapus (soft delete). Akun dapat dipulihkan atau dihapus permanen.
    </div>
    
    <!-- Data Table -->
    <div class="data-table-wrapper">
        <div class="table-responsive">
            <table class="table table-hover" id="trashedTable">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Dihapus Pada</th>
                        <th width="180">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-trash-alt fa-3x text-muted"></i>
                                </div>
                                <p class="text-muted mb-3">Tidak ada akun yang dihapus</p>
                                <a href="<?= base_url('admin/karyawan/akun') ?>" class="btn btn-primary">
                                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Manajemen Akun
                                </a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <strong><?= $user['username'] ?></strong>
                                    <br>
                                    <small class="text-muted">ID: <?= $user['id'] ?></small>
                                </td>
                                <td><?= $user['name'] ?></td>
                                <td><?= $user['email'] ?></td>
                                <td>
                                    <?= $roleBadge($user['role']) ?>
                                </td>
                                <td>
                                    <?= $statusBadge($user['status']) ?>
                                </td>
                                <td>
                                    <?php if (!empty($user['deleted_at'])): ?>
                                        <?= date('d/m/Y H:i', strtotime($user['deleted_at'])) ?>
                                        <br>
                                        <small class="text-muted">
                                            <?= $timeAgo($user['deleted_at']) ?>
                                        </small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <form action="<?= base_url('admin/karyawan/akun/restore/' . $user['id']) ?>" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-success" 
                                                    onclick="return confirm('Pulihkan akun ini?')" title="Pulihkan">
                                                <i class="fas fa-trash-restore"></i>
                                            </button>
                                        </form>
                                        <form action="<?= base_url('admin/karyawan/akun/force-delete/' . $user['id']) ?>" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Hapus PERMANEN akun ini?\\n\\nTindakan ini tidak dapat dibatalkan!')" title="Hapus Permanen">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Modal Konfirmasi -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Konfirmasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <!-- Konten akan diisi oleh JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="confirmAction">Ya</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize DataTable
    $(document).ready(function() {
        $('#trashedTable').DataTable({
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
            "order": [[6, 'desc']]
        });
    });
</script>

<?= $this->include('admin/templates/footer') ?>