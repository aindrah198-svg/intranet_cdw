<?php
namespace App\Models\Accounting;

use CodeIgniter\Model;

class PpnKeluaranModel extends Model
{
    protected $table = 'ppn_keluaran';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'faktur_id',
        'tanggal_penjualan',
        'customer',
        'npwp_customer',
        'nomor_invoice',
        'nilai_dpp',
        'nilai_ppn',
        'masa_pajak',
        'tahun_pajak',
        'status_setor',
        'tanggal_setor'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'faktur_id' => 'required|is_natural_no_zero',
        'tanggal_penjualan' => 'required|valid_date',
        'customer' => 'required',
        'nilai_dpp' => 'required|numeric|greater_than[0]',
        'nilai_ppn' => 'required|numeric|greater_than[0]',
        'masa_pajak' => 'required|numeric|greater_than[0]|less_than_equal_to[12]',
        'tahun_pajak' => 'required|numeric|min_length[4]|max_length[4]',
        'status_setor' => 'permit_empty|in_list[Belum Disetor,Sudah Disetor]',
        'tanggal_setor' => 'permit_empty|valid_date'
    ];

    protected $validationMessages = [
        'faktur_id' => [
            'required' => 'ID faktur harus diisi'
        ],
        'tanggal_penjualan' => [
            'required' => 'Tanggal penjualan harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'customer' => [
            'required' => 'Nama customer harus diisi'
        ],
        'nilai_dpp' => [
            'required' => 'Nilai DPP harus diisi',
            'numeric' => 'Nilai DPP harus berupa angka',
            'greater_than' => 'Nilai DPP harus lebih dari 0'
        ],
        'nilai_ppn' => [
            'required' => 'Nilai PPN harus diisi',
            'numeric' => 'Nilai PPN harus berupa angka',
            'greater_than' => 'Nilai PPN harus lebih dari 0'
        ],
        'masa_pajak' => [
            'required' => 'Masa pajak harus diisi'
        ],
        'tahun_pajak' => [
            'required' => 'Tahun pajak harus diisi'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = ['setDefaultValues', 'validateFaktur', 'validateNilaiPpn'];
    protected $beforeUpdate = ['validateStatusChange', 'validateNilaiPpn'];

    /**
     * Set default values
     */
    protected function setDefaultValues(array $data)
    {
        if (!isset($data['data']['status_setor'])) {
            $data['data']['status_setor'] = 'Belum Disetor';
        }
        
        return $data;
    }

    /**
     * Validasi faktur terkait
     */
    protected function validateFaktur(array $data)
    {
        $fakturId = $data['data']['faktur_id'] ?? null;
        
        if ($fakturId) {
            $fakturModel = new FakturPajakModel();
            $faktur = $fakturModel->find($fakturId);
            
            if (!$faktur) {
                throw new \RuntimeException('Faktur pajak tidak ditemukan');
            }
            
            if ($faktur['jenis_faktur'] !== 'Keluaran') {
                throw new \RuntimeException('Faktur yang dipilih bukan faktur keluaran');
            }
            
            // Set default dari faktur jika belum diisi
            if (empty($data['data']['masa_pajak']) && !empty($faktur['masa_pajak'])) {
                $data['data']['masa_pajak'] = $faktur['masa_pajak'];
            }
            
            if (empty($data['data']['tahun_pajak']) && !empty($faktur['tahun_pajak'])) {
                $data['data']['tahun_pajak'] = $faktur['tahun_pajak'];
            }
            
            if (empty($data['data']['nilai_dpp']) && !empty($faktur['nilai_transaksi'])) {
                $data['data']['nilai_dpp'] = $faktur['nilai_transaksi'];
            }
            
            if (empty($data['data']['nilai_ppn']) && !empty($faktur['nilai_ppn'])) {
                $data['data']['nilai_ppn'] = $faktur['nilai_ppn'];
            }
            
            if (empty($data['data']['customer']) && !empty($faktur['nama_pengusaha'])) {
                $data['data']['customer'] = $faktur['nama_pengusaha'];
            }
            
            if (empty($data['data']['npwp_customer']) && !empty($faktur['npwp_pengusaha'])) {
                $data['data']['npwp_customer'] = $faktur['npwp_pengusaha'];
            }
        }
        
        return $data;
    }

    /**
     * Validasi nilai PPN (DPP x tarif)
     */
    protected function validateNilaiPpn(array $data)
    {
        $nilaiDpp = $data['data']['nilai_dpp'] ?? 0;
        $nilaiPpn = $data['data']['nilai_ppn'] ?? 0;
        $masaPajak = $data['data']['masa_pajak'] ?? date('m');
        $tahunPajak = $data['data']['tahun_pajak'] ?? date('Y');
        
        // Ambil tarif PPN yang berlaku pada masa pajak
        $tarifModel = new TarifPajakModel();
        $tanggal = date("$tahunPajak-$masaPajak-01");
        $tarif = $tarifModel->getActiveTarif('PPN', $tanggal);
        $tarifPpn = $tarif ? $tarif['persentase'] : 11;
        
        $expectedPpn = $nilaiDpp * ($tarifPpn / 100);
        
        // Tolerance 1 (untuk pembulatan)
        if (abs($nilaiPpn - $expectedPpn) > 1) {
            throw new \RuntimeException('Nilai PPN tidak sesuai dengan tarif ' . $tarifPpn . '% dari nilai DPP. Nilai PPN seharusnya: ' . number_format($expectedPpn, 2));
        }
        
        return $data;
    }

    /**
     * Validasi perubahan status
     */
    protected function validateStatusChange(array $data)
    {
        if (isset($data['data']['status_setor'])) {
            $id = $data['id'][0] ?? null;
            
            if ($id) {
                $current = $this->find($id);
                
                if ($current) {
                    $oldStatus = $current['status_setor'];
                    $newStatus = $data['data']['status_setor'];
                    
                    // Jika status berubah menjadi Sudah Disetor
                    if ($newStatus === 'Sudah Disetor' && $oldStatus === 'Belum Disetor') {
                        if (empty($data['data']['tanggal_setor'])) {
                            $data['data']['tanggal_setor'] = date('Y-m-d');
                        }
                    }
                    
                    // Jika sudah disetor, tidak bisa diubah
                    if ($oldStatus === 'Sudah Disetor' && $newStatus !== 'Sudah Disetor') {
                        throw new \RuntimeException('PPN yang sudah disetor tidak dapat diubah statusnya');
                    }
                }
            }
        }
        
        return $data;
    }

    /**
     * Get all PPN keluaran with filters
     */
    public function getAllWithFilters($filters = [], $perPage = 20, $page = 1)
    {
        $builder = $this->select('ppn_keluaran.*, 
            faktur_pajak.nomor_faktur,
            faktur_pajak.tanggal_faktur,
            faktur_pajak.npwp_pengusaha,
            faktur_pajak.nama_pengusaha,
            faktur_pajak.alamat_pengusaha,
            faktur_pajak.file_faktur,
            faktur_pajak.status_approval,
            creator.username as creator_name,
            invoice.nomor_invoice as nomor_invoice_penjualan,
            project.nama_project')
            ->join('faktur_pajak', 'faktur_pajak.id = ppn_keluaran.faktur_id', 'left')
            ->join('invoice', 'invoice.id = ppn_keluaran.nomor_invoice', 'left')
            ->join('project', 'project.id = invoice.project_id', 'left')
            ->join('users as creator', 'creator.id = ppn_keluaran.created_by', 'left');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('ppn_keluaran.customer', $search)
                ->orLike('ppn_keluaran.npwp_customer', $search)
                ->orLike('ppn_keluaran.nomor_invoice', $search)
                ->orLike('faktur_pajak.nomor_faktur', $search)
                ->groupEnd();
        }
        
        if (!empty($filters['status_setor'])) {
            $builder->where('ppn_keluaran.status_setor', $filters['status_setor']);
        }
        
        if (!empty($filters['tahun'])) {
            $builder->where('ppn_keluaran.tahun_pajak', $filters['tahun']);
        }
        
        if (!empty($filters['bulan'])) {
            $builder->where('ppn_keluaran.masa_pajak', $filters['bulan']);
        }
        
        if (!empty($filters['tanggal_mulai'])) {
            $builder->where('ppn_keluaran.tanggal_penjualan >=', $filters['tanggal_mulai']);
        }
        
        if (!empty($filters['tanggal_selesai'])) {
            $builder->where('ppn_keluaran.tanggal_penjualan <=', $filters['tanggal_selesai']);
        }
        
        $builder->orderBy('ppn_keluaran.tanggal_penjualan', 'DESC')
                ->orderBy('ppn_keluaran.id', 'DESC');
        
        // Get total for pagination
        $total = $builder->countAllResults(false);
        
        // Get paginated results
        $offset = ($page - 1) * $perPage;
        $ppnKeluaran = $builder->limit($perPage, $offset)->findAll();
        
        return [
            'data' => $ppnKeluaran,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Get PPN keluaran by ID with details
     */
    public function getWithDetails($id)
    {
        $ppnKeluaran = $this->select('ppn_keluaran.*, 
            faktur_pajak.nomor_faktur,
            faktur_pajak.tanggal_faktur,
            faktur_pajak.npwp_pengusaha,
            faktur_pajak.nama_pengusaha,
            faktur_pajak.alamat_pengusaha,
            faktur_pajak.file_faktur,
            faktur_pajak.status_approval,
            faktur_pajak.keterangan as faktur_keterangan,
            creator.username as creator_name,
            creator.name as creator_fullname,
            invoice.nomor_invoice as nomor_invoice_penjualan,
            invoice.tanggal_invoice,
            invoice.status_pembayaran,
            project.kode_project,
            project.nama_project,
            client.nama_perusahaan as client_nama')
            ->join('faktur_pajak', 'faktur_pajak.id = ppn_keluaran.faktur_id', 'left')
            ->join('invoice', 'invoice.id = ppn_keluaran.nomor_invoice', 'left')
            ->join('project', 'project.id = invoice.project_id', 'left')
            ->join('client', 'client.id = project.client_id', 'left')
            ->join('users as creator', 'creator.id = ppn_keluaran.created_by', 'left')
            ->where('ppn_keluaran.id', $id)
            ->first();
        
        return $ppnKeluaran;
    }

    /**
     * Get PPN keluaran by faktur ID
     */
    public function getByFaktur($fakturId)
    {
        return $this->where('faktur_id', $fakturId)->first();
    }

    /**
     * Get PPN keluaran by customer
     */
    public function getByCustomer($customer, $tahun = null)
    {
        $builder = $this->where('customer', $customer);
        
        if ($tahun) {
            $builder->where('tahun_pajak', $tahun);
        }
        
        return $builder->orderBy('tanggal_penjualan', 'DESC')->findAll();
    }

    /**
     * Get PPN keluaran by masa pajak
     */
    public function getByMasaPajak($bulan, $tahun)
    {
        return $this->where('masa_pajak', $bulan)
                    ->where('tahun_pajak', $tahun)
                    ->orderBy('tanggal_penjualan', 'ASC')
                    ->findAll();
    }

    /**
     * Get PPN keluaran yang belum disetor
     */
    public function getBelumDisetor()
    {
        return $this->where('status_setor', 'Belum Disetor')
                    ->orderBy('tanggal_penjualan', 'ASC')
                    ->findAll();
    }

    /**
     * Get PPN keluaran yang sudah disetor per periode
     */
    public function getSudahDisetorByPeriode($bulan, $tahun)
    {
        return $this->where('status_setor', 'Sudah Disetor')
                    ->where('masa_pajak', $bulan)
                    ->where('tahun_pajak', $tahun)
                    ->orderBy('tanggal_setor', 'ASC')
                    ->findAll();
    }

    /**
     * Mark PPN as paid (setor)
     */
    public function markAsPaid($id, $tanggalSetor = null)
    {
        $ppnKeluaran = $this->find($id);
        
        if (!$ppnKeluaran) {
            throw new \RuntimeException('Data PPN keluaran tidak ditemukan');
        }
        
        if ($ppnKeluaran['status_setor'] !== 'Belum Disetor') {
            throw new \RuntimeException('Hanya PPN dengan status Belum Disetor yang dapat ditandai sudah disetor');
        }
        
        return $this->update($id, [
            'status_setor' => 'Sudah Disetor',
            'tanggal_setor' => $tanggalSetor ?? date('Y-m-d')
        ]);
    }

    /**
     * Mark multiple PPN as paid (batch setor)
     */
    public function batchMarkAsPaid($ids, $tanggalSetor = null)
    {
        $updated = 0;
        $failed = [];
        
        foreach ($ids as $id) {
            try {
                $this->markAsPaid($id, $tanggalSetor);
                $updated++;
            } catch (\Exception $e) {
                $failed[] = ['id' => $id, 'error' => $e->getMessage()];
            }
        }
        
        return [
            'success' => $updated,
            'failed' => $failed,
            'total' => count($ids)
        ];
    }

    /**
     * Get total PPN keluaran per masa pajak
     */
    public function getTotalPerMasa($tahun = null)
    {
        $builder = $this->select("
                masa_pajak,
                tahun_pajak,
                COUNT(*) as jumlah_faktur,
                SUM(nilai_dpp) as total_dpp,
                SUM(nilai_ppn) as total_ppn,
                SUM(CASE WHEN status_setor = 'Sudah Disetor' THEN nilai_ppn ELSE 0 END) as total_ppn_disetor,
                SUM(CASE WHEN status_setor = 'Belum Disetor' THEN nilai_ppn ELSE 0 END) as total_ppn_belum_disetor
            ")
            ->groupBy('masa_pajak, tahun_pajak')
            ->orderBy('tahun_pajak', 'DESC')
            ->orderBy('masa_pajak', 'DESC');
        
        if ($tahun) {
            $builder->where('tahun_pajak', $tahun);
        }
        
        return $builder->findAll();
    }

    /**
     * Get total PPN keluaran per customer
     */
    public function getTotalPerCustomer($tahun = null)
    {
        $builder = $this->select("
                customer,
                npwp_customer,
                COUNT(*) as jumlah_faktur,
                SUM(nilai_dpp) as total_dpp,
                SUM(nilai_ppn) as total_ppn,
                SUM(CASE WHEN status_setor = 'Sudah Disetor' THEN nilai_ppn ELSE 0 END) as total_ppn_disetor
            ")
            ->groupBy('customer, npwp_customer')
            ->orderBy('total_ppn', 'DESC');
        
        if ($tahun) {
            $builder->where('tahun_pajak', $tahun);
        }
        
        return $builder->findAll();
    }

    /**
     * Get ringkasan per status setor
     */
    public function getRingkasanPerStatus($tahun = null)
    {
        $builder = $this->select("
                status_setor,
                COUNT(*) as jumlah_faktur,
                SUM(nilai_dpp) as total_dpp,
                SUM(nilai_ppn) as total_ppn
            ")
            ->groupBy('status_setor');
        
        if ($tahun) {
            $builder->where('tahun_pajak', $tahun);
        }
        
        return $builder->findAll();
    }

    /**
     * Get statistics untuk dashboard
     */
    public function getStats($tahun = null)
    {
        $builder = $this->select("
                COUNT(*) as total_faktur,
                SUM(nilai_dpp) as total_dpp,
                SUM(nilai_ppn) as total_ppn,
                SUM(CASE WHEN status_setor = 'Sudah Disetor' THEN nilai_ppn ELSE 0 END) as total_disetor,
                SUM(CASE WHEN status_setor = 'Belum Disetor' THEN nilai_ppn ELSE 0 END) as total_belum_disetor,
                COUNT(CASE WHEN status_setor = 'Sudah Disetor' THEN 1 END) as jumlah_disetor,
                COUNT(CASE WHEN status_setor = 'Belum Disetor' THEN 1 END) as jumlah_belum_disetor
            ");
        
        if ($tahun) {
            $builder->where('tahun_pajak', $tahun);
        }
        
        $stats = $builder->first();
        
        return $stats ?? [
            'total_faktur' => 0,
            'total_dpp' => 0,
            'total_ppn' => 0,
            'total_disetor' => 0,
            'total_belum_disetor' => 0,
            'jumlah_disetor' => 0,
            'jumlah_belum_disetor' => 0
        ];
    }

    /**
     * Get total PPN yang harus disetor per masa pajak
     */
    public function getPpnTerutang($masaPajak, $tahunPajak)
    {
        // Ambil PPN keluaran
        $keluaran = $this->select('SUM(nilai_ppn) as total')
                         ->where('masa_pajak', $masaPajak)
                         ->where('tahun_pajak', $tahunPajak)
                         ->first();
        
        // Ambil PPN masukan yang sudah dikreditkan
        $ppnMasukanModel = new PpnMasukanModel();
        $masukan = $ppnMasukanModel->select('SUM(nilai_ppn) as total')
                                   ->where('masa_pajak', $masaPajak)
                                   ->where('tahun_pajak', $tahunPajak)
                                   ->where('status_kredit', 'Dikreditkan')
                                   ->first();
        
        $totalKeluaran = $keluaran['total'] ?? 0;
        $totalMasukan = $masukan['total'] ?? 0;
        
        return [
            'total_ppn_keluaran' => $totalKeluaran,
            'total_ppn_masukan' => $totalMasukan,
            'ppn_kurang_bayar' => $totalKeluaran - $totalMasukan,
            'ppn_lebih_bayar' => max(0, $totalMasukan - $totalKeluaran)
        ];
    }

    /**
     * Create PPN keluaran from faktur
     */
    public function createFromFaktur($fakturId, $data = [])
    {
        $fakturModel = new FakturPajakModel();
        $faktur = $fakturModel->find($fakturId);
        
        if (!$faktur) {
            throw new \RuntimeException('Faktur pajak tidak ditemukan');
        }
        
        if ($faktur['jenis_faktur'] !== 'Keluaran') {
            throw new \RuntimeException('Faktur yang dipilih bukan faktur keluaran');
        }
        
        $ppnData = [
            'faktur_id' => $fakturId,
            'tanggal_penjualan' => $data['tanggal_penjualan'] ?? $faktur['tanggal_faktur'],
            'customer' => $data['customer'] ?? $faktur['nama_pengusaha'],
            'npwp_customer' => $data['npwp_customer'] ?? $faktur['npwp_pengusaha'],
            'nomor_invoice' => $data['nomor_invoice'] ?? null,
            'nilai_dpp' => $data['nilai_dpp'] ?? $faktur['nilai_transaksi'],
            'nilai_ppn' => $data['nilai_ppn'] ?? $faktur['nilai_ppn'],
            'masa_pajak' => $data['masa_pajak'] ?? $faktur['masa_pajak'],
            'tahun_pajak' => $data['tahun_pajak'] ?? $faktur['tahun_pajak'],
            'status_setor' => $data['status_setor'] ?? 'Belum Disetor'
        ];
        
        return $this->insert($ppnData);
    }

    /**
     * Create PPN keluaran from invoice
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
        $tarif = $tarifModel->getActiveTarif('PPN', $data['tanggal_penjualan'] ?? date('Y-m-d'));
        $tarifPpn = $tarif ? $tarif['persentase'] : 11;
        
        $nilaiDpp = $invoice['total'];
        $nilaiPpn = $nilaiDpp * ($tarifPpn / 100);
        
        $ppnData = [
            'tanggal_penjualan' => $data['tanggal_penjualan'] ?? $invoice['tanggal_invoice'],
            'customer' => $data['customer'] ?? $invoice['client_nama'],
            'npwp_customer' => $data['npwp_customer'] ?? ($invoice['client_npwp'] ?? ''),
            'nomor_invoice' => $invoiceId,
            'nilai_dpp' => $nilaiDpp,
            'nilai_ppn' => $nilaiPpn,
            'masa_pajak' => $data['masa_pajak'] ?? date('m', strtotime($invoice['tanggal_invoice'])),
            'tahun_pajak' => $data['tahun_pajak'] ?? date('Y', strtotime($invoice['tanggal_invoice'])),
            'status_setor' => 'Belum Disetor'
        ];
        
        return $this->insert($ppnData);
    }

    /**
     * Export data untuk Excel
     */
    public function getExportData($filters = [])
    {
        $builder = $this->select('ppn_keluaran.*, 
            faktur_pajak.nomor_faktur,
            faktur_pajak.tanggal_faktur,
            invoice.nomor_invoice as nomor_invoice_penjualan')
            ->join('faktur_pajak', 'faktur_pajak.id = ppn_keluaran.faktur_id', 'left')
            ->join('invoice', 'invoice.id = ppn_keluaran.nomor_invoice', 'left');
        
        if (!empty($filters['tahun'])) {
            $builder->where('ppn_keluaran.tahun_pajak', $filters['tahun']);
        }
        
        if (!empty($filters['status_setor'])) {
            $builder->where('ppn_keluaran.status_setor', $filters['status_setor']);
        }
        
        $ppnKeluaran = $builder->orderBy('ppn_keluaran.tanggal_penjualan', 'DESC')->findAll();
        
        $exportData = [];
        foreach ($ppnKeluaran as $item) {
            $exportData[] = [
                'Nomor Faktur' => $item['nomor_faktur'],
                'Tanggal Faktur' => $item['tanggal_faktur'],
                'Tanggal Penjualan' => $item['tanggal_penjualan'],
                'Customer' => $item['customer'],
                'NPWP Customer' => $item['npwp_customer'] ?? '-',
                'No Invoice' => $item['nomor_invoice_penjualan'] ?? '-',
                'Nilai DPP' => $item['nilai_dpp'],
                'Nilai PPN' => $item['nilai_ppn'],
                'Masa Pajak' => $item['masa_pajak'] . '/' . $item['tahun_pajak'],
                'Status Setor' => $item['status_setor'],
                'Tanggal Setor' => $item['tanggal_setor'] ?? '-'
            ];
        }
        
        return $exportData;
    }

    /**
     * Get total PPN keluaran untuk SPT Masa
     */
    public function getTotalForSpt($masaPajak, $tahunPajak)
    {
        $total = $this->select("
                SUM(nilai_dpp) as total_dpp,
                SUM(nilai_ppn) as total_ppn
            ")
            ->where('masa_pajak', $masaPajak)
            ->where('tahun_pajak', $tahunPajak)
            ->first();
        
        return [
            'total_dpp' => $total['total_dpp'] ?? 0,
            'total_ppn' => $total['total_ppn'] ?? 0
        ];
    }

    /**
     * Get summary for SPT Masa report
     */
    public function getSptMasaSummary($masaPajak, $tahunPajak)
    {
        $ppnKeluaran = $this->getTotalForSpt($masaPajak, $tahunPajak);
        
        $ppnMasukanModel = new PpnMasukanModel();
        $ppnMasukan = $ppnMasukanModel->getTotalForSpt($masaPajak, $tahunPajak);
        
        return [
            'masa_pajak' => $masaPajak,
            'tahun_pajak' => $tahunPajak,
            'ppn_keluaran' => $ppnKeluaran,
            'ppn_masukan' => $ppnMasukan,
            'ppn_kurang_bayar' => $ppnKeluaran['total_ppn'] - $ppnMasukan['total_ppn']
        ];
    }
}