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

    .filter-card {
        background: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }

    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        margin-bottom: 25px;
    }

    .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: #2b2d42;
    }

    .chart-container {
        background: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }

    .chart-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
    }

    .timeline-bar {
        position: relative;
        height: 40px;
        background: #f0f0f0;
        border-radius: 8px;
        margin: 10px 0;
        overflow: hidden;
    }

    .timeline-progress {
        position: absolute;
        height: 100%;
        background: #4361ee;
        border-radius: 8px;
        opacity: 0.8;
    }

    .priority-badge {
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .priority-urgent { background: #ef476f; color: white; }
    .priority-tinggi { background: #ffb703; color: white; }
    .priority-normal { background: #4361ee; color: white; }
    .priority-rendah { background: #6c757d; color: white; }

    .status-badge {
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .status-selesai { background: #06d6a0; color: white; }
    .status-proses { background: #4361ee; color: white; }
    .status-terlambat { background: #ef476f; color: white; }
</style>

<div class="container-fluid px-4">
    <!-- Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-chart-line me-3"></i>Timeline Proyek SPK</h2>
                <p>Visualisasi durasi dan progress proyek per tahun</p>
            </div>
        </div>
    </div>

    <!-- Filter Tahun -->
    <div class="filter-card">
        <form method="get" action="<?= base_url('teknisi/tugas-proyek/timeline') ?>" class="row align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Pilih Tahun</label>
                <select name="tahun" class="form-select">
                    <?php foreach($available_years as $year): ?>
                        <option value="<?= $year ?>" <?= $filter['tahun'] == $year ? 'selected' : '' ?>>
                            <?= $year ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-2"></i>Tampilkan
                </button>
            </div>
        </form>
    </div>

    <!-- Statistik -->
    <div class="row">
        <div class="col-md-6">
            <div class="stat-card">
                <div class="stat-value"><?= $total_spk ?></div>
                <div class="text-muted">Total Proyek Tahun <?= $filter['tahun'] ?></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card">
                <div class="stat-value"><?= $total_selesai ?></div>
                <div class="text-muted">Proyek Selesai</div>
            </div>
        </div>
    </div>

    <!-- Timeline Chart -->
    <div class="chart-container">
        <div class="chart-title">
            <i class="fas fa-clock me-2 text-primary"></i>
            Timeline Proyek Tahun <?= $filter['tahun'] ?>
        </div>
        
        <div style="height: 400px; overflow-y: auto;">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor SPK</th>
                        <th>Judul Proyek</th>
                        <th>Client</th>
                        <th>Prioritas</th>
                        <th>Status</th>
                        <th>Durasi</th>
                        <th>Progress</th>
                        <th>Timeline</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($timeline)): ?>
                        <?php 
                        $no = 1;
                        $min_date = strtotime($filter['tahun'] . '-01-01');
                        $max_date = strtotime($filter['tahun'] . '-12-31');
                        $total_days = 365;
                        
                        foreach($timeline as $item): 
                            $start = strtotime($item->tanggal_mulai);
                            $end = !empty($item->tanggal_selesai_aktual) ? strtotime($item->tanggal_selesai_aktual) : strtotime($item->tanggal_selesai);
                            
                            // Hitung posisi dan lebar bar
                            $start_day = max(0, ($start - $min_date) / 86400);
                            $end_day = min($total_days, ($end - $min_date) / 86400);
                            $width = max(1, $end_day - $start_day);
                            $left = ($start_day / $total_days) * 100;
                            $bar_width = ($width / $total_days) * 100;
                            
                            // Status class
                            $status_class = 'status-proses';
                            $status_text = $item->status;
                            
                            if($item->status == 'Selesai') {
                                $status_class = 'status-selesai';
                            } elseif($item->status != 'Selesai' && strtotime($item->tanggal_selesai) < time()) {
                                $status_class = 'status-terlambat';
                                $status_text = 'Terlambat';
                            }
                            
                            // Prioritas class
                            $priority_class = match($item->prioritas) {
                                'Urgent' => 'priority-urgent',
                                'Tinggi' => 'priority-tinggi',
                                'Normal' => 'priority-normal',
                                'Rendah' => 'priority-rendah',
                                default => 'priority-normal'
                            };
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <a href="<?= base_url('teknisi/tugas-proyek/timeline/detail/' . $item->id) ?>" class="text-primary">
                                    <?= esc($item->nomor_spk) ?>
                                </a>
                            </td>
                            <td><?= esc($item->judul_pekerjaan) ?></td>
                            <td><?= esc($item->client_nama) ?></td>
                            <td>
                                <span class="priority-badge <?= $priority_class ?>">
                                    <?= $item->prioritas ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge <?= $status_class ?>">
                                    <?= $status_text ?>
                                </span>
                            </td>
                            <td>
                                <?= $item->durasi_hari ?> hari
                                <br>
                                <small class="text-muted">
                                    <?= date('d/m', $start) ?> - <?= date('d/m', $end) ?>
                                </small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar <?= $item->progress_persen >= 100 ? 'bg-success' : 'bg-primary' ?>" 
                                             style="width: <?= $item->progress_persen ?>%">
                                        </div>
                                    </div>
                                    <span class="ms-2 small"><?= $item->progress_persen ?>%</span>
                                </div>
                            </td>
                            <td style="min-width: 200px;">
                                <div class="timeline-bar">
                                    <div class="timeline-progress" 
                                         style="left: <?= $left ?>%; width: <?= $bar_width ?>%; 
                                                background: <?= $item->status == 'Selesai' ? '#06d6a0' : '#4361ee' ?>;">
                                    </div>
                                </div>
                                <small class="text-muted">
                                    <?= date('d M', $start) ?> - <?= date('d M', $end) ?>
                                </small>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                                <p class="text-muted">Tidak ada data proyek untuk tahun <?= $filter['tahun'] ?></p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Keterangan -->
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="d-flex gap-4 justify-content-center">
                <div><span class="badge bg-primary">●</span> Dalam Proses</div>
                <div><span class="badge bg-success">●</span> Selesai</div>
                <div><span class="badge bg-danger">●</span> Terlambat</div>
            </div>
        </div>
    </div>
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