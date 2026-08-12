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
        $db = \Config\Database::connect();
        
        $clientSelect = $db->fieldExists('client_nama', 'spk_instalasi') ? 'spk_instalasi.client_nama' : 'COALESCE(client.nama_perusahaan, "-") as client_nama';
        $targetSelesaiCol = $db->fieldExists('target_selesai', 'spk_instalasi') ? 'spk_instalasi.target_selesai' : ($db->fieldExists('tanggal_selesai', 'spk_instalasi') ? 'spk_instalasi.tanggal_selesai' : 'spk_instalasi.tanggal_mulai');
        $selesaiAktualCol = $db->fieldExists('tanggal_selesai_aktual', 'spk_instalasi') ? 'spk_instalasi.tanggal_selesai_aktual' : 'NULL';
        $progressCol = $db->fieldExists('progress_persen', 'spk_instalasi') ? 'spk_instalasi.progress_persen' : '0';
        $prioritasCol = $db->fieldExists('prioritas', 'spk_instalasi') ? 'spk_instalasi.prioritas' : '"Normal" as prioritas';
        $statusCol = $db->fieldExists('status', 'spk_instalasi') ? 'spk_instalasi.status' : '"Dijadwalkan" as status';

        return $this->select('
                spk_instalasi.id,
                spk_instalasi.nomor_spk,
                spk_instalasi.judul_pekerjaan,
                ' . $clientSelect . ',
                spk_instalasi.tanggal_mulai,
                ' . $targetSelesaiCol . ' as tanggal_selesai,
                ' . $selesaiAktualCol . ' as tanggal_selesai_aktual,
                ' . $progressCol . ' as progress_persen,
                ' . $prioritasCol . ',
                ' . $statusCol . ',
                DATEDIFF(' . $targetSelesaiCol . ', spk_instalasi.tanggal_mulai) as durasi_hari,
                CASE 
                    WHEN ' . $selesaiAktualCol . ' IS NOT NULL 
                    THEN DATEDIFF(' . $selesaiAktualCol . ', spk_instalasi.tanggal_mulai)
                    ELSE DATEDIFF(CURDATE(), spk_instalasi.tanggal_mulai)
                END as hari_berjalan
            ')
            ->join('client', 'client.id = spk_instalasi.client_id', 'left')
            ->where('YEAR(spk_instalasi.tanggal_mulai)', $tahun)
            ->where('spk_instalasi.deleted_at IS NULL')
            ->orderBy('spk_instalasi.tanggal_mulai', 'ASC')
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