<div class="content-wrapper p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-user-plus mr-2"></i>Tambah Kontak Klien Baru</h4>
            <p class="text-muted mb-0">Input data klien baru ke direktori perusahaan</p>
        </div>
        <a href="<?= site_url('sales/kontak') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="<?= site_url('sales/kontak/store') ?>" method="POST">
                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Nama Klien / Contact Person <span class="text-danger">*</span></label>
                        <input type="text" name="nama_klien" class="form-control" required placeholder="Contoh: Pak Robert">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Nama Perusahaan</label>
                        <input type="text" name="perusahaan" class="form-control" placeholder="Contoh: PT. Abadi Jaya">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="robert@abadijaya.com">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Nomor Telepon / HP</label>
                        <input type="text" name="telepon" class="form-control" placeholder="081122334455">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Kategori Industri</label>
                        <input type="text" name="industri" class="form-control" placeholder="Contoh: Teknologi, Konstruksi, Retail">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Status Klien</label>
                        <select name="status" class="form-control">
                            <option value="Aktif">Aktif</option>
                            <option value="Prospek">Prospek</option>
                            <option value="Inaktif">Inaktif</option>
                        </select>
                    </div>
                    <div class="col-md-12 form-group mb-3">
                        <label class="font-weight-bold">Alamat Perusahaan</label>
                        <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat lengkap kantor klien..."></textarea>
                    </div>
                </div>
                <hr>
                <div class="text-right">
                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save mr-1"></i> Simpan Kontak</button>
                </div>
            </form>
        </div>
    </div>
</div>
