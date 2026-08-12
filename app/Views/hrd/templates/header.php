<?php
// app/Views/hrd/templates/header.php
$title = $title ?? 'CDW Engineering HRD';
$css = $css ?? [];
$scripts = $scripts ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - CDW Engineering Intranet</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --cdw-primary: #1e3c72;
            --cdw-secondary: #2a5298;
            --cdw-accent: #4dabf7;
            --cdw-danger: #ff6b6b;
            --cdw-warning: #ffd93d;
            --cdw-info: #6c5ce7;
            --cdw-dark: #2c3e50;
            --cdw-light: #f8f9fa;
            --sidebar-width: 260px;
            --header-height: 70px;
            --shadow-sm: 0 2px 10px rgba(0,0,0,0.08);
            --shadow-md: 0 5px 20px rgba(0,0,0,0.1);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --border-radius: 12px;
            --border-radius-sm: 8px;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            background: linear-gradient(135deg, #f5f7ff 0%, #eef2f9 100%);
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
            background: #f8faee;
            transition: var(--transition);
        }

        .welcome-card {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            border-radius: var(--border-radius);
            padding: 26px 30px;
            margin-bottom: 24px;
            color: white;
            box-shadow: 0 8px 25px rgba(30,60,114,0.3);
        }
        .welcome-card h3 { font-weight: 700; margin-bottom: 4px; font-size: 1.3rem; }
        .welcome-card p { opacity: 0.85; margin: 0; font-size: 0.9rem; }

        .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 24px; }
        @media (max-width: 992px) { .stats-row { grid-template-columns: repeat(2, 1fr); } .main-content { margin-left: 0; } }
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
        .card-icon.blue   { background: linear-gradient(135deg, #1e3c72, #2a5298); }
        .card-icon.green  { background: linear-gradient(135deg, #00b894, #00cec9); }
        .card-icon.orange { background: linear-gradient(135deg, #e17055, #fab1a0); }
        .card-icon.purple { background: linear-gradient(135deg, #6c5ce7, #a29bfe); }

        .card-value { font-size: 2rem; font-weight: 700; color: var(--cdw-dark); margin-bottom: 2px; }
        .card-label { font-size: 0.82rem; color: #888; margin-bottom: 6px; }
    </style>
    
    <?php if (!empty($css)) foreach ($css as $c) echo '<link rel="stylesheet" href="' . $c . '">' . "\n"; ?>
</head>
<body>
    <div class="app-container">
