<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Fasilitas extends BaseController
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

        // 1. buku_tamu
        if (!$this->db->tableExists('buku_tamu')) {
            $forge->addField([
                'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'tanggal_jam'    => ['type' => 'DATETIME', 'null' => true],
                'nama_tamu'      => ['type' => 'VARCHAR', 'constraint' => 255],
                'instansi'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'telepon'        => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'bertemu_dengan' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'keperluan'      => ['type' => 'TEXT', 'null' => true],
                'status'         => ['type' => 'ENUM', 'constraint' => ['Menunggu', 'Bertemu', 'Selesai'], 'default' => 'Bertemu'],
                'created_at'     => ['type' => 'DATETIME', 'null' => true]
            ]);
            $forge->addKey('id', true);
            $forge->createTable('buku_tamu', true);

            $this->db->table('buku_tamu')->insertBatch([
                [
                    'tanggal_jam'    => date('Y-m-d 09:15:00'),
                    'nama_tamu'      => 'Bpk. Irfan Wijaya',
                    'instansi'       => 'PT Siemens Indonesia',
                    'telepon'        => '081234567890',
                    'bertemu_dengan' => 'Direktur Utama',
                    'keperluan'      => 'Diskusi Kerjasama Proyek Baru & Penandatanganan MoU',
                    'status'         => 'Bertemu',
                    'created_at'     => date('Y-m-d H:i:s')
                ],
                [
                    'tanggal_jam'    => date('Y-m-d 11:00:00'),
                    'nama_tamu'      => 'Ibu Diana Rahmawati',
                    'instansi'       => 'PT PLN Nusantara Power',
                    'telepon'        => '081398765432',
                    'bertemu_dengan' => 'Manajer Divisi Pengadaan',
                    'keperluan'      => 'Klarifikasi Dokumen Kualifikasi Vendor',
                    'status'         => 'Selesai',
                    'created_at'     => date('Y-m-d H:i:s')
                ],
                [
                    'tanggal_jam'    => date('Y-m-d 14:30:00'),
                    'nama_tamu'      => 'Bpk. Ahmad Subandi',
                    'instansi'       => 'PT Pertamina Trans Kontinental',
                    'telepon'        => '081511223344',
                    'bertemu_dengan' => 'Project Manager Teknik',
                    'keperluan'      => 'Koordinasi Lapangan & BAST Pekerjaan',
                    'status'         => 'Menunggu',
                    'created_at'     => date('Y-m-d H:i:s')
                ]
            ]);
        }

        // 2. booking_ruang
        if (!$this->db->tableExists('booking_ruang')) {
            $forge->addField([
                'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'nama_ruangan'   => ['type' => 'VARCHAR', 'constraint' => 255],
                'tanggal'        => ['type' => 'DATE', 'null' => true],
                'jam_mulai'      => ['type' => 'TIME', 'null' => true],
                'jam_selesai'    => ['type' => 'TIME', 'null' => true],
                'peminjam'       => ['type' => 'VARCHAR', 'constraint' => 255],
                'divisi'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'agenda'         => ['type' => 'TEXT', 'null' => true],
                'jumlah_peserta' => ['type' => 'INT', 'constraint' => 11, 'default' => 5],
                'status'         => ['type' => 'ENUM', 'constraint' => ['Pending', 'Disetujui', 'Ditolak', 'Selesai'], 'default' => 'Disetujui'],
                'created_at'     => ['type' => 'DATETIME', 'null' => true]
            ]);
            $forge->addKey('id', true);
            $forge->createTable('booking_ruang', true);

            $this->db->table('booking_ruang')->insertBatch([
                [
                    'nama_ruangan'   => 'Ruang Rapat Utama (Lt 2)',
                    'tanggal'        => date('Y-m-d'),
                    'jam_mulai'      => '10:00:00',
                    'jam_selesai'    => '11:30:00',
                    'peminjam'       => 'Budi Santoso',
                    'divisi'         => 'Technical Engineering',
                    'agenda'         => 'Weekly Progress Review Proyek CDW',
                    'jumlah_peserta' => 12,
                    'status'         => 'Disetujui',
                    'created_at'     => date('Y-m-d H:i:s')
                ],
                [
                    'nama_ruangan'   => 'Ruang Diskusi Teknik (Lt 1)',
                    'tanggal'        => date('Y-m-d'),
                    'jam_mulai'      => '13:30:00',
                    'jam_selesai'    => '15:00:00',
                    'peminjam'       => 'Rina Astuti',
                    'divisi'         => 'Project Management Office',
                    'agenda'         => 'Meeting Koordinasi Vendor Subkontraktor',
                    'jumlah_peserta' => 6,
                    'status'         => 'Pending',
                    'created_at'     => date('Y-m-d H:i:s')
                ],
                [
                    'nama_ruangan'   => 'Executive Boardroom (Lt 3)',
                    'tanggal'        => date('Y-m-d', strtotime('+1 day')),
                    'jam_mulai'      => '09:00:00',
                    'jam_selesai'    => '12:00:00',
                    'peminjam'       => 'Siska Rahmawati',
                    'divisi'         => 'Direksi & Manajemen',
                    'agenda'         => 'Rapat Evaluation Board & Strategic Plan 2026',
                    'jumlah_peserta' => 8,
                    'status'         => 'Disetujui',
                    'created_at'     => date('Y-m-d H:i:s')
                ]
            ]);
        }

        // 3. koordinasi_kendaraan
        if (!$this->db->tableExists('koordinasi_kendaraan')) {
            $forge->addField([
                'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'nama_kendaraan'  => ['type' => 'VARCHAR', 'constraint' => 255],
                'plat_nomor'      => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'driver'          => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'pengguna'        => ['type' => 'VARCHAR', 'constraint' => 255],
                'tujuan'          => ['type' => 'VARCHAR', 'constraint' => 255],
                'tanggal_mulai'   => ['type' => 'DATETIME', 'null' => true],
                'tanggal_selesai' => ['type' => 'DATETIME', 'null' => true],
                'status'          => ['type' => 'ENUM', 'constraint' => ['Pending', 'Disetujui', 'Sedang Berjalan', 'Selesai', 'Ditolak'], 'default' => 'Sedang Berjalan'],
                'catatan'         => ['type' => 'TEXT', 'null' => true],
                'created_at'      => ['type' => 'DATETIME', 'null' => true]
            ]);
            $forge->addKey('id', true);
            $forge->createTable('koordinasi_kendaraan', true);

            $this->db->table('koordinasi_kendaraan')->insertBatch([
                [
                    'nama_kendaraan'  => 'Toyota Avanza Veloz (Hitam)',
                    'plat_nomor'      => 'B 1234 CDW',
                    'driver'          => 'Pak Joko (Driver Operational)',
                    'pengguna'        => 'Tim Teknisi Lapangan (SPK #104)',
                    'tujuan'          => 'Kunjungan Site Installation - Karawang Plant',
                    'tanggal_mulai'   => date('Y-m-d 08:00:00'),
                    'tanggal_selesai' => date('Y-m-d 17:00:00'),
                    'status'          => 'Sedang Berjalan',
                    'catatan'         => 'Membawa perkakas instrumen ukur & K3 keselamatan kerja.',
                    'created_at'      => date('Y-m-d H:i:s')
                ],
                [
                    'nama_kendaraan'  => 'Mitsubishi Triton Double Cab 4x4',
                    'plat_nomor'      => 'B 9876 CDW',
                    'driver'          => 'Pak Hendra (Driver Heavy Duty)',
                    'pengguna'        => 'Tim Logistics & Inventory',
                    'tujuan'          => 'Pengiriman Sparepart Heavy Machinery - Cikarang',
                    'tanggal_mulai'   => date('Y-m-d 09:30:00'),
                    'tanggal_selesai' => date('Y-m-d 16:00:00'),
                    'status'          => 'Disetujui',
                    'catatan'         => 'Pengambilan panel kontrol listrik tambahan.',
                    'created_at'      => date('Y-m-d H:i:s')
                ],
                [
                    'nama_kendaraan'  => 'Honda CR-V Turbo (Putih)',
                    'plat_nomor'      => 'B 5678 CDW',
                    'driver'          => 'Pak Agus (Executive Driver)',
                    'pengguna'        => 'Bpk. Hendra Wijaya (Direksi)',
                    'tujuan'          => 'Pertemuan Klien Kemitraan - Jakarta Pusat',
                    'tanggal_mulai'   => date('Y-m-d 13:00:00'),
                    'tanggal_selesai' => date('Y-m-d 18:00:00'),
                    'status'          => 'Pending',
                    'catatan'         => 'Penjemputan klien dari Bandara Soekarno Hatta.',
                    'created_at'      => date('Y-m-d H:i:s')
                ]
            ]);
        }
    }

    // ==========================================
    // 1. BUKU TAMU
    // ==========================================
    public function bukuTamu()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $tamu = $this->db->table('buku_tamu')->orderBy('id', 'DESC')->get()->getResultArray();

        $data = [
            'title'    => 'Buku Tamu',
            'subtitle' => 'Pencatatan Tamu Kantor & Kunjungan Resmi',
            'active'   => 'buku-tamu',
            'tamu'     => $tamu,
            'user'     => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/fasilitas/buku_tamu', $data);
    }

    public function simpanBukuTamu()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $tglJam = $this->request->getPost('tanggal_jam') ?: date('Y-m-d H:i:s');
        if (strlen($tglJam) == 10) {
            $tglJam .= ' ' . date('H:i:s');
        }

        $this->db->table('buku_tamu')->insert([
            'tanggal_jam'    => $tglJam,
            'nama_tamu'      => $this->request->getPost('nama_tamu'),
            'instansi'       => $this->request->getPost('instansi'),
            'telepon'        => $this->request->getPost('telepon'),
            'bertemu_dengan' => $this->request->getPost('bertemu_dengan'),
            'keperluan'      => $this->request->getPost('keperluan'),
            'status'         => $this->request->getPost('status') ?: 'Bertemu',
            'created_at'     => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('admin/fasilitas/buku-tamu'))->with('success', 'Pencatatan tamu berhasil disimpan.');
    }

    public function detailBukuTamu($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $t = $this->db->table('buku_tamu')->where('id', $id)->get()->getRowArray();
        if (!$t) {
            return redirect()->to(base_url('admin/fasilitas/buku-tamu'))->with('error', 'Data tamu tidak ditemukan.');
        }

        $data = [
            'title'    => 'Detail Kunjungan Tamu',
            'subtitle' => 'Informasi Lengkap Tamu Kantor & Kunjungan Resmi',
            'active'   => 'buku-tamu',
            't'        => $t,
            'user'     => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/fasilitas/buku_tamu_detail', $data);
    }

    public function editBukuTamu($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $t = $this->db->table('buku_tamu')->where('id', $id)->get()->getRowArray();
        if (!$t) {
            return redirect()->to(base_url('admin/fasilitas/buku-tamu'))->with('error', 'Data tamu tidak ditemukan.');
        }

        $data = [
            'title'    => 'Edit Kunjungan Tamu',
            'subtitle' => 'Perbarui Data Tamu & Rincian Kunjungan',
            'active'   => 'buku-tamu',
            't'        => $t,
            'user'     => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/fasilitas/buku_tamu_edit', $data);
    }

    public function updateBukuTamu()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $id = $this->request->getPost('id');
        $tglJam = $this->request->getPost('tanggal_jam') ?: date('Y-m-d H:i:s');
        if (strlen($tglJam) == 10) {
            $tglJam .= ' ' . date('H:i:s');
        }

        $this->db->table('buku_tamu')->where('id', $id)->update([
            'tanggal_jam'    => $tglJam,
            'nama_tamu'      => $this->request->getPost('nama_tamu'),
            'instansi'       => $this->request->getPost('instansi'),
            'telepon'        => $this->request->getPost('telepon'),
            'bertemu_dengan' => $this->request->getPost('bertemu_dengan'),
            'keperluan'      => $this->request->getPost('keperluan'),
            'status'         => $this->request->getPost('status') ?: 'Bertemu'
        ]);

        return redirect()->to(base_url('admin/fasilitas/buku-tamu'))->with('success', 'Data kunjungan tamu berhasil diperbarui.');
    }

    public function updateStatusBukuTamu()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');

        $this->db->table('buku_tamu')->where('id', $id)->update([
            'status' => $status
        ]);

        return redirect()->to(base_url('admin/fasilitas/buku-tamu'))->with('success', 'Status kunjungan tamu berhasil diperbarui.');
    }

    public function deleteBukuTamu($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $this->db->table('buku_tamu')->where('id', $id)->delete();
        return redirect()->to(base_url('admin/fasilitas/buku-tamu'))->with('success', 'Data tamu berhasil dihapus.');
    }

    // ==========================================
    // 2. BOOKING RUANG MEETING
    // ==========================================
    public function bookingRuang()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $booking = $this->db->table('booking_ruang')->orderBy('id', 'DESC')->get()->getResultArray();

        $data = [
            'title'    => 'Booking Ruang Meeting',
            'subtitle' => 'Jadwal & Reservasi Ruang Rapat Kantor',
            'active'   => 'booking-ruang',
            'booking'  => $booking,
            'user'     => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/fasilitas/booking_ruang', $data);
    }

    public function simpanBookingRuang()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $this->db->table('booking_ruang')->insert([
            'nama_ruangan'   => $this->request->getPost('nama_ruangan'),
            'tanggal'        => $this->request->getPost('tanggal') ?: date('Y-m-d'),
            'jam_mulai'      => $this->request->getPost('jam_mulai'),
            'jam_selesai'    => $this->request->getPost('jam_selesai'),
            'peminjam'       => $this->request->getPost('peminjam'),
            'divisi'         => $this->request->getPost('divisi'),
            'agenda'         => $this->request->getPost('agenda'),
            'jumlah_peserta' => $this->request->getPost('jumlah_peserta') ?: 5,
            'status'         => $this->request->getPost('status') ?: 'Pending',
            'created_at'     => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('admin/fasilitas/booking-ruang'))->with('success', 'Reservasi ruang rapat berhasil diajukan.');
    }

    public function detailBookingRuang($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $b = $this->db->table('booking_ruang')->where('id', $id)->get()->getRowArray();
        if (!$b) {
            return redirect()->to(base_url('admin/fasilitas/booking-ruang'))->with('error', 'Data reservasi ruangan tidak ditemukan.');
        }

        $data = [
            'title'    => 'Detail Booking Ruang Meeting',
            'subtitle' => 'Pratinjau Lengkap Reservasi Ruang Rapat',
            'active'   => 'booking-ruang',
            'b'        => $b,
            'user'     => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/fasilitas/booking_ruang_detail', $data);
    }

    public function editBookingRuang($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $b = $this->db->table('booking_ruang')->where('id', $id)->get()->getRowArray();
        if (!$b) {
            return redirect()->to(base_url('admin/fasilitas/booking-ruang'))->with('error', 'Data reservasi ruangan tidak ditemukan.');
        }

        $data = [
            'title'    => 'Edit Booking Ruang Meeting',
            'subtitle' => 'Perbarui Jadwal & Rincian Reservasi Rapat',
            'active'   => 'booking-ruang',
            'b'        => $b,
            'user'     => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/fasilitas/booking_ruang_edit', $data);
    }

    public function updateBookingRuang()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $id = $this->request->getPost('id');

        $this->db->table('booking_ruang')->where('id', $id)->update([
            'nama_ruangan'   => $this->request->getPost('nama_ruangan'),
            'tanggal'        => $this->request->getPost('tanggal') ?: date('Y-m-d'),
            'jam_mulai'      => $this->request->getPost('jam_mulai'),
            'jam_selesai'    => $this->request->getPost('jam_selesai'),
            'peminjam'       => $this->request->getPost('peminjam'),
            'divisi'         => $this->request->getPost('divisi'),
            'agenda'         => $this->request->getPost('agenda'),
            'jumlah_peserta' => $this->request->getPost('jumlah_peserta') ?: 5,
            'status'         => $this->request->getPost('status') ?: 'Disetujui'
        ]);

        return redirect()->to(base_url('admin/fasilitas/booking-ruang'))->with('success', 'Reservasi ruang rapat berhasil diperbarui.');
    }

    public function updateStatusBookingRuang()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');

        $this->db->table('booking_ruang')->where('id', $id)->update([
            'status' => $status
        ]);

        return redirect()->to(base_url('admin/fasilitas/booking-ruang'))->with('success', 'Status booking ruangan berhasil diperbarui.');
    }

    public function deleteBookingRuang($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $this->db->table('booking_ruang')->where('id', $id)->delete();
        return redirect()->to(base_url('admin/fasilitas/booking-ruang'))->with('success', 'Data booking ruangan berhasil dihapus.');
    }

    // ==========================================
    // 3. KOORDINASI KENDARAAN
    // ==========================================
    public function kendaraan()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $kendaraan = $this->db->table('koordinasi_kendaraan')->orderBy('id', 'DESC')->get()->getResultArray();

        $data = [
            'title'     => 'Koordinasi Kendaraan Dinas',
            'subtitle'  => 'Kelola Penggunaan Mobil Dinas & Logistik',
            'active'    => 'kendaraan',
            'kendaraan' => $kendaraan,
            'user'      => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/fasilitas/kendaraan', $data);
    }

    public function simpanKendaraan()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $this->db->table('koordinasi_kendaraan')->insert([
            'nama_kendaraan'  => $this->request->getPost('nama_kendaraan'),
            'plat_nomor'      => $this->request->getPost('plat_nomor'),
            'driver'          => $this->request->getPost('driver'),
            'pengguna'        => $this->request->getPost('pengguna'),
            'tujuan'          => $this->request->getPost('tujuan'),
            'tanggal_mulai'   => $this->request->getPost('tanggal_mulai') ?: date('Y-m-d H:i:s'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai') ?: date('Y-m-d H:i:s'),
            'status'          => $this->request->getPost('status') ?: 'Pending',
            'catatan'         => $this->request->getPost('catatan'),
            'created_at'      => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('admin/fasilitas/kendaraan'))->with('success', 'Pengajuan jadwal kendaraan berhasil disimpan.');
    }

    public function detailKendaraan($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $k = $this->db->table('koordinasi_kendaraan')->where('id', $id)->get()->getRowArray();
        if (!$k) {
            return redirect()->to(base_url('admin/fasilitas/kendaraan'))->with('error', 'Data operasional kendaraan tidak ditemukan.');
        }

        $data = [
            'title'     => 'Detail Penugasan Kendaraan',
            'subtitle'  => 'Pratinjau Informasi Operasional Mobil Dinas & Driver',
            'active'    => 'kendaraan',
            'k'         => $k,
            'user'      => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/fasilitas/kendaraan_detail', $data);
    }

    public function editKendaraan($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $k = $this->db->table('koordinasi_kendaraan')->where('id', $id)->get()->getRowArray();
        if (!$k) {
            return redirect()->to(base_url('admin/fasilitas/kendaraan'))->with('error', 'Data operasional kendaraan tidak ditemukan.');
        }

        $data = [
            'title'     => 'Edit Penugasan Kendaraan',
            'subtitle'  => 'Perbarui Rincian Armada, Driver, & Waktu Operasional',
            'active'    => 'kendaraan',
            'k'         => $k,
            'user'      => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/fasilitas/kendaraan_edit', $data);
    }

    public function updateKendaraan()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $id = $this->request->getPost('id');

        $this->db->table('koordinasi_kendaraan')->where('id', $id)->update([
            'nama_kendaraan'  => $this->request->getPost('nama_kendaraan'),
            'plat_nomor'      => $this->request->getPost('plat_nomor'),
            'driver'          => $this->request->getPost('driver'),
            'pengguna'        => $this->request->getPost('pengguna'),
            'tujuan'          => $this->request->getPost('tujuan'),
            'tanggal_mulai'   => $this->request->getPost('tanggal_mulai') ?: date('Y-m-d H:i:s'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai') ?: date('Y-m-d H:i:s'),
            'status'          => $this->request->getPost('status') ?: 'Sedang Berjalan',
            'catatan'         => $this->request->getPost('catatan')
        ]);

        return redirect()->to(base_url('admin/fasilitas/kendaraan'))->with('success', 'Operasional kendaraan berhasil diperbarui.');
    }

    public function updateStatusKendaraan()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');

        $this->db->table('koordinasi_kendaraan')->where('id', $id)->update([
            'status' => $status
        ]);

        return redirect()->to(base_url('admin/fasilitas/kendaraan'))->with('success', 'Status operasional kendaraan berhasil diperbarui.');
    }

    public function deleteKendaraan($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensureTablesExist();

        $this->db->table('koordinasi_kendaraan')->where('id', $id)->delete();
        return redirect()->to(base_url('admin/fasilitas/kendaraan'))->with('success', 'Jadwal operasional kendaraan berhasil dihapus.');
    }
}
