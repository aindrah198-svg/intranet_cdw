<div class="content-wrapper p-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-file-invoice-dollar mr-2"></i>Riwayat Quotation (Penawaran Harga)</h4>
            <p class="text-muted mb-0">Kelola dan lacak surat penawaran harga kepada calon klien</p>
        </div>
        <div>
            <a href="<?= site_url('sales/quotation/create') ?>" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus mr-1"></i> Buat Quotation Baru
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="<?= site_url('sales/quotation') ?>" class="form-inline">
                <input type="text" name="search" class="form-control mr-2" placeholder="Cari nomor / nama klien..." value="<?= esc($search) ?>">
                <select name="status" class="form-control mr-3">
                    <option value="">-- Semua Status --</option>
                    <?php foreach (['Draft', 'Sent', 'Approved', 'Rejected', 'Revised'] as $st): ?>
                        <option value="<?= $st ?>" <?= ($status == $st) ? 'selected' : '' ?>><?= $st ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary"><i class="fas fa-search mr-1"></i> Cari</button>
            </form>
        </div>
    </div>

    <!-- Tabel Quotation -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history mr-2"></i>Daftar Penawaran Harga</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>No Quotation</th>
                            <th>Nama Klien</th>
                            <th>Perusahaan</th>
                            <th>Tgl Quotation</th>
                            <th>Berlaku Sampai</th>
                            <th class="text-right">Total Penawaran</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($quotations)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">Belum ada quotation.</td>
                            </tr>
                        <?php else: ?>
                            <?php 
                            $badgeColors = ['Draft' => 'secondary', 'Sent' => 'info', 'Approved' => 'success', 'Rejected' => 'danger', 'Revised' => 'warning'];
                            $no = 1;
                            foreach ($quotations as $q): 
                            ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><code><?= esc($q['nomor_quotation']) ?></code> <span class="badge badge-light border">v<?= $q['versi'] ?></span></td>
                                    <td><strong><?= esc($q['nama_klien']) ?></strong></td>
                                    <td><?= esc($q['perusahaan'] ?? '-') ?></td>
                                    <td><?= date('d/m/Y', strtotime($q['tanggal_quotation'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($q['berlaku_hingga'])) ?></td>
                                    <td class="text-right font-weight-bold">Rp <?= number_format($q['total'], 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <span class="badge badge-<?= $badgeColors[$q['status']] ?? 'secondary' ?>"><?= esc($q['status']) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= site_url('sales/quotation/detail/' . $q['id']) ?>" class="btn btn-sm btn-light border mr-1"><i class="fas fa-eye text-primary"></i></a>
                                        <form action="<?= site_url('sales/quotation/delete/' . $q['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus quotation ini?')">
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
