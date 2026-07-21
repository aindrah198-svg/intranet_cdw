<?php
namespace App\Controllers\Direktur\Dashboard;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        // Data sederhana untuk view
        $data = [
            'title' => 'Dashboard Direktur',
            'active' => 'dashboard',
            'user' => [
                'name' => session()->get('name') ?: 'Direktur',
                'role' => session()->get('role') ?: 'direktur',
                'email' => session()->get('email') ?: 'direktur@cdw-engineering.com'
            ]
        ];
        
        return view('direktur/dashboard/index', $data);
    }
}