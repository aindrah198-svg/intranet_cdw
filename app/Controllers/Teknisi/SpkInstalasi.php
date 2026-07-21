<?php

namespace App\Controllers\Teknisi;

use App\Controllers\BaseController;
use App\Models\Teknisi\SpkInstalasiModel;
use App\Models\Teknisi\SpkInstalasiItemModel;
use App\Models\KaryawanModel;
use App\Models\ClientModel;
use App\Models\UserModel;

class SpkInstalasi extends BaseController
{
    protected $spkModel;
    protected $spkItemModel;
    protected $karyawanModel;
    protected $clientModel;
    protected $userModel;
    protected $db;
    
    public function __construct()
    {
        $this->spkModel = new SpkInstalasiModel();
        $this->spkItemModel = new SpkInstalasiItemModel();
        $this->karyawanModel = new KaryawanModel();
        $this->clientModel = new ClientModel();
        $this->userModel = new UserModel();
        $this->db = \Config\Database::connect();
        
        // Cek login
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Halaman daftar SPK
     */
    public function index()
    {
        $data['title'] = 'Daftar SPK / Tugas Instalasi';
        $data['subtitle'] = 'Kelola semua SPK dan tugas instalasi';
        
        // Ambil filter dari request
        $status = $this->request->getGet('status');
        $prioritas = $this->request->getGet('prioritas');
        $tanggal_mulai = $this->request->getGet('tanggal_mulai');
        $tanggal_selesai = $this->request->getGet('tanggal_selesai');
        
        // Get data SPK dengan filter
        $data['spk_list'] = $this->spkModel->getFiltered($status, $prioritas, $tanggal_mulai, $tanggal_selesai);
        
        // Debug: log hasil query untuk memastikan tidak error
        log_message('debug', 'Jumlah SPK ditemukan: ' . count($data['spk_list']));
        
        // Data untuk filter
        $data['status_options'] = [
            'semua' => 'Semua Status',
            'Draft' => 'Draft',
            'Dijadwalkan' => 'Dijadwalkan',
            'Dalam Pengerjaan' => 'Dalam Pengerjaan',
            'Selesai' => 'Selesai',
            'Ditunda' => 'Ditunda',
            'Dibatalkan' => 'Dibatalkan'
        ];
        
        $data['prioritas_options'] = [
            'semua' => 'Semua Prioritas',
            'Rendah' => 'Rendah',
            'Normal' => 'Normal',
            'Tinggi' => 'Tinggi',
            'Urgent' => 'Urgent'
        ];
        
        // Statistik
        $data['statistik'] = $this->spkModel->getStatistik();
        
        return view('teknisi/spk_instalasi/index', $data);
    }

    /**
     * Halaman tambah SPK
     */
    public function create()
    {
        $data['title'] = 'Tambah SPK / Tugas Instalasi';
        $data['subtitle'] = 'Buat SPK baru untuk tugas instalasi';
        
        // Ambil data untuk dropdown
        $data['teknisi_list'] = $this->karyawanModel->where('jabatan', 'Teknisi')->findAll();
        $data['managers'] = $this->karyawanModel->whereIn('jabatan', ['Senior Teknisi', 'Project Manager', 'Supervisor'])->findAll();
        $data['clients'] = $this->clientModel->where('status', 'active')->findAll();
        
        // Generate nomor SPK otomatis
        $data['nomor_spk_otomatis'] = $this->spkModel->generateNomorSpk();
        
        // Options untuk dropdown
        $data['kategori_options'] = [
            '' => '-- Pilih Kategori --',
            'Instalasi Baru' => 'Instalasi Baru',
            'Maintenance' => 'Maintenance',
            'Perbaikan' => 'Perbaikan',
            'Kalibrasi' => 'Kalibrasi',
            'Inspeksi' => 'Inspeksi',
            'Lainnya' => 'Lainnya'
        ];
        
        $data['prioritas_options'] = [
            'Rendah' => 'Rendah',
            'Normal' => 'Normal',
            'Tinggi' => 'Tinggi',
            'Urgent' => 'Urgent'
        ];
        
        $data['status_options'] = [
            'Draft' => 'Draft',
            'Dijadwalkan' => 'Dijadwalkan',
            'Selesai' => 'Selesai'
        ];
        
        return view('teknisi/spk_instalasi/create', $data);
    }

/**
 * Simpan data SPK baru
 */
public function store()
{
    // LOG SEMUA POST DATA
    log_message('debug', '========== STORE METHOD START ==========');
    log_message('debug', 'All POST data: ' . json_encode($this->request->getPost()));
    
    // Validasi input
    $rules = [
        'nomor_spk' => 'required|is_unique[spk_instalasi.nomor_spk]',
        'judul_pekerjaan' => 'required',
        'tanggal_mulai' => 'required|valid_date',
        'prioritas' => 'required',
        'client_id' => 'required|numeric'
    ];
    
    if (!$this->validate($rules)) {
        log_message('error', 'Validation errors: ' . json_encode($this->validator->getErrors()));
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }
    
    // ===== DEFINISIKAN SEMUA VARIABEL YANG DIPERLUKAN =====
    $client_id = $this->request->getPost('client_id');
    $client_nama = '';
    $client_alamat = '';
    $client_kontak = '';
    $client_catatan = '';
    $client_kontak_input = '';
    $catatan_client_input = '';
    $lokasi = '';
    $tim_teknisi_json = '[]';
    $dokumen_pendukung = null;
    $dokumentasi = null;
    $estimasi_biaya = 0;
    $target_selesai = '';
    $tanggal_selesai = '';
    $tanggal_selesai_aktual = '';
    $laporan = '';
    
    // ===== AMBIL DATA CLIENT =====
    if (!empty($client_id)) {
        $clientData = $this->clientModel->find($client_id);
        
        if (!$clientData) {
            return redirect()->back()->withInput()->with('error', 'Data client tidak ditemukan');
        }
        
        // Data client - HANDLE BAIK ARRAY MAUPUN OBJECT
        if (is_object($clientData)) {
            // Jika object
            $client_nama = $clientData->nama_perusahaan ?? '';
            $client_alamat = $clientData->alamat ?? '';
            $client_kontak = $clientData->client_kontak ?? $clientData->telepon ?? '';
            $client_catatan = $clientData->catatan_client ?? '';
        } else {
            // Jika array
            $client_nama = $clientData['nama_perusahaan'] ?? '';
            $client_alamat = $clientData['alamat'] ?? '';
            $client_kontak = $clientData['client_kontak'] ?? $clientData['telepon'] ?? '';
            $client_catatan = $clientData['catatan_client'] ?? '';
        }
    }
    
    // ===== AMBIL INPUT DARI FORM =====
    $client_kontak_input = $this->request->getPost('client_kontak_input');
    $catatan_client_input = $this->request->getPost('catatan_client_input');
    
    // Gunakan input dari form jika ada
    if (!empty($client_kontak_input)) {
        $client_kontak = $client_kontak_input;
    }
    
    if (!empty($catatan_client_input)) {
        $client_catatan = $catatan_client_input;
    }
    
    // ===== LOKASI =====
    $lokasi_input = $this->request->getPost('lokasi');
    if (!empty($lokasi_input)) {
        $lokasi = $lokasi_input;
    } else {
        $lokasi = $client_alamat;
    }
    
// ===== TIM TEKNISI =====
$tim_teknisi = $this->request->getPost('tim_teknisi');

log_message('debug', 'Raw tim_teknisi: ' . json_encode($tim_teknisi));
log_message('debug', 'Raw tim_teknisi type: ' . gettype($tim_teknisi));

if (is_array($tim_teknisi) && !empty($tim_teknisi)) {
    // Filter hanya nilai yang valid (numeric dan tidak kosong)
    $tim_teknisi_filtered = array_filter($tim_teknisi, function($value) {
        return !empty($value) && is_numeric($value);
    });
    
    // Re-index array
    $tim_teknisi_filtered = array_values($tim_teknisi_filtered);
    $tim_teknisi_json = json_encode($tim_teknisi_filtered);
    
    log_message('debug', 'Filtered tim_teknisi: ' . json_encode($tim_teknisi_filtered));
} else {
    // Jika tidak ada teknisi dipilih atau bukan array, simpan sebagai array kosong
    $tim_teknisi_json = json_encode([]);
    log_message('debug', 'tim_teknisi kosong atau bukan array, menggunakan array kosong');
}

log_message('debug', 'Final tim_teknisi JSON: ' . $tim_teknisi_json);
    
    // ===== DOKUMEN PENDUKUNG =====
    $file = $this->request->getFile('dokumen_pendukung');
    if ($file && $file->isValid() && !$file->hasMoved()) {
        $validationRule = [
            'dokumen_pendukung' => [
                'uploaded[dokumen_pendukung]',
                'mime_in[dokumen_pendukung,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/jpg,image/jpeg,image/png]',
                'max_size[dokumen_pendukung,5120]',
            ],
        ];
        
        if ($this->validate($validationRule)) {
            $newName = $file->getRandomName();
            $file->move('uploads/spk/dokumen', $newName);
            $dokumen_pendukung = 'uploads/spk/dokumen/' . $newName;
        } else {
            return redirect()->back()->withInput()->with('error', 'File dokumen tidak valid. Maks 5MB (PDF/DOC/JPG/PNG)');
        }
    }
    
    // ===== DOKUMENTASI =====
    $file_dokumentasi = $this->request->getFile('dokumentasi');
    if ($file_dokumentasi && $file_dokumentasi->isValid() && !$file_dokumentasi->hasMoved()) {
        $validationRule = [
            'dokumentasi' => [
                'uploaded[dokumentasi]',
                'mime_in[dokumentasi,image/jpg,image/jpeg,image/png]',
                'max_size[dokumentasi,5120]',
            ],
        ];
        
        if ($this->validate($validationRule)) {
            $newName = $file_dokumentasi->getRandomName();
            $file_dokumentasi->move('uploads/spk/dokumentasi', $newName);
            $dokumentasi = 'uploads/spk/dokumentasi/' . $newName;
        } else {
            return redirect()->back()->withInput()->with('error', 'File dokumentasi tidak valid. Maks 5MB (JPG/PNG)');
        }
    }
    
    // ===== ESTIMASI BIAYA =====
    $estimasi_biaya = $this->request->getPost('estimasi_biaya') ?: 0;
    
    // ===== TANGGAL =====
    $target_selesai = $this->request->getPost('target_selesai');
    $tanggal_selesai = $target_selesai; // Samakan dengan target selesai
    $tanggal_selesai_aktual = $this->request->getPost('tanggal_selesai_aktual');
    
    log_message('debug', 'Target selesai: ' . ($target_selesai ?: 'kosong'));
    log_message('debug', 'Tanggal selesai aktual: ' . ($tanggal_selesai_aktual ?: 'kosong'));
    
    // ===== LAPORAN =====
    $laporan = $this->request->getPost('laporan');
    log_message('debug', 'Laporan: ' . ($laporan ?: 'kosong'));
    
    // ===== DATA UNTUK DISIMPAN =====
    $data = [
        'nomor_spk' => $this->request->getPost('nomor_spk'),
        'judul_pekerjaan' => $this->request->getPost('judul_pekerjaan'),
        'deskripsi' => $this->request->getPost('deskripsi'),
        'lokasi' => $lokasi,
        'client_id' => $client_id,
        // Field-field client
        'client_nama' => $client_nama,
        'client_alamat' => $client_alamat,
        'client_kontak' => $client_kontak,
        'catatan_client' => $client_catatan,
        'tanggal_mulai' => $this->request->getPost('tanggal_mulai'),
        'tanggal_selesai' => $tanggal_selesai,
        'target_selesai' => $target_selesai,
        'tanggal_selesai_aktual' => $tanggal_selesai_aktual,
        'prioritas' => $this->request->getPost('prioritas'),
        'status' => $this->request->getPost('status') ?? 'Draft',
        'kategori_pekerjaan' => $this->request->getPost('kategori_pekerjaan'),
        'tim_teknisi' => $tim_teknisi_json,
        'project_manager_id' => $this->request->getPost('project_manager_id') ?: null,
        'catatan' => $this->request->getPost('catatan'),
        'laporan' => $laporan,
        'estimasi_biaya' => $estimasi_biaya,
        'progress_persen' => $this->request->getPost('progress_persen') ?? 0,
        'dokumen_pendukung' => $dokumen_pendukung,
        'dokumentasi' => $dokumentasi,
        'dibuat_oleh' => session()->get('user_id'),
        'dibuat_tanggal' => date('Y-m-d H:i:s')
    ];
    
    log_message('debug', 'Final data to insert: ' . json_encode($data));
    
    // ===== ITEM PEKERJAAN =====
    $items = [];
    $nama_items = $this->request->getPost('item_nama') ?: [];
    $deskripsi_items = $this->request->getPost('item_deskripsi') ?: [];
    $qty_items = $this->request->getPost('item_qty') ?: [];
    $satuan_items = $this->request->getPost('item_satuan') ?: [];
    $harga_items = $this->request->getPost('item_harga') ?: [];
    $keterangan_items = $this->request->getPost('item_keterangan') ?: [];
    $status_items = $this->request->getPost('item_status') ?: [];
    
    log_message('debug', 'Status items from form: ' . json_encode($status_items));
    
    foreach ($nama_items as $key => $nama) {
        if (!empty(trim($nama))) {
            // Bersihkan format harga
            $harga = 0;
            if (isset($harga_items[$key]) && !empty($harga_items[$key])) {
                $harga = str_replace('.', '', $harga_items[$key]);
                $harga = str_replace(',', '.', $harga);
                $harga = (float) $harga;
            }
            
            // Bersihkan format qty
            $qty = isset($qty_items[$key]) ? (float) str_replace(',', '.', $qty_items[$key]) : 1;
            
            // Ambil status item
            $status = 'Pending';
            if (isset($status_items[$key]) && !empty($status_items[$key])) {
                $status = $status_items[$key];
            }
            
            log_message('debug', "Item $key: nama=$nama, status=$status");
            
            $items[] = [
                'nama_item' => $nama,
                'deskripsi' => $deskripsi_items[$key] ?? '',
                'qty' => $qty,
                'satuan' => $satuan_items[$key] ?? 'unit',
                'harga' => $harga,
                'keterangan' => $keterangan_items[$key] ?? '',
                'status' => $status
            ];
        }
    }
    
    log_message('debug', 'Final items to save: ' . json_encode($items));
    
    // ===== TRANSAKSI DATABASE =====
    $this->db->transStart();
    
    try {
        // Insert SPK
        $spk_id = $this->spkModel->insert($data);
        
        if (!$spk_id) {
            $errors = $this->spkModel->errors();
            log_message('error', 'SPK Model Errors: ' . json_encode($errors));
            throw new \Exception('Gagal menyimpan SPK: ' . json_encode($errors));
        }
        
        log_message('debug', 'SPK inserted with ID: ' . $spk_id);
        
        // Simpan items
        if (!empty($items)) {
            $saveItems = $this->spkItemModel->saveItems($spk_id, $items);
            if (!$saveItems) {
                throw new \Exception('Gagal menyimpan item pekerjaan');
            }
            log_message('debug', 'Items saved: ' . count($items) . ' items');
        }
        
        // Catat log aktivitas
        $this->logAktivitas($spk_id, 'CREATE', 'SPK dibuat dengan nomor: ' . $data['nomor_spk']);
        
        $this->db->transCommit();
        
        return redirect()->to('teknisi/tugas-proyek/spk')
            ->with('success', 'SPK berhasil ditambahkan. Nomor SPK: ' . $data['nomor_spk']);
            
    } catch (\Exception $e) {
        $this->db->transRollback();
        
        // Hapus file jika ada
        if ($dokumen_pendukung && file_exists($dokumen_pendukung)) {
            unlink($dokumen_pendukung);
        }
        if ($dokumentasi && file_exists($dokumentasi)) {
            unlink($dokumentasi);
        }
        
        log_message('error', '[SpkInstalasi::store] Exception: ' . $e->getMessage());
        
        return redirect()->back()
            ->withInput()
            ->with('error', 'Gagal menyimpan SPK: ' . $e->getMessage());
    }
}

    /**
 * Halaman detail SPK
 */
public function detail($id)
{
    $data['title'] = 'Detail SPK';
    
    // Ambil data SPK dengan relasi client
    $data['spk'] = $this->spkModel->getWithRelations($id);
    
    if (!$data['spk']) {
        return redirect()->to('teknisi/tugas-proyek/spk')->with('error', 'SPK tidak ditemukan');
    }
    
    // Ambil item-item SPK
    $data['items'] = $this->spkItemModel->getBySpkId($id);
    
    // Hitung total items
    $data['total_items'] = $this->spkItemModel->getTotalBySpkId($id);
    $data['statistik_items'] = $this->spkItemModel->getStatistik($id);
    
    // Ambil log aktivitas
    $data['logs'] = $this->db->table('spk_instalasi_log')
        ->select('spk_instalasi_log.*, users.name as user_nama')
        ->join('users', 'users.id = spk_instalasi_log.user_id', 'left')
        ->where('spk_id', $id)
        ->orderBy('created_at', 'DESC')
        ->limit(10)
        ->get()
        ->getResult();
    
    // ===== PERBAIKAN: DECODE TIM TEKNISI =====
    $data['tim_teknisi_ids'] = [];
    if (!empty($data['spk']->tim_teknisi)) {
        $decoded = json_decode($data['spk']->tim_teknisi, true);
        if (is_array($decoded)) {
            $data['tim_teknisi_ids'] = $decoded;
        }
    }
    
    log_message('debug', 'Tim teknisi IDs dari DB: ' . json_encode($data['tim_teknisi_ids']));
    
    $data['tim_teknisi'] = [];
    if (!empty($data['tim_teknisi_ids'])) {
        $teknisiList = $this->karyawanModel->whereIn('id', $data['tim_teknisi_ids'])->findAll();
        foreach ($teknisiList as $teknisi) {
            if (is_array($teknisi)) {
                $data['tim_teknisi'][] = (object) $teknisi;
            } else {
                $data['tim_teknisi'][] = $teknisi;
            }
        }
    }
    
    log_message('debug', 'Jumlah teknisi ditemukan: ' . count($data['tim_teknisi']));
    
    // Ambil data project manager
    if ($data['spk']->project_manager_id) {
        $manager = $this->karyawanModel->find($data['spk']->project_manager_id);
        if (is_array($manager)) {
            $data['project_manager'] = (object) $manager;
        } else {
            $data['project_manager'] = $manager;
        }
    } else {
        $data['project_manager'] = null;
    }
    
    // Ambil data pembuat
    if ($data['spk']->dibuat_oleh) {
        $pembuat = $this->userModel->find($data['spk']->dibuat_oleh);
        if (is_array($pembuat)) {
            $data['pembuat'] = (object) $pembuat;
        } else {
            $data['pembuat'] = $pembuat;
        }
    } else {
        $data['pembuat'] = null;
    }
    
    return view('teknisi/spk_instalasi/detail', $data);
}

    /**
     * Halaman edit SPK
     */
    public function edit($id)
    {
        $data['title'] = 'Edit SPK';
        
        // Ambil data SPK
        $data['spk'] = $this->spkModel->find($id);
        
        if (!$data['spk']) {
            return redirect()->to('teknisi/tugas-proyek/spk')->with('error', 'SPK tidak ditemukan');
        }
        
        // Data untuk dropdown
        $data['teknisi_list'] = $this->karyawanModel->where('jabatan', 'Teknisi')->findAll();
        $data['managers'] = $this->karyawanModel->whereIn('jabatan', ['Senior Teknisi', 'Project Manager', 'Supervisor'])->findAll();
        $data['clients'] = $this->clientModel->where('status', 'active')->findAll();
        
        // Ambil item-item SPK
        $data['items'] = $this->spkItemModel->getBySpkId($id);
        
        // Decode tim teknisi
        $data['tim_teknisi_ids'] = json_decode($data['spk']->tim_teknisi ?? '[]', true);
        
        // Options untuk dropdown
        $data['kategori_options'] = [
            '' => '-- Pilih Kategori --',
            'Instalasi Baru' => 'Instalasi Baru',
            'Maintenance' => 'Maintenance',
            'Perbaikan' => 'Perbaikan',
            'Kalibrasi' => 'Kalibrasi',
            'Inspeksi' => 'Inspeksi',
            'Lainnya' => 'Lainnya'
        ];
        
        $data['prioritas_options'] = [
            'Rendah' => 'Rendah',
            'Normal' => 'Normal',
            'Tinggi' => 'Tinggi',
            'Urgent' => 'Urgent'
        ];
        
        $data['status_options'] = [
            'Draft' => 'Draft',
            'Dijadwalkan' => 'Dijadwalkan',
            'Dalam Pengerjaan' => 'Dalam Pengerjaan',
            'Selesai' => 'Selesai',
            'Ditunda' => 'Ditunda',
            'Dibatalkan' => 'Dibatalkan'
        ];
        
        return view('teknisi/spk_instalasi/edit', $data);
    }

 /**
 * Update data SPK
 */
public function update($id)
{
    // LOG SEMUA POST DATA
    log_message('debug', '========== UPDATE METHOD START ==========');
    log_message('debug', 'All POST data: ' . json_encode($this->request->getPost()));
    
    // Validasi input
    $rules = [
        'nomor_spk' => "required|is_unique[spk_instalasi.nomor_spk,id,$id]",
        'judul_pekerjaan' => 'required',
        'tanggal_mulai' => 'required|valid_date',
        'prioritas' => 'required',
        'client_id' => 'required|numeric'
    ];
    
    if (!$this->validate($rules)) {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }
    
    // ===== DEFINISIKAN SEMUA VARIABEL YANG DIPERLUKAN =====
    $client_id = $this->request->getPost('client_id');
    $client_nama = '';
    $client_alamat = '';
    $client_kontak = '';
    $client_catatan = '';
    $client_kontak_input = '';
    $catatan_client_input = '';
    $lokasi = '';
    $tim_teknisi_json = '[]';
    $dokumen_pendukung = null;
    $dokumentasi = null;
    $estimasi_biaya = 0;
    $target_selesai = '';
    $tanggal_selesai = '';
    $tanggal_selesai_aktual = '';
    $laporan = '';
    
    // ===== AMBIL DATA CLIENT =====
    if (!empty($client_id)) {
        $clientData = $this->clientModel->find($client_id);
        
        if (!$clientData) {
            return redirect()->back()->withInput()->with('error', 'Data client tidak ditemukan');
        }
        
        // Data client - HANDLE BAIK ARRAY MAUPUN OBJECT
        if (is_object($clientData)) {
            // Jika object
            $client_nama = $clientData->nama_perusahaan ?? '';
            $client_alamat = $clientData->alamat ?? '';
            $client_kontak = $clientData->client_kontak ?? $clientData->telepon ?? '';
            $client_catatan = $clientData->catatan_client ?? '';
        } else {
            // Jika array
            $client_nama = $clientData['nama_perusahaan'] ?? '';
            $client_alamat = $clientData['alamat'] ?? '';
            $client_kontak = $clientData['client_kontak'] ?? $clientData['telepon'] ?? '';
            $client_catatan = $clientData['catatan_client'] ?? '';
        }
    }
    
    // ===== AMBIL INPUT DARI FORM =====
    $client_kontak_input = $this->request->getPost('client_kontak_input');
    $catatan_client_input = $this->request->getPost('catatan_client_input');
    
    // Gunakan input dari form jika ada
    if (!empty($client_kontak_input)) {
        $client_kontak = $client_kontak_input;
    }
    
    if (!empty($catatan_client_input)) {
        $client_catatan = $catatan_client_input;
    }
    
    // ===== LOKASI =====
    $lokasi_input = $this->request->getPost('lokasi');
    if (!empty($lokasi_input)) {
        $lokasi = $lokasi_input;
    } else {
        $lokasi = $client_alamat;
    }
    
  // ===== TIM TEKNISI =====
$tim_teknisi = $this->request->getPost('tim_teknisi');

log_message('debug', 'Raw tim_teknisi: ' . json_encode($tim_teknisi));
log_message('debug', 'Raw tim_teknisi type: ' . gettype($tim_teknisi));

if (is_array($tim_teknisi) && !empty($tim_teknisi)) {
    // Filter hanya nilai yang valid (numeric dan tidak kosong)
    $tim_teknisi_filtered = array_filter($tim_teknisi, function($value) {
        return !empty($value) && is_numeric($value);
    });
    
    // Re-index array
    $tim_teknisi_filtered = array_values($tim_teknisi_filtered);
    $tim_teknisi_json = json_encode($tim_teknisi_filtered);
    
    log_message('debug', 'Filtered tim_teknisi: ' . json_encode($tim_teknisi_filtered));
} else {
    // Jika tidak ada teknisi dipilih atau bukan array, simpan sebagai array kosong
    $tim_teknisi_json = json_encode([]);
    log_message('debug', 'tim_teknisi kosong atau bukan array, menggunakan array kosong');
}

log_message('debug', 'Final tim_teknisi JSON: ' . $tim_teknisi_json);
    
    // ===== ESTIMASI BIAYA =====
    $estimasi_biaya = $this->request->getPost('estimasi_biaya') ?: 0;
    
    // ===== TANGGAL =====
    $target_selesai = $this->request->getPost('target_selesai');
    $tanggal_selesai = $target_selesai; // Samakan dengan target selesai
    $tanggal_selesai_aktual = $this->request->getPost('tanggal_selesai_aktual');
    
    log_message('debug', 'Target selesai: ' . ($target_selesai ?: 'kosong'));
    log_message('debug', 'Tanggal selesai aktual dari form: ' . ($tanggal_selesai_aktual ?: 'kosong'));
    
    // ===== LAPORAN =====
    $laporan = $this->request->getPost('laporan');
    log_message('debug', 'Laporan dari form: ' . ($laporan ?: 'kosong'));
    
    // ===== AMBIL DATA SPK LAMA UNTUK FILE =====
    $spk_lama = $this->spkModel->find($id);
    
    // ===== DOKUMEN PENDUKUNG =====
    $file = $this->request->getFile('dokumen_pendukung');
    if ($file && $file->isValid() && !$file->hasMoved()) {
        $validationRule = [
            'dokumen_pendukung' => [
                'uploaded[dokumen_pendukung]',
                'mime_in[dokumen_pendukung,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/jpg,image/jpeg,image/png]',
                'max_size[dokumen_pendukung,5120]',
            ],
        ];
        
        if ($this->validate($validationRule)) {
            // Hapus dokumen lama jika ada
            if ($spk_lama && $spk_lama->dokumen_pendukung && file_exists($spk_lama->dokumen_pendukung)) {
                unlink($spk_lama->dokumen_pendukung);
            }
            
            $newName = $file->getRandomName();
            $file->move('uploads/spk/dokumen', $newName);
            $dokumen_pendukung = 'uploads/spk/dokumen/' . $newName;
        } else {
            return redirect()->back()->withInput()->with('error', 'File dokumen tidak valid. Maks 5MB (PDF/DOC/JPG/PNG)');
        }
    } else {
        // Jika tidak upload file baru, gunakan file lama
        if ($spk_lama && $spk_lama->dokumen_pendukung) {
            $dokumen_pendukung = $spk_lama->dokumen_pendukung;
        }
    }
    
    // ===== DOKUMENTASI =====
    $file_dokumentasi = $this->request->getFile('dokumentasi');
    if ($file_dokumentasi && $file_dokumentasi->isValid() && !$file_dokumentasi->hasMoved()) {
        $validationRule = [
            'dokumentasi' => [
                'uploaded[dokumentasi]',
                'mime_in[dokumentasi,image/jpg,image/jpeg,image/png]',
                'max_size[dokumentasi,5120]',
            ],
        ];
        
        if ($this->validate($validationRule)) {
            // Hapus dokumentasi lama jika ada
            if ($spk_lama && $spk_lama->dokumentasi && file_exists($spk_lama->dokumentasi)) {
                unlink($spk_lama->dokumentasi);
            }
            
            $newName = $file_dokumentasi->getRandomName();
            $file_dokumentasi->move('uploads/spk/dokumentasi', $newName);
            $dokumentasi = 'uploads/spk/dokumentasi/' . $newName;
        } else {
            return redirect()->back()->withInput()->with('error', 'File dokumentasi tidak valid. Maks 5MB (JPG/PNG)');
        }
    } else {
        // Jika tidak upload file baru, gunakan file lama
        if ($spk_lama && $spk_lama->dokumentasi) {
            $dokumentasi = $spk_lama->dokumentasi;
        }
    }
    
    // ===== DATA UNTUK DISIMPAN =====
    $data = [
        'nomor_spk' => $this->request->getPost('nomor_spk'),
        'judul_pekerjaan' => $this->request->getPost('judul_pekerjaan'),
        'deskripsi' => $this->request->getPost('deskripsi'),
        'lokasi' => $lokasi,
        'client_id' => $client_id,
        // Field-field client
        'client_nama' => $client_nama,
        'client_alamat' => $client_alamat,
        'client_kontak' => $client_kontak,
        'catatan_client' => $client_catatan,
        'tanggal_mulai' => $this->request->getPost('tanggal_mulai'),
        'tanggal_selesai' => $tanggal_selesai,
        'target_selesai' => $target_selesai,
        'tanggal_selesai_aktual' => $tanggal_selesai_aktual,
        'prioritas' => $this->request->getPost('prioritas'),
        'status' => $this->request->getPost('status') ?? 'Draft',
        'kategori_pekerjaan' => $this->request->getPost('kategori_pekerjaan'),
        'tim_teknisi' => $tim_teknisi_json,
        'project_manager_id' => $this->request->getPost('project_manager_id') ?: null,
        'catatan' => $this->request->getPost('catatan'),
        'laporan' => $laporan,
        'estimasi_biaya' => $estimasi_biaya,
        'progress_persen' => $this->request->getPost('progress_persen') ?? 0,
        'dokumen_pendukung' => $dokumen_pendukung,
        'dokumentasi' => $dokumentasi,
        'diperbarui_oleh' => session()->get('user_id'),
        'diperbarui_tanggal' => date('Y-m-d H:i:s')
    ];
    
    log_message('debug', 'Final data to update: ' . json_encode($data));
    
    // ===== ITEM PEKERJAAN =====
    $items = [];
    $nama_items = $this->request->getPost('item_nama') ?: [];
    $deskripsi_items = $this->request->getPost('item_deskripsi') ?: [];
    $qty_items = $this->request->getPost('item_qty') ?: [];
    $satuan_items = $this->request->getPost('item_satuan') ?: [];
    $harga_items = $this->request->getPost('item_harga') ?: [];
    $keterangan_items = $this->request->getPost('item_keterangan') ?: [];
    $status_items = $this->request->getPost('item_status') ?: [];
    
    log_message('debug', 'Status items from form: ' . json_encode($status_items));
    
    foreach ($nama_items as $key => $nama) {
        if (!empty(trim($nama))) {
            // Bersihkan format harga
            $harga = 0;
            if (isset($harga_items[$key]) && !empty($harga_items[$key])) {
                $harga = str_replace('.', '', $harga_items[$key]);
                $harga = str_replace(',', '.', $harga);
                $harga = (float) $harga;
            }
            
            // Bersihkan format qty
            $qty = isset($qty_items[$key]) ? (float) str_replace(',', '.', $qty_items[$key]) : 1;
            
            // Ambil status item
            $status = 'Pending';
            if (isset($status_items[$key]) && !empty($status_items[$key])) {
                $status = $status_items[$key];
            }
            
            log_message('debug', "Item $key: nama=$nama, status=$status");
            
            $items[] = [
                'nama_item' => $nama,
                'deskripsi' => $deskripsi_items[$key] ?? '',
                'qty' => $qty,
                'satuan' => $satuan_items[$key] ?? 'unit',
                'harga' => $harga,
                'keterangan' => $keterangan_items[$key] ?? '',
                'status' => $status
            ];
        }
    }
    
    log_message('debug', 'Final items to save: ' . json_encode($items));
    
    // ===== TRANSAKSI DATABASE =====
    $this->db->transStart();
    
    try {
        // Update SPK
        if (!$this->spkModel->update($id, $data)) {
            $errors = $this->spkModel->errors();
            log_message('error', 'SPK Model Errors: ' . json_encode($errors));
            throw new \Exception('Gagal memperbarui SPK: ' . json_encode($errors));
        }
        
        // Simpan items (method saveItems akan menghapus yang lama dan insert baru)
        $saveItems = $this->spkItemModel->saveItems($id, $items);
        if (!$saveItems) {
            throw new \Exception('Gagal menyimpan item pekerjaan');
        }
        
        // Catat log aktivitas
        $this->logAktivitas($id, 'UPDATE', 'SPK diperbarui');
        
        $this->db->transCommit();
        
        return redirect()->to('teknisi/tugas-proyek/spk/detail/' . $id)
            ->with('success', 'SPK berhasil diperbarui');
            
    } catch (\Exception $e) {
        $this->db->transRollback();
        
        log_message('error', '[SpkInstalasi::update] Exception: ' . $e->getMessage());
        
        return redirect()->back()
            ->withInput()
            ->with('error', 'Gagal memperbarui SPK: ' . $e->getMessage());
    }
}

    /**
     * Hapus SPK
     */
    public function delete($id)
    {
        try {
            // Ambil data SPK untuk hapus file
            $spk = $this->spkModel->find($id);
            
            if (!$spk) {
                return redirect()->back()->with('error', 'SPK tidak ditemukan');
            }
            
            // Hapus file-file jika ada
            if ($spk->dokumen_pendukung && file_exists($spk->dokumen_pendukung)) {
                unlink($spk->dokumen_pendukung);
            }
            if ($spk->dokumentasi && file_exists($spk->dokumentasi)) {
                unlink($spk->dokumentasi);
            }
            
            // Catat log sebelum delete
            $this->logAktivitas($id, 'DELETE', 'SPK dihapus');
            
            // Hapus data (soft delete)
            if ($this->spkModel->delete($id)) {
                return redirect()->to('teknisi/tugas-proyek/spk')->with('success', 'SPK berhasil dihapus');
            } else {
                return redirect()->back()->with('error', 'Gagal menghapus SPK');
            }
            
        } catch (\Exception $e) {
            log_message('error', '[SpkInstalasi::delete] Exception: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus SPK: ' . $e->getMessage());
        }
    }

    /**
     * Update progress SPK (AJAX)
     */
    public function updateProgress()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }
        
        $id = $this->request->getPost('id');
        $progress = $this->request->getPost('progress');
        
        try {
            if ($this->spkModel->updateProgress($id, $progress, session()->get('user_id'))) {
                // Catat log
                $this->logAktivitas($id, 'UPDATE_PROGRESS', 'Progress diperbarui menjadi ' . $progress . '%');
                
                return $this->response->setJSON([
                    'success' => true, 
                    'message' => 'Progress berhasil diperbarui'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => 'Gagal memperbarui progress'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', '[SpkInstalasi::updateProgress] Exception: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Selesaikan SPK
     */
    public function selesaikan($id)
    {
        $laporan = $this->request->getPost('laporan_hasil');
        
        try {
            if ($this->spkModel->selesaikan($id, session()->get('user_id'), $laporan)) {
                // Catat log
                $this->logAktivitas($id, 'COMPLETE', 'SPK diselesaikan' . ($laporan ? ' dengan laporan' : ''));
                
                return redirect()->back()->with('success', 'SPK berhasil diselesaikan');
            } else {
                return redirect()->back()->with('error', 'Gagal menyelesaikan SPK');
            }
        } catch (\Exception $e) {
            log_message('error', '[SpkInstalasi::selesaikan] Exception: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyelesaikan SPK: ' . $e->getMessage());
        }
    }

    /**
     * Batalkan SPK
     */
    public function batalkan($id)
    {
        $alasan = $this->request->getPost('alasan');
        
        try {
            if ($this->spkModel->batalkan($id, session()->get('user_id'), $alasan)) {
                // Catat log
                $this->logAktivitas($id, 'CANCEL', 'SPK dibatalkan' . ($alasan ? ': ' . $alasan : ''));
                
                return redirect()->back()->with('success', 'SPK berhasil dibatalkan');
            } else {
                return redirect()->back()->with('error', 'Gagal membatalkan SPK');
            }
        } catch (\Exception $e) {
            log_message('error', '[SpkInstalasi::batalkan] Exception: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membatalkan SPK: ' . $e->getMessage());
        }
    }

    /**
     * Tunda SPK
     */
    public function tunda($id)
    {
        $alasan = $this->request->getPost('alasan');
        
        try {
            if ($this->spkModel->tunda($id, session()->get('user_id'), $alasan)) {
                // Catat log
                $this->logAktivitas($id, 'DELAY', 'SPK ditunda' . ($alasan ? ': ' . $alasan : ''));
                
                return redirect()->back()->with('success', 'SPK berhasil ditunda');
            } else {
                return redirect()->back()->with('error', 'Gagal menunda SPK');
            }
        } catch (\Exception $e) {
            log_message('error', '[SpkInstalasi::tunda] Exception: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menunda SPK: ' . $e->getMessage());
        }
    }

    /**
     * Get client data by ID (AJAX)
     */
    public function getClient($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false]);
        }
        
        try {
            $client = $this->clientModel->find($id);
            
            if ($client) {
                // Format data client sesuai struktur tabel
                $data = [
                    'id' => $client->id,
                    'nama_perusahaan' => $client->nama_perusahaan,
                    'alamat' => $client->alamat,
                    'telepon' => $client->telepon,
                    'email' => $client->email_client,
                    'kontak_nama' => $client->nama_kontak,
                    'kontak_telepon' => $client->telepon,
                    'client_kontak' => $client->client_kontak,
                    'catatan' => $client->catatan_client,
                    'kategori' => $client->kategori,
                    'kode_client' => $client->kode_client
                ];
                
                return $this->response->setJSON([
                    'success' => true,
                    'data' => $data
                ]);
            }
            
            return $this->response->setJSON(['success' => false, 'message' => 'Client tidak ditemukan']);
            
        } catch (\Exception $e) {
            log_message('error', '[SpkInstalasi::getClient] Exception: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get items by SPK ID (AJAX)
     */
    public function getItems($spk_id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false]);
        }
        
        try {
            $items = $this->spkItemModel->getBySpkId($spk_id);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $items
            ]);
            
        } catch (\Exception $e) {
            log_message('error', '[SpkInstalasi::getItems] Exception: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update status item (AJAX)
     */
    public function updateItemStatus()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false]);
        }
        
        $item_id = $this->request->getPost('item_id');
        $status = $this->request->getPost('status');
        
        try {
            if ($this->spkItemModel->updateStatus($item_id, $status)) {
                // Dapatkan spk_id dari item
                $item = $this->spkItemModel->find($item_id);
                if ($item) {
                    // Update progress SPK berdasarkan item
                    $progress = $this->spkItemModel->getProgressBySpkId($item->spk_id);
                    $this->spkModel->updateProgress($item->spk_id, $progress, session()->get('user_id'));
                    
                    // Catat log
                    $this->logAktivitas($item->spk_id, 'UPDATE_ITEM', 'Status item diperbarui menjadi ' . $status);
                }
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Status item berhasil diperbarui'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal memperbarui status item'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', '[SpkInstalasi::updateItemStatus] Exception: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Sync data client untuk semua SPK (utility method)
     * Method ini bisa dijalankan untuk memperbaiki data yang sudah ada
     */
    public function syncAllClientData()
    {
        // Hanya untuk admin/developer
        if (session()->get('role') !== 'admin') {
            return redirect()->back()->with('error', 'Unauthorized');
        }
        
        try {
            $spkList = $this->spkModel->findAll();
            $updated = 0;
            
            foreach ($spkList as $spk) {
                if ($spk->client_id) {
                    $result = $this->spkModel->syncClientData($spk->id, $spk->client_id);
                    if ($result) {
                        $updated++;
                    }
                }
            }
            
            return redirect()->back()->with('success', "Berhasil sinkronisasi data client untuk $updated SPK");
            
        } catch (\Exception $e) {
            log_message('error', '[SpkInstalasi::syncAllClientData] Exception: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal sinkronisasi: ' . $e->getMessage());
        }
    }

    /**
     * Catat log aktivitas SPK
     */
    private function logAktivitas($spk_id, $aktivitas, $keterangan = null)
    {
        try {
            $data = [
                'spk_id' => $spk_id,
                'user_id' => session()->get('user_id'),
                'aktivitas' => $aktivitas,
                'keterangan' => $keterangan,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            return $this->db->table('spk_instalasi_log')->insert($data);
            
        } catch (\Exception $e) {
            log_message('error', '[SpkInstalasi::logAktivitas] Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Membersihkan format rupiah menjadi angka
     */
    private function cleanRupiah($value)
    {
        if (empty($value)) {
            return 0;
        }
        
        // Hapus 'Rp' jika ada
        $value = str_replace('Rp', '', $value);
        $value = str_replace('rp', '', $value);
        
        // Hapus spasi
        $value = str_replace(' ', '', $value);
        
        // Hapus titik (ribuan) dan ganti koma dengan titik (desimal)
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);
        
        return (float) $value;
    }

    /**
     * Format angka ke rupiah
     */
    private function formatRupiah($angka)
    {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }

    /**
     * Export SPK ke PDF
     */
    public function exportPdf($id)
    {
        // Ambil data SPK dengan relasi client
        $spk = $this->spkModel->getWithRelations($id);
        
        if (!$spk) {
            return redirect()->back()->with('error', 'SPK tidak ditemukan');
        }
        
        // Ambil items
        $items = $this->spkItemModel->getBySpkId($id);
        
        // TODO: Implement PDF generation
        
        return redirect()->back()->with('info', 'Fitur export PDF dalam pengembangan');
    }

    /**
     * Export SPK ke Excel
     */
    public function exportExcel($id)
    {
        return redirect()->back()->with('info', 'Fitur export Excel dalam pengembangan');
    }
}