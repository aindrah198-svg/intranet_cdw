<?php

$title = $title ?? 'Edit Akun';
$active = $active ?? 'bookkeeping';
$subtitle = $subtitle ?? 'Edit Informasi Akun';

// Encode parent data untuk JavaScript
$parentDataJson = json_encode($parentData ?? []);
?>

<?= $this->include('accounting/templates/header') ?>
<?= $this->include('accounting/templates/sidebar') ?>
<?= $this->include('accounting/templates/navbar') ?>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="page-title mb-1">Edit Akun</h2>
                    <p class="page-subtitle text-muted mb-0"><?= $subtitle ?></p>
                </div>
                <div class="btn-group">
                    <a href="<?= site_url('accounting/pembukuan/daftar-akun/detail/' . $coa['id']) ?>" class="btn btn-info">
                        <i class="fas fa-eye me-1"></i> Detail
                    </a>
                    <a href="<?= site_url('accounting/pembukuan/daftar-akun') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <?php if (!empty($accountPath)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-light p-3 rounded">
                    <li class="breadcrumb-item">
                        <a href="<?= site_url('accounting/pembukuan/daftar-akun') ?>" class="text-decoration-none">
                            <i class="fas fa-list me-1"></i> Daftar Akun
                        </a>
                    </li>
                    <?php foreach ($accountPath as $index => $pathItem): ?>
                        <?php if ($index < count($accountPath) - 1): ?>
                            <li class="breadcrumb-item">
                                <a href="<?= site_url('accounting/pembukuan/daftar-akun/detail/' . $pathItem['id']) ?>" class="text-decoration-none">
                                    <?= $pathItem['kode_akun'] ?>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="breadcrumb-item active" aria-current="page">
                                <?= $pathItem['kode_akun'] ?> - <?= $pathItem['nama_akun'] ?>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </nav>
        </div>
    </div>
    <?php endif; ?>

    <!-- Alerts Section -->
    <?php if (session()->getFlashdata('success')): ?>
    <div class="row">
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <?php endif ?>
    
    <?php if (session()->getFlashdata('error')): ?>
    <div class="row">
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <?php endif ?>
    
    <?php if (session()->getFlashdata('errors')): ?>
    <div class="row">
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <?php endif ?>

    <!-- Account Information Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="modern-card">
                <h5 class="card-title mb-3"><i class="fas fa-info-circle me-2"></i> Informasi Akun Saat Ini</h5>
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <small class="text-muted d-block">Kode Akun</small>
                        <strong class="h5"><?= $coa['kode_akun'] ?></strong>
                    </div>
                    <div class="col-md-5 mb-2">
                        <small class="text-muted d-block">Nama Akun</small>
                        <strong class="h5"><?= $coa['nama_akun'] ?></strong>
                    </div>
                    <div class="col-md-2 mb-2">
                        <small class="text-muted d-block">Tipe Akun</small>
                        <span class="badge bg-<?= 
                            $coa['tipe_akun'] == 'Aset' ? 'primary' : 
                            ($coa['tipe_akun'] == 'Kewajiban' ? 'warning' : 
                            ($coa['tipe_akun'] == 'Ekuitas' ? 'success' : 
                            ($coa['tipe_akun'] == 'Pendapatan' ? 'info' : 'danger'))) 
                        ?>">
                            <?= $coa['tipe_akun'] ?>
                        </span>
                    </div>
                    <div class="col-md-2 mb-2">
                        <small class="text-muted d-block">Status</small>
                        <span class="badge bg-<?= $coa['is_active'] ? 'success' : 'danger' ?>">
                            <?= $coa['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="row">
        <div class="col-lg-8">
            <div class="modern-card">
                <form action="<?= site_url('accounting/pembukuan/daftar-akun/update/' . $coa['id']) ?>" method="post" id="coaEditForm">
                    <?= csrf_field() ?>
                    
                    <div class="row g-3">
                        <!-- Parent Selection -->
                        <div class="col-md-6">
                            <label for="parent_id" class="form-label">Parent Akun <span class="text-muted">(Opsional)</span></label>
                            <select name="parent_id" id="parent_id" class="form-select select2" <?= $hasChildren ? 'disabled' : '' ?>>
                                <option value="">-- Pilih Parent --</option>
                                <?php foreach ($parentOptions as $id => $option): ?>
                                    <option value="<?= $id ?>" <?= old('parent_id', $coa['parent_id']) == $id ? 'selected' : '' ?>>
                                        <?= $option ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($hasChildren): ?>
                            <small class="text-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Parent tidak dapat diubah karena akun ini memiliki sub-akun
                            </small>
                            <?php else: ?>
                            <small class="text-muted">Pilih parent untuk membuat akun child. Biarkan kosong untuk akun root.</small>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Informasi Parent Terpilih</label>
                            <div id="parent_info" class="form-control bg-light" style="min-height: 38px; padding: 0.375rem 0.75rem;">
                                <span class="text-muted" id="parent_info_text">Pilih parent untuk melihat informasi</span>
                            </div>
                        </div>

                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <label for="kode_akun" class="form-label">Kode Akun <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="kode_akun" id="kode_akun" class="form-control" 
                                       value="<?= old('kode_akun', $coa['kode_akun']) ?>" placeholder="Contoh: 1-1000, 2-1100" required>
                                <button type="button" class="btn btn-outline-primary" id="validateCodeBtn">
                                    <i class="fas fa-check me-1"></i> Validasi
                                </button>
                            </div>
                            <div id="kodeValidation" class="mt-1"></div>
                            <small class="text-muted">
                                Format: 1-xxxx untuk Aset, 2-xxxx untuk Kewajiban, 3-xxxx untuk Ekuitas, 4-xxxx untuk Pendapatan, 5-xxxx untuk Beban
                            </small>
                        </div>

                        <div class="col-md-6">
                            <label for="nama_akun" class="form-label">Nama Akun <span class="text-danger">*</span></label>
                            <input type="text" name="nama_akun" id="nama_akun" class="form-control" 
                                   value="<?= old('nama_akun', $coa['nama_akun']) ?>" required>
                        </div>

                        <!-- Account Type and Classification -->
                        <div class="col-md-6">
                            <label for="tipe_akun" class="form-label">Tipe Akun <span class="text-danger">*</span></label>
                            <select name="tipe_akun" id="tipe_akun" class="form-select" required <?= $hasChildren ? 'disabled' : '' ?>>
                                <option value="">-- Pilih Tipe Akun --</option>
                                <?php foreach ($tipeAkunOptions as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= old('tipe_akun', $coa['tipe_akun']) == $value ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($hasChildren): ?>
                            <small class="text-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Tipe akun tidak dapat diubah karena akun ini memiliki sub-akun
                            </small>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label for="kategori" class="form-label">Kategori <span class="text-muted">(Opsional)</span></label>
                            <select name="kategori" id="kategori" class="form-select select2">
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($kategoriOptions as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= old('kategori', $coa['kategori']) == $value ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Balance and Account Type -->
                        <div class="col-md-6">
                            <label for="saldo_normal" class="form-label">Saldo Normal <span class="text-danger">*</span></label>
                            <select name="saldo_normal" id="saldo_normal" class="form-select" required>
                                <option value="">-- Pilih Saldo Normal --</option>
                                <?php foreach ($saldoNormalOptions as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= old('saldo_normal', $coa['saldo_normal']) == $value ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">
                                <?php if ($coa['tipe_akun'] == 'Aset'): ?>
                                    Saldo normal Aset: Debit
                                <?php elseif ($coa['tipe_akun'] == 'Kewajiban'): ?>
                                    Saldo normal Kewajiban: Kredit
                                <?php elseif ($coa['tipe_akun'] == 'Ekuitas'): ?>
                                    Saldo normal Ekuitas: Kredit
                                <?php elseif ($coa['tipe_akun'] == 'Pendapatan'): ?>
                                    Saldo normal Pendapatan: Kredit
                                <?php elseif ($coa['tipe_akun'] == 'Beban'): ?>
                                    Saldo normal Beban: Debit
                                <?php endif; ?>
                            </small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jenis Akun <span class="text-danger">*</span></label>
                            <div class="mt-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_header" id="is_header_1" value="1" 
                                           <?= old('is_header', $coa['is_header']) == '1' ? 'checked' : '' ?> <?= $hasChildren ? 'disabled' : '' ?>>
                                    <label class="form-check-label" for="is_header_1">
                                        <i class="fas fa-folder text-primary me-1"></i> Header (Grup Akun)
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_header" id="is_header_0" value="0"
                                           <?= old('is_header', $coa['is_header']) == '0' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_header_0">
                                        <i class="fas fa-file text-secondary me-1"></i> Detail (Akun Transaksi)
                                    </label>
                                </div>
                            </div>
                            <?php if ($hasChildren): ?>
                            <small class="text-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Jenis akun tidak dapat diubah dari Header ke Detail karena memiliki sub-akun
                            </small>
                            <?php else: ?>
                            <small class="text-muted d-block mt-1">
                                Header: untuk pengelompokan, Detail: untuk pencatatan transaksi
                            </small>
                            <?php endif; ?>
                        </div>

                        <!-- Level and Status -->
                        <div class="col-md-6">
                            <label for="level" class="form-label">Level <span class="text-danger">*</span></label>
                            <select name="level" id="level" class="form-select" required <?= $hasChildren ? 'disabled' : '' ?>>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <option value="<?= $i ?>" <?= old('level', $coa['level']) == $i ? 'selected' : '' ?>>
                                        Level <?= $i ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                            <small class="text-muted">Level hirarki akun (1-5)</small>
                            <?php if ($hasChildren): ?>
                            <small class="text-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Level tidak dapat diubah karena akun ini memiliki sub-akun
                            </small>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status Akun</label>
                            <div class="mt-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="is_active" value="1" 
                                           <?= old('is_active', $coa['is_active']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_active">Aktif</label>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label for="deskripsi" class="form-label">Deskripsi <span class="text-muted">(Opsional)</span></label>
                            <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" placeholder="Deskripsi singkat tentang akun ini..."><?= old('deskripsi', $coa['deskripsi']) ?></textarea>
                        </div>

                        <!-- Form Actions -->
                        <div class="col-12 mt-4">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <a href="<?= site_url('accounting/pembukuan/daftar-akun/detail/' . $coa['id']) ?>" class="btn btn-secondary me-2">
                                        <i class="fas fa-times me-1"></i> Batal
                                    </a>
                                    <button type="button" class="btn btn-danger" id="deleteBtn" data-id="<?= $coa['id'] ?>" data-name="<?= $coa['nama_akun'] ?>">
                                        <i class="fas fa-trash me-1"></i> Hapus
                                    </button>
                                </div>
                                <div>
                                    <button type="reset" class="btn btn-warning me-2">
                                        <i class="fas fa-undo me-1"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Help and Information Section -->
        <div class="col-lg-4">
            <!-- Edit Guidelines -->
            <div class="modern-card mb-4">
                <h5 class="card-title mb-3"><i class="fas fa-edit me-2"></i> Panduan Edit</h5>
                <ul class="small mb-0">
                    <li class="mb-2"><strong>Format Kode:</strong> Sesuaikan dengan tipe akun (1-xxxx untuk Aset, dst)</li>
                    <li class="mb-2"><strong>Parent Akun:</strong> Pilih hanya jika akun merupakan bagian dari akun lain</li>
                    <li class="mb-2"><strong>Jenis Akun:</strong> Header untuk pengelompokan, Detail untuk transaksi</li>
                    <li class="mb-2"><strong>Saldo Normal:</strong> Pastikan sesuai dengan tipe akun</li>
                    <li><strong>Level:</strong> Pastikan level > level parent jika ada parent</li>
                    <li>Gunakan tombol "Validasi" untuk memeriksa format kode</li>
                </ul>
            </div>

            <!-- Restrictions -->
            <?php if ($hasChildren): ?>
            <div class="modern-card mb-4 border-warning">
                <h5 class="card-title mb-3 text-warning"><i class="fas fa-exclamation-triangle me-2"></i> Batasan Edit</h5>
                <div class="alert alert-warning mb-0">
                    <p class="mb-2"><strong>Akun ini memiliki <?= $childrenCount ?> sub-akun</strong></p>
                    <p class="small mb-0">Field berikut tidak dapat diubah:</p>
                    <ul class="small mb-0">
                        <li><strong>Tipe Akun</strong> - karena akan mempengaruhi sub-akun</li>
                        <li><strong>Parent Akun</strong> - karena akan memutus hubungan dengan sub-akun</li>
                        <li><strong>Level</strong> - karena akan mengubah struktur hirarki</li>
                        <li><strong>Jenis Akun</strong> - dari Header ke Detail tidak diperbolehkan</li>
                    </ul>
                </div>
            </div>
            <?php endif; ?>

            <!-- Account Summary -->
            <div class="modern-card mb-4">
                <h5 class="card-title mb-3"><i class="fas fa-chart-pie me-2"></i> Ringkasan Akun</h5>
                <div class="small">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tanggal Dibuat:</span>
                        <strong><?= date('d/m/Y H:i', strtotime($coa['created_at'])) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Terakhir Diupdate:</span>
                        <strong><?= date('d/m/Y H:i', strtotime($coa['updated_at'])) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Level Hirarki:</span>
                        <strong>Level <?= $coa['level'] ?></strong>
                    </div>
                    <?php if ($coa['parent_id']): ?>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Parent Akun:</span>
                        <strong>
                            <?php 
                            // Cari parent dari $parentOptions
                            $parentCode = '-';
                            foreach ($parentOptions as $id => $option) {
                                if ($id == $coa['parent_id']) {
                                    $optionParts = explode(' - ', $option);
                                    $parentCode = $optionParts[0] ?? $option;
                                    break;
                                }
                            }
                            echo $parentCode;
                            ?>
                        </strong>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Level Guide -->
            <div class="modern-card mb-4">
                <h5 class="card-title mb-3 text-info"><i class="fas fa-sort-amount-up me-2"></i> Panduan Level</h5>
                <div class="small">
                    <div class="alert alert-info mb-2">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Aturan Level:</strong>
                    </div>
                    <ul class="mb-0">
                        <li><strong>Tanpa Parent:</strong> Harus Level 1</li>
                        <li><strong>Dengan Parent:</strong> Level harus > Level Parent</li>
                        <li><strong>Level Maksimum:</strong> 5</li>
                        <li><strong>Validasi:</strong> Child tipe harus sama dengan parent</li>
                    </ul>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="modern-card">
                <h5 class="card-title mb-3"><i class="fas fa-bolt me-2"></i> Aksi Cepat</h5>
                <div class="d-grid gap-2">
                    <?php if ($coa['is_active']): ?>
                    <button type="button" class="btn btn-outline-danger" id="deactivateBtn" data-id="<?= $coa['id'] ?>">
                        <i class="fas fa-ban me-1"></i> Nonaktifkan Akun
                    </button>
                    <?php else: ?>
                    <button type="button" class="btn btn-outline-success" id="activateBtn" data-id="<?= $coa['id'] ?>">
                        <i class="fas fa-check me-1"></i> Aktifkan Akun
                    </button>
                    <?php endif; ?>
                    <?php if ($coa['is_header'] == 1): ?>
                    <a href="<?= site_url('accounting/pembukuan/daftar-akun/create?parent=' . $coa['id']) ?>" class="btn btn-outline-primary">
                        <i class="fas fa-plus me-1"></i> Tambah Sub-Akun
                    </a>
                    <?php endif; ?>
                    <a href="<?= site_url('accounting/pembukuan/daftar-akun/tree?highlight=' . $coa['id']) ?>" class="btn btn-outline-info">
                        <i class="fas fa-sitemap me-1"></i> Lihat Struktur
                    </a>
                    <a href="<?= site_url('accounting/pembukuan/daftar-akun/print/' . $coa['id']) ?>" class="btn btn-outline-secondary" target="_blank">
                        <i class="fas fa-print me-1"></i> Cetak Detail
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus akun <strong id="deleteAccountName"></strong>?</p>
                <?php if ($hasChildren): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Peringatan:</strong> Akun ini memiliki <?= $childrenCount ?> sub-akun. 
                    Semua sub-akun juga akan dihapus atau dinonaktifkan.
                </div>
                <?php endif; ?>
                <p class="text-danger"><small>Tindakan ini tidak dapat dibatalkan.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="post" style="display: inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Status Change Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Ubah Status Akun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin mengubah status akun <strong><?= $coa['nama_akun'] ?></strong> menjadi 
                <span id="statusActionText"></span>?</p>
                <div id="statusWarning" class="alert alert-warning d-none">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <span id="statusWarningText"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmStatusChange">Ya, Ubah Status</button>
            </div>
        </div>
    </div>
</div>

<!-- Parent Data untuk JavaScript -->
<input type="hidden" id="parentDataJson" value='<?= htmlspecialchars($parentDataJson, ENT_QUOTES, 'UTF-8') ?>'>

<!-- Include Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Parse parent data dari PHP
    const parentData = JSON.parse(document.getElementById('parentDataJson').value || '{}');
    
    // Initialize Select2
    $('.select2').select2({
        placeholder: "Pilih...",
        allowClear: true,
        width: '100%'
    });

    // Elements
    const form = document.getElementById('coaEditForm');
    const parentSelect = document.getElementById('parent_id');
    const parentInfo = document.getElementById('parent_info');
    const parentInfoText = document.getElementById('parent_info_text');
    const kodeInput = document.getElementById('kode_akun');
    const tipeSelect = document.getElementById('tipe_akun');
    const saldoSelect = document.getElementById('saldo_normal');
    const isHeaderRadio1 = document.getElementById('is_header_1');
    const isHeaderRadio0 = document.getElementById('is_header_0');
    const levelSelect = document.getElementById('level');
    const validateBtn = document.getElementById('validateCodeBtn');
    const deleteBtn = document.getElementById('deleteBtn');
    const deactivateBtn = document.getElementById('deactivateBtn');
    const activateBtn = document.getElementById('activateBtn');
    const resetBtn = document.querySelector('button[type="reset"]');

    // Current data dari PHP
    const currentData = {
        kode_akun: "<?= $coa['kode_akun'] ?>",
        nama_akun: "<?= $coa['nama_akun'] ?>",
        tipe_akun: "<?= $coa['tipe_akun'] ?>",
        kategori: "<?= $coa['kategori'] ?? '' ?>",
        saldo_normal: "<?= $coa['saldo_normal'] ?>",
        is_header: "<?= $coa['is_header'] ?>",
        parent_id: "<?= $coa['parent_id'] ?? '' ?>",
        level: "<?= $coa['level'] ?>",
        is_active: "<?= $coa['is_active'] ?>",
        deskripsi: "<?= addslashes($coa['deskripsi'] ?? '') ?>"
    };

    // Has children dari PHP
    const hasChildren = <?= $hasChildren ? 'true' : 'false' ?>;
    const childrenCount = <?= $childrenCount ?>;

    // Default level options (semua level 1-5)
    const defaultLevelOptions = Array.from(levelSelect.options).map(option => ({
        value: option.value,
        text: option.text,
        disabled: false
    }));

    // Update level options berdasarkan parent
    function updateLevelOptions(parentLevel, currentLevel = null) {
        // Reset semua options ke default
        levelSelect.innerHTML = '';
        defaultLevelOptions.forEach(option => {
            const optionElement = document.createElement('option');
            optionElement.value = option.value;
            optionElement.textContent = option.text;
            optionElement.disabled = option.disabled;
            levelSelect.appendChild(optionElement);
        });

        if (parentLevel !== null) {
            // Nonaktifkan level yang <= parent level
            for (let i = 0; i < levelSelect.options.length; i++) {
                const option = levelSelect.options[i];
                const levelValue = parseInt(option.value);
                if (levelValue <= parentLevel) {
                    option.disabled = true;
                    option.style.color = '#ccc';
                }
            }
            
            // Set level jika tidak ada current level yang valid
            if (!currentLevel || currentLevel <= parentLevel) {
                const minLevel = parentLevel + 1;
                const validOption = Array.from(levelSelect.options).find(opt => 
                    parseInt(opt.value) >= minLevel && !opt.disabled
                );
                
                if (validOption) {
                    levelSelect.value = validOption.value;
                    if (!currentLevel) {
                        showNotification(`Level minimal: ${minLevel} (Parent level: ${parentLevel})`, 'info');
                    }
                }
            }
        } else {
            // Jika tidak ada parent, hanya level 1 yang enabled
            for (let i = 0; i < levelSelect.options.length; i++) {
                const option = levelSelect.options[i];
                if (option.value !== '1') {
                    option.disabled = true;
                    option.style.color = '#ccc';
                }
            }
            // Set ke level 1 jika current level bukan 1
            if (currentLevel !== 1) {
                levelSelect.value = '1';
            }
        }
    }

    // Initialize parent info if parent is selected
    const currentParentId = "<?= $coa['parent_id'] ?? '' ?>";
    if (currentParentId && parentData[currentParentId]) {
        const parent = parentData[currentParentId];
        parentInfoText.innerHTML = `
            <strong>${parent.kode} - ${parent.nama}</strong><br>
            <span class="text-muted">${parent.tipe} | Level ${parent.level}</span>
        `;
    }

    // Update parent info when parent is selected
    $(parentSelect).on('change', function() {
        const parentId = this.value;
        
        if (!parentId) {
            parentInfoText.textContent = 'Pilih parent untuk melihat informasi';
            updateLevelOptions(null, parseInt(levelSelect.value));
            return;
        }
        
        if (parentData[parentId]) {
            const parent = parentData[parentId];
            parentInfoText.innerHTML = `
                <strong>${parent.kode} - ${parent.nama}</strong><br>
                <span class="text-muted">${parent.tipe} | Level ${parent.level}</span>
            `;
            
            // Update level options berdasarkan parent
            updateLevelOptions(parent.level, parseInt(levelSelect.value));
            
            // Auto-set tipe akun berdasarkan parent
            if (!hasChildren) {
                tipeSelect.value = parent.tipe;
            }
            
        } else {
            parentInfoText.textContent = 'Informasi parent tidak tersedia';
        }
    });

    // Validate code button
    validateBtn.addEventListener('click', validateKodeInput);

    // Validate kode input
    function validateKodeInput() {
        const kode = kodeInput.value.trim();
        const tipe = tipeSelect.value;
        const validationDiv = document.getElementById('kodeValidation');
        
        if (!kode) {
            validationDiv.innerHTML = '<span class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>Kode akun harus diisi</span>';
            return;
        }
        
        if (!tipe) {
            validationDiv.innerHTML = '<span class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>Tipe akun harus dipilih</span>';
            return;
        }
        
        const prefixMap = {
            'Aset': '1',
            'Kewajiban': '2',
            'Ekuitas': '3',
            'Pendapatan': '4',
            'Beban': '5'
        };
        
        const expectedPrefix = prefixMap[tipe];
        const isValid = kode.startsWith(expectedPrefix + '-');
        
        if (isValid) {
            // AJAX validation for uniqueness
            $.ajax({
                url: '<?= site_url("accounting/pembukuan/daftar-akun/ajax-validate-kode") ?>',
                type: 'GET',
                data: {
                    kode: kode,
                    tipe: tipe,
                    except_id: <?= $coa['id'] ?>
                },
                dataType: 'json',
                success: function(response) {
                    if (response.valid) {
                        validationDiv.innerHTML = '<span class="text-success"><i class="fas fa-check-circle me-1"></i>Kode valid dan tersedia</span>';
                    } else {
                        validationDiv.innerHTML = `<span class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>${response.message}</span>`;
                    }
                },
                error: function() {
                    validationDiv.innerHTML = '<span class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>Gagal memvalidasi kode</span>';
                }
            });
        } else {
            validationDiv.innerHTML = `<span class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>Format tidak sesuai. Harus diawali dengan "${expectedPrefix}-"</span>`;
        }
    }

    // Auto validate on blur
    kodeInput.addEventListener('blur', validateKodeInput);
    tipeSelect.addEventListener('change', validateKodeInput);

    // Auto-fill saldo normal based on account type
    tipeSelect.addEventListener('change', function() {
        const tipe = this.value;
        const saldoMap = {
            'Aset': 'Debit',
            'Kewajiban': 'Kredit',
            'Ekuitas': 'Kredit',
            'Pendapatan': 'Kredit',
            'Beban': 'Debit'
        };
        
        if (tipe && saldoMap[tipe]) {
            saldoSelect.value = saldoMap[tipe];
        }
        
        // Validate kode if it exists
        if (kodeInput.value.trim()) {
            validateKodeInput();
        }
    });

    // Level select change validation
    levelSelect.addEventListener('change', function() {
        const level = parseInt(this.value);
        const parentId = parentSelect.value;
        
        if (parentId && parentData[parentId]) {
            const parentLevel = parentData[parentId].level;
            
            if (level <= parentLevel) {
                showNotification(`Level harus lebih tinggi dari parent level (${parentLevel})`, 'error');
                // Reset ke level yang valid
                updateLevelOptions(parentLevel, level);
            }
        } else if (level !== 1) {
            showNotification('Akun tanpa parent harus Level 1', 'error');
            this.value = 1;
        }
    });

    // Delete button
    deleteBtn.addEventListener('click', function() {
        const accountId = this.getAttribute('data-id');
        const accountName = this.getAttribute('data-name');
        
        document.getElementById('deleteAccountName').textContent = accountName;
        document.getElementById('deleteForm').action = '<?= site_url("accounting/pembukuan/daftar-akun/delete") ?>/' + accountId;
        
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    });

    // Deactivate button (AJAX)
    if (deactivateBtn) {
        deactivateBtn.addEventListener('click', function() {
            const accountId = this.getAttribute('data-id');
            
            document.getElementById('statusActionText').textContent = 'Nonaktif';
            
            // Show warning if account has children
            if (hasChildren) {
                document.getElementById('statusWarningText').textContent = 
                    `Akun ini memiliki ${childrenCount} sub-akun. Semua sub-akun juga akan dinonaktifkan.`;
                document.getElementById('statusWarning').classList.remove('d-none');
            }
            
            // Set up confirm button
            const confirmBtn = document.getElementById('confirmStatusChange');
            confirmBtn.onclick = function() {
                $.ajax({
                    url: '<?= site_url("accounting/pembukuan/daftar-akun/toggle-status") ?>/' + accountId,
                    type: 'POST',
                    data: {
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
                        is_active: 0
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showNotification('Akun berhasil dinonaktifkan', 'success');
                            setTimeout(function() {
                                window.location.reload();
                            }, 1500);
                        } else {
                            showNotification(response.message, 'error');
                        }
                        bootstrap.Modal.getInstance(document.getElementById('statusModal')).hide();
                    },
                    error: function() {
                        showNotification('Gagal mengubah status akun', 'error');
                        bootstrap.Modal.getInstance(document.getElementById('statusModal')).hide();
                    }
                });
            };
            
            const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
            statusModal.show();
        });
    }

    // Activate button (AJAX)
    if (activateBtn) {
        activateBtn.addEventListener('click', function() {
            const accountId = this.getAttribute('data-id');
            
            document.getElementById('statusActionText').textContent = 'Aktif';
            document.getElementById('statusWarning').classList.add('d-none');
            
            // Set up confirm button
            const confirmBtn = document.getElementById('confirmStatusChange');
            confirmBtn.onclick = function() {
                $.ajax({
                    url: '<?= site_url("accounting/pembukuan/daftar-akun/toggle-status") ?>/' + accountId,
                    type: 'POST',
                    data: {
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
                        is_active: 1
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showNotification('Akun berhasil diaktifkan', 'success');
                            setTimeout(function() {
                                window.location.reload();
                            }, 1500);
                        } else {
                            showNotification(response.message, 'error');
                        }
                        bootstrap.Modal.getInstance(document.getElementById('statusModal')).hide();
                    },
                    error: function() {
                        showNotification('Gagal mengubah status akun', 'error');
                        bootstrap.Modal.getInstance(document.getElementById('statusModal')).hide();
                    }
                });
            };
            
            const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
            statusModal.show();
        });
    }

    // Reset form button
    resetBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        if (confirm('Anda yakin ingin mengembalikan semua perubahan ke nilai awal?')) {
            // Reset all form fields to original values
            kodeInput.value = currentData.kode_akun;
            document.getElementById('nama_akun').value = currentData.nama_akun;
            tipeSelect.value = currentData.tipe_akun;
            document.getElementById('kategori').value = currentData.kategori;
            saldoSelect.value = currentData.saldo_normal;
            
            // Reset radio buttons
            if (currentData.is_header === '1') {
                isHeaderRadio1.checked = true;
                isHeaderRadio0.checked = false;
            } else {
                isHeaderRadio1.checked = false;
                isHeaderRadio0.checked = true;
            }
            
            // Reset parent
            parentSelect.value = currentData.parent_id || '';
            $(parentSelect).trigger('change');
            
            // Reset level dengan memperhitungkan parent
            if (currentData.parent_id && parentData[currentData.parent_id]) {
                updateLevelOptions(parentData[currentData.parent_id].level, parseInt(currentData.level));
            } else {
                updateLevelOptions(null, parseInt(currentData.level));
            }
            levelSelect.value = currentData.level;
            
            document.getElementById('is_active').checked = currentData.is_active === '1';
            document.getElementById('deskripsi').value = currentData.deskripsi;
            
            // Clear validation message
            document.getElementById('kodeValidation').innerHTML = '';
            
            // Reset Select2
            $('.select2').trigger('change');
            
            showNotification('Form berhasil direset ke nilai awal', 'info');
        }
    });

    // Form submission validation
    form.addEventListener('submit', function(e) {
        const kode = kodeInput.value.trim();
        const tipe = tipeSelect.value;
        const isHeader = document.querySelector('input[name="is_header"]:checked')?.value;
        const parentId = parentSelect.value;
        const level = parseInt(levelSelect.value);
        
        // Validasi jika akun punya children
        if (hasChildren) {
            // Tipe akun tidak boleh diubah
            if (tipe !== currentData.tipe_akun) {
                e.preventDefault();
                alert('Tipe akun tidak dapat diubah karena akun ini memiliki sub-akun');
                tipeSelect.focus();
                return false;
            }
            
            // Level tidak boleh diubah
            if (level !== parseInt(currentData.level)) {
                e.preventDefault();
                alert('Level tidak dapat diubah karena akun ini memiliki sub-akun');
                levelSelect.focus();
                return false;
            }
        }
        
        // Validasi level
        if (parentId && parentData[parentId]) {
            const parentLevel = parentData[parentId].level;
            if (level <= parentLevel) {
                e.preventDefault();
                alert(`Level harus lebih tinggi dari level parent (Parent level: ${parentLevel})`);
                levelSelect.focus();
                return false;
            }
        } else if (level !== 1) {
            e.preventDefault();
            alert('Akun tanpa parent harus Level 1');
            levelSelect.focus();
            return false;
        }
        
        // Validasi level maksimum
        if (level > 5) {
            e.preventDefault();
            alert('Level maksimum adalah 5');
            levelSelect.focus();
            return false;
        }
        
        // Jika tidak ada parent, pastikan adalah header
        if (!parentId && isHeader === '0') {
            e.preventDefault();
            alert('Akun tanpa parent harus merupakan Header account');
            isHeaderRadio1.focus();
            return false;
        }
        
        // Jika kode diisi, validasi format
        if (kode && tipe) {
            const prefixMap = {
                'Aset': '1',
                'Kewajiban': '2',
                'Ekuitas': '3',
                'Pendapatan': '4',
                'Beban': '5'
            };
            
            const expectedPrefix = prefixMap[tipe];
            const regex = /^[1-5](-\d+)*$/;
            
            if (!regex.test(kode)) {
                e.preventDefault();
                alert('Format kode tidak valid. Contoh: 1-1000, 1-1100-01');
                kodeInput.focus();
                return false;
            }
            
            if (!kode.startsWith(expectedPrefix + '-')) {
                e.preventDefault();
                alert(`Format kode tidak sesuai. Untuk tipe akun "${tipe}", kode harus diawali dengan "${expectedPrefix}-"`);
                kodeInput.focus();
                return false;
            }
        }
        
        // Jika ada parent, validasi tipe akun sama
        if (parentId && parentData[parentId]) {
            const parentTipe = parentData[parentId].tipe;
            if (tipe !== parentTipe) {
                e.preventDefault();
                alert(`Tipe akun harus sama dengan parent. Parent memiliki tipe: ${parentTipe}`);
                tipeSelect.focus();
                return false;
            }
        }
        
        // Validasi parent is not itself
        if (parentId && parentId == "<?= $coa['id'] ?>") {
            e.preventDefault();
            alert('Akun tidak dapat menjadi parent dari dirinya sendiri');
            return false;
        }
        
        // Validasi header cannot have parent that is detail
        if (parentId && isHeader === '1') {
            const parent = parentData[parentId];
            if (parent && parent.is_header === 0) {
                e.preventDefault();
                alert('Parent yang dipilih adalah akun detail. Akun detail tidak dapat memiliki child. Pilih parent yang merupakan akun header.');
                return false;
            }
        }
        
        // Validasi cannot change from header to detail if has children
        if (hasChildren && currentData.is_header === '1' && isHeader === '0') {
            e.preventDefault();
            alert('Akun ini memiliki sub-akun. Tidak dapat mengubah dari Header ke Detail.');
            return false;
        }
        
        // Show loading
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';
        submitBtn.disabled = true;
        
        // Allow form submission
        return true;
    });

    // Show changes warning
    let formChanged = false;
    const formElements = form.querySelectorAll('input, select, textarea');
    
    formElements.forEach(element => {
        element.addEventListener('change', function() {
            if (!formChanged) {
                formChanged = true;
                window.onbeforeunload = function() {
                    return 'Anda memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman?';
                };
            }
        });
    });
    
    form.addEventListener('submit', function() {
        window.onbeforeunload = null;
    });

    // Helper function to show notification
    function showNotification(message, type = 'info') {
        // Remove existing notifications
        document.querySelectorAll('.custom-notification').forEach(el => el.remove());
        
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `custom-notification alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 1055; min-width: 300px;';
        
        let icon = 'info-circle';
        if (type === 'success') icon = 'check-circle';
        if (type === 'warning' || type === 'error') icon = 'exclamation-triangle';
        
        notification.innerHTML = `
            <i class="fas fa-${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 3000);
    }

    // Initialize level options berdasarkan parent saat ini
    if (currentParentId && parentData[currentParentId]) {
        updateLevelOptions(parentData[currentParentId].level, parseInt(currentData.level));
    } else {
        updateLevelOptions(null, parseInt(currentData.level));
    }

    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>

<style>
.custom-notification {
    animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

#validateCodeBtn:hover {
    background-color: #4e73df;
    color: white;
    border-color: #4e73df;
}

.form-control:focus, .form-select:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.modern-card.border-warning {
    border-left: 4px solid #ffc107 !important;
}

.select2-container--default .select2-selection--single {
    border: 1px solid #d1d3e2;
    border-radius: 0.35rem;
    height: 38px;
    padding: 0.375rem 0.75rem;
}

.select2-container--default .select2-selection--single:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.select2-container .select2-selection--single .select2-selection__rendered {
    padding-left: 0;
    line-height: 24px;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px;
}

.breadcrumb {
    background-color: #f8f9fa;
    border-radius: 0.35rem;
    padding: 0.75rem 1rem;
}

.breadcrumb-item a {
    color: #4e73df;
    text-decoration: none;
}

.breadcrumb-item a:hover {
    color: #2e59d9;
    text-decoration: underline;
}

.breadcrumb-item.active {
    color: #6c757d;
}

select option:disabled {
    color: #ccc;
    background-color: #f8f9fa;
}
</style>

<?= $this->include('accounting/templates/footer') ?>