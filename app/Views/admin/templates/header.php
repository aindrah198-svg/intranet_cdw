<?php
// app/Views/admin/templates/header.php
$title = $title ?? 'Admin Panel';
$css = $css ?? [];
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
            /* Tema Admin: Executive Navy Blue (Selaras dengan Direktur) */
            --cdw-primary: #1e3c72;
            --cdw-secondary: #2a5298;
            --cdw-accent: #3b82f6;
            --cdw-dark: #1e293b;
            --sidebar-width: 265px;
            --header-height: 70px;
            --shadow-sm: 0 2px 10px rgba(0,0,0,0.08);
            --shadow-md: 0 5px 20px rgba(0,0,0,0.12);
            --border-radius: 12px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: linear-gradient(135deg, #f5f7ff 0%, #f0f2f9 100%);
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            color: var(--cdw-dark);
            overflow-x: hidden;
        }

        .app-container { display: flex; min-height: 100vh; }

        .sidebar {
            width: var(--sidebar-width, 265px);
            background: linear-gradient(180deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            height: 100vh;
            position: fixed;
            left: 0; top: 0;
            z-index: 1050;
            box-shadow: 3px 0 15px rgba(30,60,114,0.3);
            overflow-y: auto;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar.closed {
            left: -265px !important;
        }

        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width, 265px);
            min-height: 100vh;
            background: #f5f7ff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .main-content.expanded {
            margin-left: 0 !important;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                left: -265px;
            }
            .sidebar.show {
                left: 0 !important;
            }
            .main-content {
                margin-left: 0 !important;
            }
        }

        .content-wrapper { padding: 25px; }

        .welcome-card {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            border-radius: var(--border-radius);
            padding: 30px;
            color: white;
            margin-bottom: 25px;
            box-shadow: 0 10px 25px rgba(30,60,114,0.2);
            position: relative;
            overflow: hidden;
        }
        .welcome-card h3 { font-weight: 700; font-size: 1.3rem; margin-bottom: 4px; }
        .welcome-card p { opacity: 0.85; font-size: 0.9rem; margin: 0; }

        .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 24px; }
        @media (max-width: 992px) { .stats-row { grid-template-columns: repeat(2, 1fr); } .main-content { margin-left: 0; } }
        @media (max-width: 576px)  { .stats-row { grid-template-columns: 1fr; } }

        .dashboard-card {
            background: white; border-radius: var(--border-radius);
            padding: 22px; box-shadow: var(--shadow-sm); transition: all 0.3s;
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
    </style>
</head>
<body>
    <div class="app-container">