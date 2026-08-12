<div class="content-wrapper p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-box mr-2"></i>Penyimpanan & Stok Gudang</h4>
            <p class="text-muted mb-0">Daftar stok material habis pakai (kabel, konektor, aksesoris) & mutasi stok</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-boxes mr-2"></i>Daftar Barang & Material Habis Pakai</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Nama Barang / Material</th>
                            <th>Kategori</th>
                            <th>Stok Tersedia</th>
                            <th>Satuan</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($items)): ?>
                            <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data barang gudang.</td></tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($items as $item): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><strong><?= esc($item['nama_barang'] ?? $item['nama_stok'] ?? '-') ?></strong></td>
                                    <td><span class="badge badge-light border"><?= esc($item['kategori'] ?? 'Material') ?></span></td>
                                    <td class="font-weight-bold"><?= $item['stok'] ?? $item['jumlah'] ?? 0 ?></td>
                                    <td><?= esc($item['satuan'] ?? 'Pcs') ?></td>
                                    <td class="text-center">
                                        <span class="badge badge-<?= ($item['stok'] ?? 0) > 5 ? 'success' : 'danger' ?>"><?= ($item['stok'] ?? 0) > 5 ? 'Tersedia' : 'Stok Menipis' ?></span>
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
