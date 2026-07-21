<?php
namespace App\Models\Accounting;

use CodeIgniter\Model;
use App\Models\CoaModel;

class ArusKasModel extends Model
{
    protected $table = 'mutasi_bank';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $useTimestamps = false;

    /**
     * Get arus kas untuk periode tertentu
     */
    public function getArusKas($tanggalMulai, $tanggalSelesai)
    {
        // Arus kas dari aktivitas operasi
        $arusOperasi = $this->getArusOperasi($tanggalMulai, $tanggalSelesai);
        
        // Arus kas dari aktivitas investasi
        $arusInvestasi = $this->getArusInvestasi($tanggalMulai, $tanggalSelesai);
        
        // Arus kas dari aktivitas pendanaan
        $arusPendanaan = $this->getArusPendanaan($tanggalMulai, $tanggalSelesai);
        
        // Hitung total
        $totalArusKas = $arusOperasi['total'] + $arusInvestasi['total'] + $arusPendanaan['total'];
        
        // Saldo awal dan akhir
        $saldoAwal = $this->getSaldoAwalKas($tanggalMulai);
        $saldoAkhir = $saldoAwal + $totalArusKas;
        
        return [
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'arus_operasi' => $arusOperasi,
            'arus_investasi' => $arusInvestasi,
            'arus_pendanaan' => $arusPendanaan,
            'total_arus_kas' => $totalArusKas,
            'saldo_awal_kas' => $saldoAwal,
            'saldo_akhir_kas' => $saldoAkhir
        ];
    }

    /**
     * Get arus kas dari aktivitas operasi
     */
    public function getArusOperasi($tanggalMulai, $tanggalSelesai)
    {
        // Penerimaan kas dari pelanggan
        $penerimaanPelanggan = $this->getPenerimaanDariPelanggan($tanggalMulai, $tanggalSelesai);
        
        // Pembayaran kas kepada supplier
        $pembayaranSupplier = $this->getPembayaranKepadaSupplier($tanggalMulai, $tanggalSelesai);
        
        // Pembayaran gaji
        $pembayaranGaji = $this->getPembayaranGaji($tanggalMulai, $tanggalSelesai);
        
        // Pembayaran pajak
        $pembayaranPajak = $this->getPembayaranPajak($tanggalMulai, $tanggalSelesai);
        
        // Penerimaan lain-lain
        $penerimaanLain = $this->getPenerimaanLain($tanggalMulai, $tanggalSelesai);
        
        // Pembayaran lain-lain
        $pembayaranLain = $this->getPembayaranLain($tanggalMulai, $tanggalSelesai);
        
        $totalPenerimaan = $penerimaanPelanggan + $penerimaanLain;
        $totalPengeluaran = $pembayaranSupplier + $pembayaranGaji + $pembayaranPajak + $pembayaranLain;
        
        return [
            'penerimaan_dari_pelanggan' => $penerimaanPelanggan,
            'pembayaran_kepada_supplier' => $pembayaranSupplier,
            'pembayaran_gaji' => $pembayaranGaji,
            'pembayaran_pajak' => $pembayaranPajak,
            'penerimaan_lain' => $penerimaanLain,
            'pembayaran_lain' => $pembayaranLain,
            'total_penerimaan' => $totalPenerimaan,
            'total_pengeluaran' => $totalPengeluaran,
            'total' => $totalPenerimaan - $totalPengeluaran
        ];
    }

    /**
     * Get arus kas dari aktivitas investasi
     */
    public function getArusInvestasi($tanggalMulai, $tanggalSelesai)
    {
        // Penerimaan dari penjualan aset tetap
        $penjualanAset = $this->getPenjualanAsetTetap($tanggalMulai, $tanggalSelesai);
        
        // Pembayaran untuk pembelian aset tetap
        $pembelianAset = $this->getPembelianAsetTetap($tanggalMulai, $tanggalSelesai);
        
        return [
            'penjualan_aset_tetap' => $penjualanAset,
            'pembelian_aset_tetap' => $pembelianAset,
            'total' => $penjualanAset - $pembelianAset
        ];
    }

    /**
     * Get arus kas dari aktivitas pendanaan
     */
    public function getArusPendanaan($tanggalMulai, $tanggalSelesai)
    {
        // Penerimaan dari pinjaman bank
        $penerimaanPinjaman = $this->getPenerimaanPinjamanBank($tanggalMulai, $tanggalSelesai);
        
        // Pembayaran utang bank
        $pembayaranUtang = $this->getPembayaranUtangBank($tanggalMulai, $tanggalSelesai);
        
        // Setoran modal
        $setoranModal = $this->getSetoranModal($tanggalMulai, $tanggalSelesai);
        
        // Prive (pengambilan pribadi)
        $prive = $this->getPrive($tanggalMulai, $tanggalSelesai);
        
        return [
            'penerimaan_pinjaman' => $penerimaanPinjaman,
            'pembayaran_utang' => $pembayaranUtang,
            'setoran_modal' => $setoranModal,
            'prive' => $prive,
            'total' => $penerimaanPinjaman - $pembayaranUtang + $setoranModal - $prive
        ];
    }

    /**
     * Get penerimaan dari pelanggan (mutasi bank kredit dengan akun pendapatan/piutang)
     */
    private function getPenerimaanDariPelanggan($tanggalMulai, $tanggalSelesai)
    {
        $builder = $this->db->table('mutasi_bank')
            ->select('SUM(mutasi_bank.jumlah) as total')
            ->join('coa', 'coa.id = mutasi_bank.coa_id_kredit', 'left')
            ->where('mutasi_bank.tipe', 'Kredit')
            ->where('mutasi_bank.status', 'Posted')
            ->where('mutasi_bank.tanggal >=', $tanggalMulai)
            ->where('mutasi_bank.tanggal <=', $tanggalSelesai)
            ->whereIn('coa.tipe_akun', ['Pendapatan', 'Aset'])
            ->where('coa.kode_akun LIKE', '1-12%', 'after');
        
        $result = $builder->get()->getRow();
        return $result->total ?? 0;
    }

    /**
     * Get pembayaran kepada supplier (mutasi bank debit dengan akun beban/hutang)
     */
    private function getPembayaranKepadaSupplier($tanggalMulai, $tanggalSelesai)
    {
        $builder = $this->db->table('mutasi_bank')
            ->select('SUM(mutasi_bank.jumlah) as total')
            ->join('coa', 'coa.id = mutasi_bank.coa_id_debit', 'left')
            ->where('mutasi_bank.tipe', 'Debit')
            ->where('mutasi_bank.status', 'Posted')
            ->where('mutasi_bank.tanggal >=', $tanggalMulai)
            ->where('mutasi_bank.tanggal <=', $tanggalSelesai)
            ->where('coa.tipe_akun', 'Beban');
        
        $result = $builder->get()->getRow();
        return $result->total ?? 0;
    }

    /**
     * Get pembayaran gaji
     */
    private function getPembayaranGaji($tanggalMulai, $tanggalSelesai)
    {
        $builder = $this->db->table('mutasi_bank')
            ->select('SUM(mutasi_bank.jumlah) as total')
            ->join('coa', 'coa.id = mutasi_bank.coa_id_debit', 'left')
            ->where('mutasi_bank.tipe', 'Debit')
            ->where('mutasi_bank.status', 'Posted')
            ->where('mutasi_bank.tanggal >=', $tanggalMulai)
            ->where('mutasi_bank.tanggal <=', $tanggalSelesai)
            ->where('coa.kode_akun LIKE', '5-12%', 'after');
        
        $result = $builder->get()->getRow();
        return $result->total ?? 0;
    }

    /**
     * Get pembayaran pajak
     */
    private function getPembayaranPajak($tanggalMulai, $tanggalSelesai)
    {
        $builder = $this->db->table('mutasi_bank')
            ->select('SUM(mutasi_bank.jumlah) as total')
            ->join('coa', 'coa.id = mutasi_bank.coa_id_debit', 'left')
            ->where('mutasi_bank.tipe', 'Debit')
            ->where('mutasi_bank.status', 'Posted')
            ->where('mutasi_bank.tanggal >=', $tanggalMulai)
            ->where('mutasi_bank.tanggal <=', $tanggalSelesai)
            ->where('coa.kode_akun LIKE', '5-18%', 'after');
        
        $result = $builder->get()->getRow();
        return $result->total ?? 0;
    }

    /**
     * Get penerimaan lain-lain
     */
    private function getPenerimaanLain($tanggalMulai, $tanggalSelesai)
    {
        $builder = $this->db->table('mutasi_bank')
            ->select('SUM(mutasi_bank.jumlah) as total')
            ->join('coa', 'coa.id = mutasi_bank.coa_id_kredit', 'left')
            ->where('mutasi_bank.tipe', 'Kredit')
            ->where('mutasi_bank.status', 'Posted')
            ->where('mutasi_bank.tanggal >=', $tanggalMulai)
            ->where('mutasi_bank.tanggal <=', $tanggalSelesai)
            ->where('coa.tipe_akun', 'Pendapatan')
            ->where('coa.kode_akun NOT LIKE', '4-11%', 'after')
            ->where('coa.kode_akun NOT LIKE', '4-12%', 'after');
        
        $result = $builder->get()->getRow();
        return $result->total ?? 0;
    }

    /**
     * Get pembayaran lain-lain
     */
    private function getPembayaranLain($tanggalMulai, $tanggalSelesai)
    {
        $builder = $this->db->table('mutasi_bank')
            ->select('SUM(mutasi_bank.jumlah) as total')
            ->join('coa', 'coa.id = mutasi_bank.coa_id_debit', 'left')
            ->where('mutasi_bank.tipe', 'Debit')
            ->where('mutasi_bank.status', 'Posted')
            ->where('mutasi_bank.tanggal >=', $tanggalMulai)
            ->where('mutasi_bank.tanggal <=', $tanggalSelesai)
            ->where('coa.tipe_akun', 'Beban')
            ->where('coa.kode_akun NOT LIKE', '5-12%', 'after')
            ->where('coa.kode_akun NOT LIKE', '5-18%', 'after');
        
        $result = $builder->get()->getRow();
        return $result->total ?? 0;
    }

    /**
     * Get penjualan aset tetap
     */
    private function getPenjualanAsetTetap($tanggalMulai, $tanggalSelesai)
    {
        $builder = $this->db->table('mutasi_bank')
            ->select('SUM(mutasi_bank.jumlah) as total')
            ->join('coa', 'coa.id = mutasi_bank.coa_id_kredit', 'left')
            ->where('mutasi_bank.tipe', 'Kredit')
            ->where('mutasi_bank.status', 'Posted')
            ->where('mutasi_bank.tanggal >=', $tanggalMulai)
            ->where('mutasi_bank.tanggal <=', $tanggalSelesai)
            ->where('coa.kode_akun LIKE', '4-130%', 'after');
        
        $result = $builder->get()->getRow();
        return $result->total ?? 0;
    }

    /**
     * Get pembelian aset tetap
     */
    private function getPembelianAsetTetap($tanggalMulai, $tanggalSelesai)
    {
        $builder = $this->db->table('mutasi_bank')
            ->select('SUM(mutasi_bank.jumlah) as total')
            ->join('coa', 'coa.id = mutasi_bank.coa_id_debit', 'left')
            ->where('mutasi_bank.tipe', 'Debit')
            ->where('mutasi_bank.status', 'Posted')
            ->where('mutasi_bank.tanggal >=', $tanggalMulai)
            ->where('mutasi_bank.tanggal <=', $tanggalSelesai)
            ->where('coa.kode_akun LIKE', '1-2%', 'after');
        
        $result = $builder->get()->getRow();
        return $result->total ?? 0;
    }

    /**
     * Get penerimaan pinjaman bank
     */
    private function getPenerimaanPinjamanBank($tanggalMulai, $tanggalSelesai)
    {
        $builder = $this->db->table('mutasi_bank')
            ->select('SUM(mutasi_bank.jumlah) as total')
            ->join('coa', 'coa.id = mutasi_bank.coa_id_kredit', 'left')
            ->where('mutasi_bank.tipe', 'Kredit')
            ->where('mutasi_bank.status', 'Posted')
            ->where('mutasi_bank.tanggal >=', $tanggalMulai)
            ->where('mutasi_bank.tanggal <=', $tanggalSelesai)
            ->where('coa.kode_akun LIKE', '2-2%', 'after');
        
        $result = $builder->get()->getRow();
        return $result->total ?? 0;
    }

    /**
     * Get pembayaran utang bank
     */
    private function getPembayaranUtangBank($tanggalMulai, $tanggalSelesai)
    {
        $builder = $this->db->table('mutasi_bank')
            ->select('SUM(mutasi_bank.jumlah) as total')
            ->join('coa', 'coa.id = mutasi_bank.coa_id_debit', 'left')
            ->where('mutasi_bank.tipe', 'Debit')
            ->where('mutasi_bank.status', 'Posted')
            ->where('mutasi_bank.tanggal >=', $tanggalMulai)
            ->where('mutasi_bank.tanggal <=', $tanggalSelesai)
            ->where('coa.kode_akun LIKE', '2-2%', 'after');
        
        $result = $builder->get()->getRow();
        return $result->total ?? 0;
    }

    /**
     * Get setoran modal
     */
    private function getSetoranModal($tanggalMulai, $tanggalSelesai)
    {
        $builder = $this->db->table('mutasi_bank')
            ->select('SUM(mutasi_bank.jumlah) as total')
            ->join('coa', 'coa.id = mutasi_bank.coa_id_kredit', 'left')
            ->where('mutasi_bank.tipe', 'Kredit')
            ->where('mutasi_bank.status', 'Posted')
            ->where('mutasi_bank.tanggal >=', $tanggalMulai)
            ->where('mutasi_bank.tanggal <=', $tanggalSelesai)
            ->where('coa.kode_akun LIKE', '3-11%', 'after');
        
        $result = $builder->get()->getRow();
        return $result->total ?? 0;
    }

    /**
     * Get prive
     */
    private function getPrive($tanggalMulai, $tanggalSelesai)
    {
        $builder = $this->db->table('mutasi_bank')
            ->select('SUM(mutasi_bank.jumlah) as total')
            ->join('coa', 'coa.id = mutasi_bank.coa_id_debit', 'left')
            ->where('mutasi_bank.tipe', 'Debit')
            ->where('mutasi_bank.status', 'Posted')
            ->where('mutasi_bank.tanggal >=', $tanggalMulai)
            ->where('mutasi_bank.tanggal <=', $tanggalSelesai)
            ->where('coa.kode_akun', '3-1301');
        
        $result = $builder->get()->getRow();
        return $result->total ?? 0;
    }

    /**
     * Get saldo awal kas
     */
    private function getSaldoAwalKas($tanggalMulai)
    {
        $builder = $this->db->table('mutasi_bank')
            ->select('SUM(CASE WHEN tipe = "Kredit" THEN jumlah ELSE 0 END) as total_masuk,
                      SUM(CASE WHEN tipe = "Debit" THEN jumlah ELSE 0 END) as total_keluar')
            ->where('status', 'Posted')
            ->where('tanggal <', $tanggalMulai);
        
        $result = $builder->get()->getRow();
        
        $totalMasuk = $result->total_masuk ?? 0;
        $totalKeluar = $result->total_keluar ?? 0;
        
        return $totalMasuk - $totalKeluar;
    }

    /**
     * Get arus kas komparatif (perbandingan dengan periode sebelumnya)
     */
    public function getArusKasKomparatif($tanggalMulai, $tanggalSelesai)
    {
        $arusKasSaatIni = $this->getArusKas($tanggalMulai, $tanggalSelesai);
        
        // Hitung periode sebelumnya
        $selisihHari = (strtotime($tanggalSelesai) - strtotime($tanggalMulai)) / (60 * 60 * 24);
        $tanggalMulaiLalu = date('Y-m-d', strtotime($tanggalMulai . " -$selisihHari days"));
        $tanggalSelesaiLalu = date('Y-m-d', strtotime($tanggalMulaiLalu . " +$selisihHari days"));
        
        $arusKasSebelum = $this->getArusKas($tanggalMulaiLalu, $tanggalSelesaiLalu);
        
        return [
            'periode_saat_ini' => [
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'total_arus_operasi' => $arusKasSaatIni['arus_operasi']['total'],
                'total_arus_investasi' => $arusKasSaatIni['arus_investasi']['total'],
                'total_arus_pendanaan' => $arusKasSaatIni['arus_pendanaan']['total'],
                'total_arus_kas' => $arusKasSaatIni['total_arus_kas']
            ],
            'periode_sebelum' => [
                'tanggal_mulai' => $tanggalMulaiLalu,
                'tanggal_selesai' => $tanggalSelesaiLalu,
                'total_arus_operasi' => $arusKasSebelum['arus_operasi']['total'],
                'total_arus_investasi' => $arusKasSebelum['arus_investasi']['total'],
                'total_arus_pendanaan' => $arusKasSebelum['arus_pendanaan']['total'],
                'total_arus_kas' => $arusKasSebelum['total_arus_kas']
            ],
            'perubahan' => [
                'arus_operasi' => $arusKasSaatIni['arus_operasi']['total'] - $arusKasSebelum['arus_operasi']['total'],
                'arus_investasi' => $arusKasSaatIni['arus_investasi']['total'] - $arusKasSebelum['arus_investasi']['total'],
                'arus_pendanaan' => $arusKasSaatIni['arus_pendanaan']['total'] - $arusKasSebelum['arus_pendanaan']['total'],
                'total_arus_kas' => $arusKasSaatIni['total_arus_kas'] - $arusKasSebelum['total_arus_kas']
            ]
        ];
    }

    /**
     * Get ringkasan arus kas untuk dashboard
     */
    public function getDashboardSummary($tahun = null)
    {
        if (!$tahun) {
            $tahun = date('Y');
        }
        
        $tanggalMulai = date('Y-m-d', strtotime("$tahun-01-01"));
        $tanggalSelesai = date('Y-m-d');
        
        $arusKas = $this->getArusKas($tanggalMulai, $tanggalSelesai);
        
        return [
            'tahun' => $tahun,
            'arus_operasi' => $arusKas['arus_operasi']['total'],
            'arus_investasi' => $arusKas['arus_investasi']['total'],
            'arus_pendanaan' => $arusKas['arus_pendanaan']['total'],
            'total_arus_kas' => $arusKas['total_arus_kas'],
            'saldo_awal_kas' => $arusKas['saldo_awal_kas'],
            'saldo_akhir_kas' => $arusKas['saldo_akhir_kas']
        ];
    }

    /**
     * Export data untuk Excel
     */
    public function getExportData($tanggalMulai, $tanggalSelesai)
    {
        return $this->getArusKas($tanggalMulai, $tanggalSelesai);
    }
}