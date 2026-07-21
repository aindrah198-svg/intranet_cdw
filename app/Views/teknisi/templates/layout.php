<?= $this->extend('teknisi/templates/header') ?>
<?= $this->extend('teknisi/templates/sidebar') ?>
<?= $this->extend('teknisi/templates/navbar') ?>

<?= $this->section('content') ?>
    <?= $this->renderSection('content') ?>
<?= $this->endSection() ?>

<?= $this->include('teknisi/templates/footer') ?>