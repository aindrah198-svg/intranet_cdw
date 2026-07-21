<?php
namespace App\Controllers\Sales;

use App\Models\SuratJalanModel;
use App\Models\SuratJalanItemModel;
use App\Models\ProjectModel;
use App\Models\InvoiceModel;
use App\Models\ClientModel;
use App\Models\PerusahaanModel;

class SuratJalan extends SalesController
{
    protected $db;
    protected $suratJalanModel;
    protected $suratJalanItemModel;
    protected $projectModel;
    protected $invoiceModel;
    protected $clientModel;
    protected $perusahaanModel;
    protected $karyawanModel;
    
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->db = \Config\Database::connect();
        $this->suratJalanModel = new SuratJalanModel();
        $this->suratJalanItemModel = new SuratJalanItemModel();
        $this->projectModel = new ProjectModel();
        $this->invoiceModel = new InvoiceModel();
        $this->clientModel = new ClientModel();
        $this->perusahaanModel = new PerusahaanModel();
        $this->karyawanModel = new \App\Models\KaryawanModel();
    }
    
     public function index()
{
    // Get user data
    $userData = $this->getUserData();
    
    log_message('debug', '=== SURAT JALAN INDEX DEBUG ===');
    log_message('debug', 'User Data: ' . json_encode($userData));
    log_message('debug', 'User ID: ' . $userData['id']);
    log_message('debug', 'Karyawan ID: ' . ($userData['karyawan_id'] ?? 'NULL'));
    log_message('debug', 'Role: ' . $userData['role']);
    
    // ====================================================
    // **QUERY DENGAN DEBUG DETAIL - TAMPILKAN SEMUA UNTUK SALES**
    // ====================================================
    $builder = $this->db->table('surat_jalan sj');
    $builder->select('sj.*, 
        p.nama_project, p.kode_project,
        c.nama_perusahaan, c.sales_id as client_sales_id,
        i.nomor_invoice,
        u.name as created_by_name,
        k.nama_lengkap as sales_nama,
        sj.created_by as sj_created_by')
        ->join('project p', 'p.id = sj.project_id', 'left')
        ->join('client c', 'c.id = p.client_id', 'left')
        ->join('invoice i', 'i.id = sj.invoice_id', 'left')
        ->join('users u', 'u.id = sj.created_by', 'left')
        ->join('karyawan k', 'k.id = c.sales_id', 'left')
        ->orderBy('sj.tanggal_kirim', 'DESC')
        ->orderBy('sj.id', 'DESC');
    
    // **PERBAIKAN FILTER: Untuk sales, tampilkan yang:**
    // 1. created_by = user_id (dibuat oleh user ini) ATAU
    // 2. c.sales_id = karyawan_id (client milik sales ini) ATAU  
    // 3. sj.created_by = user mana pun (UNTUK TESTING - TAMPILKAN SEMUA DULU)
    
    log_message('debug', '=== FILTER LOGIC ===');
    log_message('debug', 'Role: ' . $userData['role']);
    
    if ($userData['role'] === 'sales') {
        log_message('debug', 'Sales user detected');
        log_message('debug', 'User ID: ' . $userData['id']);
        log_message('debug', 'Karyawan ID: ' . ($userData['karyawan_id'] ?? 'NULL'));
        
        // **UNTUK TESTING: TAMPILKAN SEMUA DATA DULU**
        // Komentari filter ini dulu untuk testing
        // $builder->groupStart()
        //         ->orWhere('sj.created_by', $userData['id'])
        //         ->orWhere('c.sales_id', $userData['karyawan_id'])
        //         ->groupEnd();
        
        log_message('debug', 'NO FILTER APPLIED - Showing all data for testing');
    } else {
        log_message('debug', 'Admin/Direktur - No filter');
    }
    
    // Get data
    $suratJalanList = $builder->get()->getResultArray();
    
    log_message('debug', 'Total data found: ' . count($suratJalanList));
    
    // Debug detail untuk beberapa data
    if (!empty($suratJalanList)) {
        log_message('debug', '=== DATA DETAIL (First 5) ===');
        foreach (array_slice($suratJalanList, 0, 5) as $index => $item) {
            log_message('debug', "Data [$index]:");
            log_message('debug', "  ID: {$item['id']}");
            log_message('debug', "  No SJ: {$item['nomor_surat_jalan']}");
            log_message('debug', "  Project: {$item['nama_project']}");
            log_message('debug', "  Client: {$item['nama_perusahaan']}");
            log_message('debug', "  Created By (sj): {$item['sj_created_by']}");
            log_message('debug', "  Client Sales ID: {$item['client_sales_id']}");
            log_message('debug', "  Created By Name: {$item['created_by_name']}");
            log_message('debug', "  Sales Name: {$item['sales_nama']}");
        }
    }
    
    // ====================================================
    // **CEK DATA DI DATABASE UNTUK DEBUG**
    // ====================================================
    log_message('debug', '=== DATABASE CHECK ===');
    
    // Cek semua surat jalan
    $checkAll = $this->db->query("
        SELECT sj.id, sj.nomor_surat_jalan, sj.created_by, 
               p.nama_project, c.nama_perusahaan, c.sales_id,
               u.name as creator_name
        FROM surat_jalan sj
        LEFT JOIN project p ON p.id = sj.project_id
        LEFT JOIN client c ON c.id = p.client_id
        LEFT JOIN users u ON u.id = sj.created_by
        ORDER BY sj.id
    ")->getResultArray();
    
    log_message('debug', 'Total in database: ' . count($checkAll));
    foreach ($checkAll as $item) {
        log_message('debug', "DB - ID: {$item['id']}, No: {$item['nomor_surat_jalan']}, Created By: {$item['created_by']}, Creator: {$item['creator_name']}, Client: {$item['nama_perusahaan']}, Sales ID: {$item['sales_id']}");
    }
    
    // Cek client dengan sales_id
    $checkClients = $this->db->query("
        SELECT c.id, c.nama_perusahaan, c.sales_id, k.nama_lengkap
        FROM client c
        LEFT JOIN karyawan k ON k.id = c.sales_id
        WHERE c.sales_id IS NOT NULL
    ")->getResultArray();
    
    log_message('debug', '=== CLIENTS WITH SALES_ID ===');
    foreach ($checkClients as $client) {
        log_message('debug', "Client: {$client['nama_perusahaan']}, Sales ID: {$client['sales_id']}, Sales: {$client['nama_lengkap']}");
    }
    
    // ====================================================
    // **GET STATUS COUNT**
    // ====================================================
    $statusBuilder = $this->db->table('surat_jalan');
    $statusBuilder->select('status, COUNT(*) as count')
                 ->groupBy('status');
    
    $statusResult = $statusBuilder->get()->getResultArray();
    
    $statusCount = [
        'draft' => 0,
        'diproses' => 0,
        'dikirim' => 0,
        'diterima' => 0,
        'ditolak' => 0,
        'dibatalkan' => 0
    ];
    
    foreach ($statusResult as $row) {
        if (isset($statusCount[$row['status']])) {
            $statusCount[$row['status']] = (int)$row['count'];
        }
    }
    
    // ====================================================
    // **GET TODAY'S DELIVERIES**
    // ====================================================
    $today = date('Y-m-d');
    $todayBuilder = $this->db->table('surat_jalan sj');
    $todayBuilder->select('sj.*, p.nama_project, c.nama_perusahaan, i.nomor_invoice')
                ->join('project p', 'p.id = sj.project_id', 'left')
                ->join('client c', 'c.id = p.client_id', 'left')
                ->join('invoice i', 'i.id = sj.invoice_id', 'left')
                ->where('sj.tanggal_kirim', $today)
                ->whereIn('sj.status', ['diproses', 'dikirim'])
                ->orderBy('sj.created_at', 'DESC')
                ->limit(10);
    
    $todayDeliveries = $todayBuilder->get()->getResultArray();
    
    // ====================================================
    // **PREPARE DATA FOR VIEW**
    // ====================================================
    $data = [
        'title' => 'Daftar Surat Jalan',
        'subtitle' => 'Kelola surat jalan pengiriman',
        'suratJalanList' => $suratJalanList,
        'statusCount' => $statusCount,
        'todayDeliveries' => $todayDeliveries,
        'active' => 'surat_jalan',
        'userRole' => $userData['role'],
        'totalCount' => count($suratJalanList)
    ];
    
    log_message('debug', 'Data to view - Count: ' . count($suratJalanList));
    log_message('debug', '=== END SURAT JALAN DEBUG ===');
    
    return $this->renderView('sales/surat-jalan/index', $data);
}
    
 public function create()
{
    $userData = $this->getUserData();
    
    // Get projects yang bisa dibuat surat jalan
    $projects = $this->projectModel->getProjectsForSuratJalan($userData);
    
    // Get perusahaan pengirim (CDW)
    $perusahaanPengirim = $this->perusahaanModel->getPerusahaanCDW();
    
    // Generate nomor surat jalan otomatis
    $nomorSuratJalan = $this->suratJalanModel->generateNomorSuratJalan();
    
    // Cari data karyawan untuk user yang login
    $karyawan = null;
    
    if (!empty($userData['karyawan_id'])) {
        $karyawan = $this->karyawanModel->find($userData['karyawan_id']);
    } else {
        // Cari berdasarkan nama
        $karyawan = $this->karyawanModel->where('nama_lengkap', $userData['name'])->first();
    }
    
    // Convert karyawan data ke array jika diperlukan
    $karyawanData = null;
    if ($karyawan) {
        if (is_object($karyawan) && method_exists($karyawan, 'toArray')) {
            $karyawanData = $karyawan->toArray();
        } elseif (is_array($karyawan)) {
            $karyawanData = $karyawan;
        } else {
            $karyawanData = (array)$karyawan;
        }
    }
    
    $data = [
        'title' => 'Buat Surat Jalan Baru',
        'subtitle' => 'Tambah surat jalan pengiriman baru',
        'projects' => $projects,
        'perusahaanPengirim' => $perusahaanPengirim,
        'nomorSuratJalan' => $nomorSuratJalan,
        'userRole' => $userData['role'] ?? 'sales',
        'karyawan' => $karyawanData, // Kirim data karyawan sebagai array
        'active' => 'surat_jalan',
        'validation' => \Config\Services::validation()
    ];
    
    return $this->renderView('sales/surat-jalan/create', $data);
}
    
    /**
     * Method untuk create surat jalan dari invoice
     */
    public function createFromInvoice($invoiceId)
    {
        $userData = $this->getUserData();
        
        // Get invoice data
        $invoice = $this->invoiceModel->find($invoiceId);
        
        if (!$invoice) {
            return redirect()->to(site_url('sales/surat-jalan'))
                ->with('error', 'Invoice tidak ditemukan');
        }
        
        // Get project data
        $project = $this->projectModel->find($invoice['project_id']);
        
        if (!$project) {
            return redirect()->to(site_url('sales/surat-jalan'))
                ->with('error', 'Project tidak ditemukan');
        }
        
        // Get client data
        $client = $this->clientModel->find($project['client_id']);
        
        // Get invoice items untuk dijadikan default items
        $invoiceItems = $this->invoiceModel->getInvoiceItems($invoiceId);
        
        // Get perusahaan pengirim (CDW)
        $perusahaanPengirim = $this->perusahaanModel->getPerusahaanCDW();
        
        // Generate nomor surat jalan otomatis
        $nomorSuratJalan = $this->suratJalanModel->generateNomorSuratJalan();
        
        $data = [
            'title' => 'Buat Surat Jalan dari Invoice',
            'subtitle' => 'Buat surat jalan berdasarkan invoice',
            'invoice' => $invoice,
            'project' => $project,
            'client' => $client,
            'invoiceItems' => $invoiceItems,
            'perusahaanPengirim' => $perusahaanPengirim,
            'nomorSuratJalan' => $nomorSuratJalan,
            'userRole' => $userData['role'] ?? 'sales',
            'active' => 'surat_jalan',
            'validation' => \Config\Services::validation()
        ];
        
        return $this->renderView('sales/surat-jalan/create_from_invoice', $data);
    }
    
public function store()
{
    $userData = $this->getUserData();
    $userId = $userData['id'] ?? null;
    
    // ========== PINDAHKAN KE SINI ==========
    // Cari data karyawan untuk user yang login
    $karyawan = null;
    if (!empty($userData['karyawan_id'])) {
        $karyawan = $this->karyawanModel->find($userData['karyawan_id']);
    } else {
        // Cari berdasarkan nama dari userData
        $karyawan = $this->karyawanModel->where('nama_lengkap', $userData['name'])->first();
    }
    
    // Jika tidak ditemukan, gunakan data dari POST (jika dikirim dari form)
    if (!$karyawan) {
        $disiapkanOleh = $this->request->getPost('disiapkan_oleh');
        if (!empty($disiapkanOleh)) {
            $karyawan = $this->karyawanModel->where('nama_lengkap', $disiapkanOleh)->first();
        }
    }
    // ========== AKHIR TAMBAHAN ==========
    
    log_message('debug', '=== SURAT JALAN STORE DEBUG ===');
    log_message('debug', 'User ID: ' . $userId);
    log_message('debug', 'POST Data: ' . print_r($this->request->getPost(), true));
    log_message('debug', 'Karyawan ditemukan: ' . ($karyawan ? 'Ya' : 'Tidak'));
    if ($karyawan) {
        log_message('debug', 'Karyawan details: ' . print_r($karyawan, true));
    }

    // Validasi input
    $rules = [
        'tanggal_kirim' => 'required|valid_date',
        'penerima_perusahaan' => 'required|max_length[200]',
        'penerima_up' => 'required|max_length[100]',
        'alamat_pengiriman' => 'required',
        'penerima_telepon' => 'permit_empty|max_length[20]',
        'sopir' => 'permit_empty|max_length[100]',
        'no_kendaraan' => 'permit_empty|max_length[20]',
        'keterangan' => 'permit_empty',
        'catatan_barang' => 'permit_empty',
        'nomor_surat_jalan' => 'required|max_length[100]',
        'project_manual' => 'required|max_length[200]'
    ];
    
    if (!$this->validate($rules)) {
        log_message('debug', 'Validation Errors: ' . print_r($this->validator->getErrors(), true));
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }
    
    // Cek nomor surat jalan unik
    $nomorSuratJalan = $this->request->getPost('nomor_surat_jalan');
    if ($this->suratJalanModel->isSuratJalanNumberExists($nomorSuratJalan)) {
        log_message('debug', 'Nomor surat jalan sudah digunakan: ' . $nomorSuratJalan);
        return redirect()->back()->withInput()->with('error', 'Nomor surat jalan sudah digunakan');
    }
    
    // Karena project_id required di database, kita perlu cari atau buat project
    $projectManual = $this->request->getPost('project_manual');
    $projectId = null;
    
    // Cari project berdasarkan nama
    $project = $this->projectModel->where('nama_project', $projectManual)->first();
    
    if ($project) {
        $projectId = $project['id'];
        log_message('debug', 'Project ditemukan: ID=' . $projectId);
    } else {
        // Buat project baru untuk testing
        try {
            $projectData = [
                'kode_project' => 'PROJ-TEMP-' . date('YmdHis'),
                'nama_project' => $projectManual,
                'client_id' => 1, // Default client untuk testing
                'status' => 'aktif',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $projectId = $this->projectModel->insert($projectData);
            log_message('debug', 'Project dibuat baru: ID=' . $projectId);
            
        } catch (\Exception $e) {
            log_message('debug', 'Gagal membuat project: ' . $e->getMessage());
            // Fallback: Gunakan project_id = 1 jika ada
            $defaultProject = $this->projectModel->first();
            $projectId = $defaultProject ? $defaultProject['id'] : 1;
        }
    }
    
    // Get invoice jika ada
    $invoiceManual = $this->request->getPost('invoice_manual');
    $invoiceId = null;
    
    if (!empty($invoiceManual)) {
        // Cari invoice berdasarkan nomor
        $invoice = $this->invoiceModel->where('nomor_invoice', $invoiceManual)->first();
        if ($invoice) {
            $invoiceId = $invoice['id'];
        }
    }
    
    // HAPUS KODE INI (SUDAH DIPINDAH KE ATAS):
    // ========== TAMBAHKAN KODE INI ==========
    // Cari data karyawan untuk user yang login
    // $karyawan = null;
    // if (!empty($userData['karyawan_id'])) {
    //     $karyawan = $this->karyawanModel->find($userData['karyawan_id']);
    // } else {
    //     // Cari berdasarkan nama dari userData
    //     $karyawan = $this->karyawanModel->where('nama_lengkap', $userData['name'])->first();
    // }
    // 
    // // Jika tidak ditemukan, gunakan data dari POST (jika dikirim dari form)
    // if (!$karyawan) {
    //     $disiapkanOleh = $this->request->getPost('disiapkan_oleh');
    //     if (!empty($disiapkanOleh)) {
    //         $karyawan = $this->karyawanModel->where('nama_lengkap', $disiapkanOleh)->first();
    //     }
    // }
    // ========== AKHIR TAMBAHAN ==========
    
    // Prepare data surat jalan
    $suratJalanData = [
        'nomor_surat_jalan' => $nomorSuratJalan,
        'project_id' => $projectId,
        'invoice_id' => $invoiceId,
        'tanggal_kirim' => $this->request->getPost('tanggal_kirim'),
        'penerima_perusahaan' => $this->request->getPost('penerima_perusahaan'),
        'penerima_up' => $this->request->getPost('penerima_up'),
        'penerima_nama' => $this->request->getPost('penerima_nama') ?? $this->request->getPost('penerima_up'),
        'penerima_telepon' => $this->request->getPost('penerima_telepon'),
        'alamat_pengiriman' => $this->request->getPost('alamat_pengiriman'),
        'lokasi_proyek' => $this->request->getPost('lokasi_proyek') ?? $this->request->getPost('alamat_pengiriman'),
        'sopir' => $this->request->getPost('sopir'),
        'no_kendaraan' => $this->request->getPost('no_kendaraan'),
        'status' => 'diproses',
        'keterangan' => $this->request->getPost('keterangan'),
        'catatan_barang' => $this->request->getPost('catatan_barang'),
        'created_by' => $userId,
        
        // Data disiapkan - ambil dari tabel karyawan jika ada
        'disiapkan_oleh' => $karyawan['nama_lengkap'] ?? $userData['name'] ?? 'Admin',
        'disiapkan_telepon' => $karyawan['telepon'] ?? $userData['phone'] ?? '-',
        'disiapkan_jabatan' => $karyawan['jabatan'] ?? $userData['role'] ?? 'Sales',
        
        // Data pengiriman (bisa kosong)
        'dikirim_oleh' => $this->request->getPost('dikirim_oleh'),
        'dikirim_telepon' => $this->request->getPost('dikirim_telepon'),
        
        // Default perusahaan pengirim
        'perusahaan_pengirim_id' => 1,
        'perusahaan_pengirim_nama' => 'PT. Cipta Duta Wacana',
        'perusahaan_pengirim_alamat' => 'Villa Bintaro Regency, Jl. Riau Blok K1 No. 2, Pondok Kacang Timur, Tangerang Selatan 15226',
        'perusahaan_pengirim_website' => 'www.cdw-engineering.com'
    ];
    
    // Jika menggunakan project manual, tambahkan ke keterangan
    if (!empty($projectManual)) {
        $suratJalanData['keterangan'] = "Project: " . $projectManual . "\n" . 
                                       ($suratJalanData['keterangan'] ?? '');
    }
    
    log_message('debug', 'Surat Jalan Data: ' . print_r($suratJalanData, true));
    
    // Prepare data dengan method di model
    $suratJalanData = $this->suratJalanModel->prepareSuratJalanData($suratJalanData);
    
    log_message('debug', 'After prepareSuratJalanData: ' . print_r($suratJalanData, true));
    
    try {
        // Insert surat jalan
        log_message('debug', 'Attempting to insert surat jalan...');
        $suratJalanId = $this->suratJalanModel->insert($suratJalanData);
        
        log_message('debug', 'Insert result: ' . ($suratJalanId ? 'Success, ID=' . $suratJalanId : 'Failed'));
        
        if (!$suratJalanId) {
            // Cek error dari model
            $error = $this->suratJalanModel->errors();
            log_message('debug', 'Model Errors: ' . print_r($error, true));
            throw new \Exception('Gagal menyimpan surat jalan. ' . implode(', ', $error));
        }
        
        // Insert items jika ada
        $items = $this->request->getPost('items');
        log_message('debug', 'Items count: ' . ($items ? count($items) : 0));
        
        if ($items && is_array($items)) {
            log_message('debug', 'Items data: ' . print_r($items, true));
            $result = $this->suratJalanItemModel->insertBatchItems($suratJalanId, $items);
            log_message('debug', 'Insert items result: ' . ($result ? 'Success' : 'Failed'));
        }
        
        // Log activity
        if (function_exists('log_activity')) {
            log_activity($userId, 'create', 'surat_jalan', $suratJalanId, 'Membuat surat jalan baru: ' . $nomorSuratJalan);
        }
        
        log_message('debug', '=== SURAT JALAN STORE SUCCESS ===');
        
        return redirect()->to(site_url('sales/surat-jalan/detail/' . $suratJalanId))
            ->with('success', 'Surat jalan berhasil dibuat');
            
    } catch (\Exception $e) {
        log_message('error', 'Surat Jalan Store Error: ' . $e->getMessage());
        log_message('debug', 'Error Trace: ' . $e->getTraceAsString());
        return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
    }
}
    
 public function edit($id)
{
    $userData = $this->getUserData();
    
    // Get surat jalan dengan detail lengkap
    $suratJalan = $this->suratJalanModel->getSuratJalanWithDetails($id);
    
    if (!$suratJalan) {
        return redirect()->to(site_url('sales/surat-jalan'))
            ->with('error', 'Surat jalan tidak ditemukan');
    }
    
    // Cek akses untuk edit
    if ($userData['role'] === 'sales') {
        $project = $this->projectModel->find($suratJalan['project_id']);
        if ($project) {
            $client = $this->clientModel->find($project['client_id']);
            if ($client && $client['sales_id'] != $userData['karyawan_id'] && $suratJalan['created_by'] != $userData['id']) {
                return redirect()->to(site_url('sales/surat-jalan'))
                    ->with('error', 'Anda tidak memiliki akses untuk mengedit surat jalan ini');
            }
        }
    }
    
    // Get items
    $items = $this->suratJalanItemModel->getItemsBySuratJalan($id);
    
    // Get perusahaan pengirim
    $perusahaanPengirim = $this->perusahaanModel->getPerusahaanCDW();
    
    // ========== DEBUG: CEK DATA SURAT JALAN ==========
    log_message('debug', '=== DEBUG EDIT SURAT JALAN ===');
    log_message('debug', 'Surat Jalan ID: ' . $id);
    log_message('debug', 'Surat Jalan Data: ' . print_r($suratJalan, true));
    log_message('debug', 'Project ID: ' . ($suratJalan['project_id'] ?? 'NULL'));
    log_message('debug', 'Invoice ID: ' . ($suratJalan['invoice_id'] ?? 'NULL'));
    
    // ========== AMBIL DATA PROJECT ==========
    // Karena di create menggunakan project manual, cek jika ada keterangan tentang project
    $projectName = '';
    
    // Cari project name dari beberapa sumber:
    // 1. Dari keterangan (jika dibuat dengan cara manual)
    if (!empty($suratJalan['keterangan']) && strpos($suratJalan['keterangan'], 'Project:') !== false) {
        // Ekstrak project name dari keterangan
        $lines = explode("\n", $suratJalan['keterangan']);
        foreach ($lines as $line) {
            if (strpos($line, 'Project:') !== false) {
                $projectName = trim(str_replace('Project:', '', $line));
                break;
            }
        }
    }
    
    // 2. Jika ada project_id, ambil dari database
    if (empty($projectName) && !empty($suratJalan['project_id'])) {
        $project = $this->projectModel->find($suratJalan['project_id']);
        if ($project) {
            $projectName = $project['nama_project'] ?? '';
        }
    }
    
    // 3. Jika masih kosong, gunakan fallback
    if (empty($projectName)) {
        $projectName = $suratJalan['keterangan'] ?? '';
    }
    
    // ========== AMBIL DATA INVOICE ==========
    $invoiceNumber = '';
    
    // Cari invoice number dari beberapa sumber:
    // 1. Jika ada invoice_id, ambil dari database
    if (!empty($suratJalan['invoice_id'])) {
        $invoice = $this->invoiceModel->find($suratJalan['invoice_id']);
        if ($invoice) {
            $invoiceNumber = $invoice['nomor_invoice'] ?? '';
        }
    }
    
    // 2. Cari di keterangan
    if (empty($invoiceNumber) && !empty($suratJalan['keterangan']) && strpos($suratJalan['keterangan'], 'Invoice:') !== false) {
        $lines = explode("\n", $suratJalan['keterangan']);
        foreach ($lines as $line) {
            if (strpos($line, 'Invoice:') !== false) {
                $invoiceNumber = trim(str_replace('Invoice:', '', $line));
                break;
            }
        }
    }
    
    log_message('debug', 'Extracted Project Name: ' . $projectName);
    log_message('debug', 'Extracted Invoice Number: ' . $invoiceNumber);
    
    // Cari data karyawan untuk user yang login
    $karyawanData = null;
    
    if (!empty($userData['karyawan_id'])) {
        $karyawanData = $this->karyawanModel->find($userData['karyawan_id']);
    } else {
        // Cari berdasarkan nama yang ada di surat jalan atau user
        if (!empty($suratJalan['disiapkan_oleh'])) {
            $karyawanData = $this->karyawanModel->where('nama_lengkap', $suratJalan['disiapkan_oleh'])->first();
        }
        
        if (!$karyawanData) {
            $karyawanData = $this->karyawanModel->where('nama_lengkap', $userData['name'])->first();
        }
    }
    
    // Pastikan $karyawanData adalah array
    if ($karyawanData && is_object($karyawanData)) {
        $karyawanData = (array)$karyawanData;
    }
    
    $data = [
        'title' => 'Edit Surat Jalan',
        'subtitle' => 'Ubah data surat jalan',
        'suratJalan' => $suratJalan,
        'items' => $items,
        'perusahaanPengirim' => $perusahaanPengirim,
        'userRole' => $userData['role'],
        'karyawan' => $karyawanData,
        'projectName' => $projectName,
        'invoiceNumber' => $invoiceNumber,
        'active' => 'surat_jalan',
        'validation' => \Config\Services::validation()
    ];
    
    return $this->renderView('sales/surat-jalan/edit', $data);
}
    
 public function update($id)
{
    $userData = $this->getUserData();
    
    // Cek apakah surat jalan ada
    $suratJalan = $this->suratJalanModel->find($id);
    
    if (!$suratJalan) {
        return redirect()->to(site_url('sales/surat-jalan'))
            ->with('error', 'Surat jalan tidak ditemukan');
    }
    
    // Cek akses untuk update
    if ($userData['role'] === 'sales') {
        $project = $this->projectModel->find($suratJalan['project_id']);
        if ($project) {
            $client = $this->clientModel->find($project['client_id']);
            if ($client && $client['sales_id'] != $userData['karyawan_id'] && $suratJalan['created_by'] != $userData['id']) {
                return redirect()->to(site_url('sales/surat-jalan'))
                    ->with('error', 'Anda tidak memiliki akses untuk mengupdate surat jalan ini');
            }
        }
    }
    
    // Validasi input
    $rules = [
        'tanggal_kirim' => 'required|valid_date',
        'penerima_perusahaan' => 'required|max_length[200]',
        'penerima_up' => 'required|max_length[100]',
        'alamat_pengiriman' => 'required',
        'penerima_telepon' => 'permit_empty|max_length[20]',
        'sopir' => 'permit_empty|max_length[100]',
        'no_kendaraan' => 'permit_empty|max_length[20]',
        'keterangan' => 'permit_empty',
        'catatan_barang' => 'permit_empty',
        'nomor_surat_jalan' => 'required|max_length[100]|is_unique[surat_jalan.nomor_surat_jalan,id,' . $id . ']',
        'project_manual' => 'required|max_length[200]'
    ];
    
    if (!$this->validate($rules)) {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }
    
    // ========== TAMBAHKAN LOGIKA UNTUK PROJECT & INVOICE MANUAL ==========
    
    // Get data dari form
    $projectManual = $this->request->getPost('project_manual');
    $invoiceManual = $this->request->getPost('invoice_manual');
    
    log_message('debug', '=== UPDATE SURAT JALAN ===');
    log_message('debug', 'Project Manual: ' . $projectManual);
    log_message('debug', 'Invoice Manual: ' . $invoiceManual);
    
    // Cari atau buat project berdasarkan nama manual
    $projectId = $suratJalan['project_id']; // Default ke yang sudah ada
    
    if (!empty($projectManual)) {
        // Cari project berdasarkan nama
        $project = $this->projectModel->where('nama_project', $projectManual)->first();
        
        if ($project) {
            $projectId = $project['id'];
        } else {
            // Jika tidak ditemukan, tetap gunakan project_id yang ada
            // Atau buat project baru jika diperlukan
            // Untuk sekarang, kita tetap gunakan yang ada
            log_message('debug', 'Project not found: ' . $projectManual . ', keeping existing project_id: ' . $projectId);
        }
    }
    
    // Cari atau update invoice
    $invoiceId = $suratJalan['invoice_id']; // Default ke yang sudah ada
    
    if (!empty($invoiceManual)) {
        // Cari invoice berdasarkan nomor
        $invoice = $this->invoiceModel->where('nomor_invoice', $invoiceManual)->first();
        if ($invoice) {
            $invoiceId = $invoice['id'];
        } else {
            log_message('debug', 'Invoice not found: ' . $invoiceManual . ', keeping existing invoice_id: ' . $invoiceId);
        }
    }
    
    // Cari data karyawan untuk update data disiapkan_oleh
    $karyawan = null;
    $disiapkanOleh = $this->request->getPost('disiapkan_oleh');
    
    if (!empty($disiapkanOleh)) {
        $karyawan = $this->karyawanModel->where('nama_lengkap', $disiapkanOleh)->first();
    }
    
    if (!$karyawan && !empty($userData['karyawan_id'])) {
        $karyawan = $this->karyawanModel->find($userData['karyawan_id']);
    }
    
    // Prepare data untuk update
    $updateData = [
        'project_id' => $projectId,
        'invoice_id' => !empty($invoiceId) ? $invoiceId : null,
        'tanggal_kirim' => $this->request->getPost('tanggal_kirim'),
        'penerima_perusahaan' => $this->request->getPost('penerima_perusahaan'),
        'penerima_up' => $this->request->getPost('penerima_up'),
        'penerima_nama' => $this->request->getPost('penerima_nama'),
        'penerima_telepon' => $this->request->getPost('penerima_telepon'),
        'alamat_pengiriman' => $this->request->getPost('alamat_pengiriman'),
        'lokasi_proyek' => $this->request->getPost('lokasi_proyek'),
        'sopir' => $this->request->getPost('sopir'),
        'no_kendaraan' => $this->request->getPost('no_kendaraan'),
        'keterangan' => $this->request->getPost('keterangan'),
        'catatan_barang' => $this->request->getPost('catatan_barang'),
        'nomor_surat_jalan' => $this->request->getPost('nomor_surat_jalan'),
        'dikirim_oleh' => $this->request->getPost('dikirim_oleh'),
        'dikirim_telepon' => $this->request->getPost('dikirim_telepon')
    ];
    
    // Jika menggunakan project manual, tambahkan ke keterangan
    if (!empty($projectManual)) {
        // Bersihkan keterangan lama dari project
        $oldKeterangan = $updateData['keterangan'] ?? '';
        $lines = explode("\n", $oldKeterangan);
        $newLines = [];
        
        foreach ($lines as $line) {
            if (strpos(trim($line), 'Project:') !== 0 && strpos(trim($line), 'Invoice:') !== 0) {
                $newLines[] = $line;
            }
        }
        
        // Tambahkan project dan invoice ke keterangan
        $newKeterangan = "Project: " . $projectManual . "\n";
        if (!empty($invoiceManual)) {
            $newKeterangan .= "Invoice: " . $invoiceManual . "\n";
        }
        
        $updateData['keterangan'] = $newKeterangan . implode("\n", $newLines);
    }
    
    // Tambahkan data disiapkan_oleh
    if ($karyawan) {
        $updateData['disiapkan_oleh'] = $karyawan['nama_lengkap'] ?? $suratJalan['disiapkan_oleh'];
        $updateData['disiapkan_telepon'] = $karyawan['telepon'] ?? $suratJalan['disiapkan_telepon'];
        $updateData['disiapkan_jabatan'] = $karyawan['jabatan'] ?? $suratJalan['disiapkan_jabatan'];
    } else if ($disiapkanOleh) {
        // Jika ada data dari form tapi karyawan tidak ditemukan
        $updateData['disiapkan_oleh'] = $disiapkanOleh;
        $updateData['disiapkan_telepon'] = $this->request->getPost('disiapkan_telepon') ?? $suratJalan['disiapkan_telepon'];
        $updateData['disiapkan_jabatan'] = $this->request->getPost('disiapkan_jabatan') ?? $suratJalan['disiapkan_jabatan'];
    }
    
    log_message('debug', 'Update Data: ' . print_r($updateData, true));
    
    try {
        // Update surat jalan
        $this->suratJalanModel->update($id, $updateData);
        
        // Update items - HAPUS ITEMS LAMA DULU
        // Delete existing items terlebih dahulu
        $this->suratJalanItemModel->where('surat_jalan_id', $id)->delete();
        
        // Insert items baru jika ada
        $items = $this->request->getPost('items');
        if ($items && is_array($items)) {
            $this->suratJalanItemModel->insertBatchItems($id, $items);
        }
        
        // Log activity
        if (function_exists('log_activity')) {
            log_activity($userData['id'], 'update', 'surat_jalan', $id, 'Mengupdate surat jalan: ' . $updateData['nomor_surat_jalan']);
        }
        
        return redirect()->to(site_url('sales/surat-jalan/detail/' . $id))
            ->with('success', 'Surat jalan berhasil diupdate');
            
    } catch (\Exception $e) {
        log_message('error', 'Update Error: ' . $e->getMessage());
        return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
    }
}
    
    /**
     * Method untuk delete surat jalan
     */
    public function delete($id)
    {
        $userData = $this->getUserData();
        
        // Cek apakah surat jalan ada
        $suratJalan = $this->suratJalanModel->find($id);
        
        if (!$suratJalan) {
            return redirect()->to(site_url('sales/surat-jalan'))
                ->with('error', 'Surat jalan tidak ditemukan');
        }
        
        // Cek akses untuk delete
        if ($userData['role'] === 'sales') {
            if (!in_array($suratJalan['status'], ['draft', 'diproses'])) {
                return redirect()->to(site_url('sales/surat-jalan'))
                    ->with('error', 'Surat jalan dengan status ini tidak dapat dihapus');
            }
            
            $project = $this->projectModel->find($suratJalan['project_id']);
            if ($project) {
                $client = $this->clientModel->find($project['client_id']);
                if ($client && $client['sales_id'] != $userData['karyawan_id'] && $suratJalan['created_by'] != $userData['id']) {
                    return redirect()->to(site_url('sales/surat-jalan'))
                        ->with('error', 'Anda tidak memiliki akses untuk menghapus surat jalan ini');
                }
            }
        }
        
        try {
            // Get nomor untuk log
            $nomor = $suratJalan['nomor_surat_jalan'];
            
            // Delete surat jalan (akan otomatis delete items karena foreign key cascade)
            $this->suratJalanModel->delete($id);
            
            // Log activity
            if (function_exists('log_activity')) {
                log_activity($userData['id'], 'delete', 'surat_jalan', $id, 'Menghapus surat jalan: ' . $nomor);
            }
            
            return redirect()->to(site_url('sales/surat-jalan'))
                ->with('success', 'Surat jalan berhasil dihapus');
                
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Method untuk detail surat jalan
     */
    public function detail($id)
    {
        $userData = $this->getUserData();
        
        // Get surat jalan dengan detail lengkap
        $suratJalan = $this->suratJalanModel->getSuratJalanWithDetails($id);
        
        if (!$suratJalan) {
            return redirect()->to(site_url('sales/surat-jalan'))
                ->with('error', 'Surat jalan tidak ditemukan');
        }
        
        // Cek akses untuk view detail
        if ($userData['role'] === 'sales') {
            $project = $this->projectModel->find($suratJalan['project_id']);
            if ($project) {
                $client = $this->clientModel->find($project['client_id']);
                if ($client && $client['sales_id'] != $userData['karyawan_id'] && $suratJalan['created_by'] != $userData['id']) {
                    return redirect()->to(site_url('sales/surat-jalan'))
                        ->with('error', 'Anda tidak memiliki akses untuk melihat surat jalan ini');
                }
            }
        }
        
        // Get items
        $items = $this->suratJalanItemModel->getItemsForDisplay($id);
        
        $data = [
            'title' => 'Detail Surat Jalan',
            'subtitle' => 'Detail lengkap surat jalan',
            'suratJalan' => $suratJalan,
            'items' => $items,
            'userRole' => $userData['role'],
            'active' => 'surat_jalan'
        ];
        
        return $this->renderView('sales/surat-jalan/detail', $data);
    }
    
    /**
     * Method untuk print surat jalan
     */
    public function print($id)
    {
        $userData = $this->getUserData();
        
        // Get surat jalan dengan detail lengkap
        $suratJalan = $this->suratJalanModel->getSuratJalanWithDetails($id);
        
        if (!$suratJalan) {
            return redirect()->to(site_url('sales/surat-jalan'))
                ->with('error', 'Surat jalan tidak ditemukan');
        }
        
        // Cek akses untuk print
        if ($userData['role'] === 'sales') {
            $project = $this->projectModel->find($suratJalan['project_id']);
            if ($project) {
                $client = $this->clientModel->find($project['client_id']);
                if ($client && $client['sales_id'] != $userData['karyawan_id'] && $suratJalan['created_by'] != $userData['id']) {
                    return redirect()->to(site_url('sales/surat-jalan'))
                        ->with('error', 'Anda tidak memiliki akses untuk mencetak surat jalan ini');
                }
            }
        }
        
        // Get items
        $items = $this->suratJalanItemModel->getItemsForDisplay($id);
        
        $data = [
            'title' => 'Cetak Surat Jalan',
            'suratJalan' => $suratJalan,
            'items' => $items
        ];
        
        return view('sales/surat-jalan/print', $data);
    }
    
    /**
     * Method untuk update status surat jalan
     */
    public function updateStatus($id)
    {
        $userData = $this->getUserData();
        
        // Cek apakah surat jalan ada
        $suratJalan = $this->suratJalanModel->find($id);
        
        if (!$suratJalan) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Surat jalan tidak ditemukan'
            ]);
        }
        
        // Cek akses untuk update status
        if ($userData['role'] === 'sales') {
            $project = $this->projectModel->find($suratJalan['project_id']);
            if ($project) {
                $client = $this->clientModel->find($project['client_id']);
                if ($client && $client['sales_id'] != $userData['karyawan_id'] && $suratJalan['created_by'] != $userData['id']) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses untuk mengupdate status surat jalan ini'
                    ]);
                }
            }
        }
        
        $status = $this->request->getPost('status');
        $allowedStatuses = ['draft', 'diproses', 'dikirim', 'diterima', 'ditolak', 'dibatalkan'];
        
        if (!in_array($status, $allowedStatuses)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Status tidak valid'
            ]);
        }
        
        try {
            // Update status
            $this->suratJalanModel->update($id, ['status' => $status]);
            
            // Log activity
            if (function_exists('log_activity')) {
                log_activity($userData['id'], 'update_status', 'surat_jalan', $id, 'Mengupdate status surat jalan ke: ' . $status);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Status berhasil diupdate',
                'status' => $status
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get invoice items untuk project tertentu (AJAX)
     */
    public function getInvoiceItems($projectId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method not allowed']);
        }
        
        try {
            // Get invoices untuk project ini
            $invoices = $this->invoiceModel->where('project_id', $projectId)
                                          ->where('status', 'terbit')
                                          ->findAll();
            
            // Get client data dari project
            $project = $this->projectModel->find($projectId);
            $client = $project ? $this->clientModel->find($project['client_id']) : null;
            
            return $this->response->setJSON([
                'success' => true,
                'invoices' => $invoices,
                'client' => $client
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Export Excel untuk surat jalan tertentu
     */
    public function exportExcel($id = null)
    {
        if ($id) {
            // Export single surat jalan
            return $this->exportSingleExcel($id);
        } else {
            // Export all surat jalan
            return $this->exportAllExcel();
        }
    }
    
    /**
     * Export PDF untuk surat jalan
     */
    public function exportPdf($id)
    {
        $userData = $this->getUserData();
        
        // Get surat jalan dengan detail lengkap
        $suratJalan = $this->suratJalanModel->getSuratJalanWithDetails($id);
        
        if (!$suratJalan) {
            return redirect()->to(site_url('sales/surat-jalan'))
                ->with('error', 'Surat jalan tidak ditemukan');
        }
        
        // Cek akses untuk export
        if ($userData['role'] === 'sales') {
            $project = $this->projectModel->find($suratJalan['project_id']);
            if ($project) {
                $client = $this->clientModel->find($project['client_id']);
                if ($client && $client['sales_id'] != $userData['karyawan_id'] && $suratJalan['created_by'] != $userData['id']) {
                    return redirect()->to(site_url('sales/surat-jalan'))
                        ->with('error', 'Anda tidak memiliki akses untuk mengexport surat jalan ini');
                }
            }
        }
        
        // Get items
        $items = $this->suratJalanItemModel->getItemsForDisplay($id);
        
        // Load PDF library
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->setPaper('A4', 'portrait');
        
        $data = [
            'suratJalan' => $suratJalan,
            'items' => $items,
            'title' => 'Surat Jalan ' . $suratJalan['nomor_surat_jalan']
        ];
        
        $html = view('sales/surat-jalan/pdf_template', $data);
        $dompdf->loadHtml($html);
        $dompdf->render();
        
        // Output PDF
        $dompdf->stream('surat_jalan_' . $suratJalan['nomor_surat_jalan'] . '.pdf', ['Attachment' => 1]);
    }
    
    /**
 * Cetak PDF surat jalan
 */
public function cetakPdf($id)
{
    $userData = $this->getUserData();
    
    // Get surat jalan dengan detail lengkap
    $suratJalan = $this->suratJalanModel->getSuratJalanWithDetails($id);
    
    if (!$suratJalan) {
        return redirect()->to(site_url('sales/surat-jalan'))
            ->with('error', 'Surat jalan tidak ditemukan');
    }
    
    // Cek akses untuk cetak
    if ($userData['role'] === 'sales') {
        $project = $this->projectModel->find($suratJalan['project_id']);
        if ($project) {
            $client = $this->clientModel->find($project['client_id']);
            if ($client && $client['sales_id'] != $userData['karyawan_id'] && $suratJalan['created_by'] != $userData['id']) {
                return redirect()->to(site_url('sales/surat-jalan'))
                    ->with('error', 'Anda tidak memiliki akses untuk mencetak surat jalan ini');
            }
        }
    }
    
    // Get items
    $items = $this->suratJalanItemModel->getItemsForDisplay($id);
    
    // Load PDF library
    $dompdf = new \Dompdf\Dompdf();
    $dompdf->setPaper('A4', 'portrait');
    
    $data = [
        'suratJalan' => $suratJalan,
        'items' => $items,
        'title' => 'Surat Jalan ' . $suratJalan['nomor_surat_jalan'],
        'perusahaan' => [
            'nama' => $suratJalan['perusahaan_pengirim_nama'] ?? 'PT. Cipta Duta Wacana',
            'alamat' => $suratJalan['perusahaan_pengirim_alamat'] ?? 'Villa Bintaro Regency, Jl. Riau Blok K1 No. 2, Pondok Kacang Timur, Tangerang Selatan 15226',
            'website' => $suratJalan['perusahaan_pengirim_website'] ?? 'www.cdw-engineering.com'
        ]
    ];
    
    $html = view('sales/surat-jalan/pdf_template', $data);
    $dompdf->loadHtml($html);
    $dompdf->render();
    
    // Output PDF
    $dompdf->stream('surat_jalan_' . $suratJalan['nomor_surat_jalan'] . '.pdf', ['Attachment' => 1]);
}

public function cetak($id)
{
    $userData = $this->getUserData();
    
    // Get surat jalan dengan detail lengkap
    $suratJalan = $this->suratJalanModel->getSuratJalanWithDetails($id);
    
    if (!$suratJalan) {
        return redirect()->to(site_url('sales/surat-jalan'))
            ->with('error', 'Surat jalan tidak ditemukan');
    }
    
    // Cek akses untuk cetak
    if ($userData['role'] === 'sales') {
        $project = $this->projectModel->find($suratJalan['project_id']);
        if ($project) {
            $client = $this->clientModel->find($project['client_id']);
            if ($client && $client['sales_id'] != $userData['karyawan_id'] && $suratJalan['created_by'] != $userData['id']) {
                return redirect()->to(site_url('sales/surat-jalan'))
                    ->with('error', 'Anda tidak memiliki akses untuk mencetak surat jalan ini');
            }
        }
    }
    
    // Get items
    $items = $this->suratJalanItemModel->getItemsForDisplay($id);
    
    // Cari data karyawan untuk sales yang membuat surat jalan
    $karyawanModel = new \App\Models\KaryawanModel();
    $salesKaryawan = null;
    
    // Cari berdasarkan nama yang ada di surat jalan
    if (!empty($suratJalan['disiapkan_oleh'])) {
        $salesKaryawan = $karyawanModel->where('nama_lengkap', $suratJalan['disiapkan_oleh'])->first();
    }
    
    // Jika tidak ditemukan, cari berdasarkan user yang login
    if (!$salesKaryawan && !empty($userData['karyawan_id'])) {
        $salesKaryawan = $karyawanModel->find($userData['karyawan_id']);
    }
    
    // Update data surat jalan dengan informasi dari karyawan
    if ($salesKaryawan) {
        $suratJalan['disiapkan_oleh'] = $salesKaryawan['nama_lengkap'] ?? $suratJalan['disiapkan_oleh'];
        $suratJalan['disiapkan_telepon'] = $salesKaryawan['telepon'] ?? $suratJalan['disiapkan_telepon'];
        $suratJalan['disiapkan_jabatan'] = $salesKaryawan['jabatan'] ?? $suratJalan['disiapkan_jabatan'];
    }
    
    $data = [
        'title' => 'Cetak Surat Jalan',
        'surat_jalan' => $suratJalan,
        'items' => $items,
        'perusahaan' => [
            'nama' => $suratJalan['perusahaan_pengirim_nama'] ?? 'PT. Cipta Duta Wacana',
            'alamat' => $suratJalan['perusahaan_pengirim_alamat'] ?? 'Villa Bintaro Regency, Jl. Riau Blok K1 No. 2, Pondok Kacang Timur, Tangerang Selatan 15226',
            'website' => $suratJalan['perusahaan_pengirim_website'] ?? 'www.cdw-engineering.com'
        ]
    ];
    
    return view('sales/surat-jalan/cetak', $data);
}
    
    // ============================================
    // PRIVATE METHODS
    // ============================================
    
    /**
     * Export single surat jalan ke Excel
     */
    private function exportSingleExcel($id)
    {
        // Implementasi export single Excel
        // (Bisa menggunakan library seperti PhpSpreadsheet)
        return redirect()->back()->with('info', 'Fitur export Excel akan segera tersedia');
    }
    
    /**
     * Export all surat jalan ke Excel
     */
    private function exportAllExcel()
    {
        // Implementasi export all Excel
        // (Bisa menggunakan library seperti PhpSpreadsheet)
        return redirect()->back()->with('info', 'Fitur export Excel akan segera tersedia');
    }

}