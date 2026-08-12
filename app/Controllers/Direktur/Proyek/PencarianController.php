<?php

namespace App\Controllers\Direktur\Proyek;

use App\Controllers\BaseController;
use App\Models\PencarianBarangModel;
use App\Models\KaryawanModel;
use App\Models\UserModel;

class PencarianController extends BaseController
{
    protected $pencarianModel;
    protected $karyawanModel;
    protected $userModel;

    public function __construct()
    {
        $this->pencarianModel = new PencarianBarangModel();
        $this->karyawanModel = new KaryawanModel();
        $this->userModel = new UserModel();
    }

    private function ensurePencarianTableExist()
    {
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();

        if (!$db->tableExists('pencarian_barang')) {
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
            $fields = $db->getFieldNames('pencarian_barang');
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
        $this->ensurePencarianTableExist();
        // Ambil daftar karyawan/user untuk dropdown penugasan
        $karyawanList = $this->karyawanModel->findAll();
        if (empty($karyawanList)) {
            $users = $this->userModel->where('status', 'active')->findAll();
            $karyawanList = array_map(function($u) {
                return [
                    'id' => $u['id'],
                    'nama_lengkap' => !empty($u['name']) ? $u['name'] : $u['username'],
                    'jabatan' => ucfirst($u['role'] ?? 'Karyawan')
                ];
            }, $users);
        }

        $data = [
            'title' => 'Penugasan Pencarian Barang & Harga (RAB)',
            'penugasan' => $this->pencarianModel->getPenugasanWithDetails(),
            'karyawan' => $karyawanList
        ];
        
        return view('direktur/proyek/pencarian_barang', $data);
    }

    public function tambah()
    {
        $this->ensurePencarianTableExist();
        $karyawanList = $this->karyawanModel->findAll();
        if (empty($karyawanList)) {
            $users = $this->userModel->where('status', 'active')->findAll();
            $karyawanList = array_map(function($u) {
                return [
                    'id' => $u['id'],
                    'nama_lengkap' => !empty($u['name']) ? $u['name'] : $u['username'],
                    'jabatan' => ucfirst($u['role'] ?? 'Karyawan')
                ];
            }, $users);
        }

        $data = [
            'title' => 'Buat Penugasan Pencarian Barang Baru',
            'karyawan' => $karyawanList
        ];

        return view('direktur/proyek/pencarian_barang_tambah', $data);
    }

    public function detail($id)
    {
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
            return redirect()->to(base_url('direktur/proyek/pencarian-barang'))->with('error', 'Penugasan tidak ditemukan.');
        }

        $data = [
            'title' => 'Detail Penugasan: ' . $penugasan['judul'],
            'p' => $penugasan
        ];

        return view('direktur/proyek/pencarian_barang_detail', $data);
    }

    public function edit($id)
    {
        $this->ensurePencarianTableExist();
        $penugasan = $this->pencarianModel->find($id);

        if (!$penugasan) {
            return redirect()->to(base_url('direktur/proyek/pencarian-barang'))->with('error', 'Penugasan tidak ditemukan.');
        }

        $karyawanList = $this->karyawanModel->findAll();
        if (empty($karyawanList)) {
            $users = $this->userModel->where('status', 'active')->findAll();
            $karyawanList = array_map(function($u) {
                return [
                    'id' => $u['id'],
                    'nama_lengkap' => !empty($u['name']) ? $u['name'] : $u['username'],
                    'jabatan' => ucfirst($u['role'] ?? 'Karyawan')
                ];
            }, $users);
        }

        $data = [
            'title' => 'Edit Penugasan: ' . $penugasan['judul'],
            'p' => $penugasan,
            'karyawan' => $karyawanList
        ];

        return view('direktur/proyek/pencarian_barang_edit', $data);
    }

    public function simpan()
    {
        $this->ensurePencarianTableExist();
        $rules = [
            'judul' => 'required',
            'deskripsi' => 'required',
            'karyawan_id' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $tanggalMulai = $this->request->getPost('tanggal_mulai') ?: date('Y-m-d');
        $jamMulai = $this->request->getPost('jam_mulai') ?: '08:00';
        
        $batasWaktu = $this->request->getPost('batas_waktu');
        if (empty($batasWaktu)) {
            $batasWaktu = date('Y-m-d', strtotime($tanggalMulai . ' +2 days'));
        }
        $jamDeadline = $this->request->getPost('jam_deadline') ?: '17:00';

        $rawNominal = $this->request->getPost('nominal_estimasi');
        $nominal = (float) preg_replace('/[^0-9]/', '', $rawNominal ?: '0');

        $this->pencarianModel->insert([
            'judul'                 => $this->request->getPost('judul'),
            'deskripsi'             => $this->request->getPost('deskripsi'),
            'tanggal_mulai'         => $tanggalMulai,
            'jam_mulai'             => $jamMulai,
            'batas_waktu'           => $batasWaktu,
            'jam_deadline'          => $jamDeadline,
            'tipe_pembelian'        => $this->request->getPost('tipe_pembelian') ?: 'online_marketplace',
            'nama_toko_marketplace' => $this->request->getPost('nama_toko_marketplace') ?: 'Tokopedia/Shopee',
            'nominal_estimasi'      => $nominal,
            'karyawan_id'           => $this->request->getPost('karyawan_id'),
            'status'                => 'baru',
            'dibuat_oleh'           => session()->get('id')
        ]);

        return redirect()->to(base_url('direktur/proyek/pencarian-barang'))->with('success', 'Penugasan pencarian barang baru berhasil dibuat.');
    }

    public function update()
    {
        $this->ensurePencarianTableExist();
        $id = $this->request->getPost('id');
        $rules = [
            'id' => 'required',
            'judul' => 'required',
            'deskripsi' => 'required',
            'karyawan_id' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $tanggalMulai = $this->request->getPost('tanggal_mulai') ?: date('Y-m-d');
        $jamMulai = $this->request->getPost('jam_mulai') ?: '08:00';
        $batasWaktu = $this->request->getPost('batas_waktu') ?: date('Y-m-d', strtotime('+2 days'));
        $jamDeadline = $this->request->getPost('jam_deadline') ?: '17:00';

        $rawNominal = $this->request->getPost('nominal_estimasi');
        $nominal = (float) preg_replace('/[^0-9]/', '', $rawNominal ?: '0');

        $status = $this->request->getPost('status') ?: 'baru';

        $this->pencarianModel->update($id, [
            'judul'                 => $this->request->getPost('judul'),
            'deskripsi'             => $this->request->getPost('deskripsi'),
            'tanggal_mulai'         => $tanggalMulai,
            'jam_mulai'             => $jamMulai,
            'batas_waktu'           => $batasWaktu,
            'jam_deadline'          => $jamDeadline,
            'tipe_pembelian'        => $this->request->getPost('tipe_pembelian') ?: 'online_marketplace',
            'nama_toko_marketplace' => $this->request->getPost('nama_toko_marketplace') ?: 'Tokopedia/Shopee',
            'nominal_estimasi'      => $nominal,
            'karyawan_id'           => $this->request->getPost('karyawan_id'),
            'status'                => $status,
            'hasil_pencarian'       => $this->request->getPost('hasil_pencarian')
        ]);

        return redirect()->to(base_url('direktur/proyek/pencarian-barang'))->with('success', 'Penugasan pencarian barang berhasil diperbarui.');
    }

    public function delete($id)
    {
        $penugasan = $this->pencarianModel->find($id);
        if ($penugasan) {
            $this->pencarianModel->delete($id);
            return redirect()->to(base_url('direktur/proyek/pencarian-barang'))->with('success', 'Penugasan pencarian barang berhasil dihapus.');
        }
        return redirect()->to(base_url('direktur/proyek/pencarian-barang'))->with('error', 'Penugasan tidak ditemukan.');
    }

    public function approve_keuangan($id)
    {
        $this->ensurePencarianTableExist();
        $item = $this->pencarianModel->find($id);

        if (!$item) {
            return redirect()->back()->with('error', 'Data penugasan tidak ditemukan.');
        }

        $pembelianModel = new \App\Models\Direktur\PembelianModel();
        
        // Generate PR Number
        $nomorPr = 'PR-' . date('Ym') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $nominal = floatval($item['nominal_estimasi'] ?? 0);
        $tipePembelian = (strtolower($item['tipe_pembelian'] ?? '') === 'offline') ? 'Offline' : 'Online';

        $pembelianId = $pembelianModel->insert([
            'nomor_pr'             => $nomorPr,
            'karyawan_id'          => $item['karyawan_id'],
            'tanggal_pengajuan'    => date('Y-m-d'),
            'tanggal_dibutuhkan'   => !empty($item['batas_waktu']) ? $item['batas_waktu'] : date('Y-m-d'),
            'prioritas'            => 'Normal',
            'alasan_pembelian'     => '[Disetujui dari Penugasan RAB] ' . $item['judul'] . "\n\nSpesifikasi: " . $item['deskripsi'] . "\n\nHasil Pencarian & Rincian Harga:\n" . ($item['hasil_pencarian'] ?: ('Estimasi Harga RAB: Rp ' . number_format($nominal, 0, ',', '.'))),
            'status_hrd'           => 'Disetujui',
            'status_direktur'      => 'Disetujui',
            'status_keseluruhan'    => 'Disetujui',
            'total_estimasi'       => $nominal,
            'supplier'             => $item['nama_toko_marketplace'] ?: 'Marketplace / Offline Store',
            'tipe_pembelian'       => $tipePembelian,
            'platform_pembelian'   => $item['nama_toko_marketplace'] ?: 'Tokopedia/Shopee/Offline',
            'created_by'           => session()->get('id') ?: 1
        ]);

        // Insert row into form_pembelian_item if table exists
        try {
            $db = \Config\Database::connect();
            if ($db->tableExists('form_pembelian_item')) {
                $db->table('form_pembelian_item')->insert([
                    'form_pembelian_id' => $pembelianId,
                    'nama_barang'       => $item['judul'],
                    'spesifikasi'       => $item['deskripsi'],
                    'jumlah'            => 1,
                    'satuan'            => 'Pcs',
                    'harga_estimasi'    => $nominal,
                    'total_estimasi'    => $nominal,
                    'keterangan'        => 'Item dari Penugasan RAB: ' . $item['judul']
                ]);
            }
        } catch (\Throwable $t) {}

        // Update status pencarian_barang
        $this->pencarianModel->update($id, [
            'status'               => 'selesai',
            'is_approved_keuangan' => 1,
            'pembelian_id'         => $pembelianId
        ]);

        return redirect()->to(base_url('direktur/keuangan/pembelian'))->with('success', 'RAB & Penugasan Pencarian Barang "' . $item['judul'] . '" BERHASIL DI-APPROVE & DITERUSKAN KE PEMBELIAN KEUANGAN (Nomor PR: ' . $nomorPr . ' | Rp ' . number_format($nominal,0,',','.') . ')!');
    }
}
