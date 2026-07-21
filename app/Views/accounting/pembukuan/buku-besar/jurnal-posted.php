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
                    <h2 class="mb-1">Jurnal Posted</h2>
                    <p class="text-muted mb-0">Daftar jurnal yang sudah diposting ke buku besar</p>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-download me-1"></i> Export
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" onclick="exportData('excel')"><i class="fas fa-file-excel text-success me-2"></i> Excel</a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportData('pdf')"><i class="fas fa-file-pdf text-danger me-2"></i> PDF</a></li>
                    </ul>
                    <a href="<?= site_url('accounting/pembukuan/buku-besar') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Jurnal</h6>
                    <h3><?= number_format($pager['total'] ?? 0) ?></h3>
                    <small>Sudah diposting</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Debit</h6>
                    <h3>Rp <?= number_format(array_sum(array_column($jurnal, 'total_debit')), 0, ',', '.') ?></h3>
                    <small>Semua transaksi</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Kredit</h6>
                    <h3>Rp <?= number_format(array_sum(array_column($jurnal, 'total_kredit')), 0, ',', '.') ?></h3>
                    <small>Semua transaksi</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Status</h6>
                    <h3><?= $pager['total'] > 0 ? 'Active' : 'Empty' ?></h3>
                    <small>Jurnal aktif</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i> Filter</h5>
        </div>
        <div class="card-body">
            <form method="get" action="<?= site_url('accounting/pembukuan/buku-besar/jurnal-posted') ?>" id="filterForm">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" name="tanggal_mulai" value="<?= $filters['tanggal_mulai'] ?? '' ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control" name="tanggal_selesai" value="<?= $filters['tanggal_selesai'] ?? '' ?>">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label">Tipe Jurnal</label>
                        <select class="form-select" name="tipe_jurnal">
                            <option value="">-- Semua --</option>
                            <option value="umum" <?= ($filters['tipe_jurnal'] ?? '') == 'umum' ? 'selected' : '' ?>>Jurnal Umum</option>
                            <option value="penyesuaian" <?= ($filters['tipe_jurnal'] ?? '') == 'penyesuaian' ? 'selected' : '' ?>>Jurnal Penyesuaian</option>
                            <option value="mutasi_bank" <?= ($filters['tipe_jurnal'] ?? '') == 'mutasi_bank' ? 'selected' : '' ?>>Mutasi Bank</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">-- Semua --</option>
                            <option value="processed" <?= ($filters['status'] ?? '') == 'processed' ? 'selected' : '' ?>>Processed</option>
                            <option value="failed" <?= ($filters['status'] ?? '') == 'failed' ? 'selected' : '' ?>>Failed</option>
                            <option value="void" <?= ($filters['status'] ?? '') == 'void' ? 'selected' : '' ?>>Void</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label">Batch ID</label>
                        <select class="form-select" name="batch_id">
                            <option value="">-- Semua Batch --</option>
                            <?php if (!empty($batch_list)): ?>
                                <?php foreach ($batch_list as $batch): ?>
                                    <option value="<?= $batch['batch_id'] ?>" <?= ($filters['batch_id'] ?? '') == $batch['batch_id'] ? 'selected' : '' ?>>
                                        <?= $batch['batch_id'] ?> (<?= $batch['total'] ?> transaksi)
                                    </option>
                                <?php endforeach ?>
                            <?php endif ?>
                        </select>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Pencarian</label>
                        <input type="text" class="form-control" name="search" placeholder="Cari nomor jurnal atau keterangan..." value="<?= $filters['search'] ?? '' ?>">
                    </div>
                    <div class="col-md-8 mb-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2"><i class="fas fa-search me-1"></i> Terapkan</button>
                        <a href="<?= site_url('accounting/pembukuan/buku-besar/jurnal-posted') ?>" class="btn btn-secondary"><i class="fas fa-redo me-1"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Jurnal Posted Table -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-check-circle me-2"></i> 
                Daftar Jurnal Posted
                <span class="badge bg-primary ms-2"><?= $pager['total'] ?? 0 ?> Jurnal</span>
            </h5>
        </div>
        <div class="card-body">
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
                            <th width="8%">Status</th>
                            <th width="10%">Batch ID</th>
                            <th width="8%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($jurnal)): ?>
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5>Tidak ada data</h5>
                                <p class="text-muted">Belum ada jurnal yang diposting ke buku besar</p>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php 
                            $no = ($pager['current_page'] - 1) * $pager['per_page'] + 1;
                            foreach ($jurnal as $item): 
                                $badgeClass = match($item['status']) {
                                    'processed' => 'bg-success',
                                    'failed' => 'bg-danger',
                                    'void' => 'bg-secondary',
                                    default => 'bg-warning'
                                };
                                $badgeText = match($item['status']) {
                                    'processed' => 'Processed',
                                    'failed' => 'Failed',
                                    'void' => 'Void',
                                    default => ucfirst($item['status'])
                                };
                                
                                $tipeBadge = match($item['tipe_jurnal']) {
                                    'mutasi_bank' => 'bg-info',
                                    'penyesuaian' => 'bg-warning',
                                    default => 'bg-secondary'
                                };
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d/m/Y', strtotime($item['tanggal'])) ?></td>
                                <td>
                                    <strong><?= $item['nomor_jurnal'] ?></strong>
                                </td>
                                <td><?= htmlspecialchars($item['keterangan']) ?></td>
                                <td><span class="badge <?= $tipeBadge ?>"><?= $item['tipe_jurnal'] ?></span></td>
                                <td class="text-end text-success">Rp <?= number_format($item['total_debit'], 0, ',', '.') ?></td>
                                <td class="text-end text-danger">Rp <?= number_format($item['total_kredit'], 0, ',', '.') ?></td>
                                <td><span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span></td>
                                <td>
                                    <small><?= $item['batch_id'] ?? '-' ?></small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= site_url('accounting/pembukuan/buku-besar/jurnal-posted/detail/' . $item['jurnal_id']) ?>" 
                                           class="btn btn-outline-primary" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($item['status'] == 'processed'): ?>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="voidJurnal(<?= $item['jurnal_id'] ?>, '<?= $item['nomor_jurnal'] ?>')" 
                                                title="Void">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                        <?php endif; ?>
                                        <?php if ($item['batch_id']): ?>
                                        <a href="<?= site_url('accounting/pembukuan/buku-besar/batch-detail/' . $item['batch_id']) ?>" 
                                           class="btn btn-outline-info" title="Lihat Batch">
                                            <i class="fas fa-layer-group"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach ?>
                        <?php endif ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="5" class="text-end">TOTAL:</th>
                            <th class="text-end text-success">Rp <?= number_format(array_sum(array_column($jurnal, 'total_debit')), 0, ',', '.') ?></th>
                            <th class="text-end text-danger">Rp <?= number_format(array_sum(array_column($jurnal, 'total_kredit')), 0, ',', '.') ?></th>
                            <th colspan="3"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Pagination -->
            <?php if (($pager['total_pages'] ?? 1) > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= ($pager['current_page'] ?? 1) <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= site_url('accounting/pembukuan/buku-besar/jurnal-posted?' . http_build_query(array_merge($filters, ['page' => ($pager['current_page'] ?? 1) - 1, 'per_page' => $pager['per_page'] ?? 20]))) ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                    
                    <?php for ($i = 1; $i <= ($pager['total_pages'] ?? 1); $i++): ?>
                        <?php if ($i == 1 || $i == ($pager['total_pages'] ?? 1) || ($i >= ($pager['current_page'] ?? 1) - 2 && $i <= ($pager['current_page'] ?? 1) + 2)): ?>
                            <li class="page-item <?= $i == ($pager['current_page'] ?? 1) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= site_url('accounting/pembukuan/buku-besar/jurnal-posted?' . http_build_query(array_merge($filters, ['page' => $i, 'per_page' => $pager['per_page'] ?? 20]))) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php elseif ($i == ($pager['current_page'] ?? 1) - 3 || $i == ($pager['current_page'] ?? 1) + 3): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif ?>
                    <?php endfor ?>
                    
                    <li class="page-item <?= ($pager['current_page'] ?? 1) >= ($pager['total_pages'] ?? 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= site_url('accounting/pembukuan/buku-besar/jurnal-posted?' . http_build_query(array_merge($filters, ['page' => ($pager['current_page'] ?? 1) + 1, 'per_page' => $pager['per_page'] ?? 20]))) ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
            <?php endif ?>
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
                    <input type="hidden" name="jurnal_id" id="voidJurnalId">
                    <div class="mb-3">
                        <label class="form-label">Jurnal</label>
                        <input type="text" class="form-control" id="voidNomorJurnal" readonly>
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
function exportData(type) {
    const params = new URLSearchParams();
    
    const tanggalMulai = document.querySelector('input[name="tanggal_mulai"]')?.value;
    const tanggalSelesai = document.querySelector('input[name="tanggal_selesai"]')?.value;
    const tipeJurnal = document.querySelector('select[name="tipe_jurnal"]')?.value;
    const status = document.querySelector('select[name="status"]')?.value;
    const batchId = document.querySelector('select[name="batch_id"]')?.value;
    const search = document.querySelector('input[name="search"]')?.value;
    
    if (tanggalMulai) params.append('tanggal_mulai', tanggalMulai);
    if (tanggalSelesai) params.append('tanggal_selesai', tanggalSelesai);
    if (tipeJurnal) params.append('tipe_jurnal', tipeJurnal);
    if (status) params.append('status', status);
    if (batchId) params.append('batch_id', batchId);
    if (search) params.append('search', search);
    params.append('type', type);
    
    window.location.href = '<?= site_url("accounting/pembukuan/buku-besar/export-jurnal-posted") ?>?' + params.toString();
}

let voidJurnalId = null;
let voidNomorJurnal = null;

function voidJurnal(jurnalId, nomorJurnal) {
    voidJurnalId = jurnalId;
    voidNomorJurnal = nomorJurnal;
    document.getElementById('voidJurnalId').value = jurnalId;
    document.getElementById('voidNomorJurnal').value = nomorJurnal;
    document.getElementById('voidReason').value = '';
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
    
    fetch('<?= site_url("accounting/pembukuan/buku-besar/void-jurnal/") ?>' + voidJurnalId, {
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

<?= $this->include('accounting/templates/footer') ?>