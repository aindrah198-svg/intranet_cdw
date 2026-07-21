<?php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\CoaModel;
use App\Libraries\ExportExcel;

class Coa extends BaseController
{
    protected $coaModel;
    protected $validation;
    
    public function __construct()
    {
        $this->coaModel = new CoaModel();
        $this->validation = \Config\Services::validation();
    }
    
    /**
     * Display a listing of COA
     */
    public function index()
    {
        // Check if this is an AJAX request for tree data
        if ($this->request->isAJAX() && $this->request->getGet('ajax') == 'tree') {
            return $this->ajaxGetTreeData();
        }
        
        $userModel = model('UserModel');
        $karyawanModel = model('KaryawanModel');
        
        // Get current user data
        $userId = session()->get('user_id');
        $user = $userModel->find($userId);
        
        // Get karyawan data if exists
        $karyawan = [];
        if ($user && $user['karyawan_id']) {
            $karyawan = $karyawanModel->find($user['karyawan_id']);
        }
        
        try {
            // Get COA data
            $search = $this->request->getGet('search');
            $perPage = $this->request->getGet('per_page') ?? 20;
            $page = $this->request->getGet('page') ?? 1;
            
            $coaData = $this->coaModel->getFlatList();
            
            // Filter by search if exists
            if ($search) {
                $coaData = array_filter($coaData, function($account) use ($search) {
                    return stripos($account['kode_akun'], $search) !== false || 
                           stripos($account['nama_akun'], $search) !== false ||
                           stripos($account['deskripsi'] ?? '', $search) !== false;
                });
                $coaData = array_values($coaData); // Reset array keys
            }
            
            // Stats COA
            $stats = $this->coaModel->getStats();
            $coaStatsSummary = [
                'total_akun' => [
                    'value' => $stats['total'],
                    'label' => 'Total Akun',
                    'icon' => 'fas fa-list',
                    'color' => 'primary',
                    'trend' => $stats['active'] . ' aktif'
                ],
                'akun_aktif' => [
                    'value' => $stats['active'],
                    'label' => 'Akun Aktif',
                    'icon' => 'fas fa-check-circle',
                    'color' => 'success',
                    'trend' => round(($stats['active'] / max($stats['total'], 1)) * 100) . '%'
                ],
                'akun_header' => [
                    'value' => $stats['header'],
                    'label' => 'Akun Header',
                    'icon' => 'fas fa-folder',
                    'color' => 'info',
                    'trend' => 'Grup akun'
                ],
                'akun_detail' => [
                    'value' => $stats['detail'],
                    'label' => 'Akun Detail',
                    'icon' => 'fas fa-file',
                    'color' => 'warning',
                    'trend' => 'Akun transaksi'
                ]
            ];
            
            // Financial Stats khusus untuk COA page
            $financialStats = [
                'pendapatan' => [
                    'value' => 'Rp 850 Jt',
                    'label' => 'Pendapatan Bulan Ini',
                    'icon' => 'fas fa-arrow-up',
                    'color' => 'success',
                    'trend' => '15% dari bulan lalu',
                    'trend_color' => 'text-success'
                ],
                'pengeluaran' => [
                    'value' => 'Rp 620 Jt',
                    'label' => 'Pengeluaran Bulan Ini',
                    'icon' => 'fas fa-arrow-down',
                    'color' => 'danger',
                    'trend' => '8% dari bulan lalu',
                    'trend_color' => 'text-danger'
                ],
                'aset' => [
                    'value' => 'Rp 5.2 M',
                    'label' => 'Total Aset',
                    'icon' => 'fas fa-building',
                    'color' => 'info',
                    'trend' => '12% pertumbuhan',
                    'trend_color' => 'text-info'
                ],
                'kewajiban' => [
                    'value' => 'Rp 1.8 M',
                    'label' => 'Kewajiban',
                    'icon' => 'fas fa-file-invoice-dollar',
                    'color' => 'warning',
                    'trend' => '5% dari aset',
                    'trend_color' => 'text-warning'
                ]
            ];
            
            $data = [
                'title' => 'Chart of Accounts',
                'coa' => $coaData,
                'coaStats' => $stats,
                'coaStatsSummary' => $coaStatsSummary,
                'financialStats' => $financialStats,
                'active' => 'bookkeeping',
                'user' => $user ?? ['name' => 'Accounting Staff', 'role' => 'accounting'],
                'karyawan' => $karyawan ?? [],
                'subtitle' => 'Manajemen Daftar Akun Perusahaan',
                'showFinancialStats' => false,
                'pager' => null,
                'search' => $search
            ];
            
            return view('accounting/pembukuan/daftar-akun/index', $data);
            
        } catch (\Exception $e) {
            // Error handling
            log_message('error', 'COA Index Error: ' . $e->getMessage());
            
            $data = [
                'title' => 'Chart of Accounts',
                'coa' => [],
                'coaStats' => [
                    'total' => 0,
                    'active' => 0,
                    'header' => 0,
                    'detail' => 0,
                    'by_type' => []
                ],
                'coaStatsSummary' => [
                    'total_akun' => ['value' => 0, 'label' => 'Total Akun', 'icon' => 'fas fa-list', 'color' => 'primary', 'trend' => 'Error'],
                    'akun_aktif' => ['value' => 0, 'label' => 'Akun Aktif', 'icon' => 'fas fa-check-circle', 'color' => 'success', 'trend' => 'Error'],
                    'akun_header' => ['value' => 0, 'label' => 'Akun Header', 'icon' => 'fas fa-folder', 'color' => 'info', 'trend' => 'Error'],
                    'akun_detail' => ['value' => 0, 'label' => 'Akun Detail', 'icon' => 'fas fa-file', 'color' => 'warning', 'trend' => 'Error']
                ],
                'financialStats' => [],
                'active' => 'bookkeeping',
                'user' => $user ?? ['name' => 'Accounting Staff', 'role' => 'accounting'],
                'karyawan' => $karyawan ?? [],
                'subtitle' => 'Manajemen Daftar Akun Perusahaan',
                'showFinancialStats' => false,
                'error_message' => 'Error: ' . $e->getMessage()
            ];
            
            return view('accounting/pembukuan/daftar-akun/index', $data);
        }
    }
    
    /**
     * Show form for creating new COA
     */
    public function create()
    {
        $userModel = model('UserModel');
        $karyawanModel = model('KaryawanModel');
        
        // Get current user data
        $userId = session()->get('user_id');
        $user = $userModel->find($userId);
        
        // Get karyawan data if exists
        $karyawan = [];
        if ($user && $user['karyawan_id']) {
            $karyawan = $karyawanModel->find($user['karyawan_id']);
        }
        
        // Get parent dari query string jika ada
        $parentId = $this->request->getGet('parent');
        
        // Get parent options - filter untuk parent yang bisa memiliki child
        $parentOptions = $this->coaModel->getParentOptions(null, true);
        
        // Get parent data untuk JavaScript
        $parentData = [];
        $defaultTipe = null;
        $defaultLevel = 1;
        
        if ($parentId) {
            $parent = $this->coaModel->find($parentId);
            if ($parent) {
                $defaultTipe = $parent['tipe_akun'];
                $defaultLevel = $parent['level'] + 1;
            }
        }
        
        foreach ($parentOptions as $id => $option) {
            if ($id && is_numeric($id)) {
                $parent = $this->coaModel->find($id);
                if ($parent) {
                    $parentData[$id] = [
                        'kode' => $parent['kode_akun'],
                        'nama' => $parent['nama_akun'],
                        'tipe' => $parent['tipe_akun'],
                        'is_header' => $parent['is_header'],
                        'level' => $parent['level']
                    ];
                }
            }
        }
        
        // Get kategori options
        $kategoriOptions = $this->getKategoriOptions();
        
        $data = [
            'title' => 'Tambah Akun Baru',
            'parentOptions' => $parentOptions,
            'parentData' => $parentData,
            'defaultParentId' => $parentId,
            'defaultTipe' => $defaultTipe,
            'defaultLevel' => $defaultLevel,
            'tipeAkunOptions' => [
                'Aset' => 'Aset',
                'Kewajiban' => 'Kewajiban', 
                'Ekuitas' => 'Ekuitas',
                'Pendapatan' => 'Pendapatan',
                'Beban' => 'Beban'
            ],
            'saldoNormalOptions' => [
                'Debit' => 'Debit',
                'Kredit' => 'Kredit'
            ],
            'jenisAkunOptions' => [
                '0' => 'Detail (Akun Transaksi)',
                '1' => 'Header (Akun Grup)'
            ],
            'levelOptions' => [
                '1' => 'Level 1 (Root)',
                '2' => 'Level 2',
                '3' => 'Level 3', 
                '4' => 'Level 4',
                '5' => 'Level 5'
            ],
            'kategoriOptions' => $kategoriOptions,
            'active' => 'bookkeeping',
            'user' => $user ?? ['name' => 'Accounting Staff', 'role' => 'accounting'],
            'karyawan' => $karyawan ?? [],
            'subtitle' => 'Tambah Akun ke Chart of Accounts',
            'validation' => \Config\Services::validation(),
            'showFinancialStats' => false
        ];
        
        return view('accounting/pembukuan/daftar-akun/create', $data);
    }
    
    public function store()
{
    $rules = [
        'nama_akun' => [
            'rules' => 'required|max_length[200]',
            'errors' => [
                'required' => 'Nama akun harus diisi',
                'max_length' => 'Nama akun maksimal 200 karakter'
            ]
        ],
        'tipe_akun' => [
            'rules' => 'required|in_list[Aset,Kewajiban,Ekuitas,Pendapatan,Beban]',
            'errors' => [
                'required' => 'Tipe akun harus dipilih',
                'in_list' => 'Tipe akun tidak valid'
            ]
        ],
        'saldo_normal' => [
            'rules' => 'required|in_list[Debit,Kredit]',
            'errors' => [
                'required' => 'Saldo normal harus dipilih',
                'in_list' => 'Saldo normal tidak valid'
            ]
        ],
        'is_header' => [
            'rules' => 'required|in_list[0,1]',
            'errors' => [
                'required' => 'Jenis akun harus dipilih',
                'in_list' => 'Jenis akun tidak valid'
            ]
        ],
        'level' => [
            'rules' => 'required|in_list[1,2,3,4,5]',
            'errors' => [
                'required' => 'Level harus dipilih',
                'in_list' => 'Level harus antara 1-5'
            ]
        ]
    ];
    
    // Validasi untuk kode akun jika diisi manual
    $kodeAkunInput = $this->request->getPost('kode_akun');
    if (!empty($kodeAkunInput)) {
        $rules['kode_akun'] = [
            'rules' => 'required|max_length[20]|regex_match[/^[1-5](-\d+)*$/]',
            'errors' => [
                'required' => 'Kode akun harus diisi',
                'max_length' => 'Kode akun maksimal 20 karakter',
                'regex_match' => 'Format kode akun tidak valid. Contoh: 1-1000, 1-1100-01'
            ]
        ];
    }
    
    if (!$this->validate($rules)) {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }
    
    // 🔥 VALIDASI FORMAT KODE MANUAL
    if (!empty($kodeAkunInput)) {
        $tipeAkun = $this->request->getPost('tipe_akun');
        $prefixMap = [
            'Aset' => '1',
            'Kewajiban' => '2',
            'Ekuitas' => '3',
            'Pendapatan' => '4',
            'Beban' => '5'
        ];
        $expectedPrefix = $prefixMap[$tipeAkun] ?? null;
        
        if ($expectedPrefix && !str_starts_with($kodeAkunInput, $expectedPrefix . '-')) {
            return redirect()->back()->withInput()->with('error', 
                "Format kode akun tidak sesuai. Untuk tipe '{$tipeAkun}', kode harus diawali dengan '{$expectedPrefix}-'"
            );
        }
        
        // 🔥 CEK DUPLIKASI - TAPI JANGAN LANGSUNG MODIFIKASI
        if ($this->coaModel->isKodeExists($kodeAkunInput)) {
            return redirect()->back()->withInput()->with('error', 
                "Kode akun '{$kodeAkunInput}' sudah digunakan. Silakan gunakan kode lain."
            );
        }
    }
    
    // Prepare data
    $data = [
        'kode_akun' => $kodeAkunInput ?: '', // 🔥 Biarkan kosong untuk auto-generate
        'nama_akun' => $this->request->getPost('nama_akun'),
        'tipe_akun' => $this->request->getPost('tipe_akun'),
        'kategori' => $this->request->getPost('kategori'),
        'saldo_normal' => $this->request->getPost('saldo_normal'),
        'deskripsi' => $this->request->getPost('deskripsi'),
        'is_header' => $this->request->getPost('is_header'),
        'parent_id' => $this->request->getPost('parent_id') ?: null,
        'level' => $this->request->getPost('level'),
        'is_active' => 1,
        'created_by' => session()->get('user_id')
    ];
    
    // Validasi konsistensi parent dan level
    if ($data['parent_id']) {
        $parent = $this->coaModel->find($data['parent_id']);
        if (!$parent) {
            return redirect()->back()->withInput()->with('error', 'Parent tidak ditemukan');
        }
        
        if ($data['tipe_akun'] !== $parent['tipe_akun']) {
            return redirect()->back()->withInput()->with('error', 
                'Tipe akun harus sama dengan parent. Parent memiliki tipe: ' . $parent['tipe_akun']
            );
        }
        
        if ($data['level'] <= $parent['level']) {
            return redirect()->back()->withInput()->with('error', 
                'Level child harus lebih tinggi dari level parent. Parent level: ' . $parent['level']
            );
        }
        
        if ($data['is_header'] == 1 && $parent['is_header'] == 0) {
            return redirect()->back()->withInput()->with('error', 
                'Akun detail tidak dapat memiliki child. Pilih parent yang merupakan akun header.'
            );
        }
    } else {
        if ($data['level'] != 1) {
            return redirect()->back()->withInput()->with('error', 
                'Akun tanpa parent harus Level 1'
            );
        }
    }
    
    if ($data['level'] > 5) {
        return redirect()->back()->withInput()->with('error', 'Level maksimum adalah 5');
    }
    
    try {
        // 🔥 SIMPAN - Model akan handle auto-generate jika kode kosong
        if ($this->coaModel->save($data)) {
            return redirect()->to(site_url('accounting/pembukuan/daftar-akun'))->with('success', 'Akun berhasil ditambahkan');
        } else {
            $errors = $this->coaModel->errors();
            return redirect()->back()->withInput()->with('errors', $errors);
        }
    } catch (\Exception $e) {
        log_message('error', 'COA Store Error: ' . $e->getMessage());
        return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
    
    /**
     * Show COA detail
     */
    public function detail($id)
    {
        $userModel = model('UserModel');
        $karyawanModel = model('KaryawanModel');
        
        // Get current user data
        $userId = session()->get('user_id');
        $user = $userModel->find($userId);
        
        // Get karyawan data if exists
        $karyawan = [];
        if ($user && $user['karyawan_id']) {
            $karyawan = $karyawanModel->find($user['karyawan_id']);
        }
        
        // Get COA data
        $coa = $this->coaModel->find($id);
        
        if (!$coa) {
            return redirect()->to(site_url('accounting/pembukuan/daftar-akun'))->with('error', 'Akun tidak ditemukan');
        }
        
        // Get account path (breadcrumb)
        $accountPath = $this->coaModel->getAccountPath($id);
        
        // Get children if header
        $children = [];
        if ($coa['is_header'] == 1) {
            $children = $this->coaModel->getChildren($id);
        }
        
        // Get parent info
        $parent = null;
        if ($coa['parent_id']) {
            $parent = $this->coaModel->find($coa['parent_id']);
        }
        
        // Get creator and updater info
        $creator = null;
        if ($coa['created_by']) {
            $creator = $userModel->find($coa['created_by']);
        }
        
        $updater = null;
        if ($coa['updated_by']) {
            $updater = $userModel->find($coa['updated_by']);
        }
        
        // Format timestamps
        $coa['created_at_formatted'] = date('d/m/Y H:i', strtotime($coa['created_at']));
        $coa['updated_at_formatted'] = date('d/m/Y H:i', strtotime($coa['updated_at']));
        
        // Get account statistics
        $stats = $this->coaModel->getStats();
        
        $data = [
            'title' => 'Detail Akun: ' . $coa['nama_akun'],
            'coa' => $coa,
            'accountPath' => $accountPath,
            'children' => $children,
            'parent' => $parent,
            'creator' => $creator,
            'updater' => $updater,
            'stats' => $stats,
            'active' => 'bookkeeping',
            'user' => $user ?? ['name' => 'Accounting Staff', 'role' => 'accounting'],
            'karyawan' => $karyawan ?? [],
            'subtitle' => 'Detail Informasi Akun',
            'showFinancialStats' => false
        ];
        
        return view('accounting/pembukuan/daftar-akun/detail', $data);
    }
    
    /**
     * Show edit form
     */
    public function edit($id)
    {
        $userModel = model('UserModel');
        $karyawanModel = model('KaryawanModel');
        
        $userId = session()->get('user_id');
        $user = $userModel->find($userId);
        $karyawan = $user && $user['karyawan_id'] ? $karyawanModel->find($user['karyawan_id']) : [];
        
        $coa = $this->coaModel->find($id);
        
        if (!$coa) {
            return redirect()->to(site_url('accounting/pembukuan/daftar-akun'))->with('error', 'Akun tidak ditemukan');
        }
        
        // Validasi: jika akun memiliki children, tidak bisa diubah dari header ke detail
        $hasChildren = $this->coaModel->hasChildren($id);
        
        // Hitung jumlah children untuk ditampilkan di view
        $childrenCount = $hasChildren ? $this->coaModel->where('parent_id', $id)->countAllResults() : 0;
        
        // Get parent options - exclude current account and its descendants
        $parentOptions = $this->coaModel->getParentOptions($id, true);
        
        // Get account path for breadcrumb
        $accountPath = $this->coaModel->getAccountPath($id);
        
        // Get parent data untuk JavaScript
        $parentData = [];
        foreach ($parentOptions as $parentId => $option) {
            if ($parentId && is_numeric($parentId)) {
                $parent = $this->coaModel->find($parentId);
                if ($parent) {
                    $parentData[$parentId] = [
                        'kode' => $parent['kode_akun'],
                        'nama' => $parent['nama_akun'],
                        'tipe' => $parent['tipe_akun'],
                        'is_header' => $parent['is_header'],
                        'level' => $parent['level']
                    ];
                }
            }
        }
        
        $data = [
            'title' => 'Edit Akun: ' . $coa['nama_akun'],
            'coa' => $coa,
            'parentOptions' => $parentOptions,
            'parentData' => $parentData,
            'hasChildren' => $hasChildren,
            'childrenCount' => $childrenCount,
            'accountPath' => $accountPath,
            'tipeAkunOptions' => [
                'Aset' => 'Aset',
                'Kewajiban' => 'Kewajiban',
                'Ekuitas' => 'Ekuitas',
                'Pendapatan' => 'Pendapatan',
                'Beban' => 'Beban'
            ],
            'saldoNormalOptions' => [
                'Debit' => 'Debit',
                'Kredit' => 'Kredit'
            ],
            'kategoriOptions' => $this->getKategoriOptions(),
            'jenisAkunOptions' => [
                '0' => 'Detail (Akun Transaksi)',
                '1' => 'Header (Akun Grup)'
            ],
            'levelOptions' => [
                '1' => 'Level 1 (Root)',
                '2' => 'Level 2',
                '3' => 'Level 3',
                '4' => 'Level 4',
                '5' => 'Level 5'
            ],
            'active' => 'bookkeeping',
            'user' => $user ?? ['name' => 'Accounting Staff', 'role' => 'accounting'],
            'karyawan' => $karyawan ?? [],
            'subtitle' => 'Edit Informasi Akun',
            'validation' => $this->validation,
            'showFinancialStats' => false
        ];
        
        return view('accounting/pembukuan/daftar-akun/edit', $data);
    }
    
    /**
     * Update COA
     */
    public function update($id)
    {
        $coa = $this->coaModel->find($id);
        
        if (!$coa) {
            return redirect()->to(site_url('accounting/pembukuan/daftar-akun'))->with('error', 'Akun tidak ditemukan');
        }
        
        // Validasi: jika akun memiliki children, tidak bisa diubah dari header ke detail
        $hasChildren = $this->coaModel->hasChildren($id);
        
        $rules = [
            'kode_akun' => [
                'rules' => "required|max_length[20]|regex_match[/^[1-5](-\d+)*$/]|is_unique[coa.kode_akun,id,{$id}]",
                'errors' => [
                    'required' => 'Kode akun harus diisi',
                    'max_length' => 'Kode akun maksimal 20 karakter',
                    'regex_match' => 'Format kode akun tidak valid. Contoh: 1-1000, 1-1100-01',
                    'is_unique' => 'Kode akun sudah digunakan'
                ]
            ],
            'nama_akun' => [
                'rules' => 'required|max_length[200]',
                'errors' => [
                    'required' => 'Nama akun harus diisi',
                    'max_length' => 'Nama akun maksimal 200 karakter'
                ]
            ],
            'tipe_akun' => [
                'rules' => 'required|in_list[Aset,Kewajiban,Ekuitas,Pendapatan,Beban]',
                'errors' => [
                    'required' => 'Tipe akun harus dipilih',
                    'in_list' => 'Tipe akun tidak valid'
                ]
            ],
            'saldo_normal' => [
                'rules' => 'required|in_list[Debit,Kredit]',
                'errors' => [
                    'required' => 'Saldo normal harus dipilih',
                    'in_list' => 'Saldo normal tidak valid'
                ]
            ],
            'is_header' => [
                'rules' => 'required|in_list[0,1]',
                'errors' => [
                    'required' => 'Jenis akun harus dipilih',
                    'in_list' => 'Jenis akun tidak valid'
                ]
            ],
            'level' => [
                'rules' => 'required|in_list[1,2,3,4,5]',
                'errors' => [
                    'required' => 'Level harus dipilih',
                    'in_list' => 'Level harus antara 1-5'
                ]
            ]
        ];
        
        // Jika akun memiliki children, beberapa field tidak boleh diubah
        if ($hasChildren) {
            // Tipe akun tidak boleh diubah jika punya children
            if ($this->request->getPost('tipe_akun') != $coa['tipe_akun']) {
                return redirect()->back()->withInput()->with('error', 
                    'Tipe akun tidak dapat diubah karena akun ini memiliki sub-akun'
                );
            }
            
            // Level tidak boleh diubah jika punya children
            if ($this->request->getPost('level') != $coa['level']) {
                return redirect()->back()->withInput()->with('error', 
                    'Level tidak dapat diubah karena akun ini memiliki sub-akun'
                );
            }
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Validasi kode format
        $kode = $this->request->getPost('kode_akun');
        $tipe = $this->request->getPost('tipe_akun');
        
        if (!$this->coaModel->validateKodeFormat($kode, $tipe)) {
            $prefixMap = [
                'Aset' => '1',
                'Kewajiban' => '2',
                'Ekuitas' => '3',
                'Pendapatan' => '4',
                'Beban' => '5'
            ];
            $expectedPrefix = $prefixMap[$tipe] ?? '?';
            
            return redirect()->back()->withInput()->with('error', 
                "Format kode akun tidak sesuai. Untuk tipe akun '{$tipe}', kode harus diawali dengan '{$expectedPrefix}-'. Contoh: {$expectedPrefix}-1000"
            );
        }
        
        // Validasi: jika akun memiliki children, tidak bisa diubah dari header ke detail
        $newIsHeader = $this->request->getPost('is_header');
        
        if ($hasChildren && $coa['is_header'] == 1 && $newIsHeader == 0) {
            return redirect()->back()->withInput()->with('error', 
                'Akun ini memiliki sub-akun. Tidak dapat mengubah dari Header ke Detail.'
            );
        }
        
        // Validasi: parent tidak bisa menjadi dirinya sendiri
        $parentId = $this->request->getPost('parent_id') ?: null;
        if ($parentId == $id) {
            return redirect()->back()->withInput()->with('error', 
                'Akun tidak dapat menjadi parent dari dirinya sendiri'
            );
        }
        
        // Prepare data
        $data = [
            'id' => $id,
            'kode_akun' => $kode,
            'nama_akun' => $this->request->getPost('nama_akun'),
            'tipe_akun' => $tipe,
            'kategori' => $this->request->getPost('kategori'),
            'saldo_normal' => $this->request->getPost('saldo_normal'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'is_header' => $newIsHeader,
            'parent_id' => $parentId,
            'level' => $this->request->getPost('level'), // Biarkan user pilih level
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'updated_by' => session()->get('user_id')
        ];
        
        // Validasi konsistensi parent dan level
        if ($data['parent_id']) {
            $parent = $this->coaModel->find($data['parent_id']);
            if (!$parent) {
                return redirect()->back()->withInput()->with('error', 'Parent tidak ditemukan');
            }
            
            // Validasi: child harus memiliki tipe yang sama dengan parent
            if ($data['tipe_akun'] !== $parent['tipe_akun']) {
                return redirect()->back()->withInput()->with('error', 
                    'Tipe akun harus sama dengan parent. Parent memiliki tipe: ' . $parent['tipe_akun']
                );
            }
            
            // Validasi: child harus memiliki level lebih tinggi dari parent
            if ($data['level'] <= $parent['level']) {
                return redirect()->back()->withInput()->with('error', 
                    'Level child harus lebih tinggi dari level parent. Parent level: ' . $parent['level']
                );
            }
            
            // Validasi: child dari detail account harus detail juga
            if ($data['is_header'] == 1 && $parent['is_header'] == 0) {
                return redirect()->back()->withInput()->with('error', 
                    'Parent yang dipilih adalah akun detail. Akun detail tidak dapat memiliki child. Pilih parent yang merupakan akun header.'
                );
            }
        } else {
            // Jika tidak ada parent, harus level 1
            if ($data['level'] != 1) {
                return redirect()->back()->withInput()->with('error', 
                    'Akun tanpa parent harus Level 1'
                );
            }
        }
        
        // Validasi level maksimum
        if ($data['level'] > 5) {
            return redirect()->back()->withInput()->with('error', 
                'Level maksimum adalah 5'
            );
        }
        
        try {
            // Update database
            if ($this->coaModel->save($data)) {
                return redirect()->to(site_url('accounting/pembukuan/daftar-akun/detail/' . $id))->with('success', 'Akun berhasil diperbarui');
            } else {
                $errors = $this->coaModel->errors();
                return redirect()->back()->withInput()->with('errors', $errors);
            }
        } catch (\Exception $e) {
            log_message('error', 'COA Update Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete COA
     */
    public function delete($id)
    {
        $coa = $this->coaModel->find($id);
        
        if (!$coa) {
            return redirect()->to(site_url('accounting/pembukuan/daftar-akun'))->with('error', 'Akun tidak ditemukan');
        }
        
        try {
            // Check if account has children
            $hasChildren = $this->coaModel->hasChildren($id);
            
            if ($hasChildren) {
                // Mark as inactive instead of deleting
                $this->coaModel->updateStatus($id, 0);
                return redirect()->to(site_url('accounting/pembukuan/daftar-akun'))->with('success', 
                    'Akun dinonaktifkan karena memiliki sub-akun. Nonaktifkan sub-akun terlebih dahulu jika ingin menghapus.'
                );
            }
            
            // Check if account has been used in transactions (jurnal)
            $jurnalDetailModel = model('JurnalDetailModel');
            $usedInJurnal = $jurnalDetailModel->where('coa_id', $id)->countAllResults();
            
            if ($usedInJurnal > 0) {
                // Mark as inactive
                $this->coaModel->update($id, ['is_active' => 0]);
                return redirect()->to(site_url('accounting/pembukuan/daftar-akun'))->with('success', 
                    'Akun dinonaktifkan karena sudah digunakan dalam transaksi.'
                );
            }
            
            // Delete permanently
            $this->coaModel->delete($id);
            return redirect()->to(site_url('accounting/pembukuan/daftar-akun'))->with('success', 'Akun berhasil dihapus');
            
        } catch (\Exception $e) {
            log_message('error', 'COA Delete Error: ' . $e->getMessage());
            return redirect()->to(site_url('accounting/pembukuan/daftar-akun'))->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Tree view
     */
    public function tree()
    {
        $userModel = model('UserModel');
        $karyawanModel = model('KaryawanModel');
        
        $userId = session()->get('user_id');
        $user = $userModel->find($userId);
        $karyawan = $user && $user['karyawan_id'] ? $karyawanModel->find($user['karyawan_id']) : [];
        
        $coaTree = $this->coaModel->getAllWithHierarchy();
        
        $data = [
            'title' => 'Struktur Chart of Accounts',
            'coaTree' => $coaTree,
            'active' => 'bookkeeping',
            'user' => $user ?? ['name' => 'Accounting Staff', 'role' => 'accounting'],
            'karyawan' => $karyawan ?? [],
            'subtitle' => 'Struktur Hierarki Akun'
        ];
        
        return view('accounting/pembukuan/daftar-akun/tree', $data);
    }
    
    /**
     * Export to Excel
     */
    public function export()
    {
        try {
            $exportData = $this->coaModel->getExportData();
            
            $excel = new ExportExcel();
            $excel->setTitle('Chart of Accounts - ' . date('Y-m-d'))
                  ->setSubject('Daftar Akun Perusahaan')
                  ->setDescription('Data Chart of Accounts PT. Cipta Duta Wacana')
                  ->setKeywords('coa, akuntansi, akun, chart of accounts')
                  ->setAuthor('CDW Accounting System')
                  ->setCompany('PT. Cipta Duta Wacana');
            
            $headers = [
                'No',
                'Kode Akun',
                'Nama Akun', 
                'Tipe Akun',
                'Kategori',
                'Saldo Normal',
                'Level',
                'Jenis',
                'Status',
                'Parent Kode',
                'Deskripsi',
                'Dibuat Oleh',
                'Diupdate Oleh',
                'Tanggal Dibuat',
                'Tanggal Update'
            ];
            
            $formattedData = [];
            $counter = 1;
            
            foreach ($exportData as $row) {
                $formattedData[] = [
                    $counter++,
                    $row['Kode Akun'],
                    $row['Nama Akun'],
                    $row['Tipe Akun'],
                    $row['Kategori'],
                    $row['Saldo Normal'],
                    $row['Level'],
                    $row['Jenis'],
                    $row['Status'],
                    $row['Parent'],
                    $row['Deskripsi'],
                    $row['created_by'],
                    $row['updated_by'],
                    date('d/m/Y H:i', strtotime($row['created_at'])),
                    date('d/m/Y H:i', strtotime($row['updated_at']))
                ];
            }
            
            $excel->setHeaders($headers)
                  ->setData($formattedData)
                  ->setAutoFilter(true)
                  ->setFreezePane('A2')
                  ->setColumnWidths([
                      'A' => 8,
                      'B' => 15,
                      'C' => 35,
                      'D' => 15,
                      'E' => 20,
                      'F' => 15,
                      'G' => 10,
                      'H' => 12,
                      'I' => 12,
                      'J' => 15,
                      'K' => 40,
                      'L' => 20,
                      'M' => 20,
                      'N' => 20,
                      'O' => 20
                  ]);
            
            return $excel->export('coa_export_' . date('Ymd_His') . '.xlsx');
            
        } catch (\Exception $e) {
            log_message('error', 'Export COA Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }
    
    /**
     * Print view
     */
    public function print()
    {
        $userModel = model('UserModel');
        
        $userId = session()->get('user_id');
        $user = $userModel->find($userId);
        
        $coaData = $this->coaModel->getFlatList();
        
        $data = [
            'title' => 'Chart of Accounts - ' . date('d/m/Y'),
            'coa' => $coaData,
            'company' => model('PerusahaanModel')->first() ?? ['nama_perusahaan' => 'PT. Cipta Duta Wacana'],
            'printed_by' => $user['name'] ?? 'System',
            'print_date' => date('d/m/Y H:i:s')
        ];
        
        return view('accounting/pembukuan/daftar-akun/print', $data);
    }
    
    /**
     * AJAX: Get parent info for creating child account
     */
    public function ajaxGetParentInfo()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['error' => 'Method not allowed']);
        }
        
        $parentId = $this->request->getGet('parent_id');
        
        if (!$parentId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parent ID required'
            ]);
        }
        
        try {
            $parent = $this->coaModel->find($parentId);
            
            if (!$parent) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Parent tidak ditemukan'
                ]);
            }
            
            // Generate next child code
            $nextCode = $this->coaModel->getNextChildCode($parentId);
            
            return $this->response->setJSON([
                'success' => true,
                'parent' => [
                    'id' => $parent['id'],
                    'kode' => $parent['kode_akun'],
                    'nama' => $parent['nama_akun'],
                    'tipe' => $parent['tipe_akun'],
                    'is_header' => $parent['is_header'],
                    'level' => $parent['level']
                ],
                'next_code' => $nextCode,
                'next_level' => $parent['level'] + 1
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'AJAX Get Parent Info Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memuat informasi parent: ' . $e->getMessage()
            ]);
        }
    }
    
   /**
 * AJAX: Validate kode akun
 */
public function ajaxValidateKode()
{
    // Set header JSON untuk response
    $this->response->setHeader('Content-Type', 'application/json');
    
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON([
            'valid' => false,
            'message' => 'Method not allowed'
        ])->setStatusCode(405);
    }
    
    $kode = $this->request->getVar('kode');
    $tipe = $this->request->getVar('tipe');
    $exceptId = $this->request->getVar('except_id');
    
    // Log untuk debugging
    log_message('debug', 'AJAX Validate Kode - Input: kode=' . $kode . ', tipe=' . $tipe . ', except_id=' . $exceptId);
    
    // Validasi input
    if (empty($kode)) {
        return $this->response->setJSON([
            'valid' => true, // Kode kosong dianggap valid (akan auto-generate)
            'message' => 'Kode akan digenerate otomatis'
        ]);
    }
    
    if (empty($tipe)) {
        return $this->response->setJSON([
            'valid' => false,
            'message' => 'Pilih tipe akun terlebih dahulu'
        ]);
    }
    
    try {
        // Cek format regex
        $regex = '/^[1-5](-\d+)*$/';
        if (!preg_match($regex, $kode)) {
            return $this->response->setJSON([
                'valid' => false,
                'message' => 'Format tidak valid. Contoh: 1-1000, 1-1100-01'
            ]);
        }
        
        // Cek prefix berdasarkan tipe
        $prefixMap = [
            'Aset' => '1',
            'Kewajiban' => '2',
            'Ekuitas' => '3',
            'Pendapatan' => '4',
            'Beban' => '5'
        ];
        
        $expectedPrefix = $prefixMap[$tipe] ?? null;
        if (!$expectedPrefix) {
            return $this->response->setJSON([
                'valid' => false,
                'message' => 'Tipe akun tidak valid'
            ]);
        }
        
        if (!str_starts_with($kode, $expectedPrefix . '-')) {
            return $this->response->setJSON([
                'valid' => false,
                'message' => "Kode harus diawali dengan '{$expectedPrefix}-' untuk tipe {$tipe}"
            ]);
        }
        
        // Cek duplikasi kode
        $exists = $this->coaModel->isKodeExists($kode, $exceptId);
        
        if ($exists) {
            return $this->response->setJSON([
                'valid' => false,
                'message' => "Kode '{$kode}' sudah digunakan. Silakan gunakan kode lain."
            ]);
        }
        
        // Semua validasi berhasil
        return $this->response->setJSON([
            'valid' => true,
            'message' => 'Kode akun valid dan tersedia'
        ]);
        
    } catch (\Exception $e) {
        log_message('error', 'AJAX Validate Kode Error: ' . $e->getMessage());
        return $this->response->setJSON([
            'valid' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]);
    }
}
    
    /**
     * AJAX: Get tree data for visualization
     */
    public function ajaxGetTreeData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['error' => 'Method not allowed']);
        }
        
        try {
            // Get COA data with hierarchy
            $coaTree = $this->coaModel->getAllWithHierarchy(false);
            
            if (empty($coaTree)) {
                return $this->response->setJSON([
                    'success' => true,
                    'data' => [],
                    'message' => 'No data available'
                ]);
            }
            
            // Convert to flat structure for easier processing
            $flatData = $this->flattenTreeData($coaTree);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $flatData,
                'count' => count($flatData)
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Tree Data Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading tree data: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Flatten tree data for frontend processing
     */
    private function flattenTreeData($tree, $parentId = null)
    {
        $result = [];
        
        foreach ($tree as $node) {
            $nodeData = [
                'id' => $node['id'],
                'kode_akun' => $node['kode_akun'],
                'nama_akun' => $node['nama_akun'],
                'tipe_akun' => $node['tipe_akun'],
                'saldo_normal' => $node['saldo_normal'],
                'kategori' => $node['kategori'],
                'deskripsi' => $node['deskripsi'],
                'is_header' => $node['is_header'],
                'is_active' => $node['is_active'],
                'level' => $node['level'],
                'parent_id' => $node['parent_id'],
                'children' => []
            ];
            
            if (!empty($node['children'])) {
                $nodeData['children'] = $this->flattenTreeData($node['children'], $node['id']);
            }
            
            $result[] = $nodeData;
        }
        
        return $result;
    }
    
    /**
     * Get kategori options based on COA data
     */
    private function getKategoriOptions()
    {
        // Ambil kategori unik dari database
        $categories = $this->coaModel->distinct()
            ->select('kategori')
            ->where('kategori IS NOT NULL')
            ->where('kategori !=', '')
            ->orderBy('kategori', 'ASC')
            ->findAll();
        
        $options = ['' => '-- Pilih Kategori --'];
        
        foreach ($categories as $category) {
            if ($category['kategori']) {
                $options[$category['kategori']] = $category['kategori'];
            }
        }
        
        // Tambahkan kategori default jika tidak ada
        if (count($options) == 1) {
            $options = array_merge($options, [
                'Aset Lancar' => 'Aset Lancar',
                'Aset Tetap' => 'Aset Tetap',
                'Kewajiban Lancar' => 'Kewajiban Lancar',
                'Kewajiban Jangka Panjang' => 'Kewajiban Jangka Panjang',
                'Modal' => 'Modal',
                'Pendapatan Usaha' => 'Pendapatan Usaha',
                'Pendapatan Lainnya' => 'Pendapatan Lainnya',
                'Beban Operasional' => 'Beban Operasional',
                'Beban Administrasi' => 'Beban Administrasi'
            ]);
        }
        
        return $options;
    }
    
    /**
     * Toggle account status
     */
    public function toggleStatus($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to(site_url('accounting/pembukuan/daftar-akun'))
                ->with('error', 'Akses tidak valid');
        }
        
        try {
            $coa = $this->coaModel->find($id);
            
            if (!$coa) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Akun tidak ditemukan'
                ]);
            }
            
            $newStatus = $coa['is_active'] ? 0 : 1;
            
            // Jika menonaktifkan header, nonaktifkan juga children-nya
            $result = $this->coaModel->updateStatus($id, $newStatus);
            
            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Status akun berhasil diubah',
                    'new_status' => $newStatus,
                    'status_text' => $newStatus ? 'Aktif' : 'Nonaktif'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengubah status akun'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Toggle COA Status Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get accounts for dropdown (AJAX)
     */
    public function ajaxGetAccounts($tipe = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['error' => 'Method not allowed']);
        }
        
        try {
            $accounts = $this->coaModel->getForTransaction($tipe);
            
            $options = ['' => '-- Pilih Akun --'];
            foreach ($accounts as $account) {
                $options[$account['id']] = "{$account['kode_akun']} - {$account['nama_akun']}";
            }
            
            return $this->response->setJSON([
                'success' => true,
                'options' => $options
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Quick add account (AJAX)
     */
    public function ajaxQuickAdd()
    {
        if (!$this->request->isAJAX() || !$this->request->getMethod() === 'post') {
            return $this->response->setStatusCode(405)->setJSON(['error' => 'Method not allowed']);
        }
        
        $rules = [
            'kode_akun' => 'required|max_length[20]|is_unique[coa.kode_akun]',
            'nama_akun' => 'required|max_length[200]',
            'tipe_akun' => 'required|in_list[Aset,Kewajiban,Ekuitas,Pendapatan,Beban]',
            'saldo_normal' => 'required|in_list[Debit,Kredit]'
        ];
        
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }
        
        try {
            $data = [
                'kode_akun' => $this->request->getPost('kode_akun'),
                'nama_akun' => $this->request->getPost('nama_akun'),
                'tipe_akun' => $this->request->getPost('tipe_akun'),
                'saldo_normal' => $this->request->getPost('saldo_normal'),
                'kategori' => $this->request->getPost('kategori'),
                'deskripsi' => $this->request->getPost('deskripsi'),
                'is_header' => 0, // Quick add always detail account
                'parent_id' => $this->request->getPost('parent_id'),
                'level' => 1,
                'is_active' => 1,
                'created_by' => session()->get('user_id')
            ];
            
            // Calculate level if parent is selected
            if ($data['parent_id']) {
                $parent = $this->coaModel->find($data['parent_id']);
                if ($parent) {
                    $data['level'] = $parent['level'] + 1;
                }
            }
            
            $id = $this->coaModel->insert($data);
            
            if ($id) {
                return $this->response->setJSON([
                    'success' => true,
                    'id' => $id,
                    'kode_akun' => $data['kode_akun'],
                    'nama_akun' => $data['nama_akun'],
                    'message' => 'Akun berhasil ditambahkan'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menambahkan akun'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Quick Add COA Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}