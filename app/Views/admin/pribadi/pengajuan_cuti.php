<?php
$data = ['title' => 'Form Pengajuan Cuti', 'subtitle' => 'Pengajuan Cuti Pribadi Administrator', 'active' => 'form-pengajuan', 'user' => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']];
?>
<?= view('admin/templates/header', $data) ?>
<?= view('admin/templates/sidebar', $data) ?>
<?= view('admin/templates/navbar', $data) ?>

<div class="card border-0 shadow-sm rounded-3 p-4">
    <h5 style="color:#4a148c;font-weight:700;" class="mb-3"><i class="fas fa-paper-plane me-2"></i>Form Pengajuan Cuti</h5>
    <form action="<?= base_url('admin/form-pengajuan/cuti/store') ?>" method="post">
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label">Tanggal Mulai Cuti</label>
                <input type="date" name="tgl_mulai" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tanggal Selesai Cuti</label>
                <input type="date" name="tgl_selesai" class="form-control" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Alasan Cuti</label>
            <textarea name="alasan" class="form-control" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn text-white" style="background:#7b1fa2;"><i class="fas fa-save me-1"></i>Kirim Pengajuan Cuti</button>
    </form>
</div>

<?= view('admin/templates/footer', $data) ?>
