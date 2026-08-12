<?php

namespace App\Controllers\Hrd;

use App\Controllers\BaseController;
use App\Models\KaryawanModel;

class Karyawan extends BaseController
{
    protected $karyawanModel;
    protected $helpers = ['form', 'url'];
    
    public function __construct()
    {
        $this->karyawanModel = new KaryawanModel();
    }
    
    public function index()
    {
        $data = [
            'title' => 'Data Karyawan',
            'karyawan' => $this->karyawanModel->getAllKaryawan(),
            'statistik' => $this->karyawanModel->getStatistik(),
            'active' => 'karyawan',
            'user' => [
                'name' => session()->get('nama') ?? 'HRD',
                'role' => session()->get('role') ?? 'hrd'
            ]
        ];
        
        return view('hrd/karyawan/index', $data);
    }
    
    public function create()
    {
        $data = [
            'title' => 'Tambah Karyawan Baru',
            'validation' => \Config\Services::validation(),
            'active' => 'karyawan',
            'user' => [
                'name' => session()->get('nama') ?? 'HRD',
                'role' => session()->get('role') ?? 'hrd'
            ]
        ];
        
        return view('hrd/karyawan/create', $data);
    }
    
    public function store()
    {
        $rules = $this->karyawanModel->getValidationRules();
        $nik = $this->request->getPost('nik');
        if (!$this->karyawanModel->isNikUnique($nik)) {
            return redirect()->back()->withInput()->with('error', 'NIK sudah terdaftar. Silakan gunakan NIK yang berbeda.');
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $foto = $this->uploadFoto();
        $cv = $this->uploadCV();
        
        $data = $this->request->getPost();
        if ($foto) $data['foto'] = $foto;
        if ($cv) $data['cv_path'] = $cv;
        
        $data['tanggal_lahir'] = !empty($data['tanggal_lahir']) ? date('Y-m-d', strtotime($data['tanggal_lahir'])) : null;
        $data['tanggal_masuk'] = !empty($data['tanggal_masuk']) ? date('Y-m-d', strtotime($data['tanggal_masuk'])) : null;
        $data['tanggal_keluar'] = !empty($data['tanggal_keluar']) ? date('Y-m-d', strtotime($data['tanggal_keluar'])) : null;
        if (empty($data['tahun_lulus'])) $data['tahun_lulus'] = null;
        
        if ($this->karyawanModel->save($data)) {
            return redirect()->to('/hrd/karyawan')->with('success', 'Data karyawan berhasil ditambahkan');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan data karyawan');
        }
    }
    
    public function show($id)
    {
        $karyawan = $this->karyawanModel->getKaryawanById($id);
        if (!$karyawan) {
            return redirect()->to('/hrd/karyawan')->with('error', 'Data karyawan tidak ditemukan');
        }
        
        $data = [
            'title' => 'Detail Karyawan',
            'karyawan' => $karyawan,
            'active' => 'karyawan',
            'user' => [
                'name' => session()->get('nama') ?? 'HRD',
                'role' => session()->get('role') ?? 'hrd'
            ]
        ];
        
        return view('hrd/karyawan/detail', $data);
    }
    
    public function edit($id)
    {
        $karyawan = $this->karyawanModel->getKaryawanById($id);
        if (!$karyawan) {
            return redirect()->to('/hrd/karyawan')->with('error', 'Data karyawan tidak ditemukan');
        }
        
        $data = [
            'title' => 'Edit Data Karyawan',
            'karyawan' => $karyawan,
            'validation' => \Config\Services::validation(),
            'active' => 'karyawan',
            'user' => [
                'name' => session()->get('nama') ?? 'HRD',
                'role' => session()->get('role') ?? 'hrd'
            ]
        ];
        
        return view('hrd/karyawan/edit', $data);
    }
    
    public function update($id)
    {
        $karyawan = $this->karyawanModel->getKaryawanById($id);
        if (!$karyawan) {
            return redirect()->to('/hrd/karyawan')->with('error', 'Data karyawan tidak ditemukan');
        }
        
        $rules = $this->karyawanModel->getValidationRules();
        $inputNIK = $this->request->getPost('nik');
        $currentNIK = $karyawan['nik'];
        
        if ($inputNIK != $currentNIK) {
            if (!$this->karyawanModel->isNikUniqueForUpdate($inputNIK, $id)) {
                return redirect()->back()->withInput()->with('error', 'NIK sudah terdaftar oleh karyawan lain.');
            }
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $foto = $this->uploadFoto();
        $cv = $this->uploadCV();
        
        $data = $this->request->getPost();
        if ($this->request->getPost('hapus_foto') == '1') {
            if ($karyawan['foto']) $this->deleteFile($karyawan['foto']);
            $data['foto'] = null;
        } elseif ($foto) {
            if ($karyawan['foto']) $this->deleteFile($karyawan['foto']);
            $data['foto'] = $foto;
        }
        
        if ($this->request->getPost('hapus_cv') == '1') {
            if ($karyawan['cv_path']) $this->deleteFile($karyawan['cv_path']);
            $data['cv_path'] = null;
        } elseif ($cv) {
            if ($karyawan['cv_path']) $this->deleteFile($karyawan['cv_path']);
            $data['cv_path'] = $cv;
        }
        
        $data['tanggal_lahir'] = !empty($data['tanggal_lahir']) ? date('Y-m-d', strtotime($data['tanggal_lahir'])) : null;
        $data['tanggal_masuk'] = !empty($data['tanggal_masuk']) ? date('Y-m-d', strtotime($data['tanggal_masuk'])) : null;
        $data['tanggal_keluar'] = !empty($data['tanggal_keluar']) ? date('Y-m-d', strtotime($data['tanggal_keluar'])) : null;
        if (empty($data['tahun_lulus'])) $data['tahun_lulus'] = null;
        
        if ($this->karyawanModel->update($id, $data)) {
            return redirect()->to('/hrd/karyawan')->with('success', 'Data karyawan berhasil diperbarui');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data karyawan');
        }
    }
    
    public function delete($id = null)
    {
        if ($id === null) $id = $this->request->getPost('id');
        $karyawan = $this->karyawanModel->getKaryawanById($id);
        if (!$karyawan) {
            if ($this->request->isAJAX()) return $this->response->setJSON(['success' => false, 'message' => 'Data karyawan tidak ditemukan']);
            return redirect()->to('/hrd/karyawan')->with('error', 'Data karyawan tidak ditemukan');
        }
        
        (new \App\Models\UserModel())->where('karyawan_id', $id)->delete();
        if ($this->karyawanModel->delete($id)) {
            if ($this->request->isAJAX()) return $this->response->setJSON(['success' => true, 'message' => 'Data karyawan berhasil dihapus']);
            return redirect()->to('/hrd/karyawan')->with('success', 'Data karyawan berhasil dihapus');
        } else {
            if ($this->request->isAJAX()) return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus data karyawan']);
            return redirect()->to('/hrd/karyawan')->with('error', 'Gagal menghapus data karyawan');
        }
    }
    
    public function aktif()
    {
        $data = [
            'title' => 'Karyawan Aktif',
            'karyawan' => $this->karyawanModel->getKaryawanAktif(),
            'statistik' => $this->karyawanModel->getStatistik(),
            'active' => 'karyawan',
            'user' => ['name' => session()->get('nama') ?? 'HRD', 'role' => session()->get('role') ?? 'hrd']
        ];
        return view('hrd/karyawan/index', $data);
    }
    
    public function keluar()
    {
        $data = [
            'title' => 'Karyawan yang Sudah Keluar',
            'karyawan' => $this->karyawanModel->getKaryawanKeluar(),
            'statistik' => $this->karyawanModel->getStatistik(),
            'active' => 'karyawan',
            'user' => ['name' => session()->get('nama') ?? 'HRD', 'role' => session()->get('role') ?? 'hrd']
        ];
        return view('hrd/karyawan/index', $data);
    }
    
    public function search()
    {
        $keyword = $this->request->getGet('keyword');
        $data = [
            'title' => 'Hasil Pencarian: ' . $keyword,
            'karyawan' => $this->karyawanModel->searchKaryawan($keyword),
            'statistik' => $this->karyawanModel->getStatistik(),
            'keyword' => $keyword,
            'active' => 'karyawan',
            'user' => ['name' => session()->get('nama') ?? 'HRD', 'role' => session()->get('role') ?? 'hrd']
        ];
        return view('hrd/karyawan/index', $data);
    }
    
    private function uploadFoto()
    {
        $file = $this->request->getFile('foto');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads/foto', $newName);
            return 'uploads/foto/' . $newName;
        }
        return null;
    }
    
    private function uploadCV()
    {
        $file = $this->request->getFile('cv');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads/cv', $newName);
            return 'uploads/cv/' . $newName;
        }
        return null;
    }
    
    private function deleteFile($filePath)
    {
        if ($filePath && file_exists(ROOTPATH . 'public/' . $filePath)) {
            unlink(ROOTPATH . 'public/' . $filePath);
        }
    }
    
    public function updateFoto($id)
    {
        if (!$this->request->isAJAX()) return redirect()->to('/hrd/karyawan');
        $karyawan = $this->karyawanModel->getKaryawanById($id);
        if (!$karyawan) return $this->response->setJSON(['success' => false, 'message' => 'Karyawan tidak ditemukan']);
        
        $foto = $this->uploadFoto();
        if (!$foto) return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengupload foto']);
        if ($karyawan['foto']) $this->deleteFile($karyawan['foto']);
        
        if ($this->karyawanModel->updateFoto($id, $foto)) {
            return $this->response->setJSON(['success' => true, 'foto' => $foto]);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan data']);
    }
    
    public function updateCV($id)
    {
        if (!$this->request->isAJAX()) return redirect()->to('/hrd/karyawan');
        $karyawan = $this->karyawanModel->getKaryawanById($id);
        if (!$karyawan) return $this->response->setJSON(['success' => false, 'message' => 'Karyawan tidak ditemukan']);
        
        $cv = $this->uploadCV();
        if (!$cv) return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengupload CV']);
        if ($karyawan['cv_path']) $this->deleteFile($karyawan['cv_path']);
        
        if ($this->karyawanModel->updateCV($id, $cv)) {
            return $this->response->setJSON(['success' => true, 'cv_path' => $cv]);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan data']);
    }
    
    public function updateKeluar($id)
    {
        $karyawan = $this->karyawanModel->getKaryawanById($id);
        if (!$karyawan) return redirect()->to('/hrd/karyawan')->with('error', 'Data karyawan tidak ditemukan');
        
        $tanggalKeluar = $this->request->getPost('tanggal_keluar');
        $alasanKeluar = $this->request->getPost('alasan_keluar');
        if (empty($tanggalKeluar)) return redirect()->back()->with('error', 'Tanggal keluar harus diisi');
        
        $tanggalKeluar = date('Y-m-d', strtotime($tanggalKeluar));
        if ($this->karyawanModel->updateKeluar($id, $tanggalKeluar, $alasanKeluar)) {
            return redirect()->to('/hrd/karyawan')->with('success', 'Status karyawan berhasil diperbarui');
        }
        return redirect()->back()->with('error', 'Gagal memperbarui status karyawan');
    }
    
    public function getSelect2()
    {
        if (!$this->request->isAJAX()) return redirect()->to('/hrd/karyawan');
        $search = $this->request->getGet('search');
        $results = [];
        $karyawan = $search ? $this->karyawanModel->searchKaryawan($search) : $this->karyawanModel->getKaryawanAktif();
        foreach ($karyawan as $k) {
            $results[] = ['id' => $k['id'], 'text' => $k['nik'] . ' - ' . $k['nama_lengkap']];
        }
        return $this->response->setJSON(['results' => $results]);
    }
    
    public function export()
    {
        $karyawan = $this->karyawanModel->getAllKaryawan();
        $filename = 'data-karyawan-' . date('Y-m-d') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['NIK', 'Nama Lengkap', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir', 'Jabatan', 'Departemen', 'Status Karyawan', 'Tanggal Masuk', 'Email', 'Telepon']);
        foreach ($karyawan as $k) {
            fputcsv($output, [$k['nik'] ?? '', $k['nama_lengkap'] ?? '', $k['jenis_kelamin'] ?? '', $k['tempat_lahir'] ?? '', $k['tanggal_lahir'] ?? '', $k['jabatan'] ?? '', $k['departemen'] ?? '', $k['status_karyawan'] ?? '', $k['tanggal_masuk'] ?? '', $k['email'] ?? '', $k['telepon'] ?? '']);
        }
        fclose($output);
        exit();
    }
}
