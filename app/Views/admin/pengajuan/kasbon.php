<?php
$dataHeader = [
    'title'    => 'Pengajuan Kasbon Karyawan',
    'subtitle' => 'Kelola & Ajukan Kasbon Karyawan (Terhubung Ke Monitoring Direktur)',
    'active'   => 'pengajuan-kasbon',
    'user'     => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
];

$totalKasbon    = count($kasbonList ?? []);
$totalPinjaman  = 0;
$totalSisa      = 0;
$pendingCount   = 0;
$approvedCount  = 0;
$rejectedCount  = 0;

foreach ($kasbonList as $k) {
    $jml = floatval($k['jumlah_kasbon'] ?? 0);
    $sisa = floatval($k['sisa_pinjaman'] ?? $jml);
    $totalPinjaman += $jml;
    $totalSisa     += $sisa;

    $st = strtolower($k['status_direktur'] ?? 'menunggu');
    if ($st === 'disetujui') {
        $approvedCount++;
    } elseif ($st === 'ditolak') {
        $rejectedCount++;
    } else {
        $pendingCount++;
    }
}
?>

<?= view('admin/templates/header', $dataHeader) ?>
<?= view('admin/templates/sidebar', $dataHeader) ?>
<?= view('admin/templates/navbar', $dataHeader) ?>

<style>
    html, body {
        max-width: 100% !important;
        overflow-x: hidden !important;
    }
    .main-content {
        max-width: 100% !important;
        overflow-x: hidden !important;
    }
    .container-fluid {
        max-width: 100% !important;
        padding-left: 12px !important;
        padding-right: 12px !important;
        box-sizing: border-box !important;
    }
    .kasbon-card-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(30, 60, 114, 0.05) !important;
    }
    .stat-card-kasbon {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card-kasbon:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    }
    .table-scroll-wrapper {
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
        display: block !important;
        margin-bottom: 0;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }
    .table-scroll-wrapper table {
        min-width: 880px !important;
        width: 100% !important;
        margin-bottom: 0 !important;
    }
    .avatar-circle-sm {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
        flex-shrink: 0;
    }
    @media (max-width: 767.98px) {
        .stat-card-kasbon {
            margin-bottom: 8px;
        }
        .header-title-box {
            font-size: 1.1rem !important;
        }
    }
</style>

<div class="container-fluid py-3 py-md-4">

    <!-- 1. Header Section -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-4 shadow-sm p-3 p-md-4 mb-4 border border-light flex-wrap gap-3">
        <div class="d-flex align-items-center overflow-hidden">
            <div class="bg-primary text-white rounded-3 p-3 me-3 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 52px; height: 52px; background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;">
                <i class="fas fa-hand-holding-usd fs-4"></i>
            </div>
            <div class="overflow-hidden">
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <h4 class="mb-0 fw-bold text-dark header-title-box" style="font-size: 1.3rem;">Pengajuan & Data Kasbon Karyawan</h4>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-20 px-2.5 py-1 rounded-pill text-xs fw-semibold">
                        <i class="fas fa-link me-1"></i> Terhubung Ke Direktur Keuangan Kasbon
                    </span>
                </div>
                <p class="text-muted mb-0 text-sm">Kelola pengajuan pinjaman/kasbon staf admin & karyawan terintegrasi langsung dengan Approval Direktur.</p>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-success text-white rounded-pill shadow-sm d-inline-flex align-items-center text-sm fw-bold px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalTambahKasbon">
                <i class="fas fa-plus me-1.5"></i> <span>+ Ajukan Kasbon Baru</span>
            </button>
            <a href="<?= base_url('direktur/keuangan/kasbon') ?>" target="_blank" class="btn btn-outline-primary rounded-pill shadow-sm d-inline-flex align-items-center text-sm fw-semibold px-3 py-2">
                <i class="fas fa-external-link-alt me-1.5"></i> <span>Cek Monitoring Direktur</span>
            </a>
        </div>
    </div>

    <!-- Alert Flash Notifications -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- 2. Metrics Statistics Row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card-kasbon p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="text-muted font-semibold text-uppercase text-xs">Total Pinjaman</small>
                    <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-circle">
                        <i class="fas fa-wallet fs-6"></i>
                    </div>
                </div>
                <div class="fs-5 fw-bold text-dark mb-0">Rp <?= number_format($totalPinjaman, 0, ',', '.') ?></div>
                <small class="text-muted text-xs"><?= $totalKasbon ?> Pengajuan Terdaftar</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-kasbon p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="text-muted font-semibold text-uppercase text-xs">Total Sisa Pinjaman</small>
                    <div class="bg-danger bg-opacity-10 text-danger p-2 rounded-circle">
                        <i class="fas fa-file-invoice-dollar fs-6"></i>
                    </div>
                </div>
                <div class="fs-5 fw-bold text-danger mb-0">Rp <?= number_format($totalSisa, 0, ',', '.') ?></div>
                <small class="text-muted text-xs">Sisa Belum Pelunasan</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-kasbon p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="text-muted font-semibold text-uppercase text-xs">Menunggu Direktur</small>
                    <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-circle">
                        <i class="fas fa-clock fs-6"></i>
                    </div>
                </div>
                <div class="fs-5 fw-bold text-warning mb-0"><?= $pendingCount ?> Request</div>
                <small class="text-muted text-xs">Menunggu Approval</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-kasbon p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="text-muted font-semibold text-uppercase text-xs">Disetujui Direktur</small>
                    <div class="bg-success bg-opacity-10 text-success p-2 rounded-circle">
                        <i class="fas fa-check-circle fs-6"></i>
                    </div>
                </div>
                <div class="fs-5 fw-bold text-success mb-0"><?= $approvedCount ?> Request</div>
                <small class="text-muted text-xs">Siap Dicairkan / Lunas</small>
            </div>
        </div>
    </div>

    <!-- 3. Filter Tabs & Data Table Card -->
    <div class="kasbon-card-modern">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2 rounded-top-4">
            <div class="btn-group rounded-pill p-1 bg-light border" role="group">
                <a href="<?= base_url('admin/pengajuan/kasbon') ?>" class="btn btn-sm rounded-pill fw-semibold <?= empty($filterStatus) ? 'btn-primary shadow-sm' : 'btn-light text-secondary' ?>">Semua Data (<?= $totalKasbon ?>)</a>
                <a href="<?= base_url('admin/pengajuan/kasbon?status=pending') ?>" class="btn btn-sm rounded-pill fw-semibold <?= $filterStatus === 'pending' ? 'btn-warning text-dark shadow-sm' : 'btn-light text-secondary' ?>">Menunggu Direktur (<?= $pendingCount ?>)</a>
                <a href="<?= base_url('admin/pengajuan/kasbon?status=approved') ?>" class="btn btn-sm rounded-pill fw-semibold <?= $filterStatus === 'approved' ? 'btn-success shadow-sm' : 'btn-light text-secondary' ?>">Disetujui (<?= $approvedCount ?>)</a>
                <a href="<?= base_url('admin/pengajuan/kasbon?status=rejected') ?>" class="btn btn-sm rounded-pill fw-semibold <?= $filterStatus === 'rejected' ? 'btn-danger shadow-sm' : 'btn-light text-secondary' ?>">Ditolak (<?= $rejectedCount ?>)</a>
            </div>
            <small class="text-muted"><i class="fas fa-database me-1"></i> Form Kasbon Real-Time Synced</small>
        </div>

        <div class="card-body p-0">
            <div class="table-scroll-wrapper">
                <table class="table table-hover align-middle mb-0 text-sm">
                    <thead class="table-light border-bottom">
                        <tr>
                            <th class="ps-4" style="width: 50px;">#</th>
                            <th>No. Kasbon</th>
                            <th>Nama Karyawan</th>
                            <th>Nominal Pinjaman</th>
                            <th>Sisa Pinjaman</th>
                            <th>Keperluan</th>
                            <th class="text-center">Status Approval Direktur</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($kasbonList)): ?>
                            <?php foreach ($kasbonList as $idx => $k): ?>
                                <?php 
                                    $nama = $k['nama_lengkap'] ?? 'Karyawan CDW';
                                    $initials = strtoupper(substr($nama, 0, 2));
                                ?>
                                <tr>
                                    <td class="ps-4 fw-semibold text-secondary"><?= $idx + 1 ?></td>
                                    <td>
                                        <span class="badge bg-light text-dark border fw-bold font-monospace px-2.5 py-1.5"><?= esc($k['nomor_kasbon'] ?? 'KSB-'.$k['id']) ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-circle-sm"><?= $initials ?></div>
                                            <div>
                                                <div class="fw-bold text-dark"><?= esc($nama) ?></div>
                                                <small class="text-muted">NIK: <?= esc($k['nik'] ?? '-') ?> | <?= esc($k['jabatan'] ?? 'Staf') ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-bold text-primary">
                                        Rp <?= number_format($k['jumlah_kasbon'] ?? 0, 0, ',', '.') ?>
                                    </td>
                                    <td class="fw-bold text-danger">
                                        Rp <?= number_format($k['sisa_pinjaman'] ?? $k['jumlah_kasbon'] ?? 0, 0, ',', '.') ?>
                                    </td>
                                    <td style="max-width: 260px;">
                                        <?php $keperluanText = $k['keperluan'] ?? $k['alasan'] ?? $k['keterangan'] ?? '-'; ?>
                                        <small class="text-dark d-block text-truncate fw-semibold" title="<?= esc($keperluanText) ?>"><?= esc($keperluanText) ?></small>
                                        <small class="text-muted">Metode: <?= esc($k['metode_pembayaran'] ?? 'Potong Gaji') ?> (<?= esc($k['jumlah_angsuran'] ?? 1) ?> Bln)</small>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                            $st = $k['status_direktur'] ?? 'Menunggu';
                                            if ($st === 'Disetujui'): 
                                        ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-1.5 rounded-pill"><i class="fas fa-check-circle me-1"></i> Disetujui Direktur</span>
                                        <?php elseif ($st === 'Ditolak'): ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-1.5 rounded-pill"><i class="fas fa-times-circle me-1"></i> Ditolak</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 py-1.5 rounded-pill text-dark"><i class="fas fa-clock me-1"></i> Menunggu Approval</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Hapus Pengajuan" onclick="confirmDeleteKasbon(<?= $k['id'] ?>, '<?= esc($k['nomor_kasbon'] ?? 'KSB-'.$k['id']) ?>')">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-hand-holding-usd fs-1 mb-3 text-secondary opacity-50 d-block"></i>
                                    Belum ada data pengajuan kasbon terdaftar.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Kasbon Baru -->
<div class="modal fade" id="modalTambahKasbon" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white border-0 py-3">
                <h5 class="modal-title fw-bold text-sm"><i class="fas fa-hand-holding-usd text-warning me-2"></i> Form Pengajuan Kasbon Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/pengajuan/kasbon/simpan') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-secondary">Pemohon (Akun Anda) <span class="text-danger">*</span></label>
                        <?php if (!empty($userKaryawan)): ?>
                            <input type="text" class="form-control bg-light rounded-3 text-sm fw-bold" value="<?= esc($userKaryawan['nama_lengkap']) ?> (NIK: <?= esc($userKaryawan['nik'] ?? '-') ?> - <?= esc($userKaryawan['jabatan'] ?? 'Staf') ?>)" readonly>
                            <input type="hidden" name="karyawan_id" value="<?= esc($userKaryawan['id']) ?>">
                        <?php else: ?>
                            <select name="karyawan_id" class="form-select rounded-3 text-sm" required>
                                <option value="">-- Pilih Karyawan Pemohon --</option>
                                <?php foreach ($karyawanList as $k): ?>
                                    <option value="<?= $k['id'] ?>" <?= session()->get('karyawan_id') == $k['id'] ? 'selected' : '' ?>><?= esc($k['nama_lengkap']) ?> (NIK: <?= esc($k['nik'] ?? '-') ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-secondary">Jumlah Kasbon (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted fw-bold">Rp</span>
                            <input type="text" name="jumlah_kasbon" id="jumlah_kasbon" class="form-control border-start-0 rounded-end-3 text-sm fw-bold text-primary" placeholder="Contoh: 1.000.000" required autocomplete="off">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-secondary">Keperluan Penggunaan Kasbon <span class="text-danger">*</span></label>
                        <textarea name="keperluan" class="form-control rounded-3 text-sm" rows="3" placeholder="Jelaskan secara ringkas keperluan dana kasbon..." required></textarea>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-xs text-secondary">Metode Pengembalian</label>
                            <select name="metode_pembayaran" class="form-select rounded-3 text-sm">
                                <option value="Potong Gaji">Potong Gaji Bulanan</option>
                                <option value="Transfer Mandiri">Transfer Bank Mandiri</option>
                                <option value="Tunai">Tunai / Cash</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-xs text-secondary">Jumlah Angsuran (Bulan)</label>
                            <input type="number" name="jumlah_angsuran" class="form-control rounded-3 text-sm" value="1" min="1" max="24">
                        </div>
                    </div>

                    <!-- Box Simulasi Angsuran & Potongan Akhir Bulan -->
                    <div class="card border-0 rounded-4 p-3.5 mb-0 shadow-sm" style="background: #f0fdf4; border: 1.5px solid #86efac !important;">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-success border-opacity-20">
                            <span class="fw-bold text-success text-xs"><i class="fas fa-calculator me-1.5"></i> Simulasi Potongan Gaji Akhir Bulan</span>
                            <span class="badge bg-success text-white text-xs px-2.5 py-1 rounded-pill" id="simulasiMetodeLabel">Potong Gaji Bulanan</span>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6 border-end border-success border-opacity-20 pe-2">
                                <span class="text-muted d-block text-xs mb-1">Angsuran Baru Per Bulan:</span>
                                <div class="fw-bold text-dark fs-6" id="simulasiKasbonBaru">Rp 0</div>
                                <span class="text-muted text-xs d-block mt-0.5">Durasi: <strong id="simulasiJangkaWaktu" class="text-dark">1 Bulan</strong></span>
                            </div>
                            <div class="col-6 ps-2">
                                <span class="text-muted d-block text-xs mb-1">Cicilan Kasbon Lalu (Aktif):</span>
                                <div class="fw-bold text-warning fs-6" id="simulasiKasbonSebelumnya">Rp <?= number_format($tunggakanBulanIni ?? 0, 0, ',', '.') ?></div>
                                <?php if (!empty($activeKasbonCount) && $activeKasbonCount > 0): ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 px-2 py-0.5 rounded-pill text-xs mt-1 d-inline-block">
                                        <i class="fas fa-exclamation-circle me-1"></i> <?= $activeKasbonCount ?> Kasbon Belum Lunas
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-2 py-0.5 rounded-pill text-xs mt-1 d-inline-block">
                                        <i class="fas fa-check-circle me-1"></i> Tidak Ada Tunggakan
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Result Total Box -->
                        <div class="bg-white rounded-3 p-3 border border-success border-opacity-30 shadow-xs">
                            <div class="text-uppercase fw-bold text-muted text-xs mb-1" style="letter-spacing: 0.5px; font-size: 0.68rem; color: #475569 !important;">
                                TOTAL DIPOTONG DARI GAJI AKHIR BULAN:
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="fw-extrabold text-success fs-5 mb-0" id="simulasiTotalAkhirBulan" style="line-height: 1.2; font-size: 1.25rem;">
                                    Rp <?= number_format($tunggakanBulanIni ?? 0, 0, ',', '.') ?>
                                </div>
                                <span class="badge bg-success bg-opacity-15 text-success border border-success border-opacity-20 fw-bold px-2.5 py-1 rounded-pill text-xs">
                                    Potong Gaji
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light py-2.5">
                    <button type="button" class="btn btn-light rounded-pill px-3 text-sm fw-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 text-sm fw-bold shadow-sm"><i class="fas fa-paper-plane me-1.5"></i> Simpan & Kirim Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const tunggakanSebelumnya = <?= floatval($tunggakanBulanIni ?? 0) ?>;

function formatRupiahText(angka) {
    let number_string = angka.replace(/[^,\d]/g, '').toString(),
        split   = number_string.split(','),
        sisa    = split[0].length % 3,
        rupiah  = split[0].substr(0, sisa),
        ribuan  = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return rupiah;
}

function hitungSimulasiKasbon() {
    const inputJumlah = document.querySelector('input[name="jumlah_kasbon"]');
    const inputAngsuran = document.querySelector('input[name="jumlah_angsuran"]');
    const selectMetode = document.querySelector('select[name="metode_pembayaran"]');
    
    const labelMetode = document.getElementById('simulasiMetodeLabel');
    const labelBaru = document.getElementById('simulasiKasbonBaru');
    const labelSebelumnya = document.getElementById('simulasiKasbonSebelumnya');
    const labelTotalAkhirBulan = document.getElementById('simulasiTotalAkhirBulan');
    const labelJangkaWaktu = document.getElementById('simulasiJangkaWaktu');
    
    if (!inputJumlah || !inputAngsuran) return;
    
    // Parse raw digits from Rupiah format
    const rawDigits = inputJumlah.value.replace(/[^0-9]/g, '');
    const totalPinjaman = parseFloat(rawDigits) || 0;
    
    let angsuranBulan = parseInt(inputAngsuran.value) || 1;
    if (angsuranBulan < 1) angsuranBulan = 1;
    
    const cicilanBaru = Math.round(totalPinjaman / angsuranBulan);
    const totalPotonganAkhirBulan = cicilanBaru + tunggakanSebelumnya;
    
    if (labelMetode && selectMetode) {
        labelMetode.innerText = selectMetode.value || 'Potong Gaji Bulanan';
    }
    
    if (labelBaru) {
        labelBaru.innerText = 'Rp ' + cicilanBaru.toLocaleString('id-ID');
    }

    if (labelSebelumnya) {
        labelSebelumnya.innerText = 'Rp ' + tunggakanSebelumnya.toLocaleString('id-ID');
    }
    
    if (labelTotalAkhirBulan) {
        labelTotalAkhirBulan.innerText = 'Rp ' + totalPotonganAkhirBulan.toLocaleString('id-ID');
    }
    
    if (labelJangkaWaktu) {
        labelJangkaWaktu.innerText = angsuranBulan + ' Bulan';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const inputJumlah = document.querySelector('input[name="jumlah_kasbon"]');
    const inputAngsuran = document.querySelector('input[name="jumlah_angsuran"]');
    const selectMetode = document.querySelector('select[name="metode_pembayaran"]');
    
    if (inputJumlah) {
        inputJumlah.addEventListener('keyup', function(e) {
            this.value = formatRupiahText(this.value);
            hitungSimulasiKasbon();
        });
        inputJumlah.addEventListener('change', function(e) {
            this.value = formatRupiahText(this.value);
            hitungSimulasiKasbon();
        });
    }
    
    if (inputAngsuran) {
        inputAngsuran.addEventListener('input', hitungSimulasiKasbon);
        inputAngsuran.addEventListener('change', hitungSimulasiKasbon);
    }
    if (selectMetode) {
        selectMetode.addEventListener('change', hitungSimulasiKasbon);
    }
    
    hitungSimulasiKasbon();
});

function confirmDeleteKasbon(id, nomor) {
    Swal.fire({
        title: 'Hapus Pengajuan Kasbon?',
        text: 'Pengajuan kasbon "' + nomor + '" akan dihapus dari sistem.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-4 shadow-lg' }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= base_url('admin/pengajuan/kasbon/delete') ?>/' + id;
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '<?= csrf_token() ?>';
            csrfInput.value = '<?= csrf_hash() ?>';
            form.appendChild(csrfInput);

            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>

<?= view('admin/templates/footer', $dataHeader) ?>
