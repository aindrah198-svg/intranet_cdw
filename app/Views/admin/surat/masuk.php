<?= view('admin/templates/header') ?>
<?= view('admin/templates/sidebar') ?>
<?= view('admin/templates/navbar') ?>

<style>
    /* Styling Premium Modern Material & Glassmorphism (Selaras dengan Direktur Panel) */
    .surat-card-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(30, 60, 114, 0.06), 0 2px 6px rgba(0, 0, 0, 0.02) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .surat-card-modern:hover {
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
    }

    /* Status Pill Frosted Glass */
    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 5px 14px;
        border-radius: 30px;
        font-size: 0.76rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    
    .status-pill-disposisi {
        background: rgba(25, 135, 84, 0.12);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.25);
    }
    
    .status-pill-pending {
        background: rgba(255, 193, 7, 0.15);
        color: #b58100;
        border: 1px solid rgba(255, 193, 7, 0.3);
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
        font-size: 0.9rem;
        font-weight: 600;
        color: #0f172a;
    }

    /* Modern Action Pills */
    .btn-action-pill {
        border-radius: 20px;
        padding: 7px 16px;
        font-size: 0.82rem;
        font-weight: 600;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid transparent;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-action-view {
        background: rgba(30, 60, 114, 0.08);
        color: #1e3c72;
        border-color: rgba(30, 60, 114, 0.2);
    }

    .btn-action-view:hover {
        background: #1e3c72;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(30, 60, 114, 0.3);
    }

    .btn-action-edit {
        background: rgba(255, 193, 7, 0.12);
        color: #b58100;
        border-color: rgba(255, 193, 7, 0.3);
    }

    .btn-action-edit:hover {
        background: #ffc107;
        color: #000000;
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
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
        color: #334155;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 8px;
        letter-spacing: 0.5px;
    }
</style>

<div class="container-fluid py-3 py-md-4">

    <!-- Flash Message Notification -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- 1. Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center bg-white rounded-4 shadow-sm p-3.5 p-md-4 mb-4 border border-light gap-3">
        <div class="d-flex align-items-center">
            <div class="text-white rounded-3 p-2.5 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                <i class="fas fa-inbox fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark fs-5 fs-md-4">Daftar Surat Masuk</h4>
                <small class="text-muted d-block mt-0.5">Pencatatan, pengarsipan berkas, disposisi, dan pelacakan surat masuk CDW Engineering.</small>
            </div>
        </div>
        <a href="<?= base_url('admin/surat/masuk/tambah') ?>" class="btn btn-primary rounded-pill px-4 py-2.5 shadow-sm d-inline-flex align-items-center justify-content-center text-sm fw-semibold" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border: none;">
            <i class="fas fa-plus me-2"></i> <span>Catat Surat Masuk</span>
        </a>
    </div>

    <!-- 2. Search & Filter Bar -->
    <div class="row g-2 mb-4">
        <div class="col-12 col-md-8 col-lg-9">
            <div class="input-group shadow-sm rounded-pill overflow-hidden border border-light bg-white">
                <span class="input-group-text bg-white border-end-0 text-muted ps-3.5">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" id="searchSuratMasuk" class="form-control border-start-0 py-2.5" placeholder="Cari nomor surat, pengirim, perihal, atau status disposisi..." onkeyup="filterSuratCards()">
            </div>
        </div>
        <div class="col-12 col-md-4 col-lg-3">
            <select id="filterStatusSurat" class="form-select shadow-sm rounded-pill py-2.5 border-light text-secondary fw-semibold" onchange="filterSuratCards()">
                <option value="">Semua Status Disposisi</option>
                <option value="disposisi">Sudah Didisposisi</option>
                <option value="pending">Pending Disposisi</option>
            </select>
        </div>
    </div>

    <!-- 3. Dynamic Card Grid Layout -->
    <div class="row g-3" id="containerSuratMasuk">
        <?php if (!empty($suratList)): ?>
            <?php foreach ($suratList as $s): ?>
                <?php
                    $isDisposisi = (strtolower($s['status']) === 'disposisi');
                    $statusClass = $isDisposisi ? 'status-pill-disposisi' : 'status-pill-pending';
                    $statusLabel = $isDisposisi ? 'Sudah Didisposisi' : 'Pending Disposisi';
                    $statusIcon = $isDisposisi ? 'fas fa-check-circle' : 'fas fa-clock';
                    $initial = strtoupper(substr($s['pengirim'], 0, 1));
                    $searchData = strtolower($s['no_surat'] . ' ' . $s['pengirim'] . ' ' . $s['perihal'] . ' ' . $statusLabel);
                    $statusData = $isDisposisi ? 'disposisi' : 'pending';
                ?>
                <div class="col-12 col-xl-6 surat-item" data-search="<?= esc($searchData) ?>" data-status="<?= $statusData ?>">
                    <div class="card surat-card-modern p-3.5 p-md-4">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom border-light">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-glow text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="<?= !$isDisposisi ? 'background: linear-gradient(135deg, #e65100 0%, #ef6c00 100%);' : '' ?>">
                                    <?= $initial ?>
                                </div>
                                <div>
                                    <span class="id-tag mb-1 d-inline-block"><i class="fas fa-hashtag me-1"></i><?= esc($s['no_surat']) ?></span>
                                    <h5 class="fw-bold text-dark mb-0 fs-6"><?= esc($s['pengirim']) ?></h5>
                                </div>
                            </div>
                            <span class="status-pill <?= $statusClass ?>">
                                <i class="<?= $statusIcon ?> me-1.5"></i> <?= $statusLabel ?>
                            </span>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-12 col-md-7">
                                <div class="data-pill-bar">
                                    <div class="data-label">
                                        <i class="fas fa-file-alt text-primary"></i> Perihal Surat
                                    </div>
                                    <div class="data-value text-truncate" title="<?= esc($s['perihal']) ?>">
                                        <?= esc($s['perihal']) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-5">
                                <div class="data-pill-bar">
                                    <div class="data-label">
                                        <i class="fas fa-calendar-alt text-info"></i> Tanggal Diterima
                                    </div>
                                    <div class="data-value">
                                        <?= date('d M Y', strtotime($s['tanggal_diterima'])) ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap align-items-center justify-content-end gap-2 pt-2 border-top border-light">
                            <a href="<?= base_url('admin/surat/masuk/detail/' . $s['id']) ?>" class="btn-action-pill btn-action-view">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                            <a href="<?= base_url('admin/surat/masuk/edit/' . $s['id']) ?>" class="btn-action-pill btn-action-edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <button type="button" class="btn-action-pill btn-action-delete" onclick="confirmHapusSurat(<?= $s['id'] ?>, '<?= esc($s['no_surat'], 'js') ?>')">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Empty State -->
    <div id="noResults" class="text-center py-5 <?= !empty($suratList) ? 'd-none' : '' ?>">
        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
            <i class="fas fa-inbox fs-2 text-muted"></i>
        </div>
        <h5 class="fw-bold text-dark">Data Surat Masuk Tidak Ditemukan</h5>
        <p class="text-muted small">Belum ada catatan surat masuk atau coba sesuaikan kata kunci pencarian Anda.</p>
        <a href="<?= base_url('admin/surat/masuk/tambah') ?>" class="btn btn-primary rounded-pill px-4 py-2 mt-2 fw-semibold" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border: none;">
            <i class="fas fa-plus me-1.5"></i> Catat Surat Masuk Baru
        </a>
    </div>

</div>

<script>
    function filterSuratCards() {
        const searchInput = document.getElementById('searchSuratMasuk').value.toLowerCase().trim();
        const statusFilter = document.getElementById('filterStatusSurat').value.toLowerCase();
        const items = document.querySelectorAll('.surat-item');
        let visibleCount = 0;

        items.forEach(item => {
            const textData = item.getAttribute('data-search').toLowerCase();
            const statusData = item.getAttribute('data-status').toLowerCase();

            const matchesSearch = searchInput === '' || textData.includes(searchInput);
            const matchesStatus = statusFilter === '' || statusFilter === statusData;

            if (matchesSearch && matchesStatus) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        const noResults = document.getElementById('noResults');
        if (noResults) {
            if (visibleCount === 0) {
                noResults.classList.remove('d-none');
            } else {
                noResults.classList.add('d-none');
            }
        }
    }

    function confirmHapusSurat(id, noSurat) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Hapus Surat Masuk?',
                text: "Surat " + noSurat + " akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash me-1"></i> Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'rounded-pill px-4',
                    cancelButton: 'rounded-pill px-4'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "<?= base_url('admin/surat/masuk/hapus/') ?>" + id;
                }
            });
        } else {
            if (confirm('Apakah Anda yakin ingin menghapus surat ' + noSurat + '?')) {
                window.location.href = "<?= base_url('admin/surat/masuk/hapus/') ?>" + id;
            }
        }
    }
</script>

<?= view('admin/templates/footer', $data) ?>
