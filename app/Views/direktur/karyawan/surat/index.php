<?= $this->include('direktur/templates/header') ?>
<?= $this->include('direktur/templates/sidebar') ?>
<?= $this->include('direktur/templates/navbar') ?>

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
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
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

    /* Modern Action Pills */
    .btn-action-pill {
        border-radius: 20px;
        padding: 7px 18px;
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
        background: rgba(13, 110, 253, 0.08);
        color: #0d6efd;
        border-color: rgba(13, 110, 253, 0.2);
    }

    .btn-action-view:hover {
        background: #0d6efd;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }

    .btn-action-print {
        background: rgba(15, 23, 42, 0.08);
        color: #0f172a;
        border-color: rgba(15, 23, 42, 0.2);
    }

    .btn-action-print:hover {
        background: #0f172a;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.3);
    }

    .btn-action-edit {
        background: rgba(13, 202, 240, 0.1);
        color: #0891b2;
        border-color: rgba(13, 202, 240, 0.25);
    }

    .btn-action-edit:hover {
        background: #0891b2;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(8, 145, 178, 0.3);
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

    /* Document Live Preview Paper Box - Standard Vertical Dimensions */
    .letter-paper-preview {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        box-shadow: 0 14px 40px rgba(0, 0, 0, 0.12);
        padding: 55px 60px;
        position: relative;
        width: 100%;
        max-width: 794px;
        margin: 0 auto;
        font-family: 'Inter', Arial, sans-serif;
        color: #1e293b;
        overflow: hidden;
        box-sizing: border-box;
        transition: all 0.3s ease;
    }

    .paper-size-A4 { min-height: 1123px; max-width: 794px; }
    .paper-size-A3 { min-height: 1587px; max-width: 1123px; }
    .paper-size-Letter { min-height: 1056px; max-width: 816px; }
    .paper-size-Legal { min-height: 1344px; max-width: 816px; }
    .paper-size-Folio { min-height: 1247px; max-width: 813px; }

    .blue-header-banner {
        background: #1e40af;
        color: #ffffff;
        padding: 12px 18px;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        border-radius: 4px;
        margin-bottom: 20px;
    }

    /* Page Break Divider for Screen & Print */
    .doc-page-break {
        page-break-before: always;
        break-before: page;
        margin-top: 45px;
        padding-top: 35px;
        border-top: 2px dashed #94a3b8;
        position: relative;
        clear: both;
    }

    .doc-page-break::before {
        content: "📄 HALAMAN SELANJUTNYA (PAGE BREAK)";
        display: block;
        text-align: center;
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        letter-spacing: 1.5px;
        background: #f1f5f9;
        padding: 4px 16px;
        border-radius: 20px;
        width: fit-content;
        margin: -48px auto 25px auto;
        border: 1px solid #cbd5e1;
    }

    /* Rendered Table Styles inside Document */
    .custom-doc-table {
        width: 100%;
        border-collapse: collapse;
        margin: 18px 0;
        font-size: 0.9rem;
    }
    .custom-doc-table th, .custom-doc-table td {
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
    }
    .table-style-blue_header th {
        background: #1e40af;
        color: #ffffff;
        font-weight: 700;
        text-transform: uppercase;
        border-color: #1e3a8a;
    }
    .table-style-gold_header th {
        background: #d97706;
        color: #ffffff;
        font-weight: 700;
        text-transform: uppercase;
        border-color: #b45309;
    }
    .table-style-striped tbody tr:nth-child(even) {
        background-color: #f8fafc;
    }
    .table-style-standard th {
        background-color: #f1f5f9;
        font-weight: 700;
    }
</style>

<div id="mainIndexView">
<div class="container-fluid py-3 py-md-4">

    <!-- 1. Header Section -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light flex-wrap gap-3">
        <div class="d-flex align-items-center">
            <div class="bg-gradient-primary text-white rounded-3 p-2.5 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px;">
                <i class="fas fa-file-contract fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark">Surat Karyawan</h4>
                <small class="text-muted d-none d-sm-inline">Kelola dokumen kontrak kerja, surat peringatan (SP), surat keterangan, dan surat resmi karyawan.</small>
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

            <a href="<?= base_url('direktur/karyawan/surat/tambah') ?>" class="btn btn-primary rounded-pill px-3.5 py-2 shadow-sm d-inline-flex align-items-center text-sm fw-semibold ms-md-2">
                <i class="fas fa-plus me-1.5"></i> <span class="d-none d-md-inline">Buat Surat Baru</span><span class="d-inline d-md-none">Buat Surat</span>
            </a>
        </div>
    </div>

    <!-- 2. Search & Filter Bar -->
    <div class="input-group mb-3 shadow-sm rounded-pill overflow-hidden border border-light">
        <span class="input-group-text bg-white border-end-0 text-muted ps-3.5">
            <i class="fas fa-search"></i>
        </span>
        <input type="text" id="searchSurat" class="form-control border-start-0 border-end-0 ps-1 py-2.5" placeholder="Cari nomor surat, jenis, perihal, nama karyawan, NIK, atau divisi...">
        <button class="btn btn-dark px-4 d-flex align-items-center gap-2 fw-semibold" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">
            <i class="fas fa-sliders-h"></i> <span class="d-none d-sm-inline">Filter</span>
        </button>
    </div>

    <!-- Indicator Active Filter -->
    <div id="activeFilterTags" class="d-flex align-items-center gap-2 mb-3 d-none">
        <span class="text-xs text-muted fw-bold">Filter Aktif:</span>
        <div id="filterTagContainer" class="d-flex gap-1 flex-wrap"></div>
        <button type="button" class="btn btn-link btn-sm text-danger p-0 text-decoration-none ms-2" id="btnClearActiveFilter" style="font-size: 0.8rem;">
            <i class="fas fa-times-circle me-1"></i> Hapus Filter
        </button>
    </div>

    <!-- Modal Filter Lanjutan -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center" id="filterModalLabel">
                        <i class="fas fa-filter text-primary me-2"></i> Filter Data Surat
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-sm">Jenis Surat</label>
                            <select id="filterJenis" class="form-select rounded-3">
                                <option value="">Semua Jenis Surat</option>
                                <?php foreach ($jenisList as $j): ?>
                                    <option value="<?= esc($j) ?>" <?= $filterAktif == $j ? 'selected' : '' ?>><?= esc($j) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-sm">Status Surat</label>
                            <select id="filterStatus" class="form-select rounded-3">
                                <option value="">Semua Status</option>
                                <option value="diterbitkan">Diterbitkan</option>
                                <option value="draft">Draft</option>
                                <option value="dibatalkan">Dibatalkan</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-3 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold border" id="btnResetFilter">
                        <i class="fas fa-undo me-1"></i> Reset
                    </button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-semibold" data-bs-dismiss="modal" id="btnApplyFilter">
                        <i class="fas fa-check me-1"></i> Terapkan Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Live Preview & Customization -->
    <div class="modal fade" id="quickPreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-bottom py-3 px-4 bg-light">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                        <i class="fas fa-print text-primary me-2"></i> Pratinjau Layout Dokumen & Cetak
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="row g-3 mb-3 no-print">
                        <div class="col-md-3">
                            <label class="form-label text-xs fw-bold text-muted">Preset Template</label>
                            <select id="modalTemplateLayout" class="form-select form-select-sm rounded-3 fw-bold text-primary" onchange="modalApplyPreset()">
                                <option value="standard">📄 Standard Kop Atas</option>
                                <option value="accent_yellow">✨ Aksen Kuning Modern</option>
                                <option value="blue_header">🔷 Bilah Biru Formal</option>
                                <option value="compact_left">📌 Kop Ringkas Kiri</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-xs fw-bold text-muted">Ukuran Kertas</label>
                            <select id="modalPaperSize" class="form-select form-select-sm rounded-3 fw-bold text-dark" onchange="modalRenderPreview()">
                                <?php foreach ($paperSizes as $val => $lbl): ?>
                                    <option value="<?= $val ?>"><?= $lbl ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-xs fw-bold text-muted">Posisi Logo</label>
                            <select id="modalLogoPosition" class="form-select form-select-sm rounded-3" onchange="modalRenderPreview()">
                                <option value="top_right">Kanan Atas</option>
                                <option value="top_left">Kiri Atas</option>
                                <option value="center">Tengah Atas</option>
                                <option value="none">Sembunyikan</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-xs fw-bold text-muted">Posisi Alamat</label>
                            <select id="modalAddressPosition" class="form-select form-select-sm rounded-3" onchange="modalRenderPreview()">
                                <option value="top_left">Kop Atas Kiri</option>
                                <option value="top_center">Kop Atas Tengah</option>
                                <option value="footer">Footer Bawah</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-dark btn-sm rounded-pill w-100 fw-semibold py-2" onclick="openIndexPrintConfirmation()">
                                <i class="fas fa-print me-1"></i> Cetak PDF
                            </button>
                        </div>
                    </div>

                    <!-- Paper Render Container -->
                    <div class="letter-paper-preview paper-size-A4 mx-auto" id="modalPaperRender">
                        <!-- Rendered by JS -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Daftar Kartu Surat Karyawan -->
    <div class="row g-3" id="suratCardContainer">
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
                } elseif (str_contains($jenisStr, 'SP') || str_contains($jenisStr, 'Peringatan') || str_contains($jenisStr, 'Teguran')) {
                    $jenisClass = 'jenis-sp';
                    $avatarGlowClass = 'avatar-glow-danger';
                    $iconClass = 'fas fa-exclamation-triangle';
                } elseif (str_contains($jenisStr, 'Keterangan')) {
                    $jenisClass = 'jenis-keterangan';
                    $avatarGlowClass = 'avatar-glow-info';
                    $iconClass = 'fas fa-certificate';
                } elseif (str_contains($jenisStr, 'Tugas') || str_contains($jenisStr, 'Mutasi') || str_contains($jenisStr, 'Keputusan')) {
                    $jenisClass = 'jenis-tugas';
                    $avatarGlowClass = 'avatar-glow-warning';
                    $iconClass = 'fas fa-scroll';
                }
            ?>
            <div class="col-12 surat-card-wrapper" data-jenis="<?= esc($jenisStr) ?>" data-status="<?= esc($status) ?>">
                <div class="card employee-card-modern surat-card p-3 p-sm-4">
                    
                    <!-- Visual Header Kartu -->
                    <div class="d-flex align-items-center justify-content-between pb-3 border-bottom border-light flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <div class="<?= $avatarGlowClass ?> text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                                <i class="<?= $iconClass ?>"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <h3 class="mb-0 fw-bold text-dark" style="font-size: 1.15rem; letter-spacing: -0.2px;">
                                        <?= esc($s['nomor_surat']) ?>
                                    </h3>
                                    <span class="id-tag"><i class="far fa-calendar-alt text-primary me-1"></i><?= date('d M Y', strtotime($s['tanggal_surat'])) ?></span>
                                    <span class="badge bg-light text-dark border"><i class="far fa-file-alt text-secondary me-1"></i><?= esc($s['paper_size'] ?? 'A4') ?></span>
                                </div>
                                <div class="mt-1.5 d-flex align-items-center gap-2 flex-wrap">
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

                    <!-- Bilah Data Horizontal Grid -->
                    <div class="py-3">
                        <div class="row g-2.5">
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="data-pill-bar">
                                    <div class="data-label">
                                        <i class="fas fa-heading text-primary"></i> Perihal Surat
                                    </div>
                                    <div class="data-value text-break">
                                        <?= esc($s['perihal']) ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="data-pill-bar">
                                    <div class="data-label">
                                        <i class="fas fa-user-tie text-primary"></i> Karyawan Yang Dituju
                                    </div>
                                    <div class="data-value">
                                        <?= esc($s['nama_lengkap']) ?> <span class="text-muted font-weight-normal">(<?= esc($s['nik']) ?>)</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="data-pill-bar">
                                    <div class="data-label">
                                        <i class="fas fa-sitemap text-primary"></i> Divisi / Departemen
                                    </div>
                                    <div class="data-value">
                                        <?= !empty($s['divisi']) ? esc($s['divisi']) : '-' ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi Terintegrasi -->
                    <div class="pt-2 border-top border-light d-flex justify-content-end align-items-center gap-2 flex-wrap">
                        <button type="button" class="btn-action-pill btn-action-print btn-quick-preview" 
                                data-id="<?= $s['id'] ?>"
                                data-nomor="<?= esc($s['nomor_surat']) ?>"
                                data-jenis="<?= esc($s['jenis_surat']) ?>"
                                data-tanggal="<?= date('Y-m-d', strtotime($s['tanggal_surat'])) ?>"
                                data-perihal="<?= esc($s['perihal']) ?>"
                                data-isi="<?= esc($s['isi_surat']) ?>"
                                data-catatan="<?= esc($s['catatan']) ?>"
                                data-layout="<?= esc($s['template_layout'] ?? 'standard') ?>"
                                data-logopos="<?= esc($s['logo_position'] ?? 'top_right') ?>"
                                data-addrpos="<?= esc($s['address_position'] ?? 'top_left') ?>"
                                data-accent="<?= esc($s['accent_style'] ?? 'line') ?>"
                                data-paper="<?= esc($s['paper_size'] ?? 'A4') ?>"
                                title="Pratinjau Layout & Cetak PDF">
                            <i class="fas fa-print"></i> Pratinjau / Cetak
                        </button>
                        <a href="<?= base_url('direktur/karyawan/surat/detail/' . $s['id']) ?>" class="btn-action-pill btn-action-view" title="Lihat Detail Surat">
                            <i class="far fa-eye"></i> Detail Surat
                        </a>
                        <a href="<?= base_url('direktur/karyawan/surat/edit/' . $s['id']) ?>" class="btn-action-pill btn-action-edit" title="Edit Surat">
                            <i class="far fa-edit"></i> Edit
                        </a>
                        <form action="<?= base_url('direktur/karyawan/surat/delete/' . $s['id']) ?>" method="post" class="d-inline form-delete-surat">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn-action-pill btn-action-delete btn-delete-surat" data-nomor="<?= esc($s['nomor_surat']) ?>" title="Hapus Surat">
                                <i class="far fa-trash-alt"></i> Hapus
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Pesan Kosong -->
    <div id="noResultsMessage" class="alert alert-info text-center rounded-4 shadow-sm p-4 d-none my-3">
        <i class="fas fa-search fa-2x mb-2 text-primary"></i>
        <h5 class="fw-bold mb-1">Data Surat Tidak Ditemukan</h5>
        <p class="mb-0 text-muted small">Coba ubah kata kunci pencarian atau reset filter Anda.</p>
    </div>

    <!-- Control Bar Bawah -->
    <div class="card shadow-sm rounded-4 border-0 p-3 mt-4 mb-5 bg-white">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            
            <div class="d-flex align-items-center gap-2">
                <span class="text-xs text-muted fw-bold text-uppercase">Tampilkan:</span>
                <select id="perPageSelect" class="form-select form-select-sm rounded-pill shadow-xs fw-semibold text-dark border-light px-3 py-1.5" style="width: auto; cursor: pointer;">
                    <option value="5" selected>5 Surat / hal</option>
                    <option value="10">10 Surat / hal</option>
                    <option value="25">25 Surat / hal</option>
                    <option value="50">50 Surat / hal</option>
                    <option value="75">75 Surat / hal</option>
                    <option value="100">100 Surat / hal</option>
                    <option value="all">Tampilkan Semua</option>
                </select>
            </div>

            <div class="text-muted text-sm fw-semibold text-center" id="paginationInfo">
                Menampilkan 1 - 5 dari <?= count($suratList) ?> surat
            </div>

            <div id="paginationContainer" class="d-flex justify-content-center">
                <!-- Diisi otomatis secara responsif oleh JavaScript -->
            </div>

        </div>
    </div>

</div>
</div>

<!-- ===================================================================== -->
<!-- DEDICATED PRINT VIEW & CONFIRMATION MODAL -->
<!-- ===================================================================== -->
<div id="dedicatedPrintView" class="d-none" style="display: none !important; background:#0f172a; min-height:100vh; padding-bottom:50px;">
    <div class="no-print sticky-top bg-dark text-white p-3 shadow-lg d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom border-secondary" style="z-index: 1000000;">
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3.5 py-2 fw-semibold" onclick="closeDedicatedPrintView()">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali ke Daftar Surat
            </button>
            <span class="badge bg-primary rounded-pill px-3 py-2 text-xs d-none d-md-inline">
                Mode Pratinjau Cetak Khusus
            </span>
        </div>
        <div class="text-white text-center d-none d-lg-block">
            <h6 class="mb-0 text-white fw-bold"><i class="fas fa-file-alt text-warning me-2"></i> Pratinjau Cetak Dokumen Surat</h6>
            <small class="text-slate-400" style="font-size:0.75rem; color: #94a3b8;">Format ini siap dicetak langsung atau disimpan sebagai PDF</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-warning btn-sm rounded-pill px-4 py-2 fw-bold shadow text-dark" onclick="triggerActualPrint()">
                <i class="fas fa-print me-1.5"></i> Cetak Sekarang / Simpan PDF
            </button>
        </div>
    </div>

    <div class="container py-4">
        <div id="dedicatedPrintPaper" class="letter-paper-preview mx-auto shadow-2xl"></div>
    </div>
</div>

<!-- Modal Konfirmasi Cetak -->
<div class="modal fade" id="confirmPrintModal" tabindex="-1" aria-hidden="true" style="z-index: 10999;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg" style="overflow: hidden;">
            <div class="modal-header bg-gradient-primary text-white border-0 py-3">
                <h5 class="modal-title fw-bold text-white fs-6 mb-0 d-flex align-items-center gap-2">
                    <i class="fas fa-print text-warning"></i> Konfirmasi Pratinjau & Cetak PDF
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3 text-primary">
                    <i class="fas fa-file-invoice fa-4x animate__animated animate__bounceIn"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Siap untuk Pratinjau & Cetak?</h5>
                <p class="text-muted text-sm mb-3">
                    Sistem akan mengarahkan Anda ke Halaman Khusus Pratinjau Cetak untuk memastikan dokumen bebas kesalahan sebelum disimpan sebagai PDF.
                </p>
                <div class="p-3 bg-light rounded-3 text-start border mb-3 text-xs" style="line-height: 1.6;">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Jenis Surat:</span>
                        <strong id="printConfirmJenis" class="text-dark">-</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Nomor Surat:</span>
                        <strong id="printConfirmNomor" class="text-dark">-</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Tanggal:</span>
                        <strong id="printConfirmTanggal" class="text-dark">-</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Format Kertas:</span>
                        <strong id="printConfirmPaper" class="text-dark">A4</strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 px-4 py-3 justify-content-between">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal / Periksa Lagi
                </button>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" onclick="proceedToDedicatedPrintView()">
                    <i class="fas fa-arrow-right me-1"></i> Ya, Lanjut Cetak ➔
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Base64 Logo JS Variable -->
<script>
const companyLogoBase64 = '<?= $logoBase64 ?>';

document.addEventListener('DOMContentLoaded', function () {
    const searchInput   = document.getElementById('searchSurat');
    const filterJenis   = document.getElementById('filterJenis');
    const filterStatus  = document.getElementById('filterStatus');
    const perPageSelect = document.getElementById('perPageSelect');
    const btnReset      = document.getElementById('btnResetFilter');
    const btnApply      = document.getElementById('btnApplyFilter');
    const btnClearActive = document.getElementById('btnClearActiveFilter');
    const cards         = Array.from(document.querySelectorAll('.surat-card-wrapper'));
    const paginationEl  = document.getElementById('paginationContainer');
    const infoEl        = document.getElementById('paginationInfo');
    const noResultsEl   = document.getElementById('noResultsMessage');
    const activeTagsBox = document.getElementById('activeFilterTags');
    const tagContainer  = document.getElementById('filterTagContainer');

    let currentPage = 1;
    let itemsPerPage = 5;

    function filterAndPaginate() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const jenisVal   = filterJenis ? filterJenis.value.toLowerCase().trim() : '';
        const statusVal  = filterStatus ? filterStatus.value.toLowerCase().trim() : '';

        const perPageVal = perPageSelect ? perPageSelect.value : '5';
        itemsPerPage = perPageVal === 'all' ? 999999 : parseInt(perPageVal) || 5;

        updateActiveFilterTags(jenisVal, statusVal);

        let visibleCards = cards.filter(card => {
            const text   = card.innerText.toLowerCase();
            const jenis  = (card.getAttribute('data-jenis') || '').toLowerCase();
            const status = (card.getAttribute('data-status') || '').toLowerCase();

            const matchSearch = !searchTerm || text.includes(searchTerm);
            const matchJenis  = !jenisVal  || jenis.includes(jenisVal);
            const matchStatus = !statusVal || status.includes(statusVal);

            return matchSearch && matchJenis && matchStatus;
        });

        const totalVisible = visibleCards.length;
        const totalPages   = Math.ceil(totalVisible / itemsPerPage) || 1;

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        cards.forEach(card => card.style.display = 'none');

        if (totalVisible === 0) {
            if (noResultsEl) noResultsEl.classList.remove('d-none');
            if (infoEl) infoEl.textContent = 'Tidak ada data surat yang cocok.';
            if (paginationEl) paginationEl.innerHTML = '';
            return;
        } else {
            if (noResultsEl) noResultsEl.classList.add('d-none');
        }

        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex   = Math.min(startIndex + itemsPerPage, totalVisible);

        for (let i = startIndex; i < endIndex; i++) {
            visibleCards[i].style.display = 'block';
        }

        if (infoEl) {
            if (itemsPerPage >= 999999) {
                infoEl.textContent = `Menampilkan seluruh ${totalVisible} surat`;
            } else {
                infoEl.textContent = `Menampilkan ${startIndex + 1} - ${endIndex} dari ${totalVisible} surat`;
            }
        }

        renderPagination(totalPages);
    }

    function updateActiveFilterTags(jenisVal, statusVal) {
        if (!activeTagsBox || !tagContainer) return;
        tagContainer.innerHTML = '';

        if (jenisVal || statusVal) {
            activeTagsBox.classList.remove('d-none');
            if (jenisVal) {
                tagContainer.innerHTML += `<span class="active-filter-badge">Jenis: ${filterJenis.options[filterJenis.selectedIndex].text}</span>`;
            }
            if (statusVal) {
                tagContainer.innerHTML += `<span class="active-filter-badge">Status: ${filterStatus.options[filterStatus.selectedIndex].text}</span>`;
            }
        } else {
            activeTagsBox.classList.add('d-none');
        }
    }

    function renderPagination(totalPages) {
        if (!paginationEl) return;
        if (totalPages <= 1) {
            paginationEl.innerHTML = '';
            return;
        }

        let html = '<ul class="pagination pagination-modern mb-0 shadow-sm rounded-pill overflow-hidden bg-white p-1 border border-light">';
        
        html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <button class="page-link rounded-pill" data-page="${currentPage - 1}">
                        <i class="fas fa-chevron-left me-1"></i> <span class="d-none d-sm-inline">Sebelumnya</span>
                    </button>
                 </li>`;

        const maxVisibleButtons = window.innerWidth < 576 ? 3 : 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisibleButtons / 2));
        let endPage = Math.min(totalPages, startPage + maxVisibleButtons - 1);

        if (endPage - startPage + 1 < maxVisibleButtons) {
            startPage = Math.max(1, endPage - maxVisibleButtons + 1);
        }

        if (startPage > 1) {
            html += `<li class="page-item"><button class="page-link rounded-circle" data-page="1">1</button></li>`;
            if (startPage > 2) {
                html += `<li class="page-item disabled"><span class="page-link border-0">...</span></li>`;
            }
        }

        for (let p = startPage; p <= endPage; p++) {
            html += `<li class="page-item ${p === currentPage ? 'active' : ''}">
                        <button class="page-link rounded-circle" data-page="${p}">${p}</button>
                     </li>`;
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                html += `<li class="page-item disabled"><span class="page-link border-0">...</span></li>`;
            }
            html += `<li class="page-item"><button class="page-link rounded-circle" data-page="${totalPages}">${totalPages}</button></li>`;
        }

        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <button class="page-link rounded-pill" data-page="${currentPage + 1}">
                        <span class="d-none d-sm-inline">Selanjutnya</span> <i class="fas fa-chevron-right ms-1"></i>
                    </button>
                 </li>`;

        html += '</ul>';
        paginationEl.innerHTML = html;

        paginationEl.querySelectorAll('.page-link').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const targetPage = parseInt(this.getAttribute('data-page'));
                if (targetPage && targetPage >= 1 && targetPage <= totalPages && targetPage !== currentPage) {
                    currentPage = targetPage;
                    filterAndPaginate();
                    window.scrollTo({ top: 120, behavior: 'smooth' });
                }
            });
        });
    }

    if (searchInput) searchInput.addEventListener('input', function() { currentPage = 1; filterAndPaginate(); });
    if (perPageSelect) perPageSelect.addEventListener('change', function() { currentPage = 1; filterAndPaginate(); });
    if (btnApply) btnApply.addEventListener('click', function() { currentPage = 1; filterAndPaginate(); });

    function resetAllFilters() {
        if (filterJenis)  filterJenis.value  = '';
        if (filterStatus) filterStatus.value = '';
        if (searchInput)  searchInput.value  = '';
        if (perPageSelect) perPageSelect.value = '5';
        currentPage = 1;
        filterAndPaginate();
    }

    if (btnReset) btnReset.addEventListener('click', resetAllFilters);
    if (btnClearActive) btnClearActive.addEventListener('click', resetAllFilters);

    filterAndPaginate();

    // Quick Preview Modal Handler
    document.querySelectorAll('.btn-quick-preview').forEach(btn => {
        btn.addEventListener('click', function() {
            activeModalSuratData = {
                nomor: this.dataset.nomor,
                jenis: this.dataset.jenis,
                tanggal: this.dataset.tanggal,
                perihal: this.dataset.perihal,
                isi: this.dataset.isi,
                catatan: this.dataset.catatan,
                layout: this.dataset.layout || 'standard',
                logopos: this.dataset.logopos || 'top_right',
                addrpos: this.dataset.addrpos || 'top_left',
                accent: this.dataset.accent || 'line',
                paper: this.dataset.paper || 'A4'
            };

            document.getElementById('modalTemplateLayout').value = activeModalSuratData.layout;
            document.getElementById('modalPaperSize').value      = activeModalSuratData.paper;
            document.getElementById('modalLogoPosition').value   = activeModalSuratData.logopos;
            document.getElementById('modalAddressPosition').value= activeModalSuratData.addrpos;

            modalRenderPreview();
            const modal = new bootstrap.Modal(document.getElementById('quickPreviewModal'));
            modal.show();
        });
    });
});

function modalApplyPreset() {
    const layout = document.getElementById('modalTemplateLayout').value;
    const logoPos = document.getElementById('modalLogoPosition');
    const addrPos = document.getElementById('modalAddressPosition');

    if (layout === 'standard') {
        logoPos.value = 'top_right';
        addrPos.value = 'top_left';
        activeModalSuratData.accent = 'line';
    } else if (layout === 'accent_yellow') {
        logoPos.value = 'top_right';
        addrPos.value = 'footer';
        activeModalSuratData.accent = 'yellow_corner';
    } else if (layout === 'blue_header') {
        logoPos.value = 'top_right';
        addrPos.value = 'top_left';
        activeModalSuratData.accent = 'blue_bar';
    } else if (layout === 'compact_left') {
        logoPos.value = 'top_left';
        addrPos.value = 'top_left';
        activeModalSuratData.accent = 'line';
    }
    modalRenderPreview();
}

function modalRenderPreview() {
    const paper = document.getElementById('modalPaperRender');
    if (!paper) return;

    const paperSizeVal = document.getElementById('modalPaperSize').value || 'A4';
    paper.className = `letter-paper-preview paper-size-${paperSizeVal} mx-auto`;

    const logoPos = document.getElementById('modalLogoPosition').value;
    const addrPos = document.getElementById('modalAddressPosition').value;
    const accent  = activeModalSuratData.accent || 'line';
    const jenis   = activeModalSuratData.jenis || 'SURAT KARYAWAN';
    const nomor   = activeModalSuratData.nomor || '-';
    const perihal = activeModalSuratData.perihal || '-';
    const tanggal = activeModalSuratData.tanggal || '2026-07-29';
    const isiText = activeModalSuratData.isi || '';
    const catatan = activeModalSuratData.catatan || '';

    const dateObj = new Date(tanggal);
    const options = { day: 'numeric', month: 'long', year: 'numeric' };
    const dateFormatted = isNaN(dateObj) ? tanggal : dateObj.toLocaleDateString('id-ID', options);

    // Large prominent Logo (height: 160px) with uncompressed flex container (min-width: 220px)
    let logoHtml = '';
    if (logoPos !== 'none' && companyLogoBase64) {
        logoHtml = `<img src="${companyLogoBase64}" alt="CDW Logo" style="height: 160px; max-height: 170px; width: auto; max-width: 320px; object-fit: contain; display: block;">`;
    }

    // Address text with automatic offset when yellow corner accent is active to prevent overlapping
    let addressPaddingStyle = '';
    if (accent === 'yellow_corner' && addrPos === 'top_left') {
        addressPaddingStyle = 'padding-top: 40px; padding-left: 55px; position: relative; z-index: 3;';
    }

    let addressHtml = '';
    if (addrPos === 'top_left' || addrPos === 'top_center') {
        const alignText = addrPos === 'top_center' ? 'text-align: center;' : '';
        addressHtml = `
            <div style="${alignText} ${addressPaddingStyle} line-height: 1.45;">
                <strong style="font-size: 1.05rem; color: #000;">PT. CIPTA DUTA WACANA</strong><br>
                <span style="font-size: 0.88rem; font-weight: 600; color: #1e293b;">Beltway Office Park Tower B Lantai 5</span><br>
                <span style="font-size: 0.82rem; color: #334155;">Jl. TB Simatupang No. 41 Ragunan-Pasar Minggu, Jakarta Selatan</span><br>
                <span style="font-size: 0.78rem; color: #475569;">Phone: (+62-21) 29857462; 29215392; 29084991 | Fax: (+62-21) 29857201</span>
            </div>
        `;
    }

    let headerTopSection = '';
    if (logoPos === 'top_right') {
        headerTopSection = `
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="flex-grow-1 me-3">${addressHtml}</div>
                <div class="flex-shrink-0" style="min-width: 220px; min-height: 160px; text-align: right;">${logoHtml}</div>
            </div>
        `;
    } else if (logoPos === 'top_left') {
        headerTopSection = `
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="flex-shrink-0 me-3" style="min-width: 220px; min-height: 160px;">${logoHtml}</div>
                <div class="flex-grow-1">${addressHtml}</div>
            </div>
        `;
    } else if (logoPos === 'center') {
        headerTopSection = `
            <div class="text-center mb-3">
                <div class="mb-2 d-flex justify-content-center" style="min-height: 160px;">${logoHtml}</div>
                <div>${addressHtml}</div>
            </div>
        `;
    } else {
        headerTopSection = `<div class="mb-3">${addressHtml}</div>`;
    }

    let accentHtml = '';
    if (accent === 'line') {
        accentHtml = `<div style="border-bottom: 2px solid #0f172a; margin-bottom: 25px;"></div>`;
    } else if (accent === 'blue_bar') {
        accentHtml = `
            <div class="blue-header-banner text-center my-3 shadow-xs">
                ${jenis}
            </div>
        `;
    }

    let cornerAccents = '';
    if (accent === 'yellow_corner') {
        cornerAccents = `
            <svg style="position: absolute; top: 0; left: 0; width: 200px; height: 200px; pointer-events: none; z-index: 1; opacity: 0.85;" viewBox="0 0 280 280">
                <path d="M 0 0 L 280 0 Q 70 70 0 280 Z" fill="#f5a600"/>
            </svg>
            <svg style="position: absolute; bottom: 0; right: 0; width: 160px; height: 160px; pointer-events: none; z-index: 1; opacity: 0.85;" viewBox="0 0 260 260">
                <path d="M 260 260 L 0 260 Q 190 190 260 0 Z" fill="#f5a600"/>
            </svg>
        `;
    }

    let footerAddressHtml = '';
    if (addrPos === 'footer' || accent === 'yellow_corner') {
        footerAddressHtml = `
            <div style="position: absolute; bottom: 18px; left: 40px; right: 180px; font-size: 0.7rem; color: #475569; line-height: 1.35; z-index: 5;">
                <strong>PT. Cipta Duta Wacana</strong><br>
                Beltway Office Park Tower B Lt.5, Jl. Letjen TB Simatupang No.41, Ragunan, Pasar Minggu, Jakarta Selatan 12550<br>
                Tel: +62-21 29857462 | Fax: +62-21 29857201 | <span style="color: #0284c7;">www.cdw-engineering.com</span>
            </div>
            <div style="position: absolute; bottom: 18px; right: 30px; font-size: 0.75rem; font-weight: 700; color: #64748b; z-index: 5;">
                Halaman 1 dari 1
            </div>
        `;
    }

    function formatBodyTextWithTables(text) {
        if (!text) return '';
        const regex = /(<table[\s\S]*?<\/table>)/gi;
        const parts = text.split(regex);
        return parts.map(part => {
            if (/^<table/i.test(part.trim())) {
                return part;
            }
            const lines = part.split('\n');
            let html = '<div style="line-height:1.8; margin-bottom:14px; font-size:0.92rem; color:#1e293b;">';
            let inKvGroup = false;
            lines.forEach(line => {
                const trimmed = line.trim();
                const kvMatch = trimmed.match(/^([A-Za-z0-9\s\/]+)\s*:\s*(.*)$/);
                if (kvMatch && kvMatch[1].trim().length > 0 && kvMatch[1].trim().length <= 25) {
                    if (!inKvGroup) {
                        html += '<div style="margin: 10px 0 10px 20px; border-left: 2px solid #cbd5e1; padding-left: 14px;">';
                        inKvGroup = true;
                    }
                    const key = kvMatch[1].trim();
                    const val = kvMatch[2].trim();
                    html += `<div style="display:flex; margin-bottom:4px; font-size:0.9rem;">
                        <span style="min-width:110px; width:110px; font-weight:600; color:#334155;">${key}</span>
                        <span style="width:15px; font-weight:600; color:#64748b;">:</span>
                        <span style="font-weight:600; color:#0f172a;">${val || '-'}</span>
                    </div>`;
                } else {
                    if (inKvGroup) {
                        html += '</div>';
                        inKvGroup = false;
                    }
                    if (trimmed === '') {
                        html += '<div style="height:8px;"></div>';
                    } else {
                        html += `<p style="margin-bottom:8px; margin-top:0;">${trimmed}</p>`;
                    }
                }
            });
            if (inKvGroup) html += '</div>';
            html += '</div>';
            return html;
        }).join('');
    }

    let formattedBodyContent = formatBodyTextWithTables(isiText);

    let paperHtml = `
        <div class="doc-page-sheet">
            ${cornerAccents}
            <div style="position: relative; z-index: 2; display: flex; flex-direction: column; flex-grow: 1; height: 100%;">
                <div style="min-height: 120px; margin-bottom: 12px;">
                    ${headerTopSection}
                </div>
                ${accentHtml}

                ${accent !== 'blue_bar' ? `<h5 class="text-center fw-bold text-dark text-uppercase mb-3" style="letter-spacing: 0.5px;">${jenis}</h5>` : ''}

                <div class="d-flex justify-content-between text-sm fw-semibold text-secondary mb-3 pb-2 border-bottom border-light">
                    <span><strong>Nomor:</strong> ${nomor}</span>
                    <span><strong>Tanggal:</strong> ${dateFormatted}</span>
                </div>

                <div class="mb-3">
                    <strong class="text-dark">Perihal: ${perihal}</strong>
                </div>

                <div class="text-dark text-sm leading-relaxed mb-4 page-content-area" style="line-height: 1.8; flex-grow: 1; font-size: 0.92rem;">
                    ${formattedBodyContent}
                </div>

                <div style="margin-top: auto; padding-top: 15px;">
                    ${catatan ? `
                        <div class="alert alert-warning border-0 rounded-3 text-dark text-xs p-2.5 mb-3" style="font-size:0.78rem;">
                            <strong>Catatan Internal:</strong> ${catatan}
                        </div>
                    ` : ''}

                    <div class="row pt-4 text-center" style="position: relative; z-index: 5; margin-bottom: ${addrPos === 'footer' || accent === 'yellow_corner' ? '70px' : '15px'};">
                        <div class="col-6 ms-auto">
                            <p class="text-sm mb-5">Hormat kami,<br><strong>PT. CIPTA DUTA WACANA</strong></p>
                            <p class="fw-bold text-dark mb-0 border-bottom d-inline-block pb-1" style="min-width: 160px;">
                                Direktur Utama
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            ${footerAddressHtml}
        </div>
    `;

    paper.innerHTML = paperHtml;
}

// Print Confirmation & Dedicated Print View Handlers
function openIndexPrintConfirmation() {
    // Hide quick preview modal if open
    const quickModalEl = document.getElementById('quickPreviewModal');
    if (quickModalEl) {
        const qm = bootstrap.Modal.getInstance(quickModalEl);
        if (qm) qm.hide();
    }

    const printConfirmJenis = document.getElementById('printConfirmJenis');
    const printConfirmNomor = document.getElementById('printConfirmNomor');
    const printConfirmTanggal = document.getElementById('printConfirmTanggal');
    const printConfirmPaper = document.getElementById('printConfirmPaper');

    if (printConfirmJenis) printConfirmJenis.textContent = activeModalSuratData.jenis || '-';
    if (printConfirmNomor) printConfirmNomor.textContent = activeModalSuratData.nomor || '-';
    if (printConfirmTanggal) printConfirmTanggal.textContent = activeModalSuratData.tanggal || '-';
    if (printConfirmPaper) printConfirmPaper.textContent = (document.getElementById('modalPaperSize') || {}).value || 'A4';

    const modalEl = document.getElementById('confirmPrintModal');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
}

function proceedToDedicatedPrintView() {
    const modalEl = document.getElementById('confirmPrintModal');
    if (modalEl) {
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }

    const previewPaper = document.getElementById('modalPaperRender');
    const printPaper = document.getElementById('dedicatedPrintPaper');
    
    if (previewPaper && printPaper) {
        printPaper.className = previewPaper.className;
        printPaper.innerHTML = previewPaper.innerHTML;
    }

    const mainView = document.getElementById('mainIndexView');
    const printView = document.getElementById('dedicatedPrintView');
    if (mainView && printView) {
        mainView.style.setProperty('display', 'none', 'important');
        printView.style.setProperty('display', 'block', 'important');
        printView.classList.remove('d-none');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function closeDedicatedPrintView() {
    const mainView = document.getElementById('mainIndexView');
    const printView = document.getElementById('dedicatedPrintView');
    if (mainView && printView) {
        printView.style.setProperty('display', 'none', 'important');
        printView.classList.add('d-none');
        mainView.style.removeProperty('display');
        mainView.classList.remove('d-none');
    }
}

function triggerActualPrint() {
    window.print();
}

window.addEventListener('beforeprint', function () {
    const printView = document.getElementById('dedicatedPrintView');
    if (printView && printView.classList.contains('d-none')) {
        proceedToDedicatedPrintView();
    }
});

// Real-time Notifikasi & SweetAlert Delete Handler
window.addEventListener('load', function () {
    if (typeof $ === 'undefined' || typeof Swal === 'undefined') return;

    const flashSuccess = '<?= session()->getFlashdata('success') ? esc(session()->getFlashdata('success'), 'js') : '' ?>';
    const flashError   = '<?= session()->getFlashdata('error') ? esc(session()->getFlashdata('error'), 'js') : '' ?>';

    if (flashSuccess) {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            html: flashSuccess,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true
        });
    }

    if (flashError) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            html: flashError,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true
        });
    }

    $(document).on('click', '.btn-delete-surat', function (e) {
        e.preventDefault();
        const form = $(this).closest('form');
        const nomor = $(this).data('nomor') || 'surat ini';

        Swal.fire({
            title: 'Konfirmasi Hapus Surat',
            text: `Apakah Anda yakin ingin menghapus surat dengan nomor "${nomor}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="far fa-trash-alt me-1"></i> Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>

<style>
.letter-paper-preview {
    background: transparent !important;
    box-shadow: none !important;
    padding: 0 !important;
    border: none !important;
}

.doc-page-sheet {
    position: relative !important;
    background: #ffffff !important;
    min-height: 1120px !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08) !important;
    margin: 0 auto 30px auto !important;
    border-radius: 4px !important;
    display: flex !important;
    flex-direction: column !important;
    box-sizing: border-box !important;
    padding: 45px 50px 75px 50px !important;
    width: 100% !important;
}

@media print {
    @page { size: auto; margin: 0; }
    html, body {
        background: #ffffff !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        height: auto !important;
        overflow: visible !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .sidebar,
    .main-content > *:not(#dedicatedPrintView),
    .top-navbar,
    .navbar,
    .sidenav,
    #mainIndexView,
    header,
    footer,
    .no-print,
    .modal,
    .modal-backdrop,
    .sidebar-overlay,
    nav {
        display: none !important;
        visibility: hidden !important;
        width: 0 !important;
        height: 0 !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }
    #dedicatedPrintView {
        display: block !important;
        position: relative !important;
        left: auto !important;
        top: auto !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #ffffff !important;
        min-height: auto !important;
        height: auto !important;
        overflow: visible !important;
        z-index: 1 !important;
    }
    #dedicatedPrintPaper {
        position: relative !important;
        left: auto !important;
        top: auto !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        box-shadow: none !important;
        border: none !important;
        border-radius: 0 !important;
        background: #ffffff !important;
        height: auto !important;
        overflow: visible !important;
    }
    .doc-page-sheet {
        position: relative !important;
        width: 100% !important;
        box-shadow: none !important;
        border: none !important;
        margin: 0 !important;
        padding: 35px 45px 75px 45px !important;
        page-break-after: always !important;
        break-after: page !important;
        page-break-inside: auto !important;
        break-inside: auto !important;
        box-sizing: border-box !important;
        min-height: 297mm !important;
        height: auto !important;
        max-height: none !important;
        overflow: visible !important;
    }
    .page-break-indicator {
        display: none !important;
    }
    .custom-doc-table, tr, td, th, table {
        break-inside: avoid !important;
        page-break-inside: avoid !important;
    }
}
</style>

<?= $this->include('direktur/templates/footer') ?>
