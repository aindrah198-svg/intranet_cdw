<?php
// C:\xampp\htdocs\intranet_cdw\app\Controllers\Admin\JamKerja.php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AbsensiModel;
use App\Models\KaryawanModel;
use App\Models\UserModel;

class JamKerja extends BaseController
{
    protected $absensiModel;
    protected $karyawanModel;
    protected $userModel;
    protected $validation;
    
   public function __construct()
{
    $this->absensiModel = new AbsensiModel();
    $this->karyawanModel = new KaryawanModel();
    $this->userModel = new UserModel();
    $this->validation = \Config\Services::validation();
    
    // Tidak ada pengecekan role di constructor, biarkan method masing-masing yang handle
    // Atau gunakan pola seperti di Absensi.php
}
    
    /**
     * Index page - List jam kerja semua karyawan
     */
    public function index()
    {
        // === TAMBAHKAN INI DI AWAL METHOD ===
    $session = \Config\Services::session();
    if (!$session->get('isLoggedIn') || !in_array(strtolower($session->get('role') ?? ''), ['admin', 'hrd'])) {
        return redirect()->to(base_url('login'));
    }
    // === END TAMBAHKAN ===
        $data = [
            'title' => 'Jam Kerja Karyawan',
            'subtitle' => 'Rekapitulasi jam kerja seluruh karyawan',
            'user' => session()->get('user'),
            'active' => 'jamkerja',
            'breadcrumb' => [
                ['name' => 'Dashboard', 'url' => base_url('admin')],
                ['name' => 'Jam Kerja', 'url' => base_url('admin/jam-kerja')]
            ]
        ];
        
        // Get filter parameters
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
        $karyawan_id = $this->request->getGet('karyawan_id');
        $status = $this->request->getGet('status');
        $departemen = $this->request->getGet('departemen');
        
        // Build conditions
        $conditions = [];
        if ($startDate) {
            $conditions['absensi.tanggal >='] = $startDate;
        }
        if ($endDate) {
            $conditions['absensi.tanggal <='] = $endDate;
        }
        if ($karyawan_id && $karyawan_id != '') {
            $conditions['absensi.karyawan_id'] = $karyawan_id;
        }
        if ($status && $status != '') {
            $conditions['absensi.status'] = $status;
        }
        
        // Get attendance data
        $absensiData = $this->absensiModel->getAbsensiWithKaryawan($conditions);
        
        // Calculate summary per karyawan
        $summary = [];
        foreach ($absensiData as $absensi) {
            $karyawanId = $absensi['karyawan_id'];
            
            if (!isset($summary[$karyawanId])) {
                $summary[$karyawanId] = [
                    'karyawan_id' => $karyawanId,
                    'nik' => $absensi['nik'],
                    'nama_lengkap' => $absensi['nama_lengkap'],
                    'jabatan' => $absensi['jabatan'],
                    'departemen' => $absensi['departemen'],
                    'total_hari' => 0,
                    'total_jam_kerja' => 0,
                    'total_lembur' => 0,
                    'total_terlambat' => 0,
                    'hari_hadir' => 0,
                    'hari_izin' => 0,
                    'hari_sakit' => 0,
                    'hari_cuti' => 0,
                    'hari_libur' => 0,
                    'hari_alpha' => 0,
                    'hari_wfh' => 0,
                    'hari_dinas' => 0
                ];
            }
            
            $summary[$karyawanId]['total_hari']++;
            $summary[$karyawanId]['total_jam_kerja'] += $absensi['jam_kerja'] ?? 0;
            $summary[$karyawanId]['total_lembur'] += $absensi['jam_lembur'] ?? 0;
            $summary[$karyawanId]['total_terlambat'] += $absensi['terlambat'] ?? 0;
            
            // Count status
            switch ($absensi['status']) {
                case 'Hadir':
                    $summary[$karyawanId]['hari_hadir']++;
                    if ($absensi['keterangan'] && stripos($absensi['keterangan'], 'wfh') !== false) {
                        $summary[$karyawanId]['hari_wfh']++;
                    } elseif ($absensi['keterangan'] && stripos($absensi['keterangan'], 'dinas') !== false) {
                        $summary[$karyawanId]['hari_dinas']++;
                    }
                    break;
                case 'Izin':
                    $summary[$karyawanId]['hari_izin']++;
                    break;
                case 'Sakit':
                    $summary[$karyawanId]['hari_sakit']++;
                    break;
                case 'Cuti':
                    $summary[$karyawanId]['hari_cuti']++;
                    break;
                case 'Libur':
                    $summary[$karyawanId]['hari_libur']++;
                    break;
                case 'Alpha':
                    $summary[$karyawanId]['hari_alpha']++;
                    break;
            }
        }
        
        // Format jam kerja untuk display
        foreach ($summary as &$item) {
            // Format total jam kerja
            $item['total_jam_kerja_display'] = $this->formatJamMenit($item['total_jam_kerja']);
            
            // Format total lembur
            $item['total_lembur_display'] = $this->formatJamMenit($item['total_lembur']);
            
            // Format rata-rata per hari
            if ($item['hari_hadir'] > 0) {
                $rataPerHari = $item['total_jam_kerja'] / $item['hari_hadir'];
                $item['rata_per_hari_display'] = $this->formatJamMenit($rataPerHari);
                $item['rata_per_hari_numeric'] = round($rataPerHari, 2);
            } else {
                $item['rata_per_hari_display'] = '-';
                $item['rata_per_hari_numeric'] = 0;
            }
            
            // Persentase kehadiran
            if ($item['total_hari'] > 0) {
                $item['persentase_hadir'] = round(($item['hari_hadir'] / $item['total_hari']) * 100, 1);
            } else {
                $item['persentase_hadir'] = 0;
            }
        }
        
        $data['summary'] = array_values($summary);
        $data['karyawan'] = $this->karyawanModel->where('deleted_at', null)->orderBy('nama_lengkap', 'ASC')->findAll();
        
        // Filter values
        $data['filter'] = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'karyawan_id' => $karyawan_id,
            'status' => $status,
            'departemen' => $departemen
        ];
        
        // Departemen list for filter
        $data['departemen_list'] = $this->karyawanModel->select('departemen')
            ->where('deleted_at', null)
            ->groupBy('departemen')
            ->orderBy('departemen', 'ASC')
            ->findAll();
        
        return view('admin/jam_kerja/index', $data);
    }
    
    /**
     * Detail jam kerja per karyawan
     */
    public function detail($karyawan_id = null)
    {
        // Jika tidak ada parameter, redirect ke list
        if (!$karyawan_id) {
            return redirect()->to('/admin/jam-kerja');
        }
        
        $karyawan = $this->karyawanModel->find($karyawan_id);
        
        if (!$karyawan) {
            return redirect()->to('/admin/jam-kerja')->with('error', 'Karyawan tidak ditemukan.');
        }
        
        $data = [
            'title' => 'Detail Jam Kerja',
            'subtitle' => 'Detail jam kerja karyawan: ' . $karyawan['nama_lengkap'],
            'user' => session()->get('user'),
            'active' => 'jamkerja',
            'karyawan' => $karyawan,
            'breadcrumb' => [
                ['name' => 'Dashboard', 'url' => base_url('admin')],
                ['name' => 'Jam Kerja', 'url' => base_url('admin/jam-kerja')],
                ['name' => 'Detail', 'url' => base_url('admin/jam-kerja/detail/' . $karyawan_id)]
            ]
        ];
        
        // Get filter parameters
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
        $status = $this->request->getGet('status');
        
        // Get attendance data for this karyawan
        $absensiData = $this->absensiModel->getByKaryawan($karyawan_id, $startDate, $endDate);
        
        // Calculate summary
        $summary = [
            'total_hari' => count($absensiData),
            'total_jam_kerja' => 0,
            'total_lembur' => 0,
            'total_terlambat' => 0,
            'hari_hadir' => 0,
            'hari_izin' => 0,
            'hari_sakit' => 0,
            'hari_cuti' => 0,
            'hari_libur' => 0,
            'hari_alpha' => 0,
            'hari_wfh' => 0,
            'hari_dinas' => 0
        ];
        
        foreach ($absensiData as $absensi) {
            $summary['total_jam_kerja'] += $absensi['jam_kerja'] ?? 0;
            $summary['total_lembur'] += $absensi['jam_lembur'] ?? 0;
            $summary['total_terlambat'] += $absensi['terlambat'] ?? 0;
            
            // Count status
            switch ($absensi['status']) {
                case 'Hadir':
                    $summary['hari_hadir']++;
                    if ($absensi['keterangan'] && stripos($absensi['keterangan'], 'wfh') !== false) {
                        $summary['hari_wfh']++;
                    } elseif ($absensi['keterangan'] && stripos($absensi['keterangan'], 'dinas') !== false) {
                        $summary['hari_dinas']++;
                    }
                    break;
                case 'Izin':
                    $summary['hari_izin']++;
                    break;
                case 'Sakit':
                    $summary['hari_sakit']++;
                    break;
                case 'Cuti':
                    $summary['hari_cuti']++;
                    break;
                case 'Libur':
                    $summary['hari_libur']++;
                    break;
                case 'Alpha':
                    $summary['hari_alpha']++;
                    break;
            }
        }
        
        // Format untuk display
        $summary['total_jam_kerja_display'] = $this->formatJamMenit($summary['total_jam_kerja']);
        $summary['total_lembur_display'] = $this->formatJamMenit($summary['total_lembur']);
        
        // Format rata-rata per hari
        if ($summary['hari_hadir'] > 0) {
            $rataPerHari = $summary['total_jam_kerja'] / $summary['hari_hadir'];
            $summary['rata_per_hari_display'] = $this->formatJamMenit($rataPerHari);
        } else {
            $summary['rata_per_hari_display'] = '-';
        }
        
        // Persentase kehadiran
        if ($summary['total_hari'] > 0) {
            $summary['persentase_hadir'] = round(($summary['hari_hadir'] / $summary['total_hari']) * 100, 1);
        } else {
            $summary['persentase_hadir'] = 0;
        }
        
        $data['absensi'] = $absensiData;
        $data['summary'] = $summary;
        
        // Filter values
        $data['filter'] = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $status
        ];
        
        return view('admin/jam_kerja/detail', $data);
    }
    
    /**
     * Jam kerja per karyawan (alternatif)
     */
    public function byKaryawan($karyawan_id)
    {
        return $this->detail($karyawan_id);
    }
    
    /**
     * Rekap jam kerja (bulanan/tahunan)
     */
    public function rekap()
    {
        $data = [
            'title' => 'Rekap Jam Kerja',
            'subtitle' => 'Rekapitulasi jam kerja per periode',
            'user' => session()->get('user'),
            'active' => 'jamkerja',
            'breadcrumb' => [
                ['name' => 'Dashboard', 'url' => base_url('admin')],
                ['name' => 'Jam Kerja', 'url' => base_url('admin/jam-kerja')],
                ['name' => 'Rekap', 'url' => base_url('admin/jam-kerja/rekap')]
            ]
        ];
        
        // Get filter parameters
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $departemen = $this->request->getGet('departemen');
        $jabatan = $this->request->getGet('jabatan');
        
        // Generate date range
        $startDate = date("{$tahun}-{$bulan}-01");
        $endDate = date("{$tahun}-{$bulan}-t");
        
        // Get karyawan with filter
        $karyawanConditions = ['deleted_at' => null];
        if ($departemen && $departemen != '') {
            $karyawanConditions['departemen'] = $departemen;
        }
        if ($jabatan && $jabatan != '') {
            $karyawanConditions['jabatan'] = $jabatan;
        }
        
        $karyawanList = $this->karyawanModel->where($karyawanConditions)->orderBy('nama_lengkap', 'ASC')->findAll();
        
        $rekapData = [];
        $totalKaryawan = 0;
        $totalJamKerja = 0;
        $totalLembur = 0;
        $totalHariHadir = 0;
        
        foreach ($karyawanList as $karyawan) {
            $absensiData = $this->absensiModel->getByKaryawan($karyawan['id'], $startDate, $endDate);
            
            $totalJam = 0;
            $totalLemburKaryawan = 0;
            $hariHadir = 0;
            $hariIzin = 0;
            $hariSakit = 0;
            $hariCuti = 0;
            $hariAlpha = 0;
            
            foreach ($absensiData as $absensi) {
                $totalJam += $absensi['jam_kerja'] ?? 0;
                $totalLemburKaryawan += $absensi['jam_lembur'] ?? 0;
                
                switch ($absensi['status']) {
                    case 'Hadir':
                        $hariHadir++;
                        break;
                    case 'Izin':
                        $hariIzin++;
                        break;
                    case 'Sakit':
                        $hariSakit++;
                        break;
                    case 'Cuti':
                        $hariCuti++;
                        break;
                    case 'Alpha':
                        $hariAlpha++;
                        break;
                }
            }
            
            $rataPerHari = $hariHadir > 0 ? $totalJam / $hariHadir : 0;
            $persentaseHadir = count($absensiData) > 0 ? ($hariHadir / count($absensiData)) * 100 : 0;
            
            $rekapData[] = [
                'karyawan' => $karyawan,
                'total_hari' => count($absensiData),
                'hari_hadir' => $hariHadir,
                'hari_izin' => $hariIzin,
                'hari_sakit' => $hariSakit,
                'hari_cuti' => $hariCuti,
                'hari_alpha' => $hariAlpha,
                'total_jam_kerja' => $totalJam,
                'total_lembur' => $totalLemburKaryawan,
                'rata_per_hari' => $rataPerHari,
                'persentase_hadir' => round($persentaseHadir, 1),
                'total_jam_kerja_display' => $this->formatJamMenit($totalJam),
                'total_lembur_display' => $this->formatJamMenit($totalLemburKaryawan),
                'rata_per_hari_display' => $this->formatJamMenit($rataPerHari)
            ];
            
            $totalKaryawan++;
            $totalJamKerja += $totalJam;
            $totalLembur += $totalLemburKaryawan;
            $totalHariHadir += $hariHadir;
        }
        
        // Calculate averages
        $rataRataJamKerja = $totalKaryawan > 0 ? $totalJamKerja / $totalKaryawan : 0;
        $rataRataHariHadir = $totalKaryawan > 0 ? $totalHariHadir / $totalKaryawan : 0;
        
        $data['rekap'] = $rekapData;
        $data['statistics'] = [
            'total_karyawan' => $totalKaryawan,
            'total_jam_kerja' => $totalJamKerja,
            'total_lembur' => $totalLembur,
            'total_hari_hadir' => $totalHariHadir,
            'rata_rata_jam_kerja' => $rataRataJamKerja,
            'rata_rata_hari_hadir' => $rataRataHariHadir,
            'total_jam_kerja_display' => $this->formatJamMenit($totalJamKerja),
            'total_lembur_display' => $this->formatJamMenit($totalLembur),
            'rata_rata_jam_kerja_display' => $this->formatJamMenit($rataRataJamKerja)
        ];
        
        $data['filter'] = [
            'tahun' => $tahun,
            'bulan' => $bulan,
            'departemen' => $departemen,
            'jabatan' => $jabatan
        ];
        
        // Years for dropdown
        $currentYear = date('Y');
        $years = [];
        for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
            $years[$i] = $i;
        }
        
        // Months for dropdown
        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        
        // Departemen list
        $data['departemen_list'] = $this->karyawanModel->select('departemen')
            ->where('deleted_at', null)
            ->groupBy('departemen')
            ->orderBy('departemen', 'ASC')
            ->findAll();
            
        // Jabatan list
        $data['jabatan_list'] = $this->karyawanModel->select('jabatan')
            ->where('deleted_at', null)
            ->groupBy('jabatan')
            ->orderBy('jabatan', 'ASC')
            ->findAll();
        
        $data['years'] = $years;
        $data['months'] = $months;
        
        return view('admin/jam_kerja/rekap', $data);
    }
    
    /**
     * Export untuk print
     */
    public function export()
    {
        // Get filter parameters
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
        $karyawan_id = $this->request->getGet('karyawan_id');
        $status = $this->request->getGet('status');
        
        $data = $this->getExportData();
        
        // Check if print is requested
        $format = $this->request->getGet('format');
        if ($format === 'print') {
            return view('admin/jam_kerja/export_print', $data);
        }
        
        return view('admin/jam_kerja/export_view', $data);
    }
    
    /**
     * Method untuk menampilkan view export (sesuai route export/view)
     */
    public function exportView()
    {
        // Gunakan method export() yang sudah ada
        return $this->export();
    }
    
    /**
     * Method untuk export print (sesuai route export/print)
     */
    public function exportPrint()
    {
        // Set parameter format ke get
        $_GET['format'] = 'print';
        return $this->export();
    }
    
    /**
     * Method untuk export PDF (sesuai route export/pdf)
     */
    public function exportPdf()
    {
        $data = $this->getExportData();
        $data['title'] = 'Laporan Jam Kerja (PDF)';
        
        // Cek apakah Dompdf tersedia
        if (class_exists('\Dompdf\Dompdf')) {
            $dompdf = new \Dompdf\Dompdf();
            $html = view('admin/jam_kerja/export_pdf', $data);
            
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            
            // Output PDF
            $dompdf->stream("jam_kerja_" . date('Ymd_His') . ".pdf", ['Attachment' => 1]);
            exit;
        } else {
            // Fallback ke view biasa jika Dompdf tidak tersedia
            return view('admin/jam_kerja/export_print', $data);
        }
    }
    
    /**
     * Export to Excel
     */
    public function exportExcel()
    {
        $data = $this->getExportData();
        
        // Set proper headers for Excel
        $response = service('response');
        $response->setHeader('Content-Type', 'application/vnd.ms-excel; charset=utf-8');
        $response->setHeader('Content-Disposition', 'attachment; filename="jam_kerja_' . date('Ymd_His') . '.xls"');
        $response->setHeader('Cache-Control', 'max-age=0');
        $response->setHeader('Pragma', 'public');
        $response->setHeader('Expires', '0');
        
        // Return Excel view
        return view('admin/jam_kerja/export_excel', $data);
    }
    
    /**
     * CRUD: Create form
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Jam Kerja Manual',
            'subtitle' => 'Tambah data jam kerja manual',
            'user' => session()->get('user'),
            'active' => 'jamkerja',
            'karyawan' => $this->karyawanModel->where('deleted_at', null)->orderBy('nama_lengkap', 'ASC')->findAll(),
            'breadcrumb' => [
                ['name' => 'Dashboard', 'url' => base_url('admin')],
                ['name' => 'Jam Kerja', 'url' => base_url('admin/jam-kerja')],
                ['name' => 'Tambah', 'url' => base_url('admin/jam-kerja/create')]
            ]
        ];
        
        return view('admin/jam_kerja/create', $data);
    }
    
    /**
     * CRUD: Store data
     */
    public function store()
    {
        // Validation rules
        $rules = [
            'karyawan_id' => 'required|numeric',
            'tanggal' => 'required|valid_date',
            'check_in' => 'required',
            'check_out' => 'required',
            'status' => 'required|in_list[Hadir,Izin,Sakit,Cuti,Libur,Alpha]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Check if record already exists
        $existing = $this->absensiModel->where([
            'karyawan_id' => $this->request->getPost('karyawan_id'),
            'tanggal' => $this->request->getPost('tanggal')
        ])->first();
        
        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Data absensi untuk tanggal tersebut sudah ada.');
        }
        
        // Calculate jam kerja
        $checkIn = strtotime($this->request->getPost('check_in'));
        $checkOut = strtotime($this->request->getPost('check_out'));
        
        if ($checkOut <= $checkIn) {
            return redirect()->back()->withInput()->with('error', 'Waktu check-out harus setelah waktu check-in.');
        }
        
        $jamKerja = ($checkOut - $checkIn) / 3600; // Convert to hours
        
        // Calculate terlambat if status is Hadir
        $terlambat = 0;
        if ($this->request->getPost('status') === 'Hadir') {
            $jamMasukStandar = strtotime('08:00:00');
            if ($checkIn > $jamMasukStandar) {
                $terlambat = ($checkIn - $jamMasukStandar) / 60; // Convert to minutes
            }
        }
        
        $data = [
            'karyawan_id' => $this->request->getPost('karyawan_id'),
            'tanggal' => $this->request->getPost('tanggal'),
            'check_in' => $this->request->getPost('check_in'),
            'check_out' => $this->request->getPost('check_out'),
            'jam_kerja' => $jamKerja,
            'jam_lembur' => $this->request->getPost('jam_lembur') ?? 0,
            'terlambat' => $terlambat,
            'status' => $this->request->getPost('status'),
            'keterangan' => $this->request->getPost('keterangan'),
            'lokasi_checkin' => $this->request->getPost('lokasi_checkin') ?? 'Manual Entry',
            'lokasi_checkout' => $this->request->getPost('lokasi_checkout') ?? 'Manual Entry',
            'created_by' => session()->get('user')['id']
        ];
        
        if ($this->absensiModel->save($data)) {
            return redirect()->to('/admin/jam-kerja')->with('success', 'Data jam kerja berhasil ditambahkan.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan data jam kerja.');
        }
    }
    
    /**
     * CRUD: Edit form
     */
    public function edit($id)
    {
        $absensi = $this->absensiModel->find($id);
        
        if (!$absensi) {
            return redirect()->to('/admin/jam-kerja')->with('error', 'Data tidak ditemukan.');
        }
        
        $data = [
            'title' => 'Edit Jam Kerja',
            'subtitle' => 'Edit data jam kerja',
            'user' => session()->get('user'),
            'active' => 'jamkerja',
            'absensi' => $absensi,
            'karyawan' => $this->karyawanModel->where('deleted_at', null)->orderBy('nama_lengkap', 'ASC')->findAll(),
            'breadcrumb' => [
                ['name' => 'Dashboard', 'url' => base_url('admin')],
                ['name' => 'Jam Kerja', 'url' => base_url('admin/jam-kerja')],
                ['name' => 'Edit', 'url' => base_url('admin/jam-kerja/edit/' . $id)]
            ]
        ];
        
        return view('admin/jam_kerja/edit', $data);
    }
    
    /**
     * CRUD: Update data
     */
    public function update($id)
    {
        $absensi = $this->absensiModel->find($id);
        
        if (!$absensi) {
            return redirect()->to('/admin/jam-kerja')->with('error', 'Data tidak ditemukan.');
        }
        
        // Validation rules
        $rules = [
            'karyawan_id' => 'required|numeric',
            'tanggal' => 'required|valid_date',
            'check_in' => 'required',
            'check_out' => 'required',
            'status' => 'required|in_list[Hadir,Izin,Sakit,Cuti,Libur,Alpha]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Check if record already exists for another id
        $existing = $this->absensiModel->where([
            'karyawan_id' => $this->request->getPost('karyawan_id'),
            'tanggal' => $this->request->getPost('tanggal'),
            'id !=' => $id
        ])->first();
        
        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Data absensi untuk tanggal tersebut sudah ada.');
        }
        
        // Calculate jam kerja
        $checkIn = strtotime($this->request->getPost('check_in'));
        $checkOut = strtotime($this->request->getPost('check_out'));
        
        if ($checkOut <= $checkIn) {
            return redirect()->back()->withInput()->with('error', 'Waktu check-out harus setelah waktu check-in.');
        }
        
        $jamKerja = ($checkOut - $checkIn) / 3600; // Convert to hours
        
        // Calculate terlambat if status is Hadir
        $terlambat = $absensi['terlambat'];
        if ($this->request->getPost('status') === 'Hadir') {
            $jamMasukStandar = strtotime('08:00:00');
            if ($checkIn > $jamMasukStandar) {
                $terlambat = ($checkIn - $jamMasukStandar) / 60; // Convert to minutes
            } else {
                $terlambat = 0;
            }
        } else {
            $terlambat = 0;
        }
        
        $data = [
            'id' => $id,
            'karyawan_id' => $this->request->getPost('karyawan_id'),
            'tanggal' => $this->request->getPost('tanggal'),
            'check_in' => $this->request->getPost('check_in'),
            'check_out' => $this->request->getPost('check_out'),
            'jam_kerja' => $jamKerja,
            'jam_lembur' => $this->request->getPost('jam_lembur') ?? 0,
            'terlambat' => $terlambat,
            'status' => $this->request->getPost('status'),
            'keterangan' => $this->request->getPost('keterangan'),
            'updated_by' => session()->get('user')['id'],
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($this->absensiModel->save($data)) {
            return redirect()->to('/admin/jam-kerja')->with('success', 'Data jam kerja berhasil diperbarui.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data jam kerja.');
        }
    }
    
    /**
     * CRUD: Delete data
     */
    public function delete($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/admin/jam-kerja');
        }
        
        $absensi = $this->absensiModel->find($id);
        
        if (!$absensi) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ]);
        }
        
        if ($this->absensiModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data jam kerja berhasil dihapus.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus data jam kerja.'
            ]);
        }
    }
    
    /**
     * AJAX: Get data for datatable
     */
    public function getData()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/admin/jam-kerja');
        }
        
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
        
        // Build conditions
        $conditions = [
            'absensi.tanggal >=' => $startDate,
            'absensi.tanggal <=' => $endDate
        ];
        
        $absensiData = $this->absensiModel->getAbsensiWithKaryawan($conditions);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $absensiData
        ]);
    }
    
    /**
     * AJAX: Get rekap data
     */
    public function getRekap()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/admin/jam-kerja/rekap');
        }
        
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? date('m');
        
        // Your rekap logic here
        $startDate = date("{$tahun}-{$bulan}-01");
        $endDate = date("{$tahun}-{$bulan}-t");
        
        $karyawanList = $this->karyawanModel->where('deleted_at', null)->findAll();
        
        $rekapData = [];
        foreach ($karyawanList as $karyawan) {
            $absensiData = $this->absensiModel->getByKaryawan($karyawan['id'], $startDate, $endDate);
            
            $totalJam = 0;
            $totalLembur = 0;
            $hariHadir = 0;
            
            foreach ($absensiData as $absensi) {
                $totalJam += $absensi['jam_kerja'] ?? 0;
                $totalLembur += $absensi['jam_lembur'] ?? 0;
                if ($absensi['status'] === 'Hadir') {
                    $hariHadir++;
                }
            }
            
            $rekapData[] = [
                'karyawan' => $karyawan,
                'total_hari' => count($absensiData),
                'hari_hadir' => $hariHadir,
                'total_jam_kerja' => $totalJam,
                'total_lembur' => $totalLembur,
                'rata_per_hari' => $hariHadir > 0 ? $totalJam / $hariHadir : 0
            ];
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $rekapData
        ]);
    }
    
    /**
     * AJAX: Update status
     */
    public function updateStatus($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/admin/jam-kerja');
        }
        
        $absensi = $this->absensiModel->find($id);
        
        if (!$absensi) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak ditemukan.'
            ]);
        }
        
        $status = $this->request->getPost('status');
        $validStatuses = ['Hadir', 'Izin', 'Sakit', 'Cuti', 'Libur', 'Alpha'];
        
        if (!in_array($status, $validStatuses)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Status tidak valid.'
            ]);
        }
        
        $data = [
            'id' => $id,
            'status' => $status,
            'updated_by' => session()->get('user')['id'],
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($this->absensiModel->save($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Status berhasil diperbarui.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memperbarui status.'
            ]);
        }
    }
    
    /**
     * Import form
     */
    public function import()
    {
        $data = [
            'title' => 'Import Jam Kerja',
            'subtitle' => 'Import data jam kerja dari Excel',
            'user' => session()->get('user'),
            'active' => 'jamkerja',
            'breadcrumb' => [
                ['name' => 'Dashboard', 'url' => base_url('admin')],
                ['name' => 'Jam Kerja', 'url' => base_url('admin/jam-kerja')],
                ['name' => 'Import', 'url' => base_url('admin/jam-kerja/import')]
            ]
        ];
        
        return view('admin/jam_kerja/import', $data);
    }
    
    /**
     * Process import
     */
    public function processImport()
    {
        $file = $this->request->getFile('excel_file');
        
        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid.');
        }
        
        // Validate file type
        $ext = $file->getExtension();
        if (!in_array($ext, ['xls', 'xlsx', 'csv'])) {
            return redirect()->back()->with('error', 'Format file tidak didukung. Gunakan file Excel (xls, xlsx) atau CSV.');
        }
        
        try {
            // Pindahkan file ke direktori uploads
            $newName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads', $newName);
            $filePath = WRITEPATH . 'uploads/' . $newName;
            
            // Baca file CSV atau Excel sederhana
            $imported = 0;
            $failed = 0;
            $errors = [];
            
            if ($ext === 'csv') {
                $handle = fopen($filePath, 'r');
                $row = 0;
                
                while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    $row++;
                    if ($row === 1) continue; // Skip header
                    
                    // Proses data CSV
                    $nik = $data[0] ?? '';
                    $tanggal = $data[1] ?? '';
                    $check_in = $data[2] ?? '';
                    $check_out = $data[3] ?? '';
                    $status = $data[4] ?? 'Hadir';
                    $keterangan = $data[5] ?? '';
                    
                    // Proses data
                    $result = $this->processImportRow($nik, $tanggal, $check_in, $check_out, $status, $keterangan, $row);
                    if ($result['success']) {
                        $imported++;
                    } else {
                        $failed++;
                        $errors[] = $result['message'];
                    }
                }
                
                fclose($handle);
            } else {
                // Untuk Excel, gunakan metode sederhana
                return redirect()->back()->with('error', 'Import Excel memerlukan library PhpSpreadsheet. Untuk sementara, gunakan format CSV.');
            }
            
            // Hapus file temp
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            $message = "Import selesai. Berhasil: $imported, Gagal: $failed";
            if (!empty($errors)) {
                session()->setFlashdata('import_errors', array_slice($errors, 0, 10)); // Batasi 10 error
            }
            
            return redirect()->to('/admin/jam-kerja')->with('success', $message);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error saat memproses file: ' . $e->getMessage());
        }
    }
    
    /**
     * Proses single row import
     */
    private function processImportRow($nik, $tanggal, $check_in, $check_out, $status, $keterangan, $rowNumber)
    {
        // Validasi data
        if (empty($nik) || empty($tanggal) || empty($check_in) || empty($check_out)) {
            return [
                'success' => false,
                'message' => "Baris $rowNumber: Data tidak lengkap"
            ];
        }
        
        // Find karyawan by NIK
        $karyawan = $this->karyawanModel->where('nik', $nik)->first();
        if (!$karyawan) {
            return [
                'success' => false,
                'message' => "Baris $rowNumber: Karyawan dengan NIK '$nik' tidak ditemukan"
            ];
        }
        
        // Parse tanggal
        $tanggal = date('Y-m-d', strtotime($tanggal));
        if ($tanggal === '1970-01-01') {
            return [
                'success' => false,
                'message' => "Baris $rowNumber: Format tanggal tidak valid"
            ];
        }
        
        // Validasi waktu
        $checkInTime = strtotime($check_in);
        $checkOutTime = strtotime($check_out);
        
        if ($checkInTime === false || $checkOutTime === false) {
            return [
                'success' => false,
                'message' => "Baris $rowNumber: Format waktu tidak valid"
            ];
        }
        
        if ($checkOutTime <= $checkInTime) {
            return [
                'success' => false,
                'message' => "Baris $rowNumber: Waktu check-out harus setelah check-in"
            ];
        }
        
        // Check if record already exists
        $existing = $this->absensiModel->where([
            'karyawan_id' => $karyawan['id'],
            'tanggal' => $tanggal
        ])->first();
        
        $jamKerja = ($checkOutTime - $checkInTime) / 3600;
        
        if ($existing) {
            // Update existing record
            $data = [
                'id' => $existing['id'],
                'check_in' => date('H:i:s', $checkInTime),
                'check_out' => date('H:i:s', $checkOutTime),
                'jam_kerja' => $jamKerja,
                'status' => $status,
                'keterangan' => $keterangan,
                'updated_by' => session()->get('user')['id'],
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $this->absensiModel->save($data);
            return ['success' => true];
        } else {
            // Create new record
            $data = [
                'karyawan_id' => $karyawan['id'],
                'tanggal' => $tanggal,
                'check_in' => date('H:i:s', $checkInTime),
                'check_out' => date('H:i:s', $checkOutTime),
                'jam_kerja' => $jamKerja,
                'status' => $status,
                'keterangan' => $keterangan,
                'lokasi_checkin' => 'Import',
                'lokasi_checkout' => 'Import',
                'created_by' => session()->get('user')['id']
            ];
            
            if ($this->absensiModel->save($data)) {
                return ['success' => true];
            } else {
                return [
                    'success' => false,
                    'message' => "Baris $rowNumber: Gagal menyimpan data"
                ];
            }
        }
    }
    
    /**
     * Download template import CSV
     */
    public function downloadTemplate()
    {
        $data = "NIK,Tanggal,Check In,Check Out,Status,Keterangan\n";
        $data .= "EMP001,2024-01-01,08:00:00,17:00:00,Hadir,\n";
        $data .= "EMP002,2024-01-01,08:30:00,17:30:00,Hadir,WFH\n";
        $data .= "EMP003,2024-01-01,09:00:00,18:00:00,Hadir,Macet\n";
        
        $response = service('response');
        $response->setHeader('Content-Type', 'text/csv');
        $response->setHeader('Content-Disposition', 'attachment;filename="template_import_jam_kerja.csv"');
        $response->setHeader('Cache-Control', 'max-age=0');
        
        return $response->setBody($data);
    }
    
    /**
     * AJAX: Get karyawan data for autocomplete
     */
    public function autocomplete()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/admin/jam-kerja');
        }
        
        $search = $this->request->getGet('search');
        
        $karyawan = $this->karyawanModel->select('id, nik, nama_lengkap, jabatan, departemen')
            ->where('deleted_at', null)
            ->groupStart()
                ->like('nik', $search)
                ->orLike('nama_lengkap', $search)
                ->orLike('jabatan', $search)
            ->groupEnd()
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll(10);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $karyawan
        ]);
    }
    
    /**
     * Helper function: Format jam dan menit
     */
    private function formatJamMenit($jamDecimal)
    {
        if ($jamDecimal <= 0) {
            return '-';
        }
        
        $jam = floor($jamDecimal);
        $menit = round(($jamDecimal - $jam) * 60);
        
        if ($jam > 0 && $menit > 0) {
            return "{$jam} jam {$menit} menit";
        } elseif ($jam > 0) {
            return "{$jam} jam";
        } else {
            return "{$menit} menit";
        }
    }
    
    /**
     * Helper function: Get export data
     */
    private function getExportData()
    {
        // Get filter parameters
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
        $karyawan_id = $this->request->getGet('karyawan_id');
        $status = $this->request->getGet('status');
        
        // Build conditions
        $conditions = [];
        if ($startDate) {
            $conditions['absensi.tanggal >='] = $startDate;
        }
        if ($endDate) {
            $conditions['absensi.tanggal <='] = $endDate;
        }
        if ($karyawan_id && $karyawan_id !== '') {
            $conditions['absensi.karyawan_id'] = $karyawan_id;
        }
        if ($status && $status !== '') {
            $conditions['absensi.status'] = $status;
        }
        
        // Get attendance data
        if (!empty($conditions)) {
            $absensiData = $this->absensiModel->getAbsensiWithKaryawan($conditions);
        } else {
            $absensiData = $this->absensiModel->getAbsensiWithKaryawan([
                'absensi.tanggal >=' => $startDate,
                'absensi.tanggal <=' => $endDate
            ]);
        }
        
        // Calculate summary per karyawan
   $summary = [];
    foreach ($absensiData as $absensi) {
        $karyawanId = $absensi['karyawan_id'];
        
        if (!isset($summary[$karyawanId])) {
            $summary[$karyawanId] = [
                'karyawan_id' => $karyawanId,
                'nik' => $absensi['nik'],
                'nama_lengkap' => $absensi['nama_lengkap'],
                'jabatan' => $absensi['jabatan'],
                'departemen' => $absensi['departemen'],
                'total_hari' => 0,
                'total_jam_kerja' => 0,
                'total_lembur' => 0,
                'total_terlambat' => 0,
                'hari_hadir' => 0,
                'hari_izin' => 0,
                'hari_sakit' => 0,
                'hari_cuti' => 0,
                'hari_libur' => 0,
                'hari_alpha' => 0
            ];
        }
        
        $summary[$karyawanId]['total_hari']++;
        $summary[$karyawanId]['total_jam_kerja'] += $absensi['jam_kerja'] ?? 0;
        $summary[$karyawanId]['total_lembur'] += $absensi['jam_lembur'] ?? 0;
        $summary[$karyawanId]['total_terlambat'] += $absensi['terlambat'] ?? 0;
        
        // Count status
        switch ($absensi['status']) {
            case 'Hadir':
                $summary[$karyawanId]['hari_hadir']++;
                break;
            case 'Izin':
                $summary[$karyawanId]['hari_izin']++;
                break;
            case 'Sakit':
                $summary[$karyawanId]['hari_sakit']++;
                break;
            case 'Cuti':
                $summary[$karyawanId]['hari_cuti']++;
                break;
            case 'Libur':
                $summary[$karyawanId]['hari_libur']++;
                break;
            case 'Alpha':
                $summary[$karyawanId]['hari_alpha']++;
                break;
        }
    }
    
    // === PERBAIKAN: Filter hanya yang memiliki kehadiran > 0 ===
    $show_all = $this->request->getGet('show_all') == '1';
    if (!$show_all) {
        // Filter out karyawan with 0 attendance
        $filteredSummary = [];
        foreach ($summary as $karyawanId => $item) {
            if ($item['hari_hadir'] > 0) {
                $filteredSummary[$karyawanId] = $item;
            }
        }
        $summary = $filteredSummary;
    }

     // === PERBAIKAN: Filter hanya yang memiliki kehadiran > 0 ===
    $show_all = $this->request->getGet('show_all') == '1';
    if (!$show_all) {
        // Filter out karyawan with 0 attendance
        $filteredSummary = [];
        foreach ($summary as $karyawanId => $item) {
            if ($item['hari_hadir'] > 0) {
                $filteredSummary[$karyawanId] = $item;
            }
        }
        $summary = $filteredSummary;
    }
    // === END PERBAIKAN ===
        
        // Format jam kerja untuk display
    foreach ($summary as &$item) {
        $item['total_jam_kerja_display'] = $this->formatJamMenit($item['total_jam_kerja']);
        $item['total_lembur_display'] = $this->formatJamMenit($item['total_lembur']);
        
        if ($item['hari_hadir'] > 0) {
            $rataPerHari = $item['total_jam_kerja'] / $item['hari_hadir'];
            $item['rata_per_hari_display'] = $this->formatJamMenit($rataPerHari);
        } else {
            $item['rata_per_hari_display'] = '-';
        }
        
        if ($item['total_hari'] > 0) {
            $item['persentase_hadir'] = round(($item['hari_hadir'] / $item['total_hari']) * 100, 1);
        } else {
            $item['persentase_hadir'] = 0;
        }
    }
    
    return [
        'title' => 'Laporan Jam Kerja',
        'subtitle' => 'Rekapitulasi jam kerja karyawan',
        'user' => session()->get('user'),
        'summary' => array_values($summary),
        'filter' => [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'karyawan_id' => $karyawan_id,
            'status' => $status
        ],
        'export_date' => date('d F Y H:i:s'),
        'show_all' => $show_all // Tambahkan ini
    ];
}
/**
 * Export rekap data (bulanan)
 */
public function exportRekap()
{
    $session = \Config\Services::session();
    if (!$session->get('isLoggedIn') || !in_array(strtolower($session->get('role') ?? ''), ['admin', 'hrd'])) {
        return redirect()->to(base_url('login'));
    }
    
    // Get filter parameters
    $tahun = $this->request->getGet('tahun') ?? date('Y');
    $bulan = $this->request->getGet('bulan') ?? date('m');
    $departemen = $this->request->getGet('departemen');
    $jabatan = $this->request->getGet('jabatan');
    $show_all = $this->request->getGet('show_all') == '1';
    
    // Get data rekap (gunakan logic dari method rekap())
    $startDate = date("{$tahun}-{$bulan}-01");
    $endDate = date("{$tahun}-{$bulan}-t");
    
    // Get karyawan with filter
    $karyawanConditions = ['deleted_at' => null];
    if ($departemen && $departemen != '') {
        $karyawanConditions['departemen'] = $departemen;
    }
    if ($jabatan && $jabatan != '') {
        $karyawanConditions['jabatan'] = $jabatan;
    }
    
    $karyawanList = $this->karyawanModel->where($karyawanConditions)->orderBy('nama_lengkap', 'ASC')->findAll();
    
    $rekapData = [];
    foreach ($karyawanList as $karyawan) {
        $absensiData = $this->absensiModel->getByKaryawan($karyawan['id'], $startDate, $endDate);
        
        $totalJam = 0;
        $totalLembur = 0;
        $hariHadir = 0;
        
        foreach ($absensiData as $absensi) {
            $totalJam += $absensi['jam_kerja'] ?? 0;
            $totalLembur += $absensi['jam_lembur'] ?? 0;
            if ($absensi['status'] === 'Hadir') {
                $hariHadir++;
            }
        }
        
        // Filter jika tidak show_all dan hari_hadir = 0
        if (!$show_all && $hariHadir === 0) {
            continue;
        }
        
        $rataPerHari = $hariHadir > 0 ? $totalJam / $hariHadir : 0;
        
        $rekapData[] = [
            'karyawan' => $karyawan,
            'total_hari' => count($absensiData),
            'hari_hadir' => $hariHadir,
            'total_jam_kerja' => $totalJam,
            'total_lembur' => $totalLembur,
            'rata_per_hari' => $rataPerHari,
            'total_jam_kerja_display' => $this->formatJamMenit($totalJam),
            'total_lembur_display' => $this->formatJamMenit($totalLembur),
            'rata_per_hari_display' => $this->formatJamMenit($rataPerHari)
        ];
    }
    
    $data = [
        'title' => 'Rekap Jam Kerja Bulanan',
        'subtitle' => "Periode: " . date('F Y', strtotime($startDate)),
        'rekap' => $rekapData,
        'filter' => [
            'tahun' => $tahun,
            'bulan' => $bulan,
            'departemen' => $departemen,
            'jabatan' => $jabatan,
            'show_all' => $show_all
        ],
        'export_date' => date('d F Y H:i:s')
    ];
    
    // Check format
    $format = $this->request->getGet('format');
    
    if ($format === 'excel') {
        // Set proper headers for Excel
        $response = service('response');
        $response->setHeader('Content-Type', 'application/vnd.ms-excel; charset=utf-8');
        $response->setHeader('Content-Disposition', 'attachment; filename="rekap_jam_kerja_' . date('Ymd_His') . '.xls"');
        $response->setHeader('Cache-Control', 'max-age=0');
        $response->setHeader('Pragma', 'public');
        $response->setHeader('Expires', '0');
        
        return view('admin/jam_kerja/export_excel_rekap', $data);
    } else {
        // Default: print view
        return view('admin/jam_kerja/export_print_rekap', $data);
    }
}
}