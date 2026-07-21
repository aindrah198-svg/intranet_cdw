<?php
namespace App\Models\Accounting;

use CodeIgniter\Model;

class PenggajianProsesPembayaranModel extends Model
{
    protected $table = 'penggajian_proses_pembayaran';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'nomor_proses',
        'nama_proses',
        'periode_bulan',
        'periode_tahun',
        'tanggal_proses',
        'tanggal_pembayaran',
        'metode_pembayaran',
        'bank_pengirim',
        'coa_bank_id',
        'total_karyawan',
        'total_nominal',
        'status',
        'keterangan',
        'file_export',
        'bukti_transfer',
        'mutasi_bank_id',
        'jurnal_id',
        'selesai_at',
        'selesai_by',
        'created_by',
        'updated_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'nama_proses' => 'required',
        'periode_bulan' => 'required|in_list[1,2,3,4,5,6,7,8,9,10,11,12]',
        'periode_tahun' => 'required|numeric|min_length[4]|max_length[4]',
        'tanggal_proses' => 'required|valid_date',
        'tanggal_pembayaran' => 'required|valid_date',
        'metode_pembayaran' => 'required|in_list[Transfer Bank,Tunai,Cek,Giro]',
        'coa_bank_id' => 'permit_empty|is_natural_no_zero',
        'total_karyawan' => 'permit_empty|numeric',
        'total_nominal' => 'permit_empty|numeric',
        'status' => 'permit_empty|in_list[Draft,Diproses,Selesai,Dibatalkan]'
    ];

    protected $validationMessages = [
        'nama_proses' => [
            'required' => 'Nama proses pembayaran harus diisi'
        ],
        'periode_bulan' => [
            'required' => 'Bulan periode harus dipilih'
        ],
        'periode_tahun' => [
            'required' => 'Tahun periode harus diisi'
        ],
        'tanggal_proses' => [
            'required' => 'Tanggal proses harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'tanggal_pembayaran' => [
            'required' => 'Tanggal pembayaran harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'metode_pembayaran' => [
            'required' => 'Metode pembayaran harus dipilih'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateNomorProses', 'setDefaultValues', 'setCreatedBy'];
    protected $beforeUpdate = ['setUpdatedBy', 'validateStatusChange'];

    /**
     * Generate nomor proses otomatis
     * Format: PGP-YYYYMMDD-XXXX
     */
    protected function generateNomorProses(array $data)
    {
        if (empty($data['data']['nomor_proses'])) {
            $tanggal = $data['data']['tanggal_proses'] ?? date('Y-m-d');
            $prefix = 'PGP-' . date('Ymd', strtotime($tanggal)) . '-';
            
            // Cari sequence terakhir untuk tanggal ini
            $last = $this->where('nomor_proses LIKE', $prefix . '%')
                         ->orderBy('nomor_proses', 'DESC')
                         ->first();
            
            if ($last) {
                $lastNum = substr($last['nomor_proses'], strlen($prefix));
                $nextNum = str_pad((int)$lastNum + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNum = '0001';
            }
            
            $data['data']['nomor_proses'] = $prefix . $nextNum;
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
        
        if (!isset($data['data']['metode_pembayaran'])) {
            $data['data']['metode_pembayaran'] = 'Transfer Bank';
        }
        
        if (!isset($data['data']['total_karyawan'])) {
            $data['data']['total_karyawan'] = 0;
        }
        
        if (!isset($data['data']['total_nominal'])) {
            $data['data']['total_nominal'] = 0;
        }
        
        if (!isset($data['data']['tanggal_proses'])) {
            $data['data']['tanggal_proses'] = date('Y-m-d');
        }
        
        if (!isset($data['data']['tanggal_pembayaran'])) {
            $data['data']['tanggal_pembayaran'] = date('Y-m-d');
        }
        
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
                    // Jika status berubah menjadi Selesai
                    if ($data['data']['status'] === 'Selesai' && $current['status'] !== 'Selesai') {
                        if (empty($data['data']['selesai_by'])) {
                            $data['data']['selesai_by'] = session()->get('user_id');
                        }
                        if (empty($data['data']['selesai_at'])) {
                            $data['data']['selesai_at'] = date('Y-m-d H:i:s');
                        }
                        
                        // Validasi harus ada detail pembayaran
                        $detailModel = new PenggajianDetailPembayaranModel();
                        $details = $detailModel->where('proses_id', $id)->findAll();
                        
                        if (empty($details)) {
                            throw new \RuntimeException('Tidak dapat menyelesaikan proses pembayaran karena belum ada detail pembayaran');
                        }
                    }
                    
                    // Jika status berubah menjadi Dibatalkan
                    if ($data['data']['status'] === 'Dibatalkan' && $current['status'] === 'Selesai') {
                        throw new \RuntimeException('Proses pembayaran yang sudah selesai tidak dapat dibatalkan');
                    }
                }
            }
        }
        
        return $data;
    }

    /**
     * Get all proses pembayaran with filters
     */
    public function getAllWithFilters($filters = [], $perPage = 20, $page = 1)
    {
        $builder = $this->select('penggajian_proses_pembayaran.*, 
            coa.kode_akun as kode_akun_bank,
            coa.nama_akun as nama_akun_bank,
            creator.username as creator_name,
            finisher.username as finisher_name,
            jurnal.nomor_jurnal,
            mutasi_bank.kode_transaksi as kode_mutasi')
            ->join('coa', 'coa.id = penggajian_proses_pembayaran.coa_bank_id', 'left')
            ->join('users as creator', 'creator.id = penggajian_proses_pembayaran.created_by', 'left')
            ->join('users as finisher', 'finisher.id = penggajian_proses_pembayaran.selesai_by', 'left')
            ->join('jurnal', 'jurnal.id = penggajian_proses_pembayaran.jurnal_id', 'left')
            ->join('mutasi_bank', 'mutasi_bank.id = penggajian_proses_pembayaran.mutasi_bank_id', 'left');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('penggajian_proses_pembayaran.nomor_proses', $search)
                ->orLike('penggajian_proses_pembayaran.nama_proses', $search)
                ->orLike('penggajian_proses_pembayaran.keterangan', $search)
                ->groupEnd();
        }
        
        if (!empty($filters['periode_bulan'])) {
            $builder->where('penggajian_proses_pembayaran.periode_bulan', $filters['periode_bulan']);
        }
        
        if (!empty($filters['periode_tahun'])) {
            $builder->where('penggajian_proses_pembayaran.periode_tahun', $filters['periode_tahun']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('penggajian_proses_pembayaran.status', $filters['status']);
        }
        
        if (!empty($filters['metode_pembayaran'])) {
            $builder->where('penggajian_proses_pembayaran.metode_pembayaran', $filters['metode_pembayaran']);
        }
        
        if (!empty($filters['tanggal_mulai'])) {
            $builder->where('penggajian_proses_pembayaran.tanggal_proses >=', $filters['tanggal_mulai']);
        }
        
        if (!empty($filters['tanggal_selesai'])) {
            $builder->where('penggajian_proses_pembayaran.tanggal_proses <=', $filters['tanggal_selesai']);
        }
        
        $builder->orderBy('penggajian_proses_pembayaran.tanggal_proses', 'DESC')
                ->orderBy('penggajian_proses_pembayaran.nomor_proses', 'DESC');
        
        // Get total for pagination
        $total = $builder->countAllResults(false);
        
        // Get paginated results
        $offset = ($page - 1) * $perPage;
        $proses = $builder->limit($perPage, $offset)->findAll();
        
        return [
            'data' => $proses,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Get proses pembayaran by ID with details
     */
    public function getWithDetails($id)
    {
        $proses = $this->select('penggajian_proses_pembayaran.*, 
            coa.kode_akun as kode_akun_bank,
            coa.nama_akun as nama_akun_bank,
            coa.tipe_akun as tipe_akun_bank,
            creator.username as creator_name,
            creator.name as creator_fullname,
            finisher.username as finisher_name,
            finisher.name as finisher_fullname,
            jurnal.nomor_jurnal,
            jurnal.status as jurnal_status,
            mutasi_bank.kode_transaksi as kode_mutasi,
            mutasi_bank.status as mutasi_status')
            ->join('coa', 'coa.id = penggajian_proses_pembayaran.coa_bank_id', 'left')
            ->join('users as creator', 'creator.id = penggajian_proses_pembayaran.created_by', 'left')
            ->join('users as finisher', 'finisher.id = penggajian_proses_pembayaran.selesai_by', 'left')
            ->join('jurnal', 'jurnal.id = penggajian_proses_pembayaran.jurnal_id', 'left')
            ->join('mutasi_bank', 'mutasi_bank.id = penggajian_proses_pembayaran.mutasi_bank_id', 'left')
            ->where('penggajian_proses_pembayaran.id', $id)
            ->first();
        
        if (!$proses) {
            return null;
        }
        
        // Ambil detail pembayaran
        $detailModel = new PenggajianDetailPembayaranModel();
        $proses['details'] = $detailModel->getByProses($id);
        
        return $proses;
    }

    /**
     * Get proses pembayaran by periode
     */
    public function getByPeriode($bulan, $tahun)
    {
        return $this->where('periode_bulan', $bulan)
                    ->where('periode_tahun', $tahun)
                    ->orderBy('tanggal_proses', 'DESC')
                    ->findAll();
    }

    /**
     * Get proses pembayaran yang sudah selesai untuk periode tertentu
     */
    public function getCompletedByPeriode($bulan, $tahun)
    {
        return $this->where('periode_bulan', $bulan)
                    ->where('periode_tahun', $tahun)
                    ->where('status', 'Selesai')
                    ->first();
    }

    /**
     * Create proses pembayaran dari perhitungan gaji yang sudah disetujui
     */
    public function createFromPerhitungan($bulan, $tahun, $data)
    {
        // Ambil perhitungan gaji yang sudah disetujui
        $perhitunganModel = new PenggajianPerhitunganModel();
        $perhitungan = $perhitunganModel->getForPayment($bulan, $tahun);
        
        if (empty($perhitungan)) {
            throw new \RuntimeException('Tidak ada perhitungan gaji yang siap dibayar untuk periode ini');
        }
        
        // Hitung total
        $totalKaryawan = count($perhitungan);
        $totalNominal = array_sum(array_column($perhitungan, 'gaji_bersih'));
        
        // Siapkan data proses
        $prosesData = [
            'nama_proses' => $data['nama_proses'] ?? 'Pembayaran Gaji ' . $this->getNamaBulan($bulan) . ' ' . $tahun,
            'periode_bulan' => $bulan,
            'periode_tahun' => $tahun,
            'tanggal_proses' => $data['tanggal_proses'] ?? date('Y-m-d'),
            'tanggal_pembayaran' => $data['tanggal_pembayaran'] ?? date('Y-m-d'),
            'metode_pembayaran' => $data['metode_pembayaran'] ?? 'Transfer Bank',
            'coa_bank_id' => $data['coa_bank_id'] ?? null,
            'bank_pengirim' => $data['bank_pengirim'] ?? null,
            'total_karyawan' => $totalKaryawan,
            'total_nominal' => $totalNominal,
            'keterangan' => $data['keterangan'] ?? null,
            'status' => 'Draft'
        ];
        
        // Insert proses
        $prosesId = $this->insert($prosesData);
        
        if (!$prosesId) {
            throw new \RuntimeException('Gagal membuat proses pembayaran');
        }
        
        // Buat detail pembayaran
        $detailModel = new PenggajianDetailPembayaranModel();
        
        foreach ($perhitungan as $item) {
            $detailModel->insert([
                'proses_id' => $prosesId,
                'perhitungan_id' => $item['id'],
                'karyawan_id' => $item['karyawan_id'],
                'nomor_karyawan' => $item['nomor_karyawan'] ?? null,
                'nama_karyawan' => $item['nama_karyawan'],
                'bank' => $item['bank'] ?? null,
                'no_rekening' => $item['no_rekening'] ?? null,
                'nama_rekening' => $item['nama_rekening'] ?? null,
                'gaji_pokok' => $item['gaji_pokok'],
                'total_tunjangan' => $item['total_pendapatan'] - $item['gaji_pokok'] - ($item['upah_lembur'] ?? 0),
                'upah_lembur' => $item['upah_lembur'] ?? 0,
                'total_potongan' => $item['total_potongan'],
                'gaji_bersih' => $item['gaji_bersih'],
                'status_pembayaran' => 'Pending'
            ]);
        }
        
        return $this->find($prosesId);
    }

    /**
     * Update total setelah ada perubahan detail
     */
    public function updateTotal($id)
    {
        $detailModel = new PenggajianDetailPembayaranModel();
        $details = $detailModel->where('proses_id', $id)->findAll();
        
        $totalKaryawan = count($details);
        $totalNominal = array_sum(array_column($details, 'gaji_bersih'));
        
        return $this->update($id, [
            'total_karyawan' => $totalKaryawan,
            'total_nominal' => $totalNominal
        ]);
    }

    /**
     * Proses pembayaran (ubah status menjadi Diproses)
     */
    public function process($id)
    {
        $proses = $this->find($id);
        
        if (!$proses) {
            throw new \RuntimeException('Proses pembayaran tidak ditemukan');
        }
        
        if ($proses['status'] !== 'Draft') {
            throw new \RuntimeException('Hanya proses dengan status Draft yang dapat diproses');
        }
        
        // Validasi ada detail pembayaran
        $detailModel = new PenggajianDetailPembayaranModel();
        $details = $detailModel->where('proses_id', $id)->findAll();
        
        if (empty($details)) {
            throw new \RuntimeException('Tidak ada detail pembayaran untuk diproses');
        }
        
        return $this->update($id, ['status' => 'Diproses']);
    }

    /**
     * Selesaikan pembayaran
     */
    public function complete($id, $data = [])
    {
        $proses = $this->find($id);
        
        if (!$proses) {
            throw new \RuntimeException('Proses pembayaran tidak ditemukan');
        }
        
        if ($proses['status'] !== 'Diproses') {
            throw new \RuntimeException('Hanya proses dengan status Diproses yang dapat diselesaikan');
        }
        
        $updateData = [
            'status' => 'Selesai',
            'selesai_by' => session()->get('user_id'),
            'selesai_at' => date('Y-m-d H:i:s')
        ];
        
        if (!empty($data['file_export'])) {
            $updateData['file_export'] = $data['file_export'];
        }
        
        if (!empty($data['bukti_transfer'])) {
            $updateData['bukti_transfer'] = $data['bukti_transfer'];
        }
        
        if (!empty($data['mutasi_bank_id'])) {
            $updateData['mutasi_bank_id'] = $data['mutasi_bank_id'];
        }
        
        if (!empty($data['jurnal_id'])) {
            $updateData['jurnal_id'] = $data['jurnal_id'];
        }
        
        // Update status detail pembayaran
        $detailModel = new PenggajianDetailPembayaranModel();
        $details = $detailModel->where('proses_id', $id)->findAll();
        
        foreach ($details as $detail) {
            $detailModel->update($detail['id'], [
                'status_pembayaran' => 'Berhasil',
                'tanggal_pembayaran' => $proses['tanggal_pembayaran']
            ]);
        }
        
        return $this->update($id, $updateData);
    }

    /**
     * Batalkan proses pembayaran
     */
    public function cancel($id)
    {
        $proses = $this->find($id);
        
        if (!$proses) {
            throw new \RuntimeException('Proses pembayaran tidak ditemukan');
        }
        
        if ($proses['status'] === 'Selesai') {
            throw new \RuntimeException('Proses pembayaran yang sudah selesai tidak dapat dibatalkan');
        }
        
        return $this->update($id, ['status' => 'Dibatalkan']);
    }

    /**
     * Get statistics untuk dashboard
     */
    public function getStats($tahun = null, $bulan = null)
    {
        $builder = $this->where('status', 'Selesai');
        
        if ($tahun) {
            $builder->where('periode_tahun', $tahun);
        }
        
        if ($bulan) {
            $builder->where('periode_bulan', $bulan);
        }
        
        $stats = $builder->select("
                COUNT(*) as total_proses,
                SUM(total_karyawan) as total_karyawan,
                SUM(total_nominal) as total_nominal
            ")
            ->first();
        
        return $stats ?? [
            'total_proses' => 0,
            'total_karyawan' => 0,
            'total_nominal' => 0
        ];
    }

    /**
     * Get ringkasan per periode
     */
    public function getRingkasanPerPeriode($tahun = null)
    {
        $builder = $this->select("
                periode_bulan,
                periode_tahun,
                COUNT(*) as jumlah_proses,
                SUM(total_karyawan) as total_karyawan,
                SUM(total_nominal) as total_nominal
            ")
            ->where('status', 'Selesai')
            ->groupBy('periode_bulan, periode_tahun')
            ->orderBy('periode_tahun', 'DESC')
            ->orderBy('periode_bulan', 'DESC');
        
        if ($tahun) {
            $builder->where('periode_tahun', $tahun);
        }
        
        $result = $builder->findAll();
        
        foreach ($result as &$item) {
            $item['nama_bulan'] = $this->getNamaBulan($item['periode_bulan']);
        }
        
        return $result;
    }

    /**
     * Get COA bank options untuk dropdown
     */
    public function getCoaBankOptions()
    {
        $coaModel = new \App\Models\CoaModel();
        
        return $coaModel->where('is_header', 0)
                        ->where('is_active', 1)
                        ->like('kode_akun', '1-11', 'after')
                        ->orderBy('kode_akun', 'ASC')
                        ->findAll();
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
        $builder = $this->select('penggajian_proses_pembayaran.*, 
            coa.kode_akun as kode_akun_bank,
            coa.nama_akun as nama_akun_bank,
            creator.username as creator_name,
            finisher.username as finisher_name')
            ->join('coa', 'coa.id = penggajian_proses_pembayaran.coa_bank_id', 'left')
            ->join('users as creator', 'creator.id = penggajian_proses_pembayaran.created_by', 'left')
            ->join('users as finisher', 'finisher.id = penggajian_proses_pembayaran.selesai_by', 'left');
        
        if (!empty($filters['periode_bulan'])) {
            $builder->where('penggajian_proses_pembayaran.periode_bulan', $filters['periode_bulan']);
        }
        
        if (!empty($filters['periode_tahun'])) {
            $builder->where('penggajian_proses_pembayaran.periode_tahun', $filters['periode_tahun']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('penggajian_proses_pembayaran.status', $filters['status']);
        }
        
        $proses = $builder->orderBy('penggajian_proses_pembayaran.tanggal_proses', 'DESC')->findAll();
        
        $exportData = [];
        foreach ($proses as $item) {
            $exportData[] = [
                'Nomor Proses' => $item['nomor_proses'],
                'Nama Proses' => $item['nama_proses'],
                'Periode' => $this->getNamaBulan($item['periode_bulan']) . ' ' . $item['periode_tahun'],
                'Tanggal Proses' => $item['tanggal_proses'],
                'Tanggal Pembayaran' => $item['tanggal_pembayaran'],
                'Metode Pembayaran' => $item['metode_pembayaran'],
                'Bank Pengirim' => $item['bank_pengirim'] ?? '-',
                'Akun Bank' => ($item['kode_akun_bank'] ?? '') . ' - ' . ($item['nama_akun_bank'] ?? ''),
                'Jumlah Karyawan' => $item['total_karyawan'],
                'Total Nominal' => $item['total_nominal'],
                'Status' => $item['status'],
                'Keterangan' => $item['keterangan'] ?? '-',
                'Dibuat Oleh' => $item['creator_name'] ?? '-',
                'Dibuat Tanggal' => $item['created_at'],
                'Selesai Oleh' => $item['finisher_name'] ?? '-',
                'Selesai Tanggal' => $item['selesai_at'] ?? '-'
            ];
        }
        
        return $exportData;
    }

    /**
     * Check if process exists for period
     */
    public function existsForPeriod($bulan, $tahun)
    {
        return $this->where('periode_bulan', $bulan)
                    ->where('periode_tahun', $tahun)
                    ->where('status !=', 'Dibatalkan')
                    ->countAllResults() > 0;
    }

    /**
     * Get proses pembayaran yang sudah selesai dan belum memiliki jurnal
     */
    public function getCompletedWithoutJournal()
    {
        return $this->where('status', 'Selesai')
                    ->where('jurnal_id IS NULL')
                    ->findAll();
    }

    /**
     * Update jurnal ID setelah posting
     */
    public function updateJournal($id, $jurnalId)
    {
        return $this->update($id, ['jurnal_id' => $jurnalId]);
    }
}