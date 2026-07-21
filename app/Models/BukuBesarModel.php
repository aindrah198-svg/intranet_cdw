<?php

namespace App\Models;

use CodeIgniter\Model;

class BukuBesarModel extends Model
{
    protected $table = 'buku_besar';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        // Link ke sumber
        'jurnal_id',
        'jurnal_detail_id',
        'coa_id',
        
        // Denormalisasi
        'kode_akun',
        'nama_akun',
        'nomor_jurnal',
        'tanggal',
        'keterangan',
        'tipe_jurnal',
        
        // Nilai transaksi
        'debit',
        'kredit',
        
        // Saldo
        'saldo_akhir',
        
        // Batch processing
        'batch_id',
        'status',
        'error_message',
        
        // Void & koreksi
        'is_void',
        'void_reason',
        'voided_at',
        'voided_by',
        'reversed_by_id',
        
        // Periode
        'tahun',
        'bulan',
        'periode',
        
        // Timestamp proses
        'processed_at',
        'processed_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'jurnal_id' => 'required|integer',
        'jurnal_detail_id' => 'required|integer|is_unique[buku_besar.jurnal_detail_id]',
        'coa_id' => 'required|integer',
        'kode_akun' => 'required|max_length[20]',
        'nama_akun' => 'required|max_length[200]',
        'nomor_jurnal' => 'required|max_length[100]',
        'tanggal' => 'required|valid_date',
        'keterangan' => 'required',
        'debit' => 'decimal|greater_than_equal_to[0]',
        'kredit' => 'decimal|greater_than_equal_to[0]',
        'tipe_jurnal' => 'permit_empty|in_list[umum,penyesuaian,mutasi_bank]',
        'status' => 'permit_empty|in_list[pending,processed,failed,void,reversed]'
    ];

    protected $validationMessages = [
        'jurnal_detail_id' => [
            'is_unique' => 'Jurnal detail ini sudah diproses ke buku besar'
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
    protected $beforeInsert = ['setPeriodeFields', 'setDefaultStatus', 'setProcessedTimestamp'];
    protected $beforeUpdate = ['validateVoidOperation'];

    /**
     * Set periode fields (tahun, bulan, periode) dari tanggal
     */
    protected function setPeriodeFields(array $data)
    {
        if (isset($data['data']['tanggal'])) {
            $tanggal = $data['data']['tanggal'];
            $data['data']['tahun'] = date('Y', strtotime($tanggal));
            $data['data']['bulan'] = date('m', strtotime($tanggal));
            $data['data']['periode'] = date('Y-m', strtotime($tanggal));
        }
        return $data;
    }

    /**
     * Set default status jika tidak diisi
     */
    protected function setDefaultStatus(array $data)
    {
        if (!isset($data['data']['status']) || empty($data['data']['status'])) {
            $data['data']['status'] = 'pending';
        }
        return $data;
    }

    /**
     * Set processed_at dan processed_by saat status berubah menjadi processed
     */
    protected function setProcessedTimestamp(array $data)
    {
        if (isset($data['data']['status']) && $data['data']['status'] === 'processed') {
            if (!isset($data['data']['processed_at'])) {
                $data['data']['processed_at'] = date('Y-m-d H:i:s');
            }
            if (!isset($data['data']['processed_by']) && session()->get('user_id')) {
                $data['data']['processed_by'] = session()->get('user_id');
            }
        }
        return $data;
    }

    /**
     * Validasi operasi void
     */
    protected function validateVoidOperation(array $data)
    {
        // Jika sedang mengubah is_void menjadi 1
        if (isset($data['data']['is_void']) && $data['data']['is_void'] == 1) {
            // Pastikan ada alasan pembatalan
            if (empty($data['data']['void_reason'])) {
                throw new \RuntimeException('Alasan pembatalan harus diisi saat melakukan void');
            }
            
            // Set voided_at dan voided_by jika belum
            if (!isset($data['data']['voided_at'])) {
                $data['data']['voided_at'] = date('Y-m-d H:i:s');
            }
            if (!isset($data['data']['voided_by']) && session()->get('user_id')) {
                $data['data']['voided_by'] = session()->get('user_id');
            }
        }
        return $data;
    }

    // ================================================================
    // PROSES BATCH (Posting ke Buku Besar)
    // ================================================================

        public function getPendingJurnals($periodeMulai = null, $periodeSelesai = null)
    {
        $db = \Config\Database::connect();
        
        // Ambil semua jurnal_detail_id yang sudah ada di buku_besar
        $processedSubquery = $db->table('buku_besar')
            ->select('jurnal_detail_id')
            ->distinct()
            ->where('is_void', 0)
            ->where('status', 'processed')
            ->get();
        
        $processedIds = [];
        foreach ($processedSubquery->getResultArray() as $row) {
            if (!empty($row['jurnal_detail_id'])) {
                $processedIds[] = $row['jurnal_detail_id'];
            }
        }
        
        // Query utama - hanya jurnal dengan status 'posted'
        $builder = $db->table('jurnal_detail')
            ->select('
                jurnal_detail.*,
                jurnal.nomor_jurnal,
                jurnal.tanggal,
                jurnal.keterangan as jurnal_keterangan,
                jurnal.tipe_jurnal,
                jurnal.referensi,
                jurnal.tipe_referensi,
                jurnal.status as jurnal_status,
                coa.kode_akun,
                coa.nama_akun,
                coa.saldo_normal,
                coa.tipe_akun
            ')
            ->join('jurnal', 'jurnal.id = jurnal_detail.jurnal_id')
            ->join('coa', 'coa.id = jurnal_detail.coa_id')
            ->where('jurnal.status', 'posted')
            ->where('(jurnal_detail.debit > 0 OR jurnal_detail.kredit > 0)');
        
        // Filter periode
        if ($periodeMulai && $periodeSelesai) {
            $startDate = $periodeMulai . '-01';
            $endDate = date('Y-m-t', strtotime($periodeSelesai . '-01'));
            $builder->where('jurnal.tanggal >=', $startDate);
            $builder->where('jurnal.tanggal <=', $endDate);
        } elseif ($periodeMulai) {
            $startDate = $periodeMulai . '-01';
            $endDate = date('Y-m-t', strtotime($periodeMulai . '-01'));
            $builder->where('jurnal.tanggal >=', $startDate);
            $builder->where('jurnal.tanggal <=', $endDate);
        }
        
        // Exclude yang sudah diproses
        if (!empty($processedIds)) {
            $builder->whereNotIn('jurnal_detail.id', $processedIds);
        }
        
        $builder->orderBy('jurnal.tanggal', 'ASC')
                ->orderBy('jurnal.nomor_jurnal', 'ASC')
                ->orderBy('jurnal_detail.id', 'ASC');
        
        $result = $builder->get()->getResultArray();
        
        return $result;
    }
    /**
     * Get count of pending jurnals
     */
 public function getPendingCount($periodeMulai = null, $periodeSelesai = null)
    {
        $pendingJurnals = $this->getPendingJurnals($periodeMulai, $periodeSelesai);
        
        $jurnalIds = [];
        foreach ($pendingJurnals as $detail) {
            $jurnalIds[$detail['jurnal_id']] = true;
        }
        
        return [
            'jurnal_count' => count($jurnalIds),
            'detail_count' => count($pendingJurnals)
        ];
    }

  /**
     
     * Cek apakah periode sudah pernah diposting (batch sukses)
     * 
     * @param string $periode Periode dalam format YYYY-MM
     * @return bool True jika sudah pernah diposting
     */
    public function isPeriodeAlreadyPosted($periode)
    {
        $startDate = $periode . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));
        
        // Cek apakah ada jurnal di periode ini yang sudah masuk buku besar
        $result = $this->db->table('buku_besar')
            ->select('COUNT(DISTINCT jurnal_id) as total')
            ->where('tanggal >=', $startDate)
            ->where('tanggal <=', $endDate)
            ->where('status', 'processed')
            ->where('is_void', 0)
            ->get()
            ->getRowArray();
        
        $count = (int)($result['total'] ?? 0);
        
        //  PERBAIKAN: Ambil jurnal dengan status 'posted' ATAU 'draft'
        $jurnalIds = $this->db->table('jurnal')
            ->select('id')
            ->where('tanggal >=', $startDate)
            ->where('tanggal <=', $endDate)
            ->whereIn('status', ['posted', 'draft'])  //  PERBAIKAN
            ->get()
            ->getResultArray();
        
        $totalJurnalInPeriode = count($jurnalIds);
        
        if ($totalJurnalInPeriode === 0) {
            return false;
        }
        
        return ($count > 0 && $count >= $totalJurnalInPeriode);
    }

        /**
     *  TAMBAHKAN METHOD INI
     * Get jurnal yang sudah diposting di suatu periode
     * 
     * @param string $periode Periode dalam format YYYY-MM
     * @return array
     */
    public function getPostedJurnalIdsInPeriode($periode)
    {
        $startDate = $periode . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));
        
        $result = $this->db->table('buku_besar')
            ->select('jurnal_id, nomor_jurnal')
            ->distinct()  //  PERBAIKAN: Gunakan distinct() method
            ->where('tanggal >=', $startDate)
            ->where('tanggal <=', $endDate)
            ->where('status', 'processed')
            ->where('is_void', 0)
            ->get()
            ->getResultArray();
        
        return $result;
    }

    /**
     *  TAMBAHKAN METHOD INI
     * Get pending counts by specific month (untuk AJAX)
     * 
     * @param string $bulan Bulan (01-12)
     * @param string $tahun Tahun (YYYY)
     * @return array ['jurnal_count' => int, 'detail_count' => int]
     */
    public function getPendingCountsByMonth($bulan, $tahun)
    {
        $periode = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $pendingJurnals = $this->getPendingJurnals($periode, $periode);
        
        $jurnalIds = [];
        foreach ($pendingJurnals as $detail) {
            $jurnalIds[$detail['jurnal_id']] = true;
        }
        
        return [
            'jurnal_count' => count($jurnalIds),
            'detail_count' => count($pendingJurnals)
        ];
    }

    
    /**
     * Proses satu jurnal detail ke buku besar
     * 
     * @param array $jurnalDetail Data jurnal detail dari getPendingJurnals()
     * @param string $batchId ID batch untuk grouping
     * @return array ['success' => bool, 'message' => string, 'data' => array]
     */
    public function processSingleJurnalDetail($jurnalDetail, $batchId = null)
    {
        try {
            // Cek apakah sudah pernah diproses
            $existing = $this->where('jurnal_detail_id', $jurnalDetail['id'])->first();
            if ($existing) {
                return [
                    'success' => false,
                    'message' => 'Jurnal detail ini sudah diproses ke buku besar',
                    'data' => null
                ];
            }
            
            // Siapkan data untuk buku besar
            $debit = (float)($jurnalDetail['debit'] ?? 0);
            $kredit = (float)($jurnalDetail['kredit'] ?? 0);
            
            $data = [
                'jurnal_id' => $jurnalDetail['jurnal_id'],
                'jurnal_detail_id' => $jurnalDetail['id'],
                'coa_id' => $jurnalDetail['coa_id'],
                'kode_akun' => $jurnalDetail['kode_akun'],
                'nama_akun' => $jurnalDetail['nama_akun'],
                'nomor_jurnal' => $jurnalDetail['nomor_jurnal'],
                'tanggal' => $jurnalDetail['tanggal'],
                'keterangan' => $jurnalDetail['jurnal_keterangan'] . ' - ' . ($jurnalDetail['keterangan'] ?? ''),
                'tipe_jurnal' => $jurnalDetail['tipe_jurnal'] ?? 'umum',
                'debit' => $debit,
                'kredit' => $kredit,
                'batch_id' => $batchId,
                'status' => 'pending'
            ];
            
            // Simpan ke buku besar
            if (!$this->save($data)) {
                return [
                    'success' => false,
                    'message' => 'Gagal menyimpan ke buku besar: ' . json_encode($this->errors()),
                    'data' => null
                ];
            }
            
            $insertId = $this->getInsertID();
            
            return [
                'success' => true,
                'message' => 'Berhasil diproses',
                'data' => ['id' => $insertId, 'jurnal_detail_id' => $jurnalDetail['id']]
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Proses batch semua jurnal pending ke buku besar
     * 
     * @param string $periodeMulai Periode mulai (format: YYYY-MM)
     * @param string $periodeSelesai Periode selesai (format: YYYY-MM)
     * @return array [
     *   'success' => bool,
     *   'batch_id' => string,
     *   'total' => int,
     *   'success_count' => int,
     *   'failed_count' => int,
     *   'failed_items' => array,
     *   'message' => string
     * ]
     */
public function processBatch($periodeMulai = null, $periodeSelesai = null)
{
    // Generate batch ID
    $batchId = 'BATCH-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    
    //  PASTIKAN parameter periode dikirim dengan benar
    log_message('debug', 'processBatch called with periodeMulai: ' . $periodeMulai . ', periodeSelesai: ' . $periodeSelesai);
    
    // Get pending jurnals dengan filter periode
    $pendingJurnals = $this->getPendingJurnals($periodeMulai, $periodeSelesai);
    
    if (empty($pendingJurnals)) {
        return [
            'success' => false,
            'batch_id' => $batchId,
            'total' => 0,
            'success_count' => 0,
            'failed_count' => 0,
            'failed_items' => [],
            'message' => 'Tidak ada jurnal pending untuk diproses pada periode ' . ($periodeMulai ?: 'yang dipilih')
        ];
    }
        
        $total = count($pendingJurnals);
        $successCount = 0;
        $failedCount = 0;
        $failedItems = [];
        
        // Mulai transaksi database
        $this->db->transStart();
        
        try {
            foreach ($pendingJurnals as $jurnalDetail) {
                $result = $this->processSingleJurnalDetail($jurnalDetail, $batchId);
                
                if ($result['success']) {
                    $successCount++;
                } else {
                    $failedCount++;
                    $failedItems[] = [
                        'jurnal_detail_id' => $jurnalDetail['id'],
                        'nomor_jurnal' => $jurnalDetail['nomor_jurnal'],
                        'kode_akun' => $jurnalDetail['kode_akun'],
                        'error' => $result['message']
                    ];
                }
            }
            
            // Jika ada yang gagal, rollback semua
            if ($failedCount > 0) {
                $this->db->transRollback();
                
                // Update status failed items di database (opsional)
                foreach ($failedItems as $item) {
                    $this->where('batch_id', $batchId)
                         ->where('jurnal_detail_id', $item['jurnal_detail_id'])
                         ->set(['status' => 'failed', 'error_message' => $item['error']])
                         ->update();
                }
                
                return [
                    'success' => false,
                    'batch_id' => $batchId,
                    'total' => $total,
                    'success_count' => $successCount,
                    'failed_count' => $failedCount,
                    'failed_items' => $failedItems,
                    'message' => "Proses batch gagal: {$failedCount} dari {$total} jurnal mengalami error"
                ];
            }
            
            // Update status semua entri menjadi processed
            $this->where('batch_id', $batchId)
                 ->set(['status' => 'processed', 'processed_at' => date('Y-m-d H:i:s')])
                 ->update();
            
            $this->db->transComplete();
            
            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Transaksi database gagal');
            }
            
            // Hitung ulang saldo setelah batch berhasil
            $this->recalculateAllSaldo();
            
            return [
                'success' => true,
                'batch_id' => $batchId,
                'total' => $total,
                'success_count' => $successCount,
                'failed_count' => $failedCount,
                'failed_items' => [],
                'message' => "Berhasil memproses {$successCount} jurnal ke buku besar"
            ];
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return [
                'success' => false,
                'batch_id' => $batchId,
                'total' => $total,
                'success_count' => $successCount,
                'failed_count' => $total,
                'failed_items' => [['error' => $e->getMessage()]],
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Rollback batch processing
     * 
     * @param string $batchId ID batch yang akan di-rollback
     * @return array
     */
    public function rollbackBatch($batchId)
    {
        try {
            $affected = $this->where('batch_id', $batchId)
                ->where('status', 'processed')
                ->delete();
            
            if ($affected > 0) {
                // Recalculate saldo setelah rollback
                $this->recalculateAllSaldo();
                
                return [
                    'success' => true,
                    'affected' => $affected,
                    'message' => "Berhasil menghapus {$affected} entri buku besar"
                ];
            }
            
            return [
                'success' => false,
                'affected' => 0,
                'message' => 'Tidak ada entri dengan batch ID tersebut'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'affected' => 0,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    // ================================================================
    // SALDO & PERHITUNGAN
    // ================================================================

    /**
     * Recalculate saldo_akhir untuk semua entri buku besar per akun
     */
    public function recalculateAllSaldo()
    {
        // Get all active COA
        $coaModel = new CoaModel();
        $allCoa = $coaModel->where('is_active', 1)->findAll();
        
        $totalUpdated = 0;
        
        foreach ($allCoa as $coa) {
            $updated = $this->recalculateSaldoByCoa($coa['id']);
            $totalUpdated += $updated;
        }
        
        return $totalUpdated;
    }

    /**
     * Recalculate saldo_akhir untuk satu akun tertentu
     * 
     * @param int $coaId ID akun
     * @return int Jumlah entri yang diupdate
     */
    public function recalculateSaldoByCoa($coaId)
    {
        // Get COA info untuk tahu saldo_normal
        $coaModel = new CoaModel();
        $coa = $coaModel->find($coaId);
        
        if (!$coa) {
            return 0;
        }
        
        // Get semua entri untuk akun ini, urut berdasarkan tanggal dan ID
        $entries = $this->where('coa_id', $coaId)
            ->where('is_void', 0)
            ->orderBy('tanggal', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
        
        if (empty($entries)) {
            return 0;
        }
        
        $saldoBerjalan = 0;
        $updatedCount = 0;
        
        foreach ($entries as $entry) {
            // Hitung saldo berdasarkan normal akun
            if ($coa['saldo_normal'] == 'Debit') {
                // Akun debit: +debit, -kredit
                $saldoBerjalan += ($entry['debit'] - $entry['kredit']);
            } else {
                // Akun kredit: +kredit, -debit
                $saldoBerjalan += ($entry['kredit'] - $entry['debit']);
            }
            
            // Update saldo_akhir
            $this->update($entry['id'], ['saldo_akhir' => $saldoBerjalan]);
            $updatedCount++;
        }
        
        return $updatedCount;
    }

    /**
     * Get saldo akun pada tanggal tertentu
     * 
     * @param int $coaId ID akun
     * @param string $tanggal Tanggal (format: YYYY-MM-DD)
     * @return float
     */
    public function getSaldoByDate($coaId, $tanggal)
    {
        $coaModel = new CoaModel();
        $coa = $coaModel->find($coaId);
        
        if (!$coa) {
            return 0;
        }
        
        $lastEntry = $this->where('coa_id', $coaId)
            ->where('tanggal <=', $tanggal)
            ->where('is_void', 0)
            ->orderBy('tanggal', 'DESC')
            ->orderBy('id', 'DESC')
            ->first();
        
        if ($lastEntry) {
            return (float)$lastEntry['saldo_akhir'];
        }
        
        return 0;
    }

    /**
     * Get buku besar untuk satu akun
     * 
     * @param int $coaId ID akun
     * @param string $startDate Tanggal mulai
     * @param string $endDate Tanggal selesai
     * @return array
     */
    public function getBukuBesarByCoa($coaId, $startDate = null, $endDate = null)
    {
        $builder = $this->where('coa_id', $coaId)
            ->where('is_void', 0)
            ->orderBy('tanggal', 'ASC')
            ->orderBy('id', 'ASC');
        
        if ($startDate) {
            $builder->where('tanggal >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('tanggal <=', $endDate);
        }
        
        $entries = $builder->findAll();
        
        // Get COA info
        $coaModel = new CoaModel();
        $coa = $coaModel->find($coaId);
        
        // Hitung saldo awal
        $saldoAwal = 0;
        if ($startDate) {
            $saldoAwal = $this->getSaldoByDate($coaId, date('Y-m-d', strtotime($startDate . ' -1 day')));
        }
        
        return [
            'coa' => $coa,
            'saldo_awal' => $saldoAwal,
            'entries' => $entries,
            'total_debit' => array_sum(array_column($entries, 'debit')),
            'total_kredit' => array_sum(array_column($entries, 'kredit')),
            'saldo_akhir' => !empty($entries) ? $entries[count($entries) - 1]['saldo_akhir'] : $saldoAwal
        ];
    }

    // ================================================================
    // LAPORAN
    // ================================================================

     /**
     * Get statistics for dashboard
     * 
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getStatistics($startDate = null, $endDate = null)
    {
        $builder = $this->select('
            COUNT(*) as total_transaksi,
            SUM(debit) as total_debit,
            SUM(kredit) as total_kredit,
            COUNT(DISTINCT jurnal_id) as total_transaksi_unik
        ');
        
        if ($startDate) {
            $builder->where('tanggal >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('tanggal <=', $endDate);
        }
        
        $result = $builder->first();
        
        return [
            'total_transaksi' => (int)($result['total_transaksi'] ?? 0),
            'total_transaksi_unik' => (int)($result['total_transaksi_unik'] ?? 0),
            'total_debit' => (float)($result['total_debit'] ?? 0),
            'total_kredit' => (float)($result['total_kredit'] ?? 0)
        ];
    }

    /**
     * Get neraca saldo (Trial Balance)
     * 
     * @param string $periode Periode (format: YYYY-MM)
     * @return array
     */
    public function getNeracaSaldo($periode)
    {
        $coaModel = new CoaModel();
        
        // Get all active COA
        $allCoa = $coaModel->where('is_active', 1)
            ->orderBy('kode_akun', 'ASC')
            ->findAll();
        
        $result = [];
        $totalDebit = 0;
        $totalKredit = 0;
        
        // Get last day of period
        $lastDay = date('Y-m-t', strtotime($periode . '-01'));
        
        foreach ($allCoa as $coa) {
            $saldo = $this->getSaldoByDate($coa['id'], $lastDay);
            
            $item = [
                'kode_akun' => $coa['kode_akun'],
                'nama_akun' => $coa['nama_akun'],
                'tipe_akun' => $coa['tipe_akun'],
                'saldo_normal' => $coa['saldo_normal'],
                'saldo' => $saldo
            ];
            
            if ($coa['saldo_normal'] == 'Debit') {
                $item['debit'] = $saldo;
                $item['kredit'] = 0;
                $totalDebit += $saldo;
            } else {
                $item['debit'] = 0;
                $item['kredit'] = $saldo;
                $totalKredit += $saldo;
            }
            
            $result[] = $item;
        }
        
        return [
            'data' => $result,
            'total_debit' => $totalDebit,
            'total_kredit' => $totalKredit,
            'is_balance' => abs($totalDebit - $totalKredit) <= 0.01
        ];
    }

    /**
     * Get summary per periode
     */
    public function getSummaryByPeriode($tahun = null)
    {
        if (!$tahun) {
            $tahun = date('Y');
        }
        
        return $this->select('periode, COUNT(*) as total_transaksi, SUM(debit) as total_debit, SUM(kredit) as total_kredit')
            ->where('YEAR(tanggal)', $tahun)
            ->where('is_void', 0)
            ->groupBy('periode')
            ->orderBy('periode', 'ASC')
            ->findAll();
    }

    /**
     * Get batch history
     */
    public function getBatchHistory($limit = 10)
    {
        return $this->select('batch_id, COUNT(*) as total, status, MIN(created_at) as created_at')
            ->where('batch_id IS NOT NULL')
            ->groupBy('batch_id')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    // ================================================================
    // VOID & KOREKSI
    // ================================================================

    /**
     * Void entri buku besar (ketika jurnal sumber di-void)
     * 
     * @param int $jurnalId ID jurnal yang di-void
     * @param string $reason Alasan void
     * @return array
     */
    public function voidByJurnalId($jurnalId, $reason = 'Jurnal sumber dibatalkan')
    {
        $entries = $this->where('jurnal_id', $jurnalId)
            ->where('is_void', 0)
            ->findAll();
        
        if (empty($entries)) {
            return [
                'success' => false,
                'message' => 'Tidak ada entri buku besar untuk jurnal ini'
            ];
        }
        
        $updated = 0;
        foreach ($entries as $entry) {
            $result = $this->update($entry['id'], [
                'is_void' => 1,
                'void_reason' => $reason,
                'voided_at' => date('Y-m-d H:i:s'),
                'voided_by' => session()->get('user_id')
            ]);
            
            if ($result) {
                $updated++;
            }
        }
        
        // Recalculate saldo setelah void
        if ($updated > 0) {
            $this->recalculateAllSaldo();
        }
        
        return [
            'success' => true,
            'updated' => $updated,
            'message' => "Berhasil me-void {$updated} entri buku besar"
        ];
    }

    // ================================================================
// JURNAL POSTED (Jurnal yang sudah masuk buku besar)
// ================================================================

/**
 * Get daftar jurnal yang sudah diposting ke buku besar
 * 
 * @param array $filters Filter pencarian
 * @param int $perPage Jumlah per halaman
 * @param int $page Halaman saat ini
 * @return array
 */
public function getJurnalPosted($filters = [], $perPage = 20, $page = 1)
{
    $db = \Config\Database::connect();
    
    // Query untuk mendapatkan jurnal unik dari buku besar
    $builder = $db->table('buku_besar bb')
        ->select('
            bb.jurnal_id,
            bb.nomor_jurnal,
            bb.tanggal,
            bb.keterangan,
            bb.tipe_jurnal,
            bb.batch_id,
            bb.status,
            bb.processed_at,
            SUM(bb.debit) as total_debit,
            SUM(bb.kredit) as total_kredit
        ')
        ->groupBy('bb.jurnal_id, bb.nomor_jurnal, bb.tanggal, bb.keterangan, bb.tipe_jurnal, bb.batch_id, bb.status, bb.processed_at')
        ->orderBy('bb.tanggal', 'DESC')
        ->orderBy('bb.nomor_jurnal', 'DESC');
    
    // Apply filters
    if (!empty($filters['tanggal_mulai'])) {
        $builder->where('bb.tanggal >=', $filters['tanggal_mulai']);
    }
    
    if (!empty($filters['tanggal_selesai'])) {
        $builder->where('bb.tanggal <=', $filters['tanggal_selesai']);
    }
    
    if (!empty($filters['tipe_jurnal'])) {
        $builder->where('bb.tipe_jurnal', $filters['tipe_jurnal']);
    }
    
    if (!empty($filters['status'])) {
        $builder->where('bb.status', $filters['status']);
    }
    
    if (!empty($filters['batch_id'])) {
        $builder->where('bb.batch_id', $filters['batch_id']);
    }
    
    if (!empty($filters['search'])) {
        $builder->groupStart()
            ->like('bb.nomor_jurnal', $filters['search'])
            ->orLike('bb.keterangan', $filters['search'])
            ->groupEnd();
    }
    
    // Hitung total
    $total = $builder->countAllResults(false);
    
    // Ambil data dengan pagination
    $offset = ($page - 1) * $perPage;
    $builder->limit($perPage, $offset);
    $data = $builder->get()->getResultArray();
    
    return [
        'data' => $data,
        'total' => $total,
        'per_page' => $perPage,
        'current_page' => $page,
        'total_pages' => ceil($total / $perPage)
    ];
}

/**
 * Get detail jurnal yang sudah diposting
 * 
 * @param int $jurnalId ID jurnal
 * @return array|null
 */
public function getJurnalPostedDetail($jurnalId)
{
    $db = \Config\Database::connect();
    
    // Get header jurnal
    $header = $db->table('buku_besar bb')
        ->select('
            bb.jurnal_id,
            bb.nomor_jurnal,
            bb.tanggal,
            bb.keterangan,
            bb.tipe_jurnal,
            bb.batch_id,
            bb.status,
            bb.processed_at,
            bb.processed_by,
            SUM(bb.debit) as total_debit,
            SUM(bb.kredit) as total_kredit
        ')
        ->where('bb.jurnal_id', $jurnalId)
        ->groupBy('bb.jurnal_id, bb.nomor_jurnal, bb.tanggal, bb.keterangan, bb.tipe_jurnal, bb.batch_id, bb.status, bb.processed_at, bb.processed_by')
        ->get()
        ->getRowArray();
    
    if (!$header) {
        return null;
    }
    
    // Get detail entries (debit dan kredit per akun)
    $details = $db->table('buku_besar bb')
        ->select('
            bb.id,
            bb.coa_id,
            bb.kode_akun,
            bb.nama_akun,
            bb.debit,
            bb.kredit,
            bb.keterangan as entry_keterangan,
            bb.status as entry_status,
            bb.is_void
        ')
        ->where('bb.jurnal_id', $jurnalId)
        ->orderBy('bb.id', 'ASC')
        ->get()
        ->getResultArray();
    
    // Pisahkan debit dan kredit
    $debitEntries = [];
    $creditEntries = [];
    
    foreach ($details as $detail) {
        if ($detail['debit'] > 0) {
            $debitEntries[] = $detail;
        } else {
            $creditEntries[] = $detail;
        }
    }
    
    // Get user info untuk processed_by
    $processedByName = '-';
    if (!empty($header['processed_by'])) {
        $user = $db->table('users')->select('name')->where('id', $header['processed_by'])->get()->getRowArray();
        $processedByName = $user['name'] ?? '-';
    }
    
    return [
        'header' => $header,
        'debit_entries' => $debitEntries,
        'credit_entries' => $creditEntries,
        'total_debit' => $header['total_debit'],
        'total_kredit' => $header['total_kredit'],
        'is_balance' => abs($header['total_debit'] - $header['total_kredit']) <= 0.01,
        'processed_by_name' => $processedByName
    ];
}

/**
 * Get detail batch (semua jurnal dalam satu batch)
 * 
 * @param string $batchId ID batch
 * @return array
 */
public function getBatchDetail($batchId)
{
    $db = \Config\Database::connect();
    
    return $db->table('buku_besar')
        ->select('
            id,
            jurnal_id,
            jurnal_detail_id,
            coa_id,
            kode_akun,
            nama_akun,
            nomor_jurnal,
            tanggal,
            keterangan,
            tipe_jurnal,
            debit,
            kredit,
            saldo_akhir,
            status,
            error_message,
            is_void,
            processed_at
        ')
        ->where('batch_id', $batchId)
        ->orderBy('tanggal', 'ASC')
        ->orderBy('nomor_jurnal', 'ASC')
        ->orderBy('id', 'ASC')
        ->get()
        ->getResultArray();
}

/**
 * Get data jurnal posted untuk export
 * 
 * @param array $filters Filter pencarian
 * @return array
 */
public function getJurnalPostedExport($filters = [])
{
    $db = \Config\Database::connect();
    
    $builder = $db->table('buku_besar bb')
        ->select('
            bb.jurnal_id,
            bb.nomor_jurnal,
            bb.tanggal,
            bb.keterangan,
            bb.tipe_jurnal,
            bb.batch_id,
            bb.status,
            bb.processed_at,
            SUM(bb.debit) as total_debit,
            SUM(bb.kredit) as total_kredit
        ')
        ->groupBy('bb.jurnal_id, bb.nomor_jurnal, bb.tanggal, bb.keterangan, bb.tipe_jurnal, bb.batch_id, bb.status, bb.processed_at')
        ->orderBy('bb.tanggal', 'DESC')
        ->orderBy('bb.nomor_jurnal', 'DESC');
    
    // Apply filters
    if (!empty($filters['tanggal_mulai'])) {
        $builder->where('bb.tanggal >=', $filters['tanggal_mulai']);
    }
    
    if (!empty($filters['tanggal_selesai'])) {
        $builder->where('bb.tanggal <=', $filters['tanggal_selesai']);
    }
    
    if (!empty($filters['tipe_jurnal'])) {
        $builder->where('bb.tipe_jurnal', $filters['tipe_jurnal']);
    }
    
    if (!empty($filters['status'])) {
        $builder->where('bb.status', $filters['status']);
    }
    
    if (!empty($filters['batch_id'])) {
        $builder->where('bb.batch_id', $filters['batch_id']);
    }
    
    if (!empty($filters['search'])) {
        $builder->groupStart()
            ->like('bb.nomor_jurnal', $filters['search'])
            ->orLike('bb.keterangan', $filters['search'])
            ->groupEnd();
    }
    
    return $builder->get()->getResultArray();
}

/**
 * Get ringkasan statistik jurnal posted
 * 
 * @param array $filters Filter pencarian
 * @return array
 */
public function getJurnalPostedStats($filters = [])
{
    $db = \Config\Database::connect();
    
    $builder = $db->table('buku_besar bb')
        ->select('
            COUNT(DISTINCT bb.jurnal_id) as total_jurnal,
            SUM(bb.debit) as total_debit,
            SUM(bb.kredit) as total_kredit,
            COUNT(DISTINCT CASE WHEN bb.status = "processed" THEN bb.jurnal_id END) as total_sukses,
            COUNT(DISTINCT CASE WHEN bb.status = "failed" THEN bb.jurnal_id END) as total_gagal,
            COUNT(DISTINCT CASE WHEN bb.status = "void" THEN bb.jurnal_id END) as total_void
        ');
    
    // Apply filters
    if (!empty($filters['tanggal_mulai'])) {
        $builder->where('bb.tanggal >=', $filters['tanggal_mulai']);
    }
    
    if (!empty($filters['tanggal_selesai'])) {
        $builder->where('bb.tanggal <=', $filters['tanggal_selesai']);
    }
    
    if (!empty($filters['tipe_jurnal'])) {
        $builder->where('bb.tipe_jurnal', $filters['tipe_jurnal']);
    }
    
    if (!empty($filters['batch_id'])) {
        $builder->where('bb.batch_id', $filters['batch_id']);
    }
    
    $result = $builder->get()->getRowArray();
    
    return [
        'total_jurnal' => (int)($result['total_jurnal'] ?? 0),
        'total_debit' => (float)($result['total_debit'] ?? 0),
        'total_kredit' => (float)($result['total_kredit'] ?? 0),
        'total_sukses' => (int)($result['total_sukses'] ?? 0),
        'total_gagal' => (int)($result['total_gagal'] ?? 0),
        'total_void' => (int)($result['total_void'] ?? 0),
        'is_balance' => abs(($result['total_debit'] ?? 0) - ($result['total_kredit'] ?? 0)) <= 0.01
    ];
}

/**
 * AJAX: Get pending counts by month (untuk modal)
 */
public function ajaxGetPendingCountsByMonth($bulan = null, $tahun = null)
{
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON(['success' => false]);
    }
    
    $bulan = $this->request->getGet('bulan');
    $tahun = $this->request->getGet('tahun');
    
    $periodeMulai = null;
    $periodeSelesai = null;
    
    if ($bulan && $tahun) {
        $periodeMulai = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $periodeSelesai = $periodeMulai;
    }
    
    $pendingJurnals = $this->getPendingJurnals($periodeMulai, $periodeSelesai);
    
    // Hitung jumlah jurnal unik
    $jurnalIds = [];
    foreach ($pendingJurnals as $detail) {
        $jurnalIds[$detail['jurnal_id']] = true;
    }
    
    return $this->response->setJSON([
        'success' => true,
        'jurnal_count' => count($jurnalIds),
        'detail_count' => count($pendingJurnals)
    ]);
}


}
?>