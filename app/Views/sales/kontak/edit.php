<div class="content-wrapper p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-edit mr-2"></i>Edit Kontak Klien - <?= esc($klien['kode_klien']) ?></h4>
            <p class="text-muted mb-0">Perbarui data profil klien</p>
        </div>
        <a href="<?= site_url('sales/kontak') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="<?= site_url('sales/kontak/update/' . $klien['id']) ?>" method="POST">
                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Nama Klien / Contact Person <span class="text-danger">*</span></label>
                        <input type="text" name="nama_klien" class="form-control" required value="<?= esc($klien['nama_klien']) ?>">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Nama Perusahaan</label>
                        <input type="text" name="perusahaan" class="form-control" value="<?= esc($klien['perusahaan']) ?>">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= esc($klien['email']) ?>">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Nomor Telepon / HP</label>
                        <input type="text" name="telepon" class="form-control" value="<?= esc($klien['telepon']) ?>">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Kategori Industri</label>
                        <input type="text" name="industri" class="form-control" value="<?= esc($klien['industri']) ?>">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Status Klien</label>
                        <select name="status" class="form-control">
                            <?php foreach (['Aktif', 'Prospek', 'Inaktif'] as $st): ?>
                                <option value="<?= $st ?>" <?= ($klien['status'] == $st) ? 'selected' : '' ?>><?= $st ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-12 form-group mb-3">
                        <label class="font-weight-bold">Alamat Perusahaan</label>
                        <textarea name="alamat" class="form-control" rows="2"><?= esc($klien['alamat']) ?></textarea>
                    </div>
                </div>
                <hr>
                <div class="text-right">
                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save mr-1"></i> Update Kontak</button>
                </div>
            </form>
        </div>
    </div>
</div>
