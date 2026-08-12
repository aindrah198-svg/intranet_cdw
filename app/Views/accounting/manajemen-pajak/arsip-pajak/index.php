<div class="content-wrapper p-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-archive mr-2"></i>Arsip Dokumen Pajak</h4>
            <p class="text-muted mb-0">Penyimpanan Dokumen SPT Masa, SPT Tahunan, Bukti Potong, dan Faktur Pajak</p>
        </div>
        <div>
            <a href="<?= site_url('accounting/manajemen-pajak/arsip-pajak/upload') ?>" class="btn btn-primary shadow-sm">
                <i class="fas fa-upload mr-1"></i> Upload Dokumen Pajak
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="<?= site_url('accounting/manajemen-pajak/arsip-pajak') ?>" class="form-inline">
                <label class="mr-2 font-weight-bold">Tahun Pajak:</label>
                <select name="tahun" class="form-control mr-3">
                    <?php foreach ($tahunOptions as $t): ?>
                        <option value="<?= $t ?>" <?= ($tahun == $t) ? 'selected' : '' ?>><?= $t ?></option>
                    <?php endforeach; ?>
                </select>

                <label class="mr-2 font-weight-bold">Kategori:</label>
                <select name="kategori" class="form-control mr-3">
                    <option value="">-- Semua Kategori --</option>
                    <option value="SPT PPN" <?= ($kategori == 'SPT PPN') ? 'selected' : '' ?>>SPT PPN</option>
                    <option value="SPT PPh" <?= ($kategori == 'SPT PPh') ? 'selected' : '' ?>>SPT PPh</option>
                    <option value="Bukti Potong" <?= ($kategori == 'Bukti Potong') ? 'selected' : '' ?>>Bukti Potong</option>
                    <option value="BPE" <?= ($kategori == 'BPE') ? 'selected' : '' ?>>Bukti Penerimaan Elektronik (BPE)</option>
                    <option value="Lainnya" <?= ($kategori == 'Lainnya') ? 'selected' : '' ?>>Lainnya</option>
                </select>

                <button type="submit" class="btn btn-primary"><i class="fas fa-filter mr-1"></i> Filter Data</button>
            </form>
        </div>
    </div>

    <!-- Main Content Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-folder-open mr-2"></i>Daftar Dokumen Arsip Pajak</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Kode Arsip</th>
                            <th>Nama / Judul Dokumen</th>
                            <th>Jenis Pajak</th>
                            <th class="text-center">Masa / Tahun</th>
                            <th>Tanggal Dokumen</th>
                            <th class="text-center" width="140">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($arsipList)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>
                                    Belum ada arsip dokumen pajak untuk tahun <?= $tahun ?>.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($arsipList as $item): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><code><?= esc($item['kode_arsip'] ?? '-') ?></code></td>
                                    <td>
                                        <strong><?= esc($item['judul'] ?? $item['nama_dokumen'] ?? '-') ?></strong>
                                        <?php if (!empty($item['nomor_dokumen'])): ?>
                                            <br><small class="text-muted">No: <?= esc($item['nomor_dokumen']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge badge-info"><?= esc($item['jenis_pajak'] ?? $item['kategori'] ?? 'Lainnya') ?></span></td>
                                    <td class="text-center"><?= esc((!empty($item['masa_pajak']) ? 'Masa ' . $item['masa_pajak'] . ' - ' : '') . ($item['tahun_pajak'] ?? '-')) ?></td>
                                    <td><?= !empty($item['tanggal_dokumen']) ? date('d/m/Y', strtotime($item['tanggal_dokumen'])) : '-' ?></td>
                                    <td class="text-center">
                                        <?php if (!empty($item['file_path'])): ?>
                                            <a href="<?= base_url($item['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary mr-1" title="Download / Lihat File">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        <?php endif; ?>
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
