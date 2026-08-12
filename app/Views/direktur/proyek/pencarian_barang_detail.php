<?php
$title = $title ?? 'Detail Penugasan: ' . esc($p['judul']);
$templateData = [
    'title' => $title,
    'user'  => session()->get('user') ?? ['name' => 'Direktur', 'role' => 'direktur'],
    'active' => 'proyek'
];

echo view('direktur/templates/header', $templateData);
echo view('direktur/templates/sidebar', $templateData);
echo view('direktur/templates/navbar', $templateData);
?>

<?php
    $status = strtolower($p['status']);
    $statusPillClass = 'status-pill-baru';
    $statusIcon = 'fas fa-info-circle';
    $statusLabel = 'Baru';

    if ($status === 'proses') {
        $statusPillClass = 'status-pill-proses';
        $statusIcon = 'fas fa-spinner fa-spin';
        $statusLabel = 'Diproses';
    } elseif ($status === 'selesai') {
        $statusPillClass = 'status-pill-selesai';
        $statusIcon = 'fas fa-check-circle';
        $statusLabel = 'Selesai';
    } elseif ($status === 'batal') {
        $statusPillClass = 'status-pill-batal';
        $statusIcon = 'fas fa-times-circle';
        $statusLabel = 'Dibatalkan';
    }
    
    // Waktu & Durasi
    $tglMulai = !empty($p['tanggal_mulai']) ? $p['tanggal_mulai'] : date('Y-m-d', strtotime($p['created_at'] ?? 'now'));
    $jamMulai = !empty($p['jam_mulai']) ? $p['jam_mulai'] : '08:00';
    $tglDeadline = !empty($p['batas_waktu']) ? $p['batas_waktu'] : date('Y-m-d', strtotime($tglMulai . ' +2 days'));
    $jamDeadline = !empty($p['jam_deadline']) ? $p['jam_deadline'] : '17:00';

    $tsMulai = strtotime($tglMulai . ' ' . $jamMulai);
    $tsDeadline = strtotime($tglDeadline . ' ' . $jamDeadline);
    $now = time();

    $is_late = ($tsDeadline < $now && $status !== 'selesai' && $status !== 'batal');
    $diffDays = ceil(($tsDeadline - strtotime($tglMulai)) / 86400);

    if ($diffDays <= 1) {
        $diffHours = round(abs($tsDeadline - $tsMulai) / 3600);
        $durasiText = "1 Hari (" . $diffHours . " Jam, s/d Jam " . $jamDeadline . " WIB)";
    } else {
        $durasiText = $diffDays . " Hari (s/d " . date('d M Y', strtotime($tglDeadline)) . " Jam " . $jamDeadline . " WIB)";
    }

    $isOffline = (strtolower($p['tipe_pembelian'] ?? '') === 'offline');
    $tipePembelianLabel = $isOffline ? 'Offline Store (Toko Fisik)' : 'Online Store (Marketplace)';
    $namaToko = !empty($p['nama_toko_marketplace']) ? $p['nama_toko_marketplace'] : ($isOffline ? 'Toko Fisik / Supplier' : 'Tokopedia / Shopee');
    $nominalEstimasi = (!empty($p['nominal_estimasi']) && $p['nominal_estimasi'] > 0) ? 'Rp ' . number_format($p['nominal_estimasi'], 0, ',', '.') : 'Belum Ditentukan (Belum Diketahui)';
?>

<div class="container-fluid py-3 py-md-4 fade-in pb-5">
    <!-- Header Title & Back Button -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light">
        <div class="d-flex align-items-center">
            <a href="<?= base_url('direktur/proyek/pencarian-barang') ?>" class="btn btn-outline-secondary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;" title="Kembali">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h4 class="mb-0 fw-bold text-dark">Detail Penugasan Pencarian Barang & RAB</h4>
                <small class="text-muted">Rincian spesifikasi barang, rentang deadline, dan hasil laporan harga</small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('direktur/proyek/pencarian-barang/edit/'.$p['id']) ?>" class="btn btn-warning rounded-pill px-3.5 shadow-sm text-sm fw-semibold">
                <i class="fas fa-edit me-1.5"></i> Edit Penugasan
            </a>
            <a href="<?= base_url('direktur/proyek/pencarian-barang') ?>" class="btn btn-secondary rounded-pill px-3.5 shadow-sm text-sm fw-semibold">
                <i class="fas fa-list me-1.5"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Panel Utama Detail (Kiri) -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 rounded-4 shadow-sm p-4 mb-4 bg-white">
                <div class="d-flex align-items-center justify-content-between pb-3 border-bottom mb-4 flex-wrap gap-2">
                    <div>
                        <h4 class="fw-bold text-dark mb-1"><?= esc($p['judul']) ?></h4>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <span class="status-pill <?= $statusPillClass ?>">
                                <i class="<?= $statusIcon ?> me-1"></i> <?= $statusLabel ?>
                            </span>
                            <?php if(!empty($p['is_approved_keuangan'])): ?>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 text-xs fw-bold">
                                    <i class="fas fa-check-double me-1"></i> Disetujui & Masuk Keuangan Pembelian
                                </span>
                            <?php endif; ?>
                            <?php if($is_late): ?>
                                <span class="badge bg-danger rounded-pill px-3 py-1"><i class="fas fa-exclamation-triangle me-1"></i> Terlambat</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block fw-semibold text-uppercase" style="font-size: 0.75rem;">Deadline Akhir Penugasan</small>
                        <span class="fw-bold fs-6 <?= $is_late ? 'text-danger' : 'text-dark' ?>"><i class="fas fa-calendar-alt me-1 text-primary"></i> <?= date('d F Y', strtotime($tglDeadline)) ?> (Jam <?= $jamDeadline ?> WIB)</span>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold text-dark text-xs text-uppercase mb-2"><i class="fas fa-align-left me-1.5 text-primary"></i> Deskripsi & Spesifikasi Detail Barang</h6>
                    <div class="p-3.5 bg-light rounded-3 border-start border-4 border-primary text-secondary fs-6" style="white-space: pre-line; line-height: 1.6;">
                        <?= esc($p['deskripsi'] ?: 'Tidak ada deskripsi spesifikasi.') ?>
                    </div>
                </div>

                <div class="mt-4">
                    <h6 class="fw-bold text-dark text-xs text-uppercase mb-2"><i class="fas fa-file-invoice-dollar me-1.5 text-success"></i> Hasil Laporan Harga / Jawaban Karyawan</h6>
                    <div class="p-4 bg-success bg-opacity-10 border border-success border-opacity-25 rounded-3 text-dark fs-6 mb-3" style="white-space: pre-line; line-height: 1.6;">
                        <?php if (!empty($p['hasil_pencarian'])): ?>
                            <?= esc($p['hasil_pencarian']) ?>
                        <?php elseif (!empty($p['nominal_estimasi']) && $p['nominal_estimasi'] > 0): ?>
                            <strong>Estimasi Harga RAB:</strong> Rp <?= number_format($p['nominal_estimasi'], 0, ',', '.') ?>
                        <?php else: ?>
                            Belum ada laporan hasil pencarian barang yang diserahkan oleh karyawan.
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($p['lampiran_hasil'])): ?>
                        <?php
                            $lampiranFile = $p['lampiran_hasil'];
                            $ext = strtolower(pathinfo($lampiranFile, PATHINFO_EXTENSION));
                            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            
                            $fileUrl = base_url('uploads/pencarian_barang/' . $lampiranFile);
                            if (!file_exists(FCPATH . 'uploads/pencarian_barang/' . $lampiranFile) && file_exists(FCPATH . 'uploads/' . $lampiranFile)) {
                                $fileUrl = base_url('uploads/' . $lampiranFile);
                            }
                        ?>
                        <div class="p-3 bg-white border border-light rounded-3 shadow-sm">
                            <h6 class="fw-bold text-dark text-xs text-uppercase mb-2.5">
                                <i class="fas fa-paperclip text-primary me-1.5"></i> Lampiran Gambar / Foto Barang Admin:
                            </h6>
                            <?php if ($isImage): ?>
                                <div class="mb-3 text-center bg-light p-2.5 rounded-3 border">
                                    <a href="<?= $fileUrl ?>" target="_blank" title="Klik untuk membuka gambar ukuran penuh">
                                        <img src="<?= $fileUrl ?>" alt="Hasil Pencarian Barang" class="img-fluid rounded-3 shadow-sm" style="max-height: 420px; width: auto; object-fit: contain; border: 1px solid #e2e8f0;">
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <span class="small text-dark fw-semibold"><i class="fas fa-image me-1.5 text-primary"></i> <?= esc($lampiranFile) ?></span>
                                <a href="<?= $fileUrl ?>" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3.5 fw-semibold shadow-sm">
                                    <i class="fas fa-external-link-alt me-1.5"></i> Buka / Unduh Lampiran Full
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Panel Samping Meta Information & Action Approve (Kanan) -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
                <h6 class="fw-bold text-dark text-xs text-uppercase mb-3 pb-2 border-bottom"><i class="fas fa-info-circle me-1.5 text-info"></i> Ringkasan Parameter Penugasan</h6>

                <div class="mb-3 p-3 bg-light rounded-3">
                    <small class="text-muted fw-semibold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Ditugaskan Kepada</small>
                    <div class="fw-bold text-dark fs-6"><i class="fas fa-user-tie me-2 text-primary"></i> <?= esc($p['ditugaskan_kepada']) ?></div>
                </div>

                <div class="mb-3 p-3 bg-light rounded-3">
                    <small class="text-muted fw-semibold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Tempat & Platform Pembelian</small>
                    <div class="fw-bold text-dark fs-6 mb-1"><i class="fas fa-store me-2 text-info"></i> <?= esc($namaToko) ?></div>
                    <span class="badge <?= $isOffline ? 'bg-warning text-dark' : 'bg-info text-white' ?> rounded-pill text-xs px-2.5 py-1">
                        <?= $tipePembelianLabel ?>
                    </span>
                </div>

                <div class="mb-3 p-3 bg-light rounded-3">
                    <small class="text-muted fw-semibold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Estimasi Total RAB (Rp)</small>
                    <div class="fw-bold text-success fs-5"><i class="fas fa-coins me-1.5"></i> <?= $nominalEstimasi ?></div>
                </div>

                <div class="mb-4 p-3 bg-light rounded-3">
                    <small class="text-muted fw-semibold text-uppercase d-block mb-1" style="font-size: 0.7rem;">Rentang Waktu & Durasi</small>
                    <div class="small text-dark">
                        <div><i class="fas fa-play me-1.5 text-success"></i> Mulai: <strong><?= date('d M Y', strtotime($tglMulai)) ?> (<?= $jamMulai ?> WIB)</strong></div>
                        <div><i class="fas fa-flag-checkered me-1.5 text-warning"></i> Deadline: <strong><?= date('d M Y', strtotime($tglDeadline)) ?> (<?= $jamDeadline ?> WIB)</strong></div>
                        <small class="text-muted d-block mt-1"><i class="fas fa-hourglass-half me-1"></i> Durasi: <?= $durasiText ?></small>
                    </div>
                </div>

                <hr class="my-4">

                <?php if (strtolower($p['status']) === 'selesai' && empty($p['is_approved_keuangan'])): ?>
                    <button type="button" onclick="confirmApproveKeuangan(<?= $p['id'] ?>, '<?= esc($p['judul'], 'js') ?>')" class="btn btn-success btn-lg rounded-pill w-100 fw-bold shadow-sm py-3">
                        <i class="fas fa-paper-plane me-2"></i> Approve & Kirim Ke Keuangan Pembelian
                    </button>
                <?php elseif (!empty($p['is_approved_keuangan'])): ?>
                    <a href="<?= base_url('direktur/keuangan/pembelian') ?>" class="btn btn-outline-success btn-lg rounded-pill w-100 fw-bold shadow-sm py-3 text-center">
                        <i class="fas fa-external-link-alt me-2"></i> Lihat di Keuangan Pembelian
                    </a>
                <?php else: ?>
                    <div class="alert alert-warning py-3 px-3 mb-0 rounded-3 text-xs text-center border-warning border-opacity-25 shadow-sm">
                        <i class="fas fa-clock me-1.5 fs-6 text-warning"></i><br>
                        <strong>Menunggu Karyawan Memproses:</strong><br>
                        Tombol Approve Keuangan akan aktif setelah karyawan (<strong><?= esc($p['ditugaskan_kepada']) ?></strong>) selesai mencari barang & menyerahkan laporan harga.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmApproveKeuangan(id, judul) {
        Swal.fire({
            title: 'Approve & Kirim ke Keuangan?',
            text: 'Penugasan RAB "' + judul + '" akan disetujui dan otomatis diteruskan ke modul Pengajuan Pembelian Keuangan.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-check-circle me-1"></i> Ya, Approve & Kirim!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= base_url('direktur/proyek/pencarian-barang/approve-keuangan') ?>/' + id;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>

<?= view('direktur/templates/footer', $templateData ?? []) ?>
