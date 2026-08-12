<?php

namespace App\Controllers\SoftwareEngineer;

use App\Models\CutiModel;

class Pengajuan extends BaseSeController
{
    protected $cutiModel;

    public function __construct()
    {
        $this->cutiModel = new CutiModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();
        
        $karyawanId = session()->get('karyawan_id');
        $username   = session()->get('username');

        $cutiList = $this->cutiModel->where('karyawan_id', $karyawanId)->findAll();
        
        $pengajuanAlat = [];
        if ($db->tableExists('pengajuan')) {
            $pengajuanAlat = $db->table('pengajuan')->where('karyawan_id', $karyawanId)->get()->getResultArray();
        }

        $data = [
            'title'          => 'Semua Pengajuan - Software Engineer',
            'active'         => 'pengajuan',
            'sub'            => 'semua',
            'cuti_list'      => $cutiList,
            'pengajuan_alat' => $pengajuanAlat
        ];

        return view('software_engineer/pengajuan/index', $data);
    }

    public function permintaanAlat()
    {
        $db = \Config\Database::connect();
        $karyawanId = session()->get('karyawan_id');

        $pengajuanAlat = [];
        if ($db->tableExists('pengajuan')) {
            $pengajuanAlat = $db->table('pengajuan')->where('karyawan_id', $karyawanId)->get()->getResultArray();
        }

        $data = [
            'title'          => 'Permintaan Alat / Software / Lisensi - Software Engineer',
            'active'         => 'pengajuan',
            'sub'            => 'permintaan-alat',
            'pengajuan_alat' => $pengajuanAlat
        ];

        return view('software_engineer/pengajuan/permintaan_alat', $data);
    }

    public function storePermintaanAlat()
    {
        $db = \Config\Database::connect();
        if ($db->tableExists('pengajuan')) {
            $db->table('pengajuan')->insert([
                'karyawan_id'      => session()->get('karyawan_id'),
                'jenis_pengajuan'  => 'Lisensi / Software / Tools',
                'nama_barang_tools' => $this->request->getPost('nama_item'),
                'alasan'           => $this->request->getPost('alasan'),
                'estimasi_biaya'   => $this->request->getPost('estimasi_biaya') ?: 0,
                'status'           => 'Pending',
                'created_at'       => date('Y-m-d H:i:s')
            ]);
        }

        return redirect()->to(base_url('software-engineer/pengajuan/permintaan-alat'))->with('success', 'Pengajuan alat/software/lisensi berhasil dikirim.');
    }

    public function cuti()
    {
        $karyawanId = session()->get('karyawan_id');
        $cutiList   = $this->cutiModel->where('karyawan_id', $karyawanId)->findAll();

        $data = [
            'title'     => 'Pengajuan Cuti - Software Engineer',
            'active'    => 'pengajuan',
            'sub'       => 'cuti',
            'cuti_list' => $cutiList
        ];

        return view('software_engineer/pengajuan/cuti', $data);
    }

    public function storeCuti()
    {
        $this->cutiModel->save([
            'karyawan_id'   => session()->get('karyawan_id'),
            'jenis_cuti'    => $this->request->getPost('jenis_cuti'),
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai'),
            'alasan'        => $this->request->getPost('alasan'),
            'status'        => 'Pending'
        ]);

        return redirect()->to(base_url('software-engineer/pengajuan/cuti'))->with('success', 'Form pengajuan cuti berhasil dikirim.');
    }
}
