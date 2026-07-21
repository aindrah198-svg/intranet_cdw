<?php
namespace App\Controllers\Teknisi;

use App\Controllers\BaseController;

class Gudang extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Gudang & Penyimpanan',
            'active' => 'gudang',
            'subtitle' => 'Dashboard Gudang',
            'user' => [
                'name' => session()->get('name') ?: 'Teknisi',
                'role' => session()->get('role') ?: 'teknisi',
                'email' => session()->get('email') ?: 'teknisi@cdw.com'
            ]
        ];
        
        return view('teknisi/gudang/index', $data);
    }
    
    public function penyimpanan()
    {
        $data = [
            'title' => 'Penyimpanan Gudang',
            'active' => 'gudang',
            'subtitle' => 'Manajemen Penyimpanan Barang',
            'user' => [
                'name' => session()->get('name') ?: 'Teknisi',
                'role' => session()->get('role') ?: 'teknisi',
                'email' => session()->get('email') ?: 'teknisi@cdw.com'
            ]
        ];
        
        return view('teknisi/gudang/penyimpanan', $data);
    }
    
    public function peralatanDipinjam()
    {
        $data = [
            'title' => 'Peralatan Dipinjam',
            'active' => 'gudang',
            'subtitle' => 'Daftar Peralatan yang Dipinjam',
            'user' => [
                'name' => session()->get('name') ?: 'Teknisi',
                'role' => session()->get('role') ?: 'teknisi',
                'email' => session()->get('email') ?: 'teknisi@cdw.com'
            ]
        ];
        
        return view('teknisi/gudang/peralatan_dipinjam', $data);
    }
    
    public function perawatanAlat()
    {
        $data = [
            'title' => 'Perawatan Alat',
            'active' => 'gudang',
            'subtitle' => 'Jadwal dan Riwayat Perawatan Alat',
            'user' => [
                'name' => session()->get('name') ?: 'Teknisi',
                'role' => session()->get('role') ?: 'teknisi',
                'email' => session()->get('email') ?: 'teknisi@cdw.com'
            ]
        ];
        
        return view('teknisi/gudang/perawatan_alat', $data);
    }
}