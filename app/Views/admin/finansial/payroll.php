<div class="main-content" style="margin-left: 250px; padding: 25px;">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1 fw-bold text-dark"><i class="fas fa-calculator text-success me-2"></i> Payroll & Komponen Perhitungan Gaji</h4>
                <p class="text-muted small mb-0">Kelola komponen Gaji Pokok + Tunjangan + Lembur − Potongan per Karyawan (Pengiriman Data ke Accounting)</p>
            </div>
            <button class="btn btn-outline-success"><i class="fas fa-file-excel me-1"></i> Export Rekap Payroll</button>
        </div>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>NIK</th>
                                <th>Nama Karyawan</th>
                                <th>Divisi / Jabatan</th>
                                <th>Gaji Pokok</th>
                                <th>Tunjangan</th>
                                <th>Potongan (Absen/Kasbon)</th>
                                <th>Estimasi Gaji Bersih</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($karyawanList)): foreach($karyawanList as $idx => $k): ?>
                                <tr>
                                    <td><?= $idx + 1 ?></td>
                                    <td><code><?= esc($k['nik']) ?></code></td>
                                    <td class="fw-bold"><?= esc($k['nama_lengkap']) ?></td>
                                    <td><?= esc($k['divisi']) ?> - <?= esc($k['jabatan']) ?></td>
                                    <td>Rp 5.000.000</td>
                                    <td>Rp 750.000</td>
                                    <td class="text-danger">- Rp 0</td>
                                    <td class="fw-bold text-success">Rp 5.750.000</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i> Set Komponen</button>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="9" class="text-center text-muted py-4">Belum ada data karyawan aktif.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
