<?= $this->include('teknisi/templates/header') ?>
<?= $this->include('teknisi/templates/sidebar') ?>
<?= $this->include('teknisi/templates/navbar') ?>

<!-- Konten utama -->
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><?= esc($title ?? 'Pengeluaran / Tambahan Barang') ?></h4>
            <p class="text-muted mb-0"><?= esc($subtitle ?? 'Kelola semua pengeluaran dan tambahan barang dari setiap proyek') ?></p>
        </div>
        <div>
            <!-- Tombol Export Excel -->
            <button type="button" class="btn btn-success me-2" onclick="showExportModal()" id="btnExport" <?= empty($spk_list) ? 'disabled' : '' ?>>
                <i class="fas fa-file-excel me-2"></i>Export Excel
            </button>
            <a href="<?= base_url('teknisi/tugas-proyek/tambahan-barang/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Pengeluaran
            </a>
            <a href="<?= base_url('teknisi/tugas-proyek/spk') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke SPK
            </a>
        </div>
    </div>
    
    <!-- Statistik -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="dashboard-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="fas fa-wallet fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Pengeluaran</h6>
                            <h3 class="mb-0 fw-bold">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></h3>
                            <?php if($selected_spk_id): ?>
                                <small class="text-muted">(Filter berdasarkan SPK)</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="dashboard-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="fas fa-calendar-alt fa-2x text-success"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">
                                <?php if(isset($periode_display)): ?>
                                    Pengeluaran Periode
                                <?php else: ?>
                                    Pengeluaran Bulan Ini
                                <?php endif; ?>
                            </h6>
                            <h3 class="mb-0 fw-bold">Rp <?= number_format($total_pengeluaran_bulan_ini, 0, ',', '.') ?></h3>
                            <small class="text-muted">
                                <?= $periode_display ?? date('F Y') ?>
                                <?php if($selected_spk_id): ?> (Filter SPK)<?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filter -->
    <div class="dashboard-card mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="fas fa-filter me-2 text-primary"></i>Filter</h5>
        </div>
        <div class="card-body">
            <form method="get" action="<?= base_url('teknisi/tugas-proyek/tambahan-barang') ?>" id="filterForm">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold">Proyek / SPK</label>
<select name="spk_id" class="form-select" id="spk_id_filter">
    <option value="">Semua Proyek</option>
    <?php foreach($spk_list as $spk): ?>
        <option value="<?= $spk->id ?>" <?= ($selected_spk_id ?? '') == $spk->id ? 'selected' : '' ?>>
            <?= esc($spk->nomor_spk) ?> - <?= esc($spk->judul_pekerjaan) ?>
            [<?= $spk->status ?? 'Unknown' ?>]
        </option>
    <?php endforeach; ?>
</select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-semibold">Jenis</label>
                        <select name="jenis" class="form-select">
                            <?php foreach($jenis_options as $key => $value): ?>
                                <option value="<?= $key ?>" <?= ($selected_jenis ?? 'semua') == $key ? 'selected' : '' ?>>
                                    <?= $value ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-semibold">Tanggal Awal</label>
                        <input type="date" name="tanggal_awal" class="form-control" value="<?= $selected_tanggal_awal ?? '' ?>" 
                               max="<?= date('Y-m-d') ?>" id="tanggal_awal">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-semibold">Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" class="form-control" value="<?= $selected_tanggal_akhir ?? '' ?>" 
                               max="<?= date('Y-m-d') ?>" id="tanggal_akhir">
                    </div>
                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-2"></i>Filter
                        </button>
                        <a href="<?= base_url('teknisi/tugas-proyek/tambahan-barang') ?>" class="btn btn-secondary me-2">
                            <i class="fas fa-sync-alt me-2"></i>Reset
                        </a>
                        <!-- Tombol Export di samping filter -->
                        <button type="button" class="btn btn-success" onclick="showExportModal()" id="btnExportFilter" <?= empty($spk_list) ? 'disabled' : '' ?>>
                            <i class="fas fa-file-excel"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Tabel Pengeluaran -->
    <div class="dashboard-card">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>Daftar Pengeluaran</h5>
            <span class="badge bg-primary">Total: <?= count($pengeluaran_list) ?> data</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover datatable">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">Tanggal</th>
                            <th width="15%">Proyek / SPK</th>
                            <th width="15%">No. Referensi</th>
                            <th width="10%">Jenis</th>
                            <th width="18%">Nama Pengeluaran</th>
                            <th width="12%">Jumlah</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($pengeluaran_list)): ?>
                            <?php foreach($pengeluaran_list as $key => $item): ?>
                                <tr>
                                    <td><?= $key + 1 ?></td>
                                    <td><?= date('d/m/Y', strtotime($item->tanggal)) ?></td>
                                    <td>
                                        <strong><?= esc($item->nomor_spk ?? '-') ?></strong><br>
                                        <small class="text-muted"><?= esc($item->judul_pekerjaan ?? '-') ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark p-2" style="font-size: 0.85rem; font-family: monospace; border: 1px solid #dee2e6;">
                                            <i class="fas fa-hashtag me-1 text-primary"></i>
                                            <?= esc($item->no_ref ?? '-') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $badgeClass = match($item->jenis) {
                                            'Bensin' => 'primary',
                                            'Tol' => 'info',
                                            'Makan' => 'success',
                                            'Akomodasi' => 'warning',
                                            'Material Tambahan' => 'secondary',
                                            default => 'dark'
                                        };
                                        ?>
                                        <span class="badge bg-<?= $badgeClass ?>"><?= $item->jenis ?></span>
                                    </td>
                                    <td>
                                        <strong><?= esc($item->nama_pengeluaran) ?></strong>
                                        <?php if($item->deskripsi): ?>
                                            <br><small class="text-muted"><?= esc($item->deskripsi) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-bold text-end">Rp <?= number_format($item->jumlah, 0, ',', '.') ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('teknisi/tugas-proyek/tambahan-barang/detail/' . $item->id) ?>" 
                                               class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('teknisi/tugas-proyek/tambahan-barang/edit/' . $item->id) ?>" 
                                               class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="hapusData(<?= $item->id ?>, '<?= esc($item->nama_pengeluaran) ?>')" 
                                                    data-bs-toggle="tooltip" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Belum ada data pengeluaran</p>
                                    <a href="<?= base_url('teknisi/tugas-proyek/tambahan-barang/create') ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus me-2"></i>Tambah Pengeluaran
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Export Excel -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">
                    <i class="fas fa-file-excel text-success me-2"></i>
                    Export Excel Pengeluaran
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="exportForm" method="get" action="">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Proyek / SPK <span class="text-danger">*</span></label>
<select name="spk_id" class="form-select" id="export_spk_id" required>
    <option value="">-- Pilih SPK --</option>
    <?php foreach($spk_list as $spk): ?>
        <option value="<?= $spk->id ?>" data-nomor="<?= esc($spk->nomor_spk) ?>" data-judul="<?= esc($spk->judul_pekerjaan) ?>">
            <?= esc($spk->nomor_spk) ?> - <?= esc($spk->judul_pekerjaan) ?> 
            [<?= $spk->status ?? 'Unknown' ?>]
        </option>
    <?php endforeach; ?>
</select>
                        <div class="form-text">Pilih SPK yang akan diexport datanya</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Uang Akomodasi <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" id="uang_akomodasi" 
                                   placeholder="Masukkan jumlah uang akomodasi" 
                                   value="40.000.000" required>
                        </div>
                        <div class="form-text">Uang akomodasi yang diberikan untuk proyek ini (default: 40.000.000)</div>
                    </div>
                    
                    <!-- Info Total Pengeluaran (akan diupdate via AJAX) -->
                    <div class="alert alert-info" id="infoTotalPengeluaran" style="display: none;">
                        <div class="d-flex justify-content-between">
                            <span>Total Pengeluaran Saat Ini:</span>
                            <strong id="totalPengeluaranText">Rp 0</strong>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="button" class="btn btn-success" id="btnExportSubmit" onclick="processExport()">
                        <i class="fas fa-file-excel me-2"></i>Export Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form Hapus -->
<form id="delete-form" method="post" style="display: none;">
    <?= csrf_field() ?>
</form>

<script>
// Variabel global untuk base URL
var baseUrl = '<?= base_url() ?>';

$(document).ready(function() {
    // Inisialisasi tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Inisialisasi DataTable
    if ($('.datatable').length > 0) {
        $('.datatable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            order: [[1, 'desc']],
            pageLength: 25,
            columnDefs: [
                { orderable: false, targets: [7] } // Kolom aksi tidak bisa diurutkan
            ]
        });
    }

    // Validasi tanggal
    $('#tanggal_awal, #tanggal_akhir').on('change', function() {
        var tanggalAwal = $('#tanggal_awal').val();
        var tanggalAkhir = $('#tanggal_akhir').val();
        
        if (tanggalAwal && tanggalAkhir) {
            if (tanggalAwal > tanggalAkhir) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Tanggal awal tidak boleh lebih besar dari tanggal akhir!',
                    confirmButtonText: 'OK'
                });
                $(this).val('');
            }
        }
    });

    // Format Rupiah untuk input uang akomodasi
    $('#uang_akomodasi').on('keyup', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
            $(this).val(value);
        }
    });

    // Saat memilih SPK di modal, ambil total pengeluaran
    $('#export_spk_id').on('change', function() {
        let spkId = $(this).val();
        if (spkId) {
            // Ambil total pengeluaran via AJAX
            $.ajax({
                url: baseUrl + '/teknisi/tugas-proyek/tambahan-barang/total-by-spk/' + spkId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#totalPengeluaranText').text(response.total_formatted);
                        $('#infoTotalPengeluaran').show();
                    } else {
                        $('#infoTotalPengeluaran').hide();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching total:', error);
                    $('#infoTotalPengeluaran').hide();
                }
            });
        } else {
            $('#infoTotalPengeluaran').hide();
        }
    });
});

function showExportModal() {
    // Reset form
    $('#export_spk_id').val('');
    $('#uang_akomodasi').val('40.000.000');
    $('#infoTotalPengeluaran').hide();
    
    // Cek apakah ada SPK yang dipilih di filter
    let selectedSpkId = $('#spk_id_filter').val();
    if (selectedSpkId) {
        $('#export_spk_id').val(selectedSpkId).trigger('change');
    }
    
    // Tampilkan modal
    $('#exportModal').modal('show');
}

function processExport() {
    let spkId = $('#export_spk_id').val();
    let uangAkomodasi = $('#uang_akomodasi').val().replace(/\D/g, '');
    
    if (!spkId) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Silakan pilih SPK terlebih dahulu'
        });
        return;
    }
    
    if (!uangAkomodasi || parseInt(uangAkomodasi) <= 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Masukkan jumlah uang akomodasi yang valid'
        });
        return;
    }
    
    // Tampilkan loading
    Swal.fire({
        title: 'Memproses Export',
        text: 'Mohon tunggu...',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Ambil CSRF token
    var csrfToken = $('input[name="<?= csrf_token() ?>"]').val();
    
    // Simpan uang akomodasi ke session via AJAX dulu
    $.ajax({
        url: baseUrl + '/teknisi/tugas-proyek/tambahan-barang/set-uang-akomodasi',
        type: 'POST',
        data: {
            spk_id: spkId,
            uang_akomodasi: uangAkomodasi,
            '<?= csrf_token() ?>': csrfToken
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Redirect ke URL export
                window.location.href = baseUrl + '/teknisi/tugas-proyek/tambahan-barang/export-excel/' + spkId;
                
                // Tutup modal setelah redirect
                setTimeout(function() {
                    $('#exportModal').modal('hide');
                    Swal.close();
                }, 1000);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Gagal menyimpan data'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            console.error('Response:', xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.'
            });
        }
    });
}

function hapusData(id, nama) {
    Swal.fire({
        title: 'Hapus Pengeluaran',
        html: `Apakah Anda yakin ingin menghapus pengeluaran <strong>"${nama}"</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $('#delete-form').attr('action', baseUrl + '/teknisi/tugas-proyek/tambahan-barang/delete/' + id);
            $('#delete-form').submit();
        }
    });
}

// Tampilkan pesan dari session
<?php if(session()->getFlashdata('success')): ?>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '<?= session()->getFlashdata('success') ?>',
        showConfirmButton: false,
        timer: 2000
    });
<?php endif; ?>

<?php if(session()->getFlashdata('error')): ?>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '<?= session()->getFlashdata('error') ?>',
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
.form-label.fw-semibold {
    font-weight: 500;
    color: #5a5c69;
}
.datatable tbody tr:hover {
    background-color: #f8f9fc;
}
.badge {
    font-size: 0.8rem;
    padding: 0.4rem 0.6rem;
}
.btn-group .btn {
    padding: 0.25rem 0.5rem;
}
/* Style untuk nomor referensi */
.badge.bg-light {
    background-color: #f8f9fc !important;
    font-weight: 500;
}
/* Style untuk modal */
.modal-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #eaeaea;
}
.modal-footer {
    border-top: 1px solid #eaeaea;
}
/* Style untuk tombol export */
.btn-success {
    background-color: #28a745;
    border-color: #28a745;
}
.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
}
/* Di bagian style, tambahkan */
option.status-selesai {
    color: #28a745;
    font-weight: bold;
}
option.status-dijadwalkan {
    color: #ffc107;
}
option.status-proses {
    color: #0d6efd;
}
</style>

<?= $this->include('teknisi/templates/footer') ?>