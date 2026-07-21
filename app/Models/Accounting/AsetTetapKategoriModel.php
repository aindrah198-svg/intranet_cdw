<?php
namespace App\Models\Accounting;

use CodeIgniter\Model;

class AsetTetapKategoriModel extends Model
{
    protected $table = 'aset_tetap_kategori';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'kode_kategori',
        'nama_kategori',
        'masa_manfaat',
        'metode_penyusutan',
        'persentase_penyusutan',
        'coa_aset_id',
        'coa_akumulasi_id',
        'coa_beban_id',
        'keterangan',
        'is_active'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'kode_kategori' => 'required|is_unique[aset_tetap_kategori.kode_kategori]',
        'nama_kategori' => 'required',
        'masa_manfaat' => 'permit_empty|is_natural|greater_than[0]',
        'metode_penyusutan' => 'permit_empty|in_list[Garis Lurus,Saldo Menurun,Unit Produksi]',
        'persentase_penyusutan' => 'permit_empty|numeric|greater_than[0]|less_than_equal_to[100]',
        'coa_aset_id' => 'permit_empty|is_natural_no_zero',
        'coa_akumulasi_id' => 'permit_empty|is_natural_no_zero',
        'coa_beban_id' => 'permit_empty|is_natural_no_zero',
        'is_active' => 'permit_empty|in_list[0,1]'
    ];

    protected $validationMessages = [
        'kode_kategori' => [
            'required' => 'Kode kategori harus diisi',
            'is_unique' => 'Kode kategori sudah terdaftar'
        ],
        'nama_kategori' => [
            'required' => 'Nama kategori harus diisi'
        ],
        'masa_manfaat' => [
            'is_natural' => 'Masa manfaat harus berupa angka positif',
            'greater_than' => 'Masa manfaat harus lebih dari 0'
        ],
        'persentase_penyusutan' => [
            'numeric' => 'Persentase penyusutan harus berupa angka',
            'greater_than' => 'Persentase penyusutan harus lebih dari 0',
            'less_than_equal_to' => 'Persentase penyusutan maksimal 100%'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateKodeKategori', 'setDefaultValues', 'setCreatedBy'];
    protected $beforeUpdate = ['setUpdatedBy', 'validateStatusChange'];

    /**
     * Generate kode kategori otomatis
     * Format: KAT-XXXX (contoh: KAT-0001, KAT-0002)
     */
    protected function generateKodeKategori(array $data)
    {
        if (empty($data['data']['kode_kategori'])) {
            $prefix = 'KAT-';
            
            // Cari sequence terakhir
            $last = $this->where('kode_kategori LIKE', $prefix . '%')
                         ->orderBy('kode_kategori', 'DESC')
                         ->first();
            
            if ($last) {
                $lastNum = (int) substr($last['kode_kategori'], strlen($prefix));
                $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNum = '0001';
            }
            
            $data['data']['kode_kategori'] = $prefix . $nextNum;
        }
        
        return $data;
    }

    /**
     * Set default values
     */
    protected function setDefaultValues(array $data)
    {
        if (!isset($data['data']['is_active'])) {
            $data['data']['is_active'] = 1;
        }
        
        if (!isset($data['data']['metode_penyusutan'])) {
            $data['data']['metode_penyusutan'] = 'Garis Lurus';
        }
        
        if (!isset($data['data']['masa_manfaat'])) {
            $data['data']['masa_manfaat'] = 5; // Default 5 tahun
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
        if (isset($data['data']['is_active'])) {
            $id = $data['id'][0] ?? null;
            
            if ($id) {
                $current = $this->find($id);
                
                // Jika menonaktifkan kategori, cek apakah ada aset yang menggunakan
                if ($data['data']['is_active'] == 0 && $current && $current['is_active'] == 1) {
                    $asetModel = new AsetTetapModel();
                    $asetCount = $asetModel->where('kategori_id', $id)
                                           ->where('deleted_at IS NULL')
                                           ->countAllResults();
                    
                    if ($asetCount > 0) {
                        throw new \RuntimeException('Kategori ini masih memiliki ' . $asetCount . ' aset yang terdaftar. Nonaktifkan atau pindahkan aset tersebut terlebih dahulu.');
                    }
                }
            }
        }
        
        return $data;
    }

    /**
     * Get all kategori with filters
     */
    public function getAllWithFilters($filters = [], $perPage = 20, $page = 1)
    {
        $builder = $this->select('aset_tetap_kategori.*, 
            coa_aset.kode_akun as kode_akun_aset,
            coa_aset.nama_akun as nama_akun_aset,
            coa_akumulasi.kode_akun as kode_akun_akumulasi,
            coa_akumulasi.nama_akun as nama_akun_akumulasi,
            coa_beban.kode_akun as kode_akun_beban,
            coa_beban.nama_akun as nama_akun_beban,
            creator.username as creator_name')
            ->join('coa as coa_aset', 'coa_aset.id = aset_tetap_kategori.coa_aset_id', 'left')
            ->join('coa as coa_akumulasi', 'coa_akumulasi.id = aset_tetap_kategori.coa_akumulasi_id', 'left')
            ->join('coa as coa_beban', 'coa_beban.id = aset_tetap_kategori.coa_beban_id', 'left')
            ->join('users as creator', 'creator.id = aset_tetap_kategori.created_by', 'left');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('aset_tetap_kategori.kode_kategori', $search)
                ->orLike('aset_tetap_kategori.nama_kategori', $search)
                ->orLike('aset_tetap_kategori.keterangan', $search)
                ->groupEnd();
        }
        
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $builder->where('aset_tetap_kategori.is_active', $filters['is_active']);
        }
        
        if (!empty($filters['metode_penyusutan'])) {
            $builder->where('aset_tetap_kategori.metode_penyusutan', $filters['metode_penyusutan']);
        }
        
        $builder->orderBy('aset_tetap_kategori.kode_kategori', 'ASC');
        
        // Get total for pagination
        $total = $builder->countAllResults(false);
        
        // Get paginated results
        $offset = ($page - 1) * $perPage;
        $kategori = $builder->limit($perPage, $offset)->findAll();
        
        return [
            'data' => $kategori,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Get kategori by ID with details
     */
    public function getWithDetails($id)
    {
        $kategori = $this->select('aset_tetap_kategori.*, 
            coa_aset.kode_akun as kode_akun_aset,
            coa_aset.nama_akun as nama_akun_aset,
            coa_aset.tipe_akun as tipe_akun_aset,
            coa_akumulasi.kode_akun as kode_akun_akumulasi,
            coa_akumulasi.nama_akun as nama_akun_akumulasi,
            coa_akumulasi.tipe_akun as tipe_akun_akumulasi,
            coa_beban.kode_akun as kode_akun_beban,
            coa_beban.nama_akun as nama_akun_beban,
            coa_beban.tipe_akun as tipe_akun_beban,
            creator.username as creator_name,
            updater.username as updater_name')
            ->join('coa as coa_aset', 'coa_aset.id = aset_tetap_kategori.coa_aset_id', 'left')
            ->join('coa as coa_akumulasi', 'coa_akumulasi.id = aset_tetap_kategori.coa_akumulasi_id', 'left')
            ->join('coa as coa_beban', 'coa_beban.id = aset_tetap_kategori.coa_beban_id', 'left')
            ->join('users as creator', 'creator.id = aset_tetap_kategori.created_by', 'left')
            ->join('users as updater', 'updater.id = aset_tetap_kategori.updated_by', 'left')
            ->where('aset_tetap_kategori.id', $id)
            ->first();
        
        if ($kategori) {
            // Hitung jumlah aset dalam kategori ini
            $asetModel = new AsetTetapModel();
            $kategori['total_aset'] = $asetModel->where('kategori_id', $id)
                                                ->where('deleted_at IS NULL')
                                                ->countAllResults();
            
            $kategori['total_nilai_perolehan'] = $asetModel->where('kategori_id', $id)
                                                           ->where('deleted_at IS NULL')
                                                           ->selectSum('harga_perolehan')
                                                           ->first()['harga_perolehan'] ?? 0;
        }
        
        return $kategori;
    }

    /**
     * Get active kategori for dropdown
     */
    public function getActiveOptions()
    {
        return $this->select('id, kode_kategori, nama_kategori, masa_manfaat, metode_penyusutan, persentase_penyusutan')
                    ->where('is_active', 1)
                    ->orderBy('kode_kategori', 'ASC')
                    ->findAll();
    }

    /**
     * Get all active kategori
     */
    public function getActive()
    {
        return $this->where('is_active', 1)
                    ->orderBy('kode_kategori', 'ASC')
                    ->findAll();
    }

    /**
     * Get kategori by ID
     */
    public function getById($id)
    {
        return $this->where('id', $id)
                    ->where('is_active', 1)
                    ->first();
    }

    /**
     * Toggle status aktif
     */
    public function toggleStatus($id)
    {
        $kategori = $this->find($id);
        
        if (!$kategori) {
            throw new \RuntimeException('Kategori aset tidak ditemukan');
        }
        
        $newStatus = $kategori['is_active'] == 1 ? 0 : 1;
        
        return $this->update($id, ['is_active' => $newStatus]);
    }

    /**
     * Check if kategori can be deleted (no aset attached)
     */
    public function canDelete($id)
    {
        $asetModel = new AsetTetapModel();
        $asetCount = $asetModel->where('kategori_id', $id)
                               ->where('deleted_at IS NULL')
                               ->countAllResults();
        
        return $asetCount == 0;
    }

    /**
     * Get COA options for dropdown
     */
    public function getCoaAsetOptions()
    {
        $coaModel = new \App\Models\CoaModel();
        
        return $coaModel->where('is_header', 0)
                        ->where('is_active', 1)
                        ->where('tipe_akun', 'Aset')
                        ->like('kode_akun', '1-2', 'after')
                        ->orderBy('kode_akun', 'ASC')
                        ->findAll();
    }

    /**
     * Get COA akumulasi options
     */
    public function getCoaAkumulasiOptions()
    {
        $coaModel = new \App\Models\CoaModel();
        
        return $coaModel->where('is_header', 0)
                        ->where('is_active', 1)
                        ->where('tipe_akun', 'Aset')
                        ->like('kode_akun', '1-23', 'after')
                        ->orderBy('kode_akun', 'ASC')
                        ->findAll();
    }

    /**
     * Get COA beban options
     */
    public function getCoaBebanOptions()
    {
        $coaModel = new \App\Models\CoaModel();
        
        return $coaModel->where('is_header', 0)
                        ->where('is_active', 1)
                        ->where('tipe_akun', 'Beban')
                        ->like('kode_akun', '5-17', 'after')
                        ->orderBy('kode_akun', 'ASC')
                        ->findAll();
    }

    /**
     * Get statistics untuk dashboard
     */
    public function getStats()
    {
        $stats = $this->select("
                COUNT(*) as total_kategori,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as total_aktif,
                SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as total_nonaktif
            ")
            ->first();
        
        return $stats ?? [
            'total_kategori' => 0,
            'total_aktif' => 0,
            'total_nonaktif' => 0
        ];
    }

    /**
     * Get rekap penyusutan per kategori
     */
    public function getRekapPenyusutan()
    {
        $kategori = $this->where('is_active', 1)->findAll();
        
        $asetModel = new AsetTetapModel();
        $penyusutanModel = new PenyusutanModel();
        
        foreach ($kategori as &$item) {
            // Total nilai perolehan aset dalam kategori ini
            $totalPerolehan = $asetModel->where('kategori_id', $item['id'])
                                        ->where('status', 'Aktif')
                                        ->selectSum('harga_perolehan')
                                        ->first()['harga_perolehan'] ?? 0;
            $item['total_perolehan'] = $totalPerolehan;
            
            // Total akumulasi penyusutan
            $totalAkumulasi = $penyusutanModel->getTotalAkumulasiByKategori($item['id']);
            $item['total_akumulasi'] = $totalAkumulasi;
            
            // Nilai buku
            $item['nilai_buku'] = $totalPerolehan - $totalAkumulasi;
        }
        
        return $kategori;
    }

    /**
     * Export data untuk Excel
     */
    public function getExportData($filters = [])
    {
        $builder = $this->select('aset_tetap_kategori.*, 
            coa_aset.kode_akun as kode_akun_aset,
            coa_aset.nama_akun as nama_akun_aset,
            coa_akumulasi.kode_akun as kode_akun_akumulasi,
            coa_akumulasi.nama_akun as nama_akun_akumulasi,
            coa_beban.kode_akun as kode_akun_beban,
            coa_beban.nama_akun as nama_akun_beban')
            ->join('coa as coa_aset', 'coa_aset.id = aset_tetap_kategori.coa_aset_id', 'left')
            ->join('coa as coa_akumulasi', 'coa_akumulasi.id = aset_tetap_kategori.coa_akumulasi_id', 'left')
            ->join('coa as coa_beban', 'coa_beban.id = aset_tetap_kategori.coa_beban_id', 'left');
        
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $builder->where('aset_tetap_kategori.is_active', $filters['is_active']);
        }
        
        $kategori = $builder->orderBy('aset_tetap_kategori.kode_kategori', 'ASC')->findAll();
        
        $exportData = [];
        foreach ($kategori as $item) {
            $exportData[] = [
                'Kode Kategori' => $item['kode_kategori'],
                'Nama Kategori' => $item['nama_kategori'],
                'Masa Manfaat (Tahun)' => $item['masa_manfaat'] ?? '-',
                'Metode Penyusutan' => $item['metode_penyusutan'],
                'Persentase Penyusutan (%)' => $item['persentase_penyusutan'] ?? '-',
                'COA Aset' => ($item['kode_akun_aset'] ?? '') . ' - ' . ($item['nama_akun_aset'] ?? ''),
                'COA Akumulasi' => ($item['kode_akun_akumulasi'] ?? '') . ' - ' . ($item['nama_akun_akumulasi'] ?? ''),
                'COA Beban' => ($item['kode_akun_beban'] ?? '') . ' - ' . ($item['nama_akun_beban'] ?? ''),
                'Keterangan' => $item['keterangan'] ?? '-',
                'Status' => $item['is_active'] ? 'Aktif' : 'Nonaktif',
                'Dibuat Tanggal' => $item['created_at']
            ];
        }
        
        return $exportData;
    }

    /**
     * Get default penyusutan rate per tahun (dalam persen)
     */
    public function getPenyusutanRate($kategoriId)
    {
        $kategori = $this->find($kategoriId);
        
        if (!$kategori) {
            return 0;
        }
        
        // Jika sudah ada persentase yang ditentukan
        if (!empty($kategori['persentase_penyusutan'])) {
            return $kategori['persentase_penyusutan'];
        }
        
        // Hitung berdasarkan metode garis lurus
        if ($kategori['metode_penyusutan'] === 'Garis Lurus' && !empty($kategori['masa_manfaat'])) {
            return (100 / $kategori['masa_manfaat']);
        }
        
        return 0;
    }

    /**
     * Get masa manfaat dalam tahun
     */
    public function getMasaManfaat($kategoriId)
    {
        $kategori = $this->find($kategoriId);
        return $kategori ? ($kategori['masa_manfaat'] ?? 5) : 5;
    }

    /**
     * Get metode penyusutan
     */
    public function getMetodePenyusutan($kategoriId)
    {
        $kategori = $this->find($kategoriId);
        return $kategori ? ($kategori['metode_penyusutan'] ?? 'Garis Lurus') : 'Garis Lurus';
    }

    /**
     * Get COA IDs for a kategori
     */
    public function getCoaIds($kategoriId)
    {
        $kategori = $this->find($kategoriId);
        
        if (!$kategori) {
            return [
                'coa_aset_id' => null,
                'coa_akumulasi_id' => null,
                'coa_beban_id' => null
            ];
        }
        
        return [
            'coa_aset_id' => $kategori['coa_aset_id'],
            'coa_akumulasi_id' => $kategori['coa_akumulasi_id'],
            'coa_beban_id' => $kategori['coa_beban_id']
        ];
    }

    /**
     * Validate if kategori has complete COA configuration
     */
    public function hasCompleteCoa($kategoriId)
    {
        $kategori = $this->find($kategoriId);
        
        if (!$kategori) {
            return false;
        }
        
        return !empty($kategori['coa_aset_id']) && 
               !empty($kategori['coa_akumulasi_id']) && 
               !empty($kategori['coa_beban_id']);
    }
}