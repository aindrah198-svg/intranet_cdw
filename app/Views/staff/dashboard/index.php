<div class="main-content">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">Selamat Datang, <?= esc($user['name']) ?> 👋</h3>
                <p class="text-muted mb-0">Panel Informasi Personal Staff - CDW Engineering</p>
            </div>
            <div>
                <span class="badge bg-primary fs-6 px-3 py-2 rounded-pill">
                    <i class="fas fa-id-badge me-1"></i> <?= esc($user['jabatan']) ?> (<?= esc($user['divisi']) ?>)
                </span>
            </div>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Stat Cards -->
        <div class="row g-3 mb-4">
            <!-- Status Absensi -->
            <div class="col-md-3">
                <div class="card card-custom p-3 border-start border-4 border-primary h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted fw-semibold">Absensi Hari Ini</small>
                            <h5 class="fw-bold text-dark mt-1 mb-0">
                                <?php if (!empty($absensiHariIni['waktu_masuk'])): ?>
                                    <span class="text-success"><i class="fas fa-check-circle me-1"></i> Hadir (<?= esc(substr($absensiHariIni['waktu_masuk'], 0, 5)) ?>)</span>
                                <?php else: ?>
                                    <span class="text-danger"><i class="fas fa-times-circle me-1"></i> Belum Absen</span>
                                <?php endif; ?>
                            </h5>
                        </div>
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sisa Cuti -->
            <div class="col-md-3">
                <div class="card card-custom p-3 border-start border-4 border-info h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted fw-semibold">Sisa Kuota Cuti</small>
                            <h4 class="fw-bold text-info mt-1 mb-0"><?= esc($sisaCuti) ?> <span class="fs-6 text-muted font-normal">Hari</span></h4>
                        </div>
                        <div class="rounded-circle bg-info bg-opacity-10 p-3 text-info">
                            <i class="fas fa-calendar-minus fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tugas Hari Ini -->
            <div class="col-md-3">
                <div class="card card-custom p-3 border-start border-4 border-warning h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted fw-semibold">Tugas Saya</small>
                            <h4 class="fw-bold text-warning mt-1 mb-0"><?= count($tugasHariIni) ?> <span class="fs-6 text-muted">Tugas</span></h4>
                        </div>
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3 text-warning">
                            <i class="fas fa-tasks fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Pengajuan -->
            <div class="col-md-3">
                <div class="card card-custom p-3 border-start border-4 border-success h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted fw-semibold">Pengajuan Terakhir</small>
                            <h5 class="fw-bold text-dark mt-1 mb-0">
                                <?php if (!empty($pengajuanTerakhir[0])): ?>
                                    <span class="badge bg-secondary"><?= esc($pengajuanTerakhir[0]['kategori']) ?></span>
                                    <span class="badge bg-info text-dark"><?= esc($pengajuanTerakhir[0]['status']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted fs-6">Belum Ada</span>
                                <?php endif; ?>
                            </h5>
                        </div>
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success">
                            <i class="fas fa-paper-plane fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Tugas & Pengajuan -->
        <div class="row g-4">
            <!-- Table Tugas Saya -->
            <div class="col-md-7">
                <div class="card card-custom p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0"><i class="fas fa-clipboard-list text-primary me-2"></i> Tugas Saya Terbaru</h5>
                        <a href="<?= base_url('staff/tugas') ?>" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Judul Tugas</th>
                                    <th>Prioritas</th>
                                    <th>Tenggat Waktu</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($tugasHariIni)): foreach (array_slice($tugasHariIni, 0, 4) as $t): ?>
                                    <tr>
                                        <td class="fw-bold"><?= esc($t['judul_tugas'] ?? 'Tugas Harian') ?></td>
                                        <td>
                                            <?php 
                                            $prioClass = ($t['prioritas'] ?? '') == 'tinggi' ? 'danger' : (($t['prioritas'] ?? '') == 'sedang' ? 'warning' : 'secondary');
                                            ?>
                                            <span class="badge bg-<?= $prioClass ?>"><?= ucfirst(esc($t['prioritas'] ?? 'normal')) ?></span>
                                        </td>
                                        <td><?= esc($t['tenggat_waktu'] ?? '-') ?></td>
                                        <td>
                                            <span class="badge bg-<?= ($t['status'] ?? '') == 'selesai' ? 'success' : (($t['status'] ?? '') == 'proses' ? 'info' : 'warning') ?>">
                                                <?= ucfirst(esc($t['status'] ?? 'pending')) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr><td colspan="4" class="text-center text-muted py-3">Belum ada tugas yang di-assign.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Table Riwayat Pengajuan Terakhir -->
            <div class="col-md-5">
                <div class="card card-custom p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0"><i class="fas fa-history text-info me-2"></i> Pengajuan Terakhir</h5>
                        <a href="<?= base_url('staff/pengajuan/riwayat') ?>" class="btn btn-sm btn-outline-info">Riwayat</a>
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php if (!empty($pengajuanTerakhir)): foreach ($pengajuanTerakhir as $p): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                <div>
                                    <div class="fw-bold"><?= esc($p['kategori']) ?></div>
                                    <small class="text-muted"><?= esc(substr($p['alasan'] ?? '', 0, 35)) ?>...</small>
                                </div>
                                <span class="badge bg-<?= ($p['status'] == 'Disetujui' || $p['status'] == 'Disetujui HRD') ? 'success' : ($p['status'] == 'Ditolak' ? 'danger' : 'warning') ?>">
                                    <?= esc($p['status']) ?>
                                </span>
                            </li>
                        <?php endforeach; else: ?>
                            <li class="list-group-item text-center text-muted py-3">Belum ada pengajuan.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
