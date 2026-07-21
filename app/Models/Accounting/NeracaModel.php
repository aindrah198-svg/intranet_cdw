<?php

namespace App\Models\Accounting;

use CodeIgniter\Model;

class NeracaModel extends Model
{
    protected $db;
    
    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }
    
    public function getNeraca(string $tanggalPeriode): array
    {
        $cacheKey = 'neraca_' . $tanggalPeriode;
        
        if ($cache = cache($cacheKey)) {
            return $cache;
        }
        
        // ============================================
        // 1. HITUNG LABA BERSIH TAHUN BERJALAN
        // ============================================
        $tahunMulai = date('Y-01-01', strtotime($tanggalPeriode));
        
        $pendapatanResult = $this->db->table('coa')
            ->select('(SUM(buku_besar.kredit) - SUM(buku_besar.debit)) as saldo')
            ->join('buku_besar', 'buku_besar.coa_id = coa.id')
            ->where('coa.tipe_akun', 'Pendapatan')
            ->where('buku_besar.tanggal >=', $tahunMulai)
            ->where('buku_besar.tanggal <=', $tanggalPeriode)
            ->where('buku_besar.status', 'processed')
            ->where('buku_besar.is_void', 0)
            ->get()->getRowArray();
        $totalPendapatan = (float) ($pendapatanResult['saldo'] ?? 0);
        
        $bebanResult = $this->db->table('coa')
            ->select('(SUM(buku_besar.debit) - SUM(buku_besar.kredit)) as saldo')
            ->join('buku_besar', 'buku_besar.coa_id = coa.id')
            ->where('coa.tipe_akun', 'Beban')
            ->where('buku_besar.tanggal >=', $tahunMulai)
            ->where('buku_besar.tanggal <=', $tanggalPeriode)
            ->where('buku_besar.status', 'processed')
            ->where('buku_besar.is_void', 0)
            ->get()->getRowArray();
        $totalBeban = (float) ($bebanResult['saldo'] ?? 0);
        
        $labaBersih = $totalPendapatan - $totalBeban;
        
        // ============================================
        // 2. BANGUN DATA NERACA MURNI DARI BUKU BESAR
        // ============================================
        
        $data = [
            'periode' => [
                'tanggal' => $tanggalPeriode,
                'label' => date('d F Y', strtotime($tanggalPeriode))
            ],
            'asets' => [],
            'kewajibans' => [],
            'ekuitas' => [],
            'total' => [
                'aset' => 0,
                'kewajiban' => 0,
                'ekuitas' => 0
            ],
            'laba_bersih' => $labaBersih,
            'laba_bersih_formatted' => $this->formatRupiah($labaBersih),
            'is_profit' => $labaBersih >= 0,
            'total_pendapatan' => $totalPendapatan,
            'total_beban' => $totalBeban
        ];
        
        // ASET (Debit - Kredit)
        $asetQuery = $this->db->table('coa')
            ->select('coa.id, coa.kode_akun, coa.nama_akun, coa.tipe_akun, coa.kategori, coa.saldo_normal, (SUM(buku_besar.debit) - SUM(buku_besar.kredit)) as saldo')
            ->join('buku_besar', 'buku_besar.coa_id = coa.id')
            ->where('coa.tipe_akun', 'Aset')
            ->where('buku_besar.tanggal <=', $tanggalPeriode)
            ->where('buku_besar.status', 'processed')
            ->where('buku_besar.is_void', 0)
            ->groupBy('coa.id, coa.kode_akun, coa.nama_akun, coa.tipe_akun, coa.kategori, coa.saldo_normal')
            ->orderBy('coa.kode_akun', 'ASC')
            ->get()->getResultArray();

        foreach ($asetQuery as $coa) {
            $saldo = (float) $coa['saldo'];
            if (abs($saldo) < 1) continue;
            
            $data['asets'][] = [
                'id' => $coa['id'],
                'kode_akun' => $coa['kode_akun'],
                'nama_akun' => $coa['nama_akun'],
                'tipe_akun' => $coa['tipe_akun'],
                'kategori' => $coa['kategori'] ?? $this->getKategoriFromKode($coa['kode_akun']),
                'saldo_normal' => $coa['saldo_normal'],
                'saldo' => $saldo,
                'saldo_formatted' => $this->formatRupiah($saldo)
            ];
            $data['total']['aset'] += $saldo;
        }

        // KEWAJIBAN (Kredit - Debit)
        $kewajibanQuery = $this->db->table('coa')
            ->select('coa.id, coa.kode_akun, coa.nama_akun, coa.tipe_akun, coa.kategori, coa.saldo_normal, (SUM(buku_besar.kredit) - SUM(buku_besar.debit)) as saldo')
            ->join('buku_besar', 'buku_besar.coa_id = coa.id')
            ->where('coa.tipe_akun', 'Kewajiban')
            ->where('buku_besar.tanggal <=', $tanggalPeriode)
            ->where('buku_besar.status', 'processed')
            ->where('buku_besar.is_void', 0)
            ->groupBy('coa.id, coa.kode_akun, coa.nama_akun, coa.tipe_akun, coa.kategori, coa.saldo_normal')
            ->orderBy('coa.kode_akun', 'ASC')
            ->get()->getResultArray();

        foreach ($kewajibanQuery as $coa) {
            $saldo = (float) $coa['saldo'];
            if (abs($saldo) < 1) continue;
            
            $data['kewajibans'][] = [
                'id' => $coa['id'],
                'kode_akun' => $coa['kode_akun'],
                'nama_akun' => $coa['nama_akun'],
                'tipe_akun' => $coa['tipe_akun'],
                'kategori' => $coa['kategori'] ?? $this->getKategoriFromKode($coa['kode_akun']),
                'saldo_normal' => $coa['saldo_normal'],
                'saldo' => $saldo,
                'saldo_formatted' => $this->formatRupiah($saldo)
            ];
            $data['total']['kewajiban'] += $saldo;
        }

        // EKUITAS (Kredit - Debit)
        $ekuitasQuery = $this->db->table('coa')
            ->select('coa.id, coa.kode_akun, coa.nama_akun, coa.tipe_akun, coa.kategori, coa.saldo_normal, (SUM(buku_besar.kredit) - SUM(buku_besar.debit)) as saldo')
            ->join('buku_besar', 'buku_besar.coa_id = coa.id')
            ->where('coa.tipe_akun', 'Ekuitas')
            ->where('buku_besar.tanggal <=', $tanggalPeriode)
            ->where('buku_besar.status', 'processed')
            ->where('buku_besar.is_void', 0)
            ->groupBy('coa.id, coa.kode_akun, coa.nama_akun, coa.tipe_akun, coa.kategori, coa.saldo_normal')
            ->orderBy('coa.kode_akun', 'ASC')
            ->get()->getResultArray();

        foreach ($ekuitasQuery as $coa) {
            $saldo = (float) $coa['saldo'];
            // Ekuitas tidak di-skip meskipun saldo 0
            
            $data['ekuitas'][] = [
                'id' => $coa['id'],
                'kode_akun' => $coa['kode_akun'],
                'nama_akun' => $coa['nama_akun'],
                'tipe_akun' => $coa['tipe_akun'],
                'kategori' => $coa['kategori'] ?? $this->getKategoriFromKode($coa['kode_akun']),
                'saldo_normal' => $coa['saldo_normal'],
                'saldo' => $saldo,
                'saldo_formatted' => $this->formatRupiah($saldo)
            ];
            $data['total']['ekuitas'] += $saldo;
        }
        
        // ============================================
        // 3. TAMBAHKAN LABA BERSIH KE EKUITAS
        // ============================================
        // Selalu tambahkan agar ada row Laba di Laporan
        $data['ekuitas'][] = [
            'id' => null,
            'kode_akun' => 'LABA',
            'nama_akun' => 'Laba Tahun Berjalan',
            'tipe_akun' => 'Ekuitas',
            'kategori' => 'Ekuitas',
            'saldo_normal' => 'Kredit',
            'saldo' => $labaBersih,
            'saldo_formatted' => $this->formatRupiah($labaBersih),
            'is_laba_berjalan' => true
        ];
        $data['total']['ekuitas'] += $labaBersih;
        
        // ============================================
        // 4. HITUNG VERIFIKASI
        // ============================================
        $totalKewajibanEkuitas = $data['total']['kewajiban'] + $data['total']['ekuitas'];
        $selisih = $data['total']['aset'] - $totalKewajibanEkuitas;
        
        $data['verifikasi'] = [
            'is_seimbang' => abs($selisih) < 1,
            'selisih' => $selisih,
            'selisih_formatted' => $this->formatRupiah($selisih),
            'total_aset' => $data['total']['aset'],
            'total_kewajiban' => $data['total']['kewajiban'],
            'total_ekuitas' => $data['total']['ekuitas'],
            'total_pasiva' => $totalKewajibanEkuitas,
            'formula' => $this->formatRupiah($data['total']['aset']) . ' = ' . 
                        $this->formatRupiah($data['total']['kewajiban']) . ' + ' . 
                        $this->formatRupiah($data['total']['ekuitas'])
        ];
        
        // ============================================
        // 5. KELOMPOKKAN ASET BERDASARKAN KATEGORI
        // ============================================
        $data['aset_lancar'] = array_values(array_filter($data['asets'], function($item) {
            $kategori = $item['kategori'] ?? '';
            $kode = $item['kode_akun'] ?? '';
            return strpos($kategori, 'Aset Lancar') !== false || 
                   strpos($kode, '1-1') === 0;
        }));
        
        $data['aset_tetap'] = array_values(array_filter($data['asets'], function($item) {
            $kategori = $item['kategori'] ?? '';
            $kode = $item['kode_akun'] ?? '';
            return strpos($kategori, 'Aset Tetap') !== false || 
                   (strpos($kode, '1-2') === 0) ||
                   (strpos($kode, '1-3') === 0 && $kategori != 'Aset Lancar');
        }));
        
        $data['aset_lainnya'] = array_values(array_filter($data['asets'], function($item) {
            $kategori = $item['kategori'] ?? '';
            $kode = $item['kode_akun'] ?? '';
            return strpos($kategori, 'Aset Lainnya') !== false || 
                   (strpos($kode, '1-') === 0 && 
                    strpos($kode, '1-1') !== 0 && 
                    strpos($kode, '1-2') !== 0 &&
                    strpos($kode, '1-3') !== 0);
        }));
        
        $data['kewajiban_lancar'] = array_values(array_filter($data['kewajibans'], function($item) {
            $kategori = $item['kategori'] ?? '';
            $kode = $item['kode_akun'] ?? '';
            return strpos($kategori, 'Kewajiban Lancar') !== false || 
                   strpos($kode, '2-1') === 0;
        }));
        
        $data['kewajiban_jangka_panjang'] = array_values(array_filter($data['kewajibans'], function($item) {
            $kategori = $item['kategori'] ?? '';
            $kode = $item['kode_akun'] ?? '';
            return strpos($kategori, 'Kewajiban Jangka Panjang') !== false || 
                   strpos($kode, '2-2') === 0;
        }));
        
        // Hitung subtotal per kategori
        $data['subtotal'] = [
            'aset_lancar' => array_sum(array_column($data['aset_lancar'], 'saldo')),
            'aset_tetap' => array_sum(array_column($data['aset_tetap'], 'saldo')),
            'aset_lainnya' => array_sum(array_column($data['aset_lainnya'], 'saldo')),
            'kewajiban_lancar' => array_sum(array_column($data['kewajiban_lancar'], 'saldo')),
            'kewajiban_jangka_panjang' => array_sum(array_column($data['kewajiban_jangka_panjang'], 'saldo'))
        ];
        
        // Format semua nilai rupiah di subtotal
        foreach ($data['subtotal'] as $key => $value) {
            $data['subtotal_formatted'][$key] = $this->formatRupiah($value);
        }
        
        $data['total_formatted'] = [
            'aset' => $this->formatRupiah($data['total']['aset']),
            'kewajiban' => $this->formatRupiah($data['total']['kewajiban']),
            'ekuitas' => $this->formatRupiah($data['total']['ekuitas'])
        ];
        
        cache()->save($cacheKey, $data, 300);
        
        return $data;
    }
    
    private function getKategoriFromKode($kodeAkun)
    {
        if (empty($kodeAkun)) return '';
        if (strpos($kodeAkun, '1-1') === 0) return 'Aset Lancar';
        if (strpos($kodeAkun, '1-2') === 0) return 'Aset Tetap';
        if (strpos($kodeAkun, '2-1') === 0) return 'Kewajiban Lancar';
        if (strpos($kodeAkun, '2-2') === 0) return 'Kewajiban Jangka Panjang';
        if (strpos($kodeAkun, '3-') === 0) return 'Ekuitas';
        return '';
    }
    
    private function formatRupiah($nilai): string
    {
        $nilai = (float) $nilai;
        if ($nilai < 0) {
            return '(Rp ' . number_format(abs($nilai), 0, ',', '.') . ')';
        }
        return 'Rp ' . number_format($nilai, 0, ',', '.');
    }
    
    public function clearCache(string $tanggalPeriode = null): void
    {
        if ($tanggalPeriode) {
            cache()->delete('neraca_' . $tanggalPeriode);
        }
    }
    
    public function getRingkasanNeraca(string $tanggalPeriode): array
    {
        $neraca = $this->getNeraca($tanggalPeriode);
        
        return [
            'total_aset' => $neraca['total']['aset'],
            'total_aset_formatted' => $neraca['total_formatted']['aset'],
            'total_kewajiban' => $neraca['total']['kewajiban'],
            'total_kewajiban_formatted' => $neraca['total_formatted']['kewajiban'],
            'total_ekuitas' => $neraca['total']['ekuitas'],
            'total_ekuitas_formatted' => $neraca['total_formatted']['ekuitas'],
            'laba_bersih' => $neraca['laba_bersih'],
            'laba_bersih_formatted' => $neraca['laba_bersih_formatted'],
            'is_seimbang' => $neraca['verifikasi']['is_seimbang'],
            'periode' => $neraca['periode']['label']
        ];
    }
}