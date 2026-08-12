<?php
$data = [
    'title'    => 'Form Pengajuan Permohonan & Izin',
    'subtitle' => 'Isi Formulir Pengajuan Sakit, Kecelakaan, WFH, WFC, Dinas, & Izin (Luar Cuti)',
    'active'   => 'pengajuan-semua',
    'user'     => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
];

echo view('admin/templates/header', $data);
echo view('admin/templates/sidebar', $data);
echo view('admin/templates/navbar', $data);
?>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/pengajuan/semua') ?>" class="text-decoration-none text-muted">Semua Pengajuan</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Form Pengajuan</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-paper-plane text-primary me-2"></i> Form Permohonan & Izin (Non-Cuti)</h4>
            <small class="text-muted">Formulir pengajuan Sakit, Kecelakaan Kerja, WFH, WFC, Perjalanan Dinas, & Izin Pribadi.</small>
        </div>
        <div>
            <a href="<?= base_url('admin/pengajuan/semua') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Main Card Form -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                <div class="card-header text-white py-3 px-4" style="background: linear-gradient(135deg, #4a148c, #7b1fa2);">
                    <h5 class="card-title fs-6 fw-bold mb-0"><i class="fas fa-edit me-2"></i> Formulir Input Permohonan & Izin</h5>
                </div>
                <form action="<?= base_url('admin/pengajuan/simpan') ?>" method="POST" enctype="multipart/form-data" onsubmit="const btn = this.querySelector('button[type=submit]'); if(btn){ btn.disabled = true; btn.innerHTML = '<i class=\'fas fa-spinner fa-spin me-1\'></i> Mengirim...'; }">
                    <div class="card-body p-4">
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Kategori Permohonan / Izin *</label>
                                <select name="kategori_pengajuan" class="form-select rounded-3" required>
                                    <option value="Sakit / Izin Medis">Sakit / Izin Medis</option>
                                    <option value="Kecelakaan Kerja">Kecelakaan Kerja</option>
                                    <option value="WFH (Work From Home)">WFH (Work From Home)</option>
                                    <option value="WFC (Work From Cafe)">WFC (Work From Cafe)</option>
                                    <option value="Perjalanan Dinas Luar">Perjalanan Dinas / Tugas Luar</option>
                                    <option value="Izin Terlambat / Pulang Awal">Izin Terlambat / Pulang Awal</option>
                                    <option value="Permohonan Administrasi">Permohonan Administrasi / General</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Pemohon / Karyawan (Saya Saat Ini) *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-primary"><i class="fas fa-user-lock"></i></span>
                                    <input type="text" class="form-control rounded-end-3 bg-light text-dark fw-bold" value="<?= esc($currentKaryawan['nama_lengkap'] ?? session()->get('name') ?? 'Admin Staff') ?> (<?= esc($currentKaryawan['divisi'] ?? 'Administrasi') ?> - <?= esc($currentKaryawan['jabatan'] ?? 'Staff') ?>)" readonly>
                                </div>
                                <input type="hidden" name="karyawan_id" value="<?= $currentKaryawan['id'] ?? 1 ?>">
                                <small class="text-muted text-xs"><i class="fas fa-info-circle me-1"></i> Permohonan terikat pada akun Anda yang sedang login.</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Judul / Perihal Permohonan *</label>
                            <input type="text" class="form-control rounded-3" name="judul_pengajuan" required placeholder="Cth: Izin Sakit Demam 2 Hari / Tugas Dinas Karawang / WFH Hari Jumat">
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Tanggal Mulai *</label>
                                <input type="date" class="form-control rounded-3" id="tglMulaiPengajuan" name="tanggal_mulai" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Tanggal Selesai *</label>
                                <input type="date" class="form-control rounded-3" id="tglSelesaiPengajuan" name="tanggal_selesai" value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-12">
                                <div id="durasiInfoBadge" class="p-2.5 rounded-3 text-xs fw-semibold bg-light border"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Keterangan / Alasan Permohonan *</label>
                            <textarea class="form-control rounded-3" name="keterangan" rows="4" required placeholder="Tuliskan alasan lengkap, lokasi kerja WFH/WFC, kebutuhan penugasan dinas, atau catatan pelimpahan tugas..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Upload Bukti Foto Pendukung *</label>
                            <input type="file" class="form-control rounded-3" name="bukti_foto" id="buktiFotoInput" accept="image/*" required onchange="previewBuktiFoto(this)">
                            <small class="text-muted text-xs d-block mt-1">
                                <i class="fas fa-image text-primary me-1"></i> Wajib melampirkan foto bukti (Surat Keterangan Dokter, Foto Surat Dinas, Tiket Perjalanan, Foto Lokasi WFH/WFC, dll). Gambar akan otomatis dikompres oleh sistem.
                            </small>
                            <div id="imagePreviewContainer" class="mt-2 text-center" style="display: none;">
                                <img id="imagePreview" src="#" alt="Pratinjau Foto Bukti" class="img-thumbnail rounded-3 shadow-sm" style="max-height: 200px;">
                            </div>
                        </div>

                    </div>
                    <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                        <a href="<?= base_url('admin/pengajuan/semua') ?>" class="btn btn-secondary rounded-pill px-4 me-2 font-semibold">Batal</a>
                        <button type="submit" id="btnSubmitPengajuan" class="btn btn-primary rounded-pill px-4 font-semibold shadow-sm" style="background: #7b1fa2; border-color: #7b1fa2;">
                            <i class="fas fa-paper-plane me-1.5"></i> Kirim Pengajuan Permohonan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function validatePengajuanDates() {
    const tglMulai = document.getElementById('tglMulaiPengajuan');
    const tglSelesai = document.getElementById('tglSelesaiPengajuan');
    const btnSubmit = document.getElementById('btnSubmitPengajuan');
    const durasiInfo = document.getElementById('durasiInfoBadge');

    if (!tglMulai || !tglSelesai || !durasiInfo) return;

    // Kunci tanggal selesai minimal sama dengan tanggal mulai (hari yang sama diperbolehkan)
    tglSelesai.min = tglMulai.value;

    if (tglMulai.value && tglSelesai.value) {
        const d1 = new Date(tglMulai.value);
        const d2 = new Date(tglSelesai.value);

        if (d2 < d1) {
            durasiInfo.className = 'p-2.5 rounded-3 text-xs fw-bold bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
            durasiInfo.innerHTML = '<i class="fas fa-exclamation-circle me-1.5"></i> Tanggal selesai tidak boleh lebih awal dari tanggal mulai!';
            if (btnSubmit) { btnSubmit.disabled = true; btnSubmit.classList.add('opacity-50'); }
            return false;
        }

        const diffTime = Math.abs(d2 - d1);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

        durasiInfo.className = 'p-2.5 rounded-3 text-xs fw-semibold bg-success bg-opacity-10 text-success border border-success border-opacity-25';
        durasiInfo.innerHTML = `<i class="fas fa-check-circle me-1.5"></i> Durasi pelaksanaan permohonan: <strong>${diffDays} Hari Kerja</strong>`;
        if (btnSubmit) { btnSubmit.disabled = false; btnSubmit.classList.remove('opacity-50'); }
        return true;
    }
}

function previewBuktiFoto(input) {
    const container = document.getElementById('imagePreviewContainer');
    const preview = document.getElementById('imagePreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            container.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        container.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const tglMulai = document.getElementById('tglMulaiPengajuan');
    const tglSelesai = document.getElementById('tglSelesaiPengajuan');
    if (tglMulai && tglSelesai) {
        tglMulai.addEventListener('change', function() {
            if (tglSelesai.value < tglMulai.value) {
                tglSelesai.value = tglMulai.value;
            }
            validatePengajuanDates();
        });
        tglSelesai.addEventListener('change', validatePengajuanDates);
        validatePengajuanDates();
    }
});
</script>

<?php if (session()->getFlashdata('error')): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'error',
        title: 'Perhatian!',
        text: '<?= esc(session()->getFlashdata('error')) ?>',
        confirmButtonColor: '#dc3545',
        customClass: { popup: 'rounded-4 shadow-lg' }
    });
});
</script>
<?php endif; ?>

<?= view('admin/templates/footer', $data) ?>
