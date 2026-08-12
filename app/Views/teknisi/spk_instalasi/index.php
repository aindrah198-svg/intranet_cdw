<?= $this->include('teknisi/templates/header') ?>
<?= $this->include('teknisi/templates/sidebar') ?>
<?= $this->include('teknisi/templates/navbar') ?>

<!-- Konten utama -->
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="mb-1"><?= esc($title ?? 'Daftar SPK / Tugas Instalasi') ?></h4>
            <p class="text-muted mb-0"><?= esc($subtitle ?? 'Kelola semua SPK dan tugas instalasi') ?></p>
        </div>
        <div>
            <a href="<?= base_url('teknisi/tugas-proyek/spk/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah SPK Baru
            </a>
            <a href="<?= base_url('teknisi/tugas-proyek/spk/export') ?>" class="btn btn-success">
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
                                Total SPK</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $statistik['total'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-contract fa-2x text-gray-300"></i>
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
                                Dalam Pengerjaan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $statistik['dalam_pengerjaan'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-spinner fa-2x text-gray-300"></i>
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
                                Selesai</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $statistik['selesai'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Progress Rata-rata</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= round($statistik['total_progress'] ?? 0) ?>%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="dashboard-card mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="fas fa-filter me-2 text-primary"></i>Filter SPK</h5>
        </div>
        <div class="card-body">
            <form action="<?= base_url('teknisi/tugas-proyek/spk') ?>" method="get" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <?php foreach($status_options as $key => $value): ?>
                            <option value="<?= $key ?>" <?= ($key == ($status ?? 'semua')) ? 'selected' : '' ?>>
                                <?= $value ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Prioritas</label>
                    <select name="prioritas" class="form-select">
                        <?php foreach($prioritas_options as $key => $value): ?>
                            <option value="<?= $key ?>" <?= ($key == ($prioritas ?? 'semua')) ? 'selected' : '' ?>>
                                <?= $value ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control" value="<?= $tanggal_mulai ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control" value="<?= $tanggal_selesai ?? '' ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel SPK -->
    <div class="dashboard-card">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>Daftar SPK</h5>
            <div>
                <span class="badge bg-primary">Total: <?= count($spk_list) ?></span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="12%">Nomor SPK</th>
                            <th width="20%">Judul Pekerjaan</th>
                            <th width="15%">Client</th>
                            <th width="10%">Tanggal Mulai</th>
                            <th width="10%">Target Selesai</th>
                            <th width="8%">Prioritas</th>
                            <th width="8%">Status</th>
                            <th width="7%">Progress</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($spk_list)): ?>
                            <?php $no = 1; ?>
                            <?php foreach($spk_list as $spk): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <strong><?= esc($spk->nomor_spk ?? '-') ?></strong>
                                    </td>
                                    <td>
                                        <?= esc($spk->judul_pekerjaan ?? '-') ?>
                                        <?php if(!empty($spk->deskripsi)): ?>
                                            <br><small class="text-muted"><?= esc(substr($spk->deskripsi, 0, 50)) ?>...</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= esc($spk->client_nama ?? $spk->client_nama_tabel ?? '-') ?>
                                        <?php if(!empty($spk->client_kontak)): ?>
                                            <br><small class="text-muted"><?= esc($spk->client_kontak) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= !empty($spk->tanggal_mulai) ? date('d/m/Y', strtotime($spk->tanggal_mulai)) : '-' ?></td>
                                    <td>
                                        <?= !empty($spk->target_selesai) ? date('d/m/Y', strtotime($spk->target_selesai)) : (!empty($spk->tanggal_selesai) ? date('d/m/Y', strtotime($spk->tanggal_selesai)) : '-') ?>
                                    </td>
                                    <td>
                                        <?php
                                        $prioritasClass = [
                                            'Rendah' => 'badge bg-info',
                                            'Normal' => 'badge bg-primary',
                                            'Tinggi' => 'badge bg-warning',
                                            'Urgent' => 'badge bg-danger'
                                        ];
                                        $prioritasVal = $spk->prioritas ?? 'Normal';
                                        $class = $prioritasClass[$prioritasVal] ?? 'badge bg-secondary';
                                        ?>
                                        <span class="<?= $class ?>"><?= esc($prioritasVal) ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClass = [
                                            'Draft' => 'badge bg-secondary',
                                            'Dijadwalkan' => 'badge bg-info',
                                            'Dalam Pengerjaan' => 'badge bg-primary',
                                            'Selesai' => 'badge bg-success',
                                            'Ditunda' => 'badge bg-warning',
                                            'Dibatalkan' => 'badge bg-danger'
                                        ];
                                        $statusVal = $spk->status ?? 'Dijadwalkan';
                                        $class = $statusClass[$statusVal] ?? 'badge bg-secondary';
                                        ?>
                                        <span class="<?= $class ?>"><?= esc($statusVal) ?></span>
                                    </td>
                                    <td>
                                        <?php $progressVal = (int)($spk->progress_persen ?? 0); ?>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar <?= $progressVal >= 100 ? 'bg-success' : ($progressVal >= 50 ? 'bg-info' : 'bg-warning') ?>" 
                                                 role="progressbar" 
                                                 style="width: <?= $progressVal ?>%;"
                                                 aria-valuenow="<?= $progressVal ?>" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                <?= $progressVal ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('teknisi/tugas-proyek/spk/detail/' . $spk->id) ?>" 
                                               class="btn btn-sm btn-info" 
                                               data-bs-toggle="tooltip" 
                                               title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('teknisi/tugas-proyek/spk/edit/' . $spk->id) ?>" 
                                               class="btn btn-sm btn-warning"
                                               data-bs-toggle="tooltip" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger" 
                                                    onclick="hapusSpk(<?= $spk->id ?>, '<?= esc($spk->nomor_spk) ?>')"
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
                                                    <a class="dropdown-item" href="#" onclick="updateStatus(<?= $spk->id ?>, 'progress')">
                                                        <i class="fas fa-chart-line me-2"></i>Update Progress
                                                    </a>
                                                </li>
                                                <?php if($spk->status != 'Selesai'): ?>
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="updateStatus(<?= $spk->id ?>, 'selesai')">
                                                        <i class="fas fa-check-circle me-2"></i>Tandai Selesai
                                                    </a>
                                                </li>
                                                <?php endif; ?>
                                                <?php if($spk->status != 'Ditunda'): ?>
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="updateStatus(<?= $spk->id ?>, 'tunda')">
                                                        <i class="fas fa-pause-circle me-2"></i>Tunda
                                                    </a>
                                                </li>
                                                <?php endif; ?>
                                                <?php if($spk->status != 'Dibatalkan'): ?>
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="updateStatus(<?= $spk->id ?>, 'batal')">
                                                        <i class="fas fa-times-circle me-2"></i>Batalkan
                                                    </a>
                                                </li>
                                                <?php endif; ?>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item" href="<?= base_url('teknisi/tugas-proyek/spk/export-pdf/' . $spk->id) ?>">
                                                        <i class="fas fa-file-pdf me-2"></i>Export PDF
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center py-5">
                                    <i class="fas fa-file-contract fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Belum ada data SPK</p>
                                    <a href="<?= base_url('teknisi/tugas-proyek/spk/create') ?>" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Tambah SPK Baru
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

<!-- Modal Update Progress -->
<div class="modal fade" id="progressModal" tabindex="-1" aria-labelledby="progressModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="progressModalLabel">Update Progress SPK</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="progressForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="progress_spk_id">
                    <div class="mb-3">
                        <label class="form-label">Progress (%)</label>
                        <input type="range" class="form-range" name="progress" id="progress_value" min="0" max="100" step="5" onchange="updateProgressDisplay(this.value)">
                        <div class="text-center mt-2">
                            <span class="badge bg-primary" id="progress_display">0%</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Progress</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Alasan -->
<div class="modal fade" id="alasanModal" tabindex="-1" aria-labelledby="alasanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="alasanModalLabel">Alasan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="alasanForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="alasan_spk_id">
                    <input type="hidden" name="action" id="alasan_action">
                    <div class="mb-3">
                        <label class="form-label">Alasan <span class="text-danger">*</span></label>
                        <textarea name="alasan" id="alasan_text" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
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
        order: [[1, 'desc']],
        pageLength: 25
    });

    // Inisialisasi tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});

function updateProgressDisplay(value) {
    $('#progress_display').text(value + '%');
}

function updateStatus(id, action) {
    if (action === 'progress') {
        $('#progress_spk_id').val(id);
        $('#progress_value').val(0);
        $('#progress_display').text('0%');
        $('#progressModal').modal('show');
    } else if (action === 'selesai') {
        Swal.fire({
            title: 'Selesaikan SPK?',
            text: 'Apakah Anda yakin ingin menandai SPK ini sebagai selesai?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Selesaikan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url("teknisi/tugas-proyek/spk/selesaikan") ?>/' + id,
                    type: 'POST',
                    data: {
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'SPK berhasil diselesaikan',
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
                            text: 'Gagal menyelesaikan SPK: ' + error
                        });
                    }
                });
            }
        });
    } else {
        let title = action === 'tunda' ? 'Tunda SPK' : 'Batalkan SPK';
        let actionText = action === 'tunda' ? 'menunda' : 'membatalkan';
        
        $('#alasan_spk_id').val(id);
        $('#alasan_action').val(action);
        $('#alasanModalLabel').text(title);
        $('#alasanForm').attr('action', 'javascript:void(0)');
        $('#alasanModal').modal('show');
    }
}

// Form Progress
$('#progressForm').on('submit', function(e) {
    e.preventDefault();
    
    let id = $('#progress_spk_id').val();
    let progress = $('#progress_value').val();
    
    $.ajax({
        url: '<?= base_url("teknisi/tugas-proyek/spk/updateProgress") ?>',
        type: 'POST',
        data: {
            id: id,
            progress: progress,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        },
        success: function(response) {
            if (response.success) {
                $('#progressModal').modal('hide');
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
});

// Form Alasan
$('#alasanForm').on('submit', function(e) {
    e.preventDefault();
    
    let id = $('#alasan_spk_id').val();
    let action = $('#alasan_action').val();
    let alasan = $('#alasan_text').val();
    
    if (!alasan) {
        Swal.fire({
            icon: 'warning',
            title: 'Alasan Diperlukan',
            text: 'Mohon isi alasan terlebih dahulu'
        });
        return;
    }
    
    let url = action === 'tunda' ? 'tunda' : 'batalkan';
    
    $.ajax({
        url: '<?= base_url("teknisi/tugas-proyek/spk/") ?>' + url + '/' + id,
        type: 'POST',
        data: {
            alasan: alasan,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        },
        success: function(response) {
            $('#alasanModal').modal('hide');
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'SPK berhasil ' + (action === 'tunda' ? 'ditunda' : 'dibatalkan'),
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
                text: 'Gagal memproses permintaan: ' + error
            });
        }
    });
});

function hapusSpk(id, nomor) {
    Swal.fire({
        title: 'Hapus SPK?',
        html: `Apakah Anda yakin ingin menghapus SPK <strong>${nomor}</strong>?<br><br>
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
                url: '<?= base_url("teknisi/tugas-proyek/spk/delete") ?>/' + id,
                type: 'POST',
                data: {
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'SPK berhasil dihapus',
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
                        text: 'Gagal menghapus SPK: ' + error
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

/* Progress bar */
.progress {
    background-color: #eaecf4;
    border-radius: 0.35rem;
}
.progress-bar {
    font-size: 0.7rem;
    font-weight: 600;
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

<!-- Additional CSS for DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">

<?= $this->include('teknisi/templates/footer') ?>