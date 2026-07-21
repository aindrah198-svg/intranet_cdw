<?php
/**
 * Auto-Deploy Webhook Script untuk GitHub
 * Letakkan file ini di: public/deploy.php
 * 
 * GitHub Webhook Payload URL: https://cdw-engineering-intranet.my.id/deploy.php?secret=CDW_DEPLOY_2026
 */

// ============================================================
// KONFIGURASI - Ganti secret key ini jika mau
// ============================================================
define('SECRET_KEY', 'CDW_DEPLOY_2026');
define('REPO_PATH', '/home/cdwengin/public_html');

// ============================================================
// KEAMANAN - Validasi secret key
// ============================================================
$secret = $_GET['secret'] ?? '';
if ($secret !== SECRET_KEY) {
    http_response_code(403);
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

// ============================================================
// EKSEKUSI GIT PULL
// ============================================================
$commands = [
    "cd " . REPO_PATH,
    "git checkout -- .",          // Buang perubahan lokal
    "git fetch origin main",      // Ambil update dari GitHub
    "git reset --hard origin/main" // Paksa update ke versi GitHub
];

$command = implode(' && ', $commands) . ' 2>&1';
$output  = shell_exec($command);

// ============================================================
// RESPONSE
// ============================================================
header('Content-Type: application/json');
echo json_encode([
    'status'    => 'success',
    'timestamp' => date('Y-m-d H:i:s'),
    'output'    => $output
]);
