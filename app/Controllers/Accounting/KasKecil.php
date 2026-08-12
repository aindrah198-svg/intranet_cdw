<?php
namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\Accounting\KasKecilModel;
use App\Models\CoaModel;
use App\Models\JurnalModel;
use App\Models\JurnalDetailModel;
use App\Models\KaryawanModel;
use App\Models\Teknisi\SpkInstalasiModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Dompdf\Dompdf;
use Dompdf\Options;

class KasKecil extends BaseController
{
    protected $kasKecilModel;
    protected $coaModel;
    protected $jurnalModel;
    protected $jurnalDetailModel;
    protected $karyawanModel;
    protected $spkModel;
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        if (!$this->db->tableExists('kas_kecil')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `kas_kecil` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `tanggal` DATE NOT NULL,
                  `kode_transaksi` VARCHAR(50) NOT NULL,
                  `tipe` ENUM('Pemasukan','Pengeluaran') NOT NULL,
                  `jumlah` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                  `keterangan` TEXT DEFAULT NULL,
                  `coa_lawan_id` INT DEFAULT NULL,
                  `coa_lawan_kode` VARCHAR(50) DEFAULT NULL,
                  `coa_lawan_nama` VARCHAR(150) DEFAULT NULL,
                  `karyawan_id` INT DEFAULT NULL,
                  `nama_karyawan` VARCHAR(150) DEFAULT NULL,
                  `spk_id` INT DEFAULT NULL,
                  `nomor_spk` VARCHAR(50) DEFAULT NULL,
                  `no_bukti` VARCHAR(100) DEFAULT NULL,
                  `lampiran` VARCHAR(255) DEFAULT NULL,
                  `status` ENUM('Draft','Posted','Dibatalkan') DEFAULT 'Draft',
                  `posted_at` DATETIME DEFAULT NULL,
                  `jurnal_id` INT DEFAULT NULL,
                  `nomor_jurnal` VARCHAR(50) DEFAULT NULL,
                  `metode_imprest` TINYINT(1) DEFAULT 0,
                  `saldo_setelah` DECIMAL(15,2) DEFAULT 0.00,
                  `created_by` INT DEFAULT NULL,
                  `updated_by` INT DEFAULT NULL,
                  `created_at` DATETIME DEFAULT NULL,
                  `updated_at` DATETIME DEFAULT NULL,
                  `deleted_at` DATETIME DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }
        $this->kasKecilModel = new KasKecilModel();
        $this->coaModel = new CoaModel();
        $this->jurnalModel = new JurnalModel();
        $this->jurnalDetailModel = new JurnalDetailModel();
        $this->karyawanModel = new KaryawanModel();
        $this->spkModel = new SpkInstalasiModel();
        
        helper(['form', 'url', 'text', 'number']);
        
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Index / Daftar Transaksi Kas Kecil
     */
    public function index()
    {
        $data['title'] = 'Daftar Transaksi Kas Kecil';
        
        $filters = [
            'search' => $this->request->getGet('search'),
            'tanggal_mulai' => $this->request->getGet('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getGet('tanggal_selesai'),
            'tipe' => $this->request->getGet('tipe'),
            'status' => $this->request->getGet('status'),
            'karyawan_id' => $this->request->getGet('karyawan_id'),
            'spk_id' => $this->request->getGet('spk_id')
        ];
        
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 20;
        
        $result = $this->kasKecilModel->getAllWithFilters($filters, $perPage, $page);
        
        $data['transaksi'] = $result['data'];
        $data['pager'] = $this->kasKecilModel->pager;
        $data['total'] = $result['total'];
        $data['perPage'] = $perPage;
        $data['currentPage'] = $page;
        $data['totalPages'] = $result['total_pages'];
        
        $data['tipeOptions'] = ['Pemasukan', 'Pengeluaran'];
        $data['statusOptions'] = ['Draft', 'Posted', 'Dibatalkan'];
        $data['karyawanOptions'] = $this->kasKecilModel->getKaryawanOptions();
        $data['spkOptions'] = $this->kasKecilModel->getSpkOptions();
        
        $statsFilters = [];
        if (!empty($filters['tanggal_mulai'])) $statsFilters['tanggal_mulai'] = $filters['tanggal_mulai'];
        if (!empty($filters['tanggal_selesai'])) $statsFilters['tanggal_selesai'] = $filters['tanggal_selesai'];
        $data['stats'] = $this->kasKecilModel->getStats(
            $filters['tanggal_mulai'] ?? null,
            $filters['tanggal_selesai'] ?? null
        );
        
        $data['saldo_kas_kecil'] = $this->formatRupiah($this->kasKecilModel->getSaldoKasKecil());
        
        return view('accounting/kas-bank/kas-kecil/index', $data);
    }

   /**
 * Form tambah transaksi kas kecil
 */
public function create()
{
    $data['title'] = 'Tambah Transaksi Kas Kecil';
    $data['validation'] = \Config\Services::validation();
    
    $tipe = $this->request->getGet('tipe') ?? 'Pengeluaran';
    
    $data['tipe'] = $tipe;
    $data['coaLawanOptions'] = $this->kasKecilModel->getCoaLawanOptions($tipe);
    $data['karyawanOptions'] = $this->kasKecilModel->getKaryawanOptions();
    
    // Ambil SPK options dan konversi ke array jika perlu
    $spkOptions = $this->kasKecilModel->getSpkOptions();
    // Pastikan dalam bentuk array
    if (!empty($spkOptions) && is_object($spkOptions[0] ?? null)) {
        $spkOptions = json_decode(json_encode($spkOptions), true);
    }
    $data['spkOptions'] = $spkOptions;
    
    $data['transaksi'] = [
        'tanggal' => date('Y-m-d'),
        'tipe' => $tipe,
        'jumlah' => 0,
        'status' => 'Draft',
        'metode_imprest' => 1
    ];
    
    $data['saldo_kas_kecil'] = $this->formatRupiah($this->kasKecilModel->getSaldoKasKecil());
    
    return view('accounting/kas-bank/kas-kecil/create', $data);
}

    /**
     * Simpan transaksi kas kecil baru
     */
    public function store()
    {
        // Validasi input
        $rules = [
            'tanggal' => 'required|valid_date',
            'tipe' => 'required|in_list[Pemasukan,Pengeluaran]',
            'jumlah' => 'required|numeric|greater_than[0]',
            'keterangan' => 'required|min_length[3]',
            'coa_lawan_id' => 'required|is_natural_no_zero',
            'karyawan_id' => 'permit_empty|is_natural_no_zero',
            'spk_id' => 'permit_empty|is_natural_no_zero',
            'no_bukti' => 'permit_empty',
            'metode_imprest' => 'permit_empty|in_list[0,1]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $tipe = $this->request->getPost('tipe');
        $jumlah = $this->cleanCurrency($this->request->getPost('jumlah'));
        
        // Validasi saldo untuk pengeluaran
        if ($tipe === 'Pengeluaran') {
            $saldoKasKecil = $this->kasKecilModel->getSaldoKasKecil();
            if ($saldoKasKecil < $jumlah) {
                return redirect()->back()->withInput()
                    ->with('error', 'Saldo kas kecil tidak mencukupi. Saldo saat ini: Rp ' . $this->formatRupiah($saldoKasKecil));
            }
        }
        
        $data = [
            'tanggal' => $this->request->getPost('tanggal'),
            'tipe' => $tipe,
            'jumlah' => $jumlah,
            'keterangan' => $this->request->getPost('keterangan'),
            'coa_lawan_id' => $this->request->getPost('coa_lawan_id'),
            'karyawan_id' => $this->request->getPost('karyawan_id') ?: null,
            'spk_id' => $this->request->getPost('spk_id') ?: null,
            'no_bukti' => $this->request->getPost('no_bukti'),
            'metode_imprest' => $this->request->getPost('metode_imprest') ?: 1,
            'status' => 'Draft'
        ];
        
        $lampiran = $this->request->getFile('lampiran');
        if ($lampiran && $lampiran->isValid() && !$lampiran->hasMoved()) {
            $newName = $lampiran->getRandomName();
            $lampiran->move('uploads/kas-kecil', $newName);
            $data['lampiran'] = 'uploads/kas-kecil/' . $newName;
        }
        
        try {
            $this->db->transBegin();
            
            $saved = $this->kasKecilModel->save($data);
            
            if (!$saved) {
                throw new \Exception('Gagal menyimpan data: ' . json_encode($this->kasKecilModel->errors()));
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/kas-bank/kas-kecil')
                ->with('success', 'Transaksi kas kecil berhasil disimpan sebagai Draft.');
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menyimpan transaksi kas kecil: ' . $e->getMessage());
        }
    }

    /**
     * Detail transaksi kas kecil
     */
    public function detail($id)
    {
        $data['title'] = 'Detail Transaksi Kas Kecil';
        
        $transaksi = $this->kasKecilModel->getWithDetails($id);
        
        if (!$transaksi) {
            return redirect()->to('accounting/kas-bank/kas-kecil')
                ->with('error', 'Transaksi kas kecil tidak ditemukan');
        }
        
        // Format jumlah ke Rupiah
        $transaksi['jumlah_formatted'] = $this->formatRupiah($transaksi['jumlah']);
        $transaksi['terbilang'] = ucwords($this->terbilang($transaksi['jumlah'])) . ' Rupiah';
        
        $data['transaksi'] = $transaksi;
        
        return view('accounting/kas-bank/kas-kecil/detail', $data);
    }

    /**
     * Form edit transaksi kas kecil
     */
    public function edit($id)
    {
        $data['title'] = 'Edit Transaksi Kas Kecil';
        
        $transaksi = $this->kasKecilModel->find($id);
        
        if (!$transaksi) {
            return redirect()->to('accounting/kas-bank/kas-kecil')
                ->with('error', 'Transaksi kas kecil tidak ditemukan');
        }
        
        if ($transaksi['status'] !== 'Draft') {
            return redirect()->to('accounting/kas-bank/kas-kecil')
                ->with('error', 'Hanya transaksi dengan status Draft yang dapat diedit');
        }
        
        $transaksi['jumlah_formatted'] = $this->formatRupiah($transaksi['jumlah']);
        
        $data['validation'] = \Config\Services::validation();
        $data['transaksi'] = $transaksi;
        $data['tipe'] = $transaksi['tipe'];
        $data['coaLawanOptions'] = $this->kasKecilModel->getCoaLawanOptions($transaksi['tipe']);
        $data['karyawanOptions'] = $this->kasKecilModel->getKaryawanOptions();
        $data['spkOptions'] = $this->kasKecilModel->getSpkOptions();
        $data['saldo_kas_kecil'] = $this->formatRupiah($this->kasKecilModel->getSaldoKasKecil());
        
        return view('accounting/kas-bank/kas-kecil/edit', $data);
    }

    /**
     * Update transaksi kas kecil
     */
    public function update($id)
    {
        $transaksi = $this->kasKecilModel->find($id);
        
        if (!$transaksi) {
            return redirect()->to('accounting/kas-bank/kas-kecil')
                ->with('error', 'Transaksi kas kecil tidak ditemukan');
        }
        
        if ($transaksi['status'] !== 'Draft') {
            return redirect()->to('accounting/kas-bank/kas-kecil')
                ->with('error', 'Hanya transaksi dengan status Draft yang dapat diedit');
        }
        
        $rules = [
            'tanggal' => 'required|valid_date',
            'tipe' => 'required|in_list[Pemasukan,Pengeluaran]',
            'jumlah' => 'required|numeric|greater_than[0]',
            'keterangan' => 'required|min_length[3]',
            'coa_lawan_id' => 'required|is_natural_no_zero',
            'karyawan_id' => 'permit_empty|is_natural_no_zero',
            'spk_id' => 'permit_empty|is_natural_no_zero',
            'no_bukti' => 'permit_empty'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $tipe = $this->request->getPost('tipe');
        $jumlah = $this->cleanCurrency($this->request->getPost('jumlah'));
        
        // Validasi saldo untuk pengeluaran jika jumlah berubah
        if ($tipe === 'Pengeluaran' && $jumlah != $transaksi['jumlah']) {
            $selisih = $jumlah - $transaksi['jumlah'];
            if ($selisih > 0) {
                $saldoKasKecil = $this->kasKecilModel->getSaldoKasKecilSebelumTransaksi($id);
                if ($saldoKasKecil < $selisih) {
                    return redirect()->back()->withInput()
                        ->with('error', 'Saldo kas kecil tidak mencukupi untuk perubahan ini. Saldo tersedia: Rp ' . $this->formatRupiah($saldoKasKecil));
                }
            }
        }
        
        $data = [
            'id' => $id,
            'tanggal' => $this->request->getPost('tanggal'),
            'tipe' => $tipe,
            'jumlah' => $jumlah,
            'keterangan' => $this->request->getPost('keterangan'),
            'coa_lawan_id' => $this->request->getPost('coa_lawan_id'),
            'karyawan_id' => $this->request->getPost('karyawan_id') ?: null,
            'spk_id' => $this->request->getPost('spk_id') ?: null,
            'no_bukti' => $this->request->getPost('no_bukti'),
            'metode_imprest' => $this->request->getPost('metode_imprest') ?: 1
        ];
        
        $lampiran = $this->request->getFile('lampiran');
        if ($lampiran && $lampiran->isValid() && !$lampiran->hasMoved()) {
            if (!empty($transaksi['lampiran']) && file_exists(FCPATH . $transaksi['lampiran'])) {
                unlink(FCPATH . $transaksi['lampiran']);
            }
            
            $newName = $lampiran->getRandomName();
            $lampiran->move('uploads/kas-kecil', $newName);
            $data['lampiran'] = 'uploads/kas-kecil/' . $newName;
        }
        
        try {
            $this->db->transBegin();
            
            $updated = $this->kasKecilModel->save($data);
            
            if (!$updated) {
                throw new \Exception('Gagal mengupdate data: ' . json_encode($this->kasKecilModel->errors()));
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/kas-bank/kas-kecil')
                ->with('success', 'Transaksi kas kecil berhasil diupdate.');
                
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal mengupdate transaksi kas kecil: ' . $e->getMessage());
        }
    }

    /**
     * Hapus transaksi kas kecil
     */
    public function delete($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $transaksi = $this->kasKecilModel->find($id);
        
        if (!$transaksi) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Transaksi kas kecil tidak ditemukan'
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/kas-kecil')
                    ->with('error', 'Transaksi kas kecil tidak ditemukan');
            }
        }
        
        if ($transaksi['status'] !== 'Draft') {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Hanya transaksi dengan status Draft yang dapat dihapus'
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/kas-kecil')
                    ->with('error', 'Hanya transaksi dengan status Draft yang dapat dihapus');
            }
        }
        
        try {
            $this->db->transBegin();
            
            if (!empty($transaksi['lampiran']) && file_exists(FCPATH . $transaksi['lampiran'])) {
                unlink(FCPATH . $transaksi['lampiran']);
            }
            
            $deleted = $this->kasKecilModel->delete($id);
            
            if (!$deleted) {
                throw new \Exception('Gagal menghapus data');
            }
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Transaksi kas kecil berhasil dihapus',
                    'redirect' => site_url('accounting/kas-bank/kas-kecil')
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/kas-kecil')
                    ->with('success', 'Transaksi kas kecil berhasil dihapus');
            }
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus transaksi kas kecil: ' . $e->getMessage()
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal menghapus transaksi kas kecil: ' . $e->getMessage());
            }
        }
    }

    /**
     * Posting transaksi kas kecil ke jurnal
     */
    public function post($id)
    {
        $transaksi = $this->kasKecilModel->find($id);
        
        if (!$transaksi) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Transaksi kas kecil tidak ditemukan'
            ]);
        }
        
        if ($transaksi['status'] !== 'Draft') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Hanya transaksi dengan status Draft yang bisa diposting'
            ]);
        }
        
        try {
            $this->db->transBegin();
            
            // Ambil data COA lawan
            $coaLawan = $this->coaModel->find($transaksi['coa_lawan_id']);
            
            if (!$coaLawan) {
                throw new \Exception('Akun lawan tidak ditemukan');
            }
            
            // Ambil COA Kas Kecil (1-1101)
            $coaKasKecil = $this->coaModel->where('kode_akun', '1-1101')->first();
            if (!$coaKasKecil) {
                // Jika tidak ditemukan, cari akun dengan nama mengandung 'Kas Kecil'
                $coaKasKecil = $this->coaModel->like('nama_akun', 'Kas Kecil')
                    ->where('is_header', 0)
                    ->first();
            }
            
            if (!$coaKasKecil) {
                throw new \Exception('Akun Kas Kecil tidak ditemukan. Harap buat akun dengan kode 1-1101 atau nama "Kas Kecil"');
            }
            
            // Nonaktifkan validasi sementara untuk jurnal
            $this->jurnalModel->skipValidation(true);
            
            // Buat jurnal
            $jurnalData = [
                'tanggal' => $transaksi['tanggal'],
                'keterangan' => $transaksi['keterangan'] . ' (' . $transaksi['kode_transaksi'] . ')',
                'referensi' => $transaksi['kode_transaksi'],
                'tipe_referensi' => 'kas_kecil',
                'status' => 'posted',
                'posted_by' => session()->get('user_id'),
                'posted_at' => date('Y-m-d H:i:s'),
                'total_debit' => $transaksi['jumlah'],
                'total_kredit' => $transaksi['jumlah'],
                'created_by' => session()->get('user_id')
            ];
            
            $jurnalId = $this->jurnalModel->insert($jurnalData);
            if (!$jurnalId) {
                $errors = $this->jurnalModel->errors();
                log_message('error', 'Gagal insert jurnal: ' . json_encode($errors));
                $this->jurnalModel->skipValidation(false);
                throw new \Exception('Gagal membuat jurnal: ' . json_encode($errors));
            }
            
            $this->jurnalModel->skipValidation(false);
            
            $detailData = [];
            
            if ($transaksi['tipe'] === 'Pemasukan') {
                // Pemasukan (pengisian kembali): Debit Kas Kecil, Kredit Kas/Bank
                $detailData[] = [
                    'jurnal_id' => $jurnalId,
                    'coa_id' => $coaKasKecil['id'],
                    'kode_akun' => $coaKasKecil['kode_akun'],
                    'nama_akun' => $coaKasKecil['nama_akun'],
                    'debit' => $transaksi['jumlah'],
                    'kredit' => 0,
                    'keterangan' => 'Pengisian kembali kas kecil: ' . $transaksi['keterangan']
                ];
                
                $detailData[] = [
                    'jurnal_id' => $jurnalId,
                    'coa_id' => $coaLawan['id'],
                    'kode_akun' => $coaLawan['kode_akun'],
                    'nama_akun' => $coaLawan['nama_akun'],
                    'debit' => 0,
                    'kredit' => $transaksi['jumlah'],
                    'keterangan' => 'Pengisian kembali kas kecil dari: ' . $coaLawan['nama_akun']
                ];
                
            } else {
                // Pengeluaran: Debit Beban/Aset, Kredit Kas Kecil
                $detailData[] = [
                    'jurnal_id' => $jurnalId,
                    'coa_id' => $coaLawan['id'],
                    'kode_akun' => $coaLawan['kode_akun'],
                    'nama_akun' => $coaLawan['nama_akun'],
                    'debit' => $transaksi['jumlah'],
                    'kredit' => 0,
                    'keterangan' => 'Pengeluaran kas kecil: ' . $transaksi['keterangan']
                ];
                
                $detailData[] = [
                    'jurnal_id' => $jurnalId,
                    'coa_id' => $coaKasKecil['id'],
                    'kode_akun' => $coaKasKecil['kode_akun'],
                    'nama_akun' => $coaKasKecil['nama_akun'],
                    'debit' => 0,
                    'kredit' => $transaksi['jumlah'],
                    'keterangan' => 'Pengeluaran kas kecil untuk: ' . $coaLawan['nama_akun']
                ];
            }
            
            // Simpan detail jurnal
            foreach ($detailData as $detail) {
                if (!$this->jurnalDetailModel->insert($detail)) {
                    $errors = $this->jurnalDetailModel->errors();
                    log_message('error', 'Gagal insert detail jurnal: ' . json_encode($errors));
                    throw new \Exception('Gagal menyimpan detail jurnal: ' . json_encode($errors));
                }
            }
            
            // Update status transaksi menjadi Posted
            $this->kasKecilModel->postTransaksi($id, $jurnalId);
            
            $this->db->transCommit();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Transaksi kas kecil berhasil diposting ke jurnal',
                'redirect' => site_url('accounting/kas-bank/kas-kecil/detail/' . $id)
            ]);
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error posting kas kecil: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memposting transaksi kas kecil: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Batalkan transaksi kas kecil
     */
    public function batalkan($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $transaksi = $this->kasKecilModel->find($id);
        
        if (!$transaksi) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Transaksi kas kecil tidak ditemukan'
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/kas-kecil')
                    ->with('error', 'Transaksi kas kecil tidak ditemukan');
            }
        }
        
        try {
            $this->db->transBegin();
            
            if ($transaksi['status'] === 'Posted' && !empty($transaksi['jurnal_id'])) {
                $this->jurnalModel->update($transaksi['jurnal_id'], ['status' => 'void']);
            }
            
            $this->kasKecilModel->batalkanTransaksi($id);
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Transaksi kas kecil berhasil dibatalkan',
                    'redirect' => site_url('accounting/kas-bank/kas-kecil')
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/kas-kecil')
                    ->with('success', 'Transaksi kas kecil berhasil dibatalkan');
            }
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal membatalkan transaksi kas kecil: ' . $e->getMessage()
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal membatalkan transaksi kas kecil: ' . $e->getMessage());
            }
        }
    }

    /**
     * Form pengisian kembali kas kecil (replenishment)
     */
    public function pengisianKembali()
    {
        $data['title'] = 'Pengisian Kembali Kas Kecil';
        $data['validation'] = \Config\Services::validation();
        
        // Hitung total pengeluaran periode berjalan
        $tanggalMulai = date('Y-m-01');
        $tanggalAkhir = date('Y-m-d');
        
        $totalPengeluaran = $this->kasKecilModel->select('SUM(jumlah) as total')
            ->where('tipe', 'Pengeluaran')
            ->where('status', 'Posted')
            ->where('tanggal >=', $tanggalMulai)
            ->where('tanggal <=', $tanggalAkhir)
            ->first();
        
        $data['total_pengeluaran'] = $totalPengeluaran['total'] ?? 0;
        $data['total_pengeluaran_formatted'] = $this->formatRupiah($data['total_pengeluaran']);
        
        $data['saldo_kas_kecil'] = $this->formatRupiah($this->kasKecilModel->getSaldoKasKecil());
        
        // Ambil akun bank untuk sumber dana
        $data['bankOptions'] = $this->coaModel->where('is_header', 0)
            ->where('is_active', 1)
            ->like('kode_akun', '1-11', 'after')
            ->orderBy('kode_akun', 'ASC')
            ->findAll();
        
        $data['tanggal_pengisian'] = date('Y-m-d');
        
        return view('accounting/kas-bank/kas-kecil/pengisian-kembali', $data);
    }

    /**
     * Proses pengisian kembali kas kecil
     */
    public function prosesPengisianKembali()
    {
        $rules = [
            'tanggal' => 'required|valid_date',
            'jumlah' => 'required|numeric|greater_than[0]',
            'coa_bank_id' => 'required|is_natural_no_zero',
            'keterangan' => 'required|min_length[3]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $tanggal = $this->request->getPost('tanggal');
        $jumlah = $this->cleanCurrency($this->request->getPost('jumlah'));
        $coaBankId = $this->request->getPost('coa_bank_id');
        $keterangan = $this->request->getPost('keterangan');
        
        // Validasi
        $validation = $this->kasKecilModel->validateReplenishment($jumlah, $coaBankId);
        if (!$validation['valid']) {
            return redirect()->back()->withInput()
                ->with('error', implode('<br>', $validation['errors']));
        }
        
        try {
            $this->db->transBegin();
            
            // Buat transaksi pemasukan kas kecil
            $data = [
                'tanggal' => $tanggal,
                'tipe' => 'Pemasukan',
                'jumlah' => $jumlah,
                'keterangan' => $keterangan ?: 'Pengisian kembali kas kecil',
                'coa_lawan_id' => $coaBankId,
                'status' => 'Draft',
                'metode_imprest' => 1
            ];
            
            $transaksiId = $this->kasKecilModel->insert($data);
            
            if (!$transaksiId) {
                throw new \Exception('Gagal membuat transaksi pengisian kembali');
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/kas-bank/kas-kecil/detail/' . $transaksiId)
                ->with('success', 'Transaksi pengisian kembali berhasil dibuat. Silakan posting ke jurnal.');
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal memproses pengisian kembali: ' . $e->getMessage());
        }
    }

    /**
     * Buku kas kecil (mutasi)
     */
    public function bukuKasKecil()
    {
        $data['title'] = 'Buku Kas Kecil';
        
        $tanggalMulai = $this->request->getGet('tanggal_mulai') ?? date('Y-m-01');
        $tanggalSelesai = $this->request->getGet('tanggal_selesai') ?? date('Y-m-d');
        
        $data['tanggal_mulai'] = $tanggalMulai;
        $data['tanggal_selesai'] = $tanggalSelesai;
        
        $data['transaksi'] = $this->kasKecilModel->getBukuKasKecil($tanggalMulai, $tanggalSelesai);
        
        // Hitung saldo awal periode
        $saldoAwal = $this->kasKecilModel->select('SUM(CASE WHEN tipe = "Pemasukan" THEN jumlah ELSE 0 END) - SUM(CASE WHEN tipe = "Pengeluaran" THEN jumlah ELSE 0 END) as saldo')
            ->where('status', 'Posted')
            ->where('tanggal <', $tanggalMulai)
            ->first();
        
        $data['saldo_awal'] = $saldoAwal['saldo'] ?? 0;
        $data['saldo_awal_formatted'] = $this->formatRupiah($data['saldo_awal']);
        
        // Hitung total pemasukan dan pengeluaran periode
        $total = $this->kasKecilModel->select("
                SUM(CASE WHEN tipe = 'Pemasukan' THEN jumlah ELSE 0 END) as total_pemasukan,
                SUM(CASE WHEN tipe = 'Pengeluaran' THEN jumlah ELSE 0 END) as total_pengeluaran
            ")
            ->where('status', 'Posted')
            ->where('tanggal >=', $tanggalMulai)
            ->where('tanggal <=', $tanggalSelesai)
            ->first();
        
        $data['total_pemasukan'] = $total['total_pemasukan'] ?? 0;
        $data['total_pengeluaran'] = $total['total_pengeluaran'] ?? 0;
        $data['total_pemasukan_formatted'] = $this->formatRupiah($data['total_pemasukan']);
        $data['total_pengeluaran_formatted'] = $this->formatRupiah($data['total_pengeluaran']);
        
        // Saldo akhir
        $data['saldo_akhir'] = $data['saldo_awal'] + $data['total_pemasukan'] - $data['total_pengeluaran'];
        $data['saldo_akhir_formatted'] = $this->formatRupiah($data['saldo_akhir']);
        
        return view('accounting/kas-bank/kas-kecil/buku-kas-kecil', $data);
    }

    /**
     * Mutasi kas kecil (sama dengan buku kas kecil, untuk konsistensi menu)
     */
    public function mutasiKasKecil()
    {
        return $this->bukuKasKecil();
    }

    /**
     * AJAX: Get COA lawan options
     */
    public function ajaxGetCoaLawan()
    {
        $tipe = $this->request->getGet('tipe');
        
        $coa = $this->kasKecilModel->getCoaLawanOptions($tipe);
        
        $options = [];
        foreach ($coa as $item) {
            $options[] = [
                'id' => $item['id'],
                'text' => $item['kode_akun'] . ' - ' . $item['nama_akun']
            ];
        }
        
        return $this->response->setJSON($options);
    }

    /**
     * AJAX: Get karyawan options
     */
    public function ajaxGetKaryawan()
    {
        $karyawan = $this->kasKecilModel->getKaryawanOptions();
        
        $options = [];
        foreach ($karyawan as $item) {
            $options[] = [
                'id' => $item['id'],
                'text' => $item['nik'] . ' - ' . $item['nama_lengkap'] . ' (' . ($item['jabatan'] ?? '-') . ')'
            ];
        }
        
        return $this->response->setJSON($options);
    }

    /**
     * AJAX: Get SPK options
     */
    public function ajaxGetSpk()
    {
        $spk = $this->kasKecilModel->getSpkOptions();
        
        $options = [];
        foreach ($spk as $item) {
            $options[] = [
                'id' => $item['id'],
                'text' => $item['nomor_spk'] . ' - ' . $item['judul_pekerjaan'] . ' (' . $item['status'] . ')'
            ];
        }
        
        return $this->response->setJSON($options);
    }

    /**
     * AJAX: Get saldo kas kecil terkini
     */
    public function ajaxGetSaldoKasKecil()
    {
        $saldo = $this->kasKecilModel->getSaldoKasKecil();
        
        return $this->response->setJSON([
            'success' => true,
            'saldo' => $this->formatRupiah($saldo),
            'saldo_raw' => $saldo
        ]);
    }

    /**
     * AJAX: Validate saldo sebelum simpan
     */
    public function ajaxValidateSaldo()
    {
        $tipe = $this->request->getPost('tipe');
        $jumlah = $this->cleanCurrency($this->request->getPost('jumlah'));
        
        $errors = [];
        
        if ($tipe === 'Pengeluaran' && $jumlah > 0) {
            $saldoKasKecil = $this->kasKecilModel->getSaldoKasKecil();
            if ($saldoKasKecil < $jumlah) {
                $errors[] = 'Saldo kas kecil tidak mencukupi. Saldo saat ini: Rp ' . $this->formatRupiah($saldoKasKecil);
            }
        }
        
        return $this->response->setJSON([
            'success' => empty($errors),
            'errors' => $errors
        ]);
    }

    /**
     * AJAX: Get teks terbilang
     */
    public function ajaxGetTerbilang()
    {
        try {
            $jumlah = $this->request->getGet('jumlah');
            
            log_message('debug', 'ajaxGetTerbilang called with jumlah: ' . $jumlah);
            
            if (empty($jumlah)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Jumlah tidak boleh kosong',
                    'terbilang' => ''
                ]);
            }
            
            $jumlahBersih = $this->cleanCurrency($jumlah);
            
            log_message('debug', 'Jumlah bersih: ' . $jumlahBersih);
            
            if ($jumlahBersih > 0) {
                $terbilang = ucwords($this->terbilang($jumlahBersih)) . ' Rupiah';
                
                return $this->response->setJSON([
                    'success' => true,
                    'terbilang' => $terbilang,
                    'jumlah_original' => $jumlah,
                    'jumlah_bersih' => $jumlahBersih
                ]);
            }
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Jumlah harus lebih dari 0',
                'terbilang' => ''
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in ajaxGetTerbilang: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'terbilang' => ''
            ]);
        }
    }

    /**
     * AJAX: Get rekap pengeluaran
     */
    public function ajaxGetRekapPengeluaran()
    {
        $tanggalMulai = $this->request->getGet('tanggal_mulai') ?? date('Y-m-01');
        $tanggalSelesai = $this->request->getGet('tanggal_selesai') ?? date('Y-m-d');
        $tipe = $this->request->getGet('tipe'); // 'kategori', 'karyawan', 'spk'
        
        $data = [];
        
        switch ($tipe) {
            case 'kategori':
                $data = $this->kasKecilModel->getRekapPengeluaranPerKategori($tanggalMulai, $tanggalSelesai);
                break;
            case 'karyawan':
                $data = $this->kasKecilModel->getRekapPengeluaranPerKaryawan($tanggalMulai, $tanggalSelesai);
                break;
            case 'spk':
                $data = $this->kasKecilModel->getRekapPengeluaranPerSpk($tanggalMulai, $tanggalSelesai);
                break;
        }
        
        // Format angka
        foreach ($data as &$item) {
            if (isset($item['total_pengeluaran'])) {
                $item['total_pengeluaran_formatted'] = $this->formatRupiah($item['total_pengeluaran']);
            }
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Filter data
     */
    public function filter()
    {
        $filters = [
            'tanggal_mulai' => $this->request->getGet('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getGet('tanggal_selesai'),
            'tipe' => $this->request->getGet('tipe'),
            'status' => $this->request->getGet('status'),
            'karyawan_id' => $this->request->getGet('karyawan_id'),
            'spk_id' => $this->request->getGet('spk_id')
        ];
        
        session()->set('filter_kas_kecil', $filters);
        
        return redirect()->to('accounting/kas-bank/kas-kecil');
    }

    /**
     * Search data
     */
    public function search()
    {
        $search = $this->request->getGet('q');
        
        $filters = session()->get('filter_kas_kecil') ?? [];
        $filters['search'] = $search;
        
        session()->set('filter_kas_kecil', $filters);
        
        return redirect()->to('accounting/kas-bank/kas-kecil');
    }

    /**
     * List status Draft
     */
    public function draft()
    {
        $filters = ['status' => 'Draft'];
        session()->set('filter_kas_kecil', $filters);
        
        return redirect()->to('accounting/kas-bank/kas-kecil');
    }

    /**
     * List status Posted
     */
    public function posted()
    {
        $filters = ['status' => 'Posted'];
        session()->set('filter_kas_kecil', $filters);
        
        return redirect()->to('accounting/kas-bank/kas-kecil');
    }

    /**
     * List status Dibatalkan
     */
    public function dibatalkan()
    {
        $filters = ['status' => 'Dibatalkan'];
        session()->set('filter_kas_kecil', $filters);
        
        return redirect()->to('accounting/kas-bank/kas-kecil');
    }

    /**
     * Export data
     */
    public function export()
    {
        $type = $this->request->getGet('type') ?? 'excel';
        $filters = [
            'search' => $this->request->getGet('search'),
            'tanggal_mulai' => $this->request->getGet('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getGet('tanggal_selesai'),
            'tipe' => $this->request->getGet('tipe'),
            'status' => $this->request->getGet('status')
        ];
        
        $data = $this->kasKecilModel->getExportData($filters);
        $stats = $this->kasKecilModel->getStats(
            $filters['tanggal_mulai'] ?? null,
            $filters['tanggal_selesai'] ?? null
        );
        
        if ($type === 'excel') {
            return $this->exportExcel($data, $filters, $stats);
        } else {
            return $this->exportPdf($data, $filters, $stats);
        }
    }

    /**
     * Export ke Excel
     */
    private function exportExcel($data, $filters, $stats)
    {
        try {
            $spreadsheet = new Spreadsheet();
            
            $spreadsheet->getProperties()
                ->setCreator("CDW Engineering")
                ->setLastModifiedBy("CDW Engineering")
                ->setTitle("Laporan Kas Kecil")
                ->setSubject("Laporan Kas Kecil")
                ->setDescription("Laporan Kas Kecil " . date('d-m-Y'));
            
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Data Kas Kecil');
            
            // Header laporan
            $sheet->mergeCells('A1:L1');
            $sheet->setCellValue('A1', 'LAPORAN KAS KECIL');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A2:L2');
            $sheet->setCellValue('A2', 'PT. CIPTA DUTA WACANA');
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Periode
            $periodeText = 'Periode: ';
            if (!empty($filters['tanggal_mulai']) && !empty($filters['tanggal_selesai'])) {
                $periodeText .= date('d/m/Y', strtotime($filters['tanggal_mulai'])) . ' - ' . date('d/m/Y', strtotime($filters['tanggal_selesai']));
            } elseif (!empty($filters['tanggal_mulai'])) {
                $periodeText .= 'Mulai ' . date('d/m/Y', strtotime($filters['tanggal_mulai']));
            } elseif (!empty($filters['tanggal_selesai'])) {
                $periodeText .= 'Sampai ' . date('d/m/Y', strtotime($filters['tanggal_selesai']));
            } else {
                $periodeText .= 'Semua Periode';
            }
            
            $sheet->mergeCells('A3:L3');
            $sheet->setCellValue('A3', $periodeText);
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Baris kosong
            $startRow = 5;
            
            // Header tabel
            $headers = [
                'A' => 'No',
                'B' => 'Tanggal',
                'C' => 'Kode Transaksi',
                'D' => 'Tipe',
                'E' => 'Jumlah',
                'F' => 'Saldo Setelah',
                'G' => 'Akun Lawan',
                'H' => 'Karyawan',
                'I' => 'SPK/Proyek',
                'J' => 'No Bukti',
                'K' => 'Keterangan',
                'L' => 'Status'
            ];
            
            foreach ($headers as $col => $header) {
                $sheet->setCellValue($col . $startRow, $header);
            }
            
            // Style header
            $headerRange = 'A' . $startRow . ':L' . $startRow;
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
                $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($item['Tanggal'])));
                $sheet->setCellValue('C' . $row, $item['Kode Transaksi']);
                $sheet->setCellValue('D' . $row, $item['Tipe']);
                $sheet->setCellValue('E' . $row, $item['Jumlah']);
                $sheet->setCellValue('F' . $row, $item['Saldo Setelah']);
                $sheet->setCellValue('G' . $row, $item['Akun Lawan']);
                $sheet->setCellValue('H' . $row, $item['Karyawan']);
                $sheet->setCellValue('I' . $row, $item['SPK/Proyek']);
                $sheet->setCellValue('J' . $row, $item['No Bukti']);
                $sheet->setCellValue('K' . $row, $item['Keterangan']);
                $sheet->setCellValue('L' . $row, $item['Status']);
                
                // Format jumlah sebagai currency
                $sheet->getStyle('E' . $row . ':F' . $row)->getNumberFormat()
                    ->setFormatCode('"Rp" #,##0.00');
                
                // Warna berdasarkan tipe
                if ($item['Tipe'] == 'Pemasukan') {
                    $sheet->getStyle('D' . $row)->getFont()->getColor()->setARGB('FF008000');
                } else {
                    $sheet->getStyle('D' . $row)->getFont()->getColor()->setARGB('FFFF0000');
                }
                
                // Warna status
                $statusColor = match($item['Status']) {
                    'Posted' => 'FF008000',
                    'Draft' => 'FFFFA500',
                    'Dibatalkan' => 'FFFF0000',
                    default => 'FF000000'
                };
                $sheet->getStyle('L' . $row)->getFont()->getColor()->setARGB($statusColor);
                
                $row++;
                $no++;
            }
            
            // Auto-size columns
            foreach (range('A', 'L') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Border untuk seluruh data
            $lastRow = $row - 1;
            if ($lastRow >= $startRow) {
                $dataRange = 'A' . $startRow . ':L' . $lastRow;
                $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            }
            
            // ============= SHEET 2: RINGKASAN =============
            $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex(1);
            $sheet2 = $spreadsheet->getActiveSheet();
            $sheet2->setTitle('Ringkasan');
            
            // Header
            $sheet2->mergeCells('A1:C1');
            $sheet2->setCellValue('A1', 'RINGKASAN KAS KECIL');
            $sheet2->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            
            $sheet2->mergeCells('A2:C2');
            $sheet2->setCellValue('A2', $periodeText);
            
            // Statistik
            $sheet2->setCellValue('A4', 'STATISTIK');
            $sheet2->getStyle('A4')->getFont()->setBold(true);
            
            $statsData = [
                ['Total Transaksi', $stats['total_transaksi'] ?? 0],
                ['Total Pemasukan', $stats['total_pemasukan'] ?? 0],
                ['Total Pengeluaran', $stats['total_pengeluaran'] ?? 0],
                ['Jumlah Pemasukan', $stats['jumlah_pemasukan'] ?? 0],
                ['Jumlah Pengeluaran', $stats['jumlah_pengeluaran'] ?? 0],
                ['Transaksi Hari Ini', $stats['transaksi_hari_ini'] ?? 0],
                ['Saldo Kas Kecil', $stats['saldo_terkini'] ?? 0]
            ];
            
            $rowStat = 5;
            foreach ($statsData as $stat) {
                $sheet2->setCellValue('A' . $rowStat, $stat[0]);
                $sheet2->setCellValue('B' . $rowStat, $stat[1]);
                
                if (strpos($stat[0], 'Total') === 0 || strpos($stat[0], 'Saldo') === 0) {
                    $sheet2->getStyle('B' . $rowStat)->getNumberFormat()
                        ->setFormatCode('"Rp" #,##0.00');
                }
                
                $rowStat++;
            }
            
            // Auto-size columns sheet 2
            foreach (range('A', 'C') as $col) {
                $sheet2->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Set active sheet ke sheet pertama
            $spreadsheet->setActiveSheetIndex(0);
            
            // Output file
            $filename = 'Kas_Kecil_' . date('Ymd_His') . '.xlsx';
            
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
     * Export ke PDF
     */
    private function exportPdf($data, $filters, $stats)
    {
        try {
            $html = $this->generatePdfHtml($data, $filters, $stats);
            
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->set('defaultFont', 'Arial');
            $options->set('isRemoteEnabled', true);
            
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            
            $filename = 'Kas_Kecil_' . date('Ymd_His') . '.pdf';
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
    private function generatePdfHtml($data, $filters, $stats)
    {
        $periodeText = '';
        if (!empty($filters['tanggal_mulai']) && !empty($filters['tanggal_selesai'])) {
            $periodeText = date('d/m/Y', strtotime($filters['tanggal_mulai'])) . ' - ' . date('d/m/Y', strtotime($filters['tanggal_selesai']));
        } elseif (!empty($filters['tanggal_mulai'])) {
            $periodeText = 'Mulai ' . date('d/m/Y', strtotime($filters['tanggal_mulai']));
        } elseif (!empty($filters['tanggal_selesai'])) {
            $periodeText = 'Sampai ' . date('d/m/Y', strtotime($filters['tanggal_selesai']));
        } else {
            $periodeText = 'Semua Periode';
        }
        
        $filterInfo = [];
        if (!empty($filters['tipe'])) $filterInfo[] = 'Tipe: ' . $filters['tipe'];
        if (!empty($filters['status'])) $filterInfo[] = 'Status: ' . $filters['status'];
        $filterText = implode(' | ', $filterInfo);
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Laporan Kas Kecil</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 9px;
                    line-height: 1.2;
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
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 15px;
                    font-size: 8px;
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
                .text-end {
                    text-align: right;
                }
                .text-center {
                    text-align: center;
                }
                .text-success {
                    color: #28a745;
                    font-weight: bold;
                }
                .text-danger {
                    color: #dc3545;
                    font-weight: bold;
                }
                .badge {
                    padding: 2px 4px;
                    border-radius: 2px;
                    font-weight: bold;
                    font-size: 7px;
                }
                .badge-success {
                    background-color: #28a745;
                    color: white;
                }
                .badge-warning {
                    background-color: #ffc107;
                    color: black;
                }
                .badge-danger {
                    background-color: #dc3545;
                    color: white;
                }
                .footer {
                    margin-top: 15px;
                    padding-top: 8px;
                    border-top: 1px solid #000;
                    font-size: 8px;
                }
                .summary-box {
                    float: left;
                    width: 13%;
                    margin: 0 0.5%;
                    padding: 5px;
                    background-color: #f8f9fa;
                    border: 1px solid #dee2e6;
                    border-radius: 3px;
                    text-align: center;
                }
                .summary-value {
                    font-size: 10px;
                    font-weight: bold;
                    margin-top: 3px;
                }
                .summary-label {
                    font-size: 7px;
                    color: #666;
                }
                .clearfix:after {
                    content: "";
                    display: table;
                    clear: both;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>LAPORAN KAS KECIL</h1>
                <h2>PT. CIPTA DUTA WACANA</h2>
                <div style="font-size: 9px;">Periode: ' . $periodeText . '</div>
                ' . (!empty($filterText) ? '<div style="font-size: 9px;">' . $filterText . '</div>' : '') . '
            </div>
            
            <!-- Summary Cards -->
            <div class="clearfix" style="margin-bottom: 15px;">
                <div class="summary-box">
                    <div class="summary-label">Total Transaksi</div>
                    <div class="summary-value">' . number_format($stats['total_transaksi'] ?? 0, 0) . '</div>
                </div>
                <div class="summary-box" style="background-color: #d4edda;">
                    <div class="summary-label">Total Pemasukan</div>
                    <div class="summary-value">Rp ' . number_format($stats['total_pemasukan'] ?? 0, 0) . '</div>
                </div>
                <div class="summary-box" style="background-color: #f8d7da;">
                    <div class="summary-label">Total Pengeluaran</div>
                    <div class="summary-value">Rp ' . number_format($stats['total_pengeluaran'] ?? 0, 0) . '</div>
                </div>
                <div class="summary-box" style="background-color: #cce5ff;">
                    <div class="summary-label">Jml Pemasukan</div>
                    <div class="summary-value">' . ($stats['jumlah_pemasukan'] ?? 0) . '</div>
                </div>
                <div class="summary-box" style="background-color: #fff3cd;">
                    <div class="summary-label">Jml Pengeluaran</div>
                    <div class="summary-value">' . ($stats['jumlah_pengeluaran'] ?? 0) . '</div>
                </div>
                <div class="summary-box" style="background-color: #e2d5f0;">
                    <div class="summary-label">Transaksi Hari Ini</div>
                    <div class="summary-value">' . ($stats['transaksi_hari_ini'] ?? 0) . '</div>
                </div>
                <div class="summary-box" style="background-color: #f0d5e2;">
                    <div class="summary-label">Saldo Kas Kecil</div>
                    <div class="summary-value">Rp ' . number_format($stats['saldo_terkini'] ?? 0, 0) . '</div>
                </div>
            </div>
            
            <!-- Tabel Data Kas Kecil -->
            <h3 style="margin-bottom: 5px; font-size: 10px;">Detail Transaksi Kas Kecil</h3>
            <table>
                <thead>
                    <tr>
                        <th width="3%">No</th>
                        <th width="6%">Tanggal</th>
                        <th width="8%">Kode</th>
                        <th width="4%">Tipe</th>
                        <th width="7%">Jumlah</th>
                        <th width="7%">Saldo</th>
                        <th width="10%">Akun Lawan</th>
                        <th width="8%">Karyawan</th>
                        <th width="10%">SPK/Proyek</th>
                        <th width="6%">No Bukti</th>
                        <th width="10%">Keterangan</th>
                        <th width="5%">Status</th>
                    </tr>
                </thead>
                <tbody>';
        
        if (empty($data)) {
            $html .= '
                    <tr>
                        <td colspan="12" class="text-center">Tidak ada data kas kecil</td>
                    </tr>';
        } else {
            $no = 1;
            foreach ($data as $item) {
                $tipeClass = $item['Tipe'] == 'Pemasukan' ? 'text-success' : 'text-danger';
                
                $statusClass = match($item['Status']) {
                    'Posted' => 'badge-success',
                    'Draft' => 'badge-warning',
                    'Dibatalkan' => 'badge-danger',
                    default => ''
                };
                
                $html .= '
                    <tr>
                        <td class="text-center">' . $no . '</td>
                        <td class="text-center">' . date('d/m/Y', strtotime($item['Tanggal'])) . '</td>
                        <td>' . $item['Kode Transaksi'] . '</td>
                        <td class="text-center ' . $tipeClass . '">' . $item['Tipe'] . '</td>
                        <td class="text-end">Rp ' . number_format($item['Jumlah'], 0) . '</td>
                        <td class="text-end">Rp ' . number_format($item['Saldo Setelah'], 0) . '</td>
                        <td><small>' . $item['Akun Lawan'] . '</small></td>
                        <td><small>' . ($item['Karyawan'] ?? '-') . '</small></td>
                        <td><small>' . ($item['SPK/Proyek'] ?? '-') . '</small></td>
                        <td><small>' . ($item['No Bukti'] ?? '-') . '</small></td>
                        <td><small>' . $item['Keterangan'] . '</small></td>
                        <td class="text-center"><span class="badge ' . $statusClass . '">' . $item['Status'] . '</span></td>
                    </tr>';
                $no++;
            }
        }
        
        $html .= '
                </tbody>
            </table>
            
            <div class="footer">
                <table style="width: 100%; border: none; margin-top: 5px;">
                    <tr>
                        <td style="border: none; text-align: left; width: 50%;">
                            <strong>Total Data:</strong> ' . count($data) . ' transaksi<br>
                            <strong>Total Pemasukan:</strong> Rp ' . number_format($stats['total_pemasukan'] ?? 0, 0) . '<br>
                            <strong>Total Pengeluaran:</strong> Rp ' . number_format($stats['total_pengeluaran'] ?? 0, 0) . '
                        </td>
                        <td style="border: none; text-align: right; width: 50%;">
                            Dicetak pada: ' . date('d/m/Y H:i:s') . '<br>
                            Oleh: ' . session()->get('name') . '
                        </td>
                    </tr>
                </table>
            </div>
            
        </body>
        </html>';
        
        return $html;
    }

    /**
     * Print transaksi kas kecil
     */
    public function print($id)
    {
        $transaksi = $this->kasKecilModel->getWithDetails($id);
        
        if (!$transaksi) {
            return redirect()->to('accounting/kas-bank/kas-kecil')
                ->with('error', 'Transaksi kas kecil tidak ditemukan');
        }
        
        $transaksi['jumlah_formatted'] = $this->formatRupiah($transaksi['jumlah']);
        $transaksi['terbilang'] = ucwords($this->terbilang($transaksi['jumlah'])) . ' Rupiah';
        
        $data['transaksi'] = $transaksi;
        $data['title'] = 'Print Transaksi Kas Kecil';
        
        return view('accounting/kas-bank/kas-kecil/print', $data);
    }

    /**
     * Bulk Post - Posting multiple transaksi
     */
    public function bulkPost()
    {
        $ids = $this->request->getPost('ids');
        
        if (empty($ids)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada data yang dipilih'
            ]);
        }
        
        $success = 0;
        $failed = 0;
        $errors = [];
        
        foreach ($ids as $id) {
            try {
                $transaksi = $this->kasKecilModel->find($id);
                
                if (!$transaksi || $transaksi['status'] !== 'Draft') {
                    $failed++;
                    $errors[] = "Transaksi {$id} tidak dapat diposting (bukan Draft)";
                    continue;
                }
                
                // Proses posting (sama seperti method post)
                $coaLawan = $this->coaModel->find($transaksi['coa_lawan_id']);
                
                if (!$coaLawan) {
                    throw new \Exception('Akun lawan tidak ditemukan');
                }
                
                $coaKasKecil = $this->coaModel->where('kode_akun', '1-1101')->first();
                if (!$coaKasKecil) {
                    $coaKasKecil = $this->coaModel->like('nama_akun', 'Kas Kecil')
                        ->where('is_header', 0)
                        ->first();
                }
                
                if (!$coaKasKecil) {
                    throw new \Exception('Akun Kas Kecil tidak ditemukan');
                }
                
                $this->db->transBegin();
                
                $jurnalData = [
                    'tanggal' => $transaksi['tanggal'],
                    'keterangan' => $transaksi['keterangan'] . ' (' . $transaksi['kode_transaksi'] . ')',
                    'referensi' => $transaksi['kode_transaksi'],
                    'tipe_referensi' => 'kas_kecil',
                    'status' => 'posted',
                    'posted_by' => session()->get('user_id'),
                    'posted_at' => date('Y-m-d H:i:s'),
                    'total_debit' => $transaksi['jumlah'],
                    'total_kredit' => $transaksi['jumlah'],
                    'created_by' => session()->get('user_id')
                ];
                
                $jurnalId = $this->jurnalModel->insert($jurnalData);
                if (!$jurnalId) {
                    throw new \Exception('Gagal membuat jurnal');
                }
                
                $detailData = [];
                
                if ($transaksi['tipe'] === 'Pemasukan') {
                    $detailData[] = [
                        'jurnal_id' => $jurnalId,
                        'coa_id' => $coaKasKecil['id'],
                        'kode_akun' => $coaKasKecil['kode_akun'],
                        'nama_akun' => $coaKasKecil['nama_akun'],
                        'debit' => $transaksi['jumlah'],
                        'kredit' => 0,
                        'keterangan' => 'Pengisian kembali kas kecil'
                    ];
                    
                    $detailData[] = [
                        'jurnal_id' => $jurnalId,
                        'coa_id' => $coaLawan['id'],
                        'kode_akun' => $coaLawan['kode_akun'],
                        'nama_akun' => $coaLawan['nama_akun'],
                        'debit' => 0,
                        'kredit' => $transaksi['jumlah'],
                        'keterangan' => 'Pengisian kembali kas kecil'
                    ];
                } else {
                    $detailData[] = [
                        'jurnal_id' => $jurnalId,
                        'coa_id' => $coaLawan['id'],
                        'kode_akun' => $coaLawan['kode_akun'],
                        'nama_akun' => $coaLawan['nama_akun'],
                        'debit' => $transaksi['jumlah'],
                        'kredit' => 0,
                        'keterangan' => 'Pengeluaran kas kecil'
                    ];
                    
                    $detailData[] = [
                        'jurnal_id' => $jurnalId,
                        'coa_id' => $coaKasKecil['id'],
                        'kode_akun' => $coaKasKecil['kode_akun'],
                        'nama_akun' => $coaKasKecil['nama_akun'],
                        'debit' => 0,
                        'kredit' => $transaksi['jumlah'],
                        'keterangan' => 'Pengeluaran kas kecil'
                    ];
                }
                
                foreach ($detailData as $detail) {
                    if (!$this->jurnalDetailModel->insert($detail)) {
                        throw new \Exception('Gagal menyimpan detail jurnal');
                    }
                }
                
                $this->kasKecilModel->postTransaksi($id, $jurnalId);
                
                $this->db->transCommit();
                $success++;
                
            } catch (\Exception $e) {
                $this->db->transRollback();
                $failed++;
                $errors[] = "Transaksi {$transaksi['kode_transaksi']}: " . $e->getMessage();
            }
        }
        
        $message = "Berhasil memposting {$success} transaksi";
        if ($failed > 0) {
            $message .= ", {$failed} gagal";
        }
        
        return $this->response->setJSON([
            'success' => true,
            'message' => $message,
            'errors' => $errors
        ]);
    }

    /**
     * Bulk Delete - Hapus multiple transaksi
     */
    public function bulkDelete()
    {
        $ids = $this->request->getPost('ids');
        
        if (empty($ids)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada data yang dipilih'
            ]);
        }
        
        $success = 0;
        $failed = 0;
        $errors = [];
        
        foreach ($ids as $id) {
            try {
                $transaksi = $this->kasKecilModel->find($id);
                
                if (!$transaksi) {
                    $failed++;
                    $errors[] = "Transaksi ID {$id} tidak ditemukan";
                    continue;
                }
                
                if ($transaksi['status'] !== 'Draft') {
                    $failed++;
                    $errors[] = "Transaksi {$transaksi['kode_transaksi']} tidak dapat dihapus (bukan Draft)";
                    continue;
                }
                
                $this->db->transBegin();
                
                if (!empty($transaksi['lampiran']) && file_exists(FCPATH . $transaksi['lampiran'])) {
                    unlink(FCPATH . $transaksi['lampiran']);
                }
                
                $this->kasKecilModel->delete($id);
                
                $this->db->transCommit();
                $success++;
                
            } catch (\Exception $e) {
                $this->db->transRollback();
                $failed++;
                $errors[] = "Transaksi ID {$id}: " . $e->getMessage();
            }
        }
        
        $message = "Berhasil menghapus {$success} transaksi";
        if ($failed > 0) {
            $message .= ", {$failed} gagal";
        }
        
        return $this->response->setJSON([
            'success' => true,
            'message' => $message,
            'errors' => $errors
        ]);
    }

    /**
     * Fungsi untuk membersihkan format currency
     */
    private function cleanCurrency($value)
    {
        if (empty($value)) return 0;
        
        $value = str_replace('Rp', '', $value);
        $value = str_replace('rp', '', $value);
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);
        $value = trim($value);
        
        return (float) $value;
    }

    /**
     * Fungsi untuk format currency ke Rupiah
     */
    private function formatRupiah($angka)
    {
        if (!$angka || $angka == 0) return '0';
        return number_format($angka, 0, ',', '.');
    }

    /**
     * Fungsi untuk mendapatkan teks terbilang
     */
    private function terbilang($angka)
    {
        $angka = abs($angka);
        $baca = array('', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas');
        
        if ($angka < 12) {
            return $baca[$angka];
        } elseif ($angka < 20) {
            return $baca[$angka - 10] . ' belas';
        } elseif ($angka < 100) {
            return $baca[floor($angka / 10)] . ' puluh ' . $baca[$angka % 10];
        } elseif ($angka < 200) {
            return 'seratus ' . $this->terbilang($angka - 100);
        } elseif ($angka < 1000) {
            return $baca[floor($angka / 100)] . ' ratus ' . $this->terbilang($angka % 100);
        } elseif ($angka < 2000) {
            return 'seribu ' . $this->terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            return $this->terbilang(floor($angka / 1000)) . ' ribu ' . $this->terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            return $this->terbilang(floor($angka / 1000000)) . ' juta ' . $this->terbilang($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            return $this->terbilang(floor($angka / 1000000000)) . ' miliar ' . $this->terbilang($angka % 1000000000);
        } else {
            return $this->terbilang(floor($angka / 1000000000000)) . ' triliun ' . $this->terbilang($angka % 1000000000000);
        }
    }
}