<?php
namespace App\Models\Accounting;

use CodeIgniter\Model;

class PelepasanAsetModel extends Model
{
    protected $table = 'pelepasan_aset';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'aset_id',
        'tanggal_pelepasan',
        'jenis_pelepasan',
        'harga_jual',
        'biaya_pelepasan',
        'nilai_buku_saat_pelepasan',
        'laba_rugi',
        'keterangan',
        'pembeli_penerima',
        'dokumen_pelepasan',
        'status',
        'disetujui_oleh',
        'disetujui_at',
        'jurnal_id',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'aset_id' => 'required|is_natural_no_zero',
        'tanggal_pelepasan' => 'required|valid_date',
        'jenis_pelepasan' => 'required|in_list[Dijual,Dihibahkan,Dimusnahkan,Hilang,Tukar Tambah]',
        'harga_jual' => 'permit_empty|numeric|greater_than_equal_to[0]',
        'biaya_pelepasan' => 'permit_empty|numeric|greater_than_equal_to[0]',
        'nilai_buku_saat_pelepasan' => 'required|numeric',
        'keterangan' => 'required',
        'status' => 'permit_empty|in_list[Draft,Disetujui,Selesai,Dibatalkan]'
    ];

    protected $validationMessages = [
        'aset_id' => [
            'required' => 'Aset harus dipilih'
        ],
        'tanggal_pelepasan' => [
            'required' => 'Tanggal pelepasan harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'jenis_pelepasan' => [
            'required' => 'Jenis pelepasan harus dipilih'
        ],
        'keterangan' => [
            'required' => 'Keterangan pelepasan harus diisi'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = ['setDefaultValues', 'validateAset', 'hitungLabaRugi', 'setCreatedBy'];
    protected $beforeUpdate = ['validateStatusChange', 'validateAsetOnUpdate', 'hitungLabaRugi', 'validateApproval'];

    /**
     * Set default values
     */
    protected function setDefaultValues(array $data)
    {
        if (!isset($data['data']['status'])) {
            $data['data']['status'] = 'Draft';
        }
        
        if (!isset($data['data']['harga_jual'])) {
            $data['data']['harga_jual'] = 0;
        }
        
        if (!isset($data['data']['biaya_pelepasan'])) {
            $data['data']['biaya_pelepasan'] = 0;
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
     * Validasi aset sebelum pelepasan
     */
    protected function validateAset(array $data)
    {
        $asetId = $data['data']['aset_id'] ?? null;
        
        if ($asetId) {
            $asetModel = new AsetTetapModel();
            $aset = $asetModel->find($asetId);
            
            if (!$aset) {
                throw new \RuntimeException('Aset tidak ditemukan');
            }
            
            if ($aset['status'] === 'Dilepas') {
                throw new \RuntimeException('Aset ini sudah dilepas sebelumnya');
            }
            
            if ($aset['status'] !== 'Aktif') {
                throw new \RuntimeException('Hanya aset dengan status Aktif yang dapat dilepas');
            }
            
            // Hitung nilai buku saat pelepasan
            $nilaiBuku = $asetModel->getNilaiBuku($asetId);
            $data['data']['nilai_buku_saat_pelepasan'] = $nilaiBuku;
            
            // Simpan data aset untuk referensi
            $data['data']['_aset_temp'] = $aset;
        }
        
        return $data;
    }

    /**
     * Validasi aset pada update
     */
    protected function validateAsetOnUpdate(array $data)
    {
        $id = $data['id'][0] ?? null;
        
        if ($id) {
            $current = $this->find($id);
            
            if ($current && $current['status'] === 'Selesai') {
                // Jika sudah selesai, tidak bisa mengubah data aset
                if (isset($data['data']['aset_id']) && $data['data']['aset_id'] != $current['aset_id']) {
                    throw new \RuntimeException('Tidak dapat mengubah aset pada pelepasan yang sudah selesai');
                }
            }
        }
        
        // Jika ada perubahan aset_id, validasi ulang
        if (isset($data['data']['aset_id'])) {
            $asetId = $data['data']['aset_id'];
            $asetModel = new AsetTetapModel();
            $aset = $asetModel->find($asetId);
            
            if (!$aset) {
                throw new \RuntimeException('Aset tidak ditemukan');
            }
            
            // Hitung nilai buku saat pelepasan
            $nilaiBuku = $asetModel->getNilaiBuku($asetId);
            $data['data']['nilai_buku_saat_pelepasan'] = $nilaiBuku;
        }
        
        return $data;
    }

    /**
     * Hitung laba/rugi pelepasan
     */
    protected function hitungLabaRugi(array $data)
    {
        $hargaJual = $data['data']['harga_jual'] ?? 0;
        $biayaPelepasan = $data['data']['biaya_pelepasan'] ?? 0;
        $nilaiBuku = $data['data']['nilai_buku_saat_pelepasan'] ?? 0;
        
        // Laba/Rugi = Harga Jual - Biaya Pelepasan - Nilai Buku
        $labaRugi = $hargaJual - $biayaPelepasan - $nilaiBuku;
        $data['data']['laba_rugi'] = $labaRugi;
        
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
                    $oldStatus = $current['status'];
                    $newStatus = $data['data']['status'];
                    
                    // Validasi urutan status
                    $validTransitions = [
                        'Draft' => ['Disetujui', 'Dibatalkan'],
                        'Disetujui' => ['Selesai', 'Dibatalkan'],
                        'Selesai' => [], // Tidak bisa berubah lagi
                        'Dibatalkan' => [] // Tidak bisa berubah lagi
                    ];
                    
                    if (isset($validTransitions[$oldStatus]) && !in_array($newStatus, $validTransitions[$oldStatus])) {
                        throw new \RuntimeException("Status tidak dapat berubah dari {$oldStatus} menjadi {$newStatus}");
                    }
                    
                    // Jika status berubah menjadi Disetujui
                    if ($newStatus === 'Disetujui' && $oldStatus === 'Draft') {
                        if (empty($data['data']['disetujui_oleh'])) {
                            $data['data']['disetujui_oleh'] = session()->get('user_id');
                        }
                        if (empty($data['data']['disetujui_at'])) {
                            $data['data']['disetujui_at'] = date('Y-m-d H:i:s');
                        }
                    }
                    
                    // Jika status berubah menjadi Selesai
                    if ($newStatus === 'Selesai' && $oldStatus === 'Disetujui') {
                        // Validasi jurnal harus sudah ada
                        if (empty($current['jurnal_id']) && empty($data['data']['jurnal_id'])) {
                            throw new \RuntimeException('Pelepasan aset harus memiliki jurnal_id sebelum diselesaikan');
                        }
                    }
                }
            }
        }
        
        return $data;
    }

    /**
     * Validasi approval
     */
    protected function validateApproval(array $data)
    {
        // Jika disetujui_oleh diisi, pastikan user ada
        if (isset($data['data']['disetujui_oleh']) && !empty($data['data']['disetujui_oleh'])) {
            $userModel = new \App\Models\UserModel();
            $user = $userModel->find($data['data']['disetujui_oleh']);
            
            if (!$user) {
                throw new \RuntimeException('User approval tidak valid');
            }
        }
        
        return $data;
    }

    /**
     * Get all pelepasan aset with filters
     */
    public function getAllWithFilters($filters = [], $perPage = 20, $page = 1)
    {
        $builder = $this->select('pelepasan_aset.*, 
            aset_tetap.kode_aset,
            aset_tetap.nama_aset,
            aset_tetap.harga_perolehan,
            aset_tetap.tanggal_perolehan,
            aset_tetap.status as aset_status,
            kategori.nama_kategori,
            kategori.kode_kategori,
            creator.username as creator_name,
            approver.username as approver_name,
            jurnal.nomor_jurnal,
            jurnal.status as jurnal_status')
            ->join('aset_tetap', 'aset_tetap.id = pelepasan_aset.aset_id', 'left')
            ->join('aset_tetap_kategori as kategori', 'kategori.id = aset_tetap.kategori_id', 'left')
            ->join('users as creator', 'creator.id = pelepasan_aset.created_by', 'left')
            ->join('users as approver', 'approver.id = pelepasan_aset.disetujui_oleh', 'left')
            ->join('jurnal', 'jurnal.id = pelepasan_aset.jurnal_id', 'left');
        
        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('aset_tetap.kode_aset', $search)
                ->orLike('aset_tetap.nama_aset', $search)
                ->orLike('pelepasan_aset.pembeli_penerima', $search)
                ->orLike('pelepasan_aset.keterangan', $search)
                ->groupEnd();
        }
        
        if (!empty($filters['aset_id'])) {
            $builder->where('pelepasan_aset.aset_id', $filters['aset_id']);
        }
        
        if (!empty($filters['kategori_id'])) {
            $builder->where('aset_tetap.kategori_id', $filters['kategori_id']);
        }
        
        if (!empty($filters['jenis_pelepasan'])) {
            $builder->where('pelepasan_aset.jenis_pelepasan', $filters['jenis_pelepasan']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('pelepasan_aset.status', $filters['status']);
        }
        
        if (!empty($filters['tanggal_mulai'])) {
            $builder->where('pelepasan_aset.tanggal_pelepasan >=', $filters['tanggal_mulai']);
        }
        
        if (!empty($filters['tanggal_selesai'])) {
            $builder->where('pelepasan_aset.tanggal_pelepasan <=', $filters['tanggal_selesai']);
        }
        
        if (isset($filters['min_laba_rugi']) && $filters['min_laba_rugi'] !== '') {
            $builder->where('pelepasan_aset.laba_rugi >=', $filters['min_laba_rugi']);
        }
        
        if (isset($filters['max_laba_rugi']) && $filters['max_laba_rugi'] !== '') {
            $builder->where('pelepasan_aset.laba_rugi <=', $filters['max_laba_rugi']);
        }
        
        $builder->orderBy('pelepasan_aset.tanggal_pelepasan', 'DESC')
                ->orderBy('pelepasan_aset.id', 'DESC');
        
        // Get total for pagination
        $total = $builder->countAllResults(false);
        
        // Get paginated results
        $offset = ($page - 1) * $perPage;
        $pelepasan = $builder->limit($perPage, $offset)->findAll();
        
        return [
            'data' => $pelepasan,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Get pelepasan aset by ID with details
     */
    public function getWithDetails($id)
    {
        $pelepasan = $this->select('pelepasan_aset.*, 
            aset_tetap.kode_aset,
            aset_tetap.nama_aset,
            aset_tetap.harga_perolehan,
            aset_tetap.nilai_residu,
            aset_tetap.masa_manfaat_tahun,
            aset_tetap.metode_penyusutan,
            aset_tetap.tanggal_perolehan,
            aset_tetap.status as aset_status,
            aset_tetap.kondisi as aset_kondisi,
            aset_tetap.lokasi as aset_lokasi,
            kategori.nama_kategori,
            kategori.kode_kategori,
            creator.username as creator_name,
            creator.name as creator_fullname,
            approver.username as approver_name,
            approver.name as approver_fullname,
            jurnal.nomor_jurnal,
            jurnal.status as jurnal_status,
            jurnal.created_at as jurnal_created_at')
            ->join('aset_tetap', 'aset_tetap.id = pelepasan_aset.aset_id', 'left')
            ->join('aset_tetap_kategori as kategori', 'kategori.id = aset_tetap.kategori_id', 'left')
            ->join('users as creator', 'creator.id = pelepasan_aset.created_by', 'left')
            ->join('users as approver', 'approver.id = pelepasan_aset.disetujui_oleh', 'left')
            ->join('jurnal', 'jurnal.id = pelepasan_aset.jurnal_id', 'left')
            ->where('pelepasan_aset.id', $id)
            ->first();
        
        if ($pelepasan) {
            // Ambil riwayat penyusutan terakhir
            $penyusutanModel = new PenyusutanModel();
            $latestPenyusutan = $penyusutanModel->getLatestByAset($pelepasan['aset_id']);
            $pelepasan['latest_penyusutan'] = $latestPenyusutan;
            
            // Ambil riwayat mutasi
            $mutasiModel = new MutasiAsetModel();
            $pelepasan['riwayat_mutasi'] = $mutasiModel->getByAset($pelepasan['aset_id']);
        }
        
        return $pelepasan;
    }

    /**
     * Get pelepasan by aset
     */
    public function getByAset($asetId)
    {
        return $this->where('aset_id', $asetId)
                    ->orderBy('tanggal_pelepasan', 'DESC')
                    ->findAll();
    }

    /**
     * Get pelepasan by status
     */
    public function getByStatus($status)
    {
        return $this->where('status', $status)
                    ->orderBy('tanggal_pelepasan', 'DESC')
                    ->findAll();
    }

    /**
     * Get pending approval
     */
    public function getPendingApproval()
    {
        return $this->where('status', 'Disetujui')
                    ->where('jurnal_id IS NULL')
                    ->orderBy('tanggal_pelepasan', 'ASC')
                    ->findAll();
    }

    /**
     * Get pelepasan by periode
     */
    public function getByPeriode($tahun, $bulan = null)
    {
        $builder = $this->where('YEAR(tanggal_pelepasan)', $tahun)
                        ->where('status', 'Selesai');
        
        if ($bulan) {
            $builder->where('MONTH(tanggal_pelepasan)', $bulan);
        }
        
        return $builder->orderBy('tanggal_pelepasan', 'ASC')->findAll();
    }

    /**
     * Approve pelepasan aset
     */
    public function approve($id)
    {
        $pelepasan = $this->find($id);
        
        if (!$pelepasan) {
            throw new \RuntimeException('Data pelepasan aset tidak ditemukan');
        }
        
        if ($pelepasan['status'] !== 'Draft') {
            throw new \RuntimeException('Hanya pelepasan dengan status Draft yang dapat disetujui');
        }
        
        return $this->update($id, [
            'status' => 'Disetujui',
            'disetujui_oleh' => session()->get('user_id'),
            'disetujui_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Reject pelepasan aset (batalkan)
     */
    public function reject($id, $alasan = null)
    {
        $pelepasan = $this->find($id);
        
        if (!$pelepasan) {
            throw new \RuntimeException('Data pelepasan aset tidak ditemukan');
        }
        
        if ($pelepasan['status'] !== 'Draft' && $pelepasan['status'] !== 'Disetujui') {
            throw new \RuntimeException('Hanya pelepasan dengan status Draft atau Disetujui yang dapat dibatalkan');
        }
        
        $keterangan = $pelepasan['keterangan'];
        if ($alasan) {
            $keterangan .= "\n[DIBATALKAN] " . $alasan;
        }
        
        return $this->update($id, [
            'status' => 'Dibatalkan',
            'keterangan' => $keterangan
        ]);
    }

    /**
     * Complete pelepasan aset (update status aset dan buat jurnal)
     */
    public function complete($id, $jurnalId)
    {
        $pelepasan = $this->find($id);
        
        if (!$pelepasan) {
            throw new \RuntimeException('Data pelepasan aset tidak ditemukan');
        }
        
        if ($pelepasan['status'] !== 'Disetujui') {
            throw new \RuntimeException('Hanya pelepasan dengan status Disetujui yang dapat diselesaikan');
        }
        
        // Update status aset menjadi Dilepas
        $asetModel = new AsetTetapModel();
        $asetModel->update($pelepasan['aset_id'], [
            'status' => 'Dilepas',
            'catatan' => ($asetModel->find($pelepasan['aset_id'])['catatan'] ?? '') . 
                         "\n[Dilepas pada " . date('Y-m-d') . "] " . $pelepasan['keterangan']
        ]);
        
        // Update pelepasan
        return $this->update($id, [
            'status' => 'Selesai',
            'jurnal_id' => $jurnalId
        ]);
    }

    /**
     * Get ringkasan pelepasan per jenis
     */
    public function getRingkasanPerJenis($tahun = null)
    {
        $builder = $this->select("
                jenis_pelepasan,
                COUNT(*) as jumlah,
                SUM(harga_jual) as total_harga_jual,
                SUM(biaya_pelepasan) as total_biaya,
                SUM(nilai_buku_saat_pelepasan) as total_nilai_buku,
                SUM(laba_rugi) as total_laba_rugi
            ")
            ->where('status', 'Selesai')
            ->groupBy('jenis_pelepasan');
        
        if ($tahun) {
            $builder->where('YEAR(tanggal_pelepasan)', $tahun);
        }
        
        return $builder->findAll();
    }

    /**
     * Get ringkasan pelepasan per bulan
     */
    public function getRingkasanPerBulan($tahun = null)
    {
        $builder = $this->select("
                DATE_FORMAT(tanggal_pelepasan, '%Y-%m') as bulan,
                COUNT(*) as jumlah,
                SUM(harga_jual) as total_harga_jual,
                SUM(biaya_pelepasan) as total_biaya,
                SUM(nilai_buku_saat_pelepasan) as total_nilai_buku,
                SUM(laba_rugi) as total_laba_rugi
            ")
            ->where('status', 'Selesai')
            ->groupBy("DATE_FORMAT(tanggal_pelepasan, '%Y-%m')")
            ->orderBy('bulan', 'ASC');
        
        if ($tahun) {
            $builder->where('YEAR(tanggal_pelepasan)', $tahun);
        }
        
        return $builder->findAll();
    }

    /**
     * Get ringkasan pelepasan per aset
     */
    public function getRingkasanPerAset($tahun = null)
    {
        $builder = $this->select("
                aset_tetap.id as aset_id,
                aset_tetap.kode_aset,
                aset_tetap.nama_aset,
                aset_tetap.harga_perolehan,
                kategori.nama_kategori,
                pelepasan_aset.jenis_pelepasan,
                pelepasan_aset.tanggal_pelepasan,
                pelepasan_aset.harga_jual,
                pelepasan_aset.nilai_buku_saat_pelepasan,
                pelepasan_aset.laba_rugi
            ")
            ->join('aset_tetap', 'aset_tetap.id = pelepasan_aset.aset_id')
            ->join('aset_tetap_kategori as kategori', 'kategori.id = aset_tetap.kategori_id')
            ->where('pelepasan_aset.status', 'Selesai');
        
        if ($tahun) {
            $builder->where('YEAR(pelepasan_aset.tanggal_pelepasan)', $tahun);
        }
        
        return $builder->orderBy('pelepasan_aset.tanggal_pelepasan', 'DESC')->findAll();
    }

    /**
     * Get statistics untuk dashboard
     */
    public function getStats($tahun = null)
    {
        $builder = $this->where('status', 'Selesai');
        
        if ($tahun) {
            $builder->where('YEAR(tanggal_pelepasan)', $tahun);
        }
        
        $stats = $builder->select("
                COUNT(*) as total_pelepasan,
                SUM(harga_jual) as total_harga_jual,
                SUM(biaya_pelepasan) as total_biaya,
                SUM(nilai_buku_saat_pelepasan) as total_nilai_buku,
                SUM(laba_rugi) as total_laba_rugi,
                SUM(CASE WHEN laba_rugi > 0 THEN 1 ELSE 0 END) as jumlah_laba,
                SUM(CASE WHEN laba_rugi < 0 THEN 1 ELSE 0 END) as jumlah_rugi,
                SUM(CASE WHEN laba_rugi = 0 THEN 1 ELSE 0 END) as jumlah_impas
            ")
            ->first();
        
        return $stats ?? [
            'total_pelepasan' => 0,
            'total_harga_jual' => 0,
            'total_biaya' => 0,
            'total_nilai_buku' => 0,
            'total_laba_rugi' => 0,
            'jumlah_laba' => 0,
            'jumlah_rugi' => 0,
            'jumlah_impas' => 0
        ];
    }

    /**
     * Get total laba/rugi pelepasan per tahun
     */
    public function getTotalLabaRugiPerTahun()
    {
        return $this->select("
                YEAR(tanggal_pelepasan) as tahun,
                SUM(laba_rugi) as total_laba_rugi,
                COUNT(*) as jumlah_pelepasan
            ")
            ->where('status', 'Selesai')
            ->groupBy('YEAR(tanggal_pelepasan)')
            ->orderBy('tahun', 'ASC')
            ->findAll();
    }

    /**
     * Check if aset can be released
     */
    public function canRelease($asetId)
    {
        $asetModel = new AsetTetapModel();
        $aset = $asetModel->find($asetId);
        
        if (!$aset) {
            return false;
        }
        
        // Cek apakah sudah ada pelepasan
        $existing = $this->where('aset_id', $asetId)
                         ->where('status !=', 'Dibatalkan')
                         ->countAllResults();
        
        if ($existing > 0) {
            return false;
        }
        
        // Hanya aset aktif yang bisa dilepas
        return $aset['status'] === 'Aktif';
    }

    /**
     * Export data untuk Excel
     */
    public function getExportData($filters = [])
    {
        $builder = $this->select('pelepasan_aset.*, 
            aset_tetap.kode_aset,
            aset_tetap.nama_aset,
            aset_tetap.harga_perolehan,
            kategori.nama_kategori,
            jurnal.nomor_jurnal,
            approver.username as approver_name')
            ->join('aset_tetap', 'aset_tetap.id = pelepasan_aset.aset_id', 'left')
            ->join('aset_tetap_kategori as kategori', 'kategori.id = aset_tetap.kategori_id', 'left')
            ->join('jurnal', 'jurnal.id = pelepasan_aset.jurnal_id', 'left')
            ->join('users as approver', 'approver.id = pelepasan_aset.disetujui_oleh', 'left');
        
        if (!empty($filters['tahun'])) {
            $builder->where('YEAR(pelepasan_aset.tanggal_pelepasan)', $filters['tahun']);
        }
        
        if (!empty($filters['jenis_pelepasan'])) {
            $builder->where('pelepasan_aset.jenis_pelepasan', $filters['jenis_pelepasan']);
        }
        
        if (!empty($filters['status'])) {
            $builder->where('pelepasan_aset.status', $filters['status']);
        }
        
        $pelepasan = $builder->orderBy('pelepasan_aset.tanggal_pelepasan', 'DESC')->findAll();
        
        $exportData = [];
        foreach ($pelepasan as $item) {
            $exportData[] = [
                'Kode Aset' => $item['kode_aset'],
                'Nama Aset' => $item['nama_aset'],
                'Kategori' => $item['nama_kategori'] ?? '-',
                'Tanggal Pelepasan' => $item['tanggal_pelepasan'],
                'Jenis Pelepasan' => $item['jenis_pelepasan'],
                'Harga Jual' => $item['harga_jual'],
                'Biaya Pelepasan' => $item['biaya_pelepasan'],
                'Nilai Buku' => $item['nilai_buku_saat_pelepasan'],
                'Laba/Rugi' => $item['laba_rugi'],
                'Pembeli/Penerima' => $item['pembeli_penerima'] ?? '-',
                'Keterangan' => $item['keterangan'],
                'Status' => $item['status'],
                'Disetujui Oleh' => $item['approver_name'] ?? '-',
                'Tanggal Setuju' => $item['disetujui_at'] ?? '-',
                'No Jurnal' => $item['nomor_jurnal'] ?? '-'
            ];
        }
        
        return $exportData;
    }

    /**
     * Get total nilai buku aset yang dilepas
     */
    public function getTotalNilaiBukuDilepas($tahun = null)
    {
        $builder = $this->where('status', 'Selesai');
        
        if ($tahun) {
            $builder->where('YEAR(tanggal_pelepasan)', $tahun);
        }
        
        $result = $builder->selectSum('nilai_buku_saat_pelepasan')->first();
        return $result['nilai_buku_saat_pelepasan'] ?? 0;
    }
}