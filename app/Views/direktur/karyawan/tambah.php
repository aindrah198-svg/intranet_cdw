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

    <!-- Header Section Terpadu -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light">
        <div class="d-flex align-items-center">
            <div class="bg-gradient-primary text-white rounded-3 p-2.5 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px;">
                <i class="fas fa-user-plus fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold text-dark">Tambah Karyawan Baru</h4>
                <small class="text-muted d-none d-sm-inline">Masukkan data diri karyawan, penempatan divisi, dan status pekerjaan.</small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="button" id="btnAutoFillDummy" class="btn btn-outline-success rounded-pill px-3.5 py-2 shadow-sm d-inline-flex align-items-center text-sm fw-semibold" title="Isi form otomatis dengan data dummy">
                <i class="fas fa-magic me-1.5"></i> <span class="d-none d-md-inline">Isi Data Dummy</span><span class="d-inline d-md-none">Dummy</span>
            </button>
            <a href="<?= base_url('direktur/karyawan') ?>" class="btn btn-outline-secondary rounded-pill px-3.5 py-2 shadow-sm d-inline-flex align-items-center text-sm fw-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> <span class="d-none d-md-inline">Kembali ke Daftar</span><span class="d-inline d-md-none">Kembali</span>
            </a>
        </div>
    </div>

    <!-- Form Input Card -->
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

                    <form action="<?= base_url('direktur/karyawan/simpan') ?>" method="post">
                        <?= csrf_field() ?>

                        <!-- Section 1: Data Pribadi -->
                        <div class="form-section-title">
                            <i class="fas fa-user-id-card text-primary"></i> Data Pribadi Karyawan
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">NIK (Nomor Induk Karyawan) <span class="text-danger">*</span></label>
                                <input type="text" name="nik" class="form-control form-control-custom fw-bold text-primary" value="<?= old('nik') ?: esc($autoNik ?? '') ?>" required placeholder="Misal: CDW2026001">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama_lengkap" class="form-control form-control-custom" value="<?= old('nama_lengkap') ?>" required placeholder="Nama lengkap karyawan">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control form-control-custom" value="<?= old('tempat_lahir') ?>" placeholder="Kota kelahiran">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control form-control-custom" value="<?= old('tanggal_lahir') ?>">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select form-select-custom">
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="L" <?= old('jenis_kelamin') == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="P" <?= old('jenis_kelamin') == 'P' ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">No. Telepon / WhatsApp</label>
                                <input type="text" name="telepon" class="form-control form-control-custom" value="<?= old('telepon') ?>" placeholder="08xxxxxxxxxx">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">Email Pribadi / Kerja</label>
                                <input type="email" name="email" class="form-control form-control-custom" value="<?= old('email') ?>" placeholder="karyawan@company.com">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">No. KTP / NIK Kependudukan</label>
                                <input type="text" name="no_ktp" class="form-control form-control-custom" value="<?= old('no_ktp') ?>" placeholder="16 digit NIK KTP">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-sm">Alamat Lengkap</label>
                                <textarea name="alamat" class="form-control form-control-custom" rows="3" placeholder="Alamat domisili lengkap..."><?= old('alamat') ?></textarea>
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
                                    <option value="Teknisi" <?= old('divisi') == 'Teknisi' ? 'selected' : '' ?>>Teknisi</option>
                                    <option value="Engineering" <?= old('divisi') == 'Engineering' ? 'selected' : '' ?>>Engineering</option>
                                    <option value="Keuangan" <?= old('divisi') == 'Keuangan' ? 'selected' : '' ?>>Keuangan</option>
                                    <option value="Marketing" <?= old('divisi') == 'Marketing' ? 'selected' : '' ?>>Marketing</option>
                                    <option value="HRD" <?= old('divisi') == 'HRD' ? 'selected' : '' ?>>HRD</option>
                                    <option value="Admin" <?= old('divisi') == 'Admin' ? 'selected' : '' ?>>Admin</option>
                                    <option value="Operasional" <?= old('divisi') == 'Operasional' ? 'selected' : '' ?>>Operasional</option>
                                    <option value="ADD_NEW_DIVISI" <?= old('divisi') == 'ADD_NEW_DIVISI' ? 'selected' : '' ?>>+ Tambah Divisi Baru</option>
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

                            <!-- Status Karyawan -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">Status Karyawan <span class="text-danger">*</span></label>
                                <select name="status_karyawan" class="form-select form-select-custom" required>
                                    <option value="">-- Pilih Status Karyawan --</option>
                                    <option value="Tetap" <?= old('status_karyawan', 'Tetap') == 'Tetap' ? 'selected' : '' ?>>Tetap</option>
                                    <option value="Kontrak" <?= old('status_karyawan') == 'Kontrak' ? 'selected' : '' ?>>Kontrak (PKWT)</option>
                                    <option value="Probation" <?= old('status_karyawan') == 'Probation' ? 'selected' : '' ?>>Probation (Percobaan)</option>
                                    <option value="Magang" <?= old('status_karyawan') == 'Magang' ? 'selected' : '' ?>>Magang / Internship</option>
                                    <option value="Staff" <?= old('status_karyawan') == 'Staff' ? 'selected' : '' ?>>Staff</option>
                                </select>
                            </div>

                            <!-- Tanggal Masuk -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">Tanggal Masuk (Join Date)</label>
                                <input type="date" name="tanggal_masuk" class="form-control form-control-custom" value="<?= old('tanggal_masuk') ?: date('Y-m-d') ?>">
                            </div>

                            <!-- NPWP -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-sm">Nomor NPWP (Opsional)</label>
                                <input type="text" name="no_npwp" class="form-control form-control-custom" value="<?= old('no_npwp') ?>" placeholder="Nomor NPWP">
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-3 border-top border-light d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <button type="button" id="btnAutoFillDummyBottom" class="btn btn-outline-success rounded-pill px-3.5 py-2 text-sm fw-semibold">
                                <i class="fas fa-magic me-1.5"></i> Isi Data Dummy
                            </button>
                            <div class="d-flex align-items-center gap-2">
                                <a href="<?= base_url('direktur/karyawan') ?>" class="btn btn-light rounded-pill px-4 py-2 text-sm fw-semibold border">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 text-sm fw-semibold shadow-sm">
                                    <i class="fas fa-save me-1.5"></i> Simpan Data Karyawan
                                </button>
                            </div>
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

    const initialDivisi  = "<?= esc(old('divisi', '')) ?>";
    const initialJabatan = "<?= esc(old('jabatan', '')) ?>";

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
                    const isSelected = (initialJabatan === jab || idx === 0) ? 'selected' : '';
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

    // Auto-fill Dummy Data Handler
    function fillDummyData() {
        const dummyNames = [
            { name: 'Budi Santoso', gender: 'L' },
            { name: 'Dewi Rahayu', gender: 'P' },
            { name: 'Ahmad Fauzi', gender: 'L' },
            { name: 'Siti Nurhaliza', gender: 'P' },
            { name: 'Hendra Gunawan', gender: 'L' },
            { name: 'Rina Marlina', gender: 'P' },
            { name: 'Dodi Firmansyah', gender: 'L' },
            { name: 'Yoga Maulana', gender: 'L' }
        ];

        const selected = dummyNames[Math.floor(Math.random() * dummyNames.length)];
        const randomNum = Math.floor(100 + Math.random() * 900);
        const nikVal = '<?= esc($autoNik ?? '') ?>' || ('CDW' + dateNowYear() + (Math.floor(1000 + Math.random() * 9000)));
        const nameVal = selected.name;
        const emailVal = nameVal.toLowerCase().replace(/[^a-z0-9]/g, '.') + randomNum + '@cdw-engineering.com';
        const phoneVal = '0812' + Math.floor(10000000 + Math.random() * 90000000);
        const ktpVal = '31710' + Math.floor(10000000000 + Math.random() * 90000000000);
        const npwpVal = '09.' + Math.floor(100 + Math.random() * 900) + '.' + Math.floor(100 + Math.random() * 900) + '.4-015.000';

        function dateNowYear() {
            return new Date().getFullYear();
        }

        // Populate fields
        const elNik = document.querySelector('input[name="nik"]');
        const elNama = document.querySelector('input[name="nama_lengkap"]');
        const elTempat = document.querySelector('input[name="tempat_lahir"]');
        const elTglLahir = document.querySelector('input[name="tanggal_lahir"]');
        const elGender = document.querySelector('select[name="jenis_kelamin"]');
        const elPhone = document.querySelector('input[name="telepon"]');
        const elEmail = document.querySelector('input[name="email"]');
        const elKtp = document.querySelector('input[name="no_ktp"]');
        const elAlamat = document.querySelector('textarea[name="alamat"]');
        const elStatus = document.querySelector('select[name="status_karyawan"]');
        const elTglMasuk = document.querySelector('input[name="tanggal_masuk"]');
        const elNpwp = document.querySelector('input[name="no_npwp"]');

        if (elNik) elNik.value = nikVal;
        if (elNama) elNama.value = nameVal;
        if (elTempat) elTempat.value = 'Jakarta';
        if (elTglLahir) elTglLahir.value = '1995-08-17';
        if (elGender) elGender.value = selected.gender;
        if (elPhone) elPhone.value = phoneVal;
        if (elEmail) elEmail.value = emailVal;
        if (elKtp) elKtp.value = ktpVal;
        if (elAlamat) elAlamat.value = 'Jl. Jend. Sudirman No. ' + Math.floor(1 + Math.random() * 100) + ', Jakarta Selatan';
        if (elStatus) elStatus.value = 'Tetap';
        if (elTglMasuk) elTglMasuk.value = '2024-01-15';
        if (elNpwp) elNpwp.value = npwpVal;

        // Select Divisi & Jabatan
        const divisipool = ['Teknisi', 'Engineering', 'Keuangan', 'Marketing', 'HRD', 'Admin', 'Operasional'];
        const chosenDiv = divisipool[Math.floor(Math.random() * divisipool.length)];
        if (selectDivisi) {
            selectDivisi.value = chosenDiv;
            updateJabatanOptions();
            if (selectJabatan && selectJabatan.options.length > 1) {
                selectJabatan.selectedIndex = Math.floor(1 + Math.random() * (selectJabatan.options.length - 1));
            }
        }

        // SweetAlert Notification
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: '✨ Form Terisi Data Dummy!',
                html: 'Form berhasil diisi secara otomatis.<br><small class="text-muted">Silakan klik <strong>"Simpan Data Karyawan"</strong> untuk menyimpan.</small>',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
        }
    }

    const btnAutoFill = document.getElementById('btnAutoFillDummy');
    if (btnAutoFill) btnAutoFill.addEventListener('click', fillDummyData);

    const btnAutoFillBottom = document.getElementById('btnAutoFillDummyBottom');
    if (btnAutoFillBottom) btnAutoFillBottom.addEventListener('click', fillDummyData);
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
