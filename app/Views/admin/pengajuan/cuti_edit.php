<?php
$data = [
    'title'    => $title ?? 'Edit Pengajuan Cuti',
    'subtitle' => 'Perbarui Data Permohonan Cuti Karyawan',
    'active'   => 'pengajuan-cuti',
    'user'     => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
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
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/pengajuan/cuti') ?>" class="text-decoration-none text-muted">Pengajuan Cuti</a></li>
                    <li class="breadcrumb-item active text-info fw-bold" aria-current="page">Edit Cuti</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-edit text-warning me-2"></i> Edit Permohonan Cuti</h4>
            <small class="text-muted">Perbarui tipe cuti, tanggal pelaksanaan, alasan, atau status permohonan.</small>
        </div>
        <div>
            <a href="<?= base_url('admin/pengajuan/cuti') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Batal / Kembali
            </a>
        </div>
    </div>

    <!-- Main Card Form -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                <div class="card-header text-white py-3 px-4" style="background: linear-gradient(135deg, #0288d1, #01579b);">
                    <h5 class="card-title fs-6 fw-bold mb-0"><i class="fas fa-pencil-alt me-2"></i> Form Perubahan Cuti (<?= esc($c['nomor_cuti']) ?>)</h5>
                </div>
                <form action="<?= base_url('admin/pengajuan/cuti/update') ?>" method="POST">
                    <input type="hidden" name="id" value="<?= $c['id'] ?>">
                    
                    <div class="card-body p-4">
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Jenis Cuti *</label>
                                <select name="jenis_cuti" class="form-select rounded-3" required>
                                    <option value="Tahunan" selected>Cuti Tahunan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Status Cuti</label>
                                <select name="status" class="form-select rounded-3">
                                    <option value="Menunggu" <?= (($c['status'] ?? '') =='Menunggu')?'selected':'' ?>>Menunggu Persetujuan</option>
                                    <option value="Disetujui" <?= (($c['status'] ?? '') =='Disetujui')?'selected':'' ?>>Disetujui</option>
                                    <option value="Ditolak" <?= (($c['status'] ?? '') =='Ditolak')?'selected':'' ?>>Ditolak</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Tanggal Mulai *</label>
                                <input type="date" class="form-control rounded-3" name="tanggal_mulai" value="<?= esc($c['tanggal_mulai']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Tanggal Selesai *</label>
                                <input type="date" class="form-control rounded-3" name="tanggal_selesai" value="<?= esc($c['tanggal_selesai']) ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Alasan Cuti *</label>
                            <textarea class="form-control rounded-3" name="alasan" rows="4" required><?= esc($c['alasan']) ?></textarea>
                        </div>

                    </div>
                    <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                        <a href="<?= base_url('admin/pengajuan/cuti') ?>" class="btn btn-secondary rounded-pill px-4 me-2 font-semibold">Batal</a>
                        <button type="submit" class="btn btn-warning text-white rounded-pill px-4 font-semibold shadow-sm">
                            <i class="fas fa-save me-1.5"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= view('admin/templates/footer', $data) ?>
