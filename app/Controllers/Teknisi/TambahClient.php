<?php

namespace App\Controllers\Teknisi;

use App\Controllers\BaseController;
use App\Models\Teknisi\ClientModel;
use App\Models\KaryawanModel;
use App\Models\UserModel;

class TambahClient extends TeknisiController
{
    protected $clientModel;
    protected $karyawanModel;
    protected $userModel;
    protected $db;
    protected $session;
    
    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->karyawanModel = new KaryawanModel();
        $this->userModel = new UserModel();
        $this->db = \Config\Database::connect();
        $this->session = \Config\Services::session();
        
        // Cek login
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Halaman daftar client
     */
    public function index()
    {
        $data['title'] = 'Daftar Client';
        $data['subtitle'] = 'Kelola semua data client';
        
        // Ambil filter dari request
        $kategori = $this->request->getGet('kategori');
        $status = $this->request->getGet('status');
        $search = $this->request->getGet('search');
        
        // Get data client dengan filter
        $data['clients'] = $this->clientModel->getFiltered($kategori, $status, $search);
        
        // Data untuk form
        $data['kode_client'] = $this->clientModel->generateKodeClient();
        $data['karyawan'] = $this->karyawanModel->select('id, nama_lengkap, jabatan')
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();
        
        // Data untuk filter
        $data['kategori_options'] = [
            'semua' => 'Semua Kategori',
            'perusahaan' => 'Perusahaan',
            'pemerintah' => 'Pemerintah',
            'perorangan' => 'Perorangan'
        ];
        
        $data['status_options'] = [
            'semua' => 'Semua Status',
            'active' => 'Active',
            'inactive' => 'Inactive',
            'potensial' => 'Potensial'
        ];
        
        // Filter yang dipilih
        $data['selected_kategori'] = $kategori ?? 'semua';
        $data['selected_status'] = $status ?? 'semua';
        $data['search'] = $search ?? '';
        
        // Statistik
        $data['statistik'] = $this->clientModel->getStatistik();
        
        return $this->renderView('teknisi/tugas_proyek/tambah_client/index', $data);
    }

    /**
     * Halaman tambah client (form saja)
     */
    public function create()
    {
        $data['title'] = 'Tambah Client Baru';
        $data['subtitle'] = 'Isi form untuk menambahkan client baru';
        
        // Generate kode client otomatis
        $data['kode_client'] = $this->clientModel->generateKodeClient();
        
        // Data karyawan untuk dropdown
        $data['karyawan'] = $this->karyawanModel->select('id, nama_lengkap, jabatan')
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();
        
        return $this->renderView('teknisi/tugas_proyek/tambah_client/create', $data);
    }

    /**
     * Simpan data client baru
     */
    public function store()
    {
        // Validasi input
        $rules = [
            'kode_client' => 'required|is_unique[client.kode_client]',
            'nama_perusahaan' => 'required',
            'email_client' => 'permit_empty|valid_email'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        // Data untuk disimpan
        $data = [
            'kode_client' => $this->request->getPost('kode_client'),
            'nama_perusahaan' => $this->request->getPost('nama_perusahaan'),
            'nama_kontak' => $this->request->getPost('nama_kontak'),
            'email_client' => $this->request->getPost('email_client'),
            'telepon' => $this->request->getPost('telepon'),
            'alamat' => $this->request->getPost('alamat'),
            'client_alamat' => $this->request->getPost('client_alamat'),
            'client_kontak' => $this->request->getPost('client_kontak'),
            'catatan_client' => $this->request->getPost('catatan_client'),
            'keperluan_client' => $this->request->getPost('keperluan_client'),
            'kategori' => $this->request->getPost('kategori') ?: 'perusahaan',
            'status' => $this->request->getPost('status') ?: 'active',
            'karyawan_id' => $this->request->getPost('karyawan_id') ?: null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // Log data untuk debugging
        log_message('debug', 'Data client to save: ' . json_encode($data));
        
        // Simpan ke database dengan transaksi
        $this->db->transStart();
        
        try {
            // Insert client
            $client_id = $this->clientModel->insert($data);
            
            if (!$client_id) {
                $errors = $this->clientModel->errors();
                log_message('error', 'Client Model Errors: ' . json_encode($errors));
                throw new \Exception('Gagal menyimpan client: ' . json_encode($errors));
            }
            
            log_message('debug', 'Client inserted with ID: ' . $client_id);
            
            // Catat log aktivitas
            $this->logAktivitas($client_id, 'CREATE', 'Client baru ditambahkan: ' . $data['nama_perusahaan']);
            
            $this->db->transCommit();
            
            return redirect()->to('teknisi/tugas-proyek/tambah-client')
                ->with('success', 'Client berhasil ditambahkan. Kode Client: ' . $data['kode_client']);
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            
            log_message('error', '[TambahClient::store] Exception: ' . $e->getMessage());
            log_message('error', '[TambahClient::store] Trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan client: ' . $e->getMessage());
        }
    }

   /**
 * Halaman detail client
 */
public function detail($id)
{
    $data['title'] = 'Detail Client';
    
    // Ambil data client dengan relations - PASTIKAN MENGEMBALIKAN OBJECT
    $client = $this->clientModel->getWithRelations($id);
    
    if (!$client) {
        return redirect()->to('teknisi/tugas-proyek/tambah-client')
            ->with('error', 'Client tidak ditemukan');
    }
    
    // Konversi ke object jika masih array
    if (is_array($client)) {
        $client = (object) $client;
    }
    
    $data['client'] = $client;
    
    // Ambil data terkait (SPK, Project)
    $spk_list = $this->db->table('spk_instalasi')
        ->select('spk_instalasi.*, users.name as dibuat_oleh_nama')
        ->join('users', 'users.id = spk_instalasi.dibuat_oleh', 'left')
        ->where('client_id', $id)
        ->orWhere('client_nama', $client->nama_perusahaan)
        ->orderBy('tanggal_mulai', 'DESC')
        ->limit(10)
        ->get()
        ->getResult();
    
    $data['spk_list'] = $spk_list;
    
    // Hitung statistik SPK untuk client ini
    $statistik = $this->db->table('spk_instalasi')
        ->select('
            COUNT(*) as total,
            SUM(CASE WHEN status = "Selesai" THEN 1 ELSE 0 END) as selesai,
            SUM(CASE WHEN status = "Dalam Pengerjaan" THEN 1 ELSE 0 END) as dalam_pengerjaan,
            SUM(CASE WHEN status = "Dijadwalkan" THEN 1 ELSE 0 END) as dijadwalkan
        ')
        ->where('client_id', $id)
        ->orWhere('client_nama', $client->nama_perusahaan)
        ->get()
        ->getRow();
    
    $data['statistik_spk'] = $statistik;
    
    return $this->renderView('teknisi/tugas_proyek/tambah_client/detail', $data);
}

    /**
     * Halaman edit client
     */
    public function edit($id)
    {
        $data['title'] = 'Edit Client';
        
        // Ambil data client
        $data['client'] = $this->clientModel->find($id);
        
        if (!$data['client']) {
            return redirect()->to('teknisi/tugas-proyek/tambah-client')
                ->with('error', 'Client tidak ditemukan');
        }
        
        // Data karyawan untuk dropdown
        $data['karyawan'] = $this->karyawanModel->select('id, nama_lengkap, jabatan')
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();
        
        return $this->renderView('teknisi/tugas_proyek/tambah_client/edit', $data);
    }

  /**
 * Update data client
 */
public function update($id)
{
    // Validasi input - PERBAIKAN: tambahkan aturan is_unique dengan pengecualian ID
    $rules = [
        'kode_client' => "required|is_unique[client.kode_client,id,{$id}]",
        'nama_perusahaan' => 'required',
        'email_client' => 'permit_empty|valid_email'
    ];
    
    if (!$this->validate($rules)) {
        return redirect()->back()
            ->withInput()
            ->with('errors', $this->validator->getErrors());
    }
    
    // Data untuk diupdate
    $data = [
        'kode_client' => $this->request->getPost('kode_client'),
        'nama_perusahaan' => $this->request->getPost('nama_perusahaan'),
        'nama_kontak' => $this->request->getPost('nama_kontak'),
        'email_client' => $this->request->getPost('email_client'),
        'telepon' => $this->request->getPost('telepon'),
        'alamat' => $this->request->getPost('alamat'),
        'client_alamat' => $this->request->getPost('client_alamat'),
        'client_kontak' => $this->request->getPost('client_kontak'),
        'catatan_client' => $this->request->getPost('catatan_client'),
        'keperluan_client' => $this->request->getPost('keperluan_client'),
        'kategori' => $this->request->getPost('kategori') ?: 'perusahaan',
        'status' => $this->request->getPost('status') ?: 'active',
        'karyawan_id' => $this->request->getPost('karyawan_id') ?: null,
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    // Log data untuk debugging
    log_message('debug', 'Data client to update: ' . json_encode($data));
    
    // Update database dengan transaksi
    $this->db->transStart();
    
    try {
        // PERBAIKAN: Nonaktifkan validasi sementara atau gunakan validation dengan ID
        $this->clientModel->setValidationRule('kode_client', "required|is_unique[client.kode_client,id,{$id}]");
        
        // Update client
        if (!$this->clientModel->update($id, $data)) {
            $errors = $this->clientModel->errors();
            log_message('error', 'Client Model Errors: ' . json_encode($errors));
            throw new \Exception('Gagal memperbarui client: ' . json_encode($errors));
        }
        
        // Catat log aktivitas
        $this->logAktivitas($id, 'UPDATE', 'Data client diperbarui');
        
        $this->db->transCommit();
        
        return redirect()->to('teknisi/tugas-proyek/tambah-client/detail/' . $id)
            ->with('success', 'Client berhasil diperbarui');
        
    } catch (\Exception $e) {
        $this->db->transRollback();
        
        log_message('error', '[TambahClient::update] Exception: ' . $e->getMessage());
        
        return redirect()->back()
            ->withInput()
            ->with('error', 'Gagal memperbarui client: ' . $e->getMessage());
    }
}

/**
 * Ubah status client (AJAX)
 */
public function ubahStatus($id)
{
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
    }
    
    $status = $this->request->getPost('status');
    
    if (!in_array($status, ['active', 'inactive', 'potensial'])) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Status tidak valid'
        ]);
    }
    
    try {
        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($this->clientModel->update($id, $data)) {
            $this->logAktivitas($id, 'UPDATE_STATUS', 'Status diubah menjadi ' . $status);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Status client berhasil diperbarui'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memperbarui status client'
            ]);
        }
    } catch (\Exception $e) {
        log_message('error', '[TambahClient::ubahStatus] Exception: ' . $e->getMessage());
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

    /**
     * Hapus client
     */
    public function delete($id)
    {
        try {
            // Ambil data client untuk log
            $client = $this->clientModel->find($id);
            
            if (!$client) {
                return redirect()->back()->with('error', 'Client tidak ditemukan');
            }
            
            // Cek apakah client memiliki data terkait
            if ($this->clientModel->hasRelatedData($id)) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat menghapus client karena masih memiliki data terkait (SPK/Project)');
            }
            
            // Catat log sebelum delete
            $this->logAktivitas($id, 'DELETE', 'Client dihapus: ' . $client->nama_perusahaan);
            
            // Hapus data (soft delete)
            if ($this->clientModel->delete($id)) {
                return redirect()->to('teknisi/tugas-proyek/tambah-client')
                    ->with('success', 'Client berhasil dihapus');
            } else {
                return redirect()->back()->with('error', 'Gagal menghapus client');
            }
            
        } catch (\Exception $e) {
            log_message('error', '[TambahClient::delete] Exception: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus client: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Simpan client cepat
     */
    public function ajaxStore()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }
        
        // Validasi input
        $rules = [
            'nama_perusahaan' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }
        
        try {
            // Generate kode client
            $kode_client = $this->clientModel->generateKodeClient();
            
            // Data untuk disimpan
            $data = [
                'kode_client' => $kode_client,
                'nama_perusahaan' => $this->request->getPost('nama_perusahaan'),
                'nama_kontak' => $this->request->getPost('nama_kontak'),
                'email_client' => $this->request->getPost('email_client'),
                'telepon' => $this->request->getPost('telepon'),
                'alamat' => $this->request->getPost('alamat'),
                'kategori' => $this->request->getPost('kategori') ?: 'perusahaan',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // Insert client
            $client_id = $this->clientModel->insert($data);
            
            if (!$client_id) {
                throw new \Exception('Gagal menyimpan client');
            }
            
            // Ambil data client yang baru disimpan
            $newClient = $this->clientModel->find($client_id);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Client berhasil ditambahkan',
                'data' => $newClient
            ]);
            
        } catch (\Exception $e) {
            log_message('error', '[TambahClient::ajaxStore] Exception: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan client: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * AJAX: Get list client untuk autocomplete
     */
    public function getList()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false]);
        }
        
        $term = $this->request->getGet('term');
        $limit = $this->request->getGet('limit') ?: 10;
        
        try {
            $clients = $this->clientModel->searchForAutocomplete($term, $limit);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $clients
            ]);
            
        } catch (\Exception $e) {
            log_message('error', '[TambahClient::getList] Exception: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * AJAX: Get client by ID
     */
    public function getClient($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false]);
        }
        
        try {
            $client = $this->clientModel->find($id);
            
            if ($client) {
                return $this->response->setJSON([
                    'success' => true,
                    'data' => $client
                ]);
            }
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Client tidak ditemukan'
            ]);
            
        } catch (\Exception $e) {
            log_message('error', '[TambahClient::getClient] Exception: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * AJAX: Bulk update status
     */
    public function bulkUpdateStatus()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false]);
        }
        
        $ids = $this->request->getPost('ids');
        $status = $this->request->getPost('status');
        
        if (empty($ids) || empty($status)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak lengkap'
            ]);
        }
        
        try {
            $result = $this->clientModel->bulkUpdateStatus($ids, $status);
            
            if ($result) {
                // Catat log
                foreach ($ids as $id) {
                    $this->logAktivitas($id, 'BULK_UPDATE', 'Status diubah menjadi ' . $status);
                }
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Status berhasil diperbarui untuk ' . count($ids) . ' client'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memperbarui status'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', '[TambahClient::bulkUpdateStatus] Exception: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Catat log aktivitas client
     */
    private function logAktivitas($client_id, $aktivitas, $keterangan = null)
    {
        try {
            // Buat tabel log jika belum ada
            $this->createLogTableIfNotExists();
            
            $data = [
                'client_id' => $client_id,
                'user_id' => session()->get('user_id'),
                'aktivitas' => $aktivitas,
                'keterangan' => $keterangan,
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString(),
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            return $this->db->table('client_activity_logs')->insert($data);
            
        } catch (\Exception $e) {
            log_message('error', '[TambahClient::logAktivitas] Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Buat tabel log jika belum ada
     */
    private function createLogTableIfNotExists()
    {
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();
        
        // Cek apakah tabel sudah ada
        if (!$db->tableExists('client_activity_logs')) {
            $fields = [
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'client_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => true,
                ],
                'user_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => true,
                ],
                'aktivitas' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
                ],
                'keterangan' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'ip_address' => [
                    'type' => 'VARCHAR',
                    'constraint' => 45,
                    'null' => true,
                ],
                'user_agent' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ];
            
            $forge->addField($fields);
            $forge->addKey('id', true);
            $forge->addKey('client_id');
            $forge->addKey('user_id');
            $forge->addKey('created_at');
            $forge->createTable('client_activity_logs');
        }
    }

    /**
     * Export client ke Excel
     */
    public function exportExcel()
    {
        $kategori = $this->request->getGet('kategori');
        $status = $this->request->getGet('status');
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');
        
        $data['clients'] = $this->clientModel->getForExport($kategori, $status, $start_date, $end_date);
        
        // Load library Excel (misal menggunakan PhpSpreadsheet)
        // $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        // ...
        
        return redirect()->back()->with('info', 'Fitur export Excel dalam pengembangan');
    }

    /**
     * Export client ke PDF
     */
    public function exportPdf()
    {
        return redirect()->back()->with('info', 'Fitur export PDF dalam pengembangan');
    }
}