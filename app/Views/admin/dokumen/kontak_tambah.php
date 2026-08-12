<?php
$title = $title ?? 'Tambah Kontak PIC Project';
$data = [
    'title'  => $title,
    'user'   => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin'],
    'active' => 'dokumen'
];

echo view('admin/templates/header', $data);
echo view('admin/templates/sidebar', $data);
echo view('admin/templates/navbar', $data);
?>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dokumen/kontak') ?>" class="text-decoration-none text-muted">Kontak Project</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Tambah Kontak</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-user-plus text-primary me-2"></i> Tambah Kontak PIC Project Baru</h4>
            <small class="text-muted">Isi informasi kontak PIC Klien, Subkontraktor, Vendor atau Stakeholder Proyek.</small>
        </div>
        <div>
            <a href="<?= base_url('admin/dokumen/kontak') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Main Card Form -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                <div class="card-header bg-primary text-white py-3 px-4">
                    <h5 class="card-title fs-6 fw-bold mb-0"><i class="fas fa-address-card me-2"></i> Form Data Kontak PIC</h5>
                </div>
                <form action="<?= base_url('admin/dokumen/kontak/simpan') ?>" method="POST" onsubmit="const btn = this.querySelector('button[type=submit]'); if(btn){ btn.disabled = true; btn.innerHTML = '<i class=\'fas fa-spinner fa-spin me-1\'></i> Menyimpan...'; }">
                    <div class="card-body p-4">
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Project Terkait</label>
                            <select name="project_id" class="form-select rounded-3">
                                <option value="">-- Non-Project / Kontak Umum --</option>
                                <?php foreach(($projects ?? []) as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= esc($p['kode_project']) ?> - <?= esc($p['nama_project']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Nama Kontak / PIC *</label>
                            <input type="text" class="form-control rounded-3" name="nama_kontak" required placeholder="Cth: Bpk. Heru Wijaya">
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Perusahaan / Klien</label>
                                <input type="text" class="form-control rounded-3" name="perusahaan_klien" placeholder="Cth: PT Pertamina Trans Kontinental, PT PLN">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Jabatan PIC</label>
                                <input type="text" class="form-control rounded-3" name="jabatan" placeholder="Cth: Senior Project Manager, Site Engineer">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">No. Telepon / WhatsApp *</label>
                                <input type="text" class="form-control rounded-3" name="telepon" required placeholder="Cth: 081298765432">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Email Kontak</label>
                                <input type="email" class="form-control rounded-3" name="email" placeholder="Cth: heru.gunawan@pertamina.com">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Catatan Khusus</label>
                            <textarea class="form-control rounded-3" name="catatan" rows="3" placeholder="Catatan mengenai peran PIC, penandatanganan BAST/Invoice, atau jadwal koordinasi..."></textarea>
                        </div>

                    </div>
                    <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                        <a href="<?= base_url('admin/dokumen/kontak') ?>" class="btn btn-secondary rounded-pill px-4 me-2 font-semibold">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 font-semibold shadow-sm">
                            <i class="fas fa-save me-1.5"></i> Simpan Kontak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= view('admin/templates/footer', $data) ?>
