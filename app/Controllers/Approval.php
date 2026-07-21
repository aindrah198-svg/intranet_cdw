<?php
namespace App\Controllers\Direktur;

use App\Controllers\BaseController;

class Approval extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Approval',
            'active' => 'approval',
            'subtitle' => 'Dashboard Approval Direktur',
            'user' => [
                'name' => session()->get('name') ?: 'Direktur',
                'role' => session()->get('role') ?: 'direktur'
            ]
        ];
        
        return view('direktur/approval/index', $data);
    }
    
    public function cuti()
    {
        $data = [
            'title' => 'Approval Cuti',
            'active' => 'approval',
            'subtitle' => 'Approval Permohonan Cuti Karyawan',
            'user' => [
                'name' => session()->get('name') ?: 'Direktur',
                'role' => session()->get('role') ?: 'direktur'
            ]
        ];
        
        return view('direktur/approval/cuti', $data);
    }
    
    public function spk()
    {
        $data = [
            'title' => 'Approval SPK',
            'active' => 'approval',
            'subtitle' => 'Approval Surat Perintah Kerja',
            'user' => [
                'name' => session()->get('name') ?: 'Direktur',
                'role' => session()->get('role') ?: 'direktur'
            ]
        ];
        
        return view('direktur/approval/spk', $data);
    }
    
    public function kasbon()
    {
        $data = [
            'title' => 'Approval Kasbon',
            'active' => 'approval',
            'subtitle' => 'Approval Permohonan Kasbon Karyawan',
            'user' => [
                'name' => session()->get('name') ?: 'Direktur',
                'role' => session()->get('role') ?: 'direktur'
            ]
        ];
        
        return view('direktur/approval/kasbon', $data);
    }
    
    public function dokumen()
    {
        $data = [
            'title' => 'Approval Dokumen',
            'active' => 'approval',
            'subtitle' => 'Approval Dokumen Perusahaan',
            'user' => [
                'name' => session()->get('name') ?: 'Direktur',
                'role' => session()->get('role') ?: 'direktur'
            ]
        ];
        
        return view('direktur/approval/dokumen', $data);
    }
    
    public function pembelian()
    {
        $data = [
            'title' => 'Approval Pembelian',
            'active' => 'approval',
            'subtitle' => 'Approval Permohonan Pembelian',
            'user' => [
                'name' => session()->get('name') ?: 'Direktur',
                'role' => session()->get('role') ?: 'direktur'
            ]
        ];
        
        return view('direktur/approval/pembelian', $data);
    }
    
    public function suratJalan()
    {
        $data = [
            'title' => 'Approval Surat Jalan',
            'active' => 'approval',
            'subtitle' => 'Approval Surat Jalan',
            'user' => [
                'name' => session()->get('name') ?: 'Direktur',
                'role' => session()->get('role') ?: 'direktur'
            ]
        ];
        
        return view('direktur/approval/surat_jalan', $data);
    }
}