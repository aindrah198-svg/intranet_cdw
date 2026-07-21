<?php
$session = session();
$success = $session->getFlashdata('success');
$error = $session->getFlashdata('error');
?>

<!-- Gunakan template yang sama dengan admin/index.php -->
<!-- Hanya ubah bagian main content -->
<div class="main-content">
    <div class="top-navbar">
        <div>
            <h5 class="mb-0">User Management</h5>
            <small class="text-muted">Manage system users</small>
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
    
    <?php if($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <!-- Add User Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5><i class="fas fa-users me-2"></i>System Users</h5>
        <a href="<?= base_url('admin/register') ?>" class="btn btn-primary">
            <i class="fas fa-user-plus me-2"></i>Add New User
        </a>
    </div>

    <!-- Users Table -->
    <div class="dashboard-card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $index => $u): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td>
                            <strong><?= $u['username'] ?></strong>
                            <?php if($u['id'] == session()->get('user_id')): ?>
                            <span class="badge bg-info ms-2">You</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $u['name'] ?></td>
                        <td><?= $u['email'] ?></td>
                        <td>
                            <span class="badge 
                                <?= $u['role'] == 'admin' ? 'bg-danger' : 
                                   ($u['role'] == 'manager' ? 'bg-warning' : 'bg-secondary') ?>">
                                <?= ucfirst($u['role']) ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                        <td>
                            <a href="<?= base_url('admin/users/edit/' . $u['id']) ?>" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <?php if($u['id'] != session()->get('user_id')): ?>
                            <a href="<?= base_url('admin/users/delete/' . $u['id']) ?>" 
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Are you sure you want to delete this user?')">
                                <i class="fas fa-trash"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if(empty($users)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-users fa-2x mb-3"></i><br>
                            No users found
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Auto-hide alerts
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
</script>