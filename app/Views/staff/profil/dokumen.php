<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="fas fa-id-card text-primary me-2"></i> Dokumen Saya</h4>
                <p class="text-muted mb-0">Arsip dokumen personal & surat keputusan (read-only dari HRD)</p>
            </div>
            <a href="<?= base_url('staff/profil') ?>" class="btn btn-outline-secondary btn-sm"><i class="fas fa-user-edit me-1"></i> Edit Profil</a>
        </div>

        <div class="card card-custom p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama / Jenis Dokumen</th>
                            <th>Nomor Dokumen</th>
                            <th>Tanggal Upload / Terbit</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($dokumenList)): foreach ($dokumenList as $idx => $d): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td class="fw-bold text-dark"><?= esc($d['nama_dokumen'] ?? $d['jenis_dokumen'] ?? 'Dokumen Karyawan') ?></td>
                                <td><span class="badge bg-secondary"><?= esc($d['nomor_dokumen'] ?? $d['kode_dokumen'] ?? '-') ?></span></td>
                                <td><?= date('d M Y', strtotime($d['created_at'] ?? date('Y-m-d'))) ?></td>
                                <td><small class="text-secondary"><?= esc($d['keterangan'] ?? $d['alasan'] ?? '-') ?></small></td>
                                <td>
                                    <?php if (!empty($d['file_path']) || !empty($d['file'])): ?>
                                        <a href="<?= base_url('uploads/dokumen/' . ($d['file_path'] ?? $d['file'])) ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-download me-1"></i> Unduh Dokumen</a>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted">File Tersimpan di HRD</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada berkas dokumen (Kontrak / SK) yang diunggah oleh HRD untuk Anda.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
