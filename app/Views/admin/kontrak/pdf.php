<?php
// C:\xampp\htdocs\cdwnet\app\Views\admin\kontrak\pdf.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
    <style>
        /* Reset CSS untuk PDF */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Helvetica', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
        }
        
        /* Page Setup */
        @page {
            margin: 2cm 1.5cm;
            size: A4 portrait;
        }
        
        /* Header Kontrak */
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #000;
        }
        
        .header h1 {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .header h2 {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .nomor-kontrak {
            font-size: 11pt;
            font-weight: bold;
            margin-top: 8px;
        }
        
        /* Content */
        .content {
            margin-bottom: 20px;
        }
        
        /* Pihak */
        .pihak {
            margin-bottom: 25px;
        }
        
        .pihak h3 {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 8px;
            text-decoration: underline;
        }
        
        .pihak-info {
            margin-left: 15px;
            margin-bottom: 12px;
        }
        
        .pihak-info p {
            margin-bottom: 4px;
        }
        
        /* Pasal */
        .pasal {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        
        .pasal h4 {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 8px;
            text-align: center;
        }
        
        .pasal h5 {
            font-size: 11pt;
            font-weight: bold;
            margin: 8px 0 4px 15px;
        }
        
        .pasal p {
            margin-bottom: 6px;
            text-align: justify;
        }
        
        .pasal ol, .pasal ul {
            margin-left: 30px;
            margin-bottom: 8px;
        }
        
        .pasal li {
            margin-bottom: 4px;
        }
        
        /* Table */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 10pt;
        }
        
        .table th, .table td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: left;
            vertical-align: top;
        }
        
        .table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        
        /* Tanda Tangan */
        .signature {
            margin-top: 40px;
            page-break-inside: avoid;
        }
        
        .signature-table {
            width: 100%;
            border: none;
        }
        
        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
            padding: 0 15px;
        }
        
        .signature-space {
            height: 60px;
            margin-bottom: 5px;
            border-bottom: 1px solid #000;
        }
        
        /* Footer */
        .footer {
            margin-top: 20px;
            font-size: 9pt;
            text-align: center;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 8px;
        }
        
        /* Utility Classes */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .text-underline { text-decoration: underline; }
        .mb-1 { margin-bottom: 4px; }
        .mb-2 { margin-bottom: 8px; }
        .mb-3 { margin-bottom: 12px; }
        .mt-1 { margin-top: 4px; }
        .mt-2 { margin-top: 8px; }
        .mt-3 { margin-top: 12px; }
        
        /* Page breaks */
        .page-break {
            page-break-before: always;
        }
        
        .keep-together {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <!-- Konten sama dengan print.php -->
    <?php include(APPPATH . 'Views/admin/kontrak/print.php'); ?>
</body>
</html>