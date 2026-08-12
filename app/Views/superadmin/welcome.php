<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Selamat Datang Superadmin - CDW Engineering') ?></title>
    
    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Plus Jakarta Sans', sans-serif;
            box-sizing: border-box;
        }

        body {
            background: radial-gradient(circle at 15% 15%, #0f172a 0%, #1e1b4b 50%, #090d16 100%);
            min-height: 100vh;
            color: #f8fafc;
            overflow-x: hidden;
            margin: 0;
            padding: 0;
        }

        /* Layout Structure */
        #superadminWrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
            position: relative;
        }

        /* Sidebar Styling */
        #superadminSidebar {
            width: 275px;
            min-width: 275px;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1050;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(99, 102, 241, 0.3) transparent;
        }

        #superadminSidebar::-webkit-scrollbar {
            width: 5px;
        }
        #superadminSidebar::-webkit-scrollbar-thumb {
            background: rgba(99, 102, 241, 0.4);
            border-radius: 10px;
        }

        /* Main Content Wrapper */
        #superadminContent {
            flex: 1;
            margin-left: 275px;
            width: calc(100% - 275px);
            min-height: 100vh;
            padding: 30px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Glassmorphism Cards */
        .glass-card {
            background: rgba(30, 41, 59, 0.65);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-card:hover {
            transform: translateY(-4px);
            border-color: rgba(99, 102, 241, 0.4);
            box-shadow: 0 25px 60px rgba(99, 102, 241, 0.15);
        }

        /* Sidebar Profile Card */
        .sidebar-profile-box {
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(99, 102, 241, 0.25);
            border-radius: 18px;
            padding: 16px;
            margin: 16px 16px 20px 16px;
        }

        .superadmin-avatar-sm {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 50%, #ec4899 100%);
            border: 2px solid rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: #ffffff;
            position: relative;
            flex-shrink: 0;
        }

        .superadmin-badge-crown-sm {
            position: absolute;
            top: -6px;
            right: -4px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #ffffff;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            border: 1px solid #0f172a;
        }

        .superadmin-avatar-glow {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 50%, #ec4899 100%);
            border: 3px solid rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            box-shadow: 0 0 35px rgba(99, 102, 241, 0.6), inset 0 0 15px rgba(255, 255, 255, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.8rem;
            color: #ffffff;
            position: relative;
        }

        .superadmin-badge-crown {
            position: absolute;
            top: -8px;
            right: -4px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #ffffff;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            border: 2px solid #0f172a;
            box-shadow: 0 4px 10px rgba(245, 158, 11, 0.5);
        }

        /* Sidebar Navigation Items */
        .sidebar-section-title {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #94a3b8;
            padding: 12px 20px 6px 20px;
        }

        .sidebar-nav-item {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            color: #cbd5e1;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
        }

        .sidebar-nav-item i {
            width: 24px;
            font-size: 0.95rem;
            margin-right: 10px;
            text-align: center;
            color: #818cf8;
            transition: color 0.2s ease;
        }

        .sidebar-nav-item:hover {
            color: #ffffff;
            background: rgba(99, 102, 241, 0.15);
            border-left-color: #6366f1;
        }

        .sidebar-nav-item:hover i {
            color: #a5b4fc;
        }

        .sidebar-nav-item.active {
            color: #ffffff;
            font-weight: 700;
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.25) 0%, rgba(99, 102, 241, 0.05) 100%);
            border-left-color: #6366f1;
        }

        .sidebar-nav-item.active i {
            color: #818cf8;
        }

        /* Pulse Dots & Buttons */
        .pulse-dot {
            width: 9px;
            height: 9px;
            background-color: #22c55e;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 10px #22c55e;
            animation: pulse-animation 2s infinite;
        }

        @keyframes pulse-animation {
            0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
            70% { box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); }
            100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }

        .btn-superadmin-gradient {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: #ffffff;
            border: none;
            border-radius: 30px;
            padding: 12px 28px;
            font-weight: 700;
            letter-spacing: 0.3px;
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
            transition: all 0.3s ease;
        }

        .btn-superadmin-gradient:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(99, 102, 241, 0.5);
        }

        .btn-pdf-gradient {
            background: linear-gradient(135deg, #ec4899 0%, #ef4444 100%);
            color: #ffffff;
            border: none;
            border-radius: 30px;
            padding: 12px 28px;
            font-weight: 700;
            letter-spacing: 0.3px;
            box-shadow: 0 10px 25px rgba(236, 72, 153, 0.4);
            transition: all 0.3s ease;
        }

        .btn-pdf-gradient:hover {
            background: linear-gradient(135deg, #db2777 0%, #dc2626 100%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(236, 72, 153, 0.5);
        }

        .btn-logout-danger {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 30px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-logout-danger:hover {
            background: #ef4444;
            color: #ffffff;
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.4);
        }

        .stat-box {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 18px;
            padding: 18px;
        }

        .wa-preview-box {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(34, 197, 94, 0.4);
            border-radius: 16px;
            padding: 16px;
            font-family: monospace;
            font-size: 0.8rem;
            color: #4ade80;
            white-space: pre-wrap;
        }

        /* Mobile Header & Overlay */
        .mobile-header-bar {
            display: none;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 12px 20px;
            position: sticky;
            top: 0;
            z-index: 1040;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            z-index: 1045;
        }

        @media (max-width: 991.98px) {
            #superadminSidebar {
                transform: translateX(-100%);
            }
            #superadminSidebar.show-sidebar {
                transform: translateX(0);
            }
            #superadminContent {
                margin-left: 0;
                width: 100%;
                padding: 16px;
            }
            .mobile-header-bar {
                display: flex;
            }
            .sidebar-overlay.show-overlay {
                display: block;
            }
        }
    </style>
</head>
<body>

<div id="superadminWrapper">
    
    <!-- Mobile Backdrop Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSuperadminSidebar()"></div>

    <!-- 1. Superadmin Glassmorphism Sidebar -->
    <aside id="superadminSidebar">
        <!-- Sidebar Brand Logo -->
        <div class="p-3 border-bottom border-secondary border-opacity-25 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2.5">
                <div class="bg-indigo-600 p-2 rounded-3 text-white d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: linear-gradient(135deg, #6366f1, #4f46e5);">
                    <i class="fas fa-user-shield fs-5"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-white text-sm" style="letter-spacing: 0.5px;">CDW INTRANET</h6>
                    <small class="text-xs text-indigo-300 font-monospace" style="color: #a5b4fc;">SUPERADMIN ROOT</small>
                </div>
            </div>
            <button type="button" class="btn text-white p-1 d-lg-none" onclick="toggleSuperadminSidebar()">
                <i class="fas fa-times fs-5"></i>
            </button>
        </div>

        <!-- Sidebar Profile Box -->
        <div class="sidebar-profile-box d-flex align-items-center gap-3">
            <div class="superadmin-avatar-sm">
                <i class="fas fa-user-ninja"></i>
                <div class="superadmin-badge-crown-sm" title="Root Access">
                    <i class="fas fa-crown"></i>
                </div>
            </div>
            <div class="overflow-hidden">
                <h6 class="fw-bold text-white text-sm mb-0 text-truncate"><?= esc($user['name']) ?></h6>
                <div class="d-flex align-items-center gap-1.5 mt-0.5">
                    <span class="pulse-dot"></span>
                    <span class="text-xs text-emerald-400 fw-semibold" style="color: #4ade80;">Root Online</span>
                </div>
            </div>
        </div>

        <!-- Sidebar Navigation Links -->
        <div class="py-2">
            <div class="sidebar-section-title">MENU SUPERADMIN</div>
            <a href="<?= base_url('superadmin') ?>" class="sidebar-nav-item <?= ($activeNav ?? '') === 'dashboard' || empty($activeNav) ? 'active' : '' ?>">
                <i class="fas fa-shield-halved"></i> <span>Dashboard</span>
            </a>
            <a href="<?= base_url('superadmin/flow-direktur') ?>" class="sidebar-nav-item <?= ($activeNav ?? '') === 'flow-direktur' ? 'active' : '' ?>">
                <i class="fas fa-project-diagram text-indigo-400"></i> <span>Flow Sistem Direktur</span>
            </a>
            <a href="<?= base_url('superadmin/flow-admin') ?>" class="sidebar-nav-item <?= ($activeNav ?? '') === 'flow-admin' ? 'active' : '' ?>">
                <i class="fas fa-sitemap text-emerald-400"></i> <span>Flow Sistem Admin</span>
            </a>
            <a href="<?= base_url('logout') ?>" class="sidebar-nav-item text-danger mt-2">
                <i class="fas fa-power-off text-danger"></i> <span>Logout</span>
            </a>
        </div>

        <div class="mt-auto p-3 border-top border-secondary border-opacity-25 text-center">
            <small class="text-slate-400 text-xs d-block" style="color: #94a3b8;">CDW Intranet v2.5 Root Access</small>
        </div>
    </aside>

    <!-- 2. Main Page Content Container -->
    <main id="superadminContent">

        <!-- Mobile Header Bar -->
        <div class="mobile-header-bar justify-content-between align-items-center rounded-4 mb-4">
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-dark btn-sm rounded-circle p-2" onclick="toggleSuperadminSidebar()">
                    <i class="fas fa-bars fs-6"></i>
                </button>
                <h6 class="fw-bold text-white mb-0 text-sm">Superadmin Portal</h6>
            </div>
            <a href="<?= base_url('superadmin/cetak-pdf') ?>" target="_blank" class="btn btn-pdf-gradient text-decoration-none text-xs py-1.5 px-3">
                <i class="fas fa-file-pdf me-1"></i> PDF
            </a>
        </div>

        <!-- Top Header Navigation Bar (Desktop) -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary border-opacity-25 flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <button type="button" class="btn btn-outline-light btn-sm rounded-circle p-2.5 d-none d-lg-flex" onclick="toggleSuperadminSidebar()" title="Toggle Sidebar">
                    <i class="fas fa-outdent fs-6"></i>
                </button>
                <div>
                    <h5 class="fw-bold mb-0 text-white">CDW ENGINEERING INTRANET</h5>
                    <small class="text-slate-400 text-xs" style="color: #94a3b8;">Super Administrator Root Access Portal</small>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= base_url('superadmin/cetak-pdf') ?>" target="_blank" class="btn btn-pdf-gradient text-decoration-none text-xs">
                    <i class="fas fa-file-pdf me-1.5"></i> Cetak PDF Fitur Direktur & Admin
                </a>
                <a href="<?= base_url('logout') ?>" class="btn btn-logout-danger text-decoration-none text-xs">
                    <i class="fas fa-power-off me-1.5"></i> Logout
                </a>
            </div>
        </div>

        <!-- Alert Flash Notification -->
        <?php if (session()->getFlashdata('success')): ?>
        <div class="alert bg-indigo-900 border border-indigo-500 text-white rounded-4 shadow-lg p-3.5 mb-4 d-flex align-items-center gap-3" style="background: rgba(99, 102, 241, 0.2);">
            <i class="fas fa-check-circle fs-4 text-indigo-400"></i>
            <div>
                <h6 class="fw-bold mb-0 text-sm">Autentikasi Berhasil!</h6>
                <small class="text-slate-300 text-xs"><?= esc(session()->getFlashdata('success')) ?></small>
            </div>
        </div>
        <?php endif; ?>

        <!-- Main Hero Welcome Card -->
        <div class="glass-card p-4 p-md-5 mb-5 text-center position-relative">
            <div class="d-inline-block mb-4">
                <div class="superadmin-avatar-glow mx-auto">
                    <i class="fas fa-user-ninja"></i>
                    <div class="superadmin-badge-crown" title="Root Superadmin Access">
                        <i class="fas fa-crown"></i>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <span class="badge bg-indigo-500 bg-opacity-20 text-indigo-300 border border-indigo-500 border-opacity-30 px-3.5 py-1.5 rounded-pill text-xs fw-bold mb-3 d-inline-flex align-items-center gap-2" style="color: #c7d2fe;">
                    <span class="pulse-dot"></span> ROOT HAK AKSES TERTINGGI AKTIF
                </span>
                <h2 class="fw-extrabold text-white display-6 mb-2">Selamat Datang, <?= esc($user['name']) ?>!</h2>
                <p class="text-slate-300 mx-auto text-sm" style="max-width: 680px; color: #cbd5e1;">
                    Anda telah masuk sebagai <strong>Superadmin</strong>. Seluruh fitur pada modul Direktur dan Admin telah siap digunakan & disinkronkan. Gunakan sidebar kiri atau tombol di bawah untuk menavigasi portal.
                </p>
            </div>

            <!-- Quick System Stats Row -->
            <div class="row g-3 justify-content-center max-w-4xl mx-auto mb-4">
                <div class="col-6 col-md-4">
                    <div class="stat-box text-center">
                        <small class="text-slate-400 text-xs uppercase font-semibold d-block mb-1" style="color: #94a3b8;">Total Akun Pengguna</small>
                        <div class="fs-4 fw-bold text-white"><?= $totalUsers ?> Akun</div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="stat-box text-center">
                        <small class="text-slate-400 text-xs uppercase font-semibold d-block mb-1" style="color: #94a3b8;">Total Data Karyawan</small>
                        <div class="fs-4 fw-bold text-white"><?= $totalKaryawan ?> Personel</div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="stat-box text-center">
                        <small class="text-slate-400 text-xs uppercase font-semibold d-block mb-1" style="color: #94a3b8;">Total Proyek Terdaftar</small>
                        <div class="fs-4 fw-bold text-white"><?= $totalProyek ?> Proyek</div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-center gap-3 flex-wrap pt-2">
                <a href="<?= base_url('superadmin/cetak-pdf') ?>" target="_blank" class="btn btn-pdf-gradient text-decoration-none text-sm">
                    <i class="fas fa-file-pdf me-2"></i> Cetak PDF Ringkasan Fitur
                </a>
                <button type="button" onclick="copyWaText()" class="btn btn-success rounded-pill px-4 py-2.5 font-semibold text-sm shadow-sm">
                    <i class="fab fa-whatsapp me-2"></i> Salin Ringkasan WA
                </button>
                <a href="<?= base_url('admin') ?>" class="btn btn-superadmin-gradient text-decoration-none text-sm">
                    <i class="fas fa-columns me-2"></i> Dashboard Admin
                </a>
                <a href="<?= base_url('direktur') ?>" class="btn btn-outline-light rounded-pill px-4 py-2.5 font-semibold text-sm">
                    <i class="fas fa-chart-line me-2"></i> Dashboard Direktur
                </a>
            </div>
        </div>

        <!-- WhatsApp Summary Box Card -->
        <div class="glass-card p-4 mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h6 class="fw-bold text-white mb-0"><i class="fab fa-whatsapp text-success me-2 fs-5"></i> Ringkasan Format Kirim WhatsApp (Siap Salin)</h6>
                <button type="button" onclick="copyWaText()" class="btn btn-sm btn-outline-success rounded-pill px-3 font-semibold text-xs">
                    <i class="fas fa-copy me-1"></i> Salin Teks WA
                </button>
            </div>
            <div class="wa-preview-box" id="waBoxText">REKAPITULASI DOKUMENTASI FITUR & FUNGSI SISTEM INTRANET CDW

Tanggal Update: <?= date('d F Y H:i') ?> WIB
Oleh: Superadmin System Access

==================================================
1. FITUR & FUNGSI SISI DIREKTUR (EXECUTIVE DASHBOARD)
==================================================

[ Dashboard ]
- Dashboard: Overview KPI eksekutif, statistik absensi, proyek aktif, & approval pending.

[ Karyawan & SDM ]
- Kelola Karyawan: Manajemen data induk SDM, NIK, jabatan, & profil staf.
- Kelola Akun Karyawan: Pembuatan & pengaturan akun login pengguna intranet.
- Surat (Kontrak/SP): Penerbitan & arsip surat kontrak kerja serta Surat Peringatan.
- Permohonan & Izin: Persetujuan permohonan izin operasional & kedinasan karyawan.
- Cuti Karyawan: Kelola cuti dengan sistem kalkulasi kuota otomatis & pengembalian kuota dinamis.
- Keluhan Karyawan: Pemantauan & penanganan tanggapan aspirasi/keluhan staf.
- Monitoring Absensi: Rekapitulasi log jam masuk/pulang & kehadiran karyawan real-time.

[ Penugasan & Proyek ]
- Penugasan Harian: Pendelegasian tugas ke staf via form dedicated tanpa modal + multi-item checklist.
- Laporan Kerja Harian: Pengawasan & review laporan hasil kerja harian karyawan.
- Monitoring Laporan: Pemantauan rekapitulasi status laporan harian staf secara menyeluruh.
- Project Baru: Inisiasi & pendaftaran proyek baru perusahaan.
- Timeline Kerja: Pengelolaan jadwal milestone harian/mingguan proyek & progres %.
- Project Selesai: Arsip & rekapitulasi proyek yang telah diselesaikan.
- Penugasan Pencarian: Pendelegasian tugas pencarian barang/material proyek.

[ Keuangan ]
- Penggajian Karyawan: Pengawasan payroll, rincian komponen gaji, & cetak slip gaji.
- Kasbon: Approval & pencatatan pengajuan pinjaman/kasbon karyawan.
- Laporan Keuangan: Rekapitulasi transaksi keuangan & arus kas operasional.

[ Pengadaan & Aset ]
- Pengajuan ATK: Persetujuan & pengawasan permintaan Alat Tulis Kantor.
- Monitoring Stok ATK: Kontrol sisa stok inventaris ATK secara real-time.
- Pengadaan Aset: Persetujuan pembelian & pencatatan aset inventaris perusahaan.
- Pencatatan & Tracking Pembelian (PR): Tracking status Purchase Request & barang masuk.
- Kerusakan Alat: Laporan & penanganan kerusakan alat/fasilitas operasional.
- Monitoring Gudang: Pengawasan keluar-masuk stok barang & material gudang.

[ Dokumen ]
- Dokumen Penting: Penyimpanan & pengelolaan arsip dokumen legalitas perusahaan.
- Dokumen Sertifikat: Pengarsipan sertifikat perusahaan, lisensi, & keahlian.
- Kontak Project: Direktori data kontak klien, vendor, & stakeholder proyek.

==================================================
2. FITUR & FUNGSI SISI ADMIN (ADMIN PANEL)
==================================================

[ Dashboard ]
- Dashboard: Monitoring aktivitas harian admin, tugas pending, & ringkasan operasional.

[ Administrasi ]
- Surat Menyurat: Pengelolaan arsip surat masuk, surat keluar, & surat jalan operasional.

[ Pengadaan & Aset ]
- Pengajuan ATK: Pembuatan & pengajuan kebutuhan Alat Tulis Kantor.
- Monitoring Stok ATK: Pengecekan sisa stok ATK gudang operasional.
- Pengadaan Aset: Penginputan & pencatatan barang aset kantor baru.
- Pencatatan & Tracking Pembelian (PR): Input pengajuan Purchase Request & tracking barang.
- Kerusakan Alat: Pelaporan unit peralatan operasional yang mengalami kerusakan.
- Monitoring Gudang: Pencatatan inventarisasi keluar-masuk fisik barang di gudang.

[ Dokumen ]
- Dokumen Penting: Akses & pengarsipan berkas dokumen penting kantor.
- Dokumen Sertifikat: Pengarsipan berkas sertifikat & dokumen perizinan.
- Kontak Project: Pencatatan nomor kontak klien, supplier, & mitra proyek.

[ Fasilitas & Tamu ]
- Buku Tamu: Pencatatan identitas & tujuan kunjungan tamu perusahaan.
- Booking Ruang Meeting: Reservasi jadwal pemakaian ruang rapat/meeting kantor.
- Koordinasi Kendaraan: Pengaturan jadwal & pemakaian kendaraan operasional kantor.

[ Pengajuan ]
- Pengajuan: Penginputan form pengajuan operasional umum staf.
- Cuti: Pengajuan permohonan cuti mandiri admin ke pimpinan.
- Kasbon: Pengajuan pinjaman/kasbon staf & karyawan yang terhubung real-time ke Direktur (http://localhost:8080/direktur/keuangan/kasbon).
- Keluhan: Pelaporan kendala atau keluhan operasional kerja yang terhubung real-time ke Direktur (http://localhost:8080/direktur/karyawan/keluhan).

[ Laporan & Keluhan ]
- Laporan Kerja Harian: Full CRUD laporan kerja harian + konversi otomatis tugas selesai ke form pratinjau/edit.
- Keluhan: Pelaporan & pemantauan keluhan karyawan terhubung real-time dengan Direktur (http://localhost:8080/direktur/karyawan/keluhan).
- Slip Gaji: Pemeriksaan & pencetakan slip gaji bulanan mandiri admin.

[ Menu Pribadi ]
- Absensi: Log absensi masuk & pulang mandiri (fix route) yang terhubung real-time ke Direktur.
- Tugas Hari Ini: Pengerjaan tugas dari Direktur dengan update sub-item checklist (1 per 1 / sekaligus) & validasi 100% selesai untuk kunci laporan.
- Timeline Kerja: Pemantauan & update milestone proyek yang sinkron 100% dengan Direktur.
- Project Saat Ini: Monitoring status & rincian proyek berjalan yang sedang ditangani.
- Profil: Pengaturan data diri, tampilan glassmorphism, & password strength meter.

==================================================
3. MODUL SELANJUTNYA (UPCOMING MODULES)
==================================================
- Modul HRD (Dalam tahap pengembangan selanjutnya)
- Modul Accounting / Akuntansi (Dalam tahap pengembangan selanjutnya)
- Modul Staff / Karyawan General (Dalam tahap pengembangan selanjutnya)
- Modul Teknisi (Dalam tahap pengembangan selanjutnya)

==================================================
4. KREDENSIAL LOGIN SUPERADMIN
==================================================
- URL Login: http://localhost:8080/login
- Username: superadmin
- Password: superadminpw123

==================================================
STATUS SISTEM: 100% Aktif, Berfungsi Normal & Terintegrasi Sempurna.
==================================================</div>
        </div>

        <!-- System Features Matrix -->
        <div class="mb-4">
            <h5 class="fw-bold text-white mb-3"><i class="fas fa-cubes text-indigo-400 me-2" style="color: #818cf8;"></i> Modul Fitur Khusus Superadmin</h5>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="glass-card p-4 h-100">
                        <div class="text-indigo-400 mb-3" style="color: #a5b4fc;"><i class="fas fa-users-cog fa-2x"></i></div>
                        <h6 class="fw-bold text-white mb-2">Manajemen Akun Global</h6>
                        <small class="text-slate-400 text-xs d-block" style="color: #94a3b8;">Kontrol penuh atas pembuatan akun, reset password instan, dan penetapan role pengguna.</small>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="glass-card p-4 h-100">
                        <div class="text-indigo-400 mb-3" style="color: #38bdf8;"><i class="fas fa-shield-cat fa-2x"></i></div>
                        <h6 class="fw-bold text-white mb-2">Audit Sentinel Log</h6>
                        <small class="text-slate-400 text-xs d-block" style="color: #94a3b8;">Memantau riwayat login, alamat IP, serta jejak audit keamanan dari seluruh staf dan manajemen.</small>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="glass-card p-4 h-100">
                        <div class="text-indigo-400 mb-3" style="color: #4ade80;"><i class="fas fa-database fa-2x"></i></div>
                        <h6 class="fw-bold text-white mb-2">Backup & Database Engine</h6>
                        <small class="text-slate-400 text-xs d-block" style="color: #94a3b8;">Manajemen pencadangan otomatis database MySQL dan optimasi tabel intranet.</small>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="glass-card p-4 h-100">
                        <div class="text-indigo-400 mb-3" style="color: #f472b6;"><i class="fas fa-sliders-h fa-2x"></i></div>
                        <h6 class="fw-bold text-white mb-2">Konfigurasi Sistem Root</h6>
                        <small class="text-slate-400 text-xs d-block" style="color: #94a3b8;">Pengaturan variabel lingkungan, batas ukuran upload file, dan konfigurasi portal perusahaan.</small>
                    </div>
                </div>
            </div>
        </div>

    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSuperadminSidebar() {
    const sidebar = document.getElementById('superadminSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.toggle('show-sidebar');
    overlay.classList.toggle('show-overlay');
}

function copyWaText() {
    const text = document.getElementById('waBoxText').innerText;
    navigator.clipboard.writeText(text).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil Disalin!',
            text: 'Teks ringkasan format WhatsApp berhasil disalin ke clipboard. Silakan tempel (paste) di WhatsApp!',
            timer: 3000,
            showConfirmButton: false,
            customClass: { popup: 'rounded-4 shadow-lg' }
        });
    });
}
</script>
</body>
</html>
