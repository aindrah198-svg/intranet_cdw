<?php
namespace App\Models\Accounting\Penggajian;

use CodeIgniter\Model;

class PerhitunganGajiModel extends Model
{
    protected $table = 'penggajian_perhitungan';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'nomor_perhitungan',
        'karyawan_id',
        'periode_bulan',
        'periode_tahun',
        'tanggal_perhitungan',
        
        // Komponen Gaji Pokok
        'gaji_pokok',
        'tunjangan_jabatan',
        'tunjangan_bpjs',
        'tunjangan_makan',
        'tunjangan_transport',
        'tunjangan_lainnya',
        'total_pendapatan',
        
        // Komponen Potongan
        'potongan_bpjs_kes',
        'potongan_bpjs_tk',
        'potongan_pph21',
        'potongan_absensi',
        'potongan_kasbon',
        'potongan_lainnya',
        'total_potongan',
        
        // Data Pendukung
        'total_hari_kerja',
        'total_hadir',
        'total_izin',
        'total_sakit',
        'total_cuti',
        'total_alpha',
        'total_terlambat',
        'jam_lembur',
        'upah_lembur',
        
        'gaji_bersih',
        'status',
        'catatan',
        'disetujui_oleh',
        'disetujui_at',
        'created_by',
        'updated_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'karyawan_id' => 'required|numeric',
        'periode_bulan' => 'required|numeric|min_length[1]|max_length[2]|in_list[1,2,3,4,5,6,7,8,9,10,11,12]',
        'periode_tahun' => 'required|numeric|min_length[4]|max_length[4]',
        'tanggal_perhitungan' => 'required|valid_date',
        'gaji_pokok' => 'permit_empty|numeric|greater_than_equal_to[0]',
        'total_pendapatan' => 'permit_empty|numeric|greater_than_equal_to[0]',
        'total_potongan' => 'permit_empty|numeric|greater_than_equal_to[0]',
        'gaji_bersih' => 'permit_empty|numeric|greater_than_equal_to[0]',
        'status' => 'permit_empty|in_list[Draft,Dihitung,Disetujui,Ditolak]'
    ];

    protected $validationMessages = [
        'karyawan_id' => [
            'required' => 'Karyawan harus dipilih'
        ],
        'periode_bulan' => [
            'required' => 'Periode bulan harus diisi',
            'in_list' => 'Periode bulan tidak valid'
        ],
        'periode_tahun' => [
            'required' => 'Periode tahun harus diisi'
        ],
        'tanggal_perhitungan' => [
            'required' => 'Tanggal perhitungan harus diisi'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateNomorPerhitungan', 'setDefaultValues', 'setCreatedBy'];
    protected $beforeUpdate = ['validateStatusChange', 'setUpdatedBy'];
    protected $afterFind = ['formatAngka'];

    /**
     * Generate nomor perhitungan otomatis
     * Format: PGH-YYYYMM-XXXX
     */
    protected function generateNomorPerhitungan(array $data)
    {
        if (empty($data['data']['nomor_perhitungan'])) {
            $periodeTahun = $data['data']['periode_tahun'] ?? date('Y');
            $periodeBulan = str_pad($data['data']['periode_bulan'] ?? date('m'), 2, '0', STR_PAD_LEFT);
            $prefix = 'PGH-' . $periodeTahun . $periodeBulan . '-';
            
            // Cari nomor perhitungan terakhir untuk periode ini
            $lastPerhitungan = $this->select('nomor_perhitungan')
                ->like('nomor_perhitungan', $prefix, 'after')
                ->orderBy('nomor_perhitungan', 'DESC')
                ->first();
            
            if ($lastPerhitungan) {
                $lastNum = substr($lastPerhitungan['nomor_perhitungan'], strlen($prefix));
                $nextNum = str_pad((int)$lastNum + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNum = '0001';
            }
            
            $data['data']['nomor_perhitungan'] = $prefix . $nextNum;
        }
        
        return $data;
    }

    /**
     * Set default values
     */
    protected function setDefaultValues(array $data)
    {
        // Set status default
        if (!isset($data['data']['status']) || empty($data['data']['status'])) {
            $data['data']['status'] = 'Draft';
        }
        
        // Set default 0 untuk field numerik
        $numericFields = [
            'gaji_pokok', 'tunjangan_jabatan', 'tunjangan_bpjs', 'tunjangan_makan',
            'tunjangan_transport', 'tunjangan_lainnya', 'total_pendapatan',
            'potongan_bpjs_kes', 'potongan_bpjs_tk', 'potongan_pph21',
            'potongan_absensi', 'potongan_kasbon', 'potongan_lainnya', 'total_potongan',
            'total_hari_kerja', 'total_hadir', 'total_izin', 'total_sakit',
            'total_cuti', 'total_alpha', 'total_terlambat', 'jam_lembur', 'upah_lembur',
            'gaji_bersih'
        ];
        
        foreach ($numericFields as $field) {
            if (!isset($data['data'][$field]) || $data['data'][$field] === '') {
                $data['data'][$field] = 0;
            }
        }
        
        return $data;
    }

    /**
     * Set created by
     */
    protected function setCreatedBy(array $data)
    {
        $data['data']['created_by'] = session()->get('user_id');
        return $data;
    }

    /**
     * Set updated by
     */
    protected function setUpdatedBy(array $data)
    {
        $data['data']['updated_by'] = session()->get('user_id');
        return $data;
    }

    /**
     * Validasi perubahan status
     */
    protected function validateStatusChange(array $data)
    {
        if (isset($data['data']['status'])) {
            $id = $data['id'][0] ?? null;
            
            if ($id) {
                $current = $this->find($id);
                
                // Validasi perubahan dari Draft ke Dihitung
                if ($data['data']['status'] === 'Dihitung' && $current['status'] === 'Draft') {
                    // Pastikan total pendapatan dan potongan sudah dihitung
                    if (empty($current['total_pendapatan']) || $current['total_pendapatan'] == 0) {
                        throw new \RuntimeException('Total pendapatan belum dihitung');
                    }
                }
                
                // Validasi perubahan dari Dihitung ke Disetujui
                if ($data['data']['status'] === 'Disetujui' && $current['status'] === 'Dihitung') {
                    // Pastikan ada yang menyetujui
                    if (empty($data['data']['disetujui_oleh'])) {
                        throw new \RuntimeException('Pihak yang menyetujui harus diisi');
                    }
                    
                    // Set waktu persetujuan
                    $data['data']['disetujui_at'] = date('Y-m-d H:i:s');
                }
                
                // Cegah perubahan dari Disetujui ke status lain (kecuali admin)
                if ($current['status'] === 'Disetujui' && $data['data']['status'] !== 'Disetujui') {
                    // Cek role user (hanya admin/direktur yang bisa)
                    $userRole = session()->get('role');
                    if (!in_array($userRole, ['admin', 'direktur'])) {
                        throw new \RuntimeException('Perhitungan yang sudah disetujui tidak dapat diubah');
                    }
                }
            }
        }
        
        return $data;
    }

    /**
     * Format angka setelah find
     */
    protected function formatAngka(array $data)
    {
        if (isset($data['data'])) {
            // Jika single result
            if (is_array($data['data']) && isset($data['data']['id'])) {
                $this->formatSingleResult($data['data']);
            }
            // Jika multiple results
            elseif (is_array($data['data']) && !empty($data['data'])) {
                foreach ($data['data'] as &$item) {
                    $this->formatSingleResult($item);
                }
            }
        }
        
        return $data;
    }

    /**
     * Format single result
     */
    private function formatSingleResult(&$item)
    {
        $numericFields = [
            'gaji_pokok', 'tunjangan_jabatan', 'tunjangan_bpjs', 'tunjangan_makan',
            'tunjangan_transport', 'tunjangan_lainnya', 'total_pendapatan',
            'potongan_bpjs_kes', 'potongan_bpjs_tk', 'potongan_pph21',
            'potongan_absensi', 'potongan_kasbon', 'potongan_lainnya', 'total_potongan',
            'jam_lembur', 'upah_lembur', 'gaji_bersih'
        ];
        
        foreach ($numericFields as $field) {
            if (isset($item[$field])) {
                $item[$field . '_formatted'] = number_format($item[$field], 0, ',', '.');
            }
        }
        
        // Format status dengan badge
        if (isset($item['status'])) {
            $badgeClass = [
                'Draft' => 'secondary',
                'Dihitung' => 'info',
                'Disetujui' => 'success',
                'Ditolak' => 'danger'
            ];
            $item['status_badge'] = '<span class="badge bg-' . ($badgeClass[$item['status']] ?? 'secondary') . '">' . $item['status'] . '</span>';
        }
    }

    /**
     * Get all perhitungan gaji dengan filter
     */
    public function getAllWithFilters($filters = [], $perPage = 20, $page = 1)
    {
        $builder = $this->select('
                penggajian_perhitungan.*,
                karyawan.nik,
                karyawan.nama_lengkap,
                karyawan.jabatan,
                karyawan.departemen,
                karyawan.divisi,
                creator.username as creator_name,
                approver.username as approver_name
            ')
            ->join('karyawan', 'karyawan.id = penggajian_perhitungan.karyawan_id')
            ->join('users as creator', 'creator.id = penggajian_perhitungan.created_by', 'left')
            ->join('users as approver', 'approver.id = penggajian_perhitungan.disetujui_oleh', 'left')
            ->where('penggajian_perhitungan.deleted_at IS NULL');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('penggajian_perhitungan.nomor_perhitungan', $search)
                ->orLike('karyawan.nik', $search)
                ->orLike('karyawan.nama_lengkap', $search)
                ->orLike('karyawan.jabatan', $search)
                ->orLike('karyawan.departemen', $search)
                ->groupEnd();
        }
        
        if (!empty($filters['karyawan_id'])) {
            $builder->where('penggajian_perhitungan.karyawan_id', $filters['karyawan_id']);
        }
        
        if (!empty($filters['periode_bulan'])) {
            $builder->where('penggajian_perhitungan.periode_bulan', $filters['periode_bulan']);
        }
        
        if (!empty($filters['periode_tahun'])) {
            $builder->where('penggajian_perhitungan.periode_tahun', $filters['periode_tahun']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('penggajian_perhitungan.status', $filters['status']);
        }
        
        if (!empty($filters['tanggal_mulai'])) {
            $builder->where('penggajian_perhitungan.tanggal_perhitungan >=', $filters['tanggal_mulai']);
        }
        
        if (!empty($filters['tanggal_selesai'])) {
            $builder->where('penggajian_perhitungan.tanggal_perhitungan <=', $filters['tanggal_selesai']);
        }
        
        if (!empty($filters['departemen'])) {
            $builder->where('karyawan.departemen', $filters['departemen']);
        }
        
        if (!empty($filters['gaji_min'])) {
            $builder->where('penggajian_perhitungan.gaji_bersih >=', $filters['gaji_min']);
        }
        
        if (!empty($filters['gaji_max'])) {
            $builder->where('penggajian_perhitungan.gaji_bersih <=', $filters['gaji_max']);
        }
        
        // Filter by approval status
        if (!empty($filters['approval_status'])) {
            if ($filters['approval_status'] === 'pending') {
                $builder->whereIn('penggajian_perhitungan.status', ['Draft', 'Dihitung']);
            } elseif ($filters['approval_status'] === 'approved') {
                $builder->where('penggajian_perhitungan.status', 'Disetujui');
            } elseif ($filters['approval_status'] === 'rejected') {
                $builder->where('penggajian_perhitungan.status', 'Ditolak');
            }
        }
        
        $builder->orderBy('penggajian_perhitungan.periode_tahun', 'DESC')
                ->orderBy('penggajian_perhitungan.periode_bulan', 'DESC')
                ->orderBy('karyawan.nama_lengkap', 'ASC');
        
        // Get total for pagination
        $total = $builder->countAllResults(false);
        
        // Get paginated results
        $offset = ($page - 1) * $perPage;
        $data = $builder->limit($perPage, $offset)->findAll();
        
        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Get perhitungan gaji by ID dengan detail lengkap
     */
    public function getDetail($id)
    {
        $data = $this->select('
                penggajian_perhitungan.*,
                karyawan.nik,
                karyawan.nama_lengkap,
                karyawan.nama_panggilan,
                karyawan.jenis_kelamin,
                karyawan.tempat_lahir,
                karyawan.tanggal_lahir,
                karyawan.alamat,
                karyawan.telepon,
                karyawan.email,
                karyawan.jabatan,
                karyawan.departemen,
                karyawan.divisi,
                karyawan.tanggal_masuk,
                karyawan.status_karyawan,
                karyawan.no_npwp,
                karyawan.no_bpjs_kes,
                karyawan.no_bpjs_tk,
                karyawan.bank,
                karyawan.no_rekening,
                karyawan.nama_rekening,
                creator.username as creator_name,
                approver.username as approver_name
            ')
            ->join('karyawan', 'karyawan.id = penggajian_perhitungan.karyawan_id')
            ->join('users as creator', 'creator.id = penggajian_perhitungan.created_by', 'left')
            ->join('users as approver', 'approver.id = penggajian_perhitungan.disetujui_oleh', 'left')
            ->where('penggajian_perhitungan.id', $id)
            ->first();
        
        if (!$data) {
            return null;
        }
        
        // Hitung ulang gaji bersih untuk memastikan
        $data['gaji_bersih'] = $data['total_pendapatan'] - $data['total_potongan'];
        
        return $data;
    }

    /**
     * Hitung gaji untuk seorang karyawan
     */
    public function hitungGaji($karyawanId, $periodeBulan, $periodeTahun, $dataAbsensi = null, $dataKasbon = null)
    {
        // Ambil data karyawan
        $karyawanModel = new DataKaryawanModel();
        $karyawan = $karyawanModel->find($karyawanId);
        
        if (!$karyawan) {
            throw new \RuntimeException('Data karyawan tidak ditemukan');
        }
        
        // Cek apakah sudah ada perhitungan untuk periode ini
        $existing = $this->where('karyawan_id', $karyawanId)
            ->where('periode_bulan', $periodeBulan)
            ->where('periode_tahun', $periodeTahun)
            ->first();
        
        if ($existing) {
            throw new \RuntimeException('Perhitungan gaji untuk karyawan ini pada periode tersebut sudah ada');
        }
        
        // Ambil data absensi jika tidak disediakan
        if (!$dataAbsensi) {
            $dataAbsensi = $this->getDataAbsensi($karyawanId, $periodeBulan, $periodeTahun);
        }
        
        // Ambil data kasbon jika tidak disediakan
        if (!$dataKasbon) {
            $dataKasbon = $this->getDataKasbon($karyawanId, $periodeBulan, $periodeTahun);
        }
        
        // Ambil data kontrak aktif
        $kontrak = $this->getKontrakAktif($karyawanId);
        
        // Hitung komponen gaji
        $gajiPokok = $karyawan['gaji_pokok'] ?? 0;
        $tunjanganJabatan = $this->hitungTunjanganJabatan($karyawan, $kontrak);
        $tunjanganMakan = $this->hitungTunjanganMakan($karyawan, $dataAbsensi, $kontrak);
        $tunjanganTransport = $this->hitungTunjanganTransport($karyawan, $dataAbsensi, $kontrak);
        $tunjanganBPJS = $this->hitungTunjanganBPJS($karyawan, $gajiPokok);
        
        // Hitung potongan
        $potonganBPJSKes = $this->hitungPotonganBPJSKes($karyawan, $gajiPokok);
        $potonganBPJSTK = $this->hitungPotonganBPJSTK($karyawan, $gajiPokok);
        $potonganAbsensi = $this->hitungPotonganAbsensi($karyawan, $dataAbsensi);
        $potonganKasbon = $this->hitungPotonganKasbon($dataKasbon);
        $potonganPPH21 = $this->hitungPotonganPPH21($karyawan, $gajiPokok + $tunjanganJabatan + $tunjanganMakan + $tunjanganTransport);
        
        // Hitung lembur
        $jamLembur = $dataAbsensi['total_jam_lembur'] ?? 0;
        $upahLembur = $this->hitungUpahLembur($karyawan, $jamLembur, $gajiPokok);
        
        // Hitung total
        $totalPendapatan = $gajiPokok + $tunjanganJabatan + $tunjanganBPJS + $tunjanganMakan + 
                          $tunjanganTransport + $upahLembur;
        
        $totalPotongan = $potonganBPJSKes + $potonganBPJSTK + $potonganPPH21 + 
                        $potonganAbsensi + $potonganKasbon;
        
        $gajiBersih = $totalPendapatan - $totalPotongan;
        
        return [
            'karyawan_id' => $karyawanId,
            'periode_bulan' => $periodeBulan,
            'periode_tahun' => $periodeTahun,
            'tanggal_perhitungan' => date('Y-m-d'),
            
            // Komponen Gaji
            'gaji_pokok' => $gajiPokok,
            'tunjangan_jabatan' => $tunjanganJabatan,
            'tunjangan_bpjs' => $tunjanganBPJS,
            'tunjangan_makan' => $tunjanganMakan,
            'tunjangan_transport' => $tunjanganTransport,
            'tunjangan_lainnya' => 0,
            'total_pendapatan' => $totalPendapatan,
            
            // Potongan
            'potongan_bpjs_kes' => $potonganBPJSKes,
            'potongan_bpjs_tk' => $potonganBPJSTK,
            'potongan_pph21' => $potonganPPH21,
            'potongan_absensi' => $potonganAbsensi,
            'potongan_kasbon' => $potonganKasbon,
            'potongan_lainnya' => 0,
            'total_potongan' => $totalPotongan,
            
            // Data Absensi
            'total_hari_kerja' => $dataAbsensi['total_hari_kerja'] ?? 0,
            'total_hadir' => $dataAbsensi['total_hadir'] ?? 0,
            'total_izin' => $dataAbsensi['total_izin'] ?? 0,
            'total_sakit' => $dataAbsensi['total_sakit'] ?? 0,
            'total_cuti' => $dataAbsensi['total_cuti'] ?? 0,
            'total_alpha' => $dataAbsensi['total_alpha'] ?? 0,
            'total_terlambat' => $dataAbsensi['total_terlambat'] ?? 0,
            'jam_lembur' => $jamLembur,
            'upah_lembur' => $upahLembur,
            
            'gaji_bersih' => $gajiBersih,
            'status' => 'Dihitung'
        ];
    }

    /**
     * Get data absensi untuk perhitungan gaji
     */
    private function getDataAbsensi($karyawanId, $bulan, $tahun)
    {
        $db = \Config\Database::connect();
        
        $tanggalAwal = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-01';
        $tanggalAkhir = date('Y-m-t', strtotime($tanggalAwal));
        
        $builder = $db->table('absensi');
        $builder->select("
                COUNT(*) as total_hari_kerja,
                SUM(CASE WHEN status = 'Hadir' THEN 1 ELSE 0 END) as total_hadir,
                SUM(CASE WHEN status = 'Izin' THEN 1 ELSE 0 END) as total_izin,
                SUM(CASE WHEN status = 'Sakit' THEN 1 ELSE 0 END) as total_sakit,
                SUM(CASE WHEN status = 'Cuti' THEN 1 ELSE 0 END) as total_cuti,
                SUM(CASE WHEN status = 'Alpha' OR status IS NULL THEN 1 ELSE 0 END) as total_alpha,
                SUM(CASE WHEN terlambat > 0 THEN 1 ELSE 0 END) as total_terlambat,
                SUM(jam_lembur) as total_jam_lembur
            ")
            ->where('karyawan_id', $karyawanId)
            ->where('tanggal >=', $tanggalAwal)
            ->where('tanggal <=', $tanggalAkhir);
        
        $result = $builder->get()->getRowArray();
        
        return $result ?: [
            'total_hari_kerja' => 0,
            'total_hadir' => 0,
            'total_izin' => 0,
            'total_sakit' => 0,
            'total_cuti' => 0,
            'total_alpha' => 0,
            'total_terlambat' => 0,
            'total_jam_lembur' => 0
        ];
    }

    /**
     * Get data kasbon untuk perhitungan gaji
     */
    private function getDataKasbon($karyawanId, $bulan, $tahun)
    {
        $db = \Config\Database::connect();
        
        $tanggalAwal = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-01';
        $tanggalAkhir = date('Y-m-t', strtotime($tanggalAwal));
        
        $builder = $db->table('pengeluaran_pribadi');
        $builder->select("
                COALESCE(SUM(jumlah - jumlah_dibayar), 0) as total_kasbon
            ")
            ->where('karyawan_id', $karyawanId)
            ->where('jenis', 'Kasbon')
            ->where('status_hutang', 'Belum Dibayar');
        
        $result = $builder->get()->getRowArray();
        
        return $result ? $result['total_kasbon'] : 0;
    }

    /**
     * Get kontrak aktif karyawan
     */
    private function getKontrakAktif($karyawanId)
    {
        $db = \Config\Database::connect();
        
        $builder = $db->table('kontrak');
        $builder->select('*')
            ->where('karyawan_id', $karyawanId)
            ->where('status', 'Aktif')
            ->orderBy('tanggal_mulai', 'DESC')
            ->limit(1);
        
        return $builder->get()->getRowArray();
    }

    /**
     * Hitung tunjangan jabatan
     */
    private function hitungTunjanganJabatan($karyawan, $kontrak)
    {
        // Logika sesuai kebijakan perusahaan
        $tunjangan = 0;
        
        if (!empty($kontrak) && isset($kontrak['tunjangan_jabatan'])) {
            $tunjangan = $kontrak['tunjangan_jabatan'];
        } else {
            // Default berdasarkan jabatan
            switch ($karyawan['jabatan']) {
                case 'Direktur Utama':
                case 'Manager':
                    $tunjangan = 1000000;
                    break;
                case 'Supervisor':
                    $tunjangan = 500000;
                    break;
                default:
                    $tunjangan = 0;
            }
        }
        
        return $tunjangan;
    }

    /**
     * Hitung tunjangan makan
     */
    private function hitungTunjanganMakan($karyawan, $dataAbsensi, $kontrak)
    {
        $perHari = 25000; // Default Rp 25.000/hari
        $jumlahHadir = $dataAbsensi['total_hadir'] ?? 0;
        
        if (!empty($kontrak) && isset($kontrak['tunjangan_makan'])) {
            $perHari = $kontrak['tunjangan_makan'];
        }
        
        return $perHari * $jumlahHadir;
    }

    /**
     * Hitung tunjangan transport
     */
    private function hitungTunjanganTransport($karyawan, $dataAbsensi, $kontrak)
    {
        $perHari = 20000; // Default Rp 20.000/hari
        $jumlahHadir = $dataAbsensi['total_hadir'] ?? 0;
        
        if (!empty($kontrak) && isset($kontrak['tunjangan_transport'])) {
            $perHari = $kontrak['tunjangan_transport'];
        }
        
        return $perHari * $jumlahHadir;
    }

    /**
     * Hitung tunjangan BPJS (yang dibayar perusahaan)
     */
    private function hitungTunjanganBPJS($karyawan, $gajiPokok)
    {
        // BPJS Kesehatan 4% dari gaji (dibayar perusahaan)
        // BPJS Ketenagakerjaan 3.7% dari gaji (JKK 1.74%, JKM 0.3%, JP 1%, JHT 3.7% perusahaan)
        $bpjsKes = $gajiPokok * 0.04;
        $bpjsTK = $gajiPokok * 0.037;
        
        return $bpjsKes + $bpjsTK;
    }

    /**
     * Hitung potongan BPJS Kesehatan (dari karyawan)
     */
    private function hitungPotonganBPJSKes($karyawan, $gajiPokok)
    {
        // BPJS Kesehatan 1% dari gaji (dipotong dari karyawan)
        return $gajiPokok * 0.01;
    }

    /**
     * Hitung potongan BPJS Ketenagakerjaan (dari karyawan)
     */
    private function hitungPotonganBPJSTK($karyawan, $gajiPokok)
    {
        // JHT 2% dari gaji (dipotong dari karyawan)
        return $gajiPokok * 0.02;
    }

    /**
     * Hitung potongan absensi
     */
    private function hitungPotonganAbsensi($karyawan, $dataAbsensi)
    {
        $potongan = 0;
        $gajiPerHari = ($karyawan['gaji_pokok'] ?? 0) / 25; // Asumsi 25 hari kerja
        
        // Potongan untuk alpha
        $potongan += ($dataAbsensi['total_alpha'] ?? 0) * $gajiPerHari;
        
        // Potongan untuk terlambat (misal: potongan 5% dari gaji per hari per keterlambatan)
        $potongan += ($dataAbsensi['total_terlambat'] ?? 0) * ($gajiPerHari * 0.05);
        
        return $potongan;
    }

    /**
     * Hitung potongan kasbon
     */
    private function hitungPotonganKasbon($totalKasbon)
    {
        // Maksimal potongan 30% dari gaji? Nanti dihitung setelah dapat gaji pokok
        // Sementara return 0, nanti di-set manual atau di method terpisah
        return 0;
    }

    /**
     * Hitung potongan PPH21
     */
    private function hitungPotonganPPH21($karyawan, $penghasilanBruto)
    {
        // Logika sederhana: jika punya NPWP, potong 5% dari PKP
        // Untuk implementasi riil perlu perhitungan lebih kompleks
        if (empty($karyawan['no_npwp'])) {
            return 0; // Belum punya NPWP
        }
        
        // PTKP setahun
        $status = $karyawan['status_pernikahan'] ?? 'Belum Menikah';
        $ptkp = 54000000; // TK/0
        if ($status == 'Menikah') {
            $ptkp = 58500000; // K/0
        }
        
        // Penghasilan setahun
        $penghasilanSetahun = $penghasilanBruto * 12;
        
        // PKP
        $pkp = max(0, $penghasilanSetahun - $ptkp);
        
        // PPh21 setahun (tarif progresif)
        $pphSetahun = 0;
        if ($pkp <= 60000000) {
            $pphSetahun = $pkp * 0.05;
        } elseif ($pkp <= 250000000) {
            $pphSetahun = 60000000 * 0.05 + ($pkp - 60000000) * 0.15;
        } else {
            $pphSetahun = 60000000 * 0.05 + 190000000 * 0.15 + ($pkp - 250000000) * 0.25;
        }
        
        // PPh21 per bulan
        return $pphSetahun / 12;
    }

    /**
     * Hitung upah lembur
     */
    private function hitungUpahLembur($karyawan, $jamLembur, $gajiPokok)
    {
        $gajiPerJam = $gajiPokok / 173; // Standar hitungan lembur
        
        // Jam pertama: 1.5 x upah per jam
        // Jam berikutnya: 2 x upah per jam
        if ($jamLembur <= 1) {
            return $jamLembur * $gajiPerJam * 1.5;
        } else {
            return (1 * $gajiPerJam * 1.5) + (($jamLembur - 1) * $gajiPerJam * 2);
        }
    }

    /**
     * Hitung massal untuk semua karyawan aktif
     */
    public function hitungMassal($periodeBulan, $periodeTahun, $karyawanIds = [])
    {
        $db = \Config\Database::connect();
        $db->transStart();
        
        $karyawanModel = new DataKaryawanModel();
        
        // Ambil karyawan yang akan dihitung
        $builder = $karyawanModel->select('id')
            ->whereIn('status_karyawan', ['Tetap', 'Kontrak', 'Probation'])
            ->where('deleted_at IS NULL');
        
        if (!empty($karyawanIds)) {
            $builder->whereIn('id', $karyawanIds);
        }
        
        $karyawanList = $builder->findAll();
        
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];
        
        foreach ($karyawanList as $karyawan) {
            try {
                // Cek apakah sudah ada perhitungan
                $existing = $this->where('karyawan_id', $karyawan['id'])
                    ->where('periode_bulan', $periodeBulan)
                    ->where('periode_tahun', $periodeTahun)
                    ->first();
                
                if ($existing) {
                    $results['failed']++;
                    $results['errors'][] = "Karyawan ID {$karyawan['id']} sudah memiliki perhitungan";
                    continue;
                }
                
                // Hitung gaji
                $dataGaji = $this->hitungGaji($karyawan['id'], $periodeBulan, $periodeTahun);
                
                // Simpan
                $this->insert($dataGaji);
                
                $results['success']++;
                
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Karyawan ID {$karyawan['id']}: " . $e->getMessage();
            }
        }
        
        $db->transComplete();
        
        return $results;
    }

    /**
     * Setujui perhitungan gaji
     */
    public function setujui($id, $userId)
    {
        $data = [
            'status' => 'Disetujui',
            'disetujui_oleh' => $userId,
            'disetujui_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->update($id, $data);
    }

    /**
     * Tolak perhitungan gaji
     */
    public function tolak($id, $userId, $catatan = null)
    {
        $data = [
            'status' => 'Ditolak',
            'catatan' => $catatan,
            'disetujui_oleh' => $userId,
            'disetujui_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->update($id, $data);
    }

    /**
     * Get ringkasan per periode
     */
    public function getRingkasanPeriode($bulan, $tahun)
    {
        $builder = $this->select("
                COUNT(*) as total_karyawan,
                SUM(gaji_pokok) as total_gaji_pokok,
                SUM(tunjangan_jabatan + tunjangan_bpjs + tunjangan_makan + tunjangan_transport + tunjangan_lainnya) as total_tunjangan,
                SUM(upah_lembur) as total_lembur,
                SUM(potongan_bpjs_kes + potongan_bpjs_tk + potongan_pph21 + potongan_absensi + potongan_kasbon + potongan_lainnya) as total_potongan,
                SUM(gaji_bersih) as total_gaji_bersih
            ")
            ->where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->where('status', 'Disetujui')
            ->first();
        
        if (!$builder) {
            return [
                'total_karyawan' => 0,
                'total_gaji_pokok' => 0,
                'total_tunjangan' => 0,
                'total_lembur' => 0,
                'total_potongan' => 0,
                'total_gaji_bersih' => 0
            ];
        }
        
        return $builder;
    }

    /**
     * Get statistik untuk dashboard
     */
    public function getStats($tahun = null)
    {
        if (!$tahun) {
            $tahun = date('Y');
        }
        
        $builder = $this->select("
                COUNT(DISTINCT CONCAT(periode_tahun, '-', periode_bulan)) as total_periode,
                COUNT(*) as total_perhitungan,
                SUM(CASE WHEN status = 'Disetujui' THEN 1 ELSE 0 END) as total_disetujui,
                SUM(CASE WHEN status = 'Draft' THEN 1 ELSE 0 END) as total_draft,
                SUM(CASE WHEN status = 'Dihitung' THEN 1 ELSE 0 END) as total_dihitung,
                SUM(CASE WHEN status = 'Ditolak' THEN 1 ELSE 0 END) as total_ditolak,
                SUM(gaji_bersih) as total_gaji
            ")
            ->where('periode_tahun', $tahun);
        
        $stats = $builder->first();
        
        if (!$stats) {
            return [
                'total_periode' => 0,
                'total_perhitungan' => 0,
                'total_disetujui' => 0,
                'total_draft' => 0,
                'total_dihitung' => 0,
                'total_ditolak' => 0,
                'total_gaji' => 0
            ];
        }
        
        return $stats;
    }

    /**
     * Get data untuk export Excel
     */
    public function getExportData($filters = [])
    {
        $builder = $this->select('
                penggajian_perhitungan.nomor_perhitungan,
                penggajian_perhitungan.periode_bulan,
                penggajian_perhitungan.periode_tahun,
                penggajian_perhitungan.tanggal_perhitungan,
                karyawan.nik,
                karyawan.nama_lengkap,
                karyawan.jabatan,
                karyawan.departemen,
                karyawan.divisi,
                karyawan.bank,
                karyawan.no_rekening,
                karyawan.nama_rekening,
                penggajian_perhitungan.gaji_pokok,
                penggajian_perhitungan.tunjangan_jabatan,
                penggajian_perhitungan.tunjangan_bpjs,
                penggajian_perhitungan.tunjangan_makan,
                penggajian_perhitungan.tunjangan_transport,
                penggajian_perhitungan.tunjangan_lainnya,
                penggajian_perhitungan.total_pendapatan,
                penggajian_perhitungan.potongan_bpjs_kes,
                penggajian_perhitungan.potongan_bpjs_tk,
                penggajian_perhitungan.potongan_pph21,
                penggajian_perhitungan.potongan_absensi,
                penggajian_perhitungan.potongan_kasbon,
                penggajian_perhitungan.potongan_lainnya,
                penggajian_perhitungan.total_potongan,
                penggajian_perhitungan.total_hari_kerja,
                penggajian_perhitungan.total_hadir,
                penggajian_perhitungan.total_izin,
                penggajian_perhitungan.total_sakit,
                penggajian_perhitungan.total_cuti,
                penggajian_perhitungan.total_alpha,
                penggajian_perhitungan.jam_lembur,
                penggajian_perhitungan.upah_lembur,
                penggajian_perhitungan.gaji_bersih,
                penggajian_perhitungan.status,
                creator.username as creator_name,
                approver.username as approver_name,
                penggajian_perhitungan.disetujui_at
            ')
            ->join('karyawan', 'karyawan.id = penggajian_perhitungan.karyawan_id')
            ->join('users as creator', 'creator.id = penggajian_perhitungan.created_by', 'left')
            ->join('users as approver', 'approver.id = penggajian_perhitungan.disetujui_oleh', 'left')
            ->where('penggajian_perhitungan.deleted_at IS NULL');
        
        // Apply filters
        if (!empty($filters['periode_bulan'])) {
            $builder->where('penggajian_perhitungan.periode_bulan', $filters['periode_bulan']);
        }
        
        if (!empty($filters['periode_tahun'])) {
            $builder->where('penggajian_perhitungan.periode_tahun', $filters['periode_tahun']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('penggajian_perhitungan.status', $filters['status']);
        }
        
        if (!empty($filters['departemen'])) {
            $builder->where('karyawan.departemen', $filters['departemen']);
        }
        
        $builder->orderBy('penggajian_perhitungan.periode_tahun', 'DESC')
                ->orderBy('penggajian_perhitungan.periode_bulan', 'DESC')
                ->orderBy('karyawan.nama_lengkap', 'ASC');
        
        $data = $builder->findAll();
        
        // Format untuk export
        $exportData = [];
        foreach ($data as $item) {
            $exportData[] = [
                'No Perhitungan' => $item['nomor_perhitungan'],
                'Periode' => $item['periode_bulan'] . '-' . $item['periode_tahun'],
                'Tanggal Hitung' => $item['tanggal_perhitungan'],
                'NIK' => $item['nik'],
                'Nama Karyawan' => $item['nama_lengkap'],
                'Jabatan' => $item['jabatan'],
                'Departemen' => $item['departemen'],
                'Bank' => $item['bank'],
                'No Rekening' => $item['no_rekening'],
                'Nama Rekening' => $item['nama_rekening'],
                'Gaji Pokok' => $item['gaji_pokok'],
                'Tunj Jabatan' => $item['tunjangan_jabatan'],
                'Tunj BPJS' => $item['tunjangan_bpjs'],
                'Tunj Makan' => $item['tunjangan_makan'],
                'Tunj Transport' => $item['tunjangan_transport'],
                'Tunj Lainnya' => $item['tunjangan_lainnya'],
                'Upah Lembur' => $item['upah_lembur'],
                'Total Pendapatan' => $item['total_pendapatan'],
                'Pot BPJS Kes' => $item['potongan_bpjs_kes'],
                'Pot BPJS TK' => $item['potongan_bpjs_tk'],
                'Pot PPH21' => $item['potongan_pph21'],
                'Pot Absensi' => $item['potongan_absensi'],
                'Pot Kasbon' => $item['potongan_kasbon'],
                'Pot Lainnya' => $item['potongan_lainnya'],
                'Total Potongan' => $item['total_potongan'],
                'Gaji Bersih' => $item['gaji_bersih'],
                'Hadir' => $item['total_hadir'],
                'Izin' => $item['total_izin'],
                'Sakit' => $item['total_sakit'],
                'Cuti' => $item['total_cuti'],
                'Alpha' => $item['total_alpha'],
                'Jam Lembur' => $item['jam_lembur'],
                'Status' => $item['status'],
                'Dibuat Oleh' => $item['creator_name'],
                'Disetujui Oleh' => $item['approver_name'],
                'Tgl Disetujui' => $item['disetujui_at']
            ];
        }
        
        return $exportData;
    }

    /**
     * Get opsi periode yang tersedia
     */
    public function getPeriodeOptions()
    {
        $builder = $this->select('DISTINCT periode_tahun, periode_bulan')
            ->orderBy('periode_tahun', 'DESC')
            ->orderBy('periode_bulan', 'DESC')
            ->findAll();
        
        $options = [];
        foreach ($builder as $item) {
            $bulan = $this->getNamaBulan($item['periode_bulan']);
            $options[] = [
                'tahun' => $item['periode_tahun'],
                'bulan' => $item['periode_bulan'],
                'label' => $bulan . ' ' . $item['periode_tahun']
            ];
        }
        
        return $options;
    }

    /**
     * Get nama bulan
     */
    private function getNamaBulan($bulan)
    {
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return $namaBulan[(int)$bulan] ?? $bulan;
    }

    /**
     * Cek apakah periode sudah diproses
     */
    public function isPeriodeProcessed($bulan, $tahun)
    {
        $count = $this->where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->countAllResults();
        
        return $count > 0;
    }

    /**
     * Get total gaji per periode untuk chart
     */
    public function getTotalGajiPerPeriode($tahun = null)
    {
        if (!$tahun) {
            $tahun = date('Y');
        }
        
        $builder = $this->select('
                periode_bulan,
                SUM(gaji_bersih) as total_gaji
            ')
            ->where('periode_tahun', $tahun)
            ->where('status', 'Disetujui')
            ->groupBy('periode_bulan')
            ->orderBy('periode_bulan', 'ASC')
            ->findAll();
        
        // Format untuk 12 bulan
        $data = array_fill(1, 12, 0);
        foreach ($builder as $item) {
            $data[(int)$item['periode_bulan']] = (float)$item['total_gaji'];
        }
        
        return $data;
    }
}