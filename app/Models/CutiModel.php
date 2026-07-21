<?php
// app/Models/CutiModel.php

namespace App\Models;

use CodeIgniter\Model;

class CutiModel extends Model
{
    protected $table = 'cuti';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'karyawan_id', 'nomor_cuti', 'jenis_cuti', 'alasan',
        'tanggal_mulai', 'tanggal_selesai', 'lama_hari',
        'sisa_cuti_tahunan', 'status', 'disetujui_oleh',
        'disetujui_at', 'alasan_penolakan', 'tanggal_pengajuan'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'karyawan_id' => 'required|numeric',
        'jenis_cuti' => 'required|in_list[Tahunan,Hamil,Sakit,Khusus,Lainnya]',
        'alasan' => 'required|min_length[10]',
        'tanggal_mulai' => 'required|valid_date',
        'tanggal_selesai' => 'required|valid_date',
        'lama_hari' => 'required|numeric|greater_than[0]',
        'status' => 'permit_empty|in_list[Draft,Menunggu,Disetujui HRD,Disetujui Atasan,Ditolak,Dibatalkan]'
    ];

    protected $validationMessages = [
        'karyawan_id' => [
            'required' => 'Karyawan harus dipilih',
            'numeric' => 'Karyawan tidak valid'
        ],
        'tanggal_mulai' => [
            'valid_date' => 'Tanggal mulai tidak valid'
        ],
        'tanggal_selesai' => [
            'valid_date' => 'Tanggal selesai tidak valid'
        ]
    ];

    protected $skipValidation = false;

    /**
     * Generate nomor cuti
     */
    public function generateNomorCuti()
    {
        $prefix = 'CTI-' . date('Ymd') . '-';
        $last = $this->select('nomor_cuti')
                    ->like('nomor_cuti', $prefix, 'after')
                    ->orderBy('nomor_cuti', 'DESC')
                    ->first();

        if ($last) {
            $lastNumber = (int) substr($last['nomor_cuti'], strlen($prefix));
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . $nextNumber;
    }

    /**
     * Get cuti with karyawan data
     */
    public function getCutiWithKaryawan($id = null)
    {
        $builder = $this->db->table($this->table . ' c');
        $builder->select('c.*, k.nik, k.nama_lengkap, k.jabatan, k.departemen, u.name as disetujui_nama');
        $builder->join('karyawan k', 'k.id = c.karyawan_id', 'left');
        $builder->join('users u', 'u.id = c.disetujui_oleh', 'left');

        if ($id) {
            $builder->where('c.id', $id);
            return $builder->get()->getRowArray();
        }

        $builder->orderBy('c.created_at', 'DESC');
        return $builder->get()->getResultArray();
    }

    /**
     * Get cuti by status
     */
    public function getByStatus($status)
    {
        return $this->select('c.*, k.nik, k.nama_lengkap')
                   ->join('karyawan k', 'k.id = c.karyawan_id', 'left')
                   ->where('c.status', $status)
                   ->orderBy('c.created_at', 'DESC')
                   ->findAll();
    }

    /**
     * Get cuti by karyawan
     */
    public function getByKaryawan($karyawanId, $year = null)
    {
        $builder = $this->where('karyawan_id', $karyawanId);

        if ($year) {
            $builder->where('YEAR(tanggal_mulai)', $year)
                   ->orWhere('YEAR(tanggal_selesai)', $year);
        }

        return $builder->orderBy('tanggal_mulai', 'DESC')->findAll();
    }

    /**
     * Check tanggal cuti bentrok
     */
    public function checkDateConflict($karyawanId, $startDate, $endDate, $excludeId = null)
    {
        $builder = $this->where('karyawan_id', $karyawanId)
                       ->where('status !=', 'Ditolak')
                       ->where('status !=', 'Dibatalkan')
                       ->groupStart()
                           ->groupStart()
                               ->where('tanggal_mulai <=', $endDate)
                               ->where('tanggal_selesai >=', $startDate)
                           ->groupEnd()
                           ->orGroupStart()
                               ->where('tanggal_mulai >=', $startDate)
                               ->where('tanggal_mulai <=', $endDate)
                           ->groupEnd()
                           ->orGroupStart()
                               ->where('tanggal_selesai >=', $startDate)
                               ->where('tanggal_selesai <=', $endDate)
                           ->groupEnd()
                       ->groupEnd();

        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Update status cuti
     */
    public function updateStatus($id, $status, $approvedBy = null, $rejectionReason = null)
    {
        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($approvedBy && ($status === 'Disetujui HRD' || $status === 'Disetujui Atasan')) {
            $data['disetujui_oleh'] = $approvedBy;
            $data['disetujui_at'] = date('Y-m-d H:i:s');
        }

        if ($rejectionReason && $status === 'Ditolak') {
            $data['alasan_penolakan'] = $rejectionReason;
        }

        return $this->update($id, $data);
    }

    /**
     * Get statistics
     */
    public function getStatistics($startDate = null, $endDate = null, $department = null)
    {
        $builder = $this->db->table($this->table . ' c');
        $builder->select("
            COUNT(*) as total_pengajuan,
            SUM(CASE WHEN c.status = 'Disetujui HRD' OR c.status = 'Disetujui Atasan' THEN 1 ELSE 0 END) as total_disetujui,
            SUM(CASE WHEN c.status = 'Ditolak' THEN 1 ELSE 0 END) as total_ditolak,
            SUM(CASE WHEN c.status = 'Menunggu' THEN 1 ELSE 0 END) as total_menunggu,
            SUM(c.lama_hari) as total_hari_cuti
        ")->join('karyawan k', 'k.id = c.karyawan_id', 'left');

        if ($startDate) {
            $builder->where('c.tanggal_mulai >=', $startDate);
        }

        if ($endDate) {
            $builder->where('c.tanggal_mulai <=', $endDate);
        }

        if ($department) {
            $builder->where('k.departemen', $department);
        }

        return $builder->get()->getRowArray();
    }
}

// Model untuk KuotaCuti
class KuotaCutiModel extends Model
{
    protected $table = 'kuota_cuti';
    protected $primaryKey = 'id';
    protected $allowedFields = ['karyawan_id', 'tahun', 'kuota_tahunan', 'terpakai', 'sisa'];
    protected $useTimestamps = true;

    public function getOrCreateQuota($karyawanId, $year)
    {
        $quota = $this->where('karyawan_id', $karyawanId)
                     ->where('tahun', $year)
                     ->first();

        if (!$quota) {
            // Default kuota 12 hari
            $data = [
                'karyawan_id' => $karyawanId,
                'tahun' => $year,
                'kuota_tahunan' => 12,
                'terpakai' => 0,
                'sisa' => 12
            ];
            $this->insert($data);
            $quota = (object) $data;
            $quota->id = $this->insertID();
        }

        return $quota;
    }

    public function updateQuota($karyawanId, $year, $daysUsed)
    {
        $quota = $this->getOrCreateQuota($karyawanId, $year);
        
        $terpakai = $quota->terpakai + $daysUsed;
        $sisa = $quota->kuota_tahunan - $terpakai;

        return $this->update($quota->id, [
            'terpakai' => $terpakai,
            'sisa' => $sisa
        ]);
    }

    public function getQuotaByKaryawan($karyawanId, $year = null)
    {
        if (!$year) {
            $year = date('Y');
        }

        return $this->where('karyawan_id', $karyawanId)
                   ->where('tahun', $year)
                   ->first();
    }
}