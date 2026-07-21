<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectModel extends Model
{
    protected $table = 'project';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $allowedFields = [
        'kode_project',
        'nama_project',
        'client_id',
        'deskripsi',
        'nilai_project',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'project_manager_id'
    ];
    
    protected $validationRules = [
        'kode_project' => 'required|is_unique[project.kode_project,id,{id}]|max_length[50]',
        'nama_project' => 'required|max_length[200]',
        'client_id' => 'required|integer',
        'deskripsi' => 'permit_empty',
        'nilai_project' => 'permit_empty|decimal',
        'tanggal_mulai' => 'permit_empty|valid_date',
        'tanggal_selesai' => 'permit_empty|valid_date',
        'status' => 'required|in_list[penawaran,nego,deal,on_progress,selesai,batal]',
        'project_manager_id' => 'permit_empty|integer'
    ];
    
    public function generateKodeProject()
    {
        $prefix = 'PROJ';
        $year = date('Y');
        $month = date('m');
        
        $count = $this->like('kode_project', $prefix . $year . $month, 'after')
                     ->countAllResults();
        
        $sequence = $count + 1;
        
        return $prefix . $year . $month . sprintf('%03d', $sequence);
    }
    
    /**
     * Get projects untuk membuat surat jalan
     * Project yang bisa dibuat surat jalan adalah yang statusnya:
     * - deal, on_progress, atau aktif
     */
   public function getProjectsForSuratJalan($userData)
{
    $builder = $this->db->table('project p');
    $builder->select('p.*, 
        c.nama_perusahaan, 
        c.nama_kontak, 
        c.alamat, 
        c.telepon,
        c.sales_id as client_sales_id')
        ->join('client c', 'c.id = p.client_id')
        ->whereIn('p.status', ['deal', 'on_progress']);
        
        log_message('debug', '=== DEBUG getProjectsForSuratJalan ===');
        log_message('debug', 'User Data: ' . json_encode($userData));
        
        // Filter berdasarkan role
        if ($userData['role'] === 'sales') {
            log_message('debug', 'Sales user detected');
            log_message('debug', 'User ID: ' . $userData['id']);
            log_message('debug', 'Karyawan ID: ' . ($userData['karyawan_id'] ?? 'NULL'));
            
            $salesId = !empty($userData['karyawan_id']) ? $userData['karyawan_id'] : $userData['id'];
            
            $builder->where('c.sales_id', $salesId);
            
            log_message('debug', 'Filtering by sales_id: ' . $salesId);
        } else {
            log_message('debug', 'Admin/Direktur - No filter by sales');
        }
        
        $builder->orderBy('p.nama_project', 'ASC');
        
        $query = $builder->get();
        $results = $query->getResultArray();
        
        log_message('debug', 'Projects found: ' . count($results));
        foreach ($results as $index => $project) {
            log_message('debug', "Project [$index]: ID={$project['id']}, Name={$project['nama_project']}, Status={$project['status']}, Client={$project['nama_perusahaan']}, Sales ID={$project['client_sales_id']}");
        }
        
        return $results;
    }
    
    /**
     * Get all projects dengan join client
     */
    public function getAllProjectsWithClient()
    {
        $builder = $this->db->table('project p');
        $builder->select('p.*, 
            c.nama_perusahaan, 
            c.nama_kontak, 
            c.telepon as client_telepon,
            c.email as client_email,
            c.alamat as client_alamat,
            k.nama_lengkap as project_manager_name')
            ->join('client c', 'c.id = p.client_id', 'left')
            ->join('karyawan k', 'k.id = p.project_manager_id', 'left')
            ->orderBy('p.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get project by ID dengan detail lengkap
     */
    public function getProjectWithDetails($id)
    {
        $builder = $this->db->table('project p');
        $builder->select('p.*, 
            c.nama_perusahaan, 
            c.nama_kontak, 
            c.telepon as client_telepon,
            c.email as client_email,
            c.alamat as client_alamat,
            c.sales_id as client_sales_id,
            k.nama_lengkap as project_manager_name,
            s.nama_lengkap as sales_name')
            ->join('client c', 'c.id = p.client_id', 'left')
            ->join('karyawan k', 'k.id = p.project_manager_id', 'left')
            ->join('karyawan s', 's.id = c.sales_id', 'left')
            ->where('p.id', $id);
        
        return $builder->get()->getRowArray();
    }
    
    /**
     * Get projects by client ID
     */
    public function getProjectsByClient($clientId)
    {
        return $this->where('client_id', $clientId)
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }
    
    /**
     * Get projects by status
     */
    public function getProjectsByStatus($status)
    {
        return $this->where('status', $status)
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }
    
    /**
     * Get projects by sales ID
     */
    public function getProjectsBySales($salesId)
    {
        $builder = $this->db->table('project p');
        $builder->select('p.*, c.nama_perusahaan, c.nama_kontak')
                ->join('client c', 'c.id = p.client_id')
                ->where('c.sales_id', $salesId)
                ->orderBy('p.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Search projects
     */
    public function searchProjects($keyword, $salesId = null)
    {
        $builder = $this->db->table('project p');
        $builder->select('p.*, c.nama_perusahaan, c.nama_kontak')
                ->join('client c', 'c.id = p.client_id');
        
        if ($salesId) {
            $builder->where('c.sales_id', $salesId);
        }
        
        $builder->groupStart()
                ->like('p.kode_project', $keyword)
                ->orLike('p.nama_project', $keyword)
                ->orLike('c.nama_perusahaan', $keyword)
                ->groupEnd()
                ->orderBy('p.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Update project status
     */
    public function updateStatus($id, $status)
    {
        return $this->update($id, ['status' => $status]);
    }
    
    /**
     * Get project statistics
     */
    public function getProjectStatistics()
    {
        $stats = [];
        
        // Total projects
        $stats['total'] = $this->countAll();
        
        // Projects by status
        $builder = $this->db->table($this->table);
        $builder->select('status, COUNT(*) as count')
                ->groupBy('status');
        
        $result = $builder->get()->getResultArray();
        
        $stats['by_status'] = [];
        foreach ($result as $row) {
            $stats['by_status'][$row['status']] = $row['count'];
        }
        
        return $stats;
    }
    
    /**
     * Get recent projects
     */
    public function getRecentProjects($limit = 10)
    {
        return $this->orderBy('created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }
}