<?php
// app/Controllers/Accounting/PenggajianPerhitungan.php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\Accounting\PenggajianPerhitunganModel;
use App\Models\Accounting\PenggajianKomponenModel;
use App\Models\KaryawanModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Dompdf\Dompdf;
use Dompdf\Options;

class PenggajianPerhitungan extends BaseController
{
    protected $perhitunganModel;
    protected $komponenModel;
    protected $karyawanModel;
    protected $db;

    public function __construct()
    {
        $this->perhitunganModel = new PenggajianPerhitunganModel();
        $this->komponenModel = new PenggajianKomponenModel();
        $this->karyawanModel = new KaryawanModel();
        $this->db = \Config\Database::connect();
        
        helper(['form', 'url', 'text', 'number']);
        
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Daftar perhitungan gaji
     */
    public function index()
    {
        $data['title'] = 'Perhitungan Gaji Karyawan';
        
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $status = $this->request->getGet('status');
        $karyawanId = $this->request->getGet('karyawan_id');
        
        $data['tahun'] = $tahun;
        $data['bulan'] = $bulan;
        $data['status'] = $status;
        $data['karyawan_id'] = $karyawanId;
        
        $data['tahunOptions'] = $this->getTahunOptions();
        $data['bulanOptions'] = $this->getBulanOptions();
        $data['statusOptions'] = ['Draft', 'Dihitung', 'Disetujui', 'Ditolak'];
        
        // Ambil data perhitungan
        $builder = $this->perhitunganModel->select('penggajian_perhitungan.*, karyawan.nik, karyawan.nama_lengkap, karyawan.jabatan, karyawan.departemen')
            ->join('karyawan', 'karyawan.id = penggajian_perhitungan.karyawan_id')
            ->where('penggajian_perhitungan.periode_bulan', $bulan)
            ->where('penggajian_perhitungan.periode_tahun', $tahun)
            ->where('penggajian_perhitungan.deleted_at IS NULL');
        
        if ($status) {
            $builder->where('penggajian_perhitungan.status', $status);
        }
        
        if ($karyawanId) {
            $builder->where('penggajian_perhitungan.karyawan_id', $karyawanId);
        }
        
        $data['perhitungan'] = $builder->orderBy('karyawan.nama_lengkap', 'ASC')->findAll();
        
        // Ringkasan periode
        $data['ringkasan'] = $this->perhitunganModel->getRingkasanPeriode($bulan, $tahun);
        
        // Status count
        $data['statusCount'] = [
            'total' => $this->perhitunganModel->where('periode_bulan', $bulan)->where('periode_tahun', $tahun)->countAllResults(),
            'draft' => $this->perhitunganModel->where('periode_bulan', $bulan)->where('periode_tahun', $tahun)->where('status', 'Draft')->countAllResults(),
            'dihitung' => $this->perhitunganModel->where('periode_bulan', $bulan)->where('periode_tahun', $tahun)->where('status', 'Dihitung')->countAllResults(),
            'disetujui' => $this->perhitunganModel->where('periode_bulan', $bulan)->where('periode_tahun', $tahun)->where('status', 'Disetujui')->countAllResults(),
            'ditolak' => $this->perhitunganModel->where('periode_bulan', $bulan)->where('periode_tahun', $tahun)->where('status', 'Ditolak')->countAllResults()
        ];
        
        // Data karyawan untuk filter
        $data['karyawanOptions'] = $this->karyawanModel->select('id, nik, nama_lengkap')
            ->where('deleted_at IS NULL')
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();
        
        return view('accounting/penggajian/perhitungan-gaji/index', $data);
    }

    /**
     * Form perhitungan gaji per karyawan
     */
    public function create()
    {
        $data['title'] = 'Tambah Perhitungan Gaji';
        
        $karyawanId = $this->request->getGet('karyawan_id');
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        // Cek apakah sudah ada perhitungan untuk periode ini
        if ($karyawanId && $this->perhitunganModel->existsForPeriod($karyawanId, $bulan, $tahun)) {
            return redirect()->to('accounting/penggajian/perhitungan-gaji')
                ->with('error', 'Perhitungan gaji untuk karyawan ini pada periode ' . $this->getNamaBulan($bulan) . ' ' . $tahun . ' sudah ada');
        }
        
        // Ambil data karyawan
        if ($karyawanId) {
            $data['karyawan'] = $this->karyawanModel->find($karyawanId);
            
            if (!$data['karyawan']) {
                return redirect()->to('accounting/penggajian/perhitungan-gaji')
                    ->with('error', 'Karyawan tidak ditemukan');
            }
        } else {
            $data['karyawan'] = null;
        }
        
        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;
        $data['bulanOptions'] = $this->getBulanOptions();
        $data['tahunOptions'] = $this->getTahunOptions();
        
        // Ambil daftar karyawan untuk dropdown
        $data['karyawanOptions'] = $this->karyawanModel->select('id, nik, nama_lengkap, jabatan')
            ->where('deleted_at IS NULL')
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();
        
        // Ambil komponen gaji aktif
        $data['komponenPendapatan'] = $this->komponenModel->getPendapatanOptions();
        $data['komponenPotongan'] = $this->komponenModel->getPotonganOptions();
        
        // Set default values
        $data['perhitungan'] = [
            'gaji_pokok' => $data['karyawan'] ? ($data['karyawan']['gaji_pokok'] ?? 0) : 0,
            'tunjangan_jabatan' => 0,
            'tunjangan_makan' => 0,
            'tunjangan_transport' => 0,
            'tunjangan_lainnya' => 0,
            'potongan_bpjs_kes' => 0,
            'potongan_bpjs_tk' => 0,
            'potongan_pph21' => 0,
            'potongan_absensi' => 0,
            'potongan_kasbon' => 0,
            'potongan_lainnya' => 0,
            'total_hadir' => 0,
            'total_izin' => 0,
            'total_sakit' => 0,
            'total_cuti' => 0,
            'total_alpha' => 0,
            'total_terlambat' => 0,
            'jam_lembur' => 0,
            'upah_lembur' => 0,
            'catatan' => ''
        ];
        
        $data['mode'] = 'manual';
        
        return view('accounting/penggajian/perhitungan-gaji/create', $data);
    }

    /**
     * Form perhitungan dengan mode tetap (fixed)
     */
    public function createFixed()
    {
        $data['title'] = 'Tambah Perhitungan Gaji (Mode Tetap)';
        
        $karyawanId = $this->request->getGet('karyawan_id');
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        // Cek apakah sudah ada perhitungan untuk periode ini
        if ($karyawanId && $this->perhitunganModel->existsForPeriod($karyawanId, $bulan, $tahun)) {
            return redirect()->to('accounting/penggajian/perhitungan-gaji')
                ->with('error', 'Perhitungan gaji untuk karyawan ini pada periode ' . $this->getNamaBulan($bulan) . ' ' . $tahun . ' sudah ada');
        }
        
        // Ambil data karyawan
        if ($karyawanId) {
            $data['karyawan'] = $this->karyawanModel->find($karyawanId);
            
            if (!$data['karyawan']) {
                return redirect()->to('accounting/penggajian/perhitungan-gaji')
                    ->with('error', 'Karyawan tidak ditemukan');
            }
        } else {
            $data['karyawan'] = null;
        }
        
        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;
        $data['bulanOptions'] = $this->getBulanOptions();
        $data['tahunOptions'] = $this->getTahunOptions();
        
        // Ambil daftar karyawan untuk dropdown
        $data['karyawanOptions'] = $this->karyawanModel->select('id, nik, nama_lengkap, jabatan')
            ->where('deleted_at IS NULL')
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();
        
        // Set default values
        $data['perhitungan'] = [
            'gaji_pokok' => $data['karyawan'] ? ($data['karyawan']['gaji_pokok'] ?? 0) : 0,
            'tunjangan_jabatan' => $data['karyawan'] ? ($data['karyawan']['tunjangan_jabatan'] ?? 0) : 0,
            'tunjangan_makan' => $data['karyawan'] ? ($data['karyawan']['tunjangan_makan'] ?? 0) : 0,
            'tunjangan_transport' => $data['karyawan'] ? ($data['karyawan']['tunjangan_transport'] ?? 0) : 0,
            'tunjangan_lainnya' => 0,
            'potongan_bpjs_kes' => 0,
            'potongan_bpjs_tk' => 0,
            'potongan_pph21' => 0,
            'potongan_absensi' => 0,
            'potongan_kasbon' => 0,
            'potongan_lainnya' => 0,
            'gaji_bersih' => $data['karyawan'] ? ($data['karyawan']['gaji_pokok'] ?? 0) : 0,
            'catatan' => 'Perhitungan mode tetap'
        ];
        
        $data['mode'] = 'fixed';
        
        return view('accounting/penggajian/perhitungan-gaji/create-fixed', $data);
    }

    /**
     * Simpan perhitungan gaji
     */
    public function store()
    {
        $mode = $this->request->getPost('mode');
        
        $rules = [
            'karyawan_id' => 'required|is_natural_no_zero',
            'periode_bulan' => 'required|in_list[1,2,3,4,5,6,7,8,9,10,11,12]',
            'periode_tahun' => 'required|numeric|min_length[4]|max_length[4]',
            'tanggal_perhitungan' => 'required|valid_date'
        ];
        
        if ($mode === 'fixed') {
            $rules['gaji_bersih'] = 'required|numeric';
        } else {
            $rules['gaji_pokok'] = 'permit_empty|numeric';
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $karyawanId = $this->request->getPost('karyawan_id');
        $bulan = $this->request->getPost('periode_bulan');
        $tahun = $this->request->getPost('periode_tahun');
        
        // Cek duplikasi
        if ($this->perhitunganModel->existsForPeriod($karyawanId, $bulan, $tahun)) {
            return redirect()->back()->withInput()
                ->with('error', 'Perhitungan gaji untuk karyawan ini pada periode ' . $this->getNamaBulan($bulan) . ' ' . $tahun . ' sudah ada');
        }
        
        $data = [
            'karyawan_id' => $karyawanId,
            'periode_bulan' => $bulan,
            'periode_tahun' => $tahun,
            'tanggal_perhitungan' => $this->request->getPost('tanggal_perhitungan'),
            'gaji_pokok' => $this->request->getPost('gaji_pokok') ?? 0,
            'tunjangan_jabatan' => $this->request->getPost('tunjangan_jabatan') ?? 0,
            'tunjangan_makan' => $this->request->getPost('tunjangan_makan') ?? 0,
            'tunjangan_transport' => $this->request->getPost('tunjangan_transport') ?? 0,
            'tunjangan_lainnya' => $this->request->getPost('tunjangan_lainnya') ?? 0,
            'potongan_bpjs_kes' => $this->request->getPost('potongan_bpjs_kes') ?? 0,
            'potongan_bpjs_tk' => $this->request->getPost('potongan_bpjs_tk') ?? 0,
            'potongan_pph21' => $this->request->getPost('potongan_pph21') ?? 0,
            'potongan_absensi' => $this->request->getPost('potongan_absensi') ?? 0,
            'potongan_kasbon' => $this->request->getPost('potongan_kasbon') ?? 0,
            'potongan_lainnya' => $this->request->getPost('potongan_lainnya') ?? 0,
            'total_hari_kerja' => $this->request->getPost('total_hari_kerja') ?? 0,
            'total_hadir' => $this->request->getPost('total_hadir') ?? 0,
            'total_izin' => $this->request->getPost('total_izin') ?? 0,
            'total_sakit' => $this->request->getPost('total_sakit') ?? 0,
            'total_cuti' => $this->request->getPost('total_cuti') ?? 0,
            'total_alpha' => $this->request->getPost('total_alpha') ?? 0,
            'total_terlambat' => $this->request->getPost('total_terlambat') ?? 0,
            'jam_lembur' => $this->request->getPost('jam_lembur') ?? 0,
            'upah_lembur' => $this->request->getPost('upah_lembur') ?? 0,
            'catatan' => $this->request->getPost('catatan'),
            'status' => 'Draft'
        ];
        
        // Untuk mode fixed, hitung langsung
        if ($mode === 'fixed') {
            $gajiBersih = $this->request->getPost('gaji_bersih');
            $data['gaji_bersih'] = $gajiBersih;
            $data['total_pendapatan'] = $gajiBersih + ($data['total_potongan'] ?? 0);
        }
        
        try {
            $this->db->transBegin();
            
            $saved = $this->perhitunganModel->save($data);
            
            if (!$saved) {
                throw new \Exception('Gagal menyimpan data: ' . json_encode($this->perhitunganModel->errors()));
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/penggajian/perhitungan-gaji')
                ->with('success', 'Perhitungan gaji berhasil disimpan.');
                
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menyimpan perhitungan gaji: ' . $e->getMessage());
        }
    }

    /**
     * Detail perhitungan gaji
     */
    public function detail($id)
    {
        $data['title'] = 'Detail Perhitungan Gaji';
        
        $perhitungan = $this->perhitunganModel->getWithDetails($id);
        
        if (!$perhitungan) {
            return redirect()->to('accounting/penggajian/perhitungan-gaji')
                ->with('error', 'Perhitungan gaji tidak ditemukan');
        }
        
        $data['perhitungan'] = $perhitungan;
        
        return view('accounting/penggajian/perhitungan-gaji/detail', $data);
    }

    /**
     * Edit perhitungan gaji
     */
    public function edit($id)
    {
        $data['title'] = 'Edit Perhitungan Gaji';
        
        $perhitungan = $this->perhitunganModel->find($id);
        
        if (!$perhitungan) {
            return redirect()->to('accounting/penggajian/perhitungan-gaji')
                ->with('error', 'Perhitungan gaji tidak ditemukan');
        }
        
        if ($perhitungan['status'] !== 'Draft') {
            return redirect()->back()
                ->with('error', 'Hanya perhitungan dengan status Draft yang dapat diedit');
        }
        
        // Ambil data karyawan
        $karyawan = $this->karyawanModel->find($perhitungan['karyawan_id']);
        
        $data['perhitungan'] = $perhitungan;
        $data['karyawan'] = $karyawan;
        $data['bulanOptions'] = $this->getBulanOptions();
        $data['tahunOptions'] = $this->getTahunOptions();
        
        // Ambil komponen gaji aktif
        $data['komponenPendapatan'] = $this->komponenModel->getPendapatanOptions();
        $data['komponenPotongan'] = $this->komponenModel->getPotonganOptions();
        
        return view('accounting/penggajian/perhitungan-gaji/edit', $data);
    }

    /**
     * Update perhitungan gaji
     */
    public function update($id)
    {
        $perhitungan = $this->perhitunganModel->find($id);
        
        if (!$perhitungan) {
            return redirect()->to('accounting/penggajian/perhitungan-gaji')
                ->with('error', 'Perhitungan gaji tidak ditemukan');
        }
        
        if ($perhitungan['status'] !== 'Draft') {
            return redirect()->back()
                ->with('error', 'Hanya perhitungan dengan status Draft yang dapat diedit');
        }
        
        $rules = [
            'tanggal_perhitungan' => 'required|valid_date',
            'gaji_pokok' => 'permit_empty|numeric',
            'tunjangan_jabatan' => 'permit_empty|numeric',
            'tunjangan_makan' => 'permit_empty|numeric',
            'tunjangan_transport' => 'permit_empty|numeric',
            'tunjangan_lainnya' => 'permit_empty|numeric',
            'potongan_bpjs_kes' => 'permit_empty|numeric',
            'potongan_bpjs_tk' => 'permit_empty|numeric',
            'potongan_pph21' => 'permit_empty|numeric',
            'potongan_absensi' => 'permit_empty|numeric',
            'potongan_kasbon' => 'permit_empty|numeric',
            'potongan_lainnya' => 'permit_empty|numeric'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'id' => $id,
            'tanggal_perhitungan' => $this->request->getPost('tanggal_perhitungan'),
            'gaji_pokok' => $this->request->getPost('gaji_pokok') ?? 0,
            'tunjangan_jabatan' => $this->request->getPost('tunjangan_jabatan') ?? 0,
            'tunjangan_makan' => $this->request->getPost('tunjangan_makan') ?? 0,
            'tunjangan_transport' => $this->request->getPost('tunjangan_transport') ?? 0,
            'tunjangan_lainnya' => $this->request->getPost('tunjangan_lainnya') ?? 0,
            'potongan_bpjs_kes' => $this->request->getPost('potongan_bpjs_kes') ?? 0,
            'potongan_bpjs_tk' => $this->request->getPost('potongan_bpjs_tk') ?? 0,
            'potongan_pph21' => $this->request->getPost('potongan_pph21') ?? 0,
            'potongan_absensi' => $this->request->getPost('potongan_absensi') ?? 0,
            'potongan_kasbon' => $this->request->getPost('potongan_kasbon') ?? 0,
            'potongan_lainnya' => $this->request->getPost('potongan_lainnya') ?? 0,
            'total_hari_kerja' => $this->request->getPost('total_hari_kerja') ?? 0,
            'total_hadir' => $this->request->getPost('total_hadir') ?? 0,
            'total_izin' => $this->request->getPost('total_izin') ?? 0,
            'total_sakit' => $this->request->getPost('total_sakit') ?? 0,
            'total_cuti' => $this->request->getPost('total_cuti') ?? 0,
            'total_alpha' => $this->request->getPost('total_alpha') ?? 0,
            'total_terlambat' => $this->request->getPost('total_terlambat') ?? 0,
            'jam_lembur' => $this->request->getPost('jam_lembur') ?? 0,
            'upah_lembur' => $this->request->getPost('upah_lembur') ?? 0,
            'catatan' => $this->request->getPost('catatan')
        ];
        
        try {
            $this->db->transBegin();
            
            $updated = $this->perhitunganModel->save($data);
            
            if (!$updated) {
                throw new \Exception('Gagal mengupdate data: ' . json_encode($this->perhitunganModel->errors()));
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/penggajian/perhitungan-gaji')
                ->with('success', 'Perhitungan gaji berhasil diupdate.');
                
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal mengupdate perhitungan gaji: ' . $e->getMessage());
        }
    }

    /**
     * Hapus perhitungan gaji
     */
    public function delete($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $perhitungan = $this->perhitunganModel->find($id);
        
        if (!$perhitungan) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Perhitungan gaji tidak ditemukan'
                ]);
            }
            return redirect()->to('accounting/penggajian/perhitungan-gaji')
                ->with('error', 'Perhitungan gaji tidak ditemukan');
        }
        
        if ($perhitungan['status'] === 'Disetujui') {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Perhitungan gaji yang sudah disetujui tidak dapat dihapus'
                ]);
            }
            return redirect()->back()
                ->with('error', 'Perhitungan gaji yang sudah disetujui tidak dapat dihapus');
        }
        
        try {
            $this->db->transBegin();
            
            $deleted = $this->perhitunganModel->delete($id);
            
            if (!$deleted) {
                throw new \Exception('Gagal menghapus data');
            }
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Perhitungan gaji berhasil dihapus',
                    'redirect' => site_url('accounting/penggajian/perhitungan-gaji')
                ]);
            }
            
            return redirect()->to('accounting/penggajian/perhitungan-gaji')
                ->with('success', 'Perhitungan gaji berhasil dihapus');
                
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus perhitungan gaji: ' . $e->getMessage()
                ]);
            }
            
            return redirect()->back()
                ->with('error', 'Gagal menghapus perhitungan gaji: ' . $e->getMessage());
        }
    }

    /**
     * Hitung otomatis perhitungan gaji
     */
    public function hitung($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $perhitungan = $this->perhitunganModel->find($id);
        
        if (!$perhitungan) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Perhitungan gaji tidak ditemukan'
                ]);
            }
            return redirect()->back()->with('error', 'Perhitungan gaji tidak ditemukan');
        }
        
        if ($perhitungan['status'] !== 'Draft') {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Hanya perhitungan dengan status Draft yang dapat dihitung'
                ]);
            }
            return redirect()->back()->with('error', 'Hanya perhitungan dengan status Draft yang dapat dihitung');
        }
        
        // Hitung total pendapatan dan potongan
        $totalPendapatan = ($perhitungan['gaji_pokok'] ?? 0) +
                           ($perhitungan['tunjangan_jabatan'] ?? 0) +
                           ($perhitungan['tunjangan_makan'] ?? 0) +
                           ($perhitungan['tunjangan_transport'] ?? 0) +
                           ($perhitungan['tunjangan_lainnya'] ?? 0) +
                           ($perhitungan['upah_lembur'] ?? 0);
        
        $totalPotongan = ($perhitungan['potongan_bpjs_kes'] ?? 0) +
                         ($perhitungan['potongan_bpjs_tk'] ?? 0) +
                         ($perhitungan['potongan_pph21'] ?? 0) +
                         ($perhitungan['potongan_absensi'] ?? 0) +
                         ($perhitungan['potongan_kasbon'] ?? 0) +
                         ($perhitungan['potongan_lainnya'] ?? 0);
        
        $gajiBersih = $totalPendapatan - $totalPotongan;
        
        $updateData = [
            'total_pendapatan' => $totalPendapatan,
            'total_potongan' => $totalPotongan,
            'gaji_bersih' => $gajiBersih,
            'status' => 'Dihitung'
        ];
        
        try {
            $this->db->transBegin();
            
            $updated = $this->perhitunganModel->update($id, $updateData);
            
            if (!$updated) {
                throw new \Exception('Gagal menghitung gaji');
            }
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Perhitungan gaji berhasil dihitung',
                    'data' => $updateData
                ]);
            }
            
            return redirect()->back()
                ->with('success', 'Perhitungan gaji berhasil dihitung');
                
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghitung gaji: ' . $e->getMessage()
                ]);
            }
            
            return redirect()->back()
                ->with('error', 'Gagal menghitung gaji: ' . $e->getMessage());
        }
    }

    /**
     * Setujui perhitungan gaji
     */
    public function approve($id)
    {
        $isAjax = $this->request->isAJAX();
        
        try {
            $result = $this->perhitunganModel->approve($id);
            
            if (!$result) {
                throw new \Exception('Gagal menyetujui perhitungan gaji');
            }
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Perhitungan gaji berhasil disetujui'
                ]);
            }
            
            return redirect()->back()
                ->with('success', 'Perhitungan gaji berhasil disetujui');
                
        } catch (\Exception $e) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Tolak perhitungan gaji
     */
    public function reject($id)
    {
        $isAjax = $this->request->isAJAX();
        $catatan = $this->request->getPost('catatan');
        
        try {
            $result = $this->perhitunganModel->reject($id, $catatan);
            
            if (!$result) {
                throw new \Exception('Gagal menolak perhitungan gaji');
            }
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Perhitungan gaji berhasil ditolak'
                ]);
            }
            
            return redirect()->back()
                ->with('success', 'Perhitungan gaji berhasil ditolak');
                
        } catch (\Exception $e) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Hitung massal per periode
     */
    public function hitungMassal()
    {
        $data['title'] = 'Hitung Gaji Massal';
        
        $bulan = $this->request->getPost('bulan') ?? $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getPost('tahun') ?? $this->request->getGet('tahun') ?? date('Y');
        
        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;
        $data['bulanOptions'] = $this->getBulanOptions();
        $data['tahunOptions'] = $this->getTahunOptions();
        
        // Ambil karyawan aktif yang belum memiliki perhitungan
        $karyawanAktif = $this->karyawanModel->select('id, nik, nama_lengkap, jabatan, departemen, gaji_pokok, tunjangan_jabatan, tunjangan_makan, tunjangan_transport')
            ->where('deleted_at IS NULL')
            ->where('tanggal_keluar IS NULL')
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();
        
        // Filter yang belum ada perhitungan
        $karyawanBelum = [];
        foreach ($karyawanAktif as $karyawan) {
            if (!$this->perhitunganModel->existsForPeriod($karyawan['id'], $bulan, $tahun)) {
                $karyawanBelum[] = $karyawan;
            }
        }
        
        $data['karyawan'] = $karyawanBelum;
        $data['totalKaryawan'] = count($karyawanBelum);
        
        // Ambil komponen gaji untuk referensi
        $data['komponenPendapatan'] = $this->komponenModel->getPendapatanOptions();
        $data['komponenPotongan'] = $this->komponenModel->getPotonganOptions();
        
        return view('accounting/penggajian/perhitungan-gaji/hitung-massal', $data);
    }

    /**
     * Proses hitung massal
     */
    public function prosesHitungMassal()
    {
        $bulan = $this->request->getPost('periode_bulan');
        $tahun = $this->request->getPost('periode_tahun');
        $tanggalPerhitungan = $this->request->getPost('tanggal_perhitungan');
        $karyawanIds = $this->request->getPost('karyawan_id') ?? [];
        $mode = $this->request->getPost('mode') ?? 'system';
        
        if (empty($karyawanIds)) {
            return redirect()->back()->withInput()
                ->with('error', 'Tidak ada karyawan yang dipilih');
        }
        
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        foreach ($karyawanIds as $karyawanId) {
            // Cek duplikasi
            if ($this->perhitunganModel->existsForPeriod($karyawanId, $bulan, $tahun)) {
                $errors[] = "Karyawan ID $karyawanId sudah memiliki perhitungan untuk periode ini";
                $errorCount++;
                continue;
            }
            
            // Ambil data karyawan
            $karyawan = $this->karyawanModel->find($karyawanId);
            
            if (!$karyawan) {
                $errors[] = "Karyawan ID $karyawanId tidak ditemukan";
                $errorCount++;
                continue;
            }
            
            if ($mode === 'fixed') {
                // Mode tetap: gunakan gaji bersih dari input
                $gajiBersih = $this->request->getPost('gaji_bersih_' . $karyawanId);
                
                $data = [
                    'karyawan_id' => $karyawanId,
                    'periode_bulan' => $bulan,
                    'periode_tahun' => $tahun,
                    'tanggal_perhitungan' => $tanggalPerhitungan,
                    'gaji_bersih' => $gajiBersih ?? 0,
                    'gaji_pokok' => $karyawan['gaji_pokok'] ?? 0,
                    'tunjangan_jabatan' => $karyawan['tunjangan_jabatan'] ?? 0,
                    'tunjangan_makan' => $karyawan['tunjangan_makan'] ?? 0,
                    'tunjangan_transport' => $karyawan['tunjangan_transport'] ?? 0,
                    'total_pendapatan' => $gajiBersih ?? 0,
                    'status' => 'Dihitung',
                    'catatan' => 'Perhitungan mode tetap'
                ];
            } else {
                // Mode sistem: gunakan data dari karyawan
                $totalPendapatan = ($karyawan['gaji_pokok'] ?? 0) +
                                   ($karyawan['tunjangan_jabatan'] ?? 0) +
                                   ($karyawan['tunjangan_makan'] ?? 0) +
                                   ($karyawan['tunjangan_transport'] ?? 0);
                
                $data = [
                    'karyawan_id' => $karyawanId,
                    'periode_bulan' => $bulan,
                    'periode_tahun' => $tahun,
                    'tanggal_perhitungan' => $tanggalPerhitungan,
                    'gaji_pokok' => $karyawan['gaji_pokok'] ?? 0,
                    'tunjangan_jabatan' => $karyawan['tunjangan_jabatan'] ?? 0,
                    'tunjangan_makan' => $karyawan['tunjangan_makan'] ?? 0,
                    'tunjangan_transport' => $karyawan['tunjangan_transport'] ?? 0,
                    'total_pendapatan' => $totalPendapatan,
                    'gaji_bersih' => $totalPendapatan,
                    'status' => 'Dihitung',
                    'catatan' => 'Perhitungan sistem'
                ];
            }
            
            try {
                $saved = $this->perhitunganModel->insert($data);
                if ($saved) {
                    $successCount++;
                } else {
                    $errors[] = "Gagal menyimpan perhitungan untuk " . $karyawan['nama_lengkap'];
                    $errorCount++;
                }
            } catch (\Exception $e) {
                $errors[] = "Error untuk " . $karyawan['nama_lengkap'] . ": " . $e->getMessage();
                $errorCount++;
            }
        }
        
        $message = "Berhasil menghitung $successCount karyawan";
        if ($errorCount > 0) {
            $message .= ", gagal $errorCount karyawan";
        }
        
        if (!empty($errors)) {
            session()->setFlashdata('errors_detail', $errors);
        }
        
        return redirect()->to('accounting/penggajian/perhitungan-gaji')
            ->with('success', $message);
    }

    /**
     * Export perhitungan gaji ke Excel
     */
    public function exportExcel()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $status = $this->request->getGet('status');
        
        $data = $this->perhitunganModel->getExportData([
            'periode_bulan' => $bulan,
            'periode_tahun' => $tahun,
            'status' => $status
        ]);
        
        try {
            $spreadsheet = new Spreadsheet();
            
            $spreadsheet->getProperties()
                ->setCreator("CDW Engineering")
                ->setLastModifiedBy("CDW Engineering")
                ->setTitle("Perhitungan Gaji " . $this->getNamaBulan($bulan) . " $tahun")
                ->setSubject("Perhitungan Gaji")
                ->setDescription("Perhitungan Gaji " . $this->getNamaBulan($bulan) . " $tahun");
            
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Perhitungan Gaji');
            
            // Header
            $sheet->mergeCells('A1:S1');
            $sheet->setCellValue('A1', 'LAPORAN PERHITUNGAN GAJI');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A2:S2');
            $sheet->setCellValue('A2', 'PT. CIPTA DUTA WACANA');
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A3:S3');
            $sheet->setCellValue('A3', 'Periode: ' . $this->getNamaBulan($bulan) . ' ' . $tahun);
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A4:S4');
            $sheet->setCellValue('A4', 'Dicetak: ' . date('d/m/Y H:i:s'));
            $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Header tabel
            $headers = [
                'A' => 'No',
                'B' => 'Nomor Perhitungan',
                'C' => 'NIK',
                'D' => 'Nama Karyawan',
                'E' => 'Jabatan',
                'F' => 'Departemen',
                'G' => 'Gaji Pokok',
                'H' => 'Tunjangan Jabatan',
                'I' => 'Tunjangan Makan',
                'J' => 'Tunjangan Transport',
                'K' => 'Upah Lembur',
                'L' => 'Total Pendapatan',
                'M' => 'Potongan BPJS Kes',
                'N' => 'Potongan BPJS TK',
                'O' => 'Potongan PPh 21',
                'P' => 'Potongan Absensi',
                'Q' => 'Potongan Kasbon',
                'R' => 'Total Potongan',
                'S' => 'Gaji Bersih'
            ];
            
            $startRow = 6;
            foreach ($headers as $col => $header) {
                $sheet->setCellValue($col . $startRow, $header);
            }
            
            // Style header
            $headerRange = 'A' . $startRow . ':S' . $startRow;
            $sheet->getStyle($headerRange)->getFont()->setBold(true);
            $sheet->getStyle($headerRange)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF4F81BD');
            $sheet->getStyle($headerRange)->getFont()->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
            
            // Isi data
            $row = $startRow + 1;
            $no = 1;
            foreach ($data as $item) {
                $sheet->setCellValue('A' . $row, $no);
                $sheet->setCellValue('B' . $row, $item['Nomor Perhitungan']);
                $sheet->setCellValue('C' . $row, $item['NIK']);
                $sheet->setCellValue('D' . $row, $item['Nama Karyawan']);
                $sheet->setCellValue('E' . $row, $item['Jabatan']);
                $sheet->setCellValue('F' . $row, $item['Departemen']);
                $sheet->setCellValue('G' . $row, $item['Gaji Pokok']);
                $sheet->setCellValue('H' . $row, $item['Tunjangan Jabatan']);
                $sheet->setCellValue('I' . $row, $item['Tunjangan Makan']);
                $sheet->setCellValue('J' . $row, $item['Tunjangan Transport']);
                $sheet->setCellValue('K' . $row, $item['Upah Lembur']);
                $sheet->setCellValue('L' . $row, $item['Total Pendapatan']);
                $sheet->setCellValue('M' . $row, $item['Potongan BPJS Kes']);
                $sheet->setCellValue('N' . $row, $item['Potongan BPJS TK']);
                $sheet->setCellValue('O' . $row, $item['Potongan PPh 21']);
                $sheet->setCellValue('P' . $row, $item['Potongan Absensi']);
                $sheet->setCellValue('Q' . $row, $item['Potongan Kasbon']);
                $sheet->setCellValue('R' . $row, $item['Total Potongan']);
                $sheet->setCellValue('S' . $row, $item['Gaji Bersih']);
                
                // Format currency
                foreach (range('G', 'S') as $col) {
                    $sheet->getStyle($col . $row)->getNumberFormat()
                        ->setFormatCode('#,##0');
                }
                
                $row++;
                $no++;
            }
            
            // Auto-size columns
            foreach (range('A', 'S') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Border untuk seluruh data
            $lastRow = $row - 1;
            if ($lastRow >= $startRow) {
                $dataRange = 'A' . $startRow . ':S' . $lastRow;
                $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            }
            
            // Footer
            $footerRow = $lastRow + 2;
            $sheet->mergeCells('A' . $footerRow . ':S' . $footerRow);
            $sheet->setCellValue('A' . $footerRow, 'Dicetak oleh: ' . session()->get('name'));
            $sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Output file
            $filename = 'Perhitungan_Gaji_' . $this->getNamaBulan($bulan) . '_' . $tahun . '.xlsx';
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit();
            
        } catch (\Exception $e) {
            log_message('error', 'Export Excel error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export Excel: ' . $e->getMessage());
        }
    }

    /**
     * Export perhitungan gaji ke PDF
     */
    public function exportPdf()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $status = $this->request->getGet('status');
        
        $data = $this->perhitunganModel->getExportData([
            'periode_bulan' => $bulan,
            'periode_tahun' => $tahun,
            'status' => $status
        ]);
        
        $ringkasan = $this->perhitunganModel->getRingkasanPeriode($bulan, $tahun);
        
        $html = $this->generatePdfHtml($data, $ringkasan, $bulan, $tahun);
        
        try {
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->set('defaultFont', 'Arial');
            
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            
            $filename = 'Perhitungan_Gaji_' . $this->getNamaBulan($bulan) . '_' . $tahun . '.pdf';
            $dompdf->stream($filename, ['Attachment' => true]);
            exit();
            
        } catch (\Exception $e) {
            log_message('error', 'Export PDF error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal export PDF: ' . $e->getMessage());
        }
    }

    /**
     * Generate HTML untuk PDF
     */
    private function generatePdfHtml($data, $ringkasan, $bulan, $tahun)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Perhitungan Gaji ' . $this->getNamaBulan($bulan) . ' ' . $tahun . '</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 9px;
                    margin: 15px;
                }
                h1 {
                    text-align: center;
                    font-size: 16px;
                    margin-bottom: 5px;
                }
                h2 {
                    text-align: center;
                    font-size: 12px;
                    color: #666;
                    margin-top: 0;
                    margin-bottom: 10px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 15px;
                }
                .summary {
                    margin-bottom: 15px;
                    padding: 8px;
                    background-color: #f8f9fa;
                    border: 1px solid #dee2e6;
                }
                .summary table {
                    width: 100%;
                    border-collapse: collapse;
                }
                .summary td {
                    padding: 3px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 15px;
                }
                th {
                    background-color: #4F81BD;
                    color: white;
                    padding: 5px;
                    text-align: center;
                    font-weight: bold;
                    border: 1px solid #000;
                }
                td {
                    padding: 4px;
                    border: 1px solid #000;
                    vertical-align: top;
                }
                .text-center {
                    text-align: center;
                }
                .text-right {
                    text-align: right;
                }
                .footer {
                    margin-top: 15px;
                    padding-top: 8px;
                    border-top: 1px solid #000;
                    font-size: 8px;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>LAPORAN PERHITUNGAN GAJI</h1>
                <h2>PT. CIPTA DUTA WACANA</h2>
                <div>Periode: ' . $this->getNamaBulan($bulan) . ' ' . $tahun . '</div>
                <div>Dicetak: ' . date('d/m/Y H:i:s') . '</div>
            </div>
            
            <div class="summary">
                <table>
                    <tr>
                        <td width="33%"><strong>Total Karyawan:</strong> ' . number_format($ringkasan['jumlah_karyawan'] ?? 0) . ' orang</td>
                        <td width="33%"><strong>Total Gaji Pokok:</strong> Rp ' . number_format($ringkasan['total_gaji_pokok'] ?? 0, 0, ',', '.') . '</td>
                        <td width="34%"><strong>Total Gaji Bersih:</strong> Rp ' . number_format($ringkasan['total_gaji_bersih'] ?? 0, 0, ',', '.') . '</td>
                    </tr>
                </table>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th width="3%">No</th>
                        <th width="8%">Nomor</th>
                        <th width="8%">NIK</th>
                        <th width="15%">Nama Karyawan</th>
                        <th width="10%">Jabatan</th>
                        <th width="8%">Gaji Pokok</th>
                        <th width="8%">Tunjangan</th>
                        <th width="8%">Total Pendapatan</th>
                        <th width="8%">Potongan</th>
                        <th width="8%">Gaji Bersih</th>
                    </tr>
                </thead>
                <tbody>';
        
        if (empty($data)) {
            $html .= '<tr><td colspan="10" class="text-center">Tidak ada data perhitungan gaji</td></tr>';
        } else {
            $no = 1;
            foreach ($data as $item) {
                $html .= '
                    <tr>
                        <td class="text-center">' . $no . '</td>
                        <td class="text-center">' . $item['Nomor Perhitungan'] . '</td>
                        <td class="text-center">' . $item['NIK'] . '</td>
                        <td>' . $item['Nama Karyawan'] . '</td>
                        <td>' . $item['Jabatan'] . '</td>
                        <td class="text-right">' . number_format($item['Gaji Pokok'], 0, ',', '.') . '</td>
                        <td class="text-right">' . number_format(($item['Tunjangan Jabatan'] ?? 0) + ($item['Tunjangan Makan'] ?? 0) + ($item['Tunjangan Transport'] ?? 0), 0, ',', '.') . '</td>
                        <td class="text-right">' . number_format($item['Total Pendapatan'], 0, ',', '.') . '</td>
                        <td class="text-right">' . number_format($item['Total Potongan'], 0, ',', '.') . '</td>
                        <td class="text-right">' . number_format($item['Gaji Bersih'], 0, ',', '.') . '</td>
                    </tr>';
                $no++;
            }
        }
        
        $html .= '
                </tbody>
            </table>
            
            <div class="footer">
                <table style="width: 100%; border: none;">
                    <tr>
                        <td style="border: none; text-align: left;">
                            <strong>Total Data:</strong> ' . count($data) . ' karyawan
                        </td>
                        <td style="border: none; text-align: right;">
                            Dicetak oleh: ' . session()->get('name') . '
                        </td>
                    </tr>
                </table>
            </div>
        </body>
        </html>';
        
        return $html;
    }

    /**
     * AJAX: Hitung gaji otomatis
     */
    public function ajaxHitungGaji()
    {
        $karyawanId = $this->request->getPost('karyawan_id');
        $bulan = $this->request->getPost('bulan');
        $tahun = $this->request->getPost('tahun');
        $mode = $this->request->getPost('mode') ?? 'system';
        
        $karyawan = $this->karyawanModel->find($karyawanId);
        
        if (!$karyawan) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan'
            ]);
        }
        
        if ($mode === 'fixed') {
            // Mode tetap: kembalikan gaji bersih dari data karyawan
            $gajiBersih = $karyawan['gaji_pokok'] ?? 0;
            
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'gaji_bersih' => $gajiBersih,
                    'gaji_pokok' => $karyawan['gaji_pokok'] ?? 0,
                    'tunjangan_jabatan' => $karyawan['tunjangan_jabatan'] ?? 0,
                    'tunjangan_makan' => $karyawan['tunjangan_makan'] ?? 0,
                    'tunjangan_transport' => $karyawan['tunjangan_transport'] ?? 0
                ]
            ]);
        }
        
        // Mode sistem: hitung total pendapatan
        $totalPendapatan = ($karyawan['gaji_pokok'] ?? 0) +
                           ($karyawan['tunjangan_jabatan'] ?? 0) +
                           ($karyawan['tunjangan_makan'] ?? 0) +
                           ($karyawan['tunjangan_transport'] ?? 0);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'total_pendapatan' => $totalPendapatan,
                'gaji_bersih' => $totalPendapatan,
                'gaji_pokok' => $karyawan['gaji_pokok'] ?? 0,
                'tunjangan_jabatan' => $karyawan['tunjangan_jabatan'] ?? 0,
                'tunjangan_makan' => $karyawan['tunjangan_makan'] ?? 0,
                'tunjangan_transport' => $karyawan['tunjangan_transport'] ?? 0
            ]
        ]);
    }

    /**
     * AJAX: Get perhitungan gaji untuk periode
     */
    public function ajaxGetByPeriode()
    {
        $bulan = $this->request->getGet('bulan');
        $tahun = $this->request->getGet('tahun');
        
        $data = $this->perhitunganModel->select('penggajian_perhitungan.*, karyawan.nama_lengkap')
            ->join('karyawan', 'karyawan.id = penggajian_perhitungan.karyawan_id')
            ->where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->findAll();
        
        return $this->response->setJSON($data);
    }

    /**
     * AJAX: Get ringkasan per periode
     */
    public function ajaxGetRingkasan()
    {
        $bulan = $this->request->getGet('bulan');
        $tahun = $this->request->getGet('tahun');
        
        $ringkasan = $this->perhitunganModel->getRingkasanPeriode($bulan, $tahun);
        
        return $this->response->setJSON($ringkasan);
    }

    /**
     * Get tahun options
     */
    private function getTahunOptions()
    {
        $tahunSekarang = date('Y');
        $options = [];
        
        for ($i = $tahunSekarang - 2; $i <= $tahunSekarang + 1; $i++) {
            $options[] = $i;
        }
        
        return $options;
    }

    /**
     * Get bulan options
     */
    private function getBulanOptions()
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
    }

    /**
     * Get nama bulan
     */
    private function getNamaBulan($bulan)
    {
        $bulanNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return $bulanNames[$bulan] ?? '';
    }
}