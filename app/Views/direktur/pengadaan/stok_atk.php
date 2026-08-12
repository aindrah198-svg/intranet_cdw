<?php
$title = $title ?? 'Monitoring Stok ATK';
$templateData = [
    'title' => $title,
    'user'  => session()->get('user') ?? ['name' => 'Direktur', 'role' => 'direktur'],
    'active' => 'pengadaan'
];

$totalJenis = count($stok ?? []);
$amanCount = 0;
$menipisCount = 0;
$habisCount = 0;

foreach($stok as $item) {
    $st = strtolower($item['status_stok'] ?? '');
    if($st === 'menipis') $menipisCount++;
    elseif($st === 'habis' || $st === 'kosong') $habisCount++;
    else $amanCount++;
}
?>

<?= view('direktur/templates/header', $templateData) ?>
<?= view('direktur/templates/sidebar', $templateData) ?>
<?= view('direktur/templates/navbar', $templateData) ?>

<style>
    /* Prevent Any Horizontal Page Overflow */
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

    /* Glassmorphism & Modern Card Styling */
    .pengadaan-card-modern {
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

    .pengadaan-card-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 32px rgba(30, 60, 114, 0.12) !important;
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
        font-size: clamp(1.1rem, 2.5vw, 1.4rem);
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
    
    .status-pill-aman {
        background: rgba(25, 135, 84, 0.12);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.25);
    }

    .status-pill-menipis {
        background: rgba(255, 193, 7, 0.15);
        color: #b58100;
        border: 1px solid rgba(255, 193, 7, 0.3);
    }

    .status-pill-habis {
        background: rgba(220, 53, 69, 0.12);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.25);
    }

    /* Inner Table Scroll Container - Keeps Page Fit 100% */
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
        min-width: 720px !important;
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
            <div class="bg-gradient-primary text-white rounded-3 p-2 me-2.5 me-md-3 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 44px; height: 44px;">
                <i class="fas fa-boxes fs-5"></i>
            </div>
            <div class="overflow-hidden">
                <h4 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 1.25rem;">Monitoring Stok ATK</h4>
                <small class="text-muted d-none d-sm-inline">Pantau ketersediaan dan saldo inventaris Alat Tulis Kantor secara real-time.</small>
            </div>
        </div>
        <div>
            <a href="<?= base_url('direktur/pengadaan/pengajuan-atk') ?>" class="btn btn-outline-primary rounded-pill shadow-sm d-inline-flex align-items-center text-sm fw-semibold">
                <i class="fas fa-pen-nib me-1.5"></i> <span>Tinjau Pengajuan ATK</span>
            </a>
        </div>
    </div>

    <!-- 2. KPI Statistik Cards -->
    <div class="row g-2 g-md-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card stat-card-pengadaan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center stat-icon-wrapper" style="width: 44px; height: 44px;">
                        <i class="fas fa-cubes text-primary"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Total Items</small>
                        <div class="stat-number-responsive text-primary text-truncate"><?= number_format($totalJenis) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card-pengadaan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center stat-icon-wrapper" style="width: 44px; height: 44px;">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Stok Aman</small>
                        <div class="stat-number-responsive text-success text-truncate"><?= number_format($amanCount) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card-pengadaan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center stat-icon-wrapper" style="width: 44px; height: 44px;">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Stok Menipis</small>
                        <div class="stat-number-responsive text-warning text-truncate"><?= number_format($menipisCount) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card-pengadaan p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-danger bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center stat-icon-wrapper" style="width: 44px; height: 44px;">
                        <i class="fas fa-ban text-danger"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted text-xs uppercase fw-bold d-block text-truncate">Stok Habis</small>
                        <div class="stat-number-responsive text-danger text-truncate"><?= number_format($habisCount) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Tabel Monitoring Stok ATK -->
    <div class="card pengadaan-card-modern p-3 p-md-4 mb-3">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-0 fs-6 fs-md-5 d-flex align-items-center">
                    <i class="fas fa-clipboard-list text-primary me-2"></i> Inventaris Stok Alat Tulis Kantor
                </h5>
                <small class="text-muted text-xs d-block mt-0.5">Geser tabel kesamping untuk melihat rincian barang & lokasi di mobile.</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <input type="text" id="searchTableStok" class="form-control form-control-sm rounded-pill px-3" style="max-width: 200px;" placeholder="Cari nama / kode barang...">
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold text-nowrap" data-bs-toggle="modal" data-bs-target="#tambahStokModal">
                    <i class="fas fa-plus-circle me-1"></i> Tambah Item Stok
                </button>
            </div>
        </div>

        <div class="table-scroll-wrapper">
            <table class="table table-hover align-middle text-sm mb-0" id="stokTable">
                <thead class="table-light text-nowrap">
                    <tr>
                        <th width="12%">Kode Barang</th>
                        <th width="28%">Nama Barang</th>
                        <th width="15%">Kategori</th>
                        <th width="14%">Jumlah Stok</th>
                        <th width="15%">Lokasi Simpan</th>
                        <th width="10%">Status Stok</th>
                        <th width="6%" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($stok)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada inventaris stok ATK terdata.</td></tr>
                    <?php else: ?>
                        <?php foreach($stok as $s): ?>
                        <tr>
                            <td class="fw-bold text-dark text-nowrap"><?= esc($s['kode_barang']) ?></td>
                            <td class="fw-semibold text-dark"><?= esc($s['nama_barang']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= esc($s['kategori'] ?: 'Umum') ?></span></td>
                            <td class="fw-bold text-primary text-nowrap"><?= esc($s['stok']) ?> <?= esc($s['satuan']) ?></td>
                            <td class="text-nowrap"><small class="text-muted"><i class="fas fa-map-marker-alt me-1 text-danger"></i><?= esc($s['lokasi'] ?: 'Gudang Utama') ?></small></td>
                            <td>
                                <?php
                                    $st = strtolower($s['status_stok'] ?? '');
                                    $pillClass = 'status-pill-aman';
                                    $statusText = 'AMAN';
                                    if ($st === 'menipis') {
                                        $pillClass = 'status-pill-menipis';
                                        $statusText = 'MENIPIS';
                                    } elseif ($st === 'habis' || $st === 'kosong') {
                                        $pillClass = 'status-pill-habis';
                                        $statusText = 'HABIS';
                                    }
                                ?>
                                <span class="status-pill <?= $pillClass ?>">
                                    <i class="fas fa-circle fs-6 me-1.5" style="font-size: 0.4rem !important;"></i>
                                    <?= $statusText ?>
                                </span>
                            </td>
                            <td class="text-end text-nowrap">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-outline-info rounded-pill px-2 py-1 text-xs fw-semibold me-1" title="Detail Item" onclick="showDetailStok(<?= esc(json_encode($s)) ?>)">
                                        <i class="fas fa-eye me-1"></i> Detail
                                    </button>
                                     <button type="button" 
                                             class="btn btn-xs btn-outline-warning rounded-pill px-2 py-1 text-xs fw-semibold btn-edit-stok" 
                                             data-bs-toggle="modal" 
                                             data-bs-target="#editStokModal"
                                             data-id="<?= $s['id'] ?>"
                                             data-nama="<?= esc($s['nama_barang']) ?>"
                                             data-kategori="<?= esc($s['kategori'] ?? '') ?>"
                                             data-satuan="<?= esc($s['satuan'] ?? 'Pcs') ?>"
                                             data-stok="<?= esc($s['stok'] ?? 0) ?>"
                                             data-lokasi="<?= esc($s['lokasi'] ?? '') ?>"
                                             title="Edit Data">
                                         <i class="fas fa-edit"></i> Edit
                                     </button>
                                    <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2 py-1 text-xs fw-semibold" title="Hapus Item" onclick="confirmDeleteStok('<?= base_url('direktur/pengadaan/stok-atk/delete/'.$s['id']) ?>', '<?= esc(addslashes($s['nama_barang'])) ?>')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
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

<!-- Single Reusable Modal Edit Stok ATK Outside Table -->
<div class="modal fade text-start" id="editStokModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white rounded-top-4 py-3">
                <h5 class="modal-title fs-6 fw-bold mb-0"><i class="fas fa-edit me-2"></i> Edit Data Stok ATK</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('direktur/pengadaan/stok-atk/update') ?>" method="POST" onsubmit="const btn = this.querySelector('button[type=submit]'); if(btn){ btn.disabled = true; btn.innerHTML = '<i class=\'fas fa-spinner fa-spin me-1\'></i> Menyimpan...'; }">
                <input type="hidden" name="id" id="edit_direktur_stok_id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Nama Barang ATK *</label>
                        <input type="text" class="form-control rounded-3" name="nama_barang" id="edit_direktur_stok_nama" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-xs text-dark">Jumlah Stok *</label>
                            <input type="number" class="form-control rounded-3 fw-bold" name="stok" id="edit_direktur_stok_jumlah" min="0" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-xs text-dark">Satuan</label>
                            <input type="text" class="form-control rounded-3" name="satuan" id="edit_direktur_stok_satuan">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Kategori</label>
                        <input type="text" class="form-control rounded-3" name="kategori" id="edit_direktur_stok_kategori">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Lokasi Simpan</label>
                        <input type="text" class="form-control rounded-3" name="lokasi" id="edit_direktur_stok_lokasi">
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4 py-2">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-semibold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Stok ATK -->
<div class="modal fade" id="detailStokModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-primary text-white rounded-top-4 py-3">
                <h5 class="modal-title fs-6 fw-bold mb-0"><i class="fas fa-info-circle me-2"></i> Detail Inventaris Stok ATK</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4 pb-3 border-bottom">
                    <div class="badge bg-primary bg-opacity-10 text-primary px-3 py-1.5 rounded-pill mb-2 fw-bold" id="detailKodeBarang">-</div>
                    <h5 class="fw-bold text-dark mb-1" id="detailNamaBarang">-</h5>
                    <span class="badge bg-secondary rounded-pill px-3 py-1 text-xs" id="detailKategori">-</span>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <small class="text-muted text-xs uppercase d-block fw-semibold mb-1">Jumlah Stok Saat Ini</small>
                        <h4 class="fw-bold text-primary mb-0" id="detailJumlahStok">-</h4>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-xs uppercase d-block fw-semibold mb-1">Status Ketersediaan</small>
                        <div id="detailStatusStokPill">-</div>
                    </div>
                    <div class="col-12 border-top pt-3">
                        <small class="text-muted text-xs uppercase d-block fw-semibold mb-1"><i class="fas fa-map-marker-alt text-danger me-1"></i> Lokasi Penyimpanan</small>
                        <span class="fw-semibold text-dark fs-6" id="detailLokasi">-</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light rounded-bottom-4 py-2">
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Stok ATK -->
<div class="modal fade" id="tambahStokModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white rounded-top-4 py-3">
                <h5 class="modal-title fs-6 fw-bold mb-0"><i class="fas fa-plus-circle me-2"></i> Tambah Item Stok ATK Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('direktur/pengadaan/stok-atk/simpan') ?>" method="POST" onsubmit="const btn = this.querySelector('button[type=submit]'); if(btn){ btn.disabled = true; btn.innerHTML = '<i class=\'fas fa-spinner fa-spin me-1\'></i> Menyimpan...'; }">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Nama Barang ATK *</label>
                        <input type="text" class="form-control rounded-3" name="nama_barang" required placeholder="Cth: Kertas A4 70gr, Spidol Boardmarker">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-xs text-dark">Jumlah Stok *</label>
                            <input type="number" class="form-control rounded-3" name="stok" value="10" min="0" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-xs text-dark">Satuan</label>
                            <input type="text" class="form-control rounded-3" name="satuan" value="Pcs" placeholder="Rim / Pcs / Box / Botol">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Kategori</label>
                        <input type="text" class="form-control rounded-3" name="kategori" value="Alat Tulis" placeholder="Kertas / Alat Tulis / Tinta / Perlengkapan">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Lokasi Simpan</label>
                        <input type="text" class="form-control rounded-3" name="lokasi" value="Gudang ATK Lt. 2" placeholder="Cth: Gudang Utama, Ruang IT, Rak ATK 1">
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4 py-2">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-semibold">Simpan Item Stok</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SweetAlert2 CDN & Custom Interactivity Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (session()->getFlashdata('success')): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '<?= esc(session()->getFlashdata('success')) ?>',
        confirmButtonColor: '#0d6efd',
        timer: 3000,
        timerProgressBar: true,
        customClass: { popup: 'rounded-4 shadow-lg' }
    });
});
</script>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '<?= esc(session()->getFlashdata('error')) ?>',
        confirmButtonColor: '#dc3545',
        customClass: { popup: 'rounded-4 shadow-lg' }
    });
});
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchTableStok');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#stokTable tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }

    $(document).on('click', '.btn-edit-stok', function() {
        $('#edit_direktur_stok_id').val($(this).data('id'));
        $('#edit_direktur_stok_nama').val($(this).data('nama'));
        $('#edit_direktur_stok_kategori').val($(this).data('kategori'));
        $('#edit_direktur_stok_satuan').val($(this).data('satuan'));
        $('#edit_direktur_stok_jumlah').val($(this).data('stok'));
        $('#edit_direktur_stok_lokasi').val($(this).data('lokasi'));
    });
});

function showDetailStok(item) {
    document.getElementById('detailKodeBarang').innerText = item.kode_barang || '-';
    document.getElementById('detailNamaBarang').innerText = item.nama_barang || '-';
    document.getElementById('detailKategori').innerText = item.kategori || 'Umum';
    document.getElementById('detailJumlahStok').innerText = (item.stok || 0) + ' ' + (item.satuan || 'Pcs');
    document.getElementById('detailLokasi').innerText = item.lokasi || 'Gudang Utama';

    const st = (item.status_stok || '').toLowerCase();
    let statusHtml = '<span class="status-pill status-pill-aman"><i class="fas fa-circle me-1" style="font-size:0.4rem;"></i> AMAN</span>';
    if (st === 'menipis') {
        statusHtml = '<span class="status-pill status-pill-menipis"><i class="fas fa-circle me-1" style="font-size:0.4rem;"></i> MENIPIS</span>';
    } else if (st === 'habis' || st === 'kosong') {
        statusHtml = '<span class="status-pill status-pill-habis"><i class="fas fa-circle me-1" style="font-size:0.4rem;"></i> HABIS</span>';
    }
    document.getElementById('detailStatusStokPill').innerHTML = statusHtml;

    const modal = new bootstrap.Modal(document.getElementById('detailStokModal'));
    modal.show();
}

function confirmDeleteStok(url, name) {
    Swal.fire({
        title: 'Apakah Anda Yakin?',
        text: "Anda akan menghapus stok '" + name + "'. Tindakan ini tidak dapat dibatalkan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash me-1"></i> Ya, Hapus!',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-4 shadow-lg' }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>

<?= view('direktur/templates/footer', $templateData) ?>
