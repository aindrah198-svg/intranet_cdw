<?php
$title = 'Detail Karyawan';
$active = 'karyawan';
$css = [];
$scripts = [];
?>

<?= $this->include('admin/templates/header') ?>
<?= $this->include('admin/templates/sidebar') ?>
<?= $this->include('admin/templates/navbar') ?>

<!-- Main Content -->
<div class="dashboard-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-2" style="color: var(--cdw-dark); font-weight: 600;">
                <i class="fas fa-user-circle me-2"></i>Detail Karyawan
            </h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/karyawan') ?>">Karyawan</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('admin/karyawan/edit/' . $karyawan['id']) ?>" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="<?= base_url('admin/karyawan') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
    
    <div class="row">
        <!-- Kolom Kiri: Foto & Info Utama -->
        <div class="col-lg-4">
            <div class="card mb-4" style="border: 1px solid #eaeaea;">
                <div class="card-body text-center">
                    <!-- Foto -->
                    <div class="mb-3">
                        <?php if (!empty($karyawan['foto'])): ?>
                            <img src="<?= base_url($karyawan['foto']) ?>" alt="Foto" 
                                 style="width: 200px; height: 200px; border-radius: 50%; object-fit: cover; border: 3px solid var(--cdw-blue);">
                        <?php else: ?>
                            <div style="width: 200px; height: 200px; border-radius: 50%; background: linear-gradient(135deg, var(--cdw-blue), #0a58ca); 
                                 display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                <span style="font-size: 4rem; color: white; font-weight: bold;">
                                    <?= strtoupper(substr($karyawan['nama_lengkap'], 0, 1)) ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Nama & NIK -->
                    <h4 class="mb-1"><?= $karyawan['nama_lengkap'] ?></h4>
                    <p class="text-muted mb-2"><?= $karyawan['nik'] ?></p>
                    
                    <!-- Nama Panggilan -->
                    <?php if (!empty($karyawan['nama_panggilan'])): ?>
                        <p class="mb-2">
                            <small class="text-muted">Panggilan:</small> 
                            <strong><?= $karyawan['nama_panggilan'] ?></strong>
                        </p>
                    <?php endif; ?>
                    
                    <!-- Status Badge -->
                    <?php
                    $badgeClass = '';
                    switch ($karyawan['status_karyawan']) {
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
                    <span class="badge <?= $badgeClass ?> badge-custom mb-3">
                        <?= $karyawan['status_karyawan'] ?>
                    </span>
                    
                    <!-- Jabatan & Departemen -->
                    <div class="mb-3">
                        <p class="mb-1">
                            <i class="fas fa-briefcase me-2 text-muted"></i>
                            <?= $karyawan['jabatan'] ?? '-' ?>
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-building me-2 text-muted"></i>
                            <?= $karyawan['departemen'] ?? '-' ?>
                        </p>
                        <?php if (!empty($karyawan['divisi'])): ?>
                            <p class="mb-0">
                                <i class="fas fa-sitemap me-2 text-muted"></i>
                                <?= $karyawan['divisi'] ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- CV Download -->
                    <?php if (!empty($karyawan['cv_path'])): ?>
                        <a href="<?= base_url($karyawan['cv_path']) ?>" target="_blank" class="btn btn-outline-primary w-100 mb-2">
                            <i class="fas fa-file-pdf me-1"></i> Download CV
                        </a>
                    <?php endif; ?>
                    
                    <!-- Tandai Keluar jika masih aktif -->
                    <?php if (empty($karyawan['tanggal_keluar'])): ?>
                        <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#keluarModal">
                            <i class="fas fa-door-open me-1"></i> Tandai Keluar
                        </button>
                    <?php else: ?>
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Karyawan sudah keluar pada <?= date('d/m/Y', strtotime($karyawan['tanggal_keluar'])) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Info Kontak -->
            <div class="card mb-4" style="border: 1px solid #eaeaea;">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-address-card me-2"></i> Kontak</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($karyawan['email'])): ?>
                        <p class="mb-2">
                            <i class="fas fa-envelope me-2 text-muted"></i>
                            <?= $karyawan['email'] ?>
                        </p>
                    <?php endif; ?>
                    
                    <?php if (!empty($karyawan['telepon'])): ?>
                        <p class="mb-2">
                            <i class="fas fa-phone me-2 text-muted"></i>
                            <?= $karyawan['telepon'] ?>
                        </p>
                    <?php endif; ?>
                    
                    <?php if (!empty($karyawan['alamat'])): ?>
                        <p class="mb-0">
                            <i class="fas fa-map-marker-alt me-2 text-muted"></i>
                            <small><?= nl2br($karyawan['alamat']) ?></small>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Kolom Kanan: Detail Lengkap -->
        <div class="col-lg-8">
            <!-- Tab Navigation -->
            <ul class="nav nav-tabs mb-4" id="karyawanTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button">
                        <i class="fas fa-user me-1"></i> Profil
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pekerjaan-tab" data-bs-toggle="tab" data-bs-target="#pekerjaan" type="button">
                        <i class="fas fa-briefcase me-1"></i> Pekerjaan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="administrasi-tab" data-bs-toggle="tab" data-bs-target="#administrasi" type="button">
                        <i class="fas fa-file-alt me-1"></i> Administrasi
                    </button>
                </li>
            </ul>
            
            <!-- Tab Content -->
            <div class="tab-content" id="karyawanTabContent">
                <!-- Tab Profil -->
                <div class="tab-pane fade show active" id="profile" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Jenis Kelamin</label>
                            <p>
                                <i class="fas fa-<?= $karyawan['jenis_kelamin'] == 'L' ? 'male' : 'female' ?> me-2"></i>
                                <?= $karyawan['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Tempat Lahir</label>
                            <p><?= $karyawan['tempat_lahir'] ?? '-' ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Tanggal Lahir</label>
                            <p>
                                <?= !empty($karyawan['tanggal_lahir']) ? date('d/m/Y', strtotime($karyawan['tanggal_lahir'])) : '-' ?>
                                <?php if (!empty($karyawan['tanggal_lahir'])): ?>
                                    <br>
                                    <small class="text-muted">
                                        (<?= date_diff(date_create($karyawan['tanggal_lahir']), date_create('today'))->y ?> tahun)
                                    </small>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Agama</label>
                            <p><?= $karyawan['agama'] ?? '-' ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Status Pernikahan</label>
                            <p><?= $karyawan['status_pernikahan'] ?? '-' ?></p>
                        </div>
                    </div>
                    
                    <!-- Pendidikan -->
                    <h6 class="mb-3"><i class="fas fa-graduation-cap me-2"></i> Pendidikan Terakhir</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Pendidikan</label>
                            <p><?= $karyawan['pendidikan_terakhir'] ?? '-' ?></p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Jurusan</label>
                            <p><?= $karyawan['jurusan'] ?? '-' ?></p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Institusi</label>
                            <p><?= $karyawan['institusi'] ?? '-' ?></p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Tahun Lulus</label>
                            <p><?= $karyawan['tahun_lulus'] ?? '-' ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Tab Pekerjaan -->
                <div class="tab-pane fade" id="pekerjaan" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Tanggal Masuk</label>
                            <p>
                                <?= !empty($karyawan['tanggal_masuk']) ? date('d/m/Y', strtotime($karyawan['tanggal_masuk'])) : '-' ?>
                                <?php if (!empty($karyawan['tanggal_masuk'])): ?>
                                    <br>
                                    <small class="text-muted">
                                        (<?php 
                                        $masuk = new DateTime($karyawan['tanggal_masuk']);
                                        $sekarang = new DateTime();
                                        $selisih = $sekarang->diff($masuk);
                                        echo $selisih->y . ' tahun ' . $selisih->m . ' bulan';
                                        ?>)
                                    </small>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Status Karyawan</label>
                            <p>
                                <span class="badge <?= $badgeClass ?>">
                                    <?= $karyawan['status_karyawan'] ?>
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Jabatan</label>
                            <p><?= $karyawan['jabatan'] ?? '-' ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Departemen</label>
                            <p><?= $karyawan['departemen'] ?? '-' ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Divisi</label>
                            <p><?= $karyawan['divisi'] ?? '-' ?></p>
                        </div>
                        <?php if (!empty($karyawan['tanggal_keluar'])): ?>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Tanggal Keluar</label>
                                <p class="text-danger">
                                    <i class="fas fa-calendar-times me-1"></i>
                                    <?= date('d/m/Y', strtotime($karyawan['tanggal_keluar'])) ?>
                                </p>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label text-muted">Alasan Keluar</label>
                                <p><?= nl2br($karyawan['alasan_keluar'] ?? '-') ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Tab Administrasi -->
                <div class="tab-pane fade" id="administrasi" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">NPWP</label>
                            <p><?= $karyawan['no_npwp'] ?? '-' ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">BPJS Kesehatan</label>
                            <p><?= $karyawan['no_bpjs_kes'] ?? '-' ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">BPJS Ketenagakerjaan</label>
                            <p><?= $karyawan['no_bpjs_tk'] ?? '-' ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Bank</label>
                            <p><?= $karyawan['bank'] ?? '-' ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">No. Rekening</label>
                            <p><?= $karyawan['no_rekening'] ?? '-' ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Nama Rekening</label>
                            <p><?= $karyawan['nama_rekening'] ?? '-' ?></p>
                        </div>
                    </div>
                    
                    <!-- Kontak Darurat -->
                    <h6 class="mb-3"><i class="fas fa-phone-alt me-2"></i> Kontak Darurat</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Nama</label>
                            <p><?= $karyawan['kontak_darurat_nama'] ?? '-' ?></p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Hubungan</label>
                            <p><?= $karyawan['kontak_darurat_hubungan'] ?? '-' ?></p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Telepon</label>
                            <p><?= $karyawan['kontak_darurat_telepon'] ?? '-' ?></p>
                        </div>
                    </div>
                </div>
            </div>
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
            <form action="<?= base_url('admin/karyawan/update-keluar/' . $karyawan['id']) ?>" method="post">
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menandai <strong><?= $karyawan['nama_lengkap'] ?></strong> keluar dari perusahaan?</p>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Keluar <span class="text-danger">*</span></label>
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

<script>
    // Set tanggal keluar default ke hari ini
    document.addEventListener('DOMContentLoaded', function() {
        var today = new Date().toISOString().split('T')[0];
        document.querySelector('#keluarModal input[name="tanggal_keluar"]').value = today;
        
        // Initialize tabs
        var triggerTabList = [].slice.call(document.querySelectorAll('#karyawanTab button'))
        triggerTabList.forEach(function (triggerEl) {
            var tabTrigger = new bootstrap.Tab(triggerEl)
            triggerEl.addEventListener('click', function (event) {
                event.preventDefault()
                tabTrigger.show()
            })
        })
    });
</script>

<?= $this->include('admin/templates/footer') ?>