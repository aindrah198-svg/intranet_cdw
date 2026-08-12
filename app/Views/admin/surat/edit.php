<?= view('admin/templates/header') ?>
<?= view('admin/templates/sidebar') ?>
<?= view('admin/templates/navbar') ?>

<style>
    .employee-card-modern {
        background: rgba(255,255,255,0.95); backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255,255,255,0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(30,60,114,0.06), 0 2px 6px rgba(0,0,0,0.02) !important;
    }
    .form-section-title { font-size:0.95rem;font-weight:700;color:#1e3c72;display:flex;align-items:center;gap:8px;padding-bottom:8px;border-bottom:2px solid #f1f5f9;margin-bottom:20px; }
    .form-control-custom, .form-select-custom { border-radius:10px;border:1px solid #e2e8f0;padding:10px 14px;font-size:0.9rem;transition:all 0.2s ease; }
    .form-control-custom:focus, .form-select-custom:focus { border-color:#1e3c72;box-shadow:0 0 0 3px rgba(30,60,114,0.12); }
    .letter-paper-preview { background:transparent !important;border:none !important;box-shadow:none !important;padding:0 !important;position:relative;width:100%;max-width:794px;margin:0 auto;font-family:'Inter',Arial,sans-serif;color:#1e293b;box-sizing:border-box;transition:all 0.3s ease; }
    .paper-size-A4 .doc-page-sheet { min-height:1123px;max-width:794px; }
    .paper-size-A3 .doc-page-sheet { min-height:1587px;max-width:1123px; }
    .paper-size-Letter .doc-page-sheet { min-height:1056px;max-width:816px; }
    .paper-size-Legal .doc-page-sheet { min-height:1344px;max-width:816px; }
    .paper-size-Folio .doc-page-sheet { min-height:1247px;max-width:813px; }
    .blue-header-banner { background:#1e40af;color:#fff;padding:12px 18px;font-weight:700;letter-spacing:1px;text-transform:uppercase;border-radius:4px;margin-bottom:20px; }
    .custom-doc-table { width:100%;border-collapse:collapse;margin:18px 0;font-size:0.9rem; }
    .custom-doc-table th, .custom-doc-table td { padding:10px 14px;border:1px solid #cbd5e1; }
    .table-style-blue_header th { background:#1e40af;color:#fff;font-weight:700;text-transform:uppercase;border-color:#1e3a8a; }
    .table-style-gold_header th { background:#d97706;color:#fff;font-weight:700;text-transform:uppercase;border-color:#b45309; }
    .table-style-striped tbody tr:nth-child(even) { background-color:#f8fafc; }
    .table-style-standard th { background-color:#f1f5f9;font-weight:700; }
    /* Block Editor */
    .content-block-card { border:1.5px solid #e2e8f0;border-radius:10px;margin-bottom:10px;overflow:hidden;transition:box-shadow 0.2s ease; }
    .content-block-card:hover { box-shadow:0 4px 14px rgba(30,60,114,0.09); }
    .block-header-bar { background:#f8fafc;border-bottom:1px solid #e2e8f0;padding:8px 12px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:6px; }
    .table-blk-header { background:#eff6ff;border-bottom-color:#bfdbfe; }
    .block-type-badge { font-size:0.72rem;font-weight:700;padding:3px 10px;border-radius:20px;letter-spacing:0.4px;text-transform:uppercase;display:inline-flex;align-items:center;gap:4px; }
    .text-badge  { background:rgba(100,116,139,0.12);color:#475569;border:1px solid rgba(100,116,139,0.25); }
    .table-badge { background:rgba(30,64,175,0.1);color:#1d4ed8;border:1px solid rgba(30,64,175,0.2); }
    .block-body-area { padding:12px; }
    .table-block-body { padding:8px;background:#fafafa; }
    .block-textarea { border-radius:6px;font-size:0.88rem;line-height:1.75;resize:vertical;min-height:90px; }
    .btn-blk-action { border:1px solid #e2e8f0;background:#fff;color:#475569;border-radius:6px;padding:4px 8px;font-size:0.75rem;cursor:pointer;transition:all 0.15s ease;display:inline-flex;align-items:center;gap:4px;font-weight:600; }
    .btn-blk-action:hover:not(:disabled) { background:#1e3c72;color:#fff;border-color:#1e3c72; }
    .btn-blk-action:disabled { opacity:0.35;cursor:not-allowed; }
    .btn-blk-danger:hover:not(:disabled)  { background:#dc3545 !important;border-color:#dc3545 !important; }
    .btn-blk-success:hover:not(:disabled) { background:#198754 !important;border-color:#198754 !important; }
    .btn-blk-warning:hover:not(:disabled) { background:#d97706 !important;border-color:#d97706 !important; }
    .block-empty-state { text-align:center;padding:28px 16px;color:#94a3b8;border:2px dashed #cbd5e1;border-radius:10px;background:#f8fafc; }
    .page-break-indicator { margin:20px 0 0 0;border-top:2.5px dashed #6366f1;position:relative;display:block;clear:both; }
    .page-break-indicator::before { content:attr(data-label);display:block;text-align:center;font-size:0.7rem;font-weight:700;color:#6366f1;letter-spacing:1.5px;background:#eef2ff;padding:3px 14px;border-radius:20px;width:fit-content;margin:-13px auto 12px auto;border:1.5px solid #c7d2fe; }
    .mini-page-header-repeat { padding:8px 0 6px 0;border-bottom:1.5px solid #334155;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between; }
    .auto-break-badge { background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;font-size:0.65rem;font-weight:700;padding:2px 8px;border-radius:20px;letter-spacing:0.5px;display:inline-flex;align-items:center;gap:4px; }
</style>

<div id="mainEditorView">
<div class="container-fluid py-3 py-md-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light flex-wrap gap-2">
        <div class="d-flex align-items-center">
            <div class="text-white rounded-3 p-2 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width:46px;height:46px;background:linear-gradient(135deg,#f59e0b 0%,#d97706 100%);">
                <i class="fas fa-edit fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark">Edit Dokumen Surat</h4>
                <small class="text-muted d-none d-sm-inline">Ubah naskah, perihal, karyawan, atau kustomisasi desain template surat.</small>
            </div>
        </div>
        <a href="<?= base_url('admin/surat/detail/' . $surat['id']) ?>" class="btn btn-outline-secondary rounded-pill px-3 py-2 shadow-sm d-inline-flex align-items-center text-sm fw-semibold">
            <i class="fas fa-arrow-left me-1"></i> <span class="d-none d-md-inline">Kembali ke Detail</span><span class="d-inline d-md-none">Kembali</span>
        </a>
    </div>

    <div class="row g-4">
        <!-- COL 1: Form -->
        <div class="col-12 col-xl-5">
            <div class="card employee-card-modern p-4 mb-4">

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('admin/surat/update') ?>" method="post" id="suratForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= $surat['id'] ?>">
                    <input type="hidden" name="isi_surat" id="isiSurat">
                    <input type="hidden" name="html_full" id="htmlFull">
                    <input type="hidden" name="total_halaman" id="totalHalamanInput" value="1">

                    <!-- Layout & Template -->
                    <div class="form-section-title">
                        <i class="fas fa-palette text-primary"></i> Desain Layout, Ukuran Kertas & Template
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label text-sm fw-semibold text-dark">Pilih Preset Template</label>
                            <select name="template_layout" id="templateLayout" class="form-select form-select-custom fw-bold text-primary" onchange="applyPresetTemplate()">
                                <option value="standard"      <?= ($surat['template_layout'] ?? '') == 'standard'      ? 'selected' : '' ?>>📄 Preset 1: Standard Kop Atas (Line Black)</option>
                                <option value="accent_yellow" <?= ($surat['template_layout'] ?? '') == 'accent_yellow' ? 'selected' : '' ?>>✨ Preset 2: Aksen Kuning Modern (Footer Alamat)</option>
                                <option value="blue_header"   <?= ($surat['template_layout'] ?? '') == 'blue_header'   ? 'selected' : '' ?>>🔷 Preset 3: Bilah Biru Formal</option>
                                <option value="compact_left"  <?= ($surat['template_layout'] ?? '') == 'compact_left'  ? 'selected' : '' ?>>📌 Preset 4: Kop Ringkas Kiri</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label text-sm fw-semibold text-dark">Ukuran Kertas</label>
                            <select name="paper_size" id="paperSize" class="form-select form-select-custom fw-semibold text-dark" onchange="renderLivePreview()">
                                <option value="A4" selected>A4 (210 x 297 mm)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-xs fw-semibold text-muted">Posisi Logo</label>
                            <select name="logo_position" id="logoPosition" class="form-select form-select-custom" onchange="renderLivePreview()">
                                <option value="top_right" <?= ($surat['logo_position'] ?? '') == 'top_right' ? 'selected' : '' ?>>Kanan Atas</option>
                                <option value="top_left"  <?= ($surat['logo_position'] ?? '') == 'top_left'  ? 'selected' : '' ?>>Kiri Atas</option>
                                <option value="center"    <?= ($surat['logo_position'] ?? '') == 'center'    ? 'selected' : '' ?>>Tengah Atas</option>
                                <option value="none"      <?= ($surat['logo_position'] ?? '') == 'none'      ? 'selected' : '' ?>>Sembunyikan Logo</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-xs fw-semibold text-muted">Posisi Alamat Perusahaan</label>
                            <select name="address_position" id="addressPosition" class="form-select form-select-custom" onchange="renderLivePreview()">
                                <option value="top_left"   <?= ($surat['address_position'] ?? '') == 'top_left'   ? 'selected' : '' ?>>Kop Atas Kiri</option>
                                <option value="top_center" <?= ($surat['address_position'] ?? '') == 'top_center' ? 'selected' : '' ?>>Kop Atas Tengah</option>
                                <option value="footer"     <?= ($surat['address_position'] ?? '') == 'footer'     ? 'selected' : '' ?>>Catatan Kaki (Footer)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-xs fw-semibold text-muted">Gaya Aksen Dekorasi</label>
                            <select name="accent_style" id="accentStyle" class="form-select form-select-custom" onchange="renderLivePreview()">
                                <option value="line"          <?= ($surat['accent_style'] ?? '') == 'line'          ? 'selected' : '' ?>>Garis Hitam Standard</option>
                                <option value="yellow_corner" <?= ($surat['accent_style'] ?? '') == 'yellow_corner' ? 'selected' : '' ?>>Aksen Kuning Corner</option>
                                <option value="blue_bar"      <?= ($surat['accent_style'] ?? '') == 'blue_bar'      ? 'selected' : '' ?>>Bilah Biru Formal (Banner Title)</option>
                                <option value="none"          <?= ($surat['accent_style'] ?? '') == 'none'          ? 'selected' : '' ?>>Polos (Tanpa Aksen)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-xs fw-semibold text-muted">Layout Tanda Tangan</label>
                            <select name="signature_layout" id="signatureLayout" class="form-select form-select-custom" onchange="toggleSignatureFields(); renderLivePreview();">
                                <option value="1_pihak" <?= ($surat['signature_layout'] ?? '1_pihak') == '1_pihak' ? 'selected' : '' ?>>1 Pihak / Standard (Kanan: PT CDW)</option>
                                <option value="2_pihak" <?= ($surat['signature_layout'] ?? '') == '2_pihak' ? 'selected' : '' ?>>2 Pihak Berdampingan (Pihak I & Pihak II)</option>
                                <option value="3_pihak" <?= ($surat['signature_layout'] ?? '') == '3_pihak' ? 'selected' : '' ?>>3 Pihak (Pihak I, Pihak II, Pihak III)</option>
                            </select>
                        </div>
                    </div>

                    <?php
                        $sigData = json_decode($surat['signature_data'] ?? '{}', true) ?: [];
                        $p1Title = $sigData['p1_title'] ?? 'Hormat kami,';
                        $p1Nama  = $sigData['p1_nama']  ?? 'PT. CIPTA DUTA WACANA';
                        $p1Jab   = $sigData['p1_jabatan'] ?? 'Direktur Utama';

                        $p2Title = $sigData['p2_title'] ?? 'Pihak Kedua,';
                        $p2Nama  = $sigData['p2_nama']  ?? 'PT. CIPTA DUTA WACANA';
                        $p2Jab   = $sigData['p2_jabatan'] ?? 'Direktur Utama';

                        $p3Title = $sigData['p3_title'] ?? 'Pihak Ketiga,';
                        $p3Nama  = $sigData['p3_nama']  ?? 'PT. CIPTA DUTA WACANA';
                        $p3Jab   = $sigData['p3_jabatan'] ?? 'Direktur Utama';
                        $sigLayoutVal = $surat['signature_layout'] ?? '1_pihak';
                    ?>

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
                                <input type="text" name="pihak_1_title" id="pihak1Title" class="form-control form-control-sm" placeholder="Judul (misal: Pihak Pertama,)" value="<?= esc($p1Title) ?>" oninput="renderLivePreview()">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="pihak_1_nama" id="pihak1Nama" class="form-control form-control-sm" placeholder="Nama / Perusahaan" value="<?= esc($p1Nama) ?>" oninput="renderLivePreview()">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="pihak_1_jabatan" id="pihak1Jabatan" class="form-control form-control-sm" placeholder="Jabatan" value="<?= esc($p1Jab) ?>" oninput="renderLivePreview()">
                            </div>
                        </div>

                        <!-- Pihak 2 -->
                        <div id="p2Fields" class="row g-2 mb-2 <?= in_array($sigLayoutVal, ['2_pihak', '3_pihak']) ? '' : 'd-none' ?>">
                            <div class="col-12"><small class="fw-bold text-primary">Pihak Kedua</small></div>
                            <div class="col-md-4">
                                <input type="text" name="pihak_2_title" id="pihak2Title" class="form-control form-control-sm" placeholder="Judul (misal: Pihak Kedua,)" value="<?= esc($p2Title) ?>" oninput="renderLivePreview()">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="pihak_2_nama" id="pihak2Nama" class="form-control form-control-sm" placeholder="Nama / Perusahaan II" value="<?= esc($p2Nama) ?>" oninput="renderLivePreview()">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="pihak_2_jabatan" id="pihak2Jabatan" class="form-control form-control-sm" placeholder="Jabatan II" value="<?= esc($p2Jab) ?>" oninput="renderLivePreview()">
                            </div>
                        </div>

                        <!-- Pihak 3 -->
                        <div id="p3Fields" class="row g-2 <?= $sigLayoutVal === '3_pihak' ? '' : 'd-none' ?>">
                            <div class="col-12"><small class="fw-bold text-primary">Pihak Ketiga</small></div>
                            <div class="col-md-4">
                                <input type="text" name="pihak_3_title" id="pihak3Title" class="form-control form-control-sm" placeholder="Judul (misal: Pihak Ketiga,)" value="<?= esc($p3Title) ?>" oninput="renderLivePreview()">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="pihak_3_nama" id="pihak3Nama" class="form-control form-control-sm" placeholder="Nama / Perusahaan III" value="<?= esc($p3Nama) ?>" oninput="renderLivePreview()">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="pihak_3_jabatan" id="pihak3Jabatan" class="form-control form-control-sm" placeholder="Jabatan III" value="<?= esc($p3Jab) ?>" oninput="renderLivePreview()">
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Dokumen -->
                    <div class="form-section-title mt-4">
                        <i class="fas fa-user-tag text-primary"></i> Informasi Dokumen & Penerima
                    </div>
                    <?php $isCustomJenis = !in_array($surat['jenis_surat'], $jenisList); ?>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-sm fw-semibold text-dark">Jenis Surat <span class="text-danger">*</span></label>
                            <select name="jenis_surat" class="form-select form-select-custom" required id="jenisSurat" onchange="updateTemplateText(); renderLivePreview();">
                                <?php foreach ($jenisList as $j): ?>
                                    <option value="<?= esc($j) ?>" <?= ($surat['jenis_surat'] == $j || ($isCustomJenis && $j == 'Lainnya')) ? 'selected' : '' ?>><?= esc($j) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div id="jenisSuratLainnyaWrapper" class="mt-2 <?= $isCustomJenis ? '' : 'd-none' ?>">
                                <label class="form-label text-xs fw-bold text-primary">Ketikkan Jenis Surat Khusus:</label>
                                <input type="text" id="jenisSuratLainnya" name="jenis_surat_custom" class="form-control form-control-custom text-uppercase fw-bold border-primary" value="<?= $isCustomJenis ? esc($surat['jenis_surat']) : '' ?>" oninput="renderLivePreview()">
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
                                        <?= ($surat['karyawan_id'] ?? '') == $k['id'] ? 'selected' : '' ?>>
                                        <?= esc($k['nama_lengkap']) ?> - <?= esc($k['nik']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div id="infoKaryawan" class="alert alert-info border-0 rounded-3 shadow-sm d-none mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-id-card fs-5 text-primary"></i>
                            <span id="infoKaryawanText" class="fw-semibold text-dark" style="font-size:0.88rem;"></span>
                        </div>
                    </div>

                    <!-- Tanggal & Perihal -->
                    <div class="form-section-title mt-4">
                        <i class="fas fa-heading text-primary"></i> Detail Tanggal & Perihal
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label text-sm fw-semibold text-dark">Tanggal Surat <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_surat" id="tanggalSurat" class="form-control form-control-custom" value="<?= esc($surat['tanggal_surat']) ?>" required onchange="renderLivePreview()">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label text-sm fw-semibold text-dark">Perihal Surat <span class="text-danger">*</span></label>
                            <input type="text" name="perihal" id="perihalInput" class="form-control form-control-custom" value="<?= esc($surat['perihal']) ?>" required oninput="renderLivePreview()">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-sm fw-semibold text-dark">Status</label>
                            <select name="status" id="statusSurat" class="form-select form-select-custom">
                                <option value="draft"       <?= $surat['status'] == 'draft'       ? 'selected' : '' ?>>Draft</option>
                                <option value="diterbitkan" <?= $surat['status'] == 'diterbitkan' ? 'selected' : '' ?>>Diterbitkan</option>
                                <option value="dibatalkan"  <?= $surat['status'] == 'dibatalkan'  ? 'selected' : '' ?>>Dibatalkan</option>
                            </select>
                        </div>
                    </div>

                    <!-- Block Editor -->
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
                    <div class="mb-3" id="contentBlocksEditor"></div>

                    <div class="mb-4">
                        <label class="form-label text-sm fw-semibold text-dark">Catatan Internal <span class="text-xs text-muted">(Opsional)</span></label>
                        <textarea name="catatan" id="catatanInput" class="form-control form-control-custom" rows="2" oninput="renderLivePreview()"><?= esc($surat['catatan'] ?? '') ?></textarea>
                    </div>

                    <div class="pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="<?= base_url('admin/surat/detail/' . $surat['id']) ?>" class="btn btn-light rounded-pill px-4 fw-semibold border">Batal</a>
                        <button type="submit" class="btn btn-warning rounded-pill px-4 py-2 fw-semibold shadow-sm">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan Surat
                        </button>
                    </div>

                </form>
            </div>
        </div>

        <!-- COL 2: Live Preview -->
        <div class="col-12 col-xl-7">
            <div class="sticky-top" style="top:20px;z-index:10;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge px-3 py-2 rounded-pill fw-bold shadow-sm" style="background:linear-gradient(135deg,#1e3c72 0%,#2a5298 100%);">
                        <i class="fas fa-eye me-1"></i> Pratinjau Kertas Realistis
                    </span>
                    <div class="d-flex gap-2 align-items-center">
                        <small id="pageCountBadge" class="text-muted fw-semibold"></small>
                    </div>
                </div>
                <div class="letter-paper-preview paper-size-A4" id="previewPaper"></div>
            </div>
        </div>

    </div>
</div>
</div>

<!-- Dedicated Print View -->
<div id="dedicatedPrintView" class="d-none" style="display:none !important;background:#0f172a;min-height:100vh;padding-bottom:60px;">
    <div class="no-print sticky-top bg-dark border-bottom border-secondary px-4 py-3 shadow-lg d-flex justify-content-between align-items-center flex-wrap gap-2" style="z-index:1050;">
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3 fw-semibold" onclick="closeDedicatedPrintView()">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Form Editor
            </button>
            <span class="badge bg-primary rounded-pill px-3 py-2 d-none d-md-inline" style="font-size:0.75rem;">Mode Pratinjau Cetak</span>
        </div>
        <div class="text-white text-center d-none d-lg-block">
            <h6 class="mb-0 fw-bold"><i class="fas fa-file-alt text-warning me-2"></i>Pratinjau Cetak Dokumen Surat</h6>
            <small style="font-size:0.75rem;color:#94a3b8;">Format ini siap dicetak atau disimpan sebagai PDF</small>
        </div>
        <div>
            <button type="button" class="btn btn-warning btn-sm rounded-pill px-4 py-2 fw-bold shadow text-dark" onclick="triggerActualPrint()">
                <i class="fas fa-print me-1"></i> Cetak Sekarang / Simpan PDF
            </button>
        </div>
    </div>
    <div class="container py-4">
        <div class="d-flex justify-content-center">
            <div id="dedicatedPrintPaper" class="letter-paper-preview paper-size-A4 shadow-lg"></div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Cetak -->
<div class="modal fade" id="confirmPrintModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header text-white border-0 rounded-top-4 py-3" style="background:linear-gradient(135deg,#1e3c72 0%,#2a5298 100%);">
                <h6 class="modal-title fw-bold text-white"><i class="fas fa-file-invoice me-2"></i>Konfirmasi Pratinjau & Cetak</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning rounded-circle p-3" style="width:70px;height:70px;">
                        <i class="fas fa-question-circle fa-2x"></i>
                    </div>
                </div>
                <h5 class="fw-bold text-dark mb-2">Apakah Dokumen Sudah Benar?</h5>
                <p class="text-muted mb-4" style="font-size:0.9rem;">Periksa kembali data surat. Jika sudah benar, lanjutkan ke halaman Pratinjau Cetak.</p>
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

                <div class="card bg-light border-0 rounded-3 p-3 text-start mb-4" style="font-size:0.8rem;">
                    <div class="row g-2">
                        <div class="col-6"><strong>Jenis Surat:</strong><br><span id="printConfirmJenis" class="text-primary fw-bold">-</span></div>
                        <div class="col-6"><strong>Penerima:</strong><br><span id="printConfirmKaryawan" class="fw-bold">-</span></div>
                        <div class="col-6"><strong>Tanggal:</strong><br><span id="printConfirmTanggal">-</span></div>
                        <div class="col-6"><strong>Format Kertas:</strong><br><span id="printConfirmPaper" class="text-success fw-bold">-</span></div>
                    </div>
                </div>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold border" data-bs-dismiss="modal">
                        <i class="fas fa-edit me-1"></i> Periksa / Edit Lagi
                    </button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" style="background:linear-gradient(135deg,#1e3c72 0%,#2a5298 100%);border:none;" onclick="proceedToDedicatedPrintView()">
                        <i class="fas fa-arrow-right me-1"></i> Ya, Lanjut Cetak ➔
                    </button>
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

<script>
const companyLogoBase64 = '<?= $logoBase64 ?>';
const nomorSuratText    = '<?= esc($surat['nomor_surat']) ?>';
const existingIsiSurat  = <?= json_encode($surat['isi_surat'] ?? '') ?>;

let blocks = [], blockCounter = 0;
const PAPER_BODY_PX = { A4:{page1:670,cont:870}, A3:{page1:1250,cont:1450}, Letter:{page1:630,cont:830}, Legal:{page1:900,cont:1100}, Folio:{page1:800,cont:1000} };

function genBlkId() { return 'blk_' + (++blockCounter); }
function escHtml(s) { if(s===undefined||s===null) return ''; return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }

function addBlock(type) {
    const id=genBlkId();
    if(type==='text') blocks.push({id,type:'text',content:''});
    else if(type==='table') blocks.push({id,type:'table',style:'blue_header',headers:['No','Deskripsi / Uraian Pekerjaan','Volume / Satuan','Keterangan'],rows:[['1','Pengadaan & Pemasangan Material','1 Paket','Selesai 100%'],['2','Pemeriksaan & Pengujian Lapangan','1 Laporan','Baik & Layak']]});
    renderBlocksEditor(); renderLivePreview();
}
function removeBlock(id) { if(blocks.length<=1&&!confirm('Hapus blok ini?')) return; blocks=blocks.filter(b=>b.id!==id); renderBlocksEditor(); renderLivePreview(); }
function moveBlock(id,dir) { const idx=blocks.findIndex(b=>b.id===id); if(idx<0) return; const ni=idx+dir; if(ni<0||ni>=blocks.length) return; [blocks[idx],blocks[ni]]=[blocks[ni],blocks[idx]]; renderBlocksEditor(); renderLivePreview(); }
function updateBlockText(id,value) { const b=blocks.find(b=>b.id===id); if(b) b.content=value; renderLivePreview(); }
function updateTableHeader(id,ci,value) { const b=blocks.find(b=>b.id===id); if(b){b.headers[ci]=value; renderLivePreview();} }
function updateTableCell(id,ri,ci,value) { const b=blocks.find(b=>b.id===id); if(b){b.rows[ri][ci]=value; renderLivePreview();} }
function addTableRow(id) { const b=blocks.find(b=>b.id===id); if(!b) return; const row=new Array(b.headers.length).fill(''); row[0]=String(b.rows.length+1); b.rows.push(row); renderBlockEditor(id); renderLivePreview(); }
function removeTableRow(id) { const b=blocks.find(b=>b.id===id); if(b&&b.rows.length>1){b.rows.pop(); renderBlockEditor(id); renderLivePreview();} }
function addTableCol(id) { const b=blocks.find(b=>b.id===id); if(b&&b.headers.length<8){b.headers.push('Kolom '+(b.headers.length+1)); b.rows.forEach(r=>r.push('')); renderBlockEditor(id); renderLivePreview();} }
function removeTableCol(id) { const b=blocks.find(b=>b.id===id); if(b&&b.headers.length>1){b.headers.pop(); b.rows.forEach(r=>r.pop()); renderBlockEditor(id); renderLivePreview();} }
function updateTableStyle(id,style) { const b=blocks.find(b=>b.id===id); if(b){b.style=style; renderLivePreview();} }

function renderBlockEditor(id) {
    const container=document.getElementById('contentBlocksEditor'); if(!container) return;
    const el=container.querySelector(`[data-block-id="${id}"]`);
    const b=blocks.find(b=>b.id===id); if(!el||!b){renderBlocksEditor();return;}
    const idx=blocks.findIndex(x=>x.id===id);
    const tmp=document.createElement('div'); tmp.innerHTML=buildBlockHtml(b,idx); el.replaceWith(tmp.firstElementChild);
}

function buildBlockHtml(b, idx) {
    const first=idx===0, last=idx===blocks.length-1;
    if(b.type==='text') return `<div class="content-block-card" data-block-id="${b.id}">
        <div class="block-header-bar"><span class="block-type-badge text-badge"><i class="fas fa-paragraph"></i> Paragraf</span>
        <div class="d-flex gap-1 flex-wrap">
            <button type="button" class="btn-blk-action" onclick="moveBlock('${b.id}',-1)" ${first?'disabled':''} title="Naik"><i class="fas fa-arrow-up"></i></button>
            <button type="button" class="btn-blk-action" onclick="moveBlock('${b.id}',1)" ${last?'disabled':''} title="Turun"><i class="fas fa-arrow-down"></i></button>
            <button type="button" class="btn-blk-action btn-blk-danger" onclick="removeBlock('${b.id}')"><i class="fas fa-trash-alt"></i></button>
        </div></div>
        <div class="block-body-area"><textarea class="form-control block-textarea" rows="4" oninput="updateBlockText('${b.id}',this.value)">${escHtml(b.content)}</textarea></div>
    </div>`;
    if(b.type==='table') {
        const styleOpts=[['blue_header','🔷 Biru Formal'],['gold_header','🟡 Emas Premium'],['standard','⬜ Standard'],['striped','🟦 Striped']].map(([v,l])=>`<option value="${v}" ${b.style===v?'selected':''}>${l}</option>`).join('');
        const headTh=b.headers.map((h,ci)=>`<th style="min-width:80px"><input type="text" class="form-control form-control-sm fw-bold px-1" value="${escHtml(h)}" oninput="updateTableHeader('${b.id}',${ci},this.value)" onpaste="handleCellPaste(event,'${b.id}',-1,${ci})"></th>`).join('');
        const bodyTr=b.rows.map((row,ri)=>`<tr>${row.map((cell,ci)=>`<td><input type="text" class="form-control form-control-sm px-1" value="${escHtml(cell)}" oninput="updateTableCell('${b.id}',${ri},${ci},this.value)" onpaste="handleCellPaste(event,'${b.id}',${ri},${ci})"></td>`).join('')}</tr>`).join('');
        return `<div class="content-block-card" data-block-id="${b.id}">
        <div class="block-header-bar table-blk-header"><span class="block-type-badge table-badge"><i class="fas fa-table"></i> Tabel Data</span>
        <div class="d-flex gap-1 flex-wrap align-items-center">
            <select class="form-select form-select-sm rounded-2" style="width:auto;font-size:0.75rem;" onchange="updateTableStyle('${b.id}',this.value)">${styleOpts}</select>
            <button type="button" class="btn-blk-action btn-blk-info text-nowrap" onclick="openPasteTableModal('${b.id}')" title="Paste / Impor Tabel dari Word / Excel"><i class="fas fa-paste me-1"></i> Paste Tabel</button>
            <button type="button" class="btn-blk-action btn-blk-success" onclick="addTableRow('${b.id}')"><i class="fas fa-plus"></i> Baris</button>
            <button type="button" class="btn-blk-action btn-blk-warning" onclick="removeTableRow('${b.id}')"><i class="fas fa-minus"></i> Baris</button>
            <button type="button" class="btn-blk-action btn-blk-success" onclick="addTableCol('${b.id}')"><i class="fas fa-plus"></i> Kolom</button>
            <button type="button" class="btn-blk-action btn-blk-warning" onclick="removeTableCol('${b.id}')"><i class="fas fa-minus"></i> Kolom</button>
            <button type="button" class="btn-blk-action" onclick="moveBlock('${b.id}',-1)" ${first?'disabled':''}><i class="fas fa-arrow-up"></i></button>
            <button type="button" class="btn-blk-action" onclick="moveBlock('${b.id}',1)" ${last?'disabled':''}><i class="fas fa-arrow-down"></i></button>
            <button type="button" class="btn-blk-action btn-blk-danger" onclick="removeBlock('${b.id}')"><i class="fas fa-trash-alt"></i></button>
        </div></div>
        <div class="block-body-area table-block-body"><div class="table-responsive" style="max-height:260px;overflow-y:auto;">
            <table class="table table-bordered table-sm mb-0" style="font-size:0.78rem;min-width:320px;">
                <thead class="table-dark"><tr>${headTh}</tr></thead><tbody>${bodyTr}</tbody>
            </table></div></div></div>`;
    }
    return '';
}

function renderBlocksEditor() {
    const container=document.getElementById('contentBlocksEditor'); if(!container) return;
    if(blocks.length===0){ container.innerHTML=`<div class="block-empty-state"><i class="fas fa-layer-group fa-2x mb-2" style="color:#c7d2fe"></i><p class="mb-1 fw-bold" style="color:#1e3c72">Belum ada konten</p><p class="mb-0" style="font-size:0.88rem;">Klik <strong>+ Paragraf</strong> atau <strong>+ Tabel</strong></p></div>`; return; }
    container.innerHTML=blocks.map((b,i)=>buildBlockHtml(b,i)).join('');
}

function assembleIsiSurat() {
    return blocks.map(b=>{
        if(b.type==='text') return b.content||'';
        if(b.type==='table'){
            let html=`\n<table class="custom-doc-table table-style-${b.style}">\n<thead>\n<tr>\n`;
            b.headers.forEach(h=>{html+=`  <th>${h}</th>\n`;});
            html+=`</tr>\n</thead>\n<tbody>\n`;
            b.rows.forEach(row=>{html+=`<tr>\n`;row.forEach(cell=>{html+=`  <td>${cell}</td>\n`;});html+=`</tr>\n`;});
            html+=`</tbody>\n</table>\n`; return html;
        } return '';
    }).join('\n');
}

function parseHtmlToBlocks(html) {
    if(!html||!html.trim()) return [];
    const result=[];
    const parts=html.split(/(<table[\s\S]*?<\/table>)/gi);
    parts.forEach(part=>{
        const trimmed=part.trim(); if(!trimmed) return;
        if(/^<table/i.test(trimmed)){
            try{
                const doc=new DOMParser().parseFromString(trimmed,'text/html');
                const tbl=doc.querySelector('table');
                if(tbl){
                    const styleM=(tbl.className||'').match(/table-style-(\w+)/);
                    const style=styleM?styleM[1]:'blue_header';
                    const headers=Array.from(tbl.querySelectorAll('thead th')).map(x=>x.textContent.trim());
                    const rows=Array.from(tbl.querySelectorAll('tbody tr')).map(tr=>Array.from(tr.querySelectorAll('td')).map(td=>td.textContent.trim()));
                    if(headers.length) result.push({id:genBlkId(),type:'table',style,headers,rows:rows.length?rows:[new Array(headers.length).fill('')]});
                }
            }catch(e){}
        } else {
            const txt=trimmed.replace(/<div[^>]*doc-page-break[^>]*>.*?<\/div>/gi,'').replace(/<br\s*\/?>/gi,'\n').replace(/<[^>]+>/g,'').trim();
            if(txt) result.push({id:genBlkId(),type:'text',content:txt});
        }
    });
    return result;
}

function applyPresetTemplate() {
    const layout=document.getElementById('templateLayout').value;
    const logoPos=document.getElementById('logoPosition');
    const addrPos=document.getElementById('addressPosition');
    const accent=document.getElementById('accentStyle');
    if(layout==='standard'){logoPos.value='top_right';addrPos.value='top_left';accent.value='line';}
    else if(layout==='accent_yellow'){logoPos.value='top_right';addrPos.value='footer';accent.value='yellow_corner';}
    else if(layout==='blue_header'){logoPos.value='top_right';addrPos.value='top_left';accent.value='blue_bar';}
    else if(layout==='compact_left'){logoPos.value='top_left';addrPos.value='top_left';accent.value='line';}
    renderLivePreview();
}

function updateTemplateText() {
    const jenisSel=document.getElementById('jenisSurat').value;
    const cw=document.getElementById('jenisSuratLainnyaWrapper');
    if(jenisSel==='Lainnya'){cw&&cw.classList.remove('d-none');} else {cw&&cw.classList.add('d-none');}
    updateKaryawanText();
}

function updateKaryawanText() {
    const sel=document.getElementById('karyawanSelect');
    const opt=sel?sel.options[sel.selectedIndex]:null;
    const infoEl=document.getElementById('infoKaryawan');
    const infoTxt=document.getElementById('infoKaryawanText');
    if(opt&&opt.value){
        const divisi=opt.dataset.divisi||'-', jabatan=opt.dataset.jabatan||'-', nama=opt.dataset.nama||opt.text.split(' - ')[0];
        if(infoTxt) infoTxt.textContent=`${opt.text} | Divisi: ${divisi} | Jabatan: ${jabatan}`;
        if(infoEl) infoEl.classList.remove('d-none');
    } else { if(infoEl) infoEl.classList.add('d-none'); }
    renderLivePreview();
}

function blockToDocHtml(b) {
    if(b.type==='text'){
        if(!b.content||!b.content.trim()) return '';
        const lines=b.content.split('\n');
        let html='<div style="line-height:1.8;margin-bottom:14px;font-size:0.92rem;color:#1e293b;">';
        let inKvGroup=false;
        lines.forEach(line=>{
            const trimmed=line.trim();
            const kvMatch=trimmed.match(/^([A-Za-z0-9\s\/]+)\s*:\s*(.*)$/);
            if(kvMatch&&kvMatch[1].trim().length>0&&kvMatch[1].trim().length<=25){
                if(!inKvGroup){html+='<div style="margin:10px 0 10px 20px;border-left:2px solid #cbd5e1;padding-left:14px;">';inKvGroup=true;}
                html+=`<div style="display:flex;margin-bottom:4px;font-size:0.9rem;"><span style="min-width:110px;width:110px;font-weight:600;color:#334155;">${kvMatch[1].trim()}</span><span style="width:15px;font-weight:600;color:#64748b;">:</span><span style="font-weight:600;color:#0f172a;">${kvMatch[2].trim()||'-'}</span></div>`;
            } else {
                if(inKvGroup){html+='</div>';inKvGroup=false;}
                if(trimmed===''){html+='<div style="height:8px;"></div>';} else {html+=`<p style="margin-bottom:8px;margin-top:0;">${trimmed}</p>`;}
            }
        });
        if(inKvGroup) html+='</div>'; html+='</div>'; return html;
    }
    if(b.type==='table'){
        const ths=b.headers.map(h=>`<th>${h}</th>`).join('');
        const trs=b.rows.map(row=>`<tr>${row.map(c=>`<td>${c}</td>`).join('')}</tr>`).join('');
        return `<table class="custom-doc-table table-style-${b.style}" style="break-inside:avoid;page-break-inside:avoid;"><thead><tr>${ths}</tr></thead><tbody>${trs}</tbody></table>`;
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

function buildMiniKopHtml(pageNum) {
    return `<div class="mini-page-header-repeat"><small style="font-size:0.78rem;font-weight:700;color:#1e293b;">PT. CIPTA DUTA WACANA</small><small style="font-size:0.72rem;color:#64748b;">Halaman ${pageNum}</small></div>`;
}

function renderLivePreview() {
    const paper=document.getElementById('previewPaper'); if(!paper) return;
    const paperSizeVal=(document.getElementById('paperSize')||{value:'A4'}).value||'A4';
    paper.className=`letter-paper-preview paper-size-${paperSizeVal}`;
    const logoPos=(document.getElementById('logoPosition')||{value:'top_right'}).value;
    const addrPos=(document.getElementById('addressPosition')||{value:'top_left'}).value;
    const accent=(document.getElementById('accentStyle')||{value:'line'}).value;
    let jenisSel=(document.getElementById('jenisSurat')||{value:''}).value||'SURAT PERUSAHAAN';
    let jenis=jenisSel;
    if(jenisSel==='Lainnya'){const cv=(document.getElementById('jenisSuratLainnya')||{value:''}).value.trim(); jenis=cv?cv.toUpperCase():'SURAT PERUSAHAAN';}
    const perihal=(document.getElementById('perihalInput')||{value:'Perihal...'}).value||'Perihal...';
    const tanggal=(document.getElementById('tanggalSurat')||{value:''}).value;
    const catatan=(document.getElementById('catatanInput')||{value:''}).value||'';
    const dateObj=new Date(tanggal);
    const dateFormatted=isNaN(dateObj)?tanggal:dateObj.toLocaleDateString('id-ID',{day:'numeric',month:'long',year:'numeric'});

    let logoHtml='';
    if(logoPos!=='none'&&companyLogoBase64) logoHtml=`<img src="${companyLogoBase64}" alt="CDW Logo" style="height:130px;max-height:140px;width:auto;max-width:280px;object-fit:contain;display:block;">`;

    let addrPad='';
    if(accent==='yellow_corner'&&addrPos==='top_left') addrPad='padding-top:40px;padding-left:55px;position:relative;z-index:3;';

    let addressHtml='';
    if(addrPos==='top_left'||addrPos==='top_center'){
        const align=addrPos==='top_center'?'text-align:center;':'';
        addressHtml=`<div style="${align}${addrPad}line-height:1.45;"><strong style="font-size:1.05rem;color:#000;">PT. CIPTA DUTA WACANA</strong><br><span style="font-size:0.88rem;font-weight:600;color:#1e293b;">Beltway Office Park Tower B Lantai 5</span><br><span style="font-size:0.82rem;color:#334155;">Jl. TB Simatupang No. 41 Ragunan-Pasar Minggu, Jakarta Selatan</span><br><span style="font-size:0.78rem;color:#475569;">Phone: (+62-21) 29857462; 29215392; 29084991 | Fax: (+62-21) 29857201</span></div>`;
    }

    let kopHtml='';
    if(logoPos==='top_right') kopHtml=`<div class="d-flex justify-content-between align-items-start mb-3"><div class="flex-grow-1 me-3">${addressHtml}</div><div class="flex-shrink-0" style="min-width:200px;min-height:130px;text-align:right;">${logoHtml}</div></div>`;
    else if(logoPos==='top_left') kopHtml=`<div class="d-flex justify-content-between align-items-start mb-3"><div class="flex-shrink-0 me-3" style="min-width:200px;min-height:130px;">${logoHtml}</div><div class="flex-grow-1">${addressHtml}</div></div>`;
    else if(logoPos==='center') kopHtml=`<div class="text-center mb-3"><div class="mb-2 d-flex justify-content-center" style="min-height:130px;">${logoHtml}</div>${addressHtml}</div>`;
    else kopHtml=`<div class="mb-3">${addressHtml}</div>`;

    let accentHtml='';
    if(accent==='line') accentHtml=`<div style="border-bottom:2px solid #0f172a;margin-bottom:22px;"></div>`;
    else if(accent==='blue_bar') accentHtml=`<div class="blue-header-banner text-center my-3">${jenis}</div>`;

    const bodyLimits=PAPER_BODY_PX[paperSizeVal]||PAPER_BODY_PX.A4;

    const renderBlocks = [];
    blocks.forEach(b => {
        if (b.type === 'text') {
            const paras = (b.content || '').split(/\n+/).filter(p => p.trim() !== '');
            if (paras.length > 1) {
                paras.forEach(p => renderBlocks.push({ type: 'text', content: p, originalId: b.id }));
            } else {
                renderBlocks.push(b);
            }
        } else {
            renderBlocks.push(b);
        }
    });

    const measurer=document.createElement('div');
    measurer.style.cssText=`position:absolute;visibility:hidden;left:-9999px;top:0;width:${(paper.clientWidth||794)-120}px;font-family:Inter,Arial,sans-serif;font-size:0.9rem;`;
    document.body.appendChild(measurer);
    const blockHeights=renderBlocks.map(b=>{const div=document.createElement('div');div.innerHTML=blockToDocHtml(b);measurer.appendChild(div);const h=div.getBoundingClientRect().height||40;measurer.removeChild(div);return Math.ceil(h)+16;});
    document.body.removeChild(measurer);

    const pages=[[]]; let curH=0, pNum=1;
    renderBlocks.forEach((b,i)=>{
        const bh=blockHeights[i]||50;
        const limit=pNum===1?bodyLimits.page1:bodyLimits.cont;
        if(curH+bh>limit&&curH>0){pages.push([]);pNum++;curH=bh;} else curH+=bh;
        pages[pages.length-1].push(b);
    });

    const badge=document.getElementById('pageCountBadge');
    if(badge) badge.innerHTML=pages.length>1?`<i class="fas fa-copy text-primary me-1"></i>${pages.length} Halaman (Auto)`:'';

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
        <div class="row pt-4 text-center mt-auto" style="position:relative;z-index:5;${mbStyle}">
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
        <div class="row pt-4 text-center mt-auto" style="position:relative;z-index:5;${mbStyle}">
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
        <div class="row pt-4 text-center mt-auto" style="position:relative;z-index:5;${mbStyle}">
            <div class="col-6 ms-auto">
                <p class="text-sm mb-5">${p1Title}<br><strong>${p1Nama}</strong></p>
                <p class="fw-bold text-dark mb-0 border-bottom d-inline-block pb-1" style="min-width:160px;">${p1Jab}</p>
            </div>
        </div>`;
    }
    const catatanHtml=catatan?`<div class="alert alert-warning border-0 rounded-3 text-dark p-2 mb-3" style="font-size:0.78rem;"><strong>Catatan Internal:</strong> ${catatan}</div>`:'';

    let paperHtml='';
    pages.forEach((pageBlocks,pi)=>{
        const isFirstPage=(pi===0), isLastPage=(pi===pages.length-1), pageNum=pi+1;
        if(!isFirstPage) paperHtml+=`<div class="page-break-indicator" data-label="📄 HALAMAN ${pageNum}"></div>`;

        let pageCornerTop='', pageCornerBottom='';
        if(accent==='yellow_corner'){
            if(isFirstPage) pageCornerTop=`<svg style="position:absolute;top:0;left:0;width:200px;height:200px;pointer-events:none;z-index:1;opacity:0.85;" viewBox="0 0 280 280"><path d="M 0 0 L 280 0 Q 70 70 0 280 Z" fill="#f5a600"/></svg>`;
            pageCornerBottom=`<svg style="position:absolute;bottom:0;right:0;width:160px;height:160px;pointer-events:none;z-index:1;opacity:0.85;" viewBox="0 0 260 260"><path d="M 260 260 L 0 260 Q 190 190 260 0 Z" fill="#f5a600"/></svg>`;
        }

        let pageFooterAddr='';
        if(addrPos==='footer') pageFooterAddr=`<div style="position:absolute;bottom:18px;left:40px;right:180px;font-size:0.7rem;color:#475569;line-height:1.35;z-index:5;"><strong>PT. Cipta Duta Wacana</strong><br>Beltway Office Park Tower B Lt.5, Jl. Letjen TB Simatupang No.41, Ragunan, Jakarta Selatan 12550<br>Tel: +62-21 29857462 | <span style="color:#0284c7">www.cdw-engineering.com</span></div>`;

        const pageNumIndicator=`<div style="position:absolute;bottom:18px;right:30px;font-size:0.82rem;font-weight:700;color:#1e3c72;z-index:5;text-align:right;">Halaman ${pageNum} dari ${pages.length}</div>`;

        paperHtml+=`<div class="doc-page-sheet">${pageCornerTop}${pageCornerBottom}<div style="position:relative;z-index:2;display:flex;flex-direction:column;flex-grow:1;height:100%;">`;
        if(isFirstPage){
            paperHtml+=`<div style="margin-bottom:16px;position:relative;z-index:3;">${kopHtml}</div>${accentHtml}`;
            paperHtml+=accent!=='blue_bar'?`<h5 class="text-center fw-bold text-dark text-uppercase mb-3" style="letter-spacing:0.5px;">${jenis}</h5>`:'';
            paperHtml+=`<div class="d-flex justify-content-between fw-semibold text-secondary mb-3 pb-2 border-bottom border-light" style="font-size:0.88rem;"><span><strong>Nomor:</strong> ${nomorSuratText}</span><span><strong>Tanggal:</strong> ${dateFormatted}</span></div>`;
            paperHtml+=`<div class="mb-3"><strong class="text-dark">Perihal: ${perihal}</strong></div>`;
        } else { paperHtml+=buildMiniKopHtml(pageNum); }
        paperHtml+=`<div class="page-content-area" style="flex-grow:1;">`;
        pageBlocks.forEach(b=>{ paperHtml+=blockToDocHtml(b); });
        paperHtml+=`</div>`;
if(isLastPage){ paperHtml+=`<div style="margin-top:auto;padding-top:20px;">${catatanHtml}${signatureHtml}</div>`; }
        paperHtml+=`</div>${pageFooterAddr}${pageNumIndicator}</div>`;
    });

    if(blocks.length===0){
        paperHtml=`<div class="doc-page-sheet"><div style="position:relative;z-index:2;display:flex;flex-direction:column;flex-grow:1;"><div style="min-height:120px;margin-bottom:12px;">${kopHtml}</div>${accentHtml}<div style="min-height:200px;display:flex;align-items:center;justify-content:center;color:#94a3b8;"><div class="text-center"><i class="fas fa-file-alt fa-3x mb-3" style="opacity:0.3;"></i><p>Konten surat akan muncul di sini.</p></div></div><div style="margin-top:auto;">${catatanHtml}${signatureHtml}</div></div></div>`;
    }
    paper.innerHTML = paperHtml;

    const totalHalamanInput = document.getElementById('totalHalamanInput');
    if (totalHalamanInput) {
        const detectedSheets = paper.querySelectorAll('.doc-page-sheet').length || 1;
        totalHalamanInput.value = detectedSheets;
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const parsed = parseHtmlToBlocks(existingIsiSurat);
    if (parsed.length > 0) blocks = parsed;
    else blocks = [{ id: genBlkId(), type: 'text', content: '' }];
    renderBlocksEditor();

    const form=document.getElementById('suratForm');
    if(form){
        form.addEventListener('submit',function(){
            const hidden=document.getElementById('isiSurat');
            const htmlFull=document.getElementById('htmlFull');
            const paper=document.getElementById('previewPaper');
            const totalHalamanInput = document.getElementById('totalHalamanInput');
            if(hidden) hidden.value=assembleIsiSurat();
            if(htmlFull&&paper) htmlFull.value=paper.innerHTML;
            if(totalHalamanInput&&paper) {
                const detectedSheets = paper.querySelectorAll('.doc-page-sheet').length || 1;
                totalHalamanInput.value = detectedSheets;
            }
        });
    }

    ['templateLayout','paperSize','logoPosition','addressPosition','accentStyle','jenisSurat','jenisSuratLainnya','karyawanSelect','tanggalSurat','perihalInput','statusSurat','catatanInput'].forEach(id=>{
        const el=document.getElementById(id);
        if(el){el.addEventListener('change',renderLivePreview);el.addEventListener('input',renderLivePreview);}
    });

    updateKaryawanText();
    renderLivePreview();
});

function openPrintConfirmation() {
    const jenisSel=(document.getElementById('jenisSurat')||{}).value||'-';
    const sel=document.getElementById('karyawanSelect');
    const opt=sel?sel.options[sel.selectedIndex]:null;
    const nama=opt&&opt.value?(opt.dataset.nama||opt.text.split(' - ')[0]):'-';
    const tanggal=(document.getElementById('tanggalSurat')||{}).value||'-';
    const paper=(document.getElementById('paperSize')||{}).value||'A4';

    const sheets = document.querySelectorAll('#previewPaper .doc-page-sheet');
    const pageCount = sheets.length > 0 ? sheets.length : 1;
    const pageTxt = pageCount + ' Halaman';

    const pj=document.getElementById('printConfirmJenis'); if(pj) pj.textContent=jenisSel;
    const pk=document.getElementById('printConfirmKaryawan'); if(pk) pk.textContent=nama;
    const pt=document.getElementById('printConfirmTanggal'); if(pt) pt.textContent=tanggal;
    const pp=document.getElementById('printConfirmPaper'); if(pp) pp.textContent=paper + ' (' + pageTxt + ')';

    const confirmPageCountInput = document.getElementById('confirmPageCountInput');
    const printConfirmPageBadge = document.getElementById('printConfirmPageBadge');
    const confirmPageCountText = document.getElementById('confirmPageCountText');
    const totalHalamanInput = document.getElementById('totalHalamanInput');

    if (totalHalamanInput) totalHalamanInput.value = pageCount;
    if (confirmPageCountInput) confirmPageCountInput.value = pageCount + ' Halaman ' + paper + ' (Terdeteksi Otomatis oleh Sistem)';
    if (printConfirmPageBadge) printConfirmPageBadge.textContent = pageCount + ' Halaman ' + paper;
    if (confirmPageCountText) confirmPageCountText.textContent = pageCount + ' Halaman';

    const modalEl=document.getElementById('confirmPrintModal');
    if(modalEl) new bootstrap.Modal(modalEl).show();
}

function proceedToDedicatedPrintView() {
    const modalEl=document.getElementById('confirmPrintModal');
    if(modalEl){const m=bootstrap.Modal.getInstance(modalEl);if(m) m.hide();}
    const previewPaper=document.getElementById('previewPaper');
    const printPaper=document.getElementById('dedicatedPrintPaper');
    if(previewPaper&&printPaper){printPaper.className=previewPaper.className;printPaper.innerHTML=previewPaper.innerHTML;}
    const mainView=document.getElementById('mainEditorView');
    const printView=document.getElementById('dedicatedPrintView');
    if(mainView&&printView){
        mainView.style.setProperty('display','none','important');
        printView.style.setProperty('display','block','important');
        printView.classList.remove('d-none');
        window.scrollTo({top:0,behavior:'smooth'});
    }
}

function closeDedicatedPrintView() {
    const mainView=document.getElementById('mainEditorView');
    const printView=document.getElementById('dedicatedPrintView');
    if(mainView&&printView){printView.style.setProperty('display','none','important');printView.classList.add('d-none');mainView.style.removeProperty('display');}
}

function triggerActualPrint() { window.print(); }

window.addEventListener('beforeprint',function(){ const pv=document.getElementById('dedicatedPrintView'); if(pv&&pv.classList.contains('d-none')) proceedToDedicatedPrintView(); });

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
.doc-page-sheet {
    position:relative !important; background:#ffffff !important;
    min-height:1123px !important; max-height:1123px !important; height:1123px !important;
    box-shadow:0 10px 30px rgba(0,0,0,0.08) !important;
    margin:0 auto 30px auto !important; border-radius:4px !important;
    display:flex !important; flex-direction:column !important;
    box-sizing:border-box !important; padding:45px 50px 75px 50px !important; width:100% !important;
    overflow:hidden !important;
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

<?= view('admin/templates/footer') ?>
