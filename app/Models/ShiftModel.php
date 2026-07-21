<?php

namespace App\Models;

use CodeIgniter\Model;

class ShiftModel extends Model
{
    protected $table = 'shift_kerja';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'nama_shift', 'jam_mulai', 'jam_selesai', 'durasi_jam',
        'toleransi_terlambat', 'status'
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    
    /**
     * Get all active shifts
     */
    public function getActiveShifts()
    {
        return $this->where('status', 'aktif')
                    ->where('deleted_at', null)
                    ->orderBy('jam_mulai', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get shift by ID
     */
    public function getShift($id)
    {
        return $this->where('id', $id)
                    ->where('deleted_at', null)
                    ->first();
    }
    
    /**
     * Get shift options for select
     */
    public function getShiftOptions()
    {
        $shifts = $this->getActiveShifts();
        $options = [];
        
        foreach ($shifts as $shift) {
            $options[$shift['id']] = $shift['nama_shift'] . ' (' . 
                substr($shift['jam_mulai'], 0, 5) . ' - ' . 
                substr($shift['jam_selesai'], 0, 5) . ')';
        }
        
        return $options;
    }
}