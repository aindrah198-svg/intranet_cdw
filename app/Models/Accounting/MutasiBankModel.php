<?php
namespace App\Models\Accounting;

use CodeIgniter\Model;

class MutasiBankModel extends Model
{
    protected $table = 'mutasi_bank';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'tanggal',
        'kode_transaksi',
        'tipe',
        'jumlah',
        'keterangan',
        'coa_id_debit',
        'coa_id_kredit',
        'bank_asal',
        'bank_tujuan',
        'no_referensi',
        'lampiran',
        'status',
        'posted_at',
        'jurnal_id',
        'spk_id',
        'pengeluaran_id',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'tanggal' => 'required|valid_date',
        'tipe' => 'required|in_list[Debit,Kredit]',
        'jumlah' => 'required|numeric|greater_than[0]',
        'keterangan' => 'required',
        'status' => 'permit_empty|in_list[Draft,Posted,Dibatalkan]'
    ];

    protected $validationMessages = [
        'tanggal' => [
            'required' => 'Tanggal transaksi harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'tipe' => [
            'required' => 'Tipe transaksi (Debit/Kredit) harus dipilih'
        ],
        'jumlah' => [
            'required' => 'Jumlah harus diisi',
            'numeric' => 'Jumlah harus berupa angka',
            'greater_than' => 'Jumlah harus lebih besar dari 0'
        ],
        'keterangan' => [
            'required' => 'Keterangan harus diisi'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateKodeTransaksi', 'setCreatedBy', 'validateCoa'];
    protected $beforeUpdate = ['validateCoa', 'validateStatusChange'];

    protected function generateKodeTransaksi(array $data)
    {
        if (empty($data['data']['kode_transaksi'])) {
            $tanggal = $data['data']['tanggal'] ?? date('Y-m-d');
            $prefix = 'MB-' . date('Ymd', strtotime($tanggal)) . '-';
            
            $lastTransaksi = $this->select('kode_transaksi')
                ->like('kode_transaksi', $prefix, 'after')
                ->orderBy('kode_transaksi', 'DESC')
                ->first();
            
            if ($lastTransaksi) {
                $lastNum = substr($lastTransaksi['kode_transaksi'], strlen($prefix));
                $nextNum = str_pad((int)$lastNum + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNum = '0001';
            }
            
            $data['data']['kode_transaksi'] = $prefix . $nextNum;
        }
        
        return $data;
    }

    protected function validateCoa(array $data)
    {
        $tipe = $data['data']['tipe'] ?? null;
        
        if ($tipe === 'Debit') {
            if (empty($data['data']['coa_id_debit'])) {
                throw new \RuntimeException('Untuk transaksi Debit (uang keluar), akun debit harus diisi');
            }
        } 
        elseif ($tipe === 'Kredit') {
            if (empty($data['data']['coa_id_kredit'])) {
                throw new \RuntimeException('Untuk transaksi Kredit (uang masuk), akun kredit harus diisi');
            }
        }
        
        return $data;
    }

    protected function validateStatusChange(array $data)
    {
        if (isset($data['data']['status'])) {
            $id = $data['id'][0] ?? null;
            
            if ($id) {
                $current = $this->find($id);
                
                if ($data['data']['status'] === 'Posted' && $current['status'] === 'Draft') {
                    if (empty($current['jurnal_id']) && empty($data['data']['jurnal_id'])) {
                        throw new \RuntimeException('Transaksi harus memiliki jurnal_id sebelum diposting');
                    }
                }
                
                if ($data['data']['status'] === 'Dibatalkan' && $current['status'] === 'Posted') {
                    throw new \RuntimeException('Transaksi yang sudah diposting tidak dapat dibatalkan');
                }
            }
        }
        
        return $data;
    }

    protected function setCreatedBy(array $data)
    {
        $data['data']['created_by'] = session()->get('user_id');
        return $data;
    }

   /**
 * Get all mutasi bank with filters and pagination
 */
public function getAllWithFilters($filters = [], $perPage = 20, $page = 1)
{
    // Pertama, ambil SEMUA data yang sesuai filter (tanpa pagination)
    $allBuilder = $this->select('mutasi_bank.*, 
        creator.username as creator_name,
        coa_debit.kode_akun as kode_akun_debit,
        coa_debit.nama_akun as nama_akun_debit,
        coa_kredit.kode_akun as kode_akun_kredit,
        coa_kredit.nama_akun as nama_akun_kredit,
        jurnal.nomor_jurnal,
        spk.nomor_spk,
        spk.judul_pekerjaan')
        ->join('users as creator', 'creator.id = mutasi_bank.created_by', 'left')
        ->join('coa as coa_debit', 'coa_debit.id = mutasi_bank.coa_id_debit', 'left')
        ->join('coa as coa_kredit', 'coa_kredit.id = mutasi_bank.coa_id_kredit', 'left')
        ->join('jurnal', 'jurnal.id = mutasi_bank.jurnal_id', 'left')
        ->join('spk_instalasi as spk', 'spk.id = mutasi_bank.spk_id', 'left');
    
    // Apply filters
    if (!empty($filters['search'])) {
        $search = $filters['search'];
        $allBuilder->groupStart()
            ->like('mutasi_bank.kode_transaksi', $search)
            ->orLike('mutasi_bank.keterangan', $search)
            ->orLike('mutasi_bank.no_referensi', $search)
            ->orLike('coa_debit.nama_akun', $search)
            ->orLike('coa_kredit.nama_akun', $search)
            ->orLike('spk.nomor_spk', $search)
            ->groupEnd();
    }
    
    if (!empty($filters['tanggal_mulai'])) {
        $allBuilder->where('mutasi_bank.tanggal >=', $filters['tanggal_mulai']);
    }
    
    if (!empty($filters['tanggal_selesai'])) {
        $allBuilder->where('mutasi_bank.tanggal <=', $filters['tanggal_selesai']);
    }
    
    if (!empty($filters['tipe'])) {
        $dbTipe = ($filters['tipe'] == 'Masuk') ? 'Kredit' : 'Debit';
        $allBuilder->where('mutasi_bank.tipe', $dbTipe);
    }
    
    if (!empty($filters['status'])) {
        $allBuilder->where('mutasi_bank.status', $filters['status']);
    }
    
    if (!empty($filters['coa_bank_id'])) {
        $allBuilder->groupStart()
            ->where('mutasi_bank.coa_id_debit', $filters['coa_bank_id'])
            ->orWhere('mutasi_bank.coa_id_kredit', $filters['coa_bank_id'])
            ->groupEnd();
    }
    
    // Urutkan dari yang TERLAMA ke TERBARU
    $allBuilder->orderBy('mutasi_bank.tanggal', 'ASC')
               ->orderBy('mutasi_bank.id', 'ASC');
    
    // Ambil semua data
    $allData = $allBuilder->findAll();
    $total = count($allData);
    
    // Hitung saldo kumulatif untuk semua data
    $saldo = 0;
    foreach ($allData as &$item) {
        if ($item['tipe'] === 'Kredit') {
            $saldo += $item['jumlah'];
        } else {
            $saldo -= $item['jumlah'];
        }
        $item['saldo_berjalan'] = $saldo;
    }
    
    // Balik urutan untuk tampilan (terbaru ke terlama)
    $allData = array_reverse($allData);
    
    // Lakukan pagination manual
    $offset = ($page - 1) * $perPage;
    $paginatedData = array_slice($allData, $offset, $perPage);
    
    return [
        'data' => $paginatedData,
        'total' => $total,
        'per_page' => $perPage,
        'current_page' => $page,
        'total_pages' => ceil($total / $perPage)
    ];
}

    /**
     * Hitung saldo berjalan dengan mempertimbangkan offset
     */
    private function calculateSaldoWithOffset($mutasi, $filters, $offset)
    {
        if (empty($mutasi)) {
            return $mutasi;
        }
        
        // Ambil saldo dari semua transaksi SEBELUM halaman ini
        $saldoAwal = $this->getSaldoBeforeOffset($filters, $offset);
        
        // Hitung saldo kumulatif
        $saldo = $saldoAwal;
        
        foreach ($mutasi as &$item) {
            if ($item['tipe'] === 'Kredit') {
                $saldo += $item['jumlah'];
            } else {
                $saldo -= $item['jumlah'];
            }
            $item['saldo_berjalan'] = $saldo;
        }
        
        return $mutasi;
    }

   /**
 * Ambil saldo dari semua transaksi sebelum offset
 */
private function getSaldoBeforeOffset($filters, $offset)
{
    // Jika offset = 0 (halaman 1), saldo awal adalah 0
    if ($offset == 0) {
        return 0;
    }
    
    // Ambil semua transaksi yang sudah diposting
    $builder = $this->db->table('mutasi_bank')
        ->select("
            COALESCE(SUM(CASE WHEN tipe = 'Kredit' THEN jumlah ELSE 0 END), 0) as total_masuk,
            COALESCE(SUM(CASE WHEN tipe = 'Debit' THEN jumlah ELSE 0 END), 0) as total_keluar
        ")
        ->where('status', 'Posted');
    
    // Apply filters
    if (!empty($filters['tanggal_mulai'])) {
        $builder->where('tanggal >=', $filters['tanggal_mulai']);
    }
    
    if (!empty($filters['tanggal_selesai'])) {
        $builder->where('tanggal <=', $filters['tanggal_selesai']);
    }
    
    if (!empty($filters['tipe'])) {
        $dbTipe = ($filters['tipe'] == 'Masuk') ? 'Kredit' : 'Debit';
        $builder->where('tipe', $dbTipe);
    }
    
    if (!empty($filters['status'])) {
        $builder->where('status', $filters['status']);
    }
    
    if (!empty($filters['coa_bank_id'])) {
        $builder->groupStart()
            ->where('coa_id_debit', $filters['coa_bank_id'])
            ->orWhere('coa_id_kredit', $filters['coa_bank_id'])
            ->groupEnd();
    }
    
    // Ambil ID transaksi yang ditampilkan di halaman ini
    $offsetIds = $this->db->table('mutasi_bank')
        ->select('id')
        ->orderBy('tanggal', 'ASC')
        ->orderBy('id', 'ASC')
        ->limit($offset);
    
    if (!empty($filters['tanggal_mulai'])) {
        $offsetIds->where('tanggal >=', $filters['tanggal_mulai']);
    }
    if (!empty($filters['tanggal_selesai'])) {
        $offsetIds->where('tanggal <=', $filters['tanggal_selesai']);
    }
    if (!empty($filters['tipe'])) {
        $dbTipe = ($filters['tipe'] == 'Masuk') ? 'Kredit' : 'Debit';
        $offsetIds->where('tipe', $dbTipe);
    }
    if (!empty($filters['status'])) {
        $offsetIds->where('status', $filters['status']);
    }
    if (!empty($filters['coa_bank_id'])) {
        $offsetIds->groupStart()
            ->where('coa_id_debit', $filters['coa_bank_id'])
            ->orWhere('coa_id_kredit', $filters['coa_bank_id'])
            ->groupEnd();
    }
    
    $ids = $offsetIds->get()->getResultArray();
    $excludeIds = array_column($ids, 'id');
    
    if (!empty($excludeIds)) {
        $builder->whereNotIn('id', $excludeIds);
    }
    
    $result = $builder->get()->getRow();
    
    return ($result->total_masuk ?? 0) - ($result->total_keluar ?? 0);
}

    public function getWithDetails($id)
    {
        return $this->select('mutasi_bank.*, 
            creator.username as creator_name,
            coa_debit.kode_akun as kode_akun_debit,
            coa_debit.nama_akun as nama_akun_debit,
            coa_debit.tipe_akun as tipe_akun_debit,
            coa_kredit.kode_akun as kode_akun_kredit,
            coa_kredit.nama_akun as nama_akun_kredit,
            coa_kredit.tipe_akun as tipe_akun_kredit,
            jurnal.nomor_jurnal,
            jurnal.status as jurnal_status,
            spk.nomor_spk,
            spk.judul_pekerjaan')
            ->join('users as creator', 'creator.id = mutasi_bank.created_by', 'left')
            ->join('coa as coa_debit', 'coa_debit.id = mutasi_bank.coa_id_debit', 'left')
            ->join('coa as coa_kredit', 'coa_kredit.id = mutasi_bank.coa_id_kredit', 'left')
            ->join('jurnal', 'jurnal.id = mutasi_bank.jurnal_id', 'left')
            ->join('spk_instalasi as spk', 'spk.id = mutasi_bank.spk_id', 'left')
            ->where('mutasi_bank.id', $id)
            ->first();
    }

    public function postMutasi($id, $jurnalId)
    {
        $mutasi = $this->find($id);
        
        if (!$mutasi) {
            throw new \RuntimeException('Transaksi mutasi bank tidak ditemukan');
        }
        
        if ($mutasi['status'] !== 'Draft') {
            throw new \RuntimeException('Hanya transaksi dengan status Draft yang bisa diposting');
        }
        
        return $this->update($id, [
            'status' => 'Posted',
            'posted_at' => date('Y-m-d H:i:s'),
            'jurnal_id' => $jurnalId
        ]);
    }

    public function batalkanMutasi($id)
    {
        $mutasi = $this->find($id);
        
        if (!$mutasi) {
            throw new \RuntimeException('Transaksi mutasi bank tidak ditemukan');
        }
        
        if ($mutasi['status'] === 'Posted') {
            throw new \RuntimeException('Transaksi yang sudah diposting tidak dapat dibatalkan langsung. Batalkan jurnal terlebih dahulu.');
        }
        
        return $this->update($id, ['status' => 'Dibatalkan']);
    }

    public function getCoaOptions($tipe = null, $isBank = true)
    {
        $coaModel = new \App\Models\CoaModel();
        
        $builder = $coaModel->where('is_header', 0)->where('is_active', 1);
        
        if ($isBank) {
            $builder->like('kode_akun', '1-11', 'after');
        }
        
        if ($tipe === 'Debit') {
            $builder->whereIn('tipe_akun', ['Beban', 'Aset']);
        } elseif ($tipe === 'Kredit') {
            $builder->whereIn('tipe_akun', ['Pendapatan', 'Kewajiban', 'Ekuitas']);
        }
        
        return $builder->orderBy('kode_akun', 'ASC')->findAll();
    }

    public function getAllCoaOptions($isBank = true)
    {
        $coaModel = new \App\Models\CoaModel();
        
        $builder = $coaModel->where('is_header', 0)->where('is_active', 1);
        
        if ($isBank) {
            $builder->like('kode_akun', '1-11', 'after');
        }
        
        return $builder->orderBy('kode_akun', 'ASC')->findAll();
    }

    public function getSaldoBank($coaBankId = null, $tanggal = null)
    {
        if (!$coaBankId) {
            return 0;
        }
        
        $builder = $this->db->table('mutasi_bank')
            ->select("
                COALESCE(SUM(CASE WHEN coa_id_kredit = {$coaBankId} THEN jumlah ELSE 0 END), 0) as total_masuk,
                COALESCE(SUM(CASE WHEN coa_id_debit = {$coaBankId} THEN jumlah ELSE 0 END), 0) as total_keluar
            ")
            ->where('status', 'Posted');
        
        if ($tanggal) {
            $builder->where('tanggal <=', $tanggal);
        }
        
        $result = $builder->get()->getRow();
        
        return ($result->total_masuk ?? 0) - ($result->total_keluar ?? 0);
    }

/**
 * Get ringkasan per bank - HITUNG LANGSUNG DARI MUTASI
 */
public function getRingkasanPerBank($tanggalMulai = null, $tanggalSelesai = null)
{
    // Ambil semua data mutasi yang sudah diposting
    $builder = $this->db->table('mutasi_bank')
        ->select('tipe, jumlah, coa_id_debit, coa_id_kredit, bank_asal, bank_tujuan')
        ->where('status', 'Posted');
    
    if ($tanggalMulai) {
        $builder->where('tanggal >=', $tanggalMulai);
    }
    
    if ($tanggalSelesai) {
        $builder->where('tanggal <=', $tanggalSelesai);
    }
    
    $mutasi = $builder->get()->getResultArray();
    
    // Inisialisasi
    $totalMasuk = 0;
    $totalKeluar = 0;
    
    // Hitung dari data mutasi
    foreach ($mutasi as $row) {
        if ($row['tipe'] == 'Kredit') {
            // Transaksi MASUK (Kredit)
            $totalMasuk += $row['jumlah'];
        } else {
            // Transaksi KELUAR (Debit)
            $totalKeluar += $row['jumlah'];
        }
    }
    
    $saldo = $totalMasuk - $totalKeluar;
    
    return [[
        'kode_bank' => '1-1103',
        'nama_bank' => 'Kas di Bank - Mandiri',
        'total_masuk' => $totalMasuk,
        'total_keluar' => $totalKeluar
    ]];
}

    public function getStats($tanggalMulai = null, $tanggalSelesai = null)
    {
        $builder = $this->db->table('mutasi_bank')
            ->where('status', 'Posted');
        
        if ($tanggalMulai) {
            $builder->where('tanggal >=', $tanggalMulai);
        }
        
        if ($tanggalSelesai) {
            $builder->where('tanggal <=', $tanggalSelesai);
        }
        
        $result = $builder->select("
                COUNT(*) as total_transaksi,
                COALESCE(SUM(CASE WHEN tipe = 'Kredit' THEN jumlah ELSE 0 END), 0) as total_masuk,
                COALESCE(SUM(CASE WHEN tipe = 'Debit' THEN jumlah ELSE 0 END), 0) as total_keluar,
                COUNT(CASE WHEN tipe = 'Kredit' THEN 1 END) as jumlah_masuk,
                COUNT(CASE WHEN tipe = 'Debit' THEN 1 END) as jumlah_keluar,
                COUNT(CASE WHEN DATE(tanggal) = CURDATE() THEN 1 END) as transaksi_hari_ini
            ")
            ->get()
            ->getRow();
        
        return [
            'total_transaksi' => (int)($result->total_transaksi ?? 0),
            'total_masuk' => (float)($result->total_masuk ?? 0),
            'total_keluar' => (float)($result->total_keluar ?? 0),
            'jumlah_masuk' => (int)($result->jumlah_masuk ?? 0),
            'jumlah_keluar' => (int)($result->jumlah_keluar ?? 0),
            'transaksi_hari_ini' => (int)($result->transaksi_hari_ini ?? 0)
        ];
    }

    public function verifikasiSaldoBank($coaBankId = null, $tanggalAkhir = null)
    {
        if (!$coaBankId) {
            return ['error' => 'ID Bank tidak ditemukan'];
        }
        
        $builder = $this->db->table('mutasi_bank')
            ->select('id, tanggal, kode_transaksi, tipe, jumlah, coa_id_debit, coa_id_kredit')
            ->where('status', 'Posted')
            ->groupStart()
                ->where('coa_id_debit', $coaBankId)
                ->orWhere('coa_id_kredit', $coaBankId)
            ->groupEnd()
            ->orderBy('tanggal', 'ASC')
            ->orderBy('id', 'ASC');
        
        if ($tanggalAkhir) {
            $builder->where('tanggal <=', $tanggalAkhir);
        }
        
        $transaksi = $builder->get()->getResultArray();
        
        $saldo = 0;
        $detail = [];
        
        foreach ($transaksi as $trx) {
            if ($trx['coa_id_kredit'] == $coaBankId) {
                $saldo += $trx['jumlah'];
                $jenis = 'MASUK';
            } else {
                $saldo -= $trx['jumlah'];
                $jenis = 'KELUAR';
            }
            
            $detail[] = [
                'tanggal' => $trx['tanggal'],
                'kode' => $trx['kode_transaksi'],
                'jenis' => $jenis,
                'jumlah' => $trx['jumlah'],
                'saldo_kumulatif' => $saldo
            ];
        }
        
        return [
            'coa_bank_id' => $coaBankId,
            'saldo_akhir' => $saldo,
            'total_transaksi' => count($transaksi),
            'detail' => $detail
        ];
    }

    public function getExportData($filters = [])
    {
        $builder = $this->select('mutasi_bank.*, 
            creator.username as creator_name,
            coa_debit.kode_akun as kode_akun_debit,
            coa_debit.nama_akun as nama_akun_debit,
            coa_kredit.kode_akun as kode_akun_kredit,
            coa_kredit.nama_akun as nama_akun_kredit,
            spk.nomor_spk,
            spk.judul_pekerjaan')
            ->join('users as creator', 'creator.id = mutasi_bank.created_by', 'left')
            ->join('coa as coa_debit', 'coa_debit.id = mutasi_bank.coa_id_debit', 'left')
            ->join('coa as coa_kredit', 'coa_kredit.id = mutasi_bank.coa_id_kredit', 'left')
            ->join('spk_instalasi as spk', 'spk.id = mutasi_bank.spk_id', 'left');
        
        if (!empty($filters['tanggal_mulai'])) {
            $builder->where('mutasi_bank.tanggal >=', $filters['tanggal_mulai']);
        }
        
        if (!empty($filters['tanggal_selesai'])) {
            $builder->where('mutasi_bank.tanggal <=', $filters['tanggal_selesai']);
        }
        
        if (!empty($filters['tipe'])) {
            $builder->where('mutasi_bank.tipe', $filters['tipe']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('mutasi_bank.status', $filters['status']);
        }
        
        return $builder->orderBy('mutasi_bank.tanggal', 'DESC')
                      ->orderBy('mutasi_bank.kode_transaksi', 'DESC')
                      ->findAll();
    }

    /**
 * Get export data untuk PDF (urutan ASC - dari terlama ke terbaru)
 */
public function getExportDataForPdf($filters = [])
{
    $builder = $this->select('mutasi_bank.*, 
        creator.username as creator_name,
        coa_debit.kode_akun as kode_akun_debit,
        coa_debit.nama_akun as nama_akun_debit,
        coa_kredit.kode_akun as kode_akun_kredit,
        coa_kredit.nama_akun as nama_akun_kredit,
        spk.nomor_spk,
        spk.judul_pekerjaan')
        ->join('users as creator', 'creator.id = mutasi_bank.created_by', 'left')
        ->join('coa as coa_debit', 'coa_debit.id = mutasi_bank.coa_id_debit', 'left')
        ->join('coa as coa_kredit', 'coa_kredit.id = mutasi_bank.coa_id_kredit', 'left')
        ->join('spk_instalasi as spk', 'spk.id = mutasi_bank.spk_id', 'left');
    
    if (!empty($filters['tanggal_mulai'])) {
        $builder->where('mutasi_bank.tanggal >=', $filters['tanggal_mulai']);
    }
    
    if (!empty($filters['tanggal_selesai'])) {
        $builder->where('mutasi_bank.tanggal <=', $filters['tanggal_selesai']);
    }
    
    if (!empty($filters['tipe'])) {
        $builder->where('mutasi_bank.tipe', $filters['tipe']);
    }
    
    if (!empty($filters['status'])) {
        $builder->where('mutasi_bank.status', $filters['status']);
    }
    
    if (!empty($filters['coa_bank_id'])) {
        $builder->groupStart()
            ->where('mutasi_bank.coa_id_debit', $filters['coa_bank_id'])
            ->orWhere('mutasi_bank.coa_id_kredit', $filters['coa_bank_id'])
            ->groupEnd();
    }
    
    if (!empty($filters['spk_id'])) {
        $builder->where('mutasi_bank.spk_id', $filters['spk_id']);
    }
    
    // Untuk PDF: urutan dari terlama ke terbaru (ASC)
    return $builder->orderBy('mutasi_bank.tanggal', 'ASC')
                  ->orderBy('mutasi_bank.id', 'ASC')
                  ->findAll();
}


    public function recalculateSaldo()
    {
        return true;
    }

    public function isPosted($id)
    {
        $mutasi = $this->find($id);
        return $mutasi && $mutasi['status'] === 'Posted';
    }
}