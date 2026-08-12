<?php

namespace App\Controllers\Direktur\Karyawan;

use App\Controllers\BaseController;
use App\Models\SuratKaryawanModel;
use App\Models\KaryawanModel;

class SuratController extends BaseController
{
    protected $suratModel;
    protected $karyawanModel;

    public function __construct()
    {
        $this->suratModel    = new SuratKaryawanModel();
        $this->karyawanModel = new KaryawanModel();
    }

    public function index()
    {
        $filter = $this->request->getGet('jenis');
        $query  = $this->suratModel->select('surat_karyawan.*, karyawan.nama_lengkap, karyawan.nik, karyawan.divisi')
                                   ->join('karyawan', 'karyawan.id = surat_karyawan.karyawan_id', 'left')
                                   ->orderBy('surat_karyawan.tanggal_surat', 'DESC');
        if ($filter) {
            $query->where('surat_karyawan.jenis_surat', $filter);
        }

        $allSurat = $query->findAll();

        $data = [
            'title'            => 'Surat Karyawan',
            'active'           => 'karyawan',
            'user'             => session()->get('user') ?? ['name' => session()->get('name') ?? 'Direktur', 'role' => 'direktur'],
            'suratList'        => $allSurat,
            'jenisList'        => $this->suratModel->jenisSurat,
            'paperSizes'       => $this->suratModel->paperSizes,
            'filterAktif'      => $filter,
            'countTotal'       => count($allSurat),
            'countDiterbitkan' => count(array_filter($allSurat, fn($s) => ($s['status'] ?? '') === 'diterbitkan')),
            'countDraft'       => count(array_filter($allSurat, fn($s) => ($s['status'] ?? '') === 'draft')),
            'countDibatalkan'   => count(array_filter($allSurat, fn($s) => ($s['status'] ?? '') === 'dibatalkan')),
            'logoBase64'       => $this->suratModel->getCompanyLogoBase64(),
        ];
        return view('direktur/karyawan/surat/index', $data);
    }

    public function tambah()
    {
        $karyawanList = [];
        try {
            $builder = $this->karyawanModel->orderBy('nama_lengkap', 'ASC');
            if ($this->karyawanModel->allowedFields && in_array('deleted_at', $this->karyawanModel->allowedFields)) {
                $builder->where('deleted_at', null);
            }
            $karyawanList = $builder->findAll();
        } catch (\Throwable $t) {
            $karyawanList = $this->karyawanModel->orderBy('nama_lengkap', 'ASC')->findAll();
        }

        $data = [
            'title'        => 'Buat Surat Baru',
            'active'       => 'karyawan',
            'user'         => session()->get('user') ?? ['name' => session()->get('name') ?? 'Direktur', 'role' => 'direktur'],
            'karyawanList' => $karyawanList,
            'jenisList'    => $this->suratModel->jenisSurat,
            'paperSizes'   => $this->suratModel->paperSizes,
            'logoBase64'   => $this->suratModel->getCompanyLogoBase64(),
        ];
        return view('direktur/karyawan/surat/tambah', $data);
    }

    public function simpan()
    {
        $jenis = $this->request->getPost('jenis_surat');
        if ($jenis === 'Lainnya' && $this->request->getPost('jenis_surat_custom')) {
            $jenis = trim($this->request->getPost('jenis_surat_custom'));
        }

        $karyawanIdRaw = $this->request->getPost('karyawan_id');
        $karyawanId    = (!empty($karyawanIdRaw) && is_numeric($karyawanIdRaw)) ? (int)$karyawanIdRaw : null;
        $tanggal     = $this->request->getPost('tanggal_surat');
        $perihal     = $this->request->getPost('perihal');

        if (empty($jenis) || empty($tanggal) || empty($perihal)) {
            return redirect()->back()->withInput()->with('error', 'Jenis Surat, Tanggal, dan Perihal wajib diisi.');
        }

        $nomor = $this->suratModel->generateNomor($jenis);
        $dibuatOleh = session('user_id') ?: (session('id') ?: (session('karyawan_id') ?: 1));

        $sigData = [
            'p1_title'   => $this->request->getPost('pihak_1_title'),
            'p1_nama'    => $this->request->getPost('pihak_1_nama'),
            'p1_jabatan' => $this->request->getPost('pihak_1_jabatan'),
            'p2_title'   => $this->request->getPost('pihak_2_title'),
            'p2_nama'    => $this->request->getPost('pihak_2_nama'),
            'p2_jabatan' => $this->request->getPost('pihak_2_jabatan'),
            'p3_title'   => $this->request->getPost('pihak_3_title'),
            'p3_nama'    => $this->request->getPost('pihak_3_nama'),
            'p3_jabatan' => $this->request->getPost('pihak_3_jabatan'),
        ];

        $id = $this->suratModel->insert([
            'nomor_surat'      => $nomor,
            'jenis_surat'      => $jenis,
            'karyawan_id'      => $karyawanId,
            'tanggal_surat'    => $tanggal,
            'perihal'          => $perihal,
            'isi_surat'        => $this->request->getPost('isi_surat'),
            'html_full'        => str_replace('[Auto-Generated]', $nomor, (string)$this->request->getPost('html_full')),
            'catatan'          => $this->request->getPost('catatan'),
            'dibuat_oleh'      => $dibuatOleh,
            'status'           => strtolower((string)$this->request->getPost('status')) ?: 'draft',
            'template_layout'  => $this->request->getPost('template_layout') ?: 'standard',
            'logo_position'    => $this->request->getPost('logo_position') ?: 'top_right',
            'address_position' => $this->request->getPost('address_position') ?: 'top_left',
            'accent_style'     => $this->request->getPost('accent_style') ?: 'line',
            'paper_size'       => $this->request->getPost('paper_size') ?: 'A4',
            'signature_layout' => $this->request->getPost('signature_layout') ?: '1_pihak',
            'signature_data'   => json_encode($sigData),
        ]);

        if (!$id) {
            $id = $this->suratModel->getInsertID(); // fallback
        }

        $isPrintNow = $this->request->getPost('print_now');
        $redirectUrl = base_url('direktur/karyawan/surat/detail/'.$id);
        if ($isPrintNow == '1') {
            $redirectUrl .= '?print_now=1';
        }
        return redirect()->to($redirectUrl)->with('success', 'Surat berhasil dibuat dengan nomor: '.$nomor);
    }

    public function detail($id)
    {
        $surat = $this->suratModel->getDetailWithKaryawan($id);
        if (!$surat) {
            return redirect()->to(base_url('direktur/karyawan/surat'))->with('error', 'Surat tidak ditemukan.');
        }
        $data = [
            'title'      => 'Detail Surat',
            'active'     => 'karyawan',
            'user'       => session()->get('user') ?? ['name' => session()->get('name') ?? 'Direktur', 'role' => 'direktur'],
            'surat'      => $surat,
            'paperSizes' => $this->suratModel->paperSizes,
            'logoBase64' => $this->suratModel->getCompanyLogoBase64(),
        ];
        return view('direktur/karyawan/surat/detail', $data);
    }

    public function pratinjau($id)
    {
        if (function_exists('service')) {
            try { service('toolbar')->stop(); } catch (\Throwable $e) {}
        }
        $surat = $this->suratModel->getDetailWithKaryawan($id);
        if (!$surat) {
            return redirect()->to(base_url('direktur/karyawan/surat'))->with('error', 'Surat tidak ditemukan.');
        }
        $data = [
            'title'      => 'Pratinjau Cetak Surat - ' . ($surat['nomor_surat'] ?? 'SPK'),
            'active'     => 'karyawan',
            'user'       => session()->get('user') ?? ['name' => session()->get('name') ?? 'Direktur', 'role' => 'direktur'],
            'surat'      => $surat,
            'paperSizes' => $this->suratModel->paperSizes,
            'logoBase64' => $this->suratModel->getCompanyLogoBase64(),
        ];
        return view('direktur/karyawan/surat/pratinjau', $data);
    }

    public function edit($id)
    {
        $surat = $this->suratModel->find($id);
        if (!$surat) {
            return redirect()->to(base_url('direktur/karyawan/surat'))->with('error', 'Surat tidak ditemukan.');
        }
        $data = [
            'title'        => 'Edit Surat Karyawan',
            'active'       => 'karyawan',
            'user'         => session()->get('user') ?? ['name' => session()->get('name') ?? 'Direktur', 'role' => 'direktur'],
            'surat'        => $surat,
            'karyawanList' => $this->karyawanModel->orderBy('nama_lengkap', 'ASC')->findAll(),
            'jenisList'    => $this->suratModel->jenisSurat,
            'paperSizes'   => $this->suratModel->paperSizes,
            'logoBase64'   => $this->suratModel->getCompanyLogoBase64(),
        ];
        return view('direktur/karyawan/surat/edit', $data);
    }

    public function update($id)
    {
        $surat = $this->suratModel->find($id);
        if (!$surat) {
            return redirect()->to(base_url('direktur/karyawan/surat'))->with('error', 'Surat tidak ditemukan.');
        }

        $jenis = $this->request->getPost('jenis_surat');
        if ($jenis === 'Lainnya' && $this->request->getPost('jenis_surat_custom')) {
            $jenis = trim($this->request->getPost('jenis_surat_custom'));
        }

        $karyawanIdRaw = $this->request->getPost('karyawan_id');
        $karyawanId    = (!empty($karyawanIdRaw) && is_numeric($karyawanIdRaw)) ? (int)$karyawanIdRaw : null;
        $tanggal    = $this->request->getPost('tanggal_surat');
        $perihal    = $this->request->getPost('perihal');

        if (empty($jenis) || empty($tanggal) || empty($perihal)) {
            return redirect()->back()->withInput()->with('error', 'Jenis Surat, Tanggal, dan Perihal wajib diisi.');
        }

        $sigData = [
            'p1_title'   => $this->request->getPost('pihak_1_title'),
            'p1_nama'    => $this->request->getPost('pihak_1_nama'),
            'p1_jabatan' => $this->request->getPost('pihak_1_jabatan'),
            'p2_title'   => $this->request->getPost('pihak_2_title'),
            'p2_nama'    => $this->request->getPost('pihak_2_nama'),
            'p2_jabatan' => $this->request->getPost('pihak_2_jabatan'),
            'p3_title'   => $this->request->getPost('pihak_3_title'),
            'p3_nama'    => $this->request->getPost('pihak_3_nama'),
            'p3_jabatan' => $this->request->getPost('pihak_3_jabatan'),
        ];

        $this->suratModel->update($id, [
            'jenis_surat'      => $jenis,
            'karyawan_id'      => $karyawanId,
            'tanggal_surat'    => $tanggal,
            'perihal'          => $perihal,
            'isi_surat'        => $this->request->getPost('isi_surat'),
            'html_full'        => (string)$this->request->getPost('html_full'),
            'catatan'          => $this->request->getPost('catatan'),
            'status'           => strtolower((string)$this->request->getPost('status')) ?: 'draft',
            'template_layout'  => $this->request->getPost('template_layout') ?: 'standard',
            'logo_position'    => $this->request->getPost('logo_position') ?: 'top_right',
            'address_position' => $this->request->getPost('address_position') ?: 'top_left',
            'accent_style'     => $this->request->getPost('accent_style') ?: 'line',
            'paper_size'       => $this->request->getPost('paper_size') ?: 'A4',
            'signature_layout' => $this->request->getPost('signature_layout') ?: '1_pihak',
            'signature_data'   => json_encode($sigData),
        ]);

        return redirect()->to(base_url('direktur/karyawan/surat/detail/'.$id))->with('success', 'Surat berhasil diperbarui.');
    }

    public function delete($id)
    {
        $surat = $this->suratModel->find($id);
        if ($surat) {
            $this->suratModel->delete($id);
            return redirect()->to(base_url('direktur/karyawan/surat'))->with('success', 'Surat berhasil dihapus.');
        }
        return redirect()->to(base_url('direktur/karyawan/surat'))->with('error', 'Surat tidak ditemukan.');
    }

    public function updateStatus($id)
    {
        $surat  = $this->suratModel->find($id);
        $status = strtolower((string)$this->request->getPost('status'));
        if ($surat && in_array($status, ['draft', 'diterbitkan', 'dibatalkan'])) {
            $this->suratModel->update($id, ['status' => $status]);
            return redirect()->back()->with('success', 'Status surat berhasil diubah menjadi '.ucfirst($status).'.');
        }
        return redirect()->back()->with('error', 'Gagal memperbarui status surat.');
    }
}
