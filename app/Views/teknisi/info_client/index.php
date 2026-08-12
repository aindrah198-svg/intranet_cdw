<div class="content-wrapper p-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-address-book mr-2"></i>Info Client Project</h4>
            <p class="text-muted mb-0">Informasi kontak klien dari proyek yang ditugaskan (Read-Only Terpusat)</p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="<?= site_url('teknisi/tugas-proyek/info-client') ?>" class="form-inline">
                <input type="text" name="search" class="form-control mr-2" placeholder="Cari nama klien / perusahaan..." value="<?= esc($search ?? '') ?>">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search mr-1"></i> Cari</button>
            </form>
        </div>
    </div>

    <!-- Client Info Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-building mr-2"></i>Daftar Klien Terpusat</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Nama Klien</th>
                            <th>Perusahaan</th>
                            <th>Kontak Email / Telp</th>
                            <th>Industri</th>
                            <th class="text-center" width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($clients)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada data klien terpusat.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($clients as $c): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><strong><?= esc($c['nama_klien'] ?? $c['nama_client'] ?? '-') ?></strong></td>
                                    <td><?= esc($c['perusahaan'] ?? '-') ?></td>
                                    <td>
                                        <small><i class="fas fa-envelope text-muted mr-1"></i> <?= esc($c['email'] ?? '-') ?></small><br>
                                        <small><i class="fas fa-phone text-muted mr-1"></i> <?= esc($c['telepon'] ?? '-') ?></small>
                                    </td>
                                    <td><span class="badge badge-light border"><?= esc($c['industri'] ?? 'Proyek') ?></span></td>
                                    <td class="text-center">
                                        <a href="<?= site_url('teknisi/tugas-proyek/info-client/detail/' . $c['id']) ?>" class="btn btn-sm btn-light border" title="Detail Info"><i class="fas fa-eye text-primary"></i></a>
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
