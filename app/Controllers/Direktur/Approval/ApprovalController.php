<?php
// app/Controllers/Direktur/Approval/ApprovalController.php

namespace App\Controllers\Direktur\Approval;

use App\Controllers\BaseController;

class ApprovalController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Approval Berkas',
            'active' => 'approval',
            // Di sini nanti bisa ditambahkan query untuk menghitung total pending per kategori
            'pending_counts' => [
                'cuti' => 0, // dummy,
                'spk' => 0,
                'kasbon' => 0,
                'dokumen' => 0,
                'pembelian' => 0,
                'surat_jalan' => 0,
                'izin' => 0,
                'bast' => 0
            ]
        ];

        return view('direktur/approval/index', $data);
    }
}
