<?php
namespace App\Controllers;

use App\Models\KaryawanModel;

class Direktur extends BaseController
{
    protected $karyawanModel;
    
    public function __construct()
    {
        $this->karyawanModel = new KaryawanModel();
        $this->checkAuth();
        $this->checkRole(['direktur']);
    }
    
    public function index()
    {
        // Get karyawan data if exists
        $karyawan = null;
        if (session()->get('karyawan_id')) {
            $karyawan = $this->karyawanModel->find(session()->get('karyawan_id'));
        }
        
        $data = [
            'title' => 'Dashboard Direktur - CDW Engineering',
            'subtitle' => 'Executive Dashboard',
            'karyawan' => $karyawan,
            'active' => 'dashboard'
        ];
        
        return $this->renderView('direktur/dashboard/index', $data);
    }
    
    public function reports()
    {
        $data = [
            'title' => 'Executive Reports - CDW Engineering',
            'active' => 'reports'
        ];
        
        return $this->renderView('direktur/reports/index', $data);
    }
    
    public function approval()
    {
        $data = [
            'title' => 'Approval System - CDW Engineering',
            'active' => 'approval'
        ];
        
        return $this->renderView('direktur/approval/index', $data);
    }
    
    public function financial()
    {
        $data = [
            'title' => 'Financial Overview - CDW Engineering',
            'active' => 'financial'
        ];
        
        return $this->renderView('direktur/financial/index', $data);
    }
    
    public function performance()
    {
        $data = [
            'title' => 'Performance Metrics - CDW Engineering',
            'active' => 'performance'
        ];
        
        return $this->renderView('direktur/performance/index', $data);
    }
}