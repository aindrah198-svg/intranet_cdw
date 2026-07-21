<?= $this->include('teknisi/templates/header') ?>
<?= $this->include('teknisi/templates/sidebar') ?>
<?= $this->include('teknisi/templates/navbar') ?>

<!-- Konten utama -->
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><?= esc($title ?? 'Detail Pengeluaran') ?></h4>
            <p class="text-muted mb-0">Informasi lengkap pengeluaran proyek</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('teknisi/tugas-proyek/tambahan-barang') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
            <a href="<?= base_url('teknisi/tugas-proyek/tambahan-barang/edit/' . $pengeluaran->id) ?>" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <button type="button" class="btn btn-danger" onclick="confirmDelete(<?= $pengeluaran->id ?>)">
                <i class="fas fa-trash me-2"></i>Hapus
            </button>
        </div>
    </div>
    
    <!-- Alert Messages -->
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <!-- Info Proyek Card -->
    <div class="dashboard-card mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="fas fa-project-diagram me-2 text-primary"></i>Informasi Proyek</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="35%">Nomor SPK</th>
                            <td width="5%">:</td>
                            <td><strong><?= esc($pengeluaran->nomor_spk ?? '-') ?></strong></td>
                        </tr>
                        <tr>
                            <th>Judul Pekerjaan</th>
                            <td>:</td>
                            <td><?= esc($pengeluaran->judul_pekerjaan ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>ID Proyek</th>
                            <td>:</td>
                            <td><span class="badge bg-info">#<?= $pengeluaran->spk_id ?></span></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="35%">Tanggal Pengeluaran</th>
                            <td width="5%">:</td>
                            <td><?= date('d F Y', strtotime($pengeluaran->tanggal)) ?></td>
                        </tr>
                        <tr>
                            <th>Dicatat Oleh</th>
                            <td>:</td>
                            <td><?= esc($pengeluaran->created_by_nama ?? 'System') ?></td>
                        </tr>
                        <tr>
                            <th>Waktu Input</th>
                            <td>:</td>
                            <td><?= date('d F Y H:i', strtotime($pengeluaran->created_at)) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Detail Pengeluaran Card -->
    <div class="dashboard-card mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-receipt me-2 text-primary"></i>Detail Pengeluaran</h5>
            <span class="badge <?= getJenisBadgeColor($pengeluaran->jenis) ?> px-3 py-2"><?= $pengeluaran->jenis ?></span>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Informasi Pengeluaran -->
                <div class="col-md-8">
                    <table class="table table-bordered">
                        <tr>
                            <th width="25%">Nama Pengeluaran</th>
                            <td width="5%">:</td>
                            <td><strong><?= esc($pengeluaran->nama_pengeluaran) ?></strong></td>
                        </tr>
                        <?php if($pengeluaran->no_ref): ?>
                        <tr>
                            <th>No. Referensi</th>
                            <td>:</td>
                            <td><?= esc($pengeluaran->no_ref) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <th>Jumlah Biaya</th>
                            <td>:</td>
                            <td>
                                <h5 class="text-primary mb-0">Rp <?= number_format($pengeluaran->jumlah, 0, ',', '.') ?></h5>
                            </td>
                        </tr>
                        <?php if(!empty($pengeluaran->deskripsi)): ?>
                        <tr>
                            <th>Deskripsi</th>
                            <td>:</td>
                            <td><?= nl2br(esc((string)$pengeluaran->deskripsi)) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <th>Status</th>
                            <td>:</td>
                            <td>
                                <span class="badge bg-success">Aktif</span>
                            </td>
                        </tr>
                    </table>
                    
                    <!-- Ringkasan Biaya -->
                    <div class="alert alert-info mt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Informasi:</strong> Data ini dicatat pada <?= date('d F Y H:i', strtotime($pengeluaran->created_at)) ?> 
                                <?php if($pengeluaran->updated_at != $pengeluaran->created_at): ?>
                                <br><small class="text-muted">Terakhir diperbarui: <?= date('d F Y H:i', strtotime($pengeluaran->updated_at)) ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Foto Nota / Bukti -->
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0"><i class="fas fa-image me-2"></i>Foto Nota / Bukti</h6>
                        </div>
                        <div class="card-body text-center">
                            <?php 
                            $foto_nota = $pengeluaran->foto_nota ?? '';
                            if(!empty($foto_nota) && file_exists($foto_nota)): 
                                $file_ext = pathinfo($foto_nota, PATHINFO_EXTENSION);
                                if(in_array(strtolower($file_ext), ['jpg', 'jpeg', 'png'])): 
                            ?>
                                    <a href="<?= base_url($foto_nota) ?>" target="_blank">
                                        <img src="<?= base_url($foto_nota) ?>" alt="Nota" class="img-fluid rounded mb-3" style="max-height: 200px; cursor: pointer;">
                                    </a>
                                    <div>
                                        <a href="<?= base_url($foto_nota) ?>" target="_blank" class="btn btn-sm btn-primary">
                                            <i class="fas fa-download me-1"></i>Lihat Fullscreen
                                        </a>
                                    </div>
                                <?php elseif(strtolower($file_ext) == 'pdf'): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-file-pdf text-danger" style="font-size: 64px;"></i>
                                        <p class="mt-2 mb-2">File PDF</p>
                                        <a href="<?= base_url($foto_nota) ?>" target="_blank" class="btn btn-sm btn-danger">
                                            <i class="fas fa-file-pdf me-1"></i>Buka PDF
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="fas fa-file text-muted" style="font-size: 64px;"></i>
                                        <p class="text-muted mt-3 mb-0">File tidak dikenal</p>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-image text-muted" style="font-size: 64px;"></i>
                                    <p class="text-muted mt-3 mb-0">Tidak ada foto nota</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Informasi Tambahan Card -->
    <div class="dashboard-card">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="fas fa-clipboard-list me-2 text-primary"></i>Riwayat Pengeluaran Tambahan Proyek</h5>
        </div>
        <div class="card-body">
            <?php
            // Panggil model untuk mendapatkan data pengeluaran lain dari SPK yang sama
            $pengeluaranModel = new \App\Models\Teknisi\SpkInstalasiPengeluaranModel();
            $riwayat_pengeluaran = $pengeluaranModel->select('spk_instalasi_pengeluaran.*, users.name as created_by_nama')
                ->join('users', 'users.id = spk_instalasi_pengeluaran.created_by', 'left')
                ->where('spk_id', $pengeluaran->spk_id)
                ->orderBy('tanggal', 'DESC')
                ->orderBy('created_at', 'DESC')
                ->findAll();
            
            // Hitung total pengeluaran
            $total_pengeluaran = 0;
            foreach($riwayat_pengeluaran as $item) {
                $total_pengeluaran += $item->jumlah;
            }
            ?>
            
            <!-- Menampilkan total pengeluaran proyek -->
            <div class="alert alert-warning mb-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <i class="fas fa-chart-pie me-2"></i>
                        <strong>Total Pengeluaran Proyek Ini:</strong>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h4 class="mb-0 text-primary">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
            
            <!-- Tabel riwayat pengeluaran -->
            <div class="table-responsive">
                <table class="table table-hover" id="riwayatTable">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Pengeluaran</th>
                            <th>Jenis</th>
                            <th class="text-end">Jumlah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="riwayatBody">
                        <?php if(count($riwayat_pengeluaran) > 0): ?>
                            <?php foreach($riwayat_pengeluaran as $item): ?>
                                <?php 
                                $isCurrent = ($item->id == $pengeluaran->id);
                                $rowClass = $isCurrent ? 'table-primary' : '';
                                ?>
                                <tr class="<?= $rowClass ?>">
                                    <td><?= date('d M Y', strtotime($item->tanggal)) ?></td>
                                    <td>
                                        <?php if($isCurrent): ?>
                                            <strong><?= esc($item->nama_pengeluaran) ?></strong>
                                        <?php else: ?>
                                            <?= esc($item->nama_pengeluaran) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= getJenisBadgeColor($item->jenis) ?>">
                                            <?= esc($item->jenis) ?>
                                        </span>
                                    </td>
                                    <td class="text-end">Rp <?= number_format($item->jumlah, 0, ',', '.') ?></td>
                                    <td>
                                        <a href="<?= base_url('teknisi/tugas-proyek/tambahan-barang/detail/' . $item->id) ?>" class="btn btn-sm btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-inbox text-muted" style="font-size: 48px;"></i>
                                    <p class="text-muted mt-2">Belum ada pengeluaran untuk proyek ini</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Form Delete (Hidden) -->
<form id="deleteForm" method="post" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="DELETE">
</form>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Hapus Pengeluaran?',
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $('#deleteForm').attr('action', '<?= base_url("teknisi/tugas-proyek/tambahan-barang/delete") ?>/' + id);
            $('#deleteForm').submit();
        }
    });
}

// Tampilkan pesan error dari session
<?php if(session()->getFlashdata('error')): ?>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '<?= session()->getFlashdata('error') ?>',
        confirmButtonText: 'OK'
    });
<?php endif; ?>

<?php if(session()->getFlashdata('success')): ?>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '<?= session()->getFlashdata('success') ?>',
        confirmButtonText: 'OK'
    });
<?php endif; ?>
</script>

<style>
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
.table-borderless td, .table-borderless th {
    padding: 0.5rem 0;
}
.table-bordered th {
    background-color: #f8f9fc;
    vertical-align: middle;
}
.badge {
    font-size: 0.85rem;
    font-weight: 500;
}
.table-primary {
    background-color: #e8f4ff !important;
}
.alert-info {
    background-color: #e7f1ff;
    border-color: #b8daff;
    color: #004085;
}
.img-fluid {
    transition: transform 0.3s;
}
.img-fluid:hover {
    transform: scale(1.02);
}
.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>

<!-- Helper function untuk badge warna -->
<?php
function getJenisBadgeColor($jenis) {
    $colors = [
        'Bensin' => 'bg-warning text-dark',
        'Tol' => 'bg-info text-white',
        'Makan' => 'bg-success text-white',
        'Akomodasi' => 'bg-primary text-white',
        'Material Tambahan' => 'bg-secondary text-white',
        'Lainnya' => 'bg-dark text-white'
    ];
    return $colors[$jenis] ?? 'bg-secondary text-white';
}
?>

<?= $this->include('teknisi/templates/footer') ?>