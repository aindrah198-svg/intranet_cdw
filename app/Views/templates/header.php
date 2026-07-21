<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> | CDW Engineering</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- jQuery untuk counter -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        /* ===== ROOT VARIABLES (ORANGE/KUNING THEME) ===== */
        :root {
            --cdw-orange-primary: #FF6B00;
            --cdw-orange-secondary: #FF8A00;
            --cdw-yellow-accent: #FFC107;
            --cdw-orange-dark: #E55A00;
            --cdw-orange-light: #FFE0B2;
            --cdw-orange-gradient: linear-gradient(135deg, #FF6B00 0%, #FF8A00 50%, #FFC107 100%);
            --cdw-blue: #2196F3;
            --cdw-gray-dark: #333333;
            --cdw-gray-medium: #666666;
            --cdw-gray-light: #f5f5f5;
            --cdw-white: #ffffff;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
            color: var(--cdw-gray-dark);
            background-color: var(--cdw-white);
            line-height: 1.6;
            overflow-x: hidden;
            padding-top: 1px;
        }
        
        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        /* Section Spacing */
        section {
            padding: 100px 0;
            position: relative;
        }
        
        /* Utility Classes */
        .text-orange { color: var(--cdw-orange-primary) !important; }
        .text-yellow { color: var(--cdw-yellow-accent) !important; }
        .bg-orange-light { background-color: var(--cdw-orange-light) !important; }
        .bg-gray-light { background-color: var(--cdw-gray-light) !important; }
        
        .rounded-xl { border-radius: 15px !important; }
        
        .text-gradient {
            background: var(--cdw-orange-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* CTA Button Secondary */
        .btn-cta-secondary {
            background: transparent;
            border: 2px solid var(--cdw-orange-primary);
            border-radius: 8px;
            padding: 10px 25px;
            color: var(--cdw-orange-primary);
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-cta-secondary:hover {
            background: var(--cdw-orange-gradient);
            color: white;
            border-color: transparent;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 107, 0, 0.3);
        }
        
        .btn-cta-secondary.btn-lg {
            padding: 18px 35px;
            font-size: 1.1rem;
            font-weight: 700;
            border-width: 2px;
        }
        
        /* Section Headers */
        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }
        
        .section-title {
            position: relative;
            display: inline-block;
            margin-bottom: 1.5rem;
            color: var(--cdw-gray-dark);
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--cdw-orange-gradient);
            border-radius: 2px;
        }
        
        .section-subtitle {
            color: var(--cdw-gray-medium);
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto;
        }
        
        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
            scroll-padding-top: 100px;
        }
        
        /* Responsive Design */
        @media (max-width: 991.98px) {
            body { padding-top: 75px; }
            section { padding: 80px 0; }
        }
        
        @media (max-width: 767.98px) {
            section { padding: 60px 0; }
        }
    </style>
</head>
<body>
    <!-- NAVBAR AKAN DILOAD DARI templates/nav.php -->