<?php
namespace App\Models;

use CodeIgniter\Model;

class JurnalDetailModel extends Model
{
    protected $table = 'jurnal_detail';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    // PERBAIKAN: Pastikan hanya field yang ada di table
    protected $allowedFields = [
        'jurnal_id',
        'coa_id',
        'kode_akun',
        'nama_akun',
        'debit',
        'kredit',
        'keterangan',
        'created_at' // Tambahkan ini jika ada di table
    ];

    // PERBAIKAN: Nonaktifkan timestamps sementara untuk debugging
    protected $useTimestamps = false;
    
    // Atau jika table memiliki created_at tanpa updated_at:
    // protected $useTimestamps = true;
    // protected $dateFormat = 'datetime';
    // protected $createdField = 'created_at';
    // protected $updatedField = null;

    protected $validationRules = [
        'jurnal_id' => 'required|integer',
        'coa_id' => 'required|integer',
        'kode_akun' => 'required|max_length[20]',
        'nama_akun' => 'required|max_length[200]',
        'debit' => 'decimal|greater_than_equal_to[0]',
        'kredit' => 'decimal|greater_than_equal_to[0]'
    ];

    protected $validationMessages = [
        'coa_id' => [
            'required' => 'Akun harus dipilih'
        ],
        'debit' => [
            'greater_than_equal_to' => 'Nilai debit tidak boleh negatif'
        ],
        'kredit' => [
            'greater_than_equal_to' => 'Nilai kredit tidak boleh negatif'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = ['validateDebitKredit', 'setCreatedAt'];

    /**
     * Validate that either debit or kredit is filled (not both)
     */
    protected function validateDebitKredit(array $data)
    {
        $debit = (float)($data['data']['debit'] ?? 0);
        $kredit = (float)($data['data']['kredit'] ?? 0);
        
        if ($debit > 0 && $kredit > 0) {
            throw new \RuntimeException('Satu baris tidak boleh memiliki debit dan kredit sekaligus');
        }
        
        if ($debit == 0 && $kredit == 0) {
            throw new \RuntimeException('Satu baris harus memiliki nilai debit atau kredit');
        }
        
        return $data;
    }

    /**
     * Set created_at manually if timestamps disabled
     */
    protected function setCreatedAt(array $data)
    {
        if (!isset($data['data']['created_at'])) {
            $data['data']['created_at'] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    /**
     * Override save method untuk debugging
     */
    public function save($data = null): bool
    {
        try {
            // Log data yang akan disimpan
            log_message('debug', 'JurnalDetailModel save data: ' . print_r($data, true));
            
            // Jika data adalah array biasa (bukan entity)
            if (is_array($data)) {
                // Pastikan hanya field yang di allowedFields yang dikirim
                $filteredData = array_intersect_key($data, array_flip($this->allowedFields));
                
                // Debug filtered data
                log_message('debug', 'Filtered data for save: ' . print_r($filteredData, true));
                
                return parent::save($filteredData);
            }
            
            return parent::save($data);
        } catch (\Exception $e) {
            log_message('error', 'JurnalDetailModel save error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Insert data dengan SQL langsung untuk debugging
     */
    public function insertDirect(array $data): bool
    {
        try {
            $db = \Config\Database::connect();
            
            // Debug data
            log_message('debug', 'Insert direct data: ' . print_r($data, true));
            
            // Build query manual
            $builder = $db->table($this->table);
            
            // Pastikan hanya field yang valid
            $validData = [];
            foreach ($data as $key => $value) {
                if (in_array($key, $this->allowedFields)) {
                    $validData[$key] = $value;
                }
            }
            
            // Tambahkan created_at jika belum ada
            if (!isset($validData['created_at']) && in_array('created_at', $this->allowedFields)) {
                $validData['created_at'] = date('Y-m-d H:i:s');
            }
            
            log_message('debug', 'Valid data for insert: ' . print_r($validData, true));
            
            return $builder->insert($validData);
        } catch (\Exception $e) {
            log_message('error', 'Insert direct error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get details by jurnal_id
     */
  public function getByJurnalId($jurnalId)
    {
        return $this->where('jurnal_id', $jurnalId)->findAll();
    }

    /**
     * Get summary by COA
     */
    public function getSummaryByCoa($startDate = null, $endDate = null)
    {
        $builder = $this->select('
                jurnal_detail.kode_akun,
                jurnal_detail.nama_akun,
                SUM(jurnal_detail.debit) as total_debit,
                SUM(jurnal_detail.kredit) as total_kredit,
                coa.tipe_akun,
                coa.saldo_normal
            ')
            ->join('jurnal', 'jurnal.id = jurnal_detail.jurnal_id')
            ->join('coa', 'coa.id = jurnal_detail.coa_id')
            ->where('jurnal.status', 'posted');
        
        if ($startDate) {
            $builder->where('jurnal.tanggal >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('jurnal.tanggal <=', $endDate);
        }
        
        return $builder->groupBy('jurnal_detail.coa_id, jurnal_detail.kode_akun')
            ->orderBy('jurnal_detail.kode_akun', 'ASC')
            ->findAll();
    }

    /**
     * Delete all details for a jurnal
     */
    public function deleteByJurnalId($jurnalId)
    {
        return $this->where('jurnal_id', $jurnalId)->delete();
    }

    /**
     * Get total debit and kredit for jurnal
     */
    public function getTotalsForJurnal($jurnalId)
    {
        $result = $this->select('SUM(debit) as total_debit, SUM(kredit) as total_kredit')
            ->where('jurnal_id', $jurnalId)
            ->first();
        
        return [
            'debit' => (float)($result['total_debit'] ?? 0),
            'kredit' => (float)($result['total_kredit'] ?? 0)
        ];
    }

    /**
     * Validate that all COA IDs exist
     */
    public function validateCoaIds(array $coaIds)
    {
        $coaModel = new CoaModel();
        $existingIds = $coaModel->whereIn('id', $coaIds)
            ->where('is_active', 1)
            ->findColumn('id');
        
        return $existingIds ?: [];
    }

    /**
     * Save batch details dengan error handling yang lebih baik
     */
    public function saveBatchDetails(array $details): bool
    {
        try {
            $db = \Config\Database::connect();
            
            foreach ($details as $detail) {
                // Pastikan format data benar
                $data = [
                    'jurnal_id' => $detail['jurnal_id'] ?? 0,
                    'coa_id' => $detail['coa_id'] ?? 0,
                    'kode_akun' => $detail['kode_akun'] ?? '',
                    'nama_akun' => $detail['nama_akun'] ?? '',
                    'debit' => (float)($detail['debit'] ?? 0),
                    'kredit' => (float)($detail['kredit'] ?? 0),
                    'keterangan' => $detail['keterangan'] ?? '',
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                log_message('debug', 'Saving detail row: ' . print_r($data, true));
                
                if (!$this->insertDirect($data)) {
                    throw new \RuntimeException('Gagal menyimpan detail jurnal');
                }
            }
            
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Save batch details error: ' . $e->getMessage());
            return false;
        }
    }
}
?>