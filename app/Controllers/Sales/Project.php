<?php

namespace App\Controllers\Sales;

class Project extends SalesController
{
    protected $projectModel;
    protected $clientModel;
    
    public function __construct()
    {
        parent::initController(
            \Config\Services::request(),
            \Config\Services::response(),
            \Config\Services::logger()
        );
        
        $this->projectModel = new \App\Models\ProjectModel();
        $this->clientModel = new \App\Models\ClientModel();
    }
    
    public function create()
    {
        // Get redirect URL
        $redirectUrl = $this->request->getGet('redirect') ?? base_url('sales/invoice/create');
        
        // Get user data
        $userData = $this->getUserData();
        
        // Get client options
        $clients = $this->clientModel->where('status', 'active')
            ->where('sales_id', $userData['id'])
            ->orderBy('nama_perusahaan', 'ASC')
            ->findAll();
        
        // Generate project code
        $projectCode = $this->generateProjectCode();
        
        $data = [
            'title' => 'Buat Project Baru',
            'subtitle' => 'Form Project',
            'redirect_url' => $redirectUrl,
            'clients' => $clients,
            'project_code' => $projectCode,
            'validation' => \Config\Services::validation(),
            'active' => 'project'
        ];
        
        return $this->renderView('sales/project/create', $data);
    }
    
    public function store()
    {
        // Get redirect URL
        $redirectUrl = $this->request->getPost('redirect_url') ?? base_url('sales/invoice/create');
        
        // Validation rules
        $rules = [
            'kode_project' => 'required|is_unique[project.kode_project]|max_length[50]',
            'nama_project' => 'required|max_length[200]',
            'client_id' => 'required|integer',
            'deskripsi' => 'permit_empty',
            'nilai_project' => 'permit_empty|decimal',
            'tanggal_mulai' => 'permit_empty|valid_date'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        try {
            $data = [
                'kode_project' => $this->request->getPost('kode_project'),
                'nama_project' => $this->request->getPost('nama_project'),
                'client_id' => $this->request->getPost('client_id'),
                'deskripsi' => $this->request->getPost('deskripsi'),
                'nilai_project' => $this->request->getPost('nilai_project') ? 
                    str_replace(',', '', $this->request->getPost('nilai_project')) : 0,
                'tanggal_mulai' => $this->request->getPost('tanggal_mulai') ?: date('Y-m-d'),
                'status' => 'deal',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // Save project
            $projectId = $this->projectModel->insert($data, true);
            
            // Get project name for redirect
            $projectName = $this->request->getPost('nama_project');
            
            session()->setFlashdata('success', 'Project berhasil dibuat!');
            
            // Redirect back to invoice create with parameters
            $redirectUrl .= (strpos($redirectUrl, '?') === false ? '?' : '&') . 
                          'new_project_id=' . $projectId . 
                          '&new_project_name=' . urlencode('[NEW] ' . $projectName);
            
            return redirect()->to($redirectUrl);
            
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
    
    private function generateProjectCode()
    {
        $year = date('Y');
        $month = date('m');
        
        $lastProject = $this->projectModel->select('kode_project')
            ->like('kode_project', 'PROJ-' . $year . $month . '-%')
            ->orderBy('id', 'DESC')
            ->first();
        
        if ($lastProject) {
            $lastNumber = explode('-', $lastProject['kode_project']);
            $sequence = intval(end($lastNumber)) + 1;
        } else {
            $sequence = 1;
        }
        
        $sequenceFormatted = str_pad($sequence, 3, '0', STR_PAD_LEFT);
        
        return 'PROJ-' . $year . $month . '-' . $sequenceFormatted;
    }
}