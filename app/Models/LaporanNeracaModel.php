<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanNeracaModel extends Model
{
    protected $table = 'buku_besar';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    
    /**
     * GET LAPORAN NERACA - VERSI STANDAR (FIXED)
     */
    public function getLaporanNeraca($periodeDate)
    {
        $db = \Config\Database::connect();
        
        // ============================================
        // 1. DAPATKAN SEMUA AKUN ASET, KEWAJIBAN, EKUITAS
        // ============================================
        $coaModel = new CoaModel();
        $akuns = $coaModel->where('is_active', 1)
            ->where('is_header', 0)
            ->groupStart()
                ->where('tipe_akun', 'Aset')
                ->orWhere('tipe_akun', 'Kewajiban')
                ->orWhere('tipe_akun', 'Ekuitas')
            ->groupEnd()
            ->orderBy('kode_akun', 'ASC')
            ->findAll();
        
        if (empty($akuns)) {
            return $this->getResponseKosong($periodeDate);
        }
        
        // ============================================
        // 2. HITUNG SALDO PER AKUN SAMPAI DENGAN PERIODE
        // ============================================
        $hasilAkun = [];
        $totalAset = 0;
        $totalKewajiban = 0;
        $totalEkuitas = 0;
        
       foreach ($akuns as $akun) {
    $saldo = $this->getSaldoAkunSampaiTanggal($akun['id'], $periodeDate);
    
    // Skip jika saldo 0
    if (abs($saldo) < 0.01) {
        continue;
    }
    
    // Simpan nilai ASLI (sudah termasuk negatif untuk kontra-aset)
    $dataAkun = [
        'id' => $akun['id'],
        'kode_akun' => $akun['kode_akun'],
        'nama_akun' => $akun['nama_akun'],
        'tipe_akun' => $akun['tipe_akun'],
        'kategori' => $akun['kategori'] ?? '',
        'saldo_normal' => $akun['saldo_normal'],
        'saldo' => $saldo,  // NILAI ASLI (bisa positif/negatif)
        'saldo_formatted' => $this->formatRupiah($saldo)
    ];
    
    $hasilAkun[] = $dataAkun;
    
    // Akumulasi total berdasarkan tipe akun
    if ($akun['tipe_akun'] == 'Aset') {
        $totalAset += $saldo;  // Langsung tambah (nilai negatif akan mengurangi)
    } elseif ($akun['tipe_akun'] == 'Kewajiban') {
        $totalKewajiban += $saldo;
    } elseif ($akun['tipe_akun'] == 'Ekuitas') {
        $totalEkuitas += $saldo;
    }
}
        
        // ============================================
        // 3. HITUNG LABA BERJALAN - PAKAI QUERY LANGSUNG
        // ============================================
        $labaBerjalan = $this->hitungLabaBerjalanFix($periodeDate);
        
        // Tambahkan laba berjalan ke ekuitas
        $totalEkuitas += $labaBerjalan['laba_bersih'];
        
        // ============================================
        // 4. VERIFIKASI PERSAMAAN DASAR AKUNTANSI
        // ============================================
        $verifikasi = $this->verifikasiNeraca($totalAset, $totalKewajiban, $totalEkuitas);
        
        // ============================================
        // 5. KELOMPOKKAN BERDASARKAN KATEGORI
        // ============================================
        $kelompokAkun = $this->kelompokkanAkun($hasilAkun);
        
        return [
            'periode' => [
                'tanggal' => $periodeDate,
                'label' => 'Per ' . date('d M Y', strtotime($periodeDate))
            ],
            'akun' => $hasilAkun,
            'kelompok' => $kelompokAkun,
            'total' => [
                'aset' => $totalAset,
                'kewajiban' => $totalKewajiban,
                'ekuitas' => $totalEkuitas,
                'aset_formatted' => $this->formatRupiah($totalAset),
                'kewajiban_formatted' => $this->formatRupiah($totalKewajiban),
                'ekuitas_formatted' => $this->formatRupiah($totalEkuitas)
            ],
            'laba_berjalan' => $labaBerjalan,
            'verifikasi' => $verifikasi,
            'persamaan_akuntansi' => [
                'aset' => $totalAset,
                'kewajiban_ekuitas' => $totalKewajiban + $totalEkuitas,
                'selisih' => $totalAset - ($totalKewajiban + $totalEkuitas),
                'is_seimbang' => abs($totalAset - ($totalKewajiban + $totalEkuitas)) < 1,
                'formula' => $this->formatRupiah($totalAset) . ' = ' . 
                            $this->formatRupiah($totalKewajiban) . ' + ' . 
                            $this->formatRupiah($totalEkuitas)
            ],
            'metadata' => [
                'generated_at' => date('Y-m-d H:i:s'),
                'version' => '1.0.2 - Fixed Neraca'
            ]
        ];
    }
    
    /**
     * HITUNG LABA BERJALAN - VERSI FIX (QUERY LANGSUNG)
     */
    private function hitungLabaBerjalanFix($periodeDate)
    {
        $db = \Config\Database::connect();
        
        // Ambil tanggal awal tahun
        $tahun = date('Y', strtotime($periodeDate));
        $startYear = $tahun . '-01-01';
        
        // =========== HITUNG PENDAPATAN ===========
        $queryPendapatan = $db->table('buku_besar bb')
            ->select('SUM(bb.debit) as total_debit, SUM(bb.kredit) as total_kredit')
            ->join('coa', 'coa.id = bb.coa_id')
            ->where('coa.tipe_akun', 'Pendapatan')
            ->where('coa.is_active', 1)
            ->where('bb.tanggal >=', $startYear)
            ->where('bb.tanggal <=', $periodeDate)
            ->get();
        
        $pendapatan = $queryPendapatan->getRowArray();
        
        // Pendapatan: saldo_normal Kredit, jadi Kredit - Debit
        $totalPendapatan = ($pendapatan['total_kredit'] ?? 0) - ($pendapatan['total_debit'] ?? 0);
        
        // =========== HITUNG BEBAN ===========
        $queryBeban = $db->table('buku_besar bb')
            ->select('SUM(bb.debit) as total_debit, SUM(bb.kredit) as total_kredit')
            ->join('coa', 'coa.id = bb.coa_id')
            ->where('coa.tipe_akun', 'Beban')
            ->where('coa.is_active', 1)
            ->where('bb.tanggal >=', $startYear)
            ->where('bb.tanggal <=', $periodeDate)
            ->get();
        
        $beban = $queryBeban->getRowArray();
        
        // Beban: saldo_normal Debit, jadi Debit - Kredit
        $totalBeban = ($beban['total_debit'] ?? 0) - ($beban['total_kredit'] ?? 0);
        
        // Laba Bersih
        $labaBersih = $totalPendapatan - $totalBeban;
        
        // Untuk debug
        log_message('debug', '===== HITUNG LABA NERACA =====');
        log_message('debug', 'Start Year: ' . $startYear);
        log_message('debug', 'End Date: ' . $periodeDate);
        log_message('debug', 'Total Pendapatan: ' . $totalPendapatan);
        log_message('debug', 'Total Beban: ' . $totalBeban);
        log_message('debug', 'Laba Bersih: ' . $labaBersih);
        
        return [
            'pendapatan_usaha' => $totalPendapatan,
            'total_beban' => $totalBeban,
            'laba_bersih' => $labaBersih,
            'laba_bersih_formatted' => $this->formatRupiah($labaBersih),
            'is_profit' => $labaBersih > 0,
            'is_loss' => $labaBersih < 0
        ];
    }
    
    /**
     * FORMAT RUPIAH DENGAN TANDA NEGATIF
     */
    private function formatRupiah($nilai)
    {
        if ($nilai < 0) {
            return '(Rp ' . number_format(abs($nilai), 0, ',', '.') . ')';
        } else {
            return 'Rp ' . number_format($nilai, 0, ',', '.');
        }
    }
    
    /**
     * GET SALDO AKUN SAMPAI DENGAN TANGGAL TERTENTU
     */
private function getSaldoAkunSampaiTanggal($coaId, $tanggal)
{
    $db = \Config\Database::connect();
    
    $query = $db->table('buku_besar')
        ->select('SUM(debit) as total_debit, SUM(kredit) as total_kredit')
        ->where('coa_id', $coaId)
        ->where('tanggal <=', $tanggal)
        ->get();
    
    $row = $query->getRowArray();
    
    if (!$row) {
        return 0;
    }
    
    $debit = $row['total_debit'] ?? 0;
    $kredit = $row['total_kredit'] ?? 0;
    
    // Dapatkan informasi akun
    $coaModel = new CoaModel();
    $akun = $coaModel->find($coaId);
    
    if (!$akun) {
        return 0;
    }
    
    // ===== PERBAIKAN PENTING =====
    // Hitung saldo berdasarkan saldo_normal
    if ($akun['saldo_normal'] == 'Debit') {
        $saldo = $debit - $kredit;
    } else {
        $saldo = $kredit - $debit;
    }
    
    // ===== PERBAIKAN UNTUK AKUN KONTRA-ASET =====
    // Akun dengan tipe_akun = 'Aset' tapi saldo_normal = 'Kredit' adalah akun kontra-aset
    // Harusnya bernilai NEGATIF di neraca
    if ($akun['tipe_akun'] == 'Aset' && $akun['saldo_normal'] == 'Kredit') {
        // Pastikan nilainya negatif
        return -abs($saldo);
    }
    
    // Untuk akun lainnya, kembalikan sesuai perhitungan
    return $saldo;
}
    
    /**
     * VERIFIKASI PERSAMAAN DASAR AKUNTANSI
     */
    private function verifikasiNeraca($totalAset, $totalKewajiban, $totalEkuitas)
    {
        $kewajibanEkuitas = $totalKewajiban + $totalEkuitas;
        $selisih = $totalAset - $kewajibanEkuitas;
        $isSeimbang = abs($selisih) < 1; // Toleransi 1 rupiah
        
        return [
            'is_seimbang' => $isSeimbang,
            'total_aset' => $totalAset,
            'total_kewajiban_ekuitas' => $kewajibanEkuitas,
            'selisih' => $selisih,
            'selisih_formatted' => $this->formatRupiah($selisih),
            'keterangan' => $isSeimbang ? 
                '✓ NERACA SEIMBANG: Aset = Kewajiban + Ekuitas' :
                '✗ NERACA TIDAK SEIMBANG: Terdapat selisih ' . $this->formatRupiah($selisih),
            'formula' => $this->formatRupiah($totalAset) . ' = ' . 
                        $this->formatRupiah($totalKewajiban) . ' + ' . 
                        $this->formatRupiah($totalEkuitas)
        ];
    }
    
    /**
     * GET LAPORAN NERACA DENGAN DETAIL PER KATEGORI
     */
    public function getLaporanNeracaDetail($periodeDate)
    {
        $neraca = $this->getLaporanNeraca($periodeDate);
        
        // Tambahkan breakdown per kategori
        $detail = [
            'aset_lancar' => $this->filterAkunByKategori($neraca['akun'], 'Aset Lancar'),
            'aset_tetap' => $this->filterAkunByKategori($neraca['akun'], 'Aset Tetap'),
            'aset_lainnya' => $this->filterAkunByKategori($neraca['akun'], 'Aset Lainnya'),
            'kewajiban_lancar' => $this->filterAkunByKategori($neraca['akun'], 'Kewajiban Lancar'),
            'kewajiban_jangka_panjang' => $this->filterAkunByKategori($neraca['akun'], 'Kewajiban Jangka Panjang'),
            'ekuitas' => $this->filterAkunByTipe($neraca['akun'], 'Ekuitas')
        ];
        
        // Hitung subtotal per kategori
        $subtotal = [
            'aset_lancar' => $this->hitungSubtotalKategori($detail['aset_lancar']),
            'aset_tetap' => $this->hitungSubtotalKategori($detail['aset_tetap']),
            'aset_lainnya' => $this->hitungSubtotalKategori($detail['aset_lainnya']),
            'kewajiban_lancar' => $this->hitungSubtotalKategori($detail['kewajiban_lancar']),
            'kewajiban_jangka_panjang' => $this->hitungSubtotalKategori($detail['kewajiban_jangka_panjang']),
            'ekuitas' => $this->hitungSubtotalKategori($detail['ekuitas'])
        ];
        
        $neraca['detail_per_kategori'] = $detail;
        $neraca['subtotal_per_kategori'] = $subtotal;
        
        return $neraca;
    }
    
    /**
     * FILTER AKUN BERDASARKAN KATEGORI
     */
    private function filterAkunByKategori($akun, $kategori)
    {
        $filtered = array_filter($akun, function($item) use ($kategori) {
            return ($item['kategori'] ?? '') == $kategori;
        });
        
        return array_values($filtered);
    }
    
    /**
     * FILTER AKUN BERDASARKAN TIPE
     */
    private function filterAkunByTipe($akun, $tipe)
    {
        $filtered = array_filter($akun, function($item) use ($tipe) {
            return $item['tipe_akun'] == $tipe;
        });
        
        return array_values($filtered);
    }
    
    /**
     * HITUNG SUBTOTAL KATEGORI
     */
    private function hitungSubtotalKategori($akun)
    {
        $total = 0;
        foreach ($akun as $item) {
            $total += $item['saldo'];
        }
        return $total;
    }
    
    /**
     * KELOMPOKKAN AKUN BERDASARKAN KATEGORI
     */
    private function kelompokkanAkun($akun)
    {
        $kelompok = [];
        
        foreach ($akun as $item) {
            $kategori = $item['kategori'] ?? 'Lainnya';
            
            if (!isset($kelompok[$kategori])) {
                $kelompok[$kategori] = [
                    'nama' => $kategori,
                    'akun' => [],
                    'total' => 0
                ];
            }
            
            $kelompok[$kategori]['akun'][] = $item;
            $kelompok[$kategori]['total'] += $item['saldo'];
        }
        
        // Format total per kelompok
        foreach ($kelompok as &$group) {
            $group['total_formatted'] = $this->formatRupiah($group['total']);
        }
        
        return $kelompok;
    }
    
    /**
     * GET NERACA BANDINGAN (COMPARATIVE BALANCE SHEET)
     */
    public function getNeracaBandingan($periode1, $periode2)
    {
        $neraca1 = $this->getLaporanNeraca($periode1);
        $neraca2 = $this->getLaporanNeraca($periode2);
        
        // Gabungkan data untuk perbandingan
        $perbandingan = [];
        
        // Map akun berdasarkan kode_akun
        $mapAkun1 = [];
        foreach ($neraca1['akun'] as $akun) {
            $mapAkun1[$akun['kode_akun']] = $akun;
        }
        
        $mapAkun2 = [];
        foreach ($neraca2['akun'] as $akun) {
            $mapAkun2[$akun['kode_akun']] = $akun;
        }
        
        // Gabungkan semua kode akun
        $allKode = array_unique(array_merge(array_keys($mapAkun1), array_keys($mapAkun2)));
        sort($allKode);
        
        foreach ($allKode as $kode) {
            $akun1 = $mapAkun1[$kode] ?? null;
            $akun2 = $mapAkun2[$kode] ?? null;
            
            $saldo1 = $akun1['saldo'] ?? 0;
            $saldo2 = $akun2['saldo'] ?? 0;
            $perubahan = $saldo2 - $saldo1;
            $persentase = $saldo1 != 0 ? ($perubahan / abs($saldo1)) * 100 : 0;
            
            $perbandingan[] = [
                'kode_akun' => $kode,
                'nama_akun' => $akun1['nama_akun'] ?? $akun2['nama_akun'] ?? '',
                'tipe_akun' => $akun1['tipe_akun'] ?? $akun2['tipe_akun'] ?? '',
                'saldo_periode1' => $saldo1,
                'saldo_periode2' => $saldo2,
                'perubahan' => $perubahan,
                'persentase' => round($persentase, 2),
                'saldo_periode1_formatted' => $this->formatRupiah($saldo1),
                'saldo_periode2_formatted' => $this->formatRupiah($saldo2),
                'perubahan_formatted' => $this->formatRupiah($perubahan)
            ];
        }
        
        return [
            'periode1' => $periode1,
            'periode2' => $periode2,
            'periode1_label' => 'Per ' . date('d M Y', strtotime($periode1)),
            'periode2_label' => 'Per ' . date('d M Y', strtotime($periode2)),
            'data' => $perbandingan,
            'total' => [
                'periode1' => [
                    'aset' => $neraca1['total']['aset'],
                    'kewajiban' => $neraca1['total']['kewajiban'],
                    'ekuitas' => $neraca1['total']['ekuitas']
                ],
                'periode2' => [
                    'aset' => $neraca2['total']['aset'],
                    'kewajiban' => $neraca2['total']['kewajiban'],
                    'ekuitas' => $neraca2['total']['ekuitas']
                ],
                'perubahan' => [
                    'aset' => $neraca2['total']['aset'] - $neraca1['total']['aset'],
                    'kewajiban' => $neraca2['total']['kewajiban'] - $neraca1['total']['kewajiban'],
                    'ekuitas' => $neraca2['total']['ekuitas'] - $neraca1['total']['ekuitas']
                ]
            ],
            'verifikasi_periode1' => $neraca1['verifikasi'],
            'verifikasi_periode2' => $neraca2['verifikasi']
        ];
    }
    
    /**
     * GET RINGKASAN NERACA UNTUK DASHBOARD
     */
    public function getRingkasanNeraca($periodeDate)
    {
        try {
            $neraca = $this->getLaporanNeraca($periodeDate);
            
            return [
                'success' => true,
                'data' => [
                    'total_aset' => $neraca['total']['aset'],
                    'total_aset_formatted' => $neraca['total']['aset_formatted'],
                    'total_kewajiban' => $neraca['total']['kewajiban'],
                    'total_kewajiban_formatted' => $neraca['total']['kewajiban_formatted'],
                    'total_ekuitas' => $neraca['total']['ekuitas'],
                    'total_ekuitas_formatted' => $neraca['total']['ekuitas_formatted'],
                    'laba_berjalan' => $neraca['laba_berjalan']['laba_bersih'] ?? 0,
                    'laba_berjalan_formatted' => $neraca['laba_berjalan']['laba_bersih_formatted'] ?? 'Rp 0',
                    'is_seimbang' => $neraca['verifikasi']['is_seimbang'],
                    'is_seimbang_label' => $neraca['verifikasi']['is_seimbang'] ? 'SEIMBANG' : 'TIDAK SEIMBANG',
                    'periode' => $neraca['periode']['label']
                ]
            ];
        } catch (\Exception $e) {
            log_message('error', 'GetRingkasanNeraca Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'data' => [
                    'total_aset' => 0,
                    'total_aset_formatted' => 'Rp 0',
                    'total_kewajiban' => 0,
                    'total_kewajiban_formatted' => 'Rp 0',
                    'total_ekuitas' => 0,
                    'total_ekuitas_formatted' => 'Rp 0',
                    'laba_berjalan' => 0,
                    'laba_berjalan_formatted' => 'Rp 0',
                    'is_seimbang' => false,
                    'is_seimbang_label' => 'ERROR',
                    'periode' => 'Per ' . date('d M Y', strtotime($periodeDate))
                ]
            ];
        }
    }
    
    /**
     * GET NERACA PER BULAN (UNTUK TREN)
     */
    public function getNeracaPerBulan($tahun = null)
    {
        if ($tahun === null) {
            $tahun = date('Y');
        }
        
        $data = [];
        $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        
        for ($i = 1; $i <= 12; $i++) {
            $tanggal = $tahun . '-' . str_pad($i, 2, '0', STR_PAD_LEFT) . '-' . date('t', strtotime($tahun . '-' . $i . '-01'));
            
            $neraca = $this->getLaporanNeraca($tanggal);
            
            $data[] = [
                'bulan' => $bulan[$i - 1],
                'bulan_angka' => $i,
                'tahun' => $tahun,
                'tanggal' => $tanggal,
                'total_aset' => $neraca['total']['aset'],
                'total_kewajiban' => $neraca['total']['kewajiban'],
                'total_ekuitas' => $neraca['total']['ekuitas'],
                'total_aset_formatted' => $neraca['total']['aset_formatted'],
                'total_kewajiban_formatted' => $neraca['total']['kewajiban_formatted'],
                'total_ekuitas_formatted' => $neraca['total']['ekuitas_formatted'],
                'is_seimbang' => $neraca['verifikasi']['is_seimbang']
            ];
        }
        
        return $data;
    }
    
    /**
     * GET TREN NERACA UNTUK DASHBOARD
     */
    public function getTrenNeraca($tahun = null)
    {
        $data = $this->getNeracaPerBulan($tahun);
        
        $labels = [];
        $aset = [];
        $kewajiban = [];
        $ekuitas = [];
        
        foreach ($data as $item) {
            $labels[] = $item['bulan'];
            $aset[] = $item['total_aset'];
            $kewajiban[] = $item['total_kewajiban'];
            $ekuitas[] = $item['total_ekuitas'];
        }
        
        return [
            'labels' => $labels,
            'aset' => $aset,
            'kewajiban' => $kewajiban,
            'ekuitas' => $ekuitas,
            'data' => $data
        ];
    }
    
    /**
     * DEBUG: CEK DATA NERACA MENTAH
     */
    public function debugNeraca($periodeDate)
    {
        $db = \Config\Database::connect();
        
        // 1. Ambil semua akun aset, kewajiban, ekuitas
        $coaModel = new CoaModel();
        $akuns = $coaModel->where('is_active', 1)
            ->where('is_header', 0)
            ->groupStart()
                ->where('tipe_akun', 'Aset')
                ->orWhere('tipe_akun', 'Kewajiban')
                ->orWhere('tipe_akun', 'Ekuitas')
            ->groupEnd()
            ->orderBy('kode_akun', 'ASC')
            ->findAll();
        
        $akunIds = array_column($akuns, 'id');
        
        // 2. Ambil data buku besar untuk akun-akun tersebut
        $bukuBesar = [];
        if (!empty($akunIds)) {
            $bukuBesar = $db->table('buku_besar')
                ->whereIn('coa_id', $akunIds)
                ->where('tanggal <=', $periodeDate)
                ->orderBy('coa_id', 'ASC')
                ->orderBy('tanggal', 'DESC')
                ->orderBy('id', 'DESC')
                ->get()
                ->getResultArray();
        }
        
        // 3. Hitung saldo per akun
        $saldoPerAkun = [];
        foreach ($akuns as $akun) {
            $saldo = $this->getSaldoAkunSampaiTanggal($akun['id'], $periodeDate);
            
                // Log untuk debugging
    log_message('debug', "Akun: {$akun['kode_akun']} - {$akun['nama_akun']}");
    log_message('debug', "  Tipe: {$akun['tipe_akun']}, Saldo Normal: {$akun['saldo_normal']}");
    log_message('debug', "  Saldo: {$saldo}");
            $saldoPerAkun[$akun['kode_akun']] = [
                'nama' => $akun['nama_akun'],
                'tipe' => $akun['tipe_akun'],
                'kategori' => $akun['kategori'],
                'saldo' => $saldo,
                'saldo_formatted' => $this->formatRupiah($saldo)
            ];
        }
        
        return [
            'periode' => $periodeDate,
            'akun' => $akuns,
            'buku_besar' => $bukuBesar,
            'saldo_per_akun' => $saldoPerAkun,
            'counts' => [
                'total_akun' => count($akuns),
                'total_buku_besar' => count($bukuBesar)
            ]
        ];
    }
    
    /**
     * CLEAR CACHE
     */
    public function clearCache()
    {
        return true;
    }
}