<?php

namespace App\Controllers;

class Products extends BaseController
{
    public function index()
    {
  // Cek apakah ada anchor (section) dari URL
    $section = $this->request->getGet('section') ?? '';
    
    $data = [
        'title' => 'Our Products | CDW Engineering',
        'active' => 'products',
        'section' => $section, // Tambahkan ini
        'meta_description' => 'CDW Engineering offers high-quality petroleum equipment, manufacturing components, electrical systems, and IT integration solutions for industrial applications.',
        'meta_keywords' => 'petroleum equipment, manufacturing components, electrical systems, IT integration, industrial products',
        'products' => $this->getProductsData()
    ];
    
    return view('templates/header', $data)
        . view('templates/nav')
        . view('products')
        . view('templates/footer');
}
    
    public function detail($slug)
    {
        $products = $this->getProductsData();
        $product = null;
        
        // Cari product berdasarkan slug
        foreach ($products as $p) {
            if ($p['slug'] === $slug) {
                $product = $p;
                break;
            }
        }
        
        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        
        $data = [
            'title' => $product['name'] . ' | CDW Engineering',
            'active' => 'products',
            'meta_description' => $product['meta_description'],
            'meta_keywords' => $product['meta_keywords'],
            'product' => $product,
            'other_products' => array_filter($products, function($p) use ($product) {
                return $p['slug'] !== $product['slug'];
            })
        ];
        
        return view('templates/header', $data)
            . view('templates/nav')
            . view('product_detail')
            . view('templates/footer');
    }
    
    /**
     * Get all products data
     */
    private function getProductsData()
    {
        return [
            [
                'slug' => 'petroleum',
                'name' => 'Petroleum Equipment',
                'short_name' => 'Petroleum',
                'icon' => 'fas fa-gas-pump',
                'color' => 'primary',
                'short_description' => 'Complete petroleum equipment solutions for fuel stations and industrial applications',
                'full_description' => 'We provide comprehensive petroleum equipment solutions including fuel dispensers, storage tanks, piping systems, and monitoring equipment. Our products meet international standards and are designed for safety, efficiency, and durability.',
                'meta_description' => 'High-quality petroleum equipment including fuel dispensers, storage tanks, and monitoring systems for fuel stations.',
                'meta_keywords' => 'petroleum equipment, fuel dispensers, storage tanks, fuel station equipment, petroleum systems',
                'applications' => [
                    'Fuel Stations (PERTASHOP)',
                    'Fuel Depots',
                    'Industrial Fuel Systems',
                    'Aviation Fuel Equipment'
                ],
                'features' => [
                    [
                        'title' => 'Fuel Dispensers',
                        'description' => 'Modern electronic dispensers with precise measurement',
                        'icon' => 'fas fa-gas-pump'
                    ],
                    [
                        'title' => 'Storage Tanks',
                        'description' => 'Double-walled tanks with leak detection',
                        'icon' => 'fas fa-oil-can'
                    ],
                    [
                        'title' => 'Piping Systems',
                        'description' => 'Corrosion-resistant piping with safety valves',
                        'icon' => 'fas fa-pipe'
                    ],
                    [
                        'title' => 'Monitoring Systems',
                        'description' => 'Real-time inventory and leak monitoring',
                        'icon' => 'fas fa-desktop'
                    ]
                ],
                'specifications' => [
                    'Compliance: International Standards',
                    'Material: Stainless Steel / Carbon Steel',
                    'Pressure Rating: Up to 10 bar',
                    'Temperature Range: -20°C to 60°C'
                ],
                'image' => 'https://images.unsplash.com/photo-1581092160562-40aa08e78837?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'image_alt' => 'Petroleum equipment installation',
                'gallery' => [
                    'https://images.unsplash.com/photo-1581092160562-40aa08e78837?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                ]
            ],
            [
                'slug' => 'manufacturing',
                'name' => 'Manufacturing Components',
                'short_name' => 'Manufacturing',
                'icon' => 'fas fa-industry',
                'color' => 'warning',
                'short_description' => 'Precision manufacturing components for industrial machinery and equipment',
                'full_description' => 'We manufacture and supply high-precision components for various industrial applications. Our products include machined parts, fabricated structures, and specialized components for manufacturing and processing industries.',
                'meta_description' => 'Precision manufacturing components including machined parts, fabricated structures, and industrial components.',
                'meta_keywords' => 'manufacturing components, machined parts, fabricated structures, industrial components',
                'applications' => [
                    'Machinery Components',
                    'Structural Steel Fabrication',
                    'Conveyor Systems',
                    'Processing Equipment'
                ],
                'features' => [
                    [
                        'title' => 'Machined Parts',
                        'description' => 'CNC machined components with tight tolerances',
                        'icon' => 'fas fa-cogs'
                    ],
                    [
                        'title' => 'Fabricated Structures',
                        'description' => 'Custom steel structures and frames',
                        'icon' => 'fas fa-cube'
                    ],
                    [
                        'title' => 'Assembly Components',
                        'description' => 'Pre-assembled modules and sub-assemblies',
                        'icon' => 'fas fa-th-large'
                    ],
                    [
                        'title' => 'Quality Testing',
                        'description' => 'Rigorous quality control and testing',
                        'icon' => 'fas fa-vial'
                    ]
                ],
                'specifications' => [
                    'Material: Steel, Aluminum, Stainless Steel',
                    'Tolerance: ±0.01mm',
                    'Surface Finish: Ra 0.8 - 3.2',
                    'Certification: ISO 9001:2015'
                ],
                'image' => 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'image_alt' => 'Manufacturing components production',
                'gallery' => [
                    'https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                ]
            ],
            [
                'slug' => 'electrical',
                'name' => 'Electrical Components',
                'short_name' => 'Electrical',
                'icon' => 'fas fa-plug',
                'color' => 'success',
                'short_description' => 'Comprehensive electrical components and systems for industrial applications',
                'full_description' => 'We supply and install a wide range of electrical components including control panels, switchgear, transformers, and power distribution systems. Our products ensure reliable and safe electrical operations.',
                'meta_description' => 'Electrical components including control panels, switchgear, transformers, and power distribution systems.',
                'meta_keywords' => 'electrical components, control panels, switchgear, transformers, power distribution',
                'applications' => [
                    'Control Panels',
                    'Power Distribution',
                    'Motor Control Centers',
                    'Industrial Wiring'
                ],
                'features' => [
                    [
                        'title' => 'Control Panels',
                        'description' => 'Custom designed PLC and relay panels',
                        'icon' => 'fas fa-sliders-h'
                    ],
                    [
                        'title' => 'Switchgear',
                        'description' => 'HV and LV switchgear with protection',
                        'icon' => 'fas fa-toggle-on'
                    ],
                    [
                        'title' => 'Transformers',
                        'description' => 'Power transformers and distribution units',
                        'icon' => 'fas fa-bolt'
                    ],
                    [
                        'title' => 'Cable Systems',
                        'description' => 'Industrial cabling and wiring solutions',
                        'icon' => 'fas fa-network-wired'
                    ]
                ],
                'specifications' => [
                    'Voltage: Up to 35kV',
                    'Protection: IP55 - IP65',
                    'Standards: IEC, IEEE',
                    'Certification: CE, UL'
                ],
                'image' => 'https://images.unsplash.com/photo-1621905252507-b35492cc74b4?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'image_alt' => 'Electrical control panel installation',
                'gallery' => [
                    'https://images.unsplash.com/photo-1621905252507-b35492cc74b4?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                ]
            ],
            [
                'slug' => 'it-products',
                'name' => 'IT & System Integration',
                'short_name' => 'IT & Integration',
                'icon' => 'fas fa-laptop-code',
                'color' => 'info',
                'short_description' => 'Advanced IT solutions and system integration for industrial automation',
                'full_description' => 'We provide cutting-edge IT solutions including SCADA systems, PLC programming, network infrastructure, and IoT integration for industrial automation and control systems.',
                'meta_description' => 'IT solutions including SCADA systems, PLC programming, network infrastructure, and IoT integration.',
                'meta_keywords' => 'IT solutions, SCADA systems, PLC programming, network infrastructure, IoT integration',
                'applications' => [
                    'SCADA Systems',
                    'PLC Programming',
                    'Network Infrastructure',
                    'IoT Solutions'
                ],
                'features' => [
                    [
                        'title' => 'SCADA Systems',
                        'description' => 'Supervisory control and data acquisition',
                        'icon' => 'fas fa-desktop'
                    ],
                    [
                        'title' => 'PLC Systems',
                        'description' => 'Programmable logic controllers and programming',
                        'icon' => 'fas fa-microchip'
                    ],
                    [
                        'title' => 'Network Solutions',
                        'description' => 'Industrial Ethernet and network infrastructure',
                        'icon' => 'fas fa-server'
                    ],
                    [
                        'title' => 'IoT Integration',
                        'description' => 'Internet of Things sensors and systems',
                        'icon' => 'fas fa-satellite-dish'
                    ]
                ],
                'specifications' => [
                    'Protocols: Modbus, Profibus, Ethernet/IP',
                    'Connectivity: Wireless, Fiber Optic, Ethernet',
                    'Security: Industrial Firewalls, VPN',
                    'Support: 24/7 Technical Support'
                ],
                'image' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'image_alt' => 'IT system integration',
                'gallery' => [
                    'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                ]
            ]
        ];
    }
    
    /**
     * API endpoint untuk products data
     */
    public function api()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/products');
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $this->getProductsData()
        ]);
    }
    
    /**
     * Get single product by slug (API)
     */
    public function getProduct($slug)
    {
        $products = $this->getProductsData();
        $product = null;
        
        foreach ($products as $p) {
            if ($p['slug'] === $slug) {
                $product = $p;
                break;
            }
        }
        
        if (!$product) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Product not found'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $product
        ]);
    }
    
    /**
     * Get product categories (API)
     */
    public function categories()
    {
        $categories = [
            ['id' => 'petroleum', 'name' => 'Petroleum Equipment', 'count' => 1],
            ['id' => 'manufacturing', 'name' => 'Manufacturing Components', 'count' => 1],
            ['id' => 'electrical', 'name' => 'Electrical Components', 'count' => 1],
            ['id' => 'it-products', 'name' => 'IT & System Integration', 'count' => 1]
        ];
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $categories
        ]);
    }
}