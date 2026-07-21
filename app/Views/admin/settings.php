<?= view('admin/templates/header', $data) ?>
<?= view('admin/templates/sidebar', $data) ?>
<?= view('admin/templates/navbar', $data) ?>

<div class="dashboard-card">
    <h5 class="mb-4"><i class="fas fa-cog me-2"></i>System Settings</h5>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 mb-4">
                <div class="card-body">
                    <h6 class="card-title mb-3"><i class="fas fa-key me-2"></i>Change Password</h6>
                    
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-key me-2"></i>Change Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card border-0 mb-4">
                <div class="card-body">
                    <h6 class="card-title mb-3"><i class="fas fa-bell me-2"></i>Notification Settings</h6>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                        <label class="form-check-label" for="emailNotifications">
                            Email Notifications
                        </label>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="systemNotifications" checked>
                        <label class="form-check-label" for="systemNotifications">
                            System Notifications
                        </label>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="projectUpdates">
                        <label class="form-check-label" for="projectUpdates">
                            Project Updates
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Settings
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card border-0 mt-3">
        <div class="card-body">
            <h6 class="card-title mb-3"><i class="fas fa-language me-2"></i>Language & Region</h6>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Language</label>
                    <select class="form-select">
                        <option selected>English</option>
                        <option>Indonesian</option>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Time Zone</label>
                    <select class="form-select">
                        <option selected>Asia/Jakarta (WIB)</option>
                        <option>UTC</option>
                    </select>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Save Preferences
            </button>
        </div>
    </div>
</div>

<?= view('admin/templates/footer') ?>