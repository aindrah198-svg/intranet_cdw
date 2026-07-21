<?php
namespace App\Models\Accounting;

use CodeIgniter\Model;

class PenggajianPerhitunganModel extends Model
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
        'gaji_pokok',
        'tunjangan_jabatan',
        'tunjangan_bpjs',
        'tunjangan_makan',
        'tunjangan_transport',
        'tunjangan_lainnya',
        'total_pendapatan',
        'potongan_bpjs_kes',
        'potongan_bpjs_tk',
        'potongan_pph21',
        'potongan_absensi',
        'potongan_kasbon',
        'potongan_lainnya',
        'total_potongan',
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
        'karyawan_id' => 'required|is_natural_no_zero',
        'periode_bulan' => 'required|in_list[1,2,3,4,5,6,7,8,9,10,11,12]',
        'periode_tahun' => 'required|numeric|min_length[4]|max_length[4]',
        'tanggal_perhitungan' => 'required|valid_date',
        'gaji_pokok' => 'permit_empty|numeric',
        'total_pendapatan' => 'permit_empty|numeric',
        'total_potongan' => 'permit_empty|numeric',
        'gaji_bersih' => 'permit_empty|numeric',
        'status' => 'permit_empty|in_list[Draft,Dihitung,Disetujui,Ditolak]'
    ];

    protected $validationMessages = [
        'karyawan_id' => [
            'required' => 'Karyawan harus dipilih',
            'is_natural_no_zero' => 'Karyawan tidak valid'
        ],
        'periode_bulan' => [
            'required' => 'Bulan periode harus dipilih'
        ],
        'periode_tahun' => [
            'required' => 'Tahun periode harus diisi',
            'numeric' => 'Tahun harus berupa angka'
        ],
        'tanggal_perhitungan' => [
            'required' => 'Tanggal perhitungan harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateNomorPerhitungan', 'setDefaultValues', 'setCreatedBy', 'hitungTotal'];
    protected $beforeUpdate = ['setUpdatedBy', 'validateStatusChange', 'hitungTotal'];

    /**
     * Generate nomor perhitungan otomatis
     * Format: PGH-YYYYMM-XXXX
     */
    protected function generateNomorPerhitungan(array $data)
    {
        if (empty($data['data']['nomor_perhitungan'])) {
            $periodeBulan = str_pad($data['data']['periode_bulan'] ?? date('m'), 2, '0', STR_PAD_LEFT);
            $periodeTahun = $data['data']['periode_tahun'] ?? date('Y');
            $prefix = 'PGH-' . $periodeTahun . $periodeBulan . '-';
            
            // Cari sequence terakhir untuk periode ini
            $last = $this->where('nomor_perhitungan LIKE', $prefix . '%')
                         ->orderBy('nomor_perhitungan', 'DESC')
                         ->first();
            
            if ($last) {
                $lastNum = substr($last['nomor_perhitungan'], strlen($prefix));
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
        if (!isset($data['data']['status'])) {
            $data['data']['status'] = 'Draft';
        }
        
        if (!isset($data['data']['tanggal_perhitungan'])) {
            $data['data']['tanggal_perhitungan'] = date('Y-m-d');
        }
        
        // Default nilai 0 untuk field numeric
        $numericFields = [
            'gaji_pokok', 'tunjangan_jabatan', 'tunjangan_bpjs', 'tunjangan_makan',
            'tunjangan_transport', 'tunjangan_lainnya', 'total_pendapatan',
            'potongan_bpjs_kes', 'potongan_bpjs_tk', 'potongan_pph21',
            'potongan_absensi', 'potongan_kasbon', 'potongan_lainnya',
            'total_potongan', 'jam_lembur', 'upah_lembur', 'gaji_bersih'
        ];
        
        foreach ($numericFields as $field) {
            if (!isset($data['data'][$field])) {
                $data['data'][$field] = 0;
            }
        }
        
        return $data;
    }

    /**
     * Hitung total pendapatan, potongan, dan gaji bersih
     */
    protected function hitungTotal(array $data)
    {
        // Hitung total pendapatan
        $pendapatan = ($data['data']['gaji_pokok'] ?? 0) +
                      ($data['data']['tunjangan_jabatan'] ?? 0) +
                      ($data['data']['tunjangan_bpjs'] ?? 0) +
                      ($data['data']['tunjangan_makan'] ?? 0) +
                      ($data['data']['tunjangan_transport'] ?? 0) +
                      ($data['data']['tunjangan_lainnya'] ?? 0) +
                      ($data['data']['upah_lembur'] ?? 0);
        
        $data['data']['total_pendapatan'] = $pendapatan;
        
        // Hitung total potongan
        $potongan = ($data['data']['potongan_bpjs_kes'] ?? 0) +
                    ($data['data']['potongan_bpjs_tk'] ?? 0) +
                    ($data['data']['potongan_pph21'] ?? 0) +
                    ($data['data']['potongan_absensi'] ?? 0) +
                    ($data['data']['potongan_kasbon'] ?? 0) +
                    ($data['data']['potongan_lainnya'] ?? 0);
        
        $data['data']['total_potongan'] = $potongan;
        
        // Hitung gaji bersih
        $data['data']['gaji_bersih'] = $pendapatan - $potongan;
        
        return $data;
    }

    /**
     * Set created_by
     */
    protected function setCreatedBy(array $data)
    {
        $data['data']['created_by'] = session()->get('user_id');
        return $data;
    }

    /**
     * Set updated_by
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
                
                if ($current) {
                    // Jika status berubah menjadi Disetujui
                    if ($data['data']['status'] === 'Disetujui' && $current['status'] !== 'Disetujui') {
                        if (empty($data['data']['disetujui_oleh'])) {
                            $data['data']['disetujui_oleh'] = session()->get('user_id');
                        }
                        if (empty($data['data']['disetujui_at'])) {
                            $data['data']['disetujui_at'] = date('Y-m-d H:i:s');
                        }
                    }
                    
                    // Jika status berubah menjadi Ditolak
                    if ($data['data']['status'] === 'Ditolak' && $current['status'] === 'Disetujui') {
                        throw new \RuntimeException('Perhitungan gaji yang sudah disetujui tidak dapat ditolak');
                    }
                    
                    // Jika status berubah menjadi Draft dari Disetujui
                    if ($data['data']['status'] === 'Draft' && $current['status'] === 'Disetujui') {
                        throw new \RuntimeException('Perhitungan gaji yang sudah disetujui tidak dapat dikembalikan ke Draft');
                    }
                }
            }
        }
        
        return $data;
    }

    /**
     * Get all perhitungan gaji dengan filters
     */
    public function getAllWithFilters($filters = [], $perPage = 20, $page = 1)
    {
        $builder = $this->select('penggajian_perhitungan.*, 
            karyawan.nik as nomor_karyawan,
            karyawan.nama_lengkap as nama_karyawan,
            karyawan.jabatan,
            karyawan.departemen,
            creator.username as creator_name,
            approver.username as approver_name')
            ->join('karyawan', 'karyawan.id = penggajian_perhitungan.karyawan_id', 'left')
            ->join('users as creator', 'creator.id = penggajian_perhitungan.created_by', 'left')
            ->join('users as approver', 'approver.id = penggajian_perhitungan.disetujui_oleh', 'left');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('penggajian_perhitungan.nomor_perhitungan', $search)
                ->orLike('karyawan.nik', $search)
                ->orLike('karyawan.nama_lengkap', $search)
                ->orLike('karyawan.jabatan', $search)
                ->groupEnd();
        }
        
        if (!empty($filters['periode_bulan'])) {
            $builder->where('penggajian_perhitungan.periode_bulan', $filters['periode_bulan']);
        }
        
        if (!empty($filters['periode_tahun'])) {
            $builder->where('penggajian_perhitungan.periode_tahun', $filters['periode_tahun']);
        }
        
        if (!empty($filters['karyawan_id'])) {
            $builder->where('penggajian_perhitungan.karyawan_id', $filters['karyawan_id']);
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
        
        // Get total for pagination
        $total = $builder->countAllResults(false);
        
        // Get paginated results
        $offset = ($page - 1) * $perPage;
        $perhitungan = $builder->limit($perPage, $offset)->findAll();
        
        return [
            'data' => $perhitungan,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Get perhitungan gaji by ID with details
     */
    public function getWithDetails($id)
    {
        $perhitungan = $this->select('penggajian_perhitungan.*, 
            karyawan.nik as nomor_karyawan,
            karyawan.nama_lengkap as nama_karyawan,
            karyawan.jenis_kelamin,
            karyawan.tempat_lahir,
            karyawan.tanggal_lahir,
            karyawan.jabatan,
            karyawan.departemen,
            karyawan.tanggal_masuk,
            karyawan.status_karyawan,
            karyawan.no_rekening,
            karyawan.bank,
            karyawan.nama_rekening,
            creator.username as creator_name,
            creator.name as creator_fullname,
            approver.username as approver_name,
            approver.name as approver_fullname')
            ->join('karyawan', 'karyawan.id = penggajian_perhitungan.karyawan_id', 'left')
            ->join('users as creator', 'creator.id = penggajian_perhitungan.created_by', 'left')
            ->join('users as approver', 'approver.id = penggajian_perhitungan.disetujui_oleh', 'left')
            ->where('penggajian_perhitungan.id', $id)
            ->first();
        
        return $perhitungan;
    }

    /**
     * Get perhitungan gaji by karyawan and periode
     */
    public function getByKaryawanPeriode($karyawanId, $bulan, $tahun)
    {
        return $this->where('karyawan_id', $karyawanId)
                    ->where('periode_bulan', $bulan)
                    ->where('periode_tahun', $tahun)
                    ->first();
    }

    /**
     * Get perhitungan gaji by periode
     */
    public function getByPeriode($bulan, $tahun, $status = null)
    {
        $builder = $this->where('periode_bulan', $bulan)
                        ->where('periode_tahun', $tahun);
        
        if ($status) {
            $builder->where('status', $status);
        }
        
        return $builder->findAll();
    }

    /**
     * Get ringkasan per periode
     */
    public function getRingkasanPeriode($bulan, $tahun)
    {
        $result = $this->select("
                COUNT(*) as jumlah_karyawan,
                SUM(gaji_pokok) as total_gaji_pokok,
                SUM(tunjangan_jabatan) as total_tunjangan_jabatan,
                SUM(tunjangan_makan) as total_tunjangan_makan,
                SUM(tunjangan_transport) as total_tunjangan_transport,
                SUM(upah_lembur) as total_upah_lembur,
                SUM(total_pendapatan) as total_pendapatan,
                SUM(potongan_pph21) as total_pph21,
                SUM(potongan_bpjs_kes) as total_bpjs_kes,
                SUM(potongan_bpjs_tk) as total_bpjs_tk,
                SUM(total_potongan) as total_potongan,
                SUM(gaji_bersih) as total_gaji_bersih
            ")
            ->where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->where('status', 'Disetujui')
            ->first();
        
        return $result ?? [
            'jumlah_karyawan' => 0,
            'total_gaji_pokok' => 0,
            'total_tunjangan_jabatan' => 0,
            'total_tunjangan_makan' => 0,
            'total_tunjangan_transport' => 0,
            'total_upah_lembur' => 0,
            'total_pendapatan' => 0,
            'total_pph21' => 0,
            'total_bpjs_kes' => 0,
            'total_bpjs_tk' => 0,
            'total_potongan' => 0,
            'total_gaji_bersih' => 0
        ];
    }

    /**
     * Get rekap per departemen
     */
    public function getRekapPerDepartemen($bulan, $tahun)
    {
        return $this->select("
                karyawan.departemen,
                COUNT(*) as jumlah_karyawan,
                SUM(penggajian_perhitungan.gaji_pokok) as total_gaji_pokok,
                SUM(penggajian_perhitungan.tunjangan_jabatan) as total_tunjangan_jabatan,
                SUM(penggajian_perhitungan.upah_lembur) as total_upah_lembur,
                SUM(penggajian_perhitungan.total_pendapatan) as total_pendapatan,
                SUM(penggajian_perhitungan.total_potongan) as total_potongan,
                SUM(penggajian_perhitungan.gaji_bersih) as total_gaji_bersih
            ")
            ->join('karyawan', 'karyawan.id = penggajian_perhitungan.karyawan_id')
            ->where('penggajian_perhitungan.periode_bulan', $bulan)
            ->where('penggajian_perhitungan.periode_tahun', $tahun)
            ->where('penggajian_perhitungan.status', 'Disetujui')
            ->groupBy('karyawan.departemen')
            ->orderBy('karyawan.departemen', 'ASC')
            ->findAll();
    }

    /**
     * Get perhitungan gaji untuk diproses pembayaran (status Dihitung atau Disetujui)
     */
    public function getForPayment($bulan, $tahun)
    {
        return $this->select('penggajian_perhitungan.*, 
            karyawan.nik as nomor_karyawan,
            karyawan.nama_lengkap as nama_karyawan,
            karyawan.bank,
            karyawan.no_rekening,
            karyawan.nama_rekening')
            ->join('karyawan', 'karyawan.id = penggajian_perhitungan.karyawan_id')
            ->where('penggajian_perhitungan.periode_bulan', $bulan)
            ->where('penggajian_perhitungan.periode_tahun', $tahun)
            ->whereIn('penggajian_perhitungan.status', ['Dihitung', 'Disetujui'])
            ->orderBy('karyawan.nama_lengkap', 'ASC')
            ->findAll();
    }

    /**
     * Get perhitungan yang sudah disetujui untuk periode tertentu
     */
    public function getApproved($bulan, $tahun)
    {
        return $this->where('periode_bulan', $bulan)
                    ->where('periode_tahun', $tahun)
                    ->where('status', 'Disetujui')
                    ->findAll();
    }

    /**
     * Approve perhitungan gaji
     */
    public function approve($id)
    {
        $perhitungan = $this->find($id);
        
        if (!$perhitungan) {
            throw new \RuntimeException('Perhitungan gaji tidak ditemukan');
        }
        
        if ($perhitungan['status'] !== 'Dihitung') {
            throw new \RuntimeException('Hanya perhitungan dengan status Dihitung yang dapat disetujui');
        }
        
        return $this->update($id, [
            'status' => 'Disetujui',
            'disetujui_oleh' => session()->get('user_id'),
            'disetujui_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Reject perhitungan gaji
     */
    public function reject($id, $catatan = null)
    {
        $perhitungan = $this->find($id);
        
        if (!$perhitungan) {
            throw new \RuntimeException('Perhitungan gaji tidak ditemukan');
        }
        
        if ($perhitungan['status'] !== 'Dihitung') {
            throw new \RuntimeException('Hanya perhitungan dengan status Dihitung yang dapat ditolak');
        }
        
        $data = [
            'status' => 'Ditolak'
        ];
        
        if ($catatan) {
            $data['catatan'] = $catatan;
        }
        
        return $this->update($id, $data);
    }

    /**
     * Update status menjadi Dihitung (setelah perhitungan selesai)
     */
    public function markAsCalculated($id)
    {
        $perhitungan = $this->find($id);
        
        if (!$perhitungan) {
            throw new \RuntimeException('Perhitungan gaji tidak ditemukan');
        }
        
        if ($perhitungan['status'] !== 'Draft') {
            throw new \RuntimeException('Hanya perhitungan dengan status Draft yang dapat dihitung');
        }
        
        return $this->update($id, ['status' => 'Dihitung']);
    }

    /**
     * Get statistics untuk dashboard
     */
    public function getStats($tahun = null, $bulan = null)
    {
        $builder = $this->where('status', 'Disetujui');
        
        if ($tahun) {
            $builder->where('periode_tahun', $tahun);
        }
        
        if ($bulan) {
            $builder->where('periode_bulan', $bulan);
        }
        
        $stats = $builder->select("
                COUNT(*) as total_karyawan,
                SUM(gaji_pokok) as total_gaji_pokok,
                SUM(tunjangan_jabatan) as total_tunjangan_jabatan,
                SUM(upah_lembur) as total_upah_lembur,
                SUM(total_pendapatan) as total_pendapatan,
                SUM(potongan_pph21) as total_pph21,
                SUM(potongan_bpjs_kes) as total_bpjs_kes,
                SUM(potongan_bpjs_tk) as total_bpjs_tk,
                SUM(total_potongan) as total_potongan,
                SUM(gaji_bersih) as total_gaji_bersih
            ")
            ->first();
        
        return $stats ?? [
            'total_karyawan' => 0,
            'total_gaji_pokok' => 0,
            'total_tunjangan_jabatan' => 0,
            'total_upah_lembur' => 0,
            'total_pendapatan' => 0,
            'total_pph21' => 0,
            'total_bpjs_kes' => 0,
            'total_bpjs_tk' => 0,
            'total_potongan' => 0,
            'total_gaji_bersih' => 0
        ];
    }

    /**
     * Hitung otomatis berdasarkan absensi dan komponen gaji
     * (Untuk auto generate perhitungan gaji)
     */
    public function calculateFromAbsensi($karyawanId, $bulan, $tahun)
    {
        // Ambil data karyawan
        $karyawanModel = new \App\Models\KaryawanModel();
        $karyawan = $karyawanModel->find($karyawanId);
        
        if (!$karyawan) {
            throw new \RuntimeException('Karyawan tidak ditemukan');
        }
        
        // Ambil data absensi karyawan untuk periode tersebut
        $absensiModel = new \App\Models\AbsensiModel();
        $absensi = $absensiModel->where('karyawan_id', $karyawanId)
            ->where('MONTH(tanggal)', $bulan)
            ->where('YEAR(tanggal)', $tahun)
            ->findAll();
        
        // Hitung kehadiran
        $totalHadir = 0;
        $totalIzin = 0;
        $totalSakit = 0;
        $totalCuti = 0;
        $totalAlpha = 0;
        $totalTerlambat = 0;
        $totalJamKerja = 0;
        $totalJamLembur = 0;
        
        foreach ($absensi as $item) {
            if ($item['status'] == 'Hadir') {
                $totalHadir++;
                $totalJamKerja += $item['jam_kerja'] ?? 0;
                $totalJamLembur += $item['jam_lembur'] ?? 0;
                $totalTerlambat += $item['terlambat'] ?? 0;
            } elseif ($item['status'] == 'Izin') {
                $totalIzin++;
            } elseif ($item['status'] == 'Sakit') {
                $totalSakit++;
            } elseif ($item['status'] == 'Cuti') {
                $totalCuti++;
            } elseif ($item['status'] == 'Alpha') {
                $totalAlpha++;
            }
        }
        
        // Hitung total hari kerja dalam periode
        $totalHariKerja = $this->getTotalHariKerja($bulan, $tahun);
        
        // Hitung gaji pokok prorata jika ada ketidakhadiran tanpa izin
        $gajiPokok = $karyawan['gaji_pokok'] ?? 0;
        $potonganAbsensi = 0;
        
        if ($totalAlpha > 0) {
            $gajiPerHari = $gajiPokok / $totalHariKerja;
            $potonganAbsensi = $gajiPerHari * $totalAlpha;
        }
        
        // Hitung upah lembur
        $upahLembur = $totalJamLembur * ($gajiPerHari ?? 0) / 8 * 1.5;
        
        // Hitung tunjangan (contoh sederhana)
        $tunjanganJabatan = $karyawan['tunjangan_jabatan'] ?? 0;
        $tunjanganMakan = $karyawan['tunjangan_makan'] ?? 0;
        $tunjanganTransport = $karyawan['tunjangan_transport'] ?? 0;
        
        // Data untuk disimpan
        return [
            'gaji_pokok' => $gajiPokok,
            'tunjangan_jabatan' => $tunjanganJabatan,
            'tunjangan_makan' => $tunjanganMakan,
            'tunjangan_transport' => $tunjanganTransport,
            'upah_lembur' => $upahLembur,
            'potongan_absensi' => $potonganAbsensi,
            'total_hari_kerja' => $totalHariKerja,
            'total_hadir' => $totalHadir,
            'total_izin' => $totalIzin,
            'total_sakit' => $totalSakit,
            'total_cuti' => $totalCuti,
            'total_alpha' => $totalAlpha,
            'total_terlambat' => $totalTerlambat,
            'jam_lembur' => $totalJamLembur
        ];
    }

    /**
     * Get total hari kerja dalam periode (Senin-Jumat, exclude hari libur nasional)
     */
    private function getTotalHariKerja($bulan, $tahun)
    {
        $tanggalMulai = date("$tahun-$bulan-01");
        $tanggalAkhir = date("Y-m-t", strtotime($tanggalMulai));
        
        $start = new \DateTime($tanggalMulai);
        $end = new \DateTime($tanggalAkhir);
        $end->modify('+1 day');
        
        $interval = new \DateInterval('P1D');
        $periode = new \DatePeriod($start, $interval, $end);
        
        $hariKerja = 0;
        foreach ($periode as $date) {
            $dayOfWeek = $date->format('N');
            // Senin = 1, Minggu = 7
            if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
                $hariKerja++;
            }
        }
        
        return $hariKerja;
    }

    /**
     * Get list untuk dropdown periode
     */
    public function getPeriodeOptions()
    {
        $result = $this->select('periode_bulan, periode_tahun')
            ->groupBy('periode_bulan, periode_tahun')
            ->orderBy('periode_tahun', 'DESC')
            ->orderBy('periode_bulan', 'DESC')
            ->findAll();
        
        $options = [];
        foreach ($result as $item) {
            $options[] = [
                'bulan' => $item['periode_bulan'],
                'tahun' => $item['periode_tahun'],
                'label' => $this->getNamaBulan($item['periode_bulan']) . ' ' . $item['periode_tahun']
            ];
        }
        
        return $options;
    }

    /**
     * Get nama bulan
     */
    private function getNamaBulan($bulan)
    {
        $bulanNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return $bulanNames[$bulan] ?? '';
    }

    /**
     * Export data untuk Excel
     */
    public function getExportData($filters = [])
    {
        $builder = $this->select('penggajian_perhitungan.*, 
            karyawan.nik as nomor_karyawan,
            karyawan.nama_lengkap as nama_karyawan,
            karyawan.jabatan,
            karyawan.departemen')
            ->join('karyawan', 'karyawan.id = penggajian_perhitungan.karyawan_id', 'left');
        
        if (!empty($filters['periode_bulan'])) {
            $builder->where('penggajian_perhitungan.periode_bulan', $filters['periode_bulan']);
        }
        
        if (!empty($filters['periode_tahun'])) {
            $builder->where('penggajian_perhitungan.periode_tahun', $filters['periode_tahun']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('penggajian_perhitungan.status', $filters['status']);
        }
        
        $perhitungan = $builder->orderBy('karyawan.nama_lengkap', 'ASC')->findAll();
        
        $exportData = [];
        foreach ($perhitungan as $item) {
            $exportData[] = [
                'Nomor Perhitungan' => $item['nomor_perhitungan'],
                'NIK' => $item['nomor_karyawan'],
                'Nama Karyawan' => $item['nama_karyawan'],
                'Jabatan' => $item['jabatan'],
                'Departemen' => $item['departemen'],
                'Periode' => $this->getNamaBulan($item['periode_bulan']) . ' ' . $item['periode_tahun'],
                'Gaji Pokok' => $item['gaji_pokok'],
                'Tunjangan Jabatan' => $item['tunjangan_jabatan'],
                'Tunjangan Makan' => $item['tunjangan_makan'],
                'Tunjangan Transport' => $item['tunjangan_transport'],
                'Upah Lembur' => $item['upah_lembur'],
                'Total Pendapatan' => $item['total_pendapatan'],
                'Potongan BPJS Kes' => $item['potongan_bpjs_kes'],
                'Potongan BPJS TK' => $item['potongan_bpjs_tk'],
                'Potongan PPh 21' => $item['potongan_pph21'],
                'Potongan Absensi' => $item['potongan_absensi'],
                'Potongan Kasbon' => $item['potongan_kasbon'],
                'Total Potongan' => $item['total_potongan'],
                'Gaji Bersih' => $item['gaji_bersih'],
                'Status' => $item['status'],
                'Tanggal Perhitungan' => $item['tanggal_perhitungan']
            ];
        }
        
        return $exportData;
    }

    /**
     * Check if perhitungan exists for period
     */
    public function existsForPeriod($karyawanId, $bulan, $tahun)
    {
        return $this->where('karyawan_id', $karyawanId)
                    ->where('periode_bulan', $bulan)
                    ->where('periode_tahun', $tahun)
                    ->countAllResults() > 0;
    }
}