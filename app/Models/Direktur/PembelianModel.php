<?php
// app/Models/Direktur/PembelianModel.php

namespace App\Models\Direktur;

use CodeIgniter\Model;

class PembelianModel extends Model
{
    protected $table = 'form_pembelian';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'nomor_pr',
        'karyawan_id',
        'tanggal_pengajuan',
        'tanggal_dibutuhkan',
        'prioritas',
        'alasan_pembelian',
        'status_hrd',
        'status_direktur',
        'status_keseluruhan',
        'disetujui_hrd_oleh',
        'disetujui_direktur_oleh',
        'disetujui_hrd_at',
        'disetujui_direktur_at',
        'alasan_penolakan_hrd',
        'alasan_penolakan_direktur',
        'total_estimasi',
        'supplier',
        'tipe_pembelian',
        'platform_pembelian',
        'metode_pembayaran',
        'status_pembayaran',
        'link_produk',
        'no_resi_transaksi',
        'bukti_pembelian',
        'bukti_pembayaran',
        'bukti_barang',
        'no_po_dibuat',
        'tanggal_pemesanan',
        'tanggal_terima',
        'status_penerimaan',
        'catatan',
        'created_by',
        'updated_by'
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    public function __construct()
    {
        parent::__construct();
        $this->ensureColumnsExist();
    }

    public function ensureColumnsExist()
    {
        if (!$this->db->tableExists('form_pembelian')) {
            return;
        }

        $fields = $this->db->getFieldNames('form_pembelian');
        $forge = \Config\Database::forge();
        $newFields = [];

        if (!in_array('tanggal_pengajuan', $fields)) {
            $newFields['tanggal_pengajuan'] = ['type' => 'DATE', 'null' => true, 'after' => 'karyawan_id'];
        }
        if (!in_array('tanggal_dibutuhkan', $fields)) {
            $newFields['tanggal_dibutuhkan'] = ['type' => 'DATE', 'null' => true, 'after' => 'tanggal_pengajuan'];
        }
        if (!in_array('tipe_pembelian', $fields)) {
            $newFields['tipe_pembelian'] = ['type' => 'VARCHAR', 'constraint' => '50', 'default' => 'Online', 'after' => 'supplier'];
        }
        if (!in_array('platform_pembelian', $fields)) {
            $newFields['platform_pembelian'] = ['type' => 'VARCHAR', 'constraint' => '100', 'null' => true, 'after' => 'tipe_pembelian'];
        }
        if (!in_array('metode_pembayaran', $fields)) {
            $newFields['metode_pembayaran'] = ['type' => 'VARCHAR', 'constraint' => '100', 'null' => true, 'after' => 'platform_pembelian'];
        }
        if (!in_array('link_produk', $fields)) {
            $newFields['link_produk'] = ['type' => 'TEXT', 'null' => true, 'after' => 'metode_pembayaran'];
        }
        if (!in_array('no_resi_transaksi', $fields)) {
            $newFields['no_resi_transaksi'] = ['type' => 'VARCHAR', 'constraint' => '100', 'null' => true, 'after' => 'link_produk'];
        }
        if (!in_array('status_pembayaran', $fields)) {
            $newFields['status_pembayaran'] = ['type' => 'VARCHAR', 'constraint' => '50', 'default' => 'Belum Dibayar', 'after' => 'status_direktur'];
        }
        if (!in_array('status_penerimaan', $fields)) {
            $newFields['status_penerimaan'] = ['type' => 'VARCHAR', 'constraint' => '50', 'default' => 'Belum', 'after' => 'status_pembayaran'];
        }
        if (!in_array('bukti_pembelian', $fields)) {
            $newFields['bukti_pembelian'] = ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true, 'after' => 'no_resi_transaksi'];
        }
        if (!in_array('bukti_pembayaran', $fields)) {
            $newFields['bukti_pembayaran'] = ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true, 'after' => 'bukti_pembelian'];
        }
        if (!in_array('bukti_barang', $fields)) {
            $newFields['bukti_barang'] = ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true, 'after' => 'bukti_pembayaran'];
        }

        if (!empty($newFields)) {
            $forge->addColumn('form_pembelian', $newFields);
        }

        if ($this->db->tableExists('form_pembelian_item')) {
            $itemFields = $this->db->getFieldNames('form_pembelian_item');
            $newItemFields = [];
            if (!in_array('jumlah', $itemFields) && !in_array('qty', $itemFields) && !in_array('quantity', $itemFields)) {
                $newItemFields['jumlah'] = ['type' => 'INT', 'constraint' => 11, 'default' => 1];
            }
            if (!in_array('satuan', $itemFields)) {
                $newItemFields['satuan'] = ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Pcs'];
            }
            if (!in_array('spesifikasi', $itemFields)) {
                $newItemFields['spesifikasi'] = ['type' => 'TEXT', 'null' => true];
            }
            if (!in_array('harga_estimasi', $itemFields) && !in_array('harga_satuan', $itemFields)) {
                $newItemFields['harga_estimasi'] = ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0];
            }
            if (!in_array('total_estimasi', $itemFields) && !in_array('total_harga', $itemFields) && !in_array('subtotal', $itemFields)) {
                $newItemFields['total_estimasi'] = ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0];
            }
            if (!empty($newItemFields)) {
                $forge->addColumn('form_pembelian_item', $newItemFields);
            }
        }

        // Auto-correct any small nominal values or empty dates stored incorrectly
        try {
            $this->db->query("UPDATE form_pembelian SET tanggal_pengajuan = DATE(created_at) WHERE tanggal_pengajuan IS NULL OR tanggal_pengajuan = '0000-00-00'");
            $this->db->query("UPDATE form_pembelian SET tanggal_dibutuhkan = DATE(created_at) WHERE tanggal_dibutuhkan IS NULL OR tanggal_dibutuhkan = '0000-00-00'");
            $this->db->query("UPDATE form_pembelian SET total_estimasi = total_estimasi * 1000 WHERE total_estimasi > 0 AND total_estimasi < 1000");
            if ($this->db->tableExists('form_pembelian_item')) {
                $itemCols = $this->db->getFieldNames('form_pembelian_item');
                if (in_array('harga_estimasi', $itemCols)) {
                    $this->db->query("UPDATE form_pembelian_item SET harga_estimasi = harga_estimasi * 1000 WHERE harga_estimasi > 0 AND harga_estimasi < 1000");
                }
                if (in_array('harga_satuan', $itemCols)) {
                    $this->db->query("UPDATE form_pembelian_item SET harga_satuan = harga_satuan * 1000 WHERE harga_satuan > 0 AND harga_satuan < 1000");
                }
                if (in_array('total_estimasi', $itemCols)) {
                    $this->db->query("UPDATE form_pembelian_item SET total_estimasi = total_estimasi * 1000 WHERE total_estimasi > 0 AND total_estimasi < 1000");
                }
                if (in_array('total_harga', $itemCols)) {
                    $this->db->query("UPDATE form_pembelian_item SET total_harga = total_harga * 1000 WHERE total_harga > 0 AND total_harga < 1000");
                }
                if (in_array('subtotal', $itemCols)) {
                    $this->db->query("UPDATE form_pembelian_item SET subtotal = subtotal * 1000 WHERE subtotal > 0 AND subtotal < 1000");
                }
            }
        } catch (\Throwable $th) {}
    }
    
    protected $validationRules = [
        'karyawan_id' => 'required|integer',
        'alasan_pembelian' => 'required|min_length[5]',
        'prioritas' => 'required|in_list[Rendah,Normal,Tinggi,Urgent]'
    ];
    
    protected $validationMessages = [
        'karyawan_id' => [
            'required' => 'Karyawan harus dipilih',
            'integer' => 'ID Karyawan tidak valid'
        ],
        'alasan_pembelian' => [
            'required' => 'Alasan pembelian harus diisi',
            'min_length' => 'Alasan minimal 5 karakter'
        ],
        'prioritas' => [
            'required' => 'Prioritas harus dipilih',
            'in_list' => 'Prioritas tidak valid'
        ]
    ];
    
    /**
     * Get all form pembelian for direktur approval
     * Menampilkan form pembelian yang membutuhkan approval direktur (status_direktur = 'Menunggu' dan status_hrd = 'Disetujui HRD')
     */
    public function getForDirekturApproval($status = null, $limit = null, $offset = 0)
    {
        $builder = $this->db->table('form_pembelian');
        
        $builder->select('
            form_pembelian.*,
            karyawan.nik,
            karyawan.nama_lengkap,
            karyawan.nama_panggilan,
            karyawan.jabatan,
            karyawan.departemen,
            karyawan.foto,
            hrd.nama_lengkap as hrd_nama,
            creator.nama_lengkap as created_by_name
        ');
        
        $builder->join('karyawan', 'karyawan.id = form_pembelian.karyawan_id');
        $builder->join('users as creator_user', 'creator_user.id = form_pembelian.created_by', 'left');
        $builder->join('karyawan as creator', 'creator.id = creator_user.karyawan_id', 'left');
        $builder->join('karyawan as hrd', 'hrd.id = form_pembelian.disetujui_hrd_oleh', 'left');
        
        $builder->where('form_pembelian.deleted_at', null);
        
        // Filter status untuk approval direktur
        if ($status === 'pending') {
            $builder->where('form_pembelian.status_direktur', 'Menunggu');
            $builder->where('form_pembelian.status_hrd', 'Disetujui HRD');
        } elseif ($status === 'approved') {
            $builder->where('form_pembelian.status_direktur', 'Disetujui');
        } elseif ($status === 'rejected') {
            $builder->where('form_pembelian.status_direktur', 'Ditolak');
        } elseif ($status === 'ordered') {
            $builder->where('form_pembelian.status_keseluruhan', 'Dipesan');
        } elseif ($status === 'completed') {
            $builder->where('form_pembelian.status_keseluruhan', 'Selesai');
        } else {
            // Default: tampilkan yang menunggu, disetujui, ditolak
            $builder->whereIn('form_pembelian.status_direktur', ['Menunggu', 'Disetujui', 'Ditolak']);
            $builder->where('form_pembelian.status_keseluruhan !=', 'Draft');
        }
        
        $builder->orderBy('form_pembelian.tanggal_pengajuan', 'DESC');
        
        if ($limit !== null) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get total count for direktur approval
     */
    public function getCountForDirekturApproval($status = null)
    {
        $builder = $this->db->table('form_pembelian');
        $builder->where('deleted_at', null);
        
        if ($status === 'pending') {
            $builder->where('status_direktur', 'Menunggu');
            $builder->where('status_hrd', 'Disetujui HRD');
        } elseif ($status === 'all') {
            // Semua
        } elseif ($status) {
            $builder->where('status_direktur', $status);
        } else {
            $builder->whereIn('status_direktur', ['Menunggu', 'Disetujui', 'Ditolak']);
            $builder->where('status_keseluruhan !=', 'Draft');
        }
        
        return $builder->countAllResults();
    }
    
    /**
     * Get pending count for badge notification
     */
    public function getPendingCount()
    {
        return $this->db->table('form_pembelian')
            ->where('status_direktur', 'Menunggu')
            ->where('status_hrd', 'Disetujui HRD')
            ->where('deleted_at', null)
            ->countAllResults();
    }
    
    /**
     * Get form pembelian detail by ID with complete info
     */
    public function getDetailById($id)
    {
        $builder = $this->db->table('form_pembelian');
        
        $builder->select('
            form_pembelian.*,
            karyawan.nik,
            karyawan.nama_lengkap,
            karyawan.nama_panggilan,
            karyawan.jabatan,
            karyawan.departemen,
            karyawan.tanggal_masuk as karyawan_tanggal_masuk,
            karyawan.foto,
            hrd.nama_lengkap as hrd_nama,
            hrd.jabatan as hrd_jabatan,
            direktur.nama_lengkap as direktur_nama,
            direktur.jabatan as direktur_jabatan,
            creator.nama_lengkap as created_by_name
        ');
        
        $builder->join('karyawan', 'karyawan.id = form_pembelian.karyawan_id');
        $builder->join('users as creator_user', 'creator_user.id = form_pembelian.created_by', 'left');
        $builder->join('karyawan as creator', 'creator.id = creator_user.karyawan_id', 'left');
        $builder->join('karyawan as hrd', 'hrd.id = form_pembelian.disetujui_hrd_oleh', 'left');
        $builder->join('karyawan as direktur', 'direktur.id = form_pembelian.disetujui_direktur_oleh', 'left');
        
        $builder->where('form_pembelian.id', $id);
        $builder->where('form_pembelian.deleted_at', null);
        
        return $builder->get()->getRowArray();
    }
    
    /**
     * Get items for a purchase request
     */
    public function getItems($formPembelianId)
    {
        $builder = $this->db->table('form_pembelian_item');
        $builder->where('form_pembelian_id', $formPembelianId);
        $builder->orderBy('id', 'ASC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Approve form pembelian by direktur
     */
    public function approveByDirektur($id, $userId, $karyawanId)
    {
        $data = [
            'status_direktur' => 'Disetujui',
            'status_keseluruhan' => 'Disetujui',
            'disetujui_direktur_oleh' => $karyawanId,
            'disetujui_direktur_at' => date('Y-m-d H:i:s'),
            'updated_by' => $userId,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->update($id, $data);
    }
    
    /**
     * Reject form pembelian by direktur
     */
    public function rejectByDirektur($id, $userId, $karyawanId, $alasan)
    {
        $data = [
            'status_direktur' => 'Ditolak',
            'status_keseluruhan' => 'Ditolak',
            'disetujui_direktur_oleh' => $karyawanId,
            'disetujui_direktur_at' => date('Y-m-d H:i:s'),
            'alasan_penolakan_direktur' => $alasan,
            'updated_by' => $userId,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->update($id, $data);
    }
    
    /**
     * Get form pembelian statistics for dashboard
     */
    public function getStatistics($startDate = null, $endDate = null)
    {
        $builder = $this->db->table('form_pembelian');
        
        $builder->select("
            COUNT(*) as total_pr,
            SUM(CASE WHEN status_direktur = 'Menunggu' AND status_hrd = 'Disetujui HRD' THEN 1 ELSE 0 END) as total_menunggu,
            SUM(CASE WHEN status_direktur = 'Disetujui' THEN 1 ELSE 0 END) as total_disetujui,
            SUM(CASE WHEN status_direktur = 'Ditolak' THEN 1 ELSE 0 END) as total_ditolak,
            SUM(CASE WHEN prioritas = 'Urgent' THEN 1 ELSE 0 END) as total_urgent,
            SUM(CASE WHEN prioritas = 'Tinggi' THEN 1 ELSE 0 END) as total_tinggi,
            SUM(total_estimasi) as total_nominal,
            SUM(CASE WHEN status_direktur = 'Disetujui' THEN total_estimasi ELSE 0 END) as total_disetujui_nominal
        ");
        
        $builder->where('deleted_at', null);
        
        if ($startDate) {
            $builder->where('tanggal_pengajuan >=', $startDate);
        }
        if ($endDate) {
            $builder->where('tanggal_pengajuan <=', $endDate . ' 23:59:59');
        }
        
        return $builder->get()->getRowArray();
    }
    
    /**
     * Get prioritas list
     */
    public function getPrioritasList()
    {
        return [
            'Rendah' => 'Rendah',
            'Normal' => 'Normal',
            'Tinggi' => 'Tinggi',
            'Urgent' => 'Urgent'
        ];
    }
    
    /**
     * Get status penerimaan list
     */
    public function getStatusPenerimaanList()
    {
        return [
            'Belum' => 'Belum Diterima',
            'Sebagian' => 'Sebagian',
            'Lengkap' => 'Lengkap'
        ];
    }
    
    /**
     * Get human readable status label for direktur
     */
    public function getDirekturStatusLabel($status)
    {
        $labels = [
            'Menunggu' => '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Menunggu</span>',
            'Disetujui' => '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Disetujui</span>',
            'Ditolak' => '<span class="badge bg-danger"><i class="fas fa-times me-1"></i>Ditolak</span>'
        ];
        
        return $labels[$status] ?? '<span class="badge bg-secondary">' . $status . '</span>';
    }
    
    /**
     * Get human readable status label for keseluruhan
     */
    public function getKeseluruhanStatusLabel($status)
    {
        $labels = [
            'Draft' => '<span class="badge bg-secondary">Draft</span>',
            'Menunggu HRD' => '<span class="badge bg-info">Menunggu HRD</span>',
            'Menunggu Direktur' => '<span class="badge bg-warning text-dark">Menunggu Direktur</span>',
            'Disetujui' => '<span class="badge bg-success">Disetujui</span>',
            'Ditolak' => '<span class="badge bg-danger">Ditolak</span>',
            'Dipesan' => '<span class="badge bg-primary">Dipesan</span>',
            'Selesai' => '<span class="badge bg-dark">Selesai</span>'
        ];
        
        return $labels[$status] ?? '<span class="badge bg-secondary">' . $status . '</span>';
    }
    
    /**
     * Get priority badge
     */
    public function getPrioritasBadge($prioritas)
    {
        $badges = [
            'Rendah' => '<span class="badge bg-secondary">Rendah</span>',
            'Normal' => '<span class="badge bg-info">Normal</span>',
            'Tinggi' => '<span class="badge bg-warning text-dark">Tinggi</span>',
            'Urgent' => '<span class="badge bg-danger">Urgent</span>'
        ];
        
        return $badges[$prioritas] ?? '<span class="badge bg-secondary">' . $prioritas . '</span>';
    }
    
    /**
     * Format currency
     */
    public function formatCurrency($amount)
    {
        if (empty($amount) || $amount == 0) {
            return '-';
        }
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
    
    /**
     * Get form pembelian for export
     */
    public function getForExport($startDate = null, $endDate = null, $status = null)
    {
        $builder = $this->db->table('form_pembelian');
        
        $builder->select('
            form_pembelian.nomor_pr,
            form_pembelian.tanggal_pengajuan,
            form_pembelian.tanggal_dibutuhkan,
            form_pembelian.prioritas,
            form_pembelian.alasan_pembelian,
            form_pembelian.status_hrd,
            form_pembelian.status_direktur,
            form_pembelian.status_keseluruhan,
            form_pembelian.total_estimasi,
            form_pembelian.supplier,
            form_pembelian.no_po_dibuat,
            karyawan.nik,
            karyawan.nama_lengkap,
            karyawan.jabatan,
            karyawan.departemen
        ');
        
        $builder->join('karyawan', 'karyawan.id = form_pembelian.karyawan_id');
        $builder->where('form_pembelian.deleted_at', null);
        
        if ($startDate) {
            $builder->where('form_pembelian.tanggal_pengajuan >=', $startDate);
        }
        if ($endDate) {
            $builder->where('form_pembelian.tanggal_pengajuan <=', $endDate . ' 23:59:59');
        }
        if ($status) {
            $builder->where('form_pembelian.status_direktur', $status);
        }
        
        $builder->orderBy('form_pembelian.tanggal_pengajuan', 'DESC');
        
        return $builder->get()->getResultArray();
    }
}