<?php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\AbsensiModel;

class Pribadi extends BaseController
{
    protected $db;
    protected $absensiModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->absensiModel = new AbsensiModel();
        helper(['form', 'url', 'number', 'text']);
        
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Absensi Saya
     */
    public function absensi()
    {
        $data['title'] = 'Absensi Saya';
        $userId = session()->get('user_id');
        $user = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
        $karyawanId = session()->get('karyawan_id') ?? $user['karyawan_id'] ?? null;
        
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;
        $data['user'] = $user;

        $absensiList = [];
        if ($karyawanId && $this->db->tableExists('absensi')) {
            $absensiList = $this->db->table('absensi')
                ->where('karyawan_id', $karyawanId)
                ->where('MONTH(tanggal)', $bulan)
                ->where('YEAR(tanggal)', $tahun)
                ->orderBy('tanggal', 'DESC')
                ->get()->getResultArray();
        }

        $data['absensiList'] = $absensiList;
        $data['active'] = 'absensi';

        return view('accounting/templates/header', $data)
             . view('accounting/templates/sidebar', $data)
             . view('accounting/pribadi/absensi/index', $data)
             . view('accounting/templates/footer', $data);
    }

    /**
     * Profil Saya
     */
    public function profil()
    {
        $data['title'] = 'Profil Saya';
        $userId = session()->get('user_id');
        $user = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
        $karyawan = null;
        
        if (!empty($user['karyawan_id']) && $this->db->tableExists('karyawan')) {
            $karyawan = $this->db->table('karyawan')->where('id', $user['karyawan_id'])->get()->getRowArray();
        }

        $data['user'] = $user;
        $data['karyawan'] = $karyawan;
        $data['active'] = 'profil';

        return view('accounting/templates/header', $data)
             . view('accounting/templates/sidebar', $data)
             . view('accounting/pribadi/profil/index', $data)
             . view('accounting/templates/footer', $data);
    }

    /**
     * Riwayat Audit
     */
    public function riwayatAudit()
    {
        $data['title'] = 'Riwayat Audit Saya';
        $userId = session()->get('user_id');
        
        $auditList = [];
        if ($this->db->tableExists('audit_trail')) {
            $auditList = $this->db->table('audit_trail')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->limit(50)
                ->get()->getResultArray();
        } elseif ($this->db->tableExists('activity_log')) {
            $auditList = $this->db->table('activity_log')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->limit(50)
                ->get()->getResultArray();
        }

        $data['auditList'] = $auditList;
        $data['active'] = 'riwayat-audit';

        return view('accounting/templates/header', $data)
             . view('accounting/templates/sidebar', $data)
             . view('accounting/pribadi/riwayat-audit/index', $data)
             . view('accounting/templates/footer', $data);
    }
}
