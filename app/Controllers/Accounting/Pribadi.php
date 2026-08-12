<?php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;

class Pribadi extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        helper(['form', 'url', 'number', 'text']);
    }

    private function checkAccess()
    {
        if (!session()->get('isLoggedIn')) return redirect()->to(base_url('login'));
        return null;
    }

    private function getKaryawanId()
    {
        $name     = session()->get('name') ?? 'Accounting';
        $karyawan = $this->db->table('karyawan')
            ->where('deleted_at', null)
            ->like('nama_lengkap', $name)
            ->get()->getRowArray();
        if (!$karyawan) {
            $karyawan = $this->db->table('karyawan')->where('deleted_at', null)->get()->getRowArray();
        }
        return $karyawan['id'] ?? 1;
    }

    private function baseData(): array
    {
        return [
            'user' => [
                'name' => session()->get('name') ?? 'Accounting Staff',
                'role' => session()->get('role') ?? 'accounting'
            ]
        ];
    }

    /**
     * Absensi Saya
     */
    public function absensi()
    {
        if ($r = $this->checkAccess()) return $r;

        $karyawanId = $this->getKaryawanId();
        $today      = date('Y-m-d');

        if (!$this->db->tableExists('absensi')) {
            $forge = \Config\Database::forge();
            $forge->addField([
                'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'karyawan_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'tanggal'      => ['type' => 'DATE'],
                'waktu_masuk'  => ['type' => 'TIME', 'null' => true],
                'waktu_pulang' => ['type' => 'TIME', 'null' => true],
                'status'       => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Hadir'],
                'keterangan'   => ['type' => 'TEXT', 'null' => true],
                'created_at'   => ['type' => 'DATETIME', 'null' => true],
                'updated_at'   => ['type' => 'DATETIME', 'null' => true],
                'deleted_at'   => ['type' => 'DATETIME', 'null' => true]
            ]);
            $forge->addKey('id', true);
            $forge->createTable('absensi', true);
        }

        $todayAbsen     = $this->db->table('absensi')
            ->where('karyawan_id', $karyawanId)->where('tanggal', $today)
            ->where('deleted_at', null)->get()->getRowArray();

        $riwayatAbsensi = $this->db->table('absensi')
            ->where('karyawan_id', $karyawanId)->where('deleted_at', null)
            ->orderBy('tanggal', 'DESC')->limit(30)->get()->getResultArray();

        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        // Filter riwayat by bulan/tahun if needed
        $absensiList = $this->db->table('absensi')
            ->where('karyawan_id', $karyawanId)
            ->where('MONTH(tanggal)', $bulan)
            ->where('YEAR(tanggal)', $tahun)
            ->where('deleted_at', null)
            ->orderBy('tanggal', 'DESC')
            ->get()->getResultArray();

        $data = array_merge($this->baseData(), [
            'title'          => 'Absensi Saya',
            'subtitle'       => 'Pencatatan Kehadiran Mandiri Accounting',
            'active'         => 'absensi-saya',
            'todayAbsen'     => $todayAbsen,
            'riwayatAbsensi' => $riwayatAbsensi,
            'absensiList'    => $absensiList,
            'bulan'          => $bulan,
            'tahun'          => $tahun
        ]);

        return view('accounting/templates/header', $data)
             . view('accounting/templates/sidebar', $data)
             . view('accounting/templates/navbar', $data)
             . view('accounting/pribadi/absensi/index', $data)
             . view('accounting/templates/footer', $data);
    }

    public function checkin()
    {
        if ($r = $this->checkAccess()) return $r;
        $karyawanId  = $this->getKaryawanId();
        $today       = date('Y-m-d');
        $existing    = $this->db->table('absensi')
            ->where('karyawan_id', $karyawanId)->where('tanggal', $today)
            ->where('deleted_at', null)->get()->getRowArray();
        if ($existing) {
            return redirect()->to(base_url('accounting/pribadi/absensi'))->with('error', 'Anda sudah melakukan Check-in hari ini.');
        }
        $status = (date('H:i') > '08:30') ? 'Terlambat' : 'Hadir';
        $this->db->table('absensi')->insert([
            'karyawan_id' => $karyawanId, 'tanggal' => $today,
            'waktu_masuk' => date('H:i:s'), 'status' => $status,
            'created_at'  => date('Y-m-d H:i:s')
        ]);
        return redirect()->to(base_url('accounting/pribadi/absensi'))->with('success', 'Absen Masuk berhasil dicatat pada jam ' . date('H:i') . ' WIB.');
    }

    public function checkout()
    {
        if ($r = $this->checkAccess()) return $r;
        $karyawanId = $this->getKaryawanId();
        $today      = date('Y-m-d');
        $existing   = $this->db->table('absensi')
            ->where('karyawan_id', $karyawanId)->where('tanggal', $today)
            ->where('deleted_at', null)->get()->getRowArray();
        if (!$existing) {
            return redirect()->to(base_url('accounting/pribadi/absensi'))->with('error', 'Anda belum melakukan Check-in hari ini.');
        }
        if (!empty($existing['waktu_pulang'])) {
            return redirect()->to(base_url('accounting/pribadi/absensi'))->with('error', 'Anda sudah melakukan Check-out hari ini.');
        }
        $this->db->table('absensi')->where('id', $existing['id'])->update([
            'waktu_pulang' => date('H:i:s'), 'updated_at' => date('Y-m-d H:i:s')
        ]);
        return redirect()->to(base_url('accounting/pribadi/absensi'))->with('success', 'Absen Pulang berhasil dicatat pada jam ' . date('H:i') . ' WIB.');
    }

    /**
     * Tugas Hari Ini
     */
    public function tugas()
    {
        if ($r = $this->checkAccess()) return $r;

        $penugasanModel = new \App\Models\PenugasanHarianModel();
        $penugasanModel->ensureTableExists();
        $karyawanId = $this->getKaryawanId();

        $rawTasks = $penugasanModel
            ->where('deleted_at', null)
            ->groupStart()
                ->where('penerima_role', 'accounting')
                ->orWhere('penerima_role', 'all')
                ->orWhere('penerima_id', $karyawanId)
            ->groupEnd()
            ->orderBy('id', 'DESC')
            ->findAll();

        $tasks = [];
        foreach ($rawTasks as $t) {
            $tasks[] = $penugasanModel->getTaskWithItems($t['id']);
        }

        $data = array_merge($this->baseData(), [
            'title'  => 'Tugas Hari Ini',
            'active' => 'tugas-saya',
            'tasks'  => $tasks
        ]);

        return view('accounting/templates/header', $data)
             . view('accounting/templates/sidebar', $data)
             . view('accounting/templates/navbar', $data)
             . view('accounting/pribadi/tugas/index', $data)
             . view('accounting/templates/footer', $data);
    }

    public function detailTugas($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $penugasanModel = new \App\Models\PenugasanHarianModel();
        $task = $penugasanModel->getTaskWithItems($id);
        if (!$task) return redirect()->to(base_url('accounting/pribadi/tugas-saya'))->with('error', 'Tugas tidak ditemukan.');
        $data = array_merge($this->baseData(), ['title' => 'Detail Tugas', 'active' => 'tugas-saya', 'task' => $task]);
        return view('accounting/templates/header', $data)
             . view('accounting/templates/sidebar', $data)
             . view('accounting/templates/navbar', $data)
             . view('accounting/pribadi/tugas/detail', $data)
             . view('accounting/templates/footer', $data);
    }

    public function updateStatusTugas($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $penugasanModel = new \App\Models\PenugasanHarianModel();
        $status = $this->request->getPost('status');
        if (in_array($status, ['proses', 'selesai', 'pending'])) {
            $penugasanModel->update($id, ['status' => $status]);
        }
        return redirect()->back()->with('success', 'Status tugas berhasil diperbarui.');
    }

    public function updateSubItemStatus($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $penugasanModel = new \App\Models\PenugasanHarianModel();
        $itemModel      = new \App\Models\PenugasanHarianItemModel();
        $task = $penugasanModel->getTaskWithItems($id);
        if (!$task) return redirect()->to(base_url('accounting/pribadi/tugas-saya'))->with('error', 'Penugasan tidak ditemukan.');
        $markAllCompleted = $this->request->getPost('mark_all_completed');
        if ($markAllCompleted == '1') {
            $this->db->table('penugasan_harian_items')->where('penugasan_id', $id)
                ->update(['status_item' => 'selesai', 'updated_at' => date('Y-m-d H:i:s')]);
        } else {
            $itemStatuses = $this->request->getPost('subitem_status');
            if (!empty($itemStatuses) && is_array($itemStatuses)) {
                foreach ($itemStatuses as $itemId => $st) {
                    if (in_array($st, ['pending', 'proses', 'selesai', 'ditunda'])) {
                        $itemModel->update($itemId, ['status_item' => $st]);
                    }
                }
            }
        }
        $mainStatus = $this->request->getPost('main_status');
        if (!empty($mainStatus) && in_array($mainStatus, ['pending', 'proses', 'selesai'])) {
            $penugasanModel->update($id, ['status' => $mainStatus]);
        }
        $penugasanModel->recalculateTaskStatus($id);
        return redirect()->back()->with('success', 'Status sub-item tugas berhasil diperbarui.');
    }

    public function hapusTugas($id = null)
    {
        if ($r = $this->checkAccess()) return $r;

        $penugasanModel = new \App\Models\PenugasanHarianModel();
        $penugasanModel->ensureTableExists();

        $task = $penugasanModel->find($id);
        if (!$task) {
            return redirect()->to(site_url('accounting/pribadi/tugas-saya'))->with('error', 'Penugasan tidak ditemukan.');
        }

        $penugasanModel->delete($id);
        return redirect()->to(site_url('accounting/pribadi/tugas-saya'))->with('success', 'Penugasan harian berhasil dihapus.');
    }

    /**
     * Timeline Kerja
     */
    public function timelineKerja()
    {
        if ($r = $this->checkAccess()) return $r;
        $projectModel = new \App\Models\ProjectModel();

        $projectsOnProgress = $projectModel->whereIn('status', ['on_progress', 'selesai'])->orderBy('tanggal_mulai', 'DESC')->findAll();
        $projectsPending    = $projectModel->whereIn('status', ['penawaran', 'nego', 'deal'])->orderBy('created_at', 'DESC')->findAll();

        $data = array_merge($this->baseData(), [
            'title'            => 'Timeline Kerja (Project Aktif)',
            'active'           => 'timeline-kerja',
            'projects'         => $projectsOnProgress,
            'projects_pending' => $projectsPending,
            'clients'          => $this->db->tableExists('client') ? $this->db->table('client')->get()->getResultArray() : []
        ]);

        return view('accounting/templates/header', $data)
             . view('accounting/templates/sidebar', $data)
             . view('accounting/templates/navbar', $data)
             . view('admin/pribadi/timeline_kerja', $data)
             . view('accounting/templates/footer', $data);
    }

    /**
     * Project Saat Ini
     */
    public function projectSaatIni()
    {
        if ($r = $this->checkAccess()) return $r;

        $projects = [];
        if ($this->db->tableExists('project')) {
            $projects = $this->db->table('project')
                ->select('project.*, client.nama_perusahaan, client.nama_kontak')
                ->join('client', 'client.id = project.client_id', 'left')
                ->orderBy('project.id', 'DESC')
                ->get()->getResultArray();
        }

        $data = array_merge($this->baseData(), [
            'title'    => 'Project Saat Ini',
            'active'   => 'project-saat-ini',
            'projects' => $projects
        ]);

        return view('accounting/templates/header', $data)
             . view('accounting/templates/sidebar', $data)
             . view('accounting/templates/navbar', $data)
             . view('admin/pribadi/project_saat_ini', $data)
             . view('accounting/templates/footer', $data);
    }

    /**
     * Profil Saya
     */
    public function profil()
    {
        if ($r = $this->checkAccess()) return $r;

        $userId    = session()->get('user_id');
        $userModel = new \App\Models\UserModel();
        $userData  = $userId ? $userModel->find($userId) : null;

        if (!$userData) {
            $userData = [
                'id'       => $userId ?: 1,
                'name'     => session()->get('name') ?? 'Accounting Staff',
                'username' => session()->get('username') ?? 'accounting',
                'email'    => session()->get('email') ?? '',
                'role'     => session()->get('role') ?? 'accounting'
            ];
        }

        $karyawan = null;
        if (!empty($userData['karyawan_id']) && $this->db->tableExists('karyawan')) {
            $karyawan = $this->db->table('karyawan')->where('id', $userData['karyawan_id'])->get()->getRowArray();
        }

        $data = array_merge($this->baseData(), [
            'title'    => 'Profil Saya & Pengaturan Akun',
            'active'   => 'profil',
            'userData' => $userData,
            'karyawan' => $karyawan
        ]);

        return view('accounting/templates/header', $data)
             . view('accounting/templates/sidebar', $data)
             . view('accounting/templates/navbar', $data)
             . view('admin/pribadi/profil', $data)
             . view('accounting/templates/footer', $data);
    }

    public function updateProfil()
    {
        if ($r = $this->checkAccess()) return $r;
        $userId    = session()->get('user_id');
        $userModel = new \App\Models\UserModel();
        $name     = $this->request->getPost('name');
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        if ($userId && $userModel->find($userId)) {
            $upd = ['name' => $name, 'email' => $email];
            if (!empty($password)) $upd['password'] = password_hash($password, PASSWORD_BCRYPT);
            $userModel->update($userId, $upd);
            session()->set('name', $name);
            session()->set('email', $email);
        }
        return redirect()->to(base_url('accounting/pribadi/profil'))->with('success', 'Profil berhasil diperbarui!');
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
             . view('accounting/templates/navbar', $data)
             . view('accounting/pribadi/riwayat-audit/index', $data)
             . view('accounting/templates/footer', $data);
    }
}
