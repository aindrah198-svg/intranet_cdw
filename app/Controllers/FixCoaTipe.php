<?php

namespace App\Controllers;

class FixCoaTipe extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('coa');

        // 1. ASET
        $builder->like('kode_akun', '1-', 'after')
                ->update([
                    'tipe_akun' => 'Aset',
                    'saldo_normal' => 'Debit'
                ]);
        $affectedAset = $db->affectedRows();

        // 2. KEWAJIBAN
        $builder->like('kode_akun', '2-', 'after')
                ->update([
                    'tipe_akun' => 'Kewajiban',
                    'saldo_normal' => 'Kredit'
                ]);
        $affectedKewajiban = $db->affectedRows();

        // 3. EKUITAS
        $builder->like('kode_akun', '3-', 'after')
                ->update([
                    'tipe_akun' => 'Ekuitas',
                    'saldo_normal' => 'Kredit'
                ]);
        $affectedEkuitas = $db->affectedRows();

        // 4. PENDAPATAN
        $builder->like('kode_akun', '4-', 'after')
                ->update([
                    'tipe_akun' => 'Pendapatan',
                    'saldo_normal' => 'Kredit'
                ]);
        $affectedPendapatan = $db->affectedRows();

        // 5. BEBAN
        $builder->like('kode_akun', '5-', 'after')
                ->update([
                    'tipe_akun' => 'Beban',
                    'saldo_normal' => 'Debit'
                ]);
        $affectedBeban = $db->affectedRows();

        echo "<h1>Perbaikan Master Data COA Selesai</h1>";
        echo "<ul>";
        echo "<li>Aset (1-) terupdate: $affectedAset baris</li>";
        echo "<li>Kewajiban (2-) terupdate: $affectedKewajiban baris</li>";
        echo "<li>Ekuitas (3-) terupdate: $affectedEkuitas baris</li>";
        echo "<li>Pendapatan (4-) terupdate: $affectedPendapatan baris</li>";
        echo "<li>Beban (5-) terupdate: $affectedBeban baris</li>";
        echo "</ul>";
        echo "<p>Semua tipe akun dan saldo normal telah dicuci bersih sesuai standar akuntansi.</p>";
    }
}
