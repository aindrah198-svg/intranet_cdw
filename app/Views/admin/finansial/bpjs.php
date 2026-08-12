<div class="main-content" style="margin-left: 250px; padding: 25px;">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1 fw-bold text-dark"><i class="fas fa-notes-medical text-primary me-2"></i> BPJS Kesehatan & Ketenagakerjaan</h4>
                <p class="text-muted small mb-0">Kelola nomor kepesertaan, iuran perusahaan & potongan karyawan untuk BPJS</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nama Karyawan</th>
                                <th>No BPJS Kesehatan</th>
                                <th>No BPJS Ketenagakerjaan</th>
                                <th>Iuran Perusahaan</th>
                                <th>Potongan Karyawan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($karyawanList)): foreach($karyawanList as $idx => $k): ?>
                                <tr>
                                    <td><?= $idx + 1 ?></td>
                                    <td class="fw-bold"><?= esc($k['nama_lengkap']) ?></td>
                                    <td><code>00012345678<?= $idx ?></code></td>
                                    <td><code>99987654321<?= $idx ?></code></td>
                                    <td>Rp 200.000</td>
                                    <td>Rp 50.000</td>
                                    <td><span class="badge bg-success">Aktif</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i> Edit BPJS</button>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data kepesertaan BPJS.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
