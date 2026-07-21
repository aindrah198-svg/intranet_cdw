<?php
$title = 'Buat Penawaran Baru';
$subtitle = 'Form Pengajuan Penawaran';
$user = $user ?? session()->get();
$active = 'penawaran';
$perusahaan = $perusahaan ?? [
    'nama_perusahaan' => 'PT. CIPTA DUTA WACANA',
    'alamat' => 'Beltway Office Park Tower B Lantai 5, Jl. TB Simatupang No. 41 Ragunan-Pasar Minggu, Jakarta Selatan',
    'telepon' => '(+62-21) 29857462; 29215392; 29084991',
    'fax' => '(+62-21) 29857201',
    'email' => 'info@cdw-engineering.com',
    'website' => 'www.cdw-engineering.com'
];
?>

<?= $this->include('direktur/templates/header') ?>
<?= $this->include('direktur/templates/sidebar') ?>
<?= $this->include('direktur/templates/navbar') ?>

<style>
    .form-create-penawaran {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    }
    
    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--cdw-primary);
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .section-title i {
        font-size: 1.2rem;
    }
    
    .item-table {
        width: 100%;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    
    .item-table th {
        background: #f8f9fa;
        padding: 12px 15px;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
        text-align: center;
        vertical-align: middle;
    }
    
    .item-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #dee2e6;
        vertical-align: middle;
    }
    
    .item-table .item-number {
        width: 50px;
        text-align: center;
        font-weight: 600;
    }
    
    .item-table .item-actions {
        width: 100px;
        text-align: center;
    }
    
    .summary-box {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        border: 1px solid #e9ecef;
    }
    
    .summary-box .total-row {
        font-size: 1.1rem;
        font-weight: 600;
        border-top: 2px solid #dee2e6;
        padding-top: 10px;
        margin-top: 10px;
    }
    
    .in-words {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 6px;
        padding: 15px;
        margin-top: 15px;
        font-style: italic;
        color: #856404;
    }
    
    .payment-info {
        background: #e7f5ff;
        border: 1px solid #a5d8ff;
        border-radius: 6px;
        padding: 15px;
        margin-top: 15px;
    }
    
    .payment-info h6 {
        color: #1864ab;
        font-weight: 600;
        margin-bottom: 10px;
    }
    
    .modal-header-custom {
        background: linear-gradient(135deg, var(--cdw-primary), #2d8cf0);
        color: white;
        border-radius: 8px 8px 0 0;
    }
    
    .modal-header-custom .modal-title {
        font-weight: 600;
    }
    
    .btn-add-item {
        background: linear-gradient(135deg, var(--cdw-accent), #00d2d3);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
    }
    
    .btn-add-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 210, 211, 0.2);
    }
    
    .alert-light-custom {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-left: 4px solid var(--cdw-primary);
    }
    
    .form-control-sm {
        min-height: calc(1.5em + 0.5rem + 2px);
    }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title mb-1">
                <i class="fas fa-plus-circle me-2"></i>Buat Penawaran Baru
            </h1>
            <p class="page-subtitle">Form pengajuan penawaran harga kepada client</p>
        </div>
        <div>
            <a href="<?= base_url('direktur/penawaran') ?>" class="btn btn-modern-outline">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Error Messages -->
    <?php if (session()->has('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session()->get('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->has('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach (session()->get('errors') as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="form-create-penawaran">
                <form action="<?= base_url('direktur/penawaran/store') ?>" method="POST" id="penawaranForm">
                    <?= csrf_field() ?>
                    
                    <!-- Section 1: Informasi Perusahaan Pengirim -->
                    <div class="mb-4">
                        <div class="section-title">
                            <i class="fas fa-building me-2"></i>Informasi Perusahaan Pengirim
                        </div>
                        <div class="alert alert-info">
                            <div class="row">
                                <div class="col-md-8">
                                    <strong><?= $perusahaan['nama_perusahaan'] ?></strong><br>
                                    <?= $perusahaan['alamat'] ?><br>
                                    Phone: <?= $perusahaan['telepon'] ?><br>
                                    Fax : <?= $perusahaan['fax'] ?><br>
                                    Email: <?= $perusahaan['email'] ?><br>
                                    Website: <?= $perusahaan['website'] ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section 2: Informasi Utama & Client -->
                    <div class="mb-4">
                        <div class="section-title">
                            <i class="fas fa-info-circle me-2"></i>Informasi Utama & Client
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="project_id" class="form-label">Pilih Project <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select class="form-select <?= session()->has('errors.project_id') ? 'is-invalid' : '' ?>" 
                                            id="project_id" name="project_id" required>
                                        <option value="">-- Pilih Project --</option>
                                        <?php if(!empty($projects)): ?>
                                            <?php foreach($projects as $project): ?>
                                                <option value="<?= $project['id'] ?>" 
                                                    <?= old('project_id') == $project['id'] ? 'selected' : '' ?>>
                                                    <?= $project['kode_project'] ?> - <?= $project['nama_project'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <button type="button" class="btn btn-modern-outline" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <?php if (session()->has('errors.project_id')): ?>
                                    <div class="invalid-feedback d-block">
                                        <?= session()->get('errors.project_id') ?>
                                    </div>
                                <?php endif; ?>
                                <small class="text-muted">Jika project tidak ada, buat project baru</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_penawaran" class="form-label">Tanggal Penawaran <span class="text-danger">*</span></label>
                                <input type="date" class="form-control <?= session()->has('errors.tanggal_penawaran') ? 'is-invalid' : '' ?>" 
                                       id="tanggal_penawaran" name="tanggal_penawaran" 
                                       value="<?= old('tanggal_penawaran', date('Y-m-d')) ?>" required>
                                <?php if (session()->has('errors.tanggal_penawaran')): ?>
                                    <div class="invalid-feedback d-block">
                                        <?= session()->get('errors.tanggal_penawaran') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Informasi Client -->
                            <div class="col-md-12 mt-3">
                                <div id="clientInfo" class="alert alert-light-custom">
                                    <h6 class="mb-2">Informasi Client:</h6>
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label small">Attention (Attn)</label>
                                            <input type="text" class="form-control form-control-sm" 
                                                   name="client_attention" placeholder="Contoh: Ibu Clarissa"
                                                   value="<?= old('client_attention') ?>">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label small">Kontak</label>
                                            <input type="text" class="form-control form-control-sm" 
                                                   name="client_contact" placeholder="Nomor telepon"
                                                   value="<?= old('client_contact') ?>">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label small">Due Date</label>
                                            <input type="date" class="form-control form-control-sm" 
                                                   name="due_date" value="<?= old('due_date', date('Y-m-d', strtotime('+14 days'))) ?>">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label small">Customer Code</label>
                                            <input type="text" class="form-control form-control-sm" 
                                                   id="customer_code" readonly value="<?= old('customer_code') ?>">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label small">PO Number</label>
                                            <input type="text" class="form-control form-control-sm" 
                                                   name="client_po_number" placeholder="PO No."
                                                   value="<?= old('client_po_number') ?>">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label small">Alamat Pengiriman</label>
                                            <textarea class="form-control form-control-sm" id="client_address" 
                                                      rows="2" readonly><?= old('client_address') ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section 3: Item Penawaran -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="section-title">
                                <i class="fas fa-list me-2"></i>Item Penawaran
                            </div>
                            <button type="button" class="btn btn-add-item" onclick="addItemRow()">
                                <i class="fas fa-plus me-1"></i> Tambah Item
                            </button>
                        </div>
                        
                        <?php if (session()->has('errors.items')): ?>
                            <div class="alert alert-warning mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?= session()->get('errors.items') ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="table-responsive">
                            <table class="item-table" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th class="item-number">No.</th>
                                        <th>Description</th>
                                        <th style="width: 100px;">Qty</th>
                                        <th style="width: 100px;">Satuan</th>
                                        <th style="width: 180px;">Unit Price (Rp)</th>
                                        <th style="width: 180px;">Total (Rp)</th>
                                        <th class="item-actions">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsContainer">
                                    <!-- Item rows akan ditambahkan di sini via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Summary Section -->
                        <div class="row mt-4">
                            <div class="col-md-8">
                                <div class="payment-info">
                                    <h6>Payment can be transfer to:</h6>
                                    <?php if(!empty($bank_accounts)): ?>
                                        <?php foreach($bank_accounts as $bank): ?>
                                            <div class="mb-2">
                                                <strong><?= $bank['bank_name'] ?></strong><br>
                                                Acc No. <?= $bank['account_number'] ?> (<?= $bank['currency'] ?>)<br>
                                                Atas Nama: <?= $bank['account_name'] ?><br>
                                                <?php if(!empty($bank['branch'])): ?>Branch: <?= $bank['branch'] ?><br><?php endif; ?>
                                                <?php if(!empty($bank['swift_code'])): ?>SWIFT: <?= $bank['swift_code'] ?><br><?php endif; ?>
                                                <?php if($bank['is_default']): ?><span class="badge bg-success">Default</span><?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="mb-2">
                                            <strong>Bank Mandiri</strong><br>
                                            Acc No. 101-000-676-607-3 (IDR)<br>
                                            Atas Nama: PT. CIPTA DUTA WACANA<br>
                                            <span class="badge bg-success">Default</span>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Bank Mandiri</strong><br>
                                            Acc No. 127-000-494-5356 (IDR)<br>
                                            Atas Nama: CECEP TRI HARDIYANTO
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="summary-box">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal:</span>
                                        <span id="totalSubtotal">Rp 0</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>PPN (11%):</span>
                                        <span id="totalPpn">Rp 0</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2 total-row">
                                        <span>Total:</span>
                                        <span id="grandTotal">Rp 0</span>
                                    </div>
                                    
                                    <div class="in-words">
                                        <small>In Word:</small><br>
                                        <span id="inWords">#Nol rupiah#</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section 4: Informasi Tambahan -->
                    <div class="mb-4">
                        <div class="section-title">
                            <i class="fas fa-file-alt me-2"></i>Informasi Tambahan
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="remarks" class="form-label">Remarks <span class="text-muted">(Syarat dan Ketentuan)</span></label>
                                <textarea class="form-control" id="remarks" name="remarks" rows="3"><?= old('remarks', '* Masa berlaku 1 Minggu
* Harga termasuk ongkos kirim
* Tidak termasuk jasa tarik kabel') ?></textarea>
                                <small class="text-muted">Syarat dan ketentuan penawaran</small>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="terms_payment" class="form-label">Syarat Pembayaran</label>
                                <textarea class="form-control" id="terms_payment" name="terms_payment" rows="2" placeholder="Contoh: 50% DP, 50% setelah instalasi"><?= old('terms_payment') ?></textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Keterangan tambahan untuk client"><?= old('keterangan') ?></textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="catatan_khusus" class="form-label">Catatan Khusus (Internal)</label>
                                <textarea class="form-control" id="catatan_khusus" name="catatan_khusus" rows="3" placeholder="Catatan internal untuk tim sales"><?= old('catatan_khusus') ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section 5: Aksi -->
                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?= base_url('direktur/penawaran') ?>" class="btn btn-modern-outline">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-modern-primary">
                                <i class="fas fa-paper-plane me-1"></i> Kirim ke Sales Marketing
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk menambahkan Project baru -->
<div class="modal fade" id="addProjectModal" tabindex="-1" aria-labelledby="addProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <h5 class="modal-title" id="addProjectModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Tambah Project Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addProjectForm">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="new_client_id" class="form-label">Pilih Client <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <select class="form-select" id="new_client_id" name="client_id" required>
                                    <option value="">-- Pilih Client --</option>
                                    <?php if(!empty($clients)): ?>
                                        <?php foreach($clients as $client): ?>
                                            <option value="<?= $client['id'] ?>">
                                                <?= $client['kode_client'] ?> - <?= $client['nama_perusahaan'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <button type="button" class="btn btn-modern-outline" data-bs-toggle="modal" data-bs-target="#addClientModal">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nama_project" class="form-label">Nama Project <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_project" name="nama_project" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi Project</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-modern-outline" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-modern-primary" onclick="saveNewProject()">Simpan Project</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk menambahkan Client baru -->
<div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <h5 class="modal-title" id="addClientModalLabel">
                    <i class="fas fa-user-plus me-2"></i>Tambah Client Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addClientForm">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="nama_perusahaan" class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nama_kontak" class="form-label">Nama Kontak</label>
                            <input type="text" class="form-control" id="nama_kontak" name="nama_kontak">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="telepon" class="form-label">Telepon</label>
                            <input type="text" class="form-control" id="telepon" name="telepon">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="npwp" class="form-label">NPWP</label>
                            <input type="text" class="form-control" id="npwp" name="npwp">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="kategori" class="form-label">Kategori</label>
                            <select class="form-select" id="kategori" name="kategori">
                                <option value="perusahaan">Perusahaan</option>
                                <option value="pemerintah">Pemerintah</option>
                                <option value="perorangan">Perorangan</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-modern-outline" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-modern-primary" onclick="saveNewClient()">Simpan Client</button>
            </div>
        </div>
    </div>
</div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
   

<script>
// CSRF Token untuk AJAX
const csrfToken = "<?= csrf_hash() ?>";
let itemCounter = 0;

// Fungsi untuk format currency
function formatCurrency(amount) {
    return 'Rp ' + amount.toLocaleString('id-ID');
}

// Fungsi untuk menghitung terbilang
function terbilang(angka) {
    angka = Math.abs(Math.round(angka));
    const bilangan = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
    
    if (angka < 12) {
        return bilangan[angka] || '';
    } else if (angka < 20) {
        return bilangan[angka - 10] + ' Belas';
    } else if (angka < 100) {
        const hasil_bagi = Math.floor(angka / 10);
        const hasil_mod = angka % 10;
        return bilangan[hasil_bagi] + ' Puluh ' + bilangan[hasil_mod];
    } else if (angka < 200) {
        return 'Seratus ' + terbilang(angka - 100);
    } else if (angka < 1000) {
        const hasil_bagi = Math.floor(angka / 100);
        const hasil_mod = angka % 100;
        return bilangan[hasil_bagi] + ' Ratus ' + terbilang(hasil_mod);
    } else if (angka < 2000) {
        return 'Seribu ' + terbilang(angka - 1000);
    } else if (angka < 1000000) {
        const hasil_bagi = Math.floor(angka / 1000);
        const hasil_mod = angka % 1000;
        return terbilang(hasil_bagi) + ' Ribu ' + terbilang(hasil_mod);
    } else if (angka < 1000000000) {
        const hasil_bagi = Math.floor(angka / 1000000);
        const hasil_mod = angka % 1000000;
        return terbilang(hasil_bagi) + ' Juta ' + terbilang(hasil_mod);
    } else if (angka < 1000000000000) {
        const hasil_bagi = Math.floor(angka / 1000000000);
        const hasil_mod = angka % 1000000000;
        return terbilang(hasil_bagi) + ' Miliar ' + terbilang(hasil_mod);
    }
    
    return 'Angka terlalu besar';
}

// Fungsi untuk menambahkan baris item
function addItemRow(itemData = null) {
    const container = document.getElementById('itemsContainer');
    const row = document.createElement('tr');
    row.id = `itemRow_${itemCounter}`;
    
    const itemNumber = itemCounter + 1;
    const defaultItem = itemData || {
        nama_item: '',
        deskripsi: '',
        qty: 1,
        satuan: 'unit',
        harga_satuan: 0
    };
    
    row.innerHTML = `
        <td class="item-number">${itemNumber}</td>
        <td>
            <input type="text" class="form-control form-control-sm item-name" 
                   name="items[${itemCounter}][nama_item]" placeholder="Nama item" 
                   value="${defaultItem.nama_item}" required>
            <textarea class="form-control form-control-sm mt-1 item-desc" 
                      name="items[${itemCounter}][deskripsi]" placeholder="Deskripsi item" 
                      rows="2">${defaultItem.deskripsi}</textarea>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm qty" 
                   name="items[${itemCounter}][qty]" value="${defaultItem.qty}" 
                   step="0.01" min="0" required onchange="calculateTotal()">
        </td>
        <td>
            <select class="form-select form-select-sm unit" name="items[${itemCounter}][satuan]">
                <option value="unit" ${defaultItem.satuan === 'unit' ? 'selected' : ''}>Unit</option>
                <option value="pcs" ${defaultItem.satuan === 'pcs' ? 'selected' : ''}>Pcs</option>
                <option value="set" ${defaultItem.satuan === 'set' ? 'selected' : ''}>Set</option>
                <option value="meter" ${defaultItem.satuan === 'meter' ? 'selected' : ''}>Meter</option>
                <option value="lot" ${defaultItem.satuan === 'lot' ? 'selected' : ''}>Lot</option>
                <option value="roll" ${defaultItem.satuan === 'roll' ? 'selected' : ''}>Roll</option>
                <option value="buah" ${defaultItem.satuan === 'buah' ? 'selected' : ''}>Buah</option>
            </select>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm harga-satuan" 
                   name="items[${itemCounter}][harga_satuan]" value="${defaultItem.harga_satuan}" 
                   step="1000" min="0" required onchange="calculateTotal()">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm item-total" readonly>
        </td>
        <td class="item-actions">
            <button type="button" class="btn btn-danger btn-sm" onclick="removeItemRow(${itemCounter})">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    
    container.appendChild(row);
    itemCounter++;
    
    // Hitung total item ini
    calculateItemTotal(itemCounter - 1);
    calculateTotal();
}

// Fungsi untuk menghapus baris item
function removeItemRow(id) {
    const row = document.getElementById(`itemRow_${id}`);
    if (row && confirm('Hapus item ini?')) {
        row.remove();
        updateItemNumbers();
        calculateTotal();
    }
}

// Fungsi untuk memperbarui nomor item
function updateItemNumbers() {
    const rows = document.querySelectorAll('#itemsContainer tr');
    rows.forEach((row, index) => {
        const numberCell = row.querySelector('.item-number');
        if (numberCell) {
            numberCell.textContent = index + 1;
            
            // Update input names
            const inputs = row.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                const oldName = input.getAttribute('name');
                if (oldName) {
                    const newName = oldName.replace(/items\[\d+\]/, `items[${index}]`);
                    input.setAttribute('name', newName);
                }
            });
            
            // Update row id
            row.id = `itemRow_${index}`;
            
            // Update button onclick
            const removeBtn = row.querySelector('button[onclick*="removeItemRow"]');
            if (removeBtn) {
                removeBtn.setAttribute('onclick', `removeItemRow(${index})`);
            }
        }
    });
    itemCounter = rows.length;
}

// Fungsi untuk menghitung total per item
function calculateItemTotal(index) {
    const row = document.getElementById(`itemRow_${index}`);
    if (!row) return 0;
    
    const qty = parseFloat(row.querySelector('.qty')?.value) || 0;
    const harga = parseFloat(row.querySelector('.harga-satuan')?.value) || 0;
    const itemSubtotal = qty * harga;
    
    const itemTotalField = row.querySelector('.item-total');
    if (itemTotalField) {
        itemTotalField.value = formatCurrency(itemSubtotal);
    }
    
    return itemSubtotal;
}

// Fungsi untuk menghitung total keseluruhan
function calculateTotal() {
    let subtotal = 0;
    
    // Calculate subtotal from all items
    document.querySelectorAll('#itemsContainer tr').forEach((row, index) => {
        subtotal += calculateItemTotal(index);
    });
    
    // Calculate PPN (11%)
    const ppn = subtotal * 0.11;
    const total = subtotal + ppn;
    
    // Update display
    document.getElementById('totalSubtotal').textContent = formatCurrency(subtotal);
    document.getElementById('totalPpn').textContent = formatCurrency(ppn);
    document.getElementById('grandTotal').textContent = formatCurrency(total);
    
    // Update in words
    const inWordsText = terbilang(total) + ' rupiah';
    document.getElementById('inWords').textContent = `#${inWordsText}#`;
}

// Load project details when project is selected
document.getElementById('project_id').addEventListener('change', function() {
    const projectId = this.value;
    if (projectId) {
        // Show loading
        const clientInfo = document.getElementById('clientInfo');
        clientInfo.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading client data...</div>';
        
        fetch(`<?= base_url('direktur/penawaran/getProjectDetails/') ?>${projectId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.project) {
                    const project = data.project;
                    
                    // Update HTML dengan data client
                    const clientInfoHTML = `
                        <h6 class="mb-2">Informasi Client:</h6>
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="form-label small">Attention (Attn)</label>
                                <input type="text" class="form-control form-control-sm" 
                                       name="client_attention" placeholder="Contoh: Ibu Clarissa"
                                       value="${project.nama_kontak || ''}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label small">Kontak</label>
                                <input type="text" class="form-control form-control-sm" 
                                       name="client_contact" placeholder="Nomor telepon"
                                       value="${project.client_telepon || ''}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label small">Due Date</label>
                                <input type="date" class="form-control form-control-sm" 
                                       name="due_date" value="<?= date('Y-m-d', strtotime('+14 days')) ?>">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small">Customer Code</label>
                                <input type="text" class="form-control form-control-sm" 
                                       id="customer_code" readonly value="${project.kode_client || ''}">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small">PO Number</label>
                                <input type="text" class="form-control form-control-sm" 
                                       name="client_po_number" placeholder="PO No.">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small">Alamat Pengiriman</label>
                                <textarea class="form-control form-control-sm" id="client_address" 
                                          rows="2" readonly>${project.client_alamat || ''}</textarea>
                            </div>
                        </div>
                    `;
                    
                    clientInfo.innerHTML = clientInfoHTML;
                } else {
                    clientInfo.innerHTML = '<div class="text-danger">Gagal memuat data client</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                clientInfo.innerHTML = '<div class="text-danger">Error loading client data</div>';
            });
    } else {
        // Reset client info jika tidak ada project yang dipilih
        const clientInfo = document.getElementById('clientInfo');
        clientInfo.innerHTML = `
            <h6 class="mb-2">Informasi Client:</h6>
            <div class="text-muted">Pilih project terlebih dahulu</div>
        `;
    }
});

function saveNewClient() {
    const form = document.getElementById('addClientForm');
    const formData = new FormData(form);
    
    // Validasi mandatory field
    const namaPerusahaan = document.getElementById('nama_perusahaan').value.trim();
    if (!namaPerusahaan) {
        alert('Nama Perusahaan wajib diisi');
        document.getElementById('nama_perusahaan').focus();
        return;
    }
    
    // Validasi email jika diisi
    const email = document.getElementById('email').value.trim();
    if (email && !validateEmail(email)) {
        alert('Format email tidak valid');
        document.getElementById('email').focus();
        return;
    }
    
    // Show loading
    const saveBtn = form.closest('.modal-content').querySelector('.btn-modern-primary');
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    saveBtn.disabled = true;
    
    // Tambahkan CSRF token
    formData.append('csrf_token', csrfToken);
    formData.append('<?= csrf_token() ?>', csrfToken);
    
    console.log('Sending client data...', Object.fromEntries(formData));
    
    fetch('<?= base_url('direktur/penawaran/createClient') ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        
        // Cek jika response error
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
        
        if (data.success) {
            // Add new client to dropdown in project modal
            const select = document.getElementById('new_client_id');
            const option = document.createElement('option');
            option.value = data.client_id;
            option.textContent = `${data.kode_client} - ${data.nama_perusahaan}`;
            select.appendChild(option);
            
            // Select the new client
            select.value = data.client_id;
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('addClientModal'));
            if (modal) {
                modal.hide();
            }
            
            // Clear form
            form.reset();
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Client berhasil dibuat: ' + data.nama_perusahaan,
                timer: 2000,
                showConfirmButton: false
            });
            
        } else {
            // Tampilkan error detail jika ada
            let errorMsg = data.message || 'Gagal menyimpan client';
            
            if (data.errors) {
                errorMsg += '\n\nDetail:\n';
                for (const [field, message] of Object.entries(data.errors)) {
                    errorMsg += `• ${field}: ${message}\n`;
                }
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: errorMsg,
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
        
        Swal.fire({
            icon: 'error',
            title: 'Kesalahan Jaringan',
            text: 'Terjadi kesalahan jaringan. Silakan coba lagi.\n' + error.message,
            confirmButtonText: 'OK'
        });
    });
}

// Fungsi validasi email sederhana
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Save new project via AJAX
function saveNewProject() {
    const form = document.getElementById('addProjectForm');
    const formData = new FormData(form);
    
    // Validasi
    const namaProject = form.querySelector('#nama_project').value;
    const clientId = form.querySelector('#new_client_id').value;
    
    if (!namaProject.trim()) {
        alert('Nama project wajib diisi');
        return;
    }
    
    if (!clientId) {
        alert('Client wajib dipilih');
        return;
    }
    
    // Show loading
    const saveBtn = form.closest('.modal-content').querySelector('.btn-modern-primary');
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    saveBtn.disabled = true;
    
    fetch('<?= base_url('direktur/penawaran/createProject') ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
        
        if (data.success) {
            // Add new project to main dropdown
            const select = document.getElementById('project_id');
            const option = document.createElement('option');
            option.value = data.project_id;
            option.textContent = `${data.kode_project} - ${data.nama_project}`;
            select.appendChild(option);
            
            // Select the new project
            select.value = data.project_id;
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('addProjectModal'));
            if (modal) {
                modal.hide();
            }
            
            // Clear form
            form.reset();
            
            // Show success message
            alert('Project berhasil dibuat: ' + data.nama_project);
            
            // Trigger change event to load project details
            select.dispatchEvent(new Event('change'));
        } else {
            alert('Gagal menyimpan project: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
        alert('Terjadi kesalahan jaringan. Silakan coba lagi.');
    });
}

// Form validation sebelum submit
document.getElementById('penawaranForm').addEventListener('submit', function(e) {
    // Validasi minimal ada 1 item
    const itemRows = document.querySelectorAll('#itemsContainer tr');
    if (itemRows.length === 0) {
        e.preventDefault();
        alert('Minimal harus ada 1 item penawaran.');
        return;
    }
    
    // Validasi setiap item memiliki nama dan harga
    let validItems = true;
    itemRows.forEach((row, index) => {
        const namaItem = row.querySelector('.item-name')?.value?.trim();
        const hargaSatuan = row.querySelector('.harga-satuan')?.value;
        
        if (!namaItem || !hargaSatuan || parseFloat(hargaSatuan) <= 0) {
            validItems = false;
        }
    });
    
    if (!validItems) {
        e.preventDefault();
        alert('Semua item harus memiliki nama dan harga yang valid.');
        return;
    }
});

// Initialize form dengan 1 item default
document.addEventListener('DOMContentLoaded', function() {
    // Add first item row
    addItemRow({
        nama_item: 'Jasa Instalasi CCTV',
        deskripsi: 'Instalasi CCTV lengkap dengan pengkabelan',
        qty: 1,
        satuan: 'unit',
        harga_satuan: 5000000
    });
    
    // Set tanggal penawaran default ke hari ini
    const tanggalPenawaran = document.getElementById('tanggal_penawaran');
    if (tanggalPenawaran) {
        const today = new Date().toISOString().split('T')[0];
        tanggalPenawaran.value = today;
    }
    
    // Set due date default 14 hari dari sekarang
    const dueDate = document.querySelector('input[name="due_date"]');
    if (dueDate) {
        const futureDate = new Date();
        futureDate.setDate(futureDate.getDate() + 14);
        dueDate.value = futureDate.toISOString().split('T')[0];
    }
});
</script>

<?= $this->include('direktur/templates/footer') ?>