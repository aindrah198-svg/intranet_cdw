<?php
namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\Accounting\TransferInternalModel;
use App\Models\CoaModel;
use App\Models\JurnalModel;
use App\Models\JurnalDetailModel;
use App\Models\BukuBesarModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Dompdf\Dompdf;
use Dompdf\Options;

class TransferInternal extends BaseController
{
    protected $transferInternalModel;
    protected $coaModel;
    protected $jurnalModel;
    protected $jurnalDetailModel;
    protected $bukuBesarModel;
    protected $db;

    public function __construct()
    {
        $this->transferInternalModel = new TransferInternalModel();
        $this->coaModel = new CoaModel();
        $this->jurnalModel = new JurnalModel();
        $this->jurnalDetailModel = new JurnalDetailModel();
        $this->bukuBesarModel = new BukuBesarModel();
        $this->db = \Config\Database::connect();
        
        helper(['form', 'url', 'text', 'number']);
        
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Index / Daftar Transfer Internal
     */
    public function index()
    {
        $data['title'] = 'Daftar Transfer Internal';
        
        $filters = [
            'search' => $this->request->getGet('search'),
            'tanggal_mulai' => $this->request->getGet('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getGet('tanggal_selesai'),
            'status' => $this->request->getGet('status'),
            'coa_id' => $this->request->getGet('coa_id')
        ];
        
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 20;
        
        $result = $this->transferInternalModel->getAllWithFilters($filters, $perPage, $page);
        
        $data['transfers'] = $result['data'];
        $data['pager'] = $this->transferInternalModel->pager;
        $data['total'] = $result['total'];
        $data['perPage'] = $perPage;
        $data['currentPage'] = $page;
        $data['totalPages'] = $result['total_pages'];
        
        $data['coaOptions'] = $this->transferInternalModel->getCoaKasBankOptions();
        $data['statusOptions'] = ['Draft', 'Posted', 'Dibatalkan'];
        
        $statsFilters = [];
        if (!empty($filters['tanggal_mulai'])) $statsFilters['tanggal_mulai'] = $filters['tanggal_mulai'];
        if (!empty($filters['tanggal_selesai'])) $statsFilters['tanggal_selesai'] = $filters['tanggal_selesai'];
        $data['stats'] = $this->transferInternalModel->getStats(
            $filters['tanggal_mulai'] ?? null,
            $filters['tanggal_selesai'] ?? null
        );
        
        return view('accounting/kas-bank/transfer-internal/index', $data);
    }
/**
 * Form tambah transfer internal
 */
public function create()
{
    $data['title'] = 'Tambah Transfer Internal';
    $data['validation'] = \Config\Services::validation();
    
    // Ambil daftar akun Kas/Bank
    $coaOptions = $this->transferInternalModel->getCoaKasBankOptions();
    
    // Hitung saldo untuk setiap akun
    $saldoAkun = [];
    foreach ($coaOptions as $akun) {
        $saldo = $this->transferInternalModel->getSaldoAkun($akun['id']);
        $saldoAkun[] = [
            'id' => $akun['id'],
            'kode_akun' => $akun['kode_akun'],
            'nama_akun' => $akun['nama_akun'],
            'saldo' => $saldo
        ];
    }
    
    $data['saldoAkun'] = $saldoAkun;
    $data['coaOptions'] = $coaOptions;
    
    $data['transfer'] = [
        'tanggal' => date('Y-m-d'),
        'jumlah' => 0,
        'status' => 'Draft'
    ];
    
    return view('accounting/kas-bank/transfer-internal/create', $data);
}
 /**
 * Simpan transfer internal baru
 */
public function store()
{
    // Validasi input
    $rules = [
        'tanggal' => 'required|valid_date',
        'jumlah' => 'required|numeric|greater_than[0]',
        'keterangan' => 'required|min_length[3]',
        'coa_id_sumber' => 'required|is_natural_no_zero',
        'coa_id_tujuan' => 'required|is_natural_no_zero'
    ];
    
    if (!$this->validate($rules)) {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }
    
    // Validasi manual: akun sumber dan tujuan harus berbeda
    $coaSumberId = $this->request->getPost('coa_id_sumber');
    $coaTujuanId = $this->request->getPost('coa_id_tujuan');
    
    if ($coaSumberId == $coaTujuanId) {
        return redirect()->back()->withInput()
            ->with('error', 'Akun sumber dan tujuan harus berbeda');
    }
    
    // Validasi saldo sumber cukup
    $jumlah = $this->cleanCurrency($this->request->getPost('jumlah'));
    $tanggal = $this->request->getPost('tanggal');
    
    $saldoValidation = $this->transferInternalModel->validateSaldoSumber($coaSumberId, $jumlah, $tanggal);
    if (!$saldoValidation['valid']) {
        return redirect()->back()->withInput()
            ->with('error', $saldoValidation['message']);
    }
    
    $data = [
        'tanggal' => $tanggal,
        'jumlah' => $jumlah,
        'keterangan' => $this->request->getPost('keterangan'),
        'coa_id_sumber' => $coaSumberId,
        'coa_id_tujuan' => $coaTujuanId,
        'bank_asal' => $this->request->getPost('bank_asal'),
        'bank_tujuan' => $this->request->getPost('bank_tujuan'),
        'no_referensi' => $this->request->getPost('no_referensi'),
        'status' => 'Draft'
    ];
    
    $lampiran = $this->request->getFile('lampiran');
    if ($lampiran && $lampiran->isValid() && !$lampiran->hasMoved()) {
        $newName = $lampiran->getRandomName();
        $lampiran->move('uploads/transfer-internal', $newName);
        $data['lampiran'] = 'uploads/transfer-internal/' . $newName;
    }
    
    try {
        $this->db->transBegin();
        
        $saved = $this->transferInternalModel->save($data);
        
        if (!$saved) {
            throw new \Exception('Gagal menyimpan data: ' . json_encode($this->transferInternalModel->errors()));
        }
        
        $this->db->transCommit();
        
        return redirect()->to('accounting/kas-bank/transfer-internal')
            ->with('success', 'Transfer internal berhasil disimpan sebagai Draft.');
        
    } catch (\Exception $e) {
        $this->db->transRollback();
        return redirect()->back()->withInput()
            ->with('error', 'Gagal menyimpan transfer internal: ' . $e->getMessage());
    }
}

    /**
     * Detail transfer internal
     */
    public function detail($id)
    {
        $data['title'] = 'Detail Transfer Internal';
        
        $transfer = $this->transferInternalModel->getWithDetails($id);
        
        if (!$transfer) {
            return redirect()->to('accounting/kas-bank/transfer-internal')
                ->with('error', 'Transfer internal tidak ditemukan');
        }
        
        // Format jumlah ke Rupiah
        $transfer['jumlah_formatted'] = $this->formatRupiah($transfer['jumlah']);
        $transfer['terbilang'] = ucwords($this->terbilang($transfer['jumlah'])) . ' Rupiah';
        
        $data['transfer'] = $transfer;
        
        return view('accounting/kas-bank/transfer-internal/detail', $data);
    }

    /**
     * Form edit transfer internal
     */
    public function edit($id)
    {
        $data['title'] = 'Edit Transfer Internal';
        
        $transfer = $this->transferInternalModel->find($id);
        
        if (!$transfer) {
            return redirect()->to('accounting/kas-bank/transfer-internal')
                ->with('error', 'Transfer internal tidak ditemukan');
        }
        
        if ($transfer['status'] !== 'Draft') {
            return redirect()->to('accounting/kas-bank/transfer-internal')
                ->with('error', 'Hanya transfer dengan status Draft yang dapat diedit');
        }
        
        $transfer['jumlah_formatted'] = $this->formatRupiah($transfer['jumlah']);
        
        $data['validation'] = \Config\Services::validation();
        $data['transfer'] = $transfer;
        $data['coaOptions'] = $this->transferInternalModel->getCoaKasBankOptions();
        
        return view('accounting/kas-bank/transfer-internal/edit', $data);
    }

 /**
 * Update transfer internal
 */
public function update($id)
{
    $transfer = $this->transferInternalModel->find($id);
    
    if (!$transfer) {
        return redirect()->to('accounting/kas-bank/transfer-internal')
            ->with('error', 'Transfer internal tidak ditemukan');
    }
    
    if ($transfer['status'] !== 'Draft') {
        return redirect()->to('accounting/kas-bank/transfer-internal')
            ->with('error', 'Hanya transfer dengan status Draft yang dapat diedit');
    }
    
    $rules = [
        'tanggal' => 'required|valid_date',
        'jumlah' => 'required|numeric|greater_than[0]',
        'keterangan' => 'required|min_length[3]',
        'coa_id_sumber' => 'required|is_natural_no_zero',
        'coa_id_tujuan' => 'required|is_natural_no_zero'
    ];
    
    if (!$this->validate($rules)) {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }
    
    // Validasi manual: akun sumber dan tujuan harus berbeda
    $coaSumberId = $this->request->getPost('coa_id_sumber');
    $coaTujuanId = $this->request->getPost('coa_id_tujuan');
    
    if ($coaSumberId == $coaTujuanId) {
        return redirect()->back()->withInput()
            ->with('error', 'Akun sumber dan tujuan harus berbeda');
    }
    
    $jumlah = $this->cleanCurrency($this->request->getPost('jumlah'));
    $tanggal = $this->request->getPost('tanggal');
    
    // Validasi saldo jika akun sumber berubah atau jumlah berubah
    if ($coaSumberId != $transfer['coa_id_sumber'] || $jumlah != $transfer['jumlah']) {
        $saldoValidation = $this->transferInternalModel->validateSaldoSumber($coaSumberId, $jumlah, $tanggal);
        if (!$saldoValidation['valid']) {
            return redirect()->back()->withInput()
                ->with('error', $saldoValidation['message']);
        }
    }
    
    $data = [
        'id' => $id,
        'tanggal' => $tanggal,
        'jumlah' => $jumlah,
        'keterangan' => $this->request->getPost('keterangan'),
        'coa_id_sumber' => $coaSumberId,
        'coa_id_tujuan' => $coaTujuanId,
        'bank_asal' => $this->request->getPost('bank_asal'),
        'bank_tujuan' => $this->request->getPost('bank_tujuan'),
        'no_referensi' => $this->request->getPost('no_referensi')
    ];
    
    $lampiran = $this->request->getFile('lampiran');
    if ($lampiran && $lampiran->isValid() && !$lampiran->hasMoved()) {
        if (!empty($transfer['lampiran']) && file_exists(FCPATH . $transfer['lampiran'])) {
            unlink(FCPATH . $transfer['lampiran']);
        }
        
        $newName = $lampiran->getRandomName();
        $lampiran->move('uploads/transfer-internal', $newName);
        $data['lampiran'] = 'uploads/transfer-internal/' . $newName;
    }
    
    try {
        $this->db->transBegin();
        
        $updated = $this->transferInternalModel->save($data);
        
        if (!$updated) {
            throw new \Exception('Gagal mengupdate data: ' . json_encode($this->transferInternalModel->errors()));
        }
        
        $this->db->transCommit();
        
        return redirect()->to('accounting/kas-bank/transfer-internal')
            ->with('success', 'Transfer internal berhasil diupdate.');
            
    } catch (\Exception $e) {
        $this->db->transRollback();
        return redirect()->back()->withInput()
            ->with('error', 'Gagal mengupdate transfer internal: ' . $e->getMessage());
    }
}

    /**
     * Hapus transfer internal
     */
    public function delete($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $transfer = $this->transferInternalModel->find($id);
        
        if (!$transfer) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Transfer internal tidak ditemukan'
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/transfer-internal')
                    ->with('error', 'Transfer internal tidak ditemukan');
            }
        }
        
        if ($transfer['status'] !== 'Draft') {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Hanya transfer dengan status Draft yang dapat dihapus'
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/transfer-internal')
                    ->with('error', 'Hanya transfer dengan status Draft yang dapat dihapus');
            }
        }
        
        try {
            $this->db->transBegin();
            
            if (!empty($transfer['lampiran']) && file_exists(FCPATH . $transfer['lampiran'])) {
                unlink(FCPATH . $transfer['lampiran']);
            }
            
            $deleted = $this->transferInternalModel->delete($id);
            
            if (!$deleted) {
                throw new \Exception('Gagal menghapus data');
            }
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Transfer internal berhasil dihapus',
                    'redirect' => site_url('accounting/kas-bank/transfer-internal')
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/transfer-internal')
                    ->with('success', 'Transfer internal berhasil dihapus');
            }
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus transfer internal: ' . $e->getMessage()
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal menghapus transfer internal: ' . $e->getMessage());
            }
        }
    }

    /**
     * Posting transfer internal ke jurnal
     */
    public function post($id)
    {
        $transfer = $this->transferInternalModel->find($id);
        
        if (!$transfer) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Transfer internal tidak ditemukan'
            ]);
        }
        
        if ($transfer['status'] !== 'Draft') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Hanya transfer dengan status Draft yang bisa diposting'
            ]);
        }
        
        try {
            $this->db->transBegin();
            
            // Ambil data akun sumber dan tujuan
            $coaSumber = $this->coaModel->find($transfer['coa_id_sumber']);
            $coaTujuan = $this->coaModel->find($transfer['coa_id_tujuan']);
            
            if (!$coaSumber || !$coaTujuan) {
                throw new \Exception('Akun sumber atau tujuan tidak ditemukan');
            }
            
            // Nonaktifkan validasi sementara untuk jurnal
            $this->jurnalModel->skipValidation(true);
            
            // Buat jurnal
            $jurnalData = [
                'tanggal' => $transfer['tanggal'],
                'keterangan' => $transfer['keterangan'] . ' (' . $transfer['kode_transfer'] . ')',
                'referensi' => $transfer['kode_transfer'],
                'tipe_referensi' => 'transfer_internal',
                'status' => 'posted',
                'posted_by' => session()->get('user_id'),
                'posted_at' => date('Y-m-d H:i:s'),
                'total_debit' => $transfer['jumlah'],
                'total_kredit' => $transfer['jumlah'],
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
                    'coa_id' => $coaTujuan['id'],
                    'kode_akun' => $coaTujuan['kode_akun'],
                    'nama_akun' => $coaTujuan['nama_akun'],
                    'debit' => $transfer['jumlah'],
                    'kredit' => 0,
                    'keterangan' => 'Transfer masuk dari: ' . $coaSumber['nama_akun']
                ],
                [
                    'jurnal_id' => $jurnalId,
                    'coa_id' => $coaSumber['id'],
                    'kode_akun' => $coaSumber['kode_akun'],
                    'nama_akun' => $coaSumber['nama_akun'],
                    'debit' => 0,
                    'kredit' => $transfer['jumlah'],
                    'keterangan' => 'Transfer keluar ke: ' . $coaTujuan['nama_akun']
                ]
            ];
            
            foreach ($detailData as $detail) {
                if (!$this->jurnalDetailModel->insert($detail)) {
                    $errors = $this->jurnalDetailModel->errors();
                    log_message('error', 'Gagal insert detail jurnal: ' . json_encode($errors));
                    throw new \Exception('Gagal menyimpan detail jurnal: ' . json_encode($errors));
                }
            }
            
            // Update status transfer menjadi Posted
            $this->transferInternalModel->postTransfer($id, $jurnalId);
            
            $this->db->transCommit();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Transfer internal berhasil diposting ke jurnal',
                'redirect' => site_url('accounting/kas-bank/transfer-internal/detail/' . $id)
            ]);
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error posting transfer internal: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memposting transfer internal: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Batalkan transfer internal
     */
    public function batalkan($id)
    {
        $isAjax = $this->request->isAJAX();
        
        $transfer = $this->transferInternalModel->find($id);
        
        if (!$transfer) {
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Transfer internal tidak ditemukan'
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/transfer-internal')
                    ->with('error', 'Transfer internal tidak ditemukan');
            }
        }
        
        try {
            $this->db->transBegin();
            
            if ($transfer['status'] === 'Posted' && !empty($transfer['jurnal_id'])) {
                $this->jurnalModel->update($transfer['jurnal_id'], ['status' => 'void']);
            }
            
            $this->transferInternalModel->batalkanTransfer($id);
            
            $this->db->transCommit();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Transfer internal berhasil dibatalkan',
                    'redirect' => site_url('accounting/kas-bank/transfer-internal')
                ]);
            } else {
                return redirect()->to('accounting/kas-bank/transfer-internal')
                    ->with('success', 'Transfer internal berhasil dibatalkan');
            }
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            if ($isAjax) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal membatalkan transfer internal: ' . $e->getMessage()
                ]);
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal membatalkan transfer internal: ' . $e->getMessage());
            }
        }
    }

    /**
     * AJAX: Get COA options untuk dropdown
     */
    public function ajaxGetCoa()
    {
        $coa = $this->transferInternalModel->getCoaKasBankOptions();
        
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
     * AJAX: Get saldo akun sumber
     */
    public function ajaxGetSaldoSumber()
    {
        $coaSumberId = $this->request->getGet('coa_sumber_id');
        $tanggal = $this->request->getGet('tanggal');
        
        if (!$coaSumberId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akun sumber tidak dipilih'
            ]);
        }
        
        $saldo = $this->transferInternalModel->getSaldoAkun($coaSumberId, $tanggal);
        
        return $this->response->setJSON([
            'success' => true,
            'saldo' => $this->formatRupiah($saldo),
            'saldo_raw' => $saldo
        ]);
    }

    /**
     * AJAX: Get informasi rekening
     */
    public function ajaxGetRekeningInfo()
    {
        $coaId = $this->request->getGet('coa_id');
        
        if (!$coaId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akun tidak dipilih'
            ]);
        }
        
        $info = $this->transferInternalModel->getRekeningInfo($coaId);
        
        if (!$info) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Informasi rekening tidak ditemukan'
            ]);
        }
        
        $info['saldo_formatted'] = $this->formatRupiah($info['saldo'] ?? 0);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $info
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
     * AJAX: Validate data sebelum simpan
     */
    public function ajaxValidate()
    {
        $coaSumberId = $this->request->getPost('coa_sumber_id');
        $jumlah = $this->cleanCurrency($this->request->getPost('jumlah'));
        $tanggal = $this->request->getPost('tanggal');
        
        $errors = [];
        
        if ($coaSumberId && $jumlah > 0) {
            $saldoValidation = $this->transferInternalModel->validateSaldoSumber($coaSumberId, $jumlah, $tanggal);
            if (!$saldoValidation['valid']) {
                $errors[] = $saldoValidation['message'];
            }
        }
        
        return $this->response->setJSON([
            'success' => empty($errors),
            'errors' => $errors
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
            'status' => $this->request->getGet('status'),
            'coa_id' => $this->request->getGet('coa_id')
        ];
        
        session()->set('filter_transfer_internal', $filters);
        
        return redirect()->to('accounting/kas-bank/transfer-internal');
    }

    /**
     * Search data
     */
    public function search()
    {
        $search = $this->request->getGet('q');
        
        $filters = session()->get('filter_transfer_internal') ?? [];
        $filters['search'] = $search;
        
        session()->set('filter_transfer_internal', $filters);
        
        return redirect()->to('accounting/kas-bank/transfer-internal');
    }

    /**
     * List status Draft
     */
    public function draft()
    {
        $filters = ['status' => 'Draft'];
        session()->set('filter_transfer_internal', $filters);
        
        return redirect()->to('accounting/kas-bank/transfer-internal');
    }

    /**
     * List status Posted
     */
    public function posted()
    {
        $filters = ['status' => 'Posted'];
        session()->set('filter_transfer_internal', $filters);
        
        return redirect()->to('accounting/kas-bank/transfer-internal');
    }

    /**
     * List status Dibatalkan
     */
    public function dibatalkan()
    {
        $filters = ['status' => 'Dibatalkan'];
        session()->set('filter_transfer_internal', $filters);
        
        return redirect()->to('accounting/kas-bank/transfer-internal');
    }

   /**
 * Export PDF Laporan Transfer Internal (Method Khusus untuk Route export-pdf)
 */
public function exportPdf()
{
    try {
        $filters = [
            'search' => $this->request->getGet('search'),
            'tanggal_mulai' => $this->request->getGet('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getGet('tanggal_selesai'),
            'status' => $this->request->getGet('status'),
            'coa_id' => $this->request->getGet('coa_id')
        ];
        
        // Ambil data dengan urutan ASC (terlama ke terbaru) untuk PDF
        $data = $this->transferInternalModel->getExportDataForPdf($filters);
        $stats = $this->transferInternalModel->getStats(
            $filters['tanggal_mulai'] ?? null,
            $filters['tanggal_selesai'] ?? null
        );
        
        // Hitung total nilai transfer
        $totalTransfer = array_sum(array_column($data, 'Jumlah'));
        
        // Format periode text
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
        if (!empty($filters['status'])) $filterInfo[] = 'Status: ' . $filters['status'];
        if (!empty($filters['coa_id'])) {
            $coa = $this->coaModel->find($filters['coa_id']);
            if ($coa) $filterInfo[] = 'Akun: ' . $coa['kode_akun'] . ' - ' . $coa['nama_akun'];
        }
        $filterText = implode(' | ', $filterInfo);
        
        $viewData = [
            'title' => 'Laporan Transfer Internal',
            'data' => $data,
            'stats' => $stats,
            'total_transfer' => $totalTransfer,
            'periode_text' => $periodeText,
            'filter_text' => $filterText,
            'user_name' => session()->get('name') ?? 'System',
            'date_generated' => date('d/m/Y H:i:s')
        ];
        
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml(view('accounting/kas-bank/transfer-internal/pdf_template', $viewData));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        $dompdf->stream('Laporan_Transfer_Internal_' . date('Ymd_His') . '.pdf', ['Attachment' => 1]);
        exit();
        
    } catch (\Exception $e) {
        log_message('error', 'Export PDF Transfer Internal Error: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
    }
}

    /**
     * Report / Laporan Transfer Internal
     */
    public function report()
    {
        $data['title'] = 'Laporan Transfer Internal';
        
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan');
        
        $data['ringkasanPeriode'] = $this->transferInternalModel->getRingkasanPeriode($tahun, $bulan);
        $data['totalPerAkun'] = $this->transferInternalModel->getTotalPerAkun(
            !empty($bulan) ? $tahun . '-' . $bulan . '-01' : $tahun . '-01-01',
            !empty($bulan) ? date('Y-m-t', strtotime($tahun . '-' . $bulan . '-01')) : $tahun . '-12-31'
        );
        
        $data['tahun'] = $tahun;
        $data['bulan'] = $bulan;
        
        return view('accounting/kas-bank/transfer-internal/report', $data);
    }

    /**
     * Bulk Post - Posting multiple transfer
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
                $transfer = $this->transferInternalModel->find($id);
                
                if (!$transfer || $transfer['status'] !== 'Draft') {
                    $failed++;
                    $errors[] = "Transfer {$id} tidak dapat diposting (bukan Draft)";
                    continue;
                }
                
                // Proses posting (sama seperti method post)
                $coaSumber = $this->coaModel->find($transfer['coa_id_sumber']);
                $coaTujuan = $this->coaModel->find($transfer['coa_id_tujuan']);
                
                $this->db->transBegin();
                
                $jurnalData = [
                    'tanggal' => $transfer['tanggal'],
                    'keterangan' => $transfer['keterangan'] . ' (' . $transfer['kode_transfer'] . ')',
                    'referensi' => $transfer['kode_transfer'],
                    'tipe_referensi' => 'transfer_internal',
                    'status' => 'posted',
                    'posted_by' => session()->get('user_id'),
                    'posted_at' => date('Y-m-d H:i:s'),
                    'total_debit' => $transfer['jumlah'],
                    'total_kredit' => $transfer['jumlah'],
                    'created_by' => session()->get('user_id')
                ];
                
                $jurnalId = $this->jurnalModel->insert($jurnalData);
                if (!$jurnalId) {
                    throw new \Exception('Gagal membuat jurnal');
                }
                
                $detailData = [
                    [
                        'jurnal_id' => $jurnalId,
                        'coa_id' => $coaTujuan['id'],
                        'kode_akun' => $coaTujuan['kode_akun'],
                        'nama_akun' => $coaTujuan['nama_akun'],
                        'debit' => $transfer['jumlah'],
                        'kredit' => 0,
                        'keterangan' => 'Transfer masuk dari: ' . $coaSumber['nama_akun']
                    ],
                    [
                        'jurnal_id' => $jurnalId,
                        'coa_id' => $coaSumber['id'],
                        'kode_akun' => $coaSumber['kode_akun'],
                        'nama_akun' => $coaSumber['nama_akun'],
                        'debit' => 0,
                        'kredit' => $transfer['jumlah'],
                        'keterangan' => 'Transfer keluar ke: ' . $coaTujuan['nama_akun']
                    ]
                ];
                
                foreach ($detailData as $detail) {
                    if (!$this->jurnalDetailModel->insert($detail)) {
                        throw new \Exception('Gagal menyimpan detail jurnal');
                    }
                }
                
                $this->transferInternalModel->postTransfer($id, $jurnalId);
                
                $this->db->transCommit();
                $success++;
                
            } catch (\Exception $e) {
                $this->db->transRollback();
                $failed++;
                $errors[] = "Transfer {$transfer['kode_transfer']}: " . $e->getMessage();
            }
        }
        
        $message = "Berhasil memposting {$success} transfer";
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
     * Bulk Delete - Hapus multiple transfer
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
                $transfer = $this->transferInternalModel->find($id);
                
                if (!$transfer) {
                    $failed++;
                    $errors[] = "Transfer ID {$id} tidak ditemukan";
                    continue;
                }
                
                if ($transfer['status'] !== 'Draft') {
                    $failed++;
                    $errors[] = "Transfer {$transfer['kode_transfer']} tidak dapat dihapus (bukan Draft)";
                    continue;
                }
                
                $this->db->transBegin();
                
                if (!empty($transfer['lampiran']) && file_exists(FCPATH . $transfer['lampiran'])) {
                    unlink(FCPATH . $transfer['lampiran']);
                }
                
                $this->transferInternalModel->delete($id);
                
                $this->db->transCommit();
                $success++;
                
            } catch (\Exception $e) {
                $this->db->transRollback();
                $failed++;
                $errors[] = "Transfer ID {$id}: " . $e->getMessage();
            }
        }
        
        $message = "Berhasil menghapus {$success} transfer";
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