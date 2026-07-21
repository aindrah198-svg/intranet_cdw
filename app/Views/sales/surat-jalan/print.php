<?php
// Data
$suratJalan = $suratJalan ?? [];
$items = $items ?? [];

if (empty($suratJalan)) {
    echo '<div class="alert alert-danger">Surat jalan tidak ditemukan!</div>';
    exit;
}

// Helper functions
function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

function formatNumber($number) {
    return number_format($number, 2, ',', '.');
}

// Status text
$statusText = [
    'diproses' => 'DIPROSES',
    'dikirim' => 'DIKIRIM',
    'diterima' => 'DITERIMA',
    'dibatalkan' => 'DIBATALKAN'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan <?= htmlspecialchars($suratJalan['nomor_surat_jalan'] ?? '') ?> - Print</title>
    
    <!-- Bootstrap CSS for better styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
                background: white;
            }
            
            .print-container {
                margin: 0;
                padding: 0;
                width: 100%;
            }
            
            .no-print {
                display: none !important;
            }
            
            .page-break {
                page-break-after: always;
            }
            
            .card {
                border: none !important;
                box-shadow: none !important;
            }
            
            .table {
                border-collapse: collapse !important;
            }
            
            .table th, .table td {
                border: 1px solid #000 !important;
            }
            
            .signature-line {
                border-top: 1px solid #000 !important;
            }
            
            .watermark {
                display: block !important;
            }
        }
        
        @media screen {
            body {
                background-color: #f5f5f5;
                padding: 20px;
            }
            
            .print-container {
                max-width: 21cm;
                margin: 0 auto;
                background: white;
                padding: 2cm;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
            
            .watermark {
                display: none !important;
            }
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #000;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
        }
        
        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #1e3c72;
        }
        
        .company-address {
            font-size: 12px;
            color: #666;
        }
        
        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 25px 0;
            text-transform: uppercase;
        }
        
        .info-label {
            font-weight: bold;
            width: 120px;
        }
        
        table {
            font-size: 11px;
        }
        
        th {
            background-color: #f8f9fa !important;
            font-weight: bold !important;
        }
        
        .signature-section {
            margin-top: 50px;
        }
        
        .signature-box {
            text-align: center;
            padding-top: 40px;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            width: 100%;
            margin-top: 50px;
        }
        
        .signature-name {
            font-weight: bold;
            margin-top: 5px;
        }
        
        .signature-position {
            font-size: 11px;
            color: #666;
        }
        
        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #666;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        
        .status-badge {
            padding: 2px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 10px;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(0,0,0,0.1);
            font-weight: bold;
            z-index: -1;
            white-space: nowrap;
            pointer-events: none;
        }
        
        .print-actions {
            margin-bottom: 20px;
            padding: 15px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .logo {
            font-size: 24px;
            color: #1e3c72;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <!-- Print Actions (only on screen) -->
    <div class="no-print print-actions">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">
                        <i class="fas fa-truck logo"></i>
                        Surat Jalan - Print Preview
                    </h4>
                    <p class="text-muted mb-0">
                        <?= htmlspecialchars($suratJalan['nomor_surat_jalan']) ?>
                    </p>
                </div>
                <div class="btn-group">
                    <a href="<?= base_url('sales/surat-jalan/detail/' . $suratJalan['id']) ?>" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Kembali
                    </a>
                    <a href="<?= base_url('sales/surat-jalan/export-pdf/' . $suratJalan['id']) ?>" 
                       class="btn btn-outline-danger">
                        <i class="fas fa-file-pdf me-2"></i> Download PDF
                    </a>
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="fas fa-print me-2"></i> Print Dokumen
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Watermark -->
    <div class="watermark">SURAT JALAN</div>
    
    <!-- Main Content -->
    <div class="print-container">
        <!-- Header -->
        <div class="header">
            <div class="company-name">PT. CIPTA DUTA WACANA</div>
            <div class="company-address">
                Villa Bintaro Regency | Jl. Riau Blok K1 No. 2 | Pondok Kacang Timur<br>
                Tangerang Selatan 15226 | www.cdw-engineering.com
            </div>
        </div>
        
        <!-- Title -->
        <div class="title">
            SURAT JALAN / DELIVERY NOTE
        </div>
        
        <!-- Document Info -->
        <div class="card mb-3 border-0">
            <div class="card-body p-0">
                <div class="row mb-2">
                    <div class="col-3">
                        <span class="info-label">Nomor</span>
                    </div>
                    <div class="col-9">
                        : <strong><?= htmlspecialchars($suratJalan['nomor_surat_jalan']) ?></strong>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-3">
                        <span class="info-label">Tanggal</span>
                    </div>
                    <div class="col-9">
                        : <?= formatDate($suratJalan['tanggal_kirim']) ?>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-3">
                        <span class="info-label">Status</span>
                    </div>
                    <div class="col-9">
                        : <?= $statusText[$suratJalan['status']] ?? $suratJalan['status'] ?>
                        <span class="status-badge bg-<?= 
                            $suratJalan['status'] == 'diproses' ? 'warning text-dark' : 
                            ($suratJalan['status'] == 'dikirim' ? 'info' : 
                            ($suratJalan['status'] == 'diterima' ? 'success' : 'danger')) 
                        ?>">
                            <?= $suratJalan['status'] ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Project & Client Info -->
        <div class="card mb-3 border-0">
            <div class="card-body p-0">
                <div class="row mb-2">
                    <div class="col-3">
                        <span class="info-label">Project</span>
                    </div>
                    <div class="col-9">
                        : <?= htmlspecialchars($suratJalan['nama_project'] ?? '') ?>
                    </div>
                </div>
                <?php if ($suratJalan['nomor_invoice'] ?? ''): ?>
                <div class="row mb-2">
                    <div class="col-3">
                        <span class="info-label">Invoice</span>
                    </div>
                    <div class="col-9">
                        : <?= htmlspecialchars($suratJalan['nomor_invoice']) ?>
                    </div>
                </div>
                <?php endif; ?>
                <div class="row mb-2">
                    <div class="col-3">
                        <span class="info-label">Client</span>
                    </div>
                    <div class="col-9">
                        : <?= htmlspecialchars($suratJalan['nama_perusahaan'] ?? '') ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recipient Info -->
        <div class="card mb-3 border-0">
            <div class="card-header bg-light p-2">
                <strong>Dikirimkan kepada:</strong>
            </div>
            <div class="card-body p-2">
                <div class="row mb-2">
                    <div class="col-3">
                        <span class="info-label">Nama Penerima</span>
                    </div>
                    <div class="col-9">
                        : <strong><?= htmlspecialchars($suratJalan['penerima']) ?></strong>
                    </div>
                </div>
                <div class="row">
                    <div class="col-3">
                        <span class="info-label">Alamat</span>
                    </div>
                    <div class="col-9">
                        : <?= nl2br(htmlspecialchars($suratJalan['alamat_pengiriman'])) ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Delivery Info -->
        <div class="card mb-4 border-0">
            <div class="card-body p-0">
                <div class="row mb-2">
                    <div class="col-3">
                        <span class="info-label">Sopir</span>
                    </div>
                    <div class="col-9">
                        : <?= htmlspecialchars($suratJalan['sopir'] ?? '-') ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-3">
                        <span class="info-label">No. Kendaraan</span>
                    </div>
                    <div class="col-9">
                        : <?= htmlspecialchars($suratJalan['no_kendaraan'] ?? '-') ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Items Table -->
        <div class="mb-4">
            <h6 class="border-bottom pb-2 mb-3">
                <i class="fas fa-boxes me-2"></i>Barang yang Dikirim
            </h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="45%">Nama Barang</th>
                            <th width="15%" class="text-center">Qty</th>
                            <th width="10%" class="text-center">Satuan</th>
                            <th width="25%">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($items)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-3">
                                    <i class="fas fa-box-open me-2"></i>Tidak ada barang yang dikirim
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($items as $index => $item): ?>
                            <tr>
                                <td class="text-center align-middle"><?= $index + 1 ?></td>
                                <td class="align-middle"><?= htmlspecialchars($item['nama_barang']) ?></td>
                                <td class="text-center align-middle">
                                    <span class="badge bg-primary"><?= formatNumber($item['qty']) ?></span>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge bg-secondary"><?= htmlspecialchars($item['satuan']) ?></span>
                                </td>
                                <td class="align-middle"><?= htmlspecialchars($item['keterangan'] ?? '-') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Additional Notes -->
        <?php if ($suratJalan['keterangan'] ?? ''): ?>
        <div class="card mb-4 border-0">
            <div class="card-header bg-light p-2">
                <strong><i class="fas fa-sticky-note me-2"></i>Keterangan Tambahan:</strong>
            </div>
            <div class="card-body p-2">
                <?= nl2br(htmlspecialchars($suratJalan['keterangan'])) ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Signatures -->
        <div class="signature-section">
            <div class="row">
                <!-- Prepared By -->
                <div class="col-4">
                    <div class="signature-box">
                        <div>Disiapkan oleh,</div>
                        <div class="signature-line"></div>
                        <div class="signature-name"><?= htmlspecialchars($suratJalan['created_by_name'] ?? 'PT. Cipta Duta Wacana') ?></div>
                        <div class="signature-position">PT. Cipta Duta Wacana</div>
                    </div>
                </div>
                
                <!-- Delivered By -->
                <div class="col-4">
                    <div class="signature-box">
                        <div>Dikirim oleh,</div>
                        <div class="signature-line"></div>
                        <div class="signature-name"><?= htmlspecialchars($suratJalan['sopir'] ?? 'Sopir') ?></div>
                        <div class="signature-position">Sopir</div>
                    </div>
                </div>
                
                <!-- Received By -->
                <div class="col-4">
                    <div class="signature-box">
                        <div>Diterima oleh,</div>
                        <div class="signature-line"></div>
                        <div class="signature-name"><?= htmlspecialchars($suratJalan['penerima'] ?? 'Penerima') ?></div>
                        <div class="signature-position"><?= htmlspecialchars($suratJalan['nama_perusahaan'] ?? '') ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <small>
                <i class="fas fa-info-circle me-1"></i>
                Dokumen ini dicetak pada: <?= date('d/m/Y H:i:s') ?> | 
                Halaman 1 dari 1 | 
                <?= htmlspecialchars($suratJalan['nomor_surat_jalan']) ?>
            </small>
        </div>
    </div>
    
    <!-- Print Actions Footer -->
    <div class="no-print mt-4">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-print me-2"></i>Print Instructions
                    </h6>
                    <ul class="mb-0">
                        <li>Gunakan browser Chrome atau Firefox untuk hasil print terbaik</li>
                        <li>Pastikan printer dalam kondisi siap</li>
                        <li>Ukuran kertas: A4</li>
                        <li>Orientasi: Portrait</li>
                        <li>Margin: Default (2cm)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Print Script -->
    <script>
        // Auto print after page load (optional)
        // window.onload = function() {
        //     setTimeout(function() {
        //         window.print();
        //     }, 1000);
        // };
        
        // After print, redirect back
        window.onafterprint = function() {
            // Optional: Redirect or show message
            // window.location.href = "<?= base_url('sales/surat-jalan/detail/' . $suratJalan['id']) ?>";
        };
        
        // Print button handler
        document.addEventListener('keydown', function(e) {
            // Ctrl+P shortcut
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
</body>
</html>