<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Laporan extends BaseController
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

    private function ensureLaporanHarianTable()
    {
        if (!$this->db->tableExists('laporan_harian')) {
            $forge = \Config\Database::forge();
            $forge->addField([
                'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'karyawan_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'tanggal'           => ['type' => 'DATE'],
                'judul'             => ['type' => 'VARCHAR', 'constraint' => 255],
                'deskripsi'         => ['type' => 'TEXT', 'null' => true],
                'lampiran'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'status'            => ['type' => 'ENUM', 'constraint' => ['Draft', 'Terkirim', 'Disetujui', 'Revisi', 'Ditolak'], 'default' => 'Terkirim'],
                'komentar_direktur' => ['type' => 'TEXT', 'null' => true],
                'direview_oleh'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'created_at'        => ['type' => 'DATETIME', 'null' => true],
                'updated_at'        => ['type' => 'DATETIME', 'null' => true]
            ]);
            $forge->addKey('id', true);
            $forge->createTable('laporan_harian', true);
        }
    }

    public function dashboard()
    {
        if ($r = $this->checkAccess()) return $r;
        return view('admin/laporan/dashboard', ['title' => 'Dashboard Laporan', 'active' => 'laporan-dashboard', 'user' => ['name' => session()->get('name'), 'role' => session()->get('role')]]);
    }

    public function kerjaHarian()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureLaporanHarianTable();

        $laporanList = $this->db->table('laporan_harian')
            ->select('laporan_harian.*, karyawan.nama_lengkap, karyawan.jabatan')
            ->join('karyawan', 'karyawan.id = laporan_harian.karyawan_id', 'left')
            ->orderBy('laporan_harian.tanggal', 'DESC')
            ->orderBy('laporan_harian.id', 'DESC')
            ->get()->getResultArray();

        $data = [
            'title'       => 'Laporan Kerja Harian',
            'active'      => 'laporan-kerja',
            'laporanList' => $laporanList,
            'user'        => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/laporan/kerja_harian', $data);
    }

    public function tambahKerjaHarian()
    {
        if ($r = $this->checkAccess()) return $r;

        $fromTaskId = $this->request->getGet('from_task');
        $prefilledJudul = '';
        $prefilledDeskripsi = '';
        $fromTaskData = null;

        if (!empty($fromTaskId)) {
            $penugasanModel = new \App\Models\PenugasanHarianModel();
            $task = $penugasanModel->getTaskWithItems($fromTaskId);
            if ($task) {
                $fromTaskData = $task;
                $prefilledJudul = 'Laporan: ' . $task['judul_tugas'];

                $prefilledDeskripsi = !empty($task['deskripsi_tugas']) ? "Arahan Direktur: " . $task['deskripsi_tugas'] . "\n\n" : "";
                if (!empty($task['items'])) {
                    $prefilledDeskripsi .= "Rincian Item Tugas Yang Diselesaikan:\n";
                    foreach ($task['items'] as $idx => $it) {
                        $prefilledDeskripsi .= ($idx + 1) . ". [SELESAI] " . $it['judul_item'];
                        if (!empty($it['deskripsi_item'])) $prefilledDeskripsi .= " - " . $it['deskripsi_item'];
                        $prefilledDeskripsi .= "\n";
                    }
                }
            }
        }

        $data = [
            'title'              => 'Tambah Laporan Kerja Harian',
            'active'             => 'laporan-kerja',
            'todayDate'          => date('Y-m-d'),
            'fromTaskId'         => $fromTaskId,
            'prefilledJudul'     => $prefilledJudul,
            'prefilledDeskripsi' => $prefilledDeskripsi,
            'fromTaskData'       => $fromTaskData,
            'user'               => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/laporan/kerja_harian_tambah', $data);
    }

    public function simpanKerjaHarian()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureLaporanHarianTable();

        $karyawanId = $this->getKaryawanId();
        $fromTaskId = $this->request->getPost('from_task_id');

        $this->db->table('laporan_harian')->insert([
            'karyawan_id' => $karyawanId,
            'tanggal'     => $this->request->getPost('tanggal') ?: date('Y-m-d'),
            'judul'       => $this->request->getPost('judul'),
            'deskripsi'   => $this->request->getPost('deskripsi'),
            'status'      => 'Terkirim',
            'created_at'  => date('Y-m-d H:i:s')
        ]);

        $newLaporanId = $this->db->insertID();

        if (!empty($fromTaskId)) {
            $penugasanModel = new \App\Models\PenugasanHarianModel();
            $penugasanModel->update($fromTaskId, [
                'laporan_harian_id' => $newLaporanId,
                'status'            => 'selesai'
            ]);
        }

        return redirect()->to(base_url('admin/laporan/kerja-harian'))->with('success', 'Laporan kerja harian berhasil diperiksa dan dikirim ke Direktur!');
    }

    public function detailKerjaHarian($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureLaporanHarianTable();

        $laporan = $this->db->table('laporan_harian')
            ->select('laporan_harian.*, karyawan.nama_lengkap, karyawan.nik, karyawan.jabatan')
            ->join('karyawan', 'karyawan.id = laporan_harian.karyawan_id', 'left')
            ->where('laporan_harian.id', $id)
            ->get()->getRowArray();

        if (!$laporan) {
            return redirect()->to(base_url('admin/laporan/kerja-harian'))->with('error', 'Laporan tidak ditemukan.');
        }

        $data = [
            'title'   => 'Detail Laporan Kerja Harian',
            'active'  => 'laporan-kerja',
            'laporan' => $laporan,
            'user'    => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/laporan/kerja_harian_detail', $data);
    }

    public function editKerjaHarian($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureLaporanHarianTable();

        $laporan = $this->db->table('laporan_harian')->where('id', $id)->get()->getRowArray();
        if (!$laporan) {
            return redirect()->to(base_url('admin/laporan/kerja-harian'))->with('error', 'Laporan tidak ditemukan.');
        }

        $data = [
            'title'   => 'Edit Laporan Kerja Harian',
            'active'  => 'laporan-kerja',
            'laporan' => $laporan,
            'user'    => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/laporan/kerja_harian_edit', $data);
    }

    public function updateKerjaHarian()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureLaporanHarianTable();

        $id = $this->request->getPost('id');

        $this->db->table('laporan_harian')->where('id', $id)->update([
            'tanggal'    => $this->request->getPost('tanggal'),
            'judul'      => $this->request->getPost('judul'),
            'deskripsi'  => $this->request->getPost('deskripsi'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('admin/laporan/kerja-harian'))->with('success', 'Laporan kerja harian berhasil diperbarui!');
    }

    public function deleteKerjaHarian($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureLaporanHarianTable();

        $this->db->table('laporan_harian')->where('id', $id)->delete();
        return redirect()->to(base_url('admin/laporan/kerja-harian'))->with('success', 'Laporan kerja harian berhasil dihapus.');
    }

    public function keluhan()
    {
        if ($r = $this->checkAccess()) return $r;

        $keluhanModel  = new \App\Models\KeluhanKaryawanModel();
        $karyawanModel = new \App\Models\KaryawanModel();

        $status   = $this->request->getGet('status');
        $kategori = $this->request->getGet('kategori');
        $search   = $this->request->getGet('q');

        $keluhanList = $keluhanModel->getFilteredKeluhan($status, $kategori, $search);
        $statistik   = $keluhanModel->getStatistik();
        
        $userId     = session()->get('user_id');
        $karyawanId = session()->get('karyawan_id');
        if (!$karyawanId && $userId) {
            $u = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
            $karyawanId = $u['karyawan_id'] ?? null;
            if (!$karyawanId && !empty($u['email'])) {
                $kar = $this->db->table('karyawan')->where('email', $u['email'])->get()->getRowArray();
                if ($kar) $karyawanId = $kar['id'];
            }
        }

        $userKaryawan = null;
        if ($karyawanId) {
            $userKaryawan = $karyawanModel->find($karyawanId);
        }

        $karyawanList = $karyawanModel->orderBy('nama_lengkap', 'ASC')->findAll();

        $data = [
            'title'          => 'Laporan Keluhan Karyawan (Terhubung Ke Direktur)',
            'subtitle'       => 'Pengelolaan & Pemantauan Keluhan Terkoneksi Real-Time Ke Direktur',
            'active'         => 'laporan-keluhan',
            'keluhanList'    => $keluhanList,
            'statistik'      => $statistik,
            'filterStatus'   => $status,
            'filterKategori' => $kategori,
            'search'         => $search,
            'userKaryawan'   => $userKaryawan,
            'karyawanList'   => $karyawanList,
            'kategoriList'   => $keluhanModel->kategoriList
        ];

        return view('admin/laporan/keluhan', $data);
    }

    public function simpanKeluhan()
    {
        if ($r = $this->checkAccess()) return $r;

        $keluhanModel = new \App\Models\KeluhanKaryawanModel();
        
        $karyawanId = $this->request->getPost('karyawan_id');
        $judul      = trim($this->request->getPost('judul') ?? '');
        $kategori   = $this->request->getPost('kategori');
        $deskripsi  = trim($this->request->getPost('deskripsi') ?? '');
        $tanggal    = $this->request->getPost('tanggal') ?: date('Y-m-d');

        if (empty($karyawanId) || empty($judul) || empty($kategori) || empty($deskripsi)) {
            return redirect()->back()->withInput()->with('error', 'Semua bidang bertanda bintang (*) wajib diisi.');
        }

        $inserted = $keluhanModel->insert([
            'karyawan_id' => $karyawanId,
            'tanggal'     => $tanggal,
            'kategori'    => $kategori,
            'judul'       => $judul,
            'deskripsi'   => $deskripsi,
            'status'      => 'baru',
        ]);

        if (!$inserted) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan keluhan. Silakan coba lagi.');
        }

        return redirect()->to(base_url('admin/laporan/keluhan'))->with('success', 'Keluhan berhasil dilaporkan dan terhubung secara real-time ke Direktur.');
    }

    public function tambahKeluhan()
    {
        if ($r = $this->checkAccess()) return $r;

        $keluhanModel  = new \App\Models\KeluhanKaryawanModel();
        $karyawanModel = new \App\Models\KaryawanModel();

        $userId     = session()->get('user_id');
        $karyawanId = session()->get('karyawan_id');
        if (!$karyawanId && $userId) {
            $u = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
            $karyawanId = $u['karyawan_id'] ?? null;
            if (!$karyawanId && !empty($u['email'])) {
                $kar = $this->db->table('karyawan')->where('email', $u['email'])->get()->getRowArray();
                if ($kar) $karyawanId = $kar['id'];
            }
        }

        $userKaryawan = null;
        if ($karyawanId) {
            $userKaryawan = $karyawanModel->find($karyawanId);
        }

        $data = [
            'title'        => 'Laporkan Keluhan Baru',
            'subtitle'     => 'Form Input Keluhan Karyawan',
            'active'       => 'laporan-keluhan',
            'userKaryawan' => $userKaryawan,
            'karyawanList' => $karyawanModel->orderBy('nama_lengkap', 'ASC')->findAll(),
            'kategoriList' => $keluhanModel->kategoriList
        ];

        return view('admin/laporan/keluhan_tambah', $data);
    }

    public function detailKeluhan($id)
    {
        if ($r = $this->checkAccess()) return $r;

        $keluhanModel = new \App\Models\KeluhanKaryawanModel();
        $keluhan = $keluhanModel->getDetailWithKaryawan($id);

        if (!$keluhan) {
            return redirect()->to(base_url('admin/laporan/keluhan'))->with('error', 'Data keluhan tidak ditemukan.');
        }

        $data = [
            'title'    => 'Detail Keluhan - ' . esc($keluhan['judul']),
            'subtitle' => 'Informasi Lengkap & Status Tanggapan Keluhan',
            'active'   => 'laporan-keluhan',
            'keluhan'  => $keluhan
        ];

        return view('admin/laporan/keluhan_detail', $data);
    }

    public function deleteKeluhan($id)
    {
        if ($r = $this->checkAccess()) return $r;

        $keluhanModel = new \App\Models\KeluhanKaryawanModel();
        $keluhanModel->delete($id);

        return redirect()->to(base_url('admin/laporan/keluhan'))->with('success', 'Data keluhan berhasil dihapus.');
    }
}
