<?= view('admin/templates/header') ?>
<?= view('admin/templates/sidebar') ?>
<?= view('admin/templates/navbar') ?>

<style>
    .form-card-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(30, 60, 114, 0.06) !important;
    }

    .form-section-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e3c72;
        display: flex;
        align-items: center;
        gap: 8px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f1f5f9;
        margin-bottom: 20px;
    }

    .form-control-custom, .form-select-custom {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        padding: 10px 14px;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .form-control-custom:focus, .form-select-custom:focus {
        border-color: #1e3c72;
        box-shadow: 0 0 0 3px rgba(30, 60, 114, 0.12);
    }
</style>

<div class="container-fluid py-3 py-md-4">

    <!-- Breadcrumb & Header Bar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center bg-white rounded-4 shadow-sm p-3.5 p-md-4 mb-4 border border-light gap-3">
        <div class="d-flex align-items-center">
            <a href="<?= base_url('admin/surat/masuk') ?>" class="btn btn-light rounded-circle me-3 d-flex align-items-center justify-content-center p-0" style="width: 42px; height: 42px;">
                <i class="fas fa-arrow-left text-dark"></i>
            </a>
            <div>
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb mb-0 small" style="font-size: 0.78rem;">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>" class="text-decoration-none text-muted">Admin Panel</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/surat/masuk') ?>" class="text-decoration-none text-muted">Surat Masuk</a></li>
                        <li class="breadcrumb-item active fw-bold text-primary" aria-current="page">Catat Surat Masuk</li>
                    </ol>
                </nav>
                <h4 class="mb-0 fw-bold text-dark fs-5 fs-md-4">Catat Surat Masuk Baru</h4>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="card form-card-modern p-4 p-md-5 mb-4">
        <form action="<?= base_url('admin/surat/masuk/simpan') ?>" method="POST" enctype="multipart/form-data">
            
            <!-- Section 1: Informasi Dokumen Surat -->
            <div class="form-section-title">
                <i class="fas fa-file-invoice text-primary"></i> 1. Informasi Dokumen & Registrasi Surat
            </div>

            <div class="row g-3 mb-4">
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold text-dark text-xs text-uppercase">Nomor Surat (Otomatis)</label>
                    <div class="input-group">
                        <input type="text" name="no_surat" id="no_surat_input" class="form-control form-control-custom bg-light fw-bold text-primary" value="<?= esc($autoNoSurat) ?>" required readonly>
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3" onclick="toggleEditNoSurat()" title="Ubah Nomor Surat Manual">
                            <i class="fas fa-pen"></i> Ubah
                        </button>
                    </div>
                    <small class="text-muted d-block mt-1" style="font-size:0.75rem;">
                        <i class="fas fa-magic me-1 text-info"></i> Nomor diset otomatis oleh sistem berdasarkan nomor urut bulan ini.
                    </small>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold text-dark text-xs text-uppercase">Tanggal Diterima</label>
                    <input type="date" name="tanggal_diterima" class="form-control form-control-custom" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold text-dark text-xs text-uppercase">Pengirim Surat</label>
                    <input type="text" name="pengirim" class="form-control form-control-custom" placeholder="Nama Instansi / Perusahaan / Pengirim Eksternal (Contoh: PT Pertamina Retail)" required>
                    <small class="text-muted d-block mt-1" style="font-size:0.75rem;">
                        Identitas pengirim surat masuk (Instansi pemerintah, klien, vendor, atau mitra bisnis).
                    </small>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold text-dark text-xs text-uppercase">Perihal / Hal Surat</label>
                    <textarea name="perihal" class="form-control form-control-custom" rows="3" placeholder="Ringkasan perihal atau isi singkat surat..." required></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold text-dark text-xs text-uppercase">Upload Berkas PDF / Foto Surat (Opsional)</label>
                    <input type="file" name="file_surat" class="form-control form-control-custom" accept=".pdf,.png,.jpg,.jpeg">
                    <small class="text-muted d-block mt-1" style="font-size:0.75rem;">
                        Format yang didukung: PDF, PNG, JPG (Maksimal 10MB).
                    </small>
                </div>
            </div>

            <!-- Section 2: Disposisi Surat -->
            <div class="form-section-title mt-4">
                <i class="fas fa-paper-plane text-success"></i> 2. Penerusan & Disposisi Surat (Opsional)
            </div>

            <div class="row g-3 mb-4">
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold text-dark text-xs text-uppercase">Status Disposisi Awal</label>
                    <select name="status" class="form-select form-select-custom fw-semibold">
                        <option value="pending">Pending Disposisi (Belum Diteruskan)</option>
                        <option value="disposisi">Sudah Didisposisikan</option>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold text-dark text-xs text-uppercase">Penerima Disposisi</label>
                    <input type="text" name="penerima_disposisi" class="form-control form-control-custom" placeholder="Contoh: Divisi Operasional & Engineering / Direktur Utama">
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold text-dark text-xs text-uppercase">Catatan / Instruksi Disposisi</label>
                    <textarea name="catatan_disposisi" class="form-control form-control-custom" rows="2" placeholder="Catatan atau instruksi pelaksanaan dari surat..."></textarea>
                </div>
            </div>

            <!-- Action Footer Buttons -->
            <div class="d-flex align-items-center justify-content-end gap-2 pt-4 border-top">
                <a href="<?= base_url('admin/surat/masuk') ?>" class="btn btn-light rounded-pill px-4 py-2.5 fw-semibold">Batal</a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold shadow-sm" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border: none;">
                    <i class="fas fa-save me-2"></i> Simpan Surat Masuk
                </button>
            </div>

        </form>
    </div>

</div>

<script>
    function toggleEditNoSurat() {
        const input = document.getElementById('no_surat_input');
        if (input.hasAttribute('readonly')) {
            input.removeAttribute('readonly');
            input.classList.remove('bg-light');
            input.focus();
        } else {
            input.setAttribute('readonly', 'readonly');
            input.classList.add('bg-light');
        }
    }
</script>

<?= view('admin/templates/footer', $data) ?>
