<?php
$title = $title ?? 'Buat Surat Jalan Baru';
$active = $active ?? 'surat_jalan';
?>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus-circle me-2"></i>
                <?= $title ?>
            </h1>
            <p class="text-muted">Form pembuatan surat jalan baru</p>
        </div>
    </div>

    <!-- Form -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-edit me-2"></i>
                        Form Surat Jalan
                    </h6>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('sales/surat-jalan/store') ?>" method="POST">
                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nomor Surat Jalan *</label>
                                    <input type="text" class="form-control" name="nomor_surat_jalan" required 
                                           placeholder="Contoh: 001/SJ/CDW/01/2024">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Kirim *</label>
                                    <input type="date" class="form-control" name="tanggal_kirim" required 
                                           value="<?= date('Y-m-d') ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Penerima -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="font-weight-bold text-primary mb-3">
                                    <i class="fas fa-user me-2"></i>Informasi Penerima
                                </h6>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nama Perusahaan Penerima *</label>
                                    <input type="text" class="form-control" name="penerima_perusahaan" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">UP / Penanggung Jawab *</label>
                                    <input type="text" class="form-control" name="penerima_up" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Alamat Pengiriman *</label>
                                    <textarea class="form-control" name="alamat_pengiriman" rows="3" required></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Pengiriman -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="font-weight-bold text-primary mb-3">
                                    <i class="fas fa-truck me-2"></i>Informasi Pengiriman
                                </h6>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nama Sopir</label>
                                    <input type="text" class="form-control" name="sopir">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nomor Kendaraan</label>
                                    <input type="text" class="form-control" name="no_kendaraan">
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status *</label>
                                    <select class="form-select" name="status" required>
                                        <option value="diproses">Diproses</option>
                                        <option value="dikirim">Dikirim</option>
                                        <option value="diterima">Diterima</option>
                                        <option value="dibatalkan">Dibatalkan</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Keterangan</label>
                                    <textarea class="form-control" name="keterangan" rows="4"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="<?= base_url('sales/surat-jalan') ?>" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i> Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i> Simpan Surat Jalan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Create page loaded');
});
</script>