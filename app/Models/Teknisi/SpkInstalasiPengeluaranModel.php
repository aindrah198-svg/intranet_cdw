<?php

namespace App\Models\Teknisi;

use CodeIgniter\Model;

class SpkInstalasiPengeluaranModel extends Model
{
    protected $table = 'spk_instalasi_pengeluaran';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'spk_id', 'no_ref', 'nama_pengeluaran', 'jenis', 'deskripsi', 
        'jumlah', 'tanggal', 'foto_nota', 'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get pengeluaran by SPK ID
     */
    public function getBySpkId($spk_id)
    {
        return $this->select('spk_instalasi_pengeluaran.*, users.name as created_by_nama')
            ->join('users', 'users.id = spk_instalasi_pengeluaran.created_by', 'left')
            ->where('spk_id', $spk_id)
            ->orderBy('tanggal', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get total pengeluaran per SPK
     */
    public function getTotalBySpkId($spk_id)
    {
        return $this->selectSum('jumlah')
            ->where('spk_id', $spk_id)
            ->get()
            ->getRow()
            ->jumlah ?? 0;
    }

    /**
     * Get pengeluaran by jenis
     */
    public function getByJenis($spk_id, $jenis)
    {
        return $this->where('spk_id', $spk_id)
            ->where('jenis', $jenis)
            ->orderBy('tanggal', 'DESC')
            ->findAll();
    }

    /**
     * Get pengeluaran by tanggal range
     */
    public function getByTanggalRange($spk_id, $tanggal_awal, $tanggal_akhir)
    {
        return $this->where('spk_id', $spk_id)
            ->where('tanggal >=', $tanggal_awal)
            ->where('tanggal <=', $tanggal_akhir)
            ->orderBy('tanggal', 'ASC')
            ->findAll();
    }
}