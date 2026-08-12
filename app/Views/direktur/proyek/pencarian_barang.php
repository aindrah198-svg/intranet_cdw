<?php
$title = $title ?? 'Pencarian Barang & RAB';
$templateData = [
    'title' => $title,
    'user'  => session()->get('user') ?? ['name' => 'Direktur', 'role' => 'direktur'],
    'active' => 'proyek'
];

echo view('direktur/templates/header', $templateData);
echo view('direktur/templates/sidebar', $templateData);
echo view('direktur/templates/navbar', $templateData);
?>

<style>
    /* Custom Styling Pencarian Barang Modern Cards */
    .employee-card-modern {
        border: none;
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .employee-card-modern:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .avatar-glow {
        width: 52px;
        height: 52px;
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
        box-shadow: 0 4px 14px 0 rgba(59, 130, 246, 0.39);
        font-size: 1.35rem;
    }
    .data-pill-bar {
        background: #f8fafc;
        border-radius: 10px;
        padding: 8px 12px;
        border: 1px solid #f1f5f9;
    }
    .data-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.3px;
        margin-bottom: 2px;
    }
    .data-value {
        font-size: 0.88rem;
        color: #1e293b;
    }
    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.2px;
    }
    .status-pill-baru {
        background: rgba(59, 130, 246, 0.1);
        color: #2563eb;
        border: 1px solid rgba(59, 130, 246, 0.2);
    }
    .status-pill-proses {
        background: rgba(245, 158, 11, 0.1);
        color: #d97706;
        border: 1px solid rgba(245, 158, 11, 0.2);
    }
    .status-pill-selesai {
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }
    .status-pill-batal {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.2);
    }
    .btn-action-pill {
        border-radius: 9999px;
        padding: 6px 14px;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-action-view {
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #cbd5e1;
    }
    .btn-action-view:hover {
        background: #e2e8f0;
        color: #0f172a;
    }
</style>

<div class="container-fluid py-3 py-md-4 fade-in pb-5">
    <!-- Header Title & Tambah Button (Direct Link Page) -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light">
        <div class="d-flex align-items-center">
            <div class="bg-gradient-primary text-white rounded-3 p-2.5 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px;">
                <i class="fas fa-search-dollar fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark">Pencarian Barang & RAB</h4>
                <small class="text-muted d-none d-sm-inline">Delegasikan tugas pencarian harga barang atau pembuatan RAB</small>
            </div>
        </div>
        <a href="<?= base_url('direktur/proyek/pencarian-barang/tambah') ?>" class="btn btn-primary rounded-pill px-3.5 py-2 shadow-sm d-inline-flex align-items-center text-sm fw-semibold">
            <i class="fas fa-plus me-1.5"></i> <span class="d-none d-md-inline">Buat Penugasan Baru</span><span class="d-inline d-md-none">Buat</span>
        </a>
    </div>

    <!-- Search & Filter Bar (Input Group) -->
    <div class="input-group mb-4 shadow-sm rounded-pill overflow-hidden border border-light">
        <span class="input-group-text bg-white border-end-0 text-muted ps-3.5">
            <i class="fas fa-search"></i>
        </span>
        <input type="text" id="searchPencarian" class="form-control border-start-0 border-end-0 ps-1 py-2.5" placeholder="Cari judul pencarian, orang yang ditugaskan, toko/marketplace, atau status...">
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Pencarian Barang Grid (Modern Cards) -->
    <div class="row g-3" id="pencarianCardContainer">
        <?php if (empty($penugasan)): ?>
            <div class="col-12 text-center py-5">
                <div class="text-muted mb-3"><i class="fas fa-search-dollar fa-4x opacity-25"></i></div>
                <h6 class="fw-bold text-dark mb-1">Belum Ada Penugasan Pencarian Barang</h6>
                <p class="text-muted small mb-3">Klik tombol di atas untuk mendelegasikan tugas pencarian harga atau RAB barang.</p>
                <a href="<?= base_url('direktur/proyek/pencarian-barang/tambah') ?>" class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold">
                    <i class="fas fa-plus me-1.5"></i> Buat Penugasan Baru
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($penugasan as $p): ?>
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
                    
                    // Kalkulasi Waktu & Durasi Jam/Hari
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

                    // Tipe Tempat Pembelian (Offline vs Online Marketplace)
                    $isOffline = (strtolower($p['tipe_pembelian'] ?? '') === 'offline');
                    $tipePembelianLabel = $isOffline ? 'Offline Store' : 'Online Store';
                    $namaToko = !empty($p['nama_toko_marketplace']) ? $p['nama_toko_marketplace'] : ($isOffline ? 'Toko Fisik / Supplier' : 'Tokopedia / Shopee');
                    
                    // Nominal Estimasi Total
                    $nominalEstimasi = (!empty($p['nominal_estimasi']) && $p['nominal_estimasi'] > 0) ? 'Rp ' . number_format($p['nominal_estimasi'], 0, ',', '.') : 'Belum Ditentukan';
                    
                    $initial = strtoupper(substr($p['ditugaskan_kepada'], 0, 1));
                ?>
                <div class="col-12 col-xl-6 pencarian-card-wrapper" data-status="<?= esc($status) ?>">
                    <div class="card employee-card-modern p-3 p-sm-4 h-100 <?= $is_late ? 'border-danger' : '' ?>">
                        
                        <!-- Visual Header Kartu -->
                        <div class="d-flex align-items-center justify-content-between pb-3 border-bottom border-light flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-3">
                                <!-- Avatar Lingkaran Inisial -->
                                <div class="avatar-glow text-white rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" title="Ditugaskan kepada: <?= esc($p['ditugaskan_kepada']) ?>">
                                    <?= $initial ?>
                                </div>
                                <div style="min-width:0;">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <!-- Judul Pencarian -->
                                        <h3 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 1.15rem; letter-spacing: -0.2px;">
                                            <?= esc($p['judul']) ?>
                                        </h3>
                                    </div>
                                    <div class="text-muted small mt-1">
                                        Ditugaskan kepada: <strong><?= esc($p['ditugaskan_kepada']) ?></strong>
                                    </div>
                                    <!-- Lencana Status Chip Frosted Glass -->
                                    <div class="mt-2 d-flex align-items-center gap-2 flex-wrap">
                                        <span class="status-pill <?= $statusPillClass ?>">
                                            <i class="<?= $statusIcon ?> me-1"></i> <?= $statusLabel ?>
                                        </span>
                                        <?php if(!empty($p['is_approved_keuangan'])): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2.5 py-1 text-xs fw-bold">
                                                <i class="fas fa-check-double me-1"></i> Disetujui & Masuk Keuangan Pembelian
                                            </span>
                                        <?php endif; ?>
                                        <?php if($is_late): ?>
                                            <span class="badge bg-danger rounded-pill"><i class="fas fa-exclamation-triangle me-1"></i> Terlambat</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Penataan Bidang Data (Bilah Data Horizontal Grid) -->
                        <div class="py-3 flex-grow-1">
                            <div class="row g-2.5">
                                <div class="col-sm-6">
                                    <div class="data-pill-bar">
                                        <div class="data-label">
                                            <i class="fas fa-clock text-primary"></i> Rentang Waktu & Deadline
                                        </div>
                                        <div class="data-value <?= $is_late ? 'text-danger fw-bold' : '' ?>" style="font-weight: 500; font-size: 0.85rem;">
                                            <div><i class="fas fa-play me-1 text-success"></i> <?= date('d M Y', strtotime($tglMulai)) ?> (<?= $jamMulai ?> WIB)</div>
                                            <div><i class="fas fa-flag-checkered me-1 text-warning"></i> <?= date('d M Y', strtotime($tglDeadline)) ?> (<?= $jamDeadline ?> WIB)</div>
                                            <small class="text-muted d-block mt-0.5"><i class="fas fa-hourglass-half me-1"></i> <?= $durasiText ?></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="data-pill-bar">
                                        <div class="data-label">
                                            <i class="fas fa-store text-info"></i> Tempat & Platform Pembelian
                                        </div>
                                        <div class="data-value" style="font-weight: 600; font-size: 0.88rem;">
                                            <div>
                                                <?php if($isOffline): ?>
                                                    <span class="badge bg-warning bg-opacity-10 text-dark border border-warning rounded-pill px-2 py-0.5 text-xs"><i class="fas fa-building me-1"></i> <?= $tipePembelianLabel ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-info bg-opacity-10 text-info border border-info rounded-pill px-2 py-0.5 text-xs"><i class="fas fa-shopping-cart me-1"></i> <?= $tipePembelianLabel ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-dark mt-1"><i class="fas fa-shopping-bag me-1 text-muted"></i> <?= esc($namaToko) ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="data-pill-bar">
                                        <div class="data-label">
                                            <i class="fas fa-coins text-success"></i> Estimasi Total RAB (Rp)
                                        </div>
                                        <div class="data-value text-success fw-bold" style="font-size: 1rem;">
                                            <?= $nominalEstimasi ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="data-pill-bar">
                                        <div class="data-label">
                                            <i class="fas fa-file-invoice-dollar text-success"></i> Hasil Laporan Harga
                                        </div>
                                        <div class="data-value" style="font-weight: 500;">
                                            <?php if(!empty($p['hasil_pencarian'])): ?>
                                                <a href="<?= base_url('direktur/proyek/pencarian-barang/detail/'.$p['id']) ?>" class="btn btn-sm btn-link text-success p-0 text-decoration-none fw-bold">
                                                    <i class="fas fa-eye me-1"></i> Lihat Hasil Pencarian
                                                </a>
                                            <?php elseif(!empty($p['nominal_estimasi']) && $p['nominal_estimasi'] > 0): ?>
                                                <span class="text-success fw-bold">
                                                    <i class="fas fa-tag me-1"></i> Rp <?= number_format($p['nominal_estimasi'], 0, ',', '.') ?>
                                                </span>
                                            <?php else: ?>
                                                <i class="text-muted small">- Menunggu Hasil -</i>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="data-pill-bar">
                                        <div class="data-label">
                                            <i class="fas fa-align-left text-info"></i> Deskripsi Penugasan
                                        </div>
                                        <div class="data-value" style="font-weight: 500; font-size: 0.85rem; max-height: 3.6em; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                            <?= esc($p['deskripsi'] ?: 'Tidak ada deskripsi') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tombol Aksi Halaman Baru (Detail Page, Edit Page, Approve Keuangan, Hapus) -->
                        <div class="pt-3 border-top border-light mt-auto d-flex align-items-center justify-content-end gap-1.5 flex-wrap">
                            <a href="<?= base_url('direktur/proyek/pencarian-barang/detail/'.$p['id']) ?>" class="btn-action-pill btn-action-view" title="Detail Penugasan">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                            <a href="<?= base_url('direktur/proyek/pencarian-barang/edit/'.$p['id']) ?>" class="btn-action-pill text-warning border-warning bg-warning bg-opacity-10" title="Edit Penugasan">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            
                            <?php if (strtolower($p['status']) === 'selesai' && empty($p['is_approved_keuangan'])): ?>
                                <button type="button" onclick="confirmApproveKeuangan(<?= $p['id'] ?>, '<?= esc($p['judul'], 'js') ?>')" class="btn-action-pill text-success border-success bg-success bg-opacity-10" title="Approve & Teruskan ke Pembelian Keuangan">
                                    <i class="fas fa-check-double"></i> Approve Keuangan
                                </button>
                            <?php endif; ?>

                            <button type="button" onclick="confirmDeletePencarian(<?= $p['id'] ?>, '<?= esc($p['judul'], 'js') ?>')" class="btn-action-pill text-secondary border-secondary bg-secondary bg-opacity-10" title="Hapus Penugasan">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("searchPencarian");
        if (searchInput) {
            searchInput.addEventListener("keyup", function() {
                const keyword = this.value.toLowerCase();
                const cards = document.querySelectorAll(".pencarian-card-wrapper");
                
                cards.forEach(card => {
                    const textContent = card.textContent.toLowerCase();
                    if (textContent.includes(keyword)) {
                        card.style.display = "";
                    } else {
                        card.style.display = "none";
                    }
                });
            });
        }
    });

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

    function confirmDeletePencarian(id, judul) {
        Swal.fire({
            title: 'Hapus Penugasan?',
            text: 'Tugas pencarian barang "' + judul + '" akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash me-1"></i> Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-4 shadow-lg' }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= base_url('direktur/proyek/pencarian-barang/delete') ?>/' + id;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>

<?= view('direktur/templates/footer', $templateData ?? []) ?>
