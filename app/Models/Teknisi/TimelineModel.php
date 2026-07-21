<?php

namespace App\Models\Teknisi;

use CodeIgniter\Model;

class TimelineModel extends Model
{
    protected $table = 'spk_instalasi';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'nomor_spk', 'judul_pekerjaan', 'client_nama', 'tanggal_mulai', 
        'target_selesai', 'tanggal_selesai_aktual', 'progress_persen', 
        'prioritas', 'status', 'estimasi_biaya', 'biaya_aktual'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    /**
     * Get timeline data untuk Gantt Chart sederhana
     */
    public function getTimelineData($tahun = null)
    {
        if (!$tahun) $tahun = date('Y');
        
        return $this->select('
                id,
                nomor_spk,
                judul_pekerjaan,
                client_nama,
                tanggal_mulai,
                target_selesai as tanggal_selesai,
                tanggal_selesai_aktual,
                progress_persen,
                prioritas,
                status,
                DATEDIFF(target_selesai, tanggal_mulai) as durasi_hari,
                CASE 
                    WHEN tanggal_selesai_aktual IS NOT NULL 
                    THEN DATEDIFF(tanggal_selesai_aktual, tanggal_mulai)
                    ELSE DATEDIFF(CURDATE(), tanggal_mulai)
                END as hari_berjalan
            ')
            ->where('YEAR(tanggal_mulai)', $tahun)
            ->where('deleted_at IS NULL')
            ->orderBy('tanggal_mulai', 'ASC')
            ->findAll();
    }

    /**
     * Get available years
     */
    public function getAvailableYears()
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT DISTINCT YEAR(tanggal_mulai) as tahun
            FROM spk_instalasi
            WHERE deleted_at IS NULL
            ORDER BY tahun DESC
        ");
        
        $results = $query->getResult();
        
        $years = [];
        foreach ($results as $row) {
            $years[] = $row->tahun;
        }
        
        if (empty($years)) {
            $years[] = date('Y');
        }
        
        return $years;
    }
}