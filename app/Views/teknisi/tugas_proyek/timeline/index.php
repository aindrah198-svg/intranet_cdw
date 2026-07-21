<?= $this->include('teknisi/templates/header') ?>
<?= $this->include('teknisi/templates/sidebar') ?>
<?= $this->include('teknisi/templates/navbar') ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<!-- Moment.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<!-- Font Awesome untuk icon lebih lengkap -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    :root {
        --primary: #4361ee;
        --secondary: #3a0ca3;
        --success: #06d6a0;
        --warning: #ffb703;
        --danger: #ef476f;
        --info: #4cc9f0;
        --dark: #2b2d42;
        --light: #f8f9fa;
    }

    * {
        transition: all 0.2s ease;
    }

    .page-header {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        color: white;
        box-shadow: 0 10px 30px rgba(67, 97, 238, 0.3);
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        transform: rotate(25deg);
    }

    .page-header h2 {
        font-weight: 700;
        margin-bottom: 10px;
        font-size: 2rem;
        position: relative;
    }

    .page-header p {
        opacity: 0.9;
        margin-bottom: 0;
        font-size: 1.1rem;
        position: relative;
    }

    .filter-card {
        background: white;
        border-radius: 20px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid rgba(0,0,0,0.05);
    }

    .form-select, .form-control {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 12px 15px;
        font-size: 14px;
        background-color: white;
        cursor: pointer;
    }

    .form-select:hover, .form-control:hover {
        border-color: var(--primary);
    }

    .form-select:focus, .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border: none;
        border-radius: 12px;
        padding: 12px 25px;
        font-weight: 600;
        box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(67, 97, 238, 0.4);
    }

    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid rgba(0,0,0,0.05);
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: -20px;
        right: -20px;
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, rgba(67, 97, 238, 0.1), rgba(58, 12, 163, 0.1));
        border-radius: 50%;
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 5px;
        line-height: 1.2;
    }

    .stat-label {
        color: #6c757d;
        font-size: 0.9rem;
        font-weight: 500;
        margin-bottom: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .stat-label i {
        color: var(--primary);
        font-size: 1.2rem;
    }

    .chart-container {
        background: white;
        border-radius: 20px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: 1px solid rgba(0,0,0,0.05);
    }

    .chart-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .chart-title i {
        color: var(--primary);
        font-size: 1.3rem;
    }

    .timeline-wrapper {
        max-height: 500px;
        overflow-y: auto;
        border-radius: 12px;
        scrollbar-width: thin;
        scrollbar-color: var(--primary) #f0f0f0;
    }

    .timeline-wrapper::-webkit-scrollbar {
        width: 6px;
    }

    .timeline-wrapper::-webkit-scrollbar-track {
        background: #f0f0f0;
        border-radius: 10px;
    }

    .timeline-wrapper::-webkit-scrollbar-thumb {
        background: var(--primary);
        border-radius: 10px;
    }

    .table {
        margin-bottom: 0;
    }

    .table thead th {
        background: #f8f9fa;
        color: var(--dark);
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e9ecef;
        padding: 15px 12px;
        position: sticky;
        top: 0;
        background: #f8f9fa;
        z-index: 10;
    }

    .table tbody tr {
        transition: all 0.2s;
    }

    .table tbody tr:hover {
        background: rgba(67, 97, 238, 0.02);
        transform: translateX(5px);
    }

    .table td {
        padding: 15px 12px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f0f0;
    }

    .spk-link {
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
        display: inline-block;
        position: relative;
    }

    .spk-link::after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        bottom: -2px;
        left: 0;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        transition: width 0.3s;
    }

    .spk-link:hover {
        color: var(--secondary);
    }

    .spk-link:hover::after {
        width: 100%;
    }

    .priority-badge {
        padding: 5px 12px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-block;
    }

    .priority-urgent { background: #ef476f; color: white; }
    .priority-tinggi { background: #ffb703; color: white; }
    .priority-normal { background: #4361ee; color: white; }
    .priority-rendah { background: #6c757d; color: white; }

    .status-badge {
        padding: 5px 12px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-block;
    }

    .status-selesai { background: #06d6a0; color: white; }
    .status-proses { background: #4361ee; color: white; }
    .status-terlambat { background: #ef476f; color: white; }

    .progress-custom {
        height: 8px;
        border-radius: 10px;
        background-color: #f0f0f0;
        overflow: hidden;
    }

    .progress-bar-custom {
        height: 100%;
        border-radius: 10px;
        transition: width 0.5s ease;
    }

    .timeline-bar {
        position: relative;
        height: 40px;
        background: #f0f0f0;
        border-radius: 12px;
        margin: 5px 0;
        overflow: hidden;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
    }

    .timeline-progress {
        position: absolute;
        height: 100%;
        border-radius: 12px;
        opacity: 0.9;
        box-shadow: 0 2px 8px rgba(67, 97, 238, 0.3);
    }

    .timeline-label {
        display: flex;
        justify-content: space-between;
        margin-top: 5px;
        font-size: 0.8rem;
        color: #6c757d;
    }

    .legend {
        background: white;
        border-radius: 50px;
        padding: 15px 25px;
        display: inline-flex;
        gap: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
    }

    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 4px;
    }

    .empty-state {
        padding: 60px 20px;
        text-align: center;
        background: #f8f9fa;
        border-radius: 15px;
    }

    .empty-state i {
        font-size: 4rem;
        color: #adb5bd;
        margin-bottom: 20px;
    }

    .empty-state h5 {
        color: #495057;
        margin-bottom: 10px;
    }

    .empty-state p {
        color: #6c757d;
        margin-bottom: 0;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .fade-in {
        animation: fadeIn 0.5s ease forwards;
    }

    .badge-year {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 8px 20px;
        border-radius: 50px;
        font-size: 1rem;
        font-weight: 600;
        backdrop-filter: blur(10px);
    }
</style>

<div class="container-fluid px-4">
    <!-- Header dengan animasi -->
    <div class="page-header fade-in">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="fas fa-chart-line me-3"></i>Timeline Proyek SPK</h2>
                <p><i class="fas fa-calendar-alt me-2"></i>Visualisasi durasi dan progress proyek per tahun</p>
            </div>
            <div class="badge-year">
                <i class="fas fa-calendar me-2"></i>Tahun <?= $filter['tahun'] ?>
            </div>
        </div>
    </div>

    <!-- Filter Tahun dengan desain lebih baik -->
    <div class="filter-card fade-in" style="animation-delay: 0.1s;">
        <form method="get" action="<?= base_url('teknisi/tugas-proyek/timeline') ?>" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold">
                    <i class="fas fa-calendar-alt me-2 text-primary"></i>Pilih Tahun
                </label>
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
            <div class="col-md-6 text-end">
                <span class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Menampilkan <?= $total_spk ?> proyek
                </span>
            </div>
        </form>
    </div>

    <!-- Statistik dengan icon -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3 fade-in" style="animation-delay: 0.2s;">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="stat-value"><?= $total_spk ?></div>
                        <div class="stat-label">
                            <i class="fas fa-tasks"></i>
                            Total Proyek Tahun <?= $filter['tahun'] ?>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-project-diagram fa-3x text-primary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3 fade-in" style="animation-delay: 0.25s;">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="stat-value"><?= $total_selesai ?></div>
                        <div class="stat-label">
                            <i class="fas fa-check-circle text-success"></i>
                            Proyek Selesai
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-check-double fa-3x text-success opacity-25"></i>
                    </div>
                </div>
                <?php if($total_spk > 0): ?>
                    <div class="progress-custom mt-3">
                        <div class="progress-bar-custom bg-success" 
                             style="width: <?= ($total_selesai / $total_spk) * 100 ?>%">
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block">
                        <?= round(($total_selesai / $total_spk) * 100) ?>% dari total proyek
                    </small>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Timeline Chart dengan scroll yang lebih baik -->
    <div class="chart-container fade-in" style="animation-delay: 0.3s;">
        <div class="chart-title">
            <i class="fas fa-clock"></i>
            Timeline Proyek Tahun <?= $filter['tahun'] ?>
            <span class="badge bg-light text-dark ms-2"><?= count($timeline) ?> Proyek</span>
        </div>
        
        <div class="timeline-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="12%">No. SPK</th>
                        <th width="18%">Judul Proyek</th>
                        <th width="12%">Client</th>
                        <th width="8%">Prioritas</th>
                        <th width="8%">Status</th>
                        <th width="10%">Durasi</th>
                        <th width="10%">Progress</th>
                        <th width="17%">Timeline</th>
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
                            
                            // Progress color
                            $progress_class = $item->progress_persen >= 100 ? 'bg-success' : 
                                              ($item->progress_persen >= 50 ? 'bg-primary' : 'bg-warning');
                            
                            // Bar color
                            $bar_color = $item->status == 'Selesai' ? '#06d6a0' : 
                                        ($status_class == 'status-terlambat' ? '#ef476f' : '#4361ee');
                        ?>
                        <tr>
                            <td><span class="fw-semibold"><?= sprintf('%02d', $no++) ?></span></td>
                            <td>
                                <a href="<?= base_url('teknisi/tugas-proyek/timeline/detail/' . $item->id) ?>" class="spk-link">
                                    <?= esc($item->nomor_spk) ?>
                                </a>
                            </td>
                            <td>
                                <span class="fw-semibold"><?= esc($item->judul_pekerjaan) ?></span>
                            </td>
                            <td><?= esc($item->client_nama) ?></td>
                            <td>
                                <span class="priority-badge <?= $priority_class ?>">
                                    <i class="fas fa-flag me-1"></i>
                                    <?= $item->prioritas ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge <?= $status_class ?>">
                                    <i class="fas fa-circle me-1" style="font-size: 6px;"></i>
                                    <?= $status_text ?>
                                </span>
                            </td>
                            <td>
                                <span class="fw-semibold"><?= $item->durasi_hari ?> hari</span>
                                <br>
                                <small class="text-muted">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    <?= date('d/m', $start) ?> - <?= date('d/m', $end) ?>
                                </small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress-custom flex-grow-1" style="width: 80px;">
                                        <div class="progress-bar-custom <?= $progress_class ?>" 
                                             style="width: <?= $item->progress_persen ?>%">
                                        </div>
                                    </div>
                                    <span class="small fw-bold <?= $item->progress_persen >= 100 ? 'text-success' : '' ?>">
                                        <?= $item->progress_persen ?>%
                                    </span>
                                </div>
                            </td>
                            <td style="min-width: 200px;">
                                <div class="timeline-bar">
                                    <div class="timeline-progress" 
                                         style="left: <?= $left ?>%; width: <?= $bar_width ?>%; background: <?= $bar_color ?>;">
                                    </div>
                                </div>
                                <div class="timeline-label">
                                    <small><i class="far fa-calendar-check me-1"></i><?= date('d M', $start) ?></small>
                                    <small><i class="far fa-calendar-times me-1"></i><?= date('d M', $end) ?></small>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-calendar-times"></i>
                                    <h5>Belum Ada Data Proyek</h5>
                                    <p class="text-muted">Tidak ada data proyek untuk tahun <?= $filter['tahun'] ?></p>
                                    <p class="text-muted small">Silakan pilih tahun lain atau tambahkan proyek baru</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Keterangan dengan desain lebih baik -->
    <div class="row mt-3 fade-in" style="animation-delay: 0.4s;">
        <div class="col-md-12 text-center">
            <div class="legend">
                <div class="legend-item">
                    <span class="legend-color" style="background: #4361ee;"></span>
                    <span>Dalam Proses</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color" style="background: #06d6a0;"></span>
                    <span>Selesai</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color" style="background: #ef476f;"></span>
                    <span>Terlambat</span>
                </div>
                <div class="legend-item">
                    <i class="fas fa-mouse-pointer text-primary"></i>
                    <span>Klik nomor SPK untuk detail</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inisialisasi tooltips Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Smooth scroll untuk timeline wrapper
    $('.timeline-wrapper').on('wheel', function(e) {
        if (e.originalEvent.deltaY > 0) {
            $(this).scrollTop($(this).scrollTop() + 30);
        } else {
            $(this).scrollTop($(this).scrollTop() - 30);
        }
        e.preventDefault();
    });
    
    // Highlight row ketika dihover
    $('.table tbody tr').hover(
        function() {
            $(this).find('.timeline-progress').css('opacity', '1');
        },
        function() {
            $(this).find('.timeline-progress').css('opacity', '0.9');
        }
    );
    
    // Animasi untuk progress bar
    $('.progress-bar-custom').each(function() {
        var width = $(this).width();
        $(this).width(0);
        $(this).animate({ width: width }, 1000);
    });
});
</script>

<?= $this->include('teknisi/templates/footer') ?>