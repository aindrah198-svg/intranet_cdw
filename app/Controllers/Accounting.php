<?php
namespace App\Controllers;

use App\Models\KaryawanModel;

class Accounting extends BaseController
{
    protected $karyawanModel;
    
    public function __construct()
    {
        $this->karyawanModel = new KaryawanModel();
        $this->checkAuth();
        $this->checkRole(['accounting']);
    }
    
    public function index()
    {
        // Get karyawan data if exists
        $karyawan = null;
        if (session()->get('karyawan_id')) {
            $karyawan = $this->karyawanModel->find(session()->get('karyawan_id'));
        }
        
        $data = [
            'title' => 'Dashboard Accounting - CDW Engineering',
            'subtitle' => 'Finance & Accounting Dashboard',
            'karyawan' => $karyawan,
            'active' => 'dashboard'
        ];
        
        return $this->renderView('accounting/dashboard/index', $data);
    }
    
    public function invoices()
    {
        $data = [
            'title' => 'Invoice Management - CDW Engineering',
            'active' => 'invoices'
        ];
        
        return $this->renderView('accounting/invoices/index', $data);
    }
    
    public function payroll()
    {
        $data = [
            'title' => 'Payroll Management - CDW Engineering',
            'active' => 'payroll'
        ];
        
        return $this->renderView('accounting/payroll/index', $data);
    }
    
    public function expenses()
    {
        $data = [
            'title' => 'Expense Management - CDW Engineering',
            'active' => 'expenses'
        ];
        
        return $this->renderView('accounting/expenses/index', $data);
    }
    
    public function reports()
    {
        $data = [
            'title' => 'Financial Reports - CDW Engineering',
            'active' => 'reports'
        ];
        
        return $this->renderView('accounting/reports/index', $data);
    }
}