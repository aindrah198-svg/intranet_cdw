<?php
namespace App\Models\Accounting;

use CodeIgniter\Model;
use App\Models\BukuBesarModel;

class TransferInternalModel extends Model
{
    protected $table = 'transfer_internal';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'tanggal',
        'kode_transfer',
        'jumlah',
        'keterangan',
        'coa_id_sumber',
        'coa_id_tujuan',
        'bank_asal',
        'bank_tujuan',
        'no_referensi',
        'lampiran',
        'status',
        'posted_at',
        'jurnal_id',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

protected $validationRules = [
    'tanggal' => 'required|valid_date',
    'jumlah' => 'required|numeric|greater_than[0]',
    'keterangan' => 'required',
    'coa_id_sumber' => 'required|is_natural_no_zero',
    'coa_id_tujuan' => 'required|is_natural_no_zero', // Hapus 'different' dari sini
    'status' => 'permit_empty|in_list[Draft,Posted,Dibatalkan]'
];

    protected $validationMessages = [
        'tanggal' => [
            'required' => 'Tanggal transfer harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'jumlah' => [
            'required' => 'Jumlah transfer harus diisi',
            'numeric' => 'Jumlah harus berupa angka',
            'greater_than' => 'Jumlah harus lebih besar dari 0'
        ],
        'keterangan' => [
            'required' => 'Keterangan harus diisi'
        ],
        'coa_id_sumber' => [
            'required' => 'Akun sumber harus dipilih'
        ],
        'coa_id_tujuan' => [
            'required' => 'Akun tujuan harus dipilih',
            'different' => 'Akun sumber dan tujuan harus berbeda'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateKodeTransfer', 'setCreatedBy', 'validateCoaKasBank'];
    protected $beforeUpdate = ['validateCoaKasBank', 'validateStatusChange'];

    /**
     * Generate kode transfer otomatis
     * Format: TI-YYYYMMDD-XXXX
     */
    protected function generateKodeTransfer(array $data)
    {
        if (empty($data['data']['kode_transfer'])) {
            $tanggal = $data['data']['tanggal'] ?? date('Y-m-d');
            $prefix = 'TI-' . date('Ymd', strtotime($tanggal)) . '-';
            
            $lastTransfer = $this->select('kode_transfer')
                ->like('kode_transfer', $prefix, 'after')
                ->orderBy('kode_transfer', 'DESC')
                ->first();
            
            if ($lastTransfer) {
                $lastNum = substr($lastTransfer['kode_transfer'], strlen($prefix));
                $nextNum = str_pad((int)$lastNum + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNum = '0001';
            }
            
            $data['data']['kode_transfer'] = $prefix . $nextNum;
        }
        
        return $data;
    }

    /**
     * Validasi COA sumber dan tujuan harus akun Kas/Bank (kode 1-11xx)
     */
    protected function validateCoaKasBank(array $data)
    {
        $coaModel = new \App\Models\CoaModel();
        
        if (!empty($data['data']['coa_id_sumber'])) {
            $coaSumber = $coaModel->find($data['data']['coa_id_sumber']);
            if (!$coaSumber || !str_starts_with($coaSumber['kode_akun'] ?? '', '1-11')) {
                throw new \RuntimeException('Akun sumber harus merupakan akun Kas/Bank (kode 1-11xx)');
            }
        }
        
        if (!empty($data['data']['coa_id_tujuan'])) {
            $coaTujuan = $coaModel->find($data['data']['coa_id_tujuan']);
            if (!$coaTujuan || !str_starts_with($coaTujuan['kode_akun'] ?? '', '1-11')) {
                throw new \RuntimeException('Akun tujuan harus merupakan akun Kas/Bank (kode 1-11xx)');
            }
        }
        
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
                
                if ($data['data']['status'] === 'Posted' && $current['status'] === 'Draft') {
                    if (empty($current['jurnal_id']) && empty($data['data']['jurnal_id'])) {
                        throw new \RuntimeException('Transfer harus memiliki jurnal_id sebelum diposting');
                    }
                }
                
                if ($data['data']['status'] === 'Dibatalkan' && $current['status'] === 'Posted') {
                    throw new \RuntimeException('Transfer yang sudah diposting tidak dapat dibatalkan');
                }
                
                if ($current['status'] === 'Posted' && $data['data']['status'] !== 'Posted') {
                    throw new \RuntimeException('Data yang sudah diposting tidak dapat diubah');
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
     * Get all transfer internal with filters and pagination
     */
    public function getAllWithFilters($filters = [], $perPage = 20, $page = 1)
    {
        $builder = $this->select('transfer_internal.*, 
            creator.username as creator_name,
            coa_sumber.kode_akun as kode_akun_sumber,
            coa_sumber.nama_akun as nama_akun_sumber,
            coa_tujuan.kode_akun as kode_akun_tujuan,
            coa_tujuan.nama_akun as nama_akun_tujuan,
            jurnal.nomor_jurnal,
            jurnal.status as jurnal_status')
            ->join('users as creator', 'creator.id = transfer_internal.created_by', 'left')
            ->join('coa as coa_sumber', 'coa_sumber.id = transfer_internal.coa_id_sumber', 'left')
            ->join('coa as coa_tujuan', 'coa_tujuan.id = transfer_internal.coa_id_tujuan', 'left')
            ->join('jurnal', 'jurnal.id = transfer_internal.jurnal_id', 'left');
        
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('transfer_internal.kode_transfer', $search)
                ->orLike('transfer_internal.keterangan', $search)
                ->orLike('transfer_internal.no_referensi', $search)
                ->orLike('coa_sumber.nama_akun', $search)
                ->orLike('coa_tujuan.nama_akun', $search)
                ->orLike('jurnal.nomor_jurnal', $search)
                ->groupEnd();
        }
        
        if (!empty($filters['tanggal_mulai'])) {
            $builder->where('transfer_internal.tanggal >=', $filters['tanggal_mulai']);
        }
        
        if (!empty($filters['tanggal_selesai'])) {
            $builder->where('transfer_internal.tanggal <=', $filters['tanggal_selesai']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('transfer_internal.status', $filters['status']);
        }
        
        if (!empty($filters['coa_id'])) {
            $builder->groupStart()
                ->where('transfer_internal.coa_id_sumber', $filters['coa_id'])
                ->orWhere('transfer_internal.coa_id_tujuan', $filters['coa_id'])
                ->groupEnd();
        }

        $builder->orderBy('transfer_internal.tanggal', 'DESC')
                ->orderBy('transfer_internal.kode_transfer', 'DESC');
        
        $total = $builder->countAllResults(false);
        $offset = ($page - 1) * $perPage;
        $transfers = $builder->limit($perPage, $offset)->findAll();
        
        return [
            'data' => $transfers,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Get transfer internal by ID with details
     */
    public function getWithDetails($id)
    {
        $transfer = $this->select('transfer_internal.*, 
            creator.username as creator_name,
            creator.name as creator_fullname,
            coa_sumber.kode_akun as kode_akun_sumber,
            coa_sumber.nama_akun as nama_akun_sumber,
            coa_sumber.tipe_akun as tipe_akun_sumber,
            coa_sumber.saldo_normal as saldo_normal_sumber,
            coa_tujuan.kode_akun as kode_akun_tujuan,
            coa_tujuan.nama_akun as nama_akun_tujuan,
            coa_tujuan.tipe_akun as tipe_akun_tujuan,
            coa_tujuan.saldo_normal as saldo_normal_tujuan,
            jurnal.nomor_jurnal,
            jurnal.status as jurnal_status,
            jurnal.created_at as jurnal_created_at,
            creator_jurnal.username as posted_by_name')
            ->join('users as creator', 'creator.id = transfer_internal.created_by', 'left')
            ->join('coa as coa_sumber', 'coa_sumber.id = transfer_internal.coa_id_sumber', 'left')
            ->join('coa as coa_tujuan', 'coa_tujuan.id = transfer_internal.coa_id_tujuan', 'left')
            ->join('jurnal', 'jurnal.id = transfer_internal.jurnal_id', 'left')
            ->join('users as creator_jurnal', 'creator_jurnal.id = jurnal.created_by', 'left')
            ->where('transfer_internal.id', $id)
            ->first();
        
        if (!$transfer) {
            return null;
        }
        
        return $transfer;
    }

    /**
     * Post transfer internal (ubah status Draft → Posted)
     */
    public function postTransfer($id, $jurnalId)
    {
        $transfer = $this->find($id);
        
        if (!$transfer) {
            throw new \RuntimeException('Transfer internal tidak ditemukan');
        }
        
        if ($transfer['status'] !== 'Draft') {
            throw new \RuntimeException('Hanya transfer dengan status Draft yang bisa diposting');
        }
        
        $data = [
            'id' => $id,
            'status' => 'Posted',
            'posted_at' => date('Y-m-d H:i:s'),
            'jurnal_id' => $jurnalId
        ];
        
        return $this->save($data);
    }

    /**
     * Batalkan transfer internal
     */
    public function batalkanTransfer($id)
    {
        $transfer = $this->find($id);
        
        if (!$transfer) {
            throw new \RuntimeException('Transfer internal tidak ditemukan');
        }
        
        if ($transfer['status'] === 'Posted') {
            throw new \RuntimeException('Transfer yang sudah diposting tidak dapat dibatalkan langsung. Batalkan jurnal terlebih dahulu.');
        }
        
        $data = [
            'id' => $id,
            'status' => 'Dibatalkan'
        ];
        
        return $this->save($data);
    }

    /**
     * Get daftar COA untuk dropdown (hanya akun Kas/Bank)
     */
    public function getCoaKasBankOptions()
    {
        $coaModel = new \App\Models\CoaModel();
        
        return $coaModel->where('is_header', 0)
                        ->where('is_active', 1)
                        ->like('kode_akun', '1-11', 'after')
                        ->orderBy('kode_akun', 'ASC')
                        ->findAll();
    }

 /**
 * Get saldo akun dari MUTASI BANK + TRANSFER INTERNAL yang sudah DIPOSTING
 */
public function getSaldoAkun($coaId, $tanggal = null)
{
    if (!$tanggal) {
        $tanggal = date('Y-m-d');
    }
    
    $db = \Config\Database::connect();
    
    // ==================== 1. Dari MUTASI BANK ====================
    // MASUK: ketika akun ini sebagai coa_id_debit (Debit menambah saldo Aset)
    $builderMasuk = $db->table('mutasi_bank')
        ->select('COALESCE(SUM(jumlah), 0) as total')
        ->where('coa_id_debit', $coaId)
        ->where('status', 'Posted')
        ->where('tanggal <=', $tanggal);
    
    $resultMasuk = $builderMasuk->get()->getRow();
    $totalMasukMutasi = (float) ($resultMasuk->total ?? 0);
    
    // KELUAR: ketika akun ini sebagai coa_id_kredit (Kredit mengurangi saldo Aset)
    $builderKeluar = $db->table('mutasi_bank')
        ->select('COALESCE(SUM(jumlah), 0) as total')
        ->where('coa_id_kredit', $coaId)
        ->where('status', 'Posted')
        ->where('tanggal <=', $tanggal);
    
    $resultKeluar = $builderKeluar->get()->getRow();
    $totalKeluarMutasi = (float) ($resultKeluar->total ?? 0);
    
    // ==================== 2. Dari TRANSFER INTERNAL ====================
    // MASUK ke akun ini (sebagai akun TUJUAN) -> menambah saldo
    $builderMasukTransfer = $db->table('transfer_internal')
        ->select('COALESCE(SUM(jumlah), 0) as total')
        ->where('coa_id_tujuan', $coaId)
        ->where('status', 'Posted')
        ->where('tanggal <=', $tanggal);
    
    $resultMasukTransfer = $builderMasukTransfer->get()->getRow();
    $totalMasukTransfer = (float) ($resultMasukTransfer->total ?? 0);
    
    // KELUAR dari akun ini (sebagai akun SUMBER) -> mengurangi saldo
    $builderKeluarTransfer = $db->table('transfer_internal')
        ->select('COALESCE(SUM(jumlah), 0) as total')
        ->where('coa_id_sumber', $coaId)
        ->where('status', 'Posted')
        ->where('tanggal <=', $tanggal);
    
    $resultKeluarTransfer = $builderKeluarTransfer->get()->getRow();
    $totalKeluarTransfer = (float) ($resultKeluarTransfer->total ?? 0);
    
    // ==================== TOTAL ====================
    $totalMasuk = $totalMasukMutasi + $totalMasukTransfer;
    $totalKeluar = $totalKeluarMutasi + $totalKeluarTransfer;
    
    $saldo = $totalMasuk - $totalKeluar;
    
    return $saldo;
}

    /**
     * Get informasi rekening (untuk validasi dan tampilan)
     */
    public function getRekeningInfo($coaId)
    {
        $coaModel = new \App\Models\CoaModel();
        
        $rekening = $coaModel->select('id, kode_akun, nama_akun, tipe_akun, saldo_normal')
            ->where('id', $coaId)
            ->where('is_header', 0)
            ->where('is_active', 1)
            ->first();
        
        if (!$rekening) {
            return null;
        }
        
        $rekening['saldo'] = $this->getSaldoAkun($coaId);
        
        return $rekening;
    }

    /**
     * Get statistics untuk dashboard
     */
    public function getStats($tanggalMulai = null, $tanggalSelesai = null)
    {
        $builder = $this->where('status', 'Posted');
        
        if ($tanggalMulai) {
            $builder->where('tanggal >=', $tanggalMulai);
        }
        
        if ($tanggalSelesai) {
            $builder->where('tanggal <=', $tanggalSelesai);
        }
        
        $stats = $builder->select("
                COUNT(*) as total_transaksi,
                SUM(jumlah) as total_transfer,
                COUNT(CASE WHEN DATE(tanggal) = CURDATE() THEN 1 END) as transaksi_hari_ini,
                SUM(CASE WHEN DATE(tanggal) = CURDATE() THEN jumlah ELSE 0 END) as jumlah_hari_ini
            ")
            ->first();
        
        if (!$stats) {
            return [
                'total_transaksi' => 0,
                'total_transfer' => 0,
                'transaksi_hari_ini' => 0,
                'jumlah_hari_ini' => 0
            ];
        }
        
        return $stats;
    }

    /**
     * Get ringkasan per periode
     */
    public function getRingkasanPeriode($tahun, $bulan = null)
    {
        $builder = $this->select("
                DATE_FORMAT(tanggal, '%Y-%m') as periode,
                COUNT(*) as jumlah_transaksi,
                SUM(jumlah) as total_transfer
            ")
            ->where('status', 'Posted')
            ->where('YEAR(tanggal)', $tahun);
        
        if ($bulan) {
            $builder->where('MONTH(tanggal)', $bulan);
        }
        
        $builder->groupBy('periode')
                ->orderBy('periode', 'DESC');
        
        return $builder->findAll();
    }

    /**
     * Get export data untuk PDF (urutan ASC - dari terlama ke terbaru)
     */
    public function getExportDataForPdf($filters = [])
    {
        $builder = $this->select('transfer_internal.*, 
            creator.username as creator_name,
            coa_sumber.kode_akun as kode_akun_sumber,
            coa_sumber.nama_akun as nama_akun_sumber,
            coa_tujuan.kode_akun as kode_akun_tujuan,
            coa_tujuan.nama_akun as nama_akun_tujuan,
            jurnal.nomor_jurnal')
            ->join('users as creator', 'creator.id = transfer_internal.created_by', 'left')
            ->join('coa as coa_sumber', 'coa_sumber.id = transfer_internal.coa_id_sumber', 'left')
            ->join('coa as coa_tujuan', 'coa_tujuan.id = transfer_internal.coa_id_tujuan', 'left')
            ->join('jurnal', 'jurnal.id = transfer_internal.jurnal_id', 'left');
        
        if (!empty($filters['tanggal_mulai'])) {
            $builder->where('transfer_internal.tanggal >=', $filters['tanggal_mulai']);
        }
        
        if (!empty($filters['tanggal_selesai'])) {
            $builder->where('transfer_internal.tanggal <=', $filters['tanggal_selesai']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('transfer_internal.status', $filters['status']);
        }
        
        if (!empty($filters['coa_id'])) {
            $builder->groupStart()
                ->where('transfer_internal.coa_id_sumber', $filters['coa_id'])
                ->orWhere('transfer_internal.coa_id_tujuan', $filters['coa_id'])
                ->groupEnd();
        }
        
        $transfers = $builder->orderBy('transfer_internal.tanggal', 'ASC')
                         ->orderBy('transfer_internal.kode_transfer', 'ASC')
                         ->findAll();
        
        $exportData = [];
        foreach ($transfers as $item) {
            $exportData[] = [
                'Kode Transfer' => $item['kode_transfer'],
                'Tanggal' => $item['tanggal'],
                'Jumlah' => $item['jumlah'],
                'Keterangan' => $item['keterangan'],
                'Akun Sumber' => ($item['kode_akun_sumber'] ?? '') . ' - ' . ($item['nama_akun_sumber'] ?? ''),
                'Akun Tujuan' => ($item['kode_akun_tujuan'] ?? '') . ' - ' . ($item['nama_akun_tujuan'] ?? ''),
                'Bank Asal' => trim($item['bank_asal'] ?? '-'),
                'Bank Tujuan' => trim($item['bank_tujuan'] ?? '-'),
                'No Referensi' => $item['no_referensi'] ?? '-',
                'No Jurnal' => $item['nomor_jurnal'] ?? '-',
                'Status' => $item['status'],
                'Dibuat Oleh' => $item['creator_name'] ?? '-',
                'Dibuat Tanggal' => $item['created_at']
            ];
        }
        
        return $exportData;
    }

    /**
     * Check if transfer has been posted
     */
    public function isPosted($id)
    {
        $transfer = $this->find($id);
        return $transfer && $transfer['status'] === 'Posted';
    }

    /**
     * Get transfers by akun (sumber atau tujuan)
     */
    public function getByAkun($coaId, $limit = 10)
    {
        return $this->select('transfer_internal.*, 
                coa_sumber.nama_akun as nama_sumber,
                coa_tujuan.nama_akun as nama_tujuan')
            ->join('coa as coa_sumber', 'coa_sumber.id = transfer_internal.coa_id_sumber', 'left')
            ->join('coa as coa_tujuan', 'coa_tujuan.id = transfer_internal.coa_id_tujuan', 'left')
            ->groupStart()
                ->where('transfer_internal.coa_id_sumber', $coaId)
                ->orWhere('transfer_internal.coa_id_tujuan', $coaId)
            ->groupEnd()
            ->where('transfer_internal.status', 'Posted')
            ->orderBy('transfer_internal.tanggal', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get total transfer per akun (untuk laporan)
     */
    public function getTotalPerAkun($tanggalMulai = null, $tanggalSelesai = null)
    {
        $builder = $this->select("
                coa.id,
                coa.kode_akun,
                coa.nama_akun,
                SUM(CASE 
                    WHEN transfer_internal.coa_id_sumber = coa.id THEN transfer_internal.jumlah 
                    ELSE 0 
                END) as total_keluar,
                SUM(CASE 
                    WHEN transfer_internal.coa_id_tujuan = coa.id THEN transfer_internal.jumlah 
                    ELSE 0 
                END) as total_masuk
            ")
            ->join('coa', 'coa.id IN (transfer_internal.coa_id_sumber, transfer_internal.coa_id_tujuan)')
            ->where('transfer_internal.status', 'Posted')
            ->groupBy('coa.id, coa.kode_akun, coa.nama_akun');
        
        if ($tanggalMulai) {
            $builder->where('transfer_internal.tanggal >=', $tanggalMulai);
        }
        
        if ($tanggalSelesai) {
            $builder->where('transfer_internal.tanggal <=', $tanggalSelesai);
        }
        
        return $builder->orderBy('coa.kode_akun', 'ASC')->findAll();
    }

    /**
     * Validasi saldo sumber cukup sebelum transfer
     * (Hanya menghitung mutasi yang sudah diposting)
     */
    public function validateSaldoSumber($coaSumberId, $jumlah, $tanggal = null)
    {
        $saldoSumber = $this->getSaldoAkun($coaSumberId, $tanggal);
        
        if ($saldoSumber < $jumlah) {
            return [
                'valid' => false,
                'message' => 'Saldo akun sumber tidak mencukupi. Saldo tersedia: ' . number_format($saldoSumber, 0, ',', '.'),
                'saldo' => $saldoSumber
            ];
        }
        
        return [
            'valid' => true,
            'message' => 'Saldo mencukupi',
            'saldo' => $saldoSumber
        ];
    }

    /**
     * Rekalkulasi (untuk maintenance)
     */
    public function recalculateAll()
    {
        return true;
    }

}