<?php
$session = session();
$success = $session->getFlashdata('success');
$errors = $session->getFlashdata('errors');
?>

<!-- Gunakan template yang sama dengan admin/index.php -->
<!-- Hanya ubah bagian main content -->
<div class="main-content">
    <div class="top-navbar">
        <div>
            <h5 class="mb-0">Profile Settings</h5>
            <small class="text-muted">Manage your profile information</small>
        </div>
        
        <div class="user-info">
            <div class="user-avatar">
                <?= strtoupper(substr($user['name'], 0, 1)) ?>
            </div>
            <div>
                <strong><?= $user['name'] ?></strong><br>
                <small class="text-muted"><?= ucfirst($user['role']) ?></small>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if($success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= $success ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4">
            <div class="dashboard-card">
                <div class="text-center">
                    <div class="user-avatar mx-auto" style="width: 80px; height: 80px; font-size: 2rem;">
                        <?= strtoupper(substr($user['name'], 0, 1)) ?>
                    </div>
                    <h5 class="mt-3"><?= $user['name'] ?></h5>
                    <p class="text-muted"><?= ucfirst($user['role']) ?></p>
                    
                    <div class="mt-4">
                        <p><i class="fas fa-user me-2"></i> <?= $user['username'] ?></p>
                        <p><i class="fas fa-envelope me-2"></i> <?= $user['email'] ?></p>
                        <p><i class="fas fa-clock me-2"></i> Last Login: <?= $user['login_time'] ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="dashboard-card">
                <h5 class="mb-4">Edit Profile</h5>
                
                <form action="/cdwnet/public/admin/update-profile" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" 
                                   class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                                   name="name" 
                                   value="<?= old('name', $user['name']) ?>"
                                   required>
                            <?php if(isset($errors['name'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['name'] ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" 
                                   class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                   name="email" 
                                   value="<?= old('email', $user['email']) ?>"
                                   required>
                            <?php if(isset($errors['email'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['email'] ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" 
                                   class="form-control" 
                                   value="<?= $user['username'] ?>" 
                                   disabled>
                            <small class="text-muted">Username cannot be changed</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role</label>
                            <input type="text" 
                                   class="form-control" 
                                   value="<?= ucfirst($user['role']) ?>" 
                                   disabled>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Profile
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-hide alerts
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
</script><?= view('admin/templates/header', $data) ?>
<?= view('admin/templates/sidebar', $data) ?>
<?= view('admin/templates/navbar', $data) ?>

<div class="dashboard-card">
    <h5 class="mb-4"><i class="fas fa-user me-2"></i>Profile Settings</h5>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 bg-light">
                <div class="card-body text-center">
                    <div class="user-avatar mx-auto" style="width: 100px; height: 100px; font-size: 2.5rem;">
                        <?= strtoupper(substr($user['name'], 0, 1)) ?>
                    </div>
                    <h5 class="mt-3"><?= $user['name'] ?></h5>
                    <p class="text-muted"><?= ucfirst($user['role']) ?></p>
                    
                    <div class="mt-4 text-start">
                        <p><i class="fas fa-user me-2"></i> <?= $user['username'] ?></p>
                        <p><i class="fas fa-envelope me-2"></i> <?= $user['email'] ?></p>
                        <p><i class="fas fa-clock me-2"></i> Last Login: <?= $user['login_time'] ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card border-0">
                <div class="card-body">
                    <h6 class="card-title mb-4">Edit Profile</h6>
                    
                    <form>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" value="<?= $user['name'] ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" value="<?= $user['email'] ?>">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" value="<?= $user['username'] ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Role</label>
                                <input type="text" class="form-control" value="<?= ucfirst($user['role']) ?>" disabled>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Profile
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('admin/templates/footer') ?>