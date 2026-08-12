<div class="content-wrapper p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-file-invoice mr-2"></i>Quotation #<?= esc($quotation['nomor_quotation']) ?></h4>
            <p class="text-muted mb-0">Detail Penawaran Harga Klien</p>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-outline-secondary mr-2"><i class="fas fa-print mr-1"></i> Cetak</button>
            <a href="<?= site_url('sales/quotation') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5 class="font-weight-bold text-dark mb-1"><?= esc($quotation['nama_klien']) ?></h5>
                    <p class="text-muted mb-0"><?= esc($quotation['perusahaan'] ?? '-') ?></p>
                </div>
                <div class="col-md-6 text-right">
                    <span class="badge badge-primary px-3 py-2">Status: <?= esc($quotation['status']) ?></span>
                    <p class="text-muted small mt-2 mb-0">Tgl: <?= date('d/m/Y', strtotime($quotation['tanggal_quotation'])) ?> | Berlaku s/d: <?= date('d/m/Y', strtotime($quotation['berlaku_hingga'])) ?></p>
                </div>
            </div>

            <table class="table table-bordered mb-4">
                <thead class="bg-light">
                    <tr>
                        <th width="50" class="text-center">No</th>
                        <th>Deskripsi Barang / Jasa</th>
                        <th width="100" class="text-center">Qty</th>
                        <th width="180" class="text-right">Harga Satuan</th>
                        <th width="180" class="text-right">Total Harga</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($items as $item): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= esc($item['deskripsi']) ?></td>
                            <td class="text-center"><?= $item['qty'] ?></td>
                            <td class="text-right">Rp <?= number_format($item['harga_satuan'], 0, ',', '.') ?></td>
                            <td class="text-right font-weight-bold">Rp <?= number_format($item['total_harga'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="row justify-content-end">
                <div class="col-md-5">
                    <table class="table table-borderless align-middle">
                        <tr>
                            <td class="font-weight-bold">Subtotal:</td>
                            <td class="text-right font-weight-bold">Rp <?= number_format($quotation['subtotal'], 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Diskon:</td>
                            <td class="text-right text-danger">(Rp <?= number_format($quotation['diskon'], 0, ',', '.') ?>)</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">PPN 11%:</td>
                            <td class="text-right">Rp <?= number_format($quotation['ppn'], 0, ',', '.') ?></td>
                        </tr>
                        <tr class="border-top">
                            <td class="font-weight-bold h5 text-primary">TOTAL PENAWARAN:</td>
                            <td class="text-right font-weight-bold h5 text-primary">Rp <?= number_format($quotation['total'], 0, ',', '.') ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <?php if (!empty($quotation['catatan'])): ?>
                <div class="bg-light p-3 rounded mt-3">
                    <strong class="text-dark">Catatan & Syarat Penawaran:</strong>
                    <p class="mb-0 text-muted"><?= nl2br(esc($quotation['catatan'])) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
