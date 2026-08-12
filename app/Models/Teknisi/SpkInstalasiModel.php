<?php

namespace App\Models\Teknisi;

use CodeIgniter\Model;

class SpkInstalasiModel extends Model
{
    protected $table = 'spk_instalasi';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'nomor_spk',
        'judul_pekerjaan',
        'deskripsi',
        'lokasi',
        'client_id',
        // TAMBAHKAN field-field client berikut
        'client_nama',
        'client_alamat',
        'client_kontak',
        'catatan_client',
        'tanggal_mulai',
        'tanggal_selesai',
        'target_selesai',
        'tanggal_selesai_aktual',
        'prioritas',
        'status',
        'kategori_pekerjaan',
        'tim_teknisi',
        'project_manager_id',
        'catatan',
        'laporan',
        'estimasi_biaya',
        'biaya_aktual',
        'progress_persen',
        'dokumen_pendukung',
        'dokumentasi',
        'foto_sebelum',
        'foto_sesudah',
        'laporan_hasil',
        'dibuat_oleh',
        'dibuat_tanggal',
        'diperbarui_oleh',
        'diperbarui_tanggal'
    ];

    // Nonaktifkan timestamps otomatis karena kita menggunakan field manual
    protected $useTimestamps = false;
    
    // Jika ingin menggunakan soft deletes, pastikan kolom deleted_at ada
    protected $deletedField = 'deleted_at';

    // Validation rules
    protected $validationRules = [
        'nomor_spk' => 'required|is_unique[spk_instalasi.nomor_spk,id,{id}]',
        'judul_pekerjaan' => 'required',
        'tanggal_mulai' => 'required|valid_date',
        'prioritas' => 'required',
        'client_id' => 'required|numeric'
    ];

    protected $validationMessages = [
        'nomor_spk' => [
            'required' => 'Nomor SPK wajib diisi',
            'is_unique' => 'Nomor SPK sudah digunakan'
        ],
        'judul_pekerjaan' => [
            'required' => 'Judul pekerjaan wajib diisi'
        ],
        'tanggal_mulai' => [
            'required' => 'Tanggal mulai wajib diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'prioritas' => [
            'required' => 'Prioritas wajib dipilih'
        ],
        'client_id' => [
            'required' => 'Client wajib dipilih',
            'numeric' => 'ID client tidak valid'
        ]
    ];

    /**
     * Get filtered SPK list dengan data client
     */
    public function getFiltered($status = null, $prioritas = null, $tanggal_mulai = null, $tanggal_selesai = null)
    {
        $db = \Config\Database::connect();
        $joinUser1 = $db->fieldExists('dibuat_oleh', 'spk_instalasi') ? 'users.id = spk_instalasi.dibuat_oleh' : ($db->fieldExists('created_by', 'spk_instalasi') ? 'users.id = spk_instalasi.created_by' : '1=0');
        $joinUser2 = $db->fieldExists('diperbarui_oleh', 'spk_instalasi') ? 'u2.id = spk_instalasi.diperbarui_oleh' : '1=0';

        $builder = $this->select('
                spk_instalasi.*, 
                users.name as dibuat_oleh_nama, 
                u2.name as diperbarui_oleh_nama,
                client.nama_perusahaan as client_nama_tabel, 
                client.alamat as client_alamat_tabel, 
                client.telepon as client_telepon, 
                client.email_client as client_email,
                client.nama_kontak as client_kontak_nama,
                client.client_kontak as client_info_tambahan,
                client.catatan_client as client_catatan,
                client.kategori as client_kategori,
                client.status as client_status,
                client.kode_client as client_kode
            ')
            ->join('users', $joinUser1, 'left')
            ->join('users as u2', $joinUser2, 'left')
            ->join('client', 'client.id = spk_instalasi.client_id', 'left');
        
        if ($status && $status != 'semua') {
            $builder->where('spk_instalasi.status', $status);
        }
        
        if ($prioritas && $prioritas != 'semua') {
            $builder->where('spk_instalasi.prioritas', $prioritas);
        }
        
        if ($tanggal_mulai) {
            $builder->where('spk_instalasi.tanggal_mulai >=', $tanggal_mulai);
        }
        
        if ($tanggal_selesai) {
            $builder->where('spk_instalasi.tanggal_mulai <=', $tanggal_selesai);
        }
        
        return $builder->orderBy('spk_instalasi.tanggal_mulai', 'DESC')
            ->orderBy('spk_instalasi.id', 'DESC')
            ->findAll();
    }

    /**
     * Get SPK with relations (creator, updater, client)
     */
    public function getWithRelations($id)
    {
        $db = \Config\Database::connect();
        $joinUser1 = $db->fieldExists('dibuat_oleh', 'spk_instalasi') ? 'users.id = spk_instalasi.dibuat_oleh' : ($db->fieldExists('created_by', 'spk_instalasi') ? 'users.id = spk_instalasi.created_by' : '1=0');
        $joinUser2 = $db->fieldExists('diperbarui_oleh', 'spk_instalasi') ? 'u2.id = spk_instalasi.diperbarui_oleh' : '1=0';

        return $this->select('
                spk_instalasi.*, 
                users.name as dibuat_oleh_nama, 
                u2.name as diperbarui_oleh_nama,
                client.nama_perusahaan as client_nama_tabel,
                client.alamat as client_alamat_tabel,
                client.telepon as client_telepon,
                client.email_client as client_email,
                client.nama_kontak as client_kontak_nama,
                client.client_kontak as client_info_tambahan,
                client.catatan_client as client_catatan,
                client.kategori as client_kategori,
                client.status as client_status,
                client.kode_client as client_kode
            ')
            ->join('users', $joinUser1, 'left')
            ->join('users as u2', $joinUser2, 'left')
            ->join('client', 'client.id = spk_instalasi.client_id', 'left')
            ->find($id);
    }

    /**
     * Get SPK dengan data client lengkap
     */
    public function getWithClient($id)
    {
        return $this->select('
                spk_instalasi.*, 
                client.*, 
                client.id as client_id,
                client.nama_perusahaan as client_nama, 
                client.alamat as client_alamat, 
                client.telepon as client_telepon, 
                client.email_client as client_email,
                client.nama_kontak as client_kontak_nama,
                client.client_kontak as client_info_tambahan,
                client.catatan_client as client_catatan
            ')
            ->join('client', 'client.id = spk_instalasi.client_id', 'left')
            ->find($id);
    }

    /**
     * Override insert method untuk memastikan data client ikut tersimpan
     */
    public function insert($data = null, bool $returnID = true)
    {
        // Jika ada client_id, ambil data client dari tabel client
        if (isset($data['client_id']) && !empty($data['client_id'])) {
            $clientData = $this->getClientData($data['client_id']);
            if ($clientData) {
                // Isi field-field client dengan data dari tabel client
                $data['client_nama'] = $clientData['nama_perusahaan'] ?? null;
                $data['client_alamat'] = $clientData['alamat'] ?? null;
                $data['client_kontak'] = $clientData['client_kontak'] ?? $clientData['telepon'] ?? null;
                $data['catatan_client'] = $clientData['catatan_client'] ?? null;
            }
        }

        return parent::insert($data, $returnID);
    }

    /**
     * Override update method untuk memastikan data client ikut tersimpan
     */
    public function update($id = null, $data = null): bool
    {
        // Jika ada client_id, ambil data client dari tabel client
        if (isset($data['client_id']) && !empty($data['client_id'])) {
            $clientData = $this->getClientData($data['client_id']);
            if ($clientData) {
                // Isi field-field client dengan data dari tabel client
                $data['client_nama'] = $clientData['nama_perusahaan'] ?? null;
                $data['client_alamat'] = $clientData['alamat'] ?? null;
                $data['client_kontak'] = $clientData['client_kontak'] ?? $clientData['telepon'] ?? null;
                $data['catatan_client'] = $clientData['catatan_client'] ?? null;
            }
        }

        return parent::update($id, $data);
    }

    /**
     * Ambil data client dari tabel client
     */
    private function getClientData($client_id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('client');
        $builder->select('nama_perusahaan, alamat, telepon, client_kontak, catatan_client');
        $builder->where('id', $client_id);
        $result = $builder->get()->getRowArray();
        
        return $result;
    }

    /**
     * Update progress SPK
     */
    public function updateProgress($id, $progress, $user_id)
    {
        $data = [
            'progress_persen' => $progress,
            'diperbarui_oleh' => $user_id,
            'diperbarui_tanggal' => date('Y-m-d H:i:s')
        ];

        // Jika progress 100%, otomatis selesai
        if ($progress >= 100) {
            $data['status'] = 'Selesai';
            $data['tanggal_selesai_aktual'] = date('Y-m-d');
        }

        return $this->update($id, $data);
    }

    /**
     * Selesaikan SPK
     */
    public function selesaikan($id, $user_id, $laporan = null)
    {
        $data = [
            'status' => 'Selesai',
            'tanggal_selesai_aktual' => date('Y-m-d'),
            'progress_persen' => 100,
            'diperbarui_oleh' => $user_id,
            'diperbarui_tanggal' => date('Y-m-d H:i:s')
        ];
        
        if ($laporan) {
            $data['laporan_hasil'] = $laporan;
        }
        
        return $this->update($id, $data);
    }

    /**
     * Batalkan SPK
     */
    public function batalkan($id, $user_id, $alasan = null)
    {
        $data = [
            'status' => 'Dibatalkan',
            'diperbarui_oleh' => $user_id,
            'diperbarui_tanggal' => date('Y-m-d H:i:s'),
            'catatan' => $alasan ? 'Dibatalkan: ' . $alasan : 'Dibatalkan'
        ];
        
        return $this->update($id, $data);
    }

    /**
     * Tunda SPK
     */
    public function tunda($id, $user_id, $alasan = null)
    {
        $data = [
            'status' => 'Ditunda',
            'diperbarui_oleh' => $user_id,
            'diperbarui_tanggal' => date('Y-m-d H:i:s'),
            'catatan' => $alasan ? 'Ditunda: ' . $alasan : 'Ditunda'
        ];
        
        return $this->update($id, $data);
    }

    /**
     * Generate nomor SPK otomatis
     */
    public function generateNomorSpk()
    {
        $tahun = date('Y');
        $bulan = date('m');
        $tanggal = date('d');
        
        // Cari nomor SPK terakhir di tanggal ini
        $lastSpk = $this->like('nomor_spk', "SPK-{$tahun}{$bulan}{$tanggal}-", 'after')
            ->orderBy('id', 'DESC')
            ->first();
        
        if ($lastSpk && isset($lastSpk->nomor_spk)) {
            $lastNumber = (int) substr($lastSpk->nomor_spk, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }
        
        return "SPK-{$tahun}{$bulan}{$tanggal}-{$newNumber}";
    }

    /**
     * Get statistik SPK dengan data client
     */
    public function getStatistik()
    {
        $db = \Config\Database::connect();
        $estimasiBiayaSelect = $db->fieldExists('estimasi_biaya', 'spk_instalasi') ? 'SUM(spk_instalasi.estimasi_biaya)' : '0';
        $progressSelect = $db->fieldExists('progress_persen', 'spk_instalasi') ? 'AVG(spk_instalasi.progress_persen)' : '0';

        $builder = $this->select('
                COUNT(spk_instalasi.id) as total,
                SUM(CASE WHEN spk_instalasi.status = "Draft" THEN 1 ELSE 0 END) as draft,
                SUM(CASE WHEN spk_instalasi.status = "Dijadwalkan" THEN 1 ELSE 0 END) as dijadwalkan,
                SUM(CASE WHEN spk_instalasi.status = "Dalam Pengerjaan" THEN 1 ELSE 0 END) as dalam_pengerjaan,
                SUM(CASE WHEN spk_instalasi.status = "Selesai" THEN 1 ELSE 0 END) as selesai,
                SUM(CASE WHEN spk_instalasi.status = "Ditunda" THEN 1 ELSE 0 END) as ditunda,
                SUM(CASE WHEN spk_instalasi.status = "Dibatalkan" THEN 1 ELSE 0 END) as dibatalkan,
                ' . $estimasiBiayaSelect . ' as total_estimasi,
                ' . $progressSelect . ' as total_progress
            ')
            ->join('client', 'client.id = spk_instalasi.client_id', 'left');
            
        $result = $builder->get()->getRow();
        
        if ($result) {
            return [
                'total' => (int) $result->total,
                'draft' => (int) $result->draft,
                'dijadwalkan' => (int) $result->dijadwalkan,
                'dalam_pengerjaan' => (int) $result->dalam_pengerjaan,
                'selesai' => (int) $result->selesai,
                'ditunda' => (int) $result->ditunda,
                'dibatalkan' => (int) $result->dibatalkan,
                'total_estimasi' => (float) $result->total_estimasi,
                'total_progress' => (float) $result->total_progress
            ];
        }
        
        return [
            'total' => 0,
            'draft' => 0,
            'dijadwalkan' => 0,
            'dalam_pengerjaan' => 0,
            'selesai' => 0,
            'ditunda' => 0,
            'dibatalkan' => 0,
            'total_estimasi' => 0,
            'total_progress' => 0
        ];
    }

    /**
     * Get SPK by client ID
     */
    public function getByClientId($client_id)
    {
        return $this->select('spk_instalasi.*, client.nama_perusahaan as client_nama_tabel')
            ->join('client', 'client.id = spk_instalasi.client_id', 'left')
            ->where('spk_instalasi.client_id', $client_id)
            ->orderBy('spk_instalasi.tanggal_mulai', 'DESC')
            ->findAll();
    }

    /**
     * Get daftar client untuk dropdown
     */
    public function getClientDropdown()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('client');
        $builder->select('id, nama_perusahaan, kode_client');
        $builder->where('status', 'active');
        $builder->orderBy('nama_perusahaan', 'ASC');
        $result = $builder->get()->getResult();
        
        $options = [];
        foreach ($result as $row) {
            $options[$row->id] = $row->nama_perusahaan . ' (' . $row->kode_client . ')';
        }
        
        return $options;
    }

    /**
     * Method baru: Update data client di SPK berdasarkan client_id
     */
    public function syncClientData($spk_id, $client_id)
    {
        $clientData = $this->getClientData($client_id);
        if ($clientData) {
            $data = [
                'client_nama' => $clientData['nama_perusahaan'] ?? null,
                'client_alamat' => $clientData['alamat'] ?? null,
                'client_kontak' => $clientData['client_kontak'] ?? $clientData['telepon'] ?? null,
                'catatan_client' => $clientData['catatan_client'] ?? null
            ];
            
            return $this->update($spk_id, $data);
        }
        
        return false;
    }
}