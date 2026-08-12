<!-- Konten utama -->
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="mb-1"><?= esc($title ?? 'Daftar Client') ?></h4>
            <p class="text-muted mb-0"><?= esc($subtitle ?? 'Kelola semua data client') ?></p>
        </div>
        <div>
            <a href="<?= base_url('teknisi/tugas-proyek/tambah-client/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Client Baru
            </a>
            <a href="<?= base_url('teknisi/tugas-proyek/tambah-client/export-excel') ?>" class="btn btn-success">
                <i class="fas fa-file-excel me-2"></i>Export Excel
            </a>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Client</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $statistik['total'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
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
                                Client Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $statistik['active'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Client Potensial</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $statistik['potensial'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
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
                                Perusahaan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $statistik['perusahaan'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-briefcase fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="dashboard-card mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="fas fa-filter me-2 text-primary"></i>Filter Client</h5>
        </div>
        <div class="card-body">
            <form action="<?= base_url('teknisi/tugas-proyek/tambah-client') ?>" method="get" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Kategori</label>
                    <select name="kategori" class="form-select">
                        <?php foreach($kategori_options as $key => $value): ?>
                            <option value="<?= $key ?>" <?= ($key == ($selected_kategori ?? 'semua')) ? 'selected' : '' ?>>
                                <?= $value ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <?php foreach($status_options as $key => $value): ?>
                            <option value="<?= $key ?>" <?= ($key == ($selected_status ?? 'semua')) ? 'selected' : '' ?>>
                                <?= $value ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Pencarian</label>
                    <input type="text" name="search" class="form-control" value="<?= $search ?? '' ?>" placeholder="Nama perusahaan, kontak, email...">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Client -->
    <div class="dashboard-card">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>Daftar Client</h5>
            <div>
                <span class="badge bg-primary">Total: <?= count($clients) ?></span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="12%">Kode Client</th>
                            <th width="20%">Nama Perusahaan</th>
                            <th width="15%">Kontak Person</th>
                            <th width="12%">Telepon/Email</th>
                            <th width="8%">Kategori</th>
                            <th width="8%">Status</th>
                            <th width="10%">Ditangani Oleh</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($clients)): ?>
                            <?php $no = 1; ?>
                            <?php foreach($clients as $client): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <strong><?= esc($client->kode_client) ?></strong>
                                    </td>
                                    <td>
                                        <strong><?= esc($client->nama_perusahaan) ?></strong>
                                        <?php if(!empty($client->keperluan_client)): ?>
                                            <br><small class="text-muted"><i class="fas fa-tag me-1"></i><?= esc(substr($client->keperluan_client, 0, 50)) ?><?= strlen($client->keperluan_client) > 50 ? '...' : '' ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= esc($client->nama_kontak) ?: '-' ?>
                                        <?php if(!empty($client->client_kontak)): ?>
                                            <br><small class="text-muted"><?= esc($client->client_kontak) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if(!empty($client->telepon)): ?>
                                            <i class="fas fa-phone me-1 text-muted"></i><?= esc($client->telepon) ?><br>
                                        <?php endif; ?>
                                        <?php if(!empty($client->email_client)): ?>
                                            <i class="fas fa-envelope me-1 text-muted"></i><?= esc($client->email_client) ?>
                                        <?php endif; ?>
                                        <?php if(empty($client->telepon) && empty($client->email_client)): ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $kategoriClass = [
                                            'perusahaan' => 'badge bg-primary',
                                            'pemerintah' => 'badge bg-success',
                                            'perorangan' => 'badge bg-info'
                                        ];
                                        $class = $kategoriClass[$client->kategori] ?? 'badge bg-secondary';
                                        ?>
                                        <span class="<?= $class ?>"><?= ucfirst($client->kategori) ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClass = [
                                            'active' => 'badge bg-success',
                                            'inactive' => 'badge bg-danger',
                                            'potensial' => 'badge bg-warning'
                                        ];
                                        $class = $statusClass[$client->status] ?? 'badge bg-secondary';
                                        $statusText = [
                                            'active' => 'Active',
                                            'inactive' => 'Inactive',
                                            'potensial' => 'Potensial'
                                        ];
                                        ?>
                                        <span class="<?= $class ?>"><?= $statusText[$client->status] ?? $client->status ?></span>
                                    </td>
                                    <td>
                                        <?php if(!empty($client->karyawan_nama)): ?>
                                            <?= esc($client->karyawan_nama) ?>
                                            <br><small class="text-muted"><?= esc($client->karyawan_jabatan ?? '') ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('teknisi/tugas-proyek/tambah-client/detail/' . $client->id) ?>" 
                                               class="btn btn-sm btn-info" 
                                               data-bs-toggle="tooltip" 
                                               title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('teknisi/tugas-proyek/tambah-client/edit/' . $client->id) ?>" 
                                               class="btn btn-sm btn-warning"
                                               data-bs-toggle="tooltip" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger" 
                                                    onclick="hapusClient(<?= $client->id ?>, '<?= esc($client->nama_perusahaan) ?>')"
                                                    data-bs-toggle="tooltip" 
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-success dropdown-toggle" 
                                                    data-bs-toggle="dropdown" 
                                                    aria-expanded="false">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="ubahStatus(<?= $client->id ?>, 'active')">
                                                        <i class="fas fa-check-circle me-2 text-success"></i>Set Active
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="ubahStatus(<?= $client->id ?>, 'inactive')">
                                                        <i class="fas fa-times-circle me-2 text-danger"></i>Set Inactive
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="ubahStatus(<?= $client->id ?>, 'potensial')">
                                                        <i class="fas fa-star me-2 text-warning"></i>Set Potensial
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item" href="<?= base_url('teknisi/tugas-proyek/spk?client_id=' . $client->id) ?>">
                                                        <i class="fas fa-file-contract me-2"></i>Lihat SPK
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="fas fa-building fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Belum ada data client</p>
                                    <a href="<?= base_url('teknisi/tugas-proyek/tambah-client/create') ?>" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Tambah Client Baru
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

<!-- SweetAlert2 & DataTables -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Inisialisasi DataTable
    $('#dataTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
        },
        order: [[0, 'asc']],
        pageLength: 25
    });

    // Inisialisasi tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});

function ubahStatus(id, status) {
    let statusText = status === 'active' ? 'Active' : (status === 'inactive' ? 'Inactive' : 'Potensial');
    
    Swal.fire({
        title: 'Ubah Status Client?',
        text: `Apakah Anda yakin ingin mengubah status client menjadi ${statusText}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Ubah',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url("teknisi/tugas-proyek/tambah-client/ubah-status") ?>/' + id,
                type: 'POST',
                data: {
                    status: status,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan: ' + error
                    });
                }
            });
        }
    });
}

function hapusClient(id, nama) {
    Swal.fire({
        title: 'Hapus Client?',
        html: `Apakah Anda yakin ingin menghapus client <strong>${nama}</strong>?<br><br>
               <span class="text-danger">Data yang dihapus tidak dapat dikembalikan!</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url("teknisi/tugas-proyek/tambah-client/delete") ?>/' + id,
                type: 'POST',
                data: {
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Client berhasil dihapus',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal menghapus client: ' + error
                    });
                }
            });
        }
    });
}

// Tampilkan pesan dari session
<?php if(session()->getFlashdata('success')): ?>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '<?= session()->getFlashdata('success') ?>',
        showConfirmButton: false,
        timer: 3000
    });
<?php endif; ?>

<?php if(session()->getFlashdata('error')): ?>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '<?= session()->getFlashdata('error') ?>',
        confirmButtonText: 'OK'
    });
<?php endif; ?>

<?php if(session()->getFlashdata('info')): ?>
    Swal.fire({
        icon: 'info',
        title: 'Info',
        text: '<?= session()->getFlashdata('info') ?>',
        confirmButtonText: 'OK'
    });
<?php endif; ?>
</script>

<style>
/* Custom styles */
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}

.dashboard-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    overflow: hidden;
}
.card-header {
    border-bottom: 1px solid #eaeaea;
    background-color: white;
}

/* Badge styles */
.badge {
    padding: 0.5em 0.75em;
    font-size: 0.75rem;
    font-weight: 500;
}

/* Table styles */
.table td {
    vertical-align: middle;
    padding: 0.75rem;
}
.table th {
    background-color: #f8f9fc;
    font-weight: 600;
    font-size: 0.85rem;
    white-space: nowrap;
}

/* Button group */
.btn-group .btn {
    padding: 0.25rem 0.5rem;
}
.dropdown-menu {
    font-size: 0.875rem;
}

/* Responsive */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    .btn-group {
        display: flex;
        flex-wrap: wrap;
    }
}
</style>

<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">