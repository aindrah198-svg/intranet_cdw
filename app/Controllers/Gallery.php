<?php
// app/Controllers/Gallery.php

namespace App\Controllers;

class Gallery extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Project Gallery | CDW Engineering',
            'active' => 'gallery',
            'meta_description' => 'Explore our portfolio of engineering projects and construction works.',
            'current_year' => date('Y')
        ];
        
        // Ambil data project untuk ditampilkan di view
        $data['projects'] = $this->getInitialProjects();
        
        echo view('templates/header', $data);
        echo view('templates/nav', $data);
        echo view('gallery', $data); // Kembali ke gallery.php asli
        echo view('templates/footer');
    }
    
    // API untuk load lebih banyak project via AJAX
    public function getMoreProjects()
    {
        // Cek apakah ini request AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }
        
        $page = $this->request->getGet('page') ?? 1;
        $category = $this->request->getGet('category') ?? 'all';
        
        $projects = $this->getProjectData($page, $category);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $projects['items'],
            'hasMore' => $projects['hasMore']
        ]);
    }
    
    // Ambil data awal untuk ditampilkan di halaman pertama
    private function getInitialProjects()
    {
        return $this->getProjectData(1, 'all')['items'];
    }
    
    private function getProjectData($page = 1, $category = 'all')
    {
        // Data project lengkap
        $allProjects = [
            [
                'id' => 1,
                'title' => 'Gas Station Construction',
                'category' => 'construction',
                'category_display' => 'Construction',
                'image' => 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'description' => 'Complete construction of Pertamina gas station with modern facilities',
                'location' => 'Jakarta',
                'date' => 'Jan 2023'
            ],
            [
                'id' => 2,
                'title' => 'Industrial Plant Design',
                'category' => 'engineering',
                'category_display' => 'Engineering',
                'image' => 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'description' => 'Comprehensive engineering design for manufacturing facility',
                'location' => 'Surabaya',
                'date' => 'Mar 2023'
            ],
            [
                'id' => 3,
                'title' => 'Pump System Installation',
                'category' => 'mechanical',
                'category_display' => 'Mechanical',
                'image' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'description' => 'High-pressure pump installation for industrial application',
                'location' => 'Bandung',
                'date' => 'May 2023'
            ],
            [
                'id' => 4,
                'title' => 'Control Panel Installation',
                'category' => 'electrical',
                'category_display' => 'Electrical',
                'image' => 'https://images.unsplash.com/photo-1621905252507-b35492cc74b4?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'description' => 'Electrical control systems for automated manufacturing',
                'location' => 'Semarang',
                'date' => 'Jul 2023'
            ],
            [
                'id' => 5,
                'title' => 'Fuel Management System',
                'category' => 'petroleum',
                'category_display' => 'Petroleum',
                'image' => 'https://images.unsplash.com/photo-1581092160562-40aa08e78837?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'description' => 'Advanced fuel dispensing and monitoring system',
                'location' => 'Bali',
                'date' => 'Sep 2023'
            ],
            [
                'id' => 6,
                'title' => 'Industrial Warehouse',
                'category' => 'construction',
                'category_display' => 'Construction',
                'image' => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'description' => 'Large-scale warehouse construction for logistics company',
                'location' => 'Medan',
                'date' => 'Nov 2023'
            ],
            [
                'id' => 7,
                'title' => 'CAD Design Project',
                'category' => 'engineering',
                'category_display' => 'Engineering',
                'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'description' => '3D modeling and engineering drawings',
                'location' => 'Makassar',
                'date' => 'Dec 2023'
            ],
            [
                'id' => 8,
                'title' => 'Conveyor System',
                'category' => 'mechanical',
                'category_display' => 'Mechanical',
                'image' => 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'description' => 'Automated conveyor system for packaging',
                'location' => 'Yogyakarta',
                'date' => 'Feb 2024'
            ],
            [
                'id' => 9,
                'title' => 'Power Distribution System',
                'category' => 'electrical',
                'category_display' => 'Electrical',
                'image' => 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'description' => 'High-voltage power distribution',
                'location' => 'Palembang',
                'date' => 'Apr 2024'
            ]
        ];
        
        // Filter by category
        if ($category !== 'all') {
            $filtered = array_filter($allProjects, function($p) use ($category) {
                return $p['category'] === $category;
            });
            $projects = array_values($filtered);
        } else {
            $projects = $allProjects;
        }
        
        // Pagination
        $perPage = 6;
        $offset = ($page - 1) * $perPage;
        $items = array_slice($projects, $offset, $perPage);
        $hasMore = count($projects) > ($offset + $perPage);
        
        return [
            'items' => $items,
            'hasMore' => $hasMore
        ];
    }
}