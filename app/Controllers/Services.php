<?php

namespace App\Controllers;

class Services extends BaseController
{
    public function index()
    {
 // Cek apakah ada anchor (section) dari URL
    $section = $this->request->getGet('section') ?? '';
    
    $data = [
        'title' => 'Our Services | CDW Engineering',
        'active' => 'services',
        'section' => $section, // Tambahkan ini
        'meta_description' => 'CDW Engineering offers comprehensive engineering, construction, mechanical & electrical, and IT integration services for industrial and commercial projects.',
        'meta_keywords' => 'engineering services, construction services, mechanical electrical services, IT integration, CDW Engineering services',
        'services' => $this->getServicesData()
    ];
    
    return view('templates/header', $data)
        . view('templates/nav')
        . view('services')
        . view('templates/footer');
}
    
    public function detail($slug)
    {
        $services = $this->getServicesData();
        $service = null;
        
        // Cari service berdasarkan slug
        foreach ($services as $s) {
            if ($s['slug'] === $slug) {
                $service = $s;
                break;
            }
        }
        
        if (!$service) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        
        $data = [
            'title' => $service['name'] . ' | CDW Engineering',
            'active' => 'services',
            'meta_description' => $service['meta_description'],
            'meta_keywords' => $service['meta_keywords'],
            'service' => $service,
            'other_services' => array_filter($services, function($s) use ($service) {
                return $s['slug'] !== $service['slug'];
            })
        ];
        
        return view('templates/header', $data)
            . view('templates/nav')
            . view('service_detail')
            . view('templates/footer');
    }
    
    /**
     * Get all services data
     */
    private function getServicesData()
    {
        return [
            [
                'slug' => 'engineering',
                'name' => 'Engineering Services',
                'short_name' => 'Engineering',
                'icon' => 'fas fa-cog',
                'color' => 'primary',
                'short_description' => 'Comprehensive engineering solutions from design to implementation',
                'full_description' => 'Our engineering services provide end-to-end solutions for industrial and commercial projects. We specialize in conceptual design, detailed engineering, project management, and technical consulting.',
                'meta_description' => 'Professional engineering services including design, consultation, and project management for industrial applications.',
                'meta_keywords' => 'engineering design, project management, technical consulting, industrial engineering',
                'features' => [
                    [
                        'title' => 'Conceptual Design',
                        'description' => 'Initial project planning and feasibility studies',
                        'icon' => 'fas fa-lightbulb'
                    ],
                    [
                        'title' => 'Detailed Engineering',
                        'description' => 'Comprehensive technical specifications and drawings',
                        'icon' => 'fas fa-drafting-compass'
                    ],
                    [
                        'title' => 'Project Management',
                        'description' => 'Complete project oversight and coordination',
                        'icon' => 'fas fa-tasks'
                    ],
                    [
                        'title' => 'Technical Consulting',
                        'description' => 'Expert advice and problem-solving',
                        'icon' => 'fas fa-headset'
                    ]
                ],
                'projects' => [
                    'Industrial Plant Design',
                    'Process Engineering',
                    'Structural Analysis',
                    'Equipment Specification'
                ],
                'image' => 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'image_alt' => 'Engineering design and planning'
            ],
            [
                'slug' => 'construction',
                'name' => 'Construction Services',
                'short_name' => 'Construction',
                'icon' => 'fas fa-building',
                'color' => 'warning',
                'short_description' => 'Turnkey construction solutions for industrial facilities',
                'full_description' => 'We provide complete construction services for industrial and commercial projects. Our expertise includes civil works, structural steel erection, equipment installation, and quality assurance.',
                'meta_description' => 'Full-service construction solutions including civil works, structural erection, and equipment installation.',
                'meta_keywords' => 'construction services, civil works, structural erection, equipment installation',
                'features' => [
                    [
                        'title' => 'Civil Works',
                        'description' => 'Site preparation and foundation construction',
                        'icon' => 'fas fa-hard-hat'
                    ],
                    [
                        'title' => 'Structural Erection',
                        'description' => 'Steel structure assembly and installation',
                        'icon' => 'fas fa-industry'
                    ],
                    [
                        'title' => 'Equipment Installation',
                        'description' => 'Precision installation of industrial equipment',
                        'icon' => 'fas fa-tools'
                    ],
                    [
                        'title' => 'Quality Assurance',
                        'description' => 'Rigorous quality control and testing',
                        'icon' => 'fas fa-clipboard-check'
                    ]
                ],
                'projects' => [
                    'Factory Construction',
                    'Warehouse Building',
                    'Industrial Facilities',
                    'Commercial Buildings'
                ],
                'image' => 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'image_alt' => 'Construction site work'
            ],
            [
                'slug' => 'mechanical',
                'name' => 'Mechanical & Electrical Services',
                'short_name' => 'Mech & Elec',
                'icon' => 'fas fa-bolt',
                'color' => 'success',
                'short_description' => 'Integrated mechanical and electrical systems',
                'full_description' => 'Our mechanical and electrical services cover the design, installation, and maintenance of integrated systems for industrial applications. We ensure optimal performance and energy efficiency.',
                'meta_description' => 'Professional mechanical and electrical system installation, maintenance, and optimization services.',
                'meta_keywords' => 'mechanical services, electrical installation, system maintenance, energy efficiency',
                'features' => [
                    [
                        'title' => 'Mechanical Systems',
                        'description' => 'Piping, HVAC, and machinery installation',
                        'icon' => 'fas fa-cogs'
                    ],
                    [
                        'title' => 'Electrical Installation',
                        'description' => 'Power distribution and control systems',
                        'icon' => 'fas fa-plug'
                    ],
                    [
                        'title' => 'System Integration',
                        'description' => 'Seamless integration of M&E systems',
                        'icon' => 'fas fa-link'
                    ],
                    [
                        'title' => 'Maintenance Services',
                        'description' => 'Preventive and corrective maintenance',
                        'icon' => 'fas fa-wrench'
                    ]
                ],
                'projects' => [
                    'Pump Systems Installation',
                    'Electrical Panel Setup',
                    'HVAC Systems',
                    'Control Systems'
                ],
                'image' => 'https://images.unsplash.com/photo-1621905252507-b35492cc74b4?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'image_alt' => 'Mechanical and electrical systems'
            ],
            [
                'slug' => 'it',
                'name' => 'IT & Integration Services',
                'short_name' => 'IT & Integration',
                'icon' => 'fas fa-server',
                'color' => 'info',
                'short_description' => 'Advanced IT solutions and system integration',
                'full_description' => 'We provide cutting-edge IT solutions and system integration services. Our expertise includes network infrastructure, automation systems, data management, and IoT integration.',
                'meta_description' => 'Comprehensive IT services including network setup, automation, data management, and system integration.',
                'meta_keywords' => 'IT services, system integration, automation, network infrastructure, IoT',
                'features' => [
                    [
                        'title' => 'Network Infrastructure',
                        'description' => 'Enterprise network design and implementation',
                        'icon' => 'fas fa-network-wired'
                    ],
                    [
                        'title' => 'Automation Systems',
                        'description' => 'Industrial automation and control',
                        'icon' => 'fas fa-robot'
                    ],
                    [
                        'title' => 'Data Management',
                        'description' => 'Database and information systems',
                        'icon' => 'fas fa-database'
                    ],
                    [
                        'title' => 'IoT Integration',
                        'description' => 'Internet of Things solutions',
                        'icon' => 'fas fa-satellite-dish'
                    ]
                ],
                'projects' => [
                    'SCADA Systems',
                    'PLC Programming',
                    'Network Infrastructure',
                    'Data Center Setup'
                ],
                'image' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'image_alt' => 'IT and system integration'
            ]
        ];
    }
    
    /**
     * API endpoint untuk services data
     */
    public function api()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/services');
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $this->getServicesData()
        ]);
    }
    
    /**
     * Get single service by slug (API)
     */
    public function getService($slug)
    {
        $services = $this->getServicesData();
        $service = null;
        
        foreach ($services as $s) {
            if ($s['slug'] === $slug) {
                $service = $s;
                break;
            }
        }
        
        if (!$service) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Service not found'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $service
        ]);
    }
}