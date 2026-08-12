<?php

namespace App\Controllers\Direktur\Proyek;

use App\Controllers\BaseController;
use App\Models\ProjectModel;
use App\Models\ProyekTimelineModel;
use App\Models\ClientModel;
use App\Models\UserModel;

class ProyekController extends BaseController
{
    protected $projectModel;
    protected $timelineModel;
    protected $clientModel;
    protected $userModel;

    public function __construct()
    {
        $this->projectModel = new ProjectModel();
        $this->timelineModel = new ProyekTimelineModel();
        
        try {
            $this->clientModel = new ClientModel();
        } catch (\Exception $e) {
            // fallback if ClientModel doesn't exist yet
        }
        $this->userModel = new UserModel();
    }

    private function ensureClientColumns()
    {
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();

        if (!$db->tableExists('client')) {
            $forge->addField([
                'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'kode_client'     => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'nama_perusahaan' => ['type' => 'VARCHAR', 'constraint' => 200],
                'nama_kontak'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'telepon'         => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'email'           => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'alamat'          => ['type' => 'TEXT', 'null' => true],
                'npwp'            => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'kategori'        => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'perusahaan'],
                'status'          => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'active'],
                'created_at'      => ['type' => 'DATETIME', 'null' => true],
                'updated_at'      => ['type' => 'DATETIME', 'null' => true]
            ]);
            $forge->addKey('id', true);
            $forge->createTable('client', true);
        } else {
            $fields = $db->getFieldNames('client');
            $newColumns = [];

            if (!in_array('kode_client', $fields)) {
                $newColumns['kode_client'] = ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true];
            }
            if (!in_array('nama_kontak', $fields)) {
                $newColumns['nama_kontak'] = ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true];
            }
            if (!in_array('telepon', $fields)) {
                $newColumns['telepon'] = ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true];
            }
            if (!in_array('email', $fields)) {
                $newColumns['email'] = ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true];
            }
            if (!in_array('alamat', $fields)) {
                $newColumns['alamat'] = ['type' => 'TEXT', 'null' => true];
            }
            if (!in_array('status', $fields)) {
                $newColumns['status'] = ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'active'];
            }

            if (!empty($newColumns)) {
                $forge->addColumn('client', $newColumns);
            }
        }

        
    }

    public function baru()
    {
        $this->ensureClientColumns();

        // Tampilkan project yang baru/belum jalan (penawaran, nego, deal)
        $builder = $this->projectModel->whereIn('status', ['penawaran', 'nego', 'deal'])->orderBy('created_at', 'DESC');
        
        // Distinct list of project names for reusable input
        $existingProjects = $this->projectModel->select('nama_project')->distinct()->orderBy('nama_project', 'ASC')->findAll();

        $data = [
            'title' => 'Project Baru & Inisiasi',
            'projects' => $builder->findAll(),
            'existing_projects' => array_column($existingProjects, 'nama_project'),
            'clients' => $this->db->table('client')->orderBy('nama_perusahaan', 'ASC')->get()->getResultArray(),
            'managers' => $this->userModel->where('status', 'active')->findAll()
        ];

        return view('direktur/proyek/baru', $data);
    }

    public function simpan()
    {
        $this->ensureClientColumns();

        $rules = [
            'nama_project' => 'required',
            'client_id' => 'required',
            'status' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $kode_project = $this->projectModel->generateKodeProject();

        $this->projectModel->insert([
            'kode_project' => $kode_project,
            'nama_project' => $this->request->getPost('nama_project'),
            'client_id' => $this->request->getPost('client_id'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'nilai_project' => str_replace(['Rp', '.', ' '], '', $this->request->getPost('nilai_project')),
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai') ?: date('Y-m-d'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai') ?: null,
            'status' => $this->request->getPost('status'),
            'project_manager_id' => $this->request->getPost('project_manager_id')
        ]);

        return redirect()->to(base_url('direktur/proyek/baru'))->with('success', 'Project berhasil ditambahkan.');
    }

    public function tambah_proyek()
    {
        $this->ensureClientColumns();

        $existingProjects = $this->projectModel->select('nama_project')->distinct()->orderBy('nama_project', 'ASC')->findAll();
        $data = [
            'title' => 'Buat Project Baru',
            'existing_projects' => array_column($existingProjects, 'nama_project'),
            'clients' => $this->db->table('client')->orderBy('nama_perusahaan', 'ASC')->get()->getResultArray(),
            'managers' => $this->userModel->where('status', 'active')->findAll()
        ];
        return view('direktur/proyek/baru_tambah', $data);
    }

    public function edit_proyek($id)
    {
        $this->ensureClientColumns();

        $project = $this->projectModel->find($id);
        if (!$project) {
            return redirect()->to(base_url('direktur/proyek/baru'))->with('error', 'Project tidak ditemukan.');
        }

        $existingProjects = $this->projectModel->select('nama_project')->distinct()->orderBy('nama_project', 'ASC')->findAll();

        $data = [
            'title' => 'Edit Project: ' . $project['nama_project'],
            'p' => $project,
            'existing_projects' => array_column($existingProjects, 'nama_project'),
            'clients' => $this->db->table('client')->orderBy('nama_perusahaan', 'ASC')->get()->getResultArray(),
            'managers' => $this->userModel->where('status', 'active')->findAll()
        ];
        return view('direktur/proyek/baru_edit', $data);
    }

    public function detail_proyek($id)
    {
        $this->ensureClientColumns();

        $project = $this->projectModel->find($id);
        if (!$project) {
            return redirect()->to(base_url('direktur/proyek/baru'))->with('error', 'Project tidak ditemukan.');
        }

        $client = null;
        if (!empty($project['client_id'])) {
            $client = $this->db->table('client')->where('id', $project['client_id'])->get()->getRowArray();
        }

        $manager = null;
        if (!empty($project['project_manager_id'])) {
            $manager = $this->userModel->find($project['project_manager_id']);
        }

        $data = [
            'title' => 'Detail Project: ' . $project['nama_project'],
            'p' => $project,
            'client' => $client,
            'manager' => $manager
        ];
        return view('direktur/proyek/baru_detail', $data);
    }

    public function update_proyek()
    {
        $this->ensureClientColumns();

        $id = $this->request->getPost('id');
        $rules = [
            'nama_project' => 'required',
            'client_id' => 'required',
            'status' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->projectModel->update($id, [
            'nama_project' => $this->request->getPost('nama_project'),
            'client_id' => $this->request->getPost('client_id'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'nilai_project' => str_replace(['Rp', '.', ' '], '', $this->request->getPost('nilai_project')),
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai') ?: null,
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai') ?: null,
            'status' => $this->request->getPost('status'),
            'project_manager_id' => $this->request->getPost('project_manager_id') ?: null
        ]);

        return redirect()->to(base_url('direktur/proyek/baru'))->with('success', 'Data project berhasil diperbarui.');
    }

    public function delete_proyek($id)
    {
        $project = $this->projectModel->find($id);
        if ($project) {
            $this->projectModel->delete($id);
            return redirect()->to(base_url('direktur/proyek/baru'))->with('success', 'Project berhasil dihapus.');
        }
        return redirect()->to(base_url('direktur/proyek/baru'))->with('error', 'Project tidak ditemukan.');
    }

    public function simpan_client()
    {
        $this->ensureClientColumns();

        $namaPerusahaan = trim($this->request->getPost('nama_perusahaan') ?? '');
        if (empty($namaPerusahaan)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Nama perusahaan wajib diisi.']);
            }
            return redirect()->back()->with('error', 'Nama perusahaan client wajib diisi.');
        }

        try {
            // Generate client code
            $prefix = 'CLT-' . date('Y');
            $count = $this->db->table('client')->like('kode_client', $prefix, 'after')->countAllResults();
            $kodeClient = $prefix . '-' . sprintf('%03d', $count + 1);

            $clientData = [
                'kode_client'     => $kodeClient,
                'nama_perusahaan' => $namaPerusahaan,
                'nama_kontak'     => $this->request->getPost('nama_kontak') ?: null,
                'telepon'         => $this->request->getPost('telepon') ?: null,
                'email'           => $this->request->getPost('email') ?: null,
                'alamat'          => $this->request->getPost('alamat') ?: null,
                'status'          => 'active',
                'created_at'      => date('Y-m-d H:i:s')
            ];

            $this->db->table('client')->insert($clientData);
            $clientId = $this->db->insertID();

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'          => 'success',
                    'client_id'       => $clientId,
                    'nama_perusahaan' => $namaPerusahaan
                ]);
            }

            return redirect()->to(base_url('direktur/proyek/baru'))->with('success', 'Client baru "' . $namaPerusahaan . '" berhasil ditambahkan.');
        } catch (\Exception $e) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Gagal menyimpan client: ' . $e->getMessage()
                ]);
            }
            return redirect()->back()->with('error', 'Gagal menyimpan client: ' . $e->getMessage());
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
        } else {
            $fields = $db->getFieldNames('proyek_timeline');
            $newColumns = [];
            if (!in_array('tipe_periode', $fields)) {
                $newColumns['tipe_periode'] = ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'harian'];
            }
            if (!in_array('periode_ke', $fields)) {
                $newColumns['periode_ke'] = ['type' => 'INT', 'constraint' => 11, 'default' => 1];
            }
            if (!in_array('progres_persen', $fields)) {
                $newColumns['progres_persen'] = ['type' => 'INT', 'constraint' => 11, 'default' => 0];
            }
            if (!empty($newColumns)) {
                $forge->addColumn('proyek_timeline', $newColumns);
            }
        }
    }

    public function timeline()
    {
        $this->ensureClientColumns();
        $this->ensureTimelineTablesExist();

        // Projects on progress & selesai (tetap tampil di timeline)
        $projectsOnProgress = $this->projectModel->whereIn('status', ['on_progress', 'selesai'])->orderBy('tanggal_mulai', 'DESC')->findAll();
        
        // Projects from 'proyek/baru' (penawaran, nego, deal) available to be activated
        $projectsPending = $this->projectModel->whereIn('status', ['penawaran', 'nego', 'deal'])->orderBy('created_at', 'DESC')->findAll();

        $data = [
            'title' => 'Timeline Kerja (Project Aktif)',
            'projects' => $projectsOnProgress,
            'projects_pending' => $projectsPending,
            'clients' => $this->db->table('client')->orderBy('nama_perusahaan', 'ASC')->get()->getResultArray(),
            'managers' => $this->userModel->where('status', 'active')->findAll()
        ];
        
        return view('direktur/proyek/timeline', $data);
    }

    public function aktifkan_proyek_timeline()
    {
        $proyekId = $this->request->getPost('proyek_id');
        if (!empty($proyekId)) {
            $this->projectModel->update($proyekId, [
                'status' => 'on_progress'
            ]);
            return redirect()->to(base_url('direktur/proyek/timeline/' . $proyekId))->with('success', 'Project berhasil diaktifkan ke Timeline Kerja.');
        }
        return redirect()->back()->with('error', 'Silakan pilih project terlebih dahulu.');
    }

    public function tambah_timeline()
    {
        $this->ensureClientColumns();
        $this->ensureTimelineTablesExist();

        $existingProjects = $this->projectModel->select('nama_project')->distinct()->orderBy('nama_project', 'ASC')->findAll();
        $data = [
            'title' => 'Buat Project Timeline Baru',
            'existing_projects' => array_column($existingProjects, 'nama_project'),
            'clients' => $this->db->table('client')->orderBy('nama_perusahaan', 'ASC')->get()->getResultArray(),
            'managers' => $this->userModel->where('status', 'active')->findAll()
        ];
        return view('direktur/proyek/timeline_tambah', $data);
    }

    public function edit_timeline($id)
    {
        $this->ensureClientColumns();
        $this->ensureTimelineTablesExist();

        $project = $this->projectModel->find($id);
        if (!$project) {
            return redirect()->to(base_url('direktur/proyek/timeline'))->with('error', 'Project timeline tidak ditemukan.');
        }

        $existingProjects = $this->projectModel->select('nama_project')->distinct()->orderBy('nama_project', 'ASC')->findAll();

        $data = [
            'title' => 'Edit Project Timeline: ' . $project['nama_project'],
            'p' => $project,
            'existing_projects' => array_column($existingProjects, 'nama_project'),
            'clients' => $this->db->table('client')->orderBy('nama_perusahaan', 'ASC')->get()->getResultArray(),
            'managers' => $this->userModel->where('status', 'active')->findAll()
        ];
        return view('direktur/proyek/timeline_edit', $data);
    }

    public function detail_timeline($id)
    {
        $this->ensureTimelineTablesExist();
        $this->ensureClientColumns();

        $project = $this->projectModel->find($id);
        if (!$project) return redirect()->to(base_url('direktur/proyek/timeline'))->with('error', 'Project timeline tidak ditemukan.');
        
        $client = null;
        if (!empty($project['client_id'])) {
            $client = $this->db->table('client')->where('id', $project['client_id'])->get()->getRowArray();
        }

        $manager = null;
        if (!empty($project['project_manager_id'])) {
            $manager = $this->userModel->find($project['project_manager_id']);
        }

        $karyawanList = $this->userModel->where('status', 'active')->findAll();

        $data = [
            'title' => 'Detail Timeline: ' . $project['nama_project'],
            'project' => $project,
            'client' => $client,
            'manager' => $manager,
            'timeline' => $this->timelineModel->getTimelineByProyek($id),
            'karyawan' => $karyawanList
        ];
        
        return view('direktur/proyek/detail_timeline', $data);
    }

    public function simpan_task()
    {
        $this->ensureTimelineTablesExist();

        $proyekId = $this->request->getPost('proyek_id');
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

        $this->timelineModel->insert([
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

        return redirect()->to(base_url('direktur/proyek/timeline/' . $proyekId))->with('success', 'Tugas/Milestone timeline berhasil ditambahkan.');
    }

    public function update_task_status()
    {
        $this->ensureTimelineTablesExist();
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status') ?: 'pending';
        $customProgres = $this->request->getPost('progres_persen');

        $progres = 0;
        if ($status === 'on_progress') $progres = 50;
        if ($status === 'done' || $status === 'selesai') $progres = 100;
        if ($customProgres !== null && $customProgres !== '') {
            $progres = (int) $customProgres;
        }

        $task = $this->timelineModel->find($id);
        if ($task) {
            $this->timelineModel->update($id, [
                'status' => $status,
                'progres_persen' => $progres
            ]);

            return redirect()->to(base_url('direktur/proyek/timeline/' . $task['proyek_id']))->with('success', 'Status & Progres task berhasil diperbarui.');
        }
        return redirect()->back()->with('error', 'Task tidak ditemukan.');
    }

    public function delete_task($id)
    {
        $task = $this->timelineModel->find($id);
        if ($task) {
            $proyekId = $task['proyek_id'];
            $this->timelineModel->delete($id);

            return redirect()->to(base_url('direktur/proyek/timeline/' . $proyekId))->with('success', 'Tugas timeline berhasil dihapus.');
        }
        return redirect()->back()->with('error', 'Tugas tidak ditemukan.');
    }

    public function export_excel_timeline($id)
    {
        $this->ensureTimelineTablesExist();
        $this->ensureClientColumns();

        $project = $this->projectModel->find($id);
        if (!$project) return redirect()->to(base_url('direktur/proyek/timeline'));

        $timeline = $this->timelineModel->getTimelineByProyek($id);
        $client = !empty($project['client_id']) ? $this->db->table('client')->where('id', $project['client_id'])->get()->getRowArray() : null;
        $manager = !empty($project['project_manager_id']) ? $this->userModel->find($project['project_manager_id']) : null;

        $filename = 'Timeline_Proyek_' . preg_replace('/[^a-zA-Z0-9]/', '_', $project['kode_project']) . '_' . date('Ymd') . '.xls';

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head><meta charset="utf-8"><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Timeline Proyek</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head>';
        echo '<body style="font-family:Arial, sans-serif;">';
        echo '<table border="1" style="border-collapse:collapse; width:100%; font-size:11pt;">';
        
        // Header Title
        echo '<tr><th colspan="8" style="background-color:#1e3c72; color:#ffffff; font-size:14pt; padding:12px; text-align:center; font-weight:bold;">TIMELINE JADWAL & MILESTONE PELAKSANAAN PROYEK</th></tr>';
        echo '<tr><td style="background-color:#f1f5f9; font-weight:bold;" width="15%">Kode Proyek</td><td colspan="3" width="35%">' . esc($project['kode_project']) . '</td><td style="background-color:#f1f5f9; font-weight:bold;" width="15%">Tanggal Mulai</td><td colspan="3" width="35%">' . (!empty($project['tanggal_mulai']) ? date('d-m-Y', strtotime($project['tanggal_mulai'])) : '-') . '</td></tr>';
        echo '<tr><td style="background-color:#f1f5f9; font-weight:bold;">Nama Proyek</td><td colspan="3">' . esc($project['nama_project']) . '</td><td style="background-color:#f1f5f9; font-weight:bold;">Estimasi Selesai</td><td colspan="3">' . (!empty($project['tanggal_selesai']) ? date('d-m-Y', strtotime($project['tanggal_selesai'])) : '-') . '</td></tr>';
        echo '<tr><td style="background-color:#f1f5f9; font-weight:bold;">Client</td><td colspan="3">' . esc($client['nama_perusahaan'] ?? 'General / Non-Client') . '</td><td style="background-color:#f1f5f9; font-weight:bold;">Project Manager</td><td colspan="3">' . esc($manager['username'] ?? 'Belum Ditunjuk') . '</td></tr>';
        echo '<tr><td style="background-color:#f1f5f9; font-weight:bold;">Nilai Project</td><td colspan="7" style="color:#198754; font-weight:bold;">Rp ' . number_format($project['nilai_project'] ?? 0, 0, ',', '.') . '</td></tr>';
        echo '<tr><td colspan="8" style="background-color:#ffffff; height:15px; border:none;"></td></tr>';

        // Table Header
        echo '<tr style="background-color:#2a5298; color:#ffffff; font-weight:bold; text-align:center;">';
        echo '<th style="padding:8px;" width="5%">No</th>';
        echo '<th style="padding:8px;" width="30%">Nama Tahapan / Pekerjaan</th>';
        echo '<th style="padding:8px;" width="18%">Urutan Hari & Minggu</th>';
        echo '<th style="padding:8px;" width="12%">Tanggal Mulai</th>';
        echo '<th style="padding:8px;" width="12%">Tenggat Selesai</th>';
        echo '<th style="padding:8px;" width="15%">PIC Karyawan</th>';
        echo '<th style="padding:8px;" width="10%">Progres (%)</th>';
        echo '<th style="padding:8px;" width="12%">Status Task</th>';
        echo '</tr>';

        $no = 1;
        foreach ($timeline as $t) {
            $hariKe = (int)($t['periode_ke'] ?? 1);
            if (!empty($project['tanggal_mulai']) && !empty($t['tanggal_mulai'])) {
                $diffMulai = strtotime($t['tanggal_mulai']) - strtotime($project['tanggal_mulai']);
                $hariKeCalculated = max(1, floor($diffMulai / 86400) + 1);
                if ($hariKe <= 1 && $hariKeCalculated > 1) $hariKe = $hariKeCalculated;
            }
            $mingguKe = ceil($hariKe / 7);

            $statusText = 'Belum Mulai';
            if ($t['status'] === 'on_progress') $statusText = 'Sedang Berjalan';
            if ($t['status'] === 'done' || $t['status'] === 'selesai') $statusText = 'Selesai';

            $progresVal = (int)($t['progres_persen'] ?? 0);
            if ($t['status'] === 'done' || $t['status'] === 'selesai') $progresVal = 100;

            $picName = !empty($t['ditugaskan_kepada']) ? $t['ditugaskan_kepada'] : ($manager['username'] ?? 'Belum Ditunjuk');

            echo '<tr>';
            echo '<td style="text-align:center;">' . $no++ . '</td>';
            echo '<td style="font-weight:bold;">' . esc($t['nama_tugas']) . '</td>';
            echo '<td style="text-align:center;">Hari ke-' . $hariKe . ' (Minggu ke-' . $mingguKe . ')</td>';
            echo '<td style="text-align:center;">' . date('d-m-Y', strtotime($t['tanggal_mulai'])) . '</td>';
            echo '<td style="text-align:center;">' . date('d-m-Y', strtotime($t['tanggal_selesai'])) . '</td>';
            echo '<td>' . esc($picName) . '</td>';
            echo '<td style="text-align:center; font-weight:bold;">' . $progresVal . '%</td>';
            echo '<td style="text-align:center; font-weight:bold;">' . strtoupper($statusText) . '</td>';
            echo '</tr>';
        }

        echo '</table>';
        echo '</body></html>';
        exit;
    }

    public function print_pdf_timeline($id)
    {
        $this->ensureTimelineTablesExist();
        $this->ensureClientColumns();

        $project = $this->projectModel->find($id);
        if (!$project) return redirect()->to(base_url('direktur/proyek/timeline'));

        $timeline = $this->timelineModel->getTimelineByProyek($id);

        $client = null;
        if (!empty($project['client_id'])) {
            $client = $this->db->table('client')->where('id', $project['client_id'])->get()->getRowArray();
        }

        $manager = null;
        if (!empty($project['project_manager_id'])) {
            $manager = $this->userModel->find($project['project_manager_id']);
        }

        $data = [
            'title' => 'Cetak Timeline: ' . $project['nama_project'],
            'project' => $project,
            'client' => $client,
            'manager' => $manager,
            'timeline' => $timeline
        ];

        return view('direktur/proyek/timeline_print', $data);
    }

    public function simpan_timeline()
    {
        $this->ensureClientColumns();
        $this->ensureTimelineTablesExist();

        $rules = [
            'nama_project' => 'required',
            'client_id' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $kode_project = $this->projectModel->generateKodeProject();

        $this->projectModel->insert([
            'kode_project' => $kode_project,
            'nama_project' => $this->request->getPost('nama_project'),
            'client_id' => $this->request->getPost('client_id'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'nilai_project' => str_replace(['Rp', '.', ' '], '', $this->request->getPost('nilai_project')),
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai') ?: date('Y-m-d'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai') ?: null,
            'status' => 'on_progress',
            'project_manager_id' => $this->request->getPost('project_manager_id') ?: null
        ]);

        return redirect()->to(base_url('direktur/proyek/timeline'))->with('success', 'Project timeline berhasil ditambahkan.');
    }

    public function update_timeline()
    {
        $this->ensureClientColumns();

        $id = $this->request->getPost('id');
        $rules = [
            'nama_project' => 'required',
            'client_id' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $status = $this->request->getPost('status') ?: 'on_progress';

        $this->projectModel->update($id, [
            'nama_project' => $this->request->getPost('nama_project'),
            'client_id' => $this->request->getPost('client_id'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'nilai_project' => str_replace(['Rp', '.', ' '], '', $this->request->getPost('nilai_project')),
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai') ?: null,
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai') ?: null,
            'status' => $status,
            'project_manager_id' => $this->request->getPost('project_manager_id') ?: null
        ]);

        return redirect()->to(base_url('direktur/proyek/timeline'))->with('success', 'Data project timeline berhasil diperbarui.');
    }

    public function delete_timeline($id)
    {
        $project = $this->projectModel->find($id);
        if ($project) {
            // Hapus task milestone timeline proyek ini
            $this->ensureTimelineTablesExist();
            $this->timelineModel->where('proyek_id', $id)->delete();

            // Kembalikan status proyek ke 'deal' agar proyek di Project Baru tetap aman utuh
            $this->projectModel->update($id, ['status' => 'deal']);

            return redirect()->to(base_url('direktur/proyek/timeline'))->with('success', 'Timeline pelaksanaan dikosongkan & status proyek dikembalikan ke Project Baru.');
        }
        return redirect()->to(base_url('direktur/proyek/timeline'))->with('error', 'Project timeline tidak ditemukan.');
    }

    public function selesaikan_proyek($id)
    {
        $project = $this->projectModel->find($id);
        if ($project) {
            $this->projectModel->update($id, [
                'status' => 'selesai',
                'tanggal_selesai' => !empty($project['tanggal_selesai']) ? $project['tanggal_selesai'] : date('Y-m-d')
            ]);
            return redirect()->to(base_url('direktur/proyek/selesai'))->with('success', 'Proyek "' . $project['nama_project'] . '" telah ditandai Selesai dan dipindahkan ke Arsip Project Selesai.');
        }
        return redirect()->back()->with('error', 'Project tidak ditemukan.');
    }

    public function selesai()
    {
        $this->ensureClientColumns();
        $existingProjects = $this->projectModel->select('nama_project')->distinct()->orderBy('nama_project', 'ASC')->findAll();

        $data = [
            'title' => 'Arsip Project Selesai / Batal',
            'projects' => $this->projectModel->whereIn('status', ['selesai', 'batal', 'done', 'completed', 'Completed'])->orderBy('tanggal_selesai', 'DESC')->findAll(),
            'existing_projects' => array_column($existingProjects, 'nama_project'),
            'clients' => $this->db->table('client')->orderBy('nama_perusahaan', 'ASC')->get()->getResultArray(),
            'managers' => $this->userModel->where('status', 'active')->findAll()
        ];

        return view('direktur/proyek/selesai', $data);
    }

    public function simpan_selesai()
    {
        $this->ensureClientColumns();
        $rules = [
            'nama_project' => 'required',
            'client_id' => 'required',
            'status' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $kode_project = $this->projectModel->generateKodeProject();

        $this->projectModel->insert([
            'kode_project' => $kode_project,
            'nama_project' => $this->request->getPost('nama_project'),
            'client_id' => $this->request->getPost('client_id'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'nilai_project' => str_replace(['Rp', '.', ' '], '', $this->request->getPost('nilai_project')),
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai') ?: date('Y-m-d'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai') ?: date('Y-m-d'),
            'status' => $this->request->getPost('status') ?: 'selesai',
            'project_manager_id' => $this->request->getPost('project_manager_id') ?: null
        ]);

        return redirect()->to(base_url('direktur/proyek/selesai'))->with('success', 'Arsip project baru berhasil ditambahkan.');
    }

    public function delete_selesai($id)
    {
        $project = $this->projectModel->find($id);
        if ($project) {
            $this->projectModel->delete($id);
            return redirect()->to(base_url('direktur/proyek/selesai'))->with('success', 'Data arsip project berhasil dihapus.');
        }
        return redirect()->to(base_url('direktur/proyek/selesai'))->with('error', 'Project tidak ditemukan.');
    }
}
