<?= view('software_engineer/templates/header') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-1"><i class="fas fa-exclamation-circle text-danger me-2"></i> Keluhan & Masalah Kerja</h5>
        <small class="text-muted">Form penyampaian kendala infrastruktur, peralatan dev, atau lingkungan kerja</small>
    </div>
    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKeluhan">
        <i class="fas fa-plus me-1"></i> Buat Keluhan
    </button>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Judul Keluhan</th>
                        <th>Kategori</th>
                        <th>Detail Keluhan</th>
                        <th>Tanggal Kirim</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($keluhan_list)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada keluhan dikirim.</td></tr>
                    <?php else: ?>
                        <?php foreach ($keluhan_list as $k): ?>
                            <tr>
                                <td class="fw-bold text-dark"><?= esc($k['judul_keluhan']) ?></td>
                                <td><span class="badge bg-secondary"><?= esc($k['kategori']) ?></span></td>
                                <td><small class="text-dark"><?= esc($k['detail_keluhan']) ?></small></td>
                                <td><small class="code-font"><?= date('d M Y', strtotime($k['created_at'])) ?></small></td>
                                <td><span class="badge bg-warning text-dark"><?= esc($k['status']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Input Keluhan -->
<div class="modal fade" id="modalTambahKeluhan" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= site_url('software-engineer/laporan-keluhan/keluhan/store') ?>" method="POST" class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-exclamation-triangle me-1"></i> Form Keluhan Karyawan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Judul Keluhan</label>
                    <input type="text" name="judul_keluhan" class="form-control" required placeholder="Contoh: Laptop dev sering lag saat compile">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kategori Keluhan</label>
                    <select name="kategori" class="form-select">
                        <option value="Teknis / Infrastructure">Teknis / Infrastructure / Hardware</option>
                        <option value="Lingkungan Kerja">Lingkungan Kerja & Tim</option>
                        <option value="Fasilitas / Lisensi">Fasilitas / Lisensi Software</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Detail Keluhan</label>
                    <textarea name="detail_keluhan" class="form-control" rows="4" required placeholder="Uraikan keluhan secara detail..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Kirim Keluhan</button>
            </div>
        </form>
    </div>
</div>

<?= view('software_engineer/templates/footer') ?>
