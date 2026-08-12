<div class="content-wrapper p-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-list mr-2"></i>Daftar Leads</h4>
            <p class="text-muted mb-0">Kelola prospek dan calon klien dari berbagai sumber</p>
        </div>
        <div>
            <a href="<?= site_url('sales/leads/pipeline') ?>" class="btn btn-outline-info mr-2">
                <i class="fas fa-columns mr-1"></i> Pipeline (Kanban)
            </a>
            <a href="<?= site_url('sales/leads/create') ?>" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Lead
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="<?= site_url('sales/leads') ?>" class="form-inline">
                <input type="text" name="search" class="form-control mr-2" placeholder="Cari nama / perusahaan..." value="<?= esc($search) ?>">
                <select name="status" class="form-control mr-3">
                    <option value="">-- Semua Status --</option>
                    <?php foreach (['Baru', 'Follow Up', 'Negosiasi', 'Closing', 'Hilang'] as $st): ?>
                        <option value="<?= $st ?>" <?= ($status == $st) ? 'selected' : '' ?>><?= $st ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary"><i class="fas fa-search mr-1"></i> Cari</button>
            </form>
        </div>
    </div>

    <!-- Tabel Leads -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-users mr-2"></i>Data Prospek Sales</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Kode Lead</th>
                            <th>Nama Lead</th>
                            <th>Perusahaan</th>
                            <th>Kontak</th>
                            <th>Sumber</th>
                            <th class="text-right">Nilai Potensi</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($leads)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">Belum ada data leads.</td>
                            </tr>
                        <?php else: ?>
                            <?php 
                            $badgeColors = ['Baru' => 'secondary', 'Follow Up' => 'info', 'Negosiasi' => 'warning', 'Closing' => 'success', 'Hilang' => 'danger'];
                            $no = 1; 
                            foreach ($leads as $lead): 
                            ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><code><?= esc($lead['kode_lead']) ?></code></td>
                                    <td><strong><?= esc($lead['nama_lead']) ?></strong></td>
                                    <td><?= esc($lead['perusahaan'] ?? '-') ?></td>
                                    <td>
                                        <small><i class="fas fa-envelope text-muted mr-1"></i> <?= esc($lead['email'] ?? '-') ?></small><br>
                                        <small><i class="fas fa-phone text-muted mr-1"></i> <?= esc($lead['telepon'] ?? '-') ?></small>
                                    </td>
                                    <td><span class="badge badge-light border"><?= esc($lead['sumber_lead']) ?></span></td>
                                    <td class="text-right font-weight-bold">Rp <?= number_format($lead['nilai_potensi'], 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <span class="badge badge-<?= $badgeColors[$lead['status']] ?? 'secondary' ?>"><?= esc($lead['status']) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= site_url('sales/leads/edit/' . $lead['id']) ?>" class="btn btn-sm btn-light border mr-1"><i class="fas fa-edit text-primary"></i></a>
                                        <form action="<?= site_url('sales/leads/delete/' . $lead['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus lead ini?')">
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
