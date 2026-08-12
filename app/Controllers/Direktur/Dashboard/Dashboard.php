<?php
namespace App\Controllers\Direktur\Dashboard;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        // Data sederhana untuk view
        $data = [
            'title' => 'Dashboard Direktur',
            'active' => 'dashboard',
            'user' => [
                'name' => session()->get('name') ?: 'Direktur',
                'role' => session()->get('role') ?: 'direktur',
                'email' => session()->get('email') ?: 'direktur@cdw-engineering.com'
            ]
        ];
        
        return view('direktur/dashboard/index', $data);
    }

    public function getNotifications()
    {
        $db = \Config\Database::connect();
        $totalNotif = 0;
        
        if ($db->tableExists('form_kasbon')) {
            $totalNotif += $db->table('form_kasbon')->where('status_direktur', 'Menunggu')->countAllResults();
        }
        if ($db->tableExists('form_pembelian')) {
            $totalNotif += $db->table('form_pembelian')->where('status_direktur', 'Menunggu')->countAllResults();
        }
        if ($db->tableExists('laporan_harian')) {
            $totalNotif += $db->table('laporan_harian')->where('status', 'menunggu_review')->countAllResults();
        }
        if ($db->tableExists('pengajuan_atk')) {
            $totalNotif += $db->table('pengajuan_atk')->where('status', 'menunggu')->countAllResults();
        }
        if ($db->tableExists('pengadaan_aset')) {
            $totalNotif += $db->table('pengadaan_aset')->where('status', 'menunggu')->countAllResults();
        }
        if ($db->tableExists('laporan_kerusakan')) {
            $totalNotif += $db->table('laporan_kerusakan')->where('status_tindakan', 'dilaporkan')->countAllResults();
        }

        return $this->response->setJSON([
            'status' => 'success',
            'count'  => $totalNotif
        ]);
    }
}