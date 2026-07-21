<?= $this->include('accounting/templates/header') ?>
<?= $this->include('accounting/templates/sidebar') ?>
<?= $this->include('accounting/templates/navbar') ?>

<div class="container-fluid py-4">
    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif ?>
    
    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif ?>

    <!-- Header Section -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Detail Batch</h2>
                    <p class="text-muted mb-0">Batch ID: <strong><?= $batch_id ?></strong></p>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-download me-1"></i> Export
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" onclick="exportData('excel')"><i class="fas fa-file-excel text-success me-2"></i> Excel</a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportData('pdf')"><i class="fas fa-file-pdf text-danger me-2"></i> PDF</a></li>
                    </ul>
                    <a href="<?= site_url('accounting/pembukuan/buku-besar/jurnal-posted') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Jurnal</h6>
                    <h3><?= number_format($total_jurnal ?? 0) ?></h3>
                    <small>Dalam batch ini</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Baris Transaksi</h6>
                    <h3><?= number_format($total_baris ?? 0) ?></h3>
                    <small>Debit + Kredit</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Jurnal Sukses</h6>
                    <h3>
                        <?php 
                        $successCount = 0;
                        foreach ($batch_detail as $jurnal) {
                            if (($jurnal['status'] ?? '') == 'processed') {
                                $successCount++;
                            }
                        }
                        echo number_format($successCount);
                        ?>
                    </h3>
                    <small>Status Processed</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Jurnal dalam Batch -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-layer-group me-2"></i> 
                Daftar Jurnal dalam Batch
                <span class="badge bg-primary ms-2"><?= $total_jurnal ?? 0 ?> Jurnal</span>
                <span class="badge bg-secondary ms-1"><?= $total_baris ?? 0 ?> Baris</span>
            </h5>
        </div>
        <div class="card-body">
            <?php if (empty($batch_detail)): ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                <h4>Tidak Ada Data</h4>
                <p class="text-muted">Batch ini tidak memiliki data transaksi</p>
                <a href="<?= site_url('accounting/pembukuan/buku-besar/jurnal-posted') ?>" class="btn btn-primary mt-3">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Jurnal Posted
                </a>
            </div>
            <?php else: ?>
            
            <!-- Summary per Jurnal -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">Tanggal</th>
                            <th width="15%">Nomor Jurnal</th>
                            <th width="30%">Keterangan</th>
                            <th width="10%">Tipe</th>
                            <th width="12%" class="text-end">Total Debit</th>
                            <th width="12%" class="text-end">Total Kredit</th>
                            <th width="6%">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        foreach ($batch_detail as $jurnal): 
                            $badgeClass = match($jurnal['status'] ?? 'pending') {
                                'processed' => 'bg-success',
                                'failed' => 'bg-danger',
                                'void' => 'bg-secondary',
                                default => 'bg-warning'
                            };
                            $badgeText = match($jurnal['status'] ?? 'pending') {
                                'processed' => 'Processed',
                                'failed' => 'Failed',
                                'void' => 'Void',
                                default => ucfirst($jurnal['status'] ?? 'Pending')
                            };
                            
                            $tipeBadge = match($jurnal['tipe_jurnal'] ?? 'umum') {
                                'mutasi_bank' => 'bg-info',
                                'penyesuaian' => 'bg-warning',
                                default => 'bg-secondary'
                            };
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= date('d/m/Y', strtotime($jurnal['tanggal'])) ?></td>
                            <td>
                                <a href="<?= site_url('accounting/pembukuan/buku-besar/jurnal-posted/detail/' . $jurnal['jurnal_id']) ?>">
                                    <strong><?= $jurnal['nomor_jurnal'] ?></strong>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($jurnal['keterangan']) ?></td>
                            <td><span class="badge <?= $tipeBadge ?>"><?= $jurnal['tipe_jurnal'] ?? 'Umum' ?></span></td>
                            <td class="text-end text-success">Rp <?= number_format($jurnal['total_debit'], 0, ',', '.') ?></td>
                            <td class="text-end text-danger">Rp <?= number_format($jurnal['total_kredit'], 0, ',', '.') ?></td>
                            <td><span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span></td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <th colspan="5" class="text-end">TOTAL KESELURUHAN</th>
                            <th class="text-end">Rp <?= number_format(array_sum(array_column($batch_detail, 'total_debit')), 0, ',', '.') ?></th>
                            <th class="text-end">Rp <?= number_format(array_sum(array_column($batch_detail, 'total_kredit')), 0, ',', '.') ?></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Detail Transaksi per Jurnal (Collapsible) -->
            <div class="mt-4">
                <h6 class="mb-3"><i class="fas fa-list me-2"></i> Detail Transaksi per Jurnal</h6>
                
                <?php foreach ($batch_detail as $index => $jurnal): ?>
                <div class="card mb-3 border">
                    <div class="card-header bg-light cursor-pointer" onclick="toggleDetail(<?= $index ?>)" style="cursor: pointer;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?= $jurnal['nomor_jurnal'] ?></strong>
                                <span class="text-muted ms-2">(<?= date('d/m/Y', strtotime($jurnal['tanggal'])) ?>)</span>
                                <span class="badge bg-secondary ms-2"><?= $jurnal['tipe_jurnal'] ?? 'Umum' ?></span>
                            </div>
                            <div>
                                <span class="badge <?= match($jurnal['status'] ?? 'pending') { 'processed' => 'bg-success', 'failed' => 'bg-danger', 'void' => 'bg-secondary', default => 'bg-warning' } ?> me-2">
                                    <?= ucfirst($jurnal['status'] ?? 'Pending') ?>
                                </span>
                                <i class="fas fa-chevron-down" id="icon-<?= $index ?>"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0" id="detail-<?= $index ?>" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="table-secondary">
                                    <tr>
                                        <th width="15%">Kode Akun</th>
                                        <th width="30%">Nama Akun</th>
                                        <th width="35%">Keterangan</th>
                                        <th width="10%" class="text-end">Debit</th>
                                        <th width="10%" class="text-end">Kredit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($jurnal['entries'] as $entry): ?>
                                    <tr>
                                        <td><?= $entry['kode_akun'] ?></td>
                                        <td><?= $entry['nama_akun'] ?></td>
                                        <td><?= htmlspecialchars($entry['keterangan'] ?? '-') ?></td>
                                        <td class="text-end text-success"><?= $entry['debit'] > 0 ? 'Rp ' . number_format($entry['debit'], 0, ',', '.') : '-' ?></td>
                                        <td class="text-end text-danger"><?= $entry['kredit'] > 0 ? 'Rp ' . number_format($entry['kredit'], 0, ',', '.') : '-' ?></td>
                                    </tr>
                                    <?php endforeach ?>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="3" class="text-end">TOTAL</th>
                                        <th class="text-end text-success">Rp <?= number_format($jurnal['total_debit'], 0, ',', '.') ?></th>
                                        <th class="text-end text-danger">Rp <?= number_format($jurnal['total_kredit'], 0, ',', '.') ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endforeach ?>
            </div>

            <!-- Action Buttons -->
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <a href="<?= site_url('accounting/pembukuan/buku-besar/jurnal-posted') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Jurnal Posted
                    </a>
                    <button type="button" class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                </div>
            </div>
            <?php endif ?>
        </div>
    </div>
</div>

<script>
function toggleDetail(index) {
    const detail = document.getElementById('detail-' + index);
    const icon = document.getElementById('icon-' + index);
    
    if (detail.style.display === 'none') {
        detail.style.display = 'block';
        icon.style.transform = 'rotate(180deg)';
    } else {
        detail.style.display = 'none';
        icon.style.transform = 'rotate(0deg)';
    }
}

function exportData(type) {
    const batchId = '<?= $batch_id ?>';
    window.location.href = '<?= site_url("accounting/pembukuan/buku-besar/export-batch") ?>?batch_id=' + batchId + '&type=' + type;
}
</script>

<style>
.cursor-pointer {
    cursor: pointer;
}
.cursor-pointer:hover {
    background-color: #e9ecef;
}
#icon-0, #icon-1, #icon-2, #icon-3, #icon-4, #icon-5, #icon-6, #icon-7, #icon-8, #icon-9 {
    transition: transform 0.3s ease;
}
</style>

<?= $this->include('accounting/templates/footer') ?>