<?php
$title = $title ?? 'Penggajian Karyawan';
$templateData = [
    'title' => $title,
    'user'  => session()->get('user') ?? ['name' => 'Direktur', 'role' => 'direktur'],
    'active' => 'keuangan'
];
?>

<?= view('direktur/templates/header', $templateData) ?>
<?= view('direktur/templates/sidebar', $templateData) ?>
<?= view('direktur/templates/navbar', $templateData) ?>

<style>
    /* Styling Premium Modern Material & Glassmorphism */
    .payroll-card-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(30, 60, 114, 0.06), 0 2px 6px rgba(0, 0, 0, 0.02) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .payroll-card-modern:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 32px rgba(30, 60, 114, 0.12), 0 4px 10px rgba(0, 0, 0, 0.04) !important;
    }

    .avatar-glow-payroll {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        box-shadow: 0 4px 14px rgba(30, 60, 114, 0.35);
        border: 2px solid rgba(255, 255, 255, 0.9);
        width: 48px;
        height: 48px;
        font-size: 1.25rem;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 5px 12px;
        border-radius: 30px;
        font-size: 0.76rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    .status-pill-terbayar {
        background: rgba(25, 135, 84, 0.12);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.25);
    }

    .status-pill-pending {
        background: rgba(255, 193, 7, 0.15);
        color: #b58100;
        border: 1px solid rgba(255, 193, 7, 0.3);
    }

    .data-pill-bar {
        background: #f8fafc;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 12px;
        padding: 10px 14px;
        transition: all 0.2s ease;
        height: 100%;
    }
    
    .data-pill-bar:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    .data-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 2px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .data-value {
        font-size: 0.92rem;
        font-weight: 600;
        color: #0f172a;
    }

    .btn-action-pill {
        border-radius: 20px;
        padding: 6px 14px;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border: 1px solid transparent;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-action-view {
        background: rgba(13, 110, 253, 0.08);
        color: #0d6efd;
        border-color: rgba(13, 110, 253, 0.2);
    }
    .btn-action-view:hover {
        background: #0d6efd;
        color: #ffffff;
    }

    .btn-action-edit {
        background: rgba(255, 193, 7, 0.12);
        color: #b58100;
        border-color: rgba(255, 193, 7, 0.3);
    }
    .btn-action-edit:hover {
        background: #ffc107;
        color: #000000;
    }

    .btn-action-delete {
        background: rgba(220, 53, 69, 0.08);
        color: #dc3545;
        border-color: rgba(220, 53, 69, 0.2);
    }
    .btn-action-delete:hover {
        background: #dc3545;
        color: #ffffff;
    }

    .btn-action-pay {
        background: rgba(25, 135, 84, 0.1);
        color: #198754;
        border-color: rgba(25, 135, 84, 0.25);
    }
    .btn-action-pay:hover {
        background: #198754;
        color: #ffffff;
    }

    .btn-action-slip {
        background: rgba(108, 117, 125, 0.1);
        color: #495057;
        border-color: rgba(108, 117, 125, 0.25);
    }
    .btn-action-slip:hover {
        background: #495057;
        color: #ffffff;
    }

    .btn-action-wa {
        background: rgba(37, 211, 102, 0.12);
        color: #128c7e;
        border-color: rgba(37, 211, 102, 0.3);
    }
    .btn-action-wa:hover {
        background: #25d366;
        color: #ffffff;
    }

    .id-tag {
        background: #e2e8f0;
        color: #475569;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 6px;
        letter-spacing: 0.5px;
    }

    .stat-card-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(30, 60, 114, 0.05);
        transition: transform 0.2s ease;
    }
</style>

<div class="container-fluid py-3 py-md-4">

    <!-- 1. Header Section -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light flex-wrap gap-3">
        <div class="d-flex align-items-center">
            <div class="bg-gradient-primary text-white rounded-3 p-2.5 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px;">
                <i class="fas fa-hand-holding-usd fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark">Penggajian Karyawan</h4>
                <small class="text-muted d-none d-sm-inline">Buat & kelola penggajian staf secara individu (per orang) secara mandiri oleh Direktur.</small>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-primary rounded-pill px-3.5 py-2 shadow-sm d-inline-flex align-items-center text-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambahPenggajian">
                <i class="fas fa-plus me-1.5"></i> <span>Tambah Penggajian Karyawan</span>
            </button>
            <a href="<?= base_url('direktur/keuangan/penggajian/export-excel?bulan='.$bulan.'&tahun='.$tahun) ?>" class="btn btn-outline-success rounded-pill px-3 py-2 shadow-sm d-inline-flex align-items-center text-sm fw-semibold">
                <i class="fas fa-file-excel me-1.5"></i> <span class="d-none d-sm-inline">Export Excel</span>
            </a>
            <a href="<?= base_url('direktur/keuangan/penggajian/cetak?bulan='.$bulan.'&tahun='.$tahun) ?>" target="_blank" class="btn btn-outline-primary rounded-pill px-3 py-2 shadow-sm d-inline-flex align-items-center text-sm fw-semibold">
                <i class="fas fa-file-pdf me-1.5"></i> <span class="d-none d-sm-inline">Export PDF</span>
            </a>
        </div>
    </div>

    <!-- Flash Notifications -->
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- 2. Ringkasan Statistik -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card stat-card-modern p-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="fas fa-coins fa-lg text-primary"></i>
                    </div>
                    <div>
                        <small class="text-muted text-xs uppercase fw-bold d-block">Total Payroll (Periode <?= $bulan ?>/<?= $tahun ?>)</small>
                        <h4 class="fw-bold mb-0 text-dark">Rp <?= number_format($totalGaji, 0, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card stat-card-modern p-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="fas fa-check-circle fa-lg text-success"></i>
                    </div>
                    <div>
                        <small class="text-muted text-xs uppercase fw-bold d-block">Terbayar</small>
                        <h4 class="fw-bold mb-0 text-success">Rp <?= number_format($totalTransfer, 0, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card stat-card-modern p-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="fas fa-clock fa-lg text-warning"></i>
                    </div>
                    <div>
                        <small class="text-muted text-xs uppercase fw-bold d-block">Pending / Belum Cair</small>
                        <h4 class="fw-bold mb-0 text-warning">Rp <?= number_format($totalPending, 0, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Filter & Search Bar -->
    <div class="input-group mb-3 shadow-sm rounded-pill overflow-hidden border border-light">
        <span class="input-group-text bg-white border-end-0 text-muted ps-3.5">
            <i class="fas fa-search"></i>
        </span>
        <input type="text" id="searchPayroll" class="form-control border-start-0 border-end-0 ps-1 py-2.5" placeholder="Cari nama karyawan, NIK, jabatan, atau divisi...">
        <button class="btn btn-dark px-4 d-flex align-items-center gap-2 fw-semibold" type="button" data-bs-toggle="modal" data-bs-target="#filterPayrollModal">
            <i class="fas fa-sliders-h"></i> <span class="d-none d-sm-inline">Filter Periode</span>
        </button>
    </div>

    <!-- Modal Filter Periode -->
    <div class="modal fade" id="filterPayrollModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                        <i class="fas fa-calendar-alt text-primary me-2"></i> Filter Periode Penggajian
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="GET">
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">Bulan</label>
                                <select name="bulan" class="form-select rounded-3">
                                    <?php 
                                    $months = [
                                        '01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April',
                                        '05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus',
                                        '09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'
                                    ];
                                    foreach($months as $k => $v): 
                                    ?>
                                        <option value="<?= $k ?>" <?= $bulan == $k ? 'selected' : '' ?>><?= $v ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">Tahun</label>
                                <input type="number" name="tahun" class="form-control rounded-3" value="<?= $tahun ?>">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top py-3 px-4 rounded-bottom-4">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold border" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                            <i class="fas fa-check me-1"></i> Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Penggajian Karyawan (Per Orang) -->
    <div class="modal fade" id="modalTambahPenggajian" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                        <i class="fas fa-user-plus text-primary me-2"></i> Tambah Penggajian Karyawan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= base_url('direktur/keuangan/penggajian/simpan-detail') ?>" method="POST" id="formTambahGaji" enctype="multipart/form-data">
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <!-- Pilih Karyawan -->
                            <div class="col-12 col-md-8">
                                <label class="form-label fw-semibold text-sm">Pilih Karyawan *</label>
                                <select name="karyawan_id" id="selectKaryawanTambah" class="form-select rounded-3" required>
                                    <option value="">-- Pilih Karyawan --</option>
                                    <?php foreach($karyawanList as $kar): ?>
                                        <?php $sisaKasbon = $kasbonMap[$kar['id']] ?? 0; ?>
                                        <option value="<?= $kar['id'] ?>" data-gajipokok="<?= $kar['gaji_pokok'] ?? 0 ?>" data-kasbon="<?= $sisaKasbon ?>">
                                            <?= esc($kar['nama_lengkap']) ?> (NIK: <?= esc($kar['nik']) ?> - <?= esc($kar['jabatan']) ?>) <?= $sisaKasbon > 0 ? '[Kasbon: Rp '.number_format($sisaKasbon, 0, ',', '.').']' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="form-label fw-semibold text-sm">Bulan</label>
                                <select name="bulan" class="form-select rounded-3">
                                    <?php foreach($months as $k => $v): ?>
                                        <option value="<?= $k ?>" <?= $bulan == $k ? 'selected' : '' ?>><?= $v ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="form-label fw-semibold text-sm">Tahun</label>
                                <input type="number" name="tahun" class="form-control rounded-3" value="<?= $tahun ?>">
                            </div>

                            <!-- Gaji Pokok & Tunjangan -->
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold text-sm">Gaji Pokok (Rp) *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted fw-bold">Rp</span>
                                    <input type="text" name="gaji_pokok" id="inputGajiPokokTambah" class="form-control rounded-end input-rupiah calc-tambah" placeholder="0" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold text-sm">Tunjangan (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted fw-bold">Rp</span>
                                    <input type="text" name="tunjangan" id="inputTunjanganTambah" class="form-control rounded-end input-rupiah calc-tambah" placeholder="0">
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold text-sm">Bonus (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted fw-bold">Rp</span>
                                    <input type="text" name="bonus" id="inputBonusTambah" class="form-control rounded-end input-rupiah calc-tambah" placeholder="0">
                                </div>
                            </div>

                            <!-- Potongan & BPJS -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">Potongan Kasbon (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted fw-bold">Rp</span>
                                    <input type="text" name="potongan_kasbon" id="inputKasbonTambah" class="form-control rounded-end input-rupiah calc-tambah" placeholder="0">
                                </div>
                                <small class="text-primary d-none fw-bold mt-1" id="alertKasbonTambah"></small>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">Potongan Lainnya (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted fw-bold">Rp</span>
                                    <input type="text" name="potongan_lainnya" id="inputPotLainTambah" class="form-control rounded-end input-rupiah calc-tambah" placeholder="0">
                                </div>
                            </div>

                            <!-- BPJS Options (Karyawan) -->
                            <div class="col-12 bg-light p-3 rounded-3 border">
                                <label class="form-label fw-bold text-dark text-sm mb-2"><i class="fas fa-shield-alt text-primary me-1"></i> Potongan BPJS Karyawan (Opsional)</label>
                                <div class="row g-2">
                                    <div class="col-12 col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input calc-tambah-bpjs" type="checkbox" name="enable_bpjs_kes" id="cbBpjsKesTambah" value="1">
                                            <label class="form-check-label text-sm" for="cbBpjsKesTambah">
                                                BPJS Kesehatan (1%)
                                            </label>
                                        </div>
                                        <small class="text-muted d-block ms-4" id="valBpjsKesTambah">Rp 0</small>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input calc-tambah-bpjs" type="checkbox" name="enable_bpjs_jht" id="cbBpjsJhtTambah" value="1">
                                            <label class="form-check-label text-sm" for="cbBpjsJhtTambah">
                                                BPJS TK JHT (2%)
                                            </label>
                                        </div>
                                        <small class="text-muted d-block ms-4" id="valBpjsJhtTambah">Rp 0</small>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input calc-tambah-bpjs" type="checkbox" name="enable_bpjs_jp" id="cbBpjsJpTambah" value="1">
                                            <label class="form-check-label text-sm" for="cbBpjsJpTambah">
                                                BPJS TK JP (1%)
                                            </label>
                                        </div>
                                        <small class="text-muted d-block ms-4" id="valBpjsJpTambah">Rp 0</small>
                                    </div>
                                </div>
                                <small class="text-muted text-xs d-block mt-2 fs-7">* Catatan: Program JKK, JKM, dan JKP ditanggung penuh perusahaan.</small>
                            </div>

                            <!-- Calculation Preview -->
                            <div class="col-12 bg-primary bg-opacity-10 p-3 rounded-3 border border-primary border-opacity-25">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-sm fw-semibold text-dark">Total Pendapatan (Gaji + Tunjangan + Bonus):</span>
                                    <span class="fw-bold text-success" id="previewPendapatanTambah">Rp 0</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-sm fw-semibold text-dark">Total Potongan (Kasbon + BPJS + Lainnya):</span>
                                    <span class="fw-bold text-danger" id="previewPotonganTambah">Rp 0</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top border-primary border-opacity-25">
                                    <span class="fw-bold text-dark fs-6">Gaji Diterima (Take Home Pay):</span>
                                    <span class="fw-bold text-primary fs-5" id="previewGajiBersihTambah">Rp 0</span>
                                </div>
                            </div>

                            <!-- Bukti Transfer -->
                            <div class="col-12">
                                <label class="form-label fw-semibold text-sm"><i class="fas fa-file-upload text-primary me-1"></i> Upload Bukti Transfer (Opsional)</label>
                                <input type="file" name="bukti_transfer" class="form-control rounded-3" accept="image/*,application/pdf">
                                <small class="text-muted text-xs d-block mt-1">* File gambar (JPG, PNG, WEBP) dikompres otomatis oleh sistem agar ringan.</small>
                            </div>

                            <!-- Status Pembayaran & Catatan -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">Status Pembayaran</label>
                                <select name="status_pembayaran" class="form-select rounded-3">
                                    <option value="Pending">Pending / Belum Cair</option>
                                    <option value="Dibayar">Dibayar (Langsung Lunas)</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">Catatan / Keterangan</label>
                                <input type="text" name="catatan" class="form-control rounded-3" placeholder="Catatan opsional...">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top py-3 px-4 rounded-bottom-4">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold border" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                            <i class="fas fa-save me-1"></i> Simpan Penggajian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 4. Payroll Cards Container Grid -->
    <div class="row g-3" id="payrollCardContainer">
        <?php if(empty($penggajian)): ?>
            <div class="col-12 text-center py-5 bg-white rounded-3 shadow-sm border border-light">
                <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3 opacity-50"></i>
                <p class="text-muted fw-semibold mb-2">Belum ada data penggajian untuk periode <?= $bulan ?>/<?= $tahun ?>.</p>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-semibold text-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPenggajian">
                    <i class="fas fa-plus me-1"></i> Buat Penggajian Karyawan Sekarang
                </button>
            </div>
        <?php else: ?>
            <?php foreach($penggajian as $p): ?>
                <?php 
                $initial = !empty($p['nama_lengkap']) ? strtoupper(substr($p['nama_lengkap'], 0, 1)) : 'K';
                $st = strtolower($p['status_pembayaran'] ?? 'pending');
                $isPaid = ($st == 'dibayar' || $st == 'sukses');
                $statusPillClass = $isPaid ? 'status-pill-terbayar' : 'status-pill-pending';
                $statusIcon = $isPaid ? 'fas fa-check-circle' : 'fas fa-clock';

                $sisaKasbonAktif = $kasbonMap[$p['karyawan_id']] ?? 0;
                ?>
                <div class="col-12 payroll-card-wrapper">
                    <div class="card payroll-card-modern p-3 p-sm-4">
                        <div class="d-flex align-items-center justify-content-between pb-3 border-bottom border-light flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-glow-payroll text-white rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0">
                                    <?= $initial ?>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <h3 class="mb-0 fw-bold text-dark" style="font-size: 1.15rem;">
                                            <?= esc($p['nama_karyawan'] ?? $p['nama_lengkap']) ?>
                                        </h3>
                                        <span class="id-tag">NIK: <?= esc($p['nik'] ?? '-') ?></span>
                                    </div>
                                    <div class="mt-1">
                                        <span class="status-pill <?= $statusPillClass ?>">
                                            <i class="<?= $statusIcon ?> me-1"></i> <?= ucfirst($st) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                <button type="button" class="btn-action-pill btn-action-view" data-bs-toggle="modal" data-bs-target="#modalDetailGaji<?= $p['id'] ?>">
                                    <i class="fas fa-eye"></i> Detail
                                </button>

                                <?php if(!$isPaid): ?>
                                    <form action="<?= base_url('direktur/keuangan/penggajian/bayar/'.$p['id'].'?bulan='.$bulan.'&tahun='.$tahun) ?>" method="POST" class="d-inline">
                                        <button type="submit" class="btn-action-pill btn-action-pay" onclick="return confirm('Tandai gajian ini sudah Dibayar?')">
                                            <i class="fas fa-check-double"></i> Tandai Dibayar
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <button type="button" class="btn-action-pill btn-action-edit" data-bs-toggle="modal" data-bs-target="#modalEditGajiRecord<?= $p['id'] ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>

                                <form action="<?= base_url('direktur/keuangan/penggajian/delete/'.$p['id'].'?bulan='.$bulan.'&tahun='.$tahun) ?>" method="POST" class="d-inline">
                                    <button type="submit" class="btn-action-pill btn-action-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus data penggajian ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>

                                <a href="<?= base_url('direktur/keuangan/penggajian/cetak-slip/'.($p['karyawan_id'] ?? $p['id'])) ?>" target="_blank" class="btn-action-pill btn-action-slip">
                                    <i class="fas fa-print"></i> Slip PDF
                                </a>
                            </div>
                        </div>

                        <div class="py-3">
                            <div class="row g-2.5">
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="data-pill-bar">
                                        <div class="data-label">
                                            <i class="fas fa-briefcase text-primary"></i> Jabatan / Divisi
                                        </div>
                                        <div class="data-value"><?= esc($p['jabatan'] ?? '-') ?> <?= !empty($p['divisi']) ? '| '.esc($p['divisi']) : '' ?></div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="data-pill-bar">
                                        <div class="data-label">
                                            <i class="fas fa-money-bill-wave text-primary"></i> Gaji Pokok
                                        </div>
                                        <div class="data-value text-dark">Rp <?= number_format($p['gaji_pokok'] ?? 0, 0, ',', '.') ?></div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="data-pill-bar">
                                        <div class="data-label">
                                            <i class="fas fa-plus-circle text-success"></i> Tunjangan & Bonus
                                        </div>
                                        <div class="data-value text-success">+ Rp <?= number_format($p['total_tunjangan'] ?? 0, 0, ',', '.') ?></div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="data-pill-bar">
                                        <div class="data-label">
                                            <i class="fas fa-wallet text-primary"></i> Take Home Pay
                                        </div>
                                        <div class="data-value text-primary font-weight-bold">Rp <?= number_format($p['gaji_bersih'] ?? 0, 0, ',', '.') ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Detail Penggajian -->
                <div class="modal fade" id="modalDetailGaji<?= $p['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content rounded-4 border-0 shadow-lg">
                            <div class="modal-header border-bottom py-3 px-4">
                                <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                                    <i class="fas fa-info-circle text-primary me-2"></i> Rincian Penggajian - <?= esc($p['nama_lengkap'] ?? $p['nama_karyawan']) ?>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6 border-end">
                                        <h6 class="fw-bold text-success border-bottom pb-2 mb-3"><i class="fas fa-plus-circle me-1"></i> PENERIMAAN</h6>
                                        <div class="d-flex justify-content-between py-1 border-bottom">
                                            <span class="text-muted">Gaji Pokok:</span>
                                            <span class="fw-semibold">Rp <?= number_format($p['gaji_pokok'] ?? 0, 0, ',', '.') ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between py-1 border-bottom">
                                            <span class="text-muted">Tunjangan:</span>
                                            <span class="fw-semibold">Rp <?= number_format($p['tunjangan'] ?? 0, 0, ',', '.') ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between py-1 border-bottom">
                                            <span class="text-muted">Bonus:</span>
                                            <span class="fw-semibold">Rp <?= number_format($p['bonus'] ?? 0, 0, ',', '.') ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between py-2 fw-bold text-success">
                                            <span>TOTAL PENDAPATAN:</span>
                                            <span>Rp <?= number_format(($p['gaji_pokok'] ?? 0) + ($p['total_tunjangan'] ?? 0), 0, ',', '.') ?></span>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h6 class="fw-bold text-danger border-bottom pb-2 mb-3"><i class="fas fa-minus-circle me-1"></i> POTONGAN GAJI</h6>
                                        <div class="d-flex justify-content-between py-1 border-bottom">
                                            <span class="text-muted">Potongan Kasbon:</span>
                                            <span class="fw-semibold">Rp <?= number_format($p['potongan_kasbon'] ?? 0, 0, ',', '.') ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between py-1 border-bottom">
                                            <span class="text-muted">BPJS Kesehatan (1%):</span>
                                            <span class="fw-semibold">Rp <?= number_format($p['potongan_bpjs_kes'] ?? 0, 0, ',', '.') ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between py-1 border-bottom">
                                            <span class="text-muted">BPJS Ketenagakerjaan JHT (2%):</span>
                                            <span class="fw-semibold">Rp <?= number_format($p['potongan_bpjs_jht'] ?? 0, 0, ',', '.') ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between py-1 border-bottom">
                                            <span class="text-muted">BPJS Ketenagakerjaan JP (1%):</span>
                                            <span class="fw-semibold">Rp <?= number_format($p['potongan_bpjs_jp'] ?? 0, 0, ',', '.') ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between py-1 border-bottom">
                                            <span class="text-muted">Potongan Lainnya:</span>
                                            <span class="fw-semibold">Rp <?= number_format($p['potongan_lainnya'] ?? 0, 0, ',', '.') ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between py-2 fw-bold text-danger">
                                            <span>TOTAL POTONGAN:</span>
                                            <span>Rp <?= number_format($p['total_potongan'] ?? 0, 0, ',', '.') ?></span>
                                        </div>
                                    </div>

                                    <div class="col-12 bg-light p-3 rounded-3 border mt-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-bold fs-6 text-dark">GAJI BERSIH (TAKE HOME PAY):</span>
                                            <span class="fw-bold fs-4 text-primary">Rp <?= number_format($p['gaji_bersih'] ?? 0, 0, ',', '.') ?></span>
                                        </div>
                                        <?php if($sisaKasbonAktif > 0): ?>
                                            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                                <span class="fw-bold text-warning">SISA KASBON AKTIF KARYAWAN:</span>
                                                <span class="fw-bold text-danger">Rp <?= number_format($sisaKasbonAktif, 0, ',', '.') ?></span>
                                            </div>
                                        <?php endif; ?>
                                        <?php if(!empty($p['catatan'])): ?>
                                            <small class="text-muted d-block mt-2"><strong>Catatan:</strong> <?= esc($p['catatan']) ?></small>
                                        <?php endif; ?>
                                    </div>

                                    <?php if(!empty($p['bukti_transfer'])): ?>
                                        <div class="col-12 mt-3">
                                            <label class="fw-bold text-dark text-sm mb-2"><i class="fas fa-file-invoice text-success me-1"></i> Bukti Transfer Pembayaran:</label>
                                            <div class="p-2 border rounded text-center bg-white">
                                                <?php $ext = strtolower(pathinfo($p['bukti_transfer'], PATHINFO_EXTENSION)); ?>
                                                <?php if($ext === 'pdf'): ?>
                                                    <a href="<?= base_url($p['bukti_transfer']) ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill">
                                                        <i class="fas fa-file-pdf me-1"></i> Lihat Dokumen Bukti Transfer PDF
                                                    </a>
                                                <?php else: ?>
                                                    <img src="<?= base_url($p['bukti_transfer']) ?>" alt="Bukti Transfer" class="img-fluid rounded shadow-sm" style="max-height: 250px;">
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="modal-footer bg-light border-top py-3 px-4 rounded-bottom-4">
                                <a href="<?= base_url('direktur/keuangan/penggajian/cetak-slip/'.($p['karyawan_id'] ?? $p['id']).'?autoprint=1') ?>" target="_blank" class="btn btn-outline-primary rounded-pill px-3 fw-semibold">
                                    <i class="fas fa-file-pdf me-1"></i> Cetak / Simpan PDF Slip
                                </a>
                                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-semibold ms-auto" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Edit Gaji Record -->
                <div class="modal fade" id="modalEditGajiRecord<?= $p['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content rounded-4 border-0 shadow-lg">
                            <div class="modal-header border-bottom py-3 px-4">
                                <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                                    <i class="fas fa-edit text-primary me-2"></i> Edit Penggajian - <?= esc($p['nama_lengkap'] ?? $p['nama_karyawan']) ?>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="<?= base_url('direktur/keuangan/penggajian/simpan-detail') ?>" method="POST" enctype="multipart/form-data">
                                <div class="modal-body p-4">
                                    <input type="hidden" name="karyawan_id" value="<?= $p['karyawan_id'] ?>">
                                    <input type="hidden" name="bulan" value="<?= $bulan ?>">
                                    <input type="hidden" name="tahun" value="<?= $tahun ?>">

                                    <div class="row g-3">
                                        <div class="col-12 col-md-4">
                                            <label class="form-label fw-semibold text-sm">Gaji Pokok (Rp) *</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted fw-bold">Rp</span>
                                                <input type="text" name="gaji_pokok" class="form-control rounded-end input-rupiah" value="<?= number_format($p['gaji_pokok'] ?? 0, 0, ',', '.') ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label class="form-label fw-semibold text-sm">Tunjangan (Rp)</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted fw-bold">Rp</span>
                                                <input type="text" name="tunjangan" class="form-control rounded-end input-rupiah" value="<?= number_format($p['tunjangan'] ?? 0, 0, ',', '.') ?>">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label class="form-label fw-semibold text-sm">Bonus (Rp)</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted fw-bold">Rp</span>
                                                <input type="text" name="bonus" class="form-control rounded-end input-rupiah" value="<?= number_format($p['bonus'] ?? 0, 0, ',', '.') ?>">
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label class="form-label fw-semibold text-sm">Potongan Kasbon (Rp)</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted fw-bold">Rp</span>
                                                <input type="text" name="potongan_kasbon" class="form-control rounded-end input-rupiah" value="<?= number_format($p['potongan_kasbon'] ?? 0, 0, ',', '.') ?>">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label fw-semibold text-sm">Potongan Lainnya (Rp)</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted fw-bold">Rp</span>
                                                <input type="text" name="potongan_lainnya" class="form-control rounded-end input-rupiah" value="<?= number_format($p['potongan_lainnya'] ?? 0, 0, ',', '.') ?>">
                                            </div>
                                        </div>

                                        <!-- BPJS Fields -->
                                        <div class="col-12 col-md-4">
                                            <label class="form-label fw-semibold text-sm">BPJS Kesehatan (1%)</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted fw-bold">Rp</span>
                                                <input type="text" name="potongan_bpjs_kes" class="form-control rounded-end input-rupiah" value="<?= number_format($p['potongan_bpjs_kes'] ?? 0, 0, ',', '.') ?>">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label class="form-label fw-semibold text-sm">BPJS TK JHT (2%)</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted fw-bold">Rp</span>
                                                <input type="text" name="potongan_bpjs_jht" class="form-control rounded-end input-rupiah" value="<?= number_format($p['potongan_bpjs_jht'] ?? 0, 0, ',', '.') ?>">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label class="form-label fw-semibold text-sm">BPJS TK JP (1%)</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted fw-bold">Rp</span>
                                                <input type="text" name="potongan_bpjs_jp" class="form-control rounded-end input-rupiah" value="<?= number_format($p['potongan_bpjs_jp'] ?? 0, 0, ',', '.') ?>">
                                            </div>
                                        </div>

                                        <!-- Upload Bukti Transfer -->
                                        <div class="col-12">
                                            <label class="form-label fw-semibold text-sm"><i class="fas fa-file-upload text-primary me-1"></i> Upload Bukti Transfer Baru (Opsional)</label>
                                            <input type="file" name="bukti_transfer" class="form-control rounded-3" accept="image/*,application/pdf">
                                            <?php if(!empty($p['bukti_transfer'])): ?>
                                                <small class="text-success d-block mt-1"><i class="fas fa-check me-1"></i> Bukti transfer sudah diupload.</small>
                                            <?php endif; ?>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label class="form-label fw-semibold text-sm">Status Pembayaran</label>
                                            <select name="status_pembayaran" class="form-select rounded-3">
                                                <option value="Pending" <?= ($p['status_pembayaran'] ?? '') == 'Pending' ? 'selected' : '' ?>>Pending / Belum Cair</option>
                                                <option value="Dibayar" <?= ($p['status_pembayaran'] ?? '') == 'Dibayar' ? 'selected' : '' ?>>Dibayar</option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label fw-semibold text-sm">Catatan / Keterangan</label>
                                            <input type="text" name="catatan" class="form-control rounded-3" value="<?= esc($p['catatan'] ?? '') ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light border-top py-3 px-4 rounded-bottom-4">
                                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold border" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">Update Penggajian</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchPayroll');
    const cardWrappers = document.querySelectorAll('.payroll-card-wrapper');

    if(searchInput) {
        searchInput.addEventListener('keyup', function() {
            const query = this.value.toLowerCase().trim();

            cardWrappers.forEach(card => {
                const text = card.textContent.toLowerCase();
                if(text.includes(query)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }

    // Helper Rupiah Format for Input
    function formatRupiahString(numStr) {
        if (!numStr) return '';
        let number_string = numStr.toString().replace(/[^0-9]/g, '');
        if (!number_string) return '';
        let sisa = number_string.length % 3;
        let rupiah = number_string.substr(0, sisa);
        let ribuan = number_string.substr(sisa).match(/\d{3}/g);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return rupiah;
    }

    function parseRupiahNumber(valStr) {
        if (!valStr) return 0;
        let clean = valStr.toString().replace(/[^0-9]/g, '');
        return parseFloat(clean) || 0;
    }

    function formatRupiahDisplay(num) {
        return 'Rp ' + Math.round(num).toLocaleString('id-ID');
    }

    // Auto Format Input Rupiah on type
    document.addEventListener('input', function(e) {
        if (e.target && e.target.classList.contains('input-rupiah')) {
            let cursorPosition = e.target.selectionStart;
            let originalLength = e.target.value.length;
            
            e.target.value = formatRupiahString(e.target.value);
            
            let newLength = e.target.value.length;
            cursorPosition = cursorPosition + (newLength - originalLength);
            e.target.setSelectionRange(cursorPosition, cursorPosition);
        }
    });

    // Modal Tambah Auto Calculation & Dropdown Change
    const selectKaryawan = document.getElementById('selectKaryawanTambah');
    const inputGajiPokok = document.getElementById('inputGajiPokokTambah');
    const inputTunjangan = document.getElementById('inputTunjanganTambah');
    const inputBonus = document.getElementById('inputBonusTambah');
    const inputKasbon = document.getElementById('inputKasbonTambah');
    const inputPotLain = document.getElementById('inputPotLainTambah');
    
    const cbKes = document.getElementById('cbBpjsKesTambah');
    const cbJht = document.getElementById('cbBpjsJhtTambah');
    const cbJp  = document.getElementById('cbBpjsJpTambah');

    const valKes = document.getElementById('valBpjsKesTambah');
    const valJht = document.getElementById('valBpjsJhtTambah');
    const valJp  = document.getElementById('valBpjsJpTambah');

    const prevPendapatan = document.getElementById('previewPendapatanTambah');
    const prevPotongan = document.getElementById('previewPotonganTambah');
    const prevGajiBersih = document.getElementById('previewGajiBersihTambah');

    if(selectKaryawan) {
        selectKaryawan.addEventListener('change', function() {
            const selectedOpt = this.options[this.selectedIndex];
            const gajiPokok = parseFloat(selectedOpt.getAttribute('data-gajipokok')) || 0;
            const kasbonSisa = parseFloat(selectedOpt.getAttribute('data-kasbon')) || 0;
            const alertKasbon = document.getElementById('alertKasbonTambah');

            if(inputGajiPokok) {
                inputGajiPokok.value = formatRupiahString(gajiPokok.toString());
            }

            if(kasbonSisa > 0) {
                if(inputKasbon) {
                    inputKasbon.value = formatRupiahString(kasbonSisa.toString());
                }
                if(alertKasbon) {
                    alertKasbon.textContent = 'ℹ Karyawan memiliki sisa kasbon aktif: ' + formatRupiahDisplay(kasbonSisa) + '. Anda dapat menyesuaikan nominal cicilan.';
                    alertKasbon.classList.remove('d-none');
                }
            } else {
                if(inputKasbon) inputKasbon.value = '';
                if(alertKasbon) alertKasbon.classList.add('d-none');
            }

            recalculateTambah();
        });
    }

    function recalculateTambah() {
        const gp = parseRupiahNumber(inputGajiPokok.value);
        const tj = parseRupiahNumber(inputTunjangan.value);
        const bn = parseRupiahNumber(inputBonus.value);
        const ks = parseRupiahNumber(inputKasbon.value);
        const pl = parseRupiahNumber(inputPotLain.value);

        const bKes = cbKes.checked ? (gp * 0.01) : 0;
        const bJht = cbJht.checked ? (gp * 0.02) : 0;
        const bJp  = cbJp.checked  ? (gp * 0.01) : 0;

        valKes.textContent = formatRupiahDisplay(bKes);
        valJht.textContent = formatRupiahDisplay(bJht);
        valJp.textContent  = formatRupiahDisplay(bJp);

        const totalPendapatan = gp + tj + bn;
        const totalPotongan = ks + bKes + bJht + bJp + pl;
        const gajiBersih = totalPendapatan - totalPotongan;

        prevPendapatan.textContent = formatRupiahDisplay(totalPendapatan);
        prevPotongan.textContent = formatRupiahDisplay(totalPotongan);
        prevGajiBersih.textContent = formatRupiahDisplay(gajiBersih);
    }

    document.querySelectorAll('.calc-tambah, .calc-tambah-bpjs').forEach(el => {
        el.addEventListener('input', recalculateTambah);
        el.addEventListener('change', recalculateTambah);
    });
});
</script>

<?= view('direktur/templates/footer', $templateData) ?>
