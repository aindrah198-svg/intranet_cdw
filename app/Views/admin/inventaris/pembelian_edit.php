<?php
$title = $title ?? 'Edit Pencatatan Pembelian (PR)';
$p = $p ?? $pr ?? [];
$templateData = [
    'title'  => $title,
    'user'   => session()->get('user') ?? ['name' => session()->get('name') ?? 'Administrator', 'role' => 'admin'],
    'active' => 'inventaris'
];
?>

<?= view('admin/templates/header', $templateData) ?>
<?= view('admin/templates/sidebar', $templateData) ?>
<?= view('admin/templates/navbar', $templateData) ?>

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
            <a href="<?= base_url('admin/inventaris/pembelian') ?>" class="btn btn-outline-secondary rounded-pill me-3 px-3 py-1.5 text-xs fw-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Batal / Kembali
            </a>
            <div>
                <h4 class="mb-0 fw-bold text-dark fs-5 fs-md-4">Edit Pencatatan Pembelian (PR)</h4>
                <small class="text-muted d-none d-sm-inline">Nomor PR: <strong class="text-primary"><?= esc($p['nomor_pr'] ?? '-') ?></strong></small>
            </div>
        </div>
    </div>

    <!-- Alert Flashdata Direct Output -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3 shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('admin/inventaris/pembelian/update') ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= esc($p['id'] ?? '') ?>">

        <div class="card pr-form-card p-4 mb-4">
            <h6 class="fw-bold text-warning border-bottom pb-3 mb-3 d-flex align-items-center">
                <i class="fas fa-edit me-2"></i> INFORMASI PEMOHON & TRACKING PEMBELIAN
            </h6>
            <div class="row g-3">
                <!-- Pemohon & Tipe Pembelian -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-sm text-dark">Pemohon (Karyawan) *</label>
                    <select name="karyawan_id" class="form-select rounded-3" required>
                        <option value="">-- Pilih Karyawan Pemohon --</option>
                        <?php if (!empty($karyawanList)): ?>
                            <?php foreach($karyawanList as $kar): ?>
                                <option value="<?= $kar['id'] ?>" <?= (($p['karyawan_id'] ?? '') == $kar['id']) ? 'selected' : '' ?>>
                                    <?= esc($kar['nama_lengkap']) ?> (NIK: <?= esc($kar['nik']) ?> - <?= esc($kar['jabatan']) ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-sm text-dark">Tipe Pembelian *</label>
                    <select name="tipe_pembelian" class="form-select rounded-3" required>
                        <option value="Online" <?= (($p['tipe_pembelian'] ?? 'Online') === 'Online') ? 'selected' : '' ?>>Online (Tokopedia, Shopee, Bukalapak, DLL)</option>
                        <option value="Offline" <?= (($p['tipe_pembelian'] ?? '') === 'Offline') ? 'selected' : '' ?>>Offline (Toko Fisik / Direct Vendor)</option>
                    </select>
                </div>

                <!-- Platform & Metode Pembayaran -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-sm text-dark">Platform / Toko Beli</label>
                    <input type="text" name="platform_pembelian" class="form-control rounded-3" value="<?= esc($p['platform_pembelian'] ?? 'Tokopedia') ?>">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-sm text-dark">Metode Pembayaran</label>
                    <select name="metode_pembayaran" class="form-select rounded-3">
                        <?php $met = $p['metode_pembayaran'] ?? ''; ?>
                        <option value="Dibayar Langsung Tokopedia Direktur" <?= ($met === 'Dibayar Langsung Tokopedia Direktur') ? 'selected' : '' ?>>Dibayar Langsung Tokopedia Direktur</option>
                        <option value="Transfer ke Karyawan" <?= ($met === 'Transfer ke Karyawan') ? 'selected' : '' ?>>Transfer ke Karyawan</option>
                        <option value="Bayar QRIS" <?= ($met === 'Bayar QRIS') ? 'selected' : '' ?>>Bayar QRIS</option>
                        <option value="Transfer Direct Vendor" <?= ($met === 'Transfer Direct Vendor') ? 'selected' : '' ?>>Transfer Direct Vendor / Bank</option>
                        <option value="Cash / Tunai" <?= ($met === 'Cash / Tunai') ? 'selected' : '' ?>>Cash / Tunai</option>
                    </select>
                </div>

                <!-- Link Produk & No Resi -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-sm text-dark">Link Produk Toko (Opsional)</label>
                    <input type="url" name="link_produk" class="form-control rounded-3" value="<?= esc($p['link_produk'] ?? '') ?>">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-sm text-dark">No. Resi / Order ID / Invoice (Opsional)</label>
                    <input type="text" name="no_resi_transaksi" class="form-control rounded-3" value="<?= esc($p['no_resi_transaksi'] ?? '') ?>">
                </div>

                <!-- Tanggal & Prioritas -->
                <div class="col-6 col-md-4">
                    <label class="form-label fw-semibold text-sm text-dark">Tanggal Pengajuan</label>
                    <input type="date" name="tanggal_pengajuan" class="form-control rounded-3" value="<?= !empty($p['tanggal_pengajuan']) ? date('Y-m-d', strtotime($p['tanggal_pengajuan'])) : date('Y-m-d') ?>">
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label fw-semibold text-sm text-dark">Tanggal Dibutuhkan</label>
                    <input type="date" name="tanggal_dibutuhkan" class="form-control rounded-3" value="<?= !empty($p['tanggal_dibutuhkan']) ? date('Y-m-d', strtotime($p['tanggal_dibutuhkan'])) : date('Y-m-d') ?>">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold text-sm text-dark">Prioritas</label>
                    <?php $prio = $p['prioritas'] ?? 'Normal'; ?>
                    <select name="prioritas" class="form-select rounded-3">
                        <option value="Normal" <?= ($prio === 'Normal') ? 'selected' : '' ?>>Normal</option>
                        <option value="Urgent" <?= ($prio === 'Urgent') ? 'selected' : '' ?>>Urgent</option>
                        <option value="Tinggi" <?= ($prio === 'Tinggi') ? 'selected' : '' ?>>Tinggi</option>
                        <option value="Rendah" <?= ($prio === 'Rendah') ? 'selected' : '' ?>>Rendah</option>
                    </select>
                </div>

                <!-- Alasan Pembelian -->
                <div class="col-12">
                    <label class="form-label fw-semibold text-sm text-dark">Alasan / Tujuan Pembelian *</label>
                    <textarea name="alasan_pembelian" class="form-control rounded-3" rows="3" required><?= esc($p['alasan_pembelian'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Dynamic Items Table Card -->
        <div class="card pr-form-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center">
                    <i class="fas fa-boxes text-primary me-2"></i> RINCIAN DAFTAR BARANG YANG DIBELI
                </h6>
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill font-weight-bold px-3" id="btnAddItemPageEdit">
                    <i class="fas fa-plus me-1"></i> Tambah Baris Barang
                </button>
            </div>
            <div class="table-scroll-wrapper">
                <table class="table table-bordered align-middle text-sm mb-0 bg-white" id="tableItemsPageEdit">
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
                        <?php if(empty($p['items'])): ?>
                            <tr>
                                <td><input type="text" name="item_nama[]" class="form-control form-control-sm" placeholder="Nama barang..." required></td>
                                <td><input type="text" name="item_spesifikasi[]" class="form-control form-control-sm" placeholder="Warna, tipe, merk..."></td>
                                <td><input type="number" name="item_jumlah[]" class="form-control form-control-sm item-qty" value="1" min="1" required></td>
                                <td><input type="text" name="item_satuan[]" class="form-control form-control-sm" value="Pcs"></td>
                                <td><input type="text" name="item_harga[]" class="form-control form-control-sm input-rupiah item-harga" placeholder="0"></td>
                                <td class="text-center"><button type="button" class="btn btn-link text-danger btn-sm p-0 btn-remove-item"><i class="fas fa-trash"></i></button></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($p['items'] as $item): ?>
                                <?php
                                    $itemQty = $item['jumlah'] ?? $item['qty'] ?? 1;
                                    $itemHarga = floatval($item['harga_estimasi'] ?? $item['harga_satuan'] ?? 0);
                                ?>
                                <tr>
                                    <td><input type="text" name="item_nama[]" class="form-control form-control-sm" value="<?= esc($item['nama_barang']) ?>" required></td>
                                    <td><input type="text" name="item_spesifikasi[]" class="form-control form-control-sm" value="<?= esc($item['spesifikasi'] ?? '') ?>"></td>
                                    <td><input type="number" name="item_jumlah[]" class="form-control form-control-sm item-qty" value="<?= $itemQty ?>" min="1" required></td>
                                    <td><input type="text" name="item_satuan[]" class="form-control form-control-sm" value="<?= esc($item['satuan'] ?? 'Pcs') ?>"></td>
                                    <td><input type="text" name="item_harga[]" class="form-control form-control-sm input-rupiah item-harga" value="<?= number_format($itemHarga, 0, ',', '.') ?>"></td>
                                    <td class="text-center"><button type="button" class="btn btn-link text-danger btn-sm p-0 btn-remove-item"><i class="fas fa-trash"></i></button></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Status & Upload Bukti Lampiran Card -->
        <div class="card pr-form-card p-4 mb-4">
            <h6 class="fw-bold text-dark border-bottom pb-3 mb-3 d-flex align-items-center">
                <i class="fas fa-paperclip text-primary me-2"></i> STATUS & UPLOAD BUKTI LAMPIRAN
            </h6>

            <div class="alert alert-primary py-2.5 px-3 mb-3 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <i class="fas fa-database me-2"></i> Status Penerimaan di Database saat ini:
                </div>
                <span class="badge bg-dark text-white fs-6 px-3 py-1.5 rounded-pill"><?= esc($p['status_penerimaan'] ?? 'Belum Dibeli') ?></span>
            </div>

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-sm text-dark">Status Persetujuan Direktur</label>
                    <select name="status_direktur" class="form-select rounded-3">
                        <option value="Disetujui" <?= (strtolower($p['status_direktur'] ?? '') === 'disetujui') ? 'selected' : '' ?>>Disetujui</option>
                        <option value="Menunggu" <?= (strtolower($p['status_direktur'] ?? '') === 'menunggu') ? 'selected' : '' ?>>Menunggu</option>
                        <option value="Ditolak" <?= (strtolower($p['status_direktur'] ?? '') === 'ditolak') ? 'selected' : '' ?>>Ditolak</option>
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-sm text-dark">Status Pembayaran</label>
                    <?php 
                    $stB = strtolower(trim($p['status_pembayaran'] ?? '')); 
                    $isPaidVal = ($stB === 'dibayar / lunas' || $stB === 'lunas' || $stB === 'dibayar' || $stB === 'sudah dibayar');
                    ?>
                    <select name="status_pembayaran" id="selectStatusPembayaran" class="form-select rounded-3">
                        <option value="Belum Dibayar" <?= (!$isPaidVal) ? 'selected' : '' ?>>Belum Dibayar</option>
                        <option value="Dibayar / Lunas" <?= ($isPaidVal) ? 'selected' : '' ?>>Dibayar / Lunas</option>
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold text-sm text-dark">Status Penerimaan Barang</label>
                    <?php 
                    $stT = strtolower(trim($p['status_penerimaan'] ?? '')); 
                    $isRecVal = (strpos($stT, 'terima') !== false || strpos($stT, 'lengkap') !== false || strpos($stT, 'selesai') !== false);
                    $isOrdVal = (!$isRecVal && (strpos($stT, 'pesan') !== false || strpos($stT, 'proses') !== false || strpos($stT, 'kirim') !== false));
                    ?>
                    <select name="status_penerimaan" id="selectStatusPenerimaan" class="form-select rounded-3 fw-bold border-primary">
                        <option value="Belum Dibeli" <?= (!$isRecVal && !$isOrdVal) ? 'selected' : '' ?>>Belum Dibeli</option>
                        <option value="Dipesan" <?= ($isOrdVal) ? 'selected' : '' ?>>Dipesan (Dalam Pengiriman)</option>
                        <option value="Diterima Lengkap" <?= ($isRecVal) ? 'selected' : '' ?>>Diterima Lengkap</option>
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-sm text-dark">Status Keseluruhan (PR)</label>
                    <?php 
                    $stO = strtolower(trim($p['status_keseluruhan'] ?? '')); 
                    $isSelVal = ($stO === 'selesai' || $isRecVal);
                    $isMenVal = ($stO === 'menunggu');
                    $isTolVal = ($stO === 'ditolak');
                    $isDipVal = (!$isSelVal && !$isMenVal && !$isTolVal);
                    ?>
                    <select name="status_keseluruhan" id="selectStatusKeseluruhan" class="form-select rounded-3">
                        <option value="Dipesan" <?= ($isDipVal) ? 'selected' : '' ?>>Dipesan (Dalam Proses)</option>
                        <option value="Selesai" <?= ($isSelVal) ? 'selected' : '' ?>>Selesai (Penerimaan & Pembayaran)</option>
                        <option value="Menunggu" <?= ($isMenVal) ? 'selected' : '' ?>>Menunggu Approval</option>
                        <option value="Ditolak" <?= ($isTolVal) ? 'selected' : '' ?>>Ditolak</option>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-sm text-dark">1. Bukti Invoice / Struk Beli</label>
                    <input type="file" name="bukti_pembelian" class="form-control rounded-3" accept="image/*">
                    <?php if(!empty($p['bukti_pembelian'])): ?>
                        <small class="text-success d-block mt-1"><i class="fas fa-check-circle me-1"></i> File ter-upload: <?= esc(basename($p['bukti_pembelian'])) ?></small>
                    <?php endif; ?>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-sm text-dark">2. Bukti Transfer / Bayar</label>
                    <input type="file" name="bukti_pembayaran" class="form-control rounded-3" accept="image/*">
                    <?php if(!empty($p['bukti_pembayaran'])): ?>
                        <small class="text-success d-block mt-1"><i class="fas fa-check-circle me-1"></i> File ter-upload: <?= esc(basename($p['bukti_pembayaran'])) ?></small>
                    <?php endif; ?>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-sm text-dark">3. Foto Fisik Barang Diterima</label>
                    <input type="file" name="bukti_barang" class="form-control rounded-3" accept="image/*">
                    <?php if(!empty($p['bukti_barang'])): ?>
                        <small class="text-success d-block mt-1"><i class="fas fa-check-circle me-1"></i> File ter-upload: <?= esc(basename($p['bukti_barang'])) ?></small>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="mt-4 pt-3 border-top text-end">
                <a href="<?= base_url('admin/inventaris/pembelian') ?>" class="btn btn-light rounded-pill px-4 me-2 border">Batal</a>
                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                    <i class="fas fa-save me-1.5"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function formatRupiahString(angka) {
        let number_string = angka.replace(/[^,\d]/g, '').toString(),
            split   = number_string.split(','),
            sisa    = split[0].length % 3,
            rupiah  = split[0].substr(0, sisa),
            ribuan  = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return rupiah;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const selectPenerimaan = document.getElementById('selectStatusPenerimaan');
        const selectKeseluruhan = document.getElementById('selectStatusKeseluruhan');
        const selectPembayaran = document.getElementById('selectStatusPembayaran');

        if (selectPenerimaan && selectKeseluruhan) {
            selectPenerimaan.addEventListener('change', function() {
                if (this.value === 'Diterima Lengkap') {
                    selectKeseluruhan.value = 'Selesai';
                    if (selectPembayaran) selectPembayaran.value = 'Dibayar / Lunas';
                }
            });

            selectKeseluruhan.addEventListener('change', function() {
                if (this.value === 'Selesai') {
                    if (selectPenerimaan) selectPenerimaan.value = 'Diterima Lengkap';
                    if (selectPembayaran) selectPembayaran.value = 'Dibayar / Lunas';
                }
            });
        }

        document.addEventListener('input', function(e) {
            if (e.target && e.target.classList.contains('input-rupiah')) {
                let cursorPosition = e.target.selectionStart;
                let originalLength = e.target.value.length;
                e.target.value = formatRupiahString(e.target.value);
                let newLength = e.target.value.length;
                cursorPosition = cursorPosition + (newLength - originalLength);
                e.target.setSelectionRange(cursorPosition, cursorPosition);
            }
        });

        const btnAdd = document.getElementById('btnAddItemPageEdit');
        const tableBody = document.querySelector('#tableItemsPageEdit tbody');

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

<?= view('admin/templates/footer', $templateData) ?>
