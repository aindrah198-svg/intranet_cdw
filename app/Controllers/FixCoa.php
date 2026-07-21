<?php

namespace App\Controllers;

class FixCoa extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('coa');

        // Akun 4-14000-175 (Pendapatan Penjualan)
        $builder->where('kode_akun', '4-14000-175')->update(['saldo_normal' => 'Kredit']);

        // Akun 5-1905-176 (Beban Pajak Bank)
        $builder->where('kode_akun', '5-1905-176')->update(['saldo_normal' => 'Debit']);

        // Akun 5-2000-183 (Beban Operasional)
        $builder->where('kode_akun', '5-2000-183')->update(['saldo_normal' => 'Debit']);

        // Akun 2-1800-180 (Hutang Titipan)
        $builder->where('kode_akun', '2-1800-180')->update(['saldo_normal' => 'Kredit']);

        echo "Update Berhasil";
    }
}
