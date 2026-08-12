<?php

namespace App\Controllers\Direktur\Proyek;

use App\Controllers\BaseController;
use App\Models\LaporanHarianModel;
use App\Models\UserModel;
use CodeIgniter\Files\File;

class LaporanHarianController extends BaseController
{
    protected $laporanModel;
    protected $userModel;

    public function __construct()
    {
        $this->laporanModel = new LaporanHarianModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $search  = $this->request->getGet('search');
        $status  = $this->request->getGet('status');
        $tanggal = $this->request->getGet('tanggal');

        $filters = [];
        if (!empty($status)) $filters['status'] = $status;
        if (!empty($tanggal)) $filters['tanggal'] = $tanggal;

        $laporan = $this->laporanModel->getLaporanWithKaryawan($filters);
        
        // Normalize status if empty or 'Terkirim'
        foreach ($laporan as &$lap) {
            if (empty($lap['status']) || $lap['status'] === 'Terkirim') {
                $lap['status'] = 'menunggu_review';
            }
        }
        unset($lap);

        $data = [
            'title'   => 'Laporan Kerja Harian',
            'laporan' => $laporan
        ];
        
        return view('direktur/proyek/laporan_harian', $data);
    }

    public function simpan()
    {
        $karyawan_id = session()->get('karyawan_id') ?? session()->get('id');
        
        $rules = [
            'tanggal'   => 'required|valid_date',
            'judul'     => 'required',
            'deskripsi' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $lampiran = $this->request->getFile('lampiran');
        $namaLampiran = null;
        
        if ($lampiran && $lampiran->isValid() && !$lampiran->hasMoved()) {
            $namaLampiran = $lampiran->getRandomName();
            $lampiran->move(FCPATH . 'uploads/laporan', $namaLampiran);
        }

        $this->laporanModel->insert([
            'karyawan_id' => $karyawan_id,
            'tanggal'     => $this->request->getPost('tanggal'),
            'judul'       => $this->request->getPost('judul'),
            'deskripsi'   => $this->request->getPost('deskripsi'),
            'lampiran'    => $namaLampiran,
            'status'      => 'menunggu_review'
        ]);

        return redirect()->to(base_url('direktur/proyek/laporan-harian'))->with('success', 'Laporan berhasil disimpan.');
    }

    public function monitoring()
    {
        $filter_tanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');
        $filter_status = $this->request->getGet('status');
        
        $filters = [];
        if ($filter_tanggal) $filters['tanggal'] = $filter_tanggal;
        if ($filter_status) $filters['status'] = $filter_status;
        
        $laporan = $this->laporanModel->getLaporanWithKaryawan($filters);
        
        $data = [
            'title' => 'Monitoring Laporan Karyawan',
            'laporan' => $laporan,
            'filter_tanggal' => $filter_tanggal,
            'filter_status' => $filter_status
        ];
        
        return view('direktur/proyek/monitoring_laporan', $data);
    }

    public function approve()
    {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');
        $komentar = $this->request->getPost('komentar');
        
        $this->laporanModel->update($id, [
            'status' => $status,
            'komentar_direktur' => $komentar,
            'direview_oleh' => session()->get('id')
        ]);
        
        return redirect()->back()->with('success', 'Status laporan berhasil diupdate.');
    }
}
