<?= $this->include('direktur/templates/header') ?>
<?= $this->include('direktur/templates/sidebar') ?>
<?= $this->include('direktur/templates/navbar') ?>

<style>
    /* Styling Premium Modern Material & Glassmorphism */
    .employee-card-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(30, 60, 114, 0.06), 0 2px 6px rgba(0, 0, 0, 0.02) !important;
    }

    .form-section-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e3c72;
        display: flex;
        align-items: center;
        gap: 8px;
        padding-bottom: 8px;
        border-bottom: 2px solid #f1f5f9;
        margin-bottom: 20px;
    }

    .form-control-custom, .form-select-custom {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
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

    <!-- Header Section Terpadu (Selaras dengan Halaman Kelola Karyawan) -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light">
        <div class="d-flex align-items-center">
            <div class="bg-gradient-primary text-white rounded-3 p-2.5 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px;">
                <i class="fas fa-user-edit fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark">Edit Data Karyawan</h4>
                <small class="text-muted d-none d-sm-inline">Perbarui informasi profil dan penempatan kerja untuk <strong><?= esc($karyawan['nama_lengkap']) ?></strong></small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('direktur/karyawan/detail/'.$karyawan['id']) ?>" class="btn btn-outline-info rounded-pill px-3.5 py-2 shadow-sm d-inline-flex align-items-center text-sm fw-semibold">
                <i class="far fa-eye me-1.5"></i> <span class="d-none d-md-inline">Lihat Detail</span>
            </a>
            <a href="<?= base_url('direktur/karyawan') ?>" class="btn btn-outline-secondary rounded-pill px-3.5 py-2 shadow-sm d-inline-flex align-items-center text-sm fw-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> <span class="d-none d-md-inline">Kembali</span>
            </a>
        </div>
    </div>

    <!-- Form Edit Card -->
    <div class="row">
        <div class="col-12">
            <div class="card employee-card-modern p-3 p-sm-4 mb-5">
                <div class="card-body p-2 p-md-3">

                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger rounded-3 shadow-sm border-0 mb-4 text-white">
                            <i class="fas fa-exclamation-triangle me-2"></i><strong>Terjadi kesalahan input:</strong>
                            <ul class="mb-0 mt-2 ps-3">
                            <?php foreach (session()->getFlashdata('errors') as $err): ?>
                                <li><?= esc($err) ?></li>
                            <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('direktur/karyawan/update/'.$karyawan['id']) ?>" method="post">
                        <?= csrf_field() ?>

                        <!-- Section 1: Data Pribadi -->
                        <div class="form-section-title">
                            <i class="fas fa-user-id-card text-primary"></i> Data Pribadi Karyawan
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">NIK (Nomor Induk Karyawan) <span class="text-danger">*</span></label>
                                <input type="text" name="nik" class="form-control form-control-custom" value="<?= old('nik', $karyawan['nik']) ?>" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama_lengkap" class="form-control form-control-custom" value="<?= old('nama_lengkap', $karyawan['nama_lengkap']) ?>" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control form-control-custom" value="<?= old('tempat_lahir', $karyawan['tempat_lahir']) ?>">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control form-control-custom" value="<?= old('tanggal_lahir', $karyawan['tanggal_lahir']) ?>">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select form-select-custom">
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="L" <?= old('jenis_kelamin', $karyawan['jenis_kelamin']) == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="P" <?= old('jenis_kelamin', $karyawan['jenis_kelamin']) == 'P' ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">No. Telepon / WhatsApp</label>
                                <input type="text" name="telepon" class="form-control form-control-custom" value="<?= old('telepon', $karyawan['telepon']) ?>">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">Email Pribadi / Kerja</label>
                                <input type="email" name="email" class="form-control form-control-custom" value="<?= old('email', $karyawan['email']) ?>">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">No. KTP / NIK Kependudukan</label>
                                <input type="text" name="no_ktp" class="form-control form-control-custom" value="<?= old('no_ktp', $karyawan['no_ktp'] ?? '') ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-sm">Alamat Lengkap</label>
                                <textarea name="alamat" class="form-control form-control-custom" rows="3"><?= old('alamat', $karyawan['alamat']) ?></textarea>
                            </div>
                        </div>

                        <!-- Section 2: Data Pekerjaan -->
                        <div class="form-section-title mt-4">
                            <i class="fas fa-briefcase text-primary"></i> Penempatan & Data Pekerjaan
                        </div>

                        <div class="row g-3 mb-4">
                            <!-- Dropdown Divisi Konsisten -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">Divisi <span class="text-danger">*</span></label>
                                <select id="selectDivisi" name="divisi" class="form-select form-select-custom" required>
                                    <option value="Teknisi" <?= old('divisi', $karyawan['divisi']) == 'Teknisi' ? 'selected' : '' ?>>Teknisi</option>
                                    <option value="Engineering" <?= old('divisi', $karyawan['divisi']) == 'Engineering' ? 'selected' : '' ?>>Engineering</option>
                                    <option value="Keuangan" <?= old('divisi', $karyawan['divisi']) == 'Keuangan' ? 'selected' : '' ?>>Keuangan</option>
                                    <option value="Marketing" <?= old('divisi', $karyawan['divisi']) == 'Marketing' ? 'selected' : '' ?>>Marketing</option>
                                    <option value="HRD" <?= old('divisi', $karyawan['divisi']) == 'HRD' ? 'selected' : '' ?>>HRD</option>
                                    <option value="Admin" <?= old('divisi', $karyawan['divisi']) == 'Admin' ? 'selected' : '' ?>>Admin</option>
                                    <option value="Operasional" <?= old('divisi', $karyawan['divisi']) == 'Operasional' ? 'selected' : '' ?>>Operasional</option>
                                    <option value="ADD_NEW_DIVISI">+= Tambah Divisi Baru</option>
                                </select>
                                <div id="divisiCustomBox" class="mt-2 d-none">
                                    <input type="text" id="inputDivisiCustom" class="form-control form-control-custom" placeholder="Ketik nama divisi baru...">
                                </div>
                            </div>

                            <!-- Dropdown / Input Jabatan Otomatis -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">Jabatan <span class="text-danger">*</span></label>
                                <div id="jabatanSelectBox">
                                    <select id="selectJabatan" name="jabatan" class="form-select form-select-custom" required>
                                        <option value="">-- Pilih Divisi Terlebih Dahulu --</option>
                                    </select>
                                </div>
                                <div id="jabatanCustomBox" class="mt-2 d-none">
                                    <input type="text" id="inputJabatanCustom" class="form-control form-control-custom" placeholder="Ketik nama jabatan baru (wajib diisi)...">
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">Status Karyawan <span class="text-danger">*</span></label>
                                <select name="status_karyawan" class="form-select form-select-custom" required>
                                    <option value="">-- Pilih Status Karyawan --</option>
                                    <option value="Tetap" <?= old('status_karyawan', $karyawan['status_karyawan']) == 'Tetap' ? 'selected' : '' ?>>Tetap</option>
                                    <option value="Kontrak" <?= old('status_karyawan', $karyawan['status_karyawan']) == 'Kontrak' ? 'selected' : '' ?>>Kontrak (PKWT)</option>
                                    <option value="Probation" <?= old('status_karyawan', $karyawan['status_karyawan']) == 'Probation' ? 'selected' : '' ?>>Probation (Percobaan)</option>
                                    <option value="Magang" <?= old('status_karyawan', $karyawan['status_karyawan']) == 'Magang' ? 'selected' : '' ?>>Magang / Internship</option>
                                    <option value="Staff" <?= old('status_karyawan', $karyawan['status_karyawan']) == 'Staff' ? 'selected' : '' ?>>Staff</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">Tanggal Masuk (Join Date)</label>
                                <input type="date" name="tanggal_masuk" class="form-control form-control-custom" value="<?= old('tanggal_masuk', $karyawan['tanggal_masuk']) ?>">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">Nomor NPWP (Opsional)</label>
                                <input type="text" name="no_npwp" class="form-control form-control-custom" value="<?= old('no_npwp', $karyawan['no_npwp']) ?>">
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-3 border-top border-light d-flex justify-content-end align-items-center gap-2">
                            <a href="<?= base_url('direktur/karyawan') ?>" class="btn btn-light rounded-pill px-4 py-2 text-sm fw-semibold border">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-info text-white rounded-pill px-4 py-2 text-sm fw-semibold shadow-sm">
                                <i class="fas fa-save me-1.5"></i> Update Data Karyawan
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectDivisi       = document.getElementById('selectDivisi');
    const inputDivisiCustom  = document.getElementById('inputDivisiCustom');
    const divisiCustomBox    = document.getElementById('divisiCustomBox');

    const selectJabatan      = document.getElementById('selectJabatan');
    const inputJabatanCustom = document.getElementById('inputJabatanCustom');
    const jabatanCustomBox   = document.getElementById('jabatanCustomBox');
    const jabatanSelectBox   = document.getElementById('jabatanSelectBox');

    const jabatanMapping = {
        'Teknisi': ['Staff Teknisi', 'Teknisi Field', 'Senior Teknisi', 'Leader Teknisi'],
        'Engineering': ['Software Engineer', 'Hardware Engineer', 'Project Engineer', 'Lead Engineer'],
        'Keuangan': ['Staff Keuangan', 'Accountant', 'Kasir / Finance', 'Finance Manager'],
        'Marketing': ['Marketing Officer', 'Sales Executive', 'Digital Marketing', 'Marketing Manager'],
        'HRD': ['Staff HRD', 'HR Specialist', 'General Affair (GA)', 'HRD Manager'],
        'Admin': ['Staff Admin', 'Admin Operasional', 'Sekretaris', 'Head Admin'],
        'Operasional': ['Staff Operasional', 'Supervisor Operasional', 'Manajer Operasional']
    };

    const initialDivisi  = "<?= esc(old('divisi', $karyawan['divisi'])) ?>";
    const initialJabatan = "<?= esc(old('jabatan', $karyawan['jabatan'])) ?>";

    function updateJabatanOptions() {
        const valDivisi = selectDivisi.value;

        if (valDivisi === 'ADD_NEW_DIVISI') {
            divisiCustomBox.classList.remove('d-none');
            inputDivisiCustom.required = true;
            selectDivisi.removeAttribute('name');
            inputDivisiCustom.setAttribute('name', 'divisi');

            jabatanSelectBox.classList.add('d-none');
            jabatanCustomBox.classList.remove('d-none');
            selectJabatan.required = false;
            selectJabatan.removeAttribute('name');
            inputJabatanCustom.required = true;
            inputJabatanCustom.setAttribute('name', 'jabatan');
        } else {
            divisiCustomBox.classList.add('d-none');
            inputDivisiCustom.required = false;
            inputDivisiCustom.removeAttribute('name');
            selectDivisi.setAttribute('name', 'divisi');

            if (valDivisi && jabatanMapping[valDivisi]) {
                const listJabatan = jabatanMapping[valDivisi];
                let optionsHtml = '';
                listJabatan.forEach((jab, idx) => {
                    const isSelected = (initialJabatan === jab) ? 'selected' : (idx === 0 ? 'selected' : '');
                    optionsHtml += `<option value="${jab}" ${isSelected}>${jab}</option>`;
                });
                optionsHtml += `<option value="ADD_NEW_JABATAN">+ Input Jabatan Custom</option>`;

                selectJabatan.innerHTML = optionsHtml;
                jabatanSelectBox.classList.remove('d-none');
                selectJabatan.required = true;
                selectJabatan.setAttribute('name', 'jabatan');

                if (selectJabatan.value === 'ADD_NEW_JABATAN') {
                    jabatanCustomBox.classList.remove('d-none');
                    inputJabatanCustom.required = true;
                    selectJabatan.removeAttribute('name');
                    inputJabatanCustom.setAttribute('name', 'jabatan');
                } else {
                    jabatanCustomBox.classList.add('d-none');
                    inputJabatanCustom.required = false;
                    inputJabatanCustom.removeAttribute('name');
                }
            } else {
                selectJabatan.innerHTML = '<option value="">-- Pilih Divisi Terlebih Dahulu --</option>';
                jabatanSelectBox.classList.remove('d-none');
                jabatanCustomBox.classList.add('d-none');
            }
        }
    }

    if (selectDivisi) {
        selectDivisi.addEventListener('change', updateJabatanOptions);
    }

    if (selectJabatan) {
        selectJabatan.addEventListener('change', function () {
            if (this.value === 'ADD_NEW_JABATAN') {
                jabatanCustomBox.classList.remove('d-none');
                inputJabatanCustom.required = true;
                this.removeAttribute('name');
                inputJabatanCustom.setAttribute('name', 'jabatan');
            } else {
                jabatanCustomBox.classList.add('d-none');
                inputJabatanCustom.required = false;
                inputJabatanCustom.removeAttribute('name');
                this.setAttribute('name', 'jabatan');
            }
        });
    }

    if (initialDivisi) {
        if (!jabatanMapping[initialDivisi] && initialDivisi !== '') {
            selectDivisi.value = 'ADD_NEW_DIVISI';
            updateJabatanOptions();
            inputDivisiCustom.value = initialDivisi;
            inputJabatanCustom.value = initialJabatan;
        } else {
            selectDivisi.value = initialDivisi;
            updateJabatanOptions();
            if (initialJabatan && !jabatanMapping[initialDivisi].includes(initialJabatan)) {
                selectJabatan.value = 'ADD_NEW_JABATAN';
                jabatanCustomBox.classList.remove('d-none');
                inputJabatanCustom.value = initialJabatan;
                selectJabatan.removeAttribute('name');
                inputJabatanCustom.setAttribute('name', 'jabatan');
            }
        }
    } else {
        updateJabatanOptions();
    }
});

// SweetAlert Toast Notification Trigger
// Gunakan window.addEventListener('load') agar jQuery & SweetAlert sudah dimuat oleh footer
window.addEventListener('load', function () {
    if (typeof Swal === 'undefined') return;
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
});
</script>

<?= $this->include('direktur/templates/footer') ?>
