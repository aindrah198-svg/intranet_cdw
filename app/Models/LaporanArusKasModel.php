<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanArusKasModel extends Model
{
    protected $table = 'coa';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;
    
    /**
     * GET LAPORAN ARUS KAS - VERSI FIXED
     * Menyelesaikan masalah selisih Rp 1.000.000
     */
    public function getLaporanArusKas($startDate, $endDate, $excludeSaldoAwal = true)
    {
        $db = \Config\Database::connect();
        
        // ============================================
        // 1. DAPATKAN SEMUA AKUN KAS
        // ============================================
        $akunKas = $this->getAkunKas();
        
        if (empty($akunKas)) {
            return $this->getResponseKosong($startDate, $endDate);
        }
        
        $akunKasIds = array_column($akunKas, 'id');
        $akunKasMap = [];
        foreach ($akunKas as $akun) {
            $akunKasMap[$akun['id']] = $akun;
        }
        
        // ============================================
        // 2. HITUNG SALDO KAS DARI BUKU_BESAR
        // ============================================
        $saldoKas = $this->hitungSaldoKas($akunKasIds, $startDate, $endDate);
        
        // ============================================
        // 3. AMBIL TRANSAKSI JURNAL DETAIL (HANYA AKUN KAS)
        // ============================================
        $transaksiKas = $this->getTransaksiKas($akunKasIds, $startDate, $endDate);
        
        // ============================================
        // 4. FILTER TRANSAKSI SALDO AWAL JIKA DIMINTA
        // ============================================
        if ($excludeSaldoAwal) {
            $transaksiKas = $this->filterTransaksiSaldoAwal($transaksiKas);
        }
        
        // ============================================
        // 5. KLASIFIKASI TRANSAKSI KE AKTIVITAS
        // ============================================
        $klasifikasi = $this->klasifikasiTransaksi($transaksiKas, $akunKasIds);
        
        // ============================================
        // 6. HITUNG DETAIL PER AKUN KAS
        // ============================================
        $detailPerAkun = $this->hitungDetailPerAkun($akunKas, $startDate, $endDate);
        
        // ============================================
        // 7. VERIFIKASI LAPORAN
        // ============================================
        $verifikasi = $this->verifikasiLaporan($saldoKas, $klasifikasi);
        
        // ============================================
        // 8. HITUNG STATISTIK
        // ============================================
        $statistik = $this->hitungStatistik($transaksiKas, $klasifikasi, $akunKas);
        
        return [
            'periode' => [
                'start' => $startDate,
                'end' => $endDate,
                'label' => date('d M Y', strtotime($startDate)) . ' - ' . date('d M Y', strtotime($endDate))
            ],
            'akun_kas' => $akunKas,
            'saldo_kas' => $saldoKas,
            'transaksi_kas' => $transaksiKas,
            'arus_kas' => [
                'operasi' => [
                    'penerimaan' => $klasifikasi['operasi']['penerimaan'],
                    'pengeluaran' => $klasifikasi['operasi']['pengeluaran'],
                    'items' => $klasifikasi['operasi']['items'],
                    'total' => $klasifikasi['operasi']['total']
                ],
                'investasi' => [
                    'penerimaan' => $klasifikasi['investasi']['penerimaan'],
                    'pengeluaran' => $klasifikasi['investasi']['pengeluaran'],
                    'items' => $klasifikasi['investasi']['items'],
                    'total' => $klasifikasi['investasi']['total']
                ],
                'pendanaan' => [
                    'penerimaan' => $klasifikasi['pendanaan']['penerimaan'],
                    'pengeluaran' => $klasifikasi['pendanaan']['pengeluaran'],
                    'items' => $klasifikasi['pendanaan']['items'],
                    'total' => $klasifikasi['pendanaan']['total']
                ]
            ],
            'total_operasi' => $klasifikasi['operasi']['total'],
            'total_investasi' => $klasifikasi['investasi']['total'],
            'total_pendanaan' => $klasifikasi['pendanaan']['total'],
            'total_arus_kas' => $klasifikasi['operasi']['total'] + 
                                $klasifikasi['investasi']['total'] + 
                                $klasifikasi['pendanaan']['total'],
            'detail_per_akun' => $detailPerAkun,
            'verifikasi' => $verifikasi,
            'statistik' => $statistik,
            'summary' => [
                'saldo_awal' => $saldoKas['saldo_awal'],
                'saldo_akhir' => $saldoKas['saldo_akhir'],
                'perubahan_kas' => $saldoKas['perubahan_kas'],
                'arus_kas_bersih' => $klasifikasi['operasi']['total'] + 
                                     $klasifikasi['investasi']['total'] + 
                                     $klasifikasi['pendanaan']['total'],
                'total_transaksi' => count($transaksiKas),
                'akun_kas_aktif' => count(array_filter($detailPerAkun, function($akun) {
                    return $akun['saldo_akhir'] > 0 || $akun['saldo_awal'] > 0;
                }))
            ],
            'metadata' => [
                'calculation_method' => 'arus_kas_fixed',
                'generated_at' => date('Y-m-d H:i:s'),
                'exclude_saldo_awal' => $excludeSaldoAwal,
                'version' => '2.0.1 - Fixed Arus Kas'
            ]
        ];
    }
    
    /**
     * GET SEMUA AKUN KAS (BUKAN HANYA 3 AKUN)
     */
    private function getAkunKas()
    {
        $db = \Config\Database::connect();
        
        // Ambil semua akun yang masuk kategori kas (kode 1-1100 ke bawah)
        return $db->table('coa')
            ->select('id, kode_akun, nama_akun, tipe_akun, kategori, saldo_normal')
            ->where('is_active', 1)
            ->where('is_header', 0)
            ->groupStart()
                ->where('kode_akun LIKE', '1-11%')     // Semua akun kas
                ->orWhere('kode_akun', '1-1101')       // Kas Kecil
                ->orWhere('kode_akun', '1-1102')       // Kas BCA
                ->orWhere('kode_akun', '1-1103')       // Kas Mandiri
                ->orWhere('kode_akun', '1-1104')       // Kas Lainnya (jika ada)
            ->groupEnd()
            ->orderBy('kode_akun', 'ASC')
            ->get()
            ->getResultArray();
    }
    
    /**
     * HITUNG SALDO KAS - MENGGUNAKAN METODE YANG BENAR
     */
    private function hitungSaldoKas($akunKasIds, $startDate, $endDate)
    {
        $db = \Config\Database::connect();
        
        $saldoAwal = 0;
        $saldoAkhir = 0;
        
        // ============================================
        // HITUNG SALDO AWAL (SEBELUM PERIODE)
        // ============================================
        foreach ($akunKasIds as $coaId) {
            // Ambil saldo akhir sebelum periode dimulai
            $query = $db->table('buku_besar')
                ->select('saldo_akhir')
                ->where('coa_id', $coaId)
                ->where('tanggal <', $startDate)
                ->orderBy('tanggal', 'DESC')
                ->orderBy('id', 'DESC')
                ->limit(1)
                ->get();
                
            $row = $query->getRowArray();
            
            if ($row) {
                $saldoAwal += (float)$row['saldo_akhir'];
            } else {
                // Jika tidak ada transaksi sebelumnya, coba ambil dari jurnal saldo awal
                $querySaldoAwal = $db->table('buku_besar')
                    ->select('saldo_akhir')
                    ->where('coa_id', $coaId)
                    ->where('tanggal', $startDate)
                    ->orderBy('id', 'ASC')
                    ->limit(1)
                    ->get();
                    
                $rowSaldoAwal = $querySaldoAwal->getRowArray();
                if ($rowSaldoAwal) {
                    $saldoAwal += (float)$rowSaldoAwal['saldo_akhir'];
                }
            }
        }
        
        // ============================================
        // HITUNG SALDO AKHIR (PERIODE BERAKHIR)
        // ============================================
        foreach ($akunKasIds as $coaId) {
            $query = $db->table('buku_besar')
                ->select('saldo_akhir')
                ->where('coa_id', $coaId)
                ->where('tanggal <=', $endDate)
                ->orderBy('tanggal', 'DESC')
                ->orderBy('id', 'DESC')
                ->limit(1)
                ->get();
                
            $row = $query->getRowArray();
            
            if ($row) {
                $saldoAkhir += (float)$row['saldo_akhir'];
            }
        }
        
        // Perubahan kas
        $perubahanKas = $saldoAkhir - $saldoAwal;
        
        return [
            'saldo_awal' => $saldoAwal,
            'saldo_akhir' => $saldoAkhir,
            'perubahan_kas' => $perubahanKas,
            'saldo_awal_formatted' => 'Rp ' . number_format($saldoAwal, 0, ',', '.'),
            'saldo_akhir_formatted' => 'Rp ' . number_format($saldoAkhir, 0, ',', '.'),
            'perubahan_kas_formatted' => 'Rp ' . number_format($perubahanKas, 0, ',', '.')
        ];
    }
    
    /**
     * GET TRANSAKSI KAS DARI JURNAL DETAIL
     */
    private function getTransaksiKas($akunKasIds, $startDate, $endDate)
    {
        $db = \Config\Database::connect();
        
        if (empty($akunKasIds)) {
            return [];
        }
        
        $placeholders = implode(',', array_fill(0, count($akunKasIds), '?'));
        $params = array_merge($akunKasIds, [$startDate, $endDate]);
        
        $query = $db->query("
            SELECT 
                jd.id,
                jd.jurnal_id,
                jd.coa_id,
                jd.kode_akun,
                jd.nama_akun,
                jd.debit,
                jd.kredit,
                jd.keterangan as detail_keterangan,
                j.nomor_jurnal,
                j.tanggal,
                j.keterangan as jurnal_keterangan,
                j.referensi,
                j.tipe_referensi,
                j.status,
                coa.tipe_akun,
                coa.kategori,
                coa.saldo_normal,
                (
                    SELECT GROUP_CONCAT(
                        CONCAT(kode_akun, '|', 
                               CASE WHEN debit > 0 THEN 'D' ELSE 'K' END, '|', 
                               nama_akun, '|', 
                               CASE WHEN debit > 0 THEN debit ELSE kredit END)
                        SEPARATOR ';'
                    )
                    FROM jurnal_detail jd2
                    WHERE jd2.jurnal_id = j.id
                      AND jd2.coa_id != jd.coa_id
                ) as counterpart_info
            FROM jurnal_detail jd
            JOIN jurnal j ON j.id = jd.jurnal_id
            JOIN coa ON coa.id = jd.coa_id
            WHERE jd.coa_id IN ({$placeholders})
              AND j.tanggal BETWEEN ? AND ?
              AND j.status = 'posted'
              AND (jd.debit > 0 OR jd.kredit > 0)
            ORDER BY j.tanggal ASC, j.id ASC, jd.id ASC
        ", $params);
        
        $transaksi = $query->getResultArray();
        
        // Parse counterpart_info untuk setiap transaksi
        foreach ($transaksi as &$trx) {
            $trx['counterpart'] = $this->parseCounterpartInfo($trx['counterpart_info'] ?? '');
            $trx['arus_kas'] = (float)$trx['debit'] - (float)$trx['kredit'];
            $trx['is_penerimaan'] = $trx['debit'] > 0;
            $trx['is_pengeluaran'] = $trx['kredit'] > 0;
            $trx['jumlah'] = $trx['debit'] > 0 ? $trx['debit'] : $trx['kredit'];
        }
        
        return $transaksi;
    }
    
    /**
     * PARSE COUNTERPART INFO
     */
    private function parseCounterpartInfo($counterpartInfo)
    {
        if (empty($counterpartInfo)) {
            return [];
        }
        
        $counterparts = [];
        $parts = explode(';', $counterpartInfo);
        
        foreach ($parts as $part) {
            $data = explode('|', $part);
            if (count($data) >= 4) {
                $counterparts[] = [
                    'kode_akun' => $data[0],
                    'tipe' => $data[1],
                    'nama_akun' => $data[2],
                    'jumlah' => (float)$data[3]
                ];
            }
        }
        
        return $counterparts;
    }
    
    /**
     * FILTER TRANSAKSI SALDO AWAL
     */
    private function filterTransaksiSaldoAwal($transaksiKas)
    {
        return array_filter($transaksiKas, function($trx) {
            // Skip jurnal saldo awal (nomor jurnal JRNL-20260201-XXXX)
            if (strpos($trx['nomor_jurnal'] ?? '', 'JRNL-20260201-') === 0) {
                return false;
            }
            
            // Skip yang keterangannya mengandung "Saldo Awal"
            $keterangan = strtolower($trx['jurnal_keterangan'] ?? '');
            if (strpos($keterangan, 'saldo awal') !== false) {
                return false;
            }
            
            return true;
        });
    }
    
    /**
     * KLASIFIKASI TRANSAKSI KE AKTIVITAS - VERSI FIXED
     */
    private function klasifikasiTransaksi($transaksiKas, $akunKasIds)
    {
        $operasi = [
            'penerimaan' => 0,
            'pengeluaran' => 0,
            'items' => [],
            'total' => 0
        ];
        
        $investasi = [
            'penerimaan' => 0,
            'pengeluaran' => 0,
            'items' => [],
            'total' => 0
        ];
        
        $pendanaan = [
            'penerimaan' => 0,
            'pengeluaran' => 0,
            'items' => [],
            'total' => 0
        ];
        
        foreach ($transaksiKas as $trx) {
            $kategori = $this->tentukanAktivitas($trx);
            $arusKas = $trx['arus_kas'];
            $jumlah = $trx['jumlah'];
            
            $item = [
                'tanggal' => $trx['tanggal'],
                'nomor_jurnal' => $trx['nomor_jurnal'],
                'referensi' => $trx['referensi'],
                'kode_akun_kas' => $trx['kode_akun'],
                'nama_akun_kas' => $trx['nama_akun'],
                'keterangan' => !empty($trx['detail_keterangan']) ? 
                               $trx['detail_keterangan'] : $trx['jurnal_keterangan'],
                'penerimaan' => $trx['is_penerimaan'] ? $jumlah : 0,
                'pengeluaran' => $trx['is_pengeluaran'] ? $jumlah : 0,
                'jumlah' => $jumlah,
                'arus_kas' => $arusKas,
                'counterpart' => $trx['counterpart'],
                'aktivitas' => $kategori
            ];
            
            switch ($kategori) {
                case 'INVESTASI':
                    $investasi['items'][] = $item;
                    if ($trx['is_penerimaan']) {
                        $investasi['penerimaan'] += $jumlah;
                    } else {
                        $investasi['pengeluaran'] += $jumlah;
                    }
                    $investasi['total'] += $arusKas;
                    break;
                    
                case 'PENDANAAN':
                    $pendanaan['items'][] = $item;
                    if ($trx['is_penerimaan']) {
                        $pendanaan['penerimaan'] += $jumlah;
                    } else {
                        $pendanaan['pengeluaran'] += $jumlah;
                    }
                    $pendanaan['total'] += $arusKas;
                    break;
                    
                case 'OPERASI':
                default:
                    $operasi['items'][] = $item;
                    if ($trx['is_penerimaan']) {
                        $operasi['penerimaan'] += $jumlah;
                    } else {
                        $operasi['pengeluaran'] += $jumlah;
                    }
                    $operasi['total'] += $arusKas;
                    break;
            }
        }
        
        return [
            'operasi' => $operasi,
            'investasi' => $investasi,
            'pendanaan' => $pendanaan
        ];
    }
    
    /**
     * TENTUKAN AKTIVITAS BERDASARKAN COUNTERPART
     * RULES YANG JELAS DAN KETAT
     */
    private function tentukanAktivitas($transaksi)
    {
        $keterangan = strtolower($transaksi['jurnal_keterangan'] ?? '');
        $detailKeterangan = strtolower($transaksi['detail_keterangan'] ?? '');
        $fullKeterangan = $keterangan . ' ' . $detailKeterangan;
        
        // ============================================
        // RULE 1: SALDO AWAL - SELALU DI SKIP
        // ============================================
        if (strpos($transaksi['nomor_jurnal'] ?? '', 'JRNL-20260201-') === 0 ||
            strpos($keterangan, 'saldo awal') !== false) {
            return 'OPERASI'; // Akan difilter di fungsi sebelumnya
        }
        
        // ============================================
        // RULE 2: CEK COUNTERPART UNTUK INVESTASI
        // ============================================
        foreach ($transaksi['counterpart'] as $cp) {
            $kodeAkun = $cp['kode_akun'] ?? '';
            $namaAkun = strtolower($cp['nama_akun'] ?? '');
            
            // INVESTASI: Aset Tetap (1-2xxx)
            if (strpos($kodeAkun, '1-2') === 0) {
                return 'INVESTASI';
            }
            
            // INVESTASI: Peralatan, Kendaraan, Mesin
            if (strpos($namaAkun, 'peralatan') !== false ||
                strpos($namaAkun, 'kendaraan') !== false ||
                strpos($namaAkun, 'mesin') !== false ||
                strpos($namaAkun, 'komputer') !== false ||
                strpos($namaAkun, 'aset tetap') !== false) {
                return 'INVESTASI';
            }
        }
        
        // ============================================
        // RULE 3: CEK COUNTERPART UNTUK PENDANAAN
        // ============================================
        foreach ($transaksi['counterpart'] as $cp) {
            $kodeAkun = $cp['kode_akun'] ?? '';
            $namaAkun = strtolower($cp['nama_akun'] ?? '');
            
            // PENDANAAN: Ekuitas (3-xxxx)
            if (strpos($kodeAkun, '3-') === 0) {
                return 'PENDANAAN';
            }
            
            // PENDANAAN: Kewajiban Jangka Panjang (2-2xxx)
            if (strpos($kodeAkun, '2-2') === 0) {
                return 'PENDANAAN';
            }
            
            // PENDANAAN: Modal, Pinjaman, Hutang Bank
            if (strpos($namaAkun, 'modal') !== false ||
                strpos($namaAkun, 'pinjaman') !== false ||
                strpos($namaAkun, 'hutang bank') !== false) {
                return 'PENDANAAN';
            }
        }
        
        // ============================================
        // RULE 4: CEK BERDASARKAN KEYWORD DI KETERANGAN
        // ============================================
        
        // INVESTASI - Keywords
        $investasiKeywords = [
            'beli aset', 'pembelian aset', 'aset tetap',
            'beli komputer', 'beli laptop', 'beli kendaraan',
            'beli peralatan', 'beli mesin', 'investasi'
        ];
        
        foreach ($investasiKeywords as $keyword) {
            if (strpos($fullKeterangan, $keyword) !== false) {
                return 'INVESTASI';
            }
        }
        
        // PENDANAAN - Keywords
        $pendanaanKeywords = [
            'setoran modal', 'tambah modal', 'modal disetor',
            'pinjaman bank', 'hutang bank', 'pinjaman jangka panjang'
        ];
        
        foreach ($pendanaanKeywords as $keyword) {
            if (strpos($fullKeterangan, $keyword) !== false) {
                return 'PENDANAAN';
            }
        }
        
        // ============================================
        // RULE 5: DEFAULT - OPERASI
        // ============================================
        return 'OPERASI';
    }
    
    /**
     * HITUNG DETAIL PER AKUN KAS
     */
    private function hitungDetailPerAkun($akunKas, $startDate, $endDate)
    {
        $db = \Config\Database::connect();
        $detail = [];
        
        foreach ($akunKas as $akun) {
            // ============================================
            // SALDO AWAL
            // ============================================
            $saldoAwal = 0;
            
            // Coba ambil saldo sebelum periode
            $query = $db->table('buku_besar')
                ->select('saldo_akhir')
                ->where('coa_id', $akun['id'])
                ->where('tanggal <', $startDate)
                ->orderBy('tanggal', 'DESC')
                ->orderBy('id', 'DESC')
                ->limit(1)
                ->get();
                
            $row = $query->getRowArray();
            if ($row) {
                $saldoAwal = (float)$row['saldo_akhir'];
            } else {
                // Jika tidak ada, ambil saldo di tanggal startDate
                $queryStart = $db->table('buku_besar')
                    ->select('saldo_akhir')
                    ->where('coa_id', $akun['id'])
                    ->where('tanggal', $startDate)
                    ->orderBy('id', 'ASC')
                    ->limit(1)
                    ->get();
                    
                $rowStart = $queryStart->getRowArray();
                if ($rowStart) {
                    $saldoAwal = (float)$rowStart['saldo_akhir'];
                }
            }
            
            // ============================================
            // MUTASI PERIODE
            // ============================================
            $queryMutasi = $db->table('jurnal_detail jd')
                ->select('SUM(jd.debit) as total_debit, SUM(jd.kredit) as total_kredit')
                ->join('jurnal j', 'j.id = jd.jurnal_id')
                ->where('jd.coa_id', $akun['id'])
                ->where('j.tanggal >=', $startDate)
                ->where('j.tanggal <=', $endDate)
                ->where('j.status', 'posted')
                ->get();
                
            $mutasi = $queryMutasi->getRowArray();
            $debit = (float)($mutasi['total_debit'] ?? 0);
            $kredit = (float)($mutasi['total_kredit'] ?? 0);
            
            // ============================================
            // SALDO AKHIR
            // ============================================
            $saldoAkhir = 0;
            $queryAkhir = $db->table('buku_besar')
                ->select('saldo_akhir')
                ->where('coa_id', $akun['id'])
                ->where('tanggal <=', $endDate)
                ->orderBy('tanggal', 'DESC')
                ->orderBy('id', 'DESC')
                ->limit(1)
                ->get();
                
            $rowAkhir = $queryAkhir->getRowArray();
            if ($rowAkhir) {
                $saldoAkhir = (float)$rowAkhir['saldo_akhir'];
            }
            
            // ============================================
            // VERIFIKASI
            // ============================================
            $perubahan = $debit - $kredit;
            $saldoHitung = $saldoAwal + $perubahan;
            $selisih = abs($saldoHitung - $saldoAkhir);
            $valid = $selisih < 0.01;
            
            $detail[] = [
                'id' => $akun['id'],
                'kode_akun' => $akun['kode_akun'],
                'nama_akun' => $akun['nama_akun'],
                'saldo_awal' => $saldoAwal,
                'saldo_awal_formatted' => 'Rp ' . number_format($saldoAwal, 0, ',', '.'),
                'debit_periode' => $debit,
                'debit_periode_formatted' => 'Rp ' . number_format($debit, 0, ',', '.'),
                'kredit_periode' => $kredit,
                'kredit_periode_formatted' => 'Rp ' . number_format($kredit, 0, ',', '.'),
                'perubahan' => $perubahan,
                'perubahan_formatted' => 'Rp ' . number_format($perubahan, 0, ',', '.'),
                'saldo_akhir' => $saldoAkhir,
                'saldo_akhir_formatted' => 'Rp ' . number_format($saldoAkhir, 0, ',', '.'),
                'saldo_hitung' => $saldoHitung,
                'verifikasi' => $valid,
                'verifikasi_label' => $valid ? '✓ Valid' : '✗ Tidak Valid',
                'selisih' => $selisih
            ];
        }
        
        return $detail;
    }
    
    /**
     * VERIFIKASI LAPORAN ARUS KAS
     * Rumus: Saldo Awal + Arus Kas Bersih = Saldo Akhir
     */
    private function verifikasiLaporan($saldoKas, $klasifikasi)
    {
        $totalArusKas = $klasifikasi['operasi']['total'] + 
                       $klasifikasi['investasi']['total'] + 
                       $klasifikasi['pendanaan']['total'];
        
        $saldoAwal = $saldoKas['saldo_awal'];
        $saldoAkhir = $saldoKas['saldo_akhir'];
        $perubahanSaldo = $saldoAkhir - $saldoAwal;
        
        $selisih = abs($totalArusKas - $perubahanSaldo);
        $isValid = $selisih < 0.01;
        
        $saldoHitung = $saldoAwal + $totalArusKas;
        
        return [
            'is_valid' => $isValid,
            'selisih' => $selisih,
            'selisih_formatted' => 'Rp ' . number_format($selisih, 0, ',', '.'),
            'total_arus_kas' => $totalArusKas,
            'total_arus_kas_formatted' => 'Rp ' . number_format($totalArusKas, 0, ',', '.'),
            'perubahan_saldo' => $perubahanSaldo,
            'perubahan_saldo_formatted' => 'Rp ' . number_format($perubahanSaldo, 0, ',', '.'),
            'saldo_awal' => $saldoAwal,
            'saldo_akhir' => $saldoAkhir,
            'saldo_hitung' => $saldoHitung,
            'keterangan' => $isValid ? 
                '✓ LAPORAN VALID: Saldo Awal + Arus Kas Bersih = Saldo Akhir' :
                '✗ LAPORAN TIDAK VALID: Terdapat selisih Rp ' . number_format($selisih, 0, ',', '.'),
            'formula' => 'Rp ' . number_format($saldoAwal, 0, ',', '.') . ' + (' . 
                        'Rp ' . number_format($totalArusKas, 0, ',', '.') . ') = ' .
                        'Rp ' . number_format($saldoHitung, 0, ',', '.') . 
                        ' (Seharusnya: Rp ' . number_format($saldoAkhir, 0, ',', '.') . ')'
        ];
    }
    
    /**
     * HITUNG STATISTIK LAPORAN
     */
    private function hitungStatistik($transaksiKas, $klasifikasi, $akunKas)
    {
        $totalPenerimaan = 0;
        $totalPengeluaran = 0;
        
        foreach ($transaksiKas as $trx) {
            if ($trx['is_penerimaan']) {
                $totalPenerimaan += $trx['jumlah'];
            } else {
                $totalPengeluaran += $trx['jumlah'];
            }
        }
        
        $akunAktif = 0;
        foreach ($akunKas as $akun) {
            // Cek apakah akun memiliki saldo > 0 di akhir periode
            $db = \Config\Database::connect();
            $query = $db->table('buku_besar')
                ->select('saldo_akhir')
                ->where('coa_id', $akun['id'])
                ->orderBy('tanggal', 'DESC')
                ->orderBy('id', 'DESC')
                ->limit(1)
                ->get();
                
            $row = $query->getRowArray();
            if ($row && (float)$row['saldo_akhir'] > 0) {
                $akunAktif++;
            }
        }
        
        return [
            'total_transaksi' => count($transaksiKas),
            'total_penerimaan' => $totalPenerimaan,
            'total_pengeluaran' => $totalPengeluaran,
            'total_akun_kas' => count($akunKas),
            'akun_kas_aktif' => $akunAktif,
            'transaksi_operasi' => count($klasifikasi['operasi']['items']),
            'transaksi_investasi' => count($klasifikasi['investasi']['items']),
            'transaksi_pendanaan' => count($klasifikasi['pendanaan']['items']),
            'penerimaan_operasi' => $klasifikasi['operasi']['penerimaan'],
            'pengeluaran_operasi' => $klasifikasi['operasi']['pengeluaran'],
            'penerimaan_investasi' => $klasifikasi['investasi']['penerimaan'],
            'pengeluaran_investasi' => $klasifikasi['investasi']['pengeluaran'],
            'penerimaan_pendanaan' => $klasifikasi['pendanaan']['penerimaan'],
            'pengeluaran_pendanaan' => $klasifikasi['pendanaan']['pengeluaran']
        ];
    }
    
    /**
     * RESPONSE UNTUK DATA KOSONG
     */
    private function getResponseKosong($startDate, $endDate)
    {
        return [
            'periode' => [
                'start' => $startDate,
                'end' => $endDate,
                'label' => date('d M Y', strtotime($startDate)) . ' - ' . date('d M Y', strtotime($endDate))
            ],
            'akun_kas' => [],
            'saldo_kas' => [
                'saldo_awal' => 0,
                'saldo_akhir' => 0,
                'perubahan_kas' => 0,
                'saldo_awal_formatted' => 'Rp 0',
                'saldo_akhir_formatted' => 'Rp 0',
                'perubahan_kas_formatted' => 'Rp 0'
            ],
            'transaksi_kas' => [],
            'arus_kas' => [
                'operasi' => ['penerimaan' => 0, 'pengeluaran' => 0, 'items' => [], 'total' => 0],
                'investasi' => ['penerimaan' => 0, 'pengeluaran' => 0, 'items' => [], 'total' => 0],
                'pendanaan' => ['penerimaan' => 0, 'pengeluaran' => 0, 'items' => [], 'total' => 0]
            ],
            'total_operasi' => 0,
            'total_investasi' => 0,
            'total_pendanaan' => 0,
            'total_arus_kas' => 0,
            'detail_per_akun' => [],
            'verifikasi' => [
                'is_valid' => true,
                'selisih' => 0,
                'selisih_formatted' => 'Rp 0',
                'total_arus_kas' => 0,
                'perubahan_saldo' => 0,
                'keterangan' => 'Tidak ada data transaksi kas pada periode ini'
            ],
            'statistik' => [
                'total_transaksi' => 0,
                'total_penerimaan' => 0,
                'total_pengeluaran' => 0,
                'total_akun_kas' => 0,
                'akun_kas_aktif' => 0
            ],
            'summary' => [
                'saldo_awal' => 0,
                'saldo_akhir' => 0,
                'perubahan_kas' => 0,
                'arus_kas_bersih' => 0,
                'total_transaksi' => 0,
                'akun_kas_aktif' => 0
            ],
            'metadata' => [
                'calculation_method' => 'arus_kas_fixed',
                'generated_at' => date('Y-m-d H:i:s'),
                'exclude_saldo_awal' => true,
                'version' => '2.0.1 - Fixed Arus Kas',
                'note' => 'Tidak ada data akun kas ditemukan'
            ]
        ];
    }
    
    /**
     * ALIAS UNTUK KOMPATIBILITAS
     */
    public function getLaporanArusKasSederhana($startDate, $endDate)
    {
        return $this->getLaporanArusKas($startDate, $endDate, true);
    }
    
    /**
     * GET RINGKASAN UNTUK DASHBOARD
     */
    public function getRingkasanArusKas($startDate, $endDate)
    {
        try {
            $laporan = $this->getLaporanArusKas($startDate, $endDate, true);
            
            return [
                'success' => true,
                'data' => [
                    'saldo_awal' => $laporan['saldo_kas']['saldo_awal'],
                    'saldo_akhir' => $laporan['saldo_kas']['saldo_akhir'],
                    'saldo_awal_formatted' => $laporan['saldo_kas']['saldo_awal_formatted'],
                    'saldo_akhir_formatted' => $laporan['saldo_kas']['saldo_akhir_formatted'],
                    'perubahan_kas' => $laporan['saldo_kas']['perubahan_kas'],
                    'arus_operasi' => $laporan['total_operasi'],
                    'arus_investasi' => $laporan['total_investasi'],
                    'arus_pendanaan' => $laporan['total_pendanaan'],
                    'total_arus_kas' => $laporan['total_arus_kas'],
                    'is_valid' => $laporan['verifikasi']['is_valid'],
                    'is_valid_label' => $laporan['verifikasi']['is_valid'] ? 'VALID' : 'TIDAK VALID',
                    'periode' => $laporan['periode']['label']
                ]
            ];
        } catch (\Exception $e) {
            log_message('error', 'GetRingkasanArusKas Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'data' => [
                    'saldo_awal' => 0,
                    'saldo_akhir' => 0,
                    'saldo_awal_formatted' => 'Rp 0',
                    'saldo_akhir_formatted' => 'Rp 0',
                    'perubahan_kas' => 0,
                    'arus_operasi' => 0,
                    'arus_investasi' => 0,
                    'arus_pendanaan' => 0,
                    'total_arus_kas' => 0,
                    'is_valid' => false,
                    'is_valid_label' => 'ERROR',
                    'periode' => date('d M Y', strtotime($startDate)) . ' - ' . date('d M Y', strtotime($endDate))
                ]
            ];
        }
    }
    
    /**
     * DEBUG: CEK DATA MENTAH
     */
    public function debugDataMentah($startDate, $endDate)
    {
        $db = \Config\Database::connect();
        
        // 1. Ambil semua akun kas
        $akunKas = $this->getAkunKas();
        $akunIds = array_column($akunKas, 'id');
        
        // 2. Ambil semua jurnal dalam periode
        $jurnal = $db->table('jurnal')
            ->where('tanggal >=', $startDate)
            ->where('tanggal <=', $endDate)
            ->where('status', 'posted')
            ->orderBy('tanggal', 'ASC')
            ->get()
            ->getResultArray();
        
        // 3. Ambil jurnal detail untuk akun kas
        $jurnalDetail = [];
        if (!empty($akunIds)) {
            $placeholders = implode(',', array_fill(0, count($akunIds), '?'));
            $params = array_merge($akunIds, [$startDate, $endDate]);
            
            $jurnalDetail = $db->query("
                SELECT jd.*, j.nomor_jurnal, j.tanggal, j.keterangan as jurnal_keterangan
                FROM jurnal_detail jd
                JOIN jurnal j ON j.id = jd.jurnal_id
                WHERE jd.coa_id IN ({$placeholders})
                  AND j.tanggal BETWEEN ? AND ?
                  AND j.status = 'posted'
                ORDER BY j.tanggal ASC
            ", $params)->getResultArray();
        }
        
        // 4. Ambil data buku besar
        $bukuBesar = [];
        if (!empty($akunIds)) {
            $bukuBesar = $db->table('buku_besar')
                ->whereIn('coa_id', $akunIds)
                ->where('tanggal >=', $startDate)
                ->where('tanggal <=', $endDate)
                ->orderBy('tanggal', 'ASC')
                ->orderBy('id', 'ASC')
                ->get()
                ->getResultArray();
        }
        
        // 5. Debug informasi jurnal penyesuaian
        $jurnalPenyesuaian = $db->table('jurnal_penyesuaian')
            ->where('tanggal_penyesuaian >=', $startDate)
            ->where('tanggal_penyesuaian <=', $endDate)
            ->where('status', 'posted')
            ->get()
            ->getResultArray();
        
        return [
            'periode' => ['start' => $startDate, 'end' => $endDate],
            'akun_kas' => $akunKas,
            'jurnal' => $jurnal,
            'jurnal_detail_kas' => $jurnalDetail,
            'buku_besar_kas' => $bukuBesar,
            'jurnal_penyesuaian' => $jurnalPenyesuaian,
            'counts' => [
                'akun_kas' => count($akunKas),
                'jurnal' => count($jurnal),
                'jurnal_detail_kas' => count($jurnalDetail),
                'buku_besar_kas' => count($bukuBesar),
                'jurnal_penyesuaian' => count($jurnalPenyesuaian)
            ]
        ];
    }
    
    /**
     * HITUNG SALDO MANUAL UNTUK VERIFIKASI
     */
    public function hitungSaldoManual($startDate, $endDate)
    {
        $db = \Config\Database::connect();
        
        // Ambil akun kas
        $akunKas = $this->getAkunKas();
        $akunIds = array_column($akunKas, 'id');
        
        if (empty($akunKas)) {
            return ['error' => 'Tidak ada akun kas'];
        }
        
        $result = [];
        $totalSaldoAwal = 0;
        $totalSaldoAkhir = 0;
        $totalDebit = 0;
        $totalKredit = 0;
        
        foreach ($akunKas as $akun) {
            // Saldo awal
            $saldoAwal = 0;
            $queryAwal = $db->table('buku_besar')
                ->select('saldo_akhir')
                ->where('coa_id', $akun['id'])
                ->where('tanggal <', $startDate)
                ->orderBy('tanggal', 'DESC')
                ->orderBy('id', 'DESC')
                ->limit(1)
                ->get();
                
            $rowAwal = $queryAwal->getRowArray();
            if ($rowAwal) {
                $saldoAwal = (float)$rowAwal['saldo_akhir'];
            }
            
            // Saldo akhir
            $saldoAkhir = 0;
            $queryAkhir = $db->table('buku_besar')
                ->select('saldo_akhir')
                ->where('coa_id', $akun['id'])
                ->where('tanggal <=', $endDate)
                ->orderBy('tanggal', 'DESC')
                ->orderBy('id', 'DESC')
                ->limit(1)
                ->get();
                
            $rowAkhir = $queryAkhir->getRowArray();
            if ($rowAkhir) {
                $saldoAkhir = (float)$rowAkhir['saldo_akhir'];
            }
            
            // Mutasi periode
            $queryMutasi = $db->table('jurnal_detail jd')
                ->select('SUM(jd.debit) as debit, SUM(jd.kredit) as kredit')
                ->join('jurnal j', 'j.id = jd.jurnal_id')
                ->where('jd.coa_id', $akun['id'])
                ->where('j.tanggal >=', $startDate)
                ->where('j.tanggal <=', $endDate)
                ->where('j.status', 'posted')
                ->get();
                
            $mutasi = $queryMutasi->getRowArray();
            $debit = (float)($mutasi['debit'] ?? 0);
            $kredit = (float)($mutasi['kredit'] ?? 0);
            
            $result[$akun['kode_akun']] = [
                'nama' => $akun['nama_akun'],
                'saldo_awal' => $saldoAwal,
                'debit' => $debit,
                'kredit' => $kredit,
                'perubahan' => $debit - $kredit,
                'saldo_akhir' => $saldoAkhir,
                'saldo_hitung' => $saldoAwal + ($debit - $kredit),
                'valid' => abs(($saldoAwal + ($debit - $kredit)) - $saldoAkhir) < 0.01
            ];
            
            $totalSaldoAwal += $saldoAwal;
            $totalSaldoAkhir += $saldoAkhir;
            $totalDebit += $debit;
            $totalKredit += $kredit;
        }
        
        $perubahanKas = $totalSaldoAkhir - $totalSaldoAwal;
        $arusKasNet = $totalDebit - $totalKredit;
        
        return [
            'detail_akun' => $result,
            'total' => [
                'saldo_awal' => $totalSaldoAwal,
                'saldo_akhir' => $totalSaldoAkhir,
                'debit' => $totalDebit,
                'kredit' => $totalKredit,
                'perubahan_kas' => $perubahanKas,
                'arus_kas_net' => $arusKasNet
            ],
            'verifikasi' => [
                'is_valid' => abs($perubahanKas - $arusKasNet) < 0.01,
                'selisih' => abs($perubahanKas - $arusKasNet),
                'formula' => 'Saldo Akhir (' . number_format($totalSaldoAkhir, 0) . ') - Saldo Awal (' . 
                            number_format($totalSaldoAwal, 0) . ') = ' . number_format($perubahanKas, 0) . ' | ' .
                            'Arus Kas Net = ' . number_format($arusKasNet, 0)
            ]
        ];
    }
    
    /**
     * CLEAR CACHE
     */
    public function clearCache()
    {
        // Jika Anda menggunakan cache, hapus di sini
        // Contoh: cache()->clean();
        return true;
    }

/**
 * GET ARUS KAS BULANAN - VERSI SANGAT SEDERHANA
 * Tanpa filter saldo awal untuk menghindari error binding
 */
public function getArusKasBulanan($tahun = null)
{
    $db = \Config\Database::connect();
    
    if ($tahun === null) {
        $tahun = date('Y');
    }
    
    // 1. Dapatkan semua akun kas
    $akunKas = $this->getAkunKas();
    $akunKasIds = array_column($akunKas, 'id');
    
    if (empty($akunKasIds)) {
        return $this->getEmptyArusKasBulanan();
    }
    
    // 2. Array untuk menyimpan data per bulan
    $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $data = [];
    
    for ($i = 1; $i <= 12; $i++) {
        $startDate = $tahun . '-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));
        
        // AMBIL DARI BUKU_BESAR - LEBIH SEDERHANA
        $builder = $db->table('buku_besar');
        $builder->select('
            COALESCE(SUM(CASE WHEN coa.saldo_normal = "Debit" THEN debit ELSE 0 END), 0) as total_penerimaan,
            COALESCE(SUM(CASE WHEN coa.saldo_normal = "Kredit" THEN kredit ELSE 0 END), 0) as total_pengeluaran
        ');
        $builder->join('coa', 'coa.id = buku_besar.coa_id');
        $builder->whereIn('buku_besar.coa_id', $akunKasIds);
        $builder->where('buku_besar.tanggal >=', $startDate);
        $builder->where('buku_besar.tanggal <=', $endDate);
        
        $query = $builder->get();
        $result = $query->getRowArray();
        
        $penerimaan = (float)($result['total_penerimaan'] ?? 0);
        $pengeluaran = (float)($result['total_pengeluaran'] ?? 0);
        $arusKas = $penerimaan - $pengeluaran;
        
        $data[] = [
            'bulan' => $bulan[$i - 1],
            'bulan_angka' => $i,
            'tahun' => $tahun,
            'periode' => $startDate . ' - ' . $endDate,
            'penerimaan' => $penerimaan,
            'pengeluaran' => $pengeluaran,
            'arus_kas' => $arusKas,
            'penerimaan_formatted' => 'Rp ' . number_format($penerimaan, 0, ',', '.'),
            'pengeluaran_formatted' => 'Rp ' . number_format($pengeluaran, 0, ',', '.'),
            'arus_kas_formatted' => 'Rp ' . number_format($arusKas, 0, ',', '.')
        ];
    }
    
    return $data;
}

/**
 * GET ARUS KAS BULANAN - DENGAN FILTER TAHUN DAN BULAN TERTENTU
 */
public function getArusKasBulananFiltered($tahun, $bulan = null)
{
    $db = \Config\Database::connect();
    
    $akunKas = $this->getAkunKas();
    $akunKasIds = array_column($akunKas, 'id');
    
    if (empty($akunKasIds)) {
        return [];
    }
    
    if ($bulan !== null) {
        // Satu bulan tertentu
        $startDate = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));
        
        $placeholders = implode(',', array_fill(0, count($akunKasIds), '?'));
        
        // ============ PERBAIKAN: PARAMETER BINDING YANG BENAR ============
        $params = array_merge(
            $akunKasIds,
            [$startDate, $endDate],
            ['JRNL-20260201-%'],
            ['%saldo awal%']
        );
        
        $sql = "
            SELECT 
                COALESCE(SUM(jd.debit), 0) as total_penerimaan,
                COALESCE(SUM(jd.kredit), 0) as total_pengeluaran
            FROM jurnal_detail jd
            INNER JOIN jurnal j ON j.id = jd.jurnal_id
            WHERE jd.coa_id IN ({$placeholders})
              AND j.tanggal BETWEEN ? AND ?
              AND j.status = 'posted'
              AND j.nomor_jurnal NOT LIKE ?
              AND j.keterangan NOT LIKE ?
              AND (jd.debit > 0 OR jd.kredit > 0)
        ";
        
        $query = $db->query($sql, $params);
        $result = $query->getRowArray();
        
        $penerimaan = (float)($result['total_penerimaan'] ?? 0);
        $pengeluaran = (float)($result['total_pengeluaran'] ?? 0);
        
        return [
            'penerimaan' => $penerimaan,
            'pengeluaran' => $pengeluaran,
            'arus_kas' => $penerimaan - $pengeluaran,
            'penerimaan_formatted' => 'Rp ' . number_format($penerimaan, 0, ',', '.'),
            'pengeluaran_formatted' => 'Rp ' . number_format($pengeluaran, 0, ',', '.'),
            'arus_kas_formatted' => 'Rp ' . number_format($penerimaan - $pengeluaran, 0, ',', '.')
        ];
    } else {
        // Semua bulan dalam tahun
        return $this->getArusKasBulanan($tahun);
    }
}

/**
 * EMPTY RESPONSE UNTUK ARUS KAS BULANAN
 */
private function getEmptyArusKasBulanan()
{
    $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $data = [];
    
    for ($i = 0; $i < 12; $i++) {
        $data[] = [
            'bulan' => $bulan[$i],
            'bulan_angka' => $i + 1,
            'tahun' => date('Y'),
            'periode' => '',
            'penerimaan' => 0,
            'pengeluaran' => 0,
            'arus_kas' => 0,
            'penerimaan_formatted' => 'Rp 0',
            'pengeluaran_formatted' => 'Rp 0',
            'arus_kas_formatted' => 'Rp 0'
        ];
    }
    
    return $data;
}

/**
 * GET TREN ARUS KAS UNTUK DASHBOARD
 */
public function getTrenArusKas($tahun = null)
{
    $data = $this->getArusKasBulanan($tahun);
    
    $labels = [];
    $penerimaan = [];
    $pengeluaran = [];
    $arusKas = [];
    
    foreach ($data as $item) {
        $labels[] = $item['bulan'];
        $penerimaan[] = $item['penerimaan'];
        $pengeluaran[] = $item['pengeluaran'];
        $arusKas[] = $item['arus_kas'];
    }
    
    return [
        'labels' => $labels,
        'penerimaan' => $penerimaan,
        'pengeluaran' => $pengeluaran,
        'arus_kas' => $arusKas,
        'data' => $data
    ];
}

/**
 * GET SUMMARY ARUS KAS TAHUNAN
 */
public function getSummaryTahunan($tahun = null)
{
    if ($tahun === null) {
        $tahun = date('Y');
    }
    
    $dataBulanan = $this->getArusKasBulanan($tahun);
    
    $totalPenerimaan = 0;
    $totalPengeluaran = 0;
    $totalArusKas = 0;
    $bulanPositif = 0;
    $bulanNegatif = 0;
    
    foreach ($dataBulanan as $bulan) {
        $totalPenerimaan += $bulan['penerimaan'];
        $totalPengeluaran += $bulan['pengeluaran'];
        $totalArusKas += $bulan['arus_kas'];
        
        if ($bulan['arus_kas'] >= 0) {
            $bulanPositif++;
        } else {
            $bulanNegatif++;
        }
    }
    
    return [
        'tahun' => $tahun,
        'total_penerimaan' => $totalPenerimaan,
        'total_pengeluaran' => $totalPengeluaran,
        'total_arus_kas' => $totalArusKas,
        'total_penerimaan_formatted' => 'Rp ' . number_format($totalPenerimaan, 0, ',', '.'),
        'total_pengeluaran_formatted' => 'Rp ' . number_format($totalPengeluaran, 0, ',', '.'),
        'total_arus_kas_formatted' => 'Rp ' . number_format($totalArusKas, 0, ',', '.'),
        'rata_rata_per_bulan' => $totalArusKas / 12,
        'bulan_positif' => $bulanPositif,
        'bulan_negatif' => $bulanNegatif,
        'kinerja' => $totalArusKas >= 0 ? 'Surplus' : 'Defisit'
    ];
}

}