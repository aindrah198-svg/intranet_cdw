<?= view('direktur/templates/header', $data) ?>
<?= view('direktur/templates/sidebar', $data) ?>
<?= view('direktur/templates/navbar', $data) ?>

<?php $t = $data['task']; ?>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= base_url('direktur') ?>" class="text-decoration-none text-muted">Direktur</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('direktur/penugasan') ?>" class="text-decoration-none text-muted">Penugasan Harian</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Edit Penugasan</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-edit text-primary me-2"></i> Edit Penugasan Harian</h4>
            <small class="text-muted">Perbarui rincian instruksi, waktu pelaksanaan, atau daftar sub-item tugas.</small>
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
            <h5 class="card-title fs-6 fw-bold mb-0 text-dark"><i class="fas fa-file-signature text-primary me-2"></i> Edit Informasi Penugasan</h5>
        </div>
        <div class="card-body p-4">
            <form action="<?= base_url('direktur/penugasan/update/'.$t['id']) ?>" method="POST">
                <?= csrf_field() ?>
                
                <div class="row g-3 mb-4">
                    <!-- Judul Penugasan -->
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark text-sm">Judul Penugasan <span class="text-danger">*</span></label>
                        <input type="text" name="judul_tugas" class="form-control rounded-3" value="<?= esc($t['judul_tugas']) ?>" required>
                    </div>

                    <!-- Pilih Karyawan (Yang Ada Akun) -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark text-sm">Penerima Tugas / Karyawan <span class="text-danger">*</span></label>
                        <select name="karyawan_id" class="form-select rounded-3" required>
                            <option value="">-- Pilih Karyawan Berakun Active --</option>
                            <?php foreach($data['karyawanList'] as $k): ?>
                                <option value="<?= $k['karyawan_id'] ?>" <?= ($t['penerima_id'] == $k['karyawan_id']) ? 'selected' : '' ?>>
                                    <?= esc($k['nama_lengkap']) ?> (NIK: <?= esc($k['nik']) ?>) - <?= esc($k['jabatan'] ?: strtoupper($k['role'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Prioritas -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark text-sm">Prioritas Penugasan <span class="text-danger">*</span></label>
                        <select name="prioritas" class="form-select rounded-3" required>
                            <option value="rendah" <?= $t['prioritas']==='rendah'?'selected':'' ?>>Rendah</option>
                            <option value="sedang" <?= $t['prioritas']==='sedang'?'selected':'' ?>>Sedang</option>
                            <option value="tinggi" <?= $t['prioritas']==='tinggi'?'selected':'' ?>>Tinggi</option>
                            <option value="mendesak" <?= $t['prioritas']==='mendesak'?'selected':'' ?>>Mendesak</option>
                        </select>
                    </div>

                    <!-- Tanggal Penugasan -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark text-sm">Tanggal Penugasan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_tugas" class="form-control rounded-3" value="<?= esc($t['tanggal_tugas']) ?>" required>
                    </div>

                    <!-- Tenggat Jam -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark text-sm">Tenggat Jam (Waktu Pelaksanaan) <span class="text-danger">*</span></label>
                        <input type="time" name="tenggat_waktu" class="form-control rounded-3" value="<?= esc($t['tenggat_waktu'] ?: '17:00') ?>" required>
                    </div>

                    <!-- Catatan Tambahan -->
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark text-sm">Catatan Tambahan / Arahan Direktur</label>
                        <textarea name="deskripsi_tugas" class="form-control rounded-3" rows="3"><?= esc($t['deskripsi_tugas']) ?></textarea>
                    </div>
                </div>

                <hr class="my-4 text-secondary opacity-25">

                <!-- Dynamic Item Checklist Tugas -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold text-dark mb-0"><i class="fas fa-tasks text-primary me-2"></i> Rincian Item Tugas (Multi-Item Checklist)</h6>
                        <small class="text-muted">Kelola sub-item checklist tugas di bawah ini.</small>
                    </div>
                    <button type="button" id="btnAddItem" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                        <i class="fas fa-plus me-1"></i> Tambah Item Tugas
                    </button>
                </div>

                <div id="itemListContainer">
                    <?php if(!empty($t['items'])): ?>
                        <?php foreach($t['items'] as $idx => $item): ?>
                        <div class="card bg-light border p-3 rounded-3 mb-3 item-row">
                            <input type="hidden" name="item_id[]" value="<?= $item['id'] ?>">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-primary text-xs item-number">Item Tugas #<?= $idx + 1 ?></span>
                                <button type="button" class="btn btn-link text-danger btn-sm p-0 btn-remove-item"><i class="fas fa-times"></i> Hapus</button>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <input type="text" name="item_judul[]" class="form-control form-control-sm rounded-3" value="<?= esc($item['judul_item']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="item_deskripsi[]" class="form-control form-control-sm rounded-3" value="<?= esc($item['deskripsi_item']) ?>">
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="card bg-light border p-3 rounded-3 mb-3 item-row">
                            <input type="hidden" name="item_id[]" value="">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-primary text-xs item-number">Item Tugas #1</span>
                                <button type="button" class="btn btn-link text-danger btn-sm p-0 btn-remove-item d-none"><i class="fas fa-times"></i> Hapus</button>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <input type="text" name="item_judul[]" class="form-control form-control-sm rounded-3" placeholder="Judul sub-item..." required>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="item_deskripsi[]" class="form-control form-control-sm rounded-3" placeholder="Deskripsi sub-item...">
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="<?= base_url('direktur/penugasan') ?>" class="btn btn-secondary rounded-pill px-4 font-semibold">Batal</a>
                    <button type="submit" class="btn btn-warning text-white rounded-pill px-4 font-semibold shadow-sm">
                        <i class="fas fa-save me-1.5"></i> Perbarui Penugasan
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
        newRow.querySelectorAll('input').forEach(input => {
            if(input.type === 'hidden') input.value = '';
            else input.value = '';
        });
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

    updateNumbers();
});
</script>

<?= view('direktur/templates/footer', $data) ?>
