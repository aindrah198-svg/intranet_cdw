<?php
namespace App\Models\Accounting;

use CodeIgniter\Model;

class MutasiAsetModel extends Model
{
    protected $table = 'mutasi_aset';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'aset_id',
        'tanggal_mutasi',
        'lokasi_asal',
        'lokasi_tujuan',
        'penanggung_jawab_asal',
        'penanggung_jawab_tujuan',
        'alasan',
        'dokumen',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'aset_id' => 'required|is_natural_no_zero',
        'tanggal_mutasi' => 'required|valid_date',
        'lokasi_tujuan' => 'required',
        'alasan' => 'required'
    ];

    protected $validationMessages = [
        'aset_id' => [
            'required' => 'Aset harus dipilih'
        ],
        'tanggal_mutasi' => [
            'required' => 'Tanggal mutasi harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'lokasi_tujuan' => [
            'required' => 'Lokasi tujuan harus diisi'
        ],
        'alasan' => [
            'required' => 'Alasan mutasi harus diisi'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = ['validateAset', 'validatePenanggungJawab', 'setCreatedBy', 'updateAsetLokasi'];
    protected $beforeUpdate = ['validateAsetOnUpdate', 'validatePenanggungJawab'];

    /**
     * Set created_by
     */
    protected function setCreatedBy(array $data)
    {
        $data['data']['created_by'] = session()->get('user_id');
        return $data;
    }

    /**
     * Validasi aset sebelum mutasi
     */
    protected function validateAset(array $data)
    {
        $asetId = $data['data']['aset_id'] ?? null;
        
        if ($asetId) {
            $asetModel = new AsetTetapModel();
            $aset = $asetModel->find($asetId);
            
            if (!$aset) {
                throw new \RuntimeException('Aset tidak ditemukan');
            }
            
            if ($aset['status'] !== 'Aktif') {
                throw new \RuntimeException('Hanya aset dengan status Aktif yang dapat dimutasi');
            }
            
            // Set lokasi asal dari data aset saat ini
            $data['data']['lokasi_asal'] = $aset['lokasi'] ?? null;
            $data['data']['penanggung_jawab_asal'] = $aset['penanggung_jawab'] ?? null;
            
            // Simpan data aset untuk referensi
            $data['data']['_aset_temp'] = $aset;
        }
        
        return $data;
    }

    /**
     * Validasi aset pada update
     */
    protected function validateAsetOnUpdate(array $data)
    {
        $id = $data['id'][0] ?? null;
        
        if ($id) {
            $current = $this->find($id);
            
            if ($current) {
                // Jika mengubah aset_id, validasi aset baru
                if (isset($data['data']['aset_id']) && $data['data']['aset_id'] != $current['aset_id']) {
                    $asetModel = new AsetTetapModel();
                    $aset = $asetModel->find($data['data']['aset_id']);
                    
                    if (!$aset) {
                        throw new \RuntimeException('Aset tidak ditemukan');
                    }
                    
                    if ($aset['status'] !== 'Aktif') {
                        throw new \RuntimeException('Hanya aset dengan status Aktif yang dapat dimutasi');
                    }
                    
                    // Set lokasi asal dari data aset saat ini
                    $data['data']['lokasi_asal'] = $aset['lokasi'] ?? null;
                    $data['data']['penanggung_jawab_asal'] = $aset['penanggung_jawab'] ?? null;
                } else {
                    // Jika tidak mengubah aset, pastikan lokasi asal tidak diubah
                    if (isset($data['data']['lokasi_asal'])) {
                        unset($data['data']['lokasi_asal']);
                    }
                    if (isset($data['data']['penanggung_jawab_asal'])) {
                        unset($data['data']['penanggung_jawab_asal']);
                    }
                }
            }
        }
        
        return $data;
    }

    /**
     * Validasi penanggung jawab
     */
    protected function validatePenanggungJawab(array $data)
    {
        $penanggungJawabTujuan = $data['data']['penanggung_jawab_tujuan'] ?? null;
        
        if ($penanggungJawabTujuan) {
            $karyawanModel = new \App\Models\KaryawanModel();
            $karyawan = $karyawanModel->find($penanggungJawabTujuan);
            
            if (!$karyawan) {
                throw new \RuntimeException('Penanggung jawab tujuan tidak valid');
            }
        }
        
        return $data;
    }

    /**
     * Update lokasi aset setelah mutasi (hanya untuk insert)
     */
    protected function updateAsetLokasi(array $data)
    {
        // Update dilakukan setelah insert berhasil
        // Karena callback dijalankan sebelum insert, kita simpan dulu untuk update setelahnya
        $asetId = $data['data']['aset_id'] ?? null;
        $lokasiTujuan = $data['data']['lokasi_tujuan'] ?? null;
        $penanggungJawabTujuan = $data['data']['penanggung_jawab_tujuan'] ?? null;
        
        if ($asetId) {
            // Simpan data untuk update setelah insert
            $data['data']['_update_aset'] = [
                'aset_id' => $asetId,
                'lokasi_tujuan' => $lokasiTujuan,
                'penanggung_jawab_tujuan' => $penanggungJawabTujuan
            ];
        }
        
        return $data;
    }

    /**
     * After insert hook untuk update aset
     */
    protected function afterInsert(array $data)
    {
        if (isset($data['data']['_update_aset'])) {
            $updateData = $data['data']['_update_aset'];
            $asetModel = new AsetTetapModel();
            
            $asetUpdate = [];
            if (!empty($updateData['lokasi_tujuan'])) {
                $asetUpdate['lokasi'] = $updateData['lokasi_tujuan'];
            }
            if (!empty($updateData['penanggung_jawab_tujuan'])) {
                $asetUpdate['penanggung_jawab'] = $updateData['penanggung_jawab_tujuan'];
            }
            
            if (!empty($asetUpdate)) {
                $asetModel->update($updateData['aset_id'], $asetUpdate);
            }
        }
        
        return $data;
    }

    /**
     * Get all mutasi aset with filters
     */
    public function getAllWithFilters($filters = [], $perPage = 20, $page = 1)
    {
        $builder = $this->select('mutasi_aset.*, 
            aset_tetap.kode_aset,
            aset_tetap.nama_aset,
            aset_tetap.harga_perolehan,
            aset_tetap.status as aset_status,
            kategori.nama_kategori,
            kategori.kode_kategori,
            karyawan_asal.nik as nik_asal,
            karyawan_asal.nama_lengkap as nama_penanggung_jawab_asal,
            karyawan_tujuan.nik as nik_tujuan,
            karyawan_tujuan.nama_lengkap as nama_penanggung_jawab_tujuan,
            creator.username as creator_name')
            ->join('aset_tetap', 'aset_tetap.id = mutasi_aset.aset_id', 'left')
            ->join('aset_tetap_kategori as kategori', 'kategori.id = aset_tetap.kategori_id', 'left')
            ->join('karyawan as karyawan_asal', 'karyawan_asal.id = mutasi_aset.penanggung_jawab_asal', 'left')
            ->join('karyawan as karyawan_tujuan', 'karyawan_tujuan.id = mutasi_aset.penanggung_jawab_tujuan', 'left')
            ->join('users as creator', 'creator.id = mutasi_aset.created_by', 'left');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('aset_tetap.kode_aset', $search)
                ->orLike('aset_tetap.nama_aset', $search)
                ->orLike('mutasi_aset.lokasi_asal', $search)
                ->orLike('mutasi_aset.lokasi_tujuan', $search)
                ->orLike('karyawan_asal.nama_lengkap', $search)
                ->orLike('karyawan_tujuan.nama_lengkap', $search)
                ->orLike('mutasi_aset.alasan', $search)
                ->groupEnd();
        }
        
        if (!empty($filters['aset_id'])) {
            $builder->where('mutasi_aset.aset_id', $filters['aset_id']);
        }
        
        if (!empty($filters['kategori_id'])) {
            $builder->where('aset_tetap.kategori_id', $filters['kategori_id']);
        }
        
        if (!empty($filters['lokasi'])) {
            $builder->groupStart()
                ->like('mutasi_aset.lokasi_asal', $filters['lokasi'])
                ->orLike('mutasi_aset.lokasi_tujuan', $filters['lokasi'])
                ->groupEnd();
        }
        
        if (!empty($filters['penanggung_jawab_id'])) {
            $builder->groupStart()
                ->where('mutasi_aset.penanggung_jawab_asal', $filters['penanggung_jawab_id'])
                ->orWhere('mutasi_aset.penanggung_jawab_tujuan', $filters['penanggung_jawab_id'])
                ->groupEnd();
        }
        
        if (!empty($filters['tanggal_mulai'])) {
            $builder->where('mutasi_aset.tanggal_mutasi >=', $filters['tanggal_mulai']);
        }
        
        if (!empty($filters['tanggal_selesai'])) {
            $builder->where('mutasi_aset.tanggal_mutasi <=', $filters['tanggal_selesai']);
        }
        
        $builder->orderBy('mutasi_aset.tanggal_mutasi', 'DESC')
                ->orderBy('mutasi_aset.id', 'DESC');
        
        // Get total for pagination
        $total = $builder->countAllResults(false);
        
        // Get paginated results
        $offset = ($page - 1) * $perPage;
        $mutasi = $builder->limit($perPage, $offset)->findAll();
        
        return [
            'data' => $mutasi,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Get mutasi aset by ID with details
     */
    public function getWithDetails($id)
    {
        $mutasi = $this->select('mutasi_aset.*, 
            aset_tetap.kode_aset,
            aset_tetap.nama_aset,
            aset_tetap.harga_perolehan,
            aset_tetap.tanggal_perolehan,
            aset_tetap.status as aset_status,
            aset_tetap.kondisi as aset_kondisi,
            kategori.nama_kategori,
            kategori.kode_kategori,
            karyawan_asal.nik as nik_asal,
            karyawan_asal.nama_lengkap as nama_penanggung_jawab_asal,
            karyawan_asal.jabatan as jabatan_asal,
            karyawan_asal.departemen as departemen_asal,
            karyawan_tujuan.nik as nik_tujuan,
            karyawan_tujuan.nama_lengkap as nama_penanggung_jawab_tujuan,
            karyawan_tujuan.jabatan as jabatan_tujuan,
            karyawan_tujuan.departemen as departemen_tujuan,
            creator.username as creator_name,
            creator.name as creator_fullname')
            ->join('aset_tetap', 'aset_tetap.id = mutasi_aset.aset_id', 'left')
            ->join('aset_tetap_kategori as kategori', 'kategori.id = aset_tetap.kategori_id', 'left')
            ->join('karyawan as karyawan_asal', 'karyawan_asal.id = mutasi_aset.penanggung_jawab_asal', 'left')
            ->join('karyawan as karyawan_tujuan', 'karyawan_tujuan.id = mutasi_aset.penanggung_jawab_tujuan', 'left')
            ->join('users as creator', 'creator.id = mutasi_aset.created_by', 'left')
            ->where('mutasi_aset.id', $id)
            ->first();
        
        return $mutasi;
    }

    /**
     * Get mutasi by aset
     */
    public function getByAset($asetId)
    {
        return $this->where('aset_id', $asetId)
                    ->orderBy('tanggal_mutasi', 'DESC')
                    ->findAll();
    }

    /**
     * Get mutasi by periode
     */
    public function getByPeriode($tahun, $bulan = null)
    {
        $builder = $this->where('YEAR(tanggal_mutasi)', $tahun);
        
        if ($bulan) {
            $builder->where('MONTH(tanggal_mutasi)', $bulan);
        }
        
        return $builder->orderBy('tanggal_mutasi', 'DESC')->findAll();
    }

    /**
     * Get mutasi by lokasi
     */
    public function getByLokasi($lokasi)
    {
        return $this->where('lokasi_tujuan', $lokasi)
                    ->orWhere('lokasi_asal', $lokasi)
                    ->orderBy('tanggal_mutasi', 'DESC')
                    ->findAll();
    }

    /**
     * Get mutasi by penanggung jawab
     */
    public function getByPenanggungJawab($karyawanId)
    {
        return $this->where('penanggung_jawab_asal', $karyawanId)
                    ->orWhere('penanggung_jawab_tujuan', $karyawanId)
                    ->orderBy('tanggal_mutasi', 'DESC')
                    ->findAll();
    }

    /**
     * Get riwayat mutasi aset dengan ringkasan
     */
    public function getRiwayatAset($asetId)
    {
        $mutasi = $this->getByAset($asetId);
        
        $riwayat = [];
        foreach ($mutasi as $item) {
            $riwayat[] = [
                'tanggal' => $item['tanggal_mutasi'],
                'dari' => [
                    'lokasi' => $item['lokasi_asal'],
                    'penanggung_jawab' => $item['penanggung_jawab_asal']
                ],
                'ke' => [
                    'lokasi' => $item['lokasi_tujuan'],
                    'penanggung_jawab' => $item['penanggung_jawab_tujuan']
                ],
                'alasan' => $item['alasan'],
                'dokumen' => $item['dokumen'],
                'created_at' => $item['created_at'],
                'created_by' => $item['created_by']
            ];
        }
        
        return $riwayat;
    }

    /**
     * Get lokasi unik untuk dropdown
     */
    public function getLokasiOptions()
    {
        // Ambil lokasi dari aset yang aktif
        $asetModel = new AsetTetapModel();
        $asetList = $asetModel->where('status', 'Aktif')
                              ->where('lokasi IS NOT NULL')
                              ->where('lokasi !=', '')
                              ->findAll();
        
        $lokasi = array_unique(array_column($asetList, 'lokasi'));
        
        // Tambahkan lokasi dari mutasi
        $mutasiLokasiAsal = $this->select('lokasi_asal')
                                  ->where('lokasi_asal IS NOT NULL')
                                  ->where('lokasi_asal !=', '')
                                  ->findAll();
        $mutasiLokasiTujuan = $this->select('lokasi_tujuan')
                                   ->where('lokasi_tujuan IS NOT NULL')
                                   ->where('lokasi_tujuan !=', '')
                                   ->findAll();
        
        $allLokasi = array_merge($lokasi, array_column($mutasiLokasiAsal, 'lokasi_asal'), array_column($mutasiLokasiTujuan, 'lokasi_tujuan'));
        $allLokasi = array_unique(array_filter($allLokasi));
        sort($allLokasi);
        
        return $allLokasi;
    }

    /**
     * Get karyawan options untuk dropdown
     */
    public function getKaryawanOptions()
    {
        $karyawanModel = new \App\Models\KaryawanModel();
        
        return $karyawanModel->select('id, nik, nama_lengkap, jabatan, departemen')
                             ->where('status_karyawan', 'Tetap')
                             ->orWhere('status_karyawan', 'Kontrak')
                             ->orderBy('nama_lengkap', 'ASC')
                             ->findAll();
    }

    /**
     * Get statistik mutasi
     */
    public function getStats($tahun = null)
    {
        $builder = $this->select("
                COUNT(*) as total_mutasi,
                COUNT(DISTINCT aset_id) as total_aset_dimutasi,
                COUNT(DISTINCT penanggung_jawab_asal) as total_penanggung_jawab_asal,
                COUNT(DISTINCT penanggung_jawab_tujuan) as total_penanggung_jawab_tujuan
            ");
        
        if ($tahun) {
            $builder->where('YEAR(tanggal_mutasi)', $tahun);
        }
        
        $stats = $builder->first();
        
        // Tambah mutasi per bulan
        $stats['mutasi_per_bulan'] = $this->getMutasiPerBulan($tahun);
        
        return $stats ?? [
            'total_mutasi' => 0,
            'total_aset_dimutasi' => 0,
            'total_penanggung_jawab_asal' => 0,
            'total_penanggung_jawab_tujuan' => 0,
            'mutasi_per_bulan' => []
        ];
    }

    /**
     * Get mutasi per bulan
     */
    public function getMutasiPerBulan($tahun = null)
    {
        $builder = $this->select("
                DATE_FORMAT(tanggal_mutasi, '%Y-%m') as bulan,
                COUNT(*) as jumlah_mutasi,
                COUNT(DISTINCT aset_id) as jumlah_aset
            ")
            ->groupBy("DATE_FORMAT(tanggal_mutasi, '%Y-%m')")
            ->orderBy('bulan', 'ASC');
        
        if ($tahun) {
            $builder->where('YEAR(tanggal_mutasi)', $tahun);
        }
        
        return $builder->findAll();
    }

    /**
     * Get ringkasan mutasi per lokasi tujuan
     */
    public function getRingkasanPerLokasiTujuan($tahun = null)
    {
        $builder = $this->select("
                lokasi_tujuan,
                COUNT(*) as jumlah_mutasi,
                COUNT(DISTINCT aset_id) as jumlah_aset
            ")
            ->where('lokasi_tujuan IS NOT NULL')
            ->where('lokasi_tujuan !=', '')
            ->groupBy('lokasi_tujuan')
            ->orderBy('jumlah_mutasi', 'DESC');
        
        if ($tahun) {
            $builder->where('YEAR(tanggal_mutasi)', $tahun);
        }
        
        return $builder->findAll();
    }

    /**
     * Get ringkasan mutasi per penanggung jawab tujuan
     */
    public function getRingkasanPerPenanggungJawabTujuan($tahun = null)
    {
        $builder = $this->select("
                karyawan.id as karyawan_id,
                karyawan.nik,
                karyawan.nama_lengkap,
                karyawan.jabatan,
                COUNT(*) as jumlah_mutasi,
                COUNT(DISTINCT mutasi_aset.aset_id) as jumlah_aset
            ")
            ->join('karyawan', 'karyawan.id = mutasi_aset.penanggung_jawab_tujuan', 'left')
            ->where('mutasi_aset.penanggung_jawab_tujuan IS NOT NULL')
            ->groupBy('karyawan.id, karyawan.nik, karyawan.nama_lengkap, karyawan.jabatan')
            ->orderBy('jumlah_mutasi', 'DESC');
        
        if ($tahun) {
            $builder->where('YEAR(mutasi_aset.tanggal_mutasi)', $tahun);
        }
        
        return $builder->findAll();
    }

    /**
     * Get ringkasan mutasi per kategori aset
     */
    public function getRingkasanPerKategori($tahun = null)
    {
        $builder = $this->select("
                kategori.id as kategori_id,
                kategori.kode_kategori,
                kategori.nama_kategori,
                COUNT(*) as jumlah_mutasi,
                COUNT(DISTINCT mutasi_aset.aset_id) as jumlah_aset
            ")
            ->join('aset_tetap', 'aset_tetap.id = mutasi_aset.aset_id')
            ->join('aset_tetap_kategori as kategori', 'kategori.id = aset_tetap.kategori_id')
            ->groupBy('kategori.id, kategori.kode_kategori, kategori.nama_kategori')
            ->orderBy('jumlah_mutasi', 'DESC');
        
        if ($tahun) {
            $builder->where('YEAR(mutasi_aset.tanggal_mutasi)', $tahun);
        }
        
        return $builder->findAll();
    }

    /**
     * Get aset yang paling sering dimutasi
     */
    public function getAsetPalingSeringDimutasi($limit = 10, $tahun = null)
    {
        $builder = $this->select("
                aset_tetap.id as aset_id,
                aset_tetap.kode_aset,
                aset_tetap.nama_aset,
                kategori.nama_kategori,
                COUNT(*) as jumlah_mutasi
            ")
            ->join('aset_tetap', 'aset_tetap.id = mutasi_aset.aset_id')
            ->join('aset_tetap_kategori as kategori', 'kategori.id = aset_tetap.kategori_id')
            ->groupBy('aset_tetap.id, aset_tetap.kode_aset, aset_tetap.nama_aset, kategori.nama_kategori')
            ->orderBy('jumlah_mutasi', 'DESC')
            ->limit($limit);
        
        if ($tahun) {
            $builder->where('YEAR(mutasi_aset.tanggal_mutasi)', $tahun);
        }
        
        return $builder->findAll();
    }

    /**
     * Create mutasi aset
     */
    public function createMutasi($data)
    {
        $this->db->transStart();
        
        // Insert mutasi
        $mutasiId = $this->insert($data);
        
        if (!$mutasiId) {
            $this->db->transRollback();
            throw new \RuntimeException('Gagal menambahkan mutasi aset');
        }
        
        // Update aset (sudah dilakukan di afterInsert)
        $this->db->transComplete();
        
        return $this->find($mutasiId);
    }

    /**
     * Rollback mutasi (kembalikan ke lokasi asal)
     */
    public function rollbackMutasi($id)
    {
        $mutasi = $this->find($id);
        
        if (!$mutasi) {
            throw new \RuntimeException('Data mutasi tidak ditemukan');
        }
        
        $this->db->transStart();
        
        // Kembalikan aset ke lokasi asal
        $asetModel = new AsetTetapModel();
        $asetUpdate = [];
        
        if (!empty($mutasi['lokasi_asal'])) {
            $asetUpdate['lokasi'] = $mutasi['lokasi_asal'];
        }
        if (!empty($mutasi['penanggung_jawab_asal'])) {
            $asetUpdate['penanggung_jawab'] = $mutasi['penanggung_jawab_asal'];
        }
        
        if (!empty($asetUpdate)) {
            $asetModel->update($mutasi['aset_id'], $asetUpdate);
        }
        
        // Hapus mutasi
        $this->delete($id);
        
        $this->db->transComplete();
        
        return $this->db->transStatus();
    }

    /**
     * Export data untuk Excel
     */
    public function getExportData($filters = [])
    {
        $builder = $this->select('mutasi_aset.*, 
            aset_tetap.kode_aset,
            aset_tetap.nama_aset,
            aset_tetap.harga_perolehan,
            kategori.nama_kategori,
            karyawan_asal.nik as nik_asal,
            karyawan_asal.nama_lengkap as nama_penanggung_jawab_asal,
            karyawan_tujuan.nik as nik_tujuan,
            karyawan_tujuan.nama_lengkap as nama_penanggung_jawab_tujuan,
            creator.username as creator_name')
            ->join('aset_tetap', 'aset_tetap.id = mutasi_aset.aset_id', 'left')
            ->join('aset_tetap_kategori as kategori', 'kategori.id = aset_tetap.kategori_id', 'left')
            ->join('karyawan as karyawan_asal', 'karyawan_asal.id = mutasi_aset.penanggung_jawab_asal', 'left')
            ->join('karyawan as karyawan_tujuan', 'karyawan_tujuan.id = mutasi_aset.penanggung_jawab_tujuan', 'left')
            ->join('users as creator', 'creator.id = mutasi_aset.created_by', 'left');
        
        if (!empty($filters['tahun'])) {
            $builder->where('YEAR(mutasi_aset.tanggal_mutasi)', $filters['tahun']);
        }
        
        if (!empty($filters['aset_id'])) {
            $builder->where('mutasi_aset.aset_id', $filters['aset_id']);
        }
        
        $mutasi = $builder->orderBy('mutasi_aset.tanggal_mutasi', 'DESC')->findAll();
        
        $exportData = [];
        foreach ($mutasi as $item) {
            $exportData[] = [
                'Kode Aset' => $item['kode_aset'],
                'Nama Aset' => $item['nama_aset'],
                'Kategori' => $item['nama_kategori'] ?? '-',
                'Tanggal Mutasi' => $item['tanggal_mutasi'],
                'Lokasi Asal' => $item['lokasi_asal'] ?? '-',
                'Lokasi Tujuan' => $item['lokasi_tujuan'],
                'Penanggung Jawab Asal' => $item['nama_penanggung_jawab_asal'] ?? '-',
                'Penanggung Jawab Tujuan' => $item['nama_penanggung_jawab_tujuan'] ?? '-',
                'Alasan' => $item['alasan'],
                'Dokumen' => $item['dokumen'] ?? '-',
                'Dibuat Oleh' => $item['creator_name'] ?? '-',
                'Dibuat Tanggal' => $item['created_at']
            ];
        }
        
        return $exportData;
    }

    /**
     * Check if aset has been mutated
     */
    public function hasMutasi($asetId)
    {
        return $this->where('aset_id', $asetId)->countAllResults() > 0;
    }

    /**
     * Get last mutasi for aset
     */
    public function getLastMutasi($asetId)
    {
        return $this->where('aset_id', $asetId)
                    ->orderBy('tanggal_mutasi', 'DESC')
                    ->first();
    }
}