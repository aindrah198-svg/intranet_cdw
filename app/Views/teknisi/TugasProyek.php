<?php
namespace App\Controllers\Teknisi;

use App\Controllers\BaseController;

class TugasProyek extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Tugas & Proyek',
            'active' => 'tugas-proyek',
            'subtitle' => 'Daftar Semua Tugas dan Proyek',
            'user' => [
                'name' => session()->get('name') ?: 'Teknisi',
                'role' => session()->get('role') ?: 'teknisi',
                'email' => session()->get('email') ?: 'teknisi@cdw.com'
            ]
        ];
        
        return view('teknisi/tugas_proyek/index', $data);
    }
    
    public function spk()
    {
        $data = [
            'title' => 'SPK/Tugas Instalasi',
            'active' => 'tugas-proyek',
            'subtitle' => 'Daftar SPK dan Tugas Instalasi',
            'user' => [
                'name' => session()->get('name') ?: 'Teknisi',
                'role' => session()->get('role') ?: 'teknisi',
                'email' => session()->get('email') ?: 'teknisi@cdw.com'
            ]
        ];
        
        return view('teknisi/tugas_proyek/spk', $data);
    }
    
    public function timeline()
    {
        $data = [
            'title' => 'Timeline/Grafik',
            'active' => 'tugas-proyek',
            'subtitle' => 'Timeline dan Grafik Proyek',
            'user' => [
                'name' => session()->get('name') ?: 'Teknisi',
                'role' => session()->get('role') ?: 'teknisi',
                'email' => session()->get('email') ?: 'teknisi@cdw.com'
            ]
        ];
        
        return view('teknisi/tugas_proyek/timeline', $data);
    }
    
    public function tambahanWaktu()
    {
        $data = [
            'title' => 'Tambahan Waktu',
            'active' => 'tugas-proyek',
            'subtitle' => 'Pengajuan Tambahan Waktu Proyek',
            'user' => [
                'name' => session()->get('name') ?: 'Teknisi',
                'role' => session()->get('role') ?: 'teknisi',
                'email' => session()->get('email') ?: 'teknisi@cdw.com'
            ]
        ];
        
        return view('teknisi/tugas_proyek/tambahan_waktu', $data);
    }
    
    public function tambahanBarang()
    {
        $data = [
            'title' => 'Tambahan Barang',
            'active' => 'tugas-proyek',
            'subtitle' => 'Pengajuan Tambahan Barang Proyek',
            'user' => [
                'name' => session()->get('name') ?: 'Teknisi',
                'role' => session()->get('role') ?: 'teknisi',
                'email' => session()->get('email') ?: 'teknisi@cdw.com'
            ]
        ];
        
        return view('teknisi/tugas_proyek/tambahan_barang', $data);
    }
}