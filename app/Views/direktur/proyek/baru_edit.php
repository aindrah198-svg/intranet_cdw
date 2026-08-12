<?php
// app/Views/direktur/proyek/baru_edit.php

$title = $title ?? 'Edit Project';
$templateData = [
    'title' => $title,
    'active' => 'proyek'
];

echo view('direktur/templates/header', $templateData);
echo view('direktur/templates/sidebar', $templateData);
echo view('direktur/templates/navbar', $templateData);

$formattedNilai = !empty($p['nilai_project']) ? 'Rp ' . number_format($p['nilai_project'], 0, ',', '.') : '';
?>

<div class="container-fluid px-3 px-md-4 py-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs">
                    <li class="breadcrumb-item"><a href="<?= base_url('direktur/proyek/baru') ?>" class="text-decoration-none text-muted">Project Baru</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Edit Project</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0"><i class="fas fa-edit text-warning me-2"></i> Edit Data Project</h4>
            <small class="text-muted">Perbarui informasi inisiasi, nilai proyek, status, atau penunjukan manajer proyek.</small>
        </div>
        <div>
            <a href="<?= base_url('direktur/proyek/baru') ?>" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm text-sm font-semibold">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Main Card Form -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 rounded-4 shadow-lg overflow-hidden mb-4">
                <div class="card-header bg-gradient-primary text-white py-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fs-6 fw-bold mb-0"><i class="fas fa-edit me-2"></i> Edit Project: <?= esc($p['nama_project']) ?></h5>
                    <span class="badge bg-white text-primary rounded-pill px-3 py-1 text-xs fw-bold">Kode: <?= esc($p['kode_project']) ?></span>
                </div>
                <form action="<?= base_url('direktur/proyek/update') ?>" method="POST" onsubmit="const btn = this.querySelector('button[type=submit]'); if(btn){ btn.disabled = true; btn.innerHTML = '<i class=\'fas fa-spinner fa-spin me-1\'></i> Menyimpan...'; }">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                    <div class="card-body p-4">
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Nama Project *</label>
                                <input type="text" class="form-control rounded-3" name="nama_project" id="nama_project_input" value="<?= esc($p['nama_project']) ?>" list="list_existing_projects" required autocomplete="off">
                                <datalist id="list_existing_projects">
                                    <?php foreach(($existing_projects ?? []) as $ep): ?>
                                        <option value="<?= esc($ep) ?>"></option>
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fw-semibold text-xs text-dark mb-0">Client / Klien *</label>
                                    <button type="button" class="btn btn-link p-0 text-decoration-none text-xs fw-bold text-primary" data-bs-toggle="modal" data-bs-target="#tambahClientModal">
                                        <i class="fas fa-plus-circle me-1"></i> + Tambah Client
                                    </button>
                                </div>
                                <select class="form-select rounded-3" name="client_id" id="client_select" required>
                                    <option value="">-- Pilih Client / Klien --</option>
                                    <?php foreach($clients as $c): ?>
                                        <option value="<?= $c['id'] ?>" <?= $p['client_id'] == $c['id'] ? 'selected' : '' ?>><?= esc($c['nama_perusahaan']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-xs text-dark">Deskripsi Singkat Project</label>
                            <textarea class="form-control rounded-3" name="deskripsi" rows="3"><?= esc($p['deskripsi']) ?></textarea>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-xs text-dark">Nilai Project (Rp)</label>
                                <input type="text" class="form-control rounded-3" name="nilai_project" id="input_nilai_project" value="<?= $formattedNilai ?>" placeholder="Rp 0" onkeyup="formatRupiahInput(this)">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-xs text-dark">Rencana Tanggal Mulai</label>
                                <input type="date" class="form-control rounded-3" name="tanggal_mulai" value="<?= esc($p['tanggal_mulai']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-xs text-dark">Estimasi Tanggal Selesai</label>
                                <input type="date" class="form-control rounded-3" name="tanggal_selesai" value="<?= esc($p['tanggal_selesai']) ?>">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Status Saat Ini *</label>
                                <select class="form-select rounded-3" name="status" required>
                                    <option value="penawaran" <?= strtolower($p['status']) == 'penawaran' ? 'selected' : '' ?>>Penawaran</option>
                                    <option value="nego" <?= strtolower($p['status']) == 'nego' ? 'selected' : '' ?>>Nego / Negosiasi</option>
                                    <option value="deal" <?= strtolower($p['status']) == 'deal' ? 'selected' : '' ?>>Deal / Setuju</option>
                                    <option value="on_progress" <?= strtolower($p['status']) == 'on_progress' ? 'selected' : '' ?>>On Progress (Sedang Berjalan)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-xs text-dark">Project Manager (Opsional)</label>
                                <select class="form-select rounded-3" name="project_manager_id">
                                    <option value="">-- Tunjuk Manajer Proyek --</option>
                                    <?php foreach($managers as $m): ?>
                                        <option value="<?= $m['id'] ?>" <?= $p['project_manager_id'] == $m['id'] ? 'selected' : '' ?>><?= esc($m['username']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer bg-light px-4 py-3 text-end rounded-bottom-4">
                        <a href="<?= base_url('direktur/proyek/baru') ?>" class="btn btn-secondary rounded-pill px-4 me-2 font-semibold">Batal</a>
                        <button type="submit" class="btn btn-warning text-white rounded-pill px-4 font-semibold shadow-sm">
                            <i class="fas fa-save me-1.5"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Client Baru -->
<div class="modal fade" id="tambahClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white rounded-top-4 py-3 px-4">
                <h5 class="modal-title fs-6 fw-bold mb-0"><i class="fas fa-building me-2"></i> Tambah Client Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTambahClientQuick" onsubmit="submitQuickClient(event)">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Nama Perusahaan / Client *</label>
                        <input type="text" class="form-control rounded-3" id="client_nama_perusahaan" required placeholder="Cth: PT Pertamina Trans Kontinental">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-xs text-dark">Nama PIC / Kontak</label>
                            <input type="text" class="form-control rounded-3" id="client_nama_kontak" placeholder="Cth: Bpk. Hendra">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-xs text-dark">No. Telepon / WA</label>
                            <input type="text" class="form-control rounded-3" id="client_telepon" placeholder="Cth: 081298765432">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Email Client</label>
                        <input type="email" class="form-control rounded-3" id="client_email" placeholder="Cth: info@klien.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-xs text-dark">Alamat Perusahaan</label>
                        <textarea class="form-control rounded-3" id="client_alamat" rows="2" placeholder="Alamat kantor klien..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4 py-2.5 px-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm" id="btnSimpanClientQuick">
                        <i class="fas fa-plus me-1.5"></i> Tambah Client
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function formatRupiahInput(element) {
        let value = element.value.replace(/[^0-9]/g, '');
        if (value) {
            element.value = 'Rp ' + parseInt(value, 10).toLocaleString('id-ID');
        } else {
            element.value = '';
        }
    }

    function submitQuickClient(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSimpanClientQuick');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';

        const formData = new FormData();
        formData.append('nama_perusahaan', document.getElementById('client_nama_perusahaan').value);
        formData.append('nama_kontak', document.getElementById('client_nama_kontak').value);
        formData.append('telepon', document.getElementById('client_telepon').value);
        formData.append('email', document.getElementById('client_email').value);
        formData.append('alamat', document.getElementById('client_alamat').value);

        fetch('<?= base_url('direktur/proyek/simpan_client') ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-plus me-1.5"></i> Tambah Client';

            if (data.status === 'success') {
                const clientSelect = document.getElementById('client_select');
                const newOpt = document.createElement('option');
                newOpt.value = data.client_id;
                newOpt.textContent = data.nama_perusahaan;
                newOpt.selected = true;
                clientSelect.appendChild(newOpt);

                const clientModalEl = document.getElementById('tambahClientModal');
                const clientModal = bootstrap.Modal.getInstance(clientModalEl) || new bootstrap.Modal(clientModalEl);
                clientModal.hide();

                document.getElementById('formTambahClientQuick').reset();

                Swal.fire({
                    icon: 'success',
                    title: 'Client Ditambahkan!',
                    text: 'Client "' + data.nama_perusahaan + '" telah ditambahkan.',
                    timer: 2500,
                    timerProgressBar: true,
                    confirmButtonColor: '#0d6efd'
                });
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message || 'Gagal menyimpan client.', confirmButtonColor: '#0d6efd' });
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-plus me-1.5"></i> Tambah Client';
            Swal.fire({ icon: 'error', title: 'Error!', text: 'Terjadi kesalahan sistem.', confirmButtonColor: '#0d6efd' });
        });
    }
</script>

<?= view('direktur/templates/footer', $templateData) ?>
