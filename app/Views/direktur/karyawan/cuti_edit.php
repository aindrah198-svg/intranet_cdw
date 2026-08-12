<?php
$data = [
    'title'  => 'Edit Permohonan Cuti Karyawan',
    'active' => 'karyawan',
    'user'   => ['name' => session()->get('name') ?? 'Direktur', 'role' => 'direktur']
];

echo view('direktur/templates/header', $data);
echo view('direktur/templates/sidebar', $data);
echo view('direktur/templates/navbar', $data);
?>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= base_url('direktur/karyawan/cuti') ?>" class="text-decoration-none text-muted">Cuti Karyawan</a></li>
                    <li class="breadcrumb-item active text-warning fw-bold" aria-current="page">Edit Cuti</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-edit text-warning me-2"></i> Edit Permohonan & Status Cuti (Direktur)</h4>
            <small class="text-muted">Perbarui status persetujuan, tanggal pelaksanaan, atau catatan penolakan permohonan cuti.</small>
        </div>
        <div>
            <a href="<?= base_url('direktur/karyawan/cuti') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                <div class="card-header text-white py-3 px-4" style="background: linear-gradient(135deg, #1e3c72, #2a5298);">
                    <h5 class="card-title fs-6 fw-bold mb-0"><i class="fas fa-user-edit me-2"></i> Form Edit Cuti: <?= esc($c['nama_lengkap'] ?? 'Karyawan') ?> (Nomor: <?= esc($c['nomor_cuti']) ?>)</h5>
                </div>
                <form action="<?= base_url('direktur/karyawan/cuti/update/'.$c['id']) ?>" method="POST" onsubmit="const btn = this.querySelector('button[type=submit]'); if(btn){ btn.disabled = true; btn.innerHTML = '<i class=\'fas fa-spinner fa-spin me-1\'></i> Menyimpan...'; }">
                    <div class="card-body p-4">
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Pemohon / Karyawan</label>
                                <input type="text" class="form-control rounded-3 bg-light text-dark fw-bold" value="<?= esc($c['nama_lengkap'] ?? 'Admin/Karyawan') ?> (<?= esc($c['divisi'] ?? '-') ?>)" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Status Persetujuan Direktur *</label>
                                <select name="status" id="statusSelect" class="form-select rounded-3 fw-bold" onchange="toggleAlasanPenolakan()" required>
                                    <option value="Menunggu" <?= (strtolower($c['status'] ?? '') === 'menunggu') ? 'selected' : '' ?>>Menunggu Persetujuan</option>
                                    <option value="Disetujui" <?= (strtolower($c['status'] ?? '') === 'disetujui') ? 'selected' : '' ?>>Disetujui (Memotong Kuota Cuti)</option>
                                    <option value="Ditolak" <?= (strtolower($c['status'] ?? '') === 'ditolak') ? 'selected' : '' ?>>Ditolak (Bebas Potong Kuota)</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Tanggal Mulai Cuti *</label>
                                <input type="date" class="form-control rounded-3" id="tglMulaiCuti" name="tanggal_mulai" value="<?= esc($c['tanggal_mulai']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Tanggal Selesai Cuti *</label>
                                <input type="date" class="form-control rounded-3" id="tglSelesaiCuti" name="tanggal_selesai" value="<?= esc($c['tanggal_selesai']) ?>" min="<?= esc($c['tanggal_mulai']) ?>" required>
                            </div>
                            <div class="col-12">
                                <div id="durasiInfoBadge" class="p-2.5 rounded-3 text-xs fw-semibold bg-light border"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Alasan Permohonan Cuti *</label>
                            <textarea class="form-control rounded-3" name="alasan" rows="3" required><?= esc($c['alasan']) ?></textarea>
                        </div>

                        <div class="mb-3" id="penolakanWrapper" style="display: none;">
                            <label class="form-label fw-semibold text-xs text-danger">Catatan Alasan Penolakan Direktur *</label>
                            <textarea class="form-control rounded-3 border-danger" name="alasan_penolakan" rows="2" placeholder="Tuliskan catatan pertimbangan penolakan permohonan cuti..."><?= esc($c['alasan_penolakan'] ?? '') ?></textarea>
                        </div>

                    </div>
                    <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                        <a href="<?= base_url('direktur/karyawan/cuti') ?>" class="btn btn-secondary rounded-pill px-4 me-2 font-semibold">Batal</a>
                        <button type="submit" id="btnSubmitEdit" class="btn btn-warning text-white rounded-pill px-4 font-semibold shadow-sm" style="background: #f57c00; border-color: #f57c00;">
                            <i class="fas fa-save me-1.5"></i> Simpan Perubahan & Sinkronkan Kuota
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function toggleAlasanPenolakan() {
    const val = document.getElementById('statusSelect').value;
    const wrapper = document.getElementById('penolakanWrapper');
    if (val === 'Ditolak') {
        wrapper.style.display = 'block';
    } else {
        wrapper.style.display = 'none';
    }
}

function validateCutiEditDates() {
    const tglMulai = document.getElementById('tglMulaiCuti');
    const tglSelesai = document.getElementById('tglSelesaiCuti');
    const btnSubmit = document.getElementById('btnSubmitEdit');
    const durasiInfo = document.getElementById('durasiInfoBadge');

    if (!tglMulai || !tglSelesai || !durasiInfo) return;

    // Kunci tanggal selesai minimal sama dengan tanggal mulai
    tglSelesai.min = tglMulai.value;

    if (tglMulai.value && tglSelesai.value) {
        const d1 = new Date(tglMulai.value);
        const d2 = new Date(tglSelesai.value);

        if (d2 < d1) {
            durasiInfo.className = 'p-2.5 rounded-3 text-xs fw-bold bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
            durasiInfo.innerHTML = '<i class="fas fa-exclamation-circle me-1.5"></i> Tanggal selesai cuti tidak boleh lebih awal dari tanggal mulai!';
            if (btnSubmit) { btnSubmit.disabled = true; btnSubmit.classList.add('opacity-50'); }
            return false;
        }

        const diffTime = Math.abs(d2 - d1);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

        durasiInfo.className = 'p-2.5 rounded-3 text-xs fw-semibold bg-info bg-opacity-10 text-info border border-info border-opacity-25';
        durasiInfo.innerHTML = `<i class="fas fa-calendar-check me-1.5"></i> Kalkulasi Durasi Cuti: <strong>${diffDays} Hari Kerja</strong> (Jatah kuota cuti akan disinkronkan otomatis saat status Disetujui).`;
        if (btnSubmit) { btnSubmit.disabled = false; btnSubmit.classList.remove('opacity-50'); }
        return true;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    toggleAlasanPenolakan();
    const tglMulai = document.getElementById('tglMulaiCuti');
    const tglSelesai = document.getElementById('tglSelesaiCuti');
    if (tglMulai && tglSelesai) {
        tglMulai.addEventListener('change', function() {
            if (tglSelesai.value < tglMulai.value) {
                tglSelesai.value = tglMulai.value;
            }
            validateCutiEditDates();
        });
        tglSelesai.addEventListener('change', validateCutiEditDates);
        validateCutiEditDates();
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

<?= view('direktur/templates/footer', $data) ?>
