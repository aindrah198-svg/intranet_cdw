<?php
namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\Accounting\PengeluaranPribadiModel;
use App\Models\CoaModel;
use App\Models\JurnalModel;
use App\Models\JurnalDetailModel;
use App\Models\KaryawanModel;
use App\Models\Teknisi\SpkInstalasiModel;
use App\Models\Accounting\MutasiBankModel;
use App\Models\Accounting\KasKecilModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Dompdf\Dompdf;
use Dompdf\Options;

class PengeluaranPribadi extends BaseController
{
    protected $pengeluaranPribadiModel;
    protected $coaModel;
    protected $jurnalModel;
    protected $jurnalDetailModel;
    protected $karyawanModel;
    protected $spkModel;
    protected $mutasiBankModel;
    protected $kasKecilModel;
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        if (!$this->db->tableExists('pengeluaran_pribadi')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `pengeluaran_pribadi` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `tanggal` DATE NOT NULL,
                  `kode_pengeluaran` VARCHAR(50) NOT NULL,
                  `karyawan_id` INT DEFAULT NULL,
                  `nama_karyawan` VARCHAR(150) DEFAULT NULL,
                  `jenis` VARCHAR(100) DEFAULT NULL,
                  `jumlah` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                  `keterangan` TEXT DEFAULT NULL,
                  `tujuan_penggunaan` TEXT DEFAULT NULL,
                  `coa_id_debit` INT DEFAULT NULL,
                  `coa_debit_kode` VARCHAR(50) DEFAULT NULL,
                  `coa_debit_nama` VARCHAR(150) DEFAULT NULL,
                  `coa_id_kredit` INT DEFAULT NULL,
                  `coa_kredit_kode` VARCHAR(50) DEFAULT NULL,
                  `coa_kredit_nama` VARCHAR(150) DEFAULT NULL,
                  `spk_id` INT DEFAULT NULL,
                  `nomor_spk` VARCHAR(50) DEFAULT NULL,
                  `no_bukti` VARCHAR(100) DEFAULT NULL,
                  `lampiran` VARCHAR(255) DEFAULT NULL,
                  `status_hutang` VARCHAR(50) DEFAULT 'Belum Lunas',
                  `tanggal_jatuh_tempo` DATE DEFAULT NULL,
                  `tanggal_pelunasan` DATE DEFAULT NULL,
                  `mutasi_bank_id` INT DEFAULT NULL,
                  `kas_kecil_id` INT DEFAULT NULL,
                  `jumlah_dibayar` DECIMAL(15,2) DEFAULT 0.00,
                  `status` ENUM('Draft','Posted','Dibatalkan') DEFAULT 'Draft',
                  `posted_at` DATETIME DEFAULT NULL,
                  `jurnal_id` INT DEFAULT NULL,
                  `jurnal_pelunasan_id` INT DEFAULT NULL,
                  `nomor_jurnal` VARCHAR(50) DEFAULT NULL,
                  `catatan_internal` TEXT DEFAULT NULL,
                  `created_by` INT DEFAULT NULL,
                  `updated_by` INT DEFAULT NULL,
                  `created_at` DATETIME DEFAULT NULL,
                  `updated_at` DATETIME DEFAULT NULL,
                  `deleted_at` DATETIME DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }
        $this->pengeluaranPribadiModel = new PengeluaranPribadiModel();
        $this->coaModel = new CoaModel();
        $this->jurnalModel = new JurnalModel();
        $this->jurnalDetailModel = new JurnalDetailModel();
        $this->karyawanModel = new KaryawanModel();
        $this->spkModel = new SpkInstalasiModel();
        $this->mutasiBankModel = new MutasiBankModel();
        $this->kasKecilModel = new KasKecilModel();
        
        helper(['form', 'url', 'text', 'number']);
        
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Index / Daftar Pengeluaran Pribadi
     */
    public function index()
    {
        $data['title'] = 'Daftar Pengeluaran Pribadi';
        
        $filters = [
            'search' => $this->request->getGet('search'),
            'tanggal_mulai' => $this->request->getGet('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getGet('tanggal_selesai'),
            'jenis' => $this->request->getGet('jenis'),
            'status' => $this->request->getGet('status'),
            'status_hutang' => $this->request->getGet('status_hutang'),
            'karyawan_id' => $this->request->getGet('karyawan_id'),
            'spk_id' => $this->request->getGet('spk_id')
        ];
        
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 20;
        
        $result = $this->pengeluaranPribadiModel->getAllWithFilters($filters, $perPage, $page);
        
        $data['pengeluaran'] = $result['data'];
        $data['pager'] = $this->pengeluaranPribadiModel->pager;
        $data['total'] = $result['total'];
        $data['perPage'] = $perPage;
        $data['currentPage'] = $page;
        $data['totalPages'] = $result['total_pages'];
        
        $data['jenisOptions'] = [
            'Kasbon' => 'Kasbon',
            'Reimbursement' => 'Reimbursement',
            'Dana Talangan' => 'Dana Talangan',
            'Klaim Pribadi' => 'Klaim Pribadi',
            'Prive' => 'Prive',
            'Lainnya' => 'Lainnya'
        ];
        $data['statusOptions'] = ['Draft', 'Posted', 'Dibatalkan'];
        $data['statusHutangOptions'] = ['Belum Dibayar', 'Sebagian', 'Lunas'];
        $data['karyawanOptions'] = $this->pengeluaranPribadiModel->getKaryawanOptions();
        $data['spkOptions'] = $this->pengeluaranPribadiModel->getSpkOptions();
        
        $statsFilters = [];
        if (!empty($filters['tanggal_mulai'])) $statsFilters['tanggal_mulai'] = $filters['tanggal_mulai'];
        if (!empty($filters['tanggal_selesai'])) $statsFilters['tanggal_selesai'] = $filters['tanggal_selesai'];
        $data['stats'] = $this->pengeluaranPribadiModel->getStats(
            $filters['tanggal_mulai'] ?? null,
            $filters['tanggal_selesai'] ?? null
        );
        
        // Ringkasan hutang per karyawan
        $data['ringkasanHutang'] = $this->pengeluaranPribadiModel->getRingkasanHutangPerKaryawan('Belum Lunas');
        
        return view('accounting/kas-bank/pengeluaran-pribadi/index', $data);
    }

   /**
 * Form tambah pengeluaran pribadi
 */
public function create()
{
    $data['title'] = 'Tambah Pengeluaran Pribadi';
    $data['validation'] = \Config\Services::validation();
    
    $jenis = $this->request->getGet('jenis') ?? 'Kasbon';
    
    $data['jenis'] = $jenis;
    $data['coaDebitOptions'] = $this->pengeluaranPribadiModel->getCoaOptions($jenis, 'debit');
    $data['coaKreditOptions'] = $this->pengeluaranPribadiModel->getCoaOptions($jenis, 'kredit');
    $data['karyawanOptions'] = $this->pengeluaranPribadiModel->getKaryawanOptions();
    
    // Ambil SPK options dan konversi ke array jika perlu
    $spkOptions = $this->pengeluaranPribadiModel->getSpkOptions();
    // Pastikan dalam bentuk array
    if (!empty($spkOptions) && is_object($spkOptions[0] ?? null)) {
        $spkOptions = json_decode(json_encode($spkOptions), true);
    }
    $data['spkOptions'] = $spkOptions;
    
    $data['pengeluaran'] = [
        'tanggal' => date('Y-m-d'),
        'jenis' => $jenis,
        'jumlah' => 0,
        'status' => 'Draft',
        'status_hutang' => 'Belum Dibayar',
        'jumlah_dibayar' => 0,
        'tanggal_jatuh_tempo' => date('Y-m-d', strtotime('+30 days'))
    ];
    
    return view('accounting/kas-bank/pengeluaran-pribadi/create', $data);
}

    /**
     * Simpan pengeluaran pribadi baru
     */
    public function store()
    {
        // Validasi input
        $rules = [
            'tanggal' => 'required|valid_date',
            'karyawan_id' => 'required|is_natural_no_zero',
            'jenis' => 'required|in_list[Kasbon,Reimbursement,Dana Talangan,Klaim Pribadi,Prive,Lainnya]',
            'jumlah' => 'required|numeric|greater_than[0]',
            'keterangan' => 'required|min_length[3]',
            'tujuan_penggunaan' => 'permit_empty',
            'coa_id_debit' => 'required|is_natural_no_zero',
            'coa_id_kredit' => 'required|is_natural_no_zero',
            'spk_id' => 'permit_empty|is_natural_no_zero',
            'no_bukti' => 'permit_empty',
            'tanggal_jatuh_tempo' => 'permit_empty|valid_date'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $jumlah = $this->cleanCurrency($this->request->getPost('jumlah'));
        
        $data = [
            'tanggal' => $this->request->getPost('tanggal'),
            'karyawan_id' => $this->request->getPost('karyawan_id'),
            'jenis' => $this->request->getPost('jenis'),
            'jumlah' => $jumlah,
            'keterangan' => $this->request->getPost('keterangan'),
            'tujuan_penggunaan' => $this->request->getPost('tujuan_penggunaan'),
            'coa_id_debit' => $this->request->getPost('coa_id_debit'),
            'coa_id_kredit' => $this->request->getPost('coa_id_kredit'),
            'spk_id' => $this->request->getPost('spk_id') ?: null,
            'no_bukti' => $this->request->getPost('no_bukti'),
            'tanggal_jatuh_tempo' => $this->request->getPost('tanggal_jatuh_tempo') ?: null,
            'status' => 'Draft',
            'status_hutang' => 'Belum Dibayar',
            'jumlah_dibayar' => 0,
            'catatan_internal' => $this->request->getPost('catatan_internal')
        ];
        
        $lampiran = $this->request->getFile('lampiran');
        if ($lampiran && $lampiran->isValid() && !$lampiran->hasMoved()) {
            $newName = $lampiran->getRandomName();
            $lampiran->move('uploads/pengeluaran-pribadi', $newName);
            $data['lampiran'] = 'uploads/pengeluaran-pribadi/' . $newName;
        }
        
        try {
            $this->db->transBegin();
            
            $saved = $this->pengeluaranPribadiModel->save($data);
            
            if (!$saved) {
                throw new \Exception('Gagal menyimpan data: ' . json_encode($this->pengeluaranPribadiModel->errors()));
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/kas-bank/pengeluaran-pribadi')
                ->with('success', 'Pengeluaran pribadi berhasil disimpan sebagai Draft.');
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menyimpan pengeluaran pribadi: ' . $e->getMessage());
        }
    }

    /**
     * Detail pengeluaran pribadi
     */
    public function detail($id)
    {
        $data['title'] = 'Detail Pengeluaran Pribadi';
        
        $pengeluaran = $this->pengeluaranPribadiModel->getWithDetails($id);
        
        if (!$pengeluaran) {
            return redirect()->to('accounting/kas-bank/pengeluaran-pribadi')
                ->with('error', 'Pengeluaran pribadi tidak ditemukan');
        }
        
        // Format jumlah ke Rupiah
        $pengeluaran['jumlah_formatted'] = $this->formatRupiah($pengeluaran['jumlah']);
        $pengeluaran['jumlah_dibayar_formatted'] = $this->formatRupiah($pengeluaran['jumlah_dibayar'] ?? 0);
        $pengeluaran['sisa_hutang_formatted'] = $this->formatRupiah($pengeluaran['sisa_hutang'] ?? $pengeluaran['jumlah']);
        $pengeluaran['terbilang'] = ucwords($this->terbilang($pengeluaran['jumlah'])) . ' Rupiah';
        
        $data['pengeluaran'] = $pengeluaran;
        
        // Data untuk form pelunasan
        if ($pengeluaran['status_hutang'] !== 'Lunas' && $pengeluaran['status'] === 'Posted') {
            $data['bankOptions'] = $this->coaModel->where('is_header', 0)
                ->where('is_active', 1)
                ->like('kode_akun', '1-11', 'after')
                ->orderBy('kode_akun', 'ASC')
                ->findAll();
            
            $data['saldoKasKecil'] = $this->kasKecilModel->getSaldoKasKecil();
        }
        
        return view('accounting/kas-bank/pengeluaran-pribadi/detail', $data);
    }

    /**
     * Form edit pengeluaran pribadi
     */
    public function edit($id)
    {
        $data['title'] = 'Edit Pengeluaran Pribadi';
        
        $pengeluaran = $this->pengeluaranPribadiModel->find($id);
        
        if (!$pengeluaran) {
            return redirect()->to('accounting/kas-bank/pengeluaran-pribadi')
                ->with('error', 'Pengeluaran pribadi tidak ditemukan');
        }
        
        if ($pengeluaran['status'] !== 'Draft') {
            return redirect()->to('accounting/kas-bank/pengeluaran-pribadi')
                ->with('error', 'Hanya pengeluaran dengan status Draft yang dapat diedit');
        }
        
        $pengeluaran['jumlah_formatted'] = $this->formatRupiah($pengeluaran['jumlah']);
        
        $data['validation'] = \Config\Services::validation();
        $data['pengeluaran'] = $pengeluaran;
        $data['jenis'] = $pengeluaran['jenis'];
        $data['coaDebitOptions'] = $this->pengeluaranPribadiModel->getCoaOptions($pengeluaran['jenis'], 'debit');
        $data['coaKreditOptions'] = $this->pengeluaranPribadiModel->getCoaOptions($pengeluaran['jenis'], 'kredit');
        $data['karyawanOptions'] = $this->pengeluaranPribadiModel->getKaryawanOptions();
        $data['spkOptions'] = $this->pengeluaranPribadiModel->getSpkOptions();
        
        return view('accounting/kas-bank/pengeluaran-pribadi/edit', $data);
    }

    /**
     * Update pengeluaran pribadi
     */
    public function update($id)
    {
        $pengeluaran = $this->pengeluaranPribadiModel->find($id);
        
        if (!$pengeluaran) {
            return redirect()->to('accounting/kas-bank/pengeluaran-pribadi')
                ->with('error', 'Pengeluaran pribadi tidak ditemukan');
        }
        
        if ($pengeluaran['status'] !== 'Draft') {
            return redirect()->to('accounting/kas-bank/pengeluaran-pribadi')
                ->with('error', 'Hanya pengeluaran dengan status Draft yang dapat diedit');
        }
        
        $rules = [
            'tanggal' => 'required|valid_date',
            'karyawan_id' => 'required|is_natural_no_zero',
            'jenis' => 'required|in_list[Kasbon,Reimbursement,Dana Talangan,Klaim Pribadi,Prive,Lainnya]',
            'jumlah' => 'required|numeric|greater_than[0]',
            'keterangan' => 'required|min_length[3]',
            'tujuan_penggunaan' => 'permit_empty',
            'coa_id_debit' => 'required|is_natural_no_zero',
            'coa_id_kredit' => 'required|is_natural_no_zero',
            'spk_id' => 'permit_empty|is_natural_no_zero',
            'no_bukti' => 'permit_empty',
            'tanggal_jatuh_tempo' => 'permit_empty|valid_date'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $jumlah = $this->cleanCurrency($this->request->getPost('jumlah'));
        
        $data = [
            'id' => $id,
            'tanggal' => $this->request->getPost('tanggal'),
            'karyawan_id' => $this->request->getPost('karyawan_id'),
            'jenis' => $this->request->getPost('jenis'),
            'jumlah' => $jumlah,
            'keterangan' => $this->request->getPost('keterangan'),
            'tujuan_penggunaan' => $this->request->getPost('tujuan_penggunaan'),
            'coa_id_debit' => $this->request->getPost('coa_id_debit'),
            'coa_id_kredit' => $this->request->getPost('coa_id_kredit'),
            'spk_id' => $this->request->getPost('spk_id') ?: null,
            'no_bukti' => $this->request->getPost('no_bukti'),
            'tanggal_jatuh_tempo' => $this->request->getPost('tanggal_jatuh_tempo') ?: null,
            'catatan_internal' => $this->request->getPost('catatan_internal')
        ];
        
        $lampiran = $this->request->getFile('lampiran');
        if ($lampiran && $lampiran->isValid() && !$lampiran->hasMoved()) {
            if (!empty($pengeluaran['lampiran']) && file_exists(FCPATH . $pengeluaran['lampiran'])) {
                unlink(FCPATH . $pengeluaran['lampiran']);
            }
            
            $newName = $lampiran->getRandomName();
            $lampiran->move('uploads/pengeluaran-pribadi', $newName);
            $data['lampiran'] = 'uploads/pengeluaran-pribadi/' . $newName;
        }
        
        try {
            $this->db->transBegin();
            
            $updated = $this->pengeluaranPribadiModel->save($data);
            
            if (!$updated) {
                throw new \Exception('Gagal mengupdate data: ' . json_encode($this->pengeluaranPribadiModel->errors()));
            }
            
            $this->db->transCommit();
            
            return redirect()->to('accounting/kas-bank/pengeluaran-pribadi')
                ->with('success', 'Pengeluaran pribadi berhasil diupdate.');
                
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Gagal mengupdate pengeluaran pribadi: ' . $e->getMessage());
        }
    }

    /**
     * Hapus pengeluaran pribadi
     */
    public function delete($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $pengeluaran = $this->pengeluaranPribadiModel->find($id);
        
        if (!$pengeluaran) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Pengeluaran pribadi tidak ditemukan'
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/pengeluaran-pribadi')
                    ->with('error', 'Pengeluaran pribadi tidak ditemukan');
            }
        }
        
        if ($pengeluaran['status'] !== 'Draft') {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Hanya pengeluaran dengan status Draft yang dapat dihapus'
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/pengeluaran-pribadi')
                    ->with('error', 'Hanya pengeluaran dengan status Draft yang dapat dihapus');
            }
        }
        
        try {
            $this->db->transBegin();
            
            if (!empty($pengeluaran['lampiran']) && file_exists(FCPATH . $pengeluaran['lampiran'])) {
                unlink(FCPATH . $pengeluaran['lampiran']);
            }
            
            $deleted = $this->pengeluaranPribadiModel->delete($id);
            
            if (!$deleted) {
                throw new \Exception('Gagal menghapus data');
            }
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Pengeluaran pribadi berhasil dihapus',
                    'redirect' => site_url('accounting/kas-bank/pengeluaran-pribadi')
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/pengeluaran-pribadi')
                    ->with('success', 'Pengeluaran pribadi berhasil dihapus');
            }
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus pengeluaran pribadi: ' . $e->getMessage()
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal menghapus pengeluaran pribadi: ' . $e->getMessage());
            }
        }
    }

    /**
     * Posting pengeluaran pribadi ke jurnal
     */
    public function post($id)
    {
        $pengeluaran = $this->pengeluaranPribadiModel->find($id);
        
        if (!$pengeluaran) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pengeluaran pribadi tidak ditemukan'
            ]);
        }
        
        if ($pengeluaran['status'] !== 'Draft') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Hanya pengeluaran dengan status Draft yang bisa diposting'
            ]);
        }
        
        try {
            $this->db->transBegin();
            
            // Ambil data COA debit dan kredit
            $coaDebit = $this->coaModel->find($pengeluaran['coa_id_debit']);
            $coaKredit = $this->coaModel->find($pengeluaran['coa_id_kredit']);
            
            if (!$coaDebit || !$coaKredit) {
                throw new \Exception('Akun debit atau kredit tidak ditemukan');
            }
            
            // Nonaktifkan validasi sementara untuk jurnal
            $this->jurnalModel->skipValidation(true);
            
            // Buat jurnal
            $jurnalData = [
                'tanggal' => $pengeluaran['tanggal'],
                'keterangan' => $pengeluaran['keterangan'] . ' (' . $pengeluaran['kode_pengeluaran'] . ') - ' . $pengeluaran['jenis'],
                'referensi' => $pengeluaran['kode_pengeluaran'],
                'tipe_referensi' => 'pengeluaran_pribadi',
                'status' => 'posted',
                'posted_by' => session()->get('user_id'),
                'posted_at' => date('Y-m-d H:i:s'),
                'total_debit' => $pengeluaran['jumlah'],
                'total_kredit' => $pengeluaran['jumlah'],
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
            
            // Buat detail jurnal
            $detailData = [
                [
                    'jurnal_id' => $jurnalId,
                    'coa_id' => $coaDebit['id'],
                    'kode_akun' => $coaDebit['kode_akun'],
                    'nama_akun' => $coaDebit['nama_akun'],
                    'debit' => $pengeluaran['jumlah'],
                    'kredit' => 0,
                    'keterangan' => 'Pengeluaran pribadi: ' . $pengeluaran['jenis'] . ' - ' . $pengeluaran['keterangan']
                ],
                [
                    'jurnal_id' => $jurnalId,
                    'coa_id' => $coaKredit['id'],
                    'kode_akun' => $coaKredit['kode_akun'],
                    'nama_akun' => $coaKredit['nama_akun'],
                    'debit' => 0,
                    'kredit' => $pengeluaran['jumlah'],
                    'keterangan' => 'Pengeluaran pribadi: ' . $pengeluaran['jenis'] . ' - ' . $pengeluaran['keterangan']
                ]
            ];
            
            foreach ($detailData as $detail) {
                if (!$this->jurnalDetailModel->insert($detail)) {
                    $errors = $this->jurnalDetailModel->errors();
                    log_message('error', 'Gagal insert detail jurnal: ' . json_encode($errors));
                    throw new \Exception('Gagal menyimpan detail jurnal: ' . json_encode($errors));
                }
            }
            
            // Update status pengeluaran menjadi Posted
            $this->pengeluaranPribadiModel->postPengeluaran($id, $jurnalId);
            
            $this->db->transCommit();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pengeluaran pribadi berhasil diposting ke jurnal',
                'redirect' => site_url('accounting/kas-bank/pengeluaran-pribadi/detail/' . $id)
            ]);
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error posting pengeluaran pribadi: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memposting pengeluaran pribadi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Batalkan pengeluaran pribadi
     */
    public function batalkan($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $pengeluaran = $this->pengeluaranPribadiModel->find($id);
        
        if (!$pengeluaran) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Pengeluaran pribadi tidak ditemukan'
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/pengeluaran-pribadi')
                    ->with('error', 'Pengeluaran pribadi tidak ditemukan');
            }
        }
        
        try {
            $this->db->transBegin();
            
            if ($pengeluaran['status'] === 'Posted' && !empty($pengeluaran['jurnal_id'])) {
                $this->jurnalModel->update($pengeluaran['jurnal_id'], ['status' => 'void']);
            }
            
            $this->pengeluaranPribadiModel->batalkanPengeluaran($id);
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Pengeluaran pribadi berhasil dibatalkan',
                    'redirect' => site_url('accounting/kas-bank/pengeluaran-pribadi')
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/pengeluaran-pribadi')
                    ->with('success', 'Pengeluaran pribadi berhasil dibatalkan');
            }
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal membatalkan pengeluaran pribadi: ' . $e->getMessage()
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal membatalkan pengeluaran pribadi: ' . $e->getMessage());
            }
        }
    }

    /**
     * Form proses pelunasan
     */
    public function prosesPelunasan($id)
    {
        $data['title'] = 'Proses Pelunasan';
        
        $pengeluaran = $this->pengeluaranPribadiModel->getWithDetails($id);
        
        if (!$pengeluaran) {
            return redirect()->to('accounting/kas-bank/pengeluaran-pribadi')
                ->with('error', 'Pengeluaran pribadi tidak ditemukan');
        }
        
        if ($pengeluaran['status'] !== 'Posted') {
            return redirect()->to('accounting/kas-bank/pengeluaran-pribadi/detail/' . $id)
                ->with('error', 'Hanya pengeluaran dengan status Posted yang dapat dilunasi');
        }
        
        if ($pengeluaran['status_hutang'] === 'Lunas') {
            return redirect()->to('accounting/kas-bank/pengeluaran-pribadi/detail/' . $id)
                ->with('error', 'Pengeluaran ini sudah lunas');
        }
        
        $data['pengeluaran'] = $pengeluaran;
        $data['sisa_hutang'] = $pengeluaran['jumlah'] - ($pengeluaran['jumlah_dibayar'] ?? 0);
        $data['sisa_hutang_formatted'] = $this->formatRupiah($data['sisa_hutang']);
        
        // Data untuk metode pelunasan
        $data['bankOptions'] = $this->coaModel->where('is_header', 0)
            ->where('is_active', 1)
            ->like('kode_akun', '1-11', 'after')
            ->orderBy('kode_akun', 'ASC')
            ->findAll();
        
        $data['saldoKasKecil'] = $this->kasKecilModel->getSaldoKasKecil();
        $data['saldoKasKecil_formatted'] = $this->formatRupiah($data['saldoKasKecil']);
        
        $data['tanggal_pelunasan'] = date('Y-m-d');
        
        return view('accounting/kas-bank/pengeluaran-pribadi/proses-pelunasan', $data);
    }

    /**
     * Lunasi pengeluaran pribadi (full payment)
     */
    public function lunasi($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $pengeluaran = $this->pengeluaranPribadiModel->find($id);
        
        if (!$pengeluaran) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Pengeluaran pribadi tidak ditemukan'
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/pengeluaran-pribadi')
                    ->with('error', 'Pengeluaran pribadi tidak ditemukan');
            }
        }
        
        if ($pengeluaran['status'] !== 'Posted') {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Hanya pengeluaran dengan status Posted yang dapat dilunasi'
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Hanya pengeluaran dengan status Posted yang dapat dilunasi');
            }
        }
        
        if ($pengeluaran['status_hutang'] === 'Lunas') {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Pengeluaran ini sudah lunas'
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Pengeluaran ini sudah lunas');
            }
        }
        
        $metode = $this->request->getPost('metode');
        $tanggal = $this->request->getPost('tanggal') ?? date('Y-m-d');
        $keterangan = $this->request->getPost('keterangan') ?? 'Pelunasan ' . $pengeluaran['jenis'];
        
        $sisaHutang = $pengeluaran['jumlah'] - ($pengeluaran['jumlah_dibayar'] ?? 0);
        $jumlahBayar = $sisaHutang;
        
        try {
            $this->db->transBegin();
            
            $referensiId = null;
            $jurnalPelunasanId = null;
            
            if ($metode === 'bank') {
                $coaBankId = $this->request->getPost('coa_bank_id');
                
                if (!$coaBankId) {
                    throw new \Exception('Akun bank harus dipilih');
                }
                
                // Validasi saldo bank
                $saldoBank = $this->mutasiBankModel->getSaldoBank($coaBankId, $tanggal);
                if ($saldoBank < $jumlahBayar) {
                    throw new \Exception('Saldo bank tidak mencukupi. Saldo tersedia: Rp ' . $this->formatRupiah($saldoBank));
                }
                
                // Buat transaksi mutasi bank
                $mutasiData = [
                    'tanggal' => $tanggal,
                    'tipe' => 'Debit', // Uang keluar dari bank
                    'jumlah' => $jumlahBayar,
                    'keterangan' => 'Pelunasan ' . $pengeluaran['jenis'] . ' untuk ' . $pengeluaran['nama_karyawan'] . ' - ' . $pengeluaran['kode_pengeluaran'],
                    'coa_id_debit' => $pengeluaran['coa_id_debit'], // Akun yang sama dengan debit awal (beban/aset/piutang)
                    'coa_id_kredit' => $coaBankId, // Bank
                    'bank_asal' => 'Bank',
                    'no_referensi' => $pengeluaran['kode_pengeluaran'],
                    'status' => 'Draft'
                ];
                
                $mutasiBankId = $this->mutasiBankModel->insert($mutasiData);
                if (!$mutasiBankId) {
                    throw new \Exception('Gagal membuat transaksi mutasi bank');
                }
                
                $referensiId = $mutasiBankId;
                
                // Posting mutasi bank ke jurnal
                $coaBank = $this->coaModel->find($coaBankId);
                $coaDebit = $this->coaModel->find($pengeluaran['coa_id_debit']);
                
                $jurnalPelunasanData = [
                    'tanggal' => $tanggal,
                    'keterangan' => 'Pelunasan ' . $pengeluaran['jenis'] . ' - ' . $pengeluaran['keterangan'],
                    'referensi' => $pengeluaran['kode_pengeluaran'],
                    'tipe_referensi' => 'pelunasan_pengeluaran_pribadi',
                    'status' => 'posted',
                    'posted_by' => session()->get('user_id'),
                    'posted_at' => date('Y-m-d H:i:s'),
                    'total_debit' => $jumlahBayar,
                    'total_kredit' => $jumlahBayar,
                    'created_by' => session()->get('user_id')
                ];
                
                $jurnalPelunasanId = $this->jurnalModel->insert($jurnalPelunasanData);
                if (!$jurnalPelunasanId) {
                    throw new \Exception('Gagal membuat jurnal pelunasan');
                }
                
                $detailPelunasan = [
                    [
                        'jurnal_id' => $jurnalPelunasanId,
                        'coa_id' => $pengeluaran['coa_id_debit'],
                        'kode_akun' => $coaDebit['kode_akun'],
                        'nama_akun' => $coaDebit['nama_akun'],
                        'debit' => 0, // Kredit untuk mengurangi hutang
                        'kredit' => $jumlahBayar,
                        'keterangan' => 'Pelunasan ' . $pengeluaran['jenis']
                    ],
                    [
                        'jurnal_id' => $jurnalPelunasanId,
                        'coa_id' => $coaBankId,
                        'kode_akun' => $coaBank['kode_akun'],
                        'nama_akun' => $coaBank['nama_akun'],
                        'debit' => $jumlahBayar,
                        'kredit' => 0,
                        'keterangan' => 'Pembayaran pelunasan ' . $pengeluaran['jenis']
                    ]
                ];
                
                foreach ($detailPelunasan as $detail) {
                    if (!$this->jurnalDetailModel->insert($detail)) {
                        throw new \Exception('Gagal menyimpan detail jurnal pelunasan');
                    }
                }
                
                // Update status mutasi bank menjadi Posted
                $this->mutasiBankModel->postMutasi($mutasiBankId, $jurnalPelunasanId);
                
            } elseif ($metode === 'kas_kecil') {
                // Validasi saldo kas kecil
                $saldoKasKecil = $this->kasKecilModel->getSaldoKasKecil();
                if ($saldoKasKecil < $jumlahBayar) {
                    throw new \Exception('Saldo kas kecil tidak mencukupi. Saldo tersedia: Rp ' . $this->formatRupiah($saldoKasKecil));
                }
                
                // Buat transaksi kas kecil
                $kasKecilData = [
                    'tanggal' => $tanggal,
                    'tipe' => 'Pengeluaran',
                    'jumlah' => $jumlahBayar,
                    'keterangan' => 'Pelunasan ' . $pengeluaran['jenis'] . ' untuk ' . $pengeluaran['nama_karyawan'],
                    'coa_lawan_id' => $pengeluaran['coa_id_debit'], // Akun yang sama dengan debit awal
                    'karyawan_id' => $pengeluaran['karyawan_id'],
                    'no_bukti' => $pengeluaran['kode_pengeluaran'],
                    'status' => 'Draft'
                ];
                
                $kasKecilId = $this->kasKecilModel->insert($kasKecilData);
                if (!$kasKecilId) {
                    throw new \Exception('Gagal membuat transaksi kas kecil');
                }
                
                $referensiId = $kasKecilId;
                
                // Posting kas kecil ke jurnal
                $coaKasKecil = $this->coaModel->where('kode_akun', '1-1101')->first();
                if (!$coaKasKecil) {
                    $coaKasKecil = $this->coaModel->like('nama_akun', 'Kas Kecil')->where('is_header', 0)->first();
                }
                
                $coaLawan = $this->coaModel->find($pengeluaran['coa_id_debit']);
                
                $jurnalPelunasanData = [
                    'tanggal' => $tanggal,
                    'keterangan' => 'Pelunasan ' . $pengeluaran['jenis'] . ' via Kas Kecil',
                    'referensi' => $pengeluaran['kode_pengeluaran'],
                    'tipe_referensi' => 'pelunasan_pengeluaran_pribadi_kas_kecil',
                    'status' => 'posted',
                    'posted_by' => session()->get('user_id'),
                    'posted_at' => date('Y-m-d H:i:s'),
                    'total_debit' => $jumlahBayar,
                    'total_kredit' => $jumlahBayar,
                    'created_by' => session()->get('user_id')
                ];
                
                $jurnalPelunasanId = $this->jurnalModel->insert($jurnalPelunasanData);
                if (!$jurnalPelunasanId) {
                    throw new \Exception('Gagal membuat jurnal pelunasan');
                }
                
                $detailPelunasan = [
                    [
                        'jurnal_id' => $jurnalPelunasanId,
                        'coa_id' => $pengeluaran['coa_id_debit'],
                        'kode_akun' => $coaLawan['kode_akun'],
                        'nama_akun' => $coaLawan['nama_akun'],
                        'debit' => 0, // Kredit untuk mengurangi hutang
                        'kredit' => $jumlahBayar,
                        'keterangan' => 'Pelunasan ' . $pengeluaran['jenis']
                    ],
                    [
                        'jurnal_id' => $jurnalPelunasanId,
                        'coa_id' => $coaKasKecil['id'],
                        'kode_akun' => $coaKasKecil['kode_akun'],
                        'nama_akun' => $coaKasKecil['nama_akun'],
                        'debit' => $jumlahBayar,
                        'kredit' => 0,
                        'keterangan' => 'Pembayaran pelunasan via Kas Kecil'
                    ]
                ];
                
                foreach ($detailPelunasan as $detail) {
                    if (!$this->jurnalDetailModel->insert($detail)) {
                        throw new \Exception('Gagal menyimpan detail jurnal pelunasan');
                    }
                }
                
                // Posting kas kecil
                $this->kasKecilModel->postTransaksi($kasKecilId, $jurnalPelunasanId);
            }
            
            // Update pengeluaran pribadi
            $this->pengeluaranPribadiModel->prosesPelunasan($id, $jumlahBayar, $metode, $referensiId, $jurnalPelunasanId);
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Pelunasan berhasil diproses',
                    'redirect' => site_url('accounting/kas-bank/pengeluaran-pribadi/detail/' . $id)
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/pengeluaran-pribadi/detail/' . $id)
                    ->with('success', 'Pelunasan berhasil diproses');
            }
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memproses pelunasan: ' . $e->getMessage()
                ]);
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Gagal memproses pelunasan: ' . $e->getMessage());
            }
        }
    }

    /**
     * Lunasi sebagian pengeluaran pribadi
     */
    public function lunasiSebagian($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $pengeluaran = $this->pengeluaranPribadiModel->find($id);
        
        if (!$pengeluaran) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Pengeluaran pribadi tidak ditemukan'
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/pengeluaran-pribadi')
                    ->with('error', 'Pengeluaran pribadi tidak ditemukan');
            }
        }
        
        if ($pengeluaran['status'] !== 'Posted') {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Hanya pengeluaran dengan status Posted yang dapat dilunasi'
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Hanya pengeluaran dengan status Posted yang dapat dilunasi');
            }
        }
        
        if ($pengeluaran['status_hutang'] === 'Lunas') {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Pengeluaran ini sudah lunas'
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Pengeluaran ini sudah lunas');
            }
        }
        
        $rules = [
            'jumlah_bayar' => 'required|numeric|greater_than[0]',
            'metode' => 'required|in_list[bank,kas_kecil]',
            'tanggal' => 'required|valid_date'
        ];
        
        if (!$this->validate($rules)) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi gagal: ' . json_encode($this->validator->getErrors())
                ]);
            } else {
                return redirect()->back()->withInput()
                    ->with('errors', $this->validator->getErrors());
            }
        }
        
        $metode = $this->request->getPost('metode');
        $tanggal = $this->request->getPost('tanggal');
        $jumlahBayar = $this->cleanCurrency($this->request->getPost('jumlah_bayar'));
        $keterangan = $this->request->getPost('keterangan') ?? 'Pembayaran sebagian ' . $pengeluaran['jenis'];
        
        $sisaHutang = $pengeluaran['jumlah'] - ($pengeluaran['jumlah_dibayar'] ?? 0);
        
        if ($jumlahBayar > $sisaHutang) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Jumlah pembayaran melebihi sisa hutang. Sisa: Rp ' . $this->formatRupiah($sisaHutang)
                ]);
            } else {
                return redirect()->back()->withInput()
                    ->with('error', 'Jumlah pembayaran melebihi sisa hutang. Sisa: Rp ' . $this->formatRupiah($sisaHutang));
            }
        }
        
        try {
            $this->db->transBegin();
            
            $referensiId = null;
            $jurnalPelunasanId = null;
            
            if ($metode === 'bank') {
                $coaBankId = $this->request->getPost('coa_bank_id');
                
                if (!$coaBankId) {
                    throw new \Exception('Akun bank harus dipilih');
                }
                
                // Validasi saldo bank
                $saldoBank = $this->mutasiBankModel->getSaldoBank($coaBankId, $tanggal);
                if ($saldoBank < $jumlahBayar) {
                    throw new \Exception('Saldo bank tidak mencukupi. Saldo tersedia: Rp ' . $this->formatRupiah($saldoBank));
                }
                
                // Buat transaksi mutasi bank
                $mutasiData = [
                    'tanggal' => $tanggal,
                    'tipe' => 'Debit',
                    'jumlah' => $jumlahBayar,
                    'keterangan' => 'Pembayaran sebagian ' . $pengeluaran['jenis'] . ' - ' . $pengeluaran['kode_pengeluaran'],
                    'coa_id_debit' => $pengeluaran['coa_id_debit'],
                    'coa_id_kredit' => $coaBankId,
                    'bank_asal' => 'Bank',
                    'no_referensi' => $pengeluaran['kode_pengeluaran'],
                    'status' => 'Draft'
                ];
                
                $mutasiBankId = $this->mutasiBankModel->insert($mutasiData);
                if (!$mutasiBankId) {
                    throw new \Exception('Gagal membuat transaksi mutasi bank');
                }
                
                $referensiId = $mutasiBankId;
                
                // Posting mutasi bank ke jurnal
                $coaBank = $this->coaModel->find($coaBankId);
                $coaDebit = $this->coaModel->find($pengeluaran['coa_id_debit']);
                
                $jurnalPelunasanData = [
                    'tanggal' => $tanggal,
                    'keterangan' => 'Pembayaran sebagian ' . $pengeluaran['jenis'] . ' - ' . $pengeluaran['keterangan'],
                    'referensi' => $pengeluaran['kode_pengeluaran'],
                    'tipe_referensi' => 'pelunasan_sebagian_pengeluaran_pribadi',
                    'status' => 'posted',
                    'posted_by' => session()->get('user_id'),
                    'posted_at' => date('Y-m-d H:i:s'),
                    'total_debit' => $jumlahBayar,
                    'total_kredit' => $jumlahBayar,
                    'created_by' => session()->get('user_id')
                ];
                
                $jurnalPelunasanId = $this->jurnalModel->insert($jurnalPelunasanData);
                if (!$jurnalPelunasanId) {
                    throw new \Exception('Gagal membuat jurnal pelunasan');
                }
                
                $detailPelunasan = [
                    [
                        'jurnal_id' => $jurnalPelunasanId,
                        'coa_id' => $pengeluaran['coa_id_debit'],
                        'kode_akun' => $coaDebit['kode_akun'],
                        'nama_akun' => $coaDebit['nama_akun'],
                        'debit' => 0,
                        'kredit' => $jumlahBayar,
                        'keterangan' => 'Pembayaran sebagian ' . $pengeluaran['jenis']
                    ],
                    [
                        'jurnal_id' => $jurnalPelunasanId,
                        'coa_id' => $coaBankId,
                        'kode_akun' => $coaBank['kode_akun'],
                        'nama_akun' => $coaBank['nama_akun'],
                        'debit' => $jumlahBayar,
                        'kredit' => 0,
                        'keterangan' => 'Pembayaran sebagian via bank'
                    ]
                ];
                
                foreach ($detailPelunasan as $detail) {
                    if (!$this->jurnalDetailModel->insert($detail)) {
                        throw new \Exception('Gagal menyimpan detail jurnal pelunasan');
                    }
                }
                
                $this->mutasiBankModel->postMutasi($mutasiBankId, $jurnalPelunasanId);
                
            } elseif ($metode === 'kas_kecil') {
                // Validasi saldo kas kecil
                $saldoKasKecil = $this->kasKecilModel->getSaldoKasKecil();
                if ($saldoKasKecil < $jumlahBayar) {
                    throw new \Exception('Saldo kas kecil tidak mencukupi. Saldo tersedia: Rp ' . $this->formatRupiah($saldoKasKecil));
                }
                
                // Buat transaksi kas kecil
                $kasKecilData = [
                    'tanggal' => $tanggal,
                    'tipe' => 'Pengeluaran',
                    'jumlah' => $jumlahBayar,
                    'keterangan' => 'Pembayaran sebagian ' . $pengeluaran['jenis'] . ' untuk ' . $pengeluaran['nama_karyawan'],
                    'coa_lawan_id' => $pengeluaran['coa_id_debit'],
                    'karyawan_id' => $pengeluaran['karyawan_id'],
                    'no_bukti' => $pengeluaran['kode_pengeluaran'],
                    'status' => 'Draft'
                ];
                
                $kasKecilId = $this->kasKecilModel->insert($kasKecilData);
                if (!$kasKecilId) {
                    throw new \Exception('Gagal membuat transaksi kas kecil');
                }
                
                $referensiId = $kasKecilId;
                
                // Posting kas kecil ke jurnal
                $coaKasKecil = $this->coaModel->where('kode_akun', '1-1101')->first();
                if (!$coaKasKecil) {
                    $coaKasKecil = $this->coaModel->like('nama_akun', 'Kas Kecil')->where('is_header', 0)->first();
                }
                
                $coaLawan = $this->coaModel->find($pengeluaran['coa_id_debit']);
                
                $jurnalPelunasanData = [
                    'tanggal' => $tanggal,
                    'keterangan' => 'Pembayaran sebagian ' . $pengeluaran['jenis'] . ' via Kas Kecil',
                    'referensi' => $pengeluaran['kode_pengeluaran'],
                    'tipe_referensi' => 'pelunasan_sebagian_pengeluaran_pribadi_kas_kecil',
                    'status' => 'posted',
                    'posted_by' => session()->get('user_id'),
                    'posted_at' => date('Y-m-d H:i:s'),
                    'total_debit' => $jumlahBayar,
                    'total_kredit' => $jumlahBayar,
                    'created_by' => session()->get('user_id')
                ];
                
                $jurnalPelunasanId = $this->jurnalModel->insert($jurnalPelunasanData);
                if (!$jurnalPelunasanId) {
                    throw new \Exception('Gagal membuat jurnal pelunasan');
                }
                
                $detailPelunasan = [
                    [
                        'jurnal_id' => $jurnalPelunasanId,
                        'coa_id' => $pengeluaran['coa_id_debit'],
                        'kode_akun' => $coaLawan['kode_akun'],
                        'nama_akun' => $coaLawan['nama_akun'],
                        'debit' => 0,
                        'kredit' => $jumlahBayar,
                        'keterangan' => 'Pembayaran sebagian ' . $pengeluaran['jenis']
                    ],
                    [
                        'jurnal_id' => $jurnalPelunasanId,
                        'coa_id' => $coaKasKecil['id'],
                        'kode_akun' => $coaKasKecil['kode_akun'],
                        'nama_akun' => $coaKasKecil['nama_akun'],
                        'debit' => $jumlahBayar,
                        'kredit' => 0,
                        'keterangan' => 'Pembayaran sebagian via Kas Kecil'
                    ]
                ];
                
                foreach ($detailPelunasan as $detail) {
                    if (!$this->jurnalDetailModel->insert($detail)) {
                        throw new \Exception('Gagal menyimpan detail jurnal pelunasan');
                    }
                }
                
                $this->kasKecilModel->postTransaksi($kasKecilId, $jurnalPelunasanId);
            }
            
            // Update pengeluaran pribadi
            $this->pengeluaranPribadiModel->prosesPelunasan($id, $jumlahBayar, $metode, $referensiId, $jurnalPelunasanId);
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Pembayaran sebagian berhasil diproses',
                    'redirect' => site_url('accounting/kas-bank/pengeluaran-pribadi/detail/' . $id)
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/pengeluaran-pribadi/detail/' . $id)
                    ->with('success', 'Pembayaran sebagian berhasil diproses');
            }
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()
                ]);
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
            }
        }
    }

    /**
     * AJAX: Get COA debit options
     */
    public function ajaxGetCoaDebit()
    {
        $jenis = $this->request->getGet('jenis');
        
        $coa = $this->pengeluaranPribadiModel->getCoaOptions($jenis, 'debit');
        
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
     * AJAX: Get COA kredit options
     */
    public function ajaxGetCoaKredit()
    {
        $jenis = $this->request->getGet('jenis');
        
        $coa = $this->pengeluaranPribadiModel->getCoaOptions($jenis, 'kredit');
        
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
        $karyawan = $this->pengeluaranPribadiModel->getKaryawanOptions();
        
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
        $spk = $this->pengeluaranPribadiModel->getSpkOptions();
        
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
     * AJAX: Get data pengeluaran untuk pelunasan
     */
    public function ajaxGetDataPengeluaran($id)
    {
        $pengeluaran = $this->pengeluaranPribadiModel->find($id);
        
        if (!$pengeluaran) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pengeluaran tidak ditemukan'
            ]);
        }
        
        $sisaHutang = $pengeluaran['jumlah'] - ($pengeluaran['jumlah_dibayar'] ?? 0);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'id' => $pengeluaran['id'],
                'kode_pengeluaran' => $pengeluaran['kode_pengeluaran'],
                'jenis' => $pengeluaran['jenis'],
                'jumlah' => $pengeluaran['jumlah'],
                'jumlah_formatted' => $this->formatRupiah($pengeluaran['jumlah']),
                'jumlah_dibayar' => $pengeluaran['jumlah_dibayar'] ?? 0,
                'jumlah_dibayar_formatted' => $this->formatRupiah($pengeluaran['jumlah_dibayar'] ?? 0),
                'sisa_hutang' => $sisaHutang,
                'sisa_hutang_formatted' => $this->formatRupiah($sisaHutang),
                'karyawan_id' => $pengeluaran['karyawan_id'],
                'nama_karyawan' => $pengeluaran['nama_karyawan'],
                'status_hutang' => $pengeluaran['status_hutang']
            ]
        ]);
    }

    /**
     * AJAX: Validate pelunasan
     */
    public function ajaxValidatePelunasan()
    {
        $id = $this->request->getPost('id');
        $jumlahBayar = $this->cleanCurrency($this->request->getPost('jumlah_bayar'));
        
        $validation = $this->pengeluaranPribadiModel->validatePelunasanSebelum($id, $jumlahBayar);
        
        return $this->response->setJSON($validation);
    }

    /**
     * AJAX: Get ringkasan hutang karyawan
     */
    public function ajaxGetRingkasanHutang($karyawanId)
    {
        $ringkasan = $this->pengeluaranPribadiModel->getRingkasanHutangPerKaryawan();
        
        $data = null;
        foreach ($ringkasan as $item) {
            if ($item['karyawan_id'] == $karyawanId) {
                $data = $item;
                break;
            }
        }
        
        if (!$data) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada data hutang untuk karyawan ini'
            ]);
        }
        
        $data['total_hutang_formatted'] = $this->formatRupiah($data['total_hutang']);
        $data['total_dibayar_formatted'] = $this->formatRupiah($data['total_dibayar']);
        $data['total_sisa_formatted'] = $this->formatRupiah($data['total_sisa']);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Laporan hutang karyawan
     */
    public function laporanHutangKaryawan()
    {
        $data['title'] = 'Laporan Hutang Karyawan';
        
        $status = $this->request->getGet('status') ?? 'Belum Lunas';
        
        $data['ringkasanHutang'] = $this->pengeluaranPribadiModel->getRingkasanHutangPerKaryawan($status);
        $data['ringkasanPerJenis'] = $this->pengeluaranPribadiModel->getRingkasanPerJenis();
        $data['hutangJatuhTempo'] = $this->pengeluaranPribadiModel->getHutangJatuhTempo();
        
        $data['status'] = $status;
        $data['totalHutang'] = array_sum(array_column($data['ringkasanHutang'], 'total_hutang'));
        $data['totalSisa'] = array_sum(array_column($data['ringkasanHutang'], 'total_sisa'));
        
        return view('accounting/kas-bank/pengeluaran-pribadi/laporan-hutang-karyawan', $data);
    }

    /**
     * Laporan rekap per karyawan
     */
    public function laporanRekapPerKaryawan()
    {
        $data['title'] = 'Rekap Pengeluaran Pribadi per Karyawan';
        
        $karyawanId = $this->request->getGet('karyawan_id');
        $tanggalMulai = $this->request->getGet('tanggal_mulai') ?? date('Y-m-01');
        $tanggalSelesai = $this->request->getGet('tanggal_selesai') ?? date('Y-m-d');
        
        $data['karyawanOptions'] = $this->pengeluaranPribadiModel->getKaryawanOptions();
        $data['tanggal_mulai'] = $tanggalMulai;
        $data['tanggal_selesai'] = $tanggalSelesai;
        $data['karyawan_id'] = $karyawanId;
        
        if ($karyawanId) {
            $filters = [
                'karyawan_id' => $karyawanId,
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'status' => 'Posted'
            ];
            
            $result = $this->pengeluaranPribadiModel->getAllWithFilters($filters, 100, 1);
            $data['pengeluaran'] = $result['data'];
            
            // Hitung total
            $totalJumlah = 0;
            $totalDibayar = 0;
            foreach ($data['pengeluaran'] as $item) {
                $totalJumlah += $item['jumlah'];
                $totalDibayar += $item['jumlah_dibayar'] ?? 0;
            }
            $data['totalJumlah'] = $totalJumlah;
            $data['totalDibayar'] = $totalDibayar;
            $data['totalSisa'] = $totalJumlah - $totalDibayar;
        }
        
        return view('accounting/kas-bank/pengeluaran-pribadi/laporan-rekap-per-karyawan', $data);
    }

    /**
     * Filter data
     */
    public function filter()
    {
        $filters = [
            'tanggal_mulai' => $this->request->getGet('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getGet('tanggal_selesai'),
            'jenis' => $this->request->getGet('jenis'),
            'status' => $this->request->getGet('status'),
            'status_hutang' => $this->request->getGet('status_hutang'),
            'karyawan_id' => $this->request->getGet('karyawan_id'),
            'spk_id' => $this->request->getGet('spk_id')
        ];
        
        session()->set('filter_pengeluaran_pribadi', $filters);
        
        return redirect()->to('accounting/kas-bank/pengeluaran-pribadi');
    }

    /**
     * Search data
     */
    public function search()
    {
        $search = $this->request->getGet('q');
        
        $filters = session()->get('filter_pengeluaran_pribadi') ?? [];
        $filters['search'] = $search;
        
        session()->set('filter_pengeluaran_pribadi', $filters);
        
        return redirect()->to('accounting/kas-bank/pengeluaran-pribadi');
    }

    /**
     * List status Draft
     */
    public function draft()
    {
        $filters = ['status' => 'Draft'];
        session()->set('filter_pengeluaran_pribadi', $filters);
        
        return redirect()->to('accounting/kas-bank/pengeluaran-pribadi');
    }

    /**
     * List status Posted
     */
    public function posted()
    {
        $filters = ['status' => 'Posted'];
        session()->set('filter_pengeluaran_pribadi', $filters);
        
        return redirect()->to('accounting/kas-bank/pengeluaran-pribadi');
    }

    /**
     * List status Dibatalkan
     */
    public function dibatalkan()
    {
        $filters = ['status' => 'Dibatalkan'];
        session()->set('filter_pengeluaran_pribadi', $filters);
        
        return redirect()->to('accounting/kas-bank/pengeluaran-pribadi');
    }

    /**
     * List hutang belum dibayar
     */
    public function hutangBelumDibayar()
    {
        $filters = ['status_hutang' => 'Belum Dibayar'];
        session()->set('filter_pengeluaran_pribadi', $filters);
        
        return redirect()->to('accounting/kas-bank/pengeluaran-pribadi');
    }

    /**
     * List hutang lunas
     */
    public function hutangLunas()
    {
        $filters = ['status_hutang' => 'Lunas'];
        session()->set('filter_pengeluaran_pribadi', $filters);
        
        return redirect()->to('accounting/kas-bank/pengeluaran-pribadi');
    }

    /**
     * List Kasbon
     */
    public function kasbon()
    {
        $filters = ['jenis' => 'Kasbon'];
        session()->set('filter_pengeluaran_pribadi', $filters);
        
        return redirect()->to('accounting/kas-bank/pengeluaran-pribadi');
    }

    /**
     * List Reimbursement
     */
    public function reimbursement()
    {
        $filters = ['jenis' => 'Reimbursement'];
        session()->set('filter_pengeluaran_pribadi', $filters);
        
        return redirect()->to('accounting/kas-bank/pengeluaran-pribadi');
    }

    /**
     * List Prive
     */
    public function prive()
    {
        $filters = ['jenis' => 'Prive'];
        session()->set('filter_pengeluaran_pribadi', $filters);
        
        return redirect()->to('accounting/kas-bank/pengeluaran-pribadi');
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
            'jenis' => $this->request->getGet('jenis'),
            'status' => $this->request->getGet('status'),
            'status_hutang' => $this->request->getGet('status_hutang'),
            'karyawan_id' => $this->request->getGet('karyawan_id')
        ];
        
        $data = $this->pengeluaranPribadiModel->getExportData($filters);
        $stats = $this->pengeluaranPribadiModel->getStats(
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
                ->setTitle("Laporan Pengeluaran Pribadi")
                ->setSubject("Laporan Pengeluaran Pribadi")
                ->setDescription("Laporan Pengeluaran Pribadi " . date('d-m-Y'));
            
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Data Pengeluaran Pribadi');
            
            // Header laporan
            $sheet->mergeCells('A1:P1');
            $sheet->setCellValue('A1', 'LAPORAN PENGELUARAN PRIBADI');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->mergeCells('A2:P2');
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
            
            $sheet->mergeCells('A3:P3');
            $sheet->setCellValue('A3', $periodeText);
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Baris kosong
            $startRow = 5;
            
            // Header tabel
            $headers = [
                'A' => 'No',
                'B' => 'Tanggal',
                'C' => 'Kode',
                'D' => 'Karyawan',
                'E' => 'Jenis',
                'F' => 'Jumlah',
                'G' => 'Dibayar',
                'H' => 'Sisa',
                'I' => 'Akun Debit',
                'J' => 'Akun Kredit',
                'K' => 'SPK/Proyek',
                'L' => 'No Bukti',
                'M' => 'Keterangan',
                'N' => 'Tujuan',
                'O' => 'Status Hutang',
                'P' => 'Status'
            ];
            
            foreach ($headers as $col => $header) {
                $sheet->setCellValue($col . $startRow, $header);
            }
            
            // Style header
            $headerRange = 'A' . $startRow . ':P' . $startRow;
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
                $sheet->setCellValue('C' . $row, $item['Kode Pengeluaran']);
                $sheet->setCellValue('D' . $row, $item['Karyawan']);
                $sheet->setCellValue('E' . $row, $item['Jenis']);
                $sheet->setCellValue('F' . $row, $item['Jumlah']);
                $sheet->setCellValue('G' . $row, $item['Jumlah Dibayar']);
                $sheet->setCellValue('H' . $row, $item['Sisa Hutang']);
                $sheet->setCellValue('I' . $row, $item['Akun Debit']);
                $sheet->setCellValue('J' . $row, $item['Akun Kredit']);
                $sheet->setCellValue('K' . $row, $item['SPK/Proyek']);
                $sheet->setCellValue('L' . $row, $item['No Bukti']);
                $sheet->setCellValue('M' . $row, $item['Keterangan']);
                $sheet->setCellValue('N' . $row, $item['Tujuan Penggunaan']);
                $sheet->setCellValue('O' . $row, $item['Status Hutang']);
                $sheet->setCellValue('P' . $row, $item['Status']);
                
                // Format currency
                foreach (range('F', 'H') as $col) {
                    $sheet->getStyle($col . $row)->getNumberFormat()
                        ->setFormatCode('"Rp" #,##0.00');
                }
                
                // Warna status hutang
                $hutangColor = match($item['Status Hutang']) {
                    'Lunas' => 'FF008000',
                    'Sebagian' => 'FFFFA500',
                    'Belum Dibayar' => 'FFFF0000',
                    default => 'FF000000'
                };
                $sheet->getStyle('O' . $row)->getFont()->getColor()->setARGB($hutangColor);
                
                // Warna status
                $statusColor = match($item['Status']) {
                    'Posted' => 'FF008000',
                    'Draft' => 'FFFFA500',
                    'Dibatalkan' => 'FFFF0000',
                    default => 'FF000000'
                };
                $sheet->getStyle('P' . $row)->getFont()->getColor()->setARGB($statusColor);
                
                $row++;
                $no++;
            }
            
            // Auto-size columns
            foreach (range('A', 'P') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Border untuk seluruh data
            $lastRow = $row - 1;
            if ($lastRow >= $startRow) {
                $dataRange = 'A' . $startRow . ':P' . $lastRow;
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
            $sheet2->setCellValue('A1', 'RINGKASAN PENGELUARAN PRIBADI');
            $sheet2->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            
            $sheet2->mergeCells('A2:C2');
            $sheet2->setCellValue('A2', $periodeText);
            
            // Statistik
            $sheet2->setCellValue('A4', 'STATISTIK');
            $sheet2->getStyle('A4')->getFont()->setBold(true);
            
            $statsData = [
                ['Total Transaksi', $stats['total_transaksi'] ?? 0],
                ['Total Nominal', $stats['total_nominal'] ?? 0],
                ['Total Dibayar', $stats['total_dibayar'] ?? 0],
                ['Total Sisa Hutang', $stats['total_sisa_hutang'] ?? 0],
                ['Jumlah Kasbon', $stats['jumlah_kasbon'] ?? 0],
                ['Jumlah Reimbursement', $stats['jumlah_reimbursement'] ?? 0],
                ['Jumlah Prive', $stats['jumlah_prive'] ?? 0],
                ['Belum Dibayar', $stats['belum_dibayar'] ?? 0],
                ['Sebagian', $stats['sebagian'] ?? 0],
                ['Lunas', $stats['lunas'] ?? 0]
            ];
            
            $rowStat = 5;
            foreach ($statsData as $stat) {
                $sheet2->setCellValue('A' . $rowStat, $stat[0]);
                $sheet2->setCellValue('B' . $rowStat, $stat[1]);
                
                if (strpos($stat[0], 'Total') === 0) {
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
            $filename = 'Pengeluaran_Pribadi_' . date('Ymd_His') . '.xlsx';
            
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
            
            $filename = 'Pengeluaran_Pribadi_' . date('Ymd_His') . '.pdf';
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
        if (!empty($filters['jenis'])) $filterInfo[] = 'Jenis: ' . $filters['jenis'];
        if (!empty($filters['status_hutang'])) $filterInfo[] = 'Status Hutang: ' . $filters['status_hutang'];
        $filterText = implode(' | ', $filterInfo);
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Laporan Pengeluaran Pribadi</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 8px;
                    line-height: 1.2;
                    margin: 10px;
                }
                h1 {
                    text-align: center;
                    font-size: 14px;
                    margin-bottom: 5px;
                }
                h2 {
                    text-align: center;
                    font-size: 11px;
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
                    font-size: 7px;
                }
                th {
                    background-color: #4F81BD;
                    color: white;
                    padding: 4px;
                    text-align: center;
                    font-weight: bold;
                    border: 1px solid #000;
                }
                td {
                    padding: 3px;
                    border: 1px solid #000;
                    vertical-align: top;
                }
                .text-end {
                    text-align: right;
                }
                .text-center {
                    text-align: center;
                }
                .badge {
                    padding: 1px 3px;
                    border-radius: 2px;
                    font-weight: bold;
                    font-size: 6px;
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
                .badge-info {
                    background-color: #17a2b8;
                    color: white;
                }
                .footer {
                    margin-top: 10px;
                    padding-top: 5px;
                    border-top: 1px solid #000;
                    font-size: 7px;
                }
                .summary-box {
                    float: left;
                    width: 9%;
                    margin: 0 0.5%;
                    padding: 3px;
                    background-color: #f8f9fa;
                    border: 1px solid #dee2e6;
                    border-radius: 2px;
                    text-align: center;
                }
                .summary-value {
                    font-size: 8px;
                    font-weight: bold;
                    margin-top: 2px;
                }
                .summary-label {
                    font-size: 6px;
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
                <h1>LAPORAN PENGELUARAN PRIBADI</h1>
                <h2>PT. CIPTA DUTA WACANA</h2>
                <div style="font-size: 8px;">Periode: ' . $periodeText . '</div>
                ' . (!empty($filterText) ? '<div style="font-size: 8px;">' . $filterText . '</div>' : '') . '
            </div>
            
            <!-- Summary Cards -->
            <div class="clearfix" style="margin-bottom: 10px;">
                <div class="summary-box">
                    <div class="summary-label">Total Transaksi</div>
                    <div class="summary-value">' . number_format($stats['total_transaksi'] ?? 0, 0) . '</div>
                </div>
                <div class="summary-box">
                    <div class="summary-label">Total Nominal</div>
                    <div class="summary-value">Rp ' . number_format($stats['total_nominal'] ?? 0, 0) . '</div>
                </div>
                <div class="summary-box">
                    <div class="summary-label">Total Dibayar</div>
                    <div class="summary-value">Rp ' . number_format($stats['total_dibayar'] ?? 0, 0) . '</div>
                </div>
                <div class="summary-box">
                    <div class="summary-label">Sisa Hutang</div>
                    <div class="summary-value">Rp ' . number_format($stats['total_sisa_hutang'] ?? 0, 0) . '</div>
                </div>
                <div class="summary-box">
                    <div class="summary-label">Kasbon</div>
                    <div class="summary-value">' . ($stats['jumlah_kasbon'] ?? 0) . '</div>
                </div>
                <div class="summary-box">
                    <div class="summary-label">Reimbursement</div>
                    <div class="summary-value">' . ($stats['jumlah_reimbursement'] ?? 0) . '</div>
                </div>
                <div class="summary-box">
                    <div class="summary-label">Prive</div>
                    <div class="summary-value">' . ($stats['jumlah_prive'] ?? 0) . '</div>
                </div>
                <div class="summary-box">
                    <div class="summary-label">Belum Dibayar</div>
                    <div class="summary-value">' . ($stats['belum_dibayar'] ?? 0) . '</div>
                </div>
                <div class="summary-box">
                    <div class="summary-label">Lunas</div>
                    <div class="summary-value">' . ($stats['lunas'] ?? 0) . '</div>
                </div>
                <div class="summary-box">
                    <div class="summary-label">Sebagian</div>
                    <div class="summary-value">' . ($stats['sebagian'] ?? 0) . '</div>
                </div>
            </div>
            
            <!-- Tabel Data -->
            <h3 style="margin-bottom: 3px; font-size: 9px;">Detail Pengeluaran Pribadi</h3>
            <table>
                <thead>
                    <tr>
                        <th width="2%">No</th>
                        <th width="5%">Tanggal</th>
                        <th width="7%">Kode</th>
                        <th width="8%">Karyawan</th>
                        <th width="5%">Jenis</th>
                        <th width="5%">Jumlah</th>
                        <th width="5%">Dibayar</th>
                        <th width="5%">Sisa</th>
                        <th width="8%">Akun Debit</th>
                        <th width="8%">Akun Kredit</th>
                        <th width="5%">No Bukti</th>
                        <th width="8%">Keterangan</th>
                        <th width="5%">Status Hutang</th>
                        <th width="4%">Status</th>
                    </tr>
                </thead>
                <tbody>';
        
        if (empty($data)) {
            $html .= '
                    <tr>
                        <td colspan="14" class="text-center">Tidak ada data pengeluaran pribadi</td>
                    </tr>';
        } else {
            $no = 1;
            foreach ($data as $item) {
                $hutangClass = match($item['Status Hutang']) {
                    'Lunas' => 'badge-success',
                    'Sebagian' => 'badge-warning',
                    'Belum Dibayar' => 'badge-danger',
                    default => ''
                };
                
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
                        <td>' . $item['Kode Pengeluaran'] . '</td>
                        <td><small>' . $item['Karyawan'] . '</small></td>
                        <td>' . $item['Jenis'] . '</td>
                        <td class="text-end">Rp ' . number_format($item['Jumlah'], 0) . '</td>
                        <td class="text-end">Rp ' . number_format($item['Jumlah Dibayar'], 0) . '</td>
                        <td class="text-end">Rp ' . number_format($item['Sisa Hutang'], 0) . '</td>
                        <td><small>' . $item['Akun Debit'] . '</small></td>
                        <td><small>' . $item['Akun Kredit'] . '</small></td>
                        <td><small>' . ($item['No Bukti'] ?? '-') . '</small></td>
                        <td><small>' . $item['Keterangan'] . '</small></td>
                        <td class="text-center"><span class="badge ' . $hutangClass . '">' . $item['Status Hutang'] . '</span></td>
                        <td class="text-center"><span class="badge ' . $statusClass . '">' . $item['Status'] . '</span></td>
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
                            <strong>Total Data:</strong> ' . count($data) . ' transaksi
                        </td>
                        <td style="border: none; text-align: right;">
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
     * Print pengeluaran pribadi
     */
    public function print($id)
    {
        $pengeluaran = $this->pengeluaranPribadiModel->getWithDetails($id);
        
        if (!$pengeluaran) {
            return redirect()->to('accounting/kas-bank/pengeluaran-pribadi')
                ->with('error', 'Pengeluaran pribadi tidak ditemukan');
        }
        
        $pengeluaran['jumlah_formatted'] = $this->formatRupiah($pengeluaran['jumlah']);
        $pengeluaran['jumlah_dibayar_formatted'] = $this->formatRupiah($pengeluaran['jumlah_dibayar'] ?? 0);
        $pengeluaran['sisa_hutang_formatted'] = $this->formatRupiah($pengeluaran['sisa_hutang'] ?? $pengeluaran['jumlah']);
        $pengeluaran['terbilang'] = ucwords($this->terbilang($pengeluaran['jumlah'])) . ' Rupiah';
        
        $data['pengeluaran'] = $pengeluaran;
        $data['title'] = 'Print Pengeluaran Pribadi';
        
        return view('accounting/kas-bank/pengeluaran-pribadi/print', $data);
    }

    /**
     * Bulk Post - Posting multiple pengeluaran
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
                $pengeluaran = $this->pengeluaranPribadiModel->find($id);
                
                if (!$pengeluaran || $pengeluaran['status'] !== 'Draft') {
                    $failed++;
                    $errors[] = "Pengeluaran {$id} tidak dapat diposting (bukan Draft)";
                    continue;
                }
                
                $this->db->transBegin();
                
                // Proses posting (sama seperti method post individual)
                $coaDebit = $this->coaModel->find($pengeluaran['coa_id_debit']);
                $coaKredit = $this->coaModel->find($pengeluaran['coa_id_kredit']);
                
                if (!$coaDebit || !$coaKredit) {
                    throw new \Exception('Akun debit atau kredit tidak ditemukan');
                }
                
                $jurnalData = [
                    'tanggal' => $pengeluaran['tanggal'],
                    'keterangan' => $pengeluaran['keterangan'] . ' (' . $pengeluaran['kode_pengeluaran'] . ')',
                    'referensi' => $pengeluaran['kode_pengeluaran'],
                    'tipe_referensi' => 'pengeluaran_pribadi',
                    'status' => 'posted',
                    'posted_by' => session()->get('user_id'),
                    'posted_at' => date('Y-m-d H:i:s'),
                    'total_debit' => $pengeluaran['jumlah'],
                    'total_kredit' => $pengeluaran['jumlah'],
                    'created_by' => session()->get('user_id')
                ];
                
                $jurnalId = $this->jurnalModel->insert($jurnalData);
                if (!$jurnalId) {
                    throw new \Exception('Gagal membuat jurnal');
                }
                
                $detailData = [
                    [
                        'jurnal_id' => $jurnalId,
                        'coa_id' => $coaDebit['id'],
                        'kode_akun' => $coaDebit['kode_akun'],
                        'nama_akun' => $coaDebit['nama_akun'],
                        'debit' => $pengeluaran['jumlah'],
                        'kredit' => 0,
                        'keterangan' => 'Pengeluaran pribadi'
                    ],
                    [
                        'jurnal_id' => $jurnalId,
                        'coa_id' => $coaKredit['id'],
                        'kode_akun' => $coaKredit['kode_akun'],
                        'nama_akun' => $coaKredit['nama_akun'],
                        'debit' => 0,
                        'kredit' => $pengeluaran['jumlah'],
                        'keterangan' => 'Pengeluaran pribadi'
                    ]
                ];
                
                foreach ($detailData as $detail) {
                    if (!$this->jurnalDetailModel->insert($detail)) {
                        throw new \Exception('Gagal menyimpan detail jurnal');
                    }
                }
                
                $this->pengeluaranPribadiModel->postPengeluaran($id, $jurnalId);
                
                $this->db->transCommit();
                $success++;
                
            } catch (\Exception $e) {
                $this->db->transRollback();
                $failed++;
                $errors[] = "Pengeluaran {$pengeluaran['kode_pengeluaran']}: " . $e->getMessage();
            }
        }
        
        $message = "Berhasil memposting {$success} pengeluaran";
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
     * Bulk Delete - Hapus multiple pengeluaran
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
                $pengeluaran = $this->pengeluaranPribadiModel->find($id);
                
                if (!$pengeluaran) {
                    $failed++;
                    $errors[] = "Pengeluaran ID {$id} tidak ditemukan";
                    continue;
                }
                
                if ($pengeluaran['status'] !== 'Draft') {
                    $failed++;
                    $errors[] = "Pengeluaran {$pengeluaran['kode_pengeluaran']} tidak dapat dihapus (bukan Draft)";
                    continue;
                }
                
                $this->db->transBegin();
                
                if (!empty($pengeluaran['lampiran']) && file_exists(FCPATH . $pengeluaran['lampiran'])) {
                    unlink(FCPATH . $pengeluaran['lampiran']);
                }
                
                $this->pengeluaranPribadiModel->delete($id);
                
                $this->db->transCommit();
                $success++;
                
            } catch (\Exception $e) {
                $this->db->transRollback();
                $failed++;
                $errors[] = "Pengeluaran ID {$id}: " . $e->getMessage();
            }
        }
        
        $message = "Berhasil menghapus {$success} pengeluaran";
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