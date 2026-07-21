<?= $this->include('teknisi/templates/header') ?>
<?= $this->include('teknisi/templates/sidebar') ?>
<?= $this->include('teknisi/templates/navbar') ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<!-- Moment.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<style>
    .page-header {
        background: linear-gradient(135deg, #4361ee, #3a0ca3);
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 25px;
        color: white;
    }

    .detail-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }

    .detail-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
    }

    .detail-title i {
        color: #4361ee;
        margin-right: 10px;
    }

    .info-label {
        font-size: 13px;
        color: #6c757d;
        margin-bottom: 5px;
    }

    .info-value {
        font-size: 16px;
        font-weight: 600;
        color: #2b2d42;
        margin-bottom: 15px;
    }

    .priority-badge {
        padding: 5px 15px;
        border-radius: 30px;
        font-size: 13px;
        font-weight: 600;
    }

    .priority-urgent { background: #ef476f; color: white; }
    .priority-tinggi { background: #ffb703; color: white; }
    .priority-normal { background: #4361ee; color: white; }
    .priority-rendah { background: #6c757d; color: white; }

    .status-badge {
        padding: 5px 15px;
        border-radius: 30px;
        font-size: 13px;
        font-weight: 600;
    }

    .status-selesai { background: #06d6a0; color: white; }
    .status-proses { background: #4361ee; color: white; }
    .status-terlambat { background: #ef476f; color: white; }
    .status-dijadwalkan { background: #4cc9f0; color: white; }
    .status-ditunda { background: #ffb703; color: white; }
    .status-dibatalkan { background: #6c757d; color: white; }

    .progress-large {
        height: 20px;
        border-radius: 10px;
        background-color: #f0f0f0;
    }

    .progress-bar-large {
        border-radius: 10px;
        font-size: 12px;
        line-height: 20px;
    }

    .timeline-container {
        position: relative;
        padding: 20px 0;
        margin: 30px 0;
    }

    .timeline-track {
        position: relative;
        height: 4px;
        background: #e9ecef;
        border-radius: 2px;
        margin: 30px 0;
    }

    .timeline-progress {
        position: absolute;
        height: 4px;
        background: #4361ee;
        border-radius: 2px;
        width: 0%;
    }

    .timeline-marker {
        position: absolute;
        top: -10px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #4361ee;
        border: 2px solid white;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .timeline-label {
        position: absolute;
        top: -30px;
        transform: translateX(-50%);
        font-size: 12px;
        color: #6c757d;
        white-space: nowrap;
    }

    .timeline-start {
        left: 0;
        background: #28a745;
    }

    .timeline-end {
        right: 0;
        background: #dc3545;
    }

    .timeline-now {
        left: 50%;
        background: #ffc107;
    }

    .stat-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: 700;
        margin: 0 auto 15px;
    }

    .stat-circle-success {
        background: rgba(6, 214, 160, 0.1);
        color: #06d6a0;
        border: 3px solid #06d6a0;
    }

    .stat-circle-primary {
        background: rgba(67, 97, 238, 0.1);
        color: #4361ee;
        border: 3px solid #4361ee;
    }

    .stat-circle-warning {
        background: rgba(255, 183, 3, 0.1);
        color: #ffb703;
        border: 3px solid #ffb703;
    }

    .stat-circle-danger {
        background: rgba(239, 71, 111, 0.1);
        color: #ef476f;
        border: 3px solid #ef476f;
    }

    .btn-back {
        background: white;
        color: #4361ee;
        border: 2px solid #4361ee;
        padding: 10px 25px;
        border-radius: 10px;
        font-weight: 500;
        transition: all 0.3s;
    }

    .btn-back:hover {
        background: #4361ee;
        color: white;
    }

    .timeline-stats {
        display: flex;
        justify-content: space-around;
        margin: 30px 0;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 15px;
    }

    .stat-item {
        text-align: center;
    }

    .stat-item .label {
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 5px;
    }

    .stat-item .value {
        font-size: 24px;
        font-weight: 700;
        color: #2b2d42;
    }

    .stat-item .unit {
        font-size: 12px;
        color: #6c757d;
        margin-left: 3px;
    }
</style>

<div class="container-fluid px-4">
    <!-- Header dengan tombol back -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-info-circle me-3"></i>Detail Proyek SPK</h2>
                <p><?= esc($spk->nomor_spk ?? '') ?> - <?= esc($spk->judul_pekerjaan ?? '') ?></p>
            </div>
            <div>
                <a href="<?= base_url('teknisi/tugas-proyek/timeline') ?>" class="btn-back">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Informasi Utama -->
    <div class="row">
        <div class="col-md-8">
            <!-- Detail Proyek -->
            <div class="detail-card">
                <div class="detail-title">
                    <i class="fas fa-clipboard-list"></i>Informasi Proyek
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-label">Nomor SPK</div>
                        <div class="info-value"><?= esc($spk->nomor_spk ?? '-') ?></div>
                        
                        <div class="info-label">Judul Pekerjaan</div>
                        <div class="info-value"><?= esc($spk->judul_pekerjaan ?? '-') ?></div>
                        
                        <div class="info-label">Deskripsi</div>
                        <div class="info-value"><?= esc($spk->deskripsi ?? '-') ?></div>
                        
                        <div class="info-label">Lokasi</div>
                        <div class="info-value"><?= esc($spk->lokasi ?? '-') ?></div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="info-label">Prioritas</div>
                        <div class="info-value">
                            <?php
                            $priority_class = match($spk->prioritas ?? 'Normal') {
                                'Urgent' => 'priority-urgent',
                                'Tinggi' => 'priority-tinggi',
                                'Normal' => 'priority-normal',
                                'Rendah' => 'priority-rendah',
                                default => 'priority-normal'
                            };
                            ?>
                            <span class="priority-badge <?= $priority_class ?>">
                                <?= esc($spk->prioritas ?? 'Normal') ?>
                            </span>
                        </div>
                        
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            <?php
                            $status = $spk->status ?? 'Draft';
                            $status_class = match($status) {
                                'Selesai' => 'status-selesai',
                                'Dalam Pengerjaan' => 'status-proses',
                                'Dijadwalkan' => 'status-dijadwalkan',
                                'Ditunda' => 'status-ditunda',
                                'Dibatalkan' => 'status-dibatalkan',
                                default => 'status-draft'
                            };
                            
                            // Cek overdue
                            if($status != 'Selesai' && $status != 'Dibatalkan' && !empty($spk->target_selesai) && $spk->target_selesai < date('Y-m-d')) {
                                $status_class = 'status-terlambat';
                                $status = 'Terlambat';
                            }
                            ?>
                            <span class="status-badge <?= $status_class ?>">
                                <?= $status ?>
                            </span>
                        </div>
                        
                        <div class="info-label">Kategori Pekerjaan</div>
                        <div class="info-value"><?= esc($spk->kategori_pekerjaan ?? '-') ?></div>
                        
                        <div class="info-label">Project Manager</div>
                        <div class="info-value"><?= esc($spk->project_manager_nama ?? '-') ?></div>
                    </div>
                </div>
            </div>

            <!-- Timeline Progress -->
            <div class="detail-card">
                <div class="detail-title">
                    <i class="fas fa-clock"></i>Timeline Proyek
                </div>

                <?php
                $start = strtotime($spk->tanggal_mulai);
                $end = !empty($spk->tanggal_selesai_aktual) ? strtotime($spk->tanggal_selesai_aktual) : strtotime($spk->target_selesai);
                $now = time();
                
                // Hitung progress waktu
                $total_duration = $end - $start;
                $elapsed = $now - $start;
                $time_progress = ($total_duration > 0) ? min(100, max(0, ($elapsed / $total_duration) * 100)) : 0;
                
                // Hitung sisa hari
                $remaining_days = 0;
                if($spk->target_selesai && $status != 'Selesai' && $status != 'Dibatalkan') {
                    $target = new DateTime($spk->target_selesai);
                    $today = new DateTime();
                    $remaining_days = $today->diff($target)->days;
                    $is_overdue = $today > $target;
                }
                ?>

                <!-- Progress Bar Waktu -->
                <div class="timeline-container">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted"><?= date('d M Y', $start) ?></span>
                        <span class="text-muted"><?= date('d M Y', $end) ?></span>
                    </div>
                    
                    <div class="timeline-track">
                        <div class="timeline-progress" style="width: <?= $time_progress ?>%"></div>
                        <div class="timeline-marker timeline-start" style="left: 0;" title="Mulai"></div>
                        <div class="timeline-marker timeline-end" style="left: 100%;" title="Target Selesai"></div>
                        <div class="timeline-marker timeline-now" style="left: <?= $time_progress ?>%;" title="Sekarang"></div>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-3">
                        <div>
                            <small class="text-muted">Mulai</small><br>
                            <strong><?= date('d/m/Y', $start) ?></strong>
                        </div>
                        <div class="text-center">
                            <small class="text-muted">Progress Waktu</small><br>
                            <strong><?= round($time_progress) ?>%</strong>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">Target Selesai</small><br>
                            <strong><?= date('d/m/Y', $end) ?></strong>
                        </div>
                    </div>
                </div>

                <!-- Statistik Timeline -->
                <div class="timeline-stats">
                    <div class="stat-item">
                        <div class="label">Durasi Total</div>
                        <div class="value"><?= round($total_duration / 86400) ?> <span class="unit">hari</span></div>
                    </div>
                    <div class="stat-item">
                        <div class="label">Hari Berjalan</div>
                        <div class="value"><?= max(0, round($elapsed / 86400)) ?> <span class="unit">hari</span></div>
                    </div>
                    <div class="stat-item">
                        <div class="label">Sisa Hari</div>
                        <div class="value <?= ($remaining_days < 0) ? 'text-danger' : '' ?>">
                            <?= abs($remaining_days) ?> <span class="unit">hari</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Pekerjaan -->
            <div class="detail-card">
                <div class="detail-title">
                    <i class="fas fa-tasks"></i>Progress Pekerjaan
                </div>

                <div class="row align-items-center">
                    <div class="col-md-3 text-center">
                        <div class="stat-circle <?= ($spk->progress_persen ?? 0) >= 100 ? 'stat-circle-success' : (($spk->progress_persen ?? 0) >= 50 ? 'stat-circle-primary' : 'stat-circle-warning') ?>">
                            <?= $spk->progress_persen ?? 0 ?>%
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="progress-large">
                            <div class="progress-bar progress-bar-large <?= ($spk->progress_persen ?? 0) >= 100 ? 'bg-success' : 'bg-primary' ?>" 
                                 style="width: <?= $spk->progress_persen ?? 0 ?>%">
                                <?= $spk->progress_persen ?? 0 ?>%
                            </div>
                        </div>
                        
                        <?php if($spk->progress_persen >= 100): ?>
                            <div class="alert alert-success mt-3 mb-0">
                                <i class="fas fa-check-circle me-2"></i>Proyek telah selesai pada <?= date('d/m/Y', strtotime($spk->tanggal_selesai_aktual)) ?>
                            </div>
                        <?php elseif($is_overdue ?? false): ?>
                            <div class="alert alert-danger mt-3 mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>Proyek terlambat dari target selesai
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Informasi Client -->
            <div class="detail-card">
                <div class="detail-title">
                    <i class="fas fa-building"></i>Informasi Client
                </div>

                <div class="text-center mb-3">
                    <div class="stat-circle stat-circle-primary mx-auto" style="width: 60px; height: 60px; font-size: 24px;">
                        <i class="fas fa-user"></i>
                    </div>
                </div>

                <div class="info-label">Nama Client</div>
                <div class="info-value"><?= esc($spk->client_nama ?? '-') ?></div>
                
                <div class="info-label">Alamat</div>
                <div class="info-value"><?= esc($spk->client_alamat ?? '-') ?></div>
                
                <div class="info-label">Kontak</div>
                <div class="info-value"><?= esc($spk->client_kontak ?? '-') ?></div>
                
                <div class="info-label">Catatan Client</div>
                <div class="info-value"><?= esc($spk->catatan_client ?? '-') ?></div>
            </div>

            <!-- Informasi Biaya -->
            <div class="detail-card">
                <div class="detail-title">
                    <i class="fas fa-money-bill-wave"></i>Informasi Biaya
                </div>

                <div class="info-label">Estimasi Biaya</div>
                <div class="info-value">Rp <?= number_format($spk->estimasi_biaya ?? 0, 0, ',', '.') ?></div>
                
                <div class="info-label">Biaya Aktual</div>
                <div class="info-value">Rp <?= number_format($spk->biaya_aktual ?? 0, 0, ',', '.') ?></div>
                
                <?php if(($spk->biaya_aktual ?? 0) > 0): ?>
                    <div class="info-label">Selisih</div>
                    <div class="info-value <?= ($spk->biaya_aktual > $spk->estimasi_biaya) ? 'text-danger' : 'text-success' ?>">
                        Rp <?= number_format(abs(($spk->biaya_aktual ?? 0) - ($spk->estimasi_biaya ?? 0)), 0, ',', '.') ?>
                        <?= ($spk->biaya_aktual > $spk->estimasi_biaya) ? '(Over Budget)' : '(Under Budget)' ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tim Teknisi -->
            <?php if(!empty($tim_teknisi)): ?>
            <div class="detail-card">
                <div class="detail-title">
                    <i class="fas fa-users"></i>Tim Teknisi
                </div>

                <?php foreach($tim_teknisi as $teknisi): ?>
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-circle-primary" style="width: 40px; height: 40px; font-size: 16px; margin-right: 10px;">
                            <i class="fas fa-user-cog"></i>
                        </div>
                        <div>
                            <div class="fw-bold"><?= esc($teknisi->nama_lengkap ?? '') ?></div>
                            <small class="text-muted"><?= esc($teknisi->jabatan ?? 'Teknisi') ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

  <!-- Catatan dan Laporan -->
<?php if(!empty($spk->catatan) || !empty($spk->laporan_hasil)): ?>
<div class="row mt-3">
    <div class="col-md-12">
        <div class="detail-card">
            <div class="detail-title">
                <i class="fas fa-sticky-note"></i>Catatan & Laporan
            </div>

            <?php if(!empty($spk->catatan)): ?>
                <div class="info-label">Catatan</div>
                <div class="info-value mb-3">
                    <?php 
                    // Pastikan catatan adalah string
                    $catatan = is_string($spk->catatan) ? $spk->catatan : '';
                    echo nl2br(esc($catatan)); 
                    ?>
                </div>
            <?php endif; ?>

            <?php if(!empty($spk->laporan_hasil)): ?>
                <div class="info-label">Laporan Hasil</div>
                <div class="info-value">
                    <?php 
                    // Pastikan laporan_hasil adalah string
                    $laporan = is_string($spk->laporan_hasil) ? $spk->laporan_hasil : '';
                    echo nl2br(esc($laporan)); 
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>
</div>

<script>
$(document).ready(function() {
    // Inisialisasi tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>

<?= $this->include('teknisi/templates/footer') ?>