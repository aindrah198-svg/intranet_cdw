<?php
namespace App\Models\Accounting;

use CodeIgniter\Model;
use App\Models\CoaModel;
use App\Models\BukuBesarModel;
use App\Models\Accounting\MutasiBankModel;

class KasKecilModel extends Model
{
    protected $table = 'kas_kecil';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true; // Menggunakan soft deletes
    protected $protectFields = true;
    protected $allowedFields = [
        'tanggal',
        'kode_transaksi',
        'tipe',
        'jumlah',
        'keterangan',
        'coa_lawan_id',
        'coa_lawan_kode',
        'coa_lawan_nama',
        'karyawan_id',
        'nama_karyawan',
        'spk_id',
        'nomor_spk',
        'no_bukti',
        'lampiran',
        'status',
        'posted_at',
        'jurnal_id',
        'nomor_jurnal',
        'metode_imprest',
        'saldo_setelah',
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
        'tipe' => 'required|in_list[Pemasukan,Pengeluaran]',
        'jumlah' => 'required|numeric|greater_than[0]',
        'keterangan' => 'required',
        'coa_lawan_id' => 'required|is_natural_no_zero',
        'karyawan_id' => 'permit_empty|is_natural_no_zero',
        'spk_id' => 'permit_empty|is_natural_no_zero',
        'no_bukti' => 'permit_empty|string',
        'status' => 'permit_empty|in_list[Draft,Posted,Dibatalkan]',
        'metode_imprest' => 'permit_empty|in_list[0,1]'
    ];

    protected $validationMessages = [
        'tanggal' => [
            'required' => 'Tanggal transaksi harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'tipe' => [
            'required' => 'Tipe transaksi (Pemasukan/Pengeluaran) harus dipilih'
        ],
        'jumlah' => [
            'required' => 'Jumlah harus diisi',
            'numeric' => 'Jumlah harus berupa angka',
            'greater_than' => 'Jumlah harus lebih besar dari 0'
        ],
        'keterangan' => [
            'required' => 'Keterangan harus diisi'
        ],
        'coa_lawan_id' => [
            'required' => 'Akun lawan harus dipilih'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = ['generateKodeTransaksi', 'setDefaultValues', 'setCreatedBy', 'validateCoaLawan', 'validateSaldo', 'setDenormalizedData', 'hitungSaldoSetelah'];
    protected $beforeUpdate = ['setUpdatedBy', 'validateCoaLawan', 'validateSaldoOnUpdate', 'setDenormalizedData', 'hitungSaldoSetelah', 'validateStatusChange'];
    protected $beforeDelete = ['checkCanDelete'];

    /**
     * Generate kode transaksi otomatis
     * Format: KK-YYYYMMDD-XXXX
     */
    protected function generateKodeTransaksi(array $data)
    {
        if (empty($data['data']['kode_transaksi'])) {
            $tanggal = $data['data']['tanggal'] ?? date('Y-m-d');
            $prefix = 'KK-' . date('Ymd', strtotime($tanggal)) . '-';
            
            // Cari sequence terakhir untuk tanggal ini
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

    /**
     * Set default values
     */
    protected function setDefaultValues(array $data)
    {
        if (!isset($data['data']['metode_imprest'])) {
            $data['data']['metode_imprest'] = 1; // Default metode imprest (dana tetap)
        }
        
        if (!isset($data['data']['status'])) {
            $data['data']['status'] = 'Draft';
        }
        
        return $data;
    }

    /**
     * Validasi COA lawan
     */
    protected function validateCoaLawan(array $data)
    {
        if (!empty($data['data']['coa_lawan_id'])) {
            $coaModel = new CoaModel();
            $coa = $coaModel->find($data['data']['coa_lawan_id']);
            
            if (!$coa || $coa['is_header'] == 1) {
                throw new \RuntimeException('Akun lawan tidak valid atau merupakan akun header');
            }
            
            // Validasi berdasarkan tipe transaksi
            $tipe = $data['data']['tipe'] ?? null;
            
            if ($tipe === 'Pemasukan') {
                // Untuk pemasukan (pengisian kembali), akun lawan harus Kas/Bank (1-11xx)
                if (!str_starts_with($coa['kode_akun'] ?? '', '1-11')) {
                    throw new \RuntimeException('Untuk transaksi pemasukan (pengisian kembali), akun lawan harus merupakan akun Kas/Bank (kode 1-11xx)');
                }
            } elseif ($tipe === 'Pengeluaran') {
                // Untuk pengeluaran, akun lawan bisa Beban (5-xxxx) atau Aset (1-xxxx selain kas)
                if (!str_starts_with($coa['kode_akun'] ?? '', '5-') && !str_starts_with($coa['kode_akun'] ?? '', '1-')) {
                    throw new \RuntimeException('Untuk transaksi pengeluaran, akun lawan harus merupakan akun Beban (5-xxxx) atau Aset (1-xxxx)');
                }
                
                // Tidak boleh akun Kas/Bank untuk pengeluaran
                if (str_starts_with($coa['kode_akun'] ?? '', '1-11')) {
                    throw new \RuntimeException('Untuk transaksi pengeluaran, akun lawan tidak boleh merupakan akun Kas/Bank');
                }
            }
        }
        
        return $data;
    }

    /**
     * Validasi saldo cukup untuk pengeluaran
     */
    protected function validateSaldo(array $data)
    {
        $tipe = $data['data']['tipe'] ?? null;
        $jumlah = $data['data']['jumlah'] ?? 0;
        
        if ($tipe === 'Pengeluaran') {
            $saldoKasKecil = $this->getSaldoKasKecil();
            
            if ($saldoKasKecil < $jumlah) {
                throw new \RuntimeException('Saldo kas kecil tidak mencukupi. Saldo tersedia: ' . number_format($saldoKasKecil, 0, ',', '.'));
            }
        }
        
        return $data;
    }

    /**
     * Validasi saldo pada update
     */
    protected function validateSaldoOnUpdate(array $data)
    {
        $id = $data['id'][0] ?? null;
        $tipe = $data['data']['tipe'] ?? null;
        $jumlah = $data['data']['jumlah'] ?? 0;
        
        if ($id && $tipe === 'Pengeluaran') {
            $current = $this->find($id);
            
            // Jika mengubah jumlah pengeluaran, validasi saldo
            if ($current && $current['jumlah'] != $jumlah) {
                $selisih = $jumlah - $current['jumlah'];
                
                if ($selisih > 0) {
                    // Jika jumlah bertambah, cek saldo
                    $saldoKasKecil = $this->getSaldoKasKecilSebelumTransaksi($id);
                    
                    if ($saldoKasKecil < $selisih) {
                        throw new \RuntimeException('Saldo kas kecil tidak mencukupi untuk perubahan ini. Saldo tersedia: ' . number_format($saldoKasKecil, 0, ',', '.'));
                    }
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
        // Set data COA lawan
        if (!empty($data['data']['coa_lawan_id'])) {
            $coaModel = new CoaModel();
            $coa = $coaModel->find($data['data']['coa_lawan_id']);
            
            if ($coa) {
                $data['data']['coa_lawan_kode'] = $coa['kode_akun'];
                $data['data']['coa_lawan_nama'] = $coa['nama_akun'];
            }
        }
        
        // Set data karyawan
        if (!empty($data['data']['karyawan_id'])) {
            $karyawanModel = new \App\Models\KaryawanModel();
            $karyawan = $karyawanModel->find($data['data']['karyawan_id']);
            
            if ($karyawan) {
                $data['data']['nama_karyawan'] = $karyawan['nama_lengkap'];
            }
        }
        
        // Set data SPK
        if (!empty($data['data']['spk_id'])) {
            $spkModel = new \App\Models\Teknisi\SpkInstalasiModel();
            $spk = $spkModel->find($data['data']['spk_id']);
            
            if ($spk) {
                $data['data']['nomor_spk'] = $spk['nomor_spk'];
            }
        }
        
        return $data;
    }

    /**
     * Hitung saldo setelah transaksi
     */
    protected function hitungSaldoSetelah(array $data)
    {
        $id = $data['id'][0] ?? null;
        $tipe = $data['data']['tipe'] ?? null;
        $jumlah = $data['data']['jumlah'] ?? 0;
        
        if ($id) {
            // Untuk update, hitung berdasarkan data sebelumnya
            $current = $this->find($id);
            
            if ($current) {
                $saldoSebelum = $this->getSaldoKasKecilSebelumTransaksi($id);
                
                if ($tipe === 'Pemasukan') {
                    $data['data']['saldo_setelah'] = $saldoSebelum + $jumlah;
                } else {
                    $data['data']['saldo_setelah'] = $saldoSebelum - $jumlah;
                }
            }
        } else {
            // Untuk insert baru, hitung berdasarkan saldo terkini
            $saldoTerkini = $this->getSaldoKasKecil();
            
            if ($tipe === 'Pemasukan') {
                $data['data']['saldo_setelah'] = $saldoTerkini + $jumlah;
            } else {
                $data['data']['saldo_setelah'] = $saldoTerkini - $jumlah;
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
                        throw new \RuntimeException('Transaksi harus memiliki jurnal_id sebelum diposting');
                    }
                    
                    if (empty($current['posted_at']) && empty($data['data']['posted_at'])) {
                        $data['data']['posted_at'] = date('Y-m-d H:i:s');
                    }
                }
                
                // Jika status berubah menjadi Dibatalkan, pastikan belum diposting
                if ($data['data']['status'] === 'Dibatalkan' && $current['status'] === 'Posted') {
                    throw new \RuntimeException('Transaksi yang sudah diposting tidak dapat dibatalkan');
                }
                
                // Jika status berubah menjadi Draft dari Posted, tidak boleh
                if ($data['data']['status'] === 'Draft' && $current['status'] === 'Posted') {
                    throw new \RuntimeException('Tidak dapat mengubah status dari Posted menjadi Draft');
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
            $transaksi = $this->find($id);
            
            if ($transaksi && $transaksi['status'] === 'Posted') {
                throw new \RuntimeException('Transaksi kas kecil yang sudah diposting tidak dapat dihapus');
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
     * Get all transaksi kas kecil with filters and pagination
     */
    public function getAllWithFilters($filters = [], $perPage = 20, $page = 1)
    {
        $builder = $this->select('kas_kecil.*, 
            creator.username as creator_name,
            updater.username as updater_name,
            coa.kode_akun as kode_akun_lawan,
            coa.nama_akun as nama_akun_lawan,
            coa.tipe_akun as tipe_akun_lawan,
            jurnal.nomor_jurnal,
            jurnal.status as jurnal_status')
            ->join('users as creator', 'creator.id = kas_kecil.created_by', 'left')
            ->join('users as updater', 'updater.id = kas_kecil.updated_by', 'left')
            ->join('coa', 'coa.id = kas_kecil.coa_lawan_id', 'left')
            ->join('jurnal', 'jurnal.id = kas_kecil.jurnal_id', 'left');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('kas_kecil.kode_transaksi', $search)
                ->orLike('kas_kecil.keterangan', $search)
                ->orLike('kas_kecil.no_bukti', $search)
                ->orLike('kas_kecil.nama_karyawan', $search)
                ->orLike('kas_kecil.nomor_spk', $search)
                ->orLike('coa.nama_akun', $search)
                ->groupEnd();
        }
        
        if (!empty($filters['tanggal_mulai'])) {
            $builder->where('kas_kecil.tanggal >=', $filters['tanggal_mulai']);
        }
        
        if (!empty($filters['tanggal_selesai'])) {
            $builder->where('kas_kecil.tanggal <=', $filters['tanggal_selesai']);
        }
        
        if (!empty($filters['tipe'])) {
            $builder->where('kas_kecil.tipe', $filters['tipe']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('kas_kecil.status', $filters['status']);
        }
        
        if (!empty($filters['karyawan_id'])) {
            $builder->where('kas_kecil.karyawan_id', $filters['karyawan_id']);
        }
        
        if (!empty($filters['spk_id'])) {
            $builder->where('kas_kecil.spk_id', $filters['spk_id']);
        }
        
        if (!empty($filters['metode_imprest'])) {
            $builder->where('kas_kecil.metode_imprest', $filters['metode_imprest']);
        }
        
        $builder->orderBy('kas_kecil.tanggal', 'DESC')
                ->orderBy('kas_kecil.kode_transaksi', 'DESC');
        
        // Get total for pagination
        $total = $builder->countAllResults(false);
        
        // Get paginated results
        $offset = ($page - 1) * $perPage;
        $transaksi = $builder->limit($perPage, $offset)->findAll();
        
        return [
            'data' => $transaksi,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Get transaksi kas kecil by ID with details
     */
    public function getWithDetails($id)
    {
        $transaksi = $this->select('kas_kecil.*, 
            creator.username as creator_name,
            creator.name as creator_fullname,
            updater.username as updater_name,
            coa.kode_akun as kode_akun_lawan,
            coa.nama_akun as nama_akun_lawan,
            coa.tipe_akun as tipe_akun_lawan,
            coa.saldo_normal as saldo_normal_lawan,
            jurnal.nomor_jurnal,
            jurnal.status as jurnal_status,
            jurnal.created_at as jurnal_created_at,
            creator_jurnal.username as posted_by_name')
            ->join('users as creator', 'creator.id = kas_kecil.created_by', 'left')
            ->join('users as updater', 'updater.id = kas_kecil.updated_by', 'left')
            ->join('coa', 'coa.id = kas_kecil.coa_lawan_id', 'left')
            ->join('jurnal', 'jurnal.id = kas_kecil.jurnal_id', 'left')
            ->join('users as creator_jurnal', 'creator_jurnal.id = jurnal.created_by', 'left')
            ->where('kas_kecil.id', $id)
            ->first();
        
        if (!$transaksi) {
            return null;
        }
        
        // Tambahkan informasi tambahan jika ada karyawan
        if (!empty($transaksi['karyawan_id'])) {
            $karyawanModel = new \App\Models\KaryawanModel();
            $karyawan = $karyawanModel->select('id, nik, nama_lengkap, jabatan, departemen')
                ->find($transaksi['karyawan_id']);
            $transaksi['karyawan'] = $karyawan;
        }
        
        // Tambahkan informasi SPK jika ada
        if (!empty($transaksi['spk_id'])) {
            $spkModel = new \App\Models\Teknisi\SpkInstalasiModel();
            $spk = $spkModel->select('id, nomor_spk, judul_pekerjaan, lokasi')
                ->find($transaksi['spk_id']);
            $transaksi['spk'] = $spk;
        }
        
        return $transaksi;
    }

    /**
     * Get saldo kas kecil terkini
     */
    public function getSaldoKasKecil()
    {
        $builder = $this->select("
                SUM(CASE WHEN tipe = 'Pemasukan' THEN jumlah ELSE 0 END) as total_pemasukan,
                SUM(CASE WHEN tipe = 'Pengeluaran' THEN jumlah ELSE 0 END) as total_pengeluaran
            ")
            ->where('status', 'Posted');
        
        $result = $builder->first();
        
        if (!$result) {
            return 0;
        }
        
        return ($result['total_pemasukan'] ?? 0) - ($result['total_pengeluaran'] ?? 0);
    }

    /**
     * Get saldo kas kecil sebelum transaksi tertentu (untuk validasi update)
     */
    public function getSaldoKasKecilSebelumTransaksi($transaksiId, $tanggal = null)
    {
        $transaksi = $this->find($transaksiId);
        
        if (!$transaksi) {
            return $this->getSaldoKasKecil();
        }
        
        $builder = $this->select("
                SUM(CASE WHEN tipe = 'Pemasukan' THEN jumlah ELSE 0 END) as total_pemasukan,
                SUM(CASE WHEN tipe = 'Pengeluaran' THEN jumlah ELSE 0 END) as total_pengeluaran
            ")
            ->where('status', 'Posted');
        
        if ($tanggal) {
            $builder->where('tanggal <', $tanggal)
                ->orWhere('tanggal =', $tanggal)
                ->where('id <', $transaksiId);
        } else {
            $builder->where('id <', $transaksiId);
        }
        
        $result = $builder->first();
        
        if (!$result) {
            return 0;
        }
        
        return ($result['total_pemasukan'] ?? 0) - ($result['total_pengeluaran'] ?? 0);
    }

   /**
 * Get daftar COA lawan untuk dropdown
 */
public function getCoaLawanOptions($tipe = null)
{
    $coaModel = new \App\Models\CoaModel();
    
    $builder = $coaModel->where('is_header', 0)
        ->where('is_active', 1);
    
    if ($tipe === 'Pemasukan') {
        // Untuk pemasukan (pengisian kembali), akun lawan = Kas/Bank
        $builder->like('kode_akun', '1-11', 'after');
    } elseif ($tipe === 'Pengeluaran') {
        // Untuk pengeluaran, akun lawan = Beban (5-xxxx) atau Aset non-kas (1-xxxx selain 1-11)
        $builder->groupStart()
            ->like('kode_akun', '5-', 'after')
            ->orWhere('kode_akun LIKE', '1-%')
            ->where('kode_akun NOT LIKE', '1-11%')
            ->groupEnd();
    }
    
    return $builder->orderBy('kode_akun', 'ASC')->findAll();
}

    /**
     * Get daftar karyawan untuk dropdown
     */
    public function getKaryawanOptions()
    {
        $karyawanModel = new \App\Models\KaryawanModel();
        
        return $karyawanModel->select('id, nik, nama_lengkap, jabatan')
            ->where('status_karyawan', 'Tetap')
            ->orWhere('status_karyawan', 'Kontrak')
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();
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
     * Post transaksi kas kecil (ubah status Draft → Posted) dan buat jurnal
     */
    public function postTransaksi($id, $jurnalId)
    {
        $transaksi = $this->find($id);
        
        if (!$transaksi) {
            throw new \RuntimeException('Transaksi kas kecil tidak ditemukan');
        }
        
        if ($transaksi['status'] !== 'Draft') {
            throw new \RuntimeException('Hanya transaksi dengan status Draft yang bisa diposting');
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
     * Batalkan transaksi kas kecil
     */
    public function batalkanTransaksi($id)
    {
        $transaksi = $this->find($id);
        
        if (!$transaksi) {
            throw new \RuntimeException('Transaksi kas kecil tidak ditemukan');
        }
        
        if ($transaksi['status'] === 'Posted') {
            throw new \RuntimeException('Transaksi yang sudah diposting tidak dapat dibatalkan langsung. Batalkan jurnal terlebih dahulu.');
        }
        
        $data = [
            'id' => $id,
            'status' => 'Dibatalkan'
        ];
        
        return $this->save($data);
    }

    /**
     * Proses pengisian kembali kas kecil (replenishment)
     * Membuat transaksi pemasukan berdasarkan total pengeluaran periode tertentu
     */
    public function prosesPengisianKembali($tanggal, $keterangan, $coaBankId, $jumlah = null)
    {
        // Jika jumlah tidak ditentukan, hitung dari total pengeluaran periode berjalan
        if (!$jumlah) {
            $tanggalMulai = date('Y-m-01', strtotime($tanggal));
            $tanggalAkhir = date('Y-m-d', strtotime($tanggal));
            
            $totalPengeluaran = $this->select('SUM(jumlah) as total')
                ->where('tipe', 'Pengeluaran')
                ->where('status', 'Posted')
                ->where('tanggal >=', $tanggalMulai)
                ->where('tanggal <=', $tanggalAkhir)
                ->first();
            
            $jumlah = $totalPengeluaran['total'] ?? 0;
        }
        
        if ($jumlah <= 0) {
            throw new \RuntimeException('Tidak ada pengeluaran untuk periode ini atau jumlah tidak valid');
        }
        
        // Buat transaksi pemasukan
        $data = [
            'tanggal' => $tanggal,
            'tipe' => 'Pemasukan',
            'jumlah' => $jumlah,
            'keterangan' => $keterangan ?: 'Pengisian kembali kas kecil',
            'coa_lawan_id' => $coaBankId,
            'status' => 'Draft',
            'metode_imprest' => 1
        ];
        
        $id = $this->insert($data);
        
        if (!$id) {
            throw new \RuntimeException('Gagal membuat transaksi pengisian kembali');
        }
        
        return $this->find($id);
    }

    /**
     * Get buku kas kecil (mutasi)
     */
    public function getBukuKasKecil($tanggalMulai = null, $tanggalSelesai = null)
    {
        $builder = $this->select('kas_kecil.*, 
                coa.nama_akun as nama_akun_lawan')
            ->join('coa', 'coa.id = kas_kecil.coa_lawan_id', 'left')
            ->where('kas_kecil.status', 'Posted')
            ->orderBy('kas_kecil.tanggal', 'ASC')
            ->orderBy('kas_kecil.id', 'ASC');
        
        if ($tanggalMulai) {
            $builder->where('kas_kecil.tanggal >=', $tanggalMulai);
        }
        
        if ($tanggalSelesai) {
            $builder->where('kas_kecil.tanggal <=', $tanggalSelesai);
        }
        
        $transaksi = $builder->findAll();
        
        // Hitung saldo berjalan
        $saldo = 0;
        foreach ($transaksi as &$item) {
            if ($item['tipe'] === 'Pemasukan') {
                $saldo += $item['jumlah'];
            } else {
                $saldo -= $item['jumlah'];
            }
            $item['saldo_berjalan'] = $saldo;
        }
        
        return $transaksi;
    }

    /**
     * Get rekap pengeluaran per kategori (COA lawan)
     */
    public function getRekapPengeluaranPerKategori($tanggalMulai = null, $tanggalSelesai = null)
    {
        $builder = $this->select("
                kas_kecil.coa_lawan_id,
                coa.kode_akun,
                coa.nama_akun,
                coa.tipe_akun,
                COUNT(*) as jumlah_transaksi,
                SUM(kas_kecil.jumlah) as total_pengeluaran
            ")
            ->join('coa', 'coa.id = kas_kecil.coa_lawan_id', 'left')
            ->where('kas_kecil.tipe', 'Pengeluaran')
            ->where('kas_kecil.status', 'Posted')
            ->groupBy('kas_kecil.coa_lawan_id, coa.kode_akun, coa.nama_akun, coa.tipe_akun');
        
        if ($tanggalMulai) {
            $builder->where('kas_kecil.tanggal >=', $tanggalMulai);
        }
        
        if ($tanggalSelesai) {
            $builder->where('kas_kecil.tanggal <=', $tanggalSelesai);
        }
        
        return $builder->orderBy('total_pengeluaran', 'DESC')->findAll();
    }

    /**
     * Get rekap pengeluaran per karyawan
     */
    public function getRekapPengeluaranPerKaryawan($tanggalMulai = null, $tanggalSelesai = null)
    {
        $builder = $this->select("
                kas_kecil.karyawan_id,
                karyawan.nik,
                karyawan.nama_lengkap,
                karyawan.jabatan,
                COUNT(*) as jumlah_transaksi,
                SUM(kas_kecil.jumlah) as total_pengeluaran
            ")
            ->join('karyawan', 'karyawan.id = kas_kecil.karyawan_id', 'left')
            ->where('kas_kecil.tipe', 'Pengeluaran')
            ->where('kas_kecil.status', 'Posted')
            ->where('kas_kecil.karyawan_id IS NOT NULL')
            ->groupBy('kas_kecil.karyawan_id, karyawan.nik, karyawan.nama_lengkap, karyawan.jabatan');
        
        if ($tanggalMulai) {
            $builder->where('kas_kecil.tanggal >=', $tanggalMulai);
        }
        
        if ($tanggalSelesai) {
            $builder->where('kas_kecil.tanggal <=', $tanggalSelesai);
        }
        
        return $builder->orderBy('total_pengeluaran', 'DESC')->findAll();
    }

    /**
     * Get rekap pengeluaran per SPK/Proyek
     */
    public function getRekapPengeluaranPerSpk($tanggalMulai = null, $tanggalSelesai = null)
    {
        $builder = $this->select("
                kas_kecil.spk_id,
                spk.nomor_spk,
                spk.judul_pekerjaan,
                COUNT(*) as jumlah_transaksi,
                SUM(kas_kecil.jumlah) as total_pengeluaran
            ")
            ->join('spk_instalasi as spk', 'spk.id = kas_kecil.spk_id', 'left')
            ->where('kas_kecil.tipe', 'Pengeluaran')
            ->where('kas_kecil.status', 'Posted')
            ->where('kas_kecil.spk_id IS NOT NULL')
            ->groupBy('kas_kecil.spk_id, spk.nomor_spk, spk.judul_pekerjaan');
        
        if ($tanggalMulai) {
            $builder->where('kas_kecil.tanggal >=', $tanggalMulai);
        }
        
        if ($tanggalSelesai) {
            $builder->where('kas_kecil.tanggal <=', $tanggalSelesai);
        }
        
        return $builder->orderBy('total_pengeluaran', 'DESC')->findAll();
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
                SUM(CASE WHEN tipe = 'Pemasukan' THEN jumlah ELSE 0 END) as total_pemasukan,
                SUM(CASE WHEN tipe = 'Pengeluaran' THEN jumlah ELSE 0 END) as total_pengeluaran,
                COUNT(CASE WHEN tipe = 'Pemasukan' THEN 1 END) as jumlah_pemasukan,
                COUNT(CASE WHEN tipe = 'Pengeluaran' THEN 1 END) as jumlah_pengeluaran,
                COUNT(CASE WHEN DATE(tanggal) = CURDATE() THEN 1 END) as transaksi_hari_ini
            ")
            ->first();
        
        if (!$stats) {
            return [
                'total_transaksi' => 0,
                'total_pemasukan' => 0,
                'total_pengeluaran' => 0,
                'jumlah_pemasukan' => 0,
                'jumlah_pengeluaran' => 0,
                'transaksi_hari_ini' => 0
            ];
        }
        
        // Tambah saldo terkini
        $stats['saldo_terkini'] = $this->getSaldoKasKecil();
        
        return $stats;
    }

    /**
     * Export data untuk Excel
     */
    public function getExportData($filters = [])
    {
        $builder = $this->select('kas_kecil.*, 
            creator.username as creator_name,
            coa.kode_akun as kode_akun_lawan,
            coa.nama_akun as nama_akun_lawan,
            jurnal.nomor_jurnal')
            ->join('users as creator', 'creator.id = kas_kecil.created_by', 'left')
            ->join('coa', 'coa.id = kas_kecil.coa_lawan_id', 'left')
            ->join('jurnal', 'jurnal.id = kas_kecil.jurnal_id', 'left');
        
        // Apply filters
        if (!empty($filters['tanggal_mulai'])) {
            $builder->where('kas_kecil.tanggal >=', $filters['tanggal_mulai']);
        }
        
        if (!empty($filters['tanggal_selesai'])) {
            $builder->where('kas_kecil.tanggal <=', $filters['tanggal_selesai']);
        }
        
        if (!empty($filters['tipe'])) {
            $builder->where('kas_kecil.tipe', $filters['tipe']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('kas_kecil.status', $filters['status']);
        }
        
        $builder->orderBy('kas_kecil.tanggal', 'DESC')
                ->orderBy('kas_kecil.kode_transaksi', 'DESC');
        
        $transaksi = $builder->findAll();
        
        // Hitung saldo berjalan untuk export
        $saldo = 0;
        $transaksi = array_reverse($transaksi); // Urutkan dari terlama ke terbaru untuk hitung saldo
        foreach ($transaksi as &$item) {
            if ($item['tipe'] === 'Pemasukan') {
                $saldo += $item['jumlah'];
            } else {
                $saldo -= $item['jumlah'];
            }
            $item['saldo_berjalan'] = $saldo;
        }
        $transaksi = array_reverse($transaksi); // Kembalikan ke urutan semula
        
        // Format untuk export
        $exportData = [];
        foreach ($transaksi as $item) {
            $exportData[] = [
                'Kode Transaksi' => $item['kode_transaksi'],
                'Tanggal' => $item['tanggal'],
                'Tipe' => $item['tipe'],
                'Jumlah' => $item['jumlah'],
                'Saldo Setelah' => $item['saldo_setelah'] ?? $item['saldo_berjalan'],
                'Keterangan' => $item['keterangan'],
                'Akun Lawan' => ($item['kode_akun_lawan'] ?? '') . ' - ' . ($item['nama_akun_lawan'] ?? ''),
                'Karyawan' => $item['nama_karyawan'] ?? '-',
                'SPK/Proyek' => ($item['nomor_spk'] ?? '') . ' - ' . ($item['judul_pekerjaan'] ?? ''),
                'No Bukti' => $item['no_bukti'] ?? '-',
                'No Jurnal' => $item['nomor_jurnal'] ?? '-',
                'Status' => $item['status'],
                'Metode' => $item['metode_imprest'] ? 'Imprest (Dana Tetap)' : 'Fluctuasi',
                'Dibuat Oleh' => $item['creator_name'] ?? '-',
                'Dibuat Tanggal' => $item['created_at']
            ];
        }
        
        return $exportData;
    }

    /**
     * Check if transaksi has been posted
     */
    public function isPosted($id)
    {
        $transaksi = $this->find($id);
        return $transaksi && $transaksi['status'] === 'Posted';
    }

    /**
     * Get total pengeluaran per periode (untuk laporan)
     */
    public function getTotalPengeluaranPerPeriode($tahun)
    {
        $result = [];
        
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $tanggalMulai = sprintf('%d-%02d-01', $tahun, $bulan);
            $tanggalAkhir = date('Y-m-t', strtotime($tanggalMulai));
            
            $total = $this->select('SUM(jumlah) as total')
                ->where('tipe', 'Pengeluaran')
                ->where('status', 'Posted')
                ->where('tanggal >=', $tanggalMulai)
                ->where('tanggal <=', $tanggalAkhir)
                ->first();
            
            $result[$bulan] = [
                'bulan' => $bulan,
                'nama_bulan' => date('F', mktime(0, 0, 0, $bulan, 1)),
                'total' => $total['total'] ?? 0
            ];
        }
        
        return $result;
    }

    /**
     * Validasi untuk pengisian kembali (replenishment)
     */
    public function validateReplenishment($jumlah, $coaBankId)
    {
        $errors = [];
        
        // Validasi jumlah
        if ($jumlah <= 0) {
            $errors[] = 'Jumlah pengisian kembali harus lebih dari 0';
        }
        
        // Validasi akun bank
        if ($coaBankId) {
            $coaModel = new CoaModel();
            $coa = $coaModel->find($coaBankId);
            
            if (!$coa || !str_starts_with($coa['kode_akun'] ?? '', '1-11')) {
                $errors[] = 'Akun bank yang dipilih tidak valid';
            }
        } else {
            $errors[] = 'Akun bank sumber harus dipilih';
        }
        
        // Validasi saldo bank (jika perlu)
        if ($coaBankId && $jumlah > 0) {
            $mutasiModel = new MutasiBankModel();
            $saldoBank = $mutasiModel->getSaldoBank($coaBankId);
            
            if ($saldoBank < $jumlah) {
                $errors[] = 'Saldo bank tidak mencukupi. Saldo tersedia: ' . number_format($saldoBank, 0, ',', '.');
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Rekalkulasi saldo (untuk maintenance)
     */
    public function recalculateSaldo()
    {
        $db = \Config\Database::connect();
        $db->transStart();
        
        // Ambil semua transaksi yang sudah diposting, urutkan berdasarkan tanggal
        $transaksi = $this->where('status', 'Posted')
            ->orderBy('tanggal', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
        
        $saldo = 0;
        foreach ($transaksi as $item) {
            if ($item['tipe'] === 'Pemasukan') {
                $saldo += $item['jumlah'];
            } else {
                $saldo -= $item['jumlah'];
            }
            
            // Update saldo_setelah
            $this->update($item['id'], ['saldo_setelah' => $saldo]);
        }
        
        $db->transComplete();
        
        return $db->transStatus();
    }
}