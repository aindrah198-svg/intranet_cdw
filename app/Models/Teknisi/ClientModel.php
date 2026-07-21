<?php

namespace App\Models\Teknisi;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table = 'client';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'kode_client',
        'nama_perusahaan',
        'nama_kontak',
        'email_client',
        'telepon',
        'alamat',
        'client_alamat',
        'client_kontak',
        'catatan_client',
        'keperluan_client',
        'kategori',
        'status',
        'karyawan_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Nonaktifkan timestamps otomatis karena kita menggunakan field manual
    protected $useTimestamps = false;
    
    // Soft deletes
    protected $deletedField = 'deleted_at';

    // Validation rules
    protected $validationRules = [
        'kode_client' => 'required|is_unique[client.kode_client,id,{id}]',
        'nama_perusahaan' => 'required',
        'email_client' => 'permit_empty|valid_email',
        'kategori' => 'permit_empty|in_list[perusahaan,pemerintah,perorangan]',
        'status' => 'permit_empty|in_list[active,inactive,potensial]'
    ];

    protected $validationMessages = [
        'kode_client' => [
            'required' => 'Kode client wajib diisi',
            'is_unique' => 'Kode client sudah digunakan'
        ],
        'nama_perusahaan' => [
            'required' => 'Nama perusahaan/client wajib diisi'
        ],
        'email_client' => [
            'valid_email' => 'Format email tidak valid'
        ],
        'kategori' => [
            'in_list' => 'Kategori harus perusahaan, pemerintah, atau perorangan'
        ],
        'status' => [
            'in_list' => 'Status harus active, inactive, atau potensial'
        ]
    ];

    /**
     * Get filtered client list
     */
    public function getFiltered($kategori = null, $status = null, $search = null)
    {
        $builder = $this->select('client.*, karyawan.nama_lengkap as karyawan_nama, karyawan.jabatan as karyawan_jabatan')
            ->join('karyawan', 'karyawan.id = client.karyawan_id', 'left');
        
        if ($kategori && $kategori != 'semua') {
            $builder->where('client.kategori', $kategori);
        }
        
        if ($status && $status != 'semua') {
            $builder->where('client.status', $status);
        }
        
        if ($search) {
            $builder->groupStart()
                ->like('client.nama_perusahaan', $search)
                ->orLike('client.kode_client', $search)
                ->orLike('client.nama_kontak', $search)
                ->orLike('client.email_client', $search)
                ->orLike('client.telepon', $search)
            ->groupEnd();
        }
        
        return $builder->orderBy('client.created_at', 'DESC')
            ->orderBy('client.id', 'DESC')
            ->findAll();
    }

    /**
     * Get client with relations
     */
    public function getWithRelations($id)
    {
        $result = $this->select('client.*, karyawan.nama_lengkap as karyawan_nama, karyawan.jabatan as karyawan_jabatan')
            ->join('karyawan', 'karyawan.id = client.karyawan_id', 'left')
            ->find($id);
        
        // Pastikan mengembalikan object
        if (is_array($result)) {
            return (object) $result;
        }
        
        return $result;
    }

    /**
     * Get active clients for dropdown
     */
    public function getActiveClients()
    {
        return $this->where('status', 'active')
            ->orderBy('nama_perusahaan', 'ASC')
            ->findAll();
    }

    /**
     * Get clients by kategori
     */
    public function getByKategori($kategori)
    {
        return $this->where('kategori', $kategori)
            ->where('status', 'active')
            ->orderBy('nama_perusahaan', 'ASC')
            ->findAll();
    }

    /**
     * Generate kode client otomatis
     * Format: CLT-YYYYMMDD-XXXX (contoh: CLT-20240224-0001)
     */
    public function generateKodeClient()
    {
        $tahun = date('Y');
        $bulan = date('m');
        $tanggal = date('d');
        
        // Cari kode client terakhir di tanggal ini
        $lastClient = $this->like('kode_client', "CLT-{$tahun}{$bulan}{$tanggal}-", 'after')
            ->orderBy('id', 'DESC')
            ->first();
        
        if ($lastClient && isset($lastClient->kode_client)) {
            $lastNumber = (int) substr($lastClient->kode_client, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }
        
        return "CLT-{$tahun}{$bulan}{$tanggal}-{$newNumber}";
    }

    /**
     * Get statistik client
     */
    public function getStatistik()
    {
        $data = [
            'total' => $this->countAll(),
            'active' => $this->where('status', 'active')->countAllResults(),
            'inactive' => $this->where('status', 'inactive')->countAllResults(),
            'potensial' => $this->where('status', 'potensial')->countAllResults(),
            'perusahaan' => $this->where('kategori', 'perusahaan')->countAllResults(),
            'pemerintah' => $this->where('kategori', 'pemerintah')->countAllResults(),
            'perorangan' => $this->where('kategori', 'perorangan')->countAllResults(),
            'dengan_karyawan' => $this->where('karyawan_id IS NOT NULL')->countAllResults(),
            'tanpa_karyawan' => $this->where('karyawan_id IS NULL')->countAllResults()
        ];
        
        return $data;
    }

    /**
     * Search clients for autocomplete
     */
    public function searchForAutocomplete($term, $limit = 10)
    {
        return $this->select('id, kode_client, nama_perusahaan, nama_kontak, email_client, telepon')
            ->groupStart()
                ->like('nama_perusahaan', $term)
                ->orLike('kode_client', $term)
                ->orLike('nama_kontak', $term)
                ->orLike('email_client', $term)
            ->groupEnd()
            ->where('status', 'active')
            ->orderBy('nama_perusahaan', 'ASC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Check if client has related data (SPK, Project, etc)
     */
    public function hasRelatedData($id)
    {
        $db = \Config\Database::connect();
        
        // Cek di tabel spk_instalasi
        $spkCount = $db->table('spk_instalasi')
            ->where('client_id', $id)
            ->countAllResults();
        
        if ($spkCount > 0) {
            return true;
        }
        
        // Cek di tabel project
        $projectCount = $db->table('project')
            ->where('client_id', $id)
            ->countAllResults();
        
        if ($projectCount > 0) {
            return true;
        }
        
        return false;
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus($ids, $status)
    {
        if (empty($ids)) {
            return false;
        }
        
        return $this->whereIn('id', $ids)
            ->set(['status' => $status, 'updated_at' => date('Y-m-d H:i:s')])
            ->update();
    }

    /**
     * Get clients by karyawan_id
     */
    public function getByKaryawanId($karyawan_id)
    {
        return $this->where('karyawan_id', $karyawan_id)
            ->orderBy('nama_perusahaan', 'ASC')
            ->findAll();
    }

    /**
     * Get recent clients
     */
    public function getRecent($limit = 10)
    {
        return $this->select('client.*, karyawan.nama_lengkap as karyawan_nama')
            ->join('karyawan', 'karyawan.id = client.karyawan_id', 'left')
            ->orderBy('client.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Export data untuk reporting
     */
    public function getForExport($kategori = null, $status = null, $start_date = null, $end_date = null)
    {
        $builder = $this->select('
            client.kode_client,
            client.nama_perusahaan,
            client.nama_kontak,
            client.email_client,
            client.telepon,
            client.alamat,
            client.client_alamat,
            client.client_kontak,
            client.catatan_client,
            client.keperluan_client,
            client.kategori,
            client.status,
            karyawan.nama_lengkap as karyawan_nama,
            karyawan.jabatan as karyawan_jabatan,
            client.created_at,
            client.updated_at
        ')
        ->join('karyawan', 'karyawan.id = client.karyawan_id', 'left');
        
        if ($kategori && $kategori != 'semua') {
            $builder->where('client.kategori', $kategori);
        }
        
        if ($status && $status != 'semua') {
            $builder->where('client.status', $status);
        }
        
        if ($start_date) {
            $builder->where('client.created_at >=', $start_date . ' 00:00:00');
        }
        
        if ($end_date) {
            $builder->where('client.created_at <=', $end_date . ' 23:59:59');
        }
        
        return $builder->orderBy('client.nama_perusahaan', 'ASC')
            ->findAll();
    }
}