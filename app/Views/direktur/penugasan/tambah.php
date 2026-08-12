<?= view('direktur/templates/header', $data) ?>
<?= view('direktur/templates/sidebar', $data) ?>
<?= view('direktur/templates/navbar', $data) ?>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= base_url('direktur') ?>" class="text-decoration-none text-muted">Direktur</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('direktur/penugasan') ?>" class="text-decoration-none text-muted">Penugasan Harian</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Buat Penugasan Baru</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-plus-circle text-primary me-2"></i> Form Buat Penugasan Harian</h4>
            <small class="text-muted">Isi rincian tugas dan delegasikan kepada karyawan yang memiliki akun aktif.</small>
        </div>
        <div>
            <a href="<?= base_url('direktur/penugasan') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card border-0 rounded-4 shadow-sm overflow-hidden mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom border-light">
            <h5 class="card-title fs-6 fw-bold mb-0 text-dark"><i class="fas fa-file-signature text-primary me-2"></i> Rincian Informasi Penugasan</h5>
        </div>
        <div class="card-body p-4">
            <form action="<?= base_url('direktur/penugasan/store') ?>" method="POST">
                <?= csrf_field() ?>
                
                <div class="row g-3 mb-4">
                    <!-- Judul Penugasan -->
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark text-sm">Judul Penugasan <span class="text-danger">*</span></label>
                        <input type="text" name="judul_tugas" class="form-control rounded-3" placeholder="Contoh: Penyiapan Laporan Keuangan Bulanan / Inspeksi Project" required>
                    </div>

                    <!-- Pilih Karyawan (Yang Ada Akun) -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark text-sm">Penerima Tugas / Karyawan <span class="text-danger">*</span></label>
                        <select name="karyawan_id" class="form-select rounded-3" required>
                            <option value="">-- Pilih Karyawan Berakun Active --</option>
                            <?php foreach($data['karyawanList'] as $k): ?>
                                <option value="<?= $k['karyawan_id'] ?>">
                                    <?= esc($k['nama_lengkap']) ?> (NIK: <?= esc($k['nik']) ?>) - <?= esc($k['jabatan'] ?: strtoupper($k['role'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted text-xs">Hanya menampilkan karyawan yang telah memiliki akun login aktif.</small>
                    </div>

                    <!-- Prioritas -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark text-sm">Prioritas Penugasan <span class="text-danger">*</span></label>
                        <select name="prioritas" class="form-select rounded-3" required>
                            <option value="rendah">Rendah</option>
                            <option value="sedang" selected>Sedang</option>
                            <option value="tinggi">Tinggi</option>
                            <option value="mendesak">Mendesak</option>
                        </select>
                    </div>

                    <!-- Tanggal Penugasan (Default Hari Ini) -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark text-sm">Tanggal Penugasan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_tugas" class="form-control rounded-3" value="<?= $data['todayDate'] ?>" required>
                    </div>

                    <!-- Tenggat Jam -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark text-sm">Tenggat Jam (Waktu Pelaksanaan) <span class="text-danger">*</span></label>
                        <input type="time" name="tenggat_waktu" class="form-control rounded-3" value="17:00" required>
                    </div>

                    <!-- Catatan Tambahan -->
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark text-sm">Catatan Tambahan / Arahan Direktur</label>
                        <textarea name="deskripsi_tugas" class="form-control rounded-3" rows="3" placeholder="Masukkan instruksi khusus atau catatan tambahan untuk penerima tugas..."></textarea>
                    </div>
                </div>

                <hr class="my-4 text-secondary opacity-25">

                <!-- Dynamic Item Checklist Tugas -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold text-dark mb-0"><i class="fas fa-tasks text-primary me-2"></i> Rincian Item Tugas (Multi-Item Checklist)</h6>
                        <small class="text-muted">Tambahkan item tugas spesifik jika penugasan terdiri lebih dari 1 aktivitas.</small>
                    </div>
                    <button type="button" id="btnAddItem" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                        <i class="fas fa-plus me-1"></i> Tambah Item Tugas
                    </button>
                </div>

                <div id="itemListContainer">
                    <div class="card bg-light border p-3 rounded-3 mb-3 item-row">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-primary text-xs item-number">Item Tugas #1</span>
                            <button type="button" class="btn btn-link text-danger btn-sm p-0 btn-remove-item d-none"><i class="fas fa-times"></i> Hapus</button>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <input type="text" name="item_judul[]" class="form-control form-control-sm rounded-3" placeholder="Judul sub-item tugas..." required>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="item_deskripsi[]" class="form-control form-control-sm rounded-3" placeholder="Keterangan singkat item...">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="<?= base_url('direktur/penugasan') ?>" class="btn btn-secondary rounded-pill px-4 font-semibold">Batal</a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 font-semibold shadow-sm">
                        <i class="fas fa-paper-plane me-1.5"></i> Simpan & Kirim Penugasan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('itemListContainer');
    const btnAdd = document.getElementById('btnAddItem');

    function updateNumbers() {
        const rows = container.querySelectorAll('.item-row');
        rows.forEach((row, idx) => {
            row.querySelector('.item-number').textContent = `Item Tugas #${idx + 1}`;
            const btnRemove = row.querySelector('.btn-remove-item');
            if (rows.length > 1) {
                btnRemove.classList.remove('d-none');
            } else {
                btnRemove.classList.add('d-none');
            }
        });
    }

    btnAdd.addEventListener('click', function() {
        const firstRow = container.querySelector('.item-row');
        const newRow = firstRow.cloneNode(true);
        newRow.querySelectorAll('input').forEach(input => input.value = '');
        container.appendChild(newRow);
        updateNumbers();
    });

    container.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-item')) {
            const rows = container.querySelectorAll('.item-row');
            if (rows.length > 1) {
                e.target.closest('.item-row').remove();
                updateNumbers();
            }
        }
    });
});
</script>

<?= view('direktur/templates/footer', $data) ?>
