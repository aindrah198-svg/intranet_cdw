<?php
$data = [
    'title'    => 'Form Pengajuan Cuti Baru',
    'subtitle' => 'Isi Formulir Permohonan Cuti Tahunan, Sakit, atau Cuti Khusus',
    'active'   => 'pengajuan-cuti',
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
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/pengajuan/cuti') ?>" class="text-decoration-none text-muted">Pengajuan Cuti</a></li>
                    <li class="breadcrumb-item active text-info fw-bold" aria-current="page">Form Cuti Baru</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-umbrella-beach text-info me-2"></i> Form Permohonan Cuti Karyawan</h4>
            <small class="text-muted">Isi formulir pengajuan Cuti Tahunan, Cuti Sakit (Rawat Inap), Cuti Hamil/Melahirkan, atau Cuti Khusus.</small>
        </div>
        <div>
            <a href="<?= base_url('admin/pengajuan/cuti') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali ke Pengajuan Cuti
            </a>
        </div>
    </div>

    <!-- Main Card Form -->
    <div class="row justify-content-center">
        <div class="col-lg-9">

            <!-- Information Card Kuota Cuti Saya -->
            <div class="card p-3.5 mb-4 border-0 rounded-4 shadow-sm" style="background: linear-gradient(135deg, #e0f7fa, #e1f5fe); border-left: 5px solid #0288d1 !important;">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle text-white me-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: #0288d1 !important;">
                            <i class="fas fa-chart-pie fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0 fs-6">Jatah Kuota Cuti Tahunan Saya (Tahun <?= date('Y') ?>)</h6>
                            <small class="text-muted text-xs">
                                <?php if(!empty($kuotaInfo)): ?>
                                    Status Kuota: <strong class="text-dark"><?= (int)$kuotaInfo['kuota_tahunan'] ?> Hari</strong> Total &nbsp;|&nbsp; Terpakai: <strong class="text-danger"><?= (int)$kuotaInfo['terpakai'] ?> Hari</strong> &nbsp;|&nbsp; Sisa Kuota: <strong class="text-success fs-6"><?= (int)$sisaKuota ?> Hari</strong>
                                <?php else: ?>
                                    <span class="text-danger font-bold"><i class="fas fa-exclamation-triangle me-1"></i> Jatah kuota cuti tahunan belum ditambahkan oleh Direktur!</span>
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>

                <?php if(!$canAddCuti): ?>
                    <div class="alert alert-danger mb-0 mt-3 py-2 px-3 text-xs rounded-3 d-flex align-items-center border-danger bg-white text-danger fw-semibold">
                        <i class="fas fa-exclamation-circle fs-5 me-2.5 text-danger"></i>
                        <div>
                            <?php if(!empty($kuotaInfo)): ?>
                                <strong>Perhatian:</strong> Sisa kuota cuti tahunan Anda saat ini <strong>0 Hari</strong>. Anda tidak dapat mengajukan permohonan cuti baru sampai jatah kuota ditambahkan kembali oleh Direktur.
                            <?php else: ?>
                                <strong>Perhatian:</strong> Anda belum memiliki jatah kuota cuti tahunan. Silakan minta Direktur untuk menambahkan kuota cuti pada menu <strong>Karyawan & SDM -> Cuti Karyawan</strong> di portal Direktur.
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                <div class="card-header text-white py-3 px-4" style="background: linear-gradient(135deg, #0288d1, #01579b);">
                    <h5 class="card-title fs-6 fw-bold mb-0"><i class="fas fa-edit me-2"></i> Formulir Input Pengajuan Cuti</h5>
                </div>
                <form action="<?= base_url('admin/pengajuan/cuti/simpan') ?>" method="POST" onsubmit="const btn = this.querySelector('button[type=submit]'); if(btn){ btn.disabled = true; btn.innerHTML = '<i class=\'fas fa-spinner fa-spin me-1\'></i> Mengirim...'; }">
                    <div class="card-body p-4">

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Kategori / Jenis Cuti *</label>
                                <select name="jenis_cuti" class="form-select rounded-3" required>
                                    <option value="Tahunan" selected>Cuti Tahunan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Pemohon Cuti (Saya Saat Ini) *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-primary"><i class="fas fa-user-lock"></i></span>
                                    <input type="text" class="form-control rounded-end-3 bg-light text-dark fw-bold" value="<?= esc($currentKaryawan['nama_lengkap'] ?? session()->get('name') ?? 'Admin Staff') ?> (<?= esc($currentKaryawan['divisi'] ?? 'Administrasi') ?> - <?= esc($currentKaryawan['jabatan'] ?? 'Staff') ?>)" readonly>
                                </div>
                                <input type="hidden" name="karyawan_id" value="<?= $kuotaData['karyawan_id'] ?>">
                                <small class="text-muted text-xs"><i class="fas fa-info-circle me-1"></i> Permohonan cuti terikat pada akun Anda yang sedang login.</small>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Tanggal Mulai Cuti *</label>
                                <input type="date" class="form-control rounded-3" id="tglMulaiCuti" name="tanggal_mulai" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Tanggal Selesai Cuti *</label>
                                <input type="date" class="form-control rounded-3" id="tglSelesaiCuti" name="tanggal_selesai" value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-12">
                                <div id="durasiInfoBadge" class="p-2.5 rounded-3 text-xs fw-semibold bg-light border"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Alasan & Catatan Permohonan Cuti *</label>
                            <textarea class="form-control rounded-3" name="alasan" rows="4" required placeholder="Tuliskan alasan lengkap permohonan cuti, alamat selama cuti, dan nama karyawan pengganti sementara..."></textarea>
                        </div>

                    </div>
                    <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                        <a href="<?= base_url('admin/pengajuan/cuti') ?>" class="btn btn-secondary rounded-pill px-4 me-2 font-semibold">Batal</a>
                        
                        <?php if($canAddCuti): ?>
                            <button type="submit" id="btnSubmitCuti" class="btn btn-info text-white rounded-pill px-4 font-semibold shadow-sm" style="background: #0288d1; border-color: #0288d1;">
                                <i class="fas fa-paper-plane me-1.5"></i> Kirim Permohonan Cuti
                            </button>
                        <?php else: ?>
                            <button type="button" onclick="alertNoQuota()" class="btn btn-secondary rounded-pill px-4 font-semibold shadow-sm opacity-75">
                                <i class="fas fa-lock me-1.5"></i> Kirim Permohonan Cuti (Dikunci)
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const sisaKuotaGlobal = <?= (int)$sisaKuota ?>;

function validateCutiDates() {
    const tglMulai = document.getElementById('tglMulaiCuti');
    const tglSelesai = document.getElementById('tglSelesaiCuti');
    const btnSubmit = document.getElementById('btnSubmitCuti');
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

        if (diffDays > sisaKuotaGlobal) {
            durasiInfo.className = 'p-2.5 rounded-3 text-xs fw-bold bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
            durasiInfo.innerHTML = `<i class="fas fa-ban me-1.5"></i> Durasi permohonan (<strong>${diffDays} Hari</strong>) MELEBIHI sisa kuota cuti tahunan Anda (<strong>${sisaKuotaGlobal} Hari</strong>)!`;
            if (btnSubmit) { btnSubmit.disabled = true; btnSubmit.classList.add('opacity-50'); }
            return false;
        } else {
            durasiInfo.className = 'p-2.5 rounded-3 text-xs fw-semibold bg-success bg-opacity-10 text-success border border-success border-opacity-25';
            durasiInfo.innerHTML = `<i class="fas fa-check-circle me-1.5"></i> Total permohonan cuti: <strong>${diffDays} Hari Kerja</strong> (Estimasi sisa kuota setelah cuti: ${sisaKuotaGlobal - diffDays} Hari)`;
            if (btnSubmit && sisaKuotaGlobal > 0) { btnSubmit.disabled = false; btnSubmit.classList.remove('opacity-50'); }
            return true;
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const tglMulai = document.getElementById('tglMulaiCuti');
    const tglSelesai = document.getElementById('tglSelesaiCuti');
    if (tglMulai && tglSelesai) {
        tglMulai.addEventListener('change', function() {
            if (tglSelesai.value < tglMulai.value) {
                tglSelesai.value = tglMulai.value;
            }
            validateCutiDates();
        });
        tglSelesai.addEventListener('change', validateCutiDates);
        validateCutiDates();
    }
});

function alertNoQuota() {
    Swal.fire({
        icon: 'error',
        title: 'Permohonan Cuti Dibatasi',
        html: '<?= !empty($kuotaInfo) ? "Sisa kuota cuti tahunan Anda saat ini <strong>0 Hari</strong>." : "Jatah kuota cuti tahunan Anda <strong>belum ditambahkan oleh Direktur</strong>." ?><br><br>Silakan minta Direktur untuk menambahkan/memperbarui jatah kuota cuti Anda pada menu <strong>Karyawan & SDM -> Cuti Karyawan</strong> di portal Direktur.',
        confirmButtonColor: '#d32f2f',
        customClass: { popup: 'rounded-4 shadow-lg' }
    });
}
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
