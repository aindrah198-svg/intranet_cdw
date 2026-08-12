<?php
namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\Accounting\PenggajianKomponenModel;
use App\Models\Accounting\PenggajianPerhitunganModel;
use App\Models\Accounting\PenggajianProsesPembayaranModel;
use App\Models\Accounting\PenggajianDetailPembayaranModel;
use App\Models\KaryawanModel;
use App\Models\AbsensiModel;
use App\Models\CoaModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Dompdf\Dompdf;
use Dompdf\Options;

class Penggajian extends BaseController
{
    protected $komponenModel;
    protected $perhitunganModel;
    protected $prosesPembayaranModel;
    protected $detailPembayaranModel;
    protected $karyawanModel;
    protected $absensiModel;
    protected $coaModel;
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        if (!$this->db->tableExists('penggajian_perhitungan')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `penggajian_perhitungan` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `nomor_perhitungan` VARCHAR(50) DEFAULT NULL,
                  `karyawan_id` INT NOT NULL,
                  `periode_bulan` INT NOT NULL,
                  `periode_tahun` INT NOT NULL,
                  `tanggal_perhitungan` DATE DEFAULT NULL,
                  `gaji_pokok` DECIMAL(15,2) DEFAULT 0.00,
                  `tunjangan_jabatan` DECIMAL(15,2) DEFAULT 0.00,
                  `tunjangan_bpjs` DECIMAL(15,2) DEFAULT 0.00,
                  `tunjangan_makan` DECIMAL(15,2) DEFAULT 0.00,
                  `tunjangan_transport` DECIMAL(15,2) DEFAULT 0.00,
                  `tunjangan_lainnya` DECIMAL(15,2) DEFAULT 0.00,
                  `total_pendapatan` DECIMAL(15,2) DEFAULT 0.00,
                  `potongan_bpjs_kes` DECIMAL(15,2) DEFAULT 0.00,
                  `potongan_bpjs_tk` DECIMAL(15,2) DEFAULT 0.00,
                  `potongan_pph21` DECIMAL(15,2) DEFAULT 0.00,
                  `potongan_absensi` DECIMAL(15,2) DEFAULT 0.00,
                  `potongan_kasbon` DECIMAL(15,2) DEFAULT 0.00,
                  `potongan_lainnya` DECIMAL(15,2) DEFAULT 0.00,
                  `total_potongan` DECIMAL(15,2) DEFAULT 0.00,
                  `total_hari_kerja` INT DEFAULT 0,
                  `total_hadir` INT DEFAULT 0,
                  `total_izin` INT DEFAULT 0,
                  `total_sakit` INT DEFAULT 0,
                  `total_cuti` INT DEFAULT 0,
                  `total_alpha` INT DEFAULT 0,
                  `total_terlambat` INT DEFAULT 0,
                  `jam_lembur` DECIMAL(8,2) DEFAULT 0.00,
                  `upah_lembur` DECIMAL(15,2) DEFAULT 0.00,
                  `gaji_bersih` DECIMAL(15,2) DEFAULT 0.00,
                  `status` ENUM('Draft','Dihitung','Disetujui','Ditolak') DEFAULT 'Draft',
                  `catatan` TEXT DEFAULT NULL,
                  `disetujui_oleh` INT DEFAULT NULL,
                  `disetujui_at` DATETIME DEFAULT NULL,
                  `created_by` INT DEFAULT NULL,
                  `updated_by` INT DEFAULT NULL,
                  `created_at` DATETIME DEFAULT NULL,
                  `updated_at` DATETIME DEFAULT NULL,
                  `deleted_at` DATETIME DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }
        if (!$this->db->tableExists('penggajian_proses_pembayaran')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `penggajian_proses_pembayaran` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `nomor_pembayaran` VARCHAR(50) DEFAULT NULL,
                  `periode_bulan` INT NOT NULL,
                  `periode_tahun` INT NOT NULL,
                  `tanggal_pembayaran` DATE DEFAULT NULL,
                  `tanggal_proses` DATE DEFAULT NULL,
                  `coa_bank_id` INT DEFAULT NULL,
                  `total_karyawan` INT DEFAULT 0,
                  `total_nominal` DECIMAL(15,2) DEFAULT 0.00,
                  `status` ENUM('Draft','Diproses','Selesai','Dibatalkan') DEFAULT 'Draft',
                  `catatan` TEXT DEFAULT NULL,
                  `jurnal_id` INT DEFAULT NULL,
                  `created_by` INT DEFAULT NULL,
                  `updated_by` INT DEFAULT NULL,
                  `created_at` DATETIME DEFAULT NULL,
                  `updated_at` DATETIME DEFAULT NULL,
                  `deleted_at` DATETIME DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }
        if ($this->db->tableExists('penggajian_proses_pembayaran') && !$this->db->fieldExists('tanggal_proses', 'penggajian_proses_pembayaran')) {
            $this->db->query("ALTER TABLE `penggajian_proses_pembayaran` ADD COLUMN `tanggal_proses` DATE DEFAULT NULL AFTER `tanggal_pembayaran`");
        }
        if (!$this->db->tableExists('penggajian_komponen')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `penggajian_komponen` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `kode_komponen` VARCHAR(50) NOT NULL,
                  `nama_komponen` VARCHAR(100) NOT NULL,
                  `tipe` ENUM('Pendapatan','Potongan') NOT NULL,
                  `nominal_default` DECIMAL(15,2) DEFAULT 0.00,
                  `is_aktif` TINYINT(1) DEFAULT 1,
                  `created_at` DATETIME DEFAULT NULL,
                  `updated_at` DATETIME DEFAULT NULL,
                  `deleted_at` DATETIME DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }
        if (!$this->db->tableExists('penggajian_detail_pembayaran')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `penggajian_detail_pembayaran` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `pembayaran_id` INT NOT NULL,
                  `perhitungan_id` INT NOT NULL,
                  `karyawan_id` INT NOT NULL,
                  `gaji_bersih` DECIMAL(15,2) DEFAULT 0.00,
                  `status` VARCHAR(50) DEFAULT 'Paid',
                  `created_at` DATETIME DEFAULT NULL,
                  `updated_at` DATETIME DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }

        $this->komponenModel = new PenggajianKomponenModel();
        $this->perhitunganModel = new PenggajianPerhitunganModel();
        $this->prosesPembayaranModel = new PenggajianProsesPembayaranModel();
        $this->detailPembayaranModel = new PenggajianDetailPembayaranModel();
        $this->karyawanModel = new KaryawanModel();
        $this->absensiModel = new AbsensiModel();
        $this->coaModel = new CoaModel();
        
        helper(['form', 'url', 'text', 'number']);
        
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Dashboard Penggajian
     */
    public function index()
    {
        $data['title'] = 'Dashboard Penggajian';
        
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? date('m');
        
        $data['tahun'] = $tahun;
        $data['bulan'] = $bulan;
        $data['tahunOptions'] = $this->getTahunOptions();
        $data['bulanOptions'] = $this->getBulanOptions();
        
        // Statistik karyawan
        $data['totalKaryawan'] = $this->karyawanModel->where('status_karyawan', 'Tetap')
            ->orWhere('status_karyawan', 'Kontrak')
            ->countAllResults();
        
        $data['totalKaryawanAktif'] = $this->karyawanModel->where('status_karyawan', 'Tetap')
            ->countAllResults();
        
        // Statistik komponen gaji
        $statsKomponen = $this->komponenModel->getStats();
        $data['totalKomponenPendapatan'] = $statsKomponen['total_pendapatan'] ?? 0;
        $data['totalKomponenPotongan'] = $statsKomponen['total_potongan'] ?? 0;
        
        // Statistik perhitungan gaji periode ini
        $data['perhitunganPeriode'] = $this->perhitunganModel->getRingkasanPeriode($bulan, $tahun);
        
        // Statistik pembayaran periode ini
        $data['pembayaranPeriode'] = $this->prosesPembayaranModel->getCompletedByPeriode($bulan, $tahun);
        
        // Total penggajian per bulan (chart)
        $data['ringkasanPerBulan'] = $this->prosesPembayaranModel->getRingkasanPerPeriode($tahun);
        
        // Statistik komponen
        $data['statsKomponen'] = $statsKomponen;
        
        // Perhitungan terbaru
        $data['perhitunganTerbaru'] = $this->perhitunganModel->orderBy('created_at', 'DESC')->limit(5)->findAll();
        
        // Proses pembayaran terbaru
        $data['prosesTerbaru'] = $this->prosesPembayaranModel->orderBy('created_at', 'DESC')->limit(5)->findAll();
        
        return view('accounting/penggajian/dashboard', $data);
    }

    /**
     * Filter dashboard
     */
    public function filter()
    {
        $tahun = $this->request->getGet('tahun');
        $bulan = $this->request->getGet('bulan');
        
        return redirect()->to('accounting/penggajian?tahun=' . $tahun . '&bulan=' . $bulan);
    }

    /**
     * Menu Data Karyawan (redirect ke halaman data karyawan payroll)
     */
    public function dataKaryawan()
    {
        $data['title'] = 'Data Karyawan - Penggajian';
        
        $filters = [
            'search' => $this->request->getGet('search'),
            'status_karyawan' => $this->request->getGet('status_karyawan'),
            'departemen' => $this->request->getGet('departemen')
        ];
        
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 20;
        
        $builder = $this->karyawanModel->select('karyawan.*, users.email as user_email')
            ->join('users', 'users.karyawan_id = karyawan.id', 'left')
            ->where('karyawan.deleted_at IS NULL');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('karyawan.nik', $search)
                ->orLike('karyawan.nama_lengkap', $search)
                ->orLike('karyawan.jabatan', $search)
                ->groupEnd();
        }
        
        if (!empty($filters['status_karyawan'])) {
            $builder->where('karyawan.status_karyawan', $filters['status_karyawan']);
        }
        
        if (!empty($filters['departemen'])) {
            $builder->where('karyawan.departemen', $filters['departemen']);
        }
        
        $total = $builder->countAllResults(false);
        $offset = ($page - 1) * $perPage;
        $karyawan = $builder->limit($perPage, $offset)->orderBy('karyawan.nama_lengkap', 'ASC')->findAll();
        
        $data['karyawan'] = $karyawan;
        $data['pager'] = $this->karyawanModel->pager;
        $data['total'] = $total;
        $data['perPage'] = $perPage;
        $data['currentPage'] = $page;
        $data['totalPages'] = ceil($total / $perPage);
        
        $data['statusOptions'] = ['Tetap', 'Kontrak', 'Probation', 'Magang'];
        $data['departemenOptions'] = $this->getDepartemenOptions();
        
        return view('accounting/penggajian/data-karyawan', $data);
    }

    /**
     * Detail karyawan untuk payroll
     */
    public function detailKaryawan($id)
    {
        $data['title'] = 'Detail Karyawan - Penggajian';
        
        $karyawan = $this->karyawanModel->select('karyawan.*, users.email as user_email')
            ->join('users', 'users.karyawan_id = karyawan.id', 'left')
            ->where('karyawan.id', $id)
            ->first();
        
        if (!$karyawan) {
            return redirect()->to('accounting/penggajian/data-karyawan')
                ->with('error', 'Karyawan tidak ditemukan');
        }
        
        // Ambil riwayat perhitungan gaji
        $riwayatGaji = $this->perhitunganModel->where('karyawan_id', $id)
            ->orderBy('periode_tahun', 'DESC')
            ->orderBy('periode_bulan', 'DESC')
            ->limit(12)
            ->findAll();
        
        $data['karyawan'] = $karyawan;
        $data['riwayat_gaji'] = $riwayatGaji;
        
        return view('accounting/penggajian/detail-karyawan', $data);
    }

    /**
     * Menu Perhitungan Gaji
     */
    public function perhitunganGaji()
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
        $data['karyawanOptions'] = $this->karyawanModel->select('id, nik, nama_lengkap')->orderBy('nama_lengkap', 'ASC')->findAll();
        
        // Ambil data perhitungan
        $perhitungan = $this->perhitunganModel->getByPeriode($bulan, $tahun, $status);
        if ($karyawanId) {
            $perhitungan = array_filter($perhitungan, function($item) use ($karyawanId) {
                return $item['karyawan_id'] == $karyawanId;
            });
        }
        
        // Tambah nama karyawan
        foreach ($perhitungan as &$item) {
            $karyawan = $this->karyawanModel->find($item['karyawan_id']);
            $item['nama_karyawan'] = $karyawan['nama_lengkap'] ?? '-';
            $item['nik'] = $karyawan['nik'] ?? '-';
        }
        
        $data['perhitungan'] = $perhitungan;
        
        // Ringkasan periode
        $data['ringkasan'] = $this->perhitunganModel->getRingkasanPeriode($bulan, $tahun);
        
        // Status perhitungan
        $data['statusCount'] = [
            'total' => $this->perhitunganModel->where('periode_bulan', $bulan)->where('periode_tahun', $tahun)->countAllResults(),
            'draft' => $this->perhitunganModel->where('periode_bulan', $bulan)->where('periode_tahun', $tahun)->where('status', 'Draft')->countAllResults(),
            'dihitung' => $this->perhitunganModel->where('periode_bulan', $bulan)->where('periode_tahun', $tahun)->where('status', 'Dihitung')->countAllResults(),
            'disetujui' => $this->perhitunganModel->where('periode_bulan', $bulan)->where('periode_tahun', $tahun)->where('status', 'Disetujui')->countAllResults(),
            'ditolak' => $this->perhitunganModel->where('periode_bulan', $bulan)->where('periode_tahun', $tahun)->where('status', 'Ditolak')->countAllResults()
        ];
        
        $data['active'] = 'perhitungan-gaji';
        return view('accounting/templates/header', $data)
             . view('accounting/templates/sidebar', $data)
             . view('accounting/penggajian/perhitungan-gaji/index', $data)
             . view('accounting/templates/footer', $data);
    }

    /**
     * Menu Proses Pembayaran
     */
    public function prosesPembayaran()
    {
        $data['title'] = 'Proses Pembayaran Gaji';
        
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $status = $this->request->getGet('status');
        
        $data['tahun'] = $tahun;
        $data['bulan'] = $bulan;
        $data['status'] = $status;
        $data['tahunOptions'] = $this->getTahunOptions();
        $data['bulanOptions'] = $this->getBulanOptions();
        $data['statusOptions'] = ['Draft', 'Diproses', 'Selesai', 'Dibatalkan'];
        
        // Ambil data proses pembayaran
        $proses = $this->prosesPembayaranModel->getByPeriode($bulan, $tahun);
        
        // Filter by status
        if ($status) {
            $proses = array_filter($proses, function($item) use ($status) {
                return $item['status'] === $status;
            });
        }
        
        $data['proses'] = $proses;
        
        // Ringkasan
        $data['ringkasan'] = $this->prosesPembayaranModel->getRingkasanPerPeriode($tahun);
        
        // Perhitungan yang sudah disetujui untuk periode ini
        $data['perhitunganTersedia'] = $this->perhitunganModel->getForPayment($bulan, $tahun);
        
        // Total gaji yang siap dibayar
        $data['totalSiapBayar'] = array_sum(array_column($data['perhitunganTersedia'], 'gaji_bersih'));
        
        // COA Bank options
        $data['coaBankOptions'] = $this->prosesPembayaranModel->getCoaBankOptions();
        
        $data['active'] = 'proses-pembayaran';
        return view('accounting/templates/header', $data)
             . view('accounting/templates/sidebar', $data)
             . view('accounting/penggajian/proses-pembayaran/index', $data)
             . view('accounting/templates/footer', $data);
    }

    /**
     * Menu Slip Gaji & Laporan
     */
    public function slipGajiLaporan()
    {
        $data['title'] = 'Slip Gaji & Laporan';
        
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $karyawanId = $this->request->getGet('karyawan_id');
        
        $data['tahun'] = $tahun;
        $data['bulan'] = $bulan;
        $data['karyawan_id'] = $karyawanId;
        $data['tahunOptions'] = $this->getTahunOptions();
        $data['bulanOptions'] = $this->getBulanOptions();
        
        // Data karyawan untuk filter
        $data['karyawanOptions'] = $this->karyawanModel->select('id, nik, nama_lengkap')
            ->where('status_karyawan', 'Tetap')
            ->orWhere('status_karyawan', 'Kontrak')
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();
        
        // Ambil slip gaji untuk periode
        $slipQuery = $this->perhitunganModel->select('penggajian_perhitungan.*, karyawan.nik, karyawan.nama_lengkap, karyawan.jabatan, karyawan.departemen, karyawan.bank, karyawan.no_rekening')
            ->join('karyawan', 'karyawan.id = penggajian_perhitungan.karyawan_id')
            ->where('penggajian_perhitungan.periode_bulan', $bulan)
            ->where('penggajian_perhitungan.periode_tahun', $tahun)
            ->where('penggajian_perhitungan.status', 'Disetujui');
        
        if ($karyawanId) {
            $slipQuery->where('penggajian_perhitungan.karyawan_id', $karyawanId);
        }
        
        $data['slipGaji'] = $slipQuery->findAll();
        
        // Ringkasan periode
        $data['ringkasan'] = $this->perhitunganModel->getRingkasanPeriode($bulan, $tahun);
        
        // Rekap per departemen
        $data['rekapDepartemen'] = $this->perhitunganModel->getRekapPerDepartemen($bulan, $tahun);
        
        $data['active'] = 'slip-gaji';
        return view('accounting/templates/header', $data)
             . view('accounting/templates/sidebar', $data)
             . view('accounting/penggajian/slip-gaji-laporan/slip-gaji/index', $data)
             . view('accounting/templates/footer', $data);
    }

    /**
     * Menu Komponen Gaji (redirect ke halaman komponen)
     */
    public function komponenGaji()
    {
        $data['title'] = 'Komponen Gaji';
        
        $filters = [
            'search' => $this->request->getGet('search'),
            'tipe' => $this->request->getGet('tipe'),
            'is_aktif' => $this->request->getGet('is_aktif')
        ];
        
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 20;
        
        $result = $this->komponenModel->getAllWithFilters($filters, $perPage, $page);
        
        $data['komponen'] = $result['data'];
        $data['pager'] = $this->komponenModel->pager;
        $data['total'] = $result['total'];
        $data['perPage'] = $perPage;
        $data['currentPage'] = $page;
        $data['totalPages'] = $result['total_pages'];
        
        $data['tipeOptions'] = ['Pendapatan', 'Potongan'];
        $data['statusOptions'] = ['1' => 'Aktif', '0' => 'Nonaktif'];
        
        $data['stats'] = $this->komponenModel->getStats();
        
        return view('accounting/penggajian/komponen-gaji', $data);
    }

    /**
     * Get departemen options
     */
    private function getDepartemenOptions()
    {
        $karyawan = $this->karyawanModel->select('departemen')
            ->where('departemen IS NOT NULL')
            ->where('departemen !=', '')
            ->groupBy('departemen')
            ->findAll();
        
        return array_column($karyawan, 'departemen');
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
            12 => 'Desember',
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];
    }
}