<?php

namespace App\Controllers\SoftwareEngineer;

use App\Controllers\BaseController;

class BaseSeController extends BaseController
{
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        // Self-healing database check: Ensure SE tables exist on first request if CLI was restricted
        $db = \Config\Database::connect();
        if (!$db->tableExists('se_systems')) {
            $migration = new \App\Database\Migrations\CreateSoftwareEngineerTables();
            $migration->up();
            
            $seeder = new \App\Database\Seeds\SoftwareEngineerSeeder();
            $seeder->run();
        }
    }
}
