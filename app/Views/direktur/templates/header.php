<?php
// app/Views/direktur/templates/header.php
$title = $title ?? 'CDW Engineering Direktur';
$css = $css ?? [];
$scripts = $scripts ?? [];
?>
<!DOCTYPE html>
<html lang="en">
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
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --cdw-primary: #2a4b8c;
            --cdw-secondary: #5a8dee;
            --cdw-accent: #00b894;
            --cdw-danger: #ff6b6b;
            --cdw-warning: #ffd93d;
            --cdw-info: #6c5ce7;
            --cdw-dark: #2c3e50;
            --cdw-light: #f8f9fa;
            --cdw-gray: #8e9aaf;
            --sidebar-width: 260px;
            --header-height: 70px;
            --shadow-sm: 0 2px 10px rgba(0,0,0,0.08);
            --shadow-md: 0 5px 20px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 30px rgba(0,0,0,0.15);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --border-radius: 12px;
            --border-radius-sm: 8px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7ff 0%, #f0f2f9 100%);
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            color: var(--cdw-dark);
            overflow-x: hidden;
        }
        
        /* Main Layout */
        .app-container {
            display: flex;
            min-height: 100vh;
        }
        
        .main-content {
            flex: 1;
            padding: 0;
            transition: var(--transition);
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            background: #f5f7ff;
        }
        
        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--cdw-secondary);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--cdw-primary);
        }
        
        /* Utility Classes */
        .text-gradient {
            background: linear-gradient(45deg, var(--cdw-primary), var(--cdw-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .bg-gradient-primary {
            background: linear-gradient(135deg, var(--cdw-primary) 0%, var(--cdw-secondary) 100%);
        }
        
        .bg-gradient-accent {
            background: linear-gradient(135deg, var(--cdw-accent) 0%, #00d2d3 100%);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .shadow-hover {
            transition: var(--transition);
        }
        
        .shadow-hover:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }
        
        /* Loading Animation */
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--cdw-primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Pulse Animation */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        /* Cards Modern */
        .modern-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: var(--shadow-sm);
            border: none;
            transition: var(--transition);
            border-left: 4px solid transparent;
        }
        
        .modern-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }
        
        .modern-card-primary {
            border-left-color: var(--cdw-primary);
        }
        
        .modern-card-accent {
            border-left-color: var(--cdw-accent);
        }
        
        .modern-card-danger {
            border-left-color: var(--cdw-danger);
        }
        
        .modern-card-warning {
            border-left-color: var(--cdw-warning);
        }
        
        /* Buttons Modern */
        .btn-modern {
            border: none;
            padding: 10px 24px;
            border-radius: var(--border-radius-sm);
            font-weight: 500;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-modern-primary {
            background: linear-gradient(135deg, var(--cdw-primary), var(--cdw-secondary));
            color: white;
        }
        
        .btn-modern-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            color: white;
        }
        
        .btn-modern-outline {
            background: transparent;
            border: 2px solid var(--cdw-primary);
            color: var(--cdw-primary);
        }
        
        .btn-modern-outline:hover {
            background: var(--cdw-primary);
            color: white;
            transform: translateY(-2px);
        }
        
        /* Forms Modern */
        .form-modern .form-control,
        .form-modern .form-select {
            border-radius: var(--border-radius-sm);
            border: 2px solid #e0e6ed;
            padding: 12px 16px;
            transition: var(--transition);
            font-size: 14px;
        }
        
        .form-modern .form-control:focus,
        .form-modern .form-select:focus {
            border-color: var(--cdw-secondary);
            box-shadow: 0 0 0 3px rgba(90, 141, 238, 0.1);
        }
        
        /* Badge Modern */
        .badge-modern {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 12px;
        }
        
        /* Alert Modern */
        .alert-modern {
            border: none;
            border-radius: var(--border-radius-sm);
            padding: 16px 20px;
            box-shadow: var(--shadow-sm);
        }
        
        /* Table Modern */
        .table-modern {
            background: white;
            border-radius: var(--border-radius-sm);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        
        .table-modern thead th {
            background: linear-gradient(135deg, var(--cdw-primary) 0%, var(--cdw-secondary) 100%);
            color: white;
            border: none;
            padding: 16px;
            font-weight: 600;
        }
        
        .table-modern tbody tr {
            transition: var(--transition);
        }
        
        .table-modern tbody tr:hover {
            background: rgba(90, 141, 238, 0.05);
        }
        
        /* Page Title */
        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
            color: var(--cdw-dark);
        }
        
        .page-subtitle {
            color: var(--cdw-gray);
            font-size: 0.95rem;
            margin-bottom: 20px;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 0;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            .modern-card {
                padding: 20px;
                margin: 15px;
            }
        }
        
        /* Animation Classes */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Notification Badge */
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--cdw-danger);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 1.5s infinite;
        }
        
        /* Custom Tooltip */
        .custom-tooltip {
            position: relative;
        }
        
        .custom-tooltip::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: var(--cdw-dark);
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
            z-index: 1000;
        }
        
        .custom-tooltip:hover::after {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(-5px);
        }
    </style>
    
    <?php
    // Additional CSS files
    if (!empty($css)) {
        foreach ($css as $cssFile) {
            echo '<link rel="stylesheet" href="' . $cssFile . '">' . "\n";
        }
    }
    ?>
</head>
<body>
    <div class="app-container">