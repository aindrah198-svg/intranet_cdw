<?php
$title = $title ?? 'Detail Surat Jalan';
$active = $active ?? 'surat_jalan';
?>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-truck me-2"></i>
                        <?= $title ?>
                    </h1>
                    <p class="text-muted">
                        No: <?= htmlspecialchars($suratJalan['nomor_surat_jalan']) ?>
                        <?php if (!empty($suratJalan['nama_project'])): ?>
                        | Project: <?= htmlspecialchars($suratJalan['nama_project']) ?>
                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <a href="<?= base_url('sales/surat-jalan') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Left Column: Informasi Surat Jalan -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>
                        Informasi Surat Jalan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">No. Surat Jalan</th>
                                    <td><?= htmlspecialchars($suratJalan['nomor_surat_jalan']) ?></td>
                                </tr>
                                <tr>
                                    <th>Tanggal Kirim</th>
                                    <td>
                                        <?php 
                                        if (!empty($suratJalan['tanggal_kirim']) && $suratJalan['tanggal_kirim'] != '0000-00-00') {
                                            echo date('d/m/Y', strtotime($suratJalan['tanggal_kirim']));
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Project</th>
                                    <td><?= htmlspecialchars($suratJalan['nama_project'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <th>Client</th>
                                    <td><?= htmlspecialchars($suratJalan['nama_perusahaan'] ?? '-') ?></td>
                                </tr>
                                <?php if (!empty($suratJalan['nomor_invoice'])): ?>
                                <tr>
                                    <th>Invoice</th>
                                    <td><?= htmlspecialchars($suratJalan['nomor_invoice']) ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Status</th>
                                    <td>
                                        <?php 
                                        $statusColors = [
                                            'diproses' => 'warning',
                                            'dikirim' => 'info',
                                            'diterima' => 'success',
                                            'dibatalkan' => 'danger'
                                        ];
                                        
                                        $statusText = [
                                            'diproses' => 'Diproses',
                                            'dikirim' => 'Dikirim',
                                            'diterima' => 'Diterima',
                                            'dibatalkan' => 'Dibatalkan'
                                        ];
                                        ?>
                                        <span class="badge bg-<?= $statusColors[$suratJalan['status']] ?? 'secondary' ?>">
                                            <?= $statusText[$suratJalan['status']] ?? $suratJalan['status'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Sopir</th>
                                    <td><?= htmlspecialchars($suratJalan['sopir'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <th>No. Kendaraan</th>
                                    <td><?= htmlspecialchars($suratJalan['no_kendaraan'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <th>Dibuat Oleh</th>
                                    <td><?= htmlspecialchars($suratJalan['created_by_name'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <th>Dibuat Tanggal</th>
                                    <td>
                                        <?php 
                                        if (!empty($suratJalan['created_at'])) {
                                            echo date('d/m/Y H:i', strtotime($suratJalan['created_at']));
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Penerima -->
                    <div class="mt-4">
                        <h6 class="font-weight-bold text-primary mb-3">
                            <i class="fas fa-user me-2"></i>Informasi Penerima
                        </h6>
                        <table class="table table-sm">
                            <tr>
                                <th width="30%">Nama Perusahaan</th>
                                <td><?= htmlspecialchars($suratJalan['penerima_perusahaan'] ?? $suratJalan['nama_perusahaan'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <th>UP / Penanggung Jawab</th>
                                <td><?= htmlspecialchars($suratJalan['penerima_up'] ?? $suratJalan['penerima_nama'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <th>Alamat Pengiriman</th>
                                <td><?= htmlspecialchars($suratJalan['alamat_pengiriman'] ?? '-') ?></td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Keterangan -->
                    <?php if (!empty($suratJalan['keterangan'])): ?>
                    <div class="mt-4">
                        <h6 class="font-weight-bold text-primary mb-3">
                            <i class="fas fa-sticky-note me-2"></i>Keterangan
                        </h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                <?= nl2br(htmlspecialchars($suratJalan['keterangan'])) ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column: Items dan Status -->
        <div class="col-md-4">
            <!-- Items -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-boxes me-2"></i>
                        Daftar Barang
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (empty($items)): ?>
                        <p class="text-muted text-center">Tidak ada data barang</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Barang</th>
                                        <th>Qty</th>
                                        <th>Satuan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $index => $item): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                                        <td><?= number_format($item['qty'], 2) ?></td>
                                        <td><?= htmlspecialchars($item['satuan']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cogs me-2"></i>
                        Aksi
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('sales/surat-jalan') ?>" class="btn btn-secondary">
                            <i class="fas fa-list me-2"></i> Kembali ke Daftar
                        </a>
                        <button class="btn btn-primary" onclick="window.print()">
                            <i class="fas fa-print me-2"></i> Cetak Surat Jalan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Detail page loaded');
});
</script>