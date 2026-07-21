<?php
$title = 'Debug Dokumen';
$active = 'dokumen';
$is_dokumen_page = true;
?>

<?= $this->include('admin/templates/header') ?>
<?= $this->include('admin/templates/sidebar') ?>
<?= $this->include('admin/templates/navbar') ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Debug Dokumen</h1>
    
    <?php if(isset($dokumen) && !empty($dokumen)): ?>
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Data Database</h6>
                </div>
                <div class="card-body">
                    <pre><?php print_r($dokumen); ?></pre>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">File Info</h6>
                </div>
                <div class="card-body">
                    <?php
                    $filePath = WRITEPATH . 'uploads/dokumen/' . basename($dokumen['path']);
                    $fileExists = file_exists($filePath);
                    ?>
                    
                    <p><strong>Path dari DB:</strong> <?= $dokumen['path'] ?></p>
                    <p><strong>Full Path:</strong> <?= $filePath ?></p>
                    <p><strong>File Exists:</strong> 
                        <span class="badge bg-<?= $fileExists ? 'success' : 'danger' ?>">
                            <?= $fileExists ? 'YES' : 'NO' ?>
                        </span>
                    </p>
                    
                    <?php if($fileExists): ?>
                    <p><strong>Size:</strong> <?= number_format(filesize($filePath) / 1024, 2) ?> KB</p>
                    <p><strong>MIME Type:</strong> <?= mime_content_type($filePath) ?></p>
                    <p><strong>Modified:</strong> <?= date('Y-m-d H:i:s', filemtime($filePath)) ?></p>
                    
                    <?php if(strpos(mime_content_type($filePath), 'image') !== false): ?>
                    <hr>
                    <h6>Preview:</h6>
                    <img src="<?= base_url('admin/karyawan/dokumen/preview/' . $dokumen['id']) ?>" 
                         class="img-fluid" style="max-width: 300px;">
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Test Form Update</h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('admin/karyawan/dokumen/update-test/' . $dokumen['id']) ?>" 
                  method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                
                <div class="form-group mb-3">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="pending" <?= $dokumen['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="diterima" <?= $dokumen['status'] == 'diterima' ? 'selected' : '' ?>>Diterima</option>
                        <option value="ditolak" <?= $dokumen['status'] == 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                    </select>
                </div>
                
                <div class="form-group mb-3">
                    <label>Upload File Baru (optional)</label>
                    <input type="file" name="file_dokumen" class="form-control">
                    <small>Current: <?= $dokumen['nama_file'] ?> (<?= number_format($dokumen['ukuran'] / 1024, 2) ?> KB)</small>
                </div>
                
                <button type="submit" class="btn btn-primary">Test Update</button>
                <a href="<?= base_url('admin/karyawan/dokumen') ?>" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-danger">
        Dokumen tidak ditemukan.
    </div>
    <?php endif; ?>
</div>

<?= $this->include('admin/templates/footer') ?>