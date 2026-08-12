<div class="main-content" style="margin-left: 250px; padding: 25px;">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1 fw-bold text-dark"><i class="fas fa-user-check text-success me-2"></i> Onboarding Karyawan Baru</h4>
                <p class="text-muted small mb-0">Proses penerimaan kandidat menjadi Karyawan (Generate Staff & Pembuatan Akun)</p>
            </div>
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
                                <th>Nama Kandidat</th>
                                <th>Posisi / Jabatan</th>
                                <th>Divisi Tujuan</th>
                                <th>Rencana Tgl Masuk</th>
                                <th>Status Onboarding</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($onboardingList)): foreach($onboardingList as $idx => $o): ?>
                                <tr>
                                    <td><?= $idx + 1 ?></td>
                                    <td class="fw-bold"><?= esc($o['nama']) ?></td>
                                    <td><?= esc($o['posisi']) ?></td>
                                    <td><span class="badge bg-primary"><?= esc($o['divisi']) ?></span></td>
                                    <td><?= date('d M Y', strtotime($o['tgl_masuk'])) ?></td>
                                    <td><span class="badge bg-success"><?= esc($o['status']) ?></span></td>
                                    <td>
                                        <form action="<?= base_url('admin/rekrutmen/onboarding/process/'.$o['id']) ?>" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-user-plus me-1"></i> Generate Sebagai Staff</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="7" class="text-center text-muted py-4">Belum ada kandidat siap onboarding.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
