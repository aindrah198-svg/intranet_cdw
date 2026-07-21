<?php
namespace App\Controllers\Direktur;

use App\Controllers\BaseController;

class Laporan extends BaseController
{
    public function keuangan()
    {
        $data = [
            'title' => 'Laporan Keuangan',
            'active' => 'laporan',
            'subtitle' => 'Laporan Keuangan Perusahaan',
            'user' => [
                'name' => session()->get('name') ?: 'Direktur',
                'role' => session()->get('role') ?: 'direktur'
            ]
        ];
        
        return view('direktur/laporan/keuangan', $data);
    }
    
    public function stokGudang()
    {
        $data = [
            'title' => 'Laporan Stok Gudang',
            'active' => 'laporan',
            'subtitle' => 'Laporan Stok Barang Gudang',
            'user' => [
                'name' => session()->get('name') ?: 'Direktur',
                'role' => session()->get('role') ?: 'direktur'
            ]
        ];
        
        return view('direktur/laporan/stok_gudang', $data);
    }
}