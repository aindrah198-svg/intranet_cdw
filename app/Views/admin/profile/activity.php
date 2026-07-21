<?php
// C:\xampp\htdocs\intranet_cdw\app\Views\admin\profile\activity.php
$title = 'Log Aktivitas';
$active = 'profile';
$subactive = 'activity';
?>

<style>
    .activity-card {
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 15px;
    }
    
    .activity-item {
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .activity-item:last-child {
        border-bottom: none;
    }
    
    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
    }
    
    .activity-login { background: linear-gradient(135deg, #4e73df, #2e59d9); }
    .activity-update { background: linear-gradient(135deg, #1cc88a, #17a673); }
    .activity-create { background: linear-gradient(135deg, #36b9cc, #258391); }
    .activity-delete { background: linear-gradient(135deg, #e74a3b, #d52a1a); }
    .activity-system { background: linear-gradient(135deg, #6f42c1, #5a32a3); }
    
    .activity-title {
        font-weight: 500;
        color: #343a40;
    }
    
    .activity-time {
        font-size: 0.875rem;
        color: #6c757d;
    }
    
    .activity-desc {
        color: #6c757d;
        font-size: 0.9rem;
        margin-top: 5px;
    }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-history me-2"></i>Log Aktivitas
            </h1>
            <p class="text-muted mb-0">Riwayat aktivitas akun Anda</p>
        </div>
        <div>
            <a href="<?= base_url('admin/profile') ?>" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Profil
            </a>
        </div>
    </div>

    <!-- Activity List -->
    <div class="row">
        <div class="col-md-12">
            <div class="activity-card bg-white p-4">
                <h5 class="text-primary mb-4">Aktivitas Terbaru</h5>
                
                <?php if (!empty($activities)): ?>
                    <?php foreach ($activities as $activity): ?>
                    <div class="activity-item d-flex">
                        <div class="activity-icon text-white">
                            <i class="<?= $activity['icon'] ?>"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <h6 class="activity-title mb-1"><?= esc($activity['title']) ?></h6>
                                <span class="activity-time"><?= $activity['time'] ?></span>
                            </div>
                            <p class="activity-desc mb-0"><?= esc($activity['description']) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada aktivitas yang tercatat</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>