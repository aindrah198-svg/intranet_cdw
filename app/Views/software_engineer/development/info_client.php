<?= view('software_engineer/templates/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fas fa-building text-info me-2"></i> Info Client & Requirement Proyek (Read-Only)</h5>
        <small class="text-muted">Referensi requirement teknis dari Sales/Direktur untuk kebutuhan software development</small>
    </div>
</div>

<div class="row g-4">
    <!-- Projects Section -->
    <div class="col-md-7">
        <div class="card card-custom">
            <div class="card-header bg-transparent border-bottom fw-bold">
                <i class="fas fa-project-diagram text-primary me-2"></i> Daftar Proyek & Scope Software
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Proyek</th>
                                <th>Klien</th>
                                <th>Status Proyek</th>
                                <th>Nilai Proyek</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($projects)): ?>
                                <tr><td colspan="4" class="text-center text-muted py-4">Tidak ada data proyek aktif dari Sales/Direktur.</td></tr>
                            <?php else: ?>
                                <?php foreach ($projects as $p): ?>
                                    <tr>
                                        <td class="fw-bold text-primary"><?= esc($p['nama_proyek'] ?? $p['name'] ?? 'Proyek CDW') ?></td>
                                        <td><?= esc($p['client_name'] ?? $p['klien'] ?? '-') ?></td>
                                        <td><span class="badge bg-info text-dark"><?= esc($p['status'] ?? 'Aktif') ?></span></td>
                                        <td><small class="code-font text-muted">Rp <?= number_format($p['nilai_proyek'] ?? $p['nilai'] ?? 0, 0, ',', '.') ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Clients Section -->
    <div class="col-md-5">
        <div class="card card-custom">
            <div class="card-header bg-transparent border-bottom fw-bold">
                <i class="fas fa-address-card text-success me-2"></i> Data Klien Terdaftar
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Perusahaan</th>
                                <th>Kontak / Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($clients)): ?>
                                <tr><td colspan="2" class="text-center text-muted py-4">Belum ada data klien terdaftar.</td></tr>
                            <?php else: ?>
                                <?php foreach ($clients as $c): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?= esc($c['nama_perusahaan'] ?? $c['nama'] ?? 'Klien') ?></div>
                                            <small class="text-muted"><?= esc($c['penanggung_jawab'] ?? '-') ?></small>
                                        </td>
                                        <td>
                                            <small class="d-block code-font"><?= esc($c['telepon'] ?? $c['phone'] ?? '-') ?></small>
                                            <small class="text-muted"><?= esc($c['email'] ?? '-') ?></small>
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
</div>

<?= view('software_engineer/templates/footer') ?>
