<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Dashboard Sales') ?> | CDW Engineering</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --sales-primary: #4e73df;
            --sales-secondary: #6f42c1;
            --sales-success: #1cc88a;
            --sales-warning: #f6c23e;
            --sales-danger: #e74a3b;
            --sales-info: #36b9cc;
            --sales-sidebar: #4e73df;
            --sidebar-width: 250px;
        }
        
        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        
        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--sales-sidebar) 0%, #224abe 100%);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        
        .sidebar-brand {
            color: white;
            font-weight: 600;
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-user {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-user-avatar {
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 1rem;
            color: white;
        }
        
        .sidebar-user-name {
            color: white;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .sidebar-user-role {
            color: rgba(255,255,255,0.7);
            font-size: 0.875rem;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.75rem 1.5rem;
            margin: 0.1rem 0;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }
        
        .nav-link:hover, .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            border-left-color: white;
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        .nav-link.has-submenu::after {
            content: '\f107';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            float: right;
            transition: transform 0.3s;
        }
        
        .nav-link.has-submenu.collapsed::after {
            transform: rotate(-90deg);
        }
        
        .submenu {
            background: rgba(0,0,0,0.2);
            padding-left: 1.5rem;
        }
        
        .submenu .nav-link {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            border-left: 2px solid transparent;
        }
        
        .submenu .nav-link:hover, .submenu .nav-link.active {
            border-left-color: rgba(255,255,255,0.5);
        }
        
        /* Main Content */
        .main-content, .content-wrapper {
            margin-left: 260px;
            padding: 20px;
            transition: all 0.3s;
            min-height: 100vh;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                left: -260px;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            .main-content, .content-wrapper {
                margin-left: 0 !important;
            }
        }
        
        /* Navbar */
        .navbar {
            background: white;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            padding: 1rem 1.5rem;
        }
        
        /* Sales Cards */
        .sales-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s;
            border: 1px solid #eaeaea;
        }
        
        .sales-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        /* Stats Cards */
        .stats-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            background: white;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 15px;
        }
        
        .card-icon.blue {
            background: linear-gradient(45deg, var(--sales-primary), #224abe);
        }
        
        .card-icon.green {
            background: linear-gradient(45deg, var(--sales-success), #17a673);
        }
        
        .card-icon.orange {
            background: linear-gradient(45deg, var(--sales-warning), #f6c23e);
        }
        
        .card-icon.purple {
            background: linear-gradient(45deg, #6f42c1, #59339d);
        }
        
        .card-value {
            font-size: 2rem;
            font-weight: 700;
            color: #5a5c69;
            margin-bottom: 5px;
        }
        
        .card-label {
            color: #858796;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        
        /* Table Styling */
        .table-hover tbody tr:hover {
            background-color: rgba(0,0,0,0.02);
        }
        
        /* Progress Bars */
        .progress {
            height: 8px;
            border-radius: 4px;
        }
        
        /* Quick Actions */
        .quick-action-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            display: block;
            text-decoration: none;
            color: #5a5c69;
            transition: all 0.3s;
            height: 100%;
            border: 1px solid #eaeaea;
        }
        
        .quick-action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            text-decoration: none;
            color: #5a5c69;
        }
        
        .quick-action-card i {
            font-size: 2rem;
            margin-bottom: 10px;
            color: var(--sales-primary);
        }
        
        /* Badge Styling */
        .badge {
            padding: 5px 10px;
            font-weight: 500;
            border-radius: 20px;
        }
        
        /* Welcome Card */
        .welcome-card {
            background: linear-gradient(135deg, var(--sales-primary) 0%, #224abe 100%);
            color: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .welcome-card h3 {
            color: white;
            margin-bottom: 10px;
        }
        
        /* Form Styling */
        .form-control, .form-select {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #ddd;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--sales-primary);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        /* Mobile Sidebar Toggle Button */
        .sidebar-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1001;
            border-radius: 50% !important;
            width: 50px;
            height: 50px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        
        @media (min-width: 769px) {
            .sidebar-toggle {
                display: none;
            }
        }
    </style>
    
    <!-- Page specific CSS -->
    <?php if (isset($custom_css)): ?>
        <style><?= $custom_css ?></style>
    <?php endif; ?>
</head>
<body>