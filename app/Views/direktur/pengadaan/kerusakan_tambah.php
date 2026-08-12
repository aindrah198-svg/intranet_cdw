<?php
// app/Views/direktur/pengadaan/kerusakan_tambah.php

$title = $title ?? 'Laporkan Kerusakan Alat Baru';
$templateData = [
    'title' => $title,
    'active' => 'pengadaan'
];

echo view('direktur/templates/header', $templateData);
echo view('direktur/templates/sidebar', $templateData);
echo view('direktur/templates/navbar', $templateData);
?>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= base_url('direktur/pengadaan/kerusakan') ?>" class="text-decoration-none text-muted">Kerusakan Alat</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Tambah Laporan</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-plus-circle text-danger me-2"></i> Laporkan Kerusakan Alat Baru</h4>
            <small class="text-muted">Isi formulir laporan kerusakan peralatan kerja dan tentukan alur penugasan servisnya.</small>
        </div>
        <div>
            <a href="<?= base_url('direktur/pengadaan/kerusakan') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Main Card Form -->
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                <div class="card-header bg-gradient-primary text-white py-3 px-4">
                    <h5 class="card-title fs-6 fw-bold mb-0"><i class="fas fa-file-signature me-2"></i> Formulir Laporan Kerusakan Alat</h5>
                </div>
                <form action="<?= base_url('direktur/pengadaan/kerusakan/simpan') ?>" method="POST" onsubmit="const btn = this.querySelector('button[type=submit]'); if(btn){ btn.disabled = true; btn.innerHTML = '<i class=\'fas fa-spinner fa-spin me-1\'></i> Menyimpan...'; }">
                    <div class="card-body p-4">
                        
                        <!-- Section 1: Informasi Peralatan -->
                        <div class="border-bottom pb-3 mb-4">
                            <h6 class="fw-bold text-dark mb-3"><i class="fas fa-tools text-danger me-2"></i> 1. Informasi Peralatan & Kerusakan</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-xs text-dark">Nama Alat / Inventaris *</label>
                                    <input type="text" class="form-control rounded-3" name="nama_alat" required placeholder="Cth: Printer Epson EcoTank L3210, Proyektor Meeting">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-xs text-dark">Lokasi Asal Alat</label>
                                    <input type="text" class="form-control rounded-3" name="lokasi_alat" placeholder="Cth: Ruang HRD Lt. 2, Workshop Proyek A">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-xs text-dark">Tingkat Kerusakan *</label>
                                    <select name="tingkat_kerusakan" class="form-select rounded-3">
                                        <option value="ringan">Ringan (Masih bisa pakai / kendala minor)</option>
                                        <option value="sedang" selected>Sedang (Butuh servis / perbaikan)</option>
                                        <option value="berat">Berat (Mati total / tidak dapat dipakai)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-xs text-dark">Status Tindakan Terkini</label>
                                    <select name="status_tindakan" class="form-select rounded-3">
                                        <option value="dilaporkan" selected>Dilaporkan</option>
                                        <option value="dalam_perbaikan">Dalam Perbaikan / Servis</option>
                                        <option value="selesai">Selesai Diperbaiki</option>
                                        <option value="rusak_total">Rusak Total / Ganti Unit</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-xs text-dark">Deskripsi Kronologi Kerusakan *</label>
                                    <textarea class="form-control rounded-3" name="deskripsi_kerusakan" rows="3" required placeholder="Jelaskan detail gejala atau kronologi kerusakan yang terjadi..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Penugasan & Lokasi Perbaikan -->
                        <div class="p-3.5 bg-light rounded-4 border mb-4">
                            <h6 class="fw-bold text-dark mb-2"><i class="fas fa-route text-primary me-2"></i> 2. Penugasan & Lokasi Perbaikan (Workflow)</h6>
                            <p class="text-muted text-xs mb-3">Tentukan pihak teknisi pengurus, lokasi tujuan servis, dan petugas pembawa alat.</p>
                            
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-xs text-dark">Siapa yang Benerin / Teknisi *</label>
                                    <input type="text" class="form-control rounded-3 bg-white" name="teknisi_pengurus" placeholder="Cth: Rian (Teknisi IT) / Service Center Epson">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-xs text-dark">Dibawa Ke Mana (Lokasi Servis) *</label>
                                    <input type="text" class="form-control rounded-3 bg-white" name="lokasi_perbaikan" placeholder="Cth: Service Center Epson Kelapa Gading">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-xs text-dark">Dibawa Oleh Siapa (Kurir/Pembawa)</label>
                                    <input type="text" class="form-control rounded-3 bg-white" name="petugas_pembawa" placeholder="Cth: Doni (Driver Office) / Self Deliver">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-xs text-dark">Catatan Tambahan Perbaikan</label>
                                    <textarea class="form-control rounded-3 bg-white" name="catatan_perbaikan" rows="2" placeholder="Catatan awal / garansi / estimasi pengerjaan..."></textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                        <a href="<?= base_url('direktur/pengadaan/kerusakan') ?>" class="btn btn-secondary rounded-pill px-4 me-2 font-semibold">Batal</a>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 font-semibold shadow-sm">
                            <i class="fas fa-save me-1.5"></i> Simpan Laporan Kerusakan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= view('direktur/templates/footer', $templateData) ?>
