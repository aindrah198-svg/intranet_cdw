<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Timeline Proyek - <?= esc($project['kode_project']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #1e293b; background: #fff; line-height: 1.5; }
        .kop-surat { border-bottom: 3px double #0d6efd; padding-bottom: 12px; margin-bottom: 24px; }
        .table-print { font-size: 12px; }
        .table-print th { background-color: #0d6efd !important; color: #ffffff !important; font-weight: 700; text-align: center; vertical-align: middle; }
        .table-print td { vertical-align: middle; }
        .badge-status { padding: 4px 8px; border-radius: 12px; font-weight: 700; font-size: 11px; display: inline-block; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; margin: 0; background: #fff; }
            .container-fluid { width: 100% !important; padding: 0 !important; }
            .table-print th { background-color: #0d6efd !important; color: #ffffff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="container-fluid p-4">
    <!-- Toolbar No Print -->
    <div class="no-print d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded border">
        <div>
            <span class="fw-bold text-dark"><i class="fas fa-print me-2 text-primary"></i> Pratinjau Cetak Timeline Proyek</span>
            <small class="text-muted d-block">Gunakan shortcut keyboard (Ctrl + P) untuk menyimpan file PDF atau mencetak langsung.</small>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 font-semibold shadow-sm">
                <i class="fas fa-print me-1.5"></i> Cetak / Print PDF
            </button>
            <button onclick="window.close()" class="btn btn-outline-secondary rounded-pill px-3">Tutup</button>
        </div>
    </div>

    <!-- Kop Header -->
    <div class="kop-surat d-flex align-items-center justify-content-between">
        <div>
            <h3 class="fw-bold text-primary mb-1" style="letter-spacing: -0.5px;">PT. CDW INTRANET SYSTEM</h3>
            <small class="text-muted d-block">Jl. Raya Utama Intranet No. 88, Jakarta - Indonesia</small>
            <small class="text-muted d-block">Telp: (021) 555-8899 | Email: info@cdw-intranet.co.id</small>
        </div>
        <div class="text-end">
            <h5 class="fw-bold text-dark mb-1">TIMELINE JADWAL PROYEK</h5>
            <span class="badge bg-primary px-3 py-1.5 fs-6 font-monospace">KODE: <?= esc($project['kode_project']) ?></span>
        </div>
    </div>

    <!-- Project Info Card -->
    <div class="row g-3 mb-4 p-3.5 bg-light rounded-3 border">
        <div class="col-6">
            <table class="table table-sm table-borderless mb-0" style="font-size: 13px;">
                <tr>
                    <td class="fw-bold text-muted" width="38%">Nama Proyek</td>
                    <td width="4%">:</td>
                    <td class="fw-bold text-dark"><?= esc($project['nama_project']) ?></td>
                </tr>
                <tr>
                    <td class="fw-bold text-muted">Perusahaan Client</td>
                    <td>:</td>
                    <td><?= esc($client['nama_perusahaan'] ?? 'General / Non-Client') ?></td>
                </tr>
                <tr>
                    <td class="fw-bold text-muted">Project Manager (PIC)</td>
                    <td>:</td>
                    <td><strong class="text-dark"><?= esc($manager['username'] ?? 'Belum Ditunjuk') ?></strong></td>
                </tr>
            </table>
        </div>
        <div class="col-6">
            <table class="table table-sm table-borderless mb-0" style="font-size: 13px;">
                <tr>
                    <td class="fw-bold text-muted" width="38%">Tanggal Mulai (Hari 1)</td>
                    <td width="4%">:</td>
                    <td class="fw-bold text-dark"><?= !empty($project['tanggal_mulai']) ? date('d F Y', strtotime($project['tanggal_mulai'])) : '-' ?></td>
                </tr>
                <tr>
                    <td class="fw-bold text-muted">Estimasi Selesai</td>
                    <td>:</td>
                    <td><?= !empty($project['tanggal_selesai']) ? date('d F Y', strtotime($project['tanggal_selesai'])) : 'Belum Ditentukan' ?></td>
                </tr>
                <tr>
                    <td class="fw-bold text-muted">Nilai Project</td>
                    <td>:</td>
                    <td class="fw-bold text-success">Rp <?= number_format($project['nilai_project'] ?? 0, 0, ',', '.') ?></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Timeline Tasks Table -->
    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-list-check me-2 text-primary"></i> Rincian Tahapan Pekerjaan & Schedule Pelaksanaan</h6>
    <table class="table table-bordered table-print align-middle">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="28%">Nama Tahapan / Pekerjaan</th>
                <th width="16%">Urutan Hari & Minggu</th>
                <th width="12%">Tanggal Mulai</th>
                <th width="12%">Tenggat Selesai</th>
                <th width="14%">PIC Karyawan</th>
                <th width="6%">Progres</th>
                <th width="8%">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($timeline)): ?>
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">Belum ada tahapan kegiatan timeline yang dicatat.</td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach($timeline as $t): ?>
                <?php
                    $hariKe = (int)($t['periode_ke'] ?? 1);
                    if (!empty($project['tanggal_mulai']) && !empty($t['tanggal_mulai'])) {
                        $diffMulai = strtotime($t['tanggal_mulai']) - strtotime($project['tanggal_mulai']);
                        $hariKeCalculated = max(1, floor($diffMulai / 86400) + 1);
                        if ($hariKe <= 1 && $hariKeCalculated > 1) $hariKe = $hariKeCalculated;
                    }
                    $mingguKe = ceil($hariKe / 7);

                    $statusText = 'Belum Mulai';
                    $badgeClass = 'bg-secondary text-white';
                    if ($t['status'] === 'on_progress') { $statusText = 'Sedang Berjalan'; $badgeClass = 'bg-primary text-white'; }
                    if ($t['status'] === 'done' || $t['status'] === 'selesai') { $statusText = 'Selesai'; $badgeClass = 'bg-success text-white'; }

                    $progresVal = (int)($t['progres_persen'] ?? 0);
                    if ($t['status'] === 'done' || $t['status'] === 'selesai') $progresVal = 100;

                    $picName = !empty($t['ditugaskan_kepada']) ? $t['ditugaskan_kepada'] : ($manager['username'] ?? 'Belum Ditunjuk');
                ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td>
                        <div class="fw-bold text-dark"><?= esc($t['nama_tugas']) ?></div>
                        <small class="text-muted d-block"><?= esc($t['deskripsi'] ?: '-') ?></small>
                    </td>
                    <td class="text-center">
                        <strong>Hari ke-<?= $hariKe ?></strong>
                        <small class="d-block text-muted">(Minggu ke-<?= $mingguKe ?>)</small>
                    </td>
                    <td class="text-center"><?= date('d/m/Y', strtotime($t['tanggal_mulai'])) ?></td>
                    <td class="text-center"><?= date('d/m/Y', strtotime($t['tanggal_selesai'])) ?></td>
                    <td><?= esc($picName) ?></td>
                    <td class="text-center font-monospace fw-bold"><?= $progresVal ?>%</td>
                    <td class="text-center">
                        <span class="badge-status <?= $badgeClass ?>"><?= esc($statusText) ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="row mt-5 pt-4">
        <div class="col-6 text-center">
            <small class="text-muted d-block mb-4">Disetujui Oleh,</small>
            <br><br>
            <strong class="text-dark d-block">Direktur Utama / Operational</strong>
            <small class="text-muted">PT. CDW Intranet System</small>
        </div>
        <div class="col-6 text-center">
            <small class="text-muted d-block mb-4">Dibuat Oleh,</small>
            <br><br>
            <strong class="text-dark d-block">Project Manager (PIC)</strong>
            <small class="text-muted"><?= esc($manager['username'] ?? 'Manajemen Pelaksanaan Proyek') ?></small>
        </div>
    </div>
</div>

</body>
</html>
