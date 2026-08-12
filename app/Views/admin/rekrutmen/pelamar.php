<div class="main-content" style="margin-left: 250px; padding: 25px;">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1 fw-bold text-dark"><i class="fas fa-briefcase text-primary me-2"></i> Lowongan & Pelamar Kerja</h4>
                <p class="text-muted small mb-0">Kelola posting lowongan pekerjaan dan daftar pelamar (Rekrutmen HRD)</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahPelamar">
                <i class="fas fa-plus me-1"></i> Tambah Pelamar Baru
            </button>
        </div>

        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nama Pelamar</th>
                                <th>Posisi Dilamar</th>
                                <th>Kontak</th>
                                <th>Tanggal Melamar</th>
                                <th>Status Process</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($pelamarList)): foreach($pelamarList as $idx => $p): ?>
                                <tr>
                                    <td><?= $idx + 1 ?></td>
                                    <td class="fw-bold"><?= esc($p['nama']) ?></td>
                                    <td><span class="badge bg-info text-dark"><?= esc($p['posisi']) ?></span></td>
                                    <td>
                                        <small class="d-block"><i class="fas fa-envelope text-muted me-1"></i> <?= esc($p['email']) ?></small>
                                        <small class="d-block"><i class="fas fa-phone text-muted me-1"></i> <?= esc($p['telepon']) ?></small>
                                    </td>
                                    <td><?= date('d M Y', strtotime($p['tanggal'])) ?></td>
                                    <td>
                                        <?php if($p['status'] == 'Applied'): ?>
                                            <span class="badge bg-secondary">Applied</span>
                                        <?php elseif($p['status'] == 'Interview'): ?>
                                            <span class="badge bg-warning text-dark">Interview</span>
                                        <?php elseif($p['status'] == 'Offer'): ?>
                                            <span class="badge bg-success">Offer Extended</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i> Detail</button>
                                        <button class="btn btn-sm btn-outline-success"><i class="fas fa-check"></i> Process</button>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data pelamar.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
