<?php
// C:\xampp\htdocs\cdwnet\app\Models\KontrakModel.php

namespace App\Models;

use CodeIgniter\Model;

class KontrakModel extends Model
{
    protected $table = 'kontrak';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'karyawan_id', 'nomor_kontrak', 'jenis_kontrak', 'jabatan', 
        'lokasi_kerja', 'tanggal_mulai', 'tanggal_selesai', 
        'masa_kerja_bulan', 'masa_percobaan_bulan', 'pemberitahuan_pemutusan_hari',
        'gaji_pokok', 'tunjangan_bpjs', 'tunjangan_makan_lokal',
        'tunjangan_makan_luar_jawa', 'reimburse_transport', 'reimburse_entertaint',
        'tunjangan_penginapan_max', 'hak_cuti_setelah_tahun', 'jumlah_cuti_tahunan_hari',
        'cuti_bersama_disesuaikan', 'target_penjualan_bulanan', 'komisi_aturan',
        'lampiran_path', 'pihak_pertama_nama', 'pihak_pertama_jabatan',
        'pihak_pertama_alamat', 'pihak_kedua_nama', 'pihak_kedua_jabatan',
        'pihak_kedua_alamat', 'status', 'alasan_berakhir'
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    
    // Validation rules
    protected $validationRules = [
        'karyawan_id' => 'required|numeric',
        'nomor_kontrak' => 'required|is_unique[kontrak.nomor_kontrak,id,{id}]',
        'jenis_kontrak' => 'required|in_list[Probation,Kontrak,Tetap,Magang]',
        'jabatan' => 'required|max_length[100]',
        'tanggal_mulai' => 'required|valid_date',
        'status' => 'required|in_list[Draft,Aktif,Selesai,Diperpanjang,Diputus]'
    ];
    
    protected $validationMessages = [
        'nomor_kontrak' => [
            'is_unique' => 'Nomor kontrak sudah digunakan'
        ]
    ];
    
    protected $skipValidation = false;
    
    /**
     * Get all kontrak with karyawan data
     */
    public function getAllWithKaryawan($limit = null, $offset = 0, $search = null, $jenis = null, $status = null)
    {
        $builder = $this->db->table($this->table . ' as k')
            ->select('k.*, kr.nik, kr.nama_lengkap, kr.jabatan as jabatan_karyawan, kr.status_karyawan')
            ->join('karyawan as kr', 'kr.id = k.karyawan_id', 'left')
            ->where('k.deleted_at IS NULL');
        
        // Filter by search
        if ($search) {
            $builder->groupStart()
                ->like('kr.nama_lengkap', $search)
                ->orLike('kr.nik', $search)
                ->orLike('k.nomor_kontrak', $search)
                ->orLike('k.jabatan', $search)
                ->groupEnd();
        }
        
        // Filter by jenis kontrak
        if ($jenis && $jenis != 'all') {
            $builder->where('k.jenis_kontrak', $jenis);
        }
        
        // Filter by status
        if ($status && $status != 'all') {
            $builder->where('k.status', $status);
        }
        
        // Order by
        $builder->orderBy('k.tanggal_mulai', 'DESC')
                ->orderBy('k.created_at', 'DESC');
        
        // Limit and offset
        if ($limit) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get kontrak by karyawan ID
     */
    public function getByKaryawanId($karyawanId)
    {
        return $this->db->table($this->table . ' as k')
            ->select('k.*, kr.nik, kr.nama_lengkap')
            ->join('karyawan as kr', 'kr.id = k.karyawan_id', 'left')
            ->where('k.karyawan_id', $karyawanId)
            ->where('k.deleted_at IS NULL')
            ->orderBy('k.tanggal_mulai', 'DESC')
            ->get()
            ->getResultArray();
    }
    
    /**
     * Get single kontrak with karyawan data
     */
    public function getWithKaryawan($id)
    {
        return $this->db->table($this->table . ' as k')
            ->select('k.*, kr.nik, kr.nama_lengkap, kr.email, kr.telepon, kr.alamat, kr.tanggal_masuk')
            ->join('karyawan as kr', 'kr.id = k.karyawan_id', 'left')
            ->where('k.id', $id)
            ->where('k.deleted_at IS NULL')
            ->get()
            ->getRowArray();
    }
    
    /**
     * Get statistics
     */
    public function getStats()
    {
        $total = $this->where('deleted_at IS NULL')->countAllResults();
        $aktif = $this->where('status', 'Aktif')->where('deleted_at IS NULL')->countAllResults();
        $selesai = $this->where('status', 'Selesai')->where('deleted_at IS NULL')->countAllResults();
        $draft = $this->where('status', 'Draft')->where('deleted_at IS NULL')->countAllResults();
        
        // Get by jenis
        $jenisStats = $this->db->table($this->table)
            ->select('jenis_kontrak, COUNT(*) as total')
            ->where('deleted_at IS NULL')
            ->groupBy('jenis_kontrak')
            ->get()
            ->getResultArray();
        
        return [
            'total' => $total,
            'aktif' => $aktif,
            'selesai' => $selesai,
            'draft' => $draft,
            'jenis' => $jenisStats
        ];
    }
    
    /**
     * Get kontrak that will expire soon (within 30 days)
     */
    public function getExpiringSoon()
    {
        $today = date('Y-m-d');
        $futureDate = date('Y-m-d', strtotime('+30 days'));
        
        return $this->db->table($this->table . ' as k')
            ->select('k.*, kr.nik, kr.nama_lengkap')
            ->join('karyawan as kr', 'kr.id = k.karyawan_id', 'left')
            ->where('k.status', 'Aktif')
            ->where('k.deleted_at IS NULL')
            ->where('k.tanggal_selesai >=', $today)
            ->where('k.tanggal_selesai <=', $futureDate)
            ->orderBy('k.tanggal_selesai', 'ASC')
            ->get()
            ->getResultArray();
    }
    
    /**
     * Check if kontrak number exists
     */
    public function isNomorKontrakExist($nomorKontrak, $excludeId = null)
    {
        $builder = $this->where('nomor_kontrak', $nomorKontrak)
            ->where('deleted_at IS NULL');
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }
}