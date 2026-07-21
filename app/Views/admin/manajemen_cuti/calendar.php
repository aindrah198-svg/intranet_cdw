<?php
// C:\xampp\htdocs\cdwnet\app\Views\admin\manajemen_cuti\calendar.php
$title = 'Kalendar Cuti';
$active = 'cuti';
$css = [
    'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css',
    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
    'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css'
];
$scripts = [
    'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js',
    'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/id.min.js',
    'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
    'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js',
    'https://npmcdn.com/flatpickr/dist/l10n/id.js'
];
?>

<?= $this->include('admin/templates/header') ?>
<?= $this->include('admin/templates/sidebar') ?>
<?= $this->include('admin/templates/navbar') ?>

<!-- Main Content -->
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kalendar Cuti</h1>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-success shadow-sm" onclick="printCalendar()">
                <i class="fas fa-print fa-sm text-white-50"></i> Print
            </button>
            <a href="<?= base_url('admin/cuti'); ?>" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Kalendar</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="filter_karyawan">Karyawan:</label>
                        <select class="form-control select2" id="filter_karyawan">
                            <option value="">Semua Karyawan</option>
                            <?php if(isset($karyawan)): ?>
                            <?php foreach ($karyawan as $k): ?>
                                <option value="<?= $k['id']; ?>">
                                    <?= esc($k['nik']); ?> - <?= esc($k['nama_lengkap']); ?>
                                </option>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="filter_jenis">Jenis Cuti:</label>
                        <select class="form-control" id="filter_jenis">
                            <option value="">Semua Jenis</option>
                            <option value="Tahunan">Tahunan</option>
                            <option value="Hamil">Hamil</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Khusus">Khusus</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="filter_status">Status:</label>
                        <select class="form-control" id="filter_status">
                            <option value="">Semua Status</option>
                            <option value="Disetujui HRD,Disetujui Atasan">Disetujui</option>
                            <option value="Menunggu">Menunggu</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="filter_tahun">Tahun:</label>
                        <select class="form-control" id="filter_tahun">
                            <?php for($i = date('Y') - 2; $i <= date('Y') + 2; $i++): ?>
                                <option value="<?= $i; ?>" <?= $i == date('Y') ? 'selected' : '' ?>>
                                    <?= $i; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary" onclick="applyFilters()">
                            <i class="fas fa-filter me-1"></i> Terapkan Filter
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="resetFilters()">
                            <i class="fas fa-redo me-1"></i> Reset
                        </button>
                        <button type="button" class="btn btn-info" onclick="goToToday()">
                            <i class="fas fa-calendar-day me-1"></i> Hari Ini
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Cuti Aktif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="statActive">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Sedang Cuti (Hari Ini)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="statToday">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Menunggu Persetujuan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="statPending">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Departemen Terbanyak
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="statDepartment">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Kalendar Cuti Karyawan</h6>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success me-2">Disetujui</span>
                <span class="badge bg-warning me-2">Menunggu</span>
                <span class="badge bg-danger me-2">Ditolak</span>
                <span class="badge bg-secondary">Dibatalkan</span>
            </div>
        </div>
        <div class="card-body">
            <div id="calendar"></div>
        </div>
    </div>

    <!-- Legend -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Keterangan Jenis Cuti</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <div class="legend-box" style="background-color: #3498db;"></div>
                                <span class="ms-2">Cuti Tahunan</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <div class="legend-box" style="background-color: #e74c3c;"></div>
                                <span class="ms-2">Cuti Hamil</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <div class="legend-box" style="background-color: #f39c12;"></div>
                                <span class="ms-2">Cuti Sakit</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <div class="legend-box" style="background-color: #9b59b6;"></div>
                                <span class="ms-2">Cuti Khusus</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <div class="legend-box" style="background-color: #95a5a6;"></div>
                                <span class="ms-2">Cuti Lainnya</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tips Penggunaan</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Klik event cuti untuk melihat detail</li>
                        <li>Drag untuk melihat periode lain</li>
                        <li>Gunakan tombol di atas kalendar untuk navigasi</li>
                        <li>Filter berdasarkan karyawan, jenis, dan status</li>
                        <li>Print kalendar untuk kebutuhan meeting</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Detail Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Cuti</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="eventContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <a href="#" id="eventDetailLink" class="btn btn-primary">
                    <i class="fas fa-external-link-alt me-1"></i> Lihat Detail Lengkap
                </a>
            </div>
        </div>
    </div>
</div>

<style>
#calendar {
    min-height: 650px;
}

.legend-box {
    width: 20px;
    height: 20px;
    border-radius: 3px;
    display: inline-block;
}

.fc-event {
    cursor: pointer;
    border-radius: 3px;
    padding: 2px 5px;
    margin-bottom: 1px;
}

.fc-event:hover {
    opacity: 0.8;
}

.fc-daygrid-event {
    white-space: normal !important;
    align-items: normal !important;
}

.fc-event-title {
    font-weight: 500;
}

@media print {
    .no-print {
        display: none !important;
    }
    
    #calendar {
        min-height: auto;
    }
}
</style>

<script>
let calendar;
let allEvents = [];

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap',
        width: '100%'
    });
    
    // Initialize Calendar
    initializeCalendar();
    
    // Load events
    loadCalendarEvents();
});

function initializeCalendar() {
    const calendarEl = document.getElementById('calendar');
    
    calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'id',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listMonth'
        },
        buttonText: {
            today: 'Hari Ini',
            month: 'Bulan',
            week: 'Minggu',
            list: 'Daftar'
        },
        height: 'auto',
        navLinks: true,
        editable: false,
        dayMaxEvents: true,
        events: [],
        eventClick: function(info) {
            showEventDetail(info.event);
        },
        eventDidMount: function(info) {
            // Add tooltip
            $(info.el).tooltip({
                title: info.event.extendedProps.tooltip,
                placement: 'top',
                trigger: 'hover',
                container: 'body'
            });
        }
    });
    
    calendar.render();
}

function loadCalendarEvents() {
    const baseUrl = '<?= base_url() ?>';
    
    // Get filter values
    const filterParams = getFilterParams();
    
    $.ajax({
        url: `${baseUrl}/admin/cuti/api/calendar-events`,
        method: 'GET',
        data: filterParams,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                allEvents = response.data;
                displayEvents(allEvents);
                updateStatistics(allEvents);
            } else {
                console.error('Failed to load events:', response.message);
                showNotification('error', 'Gagal memuat data kalender');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading events:', error);
            showNotification('error', 'Terjadi kesalahan saat memuat data');
        }
    });
}

function displayEvents(events) {
    // Clear existing events
    calendar.removeAllEvents();
    
    // Add events to calendar
    events.forEach(event => {
        calendar.addEvent({
            id: event.id,
            title: event.title,
            start: event.start,
            end: event.end,
            backgroundColor: event.color,
            borderColor: event.color,
            textColor: '#ffffff',
            extendedProps: {
                karyawan_id: event.karyawan_id,
                nama_lengkap: event.nama_lengkap,
                nik: event.nik,
                jenis_cuti: event.jenis_cuti,
                status: event.status,
                lama_hari: event.lama_hari,
                alasan: event.alasan,
                tooltip: event.tooltip
            }
        });
    });
}

function getFilterParams() {
    return {
        karyawan_id: $('#filter_karyawan').val(),
        jenis_cuti: $('#filter_jenis').val(),
        status: $('#filter_status').val(),
        tahun: $('#filter_tahun').val()
    };
}

function applyFilters() {
    loadCalendarEvents();
}

function resetFilters() {
    $('#filter_karyawan').val('').trigger('change');
    $('#filter_jenis').val('');
    $('#filter_status').val('');
    $('#filter_tahun').val(new Date().getFullYear());
    loadCalendarEvents();
}

function goToToday() {
    calendar.today();
}

function showEventDetail(event) {
    const baseUrl = '<?= base_url() ?>';
    const props = event.extendedProps;
    
    // Format dates
    const startDate = new Date(event.start);
    const endDate = event.end ? new Date(event.end) : startDate;
    
    const formatDate = (date) => {
        return date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
    };
    
    // Status badge
    let statusBadge = '';
    switch(props.status) {
        case 'Disetujui HRD':
        case 'Disetujui Atasan':
            statusBadge = '<span class="badge bg-success">Disetujui</span>';
            break;
        case 'Menunggu':
            statusBadge = '<span class="badge bg-warning">Menunggu</span>';
            break;
        case 'Ditolak':
            statusBadge = '<span class="badge bg-danger">Ditolak</span>';
            break;
        default:
            statusBadge = '<span class="badge bg-secondary">Draft</span>';
    }
    
    const content = `
        <div class="event-detail">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h6 class="text-muted">NIK</h6>
                    <p class="font-weight-bold">${props.nik}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">Nama Karyawan</h6>
                    <p class="font-weight-bold">${props.nama_lengkap}</p>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <h6 class="text-muted">Jenis Cuti</h6>
                    <p class="font-weight-bold">${props.jenis_cuti}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">Status</h6>
                    <p>${statusBadge}</p>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <h6 class="text-muted">Tanggal Mulai</h6>
                    <p class="font-weight-bold">${formatDate(startDate)}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">Tanggal Selesai</h6>
                    <p class="font-weight-bold">${formatDate(endDate)}</p>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <h6 class="text-muted">Lama Cuti</h6>
                    <p class="font-weight-bold">${props.lama_hari} hari kerja</p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <h6 class="text-muted">Alasan</h6>
                    <p>${props.alasan || '-'}</p>
                </div>
            </div>
        </div>
    `;
    
    $('#eventContent').html(content);
    $('#eventDetailLink').attr('href', `${baseUrl}/admin/cuti/show/${event.id}`);
    $('#eventModal').modal('show');
}

function updateStatistics(events) {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    // Total active (approved)
    const activeCount = events.filter(e => 
        e.status === 'Disetujui HRD' || e.status === 'Disetujui Atasan'
    ).length;
    
    // Today's leave
    const todayCount = events.filter(e => {
        const start = new Date(e.start);
        const end = new Date(e.end || e.start);
        return today >= start && today <= end && 
               (e.status === 'Disetujui HRD' || e.status === 'Disetujui Atasan');
    }).length;
    
    // Pending
    const pendingCount = events.filter(e => e.status === 'Menunggu').length;
    
    // Department with most leaves
    const deptCounts = {};
    events.forEach(e => {
        if (e.departemen) {
            deptCounts[e.departemen] = (deptCounts[e.departemen] || 0) + 1;
        }
    });
    
    let maxDept = '-';
    let maxCount = 0;
    for (const [dept, count] of Object.entries(deptCounts)) {
        if (count > maxCount) {
            maxCount = count;
            maxDept = dept;
        }
    }
    
    // Update UI
    $('#statActive').text(activeCount);
    $('#statToday').text(todayCount);
    $('#statPending').text(pendingCount);
    $('#statDepartment').text(maxDept);
}

function printCalendar() {
    window.print();
}

function showNotification(type, message) {
    const icon = type === 'success' ? 'check-circle' : 'exclamation-triangle';
    const bgColor = type === 'success' ? '#28a745' : '#dc3545';
    
    const notification = $(`
        <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
             role="alert" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="fas fa-${icon} me-2"></i>${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `);
    
    $('body').append(notification);
    
    setTimeout(() => {
        notification.alert('close');
    }, 3000);
}
</script>

<?= $this->include('admin/templates/footer') ?>