<?php
namespace App\Models\Accounting;

use CodeIgniter\Model;

class PenggajianKomponenModel extends Model
{
    protected $table = 'penggajian_komponen_gaji';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'kode_komponen',
        'nama_komponen',
        'tipe',
        'kategori',
        'coa_id',
        'rumus',
        'is_wajib',
        'is_aktif',
        'keterangan'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'kode_komponen' => 'required|is_unique[penggajian_komponen_gaji.kode_komponen]',
        'nama_komponen' => 'required',
        'tipe' => 'required|in_list[Pendapatan,Potongan]',
        'kategori' => 'permit_empty|in_list[Tetap,Variabel]',
        'coa_id' => 'permit_empty|is_natural_no_zero',
        'is_wajib' => 'permit_empty|in_list[0,1]',
        'is_aktif' => 'permit_empty|in_list[0,1]'
    ];

    protected $validationMessages = [
        'kode_komponen' => [
            'required' => 'Kode komponen wajib diisi',
            'is_unique' => 'Kode komponen sudah terdaftar'
        ],
        'nama_komponen' => [
            'required' => 'Nama komponen wajib diisi'
        ],
        'tipe' => [
            'required' => 'Tipe komponen (Pendapatan/Potongan) harus dipilih',
            'in_list' => 'Tipe komponen harus Pendapatan atau Potongan'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = ['setDefaultValues', 'setCreatedBy'];
    protected $beforeUpdate = ['setUpdatedBy', 'validateStatusChange'];

    /**
     * Set default values
     */
    protected function setDefaultValues(array $data)
    {
        if (!isset($data['data']['is_wajib'])) {
            $data['data']['is_wajib'] = 0;
        }
        
        if (!isset($data['data']['is_aktif'])) {
            $data['data']['is_aktif'] = 1;
        }
        
        if (!isset($data['data']['kategori'])) {
            $data['data']['kategori'] = 'Tetap';
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
     * Set updated_by
     */
    protected function setUpdatedBy(array $data)
    {
        $data['data']['updated_by'] = session()->get('user_id');
        return $data;
    }

    /**
     * Validasi perubahan status aktif
     */
    protected function validateStatusChange(array $data)
    {
        if (isset($data['data']['is_aktif'])) {
            $id = $data['id'][0] ?? null;
            
            if ($id) {
                $current = $this->find($id);
                
                // Jika menonaktifkan komponen, pastikan tidak ada data yang menggunakan
                if ($data['data']['is_aktif'] == 0 && $current && $current['is_aktif'] == 1) {
                    // Bisa ditambahkan validasi apakah ada perhitungan gaji yang menggunakan komponen ini
                    // Untuk sementara, biarkan saja
                }
            }
        }
        
        return $data;
    }

    /**
     * Get all komponen gaji dengan filter
     */
    public function getAllWithFilters($filters = [], $perPage = 20, $page = 1)
    {
        $builder = $this->select('penggajian_komponen_gaji.*, 
            coa.kode_akun as kode_akun_coa,
            coa.nama_akun as nama_akun_coa,
            coa.tipe_akun as tipe_akun_coa')
            ->join('coa', 'coa.id = penggajian_komponen_gaji.coa_id', 'left');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('penggajian_komponen_gaji.kode_komponen', $search)
                ->orLike('penggajian_komponen_gaji.nama_komponen', $search)
                ->orLike('penggajian_komponen_gaji.keterangan', $search)
                ->orLike('coa.kode_akun', $search)
                ->orLike('coa.nama_akun', $search)
                ->groupEnd();
        }
        
        if (!empty($filters['tipe'])) {
            $builder->where('penggajian_komponen_gaji.tipe', $filters['tipe']);
        }
        
        if (!empty($filters['kategori'])) {
            $builder->where('penggajian_komponen_gaji.kategori', $filters['kategori']);
        }
        
        if (isset($filters['is_aktif']) && $filters['is_aktif'] !== '') {
            $builder->where('penggajian_komponen_gaji.is_aktif', $filters['is_aktif']);
        }
        
        if (isset($filters['is_wajib']) && $filters['is_wajib'] !== '') {
            $builder->where('penggajian_komponen_gaji.is_wajib', $filters['is_wajib']);
        }
        
        $builder->orderBy('penggajian_komponen_gaji.tipe', 'ASC')
                ->orderBy('penggajian_komponen_gaji.kode_komponen', 'ASC');
        
        // Get total for pagination
        $total = $builder->countAllResults(false);
        
        // Get paginated results
        $offset = ($page - 1) * $perPage;
        $komponen = $builder->limit($perPage, $offset)->findAll();
        
        return [
            'data' => $komponen,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Get komponen by ID with details
     */
    public function getWithDetails($id)
    {
        $komponen = $this->select('penggajian_komponen_gaji.*, 
            coa.kode_akun as kode_akun_coa,
            coa.nama_akun as nama_akun_coa,
            coa.tipe_akun as tipe_akun_coa,
            coa.saldo_normal as saldo_normal_coa')
            ->join('coa', 'coa.id = penggajian_komponen_gaji.coa_id', 'left')
            ->where('penggajian_komponen_gaji.id', $id)
            ->first();
        
        return $komponen;
    }

    /**
     * Get komponen pendapatan (untuk dropdown)
     */
    public function getPendapatanOptions()
    {
        return $this->select('id, kode_komponen, nama_komponen')
            ->where('tipe', 'Pendapatan')
            ->where('is_aktif', 1)
            ->orderBy('kode_komponen', 'ASC')
            ->findAll();
    }

    /**
     * Get komponen potongan (untuk dropdown)
     */
    public function getPotonganOptions()
    {
        return $this->select('id, kode_komponen, nama_komponen')
            ->where('tipe', 'Potongan')
            ->where('is_aktif', 1)
            ->orderBy('kode_komponen', 'ASC')
            ->findAll();
    }

    /**
     * Get all active komponen gaji
     */
    public function getActive()
    {
        return $this->where('is_aktif', 1)
                    ->orderBy('tipe', 'ASC')
                    ->orderBy('kode_komponen', 'ASC')
                    ->findAll();
    }

    /**
     * Get komponen berdasarkan tipe
     */
    public function getByTipe($tipe)
    {
        return $this->where('tipe', $tipe)
                    ->where('is_aktif', 1)
                    ->orderBy('kode_komponen', 'ASC')
                    ->findAll();
    }

    /**
     * Get komponen wajib
     */
    public function getWajib()
    {
        return $this->where('is_wajib', 1)
                    ->where('is_aktif', 1)
                    ->orderBy('tipe', 'ASC')
                    ->orderBy('kode_komponen', 'ASC')
                    ->findAll();
    }

    /**
     * Toggle status aktif
     */
    public function toggleStatus($id)
    {
        $komponen = $this->find($id);
        
        if (!$komponen) {
            throw new \RuntimeException('Komponen gaji tidak ditemukan');
        }
        
        $newStatus = $komponen['is_aktif'] == 1 ? 0 : 1;
        
        return $this->update($id, ['is_aktif' => $newStatus]);
    }

    /**
     * Check if komponen is used in payroll calculations
     * (Untuk validasi sebelum delete atau nonaktifkan)
     */
    public function isUsedInPayroll($id)
    {
        // Cek apakah ada perhitungan gaji yang menggunakan komponen ini
        // Untuk sementara, asumsikan belum ada
        return false;
    }

    /**
     * Generate kode komponen otomatis
     * Format: 
     * - Untuk Pendapatan: PEND-XXXX
     * - Untuk Potongan: POT-XXXX
     */
    public function generateKodeKomponen($tipe)
    {
        $prefix = $tipe === 'Pendapatan' ? 'PEND' : 'POT';
        
        $last = $this->where('kode_komponen LIKE', $prefix . '-%')
                     ->orderBy('id', 'DESC')
                     ->first();
        
        if ($last) {
            $lastNumber = (int) substr($last['kode_komponen'], strlen($prefix) + 1);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . '-' . $newNumber;
    }

    /**
     * Get list for dropdown (simple)
     */
    public function getDropdown($tipe = null)
    {
        $builder = $this->select('id, kode_komponen, nama_komponen')
                       ->where('is_aktif', 1)
                       ->orderBy('kode_komponen', 'ASC');
        
        if ($tipe) {
            $builder->where('tipe', $tipe);
        }
        
        return $builder->findAll();
    }

    /**
     * Get COA options for dropdown
     */
    public function getCoaOptions($tipe = null)
    {
        $coaModel = new \App\Models\CoaModel();
        
        $builder = $coaModel->where('is_header', 0)
                            ->where('is_active', 1);
        
        if ($tipe === 'Pendapatan') {
            // Pendapatan: COA tipe Pendapatan (4-xxxx)
            $builder->like('kode_akun', '4-', 'after');
        } elseif ($tipe === 'Potongan') {
            // Potongan: COA tipe Beban (5-xxxx)
            $builder->like('kode_akun', '5-', 'after');
        } else {
            $builder->groupStart()
                ->like('kode_akun', '4-', 'after')
                ->orLike('kode_akun', '5-', 'after')
                ->groupEnd();
        }
        
        return $builder->orderBy('kode_akun', 'ASC')->findAll();
    }

    /**
     * Get statistics untuk dashboard
     */
    public function getStats()
    {
        $stats = $this->select("
                COUNT(*) as total_komponen,
                SUM(CASE WHEN tipe = 'Pendapatan' THEN 1 ELSE 0 END) as total_pendapatan,
                SUM(CASE WHEN tipe = 'Potongan' THEN 1 ELSE 0 END) as total_potongan,
                SUM(CASE WHEN is_aktif = 1 THEN 1 ELSE 0 END) as total_aktif,
                SUM(CASE WHEN is_wajib = 1 THEN 1 ELSE 0 END) as total_wajib
            ")
            ->first();
        
        return $stats ?? [
            'total_komponen' => 0,
            'total_pendapatan' => 0,
            'total_potongan' => 0,
            'total_aktif' => 0,
            'total_wajib' => 0
        ];
    }

    /**
     * Export data untuk Excel
     */
    public function getExportData($filters = [])
    {
        $builder = $this->select('penggajian_komponen_gaji.*, 
            coa.kode_akun as kode_akun_coa,
            coa.nama_akun as nama_akun_coa')
            ->join('coa', 'coa.id = penggajian_komponen_gaji.coa_id', 'left');
        
        if (!empty($filters['tipe'])) {
            $builder->where('penggajian_komponen_gaji.tipe', $filters['tipe']);
        }
        
        if (isset($filters['is_aktif']) && $filters['is_aktif'] !== '') {
            $builder->where('penggajian_komponen_gaji.is_aktif', $filters['is_aktif']);
        }
        
        $komponen = $builder->orderBy('penggajian_komponen_gaji.tipe', 'ASC')
                            ->orderBy('penggajian_komponen_gaji.kode_komponen', 'ASC')
                            ->findAll();
        
        $exportData = [];
        foreach ($komponen as $item) {
            $exportData[] = [
                'Kode Komponen' => $item['kode_komponen'],
                'Nama Komponen' => $item['nama_komponen'],
                'Tipe' => $item['tipe'],
                'Kategori' => $item['kategori'] ?? '-',
                'COA' => ($item['kode_akun_coa'] ?? '') . ' - ' . ($item['nama_akun_coa'] ?? ''),
                'Rumus' => $item['rumus'] ?? '-',
                'Wajib' => $item['is_wajib'] ? 'Ya' : 'Tidak',
                'Aktif' => $item['is_aktif'] ? 'Ya' : 'Tidak',
                'Keterangan' => $item['keterangan'] ?? '-'
            ];
        }
        
        return $exportData;
    }
}