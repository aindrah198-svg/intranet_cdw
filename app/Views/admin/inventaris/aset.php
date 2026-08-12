<?php
$title = $title ?? 'Pengadaan Aset';
$templateData = [
    'title'  => $title,
    'user'   => session()->get('user') ?? ['name' => session()->get('name') ?? 'Administrator', 'role' => 'admin'],
    'active' => 'inventaris'
];

$totalAset = count($aset ?? []);
$menungguCount = 0;
$disetujuiCount = 0;
$ditolakCount = 0;
$totalInvestasi = 0;

foreach($aset as $item) {
    $st = strtolower($item['status'] ?? '');
    if($st === 'menunggu') $menungguCount++;
    elseif($st === 'disetujui') $disetujuiCount++;
    elseif($st === 'ditolak') $ditolakCount++;
    
    $totalInvestasi += floatval($item['estimasi_harga'] ?? 0) * intval($item['jumlah'] ?? 1);
}
?>

<?= view('admin/templates/header', $templateData) ?>
<?= view('admin/templates/sidebar', $templateData) ?>
<?= view('admin/templates/navbar', $templateData) ?>

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
        overflow-x: hidden !important;
    }

    .row {
        margin-left: -6px !important;
        margin-right: -6px !important;
    }

    .row > [class*="col-"] {
        padding-left: 6px !important;
        padding-right: 6px !important;
    }

    .card {
        max-width: 100% !important;
        box-sizing: border-box !important;
    }

    .pengadaan-card-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(30, 60, 114, 0.06), 0 2px 6px rgba(0, 0, 0, 0.02) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .stat-card-pengadaan {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(30, 60, 114, 0.05);
        transition: transform 0.2s ease;
    }

    .stat-card-pengadaan:hover {
        transform: translateY(-2px);
    }

    .stat-number-responsive {
        font-size: clamp(1rem, 2.5vw, 1.35rem);
        font-weight: 700;
        line-height: 1.2;
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
    
    .status-pill-menunggu {
        background: rgba(255, 193, 7, 0.15);
        color: #b58100;
        border: 1px solid rgba(255, 193, 7, 0.3);
    }

    .status-pill-disetujui {
        background: rgba(25, 135, 84, 0.12);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.25);
    }

    .status-pill-ditolak {
        background: rgba(220, 53, 69, 0.12);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.25);
    }

    .table-scroll-wrapper {
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
        display: block !important;
        margin-bottom: 0;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
    }

    .table-scroll-wrapper table {
        min-width: 820px !important;
        width: 100% !important;
        margin-bottom: 0 !important;
    }

    .table-scroll-wrapper::-webkit-scrollbar {
        height: 6px;
    }
    .table-scroll-wrapper::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    .table-scroll-wrapper::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    @media (max-width: 767.98px) {
        .header-mobile-flex {
            flex-direction: column;
            align-items: stretch !important;
        }
        .header-btn-group {
            width: 100%;
        }
        .header-btn-group .btn {
            width: 100%;
            justify-content: center;
            font-size: 0.85rem;
            padding: 9px 12px;
        }
        .stat-card-pengadaan {
            padding: 12px !important;
        }
        .stat-icon-wrapper {
            width: 40px !important;
            height: 40px !important;
            min-width: 40px;
        }
    }
</style>

<div class="container-fluid py-3 py-md-4">

    <!-- 1. Header Section -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-3 border border-light header-mobile-flex gap-3">
        <div class="d-flex align-items-center overflow-hidden">
            <div class="bg-gradient-primary text-white rounded-3 p-2 me-2.5 me-md-3 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 44px; height: 44px; background: linear-gradient(135deg, #1e3c72, #2a5298);">
                <i class="fas fa-desktop fs-5"></i>
            </div>
            <div class="overflow-hidden">
                <h4 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 1.25rem;">Pengadaan Aset Perusahaan</h4>
                <small class="text-muted d-none d-sm-inline">Pengajuan dan inventarisasi aset kantor, alat kerja IT, furniture & peralatan produksi.</small>
            </div>
        </div>
        <div class="header-btn-group d-flex gap-2">
            <a href="<?= base_url('admin/inventaris/aset/cetak') ?>" target="_blank" class="btn btn-outline-secondary rounded-pill shadow-sm d-inline-flex align-items-center fw-semibold">
                <i class="fas fa-print me-1.5"></i> <span>Cetak Rekap</span>
            </a>
            <button class="btn btn-primary rounded-pill shadow-sm d-inline-flex align-items-center fw-semibold" data-bs-toggle="modal" data-bs-target="#tambahAsetModal">
                <i class="fas fa-plus me-1.5"></i> <span>Ajukan Pengadaan Aset</span>
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-3" role="alert">
            <i class="fas fa-check-circle me-1.5"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-3" role="alert">
            <i class="fas fa-exclamation-circle me-1.5"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- 2. KPI Ringkasan Statistik -->
    <div class="row g-2 g-md-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="stat-card-pengadaan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center stat-icon-wrapper" style="width: 44px; height: 44px;">
                        <i class="fas fa-boxes-stacked text-primary"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="stat-number-responsive text-dark"><?= number_format($totalAset) ?></div>
                        <div class="text-muted text-truncate" style="font-size: 0.72rem; font-weight: 500;">Total Pengadaan</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-pengadaan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center stat-icon-wrapper" style="width: 44px; height: 44px;">
                        <i class="fas fa-hourglass-half text-warning"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="stat-number-responsive text-warning"><?= number_format($menungguCount) ?></div>
                        <div class="text-muted text-truncate" style="font-size: 0.72rem; font-weight: 500;">Menunggu Review</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-pengadaan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center stat-icon-wrapper" style="width: 44px; height: 44px;">
                        <i class="fas fa-circle-check text-success"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="stat-number-responsive text-success"><?= number_format($disetujuiCount) ?></div>
                        <div class="text-muted text-truncate" style="font-size: 0.72rem; font-weight: 500;">Disetujui</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-pengadaan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center stat-icon-wrapper" style="width: 44px; height: 44px;">
                        <i class="fas fa-coins text-info"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="stat-number-responsive text-info">Rp <?= number_format($totalInvestasi, 0, ',', '.') ?></div>
                        <div class="text-muted text-truncate" style="font-size: 0.72rem; font-weight: 500;">Estimasi Total</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Table Data Pengadaan Aset -->
    <div class="card pengadaan-card-modern p-3 p-md-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="fas fa-table-list text-primary me-2"></i> Daftar Pengadaan & Inventaris Aset
            </h5>
        </div>

        <div class="table-scroll-wrapper">
            <table class="table table-hover align-middle mb-0" id="tablePengadaanAset">
                <thead class="bg-light text-secondary">
                    <tr style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <th class="ps-3 py-3" style="width: 50px;">No</th>
                        <th class="py-3">Kode & Nama Aset</th>
                        <th class="py-3">Kategori</th>
                        <th class="py-3 text-end">Est. Harga Satuan</th>
                        <th class="py-3 text-center">Jumlah</th>
                        <th class="py-3 text-end">Total Estimasi</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3 text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.85rem;">
                    <?php if (!empty($aset)): ?>
                        <?php $no = 1; foreach ($aset as $a): ?>
                            <?php $totalItem = floatval($a['estimasi_harga'] ?? 0) * intval($a['jumlah'] ?? 1); ?>
                            <tr>
                                <td class="ps-3 fw-bold text-secondary"><?= $no++ ?></td>
                                <td>
                                    <strong class="text-primary d-block"><?= esc($a['nama_aset']) ?></strong>
                                    <small class="text-muted d-block" style="font-size: 0.72rem;"><?= esc($a['kode_pengadaan']) ?> • <?= date('d M Y', strtotime($a['created_at'] ?? date('Y-m-d'))) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2.5 py-1.5 rounded-pill">
                                        <?= esc($a['kategori']) ?>
                                    </span>
                                </td>
                                <td class="text-end fw-semibold text-dark">
                                    Rp <?= number_format($a['estimasi_harga'], 0, ',', '.') ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary bg-opacity-10 text-dark border px-2.5 py-1 rounded-pill fw-bold">
                                        <?= esc($a['jumlah']) ?> Unit
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-dark">
                                    Rp <?= number_format($totalItem, 0, ',', '.') ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                        $st = strtolower($a['status'] ?? 'menunggu');
                                        $badgeClass = 'status-pill-menunggu';
                                        $icon = 'fa-clock';
                                        if ($st === 'disetujui') { $badgeClass = 'status-pill-disetujui'; $icon = 'fa-check-circle'; }
                                        elseif ($st === 'ditolak') { $badgeClass = 'status-pill-ditolak'; $icon = 'fa-times-circle'; }
                                    ?>
                                    <span class="status-pill <?= $badgeClass ?>">
                                        <i class="fas <?= $icon ?> me-1"></i> <?= ucfirst(esc($a['status'])) ?>
                                    </span>
                                </td>
                                <td class="text-center pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('admin/inventaris/aset/review/' . $a['id']) ?>" class="btn btn-outline-primary rounded-start px-2" title="Review & Approval">
                                            <i class="fas fa-clipboard-check"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-outline-secondary px-2 btn-edit-aset" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editAsetModal"
                                                data-id="<?= $a['id'] ?>"
                                                data-nama="<?= esc($a['nama_aset']) ?>"
                                                data-kategori="<?= esc($a['kategori'] ?? 'Elektronik & IT') ?>"
                                                data-harga="<?= number_format($a['estimasi_harga'], 0, ',', '.') ?>"
                                                data-jumlah="<?= esc($a['jumlah'] ?? 1) ?>"
                                                data-status="<?= esc($a['status'] ?? 'menunggu') ?>"
                                                data-alasan="<?= esc($a['alasan_pengadaan'] ?? '') ?>"
                                                title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="<?= base_url('admin/inventaris/aset/delete/' . $a['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Yakin menghapus pengadaan aset ini?')">
                                            <button type="submit" class="btn btn-outline-danger rounded-end px-2" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Single Reusable Edit Modal Aset Outside Table -->
<div class="modal fade" id="editAsetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-edit text-primary me-2"></i> Edit Pengadaan Aset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/inventaris/aset/update') ?>" method="POST">
                <div class="modal-body py-3">
                    <input type="hidden" name="id" id="edit_admin_aset_id">
                    <div class="mb-3">
                        <label class="form-label text-xs fw-semibold text-dark">Nama Aset *</label>
                        <input type="text" name="nama_aset" id="edit_admin_aset_nama" class="form-control rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-xs fw-semibold text-dark">Kategori Aset *</label>
                        <select name="kategori" id="edit_admin_aset_kategori" class="form-select rounded-3" required>
                            <option value="Elektronik & IT">Elektronik & IT</option>
                            <option value="Peralatan Kantor">Peralatan Kantor</option>
                            <option value="Furniture">Furniture / Meubel</option>
                            <option value="Kendaraan Dinas">Kendaraan Dinas</option>
                            <option value="Mesin & Alat Kerja">Mesin & Alat Kerja</option>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-7">
                            <label class="form-label text-xs fw-semibold text-dark">Estimasi Harga Satuan (Rp) *</label>
                            <input type="text" name="estimasi_harga" id="edit_admin_aset_harga" class="form-control rounded-3 input-rupiah" oninput="formatRupiah(this)" required placeholder="Rp 15.000.000">
                        </div>
                        <div class="col-5">
                            <label class="form-label text-xs fw-semibold text-dark">Jumlah Unit *</label>
                            <input type="number" name="jumlah" id="edit_admin_aset_jumlah" class="form-control rounded-3" min="1" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-xs fw-semibold text-dark">Alasan Kebutuhan Aset</label>
                        <textarea name="alasan_pengadaan" id="edit_admin_aset_alasan" class="form-control rounded-3" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-xs fw-semibold text-dark">Status Review</label>
                        <select name="status" id="edit_admin_aset_status" class="form-select rounded-3">
                            <option value="menunggu">Menunggu Review</option>
                            <option value="disetujui">Disetujui</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Aset -->
<div class="modal fade" id="tambahAsetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-plus-circle text-primary me-2"></i> Pengajuan Pengadaan Aset Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/inventaris/aset/simpan') ?>" method="POST">
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label text-xs fw-semibold text-dark">Nama Aset yang Dibutuhkan *</label>
                        <input type="text" name="nama_aset" class="form-control rounded-3" placeholder="Contoh: Laptop Core i7 / Printer EcoTank / Kursi Kerja" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-xs fw-semibold text-dark">Kategori Aset *</label>
                        <select name="kategori" class="form-select rounded-3" required>
                            <option value="Elektronik & IT">Elektronik & IT</option>
                            <option value="Peralatan Kantor">Peralatan Kantor</option>
                            <option value="Furniture">Furniture</option>
                            <option value="Kendaraan Dinas">Kendaraan Dinas</option>
                            <option value="Mesin & Alat Kerja">Mesin & Alat Kerja</option>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-7">
                            <label class="form-label text-xs fw-semibold text-dark">Estimasi Harga Satuan (Rp) *</label>
                            <input type="text" name="estimasi_harga" class="form-control rounded-3 input-rupiah" oninput="formatRupiah(this)" placeholder="Rp 15.000.000" required>
                        </div>
                        <div class="col-5">
                            <label class="form-label text-xs fw-semibold text-dark">Jumlah Unit *</label>
                            <input type="number" name="jumlah" class="form-control rounded-3" value="1" min="1" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-xs fw-semibold text-dark">Alasan Pengadaan / Justifikasi *</label>
                        <textarea name="alasan_pengadaan" class="form-control rounded-3" rows="3" placeholder="Jelaskan kegunaan dan urgensi aset baru ini..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">Kirim Pengajuan Aset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function formatRupiah(input) {
        let value = input.value.replace(/[^,\d]/g, '').toString();
        let split = value.split(',');
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        input.value = rupiah ? 'Rp ' + rupiah : '';
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (typeof $.fn.DataTable !== 'undefined') {
            $('#tablePengadaanAset').DataTable({
                responsive: false,
                language: {
                    search: "Cari Aset:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    emptyTable: "Belum ada pengadaan aset yang diajukan.",
                    zeroRecords: "Tidak ada pengadaan aset yang cocok.",
                    paginate: { first: "Awal", last: "Akhir", next: "→", previous: "←" }
                }
            });
        }

        // Dynamic Edit Modal Populator
        $(document).on('click', '.btn-edit-aset', function() {
            $('#edit_admin_aset_id').val($(this).data('id'));
            $('#edit_admin_aset_nama').val($(this).data('nama'));
            $('#edit_admin_aset_kategori').val($(this).data('kategori'));
            let hg = $(this).data('harga') || '0';
            $('#edit_admin_aset_harga').val('Rp ' + hg);
            $('#edit_admin_aset_jumlah').val($(this).data('jumlah'));
            $('#edit_admin_aset_alasan').val($(this).data('alasan'));
            $('#edit_admin_aset_status').val(($(this).data('status') || 'menunggu').toLowerCase());
        });
    });
</script>

<?= view('admin/templates/footer', $templateData) ?>
