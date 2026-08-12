<?php
$data = [
    'title'  => 'Detail Permohonan / Izin Karyawan',
    'active' => 'karyawan',
    'user'   => ['name' => session()->get('name') ?? 'Direktur', 'role' => 'direktur']
];

echo view('direktur/templates/header', $data);
echo view('direktur/templates/sidebar', $data);
echo view('direktur/templates/navbar', $data);
?>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= base_url('direktur/karyawan/pengajuan') ?>" class="text-decoration-none text-muted">Permohonan & Izin</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Detail Permohonan</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-file-alt text-primary me-2"></i> Detail Permohonan / Izin (Non-Cuti)</h4>
            <small class="text-muted">Nomor Pengajuan: <strong><?= esc($p['nomor_pengajuan'] ?? 'PGJ-'.$p['id']) ?></strong></small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('direktur/karyawan/pengajuan') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali
            </a>
            <a href="<?= base_url('direktur/karyawan/pengajuan/edit/'.$p['id']) ?>" class="btn btn-warning text-white rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-edit me-1.5"></i> Edit Permohonan
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                <div class="card-header text-white py-3 px-4" style="background: linear-gradient(135deg, #1565c0, #1e88e5);">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title fs-6 fw-bold mb-0"><i class="fas fa-id-card me-2"></i> Permohonan Dari: <?= esc($p['nama_lengkap'] ?? 'Admin/Karyawan') ?></h5>
                        <?php 
                            $statusStr = strtolower($p['status'] ?? 'menunggu');
                            $badgeClass = 'bg-warning text-dark';
                            if ($statusStr === 'disetujui') $badgeClass = 'bg-success text-white';
                            if ($statusStr === 'ditolak') $badgeClass = 'bg-danger text-white';
                        ?>
                        <span class="badge <?= $badgeClass ?> px-3 py-1.5 rounded-pill text-xs fw-bold">
                            <?= strtoupper(esc($p['status'] ?? 'Menunggu')) ?>
                        </span>
                    </div>
                </div>
                <div class="card-body p-4">

                    <!-- Profil Pemohon -->
                    <div class="p-3 rounded-3 bg-light border mb-4">
                        <div class="row g-3 text-sm">
                            <div class="col-md-4">
                                <span class="text-muted d-block text-xs">Nama Pemohon:</span>
                                <strong class="text-dark"><?= esc($p['nama_lengkap'] ?? 'Admin Staff') ?></strong>
                            </div>
                            <div class="col-md-4">
                                <span class="text-muted d-block text-xs">Divisi / Jabatan:</span>
                                <strong class="text-dark"><?= esc($p['divisi'] ?? '-') ?> &bull; <?= esc($p['jabatan'] ?? '-') ?></strong>
                            </div>
                            <div class="col-md-4">
                                <span class="text-muted d-block text-xs">Tanggal Pengajuan:</span>
                                <strong class="text-dark"><?= !empty($p['tanggal_pengajuan']) ? date('d F Y', strtotime($p['tanggal_pengajuan'])) : date('d F Y', strtotime($p['created_at'])) ?></strong>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Permohonan -->
                    <div class="row g-3 mb-4 text-sm">
                        <div class="col-md-6">
                            <label class="form-label text-xs text-muted mb-1">Kategori Permohonan / Izin</label>
                            <div class="p-2.5 rounded-3 bg-white border fw-bold text-primary">
                                <i class="fas fa-tag me-1.5"></i> <?= esc($p['kategori_pengajuan']) ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-xs text-muted mb-1">Rentang Tanggal Pelaksanaan</label>
                            <div class="p-2.5 rounded-3 bg-white border fw-bold text-dark">
                                <i class="far fa-calendar-alt text-danger me-1.5"></i>
                                <?= date('d F Y', strtotime($p['tanggal_mulai'])) ?> 
                                <?php if($p['tanggal_mulai'] !== $p['tanggal_selesai']): ?>
                                    s/d <?= date('d F Y', strtotime($p['tanggal_selesai'])) ?>
                                <?php else: ?>
                                    (1 Hari)
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-xs text-muted mb-1">Judul / Perihal Permohonan</label>
                            <div class="p-2.5 rounded-3 bg-white border fw-bold text-dark fs-6">
                                <?= esc($p['judul_pengajuan']) ?>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-xs text-muted mb-1">Keterangan & Alasan Permohonan</label>
                            <div class="p-3 rounded-3 bg-white border text-dark" style="white-space: pre-line;">
                                <?= esc($p['keterangan']) ?>
                            </div>
                        </div>
                    </div>

                    <!-- Pratinjau Bukti Foto Upload -->
                    <div class="mb-3">
                        <label class="form-label text-xs text-muted mb-2 fw-semibold"><i class="fas fa-image me-1 text-primary"></i> Lampiran Bukti Foto Pendukung:</label>
                        <?php if (!empty($p['bukti_foto'])): ?>
                            <div class="p-3 bg-light rounded-3 border text-center">
                                <a href="<?= base_url($p['bukti_foto']) ?>" target="_blank">
                                    <img src="<?= base_url($p['bukti_foto']) ?>" alt="Bukti Foto" class="img-fluid rounded-3 shadow-sm border" style="max-height: 350px; object-fit: contain;">
                                </a>
                                <div class="mt-2 text-muted text-xs">
                                    <i class="fas fa-search-plus me-1"></i> Klik gambar untuk melihat ukuran penuh
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="p-3 bg-light rounded-3 border text-muted text-center text-xs">
                                <i class="fas fa-exclamation-triangle me-1"></i> Tidak ada lampiran foto bukti.
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
                <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4 d-flex justify-content-between align-items-center">
                    <a href="<?= base_url('direktur/karyawan/pengajuan') ?>" class="btn btn-secondary rounded-pill px-4 font-semibold">Kembali</a>
                    
                    <div class="d-flex gap-2">
                        <a href="<?= base_url('direktur/karyawan/pengajuan/edit/'.$p['id']) ?>" class="btn btn-warning text-white rounded-pill px-3.5 font-semibold shadow-sm">
                            <i class="fas fa-edit me-1.5"></i> Edit Permohonan
                        </a>
                        <?php if($statusStr === 'menunggu'): ?>
                            <form action="<?= base_url('direktur/karyawan/pengajuan/approve/'.$p['id']) ?>" method="POST" class="d-inline">
                                <button type="submit" class="btn btn-success text-white rounded-pill px-4 font-semibold shadow-sm">
                                    <i class="fas fa-check me-1.5"></i> Setujui Permohonan Ini
                                </button>
                            </form>
                            <form action="<?= base_url('direktur/karyawan/pengajuan/reject/'.$p['id']) ?>" method="POST" class="d-inline">
                                <button type="submit" class="btn btn-danger text-white rounded-pill px-4 font-semibold shadow-sm">
                                    <i class="fas fa-times me-1.5"></i> Tolak Permohonan
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('direktur/templates/footer', $data) ?>
