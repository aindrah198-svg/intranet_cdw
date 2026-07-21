<?php
// app/Views/accounting/templates/header.php
$title = $title ?? 'CDW Engineering Accounting';
$css = $css ?? [];
$scripts = $scripts ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title><?= htmlspecialchars($title) ?> - CDW Engineering Intranet</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Accounting Specific CSS -->
    <style>
        :root {
            --accounting-primary: #2c5aa0;
            --accounting-secondary: #4c7bd9;
            --accounting-accent: #28a745;
            --accounting-warning: #ffc107;
            --accounting-danger: #dc3545;
            --accounting-info: #17a2b8;
            --accounting-success: #28a745;
            --sidebar-width: 260px;
            --header-height: 70px;
            --shadow-sm: 0 2px 10px rgba(0,0,0,0.08);
            --shadow-md: 0 5px 20px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 30px rgba(0,0,0,0.15);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --border-radius: 12px;
            --border-radius-sm: 8px;
        }

        /* Financial Stats Colors */
        .bg-success { background-color: var(--accounting-success) !important; }
        .bg-danger { background-color: var(--accounting-danger) !important; }
        .bg-info { background-color: var(--accounting-info) !important; }
        .bg-warning { background-color: var(--accounting-warning) !important; }

        .text-success { color: var(--accounting-success) !important; }
        .text-danger { color: var(--accounting-danger) !important; }
        .text-info { color: var(--accounting-info) !important; }
        .text-warning { color: var(--accounting-warning) !important; }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%);
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            color: #2c3e50;
            overflow-x: hidden;
        }
        
        /* Accounting Theme Colors */
        .bg-accounting-primary {
            background-color: var(--accounting-primary) !important;
        }
        
        .bg-accounting-secondary {
            background-color: var(--accounting-secondary) !important;
        }
        
        .text-accounting-primary {
            color: var(--accounting-primary) !important;
        }
        
        .text-accounting-secondary {
            color: var(--accounting-secondary) !important;
        }
        
        .border-accounting-primary {
            border-color: var(--accounting-primary) !important;
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
            background: #f8fafc;
        }
        
        /* Financial Cards */
        .financial-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: var(--shadow-sm);
            border: none;
            transition: var(--transition);
            border-left: 5px solid var(--accounting-primary);
        }
        
        .financial-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }
        
        .financial-card-income {
            border-left-color: var(--accounting-success);
        }
        
        .financial-card-expense {
            border-left-color: var(--accounting-danger);
        }
        
        .financial-card-asset {
            border-left-color: var(--accounting-info);
        }
        
        .financial-card-liability {
            border-left-color: var(--accounting-warning);
        }
        
        /* Accounting Tables */
        .accounting-table {
            background: white;
            border-radius: var(--border-radius-sm);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        
        .accounting-table thead th {
            background: linear-gradient(135deg, var(--accounting-primary) 0%, var(--accounting-secondary) 100%);
            color: white;
            border: none;
            padding: 16px;
            font-weight: 600;
        }
        
        .accounting-table tbody tr {
            transition: var(--transition);
        }
        
        .accounting-table tbody tr:hover {
            background: rgba(76, 123, 217, 0.05);
        }
        
        /* Debit/Credit Colors */
        .debit-amount {
            color: var(--accounting-danger);
            font-weight: 600;
        }
        
        .credit-amount {
            color: var(--accounting-success);
            font-weight: 600;
        }
        
        .balance-positive {
            color: var(--accounting-success);
            font-weight: 600;
        }
        
        .balance-negative {
            color: var(--accounting-danger);
            font-weight: 600;
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
            background: var(--accounting-secondary);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--accounting-primary);
        }
        
        /* Utility Classes */
        .text-gradient-accounting {
            background: linear-gradient(45deg, var(--accounting-primary), var(--accounting-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .bg-gradient-accounting {
            background: linear-gradient(135deg, var(--accounting-primary) 0%, var(--accounting-secondary) 100%);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 0;
            }
            
            .financial-card {
                padding: 20px;
                margin: 15px;
            }
        }
        
        /* Print Styles for Financial Reports */
        @media print {
            .no-print {
                display: none !important;
            }
            
            .main-content {
                margin-left: 0 !important;
                padding: 0 !important;
            }
            
            .financial-card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
                break-inside: avoid;
            }
        }
        
        /* Loading Animation */
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--accounting-primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Animation Classes */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Page Title */
        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
            color: var(--accounting-primary);
        }
        
        .page-subtitle {
            color: #6c757d;
            font-size: 0.95rem;
            margin-bottom: 20px;
        }
        
        /* Accounting Specific Components */
        .journal-entry {
            background: white;
            border-left: 4px solid var(--accounting-primary);
            padding: 15px;
            margin-bottom: 15px;
            border-radius: var(--border-radius-sm);
            box-shadow: var(--shadow-sm);
        }
        
        .coa-code {
            font-family: 'Courier New', monospace;
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 600;
            color: var(--accounting-primary);
        }
        
        .account-balance {
            font-size: 1.2rem;
            font-weight: 700;
            padding: 10px 15px;
            background: #f8f9fa;
            border-radius: var(--border-radius-sm);
            border: 2px solid #dee2e6;
        }
        
        /* Button Styles */
        .btn-accounting {
            background: linear-gradient(135deg, var(--accounting-primary), var(--accounting-secondary));
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: var(--border-radius-sm);
            font-weight: 500;
            transition: var(--transition);
        }
        
        .btn-accounting:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            color: white;
        }
        
        .btn-accounting-outline {
            background: transparent;
            border: 2px solid var(--accounting-primary);
            color: var(--accounting-primary);
        }
        
        .btn-accounting-outline:hover {
            background: var(--accounting-primary);
            color: white;
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