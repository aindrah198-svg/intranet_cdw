<?php
$title = $title ?? 'Tambah Pencatatan Pembelian';
$templateData = [
    'title' => $title,
    'user'  => session()->get('user') ?? ['name' => 'Direktur', 'role' => 'direktur'],
    'active' => 'keuangan'
];
?>

<?= view('direktur/templates/header', $templateData) ?>
<?= view('direktur/templates/sidebar', $templateData) ?>
<?= view('direktur/templates/navbar', $templateData) ?>

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
        padding-left: 12px !important;
        padding-right: 12px !important;
        box-sizing: border-box !important;
    }
    .pr-form-card {
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 25px rgba(30, 60, 114, 0.06) !important;
    }
    .table-scroll-wrapper {
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
    }
    .table-scroll-wrapper table {
        min-width: 650px !important;
    }
</style>

<div class="container-fluid py-3 py-md-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light flex-wrap gap-3">
        <div class="d-flex align-items-center">
            <a href="<?= base_url('direktur/keuangan/pembelian') ?>" class="btn btn-outline-secondary rounded-pill me-3 px-3 py-1.5 text-xs fw-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Batal / Kembali
            </a>
            <div>
                <h4 class="mb-0 fw-bold text-dark fs-5 fs-md-4">Buat Pencatatan Pembelian (PR) Baru</h4>
                <small class="text-muted d-none d-sm-inline">Isi rincian pengajuan pembelian barang online (Tokopedia/Shopee/dll) atau toko fisik.</small>
            </div>
        </div>
    </div>

    <form action="<?= base_url('direktur/keuangan/pembelian/simpan') ?>" method="POST" enctype="multipart/form-data">
        <div class="card pr-form-card p-4 mb-4">
            <h6 class="fw-bold text-primary border-bottom pb-3 mb-3 d-flex align-items-center">
                <i class="fas fa-cart-plus me-2"></i> INFORMASI PEMOHON & TRACKING PEMBELIAN
            </h6>
            <div class="row g-3">
                <!-- Pemohon & Tipe Pembelian -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-sm text-dark">Pemohon (Karyawan) *</label>
                    <select name="karyawan_id" class="form-select rounded-3" required>
                        <option value="">-- Pilih Karyawan Pemohon --</option>
                        <?php foreach($karyawanList as $kar): ?>
                            <option value="<?= $kar['id'] ?>">
                                <?= esc($kar['nama_lengkap']) ?> (NIK: <?= esc($kar['nik']) ?> - <?= esc($kar['jabatan']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-sm text-dark">Tipe Pembelian *</label>
                    <select name="tipe_pembelian" class="form-select rounded-3" required>
                        <option value="Online">Online (Tokopedia, Shopee, Bukalapak, DLL)</option>
                        <option value="Offline">Offline (Toko Fisik / Direct Vendor)</option>
                    </select>
                </div>

                <!-- Platform & Metode Pembayaran -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-sm text-dark">Platform / Toko Beli</label>
                    <input type="text" name="platform_pembelian" class="form-control rounded-3" placeholder="Contoh: Tokopedia Official Store / Toko Jaya Abadi" value="Tokopedia">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-sm text-dark">Metode Pembayaran</label>
                    <select name="metode_pembayaran" class="form-select rounded-3">
                        <option value="Dibayar Langsung Tokopedia Direktur">Dibayar Langsung Tokopedia Direktur</option>
                        <option value="Transfer ke Karyawan">Transfer ke Karyawan</option>
                        <option value="Bayar QRIS">Bayar QRIS</option>
                        <option value="Transfer Direct Vendor">Transfer Direct Vendor / Bank</option>
                        <option value="Cash / Tunai">Cash / Tunai</option>
                    </select>
                </div>

                <!-- Link Produk & No Resi -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-sm text-dark">Link Produk Toko (Opsional)</label>
                    <input type="url" name="link_produk" class="form-control rounded-3" placeholder="https://tokopedia.link/...">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-sm text-dark">No. Resi / Order ID / Invoice (Opsional)</label>
                    <input type="text" name="no_resi_transaksi" class="form-control rounded-3" placeholder="Contoh: INV/2026/TKP/12938">
                </div>

                <!-- Tanggal & Prioritas -->
                <div class="col-6 col-md-4">
                    <label class="form-label fw-semibold text-sm text-dark">Tanggal Pengajuan</label>
                    <input type="date" name="tanggal_pengajuan" class="form-control rounded-3" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label fw-semibold text-sm text-dark">Tanggal Dibutuhkan</label>
                    <input type="date" name="tanggal_dibutuhkan" class="form-control rounded-3" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-sm text-dark">Prioritas</label>
                    <select name="prioritas" class="form-select rounded-3">
                        <option value="Normal" selected>Normal</option>
                        <option value="Urgent">Urgent</option>
                        <option value="Tinggi">Tinggi</option>
                        <option value="Rendah">Rendah</option>
                    </select>
                </div>

                <!-- Alasan Pembelian -->
                <div class="col-12">
                    <label class="form-label fw-semibold text-sm text-dark">Alasan / Tujuan Pembelian *</label>
                    <textarea name="alasan_pembelian" class="form-control rounded-3" rows="3" placeholder="Jelaskan kebutuhan barang / peralatan yang dibeli..." required></textarea>
                </div>
            </div>
        </div>

        <!-- Dynamic Items Table Card -->
        <div class="card pr-form-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center">
                    <i class="fas fa-boxes text-primary me-2"></i> RINCIAN DAFTAR BARANG YANG DIBELI
                </h6>
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill font-weight-bold px-3" id="btnAddItemPage">
                    <i class="fas fa-plus me-1"></i> Tambah Baris Barang
                </button>
            </div>
            <div class="table-scroll-wrapper">
                <table class="table table-bordered align-middle text-sm mb-0 bg-white" id="tableItemsPage">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Barang *</th>
                            <th>Spesifikasi</th>
                            <th width="110">Jumlah</th>
                            <th width="100">Satuan</th>
                            <th width="180">Harga Satuan (Rp)</th>
                            <th width="50" class="text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" name="item_nama[]" class="form-control form-control-sm" placeholder="Nama barang..." required></td>
                            <td><input type="text" name="item_spesifikasi[]" class="form-control form-control-sm" placeholder="Warna, tipe, merk..."></td>
                            <td><input type="number" name="item_jumlah[]" class="form-control form-control-sm item-qty" value="1" min="1" required></td>
                            <td><input type="text" name="item_satuan[]" class="form-control form-control-sm" value="Pcs"></td>
                            <td><input type="text" name="item_harga[]" class="form-control form-control-sm input-rupiah item-harga" placeholder="0"></td>
                            <td class="text-center"><button type="button" class="btn btn-link text-danger btn-sm p-0 btn-remove-item"><i class="fas fa-trash"></i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Status & Upload Bukti Lampiran Card -->
        <div class="card pr-form-card p-4 mb-4">
            <h6 class="fw-bold text-dark border-bottom pb-3 mb-3 d-flex align-items-center">
                <i class="fas fa-paperclip text-primary me-2"></i> STATUS & UPLOAD BUKTI LAMPIRAN
            </h6>
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-sm text-dark">Status Pembayaran</label>
                    <select name="status_pembayaran" class="form-select rounded-3">
                        <option value="Belum Dibayar" selected>Belum Dibayar</option>
                        <option value="Dibayar / Lunas">Dibayar / Lunas</option>
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-sm text-dark">Status Penerimaan Barang</label>
                    <select name="status_penerimaan" class="form-select rounded-3">
                        <option value="Belum" selected>Belum Dibeli</option>
                        <option value="Dipesan">Dipesan (Dalam Pengiriman)</option>
                        <option value="Diterima Lengkap">Diterima Lengkap</option>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-sm text-dark">1. Bukti Invoice / Struk Beli</label>
                    <input type="file" name="bukti_pembelian" class="form-control rounded-3" accept="image/*">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-sm text-dark">2. Bukti Transfer / Bayar</label>
                    <input type="file" name="bukti_pembayaran" class="form-control rounded-3" accept="image/*">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-sm text-dark">3. Foto Fisik Barang Diterima</label>
                    <input type="file" name="bukti_barang" class="form-control rounded-3" accept="image/*">
                </div>
            </div>
            
            <div class="mt-4 pt-3 border-top text-end">
                <a href="<?= base_url('direktur/keuangan/pembelian') ?>" class="btn btn-light rounded-pill px-4 me-2 border">Batal</a>
                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                    <i class="fas fa-check me-1.5"></i> Simpan Pencatatan Pembelian
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnAdd = document.getElementById('btnAddItemPage');
        const tableBody = document.querySelector('#tableItemsPage tbody');

        if(btnAdd && tableBody) {
            btnAdd.addEventListener('click', function() {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><input type="text" name="item_nama[]" class="form-control form-control-sm" placeholder="Nama barang..." required></td>
                    <td><input type="text" name="item_spesifikasi[]" class="form-control form-control-sm" placeholder="Warna, tipe, merk..."></td>
                    <td><input type="number" name="item_jumlah[]" class="form-control form-control-sm item-qty" value="1" min="1" required></td>
                    <td><input type="text" name="item_satuan[]" class="form-control form-control-sm" value="Pcs"></td>
                    <td><input type="text" name="item_harga[]" class="form-control form-control-sm input-rupiah item-harga" placeholder="0"></td>
                    <td class="text-center"><button type="button" class="btn btn-link text-danger btn-sm p-0 btn-remove-item"><i class="fas fa-trash"></i></button></td>
                `;
                tableBody.appendChild(tr);
            });

            tableBody.addEventListener('click', function(e) {
                if (e.target.closest('.btn-remove-item')) {
                    if (tableBody.querySelectorAll('tr').length > 1) {
                        e.target.closest('tr').remove();
                    } else {
                        alert('Minimal harus ada 1 item barang.');
                    }
                }
            });
        }
    });
</script>

<?= view('direktur/templates/footer') ?>
