<?php
// C:\xampp\htdocs\cdwnet\app\Controllers\Admin\Kontrak.php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KontrakModel;
use App\Models\KaryawanModel;
// Tambahkan ini di atas class
use Dompdf\Dompdf;
use Dompdf\Options;

class Kontrak extends BaseController
{
    protected $kontrakModel;
    protected $karyawanModel;
    
    public function __construct()
    {
        $this->kontrakModel = new KontrakModel();
        $this->karyawanModel = new KaryawanModel();
        helper('terbilang');
    }
    
    /**
     * Helper function untuk data view
     */
    private function getViewData($additionalData = [])
    {
        $defaultData = [
            'title' => 'Manajemen Kontrak',
            'active' => 'kontrak',
            'css' => [
                'https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'scripts' => [
                'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js',
                'https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ]
        ];
        
        return array_merge($defaultData, $additionalData);
    }
    
    /**
     * Index - List all kontrak
     */
    public function index()
    {
        // Get filter parameters
        $search = $this->request->getGet('search');
        $jenis = $this->request->getGet('jenis');
        $status = $this->request->getGet('status');
        
        // Get kontrak data
        $kontrak = $this->kontrakModel->getAllWithKaryawan(null, 0, $search, $jenis, $status);
        $stats = $this->kontrakModel->getStats();
        
        // Options for dropdowns
        $jenisOptions = [
            'Probation' => 'Probation',
            'Kontrak' => 'Kontrak',
            'Tetap' => 'Tetap',
            'Magang' => 'Magang'
        ];
        
        $statusOptions = [
            'Draft' => 'Draft',
            'Aktif' => 'Aktif',
            'Selesai' => 'Selesai',
            'Diperpanjang' => 'Diperpanjang',
            'Diputus' => 'Diputus'
        ];
        
        $data = $this->getViewData([
            'title' => 'Manajemen Kontrak Kerja',
            'subtitle' => 'Daftar seluruh kontrak kerja',
            'kontrak' => $kontrak,
            'stats' => $stats,
            'search' => $search,
            'jenis' => $jenis,
            'status' => $status,
            'jenisOptions' => $jenisOptions,
            'statusOptions' => $statusOptions,
            'total' => count($kontrak)
        ]);
        
        return view('admin/kontrak/index', $data);
        
    }
    
    /**
     * Create new kontrak
     */
    public function create()
    {
        // Get karyawan for dropdown
        $karyawan = $this->karyawanModel->where('deleted_at', null)->findAll();
        
        $data = $this->getViewData([
            'title' => 'Buat Kontrak Baru',
            'subtitle' => 'Form pembuatan kontrak kerja',
            'karyawan' => $karyawan,
            'jenisOptions' => [
                'Probation' => 'Probation',
                'Kontrak' => 'Kontrak',
                'Tetap' => 'Tetap',
                'Magang' => 'Magang'
            ],
            'statusOptions' => [
                'Draft' => 'Draft',
                'Aktif' => 'Aktif'
            ]
        ]);
        
        return view('admin/kontrak/create', $data);
    }
    
    /**
     * Create kontrak for specific karyawan
     */
    public function createFor($karyawanId)
    {
        // Get karyawan data
        $karyawan = $this->karyawanModel->find($karyawanId);
        
        if (!$karyawan) {
            return redirect()->back()->with('error', 'Karyawan tidak ditemukan');
        }
        
        $data = $this->getViewData([
            'title' => 'Buat Kontrak untuk ' . $karyawan['nama_lengkap'],
            'subtitle' => 'Form pembuatan kontrak kerja',
            'karyawan' => [$karyawan], // Put in array for dropdown compatibility
            'selectedKaryawanId' => $karyawanId,
            'jenisOptions' => [
                'Probation' => 'Probation',
                'Kontrak' => 'Kontrak',
                'Tetap' => 'Tetap',
                'Magang' => 'Magang'
            ],
            'statusOptions' => [
                'Draft' => 'Draft',
                'Aktif' => 'Aktif'
            ]
        ]);
        
        return view('admin/kontrak/create', $data);
    }
    
    /**
     * Store new kontrak
     */
    public function store()
    {
        // Validate
        $rules = [
            'karyawan_id' => 'required|numeric',
            'nomor_kontrak' => 'required|is_unique[kontrak.nomor_kontrak]',
            'jenis_kontrak' => 'required',
            'jabatan' => 'required',
            'tanggal_mulai' => 'required|valid_date',
            'status' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Generate nomor kontrak if not provided
        $nomorKontrak = $this->request->getPost('nomor_kontrak');
        if (empty($nomorKontrak)) {
            $nomorKontrak = $this->generateNomorKontrak();
        }
        
        // Calculate tanggal_selesai if masa_kerja_bulan provided
        $tanggalMulai = $this->request->getPost('tanggal_mulai');
        $masaKerjaBulan = $this->request->getPost('masa_kerja_bulan');
        $tanggalSelesai = null;
        
        if ($tanggalMulai && $masaKerjaBulan) {
            $tanggalSelesai = date('Y-m-d', strtotime($tanggalMulai . " +{$masaKerjaBulan} months -1 day"));
        }
        
        // Prepare data
        $gajiPokok = $this->request->getPost('gaji_pokok');
        $tunjanganMakanLokal = $this->request->getPost('tunjangan_makan_lokal');
        $tunjanganMakanLuarJawa = $this->request->getPost('tunjangan_makan_luar_jawa');
        $tunjanganPenginapan = $this->request->getPost('tunjangan_penginapan_max');
        $targetPenjualan = $this->request->getPost('target_penjualan_bulanan');
        
        $data = [
            'karyawan_id' => $this->request->getPost('karyawan_id'),
            'nomor_kontrak' => $nomorKontrak,
            'jenis_kontrak' => $this->request->getPost('jenis_kontrak'),
            'jabatan' => $this->request->getPost('jabatan'),
            'lokasi_kerja' => $this->request->getPost('lokasi_kerja'),
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'masa_kerja_bulan' => $masaKerjaBulan,
            'masa_percobaan_bulan' => $this->request->getPost('masa_percobaan_bulan'),
            'pemberitahuan_pemutusan_hari' => $this->request->getPost('pemberitahuan_pemutusan_hari') ?? 30,
            'gaji_pokok' => $gajiPokok ? str_replace(['.', ','], '', $gajiPokok) : 0,
            'tunjangan_bpjs' => $this->request->getPost('tunjangan_bpjs') ? 1 : 0,
            'tunjangan_makan_lokal' => $tunjanganMakanLokal ? str_replace(['.', ','], '', $tunjanganMakanLokal) : 0,
            'tunjangan_makan_luar_jawa' => $tunjanganMakanLuarJawa ? str_replace(['.', ','], '', $tunjanganMakanLuarJawa) : 0,
            'reimburse_transport' => $this->request->getPost('reimburse_transport') ? 1 : 0,
            'reimburse_entertaint' => $this->request->getPost('reimburse_entertaint') ? 1 : 0,
            'tunjangan_penginapan_max' => $tunjanganPenginapan ? str_replace(['.', ','], '', $tunjanganPenginapan) : 0,
            'hak_cuti_setelah_tahun' => $this->request->getPost('hak_cuti_setelah_tahun') ?? 1,
            'jumlah_cuti_tahunan_hari' => $this->request->getPost('jumlah_cuti_tahunan_hari') ?? 12,
            'cuti_bersama_disesuaikan' => $this->request->getPost('cuti_bersama_disesuaikan') ? 1 : 0,
            'target_penjualan_bulanan' => $targetPenjualan ? str_replace(['.', ','], '', $targetPenjualan) : null,
            'komisi_aturan' => $this->request->getPost('komisi_aturan'),
            'lampiran_path' => $this->request->getPost('lampiran_path'),
            'pihak_pertama_nama' => $this->request->getPost('pihak_pertama_nama'),
            'pihak_pertama_jabatan' => $this->request->getPost('pihak_pertama_jabatan'),
            'pihak_pertama_alamat' => $this->request->getPost('pihak_pertama_alamat'),
            'pihak_kedua_nama' => $this->request->getPost('pihak_kedua_nama'),
            'pihak_kedua_jabatan' => $this->request->getPost('pihak_kedua_jabatan'),
            'pihak_kedua_alamat' => $this->request->getPost('pihak_kedua_alamat'),
            'status' => $this->request->getPost('status'),
            'alasan_berakhir' => $this->request->getPost('alasan_berakhir')
        ];
        
        // Save kontrak
        try {
            $this->kontrakModel->save($data);
            $kontrakId = $this->kontrakModel->getInsertID();
            
            return redirect()->to(base_url('admin/karyawan/kontrak/show/' . $kontrakId))
                ->with('success', 'Kontrak berhasil dibuat!');
                
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menyimpan kontrak: ' . $e->getMessage());
        }
    }
    
    /**
     * Show kontrak detail
     */
    public function show($id)
    {
        $kontrak = $this->kontrakModel->getWithKaryawan($id);
        
        if (!$kontrak) {
            return redirect()->to(base_url('admin/karyawan/kontrak'))
                ->with('error', 'Kontrak tidak ditemukan');
        }
        
        $data = $this->getViewData([
            'title' => 'Detail Kontrak: ' . $kontrak['nomor_kontrak'],
            'subtitle' => $kontrak['nama_lengkap'] . ' - ' . $kontrak['jabatan'],
            'kontrak' => $kontrak
        ]);
        
        return view('admin/kontrak/show', $data);
    }
    
    /**
     * Edit kontrak
     */
    public function edit($id)
    {
        $kontrak = $this->kontrakModel->find($id);
        
        if (!$kontrak) {
            return redirect()->to(base_url('admin/karyawan/kontrak'))
                ->with('error', 'Kontrak tidak ditemukan');
        }
        
        // Get karyawan for dropdown
        $karyawan = $this->karyawanModel->where('deleted_at', null)->findAll();
        
        $data = $this->getViewData([
            'title' => 'Edit Kontrak: ' . $kontrak['nomor_kontrak'],
            'subtitle' => 'Form edit kontrak kerja',
            'kontrak' => $kontrak,
            'karyawan' => $karyawan,
            'jenisOptions' => [
                'Probation' => 'Probation',
                'Kontrak' => 'Kontrak',
                'Tetap' => 'Tetap',
                'Magang' => 'Magang'
            ],
            'statusOptions' => [
                'Draft' => 'Draft',
                'Aktif' => 'Aktif',
                'Selesai' => 'Selesai',
                'Diperpanjang' => 'Diperpanjang',
                'Diputus' => 'Diputus'
            ]
        ]);
        
        return view('admin/kontrak/edit', $data);
    }
    
    /**
 * Update kontrak
 */
public function update($id)
{
    $kontrak = $this->kontrakModel->find($id);
    
    if (!$kontrak) {
        return redirect()->to(base_url('admin/karyawan/kontrak'))
            ->with('error', 'Kontrak tidak ditemukan');
    }
    
    // Validate nomor_kontrak uniqueness dengan custom rule
    $nomorKontrak = $this->request->getPost('nomor_kontrak');
    $isNomorExist = $this->kontrakModel->isNomorKontrakExist($nomorKontrak, $id);
    
    // Setup validation rules - TANPA valid_date untuk tanggal_mulai
    $rules = [
        'karyawan_id' => 'required|numeric',
        'jenis_kontrak' => 'required',
        'jabatan' => 'required',
        'status' => 'required'
    ];
    
    // Custom validation untuk nomor_kontrak
    if ($nomorKontrak != $kontrak['nomor_kontrak']) {
        if ($isNomorExist) {
            return redirect()->back()->withInput()
                ->with('error', 'Nomor kontrak sudah digunakan');
        }
        $rules['nomor_kontrak'] = 'required';
    } else {
        $rules['nomor_kontrak'] = 'required';
    }
    
    // Manual validation untuk tanggal_mulai
    $tanggalMulaiInput = $this->request->getPost('tanggal_mulai');
    if (empty($tanggalMulaiInput)) {
        return redirect()->back()->withInput()
            ->with('error', 'Tanggal mulai harus diisi');
    }
    
    // Konversi format DD/MM/YYYY ke YYYY-MM-DD
    $tanggalMulai = $this->convertDateToMySQL($tanggalMulaiInput);
    if (!$tanggalMulai) {
        return redirect()->back()->withInput()
            ->with('error', 'Format tanggal mulai tidak valid. Gunakan format DD/MM/YYYY');
    }
    
    if (!$this->validate($rules)) {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }
    
    // Calculate tanggal_selesai jika masa_kerja_bulan provided
    $masaKerjaBulan = $this->request->getPost('masa_kerja_bulan');
    $tanggalSelesai = $kontrak['tanggal_selesai']; // Keep existing if not changed
    
    if ($tanggalMulai && $masaKerjaBulan) {
        $tanggalSelesai = date('Y-m-d', strtotime($tanggalMulai . " +{$masaKerjaBulan} months -1 day"));
    } elseif (!$masaKerjaBulan) {
        $tanggalSelesai = null;
    }
    
    // Prepare data
    $gajiPokok = $this->request->getPost('gaji_pokok');
    $tunjanganMakanLokal = $this->request->getPost('tunjangan_makan_lokal');
    $tunjanganMakanLuarJawa = $this->request->getPost('tunjangan_makan_luar_jawa');
    $tunjanganPenginapan = $this->request->getPost('tunjangan_penginapan_max');
    $targetPenjualan = $this->request->getPost('target_penjualan_bulanan');
    
    $data = [
        'id' => $id,
        'karyawan_id' => $this->request->getPost('karyawan_id'),
        'nomor_kontrak' => $nomorKontrak,
        'jenis_kontrak' => $this->request->getPost('jenis_kontrak'),
        'jabatan' => $this->request->getPost('jabatan'),
        'lokasi_kerja' => $this->request->getPost('lokasi_kerja'),
        'tanggal_mulai' => $tanggalMulai, // Sudah dikonversi ke YYYY-MM-DD
        'tanggal_selesai' => $tanggalSelesai,
        'masa_kerja_bulan' => $masaKerjaBulan,
        'masa_percobaan_bulan' => $this->request->getPost('masa_percobaan_bulan'),
        'pemberitahuan_pemutusan_hari' => $this->request->getPost('pemberitahuan_pemutusan_hari') ?? 30,
        'gaji_pokok' => $gajiPokok ? str_replace(['.', ','], '', $gajiPokok) : 0,
        'tunjangan_bpjs' => $this->request->getPost('tunjangan_bpjs') ? 1 : 0,
        'tunjangan_makan_lokal' => $tunjanganMakanLokal ? str_replace(['.', ','], '', $tunjanganMakanLokal) : 0,
        'tunjangan_makan_luar_jawa' => $tunjanganMakanLuarJawa ? str_replace(['.', ','], '', $tunjanganMakanLuarJawa) : 0,
        'reimburse_transport' => $this->request->getPost('reimburse_transport') ? 1 : 0,
        'reimburse_entertaint' => $this->request->getPost('reimburse_entertaint') ? 1 : 0,
        'tunjangan_penginapan_max' => $tunjanganPenginapan ? str_replace(['.', ','], '', $tunjanganPenginapan) : 0,
        'hak_cuti_setelah_tahun' => $this->request->getPost('hak_cuti_setelah_tahun') ?? 1,
        'jumlah_cuti_tahunan_hari' => $this->request->getPost('jumlah_cuti_tahunan_hari') ?? 12,
        'cuti_bersama_disesuaikan' => $this->request->getPost('cuti_bersama_disesuaikan') ? 1 : 0,
        'target_penjualan_bulanan' => $targetPenjualan ? str_replace(['.', ','], '', $targetPenjualan) : null,
        'komisi_aturan' => $this->request->getPost('komisi_aturan'),
        'lampiran_path' => $this->request->getPost('lampiran_path'),
        'pihak_pertama_nama' => $this->request->getPost('pihak_pertama_nama'),
        'pihak_pertama_jabatan' => $this->request->getPost('pihak_pertama_jabatan'),
        'pihak_pertama_alamat' => $this->request->getPost('pihak_pertama_alamat'),
        'pihak_kedua_nama' => $this->request->getPost('pihak_kedua_nama'),
        'pihak_kedua_jabatan' => $this->request->getPost('pihak_kedua_jabatan'),
        'pihak_kedua_alamat' => $this->request->getPost('pihak_kedua_alamat'),
        'status' => $this->request->getPost('status'),
        'alasan_berakhir' => $this->request->getPost('alasan_berakhir')
    ];
    
    // Save kontrak dengan skip validation
    try {
        $this->kontrakModel->skipValidation(true)->save($data);
        
        return redirect()->to(base_url('admin/karyawan/kontrak/show/' . $id))
            ->with('success', 'Kontrak berhasil diperbarui!');
            
    } catch (\Exception $e) {
        log_message('error', 'Update kontrak failed: ' . $e->getMessage());
        return redirect()->back()->withInput()
            ->with('error', 'Gagal memperbarui kontrak: ' . $e->getMessage());
    }
}

/**
 * Helper function untuk konversi tanggal
 */
private function convertDateToMySQL($dateString)
{
    if (empty($dateString)) {
        return null;
    }
    
    // Coba format DD/MM/YYYY
    $pattern = '/(\d{1,2})\/(\d{1,2})\/(\d{4})/';
    if (preg_match($pattern, $dateString, $matches)) {
        $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
        $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
        $year = $matches[3];
        
        // Validasi tanggal
        if (checkdate($month, $day, $year)) {
            return $year . '-' . $month . '-' . $day;
        }
    }
    
    // Coba format YYYY-MM-DD (langsung return)
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateString)) {
        return $dateString;
    }
    
    // Coba parsing dengan strtotime
    $timestamp = strtotime($dateString);
    if ($timestamp !== false) {
        return date('Y-m-d', $timestamp);
    }
    
    return false;
}
    
    /**
     * Delete kontrak (soft delete)
     */
    public function delete($id)
    {
        $kontrak = $this->kontrakModel->find($id);
        
        if (!$kontrak) {
            return redirect()->back()->with('error', 'Kontrak tidak ditemukan');
        }
        
        try {
            $this->kontrakModel->delete($id);
            return redirect()->to(base_url('admin/karyawan/kontrak'))
                ->with('success', 'Kontrak berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus kontrak: ' . $e->getMessage());
        }
    }
    
    /**
     * Show kontrak by karyawan
     */
    public function byKaryawan($karyawanId)
    {
        $kontrak = $this->kontrakModel->getByKaryawanId($karyawanId);
        $karyawan = $this->karyawanModel->find($karyawanId);
        
        if (!$karyawan) {
            return redirect()->to(base_url('admin/karyawan/kontrak'))
                ->with('error', 'Karyawan tidak ditemukan');
        }
        
        $data = $this->getViewData([
            'title' => 'Kontrak Kerja: ' . $karyawan['nama_lengkap'],
            'subtitle' => 'Daftar kontrak untuk ' . $karyawan['nik'],
            'kontrak' => $kontrak,
            'karyawan' => $karyawan
        ]);
        
        return view('admin/kontrak/by_karyawan', $data);
    }
    
    /**
     * Generate nomor kontrak automatically
     */
    private function generateNomorKontrak()
    {
        $prefix = 'KK/CDW';
        $month = date('m');
        $year = date('Y');
        
        // Get last kontrak number this month
        $lastKontrak = $this->kontrakModel
            ->like('nomor_kontrak', $prefix . '/' . $month . '/' . $year, 'after')
            ->orderBy('created_at', 'DESC')
            ->first();
        
        $sequence = 1;
        if ($lastKontrak) {
            $parts = explode('/', $lastKontrak['nomor_kontrak']);
            if (isset($parts[3])) {
                $sequence = intval($parts[3]) + 1;
            }
        }
        
        return sprintf('%s/%s/%s/%03d', $prefix, $month, $year, $sequence);
    }
    
    /**
     * AJAX: Get kontrak data in JSON
     */
    public function getJson($id)
    {
        $kontrak = $this->kontrakModel->getWithKaryawan($id);
        
        if (!$kontrak) {
            return $this->response->setJSON(['error' => 'Kontrak tidak ditemukan'])->setStatusCode(404);
        }
        
        return $this->response->setJSON($kontrak);
    }
    
    /**
     * AJAX: Update kontrak status
     */
    public function updateStatus($id)
    {
        $kontrak = $this->kontrakModel->find($id);
        
        if (!$kontrak) {
            return $this->response->setJSON(['error' => 'Kontrak tidak ditemukan'])->setStatusCode(404);
        }
        
        $status = $this->request->getPost('status');
        $alasan = $this->request->getPost('alasan');
        
        $data = [
            'id' => $id,
            'status' => $status,
            'alasan_berakhir' => $alasan
        ];
        
        if ($status == 'Selesai' && empty($kontrak['tanggal_selesai'])) {
            $data['tanggal_selesai'] = date('Y-m-d');
        }
        
        try {
            $this->kontrakModel->save($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Status berhasil diperbarui']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Gagal memperbarui status: ' . $e->getMessage()])->setStatusCode(500);
        }
    }
    // C:\xampp\htdocs\cdwnet\app\Controllers\Admin\Kontrak.php
// Tambahkan method berikut:

/**
 * Print kontrak dalam format HTML
 */
public function print($id)
{
    $kontrak = $this->kontrakModel->getWithKaryawan($id);
    
    if (!$kontrak) {
        return redirect()->to(base_url('admin/karyawan/kontrak'))
            ->with('error', 'Kontrak tidak ditemukan');
    }
    
    $data = [
        'title' => 'Cetak Kontrak: ' . $kontrak['nomor_kontrak'],
        'kontrak' => $kontrak,
        'print_mode' => true
    ];
    
    return view('admin/kontrak/print', $data);
}

/**
 * Generate PDF kontrak (menggunakan DomPDF)
 */
  public function pdf($id)
    {
        $kontrak = $this->kontrakModel->getWithKaryawan($id);
        
        if (!$kontrak) {
            return redirect()->to(base_url('admin/karyawan/kontrak'))
                ->with('error', 'Kontrak tidak ditemukan');
        }
        
        // Set data untuk PDF
        $data = [
            'title' => 'Kontrak Kerja: ' . $kontrak['nomor_kontrak'],
            'kontrak' => $kontrak,
            'print_mode' => true
        ];
        
        // Load view untuk PDF
        $html = view('admin/kontrak/print', $data); // Gunakan view print yang sama
        
        // Setup DomPDF
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Arial');
        $options->set('chroot', realpath('')); // Tambahkan ini
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Output PDF
        $filename = 'Kontrak_' . preg_replace('/[^a-zA-Z0-9]/', '_', $kontrak['nomor_kontrak']) . '.pdf';
        
        // Untuk preview di browser
        $dompdf->stream($filename, [
            'Attachment' => false // false untuk preview, true untuk download
        ]);
        
        // Jangan return apa-apa karena DomPDF sudah meng-output
        exit;
    }
}