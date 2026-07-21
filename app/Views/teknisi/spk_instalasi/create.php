<?= $this->include('teknisi/templates/header') ?>
<?= $this->include('teknisi/templates/sidebar') ?>
<?= $this->include('teknisi/templates/navbar') ?>

<!-- Konten utama -->
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><?= esc($title ?? 'Buat SPK Baru') ?></h4>
            <p class="text-muted mb-0"><?= esc($subtitle ?? 'Tambahkan SPK / Tugas Instalasi baru') ?></p>
        </div>
        <a href="<?= base_url('teknisi/tugas-proyek/spk') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
    
    <!-- Form Tambah SPK -->
    <div class="dashboard-card">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="fas fa-file-contract me-2 text-primary"></i>Form Tambah SPK</h5>
        </div>
        <div class="card-body">
            <form action="<?= base_url('teknisi/tugas-proyek/spk/store') ?>" method="post" enctype="multipart/form-data" id="formSpk">
                <?= csrf_field() ?>
                
                <!-- Informasi Dasar -->
                <div class="section-title mb-3">
                    <h6 class="fw-bold"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi Dasar</h6>
                    <hr class="mt-1">
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <!-- Nomor SPK -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nomor SPK <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">SPK-</span>
                                <input type="text" name="nomor_spk_custom" id="nomor_spk_custom" class="form-control <?= (session('errors.nomor_spk')) ? 'is-invalid' : '' ?>" 
                                       value="<?= old('nomor_spk_custom', isset($nomor_spk_otomatis) ? substr($nomor_spk_otomatis, 4) : date('Ymd') . '-001') ?>" 
                                       placeholder="YYYYMMDD-001" required>
                                <input type="hidden" name="nomor_spk" id="nomor_spk" value="<?= old('nomor_spk', $nomor_spk_otomatis ?? 'SPK-' . date('Ymd') . '-001') ?>">
                            </div>
                            <?php if(session('errors.nomor_spk')): ?>
                                <div class="invalid-feedback d-block"><?= session('errors.nomor_spk') ?></div>
                            <?php endif; ?>
                            <small class="text-muted">Format: SPK-TAHUNBULANTANGGAL-NOURUT (contoh: SPK-20250223-001)</small>
                        </div>
                        
                        <!-- Judul Pekerjaan -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Judul Pekerjaan <span class="text-danger">*</span></label>
                            <input type="text" name="judul_pekerjaan" class="form-control <?= (session('errors.judul_pekerjaan')) ? 'is-invalid' : '' ?>" 
                                   value="<?= old('judul_pekerjaan') ?>" placeholder="Masukkan judul pekerjaan" required>
                            <?php if(session('errors.judul_pekerjaan')): ?>
                                <div class="invalid-feedback"><?= session('errors.judul_pekerjaan') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Deskripsi Pekerjaan -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi Pekerjaan</label>
                            <textarea name="deskripsi" class="form-control" rows="4" placeholder="Deskripsikan pekerjaan secara detail..."><?= old('deskripsi') ?></textarea>
                        </div>
                        
                        <!-- Lokasi Pekerjaan -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Lokasi Pekerjaan</label>
                            <input type="text" name="lokasi" id="lokasi" class="form-control" value="<?= old('lokasi') ?>" placeholder="Akan terisi otomatis dari alamat client" readonly>
                            <small class="text-muted">Lokasi akan terisi otomatis setelah memilih client</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Tanggal Mulai -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_mulai" class="form-control <?= (session('errors.tanggal_mulai')) ? 'is-invalid' : '' ?>" 
                                           value="<?= old('tanggal_mulai', date('Y-m-d')) ?>" required>
                                    <?php if(session('errors.tanggal_mulai')): ?>
                                        <div class="invalid-feedback"><?= session('errors.tanggal_mulai') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Target Selesai -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Target Selesai</label>
                                    <input type="date" name="target_selesai" class="form-control" value="<?= old('target_selesai', date('Y-m-d', strtotime('+7 days'))) ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Kategori Pekerjaan -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Kategori Pekerjaan</label>
                                    <select name="kategori_pekerjaan" class="form-select">
                                        <option value="">-- Pilih Kategori --</option>
                                        <?php foreach($kategori_options as $key => $value): ?>
                                            <?php if($key !== ''): ?>
                                                <option value="<?= $key ?>" <?= old('kategori_pekerjaan') == $key ? 'selected' : '' ?>>
                                                    <?= $value ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Prioritas -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Prioritas <span class="text-danger">*</span></label>
                                    <select name="prioritas" class="form-select <?= (session('errors.prioritas')) ? 'is-invalid' : '' ?>" required>
                                        <?php foreach($prioritas_options as $key => $value): ?>
                                            <option value="<?= $key ?>" <?= old('prioritas', 'Normal') == $key ? 'selected' : '' ?>>
                                                <?= $value ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if(session('errors.prioritas')): ?>
                                        <div class="invalid-feedback"><?= session('errors.prioritas') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Status Awal -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Status Awal</label>
                                    <select name="status" class="form-select">
                                        <?php foreach($status_options as $key => $value): ?>
                                            <option value="<?= $key ?>" <?= old('status', 'Draft') == $key ? 'selected' : '' ?>>
                                                <?= $value ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Progress Awal -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Progress Awal (%)</label>
                                    <input type="number" name="progress_persen" class="form-control" value="<?= old('progress_persen', '0') ?>" min="0" max="100" step="5">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tanggal Selesai Aktual -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Selesai Aktual</label>
                            <input type="date" name="tanggal_selesai_aktual" class="form-control" value="<?= old('tanggal_selesai_aktual') ?>">
                            <small class="text-muted">Kosongkan jika belum selesai</small>
                        </div>
                    </div>
                </div>
                
                <!-- Informasi Client - Pilih dari Database -->
                <div class="section-title mb-3">
                    <h6 class="fw-bold"><i class="fas fa-building me-2 text-primary"></i>Informasi Client</h6>
                    <hr class="mt-1">
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <!-- Pilih Client dari Database -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih Client <span class="text-danger">*</span></label>
                            <select name="client_id" id="client_id" class="form-select <?= (session('errors.client_id')) ? 'is-invalid' : '' ?>" required>
                                <option value="">-- Pilih Client --</option>
                                <?php if(!empty($clients)): ?>
                                    <?php foreach($clients as $client): ?>
                                        <?php 
                                        if (is_object($client)) {
                                            $clientId = $client->id;
                                            $clientNama = $client->nama_perusahaan;
                                            $clientAlamat = $client->alamat ?? '';
                                            $clientTelepon = $client->telepon ?? '';
                                            $clientEmail = $client->email_client ?? '';
                                            $clientKontakNama = $client->nama_kontak ?? '';
                                            $clientKontak = $client->client_kontak ?? $client->telepon ?? ''; // Informasi kontak tambahan
                                            $clientCatatan = $client->catatan_client ?? '';
                                            $clientKode = $client->kode_client ?? '';
                                        } else {
                                            $clientId = $client['id'];
                                            $clientNama = $client['nama_perusahaan'];
                                            $clientAlamat = $client['alamat'] ?? '';
                                            $clientTelepon = $client['telepon'] ?? '';
                                            $clientEmail = $client['email_client'] ?? '';
                                            $clientKontakNama = $client['nama_kontak'] ?? '';
                                            $clientKontak = $client['client_kontak'] ?? $client['telepon'] ?? '';
                                            $clientCatatan = $client['catatan_client'] ?? '';
                                            $clientKode = $client['kode_client'] ?? '';
                                        }
                                        ?>
                                        <option value="<?= $clientId ?>" 
                                                data-nama="<?= esc($clientNama) ?>"
                                                data-alamat="<?= esc($clientAlamat) ?>"
                                                data-telepon="<?= esc($clientTelepon) ?>"
                                                data-email="<?= esc($clientEmail) ?>"
                                                data-kontak-nama="<?= esc($clientKontakNama) ?>"
                                                data-kontak="<?= esc($clientKontak) ?>"
                                                data-catatan="<?= esc($clientCatatan) ?>"
                                                data-kode="<?= esc($clientKode) ?>"
                                                <?= old('client_id') == $clientId ? 'selected' : '' ?>>
                                            <?= esc($clientNama) ?> (<?= esc($clientKode) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php if(session('errors.client_id')): ?>
                                <div class="invalid-feedback"><?= session('errors.client_id') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Detail Client (Readonly) -->
                        <div class="card bg-light mb-3">
                            <div class="card-header bg-secondary text-white py-2">
                                <i class="fas fa-building me-2"></i>Detail Client
                            </div>
                            <div class="card-body" id="client-detail">
                                <div class="text-muted text-center py-3">
                                    <i class="fas fa-arrow-up me-2"></i>Pilih client terlebih dahulu
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hidden fields untuk data client yang akan dikirim ke server -->
                        <input type="hidden" name="client_nama" id="client_nama_hidden" value="">
                        <input type="hidden" name="client_alamat" id="client_alamat_hidden" value="">
                        <input type="hidden" name="client_kontak" id="client_kontak_hidden" value="">
                        <input type="hidden" name="catatan_client" id="catatan_client_hidden" value="">
                    </div>
                    
                    <div class="col-md-6">
                        <!-- Informasi Kontak - Tampilan saja -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Kontak Person</label>
                            <input type="text" id="client_kontak_nama_display" class="form-control bg-light" readonly placeholder="Nama kontak person">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Telepon</label>
                                    <input type="text" id="client_telepon_display" class="form-control bg-light" readonly placeholder="Nomor telepon">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Email</label>
                                    <input type="email" id="client_email_display" class="form-control bg-light" readonly placeholder="Email">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Informasi Kontak Tambahan (client_kontak) - Bisa diedit -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Informasi Kontak Tambahan</label>
                            <input type="text" name="client_kontak_input" id="client_kontak_input" class="form-control" 
                                   value="<?= old('client_kontak_input') ?>" placeholder="Nomor HP, WhatsApp, dll">
                            <small class="text-muted">Informasi kontak tambahan (bisa diisi manual jika perlu)</small>
                        </div>
                        
                        <!-- Catatan Client - Bisa diedit -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Catatan Client</label>
                            <textarea name="catatan_client_input" id="catatan_client_input" class="form-control" rows="3" placeholder="Catatan khusus untuk client ini"><?= old('catatan_client_input') ?></textarea>
                            <small class="text-muted">Catatan untuk client ini (bisa diedit)</small>
                        </div>
                    </div>
                </div>
                
                <!-- Tim Teknisi -->
                <div class="section-title mb-3">
                    <h6 class="fw-bold"><i class="fas fa-users me-2 text-primary"></i>Tim Teknisi</h6>
                    <hr class="mt-1">
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <!-- Pilih Teknisi -->
                        <div class="mb-3">
                            <label class="form-label">Pilih Teknisi</label>
                            <select name="tim_teknisi[]" class="form-select" multiple size="5">
                                <?php if(!empty($teknisi_list)): ?>
                                    <?php foreach($teknisi_list as $teknisi): ?>
                                        <?php 
                                        $teknisiId = is_object($teknisi) ? $teknisi->id : $teknisi['id'];
                                        $teknisiNama = is_object($teknisi) ? $teknisi->nama_lengkap : $teknisi['nama_lengkap'];
                                        $teknisiNik = is_object($teknisi) ? $teknisi->nik : $teknisi['nik'];
                                        ?>
                                        <option value="<?= $teknisiId ?>" <?= in_array($teknisiId, old('tim_teknisi', [])) ? 'selected' : '' ?>>
                                            <?= esc($teknisiNama) ?> (<?= esc($teknisiNik) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>Tidak ada teknisi tersedia</option>
                                <?php endif; ?>
                            </select>
                            <small class="text-muted">Tekan Ctrl untuk memilih lebih dari satu</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <!-- Project Manager -->
                        <div class="mb-3">
                            <label class="form-label">Project Manager</label>
                            <select name="project_manager_id" class="form-select">
                                <option value="">-- Pilih Manager --</option>
                                <?php if(!empty($managers)): ?>
                                    <?php foreach($managers as $manager): ?>
                                        <?php 
                                        $managerId = is_object($manager) ? $manager->id : $manager['id'];
                                        $managerNama = is_object($manager) ? $manager->nama_lengkap : $manager['nama_lengkap'];
                                        $managerJabatan = is_object($manager) ? $manager->jabatan : $manager['jabatan'];
                                        ?>
                                        <option value="<?= $managerId ?>" <?= old('project_manager_id') == $managerId ? 'selected' : '' ?>>
                                            <?= esc($managerNama) ?> (<?= esc($managerJabatan) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Item Pekerjaan -->
                <div class="section-title mb-3">
                    <h6 class="fw-bold"><i class="fas fa-list-check me-2 text-primary"></i>Item Pekerjaan</h6>
                    <hr class="mt-1">
                </div>
                
                <div class="mb-4">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="items_table">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="20%">Nama Item <span class="text-danger">*</span></th>
                                    <th width="20%">Deskripsi</th>
                                    <th width="8%">Qty <span class="text-danger">*</span></th>
                                    <th width="8%">Satuan</th>
                                    <th width="12%">Harga (Rp)</th>
                                    <th width="12%">Subtotal (Rp)</th>
                                    <th width="10%">Status</th>
                                    <th width="5%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr id="item_row_1">
                                    <td class="text-center align-middle">1</td>
                                    <td>
                                        <input type="text" name="item_nama[]" class="form-control form-control-sm item-nama" placeholder="Nama item" required>
                                    </td>
                                    <td>
                                        <input type="text" name="item_deskripsi[]" class="form-control form-control-sm item-deskripsi" placeholder="Deskripsi item">
                                    </td>
                                    <td>
                                        <input type="number" name="item_qty[]" class="form-control form-control-sm item-qty" value="1" min="0.01" step="0.01" required>
                                    </td>
                                    <td>
                                        <select name="item_satuan[]" class="form-select form-select-sm item-satuan">
                                            <option value="unit">unit</option>
                                            <option value="set">set</option>
                                            <option value="buah">buah</option>
                                            <option value="meter">meter</option>
                                            <option value="liter">liter</option>
                                            <option value="kg">kg</option>
                                            <option value="hari">hari</option>
                                            <option value="paket">paket</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="item_harga[]" class="form-control form-control-sm item-harga" value="0">
                                    </td>
                                    <td>
                                        <input type="text" name="item_subtotal[]" class="form-control form-control-sm item-subtotal bg-light" value="0" readonly>
                                    </td>
                                    <td>
                                        <select name="item_status[]" class="form-select form-select-sm item-status">
                                            <option value="Pending">Pending</option>
                                            <option value="Selesai">Selesai</option>
                                            <option value="Bermasalah">Bermasalah</option>
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger remove-item" disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="table-secondary">
                                <tr>
                                    <th colspan="6" class="text-end">Total Estimasi Biaya:</th>
                                    <th>
                                        <input type="text" id="total_estimasi" class="form-control form-control-sm bg-primary text-white fw-bold" value="Rp 0" readonly>
                                    </th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary" id="add-item">
                        <i class="fas fa-plus me-2"></i>Tambah Item
                    </button>
                </div>
                
                <!-- Estimasi Biaya (Hidden) -->
                <input type="hidden" name="estimasi_biaya" id="estimasi_biaya" value="0">
                
                <!-- Informasi Tambahan -->
                <div class="section-title mb-3">
                    <h6 class="fw-bold"><i class="fas fa-paperclip me-2 text-primary"></i>Informasi Tambahan & Dokumentasi</h6>
                    <hr class="mt-1">
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <!-- Dokumen Pendukung -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Dokumen Pendukung</label>
                            <input type="file" name="dokumen_pendukung" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <small class="text-muted">Format: PDF, DOC, JPG, PNG. Maks 5MB</small>
                        </div>
                        
                        <!-- Dokumentasi -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Dokumentasi</label>
                            <input type="file" name="dokumentasi" class="form-control" accept=".jpg,.jpeg,.png">
                            <small class="text-muted">Format: JPG, PNG. Maks 5MB</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <!-- Catatan -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="4" placeholder="Catatan tambahan untuk SPK ini..."><?= old('catatan') ?></textarea>
                        </div>
                        
                        <!-- Laporan -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Laporan</label>
                            <textarea name="laporan" class="form-control" rows="4" placeholder="Laporan awal jika ada..."><?= old('laporan') ?></textarea>
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div class="d-flex justify-content-end gap-2">
                    <button type="reset" class="btn btn-secondary px-4" onclick="return confirm('Reset form? Semua data yang sudah diisi akan hilang.')">
                        <i class="fas fa-undo me-2"></i>Reset
                    </button>
                    <button type="submit" class="btn btn-primary px-5" id="btnSubmit">
                        <i class="fas fa-save me-2"></i>Simpan SPK
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- jQuery (pastikan jQuery sudah terload) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
// Variabel global
let itemCounter = 1;

// Fungsi-fungsi pendukung
function generateNomorSpk() {
    let customPart = $('#nomor_spk_custom').val().trim() || 
                     new Date().getFullYear() + 
                     String(new Date().getMonth() + 1).padStart(2,'0') + 
                     String(new Date().getDate()).padStart(2,'0') + '-001';
    
    customPart = customPart.replace(/[^0-9\-]/g, '');
    if (!customPart.includes('-')) customPart += '-001';
    
    $('#nomor_spk_custom').val(customPart);
    $('#nomor_spk').val('SPK-' + customPart);
}

function formatHarga(input) {
    if (!input) return;
    
    let value = input.value.replace(/[^\d]/g, '');
    if (value === '' || value === '0') {
        input.value = '0';
    } else {
        // Hapus leading zeros
        value = parseInt(value, 10).toString();
        
        // Format dengan titik ribuan
        input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    
    hitungSubtotal(input);
}

function hitungSubtotal(element) {
    // Dapatkan baris dari element yang dipicu
    let row = $(element).closest('tr');
    if (row.length === 0) return;
    
    let qty = parseFloat(row.find('.item-qty').val()) || 0;
    
    // Ambil value harga dan bersihkan dari titik
    let hargaStr = row.find('.item-harga').val().replace(/\./g, '');
    let harga = parseFloat(hargaStr) || 0;
    
    let subtotal = qty * harga;
    
    // Format subtotal dengan titik ribuan
    let subtotalFormatted = Math.round(subtotal).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    row.find('.item-subtotal').val(subtotalFormatted);
    
    // Hitung total semua
    hitungTotalSemua();
}

function hitungTotalSemua() {
    let total = 0;
    
    $('.item-subtotal').each(function() {
        let val = $(this).val().replace(/\./g, '');
        total += parseFloat(val) || 0;
    });
    
    // Format total
    let totalFormatted = Math.round(total).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    
    $('#total_estimasi').val('Rp ' + totalFormatted);
    $('#estimasi_biaya').val(total);
}

function updateNomorUrut() {
    $('#items_table tbody tr').each(function(index) {
        $(this).find('td:first').text(index + 1);
    });
}

function tambahItem() {
    itemCounter++;
    
    let newRow = `
        <tr id="item_row_${itemCounter}">
            <td class="text-center align-middle">${itemCounter}</td>
            <td>
                <input type="text" name="item_nama[]" class="form-control form-control-sm item-nama" placeholder="Nama item" required>
            </td>
            <td>
                <input type="text" name="item_deskripsi[]" class="form-control form-control-sm item-deskripsi" placeholder="Deskripsi item">
            </td>
            <td>
                <input type="number" name="item_qty[]" class="form-control form-control-sm item-qty" value="1" min="0.01" step="0.01" required>
            </td>
            <td>
                <select name="item_satuan[]" class="form-select form-select-sm item-satuan">
                    <option value="unit">unit</option>
                    <option value="set">set</option>
                    <option value="buah">buah</option>
                    <option value="meter">meter</option>
                    <option value="liter">liter</option>
                    <option value="kg">kg</option>
                    <option value="hari">hari</option>
                    <option value="paket">paket</option>
                </select>
            </td>
            <td>
                <input type="text" name="item_harga[]" class="form-control form-control-sm item-harga" value="0">
            </td>
            <td>
                <input type="text" name="item_subtotal[]" class="form-control form-control-sm item-subtotal bg-light" value="0" readonly>
            </td>
            <td>
                <select name="item_status[]" class="form-select form-select-sm item-status">
                    <option value="Pending">Pending</option>
                    <option value="Selesai">Selesai</option>
                    <option value="Bermasalah">Bermasalah</option>
                </select>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger remove-item">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
    
    $('#items_table tbody').append(newRow);
    updateNomorUrut();
}

function hapusItem(btn) {
    if ($('#items_table tbody tr').length > 1) {
        Swal.fire({
            title: 'Hapus Item?',
            text: 'Apakah Anda yakin ingin menghapus item ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $(btn).closest('tr').remove();
                updateNomorUrut();
                hitungTotalSemua();
            }
        });
    } else {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak Bisa Hapus',
            text: 'Minimal harus ada satu item pekerjaan',
            confirmButtonText: 'OK'
        });
    }
}

// File upload validation
function validateFileSize(input) {
    const file = input.files[0];
    if (file && file.size > 5 * 1024 * 1024) {
        Swal.fire({
            icon: 'error',
            title: 'File Terlalu Besar',
            text: 'Ukuran file maksimal 5MB',
            confirmButtonText: 'OK'
        });
        input.value = '';
    }
}

// Document Ready
$(document).ready(function() {
    console.log('Document ready - memulai inisialisasi');
    
    // Generate nomor SPK otomatis
    generateNomorSpk();
    
    $('#nomor_spk_custom').on('keyup change', function() {
        generateNomorSpk();
    });
    
    // Event ketika client dipilih
    $('#client_id').on('change', function() {
        let selectedOption = $(this).find('option:selected');
        
        if (selectedOption.val()) {
            let nama = selectedOption.data('nama') || '';
            let alamat = selectedOption.data('alamat') || '';
            let telepon = selectedOption.data('telepon') || '';
            let email = selectedOption.data('email') || '';
            let kontakNama = selectedOption.data('kontak-nama') || '';
            let kontak = selectedOption.data('kontak') || '';
            let catatan = selectedOption.data('catatan') || '';
            
            // Update tampilan detail client
            let html = `
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td style="width: 40%"><i class="fas fa-building me-2 text-secondary"></i>Perusahaan:</td>
                        <td class="fw-bold">${nama}</td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-user me-2 text-secondary"></i>Kontak:</td>
                        <td>${kontakNama || '-'}</td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-phone me-2 text-secondary"></i>Telepon:</td>
                        <td>${telepon || '-'}</td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-envelope me-2 text-secondary"></i>Email:</td>
                        <td>${email || '-'}</td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-map-marker me-2 text-secondary"></i>Alamat:</td>
                        <td>${alamat || '-'}</td>
                    </tr>
                </table>
            `;
            
            $('#client-detail').html(html);
            
            // Update field display
            $('#lokasi').val(alamat);
            $('#client_kontak_nama_display').val(kontakNama);
            $('#client_telepon_display').val(telepon);
            $('#client_email_display').val(email);
            
            // Update hidden fields untuk dikirim ke server
            $('#client_nama_hidden').val(nama);
            $('#client_alamat_hidden').val(alamat);
            $('#client_kontak_hidden').val(kontak);
            $('#catatan_client_hidden').val(catatan);
            
            // Update input fields yang bisa diedit
            $('#client_kontak_input').val(kontak);
            $('#catatan_client_input').val(catatan);
            
        } else {
            $('#client-detail').html('<div class="text-muted text-center py-3"><i class="fas fa-arrow-up me-2"></i>Pilih client terlebih dahulu</div>');
            $('#lokasi, #client_kontak_nama_display, #client_telepon_display, #client_email_display').val('');
            $('#client_nama_hidden, #client_alamat_hidden, #client_kontak_hidden, #catatan_client_hidden').val('');
            $('#client_kontak_input, #catatan_client_input').val('');
        }
    });
    
    // Load data client jika ada yang terpilih
    if ($('#client_id').val()) {
        $('#client_id').trigger('change');
    }
    
    // Event untuk mengupdate hidden field ketika input yang bisa diedit berubah
    $('#client_kontak_input').on('input', function() {
        $('#client_kontak_hidden').val($(this).val());
    });
    
    $('#catatan_client_input').on('input', function() {
        $('#catatan_client_hidden').val($(this).val());
    });
    
    // ===== EVENT HANDLING UNTUK ITEM =====
    
    // Event delegation untuk semua perubahan pada item
    $(document).on('input', '.item-qty', function() {
        hitungSubtotal(this);
    });
    
    $(document).on('keyup', '.item-harga', function() {
        formatHarga(this);
    });
    
    $(document).on('blur', '.item-harga', function() {
        // Format ulang saat keluar dari field
        if ($(this).val() === '' || $(this).val() === '0') {
            $(this).val('0');
        }
        hitungSubtotal(this);
    });
    
    // Tambah item
    $('#add-item').on('click', function() {
        tambahItem();
    });
    
    // Hapus item
    $(document).on('click', '.remove-item', function() {
        hapusItem(this);
    });
    
    // Hitung untuk item pertama setelah semua event terdaftar
    setTimeout(function() {
        console.log('Menghitung item pertama');
        if ($('.item-harga').length > 0) {
            // Set harga default 0 dan hitung
            $('.item-harga').first().val('0');
            hitungSubtotal($('.item-harga').first()[0]);
        }
    }, 500);
    
    // Validasi form sebelum submit
    $('#formSpk').on('submit', function(e) {
        let valid = true;
        let errors = [];
        
        // Validasi nomor SPK
        if (!$('#nomor_spk').val().trim() || $('#nomor_spk').val().trim() === 'SPK-') {
            valid = false;
            errors.push('Nomor SPK harus diisi');
        }
        
        // Validasi judul pekerjaan
        if (!$('input[name="judul_pekerjaan"]').val().trim()) {
            valid = false;
            errors.push('Judul pekerjaan harus diisi');
        }
        
        // Validasi client
        if (!$('#client_id').val()) {
            valid = false;
            errors.push('Client harus dipilih');
        }
        
        // Validasi tanggal mulai
        if (!$('input[name="tanggal_mulai"]').val()) {
            valid = false;
            errors.push('Tanggal mulai harus diisi');
        }
        
        // Validasi item pekerjaan
        let itemFilled = false;
        $('.item-nama').each(function() {
            if ($(this).val().trim()) itemFilled = true;
        });
        
        if (!itemFilled) {
            valid = false;
            errors.push('Minimal satu item pekerjaan harus diisi');
        }
        
        if (!valid) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Validasi Gagal',
                html: errors.join('<br>'),
                confirmButtonText: 'OK'
            });
        } else {
            // Update estimasi biaya sebelum submit
            let total = 0;
            $('.item-subtotal').each(function() {
                let val = $(this).val().replace(/\./g, '');
                total += parseFloat(val) || 0;
            });
            $('#estimasi_biaya').val(total);
            
            Swal.fire({
                title: 'Menyimpan...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        }
    });
    
    // Event untuk file upload
    $(document).on('change', 'input[name="dokumen_pendukung"], input[name="dokumentasi"]', function() {
        validateFileSize(this);
    });
    
    console.log('Inisialisasi selesai');
});

// SweetAlert untuk pesan session
<?php if(session()->getFlashdata('error')): ?>
    Swal.fire({ icon: 'error', title: 'Error', text: '<?= session()->getFlashdata('error') ?>' });
<?php endif; ?>

<?php if(session()->getFlashdata('success')): ?>
    Swal.fire({ icon: 'success', title: 'Berhasil', text: '<?= session()->getFlashdata('success') ?>' });
<?php endif; ?>

<?php if(session()->getFlashdata('errors')): ?>
    Swal.fire({ 
        icon: 'error', 
        title: 'Validasi Gagal', 
        html: '<?= implode('<br>', session()->getFlashdata('errors')) ?>' 
    });
<?php endif; ?>
</script>

<style>
.section-title h6 {
    color: #4e73df;
    margin-bottom: 0;
}
.section-title hr {
    margin-top: 5px;
    margin-bottom: 15px;
    border-top: 2px solid #4e73df;
    opacity: 0.2;
}
.dashboard-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    overflow: hidden;
}
.card-header {
    border-bottom: 1px solid #eaeaea;
    background-color: white;
}
.form-label.fw-semibold {
    font-weight: 500;
    color: #5a5c69;
}
.input-group-text {
    background-color: #f8f9fc;
}
.table th {
    background-color: #f8f9fc;
    font-size: 0.85rem;
    white-space: nowrap;
}
.table td {
    vertical-align: middle;
}
#client-detail {
    min-height: 150px;
}
.required:after {
    content: " *";
    color: red;
}
</style>

<?= $this->include('teknisi/templates/footer') ?>