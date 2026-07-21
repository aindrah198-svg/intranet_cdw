<?php
$title = 'Detail Absensi';
$active = 'absensi';
?>

<style>
    .detail-card {
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
    }
    
    .detail-header {
        background: linear-gradient(135deg, #4e73df 0%, #2e59d9 100%);
        color: white;
        padding: 25px 30px;
        border-radius: 15px 15px 0 0;
    }
    
    .detail-body {
        padding: 30px;
        background: white;
    }
    
    .info-section {
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .info-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .info-label {
        color: #6c757d;
        font-size: 0.9rem;
        font-weight: 500;
        margin-bottom: 5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .info-value {
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 0;
    }
    
    .info-value.text-muted {
        font-size: 1rem;
        font-weight: normal;
    }
    
    .badge-detail {
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
    }
    
    .icon-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 24px;
    }
    
    .icon-circle.primary {
        background: linear-gradient(135deg, #4e73df, #2e59d9);
        color: white;
    }
    
    .icon-circle.success {
        background: linear-gradient(135deg, #28a745, #1e7e34);
        color: white;
    }
    
    .icon-circle.info {
        background: linear-gradient(135deg, #17a2b8, #117a8b);
        color: white;
    }
    
    .icon-circle.warning {
        background: linear-gradient(135deg, #ffc107, #e0a800);
        color: white;
    }
    
    .icon-circle.danger {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
    }
    
    .map-container {
        border-radius: 10px;
        overflow: hidden;
        height: 200px;
        background: #f8f9fa;
        border: 1px solid #e0e0e0;
    }
    
    .map-placeholder {
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #6c757d;
    }
    
    .map-placeholder i {
        font-size: 3rem;
        margin-bottom: 15px;
        opacity: 0.5;
    }
    
    .location-info {
        font-size: 0.9rem;
    }
    
    .location-info .badge {
        font-size: 0.75rem;
        padding: 3px 8px;
        border-radius: 10px;
    }
    
    .action-buttons .btn {
        min-width: 120px;
    }
    
    .back-link {
        color: #6c757d;
        text-decoration: none;
        transition: color 0.3s;
    }
    
    .back-link:hover {
        color: #4e73df;
    }
    
    .shift-badge {
        font-size: 0.85rem;
        padding: 5px 12px;
        border-radius: 20px;
    }
    
    .shift-badge.pagi {
        background: linear-gradient(135deg, #fff3cd, #ffeaa7);
        color: #856404;
        border: 1px solid #ffeaa7;
    }
    
    .shift-badge.siang {
        background: linear-gradient(135deg, #d1f2eb, #a3e4d7);
        color: #155724;
        border: 1px solid #a3e4d7;
    }
    
    .shift-badge.sore {
        background: linear-gradient(135deg, #cce5ff, #99caff);
        color: #004085;
        border: 1px solid #99caff;
    }
    
    .shift-badge.malam {
        background: linear-gradient(135deg, #e7f3ff, #d1e7ff);
        color: #0d6efd;
        border: 1px solid #d1e7ff;
    }
    
    .status-badge {
        font-size: 0.9rem;
        padding: 6px 15px;
        border-radius: 20px;
        font-weight: 600;
    }
    
    @media (max-width: 768px) {
        .detail-body {
            padding: 20px;
        }
        
        .icon-circle {
            width: 50px;
            height: 50px;
            font-size: 20px;
            margin-right: 10px;
        }
        
        .action-buttons .btn {
            width: 100%;
            margin-bottom: 10px;
        }
    }

    /* Button styling untuk Google Maps links */
.btn-outline-primary, .btn-outline-success {
    border-width: 1px;
    transition: all 0.3s;
}

.btn-outline-primary:hover {
    background-color: #4e73df;
    border-color: #4e73df;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(78, 115, 223, 0.3);
}

.btn-outline-success:hover {
    background-color: #28a745;
    border-color: #28a745;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
}

/* Map container */
.map-container {
    border-radius: 10px;
    overflow: hidden;
    height: 150px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 1px solid #dee2e6;
}

.map-placeholder {
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    padding: 20px;
}

.map-placeholder i {
    font-size: 2.5rem;
    margin-bottom: 10px;
    opacity: 0.6;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .map-container {
        height: 120px;
    }
    
    .map-placeholder i {
        font-size: 2rem;
    }
    
    .btn-sm {
        padding: 5px 10px;
        font-size: 0.8rem;
    }
}
</style>

<div class="container-fluid py-4">
    <!-- Back Navigation -->
    <div class="mb-4">
        <a href="<?= base_url('admin/absensi') ?>" class="back-link">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Absensi
        </a>
    </div>

    <!-- Main Detail Card -->
    <div class="detail-card">
        <!-- Header -->
        <div class="detail-header">
            <div class="d-flex align-items-center">
                <div class="icon-circle primary">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div>
                    <h4 class="mb-1">Detail Absensi</h4>
                    <p class="mb-0 opacity-75">
                        <i class="far fa-calendar me-1"></i>
                        <?= date('l, d F Y', strtotime($absensi['tanggal'] ?? '')) ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="detail-body">
            <div class="row">
                <!-- Left Column - Basic Info -->
                <div class="col-md-6">
                    <!-- Employee Information -->
                    <div class="info-section">
                        <h5 class="mb-3 text-primary">
                            <i class="fas fa-user me-2"></i>Informasi Karyawan
                        </h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="info-label">Nama Karyawan</div>
                                <div class="info-value"><?= esc($absensi['nama_lengkap'] ?? '-') ?></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="info-label">NIK</div>
                                <div class="info-value"><?= esc($absensi['nik'] ?? '-') ?></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="info-label">Departemen</div>
                                <div class="info-value"><?= esc($absensi['departemen'] ?? '-') ?></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="info-label">Jabatan</div>
                                <div class="info-value"><?= esc($absensi['jabatan'] ?? '-') ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Information -->
                    <div class="info-section">
                        <h5 class="mb-3 text-primary">
                            <i class="fas fa-clock me-2"></i>Informasi Absensi
                        </h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="info-label">Shift</div>
                                <div>
                                    <?php if ($absensi['shift'] ?? ''): ?>
                                        <?php
                                        $shift_names = [
                                            'pagi' => ['name' => 'Shift Pagi', 'class' => 'pagi'],
                                            'siang' => ['name' => 'Shift Siang', 'class' => 'siang'],
                                            'sore' => ['name' => 'Shift Sore', 'class' => 'sore'],
                                            'malam' => ['name' => 'Shift Malam', 'class' => 'malam']
                                        ];
                                        $shift = $absensi['shift'];
                                        ?>
                                        <span class="shift-badge <?= $shift_names[$shift]['class'] ?? '' ?>">
                                            <?= $shift_names[$shift]['name'] ?? ucfirst($shift) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="info-label">Status</div>
                                <div>
                                    <?php if ($absensi['status'] ?? ''): ?>
                                        <?php
                                        $status_colors = [
                                            'Hadir' => 'success',
                                            'Izin' => 'info',
                                            'Sakit' => 'warning',
                                            'Cuti' => 'primary',
                                            'Alpha' => 'danger'
                                        ];
                                        $status = $absensi['status'];
                                        ?>
                                        <span class="badge bg-<?= $status_colors[$status] ?? 'secondary' ?> status-badge">
                                            <?= $status ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Time Information -->
                    <div class="info-section">
                        <h5 class="mb-3 text-primary">
                            <i class="fas fa-business-time me-2"></i>Waktu Kerja
                        </h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="info-label">Waktu Masuk</div>
                                <div class="info-value">
                                    <?php if ($absensi['waktu_masuk'] ?? ''): ?>
                                        <?= date('H:i:s', strtotime($absensi['waktu_masuk'])) ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="info-label">Waktu Pulang</div>
                                <div class="info-value">
                                    <?php if ($absensi['waktu_pulang'] ?? ''): ?>
                                        <?= date('H:i:s', strtotime($absensi['waktu_pulang'])) ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="info-label">Jam Kerja</div>
                                <div class="info-value">
                                    <?php if ($absensi['jam_kerja'] ?? 0): ?>
                                        <?= number_format($absensi['jam_kerja'], 1) ?> jam
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="info-label">Jam Lembur</div>
                                <div class="info-value">
                                    <?php if ($absensi['jam_lembur'] ?? 0): ?>
                                        <span class="text-danger"><?= number_format($absensi['jam_lembur'], 1) ?> jam</span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="info-label">Terlambat</div>
                                <div class="info-value">
                                    <?php if ($absensi['terlambat'] ?? 0): ?>
                                        <?php
                                        $terlambat = $absensi['terlambat'];
                                        $jam = floor($terlambat / 60);
                                        $menit = $terlambat % 60;
                                        if ($jam > 0 && $menit > 0) {
                                            echo "<span class='text-danger'>{$jam} jam {$menit} menit</span>";
                                        } elseif ($jam > 0) {
                                            echo "<span class='text-danger'>{$jam} jam</span>";
                                        } else {
                                            echo "<span class='text-danger'>{$menit} menit</span>";
                                        }
                                        ?>
                                    <?php else: ?>
                                        <span class="text-success">Tepat waktu</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Location & Notes -->
                <div class="col-md-6">
<!-- Location Information -->
<div class="info-section">
    <h5 class="mb-3 text-primary">
        <i class="fas fa-map-marker-alt me-2"></i>Informasi Lokasi
    </h5>
    
    <!-- Check-in Location -->
    <div class="mb-4">
        <h6 class="mb-2 text-info">
            <i class="fas fa-sign-in-alt me-1"></i>Lokasi Masuk
        </h6>
        <?php if ($absensi['lokasi_masuk'] ?? ''): ?>
            <div class="location-info mb-2">
                <div class="mb-2">
                    <strong>Alamat:</strong>
                    <div class="mt-1"><?= esc($absensi['lokasi_masuk']) ?></div>
                </div>
                
                <?php if ($absensi['latitude_masuk'] ?? '' && $absensi['longitude_masuk'] ?? ''): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Koordinat:</strong>
                            <div class="mt-1">
                                <?= number_format($absensi['latitude_masuk'], 6) ?>, 
                                <?= number_format($absensi['longitude_masuk'], 6) ?>
                                <span class="badge bg-info ms-2">GPS</span>
                            </div>
                            <!-- Link Google Maps -->
                            <div class="mt-2">
                                <a href="https://www.google.com/maps?q=<?= $absensi['latitude_masuk'] ?>,<?= $absensi['longitude_masuk'] ?>" 
                                   target="_blank" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-external-link-alt me-1"></i> Lihat di Google Maps
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <strong>Device:</strong>
                            <div class="mt-1"><?= esc($absensi['device_masuk'] ?? '-') ?></div>
                            <strong>IP Address:</strong>
                            <div class="mt-1"><?= esc($absensi['ip_address_masuk'] ?? '-') ?></div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-warning">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Lokasi GPS tidak tersedia
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Lokasi masuk tidak tercatat
            </div>
        <?php endif; ?>
    </div>

    <!-- Check-out Location -->
    <div class="mb-4">
        <h6 class="mb-2 text-success">
            <i class="fas fa-sign-out-alt me-1"></i>Lokasi Pulang
        </h6>
        <?php if ($absensi['lokasi_pulang'] ?? ''): ?>
            <div class="location-info mb-2">
                <div class="mb-2">
                    <strong>Alamat:</strong>
                    <div class="mt-1"><?= esc($absensi['lokasi_pulang']) ?></div>
                </div>
                
                <?php if ($absensi['latitude_pulang'] ?? '' && $absensi['longitude_pulang'] ?? ''): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Koordinat:</strong>
                            <div class="mt-1">
                                <?= number_format($absensi['latitude_pulang'], 6) ?>, 
                                <?= number_format($absensi['longitude_pulang'], 6) ?>
                                <span class="badge bg-info ms-2">GPS</span>
                            </div>
                            <!-- Link Google Maps -->
                            <div class="mt-2">
                                <a href="https://www.google.com/maps?q=<?= $absensi['latitude_pulang'] ?>,<?= $absensi['longitude_pulang'] ?>" 
                                   target="_blank" 
                                   class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-external-link-alt me-1"></i> Lihat di Google Maps
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <strong>Device:</strong>
                            <div class="mt-1"><?= esc($absensi['device_pulang'] ?? '-') ?></div>
                            <strong>IP Address:</strong>
                            <div class="mt-1"><?= esc($absensi['ip_address_pulang'] ?? '-') ?></div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-warning">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Lokasi GPS tidak tersedia
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Lokasi pulang tidak tercatat
            </div>
        <?php endif; ?>
    </div>

    <!-- Simple Map Container -->
    <div class="mb-3">
        <div class="map-container">
            <div class="map-placeholder">
                <i class="fas fa-map"></i>
                <div class="text-center mt-2">
                    <small>Peta Lokasi Absensi</small><br>
                    <small class="text-muted">
                        <?php if (($absensi['latitude_masuk'] ?? '') && ($absensi['longitude_masuk'] ?? '')): ?>
                            Koordinat: <?= number_format($absensi['latitude_masuk'], 4) ?>, <?= number_format($absensi['longitude_masuk'], 4) ?>
                        <?php else: ?>
                            Data koordinat tidak tersedia
                        <?php endif; ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

                 <!-- Audit Trail Information -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h6 class="mb-0"><i class="fas fa-history me-2"></i>Audit Trail</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Dibuat Oleh</label>
                    <p class="form-control-static">
                        <?= !empty($absensi['created_by_name']) ? esc($absensi['created_by_name']) : 
                              (!empty($absensi['created_by_username']) ? esc($absensi['created_by_username']) : '-') ?>
                    </p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Waktu Dibuat</label>
                    <p class="form-control-static"><?= date('d/m/Y H:i', strtotime($absensi['created_at'])) ?></p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Diupdate Oleh</label>
                    <p class="form-control-static">
                        <?= !empty($absensi['updated_by_name']) ? esc($absensi['updated_by_name']) : 
                              (!empty($absensi['updated_by_username']) ? esc($absensi['updated_by_username']) : '-') ?>
                    </p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Waktu Update Terakhir</label>
                    <p class="form-control-static"><?= date('d/m/Y H:i', strtotime($absensi['updated_at'])) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

            <!-- Action Buttons -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="d-flex justify-content-end action-buttons">
                        <a href="<?= base_url('admin/absensi/edit/' . ($absensi['id'] ?? '')) ?>" 
                           class="btn btn-warning me-2">
                            <i class="fas fa-edit me-1"></i> Edit Data
                        </a>
                        <a href="<?= base_url('admin/absensi') ?>" 
                           class="btn btn-secondary me-2">
                            <i class="fas fa-list me-1"></i> Kembali ke Daftar
                        </a>
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-trash me-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div style="width: 70px; height: 70px; margin: 0 auto; background: linear-gradient(135deg, #dc3545, #c82333); border-radius: 50%; display: flex; align-items: center; justify-content: center;" class="mb-3">
                        <i class="fas fa-trash fa-2x text-white"></i>
                    </div>
                    <h5>Hapus Data Absensi?</h5>
                    <p class="text-muted mt-2">
                        Apakah Anda yakin ingin menghapus data absensi ini?<br>
                        <strong><?= esc($absensi['nama_lengkap'] ?? '') ?></strong> - 
                        <?= date('d/m/Y', strtotime($absensi['tanggal'] ?? '')) ?>
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-1"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Delete Confirmation
function confirmDelete(absensiId) {
    $('#deleteModal').modal('show');
    
    document.getElementById('confirmDeleteBtn').onclick = function() {
        deleteAbsensi(absensiId);
    };
}

function deleteAbsensi(absensiId) {
    const btn = document.getElementById('confirmDeleteBtn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menghapus...';
    btn.disabled = true;
    
    fetch(`<?= base_url('admin/absensi/delete/') ?>${absensiId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            showToast(result.message, 'success');
            setTimeout(() => {
                window.location.href = '<?= base_url('admin/absensi') ?>';
            }, 1000);
        } else {
            showToast(result.message || 'Gagal menghapus data', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan saat menghapus data', 'error');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        $('#deleteModal').modal('hide');
    });
}

// Show toast notification
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : type} border-0`;
    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '1060';
    toast.style.minWidth = '300px';
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${type === 'success' ? '<i class="fas fa-check-circle me-2"></i>' : 
                  type === 'error' ? '<i class="fas fa-exclamation-circle me-2"></i>' : 
                  '<i class="fas fa-info-circle me-2"></i>'}
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 3000);
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Show toast notification
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : type} border-0`;
    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '1060';
    toast.style.minWidth = '300px';
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${type === 'success' ? '<i class="fas fa-check-circle me-2"></i>' : 
                  type === 'error' ? '<i class="fas fa-exclamation-circle me-2"></i>' : 
                  '<i class="fas fa-info-circle me-2"></i>'}
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 3000);
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

</script>