<?php
// app/Controllers/Direktur/Karyawan/KaryawanController.php

namespace App\Controllers\Direktur\Karyawan;

use App\Controllers\BaseController;
use App\Models\KaryawanModel;

class KaryawanController extends BaseController
{
    protected $karyawanModel;

    public function __construct()
    {
        $this->karyawanModel = new KaryawanModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Kelola Karyawan',
            'active' => 'karyawan',
            'karyawan' => $this->karyawanModel->getAllKaryawan()
        ];
        return view('direktur/karyawan/index', $data);
    }

    public function tambah()
    {
        $data = [
            'title' => 'Tambah Karyawan',
            'active' => 'karyawan'
        ];

        return view('direktur/karyawan/tambah', $data);
    }

    public function simpan()
    {
        // Validasi input
        $rules = [
            'nik' => 'required|max_length[20]',
            'nama_lengkap' => 'required|max_length[100]',
            'divisi' => 'required',
            'jabatan' => 'required',
            'status_karyawan' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Cek NIK unik (Termasuk data terhapus/arsip)
        $nikSubmitted = trim($this->request->getPost('nik') ?? '');
        if (!$this->karyawanModel->isNikUnique($nikSubmitted)) {
            $recNik = $this->karyawanModel->generateAutoNik();
            return redirect()->back()->withInput()->with('error', 'NIK "' . esc($nikSubmitted) . '" sudah pernah terdaftar di sistem (termasuk arsip Hapus). Gunakan NIK rekomendasi: <strong>' . $recNik . '</strong>');
        }

        // Siapkan data
        $data = [
            'nik' => $nikSubmitted,
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'tempat_lahir' => $this->request->getPost('tempat_lahir'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'alamat' => $this->request->getPost('alamat'),
            'telepon' => $this->request->getPost('telepon'),
            'email' => $this->request->getPost('email'),
            'divisi' => $this->request->getPost('divisi'),
            'jabatan' => $this->request->getPost('jabatan'),
            'status_karyawan' => $this->request->getPost('status_karyawan'),
            'tanggal_masuk' => $this->request->getPost('tanggal_masuk'),
            'no_ktp' => $this->request->getPost('no_ktp'),
            'no_npwp' => $this->request->getPost('no_npwp')
        ];

        try {
            if ($this->karyawanModel->insert($data)) {
                return redirect()->to(base_url('direktur/karyawan'))->with('success', 'Data karyawan "' . esc($data['nama_lengkap']) . '" berhasil ditambahkan.');
            } else {
                $modelErrors = $this->karyawanModel->errors();
                if(!empty($modelErrors)) {
                    return redirect()->back()->withInput()->with('errors', $modelErrors);
                }
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data karyawan. Silakan periksa kembali isian Anda.');
            }
        } catch (\Throwable $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false || $e->getCode() == 1062) {
                $recNik = $this->karyawanModel->generateAutoNik();
                return redirect()->back()->withInput()->with('error', 'NIK "' . esc($nikSubmitted) . '" sudah digunakan. Silakan gunakan NIK rekomendasi: <strong>' . $recNik . '</strong>');
            }
            throw $e;
        }
    }
    
    public function detail($id)
    {
        $karyawan = $this->karyawanModel->find($id);
        if (!$karyawan) {
            return redirect()->to(base_url('direktur/karyawan'))->with('error', 'Data karyawan tidak ditemukan.');
        }

        $data = [
            'title' => 'Detail Karyawan',
            'active' => 'karyawan',
            'karyawan' => $karyawan
        ];
        return view('direktur/karyawan/detail', $data);
    }
    
    public function edit($id)
    {
        $karyawan = $this->karyawanModel->find($id);
        if (!$karyawan) {
            return redirect()->to(base_url('direktur/karyawan'))->with('error', 'Data karyawan tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Karyawan',
            'active' => 'karyawan',
            'karyawan' => $karyawan
        ];
        return view('direktur/karyawan/edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'nik' => 'required|max_length[20]',
            'nama_lengkap' => 'required|max_length[100]',
            'divisi' => 'required',
            'jabatan' => 'required',
            'status_karyawan' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if (!$this->karyawanModel->isNikUniqueForUpdate($this->request->getPost('nik'), $id)) {
            return redirect()->back()->withInput()->with('error', 'NIK sudah terdaftar di sistem untuk karyawan lain.');
        }

        $data = [
            'nik' => $this->request->getPost('nik'),
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'tempat_lahir' => $this->request->getPost('tempat_lahir'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'alamat' => $this->request->getPost('alamat'),
            'telepon' => $this->request->getPost('telepon'),
            'email' => $this->request->getPost('email'),
            'divisi' => $this->request->getPost('divisi'),
            'jabatan' => $this->request->getPost('jabatan'),
            'status_karyawan' => $this->request->getPost('status_karyawan'),
            'tanggal_masuk' => $this->request->getPost('tanggal_masuk'),
            'no_ktp' => $this->request->getPost('no_ktp'),
            'no_npwp' => $this->request->getPost('no_npwp')
        ];

        if ($this->karyawanModel->update($id, $data)) {
            return redirect()->to(base_url('direktur/karyawan'))->with('success', 'Data karyawan berhasil diupdate.');
        } else {
            $modelErrors = $this->karyawanModel->errors();
            if(!empty($modelErrors)) {
                return redirect()->back()->withInput()->with('errors', $modelErrors);
            }
            return redirect()->back()->withInput()->with('error', 'Gagal update data karyawan.');
        }
    }

    public function tambahDummy()
    {
        $faker_names = ['Budi Santoso', 'Dewi Rahayu', 'Andi Wijaya', 'Siti Nurhaliza', 'Ahmad Fauzi',
                        'Rina Marlina', 'Dodi Firmansyah', 'Lestari Wulandari', 'Hendra Gunawan', 'Mega Pratiwi'];
        $faker_divisi = ['HRD', 'Keuangan', 'Marketing Sales', 'Teknis', 'Engineering', 'Admin', 'Operasional'];
        $faker_jabatan = [
            'HRD'            => ['Staff HRD', 'Manajer HRD', 'Rekrutmen'],
            'Keuangan'       => ['Staff Keuangan', 'Kasir', 'Akuntan'],
            'Marketing Sales'=> ['Sales Executive', 'Marketing Manager', 'Account Manager'],
            'Teknis'         => ['Teknisi Lapangan', 'Supervisor Teknis', 'Teknisi Senior'],
            'Engineering'    => ['Software Engineer', 'System Analyst', 'DevOps'],
            'Admin'          => ['Admin Operasional', 'Staff Admin', 'Office Manager'],
            'Operasional'    => ['Koordinator Lapangan', 'Staf Operasional', 'Driver'],
        ];
        $faker_status = ['Tetap', 'Kontrak', 'Probation'];

        $divisi = $faker_divisi[array_rand($faker_divisi)];
        $jabatan_list = $faker_jabatan[$divisi] ?? ['Staff'];
        $jabatan = $jabatan_list[array_rand($jabatan_list)];
        $nama = $faker_names[array_rand($faker_names)] . ' ' . rand(1, 99);
        $nik_base = strtoupper(substr(str_replace(' ', '', $divisi), 0, 3)) . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

        // Pastikan NIK unik
        $nik = $nik_base;
        $counter = 1;
        while (!$this->karyawanModel->isNikUnique($nik)) {
            $nik = $nik_base . $counter;
            $counter++;
        }

        $data = [
            'nik'             => $nik,
            'nama_lengkap'    => $nama,
            'tempat_lahir'    => 'Jakarta',
            'tanggal_lahir'   => date('Y-m-d', strtotime('-' . rand(22, 50) . ' years')),
            'jenis_kelamin'   => (rand(0, 1) ? 'Laki-laki' : 'Perempuan'),
            'alamat'          => 'Jl. Dummy No. ' . rand(1, 200) . ', Jakarta',
            'telepon'         => '08' . rand(100000000, 999999999),
            'email'           => strtolower(str_replace(' ', '.', $nama)) . '@cdw-dummy.com',
            'divisi'          => $divisi,
            'jabatan'         => $jabatan,
            'status_karyawan' => $faker_status[array_rand($faker_status)],
            'tanggal_masuk'   => date('Y-m-d', strtotime('-' . rand(30, 1800) . ' days')),
        ];

        if ($this->karyawanModel->insert($data)) {
            return redirect()->to(base_url('direktur/karyawan'))
                ->with('success', "Data dummy <strong>\"{$nama}\"</strong> berhasil ditambahkan dengan NIK <strong>{$nik}</strong>.");
        }
        return redirect()->to(base_url('direktur/karyawan'))
            ->with('error', 'Gagal menambahkan data dummy.');
    }

    public function editDummy($id)
    {
        $karyawan = $this->karyawanModel->find($id);
        if (!$karyawan) {
            return redirect()->to(base_url('direktur/karyawan'))->with('error', 'Data karyawan tidak ditemukan.');
        }

        $faker_status = ['Tetap', 'Kontrak', 'Probation'];
        $newStatus = $faker_status[array_rand(array_diff($faker_status, [$karyawan['status_karyawan']]))];

        $data = [
            'telepon'         => '08' . rand(100000000, 999999999),
            'alamat'          => 'Jl. Update Dummy No. ' . rand(1, 200) . ', Jakarta',
            'status_karyawan' => $newStatus,
        ];

        if ($this->karyawanModel->update($id, $data)) {
            return redirect()->to(base_url('direktur/karyawan'))
                ->with('success', "Data <strong>\"{$karyawan['nama_lengkap']}\"</strong> berhasil diperbarui secara dummy (Status: {$newStatus}).");
        }
        return redirect()->to(base_url('direktur/karyawan'))
            ->with('error', 'Gagal melakukan edit dummy.');
    }

    public function delete($id)
    {
        $userModel = new \App\Models\UserModel();
        // Hapus (soft-delete) akun user yang terhubung dengan karyawan ini jika ada
        $userModel->where('karyawan_id', $id)->delete();

        if ($this->karyawanModel->delete($id)) {
            return redirect()->to(base_url('direktur/karyawan'))->with('success', 'Karyawan dan akun terhubung berhasil dihapus.');
        }
        return redirect()->to(base_url('direktur/karyawan'))->with('error', 'Gagal menghapus karyawan.');
    }

    public function akun()
    {
        $userModel = new \App\Models\UserModel();
        
        $data = [
            'title' => 'Kelola Akun Karyawan',
            'active' => 'karyawan',
            'karyawan_belum_akun' => $this->karyawanModel->getKaryawanBelumAkun(),
            // Mengambil semua user aktif yang terhubung dengan karyawan aktif (inner join agar karyawan terhapus tidak muncul)
            'akun_aktif' => $userModel->select('users.*, karyawan.nama_lengkap, karyawan.nik, karyawan.divisi')
                                      ->join('karyawan', 'karyawan.id = users.karyawan_id', 'inner')
                                      ->findAll()
        ];

        return view('direktur/karyawan/akun', $data);
    }
    
    public function generateAkun()
    {
        $karyawan_id = $this->request->getPost('karyawan_id');
        if (!$karyawan_id) {
            return redirect()->back()->with('error', 'Karyawan tidak valid.');
        }

        $karyawan = $this->karyawanModel->find($karyawan_id);
        if (!$karyawan) {
            return redirect()->back()->with('error', 'Karyawan tidak ditemukan.');
        }

        // Generate Username dari nama depan + 3 digit terakhir NIK
        $namaDepan = strtolower(str_replace(' ', '', explode(' ', $karyawan['nama_lengkap'])[0]));
        $usernameBase = $namaDepan . substr($karyawan['nik'], -3);
        $password_plain = 'cdw' . date('Y', strtotime($karyawan['tanggal_lahir'] ?: date('Y-m-d')));

        $db = \Config\Database::connect();
        
        // ===== BERSIHKAN SEMUA AKUN HANTU (soft-deleted) untuk karyawan ini, username sama, ATAU email sama =====
        // Gunakan raw query agar tidak terpengaruh soft-delete
        $emailKaryawan = $karyawan['email'];
        if ($emailKaryawan) {
            $db->query(
                "DELETE FROM users WHERE deleted_at IS NOT NULL AND (username = ? OR karyawan_id = ? OR email = ?)",
                [$usernameBase, $karyawan_id, $emailKaryawan]
            );
        } else {
            $db->query(
                "DELETE FROM users WHERE deleted_at IS NOT NULL AND (username = ? OR karyawan_id = ?)",
                [$usernameBase, $karyawan_id]
            );
        }
        
        // Cek apakah karyawan ini sudah punya akun aktif (tanpa deleted_at)
        $existingAkun = $db->query("SELECT id FROM users WHERE karyawan_id = ? AND deleted_at IS NULL LIMIT 1", [$karyawan_id])->getRow();
        if ($existingAkun) {
            return redirect()->back()->with('error', 'Karyawan ini sudah memiliki akun aktif.');
        }
        
        // Buat username unik jika base masih konflik dengan user aktif lain
        $username = $usernameBase;
        $counter = 1;
        while ($db->query("SELECT id FROM users WHERE username = ? AND deleted_at IS NULL LIMIT 1", [$username])->getRow()) {
            $username = $usernameBase . $counter;
            $counter++;
        }

        // Tentukan Role berdasarkan divisi & jabatan
        $role = 'employee';
        $divisi = strtolower($karyawan['divisi'] ?? '');
        $jabatan = strtolower($karyawan['jabatan'] ?? '');

        if (strpos($divisi, 'direktur') !== false || strpos($jabatan, 'direktur') !== false) {
            $role = 'direktur';
        } elseif (strpos($divisi, 'hrd') !== false || strpos($divisi, 'admin') !== false || strpos($jabatan, 'hrd') !== false) {
            $role = 'admin';
        } elseif (strpos($divisi, 'accounting') !== false || strpos($divisi, 'keuangan') !== false || strpos($jabatan, 'accounting') !== false) {
            $role = 'accounting';
        } elseif (strpos($divisi, 'teknisi') !== false || strpos($jabatan, 'teknisi') !== false) {
            $role = 'teknisi';
        } elseif (strpos($divisi, 'sales') !== false || strpos($divisi, 'marketing') !== false || strpos($jabatan, 'sales') !== false) {
            $role = 'sales';
        } elseif (strpos($divisi, 'software') !== false || strpos($divisi, 'engineer') !== false || strpos($divisi, 'it') !== false || strpos($jabatan, 'software') !== false || strpos($jabatan, 'developer') !== false || strpos($jabatan, 'programmer') !== false) {
            $role = 'software_engineer';
        }

        // Insert langsung via raw query agar bypass soft-delete dan model validation
        $now = date('Y-m-d H:i:s');
        $hashedPassword = password_hash($password_plain, PASSWORD_DEFAULT);
        
        // Tentukan email: gunakan email karyawan jika ada dan tidak dipakai akun aktif lain
        $emailKaryawan = $karyawan['email'];
        $email = $username . '@intranet.cdw'; // default fallback
        if ($emailKaryawan) {
            // Cek apakah email karyawan sudah dipakai akun aktif lain
            $emailConflict = $db->query(
                "SELECT id FROM users WHERE email = ? AND deleted_at IS NULL LIMIT 1",
                [$emailKaryawan]
            )->getRow();
            if (!$emailConflict) {
                $email = $emailKaryawan; // Email aman dipakai
            }
            // Jika konflik, gunakan fallback username@intranet.cdw
        }
        
        $inserted = $db->query(
            "INSERT INTO users (karyawan_id, username, password, name, email, role, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, 'active', ?, ?)",
            [$karyawan['id'], $username, $hashedPassword, $karyawan['nama_lengkap'], $email, $role, $now, $now]
        );

        if ($inserted) {
            $pesan = "Akun berhasil dibuat.<br>Username: <strong>$username</strong><br>Password: <strong>$password_plain</strong><br><em>(Harap catat password ini dan berikan kepada karyawan!)</em>";
            return redirect()->back()->with('success', $pesan);
        } else {
            return redirect()->back()->with('error', 'Gagal membuat akun. Terjadi kesalahan pada database.');
        }
    }
    
    public function hapusAkun($id)
    {
        $userModel = new \App\Models\UserModel();
        // Gunakan parameter true untuk melakukan Hard Delete (permanen) sehingga username bisa dipakai lagi
        if ($userModel->delete($id, true)) {
            return redirect()->back()->with('success', 'Akun berhasil dihapus secara permanen. Karyawan kini bisa dibuatkan akun baru.');
        }
        return redirect()->back()->with('error', 'Gagal menghapus akun.');
    }

    public function editAkun($id)
    {
        $userModel = new \App\Models\UserModel();
        $akun = $userModel->select('users.*, karyawan.nama_lengkap, karyawan.nik')
                          ->join('karyawan', 'karyawan.id = users.karyawan_id', 'left')
                          ->find($id);

        if (!$akun) {
            return redirect()->to(base_url('direktur/karyawan/akun'))->with('error', 'Akun tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Akun Karyawan',
            'active' => 'karyawan',
            'akun' => $akun
        ];

        return view('direktur/karyawan/edit_akun', $data);
    }

    public function updateAkun($id)
    {
        $db = \Config\Database::connect();
        
        // Cek apakah akun ada (raw query agar tidak terganggu soft-delete behavior)
        $akunLama = $db->query("SELECT * FROM users WHERE id = ? AND deleted_at IS NULL LIMIT 1", [$id])->getRowArray();
        if (!$akunLama) {
            return redirect()->to(base_url('direktur/karyawan/akun'))->with('error', 'Akun tidak ditemukan.');
        }

        $username  = trim($this->request->getPost('username'));
        $email     = trim($this->request->getPost('email'));
        $role      = $this->request->getPost('role');
        $status    = $this->request->getPost('status');
        $pwdBaru   = $this->request->getPost('password');

        // Validasi dasar
        if (empty($username) || empty($email) || empty($role)) {
            return redirect()->back()->withInput()->with('error', 'Username, Email, dan Role wajib diisi.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('error', 'Format email tidak valid.');
        }

        // Cek duplikat username (exclude id ini sendiri)
        $dupUser = $db->query("SELECT id FROM users WHERE username = ? AND id != ? AND deleted_at IS NULL LIMIT 1", [$username, $id])->getRow();
        if ($dupUser) {
            return redirect()->back()->withInput()->with('error', 'Username sudah dipakai oleh akun lain.');
        }

        // Cek duplikat email (exclude id ini sendiri)
        $dupEmail = $db->query("SELECT id FROM users WHERE email = ? AND id != ? AND deleted_at IS NULL LIMIT 1", [$email, $id])->getRow();
        if ($dupEmail) {
            return redirect()->back()->withInput()->with('error', 'Email sudah dipakai oleh akun lain.');
        }

        // Bangun query update
        $now = date('Y-m-d H:i:s');
        if (!empty($pwdBaru)) {
            $hashedPwd = password_hash($pwdBaru, PASSWORD_DEFAULT);
            $db->query(
                "UPDATE users SET username=?, email=?, role=?, status=?, password=?, password_changed_at=?, updated_at=? WHERE id=?",
                [$username, $email, $role, $status, $hashedPwd, $now, $now, $id]
            );
            $pesan = 'Data akun berhasil diupdate. Password juga telah diubah.';
        } else {
            $db->query(
                "UPDATE users SET username=?, email=?, role=?, status=?, updated_at=? WHERE id=?",
                [$username, $email, $role, $status, $now, $id]
            );
            $pesan = 'Data akun berhasil diupdate.';
        }

        return redirect()->to(base_url('direktur/karyawan/akun'))->with('success', $pesan);
    }
    
    public function surat()
    {
        return view('direktur/karyawan/surat', ['title' => 'Surat Karyawan', 'active' => 'karyawan']);
    }
    
    // ===============================================
    // KELUHAN KARYAWAN (DIREKTUR)
    // ===============================================
    public function keluhan()
    {
        $db = \Config\Database::connect();
        
        $status = $this->request->getGet('status');
        $kategori = $this->request->getGet('kategori');
        $search = $this->request->getGet('search');

        $builder = $db->table('keluhan_karyawan')
            ->select('keluhan_karyawan.*, karyawan.nama_lengkap, karyawan.nik, karyawan.divisi, karyawan.jabatan')
            ->join('karyawan', 'karyawan.id = keluhan_karyawan.karyawan_id', 'left');

        if (!empty($status)) {
            $builder->where('keluhan_karyawan.status', $status);
        }
        if (!empty($kategori)) {
            $builder->where('keluhan_karyawan.kategori', $kategori);
        }
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('keluhan_karyawan.judul', $search)
                    ->orLike('keluhan_karyawan.deskripsi', $search)
                    ->orLike('karyawan.nama_lengkap', $search)
                    ->groupEnd();
        }

        $keluhan = $builder->orderBy('keluhan_karyawan.id', 'DESC')->get()->getResultArray();

        $data = [
            'title'    => 'Keluhan Karyawan',
            'active'   => 'karyawan',
            'keluhan'  => $keluhan,
            'status'   => $status,
            'kategori' => $kategori,
            'search'   => $search
        ];

        return view('direktur/karyawan/keluhan', $data);
    }

    public function detailKeluhan($id)
    {
        $db = \Config\Database::connect();
        $keluhan = $db->table('keluhan_karyawan')
            ->select('keluhan_karyawan.*, karyawan.nama_lengkap, karyawan.nik, karyawan.divisi, karyawan.jabatan, karyawan.foto, karyawan.email')
            ->join('karyawan', 'karyawan.id = keluhan_karyawan.karyawan_id', 'left')
            ->where('keluhan_karyawan.id', $id)
            ->get()->getRowArray();

        if (!$keluhan) {
            return redirect()->to(base_url('direktur/karyawan/keluhan'))->with('error', 'Keluhan tidak ditemukan.');
        }

        $data = [
            'title'   => 'Detail & Tanggapan Keluhan',
            'active'  => 'karyawan',
            'keluhan' => $keluhan
        ];

        return view('direktur/karyawan/keluhan_detail', $data);
    }

    public function tanggapiKeluhan($id)
    {
        $db = \Config\Database::connect();
        $tanggapan = $this->request->getPost('tanggapan');
        $status    = $this->request->getPost('status') ?: 'Diproses';

        $db->table('keluhan_karyawan')->where('id', $id)->update([
            'tanggapan'         => $tanggapan,
            'status'            => $status,
            'ditanggapi_oleh'   => session()->get('name') ?? 'Direktur Utama',
            'tanggal_tanggapan' => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('direktur/karyawan/keluhan/detail/' . $id))->with('success', 'Tanggapan keluhan berhasil disimpan.');
    }

    public function deleteKeluhan($id)
    {
        $db = \Config\Database::connect();
        $db->table('keluhan_karyawan')->where('id', $id)->delete();
        return redirect()->to(base_url('direktur/karyawan/keluhan'))->with('success', 'Data keluhan berhasil dihapus.');
    }

    // ===============================================
    // CUTI KARYAWAN & APPROVAL (DIREKTUR)
    // ===============================================
    public function cuti()
    {
        $db = \Config\Database::connect();

        $cutiList = $db->table('cuti')
            ->select('cuti.*, karyawan.nama_lengkap, karyawan.nik, karyawan.divisi, karyawan.jabatan')
            ->join('karyawan', 'karyawan.id = cuti.karyawan_id', 'left')
            ->where('cuti.deleted_at', null)
            ->orderBy('cuti.id', 'DESC')
            ->get()->getResultArray();

        $karyawanList = $this->karyawanModel->getAllKaryawan();
        $kuotaList = $db->table('kuota_cuti')->get()->getResultArray();
        $kuotaMap = [];
        foreach ($kuotaList as $kq) {
            $kuotaMap[$kq['karyawan_id']] = $kq;
        }

        $data = [
            'title'        => 'Pengajuan & Manajemen Cuti Karyawan',
            'active'       => 'karyawan',
            'cutiList'     => $cutiList,
            'karyawanList' => $karyawanList,
            'kuotaMap'     => $kuotaMap
        ];

        return view('direktur/karyawan/cuti', $data);
    }

    public function detailCuti($id)
    {
        $db = \Config\Database::connect();
        $cuti = $db->table('cuti')
            ->select('cuti.*, karyawan.nama_lengkap, karyawan.nik, karyawan.divisi, karyawan.jabatan, karyawan.email')
            ->join('karyawan', 'karyawan.id = cuti.karyawan_id', 'left')
            ->where('cuti.id', $id)
            ->get()->getRowArray();

        if (!$cuti) {
            return redirect()->to(base_url('direktur/karyawan/cuti'))->with('error', 'Pengajuan cuti tidak ditemukan.');
        }

        // Ambil kuota cuti karyawan
        $kuota = $db->table('kuota_cuti')->where('karyawan_id', $cuti['karyawan_id'])->get()->getRowArray();

        $data = [
            'title' => 'Detail Pengajuan Cuti Karyawan',
            'active' => 'karyawan',
            'c'     => $cuti,
            'kuota' => $kuota
        ];

        return view('direktur/karyawan/cuti_detail', $data);
    }

    private function ensureTablesExist()
    {
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();

        if (!$db->tableExists('kuota_cuti')) {
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
            if (!$db->fieldExists('sisa_kuota', 'kuota_cuti')) {
                $forge->addColumn('kuota_cuti', [
                    'sisa_kuota' => ['type' => 'INT', 'constraint' => 11, 'default' => 12]
                ]);
            }
            if (!$db->fieldExists('sisa', 'kuota_cuti')) {
                $forge->addColumn('kuota_cuti', [
                    'sisa' => ['type' => 'INT', 'constraint' => 11, 'default' => 12]
                ]);
            }
        }

        if ($db->tableExists('cuti')) {
            if (!$db->fieldExists('status', 'cuti')) {
                $forge->addColumn('cuti', ['status' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Menunggu']]);
            }
            if (!$db->fieldExists('disetujui_oleh', 'cuti')) {
                $forge->addColumn('cuti', ['disetujui_oleh' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true]]);
            }
            if (!$db->fieldExists('disetujui_at', 'cuti')) {
                $forge->addColumn('cuti', ['disetujui_at' => ['type' => 'DATETIME', 'null' => true]]);
            }
            if (!$db->fieldExists('alasan_penolakan', 'cuti')) {
                $forge->addColumn('cuti', ['alasan_penolakan' => ['type' => 'TEXT', 'null' => true]]);
            }
        }

        if ($db->tableExists('pengajuan_umum')) {
            if (!$db->fieldExists('bukti_foto', 'pengajuan_umum')) {
                $forge->addColumn('pengajuan_umum', ['bukti_foto' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true]]);
            }
        }
    }

    private function recalculateKuotaCuti($karyawanId)
    {
        if (!$karyawanId) return;
        $db = \Config\Database::connect();

        $approvedSum = $db->table('cuti')
            ->selectSum('lama_hari')
            ->where('karyawan_id', $karyawanId)
            ->where('LOWER(status)', 'disetujui')
            ->where('deleted_at', null)
            ->get()->getRowArray();

        $totalTerpakai = (int)($approvedSum['lama_hari'] ?? 0);

        $kuota = $db->table('kuota_cuti')->where('karyawan_id', $karyawanId)->get()->getRowArray();
        if ($kuota) {
            $kuotaTahunan = (int)($kuota['kuota_tahunan'] ?? 12);
            $sisaBaru = max(0, $kuotaTahunan - $totalTerpakai);

            $updateData = [
                'terpakai'   => $totalTerpakai,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            if ($db->fieldExists('sisa_kuota', 'kuota_cuti')) $updateData['sisa_kuota'] = $sisaBaru;
            if ($db->fieldExists('sisa', 'kuota_cuti')) $updateData['sisa'] = $sisaBaru;

            $db->table('kuota_cuti')->where('id', $kuota['id'])->update($updateData);
        } else {
            $kuotaAwal = 12;
            $sisaBaru = max(0, $kuotaAwal - $totalTerpakai);

            $insertData = [
                'karyawan_id'   => $karyawanId,
                'tahun'         => date('Y'),
                'kuota_tahunan' => $kuotaAwal,
                'terpakai'      => $totalTerpakai,
                'created_at'    => date('Y-m-d H:i:s')
            ];
            if ($db->fieldExists('sisa_kuota', 'kuota_cuti')) $insertData['sisa_kuota'] = $sisaBaru;
            if ($db->fieldExists('sisa', 'kuota_cuti')) $insertData['sisa'] = $sisaBaru;

            $db->table('kuota_cuti')->insert($insertData);
        }
    }

    public function approveCuti($id)
    {
        $this->ensureTablesExist();
        $db = \Config\Database::connect();
        $cuti = $db->table('cuti')->where('id', $id)->get()->getRowArray();

        if (!$cuti) {
            return redirect()->back()->with('error', 'Data cuti tidak ditemukan.');
        }

        $updateDataCuti = [
            'updated_at' => date('Y-m-d H:i:s')
        ];
        if ($db->fieldExists('status', 'cuti')) $updateDataCuti['status'] = 'Disetujui';
        if ($db->fieldExists('disetujui_oleh', 'cuti')) $updateDataCuti['disetujui_oleh'] = session()->get('name') ?? 'Direktur Utama';
        if ($db->fieldExists('disetujui_at', 'cuti')) $updateDataCuti['disetujui_at'] = date('Y-m-d H:i:s');

        $db->table('cuti')->where('id', $id)->update($updateDataCuti);

        // Potong sisa kuota cuti karyawan secara akurat
        if (!empty($cuti['karyawan_id'])) {
            $this->recalculateKuotaCuti($cuti['karyawan_id']);
        }

        return redirect()->to(base_url('direktur/karyawan/cuti'))->with('success', 'Pengajuan cuti berhasil disetujui dan jatah kuota cuti disinkronkan.');
    }

    public function rejectCuti($id)
    {
        $this->ensureTablesExist();
        $db = \Config\Database::connect();
        $cuti = $db->table('cuti')->where('id', $id)->get()->getRowArray();
        $alasan = $this->request->getPost('alasan_penolakan') ?: 'Alasan operasional & beban kerja proyek.';

        $updateDataCuti = [
            'updated_at' => date('Y-m-d H:i:s')
        ];
        if ($db->fieldExists('status', 'cuti')) $updateDataCuti['status'] = 'Ditolak';
        if ($db->fieldExists('alasan_penolakan', 'cuti')) $updateDataCuti['alasan_penolakan'] = $alasan;
        if ($db->fieldExists('disetujui_oleh', 'cuti')) $updateDataCuti['disetujui_oleh'] = session()->get('name') ?? 'Direktur Utama';
        if ($db->fieldExists('disetujui_at', 'cuti')) $updateDataCuti['disetujui_at'] = date('Y-m-d H:i:s');

        $db->table('cuti')->where('id', $id)->update($updateDataCuti);

        if (!empty($cuti['karyawan_id'])) {
            $this->recalculateKuotaCuti($cuti['karyawan_id']);
        }

        return redirect()->to(base_url('direktur/karyawan/cuti'))->with('success', 'Pengajuan cuti telah ditolak.');
    }

    public function updateKuotaCuti()
    {
        $this->ensureTablesExist();
        $db = \Config\Database::connect();
        $karyawanId = $this->request->getPost('karyawan_id');
        $kuotaTahunan = (int)$this->request->getPost('kuota_tahunan');

        if (!$karyawanId) {
            return redirect()->back()->with('error', 'Pilih karyawan terlebih dahulu.');
        }

        $existing = $db->table('kuota_cuti')->where('karyawan_id', $karyawanId)->get()->getRowArray();
        if ($existing) {
            $updateData = [
                'kuota_tahunan' => $kuotaTahunan,
                'updated_at'    => date('Y-m-d H:i:s')
            ];
            $db->table('kuota_cuti')->where('id', $existing['id'])->update($updateData);
        } else {
            $insertData = [
                'karyawan_id'   => $karyawanId,
                'tahun'         => date('Y'),
                'kuota_tahunan' => $kuotaTahunan,
                'terpakai'      => 0,
                'created_at'    => date('Y-m-d H:i:s')
            ];
            $db->table('kuota_cuti')->insert($insertData);
        }

        $this->recalculateKuotaCuti($karyawanId);

        return redirect()->to(base_url('direktur/karyawan/cuti'))->with('success', 'Jatah kuota cuti tahunan berhasil diset/diperbarui.');
    }

    public function deleteCuti($id)
    {
        $db = \Config\Database::connect();
        $cuti = $db->table('cuti')->where('id', $id)->get()->getRowArray();

        $db->table('cuti')->where('id', $id)->update(['deleted_at' => date('Y-m-d H:i:s')]);
        
        if (!empty($cuti['karyawan_id'])) {
            $this->recalculateKuotaCuti($cuti['karyawan_id']);
        }

        return redirect()->to(base_url('direktur/karyawan/cuti'))->with('success', 'Data cuti berhasil dihapus dan jatah kuota dikembalikan.');
    }

    public function editCuti($id)
    {
        $db = \Config\Database::connect();
        $cuti = $db->table('cuti')
            ->select('cuti.*, karyawan.nama_lengkap, karyawan.nik, karyawan.divisi, karyawan.jabatan')
            ->join('karyawan', 'karyawan.id = cuti.karyawan_id', 'left')
            ->where('cuti.id', $id)
            ->get()->getRowArray();

        if (!$cuti) {
            return redirect()->to(base_url('direktur/karyawan/cuti'))->with('error', 'Data cuti tidak ditemukan.');
        }

        $kuota = $db->table('kuota_cuti')->where('karyawan_id', $cuti['karyawan_id'])->get()->getRowArray();
        $sisaVal = $kuota ? (int)($kuota['sisa_kuota'] ?? $kuota['sisa'] ?? max(0, ($kuota['kuota_tahunan'] ?? 12) - ($kuota['terpakai'] ?? 0))) : 0;

        $data = [
            'title'     => 'Edit Permohonan Cuti Karyawan',
            'active'    => 'karyawan',
            'c'         => $cuti,
            'kuotaInfo' => $kuota,
            'sisaKuota' => $sisaVal
        ];

        return view('direktur/karyawan/cuti_edit', $data);
    }

    public function updateCuti($id)
    {
        $this->ensureTablesExist();
        $db = \Config\Database::connect();

        $cuti = $db->table('cuti')->where('id', $id)->get()->getRowArray();
        if (!$cuti) {
            return redirect()->to(base_url('direktur/karyawan/cuti'))->with('error', 'Data cuti tidak ditemukan.');
        }

        $tglMulai = $this->request->getPost('tanggal_mulai');
        $tglSelesai = $this->request->getPost('tanggal_selesai');
        $status = $this->request->getPost('status');
        $alasan = $this->request->getPost('alasan');

        if ($tglSelesai < $tglMulai) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui cuti! Tanggal selesai cuti tidak boleh lebih awal dari tanggal mulai.');
        }

        $d1 = new \DateTime($tglMulai);
        $d2 = new \DateTime($tglSelesai);
        $lamaHari = $d1->diff($d2)->days + 1;

        $updateData = [
            'tanggal_mulai'   => $tglMulai,
            'tanggal_selesai' => $tglSelesai,
            'lama_hari'       => $lamaHari,
            'alasan'          => $alasan,
            'updated_at'      => date('Y-m-d H:i:s')
        ];

        if ($db->fieldExists('status', 'cuti')) {
            $updateData['status'] = $status;
        }
        if ($status === 'Ditolak' && $db->fieldExists('alasan_penolakan', 'cuti')) {
            $updateData['alasan_penolakan'] = $this->request->getPost('alasan_penolakan');
        }
        if ($db->fieldExists('disetujui_oleh', 'cuti')) {
            $updateData['disetujui_oleh'] = session()->get('name') ?? 'Direktur Utama';
        }
        if ($db->fieldExists('disetujui_at', 'cuti')) {
            $updateData['disetujui_at'] = date('Y-m-d H:i:s');
        }

        $db->table('cuti')->where('id', $id)->update($updateData);

        // Rekalkulasi sinkronisasi kuota cuti karyawan
        if (!empty($cuti['karyawan_id'])) {
            $this->recalculateKuotaCuti($cuti['karyawan_id']);
        }

        return redirect()->to(base_url('direktur/karyawan/cuti'))->with('success', 'Data pengajuan cuti berhasil diperbarui dan jatah kuota cuti disinkronkan.');
    }

    public function pengajuan()
    {
        $this->ensureTablesExist();
        $db = \Config\Database::connect();

        $pengajuan = $db->table('pengajuan_umum')
            ->select('pengajuan_umum.*, karyawan.nama_lengkap, karyawan.nik, karyawan.divisi, karyawan.jabatan')
            ->join('karyawan', 'karyawan.id = pengajuan_umum.karyawan_id', 'left')
            ->orderBy('pengajuan_umum.id', 'DESC')
            ->get()->getResultArray();

        $data = [
            'title'     => 'Kelola Permohonan & Izin Karyawan (Non-Cuti)',
            'active'    => 'karyawan',
            'pengajuan' => $pengajuan
        ];

        return view('direktur/karyawan/pengajuan', $data);
    }

    public function detailPengajuan($id)
    {
        $this->ensureTablesExist();
        $db = \Config\Database::connect();

        $p = $db->table('pengajuan_umum')
            ->select('pengajuan_umum.*, karyawan.nama_lengkap, karyawan.nik, karyawan.divisi, karyawan.jabatan')
            ->join('karyawan', 'karyawan.id = pengajuan_umum.karyawan_id', 'left')
            ->where('pengajuan_umum.id', $id)
            ->get()->getRowArray();

        if (!$p) {
            return redirect()->to(base_url('direktur/karyawan/pengajuan'))->with('error', 'Data pengajuan permohonan/izin tidak ditemukan.');
        }

        $data = [
            'title'  => 'Detail Permohonan / Izin Karyawan',
            'active' => 'karyawan',
            'p'      => $p
        ];

        return view('direktur/karyawan/pengajuan_detail', $data);
    }

    public function editPengajuan($id)
    {
        $this->ensureTablesExist();
        $db = \Config\Database::connect();

        $p = $db->table('pengajuan_umum')
            ->select('pengajuan_umum.*, karyawan.nama_lengkap, karyawan.nik, karyawan.divisi, karyawan.jabatan')
            ->join('karyawan', 'karyawan.id = pengajuan_umum.karyawan_id', 'left')
            ->where('pengajuan_umum.id', $id)
            ->get()->getRowArray();

        if (!$p) {
            return redirect()->to(base_url('direktur/karyawan/pengajuan'))->with('error', 'Data pengajuan tidak ditemukan.');
        }

        $data = [
            'title'  => 'Edit Permohonan / Izin Karyawan',
            'active' => 'karyawan',
            'p'      => $p
        ];

        return view('direktur/karyawan/pengajuan_edit', $data);
    }

    public function updatePengajuan($id)
    {
        $this->ensureTablesExist();
        $db = \Config\Database::connect();

        $p = $db->table('pengajuan_umum')->where('id', $id)->get()->getRowArray();
        if (!$p) {
            return redirect()->to(base_url('direktur/karyawan/pengajuan'))->with('error', 'Data pengajuan tidak ditemukan.');
        }

        $tglMulai = $this->request->getPost('tanggal_mulai');
        $tglSelesai = $this->request->getPost('tanggal_selesai');

        if ($tglSelesai < $tglMulai) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui pengajuan! Tanggal selesai tidak boleh lebih awal dari tanggal mulai.');
        }

        $updateData = [
            'kategori_pengajuan' => $this->request->getPost('kategori_pengajuan') ?: $p['kategori_pengajuan'],
            'judul_pengajuan'    => $this->request->getPost('judul_pengajuan') ?: $p['judul_pengajuan'],
            'tanggal_mulai'      => $tglMulai,
            'tanggal_selesai'    => $tglSelesai,
            'keterangan'         => $this->request->getPost('keterangan'),
            'status'             => $this->request->getPost('status') ?: 'Menunggu',
            'disetujui_oleh'     => session()->get('name') ?? 'Direktur Utama',
            'updated_at'         => date('Y-m-d H:i:s')
        ];

        // Jika Direktur mengunggah foto bukti baru
        $fileBukti = $this->request->getFile('bukti_foto');
        if ($fileBukti && $fileBukti->isValid() && !$fileBukti->hasMoved()) {
            $uploadDir = FCPATH . 'uploads/pengajuan/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $newName = $fileBukti->getRandomName();
            $fileBukti->move($uploadDir, $newName);
            $updateData['bukti_foto'] = 'uploads/pengajuan/' . $newName;

            // Kompresi Gambar
            try {
                $imageService = \Config\Services::image();
                $imageService->withFile($uploadDir . $newName)
                             ->resize(1024, 1024, true, 'height')
                             ->save($uploadDir . $newName, 75);
            } catch (\Throwable $e) {
                log_message('error', 'Gagal mengompres foto pengajuan: ' . $e->getMessage());
            }
        }

        $db->table('pengajuan_umum')->where('id', $id)->update($updateData);

        return redirect()->to(base_url('direktur/karyawan/pengajuan'))->with('success', 'Data permohonan / izin karyawan berhasil diperbarui.');
    }

    public function approvePengajuan($id)
    {
        $this->ensureTablesExist();
        $db = \Config\Database::connect();

        $p = $db->table('pengajuan_umum')->where('id', $id)->get()->getRowArray();
        if (!$p) {
            return redirect()->back()->with('error', 'Data pengajuan tidak ditemukan.');
        }

        $updateData = [
            'status'         => 'Disetujui',
            'disetujui_oleh' => session()->get('name') ?? 'Direktur Utama',
            'updated_at'     => date('Y-m-d H:i:s')
        ];

        $db->table('pengajuan_umum')->where('id', $id)->update($updateData);

        return redirect()->to(base_url('direktur/karyawan/pengajuan'))->with('success', 'Permohonan / Izin karyawan berhasil disetujui.');
    }

    public function rejectPengajuan($id)
    {
        $this->ensureTablesExist();
        $db = \Config\Database::connect();

        $p = $db->table('pengajuan_umum')->where('id', $id)->get()->getRowArray();
        if (!$p) {
            return redirect()->back()->with('error', 'Data pengajuan tidak ditemukan.');
        }

        $updateData = [
            'status'         => 'Ditolak',
            'disetujui_oleh' => session()->get('name') ?? 'Direktur Utama',
            'updated_at'     => date('Y-m-d H:i:s')
        ];

        $db->table('pengajuan_umum')->where('id', $id)->update($updateData);

        return redirect()->to(base_url('direktur/karyawan/pengajuan'))->with('success', 'Permohonan / Izin karyawan telah ditolak.');
    }

    public function deletePengajuan($id)
    {
        $this->ensureTablesExist();
        $db = \Config\Database::connect();

        $db->table('pengajuan_umum')->where('id', $id)->delete();

        return redirect()->to(base_url('direktur/karyawan/pengajuan'))->with('success', 'Data pengajuan berhasil dihapus.');
    }

    public function absensi()
    {
        return redirect()->to(base_url('direktur/monitoring/absensi'));
    }
}
