<?= $this->include('direktur/templates/header') ?>
<?= $this->include('direktur/templates/sidebar') ?>
<?= $this->include('direktur/templates/navbar') ?>

<?php
$logoPos   = $surat['logo_position'] ?? 'top_right';
$addrPos   = $surat['address_position'] ?? 'top_left';
$accent    = $surat['accent_style'] ?? 'line';
$paperSize = $surat['paper_size'] ?? 'A4';
$jenisStr  = $surat['jenis_surat'] ?? 'SURAT KARYAWAN';

function formatBodyTextWithTables($text) {
    if (empty($text)) return '';
    $pattern = '/(<table[\s\S]*?<\/table>)/i';
    $parts   = preg_split($pattern, $text, -1, PREG_SPLIT_DELIM_CAPTURE);
    $output  = '';
    foreach ($parts as $part) {
        if (preg_match('/^<table/i', trim($part))) {
            $output .= $part;
        } else {
            $lines     = explode("\n", $part);
            $html      = '<div style="line-height:1.55; margin-bottom:10px; font-size:0.9rem; color:#1e293b;">';
            $inKvGroup = false;
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if (preg_match('/^([A-Za-z0-9\s\/]+)\s*:\s*(.*)$/', $trimmed, $kvMatch) && strlen(trim($kvMatch[1])) <= 25) {
                    if (!$inKvGroup) {
                        $html .= '<div style="margin:6px 0 6px 15px;border-left:2px solid #cbd5e1;padding-left:12px;">';
                        $inKvGroup = true;
                    }
                    $key  = esc(trim($kvMatch[1]));
                    $val  = esc(trim($kvMatch[2]));
                    $html .= '<div style="display:flex;margin-bottom:3px;font-size:0.88rem;">
                        <span style="min-width:105px;width:105px;font-weight:600;color:#334155;">' . $key . '</span>
                        <span style="width:12px;font-weight:600;color:#64748b;">:</span>
                        <span style="font-weight:600;color:#0f172a;">' . ($val ?: '-') . '</span>
                    </div>';
                } else {
                    if ($inKvGroup) { $html .= '</div>'; $inKvGroup = false; }
                    if ($trimmed === '') {
                        $html .= '<div style="height:6px;"></div>';
                    } else {
                        $html .= '<p style="margin-bottom:5px;margin-top:0;">' . esc($trimmed) . '</p>';
                    }
                }
            }
            if ($inKvGroup) $html .= '</div>';
            $html .= '</div>';
            $output .= $html;
        }
    }
    return $output;
}
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
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 5px 14px;
        border-radius: 30px;
        font-size: 0.8rem;
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

    .data-pill-bar {
        background: #f8fafc;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 12px;
        padding: 10px 14px;
        margin-bottom: 12px;
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

    /* Document Printable Paper Styling - Standard Vertical A4 Ratio */
    .letter-paper-render {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        box-shadow: 0 14px 40px rgba(0, 0, 0, 0.12);
        padding: 55px 60px;
        position: relative;
        width: 100%;
        max-width: 794px;
        min-height: 1123px;
        margin: 0 auto;
        font-family: 'Inter', Arial, sans-serif;
        color: #1e293b;
        overflow: hidden;
        box-sizing: border-box;
    }

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
</style><div id="mainDetailView">
<div class="container-fluid py-3 py-md-4">

    <!-- Header Section Terpadu -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light flex-wrap gap-2 no-print">
        <div class="d-flex align-items-center">
            <div class="bg-gradient-primary text-white rounded-3 p-2.5 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px;">
                <i class="fas fa-file-contract fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark">Detail & Pratinjau Surat</h4>
                <small class="text-muted">Nomor Surat: <strong><?= esc($surat['nomor_surat']) ?></strong> | Ukuran Kertas: <span class="badge bg-secondary"><?= esc($paperSize) ?></span></small>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="<?= base_url('direktur/karyawan/surat/edit/' . $surat['id']) ?>" class="btn btn-outline-info rounded-pill px-3.5 py-2 shadow-sm d-inline-flex align-items-center text-sm fw-semibold">
                <i class="fas fa-edit me-1.5"></i> <span class="d-none d-md-inline">Edit Surat</span>
            </a>
            <button onclick="exportLetterToWord()" class="btn btn-primary rounded-pill px-3.5 py-2 shadow-sm d-inline-flex align-items-center text-sm fw-semibold" style="background:linear-gradient(135deg,#0052d4 0%,#4364f7 50%,#6fb1fc 100%);border:none;">
                <i class="fas fa-file-word me-1.5"></i> <span class="d-none d-md-inline">Download Word (.doc)</span><span class="d-inline d-md-none">Word</span>
            </button>
            <button onclick="openPrintConfirmation()" class="btn btn-dark rounded-pill px-3.5 py-2 shadow-sm d-inline-flex align-items-center text-sm fw-semibold">
                <i class="fas fa-print me-1.5"></i> <span class="d-none d-md-inline">Pratinjau & Cetak PDF (<?= esc($paperSize) ?>)</span>
            </button>
            <a href="<?= base_url('direktur/karyawan/surat') ?>" class="btn btn-outline-secondary rounded-pill px-3.5 py-2 shadow-sm d-inline-flex align-items-center text-sm fw-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> <span class="d-none d-md-inline">Kembali</span>
            </a>
        </div>
    </div>

    <!-- Alert Flash -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show text-white rounded-3 mb-4 no-print" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Sidebar Metadata -->
        <div class="col-12 col-lg-4 no-print">
            
            <!-- Metadata Surat -->
            <div class="card employee-card-modern p-4 mb-4">
                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="fas fa-info-circle text-primary"></i> Ringkasan Dokumen
                </h6>

                <div class="data-pill-bar">
                    <div class="data-label"><i class="fas fa-hashtag text-primary"></i> Nomor Surat</div>
                    <div class="data-value"><?= esc($surat['nomor_surat']) ?></div>
                </div>

                <div class="data-pill-bar">
                    <div class="data-label"><i class="fas fa-tag text-primary"></i> Jenis Surat</div>
                    <div class="data-value"><?= esc($surat['jenis_surat']) ?></div>
                </div>

                <div class="data-pill-bar">
                    <div class="data-label"><i class="far fa-file-alt text-primary"></i> Ukuran Kertas Cetak</div>
                    <div class="data-value"><?= esc($paperSizes[$paperSize] ?? $paperSize) ?></div>
                </div>

                <div class="data-pill-bar">
                    <div class="data-label"><i class="far fa-calendar-alt text-primary"></i> Tanggal Diterbitkan</div>
                    <div class="data-value"><?= date('d F Y', strtotime($surat['tanggal_surat'])) ?></div>
                </div>

                <div class="data-pill-bar mb-3">
                    <div class="data-label"><i class="fas fa-tasks text-primary"></i> Status Surat</div>
                    <div class="data-value">
                        <?php
                            $statusStr = strtolower($surat['status'] ?? 'draft');
                            $statusPillClass = 'status-pill-draft';
                            $statusText = 'Draft';
                            if ($statusStr === 'diterbitkan') {
                                $statusPillClass = 'status-pill-active';
                                $statusText = 'Diterbitkan';
                            } elseif ($statusStr === 'dibatalkan') {
                                $statusPillClass = 'status-pill-inactive';
                                $statusText = 'Dibatalkan';
                            }
                        ?>
                        <span class="status-pill <?= $statusPillClass ?>">
                            <?= $statusText ?>
                        </span>
                    </div>
                </div>

                <!-- Form Ubah Status -->
                <form action="<?= base_url('direktur/karyawan/surat/update-status/' . $surat['id']) ?>" method="post" class="mt-3">
                    <?= csrf_field() ?>
                    <label class="form-label fw-bold text-dark text-xs mb-2">Ubah Status Dokumen</label>
                    <div class="input-group">
                        <select name="status" class="form-select form-select-sm rounded-start-3">
                            <option value="draft" <?= $statusStr === 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="diterbitkan" <?= $statusStr === 'diterbitkan' ? 'selected' : '' ?>>Diterbitkan</option>
                            <option value="dibatalkan" <?= $statusStr === 'dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm rounded-end-3 px-3">
                            <i class="fas fa-save me-1"></i> Simpan
                        </button>
                    </div>
                </form>

            </div>

            <!-- Profil Karyawan Terkait -->
            <?php if (!empty($karyawan)): ?>
                <div class="card employee-card-modern p-4">
                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <i class="fas fa-user-tie text-primary"></i> Penerima Surat (Karyawan)
                    </h6>

                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar-circle flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.2rem; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                            <?= strtoupper(substr($karyawan['nama_lengkap'] ?? 'K', 0, 1)) ?>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark"><?= esc($karyawan['nama_lengkap']) ?></h6>
                            <small class="text-muted"><?= esc($karyawan['jabatan'] ?? '-') ?> | <?= esc($karyawan['divisi'] ?? '-') ?></small>
                        </div>
                    </div>

                    <div class="data-pill-bar mb-2">
                        <div class="data-label"><i class="fas fa-id-card text-primary"></i> NIK Karyawan</div>
                        <div class="data-value"><?= esc($karyawan['nik'] ?? '-') ?></div>
                    </div>

                    <div class="data-pill-bar">
                        <div class="data-label"><i class="fas fa-envelope text-primary"></i> Email</div>
                        <div class="data-value"><?= esc($karyawan['email'] ?? '-') ?></div>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <!-- Render Kertas Surat Preview -->
        <div class="col-12 col-lg-8 print-full-width">
            <div class="letter-paper-render paper-size-<?= esc($paperSize) ?>" id="previewPaper">
                <?php if (!empty($surat['html_full'])): ?>
                    <?= $surat['html_full'] ?>
                <?php else: ?>
                    <div class="doc-page-sheet">
                        <!-- Corner Accents -->
                        <?php if ($accent === 'yellow_corner'): ?>
                            <svg class="accent-top-left" viewBox="0 0 280 280">
                                <path d="M 0 0 L 280 0 Q 70 70 0 280 Z" fill="#f5a600"/>
                            </svg>
                            <svg class="accent-bottom-right" viewBox="0 0 260 260">
                                <path d="M 260 260 L 0 260 Q 190 190 260 0 Z" fill="#f5a600"/>
                            </svg>
                        <?php endif; ?>
                            
                        <!-- Header Section Top (Address & Logo) -->
                        <?php
                            $addressPaddingStyle = '';
                            if ($accent === 'yellow_corner' && $addrPos === 'top_left') {
                                $addressPaddingStyle = 'padding-top: 40px; padding-left: 55px; position: relative; z-index: 3;';
                            }

                            $addressHtml = '';
                            if ($addrPos === 'top_left' || $addrPos === 'top_center') {
                                $alignStyle = $addrPos === 'top_center' ? 'text-align: center;' : '';
                                $addressHtml = '
                                    <div style="' . $alignStyle . ' ' . $addressPaddingStyle . ' line-height: 1.45;">
                                        <strong style="font-size: 1.05rem; color: #000;">PT. CIPTA DUTA WACANA</strong><br>
                                        <span style="font-size: 0.88rem; font-weight: 600; color: #1e293b;">Beltway Office Park Tower B Lantai 5</span><br>
                                        <span style="font-size: 0.82rem; color: #334155;">Jl. TB Simatupang No. 41 Ragunan-Pasar Minggu, Jakarta Selatan</span><br>
                                        <span style="font-size: 0.78rem; color: #475569;">Phone: (+62-21) 29857462; 29215392; 29084991 | Fax: (+62-21) 29857201</span>
                                    </div>
                                ';
                            }

                            $logoHtml = '';
                            if ($logoPos !== 'none' && !empty($logoBase64)) {
                                $logoHtml = '<img src="' . $logoBase64 . '" alt="CDW Logo" style="height: 90px; max-height: 100px; width: auto; max-width: 220px; object-fit: contain; display: block;">';
                            }
                        ?>

                        <div style="min-height: 90px; margin-bottom: 8px;">
                            <?php if ($logoPos === 'top_right'): ?>
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="flex-grow-1 me-3"><?= $addressHtml ?></div>
                                    <div class="flex-shrink-0" style="min-width: 200px; min-height: 130px; text-align: right;"><?= $logoHtml ?></div>
                                </div>
                            <?php elseif ($logoPos === 'top_left'): ?>
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="flex-shrink-0 me-3" style="min-width: 200px; min-height: 130px;"><?= $logoHtml ?></div>
                                    <div class="flex-grow-1"><?= $addressHtml ?></div>
                                </div>
                            <?php elseif ($logoPos === 'center'): ?>
                                <div class="text-center mb-3">
                                    <?php if ($logoHtml): ?><div class="mb-2 d-flex justify-content-center" style="min-height: 130px;"><?= $logoHtml ?></div><?php endif; ?>
                                    <div><?= $addressHtml ?></div>
                                </div>
                            <?php else: ?>
                                <div class="mb-3"><?= $addressHtml ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Dynamic Accent Line or Blue Bar -->
                        <?php if ($accent === 'line'): ?>
                            <div style="border-bottom: 2px solid #0f172a; margin-bottom: 22px;"></div>
                        <?php elseif ($accent === 'blue_bar'): ?>
                            <div class="blue-header-banner text-center my-3 shadow-xs">
                                <?= esc($jenisStr) ?>
                            </div>
                        <?php endif; ?>

                        <!-- Title (If not blue_bar) -->
                        <?php if ($accent !== 'blue_bar'): ?>
                            <h5 class="text-center fw-bold text-dark text-uppercase mb-3" style="letter-spacing: 0.5px;">
                                <?= esc($jenisStr) ?>
                            </h5>
                        <?php endif; ?>

                        <!-- Nomor & Tanggal -->
                        <div class="d-flex justify-content-between text-sm fw-semibold text-secondary mb-3 pb-2 border-bottom border-light">
                            <span><strong>Nomor:</strong> <?= esc($surat['nomor_surat']) ?></span>
                            <span><strong>Tanggal:</strong> <?= date('d F Y', strtotime($surat['tanggal_surat'])) ?></span>
                        </div>

                        <!-- Perihal -->
                        <div class="mb-3">
                            <strong class="text-dark">Perihal: <?= esc($surat['perihal']) ?></strong>
                        </div>

                        <!-- Isi Surat -->
                        <div class="text-dark text-sm leading-relaxed mb-4 page-content-area" style="line-height: 1.8; flex-grow: 1; font-size: 0.92rem;">
                            <?= formatBodyTextWithTables($surat['isi_surat'] ?? '') ?>
                        </div>

                        <!-- Catatan Internal & Tanda Tangan at bottom -->
                        <div style="margin-top: auto; padding-top: 15px;">
                            <?php if (!empty($surat['catatan'])): ?>
                                <div class="alert alert-warning border-0 rounded-3 text-dark text-xs p-2.5 mb-3 no-print" style="font-size:0.78rem;">
                                    <i class="fas fa-exclamation-circle me-1 text-warning"></i> <strong>Catatan Internal:</strong> <?= esc($surat['catatan']) ?>
                                </div>
                            <?php endif; ?>

                            <!-- Tanda Tangan -->
                            <?php
                                $sigLayout = $surat['signature_layout'] ?? '1_pihak';
                                $sigData   = json_decode($surat['signature_data'] ?? '{}', true) ?: [];
                                $p1Title   = !empty($sigData['p1_title']) ? $sigData['p1_title'] : 'Pihak Pertama,';
                                $p1Nama    = !empty($sigData['p1_nama'])  ? $sigData['p1_nama']  : ($karyawan['nama_lengkap'] ?? 'PT. CIPTA DUTA WACANA');
                                $p1Jab     = !empty($sigData['p1_jabatan']) ? $sigData['p1_jabatan'] : 'Direktur Utama';

                                $p2Title   = !empty($sigData['p2_title']) ? $sigData['p2_title'] : 'Pihak Kedua,';
                                $p2Nama    = !empty($sigData['p2_nama'])  ? $sigData['p2_nama']  : 'PT. CIPTA DUTA WACANA';
                                $p2Jab     = !empty($sigData['p2_jabatan']) ? $sigData['p2_jabatan'] : 'Direktur Utama';

                                $p3Title   = !empty($sigData['p3_title']) ? $sigData['p3_title'] : 'Pihak Ketiga,';
                                $p3Nama    = !empty($sigData['p3_nama'])  ? $sigData['p3_nama']  : 'PT. CIPTA DUTA WACANA';
                                $p3Jab     = !empty($sigData['p3_jabatan']) ? $sigData['p3_jabatan'] : 'Direktur Utama';
                                $mbStyle   = $addrPos === 'footer' ? 'margin-bottom: 70px;' : 'margin-bottom: 15px;';
                            ?>
                            <?php if ($sigLayout === '2_pihak'): ?>
                                <div class="row pt-4 text-center mt-auto" style="position:relative; z-index:5; <?= $mbStyle ?>">
                                    <div class="col-6">
                                        <p style="font-size: 0.88rem; margin-bottom: 2.5rem;"><?= esc($p1Title) ?><br><strong><?= esc($p1Nama) ?></strong></p>
                                        <p class="fw-bold text-dark mb-0 border-bottom d-inline-block pb-1" style="min-width: 150px;"><?= esc($p1Jab) ?></p>
                                    </div>
                                    <div class="col-6">
                                        <p style="font-size: 0.88rem; margin-bottom: 2.5rem;"><?= esc($p2Title) ?><br><strong><?= esc($p2Nama) ?></strong></p>
                                        <p class="fw-bold text-dark mb-0 border-bottom d-inline-block pb-1" style="min-width: 150px;"><?= esc($p2Jab) ?></p>
                                    </div>
                                </div>
                            <?php elseif ($sigLayout === '3_pihak'): ?>
                                <div class="row pt-4 text-center mt-auto" style="position:relative; z-index:5; <?= $mbStyle ?>">
                                    <div class="col-4">
                                        <p style="font-size: 0.88rem; margin-bottom: 2.5rem;"><?= esc($p1Title) ?><br><strong><?= esc($p1Nama) ?></strong></p>
                                        <p class="fw-bold text-dark mb-0 border-bottom d-inline-block pb-1" style="min-width: 120px;"><?= esc($p1Jab) ?></p>
                                    </div>
                                    <div class="col-4">
                                        <p style="font-size: 0.88rem; margin-bottom: 2.5rem;"><?= esc($p2Title) ?><br><strong><?= esc($p2Nama) ?></strong></p>
                                        <p class="fw-bold text-dark mb-0 border-bottom d-inline-block pb-1" style="min-width: 120px;"><?= esc($p2Jab) ?></p>
                                    </div>
                                    <div class="col-4">
                                        <p style="font-size: 0.88rem; margin-bottom: 2.5rem;"><?= esc($p3Title) ?><br><strong><?= esc($p3Nama) ?></strong></p>
                                        <p class="fw-bold text-dark mb-0 border-bottom d-inline-block pb-1" style="min-width: 120px;"><?= esc($p3Jab) ?></p>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="row pt-2 text-center" style="position: relative; z-index: 5; <?= $mbStyle ?>">
                                    <div class="col-6 ms-auto">
                                        <p style="font-size: 0.88rem; margin-bottom: 2.5rem;"><?= esc($p1Title) ?><br><strong><?= esc($p1Nama) ?></strong></p>
                                        <p class="fw-bold text-dark mb-0 border-bottom d-inline-block pb-1" style="min-width: 150px;"><?= esc($p1Jab) ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>

                    <!-- Footer Address -->
                    <?php if ($addrPos === 'footer'): ?>
                        <div style="position: absolute; bottom: 18px; left: 40px; right: 180px; font-size: 0.7rem; color: #475569; line-height: 1.35; z-index: 5;">
                            <strong>PT. Cipta Duta Wacana</strong><br>
                            Beltway Office Park Tower B Lt.5, Jl. Letjen TB Simatupang No.41, Ragunan, Pasar Minggu, Jakarta Selatan 12550<br>
                            Tel: +62-21 29857462 | Fax: +62-21 29857201 | <span style="color: #0284c7;">www.cdw-engineering.com</span>
                        </div>
                    <?php endif; ?>
                    <div style="position: absolute; bottom: 18px; right: 30px; font-size: 0.82rem; font-weight: 700; color: #1e3c72; z-index: 5; text-align: right;">
                        Halaman 1 dari 1
                    </div>

                </div>
                <?php endif; ?>

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
                <i class="fas fa-arrow-left me-1.5"></i> Kembali ke Detail Surat
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
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 py-2 fw-bold shadow text-white me-1" onclick="exportLetterToWord()">
                <i class="fas fa-file-word me-1"></i> Download Word (.doc)
            </button>
            <button type="button" class="btn btn-warning btn-sm rounded-pill px-4 py-2 fw-bold shadow text-dark" onclick="triggerActualPrint()">
                <i class="fas fa-print me-1.5"></i> Cetak Sekarang / Simpan PDF
            </button>
        </div>
    </div>

    <div style="padding: 24px 0; display:flex; justify-content:center; align-items:flex-start;">
        <div id="dedicatedPrintPaper" class="letter-paper-render" style="width:100%;max-width:860px;"></div>
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
                        <strong class="text-dark"><?= esc($jenisStr) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Penerima / Karyawan:</span>
                        <strong class="text-dark"><?= esc($karyawan['nama_lengkap'] ?? '-') ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Tanggal Surat:</span>
                        <strong class="text-dark"><?= date('d F Y', strtotime($surat['tanggal_surat'])) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Format Kertas:</span>
                        <strong class="text-dark"><?= esc($paperSize) ?></strong>
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

<script>
function openPrintConfirmation() {
    const modalEl = document.getElementById('confirmPrintModal');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
}

function proceedToDedicatedPrintView() {
    window.location.href = "<?= base_url('direktur/karyawan/surat/pratinjau/' . $surat['id']) ?>?print_now=1";
}

function closeDedicatedPrintView() {
    const mainView = document.getElementById('mainDetailView');
    const printView = document.getElementById('dedicatedPrintView');
    if (mainView && printView) {
        printView.style.cssText = 'display:none !important;';
        printView.classList.add('d-none');
        mainView.style.removeProperty('display');
        mainView.classList.remove('d-none');
    }
    // Restore sidebar, navbar and body
    document.querySelectorAll('.sidebar, .sidenav, .top-navbar, nav.navbar, #ci-debug-bar, .ci-debug-bar, .debug-bar').forEach(el => {
        const orig = el.dataset.printHidden;
        el.style.display = (orig !== undefined && orig !== '') ? orig : '';
        delete el.dataset.printHidden;
    });
    document.body.style.overflow = '';
    document.body.style.paddingLeft = '';
    document.body.style.marginLeft = '';
}

function triggerActualPrint() {
    window.print();
}

function exportLetterToWord() {
    const paperElement = document.getElementById('previewPaper') || document.getElementById('dedicatedPrintPaper');
    if (!paperElement) return;

    const clone = paperElement.cloneNode(true);
    clone.querySelectorAll('.no-print').forEach(el => el.remove());

    // 1. Convert Header Flex (Logo & Address) to 2-column Word table
    clone.querySelectorAll('.d-flex.justify-content-between.align-items-start').forEach(flexHeader => {
        const leftCol = flexHeader.children[0] ? flexHeader.children[0].innerHTML : '';
        const rightCol = flexHeader.children[1] ? flexHeader.children[1].innerHTML : '';
        const table = document.createElement('table');
        table.setAttribute('width', '100%');
        table.setAttribute('border', '0');
        table.setAttribute('cellspacing', '0');
        table.setAttribute('cellpadding', '0');
        table.style.cssText = 'width:100%; border:none; margin-bottom:15px; border-collapse:collapse;';
        table.innerHTML = `
            <tr>
                <td valign="top" align="left" style="border:none; padding:0; text-align:left;">${leftCol}</td>
                <td valign="top" align="right" style="border:none; padding:0; text-align:right; width:220px;">${rightCol}</td>
            </tr>
        `;
        flexHeader.parentNode.replaceChild(table, flexHeader);
    });

    // 2. Convert Nomor & Tanggal line to 2-column Word table
    clone.querySelectorAll('.d-flex.justify-content-between').forEach(flexLine => {
        if (flexLine.textContent.includes('Nomor:') || flexLine.textContent.includes('Tanggal:')) {
            const leftText = flexLine.children[0] ? flexLine.children[0].innerHTML : '';
            const rightText = flexLine.children[1] ? flexLine.children[1].innerHTML : '';
            const table = document.createElement('table');
            table.setAttribute('width', '100%');
            table.setAttribute('border', '0');
            table.setAttribute('cellspacing', '0');
            table.setAttribute('cellpadding', '0');
            table.style.cssText = 'width:100%; border:none; margin-bottom:12pt; border-bottom:1px solid #cbd5e1; padding-bottom:6pt; border-collapse:collapse;';
            table.innerHTML = `
                <tr>
                    <td align="left" style="border:none; padding:0; font-size:10pt; font-weight:bold; color:#475569;">${leftText}</td>
                    <td align="right" style="border:none; padding:0; font-size:10pt; font-weight:bold; color:#475569; text-align:right;">${rightText}</td>
                </tr>
            `;
            flexLine.parentNode.replaceChild(table, flexLine);
        }
    });

    // 3. Convert Key-Value flex items (Nama, Jabatan, Divisi) to borderless table
    clone.querySelectorAll('div[style*="display:flex"]').forEach(kvDiv => {
        if (kvDiv.children.length === 3 && kvDiv.children[1].textContent.trim() === ':') {
            const key = kvDiv.children[0].innerHTML;
            const val = kvDiv.children[2].innerHTML;
            const row = document.createElement('table');
            row.setAttribute('border', '0');
            row.setAttribute('cellspacing', '0');
            row.setAttribute('cellpadding', '0');
            row.style.cssText = 'width:100%; border:none; margin-bottom:3pt; border-collapse:collapse; font-size:10pt;';
            row.innerHTML = `
                <tr>
                    <td width="120" valign="top" style="border:none; padding:0; font-weight:bold; color:#334155;">${key}</td>
                    <td width="15" valign="top" style="border:none; padding:0; font-weight:bold; color:#64748b;">:</td>
                    <td valign="top" style="border:none; padding:0; font-weight:bold; color:#0f172a;">${val}</td>
                </tr>
            `;
            kvDiv.parentNode.replaceChild(row, kvDiv);
        }
    });

    // 4. Convert Signature row to 2-column table
    clone.querySelectorAll('.row').forEach(rowDiv => {
        if (rowDiv.textContent.includes('Hormat kami') || rowDiv.textContent.includes('Direktur')) {
            const sigHtml = rowDiv.querySelector('.col-6') ? rowDiv.querySelector('.col-6').innerHTML : rowDiv.innerHTML;
            const table = document.createElement('table');
            table.setAttribute('width', '100%');
            table.setAttribute('border', '0');
            table.setAttribute('cellspacing', '0');
            table.setAttribute('cellpadding', '0');
            table.style.cssText = 'width:100%; border:none; margin-top:25pt; border-collapse:collapse;';
            table.innerHTML = `
                <tr>
                    <td width="50%" style="border:none; padding:0;">&nbsp;</td>
                    <td width="50%" align="center" style="border:none; padding:0; text-align:center;">
                        ${sigHtml}
                    </td>
                </tr>
            `;
            rowDiv.parentNode.replaceChild(table, rowDiv);
        }
    });

    // 5. Ensure Title is centered in Word
    clone.querySelectorAll('h5').forEach(h5 => {
        h5.setAttribute('align', 'center');
        h5.style.textAlign = 'center';
        h5.style.fontSize = '14pt';
        h5.style.fontWeight = 'bold';
        h5.style.margin = '14pt 0 10pt 0';
    });

    const rawNomor = "<?= esc($surat['nomor_surat'] ?? 'Surat') ?>";
    const fileName = "Surat_" + rawNomor.replace(/[\/\\]/g, '_') + ".doc";

    const headerHtml = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'><head><meta charset='utf-8'><title>Export Word</title><!--[if gte mso 9]><xml><w:WordDocument><w:View>Print</w:View><w:Zoom>100</w:Zoom><w:DoNotOptimizeForBrowser/></w:WordDocument></xml><![endif]--><style>@page { size: 210mm 297mm; margin: 18mm 16mm 18mm 16mm; } body { font-family: 'Calibri', 'Arial', sans-serif; font-size: 11pt; line-height: 1.4; color: #0f172a; } table { border-collapse: collapse; margin: 10pt 0; } th, td { border: 1px solid #cbd5e1; padding: 6pt 9pt; font-size: 10pt; } th { background-color: #1e40af; color: #ffffff; font-weight: bold; } p { margin-bottom: 5pt; margin-top: 0; } .accent-top-left, .accent-bottom-right { display: none; }</style></head><body>";
    const footerHtml = "</body></html>";
    const wordTemplate = headerHtml + clone.innerHTML + footerHtml;

    const blob = new Blob(['\ufeff' + wordTemplate], {
        type: 'application/msword;charset=utf-8'
    });

    if (navigator.msSaveOrOpenBlob) {
        navigator.msSaveOrOpenBlob(blob, fileName);
    } else {
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = fileName;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

window.addEventListener('beforeprint', function () {
    const printView = document.getElementById('dedicatedPrintView');
    if (printView && printView.classList.contains('d-none')) {
        proceedToDedicatedPrintView();
    }
});

window.addEventListener('load', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('print_now')) {
        // Clean URL to prevent re-triggering on refresh
        window.history.replaceState({}, document.title, window.location.pathname);
        
        // Show success loading and open print directly
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Tersimpan!',
                text: 'Membuka pratinjau cetak...',
                timer: 1500,
                showConfirmButton: false,
                willClose: () => {
                    proceedToDedicatedPrintView();
                }
            });
        } else {
            proceedToDedicatedPrintView();
        }
    }
});
</script>

<style>
/* ===== Accents ===== */
.accent-top-left { position:absolute !important;top:0;left:0;width:160px;height:160px;pointer-events:none;z-index:1;opacity:0.85; }
.accent-bottom-right { position:absolute !important;bottom:0;right:0;width:160px;height:160px;pointer-events:none;z-index:1;opacity:0.85; }

.letter-paper-render {
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

/* ===== Dedicated Print Preview (screen only) ===== */
#dedicatedPrintView .doc-page-sheet {
    margin: 0 auto 24px auto !important;
    width: 100% !important;
    max-width: 794px !important;
    min-height: 1050px !important;
    padding: 45px 50px 75px 50px !important;
    box-shadow: 0 4px 24px rgba(0,0,0,0.18) !important;
    border-radius: 6px !important;
    box-sizing: border-box !important;
}
#dedicatedPrintPaper .custom-doc-table { table-layout:fixed; }
#dedicatedPrintPaper .custom-doc-table td,
#dedicatedPrintPaper .custom-doc-table th { word-wrap:break-word; overflow-wrap:break-word; }

@media print {
    @page {
        size: A4 portrait;
        margin: 0;
    }
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
    #mainDetailView,
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
        height: 297mm !important;
        min-height: 297mm !important;
        max-height: 297mm !important;
        margin: 0 auto !important;
        padding: 25px 35px 45px 35px !important;
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

<?= $this->include('direktur/templates/footer') ?>
