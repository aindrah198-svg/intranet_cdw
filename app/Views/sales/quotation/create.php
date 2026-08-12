<div class="content-wrapper p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-plus-circle mr-2"></i>Buat Quotation Baru</h4>
            <p class="text-muted mb-0">Buat penawaran harga resmi untuk calon klien</p>
        </div>
        <a href="<?= site_url('sales/quotation') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <form action="<?= site_url('sales/quotation/store') ?>" method="POST">
                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Pilih Lead / Prospek</label>
                        <select name="lead_id" class="form-control">
                            <option value="">-- Pilih Lead (Opsional) --</option>
                            <?php foreach ($leads as $l): ?>
                                <option value="<?= $l['id'] ?>"><?= esc($l['nama_lead']) ?> (<?= esc($l['perusahaan']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Pilih Kontak Klien Exist</label>
                        <select name="klien_id" class="form-control">
                            <option value="">-- Pilih Klien Exist (Opsional) --</option>
                            <?php foreach ($kliens as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= esc($k['nama_klien']) ?> (<?= esc($k['perusahaan']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Nama Klien / UP <span class="text-danger">*</span></label>
                        <input type="text" name="nama_klien" class="form-control" required placeholder="Contoh: Bapak Hendra">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Nama Perusahaan / Organisasi</label>
                        <input type="text" name="perusahaan" class="form-control" placeholder="Contoh: PT. Sentosa">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Tanggal Quotation</label>
                        <input type="date" name="tanggal_quotation" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Berlaku Sampai</label>
                        <input type="date" name="berlaku_hingga" class="form-control" value="<?= date('Y-m-d', strtotime('+30 days')) ?>">
                    </div>
                </div>

                <!-- Items Table -->
                <h6 class="font-weight-bold text-primary mt-4 mb-3"><i class="fas fa-list mr-2"></i>Item Penawaran Harga</h6>
                <table class="table table-bordered align-middle" id="tableItems">
                    <thead class="bg-light">
                        <tr>
                            <th>Deskripsi Barang / Jasa / Pekerjaan</th>
                            <th width="100">Qty</th>
                            <th width="200">Harga Satuan (Rp)</th>
                            <th width="80" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" name="item_deskripsi[]" class="form-control" required placeholder="Item / Jasa 1"></td>
                            <td><input type="number" name="item_qty[]" class="form-control qty-input" value="1" min="1"></td>
                            <td><input type="number" name="item_harga[]" class="form-control harga-input" value="0"></td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-outline-primary mb-4" id="addRowBtn"><i class="fas fa-plus mr-1"></i> Tambah Item Baris</button>

                <div class="row justify-content-end">
                    <div class="col-md-5">
                        <div class="form-group row mb-2">
                            <label class="col-sm-5 col-form-label font-weight-bold">Subtotal (Rp):</label>
                            <div class="col-sm-7"><input type="number" name="subtotal" id="subtotalInput" class="form-control" readonly value="0"></div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-sm-5 col-form-label font-weight-bold">Diskon (Rp):</label>
                            <div class="col-sm-7"><input type="number" name="diskon" id="diskonInput" class="form-control" value="0"></div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-sm-5 col-form-label font-weight-bold">PPN 11% (Rp):</label>
                            <div class="col-sm-7"><input type="number" name="ppn" id="ppnInput" class="form-control" value="0"></div>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label class="font-weight-bold">Catatan / Term & Conditions</label>
                    <textarea name="catatan" class="form-control" rows="3" placeholder="Syarat pembayaran, waktu garansi, dll..."></textarea>
                </div>

                <hr>
                <div class="text-right">
                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save mr-1"></i> Simpan Quotation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var tableBody = document.querySelector('#tableItems tbody');
    var addRowBtn = document.getElementById('addRowBtn');

    function calculateTotal() {
        var subtotal = 0;
        document.querySelectorAll('#tableItems tbody tr').forEach(function(row) {
            var qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            var harga = parseFloat(row.querySelector('.harga-input').value) || 0;
            subtotal += (qty * harga);
        });
        document.getElementById('subtotalInput').value = subtotal;
        var diskon = parseFloat(document.getElementById('diskonInput').value) || 0;
        var ppn = (subtotal - diskon) * 0.11;
        document.getElementById('ppnInput').value = Math.round(ppn);
    }

    tableBody.addEventListener('input', calculateTotal);
    document.getElementById('diskonInput').addEventListener('input', calculateTotal);

    addRowBtn.addEventListener('click', function() {
        var tr = document.createElement('tr');
        tr.innerHTML = '<td><input type="text" name="item_deskripsi[]" class="form-control" required placeholder="Item / Jasa Baru"></td>' +
            '<td><input type="number" name="item_qty[]" class="form-control qty-input" value="1" min="1"></td>' +
            '<td><input type="number" name="item_harga[]" class="form-control harga-input" value="0"></td>' +
            '<td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button></td>';
        tableBody.appendChild(tr);
    });

    tableBody.addEventListener('click', function(e) {
        if (e.target.closest('.remove-row')) {
            if (document.querySelectorAll('#tableItems tbody tr').length > 1) {
                e.target.closest('tr').remove();
                calculateTotal();
            }
        }
    });
});
</script>
