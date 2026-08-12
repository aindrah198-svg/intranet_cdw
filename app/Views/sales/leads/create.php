<div class="content-wrapper p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary font-weight-bold"><i class="fas fa-user-plus mr-2"></i>Tambah Lead Baru</h4>
            <p class="text-muted mb-0">Input data calon klien baru ke dalam sistem sales</p>
        </div>
        <a href="<?= site_url('sales/leads') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="<?= site_url('sales/leads/store') ?>" method="POST">
                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Nama Lead / Contact Person <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lead" class="form-control" required placeholder="Contoh: Budi Santoso">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Nama Perusahaan / Organisasi</label>
                        <input type="text" name="perusahaan" class="form-control" placeholder="Contoh: PT. Maju Bersama">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="budi@majubersama.com">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Nomor Telepon / WA</label>
                        <input type="text" name="telepon" class="form-control" placeholder="081234567890">
                    </div>
                    <div class="col-md-4 form-group mb-3">
                        <label class="font-weight-bold">Sumber Lead</label>
                        <select name="sumber_lead" class="form-control">
                            <option value="Website">Website</option>
                            <option value="Referral">Referral</option>
                            <option value="Social Media">Social Media</option>
                            <option value="Cold Call">Cold Call</option>
                            <option value="Pameran">Pameran</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="col-md-4 form-group mb-3">
                        <label class="font-weight-bold">Nilai Potensi Deal (Rp)</label>
                        <input type="number" name="nilai_potensi" class="form-control" placeholder="10000000" value="0">
                    </div>
                    <div class="col-md-4 form-group mb-3">
                        <label class="font-weight-bold">Status Pipeline</label>
                        <select name="status" class="form-control">
                            <option value="Baru">Baru</option>
                            <option value="Follow Up">Follow Up</option>
                            <option value="Negosiasi">Negosiasi</option>
                            <option value="Closing">Closing</option>
                            <option value="Hilang">Hilang</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Rencana Tanggal Follow Up Next</label>
                        <input type="date" name="tgl_follow_up" class="form-control">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="font-weight-bold">Catatan / Kebutuhan Klien</label>
                        <textarea name="catatan" class="form-control" rows="2" placeholder="Penjelasan kebutuhan atau poin negosiasi..."></textarea>
                    </div>
                </div>
                <hr>
                <div class="text-right">
                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save mr-1"></i> Simpan Lead</button>
                </div>
            </form>
        </div>
    </div>
</div>
