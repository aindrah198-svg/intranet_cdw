<?php
$data = [
    'title'  => 'Pengajuan & Manajemen Cuti Karyawan',
    'active' => 'karyawan',
    'user'   => ['name' => session()->get('name') ?? 'Direktur', 'role' => 'direktur']
];

$totalCuti = count($cutiList ?? []);
$pendingCuti = 0;
$approvedCuti = 0;
$rejectedCuti = 0;

foreach($cutiList as $c) {
    $st = strtolower($c['status'] ?? '');
    if ($st === 'menunggu') $pendingCuti++;
    if ($st === 'disetujui') $approvedCuti++;
    if ($st === 'ditolak') $rejectedCuti++;
}
?>

<?= view('direktur/templates/header', $data) ?>
<?= view('direktur/templates/sidebar', $data) ?>
<?= view('direktur/templates/navbar', $data) ?>

<style>
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
        padding-left: 14px !important;
        padding-right: 14px !important;
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
    .dir-cuti-card {
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(30, 60, 114, 0.08) !important;
    }
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
        min-width: 900px !important;
        width: 100% !important;
        margin-bottom: 0 !important;
    }
</style>

<div class="container-fluid py-3 py-md-4">

    <!-- 1. Header Page -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-3 border border-light flex-wrap gap-3">
        <div class="d-flex align-items-center overflow-hidden">
            <div class="bg-primary text-white rounded-3 p-2 me-3 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 44px; height: 44px; background: linear-gradient(135deg, #1e3c72, #2a5298) !important;">
                <i class="fas fa-umbrella-beach fs-5"></i>
            </div>
            <div class="overflow-hidden">
                <h4 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 1.25rem;">Persetujuan & Manajemen Cuti Karyawan</h4>
                <small class="text-muted d-none d-sm-inline">Persetujuan permohonan cuti dan pengelolalan jatah kuota tahunan karyawan.</small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="button" data-bs-toggle="modal" data-bs-target="#modalKuotaCuti" class="btn btn-primary rounded-pill shadow-sm d-inline-flex align-items-center text-sm fw-semibold px-3" style="background: #1e3c72; border-color: #1e3c72;">
                <i class="fas fa-cog me-1.5"></i> <span>Atur Kuota Cuti Karyawan</span>
            </button>
        </div>
    </div>

    <!-- 2. KPI Ringkasan Cards -->
    <div class="row g-2 g-md-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card p-3 rounded-4 border-0 shadow-sm bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-layer-group text-primary"></i>
                    </div>
                    <div>
                        <small class="text-muted text-xs uppercase fw-bold d-block">Total Permohonan</small>
                        <div class="fs-4 fw-bold text-primary"><?= number_format($totalCuti) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3 rounded-4 border-0 shadow-sm bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-clock text-warning"></i>
                    </div>
                    <div>
                        <small class="text-muted text-xs uppercase fw-bold d-block">Butuh Persetujuan</small>
                        <div class="fs-4 fw-bold text-warning"><?= number_format($pendingCuti) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3 rounded-4 border-0 shadow-sm bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                    <div>
                        <small class="text-muted text-xs uppercase fw-bold d-block">Telah Disetujui</small>
                        <div class="fs-4 fw-bold text-success"><?= number_format($approvedCuti) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3 rounded-4 border-0 shadow-sm bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-danger bg-opacity-10 me-2.5 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-times-circle text-danger"></i>
                    </div>
                    <div>
                        <small class="text-muted text-xs uppercase fw-bold d-block">Ditolak</small>
                        <div class="fs-4 fw-bold text-danger"><?= number_format($rejectedCuti) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Tabel Cuti Direktur -->
    <div class="card dir-cuti-card p-3 p-md-4 mb-3">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-0 fs-6 fs-md-5 d-flex align-items-center">
                    <i class="fas fa-list-check me-2 text-primary"></i> Permohonan Cuti Masuk Dari Karyawan / Admin
                </h5>
                <small class="text-muted text-xs d-block mt-0.5">Tinjau, setujui, atau tolak permohonan cuti yang diajukan.</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <input type="text" id="searchTableDirCuti" class="form-control form-control-sm rounded-pill px-3" style="max-width: 220px;" placeholder="Cari pemohon / nomor...">
            </div>
        </div>

        <div class="table-scroll-wrapper">
            <table class="table table-hover align-middle text-sm mb-0" id="dirCutiTable">
                <thead class="table-light text-nowrap">
                    <tr>
                        <th width="18%">No. Cuti / Tgl</th>
                        <th width="22%">Karyawan & Divisi</th>
                        <th width="15%">Jenis & Durasi</th>
                        <th width="15%">Periode Cuti</th>
                        <th width="12%">Status</th>
                        <th width="18%" class="text-center">Aksi / Approval</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($cutiList)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pengajuan cuti yang masuk.</td></tr>
                    <?php else: ?>
                        <?php foreach($cutiList as $c): ?>
                        <tr>
                            <td class="text-nowrap">
                                <div class="fw-bold text-dark"><i class="fas fa-file-invoice text-primary me-1"></i><?= esc($c['nomor_cuti']) ?></div>
                                <small class="text-muted text-xs"><i class="fas fa-calendar me-1"></i><?= date('d M Y', strtotime($c['tanggal_pengajuan'])) ?></small>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= esc($c['nama_lengkap'] ?: 'Admin User') ?></div>
                                <small class="text-muted text-xs"><?= esc($c['divisi'] ?: 'Administrasi') ?> - <?= esc($c['jabatan'] ?: 'Staff') ?></small>
                            </td>
                            <td class="text-nowrap">
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-0.5 rounded-pill text-xs fw-semibold">
                                    <?= esc($c['jenis_cuti']) ?>
                                </span>
                                <small class="text-dark fw-bold d-block mt-0.5"><?= (int)$c['lama_hari'] ?> Hari Kerja</small>
                            </td>
                            <td class="text-nowrap">
                                <small class="text-dark fw-semibold d-block"><?= date('d M Y', strtotime($c['tanggal_mulai'])) ?></small>
                                <small class="text-muted text-xs">s/d <?= date('d M Y', strtotime($c['tanggal_selesai'])) ?></small>
                            </td>
                            <td class="text-nowrap">
                                <?php
                                    $st = strtolower($c['status'] ?? 'menunggu');
                                    $badge = 'bg-warning text-dark';
                                    if ($st === 'disetujui') $badge = 'bg-success text-white';
                                    if ($st === 'ditolak') $badge = 'bg-danger text-white';
                                ?>
                                <span class="badge <?= $badge ?> px-2.5 py-1 rounded-pill text-xs fw-semibold">
                                    <?= strtoupper(esc($c['status'] ?? 'Menunggu')) ?>
                                </span>
                            </td>
                            <td class="text-center text-nowrap">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <a href="<?= base_url('direktur/karyawan/cuti/detail/'.$c['id']) ?>" class="btn btn-xs btn-outline-info rounded-pill px-2.5 py-1 text-xs fw-semibold">
                                        <i class="fas fa-eye me-1"></i> Detail
                                    </a>
                                    <a href="<?= base_url('direktur/karyawan/cuti/edit/'.$c['id']) ?>" class="btn btn-xs btn-outline-warning rounded-pill px-2.5 py-1 text-xs fw-semibold">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>

                                    <?php if(strtolower($c['status'] ?? '') === 'menunggu'): ?>
                                        <button type="button" onclick="approveCuti(<?= $c['id'] ?>, '<?= esc($c['nomor_cuti'], 'js') ?>')" class="btn btn-xs btn-success text-white rounded-pill px-2.5 py-1 text-xs fw-semibold">
                                            <i class="fas fa-check me-1"></i> Setuju
                                        </button>
                                        <button type="button" onclick="rejectCuti(<?= $c['id'] ?>, '<?= esc($c['nomor_cuti'], 'js') ?>')" class="btn btn-xs btn-danger text-white rounded-pill px-2.5 py-1 text-xs fw-semibold">
                                            <i class="fas fa-times me-1"></i> Tolak
                                        </button>
                                    <?php else: ?>
                                        <button type="button" onclick="confirmDeleteCuti(<?= $c['id'] ?>, '<?= esc($c['nomor_cuti'], 'js') ?>')" class="btn btn-xs btn-outline-danger rounded-pill px-2 py-1 text-xs fw-semibold">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
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

<!-- Modal Setting Kuota Cuti -->
<div class="modal fade" id="modalKuotaCuti" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header text-white rounded-top-4" style="background: linear-gradient(135deg, #1e3c72, #2a5298);">
                <h5 class="modal-title fs-6 fw-bold"><i class="fas fa-cog me-2"></i> Pengaturan Jatah Kuota Cuti Karyawan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('direktur/karyawan/cuti/kuota') ?>" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Pilih Karyawan *</label>
                        <select name="karyawan_id" class="form-select rounded-3" required>
                            <option value="">-- Pilih Karyawan --</option>
                            <?php foreach($karyawanList as $k): ?>
                                <?php
                                    $kq = $kuotaMap[$k['id']] ?? null;
                                    $sisaVal = $kq ? ($kq['sisa_kuota'] ?? $kq['sisa'] ?? max(0, ($kq['kuota_tahunan'] ?? 12) - ($kq['terpakai'] ?? 0))) : 0;
                                    $infoSisa = $kq ? "Sisa: {$sisaVal} dari {$kq['kuota_tahunan']} hari" : "Belum diset";
                                ?>
                                <option value="<?= $k['id'] ?>"><?= esc($k['nama_lengkap']) ?> (<?= esc($k['divisi']) ?>) - <?= $infoSisa ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Jatah Kuota Tahunan (Hari) *</label>
                        <input type="number" name="kuota_tahunan" class="form-control rounded-3" value="12" min="1" max="60" required>
                        <small class="text-muted text-xs">Standard perusahaan: 12 hari/tahun.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4 px-4 py-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm" style="background: #1e3c72; border-color: #1e3c72;">
                        <i class="fas fa-save me-1.5"></i> Simpan Kuota
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (session()->getFlashdata('success')): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '<?= esc(session()->getFlashdata('success')) ?>',
        confirmButtonColor: '#1e3c72',
        timer: 3000,
        timerProgressBar: true,
        customClass: { popup: 'rounded-4 shadow-lg' }
    });
});
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchTableDirCuti');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#dirCutiTable tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }
});

function approveCuti(id, nomor) {
    Swal.fire({
        title: 'Setujui Pengajuan Cuti?',
        text: 'Pengajuan cuti "' + nomor + '" akan disetujui & jatah kuota karyawan dipotong.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Setujui!',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-4 shadow-lg' }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= base_url('direktur/karyawan/cuti/approve') ?>/' + id;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function rejectCuti(id, nomor) {
    Swal.fire({
        title: 'Tolak Pengajuan Cuti?',
        text: 'Masukkan alasan penolakan permohonan cuti:',
        input: 'textarea',
        inputPlaceholder: 'Tuliskan alasan penolakan...',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Tolak Cuti',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-4 shadow-lg' }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= base_url('direktur/karyawan/cuti/reject') ?>/' + id;
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'alasan_penolakan';
            input.value = result.value || 'Operasional kantor & beban pekerjaan mampet.';
            form.appendChild(input);

            document.body.appendChild(form);
            form.submit();
        }
    });
}

function confirmDeleteCuti(id, nomor) {
    Swal.fire({
        title: 'Hapus Data Cuti?',
        text: 'Data permohonan "' + nomor + '" akan dihapus.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-4 shadow-lg' }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= base_url('direktur/karyawan/cuti/delete') ?>/' + id;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>

<?= view('direktur/templates/footer', $data) ?>
