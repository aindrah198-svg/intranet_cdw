<?php
namespace App\Models\Accounting;

use CodeIgniter\Model;

class ArsipPajakModel extends Model
{
    protected $table = 'arsip_pajak';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'kode_arsip',
        'jenis_pajak',
        'masa_pajak',
        'tahun_pajak',
        'nomor_dokumen',
        'judul',
        'deskripsi',
        'file_path',
        'file_type',
        'file_size',
        'tanggal_dokumen',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'kode_arsip' => 'required|is_unique[arsip_pajak.kode_arsip]',
        'jenis_pajak' => 'required|in_list[PPN,PPh 21,PPh 23,PPh 25,PPh 29,PPh Badan,Lainnya]',
        'masa_pajak' => 'permit_empty|numeric|greater_than[0]|less_than_equal_to[12]',
        'tahun_pajak' => 'required|numeric|min_length[4]|max_length[4]',
        'judul' => 'required',
        'file_path' => 'required',
        'file_type' => 'permit_empty|string',
        'file_size' => 'permit_empty|numeric',
        'tanggal_dokumen' => 'permit_empty|valid_date'
    ];

    protected $validationMessages = [
        'kode_arsip' => [
            'required' => 'Kode arsip harus diisi',
            'is_unique' => 'Kode arsip sudah terdaftar'
        ],
        'jenis_pajak' => [
            'required' => 'Jenis pajak harus dipilih',
            'in_list' => 'Jenis pajak tidak valid'
        ],
        'tahun_pajak' => [
            'required' => 'Tahun pajak harus diisi',
            'numeric' => 'Tahun pajak harus berupa angka'
        ],
        'judul' => [
            'required' => 'Judul arsip harus diisi'
        ],
        'file_path' => [
            'required' => 'File arsip harus diupload'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateKodeArsip', 'setDefaultValues', 'setCreatedBy'];
    protected $beforeUpdate = ['validateFile'];

    /**
     * Generate kode arsip otomatis
     * Format: ARSIP-YYYYMMDD-XXXX
     */
    protected function generateKodeArsip(array $data)
    {
        if (empty($data['data']['kode_arsip'])) {
            $prefix = 'ARSIP-' . date('Ymd') . '-';
            
            // Cari sequence terakhir untuk hari ini
            $last = $this->where('kode_arsip LIKE', $prefix . '%')
                         ->orderBy('kode_arsip', 'DESC')
                         ->first();
            
            if ($last) {
                $lastNum = substr($last['kode_arsip'], strlen($prefix));
                $nextNum = str_pad((int)$lastNum + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNum = '0001';
            }
            
            $data['data']['kode_arsip'] = $prefix . $nextNum;
        }
        
        return $data;
    }

    /**
     * Set default values
     */
    protected function setDefaultValues(array $data)
    {
        if (!isset($data['data']['tanggal_dokumen'])) {
            $data['data']['tanggal_dokumen'] = date('Y-m-d');
        }
        
        if (!isset($data['data']['tahun_pajak'])) {
            $data['data']['tahun_pajak'] = date('Y');
        }
        
        if (!isset($data['data']['masa_pajak']) && $data['data']['jenis_pajak'] !== 'PPh Badan') {
            $data['data']['masa_pajak'] = date('m');
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
     * Validasi file (pastikan file masih ada)
     */
    protected function validateFile(array $data)
    {
        if (isset($data['data']['file_path'])) {
            $filePath = $data['data']['file_path'];
            
            // Cek apakah file masih ada (untuk update)
            // Ini hanya warning, tidak throw exception
            if (!empty($filePath) && !file_exists(ROOTPATH . 'public/' . $filePath)) {
                // Tidak throw exception, hanya log
                log_message('warning', 'File arsip tidak ditemukan: ' . $filePath);
            }
        }
        
        return $data;
    }

    /**
     * Get all arsip pajak with filters
     */
    public function getAllWithFilters($filters = [], $perPage = 20, $page = 1)
    {
        $builder = $this->select('arsip_pajak.*, 
            creator.username as creator_name,
            creator.name as creator_fullname')
            ->join('users as creator', 'creator.id = arsip_pajak.created_by', 'left');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('arsip_pajak.kode_arsip', $search)
                ->orLike('arsip_pajak.judul', $search)
                ->orLike('arsip_pajak.deskripsi', $search)
                ->orLike('arsip_pajak.nomor_dokumen', $search)
                ->groupEnd();
        }
        
        if (!empty($filters['jenis_pajak'])) {
            $builder->where('arsip_pajak.jenis_pajak', $filters['jenis_pajak']);
        }
        
        if (!empty($filters['tahun'])) {
            $builder->where('arsip_pajak.tahun_pajak', $filters['tahun']);
        }
        
        if (!empty($filters['bulan'])) {
            $builder->where('arsip_pajak.masa_pajak', $filters['bulan']);
        }
        
        if (!empty($filters['tanggal_mulai'])) {
            $builder->where('arsip_pajak.tanggal_dokumen >=', $filters['tanggal_mulai']);
        }
        
        if (!empty($filters['tanggal_selesai'])) {
            $builder->where('arsip_pajak.tanggal_dokumen <=', $filters['tanggal_selesai']);
        }
        
        $builder->orderBy('arsip_pajak.tanggal_dokumen', 'DESC')
                ->orderBy('arsip_pajak.created_at', 'DESC');
        
        // Get total for pagination
        $total = $builder->countAllResults(false);
        
        // Get paginated results
        $offset = ($page - 1) * $perPage;
        $arsip = $builder->limit($perPage, $offset)->findAll();
        
        // Format file size
        foreach ($arsip as &$item) {
            $item['file_size_formatted'] = $this->formatFileSize($item['file_size'] ?? 0);
        }
        
        return [
            'data' => $arsip,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Get arsip by ID with details
     */
    public function getWithDetails($id)
    {
        $arsip = $this->select('arsip_pajak.*, 
            creator.username as creator_name,
            creator.name as creator_fullname')
            ->join('users as creator', 'creator.id = arsip_pajak.created_by', 'left')
            ->where('arsip_pajak.id', $id)
            ->first();
        
        if ($arsip) {
            $arsip['file_size_formatted'] = $this->formatFileSize($arsip['file_size'] ?? 0);
            $arsip['file_exists'] = file_exists(ROOTPATH . 'public/' . $arsip['file_path']);
        }
        
        return $arsip;
    }

    /**
     * Get arsip by jenis pajak
     */
    public function getByJenisPajak($jenisPajak, $tahun = null)
    {
        $builder = $this->where('jenis_pajak', $jenisPajak);
        
        if ($tahun) {
            $builder->where('tahun_pajak', $tahun);
        }
        
        return $builder->orderBy('tanggal_dokumen', 'DESC')->findAll();
    }

    /**
     * Get arsip by masa pajak
     */
    public function getByMasaPajak($jenisPajak, $bulan, $tahun)
    {
        return $this->where('jenis_pajak', $jenisPajak)
                    ->where('masa_pajak', $bulan)
                    ->where('tahun_pajak', $tahun)
                    ->orderBy('tanggal_dokumen', 'ASC')
                    ->findAll();
    }

    /**
     * Get arsip by tahun
     */
    public function getByTahun($tahun)
    {
        return $this->where('tahun_pajak', $tahun)
                    ->orderBy('tanggal_dokumen', 'ASC')
                    ->findAll();
    }

    /**
     * Get arsip by jenis dokumen (SPT, Bukti Setor, dll)
     */
    public function getByNomorDokumen($nomorDokumen)
    {
        return $this->like('nomor_dokumen', $nomorDokumen)
                    ->orderBy('tanggal_dokumen', 'DESC')
                    ->findAll();
    }

    /**
     * Get ringkasan arsip per jenis pajak
     */
    public function getRingkasanPerJenis($tahun = null)
    {
        $builder = $this->select("
                jenis_pajak,
                COUNT(*) as jumlah_arsip,
                SUM(file_size) as total_file_size
            ")
            ->groupBy('jenis_pajak')
            ->orderBy('jumlah_arsip', 'DESC');
        
        if ($tahun) {
            $builder->where('tahun_pajak', $tahun);
        }
        
        $result = $builder->findAll();
        
        foreach ($result as &$item) {
            $item['total_file_size_formatted'] = $this->formatFileSize($item['total_file_size'] ?? 0);
        }
        
        return $result;
    }

    /**
     * Get ringkasan arsip per tahun
     */
    public function getRingkasanPerTahun()
    {
        $result = $this->select("
                tahun_pajak,
                COUNT(*) as jumlah_arsip,
                SUM(file_size) as total_file_size,
                SUM(CASE WHEN jenis_pajak = 'PPN' THEN 1 ELSE 0 END) as jumlah_ppn,
                SUM(CASE WHEN jenis_pajak = 'PPh 21' THEN 1 ELSE 0 END) as jumlah_pph21,
                SUM(CASE WHEN jenis_pajak = 'PPh 23' THEN 1 ELSE 0 END) as jumlah_pph23,
                SUM(CASE WHEN jenis_pajak = 'PPh Badan' THEN 1 ELSE 0 END) as jumlah_pph_badan
            ")
            ->groupBy('tahun_pajak')
            ->orderBy('tahun_pajak', 'DESC')
            ->findAll();
        
        foreach ($result as &$item) {
            $item['total_file_size_formatted'] = $this->formatFileSize($item['total_file_size'] ?? 0);
        }
        
        return $result;
    }

    /**
     * Get statistics untuk dashboard
     */
    public function getStats($tahun = null)
    {
        $builder = $this->select("
                COUNT(*) as total_arsip,
                SUM(file_size) as total_file_size,
                COUNT(CASE WHEN file_type LIKE 'image/%' THEN 1 END) as jumlah_gambar,
                COUNT(CASE WHEN file_type = 'application/pdf' THEN 1 END) as jumlah_pdf,
                COUNT(CASE WHEN jenis_pajak = 'PPN' THEN 1 END) as jumlah_ppn,
                COUNT(CASE WHEN jenis_pajak = 'PPh 21' THEN 1 END) as jumlah_pph21,
                COUNT(CASE WHEN jenis_pajak = 'PPh 23' THEN 1 END) as jumlah_pph23,
                COUNT(CASE WHEN jenis_pajak = 'PPh Badan' THEN 1 END) as jumlah_pph_badan
            ");
        
        if ($tahun) {
            $builder->where('tahun_pajak', $tahun);
        }
        
        $stats = $builder->first();
        
        if ($stats) {
            $stats['total_file_size_formatted'] = $this->formatFileSize($stats['total_file_size'] ?? 0);
        }
        
        return $stats ?? [
            'total_arsip' => 0,
            'total_file_size' => 0,
            'total_file_size_formatted' => '0 B',
            'jumlah_gambar' => 0,
            'jumlah_pdf' => 0,
            'jumlah_ppn' => 0,
            'jumlah_pph21' => 0,
            'jumlah_pph23' => 0,
            'jumlah_pph_badan' => 0
        ];
    }

    /**
     * Upload arsip file
     */
    public function uploadArsip($file, $data = [])
    {
        if (!$file || !$file->isValid()) {
            throw new \RuntimeException('File tidak valid');
        }
        
        // Validasi ukuran file (maks 10MB)
        if ($file->getSize() > 10 * 1024 * 1024) {
            throw new \RuntimeException('Ukuran file maksimal 10MB');
        }
        
        // Validasi tipe file
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
        $fileType = $file->getMimeType();
        
        if (!in_array($fileType, $allowedTypes)) {
            throw new \RuntimeException('Tipe file tidak diizinkan. Hanya PDF, JPG, PNG, DOC, XLS');
        }
        
        // Generate nama file unik
        $extension = $file->getExtension();
        $fileName = 'arsip_' . date('Ymd_His') . '_' . uniqid() . '.' . $extension;
        $uploadPath = 'uploads/arsip-pajak/' . date('Y/m') . '/';
        
        // Buat direktori jika belum ada
        $fullPath = ROOTPATH . 'public/' . $uploadPath;
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
        }
        
        // Pindahkan file
        if (!$file->move($fullPath, $fileName)) {
            throw new \RuntimeException('Gagal mengupload file');
        }
        
        $filePath = $uploadPath . $fileName;
        
        // Simpan ke database
        $arsipData = [
            'jenis_pajak' => $data['jenis_pajak'],
            'masa_pajak' => $data['masa_pajak'] ?? ($data['jenis_pajak'] !== 'PPh Badan' ? date('m') : null),
            'tahun_pajak' => $data['tahun_pajak'] ?? date('Y'),
            'nomor_dokumen' => $data['nomor_dokumen'] ?? null,
            'judul' => $data['judul'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'file_path' => $filePath,
            'file_type' => $fileType,
            'file_size' => $file->getSize(),
            'tanggal_dokumen' => $data['tanggal_dokumen'] ?? date('Y-m-d')
        ];
        
        $id = $this->insert($arsipData);
        
        return $this->find($id);
    }

    /**
     * Delete arsip (also delete file)
     */
    public function deleteArsip($id)
    {
        $arsip = $this->find($id);
        
        if (!$arsip) {
            throw new \RuntimeException('Arsip tidak ditemukan');
        }
        
        // Hapus file fisik
        $filePath = ROOTPATH . 'public/' . $arsip['file_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        // Hapus dari database
        return $this->delete($id);
    }

    /**
     * Download arsip
     */
    public function downloadArsip($id)
    {
        $arsip = $this->find($id);
        
        if (!$arsip) {
            throw new \RuntimeException('Arsip tidak ditemukan');
        }
        
        $filePath = ROOTPATH . 'public/' . $arsip['file_path'];
        
        if (!file_exists($filePath)) {
            throw new \RuntimeException('File arsip tidak ditemukan');
        }
        
        return [
            'path' => $filePath,
            'name' => $arsip['judul'] . '_' . $arsip['kode_arsip'] . '.' . pathinfo($filePath, PATHINFO_EXTENSION),
            'mime' => $arsip['file_type']
        ];
    }

    /**
     * Get arsip untuk SPT Masa
     */
    public function getForSptMasa($jenisPajak, $bulan, $tahun)
    {
        return $this->where('jenis_pajak', $jenisPajak)
                    ->where('masa_pajak', $bulan)
                    ->where('tahun_pajak', $tahun)
                    ->orderBy('tanggal_dokumen', 'ASC')
                    ->findAll();
    }

    /**
     * Get arsip untuk SPT Tahunan
     */
    public function getForSptTahunan($jenisPajak, $tahun)
    {
        return $this->where('jenis_pajak', $jenisPajak)
                    ->where('tahun_pajak', $tahun)
                    ->where('masa_pajak IS NULL')
                    ->orderBy('tanggal_dokumen', 'ASC')
                    ->findAll();
    }

    /**
     * Check if arsip exists for period
     */
    public function existsForPeriod($jenisPajak, $bulan, $tahun, $judul = null)
    {
        $builder = $this->where('jenis_pajak', $jenisPajak)
                        ->where('tahun_pajak', $tahun);
        
        if ($jenisPajak !== 'PPh Badan') {
            $builder->where('masa_pajak', $bulan);
        }
        
        if ($judul) {
            $builder->like('judul', $judul);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Format file size
     */
    private function formatFileSize($bytes)
    {
        if ($bytes === 0) {
            return '0 B';
        }
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes, 1024));
        
        return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
    }

    /**
     * Export data untuk Excel
     */
    public function getExportData($filters = [])
    {
        $builder = $this->select('arsip_pajak.*, creator.username as creator_name')
                        ->join('users as creator', 'creator.id = arsip_pajak.created_by', 'left');
        
        if (!empty($filters['tahun'])) {
            $builder->where('arsip_pajak.tahun_pajak', $filters['tahun']);
        }
        
        if (!empty($filters['jenis_pajak'])) {
            $builder->where('arsip_pajak.jenis_pajak', $filters['jenis_pajak']);
        }
        
        $arsip = $builder->orderBy('arsip_pajak.tanggal_dokumen', 'DESC')->findAll();
        
        $exportData = [];
        foreach ($arsip as $item) {
            $exportData[] = [
                'Kode Arsip' => $item['kode_arsip'],
                'Jenis Pajak' => $item['jenis_pajak'],
                'Masa Pajak' => $item['masa_pajak'] ? $item['masa_pajak'] . '/' . $item['tahun_pajak'] : 'Tahunan ' . $item['tahun_pajak'],
                'Nomor Dokumen' => $item['nomor_dokumen'] ?? '-',
                'Judul' => $item['judul'],
                'Deskripsi' => $item['deskripsi'] ?? '-',
                'Tanggal Dokumen' => $item['tanggal_dokumen'],
                'Tipe File' => $item['file_type'],
                'Ukuran File' => $this->formatFileSize($item['file_size'] ?? 0),
                'Dibuat Oleh' => $item['creator_name'] ?? '-',
                'Dibuat Tanggal' => $item['created_at']
            ];
        }
        
        return $exportData;
    }

    /**
     * Get total storage used
     */
    public function getTotalStorageUsed()
    {
        $total = $this->selectSum('file_size')->first();
        $bytes = $total['file_size'] ?? 0;
        
        return [
            'bytes' => $bytes,
            'formatted' => $this->formatFileSize($bytes)
        ];
    }

    /**
     * Get arsip by date range
     */
    public function getByDateRange($startDate, $endDate)
    {
        return $this->where('tanggal_dokumen >=', $startDate)
                    ->where('tanggal_dokumen <=', $endDate)
                    ->orderBy('tanggal_dokumen', 'ASC')
                    ->findAll();
    }

    /**
     * Get latest arsip (for dashboard)
     */
    public function getLatest($limit = 10)
    {
        return $this->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}