<?php
namespace App\Models;

use CodeIgniter\Model;

class SuratJalanModel extends Model
{
    protected $table = 'surat_jalan';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        // Existing fields dari database
        'nomor_surat_jalan', 
        'project_id', 
        'invoice_id', 
        'tanggal_kirim',
        'penerima_nama', 
        'alamat_pengiriman', 
        'sopir', 
        'no_kendaraan',
        'status', 
        'keterangan', 
        'created_by',
        
        // NEW FIELDS dari database (diperbarui)
        'kode_format', 
        'bulan_format', 
        'tahun_format',
        'penerima_perusahaan', 
        'penerima_up', 
        'penerima_telepon',
        'lokasi_proyek',
        'disiapkan_oleh', 
        'disiapkan_telepon', 
        'disiapkan_jabatan',
        'dikirim_oleh', 
        'dikirim_telepon',
        'diterima_oleh', 
        'diterima_telepon', 
        'diterima_perusahaan',
        'status_terima', 
        'tanggal_terima',
        'catatan_barang',
        
        // Field perusahaan pengirim dari database
        'perusahaan_pengirim_id',
        'perusahaan_pengirim_nama',
        'perusahaan_pengirim_alamat',
        'perusahaan_pengirim_website',
        
        // Timestamps
        'created_at',
        'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'project_id' => 'required|integer',
        'tanggal_kirim' => 'required|valid_date',
        'penerima_perusahaan' => 'required|max_length[200]',
        'penerima_up' => 'required|max_length[100]',
        'status' => 'required|in_list[draft,diproses,dikirim,diterima,ditolak,dibatalkan]',
        'nomor_surat_jalan' => 'required|max_length[100]|is_unique[surat_jalan.nomor_surat_jalan,id,{id}]'
    ];
    
    protected $validationMessages = [
        'project_id' => [
            'required' => 'Project wajib dipilih',
            'integer' => 'Project ID harus berupa angka'
        ],
        'tanggal_kirim' => [
            'required' => 'Tanggal pengiriman wajib diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'penerima_perusahaan' => [
            'required' => 'Nama perusahaan penerima wajib diisi',
            'max_length' => 'Nama perusahaan maksimal 200 karakter'
        ],
        'penerima_up' => [
            'required' => 'UP (Penanggung Jawab) wajib diisi',
            'max_length' => 'Nama UP maksimal 100 karakter'
        ],
        'nomor_surat_jalan' => [
            'required' => 'Nomor surat jalan wajib diisi',
            'max_length' => 'Nomor surat jalan maksimal 100 karakter',
            'is_unique' => 'Nomor surat jalan sudah digunakan'
        ]
    ];
    
    protected $skipValidation = false;
    
    protected $perusahaanModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->perusahaanModel = new \App\Models\PerusahaanModel();
    }
    
    /**
     * Generate nomor surat jalan otomatis
     * Format: XXX/DN-CDW/ROM/YY (contoh: 039/DN-CDW/VII/24)
     */
    public function generateNomorSuratJalan()
    {
        $year = date('y'); // 24
        $month = date('n'); // 7 (July)
        
        // Konversi bulan ke romawi
        $romawi = $this->convertToRoman($month);
        
        // Cari nomor terakhir bulan ini dengan format: XXX/DN-CDW/ROM/YY
        $builder = $this->db->table($this->table);
        $builder->select('nomor_surat_jalan')
                ->like('nomor_surat_jalan', '%/DN-CDW/' . $romawi . '/' . $year)
                ->orderBy('id', 'DESC')
                ->limit(1);
        
        $last = $builder->get()->getRow();
        
        if ($last) {
            // Format: 039/DN-CDW/VII/24
            $parts = explode('/', $last->nomor_surat_jalan);
            $lastNumber = (int) $parts[0];
            $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '001';
        }
        
        // Format: 039/DN-CDW/VII/24
        return $nextNumber . '/DN-CDW/' . $romawi . '/' . $year;
    }
    
    /**
     * Helper untuk konversi angka ke romawi
     */
    private function convertToRoman($number)
    {
        $map = [
            'I', 'II', 'III', 'IV', 'V', 'VI', 
            'VII', 'VIII', 'IX', 'X', 'XI', 'XII'
        ];
        
        return isset($map[$number - 1]) ? $map[$number - 1] : 'I';
    }
    
    /**
     * Get surat jalan dengan detail lengkap
     */
    public function getSuratJalanWithDetails($id)
    {
        $builder = $this->db->table('surat_jalan sj');
        $builder->select('sj.*, 
            p.nama_project, p.kode_project, 
            c.nama_perusahaan as client_nama, c.alamat as client_alamat, 
            c.telepon as client_telepon, c.email as client_email, 
            c.nama_kontak as client_kontak,
            c.sales_id, 
            i.nomor_invoice,
            u.name as created_by_name,
            kp.nama_lengkap as nama_sales')
            ->join('project p', 'p.id = sj.project_id')
            ->join('client c', 'c.id = p.client_id')
            ->join('users u', 'u.id = sj.created_by', 'left')
            ->join('karyawan kp', 'kp.id = c.sales_id', 'left')
            ->join('invoice i', 'i.id = sj.invoice_id', 'left')
            ->where('sj.id', $id);
        
        $suratJalan = $builder->get()->getRowArray();
        
        if ($suratJalan) {
            // Get data perusahaan pengirim (CDW) - sebagai fallback jika field kosong
            $perusahaanCDW = $this->perusahaanModel->getPerusahaanCDW();
            
            // Gunakan data dari database jika ada, jika tidak pakai dari perusahaanModel
            if (empty($suratJalan['perusahaan_pengirim_nama'])) {
                $suratJalan['perusahaan_pengirim_nama'] = $perusahaanCDW['nama_perusahaan'] ?? 'PT. Cipta Duta Wacana';
            }
            
            if (empty($suratJalan['perusahaan_pengirim_alamat'])) {
                $suratJalan['perusahaan_pengirim_alamat'] = $perusahaanCDW['alamat'] ?? 'Villa Bintaro Regency, Jl. Riau Blok K1 No. 2, Pondok Kacang Timur, Tangerang Selatan 15226';
            }
            
            if (empty($suratJalan['perusahaan_pengirim_website'])) {
                $suratJalan['perusahaan_pengirim_website'] = $perusahaanCDW['website'] ?? 'www.cdw-engineering.com';
            }
            
            // Jika perusahaan_pengirim_id tidak ada, ambil dari perusahaanModel
            if (empty($suratJalan['perusahaan_pengirim_id'])) {
                $suratJalan['perusahaan_pengirim_id'] = $perusahaanCDW['id'] ?? 1;
            }
            
            // Logo dari perusahaanModel
            $suratJalan['perusahaan_logo_url'] = $this->perusahaanModel->getLogoUrl($perusahaanCDW);
            $suratJalan['perusahaan_logo_base64'] = $this->perusahaanModel->getLogoBase64($perusahaanCDW);
            
            // Isi field kosong dengan data client
            if (empty($suratJalan['penerima_perusahaan'])) {
                $suratJalan['penerima_perusahaan'] = $suratJalan['client_nama'];
            }
            
            if (empty($suratJalan['penerima_nama'])) {
                $suratJalan['penerima_nama'] = $suratJalan['client_kontak'];
            }
            
            if (empty($suratJalan['alamat_pengiriman'])) {
                $suratJalan['alamat_pengiriman'] = $suratJalan['client_alamat'];
            }
            
            if (empty($suratJalan['lokasi_proyek'])) {
                $suratJalan['lokasi_proyek'] = $suratJalan['client_alamat'];
            }
            
            // Konversi \n ke <br> untuk display HTML
            if (!empty($suratJalan['catatan_barang'])) {
                $suratJalan['catatan_barang_html'] = nl2br($suratJalan['catatan_barang']);
            }
            
            // Format tanggal untuk display
            $suratJalan['tanggal_kirim_formatted'] = date('d-M-y', strtotime($suratJalan['tanggal_kirim']));
            if (!empty($suratJalan['tanggal_terima'])) {
                $suratJalan['tanggal_terima_formatted'] = date('d-M-y H:i', strtotime($suratJalan['tanggal_terima']));
            }
        }
        
        return $suratJalan;
    }
    
    /**
     * Validasi dan lengkapi data surat jalan sebelum save
     */
    public function prepareSuratJalanData($data)
    {
        // Default values untuk field yang mungkin kosong
        $defaults = [
            'status' => 'diproses',
            'status_terima' => 'pending',
            'kode_format' => 'DN-CDW',
            'bulan_format' => $this->convertToRoman(date('n')),
            'tahun_format' => date('y')
        ];
        
        foreach ($defaults as $key => $value) {
            if (empty($data[$key])) {
                $data[$key] = $value;
            }
        }
        
        // Generate nomor jika kosong
        if (empty($data['nomor_surat_jalan'])) {
            $data['nomor_surat_jalan'] = $this->generateNomorSuratJalan();
        }
        
        // Isi data perusahaan pengirim jika kosong
        if (empty($data['perusahaan_pengirim_id']) || empty($data['perusahaan_pengirim_nama'])) {
            $perusahaanCDW = $this->perusahaanModel->getPerusahaanCDW();
            $data['perusahaan_pengirim_id'] = $perusahaanCDW['id'] ?? 1;
            $data['perusahaan_pengirim_nama'] = $perusahaanCDW['nama_perusahaan'] ?? 'PT. Cipta Duta Wacana';
            $data['perusahaan_pengirim_alamat'] = $perusahaanCDW['alamat'] ?? 'Villa Bintaro Regency, Jl. Riau Blok K1 No. 2, Pondok Kacang Timur, Tangerang Selatan 15226';
            $data['perusahaan_pengirim_website'] = $perusahaanCDW['website'] ?? 'www.cdw-engineering.com';
        }
        
        return $data;
    }
    
    /**
     * Get surat jalan by sales ID
     */
    public function getSuratJalanBySales($salesId)
    {
        $builder = $this->db->table('surat_jalan sj');
        $builder->select('sj.*, p.nama_project, c.nama_perusahaan, c.sales_id, i.nomor_invoice')
                ->join('project p', 'p.id = sj.project_id')
                ->join('client c', 'c.id = p.client_id')
                ->join('invoice i', 'i.id = sj.invoice_id', 'left')
                ->where('c.sales_id', $salesId)
                ->orderBy('sj.tanggal_kirim', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get status count for sales
     */
    public function getStatusCount($salesId)
    {
        $builder = $this->db->table('surat_jalan sj');
        $builder->select('sj.status, COUNT(*) as count')
                ->join('project p', 'p.id = sj.project_id')
                ->join('client c', 'c.id = p.client_id')
                ->where('c.sales_id', $salesId)
                ->groupBy('sj.status');
        
        $result = $builder->get()->getResultArray();
        
        $counts = [
            'diproses' => 0,
            'dikirim' => 0,
            'diterima' => 0,
            'dibatalkan' => 0,
            'draft' => 0
        ];
        
        foreach ($result as $row) {
            if (isset($counts[$row['status']])) {
                $counts[$row['status']] = $row['count'];
            }
        }
        
        return $counts;
    }
    
    /**
     * Get surat jalan by status untuk sales tertentu
     */
    public function getSuratJalanByStatus($salesId, $status)
    {
        $builder = $this->db->table('surat_jalan sj');
        $builder->select('sj.*, 
            p.nama_project,
            c.nama_perusahaan,
            i.nomor_invoice')
            ->join('project p', 'p.id = sj.project_id')
            ->join('client c', 'c.id = p.client_id')
            ->join('invoice i', 'i.id = sj.invoice_id', 'left')
            ->where('c.sales_id', $salesId)
            ->where('sj.status', $status)
            ->orderBy('sj.tanggal_kirim', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Check if surat jalan number exists
     */
    public function isSuratJalanNumberExists($nomorSuratJalan, $excludeId = null)
    {
        $builder = $this->db->table($this->table);
        $builder->where('nomor_surat_jalan', $nomorSuratJalan);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }
    
    /**
     * Get today's deliveries for sales
     */
    public function getTodayDeliveries($salesId)
    {
        $today = date('Y-m-d');
        
        $builder = $this->db->table('surat_jalan sj');
        $builder->select('sj.*, p.nama_project, c.nama_perusahaan, i.nomor_invoice')
                ->join('project p', 'p.id = sj.project_id')
                ->join('client c', 'c.id = p.client_id')
                ->join('invoice i', 'i.id = sj.invoice_id', 'left')
                ->where('c.sales_id', $salesId)
                ->where('sj.tanggal_kirim', $today)
                ->whereIn('sj.status', ['diproses', 'dikirim'])
                ->orderBy('sj.created_at', 'DESC')
                ->limit(10);
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Update status penerimaan surat jalan
     */
    public function updatePenerimaan($id, $data)
    {
        $updateData = [
            'diterima_oleh' => $data['diterima_oleh'] ?? null,
            'diterima_telepon' => $data['diterima_telepon'] ?? null,
            'diterima_perusahaan' => $data['diterima_perusahaan'] ?? null,
            'status_terima' => $data['status_terima'] ?? 'diterima',
            'tanggal_terima' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // Jika diterima, update status surat jalan juga
        if (($data['status_terima'] ?? '') === 'diterima') {
            $updateData['status'] = 'diterima';
        }
        
        return $this->update($id, $updateData);
    }
    
    /**
     * Get all surat jalan untuk admin
     */
    public function getAllSuratJalan($limit = 50, $offset = 0)
    {
        $builder = $this->db->table('surat_jalan sj');
        $builder->select('sj.*, p.nama_project, c.nama_perusahaan, i.nomor_invoice, u.name as created_by_name')
                ->join('project p', 'p.id = sj.project_id', 'left')
                ->join('client c', 'c.id = p.client_id', 'left')
                ->join('invoice i', 'i.id = sj.invoice_id', 'left')
                ->join('users u', 'u.id = sj.created_by', 'left')
                ->orderBy('sj.tanggal_kirim', 'DESC')
                ->limit($limit, $offset);
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Search surat jalan
     */
    public function searchSuratJalan($keyword, $salesId = null)
    {
        $builder = $this->db->table('surat_jalan sj');
        $builder->select('sj.*, p.nama_project, c.nama_perusahaan, i.nomor_invoice')
                ->join('project p', 'p.id = sj.project_id')
                ->join('client c', 'c.id = p.client_id')
                ->join('invoice i', 'i.id = sj.invoice_id', 'left');
        
        if ($salesId) {
            $builder->where('c.sales_id', $salesId);
        }
        
        $builder->groupStart()
                ->like('sj.nomor_surat_jalan', $keyword)
                ->orLike('p.nama_project', $keyword)
                ->orLike('c.nama_perusahaan', $keyword)
                ->orLike('sj.penerima_nama', $keyword)
                ->orLike('sj.penerima_perusahaan', $keyword)
                ->groupEnd()
                ->orderBy('sj.tanggal_kirim', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    /**
 * Get all surat jalan for current user with role-based filtering
 */
public function getSuratJalanForUser($userData)
{
    $builder = $this->db->table('surat_jalan sj');
    $builder->select('sj.*, p.nama_project, c.nama_perusahaan, 
        i.nomor_invoice, u.name as created_by_name')
        ->join('project p', 'p.id = sj.project_id', 'left')
        ->join('client c', 'c.id = p.client_id', 'left')
        ->join('invoice i', 'i.id = sj.invoice_id', 'left')
        ->join('users u', 'u.id = sj.created_by', 'left')
        ->orderBy('sj.tanggal_kirim', 'DESC');
    
    // Filter berdasarkan role
    if ($userData['role'] !== 'admin' && $userData['role'] !== 'direktur') {
        if (!empty($userData['karyawan_id'])) {
            $builder->where('c.sales_id', $userData['karyawan_id']);
        } else {
            $builder->where('c.sales_id', $userData['id']);
        }
    }
    
    return $builder->get()->getResultArray();
}

/**
 * Get status count for current user
 */
public function getStatusCountForUser($userData)
{
    $builder = $this->db->table('surat_jalan sj');
    $builder->select('sj.status, COUNT(*) as count')
        ->join('project p', 'p.id = sj.project_id')
        ->join('client c', 'c.id = p.client_id');
    
    // Filter berdasarkan role
    if ($userData['role'] !== 'admin' && $userData['role'] !== 'direktur') {
        if (!empty($userData['karyawan_id'])) {
            $builder->where('c.sales_id', $userData['karyawan_id']);
        } else {
            $builder->where('c.sales_id', $userData['id']);
        }
    }
    
    $builder->groupBy('sj.status');
    $result = $builder->get()->getResultArray();
    
    $counts = [
        'draft' => 0,
        'diproses' => 0,
        'dikirim' => 0,
        'diterima' => 0,
        'ditolak' => 0,
        'dibatalkan' => 0
    ];
    
    foreach ($result as $row) {
        if (isset($counts[$row['status']])) {
            $counts[$row['status']] = (int)$row['count'];
        }
    }
    
    return $counts;
}

/**
 * Get today's deliveries for current user
 */
public function getTodayDeliveriesForUser($userData)
{
    $today = date('Y-m-d');
    
    $builder = $this->db->table('surat_jalan sj');
    $builder->select('sj.*, p.nama_project, c.nama_perusahaan, i.nomor_invoice')
        ->join('project p', 'p.id = sj.project_id')
        ->join('client c', 'c.id = p.client_id')
        ->join('invoice i', 'i.id = sj.invoice_id', 'left')
        ->where('sj.tanggal_kirim', $today)
        ->whereIn('sj.status', ['diproses', 'dikirim']);
    
    // Filter berdasarkan role
    if ($userData['role'] !== 'admin' && $userData['role'] !== 'direktur') {
        if (!empty($userData['karyawan_id'])) {
            $builder->where('c.sales_id', $userData['karyawan_id']);
        } else {
            $builder->where('c.sales_id', $userData['id']);
        }
    }
    
    return $builder->get()->getResultArray();
}

}