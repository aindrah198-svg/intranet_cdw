<?php
namespace App\Controllers\Teknisi;

use App\Controllers\BaseController;

class Laporan extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Laporan',
            'active' => 'laporan',
            'subtitle' => 'Dashboard Laporan',
            'user' => [
                'name' => session()->get('name') ?: 'Teknisi',
                'role' => session()->get('role') ?: 'teknisi',
                'email' => session()->get('email') ?: 'teknisi@cdw.com'
            ]
        ];
        
        return view('teknisi/laporan/index', $data);
    }
    
    public function lapangan()
    {
        $data = [
            'title' => 'Laporan Lapangan',
            'active' => 'laporan',
            'subtitle' => 'Laporan Pekerjaan Lapangan',
            'user' => [
                'name' => session()->get('name') ?: 'Teknisi',
                'role' => session()->get('role') ?: 'teknisi',
                'email' => session()->get('email') ?: 'teknisi@cdw.com'
            ]
        ];
        
        return view('teknisi/laporan/lapangan', $data);
    }
    
    public function inventory()
    {
        $data = [
            'title' => 'Laporan Inventory',
            'active' => 'laporan',
            'subtitle' => 'Laporan Stok Inventory',
            'user' => [
                'name' => session()->get('name') ?: 'Teknisi',
                'role' => session()->get('role') ?: 'teknisi',
                'email' => session()->get('email') ?: 'teknisi@cdw.com'
            ]
        ];
        
        return view('teknisi/laporan/inventory', $data);
    }
}