<div class="content-wrapper p-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-handshake mr-2"></i>Closing Deal & Integrasi Modul</h4>
            <p class="text-muted mb-0">Konversi deal closing ke Draft Invoice Accounting dan Draft Project Direktur</p>
        </div>
        <div>
            <a href="<?= site_url('sales/deal/create') ?>" class="btn btn-success shadow-sm">
                <i class="fas fa-plus mr-1"></i> Catat Closing Deal Baru
            </a>
        </div>
    </div>

    <!-- Info Alert -->
    <div class="alert alert-info border-0 shadow-sm mb-4">
        <i class="fas fa-info-circle mr-2"></i> <strong>Sistem Semi-Otomatis:</strong> Setiap deal closing dapat langsung dipicu untuk membuat <strong>Draft Invoice</strong> di Accounting dan <strong>Draft Project</strong> di Direktur secara 1-klik tanpa input ulang data.
    </div>

    <!-- Tabel Deal -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-award mr-2"></i>Daftar Deal Terkonfirmasi</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Kode Deal</th>
                            <th>Nama Deal / Project</th>
                            <th>Klien / Perusahaan</th>
                            <th>Tgl Closing</th>
                            <th class="text-right">Nilai Deal</th>
                            <th class="text-center">Integrasi Invoice</th>
                            <th class="text-center">Integrasi Project</th>
                            <th class="text-center" width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($deals)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">Belum ada deal closing yang dicatat.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($deals as $deal): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><code><?= esc($deal['kode_deal']) ?></code></td>
                                    <td><strong><?= esc($deal['nama_deal']) ?></strong></td>
                                    <td><?= esc($deal['perusahaan'] ?? '-') ?></td>
                                    <td><?= date('d/m/Y', strtotime($deal['tanggal_closing'])) ?></td>
                                    <td class="text-right font-weight-bold text-success">Rp <?= number_format($deal['nilai_deal'], 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <?php if ($deal['status_invoice'] == 'Draft' || $deal['status_invoice'] == 'Issued'): ?>
                                            <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Invoice <?= $deal['status_invoice'] ?></span>
                                        <?php else: ?>
                                            <a href="<?= site_url('sales/deal/invoice/' . $deal['id']) ?>" class="btn btn-sm btn-outline-primary" title="Trigger Invoice Ke Accounting">
                                                <i class="fas fa-file-invoice mr-1"></i> Deal → Invoice
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($deal['status_project'] == 'Draft' || $deal['status_project'] == 'Created'): ?>
                                            <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Project <?= $deal['status_project'] ?></span>
                                        <?php else: ?>
                                            <a href="<?= site_url('sales/deal/project/' . $deal['id']) ?>" class="btn btn-sm btn-outline-info" title="Trigger Project Ke Direktur">
                                                <i class="fas fa-project-diagram mr-1"></i> Deal → Project
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <form action="<?= site_url('sales/deal/delete/' . $deal['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus deal ini?')">
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
