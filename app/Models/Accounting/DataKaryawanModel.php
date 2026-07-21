<?php
namespace App\Models\Accounting\Penggajian;

use CodeIgniter\Model;

class DataKaryawanModel extends Model
{
    protected $table = 'karyawan';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'nik',
        'nama_lengkap',
        'nama_panggilan',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'status_pernikahan',
        'alamat',
        'telepon',
        'email',
        'jabatan',
        'departemen',
        'divisi',
        'tanggal_masuk',
        'status_karyawan',
        'tanggal_keluar',
        'alasan_keluar',
        'no_npwp',
        'no_bpjs_kes',
        'no_bpjs_tk',
        'no_rekening',
        'bank',
        'nama_rekening',
        'pendidikan_terakhir',
        'jurusan',
        'institusi',
        'tahun_lulus',
        'kontak_darurat_nama',
        'kontak_darurat_hubungan',
        'kontak_darurat_telepon',
        'foto',
        'cv_path'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'nik' => 'required|min_length[3]|max_length[20]|is_unique[karyawan.nik,id,{id}]',
        'nama_lengkap' => 'required|min_length[3]|max_length[100]',
        'jenis_kelamin' => 'permit_empty|in_list[L,P]',
        'email' => 'permit_empty|valid_email|max_length[100]',
        'tanggal_masuk' => 'permit_empty|valid_date',
        'status_karyawan' => 'permit_empty|in_list[Tetap,Kontrak,Probation,Magang]'
    ];

    protected $validationMessages = [
        'nik' => [
            'required' => 'NIK harus diisi',
            'is_unique' => 'NIK sudah digunakan'
        ],
        'nama_lengkap' => [
            'required' => 'Nama lengkap harus diisi'
        ],
        'email' => [
            'valid_email' => 'Format email tidak valid'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateNIK', 'setDefaultValues'];
    protected $beforeUpdate = ['setUpdatedAt'];

    /**
     * Generate NIK otomatis jika tidak diisi
     * Format: THN-BLN-XXXX (contoh: 2026-03-0001)
     */
    protected function generateNIK(array $data)
    {
        if (empty($data['data']['nik'])) {
            $tahun = date('Y');
            $bulan = date('m');
            $prefix = $tahun . '-' . $bulan . '-';
            
            // Cari NIK terakhir dengan prefix yang sama
            $lastKaryawan = $this->select('nik')
                ->like('nik', $prefix, 'after')
                ->orderBy('nik', 'DESC')
                ->first();
            
            if ($lastKaryawan) {
                $lastNum = substr($lastKaryawan['nik'], strlen($prefix));
                $nextNum = str_pad((int)$lastNum + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNum = '0001';
            }
            
            $data['data']['nik'] = $prefix . $nextNum;
        }
        
        return $data;
    }

    /**
     * Set default values untuk field yang mungkin kosong
     */
    protected function setDefaultValues(array $data)
    {
        // Set default status karyawan jika tidak diisi
        if (!isset($data['data']['status_karyawan']) || empty($data['data']['status_karyawan'])) {
            $data['data']['status_karyawan'] = 'Kontrak';
        }
        
        // Set default jenis kelamin jika tidak diisi
        if (!isset($data['data']['jenis_kelamin']) || empty($data['data']['jenis_kelamin'])) {
            $data['data']['jenis_kelamin'] = 'L';
        }
        
        // Set default status pernikahan jika tidak diisi
        if (!isset($data['data']['status_pernikahan']) || empty($data['data']['status_pernikahan'])) {
            $data['data']['status_pernikahan'] = 'Belum Menikah';
        }
        
        return $data;
    }

    /**
     * Set updated_at timestamp
     */
    protected function setUpdatedAt(array $data)
    {
        $data['data']['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    /**
     * Get all karyawan untuk penggajian dengan filter
     */
    public function getAllForPayroll($filters = [], $perPage = 20, $page = 1)
    {
        $builder = $this->select('
                karyawan.*,
                users.id as user_id,
                users.username,
                users.email as user_email,
                users.role,
                users.status as user_status,
                (SELECT COUNT(*) FROM kontrak WHERE kontrak.karyawan_id = karyawan.id AND kontrak.status = "Aktif") as kontrak_aktif,
                (SELECT MAX(tanggal_mulai) FROM kontrak WHERE kontrak.karyawan_id = karyawan.id) as kontrak_terakhir
            ')
            ->join('users', 'users.karyawan_id = karyawan.id', 'left')
            ->where('karyawan.deleted_at IS NULL');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('karyawan.nik', $search)
                ->orLike('karyawan.nama_lengkap', $search)
                ->orLike('karyawan.nama_panggilan', $search)
                ->orLike('karyawan.email', $search)
                ->orLike('karyawan.jabatan', $search)
                ->orLike('karyawan.departemen', $search)
                ->orLike('karyawan.divisi', $search)
                ->orLike('karyawan.telepon', $search)
                ->groupEnd();
        }
        
        if (!empty($filters['status_karyawan'])) {
            if ($filters['status_karyawan'] === 'aktif') {
                $builder->whereIn('karyawan.status_karyawan', ['Tetap', 'Kontrak', 'Probation']);
            } elseif ($filters['status_karyawan'] === 'nonaktif') {
                $builder->where('karyawan.status_karyawan', 'Magang');
            } elseif ($filters['status_karyawan'] === 'keluar') {
                $builder->where('karyawan.tanggal_keluar IS NOT NULL');
            } else {
                $builder->where('karyawan.status_karyawan', $filters['status_karyawan']);
            }
        }
        
        if (!empty($filters['departemen'])) {
            $builder->where('karyawan.departemen', $filters['departemen']);
        }
        
        if (!empty($filters['divisi'])) {
            $builder->where('karyawan.divisi', $filters['divisi']);
        }
        
        if (!empty($filters['jabatan'])) {
            $builder->where('karyawan.jabatan', $filters['jabatan']);
        }
        
        if (!empty($filters['tanggal_masuk_dari'])) {
            $builder->where('karyawan.tanggal_masuk >=', $filters['tanggal_masuk_dari']);
        }
        
        if (!empty($filters['tanggal_masuk_sampai'])) {
            $builder->where('karyawan.tanggal_masuk <=', $filters['tanggal_masuk_sampai']);
        }
        
        // Filter karyawan yang memiliki data penggajian
        if (!empty($filters['memiliki_penggajian'])) {
            if ($filters['memiliki_penggajian'] === 'ya') {
                $builder->whereExists(function($builder) {
                    $builder->select('1')
                        ->from('penggajian_perhitungan')
                        ->where('penggajian_perhitungan.karyawan_id = karyawan.id');
                });
            } elseif ($filters['memiliki_penggajian'] === 'tidak') {
                $builder->whereNotExists(function($builder) {
                    $builder->select('1')
                        ->from('penggajian_perhitungan')
                        ->where('penggajian_perhitungan.karyawan_id = karyawan.id');
                });
            }
        }
        
        // Filter karyawan yang memiliki akun user
        if (!empty($filters['memiliki_akun'])) {
            if ($filters['memiliki_akun'] === 'ya') {
                $builder->where('users.id IS NOT NULL');
            } elseif ($filters['memiliki_akun'] === 'tidak') {
                $builder->where('users.id IS NULL');
            }
        }
        
        $builder->orderBy('karyawan.status_karyawan', 'ASC')
                ->orderBy('karyawan.nama_lengkap', 'ASC');
        
        // Get total for pagination
        $total = $builder->countAllResults(false);
        
        // Get paginated results
        $offset = ($page - 1) * $perPage;
        $karyawan = $builder->limit($perPage, $offset)->findAll();
        
        return [
            'data' => $karyawan,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Get karyawan by ID dengan detail lengkap untuk penggajian
     */
    public function getKaryawanDetail($id)
    {
        $karyawan = $this->select('
                karyawan.*,
                users.id as user_id,
                users.username,
                users.email as user_email,
                users.role,
                users.status as user_status,
                users.last_login,
                (SELECT COUNT(*) FROM kontrak WHERE kontrak.karyawan_id = karyawan.id) as total_kontrak,
                (SELECT COUNT(*) FROM kontrak WHERE kontrak.karyawan_id = karyawan.id AND kontrak.status = "Aktif") as kontrak_aktif
            ')
            ->join('users', 'users.karyawan_id = karyawan.id', 'left')
            ->where('karyawan.id', $id)
            ->first();
        
        if (!$karyawan) {
            return null;
        }
        
        // Hitung masa kerja
        if (!empty($karyawan['tanggal_masuk'])) {
            $tanggalMasuk = new \DateTime($karyawan['tanggal_masuk']);
            $sekarang = new \DateTime();
            $selisih = $tanggalMasuk->diff($sekarang);
            
            $karyawan['masa_kerja_tahun'] = $selisih->y;
            $karyawan['masa_kerja_bulan'] = $selisih->m;
            $karyawan['masa_kerja_hari'] = $selisih->d;
            $karyawan['masa_kerja_string'] = $selisih->y . ' tahun ' . $selisih->m . ' bulan';
        } else {
            $karyawan['masa_kerja_tahun'] = 0;
            $karyawan['masa_kerja_bulan'] = 0;
            $karyawan['masa_kerja_hari'] = 0;
            $karyawan['masa_kerja_string'] = '-';
        }
        
        return $karyawan;
    }

    /**
     * Get karyawan aktif untuk dropdown (yang bisa digaji)
     */
    public function getKaryawanAktifForDropdown()
    {
        return $this->select('id, nik, nama_lengkap, jabatan, departemen')
            ->whereIn('status_karyawan', ['Tetap', 'Kontrak', 'Probation'])
            ->where('deleted_at IS NULL')
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();
    }

    /**
     * Get karyawan dengan informasi bank untuk pembayaran
     */
    public function getKaryawanWithBankInfo($id = null)
    {
        $builder = $this->select('
                id,
                nik,
                nama_lengkap,
                jabatan,
                departemen,
                bank,
                no_rekening,
                nama_rekening,
                status_karyawan
            ')
            ->where('deleted_at IS NULL')
            ->whereIn('status_karyawan', ['Tetap', 'Kontrak', 'Probation']);
        
        if ($id) {
            $builder->where('id', $id);
            return $builder->first();
        }
        
        return $builder->orderBy('nama_lengkap', 'ASC')->findAll();
    }

    /**
     * Get karyawan yang belum memiliki data bank lengkap
     */
    public function getKaryawanWithoutBankInfo()
    {
        return $this->select('id, nik, nama_lengkap, jabatan, departemen')
            ->where('deleted_at IS NULL')
            ->whereIn('status_karyawan', ['Tetap', 'Kontrak', 'Probation'])
            ->groupStart()
                ->where('bank IS NULL')
                ->orWhere('bank', '')
                ->orWhere('no_rekening IS NULL')
                ->orWhere('no_rekening', '')
                ->orWhere('nama_rekening IS NULL')
                ->orWhere('nama_rekening', '')
            ->groupEnd()
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();
    }

    /**
     * Get statistik karyawan untuk dashboard penggajian
     */
    public function getStatistikKaryawan()
    {
        $result = $this->select("
                COUNT(*) as total_karyawan,
                SUM(CASE WHEN status_karyawan IN ('Tetap', 'Kontrak', 'Probation') THEN 1 ELSE 0 END) as karyawan_aktif,
                SUM(CASE WHEN status_karyawan = 'Tetap' THEN 1 ELSE 0 END) as karyawan_tetap,
                SUM(CASE WHEN status_karyawan = 'Kontrak' THEN 1 ELSE 0 END) as karyawan_kontrak,
                SUM(CASE WHEN status_karyawan = 'Probation' THEN 1 ELSE 0 END) as karyawan_probation,
                SUM(CASE WHEN status_karyawan = 'Magang' THEN 1 ELSE 0 END) as karyawan_magang,
                SUM(CASE WHEN tanggal_keluar IS NOT NULL THEN 1 ELSE 0 END) as karyawan_keluar,
                SUM(CASE WHEN bank IS NULL OR bank = '' OR no_rekening IS NULL OR no_rekening = '' THEN 1 ELSE 0 END) as karyawan_tanpa_rekening,
                SUM(CASE WHEN no_npwp IS NULL OR no_npwp = '' THEN 1 ELSE 0 END) as karyawan_tanpa_npwp,
                SUM(CASE WHEN no_bpjs_kes IS NULL OR no_bpjs_kes = '' THEN 1 ELSE 0 END) as karyawan_tanpa_bpjs_kes,
                SUM(CASE WHEN no_bpjs_tk IS NULL OR no_bpjs_tk = '' THEN 1 ELSE 0 END) as karyawan_tanpa_bpjs_tk
            ")
            ->where('deleted_at IS NULL')
            ->first();
        
        if (!$result) {
            return [
                'total_karyawan' => 0,
                'karyawan_aktif' => 0,
                'karyawan_tetap' => 0,
                'karyawan_kontrak' => 0,
                'karyawan_probation' => 0,
                'karyawan_magang' => 0,
                'karyawan_keluar' => 0,
                'karyawan_tanpa_rekening' => 0,
                'karyawan_tanpa_npwp' => 0,
                'karyawan_tanpa_bpjs_kes' => 0,
                'karyawan_tanpa_bpjs_tk' => 0
            ];
        }
        
        return $result;
    }

    /**
     * Get distribusi karyawan per departemen
     */
    public function getDistribusiPerDepartemen()
    {
        return $this->select('
                COALESCE(departemen, "Tidak Ada Departemen") as departemen,
                COUNT(*) as jumlah,
                SUM(CASE WHEN status_karyawan IN ("Tetap", "Kontrak", "Probation") THEN 1 ELSE 0 END) as aktif,
                SUM(CASE WHEN status_karyawan = "Magang" THEN 1 ELSE 0 END) as magang,
                SUM(CASE WHEN tanggal_keluar IS NOT NULL THEN 1 ELSE 0 END) as keluar
            ')
            ->where('deleted_at IS NULL')
            ->groupBy('departemen')
            ->orderBy('jumlah', 'DESC')
            ->findAll();
    }

    /**
     * Get distribusi karyawan per jabatan
     */
    public function getDistribusiPerJabatan()
    {
        return $this->select('
                COALESCE(jabatan, "Tidak Ada Jabatan") as jabatan,
                COUNT(*) as jumlah
            ')
            ->where('deleted_at IS NULL')
            ->whereIn('status_karyawan', ['Tetap', 'Kontrak', 'Probation'])
            ->groupBy('jabatan')
            ->orderBy('jumlah', 'DESC')
            ->findAll();
    }

    /**
     * Get data untuk export Excel
     */
    public function getExportData($filters = [])
    {
        $builder = $this->select('
                karyawan.nik,
                karyawan.nama_lengkap,
                karyawan.nama_panggilan,
                karyawan.jenis_kelamin,
                karyawan.tempat_lahir,
                karyawan.tanggal_lahir,
                karyawan.agama,
                karyawan.status_pernikahan,
                karyawan.alamat,
                karyawan.telepon,
                karyawan.email,
                karyawan.jabatan,
                karyawan.departemen,
                karyawan.divisi,
                karyawan.tanggal_masuk,
                karyawan.status_karyawan,
                karyawan.tanggal_keluar,
                karyawan.alasan_keluar,
                karyawan.no_npwp,
                karyawan.no_bpjs_kes,
                karyawan.no_bpjs_tk,
                karyawan.bank,
                karyawan.no_rekening,
                karyawan.nama_rekening,
                karyawan.pendidikan_terakhir,
                karyawan.jurusan,
                karyawan.institusi,
                karyawan.tahun_lulus,
                karyawan.kontak_darurat_nama,
                karyawan.kontak_darurat_hubungan,
                karyawan.kontak_darurat_telepon,
                users.username,
                users.email as user_email,
                users.role,
                users.status as user_status
            ')
            ->join('users', 'users.karyawan_id = karyawan.id', 'left')
            ->where('karyawan.deleted_at IS NULL');
        
        // Apply filters
        if (!empty($filters['status_karyawan'])) {
            if ($filters['status_karyawan'] === 'aktif') {
                $builder->whereIn('karyawan.status_karyawan', ['Tetap', 'Kontrak', 'Probation']);
            } elseif ($filters['status_karyawan'] === 'nonaktif') {
                $builder->where('karyawan.status_karyawan', 'Magang');
            } elseif ($filters['status_karyawan'] === 'keluar') {
                $builder->where('karyawan.tanggal_keluar IS NOT NULL');
            } else {
                $builder->where('karyawan.status_karyawan', $filters['status_karyawan']);
            }
        }
        
        if (!empty($filters['departemen'])) {
            $builder->where('karyawan.departemen', $filters['departemen']);
        }
        
        $builder->orderBy('karyawan.status_karyawan', 'ASC')
                ->orderBy('karyawan.nama_lengkap', 'ASC');
        
        $karyawan = $builder->findAll();
        
        // Format untuk export
        $exportData = [];
        foreach ($karyawan as $item) {
            $exportData[] = [
                'NIK' => $item['nik'],
                'Nama Lengkap' => $item['nama_lengkap'],
                'Nama Panggilan' => $item['nama_panggilan'],
                'Jenis Kelamin' => $item['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan',
                'Tempat Lahir' => $item['tempat_lahir'],
                'Tanggal Lahir' => $item['tanggal_lahir'],
                'Agama' => $item['agama'],
                'Status Pernikahan' => $item['status_pernikahan'],
                'Alamat' => $item['alamat'],
                'Telepon' => $item['telepon'],
                'Email' => $item['email'],
                'Jabatan' => $item['jabatan'],
                'Departemen' => $item['departemen'],
                'Divisi' => $item['divisi'],
                'Tanggal Masuk' => $item['tanggal_masuk'],
                'Status Karyawan' => $item['status_karyawan'],
                'Tanggal Keluar' => $item['tanggal_keluar'],
                'Alasan Keluar' => $item['alasan_keluar'],
                'NPWP' => $item['no_npwp'],
                'BPJS Kesehatan' => $item['no_bpjs_kes'],
                'BPJS Ketenagakerjaan' => $item['no_bpjs_tk'],
                'Bank' => $item['bank'],
                'No Rekening' => $item['no_rekening'],
                'Nama Rekening' => $item['nama_rekening'],
                'Pendidikan Terakhir' => $item['pendidikan_terakhir'],
                'Jurusan' => $item['jurusan'],
                'Institusi' => $item['institusi'],
                'Tahun Lulus' => $item['tahun_lulus'],
                'Kontak Darurat - Nama' => $item['kontak_darurat_nama'],
                'Kontak Darurat - Hubungan' => $item['kontak_darurat_hubungan'],
                'Kontak Darurat - Telepon' => $item['kontak_darurat_telepon'],
                'Username' => $item['username'],
                'Email User' => $item['user_email'],
                'Role' => $item['role'],
                'Status User' => $item['user_status']
            ];
        }
        
        return $exportData;
    }

    /**
     * Update status karyawan (aktif/nonaktif/keluar)
     */
    public function updateStatusKaryawan($id, $status, $data = [])
    {
        $updateData = ['status_karyawan' => $status];
        
        if ($status === 'Keluar' && isset($data['tanggal_keluar'])) {
            $updateData['tanggal_keluar'] = $data['tanggal_keluar'];
            $updateData['alasan_keluar'] = $data['alasan_keluar'] ?? null;
        }
        
        return $this->update($id, $updateData);
    }

    /**
     * Cek apakah karyawan aktif (bisa digaji)
     */
    public function isKaryawanAktif($id)
    {
        $karyawan = $this->select('status_karyawan, tanggal_keluar')
            ->find($id);
        
        if (!$karyawan) {
            return false;
        }
        
        return in_array($karyawan['status_karyawan'], ['Tetap', 'Kontrak', 'Probation']) 
            && empty($karyawan['tanggal_keluar']);
    }

    /**
     * Get opsi departemen untuk filter
     */
    public function getDepartemenOptions()
    {
        return $this->select('DISTINCT departemen')
            ->where('departemen IS NOT NULL')
            ->where('departemen !=', '')
            ->where('deleted_at IS NULL')
            ->orderBy('departemen', 'ASC')
            ->findAll();
    }

    /**
     * Get opsi jabatan untuk filter
     */
    public function getJabatanOptions()
    {
        return $this->select('DISTINCT jabatan')
            ->where('jabatan IS NOT NULL')
            ->where('jabatan !=', '')
            ->where('deleted_at IS NULL')
            ->orderBy('jabatan', 'ASC')
            ->findAll();
    }

    /**
     * Get opsi divisi untuk filter
     */
    public function getDivisiOptions()
    {
        return $this->select('DISTINCT divisi')
            ->where('divisi IS NOT NULL')
            ->where('divisi !=', '')
            ->where('deleted_at IS NULL')
            ->orderBy('divisi', 'ASC')
            ->findAll();
    }

    /**
     * Get opsi status karyawan untuk filter
     */
    public function getStatusKaryawanOptions()
    {
        return [
            ['value' => 'aktif', 'label' => 'Karyawan Aktif (Tetap/Kontrak/Probation)'],
            ['value' => 'Tetap', 'label' => 'Tetap'],
            ['value' => 'Kontrak', 'label' => 'Kontrak'],
            ['value' => 'Probation', 'label' => 'Probation'],
            ['value' => 'Magang', 'label' => 'Magang'],
            ['value' => 'keluar', 'label' => 'Sudah Keluar']
        ];
    }

    /**
     * Get data untuk chart perekrutan per tahun
     */
    public function getRekrutmenPerTahun($tahun = null)
    {
        if (!$tahun) {
            $tahun = date('Y');
        }
        
        $builder = $this->select("
                MONTH(tanggal_masuk) as bulan,
                COUNT(*) as jumlah
            ")
            ->where('YEAR(tanggal_masuk)', $tahun)
            ->where('deleted_at IS NULL')
            ->groupBy('MONTH(tanggal_masuk)')
            ->orderBy('bulan', 'ASC');
        
        $result = $builder->findAll();
        
        // Format untuk 12 bulan
        $data = array_fill(1, 12, 0);
        foreach ($result as $item) {
            $data[(int)$item['bulan']] = (int)$item['jumlah'];
        }
        
        return $data;
    }
}