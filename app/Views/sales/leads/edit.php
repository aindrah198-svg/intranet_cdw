<div class="content-wrapper p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-edit mr-2"></i>Edit Lead - <?= esc($lead['kode_lead']) ?></h4>
            <p class="text-muted mb-0">Perbarui data prospek sales</p>
        </div>
        <a href="<?= site_url('sales/leads') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="<?= site_url('sales/leads/update/' . $lead['id']) ?>" method="POST">
                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Nama Lead / Contact Person <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lead" class="form-control" required value="<?= esc($lead['nama_lead']) ?>">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Nama Perusahaan / Organisasi</label>
                        <input type="text" name="perusahaan" class="form-control" value="<?= esc($lead['perusahaan']) ?>">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= esc($lead['email']) ?>">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Nomor Telepon / WA</label>
                        <input type="text" name="telepon" class="form-control" value="<?= esc($lead['telepon']) ?>">
                    </div>
                    <div class="col-md-4 form-group mb-3">
                        <label class="font-weight-bold">Sumber Lead</label>
                        <select name="sumber_lead" class="form-control">
                            <?php foreach (['Website', 'Referral', 'Social Media', 'Cold Call', 'Pameran', 'Lainnya'] as $s): ?>
                                <option value="<?= $s ?>" <?= ($lead['sumber_lead'] == $s) ? 'selected' : '' ?>><?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 form-group mb-3">
                        <label class="font-weight-bold">Nilai Potensi Deal (Rp)</label>
                        <input type="number" name="nilai_potensi" class="form-control" value="<?= $lead['nilai_potensi'] ?>">
                    </div>
                    <div class="col-md-4 form-group mb-3">
                        <label class="font-weight-bold">Status Pipeline</label>
                        <select name="status" class="form-control">
                            <?php foreach (['Baru', 'Follow Up', 'Negosiasi', 'Closing', 'Hilang'] as $st): ?>
                                <option value="<?= $st ?>" <?= ($lead['status'] == $st) ? 'selected' : '' ?>><?= $st ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Rencana Tanggal Follow Up Next</label>
                        <input type="date" name="tgl_follow_up" class="form-control" value="<?= $lead['tgl_follow_up'] ?>">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Catatan / Kebutuhan Klien</label>
                        <textarea name="catatan" class="form-control" rows="2"><?= esc($lead['catatan']) ?></textarea>
                    </div>
                </div>
                <hr>
                <div class="text-right">
                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save mr-1"></i> Update Lead</button>
                </div>
            </form>
        </div>
    </div>
</div>
