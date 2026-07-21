<?php
$title = 'Detail Dokumen';
$active = 'dokumen';
$is_dokumen_page = true;
$css = [];
$scripts = [];
?>

<?= $this->include('admin/templates/header') ?>
<?= $this->include('admin/templates/sidebar') ?>
<?= $this->include('admin/templates/navbar') ?>

<!-- Main Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Dokumen</h1>
        <div>
            <a href="<?= base_url('admin/karyawan/dokumen'); ?>" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
            </a>
            <a href="<?= base_url('admin/karyawan/dokumen/edit/' . $dokumen['id']); ?>" class="btn btn-sm btn-warning shadow-sm">
                <i class="fas fa-edit fa-sm text-white-50"></i> Edit
            </a>
        </div>
    </div>

    <!-- Debug CSRF Token (Hapus setelah testing) -->
    <div class="alert alert-info alert-dismissible fade show d-none" id="debugInfo">
        CSRF Token: <?= csrf_token() ?><br>
        CSRF Hash: <?= csrf_hash() ?><br>
        Current URL: <?= current_url() ?><br>
        ID Dokumen: <?= $dokumen['id'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Dokumen Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-alt me-2"></i>Informasi Dokumen
                    </h6>
                    <div>
                        <span class="badge bg-<?= 
                            $dokumen['status'] == 'pending' ? 'warning' : 
                            ($dokumen['status'] == 'diterima' ? 'success' : 'danger') 
                        ?>">
                            <?= $statusOptions[$dokumen['status']] ?? $dokumen['status'] ?>
                        </span>
                        <span class="badge bg-info ms-2">
                            <?= $jenisOptions[$dokumen['jenis']] ?? $dokumen['jenis'] ?>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Kolom Kiri - Informasi Dokumen -->
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Nama File</th>
                                    <td><?= esc($dokumen['nama_file']) ?></td>
                                </tr>
                                <tr>
                                    <th>Jenis Dokumen</th>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= $jenisOptions[$dokumen['jenis']] ?? $dokumen['jenis'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Nomor Dokumen</th>
                                    <td><?= !empty($dokumen['nomor_dokumen']) ? esc($dokumen['nomor_dokumen']) : '<span class="text-muted">-</span>' ?></td>
                                </tr>
                                <tr>
                                    <th>Ukuran File</th>
                                    <td><?= $dokumen['ukuran'] ? number_format($dokumen['ukuran'] / 1024, 2) . ' KB' : '-' ?></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge bg-<?= 
                                            $dokumen['status'] == 'pending' ? 'warning' : 
                                            ($dokumen['status'] == 'diterima' ? 'success' : 'danger') 
                                        ?>">
                                            <?= $statusOptions[$dokumen['status']] ?? $dokumen['status'] ?>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <!-- Kolom Kanan - Informasi Karyawan -->
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Karyawan</th>
                                    <td>
                                        <strong><?= esc($dokumen['nama_lengkap']) ?></strong><br>
                                        <small class="text-muted">NIK: <?= esc($dokumen['nik']) ?></small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Departemen</th>
                                    <td><?= !empty($dokumen['departemen']) ? esc($dokumen['departemen']) : '<span class="text-muted">-</span>' ?></td>
                                </tr>
                                <tr>
                                    <th>Jabatan</th>
                                    <td><?= !empty($dokumen['jabatan']) ? esc($dokumen['jabatan']) : '<span class="text-muted">-</span>' ?></td>
                                </tr>
                                <tr>
                                    <th>Tanggal Berlaku</th>
                                    <td><?= !empty($dokumen['tanggal_berlaku']) ? date('d/m/Y', strtotime($dokumen['tanggal_berlaku'])) : '<span class="text-muted">-</span>' ?></td>
                                </tr>
                                <tr>
                                    <th>Tanggal Kadaluarsa</th>
                                    <td>
                                        <?php if (!empty($dokumen['tanggal_kadaluarsa'])): ?>
                                            <?php 
                                            $expired = strtotime($dokumen['tanggal_kadaluarsa']) < time();
                                            ?>
                                            <span class="<?= $expired ? 'text-danger' : 'text-success' ?>">
                                                <?= date('d/m/Y', strtotime($dokumen['tanggal_kadaluarsa'])) ?>
                                                <?php if ($expired): ?>
                                                    <span class="badge bg-danger">Kadaluarsa</span>
                                                <?php endif; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Keterangan -->
                    <?php 
                    // Ambil keterangan dengan pengecekan tipe data
                    $keterangan = '';
                    if (isset($dokumen['keterangan'])) {
                        if (is_string($dokumen['keterangan'])) {
                            $keterangan = $dokumen['keterangan'];
                        } elseif (is_array($dokumen['keterangan'])) {
                            $keterangan = implode(', ', array_filter($dokumen['keterangan']));
                        }
                    }
                    ?>
                    
                    <?php if (!empty(trim($keterangan))): ?>
                        <div class="mt-4">
                            <h6 class="font-weight-bold text-primary mb-2">Keterangan</h6>
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <?= nl2br(esc($keterangan)) ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- File Preview -->
                    <div class="mt-4">
                        <h6 class="font-weight-bold text-primary mb-2">File Dokumen</h6>
                        <div class="card">
                            <div class="card-body text-center">
                                <?php
                                $fileExt = strtolower(pathinfo($dokumen['nama_file'], PATHINFO_EXTENSION));
                                $fileIcon = 'fa-file';
                                $previewClass = '';
                                
                                if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif'])) {
                                    $fileIcon = 'fa-file-image';
                                    $previewClass = 'image-preview';
                                } elseif ($fileExt == 'pdf') {
                                    $fileIcon = 'fa-file-pdf';
                                    $previewClass = 'pdf-preview';
                                } elseif (in_array($fileExt, ['doc', 'docx'])) {
                                    $fileIcon = 'fa-file-word';
                                } else {
                                    $fileIcon = 'fa-file-alt';
                                }
                                ?>
                                
                                <div class="mb-3">
                                    <i class="fas <?= $fileIcon ?> fa-4x text-primary"></i>
                                </div>
                                
                                <h5 class="mb-2"><?= esc($dokumen['nama_file']) ?></h5>
                                <p class="text-muted mb-3">
                                    <?= number_format($dokumen['ukuran'] / 1024, 2) ?> KB • 
                                    Diupload: <?= date('d/m/Y H:i', strtotime($dokumen['created_at'])) ?>
                                </p>
                                
                                <div class="d-flex justify-content-center gap-2 flex-wrap">
                                    <a href="<?= base_url('admin/karyawan/dokumen/download/' . $dokumen['id']); ?>" 
                                       class="btn btn-primary mb-2">
                                        <i class="fas fa-download me-1"></i> Download
                                    </a>
                                    
   <a href="<?= base_url('admin/karyawan/dokumen/preview/' . $dokumen['id']); ?>" 
                   target="_blank" class="btn btn-info mb-2">
                    <i class="fas fa-external-link-alt me-1"></i> Preview
                </a>
                
                <!-- Form untuk Setujui - COPY DARI TOLAK -->
                <?php if ($dokumen['status'] != 'diterima'): ?>
                <form action="<?= base_url('admin/karyawan/dokumen/update-status/' . $dokumen['id']); ?>" 
                      method="post" class="d-inline mb-2">
                    <!-- CSRF Token - PASTIKAN SAMA DENGAN TOLAK -->
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                    <input type="hidden" name="status" value="diterima">
                    <button type="submit" class="btn btn-success" 
                            onclick="return confirm('Apakah Anda yakin ingin menyetujui dokumen ini?')">
                        <i class="fas fa-check me-1"></i> Setujui
                    </button>
                </form>
                <?php endif; ?>
                
                <!-- Form untuk Tolak - JANGAN DIUBAH (SUDAH BERFUNGSI) -->
                <?php if ($dokumen['status'] != 'ditolak'): ?>
                <form action="<?= base_url('admin/karyawan/dokumen/update-status/' . $dokumen['id']); ?>" 
                      method="post" class="d-inline mb-2">
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                    <input type="hidden" name="status" value="ditolak">
                    <button type="submit" class="btn btn-danger"
                            onclick="return confirm('Apakah Anda yakin ingin menolak dokumen ini?')">
                        <i class="fas fa-times me-1"></i> Tolak
                    </button>
                </form>
                <?php endif; ?>
                
                <!-- Tombol untuk reset ke pending -->
                <?php if ($dokumen['status'] != 'pending'): ?>
                <form action="<?= base_url('admin/karyawan/dokumen/update-status/' . $dokumen['id']); ?>" 
                      method="post" class="d-inline mb-2">
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                    <input type="hidden" name="status" value="pending">
                    <button type="submit" class="btn btn-warning"
                            onclick="return confirm('Apakah Anda yakin ingin mengembalikan status ke pending?')">
                        <i class="fas fa-history me-1"></i> Reset ke Pending
                    </button>
                </form>
                <?php endif; ?>
            </div>
                                
                                <!-- Link untuk testing langsung -->
                                <div class="mt-3">
                                    <small class="text-muted">Debug Links:</small><br>
                                    <a href="javascript:void(0)" onclick="testAjax('diterima')" class="small me-2">Test AJAX Setujui</a> | 
                                    <a href="javascript:void(0)" onclick="testAjax('ditolak')" class="small me-2">Test AJAX Tolak</a> | 
                                    <a href="javascript:void(0)" onclick="testDirectLink('diterima')" class="small">Test Direct Link</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Informasi Metadata -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="font-weight-bold text-primary mb-3">
                                        <i class="fas fa-history me-2"></i>Riwayat
                                    </h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td width="50%"><small>Dibuat</small></td>
                                            <td><small><?= date('d/m/Y H:i', strtotime($dokumen['created_at'])) ?></small></td>
                                        </tr>
                                        <tr>
                                            <td><small>Terakhir Diupdate</small></td>
                                            <td><small><?= date('d/m/Y H:i', strtotime($dokumen['updated_at'])) ?></small></td>
                                        </tr>
                                        <tr>
                                            <td><small>ID Dokumen</small></td>
                                            <td><small>#<?= $dokumen['id'] ?></small></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="font-weight-bold text-primary mb-3">
                                        <i class="fas fa-cog me-2"></i>Aksi
                                    </h6>
                                    <div class="d-grid gap-2">
                                        <a href="<?= base_url('admin/karyawan/dokumen/edit/' . $dokumen['id']); ?>" 
                                           class="btn btn-warning">
                                            <i class="fas fa-edit me-1"></i> Edit Dokumen
                                        </a>
                                        
                                        <a href="<?= base_url('admin/karyawan/dokumen/' . $dokumen['karyawan_id']); ?>" 
                                           class="btn btn-info">
                                            <i class="fas fa-folder me-1"></i> Semua Dokumen Karyawan
                                        </a>
                                        
                                        <form action="<?= base_url('admin/karyawan/dokumen/delete/' . $dokumen['id']); ?>" 
                                              method="post" class="d-grid" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')">
                                            <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash me-1"></i> Hapus Dokumen
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview Gambar -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Preview Gambar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" id="previewImage" class="img-fluid" style="max-height: 70vh;">
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Tampilkan debug info
        $('#debugInfo').removeClass('d-none');
        
        // Test fungsi AJAX
        window.testAjax = function(status) {
            console.log('Testing AJAX for status:', status);
            
            $.ajax({
                url: '<?= base_url('admin/karyawan/dokumen/update-status/' . $dokumen['id']) ?>',
                type: 'POST',
                data: {
                    status: status,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    console.log('AJAX Success:', response);
                    if (response.success) {
                        alert('Success: ' + response.message);
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr.responseText);
                    alert('AJAX Error: ' + xhr.status + ' - ' + xhr.responseText);
                }
            });
        };
        
        // Test direct link (form submission)
        window.testDirectLink = function(status) {
            console.log('Testing direct link for status:', status);
            
            // Create a temporary form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= base_url('admin/karyawan/dokumen/update-status/' . $dokumen['id']) ?>';
            
            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '<?= csrf_token() ?>';
            csrfInput.value = '<?= csrf_hash() ?>';
            form.appendChild(csrfInput);
            
            // Add status
            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = status;
            form.appendChild(statusInput);
            
            // Submit the form
            document.body.appendChild(form);
            form.submit();
        };
        
        // Log form submissions
        $('form').on('submit', function(e) {
            console.log('Form submitted:', this.action);
            console.log('Form data:', $(this).serialize());
        });
        
        // Fungsi untuk menampilkan alert
        function showAlert(type, message) {
            // Hapus alert sebelumnya
            $('.alert-custom').remove();
            
            // Buat alert baru
            const alert = $('<div class="alert alert-' + type + ' alert-dismissible fade show alert-custom" role="alert">' +
                message +
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                '</div>');
            
            // Tambahkan di atas card
            $('.card.shadow.mb-4').before(alert);
            
            // Auto-hide setelah 5 detik
            setTimeout(() => {
                alert.alert('close');
            }, 5000);
        }

        // Preview gambar
        $('.image-preview').click(function(e) {
            e.preventDefault();
            var imageUrl = $(this).attr('href');
            $('#previewImage').attr('src', imageUrl);
            $('#imagePreviewModal').modal('show');
        });

        // Auto-hide alerts setelah 5 detik
        setTimeout(function() {
            $('.alert:not(.alert-custom)').alert('close');
        }, 5000);
    });
</script>

<style>
    .badge {
        font-size: 0.85em;
        padding: 0.35em 0.65em;
    }
    table.table-borderless td {
        padding: 0.5rem 0;
    }
    .btn-group .btn {
        margin-right: 2px;
    }
    @media (max-width: 768px) {
        .d-flex.justify-content-center.gap-2 {
            flex-direction: column;
            align-items: center;
        }
        .d-flex.justify-content-center.gap-2 .btn {
            width: 100%;
            max-width: 250px;
            margin-bottom: 5px;
        }
    }
    #debugInfo {
        font-size: 12px;
        padding: 5px 10px;
        margin-bottom: 10px;
    }
</style>

<?= $this->include('admin/templates/footer') ?>