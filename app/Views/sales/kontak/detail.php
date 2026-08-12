<div class="content-wrapper p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-id-card mr-2"></i>Detail Klien - <?= esc($klien['nama_klien']) ?></h4>
            <p class="text-muted mb-0"><?= esc($klien['perusahaan'] ?? '-') ?> (<code><?= esc($klien['kode_klien']) ?></code>)</p>
        </div>
        <div>
            <button class="btn btn-primary shadow-sm mr-2" data-toggle="modal" data-target="#modalAddInteraksi">
                <i class="fas fa-comment-dots mr-1"></i> Catat Interaksi Baru
            </button>
            <a href="<?= site_url('sales/kontak') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
        </div>
    </div>

    <div class="row">
        <!-- Info Card -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm p-3">
                <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-info-circle mr-2"></i>Informasi Kontak</h6>
                <p class="mb-2"><strong>Email:</strong> <?= esc($klien['email'] ?? '-') ?></p>
                <p class="mb-2"><strong>Telepon:</strong> <?= esc($klien['telepon'] ?? '-') ?></p>
                <p class="mb-2"><strong>Industri:</strong> <?= esc($klien['industri'] ?? '-') ?></p>
                <p class="mb-2"><strong>Status:</strong> <span class="badge badge-success"><?= esc($klien['status']) ?></span></p>
                <p class="mb-0"><strong>Alamat:</strong> <?= nl2br(esc($klien['alamat'] ?? '-')) ?></p>
            </div>
        </div>

        <!-- History & Deals -->
        <div class="col-md-8 mb-4">
            <!-- Timeline Interaksi -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history mr-2"></i>Riwayat Interaksi / Meeting / Telepon</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php if (empty($interaksi)): ?>
                            <li class="list-group-item text-center py-4 text-muted">Belum ada riwayat interaksi yang dicatat.</li>
                        <?php else: ?>
                            <?php foreach ($interaksi as $item): ?>
                                <li class="list-group-item p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="badge badge-info"><?= esc($item['jenis_interaksi']) ?></span>
                                        <small class="text-muted"><?= date('d/m/Y H:i', strtotime($item['tanggal'])) ?></small>
                                    </div>
                                    <p class="mb-1 font-weight-bold text-dark"><?= esc($item['ringkasan']) ?></p>
                                    <?php if (!empty($item['follow_up_note'])): ?>
                                        <small class="text-primary d-block"><i class="fas fa-sticky-note mr-1"></i> Follow Up: <?= esc($item['follow_up_note']) ?></small>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <!-- Deals Associated -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-handshake mr-2"></i>Riwayat Deal Terkonfirmasi</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Kode Deal</th>
                                <th>Nama Deal</th>
                                <th>Tanggal</th>
                                <th class="text-right">Nilai Deal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($deals)): ?>
                                <tr><td colspan="4" class="text-center py-3 text-muted">Belum ada deal dengan klien ini.</td></tr>
                            <?php else: ?>
                                <?php foreach ($deals as $d): ?>
                                    <tr>
                                        <td><code><?= esc($d['kode_deal']) ?></code></td>
                                        <td><?= esc($d['nama_deal']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($d['tanggal_closing'])) ?></td>
                                        <td class="text-right font-weight-bold text-success">Rp <?= number_format($d['nilai_deal'], 0, ',', '.') ?></td>
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

<!-- Modal Interaksi -->
<div class="modal fade" id="modalAddInteraksi" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= site_url('sales/kontak/interaksi/store') ?>" method="POST">
                <input type="hidden" name="klien_id" value="<?= $klien['id'] ?>">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-primary">Catat Interaksi Baru</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Jenis Interaksi</label>
                        <select name="jenis_interaksi" class="form-control">
                            <option value="Telepon">Telepon</option>
                            <option value="Email">Email</option>
                            <option value="Meeting">Meeting</option>
                            <option value="Presentasi">Presentasi</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Waktu Interaksi</label>
                        <input type="datetime-local" name="tanggal" class="form-control" value="<?= date('Y-m-d\TH:i') ?>">
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Ringkasan Diskusi / Pembicaraan <span class="text-danger">*</span></label>
                        <textarea name="ringkasan" class="form-control" rows="3" required placeholder="Catat poin penting pembicaraan..."></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Rencana Action Item / Follow Up Note</label>
                        <input type="text" name="follow_up_note" class="form-control" placeholder="Tindak lanjut berikutnya...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Interaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>
