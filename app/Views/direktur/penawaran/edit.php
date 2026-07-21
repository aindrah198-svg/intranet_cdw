<?php
// app/Views/direktur/penawaran/edit.php
$title = 'Edit Penawaran';
$subtitle = 'Ubah Pengajuan Penawaran';
$user = $user ?? session()->get();
$active = 'penawaran';
?>

<?= $this->include('direktur/templates/header') ?>
<?= $this->include('direktur/templates/sidebar') ?>
<?= $this->include('direktur/templates/navbar') ?>

<style>
    /* Gunakan style yang sama dengan create.php */
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title mb-1">
                <i class="fas fa-edit me-2"></i>Edit Penawaran
            </h1>
            <p class="page-subtitle">Nomor: <?= $penawaran['nomor_penawaran'] ?? '-' ?></p>
        </div>
        <div>
            <a href="<?= base_url('direktur/penawaran/detail/' . $penawaran['id']) ?>" 
               class="btn btn-modern-outline">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Form edit (sama dengan create tapi dengan data existing) -->
    <!-- ... isi form edit ... -->
</div>

<script>
// JavaScript sama dengan create.php
</script>

<?= $this->include('direktur/templates/footer') ?>