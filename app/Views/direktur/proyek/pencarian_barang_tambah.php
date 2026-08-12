<?php
$title = $title ?? 'Buat Penugasan Pencarian Barang Baru';
$templateData = [
    'title' => $title,
    'user'  => session()->get('user') ?? ['name' => 'Direktur', 'role' => 'direktur'],
    'active' => 'proyek'
];

echo view('direktur/templates/header', $templateData);
echo view('direktur/templates/sidebar', $templateData);
echo view('direktur/templates/navbar', $templateData);
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
                <h4 class="mb-0 fw-bold text-dark">Buat Penugasan Pencarian Barang Baru</h4>
                <small class="text-muted">Delegasikan tugas pencarian harga barang atau estimasi RAB ke karyawan</small>
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

    <form action="<?= base_url('direktur/proyek/pencarian-barang/simpan') ?>" method="POST">
        <div class="row g-4">
            <!-- Kolom Utama Form (Kiri) -->
            <div class="col-12 col-lg-8">
                <div class="card glass-card-form p-4">
                    <div class="form-section-title">
                        <i class="fas fa-info-circle me-1.5 text-primary"></i> Informasi Penugasan & Spesifikasi
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark text-sm">Judul Penugasan / Nama Pencarian RAB <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg rounded-3 fs-6" name="judul" required placeholder="Contoh: Cari harga Kabel Fiber Optik 100m & Switch Hub 24 Port">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark text-sm">Deskripsi & Spesifikasi Detail Barang <span class="text-danger">*</span></label>
                        <textarea class="form-control rounded-3" name="deskripsi" rows="5" required placeholder="Tuliskan spesifikasi teknis, merk yang diinginkan, batas harga max, atau ketentuan pencarian lainnya..."></textarea>
                    </div>

                    <div class="form-section-title mt-4">
                        <i class="fas fa-clock me-1.5 text-warning"></i> Durasi Pengerjaan & Waktu Deadline
                    </div>

                    <!-- Pilihan Durasi Pengerjaan & Waktu -->
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark"><i class="fas fa-hourglass-half text-primary me-1"></i> Pilih Durasi Pengerjaan *</label>
                                <select class="form-select form-select-lg rounded-3 fw-bold text-primary fs-6" id="selectDurasiTambah" onchange="updateDurasiPencarian()">
                                    <option value="1">1 Hari (Selesai Hari Ini Jam 17:00 WIB)</option>
                                    <option value="2" selected>2 Hari (Default - Besok Lusa Jam 17:00 WIB)</option>
                                    <option value="3">3 Hari (Selesai 3 Hari Lagi Jam 17:00 WIB)</option>
                                    <option value="5">5 Hari (Selesai 5 Hari Lagi Jam 17:00 WIB)</option>
                                    <option value="7">7 Hari (1 Minggu - Jam 17:00 WIB)</option>
                                    <option value="custom">Custom (Atur Tanggal & Jam Sendiri)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Tanggal & Jam Mulai Penugasan *</label>
                                <div class="input-group">
                                    <input type="date" class="form-control form-control-lg rounded-start-3 fs-6" id="tglMulaiTambah" name="tanggal_mulai" required value="<?= date('Y-m-d') ?>" onchange="updateDurasiPencarian()">
                                    <input type="time" class="form-control form-control-lg rounded-end-3 fs-6" id="jamMulaiTambah" name="jam_mulai" value="08:00" onchange="updateDurasiPencarian()">
                                </div>
                            </div>
                            <div class="col-md-6 offset-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Tanggal & Jam Deadline Akhir *</label>
                                <div class="input-group">
                                    <input type="date" class="form-control form-control-lg rounded-start-3 fs-6" id="tglDeadlineTambah" name="batas_waktu" required value="<?= date('Y-m-d', strtotime('+2 days')) ?>" onchange="checkCustomDurasi()">
                                    <input type="time" class="form-control form-control-lg rounded-end-3 fs-6" id="jamDeadlineTambah" name="jam_deadline" value="17:00" onchange="updateDurasiPencarian()">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Live Banner Preview Ringkasan Durasi -->
                        <div class="alert alert-info py-2.5 px-3 mb-0 rounded-3 text-xs d-flex align-items-center justify-content-between flex-wrap gap-2 shadow-sm" id="bannerDurasiTambah">
                            <div>
                                <i class="fas fa-info-circle me-1.5 fs-6"></i>
                                <span class="fw-bold">Ringkasan Rentang Waktu:</span>
                                <span id="textDurasiTambah">Mulai <?= date('d M Y') ?> (08:00 WIB) ➔ Deadline <?= date('d M Y', strtotime('+2 days')) ?> (17:00 WIB)</span>
                            </div>
                            <span class="badge bg-primary rounded-pill px-3 py-1.5 text-xs fw-bold" id="badgeTotalDurasiTambah">Total Durasi: 2 Hari</span>
                        </div>
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
                        <label class="form-label fw-bold text-dark text-sm">Tugaskan Kepada Karyawan <span class="text-danger">*</span></label>
                        <select class="form-select form-select-lg rounded-3 fs-6" name="karyawan_id" required>
                            <option value="">-- Pilih Karyawan --</option>
                            <?php foreach($karyawan as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= esc($k['nama_lengkap']) ?> (<?= esc($k['jabatan']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark text-sm">Tipe Tempat Pembelian</label>
                        <select class="form-select form-select-lg rounded-3 fs-6" name="tipe_pembelian">
                            <option value="online_marketplace" selected>Online Store / Marketplace</option>
                            <option value="offline">Offline Store (Toko Fisik)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark text-sm">Nama Toko / Marketplace</label>
                        <input type="text" class="form-control form-control-lg rounded-3 fs-6" name="nama_toko_marketplace" list="tokoListTambah" placeholder="Tokopedia, Shopee, Glodok, dll">
                        <datalist id="tokoListTambah">
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
                            <input type="text" class="form-control form-control-lg rounded-end-3 fs-6 rupiah-input" name="nominal_estimasi" placeholder="Kosongkan jika belum tahu harga">
                        </div>
                        <small class="text-muted d-block mt-1"><i class="fas fa-info-circle me-1"></i> Kosongkan jika tugas ini untuk mencari tahu estimasi harganya.</small>
                    </div>

                    <hr class="my-4">

                    <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100 fw-bold shadow-sm py-3">
                        <i class="fas fa-paper-plane me-2"></i> Simpan & Tugaskan Sekarang
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

        updateDurasiPencarian();
    });

    function formatDateIndo(dateStr) {
        if (!dateStr) return '';
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        const options = { day: '2-digit', month: 'short', year: 'numeric' };
        return d.toLocaleDateString('id-ID', options);
    }

    function updateDurasiPencarian() {
        const select = document.getElementById('selectDurasiTambah');
        const tglMulai = document.getElementById('tglMulaiTambah');
        const jamMulai = document.getElementById('jamMulaiTambah');
        const tglDeadline = document.getElementById('tglDeadlineTambah');
        const jamDeadline = document.getElementById('jamDeadlineTambah');
        const textDurasi = document.getElementById('textDurasiTambah');
        const badgeDurasi = document.getElementById('badgeTotalDurasiTambah');

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

    function checkCustomDurasi() {
        const select = document.getElementById('selectDurasiTambah');
        if (select) select.value = 'custom';
        updateDurasiPencarian();
    }
</script>

<?= view('direktur/templates/footer', $templateData ?? []) ?>
