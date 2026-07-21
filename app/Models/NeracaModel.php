<?php

namespace App\Models\Accounting;

use CodeIgniter\Model;

class NeracaModel extends Model
{
    protected $table = 'buku_besar';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = false;

    protected $allowedFields = [];

    /**
     * GET LAPORAN NERACA
     * Berdasarkan saldo_akhir di tabel buku_besar
     * Format: Staffel (Aset - Kewajiban - Ekuitas)
     * 
     * @param string $tanggalPeriode Tanggal periode neraca (Y-m-d)
     * @return array
     */
    public function getNeraca(string $tanggalPeriode): array
    {
        // Gunakan cache agar tidak query berulang
        $cacheKey = 'neraca_' . $tanggalPeriode;
        
        if ($cache = cache($cacheKey)) {
            return $cache;
        }
        
        // ============================================
        // 1. AMBIL SEMUA AKUN DARI COA YANG AKTIF
        // ============================================
        $coaModel = model('CoaModel');
        
        $allCoa = $coaModel
            ->where('is_active', 1)
            ->orderBy('kode_akun', 'ASC')
            ->findAll();
        
        // ============================================
        // 2. AMBIL SALDO AKHIR DARI BUKU BESAR
        // ============================================
        // Gunakan query builder dengan prefix tabel yang jelas
        $builder = $this->db->table('buku_besar');
        $builder->select('buku_besar.coa_id, buku_besar.saldo_akhir, coa.kode_akun, coa.nama_akun, coa.tipe_akun, coa.kategori, coa.saldo_normal');
        $builder->join('coa', 'coa.id = buku_besar.coa_id');
        $builder->where('buku_besar.tanggal <=', $tanggalPeriode);
        $builder->where('buku_besar.status', 'processed');
        $builder->where('buku_besar.is_void', 0);
        
        // Untuk mengambil saldo terakhir per akun, kita perlu subquery
        // Buat subquery untuk mendapatkan tanggal terakhir per coa_id
        $subquery = $this->db->table('buku_besar bb')
            ->select('bb.coa_id, MAX(bb.tanggal) as max_tanggal')
            ->where('bb.tanggal <=', $tanggalPeriode)
            ->where('bb.status', 'processed')
            ->where('bb.is_void', 0)
            ->groupBy('bb.coa_id')
            ->getCompiledSelect();
        
        // Join dengan subquery
        $builder->join("($subquery) as bb2", "bb2.coa_id = buku_besar.coa_id AND bb2.max_tanggal = buku_besar.tanggal", 'inner');
        
        $saldoAkun = $builder->get()->getResultArray();
        
        // Konversi ke array indexed by coa_id
        $saldoByCoa = [];
        foreach ($saldoAkun as $saldo) {
            $saldoByCoa[$saldo['coa_id']] = $saldo;
        }
        
        // ============================================
        // 3. HITUNG LABA BERSIH TAHUN BERJALAN
        // ============================================
        $tahunMulai = date('Y-01-01', strtotime($tanggalPeriode));
        
        // Total Pendapatan (tipe_akun = 'Pendapatan', saldo_normal = 'Kredit')
        $pendapatan = $this->db->table('buku_besar bb')
            ->select('SUM(bb.kredit) as total_kredit, SUM(bb.debit) as total_debit')
            ->join('coa c', 'c.id = bb.coa_id')
            ->where('c.tipe_akun', 'Pendapatan')
            ->where('bb.tanggal >=', $tahunMulai)
            ->where('bb.tanggal <=', $tanggalPeriode)
            ->where('bb.status', 'processed')
            ->where('bb.is_void', 0)
            ->get()
            ->getRow();
        
        $totalPendapatan = ($pendapatan->total_kredit ?? 0) - ($pendapatan->total_debit ?? 0);
        
        // Total Beban (tipe_akun = 'Beban', saldo_normal = 'Debit')
        $beban = $this->db->table('buku_besar bb')
            ->select('SUM(bb.debit) as total_debit, SUM(bb.kredit) as total_kredit')
            ->join('coa c', 'c.id = bb.coa_id')
            ->where('c.tipe_akun', 'Beban')
            ->where('bb.tanggal >=', $tahunMulai)
            ->where('bb.tanggal <=', $tanggalPeriode)
            ->where('bb.status', 'processed')
            ->where('bb.is_void', 0)
            ->get()
            ->getRow();
        
        $totalBeban = ($beban->total_debit ?? 0) - ($beban->total_kredit ?? 0);
        
        $labaBersih = $totalPendapatan - $totalBeban;
        
        // ============================================
        // 4. BANGUN DATA NERACA PER KATEGORI
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
            'is_profit' => $labaBersih >= 0
        ];
        
        // Proses setiap akun COA
        foreach ($allCoa as $coa) {
            // Skip header akun (is_header = 1)
            if ($coa['is_header'] == 1) {
                continue;
            }
            
            // Ambil saldo dari buku besar
            $saldo = $saldoByCoa[$coa['id']]['saldo_akhir'] ?? 0;
            
            // Skip jika saldo 0 (kecuali ekuitas)
            if (abs($saldo) < 1 && $coa['tipe_akun'] != 'Ekuitas') {
                continue;
            }
            
            $akunData = [
                'id' => $coa['id'],
                'kode_akun' => $coa['kode_akun'],
                'nama_akun' => $coa['nama_akun'],
                'tipe_akun' => $coa['tipe_akun'],
                'kategori' => $coa['kategori'],
                'saldo_normal' => $coa['saldo_normal'],
                'saldo' => $saldo,
                'saldo_formatted' => $this->formatRupiah($saldo)
            ];
            
            // Kelompokkan berdasarkan tipe akun
            switch ($coa['tipe_akun']) {
                case 'Aset':
                    $data['asets'][] = $akunData;
                    $data['total']['aset'] += $saldo;
                    break;
                    
                case 'Kewajiban':
                    $data['kewajibans'][] = $akunData;
                    $data['total']['kewajiban'] += $saldo;
                    break;
                    
                case 'Ekuitas':
                    $data['ekuitas'][] = $akunData;
                    $data['total']['ekuitas'] += $saldo;
                    break;
                    
                default:
                    // Pendapatan dan Beban tidak masuk neraca
                    break;
            }
        }
        
        // ============================================
        // 5. TAMBAHKAN LABA BERSIH KE EKUITAS
        // ============================================
        if ($labaBersih != 0) {
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
        }
        
        // ============================================
        // 6. HITUNG VERIFIKASI
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
        // 7. KELOMPOKKAN ASET BERDASARKAN KATEGORI
        // ============================================
        $data['aset_lancar'] = array_values(array_filter($data['asets'], function($item) {
            return strpos($item['kategori'], 'Aset Lancar') !== false || 
                   strpos($item['kode_akun'], '1-1') === 0;
        }));
        
        $data['aset_tetap'] = array_values(array_filter($data['asets'], function($item) {
            return strpos($item['kategori'], 'Aset Tetap') !== false || 
                   strpos($item['kode_akun'], '1-2') === 0;
        }));
        
        $data['aset_lainnya'] = array_values(array_filter($data['asets'], function($item) {
            return strpos($item['kategori'], 'Aset Lainnya') !== false || 
                   (strpos($item['kode_akun'], '1-') === 0 && 
                    strpos($item['kode_akun'], '1-1') !== 0 && 
                    strpos($item['kode_akun'], '1-2') !== 0);
        }));
        
        $data['kewajiban_lancar'] = array_values(array_filter($data['kewajibans'], function($item) {
            return strpos($item['kategori'], 'Kewajiban Lancar') !== false || 
                   strpos($item['kode_akun'], '2-1') === 0;
        }));
        
        $data['kewajiban_jangka_panjang'] = array_values(array_filter($data['kewajibans'], function($item) {
            return strpos($item['kategori'], 'Kewajiban Jangka Panjang') !== false || 
                   strpos($item['kode_akun'], '2-2') === 0;
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
        
        // Simpan cache (5 menit)
        cache()->save($cacheKey, $data, 300);
        
        return $data;
    }
    
    /**
     * GET SALDO AKUN
     * Untuk debugging
     */
    public function getSaldoAkun(int $coaId, string $tanggalPeriode): float
    {
        $builder = $this->db->table('buku_besar');
        $builder->select('saldo_akhir');
        $builder->where('coa_id', $coaId);
        $builder->where('tanggal <=', $tanggalPeriode);
        $builder->where('status', 'processed');
        $builder->where('is_void', 0);
        $builder->orderBy('tanggal', 'DESC');
        $builder->orderBy('id', 'DESC');
        $builder->limit(1);
        
        $result = $builder->get()->getRow();
        
        return $result->saldo_akhir ?? 0;
    }
    
    /**
     * GET NERACA BANDINGAN (Comparative)
     */
    public function getNeracaBandingan(string $periode1, string $periode2): array
    {
        $neraca1 = $this->getNeraca($periode1);
        $neraca2 = $this->getNeraca($periode2);
        
        $perubahanAset = $neraca2['total']['aset'] - $neraca1['total']['aset'];
        $perubahanKewajiban = $neraca2['total']['kewajiban'] - $neraca1['total']['kewajiban'];
        $perubahanEkuitas = $neraca2['total']['ekuitas'] - $neraca1['total']['ekuitas'];
        
        return [
            'periode1' => [
                'tanggal' => $periode1,
                'label' => date('d M Y', strtotime($periode1)),
                'total_aset' => $neraca1['total']['aset'],
                'total_aset_formatted' => $neraca1['total_formatted']['aset'],
                'total_kewajiban' => $neraca1['total']['kewajiban'],
                'total_kewajiban_formatted' => $neraca1['total_formatted']['kewajiban'],
                'total_ekuitas' => $neraca1['total']['ekuitas'],
                'total_ekuitas_formatted' => $neraca1['total_formatted']['ekuitas']
            ],
            'periode2' => [
                'tanggal' => $periode2,
                'label' => date('d M Y', strtotime($periode2)),
                'total_aset' => $neraca2['total']['aset'],
                'total_aset_formatted' => $neraca2['total_formatted']['aset'],
                'total_kewajiban' => $neraca2['total']['kewajiban'],
                'total_kewajiban_formatted' => $neraca2['total_formatted']['kewajiban'],
                'total_ekuitas' => $neraca2['total']['ekuitas'],
                'total_ekuitas_formatted' => $neraca2['total_formatted']['ekuitas']
            ],
            'perubahan' => [
                'aset' => $perubahanAset,
                'aset_formatted' => $this->formatRupiah($perubahanAset),
                'aset_persen' => $neraca1['total']['aset'] != 0 ? ($perubahanAset / $neraca1['total']['aset']) * 100 : 0,
                'kewajiban' => $perubahanKewajiban,
                'kewajiban_formatted' => $this->formatRupiah($perubahanKewajiban),
                'kewajiban_persen' => $neraca1['total']['kewajiban'] != 0 ? ($perubahanKewajiban / $neraca1['total']['kewajiban']) * 100 : 0,
                'ekuitas' => $perubahanEkuitas,
                'ekuitas_formatted' => $this->formatRupiah($perubahanEkuitas),
                'ekuitas_persen' => $neraca1['total']['ekuitas'] != 0 ? ($perubahanEkuitas / $neraca1['total']['ekuitas']) * 100 : 0
            ]
        ];
    }
    
    /**
     * GET TREN NERACA BULANAN
     */
    public function getTrenNeraca(string $tahun): array
    {
        $data = [];
        
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $tanggal = date("$tahun-$bulan-t");
            $neraca = $this->getNeraca($tanggal);
            
            $data['labels'][] = date('M', strtotime($tanggal));
            $data['aset'][] = $neraca['total']['aset'];
            $data['kewajiban'][] = $neraca['total']['kewajiban'];
            $data['ekuitas'][] = $neraca['total']['ekuitas'];
        }
        
        return $data;
    }
    
    /**
     * Format Rupiah dengan tanda kurung untuk nilai negatif
     */
    private function formatRupiah($nilai): string
    {
        $nilai = (float) $nilai;
        
        if ($nilai < 0) {
            return '(Rp ' . number_format(abs($nilai), 0, ',', '.') . ')';
        }
        
        return 'Rp ' . number_format($nilai, 0, ',', '.');
    }
    
    /**
     * Clear cache neraca
     */
    public function clearCache(string $tanggalPeriode = null): void
    {
        if ($tanggalPeriode) {
            cache()->delete('neraca_' . $tanggalPeriode);
        }
    }
}