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
                    <h2 class="mb-1">Konfirmasi Posting ke Buku Besar</h2>
                    <p class="text-muted mb-0">Review dan konfirmasi jurnal yang akan diposting</p>
                </div>
                <div>
                    <a href="<?= site_url('accounting/pembukuan/buku-besar') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Card -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Jurnal Akan Diposting</h6>
                    <h3><?= number_format($jurnal_count ?? count($pending_summary)) ?></h3>
                    <small><?= number_format($pending_count ?? 0) ?> baris transaksi</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Periode Mulai</h6>
                    <h3><?= !empty($periode_mulai) ? date('F Y', strtotime($periode_mulai . '-01')) : 'Semua' ?></h3>
                    <small>Dari periode ini</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Periode Selesai</h6>
                    <h3><?= !empty($periode_selesai) ? date('F Y', strtotime($periode_selesai . '-01')) : 'Semua' ?></h3>
                    <small>Sampai periode ini</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Nominal</h6>
                    <h3>Rp <?= number_format(array_sum(array_column($pending_summary, 'total_debit')), 0, ',', '.') ?></h3>
                    <small>Total transaksi</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Jurnal Pending -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i> 
                Daftar Jurnal yang Akan Diposting
                <span class="badge bg-primary ms-2"><?= count($pending_summary) ?> Jurnal</span>
                <span class="badge bg-secondary ms-1"><?= $pending_count ?? 0 ?> baris</span>
            </h5>
        </div>
        <div class="card-body">
            <?php if (empty($pending_summary)): ?>
            <div class="text-center py-5">
                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                <h4>Tidak Ada Jurnal Pending</h4>
                <p class="text-muted">Semua jurnal sudah diposting ke buku besar</p>
                <a href="<?= site_url('accounting/pembukuan/buku-besar') ?>" class="btn btn-primary mt-3">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Buku Besar
                </a>
            </div>
            <?php else: ?>
            
            <!-- Alert Peringatan -->
            <div class="alert alert-warning mb-4">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Perhatian!</strong> Proses ini akan memposting semua jurnal di bawah ini ke buku besar.
                Pastikan data sudah benar karena proses ini <strong>TIDAK DAPAT DIURUNGKAN</strong> secara otomatis.
                <hr>
                <small class="mb-0">
                    <i class="fas fa-info-circle me-1"></i>
                    Setelah diposting, jurnal tidak dapat diedit. Jika ada kesalahan, buat jurnal koreksi terpisah.
                </small>
            </div>

            <!-- Tabel Jurnal -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="jurnalTable">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">Tanggal</th>
                            <th width="15%">Nomor Jurnal</th>
                            <th width="25%">Keterangan</th>
                            <th width="10%">Tipe</th>
                            <th width="15%" class="text-end">Total Debit</th>
                            <th width="15%" class="text-end">Total Kredit</th>
                            <th width="5%" class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        foreach ($pending_summary as $jurnal): 
                            // Tentukan badge color berdasarkan tipe
                            $badgeClass = match($jurnal['tipe_jurnal']) {
                                'mutasi_bank' => 'bg-info',
                                'penyesuaian' => 'bg-warning',
                                default => 'bg-secondary'
                            };
                            $badgeText = match($jurnal['tipe_jurnal']) {
                                'mutasi_bank' => 'Mutasi Bank',
                                'penyesuaian' => 'Penyesuaian',
                                default => 'Umum'
                            };
                            
                            // Cek apakah balance
                            $isBalance = abs($jurnal['total_debit'] - $jurnal['total_kredit']) <= 0.01;
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= date('d/m/Y', strtotime($jurnal['tanggal'])) ?></td>
                            <td>
                                <strong><?= $jurnal['nomor_jurnal'] ?></strong>
                            </td>
                            <td><?= htmlspecialchars($jurnal['keterangan']) ?></td>
                            <td><span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span></td>
                            <td class="text-end text-success">
                                <strong>Rp <?= number_format($jurnal['total_debit'], 0, ',', '.') ?></strong>
                            </td>
                            <td class="text-end text-danger">
                                <strong>Rp <?= number_format($jurnal['total_kredit'], 0, ',', '.') ?></strong>
                            </td>
                            <td class="text-center">
                                <?php if ($isBalance): ?>
                                    <i class="fas fa-check-circle text-success" title="Balance"></i>
                                <?php else: ?>
                                    <i class="fas fa-times-circle text-danger" title="Tidak Balance"></i>
                                <?php endif ?>
                            </td>
                        </tr>
                        
                        <!-- Detail transaksi per jurnal (collapsible) -->
                        <tr class="detail-row-<?= $no-1 ?>" style="display: none;">
                            <td colspan="8" class="p-0">
                                <div class="bg-light p-3">
                                    <div class="mb-2">
                                        <small class="text-muted"><i class="fas fa-info-circle me-1"></i> Klik pada baris jurnal untuk menutup detail</small>
                                    </div>
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th width="15%">Kode Akun</th>
                                                <th width="30%">Nama Akun</th>
                                                <th width="30%">Keterangan</th>
                                                <th width="12%" class="text-end">Debit</th>
                                                <th width="12%" class="text-end">Kredit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($jurnal['details'] as $detail): ?>
                                            <tr>
                                                <td><?= $detail['kode_akun'] ?></td>
                                                <td><?= $detail['nama_akun'] ?></td>
                                                <td><?= htmlspecialchars($detail['keterangan'] ?? '-') ?></td>
                                                <td class="text-end text-success"><?= $detail['debit'] > 0 ? 'Rp ' . number_format($detail['debit'], 0, ',', '.') : '-' ?></td>
                                                <td class="text-end text-danger"><?= $detail['kredit'] > 0 ? 'Rp ' . number_format($detail['kredit'], 0, ',', '.') : '-' ?></td>
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
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <th colspan="5" class="text-end">TOTAL KESELURUHAN</th>
                            <th class="text-end">Rp <?= number_format(array_sum(array_column($pending_summary, 'total_debit')), 0, ',', '.') ?></th>
                            <th class="text-end">Rp <?= number_format(array_sum(array_column($pending_summary, 'total_kredit')), 0, ',', '.') ?></th>
                            <th class="text-center">
                                <?php 
                                $totalDebit = array_sum(array_column($pending_summary, 'total_debit'));
                                $totalKredit = array_sum(array_column($pending_summary, 'total_kredit'));
                                $isBalanceTotal = abs($totalDebit - $totalKredit) <= 0.01;
                                ?>
                                <?php if ($isBalanceTotal): ?>
                                    <i class="fas fa-check-circle"></i> Balance
                                <?php else: ?>
                                    <i class="fas fa-exclamation-triangle"></i> Not Balance
                                <?php endif ?>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Form Konfirmasi -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-white">
                            <h6 class="mb-0"><i class="fas fa-check-double me-2"></i> Konfirmasi Posting</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-danger mb-3" id="warningBalance" style="display: none;">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Peringatan!</strong> Terdapat jurnal yang tidak balance. 
                                Harap perbaiki jurnal tersebut sebelum melanjutkan posting.
                            </div>
                            
                            <form id="postingForm">
                                <?= csrf_field() ?>
                                <input type="hidden" name="periode_mulai" value="<?= $periode_mulai ?? '' ?>">
                                <input type="hidden" name="periode_selesai" value="<?= $periode_selesai ?? '' ?>">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="confirmBalance" <?= $isBalanceTotal ? '' : 'disabled' ?>>
                                            <label class="form-check-label" for="confirmBalance">
                                                <strong>Saya mengkonfirmasi bahwa semua jurnal sudah balance</strong>
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="confirmReview">
                                            <label class="form-check-label" for="confirmReview">
                                                <strong>Saya sudah mereview semua jurnal dan data sudah benar</strong>
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="confirmIrreversible">
                                            <label class="form-check-label text-danger" for="confirmIrreversible">
                                                <strong>Saya memahami bahwa proses ini TIDAK DAPAT DIURUNGKAN</strong>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Yang akan terjadi setelah posting:</strong>
                                            <ul class="mb-0 mt-2">
                                                <li>Jurnal akan dipindahkan ke buku besar</li>
                                                <li>Status jurnal akan berubah menjadi "Posted"</li>
                                                <li>Jurnal tidak dapat diedit lagi</li>
                                                <li>Saldo akun akan terupdate otomatis</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-center mt-3">
                                    <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                                        <i class="fas fa-times me-1"></i> Batal
                                    </button>
                                    <button type="button" class="btn btn-success" id="btnConfirmPosting" onclick="startPosting()" disabled>
                                        <i class="fas fa-check-circle me-1"></i> Konfirmasi Posting
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif ?>
        </div>
    </div>
</div>

<!-- Modal Progress -->
<div class="modal fade" id="progressModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-spinner fa-spin me-2"></i> Memproses Posting</h5>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5 id="progressMessage">Sedang memproses jurnal ke buku besar...</h5>
                    <p class="text-muted" id="progressDetail">Mohon tunggu, jangan tutup halaman ini</p>
                    <div class="progress mt-3" style="height: 20px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" id="progressBar">0%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Result -->
<div class="modal fade" id="resultModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" id="resultModalHeader">
                <h5 class="modal-title" id="resultModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="resultModalBody">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="window.location.href='<?= site_url('accounting/pembukuan/buku-besar') ?>'">
                    <i class="fas fa-chart-line me-1"></i> Lihat Buku Besar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle detail row
document.querySelectorAll('#jurnalTable tbody tr:not(.detail-row)').forEach((row, index) => {
    row.addEventListener('click', function(e) {
        // Jangan toggle jika klik pada link atau button
        if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON') return;
        
        const detailRow = document.querySelector(`.detail-row-${index + 1}`);
        if (detailRow) {
            if (detailRow.style.display === 'none') {
                detailRow.style.display = 'table-row';
            } else {
                detailRow.style.display = 'none';
            }
        }
    });
});

// Enable confirm button when all checkboxes checked
const confirmBalance = document.getElementById('confirmBalance');
const confirmReview = document.getElementById('confirmReview');
const confirmIrreversible = document.getElementById('confirmIrreversible');
const btnConfirm = document.getElementById('btnConfirmPosting');

function checkAllConfirmed() {
    const balanceChecked = confirmBalance ? confirmBalance.checked : true;
    const reviewChecked = confirmReview.checked;
    const irreversibleChecked = confirmIrreversible.checked;
    
    btnConfirm.disabled = !(balanceChecked && reviewChecked && irreversibleChecked);
}

if (confirmBalance) confirmBalance.addEventListener('change', checkAllConfirmed);
if (confirmReview) confirmReview.addEventListener('change', checkAllConfirmed);
if (confirmIrreversible) confirmIrreversible.addEventListener('change', checkAllConfirmed);

// Check warning balance
<?php if (!$isBalanceTotal): ?>
document.getElementById('warningBalance').style.display = 'block';
<?php endif; ?>

// Start posting process
function startPosting() {
    // Show progress modal
    const progressModal = new bootstrap.Modal(document.getElementById('progressModal'));
    progressModal.show();
    
    // Update progress
    let progress = 0;
    const progressInterval = setInterval(() => {
        if (progress < 90) {
            progress += 10;
            document.getElementById('progressBar').style.width = progress + '%';
            document.getElementById('progressBar').innerHTML = progress + '%';
        }
    }, 500);
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                      document.querySelector('input[name="csrf_test_name"]')?.value || 
                      '<?= csrf_hash() ?>';
    
    // Prepare form data
    const formData = new URLSearchParams();
    formData.append('periode_mulai', document.querySelector('input[name="periode_mulai"]')?.value || '');
    formData.append('periode_selesai', document.querySelector('input[name="periode_selesai"]')?.value || '');
    formData.append('csrf_token', csrfToken);
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
    
    // Send request
    fetch('<?= site_url("accounting/pembukuan/buku-besar/post-all") ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        credentials: 'same-origin',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        clearInterval(progressInterval);
        document.getElementById('progressBar').style.width = '100%';
        document.getElementById('progressBar').innerHTML = '100%';
        document.getElementById('progressMessage').innerHTML = 'Proses selesai!';
        
        setTimeout(() => {
            progressModal.hide();
            showResult(data);
        }, 500);
    })
    .catch(error => {
        clearInterval(progressInterval);
        progressModal.hide();
        
        console.error('Error:', error);
        showResult({
            success: false,
            message: 'Terjadi kesalahan jaringan: ' + error.message,
            total: 0,
            total_jurnal: 0,
            total_baris: 0,
            success_count: 0,
            failed_count: 0,
            failed_items: []
        });
    });
}

// Show result modal
function showResult(data) {
    const resultModal = new bootstrap.Modal(document.getElementById('resultModal'));
    const modalHeader = document.getElementById('resultModalHeader');
    const modalTitle = document.getElementById('resultModalTitle');
    const modalBody = document.getElementById('resultModalBody');
    
    if (data.success) {
        modalHeader.className = 'modal-header bg-success text-white';
        modalTitle.innerHTML = '<i class="fas fa-check-circle me-2"></i> Posting Berhasil!';
        
        let html = `
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                <strong>${data.message}</strong>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5>Total Jurnal</h5>
                            <h3 class="text-primary">${data.total_jurnal || data.total || 0}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5>Total Baris</h5>
                            <h3 class="text-info">${data.total_baris || 0}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5>Gagal</h5>
                            <h3 class="text-danger">${data.failed_count || 0}</h3>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        if (data.batch_id) {
            html += `<div class="alert alert-info mt-3">
                <i class="fas fa-tag me-2"></i>
                <strong>Batch ID:</strong> ${data.batch_id}
            </div>`;
        }
        
        if (data.failed_items && data.failed_items.length > 0) {
            html += `<div class="alert alert-warning mt-3">
                <strong><i class="fas fa-exclamation-triangle me-2"></i> Detail Gagal:</strong>
                <ul class="mb-0 mt-2">`;
            data.failed_items.forEach(item => {
                html += `<li class="text-danger">${item.nomor_jurnal || 'Jurnal'} - ${item.error}</li>`;
            });
            html += `</ul></div>`;
        }
        
        modalBody.innerHTML = html;
    } else {
        modalHeader.className = 'modal-header bg-danger text-white';
        modalTitle.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i> Posting Gagal!';
        
        let html = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>${data.message}</strong>
            </div>
        `;
        
        if (data.failed_items && data.failed_items.length > 0) {
            html += `<div class="alert alert-warning mt-3">
                <strong><i class="fas fa-info-circle me-2"></i> Detail Error:</strong>
                <ul class="mb-0 mt-2">`;
            data.failed_items.forEach(item => {
                html += `<li class="text-danger">${item.error || 'Unknown error'}</li>`;
            });
            html += `</ul></div>`;
        }
        
        html += `<div class="alert alert-info mt-3">
            <i class="fas fa-lightbulb me-2"></i>
            <strong>Saran:</strong> Perbaiki jurnal yang error, lalu coba lagi.
        </div>`;
        
        modalBody.innerHTML = html;
    }
    
    resultModal.show();
}
</script>

<style>
#jurnalTable tbody tr:not(.detail-row):hover {
    background-color: #f5f5f5;
    cursor: pointer;
}
.detail-row td {
    padding: 0 !important;
}
</style>

<?= $this->include('accounting/templates/footer') ?>