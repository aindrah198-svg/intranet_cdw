<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SuratKaryawanModel;
use App\Models\KaryawanModel;

class Surat extends BaseController
{
    protected $suratModel;
    protected $karyawanModel;
    protected $db;

    public function __construct()
    {
        $this->suratModel    = new SuratKaryawanModel();
        $this->karyawanModel = new KaryawanModel();
        $this->db            = \Config\Database::connect();
    }

    private function checkAccess()
    {
        if (!session()->get('isLoggedIn')) return redirect()->to(base_url('login'));
        if (strtolower(session()->get('role') ?? '') !== 'admin') return redirect()->to(base_url('login'))->with('error', 'Akses ditolak!');
        return null;
    }

    public function index()
    {
        if ($r = $this->checkAccess()) return $r;

        $filter = $this->request->getGet('jenis');
        $search = $this->request->getGet('q');

        $query = $this->suratModel->select('surat_karyawan.*, karyawan.nama_lengkap, karyawan.nik, karyawan.divisi')
                                   ->join('karyawan', 'karyawan.id = surat_karyawan.karyawan_id', 'left')
                                   ->orderBy('surat_karyawan.tanggal_surat', 'DESC');

        if ($filter) {
            $query->where('surat_karyawan.jenis_surat', $filter);
        }
        if ($search) {
            $query->groupStart()
                  ->like('surat_karyawan.nomor_surat', $search)
                  ->orLike('surat_karyawan.perihal', $search)
                  ->orLike('karyawan.nama_lengkap', $search)
                  ->groupEnd();
        }

        $allSurat = $query->findAll();

        $data = [
            'title'            => 'Surat Menyurat',
            'subtitle'         => 'Kelola dokumen resmi perusahaan, kontrak kerja, SP, BAST, dan surat dinas',
            'active'           => 'surat',
            'user'             => ['name' => session()->get('name') ?? 'Admin', 'role' => session()->get('role')],
            'suratList'        => $allSurat,
            'jenisList'        => $this->suratModel->jenisSurat,
            'paperSizes'       => $this->suratModel->paperSizes,
            'filterAktif'      => $filter,
            'searchQuery'      => $search,
            'countTotal'       => count($allSurat),
            'countDiterbitkan' => count(array_filter($allSurat, fn($s) => ($s['status'] ?? '') === 'diterbitkan')),
            'countDraft'       => count(array_filter($allSurat, fn($s) => ($s['status'] ?? '') === 'draft')),
            'countDibatalkan'  => count(array_filter($allSurat, fn($s) => ($s['status'] ?? '') === 'dibatalkan')),
            'logoBase64'       => $this->suratModel->getCompanyLogoBase64(),
        ];

        return view('admin/surat/index', $data);
    }

    public function tambah()
    {
        if ($r = $this->checkAccess()) return $r;

        $data = [
            'title'        => 'Buat Surat Baru',
            'subtitle'     => 'Form Generator & Pembuat Surat Resmi CDW Engineering',
            'active'       => 'surat',
            'user'         => ['name' => session()->get('name') ?? 'Admin', 'role' => session()->get('role')],
            'karyawanList' => $this->karyawanModel->orderBy('nama_lengkap', 'ASC')->findAll(),
            'jenisList'    => $this->suratModel->jenisSurat,
            'paperSizes'   => $this->suratModel->paperSizes,
            'logoBase64'   => $this->suratModel->getCompanyLogoBase64(),
        ];

        return view('admin/surat/tambah', $data);
    }

    public function simpan()
    {
        if ($r = $this->checkAccess()) return $r;

        $jenis = $this->request->getPost('jenis_surat');
        if ($jenis === 'Lainnya' && $this->request->getPost('jenis_surat_custom')) {
            $jenis = trim($this->request->getPost('jenis_surat_custom'));
        }

        $karyawanIdRaw = $this->request->getPost('karyawan_id');
        $karyawanId    = (!empty($karyawanIdRaw) && is_numeric($karyawanIdRaw)) ? (int)$karyawanIdRaw : null;
        $tanggal     = $this->request->getPost('tanggal_surat') ?: date('Y-m-d');
        $perihal     = $this->request->getPost('perihal');

        if (empty($jenis) || empty($perihal)) {
            return redirect()->back()->withInput()->with('error', 'Jenis Surat dan Perihal wajib diisi.');
        }

        $nomor = $this->suratModel->generateNomor($jenis);

        $sigData = [
            'p1_title'   => $this->request->getPost('pihak_1_title'),
            'p1_nama'    => $this->request->getPost('pihak_1_nama'),
            'p1_jabatan' => $this->request->getPost('pihak_1_jabatan'),
            'p1_img'     => $this->request->getPost('pihak_1_img'),
            'p2_title'   => $this->request->getPost('pihak_2_title'),
            'p2_nama'    => $this->request->getPost('pihak_2_nama'),
            'p2_jabatan' => $this->request->getPost('pihak_2_jabatan'),
            'p2_img'     => $this->request->getPost('pihak_2_img'),
            'p3_title'   => $this->request->getPost('pihak_3_title'),
            'p3_nama'    => $this->request->getPost('pihak_3_nama'),
            'p3_jabatan' => $this->request->getPost('pihak_3_jabatan'),
            'p3_img'     => $this->request->getPost('pihak_3_img'),
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
            'dibuat_oleh'      => session('id') ?: 1,
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
            $id = $this->suratModel->getInsertID();
        }

        // If print_now flag, redirect to detail with print_now param
        $isPrintNow = $this->request->getPost('print_now');
        if ($isPrintNow == '1') {
            return redirect()->to(base_url('admin/surat/detail/' . $id . '?print_now=1'))->with('success', 'Surat berhasil disimpan & siap dicetak.');
        }

        return redirect()->to(base_url('admin/surat'))->with('success', 'Surat "' . $jenis . '" nomor ' . $nomor . ' berhasil disimpan!');
    }

    public function detail($id)
    {
        if ($r = $this->checkAccess()) return $r;

        $surat = $this->suratModel->select('surat_karyawan.*, karyawan.nama_lengkap, karyawan.nik, karyawan.divisi, karyawan.jabatan, karyawan.email')
                                  ->join('karyawan', 'karyawan.id = surat_karyawan.karyawan_id', 'left')
                                  ->find($id);

        if (!$surat) {
            return redirect()->to(base_url('admin/surat'))->with('error', 'Surat tidak ditemukan.');
        }

        // Load karyawan separately for the sidebar card
        $karyawan = null;
        if (!empty($surat['karyawan_id'])) {
            $karyawan = $this->karyawanModel->find($surat['karyawan_id']);
        }

        $data = [
            'title'      => 'Detail Surat - ' . $surat['nomor_surat'],
            'subtitle'   => 'Preview & Cetak Berkas Surat Resmi',
            'active'     => 'surat',
            'user'       => ['name' => session()->get('name') ?? 'Admin', 'role' => session()->get('role')],
            'surat'      => $surat,
            'karyawan'   => $karyawan,
            'paperSizes' => $this->suratModel->paperSizes,
            'logoBase64' => $this->suratModel->getCompanyLogoBase64(),
        ];

        return view('admin/surat/detail', $data);
    }

    public function pratinjau($id)
    {
        if (function_exists('service')) {
            try { service('toolbar')->stop(); } catch (\Throwable $e) {}
        }
        if ($r = $this->checkAccess()) return $r;

        $surat = $this->suratModel->select('surat_karyawan.*, karyawan.nama_lengkap, karyawan.nik, karyawan.divisi, karyawan.jabatan, karyawan.email')
                                  ->join('karyawan', 'karyawan.id = surat_karyawan.karyawan_id', 'left')
                                  ->find($id);

        if (!$surat) {
            return redirect()->to(base_url('admin/surat'))->with('error', 'Surat tidak ditemukan.');
        }

        $karyawan = null;
        if (!empty($surat['karyawan_id'])) {
            $karyawan = $this->karyawanModel->find($surat['karyawan_id']);
        }

        $data = [
            'title'      => 'Pratinjau Cetak Surat - ' . $surat['nomor_surat'],
            'subtitle'   => 'Halaman Khusus Cetak Dokumen Surat',
            'active'     => 'surat',
            'user'       => ['name' => session()->get('name') ?? 'Admin', 'role' => session()->get('role')],
            'surat'      => $surat,
            'karyawan'   => $karyawan,
            'paperSizes' => $this->suratModel->paperSizes,
            'logoBase64' => $this->suratModel->getCompanyLogoBase64(),
        ];

        return view('admin/surat/pratinjau', $data);
    }

    public function edit($id)
    {
        if ($r = $this->checkAccess()) return $r;

        $surat = $this->suratModel->find($id);
        if (!$surat) {
            return redirect()->to(base_url('admin/surat'))->with('error', 'Surat tidak ditemukan.');
        }

        $data = [
            'title'        => 'Edit Surat - ' . $surat['nomor_surat'],
            'subtitle'     => 'Perbarui Data & Template Surat',
            'active'       => 'surat',
            'user'         => ['name' => session()->get('name') ?? 'Admin', 'role' => session()->get('role')],
            'surat'        => $surat,
            'karyawanList' => $this->karyawanModel->orderBy('nama_lengkap', 'ASC')->findAll(),
            'jenisList'    => $this->suratModel->jenisSurat,
            'paperSizes'   => $this->suratModel->paperSizes,
            'logoBase64'   => $this->suratModel->getCompanyLogoBase64(),
        ];

        return view('admin/surat/edit', $data);
    }

    public function update($id = null)
    {
        if ($r = $this->checkAccess()) return $r;

        if (!$id) {
            $id = $this->request->getPost('id');
        }
        $surat = $this->suratModel->find($id);
        if (!$surat) {
            return redirect()->to(base_url('admin/surat'))->with('error', 'Surat tidak ditemukan.');
        }

        $jenis = $this->request->getPost('jenis_surat');
        if ($jenis === 'Lainnya' && $this->request->getPost('jenis_surat_custom')) {
            $jenis = trim($this->request->getPost('jenis_surat_custom'));
        }

        $htmlFull = $this->request->getPost('html_full');
        if (empty($htmlFull)) {
            $htmlFull = $surat['html_full']; // preserve existing if not regenerated
        }

        $sigData = [
            'p1_title'   => $this->request->getPost('pihak_1_title'),
            'p1_nama'    => $this->request->getPost('pihak_1_nama'),
            'p1_jabatan' => $this->request->getPost('pihak_1_jabatan'),
            'p1_img'     => $this->request->getPost('pihak_1_img'),
            'p2_title'   => $this->request->getPost('pihak_2_title'),
            'p2_nama'    => $this->request->getPost('pihak_2_nama'),
            'p2_jabatan' => $this->request->getPost('pihak_2_jabatan'),
            'p2_img'     => $this->request->getPost('pihak_2_img'),
            'p3_title'   => $this->request->getPost('pihak_3_title'),
            'p3_nama'    => $this->request->getPost('pihak_3_nama'),
            'p3_jabatan' => $this->request->getPost('pihak_3_jabatan'),
            'p3_img'     => $this->request->getPost('pihak_3_img'),
        ];

        $this->suratModel->update($id, [
            'jenis_surat'      => $jenis,
            'karyawan_id'      => $this->request->getPost('karyawan_id') ?: null,
            'tanggal_surat'    => $this->request->getPost('tanggal_surat'),
            'perihal'          => $this->request->getPost('perihal'),
            'isi_surat'        => $this->request->getPost('isi_surat'),
            'html_full'        => $htmlFull,
            'catatan'          => $this->request->getPost('catatan'),
            'status'           => strtolower((string)$this->request->getPost('status')) ?: 'draft',
            'template_layout'  => $this->request->getPost('template_layout') ?: ($surat['template_layout'] ?? 'standard'),
            'logo_position'    => $this->request->getPost('logo_position') ?: ($surat['logo_position'] ?? 'top_right'),
            'address_position' => $this->request->getPost('address_position') ?: ($surat['address_position'] ?? 'top_left'),
            'accent_style'     => $this->request->getPost('accent_style') ?: ($surat['accent_style'] ?? 'line'),
            'paper_size'       => $this->request->getPost('paper_size') ?: ($surat['paper_size'] ?? 'A4'),
            'signature_layout' => $this->request->getPost('signature_layout') ?: ($surat['signature_layout'] ?? '1_pihak'),
            'signature_data'   => json_encode($sigData),
        ]);

        return redirect()->to(base_url('admin/surat/detail/' . $id))->with('success', 'Surat berhasil diperbarui.');
    }

    public function updateStatus($id)
    {
        if ($r = $this->checkAccess()) return $r;

        $surat = $this->suratModel->find($id);
        if (!$surat) {
            return redirect()->to(base_url('admin/surat'))->with('error', 'Surat tidak ditemukan.');
        }

        $newStatus = strtolower($this->request->getPost('status') ?? 'draft');
        $allowed   = ['draft', 'diterbitkan', 'dibatalkan'];
        if (!in_array($newStatus, $allowed)) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        $this->suratModel->update($id, ['status' => $newStatus]);

        return redirect()->to(base_url('admin/surat/detail/' . $id))->with('success', 'Status surat berhasil diubah ke: ' . ucfirst($newStatus));
    }

    public function hapus($id)
    {
        if ($r = $this->checkAccess()) return $r;

        $surat = $this->suratModel->find($id);
        if ($surat) {
            $this->suratModel->delete($id);
            session()->setFlashdata('success', 'Surat berhasil dihapus.');
            return $this->response->setJSON(['status' => 'ok', 'message' => 'Surat berhasil dihapus.']);
        }
        return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Surat tidak ditemukan.']);
    }

    // One-time cleanup: clear stale html_full from all records (old buggy multi-page data)
    public function clearHtmlFull()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->db->query("UPDATE surat_karyawan SET html_full = NULL WHERE html_full IS NOT NULL AND html_full != ''");
        $affected = $this->db->affectedRows();
        return $this->response->setJSON(['status' => 'ok', 'message' => "html_full cleared for {$affected} records."]);
    }
}
