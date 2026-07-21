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
                    <h2 class="mb-1">Buku Besar</h2>
                    <p class="text-muted mb-0"><?= $subtitle ?? 'General Ledger Management' ?></p>
                </div>
                <div class="btn-group">
                    <a href="<?= site_url('accounting/pembukuan/buku-besar/neraca-saldo') ?>" class="btn btn-warning">
                        <i class="fas fa-balance-scale me-1"></i> Neraca Saldo
                    </a>
                    <a href="<?= site_url('accounting/pembukuan/buku-besar/jurnal-posted') ?>" class="btn btn-info">
                        <i class="fas fa-book me-1"></i> Jurnal Posted
                    </a>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#postJurnalsModal">
                        <i class="fas fa-sync-alt me-1"></i> Posting 
                        <?php if (($pending_jurnals_count ?? 0) > 0): ?>
                            <span class="badge bg-light text-dark ms-1"><?= $pending_jurnals_count ?> Jurnal</span>
                        <?php endif ?>
                    </button>
                    <?php if (!empty($selected_coa)): ?>
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-download me-1"></i> Export
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" onclick="exportData('excel')"><i class="fas fa-file-excel text-success me-2"></i> Excel</a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportData('pdf')"><i class="fas fa-file-pdf text-danger me-2"></i> PDF</a></li>
                    </ul>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Jurnal Pending</h6>
                    <h3><?= number_format($pending_jurnals_count ?? 0) ?></h3>
                    <small><?= number_format($pending_details_count ?? 0) ?> baris transaksi</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Jurnal</h6>
                    <h3><?= number_format($total_jurnals_count ?? 0) ?></h3>
                    <small>Sudah diposting</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Debit</h6>
                    <h3>Rp <?= number_format($total_debit_all ?? 0, 0, ',', '.') ?></h3>
                    <small>Semua transaksi</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Kredit</h6>
                    <h3>Rp <?= number_format($total_kredit_all ?? 0, 0, ',', '.') ?></h3>
                    <small>Semua transaksi</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Batch History Card -->
    <?php if (!empty($batch_history)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-history me-2"></i> Riwayat Posting Batch</h6>
                </div>
                <div class="card-body p-2">
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($batch_history as $batch): ?>
                            <span class="badge bg-secondary">
                                <?= $batch['batch_id'] ?>: <?= $batch['total'] ?> transaksi
                                <small>(<?= date('d/m/Y H:i', strtotime($batch['created_at'])) ?>)</small>
                            </span>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif ?>

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i> Filter</h5>
        </div>
        <div class="card-body">
            <form method="get" action="<?= site_url('accounting/pembukuan/buku-besar') ?>" id="filterForm">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Akun</label>
                        <select class="form-select" name="coa_id" id="coa_id">
                            <option value="">-- Pilih Akun --</option>
                            <?php if (!empty($coa_list)): ?>
                                <?php foreach ($coa_list as $coa): ?>
                                    <option value="<?= $coa['id'] ?>" <?= ($filters['coa_id'] ?? '') == $coa['id'] ? 'selected' : '' ?>>
                                        <?= $coa['kode_akun'] ?> - <?= $coa['nama_akun'] ?>
                                    </option>
                                <?php endforeach ?>
                            <?php endif ?>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label">Periode</label>
                        <input type="month" class="form-control" name="periode" value="<?= $filters['periode'] ?? date('Y-m') ?>">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" name="tanggal_mulai" value="<?= $start_date ?? date('Y-m-01') ?>">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control" name="tanggal_selesai" value="<?= $end_date ?? date('Y-m-t') ?>">
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
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Terapkan</button>
                        <a href="<?= site_url('accounting/pembukuan/buku-besar') ?>" class="btn btn-secondary"><i class="fas fa-redo me-1"></i> Reset</a>
                        <?php if (!empty($selected_coa)): ?>
                            <a href="<?= site_url('accounting/pembukuan/buku-besar/print?coa_id=' . ($filters['coa_id'] ?? '') . '&tanggal_mulai=' . ($start_date ?? '') . '&tanggal_selesai=' . ($end_date ?? '')) ?>" 
                               class="btn btn-info" target="_blank">
                                <i class="fas fa-print me-1"></i> Print
                            </a>
                        <?php endif ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Buku Besar Detail (jika akun dipilih) -->
    <?php if (!empty($selected_coa) && !empty($buku_besar)): ?>
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-book me-2"></i> 
                <?= $selected_coa['kode_akun'] ?> - <?= $selected_coa['nama_akun'] ?>
                <small class="text-muted ms-2">(<?= $selected_coa['tipe_akun'] ?> - <?= $selected_coa['saldo_normal'] ?>)</small>
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">Tanggal</th>
                            <th width="15%">No. Jurnal</th>
                            <th width="35%">Keterangan</th>
                            <th width="10%">Tipe</th>
                            <th width="12%" class="text-end">Debit</th>
                            <th width="12%" class="text-end">Kredit</th>
                            <th width="10%" class="text-end">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Baris Saldo Awal -->
                        <tr class="table-secondary">
                            <td colspan="5"><strong>Saldo Awal</strong> (<?= date('d/m/Y', strtotime($start_date)) ?>)</td>
                            <td class="text-end"><?= $buku_besar['saldo_awal'] > 0 ? 'Rp ' . number_format($buku_besar['saldo_awal'], 0, ',', '.') : '-' ?></td>
                            <td class="text-end"><?= $buku_besar['saldo_awal'] < 0 ? 'Rp ' . number_format(abs($buku_besar['saldo_awal']), 0, ',', '.') : '-' ?></td>
                            <td class="text-end fw-bold">Rp <?= number_format($buku_besar['saldo_awal'], 0, ',', '.') ?></td>
                        </tr>
                        
                        <?php if (empty($buku_besar['entries'])): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5>Tidak ada transaksi</h5>
                                <p class="text-muted">Tidak ada transaksi pada periode ini</p>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php 
                            $no = 1;
                            foreach ($buku_besar['entries'] as $entry): 
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d/m/Y', strtotime($entry['tanggal'])) ?></td>
                                <td>
                                    <a href="<?= site_url('accounting/pembukuan/jurnal-umum/detail/' . $entry['jurnal_id']) ?>" target="_blank">
                                        <?= $entry['nomor_jurnal'] ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($entry['keterangan']) ?></td>
                                <td>
                                    <?php 
                                    $badgeClass = match($entry['tipe_jurnal']) {
                                        'mutasi_bank' => 'bg-info',
                                        'penyesuaian' => 'bg-warning',
                                        default => 'bg-secondary'
                                    };
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= $entry['tipe_jurnal'] ?></span>
                                </td>
                                <td class="text-end text-success">
                                    <?= $entry['debit'] > 0 ? 'Rp ' . number_format($entry['debit'], 0, ',', '.') : '-' ?>
                                </td>
                                <td class="text-end text-danger">
                                    <?= $entry['kredit'] > 0 ? 'Rp ' . number_format($entry['kredit'], 0, ',', '.') : '-' ?>
                                </td>
                                <td class="text-end fw-bold">
                                    Rp <?= number_format($entry['saldo_akhir'], 0, ',', '.') ?>
                                </td>
                            </tr>
                            <?php endforeach ?>
                        <?php endif ?>
                    </tbody>
                    <?php if (!empty($buku_besar['entries'])): ?>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="5" class="text-end">TOTAL PERIODE:</th>
                            <th class="text-end text-success">Rp <?= number_format($buku_besar['total_debit'], 0, ',', '.') ?></th>
                            <th class="text-end text-danger">Rp <?= number_format($buku_besar['total_kredit'], 0, ',', '.') ?></th>
                            <th class="text-end fw-bold">Rp <?= number_format($buku_besar['saldo_akhir'], 0, ',', '.') ?></th>
                        </tr>
                    </tfoot>
                    <?php endif ?>
                </table>
            </div>
        </div>
    </div>
    <?php elseif (empty($selected_coa)): ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-chart-line fa-4x text-muted mb-3"></i>
            <h5>Pilih Akun Terlebih Dahulu</h5>
            <p class="text-muted">Silakan pilih akun dari filter di atas untuk melihat detail buku besar</p>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Posting Jurnal - WAJIB PILIH BULAN DAN TAHUN -->
<div class="modal fade" id="postJurnalsModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-sync-alt me-2"></i> Posting ke Buku Besar</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" id="closeModalBtn"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Posting Per Bulan</strong><br>
                    Silakan pilih bulan dan tahun untuk posting jurnal. 
                    Posting dilakukan per bulan untuk menjaga akurasi laporan keuangan.
                </div>
                
                <!-- WAJIB Pilih Bulan dan Tahun -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Pilih Periode <span class="text-danger">*</span></label>
                    <div class="row">
                        <div class="col-md-5 mb-2">
                            <select class="form-select" id="postingBulan" required>
                                <option value="">-- Pilih Bulan --</option>
                                <option value="01">Januari</option>
                                <option value="02">Februari</option>
                                <option value="03">Maret</option>
                                <option value="04">April</option>
                                <option value="05">Mei</option>
                                <option value="06">Juni</option>
                                <option value="07">Juli</option>
                                <option value="08">Agustus</option>
                                <option value="09">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                        <div class="col-md-5 mb-2">
                            <select class="form-select" id="postingTahun" required>
                                <option value="">-- Pilih Tahun --</option>
                                <?php for ($y = 2024; $y <= date('Y') + 1; $y++): ?>
                                    <option value="<?= $y ?>" <?= $y == date('Y') ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endfor ?>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <button type="button" class="btn btn-primary w-100" id="checkJurnalBtn">
                                <i class="fas fa-search me-1"></i> Cek
                            </button>
                        </div>
                    </div>
                    <small class="text-muted">* Pilih bulan dan tahun, lalu klik tombol CEK untuk melihat jurnal</small>
                </div>
                
                <div id="periodeInfo" class="alert alert-secondary d-none">
                    <i class="fas fa-calendar-alt me-2"></i>
                    <span id="selectedPeriodeText"></span>
                </div>
                
                <div id="jurnalSummary" style="display: none;">
                    <div class="row text-center mb-3">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Jurnal dalam Periode</h6>
                                    <h3 class="text-primary" id="jurnalCount">-</h3>
                                    <small>Jurnal yang akan diproses</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-secondary bg-opacity-25">
                                <div class="card-body">
                                    <h6>Baris Transaksi</h6>
                                    <h3 class="text-info" id="detailCount">-</h3>
                                    <small>Total baris debit/kredit</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="postingMessage"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="closeModalFooterBtn">Tutup</button>
                <button type="button" class="btn btn-success" id="btnPosting" disabled>
                    <i class="fas fa-play me-1"></i> Mulai Posting
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Loading Proses -->
<div class="modal fade" id="loadingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h6 id="loadingMessage">Sedang memproses...</h6>
                <p class="text-muted small mb-0" id="loadingDetail">Mohon tunggu, jangan tutup halaman ini</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const postingBulan = document.getElementById('postingBulan');
    const postingTahun = document.getElementById('postingTahun');
    const periodeInfo = document.getElementById('periodeInfo');
    const selectedPeriodeText = document.getElementById('selectedPeriodeText');
    const checkJurnalBtn = document.getElementById('checkJurnalBtn');
    const btnPosting = document.getElementById('btnPosting');
    const jurnalSummary = document.getElementById('jurnalSummary');
    const postingMessage = document.getElementById('postingMessage');
    const closeModalFooterBtn = document.getElementById('closeModalFooterBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    
    const namaBulan = {
        '01': 'Januari', '02': 'Februari', '03': 'Maret', '04': 'April',
        '05': 'Mei', '06': 'Juni', '07': 'Juli', '08': 'Agustus',
        '09': 'September', '10': 'Oktober', '11': 'November', '12': 'Desember'
    };
    
   function checkJurnal() {
    const bulan = postingBulan.value;
    const tahun = postingTahun.value;
    
    if (!bulan || !tahun) {
        alert('Pilih bulan dan tahun terlebih dahulu!');
        return;
    }
    
    const periodeText = `${namaBulan[bulan]} ${tahun}`;
    selectedPeriodeText.textContent = periodeText;
    periodeInfo.classList.remove('d-none');
    
    jurnalSummary.style.display = 'block';
    document.getElementById('jurnalCount').textContent = '...';
    document.getElementById('detailCount').textContent = '...';
    btnPosting.disabled = true;
    btnPosting.innerHTML = '<i class="fas fa-play me-1"></i> Mulai Posting';
    postingMessage.innerHTML = '<div class="text-center text-muted"><i class="fas fa-spinner fa-spin me-2"></i> Sedang mengecek jurnal...</div>';
    
    fetch(`<?= site_url("accounting/pembukuan/buku-besar/ajax-get-pending-counts") ?>?bulan=${bulan}&tahun=${tahun}`, {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const jmlJurnal = data.jurnal_count || 0;
            const jmlDetail = data.detail_count || 0;
            const alreadyPosted = data.already_posted || false;
            
            document.getElementById('jurnalCount').textContent = jmlJurnal;
            document.getElementById('detailCount').textContent = jmlDetail;
            
            if (alreadyPosted) {
                btnPosting.disabled = true;
                postingMessage.innerHTML = `
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Periode ${periodeText} sudah pernah diposting!</strong><br>
                        <small>Tidak dapat melakukan posting ulang.</small>
                    </div>
                `;
            }
            else if (jmlJurnal > 0) {
                btnPosting.disabled = false;
                postingMessage.innerHTML = `
                    <div class="alert alert-success mt-3">
                        <i class="fas fa-check-circle me-2"></i>
                        Ditemukan <strong>${jmlJurnal} jurnal</strong> (${jmlDetail} baris) pada periode ${periodeText}.
                        <br><small>Klik "Mulai Posting" untuk melanjutkan.</small>
                    </div>
                `;
            } else {
                btnPosting.disabled = true;
                // 🔥 PESAN YANG LEBIH JELAS
                postingMessage.innerHTML = `
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Tidak ada jurnal dengan status POSTED pada periode ${periodeText}.</strong><br>
                        <small>
                            Pastikan:
                            <ul class="mt-2 mb-0">
                                <li>Jurnal untuk periode ini sudah dibuat</li>
                                <li>Status jurnal sudah <strong>"posted"</strong> (bukan draft)</li>
                                <li>Jurnal belum diposting ke buku besar</li>
                            </ul>
                        </small>
                    </div>
                `;
            }
        } else {
            postingMessage.innerHTML = `
                <div class="alert alert-danger mt-3">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    ${data.message || 'Gagal mengecek jurnal.'}
                </div>
            `;
            btnPosting.disabled = true;
        }
    })
    .catch(err => {
        console.error('Error:', err);
        postingMessage.innerHTML = `
            <div class="alert alert-danger mt-3">
                <i class="fas fa-exclamation-circle me-2"></i>
                Error: ${err.message}
            </div>
        `;
        btnPosting.disabled = true;
    });
}
   
    
    // Event listener untuk tombol cek jurnal
    if (checkJurnalBtn) {
        checkJurnalBtn.addEventListener('click', checkJurnal);
    }
    
    // Event untuk tombol Enter di dropdown
    postingBulan.addEventListener('change', function() {
        if (postingBulan.value && postingTahun.value) {
            checkJurnal();
        }
    });
    
    postingTahun.addEventListener('change', function() {
        if (postingBulan.value && postingTahun.value) {
            checkJurnal();
        }
    });
    
    // 🔥 PERBAIKAN: Fungsi startPosting
    function startPosting() {
        const bulan = postingBulan.value;
        const tahun = postingTahun.value;
        
        if (!bulan || !tahun) {
            alert('Pilih bulan dan tahun terlebih dahulu!');
            return;
        }
        
        const namaBulanSelected = namaBulan[bulan];
        const periodeText = `${namaBulanSelected} ${tahun}`;
        
        // Disable button dan tampilkan loading
        btnPosting.disabled = true;
        btnPosting.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...';
        postingMessage.innerHTML = '<div class="text-center text-muted"><i class="fas fa-spinner fa-spin me-2"></i> Sedang memproses posting jurnal...</div>';
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                          document.querySelector('input[name="csrf_test_name"]')?.value || 
                          '<?= csrf_hash() ?>';
        
        const formData = new URLSearchParams();
        formData.append('bulan', bulan);
        formData.append('tahun', tahun);
        formData.append('csrf_token', csrfToken);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        
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
                throw new Error('HTTP ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            let html = '';
            
            if (data.success) {
                html += `<div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i> 
                    <strong>Berhasil menutup periode ${periodeText}!</strong><br>
                    ${data.message}
                </div>`;
                
                html += `<div class="mt-3">
                    <strong>Ringkasan Posting:</strong>
                    <div class="row mt-2">
                        <div class="col-6">Jurnal diproses:</div>
                        <div class="col-6 text-end fw-bold">${data.total_jurnal || data.total || 0} jurnal</div>
                    </div>
                    <div class="row">
                        <div class="col-6">Baris transaksi:</div>
                        <div class="col-6 text-end fw-bold">${data.total_baris || 0} baris</div>
                    </div>
                    <div class="row text-success">
                        <div class="col-6">Berhasil:</div>
                        <div class="col-6 text-end fw-bold">${data.success_count || 0}</div>
                    </div>`;
                
                if (data.failed_count > 0) {
                    html += `<div class="row text-danger">
                        <div class="col-6">Gagal:</div>
                        <div class="col-6 text-end fw-bold">${data.failed_count}</div>
                    </div>`;
                }
                
                html += `<div class="row mt-2 pt-2 border-top">
                        <div class="col-6">Sisa jurnal bulan lain:</div>
                        <div class="col-6 text-end fw-bold text-warning">${data.sisa_jurnal || 0} jurnal</div>
                    </div>
                </div>`;
                
                if (data.batch_id) {
                    html += `<div class="alert alert-info mt-3">
                        <i class="fas fa-tag me-2"></i>
                        <strong>Batch ID:</strong> ${data.batch_id}
                    </div>`;
                }
                
                if (data.failed_count === 0) {
                    html += `<div class="alert alert-success mt-3">
                        <i class="fas fa-thumbs-up me-2"></i>
                        Semua jurnal periode ${periodeText} berhasil diposting!
                    </div>`;
                }
                
                postingMessage.innerHTML = html;
                btnPosting.disabled = true;
                btnPosting.innerHTML = '<i class="fas fa-check me-1"></i> Selesai';
                
                // Tutup modal setelah 2 detik dan refresh
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('postJurnalsModal'));
                    if (modal) modal.hide();
                    window.location.reload();
                }, 2000);
                
            } else {
                // 🔥 PERBAIKAN: Handle jika periode sudah diposting
                if (data.already_posted) {
                    html += `<div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i> 
                        <strong>Periode ${periodeText} sudah pernah diposting!</strong><br>
                        ${data.message}
                    </div>`;
                    
                    if (data.posted_jurnals && data.posted_jurnals.length > 0) {
                        html += `<div class="mt-3">
                            <strong>Jurnal yang sudah diposting:</strong>
                            <ul class="mb-0">`;
                        data.posted_jurnals.forEach(jurnal => {
                            html += `<li>${jurnal}</li>`;
                        });
                        html += `</ul></div>`;
                    }
                } else {
                    html += `<div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i> 
                        <strong>Gagal menutup periode ${periodeText}!</strong><br>
                        ${data.message}
                    </div>`;
                }
                
                if (data.failed_items && data.failed_items.length > 0) {
                    html += `<div class="mt-3">
                        <strong>Detail Error:</strong>
                        <ul class="mb-0">`;
                    data.failed_items.forEach(item => {
                        html += `<li class="text-danger">${item.nomor_jurnal || 'Jurnal'} - ${item.error}</li>`;
                    });
                    html += `</ul></div>`;
                }
                
                html += `<div class="alert alert-warning mt-3">
                    <i class="fas fa-lightbulb me-2"></i>
                    <strong>Saran:</strong> Perbaiki jurnal yang error, lalu coba lagi.
                </div>`;
                
                postingMessage.innerHTML = html;
                btnPosting.disabled = false;
                btnPosting.innerHTML = '<i class="fas fa-play me-1"></i> Mulai Posting';
            }
        })
        .catch(err => {
            console.error('Error:', err);
            postingMessage.innerHTML = `<div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i> 
                <strong>Error Jaringan:</strong> ${err.message}<br>
                <small class="mt-2">Cek koneksi internet dan console browser (F12).</small>
            </div>`;
            btnPosting.disabled = false;
            btnPosting.innerHTML = '<i class="fas fa-play me-1"></i> Coba Lagi';
        });
    }
    
    // Attach event ke tombol posting
    if (btnPosting) {
        btnPosting.onclick = startPosting;
    }
    
    // Fungsi untuk menutup modal
    function closeModal() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('postJurnalsModal'));
        if (modal) modal.hide();
        
        // Reset form
        postingBulan.value = '';
        postingTahun.value = '<?= date('Y') ?>';
        periodeInfo.classList.add('d-none');
        jurnalSummary.style.display = 'none';
        postingMessage.innerHTML = '';
        btnPosting.disabled = true;
        btnPosting.innerHTML = '<i class="fas fa-play me-1"></i> Mulai Posting';
        document.getElementById('jurnalCount').textContent = '-';
        document.getElementById('detailCount').textContent = '-';
    }
    
    // Event untuk tombol tutup
    if (closeModalFooterBtn) {
        closeModalFooterBtn.addEventListener('click', closeModal);
    }
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeModal);
    }
    
    // Reset form saat modal ditutup
    const modalElement = document.getElementById('postJurnalsModal');
    if (modalElement) {
        modalElement.addEventListener('hidden.bs.modal', function() {
            closeModal();
        });
    }
});

// Export data function
function exportData(type) {
    const coaId = document.getElementById('coa_id')?.value;
    if (!coaId) {
        alert('Pilih akun terlebih dahulu');
        return;
    }
    
    const params = new URLSearchParams();
    params.append('coa_id', coaId);
    params.append('type', type);
    
    const tanggalMulai = document.querySelector('input[name="tanggal_mulai"]')?.value;
    const tanggalSelesai = document.querySelector('input[name="tanggal_selesai"]')?.value;
    if (tanggalMulai) params.append('tanggal_mulai', tanggalMulai);
    if (tanggalSelesai) params.append('tanggal_selesai', tanggalSelesai);
    
    window.location.href = '<?= site_url("accounting/pembukuan/buku-besar/export") ?>?' + params.toString();
}

// 🔥 PERBAIKAN: Function to load available periods (periode yang memiliki jurnal pending)
function loadAvailablePeriods() {
    fetch('<?= site_url("accounting/pembukuan/buku-besar/ajax-get-available-periods") ?>', {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.periods && data.periods.length > 0) {
            // Tidak perlu ditampilkan lagi karena sudah ada dropdown manual
            // Tapi bisa ditambahkan info di console untuk debugging
            console.log('Available periods for posting:', data.periods);
        }
    })
    .catch(err => console.log('Error loading periods:', err));
}

// Panggil saat modal dibuka
document.getElementById('postJurnalsModal').addEventListener('show.bs.modal', function() {
    // Reset form saat modal dibuka
    const postingBulan = document.getElementById('postingBulan');
    const postingTahun = document.getElementById('postingTahun');
    
    // Tidak reset value, biarkan user memilih
    // loadAvailablePeriods();
});

</script>

<style>
/* Styling tambahan */
#jurnalSummary .card {
    transition: all 0.3s ease;
}
#jurnalSummary .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.modal-content {
    border-radius: 12px;
}
.btn-group .btn {
    border-radius: 8px;
    margin-left: 5px;
}
.card {
    border-radius: 12px;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
.card-header {
    border-radius: 12px 12px 0 0 !important;
    background-color: #f8f9fa;
}
.table th, .table td {
    vertical-align: middle;
}
</style>

<?= $this->include('accounting/templates/footer') ?>