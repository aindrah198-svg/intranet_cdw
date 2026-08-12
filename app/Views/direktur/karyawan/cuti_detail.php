<?php
$data = [
    'title'  => 'Detail Pengajuan Cuti Karyawan',
    'active' => 'karyawan',
    'user'   => ['name' => session()->get('name') ?? 'Direktur', 'role' => 'direktur']
];

echo view('direktur/templates/header', $data);
echo view('direktur/templates/sidebar', $data);
echo view('direktur/templates/navbar', $data);
?>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= base_url('direktur/karyawan/cuti') ?>" class="text-decoration-none text-muted">Cuti Karyawan</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Detail & Approval</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-umbrella-beach text-primary me-2"></i> Detail & Persetujuan Cuti</h4>
            <small class="text-muted">Pratinjau detail permohonan cuti, sisa jatah cuti tahunan, dan aksi persetujuan Direktur.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('direktur/karyawan/cuti') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali
            </a>
            <a href="<?= base_url('direktur/karyawan/cuti/edit/'.$c['id']) ?>" class="btn btn-warning text-white rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-edit me-1.5"></i> Edit Cuti
            </a>
        </div>
    </div>

    <!-- Main Detail Card -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                <div class="card-header text-white py-3.5 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e3c72, #2a5298);">
                    <div>
                        <h5 class="card-title fs-5 fw-bold mb-1"><i class="fas fa-umbrella-beach me-2"></i> Permohonan Cuti (<?= esc($c['jenis_cuti']) ?>)</h5>
                        <small class="text-white-50"><i class="fas fa-hashtag me-1"></i> Nomor: <?= esc($c['nomor_cuti']) ?></small>
                    </div>
                    <?php
                        $st = strtolower($c['status'] ?? 'menunggu');
                        $badge = 'bg-warning text-dark';
                        if ($st === 'disetujui') $badge = 'bg-success text-white';
                        if ($st === 'ditolak') $badge = 'bg-danger text-white';
                    ?>
                    <span class="badge <?= $badge ?> rounded-pill px-3 py-1.5 fs-6 fw-bold shadow-sm">
                        <?= strtoupper(esc($c['status'] ?? 'Menunggu')) ?>
                    </span>
                </div>
                <div class="card-body p-4">
                    
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-user me-1 text-primary"></i> Nama Pemohon</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1"><?= esc($c['nama_lengkap'] ?: 'Admin / Karyawan') ?></h6>
                                <small class="text-muted text-xs d-block"><?= esc($c['divisi'] ?: 'Administrasi') ?> - <?= esc($c['jabatan'] ?: 'Staff') ?></small>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-calendar-alt me-1 text-info"></i> Periode Cuti</small>
                                <h6 class="fw-bold text-dark mb-0 mt-1"><?= (int)$c['lama_hari'] ?> Hari Kerja</h6>
                                <small class="text-muted text-xs d-block"><?= date('d M Y', strtotime($c['tanggal_mulai'])) ?> s/d <?= date('d M Y', strtotime($c['tanggal_selesai'])) ?></small>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <small class="text-muted text-xs uppercase d-block fw-bold mb-1"><i class="fas fa-chart-pie me-1 text-success"></i> Status Sisa Kuota Cuti</small>
                                <?php if($kuota): ?>
                                    <h6 class="fw-bold text-dark mb-0 mt-1"><?= $kuota['sisa_kuota'] ?? $kuota['sisa'] ?? max(0, ($kuota['kuota_tahunan'] ?? 12) - ($kuota['terpakai'] ?? 0)) ?> Hari Tersisa</h6>
                                    <small class="text-muted text-xs d-block">Terpakai: <?= $kuota['terpakai'] ?> hari (Dari <?= $kuota['kuota_tahunan'] ?> hari)</small>
                                <?php else: ?>
                                    <h6 class="fw-bold text-warning mb-0 mt-1">Belum Set Kuota</h6>
                                    <small class="text-muted text-xs d-block">Default limit: 12 hari/tahun</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-align-left text-primary me-2"></i> Alasan Permohonan Cuti</h6>
                        <div class="p-3 bg-light rounded-3 border text-dark text-sm" style="line-height: 1.6;">
                            <?= nl2br(esc($c['alasan'] ?: 'Tidak ada alasan tertulis.')) ?>
                        </div>
                    </div>

                    <?php if (!empty($c['disetujui_oleh']) || !empty($c['alasan_penolakan'])): ?>
                    <div class="p-3 rounded-3 border bg-primary bg-opacity-10 mb-3">
                        <h6 class="fw-bold text-dark mb-1"><i class="fas fa-info-circle text-primary me-2"></i> Catatan Riwayat Approval</h6>
                        <small class="text-muted d-block mb-2">Ditinjau oleh: <strong><?= esc($c['disetujui_oleh'] ?: 'Direktur Utama') ?></strong> pada <?= !empty($c['disetujui_at']) ? date('d M Y H:i', strtotime($c['disetujui_at'])) : '-' ?></small>
                        <?php if (!empty($c['alasan_penolakan'])): ?>
                            <div class="alert alert-danger mb-0 py-2 text-xs">
                                <strong>Catatan Penolakan:</strong> <?= esc($c['alasan_penolakan']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                </div>
                <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4 d-flex justify-content-between align-items-center">
                    <a href="<?= base_url('direktur/karyawan/cuti') ?>" class="btn btn-secondary rounded-pill px-4 font-semibold">Kembali</a>
                    
                    <div class="d-flex gap-2">
                        <a href="<?= base_url('direktur/karyawan/cuti/edit/'.$c['id']) ?>" class="btn btn-warning text-white rounded-pill px-3.5 font-semibold shadow-sm">
                            <i class="fas fa-edit me-1.5"></i> Edit Cuti
                        </a>
                        <?php if(strtolower($c['status'] ?? '') === 'menunggu'): ?>
                            <form action="<?= base_url('direktur/karyawan/cuti/approve/'.$c['id']) ?>" method="POST" class="d-inline">
                                <button type="submit" class="btn btn-success text-white rounded-pill px-4 font-semibold shadow-sm">
                                    <i class="fas fa-check me-1.5"></i> Setujui Cuti Ini
                                </button>
                            </form>
                            <button type="button" onclick="rejectCuti(<?= $c['id'] ?>, '<?= esc($c['nomor_cuti'], 'js') ?>')" class="btn btn-danger text-white rounded-pill px-4 font-semibold shadow-sm">
                                <i class="fas fa-times me-1.5"></i> Tolak Cuti
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function rejectCuti(id, nomor) {
    Swal.fire({
        title: 'Tolak Pengajuan Cuti?',
        text: 'Masukkan alasan penolakan permohonan cuti:',
        input: 'textarea',
        inputPlaceholder: 'Tuliskan alasan penolakan...',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Tolak Cuti',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-4 shadow-lg' }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= base_url('direktur/karyawan/cuti/reject') ?>/' + id;
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'alasan_penolakan';
            input.value = result.value || 'Operasional kantor & beban pekerjaan mampet.';
            form.appendChild(input);

            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>

<?= view('direktur/templates/footer', $data) ?>
