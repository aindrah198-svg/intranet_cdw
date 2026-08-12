<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Software Engineer Panel - CDW Intranet' ?></title>
    <!-- Google Fonts Inter & Fira Code -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --se-sidebar-width: 260px;
            --se-bg-dark: #0f172a;
            --se-card-bg: #ffffff;
            --se-primary: #0284c7;
            --se-cyan: #38bdf8;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            margin: 0;
            padding: 0;
        }
        .main-content {
            margin-left: var(--se-sidebar-width);
            padding: 2rem;
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        .code-font {
            font-family: 'Fira Code', monospace;
        }
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            background: #ffffff;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card-custom:hover {
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.08);
        }
        .badge-cyan {
            background-color: #e0f2fe;
            color: #0369a1;
        }
        .hover-glow:hover {
            background: rgba(255,255,255,0.08) !important;
        }
        @media (max-width: 991.98px) {
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            #sidebar {
                left: -260px;
                transition: left 0.3s ease;
            }
            #sidebar.show {
                left: 0;
            }
        }
    </style>
</head>
<body>

<?= view('software_engineer/templates/sidebar', ['active' => $active ?? '', 'sub' => $sub ?? '']) ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h4 class="fw-bold mb-1 text-dark d-flex align-items-center">
                <i class="fas fa-laptop-code text-primary me-2"></i> <?= $title ?? 'Software Engineer Panel' ?>
            </h4>
            <small class="text-muted">CDW Intranet & System Operations Management</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">
                <i class="fas fa-check-circle me-1"></i> System Status: Operational
            </span>
        </div>
    </div>

    <!-- Flash Alerts -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
