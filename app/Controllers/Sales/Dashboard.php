<?php

namespace App\Controllers\Sales;

class Dashboard extends SalesController
{
    public function index()
    {
        // Data untuk dashboard
        $data = [
            'title' => 'Dashboard Sales',
            'subtitle' => 'Selamat datang di sistem Sales',
            'active' => 'dashboard'
        ];
        
        return $this->renderView('sales/dashboard/simple_index', $data);
    }
    
    public function absensi()
    {
        $data = [
            'title' => 'Absensi Sales',
            'subtitle' => 'Catatan kehadiran Anda',
            'active' => 'absensi'
        ];
        
        return $this->renderView('sales/absensi/index', $data);
    }
    
    public function profile()
    {
        $data = [
            'title' => 'Profile Sales',
            'subtitle' => 'Data pribadi Anda',
            'active' => 'profile'
        ];
        
        return $this->renderView('sales/profile/index', $data);
    }
}