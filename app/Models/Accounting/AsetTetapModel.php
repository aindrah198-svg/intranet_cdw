<?php
namespace App\Models\Accounting;

use CodeIgniter\Model;

class AsetTetapModel extends Model
{
    protected $table = 'aset_tetap';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'kode_aset',
        'kategori_id',
        'nama_aset',
        'merk',
        'model',
        'serial_number',
        'deskripsi',
        'tanggal_perolehan',
        'harga_perolehan',
        'nilai_residu',
        'masa_manfaat_tahun',
        'metode_penyusutan',
        'lokasi',
        'departemen',
        'penanggung_jawab',
        'status',
        'kondisi',
        'coa_aset_id',
        'coa_akumulasi_id',
        'coa_beban_id',
        'dokumen_pembelian',
        'foto_aset',
        'catatan',
        'created_by',
        'updated_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'kode_aset' => 'required|is_unique[aset_tetap.kode_aset]',
        'kategori_id' => 'required|is_natural_no_zero',
        'nama_aset' => 'required',
        'tanggal_perolehan' => 'required|valid_date',
        'harga_perolehan' => 'required|numeric|greater_than[0]',
        'nilai_residu' => 'permit_empty|numeric|greater_than_equal_to[0]',
        'masa_manfaat_tahun' => 'permit_empty|is_natural|greater_than[0]',
        'metode_penyusutan' => 'permit_empty|in_list[Garis Lurus,Saldo Menurun,Unit Produksi]',
        'status' => 'permit_empty|in_list[Aktif,Rusak,Dilepas,Dalam Perbaikan,Dihapus]',
        'kondisi' => 'permit_empty|in_list[Baik,Rusak Ringan,Rusak Berat,Perlu Perbaikan]'
    ];

    public function __construct()
    {
        parent::__construct();
        $db = \Config\Database::connect();
        if ($db->tableExists('aset_tetap_kategori')) {
            $katCols = [
                'masa_manfaat' => "INT DEFAULT 4",
                'persentase_penyusutan' => "DECIMAL(5,2) DEFAULT 0.00",
                'is_active' => "TINYINT(1) DEFAULT 1",
                'coa_aset_id' => "INT DEFAULT NULL",
                'coa_akumulasi_id' => "INT DEFAULT NULL",
                'coa_beban_id' => "INT DEFAULT NULL",
            ];
            foreach ($katCols as $col => $type) {
                if (!$db->fieldExists($col, 'aset_tetap_kategori')) {
                    $db->query("ALTER TABLE `aset_tetap_kategori` ADD COLUMN `{$col}` {$type}");
                }
            }
        }
        if ($db->tableExists('aset_tetap')) {
            $asetCols = [
                'coa_aset_id' => "INT DEFAULT NULL",
                'coa_akumulasi_id' => "INT DEFAULT NULL",
                'coa_beban_id' => "INT DEFAULT NULL",
                'penanggung_jawab_id' => "INT DEFAULT NULL",
                'merk' => "VARCHAR(100) DEFAULT NULL",
                'model' => "VARCHAR(100) DEFAULT NULL",
                'serial_number' => "VARCHAR(100) DEFAULT NULL",
            ];
            foreach ($asetCols as $col => $type) {
                if (!$db->fieldExists($col, 'aset_tetap')) {
                    $db->query("ALTER TABLE `aset_tetap` ADD COLUMN `{$col}` {$type}");
                }
            }
        }
    }

    protected $validationMessages = [
        'kode_aset' => [
            'required' => 'Kode aset harus diisi',
            'is_unique' => 'Kode aset sudah terdaftar'
        ],
        'kategori_id' => [
            'required' => 'Kategori aset harus dipilih'
        ],
        'nama_aset' => [
            'required' => 'Nama aset harus diisi'
        ],
        'tanggal_perolehan' => [
            'required' => 'Tanggal perolehan harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'harga_perolehan' => [
            'required' => 'Harga perolehan harus diisi',
            'numeric' => 'Harga perolehan harus berupa angka',
            'greater_than' => 'Harga perolehan harus lebih dari 0'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateKodeAset', 'setDefaultValues', 'setCreatedBy', 'setCoaFromKategori', 'hitungMasaManfaat'];
    protected $beforeUpdate = ['setUpdatedBy', 'validateStatusChange', 'setCoaFromKategori', 'hitungMasaManfaat'];

    /**
     * Generate kode aset otomatis
     * Format: AST-YYYYMMDD-XXXX
     */
    protected function generateKodeAset(array $data)
    {
        if (empty($data['data']['kode_aset'])) {
            $tanggal = $data['data']['tanggal_perolehan'] ?? date('Y-m-d');
            $prefix = 'AST-' . date('Ymd', strtotime($tanggal)) . '-';
            
            // Cari sequence terakhir untuk tanggal ini
            $last = $this->where('kode_aset LIKE', $prefix . '%')
                         ->orderBy('kode_aset', 'DESC')
                         ->first();
            
            if ($last) {
                $lastNum = substr($last['kode_aset'], strlen($prefix));
                $nextNum = str_pad((int)$lastNum + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNum = '0001';
            }
            
            $data['data']['kode_aset'] = $prefix . $nextNum;
        }
        
        return $data;
    }

    /**
     * Set default values
     */
    protected function setDefaultValues(array $data)
    {
        if (!isset($data['data']['status'])) {
            $data['data']['status'] = 'Aktif';
        }
        
        if (!isset($data['data']['kondisi'])) {
            $data['data']['kondisi'] = 'Baik';
        }
        
        if (!isset($data['data']['metode_penyusutan'])) {
            $data['data']['metode_penyusutan'] = 'Garis Lurus';
        }
        
        if (!isset($data['data']['nilai_residu'])) {
            $data['data']['nilai_residu'] = 0;
        }
        
        return $data;
    }

    /**
     * Set COA dari kategori aset
     */
    protected function setCoaFromKategori(array $data)
    {
        if (!empty($data['data']['kategori_id'])) {
            $kategoriModel = new AsetTetapKategoriModel();
            $kategori = $kategoriModel->find($data['data']['kategori_id']);
            
            if ($kategori) {
                if (empty($data['data']['coa_aset_id']) && !empty($kategori['coa_aset_id'])) {
                    $data['data']['coa_aset_id'] = $kategori['coa_aset_id'];
                }
                if (empty($data['data']['coa_akumulasi_id']) && !empty($kategori['coa_akumulasi_id'])) {
                    $data['data']['coa_akumulasi_id'] = $kategori['coa_akumulasi_id'];
                }
                if (empty($data['data']['coa_beban_id']) && !empty($kategori['coa_beban_id'])) {
                    $data['data']['coa_beban_id'] = $kategori['coa_beban_id'];
                }
            }
        }
        
        return $data;
    }

    /**
     * Hitung masa manfaat dari kategori jika tidak diisi
     */
    protected function hitungMasaManfaat(array $data)
    {
        if (empty($data['data']['masa_manfaat_tahun']) && !empty($data['data']['kategori_id'])) {
            $kategoriModel = new AsetTetapKategoriModel();
            $kategori = $kategoriModel->find($data['data']['kategori_id']);
            
            if ($kategori && !empty($kategori['masa_manfaat'])) {
                $data['data']['masa_manfaat_tahun'] = $kategori['masa_manfaat'];
            } else {
                $data['data']['masa_manfaat_tahun'] = 5; // Default 5 tahun
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
     * Set updated_by
     */
    protected function setUpdatedBy(array $data)
    {
        $data['data']['updated_by'] = session()->get('user_id');
        return $data;
    }

    /**
     * Validasi perubahan status
     */
    protected function validateStatusChange(array $data)
    {
        if (isset($data['data']['status'])) {
            $id = $data['id'][0] ?? null;
            
            if ($id) {
                $current = $this->find($id);
                
                if ($current) {
                    // Jika status berubah menjadi Dilepas, cek apakah sudah ada pelepasan
                    if ($data['data']['status'] === 'Dilepas' && $current['status'] !== 'Dilepas') {
                        $pelepasanModel = new PelepasanAsetModel();
                        $pelepasan = $pelepasanModel->where('aset_id', $id)
                                                    ->where('status', 'Selesai')
                                                    ->first();
                        
                        if (!$pelepasan) {
                            throw new \RuntimeException('Aset tidak dapat diubah status menjadi Dilepas tanpa proses pelepasan yang sah');
                        }
                    }
                    
                    // Jika status berubah menjadi Aktif dari non-aktif
                    if ($data['data']['status'] === 'Aktif' && $current['status'] !== 'Aktif') {
                        // Bisa diaktifkan kembali asalkan tidak dalam status Dilepas
                        if ($current['status'] === 'Dilepas') {
                            throw new \RuntimeException('Aset yang sudah dilepas tidak dapat diaktifkan kembali');
                        }
                    }
                }
            }
        }
        
        return $data;
    }

    /**
     * Get all aset with filters
     */
    public function getAllWithFilters($filters = [], $perPage = 20, $page = 1)
    {
        $builder = $this->select('aset_tetap.*, 
            kategori.nama_kategori,
            kategori.kode_kategori,
            kategori.metode_penyusutan as kategori_metode,
            kategori.masa_manfaat as kategori_masa_manfaat,
            coa_aset.kode_akun as kode_akun_aset,
            coa_aset.nama_akun as nama_akun_aset,
            coa_akumulasi.kode_akun as kode_akun_akumulasi,
            coa_akumulasi.nama_akun as nama_akun_akumulasi,
            coa_beban.kode_akun as kode_akun_beban,
            coa_beban.nama_akun as nama_akun_beban,
            karyawan.nama_lengkap as nama_penanggung_jawab,
            karyawan.nik as nik_penanggung_jawab,
            creator.username as creator_name')
            ->join('aset_tetap_kategori as kategori', 'kategori.id = aset_tetap.kategori_id', 'left')
            ->join('coa as coa_aset', 'coa_aset.id = aset_tetap.coa_aset_id', 'left')
            ->join('coa as coa_akumulasi', 'coa_akumulasi.id = aset_tetap.coa_akumulasi_id', 'left')
            ->join('coa as coa_beban', 'coa_beban.id = aset_tetap.coa_beban_id', 'left')
            ->join('karyawan', 'karyawan.id = aset_tetap.penanggung_jawab_id', 'left')
            ->join('users as creator', 'creator.id = aset_tetap.created_by', 'left');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('aset_tetap.kode_aset', $search)
                ->orLike('aset_tetap.nama_aset', $search)
                ->orLike('aset_tetap.merk', $search)
                ->orLike('aset_tetap.model', $search)
                ->orLike('aset_tetap.serial_number', $search)
                ->orLike('kategori.nama_kategori', $search)
                ->orLike('karyawan.nama_lengkap', $search)
                ->groupEnd();
        }
        
        if (!empty($filters['kategori_id'])) {
            $builder->where('aset_tetap.kategori_id', $filters['kategori_id']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('aset_tetap.status', $filters['status']);
        }
        
        if (!empty($filters['kondisi'])) {
            $builder->where('aset_tetap.kondisi', $filters['kondisi']);
        }
        
        if (!empty($filters['lokasi'])) {
            $builder->like('aset_tetap.lokasi', $filters['lokasi']);
        }
        
        if (!empty($filters['departemen'])) {
            $builder->where('aset_tetap.departemen', $filters['departemen']);
        }
        
        if (!empty($filters['penanggung_jawab'])) {
            $builder->where('aset_tetap.penanggung_jawab', $filters['penanggung_jawab']);
        }
        
        if (!empty($filters['tanggal_perolehan_mulai'])) {
            $builder->where('aset_tetap.tanggal_perolehan >=', $filters['tanggal_perolehan_mulai']);
        }
        
        if (!empty($filters['tanggal_perolehan_selesai'])) {
            $builder->where('aset_tetap.tanggal_perolehan <=', $filters['tanggal_perolehan_selesai']);
        }
        
        if (isset($filters['min_harga']) && $filters['min_harga'] !== '') {
            $builder->where('aset_tetap.harga_perolehan >=', $filters['min_harga']);
        }
        
        if (isset($filters['max_harga']) && $filters['max_harga'] !== '') {
            $builder->where('aset_tetap.harga_perolehan <=', $filters['max_harga']);
        }
        
        $builder->orderBy('aset_tetap.tanggal_perolehan', 'DESC')
                ->orderBy('aset_tetap.kode_aset', 'DESC');
        
        // Get total for pagination
        $total = $builder->countAllResults(false);
        
        // Get paginated results
        $offset = ($page - 1) * $perPage;
        $aset = $builder->limit($perPage, $offset)->findAll();
        
        // Hitung nilai buku untuk setiap aset
        foreach ($aset as &$item) {
            $item['nilai_buku'] = $this->getNilaiBuku($item['id']);
            $item['akumulasi_penyusutan'] = $this->getAkumulasiPenyusutan($item['id']);
        }
        
        return [
            'data' => $aset,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Get aset by ID with details
     */
    public function getWithDetails($id)
    {
        $aset = $this->select('aset_tetap.*, 
            kategori.nama_kategori,
            kategori.kode_kategori,
            kategori.metode_penyusutan as kategori_metode,
            kategori.masa_manfaat as kategori_masa_manfaat,
            kategori.persentase_penyusutan,
            coa_aset.kode_akun as kode_akun_aset,
            coa_aset.nama_akun as nama_akun_aset,
            coa_aset.tipe_akun as tipe_akun_aset,
            coa_akumulasi.kode_akun as kode_akun_akumulasi,
            coa_akumulasi.nama_akun as nama_akun_akumulasi,
            coa_beban.kode_akun as kode_akun_beban,
            coa_beban.nama_akun as nama_akun_beban,
            karyawan.nik as nik_penanggung_jawab,
            karyawan.nama_lengkap as nama_penanggung_jawab,
            karyawan.jabatan as jabatan_penanggung_jawab,
            karyawan.departemen as departemen_penanggung_jawab,
            creator.username as creator_name,
            updater.username as updater_name')
            ->join('aset_tetap_kategori as kategori', 'kategori.id = aset_tetap.kategori_id', 'left')
            ->join('coa as coa_aset', 'coa_aset.id = aset_tetap.coa_aset_id', 'left')
            ->join('coa as coa_akumulasi', 'coa_akumulasi.id = aset_tetap.coa_akumulasi_id', 'left')
            ->join('coa as coa_beban', 'coa_beban.id = aset_tetap.coa_beban_id', 'left')
            ->join('karyawan', 'karyawan.id = aset_tetap.penanggung_jawab', 'left')
            ->join('users as creator', 'creator.id = aset_tetap.created_by', 'left')
            ->join('users as updater', 'updater.id = aset_tetap.updated_by', 'left')
            ->where('aset_tetap.id', $id)
            ->first();
        
        if ($aset) {
            // Hitung nilai buku
            $aset['akumulasi_penyusutan'] = $this->getAkumulasiPenyusutan($id);
            $aset['nilai_buku'] = $aset['harga_perolehan'] - $aset['akumulasi_penyusutan'];
            
            // Ambil riwayat penyusutan
            $penyusutanModel = new PenyusutanModel();
            $aset['riwayat_penyusutan'] = $penyusutanModel->getByAset($id);
            
            // Ambil riwayat mutasi
            $mutasiModel = new MutasiAsetModel();
            $aset['riwayat_mutasi'] = $mutasiModel->getByAset($id);
            
            // Cek apakah ada pelepasan
            $pelepasanModel = new PelepasanAsetModel();
            $aset['pelepasan'] = $pelepasanModel->where('aset_id', $id)
                                                ->where('deleted_at IS NULL')
                                                ->orderBy('id', 'DESC')
                                                ->first();
        }
        
        return $aset;
    }

    /**
     * Get aset by kategori
     */
    public function getByKategori($kategoriId)
    {
        return $this->where('kategori_id', $kategoriId)
                    ->where('status', 'Aktif')
                    ->orderBy('nama_aset', 'ASC')
                    ->findAll();
    }

    /**
     * Get aset by penanggung jawab
     */
    public function getByPenanggungJawab($karyawanId)
    {
        return $this->where('penanggung_jawab', $karyawanId)
                    ->where('status', 'Aktif')
                    ->orderBy('nama_aset', 'ASC')
                    ->findAll();
    }

    /**
     * Get aset by status
     */
    public function getByStatus($status)
    {
        return $this->where('status', $status)
                    ->orderBy('tanggal_perolehan', 'DESC')
                    ->findAll();
    }

    /**
     * Get active aset
     */
    public function getActive()
    {
        return $this->where('status', 'Aktif')
                    ->orderBy('nama_aset', 'ASC')
                    ->findAll();
    }

    /**
     * Get aset options for dropdown
     */
    public function getAsetOptions($status = 'Aktif')
    {
        $builder = $this->select('id, kode_aset, nama_aset, harga_perolehan')
                        ->orderBy('kode_aset', 'ASC');
        
        if ($status) {
            $builder->where('status', $status);
        }
        
        return $builder->findAll();
    }

    /**
     * Hitung akumulasi penyusutan aset
     */
    public function getAkumulasiPenyusutan($asetId)
    {
        $penyusutanModel = new PenyusutanModel();
        $akumulasi = $penyusutanModel->where('aset_id', $asetId)
                                     ->where('status', 'Posted')
                                     ->selectSum('nilai_penyusutan')
                                     ->first();
        
        return $akumulasi['nilai_penyusutan'] ?? 0;
    }

    /**
     * Hitung nilai buku aset
     */
    public function getNilaiBuku($asetId)
    {
        $aset = $this->find($asetId);
        
        if (!$aset) {
            return 0;
        }
        
        $akumulasi = $this->getAkumulasiPenyusutan($asetId);
        $nilaiBuku = $aset['harga_perolehan'] - $akumulasi;
        
        return max($nilaiBuku, 0);
    }

    /**
     * Hitung penyusutan per bulan untuk aset
     */
    public function hitungPenyusutanBulanan($asetId)
    {
        $aset = $this->find($asetId);
        
        if (!$aset || $aset['status'] !== 'Aktif') {
            return 0;
        }
        
        $hargaPerolehan = $aset['harga_perolehan'];
        $nilaiResidu = $aset['nilai_residu'] ?? 0;
        $masaManfaat = $aset['masa_manfaat_tahun'] ?? 5;
        $metode = $aset['metode_penyusutan'] ?? 'Garis Lurus';
        
        $nilaiPenyusutan = $hargaPerolehan - $nilaiResidu;
        
        if ($metode === 'Garis Lurus') {
            // Per tahun = (Harga Perolehan - Nilai Residu) / Masa Manfaat
            $perTahun = $nilaiPenyusutan / $masaManfaat;
            return $perTahun / 12;
        } elseif ($metode === 'Saldo Menurun') {
            // Metode saldo menurun (biasanya 2x garis lurus)
            $rate = (2 / $masaManfaat) * 100;
            $nilaiBuku = $this->getNilaiBuku($asetId);
            return ($nilaiBuku * $rate / 100) / 12;
        }
        
        return 0;
    }

    /**
     * Hitung total nilai aset
     */
    public function getTotalNilaiPerolehan($filters = [])
    {
        $builder = $this->selectSum('harga_perolehan');
        
        if (!empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }
        
        if (!empty($filters['kategori_id'])) {
            $builder->where('kategori_id', $filters['kategori_id']);
        }
        
        $result = $builder->first();
        return $result['harga_perolehan'] ?? 0;
    }

    /**
     * Get statistics untuk dashboard
     */
    public function getStats()
    {
        $stats = $this->select("
                COUNT(*) as total_aset,
                SUM(CASE WHEN status = 'Aktif' THEN 1 ELSE 0 END) as total_aktif,
                SUM(CASE WHEN status = 'Rusak' THEN 1 ELSE 0 END) as total_rusak,
                SUM(CASE WHEN status = 'Dilepas' THEN 1 ELSE 0 END) as total_dilepas,
                SUM(CASE WHEN status = 'Dalam Perbaikan' THEN 1 ELSE 0 END) as total_perbaikan,
                SUM(harga_perolehan) as total_nilai_perolehan
            ")
            ->where('deleted_at IS NULL')
            ->first();
        
        // Hitung total nilai buku
        if ($stats) {
            $totalNilaiBuku = 0;
            $asetList = $this->where('deleted_at IS NULL')->findAll();
            foreach ($asetList as $aset) {
                $totalNilaiBuku += $this->getNilaiBuku($aset['id']);
            }
            $stats['total_nilai_buku'] = $totalNilaiBuku;
        }
        
        return $stats ?? [
            'total_aset' => 0,
            'total_aktif' => 0,
            'total_rusak' => 0,
            'total_dilepas' => 0,
            'total_perbaikan' => 0,
            'total_nilai_perolehan' => 0,
            'total_nilai_buku' => 0
        ];
    }

    /**
     * Get rekap per kategori
     */
    public function getRekapPerKategori()
    {
        return $this->select("
                kategori_id,
                kategori.nama_kategori,
                kategori.kode_kategori,
                COUNT(*) as jumlah_aset,
                SUM(harga_perolehan) as total_nilai_perolehan
            ")
            ->join('aset_tetap_kategori as kategori', 'kategori.id = aset_tetap.kategori_id')
            ->where('aset_tetap.deleted_at IS NULL')
            ->groupBy('kategori_id, kategori.nama_kategori, kategori.kode_kategori')
            ->orderBy('kategori.kode_kategori', 'ASC')
            ->findAll();
    }

    /**
     * Get rekap per departemen
     */
    public function getRekapPerDepartemen()
    {
        return $this->select("
                departemen,
                COUNT(*) as jumlah_aset,
                SUM(harga_perolehan) as total_nilai_perolehan
            ")
            ->where('departemen IS NOT NULL')
            ->where('departemen !=', '')
            ->where('deleted_at IS NULL')
            ->groupBy('departemen')
            ->orderBy('departemen', 'ASC')
            ->findAll();
    }

    /**
     * Get aset yang akan habis masa manfaat dalam X bulan
     */
    public function getAsetAkanHabis($bulanKeDepan = 12)
    {
        $batasTanggal = date('Y-m-d', strtotime("+$bulanKeDepan months"));
        
        $asetList = $this->where('status', 'Aktif')
                         ->where('deleted_at IS NULL')
                         ->findAll();
        
        $hasil = [];
        foreach ($asetList as $aset) {
            $tanggalPerolehan = $aset['tanggal_perolehan'];
            $masaManfaat = $aset['masa_manfaat_tahun'] ?? 5;
            $tanggalHabis = date('Y-m-d', strtotime($tanggalPerolehan . " +$masaManfaat years"));
            
            if ($tanggalHabis <= $batasTanggal) {
                $aset['tanggal_habis_manfaat'] = $tanggalHabis;
                $aset['sisa_bulan'] = ceil((strtotime($tanggalHabis) - time()) / (60 * 60 * 24 * 30));
                $hasil[] = $aset;
            }
        }
        
        usort($hasil, function($a, $b) {
            return strtotime($a['tanggal_habis_manfaat']) - strtotime($b['tanggal_habis_manfaat']);
        });
        
        return $hasil;
    }

    /**
     * Update status aset
     */
    public function updateStatus($id, $status, $keterangan = null)
    {
        $aset = $this->find($id);
        
        if (!$aset) {
            throw new \RuntimeException('Aset tidak ditemukan');
        }
        
        $updateData = ['status' => $status];
        
        if ($keterangan) {
            $updateData['catatan'] = ($aset['catatan'] ? $aset['catatan'] . "\n" : '') . 
                                     '[' . date('Y-m-d H:i:s') . '] Status diubah menjadi ' . $status . ': ' . $keterangan;
        }
        
        return $this->update($id, $updateData);
    }

    /**
     * Update kondisi aset
     */
    public function updateKondisi($id, $kondisi, $keterangan = null)
    {
        $aset = $this->find($id);
        
        if (!$aset) {
            throw new \RuntimeException('Aset tidak ditemukan');
        }
        
        $updateData = ['kondisi' => $kondisi];
        
        if ($keterangan) {
            $updateData['catatan'] = ($aset['catatan'] ? $aset['catatan'] . "\n" : '') . 
                                     '[' . date('Y-m-d H:i:s') . '] Kondisi diubah menjadi ' . $kondisi . ': ' . $keterangan;
        }
        
        return $this->update($id, $updateData);
    }

    /**
     * Check if aset can be deleted (no transactions)
     */
    public function canDelete($id)
    {
        $penyusutanModel = new PenyusutanModel();
        $penyusutanCount = $penyusutanModel->where('aset_id', $id)->countAllResults();
        
        if ($penyusutanCount > 0) {
            return false;
        }
        
        $pelepasanModel = new PelepasanAsetModel();
        $pelepasanCount = $pelepasanModel->where('aset_id', $id)->countAllResults();
        
        if ($pelepasanCount > 0) {
            return false;
        }
        
        return true;
    }

    /**
     * Export data untuk Excel
     */
    public function getExportData($filters = [])
    {
        $builder = $this->select('aset_tetap.*, 
            kategori.nama_kategori,
            kategori.kode_kategori,
            karyawan.nama_lengkap as nama_penanggung_jawab,
            karyawan.nik as nik_penanggung_jawab')
            ->join('aset_tetap_kategori as kategori', 'kategori.id = aset_tetap.kategori_id', 'left')
            ->join('karyawan', 'karyawan.id = aset_tetap.penanggung_jawab', 'left');
        
        if (!empty($filters['kategori_id'])) {
            $builder->where('aset_tetap.kategori_id', $filters['kategori_id']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('aset_tetap.status', $filters['status']);
        }
        
        if (!empty($filters['tanggal_perolehan_mulai'])) {
            $builder->where('aset_tetap.tanggal_perolehan >=', $filters['tanggal_perolehan_mulai']);
        }
        
        if (!empty($filters['tanggal_perolehan_selesai'])) {
            $builder->where('aset_tetap.tanggal_perolehan <=', $filters['tanggal_perolehan_selesai']);
        }
        
        $aset = $builder->orderBy('aset_tetap.kode_aset', 'ASC')->findAll();
        
        $exportData = [];
        foreach ($aset as $item) {
            $nilaiBuku = $this->getNilaiBuku($item['id']);
            $akumulasi = $this->getAkumulasiPenyusutan($item['id']);
            
            $exportData[] = [
                'Kode Aset' => $item['kode_aset'],
                'Nama Aset' => $item['nama_aset'],
                'Kategori' => $item['nama_kategori'] . ' (' . $item['kode_kategori'] . ')',
                'Merk' => $item['merk'] ?? '-',
                'Model' => $item['model'] ?? '-',
                'Serial Number' => $item['serial_number'] ?? '-',
                'Tanggal Perolehan' => $item['tanggal_perolehan'],
                'Harga Perolehan' => $item['harga_perolehan'],
                'Nilai Residu' => $item['nilai_residu'] ?? 0,
                'Masa Manfaat (Tahun)' => $item['masa_manfaat_tahun'],
                'Metode Penyusutan' => $item['metode_penyusutan'],
                'Akumulasi Penyusutan' => $akumulasi,
                'Nilai Buku' => $nilaiBuku,
                'Lokasi' => $item['lokasi'] ?? '-',
                'Departemen' => $item['departemen'] ?? '-',
                'Penanggung Jawab' => $item['nama_penanggung_jawab'] ?? '-',
                'Status' => $item['status'],
                'Kondisi' => $item['kondisi'],
                'Catatan' => $item['catatan'] ?? '-'
            ];
        }
        
        return $exportData;
    }
}