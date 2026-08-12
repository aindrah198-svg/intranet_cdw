<?php

namespace App\Controllers\Direktur\Karyawan;

use App\Controllers\BaseController;
use App\Models\KeluhanKaryawanModel;
use App\Models\KaryawanModel;

class KeluhanController extends BaseController
{
    protected $keluhanModel;
    protected $karyawanModel;

    public function __construct()
    {
        $this->keluhanModel  = new KeluhanKaryawanModel();
        $this->karyawanModel = new KaryawanModel();
    }

    public function index()
    {
        $filterStatus   = $this->request->getGet('status');
        $filterKategori = $this->request->getGet('kategori');
        $search         = $this->request->getGet('q');

        $keluhanList = $this->keluhanModel->getFilteredKeluhan($filterStatus, $filterKategori, $search);

        $data = [
            'title'          => 'Keluhan Karyawan',
            'active'         => 'karyawan',
            'keluhanList'    => $keluhanList,
            'statistik'      => $this->keluhanModel->getStatistik(),
            'filterAktif'    => $filterStatus,
            'kategoriAktif'  => $filterKategori,
            'searchAktif'    => $search,
            'kategoriList'   => $this->keluhanModel->kategoriList,
        ];

        return view('direktur/karyawan/keluhan/index', $data);
    }

    public function tambah()
    {
        $data = [
            'title'        => 'Tambah Keluhan Karyawan',
            'active'       => 'karyawan',
            'karyawanList' => $this->karyawanModel->orderBy('nama_lengkap', 'ASC')->findAll(),
            'kategoriList' => $this->keluhanModel->kategoriList,
        ];
        return view('direktur/karyawan/keluhan/tambah', $data);
    }

    public function simpan()
    {
        $karyawanId = $this->request->getPost('karyawan_id');
        $judul      = trim($this->request->getPost('judul') ?? '');
        $kategori   = $this->request->getPost('kategori');
        $deskripsi  = trim($this->request->getPost('deskripsi') ?? '');
        $tanggal    = $this->request->getPost('tanggal') ?: date('Y-m-d');

        if (empty($karyawanId) || empty($judul) || empty($kategori) || empty($deskripsi)) {
            return redirect()->back()->withInput()->with('error', 'Semua bidang bertanda bintang (*) wajib diisi.');
        }

        $inserted = $this->keluhanModel->insert([
            'karyawan_id' => $karyawanId,
            'tanggal'     => $tanggal,
            'kategori'    => $kategori,
            'judul'       => $judul,
            'deskripsi'   => $deskripsi,
            'status'      => 'baru',
        ]);

        if (!$inserted) {
            return redirect()->back()->withInput()->with('error', 'Gagal mencatat keluhan. Silakan coba lagi.');
        }

        $id = $this->keluhanModel->getInsertID();
        return redirect()->to(base_url('direktur/karyawan/keluhan/detail/'.$id))->with('success', 'Keluhan karyawan berhasil dicatat.');
    }

    public function detail($id)
    {
        $keluhan = $this->keluhanModel->getDetailWithKaryawan($id);
        if (!$keluhan) {
            return redirect()->to(base_url('direktur/karyawan/keluhan'))->with('error', 'Data keluhan tidak ditemukan.');
        }
        $data = [
            'title'   => 'Detail Keluhan - ' . esc($keluhan['nama_lengkap']),
            'active'  => 'karyawan',
            'keluhan' => $keluhan,
        ];
        return view('direktur/karyawan/keluhan/detail', $data);
    }

    public function tanggapi($id)
    {
        $keluhan = $this->keluhanModel->find($id);
        if (!$keluhan) {
            return redirect()->to(base_url('direktur/karyawan/keluhan'))->with('error', 'Data keluhan tidak ditemukan.');
        }

        $tanggapan = trim($this->request->getPost('tanggapan') ?? '');
        $status    = $this->request->getPost('status');

        if (empty($tanggapan) || empty($status)) {
            return redirect()->back()->with('error', 'Tanggapan dan Status baru wajib diisi.');
        }

        $responderId = session('user_id') ?: (session('id') ?: null);

        $this->keluhanModel->update($id, [
            'status'             => $status,
            'tanggapan'          => $tanggapan,
            'ditanggapi_oleh'    => $responderId,
            'tanggal_tanggapan'  => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(base_url('direktur/karyawan/keluhan/detail/'.$id))->with('success', 'Tanggapan direktur berhasil disimpan.');
    }

    public function delete($id)
    {
        $keluhan = $this->keluhanModel->find($id);
        if (!$keluhan) {
            return redirect()->to(base_url('direktur/karyawan/keluhan'))->with('error', 'Data keluhan tidak ditemukan.');
        }

        $this->keluhanModel->delete($id);
        return redirect()->to(base_url('direktur/karyawan/keluhan'))->with('success', 'Data keluhan berhasil dihapus.');
    }
}
