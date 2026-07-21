<?php

namespace App\Controllers;

class Projects extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Our Projects | CDW Engineering',
            'active' => 'projects',
            'meta_description' => 'Explore our portfolio of PERTASHOP installations and engineering projects across Indonesia. CDW Engineering specializes in fuel station construction and industrial solutions.',
            'meta_keywords' => 'CDW Engineering projects, PERTASHOP installation, fuel station construction, engineering projects Indonesia, petroleum equipment installation'
        ];
        
        return view('templates/header', $data)
            . view('templates/nav')
            . view('projects')
            . view('templates/footer');
    }
    
    /**
     * API endpoint untuk mengambil data projects (optional untuk AJAX loading)
     */
    public function api()
    {
        // Jika ingin implementasi AJAX loading
        if (!$this->request->isAJAX()) {
            return redirect()->to('/projects');
        }
        
        $page = $this->request->getGet('page') ?? 1;
        $limit = $this->request->getGet('limit') ?? 9;
        $region = $this->request->getGet('region') ?? 'all';
        
        // Contoh data projects
        $projectsData = $this->getProjectsData($page, $limit, $region);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $projectsData['items'],
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $projectsData['total'],
                'totalPages' => ceil($projectsData['total'] / $limit),
                'hasMore' => ($page * $limit) < $projectsData['total']
            ]
        ]);
    }
    
    /**
     * Get projects data
     */
    private function getProjectsData($page = 1, $limit = 9, $region = 'all')
    {
        // Data proyek statis sesuai dengan informasi yang diberikan
        $allProjects = [
            [
                'id' => 1,
                'number' => '01',
                'image' => 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'region' => 'jawa-timur',
                'title' => 'Instalasi PERTASHOP di Tempurejo',
                'description' => 'Complete installation of PERTASHOP retail outlet with modern facilities',
                'location' => 'Tempurejo, Jawa Timur',
                'status' => 'Completed',
                'client' => 'PT. Pertamina (Persero)',
                'type' => 'PERTASHOP Installation',
                'full_description' => 'Complete installation of PERTASHOP retail outlet including civil works, mechanical installation of fuel dispensing systems, electrical works, and safety systems implementation.'
            ],
            [
                'id' => 2,
                'number' => '02',
                'image' => 'https://images.unsplash.com/photo-1621905252507-b35492cc74b4?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'region' => 'jawa-timur',
                'title' => 'Instalasi PERTASHOP di Puger',
                'description' => 'Modern retail outlet installation with integrated fuel management system',
                'location' => 'Puger, Jawa Timur',
                'status' => 'Completed',
                'client' => 'PT. Pertamina (Persero)',
                'type' => 'PERTASHOP Installation',
                'full_description' => 'Strategic location installation with integrated fuel management system for optimized operations.'
            ],
            [
                'id' => 3,
                'number' => '03',
                'image' => 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'region' => 'jawa-timur',
                'title' => 'Instalasi PERTASHOP di Batu Marmar',
                'description' => 'Strategic location installation with enhanced customer facilities',
                'location' => 'Batu Marmar, Jawa Timur',
                'status' => 'Completed',
                'client' => 'PT. Pertamina (Persero)',
                'type' => 'PERTASHOP Installation',
                'full_description' => 'High-traffic location development with enhanced customer facilities.'
            ],
            [
                'id' => 4,
                'number' => '04',
                'image' => 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'region' => 'jawa-tengah',
                'title' => 'Instalasi PERTASHOP di Kemiri',
                'description' => 'Complete turnkey project including civil works and mechanical installation',
                'location' => 'Kemiri, Jawa Tengah',
                'status' => 'Completed',
                'client' => 'PT. Pertamina (Persero)',
                'type' => 'PERTASHOP Installation',
                'full_description' => 'Complete turnkey project from site preparation to operational handover.'
            ],
            [
                'id' => 5,
                'number' => '05',
                'image' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'region' => 'jawa-timur',
                'title' => 'Instalasi PERTASHOP di Klampis',
                'description' => 'Modern retail outlet with advanced fuel dispensing technology',
                'location' => 'Klampis, Jawa Timur',
                'status' => 'Completed',
                'client' => 'PT. Pertamina (Persero)',
                'type' => 'PERTASHOP Installation',
                'full_description' => 'Installation featuring latest generation fuel dispensing technology.'
            ],
            [
                'id' => 6,
                'number' => '06',
                'image' => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'region' => 'jawa-barat',
                'title' => 'Instalasi PERTASHOP di Cipeundeuy',
                'description' => 'Strategic highway location with high traffic volume',
                'location' => 'Cipeundeuy, Jawa Barat',
                'status' => 'Completed',
                'client' => 'PT. Pertamina (Persero)',
                'type' => 'PERTASHOP Installation',
                'full_description' => 'Highway location development designed for high-volume traffic.'
            ],
            [
                'id' => 7,
                'number' => '07',
                'image' => 'https://images.unsplash.com/photo-1581092160562-40aa08e78837?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'region' => 'jawa-tengah',
                'title' => 'Instalasi PERTASHOP di Cilongok',
                'description' => 'Complete facility including convenience store and service area',
                'location' => 'Cilongok, Jawa Tengah',
                'status' => 'Completed',
                'client' => 'PT. Pertamina (Persero)',
                'type' => 'PERTASHOP Installation',
                'full_description' => 'Comprehensive facility development including fully stocked convenience store.'
            ],
            [
                'id' => 8,
                'number' => '08',
                'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'region' => 'bali',
                'title' => 'Instalasi PERTASHOP di Kintamani',
                'description' => 'Tourist area installation with enhanced facilities',
                'location' => 'Kintamani, Bali',
                'status' => 'Completed',
                'client' => 'PT. Pertamina (Persero)',
                'type' => 'PERTASHOP Installation',
                'full_description' => 'Tourist destination installation featuring Balinese architectural elements.'
            ],
            [
                'id' => 9,
                'number' => '09',
                'image' => 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'region' => 'jawa-timur',
                'title' => 'Instalasi PERTASHOP di Kedung Adem',
                'description' => 'Rural area development project',
                'location' => 'Kedung Adem, Jawa Timur',
                'status' => 'Completed',
                'client' => 'PT. Pertamina (Persero)',
                'type' => 'PERTASHOP Installation',
                'full_description' => 'Rural development project bringing modern fuel services to underserved areas.'
            ],
            [
                'id' => 10,
                'number' => '10',
                'image' => 'https://images.unsplash.com/photo-1621905252507-b35492cc74b4?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'region' => 'jawa-timur',
                'title' => 'Instalasi PERTASHOP di Widang',
                'description' => 'High-volume commercial area installation',
                'location' => 'Kecamatan Widang, Jawa Timur',
                'status' => 'Completed',
                'client' => 'PT. Pertamina (Persero)',
                'type' => 'PERTASHOP Installation',
                'full_description' => 'Commercial area installation designed for high-volume business.'
            ],
            [
                'id' => 11,
                'number' => '11',
                'image' => 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'region' => 'jawa-timur',
                'title' => 'Instalasi PERTASHOP di Tempurejo',
                'description' => 'Second installation in strategic location',
                'location' => 'Tempurejo, Jawa Timur',
                'status' => 'Completed',
                'client' => 'PT. Pertamina (Persero)',
                'type' => 'PERTASHOP Installation',
                'full_description' => 'Follow-up installation in high-demand area.'
            ],
            [
                'id' => 12,
                'number' => '12',
                'image' => 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'region' => 'bali',
                'title' => 'Instalasi PERTASHOP di Kubutambahan',
                'description' => 'Coastal area installation',
                'location' => 'Kubutambahan, Bali',
                'status' => 'Completed',
                'client' => 'PT. Pertamina (Persero)',
                'type' => 'PERTASHOP Installation',
                'full_description' => 'Coastal location installation with special considerations for salt air.'
            ],
            [
                'id' => 13,
                'number' => '13',
                'image' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'region' => 'bali',
                'title' => 'Instalasi PERTASHOP di Sawan',
                'description' => 'Modern facility with local architectural elements',
                'location' => 'Sawan, Bali',
                'status' => 'Completed',
                'client' => 'PT. Pertamina (Persero)',
                'type' => 'PERTASHOP Installation',
                'full_description' => 'Modern facility incorporating traditional Balinese architectural elements.'
            ],
            [
                'id' => 14,
                'number' => '14',
                'image' => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'region' => 'bali',
                'title' => 'Instalasi PERTASHOP di Rendang',
                'description' => 'Mountainous area installation',
                'location' => 'Rendang, Bali',
                'status' => 'Completed',
                'client' => 'PT. Pertamina (Persero)',
                'type' => 'PERTASHOP Installation',
                'full_description' => 'Mountainous terrain installation requiring special engineering considerations.'
            ],
            [
                'id' => 15,
                'number' => '15',
                'image' => 'https://images.unsplash.com/photo-1581092160562-40aa08e78837?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'region' => 'bali',
                'title' => 'Instalasi PERTASHOP di Buleleng',
                'description' => 'Urban commercial installation',
                'location' => 'Kecamatan Buleleng, Bali',
                'status' => 'Completed',
                'client' => 'PT. Pertamina (Persero)',
                'type' => 'PERTASHOP Installation',
                'full_description' => 'Urban commercial district installation with space optimization.'
            ]
        ];
        
        // Filter berdasarkan region jika bukan 'all'
        $filteredProjects = $allProjects;
        if ($region !== 'all') {
            $filteredProjects = array_filter($allProjects, function($project) use ($region) {
                return $project['region'] === $region;
            });
            $filteredProjects = array_values($filteredProjects); // Reset array keys
        }
        
        // Pagination logic
        $total = count($filteredProjects);
        $offset = ($page - 1) * $limit;
        $paginatedItems = array_slice($filteredProjects, $offset, $limit);
        
        return [
            'items' => $paginatedItems,
            'total' => $total
        ];
    }
    
    /**
     * Get project by ID (untuk modal detail)
     */
    public function detail($id)
    {
        $project = $this->getProjectById($id);
        
        if (!$project) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Project not found'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $project
        ]);
    }
    
    /**
     * Get single project by ID
     */
    private function getProjectById($id)
    {
        $allProjects = $this->getProjectsData()['items'];
        
        foreach ($allProjects as $project) {
            if ($project['id'] == $id) {
                return $project;
            }
        }
        
        return null;
    }
    
    /**
     * Get project regions (untuk filter)
     */
    public function regions()
    {
        $regions = [
            ['id' => 'all', 'name' => 'All Regions', 'count' => 15],
            ['id' => 'jawa-timur', 'name' => 'Jawa Timur', 'count' => 8],
            ['id' => 'jawa-tengah', 'name' => 'Jawa Tengah', 'count' => 2],
            ['id' => 'jawa-barat', 'name' => 'Jawa Barat', 'count' => 1],
            ['id' => 'bali', 'name' => 'Bali', 'count' => 4]
        ];
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $regions
        ]);
    }
    
    /**
     * Get customers list
     */
    public function customers()
    {
        $customers = [
            'major_corporations' => [
                'PT. Sapta Indra Sejati',
                'PT. Rekayasa Industri (Persero)',
                'PT. Patria (United Tractor Group)',
                'PT. Sanggar Sarana Baja',
                'PT. Sumberdaya Sewatama',
                'PT. Fuchs Indonesia',
                'PT. Tempo Scan Pacific',
                'PT. Pama Persada (Astra Group)',
                'PT. Medco Energy',
                'PT. Pertamina (Persero)'
            ],
            'energy_resources' => [
                'PT. Cipta Niaga Gas (CNG)',
                'PT. Gunung Bayan Group',
                'PT. Petrosea TBK',
                'PT. Thiess Contractor Indonesia',
                'PT. AKR Corporindo',
                'Chevron Indonesia',
                'PT. Kideco Jaya Agung',
                'PT. Mega Prima Persada',
                'PT. Alam Raya Abadi',
                'PT. Prima Sarana Gemilang'
            ],
            'industrial_engineering' => [
                'PT. Buana Andi Muda',
                'PT. Bukaka',
                'PT. Straight Consultant Services',
                'PT. Adani Global Group',
                'PT. Sugar Group',
                'PT. RAPP',
                'PT. Suryaciptateknik',
                'PT. Ancora Group',
                'PT. Trakindo Utama Group',
                'PT. Astra Group'
            ]
        ];
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $customers,
            'statistics' => [
                'total_customers' => 30,
                'industries_served' => 8,
                'years_experience' => 15,
                'client_retention' => '98%'
            ]
        ]);
    }
    
    /**
     * Admin function: Upload new project (protected)
     */
    public function upload()
    {
        // Check if user is admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Unauthorized access');
        }
        
        // Handle form submission
        if ($this->request->getMethod() === 'post') {
            $validation = $this->validate([
                'title' => 'required|min_length[3]|max_length[100]',
                'location' => 'required|min_length[3]|max_length[100]',
                'region' => 'required|in_list[jawa-timur,jawa-tengah,jawa-barat,bali]',
                'image' => 'uploaded[image]|max_size[image,5120]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]'
            ]);
            
            if (!$validation) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
            
            $file = $this->request->getFile('image');
            
            if ($file->isValid() && !$file->hasMoved()) {
                // Generate unique filename
                $newName = $file->getRandomName();
                
                // Move to uploads directory
                $file->move(FCPATH . 'uploads/projects', $newName);
                
                // Save to database
                $projectData = [
                    'title' => $this->request->getPost('title'),
                    'location' => $this->request->getPost('location'),
                    'region' => $this->request->getPost('region'),
                    'description' => $this->request->getPost('description'),
                    'client' => $this->request->getPost('client'),
                    'status' => $this->request->getPost('status'),
                    'image_path' => 'uploads/projects/' . $newName,
                    'created_by' => session()->get('user_id'),
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                // TODO: Save to database
                // $this->projectModel->insert($projectData);
                
                return redirect()->to('/admin/projects')->with('success', 'Project added successfully');
            }
        }
        
        // Show upload form
        $data = [
            'title' => 'Add New Project | CDW Engineering Admin',
            'active' => 'projects'
        ];
        
        return view('admin/project_upload', $data);
    }
}