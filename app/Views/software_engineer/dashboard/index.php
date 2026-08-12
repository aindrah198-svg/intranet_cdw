<?= view('software_engineer/templates/header') ?>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-primary border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted fw-medium d-block" style="font-size: 0.85rem;">Total Sistem Maintained</span>
                    <h3 class="fw-bold text-dark mb-0 mt-1"><?= $total_systems ?></h3>
                </div>
                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                    <i class="fas fa-server fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-warning border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted fw-medium d-block" style="font-size: 0.85rem;">Active Dev Tasks</span>
                    <h3 class="fw-bold text-dark mb-0 mt-1"><?= $active_tasks ?></h3>
                </div>
                <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                    <i class="fas fa-tasks fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-danger border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted fw-medium d-block" style="font-size: 0.85rem;">Critical Open Bugs</span>
                    <h3 class="fw-bold text-danger mb-0 mt-1"><?= $critical_bugs ?></h3>
                </div>
                <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle">
                    <i class="fas fa-bug fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-success border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted fw-medium d-block" style="font-size: 0.85rem;">Deploy Terakhir</span>
                    <h6 class="fw-bold text-dark mb-0 mt-1"><?= !empty($recent_deploy) ? esc($recent_deploy[0]['versi']) : 'Belum Ada' ?></h6>
                    <small class="text-muted" style="font-size: 0.75rem;"><?= !empty($recent_deploy) ? date('d M Y', strtotime($recent_deploy[0]['tanggal_deploy'])) : '-' ?></small>
                </div>
                <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                    <i class="fas fa-rocket fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alert Peringatan Hosting & Domain Expired H-30 -->
<?php if (!empty($expiring_alerts)): ?>
    <div class="card card-custom border-danger mb-4">
        <div class="card-header bg-danger text-white fw-bold d-flex align-items-center justify-content-between">
            <span><i class="fas fa-exclamation-triangle me-2"></i> Peringatan Sistem Membutuhkan Perhatian (Expired H-30)</span>
            <span class="badge bg-white text-danger"><?= count($expiring_alerts) ?> Sistem Alert</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Sistem</th>
                            <th>Provider Hosting</th>
                            <th>Domain</th>
                            <th>Expired Hosting</th>
                            <th>Expired Domain</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($expiring_alerts as $alert): ?>
                            <tr>
                                <td class="fw-bold"><?= esc($alert['nama_sistem']) ?></td>
                                <td><?= esc($alert['nama_provider_hosting']) ?></td>
                                <td class="code-font text-primary"><?= esc($alert['nama_domain']) ?></td>
                                <td>
                                    <span class="badge bg-danger"><?= date('d M Y', strtotime($alert['tgl_expired_hosting'])) ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark"><?= date('d M Y', strtotime($alert['tgl_expired_domain'])) ?></span>
                                </td>
                                <td>
                                    <a href="<?= site_url('software-engineer/manajemen-sistem/hosting-domain') ?>" class="btn btn-sm btn-outline-danger">Perbarui</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- System Overview & Recent Activity -->
<div class="row g-4">
    <!-- List System Maintained -->
    <div class="col-md-7">
        <div class="card card-custom h-100">
            <div class="card-header bg-transparent border-bottom fw-bold d-flex justify-content-between align-items-center">
                <span><i class="fas fa-server text-primary me-2"></i> Inventaris Sistem & Tech Stack</span>
                <a href="<?= site_url('software-engineer/manajemen-sistem/daftar-sistem') ?>" class="btn btn-sm btn-link text-decoration-none">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Sistem</th>
                                <th>Jenis</th>
                                <th>Tech Stack</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($systems as $sys): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?= esc($sys['nama_sistem']) ?></div>
                                        <small class="text-muted code-font"><?= esc($sys['kode_sistem']) ?></small>
                                    </td>
                                    <td><span class="badge bg-secondary"><?= esc(ucfirst($sys['jenis'])) ?></span></td>
                                    <td><small class="code-font text-dark"><?= esc($sys['tech_stack']) ?></small></td>
                                    <td>
                                        <?php if ($sys['status'] == 'aktif'): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php elseif ($sys['status'] == 'maintenance'): ?>
                                            <span class="badge bg-warning text-dark">Maintenance</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Deployments -->
    <div class="col-md-5">
        <div class="card card-custom h-100">
            <div class="card-header bg-transparent border-bottom fw-bold d-flex justify-content-between align-items-center">
                <span><i class="fas fa-history text-info me-2"></i> Riwayat Deployment Terbaru</span>
                <a href="<?= site_url('software-engineer/manajemen-sistem/riwayat-deploy') ?>" class="btn btn-sm btn-link text-decoration-none">Log Deploy</a>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php if (empty($recent_deploy)): ?>
                        <li class="list-group-item text-center text-muted py-4">Belum ada catatan deployment.</li>
                    <?php else: ?>
                        <?php foreach (array_slice($recent_deploy, 0, 5) as $dep): ?>
                            <li class="list-group-item px-0 py-3 border-bottom border-light">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark"><?= esc($dep['nama_sistem']) ?></span>
                                    <span class="badge bg-cyan text-dark code-font"><?= esc($dep['versi']) ?></span>
                                </div>
                                <p class="text-muted small mb-1"><?= esc($dep['perubahan']) ?></p>
                                <small class="text-secondary" style="font-size: 0.75rem;">
                                    <i class="fas fa-user me-1"></i> <?= esc($dep['deployed_by']) ?> • 
                                    <i class="fas fa-clock me-1"></i> <?= date('d M Y H:i', strtotime($dep['tanggal_deploy'])) ?>
                                </small>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= view('software_engineer/templates/footer') ?>
