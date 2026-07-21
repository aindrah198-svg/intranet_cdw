<?php
namespace App\Models\Accounting;

use CodeIgniter\Model;

class FakturPajakModel extends Model
{
    protected $table = 'faktur_pajak';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'nomor_faktur',
        'tanggal_faktur',
        'jenis_faktur',
        'invoice_id',
        'pembelian_id',
        'npwp_pengusaha',
        'nama_pengusaha',
        'alamat_pengusaha',
        'nilai_transaksi',
        'nilai_ppn',
        'tarif_ppn',
        'status_approval',
        'status_lapor',
        'masa_pajak',
        'tahun_pajak',
        'file_faktur',
        'keterangan',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'nomor_faktur' => 'required|is_unique[faktur_pajak.nomor_faktur]',
        'tanggal_faktur' => 'required|valid_date',
        'jenis_faktur' => 'required|in_list[Masukan,Keluaran]',
        'npwp_pengusaha' => 'required|min_length[15]|max_length[20]',
        'nama_pengusaha' => 'required',
        'nilai_transaksi' => 'required|numeric|greater_than[0]',
        'nilai_ppn' => 'required|numeric|greater_than_equal_to[0]',
        'tarif_ppn' => 'permit_empty|numeric|greater_than[0]|less_than_equal_to[100]',
        'status_approval' => 'permit_empty|in_list[Draft,Disetujui,Ditolak,Dibatalkan]',
        'status_lapor' => 'permit_empty|in_list[Belum Dilaporkan,Sudah Dilaporkan]',
        'masa_pajak' => 'permit_empty|numeric|greater_than[0]|less_than_equal_to[12]',
        'tahun_pajak' => 'permit_empty|numeric|min_length[4]|max_length[4]'
    ];

    protected $validationMessages = [
        'nomor_faktur' => [
            'required' => 'Nomor faktur harus diisi',
            'is_unique' => 'Nomor faktur sudah terdaftar'
        ],
        'tanggal_faktur' => [
            'required' => 'Tanggal faktur harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'jenis_faktur' => [
            'required' => 'Jenis faktur harus dipilih'
        ],
        'npwp_pengusaha' => [
            'required' => 'NPWP pengusaha harus diisi',
            'min_length' => 'NPWP harus 15-20 digit',
            'max_length' => 'NPWP harus 15-20 digit'
        ],
        'nama_pengusaha' => [
            'required' => 'Nama pengusaha harus diisi'
        ],
        'nilai_transaksi' => [
            'required' => 'Nilai transaksi harus diisi',
            'numeric' => 'Nilai transaksi harus berupa angka',
            'greater_than' => 'Nilai transaksi harus lebih dari 0'
        ],
        'nilai_ppn' => [
            'required' => 'Nilai PPN harus diisi',
            'numeric' => 'Nilai PPN harus berupa angka'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateNomorFaktur', 'setDefaultValues', 'setMasaPajak', 'validateNilaiPpn', 'setCreatedBy'];
    protected $beforeUpdate = ['validateStatusChange', 'validateNilaiPpn', 'validateNilaiPpnOnUpdate'];

    /**
     * Generate nomor faktur otomatis
     * Format: FAKTUR-YYYYMMDD-XXXX
     */
    protected function generateNomorFaktur(array $data)
    {
        if (empty($data['data']['nomor_faktur'])) {
            $tanggal = $data['data']['tanggal_faktur'] ?? date('Y-m-d');
            $prefix = 'FAKTUR-' . date('Ymd', strtotime($tanggal)) . '-';
            
            // Cari sequence terakhir untuk tanggal ini
            $last = $this->where('nomor_faktur LIKE', $prefix . '%')
                         ->orderBy('nomor_faktur', 'DESC')
                         ->first();
            
            if ($last) {
                $lastNum = substr($last['nomor_faktur'], strlen($prefix));
                $nextNum = str_pad((int)$lastNum + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNum = '0001';
            }
            
            $data['data']['nomor_faktur'] = $prefix . $nextNum;
        }
        
        return $data;
    }

    /**
     * Set default values
     */
    protected function setDefaultValues(array $data)
    {
        if (!isset($data['data']['status_approval'])) {
            $data['data']['status_approval'] = 'Draft';
        }
        
        if (!isset($data['data']['status_lapor'])) {
            $data['data']['status_lapor'] = 'Belum Dilaporkan';
        }
        
        if (!isset($data['data']['tarif_ppn'])) {
            // Ambil tarif PPN terbaru
            $tarifModel = new TarifPajakModel();
            $tarif = $tarifModel->getActiveTarif('PPN', $data['data']['tanggal_faktur'] ?? date('Y-m-d'));
            $data['data']['tarif_ppn'] = $tarif ? $tarif['persentase'] : 11;
        }
        
        return $data;
    }

    /**
     * Set masa pajak dari tanggal faktur
     */
    protected function setMasaPajak(array $data)
    {
        if (empty($data['data']['masa_pajak']) && !empty($data['data']['tanggal_faktur'])) {
            $data['data']['masa_pajak'] = date('m', strtotime($data['data']['tanggal_faktur']));
        }
        
        if (empty($data['data']['tahun_pajak']) && !empty($data['data']['tanggal_faktur'])) {
            $data['data']['tahun_pajak'] = date('Y', strtotime($data['data']['tanggal_faktur']));
        }
        
        return $data;
    }

    /**
     * Validasi nilai PPN
     */
    protected function validateNilaiPpn(array $data)
    {
        $nilaiTransaksi = $data['data']['nilai_transaksi'] ?? 0;
        $nilaiPpn = $data['data']['nilai_ppn'] ?? 0;
        $tarif = $data['data']['tarif_ppn'] ?? 11;
        
        // Hitung PPN yang seharusnya
        $expectedPpn = $nilaiTransaksi * ($tarif / 100);
        
        // Tolerance 1 (untuk pembulatan)
        if (abs($nilaiPpn - $expectedPpn) > 1) {
            throw new \RuntimeException('Nilai PPN tidak sesuai dengan tarif ' . $tarif . '% dari nilai transaksi. Nilai PPN seharusnya: ' . number_format($expectedPpn, 2));
        }
        
        return $data;
    }

    /**
     * Validasi nilai PPN pada update
     */
    protected function validateNilaiPpnOnUpdate(array $data)
    {
        $id = $data['id'][0] ?? null;
        
        if ($id) {
            $current = $this->find($id);
            
            if ($current && $current['status_approval'] !== 'Draft') {
                // Jika sudah disetujui, tidak bisa mengubah nilai
                if (isset($data['data']['nilai_transaksi']) && $data['data']['nilai_transaksi'] != $current['nilai_transaksi']) {
                    throw new \RuntimeException('Tidak dapat mengubah nilai transaksi pada faktur yang sudah disetujui');
                }
                if (isset($data['data']['nilai_ppn']) && $data['data']['nilai_ppn'] != $current['nilai_ppn']) {
                    throw new \RuntimeException('Tidak dapat mengubah nilai PPN pada faktur yang sudah disetujui');
                }
            }
        }
        
        return $this->validateNilaiPpn($data);
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
     * Validasi perubahan status
     */
    protected function validateStatusChange(array $data)
    {
        if (isset($data['data']['status_approval'])) {
            $id = $data['id'][0] ?? null;
            
            if ($id) {
                $current = $this->find($id);
                
                if ($current) {
                    $oldStatus = $current['status_approval'];
                    $newStatus = $data['data']['status_approval'];
                    
                    // Validasi urutan status
                    $validTransitions = [
                        'Draft' => ['Disetujui', 'Ditolak', 'Dibatalkan'],
                        'Disetujui' => ['Dibatalkan'],
                        'Ditolak' => ['Draft'],
                        'Dibatalkan' => []
                    ];
                    
                    if (isset($validTransitions[$oldStatus]) && !in_array($newStatus, $validTransitions[$oldStatus])) {
                        throw new \RuntimeException("Status tidak dapat berubah dari {$oldStatus} menjadi {$newStatus}");
                    }
                    
                    // Jika status berubah menjadi Disetujui
                    if ($newStatus === 'Disetujui' && $oldStatus === 'Draft') {
                        // Validasi data harus lengkap
                        if (empty($current['npwp_pengusaha']) || empty($current['nama_pengusaha'])) {
                            throw new \RuntimeException('Data NPWP dan nama pengusaha harus diisi sebelum menyetujui faktur');
                        }
                    }
                    
                    // Jika status berubah menjadi Ditolak
                    if ($newStatus === 'Ditolak' && $oldStatus === 'Draft') {
                        // Bisa ditolak
                    }
                }
            }
        }
        
        return $data;
    }

    /**
     * Get all faktur pajak with filters
     */
    public function getAllWithFilters($filters = [], $perPage = 20, $page = 1)
    {
        $builder = $this->select('faktur_pajak.*, 
            creator.username as creator_name,
            invoice.nomor_invoice,
            project.nama_project')
            ->join('users as creator', 'creator.id = faktur_pajak.created_by', 'left')
            ->join('invoice', 'invoice.id = faktur_pajak.invoice_id', 'left')
            ->join('project', 'project.id = invoice.project_id', 'left');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('faktur_pajak.nomor_faktur', $search)
                ->orLike('faktur_pajak.npwp_pengusaha', $search)
                ->orLike('faktur_pajak.nama_pengusaha', $search)
                ->orLike('invoice.nomor_invoice', $search)
                ->groupEnd();
        }
        
        if (!empty($filters['jenis_faktur'])) {
            $builder->where('faktur_pajak.jenis_faktur', $filters['jenis_faktur']);
        }
        
        if (!empty($filters['status_approval'])) {
            $builder->where('faktur_pajak.status_approval', $filters['status_approval']);
        }
        
        if (!empty($filters['status_lapor'])) {
            $builder->where('faktur_pajak.status_lapor', $filters['status_lapor']);
        }
        
        if (!empty($filters['tahun'])) {
            $builder->where('faktur_pajak.tahun_pajak', $filters['tahun']);
        }
        
        if (!empty($filters['bulan'])) {
            $builder->where('faktur_pajak.masa_pajak', $filters['bulan']);
        }
        
        if (!empty($filters['tanggal_mulai'])) {
            $builder->where('faktur_pajak.tanggal_faktur >=', $filters['tanggal_mulai']);
        }
        
        if (!empty($filters['tanggal_selesai'])) {
            $builder->where('faktur_pajak.tanggal_faktur <=', $filters['tanggal_selesai']);
        }
        
        $builder->orderBy('faktur_pajak.tanggal_faktur', 'DESC')
                ->orderBy('faktur_pajak.nomor_faktur', 'DESC');
        
        // Get total for pagination
        $total = $builder->countAllResults(false);
        
        // Get paginated results
        $offset = ($page - 1) * $perPage;
        $faktur = $builder->limit($perPage, $offset)->findAll();
        
        return [
            'data' => $faktur,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Get faktur pajak by ID with details
     */
    public function getWithDetails($id)
    {
        $faktur = $this->select('faktur_pajak.*, 
            creator.username as creator_name,
            creator.name as creator_fullname,
            invoice.nomor_invoice,
            invoice.tanggal_invoice,
            invoice.status_pembayaran,
            project.kode_project,
            project.nama_project,
            client.nama_perusahaan as client_nama,
            client.alamat as client_alamat')
            ->join('users as creator', 'creator.id = faktur_pajak.created_by', 'left')
            ->join('invoice', 'invoice.id = faktur_pajak.invoice_id', 'left')
            ->join('project', 'project.id = invoice.project_id', 'left')
            ->join('client', 'client.id = project.client_id', 'left')
            ->where('faktur_pajak.id', $id)
            ->first();
        
        if ($faktur && $faktur['jenis_faktur'] === 'Masukan') {
            // Ambil detail PPN Masukan
            $ppnMasukanModel = new PpnMasukanModel();
            $faktur['detail_ppn'] = $ppnMasukanModel->where('faktur_id', $id)->first();
        } elseif ($faktur && $faktur['jenis_faktur'] === 'Keluaran') {
            // Ambil detail PPN Keluaran
            $ppnKeluaranModel = new PpnKeluaranModel();
            $faktur['detail_ppn'] = $ppnKeluaranModel->where('faktur_id', $id)->first();
        }
        
        return $faktur;
    }

    /**
     * Get faktur by jenis
     */
    public function getByJenis($jenis)
    {
        return $this->where('jenis_faktur', $jenis)
                    ->orderBy('tanggal_faktur', 'DESC')
                    ->findAll();
    }

    /**
     * Get faktur by status
     */
    public function getByStatus($status)
    {
        return $this->where('status_approval', $status)
                    ->orderBy('tanggal_faktur', 'DESC')
                    ->findAll();
    }

    /**
     * Get faktur by masa pajak
     */
    public function getByMasaPajak($bulan, $tahun)
    {
        return $this->where('masa_pajak', $bulan)
                    ->where('tahun_pajak', $tahun)
                    ->orderBy('tanggal_faktur', 'ASC')
                    ->findAll();
    }

    /**
     * Get faktur by periode
     */
    public function getByPeriode($tanggalMulai, $tanggalSelesai)
    {
        return $this->where('tanggal_faktur >=', $tanggalMulai)
                    ->where('tanggal_faktur <=', $tanggalSelesai)
                    ->orderBy('tanggal_faktur', 'ASC')
                    ->findAll();
    }

    /**
     * Get faktur pending approval
     */
    public function getPendingApproval()
    {
        return $this->where('status_approval', 'Draft')
                    ->orderBy('tanggal_faktur', 'ASC')
                    ->findAll();
    }

    /**
     * Approve faktur pajak
     */
    public function approve($id)
    {
        $faktur = $this->find($id);
        
        if (!$faktur) {
            throw new \RuntimeException('Faktur pajak tidak ditemukan');
        }
        
        if ($faktur['status_approval'] !== 'Draft') {
            throw new \RuntimeException('Hanya faktur dengan status Draft yang dapat disetujui');
        }
        
        return $this->update($id, ['status_approval' => 'Disetujui']);
    }

    /**
     * Reject faktur pajak
     */
    public function reject($id, $keterangan = null)
    {
        $faktur = $this->find($id);
        
        if (!$faktur) {
            throw new \RuntimeException('Faktur pajak tidak ditemukan');
        }
        
        if ($faktur['status_approval'] !== 'Draft') {
            throw new \RuntimeException('Hanya faktur dengan status Draft yang dapat ditolak');
        }
        
        $updateData = ['status_approval' => 'Ditolak'];
        
        if ($keterangan) {
            $updateData['keterangan'] = ($faktur['keterangan'] ?? '') . "\n[DITOLAK] " . $keterangan;
        }
        
        return $this->update($id, $updateData);
    }

    /**
     * Cancel faktur pajak
     */
    public function cancel($id, $keterangan = null)
    {
        $faktur = $this->find($id);
        
        if (!$faktur) {
            throw new \RuntimeException('Faktur pajak tidak ditemukan');
        }
        
        if ($faktur['status_approval'] === 'Dibatalkan') {
            throw new \RuntimeException('Faktur sudah dibatalkan');
        }
        
        $updateData = ['status_approval' => 'Dibatalkan'];
        
        if ($keterangan) {
            $updateData['keterangan'] = ($faktur['keterangan'] ?? '') . "\n[DIBATALKAN] " . $keterangan;
        }
        
        return $this->update($id, $updateData);
    }

    /**
     * Mark as reported (sudah dilaporkan ke DJP)
     */
    public function markAsReported($id)
    {
        $faktur = $this->find($id);
        
        if (!$faktur) {
            throw new \RuntimeException('Faktur pajak tidak ditemukan');
        }
        
        if ($faktur['status_approval'] !== 'Disetujui') {
            throw new \RuntimeException('Hanya faktur yang sudah disetujui yang dapat dilaporkan');
        }
        
        return $this->update($id, ['status_lapor' => 'Sudah Dilaporkan']);
    }

    /**
     * Get total nilai PPN per masa pajak
     */
    public function getTotalPpnPerMasa($tahun = null)
    {
        $builder = $this->select("
                masa_pajak,
                tahun_pajak,
                jenis_faktur,
                COUNT(*) as jumlah_faktur,
                SUM(nilai_transaksi) as total_transaksi,
                SUM(nilai_ppn) as total_ppn
            ")
            ->where('status_approval', 'Disetujui')
            ->groupBy('masa_pajak, tahun_pajak, jenis_faktur')
            ->orderBy('tahun_pajak', 'DESC')
            ->orderBy('masa_pajak', 'DESC');
        
        if ($tahun) {
            $builder->where('tahun_pajak', $tahun);
        }
        
        return $builder->findAll();
    }

    /**
     * Get ringkasan PPN per tahun
     */
    public function getRingkasanPpnPerTahun($tahun = null)
    {
        $builder = $this->select("
                tahun_pajak,
                jenis_faktur,
                COUNT(*) as jumlah_faktur,
                SUM(nilai_transaksi) as total_transaksi,
                SUM(nilai_ppn) as total_ppn
            ")
            ->where('status_approval', 'Disetujui')
            ->groupBy('tahun_pajak, jenis_faktur')
            ->orderBy('tahun_pajak', 'DESC');
        
        if ($tahun) {
            $builder->where('tahun_pajak', $tahun);
        }
        
        return $builder->findAll();
    }

    /**
     * Get ringkasan PPN per status
     */
    public function getRingkasanPerStatus()
    {
        return $this->select("
                status_approval,
                COUNT(*) as jumlah_faktur,
                SUM(nilai_transaksi) as total_transaksi,
                SUM(nilai_ppn) as total_ppn
            ")
            ->groupBy('status_approval')
            ->orderBy('status_approval', 'ASC')
            ->findAll();
    }

    /**
     * Get statistics untuk dashboard
     */
    public function getStats($tahun = null)
    {
        $builder = $this->where('status_approval', 'Disetujui');
        
        if ($tahun) {
            $builder->where('tahun_pajak', $tahun);
        }
        
        $stats = $builder->select("
                COUNT(*) as total_faktur,
                SUM(CASE WHEN jenis_faktur = 'Masukan' THEN 1 ELSE 0 END) as jumlah_masukan,
                SUM(CASE WHEN jenis_faktur = 'Keluaran' THEN 1 ELSE 0 END) as jumlah_keluaran,
                SUM(CASE WHEN jenis_faktur = 'Masukan' THEN nilai_ppn ELSE 0 END) as total_ppn_masukan,
                SUM(CASE WHEN jenis_faktur = 'Keluaran' THEN nilai_ppn ELSE 0 END) as total_ppn_keluaran,
                SUM(CASE WHEN status_lapor = 'Sudah Dilaporkan' THEN 1 ELSE 0 END) as jumlah_dilaporkan,
                SUM(CASE WHEN status_lapor = 'Belum Dilaporkan' THEN 1 ELSE 0 END) as jumlah_belum_dilaporkan
            ")
            ->first();
        
        if (!$stats) {
            return [
                'total_faktur' => 0,
                'jumlah_masukan' => 0,
                'jumlah_keluaran' => 0,
                'total_ppn_masukan' => 0,
                'total_ppn_keluaran' => 0,
                'jumlah_dilaporkan' => 0,
                'jumlah_belum_dilaporkan' => 0
            ];
        }
        
        $stats['ppn_kurang_bayar'] = $stats['total_ppn_keluaran'] - $stats['total_ppn_masukan'];
        
        return $stats;
    }

    /**
     * Get faktur by invoice
     */
    public function getByInvoice($invoiceId)
    {
        return $this->where('invoice_id', $invoiceId)->first();
    }

    /**
     * Create faktur from invoice
     */
    public function createFromInvoice($invoiceId, $data = [])
    {
        $invoiceModel = new \App\Models\InvoiceModel();
        $invoice = $invoiceModel->getWithDetails($invoiceId);
        
        if (!$invoice) {
            throw new \RuntimeException('Invoice tidak ditemukan');
        }
        
        // Ambil tarif PPN
        $tarifModel = new TarifPajakModel();
        $tarif = $tarifModel->getActiveTarif('PPN', $data['tanggal_faktur'] ?? date('Y-m-d'));
        $tarifPpn = $tarif ? $tarif['persentase'] : 11;
        
        $nilaiTransaksi = $invoice['total'];
        $nilaiPpn = $nilaiTransaksi * ($tarifPpn / 100);
        
        $fakturData = [
            'tanggal_faktur' => $data['tanggal_faktur'] ?? date('Y-m-d'),
            'jenis_faktur' => 'Keluaran',
            'invoice_id' => $invoiceId,
            'npwp_pengusaha' => $data['npwp_pengusaha'] ?? $invoice['client_npwp'] ?? '',
            'nama_pengusaha' => $data['nama_pengusaha'] ?? $invoice['client_nama'] ?? '',
            'alamat_pengusaha' => $data['alamat_pengusaha'] ?? $invoice['client_alamat'] ?? '',
            'nilai_transaksi' => $nilaiTransaksi,
            'nilai_ppn' => $nilaiPpn,
            'tarif_ppn' => $tarifPpn,
            'status_approval' => 'Draft',
            'keterangan' => $data['keterangan'] ?? null
        ];
        
        return $this->insert($fakturData);
    }

    /**
     * Export data untuk Excel
     */
    public function getExportData($filters = [])
    {
        $builder = $this->select('faktur_pajak.*, 
            invoice.nomor_invoice,
            project.nama_project')
            ->join('invoice', 'invoice.id = faktur_pajak.invoice_id', 'left')
            ->join('project', 'project.id = invoice.project_id', 'left');
        
        if (!empty($filters['jenis_faktur'])) {
            $builder->where('faktur_pajak.jenis_faktur', $filters['jenis_faktur']);
        }
        
        if (!empty($filters['tahun'])) {
            $builder->where('faktur_pajak.tahun_pajak', $filters['tahun']);
        }
        
        if (!empty($filters['status_approval'])) {
            $builder->where('faktur_pajak.status_approval', $filters['status_approval']);
        }
        
        $faktur = $builder->orderBy('faktur_pajak.tanggal_faktur', 'DESC')->findAll();
        
        $exportData = [];
        foreach ($faktur as $item) {
            $exportData[] = [
                'Nomor Faktur' => $item['nomor_faktur'],
                'Tanggal Faktur' => $item['tanggal_faktur'],
                'Jenis Faktur' => $item['jenis_faktur'],
                'NPWP' => $item['npwp_pengusaha'],
                'Nama Pengusaha' => $item['nama_pengusaha'],
                'Alamat' => $item['alamat_pengusaha'] ?? '-',
                'Nilai Transaksi' => $item['nilai_transaksi'],
                'Tarif PPN' => $item['tarif_ppn'] . '%',
                'Nilai PPN' => $item['nilai_ppn'],
                'Masa Pajak' => $item['masa_pajak'] . '/' . $item['tahun_pajak'],
                'No Invoice' => $item['nomor_invoice'] ?? '-',
                'Proyek' => $item['nama_project'] ?? '-',
                'Status Approval' => $item['status_approval'],
                'Status Lapor' => $item['status_lapor'],
                'Keterangan' => $item['keterangan'] ?? '-'
            ];
        }
        
        return $exportData;
    }

    /**
     * Check if faktur exists for invoice
     */
    public function existsForInvoice($invoiceId)
    {
        return $this->where('invoice_id', $invoiceId)->countAllResults() > 0;
    }

    /**
     * Get total PPN untuk SPT Masa
     */
    public function getTotalPpnForSpt($masaPajak, $tahunPajak)
    {
        $faktur = $this->where('masa_pajak', $masaPajak)
                       ->where('tahun_pajak', $tahunPajak)
                       ->where('status_approval', 'Disetujui')
                       ->findAll();
        
        $totalMasukan = array_sum(array_column(array_filter($faktur, function($item) {
            return $item['jenis_faktur'] === 'Masukan';
        }), 'nilai_ppn'));
        
        $totalKeluaran = array_sum(array_column(array_filter($faktur, function($item) {
            return $item['jenis_faktur'] === 'Keluaran';
        }), 'nilai_ppn'));
        
        return [
            'total_ppn_masukan' => $totalMasukan,
            'total_ppn_keluaran' => $totalKeluaran,
            'ppn_kurang_bayar' => $totalKeluaran - $totalMasukan
        ];
    }
}