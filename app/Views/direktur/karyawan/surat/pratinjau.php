<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Pratinjau Cetak Surat') ?></title>
    <!-- FontAwesome & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        body {
            background-color: #0f172a;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            overflow-y: auto;
            color: #f8fafc;
        }

        /* Top Action Bar (Screen Only) */
        .preview-action-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 65px;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            z-index: 9999;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .preview-container {
            margin-top: 85px;
            margin-bottom: 50px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 24px;
        }

        /* Base Page Sheet Render */
        .doc-page-sheet {
            position: relative !important;
            background: #ffffff !important;
            width: 794px !important;
            min-height: 1120px !important;
            margin: 0 auto 30px auto !important;
            padding: 45px 50px 75px 50px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4) !important;
            border-radius: 4px !important;
            box-sizing: border-box !important;
            color: #0f172a !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .accent-top-left { position:absolute !important;top:0;left:0;width:160px;height:160px;pointer-events:none;z-index:1;opacity:0.85; }
        .accent-bottom-right { position:absolute !important;bottom:0;right:0;width:160px;height:160px;pointer-events:none;z-index:1;opacity:0.85; }

        /* Custom Table Styling inside Sheet */
        .custom-doc-table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin: 14px 0 !important;
            font-size: 0.88rem !important;
        }
        .custom-doc-table th, .custom-doc-table td {
            border: 1px solid #cbd5e1 !important;
            padding: 8px 12px !important;
            text-align: left !important;
            word-wrap: break-word !important;
        }
        .custom-doc-table th {
            background-color: #1e3c72 !important;
            color: #ffffff !important;
            font-weight: 600 !important;
        }

        /* ===== PRINT MEDIA STYLING (Strict A4 1-to-1) ===== */
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
            .no-print,
            .preview-action-bar,
            #ci-debug-bar,
            .ci-debug-bar,
            .ci-debug-bar-tab,
            #toolbarContainer,
            div[id*="debug"],
            div[class*="debug"],
            div[style*="z-index: 99999"] {
                display: none !important;
                visibility: hidden !important;
                height: 0 !important;
                width: 0 !important;
                opacity: 0 !important;
            }
            .preview-container {
                margin: 0 !important;
                padding: 0 !important;
                gap: 0 !important;
                display: block !important;
            }
            .doc-page-sheet {
                position: relative !important;
                width: 210mm !important;
                max-width: 210mm !important;
                height: 297mm !important;
                min-height: 297mm !important;
                max-height: 297mm !important;
                margin: 0 !important;
                padding: 25px 35px 35px 35px !important;
                box-sizing: border-box !important;
                background: #ffffff !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                page-break-before: auto !important;
                break-before: auto !important;
                page-break-after: always !important;
                break-after: page !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                overflow: hidden !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .doc-page-sheet:last-child {
                page-break-after: auto !important;
                break-after: auto !important;
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
                display: none !important;
                visibility: hidden !important;
            }
        }
    </style>
</head>
<body>

    <!-- Top Action Bar -->
    <div class="preview-action-bar no-print">
        <div class="d-flex align-items-center gap-3">
            <a href="<?= base_url('direktur/karyawan/surat/detail/' . $surat['id']) ?>" class="btn btn-outline-light btn-sm rounded-pill px-3 fw-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali ke Detail
            </a>
            <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill">
                <i class="fas fa-file-invoice me-1"></i> Mode Pratinjau Standalone
            </span>
        </div>

        <div class="d-flex align-items-center gap-2 text-white">
            <span class="text-muted text-xs me-2 d-none d-md-inline">
                Nomor: <strong><?= esc($surat['nomor_surat'] ?? '-') ?></strong>
            </span>
            <button type="button" class="btn btn-warning btn-sm rounded-pill px-4 py-2 fw-bold shadow" onclick="window.print()">
                <i class="fas fa-print me-1.5"></i> Cetak Sekarang / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Document Sheets Container -->
    <div class="preview-container">
        <?php if (!empty($surat['html_full'])): ?>
            <?= $surat['html_full'] ?>
        <?php else: ?>
            <div class="doc-page-sheet text-center py-5 text-muted">
                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                <p>Dokumen tidak memiliki data pratinjau lengkap.</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Auto open print dialog if print_now parameter exists
        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('print_now')) {
                window.history.replaceState({}, document.title, window.location.pathname);
                setTimeout(function() {
                    window.print();
                }, 400);
            }
        });
    </script>
</body>
</html>
