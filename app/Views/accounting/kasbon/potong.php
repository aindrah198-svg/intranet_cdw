<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="fas fa-calculator text-primary me-2"></i> Proses Potong Gaji Kasbon</h4>
                <p class="text-muted mb-0">Eksekusi pemotongan kasbon karyawan ke dalam siklus penggajian bulanan</p>
            </div>
            <a href="<?= base_url('accounting/kasbon') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <div class="card card-custom p-4 mb-4">
            <h6 class="fw-bold mb-3"><i class="fas fa-filter text-secondary me-2"></i> Filter Periode Potongan</h6>
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small text-muted">Bulan</label>
                    <select name="bulan" class="form-select">
                        <?php 
                        $months = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                        foreach ($months as $num => $name):
                        ?>
                            <option value="<?= str_pad($num, 2, '0', STR_PAD_LEFT) ?>" <?= $bulan == str_pad($num, 2, '0', STR_PAD_LEFT) ? 'selected' : '' ?>><?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted">Tahun</label>
                    <select name="tahun" class="form-select">
                        <?php for($y = date('Y')-1; $y <= date('Y')+1; $y++): ?>
                            <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i> Tampilkan</button>
                </div>
            </form>
        </div>

        <div class="card card-custom p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>No. Kasbon</th>
                            <th>Karyawan</th>
                            <th>Nominal Pinjaman</th>
                            <th>Rencana Pelunasan</th>
                            <th>Status Pelunasan</th>
                            <th>Aksi Eksekusi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($potonganList)): foreach ($potonganList as $idx => $p): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td class="fw-bold text-dark"><?= esc($p['nomor_kasbon'] ?? ('KSB-' . $p['id'])) ?></td>
                                <td>
                                    <div class="fw-bold"><?= esc($p['nama_lengkap'] ?? 'Staff') ?></div>
                                    <div class="small text-muted"><?= esc($p['nik'] ?? '-') ?></div>
                                </td>
                                <td class="fw-bold text-primary">Rp <?= number_format($p['jumlah_kasbon'] ?? 0, 0, ',', '.') ?></td>
                                <td><?= esc($p['rencana_pelunasan'] ?? 'Potong Gaji Bulanan') ?></td>
                                <td>
                                    <?php $stAll = strtolower($p['status_keseluruhan'] ?? 'belum'); ?>
                                    <span class="badge bg-<?= $stAll == 'lunas' ? 'success' : 'warning' ?>">
                                        <?= ucfirst($stAll) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($stAll !== 'lunas'): ?>
                                        <a href="<?= base_url('accounting/kasbon/proses-potong/' . $p['id']) ?>" 
                                           onclick="return confirm('Tandai kasbon ini telah dipotong lunas dari penggajian?')" 
                                           class="btn btn-sm btn-success">
                                            <i class="fas fa-check me-1"></i> Mark as Lunas
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small"><i class="fas fa-check-circle text-success me-1"></i> Sudah Lunas</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada kasbon yang perlu dipotong untuk periode ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
