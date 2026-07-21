<?php
namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // Data untuk partners (logo dari direktori lokal)
        $partners = [
            [
                'name' => 'Tominaga Manufacturing Co.',
                'logo' => base_url('assets/img/logo/Tominaga-Manufacturing-Co.-Logo.png'),
                'country' => 'Japan'
            ],
            [
                'name' => 'Pertamina',
                'logo' => base_url('assets/img/logo/pertamina-logo.png'),
                'country' => 'Indonesia'
            ],
            [
                'name' => 'Censtar',
                'logo' => base_url('assets/img/logo/censtar-logo.png'),
                'country' => 'China'
            ],
            [
                'name' => 'Bever Innovations',
                'logo' => base_url('assets/img/logo/bever-innovations-logo.png'),
                'country' => 'Germany'
            ],
            [
                'name' => 'Windbell',
                'logo' => base_url('assets/img/logo/windbell-logo.jpg'),
                'country' => 'International'
            ],
            [
                'name' => 'ENE Systems',
                'logo' => base_url('assets/img/logo/ene-logo.jpg'),
                'country' => 'Japan'
            ],
            [
                'name' => 'LMW',
                'logo' => base_url('assets/img/logo/lmw-logo.jpg'),
                'country' => 'International'
            ]
        ];
        
        $data = [
            'title' => 'CDW Engineering - Home',
            'active' => 'home', // Sangat penting untuk navbar!
            'partners' => $partners
        ];
        
        // Load view dengan urutan yang benar
        echo view('templates/header', $data);
        echo view('templates/nav', $data); // Kirim data ke nav
        echo view('home', $data);
        echo view('templates/footer');
    }
}