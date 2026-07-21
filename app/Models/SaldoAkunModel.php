<?php

namespace App\Models;

use CodeIgniter\Model;

class SaldoAkunModel extends Model
{
    protected $table = 'saldo_akun';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'coa_id',
        'kode_akun',
        'nama_akun',
        'periode',
        'saldo_awal',
        'total_debit',
        'total_kredit',
        'saldo_akhir',
        'tipe_saldo'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'coa_id' => 'required|integer',
        'kode_akun' => 'required|max_length[20]',
        'nama_akun' => 'required|max_length[200]',
        'periode' => 'required|valid_date',
        'saldo_awal' => 'decimal',
        'total_debit' => 'decimal|greater_than_equal_to[0]',
        'total_kredit' => 'decimal|greater_than_equal_to[0]',
        'saldo_akhir' => 'decimal'
    ];

    protected $validationMessages = [
        'coa_id' => [
            'required' => 'Akun harus dipilih',
            'integer' => 'ID akun harus berupa angka'
        ],
        'periode' => [
            'required' => 'Periode harus diisi',
            'valid_date' => 'Format periode tidak valid'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = ['calculateSaldoAkhir'];
    protected $beforeUpdate = ['calculateSaldoAkhir'];

    /**
     * Menghitung saldo akhir dan tipe saldo
     */
    protected function calculateSaldoAkhir(array $data)
    {
        $saldoAwal = (float)($data['data']['saldo_awal'] ?? 0);
        $totalDebit = (float)($data['data']['total_debit'] ?? 0);
        $totalKredit = (float)($data['data']['total_kredit'] ?? 0);
        
        // Tentukan saldo normal akun
        $coaId = $data['data']['coa_id'] ?? null;
        if ($coaId) {
            $coaModel = new CoaModel();
            $akun = $coaModel->find($coaId);
            $saldoNormal = $akun['saldo_normal'] ?? 'Debit';
            
            if ($saldoNormal == 'Debit') {
                $saldoAkhir = $saldoAwal + $totalDebit - $totalKredit;
            } else {
                $saldoAkhir = $saldoAwal + $totalKredit - $totalDebit;
            }
        } else {
            $saldoAkhir = $saldoAwal + $totalDebit - $totalKredit;
        }
        
        $data['data']['saldo_akhir'] = $saldoAkhir;
        $data['data']['tipe_saldo'] = $saldoAkhir >= 0 ? 'Debit' : 'Kredit';
        
        return $data;
    }

    /**
     * Generate saldo bulanan dari buku besar
     */
    public function generateMonthlySaldo($tahun, $bulan)
    {
        $periode = date("$tahun-$bulan-01");
        $bulanDepan = date("Y-m-01", strtotime("+1 month", strtotime($periode)));
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Ambil semua akun aktif
            $coaModel = new CoaModel();
            $akuns = $coaModel->where('is_active', 1)
                ->where('is_header', 0)
                ->findAll();
            
            $bukuBesarModel = new BukuBesarModel();
            
            foreach ($akuns as $akun) {
                // Hitung saldo awal (saldo akhir bulan sebelumnya)
                $saldoAwal = 0;
                $previousMonth = date("Y-m-01", strtotime("-1 month", strtotime($periode)));
                
                $previousSaldo = $this->where('coa_id', $akun['id'])
                    ->where('periode', $previousMonth)
                    ->first();
                
                if ($previousSaldo) {
                    $saldoAwal = $previousSaldo['saldo_akhir'];
                } else {
                    // Jika tidak ada saldo bulan sebelumnya, hitung dari buku besar
                    $saldoAwal = $bukuBesarModel->getSaldoAwal($akun['id'], $periode);
                }
                
                // Hitung mutasi bulan ini
                $mutasi = $bukuBesarModel->select('SUM(debit) as total_debit, SUM(kredit) as total_kredit')
                    ->where('coa_id', $akun['id'])
                    ->where('tanggal >=', $periode)
                    ->where('tanggal <', $bulanDepan)
                    ->first();
                
                $totalDebit = (float)($mutasi['total_debit'] ?? 0);
                $totalKredit = (float)($mutasi['total_kredit'] ?? 0);
                
                // Cek apakah sudah ada saldo untuk periode ini
                $existing = $this->where('coa_id', $akun['id'])
                    ->where('periode', $periode)
                    ->first();
                
                $data = [
                    'coa_id' => $akun['id'],
                    'kode_akun' => $akun['kode_akun'],
                    'nama_akun' => $akun['nama_akun'],
                    'periode' => $periode,
                    'saldo_awal' => $saldoAwal,
                    'total_debit' => $totalDebit,
                    'total_kredit' => $totalKredit
                ];
                
                if ($existing) {
                    $data['id'] = $existing['id'];
                    $this->save($data);
                } else {
                    $this->insert($data);
                }
            }
            
            $db->transComplete();
            
            return $db->transStatus();
        } catch (\Exception $e) {
            $db->transRollback();
            throw new \RuntimeException('Gagal generate saldo bulanan: ' . $e->getMessage());
        }
    }

    /**
     * Get saldo untuk periode tertentu
     */
    public function getSaldoByPeriod($periode)
    {
        return $this->where('periode', $periode)
            ->orderBy('kode_akun', 'ASC')
            ->findAll();
    }

    /**
     * Get neraca saldo dari saldo akun
     */
    public function getNeracaSaldo($periode)
    {
        $saldoAkun = $this->where('periode', $periode)
            ->orderBy('kode_akun', 'ASC')
            ->findAll();
        
        $neracaSaldo = [];
        $totalDebit = 0;
        $totalKredit = 0;
        
        foreach ($saldoAkun as $saldo) {
            if ($saldo['saldo_akhir'] != 0) {
                $neracaSaldo[] = [
                    'kode_akun' => $saldo['kode_akun'],
                    'nama_akun' => $saldo['nama_akun'],
                    'saldo_awal' => $saldo['saldo_awal'],
                    'debit' => $saldo['total_debit'],
                    'kredit' => $saldo['total_kredit'],
                    'saldo_akhir' => $saldo['saldo_akhir'],
                    'tipe_saldo' => $saldo['tipe_saldo']
                ];
                
                if ($saldo['tipe_saldo'] == 'Debit') {
                    $totalDebit += abs($saldo['saldo_akhir']);
                } else {
                    $totalKredit += abs($saldo['saldo_akhir']);
                }
            }
        }
        
        return [
            'data' => $neracaSaldo,
            'total_debit' => $totalDebit,
            'total_kredit' => $totalKredit,
            'balance' => abs($totalDebit - $totalKredit) < 0.01
        ];
    }

    /**
     * Get saldo akhir untuk akun tertentu
     */
    public function getSaldoAkhirByCoa($coaId, $periode = null)
    {
        $builder = $this->where('coa_id', $coaId);
        
        if ($periode) {
            $builder->where('periode', $periode);
        } else {
            $builder->orderBy('periode', 'DESC');
        }
        
        $saldo = $builder->first();
        
        return $saldo ? (float)$saldo['saldo_akhir'] : 0;
    }

    /**
     * Get available periods
     */
    public function getAvailablePeriods()
    {
        return $this->select('periode')
            ->groupBy('periode')
            ->orderBy('periode', 'DESC')
            ->findAll();
    }

    /**
     * Check if period exists
     */
    public function periodExists($periode)
    {
        return $this->where('periode', $periode)
            ->countAllResults() > 0;
    }

    /**
     * Delete saldo for period
     */
    public function deletePeriod($periode)
    {
        return $this->where('periode', $periode)->delete();
    }

    /**
     * Get saldo trend (untuk chart)
     */
    public function getSaldoTrend($coaId, $months = 12)
    {
        $endDate = date('Y-m-01');
        $startDate = date('Y-m-01', strtotime("-$months months", strtotime($endDate)));
        
        return $this->select('periode, saldo_akhir')
            ->where('coa_id', $coaId)
            ->where('periode >=', $startDate)
            ->where('periode <=', $endDate)
            ->orderBy('periode', 'ASC')
            ->findAll();
    }

    /**
     * Get top accounts by balance
     */
    public function getTopAccounts($periode, $limit = 10, $order = 'desc')
    {
        $builder = $this->where('periode', $periode)
            ->orderBy('ABS(saldo_akhir)', $order)
            ->limit($limit);
        
        return $builder->findAll();
    }

    /**
     * Get statistics
     */
    public function getStats($periode = null)
    {
        $builder = $this;
        
        if ($periode) {
            $builder->where('periode', $periode);
        }
        
        $stats = $builder->select("
                COUNT(*) as total_akun,
                SUM(CASE WHEN tipe_saldo = 'Debit' THEN ABS(saldo_akhir) ELSE 0 END) as total_debit,
                SUM(CASE WHEN tipe_saldo = 'Kredit' THEN ABS(saldo_akhir) ELSE 0 END) as total_kredit,
                AVG(ABS(saldo_akhir)) as rata_rata_saldo,
                MAX(ABS(saldo_akhir)) as saldo_terbesar,
                MIN(ABS(saldo_akhir)) as saldo_terkecil
            ")
            ->first();
        
        return $stats ?: [
            'total_akun' => 0,
            'total_debit' => 0,
            'total_kredit' => 0,
            'rata_rata_saldo' => 0,
            'saldo_terbesar' => 0,
            'saldo_terkecil' => 0
        ];
    }
}