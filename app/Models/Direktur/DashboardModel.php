<?php
namespace App\Models\Direktur;

use CodeIgniter\Model;

class DashboardModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'karyawan';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [];
    
    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    
    public function getKaryawanData()
    {
        // Untuk sementara return data dummy
        // Nanti jika sudah ada database, ganti dengan query yang sesuai
        
        $karyawan = session()->get('karyawan');
        if ($karyawan) {
            return $karyawan;
        }
        
        // Data dummy untuk testing
        return [
            'id' => 1,
            'nama' => 'Ir. Ahmad Budiman',
            'nama_panggilan' => 'Ahmad',
            'jabatan' => 'Direktur Utama',
            'divisi' => 'Eksekutif',
            'email' => 'ahmad.budiman@cdw-engineering.com',
            'no_hp' => '081234567890',
            'foto' => null
        ];
    }
    
    public function getTotalKaryawan()
    {
        // Query ke database
        // return $this->where('deleted_at', null)->countAllResults();
        return 156; // Dummy
    }
    
    public function getTotalProyekAktif()
    {
        return 48; // Dummy
    }
    
    public function getTotalPendapatanBulanIni()
    {
        return 5800000000; // Dummy
    }
    
    public function getTotalPengeluaranBulanIni()
    {
        return 3800000000; // Dummy
    }
}