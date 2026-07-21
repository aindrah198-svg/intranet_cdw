<?php
namespace App\Models\Accounting;

use CodeIgniter\Model;

class PpnMasukanModel extends Model
{
    protected $table = 'ppn_masukan';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'faktur_id',
        'tanggal_pembelian',
        'supplier',
        'npwp_supplier',
        'nomor_invoice_supplier',
        'nilai_dpp',
        'nilai_ppn',
        'masa_pajak',
        'tahun_pajak',
        'status_kredit',
        'bulan_dikreditkan',
        'tahun_dikreditkan'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'faktur_id' => 'required|is_natural_no_zero',
        'tanggal_pembelian' => 'required|valid_date',
        'supplier' => 'required',
        'npwp_supplier' => 'required|min_length[15]|max_length[20]',
        'nilai_dpp' => 'required|numeric|greater_than[0]',
        'nilai_ppn' => 'required|numeric|greater_than[0]',
        'masa_pajak' => 'required|numeric|greater_than[0]|less_than_equal_to[12]',
        'tahun_pajak' => 'required|numeric|min_length[4]|max_length[4]',
        'status_kredit' => 'permit_empty|in_list[Belum Dikreditkan,Dikreditkan,Tidak Dikreditkan]',
        'bulan_dikreditkan' => 'permit_empty|numeric|greater_than[0]|less_than_equal_to[12]',
        'tahun_dikreditkan' => 'permit_empty|numeric|min_length[4]|max_length[4]'
    ];

    protected $validationMessages = [
        'faktur_id' => [
            'required' => 'ID faktur harus diisi'
        ],
        'tanggal_pembelian' => [
            'required' => 'Tanggal pembelian harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'supplier' => [
            'required' => 'Nama supplier harus diisi'
        ],
        'npwp_supplier' => [
            'required' => 'NPWP supplier harus diisi',
            'min_length' => 'NPWP harus 15-20 digit',
            'max_length' => 'NPWP harus 15-20 digit'
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
    protected $beforeInsert = ['setDefaultValues', 'validateKreditPeriod', 'validateFaktur', 'validateNilaiPpn'];
    protected $beforeUpdate = ['validateKreditPeriod', 'validateStatusChange'];

    /**
     * Set default values
     */
    protected function setDefaultValues(array $data)
    {
        if (!isset($data['data']['status_kredit'])) {
            $data['data']['status_kredit'] = 'Belum Dikreditkan';
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
            
            if ($faktur['jenis_faktur'] !== 'Masukan') {
                throw new \RuntimeException('Faktur yang dipilih bukan faktur masukan');
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
     * Validasi periode kredit
     */
    protected function validateKreditPeriod(array $data)
    {
        $statusKredit = $data['data']['status_kredit'] ?? null;
        $bulanDikreditkan = $data['data']['bulan_dikreditkan'] ?? null;
        $tahunDikreditkan = $data['data']['tahun_dikreditkan'] ?? null;
        $masaPajak = $data['data']['masa_pajak'] ?? null;
        $tahunPajak = $data['data']['tahun_pajak'] ?? null;
        
        if ($statusKredit === 'Dikreditkan') {
            if (empty($bulanDikreditkan) || empty($tahunDikreditkan)) {
                throw new \RuntimeException('Untuk PPN yang dikreditkan, bulan dan tahun dikreditkan harus diisi');
            }
            
            // Validasi tidak boleh dikreditkan sebelum masa pajak
            if ($tahunDikreditkan < $tahunPajak || 
                ($tahunDikreditkan == $tahunPajak && $bulanDikreditkan < $masaPajak)) {
                throw new \RuntimeException('PPN tidak dapat dikreditkan pada masa pajak sebelum masa pajak faktur');
            }
            
            // Validasi batas waktu kredit (3 bulan setelah masa pajak)
            $maxBulan = $masaPajak + 3;
            $maxTahun = $tahunPajak;
            if ($maxBulan > 12) {
                $maxBulan -= 12;
                $maxTahun++;
            }
            
            if ($tahunDikreditkan > $maxTahun || 
                ($tahunDikreditkan == $maxTahun && $bulanDikreditkan > $maxBulan)) {
                throw new \RuntimeException('PPN harus dikreditkan maksimal 3 bulan setelah masa pajak faktur');
            }
        }
        
        return $data;
    }

    /**
     * Validasi perubahan status
     */
    protected function validateStatusChange(array $data)
    {
        if (isset($data['data']['status_kredit'])) {
            $id = $data['id'][0] ?? null;
            
            if ($id) {
                $current = $this->find($id);
                
                if ($current) {
                    $oldStatus = $current['status_kredit'];
                    $newStatus = $data['data']['status_kredit'];
                    
                    // Jika sudah dikreditkan, tidak bisa diubah
                    if ($oldStatus === 'Dikreditkan' && $newStatus !== 'Dikreditkan') {
                        throw new \RuntimeException('PPN yang sudah dikreditkan tidak dapat diubah statusnya');
                    }
                    
                    // Jika sudah tidak dikreditkan, tidak bisa diubah menjadi dikreditkan
                    if ($oldStatus === 'Tidak Dikreditkan' && $newStatus === 'Dikreditkan') {
                        throw new \RuntimeException('PPN yang sudah ditetapkan tidak dikreditkan tidak dapat dikreditkan kembali');
                    }
                }
            }
        }
        
        return $data;
    }

    /**
     * Get all PPN masukan with filters
     */
    public function getAllWithFilters($filters = [], $perPage = 20, $page = 1)
    {
        $builder = $this->select('ppn_masukan.*, 
            faktur_pajak.nomor_faktur,
            faktur_pajak.tanggal_faktur,
            faktur_pajak.npwp_pengusaha,
            faktur_pajak.nama_pengusaha,
            faktur_pajak.alamat_pengusaha,
            faktur_pajak.file_faktur,
            faktur_pajak.status_approval,
            creator.username as creator_name')
            ->join('faktur_pajak', 'faktur_pajak.id = ppn_masukan.faktur_id', 'left')
            ->join('users as creator', 'creator.id = ppn_masukan.created_by', 'left');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('ppn_masukan.supplier', $search)
                ->orLike('ppn_masukan.npwp_supplier', $search)
                ->orLike('ppn_masukan.nomor_invoice_supplier', $search)
                ->orLike('faktur_pajak.nomor_faktur', $search)
                ->orLike('faktur_pajak.nama_pengusaha', $search)
                ->groupEnd();
        }
        
        if (!empty($filters['status_kredit'])) {
            $builder->where('ppn_masukan.status_kredit', $filters['status_kredit']);
        }
        
        if (!empty($filters['tahun'])) {
            $builder->where('ppn_masukan.tahun_pajak', $filters['tahun']);
        }
        
        if (!empty($filters['bulan'])) {
            $builder->where('ppn_masukan.masa_pajak', $filters['bulan']);
        }
        
        if (!empty($filters['tahun_kredit'])) {
            $builder->where('ppn_masukan.tahun_dikreditkan', $filters['tahun_kredit']);
        }
        
        if (!empty($filters['bulan_kredit'])) {
            $builder->where('ppn_masukan.bulan_dikreditkan', $filters['bulan_kredit']);
        }
        
        if (!empty($filters['tanggal_mulai'])) {
            $builder->where('ppn_masukan.tanggal_pembelian >=', $filters['tanggal_mulai']);
        }
        
        if (!empty($filters['tanggal_selesai'])) {
            $builder->where('ppn_masukan.tanggal_pembelian <=', $filters['tanggal_selesai']);
        }
        
        $builder->orderBy('ppn_masukan.tanggal_pembelian', 'DESC')
                ->orderBy('ppn_masukan.id', 'DESC');
        
        // Get total for pagination
        $total = $builder->countAllResults(false);
        
        // Get paginated results
        $offset = ($page - 1) * $perPage;
        $ppnMasukan = $builder->limit($perPage, $offset)->findAll();
        
        return [
            'data' => $ppnMasukan,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Get PPN masukan by ID with details
     */
    public function getWithDetails($id)
    {
        $ppnMasukan = $this->select('ppn_masukan.*, 
            faktur_pajak.nomor_faktur,
            faktur_pajak.tanggal_faktur,
            faktur_pajak.npwp_pengusaha,
            faktur_pajak.nama_pengusaha,
            faktur_pajak.alamat_pengusaha,
            faktur_pajak.file_faktur,
            faktur_pajak.status_approval,
            faktur_pajak.keterangan as faktur_keterangan,
            creator.username as creator_name,
            creator.name as creator_fullname')
            ->join('faktur_pajak', 'faktur_pajak.id = ppn_masukan.faktur_id', 'left')
            ->join('users as creator', 'creator.id = ppn_masukan.created_by', 'left')
            ->where('ppn_masukan.id', $id)
            ->first();
        
        return $ppnMasukan;
    }

    /**
     * Get PPN masukan by faktur ID
     */
    public function getByFaktur($fakturId)
    {
        return $this->where('faktur_id', $fakturId)->first();
    }

    /**
     * Get PPN masukan by supplier
     */
    public function getBySupplier($supplier, $tahun = null)
    {
        $builder = $this->where('supplier', $supplier);
        
        if ($tahun) {
            $builder->where('tahun_pajak', $tahun);
        }
        
        return $builder->orderBy('tanggal_pembelian', 'DESC')->findAll();
    }

    /**
     * Get PPN masukan by masa pajak
     */
    public function getByMasaPajak($bulan, $tahun)
    {
        return $this->where('masa_pajak', $bulan)
                    ->where('tahun_pajak', $tahun)
                    ->orderBy('tanggal_pembelian', 'ASC')
                    ->findAll();
    }

    /**
     * Get PPN masukan yang belum dikreditkan
     */
    public function getBelumDikreditkan()
    {
        return $this->where('status_kredit', 'Belum Dikreditkan')
                    ->orderBy('tanggal_pembelian', 'ASC')
                    ->findAll();
    }

    /**
     * Get PPN masukan yang sudah dikreditkan per periode
     */
    public function getDikreditkanByPeriode($bulan, $tahun)
    {
        return $this->where('status_kredit', 'Dikreditkan')
                    ->where('bulan_dikreditkan', $bulan)
                    ->where('tahun_dikreditkan', $tahun)
                    ->orderBy('tanggal_pembelian', 'ASC')
                    ->findAll();
    }

    /**
     * Credit PPN masukan (mark as credited)
     */
    public function credit($id, $bulanKredit, $tahunKredit)
    {
        $ppnMasukan = $this->find($id);
        
        if (!$ppnMasukan) {
            throw new \RuntimeException('Data PPN masukan tidak ditemukan');
        }
        
        if ($ppnMasukan['status_kredit'] !== 'Belum Dikreditkan') {
            throw new \RuntimeException('Hanya PPN masukan dengan status Belum Dikreditkan yang dapat dikreditkan');
        }
        
        // Validasi periode kredit
        $masaPajak = $ppnMasukan['masa_pajak'];
        $tahunPajak = $ppnMasukan['tahun_pajak'];
        
        // Maksimal 3 bulan setelah masa pajak
        $maxBulan = $masaPajak + 3;
        $maxTahun = $tahunPajak;
        if ($maxBulan > 12) {
            $maxBulan -= 12;
            $maxTahun++;
        }
        
        if ($tahunKredit > $maxTahun || ($tahunKredit == $maxTahun && $bulanKredit > $maxBulan)) {
            throw new \RuntimeException('PPN harus dikreditkan maksimal 3 bulan setelah masa pajak faktur');
        }
        
        return $this->update($id, [
            'status_kredit' => 'Dikreditkan',
            'bulan_dikreditkan' => $bulanKredit,
            'tahun_dikreditkan' => $tahunKredit
        ]);
    }

    /**
     * Mark as not creditable
     */
    public function markAsNotCreditable($id, $alasan = null)
    {
        $ppnMasukan = $this->find($id);
        
        if (!$ppnMasukan) {
            throw new \RuntimeException('Data PPN masukan tidak ditemukan');
        }
        
        if ($ppnMasukan['status_kredit'] !== 'Belum Dikreditkan') {
            throw new \RuntimeException('Hanya PPN masukan dengan status Belum Dikreditkan yang dapat ditetapkan tidak dikreditkan');
        }
        
        $updateData = ['status_kredit' => 'Tidak Dikreditkan'];
        
        if ($alasan) {
            // Tambahkan catatan ke faktur
            $fakturModel = new FakturPajakModel();
            $faktur = $fakturModel->find($ppnMasukan['faktur_id']);
            if ($faktur) {
                $keterangan = ($faktur['keterangan'] ?? '') . "\n[TIDAK DIKREDITKAN] " . $alasan;
                $fakturModel->update($faktur['id'], ['keterangan' => $keterangan]);
            }
        }
        
        return $this->update($id, $updateData);
    }

    /**
     * Get total PPN masukan per masa pajak
     */
    public function getTotalPerMasa($tahun = null)
    {
        $builder = $this->select("
                masa_pajak,
                tahun_pajak,
                COUNT(*) as jumlah_faktur,
                SUM(nilai_dpp) as total_dpp,
                SUM(nilai_ppn) as total_ppn,
                SUM(CASE WHEN status_kredit = 'Dikreditkan' THEN nilai_ppn ELSE 0 END) as total_ppn_dikreditkan,
                SUM(CASE WHEN status_kredit = 'Belum Dikreditkan' THEN nilai_ppn ELSE 0 END) as total_ppn_belum_dikreditkan,
                SUM(CASE WHEN status_kredit = 'Tidak Dikreditkan' THEN nilai_ppn ELSE 0 END) as total_ppn_tidak_dikreditkan
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
     * Get total PPN masukan per supplier
     */
    public function getTotalPerSupplier($tahun = null)
    {
        $builder = $this->select("
                supplier,
                npwp_supplier,
                COUNT(*) as jumlah_faktur,
                SUM(nilai_dpp) as total_dpp,
                SUM(nilai_ppn) as total_ppn,
                SUM(CASE WHEN status_kredit = 'Dikreditkan' THEN nilai_ppn ELSE 0 END) as total_ppn_dikreditkan
            ")
            ->groupBy('supplier, npwp_supplier')
            ->orderBy('total_ppn', 'DESC');
        
        if ($tahun) {
            $builder->where('tahun_pajak', $tahun);
        }
        
        return $builder->findAll();
    }

    /**
     * Get ringkasan per status kredit
     */
    public function getRingkasanPerStatus($tahun = null)
    {
        $builder = $this->select("
                status_kredit,
                COUNT(*) as jumlah_faktur,
                SUM(nilai_dpp) as total_dpp,
                SUM(nilai_ppn) as total_ppn
            ")
            ->groupBy('status_kredit');
        
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
                SUM(CASE WHEN status_kredit = 'Dikreditkan' THEN nilai_ppn ELSE 0 END) as total_dikreditkan,
                SUM(CASE WHEN status_kredit = 'Belum Dikreditkan' THEN nilai_ppn ELSE 0 END) as total_belum_dikreditkan,
                SUM(CASE WHEN status_kredit = 'Tidak Dikreditkan' THEN nilai_ppn ELSE 0 END) as total_tidak_dikreditkan,
                COUNT(CASE WHEN status_kredit = 'Dikreditkan' THEN 1 END) as jumlah_dikreditkan,
                COUNT(CASE WHEN status_kredit = 'Belum Dikreditkan' THEN 1 END) as jumlah_belum_dikreditkan
            ");
        
        if ($tahun) {
            $builder->where('tahun_pajak', $tahun);
        }
        
        $stats = $builder->first();
        
        return $stats ?? [
            'total_faktur' => 0,
            'total_dpp' => 0,
            'total_ppn' => 0,
            'total_dikreditkan' => 0,
            'total_belum_dikreditkan' => 0,
            'total_tidak_dikreditkan' => 0,
            'jumlah_dikreditkan' => 0,
            'jumlah_belum_dikreditkan' => 0
        ];
    }

    /**
     * Get PPN masukan yang akan kadaluarsa (batas waktu kredit)
     */
    public function getExpiringSoon($bulanKeDepan = 3)
    {
        $today = date('Y-m-d');
        $batasBulan = date('m', strtotime("+$bulanKeDepan months"));
        $batasTahun = date('Y', strtotime("+$bulanKeDepan months"));
        
        $results = [];
        $ppnList = $this->where('status_kredit', 'Belum Dikreditkan')->findAll();
        
        foreach ($ppnList as $item) {
            $masaPajak = $item['masa_pajak'];
            $tahunPajak = $item['tahun_pajak'];
            
            // Hitung batas waktu kredit (3 bulan setelah masa pajak)
            $batasBulanKredit = $masaPajak + 3;
            $batasTahunKredit = $tahunPajak;
            if ($batasBulanKredit > 12) {
                $batasBulanKredit -= 12;
                $batasTahunKredit++;
            }
            
            // Cek apakah mendekati kadaluarsa
            if ($batasTahunKredit < $batasTahun || 
                ($batasTahunKredit == $batasTahun && $batasBulanKredit <= $batasBulan)) {
                $item['batas_kredit_bulan'] = $batasBulanKredit;
                $item['batas_kredit_tahun'] = $batasTahunKredit;
                $results[] = $item;
            }
        }
        
        return $results;
    }

    /**
     * Create PPN masukan from faktur
     */
    public function createFromFaktur($fakturId, $data = [])
    {
        $fakturModel = new FakturPajakModel();
        $faktur = $fakturModel->find($fakturId);
        
        if (!$faktur) {
            throw new \RuntimeException('Faktur pajak tidak ditemukan');
        }
        
        if ($faktur['jenis_faktur'] !== 'Masukan') {
            throw new \RuntimeException('Faktur yang dipilih bukan faktur masukan');
        }
        
        $ppnData = [
            'faktur_id' => $fakturId,
            'tanggal_pembelian' => $data['tanggal_pembelian'] ?? $faktur['tanggal_faktur'],
            'supplier' => $data['supplier'] ?? $faktur['nama_pengusaha'],
            'npwp_supplier' => $data['npwp_supplier'] ?? $faktur['npwp_pengusaha'],
            'nomor_invoice_supplier' => $data['nomor_invoice_supplier'] ?? null,
            'nilai_dpp' => $data['nilai_dpp'] ?? $faktur['nilai_transaksi'],
            'nilai_ppn' => $data['nilai_ppn'] ?? $faktur['nilai_ppn'],
            'masa_pajak' => $data['masa_pajak'] ?? $faktur['masa_pajak'],
            'tahun_pajak' => $data['tahun_pajak'] ?? $faktur['tahun_pajak'],
            'status_kredit' => $data['status_kredit'] ?? 'Belum Dikreditkan'
        ];
        
        return $this->insert($ppnData);
    }

    /**
     * Export data untuk Excel
     */
    public function getExportData($filters = [])
    {
        $builder = $this->select('ppn_masukan.*, 
            faktur_pajak.nomor_faktur,
            faktur_pajak.tanggal_faktur')
            ->join('faktur_pajak', 'faktur_pajak.id = ppn_masukan.faktur_id', 'left');
        
        if (!empty($filters['tahun'])) {
            $builder->where('ppn_masukan.tahun_pajak', $filters['tahun']);
        }
        
        if (!empty($filters['status_kredit'])) {
            $builder->where('ppn_masukan.status_kredit', $filters['status_kredit']);
        }
        
        $ppnMasukan = $builder->orderBy('ppn_masukan.tanggal_pembelian', 'DESC')->findAll();
        
        $exportData = [];
        foreach ($ppnMasukan as $item) {
            $exportData[] = [
                'Nomor Faktur' => $item['nomor_faktur'],
                'Tanggal Faktur' => $item['tanggal_faktur'],
                'Tanggal Pembelian' => $item['tanggal_pembelian'],
                'Supplier' => $item['supplier'],
                'NPWP Supplier' => $item['npwp_supplier'],
                'No Invoice Supplier' => $item['nomor_invoice_supplier'] ?? '-',
                'Nilai DPP' => $item['nilai_dpp'],
                'Nilai PPN' => $item['nilai_ppn'],
                'Masa Pajak' => $item['masa_pajak'] . '/' . $item['tahun_pajak'],
                'Status Kredit' => $item['status_kredit'],
                'Dikreditkan Pada' => $item['status_kredit'] === 'Dikreditkan' ? 
                    $item['bulan_dikreditkan'] . '/' . $item['tahun_dikreditkan'] : '-'
            ];
        }
        
        return $exportData;
    }

    /**
     * Get total PPN masukan untuk SPT Masa
     */
    public function getTotalForSpt($masaPajak, $tahunPajak)
    {
        $total = $this->select("
                SUM(nilai_dpp) as total_dpp,
                SUM(nilai_ppn) as total_ppn
            ")
            ->where('masa_pajak', $masaPajak)
            ->where('tahun_pajak', $tahunPajak)
            ->where('status_kredit', 'Dikreditkan')
            ->first();
        
        return [
            'total_dpp' => $total['total_dpp'] ?? 0,
            'total_ppn' => $total['total_ppn'] ?? 0
        ];
    }
}