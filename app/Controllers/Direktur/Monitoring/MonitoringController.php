<?php
// app/Controllers/Direktur/Monitoring/MonitoringController.php

namespace App\Controllers\Direktur\Monitoring;

use App\Controllers\BaseController;

class MonitoringController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Monitoring Kinerja & Absensi',
            'active' => 'monitoring'
        ];

        return view('direktur/monitoring/index', $data);
    }
}
