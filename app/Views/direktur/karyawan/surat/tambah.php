<?php
$templateData = [
    'title'  => $title ?? 'Buat Surat Baru',
    'user'   => $user ?? (session()->get('user') ?? ['name' => session()->get('name') ?? 'Direktur', 'role' => 'direktur']),
    'active' => 'karyawan'
];
?>

<?= view('direktur/templates/header', $templateData) ?>
<?= view('direktur/templates/sidebar', $templateData) ?>
<?= view('direktur/templates/navbar', $templateData) ?>


<style>
    /* Styling Premium Modern Material & Glassmorphism */
    .employee-card-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(30, 60, 114, 0.06), 0 2px 6px rgba(0, 0, 0, 0.02) !important;
    }

    .form-section-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e3c72;
        display: flex;
        align-items: center;
        gap: 8px;
        padding-bottom: 8px;
        border-bottom: 2px solid #f1f5f9;
        margin-bottom: 20px;
    }

    .form-control-custom, .form-select-custom {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 10px 14px;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .form-control-custom:focus, .form-select-custom:focus {
        border-color: #1e3c72;
        box-shadow: 0 0 0 3px rgba(30, 60, 114, 0.12);
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

    /* Standard Paper Height Dimensions at 96 DPI */
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
        background: #1e40af; color: #ffffff;
        font-weight: 700; text-transform: uppercase; border-color: #1e3a8a;
    }
    .table-style-gold_header th {
        background: #d97706; color: #ffffff;
        font-weight: 700; text-transform: uppercase; border-color: #b45309;
    }
    .table-style-striped tbody tr:nth-child(even) { background-color: #f8fafc; }
    .table-style-standard th { background-color: #f1f5f9; font-weight: 700; }

    /* ===== Visual Block Editor Styles ===== */
    .content-block-card {
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        margin-bottom: 10px;
        overflow: hidden;
        transition: box-shadow 0.2s ease;
    }
    .content-block-card:hover { box-shadow: 0 4px 14px rgba(30,60,114,0.09); }
    .block-header-bar {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 8px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 6px;
    }
    .table-blk-header { background: #eff6ff; border-bottom-color: #bfdbfe; }
    .block-type-badge {
        font-size: 0.72rem; font-weight: 700;
        padding: 3px 10px; border-radius: 20px;
        letter-spacing: 0.4px; text-transform: uppercase;
        display: inline-flex; align-items: center; gap: 4px;
    }
    .text-badge  { background: rgba(100,116,139,0.12); color: #475569; border: 1px solid rgba(100,116,139,0.25); }
    .table-badge { background: rgba(30,64,175,0.1);  color: #1d4ed8; border: 1px solid rgba(30,64,175,0.2); }
    .block-body-area { padding: 12px; }
    .table-block-body { padding: 8px; background: #fafafa; }
    .block-textarea {
        border-radius: 6px; font-size: 0.88rem; line-height: 1.75;
        resize: vertical; min-height: 90px;
    }
    .btn-blk-action {
        border: 1px solid #e2e8f0; background: #fff;
        color: #475569; border-radius: 6px;
        padding: 4px 8px; font-size: 0.75rem;
        cursor: pointer; transition: all 0.15s ease;
        display: inline-flex; align-items: center; gap: 4px;
        font-weight: 600;
    }
    .btn-blk-action:hover:not(:disabled) { background: #1e3c72; color: #fff; border-color: #1e3c72; }
    .btn-blk-action:disabled { opacity: 0.35; cursor: not-allowed; }
    .btn-blk-danger:hover:not(:disabled)  { background: #dc3545 !important; border-color: #dc3545 !important; }
    .btn-blk-success:hover:not(:disabled) { background: #198754 !important; border-color: #198754 !important; }
    .btn-blk-warning:hover:not(:disabled) { background: #d97706 !important; border-color: #d97706 !important; }
    .btn-blk-info:hover:not(:disabled)    { background: #0d6efd !important; border-color: #0d6efd !important; color: #fff !important; }
    .block-empty-state {
        text-align: center; padding: 28px 16px; color: #94a3b8;
        border: 2px dashed #cbd5e1; border-radius: 10px;
        background: #f8fafc;
    }
    .page-break-indicator {
        margin: 20px 0 0 0;
        border-top: 2.5px dashed #6366f1;
        position: relative;
        display: block;
        clear: both;
    }
    .page-break-indicator::before {
        content: attr(data-label);
        display: block; text-align: center;
        font-size: 0.7rem; font-weight: 700;
        color: #6366f1; letter-spacing: 1.5px;
        background: #eef2ff; padding: 3px 14px;
        border-radius: 20px; width: fit-content;
        margin: -13px auto 12px auto;
        border: 1.5px solid #c7d2fe;
    }
    .mini-page-header-repeat {
        padding: 8px 0 6px 0;
        border-bottom: 1.5px solid #334155;
        margin-bottom: 16px;
        display: flex; align-items: center;
        justify-content: space-between;
    }
    .auto-break-badge {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #fff; font-size: 0.65rem; font-weight: 700;
        padding: 2px 8px; border-radius: 20px;
        letter-spacing: 0.5px;
        display: inline-flex; align-items: center; gap: 4px;
    }
</style>

<div id="mainEditorView">
<div class="container-fluid py-3 py-md-4">

    <!-- Header Section Terpadu -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light">
        <div class="d-flex align-items-center">
            <div class="bg-gradient-primary text-white rounded-3 p-2.5 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px;">
                <i class="fas fa-file-signature fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark">Buat Surat & Kustomisasi Template</h4>
                <small class="text-muted d-none d-sm-inline">Pilih jenis surat, ukuran kertas (A4, A3, Legal, Folio), posisi logo, aksen, dan generator tabel dinamis.</small>
            </div>
        </div>
        <a href="<?= base_url('direktur/karyawan/surat') ?>" class="btn btn-outline-secondary rounded-pill px-3.5 py-2 shadow-sm d-inline-flex align-items-center text-sm fw-semibold">
            <i class="fas fa-arrow-left me-1.5"></i> <span class="d-none d-md-inline">Kembali ke Daftar Surat</span><span class="d-inline d-md-none">Kembali</span>
        </a>
    </div>

    <!-- Main Row: Form + Live Interactive Preview -->
    <div class="row g-4">
        
        <!-- COL 1: Form Input & Template Customization -->
        <div class="col-12 col-xl-5">
            <div class="card employee-card-modern p-4 mb-4">
                
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show text-white rounded-3 mb-4" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('direktur/karyawan/surat/simpan') ?>" method="post" id="suratForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="html_full" id="htmlFull">
                    <input type="hidden" name="total_halaman" id="totalHalamanInput" value="1">

                    <!-- Section 1: Template & Layout Customization -->
                    <div class="form-section-title">
                        <i class="fas fa-palette text-primary"></i> Desain Layout, Ukuran Kertas & Template
                    </div>

                    <div class="row g-3 mb-3">
                        <!-- Preset Selector -->
                        <div class="col-md-7">
                            <label class="form-label text-sm fw-semibold text-dark">Pilih Preset Template</label>
                            <select name="template_layout" id="templateLayout" class="form-select form-select-custom fw-bold text-primary" onchange="applyPresetTemplate()">
                                <option value="standard" selected>📄 Preset 1: Standard Kop Atas (Line Black)</option>
                                <option value="accent_yellow">✨ Preset 2: Aksen Kuning Modern (Footer Alamat - Sample 2)</option>
                                <option value="blue_header">🔷 Preset 3: Bilah Biru Formal (Sample 3 & 4)</option>
                                <option value="compact_left">📌 Preset 4: Kop Ringkas Kiri</option>
                            </select>
                        </div>
                        <!-- Paper Size Selector -->
                        <div class="col-md-5">
                            <label class="form-label text-sm fw-semibold text-dark">Ukuran Kertas Dokumen</label>
                            <select name="paper_size" id="paperSize" class="form-select form-select-custom fw-semibold text-dark" onchange="renderLivePreview()">
                                <option value="A4" selected>A4 (210 x 297 mm)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Fine-tune Layout Controls -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-xs fw-semibold text-muted">Posisi Logo</label>
                            <select name="logo_position" id="logoPosition" class="form-select form-select-custom" onchange="renderLivePreview()">
                                <option value="top_right" selected>Kanan Atas</option>
                                <option value="top_left">Kiri Atas</option>
                                <option value="center">Tengah Atas</option>
                                <option value="none">Tanpa Logo</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-xs fw-semibold text-muted">Posisi Alamat Perusahaan</label>
                            <select name="address_position" id="addressPosition" class="form-select form-select-custom" onchange="renderLivePreview()">
                                <option value="top_left" selected>Kop Atas Kiri</option>
                                <option value="top_center">Kop Atas Tengah</option>
                                <option value="footer">Catatan Kaki (Footer)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-xs fw-semibold text-muted">Gaya Aksen Dekorasi</label>
                            <select name="accent_style" id="accentStyle" class="form-select form-select-custom" onchange="renderLivePreview()">
                                <option value="line" selected>Garis Hitam Standard</option>
                                <option value="yellow_corner">Aksen Kuning Corner (Sudut Atas & Bawah)</option>
                                <option value="blue_bar">Bilah Biru Formal (Banner Title)</option>
                                <option value="none">Polos (Tanpa Aksen)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-xs fw-semibold text-muted">Layout Tanda Tangan</label>
                            <select name="signature_layout" id="signatureLayout" class="form-select form-select-custom" onchange="toggleSignatureFields(); renderLivePreview();">
                                <option value="1_pihak" selected>1 Pihak / Standard (Kanan: PT CDW)</option>
                                <option value="2_pihak">2 Pihak Berdampingan (Pihak I & Pihak II)</option>
                                <option value="3_pihak">3 Pihak (Pihak I, Pihak II, Pihak III)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Input Detail Penandatangan Multi-Pihak -->
                    <div id="signatureFieldsContainer" class="p-3 bg-light rounded-3 border mb-4 shadow-xs">
                        <div class="fw-bold text-dark text-xs text-uppercase mb-2 d-flex align-items-center justify-content-between">
                            <span><i class="fas fa-file-signature text-primary me-1"></i> Teks & Nama Penandatangan Dokumen</span>
                            <span class="badge bg-secondary text-xs">Dapat Diubah</span>
                        </div>

                        <!-- Pihak 1 -->
                        <div id="p1Fields" class="row g-2 mb-2">
                            <div class="col-12"><small class="fw-bold text-primary" id="p1TitleLabel">Pihak Pertama / Hormat Kami</small></div>
                            <div class="col-md-4">
                                <input type="text" name="pihak_1_title" id="pihak1Title" class="form-control form-control-sm" placeholder="Judul (misal: Pihak Pertama,)" value="Hormat kami," oninput="renderLivePreview()">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="pihak_1_nama" id="pihak1Nama" class="form-control form-control-sm" placeholder="Nama / Perusahaan (misal: PT CDW / Nama Karyawan)" value="PT. CIPTA DUTA WACANA" oninput="renderLivePreview()">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="pihak_1_jabatan" id="pihak1Jabatan" class="form-control form-control-sm" placeholder="Jabatan (misal: Direktur Utama)" value="Direktur Utama" oninput="renderLivePreview()">
                            </div>
                        </div>

                        <!-- Pihak 2 -->
                        <div id="p2Fields" class="row g-2 mb-2 d-none">
                            <div class="col-12"><small class="fw-bold text-primary">Pihak Kedua</small></div>
                            <div class="col-md-4">
                                <input type="text" name="pihak_2_title" id="pihak2Title" class="form-control form-control-sm" placeholder="Judul (misal: Pihak Kedua,)" value="Pihak Kedua," oninput="renderLivePreview()">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="pihak_2_nama" id="pihak2Nama" class="form-control form-control-sm" placeholder="Nama / Perusahaan II" value="PT. CIPTA DUTA WACANA" oninput="renderLivePreview()">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="pihak_2_jabatan" id="pihak2Jabatan" class="form-control form-control-sm" placeholder="Jabatan II" value="Direktur Utama" oninput="renderLivePreview()">
                            </div>
                        </div>

                        <!-- Pihak 3 -->
                        <div id="p3Fields" class="row g-2 d-none">
                            <div class="col-12"><small class="fw-bold text-primary">Pihak Ketiga</small></div>
                            <div class="col-md-4">
                                <input type="text" name="pihak_3_title" id="pihak3Title" class="form-control form-control-sm" placeholder="Judul (misal: Pihak Ketiga,)" value="Pihak Ketiga," oninput="renderLivePreview()">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="pihak_3_nama" id="pihak3Nama" class="form-control form-control-sm" placeholder="Nama / Perusahaan III" value="PT. CIPTA DUTA WACANA" oninput="renderLivePreview()">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="pihak_3_jabatan" id="pihak3Jabatan" class="form-control form-control-sm" placeholder="Jabatan III" value="Direktur Utama" oninput="renderLivePreview()">
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Jenis Surat & Penerima -->
                    <div class="form-section-title mt-4">
                        <i class="fas fa-user-tag text-primary"></i> Informasi Dokumen & Penerima
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-sm fw-semibold text-dark">Jenis Surat <span class="text-danger">*</span></label>
                            <select name="jenis_surat" class="form-select form-select-custom" required id="jenisSurat" onchange="updateTemplateText(); renderLivePreview();">
                                <option value="">-- Pilih Jenis Surat --</option>
                                <?php foreach ($jenisList as $j): ?>
                                    <option value="<?= esc($j) ?>" <?= old('jenis_surat') == $j ? 'selected' : '' ?>><?= esc($j) ?></option>
                                <?php endforeach; ?>
                            </select>

                            <!-- Input jenis surat custom jika 'Lainnya' dipilih -->
                            <div id="jenisSuratLainnyaWrapper" class="mt-2 d-none">
                                <label class="form-label text-xs fw-bold text-primary">Ketikkan Jenis Surat Khusus:</label>
                                <input type="text" id="jenisSuratLainnya" name="jenis_surat_custom" class="form-control form-control-custom text-uppercase fw-bold border-primary" placeholder="Misal: SURAT TEGURAN LISAN / BAST..." value="<?= old('jenis_surat_custom') ?>" oninput="renderLivePreview()">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-sm fw-semibold text-dark">Karyawan Yang Dituju <span class="text-xs text-muted">(Opsional)</span></label>
                            <select name="karyawan_id" class="form-select form-select-custom" id="karyawanSelect" onchange="updateKaryawanText(); renderLivePreview();">
                                <option value="">-- Tanpa Karyawan Dituju (Opsional) --</option>
                                <?php foreach ($karyawanList as $k): ?>
                                    <option value="<?= $k['id'] ?>"
                                        data-nama="<?= esc($k['nama_lengkap']) ?>"
                                        data-nik="<?= esc($k['nik']) ?>"
                                        data-divisi="<?= esc($k['divisi']) ?>"
                                        data-jabatan="<?= esc($k['jabatan']) ?>"
                                        <?= old('karyawan_id') == $k['id'] ? 'selected' : '' ?>>
                                        <?= esc($k['nama_lengkap']) ?> - <?= esc($k['nik']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Info Preview Karyawan Selected -->
                    <div id="infoKaryawan" class="alert alert-info border-0 rounded-3 shadow-xs d-none mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-id-card fs-5 text-primary"></i>
                            <span id="infoKaryawanText" class="fw-semibold text-dark text-sm"></span>
                        </div>
                    </div>

                    <!-- Section 3: Tanggal, Perihal & Status -->
                    <div class="form-section-title mt-4">
                        <i class="fas fa-heading text-primary"></i> Detail Tanggal & Perihal
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label text-sm fw-semibold text-dark">Tanggal Surat <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_surat" id="tanggalSurat" class="form-control form-control-custom" value="<?= old('tanggal_surat', date('Y-m-d')) ?>" required onchange="renderLivePreview()">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label text-sm fw-semibold text-dark">Perihal Surat <span class="text-danger">*</span></label>
                            <input type="text" name="perihal" id="perihalInput" class="form-control form-control-custom" placeholder="Contoh: Surat Peringatan Pertama (SP-1)" value="<?= old('perihal') ?>" required oninput="renderLivePreview()">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-sm fw-semibold text-dark">Status Penerbitan</label>
                            <select name="status" id="statusSurat" class="form-select form-select-custom" onchange="renderLivePreview()">
                                <option value="draft" <?= old('status') == 'draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="diterbitkan" <?= old('status') == 'diterbitkan' ? 'selected' : '' ?>>Terbitkan Sekarang</option>
                            </select>
                        </div>
                    </div>

                    <!-- Section 4: Visual Block Editor for Isi Surat -->
                    <div class="form-section-title mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <i class="fas fa-layer-group text-primary"></i> Naskah & Konten Dokumen
                            <span class="auto-break-badge ms-2"><i class="fas fa-magic"></i> Auto Page Break</span>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold" onclick="addBlock('text')">
                                <i class="fas fa-paragraph me-1"></i> + Paragraf
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" onclick="addBlock('table')">
                                <i class="fas fa-table me-1"></i> + Tabel
                            </button>
                        </div>
                    </div>

                    <!-- Visual Block Editor Container -->
                    <div class="mb-3" id="contentBlocksEditor">
                        <!-- Blocks rendered by JS -->
                    </div>

                    <!-- Hidden textarea: assembled from blocks before submit -->
                    <textarea name="isi_surat" id="isiSurat" class="d-none"></textarea>

                    <div class="mb-4">
                        <label class="form-label text-sm fw-semibold text-dark">Catatan Internal <span class="text-xs text-muted">(Opsional)</span></label>
                        <textarea name="catatan" id="catatanInput" class="form-control form-control-custom" rows="2" placeholder="Catatan internal arsip..." oninput="renderLivePreview()"><?= old('catatan') ?></textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="<?= base_url('direktur/karyawan/surat') ?>" class="btn btn-light rounded-pill px-4 fw-semibold border">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm">
                            <i class="fas fa-save me-1.5"></i> Simpan Dokumen Surat
                        </button>
                    </div>

                </form>
            </div>
        </div>

        <!-- COL 2: Live Interactive Preview Box -->
        <div class="col-12 col-xl-7">
            <div class="sticky-top" style="top: 20px; z-index: 10;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-primary px-3 py-2 rounded-pill font-weight-bold text-sm shadow-xs">
                        <i class="fas fa-eye me-1.5"></i> Pratinjau Kertas Realistis
                    </span>
                    <div class="d-flex gap-2 align-items-center">
                        <small id="pageCountBadge" class="text-muted fw-semibold"></small>
                        <button type="button" class="btn btn-sm btn-dark rounded-pill px-3.5 py-1.5 fw-bold shadow-sm" onclick="openPrintConfirmation()">
                            <i class="fas fa-print me-1.5"></i> Pratinjau & Cetak PDF
                        </button>
                    </div>
                </div>
                <div class="letter-paper-preview paper-size-A4" id="previewPaper">
                    <!-- Live Content Rendered Here By JS -->
                </div>
            </div>
        </div>

    </div>
</div>
</div><!-- /#mainEditorView -->

<!-- Halaman Khusus Pratinjau & Cetak Surat (Dedicated Print View) -->
<div id="dedicatedPrintView" class="d-none min-vh-100" style="display: none !important; background: #0f172a; padding-bottom: 60px;">
    <!-- Top Sticky Header Control Bar (Hidden on Print) -->
    <div class="no-print sticky-top bg-dark border-bottom border-secondary px-4 py-3 shadow-lg d-flex justify-content-between align-items-center flex-wrap gap-2" style="z-index: 1050; background: #1e293b !important;">
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3.5 py-2 fw-semibold" onclick="closeDedicatedPrintView()">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali ke Form Editor
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

    <!-- Paper Centered Display Area -->
    <div class="container py-4">
        <div class="d-flex justify-content-center">
            <div id="dedicatedPrintPaper" class="letter-paper-preview paper-size-A4 shadow-lg">
                <!-- Copy of previewPaper rendered dynamically -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Sebelum Masuk Ke Pratinjau Cetak -->
<div class="modal fade" id="confirmPrintModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white border-0 rounded-top-4 py-3">
                <h6 class="modal-title fw-bold text-white" id="confirmPrintModalLabel">
                    <i class="fas fa-file-invoice me-2"></i> Konfirmasi Pratinjau & Cetak Dokumen
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning rounded-circle p-3 mb-2" style="width: 70px; height: 70px;">
                        <i class="fas fa-question-circle fa-2x"></i>
                    </div>
                </div>
                <h5 class="fw-bold text-dark mb-2">Apakah Dokumen Sudah Benar?</h5>
                <p class="text-muted text-sm mb-4">
                    Mohon periksa kembali data naskah dan susunan surat Anda. Jika sudah yakin benar, Anda akan masuk ke <strong>Halaman Pratinjau Cetak Khusus</strong>.
                </p>

                <div class="card bg-success bg-opacity-10 border border-success border-opacity-25 rounded-3 p-3 text-start mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-xs fw-bold text-success text-uppercase"><i class="fas fa-shield-alt me-1"></i> Proteksi Jumlah Halaman Dokumen</span>
                        <span class="badge bg-success rounded-pill px-3 py-1 fw-bold text-xs" id="printConfirmPageBadge">1 Halaman A4</span>
                    </div>
                    <div class="row g-2 text-xs">
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark mb-1">Jumlah Halaman (Terdeteksi Sistem):</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control text-center fw-bold bg-white text-success border-success fs-6 rounded-start-3" id="confirmPageCountInput" readonly value="1 Halaman A4 (Terdeteksi Sistem)">
                                <span class="input-group-text bg-success text-white fw-semibold rounded-end-3"><i class="fas fa-check-circle me-1"></i> Terkonfirmasi</span>
                            </div>
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-info-circle text-primary me-1"></i> Sistem secara otomatis mendeteksi dan memastikan dokumen ini disimpan sebagai <strong><span id="confirmPageCountText">1 Halaman</span> A4</strong> secara utuh dan konsisten.
                            </small>
                        </div>
                    </div>
                </div>

                <div class="card bg-light border-0 rounded-3 p-3 text-start mb-4 text-xs">
                    <div class="row g-2">
                        <div class="col-6"><strong>Jenis Surat:</strong> <br><span id="printConfirmJenis" class="text-primary fw-bold">-</span></div>
                        <div class="col-6"><strong>Penerima:</strong> <br><span id="printConfirmKaryawan" class="text-dark fw-bold">-</span></div>
                        <div class="col-6"><strong>Tanggal:</strong> <br><span id="printConfirmTanggal" class="text-dark">-</span></div>
                        <div class="col-6"><strong>Format Kertas:</strong> <br><span id="printConfirmPaper" class="text-success fw-bold">-</span></div>
                    </div>
                </div>

                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold border" data-bs-dismiss="modal">
                        <i class="fas fa-edit me-1"></i> Periksa / Edit Lagi
                    </button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" onclick="proceedToDedicatedPrintView()">
                        <i class="fas fa-arrow-right me-1"></i> Ya, Lanjut Cetak ➔
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
 </div>
</div>

<!-- Modal Paste / Impor Tabel dari Word / Excel / Web / PDF -->
<div class="modal fade" id="pasteTableModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-primary text-white border-0 rounded-top-4 py-3">
                <h6 class="modal-title fw-bold text-white">
                    <i class="fas fa-paste me-2"></i> Tempel (Paste) Tabel dari Word / Excel / Web / PDF
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="pasteTargetBlockId">
                <div class="alert alert-info border-0 rounded-3 text-xs mb-3">
                    <i class="fas fa-info-circle me-1.5"></i>
                    <strong>Petunjuk:</strong> Copy (Ctrl+C) tabel dari Word, Excel, Google Sheets, PDF, atau Web, lalu klik kotak teks di bawah dan tekan <strong>Ctrl+V</strong>.
                </div>

                <div class="mb-3">
                    <label class="form-label text-xs fw-bold text-muted">Kotak Tempel / Paste (Ctrl+V):</label>
                    <textarea id="pasteTextarea" class="form-control rounded-3 p-3 bg-light border font-monospace text-xs" rows="5" placeholder="Klik di sini lalu tekan Ctrl+V untuk menempelkan tabel..." onpaste="handlePasteTextareaEvent(event)" oninput="handlePasteTextareaEvent(event)"></textarea>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="pasteHasHeader" checked onchange="renderPastePreview()">
                    <label class="form-check-label text-xs fw-semibold" for="pasteHasHeader">
                        Gunakan Baris Pertama Sebagai Judul Kolom (Header)
                    </label>
                </div>

                <div id="pastePreviewContainer" class="d-none">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <label class="form-label text-xs fw-bold text-muted mb-0">Pratinjau Tabel Terdeteksi:</label>
                        <span class="badge bg-success text-xs" id="pasteDetectedCount">-</span>
                    </div>
                    <div class="table-responsive border rounded-3 mb-3" style="max-height:200px;">
                        <table class="table table-sm table-bordered mb-0 text-xs" id="pastePreviewTable">
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 rounded-bottom-4 py-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold" onclick="submitPastedTableModal()">
                    <i class="fas fa-check me-1"></i> Terapkan Ke Tabel Dokumen
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Data Base64 Logo dari Server -->
<script>
const companyLogoBase64 = '<?= $logoBase64 ?>';

// =====================================================================
// CONTENT BLOCKS SYSTEM
// =====================================================================
let blocks = [];
let blockCounter = 0;

// Usable body content heights (px) per paper size
// page1 = after full kop header, cont = continuation pages with mini-header
const PAPER_BODY_PX = {
    A4:     { page1: 750, cont: 950 },
    A3:     { page1: 1300, cont: 1500 },
    Letter: { page1: 700, cont: 900 },
    Legal:  { page1: 980, cont: 1180 },
    Folio:  { page1: 880, cont: 1080 },
};

function genBlkId() { return 'blk_' + (++blockCounter); }

function escHtml(str) {
    if (str === undefined || str === null) return '';
    return String(str)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
        .replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

// ---- Block Management ----
function addBlock(type) {
    const id = genBlkId();
    if (type === 'text') {
        blocks.push({ id, type: 'text', content: '' });
    } else if (type === 'table') {
        blocks.push({
            id, type: 'table',
            style: 'blue_header',
            headers: ['No', 'Deskripsi / Uraian', 'Keterangan'],
            rows: [['1','',''],['2','',''],['3','','']]
        });
    }
    renderBlocksEditor();
    renderLivePreview();
    // Scroll the new block into view
    setTimeout(() => {
        const last = document.querySelector('#contentBlocksEditor .content-block-card:last-child');
        if (last) last.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }, 100);
}

function removeBlock(id) {
    if (blocks.length <= 1 && !confirm('Hapus blok ini?')) return;
    blocks = blocks.filter(b => b.id !== id);
    renderBlocksEditor();
    renderLivePreview();
}

function moveBlock(id, dir) {
    const idx = blocks.findIndex(b => b.id === id);
    if (idx < 0) return;
    const ni = idx + dir;
    if (ni < 0 || ni >= blocks.length) return;
    [blocks[idx], blocks[ni]] = [blocks[ni], blocks[idx]];
    renderBlocksEditor();
    renderLivePreview();
}

function updateBlockText(id, value) {
    const b = blocks.find(b => b.id === id);
    if (b) b.content = value;
    renderLivePreview();
}

function updateTableHeader(id, ci, value) {
    const b = blocks.find(b => b.id === id);
    if (b) { b.headers[ci] = value; renderLivePreview(); }
}

function updateTableCell(id, ri, ci, value) {
    const b = blocks.find(b => b.id === id);
    if (b) { b.rows[ri][ci] = value; renderLivePreview(); }
}

function addTableRow(id) {
    const b = blocks.find(b => b.id === id);
    if (!b) return;
    const row = new Array(b.headers.length).fill('');
    row[0] = String(b.rows.length + 1);
    b.rows.push(row);
    renderBlockEditor(id);
    renderLivePreview();
}

function removeTableRow(id) {
    const b = blocks.find(b => b.id === id);
    if (b && b.rows.length > 1) { b.rows.pop(); renderBlockEditor(id); renderLivePreview(); }
}

function addTableCol(id) {
    const b = blocks.find(b => b.id === id);
    if (b && b.headers.length < 8) {
        b.headers.push('Kolom ' + (b.headers.length + 1));
        b.rows.forEach(r => r.push(''));
        renderBlockEditor(id);
        renderLivePreview();
    }
}

function removeTableCol(id) {
    const b = blocks.find(b => b.id === id);
    if (b && b.headers.length > 1) {
        b.headers.pop();
        b.rows.forEach(r => r.pop());
        renderBlockEditor(id);
        renderLivePreview();
    }
}

function updateTableStyle(id, style) {
    const b = blocks.find(b => b.id === id);
    if (b) { b.style = style; renderLivePreview(); }
}

// Re-render a single block editor in place (for add/remove row/col)
function renderBlockEditor(id) {
    const container = document.getElementById('contentBlocksEditor');
    if (!container) return;
    const el = container.querySelector(`[data-block-id="${id}"]`);
    const b = blocks.find(b => b.id === id);
    if (!el || !b) { renderBlocksEditor(); return; }
    const idx = blocks.findIndex(x => x.id === id);
    const tmp = document.createElement('div');
    tmp.innerHTML = buildBlockHtml(b, idx);
    el.replaceWith(tmp.firstElementChild);
}

// ---- Block HTML Builder ----
function buildBlockHtml(b, idx) {
    const first = idx === 0;
    const last  = idx === blocks.length - 1;

    if (b.type === 'text') {
        return `
        <div class="content-block-card" data-block-id="${b.id}">
            <div class="block-header-bar">
                <span class="block-type-badge text-badge"><i class="fas fa-paragraph"></i> Paragraf / Teks</span>
                <div class="d-flex gap-1 flex-wrap">
                    <button type="button" class="btn-blk-action" onclick="moveBlock('${b.id}',-1)" ${first?'disabled':''} title="Naik"><i class="fas fa-arrow-up"></i></button>
                    <button type="button" class="btn-blk-action" onclick="moveBlock('${b.id}',1)" ${last?'disabled':''} title="Turun"><i class="fas fa-arrow-down"></i></button>
                    <button type="button" class="btn-blk-action btn-blk-danger" onclick="removeBlock('${b.id}')" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                </div>
            </div>
            <div class="block-body-area">
                <textarea class="form-control block-textarea" rows="4"
                    placeholder="Tulis paragraf, kalimat, atau baris teks di sini..."
                    oninput="updateBlockText('${b.id}',this.value)">${escHtml(b.content)}</textarea>
            </div>
        </div>`;
    }

    if (b.type === 'table') {
        const styleOptions = [
            ['blue_header','🔷 Biru Formal'],
            ['gold_header','🟡 Emas Premium'],
            ['standard','⬜ Standard Abu'],
            ['striped','🟦 Striped Baris'],
        ].map(([v,l]) => `<option value="${v}" ${b.style===v?'selected':''}>${l}</option>`).join('');

        const headTh = b.headers.map((h,ci) =>
            `<th style="min-width:80px"><input type="text" class="form-control form-control-sm fw-bold px-1" value="${escHtml(h)}"
             oninput="updateTableHeader('${b.id}',${ci},this.value)"
             onpaste="handleCellPaste(event,'${b.id}',-1,${ci})"></th>`
        ).join('');

        const bodyTr = b.rows.map((row,ri) =>
            `<tr>${row.map((cell,ci) =>
                `<td><input type="text" class="form-control form-control-sm px-1" value="${escHtml(cell)}"
                 oninput="updateTableCell('${b.id}',${ri},${ci},this.value)"
                 onpaste="handleCellPaste(event,'${b.id}',${ri},${ci})"></td>`
            ).join('')}</tr>`
        ).join('');

        return `
        <div class="content-block-card table-block-card" data-block-id="${b.id}">
            <div class="block-header-bar table-blk-header">
                <span class="block-type-badge table-badge"><i class="fas fa-table"></i> Tabel Data</span>
                <div class="d-flex gap-1 flex-wrap align-items-center">
                    <select class="form-select form-select-sm rounded-2" style="width:auto;font-size:0.75rem;"
                        onchange="updateTableStyle('${b.id}',this.value)">${styleOptions}</select>
                    <button type="button" class="btn-blk-action btn-blk-info text-nowrap" onclick="openPasteTableModal('${b.id}')" title="Paste / Impor Tabel dari Word / Excel"><i class="fas fa-paste me-1"></i> Paste Tabel</button>
                    <button type="button" class="btn-blk-action btn-blk-success" onclick="addTableRow('${b.id}')" title="+Baris"><i class="fas fa-plus"></i> Baris</button>
                    <button type="button" class="btn-blk-action btn-blk-warning" onclick="removeTableRow('${b.id}')" title="-Baris"><i class="fas fa-minus"></i> Baris</button>
                    <button type="button" class="btn-blk-action btn-blk-success" onclick="addTableCol('${b.id}')" title="+Kolom"><i class="fas fa-plus"></i> Kolom</button>
                    <button type="button" class="btn-blk-action btn-blk-warning" onclick="removeTableCol('${b.id}')" title="-Kolom"><i class="fas fa-minus"></i> Kolom</button>
                    <button type="button" class="btn-blk-action" onclick="moveBlock('${b.id}',-1)" ${first?'disabled':''} title="Naik"><i class="fas fa-arrow-up"></i></button>
                    <button type="button" class="btn-blk-action" onclick="moveBlock('${b.id}',1)" ${last?'disabled':''} title="Turun"><i class="fas fa-arrow-down"></i></button>
                    <button type="button" class="btn-blk-action btn-blk-danger" onclick="removeBlock('${b.id}')" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                </div>
            </div>
            <div class="block-body-area table-block-body">
                <div class="table-responsive" style="max-height:260px;overflow-y:auto;">
                    <table class="table table-bordered table-sm mb-0" style="font-size:0.78rem;min-width:320px;">
                        <thead class="table-dark"><tr>${headTh}</tr></thead>
                        <tbody>${bodyTr}</tbody>
                    </table>
                </div>
                <small class="text-muted mt-1 d-block" style="font-size:0.7rem">
                    <i class="fas fa-info-circle me-1"></i>${b.rows.length} baris × ${b.headers.length} kolom
                </small>
            </div>
        </div>`;
    }
    return '';
}

function renderBlocksEditor() {
    const container = document.getElementById('contentBlocksEditor');
    if (!container) return;
    if (blocks.length === 0) {
        container.innerHTML = `
            <div class="block-empty-state">
                <i class="fas fa-layer-group fa-2x mb-2" style="color:#c7d2fe"></i>
                <p class="mb-1 fw-bold" style="color:#6366f1">Belum ada konten surat</p>
                <p class="mb-0 text-sm">Klik <strong>+ Paragraf</strong> untuk menulis teks, atau <strong>+ Tabel</strong> untuk membuat tabel data.</p>
            </div>`;
        return;
    }
    container.innerHTML = blocks.map((b, i) => buildBlockHtml(b, i)).join('');
}

// Assemble blocks → HTML string for hidden isi_surat textarea
function assembleIsiSurat() {
    return blocks.map(b => {
        if (b.type === 'text') {
            return b.content || '';
        }
        if (b.type === 'table') {
            let html = `\n<table class="custom-doc-table table-style-${b.style}">\n<thead>\n<tr>\n`;
            b.headers.forEach(h => { html += `  <th>${h}</th>\n`; });
            html += `</tr>\n</thead>\n<tbody>\n`;
            b.rows.forEach(row => {
                html += `<tr>\n`;
                row.forEach(cell => { html += `  <td>${cell}</td>\n`; });
                html += `</tr>\n`;
            });
            html += `</tbody>\n</table>\n`;
            return html;
        }
        return '';
    }).join('\n');
}

// Parse stored HTML → blocks array (for edit mode)
function parseHtmlToBlocks(html) {
    if (!html || !html.trim()) return [];
    const result = [];
    const tableRegex = /(<table[\s\S]*?<\/table>)/gi;
    const parts = html.split(tableRegex);
    parts.forEach(part => {
        const trimmed = part.trim();
        if (!trimmed) return;
        if (/^<table/i.test(trimmed)) {
            try {
                const parser = new DOMParser();
                const doc = parser.parseFromString(trimmed, 'text/html');
                const tbl = doc.querySelector('table');
                if (tbl) {
                    const styleM = (tbl.className || '').match(/table-style-(\w+)/);
                    const style = styleM ? styleM[1] : 'blue_header';
                    const headers = Array.from(tbl.querySelectorAll('thead th')).map(x => x.textContent.trim());
                    const rows = Array.from(tbl.querySelectorAll('tbody tr')).map(tr =>
                        Array.from(tr.querySelectorAll('td')).map(td => td.textContent.trim())
                    );
                    if (headers.length) {
                        result.push({ id: genBlkId(), type: 'table', style,
                            headers, rows: rows.length ? rows : [new Array(headers.length).fill('')] });
                    }
                }
            } catch(e) { /* skip bad table */ }
        } else {
            const txt = trimmed
                .replace(/<div[^>]*doc-page-break[^>]*>.*?<\/div>/gi, '')
                .replace(/<br\s*\/?>/gi, '\n')
                .replace(/<[^>]+>/g, '')
                .trim();
            if (txt) result.push({ id: genBlkId(), type: 'text', content: txt });
        }
    });
    return result;
}

// =====================================================================
// RENDER LIVE PREVIEW with AUTO PAGE BREAK
// =====================================================================
const templates = {
    'Kontrak Kerja': `Dengan hormat,\n\nBersama surat ini kami menyatakan bahwa:\n\nNama    : [NAMA KARYAWAN]\nJabatan : [JABATAN]\nDivisi  : [DIVISI]\n\nDinyatakan sebagai karyawan dengan status Kontrak terhitung mulai tanggal [TANGGAL].\n\nAdapun hak dan kewajiban karyawan mengikuti peraturan perusahaan yang berlaku.\n\nDemikian surat kontrak ini dibuat untuk dapat dipergunakan sebagaimana mestinya.`,
    'Surat Peringatan (SP1)': `Dengan hormat,\n\nBersama surat ini kami memberikan Surat Peringatan Pertama (SP-1) kepada:\n\nNama    : [NAMA KARYAWAN]\nJabatan : [JABATAN]\nDivisi  : [DIVISI]\n\nDengan alasan:\n[Jelaskan alasan / pelanggaran yang dilakukan]\n\nKami berharap Saudara/i dapat memperbaiki perilaku dan kinerja ke depannya.\n\nApabila dalam waktu 30 hari ke depan tidak ada perbaikan, maka akan diberikan Surat Peringatan berikutnya.`,
    'Surat Peringatan (SP2)': `Dengan hormat,\n\nBersama surat ini kami memberikan Surat Peringatan Kedua (SP-2) kepada:\n\nNama    : [NAMA KARYAWAN]\nJabatan : [JABATAN]\nDivisi  : [DIVISI]\n\nMerujuk pada SP-1 yang telah dikeluarkan sebelumnya, dan atas pelanggaran:\n[Jelaskan pelanggaran lanjutan]\n\nIni merupakan peringatan terakhir sebelum tindakan lebih lanjut diambil.`,
    'Surat Keterangan Kerja': `Yang bertanda tangan di bawah ini, menerangkan bahwa:\n\nNama    : [NAMA KARYAWAN]\nJabatan : [JABATAN]\nDivisi  : [DIVISI]\n\nBenar-benar merupakan karyawan aktif di perusahaan kami.\n\nSurat keterangan ini dibuat atas permintaan yang bersangkutan dan untuk dipergunakan sebagaimana mestinya.`,
};

const perihalDefaults = {
    'Kontrak Kerja': 'Surat Kontrak Kerja',
    'Surat Peringatan (SP1)': 'Surat Peringatan Pertama (SP-1)',
    'Surat Peringatan (SP2)': 'Surat Peringatan Kedua (SP-2)',
    'Surat Peringatan (SP3)': 'Surat Peringatan Ketiga (SP-3)',
    'Surat Keterangan Kerja': 'Surat Keterangan Kerja',
    'Surat Tugas': 'Surat Penugasan',
    'Surat Pernyataan': 'Surat Pernyataan',
};

function applyPresetTemplate() {
    const layout = document.getElementById('templateLayout').value;
    const logoPos = document.getElementById('logoPosition');
    const addrPos = document.getElementById('addressPosition');
    const accent  = document.getElementById('accentStyle');
    if (layout === 'standard')      { logoPos.value='top_right'; addrPos.value='top_left'; accent.value='line'; }
    else if (layout==='accent_yellow') { logoPos.value='top_right'; addrPos.value='footer';   accent.value='yellow_corner'; }
    else if (layout==='blue_header')   { logoPos.value='top_right'; addrPos.value='top_left'; accent.value='blue_bar'; }
    else if (layout==='compact_left')  { logoPos.value='top_left';  addrPos.value='top_left'; accent.value='line'; }
    renderLivePreview();
}

function updateTemplateText() {
    const jenisSel = document.getElementById('jenisSurat').value;
    const customWrapper = document.getElementById('jenisSuratLainnyaWrapper');
    if (jenisSel === 'Lainnya') { customWrapper && customWrapper.classList.remove('d-none'); }
    else { customWrapper && customWrapper.classList.add('d-none'); }

    const perihalEl = document.getElementById('perihalInput');
    if (perihalDefaults[jenisSel] && perihalEl) perihalEl.value = perihalDefaults[jenisSel];

    if (templates[jenisSel] && blocks.length === 0) {
        blocks = [{ id: genBlkId(), type: 'text', content: templates[jenisSel] }];
        renderBlocksEditor();
    } else if (templates[jenisSel] && blocks.length === 1 && blocks[0].type === 'text' && blocks[0].content.trim() === '') {
        blocks[0].content = templates[jenisSel];
        renderBlocksEditor();
    }
    updateKaryawanText();
}

function updateKaryawanText() {
    const sel = document.getElementById('karyawanSelect');
    const opt = sel ? sel.options[sel.selectedIndex] : null;
    const infoEl  = document.getElementById('infoKaryawan');
    const infoTxt = document.getElementById('infoKaryawanText');
    if (opt && opt.value) {
        const divisi  = opt.dataset.divisi  || '-';
        const jabatan = opt.dataset.jabatan || '-';
        const nama    = opt.dataset.nama    || opt.text.split(' - ')[0];
        if (infoTxt) infoTxt.textContent = `${opt.text} | Divisi: ${divisi} | Jabatan: ${jabatan}`;
        if (infoEl) infoEl.classList.remove('d-none');
        blocks.forEach(b => {
            if (b.type === 'text') {
                b.content = b.content
                    .replaceAll('[NAMA KARYAWAN]', nama)
                    .replaceAll('[JABATAN]', jabatan)
                    .replaceAll('[DIVISI]', divisi);
            }
        });
        renderBlocksEditor();
    } else {
        if (infoEl) infoEl.classList.add('d-none');
    }
    renderLivePreview();
}

// ---- Build block HTML for document preview (not editor) ----
function blockToDocHtml(b) {
    if (b.type === 'text') {
        if (!b.content || !b.content.trim()) return '';
        const lines = b.content.split('\n');
        let html = '<div style="line-height:1.8; margin-bottom:14px; font-size:0.92rem; color:#1e293b;">';
        let inKvGroup = false;

        lines.forEach((line) => {
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
    }
    if (b.type === 'table') {
        const ths = b.headers.map(h => `<th>${h}</th>`).join('');
        const trs = b.rows.map(row =>
            `<tr>${row.map(c=>`<td>${c}</td>`).join('')}</tr>`
        ).join('');
        return `<table class="custom-doc-table table-style-${b.style}" style="break-inside:avoid;page-break-inside:avoid;">
<thead><tr>${ths}</tr></thead><tbody>${trs}</tbody></table>`;
    }
    return '';
}

function toggleSignatureFields() {
    const layout = (document.getElementById('signatureLayout') || {}).value || '1_pihak';
    const p1 = document.getElementById('p1Fields');
    const p2 = document.getElementById('p2Fields');
    const p3 = document.getElementById('p3Fields');
    const p1TitleLabel = document.getElementById('p1TitleLabel');

    if (layout === '1_pihak') {
        if (p1) p1.classList.remove('d-none');
        if (p2) p2.classList.add('d-none');
        if (p3) p3.classList.add('d-none');
        if (p1TitleLabel) p1TitleLabel.textContent = 'Pihak Pertama / Hormat Kami';
    } else if (layout === '2_pihak') {
        if (p1) p1.classList.remove('d-none');
        if (p2) p2.classList.remove('d-none');
        if (p3) p3.classList.add('d-none');
        if (p1TitleLabel) p1TitleLabel.textContent = 'Pihak Pertama';
    } else if (layout === '3_pihak') {
        if (p1) p1.classList.remove('d-none');
        if (p2) p2.classList.remove('d-none');
        if (p3) p3.classList.remove('d-none');
        if (p1TitleLabel) p1TitleLabel.textContent = 'Pihak Pertama';
    }
}

// Build kop surat (full header for page 1)
function buildKopHtml(logoPos, addrPos, accent, logoHtml, addressHtml) {
    let hTop = '';
    if (logoPos === 'top_right') {
        hTop = `<div class="d-flex justify-content-between align-items-start mb-3"><div class="flex-grow-1 me-3">${addressHtml}</div><div class="flex-shrink-0" style="min-width:200px;min-height:130px;text-align:right;">${logoHtml}</div></div>`;
    } else if (logoPos === 'top_left') {
        hTop = `<div class="d-flex justify-content-between align-items-start mb-3"><div class="flex-shrink-0 me-3" style="min-width:200px;min-height:130px;">${logoHtml}</div><div class="flex-grow-1">${addressHtml}</div></div>`;
    } else if (logoPos === 'center') {
        hTop = `<div class="text-center mb-3"><div class="mb-2 d-flex justify-content-center" style="min-height:130px;">${logoHtml}</div>${addressHtml}</div>`;
    } else {
        hTop = `<div class="mb-3">${addressHtml}</div>`;
    }
    return hTop;
}

// Build mini kop for continuation pages
function buildMiniKopHtml(pageNum) {
    return `<div class="mini-page-header-repeat">
        <small style="font-size:0.78rem;font-weight:700;color:#1e293b;">PT. CIPTA DUTA WACANA</small>
        <small style="font-size:0.72rem;color:#64748b;">Halaman ${pageNum}</small>
    </div>`;
}

function renderLivePreview() {
    const paper = document.getElementById('previewPaper');
    if (!paper) return;

    const paperSizeVal = (document.getElementById('paperSize') || {}).value || 'A4';
    paper.className = `letter-paper-preview paper-size-${paperSizeVal}`;

    const logoPos = (document.getElementById('logoPosition')    || {value:'top_right'}).value;
    const addrPos = (document.getElementById('addressPosition') || {value:'top_left'}).value;
    const accent  = (document.getElementById('accentStyle')     || {value:'line'}).value;

    let jenisSel = (document.getElementById('jenisSurat') || {value:''}).value || 'SURAT KARYAWAN';
    let jenis = jenisSel;
    if (jenisSel === 'Lainnya') {
        const cv = (document.getElementById('jenisSuratLainnya') || {value:''}).value.trim();
        jenis = cv ? cv.toUpperCase() : 'SURAT KARYAWAN';
    }

    const perihal = (document.getElementById('perihalInput')  || {value:'Perihal...'}).value || 'Perihal Surat...';
    const tanggal = (document.getElementById('tanggalSurat')  || {value:''}).value;
    const catatan = (document.getElementById('catatanInput')  || {value:''}).value || '';
    const dateObj = new Date(tanggal);
    const dateFormatted = isNaN(dateObj) ? tanggal : dateObj.toLocaleDateString('id-ID', { day:'numeric', month:'long', year:'numeric' });

    let logoHtml = '';
    if (logoPos !== 'none' && companyLogoBase64) {
        logoHtml = `<img src="${companyLogoBase64}" alt="CDW Logo" style="height:130px;max-height:140px;width:auto;max-width:280px;object-fit:contain;display:block;">`;
    }

    let addrPadStyle = '';
    if (accent === 'yellow_corner' && addrPos === 'top_left') addrPadStyle = 'padding-left:45px;position:relative;z-index:3;';

    let addressHtml = '';
    if (addrPos === 'top_left' || addrPos === 'top_center') {
        const align = addrPos === 'top_center' ? 'text-align:center;' : '';
        addressHtml = `<div style="${align}${addrPadStyle}line-height:1.45;">
            <strong style="font-size:1.05rem;color:#000;">PT. CIPTA DUTA WACANA</strong><br>
            <span style="font-size:0.88rem;font-weight:600;color:#1e293b;">Beltway Office Park Tower B Lantai 5</span><br>
            <span style="font-size:0.82rem;color:#334155;">Jl. TB Simatupang No. 41 Ragunan-Pasar Minggu, Jakarta Selatan</span><br>
            <span style="font-size:0.78rem;color:#475569;">Phone: (+62-21) 29857462; 29215392; 29084991 | Fax: (+62-21) 29857201</span>
        </div>`;
    }

    let accentHtml = '';
    if (accent === 'line') accentHtml = `<div style="border-bottom:2px solid #0f172a;margin-bottom:22px;"></div>`;
    else if (accent === 'blue_bar') accentHtml = `<div class="blue-header-banner text-center my-3">${jenis}</div>`;

    let cornerAccents = '';
    if (accent === 'yellow_corner') {
        cornerAccents = `<svg style="position:absolute;top:0;left:0;width:200px;height:200px;pointer-events:none;z-index:1;opacity:0.85;" viewBox="0 0 280 280"><path d="M 0 0 L 280 0 Q 70 70 0 280 Z" fill="#f5a600"/></svg>
        <svg style="position:absolute;bottom:0;right:0;width:160px;height:160px;pointer-events:none;z-index:1;opacity:0.85;" viewBox="0 0 260 260"><path d="M 260 260 L 0 260 Q 190 190 260 0 Z" fill="#f5a600"/></svg>`;
    }

    let footerAddrHtml = '';
    if (addrPos === 'footer') {
        footerAddrHtml = `
        <div style="position:absolute;bottom:15px;left:30px;right:180px;font-size:0.7rem;color:#475569;line-height:1.35;z-index:5;">
            <strong>PT. Cipta Duta Wacana</strong><br>
            Beltway Office Park Tower B Lt.5, Jl. Letjen TB Simatupang No.41, Ragunan, Pasar Minggu, Jakarta Selatan 12550<br>
            Tel: +62-21 29857462 | Fax: +62-21 29857201 |
            <span style="color:#0284c7">www.cdw-engineering.com</span>
        </div>`;
    }

    // ---- Auto Page Break Engine ----
    const paperWidthPx = paper.clientWidth || 794;
    const bodyLimits = PAPER_BODY_PX[paperSizeVal] || PAPER_BODY_PX.A4;

    // Decompose blocks for pagination so multi-paragraph text blocks can split across pages
    const renderBlocks = [];
    blocks.forEach(b => {
        if (b.type === 'text') {
            const paras = (b.content || '').split(/\n\n+/).filter(p => p.trim() !== '');
            if (paras.length > 1) {
                paras.forEach(p => renderBlocks.push({ type: 'text', content: p, originalId: b.id }));
            } else {
                renderBlocks.push(b);
            }
        } else {
            renderBlocks.push(b);
        }
    });

    // Measure each block height using offscreen div
    const measurer = document.createElement('div');
    measurer.style.cssText = `position:absolute;visibility:hidden;left:-9999px;top:0;
        width:${paperWidthPx - 120}px;font-family:Inter,Arial,sans-serif;font-size:0.9rem;`;
    document.body.appendChild(measurer);

    const blockHeights = renderBlocks.map(b => {
        const div = document.createElement('div');
        div.innerHTML = blockToDocHtml(b);
        measurer.appendChild(div);
        const h = div.getBoundingClientRect().height || 40;
        measurer.removeChild(div);
        return Math.ceil(h) + 16; // +16px margin buffer
    });
    document.body.removeChild(measurer);

    // Group blocks into pages
    const pages = [[]];
    let curPageHeight = 0;
    let pageNum = 1;

    renderBlocks.forEach((b, i) => {
        const bh = blockHeights[i] || 50;
        const limit = pageNum === 1 ? bodyLimits.page1 : bodyLimits.cont;

        if (curPageHeight + bh > limit && curPageHeight > 0) {
            // Start new page
            pages.push([]);
            pageNum++;
            curPageHeight = bh;
        } else {
            curPageHeight += bh;
        }
        pages[pages.length - 1].push(b);
    });

    const totalPages = pages.length;

    // Update page count badge
    const badge = document.getElementById('pageCountBadge');
    if (badge) {
        if (totalPages > 1) {
            badge.innerHTML = `<i class="fas fa-copy text-primary me-1"></i> ${totalPages} Halaman (Auto)`;
        } else {
            badge.textContent = '';
        }
    }

    // Build full kop surat HTML for page 1
    const kopTopHtml = buildKopHtml(logoPos, addrPos, accent, logoHtml, addressHtml);

    // Signature block
    const sigLayout = (document.getElementById('signatureLayout') || {value:'1_pihak'}).value;
    const mbStyle = addrPos === 'footer' ? 'margin-bottom:70px;' : 'margin-bottom:15px;';

    const p1Title = (document.getElementById('pihak1Title') || {}).value || 'Hormat kami,';
    const p1Nama  = (document.getElementById('pihak1Nama') || {}).value || (sigLayout==='1_pihak'?'PT. CIPTA DUTA WACANA':'[ PIHAK I ]');
    const p1Jab   = (document.getElementById('pihak1Jabatan') || {}).value || (sigLayout==='1_pihak'?'Direktur Utama':'( ......................... )');

    const p2Title = (document.getElementById('pihak2Title') || {}).value || 'Pihak Kedua,';
    const p2Nama  = (document.getElementById('pihak2Nama') || {}).value || 'PT. CIPTA DUTA WACANA';
    const p2Jab   = (document.getElementById('pihak2Jabatan') || {}).value || 'Direktur Utama';

    const p3Title = (document.getElementById('pihak3Title') || {}).value || 'Pihak Ketiga,';
    const p3Nama  = (document.getElementById('pihak3Nama') || {}).value || 'PT. CIPTA DUTA WACANA';
    const p3Jab   = (document.getElementById('pihak3Jabatan') || {}).value || 'Direktur Utama';

    let signatureHtml = '';

    if (sigLayout === '2_pihak') {
        signatureHtml = `
        <div class="row pt-4 text-center mt-auto" style="${mbStyle}">
            <div class="col-6">
                <p class="text-sm mb-5">${p1Title}<br><strong>${p1Nama}</strong></p>
                <p class="fw-bold text-dark mb-0 border-bottom d-inline-block pb-1" style="min-width:160px;">${p1Jab}</p>
            </div>
            <div class="col-6">
                <p class="text-sm mb-5">${p2Title}<br><strong>${p2Nama}</strong></p>
                <p class="fw-bold text-dark mb-0 border-bottom d-inline-block pb-1" style="min-width:160px;">${p2Jab}</p>
            </div>
        </div>`;
    } else if (sigLayout === '3_pihak') {
        signatureHtml = `
        <div class="row pt-4 text-center mt-auto" style="${mbStyle}">
            <div class="col-4">
                <p class="text-sm mb-5">${p1Title}<br><strong>${p1Nama}</strong></p>
                <p class="fw-bold text-dark mb-0 border-bottom d-inline-block pb-1" style="min-width:120px;">${p1Jab}</p>
            </div>
            <div class="col-4">
                <p class="text-sm mb-5">${p2Title}<br><strong>${p2Nama}</strong></p>
                <p class="fw-bold text-dark mb-0 border-bottom d-inline-block pb-1" style="min-width:120px;">${p2Jab}</p>
            </div>
            <div class="col-4">
                <p class="text-sm mb-5">${p3Title}<br><strong>${p3Nama}</strong></p>
                <p class="fw-bold text-dark mb-0 border-bottom d-inline-block pb-1" style="min-width:120px;">${p3Jab}</p>
            </div>
        </div>`;
    } else {
        signatureHtml = `
        <div class="row pt-4 text-center mt-auto" style="${mbStyle}">
            <div class="col-6 ms-auto">
                <p class="text-sm mb-5">${p1Title}<br><strong>${p1Nama}</strong></p>
                <p class="fw-bold text-dark mb-0 border-bottom d-inline-block pb-1" style="min-width:160px;">${p1Jab}</p>
            </div>
        </div>`;
    }

    // Catatan block
    const catatanHtml = catatan
        ? `<div class="alert alert-warning border-0 rounded-3 text-dark text-xs p-2 mb-3" style="font-size:0.78rem;">
              <strong>Catatan Internal:</strong> ${catatan}
           </div>`
        : '';

    // Build pages HTML
    let paperHtml = '';

    pages.forEach((pageBlocks, pi) => {
        const isFirstPage = (pi === 0);
        const isLastPage  = (pi === pages.length - 1);
        const pageNum     = pi + 1;

        // Break indicator before page 2+
        if (!isFirstPage) {
            paperHtml += `<div class="page-break-indicator" data-label="📄 HALAMAN ${pageNum}"></div>`;
        }

        // Page Corner Accents
        let pageCornerTop = '';
        let pageCornerBottom = '';
        if (accent === 'yellow_corner') {
            if (isFirstPage) {
                pageCornerTop = `<svg style="position:absolute;top:0;left:0;width:200px;height:200px;pointer-events:none;z-index:1;opacity:0.85;" viewBox="0 0 280 280"><path d="M 0 0 L 280 0 Q 70 70 0 280 Z" fill="#f5a600"/></svg>`;
            }
            pageCornerBottom = `<svg style="position:absolute;bottom:0;right:0;width:160px;height:160px;pointer-events:none;z-index:1;opacity:0.85;" viewBox="0 0 260 260"><path d="M 260 260 L 0 260 Q 190 190 260 0 Z" fill="#f5a600"/></svg>`;
        }

        // Footer Address for this page
        let pageFooterAddr = '';
        if (addrPos === 'footer') {
            pageFooterAddr = `
            <div style="position:absolute;bottom:18px;left:40px;right:180px;font-size:0.7rem;color:#475569;line-height:1.35;z-index:5;">
                <strong>PT. Cipta Duta Wacana</strong><br>
                Beltway Office Park Tower B Lt.5, Jl. Letjen TB Simatupang No.41, Ragunan, Pasar Minggu, Jakarta Selatan 12550<br>
                Tel: +62-21 29857462 | Fax: +62-21 29857201 | <span style="color:#0284c7">www.cdw-engineering.com</span>
            </div>`;
        }

        // Page number at bottom-right
        let pageNumIndicator = `<div style="position:absolute;bottom:18px;right:30px;font-size:0.82rem;font-weight:700;color:#1e3c72;z-index:5;text-align:right;">Halaman ${pageNum} dari ${totalPages}</div>`;

        // Start sheet
        paperHtml += `<div class="doc-page-sheet">`;
        paperHtml += pageCornerTop;
        paperHtml += pageCornerBottom;

        paperHtml += `<div style="position:relative;z-index:2;display:flex;flex-direction:column;flex-grow:1;height:100%;">`;

        if (isFirstPage) {
            // Page 1 — Full Header
            paperHtml += `<div style="margin-bottom:16px;position:relative;z-index:3;">${kopTopHtml}</div>`;
            paperHtml += accentHtml;
            paperHtml += accent !== 'blue_bar' ? `<h5 class="text-center fw-bold text-dark text-uppercase mb-3" style="letter-spacing:0.5px;">${jenis}</h5>` : '';
            paperHtml += `<div class="d-flex justify-content-between text-sm fw-semibold text-secondary mb-3 pb-2 border-bottom border-light">
                <span><strong>Nomor:</strong> [Auto-Generated]</span>
                <span><strong>Tanggal:</strong> ${dateFormatted}</span>
            </div>`;
            paperHtml += `<div class="mb-3"><strong class="text-dark">Perihal: ${perihal}</strong></div>`;
        } else {
            // Continuation Page Mini Header
            paperHtml += buildMiniKopHtml(pageNum);
        }

        // Content Blocks
        paperHtml += `<div class="page-content-area" style="flex-grow:1;">`;
        pageBlocks.forEach(b => {
            paperHtml += blockToDocHtml(b);
        });
        paperHtml += `</div>`;

        // Signature Block & Catatan only on Last Page
        if (isLastPage) {
            paperHtml += `<div style="margin-top:auto;padding-top:20px;">`;
            paperHtml += catatanHtml;
            paperHtml += signatureHtml;
            paperHtml += `</div>`;
        }

        paperHtml += `</div>`; // end inner div

        paperHtml += pageFooterAddr;
        paperHtml += pageNumIndicator;

        paperHtml += `</div>`; // end .doc-page-sheet
    });

    // Empty State if no blocks
    if (blocks.length === 0) {
        paperHtml = `<div class="doc-page-sheet">
            <div style="position:relative;z-index:2;display:flex;flex-direction:column;flex-grow:1;">
                <div style="min-height:120px;margin-bottom:12px;">${kopTopHtml}</div>
                ${accentHtml}
                <div style="min-height:200px;display:flex;align-items:center;justify-content:center;color:#94a3b8;">
                    <div class="text-center">
                        <i class="fas fa-file-alt fa-3x mb-3" style="opacity:0.3;"></i>
                        <p class="text-sm">Konten surat akan muncul di sini.</p>
                    </div>
                </div>
                <div style="margin-top:auto;">${catatanHtml}${signatureHtml}</div>
            </div>
            ${footerAddrHtml}
        </div>`;
    }

    paper.innerHTML = paperHtml;

    const totalHalamanInput = document.getElementById('totalHalamanInput');
    if (totalHalamanInput) {
        const detectedSheets = paper.querySelectorAll('.doc-page-sheet').length || 1;
        totalHalamanInput.value = detectedSheets;
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Start with one empty text block
    addBlock('text');

    // Assemble isi_surat from blocks before form submit
    const form = document.getElementById('suratForm');
    if (form) {
        form.addEventListener('submit', function () {
            const hidden = document.getElementById('isiSurat');
            const htmlFull = document.getElementById('htmlFull');
            const paper = document.getElementById('previewPaper');
            const totalHalamanInput = document.getElementById('totalHalamanInput');
            if (hidden) hidden.value = assembleIsiSurat();
            if (htmlFull && paper) htmlFull.value = paper.innerHTML;
            if (totalHalamanInput && paper) {
                const detectedSheets = paper.querySelectorAll('.doc-page-sheet').length || 1;
                totalHalamanInput.value = detectedSheets;
            }
        });
    }

    // Re-render preview on any design field change
    ['templateLayout','paperSize','logoPosition','addressPosition','accentStyle','signatureLayout',
     'jenisSurat','jenisSuratLainnya','karyawanSelect','tanggalSurat','perihalInput','statusSurat','catatanInput',
     'pihak1Title','pihak1Nama','pihak1Jabatan','pihak2Title','pihak2Nama','pihak2Jabatan','pihak3Title','pihak3Nama','pihak3Jabatan'
    ].forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.addEventListener('change', renderLivePreview); el.addEventListener('input', renderLivePreview); }
    });

    toggleSignatureFields();
    renderBlocksEditor();
    renderLivePreview();
});

// =====================================================================
// DEDICATED PRINT VIEW & CONFIRMATION MODAL HANDLERS
// =====================================================================
function openPrintConfirmation() {
    const jenisSel = (document.getElementById('jenisSurat') || {}).value || 'Surat Karyawan';
    const sel = document.getElementById('karyawanSelect');
    const opt = sel ? sel.options[sel.selectedIndex] : null;
    const nama = opt && opt.value ? (opt.dataset.nama || opt.text.split(' - ')[0]) : '-';
    const tanggal = (document.getElementById('tanggalSurat') || {}).value || '-';
    const paper = (document.getElementById('paperSize') || {}).value || 'A4';
    
    const sheets = document.querySelectorAll('#previewPaper .doc-page-sheet');
    const pageCount = sheets.length > 0 ? sheets.length : 1;
    const pageTxt = pageCount + ' Halaman';

    const printConfirmJenis = document.getElementById('printConfirmJenis');
    const printConfirmKaryawan = document.getElementById('printConfirmKaryawan');
    const printConfirmTanggal = document.getElementById('printConfirmTanggal');
    const printConfirmPaper = document.getElementById('printConfirmPaper');

    const confirmPageCountInput = document.getElementById('confirmPageCountInput');
    const printConfirmPageBadge = document.getElementById('printConfirmPageBadge');
    const confirmPageCountText = document.getElementById('confirmPageCountText');
    const totalHalamanInput = document.getElementById('totalHalamanInput');

    if (printConfirmJenis) printConfirmJenis.textContent = jenisSel;
    if (printConfirmKaryawan) printConfirmKaryawan.textContent = nama;
    if (printConfirmTanggal) printConfirmTanggal.textContent = tanggal;
    if (printConfirmPaper) printConfirmPaper.textContent = paper + ' (' + pageTxt + ')';

    if (totalHalamanInput) totalHalamanInput.value = pageCount;
    if (confirmPageCountInput) confirmPageCountInput.value = pageCount + ' Halaman ' + paper + ' (Terdeteksi Otomatis oleh Sistem)';
    if (printConfirmPageBadge) printConfirmPageBadge.textContent = pageCount + ' Halaman ' + paper;
    if (confirmPageCountText) confirmPageCountText.textContent = pageCount + ' Halaman';

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

    const form = document.getElementById('suratForm');
    if (form) {
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        let printFlag = document.getElementById('printNowFlag');
        if (!printFlag) {
            printFlag = document.createElement('input');
            printFlag.type = 'hidden';
            printFlag.name = 'print_now';
            printFlag.id = 'printNowFlag';
            form.appendChild(printFlag);
        }
        printFlag.value = '1';

        const hidden = document.getElementById('isiSurat');
        const htmlFull = document.getElementById('htmlFull');
        const paper = document.getElementById('previewPaper');
        if (hidden && typeof assembleIsiSurat === 'function') {
            hidden.value = assembleIsiSurat();
        }
        if (htmlFull && paper) {
            htmlFull.value = paper.innerHTML;
        }

        Swal.fire({
            title: 'Menyimpan Dokumen...',
            text: 'Sistem sedang menyimpan surat dan menyiapkan pratinjau cetak...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        form.submit();
    }
}

function closeDedicatedPrintView() {
    const mainView = document.getElementById('mainEditorView');
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

// =====================================================================
// TABLE COPY-PASTE & SMART PARSER SYSTEM
// =====================================================================
let currentPasteData = [];

function preprocessTableString(text) {
    if (!text) return '';
    let str = text.trim();

    // 1. Break joined header and first row item e.g. "Total Harga (Rp)1[" or "Total Harga (Rp)1"
    str = str.replace(/([a-zA-Z0-9\)\>\]])\s*(\d+)\s*(\[)/g, '$1\n$2$3');

    // 2. Break joined bracket end and next row number e.g. "][Misal: 5.000.000]2["
    str = str.replace(/(\])\s*(\d+)\s*(\[)/g, '$1\n$2$3');

    // 3. Break joined bracket end and Total row e.g. "][Misal: 1.500.000]-Total"
    str = str.replace(/(\])\s*(\-?\s*Total\s*Keseluruhan|\-?\s*Total)/gi, '$1\n$2');

    return str;
}

function parseHeaderLine(line) {
    if (!line) return [];
    let str = line.trim();
    if (!str) return [];

    if (str.includes('\t')) {
        const parts = str.split('\t').map(s => s.trim()).filter(Boolean);
        if (parts.length >= 3) return parts;
    }

    if (str.includes('|')) {
        const parts = str.split('|').map(s => s.trim()).filter(Boolean);
        if (parts.length >= 3) return parts;
    }

    let lowerStr = str.toLowerCase();

    if (lowerStr.includes('deskripsi') || lowerStr.includes('uraian')) {
        let idxDesk = Math.max(lowerStr.indexOf('deskripsi'), lowerStr.indexOf('uraian'));
        let idxVol = Math.max(lowerStr.indexOf('volume'), lowerStr.indexOf('vol'), lowerStr.indexOf('qty'), lowerStr.indexOf('jumlah'), lowerStr.indexOf('satuan'));
        
        let idxHarga = lowerStr.indexOf('harga satuan');
        if (idxHarga === -1) idxHarga = lowerStr.indexOf('harga');

        let idxTotal = lowerStr.indexOf('total harga');
        if (idxTotal === -1 && lowerStr.lastIndexOf('total') > idxHarga) idxTotal = lowerStr.lastIndexOf('total');

        if (idxDesk > 0 && idxVol > idxDesk && idxHarga > idxVol && idxTotal > idxHarga) {
            return [
                str.substring(0, idxDesk).trim() || 'No',
                str.substring(idxDesk, idxVol).trim(),
                str.substring(idxVol, idxHarga).trim(),
                str.substring(idxHarga, idxTotal).trim(),
                str.substring(idxTotal).trim()
            ];
        }
    }

    const knownHeaders = [
        'No', 'Deskripsi Pekerjaan', 'Deskripsi / Uraian Pekerjaan', 'Deskripsi / Uraian', 'Deskripsi', 'Uraian Pekerjaan',
        'Volume/Satuan', 'Volume / Satuan', 'Satuan', 'Volume',
        'Harga Satuan (Rp)', 'Harga Satuan', 'Harga',
        'Total Harga (Rp)', 'Total Harga', 'Total', 'Keterangan'
    ];

    let found = [];
    let pos = 0;
    while (pos < str.length) {
        let matched = false;
        for (let h of knownHeaders) {
            if (str.substring(pos).toLowerCase().startsWith(h.toLowerCase())) {
                found.push(str.substring(pos, pos + h.length));
                pos += h.length;
                matched = true;
                break;
            }
        }
        if (!matched) pos++;
    }
    if (found.length >= 2) return found;

    if (/\s{2,}/.test(str)) {
        return str.split(/\s{2,}/).map(s => s.trim());
    }

    return [str];
}

function splitLineIntoCells(line, targetCols = 5) {
    if (!line) return [];
    line = line.trim();
    if (!line) return [];

    if (line.includes('\t')) {
        return line.split('\t').map(c => c.trim());
    }

    if (line.includes('|')) {
        const parts = line.split('|').map(c => c.trim()).filter((c, i, a) => !(i === 0 && c === '') && !(i === a.length - 1 && c === ''));
        if (parts.length > 1) return parts;
    }

    if (line.includes('[') && line.includes(']')) {
        const firstBracket = line.indexOf('[');
        let prefix = firstBracket > 0 ? line.substring(0, firstBracket).trim() : '';
        const brackets = Array.from(line.matchAll(/\[(.*?)\]/g)).map(m => m[0].trim());

        if (brackets.length > 0) {
            let row = [];
            if (prefix.startsWith('-') && prefix.toLowerCase().includes('total')) {
                const label = prefix.substring(1).trim() || 'Total Keseluruhan';
                row = ['-', label];
                while (row.length < targetCols - brackets.length) row.push('');
                row.push(...brackets);
                return row;
            } else if (prefix.toLowerCase().startsWith('total')) {
                row = ['', prefix];
                while (row.length < targetCols - brackets.length) row.push('');
                row.push(...brackets);
                return row;
            } else if (prefix) {
                row = [prefix, ...brackets];
                return row;
            } else {
                return brackets;
            }
        }
    }

    if (/\s{2,}/.test(line)) {
        return line.split(/\s{2,}/).map(c => c.trim());
    }

    if (line.includes(',')) {
        return line.split(',').map(c => c.trim());
    }

    return [line];
}

function extractRowsFromHtml(htmlStr) {
    if (!htmlStr) return [];
    try {
        const cleanHtml = htmlStr.replace(/<!--[\s\S]*?-->/g, '');
        const parser = new DOMParser();
        const doc = parser.parseFromString(cleanHtml, 'text/html');
        const tbl = doc.querySelector('table');
        if (tbl) {
            const rows = [];
            const trs = Array.from(tbl.querySelectorAll('tr'));
            trs.forEach(tr => {
                const cells = Array.from(tr.querySelectorAll('th, td')).map(td => {
                    return (td.textContent || '').replace(/[\r\n\t]+/g, ' ').replace(/\s+/g, ' ').trim();
                });
                if (cells.length > 0 && cells.some(c => c !== '')) {
                    rows.push(cells);
                }
            });
            if (rows.length > 0) return rows;
        }
    } catch (e) {
        console.warn('HTML table parsing failed:', e);
    }
    return [];
}

function parsePastedTableData(textPlain, textHtml) {
    let rows = [];

    if (textHtml && (textHtml.toLowerCase().includes('<table') || textHtml.toLowerCase().includes('<tr'))) {
        rows = extractRowsFromHtml(textHtml);
    }

    if (rows.length === 0 && textPlain && textPlain.trim() !== '') {
        const preprocessed = preprocessTableString(textPlain);
        const rawLines = preprocessed.split(/\r?\n/).map(l => l.trim()).filter(l => l !== '');
        
        rawLines.forEach((line, idx) => {
            if (idx === 0) {
                const headerCells = parseHeaderLine(line);
                if (headerCells.length > 1) {
                    rows.push(headerCells);
                    return;
                }
            }
            const expectedCols = rows.length > 0 ? rows[0].length : 5;
            const cells = splitLineIntoCells(line, expectedCols);
            if (cells.length > 0) {
                rows.push(cells);
            }
        });
    }

    return rows;
}

function handleCellPaste(e, blockId, ri, ci) {
    const clipboardData = e.clipboardData || window.clipboardData;
    if (!clipboardData) return;

    const textPlain = clipboardData.getData('text/plain') || '';
    const textHtml  = clipboardData.getData('text/html') || '';

    if (textPlain.includes('\t') || textPlain.includes('\n') || textPlain.includes('[') || textHtml.toLowerCase().includes('<table')) {
        const parsedData = parsePastedTableData(textPlain, textHtml);
        if (parsedData.length > 0 && (parsedData.length > 1 || parsedData[0].length > 1)) {
            e.preventDefault();
            applyParsedDataToTableBlock(blockId, parsedData, ri, ci);
        }
    }
}

function applyParsedDataToTableBlock(blockId, data, startRi = 0, startCi = 0, forceHasHeader = null) {
    const b = blocks.find(x => x.id === blockId);
    if (!b || b.type !== 'table') return;
    if (!data || data.length === 0) return;

    let isHeaderPaste = false;
    if (forceHasHeader !== null) {
        isHeaderPaste = forceHasHeader;
    } else if (startRi === -1) {
        isHeaderPaste = true;
    } else if (startRi === 0 && startCi === 0 && data.length > 1) {
        const firstRowHeaderLike = data[0].every(val => isNaN(val.replace(/[\s\$\.Rp]/gi, '')) || val.trim() === '');
        if (firstRowHeaderLike) {
            isHeaderPaste = true;
        }
    }

    let dataRowIndex = 0;

    if (isHeaderPaste) {
        const headerRow = data[0];
        b.headers = [];
        headerRow.forEach((val) => {
            b.headers.push(val || `Kolom ${b.headers.length + 1}`);
        });
        dataRowIndex = 1;
    }

    const bodyStartRow = isHeaderPaste ? 0 : startRi;

    if (bodyStartRow === 0 && startCi === 0) {
        b.rows = [];
    }

    for (let r = dataRowIndex; r < data.length; r++) {
        const rowData = data[r];
        const targetRowIdx = bodyStartRow + (r - dataRowIndex);

        while (b.rows.length <= targetRowIdx) {
            b.rows.push(new Array(b.headers.length).fill(''));
        }

        rowData.forEach((val, cIdx) => {
            const targetColIdx = startCi + cIdx;
            while (b.headers.length <= targetColIdx) {
                b.headers.push(`Kolom ${b.headers.length + 1}`);
            }
            b.rows.forEach(row => {
                while (row.length < b.headers.length) {
                    row.push('');
                }
            });
            b.rows[targetRowIdx][targetColIdx] = val;
        });
    }

    b.rows.forEach(row => {
        while (row.length < b.headers.length) {
            row.push('');
        }
    });

    renderBlocksEditor();
    renderLivePreview();

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: `Tabel disalin (${b.rows.length} baris × ${b.headers.length} kolom)`,
            showConfirmButton: false,
            timer: 2500
        });
    }
}

function openPasteTableModal(blockId) {
    document.getElementById('pasteTargetBlockId').value = blockId;
    const pasteTextarea = document.getElementById('pasteTextarea');
    if (pasteTextarea) {
        pasteTextarea.value = '';
    }
    const prevCont = document.getElementById('pastePreviewContainer');
    if (prevCont) prevCont.classList.add('d-none');
    currentPasteData = [];

    const modalEl = document.getElementById('pasteTableModal');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
        setTimeout(() => pasteTextarea && pasteTextarea.focus(), 300);
    }
}

function handlePasteTextareaEvent(e) {
    const clipboardData = e ? (e.clipboardData || window.clipboardData) : null;
    const textPlain = clipboardData ? (clipboardData.getData('text/plain') || '') : (document.getElementById('pasteTextarea') ? document.getElementById('pasteTextarea').value : '');
    const textHtml  = clipboardData ? (clipboardData.getData('text/html') || '') : '';

    setTimeout(() => {
        const val = document.getElementById('pasteTextarea') ? document.getElementById('pasteTextarea').value : textPlain;
        currentPasteData = parsePastedTableData(val || textPlain, textHtml);
        renderPastePreview();
    }, 50);
}

function renderPastePreview() {
    const prevCont = document.getElementById('pastePreviewContainer');
    const tableEl  = document.getElementById('pastePreviewTable');
    const countEl  = document.getElementById('pasteDetectedCount');
    const hasHeaderEl = document.getElementById('pasteHasHeader');
    const hasHeader = hasHeaderEl ? hasHeaderEl.checked : true;

    if (!currentPasteData || currentPasteData.length === 0) {
        if (prevCont) prevCont.classList.add('d-none');
        return;
    }

    if (countEl) {
        countEl.textContent = `Terdeteksi ${currentPasteData.length} baris data x ${Math.max(...currentPasteData.map(r => r.length))} kolom`;
    }

    let html = '';
    let startRow = 0;
    if (hasHeader && currentPasteData.length > 0) {
        html += '<thead class="table-dark"><tr>';
        currentPasteData[0].forEach(h => {
            html += `<th>${escHtml(h)}</th>`;
        });
        html += '</tr></thead>';
        startRow = 1;
    }
    html += '<tbody>';
    for (let r = startRow; r < currentPasteData.length; r++) {
        html += '<tr>';
        currentPasteData[r].forEach(c => {
            html += `<td>${escHtml(c)}</td>`;
        });
        html += '</tr>';
    }
    html += '</tbody>';

    if (tableEl) tableEl.innerHTML = html;
    if (prevCont) prevCont.classList.remove('d-none');
}

function submitPastedTableModal() {
    const blockId = document.getElementById('pasteTargetBlockId').value;
    const hasHeaderEl = document.getElementById('pasteHasHeader');
    const hasHeader = hasHeaderEl ? hasHeaderEl.checked : true;

    if (!currentPasteData || currentPasteData.length === 0) {
        const pasteVal = document.getElementById('pasteTextarea') ? document.getElementById('pasteTextarea').value : '';
        if (pasteVal.trim() !== '') {
            currentPasteData = parsePastedTableData(pasteVal, null);
        }
    }

    if (!currentPasteData || currentPasteData.length === 0) {
        if (typeof Swal !== 'undefined') Swal.fire('Perhatian', 'Belum ada data tabel yang ditempelkan. Silakan tempelkan tabel (Ctrl+V) ke dalam kotak teks.', 'warning');
        return;
    }

    applyParsedDataToTableBlock(blockId, currentPasteData, 0, 0, hasHeader);

    const modalEl = document.getElementById('pasteTableModal');
    if (modalEl) {
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }
}
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
    @page { size: A4 portrait; margin: 0; }
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
    #mainEditorView,
    header,
    footer,
    .no-print,
    .modal,
    .modal-backdrop,
    .sidebar-overlay,
    nav,
    #ci-debug-bar,
    .ci-debug-bar,
    .debug-bar {
        display: none !important;
        visibility: hidden !important;
        width: 0 !important;
        height: 0 !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }
    #dedicatedPrintView {
        display: block !important;
        position: static !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #ffffff !important;
        overflow: visible !important;
        z-index: 1 !important;
    }
    #dedicatedPrintPaper, #previewPaper {
        position: relative !important;
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
        width: 210mm !important;
        max-width: 210mm !important;
        min-height: 296mm !important;
        max-height: 296mm !important;
        height: 296mm !important;
        margin: 0 auto !important;
        padding: 35px 45px 75px 45px !important;
        box-sizing: border-box !important;
        background: #ffffff !important;
        box-shadow: none !important;
        border: none !important;
        border-radius: 0 !important;
        page-break-before: always !important;
        break-before: page !important;
        page-break-after: always !important;
        break-after: page !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
        overflow: hidden !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .doc-page-sheet:first-child {
        page-break-before: auto !important;
        break-before: auto !important;
    }
    .accent-top-left {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 160px !important;
        height: 160px !important;
        z-index: 1 !important;
    }
    .accent-bottom-right {
        position: absolute !important;
        bottom: 0 !important;
        right: 0 !important;
        width: 160px !important;
        height: 160px !important;
        z-index: 1 !important;
    }
    .custom-doc-table, tr, td, th, table {
        break-inside: avoid !important;
        page-break-inside: avoid !important;
    }
    .mini-page-header-repeat {
        display: flex !important;
        visibility: visible !important;
    }
}
</style>

<?= view('direktur/templates/footer', $templateData) ?>

