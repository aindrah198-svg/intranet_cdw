<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Diagram Flowchart Sistem Direktur - CDW Engineering') ?></title>
    
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

        #superadminWrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
            position: relative;
        }

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
        }

        #superadminContent {
            flex: 1;
            margin-left: 275px;
            width: calc(100% - 275px);
            min-height: 100vh;
            padding: 30px;
        }

        .glass-card {
            background: rgba(30, 41, 59, 0.65);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
        }

        .sidebar-profile-box {
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(99, 102, 241, 0.25);
            border-radius: 18px;
            padding: 16px;
            margin: 16px;
        }

        .superadmin-avatar-sm {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 50%, #ec4899 100%);
            border: 2px solid rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: #ffffff;
            position: relative;
            flex-shrink: 0;
        }

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
            padding: 11px 20px;
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
        }

        .sidebar-nav-item.active {
            color: #ffffff;
            font-weight: 700;
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.25) 0%, rgba(99, 102, 241, 0.05) 100%);
            border-left-color: #6366f1;
        }

        /* ELEGANT FLOWCHART DESIGN */
        .fc-block {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .fc-node-start-end {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 24px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.3);
        }

        .fc-node-start {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            border: 2px solid #34d399;
        }

        .fc-node-end {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #ffffff;
            border: 2px solid #f87171;
            box-shadow: 0 4px 20px rgba(239, 68, 68, 0.3);
        }

        .fc-node-process {
            background: rgba(30, 41, 59, 0.9);
            border: 1.5px solid #6366f1;
            border-left: 5px solid #6366f1;
            border-radius: 14px;
            padding: 14px 18px;
            color: #f8fafc;
            font-size: 0.85rem;
            font-weight: 600;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .fc-node-decision {
            background: rgba(245, 158, 11, 0.12);
            border: 2px solid #f59e0b;
            border-radius: 16px;
            padding: 16px 20px;
            text-align: center;
            color: #fbbf24;
            font-size: 0.85rem;
            font-weight: 800;
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.2);
        }

        .fc-node-doc {
            background: rgba(236, 72, 153, 0.12);
            border: 1.5px solid #ec4899;
            border-radius: 12px 12px 22px 12px;
            padding: 12px 16px;
            color: #f472b6;
            font-size: 0.82rem;
            font-weight: 600;
        }

        .fc-node-io {
            background: rgba(56, 189, 248, 0.12);
            border: 1.5px dashed #38bdf8;
            border-radius: 12px;
            padding: 12px 16px;
            color: #38bdf8;
            font-size: 0.82rem;
            font-weight: 600;
        }

        .flow-line-vertical {
            width: 2px;
            height: 28px;
            background: linear-gradient(180deg, #6366f1, #a855f7);
            margin: 0 auto;
            position: relative;
        }

        .flow-line-vertical::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: -4px;
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 7px solid #a855f7;
        }

        .branch-pill-yes {
            background: #10b981;
            color: #ffffff;
            font-size: 0.7rem;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 20px;
            display: inline-block;
        }

        .branch-pill-no {
            background: #ef4444;
            color: #ffffff;
            font-size: 0.7rem;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 20px;
            display: inline-block;
        }

        @media (max-width: 991.98px) {
            #superadminSidebar { transform: translateX(-100%); }
            #superadminSidebar.show-sidebar { transform: translateX(0); }
            #superadminContent { margin-left: 0; width: 100%; padding: 16px; }
        }
    </style>
</head>
<body>

<div id="superadminWrapper">
    
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSuperadminSidebar()"></div>

    <!-- Sidebar -->
    <aside id="superadminSidebar">
        <div class="p-3 border-bottom border-secondary border-opacity-25 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2.5">
                <div class="bg-indigo-600 p-2 rounded-3 text-white d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: linear-gradient(135deg, #6366f1, #4f46e5);">
                    <i class="fas fa-user-shield fs-5"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-white text-sm">CDW INTRANET</h6>
                    <small class="text-xs text-indigo-300 font-monospace" style="color: #a5b4fc;">SUPERADMIN ROOT</small>
                </div>
            </div>
        </div>

        <div class="sidebar-profile-box d-flex align-items-center gap-3">
            <div class="superadmin-avatar-sm">
                <i class="fas fa-user-ninja"></i>
            </div>
            <div class="overflow-hidden">
                <h6 class="fw-bold text-white text-sm mb-0 text-truncate"><?= esc($user['name']) ?></h6>
                <span class="text-xs text-emerald-400 fw-semibold" style="color: #4ade80;">Root Online</span>
            </div>
        </div>

        <div class="py-2">
            <div class="sidebar-section-title">MENU SUPERADMIN</div>
            <a href="<?= base_url('superadmin') ?>" class="sidebar-nav-item">
                <i class="fas fa-shield-halved"></i> <span>Dashboard</span>
            </a>
            <a href="<?= base_url('superadmin/flow-direktur') ?>" class="sidebar-nav-item active">
                <i class="fas fa-project-diagram text-indigo-400"></i> <span>Flow Sistem Direktur</span>
            </a>
            <a href="<?= base_url('superadmin/flow-admin') ?>" class="sidebar-nav-item">
                <i class="fas fa-sitemap text-emerald-400"></i> <span>Flow Sistem Admin</span>
            </a>
            <a href="<?= base_url('logout') ?>" class="sidebar-nav-item text-danger mt-2">
                <i class="fas fa-power-off text-danger"></i> <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main id="superadminContent">
        <!-- Top Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary border-opacity-25 flex-wrap gap-3">
            <div>
                <h4 class="fw-bold mb-0 text-white"><i class="fas fa-project-diagram text-indigo-400 me-2" style="color: #818cf8;"></i> Flowchart Diagram Alur Sistem Direktur</h4>
                <small class="text-slate-400 text-xs" style="color: #94a3b8;">Struktur Lengkap Berdasarkan Sidebar (`sidebar.php`) & Routes (`DirekturRoutes.php`)</small>
            </div>
            <a href="<?= base_url('direktur') ?>" target="_blank" class="btn btn-outline-light rounded-pill px-4 text-xs font-semibold">
                <i class="fas fa-external-link-alt me-1.5"></i> Buka Portal Direktur
            </a>
        </div>

        <!-- Flowchart Symbols Legend Card -->
        <div class="glass-card p-3 mb-4">
            <small class="fw-bold text-white uppercase d-block mb-2 text-xs"><i class="fas fa-shapes text-indigo-400 me-1.5" style="color: #818cf8;"></i> LEGENDA SIMBOL FLOWCHART STANDAR:</small>
            <div class="d-flex flex-wrap gap-2.5 text-xs">
                <span class="fc-node-start-end fc-node-start py-1 px-3" style="font-size: 0.72rem;"><i class="fas fa-play"></i> START / END (OVAL)</span>
                <span class="fc-node-process py-1 px-3" style="font-size: 0.72rem; border-left-width: 2px;"><i class="fas fa-cog"></i> PROCESS (RECTANGLE)</span>
                <span class="fc-node-decision py-1 px-3" style="font-size: 0.72rem;"><i class="fas fa-question-circle"></i> DECISION (DIAMOND)</span>
                <span class="fc-node-io py-1 px-3" style="font-size: 0.72rem;"><i class="fas fa-database"></i> INPUT/OUTPUT (PARALLELOGRAM)</span>
                <span class="fc-node-doc py-1 px-3" style="font-size: 0.72rem;"><i class="fas fa-file-alt"></i> DOCUMENT (REPORT/SLIP)</span>
            </div>
        </div>

        <!-- 1. DASHBOARD & OVERVIEW FLOWCHART -->
        <div class="fc-block">
            <div class="d-flex align-items-center justify-content-between mb-4 border-bottom border-secondary border-opacity-25 pb-2">
                <h6 class="fw-bold text-white mb-0 text-sm">
                    <i class="fas fa-tachometer-alt text-primary me-2"></i> 1. FLOWCHART UTAMA & EXECUTIVE DASHBOARD (`/direktur`)
                </h6>
                <span class="badge bg-primary bg-opacity-20 text-primary border border-primary px-2.5 py-1 text-xs">Executive Route</span>
            </div>

            <div class="text-center mb-3">
                <div class="fc-node-start-end fc-node-start"><i class="fas fa-power-off"></i> 1. MULAI PORTAL DIREKTUR (`/direktur`)</div>
            </div>

            <div class="flow-line-vertical"></div>

            <div class="row g-3 justify-content-center text-center my-1">
                <div class="col-12 col-md-4">
                    <div class="fc-node-io">
                        <i class="fas fa-chart-pie me-1"></i> Input/Read Data KPI & Stats Graph Real-Time
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="fc-node-process">
                        <i class="fas fa-bell text-warning me-1"></i> Monitoring Pending Approval Center
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="fc-node-doc">
                        <i class="fas fa-file-pdf me-1"></i> Export PDF Executive Summary (`/direktur/dashboard/export-pdf`)
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. KARYAWAN & SDM WORKFLOW FLOWCHART -->
        <div class="fc-block">
            <div class="d-flex align-items-center justify-content-between mb-4 border-bottom border-secondary border-opacity-25 pb-2">
                <h6 class="fw-bold text-white mb-0 text-sm">
                    <i class="fas fa-users text-success me-2"></i> 2. FLOWCHART SUB-MENU: KARYAWAN & SDM (`/direktur/karyawan/*`)
                </h6>
                <span class="badge bg-success bg-opacity-20 text-success border border-success px-2.5 py-1 text-xs">SDM & Approval</span>
            </div>

            <div class="row g-3 justify-content-center align-items-center mb-3">
                <div class="col-12 col-md-5">
                    <div class="fc-node-process">
                        <i class="fas fa-users-cog text-success me-1.5"></i> Kelola Data Karyawan & Akun User (`/direktur/karyawan` & `/akun`)
                    </div>
                </div>
                <div class="col-12 col-md-2 text-center d-none d-md-block">
                    <i class="fas fa-arrow-right text-indigo-400 fs-5"></i>
                </div>
                <div class="col-12 col-md-5">
                    <div class="fc-node-io">
                        <i class="fas fa-user-clock text-info me-1.5"></i> Read Log Monitoring Absensi Harian (`/direktur/karyawan/absensi`)
                    </div>
                </div>
            </div>

            <div class="flow-line-vertical"></div>

            <!-- Decision Box -->
            <div class="row justify-content-center my-2">
                <div class="col-12 col-md-8">
                    <div class="fc-node-decision">
                        <i class="fas fa-question-circle me-1"></i> DECISION: PERMOHONAN IZIN / CUTI / SURAT KARYAWAN?
                    </div>
                </div>
            </div>

            <div class="row g-3 justify-content-center mt-2">
                <div class="col-12 col-md-5">
                    <div class="p-3 rounded-4 bg-emerald-900 bg-opacity-20 border border-emerald-500 border-opacity-30">
                        <div class="mb-2"><span class="branch-pill-yes">YA (DISETUJUI)</span></div>
                        <div class="fc-node-doc text-white border-success">
                            <i class="fas fa-check-circle text-success me-1"></i> Terbitkan Persetujuan (`/approve`), Reduksi Kuota Cuti & Terbitkan Surat
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-5">
                    <div class="p-3 rounded-4 bg-rose-900 bg-opacity-20 border border-rose-500 border-opacity-30">
                        <div class="mb-2"><span class="branch-pill-no">TIDAK (DITOLAK)</span></div>
                        <div class="fc-node-process border-danger text-white">
                            <i class="fas fa-times-circle text-danger me-1"></i> Update Status "Ditolak" (`/reject`) + Catatan Direktur
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. PENUGASAN & PROYEK WORKFLOW FLOWCHART -->
        <div class="fc-block">
            <div class="d-flex align-items-center justify-content-between mb-4 border-bottom border-secondary border-opacity-25 pb-2">
                <h6 class="fw-bold text-white mb-0 text-sm">
                    <i class="fas fa-project-diagram text-info me-2"></i> 3. FLOWCHART SUB-MENU: PENUGASAN & PROYEK (`/direktur/proyek/*` & `/direktur/penugasan/*`)
                </h6>
                <span class="badge bg-info bg-opacity-20 text-info border border-info px-2.5 py-1 text-xs">Project & Tasks</span>
            </div>

            <div class="row g-3 justify-content-center align-items-center mb-3">
                <div class="col-12 col-md-4">
                    <div class="fc-node-process">
                        <i class="fas fa-folder-plus text-primary me-1"></i> Input Project Baru (`/direktur/proyek/baru`)
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="fc-node-process">
                        <i class="fas fa-tasks text-warning me-1"></i> Buat Penugasan Harian & Multi-Checklist (`/direktur/penugasan`)
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="fc-node-io">
                        <i class="fas fa-stream text-info me-1"></i> Timeline Milestone Sync % (`/direktur/proyek/timeline`)
                    </div>
                </div>
            </div>

            <div class="flow-line-vertical"></div>

            <div class="row justify-content-center my-2">
                <div class="col-12 col-md-8">
                    <div class="fc-node-decision">
                        <i class="fas fa-question-circle me-1"></i> DECISION: REVIEW LAPORAN HARIAN SELESAI 100%?
                    </div>
                </div>
            </div>

            <div class="row g-3 justify-content-center mt-2">
                <div class="col-12 col-md-5">
                    <div class="p-3 rounded-4 bg-emerald-900 bg-opacity-20 border border-emerald-500 border-opacity-30">
                        <div class="mb-2"><span class="branch-pill-yes">YA (SELESAI 100%)</span></div>
                        <div class="fc-node-process border-success text-white">
                            <i class="fas fa-archive text-success me-1"></i> Arsipkan Ke Project Selesai (`/direktur/proyek/selesai`)
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-5">
                    <div class="p-3 rounded-4 bg-amber-900 bg-opacity-20 border border-amber-500 border-opacity-30">
                        <div class="mb-2"><span class="branch-pill-no">BELUM (&lt;100%)</span></div>
                        <div class="fc-node-process border-warning text-white">
                            <i class="fas fa-redo text-warning me-1"></i> Monitoring Laporan & Penugasan Pencarian Barang (`/pencarian-barang`)
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. KEUANGAN, PENGADAAN & DOKUMEN WORKFLOW FLOWCHART -->
        <div class="fc-block">
            <div class="d-flex align-items-center justify-content-between mb-4 border-bottom border-secondary border-opacity-25 pb-2">
                <h6 class="fw-bold text-white mb-0 text-sm">
                    <i class="fas fa-wallet text-warning me-2"></i> 4. FLOWCHART SUB-MENU: KEUANGAN, PENGADAAN & DOKUMEN (`/direktur/keuangan/*` & `/pengadaan/*`)
                </h6>
                <span class="badge bg-warning bg-opacity-20 text-warning border border-warning px-2.5 py-1 text-xs">Finance & Assets</span>
            </div>

            <div class="row g-3 justify-content-center align-items-center mb-3">
                <div class="col-12 col-md-4">
                    <div class="fc-node-io">
                        <i class="fas fa-hand-holding-usd me-1"></i> Input Kasbon & Pengajuan ATK / PR Aset
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="fc-node-decision">
                        <i class="fas fa-question-circle me-1"></i> APPROVAL KASBON & PEMBELIAN ASET?
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="fc-node-process border-success">
                        <i class="fas fa-calculator text-success me-1"></i> Payroll Gaji + Potongan Kasbon Bulanan (`/keuangan/penggajian`)
                    </div>
                </div>
            </div>

            <div class="flow-line-vertical"></div>

            <div class="row g-3 justify-content-center align-items-center text-center mt-2">
                <div class="col-12 col-md-5">
                    <div class="fc-node-doc">
                        <i class="fas fa-file-invoice-dollar me-1"></i> Output Slip Gaji, Laporan Keuangan & Monitoring Gudang
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="fc-node-start-end fc-node-end"><i class="fas fa-flag-checkered"></i> END: FINISH DIREKTUR WORKFLOW</div>
                </div>
            </div>
        </div>

    </main>
</div>

</body>
</html>
