<?php
// C:\xampp\htdocs\cdwnet\app\Views\admin\manajemen_cuti\export_pdf.php

use Dompdf\Dompdf;
use Dompdf\Options;

// Create PDF options
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Arial');

$dompdf = new Dompdf($options);

// Data from controller
$title = $data['title'] ?? 'Surat Persetujuan Cuti';
$cuti = $data['cuti'] ?? null;
$company = 'CDW ENGINEERING';
$currentDate = date('d F Y');

// HTML content for PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>' . $title . '</title>
    <style>
        body {
            font-family: "Arial", sans-serif;
            font-size: 12px;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            color: #1e3c72;
        }
        
        .document-title {
            font-size: 18px;
            margin: 10px 0;
            color: #333;
        }
        
        .document-subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .info-table td {
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        
        .info-table .label {
            width: 150px;
            background-color: #f5f5f5;
            font-weight: bold;
        }
        
        .detail-section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            background-color: #e9ecef;
            padding: 8px 12px;
            border-left: 4px solid #1e3c72;
            margin-bottom: 15px;
        }
        
        .approval-section {
            margin-top: 50px;
        }
        
        .approval-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .approval-table td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
            height: 100px;
            vertical-align: top;
        }
        
        .signature-line {
            width: 200px;
            border-top: 1px solid #333;
            margin: 60px auto 10px;
        }
        
        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 11px;
        }
        
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @page {
            margin: 20mm;
        }
    </style>
</head>
<body>';

if ($cuti) {
    // Get status badge class
    $statusClass = 'status-pending';
    if (in_array($cuti['status'], ['Disetujui HRD', 'Disetujui Atasan'])) {
        $statusClass = 'status-approved';
    } elseif ($cuti['status'] === 'Ditolak') {
        $statusClass = 'status-rejected';
    }
    
    // Format dates
    $tanggalMulai = date('d F Y', strtotime($cuti['tanggal_mulai']));
    $tanggalSelesai = date('d F Y', strtotime($cuti['tanggal_selesai']));
    $tanggalPengajuan = date('d F Y H:i', strtotime($cuti['tanggal_pengajuan']));
    $disetujuiAt = $cuti['disetujui_at'] ? date('d F Y H:i', strtotime($cuti['disetujui_at'])) : '-';
    
    $html .= '
    <div class="header">
        <p class="company-name">' . $company . '</p>
        <p class="document-title">SURAT PERSETUJUAN CUTI</p>
        <p class="document-subtitle">Human Resource Management System</p>
    </div>
    
    <table class="info-table">
        <tr>
            <td class="label">Nomor Cuti</td>
            <td>' . htmlspecialchars($cuti['nomor_cuti']) . '</td>
        </tr>
        <tr>
            <td class="label">Tanggal Dokumen</td>
            <td>' . $currentDate . '</td>
        </tr>
    </table>
    
    <div class="detail-section">
        <div class="section-title">INFORMASI KARYAWAN</div>
        <table class="info-table">
            <tr>
                <td class="label">NIK</td>
                <td>' . htmlspecialchars($cuti['nik'] ?? '-') . '</td>
                <td class="label">Nama Lengkap</td>
                <td>' . htmlspecialchars($cuti['nama_lengkap'] ?? '-') . '</td>
            </tr>
            <tr>
                <td class="label">Jabatan</td>
                <td>' . htmlspecialchars($cuti['jabatan'] ?? '-') . '</td>
                <td class="label">Departemen</td>
                <td>' . htmlspecialchars($cuti['departemen'] ?? '-') . '</td>
            </tr>
        </table>
    </div>
    
    <div class="detail-section">
        <div class="section-title">DETAIL CUTI</div>
        <table class="info-table">
            <tr>
                <td class="label">Jenis Cuti</td>
                <td>' . htmlspecialchars($cuti['jenis_cuti']) . '</td>
                <td class="label">Status</td>
                <td><span class="status-badge ' . $statusClass . '">' . $cuti['status'] . '</span></td>
            </tr>
            <tr>
                <td class="label">Periode Cuti</td>
                <td colspan="3">' . $tanggalMulai . ' s/d ' . $tanggalSelesai . ' (' . $cuti['lama_hari'] . ' hari kerja)</td>
            </tr>
            <tr>
                <td class="label">Alasan Cuti</td>
                <td colspan="3">' . nl2br(htmlspecialchars($cuti['alasan'])) . '</td>
            </tr>
            <tr>
                <td class="label">Tanggal Pengajuan</td>
                <td>' . $tanggalPengajuan . '</td>
                <td class="label">Disetujui Pada</td>
                <td>' . $disetujuiAt . '</td>
            </tr>
            <tr>
                <td class="label">Disetujui Oleh</td>
                <td colspan="3">' . htmlspecialchars($cuti['disetujui_nama'] ?? '-') . '</td>
            </tr>';
            
    if (!empty($cuti['alasan_penolakan'])) {
        $html .= '
            <tr>
                <td class="label">Alasan Penolakan</td>
                <td colspan="3">' . nl2br(htmlspecialchars($cuti['alasan_penolakan'])) . '</td>
            </tr>';
    }
    
    if (!empty($cuti['sisa_cuti_tahunan'])) {
        $html .= '
            <tr>
                <td class="label">Sisa Cuti Tahunan</td>
                <td colspan="3">' . $cuti['sisa_cuti_tahunan'] . ' hari (pada saat pengajuan)</td>
            </tr>';
    }
    
    $html .= '
        </table>
    </div>
    
    <div class="approval-section">
        <div class="section-title">PERSETUJUAN</div>
        
        <table class="approval-table">
            <tr>
                <td style="width: 33%;">
                    <p><strong>Pemohon</strong></p>
                    <div class="signature-line"></div>
                    <p>' . htmlspecialchars($cuti['nama_lengkap'] ?? '') . '</p>
                    <p>NIK: ' . htmlspecialchars($cuti['nik'] ?? '') . '</p>
                    <p>Tanggal: ' . $tanggalPengajuan . '</p>
                </td>
                <td style="width: 33%;">
                    <p><strong>Human Resource Department</strong></p>
                    <div class="signature-line"></div>
                    <p>HRD Manager</p>
                    <p>CDW Engineering</p>
                    <p>Tanggal: ' . $disetujuiAt . '</p>
                </td>
                <td style="width: 33%;">
                    <p><strong>Atasan Langsung</strong></p>
                    <div class="signature-line"></div>
                    <p>Department Head</p>
                    <p>CDW Engineering</p>
                    <p>Tanggal: ' . $disetujuiAt . '</p>
                </td>
            </tr>
        </table>
    </div>
    
    <div class="footer">
        <p><strong>Catatan:</strong></p>
        <p>1. Dokumen ini sah dan diterbitkan secara digital oleh sistem CDW Engineering HRM</p>
        <p>2. Tidak memerlukan tanda tangan basah untuk validasi</p>
        <p>3. Dokumen ini dapat dipergunakan sebagai bukti pengajuan cuti yang sah</p>
        <p>4. Dicetak pada: ' . $currentDate . ' | Dokumen ID: ' . $cuti['nomor_cuti'] . '</p>
    </div>';
} else {
    $html .= '
    <div style="text-align: center; padding: 50px;">
        <h3>Data Cuti Tidak Ditemukan</h3>
        <p>Data cuti yang diminta tidak tersedia atau telah dihapus.</p>
    </div>';
}

$html .= '
</body>
</html>';

// Load HTML to Dompdf
$dompdf->loadHtml($html);

// Set paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render PDF
$dompdf->render();

// Output PDF
$dompdf->stream('cuti_' . ($cuti['nomor_cuti'] ?? date('Ymd_His')) . '.pdf', [
    'Attachment' => true
]);

exit;