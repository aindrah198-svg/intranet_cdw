<?= view('admin/templates/header') ?>
<?= view('admin/templates/sidebar') ?>
<?= view('admin/templates/navbar') ?>

<?php
$countTotal       = $countTotal ?? count($suratList);
$countDiterbitkan = $countDiterbitkan ?? 0;
$countDraft       = $countDraft ?? 0;
$countDibatalkan  = $countDibatalkan ?? 0;
$logoBase64       = $logoBase64 ?? '';
$paperSizes       = $paperSizes ?? [
    'A4' => 'A4 (210 x 297 mm)',
    'A3' => 'A3 (297 x 420 mm)',
    'Letter' => 'Letter (216 x 279 mm)',
    'Legal' => 'Legal (216 x 356 mm)',
    'Folio' => 'F4 / Folio (215 x 330 mm)',
];
?>

<style>
    /* Styling Premium Modern Material & Glassmorphism */
    .employee-card-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(30, 60, 114, 0.06), 0 2px 6px rgba(0, 0, 0, 0.02) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .employee-card-modern:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 32px rgba(30, 60, 114, 0.12), 0 4px 10px rgba(0, 0, 0, 0.04) !important;
    }

    /* Avatar Soft Glowing Ring */
    .avatar-glow {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        box-shadow: 0 4px 14px rgba(30, 60, 114, 0.35);
        border: 2px solid rgba(255, 255, 255, 0.9);
        width: 50px;
        height: 50px;
        font-size: 1.3rem;
    }

    .avatar-glow-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        box-shadow: 0 4px 14px rgba(245, 158, 11, 0.35);
        border: 2px solid rgba(255, 255, 255, 0.9);
        width: 50px;
        height: 50px;
        font-size: 1.3rem;
    }

    .avatar-glow-danger {
        background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
        box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35);
        border: 2px solid rgba(255, 255, 255, 0.9);
        width: 50px;
        height: 50px;
        font-size: 1.3rem;
    }

    .avatar-glow-info {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        box-shadow: 0 4px 14px rgba(6, 182, 212, 0.35);
        border: 2px solid rgba(255, 255, 255, 0.9);
        width: 50px;
        height: 50px;
        font-size: 1.3rem;
    }

    /* Status Pill Frosted Glass */
    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 5px 12px;
        border-radius: 30px;
        font-size: 0.76rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    
    .status-pill-active {
        background: rgba(25, 135, 84, 0.12);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.25);
    }
    
    .status-pill-inactive {
        background: rgba(220, 53, 69, 0.12);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.25);
    }

    .status-pill-draft {
        background: rgba(255, 193, 7, 0.15);
        color: #b58100;
        border: 1px solid rgba(255, 193, 7, 0.3);
    }

    /* Jenis Surat Pills */
    .jenis-pill {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        letter-spacing: 0.4px;
        text-transform: uppercase;
    }
    .jenis-kontrak { background: rgba(13, 110, 253, 0.12); color: #0d6efd; border: 1px solid rgba(13, 110, 253, 0.25); }
    .jenis-sp { background: rgba(220, 53, 69, 0.12); color: #dc3545; border: 1px solid rgba(220, 53, 69, 0.25); }
    .jenis-keterangan { background: rgba(25, 135, 84, 0.12); color: #198754; border: 1px solid rgba(25, 135, 84, 0.25); }
    .jenis-tugas { background: rgba(112, 51, 255, 0.12); color: #6f42c1; border: 1px solid rgba(112, 51, 255, 0.25); }
    .jenis-default { background: rgba(108, 117, 125, 0.12); color: #495057; border: 1px solid rgba(108, 117, 125, 0.25); }

    /* Bilah Data Horizontal */
    .data-pill-bar {
        background: #f8fafc;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 12px;
        padding: 10px 14px;
        transition: all 0.2s ease;
        height: 100%;
    }
    .data-pill-bar:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    .data-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 2px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .data-value {
        font-size: 0.92rem;
        font-weight: 600;
        color: #0f172a;
    }

    /* Action Pills */
    .btn-action-pill {
        border-radius: 20px;
        padding: 7px 16px;
        font-size: 0.82rem;
        font-weight: 600;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid transparent;
        text-decoration: none;
    }

    .btn-action-view {
        background: rgba(30, 60, 114, 0.08);
        color: #1e3c72;
        border-color: rgba(30, 60, 114, 0.2);
    }
    .btn-action-view:hover {
        background: #1e3c72;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(30, 60, 114, 0.3);
    }

    .btn-action-edit {
        background: rgba(255, 193, 7, 0.12);
        color: #b58100;
        border-color: rgba(255, 193, 7, 0.3);
    }
    .btn-action-edit:hover {
        background: #ffc107;
        color: #000000;
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
    }

    .btn-action-delete {
        background: rgba(220, 53, 69, 0.08);
        color: #dc3545;
        border-color: rgba(220, 53, 69, 0.2);
    }
    .btn-action-delete:hover {
        background: #dc3545;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    }
    
    .id-tag {
        background: #e2e8f0;
        color: #475569;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 6px;
        letter-spacing: 0.5px;
    }
</style>

<div class="container-fluid py-3 py-md-4">

    <!-- Flash Message Notification -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- 1. Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center bg-white rounded-4 shadow-sm p-3.5 p-md-4 mb-4 border border-light gap-3">
        <div class="d-flex align-items-center">
            <div class="text-white rounded-3 p-2.5 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                <i class="fas fa-envelope fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark fs-5 fs-md-4">Surat Menyurat</h4>
                <small class="text-muted d-block mt-0.5">Kelola dokumen resmi perusahaan, surat masuk/keluar, kontrak kerja, SP, dan surat dinas.</small>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill font-weight-bold" style="font-size: 0.82rem;">
                <i class="fas fa-folder text-primary me-1"></i> Total: <strong><?= $countTotal ?></strong>
            </span>
            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill font-weight-bold" style="font-size: 0.82rem;">
                <i class="fas fa-check-circle text-success me-1"></i> Diterbitkan: <strong><?= $countDiterbitkan ?></strong>
            </span>
            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill font-weight-bold" style="font-size: 0.82rem;">
                <i class="fas fa-file-alt text-warning me-1"></i> Draft: <strong><?= $countDraft ?></strong>
            </span>

            <a href="<?= base_url('admin/surat/tambah') ?>" class="btn btn-primary rounded-pill px-4 py-2.5 shadow-sm d-inline-flex align-items-center text-sm fw-semibold" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border: none;">
                <i class="fas fa-plus me-1.5"></i> <span>Buat Surat Baru</span>
            </a>
        </div>
    </div>

    <!-- 2. Search & Filter Bar -->
    <div class="row g-2 mb-4">
        <div class="col-12 col-md-8 col-lg-9">
            <div class="input-group shadow-sm rounded-pill overflow-hidden border border-light bg-white">
                <span class="input-group-text bg-white border-end-0 text-muted ps-3.5">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" id="searchSurat" class="form-control border-start-0 py-2.5" placeholder="Cari nomor surat, jenis, perihal, nama karyawan, NIK, atau divisi..." onkeyup="filterSuratCards()">
            </div>
        </div>
        <div class="col-12 col-md-4 col-lg-3">
            <select id="filterJenis" class="form-select shadow-sm rounded-pill py-2.5 border-light text-secondary fw-semibold" onchange="filterSuratCards()">
                <option value="">Semua Jenis Surat</option>
                <?php foreach ($jenisList as $j): ?>
                    <option value="<?= esc($j) ?>" <?= ($filterAktif ?? '') == $j ? 'selected' : '' ?>><?= esc($j) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- 3. Daftar Kartu Surat -->
    <div class="row g-3" id="suratCardContainer">
        <?php if (!empty($suratList)): ?>
            <?php foreach ($suratList as $s): ?>
                <?php
                    $status = strtolower($s['status'] ?? 'draft');
                    $statusPillClass = 'status-pill-draft';
                    $statusIcon = 'fas fa-file-alt';
                    $statusText = 'Draft';

                    if ($status === 'diterbitkan') {
                        $statusPillClass = 'status-pill-active';
                        $statusIcon = 'fas fa-check-circle';
                        $statusText = 'Diterbitkan';
                    } elseif ($status === 'dibatalkan') {
                        $statusPillClass = 'status-pill-inactive';
                        $statusIcon = 'fas fa-ban';
                        $statusText = 'Dibatalkan';
                    }

                    $jenisStr = $s['jenis_surat'] ?? 'Lainnya';
                    $jenisClass = 'jenis-default';
                    $avatarGlowClass = 'avatar-glow';
                    $iconClass = 'fas fa-file-signature';

                    if (str_contains($jenisStr, 'Kontrak')) {
                        $jenisClass = 'jenis-kontrak';
                        $avatarGlowClass = 'avatar-glow';
                        $iconClass = 'fas fa-file-contract';
                    } elseif (str_contains($jenisStr, 'SP') || str_contains($jenisStr, 'Peringatan')) {
                        $jenisClass = 'jenis-sp';
                        $avatarGlowClass = 'avatar-glow-danger';
                        $iconClass = 'fas fa-exclamation-triangle';
                    } elseif (str_contains($jenisStr, 'Keterangan')) {
                        $jenisClass = 'jenis-keterangan';
                        $avatarGlowClass = 'avatar-glow-info';
                        $iconClass = 'fas fa-certificate';
                    } elseif (str_contains($jenisStr, 'Tugas') || str_contains($jenisStr, 'Masuk') || str_contains($jenisStr, 'Keluar')) {
                        $jenisClass = 'jenis-tugas';
                        $avatarGlowClass = 'avatar-glow-warning';
                        $iconClass = 'fas fa-scroll';
                    }
                    
                    $namaTarget = $s['nama_lengkap'] ?? 'Internal / Eksternal';
                    $searchData = strtolower(($s['nomor_surat'] ?? '') . ' ' . $jenisStr . ' ' . ($s['perihal'] ?? '') . ' ' . $namaTarget . ' ' . $statusText);
                ?>
                <div class="col-12 col-xl-6 surat-card-wrapper" data-search="<?= esc($searchData) ?>" data-jenis="<?= esc($jenisStr) ?>" data-status="<?= esc($status) ?>">
                    <div class="card employee-card-modern surat-card p-3.5 p-md-4">
                        
                        <!-- Header Kartu -->
                        <div class="d-flex align-items-center justify-content-between pb-3 border-bottom border-light flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="<?= $avatarGlowClass ?> text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                                    <i class="<?= $iconClass ?>"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <h5 class="mb-0 fw-bold text-dark fs-6">
                                            <?= esc($s['nomor_surat'] ?? 'SM-UNTITLED') ?>
                                        </h5>
                                        <span class="id-tag"><i class="far fa-calendar-alt text-primary me-1"></i><?= date('d M Y', strtotime($s['tanggal_surat'] ?? date('Y-m-d'))) ?></span>
                                    </div>
                                    <div class="mt-1 d-flex align-items-center gap-2 flex-wrap">
                                        <span class="jenis-pill <?= $jenisClass ?>">
                                            <?= esc($jenisStr) ?>
                                        </span>
                                        <span class="status-pill <?= $statusPillClass ?>">
                                            <i class="<?= $statusIcon ?> me-1"></i> <?= $statusText ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Pill Bars -->
                        <div class="row g-2 my-3">
                            <div class="col-12 col-md-7">
                                <div class="data-pill-bar">
                                    <div class="data-label">
                                        <i class="fas fa-file-alt text-primary"></i> Perihal Surat
                                    </div>
                                    <div class="data-value text-truncate" title="<?= esc($s['perihal'] ?? '') ?>">
                                        <?= esc($s['perihal'] ?? 'Tidak ada perihal') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-5">
                                <div class="data-pill-bar">
                                    <div class="data-label">
                                        <i class="fas fa-user text-info"></i> Ditujukan / Dibuat Oleh
                                    </div>
                                    <div class="data-value text-truncate">
                                        <?= esc($namaTarget) ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="d-flex flex-wrap align-items-center justify-content-end gap-2 pt-2 border-top border-light">
                            <a href="<?= base_url('admin/surat/detail/' . $s['id']) ?>" class="btn-action-pill btn-action-view">
                                <i class="fas fa-eye"></i> Detail / Cetak
                            </a>
                            <a href="<?= base_url('admin/surat/edit/' . $s['id']) ?>" class="btn-action-pill btn-action-edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <button type="button" class="btn-action-pill btn-action-delete" onclick="confirmHapusSurat(<?= $s['id'] ?>, '<?= esc($s['nomor_surat'] ?? '', 'js') ?>')">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Empty State -->
    <div id="noResults" class="text-center py-5 <?= !empty($suratList) ? 'd-none' : '' ?>">
        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
            <i class="fas fa-envelope-open-text fs-2 text-muted"></i>
        </div>
        <h5 class="fw-bold text-dark">Data Surat Tidak Ditemukan</h5>
        <p class="text-muted small">Belum ada surat terdaftar atau coba sesuaikan kata kunci pencarian Anda.</p>
        <a href="<?= base_url('admin/surat/tambah') ?>" class="btn btn-primary rounded-pill px-4 py-2 mt-2 fw-semibold" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border: none;">
            <i class="fas fa-plus me-1.5"></i> Buat Surat Baru
        </a>
    </div>

</div>

<script>
    function filterSuratCards() {
        const searchInput = document.getElementById('searchSurat').value.toLowerCase().trim();
        const jenisFilter = document.getElementById('filterJenis').value.toLowerCase();
        const items = document.querySelectorAll('.surat-card-wrapper');
        let visibleCount = 0;

        items.forEach(item => {
            const textData = item.getAttribute('data-search').toLowerCase();
            const jenisData = item.getAttribute('data-jenis').toLowerCase();

            const matchesSearch = searchInput === '' || textData.includes(searchInput);
            const matchesJenis = jenisFilter === '' || jenisFilter === jenisData;

            if (matchesSearch && matchesJenis) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        const noResults = document.getElementById('noResults');
        if (noResults) {
            if (visibleCount === 0) {
                noResults.classList.remove('d-none');
            } else {
                noResults.classList.add('d-none');
            }
        }
    }

    function confirmHapusSurat(id, noSurat) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Hapus Surat?',
                text: "Surat " + noSurat + " akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash me-1"></i> Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: { confirmButton: 'rounded-pill px-4', cancelButton: 'rounded-pill px-4' }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Menghapus...', allowOutsideClick: false, showConfirmButton: false, didOpen: () => Swal.showLoading() });
                    fetch("<?= base_url('admin/surat/hapus/') ?>" + id, {
                        method: 'GET',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.status === 'ok') {
                            Swal.fire({ icon: 'success', title: 'Berhasil Dihapus!', text: data.message || 'Surat berhasil dihapus.', timer: 1800, showConfirmButton: false })
                            .then(() => location.reload());
                        } else {
                            Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message || 'Terjadi kesalahan saat menghapus.' });
                        }
                    })
                    .catch(() => { location.href = "<?= base_url('admin/surat/hapus/') ?>" + id; });
                }
            });
        } else {
            if (confirm('Apakah Anda yakin ingin menghapus surat ' + noSurat + '?')) {
                window.location.href = "<?= base_url('admin/surat/hapus/') ?>" + id;
            }
        }
    }
</script>

<?= view('admin/templates/footer') ?>
