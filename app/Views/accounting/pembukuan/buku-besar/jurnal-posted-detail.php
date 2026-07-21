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
                    <h2 class="mb-1">Detail Jurnal Posted</h2>
                    <p class="text-muted mb-0"><?= $jurnal['header']['nomor_jurnal'] ?? '-' ?></p>
                </div>
                <div>
                    <a href="<?= site_url('accounting/pembukuan/buku-besar/jurnal-posted') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Jurnal Header Info -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Informasi Jurnal</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <small class="text-muted">Nomor Jurnal</small>
                        <h5 class="mb-0"><?= $jurnal['header']['nomor_jurnal'] ?? '-' ?></h5>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <small class="text-muted">Tanggal</small>
                        <h5 class="mb-0"><?= date('d/m/Y', strtotime($jurnal['header']['tanggal'] ?? date('Y-m-d'))) ?></h5>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <small class="text-muted">Tipe Jurnal</small>
                        <?php 
                        $tipeBadge = match($jurnal['header']['tipe_jurnal'] ?? 'umum') {
                            'mutasi_bank' => 'bg-info',
                            'penyesuaian' => 'bg-warning',
                            default => 'bg-secondary'
                        };
                        ?>
                        <h5><span class="badge <?= $tipeBadge ?>"><?= $jurnal['header']['tipe_jurnal'] ?? 'Umum' ?></span></h5>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <small class="text-muted">Status</small>
                        <?php 
                        $statusBadge = match($jurnal['header']['status'] ?? 'pending') {
                            'processed' => 'bg-success',
                            'failed' => 'bg-danger',
                            'void' => 'bg-secondary',
                            default => 'bg-warning'
                        };
                        ?>
                        <h5><span class="badge <?= $statusBadge ?>"><?= ucfirst($jurnal['header']['status'] ?? 'Pending') ?></span></h5>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="border rounded p-3">
                        <small class="text-muted">Keterangan</small>
                        <p class="mb-0"><?= htmlspecialchars($jurnal['header']['keterangan'] ?? '-') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Batch Info (Jika Ada) -->
    <?php if (!empty($jurnal['header']['batch_id'])): ?>
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-layer-group me-2"></i> Informasi Batch</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="border rounded p-3">
                        <small class="text-muted">Batch ID</small>
                        <h6 class="mb-0">
                            <a href="<?= site_url('accounting/pembukuan/buku-besar/batch-detail/' . $jurnal['header']['batch_id']) ?>">
                                <?= $jurnal['header']['batch_id'] ?>
                            </a>
                        </h6>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3">
                        <small class="text-muted">Waktu Posting</small>
                        <h6 class="mb-0"><?= !empty($jurnal['header']['processed_at']) ? date('d/m/Y H:i:s', strtotime($jurnal['header']['processed_at'])) : '-' ?></h6>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3">
                        <small class="text-muted">Diposting Oleh</small>
                        <h6 class="mb-0"><?= $jurnal['processed_by_name'] ?? '-' ?></h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Tabel Transaksi -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i> Detail Transaksi</h5>
        </div>
        <div class="card-body">
            <!-- Debit Entries -->
            <h6 class="text-success mb-3">
                <i class="fas fa-arrow-down me-2"></i> Entri Debit
            </h6>
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Kode Akun</th>
                            <th width="30%">Nama Akun</th>
                            <th width="35%">Keterangan</th>
                            <th width="15%" class="text-end">Debit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($jurnal['debit_entries'])): ?>
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada entri debit</td>
                        </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($jurnal['debit_entries'] as $entry): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><span class="coa-code"><?= $entry['kode_akun'] ?></span></td>
                                <td><?= $entry['nama_akun'] ?></td>
                                <td><?= htmlspecialchars($entry['entry_keterangan'] ?? '-') ?></td>
                                <td class="text-end text-success">Rp <?= number_format($entry['debit'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach ?>
                        <?php endif ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="4" class="text-end">TOTAL DEBIT</th>
                            <th class="text-end text-success">Rp <?= number_format($jurnal['total_debit'], 0, ',', '.') ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Credit Entries -->
            <h6 class="text-danger mb-3">
                <i class="fas fa-arrow-up me-2"></i> Entri Kredit
            </h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Kode Akun</th>
                            <th width="30%">Nama Akun</th>
                            <th width="35%">Keterangan</th>
                            <th width="15%" class="text-end">Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($jurnal['credit_entries'])): ?>
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada entri kredit</td>
                        </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($jurnal['credit_entries'] as $entry): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><span class="coa-code"><?= $entry['kode_akun'] ?></span></td>
                                <td><?= $entry['nama_akun'] ?></td>
                                <td><?= htmlspecialchars($entry['entry_keterangan'] ?? '-') ?></td>
                                <td class="text-end text-danger">Rp <?= number_format($entry['kredit'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach ?>
                        <?php endif ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="4" class="text-end">TOTAL KREDIT</th>
                            <th class="text-end text-danger">Rp <?= number_format($jurnal['total_kredit'], 0, ',', '.') ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Balance Status -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="alert <?= $jurnal['is_balance'] ? 'alert-success' : 'alert-danger' ?> text-center">
                        <i class="fas <?= $jurnal['is_balance'] ? 'fa-check-circle' : 'fa-exclamation-triangle' ?> me-2"></i>
                        <strong>Status Jurnal:</strong> 
                        <?= $jurnal['is_balance'] ? 'BALANCE (Debit = Kredit)' : 'TIDAK BALANCE' ?>
                        <?php if (!$jurnal['is_balance']): ?>
                            <br><small>Selisih: Rp <?= number_format(abs($jurnal['total_debit'] - $jurnal['total_kredit']), 0, ',', '.') ?></small>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mt-4">
        <div class="col-12 text-center">
            <a href="<?= site_url('accounting/pembukuan/buku-besar/jurnal-posted') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <?php if (($jurnal['header']['status'] ?? '') == 'processed'): ?>
            <button type="button" class="btn btn-danger" onclick="voidJurnal()">
                <i class="fas fa-ban me-1"></i> Void Jurnal
            </button>
            <?php endif; ?>
            <?php if (!empty($jurnal['header']['batch_id'])): ?>
            <a href="<?= site_url('accounting/pembukuan/buku-besar/batch-detail/' . $jurnal['header']['batch_id']) ?>" class="btn btn-info">
                <i class="fas fa-layer-group me-1"></i> Lihat Batch
            </a>
            <?php endif; ?>
            <button type="button" class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print
            </button>
        </div>
    </div>
</div>

<!-- Modal Void Jurnal -->
<div class="modal fade" id="voidModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-ban me-2"></i> Void Jurnal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Perhatian!</strong> Void jurnal akan membatalkan jurnal ini dari buku besar.
                    Tindakan ini <strong>TIDAK DAPAT DIURUNGKAN</strong> secara otomatis.
                </div>
                <form id="voidForm">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Jurnal</label>
                        <input type="text" class="form-control" value="<?= $jurnal['header']['nomor_jurnal'] ?? '-' ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alasan Pembatalan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="reason" id="voidReason" rows="3" required placeholder="Masukkan alasan pembatalan..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="submitVoid()">Ya, Void Jurnal</button>
            </div>
        </div>
    </div>
</div>

<script>
function voidJurnal() {
    new bootstrap.Modal(document.getElementById('voidModal')).show();
}

function submitVoid() {
    const reason = document.getElementById('voidReason').value;
    if (!reason) {
        alert('Alasan pembatalan harus diisi');
        return;
    }
    
    const formData = new URLSearchParams();
    formData.append('reason', reason);
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
    
    fetch('<?= site_url("accounting/pembukuan/buku-besar/void-jurnal/") . ($jurnal['header']['jurnal_id'] ?? 0) ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('voidModal')).hide();
            alert(data.message);
            window.location.reload();
        } else {
            alert('Gagal: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}
</script>

<style>
.coa-code {
    font-family: 'Courier New', monospace;
    background: #f8f9fa;
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 600;
    color: #2c5aa0;
}
</style>

<?= $this->include('accounting/templates/footer') ?>