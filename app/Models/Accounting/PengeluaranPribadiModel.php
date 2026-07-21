<?php
namespace App\Models\Accounting;

use CodeIgniter\Model;
use App\Models\CoaModel;
use App\Models\KaryawanModel;
use App\Models\BukuBesarModel;
use App\Models\Accounting\MutasiBankModel;
use App\Models\Accounting\KasKecilModel;

class PengeluaranPribadiModel extends Model
{
    protected $table = 'pengeluaran_pribadi';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true; // Menggunakan soft deletes
    protected $protectFields = true;
    protected $allowedFields = [
        'tanggal',
        'kode_pengeluaran',
        'karyawan_id',
        'nama_karyawan',
        'jenis',
        'jumlah',
        'keterangan',
        'tujuan_penggunaan',
        'coa_id_debit',
        'coa_debit_kode',
        'coa_debit_nama',
        'coa_id_kredit',
        'coa_kredit_kode',
        'coa_kredit_nama',
        'spk_id',
        'nomor_spk',
        'no_bukti',
        'lampiran',
        'status_hutang',
        'tanggal_jatuh_tempo',
        'tanggal_pelunasan',
        'mutasi_bank_id',
        'kas_kecil_id',
        'jumlah_dibayar',
        'status',
        'posted_at',
        'jurnal_id',
        'jurnal_pelunasan_id',
        'nomor_jurnal',
        'catatan_internal',
        'created_by',
        'updated_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'tanggal' => 'required|valid_date',
        'karyawan_id' => 'required|is_natural_no_zero',
        'jenis' => 'required|in_list[Kasbon,Reimbursement,Dana Talangan,Klaim Pribadi,Prive,Lainnya]',
        'jumlah' => 'required|numeric|greater_than[0]',
        'keterangan' => 'required',
        'coa_id_debit' => 'required|is_natural_no_zero',
        'coa_id_kredit' => 'required|is_natural_no_zero',
        'spk_id' => 'permit_empty|is_natural_no_zero',
        'no_bukti' => 'permit_empty|string',
        'status_hutang' => 'permit_empty|in_list[Belum Dibayar,Lunas,Sebagian]',
        'tanggal_jatuh_tempo' => 'permit_empty|valid_date',
        'status' => 'permit_empty|in_list[Draft,Posted,Dibatalkan]'
    ];

    protected $validationMessages = [
        'tanggal' => [
            'required' => 'Tanggal pengeluaran harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'karyawan_id' => [
            'required' => 'Karyawan harus dipilih'
        ],
        'jenis' => [
            'required' => 'Jenis pengeluaran harus dipilih'
        ],
        'jumlah' => [
            'required' => 'Jumlah harus diisi',
            'numeric' => 'Jumlah harus berupa angka',
            'greater_than' => 'Jumlah harus lebih besar dari 0'
        ],
        'keterangan' => [
            'required' => 'Keterangan harus diisi'
        ],
        'coa_id_debit' => [
            'required' => 'Akun debit harus dipilih'
        ],
        'coa_id_kredit' => [
            'required' => 'Akun kredit harus dipilih'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateKodePengeluaran', 'setDefaultValues', 'setCreatedBy', 'validateCoa', 'validateKaryawan', 'setDenormalizedData', 'validateJenisCoa'];
    protected $beforeUpdate = ['setUpdatedBy', 'validateCoa', 'validateKaryawan', 'setDenormalizedData', 'validateJenisCoa', 'validateStatusChange', 'validatePelunasan'];
    protected $beforeDelete = ['checkCanDelete'];

    /**
     * Generate kode pengeluaran otomatis
     * Format: PP-[InisialKaryawan]-YYYYMMDD-XXXX atau PP-YYYYMMDD-XXXX
     */
    protected function generateKodePengeluaran(array $data)
    {
        if (empty($data['data']['kode_pengeluaran'])) {
            $tanggal = $data['data']['tanggal'] ?? date('Y-m-d');
            $karyawanId = $data['data']['karyawan_id'] ?? null;
            
            // Cari inisial karyawan jika ada
            $inisial = '';
            if ($karyawanId) {
                $karyawanModel = new KaryawanModel();
                $karyawan = $karyawanModel->find($karyawanId);
                if ($karyawan) {
                    // Ambil inisial dari nama (3 huruf pertama)
                    $nama = $karyawan['nama_lengkap'] ?? '';
                    $inisial = substr(strtoupper(preg_replace('/[^a-zA-Z]/', '', $nama)), 0, 3);
                    if (!empty($inisial)) {
                        $inisial = '-' . $inisial;
                    }
                }
            }
            
            $prefix = 'PP' . $inisial . '-' . date('Ymd', strtotime($tanggal)) . '-';
            
            // Cari sequence terakhir untuk prefix ini
            $lastPengeluaran = $this->select('kode_pengeluaran')
                ->like('kode_pengeluaran', $prefix, 'after')
                ->orderBy('kode_pengeluaran', 'DESC')
                ->first();
            
            if ($lastPengeluaran) {
                $lastNum = substr($lastPengeluaran['kode_pengeluaran'], strlen($prefix));
                $nextNum = str_pad((int)$lastNum + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNum = '0001';
            }
            
            $data['data']['kode_pengeluaran'] = $prefix . $nextNum;
        }
        
        return $data;
    }

    /**
     * Set default values
     */
    protected function setDefaultValues(array $data)
    {
        if (!isset($data['data']['status_hutang'])) {
            $data['data']['status_hutang'] = 'Belum Dibayar';
        }
        
        if (!isset($data['data']['jumlah_dibayar'])) {
            $data['data']['jumlah_dibayar'] = 0;
        }
        
        if (!isset($data['data']['status'])) {
            $data['data']['status'] = 'Draft';
        }
        
        return $data;
    }

    /**
     * Validasi COA debit dan kredit
     */
    protected function validateCoa(array $data)
    {
        $coaModel = new CoaModel();
        
        // Validasi COA Debit
        if (!empty($data['data']['coa_id_debit'])) {
            $coaDebit = $coaModel->find($data['data']['coa_id_debit']);
            if (!$coaDebit || $coaDebit['is_header'] == 1) {
                throw new \RuntimeException('Akun debit tidak valid atau merupakan akun header');
            }
            
            // Validasi saldo_normal untuk debit (harusnya Debit)
            if ($coaDebit['saldo_normal'] !== 'Debit') {
                // Tidak selalu error, tapi bisa warning
                log_message('warning', 'Akun debit dengan saldo_normal Kredit: ' . $coaDebit['kode_akun']);
            }
        }
        
        // Validasi COA Kredit
        if (!empty($data['data']['coa_id_kredit'])) {
            $coaKredit = $coaModel->find($data['data']['coa_id_kredit']);
            if (!$coaKredit || $coaKredit['is_header'] == 1) {
                throw new \RuntimeException('Akun kredit tidak valid atau merupakan akun header');
            }
            
            // Validasi saldo_normal untuk kredit (harusnya Kredit)
            if ($coaKredit['saldo_normal'] !== 'Kredit') {
                // Tidak selalu error, tapi bisa warning
                log_message('warning', 'Akun kredit dengan saldo_normal Debit: ' . $coaKredit['kode_akun']);
            }
        }
        
        // Pastikan COA debit dan kredit berbeda
        if (!empty($data['data']['coa_id_debit']) && !empty($data['data']['coa_id_kredit']) && 
            $data['data']['coa_id_debit'] == $data['data']['coa_id_kredit']) {
            throw new \RuntimeException('Akun debit dan kredit harus berbeda');
        }
        
        return $data;
    }

    /**
     * Validasi berdasarkan jenis pengeluaran
     */
    protected function validateJenisCoa(array $data)
    {
        $jenis = $data['data']['jenis'] ?? null;
        $coaDebitId = $data['data']['coa_id_debit'] ?? null;
        $coaKreditId = $data['data']['coa_id_kredit'] ?? null;
        
        if (!$jenis || !$coaDebitId || !$coaKreditId) {
            return $data;
        }
        
        $coaModel = new CoaModel();
        $coaDebit = $coaModel->find($coaDebitId);
        $coaKredit = $coaModel->find($coaKreditId);
        
        if (!$coaDebit || !$coaKredit) {
            return $data;
        }
        
        switch ($jenis) {
            case 'Kasbon':
                // Kasbon: Debit = Piutang Karyawan, Kredit = Kas/Bank
                if (!str_starts_with($coaDebit['kode_akun'] ?? '', '1-12')) {
                    // Piutang karyawan biasanya 1-12xx
                    log_message('info', 'Kasbon: Akun debit sebaiknya akun piutang (1-12xx)');
                }
                if (!str_starts_with($coaKredit['kode_akun'] ?? '', '1-11')) {
                    throw new \RuntimeException('Untuk Kasbon, akun kredit harus merupakan akun Kas/Bank (1-11xx)');
                }
                break;
                
            case 'Reimbursement':
                // Reimbursement: Debit = Beban/Aset, Kredit = Hutang Karyawan
                if (!str_starts_with($coaDebit['kode_akun'] ?? '', '5-') && !str_starts_with($coaDebit['kode_akun'] ?? '', '1-')) {
                    throw new \RuntimeException('Untuk Reimbursement, akun debit harus merupakan akun Beban (5-xxxx) atau Aset (1-xxxx)');
                }
                if (!str_starts_with($coaKredit['kode_akun'] ?? '', '2-15')) {
                    // Hutang karyawan biasanya 2-15xx
                    log_message('info', 'Reimbursement: Akun kredit sebaiknya akun hutang karyawan (2-15xx)');
                }
                break;
                
            case 'Prive':
                // Prive: Debit = Prive (3-1301), Kredit = Kas/Bank
                if (!str_starts_with($coaDebit['kode_akun'] ?? '', '3-13')) {
                    throw new \RuntimeException('Untuk Prive, akun debit harus merupakan akun Prive (3-13xx)');
                }
                if (!str_starts_with($coaKredit['kode_akun'] ?? '', '1-11')) {
                    throw new \RuntimeException('Untuk Prive, akun kredit harus merupakan akun Kas/Bank (1-11xx)');
                }
                break;
                
            case 'Dana Talangan':
            case 'Klaim Pribadi':
                // Dana Talangan/Klaim: Debit = Piutang Karyawan, Kredit = Kas/Bank
                if (!str_starts_with($coaDebit['kode_akun'] ?? '', '1-12')) {
                    log_message('info', 'Dana Talangan: Akun debit sebaiknya akun piutang (1-12xx)');
                }
                if (!str_starts_with($coaKredit['kode_akun'] ?? '', '1-11')) {
                    throw new \RuntimeException('Untuk ' . $jenis . ', akun kredit harus merupakan akun Kas/Bank (1-11xx)');
                }
                break;
        }
        
        return $data;
    }

    /**
     * Validasi karyawan
     */
    protected function validateKaryawan(array $data)
    {
        if (!empty($data['data']['karyawan_id'])) {
            $karyawanModel = new KaryawanModel();
            $karyawan = $karyawanModel->find($data['data']['karyawan_id']);
            
            if (!$karyawan) {
                throw new \RuntimeException('Karyawan tidak ditemukan');
            }
            
            // Validasi untuk jenis Prive (hanya untuk direktur/pemilik)
            $jenis = $data['data']['jenis'] ?? null;
            if ($jenis === 'Prive') {
                // Cek apakah karyawan adalah direktur atau pemilik
                $jabatan = strtolower($karyawan['jabatan'] ?? '');
                if (!str_contains($jabatan, 'direktur') && !str_contains($jabatan, 'owner') && !str_contains($jabatan, 'pemilik')) {
                    log_message('warning', 'Prive dilakukan oleh karyawan non-direktur: ' . $karyawan['nama_lengkap']);
                }
            }
        }
        
        return $data;
    }

   /**
 * Set denormalized data
 */
protected function setDenormalizedData(array $data)
{
    // Fungsi helper untuk konversi ke array
    $toArray = function($item) {
        if (is_object($item)) {
            return json_decode(json_encode($item), true);
        }
        return $item;
    };
    
    // Set data karyawan
    if (!empty($data['data']['karyawan_id'])) {
        $karyawanModel = new \App\Models\KaryawanModel();
        $karyawan = $karyawanModel->find($data['data']['karyawan_id']);
        
        if ($karyawan) {
            $karyawan = $toArray($karyawan);
            $data['data']['nama_karyawan'] = $karyawan['nama_lengkap'] ?? '';
        }
    }
    
    // Set data COA Debit
    if (!empty($data['data']['coa_id_debit'])) {
        $coaModel = new \App\Models\CoaModel();
        $coaDebit = $coaModel->find($data['data']['coa_id_debit']);
        
        if ($coaDebit) {
            $coaDebit = $toArray($coaDebit);
            $data['data']['coa_debit_kode'] = $coaDebit['kode_akun'] ?? '';
            $data['data']['coa_debit_nama'] = $coaDebit['nama_akun'] ?? '';
        }
    }
    
    // Set data COA Kredit
    if (!empty($data['data']['coa_id_kredit'])) {
        $coaModel = new \App\Models\CoaModel();
        $coaKredit = $coaModel->find($data['data']['coa_id_kredit']);
        
        if ($coaKredit) {
            $coaKredit = $toArray($coaKredit);
            $data['data']['coa_kredit_kode'] = $coaKredit['kode_akun'] ?? '';
            $data['data']['coa_kredit_nama'] = $coaKredit['nama_akun'] ?? '';
        }
    }
    
    // Set data SPK
    if (!empty($data['data']['spk_id'])) {
        $spkModel = new \App\Models\Teknisi\SpkInstalasiModel();
        $spk = $spkModel->find($data['data']['spk_id']);
        
        if ($spk) {
            $spk = $toArray($spk);
            $data['data']['nomor_spk'] = $spk['nomor_spk'] ?? '';
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
                
                // Jika status berubah menjadi Posted, pastikan jurnal_id sudah terisi
                if ($data['data']['status'] === 'Posted' && $current['status'] === 'Draft') {
                    if (empty($current['jurnal_id']) && empty($data['data']['jurnal_id'])) {
                        throw new \RuntimeException('Pengeluaran harus memiliki jurnal_id sebelum diposting');
                    }
                    
                    if (empty($current['posted_at']) && empty($data['data']['posted_at'])) {
                        $data['data']['posted_at'] = date('Y-m-d H:i:s');
                    }
                }
                
                // Jika status berubah menjadi Dibatalkan, pastikan belum diposting
                if ($data['data']['status'] === 'Dibatalkan' && $current['status'] === 'Posted') {
                    throw new \RuntimeException('Pengeluaran yang sudah diposting tidak dapat dibatalkan');
                }
            }
        }
        
        return $data;
    }

    /**
     * Validasi pelunasan
     */
    protected function validatePelunasan(array $data)
    {
        if (isset($data['data']['jumlah_dibayar']) || isset($data['data']['status_hutang'])) {
            $id = $data['id'][0] ?? null;
            
            if ($id) {
                $current = $this->find($id);
                
                // Hitung sisa hutang
                $jumlah = $data['data']['jumlah'] ?? $current['jumlah'];
                $jumlahDibayar = $data['data']['jumlah_dibayar'] ?? $current['jumlah_dibayar'];
                $sisa = $jumlah - $jumlahDibayar;
                
                // Update status hutang berdasarkan sisa
                if ($sisa <= 0) {
                    $data['data']['status_hutang'] = 'Lunas';
                    $data['data']['tanggal_pelunasan'] = date('Y-m-d');
                } elseif ($jumlahDibayar > 0) {
                    $data['data']['status_hutang'] = 'Sebagian';
                } else {
                    $data['data']['status_hutang'] = 'Belum Dibayar';
                }
                
                // Validasi jika lunas tapi tidak ada jurnal pelunasan
                if ($data['data']['status_hutang'] === 'Lunas' && empty($data['data']['jurnal_pelunasan_id']) && empty($current['jurnal_pelunasan_id'])) {
                    log_message('warning', 'Pengeluaran lunas tanpa jurnal pelunasan: ID ' . $id);
                }
            }
        }
        
        return $data;
    }

    /**
     * Validasi sebelum delete
     */
    protected function checkCanDelete(array $data)
    {
        $id = $data['id'][0] ?? null;
        
        if ($id) {
            $pengeluaran = $this->find($id);
            
            if ($pengeluaran && $pengeluaran['status'] === 'Posted') {
                throw new \RuntimeException('Pengeluaran pribadi yang sudah diposting tidak dapat dihapus');
            }
            
            if ($pengeluaran && $pengeluaran['status_hutang'] === 'Lunas') {
                throw new \RuntimeException('Pengeluaran yang sudah lunas tidak dapat dihapus');
            }
        }
        
        return $data;
    }

    protected function setCreatedBy(array $data)
    {
        $data['data']['created_by'] = session()->get('user_id');
        return $data;
    }

    protected function setUpdatedBy(array $data)
    {
        $data['data']['updated_by'] = session()->get('user_id');
        return $data;
    }

    /**
     * Get all pengeluaran pribadi with filters and pagination
     */
    public function getAllWithFilters($filters = [], $perPage = 20, $page = 1)
    {
        $builder = $this->select('pengeluaran_pribadi.*, 
            creator.username as creator_name,
            updater.username as updater_name,
            karyawan.nik as karyawan_nik,
            karyawan.jabatan as karyawan_jabatan,
            karyawan.departemen as karyawan_departemen,
            coa_debit.kode_akun as kode_akun_debit,
            coa_debit.nama_akun as nama_akun_debit,
            coa_debit.tipe_akun as tipe_akun_debit,
            coa_kredit.kode_akun as kode_akun_kredit,
            coa_kredit.nama_akun as nama_akun_kredit,
            coa_kredit.tipe_akun as tipe_akun_kredit,
            jurnal.nomor_jurnal,
            jurnal.status as jurnal_status,
            jurnal_pelunasan.nomor_jurnal as nomor_jurnal_pelunasan,
            mutasi_bank.kode_transaksi as kode_mutasi_bank,
            kas_kecil.kode_transaksi as kode_kas_kecil')
            ->join('users as creator', 'creator.id = pengeluaran_pribadi.created_by', 'left')
            ->join('users as updater', 'updater.id = pengeluaran_pribadi.updated_by', 'left')
            ->join('karyawan', 'karyawan.id = pengeluaran_pribadi.karyawan_id', 'left')
            ->join('coa as coa_debit', 'coa_debit.id = pengeluaran_pribadi.coa_id_debit', 'left')
            ->join('coa as coa_kredit', 'coa_kredit.id = pengeluaran_pribadi.coa_id_kredit', 'left')
            ->join('jurnal', 'jurnal.id = pengeluaran_pribadi.jurnal_id', 'left')
            ->join('jurnal as jurnal_pelunasan', 'jurnal_pelunasan.id = pengeluaran_pribadi.jurnal_pelunasan_id', 'left')
            ->join('mutasi_bank', 'mutasi_bank.id = pengeluaran_pribadi.mutasi_bank_id', 'left')
            ->join('kas_kecil', 'kas_kecil.id = pengeluaran_pribadi.kas_kecil_id', 'left');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('pengeluaran_pribadi.kode_pengeluaran', $search)
                ->orLike('pengeluaran_pribadi.keterangan', $search)
                ->orLike('pengeluaran_pribadi.tujuan_penggunaan', $search)
                ->orLike('pengeluaran_pribadi.no_bukti', $search)
                ->orLike('pengeluaran_pribadi.nama_karyawan', $search)
                ->orLike('karyawan.nik', $search)
                ->orLike('pengeluaran_pribadi.nomor_spk', $search)
                ->orLike('coa_debit.nama_akun', $search)
                ->orLike('coa_kredit.nama_akun', $search)
                ->groupEnd();
        }
        
        if (!empty($filters['tanggal_mulai'])) {
            $builder->where('pengeluaran_pribadi.tanggal >=', $filters['tanggal_mulai']);
        }
        
        if (!empty($filters['tanggal_selesai'])) {
            $builder->where('pengeluaran_pribadi.tanggal <=', $filters['tanggal_selesai']);
        }
        
        if (!empty($filters['jenis'])) {
            if (is_array($filters['jenis'])) {
                $builder->whereIn('pengeluaran_pribadi.jenis', $filters['jenis']);
            } else {
                $builder->where('pengeluaran_pribadi.jenis', $filters['jenis']);
            }
        }
        
        if (!empty($filters['karyawan_id'])) {
            $builder->where('pengeluaran_pribadi.karyawan_id', $filters['karyawan_id']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('pengeluaran_pribadi.status', $filters['status']);
        }
        
        if (!empty($filters['status_hutang'])) {
            $builder->where('pengeluaran_pribadi.status_hutang', $filters['status_hutang']);
        }
        
        if (!empty($filters['spk_id'])) {
            $builder->where('pengeluaran_pribadi.spk_id', $filters['spk_id']);
        }
        
        if (!empty($filters['coa_debit_id'])) {
            $builder->where('pengeluaran_pribadi.coa_id_debit', $filters['coa_debit_id']);
        }
        
        if (!empty($filters['coa_kredit_id'])) {
            $builder->where('pengeluaran_pribadi.coa_id_kredit', $filters['coa_kredit_id']);
        }
        
        if (isset($filters['hutang_belum_lunas']) && $filters['hutang_belum_lunas']) {
            $builder->where('pengeluaran_pribadi.status_hutang !=', 'Lunas');
        }
        
        $builder->orderBy('pengeluaran_pribadi.tanggal', 'DESC')
                ->orderBy('pengeluaran_pribadi.kode_pengeluaran', 'DESC');
        
        // Get total for pagination
        $total = $builder->countAllResults(false);
        
        // Get paginated results
        $offset = ($page - 1) * $perPage;
        $pengeluaran = $builder->limit($perPage, $offset)->findAll();
        
        // Hitung sisa hutang
        foreach ($pengeluaran as &$item) {
            $item['sisa_hutang'] = $item['jumlah'] - ($item['jumlah_dibayar'] ?? 0);
        }
        
        return [
            'data' => $pengeluaran,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Get pengeluaran pribadi by ID with details
     */
    public function getWithDetails($id)
    {
        $pengeluaran = $this->select('pengeluaran_pribadi.*, 
            creator.username as creator_name,
            creator.name as creator_fullname,
            updater.username as updater_name,
            karyawan.nik as karyawan_nik,
            karyawan.nama_lengkap as karyawan_nama_lengkap,
            karyawan.jabatan as karyawan_jabatan,
            karyawan.departemen as karyawan_departemen,
            karyawan.no_rekening as karyawan_no_rekening,
            karyawan.bank as karyawan_bank,
            karyawan.nama_rekening as karyawan_nama_rekening,
            coa_debit.kode_akun as kode_akun_debit,
            coa_debit.nama_akun as nama_akun_debit,
            coa_debit.tipe_akun as tipe_akun_debit,
            coa_debit.saldo_normal as saldo_normal_debit,
            coa_kredit.kode_akun as kode_akun_kredit,
            coa_kredit.nama_akun as nama_akun_kredit,
            coa_kredit.tipe_akun as tipe_akun_kredit,
            coa_kredit.saldo_normal as saldo_normal_kredit,
            jurnal.nomor_jurnal,
            jurnal.status as jurnal_status,
            jurnal.created_at as jurnal_created_at,
            jurnal.keterangan as jurnal_keterangan,
            jurnal_pelunasan.nomor_jurnal as nomor_jurnal_pelunasan,
            jurnal_pelunasan.status as jurnal_pelunasan_status,
            jurnal_pelunasan.created_at as jurnal_pelunasan_created_at,
            mutasi_bank.kode_transaksi as kode_mutasi_bank,
            mutasi_bank.tanggal as tanggal_mutasi_bank,
            mutasi_bank.jumlah as jumlah_mutasi_bank,
            mutasi_bank.keterangan as keterangan_mutasi_bank,
            kas_kecil.kode_transaksi as kode_kas_kecil,
            kas_kecil.tanggal as tanggal_kas_kecil,
            kas_kecil.jumlah as jumlah_kas_kecil,
            kas_kecil.keterangan as keterangan_kas_kecil')
            ->join('users as creator', 'creator.id = pengeluaran_pribadi.created_by', 'left')
            ->join('users as updater', 'updater.id = pengeluaran_pribadi.updated_by', 'left')
            ->join('karyawan', 'karyawan.id = pengeluaran_pribadi.karyawan_id', 'left')
            ->join('coa as coa_debit', 'coa_debit.id = pengeluaran_pribadi.coa_id_debit', 'left')
            ->join('coa as coa_kredit', 'coa_kredit.id = pengeluaran_pribadi.coa_id_kredit', 'left')
            ->join('jurnal', 'jurnal.id = pengeluaran_pribadi.jurnal_id', 'left')
            ->join('jurnal as jurnal_pelunasan', 'jurnal_pelunasan.id = pengeluaran_pribadi.jurnal_pelunasan_id', 'left')
            ->join('mutasi_bank', 'mutasi_bank.id = pengeluaran_pribadi.mutasi_bank_id', 'left')
            ->join('kas_kecil', 'kas_kecil.id = pengeluaran_pribadi.kas_kecil_id', 'left')
            ->where('pengeluaran_pribadi.id', $id)
            ->first();
        
        if (!$pengeluaran) {
            return null;
        }
        
        // Hitung sisa hutang
        $pengeluaran['sisa_hutang'] = $pengeluaran['jumlah'] - ($pengeluaran['jumlah_dibayar'] ?? 0);
        
        // Tambahkan informasi SPK jika ada
        if (!empty($pengeluaran['spk_id'])) {
            $spkModel = new \App\Models\Teknisi\SpkInstalasiModel();
            $spk = $spkModel->select('id, nomor_spk, judul_pekerjaan, lokasi, client_nama')
                ->find($pengeluaran['spk_id']);
            $pengeluaran['spk'] = $spk;
        }
        
        return $pengeluaran;
    }

    /**
     * Get daftar karyawan untuk dropdown
     */
    public function getKaryawanOptions()
    {
        $karyawanModel = new KaryawanModel();
        
        return $karyawanModel->select('id, nik, nama_lengkap, jabatan')
            ->where('status_karyawan', 'Tetap')
            ->orWhere('status_karyawan', 'Kontrak')
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();
    }

   /**
 * Get daftar COA untuk dropdown berdasarkan jenis
 */
public function getCoaOptions($jenis = null, $tipe = 'debit')
{
    $coaModel = new \App\Models\CoaModel();
    
    $builder = $coaModel->where('is_header', 0)
        ->where('is_active', 1);
    
    if ($jenis && $tipe) {
        switch ($jenis) {
            case 'Kasbon':
                if ($tipe === 'debit') {
                    // Piutang Karyawan (1-12xx)
                    $builder->like('kode_akun', '1-12', 'after');
                } else {
                    // Kas/Bank (1-11xx)
                    $builder->like('kode_akun', '1-11', 'after');
                }
                break;
                
            case 'Reimbursement':
                if ($tipe === 'debit') {
                    // Beban (5-xxxx) atau Aset non-kas (1-xxxx selain 1-11)
                    $builder->groupStart()
                        ->like('kode_akun', '5-', 'after')
                        ->orWhere('kode_akun LIKE', '1-%')
                        ->where('kode_akun NOT LIKE', '1-11%')
                        ->groupEnd();
                } else {
                    // Hutang Karyawan (2-15xx)
                    $builder->like('kode_akun', '2-15', 'after');
                }
                break;
                
            case 'Prive':
                if ($tipe === 'debit') {
                    // Prive (3-1301)
                    $builder->like('kode_akun', '3-13', 'after');
                } else {
                    // Kas/Bank (1-11xx)
                    $builder->like('kode_akun', '1-11', 'after');
                }
                break;
                
            case 'Dana Talangan':
            case 'Klaim Pribadi':
                if ($tipe === 'debit') {
                    // Piutang Karyawan (1-12xx)
                    $builder->like('kode_akun', '1-12', 'after');
                } else {
                    // Kas/Bank (1-11xx)
                    $builder->like('kode_akun', '1-11', 'after');
                }
                break;
                
            default:
                // Semua akun aktif
                break;
        }
    }
    
    return $builder->orderBy('kode_akun', 'ASC')->findAll();
}

   /**
 * Get daftar SPK untuk dropdown
 */
public function getSpkOptions()
{
    $spkModel = new \App\Models\Teknisi\SpkInstalasiModel();
    
    $result = $spkModel->select('id, nomor_spk, judul_pekerjaan, status')
        ->whereIn('status', ['Dijadwalkan', 'Dalam Pengerjaan'])
        ->orderBy('nomor_spk', 'DESC')
        ->findAll();
    
    // Konversi ke array jika perlu
    if (!empty($result) && is_object($result[0])) {
        return json_decode(json_encode($result), true);
    }
    
    return $result;
}

    /**
     * Post pengeluaran pribadi (ubah status Draft → Posted) dan buat jurnal
     */
    public function postPengeluaran($id, $jurnalId)
    {
        $pengeluaran = $this->find($id);
        
        if (!$pengeluaran) {
            throw new \RuntimeException('Pengeluaran pribadi tidak ditemukan');
        }
        
        if ($pengeluaran['status'] !== 'Draft') {
            throw new \RuntimeException('Hanya pengeluaran dengan status Draft yang bisa diposting');
        }
        
        $data = [
            'id' => $id,
            'status' => 'Posted',
            'posted_at' => date('Y-m-d H:i:s'),
            'jurnal_id' => $jurnalId,
            'nomor_jurnal' => $this->getNomorJurnal($jurnalId)
        ];
        
        return $this->save($data);
    }

    /**
     * Batalkan pengeluaran pribadi
     */
    public function batalkanPengeluaran($id)
    {
        $pengeluaran = $this->find($id);
        
        if (!$pengeluaran) {
            throw new \RuntimeException('Pengeluaran pribadi tidak ditemukan');
        }
        
        if ($pengeluaran['status'] === 'Posted') {
            throw new \RuntimeException('Pengeluaran yang sudah diposting tidak dapat dibatalkan langsung. Batalkan jurnal terlebih dahulu.');
        }
        
        $data = [
            'id' => $id,
            'status' => 'Dibatalkan'
        ];
        
        return $this->save($data);
    }

    /**
     * Proses pelunasan pengeluaran pribadi
     */
    public function prosesPelunasan($id, $jumlahBayar, $metode, $referensiId, $jurnalPelunasanId = null)
    {
        $pengeluaran = $this->find($id);
        
        if (!$pengeluaran) {
            throw new \RuntimeException('Pengeluaran pribadi tidak ditemukan');
        }
        
        if ($pengeluaran['status_hutang'] === 'Lunas') {
            throw new \RuntimeException('Pengeluaran ini sudah lunas');
        }
        
        $jumlahDibayarBaru = ($pengeluaran['jumlah_dibayar'] ?? 0) + $jumlahBayar;
        $sisa = $pengeluaran['jumlah'] - $jumlahDibayarBaru;
        
        if ($sisa < 0) {
            throw new \RuntimeException('Jumlah pembayaran melebihi sisa hutang');
        }
        
        $data = [
            'id' => $id,
            'jumlah_dibayar' => $jumlahDibayarBaru
        ];
        
        // Set status hutang
        if ($sisa <= 0) {
            $data['status_hutang'] = 'Lunas';
            $data['tanggal_pelunasan'] = date('Y-m-d');
        } else {
            $data['status_hutang'] = 'Sebagian';
        }
        
        // Set referensi pelunasan
        if ($metode === 'bank' && $referensiId) {
            $data['mutasi_bank_id'] = $referensiId;
        } elseif ($metode === 'kas_kecil' && $referensiId) {
            $data['kas_kecil_id'] = $referensiId;
        }
        
        if ($jurnalPelunasanId) {
            $data['jurnal_pelunasan_id'] = $jurnalPelunasanId;
        }
        
        return $this->save($data);
    }

    /**
     * Get nomor jurnal dari tabel jurnal
     */
    private function getNomorJurnal($jurnalId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('jurnal');
        $jurnal = $builder->select('nomor_jurnal')
            ->where('id', $jurnalId)
            ->get()
            ->getRowArray();
        
        return $jurnal ? $jurnal['nomor_jurnal'] : null;
    }

    /**
     * Get ringkasan hutang per karyawan
     */
    public function getRingkasanHutangPerKaryawan($status = null)
    {
        $builder = $this->select("
                pengeluaran_pribadi.karyawan_id,
                karyawan.nik,
                karyawan.nama_lengkap,
                karyawan.jabatan,
                karyawan.departemen,
                COUNT(*) as jumlah_transaksi,
                SUM(pengeluaran_pribadi.jumlah) as total_hutang,
                SUM(pengeluaran_pribadi.jumlah_dibayar) as total_dibayar,
                SUM(pengeluaran_pribadi.jumlah - pengeluaran_pribadi.jumlah_dibayar) as total_sisa
            ")
            ->join('karyawan', 'karyawan.id = pengeluaran_pribadi.karyawan_id', 'left')
            ->where('pengeluaran_pribadi.status', 'Posted')
            ->groupBy('pengeluaran_pribadi.karyawan_id, karyawan.nik, karyawan.nama_lengkap, karyawan.jabatan, karyawan.departemen');
        
        if ($status) {
            if ($status === 'Belum Lunas') {
                $builder->where('pengeluaran_pribadi.status_hutang !=', 'Lunas');
            } else {
                $builder->where('pengeluaran_pribadi.status_hutang', $status);
            }
        }
        
        return $builder->orderBy('total_sisa', 'DESC')->findAll();
    }

    /**
     * Get ringkasan hutang per jenis
     */
    public function getRingkasanPerJenis($tanggalMulai = null, $tanggalSelesai = null)
    {
        $builder = $this->select("
                pengeluaran_pribadi.jenis,
                COUNT(*) as jumlah_transaksi,
                SUM(pengeluaran_pribadi.jumlah) as total,
                SUM(pengeluaran_pribadi.jumlah_dibayar) as total_dibayar,
                SUM(pengeluaran_pribadi.jumlah - pengeluaran_pribadi.jumlah_dibayar) as total_sisa,
                COUNT(CASE WHEN pengeluaran_pribadi.status_hutang = 'Lunas' THEN 1 END) as lunas,
                COUNT(CASE WHEN pengeluaran_pribadi.status_hutang = 'Belum Dibayar' THEN 1 END) as belum_dibayar,
                COUNT(CASE WHEN pengeluaran_pribadi.status_hutang = 'Sebagian' THEN 1 END) as sebagian
            ")
            ->where('pengeluaran_pribadi.status', 'Posted');
        
        if ($tanggalMulai) {
            $builder->where('pengeluaran_pribadi.tanggal >=', $tanggalMulai);
        }
        
        if ($tanggalSelesai) {
            $builder->where('pengeluaran_pribadi.tanggal <=', $tanggalSelesai);
        }
        
        $builder->groupBy('pengeluaran_pribadi.jenis');
        
        return $builder->orderBy('total', 'DESC')->findAll();
    }

    /**
     * Get daftar hutang yang jatuh tempo
     */
    public function getHutangJatuhTempo($tanggal = null)
    {
        if (!$tanggal) {
            $tanggal = date('Y-m-d');
        }
        
        return $this->select('pengeluaran_pribadi.*, 
                karyawan.nama_lengkap as karyawan_nama,
                karyawan.nik as karyawan_nik,
                karyawan.telepon as karyawan_telepon')
            ->join('karyawan', 'karyawan.id = pengeluaran_pribadi.karyawan_id', 'left')
            ->where('pengeluaran_pribadi.status', 'Posted')
            ->where('pengeluaran_pribadi.status_hutang !=', 'Lunas')
            ->where('pengeluaran_pribadi.tanggal_jatuh_tempo <=', $tanggal)
            ->where('pengeluaran_pribadi.tanggal_jatuh_tempo IS NOT NULL')
            ->orderBy('pengeluaran_pribadi.tanggal_jatuh_tempo', 'ASC')
            ->findAll();
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
                SUM(jumlah) as total_nominal,
                SUM(jumlah_dibayar) as total_dibayar,
                SUM(jumlah - jumlah_dibayar) as total_sisa_hutang,
                COUNT(CASE WHEN jenis = 'Kasbon' THEN 1 END) as jumlah_kasbon,
                COUNT(CASE WHEN jenis = 'Reimbursement' THEN 1 END) as jumlah_reimbursement,
                COUNT(CASE WHEN jenis = 'Prive' THEN 1 END) as jumlah_prive,
                COUNT(CASE WHEN status_hutang = 'Belum Dibayar' THEN 1 END) as belum_dibayar,
                COUNT(CASE WHEN status_hutang = 'Lunas' THEN 1 END) as lunas,
                COUNT(CASE WHEN status_hutang = 'Sebagian' THEN 1 END) as sebagian,
                COUNT(CASE WHEN DATE(tanggal) = CURDATE() THEN 1 END) as transaksi_hari_ini
            ")
            ->first();
        
        if (!$stats) {
            return [
                'total_transaksi' => 0,
                'total_nominal' => 0,
                'total_dibayar' => 0,
                'total_sisa_hutang' => 0,
                'jumlah_kasbon' => 0,
                'jumlah_reimbursement' => 0,
                'jumlah_prive' => 0,
                'belum_dibayar' => 0,
                'lunas' => 0,
                'sebagian' => 0,
                'transaksi_hari_ini' => 0
            ];
        }
        
        return $stats;
    }

    /**
     * Export data untuk Excel
     */
    public function getExportData($filters = [])
    {
        $builder = $this->select('pengeluaran_pribadi.*, 
            creator.username as creator_name,
            karyawan.nik as karyawan_nik,
            karyawan.nama_lengkap as karyawan_nama,
            coa_debit.kode_akun as kode_akun_debit,
            coa_debit.nama_akun as nama_akun_debit,
            coa_kredit.kode_akun as kode_akun_kredit,
            coa_kredit.nama_akun as nama_akun_kredit,
            jurnal.nomor_jurnal,
            jurnal_pelunasan.nomor_jurnal as nomor_jurnal_pelunasan')
            ->join('users as creator', 'creator.id = pengeluaran_pribadi.created_by', 'left')
            ->join('karyawan', 'karyawan.id = pengeluaran_pribadi.karyawan_id', 'left')
            ->join('coa as coa_debit', 'coa_debit.id = pengeluaran_pribadi.coa_id_debit', 'left')
            ->join('coa as coa_kredit', 'coa_kredit.id = pengeluaran_pribadi.coa_id_kredit', 'left')
            ->join('jurnal', 'jurnal.id = pengeluaran_pribadi.jurnal_id', 'left')
            ->join('jurnal as jurnal_pelunasan', 'jurnal_pelunasan.id = pengeluaran_pribadi.jurnal_pelunasan_id', 'left');
        
        // Apply filters
        if (!empty($filters['tanggal_mulai'])) {
            $builder->where('pengeluaran_pribadi.tanggal >=', $filters['tanggal_mulai']);
        }
        
        if (!empty($filters['tanggal_selesai'])) {
            $builder->where('pengeluaran_pribadi.tanggal <=', $filters['tanggal_selesai']);
        }
        
        if (!empty($filters['jenis'])) {
            $builder->where('pengeluaran_pribadi.jenis', $filters['jenis']);
        }
        
        if (!empty($filters['karyawan_id'])) {
            $builder->where('pengeluaran_pribadi.karyawan_id', $filters['karyawan_id']);
        }
        
        if (!empty($filters['status_hutang'])) {
            $builder->where('pengeluaran_pribadi.status_hutang', $filters['status_hutang']);
        }
        
        $builder->orderBy('pengeluaran_pribadi.tanggal', 'DESC')
                ->orderBy('pengeluaran_pribadi.kode_pengeluaran', 'DESC');
        
        $pengeluaran = $builder->findAll();
        
        // Format untuk export
        $exportData = [];
        foreach ($pengeluaran as $item) {
            $sisa = $item['jumlah'] - ($item['jumlah_dibayar'] ?? 0);
            
            $exportData[] = [
                'Kode Pengeluaran' => $item['kode_pengeluaran'],
                'Tanggal' => $item['tanggal'],
                'Karyawan' => ($item['karyawan_nik'] ?? '') . ' - ' . ($item['karyawan_nama'] ?? ''),
                'Jenis' => $item['jenis'],
                'Jumlah' => $item['jumlah'],
                'Jumlah Dibayar' => $item['jumlah_dibayar'] ?? 0,
                'Sisa Hutang' => $sisa,
                'Keterangan' => $item['keterangan'],
                'Tujuan Penggunaan' => $item['tujuan_penggunaan'] ?? '-',
                'Akun Debit' => ($item['kode_akun_debit'] ?? '') . ' - ' . ($item['nama_akun_debit'] ?? ''),
                'Akun Kredit' => ($item['kode_akun_kredit'] ?? '') . ' - ' . ($item['nama_akun_kredit'] ?? ''),
                'SPK/Proyek' => $item['nomor_spk'] ?? '-',
                'No Bukti' => $item['no_bukti'] ?? '-',
                'No Jurnal' => $item['nomor_jurnal'] ?? '-',
                'No Jurnal Pelunasan' => $item['nomor_jurnal_pelunasan'] ?? '-',
                'Status Hutang' => $item['status_hutang'],
                'Tanggal Jatuh Tempo' => $item['tanggal_jatuh_tempo'] ?? '-',
                'Tanggal Pelunasan' => $item['tanggal_pelunasan'] ?? '-',
                'Status' => $item['status'],
                'Dibuat Oleh' => $item['creator_name'] ?? '-',
                'Dibuat Tanggal' => $item['created_at']
            ];
        }
        
        return $exportData;
    }

    /**
     * Validasi sebelum pelunasan
     */
    public function validatePelunasanSebelum($id, $jumlahBayar)
    {
        $pengeluaran = $this->find($id);
        
        if (!$pengeluaran) {
            return [
                'valid' => false,
                'message' => 'Pengeluaran tidak ditemukan'
            ];
        }
        
        if ($pengeluaran['status_hutang'] === 'Lunas') {
            return [
                'valid' => false,
                'message' => 'Pengeluaran ini sudah lunas'
            ];
        }
        
        $sisa = $pengeluaran['jumlah'] - ($pengeluaran['jumlah_dibayar'] ?? 0);
        
        if ($jumlahBayar <= 0) {
            return [
                'valid' => false,
                'message' => 'Jumlah pembayaran harus lebih dari 0'
            ];
        }
        
        if ($jumlahBayar > $sisa) {
            return [
                'valid' => false,
                'message' => 'Jumlah pembayaran melebihi sisa hutang. Sisa: ' . number_format($sisa, 0, ',', '.')
            ];
        }
        
        return [
            'valid' => true,
            'message' => 'Valid',
            'sisa' => $sisa,
            'jumlah' => $pengeluaran['jumlah'],
            'jumlah_dibayar' => $pengeluaran['jumlah_dibayar'] ?? 0,
            'karyawan_id' => $pengeluaran['karyawan_id'],
            'karyawan_nama' => $pengeluaran['nama_karyawan']
        ];
    }

    /**
     * Check if pengeluaran has been posted
     */
    public function isPosted($id)
    {
        $pengeluaran = $this->find($id);
        return $pengeluaran && $pengeluaran['status'] === 'Posted';
    }

    /**
     * Check if pengeluaran is lunas
     */
    public function isLunas($id)
    {
        $pengeluaran = $this->find($id);
        return $pengeluaran && $pengeluaran['status_hutang'] === 'Lunas';
    }

    /**
     * Rekalkulasi status hutang (untuk maintenance)
     */
    public function recalculateStatusHutang()
    {
        $db = \Config\Database::connect();
        $db->transStart();
        
        // Ambil semua pengeluaran yang sudah diposting
        $pengeluaran = $this->where('status', 'Posted')
            ->orderBy('id', 'ASC')
            ->findAll();
        
        foreach ($pengeluaran as $item) {
            $sisa = $item['jumlah'] - ($item['jumlah_dibayar'] ?? 0);
            
            $statusHutang = 'Belum Dibayar';
            if ($sisa <= 0) {
                $statusHutang = 'Lunas';
            } elseif ($item['jumlah_dibayar'] > 0) {
                $statusHutang = 'Sebagian';
            }
            
            if ($statusHutang !== $item['status_hutang']) {
                $this->update($item['id'], ['status_hutang' => $statusHutang]);
            }
        }
        
        $db->transComplete();
        
        return $db->transStatus();
    }
}