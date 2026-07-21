<?php
namespace App\Models\Accounting;

use CodeIgniter\Model;

class PenyusutanModel extends Model
{
    protected $table = 'penyusutan';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'aset_id',
        'periode',
        'nilai_buku_awal',
        'nilai_penyusutan',
        'akumulasi_penyusutan',
        'nilai_buku_akhir',
        'status',
        'posted_at',
        'jurnal_id'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'aset_id' => 'required|is_natural_no_zero',
        'periode' => 'required|valid_date',
        'nilai_buku_awal' => 'required|numeric',
        'nilai_penyusutan' => 'required|numeric',
        'akumulasi_penyusutan' => 'required|numeric',
        'nilai_buku_akhir' => 'required|numeric',
        'status' => 'permit_empty|in_list[Draft,Posted,Dibatalkan]'
    ];

    protected $validationMessages = [
        'aset_id' => [
            'required' => 'Aset harus dipilih'
        ],
        'periode' => [
            'required' => 'Periode harus diisi',
            'valid_date' => 'Format periode tidak valid'
        ],
        'nilai_buku_awal' => [
            'required' => 'Nilai buku awal harus diisi',
            'numeric' => 'Nilai buku awal harus berupa angka'
        ],
        'nilai_penyusutan' => [
            'required' => 'Nilai penyusutan harus diisi',
            'numeric' => 'Nilai penyusutan harus berupa angka'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = ['setDefaultValues', 'validatePeriode', 'validateNilaiBuku', 'setCreatedBy'];
    protected $beforeUpdate = ['validateStatusChange', 'validateNilaiBuku'];

    /**
     * Set default values
     */
    protected function setDefaultValues(array $data)
    {
        if (!isset($data['data']['status'])) {
            $data['data']['status'] = 'Draft';
        }
        
        return $data;
    }

    /**
     * Set created_by (untuk audit trail)
     */
    protected function setCreatedBy(array $data)
    {
        $data['data']['created_by'] = session()->get('user_id');
        return $data;
    }

    /**
     * Validasi periode unik per aset
     */
    protected function validatePeriode(array $data)
    {
        $asetId = $data['data']['aset_id'] ?? null;
        $periode = $data['data']['periode'] ?? null;
        $id = $data['id'][0] ?? null;
        
        if ($asetId && $periode) {
            $builder = $this->where('aset_id', $asetId)
                            ->where('periode', $periode);
            
            if ($id) {
                $builder->where('id !=', $id);
            }
            
            $exists = $builder->countAllResults() > 0;
            
            if ($exists) {
                throw new \RuntimeException('Penyusutan untuk aset ini pada periode ' . $periode . ' sudah ada');
            }
        }
        
        return $data;
    }

    /**
     * Validasi nilai buku tidak negatif
     */
    protected function validateNilaiBuku(array $data)
    {
        $nilaiBukuAkhir = $data['data']['nilai_buku_akhir'] ?? null;
        
        if ($nilaiBukuAkhir !== null && $nilaiBukuAkhir < 0) {
            throw new \RuntimeException('Nilai buku akhir tidak boleh negatif');
        }
        
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
                    // Jika status berubah menjadi Posted
                    if ($data['data']['status'] === 'Posted' && $current['status'] === 'Draft') {
                        if (empty($data['data']['posted_at'])) {
                            $data['data']['posted_at'] = date('Y-m-d H:i:s');
                        }
                    }
                    
                    // Jika status berubah menjadi Dibatalkan
                    if ($data['data']['status'] === 'Dibatalkan' && $current['status'] === 'Posted') {
                        throw new \RuntimeException('Penyusutan yang sudah diposting tidak dapat dibatalkan langsung. Batalkan jurnal terlebih dahulu.');
                    }
                    
                    // Tidak bisa mengubah status Draft menjadi selain Posted/Dibatalkan
                    if ($current['status'] === 'Draft' && !in_array($data['data']['status'], ['Posted', 'Dibatalkan'])) {
                        throw new \RuntimeException('Status tidak valid untuk penyusutan');
                    }
                }
            }
        }
        
        return $data;
    }

    /**
     * Get all penyusutan with filters
     */
    public function getAllWithFilters($filters = [], $perPage = 20, $page = 1)
    {
        $builder = $this->select('penyusutan.*, 
            aset_tetap.kode_aset,
            aset_tetap.nama_aset,
            aset_tetap.harga_perolehan,
            aset_tetap.status as aset_status,
            kategori.nama_kategori,
            kategori.kode_kategori,
            jurnal.nomor_jurnal,
            jurnal.status as jurnal_status,
            creator.username as creator_name')
            ->join('aset_tetap', 'aset_tetap.id = penyusutan.aset_id', 'left')
            ->join('aset_tetap_kategori as kategori', 'kategori.id = aset_tetap.kategori_id', 'left')
            ->join('jurnal', 'jurnal.id = penyusutan.jurnal_id', 'left')
            ->join('users as creator', 'creator.id = penyusutan.created_by', 'left');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('aset_tetap.kode_aset', $search)
                ->orLike('aset_tetap.nama_aset', $search)
                ->orLike('kategori.nama_kategori', $search)
                ->groupEnd();
        }
        
        if (!empty($filters['aset_id'])) {
            $builder->where('penyusutan.aset_id', $filters['aset_id']);
        }
        
        if (!empty($filters['kategori_id'])) {
            $builder->where('aset_tetap.kategori_id', $filters['kategori_id']);
        }
        
        if (!empty($filters['periode_mulai'])) {
            $builder->where('penyusutan.periode >=', $filters['periode_mulai']);
        }
        
        if (!empty($filters['periode_selesai'])) {
            $builder->where('penyusutan.periode <=', $filters['periode_selesai']);
        }
        
        if (!empty($filters['tahun'])) {
            $builder->where('YEAR(penyusutan.periode)', $filters['tahun']);
        }
        
        if (!empty($filters['bulan'])) {
            $builder->where('MONTH(penyusutan.periode)', $filters['bulan']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('penyusutan.status', $filters['status']);
        }
        
        $builder->orderBy('penyusutan.periode', 'DESC')
                ->orderBy('aset_tetap.kode_aset', 'ASC');
        
        // Get total for pagination
        $total = $builder->countAllResults(false);
        
        // Get paginated results
        $offset = ($page - 1) * $perPage;
        $penyusutan = $builder->limit($perPage, $offset)->findAll();
        
        return [
            'data' => $penyusutan,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Get penyusutan by ID with details
     */
    public function getWithDetails($id)
    {
        $penyusutan = $this->select('penyusutan.*, 
            aset_tetap.kode_aset,
            aset_tetap.nama_aset,
            aset_tetap.harga_perolehan,
            aset_tetap.nilai_residu,
            aset_tetap.masa_manfaat_tahun,
            aset_tetap.metode_penyusutan,
            aset_tetap.tanggal_perolehan,
            aset_tetap.status as aset_status,
            kategori.nama_kategori,
            kategori.kode_kategori,
            kategori.metode_penyusutan as kategori_metode,
            jurnal.nomor_jurnal,
            jurnal.status as jurnal_status,
            jurnal.created_at as jurnal_created_at,
            creator.username as creator_name,
            updater.username as updater_name')
            ->join('aset_tetap', 'aset_tetap.id = penyusutan.aset_id', 'left')
            ->join('aset_tetap_kategori as kategori', 'kategori.id = aset_tetap.kategori_id', 'left')
            ->join('jurnal', 'jurnal.id = penyusutan.jurnal_id', 'left')
            ->join('users as creator', 'creator.id = penyusutan.created_by', 'left')
            ->join('users as updater', 'updater.id = penyusutan.updated_by', 'left')
            ->where('penyusutan.id', $id)
            ->first();
        
        return $penyusutan;
    }

    /**
     * Get penyusutan by aset
     */
    public function getByAset($asetId)
    {
        return $this->where('aset_id', $asetId)
                    ->orderBy('periode', 'ASC')
                    ->findAll();
    }

    /**
     * Get penyusutan by aset and periode
     */
    public function getByAsetPeriode($asetId, $periode)
    {
        return $this->where('aset_id', $asetId)
                    ->where('periode', $periode)
                    ->first();
    }

    /**
     * Get penyusutan by periode
     */
    public function getByPeriode($periode)
    {
        return $this->where('periode', $periode)
                    ->orderBy('aset_id', 'ASC')
                    ->findAll();
    }

    /**
     * Get penyusutan by periode with summary
     */
    public function getByPeriodeWithSummary($periode)
    {
        $penyusutan = $this->getByPeriode($periode);
        
        $summary = [
            'total_aset' => count($penyusutan),
            'total_nilai_penyusutan' => array_sum(array_column($penyusutan, 'nilai_penyusutan')),
            'total_akumulasi' => array_sum(array_column($penyusutan, 'akumulasi_penyusutan')),
            'total_nilai_buku_akhir' => array_sum(array_column($penyusutan, 'nilai_buku_akhir'))
        ];
        
        return [
            'data' => $penyusutan,
            'summary' => $summary
        ];
    }

    /**
     * Get total akumulasi penyusutan per kategori
     */
    public function getTotalAkumulasiByKategori($kategoriId)
    {
        $result = $this->select('SUM(penyusutan.akumulasi_penyusutan) as total')
            ->join('aset_tetap', 'aset_tetap.id = penyusutan.aset_id')
            ->where('aset_tetap.kategori_id', $kategoriId)
            ->where('penyusutan.status', 'Posted')
            ->first();
        
        return $result['total'] ?? 0;
    }

    /**
     * Get total akumulasi penyusutan per aset (latest)
     */
    public function getLatestAkumulasiByAset($asetId)
    {
        $latest = $this->where('aset_id', $asetId)
                       ->where('status', 'Posted')
                       ->orderBy('periode', 'DESC')
                       ->first();
        
        return $latest ? $latest['akumulasi_penyusutan'] : 0;
    }

    /**
     * Get latest penyusutan by aset
     */
    public function getLatestByAset($asetId)
    {
        return $this->where('aset_id', $asetId)
                    ->where('status', 'Posted')
                    ->orderBy('periode', 'DESC')
                    ->first();
    }

    /**
     * Generate penyusutan untuk aset tertentu
     */
    public function generateForAset($asetId, $periode, $force = false)
    {
        $asetModel = new AsetTetapModel();
        $aset = $asetModel->find($asetId);
        
        if (!$aset) {
            throw new \RuntimeException('Aset tidak ditemukan');
        }
        
        if ($aset['status'] !== 'Aktif') {
            throw new \RuntimeException('Aset tidak aktif, tidak dapat menghitung penyusutan');
        }
        
        // Cek apakah sudah ada
        $existing = $this->getByAsetPeriode($asetId, $periode);
        if ($existing && !$force) {
            throw new \RuntimeException('Penyusutan untuk aset ini pada periode ' . $periode . ' sudah ada');
        }
        
        // Ambil nilai buku awal
        $previous = $this->getLatestByAset($asetId);
        if ($previous) {
            $nilaiBukuAwal = $previous['nilai_buku_akhir'];
            $akumulasiSebelum = $previous['akumulasi_penyusutan'];
        } else {
            $nilaiBukuAwal = $aset['harga_perolehan'];
            $akumulasiSebelum = 0;
        }
        
        // Hitung penyusutan bulanan
        $penyusutanBulanan = $asetModel->hitungPenyusutanBulanan($asetId);
        
        // Jika nilai buku awal sudah <= nilai residu, stop
        $nilaiResidu = $aset['nilai_residu'] ?? 0;
        if ($nilaiBukuAwal <= $nilaiResidu) {
            return null;
        }
        
        // Hitung nilai penyusutan (tidak boleh melebihi nilai buku - residu)
        $maxPenyusutan = $nilaiBukuAwal - $nilaiResidu;
        $nilaiPenyusutan = min($penyusutanBulanan, $maxPenyusutan);
        
        if ($nilaiPenyusutan <= 0) {
            return null;
        }
        
        $akumulasiBaru = $akumulasiSebelum + $nilaiPenyusutan;
        $nilaiBukuAkhir = $nilaiBukuAwal - $nilaiPenyusutan;
        
        $data = [
            'aset_id' => $asetId,
            'periode' => $periode,
            'nilai_buku_awal' => $nilaiBukuAwal,
            'nilai_penyusutan' => $nilaiPenyusutan,
            'akumulasi_penyusutan' => $akumulasiBaru,
            'nilai_buku_akhir' => $nilaiBukuAkhir,
            'status' => 'Draft'
        ];
        
        if ($existing) {
            $this->update($existing['id'], $data);
            return $this->find($existing['id']);
        } else {
            $id = $this->insert($data);
            return $this->find($id);
        }
    }

    /**
     * Generate penyusutan untuk semua aset aktif dalam periode tertentu
     */
    public function generateForAllAset($periode, $force = false)
    {
        $asetModel = new AsetTetapModel();
        $asetList = $asetModel->where('status', 'Aktif')
                              ->where('deleted_at IS NULL')
                              ->findAll();
        
        $results = [];
        foreach ($asetList as $aset) {
            try {
                $result = $this->generateForAset($aset['id'], $periode, $force);
                if ($result) {
                    $results[] = [
                        'aset' => $aset['nama_aset'],
                        'status' => 'success',
                        'nilai_penyusutan' => $result['nilai_penyusutan']
                    ];
                } else {
                    $results[] = [
                        'aset' => $aset['nama_aset'],
                        'status' => 'skipped',
                        'reason' => 'Nilai buku sudah mencapai nilai residu'
                    ];
                }
            } catch (\Exception $e) {
                $results[] = [
                    'aset' => $aset['nama_aset'],
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
        }
        
        return $results;
    }

    /**
     * Generate penyusutan untuk periode tertentu (bulanan)
     */
    public function generateForPeriode($tahun, $bulan, $force = false)
    {
        $periode = date('Y-m-01', strtotime("$tahun-$bulan-01"));
        return $this->generateForAllAset($periode, $force);
    }

    /**
     * Post penyusutan ke jurnal
     */
    public function postPenyusutan($id, $jurnalId)
    {
        $penyusutan = $this->find($id);
        
        if (!$penyusutan) {
            throw new \RuntimeException('Data penyusutan tidak ditemukan');
        }
        
        if ($penyusutan['status'] !== 'Draft') {
            throw new \RuntimeException('Hanya penyusutan dengan status Draft yang bisa diposting');
        }
        
        return $this->update($id, [
            'status' => 'Posted',
            'posted_at' => date('Y-m-d H:i:s'),
            'jurnal_id' => $jurnalId
        ]);
    }

    /**
     * Post all penyusutan in periode
     */
    public function postAllForPeriode($periode, $jurnalId = null)
    {
        $penyusutanList = $this->where('periode', $periode)
                               ->where('status', 'Draft')
                               ->findAll();
        
        $results = [];
        foreach ($penyusutanList as $item) {
            try {
                $this->postPenyusutan($item['id'], $jurnalId);
                $results[] = [
                    'id' => $item['id'],
                    'status' => 'success'
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'id' => $item['id'],
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
        }
        
        return $results;
    }

    /**
     * Get proyeksi penyusutan untuk aset
     */
    public function getProyeksiPenyusutan($asetId, $bulanKeDepan = 12)
    {
        $asetModel = new AsetTetapModel();
        $aset = $asetModel->find($asetId);
        
        if (!$aset || $aset['status'] !== 'Aktif') {
            return [];
        }
        
        $latest = $this->getLatestByAset($asetId);
        $nilaiBuku = $latest ? $latest['nilai_buku_akhir'] : $aset['harga_perolehan'];
        $akumulasi = $latest ? $latest['akumulasi_penyusutan'] : 0;
        $penyusutanBulanan = $asetModel->hitungPenyusutanBulanan($asetId);
        $nilaiResidu = $aset['nilai_residu'] ?? 0;
        
        $proyeksi = [];
        $periodeAwal = $latest ? date('Y-m-01', strtotime($latest['periode'] . ' +1 month')) : date('Y-m-01');
        
        for ($i = 1; $i <= $bulanKeDepan; $i++) {
            $periode = date('Y-m-01', strtotime($periodeAwal . " +" . ($i - 1) . " months"));
            
            $maxPenyusutan = $nilaiBuku - $nilaiResidu;
            $penyusutan = min($penyusutanBulanan, max($maxPenyusutan, 0));
            
            if ($penyusutan <= 0) {
                break;
            }
            
            $akumulasi += $penyusutan;
            $nilaiBuku -= $penyusutan;
            
            $proyeksi[] = [
                'periode' => $periode,
                'nilai_buku_awal' => $nilaiBuku + $penyusutan,
                'nilai_penyusutan' => $penyusutan,
                'akumulasi_penyusutan' => $akumulasi,
                'nilai_buku_akhir' => $nilaiBuku
            ];
            
            if ($nilaiBuku <= $nilaiResidu) {
                break;
            }
        }
        
        return $proyeksi;
    }

    /**
     * Get ringkasan penyusutan per bulan
     */
    public function getRingkasanPerBulan($tahun = null)
    {
        $builder = $this->select("
                DATE_FORMAT(periode, '%Y-%m') as bulan,
                COUNT(*) as jumlah_aset,
                SUM(nilai_penyusutan) as total_penyusutan,
                SUM(akumulasi_penyusutan) as total_akumulasi
            ")
            ->where('status', 'Posted')
            ->groupBy("DATE_FORMAT(periode, '%Y-%m')")
            ->orderBy("DATE_FORMAT(periode, '%Y-%m')", 'ASC');
        
        if ($tahun) {
            $builder->where('YEAR(periode)', $tahun);
        }
        
        return $builder->findAll();
    }

    /**
     * Get ringkasan penyusutan per tahun
     */
    public function getRingkasanPerTahun()
    {
        return $this->select("
                YEAR(periode) as tahun,
                COUNT(*) as jumlah_aset,
                SUM(nilai_penyusutan) as total_penyusutan,
                SUM(akumulasi_penyusutan) as total_akumulasi
            ")
            ->where('status', 'Posted')
            ->groupBy('YEAR(periode)')
            ->orderBy('YEAR(periode)', 'ASC')
            ->findAll();
    }

    /**
     * Get ringkasan penyusutan per aset
     */
    public function getRingkasanPerAset($tahun = null)
    {
        $builder = $this->select("
                aset_tetap.id as aset_id,
                aset_tetap.kode_aset,
                aset_tetap.nama_aset,
                aset_tetap.harga_perolehan,
                kategori.nama_kategori,
                COUNT(penyusutan.id) as jumlah_bulan,
                SUM(penyusutan.nilai_penyusutan) as total_penyusutan,
                MAX(penyusutan.akumulasi_penyusutan) as total_akumulasi
            ")
            ->join('aset_tetap', 'aset_tetap.id = penyusutan.aset_id')
            ->join('aset_tetap_kategori as kategori', 'kategori.id = aset_tetap.kategori_id')
            ->where('penyusutan.status', 'Posted')
            ->groupBy('aset_tetap.id, aset_tetap.kode_aset, aset_tetap.nama_aset, aset_tetap.harga_perolehan, kategori.nama_kategori');
        
        if ($tahun) {
            $builder->where('YEAR(penyusutan.periode)', $tahun);
        }
        
        $results = $builder->findAll();
        
        foreach ($results as &$item) {
            $item['nilai_buku'] = $item['harga_perolehan'] - ($item['total_akumulasi'] ?? 0);
        }
        
        return $results;
    }

    /**
     * Get statistics untuk dashboard
     */
    public function getStats($tahun = null)
    {
        $builder = $this->where('status', 'Posted');
        
        if ($tahun) {
            $builder->where('YEAR(periode)', $tahun);
        }
        
        $stats = $builder->select("
                COUNT(*) as total_transaksi,
                SUM(nilai_penyusutan) as total_penyusutan,
                COUNT(DISTINCT aset_id) as total_aset
            ")
            ->first();
        
        // Tambah total akumulasi keseluruhan
        $totalAkumulasi = $this->select('MAX(akumulasi_penyusutan) as total')
                               ->where('status', 'Posted')
                               ->groupBy('aset_id')
                               ->findAll();
        
        $stats['total_akumulasi'] = array_sum(array_column($totalAkumulasi, 'total'));
        
        return $stats ?? [
            'total_transaksi' => 0,
            'total_penyusutan' => 0,
            'total_akumulasi' => 0,
            'total_aset' => 0
        ];
    }

    /**
     * Export data untuk Excel
     */
    public function getExportData($filters = [])
    {
        $builder = $this->select('penyusutan.*, 
            aset_tetap.kode_aset,
            aset_tetap.nama_aset,
            aset_tetap.harga_perolehan,
            kategori.nama_kategori,
            jurnal.nomor_jurnal')
            ->join('aset_tetap', 'aset_tetap.id = penyusutan.aset_id', 'left')
            ->join('aset_tetap_kategori as kategori', 'kategori.id = aset_tetap.kategori_id', 'left')
            ->join('jurnal', 'jurnal.id = penyusutan.jurnal_id', 'left');
        
        if (!empty($filters['tahun'])) {
            $builder->where('YEAR(penyusutan.periode)', $filters['tahun']);
        }
        
        if (!empty($filters['bulan'])) {
            $builder->where('MONTH(penyusutan.periode)', $filters['bulan']);
        }
        
        if (!empty($filters['aset_id'])) {
            $builder->where('penyusutan.aset_id', $filters['aset_id']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('penyusutan.status', $filters['status']);
        }
        
        $penyusutan = $builder->orderBy('penyusutan.periode', 'DESC')
                              ->orderBy('aset_tetap.kode_aset', 'ASC')
                              ->findAll();
        
        $exportData = [];
        foreach ($penyusutan as $item) {
            $exportData[] = [
                'Kode Aset' => $item['kode_aset'],
                'Nama Aset' => $item['nama_aset'],
                'Kategori' => $item['nama_kategori'] ?? '-',
                'Periode' => date('F Y', strtotime($item['periode'])),
                'Nilai Buku Awal' => $item['nilai_buku_awal'],
                'Nilai Penyusutan' => $item['nilai_penyusutan'],
                'Akumulasi Penyusutan' => $item['akumulasi_penyusutan'],
                'Nilai Buku Akhir' => $item['nilai_buku_akhir'],
                'Status' => $item['status'],
                'No Jurnal' => $item['nomor_jurnal'] ?? '-',
                'Tanggal Posting' => $item['posted_at'] ?? '-'
            ];
        }
        
        return $exportData;
    }

    /**
     * Check if aset has penyusutan records
     */
    public function hasPenyusutan($asetId)
    {
        return $this->where('aset_id', $asetId)->countAllResults() > 0;
    }

    /**
     * Delete penyusutan (soft delete) - untuk maintenance
     */
    public function deletePenyusutan($id)
    {
        $penyusutan = $this->find($id);
        
        if (!$penyusutan) {
            throw new \RuntimeException('Data penyusutan tidak ditemukan');
        }
        
        if ($penyusutan['status'] === 'Posted') {
            throw new \RuntimeException('Penyusutan yang sudah diposting tidak dapat dihapus');
        }
        
        return $this->delete($id);
    }
}