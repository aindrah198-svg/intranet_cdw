<?php
// app/Views/direktur/proyek/detail_timeline.php

$title = $title ?? 'Detail Timeline Proyek';
$templateData = [
    'title' => $title,
    'active' => 'proyek'
];

echo view('direktur/templates/header', $templateData);
echo view('direktur/templates/sidebar', $templateData);
echo view('direktur/templates/navbar', $templateData);

$projectTglMulai = !empty($project['tanggal_mulai']) ? $project['tanggal_mulai'] : date('Y-m-d');
$tglMulaiTimestamp = strtotime($projectTglMulai);
$tglSelesaiTimestamp = !empty($project['tanggal_selesai']) ? strtotime($project['tanggal_selesai']) : $tglMulaiTimestamp;

$totalHari = max(1, ceil(($tglSelesaiTimestamp - $tglMulaiTimestamp) / 86400));
$totalMinggu = max(1, ceil($totalHari / 7));

$totalTask = count($timeline);
$completedTask = 0;
$totalProgressSum = 0;

foreach ($timeline as $t) {
    $progresItem = (int)($t['progres_persen'] ?? 0);
    if (strtolower($t['status']) === 'done' || strtolower($t['status']) === 'selesai' || $progresItem >= 100) {
        $completedTask++;
        $progresItem = 100;
    }
    $totalProgressSum += $progresItem;
}
$overallProgress = $totalTask > 0 ? round($totalProgressSum / $totalTask) : 0;
?>

<style>
    .gantt-bar-bg {
        background: #e2e8f0;
        border-radius: 8px;
        height: 10px;
        overflow: hidden;
    }
    .gantt-bar-fill {
        background: linear-gradient(90deg, #0d6efd 0%, #0dcaf0 100%);
        height: 100%;
        border-radius: 8px;
        transition: width 0.4s ease;
    }
    .status-select-btn {
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .status-select-btn:hover {
        transform: scale(1.04);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
</style>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= base_url('direktur/proyek/timeline') ?>" class="text-decoration-none text-muted">Timeline Kerja</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Detail Timeline</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-stream text-primary me-2"></i> Jadwal Pelaksanaan Proyek (Harian)</h4>
            <small class="text-muted">Urutan Hari ditentukan langsung pengguna, Minggu Ke- diisi otomatis oleh sistem untuk <strong><?= esc($project['nama_project']) ?></strong>.</small>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?= base_url('direktur/proyek/timeline') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali
            </a>
            <a href="<?= base_url('direktur/proyek/timeline/export-excel/'.$project['id']) ?>" class="btn btn-outline-success rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-file-excel me-1.5"></i> Export Excel
            </a>
            <a href="<?= base_url('direktur/proyek/timeline/print-pdf/'.$project['id']) ?>" target="_blank" class="btn btn-outline-danger rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-print me-1.5"></i> Cetak / PDF
            </a>
            <button type="button" onclick="confirmSelesaikanProyekDetail(<?= $project['id'] ?>, '<?= esc($project['nama_project'], 'js') ?>')" class="btn btn-outline-success rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-check-circle me-1.5"></i> Tandai Selesai
            </button>
            <button type="button" class="btn btn-primary rounded-pill px-3.5 shadow-sm text-sm font-semibold" data-bs-toggle="modal" data-bs-target="#tambahTaskModal">
                <i class="fas fa-plus me-1.5"></i> + Tambah Task Harian
            </button>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Synchronized Project Metadata Card -->
    <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
        <div class="card-header bg-gradient-primary text-white py-3.5 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="card-title fs-5 fw-bold mb-1"><i class="fas fa-project-diagram me-2"></i> <?= esc($project['nama_project']) ?></h5>
                <small class="text-white-50"><i class="fas fa-barcode me-1"></i> Kode Proyek: <?= esc($project['kode_project']) ?> | Client: <?= esc($client['nama_perusahaan'] ?? 'General') ?></small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-white text-primary rounded-pill px-3 py-1.5 fs-6 fw-bold shadow-sm">
                    Akumulasi Progres: <?= $overallProgress ?>%
                </span>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row g-3 mb-3">
                <div class="col-sm-6 col-md-3">
                    <div class="p-3 bg-light rounded-4 border h-100">
                        <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-play text-primary me-1"></i> Tanggal Mulai (Hari 1)</small>
                        <h6 class="fw-bold text-dark mb-0 mt-1"><?= !empty($project['tanggal_mulai']) ? date('d M Y', strtotime($project['tanggal_mulai'])) : '-' ?></h6>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="p-3 bg-light rounded-4 border h-100">
                        <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-flag-checkered text-warning me-1"></i> Estimasi Selesai</small>
                        <h6 class="fw-bold text-dark mb-0 mt-1"><?= !empty($project['tanggal_selesai']) ? date('d M Y', strtotime($project['tanggal_selesai'])) : 'Belum Ditentukan' ?></h6>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="p-3 bg-light rounded-4 border h-100">
                        <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-user-tie text-info me-1"></i> Project Manager (PIC)</small>
                        <h6 class="fw-bold text-dark mb-0 mt-1"><?= esc($manager['username'] ?? 'Belum Ditunjuk') ?></h6>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="p-3 bg-light rounded-4 border h-100">
                        <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-tasks text-success me-1"></i> Total Task Milestone</small>
                        <h6 class="fw-bold text-dark mb-0 mt-1"><?= $completedTask ?> / <?= $totalTask ?> Selesai</h6>
                    </div>
                </div>
            </div>

            <!-- Overall Progress Bar -->
            <div class="pt-2">
                <div class="d-flex justify-content-between align-items-center mb-1 text-xs fw-bold">
                    <span class="text-muted">TOTAL AKUMULASI PROGRES KERJA</span>
                    <span class="text-primary"><?= $overallProgress ?>%</span>
                </div>
                <div class="gantt-bar-bg">
                    <div class="gantt-bar-fill" style="width: <?= $overallProgress ?>%;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tab Granularitas Timeline -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h5 class="fw-bold text-dark mb-0"><i class="fas fa-list-check text-primary me-2"></i> Schedule & Tahapan Pelaksanaan</h5>
        <ul class="nav nav-pills bg-white p-1 rounded-pill shadow-sm border border-light" id="timelineFilterTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-pill px-3 py-1.5 text-xs font-semibold" id="tab-all-tab" data-bs-toggle="pill" data-bs-target="#tab-all" type="button" role="tab">Semua Tahapan</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-3 py-1.5 text-xs font-semibold" id="tab-harian-tab" data-bs-toggle="pill" data-bs-target="#tab-harian" type="button" role="tab"><i class="fas fa-calendar-day me-1"></i> Harian</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-3 py-1.5 text-xs font-semibold" id="tab-mingguan-tab" data-bs-toggle="pill" data-bs-target="#tab-mingguan" type="button" role="tab"><i class="fas fa-calendar-week me-1"></i> Mingguan</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-3 py-1.5 text-xs font-semibold" id="tab-bulanan-tab" data-bs-toggle="pill" data-bs-target="#tab-bulanan" type="button" role="tab"><i class="fas fa-calendar-alt me-1"></i> Bulanan</button>
            </li>
        </ul>
    </div>

    <!-- Table Task Timeline -->
    <div class="card border-0 rounded-4 shadow-lg overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted text-xs text-uppercase">
                        <tr>
                            <th class="ps-4" width="26%">Nama Tahapan / Pekerjaan</th>
                            <th width="16%">Urutan Hari & Minggu</th>
                            <th width="15%">Jadwal Tanggal</th>
                            <th width="15%">PIC Karyawan</th>
                            <th width="12%">Progres Task</th>
                            <th width="12%">Status Task</th>
                            <th width="4%" class="text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($timeline)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-tasks fa-3x opacity-25 mb-3 d-block"></i>
                                    <h6 class="fw-bold text-dark mb-1">Belum Ada Tahapan Timeline</h6>
                                    <small>Klik tombol '+ Tambah Task Harian' di atas untuk mulai menyusun jadwal harian proyek.</small>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($timeline as $t): ?>
                            <?php
                                $tipePeriode = strtolower($t['tipe_periode'] ?? 'harian');
                                $hariKe = (int)($t['periode_ke'] ?? 1);
                                $statusTask = strtolower($t['status'] ?? 'pending');
                                
                                // Auto calculate progress percentage from status
                                $progres = (int)($t['progres_persen'] ?? 0);
                                if ($statusTask === 'pending') $progres = 0;
                                if ($statusTask === 'on_progress' && $progres == 0) $progres = 50;
                                if ($statusTask === 'done' || $statusTask === 'selesai') $progres = 100;

                                // Days & Weeks calculation
                                if (!empty($project['tanggal_mulai']) && !empty($t['tanggal_mulai'])) {
                                    $diffMulai = strtotime($t['tanggal_mulai']) - strtotime($project['tanggal_mulai']);
                                    $hariKeCalculated = max(1, floor($diffMulai / 86400) + 1);
                                    if ($hariKe <= 1 && $hariKeCalculated > 1) {
                                        $hariKe = $hariKeCalculated;
                                    }
                                }
                                $mingguKe = ceil($hariKe / 7);
                            ?>
                            <tr class="timeline-row" data-periode="<?= $tipePeriode ?>">
                                <td class="ps-4">
                                    <div class="fw-bold text-dark mb-0.5"><?= esc($t['nama_tugas']) ?></div>
                                    <small class="text-muted text-xs text-truncate d-inline-block" style="max-width:240px;"><?= esc($t['deskripsi'] ?: 'Tanpa catatan') ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-primary rounded-pill px-2.5 py-1 text-xs fw-bold mb-0.5">
                                        <i class="fas fa-calendar-day me-1"></i> Hari ke-<?= $hariKe ?>
                                    </span>
                                    <small class="d-block text-muted text-xs fw-semibold">Sistem: Minggu ke-<?= $mingguKe ?></small>
                                </td>
                                <td>
                                    <span class="text-xs fw-semibold text-dark d-block"><?= date('d M Y', strtotime($t['tanggal_mulai'])) ?></span>
                                    <small class="text-muted text-xs">s/d <?= date('d M Y', strtotime($t['tanggal_selesai'])) ?></small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1.5">
                                        <i class="fas fa-user-circle text-muted"></i>
                                        <span class="fw-semibold text-xs text-dark"><?= esc($t['ditugaskan_kepada'] ?: ($manager['username'] ?? 'Belum Ditunjuk')) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-between align-items-center mb-1 text-xs font-semibold">
                                        <span class="text-dark fw-bold"><?= $progres ?>%</span>
                                    </div>
                                    <div class="gantt-bar-bg" style="height:6px;">
                                        <div class="gantt-bar-fill" style="width: <?= $progres ?>%;"></div>
                                    </div>
                                </td>
                                <td>
                                    <!-- Interactive System Status Dropdown -->
                                    <form action="<?= base_url('direktur/proyek/timeline/update_task_status') ?>" method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                        <select name="status" class="form-select form-select-sm rounded-pill text-xs fw-bold status-select-btn" onchange="this.form.submit()" style="width:130px; font-size: 11px;">
                                            <option value="pending" <?= $statusTask === 'pending' ? 'selected' : '' ?>>⚪ Belum Mulai (0%)</option>
                                            <option value="on_progress" <?= $statusTask === 'on_progress' ? 'selected' : '' ?>>🔵 On Progress (50%)</option>
                                            <option value="done" <?= ($statusTask === 'done' || $statusTask === 'selesai') ? 'selected' : '' ?>>🟢 Selesai (100%)</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="text-center pe-4">
                                    <button type="button" onclick="confirmDeleteTask(<?= $t['id'] ?>, '<?= esc($t['nama_tugas'], 'js') ?>')" class="btn btn-sm btn-outline-danger rounded-circle p-1.5 text-xs" title="Hapus Task">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Task Baru -->
<div class="modal fade" id="tambahTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white rounded-top-4 py-3 px-4">
                <h5 class="modal-title fs-6 fw-bold mb-0"><i class="fas fa-plus-circle me-2"></i> Tambah Task Harian Proyek</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('direktur/proyek/timeline/simpan_task') ?>" method="POST" onsubmit="const btn = this.querySelector('button[type=submit]'); if(btn){ btn.disabled = true; btn.innerHTML = '<i class=\'fas fa-spinner fa-spin me-1\'></i> Menyimpan...'; }">
                <input type="hidden" name="proyek_id" value="<?= $project['id'] ?>">
                <input type="hidden" name="tipe_periode" value="harian">
                
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Nama Pekerjaan / Task *</label>
                        <input type="text" class="form-control rounded-3" name="nama_tugas" required placeholder="Cth: Persiapan Material & Mobilisasi Peralatan">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-xs text-dark">Hari Ke- (Input Pengguna) *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold text-xs">Hari Ke-</span>
                                <input type="number" class="form-control rounded-end-3" name="periode_ke" id="input_hari_ke" value="<?= count($timeline) + 1 ?>" min="1" required onchange="calculateAutoDatesAndWeeks()" onkeyup="calculateAutoDatesAndWeeks()">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-xs text-dark">Minggu Ke- (Diisi Otomatis Sistem)</label>
                            <input type="text" class="form-control rounded-3 bg-light fw-bold text-primary" id="display_minggu_ke" readonly value="Minggu ke-1">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-xs text-dark">Tanggal Mulai Task</label>
                            <input type="date" class="form-control rounded-3" name="tanggal_mulai" id="input_task_tgl_mulai" value="<?= $projectTglMulai ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-xs text-dark">Tenggat Waktu Selesai</label>
                            <input type="date" class="form-control rounded-3" name="tanggal_selesai" id="input_task_tgl_selesai" value="<?= $projectTglMulai ?>">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-xs text-dark">Penugasan PIC Karyawan</label>
                            <select class="form-select rounded-3" name="karyawan_id">
                                <option value="">-- Gunakan PM Proyek / Pilih PIC --</option>
                                <?php foreach(($karyawan ?? []) as $k): ?>
                                    <option value="<?= $k['id'] ?>" <?= (!empty($project['project_manager_id']) && $project['project_manager_id'] == $k['id']) ? 'selected' : '' ?>><?= esc($k['username']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-xs text-dark">Status Pelaksanaan</label>
                            <select class="form-select rounded-3" name="status">
                                <option value="pending">⚪ Belum Mulai (Progres 0%)</option>
                                <option value="on_progress">🔵 Sedang Berjalan (Progres 50%)</option>
                                <option value="done">🟢 Selesai / Done (Progres 100%)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Deskripsi / Catatan Tugas</label>
                        <textarea class="form-control rounded-3" name="deskripsi" rows="2" placeholder="Catatan teknis pekerjaan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4 py-2.5 px-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                        <i class="fas fa-plus me-1.5"></i> Simpan Task Harian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const PROJECT_START_DATE = '<?= $projectTglMulai ?>';

    function calculateAutoDatesAndWeeks() {
        const hariKe = parseInt(document.getElementById('input_hari_ke').value) || 1;
        const mingguKe = Math.ceil(hariKe / 7);

        // Display auto-calculated Minggu Ke-
        document.getElementById('display_minggu_ke').value = 'Minggu ke-' + mingguKe;

        // Auto-calculate dates based on Hari ke-
        const startDate = new Date(PROJECT_START_DATE);
        if (!isNaN(startDate.getTime())) {
            let taskStart = new Date(startDate);
            taskStart.setDate(taskStart.getDate() + (hariKe - 1));

            document.getElementById('input_task_tgl_mulai').value = taskStart.toISOString().split('T')[0];
            document.getElementById('input_task_tgl_selesai').value = taskStart.toISOString().split('T')[0];
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        calculateAutoDatesAndWeeks();

        const tabs = document.querySelectorAll('#timelineFilterTab button');
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const target = this.getAttribute('id');
                const rows = document.querySelectorAll('.timeline-row');

                rows.forEach(row => {
                    if (target === 'tab-all-tab') {
                        row.style.display = '';
                    } else if (target === 'tab-harian-tab') {
                        row.style.display = '';
                    } else if (target === 'tab-mingguan-tab') {
                        row.style.display = '';
                    } else if (target === 'tab-bulanan-tab') {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    });

    function confirmSelesaikanProyekDetail(id, nama) {
        Swal.fire({
            title: 'Selesaikan Proyek?',
            text: 'Proyek "' + nama + '" akan ditandai Selesai dan dipindahkan ke Arsip Project Selesai.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-check me-1"></i> Ya, Selesaikan!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= base_url('direktur/proyek/timeline/selesaikan') ?>/' + id;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function confirmDeleteTask(id, nama) {
        Swal.fire({
            title: 'Hapus Task?',
            text: 'Tugas "' + nama + '" akan dihapus dari timeline proyek.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash me-1"></i> Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= base_url('direktur/proyek/timeline/delete_task') ?>/' + id;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>

<?= view('direktur/templates/footer', $templateData) ?>
