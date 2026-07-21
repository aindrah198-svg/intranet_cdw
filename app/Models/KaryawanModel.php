<?php

namespace App\Models;

use CodeIgniter\Model;

class KaryawanModel extends Model
{
    protected $table = 'karyawan';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    
    protected $allowedFields = [
        'nik',
        'nama_lengkap',
        'nama_panggilan',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'status_pernikahan',
        'alamat',
        'telepon',
        'email',
        'jabatan',
        'departemen',
        'divisi',
        'tanggal_masuk',
        'status_karyawan',
        'tanggal_keluar',
        'alasan_keluar',
        'no_npwp',
        'no_bpjs_kes',
        'no_bpjs_tk',
        'no_rekening',
        'bank',
        'nama_rekening',
        'pendidikan_terakhir',
        'jurusan',
        'institusi',
        'tahun_lulus',
        'kontak_darurat_nama',
        'kontak_darurat_hubungan',
        'kontak_darurat_telepon',
        'foto',
        'cv_path',
        'shift_id'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    
    // Validation Rules tanpa is_unique di NIK (kita handle manual di controller)
    protected $validationRules = [
        'nik' => 'required|max_length[20]',
        'nama_lengkap' => 'required|max_length[100]',
        'nama_panggilan' => 'max_length[50]',
        'jenis_kelamin' => 'in_list[L,P]',
        'tempat_lahir' => 'max_length[50]',
        'tanggal_lahir' => 'permit_empty|valid_date',
        'agama' => 'max_length[20]',
        'status_pernikahan' => 'in_list[Belum Menikah,Menikah,Janda/Duda]',
        'telepon' => 'max_length[20]',
        'email' => 'permit_empty|valid_email|max_length[100]',
        'jabatan' => 'max_length[50]',
        'departemen' => 'max_length[50]',
        'divisi' => 'max_length[50]',
        'tanggal_masuk' => 'permit_empty|valid_date',
        'status_karyawan' => 'in_list[Tetap,Kontrak,Probation,Magang]',
        'tanggal_keluar' => 'permit_empty|valid_date',
        'no_npwp' => 'max_length[25]',
        'no_bpjs_kes' => 'max_length[20]',
        'no_bpjs_tk' => 'max_length[20]',
        'no_rekening' => 'max_length[30]',
        'bank' => 'max_length[50]',
        'nama_rekening' => 'max_length[100]',
        'pendidikan_terakhir' => 'max_length[50]',
        'jurusan' => 'max_length[100]',
        'institusi' => 'max_length[100]',
        'tahun_lulus' => 'permit_empty|integer|greater_than[1900]|less_than[2100]',
        'kontak_darurat_nama' => 'max_length[100]',
        'kontak_darurat_hubungan' => 'max_length[50]',
        'kontak_darurat_telepon' => 'max_length[20]'
    ];
    
    protected $validationMessages = [
        'nik' => [
            'required' => 'NIK harus diisi',
            'max_length' => 'NIK maksimal 20 karakter'
        ],
        'nama_lengkap' => [
            'required' => 'Nama lengkap harus diisi',
            'max_length' => 'Nama lengkap maksimal 100 karakter'
        ],
        'email' => [
            'valid_email' => 'Format email tidak valid',
            'max_length' => 'Email maksimal 100 karakter'
        ]
    ];
    
    protected $skipValidation = false;
    
    /**
     * Mendapatkan semua data karyawan yang belum dihapus
     */
    public function getAllKaryawan()
    {
        return $this->orderBy('nama_lengkap', 'ASC')->findAll();
    }
    
    /**
     * Mendapatkan data karyawan berdasarkan ID
     */
    public function getKaryawanById($id)
    {
        return $this->where('id', $id)->first();
    }
    
    /**
     * Mendapatkan data karyawan berdasarkan NIK
     */
    public function getKaryawanByNik($nik)
    {
        return $this->where('nik', $nik)->first();
    }
    
    /**
     * Cek apakah NIK sudah terdaftar (untuk ID lain)
     */
    public function isNikExistsForOther($nik, $excludeId)
    {
        return $this->where('nik', $nik)
                    ->where('id !=', $excludeId)
                    ->first();
    }
    
    /**
     * Mencari karyawan berdasarkan nama atau NIK
     */
    public function searchKaryawan($keyword)
    {
        return $this->groupStart()
                    ->like('nik', $keyword)
                    ->orLike('nama_lengkap', $keyword)
                    ->orLike('nama_panggilan', $keyword)
                    ->groupEnd()
                    ->orderBy('nama_lengkap', 'ASC')
                    ->findAll();
    }
    
    /**
     * Mendapatkan karyawan berdasarkan status
     */
    public function getKaryawanByStatus($status)
    {
        return $this->where('status_karyawan', $status)
                    ->orderBy('nama_lengkap', 'ASC')
                    ->findAll();
    }
    
    /**
     * Mendapatkan karyawan berdasarkan departemen
     */
    public function getKaryawanByDepartemen($departemen)
    {
        return $this->where('departemen', $departemen)
                    ->orderBy('nama_lengkap', 'ASC')
                    ->findAll();
    }
    
    /**
     * Mendapatkan karyawan aktif (belum keluar)
     */
    public function getKaryawanAktif()
    {
        return $this->where('tanggal_keluar IS NULL')
                    ->orWhere('tanggal_keluar', '')
                    ->orderBy('nama_lengkap', 'ASC')
                    ->findAll();
    }
    
    /**
     * Mendapatkan karyawan yang sudah keluar
     */
    public function getKaryawanKeluar()
    {
        return $this->where('tanggal_keluar IS NOT NULL')
                    ->where('tanggal_keluar !=', '')
                    ->orderBy('tanggal_keluar', 'DESC')
                    ->findAll();
    }
    
    /**
     * Menghapus karyawan (soft delete)
     */
    public function deleteKaryawan($id)
    {
        return $this->delete($id);
    }
    
    /**
     * Mengupdate data karyawan
     */
    public function updateKaryawan($id, $data)
    {
        return $this->update($id, $data);
    }
    
    /**
     * Mengupdate foto karyawan
     */
    public function updateFoto($id, $fotoPath)
    {
        return $this->update($id, ['foto' => $fotoPath]);
    }
    
    /**
     * Mengupdate CV karyawan
     */
    public function updateCV($id, $cvPath)
    {
        return $this->update($id, ['cv_path' => $cvPath]);
    }
    
    /**
     * Mengupdate data keluar karyawan
     */
    public function updateKeluar($id, $tanggalKeluar, $alasanKeluar)
    {
        $data = [
            'tanggal_keluar' => $tanggalKeluar,
            'alasan_keluar' => $alasanKeluar
        ];
        return $this->update($id, $data);
    }
    
    /**
     * Mendapatkan statistik karyawan
     */
    public function getStatistik()
    {
        $statistik = [
            'total' => $this->countAllResults(),
            'aktif' => $this->where('tanggal_keluar IS NULL')->orWhere('tanggal_keluar', '')->countAllResults(),
            'keluar' => $this->where('tanggal_keluar IS NOT NULL')->where('tanggal_keluar !=', '')->countAllResults(),
            'tetap' => $this->where('status_karyawan', 'Tetap')->countAllResults(),
            'kontrak' => $this->where('status_karyawan', 'Kontrak')->countAllResults(),
            'probation' => $this->where('status_karyawan', 'Probation')->countAllResults(),
            'magang' => $this->where('status_karyawan', 'Magang')->countAllResults()
        ];
        
        return $statistik;
    }
    
    /**
     * Validasi NIK unik (untuk create)
     */
    public function isNikUnique($nik)
    {
        $karyawan = $this->where('nik', $nik)->first();
        return $karyawan ? false : true;
    }
    
    /**
     * Validasi NIK unik untuk update (kecuali untuk ID ini)
     */
    public function isNikUniqueForUpdate($nik, $id)
    {
        $karyawan = $this->where('nik', $nik)->where('id !=', $id)->first();
        return $karyawan ? false : true;
    }

    /**
 * Get karyawan yang belum memiliki akun
 */
public function getKaryawanBelumAkun()
{
    $db = \Config\Database::connect();
    
    $subquery = $db->table('users')
        ->select('karyawan_id')
        ->where('karyawan_id IS NOT NULL')
        ->where('deleted_at', null)
        ->getCompiledSelect();
    
    return $this->select('karyawan.id, karyawan.nik, karyawan.nama_lengkap, karyawan.jabatan, karyawan.departemen, karyawan.email')
                ->where("karyawan.id NOT IN ($subquery)", null, false)
                ->where('karyawan.deleted_at', null)
                ->where('karyawan.tanggal_keluar', null) // Hanya karyawan aktif
                ->orderBy('karyawan.nama_lengkap', 'ASC')
                ->findAll();
}

/**
 * Get karyawan by ID with user account info
 */
public function getKaryawanWithAccount($id)
{
    $this->select('karyawan.*, users.username, users.email as user_email, users.role, users.status as user_status');
    $this->join('users', 'users.karyawan_id = karyawan.id AND users.deleted_at IS NULL', 'left');
    $this->where('karyawan.id', $id);
    
    return $this->first();
}


}