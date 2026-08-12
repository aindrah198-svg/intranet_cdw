<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="fas fa-history text-primary me-2"></i> Riwayat Absensi Saya</h4>
                <p class="text-muted mb-0">Rekapitulasi kehadiran personal per bulan</p>
            </div>
            <a href="<?= base_url('staff/absensi') ?>" class="btn btn-primary btn-sm"><i class="fas fa-clock me-1"></i> Absen Hari Ini</a>
        </div>

        <!-- Filter Card -->
        <div class="card card-custom p-3 mb-4">
            <form action="<?= base_url('staff/absensi/riwayat') ?>" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small font-semibold">Pilih Bulan</label>
                    <select name="bulan" class="form-select">
                        <?php 
                        $bulanList = [
                            '01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April',
                            '05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus',
                            '09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'
                        ];
                        foreach($bulanList as $k => $v): ?>
                            <option value="<?= $k ?>" <?= $bulan == $k ? 'selected' : '' ?>><?= $v ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small font-semibold">Pilih Tahun</label>
                    <select name="tahun" class="form-select">
                        <?php for($y = date('Y'); $y >= date('Y')-2; $y--): ?>
                            <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i> Tampilkan Riwayat</button>
                </div>
            </form>
        </div>

        <!-- Data Table -->
        <div class="card card-custom p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Jam Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Lokasi Masuk</th>
                            <th>Status Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($riwayat)): foreach ($riwayat as $idx => $r): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td class="fw-bold"><?= date('d M Y', strtotime($r['tanggal'])) ?></td>
                                <td><span class="badge bg-success bg-opacity-10 text-success fs-6"><?= !empty($r['waktu_masuk']) ? esc(substr($r['waktu_masuk'], 0, 5)) : '-' ?></span></td>
                                <td><span class="badge bg-danger bg-opacity-10 text-danger fs-6"><?= !empty($r['waktu_keluar']) ? esc(substr($r['waktu_keluar'], 0, 5)) : '-' ?></span></td>
                                <td><small class="text-muted"><?= esc($r['lokasi_masuk'] ?? 'Office') ?></small></td>
                                <td>
                                    <span class="badge bg-<?= ($r['status'] ?? '') == 'Hadir' ? 'success' : (($r['status'] ?? '') == 'Terlambat' ? 'warning' : 'danger') ?>">
                                        <?= esc($r['status'] ?? 'Hadir') ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data kehadiran pada periode ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
