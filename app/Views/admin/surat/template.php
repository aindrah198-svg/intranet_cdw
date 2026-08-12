<?php
$data = ['title' => 'Template Surat', 'subtitle' => 'Kelola Master Template Surat Perusahaan', 'active' => 'surat-template', 'user' => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']];
?>
<?= view('admin/templates/header', $data) ?>
<?= view('admin/templates/sidebar', $data) ?>
<?= view('admin/templates/navbar', $data) ?>

<div class="card border-0 shadow-sm rounded-3 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 style="color:#4a148c;font-weight:700;margin:0;"><i class="fas fa-file-alt me-2"></i>Template Surat</h5>
        <button class="btn btn-sm" style="background:#7b1fa2;color:white;"><i class="fas fa-plus me-1"></i>Tambah Template</button>
    </div>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="border rounded-3 p-3 text-center" style="background:#f3e5f5;">
                <i class="fas fa-file-word fa-3x text-purple mb-2"></i>
                <h6>Template Surat Tugas</h6>
                <small class="text-muted">Format baku surat perintah tugas dinas</small>
                <div class="mt-3">
                    <button class="btn btn-sm btn-outline-primary"><i class="fas fa-download me-1"></i>Unduh DOCX</button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="border rounded-3 p-3 text-center" style="background:#f3e5f5;">
                <i class="fas fa-file-word fa-3x text-purple mb-2"></i>
                <h6>Template Surat Undangan</h6>
                <small class="text-muted">Format baku surat undangan rapat resmi</small>
                <div class="mt-3">
                    <button class="btn btn-sm btn-outline-primary"><i class="fas fa-download me-1"></i>Unduh DOCX</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('admin/templates/footer', $data) ?>
