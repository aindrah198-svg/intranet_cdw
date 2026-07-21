<?php
namespace App\Models\Accounting;

use CodeIgniter\Model;

class TarifPajakModel extends Model
{
    protected $table = 'tarif_pajak';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'jenis_pajak',
        'kode_tarif',
        'nama_tarif',
        'persentase',
        'berlaku_mulai',
        'berlaku_sampai',
        'keterangan',
        'is_active'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'jenis_pajak' => 'required|in_list[PPN,PPh 21,PPh 23,PPh 25,PPh 29,PPh Badan,Lainnya]',
        'kode_tarif' => 'required|is_unique[tarif_pajak.kode_tarif]',
        'nama_tarif' => 'required',
        'persentase' => 'required|numeric|greater_than[0]|less_than_equal_to[100]',
        'berlaku_mulai' => 'required|valid_date',
        'berlaku_sampai' => 'permit_empty|valid_date',
        'is_active' => 'permit_empty|in_list[0,1]'
    ];

    protected $validationMessages = [
        'jenis_pajak' => [
            'required' => 'Jenis pajak harus dipilih',
            'in_list' => 'Jenis pajak tidak valid'
        ],
        'kode_tarif' => [
            'required' => 'Kode tarif harus diisi',
            'is_unique' => 'Kode tarif sudah terdaftar'
        ],
        'nama_tarif' => [
            'required' => 'Nama tarif harus diisi'
        ],
        'persentase' => [
            'required' => 'Persentase tarif harus diisi',
            'numeric' => 'Persentase harus berupa angka',
            'greater_than' => 'Persentase harus lebih dari 0',
            'less_than_equal_to' => 'Persentase maksimal 100%'
        ],
        'berlaku_mulai' => [
            'required' => 'Tanggal berlaku mulai harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'berlaku_sampai' => [
            'valid_date' => 'Format tanggal tidak valid'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateKodeTarif', 'setDefaultValues', 'validateBerlaku', 'setActiveStatus'];
    protected $beforeUpdate = ['validateBerlaku', 'validateOverlapping', 'setActiveStatus'];

    /**
     * Generate kode tarif otomatis
     * Format: TARIF-JENIS-XXXX
     */
    protected function generateKodeTarif(array $data)
    {
        if (empty($data['data']['kode_tarif'])) {
            $jenis = $data['data']['jenis_pajak'] ?? 'PPN';
            $prefix = $this->getKodePrefix($jenis);
            
            // Cari sequence terakhir untuk jenis ini
            $last = $this->where('kode_tarif LIKE', $prefix . '%')
                         ->orderBy('kode_tarif', 'DESC')
                         ->first();
            
            if ($last) {
                $lastNum = (int) substr($last['kode_tarif'], strlen($prefix));
                $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNum = '0001';
            }
            
            $data['data']['kode_tarif'] = $prefix . $nextNum;
        }
        
        return $data;
    }

    /**
     * Get kode prefix berdasarkan jenis pajak
     */
    private function getKodePrefix($jenis)
    {
        $prefixes = [
            'PPN' => 'TARIF-PPN-',
            'PPh 21' => 'TARIF-PPH21-',
            'PPh 23' => 'TARIF-PPH23-',
            'PPh 25' => 'TARIF-PPH25-',
            'PPh 29' => 'TARIF-PPH29-',
            'PPh Badan' => 'TARIF-PPHB-',
            'Lainnya' => 'TARIF-LAIN-'
        ];
        
        return $prefixes[$jenis] ?? 'TARIF-';
    }

    /**
     * Set default values
     */
    protected function setDefaultValues(array $data)
    {
        if (!isset($data['data']['is_active'])) {
            $data['data']['is_active'] = 1;
        }
        
        return $data;
    }

    /**
     * Set active status based on berlaku dates
     */
    protected function setActiveStatus(array $data)
    {
        $berlakuMulai = $data['data']['berlaku_mulai'] ?? null;
        $berlakuSampai = $data['data']['berlaku_sampai'] ?? null;
        $today = date('Y-m-d');
        
        if ($berlakuMulai) {
            if ($berlakuSampai) {
                // Jika ada tanggal berakhir, aktif jika hari ini dalam rentang
                $data['data']['is_active'] = ($berlakuMulai <= $today && $today <= $berlakuSampai) ? 1 : 0;
            } else {
                // Jika tidak ada tanggal berakhir, aktif jika sudah berlaku
                $data['data']['is_active'] = ($berlakuMulai <= $today) ? 1 : 0;
            }
        }
        
        return $data;
    }

    /**
     * Validasi tanggal berlaku
     */
    protected function validateBerlaku(array $data)
    {
        $berlakuMulai = $data['data']['berlaku_mulai'] ?? null;
        $berlakuSampai = $data['data']['berlaku_sampai'] ?? null;
        
        if ($berlakuMulai && $berlakuSampai) {
            if ($berlakuSampai < $berlakuMulai) {
                throw new \RuntimeException('Tanggal berlaku sampai harus lebih besar atau sama dengan tanggal berlaku mulai');
            }
        }
        
        return $data;
    }

    /**
     * Validasi overlapping tarif untuk jenis pajak yang sama
     */
    protected function validateOverlapping(array $data)
    {
        $id = $data['id'][0] ?? null;
        $jenis = $data['data']['jenis_pajak'] ?? null;
        $berlakuMulai = $data['data']['berlaku_mulai'] ?? null;
        $berlakuSampai = $data['data']['berlaku_sampai'] ?? null;
        
        if ($jenis && $berlakuMulai) {
            $builder = $this->where('jenis_pajak', $jenis);
            
            if ($id) {
                $builder->where('id !=', $id);
            }
            
            $existing = $builder->findAll();
            
            foreach ($existing as $tarif) {
                $start1 = strtotime($berlakuMulai);
                $end1 = $berlakuSampai ? strtotime($berlakuSampai) : null;
                $start2 = strtotime($tarif['berlaku_mulai']);
                $end2 = $tarif['berlaku_sampai'] ? strtotime($tarif['berlaku_sampai']) : null;
                
                // Cek overlap
                $overlap = false;
                if ($end1 && $end2) {
                    $overlap = ($start1 <= $end2 && $end1 >= $start2);
                } elseif ($end1 && !$end2) {
                    $overlap = ($start1 <= $start2 && $end1 >= $start2);
                } elseif (!$end1 && $end2) {
                    $overlap = ($start1 <= $end2);
                } else {
                    $overlap = true;
                }
                
                if ($overlap) {
                    $namaTarif = $tarif['nama_tarif'];
                    $periode = $tarif['berlaku_mulai'];
                    if ($tarif['berlaku_sampai']) {
                        $periode .= ' s/d ' . $tarif['berlaku_sampai'];
                    } else {
                        $periode .= ' s/d seterusnya';
                    }
                    throw new \RuntimeException("Tarif untuk jenis pajak {$jenis} sudah ada dengan nama '{$namaTarif}' yang berlaku pada periode {$periode}");
                }
            }
        }
        
        return $data;
    }

    /**
     * Get all tarif pajak with filters
     */
    public function getAllWithFilters($filters = [], $perPage = 20, $page = 1)
    {
        $builder = $this->select('tarif_pajak.*')
                        ->orderBy('jenis_pajak', 'ASC')
                        ->orderBy('berlaku_mulai', 'DESC');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('kode_tarif', $search)
                ->orLike('nama_tarif', $search)
                ->orLike('keterangan', $search)
                ->groupEnd();
        }
        
        if (!empty($filters['jenis_pajak'])) {
            $builder->where('jenis_pajak', $filters['jenis_pajak']);
        }
        
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $builder->where('is_active', $filters['is_active']);
        }
        
        if (!empty($filters['berlaku_mulai'])) {
            $builder->where('berlaku_mulai <=', $filters['berlaku_mulai']);
        }
        
        if (!empty($filters['berlaku_sampai'])) {
            $builder->where('berlaku_sampai >=', $filters['berlaku_sampai']);
        }
        
        // Get total for pagination
        $total = $builder->countAllResults(false);
        
        // Get paginated results
        $offset = ($page - 1) * $perPage;
        $tarif = $builder->limit($perPage, $offset)->findAll();
        
        return [
            'data' => $tarif,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Get tarif pajak by ID
     */
    public function getById($id)
    {
        return $this->find($id);
    }

    /**
     * Get active tarif for specific jenis pajak
     */
    public function getActiveTarif($jenisPajak, $tanggal = null)
    {
        $tanggal = $tanggal ?: date('Y-m-d');
        
        return $this->where('jenis_pajak', $jenisPajak)
                    ->where('berlaku_mulai <=', $tanggal)
                    ->groupStart()
                        ->where('berlaku_sampai >=', $tanggal)
                        ->orWhere('berlaku_sampai IS NULL')
                    ->groupEnd()
                    ->where('is_active', 1)
                    ->orderBy('berlaku_mulai', 'DESC')
                    ->first();
    }

    /**
     * Get all active tarif
     */
    public function getActive()
    {
        $today = date('Y-m-d');
        
        return $this->where('berlaku_mulai <=', $today)
                    ->groupStart()
                        ->where('berlaku_sampai >=', $today)
                        ->orWhere('berlaku_sampai IS NULL')
                    ->groupEnd()
                    ->where('is_active', 1)
                    ->orderBy('jenis_pajak', 'ASC')
                    ->orderBy('berlaku_mulai', 'DESC')
                    ->findAll();
    }

    /**
     * Get tarif options for dropdown
     */
    public function getOptions($jenisPajak = null)
    {
        $today = date('Y-m-d');
        $builder = $this->select('id, kode_tarif, nama_tarif, persentase')
                        ->where('berlaku_mulai <=', $today)
                        ->groupStart()
                            ->where('berlaku_sampai >=', $today)
                            ->orWhere('berlaku_sampai IS NULL')
                        ->groupEnd()
                        ->where('is_active', 1)
                        ->orderBy('berlaku_mulai', 'DESC');
        
        if ($jenisPajak) {
            $builder->where('jenis_pajak', $jenisPajak);
        }
        
        return $builder->findAll();
    }

    /**
     * Get current rate for specific jenis pajak
     */
    public function getCurrentRate($jenisPajak)
    {
        $tarif = $this->getActiveTarif($jenisPajak);
        return $tarif ? $tarif['persentase'] : 0;
    }

    /**
     * Get PPN rate
     */
    public function getPpnRate()
    {
        return $this->getCurrentRate('PPN');
    }

    /**
     * Get PPh Badan rate
     */
    public function getPphBadanRate()
    {
        return $this->getCurrentRate('PPh Badan');
    }

    /**
     * Get PPh 21 rate by bracket (progressive)
     */
    public function getPph21Rates()
    {
        $today = date('Y-m-d');
        
        return $this->where('jenis_pajak', 'PPh 21')
                    ->where('berlaku_mulai <=', $today)
                    ->groupStart()
                        ->where('berlaku_sampai >=', $today)
                        ->orWhere('berlaku_sampai IS NULL')
                    ->groupEnd()
                    ->where('is_active', 1)
                    ->orderBy('persentase', 'ASC')
                    ->findAll();
    }

    /**
     * Get PPh 23 rates
     */
    public function getPph23Rates()
    {
        $today = date('Y-m-d');
        
        return $this->where('jenis_pajak', 'PPh 23')
                    ->where('berlaku_mulai <=', $today)
                    ->groupStart()
                        ->where('berlaku_sampai >=', $today)
                        ->orWhere('berlaku_sampai IS NULL')
                    ->groupEnd()
                    ->where('is_active', 1)
                    ->orderBy('persentase', 'ASC')
                    ->findAll();
    }

    /**
     * Toggle active status
     */
    public function toggleStatus($id)
    {
        $tarif = $this->find($id);
        
        if (!$tarif) {
            throw new \RuntimeException('Tarif pajak tidak ditemukan');
        }
        
        $newStatus = $tarif['is_active'] == 1 ? 0 : 1;
        
        return $this->update($id, ['is_active' => $newStatus]);
    }

    /**
     * Get statistics untuk dashboard
     */
    public function getStats()
    {
        $stats = $this->select("
                COUNT(*) as total_tarif,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as total_aktif,
                SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as total_nonaktif,
                COUNT(DISTINCT jenis_pajak) as total_jenis
            ")
            ->first();
        
        // Tambah tarif per jenis
        $stats['per_jenis'] = $this->select('jenis_pajak, COUNT(*) as jumlah, AVG(persentase) as rata_rata')
                                   ->groupBy('jenis_pajak')
                                   ->findAll();
        
        return $stats ?? [
            'total_tarif' => 0,
            'total_aktif' => 0,
            'total_nonaktif' => 0,
            'total_jenis' => 0,
            'per_jenis' => []
        ];
    }

    /**
     * Get riwayat perubahan tarif per jenis pajak
     */
    public function getRiwayatPerJenis($jenisPajak)
    {
        return $this->where('jenis_pajak', $jenisPajak)
                    ->orderBy('berlaku_mulai', 'DESC')
                    ->findAll();
    }

    /**
     * Get all jenis pajak options
     */
    public function getJenisPajakOptions()
    {
        $jenis = $this->select('jenis_pajak')
                      ->groupBy('jenis_pajak')
                      ->orderBy('jenis_pajak', 'ASC')
                      ->findAll();
        
        $options = [];
        foreach ($jenis as $item) {
            $options[] = $item['jenis_pajak'];
        }
        
        return $options;
    }

    /**
     * Hitung pajak berdasarkan tarif
     */
    public function hitungPajak($jenisPajak, $nilaiDasar, $tanggal = null)
    {
        $tarif = $this->getActiveTarif($jenisPajak, $tanggal);
        
        if (!$tarif) {
            return 0;
        }
        
        return $nilaiDasar * ($tarif['persentase'] / 100);
    }

    /**
     * Hitung PPN
     */
    public function hitungPpn($nilaiDasar, $tanggal = null)
    {
        return $this->hitungPajak('PPN', $nilaiDasar, $tanggal);
    }

    /**
     * Hitung PPh Badan
     */
    public function hitungPphBadan($pkp, $tanggal = null)
    {
        return $this->hitungPajak('PPh Badan', $pkp, $tanggal);
    }

    /**
     * Hitung PPh 21 progressive
     */
    public function hitungPph21($penghasilanKenaPajak, $tanggal = null)
    {
        $rates = $this->getPph21Rates();
        $pph = 0;
        $sisa = $penghasilanKenaPajak;
        
        // Tarif PPh 21 progressive berdasarkan PP 58/2023
        // 0% untuk PKP <= 60 juta
        // 5% untuk PKP 60-250 juta
        // 15% untuk PKP 250-500 juta
        // 25% untuk PKP 500-5 M
        // 30% untuk PKP > 5 M
        
        $brackets = [
            ['max' => 60000000, 'rate' => 0],
            ['max' => 250000000, 'rate' => 5],
            ['max' => 500000000, 'rate' => 15],
            ['max' => 5000000000, 'rate' => 25],
            ['max' => PHP_INT_MAX, 'rate' => 30]
        ];
        
        foreach ($brackets as $bracket) {
            if ($sisa <= 0) break;
            
            $taxable = min($sisa, $bracket['max']);
            $pph += $taxable * ($bracket['rate'] / 100);
            $sisa -= $taxable;
        }
        
        return $pph;
    }

    /**
     * Export data untuk Excel
     */
    public function getExportData($filters = [])
    {
        $builder = $this->select('tarif_pajak.*');
        
        if (!empty($filters['jenis_pajak'])) {
            $builder->where('jenis_pajak', $filters['jenis_pajak']);
        }
        
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $builder->where('is_active', $filters['is_active']);
        }
        
        $tarif = $builder->orderBy('jenis_pajak', 'ASC')
                         ->orderBy('berlaku_mulai', 'DESC')
                         ->findAll();
        
        $exportData = [];
        foreach ($tarif as $item) {
            $exportData[] = [
                'Kode Tarif' => $item['kode_tarif'],
                'Jenis Pajak' => $item['jenis_pajak'],
                'Nama Tarif' => $item['nama_tarif'],
                'Persentase' => $item['persentase'] . '%',
                'Berlaku Mulai' => $item['berlaku_mulai'],
                'Berlaku Sampai' => $item['berlaku_sampai'] ?? '-',
                'Status' => $item['is_active'] ? 'Aktif' : 'Nonaktif',
                'Keterangan' => $item['keterangan'] ?? '-',
                'Dibuat Tanggal' => $item['created_at'],
                'Diupdate Tanggal' => $item['updated_at']
            ];
        }
        
        return $exportData;
    }

    /**
     * Check if tarif is currently active
     */
    public function isActive($id)
    {
        $tarif = $this->find($id);
        
        if (!$tarif) {
            return false;
        }
        
        $today = date('Y-m-d');
        $berlakuMulai = $tarif['berlaku_mulai'];
        $berlakuSampai = $tarif['berlaku_sampai'];
        
        $active = $berlakuMulai <= $today;
        if ($berlakuSampai) {
            $active = $active && $today <= $berlakuSampai;
        }
        
        return $active && $tarif['is_active'] == 1;
    }

    /**
     * Update tarif yang sudah tidak berlaku menjadi nonaktif
     */
    public function updateExpiredTarif()
    {
        $today = date('Y-m-d');
        
        $expired = $this->where('berlaku_sampai <', $today)
                        ->where('is_active', 1)
                        ->findAll();
        
        $updated = 0;
        foreach ($expired as $item) {
            if ($this->update($item['id'], ['is_active' => 0])) {
                $updated++;
            }
        }
        
        return $updated;
    }

    /**
     * Activate tarif that are now valid
     */
    public function activateNewTarif()
    {
        $today = date('Y-m-d');
        
        $new = $this->where('berlaku_mulai <=', $today)
                    ->groupStart()
                        ->where('berlaku_sampai >=', $today)
                        ->orWhere('berlaku_sampai IS NULL')
                    ->groupEnd()
                    ->where('is_active', 0)
                    ->findAll();
        
        $updated = 0;
        foreach ($new as $item) {
            if ($this->update($item['id'], ['is_active' => 1])) {
                $updated++;
            }
        }
        
        return $updated;
    }

    /**
     * Get tarif yang akan segera berakhir
     */
    public function getExpiringSoon($hariKeDepan = 30)
    {
        $batasTanggal = date('Y-m-d', strtotime("+$hariKeDepan days"));
        $today = date('Y-m-d');
        
        return $this->where('berlaku_sampai >=', $today)
                    ->where('berlaku_sampai <=', $batasTanggal)
                    ->where('is_active', 1)
                    ->orderBy('berlaku_sampai', 'ASC')
                    ->findAll();
    }

    /**
     * Get tarif yang akan segera berlaku
     */
    public function getUpcomingSoon($hariKeDepan = 30)
    {
        $today = date('Y-m-d');
        $batasTanggal = date('Y-m-d', strtotime("+$hariKeDepan days"));
        
        return $this->where('berlaku_mulai >', $today)
                    ->where('berlaku_mulai <=', $batasTanggal)
                    ->orderBy('berlaku_mulai', 'ASC')
                    ->findAll();
    }
}