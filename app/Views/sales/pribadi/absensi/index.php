<div class="content-wrapper p-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-user-check mr-2"></i>Absensi Saya</h4>
            <p class="text-muted mb-0">Riwayat Kehadiran Staf Sales & Marketing</p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="<?= site_url('sales/pribadi/absensi') ?>" class="form-inline">
                <label class="mr-2 font-weight-bold">Bulan:</label>
                <select name="bulan" class="form-control mr-3">
                    <?php
                    $bulanNames = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
                    foreach ($bulanNames as $k => $v):
                    ?>
                        <option value="<?= $k ?>" <?= ($bulan == $k || $bulan == (int)$k) ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>

                <label class="mr-2 font-weight-bold">Tahun:</label>
                <select name="tahun" class="form-control mr-3">
                    <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                        <option value="<?= $y ?>" <?= ($tahun == $y) ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>

                <button type="submit" class="btn btn-primary"><i class="fas fa-filter mr-1"></i> Filter</button>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history mr-2"></i>Daftar Kehadiran Periode <?= $bulanNames[str_pad($bulan, 2, '0', STR_PAD_LEFT)] ?? $bulan ?> <?= $tahun ?></h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Tanggal</th>
                            <th>Jam Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Jam Kerja</th>
                            <th>Keterlambatan</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($absensiList)): ?>
                            <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat absensi.</td></tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($absensiList as $row): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><strong><?= date('d/m/Y', strtotime($row['tanggal'])) ?></strong></td>
                                    <td><?= !empty($row['waktu_masuk']) ? date('H:i:s', strtotime($row['waktu_masuk'])) : '-' ?></td>
                                    <td><?= !empty($row['waktu_pulang']) ? date('H:i:s', strtotime($row['waktu_pulang'])) : '-' ?></td>
                                    <td><?= esc($row['jam_kerja'] ?? '-') ?></td>
                                    <td class="text-danger"><?= !empty($row['terlambat']) ? $row['terlambat'] . ' Menit' : '-' ?></td>
                                    <td class="text-center">
                                        <span class="badge badge-<?= ($row['status'] ?? '') == 'Hadir' ? 'success' : 'warning' ?>"><?= esc($row['status'] ?? 'Hadir') ?></span>
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
