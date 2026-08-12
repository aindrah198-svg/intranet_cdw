<?php
// C:\xampp\htdocs\cdwnet\app\Controllers\Teknisi\TeknisiController.php

namespace App\Controllers\Teknisi;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class TeknisiController extends BaseController
{
    // Properti dasar
    protected $session;
    protected $userRole;
    protected $userId;
    protected $userData;
    protected $karyawanData;
    
    /**
     * Initialize the controller - INI YANG BENAR UNTUK CI4
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // HARUS memanggil parent initController
        parent::initController($request, $response, $logger);
        
        $this->session = \Config\Services::session();
        $this->userRole = $this->session->get('role');
        $this->userId = $this->session->get('user_id');
        
        // Debug info
        log_message('debug', 'TEKNISI CONTROLLER: Initializing...');
        log_message('debug', 'TEKNISI CONTROLLER: User ID = ' . $this->userId);
        log_message('debug', 'TEKNISI CONTROLLER: User Role = ' . $this->userRole);
        log_message('debug', 'TEKNISI CONTROLLER: Is Logged In = ' . ($this->session->get('isLoggedIn') ? 'Yes' : 'No'));
        
        // Cek apakah user sudah login
        if (!$this->session->get('isLoggedIn')) {
            log_message('debug', 'TEKNISI CONTROLLER: User not logged in. Redirecting to login.');
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Silakan login terlebih dahulu.');
        }
        
        // Cek apakah user adalah teknisi (case-insensitive)
        $roleLower = strtolower($this->userRole ?? '');
        
        if ($roleLower !== 'teknisi') {
            log_message('debug', 'TEKNISI CONTROLLER: User is not TEKNISI. Redirecting...');
            // Redirect ke dashboard sesuai role
            return $this->redirectToDashboard($this->userRole);
        }
        
        // Load user data
        $this->userData = [
            'user_id' => $this->userId,
            'name' => $this->session->get('name'),
            'username' => $this->session->get('username'),
            'email' => $this->session->get('email'),
            'role' => $this->userRole,
            'karyawan_id' => $this->session->get('karyawan_id')
        ];
        
        log_message('debug', 'TEKNISI CONTROLLER: User Data = ' . print_r($this->userData, true));
        
        // Load karyawan data
        $userModel = new \App\Models\UserModel();
        $this->karyawanData = $userModel->getKaryawanByUserId($this->userId);
        
        if ($this->karyawanData) {
            log_message('debug', 'TEKNISI CONTROLLER: Karyawan data found. Karyawan ID = ' . $this->karyawanData['id']);
        } else {
            log_message('debug', 'TEKNISI CONTROLLER: No karyawan data found for user ID = ' . $this->userId);
        }
        
        $this->provisionTables();
        log_message('debug', 'TEKNISI CONTROLLER: Initialization complete.');
    }
    
    protected function provisionTables()
    {
        $db = \Config\Database::connect();

        if (!$db->tableExists('peralatan_dipinjam')) {
            $db->query("
                CREATE TABLE IF NOT EXISTS `peralatan_dipinjam` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `kode_peminjaman` VARCHAR(50) NOT NULL,
                  `teknisi_id` INT NOT NULL,
                  `nama_teknisi` VARCHAR(150) DEFAULT NULL,
                  `nama_alat` VARCHAR(150) NOT NULL,
                  `kode_alat` VARCHAR(50) DEFAULT NULL,
                  `qty` INT DEFAULT 1,
                  `tgl_pinjam` DATE NOT NULL,
                  `tgl_kembali_rencana` DATE NOT NULL,
                  `tgl_kembali_realisasi` DATE DEFAULT NULL,
                  `kondisi_pinjam` ENUM('Baik','Rusak Ringan') DEFAULT 'Baik',
                  `kondisi_kembali` ENUM('Baik','Rusak Ringan','Rusak Berat','Hilang') DEFAULT NULL,
                  `status` ENUM('Dipinjam','Dikembalikan','Terlambat') DEFAULT 'Dipinjam',
                  `proyek_id` INT DEFAULT NULL,
                  `nama_proyek` VARCHAR(150) DEFAULT NULL,
                  `catatan` TEXT DEFAULT NULL,
                  `created_at` DATETIME DEFAULT NULL,
                  `updated_at` DATETIME DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }

        if (!$db->tableExists('perawatan_alat')) {
            $db->query("
                CREATE TABLE IF NOT EXISTS `perawatan_alat` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `kode_perawatan` VARCHAR(50) NOT NULL,
                  `nama_alat` VARCHAR(150) NOT NULL,
                  `kode_alat` VARCHAR(50) DEFAULT NULL,
                  `jenis_perawatan` ENUM('Rutin','Perbaikan','Kalibrasi') DEFAULT 'Rutin',
                  `tgl_perawatan` DATE NOT NULL,
                  `biaya` DECIMAL(15,2) DEFAULT 0.00,
                  `penanggung_jawab` VARCHAR(150) DEFAULT NULL,
                  `status` ENUM('Dijadwalkan','Proses','Selesai') DEFAULT 'Selesai',
                  `catatan` TEXT DEFAULT NULL,
                  `created_at` DATETIME DEFAULT NULL,
                  `updated_at` DATETIME DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }

        if (!$db->tableExists('biaya_lapangan')) {
            $db->query("
                CREATE TABLE IF NOT EXISTS `biaya_lapangan` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `kode_pengajuan` VARCHAR(50) NOT NULL,
                  `teknisi_id` INT NOT NULL,
                  `proyek_id` INT DEFAULT NULL,
                  `nama_proyek` VARCHAR(150) DEFAULT NULL,
                  `tgl_pengajuan` DATE NOT NULL,
                  `kategori_biaya` ENUM('Transport','BBM','Makan','Parkir/Tol','Akomodasi','Material Darurat','Lainnya') DEFAULT 'Transport',
                  `nominal` DECIMAL(15,2) NOT NULL,
                  `bukti_foto` VARCHAR(255) DEFAULT NULL,
                  `keterangan` TEXT NOT NULL,
                  `status` ENUM('Pending','Disetujui','Ditolak','Dicairkan') DEFAULT 'Pending',
                  `disetujui_oleh` INT DEFAULT NULL,
                  `catatan_approval` TEXT DEFAULT NULL,
                  `created_at` DATETIME DEFAULT NULL,
                  `updated_at` DATETIME DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }

        if (!$db->tableExists('keluhan_karyawan')) {
            $db->query("
                CREATE TABLE IF NOT EXISTS `keluhan_karyawan` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `kode_keluhan` VARCHAR(50) NOT NULL,
                  `karyawan_id` INT NOT NULL,
                  `nama_karyawan` VARCHAR(150) DEFAULT NULL,
                  `kategori` ENUM('Fasilitas/Alat Kerja','Kendala Lapangan','K3/Keselamatan','Administrasi','Lainnya') DEFAULT 'Kendala Lapangan',
                  `judul` VARCHAR(255) NOT NULL,
                  `deskripsi` TEXT NOT NULL,
                  `tgl_keluhan` DATE NOT NULL,
                  `lampiran_foto` VARCHAR(255) DEFAULT NULL,
                  `status` ENUM('Baru','Diproses','Selesai') DEFAULT 'Baru',
                  `tanggapan_manajemen` TEXT DEFAULT NULL,
                  `created_at` DATETIME DEFAULT NULL,
                  `updated_at` DATETIME DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }

        if (!$db->tableExists('spk_instalasi')) {
            $db->query("
                CREATE TABLE IF NOT EXISTS `spk_instalasi` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `nomor_spk` VARCHAR(100) NOT NULL,
                  `judul_pekerjaan` VARCHAR(255) NOT NULL,
                  `deskripsi` TEXT DEFAULT NULL,
                  `lokasi` VARCHAR(255) DEFAULT NULL,
                  `client_id` INT DEFAULT NULL,
                  `client_nama` VARCHAR(255) DEFAULT NULL,
                  `client_alamat` TEXT DEFAULT NULL,
                  `client_kontak` VARCHAR(100) DEFAULT NULL,
                  `catatan_client` TEXT DEFAULT NULL,
                  `tanggal_mulai` DATE DEFAULT NULL,
                  `tanggal_selesai` DATE DEFAULT NULL,
                  `target_selesai` DATE DEFAULT NULL,
                  `tanggal_selesai_aktual` DATE DEFAULT NULL,
                  `prioritas` ENUM('Rendah','Normal','Tinggi','Mendesak') DEFAULT 'Normal',
                  `status` ENUM('Draft','Dijadwalkan','Dalam Pengerjaan','Selesai','Ditunda','Dibatalkan') DEFAULT 'Dijadwalkan',
                  `kategori_pekerjaan` VARCHAR(100) DEFAULT NULL,
                  `tim_teknisi` TEXT DEFAULT NULL,
                  `project_manager_id` INT DEFAULT NULL,
                  `catatan` TEXT DEFAULT NULL,
                  `laporan` TEXT DEFAULT NULL,
                  `estimasi_biaya` DECIMAL(15,2) DEFAULT 0.00,
                  `biaya_aktual` DECIMAL(15,2) DEFAULT 0.00,
                  `progress_persen` INT DEFAULT 0,
                  `dokumen_pendukung` VARCHAR(255) DEFAULT NULL,
                  `dokumentasi` TEXT DEFAULT NULL,
                  `foto_sebelum` VARCHAR(255) DEFAULT NULL,
                  `foto_sesudah` VARCHAR(255) DEFAULT NULL,
                  `laporan_hasil` TEXT DEFAULT NULL,
                  `dibuat_oleh` INT DEFAULT NULL,
                  `dibuat_tanggal` DATETIME DEFAULT NULL,
                  `diperbarui_oleh` INT DEFAULT NULL,
                  `diperbarui_tanggal` DATETIME DEFAULT NULL,
                  `created_at` DATETIME DEFAULT NULL,
                  `updated_at` DATETIME DEFAULT NULL,
                  `deleted_at` DATETIME DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        } else {
            if (!$db->fieldExists('dibuat_oleh', 'spk_instalasi')) {
                $db->query("ALTER TABLE `spk_instalasi` ADD COLUMN `dibuat_oleh` INT DEFAULT NULL");
            }
            if (!$db->fieldExists('diperbarui_oleh', 'spk_instalasi')) {
                $db->query("ALTER TABLE `spk_instalasi` ADD COLUMN `diperbarui_oleh` INT DEFAULT NULL");
            }
            if (!$db->fieldExists('client_nama', 'spk_instalasi')) {
                $db->query("ALTER TABLE `spk_instalasi` ADD COLUMN `client_nama` VARCHAR(255) DEFAULT NULL");
            }
            if (!$db->fieldExists('estimasi_biaya', 'spk_instalasi')) {
                $db->query("ALTER TABLE `spk_instalasi` ADD COLUMN `estimasi_biaya` DECIMAL(15,2) DEFAULT 0.00");
            }
            if (!$db->fieldExists('biaya_aktual', 'spk_instalasi')) {
                $db->query("ALTER TABLE `spk_instalasi` ADD COLUMN `biaya_aktual` DECIMAL(15,2) DEFAULT 0.00");
            }
            if (!$db->fieldExists('progress_persen', 'spk_instalasi')) {
                $db->query("ALTER TABLE `spk_instalasi` ADD COLUMN `progress_persen` INT DEFAULT 0");
            }
            if (!$db->fieldExists('target_selesai', 'spk_instalasi')) {
                $db->query("ALTER TABLE `spk_instalasi` ADD COLUMN `target_selesai` DATE DEFAULT NULL");
            }
            if (!$db->fieldExists('prioritas', 'spk_instalasi')) {
                $db->query("ALTER TABLE `spk_instalasi` ADD COLUMN `prioritas` ENUM('Rendah','Normal','Tinggi','Mendesak') DEFAULT 'Normal'");
            }
            if (!$db->fieldExists('status', 'spk_instalasi')) {
                $db->query("ALTER TABLE `spk_instalasi` ADD COLUMN `status` ENUM('Draft','Dijadwalkan','Dalam Pengerjaan','Selesai','Ditunda','Dibatalkan') DEFAULT 'Dijadwalkan'");
            }
            if (!$db->fieldExists('kategori_pekerjaan', 'spk_instalasi')) {
                $db->query("ALTER TABLE `spk_instalasi` ADD COLUMN `kategori_pekerjaan` VARCHAR(100) DEFAULT NULL");
            }
            if (!$db->fieldExists('tim_teknisi', 'spk_instalasi')) {
                $db->query("ALTER TABLE `spk_instalasi` ADD COLUMN `tim_teknisi` TEXT DEFAULT NULL");
            }
            if (!$db->fieldExists('project_manager_id', 'spk_instalasi')) {
                $db->query("ALTER TABLE `spk_instalasi` ADD COLUMN `project_manager_id` INT DEFAULT NULL");
            }
            if (!$db->fieldExists('catatan', 'spk_instalasi')) {
                $db->query("ALTER TABLE `spk_instalasi` ADD COLUMN `catatan` TEXT DEFAULT NULL");
            }
            if (!$db->fieldExists('laporan', 'spk_instalasi')) {
                $db->query("ALTER TABLE `spk_instalasi` ADD COLUMN `laporan` TEXT DEFAULT NULL");
            }
        }

        if (!$db->tableExists('spk_instalasi_pengeluaran')) {
            $db->query("
                CREATE TABLE IF NOT EXISTS `spk_instalasi_pengeluaran` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `spk_id` INT NOT NULL,
                  `jenis` VARCHAR(100) NOT NULL,
                  `deskripsi` TEXT DEFAULT NULL,
                  `nominal` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                  `jumlah` INT DEFAULT 1,
                  `harga_satuan` DECIMAL(15,2) DEFAULT 0.00,
                  `tanggal` DATE NOT NULL,
                  `bukti_foto` VARCHAR(255) DEFAULT NULL,
                  `created_by` INT DEFAULT NULL,
                  `created_at` DATETIME DEFAULT NULL,
                  `updated_at` DATETIME DEFAULT NULL,
                  `deleted_at` DATETIME DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }

        if (!$db->tableExists('spk_instalasi_item')) {
            $db->query("
                CREATE TABLE IF NOT EXISTS `spk_instalasi_item` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `spk_id` INT NOT NULL,
                  `nama_item` VARCHAR(255) NOT NULL,
                  `spesifikasi` TEXT DEFAULT NULL,
                  `qty` INT DEFAULT 1,
                  `satuan` VARCHAR(50) DEFAULT 'Pcs',
                  `status` VARCHAR(50) DEFAULT 'Pending',
                  `created_at` DATETIME DEFAULT NULL,
                  `updated_at` DATETIME DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }
    }
    
    /**
     * Redirect ke dashboard sesuai role
     */
    private function redirectToDashboard($role)
    {
        $roleLower = strtolower($role ?? '');
        
        log_message('debug', 'TEKNISI CONTROLLER redirectToDashboard: Role = ' . $role . ' (Lowercase: ' . $roleLower . ')');
        
        switch ($roleLower) {
            case 'hrd':
                log_message('debug', 'TEKNISI CONTROLLER: Redirecting to HRD dashboard');
                return redirect()->to(base_url('hrd'))->with('info', 'Anda dialihkan ke dashboard HRD.');
            case 'admin':
                log_message('debug', 'TEKNISI CONTROLLER: Redirecting to ADMIN dashboard');
                return redirect()->to(base_url('admin'))->with('info', 'Anda dialihkan ke dashboard Admin.');
            case 'direktur':
                log_message('debug', 'TEKNISI CONTROLLER: Redirecting to DIREKTUR dashboard');
                return redirect()->to(base_url('direktur'))->with('info', 'Anda dialihkan ke dashboard Direktur.');
            case 'accounting':
                log_message('debug', 'TEKNISI CONTROLLER: Redirecting to ACCOUNTING dashboard');
                return redirect()->to(base_url('accounting'))->with('info', 'Anda dialihkan ke dashboard Accounting.');
            case 'sales':
            case 'marketing':
                log_message('debug', 'TEKNISI CONTROLLER: Redirecting to SALES dashboard');
                return redirect()->to(base_url('sales'))->with('info', 'Anda dialihkan ke dashboard Sales.');
            case 'staff':
                log_message('debug', 'TEKNISI CONTROLLER: Redirecting to STAFF dashboard');
                return redirect()->to(base_url('staff'))->with('info', 'Anda dialihkan ke dashboard Staff.');
            default:
                log_message('debug', 'TEKNISI CONTROLLER: Role not recognized: ' . $role);
                return redirect()->to(base_url('login'))->with('error', 'Role tidak dikenali. Silakan login kembali.');
        }
    }
    
    /**
     * Render view dengan template teknisi
     */
    protected function renderView($view, $data = [])
    {
        // Set default data
        $defaultData = [
            'user' => $this->userData,
            'karyawan' => $this->karyawanData,
            'active' => 'dashboard',
            'title' => 'Dashboard Teknisi',
            'subtitle' => date('l, d F Y')
        ];
        
        // Merge dengan data yang dikirim
        $data = array_merge($defaultData, $data);
        
        // Debug info
        log_message('debug', 'TEKNISI CONTROLLER renderView: Rendering view = ' . $view);
        log_message('debug', 'TEKNISI CONTROLLER renderView: Active menu = ' . $data['active']);
        log_message('debug', 'TEKNISI CONTROLLER renderView: User name = ' . ($data['user']['name'] ?? 'Unknown'));
        
        // Cek apakah view file ada
        $viewPath = APPPATH . 'Views/' . $view . '.php';
        if (!file_exists($viewPath)) {
            log_message('error', 'TEKNISI CONTROLLER: View file not found: ' . $viewPath);
            
            // Fallback: tampilkan halaman sederhana
            return view('errors/html/error_404', [
                'message' => 'View file not found: ' . $view,
                'code' => 404
            ]);
        }
        
        // Render view dengan template teknisi
        echo view('teknisi/templates/header', $data);
        echo view('teknisi/templates/sidebar', $data);
        echo view('teknisi/templates/navbar', $data);
        echo view($view, $data);
        echo view('teknisi/templates/footer', $data);
    }
    
    /**
     * Helper method untuk mendapatkan data karyawan
     */
    protected function getKaryawanData()
    {
        return $this->karyawanData;
    }
    
    /**
     * Helper method untuk mendapatkan data user
     */
    protected function getUserData()
    {
        return $this->userData;
    }
    
    /**
     * Helper method untuk cek apakah user sudah absen hari ini
     */
    protected function hasCheckedInToday()
    {
        if (!$this->karyawanData) {
            return false;
        }
        
        $absensiModel = new \App\Models\AbsensiModel();
        $today = date('Y-m-d');
        
        $absensiToday = $absensiModel->where('karyawan_id', $this->karyawanData['id'])
                                    ->where('DATE(tanggal)', $today)
                                    ->first();
        
        return !empty($absensiToday);
    }
    
    /**
     * Helper method untuk mendapatkan absensi hari ini
     */
    protected function getTodayAttendance()
    {
        if (!$this->karyawanData) {
            return null;
        }
        
        $absensiModel = new \App\Models\AbsensiModel();
        $today = date('Y-m-d');
        
        return $absensiModel->where('karyawan_id', $this->karyawanData['id'])
                           ->where('DATE(tanggal)', $today)
                           ->first();
    }
    
    /**
     * Helper method untuk validasi akses
     */
    protected function validateAccess($requiredRole = 'teknisi')
    {
        $userRole = strtolower($this->userRole ?? '');
        $requiredRole = strtolower($requiredRole);
        
        if ($userRole !== $requiredRole) {
            log_message('debug', 'TEKNISI CONTROLLER validateAccess: Access denied. User role = ' . $userRole . ', Required role = ' . $requiredRole);
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Akses ditolak!');
        }
        
        return true;
    }
}