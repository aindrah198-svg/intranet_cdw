<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanLabaRugiModel extends Model
{
    protected $table = 'buku_besar';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    
    /**
     * Get data untuk Laporan Laba Rugi
     */
    public function getLaporanLabaRugi($startDate, $endDate)
    {
        // Ambil semua akun pendapatan dan beban yang aktif
        $coaModel = new CoaModel();
        $akuns = $coaModel->where('is_active', 1)
            ->where('is_header', 0)
            ->groupStart()
                ->where('tipe_akun', 'Pendapatan')
                ->orWhere('tipe_akun', 'Beban')
            ->groupEnd()
            ->orderBy('kode_akun', 'ASC')
            ->findAll();
        
        $results = [];
        $totalPendapatan = 0;
        $totalBeban = 0;
        
        foreach ($akuns as $akun) {
            // Hitung saldo akun untuk periode tertentu
            $saldo = $this->getSaldoAkunPeriode($akun['id'], $startDate, $endDate);
            
            if ($saldo != 0) {
                $dataAkun = [
                    'kode_akun' => $akun['kode_akun'],
                    'nama_akun' => $akun['nama_akun'],
                    'tipe_akun' => $akun['tipe_akun'],
                    'kategori' => $akun['kategori'] ?? '',
                    'saldo_normal' => $akun['saldo_normal'],
                    'saldo_periode' => $saldo // LANGSUNG NILAI ASLI, BUKAN ABS
                ];
                
                if ($akun['tipe_akun'] == 'Pendapatan') {
                    $totalPendapatan += $saldo;
                } else {
                    $totalBeban += $saldo; // PAKAI NILAI ASLI (bisa positif/negatif)
                }
                
                $results[] = $dataAkun;
            }
        }
        
        return [
            'data' => $results,
            'summary' => [
                'total_pendapatan' => $totalPendapatan,
                'total_beban' => $totalBeban,
                'laba_kotor' => $totalPendapatan,
                'laba_operasional' => $totalPendapatan - $totalBeban,
                'laba_bersih' => $totalPendapatan - $totalBeban, // NILAI ASLI
                'periode' => [
                    'start' => $startDate,
                    'end' => $endDate
                ]
            ]
        ];
    }
    
    /**
     * Get saldo akun untuk periode tertentu
     */
    private function getSaldoAkunPeriode($coaId, $startDate, $endDate)
    {
        $db = \Config\Database::connect();
        
        // Hitung saldo awal (sebelum periode)
        $saldoAwal = $this->getSaldoAwal($coaId, $startDate);
        
        // Hitung mutasi selama periode
        $builder = $db->table('buku_besar')
            ->select('SUM(debit) as total_debit, SUM(kredit) as total_kredit')
            ->where('coa_id', $coaId)
            ->where('tanggal >=', $startDate)
            ->where('tanggal <=', $endDate);
        
        $mutasi = $builder->get()->getRowArray();
        
        $totalDebit = (float)($mutasi['total_debit'] ?? 0);
        $totalKredit = (float)($mutasi['total_kredit'] ?? 0);
        
        // Ambil info akun untuk menentukan saldo normal
        $coaModel = new CoaModel();
        $akun = $coaModel->find($coaId);
        
        if (!$akun) {
            return 0;
        }
        
        // Hitung saldo akhir periode berdasarkan saldo normal
        if ($akun['saldo_normal'] == 'Debit') {
            return $saldoAwal + $totalDebit - $totalKredit;
        } else {
            return $saldoAwal + $totalKredit - $totalDebit;
        }
    }
    
    /**
     * Get saldo awal sebelum periode
     */
    private function getSaldoAwal($coaId, $startDate)
    {
        $db = \Config\Database::connect();
        
        // Ambil saldo terakhir sebelum tanggal mulai
        $builder = $db->table('buku_besar')
            ->select('saldo_akhir')
            ->where('coa_id', $coaId)
            ->where('tanggal <', $startDate)
            ->orderBy('tanggal', 'DESC')
            ->orderBy('id', 'DESC')
            ->limit(1);
        
        $result = $builder->get()->getRowArray();
        
        return $result ? (float)$result['saldo_akhir'] : 0;
    }
    
    /**
     * Get laporan laba rugi multi-step (lebih detail)
     */
    public function getLaporanLabaRugiMultiStep($startDate, $endDate)
    {
        $db = \Config\Database::connect();
        
        // Kategori akun berdasarkan kode akun
        $kategoriAkun = $this->getKategoriAkun();
        
        $results = [
            'pendapatan_usaha' => [],
            'harga_pokok_penjualan' => [],
            'beban_penjualan' => [],
            'beban_administrasi' => [],
            'pendapatan_lain' => [],
            'beban_lain' => [],
            'beban_pajak' => []
        ];
        
        $totals = [
            'total_pendapatan_usaha' => 0,
            'total_hpp' => 0,
            'total_beban_penjualan' => 0,
            'total_beban_administrasi' => 0,
            'total_beban_operasional' => 0,
            'total_pendapatan_lain' => 0,
            'total_beban_lain' => 0,
            'total_beban_pajak' => 0
        ];
        
        // Ambil semua akun yang relevan
        $coaModel = new CoaModel();
        $akuns = $coaModel->where('is_active', 1)
            ->where('is_header', 0)
            ->groupStart()
                ->where('tipe_akun', 'Pendapatan')
                ->orWhere('tipe_akun', 'Beban')
            ->groupEnd()
            ->findAll();
        
        foreach ($akuns as $akun) {
            $saldo = $this->getSaldoAkunPeriode($akun['id'], $startDate, $endDate);
            
            if ($saldo != 0) {
                $kategori = $this->determineKategori($akun['kode_akun'], $akun['nama_akun'], $akun['kategori']);
                
                $item = [
                    'kode_akun' => $akun['kode_akun'],
                    'nama_akun' => $akun['nama_akun'],
                    'saldo' => $saldo, // NILAI ASLI, BUKAN ABS
                    'tipe_saldo' => $saldo >= 0 ? 'Kredit' : 'Debit'
                ];
                
                switch ($kategori) {
                    case 'pendapatan_usaha':
                        $results['pendapatan_usaha'][] = $item;
                        $totals['total_pendapatan_usaha'] += $saldo;
                        break;
                    case 'hpp':
                        $results['harga_pokok_penjualan'][] = $item;
                        $totals['total_hpp'] += $saldo; // PAKAI NILAI ASLI
                        break;
                    case 'beban_penjualan':
                        $results['beban_penjualan'][] = $item;
                        $totals['total_beban_penjualan'] += $saldo; // PAKAI NILAI ASLI
                        break;
                    case 'beban_administrasi':
                        $results['beban_administrasi'][] = $item;
                        $totals['total_beban_administrasi'] += $saldo; // PAKAI NILAI ASLI
                        break;
                    case 'pendapatan_lain':
                        $results['pendapatan_lain'][] = $item;
                        $totals['total_pendapatan_lain'] += $saldo;
                        break;
                    case 'beban_lain':
                        $results['beban_lain'][] = $item;
                        $totals['total_beban_lain'] += $saldo; // PAKAI NILAI ASLI
                        break;
                    case 'beban_pajak':
                        $results['beban_pajak'][] = $item;
                        $totals['total_beban_pajak'] += $saldo; // PAKAI NILAI ASLI
                        break;
                }
            }
        }
        
        // Hitung totals - PASTIKAN SEMUA MENGGUNAKAN NILAI ASLI
        $totals['total_beban_operasional'] = $totals['total_beban_penjualan'] + $totals['total_beban_administrasi'];
        
        // Hitung laba rugi dengan nilai asli
        $laba_kotor = $totals['total_pendapatan_usaha'] - $totals['total_hpp'];
        $laba_operasional = $laba_kotor - $totals['total_beban_operasional'];
        $laba_sebelum_pajak = $laba_operasional + $totals['total_pendapatan_lain'] - $totals['total_beban_lain'];
        $laba_bersih = $laba_sebelum_pajak - $totals['total_beban_pajak'];
        
        return [
            'data' => $results,
            'totals' => $totals,
            'laba_rugi' => [
                'laba_kotor' => $laba_kotor,
                'laba_operasional' => $laba_operasional,
                'laba_sebelum_pajak' => $laba_sebelum_pajak,
                'laba_bersih' => $laba_bersih, // NILAI ASLI (bisa negatif)
                'rugi_bersih' => $laba_bersih < 0 ? abs($laba_bersih) : 0,
                'is_profit' => $laba_bersih > 0,
                'is_loss' => $laba_bersih < 0
            ],
            'periode' => [
                'start' => $startDate,
                'end' => $endDate
            ]
        ];
    }
    
    /**
     * Determine kategori akun berdasarkan kode, nama, dan kategori
     */
    private function determineKategori($kodeAkun, $namaAkun, $kategori)
    {
        $nama = strtolower($namaAkun);
        
        // 1. PENDAPATAN USAHA
        if (strpos($kodeAkun, '4') === 0 && strpos($nama, 'penjualan') !== false) {
            return 'pendapatan_usaha';
        }
        
        // 2. HARGA POKOK PENJUALAN (HPP)
        if (strpos($kodeAkun, '5') === 0 && 
            (strpos($nama, 'hpp') !== false || 
             strpos($nama, 'harga pokok') !== false ||
             strpos($nama, 'bahan baku') !== false ||
             strpos($nama, 'produksi') !== false)) {
            return 'hpp';
        }
        
        // 3. BEBAN PENJUALAN
        if (strpos($kategori, 'Penjualan') !== false || 
            strpos($nama, 'marketing') !== false ||
            strpos($nama, 'promosi') !== false ||
            strpos($nama, 'iklan') !== false) {
            return 'beban_penjualan';
        }
        
        // 4. BEBAN ADMINISTRASI
        if (strpos($kategori, 'Operasional') !== false ||
            strpos($kategori, 'Administrasi') !== false ||
            strpos($nama, 'gaji') !== false ||
            strpos($nama, 'sewa') !== false ||
            strpos($nama, 'listrik') !== false ||
            strpos($nama, 'pelatihan') !== false ||
            strpos($nama, 'kantor') !== false ||
            strpos($nama, 'administrasi') !== false) {
            return 'beban_administrasi';
        }
        
        // 5. PENDAPATAN LAIN-LAIN
        if (strpos($kodeAkun, '4') === 0 && 
            !strpos($nama, 'penjualan') && 
            !strpos($nama, 'jasa')) {
            return 'pendapatan_lain';
        }
        
        // 6. BEBAN PAJAK
        if (strpos($kodeAkun, '5-18') === 0 || strpos($nama, 'pajak') !== false) {
            return 'beban_pajak';
        }
        
        // 7. Default untuk beban lainnya
        if (strpos($kodeAkun, '5') === 0 || strpos($kodeAkun, '6') === 0) {
            return 'beban_lain';
        }
        
        return 'beban_lain';
    }
    
    /**
     * Get kategori akun mapping
     */
    private function getKategoriAkun()
    {
        return [
            'pendapatan_usaha' => [
                'name' => 'Pendapatan Usaha',
                'codes' => ['41'],
                'keywords' => ['penjualan', 'jasa', 'service', 'revenue']
            ],
            'hpp' => [
                'name' => 'Harga Pokok Penjualan',
                'codes' => ['51', '52'],
                'keywords' => ['hpp', 'harga pokok', 'cost of goods sold', 'cogs']
            ],
            'beban_penjualan' => [
                'name' => 'Beban Penjualan',
                'codes' => ['61'],
                'keywords' => ['penjualan', 'sales', 'marketing', 'iklan', 'promosi']
            ],
            'beban_administrasi' => [
                'name' => 'Beban Administrasi & Umum',
                'codes' => ['62', '63'],
                'keywords' => ['administrasi', 'umum', 'gaji', 'sewa', 'listrik', 'kantor']
            ],
            'pendapatan_lain' => [
                'name' => 'Pendapatan Lain-lain',
                'codes' => ['42', '43', '44'],
                'keywords' => ['bunga', 'dividen', 'lain-lain']
            ],
            'beban_lain' => [
                'name' => 'Beban Lain-lain',
                'codes' => ['64', '65', '66'],
                'keywords' => ['bunga', 'kerugian', 'lain-lain']
            ],
            'beban_pajak' => [
                'name' => 'Beban Pajak',
                'codes' => ['67'],
                'keywords' => ['pajak', 'tax']
            ]
        ];
    }
    
    /**
     * Get summary laba rugi untuk dashboard
     */
    public function getSummaryLabaRugi($startDate, $endDate)
    {
        $laporan = $this->getLaporanLabaRugiMultiStep($startDate, $endDate);
        
        $labaBersih = $laporan['laba_rugi']['laba_bersih'];
        $totalBeban = $laporan['totals']['total_beban_operasional'] + 
                     $laporan['totals']['total_beban_lain'] + 
                     $laporan['totals']['total_beban_pajak'];
        
        return [
            'pendapatan_usaha' => $laporan['totals']['total_pendapatan_usaha'],
            'total_beban' => $totalBeban,
            'laba_kotor' => $laporan['laba_rugi']['laba_kotor'],
            'laba_operasional' => $laporan['laba_rugi']['laba_operasional'],
            'laba_bersih' => $labaBersih, // NILAI ASLI (bisa positif/negatif)
            'is_profit' => $labaBersih > 0,
            'is_loss' => $labaBersih < 0,
            'is_break_even' => $labaBersih == 0,
            'profit_status' => $labaBersih > 0 ? 'LABA' : ($labaBersih < 0 ? 'RUGI' : 'BREAK EVEN'),
            'margin_laba' => $laporan['totals']['total_pendapatan_usaha'] != 0 ? 
                            ($labaBersih / abs($laporan['totals']['total_pendapatan_usaha'])) * 100 : 0
        ];
    }
    
    /**
     * Export laporan laba rugi ke Excel/CSV
     */
    public function exportLaporanLabaRugi($startDate, $endDate, $format = 'multi')
    {
        if ($format == 'multi') {
            $data = $this->getLaporanLabaRugiMultiStep($startDate, $endDate);
        } else {
            $data = $this->getLaporanLabaRugi($startDate, $endDate);
        }
        
        return $data;
    }
}