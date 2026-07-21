<?php
namespace App\Models\Accounting;

use CodeIgniter\Model;
use App\Models\CoaModel;
use App\Models\BukuBesarModel;
use App\Models\Accounting\MutasiBankModel;

class RekonsiliasiModel extends Model
{
    protected $table = 'rekonsiliasi';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true; // Menggunakan soft deletes
    protected $protectFields = true;
    protected $allowedFields = [
        'periode',
        'coa_bank_id',
        'nomor_rekening_bank',
        'nama_bank',
        'saldo_awal_bank',
        'saldo_akhir_bank',
        'saldo_awal_buku',
        'saldo_akhir_buku',
        'tanggal_rekonsiliasi',
        'data_setoran_dalam_perjalanan',
        'data_cek_dalam_edar',
        'data_penyesuaian_bank',
        'data_penyesuaian_buku',
        'total_setoran_dalam_perjalanan',
        'total_cek_dalam_edar',
        'total_penyesuaian_bank',
        'total_penyesuaian_buku',
        'keterangan',
        'lampiran_rekening_koran',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'periode' => 'required|valid_date[Y-m-d]',
        'coa_bank_id' => 'required|is_natural_no_zero',
        'tanggal_rekonsiliasi' => 'required|valid_date',
        'saldo_akhir_bank' => 'permit_empty|numeric|greater_than_equal_to[0]',
        'saldo_akhir_buku' => 'permit_empty|numeric|greater_than_equal_to[0]',
        'status' => 'permit_empty|in_list[Draft,Selesai,Dibatalkan]'
    ];

    protected $validationMessages = [
        'periode' => [
            'required' => 'Periode rekonsiliasi harus diisi',
            'valid_date' => 'Format periode tidak valid (gunakan YYYY-MM-DD)'
        ],
        'coa_bank_id' => [
            'required' => 'Akun bank harus dipilih'
        ],
        'tanggal_rekonsiliasi' => [
            'required' => 'Tanggal rekonsiliasi harus diisi'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = ['setDefaultValues', 'setCreatedBy', 'validateUniquePeriode', 'validateCoaBank'];
    protected $beforeUpdate = ['setUpdatedBy', 'validateUniquePeriodeOnUpdate', 'validateCoaBank', 'validateStatusChange'];
    protected $beforeDelete = ['checkCanDelete'];

    /**
     * Set default values for new rekonsiliasi
     */
    protected function setDefaultValues(array $data)
    {
        if (!isset($data['data']['saldo_awal_bank'])) {
            $data['data']['saldo_awal_bank'] = 0;
        }
        
        if (!isset($data['data']['saldo_akhir_bank'])) {
            $data['data']['saldo_akhir_bank'] = 0;
        }
        
        if (!isset($data['data']['saldo_awal_buku'])) {
            $data['data']['saldo_awal_buku'] = 0;
        }
        
        if (!isset($data['data']['saldo_akhir_buku'])) {
            $data['data']['saldo_akhir_buku'] = 0;
        }
        
        if (!isset($data['data']['total_setoran_dalam_perjalanan'])) {
            $data['data']['total_setoran_dalam_perjalanan'] = 0;
        }
        
        if (!isset($data['data']['total_cek_dalam_edar'])) {
            $data['data']['total_cek_dalam_edar'] = 0;
        }
        
        if (!isset($data['data']['total_penyesuaian_bank'])) {
            $data['data']['total_penyesuaian_bank'] = 0;
        }
        
        if (!isset($data['data']['total_penyesuaian_buku'])) {
            $data['data']['total_penyesuaian_buku'] = 0;
        }
        
        if (!isset($data['data']['status'])) {
            $data['data']['status'] = 'Draft';
        }
        
        return $data;
    }

    /**
     * Validasi unique periode per bank (hanya satu rekonsiliasi per periode per bank)
     */
    protected function validateUniquePeriode(array $data)
    {
        $periode = $data['data']['periode'] ?? null;
        $coaBankId = $data['data']['coa_bank_id'] ?? null;
        
        if ($periode && $coaBankId) {
            $existing = $this->where('periode', $periode)
                ->where('coa_bank_id', $coaBankId)
                ->where('deleted_at IS NULL')
                ->first();
            
            if ($existing) {
                throw new \RuntimeException('Rekonsiliasi untuk periode dan bank ini sudah ada');
            }
        }
        
        return $data;
    }

    /**
     * Validasi unique periode per bank saat update
     */
    protected function validateUniquePeriodeOnUpdate(array $data)
    {
        $id = $data['id'][0] ?? null;
        $periode = $data['data']['periode'] ?? null;
        $coaBankId = $data['data']['coa_bank_id'] ?? null;
        
        if ($id && $periode && $coaBankId) {
            $existing = $this->where('periode', $periode)
                ->where('coa_bank_id', $coaBankId)
                ->where('id !=', $id)
                ->where('deleted_at IS NULL')
                ->first();
            
            if ($existing) {
                throw new \RuntimeException('Rekonsiliasi untuk periode dan bank ini sudah ada');
            }
        }
        
        return $data;
    }

    /**
     * Validasi COA yang dipilih harus akun bank (kode 1-11xx)
     */
    protected function validateCoaBank(array $data)
    {
        if (!empty($data['data']['coa_bank_id'])) {
            $coaModel = new CoaModel();
            $coa = $coaModel->find($data['data']['coa_bank_id']);
            
            if (!$coa || !str_starts_with($coa['kode_akun'] ?? '', '1-11')) {
                throw new \RuntimeException('Akun yang dipilih harus merupakan akun Kas/Bank (kode 1-11xx)');
            }
            
            // Set denormalized data
            $data['data']['nomor_rekening_bank'] = $coa['kode_akun'] ?? null;
            $data['data']['nama_bank'] = $coa['nama_akun'] ?? null;
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
                
                // Jika status berubah menjadi Selesai, pastikan data lengkap
                if ($data['data']['status'] === 'Selesai' && $current['status'] === 'Draft') {
                    if (empty($current['saldo_akhir_bank']) && empty($data['data']['saldo_akhir_bank'])) {
                        throw new \RuntimeException('Saldo akhir bank harus diisi sebelum menyelesaikan rekonsiliasi');
                    }
                    
                    if (empty($current['saldo_akhir_buku']) && empty($data['data']['saldo_akhir_buku'])) {
                        throw new \RuntimeException('Saldo akhir buku harus diisi sebelum menyelesaikan rekonsiliasi');
                    }
                    
                    // Validasi selisih (opsional)
                    $saldoAkhirBank = $data['data']['saldo_akhir_bank'] ?? $current['saldo_akhir_bank'];
                    $saldoAkhirBuku = $data['data']['saldo_akhir_buku'] ?? $current['saldo_akhir_buku'];
                    
                    if (abs($saldoAkhirBank - $saldoAkhirBuku) > 0.01) {
                        throw new \RuntimeException('Selisih antara saldo bank dan buku harus 0 untuk menyelesaikan rekonsiliasi');
                    }
                }
                
                // Jika status berubah menjadi Dibatalkan, pastikan belum selesai
                if ($data['data']['status'] === 'Dibatalkan' && $current['status'] === 'Selesai') {
                    throw new \RuntimeException('Rekonsiliasi yang sudah selesai tidak dapat dibatalkan');
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
            $rekonsiliasi = $this->find($id);
            
            if ($rekonsiliasi && $rekonsiliasi['status'] === 'Selesai') {
                throw new \RuntimeException('Rekonsiliasi yang sudah selesai tidak dapat dihapus');
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
     * Get all rekonsiliasi with filters and pagination
     */
    public function getAllWithFilters($filters = [], $perPage = 20, $page = 1)
    {
        $builder = $this->select('rekonsiliasi.*, 
            creator.username as creator_name,
            updater.username as updater_name,
            coa.kode_akun as kode_akun_bank,
            coa.nama_akun as nama_akun_bank,
            coa.tipe_akun as tipe_akun_bank')
            ->join('users as creator', 'creator.id = rekonsiliasi.created_by', 'left')
            ->join('users as updater', 'updater.id = rekonsiliasi.updated_by', 'left')
            ->join('coa', 'coa.id = rekonsiliasi.coa_bank_id', 'left');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('coa.nama_akun', $search)
                ->orLike('coa.kode_akun', $search)
                ->orLike('rekonsiliasi.keterangan', $search)
                ->orLike('rekonsiliasi.nama_bank', $search)
                ->groupEnd();
        }
        
        if (!empty($filters['periode_mulai'])) {
            $builder->where('rekonsiliasi.periode >=', $filters['periode_mulai']);
        }
        
        if (!empty($filters['periode_selesai'])) {
            $builder->where('rekonsiliasi.periode <=', $filters['periode_selesai']);
        }
        
        if (!empty($filters['tahun'])) {
            $builder->where('YEAR(rekonsiliasi.periode)', $filters['tahun']);
        }
        
        if (!empty($filters['bulan'])) {
            $builder->where('MONTH(rekonsiliasi.periode)', $filters['bulan']);
        }
        
        if (!empty($filters['coa_bank_id'])) {
            $builder->where('rekonsiliasi.coa_bank_id', $filters['coa_bank_id']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('rekonsiliasi.status', $filters['status']);
        }
        
        $builder->orderBy('rekonsiliasi.periode', 'DESC')
                ->orderBy('rekonsiliasi.tanggal_rekonsiliasi', 'DESC');
        
        // Get total for pagination
        $total = $builder->countAllResults(false);
        
        // Get paginated results
        $offset = ($page - 1) * $perPage;
        $rekonsiliasi = $builder->limit($perPage, $offset)->findAll();
        
        // Decode JSON data
        foreach ($rekonsiliasi as &$item) {
            $item['data_setoran_dalam_perjalanan'] = !empty($item['data_setoran_dalam_perjalanan']) 
                ? json_decode($item['data_setoran_dalam_perjalanan'], true) 
                : [];
            $item['data_cek_dalam_edar'] = !empty($item['data_cek_dalam_edar']) 
                ? json_decode($item['data_cek_dalam_edar'], true) 
                : [];
            $item['data_penyesuaian_bank'] = !empty($item['data_penyesuaian_bank']) 
                ? json_decode($item['data_penyesuaian_bank'], true) 
                : [];
            $item['data_penyesuaian_buku'] = !empty($item['data_penyesuaian_buku']) 
                ? json_decode($item['data_penyesuaian_buku'], true) 
                : [];
            
            // Hitung selisih (generated column, tapi kita hitung manual untuk jaga-jaga)
            $item['selisih'] = ($item['saldo_akhir_bank'] ?? 0) - ($item['saldo_akhir_buku'] ?? 0);
        }
        
        return [
            'data' => $rekonsiliasi,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Get rekonsiliasi by ID with details
     */
    public function getWithDetails($id)
    {
        $rekonsiliasi = $this->select('rekonsiliasi.*, 
            creator.username as creator_name,
            creator.name as creator_fullname,
            updater.username as updater_name,
            coa.kode_akun as kode_akun_bank,
            coa.nama_akun as nama_akun_bank,
            coa.tipe_akun as tipe_akun_bank,
            coa.saldo_normal as saldo_normal_bank,
            coa.deskripsi as deskripsi_akun')
            ->join('users as creator', 'creator.id = rekonsiliasi.created_by', 'left')
            ->join('users as updater', 'updater.id = rekonsiliasi.updated_by', 'left')
            ->join('coa', 'coa.id = rekonsiliasi.coa_bank_id', 'left')
            ->where('rekonsiliasi.id', $id)
            ->first();
        
        if (!$rekonsiliasi) {
            return null;
        }
        
        // Decode JSON data
        $rekonsiliasi['data_setoran_dalam_perjalanan'] = !empty($rekonsiliasi['data_setoran_dalam_perjalanan']) 
            ? json_decode($rekonsiliasi['data_setoran_dalam_perjalanan'], true) 
            : [];
        $rekonsiliasi['data_cek_dalam_edar'] = !empty($rekonsiliasi['data_cek_dalam_edar']) 
            ? json_decode($rekonsiliasi['data_cek_dalam_edar'], true) 
            : [];
        $rekonsiliasi['data_penyesuaian_bank'] = !empty($rekonsiliasi['data_penyesuaian_bank']) 
            ? json_decode($rekonsiliasi['data_penyesuaian_bank'], true) 
            : [];
        $rekonsiliasi['data_penyesuaian_buku'] = !empty($rekonsiliasi['data_penyesuaian_buku']) 
            ? json_decode($rekonsiliasi['data_penyesuaian_buku'], true) 
            : [];
        
        // Hitung selisih
        $rekonsiliasi['selisih'] = ($rekonsiliasi['saldo_akhir_bank'] ?? 0) - ($rekonsiliasi['saldo_akhir_buku'] ?? 0);
        
        return $rekonsiliasi;
    }

    /**
     * Get daftar akun bank untuk dropdown
     */
    public function getBankAccounts()
    {
        $coaModel = new CoaModel();
        
        return $coaModel->where('is_header', 0)
            ->where('is_active', 1)
            ->like('kode_akun', '1-11', 'after')
            ->orderBy('kode_akun', 'ASC')
            ->findAll();
    }

    /**
     * Get mutasi bank yang belum direkonsiliasi untuk periode tertentu
     */
    public function getMutasiBelumRekonsiliasi($coaBankId, $periode, $includePenyesuaianBank = true)
    {
        $mutasiModel = new MutasiBankModel();
        
        // Tentukan tanggal mulai dan akhir periode
        $tanggalMulai = date('Y-m-01', strtotime($periode));
        $tanggalAkhir = date('Y-m-t', strtotime($periode));
        
        $builder = $mutasiModel->select('mutasi_bank.*, 
                coa_debit.nama_akun as nama_akun_debit,
                coa_kredit.nama_akun as nama_akun_kredit')
            ->join('coa as coa_debit', 'coa_debit.id = mutasi_bank.coa_id_debit', 'left')
            ->join('coa as coa_kredit', 'coa_kredit.id = mutasi_bank.coa_id_kredit', 'left')
            ->where('mutasi_bank.status', 'Posted')
            ->where('mutasi_bank.tanggal >=', $tanggalMulai)
            ->where('mutasi_bank.tanggal <=', $tanggalAkhir)
            ->groupStart()
                ->where('mutasi_bank.coa_id_debit', $coaBankId)
                ->orWhere('mutasi_bank.coa_id_kredit', $coaBankId)
            ->groupEnd();
        
        // Filter untuk mendapatkan transaksi yang belum direkonsiliasi
        // Ini perlu disesuaikan dengan implementasi penyimpanan status rekonsiliasi
        // Bisa menggunakan tabel pivot atau kolom is_reconciled
        
        return $builder->orderBy('mutasi_bank.tanggal', 'ASC')
            ->orderBy('mutasi_bank.id', 'ASC')
            ->findAll();
    }

    /**
     * Get saldo awal buku untuk periode tertentu
     */
    public function getSaldoAwalBuku($coaBankId, $periode)
    {
        $bukuBesarModel = new BukuBesarModel();
        
        // Tanggal sebelum periode dimulai
        $tanggalSebelumPeriode = date('Y-m-d', strtotime($periode . ' -1 day'));
        
        $saldo = $bukuBesarModel->select('saldo_akhir')
            ->where('coa_id', $coaBankId)
            ->where('tanggal <=', $tanggalSebelumPeriode)
            ->orderBy('tanggal', 'DESC')
            ->limit(1)
            ->first();
        
        return $saldo ? (float) $saldo['saldo_akhir'] : 0;
    }

    /**
     * Get saldo akhir buku untuk periode tertentu
     */
    public function getSaldoAkhirBuku($coaBankId, $periode)
    {
        $bukuBesarModel = new BukuBesarModel();
        
        $tanggalAkhirPeriode = date('Y-m-t', strtotime($periode));
        
        $saldo = $bukuBesarModel->select('saldo_akhir')
            ->where('coa_id', $coaBankId)
            ->where('tanggal <=', $tanggalAkhirPeriode)
            ->orderBy('tanggal', 'DESC')
            ->limit(1)
            ->first();
        
        return $saldo ? (float) $saldo['saldo_akhir'] : 0;
    }

    /**
     * Selesaikan rekonsiliasi
     */
    public function selesaikanRekonsiliasi($id)
    {
        $rekonsiliasi = $this->find($id);
        
        if (!$rekonsiliasi) {
            throw new \RuntimeException('Rekonsiliasi tidak ditemukan');
        }
        
        if ($rekonsiliasi['status'] !== 'Draft') {
            throw new \RuntimeException('Hanya rekonsiliasi dengan status Draft yang bisa diselesaikan');
        }
        
        // Validasi selisih
        $selisih = ($rekonsiliasi['saldo_akhir_bank'] ?? 0) - ($rekonsiliasi['saldo_akhir_buku'] ?? 0);
        
        if (abs($selisih) > 0.01) {
            throw new \RuntimeException('Selisih antara saldo bank dan buku harus 0 untuk menyelesaikan rekonsiliasi');
        }
        
        $data = [
            'id' => $id,
            'status' => 'Selesai'
        ];
        
        return $this->save($data);
    }

    /**
     * Batalkan rekonsiliasi
     */
    public function batalkanRekonsiliasi($id)
    {
        $rekonsiliasi = $this->find($id);
        
        if (!$rekonsiliasi) {
            throw new \RuntimeException('Rekonsiliasi tidak ditemukan');
        }
        
        if ($rekonsiliasi['status'] === 'Selesai') {
            throw new \RuntimeException('Rekonsiliasi yang sudah selesai tidak dapat dibatalkan');
        }
        
        $data = [
            'id' => $id,
            'status' => 'Dibatalkan'
        ];
        
        return $this->save($data);
    }

    /**
     * Tambah item setoran dalam perjalanan
     */
    public function addSetoranDalamPerjalanan($id, $item)
    {
        $rekonsiliasi = $this->find($id);
        
        if (!$rekonsiliasi) {
            throw new \RuntimeException('Rekonsiliasi tidak ditemukan');
        }
        
        if ($rekonsiliasi['status'] !== 'Draft') {
            throw new \RuntimeException('Hanya rekonsiliasi dengan status Draft yang bisa diubah');
        }
        
        $dataSetoran = !empty($rekonsiliasi['data_setoran_dalam_perjalanan']) 
            ? json_decode($rekonsiliasi['data_setoran_dalam_perjalanan'], true) 
            : [];
        
        // Tambah item baru
        $dataSetoran[] = $item;
        
        // Hitung total
        $total = array_sum(array_column($dataSetoran, 'jumlah'));
        
        $data = [
            'id' => $id,
            'data_setoran_dalam_perjalanan' => json_encode($dataSetoran),
            'total_setoran_dalam_perjalanan' => $total
        ];
        
        return $this->save($data);
    }

    /**
     * Tambah item cek dalam edar
     */
    public function addCekDalamEdar($id, $item)
    {
        $rekonsiliasi = $this->find($id);
        
        if (!$rekonsiliasi) {
            throw new \RuntimeException('Rekonsiliasi tidak ditemukan');
        }
        
        if ($rekonsiliasi['status'] !== 'Draft') {
            throw new \RuntimeException('Hanya rekonsiliasi dengan status Draft yang bisa diubah');
        }
        
        $dataCek = !empty($rekonsiliasi['data_cek_dalam_edar']) 
            ? json_decode($rekonsiliasi['data_cek_dalam_edar'], true) 
            : [];
        
        // Tambah item baru
        $dataCek[] = $item;
        
        // Hitung total
        $total = array_sum(array_column($dataCek, 'jumlah'));
        
        $data = [
            'id' => $id,
            'data_cek_dalam_edar' => json_encode($dataCek),
            'total_cek_dalam_edar' => $total
        ];
        
        return $this->save($data);
    }

    /**
     * Tambah penyesuaian bank
     */
    public function addPenyesuaianBank($id, $item)
    {
        $rekonsiliasi = $this->find($id);
        
        if (!$rekonsiliasi) {
            throw new \RuntimeException('Rekonsiliasi tidak ditemukan');
        }
        
        if ($rekonsiliasi['status'] !== 'Draft') {
            throw new \RuntimeException('Hanya rekonsiliasi dengan status Draft yang bisa diubah');
        }
        
        $dataPenyesuaian = !empty($rekonsiliasi['data_penyesuaian_bank']) 
            ? json_decode($rekonsiliasi['data_penyesuaian_bank'], true) 
            : [];
        
        // Tambah item baru
        $dataPenyesuaian[] = $item;
        
        // Hitung total (kredit - debit)
        $total = 0;
        foreach ($dataPenyesuaian as $p) {
            if ($p['tipe'] === 'Kredit') {
                $total += $p['jumlah'];
            } else {
                $total -= $p['jumlah'];
            }
        }
        
        $data = [
            'id' => $id,
            'data_penyesuaian_bank' => json_encode($dataPenyesuaian),
            'total_penyesuaian_bank' => $total
        ];
        
        return $this->save($data);
    }

    /**
     * Tambah penyesuaian buku
     */
    public function addPenyesuaianBuku($id, $item)
    {
        $rekonsiliasi = $this->find($id);
        
        if (!$rekonsiliasi) {
            throw new \RuntimeException('Rekonsiliasi tidak ditemukan');
        }
        
        if ($rekonsiliasi['status'] !== 'Draft') {
            throw new \RuntimeException('Hanya rekonsiliasi dengan status Draft yang bisa diubah');
        }
        
        $dataPenyesuaian = !empty($rekonsiliasi['data_penyesuaian_buku']) 
            ? json_decode($rekonsiliasi['data_penyesuaian_buku'], true) 
            : [];
        
        // Tambah item baru
        $dataPenyesuaian[] = $item;
        
        // Hitung total (kredit - debit)
        $total = 0;
        foreach ($dataPenyesuaian as $p) {
            if ($p['tipe'] === 'Kredit') {
                $total += $p['jumlah'];
            } else {
                $total -= $p['jumlah'];
            }
        }
        
        $data = [
            'id' => $id,
            'data_penyesuaian_buku' => json_encode($dataPenyesuaian),
            'total_penyesuaian_buku' => $total
        ];
        
        return $this->save($data);
    }

    /**
     * Hapus item dari setoran dalam perjalanan
     */
    public function removeSetoranDalamPerjalanan($id, $index)
    {
        $rekonsiliasi = $this->find($id);
        
        if (!$rekonsiliasi) {
            throw new \RuntimeException('Rekonsiliasi tidak ditemukan');
        }
        
        if ($rekonsiliasi['status'] !== 'Draft') {
            throw new \RuntimeException('Hanya rekonsiliasi dengan status Draft yang bisa diubah');
        }
        
        $dataSetoran = !empty($rekonsiliasi['data_setoran_dalam_perjalanan']) 
            ? json_decode($rekonsiliasi['data_setoran_dalam_perjalanan'], true) 
            : [];
        
        if (isset($dataSetoran[$index])) {
            unset($dataSetoran[$index]);
            $dataSetoran = array_values($dataSetoran); // Re-index array
        }
        
        // Hitung total
        $total = array_sum(array_column($dataSetoran, 'jumlah'));
        
        $data = [
            'id' => $id,
            'data_setoran_dalam_perjalanan' => json_encode($dataSetoran),
            'total_setoran_dalam_perjalanan' => $total
        ];
        
        return $this->save($data);
    }

    /**
     * Hapus item dari cek dalam edar
     */
    public function removeCekDalamEdar($id, $index)
    {
        $rekonsiliasi = $this->find($id);
        
        if (!$rekonsiliasi) {
            throw new \RuntimeException('Rekonsiliasi tidak ditemukan');
        }
        
        if ($rekonsiliasi['status'] !== 'Draft') {
            throw new \RuntimeException('Hanya rekonsiliasi dengan status Draft yang bisa diubah');
        }
        
        $dataCek = !empty($rekonsiliasi['data_cek_dalam_edar']) 
            ? json_decode($rekonsiliasi['data_cek_dalam_edar'], true) 
            : [];
        
        if (isset($dataCek[$index])) {
            unset($dataCek[$index]);
            $dataCek = array_values($dataCek); // Re-index array
        }
        
        // Hitung total
        $total = array_sum(array_column($dataCek, 'jumlah'));
        
        $data = [
            'id' => $id,
            'data_cek_dalam_edar' => json_encode($dataCek),
            'total_cek_dalam_edar' => $total
        ];
        
        return $this->save($data);
    }

    /**
     * Get statistics untuk dashboard
     */
    public function getStats($tahun = null, $bulan = null)
    {
        if (!$tahun) {
            $tahun = date('Y');
        }
        
        $builder = $this->where('YEAR(periode)', $tahun);
        
        if ($bulan) {
            $builder->where('MONTH(periode)', $bulan);
        }
        
        $stats = $builder->select("
                COUNT(*) as total_rekonsiliasi,
                COUNT(CASE WHEN status = 'Selesai' THEN 1 END) as selesai,
                COUNT(CASE WHEN status = 'Draft' THEN 1 END) as draft,
                COUNT(CASE WHEN status = 'Dibatalkan' THEN 1 END) as dibatalkan,
                COUNT(DISTINCT coa_bank_id) as jumlah_bank_direkonsiliasi
            ")
            ->first();
        
        if (!$stats) {
            return [
                'total_rekonsiliasi' => 0,
                'selesai' => 0,
                'draft' => 0,
                'dibatalkan' => 0,
                'jumlah_bank_direkonsiliasi' => 0
            ];
        }
        
        return $stats;
    }

    /**
     * Get ringkasan rekonsiliasi per bank
     */
    public function getRingkasanPerBank($tahun = null)
    {
        if (!$tahun) {
            $tahun = date('Y');
        }
        
        return $this->select("
                coa_bank_id,
                coa.kode_akun,
                coa.nama_akun,
                COUNT(*) as jumlah_rekonsiliasi,
                COUNT(CASE WHEN rekonsiliasi.status = 'Selesai' THEN 1 END) as selesai,
                COUNT(CASE WHEN rekonsiliasi.status = 'Draft' THEN 1 END) as draft,
                MAX(periode) as periode_terakhir
            ")
            ->join('coa', 'coa.id = rekonsiliasi.coa_bank_id', 'left')
            ->where('YEAR(rekonsiliasi.periode)', $tahun)
            ->groupBy('coa_bank_id, coa.kode_akun, coa.nama_akun')
            ->orderBy('coa.kode_akun', 'ASC')
            ->findAll();
    }

    /**
     * Export data untuk Excel
     */
    public function getExportData($filters = [])
    {
        $builder = $this->select('rekonsiliasi.*, 
            creator.username as creator_name,
            coa.kode_akun as kode_akun_bank,
            coa.nama_akun as nama_akun_bank')
            ->join('users as creator', 'creator.id = rekonsiliasi.created_by', 'left')
            ->join('coa', 'coa.id = rekonsiliasi.coa_bank_id', 'left');
        
        // Apply filters
        if (!empty($filters['periode_mulai'])) {
            $builder->where('rekonsiliasi.periode >=', $filters['periode_mulai']);
        }
        
        if (!empty($filters['periode_selesai'])) {
            $builder->where('rekonsiliasi.periode <=', $filters['periode_selesai']);
        }
        
        if (!empty($filters['tahun'])) {
            $builder->where('YEAR(rekonsiliasi.periode)', $filters['tahun']);
        }
        
        if (!empty($filters['coa_bank_id'])) {
            $builder->where('rekonsiliasi.coa_bank_id', $filters['coa_bank_id']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('rekonsiliasi.status', $filters['status']);
        }
        
        $builder->orderBy('rekonsiliasi.periode', 'DESC')
                ->orderBy('rekonsiliasi.tanggal_rekonsiliasi', 'DESC');
        
        $rekonsiliasi = $builder->findAll();
        
        // Format untuk export
        $exportData = [];
        foreach ($rekonsiliasi as $item) {
            $exportData[] = [
                'Periode' => $item['periode'],
                'Bank' => ($item['kode_akun_bank'] ?? '') . ' - ' . ($item['nama_akun_bank'] ?? ''),
                'Saldo Awal Bank' => $item['saldo_awal_bank'],
                'Saldo Akhir Bank' => $item['saldo_akhir_bank'],
                'Saldo Awal Buku' => $item['saldo_awal_buku'],
                'Saldo Akhir Buku' => $item['saldo_akhir_buku'],
                'Selisih' => ($item['saldo_akhir_bank'] ?? 0) - ($item['saldo_akhir_buku'] ?? 0),
                'Setoran Dalam Perjalanan' => $item['total_setoran_dalam_perjalanan'],
                'Cek Dalam Edar' => $item['total_cek_dalam_edar'],
                'Penyesuaian Bank' => $item['total_penyesuaian_bank'],
                'Penyesuaian Buku' => $item['total_penyesuaian_buku'],
                'Tanggal Rekonsiliasi' => $item['tanggal_rekonsiliasi'],
                'Status' => $item['status'],
                'Keterangan' => $item['keterangan'],
                'Dibuat Oleh' => $item['creator_name'] ?? '-',
                'Dibuat Tanggal' => $item['created_at']
            ];
        }
        
        return $exportData;
    }

    /**
     * Generate laporan rekonsiliasi
     */
    public function generateLaporan($id)
    {
        $rekonsiliasi = $this->getWithDetails($id);
        
        if (!$rekonsiliasi) {
            return null;
        }
        
        // Format data untuk laporan
        $laporan = [
            'header' => [
                'id' => $rekonsiliasi['id'],
                'periode' => $rekonsiliasi['periode'],
                'bank' => $rekonsiliasi['nama_akun_bank'],
                'kode_bank' => $rekonsiliasi['kode_akun_bank'],
                'tanggal_rekonsiliasi' => $rekonsiliasi['tanggal_rekonsiliasi'],
                'status' => $rekonsiliasi['status']
            ],
            'saldo' => [
                'saldo_awal_bank' => $rekonsiliasi['saldo_awal_bank'],
                'saldo_awal_buku' => $rekonsiliasi['saldo_awal_buku'],
                'saldo_akhir_bank' => $rekonsiliasi['saldo_akhir_bank'],
                'saldo_akhir_buku' => $rekonsiliasi['saldo_akhir_buku'],
                'selisih' => $rekonsiliasi['selisih']
            ],
            'setoran_dalam_perjalanan' => $rekonsiliasi['data_setoran_dalam_perjalanan'],
            'cek_dalam_edar' => $rekonsiliasi['data_cek_dalam_edar'],
            'penyesuaian_bank' => $rekonsiliasi['data_penyesuaian_bank'],
            'penyesuaian_buku' => $rekonsiliasi['data_penyesuaian_buku'],
            'total' => [
                'total_setoran_dalam_perjalanan' => $rekonsiliasi['total_setoran_dalam_perjalanan'],
                'total_cek_dalam_edar' => $rekonsiliasi['total_cek_dalam_edar'],
                'total_penyesuaian_bank' => $rekonsiliasi['total_penyesuaian_bank'],
                'total_penyesuaian_buku' => $rekonsiliasi['total_penyesuaian_buku']
            ],
            'keterangan' => $rekonsiliasi['keterangan']
        ];
        
        // Hitung saldo yang telah direkonsiliasi
        $laporan['perhitungan'] = [
            'saldo_bank_setelah_setoran' => $rekonsiliasi['saldo_akhir_bank'] - ($rekonsiliasi['total_setoran_dalam_perjalanan'] ?? 0),
            'saldo_bank_setelah_cek' => $rekonsiliasi['saldo_akhir_bank'] + ($rekonsiliasi['total_cek_dalam_edar'] ?? 0),
            'saldo_bank_setelah_penyesuaian' => $rekonsiliasi['saldo_akhir_bank'] - ($rekonsiliasi['total_penyesuaian_bank'] ?? 0),
            'saldo_buku_setelah_penyesuaian' => $rekonsiliasi['saldo_akhir_buku'] + ($rekonsiliasi['total_penyesuaian_buku'] ?? 0)
        ];
        
        return $laporan;
    }

    /**
     * Check if rekonsiliasi exists for periode and bank
     */
    public function existsForPeriode($periode, $coaBankId)
    {
        return $this->where('periode', $periode)
            ->where('coa_bank_id', $coaBankId)
            ->where('deleted_at IS NULL')
            ->countAllResults() > 0;
    }

    /**
     * Get rekonsiliasi by periode and bank
     */
    public function getByPeriode($periode, $coaBankId)
    {
        return $this->where('periode', $periode)
            ->where('coa_bank_id', $coaBankId)
            ->where('deleted_at IS NULL')
            ->first();
    }
}