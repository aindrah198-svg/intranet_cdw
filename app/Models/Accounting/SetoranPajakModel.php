<?php
namespace App\Models\Accounting;

use CodeIgniter\Model;

class SetoranPajakModel extends Model
{
    protected $table = 'setoran_pajak';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'jenis_pajak',
        'masa_pajak',
        'tahun_pajak',
        'tanggal_setor',
        'nominal',
        'no_bukti_setor',
        'no_ntpn',
        'keterangan',
        'mutasi_bank_id',
        'jurnal_id',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'jenis_pajak' => 'required|in_list[PPN,PPh 21,PPh 23,PPh 25,PPh 29,PPh Badan,Lainnya]',
        'masa_pajak' => 'permit_empty|numeric|greater_than[0]|less_than_equal_to[12]',
        'tahun_pajak' => 'required|numeric|min_length[4]|max_length[4]',
        'tanggal_setor' => 'required|valid_date',
        'nominal' => 'required|numeric|greater_than[0]',
        'no_bukti_setor' => 'permit_empty|string',
        'no_ntpn' => 'permit_empty|string|min_length[16]|max_length[20]',
        'mutasi_bank_id' => 'permit_empty|is_natural_no_zero',
        'jurnal_id' => 'permit_empty|is_natural_no_zero'
    ];

    protected $validationMessages = [
        'jenis_pajak' => [
            'required' => 'Jenis pajak harus dipilih',
            'in_list' => 'Jenis pajak tidak valid'
        ],
        'tahun_pajak' => [
            'required' => 'Tahun pajak harus diisi',
            'numeric' => 'Tahun pajak harus berupa angka'
        ],
        'tanggal_setor' => [
            'required' => 'Tanggal setor harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'nominal' => [
            'required' => 'Nominal setoran harus diisi',
            'numeric' => 'Nominal harus berupa angka',
            'greater_than' => 'Nominal harus lebih dari 0'
        ],
        'no_ntpn' => [
            'min_length' => 'NTPN minimal 16 digit',
            'max_length' => 'NTPN maksimal 20 digit'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = ['setDefaultValues', 'validateMasaPajak', 'validateMutasiBank', 'validateJurnal', 'setCreatedBy'];
    protected $beforeUpdate = ['validateMutasiBank', 'validateJurnal'];

    /**
     * Set default values
     */
    protected function setDefaultValues(array $data)
    {
        if (!isset($data['data']['tanggal_setor'])) {
            $data['data']['tanggal_setor'] = date('Y-m-d');
        }
        
        if (!isset($data['data']['tahun_pajak'])) {
            $data['data']['tahun_pajak'] = date('Y');
        }
        
        if (!isset($data['data']['masa_pajak']) && $data['data']['jenis_pajak'] !== 'PPh Badan') {
            $data['data']['masa_pajak'] = date('m');
        }
        
        return $data;
    }

    /**
     * Validasi masa pajak
     */
    protected function validateMasaPajak(array $data)
    {
        $jenisPajak = $data['data']['jenis_pajak'] ?? null;
        $masaPajak = $data['data']['masa_pajak'] ?? null;
        
        // PPh Badan tidak memiliki masa pajak bulanan
        if ($jenisPajak === 'PPh Badan' && $masaPajak) {
            throw new \RuntimeException('PPh Badan tidak memiliki masa pajak bulanan, kosongkan masa pajak');
        }
        
        // Jenis pajak lain harus memiliki masa pajak
        if ($jenisPajak !== 'PPh Badan' && empty($masaPajak)) {
            throw new \RuntimeException('Masa pajak harus diisi untuk jenis pajak ' . $jenisPajak);
        }
        
        return $data;
    }

    /**
     * Validasi mutasi bank
     */
    protected function validateMutasiBank(array $data)
    {
        $mutasiBankId = $data['data']['mutasi_bank_id'] ?? null;
        
        if ($mutasiBankId) {
            $mutasiBankModel = new MutasiBankModel();
            $mutasi = $mutasiBankModel->find($mutasiBankId);
            
            if (!$mutasi) {
                throw new \RuntimeException('Mutasi bank tidak ditemukan');
            }
            
            if ($mutasi['tipe'] !== 'Debit') {
                throw new \RuntimeException('Mutasi bank untuk setoran pajak harus bertipe Debit (pengeluaran)');
            }
            
            if ($mutasi['status'] !== 'Posted') {
                throw new \RuntimeException('Mutasi bank harus sudah diposting');
            }
        }
        
        return $data;
    }

    /**
     * Validasi jurnal
     */
    protected function validateJurnal(array $data)
    {
        $jurnalId = $data['data']['jurnal_id'] ?? null;
        
        if ($jurnalId) {
            $jurnalModel = new \App\Models\JurnalModel();
            $jurnal = $jurnalModel->find($jurnalId);
            
            if (!$jurnal) {
                throw new \RuntimeException('Jurnal tidak ditemukan');
            }
            
            if ($jurnal['status'] !== 'posted') {
                throw new \RuntimeException('Jurnal harus sudah diposting');
            }
        }
        
        return $data;
    }

    /**
     * Set created_by
     */
    protected function setCreatedBy(array $data)
    {
        $data['data']['created_by'] = session()->get('user_id');
        return $data;
    }

    /**
     * Get all setoran pajak with filters
     */
    public function getAllWithFilters($filters = [], $perPage = 20, $page = 1)
    {
        $builder = $this->select('setoran_pajak.*, 
            creator.username as creator_name,
            mutasi_bank.kode_transaksi as kode_mutasi,
            mutasi_bank.tanggal as tanggal_mutasi,
            jurnal.nomor_jurnal')
            ->join('users as creator', 'creator.id = setoran_pajak.created_by', 'left')
            ->join('mutasi_bank', 'mutasi_bank.id = setoran_pajak.mutasi_bank_id', 'left')
            ->join('jurnal', 'jurnal.id = setoran_pajak.jurnal_id', 'left');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('setoran_pajak.no_bukti_setor', $search)
                ->orLike('setoran_pajak.no_ntpn', $search)
                ->orLike('setoran_pajak.keterangan', $search)
                ->orLike('mutasi_bank.kode_transaksi', $search)
                ->groupEnd();
        }
        
        if (!empty($filters['jenis_pajak'])) {
            $builder->where('setoran_pajak.jenis_pajak', $filters['jenis_pajak']);
        }
        
        if (!empty($filters['tahun'])) {
            $builder->where('setoran_pajak.tahun_pajak', $filters['tahun']);
        }
        
        if (!empty($filters['bulan'])) {
            $builder->where('setoran_pajak.masa_pajak', $filters['bulan']);
        }
        
        if (!empty($filters['tanggal_mulai'])) {
            $builder->where('setoran_pajak.tanggal_setor >=', $filters['tanggal_mulai']);
        }
        
        if (!empty($filters['tanggal_selesai'])) {
            $builder->where('setoran_pajak.tanggal_setor <=', $filters['tanggal_selesai']);
        }
        
        $builder->orderBy('setoran_pajak.tanggal_setor', 'DESC')
                ->orderBy('setoran_pajak.id', 'DESC');
        
        // Get total for pagination
        $total = $builder->countAllResults(false);
        
        // Get paginated results
        $offset = ($page - 1) * $perPage;
        $setoran = $builder->limit($perPage, $offset)->findAll();
        
        return [
            'data' => $setoran,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Get setoran pajak by ID with details
     */
    public function getWithDetails($id)
    {
        $setoran = $this->select('setoran_pajak.*, 
            creator.username as creator_name,
            creator.name as creator_fullname,
            mutasi_bank.kode_transaksi as kode_mutasi,
            mutasi_bank.tanggal as tanggal_mutasi,
            mutasi_bank.bank_asal,
            mutasi_bank.bank_tujuan,
            mutasi_bank.no_referensi,
            jurnal.nomor_jurnal,
            jurnal.status as jurnal_status')
            ->join('users as creator', 'creator.id = setoran_pajak.created_by', 'left')
            ->join('mutasi_bank', 'mutasi_bank.id = setoran_pajak.mutasi_bank_id', 'left')
            ->join('jurnal', 'jurnal.id = setoran_pajak.jurnal_id', 'left')
            ->where('setoran_pajak.id', $id)
            ->first();
        
        return $setoran;
    }

    /**
     * Get setoran by jenis pajak
     */
    public function getByJenisPajak($jenisPajak, $tahun = null)
    {
        $builder = $this->where('jenis_pajak', $jenisPajak);
        
        if ($tahun) {
            $builder->where('tahun_pajak', $tahun);
        }
        
        return $builder->orderBy('tanggal_setor', 'DESC')->findAll();
    }

    /**
     * Get setoran by masa pajak
     */
    public function getByMasaPajak($jenisPajak, $bulan, $tahun)
    {
        return $this->where('jenis_pajak', $jenisPajak)
                    ->where('masa_pajak', $bulan)
                    ->where('tahun_pajak', $tahun)
                    ->orderBy('tanggal_setor', 'ASC')
                    ->findAll();
    }

    /**
     * Get setoran by tahun
     */
    public function getByTahun($tahun)
    {
        return $this->where('tahun_pajak', $tahun)
                    ->orderBy('tanggal_setor', 'ASC')
                    ->findAll();
    }

    /**
     * Get total setoran per jenis pajak
     */
    public function getTotalPerJenis($tahun = null)
    {
        $builder = $this->select("
                jenis_pajak,
                COUNT(*) as jumlah_setoran,
                SUM(nominal) as total_nominal
            ")
            ->groupBy('jenis_pajak')
            ->orderBy('total_nominal', 'DESC');
        
        if ($tahun) {
            $builder->where('tahun_pajak', $tahun);
        }
        
        return $builder->findAll();
    }

    /**
     * Get total setoran per masa pajak
     */
    public function getTotalPerMasa($tahun = null)
    {
        $builder = $this->select("
                masa_pajak,
                tahun_pajak,
                jenis_pajak,
                COUNT(*) as jumlah_setoran,
                SUM(nominal) as total_nominal
            ")
            ->groupBy('masa_pajak, tahun_pajak, jenis_pajak')
            ->orderBy('tahun_pajak', 'DESC')
            ->orderBy('masa_pajak', 'DESC');
        
        if ($tahun) {
            $builder->where('tahun_pajak', $tahun);
        }
        
        return $builder->findAll();
    }

    /**
     * Get ringkasan setoran per bulan
     */
    public function getRingkasanPerBulan($tahun = null)
    {
        $builder = $this->select("
                DATE_FORMAT(tanggal_setor, '%Y-%m') as bulan,
                COUNT(*) as jumlah_setoran,
                SUM(nominal) as total_nominal,
                SUM(CASE WHEN jenis_pajak = 'PPN' THEN nominal ELSE 0 END) as total_ppn,
                SUM(CASE WHEN jenis_pajak = 'PPh 21' THEN nominal ELSE 0 END) as total_pph21,
                SUM(CASE WHEN jenis_pajak = 'PPh 23' THEN nominal ELSE 0 END) as total_pph23,
                SUM(CASE WHEN jenis_pajak = 'PPh 25' THEN nominal ELSE 0 END) as total_pph25,
                SUM(CASE WHEN jenis_pajak = 'PPh 29' THEN nominal ELSE 0 END) as total_pph29,
                SUM(CASE WHEN jenis_pajak = 'PPh Badan' THEN nominal ELSE 0 END) as total_pph_badan
            ")
            ->groupBy("DATE_FORMAT(tanggal_setor, '%Y-%m')")
            ->orderBy('bulan', 'ASC');
        
        if ($tahun) {
            $builder->where('tahun_pajak', $tahun);
        }
        
        return $builder->findAll();
    }

    /**
     * Get total setoran untuk SPT Masa
     */
    public function getTotalForSpt($jenisPajak, $bulan, $tahun)
    {
        $total = $this->select('SUM(nominal) as total')
                      ->where('jenis_pajak', $jenisPajak)
                      ->where('masa_pajak', $bulan)
                      ->where('tahun_pajak', $tahun)
                      ->first();
        
        return $total['total'] ?? 0;
    }

    /**
     * Get total setoran untuk SPT Tahunan
     */
    public function getTotalForSptTahunan($jenisPajak, $tahun)
    {
        $total = $this->select('SUM(nominal) as total')
                      ->where('jenis_pajak', $jenisPajak)
                      ->where('tahun_pajak', $tahun)
                      ->first();
        
        return $total['total'] ?? 0;
    }

    /**
     * Check if setoran already exists for period
     */
    public function existsForPeriod($jenisPajak, $bulan, $tahun)
    {
        $builder = $this->where('jenis_pajak', $jenisPajak)
                        ->where('tahun_pajak', $tahun);
        
        if ($jenisPajak !== 'PPh Badan') {
            $builder->where('masa_pajak', $bulan);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Create setoran from mutasi bank
     */
    public function createFromMutasiBank($mutasiBankId, $data = [])
    {
        $mutasiBankModel = new MutasiBankModel();
        $mutasi = $mutasiBankModel->find($mutasiBankId);
        
        if (!$mutasi) {
            throw new \RuntimeException('Mutasi bank tidak ditemukan');
        }
        
        if ($mutasi['tipe'] !== 'Debit') {
            throw new \RuntimeException('Setoran pajak harus dari transaksi Debit (pengeluaran)');
        }
        
        // Extract informasi pajak dari keterangan (bisa diperbaiki dengan parsing)
        $setoranData = [
            'jenis_pajak' => $data['jenis_pajak'] ?? 'PPN',
            'masa_pajak' => $data['masa_pajak'] ?? date('m', strtotime($mutasi['tanggal'])),
            'tahun_pajak' => $data['tahun_pajak'] ?? date('Y', strtotime($mutasi['tanggal'])),
            'tanggal_setor' => $data['tanggal_setor'] ?? $mutasi['tanggal'],
            'nominal' => $data['nominal'] ?? $mutasi['jumlah'],
            'no_bukti_setor' => $data['no_bukti_setor'] ?? $mutasi['no_referensi'],
            'no_ntpn' => $data['no_ntpn'] ?? null,
            'keterangan' => $data['keterangan'] ?? $mutasi['keterangan'],
            'mutasi_bank_id' => $mutasiBankId,
            'jurnal_id' => $mutasi['jurnal_id']
        ];
        
        return $this->insert($setoranData);
    }

    /**
     * Create setoran from journal
     */
    public function createFromJournal($jurnalId, $data = [])
    {
        $jurnalModel = new \App\Models\JurnalModel();
        $jurnal = $jurnalModel->find($jurnalId);
        
        if (!$jurnal) {
            throw new \RuntimeException('Jurnal tidak ditemukan');
        }
        
        $setoranData = [
            'jenis_pajak' => $data['jenis_pajak'],
            'masa_pajak' => $data['masa_pajak'] ?? date('m'),
            'tahun_pajak' => $data['tahun_pajak'] ?? date('Y'),
            'tanggal_setor' => $data['tanggal_setor'] ?? date('Y-m-d'),
            'nominal' => $data['nominal'],
            'no_bukti_setor' => $data['no_bukti_setor'] ?? null,
            'no_ntpn' => $data['no_ntpn'] ?? null,
            'keterangan' => $data['keterangan'] ?? $jurnal['keterangan'],
            'jurnal_id' => $jurnalId
        ];
        
        return $this->insert($setoranData);
    }

    /**
     * Get statistics untuk dashboard
     */
    public function getStats($tahun = null)
    {
        $builder = $this->select("
                COUNT(*) as total_setoran,
                SUM(nominal) as total_nominal,
                SUM(CASE WHEN jenis_pajak = 'PPN' THEN nominal ELSE 0 END) as total_ppn,
                SUM(CASE WHEN jenis_pajak = 'PPh 21' THEN nominal ELSE 0 END) as total_pph21,
                SUM(CASE WHEN jenis_pajak = 'PPh 23' THEN nominal ELSE 0 END) as total_pph23,
                SUM(CASE WHEN jenis_pajak = 'PPh 25' THEN nominal ELSE 0 END) as total_pph25,
                SUM(CASE WHEN jenis_pajak = 'PPh 29' THEN nominal ELSE 0 END) as total_pph29,
                SUM(CASE WHEN jenis_pajak = 'PPh Badan' THEN nominal ELSE 0 END) as total_pph_badan,
                COUNT(CASE WHEN no_ntpn IS NOT NULL THEN 1 END) as jumlah_ada_ntpn,
                COUNT(CASE WHEN no_ntpn IS NULL THEN 1 END) as jumlah_tanpa_ntpn
            ");
        
        if ($tahun) {
            $builder->where('tahun_pajak', $tahun);
        }
        
        $stats = $builder->first();
        
        return $stats ?? [
            'total_setoran' => 0,
            'total_nominal' => 0,
            'total_ppn' => 0,
            'total_pph21' => 0,
            'total_pph23' => 0,
            'total_pph25' => 0,
            'total_pph29' => 0,
            'total_pph_badan' => 0,
            'jumlah_ada_ntpn' => 0,
            'jumlah_tanpa_ntpn' => 0
        ];
    }

    /**
     * Get setoran untuk laporan pajak
     */
    public function getForTaxReport($jenisPajak, $tahun, $bulan = null)
    {
        $builder = $this->where('jenis_pajak', $jenisPajak)
                        ->where('tahun_pajak', $tahun);
        
        if ($bulan) {
            $builder->where('masa_pajak', $bulan);
        }
        
        return $builder->orderBy('tanggal_setor', 'ASC')->findAll();
    }

    /**
     * Export data untuk Excel
     */
    public function getExportData($filters = [])
    {
        $builder = $this->select('setoran_pajak.*, 
            creator.username as creator_name,
            mutasi_bank.kode_transaksi as kode_mutasi,
            jurnal.nomor_jurnal')
            ->join('users as creator', 'creator.id = setoran_pajak.created_by', 'left')
            ->join('mutasi_bank', 'mutasi_bank.id = setoran_pajak.mutasi_bank_id', 'left')
            ->join('jurnal', 'jurnal.id = setoran_pajak.jurnal_id', 'left');
        
        if (!empty($filters['tahun'])) {
            $builder->where('setoran_pajak.tahun_pajak', $filters['tahun']);
        }
        
        if (!empty($filters['jenis_pajak'])) {
            $builder->where('setoran_pajak.jenis_pajak', $filters['jenis_pajak']);
        }
        
        $setoran = $builder->orderBy('setoran_pajak.tanggal_setor', 'DESC')->findAll();
        
        $exportData = [];
        foreach ($setoran as $item) {
            $exportData[] = [
                'Jenis Pajak' => $item['jenis_pajak'],
                'Masa Pajak' => $item['masa_pajak'] ? $item['masa_pajak'] . '/' . $item['tahun_pajak'] : 'Tahunan ' . $item['tahun_pajak'],
                'Tanggal Setor' => $item['tanggal_setor'],
                'Nominal' => $item['nominal'],
                'No Bukti Setor' => $item['no_bukti_setor'] ?? '-',
                'NTPN' => $item['no_ntpn'] ?? '-',
                'Kode Mutasi' => $item['kode_mutasi'] ?? '-',
                'No Jurnal' => $item['nomor_jurnal'] ?? '-',
                'Keterangan' => $item['keterangan'] ?? '-',
                'Dibuat Oleh' => $item['creator_name'] ?? '-'
            ];
        }
        
        return $exportData;
    }

    /**
     * Update NTPN after payment
     */
    public function updateNtpn($id, $noNtpn)
    {
        $setoran = $this->find($id);
        
        if (!$setoran) {
            throw new \RuntimeException('Setoran pajak tidak ditemukan');
        }
        
        return $this->update($id, ['no_ntpn' => $noNtpn]);
    }

    /**
     * Get total setoran per jenis pajak for chart
     */
    public function getChartData($tahun = null)
    {
        $builder = $this->select("
                jenis_pajak,
                SUM(nominal) as total_nominal
            ")
            ->groupBy('jenis_pajak')
            ->orderBy('total_nominal', 'DESC');
        
        if ($tahun) {
            $builder->where('tahun_pajak', $tahun);
        }
        
        $result = $builder->findAll();
        
        $labels = [];
        $values = [];
        
        foreach ($result as $item) {
            $labels[] = $item['jenis_pajak'];
            $values[] = $item['total_nominal'];
        }
        
        return [
            'labels' => $labels,
            'values' => $values
        ];
    }
}