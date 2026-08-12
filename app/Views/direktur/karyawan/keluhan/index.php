<?= $this->include('direktur/templates/header') ?>
<?= $this->include('direktur/templates/sidebar') ?>
<?= $this->include('direktur/templates/navbar') ?>

<style>
    /* Styling Premium Modern Material & Glassmorphism (Sama Persis dengan Halaman Kelola Karyawan) */
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
        width: 48px;
        height: 48px;
        font-size: 1.2rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-weight: 700;
        flex-shrink: 0;
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
    
    .status-pill-baru {
        background: rgba(255, 193, 7, 0.15);
        color: #b58100;
        border: 1px solid rgba(255, 193, 7, 0.3);
    }
    
    .status-pill-diproses {
        background: rgba(13, 202, 240, 0.12);
        color: #0dcaf0;
        border: 1px solid rgba(13, 202, 240, 0.25);
    }

    .status-pill-selesai {
        background: rgba(25, 135, 84, 0.12);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.25);
    }

    .status-pill-ditolak {
        background: rgba(220, 53, 69, 0.12);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.25);
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

    /* Pagination Styling */
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

    <!-- 1. Header Section Terpadu -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light">
        <div class="d-flex align-items-center">
            <div class="bg-gradient-primary text-white rounded-3 p-2.5 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px;">
                <i class="fas fa-comment-dots fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark">Keluhan Karyawan</h4>
                <small class="text-muted d-none d-sm-inline">Histori dan tindak lanjut keluhan dari karyawan secara real-time.</small>
            </div>
        </div>
        <a href="<?= base_url('direktur/karyawan/keluhan/tambah') ?>" class="btn btn-primary rounded-pill px-3.5 py-2 shadow-sm d-inline-flex align-items-center text-sm fw-semibold">
            <i class="fas fa-plus me-1.5"></i> <span class="d-none d-md-inline">Tambah Keluhan</span><span class="d-inline d-md-none">Tambah</span>
        </a>
    </div>

    <!-- Alert Flash Data -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show text-white shadow-sm border-0 mb-4" role="alert" style="border-radius: 12px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show text-white shadow-sm border-0 mb-4" role="alert" style="border-radius: 12px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
            <i class="fas fa-exclamation-triangle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- 2. Search & Filter Bar (Pencarian Instant + Filter Modal) -->
    <div class="input-group mb-3 shadow-sm rounded-pill overflow-hidden border border-light">
        <span class="input-group-text bg-white border-end-0 text-muted ps-3.5">
            <i class="fas fa-search"></i>
        </span>
        <input type="text" id="searchKeluhan" class="form-control border-start-0 border-end-0 ps-1 py-2.5" placeholder="Cari nama karyawan, NIK, divisi, kategori, atau judul keluhan...">
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
                        <i class="fas fa-filter text-primary me-2"></i> Filter Keluhan Karyawan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Filter Status Keluhan -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-sm">Status Keluhan</label>
                            <select id="filterStatus" class="form-select rounded-3">
                                <option value="">Semua Status</option>
                                <option value="baru">Belum Tanggap (Baru)</option>
                                <option value="diproses">Sedang Diproses</option>
                                <option value="selesai">Selesai</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                        </div>
                        <!-- Filter Kategori -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-sm">Kategori</label>
                            <select id="filterKategori" class="form-select rounded-3">
                                <option value="">Semua Kategori</option>
                                <?php foreach ($kategoriList as $kat): ?>
                                    <option value="<?= esc($kat) ?>"><?= esc($kat) ?></option>
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

    <!-- 3. Daftar Kartu Keluhan Karyawan (Card List Grid persis dengan Halaman Karyawan) -->
    <div class="row g-3" id="keluhanCardContainer">
        <?php if (empty($keluhanList)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center rounded-4 shadow-sm p-4 my-2">
                    <i class="fas fa-folder-open fa-2x mb-2 text-primary"></i>
                    <h5 class="fw-bold mb-1">Belum Ada Keluhan Tercatat</h5>
                    <p class="mb-0 text-muted small">Keluhan yang disampaikan karyawan akan tampil di halaman ini.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($keluhanList as $k): ?>
                <?php
                    $status = $k['status'] ?? 'baru';
                    $statusPillClass = 'status-pill-baru';
                    $statusIcon = 'fas fa-exclamation-circle';

                    if ($status === 'diproses') {
                        $statusPillClass = 'status-pill-diproses';
                        $statusIcon = 'fas fa-spinner';
                    } elseif ($status === 'selesai') {
                        $statusPillClass = 'status-pill-selesai';
                        $statusIcon = 'fas fa-check-circle';
                    } elseif ($status === 'ditolak') {
                        $statusPillClass = 'status-pill-ditolak';
                        $statusIcon = 'fas fa-times-circle';
                    }

                    $statusLabel = match($status) {
                        'baru'     => 'Belum Tanggap',
                        'diproses' => 'Sedang Diproses',
                        'selesai'  => 'Selesai',
                        'ditolak'  => 'Ditolak',
                        default    => ucfirst($status),
                    };

                    $initial = !empty($k['nama_lengkap']) ? strtoupper(substr($k['nama_lengkap'], 0, 1)) : 'K';
                    $keluhanIdTag = 'KLH' . str_pad($k['id'], 3, '0', STR_PAD_LEFT);
                ?>
                <div class="col-12 keluhan-card-wrapper" data-status="<?= esc($status) ?>" data-kategori="<?= esc($k['kategori']) ?>" data-search="<?= esc(strtolower($k['nama_lengkap'].' '.$k['nik'].' '.$k['divisi'].' '.$k['judul'].' '.$k['kategori'])) ?>">
                    <div class="card employee-card-modern keluhan-card p-3 p-sm-4">
                        
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
                                            <?= esc($k['nama_lengkap']) ?>
                                        </h3>
                                        <!-- Tag ID Keluhan -->
                                        <span class="id-tag">ID: <?= $keluhanIdTag ?></span>
                                    </div>
                                    <!-- Lencana Status Chip Frosted Glass -->
                                    <div class="mt-1.5">
                                        <span class="status-pill <?= $statusPillClass ?>">
                                            <i class="<?= $statusIcon ?> me-1"></i> <?= $statusLabel ?>
                                        </span>
                                        <span class="badge bg-light text-dark border ms-1 font-weight-normal" style="border-radius: 6px; font-size: 0.72rem;">
                                            <?= esc($k['kategori']) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Penataan Bidang Data (Bilah Data Horizontal Grid) -->
                        <div class="py-3">
                            <div class="row g-2.5">
                                <!-- NIK & Divisi -->
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="data-pill-bar">
                                        <div class="data-label">
                                            <i class="far fa-id-card text-primary"></i> NIK & Divisi
                                        </div>
                                        <div class="data-value text-break">
                                            <?= esc($k['nik'] ?: '-') ?> <span class="text-muted">|</span> <?= esc($k['divisi'] ?: 'Staf') ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Judul Keluhan -->
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="data-pill-bar">
                                        <div class="data-label">
                                            <i class="fas fa-comment-alt text-primary"></i> Pokok Keluhan
                                        </div>
                                        <div class="data-value text-truncate">
                                            <?= esc($k['judul']) ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Deskripsi Singkat -->
                                <div class="col-12 col-sm-6 col-md-3">
                                    <div class="data-pill-bar">
                                        <div class="data-label">
                                            <i class="fas fa-align-left text-primary"></i> Rincian Singkat
                                        </div>
                                        <div class="data-value text-truncate">
                                            <?= esc(mb_strimwidth($k['deskripsi'], 0, 45, '...')) ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tanggal Lapor -->
                                <div class="col-12 col-sm-6 col-md-2">
                                    <div class="data-pill-bar">
                                        <div class="data-label">
                                            <i class="far fa-calendar-alt text-primary"></i> Tgl Lapor
                                        </div>
                                        <div class="data-value">
                                            <?= date('d M Y', strtotime($k['tanggal'])) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi Terintegrasi (Modern Action Pills) -->
                        <div class="pt-2 border-top border-light d-flex justify-content-end align-items-center gap-2 flex-wrap">
                            <a href="<?= base_url('direktur/karyawan/keluhan/detail/'.$k['id']) ?>" class="btn-action-pill btn-action-view" title="Tanggapi Keluhan">
                                <i class="fas fa-reply"></i> Tanggapi
                            </a>
                            <button type="button" class="btn-action-pill btn-action-edit" onclick="showQuickModal(<?= htmlspecialchars(json_encode($k), ENT_QUOTES, 'UTF-8') ?>)" title="Pratinjau Detail">
                                <i class="far fa-eye"></i> Detail
                            </button>
                            <form action="<?= base_url('direktur/karyawan/keluhan/delete/'.$k['id']) ?>" method="post" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-action-pill btn-action-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus keluhan ini?')" title="Hapus Keluhan">
                                    <i class="far fa-trash-alt"></i> Hapus
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Pesan Jika Data Tidak Ditemukan -->
    <div id="noResultsMessage" class="alert alert-info text-center rounded-4 shadow-sm p-4 d-none my-3">
        <i class="fas fa-search fa-2x mb-2 text-primary"></i>
        <h5 class="fw-bold mb-1">Data Keluhan Tidak Ditemukan</h5>
        <p class="mb-0 text-muted small">Coba ubah kata kunci pencarian atau reset filter Anda.</p>
    </div>

    <!-- 4. Control Bar Bawah: Pengaturan Tampilkan Per Halaman + Pagination -->
    <div class="card shadow-sm rounded-4 border-0 p-3 mt-4 mb-5 bg-white">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            
            <!-- Pengaturan Jumlah Tampilkan Data -->
            <div class="d-flex align-items-center gap-2">
                <span class="text-xs text-muted fw-bold text-uppercase">Tampilkan:</span>
                <select id="perPageSelect" class="form-select form-select-sm rounded-pill shadow-xs fw-semibold text-dark border-light px-3 py-1.5" style="width: auto; cursor: pointer;">
                    <option value="5" selected>5 Keluhan / hal</option>
                    <option value="10">10 Keluhan / hal</option>
                    <option value="25">25 Keluhan / hal</option>
                    <option value="50">50 Keluhan / hal</option>
                    <option value="ALL">Semua Keluhan</option>
                </select>
            </div>

            <!-- Informasi Jumlah Data -->
            <div class="text-xs text-muted fw-semibold" id="paginationInfo">
                Menampilkan <span id="showingStart">1</span> - <span id="showingEnd">5</span> dari <span id="totalItems">0</span> Keluhan
            </div>

            <!-- Tombol Navigasi Halaman -->
            <ul class="pagination pagination-modern mb-0" id="paginationList"></ul>

        </div>
    </div>

</div>

<!-- Modal Quick View (Pratinjau Detail) -->
<div class="modal fade" id="quickModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-header border-0 bg-gradient-primary text-white p-3 p-md-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar-glow bg-white text-primary" style="width: 42px; height: 42px;">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div>
                        <h5 class="modal-title font-weight-bold text-white mb-0" id="qmJudul">Pratinjau Keluhan</h5>
                        <p class="text-xs text-white-50 mb-0" id="qmKategori">Kategori</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 p-md-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <span class="text-xs text-secondary font-weight-bold text-uppercase d-block mb-1">Pengirim Keluhan</span>
                            <h6 class="text-sm font-weight-bold text-dark mb-0" id="qmNama">-</h6>
                            <span class="text-xs text-secondary d-block mt-1" id="qmDivisi">-</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <span class="text-xs text-secondary font-weight-bold text-uppercase d-block mb-1">Status & Tanggal</span>
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-1">
                                <span class="text-sm font-weight-bold text-dark" id="qmTanggal">-</span>
                                <span id="qmStatusBadge"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="text-xs font-weight-bold text-uppercase text-secondary mb-2"><i class="fas fa-align-left me-1"></i> Isi Keluhan Lengkap</h6>
                    <div class="p-3 rounded-3" style="background: #ffffff; border: 1px solid #e2e8f0; line-height: 1.7; white-space: pre-line;" id="qmDeskripsi">
                        -
                    </div>
                </div>

                <div id="qmTanggapanSection" class="d-none">
                    <h6 class="text-xs font-weight-bold text-uppercase text-success mb-2"><i class="fas fa-check-circle me-1"></i> Tanggapan Direktur</h6>
                    <div class="p-3 rounded-3" style="background: #f0fdf4; border: 1px solid #bbf7d0; line-height: 1.7; white-space: pre-line;" id="qmTanggapan">
                        -
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-3 p-md-4 pt-0 d-flex flex-column flex-sm-row justify-content-end gap-2">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold border w-100 w-sm-auto mb-0" data-bs-dismiss="modal">Tutup</button>
                <a href="#" id="qmBtnDetail" class="btn btn-primary rounded-pill px-4 fw-semibold w-100 w-sm-auto mb-0">
                    <i class="fas fa-reply me-1"></i> Form Tanggapan Lengkap
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput         = document.getElementById('searchKeluhan');
    const filterStatus        = document.getElementById('filterStatus');
    const filterKategori      = document.getElementById('filterKategori');
    const btnApplyFilter      = document.getElementById('btnApplyFilter');
    const btnResetFilter      = document.getElementById('btnResetFilter');
    const activeFilterTags    = document.getElementById('activeFilterTags');
    const filterTagContainer  = document.getElementById('filterTagContainer');
    const btnClearActiveFilter= document.getElementById('btnClearActiveFilter');

    const cardWrappers        = Array.from(document.querySelectorAll('.keluhan-card-wrapper'));
    const noResultsMsg        = document.getElementById('noResultsMessage');

    const perPageSelect       = document.getElementById('perPageSelect');
    const showingStart        = document.getElementById('showingStart');
    const showingEnd          = document.getElementById('showingEnd');
    const totalItems          = document.getElementById('totalItems');
    const paginationList      = document.getElementById('paginationList');

    let currentPage           = 1;
    let itemsPerPage          = parseInt(perPageSelect.value) || 5;
    let filteredWrappers      = [...cardWrappers];

    function filterAndPaginate() {
        const query       = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const statusVal   = filterStatus ? filterStatus.value : '';
        const kategoriVal = filterKategori ? filterKategori.value : '';

        // Update tag filter aktif
        let tagsHtml = '';
        if (statusVal) {
            tagsHtml += `<span class="active-filter-badge">Status: ${statusVal}</span>`;
        }
        if (kategoriVal) {
            tagsHtml += `<span class="active-filter-badge">Kategori: ${kategoriVal}</span>`;
        }

        if (tagsHtml) {
            filterTagContainer.innerHTML = tagsHtml;
            activeFilterTags.classList.remove('d-none');
        } else {
            activeFilterTags.classList.add('d-none');
        }

        // Filtering
        filteredWrappers = cardWrappers.filter(wrapper => {
            const cardSearch   = wrapper.getAttribute('data-search') || '';
            const cardStatus   = wrapper.getAttribute('data-status') || '';
            const cardKategori = wrapper.getAttribute('data-kategori') || '';

            const matchQuery    = !query || cardSearch.includes(query);
            const matchStatus   = !statusVal || cardStatus === statusVal;
            const matchKategori = !kategoriVal || cardKategori === kategoriVal;

            return matchQuery && matchStatus && matchKategori;
        });

        // Hide all first
        cardWrappers.forEach(w => w.style.display = 'none');

        const totalCount = filteredWrappers.length;
        totalItems.innerText = totalCount;

        if (totalCount === 0) {
            noResultsMsg.classList.remove('d-none');
            showingStart.innerText = 0;
            showingEnd.innerText   = 0;
            paginationList.innerHTML = '';
            return;
        } else {
            noResultsMsg.classList.add('d-none');
        }

        // Calculate Pagination
        let totalPages = 1;
        if (itemsPerPage === 'ALL' || itemsPerPage >= totalCount) {
            itemsPerPage = totalCount;
            totalPages = 1;
            currentPage = 1;
        } else {
            totalPages = Math.ceil(totalCount / itemsPerPage);
            if (currentPage > totalPages) currentPage = totalPages;
        }

        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex   = Math.min(startIndex + itemsPerPage, totalCount);

        showingStart.innerText = startIndex + 1;
        showingEnd.innerText   = endIndex;

        // Display current page items
        for (let i = startIndex; i < endIndex; i++) {
            filteredWrappers[i].style.display = '';
        }

        renderPaginationControls(totalPages);
    }

    function renderPaginationControls(totalPages) {
        if (totalPages <= 1) {
            paginationList.innerHTML = '';
            return;
        }

        let html = '';
        html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${currentPage - 1}"><i class="fas fa-chevron-left"></i></a>
                 </li>`;

        for (let p = 1; p <= totalPages; p++) {
            html += `<li class="page-item ${p === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${p}">${p}</a>
                     </li>`;
        }

        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${currentPage + 1}"><i class="fas fa-chevron-right"></i></a>
                 </li>`;

        paginationList.innerHTML = html;

        paginationList.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const targetPage = parseInt(this.getAttribute('data-page'));
                if (targetPage && targetPage !== currentPage && targetPage >= 1 && targetPage <= totalPages) {
                    currentPage = targetPage;
                    filterAndPaginate();
                }
            });
        });
    }

    // Event Listeners
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            currentPage = 1;
            filterAndPaginate();
        });
    }

    if (btnApplyFilter) {
        btnApplyFilter.addEventListener('click', function () {
            currentPage = 1;
            filterAndPaginate();
        });
    }

    if (btnResetFilter) {
        btnResetFilter.addEventListener('click', function () {
            filterStatus.value = '';
            filterKategori.value = '';
            currentPage = 1;
            filterAndPaginate();
        });
    }

    if (btnClearActiveFilter) {
        btnClearActiveFilter.addEventListener('click', function () {
            filterStatus.value = '';
            filterKategori.value = '';
            currentPage = 1;
            filterAndPaginate();
        });
    }

    if (perPageSelect) {
        perPageSelect.addEventListener('change', function () {
            itemsPerPage = this.value === 'ALL' ? 'ALL' : parseInt(this.value);
            currentPage = 1;
            filterAndPaginate();
        });
    }

    // Initial Run
    filterAndPaginate();
});

// Quick View Modal
function showQuickModal(data) {
    document.getElementById('qmJudul').innerText = data.judul || 'Detail Keluhan';
    document.getElementById('qmKategori').innerText = 'Kategori: ' + (data.kategori || '-');
    document.getElementById('qmNama').innerText = data.nama_lengkap || '-';
    document.getElementById('qmDivisi').innerText = (data.divisi || 'Staf') + ' • NIK: ' + (data.nik || '-');
    document.getElementById('qmTanggal').innerText = data.tanggal ? new Date(data.tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-';
    document.getElementById('qmDeskripsi').innerText = data.deskripsi || '-';
    document.getElementById('qmBtnDetail').href = '<?= base_url('direktur/karyawan/keluhan/detail/') ?>' + data.id;

    const tanggapanSec = document.getElementById('qmTanggapanSection');
    if (data.tanggapan && data.tanggapan.trim() !== '') {
        tanggapanSec.classList.remove('d-none');
        document.getElementById('qmTanggapan').innerText = data.tanggapan;
    } else {
        tanggapanSec.classList.add('d-none');
    }

    const badgeElem = document.getElementById('qmStatusBadge');
    let statusHtml = '';
    if (data.status === 'baru') {
        statusHtml = '<span class="status-pill status-pill-baru">Belum Tanggap</span>';
    } else if (data.status === 'diproses') {
        statusHtml = '<span class="status-pill status-pill-diproses">Sedang Diproses</span>';
    } else if (data.status === 'selesai') {
        statusHtml = '<span class="status-pill status-pill-selesai">Selesai</span>';
    } else if (data.status === 'ditolak') {
        statusHtml = '<span class="status-pill status-pill-ditolak">Ditolak</span>';
    }
    badgeElem.innerHTML = statusHtml;

    const myModal = new bootstrap.Modal(document.getElementById('quickModal'));
    myModal.show();
}
</script>

<?= $this->include('direktur/templates/footer') ?>
