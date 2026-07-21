<?php
$title = 'Preview Laporan Jam Kerja';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?> - CDW Engineering</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1e3c72;
            --secondary-color: #2a5298;
            --accent-color: #3498db;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --border-color: #dee2e6;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #333;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            margin-bottom: 25px;
        }
        
        .header-section {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 25px 30px;
            text-align: center;
            position: relative;
        }
        
        .header-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(to right, #3498db, #2ecc71);
        }
        
        .company-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .company-logo i {
            font-size: 32px;
            color: #3498db;
        }
        
        .company-name {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        
        .company-tagline {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 10px;
        }
        
        .report-title {
            font-size: 24px;
            font-weight: 700;
            margin: 15px 0;
        }
        
        .info-section {
            padding: 25px 30px;
            background-color: #f8fafc;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .info-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: var(--shadow);
            border-left: 4px solid var(--primary-color);
        }
        
        .info-card h3 {
            color: var(--primary-color);
            font-size: 16px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-card h3 i {
            color: var(--accent-color);
        }
        
        .info-content {
            font-size: 14px;
            color: #555;
        }
        
        .filter-badge {
            display: inline-block;
            background: #e3f2fd;
            color: var(--primary-color);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin: 5px 5px 5px 0;
            border: 1px solid #bbdefb;
        }
        
        .data-section {
            padding: 0 30px 30px;
        }
        
        .table-responsive {
            overflow-x: auto;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            min-width: 1200px;
        }
        
        thead {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        }
        
        th {
            color: white;
            font-weight: 600;
            padding: 14px 12px;
            text-align: left;
            border: none;
            position: relative;
        }
        
        th:not(:last-child)::after {
            content: '';
            position: absolute;
            right: 0;
            top: 20%;
            height: 60%;
            width: 1px;
            background: rgba(255, 255, 255, 0.3);
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }
        
        tbody tr {
            transition: all 0.2s ease;
        }
        
        tbody tr:hover {
            background-color: #f5f9ff;
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }
        
        tbody tr:nth-child(even) {
            background-color: #fafcff;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .badge-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .total-row {
            background-color: #e8f4ff !important;
            font-weight: 700;
            color: var(--primary-color);
            border-top: 2px solid var(--primary-color);
        }
        
        .total-row td {
            border-bottom: none;
        }
        
        .action-section {
            padding: 25px 30px;
            background-color: #f8fafc;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            min-width: 140px;
        }
        
        .btn-primary {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(30, 60, 114, 0.2);
        }
        
        .btn-success {
            background: linear-gradient(to right, #28a745, #20c997);
            color: white;
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(40, 167, 69, 0.2);
        }
        
        .btn-secondary {
            background: linear-gradient(to right, #6c757d, #495057);
            color: white;
        }
        
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(108, 117, 125, 0.2);
        }
        
        .btn-print {
            background: linear-gradient(to right, #17a2b8, #138496);
            color: white;
        }
        
        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(23, 162, 184, 0.2);
        }
        
        .statistics-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 25px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: var(--shadow);
            text-align: center;
            border-top: 4px solid var(--accent-color);
        }
        
        .stat-value {
            font-size: 28px;
            font-weight: 800;
            color: var(--primary-color);
            margin: 10px 0;
        }
        
        .stat-label {
            font-size: 13px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
            color: #666;
            font-size: 13px;
        }
        
        .no-data {
            text-align: center;
            padding: 50px 20px;
            color: #666;
        }
        
        .no-data i {
            font-size: 48px;
            color: #bdc3c7;
            margin-bottom: 15px;
        }
        
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
            }
            
            .container {
                max-width: 100% !important;
                margin: 0 !important;
            }
            
            .card {
                box-shadow: none !important;
                margin: 0 !important;
            }
            
            .action-section {
                display: none !important;
            }
            
            .btn {
                display: none !important;
            }
            
            tbody tr:hover {
                transform: none !important;
                box-shadow: none !important;
            }
        }
        
        @media (max-width: 768px) {
            .header-section {
                padding: 20px 15px;
            }
            
            .company-name {
                font-size: 22px;
            }
            
            .report-title {
                font-size: 18px;
            }
            
            .info-section, .data-section {
                padding: 20px 15px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .btn {
                min-width: 120px;
                padding: 10px 15px;
            }
            
            th, td {
                padding: 10px 8px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <!-- Header -->
            <div class="header-section">
                <div class="company-logo">
                    <i class="fas fa-building"></i>
                    <div>
                        <div class="company-name">CDW ENGINEERING</div>
                        <div class="company-tagline">Human Resource Management System</div>
                    </div>
                </div>
                <div class="report-title">LAPORAN REKAP JAM KERJA KARYAWAN</div>
                <div style="opacity: 0.9; font-size: 14px;">Preview Laporan | Dicetak: <?= date('d F Y H:i:s'); ?></div>
            </div>
            
            <!-- Information Section -->
            <div class="info-section">
                <div class="info-grid">
                    <div class="info-card">
                        <h3><i class="fas fa-calendar-alt"></i> Periode Laporan</h3>
                        <div class="info-content">
                            <strong><?= date('d F Y', strtotime($filter['start_date'])); ?></strong> hingga 
                            <strong><?= date('d F Y', strtotime($filter['end_date'])); ?></strong><br>
                            <small>Total: <?= floor((strtotime($filter['end_date']) - strtotime($filter['start_date'])) / (60 * 60 * 24)) + 1; ?> hari</small>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <h3><i class="fas fa-users"></i> Statistik Data</h3>
                        <div class="info-content">
                            Total Karyawan: <strong><?= count($summary ?? []); ?> orang</strong><br>
                            <?php if (isset($summary) && !empty($summary)): 
                                $totalHadir = array_sum(array_column($summary, 'hari_hadir'));
                                $totalHari = array_sum(array_column($summary, 'total_hari'));
                                $persentase = $totalHari > 0 ? round(($totalHadir / $totalHari) * 100, 1) : 0;
                            ?>
                            Kehadiran: <strong><?= $persentase; ?>%</strong>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <h3><i class="fas fa-filter"></i> Filter yang Diterapkan</h3>
                        <div class="info-content">
                            <?php if (empty($filter['karyawan_id']) && empty($filter['status']) && empty($filter['departemen'])): ?>
                                <span class="filter-badge">Semua Data</span>
                            <?php else: ?>
                                <?php if (!empty($filter['karyawan_id'])): ?>
                                <span class="filter-badge">Karyawan Spesifik</span>
                                <?php endif; ?>
                                <?php if (!empty($filter['status'])): ?>
                                <span class="filter-badge">Status: <?= $filter['status']; ?></span>
                                <?php endif; ?>
                                <?php if (!empty($filter['departemen'])): ?>
                                <span class="filter-badge">Departemen: <?= $filter['departemen']; ?></span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Statistics -->
                <?php if (isset($summary) && !empty($summary)): 
                    $totalAllHari = array_sum(array_column($summary, 'total_hari'));
                    $totalAllHadir = array_sum(array_column($summary, 'hari_hadir'));
                    $totalAllJamKerja = array_sum(array_column($summary, 'total_jam_kerja'));
                    $totalAllLembur = array_sum(array_column($summary, 'total_lembur'));
                    $totalAllTerlambat = array_sum(array_column($summary, 'total_terlambat'));
                    $rataJamKerja = count($summary) > 0 ? $totalAllJamKerja / count($summary) : 0;
                ?>
                <div class="statistics-section">
                    <div class="stat-card">
                        <div class="stat-label">Total Hari Kerja</div>
                        <div class="stat-value"><?= $totalAllHari; ?></div>
                        <small>Rata: <?= count($summary) > 0 ? number_format($totalAllHari / count($summary), 1) : 0; ?> hari</small>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-label">Total Kehadiran</div>
                        <div class="stat-value"><?= $totalAllHadir; ?></div>
                        <small><?= $totalAllHari > 0 ? round(($totalAllHadir / $totalAllHari) * 100, 1) : 0; ?>% dari total</small>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-label">Total Jam Kerja</div>
                        <div class="stat-value"><?= floor($totalAllJamKerja); ?>:<?= str_pad(round(($totalAllJamKerja - floor($totalAllJamKerja)) * 60), 2, '0', STR_PAD_LEFT); ?></div>
                        <small>Rata: <?= number_format($rataJamKerja, 2); ?> jam</small>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-label">Total Lembur</div>
                        <div class="stat-value"><?= floor($totalAllLembur); ?>:<?= str_pad(round(($totalAllLembur - floor($totalAllLembur)) * 60), 2, '0', STR_PAD_LEFT); ?></div>
                        <small>Rata: <?= count($summary) > 0 ? number_format($totalAllLembur / count($summary), 2) : 0; ?> jam</small>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Data Table -->
            <div class="data-section">
                <h3 style="color: var(--primary-color); margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-table"></i> Detail Jam Kerja Karyawan
                </h3>
                
                <div class="table-responsive">
                    <?php if (isset($summary) && !empty($summary)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th width="40">No</th>
                                <th>NIK</th>
                                <th>Nama Karyawan</th>
                                <th>Jabatan</th>
                                <th>Departemen</th>
                                <th width="80">Total Hari</th>
                                <th width="80">Hadir</th>
                                <th width="100">Total Jam Kerja</th>
                                <th width="100">Rata per Hari</th>
                                <th width="90">Lembur</th>
                                <th width="80">Terlambat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            $totalAllHari = 0;
                            $totalAllHadir = 0;
                            $totalAllJamKerja = 0;
                            $totalAllLembur = 0;
                            $totalAllTerlambat = 0;
                            ?>
                            <?php foreach ($summary as $s): ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                <td><strong><?= esc($s['nik'] ?? '-'); ?></strong></td>
                                <td><?= esc($s['nama_lengkap'] ?? '-'); ?></td>
                                <td><?= esc($s['jabatan'] ?? '-'); ?></td>
                                <td><span class="badge-info status-badge"><?= esc($s['departemen'] ?? '-'); ?></span></td>
                                <td class="text-center"><?= $s['total_hari']; ?></td>
                                <td class="text-center">
                                    <span class="badge-success status-badge"><?= $s['hari_hadir']; ?></span>
                                </td>
                                <td class="text-center">
                                    <?php 
                                    $jam = floor($s['total_jam_kerja']);
                                    $menit = round(($s['total_jam_kerja'] - $jam) * 60);
                                    if ($jam > 0 || $menit > 0) {
                                        echo '<strong>' . $jam . '</strong>j <strong>' . $menit . '</strong>m';
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php 
                                    if ($s['hari_hadir'] > 0) {
                                        $rata = $s['total_jam_kerja'] / $s['hari_hadir'];
                                        $jam = floor($rata);
                                        $menit = round(($rata - $jam) * 60);
                                        echo '<strong>' . $jam . '</strong>j <strong>' . $menit . '</strong>m';
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php 
                                    if ($s['total_lembur'] > 0) {
                                        $jam = floor($s['total_lembur']);
                                        $menit = round(($s['total_lembur'] - $jam) * 60);
                                        echo '<span class="badge-warning status-badge">' . $jam . 'j ' . $menit . 'm</span>';
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($s['total_terlambat'] > 0): ?>
                                        <span class="badge-warning status-badge"><?= $s['total_terlambat']; ?> mnt</span>
                                    <?php else: ?>
                                        <span class="badge-success status-badge">0 mnt</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php 
                            $totalAllHari += $s['total_hari'];
                            $totalAllHadir += $s['hari_hadir'];
                            $totalAllJamKerja += $s['total_jam_kerja'];
                            $totalAllLembur += $s['total_lembur'];
                            $totalAllTerlambat += $s['total_terlambat'];
                            ?>
                            <?php endforeach; ?>
                            
                            <!-- Total Row -->
                            <tr class="total-row">
                                <td colspan="5" class="text-right"><strong>TOTAL SELURUH KARYAWAN:</strong></td>
                                <td class="text-center"><strong><?= $totalAllHari; ?></strong></td>
                                <td class="text-center"><strong><?= $totalAllHadir; ?></strong></td>
                                <td class="text-center">
                                    <strong>
                                    <?php 
                                    $jam = floor($totalAllJamKerja);
                                    $menit = round(($totalAllJamKerja - $jam) * 60);
                                    echo $jam . 'j ' . $menit . 'm';
                                    ?>
                                    </strong>
                                </td>
                                <td class="text-center">
                                    <strong>
                                    <?php 
                                    if ($totalAllHadir > 0) {
                                        $rata = $totalAllJamKerja / $totalAllHadir;
                                        $jam = floor($rata);
                                        $menit = round(($rata - $jam) * 60);
                                        echo $jam . 'j ' . $menit . 'm';
                                    } else {
                                        echo '0j 0m';
                                    }
                                    ?>
                                    </strong>
                                </td>
                                <td class="text-center">
                                    <strong>
                                    <?php 
                                    $jam = floor($totalAllLembur);
                                    $menit = round(($totalAllLembur - $jam) * 60);
                                    echo $jam . 'j ' . $menit . 'm';
                                    ?>
                                    </strong>
                                </td>
                                <td class="text-center"><strong><?= $totalAllTerlambat; ?> mnt</strong></td>
                            </tr>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-clipboard-list"></i>
                        <h3>Tidak Ada Data</h3>
                        <p>Tidak ada data jam kerja yang ditemukan pada periode yang dipilih.</p>
                        <a href="<?= base_url('admin/jam-kerja'); ?>" class="btn btn-secondary" style="margin-top: 15px;">
                            <i class="fas fa-arrow-left"></i> Kembali ke Jam Kerja
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="action-section">
                <a href="<?= base_url('admin/jam-kerja'); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                
                <button onclick="window.print()" class="btn btn-print">
                    <i class="fas fa-print"></i> Cetak Laporan
                </button>
                
                <a href="<?= base_url('admin/jam-kerja/export/excel?start_date=' . $filter['start_date'] . '&end_date=' . $filter['end_date'] . (!empty($filter['karyawan_id']) ? '&karyawan_id=' . $filter['karyawan_id'] : '') . (!empty($filter['status']) ? '&status=' . $filter['status'] : '')); ?>" 
                   class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                
                <a href="<?= base_url('admin/jam-kerja/export/pdf?start_date=' . $filter['start_date'] . '&end_date=' . $filter['end_date'] . (!empty($filter['karyawan_id']) ? '&karyawan_id=' . $filter['karyawan_id'] : '') . (!empty($filter['status']) ? '&status=' . $filter['status'] : '')); ?>" 
                   class="btn btn-primary">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p><strong>CDW ENGINEERING - Human Resource Management System</strong></p>
            <p>Laporan ini dihasilkan secara otomatis oleh sistem. Validitas data: <?= date('d F Y H:i:s'); ?></p>
            <p>&copy; <?= date('Y'); ?> PT. CDW Engineering. Semua hak dilindungi undang-undang.</p>
        </div>
    </div>
    
    <script>
        // Add interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Add animation to table rows
            const tableRows = document.querySelectorAll('tbody tr');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
                });
                
                row.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = 'none';
                });
            });
            
            // Print button functionality
            const printButton = document.querySelector('.btn-print');
            if (printButton) {
                printButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.print();
                });
            }
        });
    </script>
</body>
</html>