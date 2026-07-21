<?php
$session = session();
$errors  = $session->getFlashdata('errors');
$success = $session->getFlashdata('success');
$error   = $session->getFlashdata('error');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - CDW Engineering</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0f172a;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        body::before {
            content: '';
            position: fixed;
            top: -200px; right: -200px;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(59,130,246,0.15) 0%, transparent 70%);
            pointer-events: none;
        }

        .forgot-card {
            background: #ffffff;
            border-radius: 20px;
            width: 100%;
            max-width: 420px;
            overflow: hidden;
            position: relative;
            z-index: 1;
            animation: fadeUp 0.4s ease-out;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Header */
        .card-header-custom {
            padding: 36px 32px 36px;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 60%, #3b82f6 100%);
            position: relative;
        }
        .card-header-custom::after {
            content: '';
            position: absolute;
            bottom: -1px; left: 0; right: 0;
            height: 28px;
            background: #ffffff;
            border-radius: 28px 28px 0 0;
        }
        .logo-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 22px;
        }
        .logo-box {
            width: 46px; height: 46px;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .logo-box i { font-size: 20px; color: white; }
        .logo-name { color: white; }
        .logo-name .brand  { font-size: 17px; font-weight: 700; letter-spacing: 0.5px; line-height: 1.2; }
        .logo-name .tagline{ font-size: 11px; opacity: 0.7; letter-spacing: 2px; text-transform: uppercase; }
        .header-title { color: white; font-size: 22px; font-weight: 600; margin: 0 0 4px; }
        .header-sub   { color: rgba(255,255,255,0.7); font-size: 13px; margin: 0; }

        /* Body */
        .card-body-custom { padding: 6px 32px 28px; }

        .info-box {
            background: #eff6ff;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }
        .info-box i { color: #3b82f6; font-size: 15px; margin-top: 1px; flex-shrink: 0; }
        .info-box p { margin: 0; font-size: 13px; color: #1e40af; line-height: 1.5; }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 7px;
        }
        .input-icon-wrap { position: relative; }
        .input-icon-wrap .fi {
            position: absolute; left: 14px; top: 50%;
            transform: translateY(-50%);
            color: #9ca3af; font-size: 14px; pointer-events: none;
        }
        .input-icon-wrap .form-control {
            padding: 11px 14px 11px 40px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px; color: #111827;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .input-icon-wrap .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
            outline: none;
        }

        .btn-submit {
            width: 100%; padding: 13px;
            background: #1e3a8a; color: white;
            border: none; border-radius: 10px;
            font-size: 15px; font-weight: 600; letter-spacing: 0.3px;
            cursor: pointer; transition: background 0.2s;
        }
        .btn-submit:hover { background: #1e40af; }

        .back-link {
            display: block; text-align: center;
            font-size: 13px; color: #6b7280;
            text-decoration: none; margin-top: 16px;
            transition: color 0.2s;
        }
        .back-link:hover { color: #3b82f6; }

        /* Footer */
        .card-footer-custom {
            padding: 16px 32px 22px;
            border-top: 1px solid #f5f5f5;
            text-align: center;
        }
        .card-footer-custom .copy { font-size: 11px; color: #d1d5db; }

        /* Alert */
        .alert {
            border-radius: 10px; border: none;
            font-size: 13px; padding: 10px 14px; margin-bottom: 16px;
        }
        .alert-success { background: #f0fdf4; color: #166534; }
        .alert-danger  { background: #fef2f2; color: #991b1b; }
    </style>
</head>
<body>

<div class="forgot-card">

    <!-- Header -->
    <div class="card-header-custom">
        <div class="logo-row">
            <div class="logo-box">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="logo-name">
                <div class="brand">CDW Engineering</div>
                <div class="tagline">Management System</div>
            </div>
        </div>
        <h1 class="header-title">Reset Password</h1>
        <p class="header-sub">Masukkan email terdaftar untuk memulai proses reset</p>
    </div>

    <!-- Body -->
    <div class="card-body-custom">

        <div class="info-box">
            <i class="fas fa-circle-info"></i>
            <p>Kami akan mengirimkan tautan reset password ke alamat email yang terdaftar di sistem.</p>
        </div>

        <!-- Alerts -->
        <?php if($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-1"></i><?= esc($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <?php if($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-1"></i><?= esc($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <?php if($errors): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-1"></i>
            <ul class="mb-0 ps-3 mt-1">
            <?php foreach($errors as $e): ?>
                <li><?= esc($e) ?></li>
            <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <!-- Form -->
        <form action="<?= base_url('forgot-password/send') ?>" method="POST" id="forgotForm">
            <?= csrf_field() ?>

            <div class="mb-4">
                <label for="email" class="form-label">Alamat Email</label>
                <div class="input-icon-wrap">
                    <i class="fas fa-envelope fi"></i>
                    <input type="email"
                           class="form-control"
                           id="email"
                           name="email"
                           value="<?= old('email') ?>"
                           placeholder="Masukkan email terdaftar"
                           required
                           autocomplete="email"
                           autofocus>
                </div>
            </div>

            <button type="submit" class="btn-submit" id="submitBtn">
                <i class="fas fa-paper-plane me-2"></i>Kirim Instruksi Reset
            </button>
        </form>

        <a href="<?= base_url('login') ?>" class="back-link">
            <i class="fas fa-arrow-left me-1"></i>Kembali ke halaman login
        </a>
    </div>

    <!-- Footer -->
    <div class="card-footer-custom">
        <div class="copy">&copy; <?= date('Y') ?> CDW Engineering. Hak Cipta Dilindungi.</div>
    </div>

</div><!-- .forgot-card -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form      = document.getElementById('forgotForm');
    const submitBtn = document.getElementById('submitBtn');

    if (form && submitBtn) {
        form.addEventListener('submit', function () {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...';
        });
    }
});
</script>
</body>
</html>