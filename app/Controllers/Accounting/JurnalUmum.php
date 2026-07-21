<?php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\JurnalModel;
use App\Models\JurnalDetailModel;
use App\Models\CoaModel;

class JurnalUmum extends BaseController
{
    protected $jurnalModel;
    protected $jurnalDetailModel;
    protected $coaModel;
    protected $validation;
    
    public function __construct()
    {
        $this->jurnalModel = new JurnalModel();
        $this->jurnalDetailModel = new JurnalDetailModel();
        $this->coaModel = new CoaModel();
        $this->validation = \Config\Services::validation();
        helper('form');
    }
    
    /**
     * Display a listing of jurnal umum
     */
    public function index()
    {
        // Get current user data
        $userModel = model('UserModel');
        $karyawanModel = model('KaryawanModel');
        
        $userId = session()->get('user_id');
        $user = $userModel->find($userId);
        $karyawan = $user && $user['karyawan_id'] ? $karyawanModel->find($user['karyawan_id']) : [];
        
        // Get filters from GET parameters
        $filters = [
            'search' => $this->request->getGet('search'),
            'tanggal_mulai' => $this->request->getGet('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getGet('tanggal_selesai'),
            'status' => $this->request->getGet('status'),
            'tipe_referensi' => $this->request->getGet('tipe_referensi'),
            'tipe_jurnal' => $this->request->getGet('tipe_jurnal')
        ];
        
        $perPage = $this->request->getGet('per_page') ?? 20;
        $page = $this->request->getGet('page') ?? 1;
        
        // Get jurnal data with filters
        $result = $this->jurnalModel->getAllWithFilters($filters, $perPage, $page);
        
        // Get statistics
        $stats = $this->jurnalModel->getStats();
        
        // Get statistics by tipe
        $statsByTipe = $this->jurnalModel->getStatsByTipe();
        
        // Format stats for cards
        $jurnalStats = [
            'total_jurnal' => [
                'value' => $stats['total_jurnal'],
                'label' => 'Total Jurnal',
                'icon' => 'fas fa-book',
                'color' => 'primary',
                'trend' => number_format($stats['total_debit'], 0, ',', '.')
            ],
            'jurnal_hari_ini' => [
                'value' => $stats['jurnal_hari_ini'],
                'label' => 'Jurnal Hari Ini',
                'icon' => 'fas fa-calendar-day',
                'color' => 'success',
                'trend' => date('d M Y')
            ],
            'jurnal_bulan_ini' => [
                'value' => $stats['jurnal_bulan_ini'],
                'label' => 'Jurnal Bulan Ini',
                'icon' => 'fas fa-calendar-alt',
                'color' => 'info',
                'trend' => date('M Y')
            ],
            'balance_check' => [
                'value' => number_format($stats['total_debit'], 0, ',', '.'),
                'label' => 'Total Balance',
                'icon' => 'fas fa-balance-scale',
                'color' => $stats['total_debit'] == $stats['total_kredit'] ? 'success' : 'danger',
                'trend' => 'Debit = ' . number_format($stats['total_debit'], 0, ',', '.') . 
                          ' | Kredit = ' . number_format($stats['total_kredit'], 0, ',', '.')
            ]
        ];
        
        // Status options for filter
        $statusOptions = [
            '' => 'Semua Status',
            'draft' => 'Draft',
            'posted' => 'Posted',
            'void' => 'Void'
        ];
        
        // Tipe jurnal options for filter
        $tipeJurnalOptions = [
            '' => 'Semua Tipe',
            'umum' => 'Jurnal Umum',
            'penyesuaian' => 'Jurnal Penyesuaian',
            'mutasi_bank' => 'Mutasi Bank'
        ];
        
        $data = [
            'title' => 'Jurnal Umum',
            'jurnal' => $result['data'],
            'pager' => [
                'total' => $result['total'],
                'per_page' => $result['per_page'],
                'current_page' => $result['current_page'],
                'total_pages' => $result['total_pages']
            ],
            'filters' => $filters,
            'stats' => $jurnalStats,
            'statsByTipe' => $statsByTipe,
            'statusOptions' => $statusOptions,
            'tipeJurnalOptions' => $tipeJurnalOptions,
            'active' => 'bookkeeping',
            'user' => $user ?? ['name' => 'Accounting Staff', 'role' => 'accounting'],
            'karyawan' => $karyawan ?? [],
            'subtitle' => 'Manajemen Pencatatan Jurnal Umum'
        ];
        
        return view('accounting/pembukuan/jurnal-umum/index', $data);
    }
    
    /**
     * Show form for creating new jurnal
     */
    public function create()
    {
        // Get current user data
        $userModel = model('UserModel');
        $karyawanModel = model('KaryawanModel');
        
        $userId = session()->get('user_id');
        $user = $userModel->find($userId);
        $karyawan = $user && $user['karyawan_id'] ? $karyawanModel->find($user['karyawan_id']) : [];
        
        // Get COA data for dropdown (only detail accounts)
        $coaOptions = $this->coaModel->getForTransaction();
        
        // Reference type options
        $refTypeOptions = [
            '' => '-- Tidak Ada --',
            'invoice' => 'Invoice',
            'pembelian' => 'Pembelian',
            'arus_kas' => 'Arus Kas',
            'penggajian' => 'Penggajian',
            'penyesuaian' => 'Penyesuaian',
            'closing' => 'Closing',
            'lainnya' => 'Lainnya'
        ];
        
        // Tipe jurnal options
        $tipeJurnalOptions = [
            'umum' => 'Jurnal Umum',
            'penyesuaian' => 'Jurnal Penyesuaian',
            'mutasi_bank' => 'Mutasi Bank (Read Only)'
        ];
        
        $data = [
            'title' => 'Buat Jurnal Baru',
            'coaOptions' => $coaOptions,
            'refTypeOptions' => $refTypeOptions,
            'tipeJurnalOptions' => $tipeJurnalOptions,
            'active' => 'bookkeeping',
            'user' => $user ?? ['name' => 'Accounting Staff', 'role' => 'accounting'],
            'karyawan' => $karyawan ?? [],
            'subtitle' => 'Tambah Pencatatan Transaksi Baru',
            'validation' => $this->validation
        ];
        
        return view('accounting/pembukuan/jurnal-umum/create', $data);
    }
    
    /**
     * Store new jurnal
     */
    public function store()
    {
        // Validate input
        $rules = [
            'tanggal' => 'required|valid_date',
            'keterangan' => 'required|max_length[500]',
            'referensi' => 'max_length[100]',
            'tipe_referensi' => 'max_length[50]',
            'tipe_jurnal' => 'permit_empty|in_list[umum,penyesuaian,mutasi_bank]',
            'details' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Parse details from JSON string
        $details = json_decode($this->request->getPost('details'), true);
        
        if (!$details || !is_array($details)) {
            return redirect()->back()->withInput()->with('error', 'Data detail jurnal tidak valid');
        }
        
        // Validasi detail wajib dilakukan SEBELUM simpan
        $validationErrors = $this->validateJurnalDetails($details);
        if (!empty($validationErrors)) {
            return redirect()->back()->withInput()->with('errors', $validationErrors);
        }
        
        // Hitung total dan cek balance
        $totals = $this->calculateTotals($details);
        
        // Check balance
        if (abs($totals['debit'] - $totals['kredit']) > 0.01) {
            return redirect()->back()->withInput()->with('error', 
                'Jurnal tidak balance! Total Debit: ' . number_format($totals['debit'], 2) . 
                ', Total Kredit: ' . number_format($totals['kredit'], 2)
            );
        }
        
        // Start database transaction
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // PERBAIKAN: Skip validation sementara karena nomor_jurnal akan di-generate otomatis
            $this->jurnalModel->skipValidation(true);
            
            $tipeJurnal = $this->request->getPost('tipe_jurnal');
            if (empty($tipeJurnal)) {
                $tipeJurnal = 'umum';
            }
            
            $jurnalData = [
                'tanggal' => $this->request->getPost('tanggal'),
                'keterangan' => $this->request->getPost('keterangan'),
                'referensi' => $this->request->getPost('referensi') ?: null,
                'tipe_referensi' => $this->request->getPost('tipe_referensi') ?: null,
                'tipe_jurnal' => $tipeJurnal,
                'total_debit' => $totals['debit'],
                'total_kredit' => $totals['kredit'],
                'status' => 'draft',
                'created_by' => session()->get('user_id')
            ];
            
            // Save jurnal (ini akan trigger generateNomorJurnal di beforeInsert)
            if (!$this->jurnalModel->save($jurnalData)) {
                // Enable validation kembali untuk mendapatkan error
                $this->jurnalModel->skipValidation(false);
                $validationErrors = $this->jurnalModel->errors();
                throw new \RuntimeException('Gagal menyimpan jurnal: ' . implode(', ', $validationErrors));
            }
            
            $jurnalId = $this->jurnalModel->getInsertID();
            
            // Save details
            foreach ($details as $detail) {
                $detailData = [
                    'jurnal_id' => $jurnalId,
                    'coa_id' => $detail['coa_id'],
                    'kode_akun' => $detail['kode_akun'],
                    'nama_akun' => $detail['nama_akun'],
                    'debit' => (float)($detail['debit'] ?? 0),
                    'kredit' => (float)($detail['kredit'] ?? 0),
                    'keterangan' => $detail['keterangan'] ?? null
                ];
                
                if (!$this->jurnalDetailModel->save($detailData)) {
                    $detailErrors = $this->jurnalDetailModel->errors();
                    throw new \RuntimeException('Gagal menyimpan detail jurnal: ' . implode(', ', $detailErrors));
                }
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaksi database gagal');
            }
            
            // Get the saved jurnal to show nomor_jurnal
            $savedJurnal = $this->jurnalModel->find($jurnalId);
            $nomorJurnal = $savedJurnal['nomor_jurnal'] ?? '-';
            
            return redirect()->to(site_url('accounting/pembukuan/jurnal-umum'))
                ->with('success', 'Jurnal berhasil dibuat dengan nomor: ' . $nomorJurnal);
                
        } catch (\Exception $e) {
            $db->transRollback();
            // Pastikan validation di-enable kembali
            $this->jurnalModel->skipValidation(false);
            log_message('error', 'Store Jurnal Error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Show jurnal detail
     */
    public function detail($id)
    {
        // Get current user data
        $userModel = model('UserModel');
        $karyawanModel = model('KaryawanModel');
        
        $userId = session()->get('user_id');
        $user = $userModel->find($userId);
        $karyawan = $user && $user['karyawan_id'] ? $karyawanModel->find($user['karyawan_id']) : [];
        
        // Get jurnal with details
        $jurnal = $this->jurnalModel->getWithDetails($id);
        
        if (!$jurnal) {
            return redirect()->to(site_url('accounting/pembukuan/jurnal-umum'))->with('error', 'Jurnal tidak ditemukan');
        }
        
        // Get tipe jurnal label
        $tipeLabels = [
            'umum' => 'Jurnal Umum',
            'penyesuaian' => 'Jurnal Penyesuaian',
            'mutasi_bank' => 'Mutasi Bank'
        ];
        $jurnal['tipe_jurnal_label'] = $tipeLabels[$jurnal['tipe_jurnal']] ?? ucfirst($jurnal['tipe_jurnal']);
        
        // Format data for view
        $debitDetails = [];
        $kreditDetails = [];
        $totalDebit = 0;
        $totalKredit = 0;
        
        foreach ($jurnal['details'] as $detail) {
            if ($detail['debit'] > 0) {
                $debitDetails[] = $detail;
                $totalDebit += $detail['debit'];
            } else {
                $kreditDetails[] = $detail;
                $totalKredit += $detail['kredit'];
            }
        }
        
        $data = [
            'title' => 'Detail Jurnal: ' . $jurnal['nomor_jurnal'],
            'jurnal' => $jurnal,
            'debitDetails' => $debitDetails,
            'kreditDetails' => $kreditDetails,
            'totalDebit' => $totalDebit,
            'totalKredit' => $totalKredit,
            'active' => 'bookkeeping',
            'user' => $user ?? ['name' => 'Accounting Staff', 'role' => 'accounting'],
            'karyawan' => $karyawan ?? [],
            'subtitle' => 'Detail Pencatatan Jurnal'
        ];
        
        return view('accounting/pembukuan/jurnal-umum/detail', $data);
    }
    
    /**
     * Show edit form
     */
    public function edit($id)
    {
        // Get current user data
        $userModel = model('UserModel');
        $karyawanModel = model('KaryawanModel');
        
        $userId = session()->get('user_id');
        $user = $userModel->find($userId);
        $karyawan = $user && $user['karyawan_id'] ? $karyawanModel->find($user['karyawan_id']) : [];
        
        // Get jurnal with details
        $jurnal = $this->jurnalModel->getWithDetails($id);
        
        if (!$jurnal) {
            return redirect()->to(site_url('accounting/pembukuan/jurnal-umum'))->with('error', 'Jurnal tidak ditemukan');
        }
        
        // Check if jurnal can be edited
        if ($jurnal['status'] !== 'draft') {
            return redirect()->to(site_url('accounting/pembukuan/jurnal-umum/detail/' . $id))
                ->with('error', 'Jurnal dengan status "' . $jurnal['status'] . '" tidak dapat diedit');
        }
        
        // Check if jurnal is from mutasi bank (read only)
        if ($jurnal['tipe_jurnal'] === 'mutasi_bank') {
            return redirect()->to(site_url('accounting/pembukuan/jurnal-umum/detail/' . $id))
                ->with('error', 'Jurnal dari Mutasi Bank tidak dapat diedit. Silahkan edit di menu Mutasi Bank.');
        }
        
        // Get COA data for dropdown
        $coaOptions = $this->coaModel->getForTransaction();
        
        // Reference type options
        $refTypeOptions = [
            '' => '-- Tidak Ada --',
            'invoice' => 'Invoice',
            'pembelian' => 'Pembelian',
            'arus_kas' => 'Arus Kas',
            'penggajian' => 'Penggajian',
            'penyesuaian' => 'Penyesuaian',
            'lainnya' => 'Lainnya'
        ];
        
        // Tipe jurnal options (only for editing)
        $tipeJurnalOptions = [
            'umum' => 'Jurnal Umum',
            'penyesuaian' => 'Jurnal Penyesuaian'
        ];
        
        $data = [
            'title' => 'Edit Jurnal: ' . $jurnal['nomor_jurnal'],
            'jurnal' => $jurnal,
            'coaOptions' => $coaOptions,
            'refTypeOptions' => $refTypeOptions,
            'tipeJurnalOptions' => $tipeJurnalOptions,
            'active' => 'bookkeeping',
            'user' => $user ?? ['name' => 'Accounting Staff', 'role' => 'accounting'],
            'karyawan' => $karyawan ?? [],
            'subtitle' => 'Edit Pencatatan Jurnal',
            'validation' => $this->validation
        ];
        
        return view('accounting/pembukuan/jurnal-umum/edit', $data);
    }
    
    /**
     * Update jurnal
     */
    public function update($id)
    {
        // Check if jurnal exists and is draft
        $jurnal = $this->jurnalModel->find($id);
        
        if (!$jurnal) {
            return redirect()->to(site_url('accounting/pembukuan/jurnal-umum'))->with('error', 'Jurnal tidak ditemukan');
        }
        
        if ($jurnal['status'] !== 'draft') {
            return redirect()->to(site_url('accounting/pembukuan/jurnal-umum/detail/' . $id))
                ->with('error', 'Jurnal dengan status "' . $jurnal['status'] . '" tidak dapat diedit');
        }
        
        if ($jurnal['tipe_jurnal'] === 'mutasi_bank') {
            return redirect()->to(site_url('accounting/pembukuan/jurnal-umum/detail/' . $id))
                ->with('error', 'Jurnal dari Mutasi Bank tidak dapat diedit.');
        }
        
        // Validate input
        $rules = [
            'tanggal' => 'required|valid_date',
            'keterangan' => 'required|max_length[500]',
            'referensi' => 'max_length[100]',
            'tipe_referensi' => 'max_length[50]',
            'tipe_jurnal' => 'permit_empty|in_list[umum,penyesuaian]',
            'details' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Parse details from JSON string
        $details = json_decode($this->request->getPost('details'), true);
        
        if (!$details || !is_array($details)) {
            return redirect()->back()->withInput()->with('error', 'Data detail jurnal tidak valid');
        }
        
        // Validate details
        $validationErrors = $this->validateJurnalDetails($details);
        if (!empty($validationErrors)) {
            return redirect()->back()->withInput()->with('errors', $validationErrors);
        }
        
        // Calculate totals
        $totals = $this->calculateTotals($details);
        
        // Check balance
        if (abs($totals['debit'] - $totals['kredit']) > 0.01) {
            return redirect()->back()->withInput()->with('error', 
                'Jurnal tidak balance! Total Debit: ' . number_format($totals['debit'], 2) . 
                ', Total Kredit: ' . number_format($totals['kredit'], 2)
            );
        }
        
        // Start database transaction
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            $tipeJurnal = $this->request->getPost('tipe_jurnal');
            if (empty($tipeJurnal)) {
                $tipeJurnal = $jurnal['tipe_jurnal'];
            }
            
            // Update jurnal data
            $jurnalData = [
                'id' => $id,
                'tanggal' => $this->request->getPost('tanggal'),
                'keterangan' => $this->request->getPost('keterangan'),
                'referensi' => $this->request->getPost('referensi') ?: null,
                'tipe_referensi' => $this->request->getPost('tipe_referensi') ?: null,
                'tipe_jurnal' => $tipeJurnal,
                'total_debit' => $totals['debit'],
                'total_kredit' => $totals['kredit']
            ];
            
            if (!$this->jurnalModel->save($jurnalData)) {
                throw new \RuntimeException('Gagal update jurnal');
            }
            
            // Delete existing details
            $this->jurnalDetailModel->deleteByJurnalId($id);
            
            // Save new details
            foreach ($details as $detail) {
                $detailData = [
                    'jurnal_id' => $id,
                    'coa_id' => $detail['coa_id'],
                    'kode_akun' => $detail['kode_akun'],
                    'nama_akun' => $detail['nama_akun'],
                    'debit' => (float)$detail['debit'],
                    'kredit' => (float)$detail['kredit'],
                    'keterangan' => $detail['keterangan'] ?? null
                ];
                
                if (!$this->jurnalDetailModel->save($detailData)) {
                    throw new \RuntimeException('Gagal menyimpan detail jurnal');
                }
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaksi database gagal');
            }
            
            return redirect()->to(site_url('accounting/pembukuan/jurnal-umum/detail/' . $id))->with('success', 'Jurnal berhasil diperbarui');
            
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Update Jurnal Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete jurnal
     */
    public function delete($id)
    {
        // Check if jurnal exists and is draft
        $jurnal = $this->jurnalModel->find($id);
        
        if (!$jurnal) {
            return redirect()->to(site_url('accounting/pembukuan/jurnal-umum'))->with('error', 'Jurnal tidak ditemukan');
        }
        
        if ($jurnal['status'] !== 'draft') {
            return redirect()->to(site_url('accounting/pembukuan/jurnal-umum/detail/' . $id))
                ->with('error', 'Jurnal dengan status "' . $jurnal['status'] . '" tidak dapat dihapus. Gunakan void untuk jurnal posted.');
        }
        
        // Start database transaction
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Delete details first
            $this->jurnalDetailModel->deleteByJurnalId($id);
            
            // Delete jurnal
            if (!$this->jurnalModel->delete($id)) {
                throw new \RuntimeException('Gagal menghapus jurnal');
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaksi database gagal');
            }
            
            return redirect()->to(site_url('accounting/pembukuan/jurnal-umum'))->with('success', 'Jurnal berhasil dihapus');
            
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Delete Jurnal Error: ' . $e->getMessage());
            return redirect()->to(site_url('accounting/pembukuan/jurnal-umum'))->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Post jurnal (draft → posted)
     */
    public function post($id)
    {
        try {
            if ($this->jurnalModel->postJurnal($id)) {
                return redirect()->to(site_url('accounting/pembukuan/jurnal-umum/detail/' . $id))->with('success', 'Jurnal berhasil diposting');
            } else {
                return redirect()->to(site_url('accounting/pembukuan/jurnal-umum/detail/' . $id))->with('error', 'Gagal memposting jurnal');
            }
        } catch (\Exception $e) {
            return redirect()->to(site_url('accounting/pembukuan/jurnal-umum/detail/' . $id))->with('error', $e->getMessage());
        }
    }
    
    /**
     * Void jurnal (posted → void)
     */
    public function void($id)
    {
        try {
            if ($this->jurnalModel->voidJurnal($id)) {
                return redirect()->to(site_url('accounting/pembukuan/jurnal-umum/detail/' . $id))->with('success', 'Jurnal berhasil di-void');
            } else {
                return redirect()->to(site_url('accounting/pembukuan/jurnal-umum/detail/' . $id))->with('error', 'Gagal meng-void jurnal');
            }
        } catch (\Exception $e) {
            return redirect()->to(site_url('accounting/pembukuan/jurnal-umum/detail/' . $id))->with('error', $e->getMessage());
        }
    }
    
    /**
     * AJAX: Get COA data for dropdown
     */
    public function ajaxGetCoa()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['error' => 'Method not allowed']);
        }
        
        $search = $this->request->getGet('search');
        $type = $this->request->getGet('type');
        
        $coaModel = new CoaModel();
        $builder = $coaModel->where('is_active', 1)
                           ->where('is_header', 0);
        
        if ($search) {
            $builder->groupStart()
                ->like('kode_akun', $search)
                ->orLike('nama_akun', $search)
                ->orLike('tipe_akun', $search)
                ->groupEnd();
        }
        
        if ($type) {
            $builder->where('tipe_akun', $type);
        }
        
        $coa = $builder->orderBy('kode_akun', 'ASC')
                       ->limit(50)
                       ->findAll();
        
        $results = [];
        foreach ($coa as $account) {
            $results[] = [
                'id' => $account['id'],
                'kode_akun' => $account['kode_akun'],
                'nama_akun' => $account['nama_akun'],
                'tipe_akun' => $account['tipe_akun'],
                'saldo_normal' => $account['saldo_normal'],
                'text' => $account['kode_akun'] . ' - ' . $account['nama_akun'] . ' (' . $account['tipe_akun'] . ')'
            ];
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $results,
            'total' => count($results)
        ]);
    }
    
    /**
     * AJAX: Validate jurnal balance
     */
    public function ajaxValidateBalance()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['error' => 'Method not allowed']);
        }
        
        $details = $this->request->getPost('details');
        
        if (!$details) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data detail jurnal diperlukan'
            ]);
        }
        
        $details = json_decode($details, true);
        
        if (!$details || !is_array($details)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Format data detail tidak valid'
            ]);
        }
        
        // Validate details
        $validationErrors = $this->validateJurnalDetails($details);
        if (!empty($validationErrors)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validationErrors
            ]);
        }
        
        // Calculate totals
        $totals = $this->calculateTotals($details);
        
        // Check balance
        $isBalanced = abs($totals['debit'] - $totals['kredit']) <= 0.01;
        
        return $this->response->setJSON([
            'success' => true,
            'balanced' => $isBalanced,
            'totals' => [
                'debit' => $totals['debit'],
                'kredit' => $totals['kredit'],
                'difference' => $totals['debit'] - $totals['kredit']
            ],
            'message' => $isBalanced ? 
                'Jurnal balance ✓' : 
                'Jurnal tidak balance! Selisih: ' . number_format($totals['debit'] - $totals['kredit'], 2)
        ]);
    }
    
    /**
     * Validate jurnal details
     */
    private function validateJurnalDetails(array $details)
    {
        $errors = [];
        
        if (count($details) < 2) {
            $errors[] = 'Jurnal minimal harus memiliki 2 baris (1 debit dan 1 kredit)';
            return $errors; // Return early if not enough rows
        }
        
        $hasDebit = false;
        $hasKredit = false;
        $coaIds = [];
        
        foreach ($details as $index => $detail) {
            $rowNum = $index + 1;
            
            // Cek semua field yang diperlukan
            if (empty($detail['coa_id']) || !is_numeric($detail['coa_id'])) {
                $errors[] = "Baris {$rowNum}: Akun harus dipilih";
                continue;
            }
            
            if (empty($detail['kode_akun']) || empty($detail['nama_akun'])) {
                $errors[] = "Baris {$rowNum}: Data akun tidak lengkap";
                continue;
            }
            
            // Pastikan debit dan kredit ada
            $debit = isset($detail['debit']) ? (float)$detail['debit'] : 0;
            $kredit = isset($detail['kredit']) ? (float)$detail['kredit'] : 0;
            
            // Check debit/kredit values
            if ($debit > 0 && $kredit > 0) {
                $errors[] = "Baris {$rowNum}: Tidak boleh memiliki debit dan kredit sekaligus";
            }
            
            if ($debit == 0 && $kredit == 0) {
                $errors[] = "Baris {$rowNum}: Harus memiliki nilai debit atau kredit";
            }
            
            if ($debit < 0 || $kredit < 0) {
                $errors[] = "Baris {$rowNum}: Nilai tidak boleh negatif";
            }
            
            if ($debit > 0) $hasDebit = true;
            if ($kredit > 0) $hasKredit = true;
            
            // Collect COA IDs for validation
            $coaIds[] = (int)$detail['coa_id'];
        }
        
        // Check if has both debit and kredit
        if (!$hasDebit) {
            $errors[] = 'Jurnal harus memiliki minimal 1 baris debit';
        }
        
        if (!$hasKredit) {
            $errors[] = 'Jurnal harus memiliki minimal 1 baris kredit';
        }
        
        // Hanya validasi COA jika ada data
        if (!empty($coaIds)) {
            // Validate COA IDs exist
            $validCoaIds = $this->jurnalDetailModel->validateCoaIds($coaIds);
            $invalidCoaIds = array_diff($coaIds, $validCoaIds);
            
            if (!empty($invalidCoaIds)) {
                $errors[] = 'Beberapa akun tidak valid atau tidak aktif';
            }
        }
        
        return $errors;
    }
    
    /**
     * Calculate totals from details
     */
    private function calculateTotals(array $details)
    {
        $totalDebit = 0;
        $totalKredit = 0;
        
        foreach ($details as $detail) {
            $totalDebit += (float)($detail['debit'] ?? 0);
            $totalKredit += (float)($detail['kredit'] ?? 0);
        }
        
        return [
            'debit' => $totalDebit,
            'kredit' => $totalKredit
        ];
    }
    
    /**
     * Export jurnal to Excel
     */
    public function export()
    {
        try {
            // Get filters from session or default
            $filters = [
                'tanggal_mulai' => $this->request->getGet('tanggal_mulai'),
                'tanggal_selesai' => $this->request->getGet('tanggal_selesai'),
                'status' => $this->request->getGet('status')
            ];
            
            // Get all jurnal data with filters (no pagination)
            $builder = $this->jurnalModel->select('jurnal.*, creator.name as creator_name, poster.name as poster_name')
                ->join('users as creator', 'creator.id = jurnal.created_by', 'left')
                ->join('users as poster', 'poster.id = jurnal.posted_by', 'left');
            
            if (!empty($filters['tanggal_mulai'])) {
                $builder->where('jurnal.tanggal >=', $filters['tanggal_mulai']);
            }
            
            if (!empty($filters['tanggal_selesai'])) {
                $builder->where('jurnal.tanggal <=', $filters['tanggal_selesai']);
            }
            
            if (!empty($filters['status'])) {
                $builder->where('jurnal.status', $filters['status']);
            }
            
            $jurnal = $builder->orderBy('jurnal.tanggal', 'DESC')
                ->orderBy('jurnal.nomor_jurnal', 'DESC')
                ->findAll();
            
            // Load Excel library
            $excel = new \App\Libraries\ExportExcel();
            $excel->setTitle('Laporan Jurnal Umum - ' . date('Y-m-d'))
                  ->setSubject('Data Jurnal Umum')
                  ->setDescription('Export data jurnal umum')
                  ->setKeywords('jurnal, akuntansi, export')
                  ->setAuthor('CDW Accounting System');
            
            $headers = [
                'No',
                'Nomor Jurnal',
                'Tanggal',
                'Keterangan',
                'Referensi',
                'Tipe Referensi',
                'Total Debit',
                'Total Kredit',
                'Status',
                'Dibuat Oleh',
                'Diposting Oleh',
                'Tanggal Dibuat',
                'Tanggal Posting'
            ];
            
            $data = [];
            $counter = 1;
            
            foreach ($jurnal as $j) {
                $data[] = [
                    $counter++,
                    $j['nomor_jurnal'],
                    date('d/m/Y', strtotime($j['tanggal'])),
                    $j['keterangan'],
                    $j['referensi'] ?? '-',
                    $j['tipe_referensi'] ?? '-',
                    number_format($j['total_debit'], 2),
                    number_format($j['total_kredit'], 2),
                    ucfirst($j['status']),
                    $j['creator_name'] ?? '-',
                    $j['poster_name'] ?? '-',
                    date('d/m/Y H:i', strtotime($j['created_at'])),
                    $j['posted_at'] ? date('d/m/Y H:i', strtotime($j['posted_at'])) : '-'
                ];
            }
            
            $excel->setHeaders($headers)
                  ->setData($data)
                  ->setAutoFilter(true)
                  ->setFreezePane('A2')
                  ->setColumnWidths([
                      'A' => 5,    // No
                      'B' => 20,   // Nomor Jurnal
                      'C' => 12,   // Tanggal
                      'D' => 40,   // Keterangan
                      'E' => 20,   // Referensi
                      'F' => 15,   // Tipe Referensi
                      'G' => 15,   // Total Debit
                      'H' => 15,   // Total Kredit
                      'I' => 10,   // Status
                      'J' => 20,   // Dibuat Oleh
                      'K' => 20,   // Diposting Oleh
                      'L' => 20,   // Tanggal Dibuat
                      'M' => 20    // Tanggal Posting
                  ]);
            
            return $excel->export('jurnal_umum_' . date('Ymd_His') . '.xlsx');
            
        } catch (\Exception $e) {
            log_message('error', 'Export Jurnal Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }
    
    /**
     * Print jurnal detail
     */
    public function print($id)
    {
        // Get jurnal with details
        $jurnal = $this->jurnalModel->getWithDetails($id);
        
        if (!$jurnal) {
            return redirect()->to(site_url('accounting/pembukuan/jurnal-umum'))->with('error', 'Jurnal tidak ditemukan');
        }
        
        // Format data for print
        $debitDetails = [];
        $kreditDetails = [];
        
        foreach ($jurnal['details'] as $detail) {
            if ($detail['debit'] > 0) {
                $debitDetails[] = $detail;
            } else {
                $kreditDetails[] = $detail;
            }
        }
        
        // Get company info
        $company = model('PerusahaanModel')->first() ?? ['nama_perusahaan' => 'PT. Cipta Duta Wacana'];
        
        // Get user info
        $user = model('UserModel')->find(session()->get('user_id'));
        
        $data = [
            'title' => 'Jurnal Umum: ' . $jurnal['nomor_jurnal'],
            'jurnal' => $jurnal,
            'debitDetails' => $debitDetails,
            'kreditDetails' => $kreditDetails,
            'company' => $company,
            'printed_by' => $user['name'] ?? 'System',
            'print_date' => date('d/m/Y H:i:s')
        ];
        
        return view('accounting/pembukuan/jurnal-umum/print', $data);
    }
}