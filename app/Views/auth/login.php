<?php
$session = session();
$errors = $session->getFlashdata('errors');
$success = $session->getFlashdata('success');
$error = $session->getFlashdata('error');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CDW Engineering</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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

        /* Background decoration */
        body::before {
            content: '';
            position: fixed;
            top: -200px;
            right: -200px;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(59,130,246,0.15) 0%, transparent 70%);
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            bottom: -200px;
            left: -200px;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(139,92,246,0.12) 0%, transparent 70%);
            pointer-events: none;
        }

        .login-card {
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

        /* ── Header ── */
        .card-header-custom {
            padding: 36px 32px 36px;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 60%, #3b82f6 100%);
            position: relative;
        }
        .card-header-custom::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
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
            width: 46px;
            height: 46px;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .logo-box i {
            font-size: 20px;
            color: white;
        }
        .logo-name {
            color: white;
        }
        .logo-name .brand {
            font-size: 17px;
            font-weight: 700;
            letter-spacing: 0.5px;
            line-height: 1.2;
        }
        .logo-name .tagline {
            font-size: 11px;
            opacity: 0.7;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .header-title {
            color: white;
            font-size: 22px;
            font-weight: 600;
            margin: 0 0 4px;
        }
        .header-sub {
            color: rgba(255,255,255,0.7);
            font-size: 13px;
            margin: 0;
        }

        /* ── Body ── */
        .card-body-custom {
            padding: 6px 32px 28px;
        }

        .secure-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 11px;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 20px;
            margin-bottom: 20px;
            letter-spacing: 0.3px;
        }
        .secure-badge i { font-size: 10px; }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 7px;
        }

        .input-icon-wrap {
            position: relative;
        }
        .input-icon-wrap .fi {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 14px;
            pointer-events: none;
        }
        .input-icon-wrap .form-control {
            padding: 11px 14px 11px 40px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            color: #111827;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .input-icon-wrap .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
            outline: none;
        }
        .input-icon-wrap .form-control.is-invalid {
            border-color: #ef4444;
        }
        .input-icon-wrap .btn-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            padding: 4px 6px;
            cursor: pointer;
            line-height: 1;
            font-size: 14px;
        }
        .input-icon-wrap .btn-toggle:hover { color: #3b82f6; }

        .invalid-feedback { font-size: 12px; }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: #1e3a8a;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 0.3px;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
        }
        .btn-login:hover { background: #1e40af; }
        .btn-login:active { transform: scale(0.99); }
        .btn-login:disabled { background: #93c5fd; cursor: not-allowed; }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 18px 0 16px;
        }
        .divider hr {
            flex: 1;
            border: none;
            border-top: 1px solid #f0f0f0;
            margin: 0;
        }
        .divider span {
            font-size: 12px;
            color: #9ca3af;
            white-space: nowrap;
        }

        .link-forgot {
            display: block;
            text-align: center;
            font-size: 13px;
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
        }
        .link-forgot:hover { text-decoration: underline; color: #2563eb; }

        /* ── Footer ── */
        .card-footer-custom {
            padding: 16px 32px 22px;
            border-top: 1px solid #f5f5f5;
            text-align: center;
        }
        .card-footer-custom a {
            font-size: 13px;
            color: #6b7280;
            text-decoration: none;
            transition: color 0.2s;
        }
        .card-footer-custom a:hover { color: #3b82f6; }
        .card-footer-custom .copy {
            font-size: 11px;
            color: #d1d5db;
            margin-top: 8px;
        }

        /* Alert tweak */
        .alert {
            border-radius: 10px;
            border: none;
            font-size: 13px;
            padding: 10px 14px;
            margin-bottom: 16px;
        }
        .alert-success { background: #f0fdf4; color: #166534; }
        .alert-danger  { background: #fef2f2; color: #991b1b; }

        /* Demo Credentials */
        .demo-section {
            margin: 14px 0 4px;
            padding: 14px;
            background: #f8fafc;
            border: 1.5px dashed #cbd5e1;
            border-radius: 12px;
        }
        .demo-section-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .demo-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .demo-card {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: left;
        }
        .demo-card:hover {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
            transform: translateY(-1px);
        }
        .demo-card .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
            margin-bottom: 6px;
        }
        .badge-direktur { background: rgba(30,58,138,0.1); color: #1e3a8a; }
        .badge-admin    { background: rgba(5,150,105,0.1);  color: #065f46; }
        .demo-card .cred-row {
            font-size: 11px;
            color: #475569;
            margin-bottom: 2px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .demo-card .cred-row strong {
            color: #0f172a;
            font-family: monospace;
            font-size: 11.5px;
        }
        .demo-card .click-hint {
            font-size: 9.5px;
            color: #94a3b8;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 3px;
        }
    </style>
</head>
<body>

<div class="login-card">

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
        <h1 class="header-title">Selamat Datang</h1>
        <p class="header-sub">Masuk ke portal administrator &amp; staff</p>
    </div>

    <!-- Body -->
    <div class="card-body-custom">

        <div class="secure-badge">
            <i class="fas fa-shield-halved"></i> Area Terbatas
        </div>

        <!-- Alerts -->
        <?php if($success): ?>
        <div class="alert alert-success alert-dismissible fade show" id="success-alert" role="alert">
            <i class="fas fa-check-circle me-1"></i><?= esc($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <?php if($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" id="error-alert" role="alert">
            <i class="fas fa-exclamation-circle me-1"></i><?= esc($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <?php if($errors): ?>
        <div class="alert alert-danger alert-dismissible fade show" id="validation-alert" role="alert">
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
        <form action="<?= base_url('login') ?>" method="POST" id="loginForm">
            <?= csrf_field() ?>

            <!-- Username -->
            <div class="mb-3">
                <label for="username" class="form-label">
                    Username atau Email
                </label>
                <div class="input-icon-wrap">
                    <i class="fas fa-user fi"></i>
                    <input type="text"
                           class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>"
                           id="username"
                           name="username"
                           value="<?= old('username') ?>"
                           placeholder="Masukkan username atau email"
                           required
                           autocomplete="username"
                           autofocus>
                </div>
                <?php if(isset($errors['username'])): ?>
                <div class="invalid-feedback d-block"><?= esc($errors['username']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="form-label">
                    Password
                </label>
                <div class="input-icon-wrap">
                    <i class="fas fa-lock fi"></i>
                    <input type="password"
                           class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                           id="password"
                           name="password"
                           placeholder="Masukkan password"
                           required
                           autocomplete="current-password">
                    <button type="button" class="btn-toggle" id="togglePassword" title="Tampilkan password">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
                <?php if(isset($errors['password'])): ?>
                <div class="invalid-feedback d-block"><?= esc($errors['password']) ?></div>
                <?php endif; ?>
            </div>

            <!-- UNCOMMENT untuk mengaktifkan fitur "Ingat Saya" -->
            <!--
            <div class="mb-4 d-flex align-items-center gap-2">
                <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1" style="width:18px;height:18px;margin:0;" <?= old('remember') ? 'checked' : '' ?>>
                <label for="remember" style="font-size:13px;cursor:pointer;color:#374151;">Ingat saya pada perangkat ini</label>
            </div>
            -->

            <button type="submit" class="btn-login" id="submitBtn">
                <i class="fas fa-sign-in-alt me-2"></i>Masuk ke Sistem
            </button>
        </form>

        <div class="divider">
            <hr><span>atau</span><hr>
        </div>

        <a href="<?= base_url('forgot-password') ?>" class="link-forgot">
            <i class="fas fa-key me-1"></i>Lupa password?
        </a>

        <!-- Demo Credentials -->
        <div class="demo-section mt-3">
            <div class="demo-section-title">
                <i class="fas fa-flask"></i> Akun Demo – Klik untuk Login Otomatis
            </div>
            <div class="demo-grid">
                <!-- Direktur -->
                <div class="demo-card" onclick="fillLogin('cecep.trihardiyanto','123456')" title="Login sebagai Direktur">
                    <div class="role-badge badge-direktur"><i class="fas fa-user-tie"></i> DIREKTUR</div>
                    <div class="cred-row"><i class="fas fa-user" style="width:10px;"></i> <strong>cecep.trihardiyanto</strong></div>
                    <div class="cred-row"><i class="fas fa-lock" style="width:10px;"></i> <strong>123456</strong></div>
                    <div class="click-hint"><i class="fas fa-mouse-pointer"></i> Klik untuk isi otomatis</div>
                </div>
                <!-- Admin -->
                <div class="demo-card" onclick="fillLogin('afrijal323','pass123456')" title="Login sebagai Admin">
                    <div class="role-badge badge-admin"><i class="fas fa-user-shield"></i> ADMIN</div>
                    <div class="cred-row"><i class="fas fa-user" style="width:10px;"></i> <strong>afrijal323</strong></div>
                    <div class="cred-row"><i class="fas fa-lock" style="width:10px;"></i> <strong>pass123456</strong></div>
                    <div class="click-hint"><i class="fas fa-mouse-pointer"></i> Klik untuk isi otomatis</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="card-footer-custom">
        <a href="<?= base_url('/') ?>">
            <i class="fas fa-arrow-left me-1"></i>Kembali ke Website
        </a>
        <div class="copy">&copy; <?= date('Y') ?> CDW Engineering. Hak Cipta Dilindungi.</div>
    </div>

</div><!-- .login-card -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3500,
        timerProgressBar: true,
        didOpen: function (toast) {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    // Auto-focus username
    const usernameField = document.getElementById('username');
    if (usernameField && !usernameField.value) usernameField.focus();

    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordField  = document.getElementById('password');
    const eyeIcon        = document.getElementById('eyeIcon');

    if (togglePassword && passwordField && eyeIcon) {
        togglePassword.addEventListener('click', function () {
            const isHidden = passwordField.type === 'password';
            passwordField.type = isHidden ? 'text' : 'password';
            eyeIcon.classList.toggle('fa-eye',      !isHidden);
            eyeIcon.classList.toggle('fa-eye-slash',  isHidden);
            this.title = isHidden ? 'Sembunyikan password' : 'Tampilkan password';
        });
    }

    // SweetAlert toasts for flashdata
    if (document.getElementById('success-alert')) {
        Toast.fire({ icon: 'success', title: document.getElementById('success-alert').innerText.replace('×','').trim() });
    }
    if (document.getElementById('error-alert')) {
        Toast.fire({ icon: 'error', title: document.getElementById('error-alert').innerText.replace('×','').trim() });
    }
    if (document.getElementById('validation-alert')) {
        Toast.fire({ icon: 'warning', title: 'Terdapat kesalahan validasi pada form' });
    }

    // Form submit loading state
    const form      = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');

    if (form && submitBtn) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const uVal = usernameField ? usernameField.value.trim() : '';
            const pVal = passwordField ? passwordField.value.trim() : '';

            if (!uVal || !pVal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Form Tidak Lengkap',
                    text: 'Harap isi username dan password',
                    confirmButtonColor: '#1e3a8a',
                    borderRadius: '12px'
                });
                if (!uVal && usernameField) usernameField.focus();
                else if (passwordField) passwordField.focus();
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
            this.submit();
        });
    }

    // Restore saved username
    const savedUsername = localStorage.getItem('cdw_username');
    if (savedUsername && usernameField && !usernameField.value) {
        usernameField.value = savedUsername;
    }
    if (form && usernameField) {
        form.addEventListener('submit', function () {
            if (usernameField.value) localStorage.setItem('cdw_username', usernameField.value);
        });
    }
});

// Fill login from demo card
function fillLogin(username, password) {
    const u = document.getElementById('username');
    const p = document.getElementById('password');
    if (u) u.value = username;
    if (p) p.value = password;
    // Show password briefly so user sees it filled
    if (p) { p.type = 'text'; setTimeout(() => { p.type = 'password'; }, 800); }
    const eyeIcon = document.getElementById('eyeIcon');
    if (eyeIcon) { eyeIcon.classList.remove('fa-eye-slash'); eyeIcon.classList.add('fa-eye'); }
    // Auto submit after brief delay
    setTimeout(() => {
        const form = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...'; }
        if (form) form.submit();
    }, 900);
}

    // Keyboard shortcuts
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter' && form) {
            e.preventDefault();
            form.dispatchEvent(new Event('submit'));
        }
        if (e.altKey && e.key === 'p' && togglePassword) {
            e.preventDefault();
            togglePassword.click();
        }
    });
});
</script>
</body>
</html>