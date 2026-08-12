<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PencarianBarangModel;
use App\Models\KaryawanModel;
use App\Models\UserModel;

class PencarianBarang extends BaseController
{
    protected $pencarianModel;
    protected $karyawanModel;
    protected $userModel;
    protected $db;

    public function __construct()
    {
        $this->pencarianModel = new PencarianBarangModel();
        $this->karyawanModel  = new KaryawanModel();
        $this->userModel      = new UserModel();
        $this->db             = \Config\Database::connect();
    }

    private function checkAccess()
    {
        $role = session()->get('role');
        if (!in_array($role, ['admin', 'superadmin', 'direktur'])) {
            return redirect()->to(base_url('login'))->with('error', 'Akses tidak diizinkan.');
        }
        return null;
    }

    private function ensurePencarianTableExist()
    {
        $forge = \Config\Database::forge();

        if (!$this->db->tableExists('pencarian_barang')) {
            $forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'judul' => ['type' => 'VARCHAR', 'constraint' => 255],
                'deskripsi' => ['type' => 'TEXT', 'null' => true],
                'tanggal_mulai' => ['type' => 'DATE', 'null' => true],
                'jam_mulai' => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => '08:00'],
                'batas_waktu' => ['type' => 'DATE', 'null' => true],
                'jam_deadline' => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => '17:00'],
                'tipe_pembelian' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'online_marketplace'],
                'nama_toko_marketplace' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'nominal_estimasi' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
                'karyawan_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
                'status' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'baru'],
                'hasil_pencarian' => ['type' => 'TEXT', 'null' => true],
                'lampiran_hasil' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'is_approved_keuangan' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'pembelian_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
                'dibuat_oleh' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $forge->addKey('id', true);
            $forge->createTable('pencarian_barang', true);
        } else {
            $fields = $this->db->getFieldNames('pencarian_barang');
            $newCols = [];

            if (!in_array('tanggal_mulai', $fields)) {
                $newCols['tanggal_mulai'] = ['type' => 'DATE', 'null' => true];
            }
            if (!in_array('jam_mulai', $fields)) {
                $newCols['jam_mulai'] = ['type' => 'VARCHAR', 'constraint' => 10, 'default' => '08:00'];
            }
            if (!in_array('jam_deadline', $fields)) {
                $newCols['jam_deadline'] = ['type' => 'VARCHAR', 'constraint' => 10, 'default' => '17:00'];
            }
            if (!in_array('tipe_pembelian', $fields)) {
                $newCols['tipe_pembelian'] = ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'online_marketplace'];
            }
            if (!in_array('nama_toko_marketplace', $fields)) {
                $newCols['nama_toko_marketplace'] = ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true];
            }
            if (!in_array('nominal_estimasi', $fields)) {
                $newCols['nominal_estimasi'] = ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0];
            }
            if (!in_array('is_approved_keuangan', $fields)) {
                $newCols['is_approved_keuangan'] = ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0];
            }
            if (!in_array('pembelian_id', $fields)) {
                $newCols['pembelian_id'] = ['type' => 'INT', 'constraint' => 11, 'null' => true];
            }

            if (!empty($newCols)) {
                $forge->addColumn('pencarian_barang', $newCols);
            }
        }
    }

    public function index()
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensurePencarianTableExist();

        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status');

        $filters = [];
        if (!empty($status)) $filters['status'] = $status;

        $penugasan = $this->pencarianModel->getPenugasanWithDetails($filters);

        $data = [
            'title'     => 'Penugasan Pencarian Barang & RAB',
            'active'    => 'pengadaan',
            'penugasan' => $penugasan,
            'search'    => $search,
            'status'    => $status,
            'user'      => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/pengadaan/pencarian_barang/index', $data);
    }

    public function detail($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensurePencarianTableExist();

        $penugasanList = $this->pencarianModel->getPenugasanWithDetails();
        $penugasan = null;
        foreach ($penugasanList as $item) {
            if ($item['id'] == $id) {
                $penugasan = $item;
                break;
            }
        }

        if (!$penugasan) {
            return redirect()->to(base_url('admin/pengadaan/pencarian-barang'))->with('error', 'Penugasan pencarian barang tidak ditemukan.');
        }

        $data = [
            'title'  => 'Detail Penugasan: ' . $penugasan['judul'],
            'active' => 'pengadaan',
            'p'      => $penugasan,
            'user'   => ['name' => session()->get('name') ?? 'Admin', 'role' => 'admin']
        ];

        return view('admin/pengadaan/pencarian_barang/detail', $data);
    }

    public function updateHasil($id)
    {
        if ($r = $this->checkAccess()) return $r;
        $this->ensurePencarianTableExist();

        $penugasan = $this->pencarianModel->find($id);
        if (!$penugasan) {
            return redirect()->to(base_url('admin/pengadaan/pencarian-barang'))->with('error', 'Penugasan tidak ditemukan.');
        }

        $tipePembelian = $this->request->getPost('tipe_pembelian') ?: $penugasan['tipe_pembelian'];
        $namaToko      = $this->request->getPost('nama_toko_marketplace');
        $nominalRaw    = $this->request->getPost('nominal_estimasi');
        $nominal       = !empty($nominalRaw) ? str_replace(['Rp', '.', ' '], '', $nominalRaw) : $penugasan['nominal_estimasi'];
        $hasilPencarian= $this->request->getPost('hasil_pencarian');
        $status        = $this->request->getPost('status') ?: 'selesai';

        $fileLampiran = $this->request->getFile('lampiran_hasil');
        $namaLampiran = $penugasan['lampiran_hasil'];

        if ($fileLampiran && $fileLampiran->isValid() && !$fileLampiran->hasMoved()) {
            $namaLampiran = $fileLampiran->getRandomName();
            $fileLampiran->move(FCPATH . 'uploads/pencarian_barang', $namaLampiran);
        }

        $this->pencarianModel->update($id, [
            'tipe_pembelian'        => $tipePembelian,
            'nama_toko_marketplace' => $namaToko,
            'nominal_estimasi'      => $nominal,
            'hasil_pencarian'       => $hasilPencarian,
            'lampiran_hasil'        => $namaLampiran,
            'status'                => $status,
            'updated_at'            => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('admin/pengadaan/pencarian-barang/detail/' . $id))->with('success', 'Hasil pencarian barang & harga RAB berhasil diperbarui dan dikirim ke Direktur.');
    }
}
