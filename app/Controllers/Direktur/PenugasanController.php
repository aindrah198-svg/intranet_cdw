<?php
namespace App\Controllers\Direktur;

use App\Controllers\BaseController;
use App\Models\PenugasanHarianModel;
use App\Models\PenugasanHarianItemModel;
use App\Models\UserModel;

class PenugasanController extends BaseController
{
    protected $penugasanModel;
    protected $itemModel;
    protected $userModel;
    protected $db;

    public function __construct()
    {
        $this->db             = \Config\Database::connect();
        $this->penugasanModel = new PenugasanHarianModel();
        $this->itemModel      = new PenugasanHarianItemModel();
        $this->userModel      = new UserModel();
        
        // Ensure tables exist on first run
        $this->penugasanModel->ensureTableExists();
    }

    private function checkAccess()
    {
        if (!session()->get('isLoggedIn')) return redirect()->to(base_url('login'));
        if (strtolower(session()->get('role') ?? '') !== 'direktur') return redirect()->to(base_url('login'));
        return null;
    }

    private function getUsersWithAccounts()
    {
        // Ambil data karyawan yang memiliki akun user aktif di sistem
        $builder = $this->db->table('karyawan')
            ->select('karyawan.id as karyawan_id, karyawan.nama_lengkap, karyawan.nik, karyawan.jabatan, karyawan.departemen, users.id as user_id, users.username, users.role')
            ->join('users', 'users.karyawan_id = karyawan.id OR users.username = karyawan.nik', 'inner')
            ->where('karyawan.deleted_at', null)
            ->orderBy('karyawan.nama_lengkap', 'ASC');

        return $builder->get()->getResultArray();
    }

    public function index()
    {
        if ($r = $this->checkAccess()) return $r;

        $filterStatus  = $this->request->getGet('status');
        $filterRole    = $this->request->getGet('role');
        $filterTanggal = $this->request->getGet('tanggal') ?: date('Y-m-d');
        $search        = $this->request->getGet('search');

        $builder = $this->penugasanModel->orderBy('created_at', 'DESC');

        if (!empty($filterStatus)) {
            $builder->where('status', $filterStatus);
        }
        if (!empty($filterRole)) {
            $builder->where('penerima_role', $filterRole);
        }
        if (!empty($filterTanggal)) {
            $builder->where('tanggal_tugas', $filterTanggal);
        }
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('judul_tugas', $search)
                    ->orLike('deskripsi_tugas', $search)
                    ->groupEnd();
        }

        $rawTasks = $builder->findAll();
        $tasks = [];
        foreach ($rawTasks as $t) {
            $tasks[] = $this->penugasanModel->getTaskWithItems($t['id']);
        }

        $stats = $this->penugasanModel->getStatusStats($filterRole, $filterTanggal);

        $data = [
            'title'          => 'Penugasan Harian (Direktur)',
            'subtitle'       => 'Kelola & Delegasikan Tugas Harian Kepada Karyawan',
            'active'         => 'penugasan',
            'tasks'          => $tasks,
            'stats'          => $stats,
            'filterStatus'   => $filterStatus,
            'filterRole'     => $filterRole,
            'filterTanggal'  => $filterTanggal,
            'search'         => $search,
            'user'           => ['name' => session()->get('name') ?? 'Direktur', 'role' => 'direktur']
        ];

        return view('direktur/penugasan/index', ['data' => $data]);
    }

    public function tambah()
    {
        if ($r = $this->checkAccess()) return $r;

        $karyawanList = $this->getUsersWithAccounts();

        $data = [
            'title'        => 'Tambah Penugasan Harian Baru',
            'active'       => 'penugasan',
            'karyawanList' => $karyawanList,
            'todayDate'    => date('Y-m-d'),
            'user'         => ['name' => session()->get('name') ?? 'Direktur', 'role' => 'direktur']
        ];

        return view('direktur/penugasan/tambah', ['data' => $data]);
    }

    public function store()
    {
        if ($r = $this->checkAccess()) return $r;

        $rules = [
            'judul_tugas'   => 'required|min_length[3]|max_length[255]',
            'tanggal_tugas' => 'required|valid_date[Y-m-d]',
            'prioritas'     => 'required|in_list[rendah,sedang,tinggi,mendesak]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $penerimaKaryawanId = $this->request->getPost('karyawan_id');
        $penerimaRole = 'all';

        if (!empty($penerimaKaryawanId)) {
            $userAcc = $this->db->table('users')->where('karyawan_id', $penerimaKaryawanId)->get()->getRowArray();
            if ($userAcc) {
                $penerimaRole = $userAcc['role'];
            }
        }

        $saveHeader = [
            'pemberi_id'      => session()->get('user_id') ?: 1,
            'pemberi_role'    => 'direktur',
            'penerima_role'   => $penerimaRole,
            'penerima_id'     => !empty($penerimaKaryawanId) ? $penerimaKaryawanId : null,
            'judul_tugas'     => $this->request->getPost('judul_tugas'),
            'deskripsi_tugas' => $this->request->getPost('deskripsi_tugas'),
            'tanggal_tugas'   => $this->request->getPost('tanggal_tugas') ?: date('Y-m-d'),
            'tenggat_waktu'   => $this->request->getPost('tenggat_waktu') ?: '17:00',
            'prioritas'       => $this->request->getPost('prioritas'),
            'status'          => 'pending'
        ];

        $penugasanId = $this->penugasanModel->insert($saveHeader);

        if ($penugasanId) {
            $itemTitles = $this->request->getPost('item_judul') ?? [];
            $itemDescs  = $this->request->getPost('item_deskripsi') ?? [];

            if (!empty($itemTitles) && is_array($itemTitles)) {
                foreach ($itemTitles as $idx => $title) {
                    $cleanTitle = trim($title);
                    if (!empty($cleanTitle)) {
                        $this->itemModel->insert([
                            'penugasan_id'   => $penugasanId,
                            'judul_item'     => $cleanTitle,
                            'deskripsi_item' => isset($itemDescs[$idx]) ? trim($itemDescs[$idx]) : '',
                            'status_item'    => 'pending'
                        ]);
                    }
                }
            } else {
                $this->itemModel->insert([
                    'penugasan_id'   => $penugasanId,
                    'judul_item'     => $saveHeader['judul_tugas'],
                    'deskripsi_item' => $saveHeader['deskripsi_tugas'],
                    'status_item'    => 'pending'
                ]);
            }

            return redirect()->to(base_url('direktur/penugasan'))->with('success', 'Penugasan harian baru berhasil disimpan!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat penugasan harian.');
        }
    }

    public function detail($id = null)
    {
        if ($r = $this->checkAccess()) return $r;

        $task = $this->penugasanModel->getTaskWithItems($id);
        if (!$task) {
            return redirect()->to(base_url('direktur/penugasan'))->with('error', 'Tugas penugasan tidak ditemukan.');
        }

        // Ambil info karyawan penerima
        $penerimaKaryawan = null;
        if (!empty($task['penerima_id'])) {
            $penerimaKaryawan = $this->db->table('karyawan')->where('id', $task['penerima_id'])->get()->getRowArray();
        }

        $data = [
            'title'            => 'Detail Penugasan Harian',
            'active'           => 'penugasan',
            'task'             => $task,
            'penerimaKaryawan' => $penerimaKaryawan,
            'user'             => ['name' => session()->get('name') ?? 'Direktur', 'role' => 'direktur']
        ];

        return view('direktur/penugasan/detail', ['data' => $data]);
    }

    public function edit($id = null)
    {
        if ($r = $this->checkAccess()) return $r;

        $task = $this->penugasanModel->getTaskWithItems($id);
        if (!$task) {
            return redirect()->to(base_url('direktur/penugasan'))->with('error', 'Penugasan tidak ditemukan.');
        }

        $karyawanList = $this->getUsersWithAccounts();

        $data = [
            'title'        => 'Edit Penugasan Harian',
            'active'       => 'penugasan',
            'task'         => $task,
            'karyawanList' => $karyawanList,
            'user'         => ['name' => session()->get('name') ?? 'Direktur', 'role' => 'direktur']
        ];

        return view('direktur/penugasan/edit', ['data' => $data]);
    }

    public function update($id = null)
    {
        if ($r = $this->checkAccess()) return $r;

        $task = $this->penugasanModel->find($id);
        if (!$task) {
            return redirect()->to(base_url('direktur/penugasan'))->with('error', 'Penugasan tidak ditemukan.');
        }

        $rules = [
            'judul_tugas'   => 'required|min_length[3]|max_length[255]',
            'tanggal_tugas' => 'required|valid_date[Y-m-d]',
            'prioritas'     => 'required|in_list[rendah,sedang,tinggi,mendesak]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $penerimaKaryawanId = $this->request->getPost('karyawan_id');
        $penerimaRole = 'all';

        if (!empty($penerimaKaryawanId)) {
            $userAcc = $this->db->table('users')->where('karyawan_id', $penerimaKaryawanId)->get()->getRowArray();
            if ($userAcc) {
                $penerimaRole = $userAcc['role'];
            }
        }

        $updateHeader = [
            'penerima_role'   => $penerimaRole,
            'penerima_id'     => !empty($penerimaKaryawanId) ? $penerimaKaryawanId : null,
            'judul_tugas'     => $this->request->getPost('judul_tugas'),
            'deskripsi_tugas' => $this->request->getPost('deskripsi_tugas'),
            'tanggal_tugas'   => $this->request->getPost('tanggal_tugas'),
            'tenggat_waktu'   => $this->request->getPost('tenggat_waktu') ?: '17:00',
            'prioritas'       => $this->request->getPost('prioritas')
        ];

        $this->penugasanModel->update($id, $updateHeader);

        // Update / Replace sub-items
        $itemIds    = $this->request->getPost('item_id') ?? [];
        $itemTitles = $this->request->getPost('item_judul') ?? [];
        $itemDescs  = $this->request->getPost('item_deskripsi') ?? [];

        // Hapus item lama yang tidak dikirimkan lagi
        if (!empty($itemIds)) {
            $this->itemModel->where('penugasan_id', $id)->whereNotIn('id', $itemIds)->delete();
        }

        if (!empty($itemTitles) && is_array($itemTitles)) {
            foreach ($itemTitles as $idx => $title) {
                $cleanTitle = trim($title);
                if (!empty($cleanTitle)) {
                    $itemId = $itemIds[$idx] ?? null;
                    if (!empty($itemId)) {
                        $this->itemModel->update($itemId, [
                            'judul_item'     => $cleanTitle,
                            'deskripsi_item' => isset($itemDescs[$idx]) ? trim($itemDescs[$idx]) : ''
                        ]);
                    } else {
                        $this->itemModel->insert([
                            'penugasan_id'   => $id,
                            'judul_item'     => $cleanTitle,
                            'deskripsi_item' => isset($itemDescs[$idx]) ? trim($itemDescs[$idx]) : '',
                            'status_item'    => 'pending'
                        ]);
                    }
                }
            }
        }

        return redirect()->to(base_url('direktur/penugasan'))->with('success', 'Penugasan harian berhasil diperbarui.');
    }

    public function updateItemStatus($itemId = null)
    {
        if ($r = $this->checkAccess()) return $r;

        $item = $this->itemModel->find($itemId);
        if (!$item) {
            return redirect()->back()->with('error', 'Item tugas tidak ditemukan.');
        }

        $status = $this->request->getPost('status_item');
        if (!in_array($status, ['pending', 'proses', 'selesai', 'ditunda'])) {
            return redirect()->back()->with('error', 'Status item tidak valid.');
        }

        $updateData = ['status_item' => $status];
        if ($status === 'ditunda') $updateData['alasan_ditunda'] = $this->request->getPost('alasan_ditunda');
        if ($status === 'selesai') $updateData['catatan_penyelesaian'] = $this->request->getPost('catatan_penyelesaian');

        $this->itemModel->update($itemId, $updateData);
        $this->penugasanModel->recalculateTaskStatus($item['penugasan_id']);

        return redirect()->back()->with('success', 'Status item tugas diperbarui.');
    }

    public function delete($id = null)
    {
        if ($r = $this->checkAccess()) return $r;

        $task = $this->penugasanModel->find($id);
        if (!$task) {
            return redirect()->to(base_url('direktur/penugasan'))->with('error', 'Tugas tidak ditemukan.');
        }

        $this->penugasanModel->delete($id);
        return redirect()->to(base_url('direktur/penugasan'))->with('success', 'Penugasan harian berhasil dihapus.');
    }
}
