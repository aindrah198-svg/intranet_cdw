<?php
// network-test.php - Untuk testing akses jaringan
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Network Access Test - HRIS System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #4CAF50; padding-bottom: 10px; }
        .info { background: #e8f5e9; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .success { color: #4CAF50; font-weight: bold; }
        .error { color: #f44336; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #4CAF50; color: white; }
        .test-links a { display: inline-block; margin: 10px 15px 10px 0; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px; }
        .test-links a:hover { background: #45a049; }
    </style>
</head>
<body>
    <div class="container">
        <h1>HRIS System - Network Access Test</h1>
        
        <div class="info">
            <h2>Server Information</h2>
            <table>
                <tr><th>Parameter</th><th>Value</th></tr>
                <tr><td>Server IP</td><td><?= $_SERVER['SERVER_ADDR'] ?? 'N/A' ?></td></tr>
                <tr><td>Server Name</td><td><?= $_SERVER['SERVER_NAME'] ?? 'N/A' ?></td></tr>
                <tr><td>Your IP Address</td><td><?= $_SERVER['REMOTE_ADDR'] ?? 'N/A' ?></td></tr>
                <tr><td>Request Time</td><td><?= date('Y-m-d H:i:s') ?></td></tr>
                <tr><td>PHP Version</td><td><?= phpversion() ?></td></tr>
                <tr><td>Document Root</td><td><?= $_SERVER['DOCUMENT_ROOT'] ?? 'N/A' ?></td></tr>
            </table>
        </div>
        
        <div class="info">
            <h2>Application Access</h2>
            <?php
            $baseUrl = 'http://' . ($_SERVER['HTTP_HOST'] ?? '192.168.11.100') . '/cdwnet/public/';
            $appUrl = $baseUrl . 'index.php';
            $loginUrl = $baseUrl . 'index.php/login';
            $adminUrl = $baseUrl . 'index.php/admin';
            ?>
            <p><strong>Base URL:</strong> <?= $baseUrl ?></p>
            
            <div class="test-links">
                <a href="<?= $appUrl ?>" target="_blank">Test Main Application</a>
                <a href="<?= $loginUrl ?>" target="_blank">Test Login Page</a>
                <a href="<?= $adminUrl ?>" target="_blank">Test Admin Dashboard</a>
                <a href="<?= $baseUrl ?>network-test.php" target="_blank">Refresh This Test</a>
            </div>
        </div>
        
        <div class="info">
            <h2>Connection Test</h2>
            <?php
            // Test database connection
            try {
                $db = @mysqli_connect('localhost', 'root', '');
                if ($db) {
                    echo '<p class="success">✓ Database server is reachable</p>';
                    mysqli_close($db);
                } else {
                    echo '<p class="error">✗ Cannot connect to database server</p>';
                }
            } catch (Exception $e) {
                echo '<p class="error">✗ Database error: ' . $e->getMessage() . '</p>';
            }
            
            // Test file permissions
            $writablePaths = [
                WRITEPATH . 'logs/' => 'Logs directory',
                WRITEPATH . 'cache/' => 'Cache directory',
                WRITEPATH . 'uploads/' => 'Uploads directory'
            ];
            
            foreach ($writablePaths as $path => $label) {
                if (is_writable($path)) {
                    echo "<p class=\"success\">✓ {$label} is writable</p>";
                } else {
                    echo "<p class=\"error\">✗ {$label} is not writable</p>";
                }
            }
            ?>
        </div>
        
        <div class="info">
            <h2>Quick Access URLs</h2>
            <p>Copy these URLs to access from other devices:</p>
            <ul>
                <li><strong>From this computer:</strong> <a href="<?= $baseUrl ?>"><?= $baseUrl ?></a></li>
                <li><strong>From other devices:</strong> <a href="http://192.168.11.100/cdwnet/public/">http://192.168.11.100/cdwnet/public/</a></li>
                <li><strong>Admin Login:</strong> <a href="<?= $loginUrl ?>"><?= $loginUrl ?></a></li>
            </ul>
            <p><strong>Note:</strong> Make sure XAMPP Apache is running and firewall allows port 80.</p>
        </div>
    </div>
</body>
</html>