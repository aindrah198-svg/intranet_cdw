<?php

namespace App\Controllers\Admin;

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
    
    /**
     * Menampilkan daftar karyawan
     */
    public function index()
    {
        $data = [
            'title' => 'Data Karyawan',
            'karyawan' => $this->karyawanModel->getAllKaryawan(),
            'statistik' => $this->karyawanModel->getStatistik(),
            'active' => 'karyawan',
            'user' => [
                'name' => session()->get('nama') ?? 'Administrator',
                'role' => session()->get('role') ?? 'admin'
            ]
        ];
        
        return view('admin/karyawan/index', $data);
    }
    
    /**
     * Menampilkan form tambah karyawan
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Karyawan Baru',
            'validation' => \Config\Services::validation(),
            'active' => 'karyawan',
            'user' => [
                'name' => session()->get('nama') ?? 'Administrator',
                'role' => session()->get('role') ?? 'admin'
            ]
        ];
        
        return view('admin/karyawan/create', $data);
    }
    
    /**
     * Menyimpan data karyawan baru
     */
    public function store()
    {
        // Validasi input dasar
        $rules = $this->karyawanModel->getValidationRules();
        
        // Validasi NIK harus unik untuk create
        $nik = $this->request->getPost('nik');
        if (!$this->karyawanModel->isNikUnique($nik)) {
            return redirect()->back()->withInput()->with('error', 'NIK sudah terdaftar. Silakan gunakan NIK yang berbeda.');
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Proses upload foto jika ada
        $foto = $this->uploadFoto();
        
        // Proses upload CV jika ada
        $cv = $this->uploadCV();
        
        // Siapkan data untuk disimpan
        $data = $this->request->getPost();
        
        if ($foto) {
            $data['foto'] = $foto;
        }
        
        if ($cv) {
            $data['cv_path'] = $cv;
        }
        
        // Konversi tanggal
        $data['tanggal_lahir'] = !empty($data['tanggal_lahir']) ? date('Y-m-d', strtotime($data['tanggal_lahir'])) : null;
        $data['tanggal_masuk'] = !empty($data['tanggal_masuk']) ? date('Y-m-d', strtotime($data['tanggal_masuk'])) : null;
        $data['tanggal_keluar'] = !empty($data['tanggal_keluar']) ? date('Y-m-d', strtotime($data['tanggal_keluar'])) : null;
        
        // Handle tahun lulus jika kosong
        if (empty($data['tahun_lulus'])) {
            $data['tahun_lulus'] = null;
        }
        
        // Simpan data
        if ($this->karyawanModel->save($data)) {
            return redirect()->to('/admin/karyawan')->with('success', 'Data karyawan berhasil ditambahkan');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan data karyawan');
        }
    }
    
    /**
 * Menampilkan detail karyawan
 */
public function show($id)
{
    $karyawan = $this->karyawanModel->getKaryawanById($id);
    
    if (!$karyawan) {
        return redirect()->to('/admin/karyawan')->with('error', 'Data karyawan tidak ditemukan');
    }
    
    $data = [
        'title' => 'Detail Karyawan',
        'karyawan' => $karyawan,
        'active' => 'karyawan',
        'user' => [
            'name' => session()->get('nama') ?? 'Administrator',
            'role' => session()->get('role') ?? 'admin'
        ]
    ];
    
    // Arahkan ke view detail, bukan index
    return view('admin/karyawan/detail', $data);
}
    
    /**
     * Menampilkan form edit karyawan
     */
    public function edit($id)
    {
        $karyawan = $this->karyawanModel->getKaryawanById($id);
        
        if (!$karyawan) {
            return redirect()->to('/admin/karyawan')->with('error', 'Data karyawan tidak ditemukan');
        }
        
        $data = [
            'title' => 'Edit Data Karyawan',
            'karyawan' => $karyawan,
            'validation' => \Config\Services::validation(),
            'active' => 'karyawan',
            'user' => [
                'name' => session()->get('nama') ?? 'Administrator',
                'role' => session()->get('role') ?? 'admin'
            ]
        ];
        
        return view('admin/karyawan/edit', $data);
    }
    
    /**
     * Mengupdate data karyawan
     */
    public function update($id)
    {
        // Cek apakah karyawan ada
        $karyawan = $this->karyawanModel->getKaryawanById($id);
        if (!$karyawan) {
            return redirect()->to('/admin/karyawan')->with('error', 'Data karyawan tidak ditemukan');
        }
        
        // Validasi input dasar
        $rules = $this->karyawanModel->getValidationRules();
        
        // Validasi NIK untuk update (harus unik kecuali untuk ID ini)
        $inputNIK = $this->request->getPost('nik');
        $currentNIK = $karyawan['nik'];
        
        // Jika NIK berubah, validasi apakah NIK baru sudah digunakan
        if ($inputNIK != $currentNIK) {
            if (!$this->karyawanModel->isNikUniqueForUpdate($inputNIK, $id)) {
                return redirect()->back()->withInput()->with('error', 'NIK sudah terdaftar oleh karyawan lain. Silakan gunakan NIK yang berbeda.');
            }
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Proses upload foto jika ada file baru
        $foto = $this->uploadFoto();
        
        // Proses upload CV jika ada file baru
        $cv = $this->uploadCV();
        
        // Siapkan data untuk diupdate
        $data = $this->request->getPost();
        
        // Handle checkbox untuk hapus foto
        if ($this->request->getPost('hapus_foto') == '1') {
            if ($karyawan['foto']) {
                $this->deleteFile($karyawan['foto']);
            }
            $data['foto'] = null;
        } elseif ($foto) {
            // Jika ada foto baru, hapus foto lama jika ada
            if ($karyawan['foto']) {
                $this->deleteFile($karyawan['foto']);
            }
            $data['foto'] = $foto;
        }
        
        // Handle checkbox untuk hapus CV
        if ($this->request->getPost('hapus_cv') == '1') {
            if ($karyawan['cv_path']) {
                $this->deleteFile($karyawan['cv_path']);
            }
            $data['cv_path'] = null;
        } elseif ($cv) {
            // Jika ada CV baru, hapus CV lama jika ada
            if ($karyawan['cv_path']) {
                $this->deleteFile($karyawan['cv_path']);
            }
            $data['cv_path'] = $cv;
        }
        
        // Konversi tanggal
        $data['tanggal_lahir'] = !empty($data['tanggal_lahir']) ? date('Y-m-d', strtotime($data['tanggal_lahir'])) : null;
        $data['tanggal_masuk'] = !empty($data['tanggal_masuk']) ? date('Y-m-d', strtotime($data['tanggal_masuk'])) : null;
        $data['tanggal_keluar'] = !empty($data['tanggal_keluar']) ? date('Y-m-d', strtotime($data['tanggal_keluar'])) : null;
        
        // Handle tahun lulus jika kosong
        if (empty($data['tahun_lulus'])) {
            $data['tahun_lulus'] = null;
        }
        
        // Update data
        if ($this->karyawanModel->update($id, $data)) {
            return redirect()->to('/admin/karyawan')->with('success', 'Data karyawan berhasil diperbarui');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data karyawan');
        }
    }
    
  // C:\xampp\htdocs\cdwnet\app\Controllers\Admin\Karyawan.php

/**
 * Menghapus karyawan (soft delete)
 */
public function delete($id = null)
{
    // Jika tidak ada ID dari parameter, cek dari POST
    if ($id === null) {
        $id = $this->request->getPost('id');
    }
    
    // Cek apakah karyawan ada
    $karyawan = $this->karyawanModel->getKaryawanById($id);
    if (!$karyawan) {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data karyawan tidak ditemukan']);
        }
        return redirect()->to('/admin/karyawan')->with('error', 'Data karyawan tidak ditemukan');
    }
    
    (new \App\Models\UserModel())->where('karyawan_id', $id)->delete();
    if ($this->karyawanModel->delete($id)) {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'message' => 'Data karyawan berhasil dihapus']);
        }
        return redirect()->to('/admin/karyawan')->with('success', 'Data karyawan berhasil dihapus');
    } else {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus data karyawan']);
        }
        return redirect()->to('/admin/karyawan')->with('error', 'Gagal menghapus data karyawan');
    }
}
    
    /**
     * Menampilkan karyawan aktif
     */
    public function aktif()
    {
        $data = [
            'title' => 'Karyawan Aktif',
            'karyawan' => $this->karyawanModel->getKaryawanAktif(),
            'statistik' => $this->karyawanModel->getStatistik(),
            'active' => 'karyawan',
            'user' => [
                'name' => session()->get('nama') ?? 'Administrator',
                'role' => session()->get('role') ?? 'admin'
            ]
        ];
        
        return view('admin/karyawan/index', $data);
    }
    
    /**
     * Menampilkan karyawan yang sudah keluar
     */
    public function keluar()
    {
        $data = [
            'title' => 'Karyawan yang Sudah Keluar',
            'karyawan' => $this->karyawanModel->getKaryawanKeluar(),
            'statistik' => $this->karyawanModel->getStatistik(),
            'active' => 'karyawan',
            'user' => [
                'name' => session()->get('nama') ?? 'Administrator',
                'role' => session()->get('role') ?? 'admin'
            ]
        ];
        
        return view('admin/karyawan/index', $data);
    }
    
    /**
     * Mencari karyawan
     */
    public function search()
    {
        $keyword = $this->request->getGet('keyword');
        
        $data = [
            'title' => 'Hasil Pencarian: ' . $keyword,
            'karyawan' => $this->karyawanModel->searchKaryawan($keyword),
            'statistik' => $this->karyawanModel->getStatistik(),
            'keyword' => $keyword,
            'active' => 'karyawan',
            'user' => [
                'name' => session()->get('nama') ?? 'Administrator',
                'role' => session()->get('role') ?? 'admin'
            ]
        ];
        
        return view('admin/karyawan/index', $data);
    }
    
    /**
     * Upload foto karyawan
     */
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
    
    /**
     * Upload CV karyawan
     */
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
    
    /**
     * Hapus file
     */
    private function deleteFile($filePath)
    {
        if ($filePath && file_exists(ROOTPATH . 'public/' . $filePath)) {
            unlink(ROOTPATH . 'public/' . $filePath);
        }
    }
    
    /**
     * Update foto karyawan via AJAX
     */
    public function updateFoto($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/admin/karyawan');
        }
        
        $karyawan = $this->karyawanModel->getKaryawanById($id);
        if (!$karyawan) {
            return $this->response->setJSON(['success' => false, 'message' => 'Karyawan tidak ditemukan']);
        }
        
        $foto = $this->uploadFoto();
        if (!$foto) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengupload foto']);
        }
        
        // Hapus foto lama jika ada
        if ($karyawan['foto']) {
            $this->deleteFile($karyawan['foto']);
        }
        
        // Update database
        if ($this->karyawanModel->updateFoto($id, $foto)) {
            return $this->response->setJSON(['success' => true, 'foto' => $foto]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan data']);
        }
    }
    
    /**
     * Update CV karyawan via AJAX
     */
    public function updateCV($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/admin/karyawan');
        }
        
        $karyawan = $this->karyawanModel->getKaryawanById($id);
        if (!$karyawan) {
            return $this->response->setJSON(['success' => false, 'message' => 'Karyawan tidak ditemukan']);
        }
        
        $cv = $this->uploadCV();
        if (!$cv) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengupload CV']);
        }
        
        // Hapus CV lama jika ada
        if ($karyawan['cv_path']) {
            $this->deleteFile($karyawan['cv_path']);
        }
        
        // Update database
        if ($this->karyawanModel->updateCV($id, $cv)) {
            return $this->response->setJSON(['success' => true, 'cv_path' => $cv]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan data']);
        }
    }
    
    /**
     * Update status keluar karyawan
     */
    public function updateKeluar($id)
    {
        $karyawan = $this->karyawanModel->getKaryawanById($id);
        if (!$karyawan) {
            return redirect()->to('/admin/karyawan')->with('error', 'Data karyawan tidak ditemukan');
        }
        
        $tanggalKeluar = $this->request->getPost('tanggal_keluar');
        $alasanKeluar = $this->request->getPost('alasan_keluar');
        
        if (empty($tanggalKeluar)) {
            return redirect()->back()->with('error', 'Tanggal keluar harus diisi');
        }
        
        $tanggalKeluar = date('Y-m-d', strtotime($tanggalKeluar));
        
        if ($this->karyawanModel->updateKeluar($id, $tanggalKeluar, $alasanKeluar)) {
            return redirect()->to('/admin/karyawan')->with('success', 'Status karyawan berhasil diperbarui');
        } else {
            return redirect()->back()->with('error', 'Gagal memperbarui status karyawan');
        }
    }
    
    /**
     * Mendapatkan data karyawan untuk select2
     */
    public function getSelect2()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/admin/karyawan');
        }
        
        $search = $this->request->getGet('search');
        $results = [];
        
        if ($search) {
            $karyawan = $this->karyawanModel->searchKaryawan($search);
        } else {
            $karyawan = $this->karyawanModel->getKaryawanAktif();
        }
        
        foreach ($karyawan as $k) {
            $results[] = [
                'id' => $k['id'],
                'text' => $k['nik'] . ' - ' . $k['nama_lengkap']
            ];
        }
        
        return $this->response->setJSON(['results' => $results]);
    }
    
    /**
     * Export data karyawan ke Excel
     */
    public function export()
    {
        $karyawan = $this->karyawanModel->getAllKaryawan();
        
        $filename = 'data-karyawan-' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Header
        fputcsv($output, [
            'NIK', 'Nama Lengkap', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir',
            'Jabatan', 'Departemen', 'Status Karyawan', 'Tanggal Masuk', 'Email', 'Telepon'
        ]);
        
        // Data
        foreach ($karyawan as $k) {
            fputcsv($output, [
                $k['nik'] ?? '',
                $k['nama_lengkap'] ?? '',
                $k['jenis_kelamin'] ?? '',
                $k['tempat_lahir'] ?? '',
                $k['tanggal_lahir'] ?? '',
                $k['jabatan'] ?? '',
                $k['departemen'] ?? '',
                $k['status_karyawan'] ?? '',
                $k['tanggal_masuk'] ?? '',
                $k['email'] ?? '',
                $k['telepon'] ?? ''
            ]);
        }
        
        fclose($output);
        exit();
    }
}