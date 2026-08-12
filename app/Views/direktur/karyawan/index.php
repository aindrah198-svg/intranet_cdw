<?= $this->include('direktur/templates/header') ?>
<?= $this->include('direktur/templates/sidebar') ?>
<?= $this->include('direktur/templates/navbar') ?>

<?php
// Ekstrak Divisi dan Status secara dinamis dari data database
$divisiList = array_values(array_unique(array_filter(array_column($karyawan, 'divisi'))));
sort($divisiList);

$statusList = array_values(array_unique(array_filter(array_column($karyawan, 'status_karyawan'))));
sort($statusList);
?>

<style>
    /* Styling Premium Modern Material & Glassmorphism */
    .employee-card-modern {
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
    
    .employee-card-modern:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 32px rgba(30, 60, 114, 0.12), 0 4px 10px rgba(0, 0, 0, 0.04) !important;
    }

    /* Avatar Soft Glowing Ring */
    .avatar-glow {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        box-shadow: 0 4px 14px rgba(30, 60, 114, 0.35);
        border: 2px solid rgba(255, 255, 255, 0.9);
        width: 50px;
        height: 50px;
        font-size: 1.3rem;
    }

    /* Status Pill Frosted Glass */
    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 5px 12px;
        border-radius: 30px;
        font-size: 0.76rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    
    .status-pill-tetap {
        background: rgba(25, 135, 84, 0.12);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.25);
    }
    
    .status-pill-kontrak {
        background: rgba(13, 202, 240, 0.12);
        color: #0dcaf0;
        border: 1px solid rgba(13, 202, 240, 0.25);
    }

    .status-pill-probation {
        background: rgba(255, 193, 7, 0.15);
        color: #b58100;
        border: 1px solid rgba(255, 193, 7, 0.3);
    }

    .status-pill-staff {
        background: rgba(13, 110, 253, 0.12);
        color: #0d6efd;
        border: 1px solid rgba(13, 110, 253, 0.25);
    }

    .status-pill-default {
        background: rgba(108, 117, 125, 0.12);
        color: #6c757d;
        border: 1px solid rgba(108, 117, 125, 0.25);
    }

    /* Bilah Data Horizontal (Data Bars) */
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

    /* Modern Action Pills */
    .btn-action-pill {
        border-radius: 20px;
        padding: 7px 18px;
        font-size: 0.82rem;
        font-weight: 600;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid transparent;
        text-decoration: none;
    }

    .btn-action-view {
        background: rgba(13, 110, 253, 0.08);
        color: #0d6efd;
        border-color: rgba(13, 110, 253, 0.2);
    }

    .btn-action-view:hover {
        background: #0d6efd;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }

    .btn-action-edit {
        background: rgba(13, 202, 240, 0.1);
        color: #0891b2;
        border-color: rgba(13, 202, 240, 0.25);
    }

    .btn-action-edit:hover {
        background: #0891b2;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(8, 145, 178, 0.3);
    }

    .btn-action-delete {
        background: rgba(220, 53, 69, 0.08);
        color: #dc3545;
        border-color: rgba(220, 53, 69, 0.2);
    }

    .btn-action-delete:hover {
        background: #dc3545;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
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

    /* Eye-Catching Responsive Pagination Styling */
    .pagination-modern .page-link {
        border: none !important;
        color: #475569;
        font-weight: 600;
        font-size: 0.88rem;
        min-width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 0 2px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        background: transparent;
    }

    .pagination-modern .page-link:hover {
        background: rgba(30, 60, 114, 0.08);
        color: #1e3c72;
    }

    .pagination-modern .page-item.active .page-link {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(30, 60, 114, 0.35);
        font-weight: 700;
    }

    .pagination-modern .page-item.disabled .page-link {
        color: #cbd5e1;
        background: transparent;
    }

    .active-filter-badge {
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 20px;
        background: rgba(30, 60, 114, 0.1);
        color: #1e3c72;
        font-weight: 600;
    }
</style>

<div class="container-fluid py-3 py-md-4">

    <!-- 1. Header Section (Terpadu & Estetik) -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light">
        <div class="d-flex align-items-center">
            <div class="bg-gradient-primary text-white rounded-3 p-2.5 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px;">
                <i class="fas fa-users-cog fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark">Kelola Karyawan</h4>
                <small class="text-muted d-none d-sm-inline">Kelola dan pantau informasi komprehensif SDM CDW Engineering secara real-time.</small>
            </div>
        </div>
        <a href="<?= base_url('direktur/karyawan/tambah') ?>" class="btn btn-primary rounded-pill px-3.5 py-2 shadow-sm d-inline-flex align-items-center text-sm fw-semibold">
            <i class="fas fa-plus me-1.5"></i> <span class="d-none d-md-inline">Tambah Karyawan</span><span class="d-inline d-md-none">Tambah</span>
        </a>
    </div>

    <!-- 2. Search & Filter Bar (Input Group Dengan Centered Modal Filter) -->
    <div class="input-group mb-3 shadow-sm rounded-pill overflow-hidden border border-light">
        <span class="input-group-text bg-white border-end-0 text-muted ps-3.5">
            <i class="fas fa-search"></i>
        </span>
        <input type="text" id="searchKaryawan" class="form-control border-start-0 border-end-0 ps-1 py-2.5" placeholder="Cari nama karyawan, NIK, divisi, jabatan, atau email...">
        <button class="btn btn-dark px-4 d-flex align-items-center gap-2 fw-semibold" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">
            <i class="fas fa-sliders-h"></i> <span class="d-none d-sm-inline">Filter</span>
        </button>
    </div>

    <!-- Indicator Active Filter (Jika sedang memfilter) -->
    <div id="activeFilterTags" class="d-flex align-items-center gap-2 mb-3 d-none">
        <span class="text-xs text-muted fw-bold">Filter Aktif:</span>
        <div id="filterTagContainer" class="d-flex gap-1 flex-wrap"></div>
        <button type="button" class="btn btn-link btn-sm text-danger p-0 text-decoration-none ms-2" id="btnClearActiveFilter" style="font-size: 0.8rem;">
            <i class="fas fa-times-circle me-1"></i> Hapus Filter
        </button>
    </div>

    <!-- Modal Filter Lanjutan (Centered & Responsive) -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-bottom py-3 px-4">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center" id="filterModalLabel">
                        <i class="fas fa-filter text-primary me-2"></i> Filter Data Karyawan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Filter Status Karyawan (Dinamis dari Database) -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-sm">Status Karyawan</label>
                            <select id="filterStatus" class="form-select rounded-3">
                                <option value="">Semua Status</option>
                                <?php foreach ($statusList as $st): ?>
                                    <option value="<?= esc($st) ?>"><?= esc($st) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Filter Divisi (Dinamis dari Database) -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-sm">Divisi</label>
                            <select id="filterDivisi" class="form-select rounded-3">
                                <option value="">Semua Divisi</option>
                                <?php foreach ($divisiList as $div): ?>
                                    <option value="<?= esc($div) ?>"><?= esc($div) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-3 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold border" id="btnResetFilter">
                        <i class="fas fa-undo me-1"></i> Reset
                    </button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-semibold" data-bs-dismiss="modal" id="btnApplyFilter">
                        <i class="fas fa-check me-1"></i> Terapkan Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Daftar Kartu Karyawan (Card List Grid) -->
    <div class="row g-3" id="karyawanCardContainer">
        <?php foreach ($karyawan as $kar): ?>
            <?php
                $status = $kar['status_karyawan'] ?? '';
                $statusPillClass = 'status-pill-default';
                $statusIcon = 'fas fa-info-circle';

                if ($status === 'Tetap') {
                    $statusPillClass = 'status-pill-tetap';
                    $statusIcon = 'fas fa-check-circle';
                } elseif ($status === 'Kontrak') {
                    $statusPillClass = 'status-pill-kontrak';
                    $statusIcon = 'fas fa-clock';
                } elseif ($status === 'Probation') {
                    $statusPillClass = 'status-pill-probation';
                    $statusIcon = 'fas fa-user-clock';
                } elseif ($status === 'Staff') {
                    $statusPillClass = 'status-pill-staff';
                    $statusIcon = 'fas fa-user';
                }
                
                $initial = !empty($kar['nama_lengkap']) ? strtoupper(substr($kar['nama_lengkap'], 0, 1)) : 'K';
                $employeeIdTag = 'E' . str_pad($kar['id'], 3, '0', STR_PAD_LEFT);
            ?>
            <div class="col-12 karyawan-card-wrapper" data-status="<?= esc($status) ?>" data-divisi="<?= esc($kar['divisi']) ?>" data-jabatan="<?= esc($kar['jabatan']) ?>">
                <div class="card employee-card-modern karyawan-card p-3 p-sm-4">
                    
                    <!-- Visual Header Kartu -->
                    <div class="d-flex align-items-center justify-content-between pb-3 border-bottom border-light flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <!-- Avatar Lingkaran Inisial -->
                            <div class="avatar-glow text-white rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0">
                                <?= $initial ?>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <!-- Nama Karyawan -->
                                    <h3 class="mb-0 fw-bold text-dark" style="font-size: 1.18rem; letter-spacing: -0.2px;">
                                        <?= esc($kar['nama_lengkap']) ?>
                                    </h3>
                                    <!-- Tag ID Karyawan -->
                                    <span class="id-tag">ID: <?= $employeeIdTag ?></span>
                                </div>
                                <!-- Lencana Status Chip Frosted Glass -->
                                <div class="mt-1.5">
                                    <span class="status-pill <?= $statusPillClass ?>">
                                        <i class="<?= $statusIcon ?> me-1"></i> <?= esc($status) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Penataan Bidang Data (Bilah Data Horizontal Grid) -->
                    <div class="py-3">
                        <div class="row g-2.5">
                            <!-- NIK -->
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="data-pill-bar">
                                    <div class="data-label">
                                        <i class="far fa-id-card text-primary"></i> NIK
                                    </div>
                                    <div class="data-value text-break">
                                        <?= esc($kar['nik']) ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="data-pill-bar">
                                    <div class="data-label">
                                        <i class="far fa-envelope text-primary"></i> Email
                                    </div>
                                    <div class="data-value text-break">
                                        <?= !empty($kar['email']) ? esc($kar['email']) : '-' ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Divisi & Jabatan -->
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="data-pill-bar">
                                    <div class="data-label">
                                        <i class="fas fa-briefcase text-primary"></i> Divisi & Jabatan
                                    </div>
                                    <div class="data-value">
                                        <?= esc($kar['divisi']) ?> <span class="text-muted font-weight-normal">|</span> <?= esc($kar['jabatan']) ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Tanggal Masuk -->
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="data-pill-bar">
                                    <div class="data-label">
                                        <i class="far fa-calendar-alt text-primary"></i> Tgl Masuk
                                    </div>
                                    <div class="data-value">
                                        <?= !empty($kar['tanggal_masuk']) ? date('d M Y', strtotime($kar['tanggal_masuk'])) : '-' ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi Terintegrasi (Modern Action Pills) -->
                    <div class="pt-2 border-top border-light d-flex justify-content-end align-items-center gap-2 flex-wrap">
                        <a href="<?= base_url('direktur/karyawan/detail/'.$kar['id']) ?>" class="btn-action-pill btn-action-view" title="Lihat Detail">
                            <i class="far fa-eye"></i> Lihat
                        </a>
                        <a href="<?= base_url('direktur/karyawan/edit/'.$kar['id']) ?>" class="btn-action-pill btn-action-edit" title="Edit Data">
                            <i class="far fa-edit"></i> Edit
                        </a>
                        <form action="<?= base_url('direktur/karyawan/delete/'.$kar['id']) ?>" method="post" class="d-inline form-delete-karyawan">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn-action-pill btn-action-delete btn-delete-karyawan" data-nama="<?= esc($kar['nama_lengkap']) ?>" title="Hapus Data">
                                <i class="far fa-trash-alt"></i> Hapus
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Pesan Jika Data Tidak Ditemukan -->
    <div id="noResultsMessage" class="alert alert-info text-center rounded-4 shadow-sm p-4 d-none my-3">
        <i class="fas fa-search fa-2x mb-2 text-primary"></i>
        <h5 class="fw-bold mb-1">Data Karyawan Tidak Ditemukan</h5>
        <p class="mb-0 text-muted small">Coba ubah kata kunci pencarian atau reset filter Anda.</p>
    </div>

    <!-- 4. Control Bar Bawah: Pengaturan Tampilkan Per Halaman + Info + Eye-Catching Responsive Pagination -->
    <div class="card shadow-sm rounded-4 border-0 p-3 mt-4 mb-5 bg-white">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            
            <!-- Pengaturan Jumlah Tampilan Data (1-5, 1-10, 1-25, 1-50, 1-75, 1-100, Semua) -->
            <div class="d-flex align-items-center gap-2">
                <span class="text-xs text-muted fw-bold text-uppercase">Tampilkan:</span>
                <select id="perPageSelect" class="form-select form-select-sm rounded-pill shadow-xs fw-semibold text-dark border-light px-3 py-1.5" style="width: auto; cursor: pointer;">
                    <option value="5" selected>5 Karyawan / hal</option>
                    <option value="10">10 Karyawan / hal</option>
                    <option value="25">25 Karyawan / hal</option>
                    <option value="50">50 Karyawan / hal</option>
                    <option value="75">75 Karyawan / hal</option>
                    <option value="100">100 Karyawan / hal</option>
                    <option value="all">Tampilkan Semua</option>
                </select>
            </div>

            <!-- Teks Statistik Info Data -->
            <div class="text-muted text-sm fw-semibold text-center" id="paginationInfo">
                Menampilkan 1 - 5 dari <?= count($karyawan) ?> karyawan
            </div>

            <!-- Eye-Catching Pagination Component -->
            <div id="paginationContainer" class="d-flex justify-content-center">
                <!-- Diisi otomatis secara responsif oleh JavaScript -->
            </div>

        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput   = document.getElementById('searchKaryawan');
    const filterStatus  = document.getElementById('filterStatus');
    const filterDivisi  = document.getElementById('filterDivisi');
    const perPageSelect = document.getElementById('perPageSelect');
    const btnReset      = document.getElementById('btnResetFilter');
    const btnApply      = document.getElementById('btnApplyFilter');
    const btnClearActive = document.getElementById('btnClearActiveFilter');
    const cards         = Array.from(document.querySelectorAll('.karyawan-card-wrapper'));
    const paginationEl  = document.getElementById('paginationContainer');
    const infoEl        = document.getElementById('paginationInfo');
    const noResultsEl   = document.getElementById('noResultsMessage');
    const activeTagsBox = document.getElementById('activeFilterTags');
    const tagContainer  = document.getElementById('filterTagContainer');

    let currentPage = 1;
    let itemsPerPage = 5; // Default 5 items per page

    function filterAndPaginate() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const statusVal  = filterStatus ? filterStatus.value.toLowerCase().trim() : '';
        const divisiVal  = filterDivisi ? filterDivisi.value.toLowerCase().trim() : '';

        // Deteksi Pengaturan Per Page (5, 10, 25, 50, 75, 100, All)
        const perPageVal = perPageSelect ? perPageSelect.value : '5';
        itemsPerPage = perPageVal === 'all' ? 999999 : parseInt(perPageVal) || 5;

        // Update indikator filter aktif
        updateActiveFilterTags(statusVal, divisiVal);

        // 1. Filter Kartu berdasarkan Search, Status, & Divisi (atau Jabatan)
        let visibleCards = cards.filter(card => {
            const text    = card.innerText.toLowerCase();
            const status  = (card.getAttribute('data-status') || '').toLowerCase();
            const divisi  = (card.getAttribute('data-divisi') || '').toLowerCase();
            const jabatan = (card.getAttribute('data-jabatan') || '').toLowerCase();

            const matchSearch = !searchTerm || text.includes(searchTerm);
            const matchStatus = !statusVal  || status.includes(statusVal);
            const matchDivisi = !divisiVal  || divisi.includes(divisiVal) || jabatan.includes(divisiVal);

            return matchSearch && matchStatus && matchDivisi;
        });

        const totalVisible = visibleCards.length;
        const totalPages   = Math.ceil(totalVisible / itemsPerPage) || 1;

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        // Sembunyikan semua kartu terlebih dahulu
        cards.forEach(card => card.style.display = 'none');

        // 2. Tampilkan pesan kosong jika 0 hasil
        if (totalVisible === 0) {
            if (noResultsEl) noResultsEl.classList.remove('d-none');
            if (infoEl) infoEl.textContent = 'Tidak ada data karyawan yang cocok.';
            if (paginationEl) paginationEl.innerHTML = '';
            return;
        } else {
            if (noResultsEl) noResultsEl.classList.add('d-none');
        }

        // 3. Paginate Kartu yang Lolos Filter
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex   = Math.min(startIndex + itemsPerPage, totalVisible);

        for (let i = startIndex; i < endIndex; i++) {
            visibleCards[i].style.display = 'block';
        }

        // Update info text
        if (infoEl) {
            if (itemsPerPage >= 999999) {
                infoEl.textContent = `Menampilkan seluruh ${totalVisible} karyawan`;
            } else {
                infoEl.textContent = `Menampilkan ${startIndex + 1} - ${endIndex} dari ${totalVisible} karyawan`;
            }
        }

        // 4. Render Responsive Eye-Catching Pagination
        renderPagination(totalPages);
    }

    function updateActiveFilterTags(statusVal, divisiVal) {
        if (!activeTagsBox || !tagContainer) return;
        tagContainer.innerHTML = '';

        if (statusVal || divisiVal) {
            activeTagsBox.classList.remove('d-none');
            if (statusVal) {
                tagContainer.innerHTML += `<span class="active-filter-badge">Status: ${filterStatus.options[filterStatus.selectedIndex].text}</span>`;
            }
            if (divisiVal) {
                tagContainer.innerHTML += `<span class="active-filter-badge">Divisi: ${filterDivisi.options[filterDivisi.selectedIndex].text}</span>`;
            }
        } else {
            activeTagsBox.classList.add('d-none');
        }
    }

    function renderPagination(totalPages) {
        if (!paginationEl) return;
        if (totalPages <= 1) {
            paginationEl.innerHTML = '';
            return;
        }

        let html = '<ul class="pagination pagination-modern mb-0 shadow-sm rounded-pill overflow-hidden bg-white p-1 border border-light">';
        
        // Tombol Sebelumnya
        html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <button class="page-link rounded-pill" data-page="${currentPage - 1}">
                        <i class="fas fa-chevron-left me-1"></i> <span class="d-none d-sm-inline">Sebelumnya</span>
                    </button>
                 </li>`;

        // Algoritma Smart Page Windowing untuk Tampilan Responsif (Mobile & Desktop)
        const maxVisibleButtons = window.innerWidth < 576 ? 3 : 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisibleButtons / 2));
        let endPage = Math.min(totalPages, startPage + maxVisibleButtons - 1);

        if (endPage - startPage + 1 < maxVisibleButtons) {
            startPage = Math.max(1, endPage - maxVisibleButtons + 1);
        }

        if (startPage > 1) {
            html += `<li class="page-item"><button class="page-link rounded-circle" data-page="1">1</button></li>`;
            if (startPage > 2) {
                html += `<li class="page-item disabled"><span class="page-link border-0">...</span></li>`;
            }
        }

        for (let p = startPage; p <= endPage; p++) {
            html += `<li class="page-item ${p === currentPage ? 'active' : ''}">
                        <button class="page-link rounded-circle" data-page="${p}">${p}</button>
                     </li>`;
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                html += `<li class="page-item disabled"><span class="page-link border-0">...</span></li>`;
            }
            html += `<li class="page-item"><button class="page-link rounded-circle" data-page="${totalPages}">${totalPages}</button></li>`;
        }

        // Tombol Selanjutnya
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <button class="page-link rounded-pill" data-page="${currentPage + 1}">
                        <span class="d-none d-sm-inline">Selanjutnya</span> <i class="fas fa-chevron-right ms-1"></i>
                    </button>
                 </li>`;

        html += '</ul>';
        paginationEl.innerHTML = html;

        // Bind Event Klik Pagination
        paginationEl.querySelectorAll('.page-link').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const targetPage = parseInt(this.getAttribute('data-page'));
                if (targetPage && targetPage >= 1 && targetPage <= totalPages && targetPage !== currentPage) {
                    currentPage = targetPage;
                    filterAndPaginate();
                    window.scrollTo({ top: 120, behavior: 'smooth' });
                }
            });
        });
    }

    // Event Listeners Real-time
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            currentPage = 1;
            filterAndPaginate();
        });
    }

    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            currentPage = 1;
            filterAndPaginate();
        });
    }

    if (btnApply) {
        btnApply.addEventListener('click', function() {
            currentPage = 1;
            filterAndPaginate();
        });
    }

    function resetAllFilters() {
        if (filterStatus) filterStatus.value = '';
        if (filterDivisi) filterDivisi.value = '';
        if (searchInput)  searchInput.value  = '';
        if (perPageSelect) perPageSelect.value = '5';
        currentPage = 1;
        filterAndPaginate();
    }

    if (btnReset) btnReset.addEventListener('click', resetAllFilters);
    if (btnClearActive) btnClearActive.addEventListener('click', resetAllFilters);

    // Initial Trigger saat halaman dimuat
    filterAndPaginate();
});

// Real-time Notifikasi & SweetAlert Delete Handler
// Gunakan window.addEventListener('load') agar jQuery sudah dimuat oleh footer
window.addEventListener('load', function () {
    if (typeof $ === 'undefined' || typeof Swal === 'undefined') return;
    function updateNotificationBadge() {
        const notifUrl = '<?= base_url("direktur/dashboard/get-notifications") ?>';
        $.ajax({
            url: notifUrl,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response && response.status === 'success') {
                    const count = parseInt(response.count) || 0;
                    const $badge = $('.navbar-badge');
                    if ($badge.length) {
                        $badge.text(count);
                        if (count > 0) {
                            $badge.removeClass('d-none').show();
                        } else {
                            $badge.addClass('d-none').hide();
                        }
                    }
                    const $actionBadge = $('.navbar-notif-action-badge');
                    if ($actionBadge.length) {
                        $actionBadge.text(count + ' Perlu Action');
                    }
                }
            }
        });
    }

    const flashSuccess = '<?= session()->getFlashdata('success') ? esc(session()->getFlashdata('success'), 'js') : '' ?>';
    const flashError   = '<?= session()->getFlashdata('error') ? esc(session()->getFlashdata('error'), 'js') : '' ?>';

    if (flashSuccess) {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            html: flashSuccess,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true
        });
        updateNotificationBadge();
    }

    if (flashError) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            html: flashError,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true
        });
    }

    $(document).on('click', '.btn-delete-karyawan', function (e) {
        e.preventDefault();
        const form = $(this).closest('form');
        const nama = $(this).data('nama') || 'karyawan ini';

        Swal.fire({
            title: 'Konfirmasi Hapus Data',
            text: `Apakah Anda yakin ingin menghapus data "${nama}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="far fa-trash-alt me-1"></i> Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>

<?= $this->include('direktur/templates/footer') ?>
