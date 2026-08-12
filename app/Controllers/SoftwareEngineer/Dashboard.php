<?php

namespace App\Controllers\SoftwareEngineer;

use App\Models\SoftwareEngineer\SeSystemModel;
use App\Models\SoftwareEngineer\SeHostingDomainModel;
use App\Models\SoftwareEngineer\SeBugModel;
use App\Models\SoftwareEngineer\SeDeploymentModel;
use App\Models\SoftwareEngineer\SeTaskModel;

class Dashboard extends BaseSeController
{
    public function index()
    {
        $systemModel  = new SeSystemModel();
        $hostingModel = new SeHostingDomainModel();
        $bugModel     = new SeBugModel();
        $deployModel  = new SeDeploymentModel();
        $taskModel    = new SeTaskModel();

        $data = [
            'title'            => 'Dashboard Software Engineer - Intranet CDW',
            'active'           => 'dashboard',
            'total_systems'    => $systemModel->countAllResults(),
            'active_tasks'     => $taskModel->whereIn('status', ['todo', 'in_progress', 'review'])->countAllResults(),
            'critical_bugs'    => $bugModel->where('severity', 'critical')->whereIn('status', ['open', 'in_progress'])->countAllResults(),
            'expiring_alerts'  => $hostingModel->getExpiringAlerts(30),
            'recent_deploy'    => $deployModel->getDeploymentsWithSystem(),
            'open_bugs'        => $bugModel->whereIn('status', ['open', 'in_progress'])->findAll(),
            'systems'          => $systemModel->findAll()
        ];

        return view('software_engineer/dashboard/index', $data);
    }
}
