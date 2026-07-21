<?php

namespace App\Models\Accounting;

use CodeIgniter\Model;

class LabaRugiModel extends Model
{
    protected $db;
    
    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }
    
    public function getLaporanLabaRugi(string $startDate, string $endDate): array
    {
        $cacheKey = 'laba_rugi_' . $startDate . '_' . $endDate;
        
        if ($cache = cache($cacheKey)) {
            return $cache;
        }
        
        // 1. PENDAPATAN: SUM(kredit) - SUM(debit)
        $pendapatanRaw = $this->db->table('coa')
            ->select('coa.id as coa_id, coa.kode_akun, coa.nama_akun, (SUM(buku_besar.kredit) - SUM(buku_besar.debit)) as saldo')
            ->join('buku_besar', 'buku_besar.coa_id = coa.id')
            ->where('coa.tipe_akun', 'Pendapatan')
            ->where('buku_besar.tanggal >=', $startDate)
            ->where('buku_besar.tanggal <=', $endDate)
            ->where('buku_besar.status', 'processed')
            ->where('buku_besar.is_void', 0)
            ->groupBy('coa.id, coa.kode_akun, coa.nama_akun')
            ->having('saldo !=', 0)
            ->orderBy('coa.kode_akun', 'ASC')
            ->get()->getResultArray();
            
        $pendapatan = [];
        $totalPendapatan = 0;
        foreach ($pendapatanRaw as $item) {
            $saldo = (float) $item['saldo'];
            $pendapatan[] = [
                'coa_id' => $item['coa_id'],
                'kode_akun' => $item['kode_akun'],
                'nama_akun' => $item['nama_akun'],
                'saldo' => $saldo
            ];
            $totalPendapatan += $saldo;
        }

        // 2. BEBAN: SUM(debit) - SUM(kredit)
        $bebanRaw = $this->db->table('coa')
            ->select('coa.id as coa_id, coa.kode_akun, coa.nama_akun, (SUM(buku_besar.debit) - SUM(buku_besar.kredit)) as saldo')
            ->join('buku_besar', 'buku_besar.coa_id = coa.id')
            ->where('coa.tipe_akun', 'Beban')
            ->where('buku_besar.tanggal >=', $startDate)
            ->where('buku_besar.tanggal <=', $endDate)
            ->where('buku_besar.status', 'processed')
            ->where('buku_besar.is_void', 0)
            ->groupBy('coa.id, coa.kode_akun, coa.nama_akun')
            ->having('saldo !=', 0)
            ->orderBy('coa.kode_akun', 'ASC')
            ->get()->getResultArray();

        $beban = [];
        $totalBeban = 0;
        foreach ($bebanRaw as $item) {
            $saldo = (float) $item['saldo'];
            $beban[] = [
                'coa_id' => $item['coa_id'],
                'kode_akun' => $item['kode_akun'],
                'nama_akun' => $item['nama_akun'],
                'saldo' => $saldo
            ];
            $totalBeban += $saldo;
        }

        // 3. KELOMPOKAN BEBAN AGAR TIDAK ADA YANG TERSEMBUNYI
        $hpp = [];
        $bebanOperasional = [];
        $bebanLain = [];

        foreach ($beban as $item) {
            if (strpos($item['kode_akun'], '5-11') === 0) {
                $hpp[] = $item;
            } elseif (strpos($item['kode_akun'], '5-18') === 0 || strpos($item['kode_akun'], '5-19') === 0) {
                $bebanLain[] = $item;
            } else {
                // Semua beban sisanya otomatis masuk ke Beban Operasional (Catch-all)
                $bebanOperasional[] = $item;
            }
        }

        $totalHpp = array_sum(array_column($hpp, 'saldo'));
        $totalBebanOperasional = array_sum(array_column($bebanOperasional, 'saldo'));
        $totalBebanLain = array_sum(array_column($bebanLain, 'saldo'));

        // 4. PERHITUNGAN LABA
        $labaKotor = $totalPendapatan - $totalHpp;
        $labaOperasional = $labaKotor - $totalBebanOperasional;
        $labaSebelumPajak = $labaOperasional;
        $labaBersihFinal = $labaSebelumPajak - $totalBebanLain;

        $result = [
            'periode' => [
                'start' => $startDate,
                'end' => $endDate,
                'start_label' => date('d F Y', strtotime($startDate)),
                'end_label' => date('d F Y', strtotime($endDate))
            ],
            'pendapatan' => $pendapatan,
            'total_pendapatan' => $totalPendapatan,
            'hpp' => $hpp,
            'total_hpp' => $totalHpp,
            'beban_operasional' => $bebanOperasional,
            'total_beban_operasional' => $totalBebanOperasional,
            'beban_lain' => $bebanLain,
            'total_beban_lain' => $totalBebanLain,
            'laba_kotor' => $labaKotor,
            'laba_operasional' => $labaOperasional,
            'laba_sebelum_pajak' => $labaSebelumPajak,
            'laba_bersih' => $labaBersihFinal,
            'is_profit' => $labaBersihFinal > 0,
            'is_loss' => $labaBersihFinal < 0,
            'is_break_even' => $labaBersihFinal == 0
        ];
        
        cache()->save($cacheKey, $result, 300);
        
        return $result;
    }

    public function getRingkasan(string $startDate, string $endDate): array
    {
        $laporan = $this->getLaporanLabaRugi($startDate, $endDate);
        
        return [
            'total_pendapatan' => $laporan['total_pendapatan'],
            'total_beban' => $laporan['total_hpp'] + $laporan['total_beban_operasional'] + $laporan['total_beban_lain'],
            'laba_kotor' => $laporan['laba_kotor'],
            'laba_bersih' => $laporan['laba_bersih'],
            'is_profit' => $laporan['is_profit'],
            'margin' => $laporan['total_pendapatan'] > 0 
                ? ($laporan['laba_bersih'] / $laporan['total_pendapatan']) * 100 
                : 0
        ];
    }
    
    public function clearCache(string $startDate = null, string $endDate = null): void
    {
        if ($startDate && $endDate) {
            cache()->delete('laba_rugi_' . $startDate . '_' . $endDate);
        }
    }
}