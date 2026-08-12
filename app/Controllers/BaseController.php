<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $db;
    protected $session;
    
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        // Load helpers
        $this->helpers = ['form', 'url', 'cookie', 'text'];
        
        // Load services
        $this->db = \Config\Database::connect();
        $this->session = service('session');
    }
    
    protected function checkAuth()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }
    }
    
    /**
     * Check user role
     */
    protected function checkRole($allowedRoles = [])
    {
        $userRole = strtolower(session()->get('role'));
        $allowedRoles = array_map('strtolower', $allowedRoles);
        
        if (!in_array($userRole, $allowedRoles)) {
            return redirect()->to($this->getDashboardUrl())->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }
        
        return true;
    }
    
    /**
     * Get dashboard URL based on role
     */
    protected function getDashboardUrl()
    {
        $role = strtolower(session()->get('role'));
        
        switch ($role) {
            case 'hrd':
                return base_url('hrd');
            case 'admin':
                return base_url('admin');
            case 'teknisi':
                return base_url('teknisi');
            case 'direktur':
                return base_url('direktur');
            case 'accounting':
                return base_url('accounting');
            case 'sales':
            case 'marketing':
                return base_url('sales');
            case 'staff':
            default:
                return base_url('staff');
        }
    }
    
    /**
     * Set SweetAlert flash message
     */
    protected function setSweetAlert($type, $title, $message = '')
    {
        session()->setFlashdata('sweetalert', [
            'type' => $type,
            'title' => $title,
            'message' => $message
        ]);
    }
    
    /**
     * Get current user data
     */
    protected function getCurrentUser()
    {
        return [
            'id' => session()->get('user_id'),
            'username' => session()->get('username'),
            'name' => session()->get('name'),
            'email' => session()->get('email'),
            'role' => session()->get('role'),
            'karyawan_id' => session()->get('karyawan_id')
        ];
    }
    
    /**
     * Render view with common data
     */
    protected function renderView($view, $data = [])
    {
        // Add common data
        $commonData = [
            'user' => $this->getCurrentUser(),
            'sweetalert' => session()->getFlashdata('sweetalert')
        ];
        
        return view($view, array_merge($commonData, $data));
    }
}