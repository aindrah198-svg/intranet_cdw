<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Pengajuan extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
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

    private function ensureTablesExist()
    {
        $forge = \Config\Database::forge();

        // 1. Tabel Cuti
        if (!$this->db->tableExists('cuti')) {
            $forge->addField([
                'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'karyawan_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'nomor_cuti'        => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'jenis_cuti'        => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'Tahunan'],
                'alasan'            => ['type' => 'TEXT', 'null' => true],
                'tanggal_mulai'     => ['type' => 'DATE', 'null' => true],
                'tanggal_selesai'   => ['type' => 'DATE', 'null' => true],
                'lama_hari'         => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
                'sisa_cuti_tahunan' => ['type' => 'INT', 'constraint' => 11, 'default' => 12],
                'status'            => ['type' => 'ENUM', 'constraint' => ['Draft', 'Menunggu', 'Disetujui', 'Ditolak', 'Dibatalkan'], 'default' => 'Menunggu'],
                'disetujui_oleh'    => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'disetujui_at'      => ['type' => 'DATETIME', 'null' => true],
                'alasan_penolakan'  => ['type' => 'TEXT', 'null' => true],
                'tanggal_pengajuan' => ['type' => 'DATE', 'null' => true],
                'created_at'        => ['type' => 'DATETIME', 'null' => true],
                'updated_at'        => ['type' => 'DATETIME', 'null' => true],
                'deleted_at'        => ['type' => 'DATETIME', 'null' => true]
            ]);
            $forge->addKey('id', true);
            $forge->createTable('cuti', true);

            $this->db->table('cuti')->insertBatch([
                [
                    'karyawan_id'       => 1,
                    'nomor_cuti'        => 'CTI-' . date('Ymd') . '-001',
                    'jenis_cuti'        => 'Tahunan',
                    'alasan'            => 'Acara Keluarga di luar kota & keperluan mendesak',
                    'tanggal_mulai'     => date('Y-m-d', strtotime('+2 days')),
                    'tanggal_selesai'   => date('Y-m-d', strtotime('+4 days')),
                    'lama_hari'         => 3,
                    'sisa_cuti_tahunan' => 12,
                    'status'            => 'Menunggu',
                    'tanggal_pengajuan' => date('Y-m-d'),
                    'created_at'        => date('Y-m-d H:i:s')
                ],
                [
                    'karyawan_id'       => 2,
                    'nomor_cuti'        => 'CTI-' . date('Ymd') . '-002',
                    'jenis_cuti'        => 'Sakit',
                    'alasan'            => 'Istirahat Sakit Demam tinggi disertai surat keterangan dokter',
                    'tanggal_mulai'     => date('Y-m-d', strtotime('-5 days')),
                    'tanggal_selesai'   => date('Y-m-d', strtotime('-4 days')),
                    'lama_hari'         => 2,
                    'sisa_cuti_tahunan' => 10,
                    'status'            => 'Disetujui',
                    'disetujui_oleh'    => 'Direktur Utama',
                    'disetujui_at'      => date('Y-m-d H:i:s', strtotime('-4 days')),
                    'tanggal_pengajuan' => date('Y-m-d', strtotime('-5 days')),
                    'created_at'        => date('Y-m-d H:i:s', strtotime('-5 days'))
                ]
            ]);
        } else {
            if (!$this->db->fieldExists('status', 'cuti')) {
                $forge->addColumn('cuti', ['status' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Menunggu']]);
            }
            if (!$this->db->fieldExists('disetujui_oleh', 'cuti')) {
                $forge->addColumn('cuti', ['disetujui_oleh' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true]]);
            }
            if (!$this->db->fieldExists('disetujui_at', 'cuti')) {
                $forge->addColumn('cuti', ['disetujui_at' => ['type' => 'DATETIME', 'null' => true]]);
            }
            if (!$this->db->fieldExists('alasan_penolakan', 'cuti')) {
                $forge->addColumn('cuti', ['alasan_penolakan' => ['type' => 'TEXT', 'null' => true]]);
            }
        }

        // 2. Tabel Kuota Cuti
        if (!$this->db->tableExists('kuota_cuti')) {
            $forge->addField([
                'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'karyawan_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'tahun'         => ['type' => 'INT', 'constraint' => 4, 'default' => date('Y')],
                'kuota_tahunan' => ['type' => 'INT', 'constraint' => 11, 'default' => 12],
                'terpakai'      => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'sisa'          => ['type' => 'INT', 'constraint' => 11, 'default' => 12],
                'sisa_kuota'    => ['type' => 'INT', 'constraint' => 11, 'default' => 12],
                'created_at'    => ['type' => 'DATETIME', 'null' => true],
                'updated_at'    => ['type' => 'DATETIME', 'null' => true]
            ]);
            $forge->addKey('id', true);
            $forge->createTable('kuota_cuti', true);
        } else {
            if (!$this->db->fieldExists('sisa_kuota', 'kuota_cuti')) {
                $forge->addColumn('kuota_cuti', [
                    'sisa_kuota' => ['type' => 'INT', 'constraint' => 11, 'default' => 12]
                ]);
            }
            if (!$this->db->fieldExists('sisa', 'kuota_cuti')) {
                $forge->addColumn('kuota_cuti', [
                    'sisa' => ['type' => 'INT', 'constraint' => 11, 'default' => 12]
                ]);
            }
        }

        // 3. Tabel Keluhan Karyawan
        if (!$this->db->tableExists('keluhan_karyawan')) {
            $forge->addField([
                'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'karyawan_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'tanggal'           => ['type' => 'DATE', 'null' => true],
                'kategori'          => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'Fasilitas'],
                'judul'             => ['type' => 'VARCHAR', 'constraint' => 255],
                'deskripsi'         => ['type' => 'TEXT', 'null' => true],
                'status'            => ['type' => 'ENUM', 'constraint' => ['Menunggu', 'Diproses', 'Selesai', 'Ditolak'], 'default' => 'Menunggu'],
                'tanggapan'         => ['type' => 'TEXT', 'null' => true],
                'ditanggapi_oleh'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'tanggal_tanggapan' => ['type' => 'DATETIME', 'null' => true],
                'created_at'        => ['type' => 'DATETIME', 'null' => true],
                'updated_at'        => ['type' => 'DATETIME', 'null' => true]
            ]);
            $forge->addKey('id', true);
            $forge->createTable('keluhan_karyawan', true);

            $this->db->table('keluhan_karyawan')->insertBatch([
                [
                    'karyawan_id'   => 1,
                    'tanggal'       => date('Y-m-d'),
                    'kategori'      => 'Fasilitas',
                    'judul'         => 'AC Ruang Kerja Admin Kurang Dingin',
                    'deskripsi'     => 'AC di ruang kerja lantai 2 perlu perbaikan/service berkala karena suhu terasa panas di siang hari.',
                    'status'        => 'Menunggu',
                    'created_at'    => date('Y-m-d H:i:s')
                ],
                [
                    'karyawan_id'   => 2,
                    'tanggal'       => date('Y-m-d', strtotime('-3 days')),
                    'kategori'      => 'Lingkungan Kerja',
                    'judul'         => 'Permohonan Tambahan Alat Tulis & Printer',
                    'deskripsi'     => 'Penambahan tinta printer warna untuk mencetak berkas sertifikat & kontrak kerja.',
                    'status'        => 'Diproses',
                    'tanggapan'     => 'Sudah disetujui, pembelian tinta sedang diproses divisi pengadaan.',
                    'ditanggapi_oleh'=> 'Direktur Utama',
                    'tanggal_tanggapan' => date('Y-m-d H:i:s', strtotime('-2 days')),
                    'created_at'    => date('Y-m-d H:i:s', strtotime('-3 days'))
                ]
            ]);
        }

        // 4. Tabel Pengajuan Umum
        if (!$this->db->tableExists('pengajuan_umum')) {
            $forge->addField([
                'id'                 => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'karyawan_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'nomor_pengajuan'    => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'kategori_pengajuan' => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'Pengajuan Umum'],
                'judul_pengajuan'    => ['type' => 'VARCHAR', 'constraint' => 255],
                'tanggal_pengajuan'  => ['type' => 'DATE', 'null' => true],
                'tanggal_mulai'      => ['type' => 'DATE', 'null' => true],
                'tanggal_selesai'    => ['type' => 'DATE', 'null' => true],
                'keterangan'         => ['type' => 'TEXT', 'null' => true],
                'bukti_foto'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'status'             => ['type' => 'ENUM', 'constraint' => ['Menunggu', 'Disetujui', 'Ditolak'], 'default' => 'Menunggu'],
                'disetujui_oleh'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'created_at'         => ['type' => 'DATETIME', 'null' => true],
                'updated_at'         => ['type' => 'DATETIME', 'null' => true]
            ]);
            $forge->addKey('id', true);
            $forge->createTable('pengajuan_umum', true);

            $this->db->table('pengajuan_umum')->insertBatch([
                [
                    'karyawan_id'        => 1,
                    'nomor_pengajuan'    => 'PGJ-' . date('Ymd') . '-001',
                    'kategori_pengajuan' => 'Izin Dinas Luar',
                    'judul_pengajuan'    => 'Pengajuan Perjalanan Dinas Lapangan Proyek Karawang',
                    'tanggal_pengajuan'  => date('Y-m-d'),
                    'tanggal_mulai'      => date('Y-m-d', strtotime('+1 day')),
                    'tanggal_selesai'    => date('Y-m-d', strtotime('+2 days')),
                    'keterangan'         => 'Koordinasi fisik & penyusunan BAST lokasi proyek.',
                    'status'             => 'Menunggu',
                    'created_at'         => date('Y-m-d H:i:s')
                ]
            ]);
        } else {
            if (!$this->db->fieldExists('bukti_foto', 'pengajuan_umum')) {
                $forge->addColumn('pengajuan_umum', [
                    'bukti_foto' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true]
                ]);
            }
        }
    }

    // ==========================================
    // 1. SEMUA PENGAJUAN (OVERVIEW)
    // ==========================================
    public function semua()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $pengajuan = $this->db->table('pengajuan_umum')->orderBy('id', 'DESC')->get()->getResultArray();
        $cuti = $this->db->table('cuti')->where('deleted_at', null)->orderBy('id', 'DESC')->get()->getResultArray();
        $keluhan = $this->db->table('keluhan_karyawan')->orderBy('id', 'DESC')->get()->getResultArray();

        $data = [
            'title'     => 'Semua Pengajuan',
            'subtitle'  => 'Pusat Manajemen Pengajuan & Permohonan Administrasi CDW',
            'active'    => 'pengajuan-semua',
            'pengajuan' => $pengajuan,
            'cuti'      => $cuti,
            'keluhan'   => $keluhan,
            'user'      => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/pengajuan/semua', $data);
    }

    private function getAdminKuotaInfo()
    {
        $karyawanId = session()->get('karyawan_id');
        if (!$karyawanId) {
            $userEmail = session()->get('email');
            if ($userEmail) {
                $kar = $this->db->table('karyawan')->where('email', $userEmail)->get()->getRowArray();
                if ($kar) $karyawanId = $kar['id'];
            }
        }
        if (!$karyawanId) {
            $karFirst = $this->db->table('karyawan')->where('deleted_at', null)->get()->getRowArray();
            $karyawanId = $karFirst ? $karFirst['id'] : 1;
        }

        $kuota = $this->db->table('kuota_cuti')->where('karyawan_id', $karyawanId)->get()->getRowArray();
        $sisaVal = $kuota ? (int)($kuota['sisa_kuota'] ?? $kuota['sisa'] ?? max(0, ($kuota['kuota_tahunan'] ?? 12) - ($kuota['terpakai'] ?? 0))) : 0;
        return [
            'karyawan_id' => $karyawanId,
            'kuota'       => $kuota,
            'sisa_kuota'  => $sisaVal,
            'can_add'     => ($kuota && $sisaVal > 0)
        ];
    }

    public function tambah()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $kuotaData = $this->getAdminKuotaInfo();
        $currentKaryawan = $this->db->table('karyawan')->where('id', $kuotaData['karyawan_id'])->get()->getRowArray();

        $data = [
            'title'           => 'Form Pengajuan Permohonan / Izin',
            'subtitle'        => 'Isi Formulir Pengajuan Sakit, Kecelakaan, WFH, WFC, Dinas, & Izin (Luar Cuti)',
            'active'          => 'pengajuan-semua',
            'currentKaryawan' => $currentKaryawan,
            'user'            => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/pengajuan/tambah', $data);
    }

    public function simpan()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $tglMulai = $this->request->getPost('tanggal_mulai') ?: date('Y-m-d');
        $tglSelesai = $this->request->getPost('tanggal_selesai') ?: date('Y-m-d');
        $keterangan = trim($this->request->getPost('keterangan') ?? '');

        if ($tglSelesai < $tglMulai) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengirim pengajuan! Tanggal selesai tidak boleh lebih awal dari tanggal mulai.');
        }

        if (empty($keterangan)) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengirim pengajuan! Keterangan & alasan permohonan wajib diisi.');
        }

        // Validasi dan Upload Bukti Foto (Wajib)
        $fileBukti = $this->request->getFile('bukti_foto');
        if (!$fileBukti || !$fileBukti->isValid() || $fileBukti->hasMoved()) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengirim pengajuan! Wajib mengunggah file foto bukti pendukung.');
        }

        $uploadDir = FCPATH . 'uploads/pengajuan/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $newName = $fileBukti->getRandomName();
        $fileBukti->move($uploadDir, $newName);
        $namaFoto = 'uploads/pengajuan/' . $newName;

        // Kompresi Gambar Otomatis (Max Dimension 1024px, Quality 75%)
        try {
            $imageService = \Config\Services::image();
            $imageService->withFile($uploadDir . $newName)
                         ->resize(1024, 1024, true, 'height')
                         ->save($uploadDir . $newName, 75);
        } catch (\Throwable $e) {
            log_message('error', 'Gagal mengompres foto pengajuan: ' . $e->getMessage());
        }

        $kuotaData = $this->getAdminKuotaInfo();

        $insertData = [
            'karyawan_id'        => $kuotaData['karyawan_id'],
            'nomor_pengajuan'    => 'PGJ-' . date('Ymd') . '-' . rand(100, 999),
            'kategori_pengajuan' => $this->request->getPost('kategori_pengajuan') ?: 'Permohonan Administrasi',
            'judul_pengajuan'    => $this->request->getPost('judul_pengajuan'),
            'tanggal_pengajuan'  => date('Y-m-d'),
            'tanggal_mulai'      => $tglMulai,
            'tanggal_selesai'    => $tglSelesai,
            'keterangan'         => $keterangan,
            'bukti_foto'         => $namaFoto,
            'status'             => 'Menunggu',
            'created_at'         => date('Y-m-d H:i:s')
        ];

        $this->db->table('pengajuan_umum')->insert($insertData);

        return redirect()->to(base_url('admin/pengajuan/semua'))->with('success', 'Permohonan/izin berhasil dikirim dan foto bukti telah berhasil dikompres.');
    }

    public function detail($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $p = $this->db->table('pengajuan_umum')
            ->select('pengajuan_umum.*, karyawan.nama_lengkap, karyawan.nik, karyawan.divisi, karyawan.jabatan')
            ->join('karyawan', 'karyawan.id = pengajuan_umum.karyawan_id', 'left')
            ->where('pengajuan_umum.id', $id)
            ->get()->getRowArray();
        if (!$p) {
            return redirect()->to(base_url('admin/pengajuan/semua'))->with('error', 'Pengajuan tidak ditemukan.');
        }

        $data = [
            'title'    => 'Detail Pengajuan',
            'subtitle' => 'Pratinjau Informasi Rincian Permohonan Pengajuan',
            'active'   => 'pengajuan-semua',
            'p'        => $p,
            'user'     => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/pengajuan/detail', $data);
    }

    public function edit($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $p = $this->db->table('pengajuan_umum')->where('id', $id)->get()->getRowArray();
        if (!$p) {
            return redirect()->to(base_url('admin/pengajuan/semua'))->with('error', 'Pengajuan tidak ditemukan.');
        }

        $data = [
            'title'    => 'Edit Pengajuan',
            'subtitle' => 'Perbarui Data & Rincian Pengajuan',
            'active'   => 'pengajuan-semua',
            'p'        => $p,
            'user'     => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/pengajuan/edit', $data);
    }

    public function update()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $id = $this->request->getPost('id');

        $this->db->table('pengajuan_umum')->where('id', $id)->update([
            'kategori_pengajuan' => $this->request->getPost('kategori_pengajuan'),
            'judul_pengajuan'    => $this->request->getPost('judul_pengajuan'),
            'tanggal_mulai'      => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai'    => $this->request->getPost('tanggal_selesai'),
            'keterangan'         => $this->request->getPost('keterangan'),
            'status'             => $this->request->getPost('status') ?: 'Menunggu',
            'updated_at'         => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('admin/pengajuan/semua'))->with('success', 'Pengajuan berhasil diperbarui.');
    }

    public function delete($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $this->db->table('pengajuan_umum')->where('id', $id)->delete();
        return redirect()->to(base_url('admin/pengajuan/semua'))->with('success', 'Data pengajuan berhasil dihapus.');
    }

    // ==========================================
    // 2. PENGAJUAN CUTI (ADMIN)
    // ==========================================
    public function cuti()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $cutiList = $this->db->table('cuti')
            ->select('cuti.*, karyawan.nama_lengkap, karyawan.nik, karyawan.divisi, karyawan.jabatan')
            ->join('karyawan', 'karyawan.id = cuti.karyawan_id', 'left')
            ->where('cuti.deleted_at', null)
            ->orderBy('cuti.id', 'DESC')
            ->get()->getResultArray();

        $kuotaData = $this->getAdminKuotaInfo();

        $data = [
            'title'      => 'Pengajuan Cuti',
            'subtitle'   => 'Daftar Pengajuan Cuti Karyawan CDW Engineering',
            'active'     => 'pengajuan-cuti',
            'cutiList'   => $cutiList,
            'kuotaInfo'  => $kuotaData['kuota'],
            'sisaKuota'  => $kuotaData['sisa_kuota'],
            'canAddCuti' => $kuotaData['can_add'],
            'user'       => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/pengajuan/cuti', $data);
    }

    public function tambahCuti()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $kuotaData = $this->getAdminKuotaInfo();
        $currentKaryawan = $this->db->table('karyawan')->where('id', $kuotaData['karyawan_id'])->get()->getRowArray();

        $data = [
            'title'           => 'Form Pengajuan Cuti Baru',
            'subtitle'        => 'Isi Formulir Permohonan Cuti Tahunan',
            'active'          => 'pengajuan-cuti',
            'currentKaryawan' => $currentKaryawan,
            'kuotaData'       => $kuotaData,
            'kuotaInfo'       => $kuotaData['kuota'],
            'sisaKuota'       => $kuotaData['sisa_kuota'],
            'canAddCuti'      => $kuotaData['can_add'],
            'user'            => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/pengajuan/cuti_tambah', $data);
    }

    public function simpanCuti()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $kuotaData = $this->getAdminKuotaInfo();
        if (!$kuotaData['can_add']) {
            $msg = $kuotaData['kuota'] 
                ? 'Gagal mengajukan cuti! Sisa kuota cuti tahunan Anda telah habis (0 hari).'
                : 'Gagal mengajukan cuti! Jatah kuota cuti tahunan Anda belum ditambahkan oleh Direktur. Silakan minta Direktur untuk menambahkan kuota cuti terlebih dahulu.';
            return redirect()->back()->withInput()->with('error', $msg);
        }

        $tglMulai = $this->request->getPost('tanggal_mulai') ?: date('Y-m-d');
        $tglSelesai = $this->request->getPost('tanggal_selesai') ?: date('Y-m-d');
        
        if ($tglSelesai < $tglMulai) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengajukan cuti! Tanggal selesai cuti tidak boleh lebih awal dari tanggal mulai.');
        }

        $d1 = new \DateTime($tglMulai);
        $d2 = new \DateTime($tglSelesai);
        $lamaHari = $d1->diff($d2)->days + 1;

        if ($lamaHari > $kuotaData['sisa_kuota']) {
            return redirect()->back()->withInput()->with('error', "Gagal mengajukan cuti! Durasi permohonan cuti ({$lamaHari} Hari) MELEBIHI sisa kuota cuti tahunan Anda ({$kuotaData['sisa_kuota']} Hari).");
        }

        $insertData = [
            'karyawan_id'       => $kuotaData['karyawan_id'],
            'nomor_cuti'        => 'CTI-' . date('Ymd') . '-' . rand(100, 999),
            'jenis_cuti'        => $this->request->getPost('jenis_cuti') ?: 'Tahunan',
            'alasan'            => $this->request->getPost('alasan'),
            'tanggal_mulai'     => $tglMulai,
            'tanggal_selesai'   => $tglSelesai,
            'lama_hari'         => $lamaHari,
            'sisa_cuti_tahunan' => $kuotaData['sisa_kuota'],
            'tanggal_pengajuan' => date('Y-m-d'),
            'created_at'        => date('Y-m-d H:i:s')
        ];

        if ($this->db->fieldExists('status', 'cuti')) {
            $insertData['status'] = 'Menunggu';
        }
        if ($this->db->fieldExists('status_hrd', 'cuti')) {
            $insertData['status_hrd'] = 'Menunggu';
        }
        if ($this->db->fieldExists('status_direktur', 'cuti')) {
            $insertData['status_direktur'] = 'Menunggu';
        }

        $this->db->table('cuti')->insert($insertData);

        return redirect()->to(base_url('admin/pengajuan/cuti'))->with('success', 'Pengajuan cuti berhasil dikirim ke Direktur.');
    }

    public function detailCuti($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $c = $this->db->table('cuti')
            ->select('cuti.*, karyawan.nama_lengkap, karyawan.nik, karyawan.divisi, karyawan.jabatan')
            ->join('karyawan', 'karyawan.id = cuti.karyawan_id', 'left')
            ->where('cuti.id', $id)
            ->get()->getRowArray();

        if (!$c) {
            return redirect()->to(base_url('admin/pengajuan/cuti'))->with('error', 'Data pengajuan cuti tidak ditemukan.');
        }

        $data = [
            'title'    => 'Detail Pengajuan Cuti',
            'subtitle' => 'Pratinjau Lengkap Data Permohonan Cuti',
            'active'   => 'pengajuan-cuti',
            'c'        => $c,
            'user'     => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/pengajuan/cuti_detail', $data);
    }

    public function editCuti($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $c = $this->db->table('cuti')->where('id', $id)->get()->getRowArray();
        if (!$c) {
            return redirect()->to(base_url('admin/pengajuan/cuti'))->with('error', 'Data pengajuan cuti tidak ditemukan.');
        }

        $data = [
            'title'    => 'Edit Pengajuan Cuti',
            'subtitle' => 'Perbarui Data Permohonan Cuti',
            'active'   => 'pengajuan-cuti',
            'c'        => $c,
            'user'     => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/pengajuan/cuti_edit', $data);
    }

    private function recalculateKuotaCuti($karyawanId)
    {
        if (!$karyawanId) return;

        $approvedSum = $this->db->table('cuti')
            ->selectSum('lama_hari')
            ->where('karyawan_id', $karyawanId)
            ->where('LOWER(status)', 'disetujui')
            ->where('deleted_at', null)
            ->get()->getRowArray();

        $totalTerpakai = (int)($approvedSum['lama_hari'] ?? 0);

        $kuota = $this->db->table('kuota_cuti')->where('karyawan_id', $karyawanId)->get()->getRowArray();
        if ($kuota) {
            $kuotaTahunan = (int)($kuota['kuota_tahunan'] ?? 12);
            $sisaBaru = max(0, $kuotaTahunan - $totalTerpakai);

            $updateData = [
                'terpakai'   => $totalTerpakai,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            if ($this->db->fieldExists('sisa_kuota', 'kuota_cuti')) $updateData['sisa_kuota'] = $sisaBaru;
            if ($this->db->fieldExists('sisa', 'kuota_cuti')) $updateData['sisa'] = $sisaBaru;

            $this->db->table('kuota_cuti')->where('id', $kuota['id'])->update($updateData);
        }
    }

    public function updateCuti()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $id = $this->request->getPost('id');
        $cuti = $this->db->table('cuti')->where('id', $id)->get()->getRowArray();
        if (!$cuti) {
            return redirect()->to(base_url('admin/pengajuan/cuti'))->with('error', 'Data pengajuan cuti tidak ditemukan.');
        }

        $tglMulai = $this->request->getPost('tanggal_mulai');
        $tglSelesai = $this->request->getPost('tanggal_selesai');

        if ($tglSelesai < $tglMulai) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui cuti! Tanggal selesai cuti tidak boleh lebih awal dari tanggal mulai.');
        }

        $d1 = new \DateTime($tglMulai);
        $d2 = new \DateTime($tglSelesai);
        $lamaHari = $d1->diff($d2)->days + 1;

        $updateData = [
            'jenis_cuti'      => $this->request->getPost('jenis_cuti'),
            'alasan'          => $this->request->getPost('alasan'),
            'tanggal_mulai'   => $tglMulai,
            'tanggal_selesai' => $tglSelesai,
            'lama_hari'       => $lamaHari,
            'updated_at'      => date('Y-m-d H:i:s')
        ];

        if ($this->db->fieldExists('status', 'cuti')) {
            $updateData['status'] = $this->request->getPost('status') ?: 'Menunggu';
        }

        $this->db->table('cuti')->where('id', $id)->update($updateData);

        if (!empty($cuti['karyawan_id'])) {
            $this->recalculateKuotaCuti($cuti['karyawan_id']);
        }

        return redirect()->to(base_url('admin/pengajuan/cuti'))->with('success', 'Data pengajuan cuti berhasil diperbarui dan jatah kuota cuti disinkronkan.');
    }

    public function deleteCuti($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $cuti = $this->db->table('cuti')->where('id', $id)->get()->getRowArray();
        $this->db->table('cuti')->where('id', $id)->update(['deleted_at' => date('Y-m-d H:i:s')]);
        
        if (!empty($cuti['karyawan_id'])) {
            $this->recalculateKuotaCuti($cuti['karyawan_id']);
        }

        return redirect()->to(base_url('admin/pengajuan/cuti'))->with('success', 'Data pengajuan cuti berhasil dihapus.');
    }

    private function ensureKasbonSchema()
    {
        $forge = \Config\Database::forge();
        if (!$this->db->tableExists('form_kasbon')) {
            $forge->addField([
                'id'                 => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'nomor_kasbon'       => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'karyawan_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'jumlah_kasbon'      => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
                'sisa_pinjaman'      => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true],
                'keperluan'          => ['type' => 'TEXT', 'null' => true],
                'metode_pembayaran'  => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'Potong Gaji'],
                'jumlah_angsuran'    => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
                'status_direktur'    => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Menunggu'],
                'status_keseluruhan' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Belum Lunas'],
                'catatan'            => ['type' => 'TEXT', 'null' => true],
                'created_at'         => ['type' => 'DATETIME', 'null' => true],
                'updated_at'         => ['type' => 'DATETIME', 'null' => true],
                'deleted_at'         => ['type' => 'DATETIME', 'null' => true]
            ]);
            $forge->addKey('id', true);
            $forge->createTable('form_kasbon', true);
        } else {
            $fields = $this->db->getFieldNames('form_kasbon');
            if (!in_array('keperluan', $fields)) {
                $this->db->query("ALTER TABLE `form_kasbon` ADD COLUMN `keperluan` TEXT DEFAULT NULL");
            }
            if (!in_array('sisa_pinjaman', $fields)) {
                $this->db->query("ALTER TABLE `form_kasbon` ADD COLUMN `sisa_pinjaman` DECIMAL(15,2) DEFAULT NULL");
            }
            if (!in_array('metode_pembayaran', $fields)) {
                $this->db->query("ALTER TABLE `form_kasbon` ADD COLUMN `metode_pembayaran` VARCHAR(100) DEFAULT 'Potong Gaji'");
            }
            if (!in_array('jumlah_angsuran', $fields)) {
                $this->db->query("ALTER TABLE `form_kasbon` ADD COLUMN `jumlah_angsuran` INT DEFAULT 1");
            }
            if (!in_array('status_direktur', $fields)) {
                $this->db->query("ALTER TABLE `form_kasbon` ADD COLUMN `status_direktur` VARCHAR(50) DEFAULT 'Menunggu'");
            }
            if (!in_array('status_keseluruhan', $fields)) {
                $this->db->query("ALTER TABLE `form_kasbon` ADD COLUMN `status_keseluruhan` VARCHAR(50) DEFAULT 'Belum Lunas'");
            }
            if (!in_array('created_by', $fields)) {
                $this->db->query("ALTER TABLE `form_kasbon` ADD COLUMN `created_by` INT DEFAULT NULL");
            }
        }
    }

    public function kasbon()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureKasbonSchema();
        
        $userId     = session()->get('user_id');
        $karyawanId = session()->get('karyawan_id');

        // Cari karyawan_id jika belum ada di session
        if (!$karyawanId && $userId) {
            $user = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
            $karyawanId = $user['karyawan_id'] ?? null;
            if (!$karyawanId && !empty($user['email'])) {
                $kar = $this->db->table('karyawan')->where('email', $user['email'])->get()->getRowArray();
                if ($kar) $karyawanId = $kar['id'];
            }
        }

        $status = $this->request->getGet('status');
        
        $builder = $this->db->table('form_kasbon k')
            ->select('k.*, kar.nik, kar.nama_lengkap, kar.jabatan, kar.divisi')
            ->join('karyawan kar', 'kar.id = k.karyawan_id', 'left')
            ->where('k.deleted_at', null);

        // FILTER: HANYA DATA MILIK AKUN YANG LOGIN SAJA
        if ($karyawanId) {
            $builder->where('k.karyawan_id', $karyawanId);
        } else {
            $userName = session()->get('name');
            $builder->groupStart()
                    ->where('k.created_by', $userId)
                    ->orWhere('kar.nama_lengkap', $userName)
                    ->groupEnd();
        }

        if ($status === 'pending') {
            $builder->where('k.status_direktur', 'Menunggu');
        } elseif ($status === 'approved') {
            $builder->where('k.status_direktur', 'Disetujui');
        } elseif ($status === 'rejected') {
            $builder->where('k.status_direktur', 'Ditolak');
        }

        $kasbonList = $builder->orderBy('k.id', 'DESC')->get()->getResultArray();
        
        // Pemohon adalah akun login sendiri
        $userKaryawan = null;
        if ($karyawanId) {
            $userKaryawan = $this->db->table('karyawan')->where('id', $karyawanId)->get()->getRowArray();
        }

        // Hitung Tunggakan & Cicilan Kasbon Sebelumnya yang Aktif (Disetujui & Belum Lunas)
        $tunggakanBulanIni = 0;
        $totalSisaSebelumnya = 0;
        $activeKasbonCount = 0;

        if ($karyawanId) {
            $activeKasbon = $this->db->table('form_kasbon')
                ->where('karyawan_id', $karyawanId)
                ->where('deleted_at', null)
                ->where('status_direktur', 'Disetujui')
                ->where('status_keseluruhan !=', 'Lunas')
                ->where('sisa_pinjaman >', 0)
                ->get()->getResultArray();

            foreach ($activeKasbon as $ak) {
                $sisa   = floatval($ak['sisa_pinjaman'] ?? $ak['jumlah_kasbon'] ?? 0);
                $angsur = intval($ak['jumlah_angsuran'] ?? 1);
                if ($angsur < 1) $angsur = 1;
                $jml    = floatval($ak['jumlah_kasbon'] ?? 0);

                // Potongan per bulan dari kasbon ini = total / angsuran
                $cicilanBulan = round($jml / $angsur);
                if ($cicilanBulan > $sisa) {
                    $cicilanBulan = $sisa;
                }

                $tunggakanBulanIni   += $cicilanBulan;
                $totalSisaSebelumnya += $sisa;
                $activeKasbonCount++;
            }
        }

        $karyawanList = $this->db->table('karyawan')->orderBy('nama_lengkap', 'ASC')->get()->getResultArray();

        $data = [
            'title'               => 'Pengajuan Kasbon Saya',
            'kasbonList'          => $kasbonList,
            'userKaryawan'        => $userKaryawan,
            'karyawanList'        => $karyawanList,
            'filterStatus'        => $status,
            'tunggakanBulanIni'   => $tunggakanBulanIni,
            'totalSisaSebelumnya' => $totalSisaSebelumnya,
            'activeKasbonCount'   => $activeKasbonCount
        ];

        return view('admin/pengajuan/kasbon', $data);
    }

    public function simpanKasbon()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureKasbonSchema();

        $userId     = session()->get('user_id');
        $karyawanId = session()->get('karyawan_id');

        if (!$karyawanId && $userId) {
            $user = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
            $karyawanId = $user['karyawan_id'] ?? null;
        }

        $formKaryawanId = $this->request->getPost('karyawan_id') ?: $karyawanId;
        $nominal        = preg_replace('/[^0-9]/', '', $this->request->getPost('jumlah_kasbon'));
        
        $insertData = [
            'nomor_kasbon'        => 'KSB-' . date('Ymd') . '-' . rand(100, 999),
            'karyawan_id'         => $formKaryawanId,
            'created_by'          => $userId,
            'jumlah_kasbon'       => floatval($nominal),
            'sisa_pinjaman'       => floatval($nominal),
            'keperluan'           => $this->request->getPost('keperluan'),
            'metode_pembayaran'   => $this->request->getPost('metode_pembayaran') ?: 'Potong Gaji',
            'jumlah_angsuran'     => $this->request->getPost('jumlah_angsuran') ?: 1,
            'status_direktur'     => 'Menunggu',
            'status_keseluruhan'  => 'Belum Lunas',
            'created_at'          => date('Y-m-d H:i:s'),
            'updated_at'          => date('Y-m-d H:i:s')
        ];

        $this->db->table('form_kasbon')->insert($insertData);

        return redirect()->to(base_url('admin/pengajuan/kasbon'))->with('success', 'Pengajuan Kasbon Anda berhasil disimpan dan terhubung ke Monitoring Direktur.');
    }

    public function deleteKasbon($id)
    {
        if ($r = $this->checkAccess()) return $r;

        $this->db->table('form_kasbon')->where('id', $id)->update(['deleted_at' => date('Y-m-d H:i:s')]);

        return redirect()->to(base_url('admin/pengajuan/kasbon'))->with('success', 'Pengajuan kasbon berhasil dihapus.');
    }
}
