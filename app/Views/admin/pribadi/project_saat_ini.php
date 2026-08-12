<?php
$data = [
    'title'  => 'Project Saat Ini',
    'active' => 'project-saat-ini',
    'user'   => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
];

echo view('admin/templates/header', $data);
echo view('admin/templates/sidebar', $data);
echo view('admin/templates/navbar', $data);
?>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>" class="text-decoration-none text-muted">Menu Pribadi</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Project Saat Ini</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-project-diagram text-primary me-2"></i> Project Berjalan & Aktif (Direktur)</h4>
            <small class="text-muted">Informasi proyek aktif yang sedang ditangani perusahaan dan terhubung dengan arahan Direktur.</small>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card border-0 rounded-4 shadow-sm overflow-hidden mb-4">
        <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2 border-bottom border-light">
            <h5 class="card-title fs-6 fw-bold mb-0 text-dark"><i class="fas fa-briefcase me-2 text-primary"></i> Daftar Proyek Aktif Perusahaan</h5>
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1.5 rounded-pill font-semibold">
                Total: <?= count($projects) ?> Proyek
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-sm">
                    <thead class="table-light text-uppercase text-xs text-muted">
                        <tr>
                            <th class="ps-4 py-3">Kode & Nama Proyek</th>
                            <th class="py-3">Klien / Perusahaan</th>
                            <th class="py-3">Deskripsi Proyek</th>
                            <th class="py-3 text-center">Periode Pelaksanaan</th>
                            <th class="py-3 text-center">Status Proyek</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($projects)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                                Belum ada data proyek aktif yang diinput oleh Direktur.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach($projects as $pj): 
                            $st = strtolower($pj['status'] ?? 'on_progress');
                            $badge = 'bg-primary text-white';
                            if ($st === 'selesai' || $st === 'deal') $badge = 'bg-success text-white';
                            if ($st === 'on_progress') $badge = 'bg-info text-white';
                            if ($st === 'penawaran' || $st === 'nego') $badge = 'bg-warning text-dark';
                            if ($st === 'batal') $badge = 'bg-danger text-white';
                        ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark fs-6"><?= esc($pj['nama_project']) ?></div>
                                <small class="text-primary fw-semibold"><i class="fas fa-hashtag me-1"></i><?= esc($pj['kode_project']) ?></small>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark"><?= esc($pj['nama_perusahaan'] ?: 'Klien General') ?></div>
                                <small class="text-muted text-xs"><?= esc($pj['nama_kontak'] ?: '-') ?></small>
                            </td>
                            <td>
                                <small class="text-dark d-block text-truncate" style="max-width: 250px;" title="<?= esc($pj['deskripsi']) ?>">
                                    <?= esc($pj['deskripsi'] ?: 'Tanpa rincian deskripsi') ?>
                                </small>
                            </td>
                            <td class="text-center text-nowrap">
                                <span class="badge bg-light text-dark border px-2.5 py-1 text-xs">
                                    <i class="far fa-calendar-alt text-primary me-1"></i>
                                    <?= !empty($pj['tanggal_mulai']) ? date('d M Y', strtotime($pj['tanggal_mulai'])) : '-' ?>
                                    <?php if(!empty($pj['tanggal_selesai'])): ?>
                                        - <?= date('d M Y', strtotime($pj['tanggal_selesai'])) ?>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $badge ?> px-3 py-1 rounded-pill text-xs fw-semibold">
                                    <?= strtoupper(esc(str_replace('_', ' ', $pj['status'] ?? 'ON PROGRESS'))) ?>
                                </span>
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

<?= view('admin/templates/footer', $data) ?>
