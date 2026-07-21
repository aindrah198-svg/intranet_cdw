<?php
// C:\xampp\htdocs\cdwnet\app\Views\admin\absensi\report.php
$title = 'Laporan Absensi';
$active = 'absensi';
$css = ['https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'];
$scripts = ['https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'];
?>

<?= $this->include('admin/templates/header') ?>
<?= $this->include('admin/templates/sidebar') ?>
<?= $this->include('admin/templates/navbar') ?>

<!-- Main Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Laporan Absensi</h1>
        <a href="<?= base_url('admin/absensi'); ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <!-- Content Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Generate Laporan</h6>
        </div>
        <div class="card-body">
            <form id="reportForm">
                <?= csrf_field(); ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="start_date" class="form-label">Dari Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                   value="<?= date('Y-m-01'); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="end_date" class="form-label">Sampai Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                   value="<?= date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="karyawan_id" class="form-label">Karyawan</label>
                            <select class="form-control select2" id="karyawan_id" name="karyawan_id">
                                <option value="">Semua Karyawan</option>
                                <?php if (isset($karyawan)): ?>
                                <?php foreach ($karyawan as $k): ?>
                                    <option value="<?= $k['id']; ?>">
                                        <?= esc($k['nik']); ?> - <?= esc($k['nama_lengkap']); ?>
                                    </option>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex gap-2 mt-4">
                    <button type="button" class="btn btn-primary" onclick="viewReport()">
                        <i class="fas fa-eye me-1"></i> Lihat Laporan (HTML)
                    </button>
                    <button type="button" class="btn btn-success" onclick="downloadExcel()">
                        <i class="fas fa-file-excel me-1"></i> Download Excel
                    </button>
                    <button type="button" class="btn btn-info" onclick="printReport()">
                        <i class="fas fa-print me-1"></i> Print Langsung
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Status Messages -->
    <div id="messageContainer" style="display: none;">
        <div class="alert alert-info" role="alert">
            <i class="fas fa-spinner fa-spin me-2"></i>
            Sedang memproses laporan...
        </div>
    </div>
</div>

<script>
// Initialize Select2
$(document).ready(function() {
    $('.select2').select2({
        placeholder: 'Pilih karyawan',
        allowClear: true,
        width: '100%'
    });
});

// Function to validate form
function validateForm() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    
    if (!startDate || !endDate) {
        alert('Harap pilih tanggal mulai dan tanggal akhir');
        return false;
    }
    
    if (startDate > endDate) {
        alert('Tanggal mulai tidak boleh lebih besar dari tanggal akhir');
        return false;
    }
    
    return true;
}

// Function to show loading message
function showLoading() {
    document.getElementById('messageContainer').style.display = 'block';
}

// Function to hide loading message
function hideLoading() {
    document.getElementById('messageContainer').style.display = 'none';
}

// Function to build URL
function buildUrl(baseUrl, format = '') {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const karyawanId = document.getElementById('karyawan_id').value;
    
    let url = baseUrl + '?start_date=' + encodeURIComponent(startDate) + 
              '&end_date=' + encodeURIComponent(endDate);
    
    if (karyawanId) {
        url += '&karyawan_id=' + encodeURIComponent(karyawanId);
    }
    
    if (format) {
        url += '&format=' + encodeURIComponent(format);
    }
    
    // Add timestamp to prevent caching
    url += '&_=' + Date.now();
    
    return url;
}

// View Report (HTML)
function viewReport() {
    if (!validateForm()) return;
    
    const url = buildUrl('<?= base_url("admin/absensi/export"); ?>', 'html');
    console.log('Opening HTML report:', url);
    
    // Open in new tab
    const newWindow = window.open(url, '_blank');
    if (!newWindow || newWindow.closed || typeof newWindow.closed == 'undefined') {
        alert('Popup diblokir! Izinkan popup untuk situs ini.');
    }
}

// Download Excel
function downloadExcel() {
    if (!validateForm()) return;
    
    const url = buildUrl('<?= base_url("admin/absensi/export/excel"); ?>');
    console.log('Downloading Excel:', url);
    
    // Create hidden iframe for download
    const iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = url;
    document.body.appendChild(iframe);
    
    // Show loading
    showLoading();
    
    // Remove iframe after download
    setTimeout(function() {
        document.body.removeChild(iframe);
        hideLoading();
    }, 3000);
}

// Print Report
function printReport() {
    if (!validateForm()) return;
    
    const url = buildUrl('<?= base_url("admin/absensi/export"); ?>', 'print');
    console.log('Printing report:', url);
    
    // Open in new tab for printing
    const printWindow = window.open(url, '_blank');
    
    if (!printWindow || printWindow.closed || typeof printWindow.closed == 'undefined') {
        alert('Popup diblokir! Izinkan popup untuk situs ini.');
        return;
    }
    
    // Try to print after window loads
    printWindow.onload = function() {
        try {
            printWindow.print();
        } catch (e) {
            console.error('Print error:', e);
            alert('Gagal mencetak. Silakan gunakan tombol print di browser.');
        }
    };
}
</script>

<?= $this->include('admin/templates/footer') ?>