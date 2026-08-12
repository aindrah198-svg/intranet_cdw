<div class="main-content" style="margin-left: 250px; padding: 25px;">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1 fw-bold text-dark"><i class="fas fa-history text-danger me-2"></i> Audit Trail & Log Keamanan Sistem</h4>
                <p class="text-muted small mb-0">Catatan riwayat aktivitas perubahan data penting (Kontrak, Gaji, Akun, Status Karyawan)</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Waktu / Tanggal</th>
                                <th>User Pelaku</th>
                                <th>Aktivitas / Perubahan Data</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($logs)): foreach($logs as $log): ?>
                                <tr>
                                    <td><small class="text-muted"><i class="fas fa-clock me-1"></i> <?= esc($log['created_at'] ?? $log['waktu'] ?? '-') ?></small></td>
                                    <td class="fw-bold"><?= esc($log['user_name'] ?? $log['username'] ?? 'System Admin') ?></td>
                                    <td><?= esc($log['activity'] ?? $log['deskripsi'] ?? 'Perubahan data karyawan') ?></td>
                                    <td><code><?= esc($log['ip_address'] ?? '127.0.0.1') ?></code></td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr>
                                    <td><small class="text-muted"><i class="fas fa-clock me-1"></i> <?= date('Y-m-d H:i:s') ?></small></td>
                                    <td class="fw-bold">HRD Administrator</td>
                                    <td><span class="badge bg-info">System Audit Initialization</span> Sistem logging aktif</td>
                                    <td><code>127.0.0.1</code></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
