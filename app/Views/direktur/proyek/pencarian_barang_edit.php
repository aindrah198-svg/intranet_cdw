<?php
$title = $title ?? 'Edit Penugasan: ' . esc($p['judul']);
$templateData = [
    'title' => $title,
    'user'  => session()->get('user') ?? ['name' => 'Direktur', 'role' => 'direktur'],
    'active' => 'proyek'
];

echo view('direktur/templates/header', $templateData);
echo view('direktur/templates/sidebar', $templateData);
echo view('direktur/templates/navbar', $templateData);
?>

<?php
    $tglMulai = !empty($p['tanggal_mulai']) ? $p['tanggal_mulai'] : date('Y-m-d', strtotime($p['created_at'] ?? 'now'));
    $jamMulai = !empty($p['jam_mulai']) ? $p['jam_mulai'] : '08:00';
    $tglDeadline = !empty($p['batas_waktu']) ? $p['batas_waktu'] : date('Y-m-d', strtotime($tglMulai . ' +2 days'));
    $jamDeadline = !empty($p['jam_deadline']) ? $p['jam_deadline'] : '17:00';

    $tsMulai = strtotime($tglMulai . ' ' . $jamMulai);
    $tsDeadline = strtotime($tglDeadline . ' ' . $jamDeadline);
    $diffDays = ceil(($tsDeadline - strtotime($tglMulai)) / 86400);

    $isOffline = (strtolower($p['tipe_pembelian'] ?? '') === 'offline');
    $namaToko = !empty($p['nama_toko_marketplace']) ? $p['nama_toko_marketplace'] : ($isOffline ? 'Toko Fisik / Supplier' : 'Tokopedia / Shopee');
?>

<style>
    .glass-card-form {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
    }
    .form-section-title {
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #475569;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e2e8f0;
    }
</style>

<div class="container-fluid py-3 py-md-4 fade-in pb-5">
    <!-- Header Page dengan Tombol Kembali -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light">
        <div class="d-flex align-items-center">
            <a href="<?= base_url('direktur/proyek/pencarian-barang') ?>" class="btn btn-outline-secondary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;" title="Kembali ke Daftar">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h4 class="mb-0 fw-bold text-dark">Edit Penugasan Pencarian Barang</h4>
                <small class="text-muted">Perbarui parameter penugasan, tenggat waktu, atau status penugasan</small>
            </div>
        </div>
        <a href="<?= base_url('direktur/proyek/pencarian-barang') ?>" class="btn btn-secondary rounded-pill px-4 shadow-sm text-sm fw-semibold">
            <i class="fas fa-times me-1.5"></i> Batal
        </a>
    </div>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('direktur/proyek/pencarian-barang/update') ?>" method="POST">
        <input type="hidden" name="id" value="<?= $p['id'] ?>">

        <div class="row g-4">
            <!-- Kolom Utama Form (Kiri) -->
            <div class="col-12 col-lg-8">
                <div class="card glass-card-form p-4">
                    <div class="form-section-title">
                        <i class="fas fa-info-circle me-1.5 text-primary"></i> Informasi Penugasan & Spesifikasi
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark text-sm">Judul Penugasan / Nama Pencarian RAB <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg rounded-3 fs-6" name="judul" value="<?= esc($p['judul']) ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark text-sm">Deskripsi & Spesifikasi Detail Barang <span class="text-danger">*</span></label>
                        <textarea class="form-control rounded-3" name="deskripsi" rows="5" required><?= esc($p['deskripsi']) ?></textarea>
                    </div>

                    <div class="form-section-title mt-4">
                        <i class="fas fa-clock me-1.5 text-warning"></i> Durasi Pengerjaan & Waktu Deadline
                    </div>

                    <!-- Pilihan Durasi Pengerjaan & Waktu -->
                    <div class="p-3 bg-light rounded-3 border mb-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark"><i class="fas fa-hourglass-half text-primary me-1"></i> Pilih Durasi Pengerjaan</label>
                                <select class="form-select form-select-lg rounded-3 fw-bold text-primary fs-6" id="selectDurasiEdit" onchange="updateDurasiPencarianEdit()">
                                    <option value="1" <?= $diffDays == 1 ? 'selected' : '' ?>>1 Hari (Selesai Hari Ini Jam 17:00 WIB)</option>
                                    <option value="2" <?= $diffDays == 2 ? 'selected' : '' ?>>2 Hari (Default - Besok Lusa Jam 17:00 WIB)</option>
                                    <option value="3" <?= $diffDays == 3 ? 'selected' : '' ?>>3 Hari (Selesai 3 Hari Lagi Jam 17:00 WIB)</option>
                                    <option value="5" <?= $diffDays == 5 ? 'selected' : '' ?>>5 Hari (Selesai 5 Hari Lagi Jam 17:00 WIB)</option>
                                    <option value="7" <?= $diffDays == 7 ? 'selected' : '' ?>>7 Hari (1 Minggu - Jam 17:00 WIB)</option>
                                    <option value="custom" <?= (!in_array($diffDays, [1,2,3,5,7])) ? 'selected' : '' ?>>Custom (Atur Tanggal & Jam Sendiri)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Tanggal & Jam Mulai Penugasan *</label>
                                <div class="input-group">
                                    <input type="date" class="form-control form-control-lg rounded-start-3 fs-6" id="tglMulaiEdit" name="tanggal_mulai" value="<?= esc($tglMulai) ?>" required onchange="updateDurasiPencarianEdit()">
                                    <input type="time" class="form-control form-control-lg rounded-end-3 fs-6" id="jamMulaiEdit" name="jam_mulai" value="<?= esc($jamMulai) ?>" onchange="updateDurasiPencarianEdit()">
                                </div>
                            </div>
                            <div class="col-md-6 offset-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Tanggal & Jam Deadline Akhir *</label>
                                <div class="input-group">
                                    <input type="date" class="form-control form-control-lg rounded-start-3 fs-6" id="tglDeadlineEdit" name="batas_waktu" value="<?= esc($tglDeadline) ?>" required onchange="checkCustomDurasiEdit()">
                                    <input type="time" class="form-control form-control-lg rounded-end-3 fs-6" id="jamDeadlineEdit" name="jam_deadline" value="<?= esc($jamDeadline) ?>" onchange="updateDurasiPencarianEdit()">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Live Banner Preview Ringkasan Durasi -->
                        <div class="alert alert-info py-2.5 px-3 mb-0 rounded-3 text-xs d-flex align-items-center justify-content-between flex-wrap gap-2 shadow-sm" id="bannerDurasiEdit">
                            <div>
                                <i class="fas fa-info-circle me-1.5 fs-6"></i>
                                <span class="fw-bold">Ringkasan Rentang Waktu:</span>
                                <span id="textDurasiEdit">Mulai <?= date('d M Y', strtotime($tglMulai)) ?> (<?= $jamMulai ?> WIB) ➔ Deadline <?= date('d M Y', strtotime($tglDeadline)) ?> (<?= $jamDeadline ?> WIB)</span>
                            </div>
                            <span class="badge bg-primary rounded-pill px-3 py-1.5 text-xs fw-bold" id="badgeTotalDurasiEdit">Total Durasi: <?= $diffDays ?> Hari</span>
                        </div>
                    </div>

                    <div class="form-section-title mt-4">
                        <i class="fas fa-file-invoice-dollar me-1.5 text-success"></i> Hasil Laporan Harga / Tanggapan
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark text-sm">Hasil Pencarian Barang / Catatan Laporan Karyawan</label>
                        <textarea class="form-control rounded-3" name="hasil_pencarian" rows="4" placeholder="Isikan rincian harga barang, lokasi toko, kontak supplier, atau link barang..."><?= esc($p['hasil_pencarian']) ?></textarea>
                    </div>

                </div>
            </div>

            <!-- Kolom Samping Informasi Toko & Penugasan (Kanan) -->
            <div class="col-12 col-lg-4">
                <div class="card glass-card-form p-4 mb-4">
                    <div class="form-section-title">
                        <i class="fas fa-user-tie me-1.5 text-success"></i> Pelaksana & Tempat Pembelian
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark text-sm">Status Penugasan <span class="text-danger">*</span></label>
                        <select class="form-select form-select-lg rounded-3 fs-6" name="status" required>
                            <option value="baru" <?= $p['status'] === 'baru' ? 'selected' : '' ?>>Baru</option>
                            <option value="proses" <?= $p['status'] === 'proses' ? 'selected' : '' ?>>Diproses</option>
                            <option value="selesai" <?= $p['status'] === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                            <option value="batal" <?= $p['status'] === 'batal' ? 'selected' : '' ?>>Dibatalkan</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark text-sm">Tugaskan Kepada Karyawan <span class="text-danger">*</span></label>
                        <select class="form-select form-select-lg rounded-3 fs-6" name="karyawan_id" required>
                            <?php foreach($karyawan as $k): ?>
                                <option value="<?= $k['id'] ?>" <?= $k['id'] == $p['karyawan_id'] ? 'selected' : '' ?>>
                                    <?= esc($k['nama_lengkap']) ?> (<?= esc($k['jabatan']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark text-sm">Tipe Tempat Pembelian</label>
                        <select class="form-select form-select-lg rounded-3 fs-6" name="tipe_pembelian">
                            <option value="online_marketplace" <?= strtolower($p['tipe_pembelian'] ?? '') === 'online_marketplace' ? 'selected' : '' ?>>Online Store / Marketplace</option>
                            <option value="offline" <?= strtolower($p['tipe_pembelian'] ?? '') === 'offline' ? 'selected' : '' ?>>Offline Store (Toko Fisik)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark text-sm">Nama Toko / Marketplace</label>
                        <input type="text" class="form-control form-control-lg rounded-3 fs-6" name="nama_toko_marketplace" list="tokoListEdit" value="<?= esc($namaToko) ?>" placeholder="Tokopedia, Shopee, Glodok, dll">
                        <datalist id="tokoListEdit">
                            <option value="Tokopedia"></option>
                            <option value="Shopee"></option>
                            <option value="Bukalapak"></option>
                            <option value="Blibli"></option>
                            <option value="Toko Fisik Glodok"></option>
                            <option value="Supplier Resmi"></option>
                        </datalist>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark text-sm">Estimasi Total RAB (Rp) <span class="badge bg-secondary text-white rounded-pill ms-1 text-xs font-normal">Opsional</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold text-muted">Rp</span>
                            <input type="text" class="form-control form-control-lg rounded-end-3 fs-6 rupiah-input" name="nominal_estimasi" value="<?= esc(($p['nominal_estimasi'] && $p['nominal_estimasi'] > 0) ? number_format($p['nominal_estimasi'],0,',','.') : '') ?>" placeholder="Kosongkan jika belum tahu harga">
                        </div>
                        <small class="text-muted d-block mt-1"><i class="fas fa-info-circle me-1"></i> Kosongkan jika tugas ini untuk mencari tahu estimasi harganya.</small>
                    </div>

                    <hr class="my-4">

                    <button type="submit" class="btn btn-warning btn-lg rounded-pill w-100 fw-bold shadow-sm py-3 text-white">
                        <i class="fas fa-save me-2"></i> Simpan Perubahan Penugasan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Live Rupiah Formatter
        document.querySelectorAll('.rupiah-input').forEach(function(input) {
            input.addEventListener('keyup', function(e) {
                let value = this.value.replace(/[^,\d]/g, '').toString();
                let split = value.split(',');
                let sisa = split[0].length % 3;
                let rupiah = split[0].substr(0, sisa);
                let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                this.value = rupiah ? rupiah : '';
            });
        });

        updateDurasiPencarianEdit();
    });

    function formatDateIndo(dateStr) {
        if (!dateStr) return '';
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        const options = { day: '2-digit', month: 'short', year: 'numeric' };
        return d.toLocaleDateString('id-ID', options);
    }

    function updateDurasiPencarianEdit() {
        const select = document.getElementById('selectDurasiEdit');
        const tglMulai = document.getElementById('tglMulaiEdit');
        const jamMulai = document.getElementById('jamMulaiEdit');
        const tglDeadline = document.getElementById('tglDeadlineEdit');
        const jamDeadline = document.getElementById('jamDeadlineEdit');
        const textDurasi = document.getElementById('textDurasiEdit');
        const badgeDurasi = document.getElementById('badgeTotalDurasiEdit');

        if (!select || !tglMulai || !tglDeadline) return;

        const val = select.value;
        const startDate = new Date(tglMulai.value || new Date());
        
        if (val !== 'custom') {
            const numDays = parseInt(val) || 2;
            const endDate = new Date(startDate);
            if (numDays > 1) {
                endDate.setDate(endDate.getDate() + numDays);
            }
            const yyyy = endDate.getFullYear();
            const mm = String(endDate.getMonth() + 1).padStart(2, '0');
            const dd = String(endDate.getDate()).padStart(2, '0');
            tglDeadline.value = `${yyyy}-${mm}-${dd}`;
            if (!jamDeadline.value) jamDeadline.value = '17:00';
        }

        const startD = new Date(tglMulai.value);
        const endD = new Date(tglDeadline.value);
        const diffTime = Math.abs(endD - startD);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) || 1;

        if (textDurasi) textDurasi.innerHTML = `Mulai <strong>${formatDateIndo(tglMulai.value)} (${jamMulai.value} WIB)</strong> ➔ Deadline <strong>${formatDateIndo(tglDeadline.value)} (${jamDeadline.value} WIB)</strong>`;
        if (badgeDurasi) badgeDurasi.innerText = `Total Durasi: ${diffDays} Hari`;
    }

    function checkCustomDurasiEdit() {
        const select = document.getElementById('selectDurasiEdit');
        if (select) select.value = 'custom';
        updateDurasiPencarianEdit();
    }
</script>

<?= view('direktur/templates/footer', $templateData ?? []) ?>
