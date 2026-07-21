<?php
namespace App\Controllers\Direktur;

use App\Controllers\BaseController;

class Monitoring extends BaseController
{
    public function absensi()
    {
        $data = [
            'title' => 'Monitoring Absensi',
            'active' => 'monitoring',
            'subtitle' => 'Dashboard Monitoring Absensi Karyawan',
            'user' => [
                'name' => session()->get('name') ?: 'Direktur',
                'role' => session()->get('role') ?: 'direktur'
            ]
        ];
        
        return view('direktur/monitoring/absensi', $data);
    }
    
    public function performansi()
    {
        $data = [
            'title' => 'Monitoring Performansi',
            'active' => 'monitoring',
            'subtitle' => 'Dashboard Monitoring Performansi Karyawan',
            'user' => [
                'name' => session()->get('name') ?: 'Direktur',
                'role' => session()->get('role') ?: 'direktur'
            ]
        ];
        
        return view('direktur/monitoring/performansi', $data);
    }
    
    public function ringkasanPenggajian()
    {
        $data = [
            'title' => 'Ringkasan Penggajian',
            'active' => 'monitoring',
            'subtitle' => 'Ringkasan Data Penggajian Karyawan',
            'user' => [
                'name' => session()->get('name') ?: 'Direktur',
                'role' => session()->get('role') ?: 'direktur'
            ]
        ];
        
        return view('direktur/monitoring/ringkasan_penggajian', $data);
    }
    
    public function invoicePiutang()
    {
        $data = [
            'title' => 'Invoice & Piutang',
            'active' => 'monitoring',
            'subtitle' => 'Monitoring Invoice dan Piutang Perusahaan',
            'user' => [
                'name' => session()->get('name') ?: 'Direktur',
                'role' => session()->get('role') ?: 'direktur'
            ]
        ];
        
        return view('direktur/monitoring/invoice_piutang', $data);
    }
}