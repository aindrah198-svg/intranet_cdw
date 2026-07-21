<?php
namespace App\Models;

use CodeIgniter\Model;

class JurnalModel extends Model
{
    protected $table = 'jurnal';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'nomor_jurnal',
        'tanggal',
        'keterangan',
        'referensi',
        'tipe_referensi',
        'total_debit',
        'total_kredit',
        'status',
        'tipe_jurnal',
        'posted_by',
        'posted_at',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'nomor_jurnal' => 'required|max_length[100]|is_unique[jurnal.nomor_jurnal,id,{id}]',
        'tanggal' => 'required|valid_date',
        'keterangan' => 'required',
        'total_debit' => 'required|decimal',
        'total_kredit' => 'required|decimal',
        'tipe_jurnal' => 'permit_empty|in_list[umum,penyesuaian,mutasi_bank]'
    ];

    protected $validationMessages = [
        'nomor_jurnal' => [
            'required' => 'Nomor jurnal harus diisi',
            'is_unique' => 'Nomor jurnal sudah digunakan'
        ],
        'tanggal' => [
            'required' => 'Tanggal jurnal harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'tipe_jurnal' => [
            'in_list' => 'Tipe jurnal tidak valid. Pilihan: umum, penyesuaian, mutasi_bank'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateNomorJurnal', 'setCreatedBy', 'setDefaultTipeJurnal'];
    protected $beforeUpdate = ['validateBalance'];

    /**
     * Set default tipe_jurnal jika tidak diisi
     */
    protected function setDefaultTipeJurnal(array $data)
    {
        if (!isset($data['data']['tipe_jurnal']) || empty($data['data']['tipe_jurnal'])) {
            $data['data']['tipe_jurnal'] = 'umum';
        }
        return $data;
    }

    /**
     * Generate nomor jurnal otomatis
     * Format: JRNL-YYYYMMDD-XXXX
     */
    protected function generateNomorJurnal(array $data)
    {
        if (empty($data['data']['nomor_jurnal'])) {
            $tanggal = $data['data']['tanggal'] ?? date('Y-m-d');
            $prefix = 'JRNL-' . date('Ymd', strtotime($tanggal)) . '-';
            
            // Cari sequence terakhir untuk tanggal ini
            $lastJurnal = $this->select('nomor_jurnal')
                ->like('nomor_jurnal', $prefix, 'after')
                ->orderBy('nomor_jurnal', 'DESC')
                ->first();
            
            if ($lastJurnal) {
                $lastNum = substr($lastJurnal['nomor_jurnal'], strlen($prefix));
                $nextNum = str_pad((int)$lastNum + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNum = '0001';
            }
            
            $data['data']['nomor_jurnal'] = $prefix . $nextNum;
        }
        
        log_message('debug', 'Generated nomor jurnal: ' . ($data['data']['nomor_jurnal'] ?? 'empty'));
        
        return $data;
    }

    /**
     * Validasi bahwa total debit sama dengan total kredit
     */
    protected function validateBalance(array $data)
    {
        if (isset($data['data']['total_debit']) && isset($data['data']['total_kredit'])) {
            $debit = (float)$data['data']['total_debit'];
            $kredit = (float)$data['data']['total_kredit'];
            
            if (abs($debit - $kredit) > 0.01) { // Tolerance 0.01
                throw new \RuntimeException('Total debit (' . number_format($debit, 2) . ') tidak sama dengan total kredit (' . number_format($kredit, 2) . ')');
            }
        }
        
        return $data;
    }

    /**
     * Set created_by dari session user
     */
    protected function setCreatedBy(array $data)
    {
        if (session()->get('user_id')) {
            $data['data']['created_by'] = session()->get('user_id');
        }
        return $data;
    }

    /**
     * Get all jurnal with pagination and filters
     */
    public function getAllWithFilters($filters = [], $perPage = 20, $page = 1)
    {
        $builder = $this->select('jurnal.*, users.name as posted_by_name')
            ->join('users', 'users.id = jurnal.posted_by', 'left');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('jurnal.nomor_jurnal', $search)
                ->orLike('jurnal.keterangan', $search)
                ->orLike('jurnal.referensi', $search)
                ->groupEnd();
        }
        
        if (!empty($filters['tanggal_mulai'])) {
            $builder->where('jurnal.tanggal >=', $filters['tanggal_mulai']);
        }
        
        if (!empty($filters['tanggal_selesai'])) {
            $builder->where('jurnal.tanggal <=', $filters['tanggal_selesai']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('jurnal.status', $filters['status']);
        }
        
        if (!empty($filters['tipe_referensi'])) {
            $builder->where('jurnal.tipe_referensi', $filters['tipe_referensi']);
        }
        
        if (!empty($filters['tipe_jurnal'])) {
            $builder->where('jurnal.tipe_jurnal', $filters['tipe_jurnal']);
        }
        
        $builder->orderBy('jurnal.tanggal', 'DESC')
                ->orderBy('jurnal.nomor_jurnal', 'DESC');
        
        // Get total for pagination
        $total = $builder->countAllResults(false);
        
        // Get paginated results
        $offset = ($page - 1) * $perPage;
        $jurnal = $builder->limit($perPage, $offset)->findAll();
        
        return [
            'data' => $jurnal,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Get jurnal by ID with details
     */
    public function getWithDetails($id)
    {
        $jurnal = $this->select('jurnal.*, 
            creator.name as creator_name,
            poster.name as poster_name')
            ->join('users as creator', 'creator.id = jurnal.created_by', 'left')
            ->join('users as poster', 'poster.id = jurnal.posted_by', 'left')
            ->where('jurnal.id', $id)
            ->first();
        
        if (!$jurnal) {
            return null;
        }
        
        // Get jurnal details
        $detailModel = new JurnalDetailModel();
        $jurnal['details'] = $detailModel->where('jurnal_id', $id)->findAll();
        
        return $jurnal;
    }

    /**
     * Post jurnal (ubah status dari draft ke posted)
     */
    public function postJurnal($id)
    {
        $jurnal = $this->find($id);
        
        if (!$jurnal) {
            throw new \RuntimeException('Jurnal tidak ditemukan');
        }
        
        if ($jurnal['status'] !== 'draft') {
            throw new \RuntimeException('Hanya jurnal dengan status draft yang bisa diposting');
        }
        
        $data = [
            'id' => $id,
            'status' => 'posted',
            'posted_by' => session()->get('user_id'),
            'posted_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->save($data);
    }

    /**
     * Void jurnal (ubah status ke void)
     */
    public function voidJurnal($id)
    {
        $jurnal = $this->find($id);
        
        if (!$jurnal) {
            throw new \RuntimeException('Jurnal tidak ditemukan');
        }
        
        if ($jurnal['status'] !== 'posted') {
            throw new \RuntimeException('Hanya jurnal dengan status posted yang bisa di-void');
        }
        
        $data = [
            'id' => $id,
            'status' => 'void'
        ];
        
        return $this->save($data);
    }

    /**
     * Get statistics
     */
    public function getStats($startDate = null, $endDate = null)
    {
        $builder = $this->where('status', 'posted');
        
        if ($startDate) {
            $builder->where('tanggal >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('tanggal <=', $endDate);
        }
        
        $stats = $builder->select("
                COUNT(*) as total_jurnal,
                SUM(total_debit) as total_debit,
                SUM(total_kredit) as total_kredit,
                COUNT(CASE WHEN DATE(tanggal) = CURDATE() THEN 1 END) as jurnal_hari_ini,
                COUNT(CASE WHEN MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE()) THEN 1 END) as jurnal_bulan_ini
            ")
            ->first();
        
        return $stats ?: [
            'total_jurnal' => 0,
            'total_debit' => 0,
            'total_kredit' => 0,
            'jurnal_hari_ini' => 0,
            'jurnal_bulan_ini' => 0
        ];
    }

    /**
     * Get statistics by tipe_jurnal
     */
    public function getStatsByTipe($tipeJurnal = null, $startDate = null, $endDate = null)
    {
        $builder = $this->where('status', 'posted');
        
        if ($tipeJurnal) {
            $builder->where('tipe_jurnal', $tipeJurnal);
        }
        
        if ($startDate) {
            $builder->where('tanggal >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('tanggal <=', $endDate);
        }
        
        $stats = $builder->select("
                COUNT(*) as total_jurnal,
                SUM(total_debit) as total_debit,
                SUM(total_kredit) as total_kredit,
                SUM(CASE WHEN tipe_jurnal = 'mutasi_bank' THEN total_debit ELSE 0 END) as total_mutasi_bank,
                SUM(CASE WHEN tipe_jurnal = 'penyesuaian' THEN total_debit ELSE 0 END) as total_penyesuaian,
                SUM(CASE WHEN tipe_jurnal = 'umum' THEN total_debit ELSE 0 END) as total_umum
            ")
            ->first();
        
        return $stats ?: [
            'total_jurnal' => 0,
            'total_debit' => 0,
            'total_kredit' => 0,
            'total_mutasi_bank' => 0,
            'total_penyesuaian' => 0,
            'total_umum' => 0
        ];
    }

    /**
     * Check if jurnal has been posted
     */
    public function isPosted($id)
    {
        $jurnal = $this->find($id);
        return $jurnal && $jurnal['status'] === 'posted';
    }

    /**
     * Get jurnal by reference
     */
    public function getByReference($referensi, $tipeReferensi = null)
    {
        $builder = $this->where('referensi', $referensi);
        
        if ($tipeReferensi) {
            $builder->where('tipe_referensi', $tipeReferensi);
        }
        
        return $builder->findAll();
    }

    /**
     * Get jurnal by tipe_jurnal
     */
    public function getByTipeJurnal($tipeJurnal, $filters = [], $perPage = 20, $page = 1)
    {
        $builder = $this->select('jurnal.*, users.name as posted_by_name')
            ->join('users', 'users.id = jurnal.posted_by', 'left')
            ->where('jurnal.tipe_jurnal', $tipeJurnal);
        
        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('jurnal.nomor_jurnal', $search)
                ->orLike('jurnal.keterangan', $search)
                ->orLike('jurnal.referensi', $search)
                ->groupEnd();
        }
        
        if (!empty($filters['tanggal_mulai'])) {
            $builder->where('jurnal.tanggal >=', $filters['tanggal_mulai']);
        }
        
        if (!empty($filters['tanggal_selesai'])) {
            $builder->where('jurnal.tanggal <=', $filters['tanggal_selesai']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('jurnal.status', $filters['status']);
        }
        
        $builder->orderBy('jurnal.tanggal', 'DESC')
                ->orderBy('jurnal.nomor_jurnal', 'DESC');
        
        $total = $builder->countAllResults(false);
        $offset = ($page - 1) * $perPage;
        $data = $builder->limit($perPage, $offset)->findAll();
        
        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Get summary per tipe_jurnal
     */
    public function getSummaryPerTipe($startDate = null, $endDate = null)
    {
        $builder = $this->where('status', 'posted');
        
        if ($startDate) {
            $builder->where('tanggal >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('tanggal <=', $endDate);
        }
        
        $result = $builder->select("
                tipe_jurnal,
                COUNT(*) as jumlah_transaksi,
                SUM(total_debit) as total_debit,
                SUM(total_kredit) as total_kredit
            ")
            ->groupBy('tipe_jurnal')
            ->orderBy('tipe_jurnal', 'ASC')
            ->findAll();
        
        return $result;
    }

    /**
     * Get buku besar untuk suatu akun
     */
    public function getBukuBesar($coaId, $startDate = null, $endDate = null)
    {
        $detailModel = new JurnalDetailModel();
        
        $builder = $detailModel->select('
                jurnal_detail.*,
                jurnal.tanggal,
                jurnal.nomor_jurnal,
                jurnal.keterangan as jurnal_keterangan,
                jurnal.tipe_jurnal,
                jurnal.status as jurnal_status
            ')
            ->join('jurnal', 'jurnal.id = jurnal_detail.jurnal_id')
            ->where('jurnal_detail.coa_id', $coaId)
            ->where('jurnal.status', 'posted');
        
        if ($startDate) {
            $builder->where('jurnal.tanggal >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('jurnal.tanggal <=', $endDate);
        }
        
        return $builder->orderBy('jurnal.tanggal', 'ASC')
            ->orderBy('jurnal.nomor_jurnal', 'ASC')
            ->findAll();
    }

    /**
     * Generate laporan laba rugi
     */
    public function getLaporanLabaRugi($startDate, $endDate)
    {
        $coaModel = new CoaModel();
        
        // Get pendapatan (tipe akun = Pendapatan, saldo_normal = Kredit)
        $pendapatan = $coaModel->where('tipe_akun', 'Pendapatan')
            ->where('is_header', 0)
            ->where('is_active', 1)
            ->findAll();
        
        // Get beban (tipe akun = Beban, saldo_normal = Debit)
        $beban = $coaModel->where('tipe_akun', 'Beban')
            ->where('is_header', 0)
            ->where('is_active', 1)
            ->findAll();
        
        $result = [
            'pendapatan' => [],
            'total_pendapatan' => 0,
            'beban' => [],
            'total_beban' => 0,
            'laba_bersih' => 0
        ];
        
        // Hitung pendapatan
        foreach ($pendapatan as $akun) {
            $saldo = $this->getSaldoAkunPeriode($akun['id'], $startDate, $endDate);
            $result['pendapatan'][] = [
                'kode_akun' => $akun['kode_akun'],
                'nama_akun' => $akun['nama_akun'],
                'saldo' => $saldo
            ];
            $result['total_pendapatan'] += $saldo;
        }
        
        // Hitung beban
        foreach ($beban as $akun) {
            $saldo = $this->getSaldoAkunPeriode($akun['id'], $startDate, $endDate);
            $result['beban'][] = [
                'kode_akun' => $akun['kode_akun'],
                'nama_akun' => $akun['nama_akun'],
                'saldo' => $saldo
            ];
            $result['total_beban'] += $saldo;
        }
        
        $result['laba_bersih'] = $result['total_pendapatan'] - $result['total_beban'];
        
        return $result;
    }

    /**
     * Get saldo akun untuk periode tertentu
     */
    private function getSaldoAkunPeriode($coaId, $startDate, $endDate)
    {
        $detailModel = new JurnalDetailModel();
        
        $result = $detailModel->select('
                SUM(debit) as total_debit,
                SUM(kredit) as total_kredit
            ')
            ->join('jurnal', 'jurnal.id = jurnal_detail.jurnal_id')
            ->where('jurnal_detail.coa_id', $coaId)
            ->where('jurnal.status', 'posted')
            ->where('jurnal.tanggal >=', $startDate)
            ->where('jurnal.tanggal <=', $endDate)
            ->first();
        
        $coa = new CoaModel();
        $akun = $coa->find($coaId);
        
        if ($akun && $akun['saldo_normal'] == 'Debit') {
            return ($result['total_debit'] ?? 0) - ($result['total_kredit'] ?? 0);
        } else {
            return ($result['total_kredit'] ?? 0) - ($result['total_debit'] ?? 0);
        }
    }
}
?>