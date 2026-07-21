<?php
namespace App\Models\Accounting;

use CodeIgniter\Model;

class PenggajianDetailPembayaranModel extends Model
{
    protected $table = 'penggajian_detail_pembayaran';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'proses_id',
        'perhitungan_id',
        'karyawan_id',
        'nomor_karyawan',
        'nama_karyawan',
        'bank',
        'no_rekening',
        'nama_rekening',
        'gaji_pokok',
        'total_tunjangan',
        'upah_lembur',
        'total_potongan',
        'gaji_bersih',
        'status_pembayaran',
        'tanggal_pembayaran',
        'keterangan',
        'no_referensi_eksternal'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'proses_id' => 'required|is_natural_no_zero',
        'perhitungan_id' => 'required|is_natural_no_zero',
        'karyawan_id' => 'required|is_natural_no_zero',
        'nama_karyawan' => 'required',
        'gaji_bersih' => 'required|numeric',
        'status_pembayaran' => 'permit_empty|in_list[Pending,Berhasil,Gagal,Dikembalikan]'
    ];

    protected $validationMessages = [
        'proses_id' => [
            'required' => 'ID proses pembayaran harus diisi'
        ],
        'perhitungan_id' => [
            'required' => 'ID perhitungan gaji harus diisi'
        ],
        'karyawan_id' => [
            'required' => 'Karyawan harus dipilih'
        ],
        'nama_karyawan' => [
            'required' => 'Nama karyawan harus diisi'
        ],
        'gaji_bersih' => [
            'required' => 'Gaji bersih harus diisi',
            'numeric' => 'Gaji bersih harus berupa angka'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = ['setDefaultValues'];
    protected $beforeUpdate = ['validateStatusChange'];

    /**
     * Set default values
     */
    protected function setDefaultValues(array $data)
    {
        if (!isset($data['data']['status_pembayaran'])) {
            $data['data']['status_pembayaran'] = 'Pending';
        }
        
        // Set default 0 untuk numeric fields
        $numericFields = ['gaji_pokok', 'total_tunjangan', 'upah_lembur', 'total_potongan', 'gaji_bersih'];
        foreach ($numericFields as $field) {
            if (!isset($data['data'][$field])) {
                $data['data'][$field] = 0;
            }
        }
        
        return $data;
    }

    /**
     * Validasi perubahan status
     */
    protected function validateStatusChange(array $data)
    {
        if (isset($data['data']['status_pembayaran'])) {
            $id = $data['id'][0] ?? null;
            
            if ($id) {
                $current = $this->find($id);
                
                if ($current) {
                    // Jika status berubah menjadi Berhasil, set tanggal pembayaran jika belum ada
                    if ($data['data']['status_pembayaran'] === 'Berhasil' && $current['status_pembayaran'] !== 'Berhasil') {
                        if (empty($data['data']['tanggal_pembayaran'])) {
                            $data['data']['tanggal_pembayaran'] = date('Y-m-d');
                        }
                    }
                    
                    // Jika status berubah menjadi Gagal atau Dikembalikan, bisa diisi keterangan
                    if (in_array($data['data']['status_pembayaran'], ['Gagal', 'Dikembalikan'])) {
                        if (empty($data['data']['keterangan'])) {
                            // Tidak wajib, tapi bisa diisi nanti
                        }
                    }
                    
                    // Tidak bisa mengubah status dari Berhasil menjadi selain Berhasil
                    if ($current['status_pembayaran'] === 'Berhasil' && $data['data']['status_pembayaran'] !== 'Berhasil') {
                        throw new \RuntimeException('Tidak dapat mengubah status pembayaran yang sudah berhasil');
                    }
                }
            }
        }
        
        return $data;
    }

    /**
     * Get all detail pembayaran with filters
     */
    public function getAllWithFilters($filters = [], $perPage = 20, $page = 1)
    {
        $builder = $this->select('penggajian_detail_pembayaran.*, 
            penggajian_proses_pembayaran.nomor_proses,
            penggajian_proses_pembayaran.nama_proses,
            penggajian_proses_pembayaran.periode_bulan,
            penggajian_proses_pembayaran.periode_tahun,
            penggajian_proses_pembayaran.tanggal_pembayaran as proses_tanggal_pembayaran,
            penggajian_perhitungan.nomor_perhitungan,
            penggajian_perhitungan.status as perhitungan_status')
            ->join('penggajian_proses_pembayaran', 'penggajian_proses_pembayaran.id = penggajian_detail_pembayaran.proses_id', 'left')
            ->join('penggajian_perhitungan', 'penggajian_perhitungan.id = penggajian_detail_pembayaran.perhitungan_id', 'left');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('penggajian_detail_pembayaran.nama_karyawan', $search)
                ->orLike('penggajian_detail_pembayaran.nomor_karyawan', $search)
                ->orLike('penggajian_detail_pembayaran.no_rekening', $search)
                ->orLike('penggajian_proses_pembayaran.nomor_proses', $search)
                ->groupEnd();
        }
        
        if (!empty($filters['proses_id'])) {
            $builder->where('penggajian_detail_pembayaran.proses_id', $filters['proses_id']);
        }
        
        if (!empty($filters['perhitungan_id'])) {
            $builder->where('penggajian_detail_pembayaran.perhitungan_id', $filters['perhitungan_id']);
        }
        
        if (!empty($filters['karyawan_id'])) {
            $builder->where('penggajian_detail_pembayaran.karyawan_id', $filters['karyawan_id']);
        }
        
        if (!empty($filters['status_pembayaran'])) {
            $builder->where('penggajian_detail_pembayaran.status_pembayaran', $filters['status_pembayaran']);
        }
        
        if (!empty($filters['periode_bulan'])) {
            $builder->where('penggajian_proses_pembayaran.periode_bulan', $filters['periode_bulan']);
        }
        
        if (!empty($filters['periode_tahun'])) {
            $builder->where('penggajian_proses_pembayaran.periode_tahun', $filters['periode_tahun']);
        }
        
        if (!empty($filters['tanggal_pembayaran_mulai'])) {
            $builder->where('penggajian_detail_pembayaran.tanggal_pembayaran >=', $filters['tanggal_pembayaran_mulai']);
        }
        
        if (!empty($filters['tanggal_pembayaran_selesai'])) {
            $builder->where('penggajian_detail_pembayaran.tanggal_pembayaran <=', $filters['tanggal_pembayaran_selesai']);
        }
        
        $builder->orderBy('penggajian_detail_pembayaran.id', 'ASC');
        
        // Get total for pagination
        $total = $builder->countAllResults(false);
        
        // Get paginated results
        $offset = ($page - 1) * $perPage;
        $details = $builder->limit($perPage, $offset)->findAll();
        
        return [
            'data' => $details,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Get detail pembayaran by ID with complete info
     */
    public function getWithDetails($id)
    {
        $detail = $this->select('penggajian_detail_pembayaran.*, 
            penggajian_proses_pembayaran.nomor_proses,
            penggajian_proses_pembayaran.nama_proses,
            penggajian_proses_pembayaran.periode_bulan,
            penggajian_proses_pembayaran.periode_tahun,
            penggajian_proses_pembayaran.tanggal_proses,
            penggajian_proses_pembayaran.tanggal_pembayaran as proses_tanggal_pembayaran,
            penggajian_proses_pembayaran.metode_pembayaran,
            penggajian_proses_pembayaran.bank_pengirim,
            penggajian_proses_pembayaran.status as proses_status,
            penggajian_perhitungan.nomor_perhitungan,
            penggajian_perhitungan.total_hadir,
            penggajian_perhitungan.total_izin,
            penggajian_perhitungan.total_sakit,
            penggajian_perhitungan.total_cuti,
            penggajian_perhitungan.total_alpha,
            penggajian_perhitungan.jam_lembur,
            penggajian_perhitungan.status as perhitungan_status,
            penggajian_perhitungan.catatan as perhitungan_catatan')
            ->join('penggajian_proses_pembayaran', 'penggajian_proses_pembayaran.id = penggajian_detail_pembayaran.proses_id', 'left')
            ->join('penggajian_perhitungan', 'penggajian_perhitungan.id = penggajian_detail_pembayaran.perhitungan_id', 'left')
            ->where('penggajian_detail_pembayaran.id', $id)
            ->first();
        
        return $detail;
    }

    /**
     * Get detail by proses_id
     */
    public function getByProses($prosesId)
    {
        return $this->where('proses_id', $prosesId)
                    ->orderBy('nama_karyawan', 'ASC')
                    ->findAll();
    }

    /**
     * Get detail by proses_id with summary
     */
    public function getByProsesWithSummary($prosesId)
    {
        $details = $this->getByProses($prosesId);
        
        $summary = [
            'total_karyawan' => count($details),
            'total_gaji_pokok' => array_sum(array_column($details, 'gaji_pokok')),
            'total_tunjangan' => array_sum(array_column($details, 'total_tunjangan')),
            'total_upah_lembur' => array_sum(array_column($details, 'upah_lembur')),
            'total_potongan' => array_sum(array_column($details, 'total_potongan')),
            'total_gaji_bersih' => array_sum(array_column($details, 'gaji_bersih')),
            'jumlah_berhasil' => count(array_filter($details, function($item) {
                return $item['status_pembayaran'] === 'Berhasil';
            })),
            'jumlah_gagal' => count(array_filter($details, function($item) {
                return $item['status_pembayaran'] === 'Gagal';
            })),
            'jumlah_pending' => count(array_filter($details, function($item) {
                return $item['status_pembayaran'] === 'Pending';
            }))
        ];
        
        return [
            'details' => $details,
            'summary' => $summary
        ];
    }

    /**
     * Get detail by karyawan and periode
     */
    public function getByKaryawanPeriode($karyawanId, $bulan, $tahun)
    {
        return $this->select('penggajian_detail_pembayaran.*, 
            penggajian_proses_pembayaran.periode_bulan,
            penggajian_proses_pembayaran.periode_tahun,
            penggajian_proses_pembayaran.tanggal_pembayaran as proses_tanggal_pembayaran')
            ->join('penggajian_proses_pembayaran', 'penggajian_proses_pembayaran.id = penggajian_detail_pembayaran.proses_id')
            ->where('penggajian_detail_pembayaran.karyawan_id', $karyawanId)
            ->where('penggajian_proses_pembayaran.periode_bulan', $bulan)
            ->where('penggajian_proses_pembayaran.periode_tahun', $tahun)
            ->first();
    }

    /**
     * Get detail by karyawan for history
     */
    public function getHistoryByKaryawan($karyawanId, $limit = 12)
    {
        return $this->select('penggajian_detail_pembayaran.*, 
            penggajian_proses_pembayaran.periode_bulan,
            penggajian_proses_pembayaran.periode_tahun,
            penggajian_proses_pembayaran.tanggal_pembayaran as proses_tanggal_pembayaran,
            penggajian_proses_pembayaran.metode_pembayaran')
            ->join('penggajian_proses_pembayaran', 'penggajian_proses_pembayaran.id = penggajian_detail_pembayaran.proses_id')
            ->where('penggajian_detail_pembayaran.karyawan_id', $karyawanId)
            ->where('penggajian_detail_pembayaran.status_pembayaran', 'Berhasil')
            ->orderBy('penggajian_proses_pembayaran.periode_tahun', 'DESC')
            ->orderBy('penggajian_proses_pembayaran.periode_bulan', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Update status pembayaran untuk multiple detail
     */
    public function updateBulkStatus($ids, $status, $keterangan = null)
    {
        $db = \Config\Database::connect();
        $db->transStart();
        
        $updateData = ['status_pembayaran' => $status];
        
        if ($status === 'Berhasil') {
            $updateData['tanggal_pembayaran'] = date('Y-m-d');
        }
        
        if ($keterangan) {
            $updateData['keterangan'] = $keterangan;
        }
        
        $updated = 0;
        foreach ($ids as $id) {
            if ($this->update($id, $updateData)) {
                $updated++;
            }
        }
        
        $db->transComplete();
        
        return [
            'success' => $db->transStatus(),
            'updated' => $updated,
            'total' => count($ids)
        ];
    }

    /**
     * Update status pembayaran berdasarkan hasil transfer bank
     */
    public function updateFromBankTransfer($prosesId, $noReferensi, $status, $keterangan = null)
    {
        $detail = $this->where('proses_id', $prosesId)
                        ->where('no_referensi_eksternal', $noReferensi)
                        ->first();
        
        if (!$detail) {
            return false;
        }
        
        $updateData = ['status_pembayaran' => $status];
        
        if ($status === 'Berhasil') {
            $updateData['tanggal_pembayaran'] = date('Y-m-d');
        }
        
        if ($keterangan) {
            $updateData['keterangan'] = $keterangan;
        }
        
        return $this->update($detail['id'], $updateData);
    }

    /**
     * Get detail yang belum berhasil diproses
     */
    public function getPendingByProses($prosesId)
    {
        return $this->where('proses_id', $prosesId)
                    ->where('status_pembayaran', 'Pending')
                    ->findAll();
    }

    /**
     * Get detail yang gagal diproses
     */
    public function getFailedByProses($prosesId)
    {
        return $this->where('proses_id', $prosesId)
                    ->where('status_pembayaran', 'Gagal')
                    ->findAll();
    }

    /**
     * Get statistics untuk dashboard
     */
    public function getStats($tahun = null, $bulan = null)
    {
        $builder = $this->select('penggajian_detail_pembayaran.*, 
            penggajian_proses_pembayaran.periode_bulan,
            penggajian_proses_pembayaran.periode_tahun')
            ->join('penggajian_proses_pembayaran', 'penggajian_proses_pembayaran.id = penggajian_detail_pembayaran.proses_id')
            ->where('penggajian_detail_pembayaran.status_pembayaran', 'Berhasil');
        
        if ($tahun) {
            $builder->where('penggajian_proses_pembayaran.periode_tahun', $tahun);
        }
        
        if ($bulan) {
            $builder->where('penggajian_proses_pembayaran.periode_bulan', $bulan);
        }
        
        $stats = $builder->select("
                COUNT(*) as total_karyawan,
                SUM(gaji_pokok) as total_gaji_pokok,
                SUM(total_tunjangan) as total_tunjangan,
                SUM(upah_lembur) as total_upah_lembur,
                SUM(total_potongan) as total_potongan,
                SUM(gaji_bersih) as total_gaji_bersih
            ")
            ->first();
        
        return $stats ?? [
            'total_karyawan' => 0,
            'total_gaji_pokok' => 0,
            'total_tunjangan' => 0,
            'total_upah_lembur' => 0,
            'total_potongan' => 0,
            'total_gaji_bersih' => 0
        ];
    }

    /**
     * Get rekap pembayaran per bank
     */
    public function getRekapPerBank($bulan, $tahun)
    {
        return $this->select("
                bank,
                COUNT(*) as jumlah_karyawan,
                SUM(gaji_bersih) as total_nominal
            ")
            ->join('penggajian_proses_pembayaran', 'penggajian_proses_pembayaran.id = penggajian_detail_pembayaran.proses_id')
            ->where('penggajian_proses_pembayaran.periode_bulan', $bulan)
            ->where('penggajian_proses_pembayaran.periode_tahun', $tahun)
            ->where('penggajian_detail_pembayaran.status_pembayaran', 'Berhasil')
            ->where('bank IS NOT NULL')
            ->where('bank !=', '')
            ->groupBy('bank')
            ->orderBy('total_nominal', 'DESC')
            ->findAll();
    }

    /**
     * Get export data for bank transfer format
     */
    public function getExportBankTransfer($prosesId)
    {
        $details = $this->where('proses_id', $prosesId)
                        ->where('status_pembayaran', 'Pending')
                        ->where('bank IS NOT NULL')
                        ->where('no_rekening IS NOT NULL')
                        ->orderBy('bank', 'ASC')
                        ->orderBy('nama_karyawan', 'ASC')
                        ->findAll();
        
        $exportData = [];
        foreach ($details as $item) {
            $exportData[] = [
                'no_rekening' => $item['no_rekening'],
                'nama_rekening' => $item['nama_rekening'] ?? $item['nama_karyawan'],
                'bank' => $item['bank'],
                'nominal' => $item['gaji_bersih'],
                'keterangan' => 'Gaji ' . $item['nama_karyawan']
            ];
        }
        
        return $exportData;
    }

    /**
     * Generate file for bank transfer (CSV format)
     */
    public function generateBankTransferFile($prosesId)
    {
        $details = $this->getExportBankTransfer($prosesId);
        
        if (empty($details)) {
            return null;
        }
        
        $filename = 'transfer_gaji_' . date('Ymd_His') . '.csv';
        $filepath = WRITEPATH . 'exports/' . $filename;
        
        // Ensure directory exists
        if (!is_dir(WRITEPATH . 'exports')) {
            mkdir(WRITEPATH . 'exports', 0755, true);
        }
        
        $file = fopen($filepath, 'w');
        
        // Header CSV (sesuaikan dengan format bank)
        fputcsv($file, ['No Rekening', 'Nama Rekening', 'Bank', 'Nominal', 'Keterangan']);
        
        foreach ($details as $item) {
            fputcsv($file, [
                $item['no_rekening'],
                $item['nama_rekening'],
                $item['bank'],
                $item['nominal'],
                $item['keterangan']
            ]);
        }
        
        fclose($file);
        
        return $filepath;
    }

    /**
     * Mark as successful with reference number
     */
    public function markAsSuccess($id, $noReferensi = null)
    {
        $detail = $this->find($id);
        
        if (!$detail) {
            throw new \RuntimeException('Detail pembayaran tidak ditemukan');
        }
        
        if ($detail['status_pembayaran'] !== 'Pending') {
            throw new \RuntimeException('Hanya detail dengan status Pending yang dapat ditandai berhasil');
        }
        
        $updateData = [
            'status_pembayaran' => 'Berhasil',
            'tanggal_pembayaran' => date('Y-m-d')
        ];
        
        if ($noReferensi) {
            $updateData['no_referensi_eksternal'] = $noReferensi;
        }
        
        return $this->update($id, $updateData);
    }

    /**
     * Mark as failed
     */
    public function markAsFailed($id, $keterangan = null)
    {
        $detail = $this->find($id);
        
        if (!$detail) {
            throw new \RuntimeException('Detail pembayaran tidak ditemukan');
        }
        
        if ($detail['status_pembayaran'] !== 'Pending') {
            throw new \RuntimeException('Hanya detail dengan status Pending yang dapat ditandai gagal');
        }
        
        $updateData = ['status_pembayaran' => 'Gagal'];
        
        if ($keterangan) {
            $updateData['keterangan'] = $keterangan;
        }
        
        return $this->update($id, $updateData);
    }

    /**
     * Get summary by proses_id
     */
    public function getSummaryByProses($prosesId)
    {
        $details = $this->where('proses_id', $prosesId)->findAll();
        
        return [
            'total' => count($details),
            'total_nominal' => array_sum(array_column($details, 'gaji_bersih')),
            'berhasil' => count(array_filter($details, function($item) {
                return $item['status_pembayaran'] === 'Berhasil';
            })),
            'gagal' => count(array_filter($details, function($item) {
                return $item['status_pembayaran'] === 'Gagal';
            })),
            'pending' => count(array_filter($details, function($item) {
                return $item['status_pembayaran'] === 'Pending';
            }))
        ];
    }

    /**
     * Get total gaji bersih by proses_id
     */
    public function getTotalGajiByProses($prosesId)
    {
        return $this->where('proses_id', $prosesId)
                    ->selectSum('gaji_bersih')
                    ->first()['gaji_bersih'] ?? 0;
    }

    /**
     * Export data untuk Excel (laporan detail)
     */
    public function getExportData($filters = [])
    {
        $builder = $this->select('penggajian_detail_pembayaran.*, 
            penggajian_proses_pembayaran.nomor_proses,
            penggajian_proses_pembayaran.nama_proses,
            penggajian_proses_pembayaran.periode_bulan,
            penggajian_proses_pembayaran.periode_tahun,
            penggajian_proses_pembayaran.tanggal_pembayaran as proses_tanggal_pembayaran,
            penggajian_proses_pembayaran.metode_pembayaran,
            penggajian_perhitungan.nomor_perhitungan')
            ->join('penggajian_proses_pembayaran', 'penggajian_proses_pembayaran.id = penggajian_detail_pembayaran.proses_id', 'left')
            ->join('penggajian_perhitungan', 'penggajian_perhitungan.id = penggajian_detail_pembayaran.perhitungan_id', 'left');
        
        if (!empty($filters['proses_id'])) {
            $builder->where('penggajian_detail_pembayaran.proses_id', $filters['proses_id']);
        }
        
        if (!empty($filters['periode_bulan'])) {
            $builder->where('penggajian_proses_pembayaran.periode_bulan', $filters['periode_bulan']);
        }
        
        if (!empty($filters['periode_tahun'])) {
            $builder->where('penggajian_proses_pembayaran.periode_tahun', $filters['periode_tahun']);
        }
        
        if (!empty($filters['status_pembayaran'])) {
            $builder->where('penggajian_detail_pembayaran.status_pembayaran', $filters['status_pembayaran']);
        }
        
        $details = $builder->orderBy('penggajian_detail_pembayaran.id', 'ASC')->findAll();
        
        $exportData = [];
        foreach ($details as $item) {
            $exportData[] = [
                'Nomor Proses' => $item['nomor_proses'],
                'Nama Proses' => $item['nama_proses'],
                'Periode' => $this->getNamaBulan($item['periode_bulan']) . ' ' . $item['periode_tahun'],
                'Tanggal Pembayaran' => $item['proses_tanggal_pembayaran'] ?? '-',
                'Metode Pembayaran' => $item['metode_pembayaran'] ?? '-',
                'NIK' => $item['nomor_karyawan'] ?? '-',
                'Nama Karyawan' => $item['nama_karyawan'],
                'Bank' => $item['bank'] ?? '-',
                'No Rekening' => $item['no_rekening'] ?? '-',
                'Gaji Pokok' => $item['gaji_pokok'],
                'Tunjangan' => $item['total_tunjangan'],
                'Upah Lembur' => $item['upah_lembur'],
                'Potongan' => $item['total_potongan'],
                'Gaji Bersih' => $item['gaji_bersih'],
                'Status' => $item['status_pembayaran'],
                'Tanggal Bayar' => $item['tanggal_pembayaran'] ?? '-',
                'No Referensi' => $item['no_referensi_eksternal'] ?? '-',
                'Keterangan' => $item['keterangan'] ?? '-'
            ];
        }
        
        return $exportData;
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
}