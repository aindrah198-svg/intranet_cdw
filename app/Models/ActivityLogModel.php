<?php
// C:\xampp\htdocs\cdwnet\app\Models\ActivityLogModel.php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'user_id', 'activity', 'details', 'ip_address', 
        'user_agent', 'created_at'
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = null;
    
    // Validation rules
    protected $validationRules = [
        'user_id' => 'required|integer',
        'activity' => 'required|max_length[255]',
    ];
    
    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID harus diisi',
            'integer' => 'User ID harus berupa angka'
        ],
        'activity' => [
            'required' => 'Aktivitas harus diisi',
            'max_length' => 'Aktivitas maksimal 255 karakter'
        ]
    ];
    
    protected $skipValidation = false;
    
    /**
     * Get activity logs with user data
     */
    public function getActivityLogsWithUser($limit = 50, $offset = 0)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('activity_logs');
        
        $builder->select('activity_logs.*, users.username, users.name');
        $builder->join('users', 'users.id = activity_logs.user_id');
        $builder->orderBy('activity_logs.created_at', 'DESC');
        $builder->limit($limit, $offset);
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get activity logs by user
     */
    public function getLogsByUser($user_id, $limit = 20)
    {
        return $this->where('user_id', $user_id)
                   ->orderBy('created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }
    
    /**
     * Log an activity
     */
    public function logActivity($user_id, $activity, $details = null)
    {
        $data = [
            'user_id' => $user_id,
            'activity' => $activity,
            'details' => $details,
            'ip_address' => service('request')->getIPAddress(),
            'user_agent' => service('request')->getUserAgent()->getAgentString()
        ];
        
        return $this->insert($data);
    }
    
    /**
     * Get recent activities
     */
    public function getRecentActivities($days = 7)
    {
        $startDate = date('Y-m-d', strtotime("-{$days} days"));
        
        return $this->where('created_at >=', $startDate)
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }
}