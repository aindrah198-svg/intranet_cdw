<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KaryawanModel;
use App\Models\ProjectModel;

class DokumenController extends BaseController
{
    protected $db;
    protected $karyawanModel;
    protected $projectModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->karyawanModel = new KaryawanModel();
        $this->projectModel = new ProjectModel();
    }

    private function checkAccess()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        $role = strtolower(session()->get('role') ?? '');
        if ($role !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Akses ditolak!');
        }
        return null;
    }

    // Ensure database tables exist
    private function ensureTablesExist()
    {
        $forge = \Config\Database::forge();

        // 1. dokumen_penting
        if (!$this->db->tableExists('dokumen_penting')) {
            $forge->addField([
                'id'                 => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'judul_dokumen'      => ['type' => 'VARCHAR', 'constraint' => 255],
                'nomor_dokumen'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'kategori'           => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'Legalitas'],
                'tanggal_terbit'     => ['type' => 'DATE', 'null' => true],
                'tanggal_kadaluarsa' => ['type' => 'DATE', 'null' => true],
                'file_path'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'keterangan'         => ['type' => 'TEXT', 'null' => true],
                'created_at'         => ['type' => 'DATETIME', 'null' => true]
            ]);
            $forge->addKey('id', true);
            $forge->createTable('dokumen_penting', true);

            $this->db->table('dokumen_penting')->insertBatch([
                [
                    'judul_dokumen'      => 'Akta Pendirian Perusahaan PT CDW',
                    'nomor_dokumen'      => 'AHU-0012345.AH.01.01.2020',
                    'kategori'           => 'Legalitas',
                    'tanggal_terbit'     => '2020-01-15',
                    'tanggal_kadaluarsa' => null,
                    'keterangan'         => 'Dokumen resmi akta pendirian perseroan terbatas dari notaris.',
                    'created_at'         => date('Y-m-d H:i:s')
                ],
                [
                    'judul_dokumen'      => 'Nomor Induk Berusaha (NIB) & SIUP',
                    'nomor_dokumen'      => '9120001234567',
                    'kategori'           => 'Legalitas',
                    'tanggal_terbit'     => '2020-02-01',
                    'tanggal_kadaluarsa' => null,
                    'keterangan'         => 'NIB terintegrasi OSS Republik Indonesia.',
                    'created_at'         => date('Y-m-d H:i:s')
                ],
                [
                    'judul_dokumen'      => 'SKT Perpajakan (NPWP Perusahaan)',
                    'nomor_dokumen'      => '01.234.567.8-012.000',
                    'kategori'           => 'Perpajakan',
                    'tanggal_terbit'     => '2020-01-20',
                    'tanggal_kadaluarsa' => null,
                    'keterangan'         => 'Surat Keterangan Terdaftar Wajib Pajak Badan.',
                    'created_at'         => date('Y-m-d H:i:s')
                ]
            ]);
        }

        // 2. dokumen_sertifikat
        if (!$this->db->tableExists('dokumen_sertifikat')) {
            $forge->addField([
                'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'nama_sertifikat'   => ['type' => 'VARCHAR', 'constraint' => 255],
                'penerbit'          => ['type' => 'VARCHAR', 'constraint' => 255],
                'nomor_sertifikat'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'karyawan_id'       => ['type' => 'INT', 'constraint' => 11, 'null' => true],
                'tanggal_perolehan' => ['type' => 'DATE', 'null' => true],
                'masa_berlaku'      => ['type' => 'DATE', 'null' => true],
                'file_path'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'status'            => ['type' => 'ENUM', 'constraint' => ['aktif', 'kadaluarsa', 'proses_perpanjangan'], 'default' => 'aktif'],
                'created_at'        => ['type' => 'DATETIME', 'null' => true]
            ]);
            $forge->addKey('id', true);
            $forge->createTable('dokumen_sertifikat', true);

            $this->db->table('dokumen_sertifikat')->insertBatch([
                [
                    'nama_sertifikat'   => 'Sertifikat ISO 9001:2015 Manajemen Mutu',
                    'penerbit'          => 'SUCOFINDO International',
                    'nomor_sertifikat'  => 'ISO-9001-CDW-2024',
                    'karyawan_id'       => null,
                    'tanggal_perolehan' => '2024-01-10',
                    'masa_berlaku'      => '2027-01-10',
                    'status'            => 'aktif',
                    'created_at'        => date('Y-m-d H:i:s')
                ],
                [
                    'nama_sertifikat'   => 'Sertifikasi Ahli K3 Umum Kemnaker',
                    'penerbit'          => 'Kementerian Ketenagakerjaan RI',
                    'nomor_sertifikat'  => 'K3-KMK-2023-887',
                    'karyawan_id'       => 1,
                    'tanggal_perolehan' => '2023-06-15',
                    'masa_berlaku'      => '2026-06-15',
                    'status'            => 'aktif',
                    'created_at'        => date('Y-m-d H:i:s')
                ]
            ]);
        }

        // 3. kontak_project
        if (!$this->db->tableExists('kontak_project')) {
            $forge->addField([
                'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'project_id'       => ['type' => 'INT', 'constraint' => 11, 'null' => true],
                'nama_kontak'      => ['type' => 'VARCHAR', 'constraint' => 255],
                'perusahaan_klien' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'jabatan'          => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'telepon'          => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'email'            => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'catatan'          => ['type' => 'TEXT', 'null' => true],
                'created_at'       => ['type' => 'DATETIME', 'null' => true]
            ]);
            $forge->addKey('id', true);
            $forge->createTable('kontak_project', true);

            $this->db->table('kontak_project')->insertBatch([
                [
                    'project_id'       => null,
                    'nama_kontak'      => 'Bpk. Hendra Gunawan',
                    'perusahaan_klien' => 'PT Pertamina Trans Kontinental',
                    'jabatan'          => 'Senior Project Manager',
                    'telepon'          => '081298765432',
                    'email'            => 'hendra.gunawan@pertamina.com',
                    'catatan'          => 'PIC Utama Penandatanganan BAST & Invoice.',
                    'created_at'       => date('Y-m-d H:i:s')
                ],
                [
                    'project_id'       => null,
                    'nama_kontak'      => 'Ibu Siska Rahmawati',
                    'perusahaan_klien' => 'PT PLN Nusantara Power',
                    'jabatan'          => 'Head of Procurement',
                    'telepon'          => '081311223344',
                    'email'            => 'siska.rahma@pln.co.id',
                    'catatan'          => 'Pengurusan verifikasi kualifikasi vendor & tender.',
                    'created_at'       => date('Y-m-d H:i:s')
                ]
            ]);
        }
    }

    // ==========================================
    // 1. Dokumen Penting
    // ==========================================
    public function penting()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();
        $dokumen = $this->db->table('dokumen_penting')->orderBy('id', 'DESC')->get()->getResultArray();

        $data = [
            'title'   => 'Dokumen Penting Perusahaan',
            'dokumen' => $dokumen,
            'active'  => 'dokumen'
        ];

        return view('admin/dokumen/penting', $data);
    }

    public function tambah_penting()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();
        $data = [
            'title'  => 'Upload Dokumen Penting Baru',
            'active' => 'dokumen'
        ];
        return view('admin/dokumen/penting_tambah', $data);
    }

    public function simpan_penting()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();
        $file = $this->request->getFile('file_dokumen');
        $fileName = null;

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileName = $file->getRandomName();
            $targetDir = ROOTPATH . 'public/uploads/dokumen';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $file->move($targetDir, $fileName);
        }

        $this->db->table('dokumen_penting')->insert([
            'judul_dokumen'      => $this->request->getPost('judul_dokumen'),
            'nomor_dokumen'      => $this->request->getPost('nomor_dokumen'),
            'kategori'           => $this->request->getPost('kategori') ?: 'Legalitas',
            'tanggal_terbit'     => $this->request->getPost('tanggal_terbit') ?: null,
            'tanggal_kadaluarsa' => $this->request->getPost('tanggal_kadaluarsa') ?: null,
            'file_path'          => $fileName,
            'keterangan'         => $this->request->getPost('keterangan'),
            'created_at'         => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('admin/dokumen/penting'))->with('success', 'Dokumen penting berhasil diunggah.');
    }

    public function edit_penting($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();
        $d = $this->db->table('dokumen_penting')->where('id', $id)->get()->getRowArray();
        if (!$d) {
            return redirect()->to(base_url('admin/dokumen/penting'))->with('error', 'Dokumen tidak ditemukan.');
        }

        $data = [
            'title'  => 'Edit Dokumen Penting',
            'd'      => $d,
            'active' => 'dokumen'
        ];
        return view('admin/dokumen/penting_edit', $data);
    }

    public function detail_penting($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();
        $d = $this->db->table('dokumen_penting')->where('id', $id)->get()->getRowArray();
        if (!$d) {
            return redirect()->to(base_url('admin/dokumen/penting'))->with('error', 'Dokumen tidak ditemukan.');
        }

        $data = [
            'title'  => 'Detail Dokumen Penting',
            'd'      => $d,
            'active' => 'dokumen'
        ];
        return view('admin/dokumen/penting_detail', $data);
    }

    public function update_penting()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();
        $id = $this->request->getPost('id');
        $oldData = $this->db->table('dokumen_penting')->where('id', $id)->get()->getRowArray();

        $fileName = $oldData['file_path'] ?? null;
        $file = $this->request->getFile('file_dokumen');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newFileName = $file->getRandomName();
            $targetDir = ROOTPATH . 'public/uploads/dokumen';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $file->move($targetDir, $newFileName);

            if (!empty($fileName) && file_exists($targetDir . '/' . $fileName)) {
                @unlink($targetDir . '/' . $fileName);
            }
            $fileName = $newFileName;
        }

        $this->db->table('dokumen_penting')->where('id', $id)->update([
            'judul_dokumen'      => $this->request->getPost('judul_dokumen'),
            'nomor_dokumen'      => $this->request->getPost('nomor_dokumen'),
            'kategori'           => $this->request->getPost('kategori'),
            'tanggal_terbit'     => $this->request->getPost('tanggal_terbit') ?: null,
            'tanggal_kadaluarsa' => $this->request->getPost('tanggal_kadaluarsa') ?: null,
            'file_path'          => $fileName,
            'keterangan'         => $this->request->getPost('keterangan'),
            'updated_at'         => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('admin/dokumen/penting'))->with('success', 'Dokumen penting berhasil diperbarui.');
    }

    public function delete_penting($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();
        $oldData = $this->db->table('dokumen_penting')->where('id', $id)->get()->getRowArray();
        if ($oldData && !empty($oldData['file_path'])) {
            $file = ROOTPATH . 'public/uploads/dokumen/' . $oldData['file_path'];
            if (file_exists($file)) {
                @unlink($file);
            }
        }

        $this->db->table('dokumen_penting')->where('id', $id)->delete();
        return redirect()->to(base_url('admin/dokumen/penting'))->with('success', 'Dokumen penting berhasil dihapus.');
    }

    // ==========================================
    // 2. Dokumen Sertifikat
    // ==========================================
    public function sertifikat()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();
        $builder = $this->db->table('dokumen_sertifikat s')
            ->select('s.*, k.nama_lengkap as karyawan')
            ->join('karyawan k', 'k.id = s.karyawan_id', 'left')
            ->orderBy('s.id', 'DESC');

        $data = [
            'title'      => 'Dokumen Sertifikat & Kelayakan',
            'sertifikat' => $builder->get()->getResultArray(),
            'karyawan'   => $this->karyawanModel->findAll(),
            'active'     => 'dokumen'
        ];

        return view('admin/dokumen/sertifikat', $data);
    }

    public function tambah_sertifikat()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();
        $data = [
            'title'    => 'Tambah Sertifikat Baru',
            'karyawan' => $this->karyawanModel->findAll(),
            'active'   => 'dokumen'
        ];
        return view('admin/dokumen/sertifikat_tambah', $data);
    }

    public function simpan_sertifikat()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();
        $file = $this->request->getFile('file_sertifikat');
        $fileName = null;

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileName = $file->getRandomName();
            $targetDir = ROOTPATH . 'public/uploads/sertifikat';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $file->move($targetDir, $fileName);
        }

        $this->db->table('dokumen_sertifikat')->insert([
            'nama_sertifikat'   => $this->request->getPost('nama_sertifikat'),
            'penerbit'          => $this->request->getPost('penerbit'),
            'nomor_sertifikat'  => $this->request->getPost('nomor_sertifikat'),
            'karyawan_id'       => $this->request->getPost('karyawan_id') ?: null,
            'tanggal_perolehan' => $this->request->getPost('tanggal_perolehan') ?: null,
            'masa_berlaku'      => $this->request->getPost('masa_berlaku') ?: null,
            'file_path'         => $fileName,
            'status'            => 'aktif',
            'created_at'        => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('admin/dokumen/sertifikat'))->with('success', 'Sertifikat berhasil ditambahkan.');
    }

    public function edit_sertifikat($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();
        $s = $this->db->table('dokumen_sertifikat')->where('id', $id)->get()->getRowArray();
        if (!$s) {
            return redirect()->to(base_url('admin/dokumen/sertifikat'))->with('error', 'Sertifikat tidak ditemukan.');
        }

        $data = [
            'title'    => 'Edit Dokumen Sertifikat',
            's'        => $s,
            'karyawan' => $this->karyawanModel->findAll(),
            'active'   => 'dokumen'
        ];
        return view('admin/dokumen/sertifikat_edit', $data);
    }

    public function detail_sertifikat($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();
        $builder = $this->db->table('dokumen_sertifikat s')
            ->select('s.*, k.nama_lengkap as karyawan, k.jabatan as karyawan_jabatan')
            ->join('karyawan k', 'k.id = s.karyawan_id', 'left')
            ->where('s.id', $id);

        $s = $builder->get()->getRowArray();
        if (!$s) {
            return redirect()->to(base_url('admin/dokumen/sertifikat'))->with('error', 'Sertifikat tidak ditemukan.');
        }

        $data = [
            'title'  => 'Detail Dokumen Sertifikat',
            's'      => $s,
            'active' => 'dokumen'
        ];
        return view('admin/dokumen/sertifikat_detail', $data);
    }

    public function update_sertifikat()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();
        $id = $this->request->getPost('id');
        $oldData = $this->db->table('dokumen_sertifikat')->where('id', $id)->get()->getRowArray();

        $fileName = $oldData['file_path'] ?? null;
        $file = $this->request->getFile('file_sertifikat');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newFileName = $file->getRandomName();
            $targetDir = ROOTPATH . 'public/uploads/sertifikat';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $file->move($targetDir, $newFileName);

            if (!empty($fileName) && file_exists($targetDir . '/' . $fileName)) {
                @unlink($targetDir . '/' . $fileName);
            }
            $fileName = $newFileName;
        }

        $this->db->table('dokumen_sertifikat')->where('id', $id)->update([
            'nama_sertifikat'   => $this->request->getPost('nama_sertifikat'),
            'penerbit'          => $this->request->getPost('penerbit'),
            'nomor_sertifikat'  => $this->request->getPost('nomor_sertifikat'),
            'karyawan_id'       => $this->request->getPost('karyawan_id') ?: null,
            'tanggal_perolehan' => $this->request->getPost('tanggal_perolehan') ?: null,
            'masa_berlaku'      => $this->request->getPost('masa_berlaku') ?: null,
            'file_path'         => $fileName,
            'status'            => $this->request->getPost('status') ?: 'aktif',
            'updated_at'        => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('admin/dokumen/sertifikat'))->with('success', 'Sertifikat berhasil diperbarui.');
    }

    public function delete_sertifikat($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();
        $oldData = $this->db->table('dokumen_sertifikat')->where('id', $id)->get()->getRowArray();
        if ($oldData && !empty($oldData['file_path'])) {
            $file = ROOTPATH . 'public/uploads/sertifikat/' . $oldData['file_path'];
            if (file_exists($file)) {
                @unlink($file);
            }
        }

        $this->db->table('dokumen_sertifikat')->where('id', $id)->delete();
        return redirect()->to(base_url('admin/dokumen/sertifikat'))->with('success', 'Sertifikat berhasil dihapus.');
    }

    // ==========================================
    // 3. Kontak Project
    // ==========================================
    public function kontak()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();
        $builder = $this->db->table('kontak_project k')
            ->select('k.*, p.nama_project, p.kode_project')
            ->join('project p', 'p.id = k.project_id', 'left')
            ->orderBy('k.id', 'DESC');

        $data = [
            'title'    => 'Kontak Project & Stakeholder',
            'kontak'   => $builder->get()->getResultArray(),
            'projects' => $this->projectModel->findAll(),
            'active'   => 'dokumen'
        ];

        return view('admin/dokumen/kontak', $data);
    }

    public function tambah_kontak()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();
        $data = [
            'title'    => 'Tambah Kontak PIC Project',
            'projects' => $this->projectModel->findAll(),
            'active'   => 'dokumen'
        ];
        return view('admin/dokumen/kontak_tambah', $data);
    }

    public function simpan_kontak()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();
        $this->db->table('kontak_project')->insert([
            'project_id'       => $this->request->getPost('project_id') ?: null,
            'nama_kontak'      => $this->request->getPost('nama_kontak'),
            'perusahaan_klien' => $this->request->getPost('perusahaan_klien'),
            'jabatan'          => $this->request->getPost('jabatan'),
            'telepon'          => $this->request->getPost('telepon'),
            'email'            => $this->request->getPost('email'),
            'catatan'          => $this->request->getPost('catatan'),
            'created_at'       => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('admin/dokumen/kontak'))->with('success', 'Kontak project berhasil disimpan.');
    }

    public function edit_kontak($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();
        $k = $this->db->table('kontak_project')->where('id', $id)->get()->getRowArray();
        if (!$k) {
            return redirect()->to(base_url('admin/dokumen/kontak'))->with('error', 'Kontak tidak ditemukan.');
        }

        $data = [
            'title'    => 'Edit Kontak Project',
            'k'        => $k,
            'projects' => $this->projectModel->findAll(),
            'active'   => 'dokumen'
        ];
        return view('admin/dokumen/kontak_edit', $data);
    }

    public function detail_kontak($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();
        $builder = $this->db->table('kontak_project k')
            ->select('k.*, p.nama_project, p.kode_project')
            ->join('project p', 'p.id = k.project_id', 'left')
            ->where('k.id', $id);

        $k = $builder->get()->getRowArray();
        if (!$k) {
            return redirect()->to(base_url('admin/dokumen/kontak'))->with('error', 'Kontak tidak ditemukan.');
        }

        $data = [
            'title'  => 'Detail Kontak Project',
            'k'      => $k,
            'active' => 'dokumen'
        ];
        return view('admin/dokumen/kontak_detail', $data);
    }

    public function update_kontak()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();
        $id = $this->request->getPost('id');

        $this->db->table('kontak_project')->where('id', $id)->update([
            'project_id'       => $this->request->getPost('project_id') ?: null,
            'nama_kontak'      => $this->request->getPost('nama_kontak'),
            'perusahaan_klien' => $this->request->getPost('perusahaan_klien'),
            'jabatan'          => $this->request->getPost('jabatan'),
            'telepon'          => $this->request->getPost('telepon'),
            'email'            => $this->request->getPost('email'),
            'catatan'          => $this->request->getPost('catatan'),
            'updated_at'       => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('admin/dokumen/kontak'))->with('success', 'Kontak project berhasil diperbarui.');
    }

    public function delete_kontak($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();
        $this->db->table('kontak_project')->where('id', $id)->delete();
        return redirect()->to(base_url('admin/dokumen/kontak'))->with('success', 'Kontak project berhasil dihapus.');
    }
}
