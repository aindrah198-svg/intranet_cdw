<?php

namespace App\Controllers\SoftwareEngineer;

use App\Models\AbsensiModel;
use App\Models\LaporanHarianModel;
use App\Models\KeluhanKaryawanModel;
use App\Models\CutiModel;
use App\Models\KaryawanModel;
use App\Models\UserModel;
use App\Models\SoftwareEngineer\SeTaskModel;

class Pribadi extends BaseSeController
{
    protected $absensiModel;
    protected $laporanModel;
    protected $keluhanModel;
    protected $cutiModel;
    protected $karyawanModel;
    protected $userModel;
    protected $taskModel;

    public function __construct()
    {
        $this->absensiModel  = new AbsensiModel();
        $this->laporanModel  = new LaporanHarianModel();
        $this->keluhanModel  = new KeluhanKaryawanModel();
        $this->cutiModel     = new CutiModel();
        $this->karyawanModel = new KaryawanModel();
        $this->userModel     = new UserModel();
        $this->taskModel     = new SeTaskModel();
    }

    public function absensi()
    {
        $karyawanId = session()->get('karyawan_id');
        $absensi    = $this->absensiModel->where('karyawan_id', $karyawanId)->orderBy('tanggal', 'DESC')->findAll();

        $data = [
            'title'   => 'Absensi Saya - Software Engineer',
            'active'  => 'pribadi',
            'sub'     => 'absensi',
            'absensi' => $absensi
        ];

        return view('software_engineer/pribadi/absensi', $data);
    }

    public function tugas()
    {
        $name  = session()->get('name') ?? session()->get('username');
        $tugas = $this->taskModel->where('assigned_to', $name)->findAll();

        $data = [
            'title'  => 'Tugas Saya - Software Engineer',
            'active' => 'pribadi',
            'sub'    => 'tugas',
            'tugas'  => $tugas
        ];

        return view('software_engineer/pribadi/tugas', $data);
    }

    public function laporanHarian()
    {
        $userId  = session()->get('user_id');
        $laporan = $this->laporanModel->where('user_id', $userId)->orderBy('tanggal', 'DESC')->findAll();

        $data = [
            'title'   => 'Laporan Kerja Harian Saya - Software Engineer',
            'active'  => 'pribadi',
            'sub'     => 'laporan-harian',
            'laporan' => $laporan
        ];

        return view('software_engineer/pribadi/laporan_harian', $data);
    }

    public function keluhan()
    {
        $karyawanId = session()->get('karyawan_id');
        $keluhan    = $this->keluhanModel->where('karyawan_id', $karyawanId)->findAll();

        $data = [
            'title'   => 'Keluhan Saya - Software Engineer',
            'active'  => 'pribadi',
            'sub'     => 'keluhan',
            'keluhan' => $keluhan
        ];

        return view('software_engineer/pribadi/keluhan', $data);
    }

    public function pengajuan()
    {
        $karyawanId = session()->get('karyawan_id');
        $cutiList   = $this->cutiModel->where('karyawan_id', $karyawanId)->findAll();

        $data = [
            'title'      => 'Form Pengajuan Saya - Software Engineer',
            'active'     => 'pribadi',
            'sub'        => 'pengajuan',
            'cuti_list'  => $cutiList
        ];

        return view('software_engineer/pribadi/pengajuan', $data);
    }

    public function slipGaji()
    {
        $db         = \Config\Database::connect();
        $karyawanId = session()->get('karyawan_id');

        $slips = [];
        if ($db->tableExists('penggajian_perhitungan')) {
            $slips = $db->table('penggajian_perhitungan')->where('karyawan_id', $karyawanId)->get()->getResultArray();
        }

        $data = [
            'title'  => 'Slip Gaji Saya - Software Engineer',
            'active' => 'pribadi',
            'sub'    => 'slip-gaji',
            'slips'  => $slips
        ];

        return view('software_engineer/pribadi/slip_gaji', $data);
    }

    public function profil()
    {
        $userId   = session()->get('user_id');
        $user     = $this->userModel->find($userId);
        $karyawan = null;

        if (!empty($user['karyawan_id'])) {
            $karyawan = $this->karyawanModel->find($user['karyawan_id']);
        }

        $data = [
            'title'    => 'Profil Saya - Software Engineer',
            'active'   => 'pribadi',
            'sub'      => 'profil',
            'user'     => $user,
            'karyawan' => $karyawan
        ];

        return view('software_engineer/pribadi/profil', $data);
    }

    public function updateProfil()
    {
        $userId = session()->get('user_id');
        $name   = $this->request->getPost('name');
        $email  = $this->request->getPost('email');
        $pass   = $this->request->getPost('password');

        $updateData = [
            'name'  => $name,
            'email' => $email
        ];

        if ($pass) {
            $updateData['password'] = password_hash($pass, PASSWORD_DEFAULT);
        }

        $this->userModel->update($userId, $updateData);
        session()->set('name', $name);
        session()->set('email', $email);

        return redirect()->to(base_url('software-engineer/pribadi/profil'))->with('success', 'Profil Anda berhasil diperbarui.');
    }
}
