<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Pribadi extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    private function checkAccess()
    {
        if (!session()->get('isLoggedIn')) return redirect()->to(base_url('login'));
        if (strtolower(session()->get('role') ?? '') !== 'admin') return redirect()->to($this->getDashboardUrl())->with('error', 'Akses ditolak!');
        return null;
    }

    private function ensureKeluhanTable()
    {
        if (!$this->db->tableExists('keluhan_karyawan')) {
            $forge = \Config\Database::forge();
            $forge->addField([
                'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'karyawan_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'tanggal'           => ['type' => 'DATE', 'null' => true],
                'kategori'          => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'Fasilitas'],
                'judul'             => ['type' => 'VARCHAR', 'constraint' => 255],
                'deskripsi'         => ['type' => 'TEXT', 'null' => true],
                'status'            => ['type' => 'ENUM', 'constraint' => ['Menunggu', 'Diproses', 'Selesai', 'Ditolak'], 'default' => 'Menunggu'],
                'tanggapan'         => ['type' => 'TEXT', 'null' => true],
                'ditanggapi_oleh'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'tanggal_tanggapan' => ['type' => 'DATETIME', 'null' => true],
                'created_at'        => ['type' => 'DATETIME', 'null' => true],
                'updated_at'        => ['type' => 'DATETIME', 'null' => true]
            ]);
            $forge->addKey('id', true);
            $forge->createTable('keluhan_karyawan', true);
        }
    }

    private function getKaryawanId()
    {
        $name = session()->get('name') ?? 'Admin';
        $karyawan = $this->db->table('karyawan')
            ->where('deleted_at', null)
            ->like('nama_lengkap', $name)
            ->get()->getRowArray();
        if (!$karyawan) {
            $karyawan = $this->db->table('karyawan')->where('deleted_at', null)->get()->getRowArray();
        }
        return $karyawan['id'] ?? 1;
    }

    private function ensureAbsensiTable()
    {
        if (!$this->db->tableExists('absensi')) {
            $forge = \Config\Database::forge();
            $forge->addField([
                'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'karyawan_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'tanggal'        => ['type' => 'DATE'],
                'waktu_masuk'    => ['type' => 'TIME', 'null' => true],
                'waktu_pulang'   => ['type' => 'TIME', 'null' => true],
                'status'         => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Hadir'],
                'lokasi_masuk'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'lokasi_pulang'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'keterangan'     => ['type' => 'TEXT', 'null' => true],
                'created_at'     => ['type' => 'DATETIME', 'null' => true],
                'updated_at'     => ['type' => 'DATETIME', 'null' => true],
                'deleted_at'     => ['type' => 'DATETIME', 'null' => true]
            ]);
            $forge->addKey('id', true);
            $forge->createTable('absensi', true);
        }
    }

    public function absensi()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureAbsensiTable();

        $karyawanId = $this->getKaryawanId();
        $today = date('Y-m-d');

        $todayAbsen = $this->db->table('absensi')
            ->where('karyawan_id', $karyawanId)
            ->where('tanggal', $today)
            ->where('deleted_at', null)
            ->get()->getRowArray();

        $riwayatAbsensi = $this->db->table('absensi')
            ->where('karyawan_id', $karyawanId)
            ->where('deleted_at', null)
            ->orderBy('tanggal', 'DESC')
            ->orderBy('id', 'DESC')
            ->limit(30)
            ->get()->getResultArray();

        $currentKaryawan = $this->db->table('karyawan')->where('id', $karyawanId)->get()->getRowArray();

        $data = [
            'title'           => 'Absensi Saya',
            'subtitle'        => 'Pencatatan Kehadiran Mandiri Administrator',
            'active'          => 'absensi-saya',
            'todayAbsen'      => $todayAbsen,
            'riwayatAbsensi'  => $riwayatAbsensi,
            'currentKaryawan' => $currentKaryawan,
            'user'            => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/pribadi/absensi', $data);
    }

    public function checkin()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureAbsensiTable();

        $karyawanId = $this->getKaryawanId();
        $today = date('Y-m-d');
        $currentTime = date('H:i:s');

        $existing = $this->db->table('absensi')
            ->where('karyawan_id', $karyawanId)
            ->where('tanggal', $today)
            ->where('deleted_at', null)
            ->get()->getRowArray();

        if ($existing) {
            return redirect()->to(base_url('admin/absensi-saya'))->with('error', 'Anda sudah melakukan Absen Masuk (Check-in) hari ini.');
        }

        $status = (date('H:i') > '08:30') ? 'Terlambat' : 'Hadir';

        $this->db->table('absensi')->insert([
            'karyawan_id'  => $karyawanId,
            'tanggal'      => $today,
            'waktu_masuk'  => $currentTime,
            'status'       => $status,
            'lokasi_masuk' => 'Kantor CDW Engineering (Presensi Admin)',
            'created_at'   => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('admin/absensi-saya'))->with('success', 'Absen Masuk (Check-in) berhasil dicatat pada jam ' . date('H:i') . ' WIB. Data langsung terhubung ke Direktur.');
    }

    public function checkout()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureAbsensiTable();

        $karyawanId = $this->getKaryawanId();
        $today = date('Y-m-d');
        $currentTime = date('H:i:s');

        $existing = $this->db->table('absensi')
            ->where('karyawan_id', $karyawanId)
            ->where('tanggal', $today)
            ->where('deleted_at', null)
            ->get()->getRowArray();

        if (!$existing) {
            return redirect()->to(base_url('admin/absensi-saya'))->with('error', 'Anda belum melakukan Absen Masuk (Check-in) hari ini.');
        }

        if (!empty($existing['waktu_pulang'])) {
            return redirect()->to(base_url('admin/absensi-saya'))->with('error', 'Anda sudah melakukan Absen Pulang (Check-out) hari ini.');
        }

        $this->db->table('absensi')->where('id', $existing['id'])->update([
            'waktu_pulang'  => $currentTime,
            'lokasi_pulang' => 'Kantor CDW Engineering (Presensi Admin)',
            'updated_at'    => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('admin/absensi-saya'))->with('success', 'Absen Pulang (Check-out) berhasil dicatat pada jam ' . date('H:i') . ' WIB. Data terbarui di sistem Direktur.');
    }

    public function tugas()
    {
        if ($r = $this->checkAccess()) return $r;

        $karyawanId = $this->getKaryawanId();

        $penugasanModel = new \App\Models\PenugasanHarianModel();
        $penugasanModel->ensureTableExists();

        $rawTasks = $penugasanModel
            ->where('deleted_at', null)
            ->groupStart()
                ->where('penerima_role', 'admin')
                ->orWhere('penerima_role', 'all')
                ->orWhere('penerima_id', $karyawanId)
            ->groupEnd()
            ->orderBy('id', 'DESC')
            ->findAll();

        $tasks = [];
        foreach ($rawTasks as $t) {
            $tasks[] = $penugasanModel->getTaskWithItems($t['id']);
        }

        $data = [
            'title'  => 'Tugas Hari Ini',
            'active' => 'tugas-saya',
            'tasks'  => $tasks,
            'user'   => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/pribadi/tugas', $data);
    }

    public function detailTugas($id)
    {
        if ($r = $this->checkAccess()) return $r;

        $penugasanModel = new \App\Models\PenugasanHarianModel();
        $task = $penugasanModel->getTaskWithItems($id);

        if (!$task) {
            return redirect()->to(base_url('admin/tugas-saya'))->with('error', 'Tugas tidak ditemukan.');
        }

        $data = [
            'title'  => 'Detail Tugas Saya',
            'active' => 'tugas-saya',
            'task'   => $task,
            'user'   => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/pribadi/tugas_detail', $data);
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
        if (!$task) {
            return redirect()->to(base_url('admin/tugas-saya'))->with('error', 'Penugasan tidak ditemukan.');
        }

        $markAllCompleted = $this->request->getPost('mark_all_completed');
        if ($markAllCompleted == '1') {
            // Ubah seluruh sub-item sekaligus menjadi 'selesai'
            $this->db->table('penugasan_harian_items')
                ->where('penugasan_id', $id)
                ->update(['status_item' => 'selesai', 'updated_at' => date('Y-m-d H:i:s')]);
        } else {
            // Ubah status sub-item satu per satu dari form
            $itemStatuses = $this->request->getPost('subitem_status');
            if (!empty($itemStatuses) && is_array($itemStatuses)) {
                foreach ($itemStatuses as $itemId => $st) {
                    if (in_array($st, ['pending', 'proses', 'selesai', 'ditunda'])) {
                        $itemModel->update($itemId, ['status_item' => $st]);
                    }
                }
            }
        }

        // Update status tugas utama jika dikirim
        $mainStatus = $this->request->getPost('main_status');
        if (!empty($mainStatus) && in_array($mainStatus, ['pending', 'proses', 'selesai'])) {
            $penugasanModel->update($id, ['status' => $mainStatus]);
        }

        // Auto recalculate main task status
        $penugasanModel->recalculateTaskStatus($id);

        return redirect()->back()->with('success', 'Status sub-item tugas checklist berhasil diperbarui.');
    }

    public function buatLaporanFromTugas($id)
    {
        if ($r = $this->checkAccess()) return $r;

        $penugasanModel = new \App\Models\PenugasanHarianModel();
        $task = $penugasanModel->getTaskWithItems($id);

        if (!$task) {
            return redirect()->to(base_url('admin/tugas-saya'))->with('error', 'Penugasan tidak ditemukan.');
        }

        // Verifikasi apakah seluruh sub-item telah diselesaikan
        $allCompleted = true;
        if (!empty($task['items'])) {
            foreach ($task['items'] as $it) {
                if ($it['status_item'] !== 'selesai') {
                    $allCompleted = false;
                    break;
                }
            }
        }

        if (!$allCompleted) {
            return redirect()->to(base_url('admin/tugas-saya/detail/' . $id))->with('error', 'Seluruh Rincian Sub-Item Tugas Checklist harus diselesaikan dari pending menjadi selesai sebelum dapat membuat Laporan Harian!');
        }

        // Redirect ke form tambah/edit laporan harian dengan query parameter from_task
        return redirect()->to(base_url('admin/laporan/kerja-harian/tambah?from_task=' . $id))->with('info', 'Sistem telah menyiapkan draft Laporan Harian dari Tugas Direktur. Silakan periksa, edit, atau pratinjau sebelum dikirim ke Direktur.');
    }

    public function laporanHarian()
    {
        if ($r = $this->checkAccess()) return $r;
        return view('admin/pribadi/laporan_harian', ['title' => 'Laporan Kerja Harian', 'active' => 'laporan-harian-saya', 'user' => ['name' => session()->get('name'), 'role' => session()->get('role')]]);
    }

    public function storeLaporan()
    {
        if ($r = $this->checkAccess()) return $r;
        return redirect()->to(base_url('admin/laporan-harian-saya'))->with('success', 'Laporan berhasil disimpan!');
    }

    // ===============================================
    // KELUHAN SAYA (ADMIN)
    // ===============================================
    public function keluhan()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureKeluhanTable();

        $keluhanList = $this->db->table('keluhan_karyawan')->orderBy('id', 'DESC')->get()->getResultArray();

        $data = [
            'title'       => 'Keluhan Saya',
            'active'      => 'keluhan-saya',
            'keluhanList' => $keluhanList,
            'user'        => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/pribadi/keluhan', $data);
    }

    public function storeKeluhan()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureKeluhanTable();

        $this->db->table('keluhan_karyawan')->insert([
            'karyawan_id' => 1,
            'tanggal'     => date('Y-m-d'),
            'kategori'    => $this->request->getPost('kategori') ?: 'Fasilitas',
            'judul'       => $this->request->getPost('judul'),
            'deskripsi'   => $this->request->getPost('deskripsi'),
            'status'      => 'Menunggu',
            'created_at'  => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('admin/keluhan-saya'))->with('success', 'Keluhan Anda berhasil dikirim ke Direktur/Manajemen.');
    }

    public function detailKeluhan($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureKeluhanTable();

        $k = $this->db->table('keluhan_karyawan')->where('id', $id)->get()->getRowArray();
        if (!$k) {
            return redirect()->to(base_url('admin/keluhan-saya'))->with('error', 'Keluhan tidak ditemukan.');
        }

        $data = [
            'title'  => 'Detail Keluhan Saya',
            'active' => 'keluhan-saya',
            'k'      => $k,
            'user'   => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/pribadi/keluhan_detail', $data);
    }

    public function editKeluhan($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureKeluhanTable();

        $k = $this->db->table('keluhan_karyawan')->where('id', $id)->get()->getRowArray();
        if (!$k) {
            return redirect()->to(base_url('admin/keluhan-saya'))->with('error', 'Keluhan tidak ditemukan.');
        }

        $data = [
            'title'  => 'Edit Keluhan Saya',
            'active' => 'keluhan-saya',
            'k'      => $k,
            'user'   => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/pribadi/keluhan_edit', $data);
    }

    public function updateKeluhan()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureKeluhanTable();

        $id = $this->request->getPost('id');

        $this->db->table('keluhan_karyawan')->where('id', $id)->update([
            'kategori'   => $this->request->getPost('kategori'),
            'judul'      => $this->request->getPost('judul'),
            'deskripsi'  => $this->request->getPost('deskripsi'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('admin/keluhan-saya'))->with('success', 'Keluhan berhasil diperbarui.');
    }

    public function deleteKeluhan($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureKeluhanTable();

        $this->db->table('keluhan_karyawan')->where('id', $id)->delete();
        return redirect()->to(base_url('admin/keluhan-saya'))->with('success', 'Keluhan berhasil dihapus.');
    }

    public function pengajuanCuti()
    {
        if ($r = $this->checkAccess()) return $r;
        return view('admin/pribadi/pengajuan_cuti', ['title' => 'Form Pengajuan Cuti', 'active' => 'form-pengajuan', 'user' => ['name' => session()->get('name'), 'role' => session()->get('role')]]);
    }

    public function storeCuti()
    {
        if ($r = $this->checkAccess()) return $r;
        return redirect()->to(base_url('admin/form-pengajuan/cuti'))->with('success', 'Pengajuan cuti berhasil dikirim!');
    }

    public function slipGaji()
    {
        if ($r = $this->checkAccess()) return $r;
        return view('admin/pribadi/slip_gaji', ['title' => 'Slip Gaji', 'active' => 'slip-gaji', 'user' => ['name' => session()->get('name'), 'role' => session()->get('role')]]);
    }

    private function ensureClientColumns()
    {
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();
        if ($db->tableExists('client')) {
            $fields = $db->getFieldNames('client');
            $newColumns = [];
            if (!in_array('nama_perusahaan', $fields)) {
                $newColumns['nama_perusahaan'] = ['type' => 'VARCHAR', 'constraint' => 200, 'null' => true];
            }
            if (!in_array('nama_kontak', $fields)) {
                $newColumns['nama_kontak'] = ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true];
            }
            if (!empty($newColumns)) {
                $forge->addColumn('client', $newColumns);
            }
        }
    }

    private function ensureTimelineTablesExist()
    {
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();

        if (!$db->tableExists('proyek_timeline')) {
            $forge->addField([
                'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'proyek_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'nama_tugas'      => ['type' => 'VARCHAR', 'constraint' => 200],
                'deskripsi'       => ['type' => 'TEXT', 'null' => true],
                'karyawan_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'tanggal_mulai'   => ['type' => 'DATE', 'null' => true],
                'tanggal_selesai' => ['type' => 'DATE', 'null' => true],
                'tipe_periode'    => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'harian'],
                'periode_ke'      => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
                'progres_persen'  => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'status'          => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'pending'],
                'created_at'      => ['type' => 'DATETIME', 'null' => true],
                'updated_at'      => ['type' => 'DATETIME', 'null' => true]
            ]);
            $forge->addKey('id', true);
            $forge->createTable('proyek_timeline', true);
        }
    }

    public function timelineKerja()
    {
        if ($r = $this->checkAccess()) return $r;

        $projectModel  = new \App\Models\ProjectModel();
        $userModel     = new \App\Models\UserModel();

        $this->ensureClientColumns();
        $this->ensureTimelineTablesExist();

        $projectsOnProgress = $projectModel->whereIn('status', ['on_progress', 'selesai'])->orderBy('tanggal_mulai', 'DESC')->findAll();
        $projectsPending    = $projectModel->whereIn('status', ['penawaran', 'nego', 'deal'])->orderBy('created_at', 'DESC')->findAll();

        $data = [
            'title'            => 'Timeline Kerja (Project Aktif)',
            'active'           => 'timeline-kerja',
            'projects'         => $projectsOnProgress,
            'projects_pending' => $projectsPending,
            'clients'          => $this->db->table('client')->orderBy('nama_perusahaan', 'ASC')->get()->getResultArray(),
            'managers'         => $userModel->where('status', 'active')->findAll(),
            'user'             => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/pribadi/timeline_kerja', $data);
    }

    public function aktifkanProyekTimeline()
    {
        if ($r = $this->checkAccess()) return $r;
        $proyekId = $this->request->getPost('proyek_id');
        if (!empty($proyekId)) {
            $projectModel = new \App\Models\ProjectModel();
            $projectModel->update($proyekId, ['status' => 'on_progress']);
            return redirect()->to(base_url('admin/timeline-kerja/detail/' . $proyekId))->with('success', 'Project berhasil diaktifkan ke Timeline Kerja.');
        }
        return redirect()->back()->with('error', 'Silakan pilih project terlebih dahulu.');
    }

    public function detailTimelineKerja($id)
    {
        if ($r = $this->checkAccess()) return $r;

        $projectModel  = new \App\Models\ProjectModel();
        $timelineModel = new \App\Models\ProyekTimelineModel();
        $userModel     = new \App\Models\UserModel();

        $this->ensureTimelineTablesExist();
        $this->ensureClientColumns();

        $project = $projectModel->find($id);
        if (!$project) return redirect()->to(base_url('admin/timeline-kerja'))->with('error', 'Project timeline tidak ditemukan.');
        
        $client = null;
        if (!empty($project['client_id'])) {
            $client = $this->db->table('client')->where('id', $project['client_id'])->get()->getRowArray();
        }

        $manager = null;
        if (!empty($project['project_manager_id'])) {
            $manager = $userModel->find($project['project_manager_id']);
        }

        $karyawanList = $userModel->where('status', 'active')->findAll();

        $data = [
            'title'    => 'Detail Timeline: ' . $project['nama_project'],
            'active'   => 'timeline-kerja',
            'project'  => $project,
            'client'   => $client,
            'manager'  => $manager,
            'timeline' => $timelineModel->getTimelineByProyek($id),
            'karyawan' => $karyawanList,
            'user'     => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];
        
        return view('admin/pribadi/timeline_kerja_detail', $data);
    }

    public function simpanTaskTimeline()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTimelineTablesExist();

        $timelineModel = new \App\Models\ProyekTimelineModel();
        $proyekId  = $this->request->getPost('proyek_id');
        $namaTugas = $this->request->getPost('nama_tugas');

        if (empty($proyekId) || empty($namaTugas)) {
            return redirect()->back()->with('error', 'Nama tugas wajib diisi.');
        }

        $status = $this->request->getPost('status') ?: 'pending';
        $progres = 0;
        if ($status === 'on_progress') $progres = 50;
        if ($status === 'done') $progres = 100;
        if ($this->request->getPost('progres_persen') !== null) {
            $progres = (int) $this->request->getPost('progres_persen');
        }

        $timelineModel->insert([
            'proyek_id'       => $proyekId,
            'nama_tugas'      => $namaTugas,
            'deskripsi'       => $this->request->getPost('deskripsi'),
            'karyawan_id'     => $this->request->getPost('karyawan_id') ?: null,
            'tipe_periode'    => $this->request->getPost('tipe_periode') ?: 'harian',
            'periode_ke'      => (int) ($this->request->getPost('periode_ke') ?: 1),
            'tanggal_mulai'   => $this->request->getPost('tanggal_mulai') ?: date('Y-m-d'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai') ?: date('Y-m-d'),
            'progres_persen'  => $progres,
            'status'          => $status
        ]);

        return redirect()->to(base_url('admin/timeline-kerja/detail/' . $proyekId))->with('success', 'Tugas/Milestone timeline berhasil ditambahkan.');
    }

    public function updateTaskStatusTimeline()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTimelineTablesExist();

        $timelineModel = new \App\Models\ProyekTimelineModel();
        $id            = $this->request->getPost('id');
        $status        = $this->request->getPost('status') ?: 'pending';
        $customProgres = $this->request->getPost('progres_persen');

        $progres = 0;
        if ($status === 'on_progress') $progres = 50;
        if ($status === 'done' || $status === 'selesai') $progres = 100;
        if ($customProgres !== null && $customProgres !== '') {
            $progres = (int) $customProgres;
        }

        $task = $timelineModel->find($id);
        if ($task) {
            $timelineModel->update($id, [
                'status'         => $status,
                'progres_persen' => $progres
            ]);

            return redirect()->to(base_url('admin/timeline-kerja/detail/' . $task['proyek_id']))->with('success', 'Status & Progres task berhasil diperbarui.');
        }
        return redirect()->back()->with('error', 'Task tidak ditemukan.');
    }

    public function deleteTaskTimeline($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $timelineModel = new \App\Models\ProyekTimelineModel();
        $task = $timelineModel->find($id);
        if ($task) {
            $proyekId = $task['proyek_id'];
            $timelineModel->delete($id);

            return redirect()->to(base_url('admin/timeline-kerja/detail/' . $proyekId))->with('success', 'Tugas timeline berhasil dihapus.');
        }
        return redirect()->back()->with('error', 'Tugas tidak ditemukan.');
    }

    public function selesaikanProyekTimeline($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $projectModel = new \App\Models\ProjectModel();
        $project = $projectModel->find($id);
        if ($project) {
            $projectModel->update($id, ['status' => 'selesai']);
            return redirect()->to(base_url('admin/timeline-kerja/detail/' . $id))->with('success', 'Status proyek berhasil ditandai Selesai.');
        }
        return redirect()->back()->with('error', 'Proyek tidak ditemukan.');
    }

    public function deleteProyekTimeline($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $projectModel = new \App\Models\ProjectModel();
        $project = $projectModel->find($id);
        if ($project) {
            $projectModel->update($id, ['status' => 'penawaran']);
            return redirect()->to(base_url('admin/timeline-kerja'))->with('success', 'Proyek berhasil dikeluarkan dari Timeline Kerja.');
        }
        return redirect()->back()->with('error', 'Proyek tidak ditemukan.');
    }

    public function projectSaatIni()
    {
        if ($r = $this->checkAccess()) return $r;

        $projects = [];
        if ($this->db->tableExists('project')) {
            $builder = $this->db->table('project')
                ->select('project.*, client.nama_perusahaan, client.nama_kontak')
                ->join('client', 'client.id = project.client_id', 'left')
                ->orderBy('project.id', 'DESC');

            $projects = $builder->get()->getResultArray();
        }

        $data = [
            'title'    => 'Project Saat Ini',
            'active'   => 'project-saat-ini',
            'projects' => $projects,
            'user'     => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/pribadi/project_saat_ini', $data);
    }

    public function profil()
    {
        if ($r = $this->checkAccess()) return $r;

        $userId    = session()->get('user_id') ?? session()->get('id');
        $userModel = new \App\Models\UserModel();
        $userData  = null;

        if ($userId) {
            $userData = $userModel->find($userId);
        }

        if (!$userData) {
            $userData = [
                'id'         => $userId ?: 1,
                'name'       => session()->get('name') ?? 'Admin Systems',
                'username'   => session()->get('username') ?? 'admin',
                'email'      => session()->get('email') ?? 'admin@cdw.co.id',
                'role'       => session()->get('role') ?? 'admin',
                'no_hp'      => session()->get('no_hp') ?? '081234567890',
                'created_at' => date('Y-m-d H:i:s')
            ];
        }

        $data = [
            'title'    => 'Profil Saya & Pengaturan Akun',
            'active'   => 'profil',
            'userData' => $userData,
            'user'     => ['name' => $userData['name'] ?? session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/pribadi/profil', $data);
    }

    public function updateProfil()
    {
        if ($r = $this->checkAccess()) return $r;

        $userId    = session()->get('user_id') ?? session()->get('id');
        $userModel = new \App\Models\UserModel();

        $name     = $this->request->getPost('name');
        $email    = $this->request->getPost('email');
        $noHp     = $this->request->getPost('no_hp');
        $password = $this->request->getPost('password');

        if ($userId && $userModel->find($userId)) {
            $updateData = [
                'name'  => $name,
                'email' => $email
            ];

            if ($this->db->fieldExists('no_hp', 'users')) {
                $updateData['no_hp'] = $noHp;
            }

            if (!empty($password)) {
                $updateData['password'] = password_hash($password, PASSWORD_BCRYPT);
            }

            $userModel->update($userId, $updateData);

            // Update session data
            session()->set('name', $name);
            session()->set('email', $email);
        }

        return redirect()->to(base_url('admin/profil'))->with('success', 'Profil dan informasi akun Anda berhasil diperbarui!');
    }

    public function hapusTugas($id = null)
    {
        if ($r = $this->checkAccess()) return $r;

        $penugasanModel = new \App\Models\PenugasanHarianModel();
        $penugasanModel->ensureTableExists();

        $task = $penugasanModel->find($id);
        if (!$task) {
            return redirect()->to(base_url('admin/tugas-saya'))->with('error', 'Penugasan tidak ditemukan.');
        }

        $penugasanModel->delete($id);
        return redirect()->to(base_url('admin/tugas-saya'))->with('success', 'Penugasan harian berhasil dihapus.');
    }
}