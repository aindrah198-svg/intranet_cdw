<?php

namespace App\Models;

use CodeIgniter\Model;

class FinancialModel extends Model
{
    protected $table = 'jurnal';
    protected $primaryKey = 'id';
    
    /**
     * Get financial summary for current month
     */
    public function getFinancialSummary($month = null, $year = null)
    {
        if (!$month) $month = date('m');
        if (!$year) $year = date('Y');
        
        // Hitung pendapatan bulan ini (dari akun pendapatan)
        $pendapatan = $this->db->table('jurnal_detail jd')
            ->join('jurnal j', 'j.id = jd.jurnal_id')
            ->join('coa c', 'c.id = jd.coa_id')
            ->select('SUM(jd.kredit) as total')
            ->where('c.tipe_akun', 'Pendapatan')
            ->where('MONTH(j.tanggal)', $month)
            ->where('YEAR(j.tanggal)', $year)
            ->where('j.status', 'posted')
            ->get()
            ->getRow();
        
        // Hitung pengeluaran bulan ini (dari akun beban)
        $pengeluaran = $this->db->table('jurnal_detail jd')
            ->join('jurnal j', 'j.id = jd.jurnal_id')
            ->join('coa c', 'c.id = jd.coa_id')
            ->select('SUM(jd.debit) as total')
            ->where('c.tipe_akun', 'Beban')
            ->where('MONTH(j.tanggal)', $month)
            ->where('YEAR(j.tanggal)', $year)
            ->where('j.status', 'posted')
            ->get()
            ->getRow();
        
        // Hitung total aset (saldo akun aset)
        $aset = $this->db->table('coa')
            ->select('SUM(CASE WHEN saldo_normal = "Debit" THEN 1 ELSE -1 END) as total', false)
            ->where('tipe_akun', 'Aset')
            ->get()
            ->getRow();
        
        // Hitung total kewajiban (saldo akun kewajiban)
        $kewajiban = $this->db->table('coa')
            ->select('SUM(CASE WHEN saldo_normal = "Kredit" THEN 1 ELSE -1 END) as total', false)
            ->where('tipe_akun', 'Kewajiban')
            ->get()
            ->getRow();
        
        // Hitung bulan lalu untuk persentase
        $lastMonth = $month - 1;
        $lastYear = $year;
        if ($lastMonth < 1) {
            $lastMonth = 12;
            $lastYear = $year - 1;
        }
        
        $pendapatanBulanLalu = $this->db->table('jurnal_detail jd')
            ->join('jurnal j', 'j.id = jd.jurnal_id')
            ->join('coa c', 'c.id = jd.coa_id')
            ->select('SUM(jd.kredit) as total')
            ->where('c.tipe_akun', 'Pendapatan')
            ->where('MONTH(j.tanggal)', $lastMonth)
            ->where('YEAR(j.tanggal)', $lastYear)
            ->where('j.status', 'posted')
            ->get()
            ->getRow();
        
        $pengeluaranBulanLalu = $this->db->table('jurnal_detail jd')
            ->join('jurnal j', 'j.id = jd.jurnal_id')
            ->join('coa c', 'c.id = jd.coa_id')
            ->select('SUM(jd.debit) as total')
            ->where('c.tipe_akun', 'Beban')
            ->where('MONTH(j.tanggal)', $lastMonth)
            ->where('YEAR(j.tanggal)', $lastYear)
            ->where('j.status', 'posted')
            ->get()
            ->getRow();
        
        // Hitung persentase
        $persenPendapatan = 0;
        if ($pendapatanBulanLalu->total > 0) {
            $persenPendapatan = (($pendapatan->total - $pendapatanBulanLalu->total) / $pendapatanBulanLalu->total) * 100;
        }
        
        $persenPengeluaran = 0;
        if ($pengeluaranBulanLalu->total > 0) {
            $persenPengeluaran = (($pengeluaran->total - $pengeluaranBulanLalu->total) / $pengeluaranBulanLalu->total) * 100;
        }
        
        // Persentase aset terhadap kewajiban
        $persenKewajibanDariAset = 0;
        if ($aset->total > 0) {
            $persenKewajibanDariAset = ($kewajiban->total / $aset->total) * 100;
        }
        
        return [
            'pendapatan' => [
                'value' => $pendapatan->total ?? 0,
                'formatted' => 'Rp ' . number_format($pendapatan->total ?? 0 / 1000000, 0) . ' Jt',
                'persen' => round($persenPendapatan, 1),
                'trend' => $persenPendapatan >= 0 ? 'naik' : 'turun'
            ],
            'pengeluaran' => [
                'value' => $pengeluaran->total ?? 0,
                'formatted' => 'Rp ' . number_format($pengeluaran->total ?? 0 / 1000000, 0) . ' Jt',
                'persen' => round($persenPengeluaran, 1),
                'trend' => $persenPengeluaran >= 0 ? 'naik' : 'turun'
            ],
            'aset' => [
                'value' => $aset->total ?? 0,
                'formatted' => 'Rp ' . number_format($aset->total ?? 0 / 1000000, 1) . ' M',
                'persen' => 12, // Ini dummy, bisa dihitung dari growth year-to-year
                'trend' => 'tumbuh'
            ],
            'kewajiban' => [
                'value' => $kewajiban->total ?? 0,
                'formatted' => 'Rp ' . number_format($kewajiban->total ?? 0 / 1000000, 1) . ' M',
                'persen' => round($persenKewajibanDariAset, 1),
                'trend' => 'dari aset'
            ]
        ];
    }
}