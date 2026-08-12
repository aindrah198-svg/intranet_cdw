<?php

namespace App\Models\SoftwareEngineer;

use CodeIgniter\Model;

class SeTaskModel extends Model
{
    protected $table            = 'se_tasks';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'system_id',
        'proyek_id',
        'task_name',
        'deskripsi',
        'milestone_sprint',
        'priority',
        'status',
        'due_date',
        'assigned_to',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getTasksWithDetail()
    {
        return $this->select('se_tasks.*, s.nama_sistem, p.nama_proyek')
                    ->join('se_systems s', 's.id = se_tasks.system_id', 'left')
                    ->join('projects p', 'p.id = se_tasks.proyek_id', 'left')
                    ->orderBy('se_tasks.created_at', 'DESC')
                    ->findAll();
    }
}
