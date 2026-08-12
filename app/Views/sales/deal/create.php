<div class="content-wrapper p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-handshake mr-2"></i>Catat Closing Deal Baru</h4>
            <p class="text-muted mb-0">Input data kesepakatan deal penjualan resmi</p>
        </div>
        <a href="<?= site_url('sales/deal') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="<?= site_url('sales/deal/store') ?>" method="POST">
                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Pilih Lead Prospek</label>
                        <select name="lead_id" class="form-control">
                            <option value="">-- Pilih Lead (Opsional) --</option>
                            <?php foreach ($leads as $l): ?>
                                <option value="<?= $l['id'] ?>"><?= esc($l['nama_lead']) ?> (<?= esc($l['perusahaan']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Pilih Quotation Penawaran</label>
                        <select name="quotation_id" class="form-control">
                            <option value="">-- Pilih Quotation (Opsional) --</option>
                            <?php foreach ($quotations as $q): ?>
                                <option value="<?= $q['id'] ?>"><?= esc($q['nomor_quotation']) ?> - <?= esc($q['nama_klien']) ?> (Rp <?= number_format($q['total'], 0, ',', '.') ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Nama Deal / Proyek / Pekerjaan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_deal" class="form-control" required placeholder="Contoh: Pengadaan & Setup Jaringan Fiber Optic">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Nama Klien / Perusahaan <span class="text-danger">*</span></label>
                        <input type="text" name="perusahaan" class="form-control" required placeholder="Contoh: PT. Wijaya Karya">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Nilai Deal Kesepakatan (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="nilai_deal" class="form-control" required placeholder="150000000">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Tanggal Closing Kesepakatan</label>
                        <input type="date" name="tanggal_closing" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-12 form-group mb-3">
                        <label class="font-weight-bold">Catatan Lingkup Pekerjaan / Term Deal</label>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Ringkasan pekerjaan dan spesifikasi proyek..."></textarea>
                    </div>
                </div>
                <hr>
                <div class="text-right">
                    <button type="submit" class="btn btn-success px-4"><i class="fas fa-save mr-1"></i> Simpan Closing Deal</button>
                </div>
            </form>
        </div>
    </div>
</div>
