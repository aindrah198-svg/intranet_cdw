<?php

$title = 'Chart of Accounts - Print View';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* Print-specific styles */
        @media print {
            @page {
                margin: 0.5cm;
                size: A4 portrait;
            }
            
            body {
                font-size: 11pt;
                line-height: 1.4;
                color: #000;
                background: #fff;
            }
            
            .no-print {
                display: none !important;
            }
            
            .print-only {
                display: block !important;
            }
            
            .container-fluid {
                padding: 0 !important;
                max-width: 100% !important;
            }
            
            .table {
                font-size: 10pt;
                border-collapse: collapse;
            }
            
            .table th {
                background-color: #f8f9fa !important;
                color: #000 !important;
                font-weight: bold;
                border: 1px solid #dee2e6;
            }
            
            .table td {
                border: 1px solid #dee2e6;
                padding: 4px 8px;
            }
            
            .badge {
                border: 1px solid #000;
                padding: 2px 6px;
                font-size: 9pt;
            }
            
            h1, h2, h3, h4, h5, h6 {
                color: #000;
                margin-bottom: 0.5rem;
            }
            
            .border-bottom {
                border-bottom: 2px solid #000 !important;
            }
            
            .text-primary { color: #000 !important; }
            .text-success { color: #000 !important; }
            .text-danger { color: #000 !important; }
            .text-warning { color: #000 !important; }
            .text-info { color: #000 !important; }
            
            .bg-primary { background-color: #f8f9fa !important; }
            .bg-success { background-color: #f8f9fa !important; }
            .bg-danger { background-color: #f8f9fa !important; }
            .bg-warning { background-color: #f8f9fa !important; }
            .bg-info { background-color: #f8f9fa !important; }
            
            .shadow-sm { box-shadow: none !important; }
            .rounded { border-radius: 0 !important; }
            
            /* Page break avoidance */
            table {
                page-break-inside: auto;
            }
            
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            thead {
                display: table-header-group;
            }
            
            tfoot {
                display: table-footer-group;
            }
        }
        
        /* Screen styles */
        @media screen {
            .print-only {
                display: none !important;
            }
            
            body {
                background-color: #f8f9fa;
                padding: 20px;
            }
            
            .print-container {
                background: white;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 0 20px rgba(0,0,0,0.1);
                max-width: 210mm;
                margin: 0 auto;
            }
        }
        
        /* Common styles */
        .company-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
        }
        
        .company-name {
            font-size: 24pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .company-address {
            font-size: 11pt;
            color: #666;
            margin-bottom: 5px;
        }
        
        .report-title {
            text-align: center;
            margin: 20px 0;
            font-size: 18pt;
            font-weight: bold;
        }
        
        .print-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
            font-size: 10pt;
        }
        
        .statistics {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            font-size: 10pt;
        }
        
        .stat-item {
            text-align: center;
            flex: 1;
        }
        
        .stat-value {
            font-size: 14pt;
            font-weight: bold;
            display: block;
        }
        
        .stat-label {
            font-size: 9pt;
            color: #666;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #dee2e6;
            font-size: 9pt;
            color: #666;
            text-align: center;
        }
        
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            text-align: center;
            width: 200px;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            margin: 40px 0 10px 0;
        }
        
        .badge {
            font-size: 9pt;
            padding: 2px 6px;
            border-radius: 3px;
            display: inline-block;
        }
        
        .hierarchy-indent {
            display: inline-block;
            width: 20px;
        }
        
        /* Color badges for print */
        .badge.bg-primary { background-color: #e3f2fd !important; border: 1px solid #bbdefb; }
        .badge.bg-success { background-color: #e8f5e9 !important; border: 1px solid #c8e6c9; }
        .badge.bg-danger { background-color: #ffebee !important; border: 1px solid #ffcdd2; }
        .badge.bg-warning { background-color: #fff3e0 !important; border: 1px solid #ffe0b2; }
        .badge.bg-info { background-color: #e0f2f1 !important; border: 1px solid #b2dfdb; }
        .badge.bg-secondary { background-color: #f5f5f5 !important; border: 1px solid #e0e0e0; }
    </style>
</head>
<body>
    <!-- Print Controls (Screen Only) -->
    <div class="no-print container mb-4">
        <div class="d-flex justify-content-between align-items-center p-3 bg-white rounded shadow-sm">
            <div>
                <h4 class="mb-0"><i class="fas fa-print me-2"></i> Print Preview</h4>
                <p class="text-muted mb-0">Chart of Accounts - <?= date('d/m/Y H:i:s') ?></p>
            </div>
            <div class="btn-group">
                <button class="btn btn-secondary" onclick="window.history.back()">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </button>
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Cetak
                </button>
                <a href="<?= site_url('accounting/pembukuan/daftar-akun') ?>" class="btn btn-success">
                    <i class="fas fa-list me-1"></i> Daftar Akun
                </a>
            </div>
        </div>
        
        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Tips untuk cetakan terbaik:</strong>
            <ul class="mb-0 mt-2">
                <li>Gunakan browser Chrome atau Firefox untuk hasil terbaik</li>
                <li>Pilih orientasi <strong>Portrait</strong> dan ukuran kertas <strong>A4</strong></li>
                <li>Pastikan margin diatur ke <strong>Default</strong> atau <strong>Minimum</strong></li>
                <li>Centang opsi <strong>"Background graphics"</strong> jika ingin mencetak warna</li>
            </ul>
        </div>
    </div>

    <!-- Print Content -->
    <div class="print-container">
        <!-- Company Header -->
        <div class="company-header">
            <div class="company-name"><?= $company['nama_perusahaan'] ?? 'PT. Cipta Duta Wacana' ?></div>
            <div class="company-address">
                <?= $company['alamat'] ?? 'Villa Bintaro Regency, Jl. Riau Blok K1 No. 2, Pondok Kacang Timur, Tangerang Selatan' ?>
            </div>
            <div class="company-contact">
                <?php if (!empty($company['telepon'])): ?>
                    Telp: <?= $company['telepon'] ?> | 
                <?php endif; ?>
                <?php if (!empty($company['email'])): ?>
                    Email: <?= $company['email'] ?> | 
                <?php endif; ?>
                <?php if (!empty($company['website'])): ?>
                    Website: <?= $company['website'] ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Report Title -->
        <div class="report-title">
            CHART OF ACCOUNTS
        </div>

        <!-- Print Information -->
        <div class="print-info">
            <div>
                <strong>Tanggal Cetak:</strong> <?= date('d/m/Y H:i:s') ?>
            </div>
            <div>
                <strong>Dicetak Oleh:</strong> <?= $printed_by ?>
            </div>
            <div>
                <strong>Halaman:</strong> <span id="pageNumber">1</span> dari <span id="totalPages">1</span>
            </div>
        </div>

        <!-- Statistics Summary -->
        <div class="statistics">
            <div class="stat-item">
                <span class="stat-value"><?= count($coa) ?></span>
                <span class="stat-label">Total Akun</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">
                    <?= count(array_filter($coa, function($a) { return $a['is_active'] == 1; })) ?>
                </span>
                <span class="stat-label">Aktif</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">
                    <?= count(array_filter($coa, function($a) { return $a['is_header'] == 1; })) ?>
                </span>
                <span class="stat-label">Header</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">
                    <?= count(array_filter($coa, function($a) { return $a['is_header'] == 0; })) ?>
                </span>
                <span class="stat-label">Detail</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">
                    <?= count(array_unique(array_column($coa, 'tipe_akun'))) ?>
                </span>
                <span class="stat-label">Tipe Akun</span>
            </div>
        </div>

        <!-- COA Table -->
        <table class="table table-bordered">
            <thead>
                <tr class="table-light">
                    <th width="12%" class="text-center">Kode Akun</th>
                    <th width="30%">Nama Akun</th>
                    <th width="12%" class="text-center">Tipe Akun</th>
                    <th width="12%" class="text-center">Saldo Normal</th>
                    <th width="8%" class="text-center">Level</th>
                    <th width="8%" class="text-center">Jenis</th>
                    <th width="8%" class="text-center">Status</th>
                    <th width="10%" class="text-center">Kategori</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($coa) && is_array($coa)): ?>
                    <?php 
                    $currentType = '';
                    $typeCount = 0;
                    $totalByType = [];
                    ?>
                    
                    <?php foreach ($coa as $index => $account): ?>
                        <?php
                        // Group by tipe akun
                        if ($currentType !== $account['tipe_akun']) {
                            $currentType = $account['tipe_akun'];
                            $typeCount = 0;
                            
                            // Add type header row
                            if ($index > 0) {
                                echo '<tr><td colspan="8" style="padding: 10px 0; background-color: #f8f9fa;"></td></tr>';
                            }
                            
                            $typeColor = [
                                'Aset' => 'primary',
                                'Kewajiban' => 'warning',
                                'Ekuitas' => 'success',
                                'Pendapatan' => 'info',
                                'Beban' => 'danger'
                            ][$currentType] ?? 'secondary';
                            ?>
                            <tr class="table-active">
                                <td colspan="8" style="background-color: #<?= 
                                    $currentType == 'Aset' ? 'e3f2fd' : 
                                    ($currentType == 'Kewajiban' ? 'fff3cd' : 
                                    ($currentType == 'Ekuitas' ? 'd4edda' : 
                                    ($currentType == 'Pendapatan' ? 'd1ecf1' : 'f8d7da'))) 
                                ?>; font-weight: bold; padding: 8px;">
                                    <i class="fas fa-<?= 
                                        $currentType == 'Aset' ? 'building' : 
                                        ($currentType == 'Kewajiban' ? 'hand-holding-usd' : 
                                        ($currentType == 'Ekuitas' ? 'chart-line' : 
                                        ($currentType == 'Pendapatan' ? 'money-bill-wave' : 'receipt'))) 
                                    ?> me-2"></i>
                                    <?= strtoupper($currentType) ?>
                                </td>
                            </tr>
                            <?php
                        }
                        $typeCount++;
                        $totalByType[$currentType] = ($totalByType[$currentType] ?? 0) + 1;
                        
                        // Calculate indent based on level
                        $indent = '';
                        if ($account['level'] > 1) {
                            $indentWidth = ($account['level'] - 1) * 20;
                            $indent = '<span class="hierarchy-indent" style="width: ' . $indentWidth . 'px;"></span>';
                        }
                        ?>
                        
                        <tr>
                            <td class="text-center" style="font-family: 'Courier New', monospace; font-weight: bold;">
                                <?= $indent ?><?= $account['kode_akun'] ?>
                            </td>
                            <td>
                                <?php if ($account['is_header'] == 1): ?>
                                    <strong style="color: #2c3e50;">
                                        <i class="fas fa-folder me-1"></i>
                                        <?= $account['nama_akun'] ?>
                                    </strong>
                                <?php else: ?>
                                    <i class="fas fa-file me-1" style="color: #7f8c8d;"></i>
                                    <?= $account['nama_akun'] ?>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-<?= 
                                    $account['tipe_akun'] == 'Aset' ? 'primary' : 
                                    ($account['tipe_akun'] == 'Kewajiban' ? 'warning' : 
                                    ($account['tipe_akun'] == 'Ekuitas' ? 'success' : 
                                    ($account['tipe_akun'] == 'Pendapatan' ? 'info' : 'danger'))) 
                                ?>">
                                    <?= $account['tipe_akun'] ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-<?= $account['saldo_normal'] == 'Debit' ? 'success' : 'warning' ?>">
                                    <?= $account['saldo_normal'] == 'Debit' ? 'DR' : 'CR' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary">
                                    <?= $account['level'] ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if ($account['is_header'] == 1): ?>
                                    <span class="badge bg-info">Header</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">Detail</span>
                                <?php endif ?>
                            </td>
                            <td class="text-center">
                                <?php if ($account['is_active'] == 1): ?>
                                    <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Nonaktif</span>
                                <?php endif ?>
                            </td>
                            <td class="text-center">
                                <?= $account['kategori'] ?: '-' ?>
                            </td>
                        </tr>
                        
                        <?php 
                        // Check if we need page break (every ~40 rows)
                        if (($index + 1) % 40 === 0 && ($index + 1) < count($coa)): 
                        ?>
                            </tbody>
                        </table>
                        
                        <!-- Page Break -->
                        <div class="page-break"></div>
                        
                        <!-- New page header -->
                        <div class="company-header" style="margin-top: 0;">
                            <div class="company-name"><?= $company['nama_perusahaan'] ?? 'PT. Cipta Duta Wacana' ?></div>
                            <div class="report-title" style="margin: 10px 0; font-size: 14pt;">
                                CHART OF ACCOUNTS (Lanjutan)
                            </div>
                            <div class="print-info" style="margin-bottom: 10px;">
                                <div><strong>Halaman:</strong> <span class="page-counter"><?= ceil(($index + 1) / 40) + 1 ?></span></div>
                                <div><strong>Tanggal:</strong> <?= date('d/m/Y H:i:s') ?></div>
                            </div>
                        </div>
                        
                        <table class="table table-bordered">
                            <thead>
                                <tr class="table-light">
                                    <th width="12%" class="text-center">Kode Akun</th>
                                    <th width="30%">Nama Akun</th>
                                    <th width="12%" class="text-center">Tipe Akun</th>
                                    <th width="12%" class="text-center">Saldo Normal</th>
                                    <th width="8%" class="text-center">Level</th>
                                    <th width="8%" class="text-center">Jenis</th>
                                    <th width="8%" class="text-center">Status</th>
                                    <th width="10%" class="text-center">Kategori</th>
                                </tr>
                            </thead>
                            <tbody>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    
                    <!-- Type Summary -->
                    <tr class="table-light">
                        <td colspan="8" style="padding: 15px 0; background-color: #f8f9fa;"></td>
                    </tr>
                    <tr>
                        <td colspan="8">
                            <div style="font-size: 10pt; color: #666;">
                                <strong>Ringkasan per Tipe Akun:</strong>
                                <?php foreach ($totalByType as $type => $count): ?>
                                    <span class="badge bg-<?= 
                                        $type == 'Aset' ? 'primary' : 
                                        ($type == 'Kewajiban' ? 'warning' : 
                                        ($type == 'Ekuitas' ? 'success' : 
                                        ($type == 'Pendapatan' ? 'info' : 'danger'))) 
                                    ?> me-2">
                                        <?= $type ?>: <?= $count ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>
                    
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-database fa-2x mb-3 d-block" style="color: #ddd;"></i>
                            <strong>Tidak ada data Chart of Accounts</strong>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Summary Information -->
        <div class="mt-4">
            <div class="row">
                <div class="col-6">
                    <div class="card border-0" style="background-color: #f8f9fa;">
                        <div class="card-body p-3">
                            <h6 class="card-title mb-2"><i class="fas fa-info-circle me-2"></i> Informasi</h6>
                            <ul class="mb-0" style="font-size: 9pt;">
                                <li>Total Data: <?= count($coa) ?> akun</li>
                                <li>Akun Aktif: <?= count(array_filter($coa, function($a) { return $a['is_active'] == 1; })) ?></li>
                                <li>Akun Nonaktif: <?= count(array_filter($coa, function($a) { return $a['is_active'] == 0; })) ?></li>
                                <li>Header: <?= count(array_filter($coa, function($a) { return $a['is_header'] == 1; })) ?></li>
                                <li>Detail: <?= count(array_filter($coa, function($a) { return $a['is_header'] == 0; })) ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card border-0" style="background-color: #f8f9fa;">
                        <div class="card-body p-3">
                            <h6 class="card-title mb-2"><i class="fas fa-key me-2"></i> Legenda</h6>
                            <div style="font-size: 9pt;">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="badge bg-primary me-2">Aset</span>
                                    <span class="badge bg-success me-2">DR</span>
                                    <small>Debit</small>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <span class="badge bg-warning me-2">Kewajiban</span>
                                    <span class="badge bg-warning me-2">CR</span>
                                    <small>Kredit</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-info me-2">Header</span>
                                    <span class="badge bg-warning me-2">Detail</span>
                                    <small>Jenis Akun</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer and Signature -->
        <div class="footer">
            <div class="row">
                <div class="col-12 text-center mb-2">
                    <small>
                        Dokumen ini dicetak secara otomatis dari sistem <?= $company['nama_perusahaan'] ?? 'PT. Cipta Duta Wacana' ?>.
                        <br>
                        Tanggal cetak: <?= date('d F Y, H:i:s') ?> | User: <?= $printed_by ?>
                    </small>
                </div>
            </div>
            
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <small>Disiapkan Oleh,</small>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <small>Diperiksa Oleh,</small>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <small>Disetujui Oleh,</small>
                </div>
            </div>
            
            <div class="mt-4 pt-2 border-top text-center">
                <small class="text-muted">
                    <i class="fas fa-file-contract me-1"></i>
                    Dokumen ini adalah bagian dari sistem akuntansi <?= $company['nama_perusahaan'] ?? 'PT. Cipta Duta Wacana' ?>.
                    Untuk informasi lebih lanjut, hubungi departemen akuntansi.
                </small>
            </div>
        </div>
    </div>

    <!-- JavaScript for Print Enhancement -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Update page numbers
        function updatePageNumbers() {
            const totalRows = <?= count($coa) ?>;
            const rowsPerPage = 40;
            const totalPages = Math.ceil(totalRows / rowsPerPage);
            
            document.getElementById('totalPages').textContent = totalPages;
            
            // Update all page counters
            document.querySelectorAll('.page-counter').forEach((el, index) => {
                el.textContent = index + 2;
            });
        }
        
        updatePageNumbers();
        
        // Print button functionality
        document.querySelectorAll('[onclick="window.print()"]').forEach(btn => {
            btn.addEventListener('click', function() {
                // Add print class to body
                document.body.classList.add('printing');
                
                // Show print dialog
                setTimeout(() => {
                    window.print();
                    
                    // Remove print class after print
                    setTimeout(() => {
                        document.body.classList.remove('printing');
                    }, 1000);
                }, 500);
            });
        });
        
        // Handle before print event
        window.addEventListener('beforeprint', function() {
            // Update timestamp
            document.querySelectorAll('.print-timestamp').forEach(el => {
                el.textContent = new Date().toLocaleString('id-ID');
            });
            
            // Update page numbers
            updatePageNumbers();
        });
        
        // Handle after print event
        window.addEventListener('afterprint', function() {
            // Optional: Show confirmation
            if (confirm('Cetakan selesai. Kembali ke halaman daftar akun?')) {
                window.location.href = '<?= site_url("accounting/pembukuan/daftar-akun") ?>';
            }
        });
        
        // Keyboard shortcut for print (Ctrl+P)
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    });
    
    // Function to export as PDF (placeholder)
    function exportToPDF() {
        alert('Fitur ekspor PDF akan tersedia dalam versi mendatang.');
        // In a real implementation, you would use a library like jsPDF or html2pdf
    }
    
    // Function to export as Excel
    function exportToExcel() {
        alert('Fitur ekspor Excel akan tersedia dalam versi mendatang.');
        // In a real implementation, you would use a library like SheetJS
    }
    </script>

    <!-- Print-only watermark -->
    <div class="print-only" style="position: fixed; bottom: 10px; right: 10px; font-size: 8pt; color: #ccc; z-index: 9999;">
        <?= $company['nama_perusahaan'] ?? 'PT. Cipta Duta Wacana' ?> - Chart of Accounts
    </div>
</body>
</html>