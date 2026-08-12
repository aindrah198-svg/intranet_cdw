<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="fas fa-fingerprint text-primary me-2"></i> Absen Masuk / Pulang</h4>
                <p class="text-muted mb-0">Presensi Kehadiran Harian Staff CDW Engineering</p>
            </div>
            <a href="<?= base_url('staff/absensi/riwayat') ?>" class="btn btn-outline-secondary btn-sm"><i class="fas fa-history me-1"></i> Lihat Riwayat</a>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show"><?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('info')): ?>
            <div class="alert alert-info alert-dismissible fade show"><?= session()->getFlashdata('info') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <div class="row g-4 justify-content-center">
            <div class="col-md-6">
                <!-- Clock Card -->
                <div class="card card-custom p-4 text-center mb-4">
                    <h6 class="text-muted fw-semibold mb-2">Waktu Real-time</h6>
                    <h1 class="display-4 fw-bold text-primary mb-1" id="liveClock"><?= date('H:i:s') ?></h1>
                    <p class="text-secondary fw-semibold mb-0"><?= date('l, d F Y') ?></p>
                </div>

                <!-- Form Check-in / Check-out -->
                <div class="card card-custom p-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Status Presensi Hari Ini</h5>
                    
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded mb-2">
                            <span><i class="fas fa-sign-in-alt text-success me-2"></i> Jam Masuk:</span>
                            <strong class="fs-5 text-dark"><?= !empty($absensiHariIni['waktu_masuk']) ? esc(substr($absensiHariIni['waktu_masuk'], 0, 5)) : 'Belum Absen' ?></strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded">
                            <span><i class="fas fa-sign-out-alt text-danger me-2"></i> Jam Pulang:</span>
                            <strong class="fs-5 text-dark"><?= !empty($absensiHariIni['waktu_keluar']) ? esc(substr($absensiHariIni['waktu_keluar'], 0, 5)) : 'Belum Absen' ?></strong>
                        </div>
                    </div>

                    <div class="d-grid gap-3">
                        <?php if (empty($absensiHariIni['waktu_masuk'])): ?>
                            <form action="<?= base_url('staff/absensi/checkin') ?>" method="POST">
                                <?= csrf_field() ?>
                                <input type="hidden" name="lokasi_masuk" value="Kantor CDW Engineering">
                                <button type="submit" class="btn btn-success btn-lg w-100 py-3 fw-bold shadow-sm">
                                    <i class="fas fa-check-circle me-2"></i> ABSEN MASUK SEKARANG
                                </button>
                            </form>
                        <?php elseif (empty($absensiHariIni['waktu_keluar'])): ?>
                            <form action="<?= base_url('staff/absensi/checkout') ?>" method="POST">
                                <?= csrf_field() ?>
                                <input type="hidden" name="lokasi_keluar" value="Kantor CDW Engineering">
                                <button type="submit" class="btn btn-danger btn-lg w-100 py-3 fw-bold shadow-sm">
                                    <i class="fas fa-sign-out-alt me-2"></i> ABSEN PULANG SEKARANG
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-success text-center mb-0">
                                <i class="fas fa-check-double me-2"></i> Anda telah menyelesaikan Presensi Masuk & Pulang hari ini. Terima kasih atas kerja keras Anda!
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    setInterval(() => {
        const now = new Date();
        document.getElementById('liveClock').innerText = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }, 1000);
</script>
