<?php
// app/Views/admin_panel/templates/header.php
$title   = $title ?? 'CDW Engineering Admin Panel';
$css     = $css ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - CDW Engineering</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            /* Admin Panel — warna khas merah tua/gelap (berbeda dari HRD biru) */
            --cdw-primary:   #7b1fa2;
            --cdw-secondary: #ab47bc;
            --cdw-accent:    #ff6f00;
            --cdw-danger:    #d32f2f;
            --cdw-warning:   #f57f17;
            --cdw-info:      #0288d1;
            --cdw-dark:      #1a1a2e;
            --cdw-light:     #f3e5f5;
            --cdw-gray:      #9e9e9e;
            --sidebar-width: 265px;
            --header-height: 70px;
            --shadow-sm:     0 2px 10px rgba(0,0,0,0.08);
            --shadow-md:     0 5px 20px rgba(0,0,0,0.12);
            --shadow-lg:     0 10px 30px rgba(0,0,0,0.18);
            --transition:    all 0.3s cubic-bezier(0.4,0,0.2,1);
            --border-radius: 12px;
            --border-radius-sm: 8px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: linear-gradient(135deg, #f8f0ff 0%, #ede7f6 100%);
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            color: var(--cdw-dark);
            overflow-x: hidden;
        }

        .app-container { display: flex; min-height: 100vh; }

        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            background: #f8f0ff;
            transition: var(--transition);
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 7px; height: 7px; }
        ::-webkit-scrollbar-track { background: #ede7f6; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: var(--cdw-secondary); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--cdw-primary); }

        /* Utilities */
        .text-gradient {
            background: linear-gradient(45deg, var(--cdw-primary), var(--cdw-secondary));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .bg-gradient-primary { background: linear-gradient(135deg, var(--cdw-primary) 0%, var(--cdw-secondary) 100%); }
        .glass-effect {
            background: rgba(255,255,255,0.92); backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.4);
        }

        /* Content area */
        .content-wrapper { padding: 25px; }

        /* Page header */
        .page-header {
            background: rgba(255,255,255,0.85); border-radius: var(--border-radius);
            padding: 20px 25px; margin-bottom: 20px;
            box-shadow: var(--shadow-sm); border-left: 4px solid var(--cdw-primary);
        }
        .page-header h2 { font-size: 1.4rem; font-weight: 700; color: var(--cdw-primary); margin-bottom: 2px; }
        .page-header p  { font-size: 0.85rem; color: #777; margin: 0; }

        /* Cards */
        .stat-card {
            background: white; border-radius: var(--border-radius);
            padding: 22px; box-shadow: var(--shadow-sm); transition: var(--transition);
            border: 1px solid rgba(123,31,162,0.08);
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }

        /* Buttons */
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--cdw-primary), var(--cdw-secondary));
            color: white; border: none; border-radius: var(--border-radius-sm);
            padding: 9px 20px; font-size: 0.875rem; font-weight: 500;
            transition: var(--transition); cursor: pointer;
        }
        .btn-primary-custom:hover { opacity: 0.9; transform: translateY(-1px); }

        /* Alert flash messages */
        .alert-flash { position: fixed; top: 80px; right: 20px; z-index: 9999; min-width: 280px; }

        /* Welcome card */
        .welcome-card {
            background: linear-gradient(135deg, var(--cdw-primary) 0%, var(--cdw-secondary) 100%);
            border-radius: var(--border-radius); padding: 28px 30px; margin-bottom: 24px;
            color: white; box-shadow: 0 8px 25px rgba(123,31,162,0.35);
            position: relative; overflow: hidden;
        }
        .welcome-card::after {
            content: ''; position: absolute; right: -30px; top: -30px;
            width: 150px; height: 150px; border-radius: 50%;
            background: rgba(255,255,255,0.08);
        }
        .welcome-card h3 { font-weight: 700; font-size: 1.3rem; margin-bottom: 4px; }
        .welcome-card p  { opacity: 0.85; font-size: 0.9rem; margin: 0; }

        /* Stats row */
        .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 24px; }
        @media (max-width: 992px) { .stats-row { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 576px)  { .stats-row { grid-template-columns: 1fr; } }

        .dashboard-card {
            background: white; border-radius: var(--border-radius);
            padding: 22px; box-shadow: var(--shadow-sm); transition: var(--transition);
        }
        .dashboard-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }

        .card-icon {
            width: 50px; height: 50px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.25rem; margin-bottom: 14px; color: white;
        }
        .card-icon.purple { background: linear-gradient(135deg, #7b1fa2, #ab47bc); }
        .card-icon.orange { background: linear-gradient(135deg, #ef6c00, #ffa726); }
        .card-icon.teal   { background: linear-gradient(135deg, #00796b, #26a69a); }
        .card-icon.red    { background: linear-gradient(135deg, #c62828, #ef5350); }

        .card-value { font-size: 2rem; font-weight: 700; color: var(--cdw-dark); margin-bottom: 2px; }
        .card-label { font-size: 0.82rem; color: #888; margin-bottom: 6px; }

        /* Sidebar toggle mobile */
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; }
        @media (max-width: 992px) { .main-content { margin-left: 0; } }
    </style>

    <?php if (!empty($css)) foreach ($css as $c) echo '<link rel="stylesheet" href="' . $c . '">' . "\n"; ?>
</head>
<body>
    <div class="app-container">
