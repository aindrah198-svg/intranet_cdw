<?= $this->include('teknisi/templates/header') ?>
<?= $this->include('teknisi/templates/sidebar') ?>
<?= $this->include('teknisi/templates/navbar') ?>

<!-- Konten utama -->
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><?= esc($title ?? 'Detail Client') ?></h4>
            <p class="text-muted mb-0">Informasi lengkap data client</p>
        </div>
        <div>
            <a href="<?= base_url('teknisi/tugas-proyek/tambah-client/edit/' . ($client->id ?? 0)) ?>" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="<?= base_url('teknisi/tugas-proyek/tambah-client') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Informasi Client -->
    <div class="row">
        <div class="col-lg-4">
            <!-- Card Profil Client -->
            <div class="dashboard-card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-building me-2 text-primary"></i>Profil Client</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-circle mx-auto mb-3">
                            <span class="avatar-initials"><?= substr($client->nama_perusahaan ?? '-', 0, 1) ?></span>
                        </div>
                        <h5 class="fw-bold mb-1"><?= esc($client->nama_perusahaan ?? '-') ?></h5>
                        <p class="text-muted mb-2">
                            <span class="badge bg-<?= ($client->kategori ?? 'perusahaan') == 'perusahaan' ? 'primary' : (($client->kategori ?? '') == 'pemerintah' ? 'success' : 'info') ?>">
                                <?= ucfirst($client->kategori ?? 'perusahaan') ?>
                            </span>
                            <span class="badge bg-<?= ($client->status ?? 'active') == 'active' ? 'success' : (($client->status ?? '') == 'inactive' ? 'danger' : 'warning') ?> ms-1">
                                <?= ($client->status ?? 'active') == 'active' ? 'Active' : (($client->status ?? '') == 'inactive' ? 'Inactive' : 'Potensial') ?>
                            </span>
                        </p>
                        <p class="text-muted small">
                            <i class="fas fa-code me-1"></i>Kode: <?= esc($client->kode_client ?? '-') ?>
                        </p>
                    </div>

                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="35%"><i class="fas fa-user me-2 text-primary"></i>Kontak Person</td>
                            <td width="5%">:</td>
                            <td><?= esc($client->nama_kontak ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-phone me-2 text-primary"></i>Telepon</td>
                            <td>:</td>
                            <td>
                                <?php if(!empty($client->telepon)): ?>
                                    <a href="tel:<?= esc($client->telepon) ?>"><?= esc($client->telepon) ?></a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-envelope me-2 text-primary"></i>Email</td>
                            <td>:</td>
                            <td>
                                <?php if(!empty($client->email_client)): ?>
                                    <a href="mailto:<?= esc($client->email_client) ?>"><?= esc($client->email_client) ?></a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-address-book me-2 text-primary"></i>Kontak Lain</td>
                            <td>:</td>
                            <td><?= esc($client->client_kontak ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-user-tie me-2 text-primary"></i>Ditangani Oleh</td>
                            <td>:</td>
                            <td>
                                <?php if(!empty($client->karyawan_nama)): ?>
                                    <strong><?= esc($client->karyawan_nama) ?></strong>
                                    <br><small class="text-muted"><?= esc($client->karyawan_jabatan ?? '') ?></small>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-calendar-alt me-2 text-primary"></i>Tanggal Dibuat</td>
                            <td>:</td>
                            <td>
                                <?php if(!empty($client->created_at)): ?>
                                    <?= date('d/m/Y H:i', strtotime($client->created_at)) ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-history me-2 text-primary"></i>Terakhir Update</td>
                            <td>:</td>
                            <td>
                                <?php if(!empty($client->updated_at)): ?>
                                    <?= date('d/m/Y H:i', strtotime($client->updated_at)) ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Statistik SPK -->
            <div class="dashboard-card">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2 text-primary"></i>Statistik SPK</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="bg-light rounded p-3">
                                <h3 class="fw-bold text-primary mb-1"><?= $statistik_spk->total ?? 0 ?></h3>
                                <small class="text-muted">Total SPK</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="bg-light rounded p-3">
                                <h3 class="fw-bold text-success mb-1"><?= $statistik_spk->selesai ?? 0 ?></h3>
                                <small class="text-muted">Selesai</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded p-3">
                                <h3 class="fw-bold text-warning mb-1"><?= $statistik_spk->dalam_pengerjaan ?? 0 ?></h3>
                                <small class="text-muted">Dalam Pengerjaan</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded p-3">
                                <h3 class="fw-bold text-info mb-1"><?= $statistik_spk->dijadwalkan ?? 0 ?></h3>
                                <small class="text-muted">Dijadwalkan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Alamat -->
            <div class="dashboard-card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Alamat</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-semibold mb-2">Alamat Utama</h6>
                            <div class="bg-light p-3 rounded">
                                <?php 
                                if(!empty($client->alamat) && is_string($client->alamat)): 
                                    echo nl2br(esc($client->alamat));
                                else: 
                                ?>
                                    <span class="text-muted">- Tidak ada alamat -</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-semibold mb-2">Alamat Alternatif / Lokasi Proyek</h6>
                            <div class="bg-light p-3 rounded">
                                <?php 
                                if(!empty($client->client_alamat) && is_string($client->client_alamat)): 
                                    echo nl2br(esc($client->client_alamat));
                                else: 
                                ?>
                                    <span class="text-muted">- Tidak ada alamat alternatif -</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Keperluan & Catatan -->
            <div class="row">
                <div class="col-md-6">
                    <div class="dashboard-card mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0"><i class="fas fa-clipboard-list me-2 text-primary"></i>Keperluan / Kebutuhan</h5>
                        </div>
                        <div class="card-body">
                            <div class="bg-light p-3 rounded">
                                <?php 
                                if(!empty($client->keperluan_client) && is_string($client->keperluan_client)): 
                                    echo nl2br(esc($client->keperluan_client));
                                else: 
                                ?>
                                    <span class="text-muted">- Tidak ada data keperluan -</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="dashboard-card mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0"><i class="fas fa-sticky-note me-2 text-primary"></i>Catatan Khusus</h5>
                        </div>
                        <div class="card-body">
                            <div class="bg-light p-3 rounded">
                                <?php 
                                if(!empty($client->catatan_client) && is_string($client->catatan_client)): 
                                    echo nl2br(esc($client->catatan_client));
                                else: 
                                ?>
                                    <span class="text-muted">- Tidak ada catatan -</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Riwayat SPK -->
            <div class="dashboard-card">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-file-contract me-2 text-primary"></i>Riwayat SPK / Tugas Instalasi</h5>
                    <a href="<?= base_url('teknisi/tugas-proyek/spk/create?client_id=' . ($client->id ?? 0)) ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i>Buat SPK
                    </a>
                </div>
                <div class="card-body">
                    <?php if(!empty($spk_list) && is_array($spk_list)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nomor SPK</th>
                                        <th>Judul Pekerjaan</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php foreach($spk_list as $spk): ?>
                                        <?php if(is_object($spk)): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><span class="badge bg-secondary"><?= esc($spk->nomor_spk ?? '-') ?></span></td>
                                            <td><?= esc($spk->judul_pekerjaan ?? '-') ?></td>
                                            <td>
                                                <?php if(!empty($spk->tanggal_mulai)): ?>
                                                    <?= date('d/m/Y', strtotime($spk->tanggal_mulai)) ?>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
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
                                                $class = $statusClass[$spk->status ?? ''] ?? 'badge bg-secondary';
                                                ?>
                                                <span class="<?= $class ?>"><?= esc($spk->status ?? '-') ?></span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar <?= ($spk->progress_persen ?? 0) >= 100 ? 'bg-success' : (($spk->progress_persen ?? 0) >= 50 ? 'bg-info' : 'bg-warning') ?>" 
                                                         role="progressbar" 
                                                         style="width: <?= $spk->progress_persen ?? 0 ?>%;"
                                                         aria-valuenow="<?= $spk->progress_persen ?? 0 ?>" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        <?= $spk->progress_persen ?? 0 ?>%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('teknisi/tugas-proyek/spk/detail/' . ($spk->id ?? 0)) ?>" 
                                                   class="btn btn-sm btn-info" 
                                                   data-bs-toggle="tooltip" 
                                                   title="Detail SPK">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if(($statistik_spk->total ?? 0) > 10): ?>
                            <div class="text-center mt-3">
                                <a href="<?= base_url('teknisi/tugas-proyek/spk?client_id=' . ($client->id ?? 0)) ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-list me-2"></i>Lihat Semua SPK (<?= $statistik_spk->total ?? 0 ?>)
                                </a>
                            </div>
                        <?php endif; ?>
                        
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-file-contract fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada SPK untuk client ini</p>
                            <a href="<?= base_url('teknisi/tugas-proyek/spk/create?client_id=' . ($client->id ?? 0)) ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>Buat SPK Pertama
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Inisialisasi tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});

// Fungsi hapus client
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
            window.location.href = '<?= base_url("teknisi/tugas-proyek/tambah-client/delete") ?>/' + id;
        }
    });
}
</script>

<style>
/* Custom styles */
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
.avatar-circle {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
}
.avatar-initials {
    color: white;
    font-size: 32px;
    font-weight: 600;
    text-transform: uppercase;
}
.table-sm td {
    padding: 0.5rem 0;
    border: none;
}
.badge {
    padding: 0.5em 0.75em;
    font-size: 0.75rem;
    font-weight: 500;
}
.progress {
    background-color: #eaecf4;
    border-radius: 0.35rem;
    height: 20px;
}
.progress-bar {
    font-size: 0.7rem;
    font-weight: 600;
}
.bg-light {
    background-color: #f8f9fc !important;
}
.fw-semibold {
    font-weight: 500;
}

/* Responsive */
@media (max-width: 768px) {
    .avatar-circle {
        width: 60px;
        height: 60px;
    }
    .avatar-initials {
        font-size: 24px;
    }
}
</style>

<?= $this->include('teknisi/templates/footer') ?>