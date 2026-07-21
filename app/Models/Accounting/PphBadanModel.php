<?php
namespace App\Models\Accounting;

use CodeIgniter\Model;

class PphBadanModel extends Model
{
    protected $table = 'pph_badan';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'periode',
        'tahun',
        'triwulan',
        'penghasilan_bruto',
        'biaya_fiskal',
        'penghasilan_neto_fiskal',
        'kompensasi_kerugian',
        'pkp',
        'tarif',
        'pph_terutang',
        'kredit_pajak',
        'pph_kurang_bayar',
        'status',
        'tanggal_lapor',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'periode' => 'required|in_list[Tahunan,Triwulan]',
        'tahun' => 'required|numeric|min_length[4]|max_length[4]',
        'triwulan' => 'permit_empty|numeric|in_list[1,2,3,4]',
        'penghasilan_bruto' => 'permit_empty|numeric',
        'biaya_fiskal' => 'permit_empty|numeric',
        'penghasilan_neto_fiskal' => 'permit_empty|numeric',
        'kompensasi_kerugian' => 'permit_empty|numeric',
        'pkp' => 'permit_empty|numeric',
        'tarif' => 'permit_empty|numeric|greater_than[0]|less_than_equal_to[100]',
        'pph_terutang' => 'permit_empty|numeric',
        'kredit_pajak' => 'permit_empty|numeric',
        'pph_kurang_bayar' => 'permit_empty|numeric',
        'status' => 'permit_empty|in_list[Draft,Selesai,Dilaporkan]',
        'tanggal_lapor' => 'permit_empty|valid_date'
    ];

    protected $validationMessages = [
        'periode' => [
            'required' => 'Periode harus dipilih',
            'in_list' => 'Periode harus Tahunan atau Triwulan'
        ],
        'tahun' => [
            'required' => 'Tahun harus diisi',
            'numeric' => 'Tahun harus berupa angka'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = ['setDefaultValues', 'validateUnique', 'hitungPph', 'setCreatedBy'];
    protected $beforeUpdate = ['validateStatusChange', 'hitungPph'];

    /**
     * Set default values
     */
    protected function setDefaultValues(array $data)
    {
        if (!isset($data['data']['status'])) {
            $data['data']['status'] = 'Draft';
        }
        
        if (!isset($data['data']['tarif'])) {
            // Ambil tarif PPh Badan terbaru
            $tarifModel = new TarifPajakModel();
            $tarif = $tarifModel->getActiveTarif('PPh Badan');
            $data['data']['tarif'] = $tarif ? $tarif['persentase'] : 22;
        }
        
        // Set default 0 untuk numeric fields
        $numericFields = [
            'penghasilan_bruto', 'biaya_fiskal', 'penghasilan_neto_fiskal',
            'kompensasi_kerugian', 'pkp', 'pph_terutang', 'kredit_pajak', 'pph_kurang_bayar'
        ];
        
        foreach ($numericFields as $field) {
            if (!isset($data['data'][$field])) {
                $data['data'][$field] = 0;
            }
        }
        
        return $data;
    }

    /**
     * Validasi unique per periode
     */
    protected function validateUnique(array $data)
    {
        $periode = $data['data']['periode'] ?? null;
        $tahun = $data['data']['tahun'] ?? null;
        $triwulan = $data['data']['triwulan'] ?? null;
        $id = $data['id'][0] ?? null;
        
        if ($periode && $tahun) {
            $builder = $this->where('periode', $periode)
                            ->where('tahun', $tahun);
            
            if ($periode === 'Triwulan' && $triwulan) {
                $builder->where('triwulan', $triwulan);
            }
            
            if ($id) {
                $builder->where('id !=', $id);
            }
            
            $exists = $builder->countAllResults() > 0;
            
            if ($exists) {
                $periodeText = $periode === 'Tahunan' ? 'Tahunan ' . $tahun : 'Triwulan ' . $triwulan . ' ' . $tahun;
                throw new \RuntimeException('Perhitungan PPh Badan untuk ' . $periodeText . ' sudah ada');
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
     * Hitung PPh Badan
     */
    protected function hitungPph(array $data)
    {
        $penghasilanNetoFiskal = $data['data']['penghasilan_neto_fiskal'] ?? 0;
        $kompensasiKerugian = $data['data']['kompensasi_kerugian'] ?? 0;
        $tarif = $data['data']['tarif'] ?? 22;
        
        // Hitung PKP (Penghasilan Kena Pajak)
        $pkp = max(0, $penghasilanNetoFiskal - $kompensasiKerugian);
        $data['data']['pkp'] = $pkp;
        
        // Hitung PPh Terutang
        $pphTerutang = $pkp * ($tarif / 100);
        $data['data']['pph_terutang'] = $pphTerutang;
        
        // Hitung PPh Kurang Bayar (PPh Terutang - Kredit Pajak)
        $kreditPajak = $data['data']['kredit_pajak'] ?? 0;
        $data['data']['pph_kurang_bayar'] = $pphTerutang - $kreditPajak;
        
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
                    $oldStatus = $current['status'];
                    $newStatus = $data['data']['status'];
                    
                    // Validasi urutan status
                    $validTransitions = [
                        'Draft' => ['Selesai', 'Dilaporkan'],
                        'Selesai' => ['Dilaporkan'],
                        'Dilaporkan' => [] // Tidak bisa berubah lagi
                    ];
                    
                    if (isset($validTransitions[$oldStatus]) && !in_array($newStatus, $validTransitions[$oldStatus])) {
                        throw new \RuntimeException("Status tidak dapat berubah dari {$oldStatus} menjadi {$newStatus}");
                    }
                    
                    // Jika status berubah menjadi Dilaporkan
                    if ($newStatus === 'Dilaporkan' && $oldStatus !== 'Dilaporkan') {
                        if (empty($data['data']['tanggal_lapor'])) {
                            $data['data']['tanggal_lapor'] = date('Y-m-d');
                        }
                        
                        // Validasi harus sudah selesai dihitung
                        if ($oldStatus !== 'Selesai') {
                            throw new \RuntimeException('Perhitungan harus diselesaikan terlebih dahulu sebelum dilaporkan');
                        }
                    }
                    
                    // Jika status berubah menjadi Selesai
                    if ($newStatus === 'Selesai' && $oldStatus === 'Draft') {
                        // Validasi data harus lengkap
                        if (empty($current['penghasilan_bruto']) && empty($data['data']['penghasilan_bruto'])) {
                            throw new \RuntimeException('Penghasilan bruto harus diisi sebelum menyelesaikan perhitungan');
                        }
                    }
                }
            }
        }
        
        return $data;
    }

    /**
     * Get all perhitungan PPh Badan with filters
     */
    public function getAllWithFilters($filters = [], $perPage = 20, $page = 1)
    {
        $builder = $this->select('pph_badan.*, 
            creator.username as creator_name,
            creator.name as creator_fullname')
            ->join('users as creator', 'creator.id = pph_badan.created_by', 'left');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('tahun', $search)
                ->orLike('triwulan', $search)
                ->orLike('status', $search)
                ->groupEnd();
        }
        
        if (!empty($filters['periode'])) {
            $builder->where('periode', $filters['periode']);
        }
        
        if (!empty($filters['tahun'])) {
            $builder->where('tahun', $filters['tahun']);
        }
        
        if (!empty($filters['triwulan'])) {
            $builder->where('triwulan', $filters['triwulan']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }
        
        $builder->orderBy('tahun', 'DESC')
                ->orderBy('triwulan', 'DESC');
        
        // Get total for pagination
        $total = $builder->countAllResults(false);
        
        // Get paginated results
        $offset = ($page - 1) * $perPage;
        $pph = $builder->limit($perPage, $offset)->findAll();
        
        return [
            'data' => $pph,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Get PPh Badan by ID with details
     */
    public function getWithDetails($id)
    {
        $pph = $this->select('pph_badan.*, 
            creator.username as creator_name,
            creator.name as creator_fullname,
            updater.username as updater_name')
            ->join('users as creator', 'creator.id = pph_badan.created_by', 'left')
            ->join('users as updater', 'updater.id = pph_badan.updated_by', 'left')
            ->where('pph_badan.id', $id)
            ->first();
        
        return $pph;
    }

    /**
     * Get PPh Badan by tahun
     */
    public function getByTahun($tahun)
    {
        return $this->where('tahun', $tahun)
                    ->orderBy('periode', 'ASC')
                    ->orderBy('triwulan', 'ASC')
                    ->findAll();
    }

    /**
     * Get PPh Badan tahunan by tahun
     */
    public function getTahunanByTahun($tahun)
    {
        return $this->where('periode', 'Tahunan')
                    ->where('tahun', $tahun)
                    ->first();
    }

    /**
     * Get PPh Badan triwulan by tahun and triwulan
     */
    public function getTriwulanByPeriode($tahun, $triwulan)
    {
        return $this->where('periode', 'Triwulan')
                    ->where('tahun', $tahun)
                    ->where('triwulan', $triwulan)
                    ->first();
    }

    /**
     * Get PPh Badan yang belum dilaporkan
     */
    public function getBelumDilaporkan()
    {
        return $this->where('status !=', 'Dilaporkan')
                    ->orderBy('tahun', 'DESC')
                    ->orderBy('triwulan', 'DESC')
                    ->findAll();
    }

    /**
     * Get ringkasan per tahun
     */
    public function getRingkasanPerTahun()
    {
        return $this->select("
                tahun,
                COUNT(*) as jumlah_periode,
                SUM(pph_terutang) as total_pph_terutang,
                SUM(kredit_pajak) as total_kredit_pajak,
                SUM(pph_kurang_bayar) as total_pph_kurang_bayar
            ")
            ->where('status', 'Selesai')
            ->groupBy('tahun')
            ->orderBy('tahun', 'DESC')
            ->findAll();
    }

    /**
     * Get kompensasi kerugian yang tersedia
     */
    public function getKompensasiKerugianTersedia($tahun)
    {
        $kompensasi = 0;
        
        // Cari kerugian dari 5 tahun sebelumnya
        for ($i = 1; $i <= 5; $i++) {
            $tahunLalu = $tahun - $i;
            $pphTahunan = $this->getTahunanByTahun($tahunLalu);
            
            if ($pphTahunan && $pphTahunan['penghasilan_neto_fiskal'] < 0) {
                $kerugian = abs($pphTahunan['penghasilan_neto_fiskal']);
                $sisaKompensasi = $kerugian - ($pphTahunan['kompensasi_kerugian'] ?? 0);
                
                if ($sisaKompensasi > 0) {
                    $kompensasi += $sisaKompensasi;
                }
            }
        }
        
        return $kompensasi;
    }

    /**
     * Hitung otomatis dari laporan keuangan
     */
    public function calculateFromFinancial($tahun, $periode = 'Tahunan', $triwulan = null)
    {
        // Ambil data laba rugi dari laporan keuangan
        $labaRugiModel = new \App\Models\Accounting\LabaRugiModel();
        
        $startDate = $tahun . '-01-01';
        $endDate = $tahun . '-12-31';
        
        if ($periode === 'Triwulan' && $triwulan) {
            $bulanStart = ($triwulan - 1) * 3 + 1;
            $bulanEnd = $triwulan * 3;
            $startDate = $tahun . '-' . str_pad($bulanStart, 2, '0', STR_PAD_LEFT) . '-01';
            $endDate = date('Y-m-t', strtotime($tahun . '-' . $bulanEnd . '-01'));
        }
        
        // Ambil total pendapatan dan beban
        $pendapatan = $labaRugiModel->getTotalPendapatan($startDate, $endDate);
        $beban = $labaRugiModel->getTotalBeban($startDate, $endDate);
        
        $labaRugi = $pendapatan - $beban;
        
        // Hitung penghasilan neto fiskal (asumsi sama dengan laba rugi)
        $penghasilanNetoFiskal = $labaRugi;
        
        // Ambil kompensasi kerugian jika ada
        $kompensasi = 0;
        if ($penghasilanNetoFiskal > 0) {
            $kompensasi = $this->getKompensasiKerugianTersedia($tahun);
        }
        
        return [
            'penghasilan_bruto' => $pendapatan,
            'biaya_fiskal' => $beban,
            'penghasilan_neto_fiskal' => $penghasilanNetoFiskal,
            'kompensasi_kerugian' => min($kompensasi, $penghasilanNetoFiskal),
            'pkp' => max(0, $penghasilanNetoFiskal - $kompensasi)
        ];
    }

    /**
     * Mark as completed (Selesai)
     */
    public function markAsCompleted($id)
    {
        $pph = $this->find($id);
        
        if (!$pph) {
            throw new \RuntimeException('Data PPh Badan tidak ditemukan');
        }
        
        if ($pph['status'] !== 'Draft') {
            throw new \RuntimeException('Hanya perhitungan dengan status Draft yang dapat diselesaikan');
        }
        
        return $this->update($id, ['status' => 'Selesai']);
    }

    /**
     * Mark as reported (Dilaporkan)
     */
    public function markAsReported($id, $tanggalLapor = null)
    {
        $pph = $this->find($id);
        
        if (!$pph) {
            throw new \RuntimeException('Data PPh Badan tidak ditemukan');
        }
        
        if ($pph['status'] !== 'Selesai') {
            throw new \RuntimeException('Hanya perhitungan dengan status Selesai yang dapat dilaporkan');
        }
        
        return $this->update($id, [
            'status' => 'Dilaporkan',
            'tanggal_lapor' => $tanggalLapor ?? date('Y-m-d')
        ]);
    }

    /**
     * Get total kredit pajak dari PPh 23, PPh 25, dll
     */
    public function getTotalKreditPajak($tahun)
    {
        $setoranModel = new SetoranPajakModel();
        
        $kredit = $setoranModel->select('SUM(nominal) as total')
                               ->where('jenis_pajak', 'PPh 23')
                               ->where('YEAR(tanggal_setor)', $tahun)
                               ->first();
        
        $total = $kredit['total'] ?? 0;
        
        // Tambah PPh 25 jika ada
        $pph25 = $setoranModel->select('SUM(nominal) as total')
                              ->where('jenis_pajak', 'PPh 25')
                              ->where('YEAR(tanggal_setor)', $tahun)
                              ->first();
        
        $total += $pph25['total'] ?? 0;
        
        return $total;
    }

    /**
     * Get statistics untuk dashboard
     */
    public function getStats($tahun = null)
    {
        $builder = $this->where('status', 'Selesai');
        
        if ($tahun) {
            $builder->where('tahun', $tahun);
        }
        
        $stats = $builder->select("
                COUNT(*) as total_periode,
                SUM(penghasilan_bruto) as total_penghasilan_bruto,
                SUM(biaya_fiskal) as total_biaya_fiskal,
                SUM(penghasilan_neto_fiskal) as total_penghasilan_neto,
                SUM(pkp) as total_pkp,
                SUM(pph_terutang) as total_pph_terutang,
                SUM(kredit_pajak) as total_kredit_pajak,
                SUM(pph_kurang_bayar) as total_pph_kurang_bayar,
                COUNT(CASE WHEN pph_kurang_bayar > 0 THEN 1 END) as jumlah_kurang_bayar,
                COUNT(CASE WHEN pph_kurang_bayar < 0 THEN 1 END) as jumlah_lebih_bayar
            ")
            ->first();
        
        return $stats ?? [
            'total_periode' => 0,
            'total_penghasilan_bruto' => 0,
            'total_biaya_fiskal' => 0,
            'total_penghasilan_neto' => 0,
            'total_pkp' => 0,
            'total_pph_terutang' => 0,
            'total_kredit_pajak' => 0,
            'total_pph_kurang_bayar' => 0,
            'jumlah_kurang_bayar' => 0,
            'jumlah_lebih_bayar' => 0
        ];
    }

    /**
     * Get summary untuk SPT Tahunan
     */
    public function getSptTahunanSummary($tahun)
    {
        $pphTahunan = $this->getTahunanByTahun($tahun);
        
        if (!$pphTahunan) {
            return null;
        }
        
        // Ambil total PPh yang sudah disetor (PPh 23, PPh 25)
        $totalKredit = $this->getTotalKreditPajak($tahun);
        
        $pphTahunan['total_kredit_pajak_aktual'] = $totalKredit;
        $pphTahunan['pph_kurang_bayar_aktual'] = $pphTahunan['pph_terutang'] - $totalKredit;
        
        return $pphTahunan;
    }

    /**
     * Export data untuk Excel
     */
    public function getExportData($filters = [])
    {
        $builder = $this->select('pph_badan.*, creator.username as creator_name')
                        ->join('users as creator', 'creator.id = pph_badan.created_by', 'left');
        
        if (!empty($filters['tahun'])) {
            $builder->where('tahun', $filters['tahun']);
        }
        
        if (!empty($filters['periode'])) {
            $builder->where('periode', $filters['periode']);
        }
        
        $pph = $builder->orderBy('tahun', 'DESC')
                       ->orderBy('triwulan', 'DESC')
                       ->findAll();
        
        $exportData = [];
        foreach ($pph as $item) {
            $exportData[] = [
                'Periode' => $item['periode'] === 'Tahunan' ? 'Tahunan' : 'Triwulan ' . $item['triwulan'],
                'Tahun' => $item['tahun'],
                'Penghasilan Bruto' => $item['penghasilan_bruto'],
                'Biaya Fiskal' => $item['biaya_fiskal'],
                'Penghasilan Neto Fiskal' => $item['penghasilan_neto_fiskal'],
                'Kompensasi Kerugian' => $item['kompensasi_kerugian'],
                'PKP' => $item['pkp'],
                'Tarif' => $item['tarif'] . '%',
                'PPh Terutang' => $item['pph_terutang'],
                'Kredit Pajak' => $item['kredit_pajak'],
                'PPh Kurang Bayar' => $item['pph_kurang_bayar'],
                'Status' => $item['status'],
                'Tanggal Lapor' => $item['tanggal_lapor'] ?? '-',
                'Dibuat Oleh' => $item['creator_name'] ?? '-'
            ];
        }
        
        return $exportData;
    }

    /**
     * Calculate PPh Badan for current year
     */
    public function calculateCurrentYear()
    {
        $tahun = date('Y');
        $existing = $this->getTahunanByTahun($tahun);
        
        if ($existing && $existing['status'] !== 'Draft') {
            throw new \RuntimeException('Perhitungan PPh Badan tahun ' . $tahun . ' sudah selesai/dilaporkan');
        }
        
        $data = $this->calculateFromFinancial($tahun);
        $data['periode'] = 'Tahunan';
        $data['tahun'] = $tahun;
        $data['status'] = 'Draft';
        
        if ($existing) {
            $this->update($existing['id'], $data);
            return $this->find($existing['id']);
        } else {
            $id = $this->insert($data);
            return $this->find($id);
        }
    }

    /**
     * Calculate PPh Badan for quarter
     */
    public function calculateQuarter($tahun, $triwulan)
    {
        $existing = $this->getTriwulanByPeriode($tahun, $triwulan);
        
        if ($existing && $existing['status'] !== 'Draft') {
            throw new \RuntimeException('Perhitungan PPh Badan triwulan ' . $triwulan . ' tahun ' . $tahun . ' sudah selesai/dilaporkan');
        }
        
        $data = $this->calculateFromFinancial($tahun, 'Triwulan', $triwulan);
        $data['periode'] = 'Triwulan';
        $data['tahun'] = $tahun;
        $data['triwulan'] = $triwulan;
        $data['status'] = 'Draft';
        
        if ($existing) {
            $this->update($existing['id'], $data);
            return $this->find($existing['id']);
        } else {
            $id = $this->insert($data);
            return $this->find($id);
        }
    }

    /**
     * Get all years that have calculations
     */
    public function getAvailableYears()
    {
        $years = $this->select('tahun')
                      ->groupBy('tahun')
                      ->orderBy('tahun', 'DESC')
                      ->findAll();
        
        return array_column($years, 'tahun');
    }
}