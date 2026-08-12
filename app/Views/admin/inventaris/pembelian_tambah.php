<?php
$title = $title ?? 'Tambah Pencatatan Pembelian (PR)';
$templateData = [
    'title'  => $title,
    'user'   => session()->get('user') ?? ['name' => session()->get('name') ?? 'Administrator', 'role' => 'admin'],
    'active' => 'inventaris'
];
?>

<?= view('admin/templates/header', $templateData) ?>
<?= view('admin/templates/sidebar', $templateData) ?>
<?= view('admin/templates/navbar', $templateData) ?>

<div class="container-fluid py-3 py-md-4">
    <div class="d-flex justify-content-between align-items-center bg-white rounded-3 shadow-sm p-3 mb-4 border border-light flex-wrap gap-3">
        <div class="d-flex align-items-center">
            <a href="<?= base_url('admin/inventaris/pembelian') ?>" class="btn btn-outline-secondary rounded-pill me-3 px-3 py-1.5 text-xs fw-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali
            </a>
            <div>
                <h4 class="mb-0 fw-bold text-dark fs-5">Buat Pencatatan Purchase Requisition (PR) Baru</h4>
                <small class="text-muted">Isi formulir pengadaan barang dan rincian item yang dibutuhkan.</small>
            </div>
        </div>
    </div>

    <form action="<?= base_url('admin/inventaris/pembelian/simpan') ?>" method="POST">
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <h6 class="fw-bold text-dark mb-3"><i class="fas fa-info-circle text-primary me-2"></i> Informasi Utama Pengajuan PR</h6>
            
            <div class="row g-3 mb-3">
                <div class="col-12 col-md-6">
                    <label class="form-label text-xs fw-semibold text-dark">Karyawan Pemohon *</label>
                    <select name="karyawan_id" class="form-select rounded-3" required>
                        <?php foreach ($karyawanList as $k): ?>
                            <option value="<?= $k['id'] ?>"><?= esc($k['nama_lengkap']) ?> (<?= esc($k['departemen'] ?: $k['jabatan'] ?: 'Staf') ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label text-xs fw-semibold text-dark">Tanggal Pengajuan *</label>
                    <input type="date" name="tanggal_pengajuan" class="form-control rounded-3" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label text-xs fw-semibold text-dark">Tanggal Dibutuhkan *</label>
                    <input type="date" name="tanggal_dibutuhkan" class="form-control rounded-3" value="<?= date('Y-m-d', strtotime('+3 days')) ?>" required>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12 col-md-4">
                    <label class="form-label text-xs fw-semibold text-dark">Prioritas *</label>
                    <select name="prioritas" class="form-select rounded-3">
                        <option value="Normal">Normal</option>
                        <option value="Tinggi">Tinggi</option>
                        <option value="Urgent">Urgent</option>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label text-xs fw-semibold text-dark">Tipe Pembelian</label>
                    <select name="tipe_pembelian" class="form-select rounded-3">
                        <option value="Online">Online (E-Commerce / Tokopedia / Shopee)</option>
                        <option value="Offline">Offline (Supplier Toko Direct)</option>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label text-xs fw-semibold text-dark">Supplier / Toko</label>
                    <input type="text" name="supplier" class="form-control rounded-3" placeholder="Contoh: Tokopedia Official Store / PT Supplier Perkasa">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label text-xs fw-semibold text-dark">Alasan Pembelian / Kebutuhan *</label>
                <textarea name="alasan_pembelian" class="form-control rounded-3" rows="3" placeholder="Jelaskan kebutuhan pengadaan barang..." required></textarea>
            </div>
        </div>

        <!-- Dynamic Items Table Card -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0"><i class="fas fa-boxes text-primary me-2"></i> Rincian Barang yang Dibeli</h6>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" id="btnAddRow">
                    <i class="fas fa-plus me-1"></i> Tambah Item
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="tableItems">
                    <thead class="table-light">
                        <tr class="text-xs text-uppercase">
                            <th>Nama Barang *</th>
                            <th style="width: 120px;">Qty *</th>
                            <th style="width: 200px;">Harga Satuan Est. (Rp)</th>
                            <th style="width: 60px;" class="text-center">Hapus</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" name="items_nama[]" class="form-control" placeholder="Nama barang ATK / Aset" required></td>
                            <td><input type="number" name="items_qty[]" class="form-control" value="1" min="1" required></td>
                            <td><input type="text" name="items_harga[]" class="form-control" placeholder="50000"></td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="<?= base_url('admin/inventaris/pembelian') ?>" class="btn btn-outline-secondary rounded-pill px-4 py-2">Batal</a>
            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold"><i class="fas fa-save me-1.5"></i> Simpan Transaksi PR</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnAdd = document.getElementById('btnAddRow');
        const tbody = document.querySelector('#tableItems tbody');

        btnAdd.addEventListener('click', function() {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="text" name="items_nama[]" class="form-control" placeholder="Nama barang ATK / Aset" required></td>
                <td><input type="number" name="items_qty[]" class="form-control" value="1" min="1" required></td>
                <td><input type="text" name="items_harga[]" class="form-control" placeholder="50000"></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button></td>
            `;
            tbody.appendChild(tr);
        });

        tbody.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                if (tbody.rows.length > 1) {
                    e.target.closest('tr').remove();
                }
            }
        });
    });
</script>

<?= view('admin/templates/footer', $templateData) ?>
