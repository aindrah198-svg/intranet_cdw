<?php

namespace App\Controllers\Teknisi;

use App\Controllers\BaseController;
use App\Models\Teknisi\SpkInstalasiModel;
use App\Models\Teknisi\SpkInstalasiPengeluaranModel;

class TambahanBarang extends BaseController
{
    protected $spkModel;
    protected $pengeluaranModel;
    
public function __construct()
{
    $this->spkModel = new SpkInstalasiModel();
    $this->pengeluaranModel = new SpkInstalasiPengeluaranModel();
    
    // Cek login - PERBAIKAN
    if (!session()->get('isLoggedIn')) {
        return redirect()->to('/login');
    }
}

    /**
     * Generate nomor referensi otomatis (private)
     * Format: SPK-YYYYMMDD-001
     */
    private function generateNoRefInternal()
    {
        $tanggal = date('Ymd'); // Format: 20260302
        $prefix = "SPK-{$tanggal}-";
        
        // Cari nomor terakhir dengan prefix hari ini
        $lastNoRef = $this->pengeluaranModel
            ->select('no_ref')
            ->like('no_ref', $prefix, 'after')
            ->orderBy('no_ref', 'DESC')
            ->first();
        
        if ($lastNoRef && isset($lastNoRef->no_ref)) {
            // Ambil 3 digit terakhir
            $lastNumber = (int)substr($lastNoRef->no_ref, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            // Belum ada untuk hari ini, mulai dari 001
            $newNumber = '001';
        }
        
        return $prefix . $newNumber;
    }

   /**
 * Halaman daftar pengeluaran (tambahan barang/biaya)
 */
public function index()
{
    $data['title'] = 'Pengeluaran / Tambahan Barang';
    $data['subtitle'] = 'Kelola semua pengeluaran dan tambahan barang dari setiap proyek';
    
    // Ambil filter dari request
    $spk_id = $this->request->getGet('spk_id');
    $jenis = $this->request->getGet('jenis');
    $tanggal_awal = $this->request->getGet('tanggal_awal');
    $tanggal_akhir = $this->request->getGet('tanggal_akhir');
    
    // Simpan nilai filter untuk dikirim ke view
    $data['selected_spk_id'] = $spk_id;
    $data['selected_jenis'] = $jenis;
    $data['selected_tanggal_awal'] = $tanggal_awal;
    $data['selected_tanggal_akhir'] = $tanggal_akhir;
    
    // Query builder untuk list pengeluaran dengan filter
    $builder = $this->pengeluaranModel->select('spk_instalasi_pengeluaran.*, spk_instalasi.nomor_spk, spk_instalasi.judul_pekerjaan, users.name as created_by_nama')
        ->join('spk_instalasi', 'spk_instalasi.id = spk_instalasi_pengeluaran.spk_id', 'left')
        ->join('users', 'users.id = spk_instalasi_pengeluaran.created_by', 'left');
    
    if ($spk_id) {
        $builder->where('spk_instalasi_pengeluaran.spk_id', $spk_id);
    }
    
    if ($jenis && $jenis != 'semua') {
        $builder->where('spk_instalasi_pengeluaran.jenis', $jenis);
    }
    
    if ($tanggal_awal) {
        $builder->where('spk_instalasi_pengeluaran.tanggal >=', $tanggal_awal);
    }
    
    if ($tanggal_akhir) {
        $builder->where('spk_instalasi_pengeluaran.tanggal <=', $tanggal_akhir);
    }
    
    $data['pengeluaran_list'] = $builder->orderBy('spk_instalasi_pengeluaran.tanggal', 'DESC')
        ->orderBy('spk_instalasi_pengeluaran.created_at', 'DESC')
        ->findAll();
    
    // Data untuk filter dropdown - UBAH INI: HAPUS WHERE STATUS != 'Selesai'
    $data['spk_list'] = $this->spkModel->select('id, nomor_spk, judul_pekerjaan, status')
        // Hapus baris ini: ->where('status !=', 'Selesai')
        ->orderBy('tanggal_mulai', 'DESC')
        ->findAll();
    
    $data['jenis_options'] = [
        'semua' => 'Semua Jenis',
        'Bensin' => 'Bensin',
        'Tol' => 'Tol',
        'Makan' => 'Makan',
        'Akomodasi' => 'Akomodasi',
        'Material Tambahan' => 'Material Tambahan',
        'Lainnya' => 'Lainnya'
    ];
    
    // Statistik - Total Semua Pengeluaran
    $totalBuilder = $this->pengeluaranModel->selectSum('jumlah');
    if ($spk_id) {
        $totalBuilder->where('spk_id', $spk_id);
    }
    $data['total_pengeluaran'] = $totalBuilder->get()->getRow()->jumlah ?? 0;
    
    // Statistik - Pengeluaran Bulan Ini (sesuai filter tanggal)
    $bulanIniBuilder = $this->pengeluaranModel->selectSum('jumlah');
    
    // Jika ada filter tanggal_awal dan tanggal_akhir, gunakan filter tersebut
    if ($tanggal_awal && $tanggal_akhir) {
        $bulanIniBuilder->where('tanggal >=', $tanggal_awal)
                      ->where('tanggal <=', $tanggal_akhir);
    } 
    // Jika hanya ada filter tanggal_awal
    elseif ($tanggal_awal) {
        $bulanIniBuilder->where('tanggal >=', $tanggal_awal);
    } 
    // Jika hanya ada filter tanggal_akhir
    elseif ($tanggal_akhir) {
        $bulanIniBuilder->where('tanggal <=', $tanggal_akhir);
    } 
    // Jika tidak ada filter tanggal, gunakan bulan berjalan
    else {
        $bulanIniBuilder->where('MONTH(tanggal)', date('m'))
                      ->where('YEAR(tanggal)', date('Y'));
    }
    
    // Tambahkan filter SPK jika ada
    if ($spk_id) {
        $bulanIniBuilder->where('spk_id', $spk_id);
    }
    
    $data['total_pengeluaran_bulan_ini'] = $bulanIniBuilder->get()->getRow()->jumlah ?? 0;
    
    // Untuk menampilkan periode yang sedang ditampilkan
    if ($tanggal_awal && $tanggal_akhir) {
        $data['periode_display'] = date('d/m/Y', strtotime($tanggal_awal)) . ' - ' . date('d/m/Y', strtotime($tanggal_akhir));
    } elseif ($tanggal_awal) {
        $data['periode_display'] = 'Dari ' . date('d/m/Y', strtotime($tanggal_awal));
    } elseif ($tanggal_akhir) {
        $data['periode_display'] = 'Sampai ' . date('d/m/Y', strtotime($tanggal_akhir));
    } else {
        $data['periode_display'] = date('F Y');
    }
    
    return view('teknisi/tugas_proyek/tambahan_barang/index', $data);
}

  /**
 * Halaman tambah pengeluaran baru (tanpa parameter)
 */
public function create()
{
    $data['title'] = 'Tambah Pengeluaran';
    $data['subtitle'] = 'Catat pengeluaran baru untuk proyek';
    
    // Generate nomor referensi otomatis
    $data['no_ref_auto'] = $this->generateNoRefInternal();
    
    // Ambil data SPK untuk dropdown - UBAH INI: HAPUS WHERE STATUS
    $data['spk_list'] = $this->spkModel->select('id, nomor_spk, judul_pekerjaan, status')
        // Hapus baris ini: ->where('status !=', 'Selesai')
        ->orderBy('tanggal_mulai', 'DESC')
        ->findAll();
    
    $data['selected_spk_id'] = null;
    
    $data['jenis_options'] = [
        'Bensin' => 'Bensin',
        'Tol' => 'Tol',
        'Makan' => 'Makan',
        'Akomodasi' => 'Akomodasi',
        'Material Tambahan' => 'Material Tambahan',
        'Lainnya' => 'Lainnya'
    ];
    
    return view('teknisi/tugas_proyek/tambahan_barang/create', $data);
}

  /**
 * Halaman tambah pengeluaran dengan SPK tertentu
 */
public function createWithSpk($spk_id = null)
{
    $data['title'] = 'Tambah Pengeluaran';
    $data['subtitle'] = 'Catat pengeluaran baru untuk proyek';
    
    // Generate nomor referensi otomatis
    $data['no_ref_auto'] = $this->generateNoRefInternal();
    
    // Ambil data SPK untuk dropdown - UBAH INI: HAPUS WHERE STATUS
    $data['spk_list'] = $this->spkModel->select('id, nomor_spk, judul_pekerjaan, status')
        // Hapus baris ini: ->where('status !=', 'Selesai')
        ->orderBy('tanggal_mulai', 'DESC')
        ->findAll();
    
    // Jika ada spk_id dari parameter, set sebagai selected
    $data['selected_spk_id'] = $spk_id;
    
    // Ambil data SPK detail jika ada spk_id
    if ($spk_id) {
        $data['spk_detail'] = $this->spkModel->find($spk_id);
    }
    
    $data['jenis_options'] = [
        'Bensin' => 'Bensin',
        'Tol' => 'Tol',
        'Makan' => 'Makan',
        'Akomodasi' => 'Akomodasi',
        'Material Tambahan' => 'Material Tambahan',
        'Lainnya' => 'Lainnya'
    ];
    
    return view('teknisi/tugas_proyek/tambahan_barang/create', $data);
}

    /**
     * Generate nomor referensi baru via AJAX
     */
    public function ajaxGenerateNoRef()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false]);
        }
        
        $no_ref = $this->generateNoRefInternal();
        
        return $this->response->setJSON([
            'success' => true,
            'no_ref' => $no_ref
        ]);
    }

    /**
 * Simpan data pengeluaran baru
 */
public function store()
{
    // Ambil jumlah dari POST - gunakan jumlah_original jika ada, jika tidak gunakan jumlah
    $jumlah = $this->request->getPost('jumlah_original');
    
    // Jika jumlah_original tidak ada, coba bersihkan dari input jumlah
    if (empty($jumlah)) {
        $jumlah = $this->request->getPost('jumlah');
        // Hapus titik (pemisah ribuan) dan konversi ke numeric
        $jumlah = preg_replace('/[^0-9]/', '', $jumlah);
    }
    
    // Validasi input
    $rules = [
        'spk_id' => 'required|numeric',
        'no_ref' => 'required|is_unique[spk_instalasi_pengeluaran.no_ref]',
        'nama_pengeluaran' => 'required|min_length[3]|max_length[200]',
        'jenis' => 'required',
        'jumlah' => 'required|numeric|greater_than[0]',
        'tanggal' => 'required|valid_date'
    ];
    
    $messages = [
        'spk_id' => [
            'required' => 'Pilih proyek/SPK terlebih dahulu',
            'numeric' => 'ID proyek tidak valid'
        ],
        'no_ref' => [
            'required' => 'Nomor referensi harus diisi',
            'is_unique' => 'Nomor referensi sudah digunakan, gunakan nomor lain'
        ],
        'nama_pengeluaran' => [
            'required' => 'Nama pengeluaran harus diisi',
            'min_length' => 'Nama pengeluaran minimal 3 karakter'
        ],
        'jenis' => [
            'required' => 'Jenis pengeluaran harus dipilih'
        ],
        'jumlah' => [
            'required' => 'Jumlah biaya harus diisi',
            'numeric' => 'Jumlah biaya harus berupa angka',
            'greater_than' => 'Jumlah biaya harus lebih dari 0'
        ],
        'tanggal' => [
            'required' => 'Tanggal pengeluaran harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ]
    ];
    
    if (!$this->validate($rules, $messages)) {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }
    
    // Data untuk disimpan
    $data = [
        'spk_id' => $this->request->getPost('spk_id'),
        'no_ref' => $this->request->getPost('no_ref'),
        'nama_pengeluaran' => $this->request->getPost('nama_pengeluaran'),
        'jenis' => $this->request->getPost('jenis'),
        'deskripsi' => $this->request->getPost('deskripsi'),
        'jumlah' => $jumlah, // Sekarang sudah dalam format angka murni
        'tanggal' => $this->request->getPost('tanggal'),
        'created_by' => session()->get('user_id')
    ];
    
    // Upload foto nota jika ada
    $file = $this->request->getFile('foto_nota');
    if ($file && $file->isValid() && !$file->hasMoved()) {
        $rules = [
            'foto_nota' => [
                'uploaded[foto_nota]',
                'mime_in[foto_nota,image/jpg,image/jpeg,image/png,application/pdf]',
                'max_size[foto_nota,5120]' // 5MB
            ]
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Buat direktori jika belum ada
        $uploadPath = 'uploads/pengeluaran/' . date('Y/m');
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }
        
        $newName = $file->getRandomName();
        $file->move($uploadPath, $newName);
        $data['foto_nota'] = $uploadPath . '/' . $newName;
    }
    
    // Simpan ke database
    try {
        if ($this->pengeluaranModel->insert($data)) {
            // Update biaya aktual di SPK
            $this->updateBiayaAktualSpk($data['spk_id']);
            
            return redirect()->to('teknisi/tugas-proyek/tambahan-barang')
                ->with('success', 'Pengeluaran berhasil dicatat dengan No. Referensi: ' . $data['no_ref']);
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data pengeluaran');
        }
    } catch (\Exception $e) {
        return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
    }
}



    /**
     * Halaman detail pengeluaran
     */
    public function detail($id)
    {
        $data['title'] = 'Detail Pengeluaran';
        
        // Ambil data pengeluaran
        $data['pengeluaran'] = $this->pengeluaranModel->select('spk_instalasi_pengeluaran.*, spk_instalasi.nomor_spk, spk_instalasi.judul_pekerjaan, users.name as created_by_nama')
            ->join('spk_instalasi', 'spk_instalasi.id = spk_instalasi_pengeluaran.spk_id', 'left')
            ->join('users', 'users.id = spk_instalasi_pengeluaran.created_by', 'left')
            ->find($id);
        
        if (!$data['pengeluaran']) {
            return redirect()->to('teknisi/tugas-proyek/tambahan-barang')->with('error', 'Data pengeluaran tidak ditemukan');
        }
        
        return view('teknisi/tugas_proyek/tambahan_barang/detail', $data);
    }
/**
 * Export pengeluaran ke Excel berdasarkan SPK
 */
public function exportExcel($spk_id = null)
{
    // PERBAIKAN
    if (!session()->get('isLoggedIn')) {
        log_message('error', 'exportExcel gagal: user tidak login');
        return redirect()->to('/login');
    }

    log_message('debug', 'User logged_in: ' . session()->get('user_id'));

    // Jika tidak ada SPK ID, redirect ke halaman index dengan pesan error
    if (!$spk_id) {
        log_message('error', 'exportExcel gagal: SPK ID tidak ada');
        return redirect()->to('teknisi/tugas-proyek/tambahan-barang')
            ->with('error', 'Pilih SPK terlebih dahulu untuk ekspor data');
    }

    // Ambil data SPK
    $spk = $this->spkModel->find($spk_id);
    if (!$spk) {
        log_message('error', 'exportExcel gagal: SPK tidak ditemukan, ID: ' . $spk_id);
        return redirect()->to('teknisi/tugas-proyek/tambahan-barang')
            ->with('error', 'Data SPK tidak ditemukan');
    }

    log_message('debug', 'SPK ditemukan: ' . $spk->nomor_spk);

    // Ambil semua pengeluaran untuk SPK ini
    $pengeluaran = $this->pengeluaranModel->select('spk_instalasi_pengeluaran.*, users.name as created_by_nama')
        ->join('users', 'users.id = spk_instalasi_pengeluaran.created_by', 'left')
        ->where('spk_id', $spk_id)
        ->orderBy('tanggal', 'ASC')
        ->findAll();

    log_message('debug', 'Jumlah pengeluaran: ' . count($pengeluaran));

    // Hitung total pengeluaran
    $total_pengeluaran = $this->pengeluaranModel->getTotalBySpkId($spk_id);

    // Ambil uang akomodasi dari session atau gunakan default 40jt
    $uang_akomodasi = session()->get('uang_akomodasi_' . $spk_id) ?? 40000000;

    // Data untuk dikirim ke view export
    $data = [
        'spk' => $spk,
        'pengeluaran' => $pengeluaran,
        'total_pengeluaran' => $total_pengeluaran,
        'uang_akomodasi' => $uang_akomodasi,
        'judul_pekerjaan' => $spk->judul_pekerjaan ?? 'SPK #' . $spk_id,
        'spk_id' => $spk_id
    ];

    // Hitung sisa dan terpakai
    $data['total_terpakai'] = $total_pengeluaran;
   $data['sisa_akomodasi'] = $data['uang_akomodasi'] - $total_pengeluaran; // Boleh minus
    $data['terpakai_persen'] = $data['uang_akomodasi'] > 0 ? round(($total_pengeluaran / $data['uang_akomodasi']) * 100, 2) : 0;

    // Hapus session setelah diambil
    session()->remove('uang_akomodasi_' . $spk_id);

    // **PENTING: Hapus semua output buffer sebelumnya**
    ob_clean();
    
    // Set header untuk Excel
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=Laporan_Pengeluaran_SPK_" . date('Ymd_His') . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    
    log_message('debug', 'Header Excel sudah diset, akan me-load view');
    
    // Load view export
    return view('teknisi/tugas_proyek/tambahan_barang/export_excel', $data);
}

/**
 * Simpan uang akomodasi ke session via AJAX
 */
public function setUangAkomodasi()
{
    log_message('debug', 'setUangAkomodasi dipanggil');
    
    if (!$this->request->isAJAX()) {
        log_message('error', 'setUangAkomodasi: Bukan AJAX request');
        return $this->response->setJSON(['success' => false, 'message' => 'Bukan AJAX request']);
    }
    
    $spk_id = $this->request->getPost('spk_id');
    $uang_akomodasi = $this->request->getPost('uang_akomodasi');
    
    log_message('debug', 'Data diterima - spk_id: ' . $spk_id . ', uang_akomodasi: ' . $uang_akomodasi);
    
    if (!$spk_id || !$uang_akomodasi) {
        log_message('error', 'setUangAkomodasi: Data tidak lengkap');
        return $this->response->setJSON(['success' => false, 'message' => 'Data tidak lengkap']);
    }
    
    // Simpan ke session
    session()->set('uang_akomodasi_' . $spk_id, $uang_akomodasi);
    
    log_message('debug', 'Data tersimpan di session: uang_akomodasi_' . $spk_id . ' = ' . $uang_akomodasi);
    
    return $this->response->setJSON(['success' => true, 'message' => 'Data tersimpan']);
}
    /**
 * Halaman edit pengeluaran
 */
public function edit($id)
{
    $data['title'] = 'Edit Pengeluaran';
    
    // Ambil data pengeluaran
    $data['pengeluaran'] = $this->pengeluaranModel->find($id);
    
    if (!$data['pengeluaran']) {
        return redirect()->to('teknisi/tugas-proyek/tambahan-barang')->with('error', 'Data pengeluaran tidak ditemukan');
    }
    
    // Ambil data SPK untuk dropdown - UBAH INI: HAPUS WHERE STATUS
    $data['spk_list'] = $this->spkModel->select('id, nomor_spk, judul_pekerjaan, status')
        // Hapus baris ini: ->where('status !=', 'Selesai')
        ->orderBy('tanggal_mulai', 'DESC')
        ->findAll();
    
    $data['jenis_options'] = [
        'Bensin' => 'Bensin',
        'Tol' => 'Tol',
        'Makan' => 'Makan',
        'Akomodasi' => 'Akomodasi',
        'Material Tambahan' => 'Material Tambahan',
        'Lainnya' => 'Lainnya'
    ];
    
    return view('teknisi/tugas_proyek/tambahan_barang/edit', $data);
}

    /**
     * Update data pengeluaran
     */
    public function update($id)
    {
        // Cek data exist
        $pengeluaran = $this->pengeluaranModel->find($id);
        if (!$pengeluaran) {
            return redirect()->to('teknisi/tugas-proyek/tambahan-barang')->with('error', 'Data pengeluaran tidak ditemukan');
        }
        
        // Ambil jumlah dari POST (sudah dibersihkan oleh JavaScript di form)
        $jumlah = $this->request->getPost('jumlah');
        
        // Validasi input
        $rules = [
            'spk_id' => 'required|numeric',
            'no_ref' => "required|is_unique[spk_instalasi_pengeluaran.no_ref,id,{$id}]",
            'nama_pengeluaran' => 'required|min_length[3]|max_length[200]',
            'jenis' => 'required',
            'jumlah' => 'required|numeric|greater_than[0]',
            'tanggal' => 'required|valid_date'
        ];
        
        $messages = [
            'spk_id' => [
                'required' => 'Pilih proyek/SPK terlebih dahulu',
                'numeric' => 'ID proyek tidak valid'
            ],
            'no_ref' => [
                'required' => 'Nomor referensi harus diisi',
                'is_unique' => 'Nomor referensi sudah digunakan, gunakan nomor lain'
            ],
            'nama_pengeluaran' => [
                'required' => 'Nama pengeluaran harus diisi',
                'min_length' => 'Nama pengeluaran minimal 3 karakter'
            ],
            'jenis' => [
                'required' => 'Jenis pengeluaran harus dipilih'
            ],
            'jumlah' => [
                'required' => 'Jumlah biaya harus diisi',
                'numeric' => 'Jumlah biaya harus berupa angka',
                'greater_than' => 'Jumlah biaya harus lebih dari 0'
            ],
            'tanggal' => [
                'required' => 'Tanggal pengeluaran harus diisi',
                'valid_date' => 'Format tanggal tidak valid'
            ]
        ];
        
        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Data untuk diupdate
        $data = [
            'spk_id' => $this->request->getPost('spk_id'),
            'no_ref' => $this->request->getPost('no_ref'),
            'nama_pengeluaran' => $this->request->getPost('nama_pengeluaran'),
            'jenis' => $this->request->getPost('jenis'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'jumlah' => $jumlah, // Langsung pakai $jumlah karena sudah bersih dari JavaScript
            'tanggal' => $this->request->getPost('tanggal')
        ];
        
        // Upload foto nota baru jika ada
        $file = $this->request->getFile('foto_nota');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $rules = [
                'foto_nota' => [
                    'uploaded[foto_nota]',
                    'mime_in[foto_nota,image/jpg,image/jpeg,image/png,application/pdf]',
                    'max_size[foto_nota,5120]'
                ]
            ];
            
            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
            
            // Hapus file lama jika ada
            if ($pengeluaran->foto_nota && file_exists($pengeluaran->foto_nota)) {
                unlink($pengeluaran->foto_nota);
            }
            
            $uploadPath = 'uploads/pengeluaran/' . date('Y/m');
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            
            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            $data['foto_nota'] = $uploadPath . '/' . $newName;
        }
        
        // Cek apakah checkbox hapus foto dicentang
        if ($this->request->getPost('hapus_foto') == '1') {
            if ($pengeluaran->foto_nota && file_exists($pengeluaran->foto_nota)) {
                unlink($pengeluaran->foto_nota);
            }
            $data['foto_nota'] = null;
        }
        
        // Update database
        try {
            if ($this->pengeluaranModel->update($id, $data)) {
                // Update biaya aktual di SPK
                $this->updateBiayaAktualSpk($data['spk_id']);
                
                return redirect()->to('teknisi/tugas-proyek/tambahan-barang/detail/' . $id)
                    ->with('success', 'Data pengeluaran berhasil diperbarui');
            } else {
                return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data pengeluaran');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Hapus data pengeluaran
     */
    public function delete($id)
    {
        $pengeluaran = $this->pengeluaranModel->find($id);
        
        if (!$pengeluaran) {
            return redirect()->back()->with('error', 'Data pengeluaran tidak ditemukan');
        }
        
        try {
            // Hapus file foto jika ada
            if ($pengeluaran->foto_nota && file_exists($pengeluaran->foto_nota)) {
                unlink($pengeluaran->foto_nota);
            }
            
            $spk_id = $pengeluaran->spk_id;
            
            if ($this->pengeluaranModel->delete($id)) {
                // Update biaya aktual di SPK
                $this->updateBiayaAktualSpk($spk_id);
                
                return redirect()->to('teknisi/tugas-proyek/tambahan-barang')
                    ->with('success', 'Data pengeluaran berhasil dihapus');
            } else {
                return redirect()->back()->with('error', 'Gagal menghapus data pengeluaran');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Update biaya_aktual di tabel spk_instalasi
     */
    private function updateBiayaAktualSpk($spk_id)
    {
        $total_pengeluaran = $this->pengeluaranModel->getTotalBySpkId($spk_id);
        
        // Ambil estimasi biaya dari SPK
        $spk = $this->spkModel->find($spk_id);
        
        if ($spk) {
            $this->spkModel->update($spk_id, [
                'biaya_aktual' => $total_pengeluaran
            ]);
        }
    }

    /**
     * Get total pengeluaran per SPK (AJAX)
     */
    public function getTotalBySpk($spk_id)
    {
        if ($this->request->isAJAX()) {
            $total = $this->pengeluaranModel->getTotalBySpkId($spk_id);
            
            return $this->response->setJSON([
                'success' => true,
                'total' => $total,
                'total_formatted' => 'Rp ' . number_format($total, 0, ',', '.')
            ]);
        }
        
        return $this->response->setJSON(['success' => false]);
    }

    /**
     * Get pengeluaran by SPK ID (AJAX)
     */
    public function getBySpk($spk_id)
    {
        if ($this->request->isAJAX()) {
            $pengeluaran = $this->pengeluaranModel->getBySpkId($spk_id);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $pengeluaran
            ]);
        }
        
        return $this->response->setJSON(['success' => false]);
    }

    /**
     * Cek ketersediaan nomor referensi (AJAX)
     */
    public function cekNoRef()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false]);
        }
        
        $no_ref = $this->request->getGet('no_ref');
        $id = $this->request->getGet('id');
        
        if (empty($no_ref)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nomor referensi tidak boleh kosong'
            ]);
        }
        
        // Cek apakah nomor referensi sudah digunakan
        $builder = $this->pengeluaranModel->where('no_ref', $no_ref);
        
        // Jika ada ID (untuk edit), exclude ID tersebut
        if (!empty($id)) {
            $builder->where('id !=', $id);
        }
        
        $exists = $builder->first();
        
        return $this->response->setJSON([
            'success' => true,
            'available' => empty($exists),
            'message' => empty($exists) ? 'Nomor referensi tersedia' : 'Nomor referensi sudah digunakan'
        ]);
    }
}