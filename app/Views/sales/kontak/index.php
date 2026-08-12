<div class="content-wrapper p-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-address-book mr-2"></i>Kontak Klien</h4>
            <p class="text-muted mb-0">Direktori kontak klien dan perusahaan yang pernah deal/bertransaksi</p>
        </div>
        <div>
            <a href="<?= site_url('sales/kontak/create') ?>" class="btn btn-primary shadow-sm">
                <i class="fas fa-user-plus mr-1"></i> Tambah Kontak Klien
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="<?= site_url('sales/kontak') ?>" class="form-inline">
                <input type="text" name="search" class="form-control mr-2" placeholder="Cari nama / perusahaan / email..." value="<?= esc($search) ?>">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search mr-1"></i> Cari</button>
            </form>
        </div>
    </div>

    <!-- Tabel Kontak -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-users mr-2"></i>Daftar Klien Perusahaan</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Kode</th>
                            <th>Nama Klien</th>
                            <th>Perusahaan</th>
                            <th>Kontak</th>
                            <th>Industri</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($kliens)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Belum ada data kontak klien.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($kliens as $k): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><code><?= esc($k['kode_klien']) ?></code></td>
                                    <td><strong><?= esc($k['nama_klien']) ?></strong></td>
                                    <td><?= esc($k['perusahaan'] ?? '-') ?></td>
                                    <td>
                                        <small><i class="fas fa-envelope text-muted mr-1"></i> <?= esc($k['email'] ?? '-') ?></small><br>
                                        <small><i class="fas fa-phone text-muted mr-1"></i> <?= esc($k['telepon'] ?? '-') ?></small>
                                    </td>
                                    <td><span class="badge badge-light border"><?= esc($k['industri'] ?? 'Umum') ?></span></td>
                                    <td class="text-center"><span class="badge badge-success"><?= esc($k['status']) ?></span></td>
                                    <td class="text-center">
                                        <a href="<?= site_url('sales/kontak/detail/' . $k['id']) ?>" class="btn btn-sm btn-light border mr-1" title="Detail & Interaksi"><i class="fas fa-eye text-primary"></i></a>
                                        <a href="<?= site_url('sales/kontak/edit/' . $k['id']) ?>" class="btn btn-sm btn-light border mr-1"><i class="fas fa-edit text-info"></i></a>
                                        <form action="<?= site_url('sales/kontak/delete/' . $k['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus kontak ini?')">
                                            <button type="submit" class="btn btn-sm btn-light border"><i class="fas fa-trash text-danger"></i></button>
                                        </form>
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
