<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DokumenModel;
use App\Models\KaryawanModel;

class Dokumen extends BaseController
{
    protected $dokumenModel;
    protected $karyawanModel;
    
    // Konstanta untuk path upload
    const UPLOAD_PATH = WRITEPATH . 'uploads/dokumen/';
    const PUBLIC_PATH = 'writable/uploads/dokumen/';
    
    public function __construct()
    {
        $this->dokumenModel = new DokumenModel();
        $this->karyawanModel = new KaryawanModel();
        
        // Pastikan folder upload ada
        $this->ensureUploadDirectory();
    }
    
    /**
     * Pastikan folder upload ada dan aman
     */
    private function ensureUploadDirectory()
    {
        if (!is_dir(self::UPLOAD_PATH)) {
            mkdir(self::UPLOAD_PATH, 0777, true);
        }
        
        // Tambahkan file index.html untuk keamanan
        $indexFile = self::UPLOAD_PATH . 'index.html';
        if (!file_exists($indexFile)) {
            file_put_contents($indexFile, '<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body><h1>Directory access forbidden</h1></body></html>');
        }
    }
    
    /**
     * Menampilkan daftar semua dokumen
     */
    public function index()
    {
        // Get search parameter
        $search = $this->request->getGet('search');
        
        // Pagination
        $perPage = 10;
        $currentPage = $this->request->getGet('page') ?? 1;
        $offset = ($currentPage - 1) * $perPage;
        
        // Get data
        $dokumenData = $this->dokumenModel->getDokumenWithKaryawan($perPage, $offset, $search);
        $total = $this->dokumenModel->getTotalDokumen($search);
        
        $data = [
            'title' => 'Dokumen Karyawan',
            'active' => 'dokumen',
            'is_dokumen_page' => true,
            'dokumen' => $dokumenData,
            'total' => $total,
            'search' => $search,
            'currentPage' => $currentPage,
            'perPage' => $perPage,
            'jenisOptions' => $this->dokumenModel->getJenisOptions(),
            'statusOptions' => $this->dokumenModel->getStatusOptions(),
            'karyawanList' => $this->karyawanModel->findAll()
        ];
        
        return view('admin/dokumen/index', $data);
    }
    
    /**
     * Menampilkan dokumen berdasarkan karyawan
     */
    public function byKaryawan($karyawan_id)
    {
        $karyawan = $this->karyawanModel->find($karyawan_id);
        if (!$karyawan) {
            return redirect()->to(base_url('admin/karyawan/dokumen'))->with('error', 'Karyawan tidak ditemukan.');
        }
        
        $data = [
            'title' => 'Dokumen Karyawan: ' . $karyawan['nama_lengkap'],
            'active' => 'dokumen',
            'is_dokumen_page' => true,
            'karyawan' => $karyawan,
            'dokumen' => $this->dokumenModel->getDokumenByKaryawan($karyawan_id),
            'jenisOptions' => $this->dokumenModel->getJenisOptions(),
            'statusOptions' => $this->dokumenModel->getStatusOptions()
        ];
        
        return view('admin/dokumen/by_karyawan', $data);
    }
    
    /**
     * Menampilkan form tambah dokumen
     */
    public function create()
    {
        $data = [
            'title' => 'Upload Dokumen Baru',
            'active' => 'dokumen',
            'is_dokumen_page' => true,
            'karyawanList' => $this->karyawanModel->findAll(),
            'jenisOptions' => $this->dokumenModel->getJenisOptions(),
            'statusOptions' => $this->dokumenModel->getStatusOptions()
        ];
        
        return view('admin/dokumen/create', $data);
    }
    
    /**
     * Menyimpan dokumen baru
     */
    public function store()
    {
        // Validasi
        $rules = [
            'karyawan_id' => 'required|integer',
            'jenis' => 'required|in_list[KTP,KK,IJAZAH,CV,NPWP,BPJS_KES,BPJS_TK,SIM,SERTIFIKAT,SKCK,PAS_FOTO,SURAT_LAMARAN,REFERENSI,KONTRAK_KERJA,BUKU_REKENING,VAKSIN,FOTO_PRIBADI,LAINNYA]',
            'file_dokumen' => 'uploaded[file_dokumen]|max_size[file_dokumen,5120]|ext_in[file_dokumen,pdf,jpg,jpeg,png,doc,docx]',
            'nomor_dokumen' => 'permit_empty|max_length[100]',
            'tanggal_berlaku' => 'permit_empty|valid_date',
            'tanggal_kadaluarsa' => 'permit_empty|valid_date',
            'keterangan' => 'permit_empty',
            'status' => 'permit_empty|in_list[pending,diterima,ditolak]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Cek apakah sudah ada dokumen dengan jenis yang sama untuk karyawan ini
        $existing = $this->dokumenModel->where([
            'karyawan_id' => $this->request->getPost('karyawan_id'),
            'jenis' => $this->request->getPost('jenis')
        ])->first();
        
        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Dokumen dengan jenis ini sudah ada untuk karyawan tersebut.');
        }
        
        // Upload file
        $file = $this->request->getFile('file_dokumen');
        
        if (!$file->isValid()) {
            return redirect()->back()->withInput()->with('error', 'File tidak valid: ' . $file->getErrorString());
        }
        
        // Generate nama file unik
        $newName = $file->getRandomName();
        
        // Pindahkan file
        if (!$file->move(self::UPLOAD_PATH, $newName)) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengupload file. Periksa izin folder upload.');
        }
        
        // Simpan data ke database
        $data = [
            'karyawan_id' => $this->request->getPost('karyawan_id'),
            'jenis' => $this->request->getPost('jenis'),
            'nama_file' => $file->getClientName(),
            'path' => self::PUBLIC_PATH . $newName,
            'ukuran' => $file->getSize(),
            'nomor_dokumen' => $this->request->getPost('nomor_dokumen'),
            'tanggal_berlaku' => $this->request->getPost('tanggal_berlaku'),
            'tanggal_kadaluarsa' => $this->request->getPost('tanggal_kadaluarsa'),
            'keterangan' => $this->request->getPost('keterangan'),
            'status' => $this->request->getPost('status') ?? 'pending',
            'diupload_oleh' => session()->get('id') ?? 1
        ];
        
        try {
            $saved = $this->dokumenModel->save($data);
            
            if ($saved) {
                return redirect()->to(base_url('admin/karyawan/dokumen'))->with('success', 'Dokumen berhasil diupload.');
            } else {
                // Hapus file jika gagal save
                @unlink(self::UPLOAD_PATH . $newName);
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data dokumen ke database.');
            }
        } catch (\Exception $e) {
            // Hapus file jika error
            @unlink(self::UPLOAD_PATH . $newName);
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
      /**
     * Menampilkan detail dokumen
     */
    public function show($id)
    {
        $dokumen = $this->dokumenModel->getDokumenDetail($id);
        
        if (!$dokumen) {
            return redirect()->to(base_url('admin/karyawan/dokumen'))->with('error', 'Dokumen tidak ditemukan.');
        }
        
        // Pastikan keterangan adalah string
        if (isset($dokumen['keterangan']) && is_array($dokumen['keterangan'])) {
            $dokumen['keterangan'] = implode(', ', array_filter($dokumen['keterangan']));
        }
        
        $data = [
            'title' => 'Detail Dokumen',
            'active' => 'dokumen',
            'is_dokumen_page' => true,
            'dokumen' => $dokumen,
            'jenisOptions' => $this->dokumenModel->getJenisOptions(),
            'statusOptions' => $this->dokumenModel->getStatusOptions()
        ];
        
        return view('admin/dokumen/show', $data);
    }
    
    /**
     * Menampilkan form edit dokumen
     */
    public function edit($id)
    {
        $dokumen = $this->dokumenModel->getDokumenDetail($id);
        
        if (!$dokumen) {
            return redirect()->to(base_url('admin/karyawan/dokumen'))->with('error', 'Dokumen tidak ditemukan.');
        }
        
        $data = [
            'title' => 'Edit Dokumen',
            'active' => 'dokumen',
            'is_dokumen_page' => true,
            'dokumen' => $dokumen,
            'karyawanList' => $this->karyawanModel->findAll(),
            'jenisOptions' => $this->dokumenModel->getJenisOptions(),
            'statusOptions' => $this->dokumenModel->getStatusOptions()
        ];
        
        return view('admin/dokumen/edit', $data);
    }
    
    public function update($id)
    {
        $dokumen = $this->dokumenModel->find($id);
        if (!$dokumen) {
            return redirect()->to(base_url('admin/karyawan/dokumen'))->with('error', 'Dokumen tidak ditemukan.');
        }
        
        // Data dasar untuk update
        $data = [
            'nomor_dokumen' => $this->request->getPost('nomor_dokumen'),
            'tanggal_berlaku' => $this->request->getPost('tanggal_berlaku') ?: null,
            'tanggal_kadaluarsa' => $this->request->getPost('tanggal_kadaluarsa') ?: null,
            'keterangan' => $this->request->getPost('keterangan'),
            'status' => $this->request->getPost('status'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // Handle file upload jika ada file baru
        $file = $this->request->getFile('file_dokumen');
        
        // Cek apakah file valid dan ada file yang diupload
        if ($file && $file->isValid() && !$file->hasMoved()) {
            
            // Validasi ukuran file (5MB)
            if ($file->getSize() > 5 * 1024 * 1024) {
                return redirect()->back()->withInput()->with('error', 'Ukuran file terlalu besar. Maksimal 5MB.');
            }
            
            // Validasi extension
            $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
            $extension = strtolower($file->getExtension());
            
            if (!in_array($extension, $allowed)) {
                return redirect()->back()->withInput()->with('error', 'Format file tidak didukung. Gunakan: PDF, JPG, PNG, DOC, atau DOCX.');
            }
            
            // Generate nama file baru
            $newName = $file->getRandomName();
            
            // Pastikan folder upload ada
            $uploadPath = WRITEPATH . 'uploads/dokumen/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            
            // Pindahkan file baru
            if ($file->move($uploadPath, $newName)) {
                // Update data file di array
                $data['nama_file'] = $file->getClientName();
                $data['path'] = 'writable/uploads/dokumen/' . $newName;
                $data['ukuran'] = $file->getSize();
                
                // Hapus file lama setelah file baru berhasil diupload
                $oldFilePath = $this->getFullPath($dokumen['path']);
                if (file_exists($oldFilePath) && is_file($oldFilePath)) {
                    @unlink($oldFilePath);
                }
            } else {
                return redirect()->back()->withInput()->with('error', 'Gagal mengupload file. Error: ' . $file->getErrorString());
            }
        }
        
        // Update database
        try {
            $updated = $this->dokumenModel->update($id, $data);
            
            if ($updated) {
                $message = 'Dokumen berhasil diperbarui.';
                if (isset($data['nama_file'])) {
                    $message .= ' File baru telah diupload.';
                }
                return redirect()->to(base_url('admin/karyawan/dokumen'))->with('success', $message);
            } else {
                // Jika gagal update dan ada file baru, hapus file baru
                if (isset($data['path']) && isset($newName)) {
                    @unlink($uploadPath . $newName);
                }
                return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data ke database.');
            }
        } catch (\Exception $e) {
            // Jika error dan ada file baru, hapus file baru
            if (isset($data['path']) && isset($newName)) {
                @unlink($uploadPath . $newName);
            }
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

  /**
     * Helper untuk mendapatkan full path dari path database
     */
    private function getFullPath($dbPath)
    {
        // Jika path sudah full, langsung return
        if (strpos($dbPath, WRITEPATH) === 0) {
            return $dbPath;
        }
        
        // Jika path relatif dari writable
        if (strpos($dbPath, 'writable/') === 0) {
            return str_replace('writable/', WRITEPATH, $dbPath);
        }
        
        // Default: tambahkan ke upload path
        return self::UPLOAD_PATH . basename($dbPath);
    }
    
    /**
     * Hapus dokumen
     */
    public function delete($id)
    {
        $dokumen = $this->dokumenModel->find($id);
        
        if (!$dokumen) {
            return redirect()->to(base_url('admin/karyawan/dokumen'))->with('error', 'Dokumen tidak ditemukan.');
        }
        
        try {
            // Hapus file dari server
            $filePath = self::UPLOAD_PATH . basename($dokumen['path']);
            if (file_exists($filePath) && is_file($filePath)) {
                @unlink($filePath);
            }
            
            // Hapus dari database
            $deleted = $this->dokumenModel->delete($id);
            
            if ($deleted) {
                return redirect()->to(base_url('admin/karyawan/dokumen'))->with('success', 'Dokumen berhasil dihapus.');
            } else {
                return redirect()->to(base_url('admin/karyawan/dokumen'))->with('error', 'Gagal menghapus dokumen dari database.');
            }
        } catch (\Exception $e) {
            return redirect()->to(base_url('admin/karyawan/dokumen'))->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
  /**
     * Download dokumen
     */
    public function download($id)
    {
        $dokumen = $this->dokumenModel->find($id);
        
        if (!$dokumen) {
            return redirect()->to(base_url('admin/karyawan/dokumen'))->with('error', 'Dokumen tidak ditemukan.');
        }
        
        // Dapatkan full path file
        $filePath = $this->getFullPath($dokumen['path']);
        
        if (!file_exists($filePath)) {
            // Coba cari file dengan path alternatif
            $alternatePath = WRITEPATH . 'uploads/dokumen/' . basename($dokumen['path']);
            
            if (file_exists($alternatePath)) {
                $filePath = $alternatePath;
            } else {
                return redirect()->to(base_url('admin/karyawan/dokumen'))->with('error', 'File tidak ditemukan: ' . basename($dokumen['path']));
            }
        }
        
        // Return response download
        return $this->response->download($filePath, null)->setFileName($dokumen['nama_file']);
    }
    
     /**
     * Preview dokumen
     */
    public function preview($id)
    {
        $dokumen = $this->dokumenModel->find($id);
        
        if (!$dokumen) {
            return redirect()->to(base_url('admin/karyawan/dokumen'))->with('error', 'Dokumen tidak ditemukan.');
        }
        
        // Dapatkan full path file
        $filePath = $this->getFullPath($dokumen['path']);
        
        if (!file_exists($filePath)) {
            // Coba cari file dengan path alternatif
            $alternatePath = WRITEPATH . 'uploads/dokumen/' . basename($dokumen['path']);
            
            if (file_exists($alternatePath)) {
                $filePath = $alternatePath;
            } else {
                return redirect()->to(base_url('admin/karyawan/dokumen'))->with('error', 'File tidak ditemukan: ' . basename($dokumen['path']));
            }
        }
        
        // Dapatkan tipe mime file
        $mimeType = mime_content_type($filePath);
        
        // Set header berdasarkan tipe file
        header('Content-Type: ' . $mimeType);
        
        // Untuk gambar dan PDF, tampilkan inline
        if (strpos($mimeType, 'image') !== false || strpos($mimeType, 'pdf') !== false) {
            header('Content-Disposition: inline; filename="' . basename($dokumen['nama_file']) . '"');
        } else {
            // Untuk file lain, force download
            header('Content-Disposition: attachment; filename="' . basename($dokumen['nama_file']) . '"');
        }
        
        // Set cache control
        header('Cache-Control: public, max-age=86400');
        
        // Output file
        readfile($filePath);
        exit();
    }
    
     /**
     * Update status dokumen (untuk tombol setujui/tolak)
     * BISA menerima request biasa DAN AJAX
     */
    public function updateStatus($id)
    {
        // Validasi status
        $status = $this->request->getPost('status');
        
        if (!$status || !in_array($status, ['pending', 'diterima', 'ditolak'])) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Status tidak valid'
                ]);
            }
            return redirect()->back()->with('error', 'Status tidak valid.');
        }
        
        $dokumen = $this->dokumenModel->find($id);
        
        if (!$dokumen) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Dokumen tidak ditemukan'
                ]);
            }
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');
        }
        
        // Update status
        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        try {
            $updated = $this->dokumenModel->update($id, $data);
            
            if ($updated) {
                $statusLabel = $status == 'diterima' ? 'DITERIMA' : ($status == 'ditolak' ? 'DITOLAK' : 'PENDING');
                
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Status berhasil diperbarui menjadi ' . $statusLabel,
                        'new_status' => $status,
                        'status_label' => $statusLabel,
                        'badge_class' => $status == 'pending' ? 'bg-warning' : 
                                        ($status == 'diterima' ? 'bg-success' : 'bg-danger')
                    ]);
                }
                return redirect()->back()->with('success', 'Status dokumen berhasil diperbarui menjadi ' . $statusLabel . '.');
            } else {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Gagal memperbarui status'
                    ]);
                }
                return redirect()->back()->with('error', 'Gagal memperbarui status dokumen.');
            }
        } catch (\Exception $e) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
/**
 * Debug method untuk cek file upload
 */
public function debugUpload($id = null)
{
    if ($id) {
        // Debug dokumen spesifik
        $dokumen = $this->dokumenModel->find($id);
        
        if ($dokumen) {
            echo "<h2>Debug Dokumen #$id</h2>";
            echo "<pre>";
            print_r($dokumen);
            echo "</pre>";
            
            $filePath = $this->getFullPath($dokumen['path']);
            echo "<p>File path: $filePath</p>";
            echo "<p>File exists: " . (file_exists($filePath) ? 'YES' : 'NO') . "</p>";
            
            if (file_exists($filePath)) {
                echo "<p>File size: " . filesize($filePath) . " bytes</p>";
                echo "<p>MIME type: " . mime_content_type($filePath) . "</p>";
                
                // Tampilkan gambar jika image
                $mime = mime_content_type($filePath);
                if (strpos($mime, 'image') !== false) {
                    echo "<h3>Preview Gambar:</h3>";
                    echo "<img src='data:$mime;base64," . base64_encode(file_get_contents($filePath)) . "' style='max-width: 300px;'>";
                }
            }
        } else {
            echo "Dokumen tidak ditemukan!";
        }
    } else {
        // Debug semua dokumen
        $allDokumen = $this->dokumenModel->findAll();
        
        echo "<h2>Debug Semua Dokumen</h2>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Nama File</th><th>Path DB</th><th>File Exists</th><th>Size</th></tr>";
        
        foreach ($allDokumen as $d) {
            $filePath = $this->getFullPath($d['path']);
            $exists = file_exists($filePath) ? 'YES' : 'NO';
            $size = $exists ? filesize($filePath) : 'N/A';
            
            echo "<tr>";
            echo "<td>{$d['id']}</td>";
            echo "<td>{$d['nama_file']}</td>";
            echo "<td>{$d['path']}</td>";
            echo "<td>$exists</td>";
            echo "<td>$size</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    }
    
    echo "<hr>";
    echo "<h3>Upload Directory Info:</h3>";
    echo "<p>Upload Path: " . self::UPLOAD_PATH . "</p>";
    echo "<p>Exists: " . (is_dir(self::UPLOAD_PATH) ? 'YES' : 'NO') . "</p>";
    echo "<p>Writable: " . (is_writable(self::UPLOAD_PATH) ? 'YES' : 'NO') . "</p>";
    
    if (is_dir(self::UPLOAD_PATH)) {
        $files = scandir(self::UPLOAD_PATH);
        echo "<p>Files in upload directory: " . implode(', ', array_filter($files, function($f) {
            return !in_array($f, ['.', '..', 'index.html', '.htaccess']);
        })) . "</p>";
    }
    
    die();
}

/**
 * Debug method untuk testing
 */
public function debug($id)
{
    $dokumen = $this->dokumenModel->find($id);
    
    $data = [
        'title' => 'Debug Dokumen',
        'active' => 'dokumen',
        'is_dokumen_page' => true,
        'dokumen' => $dokumen
    ];
    
    return view('admin/dokumen/debug', $data);
}

/**
 * Test update method yang lebih sederhana
 */
public function updateTest($id)
{
    // Debug: lihat apa yang dikirim
    echo "<pre>";
    echo "POST Data:\n";
    print_r($_POST);
    echo "\n\nFILES Data:\n";
    print_r($_FILES);
    echo "</pre>";
    
    $dokumen = $this->dokumenModel->find($id);
    if (!$dokumen) {
        echo "Dokumen tidak ditemukan!";
        die();
    }
    
    // Data untuk update
    $data = [
        'status' => $this->request->getPost('status'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    // Handle file upload jika ada
    $file = $this->request->getFile('file_dokumen');
    
    if ($file && $file->getName() != '' && $file->isValid()) {
        echo "<p>File ditemukan: " . $file->getName() . "</p>";
        echo "<p>File size: " . $file->getSize() . " bytes</p>";
        echo "<p>File valid: " . ($file->isValid() ? 'YES' : 'NO') . "</p>";
        
        // Generate nama baru
        $newName = $file->getRandomName();
        $uploadPath = WRITEPATH . 'uploads/dokumen/';
        
        echo "<p>Upload path: $uploadPath</p>";
        echo "<p>New filename: $newName</p>";
        
        // Coba pindahkan file
        if ($file->move($uploadPath, $newName)) {
            echo "<p style='color:green;'>✓ File berhasil diupload!</p>";
            
            // Update data file
            $data['nama_file'] = $file->getClientName();
            $data['path'] = 'writable/uploads/dokumen/' . $newName;
            $data['ukuran'] = $file->getSize();
            
            echo "<p>Data file akan diupdate:</p>";
            echo "<pre>";
            print_r([
                'nama_file' => $data['nama_file'],
                'path' => $data['path'],
                'ukuran' => $data['ukuran']
            ]);
            echo "</pre>";
            
            // Hapus file lama
            $oldFile = WRITEPATH . 'uploads/dokumen/' . basename($dokumen['path']);
            echo "<p>Old file path: $oldFile</p>";
            echo "<p>Old file exists: " . (file_exists($oldFile) ? 'YES' : 'NO') . "</p>";
            
            if (file_exists($oldFile)) {
                if (unlink($oldFile)) {
                    echo "<p style='color:green;'>✓ File lama berhasil dihapus!</p>";
                } else {
                    echo "<p style='color:orange;'>⚠ Gagal menghapus file lama</p>";
                }
            }
        } else {
            echo "<p style='color:red;'>✗ Gagal mengupload file!</p>";
            echo "<p>Error: " . $file->getErrorString() . "</p>";
        }
    } else {
        echo "<p>Tidak ada file baru diupload</p>";
    }
    
    echo "<hr><h3>Data yang akan diupdate ke database:</h3>";
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    
    // Coba update database
    try {
        $updated = $this->dokumenModel->update($id, $data);
        
        if ($updated) {
            echo "<h3 style='color:green;'>✓ Database berhasil diupdate!</h3>";
            
            // Cek data setelah update
            $updatedData = $this->dokumenModel->find($id);
            echo "<h4>Data setelah update:</h4>";
            echo "<pre>";
            print_r($updatedData);
            echo "</pre>";
        } else {
            echo "<h3 style='color:red;'>✗ Gagal update database!</h3>";
        }
    } catch (\Exception $e) {
        echo "<h3 style='color:red;'>✗ Error: " . $e->getMessage() . "</h3>";
    }
    
    echo "<hr>";
    echo "<a href='" . base_url('admin/karyawan/dokumen/debug/' . $id) . "' class='btn btn-primary'>Kembali ke Debug</a>";
    echo " <a href='" . base_url('admin/karyawan/dokumen') . "' class='btn btn-secondary'>Kembali ke List</a>";
    
    die();
}

    
    /**
     * AJAX update status (alternatif)
     */
    public function updateStatusAjax($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }
        
        $status = $this->request->getPost('status');
        
        if (!$status || !in_array($status, ['pending', 'diterima', 'ditolak'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Status tidak valid'
            ]);
        }
        
        $dokumen = $this->dokumenModel->find($id);
        
        if (!$dokumen) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dokumen tidak ditemukan'
            ]);
        }
        
        try {
            $data = [
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $updated = $this->dokumenModel->update($id, $data);
            
            if ($updated) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Status berhasil diperbarui',
                    'new_status' => $status,
                    'status_label' => $this->dokumenModel->getStatusOptions()[$status] ?? $status,
                    'badge_class' => $status == 'pending' ? 'bg-warning' : 
                                    ($status == 'diterima' ? 'bg-success' : 'bg-danger')
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memperbarui status'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Export dokumen (Excel/PDF)
     */
    public function export($type = 'excel')
    {
        // Get semua dokumen dengan data karyawan
        $dokumen = $this->dokumenModel->getDokumenWithKaryawan(1000, 0); // Limit besar untuk export
        
        if ($type == 'excel') {
            return $this->exportExcel($dokumen);
        } elseif ($type == 'pdf') {
            return $this->exportPDF($dokumen);
        }
        
        return redirect()->to(base_url('admin/karyawan/dokumen'))->with('error', 'Format export tidak didukung.');
    }
    
    /**
     * Export ke Excel
     */
    private function exportExcel($data)
    {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="dokumen_karyawan_' . date('Ymd_His') . '.xls"');
        header('Cache-Control: max-age=0');
        
        echo "<table border='1'>";
        echo "<tr>";
        echo "<th>No</th>";
        echo "<th>NIK</th>";
        echo "<th>Nama Karyawan</th>";
        echo "<th>Jenis Dokumen</th>";
        echo "<th>Nomor Dokumen</th>";
        echo "<th>Status</th>";
        echo "<th>Tanggal Upload</th>";
        echo "<th>Kadaluarsa</th>";
        echo "</tr>";
        
        $no = 1;
        foreach ($data as $item) {
            echo "<tr>";
            echo "<td>" . $no++ . "</td>";
            echo "<td>" . $item['nik'] . "</td>";
            echo "<td>" . $item['nama_lengkap'] . "</td>";
            echo "<td>" . $item['jenis'] . "</td>";
            echo "<td>" . ($item['nomor_dokumen'] ?? '-') . "</td>";
            echo "<td>" . $item['status'] . "</td>";
            echo "<td>" . date('d/m/Y', strtotime($item['created_at'])) . "</td>";
            echo "<td>" . (!empty($item['tanggal_kadaluarsa']) ? date('d/m/Y', strtotime($item['tanggal_kadaluarsa'])) : '-') . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        exit();
    }
    
    /**
     * Export ke PDF (sederhana)
     */
    private function exportPDF($data)
    {
        $html = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid #000; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                .header { text-align: center; margin-bottom: 20px; }
                .footer { margin-top: 30px; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h2>Laporan Dokumen Karyawan</h2>
                <p>Tanggal: " . date('d/m/Y H:i') . "</p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIK</th>
                        <th>Nama Karyawan</th>
                        <th>Jenis Dokumen</th>
                        <th>Nomor Dokumen</th>
                        <th>Status</th>
                        <th>Tanggal Upload</th>
                    </tr>
                </thead>
                <tbody>";
        
        $no = 1;
        foreach ($data as $item) {
            $html .= "
                    <tr>
                        <td>" . $no++ . "</td>
                        <td>" . $item['nik'] . "</td>
                        <td>" . $item['nama_lengkap'] . "</td>
                        <td>" . $item['jenis'] . "</td>
                        <td>" . ($item['nomor_dokumen'] ?? '-') . "</td>
                        <td>" . $item['status'] . "</td>
                        <td>" . date('d/m/Y', strtotime($item['created_at'])) . "</td>
                    </tr>";
        }
        
        $html .= "
                </tbody>
            </table>
            
            <div class='footer'>
                <p>Dicetak oleh: " . (session()->get('nama') ?? 'Admin') . "</p>
                <p>Total Dokumen: " . count($data) . "</p>
            </div>
        </body>
        </html>";
        
        // Untuk sementara output HTML, bisa diganti dengan library PDF seperti Dompdf
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment;filename="dokumen_karyawan_' . date('Ymd_His') . '.pdf"');
        
        // Jika ingin PDF asli, install dan gunakan Dompdf
        echo $html;
        exit();
    }
    
    /**
     * Get jenis dokumen yang tersedia untuk karyawan tertentu
     */
    public function getAvailableJenis($karyawan_id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }
        
        // Get semua jenis dokumen yang sudah diupload oleh karyawan
        $uploadedJenis = $this->dokumenModel
            ->where('karyawan_id', $karyawan_id)
            ->select('jenis')
            ->findAll();
        
        $uploaded = array_column($uploadedJenis, 'jenis');
        
        // Get semua jenis dokumen yang tersedia
        $allJenis = $this->dokumenModel->getJenisOptions();
        
        // Filter jenis yang belum diupload
        $availableJenis = array_diff_key($allJenis, array_flip($uploaded));
        
        return $this->response->setJSON([
            'success' => true,
            'available_jenis' => $availableJenis
        ]);
    }
    
    /**
     * Bulk update status dokumen
     */
    public function bulkUpdateStatus()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }
        
        $ids = $this->request->getPost('ids');
        $status = $this->request->getPost('status');
        
        if (empty($ids) || !is_array($ids)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada dokumen yang dipilih'
            ]);
        }
        
        if (!in_array($status, ['pending', 'diterima', 'ditolak'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Status tidak valid'
            ]);
        }
        
        try {
            // Update semua dokumen terpilih
            $updated = $this->dokumenModel
                ->whereIn('id', $ids)
                ->set(['status' => $status])
                ->update();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Berhasil memperbarui ' . $updated . ' dokumen'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}