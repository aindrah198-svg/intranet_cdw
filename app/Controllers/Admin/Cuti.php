<?php
// app/Controllers/Admin/Cuti.php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CutiModel;
use App\Models\KuotaCutiModel;
use App\Models\KaryawanModel;
use App\Models\UserModel;

class Cuti extends BaseController
{
    protected $cutiModel;
    protected $kuotaCutiModel;
    protected $karyawanModel;
    protected $userModel;

    public function __construct()
    {
        $this->cutiModel = new CutiModel();
        $this->kuotaCutiModel = new KuotaCutiModel();
        $this->karyawanModel = new KaryawanModel();
        $this->userModel = new UserModel();
        
        // Load helpers
        helper(['form', 'date', 'number', 'cuti', 'user']);
    }

    // ============================================
    // METHOD UTAMA
    // ============================================

    public function index()
    {
        $data = [
            'title' => 'Manajemen Cuti',
            'active' => 'cuti',
            'css' => [
                'https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
                'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css'
            ],
            'scripts' => [
                'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js',
                'https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'
            ],
            'breadcrumbs' => [
                ['name' => 'Dashboard', 'url' => base_url('admin')],
                ['name' => 'Cuti', 'url' => base_url('admin/cuti')]
            ]
        ];

        // Filter parameters
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
        $status = $this->request->getGet('status');
        $karyawanId = $this->request->getGet('karyawan_id');
        $jenisCuti = $this->request->getGet('jenis_cuti');

        // Build query
        $builder = $this->cutiModel->db->table('cuti c');
        $builder->select('c.*, k.nik, k.nama_lengkap, k.jabatan, k.departemen');
        $builder->join('karyawan k', 'k.id = c.karyawan_id', 'left');
        $builder->where('c.tanggal_mulai >=', $startDate);
        $builder->where('c.tanggal_mulai <=', $endDate);

        if ($status) {
            $builder->where('c.status', $status);
        }

        if ($karyawanId) {
            $builder->where('c.karyawan_id', $karyawanId);
        }

        if ($jenisCuti) {
            $builder->where('c.jenis_cuti', $jenisCuti);
        }

        $data['cuti'] = $builder->orderBy('c.created_at', 'DESC')->get()->getResultArray();
        $data['karyawan'] = $this->karyawanModel->where('deleted_at', null)->findAll();
        
        // Statistics
        $data['stats'] = $this->cutiModel->getStatistics($startDate, $endDate);
        $data['filter'] = compact('startDate', 'endDate', 'status', 'karyawanId', 'jenisCuti');

        return view('admin/manajemen_cuti/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Ajukan Cuti',
            'active' => 'cuti',
            'karyawan' => $this->karyawanModel->where('deleted_at', null)->findAll(),
            'validation' => \Config\Services::validation(),
            'breadcrumbs' => [
                ['name' => 'Dashboard', 'url' => base_url('admin')],
                ['name' => 'Cuti', 'url' => base_url('admin/cuti')],
                ['name' => 'Ajukan', 'url' => base_url('admin/cuti/create')]
            ]
        ];

        return view('admin/manajemen_cuti/create', $data);
    }

    public function store()
    {
        $rules = [
            'karyawan_id' => 'required|numeric',
            'jenis_cuti' => 'required|in_list[Tahunan,Hamil,Sakit,Khusus,Lainnya]',
            'alasan' => 'required|min_length[10]',
            'tanggal_mulai' => 'required|valid_date',
            'tanggal_selesai' => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Calculate days (excluding weekends)
        $startDate = new \DateTime($this->request->getPost('tanggal_mulai'));
        $endDate = new \DateTime($this->request->getPost('tanggal_selesai'));
        $interval = $startDate->diff($endDate);
        $days = $interval->days + 1;

        // Exclude weekends
        $workDays = 0;
        for ($i = 0; $i < $days; $i++) {
            $currentDate = clone $startDate;
            $currentDate->add(new \DateInterval("P{$i}D"));
            $dayOfWeek = $currentDate->format('N');
            if ($dayOfWeek < 6) {
                $workDays++;
            }
        }

        // Check quota for tahunan cuti
        $jenisCuti = $this->request->getPost('jenis_cuti');
        $karyawanId = $this->request->getPost('karyawan_id');
        $year = date('Y');

        if ($jenisCuti === 'Tahunan') {
            $quota = $this->kuotaCutiModel->getQuotaByKaryawan($karyawanId, $year);
            if (!$quota || $quota->sisa < $workDays) {
                return redirect()->back()->withInput()->with('error', 'Kuota cuti tahunan tidak mencukupi! Sisa kuota: ' . ($quota->sisa ?? 0) . ' hari');
            }
        }

        // Check date conflict
        $conflict = $this->cutiModel->checkDateConflict(
            $karyawanId,
            $this->request->getPost('tanggal_mulai'),
            $this->request->getPost('tanggal_selesai')
        );

        if ($conflict) {
            return redirect()->back()->withInput()->with('error', 'Tanggal cuti bentrok dengan pengajuan cuti sebelumnya!');
        }

        // Generate nomor cuti
        $nomorCuti = $this->cutiModel->generateNomorCuti();

        // Prepare data
        $data = [
            'karyawan_id' => $karyawanId,
            'nomor_cuti' => $nomorCuti,
            'jenis_cuti' => $jenisCuti,
            'alasan' => $this->request->getPost('alasan'),
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai'),
            'lama_hari' => $workDays,
            'sisa_cuti_tahunan' => $jenisCuti === 'Tahunan' ? ($quota->sisa ?? 0) : 0,
            'status' => 'Menunggu',
            'tanggal_pengajuan' => date('Y-m-d H:i:s')
        ];

        // Insert data
        if ($this->cutiModel->save($data)) {
            return redirect()->to(base_url('admin/cuti'))->with('success', 'Pengajuan cuti berhasil dikirim!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal mengajukan cuti. Silakan coba lagi.');
        }
    }

    public function show($id)
    {
        $cuti = $this->cutiModel->getCutiWithKaryawan($id);

        if (!$cuti) {
            return redirect()->to(base_url('admin/cuti'))->with('error', 'Data cuti tidak ditemukan!');
        }

        $data = [
            'title' => 'Detail Pengajuan Cuti',
            'active' => 'cuti',
            'cuti' => $cuti,
            'breadcrumbs' => [
                ['name' => 'Dashboard', 'url' => base_url('admin')],
                ['name' => 'Cuti', 'url' => base_url('admin/cuti')],
                ['name' => 'Detail', 'url' => base_url('admin/cuti/show/' . $id)]
            ]
        ];

        return view('admin/manajemen_cuti/show', $data);
    }

    public function edit($id)
    {
        $cuti = $this->cutiModel->find($id);

        if (!$cuti) {
            return redirect()->to(base_url('admin/cuti'))->with('error', 'Data cuti tidak ditemukan!');
        }

        $data = [
            'title' => 'Edit Pengajuan Cuti',
            'active' => 'cuti',
            'cuti' => $cuti,
            'karyawan' => $this->karyawanModel->where('deleted_at', null)->findAll(),
            'validation' => \Config\Services::validation(),
            'breadcrumbs' => [
                ['name' => 'Dashboard', 'url' => base_url('admin')],
                ['name' => 'Cuti', 'url' => base_url('admin/cuti')],
                ['name' => 'Edit', 'url' => base_url('admin/cuti/edit/' . $id)]
            ]
        ];

        return view('admin/manajemen_cuti/edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'jenis_cuti' => 'required|in_list[Tahunan,Hamil,Sakit,Khusus,Lainnya]',
            'alasan' => 'required|min_length[10]',
            'tanggal_mulai' => 'required|valid_date',
            'tanggal_selesai' => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Get existing cuti
        $existingCuti = $this->cutiModel->find($id);
        if (!$existingCuti) {
            return redirect()->to(base_url('admin/cuti'))->with('error', 'Data cuti tidak ditemukan!');
        }

        // Check if cuti can be edited
        if (!in_array($existingCuti['status'], ['Draft', 'Menunggu'])) {
            return redirect()->back()->with('error', 'Cuti tidak dapat diedit karena status sudah ' . $existingCuti['status']);
        }

        // Calculate days
        $startDate = new \DateTime($this->request->getPost('tanggal_mulai'));
        $endDate = new \DateTime($this->request->getPost('tanggal_selesai'));
        $days = $startDate->diff($endDate)->days + 1;

        // Exclude weekends
        $workDays = 0;
        for ($i = 0; $i < $days; $i++) {
            $currentDate = clone $startDate;
            $currentDate->add(new \DateInterval("P{$i}D"));
            $dayOfWeek = $currentDate->format('N');
            if ($dayOfWeek < 6) {
                $workDays++;
            }
        }

        // Check date conflict (exclude current cuti)
        $conflict = $this->cutiModel->checkDateConflict(
            $existingCuti['karyawan_id'],
            $this->request->getPost('tanggal_mulai'),
            $this->request->getPost('tanggal_selesai'),
            $id
        );

        if ($conflict) {
            return redirect()->back()->withInput()->with('error', 'Tanggal cuti bentrok dengan pengajuan cuti lainnya!');
        }

        $data = [
            'jenis_cuti' => $this->request->getPost('jenis_cuti'),
            'alasan' => $this->request->getPost('alasan'),
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai'),
            'lama_hari' => $workDays,
            'status' => 'Menunggu'
        ];

        if ($this->cutiModel->update($id, $data)) {
            return redirect()->to(base_url('admin/cuti'))->with('success', 'Pengajuan cuti berhasil diperbarui!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui cuti. Silakan coba lagi.');
        }
    }

    public function delete($id)
    {
        $cuti = $this->cutiModel->find($id);

        if (!$cuti) {
            return redirect()->to(base_url('admin/cuti'))->with('error', 'Data cuti tidak ditemukan!');
        }

        if ($this->cutiModel->delete($id)) {
            return redirect()->to(base_url('admin/cuti'))->with('success', 'Pengajuan cuti berhasil dihapus!');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus cuti. Silakan coba lagi.');
        }
    }

    public function approve($id)
    {
        $userId = session()->get('id');
        $cuti = $this->cutiModel->find($id);

        if (!$cuti) {
            return redirect()->to(base_url('admin/cuti'))->with('error', 'Data cuti tidak ditemukan!');
        }

        // Determine approval level based on user role
        $userRole = session()->get('role');
        $newStatus = $userRole === 'hrd' ? 'Disetujui HRD' : 'Disetujui Atasan';

        // Update quota if it's tahunan cuti
        if ($cuti['jenis_cuti'] === 'Tahunan') {
            $year = date('Y', strtotime($cuti['tanggal_mulai']));
            $this->kuotaCutiModel->updateQuota($cuti['karyawan_id'], $year, $cuti['lama_hari']);
        }

        if ($this->cutiModel->updateStatus($id, $newStatus, $userId)) {
            return redirect()->to(base_url('admin/cuti'))->with('success', 'Pengajuan cuti berhasil disetujui!');
        } else {
            return redirect()->back()->with('error', 'Gagal menyetujui cuti. Silakan coba lagi.');
        }
    }

    public function reject($id)
    {
        $reason = $this->request->getPost('alasan_penolakan');

        if (!$reason) {
            return redirect()->back()->with('error', 'Alasan penolakan harus diisi!');
        }

        $userId = session()->get('id');

        if ($this->cutiModel->updateStatus($id, 'Ditolak', null, $reason)) {
            return redirect()->to(base_url('admin/cuti'))->with('success', 'Pengajuan cuti berhasil ditolak!');
        } else {
            return redirect()->back()->with('error', 'Gagal menolak cuti. Silakan coba lagi.');
        }
    }

    public function pending()
    {
        $data = [
            'title' => 'Cuti Menunggu Persetujuan',
            'active' => 'cuti',
            'cuti' => $this->cutiModel->getByStatus('Menunggu'),
            'breadcrumbs' => [
                ['name' => 'Dashboard', 'url' => base_url('admin')],
                ['name' => 'Cuti', 'url' => base_url('admin/cuti')],
                ['name' => 'Pending', 'url' => base_url('admin/cuti/pending')]
            ]
        ];

        return view('admin/manajemen_cuti/pending', $data);
    }

    public function approved()
    {
        $data = [
            'title' => 'Cuti Disetujui',
            'active' => 'cuti',
            'cuti' => $this->cutiModel->whereIn('status', ['Disetujui HRD', 'Disetujui Atasan'])->findAll(),
            'breadcrumbs' => [
                ['name' => 'Dashboard', 'url' => base_url('admin')],
                ['name' => 'Cuti', 'url' => base_url('admin/cuti')],
                ['name' => 'Disetujui', 'url' => base_url('admin/cuti/approved')]
            ]
        ];

        return view('admin/manajemen_cuti/approved', $data);
    }

    public function rejected()
    {
        $data = [
            'title' => 'Cuti Ditolak',
            'active' => 'cuti',
            'cuti' => $this->cutiModel->where('status', 'Ditolak')->findAll(),
            'breadcrumbs' => [
                ['name' => 'Dashboard', 'url' => base_url('admin')],
                ['name' => 'Cuti', 'url' => base_url('admin/cuti')],
                ['name' => 'Ditolak', 'url' => base_url('admin/cuti/rejected')]
            ]
        ];

        return view('admin/manajemen_cuti/rejected', $data);
    }

    public function report()
    {
        $data = [
            'title' => 'Laporan Cuti',
            'active' => 'cuti',
            'breadcrumbs' => [
                ['name' => 'Dashboard', 'url' => base_url('admin')],
                ['name' => 'Cuti', 'url' => base_url('admin/cuti')],
                ['name' => 'Laporan', 'url' => base_url('admin/cuti/report')]
            ]
        ];

        return view('admin/manajemen_cuti/report', $data);
    }

    // ============================================
    // MY CUTI - DENGAN AUTO-CONNECT OTOMATIS
    // ============================================

    /**
     * Menampilkan cuti untuk user yang sedang login
     * DENGAN AUTO-CONNECT OTOMATIS
     */
    public function myCuti()
    {
        // Get user ID from session
        $userId = session()->get('id');
        
        if (!$userId) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }
        
        // Get user data
        $user = $this->userModel->find($userId);
        
        if (!$user) {
            return redirect()->to('admin/dashboard')->with('error', 'User tidak ditemukan');
        }
        
        log_message('info', 'User accessing myCuti: ' . $user['id'] . ' - ' . $user['name']);
        
        // ==============================================
        // OTOMATIS HUBUNGKAN USER DENGAN KARYAWAN
        // ==============================================
        $karyawan = null;
        $autoConnected = false;
        
        // Jika user sudah punya karyawan_id
        if (!empty($user['karyawan_id'])) {
            $karyawan = $this->karyawanModel->find($user['karyawan_id']);
            log_message('info', 'User already has karyawan_id: ' . $user['karyawan_id']);
        } 
        // Jika belum, coba hubungkan otomatis
        else {
            log_message('info', 'User has no karyawan_id, attempting auto-connect');
            
            // Coba auto-connect menggunakan fungsi di controller
            $karyawan = $this->autoConnectUserToKaryawan($userId);
            $autoConnected = ($karyawan !== false);
            
            if ($autoConnected && is_array($karyawan)) {
                log_message('info', 'Auto-connect SUCCESS for user ' . $userId . ' to karyawan ' . $karyawan['id']);
                
                // Refresh user data
                $user = $this->userModel->find($userId);
            } else {
                log_message('info', 'Auto-connect FAILED for user ' . $userId);
                
                // Tampilkan form untuk pilih karyawan manual
                return $this->showKaryawanSelectionForm($user);
            }
        }
        
        // Jika tetap tidak ada karyawan (setelah auto-connect)
        if (!$karyawan) {
            log_message('error', 'Still no karyawan found for user ' . $userId);
            
            // Tampilkan semua karyawan untuk dipilih
            $allKaryawan = $this->karyawanModel->where('deleted_at', null)->findAll();
            
            $data = [
                'title' => 'Pilih Data Karyawan',
                'active' => 'cuti',
                'user' => $user,
                'karyawanList' => $allKaryawan,
                'message' => 'Akun Anda belum terhubung dengan data karyawan. Silakan pilih data karyawan Anda:'
            ];
            
            return view('admin/manajemen_cuti/select_karyawan', $data);
        }
        
        // ==============================================
        // AMBIL DATA CUTI
        // ==============================================
        $cuti = $this->cutiModel
            ->where('karyawan_id', $karyawan['id'])
            ->orderBy('created_at', 'DESC')
            ->findAll();
        
        log_message('info', 'Found ' . count($cuti) . ' cuti records for karyawan ' . $karyawan['id']);
        
        // Ambil kuota cuti
        $tahun = date('Y');
        $kuota = $this->kuotaCutiModel
            ->where('karyawan_id', $karyawan['id'])
            ->where('tahun', $tahun)
            ->first();
        
        // Hitung statistik
        $stats = $this->calculateMyCutiStats($cuti, $kuota);
        
        // Prepare data for view
        $data = [
            'title' => 'Cuti Saya',
            'active' => 'cuti',
            'user' => $user,
            'karyawan' => $karyawan,
            'cuti' => $cuti,
            'kuota' => $kuota,
            'stats' => $stats,
            'autoConnected' => $autoConnected,
            'breadcrumbs' => [
                ['name' => 'Dashboard', 'url' => base_url('admin')],
                ['name' => 'Cuti Saya', 'url' => base_url('admin/cuti/my-cuti')]
            ]
        ];
        
        return view('admin/manajemen_cuti/cuti_user', $data);
    }
    
    /**
     * Tampilkan form pemilihan karyawan manual
     */
    private function showKaryawanSelectionForm($user)
    {
        // Ambil semua karyawan aktif
        $karyawanList = $this->karyawanModel
            ->where('deleted_at', null)
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();
        
        $data = [
            'title' => 'Hubungkan dengan Karyawan',
            'active' => 'cuti',
            'user' => $user,
            'karyawanList' => $karyawanList,
            'message' => 'Pilih data karyawan Anda:'
        ];
        
        return view('admin/manajemen_cuti/select_karyawan', $data);
    }
    
    /**
     * Proses pemilihan karyawan manual
     */
    public function connectKaryawan()
    {
        $userId = session()->get('id');
        $karyawanId = $this->request->getPost('karyawan_id');
        
        if (!$userId || !$karyawanId) {
            return redirect()->back()->with('error', 'Data tidak lengkap');
        }
        
        // Update user dengan karyawan_id
        $this->userModel->update($userId, ['karyawan_id' => $karyawanId]);
        
        log_message('info', 'Manual connection: User ' . $userId . ' connected to karyawan ' . $karyawanId);
        
        return redirect()->to('admin/cuti/my-cuti')->with('success', 'Akun berhasil dihubungkan dengan data karyawan');
    }
    
    /**
     * Otomatis hubungkan user dengan karyawan (fungsi internal)
     */
    private function autoConnectUserToKaryawan($userId)
    {
        $user = $this->userModel->find($userId);
        
        if (!$user) {
            return false;
        }
        
        // Jika sudah punya karyawan_id, return karyawan data
        if (!empty($user['karyawan_id'])) {
            return $this->karyawanModel->find($user['karyawan_id']);
        }
        
        log_message('info', 'Trying to auto-connect user ' . $user['id'] . ' (' . $user['email'] . ') to karyawan');
        
        $karyawan = null;
        
        // Strategy 1: Cari berdasarkan email yang sama persis
        if (!empty($user['email'])) {
            $karyawan = $this->karyawanModel->where('email', $user['email'])->first();
            if ($karyawan) {
                log_message('info', 'Found karyawan by exact email: ' . $user['email']);
            }
        }
        
        // Strategy 2: Cari berdasarkan nama yang mirip
        if (!$karyawan && !empty($user['name'])) {
            // Hapus gelar dan spasi berlebih
            $cleanName = preg_replace('/\s+/', ' ', trim($user['name']));
            
            // Coba beberapa variasi pencarian
            $karyawan = $this->karyawanModel->like('nama_lengkap', $cleanName)->first();
            
            if (!$karyawan) {
                // Coba cari bagian dari nama
                $nameParts = explode(' ', $cleanName);
                if (count($nameParts) > 1) {
                    foreach ($nameParts as $part) {
                        if (strlen($part) > 2) {
                            $karyawan = $this->karyawanModel->like('nama_lengkap', $part)->first();
                            if ($karyawan) break;
                        }
                    }
                }
            }
            
            if ($karyawan) {
                log_message('info', 'Found karyawan by name similarity: ' . $user['name']);
            }
        }
        
        // Strategy 3: Cari berdasarkan username
        if (!$karyawan && !empty($user['username'])) {
            $karyawan = $this->karyawanModel->like('nik', $user['username'])->first();
            
            if (!$karyawan) {
                $karyawan = $this->karyawanModel->like('nama_lengkap', $user['username'])->first();
            }
            
            if ($karyawan) {
                log_message('info', 'Found karyawan by username: ' . $user['username']);
            }
        }
        
        // Jika ditemukan karyawan, update user
        if ($karyawan) {
            $updateData = ['karyawan_id' => $karyawan['id']];
            
            // Update juga email user jika kosong tapi karyawan punya email
            if (empty($user['email']) && !empty($karyawan['email'])) {
                $updateData['email'] = $karyawan['email'];
            }
            
            // Update user
            $this->userModel->update($userId, $updateData);
            
            log_message('info', 'Auto-connected user ' . $userId . ' to karyawan ' . $karyawan['id'] . ' (' . $karyawan['nama_lengkap'] . ')');
            
            return $karyawan;
        }
        
        log_message('info', 'Could not auto-connect user ' . $userId . ' to any karyawan');
        return false;
    }
    
    /**
     * Calculate statistics for my leave
     */
    private function calculateMyCutiStats($cuti, $kuota)
    {
        $approvedCount = 0;
        $pendingCount = 0;
        $rejectedCount = 0;
        $totalDays = 0;
        $upcoming = [];
        $currentDate = new \DateTime();
        
        foreach ($cuti as $c) {
            if (in_array($c['status'], ['Disetujui HRD', 'Disetujui Atasan'])) {
                $approvedCount++;
                $totalDays += $c['lama_hari'];
                
                // Check if upcoming
                if (!empty($c['tanggal_mulai'])) {
                    $startDate = new \DateTime($c['tanggal_mulai']);
                    if ($startDate > $currentDate) {
                        $upcoming[] = $c;
                    }
                }
            } elseif ($c['status'] === 'Menunggu') {
                $pendingCount++;
            } elseif ($c['status'] === 'Ditolak') {
                $rejectedCount++;
            }
        }
        
        $quota = $kuota ? $kuota['kuota_tahunan'] : 12;
        $remaining = max(0, $quota - $totalDays);
        $percentage = $quota > 0 ? ($totalDays / $quota) * 100 : 0;
        $circumference = 2 * 3.1416 * 54;
        $progress = $circumference * $percentage / 100;
        
        return [
            'approvedCount' => $approvedCount,
            'pendingCount' => $pendingCount,
            'rejectedCount' => $rejectedCount,
            'totalDays' => $totalDays,
            'quota' => $quota,
            'remaining' => $remaining,
            'percentage' => $percentage,
            'circumference' => $circumference,
            'progress' => $progress,
            'upcoming' => $upcoming,
            'totalCount' => count($cuti)
        ];
    }

    // ============================================
    // METHOD LAINNYA
    // ============================================

    public function checkQuota()
    {
        $karyawanId = $this->request->getGet('karyawan_id');
        $year = date('Y');

        if (!$karyawanId) {
            return $this->response->setJSON(['error' => 'Karyawan ID required']);
        }

        $quota = $this->kuotaCutiModel->getQuotaByKaryawan($karyawanId, $year);

        return $this->response->setJSON([
            'quota' => $quota ? $quota->kuota_tahunan : 12,
            'terpakai' => $quota ? $quota->terpakai : 0,
            'sisa' => $quota ? $quota->sisa : 12
        ]);
    }

    public function calculateDays()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        if (!$startDate || !$endDate) {
            return $this->response->setJSON(['error' => 'Tanggal required']);
        }

        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $interval = $start->diff($end);
        $totalDays = $interval->days + 1;

        // Calculate work days (exclude weekends)
        $workDays = 0;
        for ($i = 0; $i < $totalDays; $i++) {
            $currentDate = clone $start;
            $currentDate->add(new \DateInterval("P{$i}D"));
            $dayOfWeek = $currentDate->format('N');
            if ($dayOfWeek < 6) {
                $workDays++;
            }
        }

        return $this->response->setJSON([
            'total_days' => $totalDays,
            'work_days' => $workDays,
            'weekend_days' => $totalDays - $workDays
        ]);
    }

    public function exportExcel()
    {
        // Get filter parameters
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
        $karyawanId = $this->request->getGet('karyawan_id');
        $status = $this->request->getGet('status');
        $jenisCuti = $this->request->getGet('jenis_cuti');
        
        // Get data
        $builder = $this->cutiModel->db->table('cuti c');
        $builder->select('c.*, k.nik, k.nama_lengkap, k.jabatan, k.departemen, u.name as disetujui_nama');
        $builder->join('karyawan k', 'k.id = c.karyawan_id', 'left');
        $builder->join('users u', 'u.id = c.disetujui_oleh', 'left');
        $builder->where('c.tanggal_mulai >=', $startDate);
        $builder->where('c.tanggal_mulai <=', $endDate);
        
        if ($karyawanId) {
            $builder->where('c.karyawan_id', $karyawanId);
        }
        if ($status) {
            $builder->where('c.status', $status);
        }
        if ($jenisCuti) {
            $builder->where('c.jenis_cuti', $jenisCuti);
        }
        
        $cuti = $builder->orderBy('c.created_at', 'DESC')->get()->getResultArray();
        
        // Get statistics
        $stats = $this->cutiModel->getStatistics($startDate, $endDate);
        
        $data = [
            'cuti' => $cuti,
            'filter' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'karyawan_id' => $karyawanId,
                'status' => $status,
                'jenis_cuti' => $jenisCuti
            ],
            'stats' => $stats
        ];
        
        // Set headers
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="laporan_cuti_' . date('Ymd_His') . '.xls"');
        header('Cache-Control: max-age=0');
        
        // Return view
        return view('admin/manajemen_cuti/export_excel', $data);
    }

    public function calendarEvents()
    {
        // Get filter parameters
        $karyawanId = $this->request->getGet('karyawan_id');
        $jenisCuti = $this->request->getGet('jenis_cuti');
        $status = $this->request->getGet('status');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        // Build query
        $builder = $this->cutiModel->db->table('cuti c');
        $builder->select('c.*, k.nik, k.nama_lengkap, k.jabatan, k.departemen');
        $builder->join('karyawan k', 'k.id = c.karyawan_id', 'left');
        $builder->where('YEAR(c.tanggal_mulai)', $tahun);
        
        // Apply filters
        if ($karyawanId) {
            $builder->where('c.karyawan_id', $karyawanId);
        }
        
        if ($jenisCuti) {
            $builder->where('c.jenis_cuti', $jenisCuti);
        }
        
        if ($status) {
            $statusArray = explode(',', $status);
            $builder->whereIn('c.status', $statusArray);
        }
        
        $cuti = $builder->orderBy('c.tanggal_mulai', 'ASC')->get()->getResultArray();
        
        // Transform data for FullCalendar
        $events = [];
        foreach ($cuti as $item) {
            $color = $this->getEventColor($item['jenis_cuti'], $item['status']);
            
            $events[] = [
                'id' => $item['id'],
                'title' => $item['nama_lengkap'] . ' - ' . $item['jenis_cuti'],
                'start' => $item['tanggal_mulai'],
                'end' => date('Y-m-d', strtotime($item['tanggal_selesai'] . ' +1 day')),
                'color' => $color,
                'karyawan_id' => $item['karyawan_id'],
                'nama_lengkap' => $item['nama_lengkap'],
                'nik' => $item['nik'],
                'jenis_cuti' => $item['jenis_cuti'],
                'status' => $item['status'],
                'lama_hari' => $item['lama_hari'],
                'alasan' => $item['alasan'],
                'departemen' => $item['departemen'] ?? '-',
                'tooltip' => $this->generateTooltip($item)
            ];
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $events,
            'message' => 'Data berhasil dimuat'
        ]);
    }

    private function getEventColor($jenisCuti, $status)
    {
        switch ($status) {
            case 'Ditolak':
                return '#dc3545';
            case 'Menunggu':
                return '#ffc107';
            case 'Draft':
                return '#6c757d';
        }
        
        switch ($jenisCuti) {
            case 'Tahunan':
                return '#3498db';
            case 'Hamil':
                return '#e74c3c';
            case 'Sakit':
                return '#f39c12';
            case 'Khusus':
                return '#9b59b6';
            case 'Lainnya':
                return '#95a5a6';
            default:
                return '#2ecc71';
        }
    }

    private function generateTooltip($item)
    {
        $tooltip = $item['nama_lengkap'] . ' (' . $item['nik'] . ')' . "\n";
        $tooltip .= 'Jenis: ' . $item['jenis_cuti'] . "\n";
        $tooltip .= 'Status: ' . $item['status'] . "\n";
        $tooltip .= 'Durasi: ' . $item['lama_hari'] . ' hari' . "\n";
        
        if (!empty($item['departemen'])) {
            $tooltip .= 'Departemen: ' . $item['departemen'];
        }
        
        return $tooltip;
    }

    public function calendar()
    {
        $data = [
            'title' => 'Kalendar Cuti',
            'active' => 'cuti',
            'css' => [
                'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
                'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css'
            ],
            'scripts' => [
                'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js',
                'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/id.min.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js',
                'https://npmcdn.com/flatpickr/dist/l10n/id.js'
            ],
            'breadcrumbs' => [
                ['name' => 'Dashboard', 'url' => base_url('admin')],
                ['name' => 'Cuti', 'url' => base_url('admin/cuti')],
                ['name' => 'Kalendar', 'url' => base_url('admin/cuti/calendar')]
            ],
            'karyawan' => $this->karyawanModel->where('deleted_at', null)->findAll()
        ];

        return view('admin/manajemen_cuti/calendar', $data);
    }

    /**
     * Alternatif: User Cuti
     */
    public function userCuti()
    {
        return $this->myCuti();
    }
}