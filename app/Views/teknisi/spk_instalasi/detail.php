<?= $this->include('teknisi/templates/header') ?>
<?= $this->include('teknisi/templates/sidebar') ?>
<?= $this->include('teknisi/templates/navbar') ?>

<!-- Konten utama -->
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><?= esc($title ?? 'Detail SPK') ?></h4>
            <p class="text-muted mb-0">Informasi lengkap SPK / Tugas Instalasi</p>
        </div>
        <div>
            <a href="<?= base_url('teknisi/tugas-proyek/spk') ?>" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
            <a href="<?= base_url('teknisi/tugas-proyek/spk/edit/' . $spk->id) ?>" class="btn btn-primary me-2">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <button type="button" class="btn btn-danger" onclick="hapusSpk(<?= $spk->id ?>)">
                <i class="fas fa-trash me-2"></i>Hapus
            </button>
        </div>
    </div>
    
    <!-- Status Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="dashboard-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="badge bg-<?php 
                                        if($spk->status == 'Selesai') echo 'success';
                                        elseif($spk->status == 'Dalam Pengerjaan') echo 'primary';
                                        elseif($spk->status == 'Dijadwalkan') echo 'info';
                                        elseif($spk->status == 'Ditunda') echo 'warning';
                                        elseif($spk->status == 'Dibatalkan') echo 'danger';
                                        else echo 'secondary';
                                    ?> p-3 fs-6">
                                        <?= esc($spk->status) ?>
                                    </span>
                                </div>
                                <div>
                                    <h5 class="mb-1"><?= esc($spk->judul_pekerjaan) ?></h5>
                                    <p class="text-muted mb-0"><?= esc($spk->nomor_spk) ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex justify-content-end gap-2">
                                <?php if($spk->status != 'Selesai' && $spk->status != 'Dibatalkan'): ?>
                                    <button type="button" class="btn btn-success" onclick="selesaikanSpk(<?= $spk->id ?>)">
                                        <i class="fas fa-check me-2"></i>Selesaikan
                                    </button>
                                <?php endif; ?>
                                
                                <?php if($spk->status != 'Ditunda' && $spk->status != 'Dibatalkan' && $spk->status != 'Selesai'): ?>
                                    <button type="button" class="btn btn-warning" onclick="tundaSpk(<?= $spk->id ?>)">
                                        <i class="fas fa-pause me-2"></i>Tunda
                                    </button>
                                <?php endif; ?>
                                
                                <?php if($spk->status != 'Dibatalkan' && $spk->status != 'Selesai'): ?>
                                    <button type="button" class="btn btn-danger" onclick="batalkanSpk(<?= $spk->id ?>)">
                                        <i class="fas fa-times me-2"></i>Batalkan
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Progress Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="dashboard-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="fw-bold">Progress Pekerjaan</h6>
                        <span class="badge bg-primary"><?= $spk->progress_persen ?>%</span>
                    </div>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
                             role="progressbar" 
                             style="width: <?= $spk->progress_persen ?>%;" 
                             aria-valuenow="<?= $spk->progress_persen ?>" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            <?= $spk->progress_persen ?>%
                        </div>
                    </div>
                    
                    <!-- Slider Progress (AJAX) -->
                    <div class="mt-3">
                        <label class="form-label">Update Progress</label>
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <input type="range" class="form-range" id="progressSlider" 
                                       min="0" max="100" step="5" value="<?= $spk->progress_persen ?>"
                                       onchange="updateProgress(<?= $spk->id ?>, this.value)">
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control" id="progressValue" 
                                       value="<?= $spk->progress_persen ?>" min="0" max="100" step="5"
                                       onchange="updateProgress(<?= $spk->id ?>, this.value)">
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary w-100" onclick="updateProgress(<?= $spk->id ?>, $('#progressValue').val())">
                                    <i class="fas fa-sync-alt me-2"></i>Update
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Informasi Utama -->
    <div class="row">
        <!-- Informasi Dasar -->
        <div class="col-md-6 mb-4">
            <div class="dashboard-card h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi Dasar</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Nomor SPK</th>
                            <td width="10%">:</td>
                            <td><strong><?= esc($spk->nomor_spk) ?></strong></td>
                        </tr>
                        <tr>
                            <th>Judul Pekerjaan</th>
                            <td>:</td>
                            <td><?= esc($spk->judul_pekerjaan) ?></td>
                        </tr>
                        <tr>
                            <th>Kategori</th>
                            <td>:</td>
                            <td>
                                <span class="badge bg-info"><?= esc($spk->kategori_pekerjaan) ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th>Prioritas</th>
                            <td>:</td>
                            <td>
                                <?php
                                $prioritasClass = '';
                                if($spk->prioritas == 'Urgent') $prioritasClass = 'danger';
                                elseif($spk->prioritas == 'Tinggi') $prioritasClass = 'warning';
                                elseif($spk->prioritas == 'Normal') $prioritasClass = 'success';
                                else $prioritasClass = 'secondary';
                                ?>
                                <span class="badge bg-<?= $prioritasClass ?>"><?= esc($spk->prioritas) ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th>Tanggal Mulai</th>
                            <td>:</td>
                            <td><?= date('d-m-Y', strtotime($spk->tanggal_mulai)) ?></td>
                        </tr>
                        <tr>
                            <th>Target Selesai</th>
                            <td>:</td>
                            <td>
                                <?= $spk->target_selesai ? date('d-m-Y', strtotime($spk->target_selesai)) : '-' ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Tanggal Selesai</th>
                            <td>:</td>
                            <td>
                                <?= $spk->tanggal_selesai_aktual ? date('d-m-Y', strtotime($spk->tanggal_selesai_aktual)) : '-' ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Informasi Client -->
        <div class="col-md-6 mb-4">
            <div class="dashboard-card h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-building me-2 text-primary"></i>Informasi Client</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Nama Client</th>
                            <td width="10%">:</td>
                            <td><strong><?= esc($spk->client_nama) ?></strong></td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>:</td>
                            <td><?= esc($spk->client_alamat) ?: '-' ?></td>
                        </tr>
                        <tr>
                            <th>Kontak</th>
                            <td>:</td>
                            <td><?= esc($spk->client_kontak) ?: '-' ?></td>
                        </tr>
                        <tr>
                            <th>Catatan Client</th>
                            <td>:</td>
                            <td><?= esc($spk->catatan_client) ?: '-' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Detail Pekerjaan -->
    <div class="row">
        <!-- Deskripsi & Lokasi -->
        <div class="col-md-6 mb-4">
            <div class="dashboard-card h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-file-alt me-2 text-primary"></i>Deskripsi & Lokasi</h6>
                </div>
                <div class="card-body">
                    <h6 class="fw-semibold">Deskripsi Pekerjaan:</h6>
                    <?php 
                    $deskripsi = is_string($spk->deskripsi) ? $spk->deskripsi : '';
                    if(!empty($deskripsi)): 
                    ?>
                        <p class="text-muted"><?= nl2br(esc($deskripsi)) ?></p>
                    <?php else: ?>
                        <p class="text-muted"><em>Tidak ada deskripsi</em></p>
                    <?php endif; ?>
                    
                    <h6 class="fw-semibold mt-3">Lokasi Pekerjaan:</h6>
                    <?php 
                    $lokasi = is_string($spk->lokasi) ? $spk->lokasi : '';
                    if(!empty($lokasi)): 
                    ?>
                        <p class="text-muted"><?= esc($lokasi) ?></p>
                    <?php else: ?>
                        <p class="text-muted"><em>Tidak ada informasi lokasi</em></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
   <!-- Tim Teknisi & Manager -->
<div class="col-md-6 mb-4">
    <div class="dashboard-card h-100">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold"><i class="fas fa-users me-2 text-primary"></i>Tim Pelaksana</h6>
        </div>
        <div class="card-body">
            <h6 class="fw-semibold">Project Manager:</h6>
            <?php if(isset($project_manager) && !empty($project_manager)): ?>
                <?php if(is_object($project_manager)): ?>
                    <p class="text-primary">
                        <i class="fas fa-user-tie me-2"></i><?= esc($project_manager->nama_lengkap ?? '-') ?> 
                        <small class="text-muted">(<?= esc($project_manager->jabatan ?? '-') ?>)</small>
                    </p>
                <?php elseif(is_array($project_manager)): ?>
                    <p class="text-primary">
                        <i class="fas fa-user-tie me-2"></i><?= esc($project_manager['nama_lengkap'] ?? '-') ?> 
                        <small class="text-muted">(<?= esc($project_manager['jabatan'] ?? '-') ?>)</small>
                    </p>
                <?php endif; ?>
            <?php else: ?>
                <p class="text-muted"><em>Belum ditentukan</em></p>
            <?php endif; ?>
            
            <h6 class="fw-semibold mt-3">Tim Teknisi:</h6>
            <?php if(!empty($tim_teknisi)): ?>
                <ul class="list-group">
                    <?php foreach($tim_teknisi as $teknisi): ?>
                        <?php if(is_object($teknisi)): ?>
                            <li class="list-group-item border-0 ps-0">
                                <i class="fas fa-user-cog me-2 text-info"></i>
                                <?= esc($teknisi->nama_lengkap ?? '-') ?> 
                                <small class="text-muted">(<?= esc($teknisi->nik ?? '-') ?>)</small>
                            </li>
                        <?php elseif(is_array($teknisi)): ?>
                            <li class="list-group-item border-0 ps-0">
                                <i class="fas fa-user-cog me-2 text-info"></i>
                                <?= esc($teknisi['nama_lengkap'] ?? '-') ?> 
                                <small class="text-muted">(<?= esc($teknisi['nik'] ?? '-') ?>)</small>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted"><em>Belum ada teknisi ditugaskan</em></p>
            <?php endif; ?>
        </div>
    </div>
</div>
    
    <!-- Item Pekerjaan -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="dashboard-card">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-list-check me-2 text-primary"></i>Item Pekerjaan</h6>
                    <div>
                        <span class="badge bg-info me-2">Total Item: <?= count($items) ?></span>
                        <span class="badge bg-success">Selesai: <?= $statistik_items->selesai ?? 0 ?></span>
                        <span class="badge bg-warning">Pending: <?= $statistik_items->pending ?? 0 ?></span>
                        <span class="badge bg-danger">Bermasalah: <?= $statistik_items->bermasalah ?? 0 ?></span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="20%">Nama Item</th>
                                    <th width="25%">Deskripsi</th>
                                    <th width="8%">Qty</th>
                                    <th width="8%">Satuan</th>
                                    <th width="12%">Harga (Rp)</th>
                                    <th width="12%">Subtotal (Rp)</th>
                                    <th width="10%">Status</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($items)): ?>
                                    <?php $no = 1; ?>
                                    <?php foreach($items as $item): ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td><?= esc($item->nama_item) ?></td>
                                            <td><?= esc($item->deskripsi) ?: '-' ?></td>
                                            <td class="text-end"><?= number_format($item->qty, 2) ?></td>
                                            <td><?= esc($item->satuan) ?></td>
                                            <td class="text-end"><?= number_format($item->harga, 0, ',', '.') ?></td>
                                            <td class="text-end fw-bold"><?= number_format($item->total, 0, ',', '.') ?></td>
                                            <td>
                                                <select class="form-select form-select-sm item-status" 
                                                        data-item-id="<?= $item->id ?>"
                                                        onchange="updateItemStatus(<?= $item->id ?>, this.value)">
                                                    <option value="Pending" <?= $item->status == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                    <option value="Selesai" <?= $item->status == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                                                    <option value="Bermasalah" <?= $item->status == 'Bermasalah' ? 'selected' : '' ?>>Bermasalah</option>
                                                </select>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info" onclick="detailItem(<?= $item->id ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="fas fa-info-circle me-2"></i>Tidak ada item pekerjaan
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot class="table-secondary">
                                <tr>
                                    <th colspan="6" class="text-end">Total Estimasi Biaya:</th>
                                    <th class="text-end fw-bold text-primary">
                                        Rp <?= number_format($total_items, 0, ',', '.') ?>
                                    </th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Informasi Tambahan -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="dashboard-card h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-paperclip me-2 text-primary"></i>Dokumen & File</h6>
                </div>
                <div class="card-body">
                    <?php if($spk->dokumen_pendukung): ?>
                        <div class="mb-3">
                            <label class="fw-semibold">Dokumen Pendukung:</label>
                            <div class="mt-2">
                                <a href="<?= base_url($spk->dokumen_pendukung) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-file-pdf me-2"></i>Lihat Dokumen
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($spk->dokumentasi): ?>
                        <div class="mb-3">
                            <label class="fw-semibold">Dokumentasi:</label>
                            <div class="mt-2">
                                <img src="<?= base_url($spk->dokumentasi) ?>" alt="Dokumentasi" class="img-thumbnail" style="max-height: 200px;">
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(!$spk->dokumen_pendukung && !$spk->dokumentasi): ?>
                        <p class="text-muted text-center py-3">
                            <i class="fas fa-folder-open me-2"></i>Tidak ada file terlampir
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="dashboard-card h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-sticky-note me-2 text-primary"></i>Catatan & Laporan</h6>
                </div>
                <div class="card-body">
                    <h6 class="fw-semibold">Catatan:</h6>
                    <?php 
                    $catatan = is_string($spk->catatan) ? $spk->catatan : '';
                    if(!empty($catatan)): 
                    ?>
                        <p class="text-muted"><?= nl2br(esc($catatan)) ?></p>
                    <?php else: ?>
                        <p class="text-muted"><em>Tidak ada catatan</em></p>
                    <?php endif; ?>
                    
                    <h6 class="fw-semibold mt-3">Laporan:</h6>
                    <?php 
                    $laporan = is_string($spk->laporan) ? $spk->laporan : '';
                    if(!empty($laporan)): 
                    ?>
                        <p class="text-muted"><?= nl2br(esc($laporan)) ?></p>
                    <?php else: ?>
                        <p class="text-muted"><em>Tidak ada laporan</em></p>
                    <?php endif; ?>
                    
                    <?php 
                    $laporan_hasil = is_string($spk->laporan_hasil) ? $spk->laporan_hasil : '';
                    if(!empty($laporan_hasil)): 
                    ?>
                        <h6 class="fw-semibold mt-3">Laporan Hasil:</h6>
                        <p class="text-muted"><?= nl2br(esc($laporan_hasil)) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
 <!-- Informasi Pembuat -->
<div class="row mb-4">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="fas fa-clock me-2 text-primary"></i>Informasi Audit</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">Dibuat Oleh</th>
                                <td width="5%">:</td>
                                <td>
                                    <?php 
                                    if(isset($pembuat) && !empty($pembuat)):
                                        if(is_object($pembuat)):
                                            echo esc($pembuat->name ?? '-');
                                        elseif(is_array($pembuat)):
                                            echo esc($pembuat['name'] ?? '-');
                                        endif;
                                    else:
                                        echo '-';
                                    endif;
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Dibuat Tanggal</th>
                                <td>:</td>
                                <td><?= $spk->dibuat_tanggal ? date('d-m-Y H:i:s', strtotime($spk->dibuat_tanggal)) : '-' ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">Diperbarui Oleh</th>
                                <td width="5%">:</td>
                                <td>
                                    <?php 
                                    if(isset($spk->diperbarui_oleh_nama) && !empty($spk->diperbarui_oleh_nama)):
                                        echo esc($spk->diperbarui_oleh_nama);
                                    else:
                                        echo '-';
                                    endif;
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Diperbarui Tanggal</th>
                                <td>:</td>
                                <td><?= $spk->diperbarui_tanggal ? date('d-m-Y H:i:s', strtotime($spk->diperbarui_tanggal)) : '-' ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    
  <!-- Log Aktivitas -->
<div class="row mb-4">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="fas fa-history me-2 text-primary"></i>Log Aktivitas</h6>
            </div>
            <div class="card-body">
                <?php if(!empty($logs)): ?>
                    <div class="timeline">
                        <?php foreach($logs as $log): ?>
                            <div class="timeline-item">
                                <div class="d-flex">
                                    <div class="timeline-icon bg-primary text-white">
                                        <i class="fas fa-circle"></i>
                                    </div>
                                    <div class="timeline-content ms-3 pb-3">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="fw-semibold mb-1"><?= esc($log->aktivitas ?? '') ?></h6>
                                            <small class="text-muted"><?= isset($log->created_at) ? date('d-m-Y H:i:s', strtotime($log->created_at)) : '-' ?></small>
                                        </div>
                                        <?php 
                                        $keterangan = isset($log->keterangan) && is_string($log->keterangan) ? $log->keterangan : '';
                                        ?>
                                        <p class="text-muted mb-1"><?= esc($keterangan) ?: '-' ?></p>
                                        <small class="text-primary">Oleh: <?= esc($log->user_nama ?? 'System') ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-3">
                        <i class="fas fa-history me-2"></i>Belum ada aktivitas
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Modal untuk Selesaikan SPK -->
<div class="modal fade" id="modalSelesaikan" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Selesaikan SPK</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formSelesaikan" action="<?= base_url('teknisi/tugas-proyek/spk/selesaikan/') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="spk_id" id="selesaikan_spk_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Laporan Hasil</label>
                        <textarea name="laporan_hasil" class="form-control" rows="5" placeholder="Masukkan laporan hasil pekerjaan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Selesaikan SPK</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal untuk Tunda SPK -->
<div class="modal fade" id="modalTunda" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tunda SPK</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formTunda" action="<?= base_url('teknisi/tugas-proyek/spk/tunda/') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="spk_id" id="tunda_spk_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Alasan Penundaan</label>
                        <textarea name="alasan" class="form-control" rows="4" placeholder="Masukkan alasan penundaan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Tunda SPK</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal untuk Batalkan SPK -->
<div class="modal fade" id="modalBatalkan" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Batalkan SPK</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formBatalkan" action="<?= base_url('teknisi/tugas-proyek/spk/batalkan/') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="spk_id" id="batalkan_spk_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Alasan Pembatalan</label>
                        <textarea name="alasan" class="form-control" rows="4" placeholder="Masukkan alasan pembatalan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Batalkan SPK</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SweetAlert2 & jQuery -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Sinkronisasi slider dengan input number
    $('#progressSlider').on('input', function() {
        $('#progressValue').val(this.value);
    });
    
    $('#progressValue').on('input', function() {
        $('#progressSlider').val(this.value);
    });
});

// Update Progress via AJAX
function updateProgress(id, progress) {
    Swal.fire({
        title: 'Update Progress',
        text: 'Apakah Anda yakin ingin mengupdate progress menjadi ' + progress + '%?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Update',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('teknisi/tugas-proyek/spk/updateProgress') ?>',
                type: 'POST',
                data: {
                    id: id,
                    progress: progress,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: 'json',
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
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan komunikasi dengan server'
                    });
                }
            });
        } else {
            // Reset ke nilai awal
            $('#progressSlider').val(<?= $spk->progress_persen ?>);
            $('#progressValue').val(<?= $spk->progress_persen ?>);
        }
    });
}

// Update status item via AJAX
function updateItemStatus(itemId, status) {
    Swal.fire({
        title: 'Update Status Item',
        text: 'Ubah status item menjadi ' + status + '?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Update',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('teknisi/tugas-proyek/spk/updateItemStatus') ?>',
                type: 'POST',
                data: {
                    item_id: itemId,
                    status: status,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: 'json',
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
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan komunikasi dengan server'
                    });
                }
            });
        } else {
            // Reset ke nilai awal
            location.reload();
        }
    });
}

// Detail item
function detailItem(itemId) {
    // Implementasi modal detail item atau redirect ke halaman detail item
    Swal.fire({
        icon: 'info',
        title: 'Info',
        text: 'Fitur detail item dalam pengembangan'
    });
}

// Hapus SPK
function hapusSpk(id) {
    Swal.fire({
        title: 'Hapus SPK',
        text: 'Apakah Anda yakin ingin menghapus SPK ini? Data yang dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?= base_url('teknisi/tugas-proyek/spk/delete/') ?>/' + id;
        }
    });
}

// Selesaikan SPK
function selesaikanSpk(id) {
    $('#selesaikan_spk_id').val(id);
    $('#modalSelesaikan').modal('show');
}

// Tunda SPK
function tundaSpk(id) {
    $('#tunda_spk_id').val(id);
    $('#modalTunda').modal('show');
}

// Batalkan SPK
function batalkanSpk(id) {
    $('#batalkan_spk_id').val(id);
    $('#modalBatalkan').modal('show');
}

// Tampilkan pesan dari session
<?php if(session()->getFlashdata('success')): ?>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '<?= session()->getFlashdata('success') ?>',
        showConfirmButton: false,
        timer: 1500
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
</script>

<style>
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

.table-borderless th,
.table-borderless td {
    border: none;
    padding: 0.5rem 0;
}

.table-borderless th {
    font-weight: 500;
    color: #5a5c69;
}

.badge {
    padding: 0.5rem 1rem;
}

/* Timeline */
.timeline {
    position: relative;
    padding-left: 20px;
}

.timeline-item {
    position: relative;
    padding-bottom: 10px;
}

.timeline-icon {
    position: absolute;
    left: -20px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.timeline-icon i {
    font-size: 10px;
}

.timeline-content {
    border-left: 2px solid #eaeaea;
    padding-left: 20px;
    margin-left: 10px;
}

.timeline-item:last-child .timeline-content {
    border-left: none;
}

/* Progress Bar */
.progress {
    border-radius: 10px;
    background-color: #eaeaea;
}

.progress-bar {
    border-radius: 10px;
}

/* Form Range */
.form-range::-webkit-slider-thumb {
    background: #4e73df;
}

.form-range::-moz-range-thumb {
    background: #4e73df;
}

.form-range::-ms-thumb {
    background: #4e73df;
}
</style>

<?= $this->include('teknisi/templates/footer') ?>