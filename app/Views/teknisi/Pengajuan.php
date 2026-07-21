<?php
namespace App\Controllers\Teknisi;

use App\Controllers\BaseController;

class Pengajuan extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Pengajuan',
            'active' => 'pengajuan',
            'subtitle' => 'Semua Pengajuan',
            'user' => [
                'name' => session()->get('name') ?: 'Teknisi',
                'role' => session()->get('role') ?: 'teknisi',
                'email' => session()->get('email') ?: 'teknisi@cdw.com'
            ]
        ];
        
        return view('teknisi/pengajuan/index', $data);
    }
    
    public function permintaanPembelian()
    {
        $data = [
            'title' => 'Permintaan Pembelian',
            'active' => 'pengajuan',
            'subtitle' => 'Form Permintaan Pembelian Barang',
            'user' => [
                'name' => session()->get('name') ?: 'Teknisi',
                'role' => session()->get('role') ?: 'teknisi',
                'email' => session()->get('email') ?: 'teknisi@cdw.com'
            ]
        ];
        
        return view('teknisi/pengajuan/permintaan_pembelian', $data);
    }
    
    public function biayaLapangan()
    {
        $data = [
            'title' => 'Biaya Lapangan',
            'active' => 'pengajuan',
            'subtitle' => 'Pengajuan Biaya Operasional Lapangan',
            'user' => [
                'name' => session()->get('name') ?: 'Teknisi',
                'role' => session()->get('role') ?: 'teknisi',
                'email' => session()->get('email') ?: 'teknisi@cdw.com'
            ]
        ];
        
        return view('teknisi/pengajuan/biaya_lapangan', $data);
    }
}