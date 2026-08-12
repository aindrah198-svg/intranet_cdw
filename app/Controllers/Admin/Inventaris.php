<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KaryawanModel;
use App\Models\Direktur\PembelianModel;

class Inventaris extends BaseController
{
    protected $db;
    protected $karyawanModel;
    protected $pembelianModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->karyawanModel = new KaryawanModel();
        $this->pembelianModel = new PembelianModel();
    }

    private function checkAccess()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        $role = strtolower(session()->get('role') ?? '');
        if ($role !== 'admin' && $role !== 'administrator') {
            return redirect()->to($this->getDashboardUrl())->with('error', 'Akses ditolak!');
        }
        return null;
    }

    private function getValidKaryawanId($preferredId = 0)
    {
        $id = intval($preferredId);
        if ($id > 0 && $this->db->table('karyawan')->where('id', $id)->countAllResults() > 0) {
            return $id;
        }

        $sessionKaryawanId = intval(session()->get('karyawan_id') ?: session()->get('user_id') ?: session()->get('id'));
        if ($sessionKaryawanId > 0 && $this->db->table('karyawan')->where('id', $sessionKaryawanId)->countAllResults() > 0) {
            return $sessionKaryawanId;
        }

        $first = $this->db->table('karyawan')->select('id')->orderBy('id', 'ASC')->get()->getRowArray();
        if ($first && !empty($first['id'])) {
            return intval($first['id']);
        }

        try {
            $this->db->table('karyawan')->insert([
                'nama_lengkap' => 'Administrator System',
                'jabatan'      => 'Admin Panel',
                'divisi'       => 'Administrasi & Umum',
                'created_at'   => date('Y-m-d H:i:s')
            ]);
            return intval($this->db->insertID());
        } catch (\Throwable $e) {
            return 1;
        }
    }

    // =========================================================================
    // 1. PENGAJUAN ATK
    // =========================================================================
    public function pengajuanAtk()
    {
        if ($r = $this->checkAccess()) return $r;

        try {
            $this->db->query("
                DELETE p1 FROM pengajuan_atk p1
                INNER JOIN pengajuan_atk p2 
                WHERE p1.id > p2.id 
                  AND p1.karyawan_id = p2.karyawan_id 
                  AND p1.nama_barang = p2.nama_barang 
                  AND p1.jumlah = p2.jumlah 
                  AND p1.status = p2.status
                  AND ABS(TIMESTAMPDIFF(SECOND, p1.created_at, p2.created_at)) <= 10
            ");
        } catch (\Throwable $e) {}

        $builder = $this->db->table('pengajuan_atk p')
            ->select('p.*, k.nama_lengkap, k.jabatan, k.divisi')
            ->join('karyawan k', 'k.id = p.karyawan_id', 'left')
            ->orderBy('p.id', 'DESC');

        $data = [
            'title'     => 'Pengajuan Pembelian ATK',
            'active'    => 'pengajuan-atk',
            'pengajuan' => $builder->get()->getResultArray()
        ];

        return view('admin/inventaris/pengajuan_atk', $data);
    }

    public function simpanAtk()
    {
        if ($r = $this->checkAccess()) return $r;

        $karyawanId = session()->get('karyawan_id') ?? session()->get('id') ?? 1;
        $namaBarang = trim($this->request->getPost('nama_barang'));
        $jumlah     = $this->request->getPost('jumlah') ?: 1;
        $satuan     = $this->request->getPost('satuan') ?: 'Pcs';
        $alasan     = trim($this->request->getPost('alasan'));

        $fiveSecAgo = date('Y-m-d H:i:s', time() - 5);
        $existing = $this->db->table('pengajuan_atk')
            ->where('karyawan_id', $karyawanId)
            ->where('nama_barang', $namaBarang)
            ->where('jumlah', $jumlah)
            ->where('created_at >=', $fiveSecAgo)
            ->get()->getRowArray();

        if ($existing) {
            return redirect()->to(base_url('admin/inventaris/pengajuan-atk'))
                ->with('success', 'Pengajuan ATK berhasil ditambahkan.');
        }

        $this->db->table('pengajuan_atk')->insert([
            'karyawan_id' => $karyawanId,
            'nama_barang' => $namaBarang,
            'jumlah'      => $jumlah,
            'satuan'      => $satuan,
            'alasan'      => $alasan,
            'status'      => 'menunggu',
            'created_at'  => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('admin/inventaris/pengajuan-atk'))->with('success', 'Pengajuan ATK berhasil ditambahkan.');
    }

    public function updateAtk()
    {
        if ($r = $this->checkAccess()) return $r;

        $id = $this->request->getPost('id');
        $data = [
            'nama_barang' => $this->request->getPost('nama_barang'),
            'jumlah'      => $this->request->getPost('jumlah') ?: 1,
            'satuan'      => $this->request->getPost('satuan') ?: 'Pcs',
            'alasan'      => $this->request->getPost('alasan'),
            'status'      => $this->request->getPost('status') ?: 'menunggu',
            'updated_at'  => date('Y-m-d H:i:s')
        ];

        $this->db->table('pengajuan_atk')->where('id', $id)->update($data);
        return redirect()->to(base_url('admin/inventaris/pengajuan-atk'))->with('success', 'Data pengajuan ATK berhasil diperbarui.');
    }

    public function reviewAtk($id)
    {
        if ($r = $this->checkAccess()) return $r;

        $pengajuan = $this->db->table('pengajuan_atk p')
            ->select('p.*, k.nama_lengkap, k.jabatan, k.divisi')
            ->join('karyawan k', 'k.id = p.karyawan_id', 'left')
            ->where('p.id', $id)
            ->get()->getRowArray();

        if (!$pengajuan) {
            return redirect()->to(base_url('admin/inventaris/pengajuan-atk'))->with('error', 'Data pengajuan ATK tidak ditemukan.');
        }

        $data = [
            'title'  => 'Review Pengajuan ATK',
            'active' => 'pengajuan-atk',
            'p'      => $pengajuan
        ];

        return view('admin/inventaris/review_atk', $data);
    }

    public function approveAtk()
    {
        if ($r = $this->checkAccess()) return $r;

        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');
        $komentar = $this->request->getPost('komentar');

        $this->db->table('pengajuan_atk')->where('id', $id)->update([
            'status' => $status,
            'komentar_direktur' => $komentar,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if ($status === 'disetujui' && $this->db->tableExists('form_pembelian')) {
            $p = $this->db->table('pengajuan_atk p')
                ->select('p.*, k.nama_lengkap, k.jabatan, k.divisi')
                ->join('karyawan k', 'k.id = p.karyawan_id', 'left')
                ->where('p.id', $id)
                ->get()->getRowArray();

            if ($p) {
                $nomorPr = 'PR-ATK-' . date('Ym') . sprintf('%03d', $id);
                $existing = $this->db->table('form_pembelian')->where('nomor_pr', $nomorPr)->get()->getRowArray();
                if (!$existing) {
                    $pemohonName = !empty($p['nama_lengkap']) ? $p['nama_lengkap'] : 'Admin';
                    $alasanFormatted = 'ATK: ' . $p['nama_barang'] . ' (' . $p['jumlah'] . ' ' . $p['satuan'] . ') - Pemohon: ' . $pemohonName . '. Alasan: ' . ($p['alasan'] ?: '-');
                    
                    $this->db->table('form_pembelian')->insert([
                        'nomor_pr'          => $nomorPr,
                        'karyawan_id'       => $this->getValidKaryawanId($p['karyawan_id'] ?? 0),
                        'tanggal_pengajuan' => date('Y-m-d'),
                        'tanggal_dibutuhkan'=> date('Y-m-d', strtotime('+3 days')),
                        'prioritas'         => 'Normal',
                        'alasan_pembelian'  => $alasanFormatted,
                        'total_estimasi'    => 0,
                        'tipe_pembelian'    => 'Online',
                        'platform_pembelian'=> 'Tokopedia / Shopee / Toko Offline',
                        'metode_pembayaran' => 'Dibayar Langsung Admin/GA',
                        'status_direktur'   => 'Disetujui',
                        'status_hrd'        => 'Disetujui',
                        'status_keseluruhan'=> 'Disetujui',
                        'status_pembayaran' => 'Belum Dibayar',
                        'status_penerimaan' => 'Belum Dibeli',
                        'created_at'        => date('Y-m-d H:i:s')
                    ]);
                    $prId = $this->db->insertID();

                    if ($this->db->tableExists('form_pembelian_item')) {
                        $itemFields = $this->db->getFieldNames('form_pembelian_item');
                        $itemRow = [
                            'form_pembelian_id' => $prId,
                            'nama_barang'       => $p['nama_barang'],
                            'spesifikasi'       => 'Pengajuan ATK - Pemohon: ' . $pemohonName . ' (' . ($p['jabatan'] ?: '-') . ')'
                        ];

                        if (in_array('jumlah', $itemFields)) {
                            $itemRow['jumlah'] = $p['jumlah'] ?: 1;
                        } elseif (in_array('qty', $itemFields)) {
                            $itemRow['qty'] = $p['jumlah'] ?: 1;
                        } elseif (in_array('quantity', $itemFields)) {
                            $itemRow['quantity'] = $p['jumlah'] ?: 1;
                        }

                        if (in_array('satuan', $itemFields)) {
                            $itemRow['satuan'] = $p['satuan'] ?: 'Pcs';
                        }

                        $this->db->table('form_pembelian_item')->insert($itemRow);
                    }
                }
            }
        }

        return redirect()->to(base_url('admin/inventaris/pengajuan-atk'))->with('success', 'Status pengajuan ATK berhasil diperbarui & diteruskan ke Form Pembelian PR.');
    }

    public function deleteAtk($id)
    {
        if ($r = $this->checkAccess()) return $r;

        if ($this->db->tableExists('form_pembelian')) {
            $formattedId = sprintf('%03d', $id);
            $prList = $this->db->table('form_pembelian')
                ->like('nomor_pr', 'PR-ATK-')
                ->where("RIGHT(nomor_pr, 3) = '$formattedId'", null, false)
                ->get()->getResultArray();

            foreach ($prList as $pr) {
                if ($this->db->tableExists('form_pembelian_item')) {
                    $this->db->table('form_pembelian_item')->where('form_pembelian_id', $pr['id'])->delete();
                }
                $this->db->table('form_pembelian')->where('id', $pr['id'])->delete();
            }
        }

        $this->db->table('pengajuan_atk')->where('id', $id)->delete();
        return redirect()->to(base_url('admin/inventaris/pengajuan-atk'))->with('success', 'Data pengajuan ATK & data pencatatan pembelian terkait berhasil dihapus.');
    }

    // =========================================================================
    // 2. MONITORING STOK ATK
    // =========================================================================
    public function stokAtk()
    {
        if ($r = $this->checkAccess()) return $r;

        if (!$this->db->tableExists('stok_atk')) {
            $forge = \Config\Database::forge();
            $forge->addField([
                'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'kode_barang' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'nama_barang' => ['type' => 'VARCHAR', 'constraint' => 255],
                'kategori'    => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'Umum'],
                'stok'        => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'satuan'      => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Pcs'],
                'lokasi'      => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'Gudang Utama'],
                'status_stok' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Aman'],
                'created_at'  => ['type' => 'DATETIME', 'null' => true],
                'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            ]);
            $forge->addKey('id', true);
            $forge->createTable('stok_atk', true);

            $defaultItems = [
                ['kode_barang' => 'ATK-001', 'nama_barang' => 'Kertas HVS A4 70gr (PaperOne)', 'kategori' => 'Kertas', 'stok' => 25, 'satuan' => 'Rim', 'lokasi' => 'Gudang ATK Lt. 2', 'status_stok' => 'Aman'],
                ['kode_barang' => 'ATK-002', 'nama_barang' => 'Spidol Whiteboard Hitam (Snowman)', 'kategori' => 'Alat Tulis', 'stok' => 12, 'satuan' => 'Pcs', 'lokasi' => 'Gudang ATK Lt. 2', 'status_stok' => 'Aman'],
                ['kode_barang' => 'ATK-003', 'nama_barang' => 'Pulpen Gel Hitam 0.5mm (Joyko)', 'kategori' => 'Alat Tulis', 'stok' => 4, 'satuan' => 'Box', 'lokasi' => 'Gudang ATK Lt. 2', 'status_stok' => 'Menipis'],
                ['kode_barang' => 'ATK-004', 'nama_barang' => 'Tinta Printer Epson Original Black (003)', 'kategori' => 'Tinta & Toner', 'stok' => 2, 'satuan' => 'Botol', 'lokasi' => 'Ruang IT & Server', 'status_stok' => 'Menipis'],
                ['kode_barang' => 'ATK-005', 'nama_barang' => 'Map Snelhecter Plastik Biru', 'kategori' => 'Arsip & Map', 'stok' => 40, 'satuan' => 'Pcs', 'lokasi' => 'Gudang ATK Lt. 2', 'status_stok' => 'Aman'],
                ['kode_barang' => 'ATK-006', 'nama_barang' => 'Isi Staples No. 10 (Max)', 'kategori' => 'Perlengkapan', 'stok' => 0, 'satuan' => 'Box', 'lokasi' => 'Gudang ATK Lt. 2', 'status_stok' => 'Habis'],
            ];

            foreach ($defaultItems as $item) {
                $item['created_at'] = date('Y-m-d H:i:s');
                $this->db->table('stok_atk')->insert($item);
            }
        }

        $stok = $this->db->table('stok_atk')->orderBy('id', 'ASC')->get()->getResultArray();

        $data = [
            'title'  => 'Monitoring Stok ATK',
            'active' => 'stok-atk',
            'stok'   => $stok
        ];

        return view('admin/inventaris/stok_atk', $data);
    }

    public function detailStokAtk($id)
    {
        $item = $this->db->table('stok_atk')->where('id', $id)->get()->getRowArray();
        if (!$item) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data stok ATK tidak ditemukan']);
        }
        return $this->response->setJSON(['status' => 'success', 'data' => $item]);
    }

    public function simpanStokAtk()
    {
        if ($r = $this->checkAccess()) return $r;

        $nextNum = $this->db->table('stok_atk')->countAllResults() + 1;
        $kode = $this->request->getPost('kode_barang') ?: ('ATK-' . sprintf('%03d', $nextNum));
        $stokQty = intval($this->request->getPost('stok') ?: 0);

        $statusStok = 'Aman';
        if ($stokQty <= 0) $statusStok = 'Habis';
        elseif ($stokQty <= 5) $statusStok = 'Menipis';

        $this->db->table('stok_atk')->insert([
            'kode_barang' => $kode,
            'nama_barang' => $this->request->getPost('nama_barang'),
            'kategori'    => $this->request->getPost('kategori') ?: 'Umum',
            'stok'        => $stokQty,
            'satuan'      => $this->request->getPost('satuan') ?: 'Pcs',
            'lokasi'      => $this->request->getPost('lokasi') ?: 'Gudang Utama',
            'status_stok' => $statusStok,
            'created_at'  => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('admin/inventaris/stok-atk'))->with('success', 'Item inventaris stok ATK berhasil ditambahkan.');
    }

    public function updateStokAtk()
    {
        if ($r = $this->checkAccess()) return $r;

        $id = $this->request->getPost('id');
        $stokQty = intval($this->request->getPost('stok') ?: 0);

        $statusStok = 'Aman';
        if ($stokQty <= 0) $statusStok = 'Habis';
        elseif ($stokQty <= 5) $statusStok = 'Menipis';

        $this->db->table('stok_atk')->where('id', $id)->update([
            'nama_barang' => $this->request->getPost('nama_barang'),
            'kategori'    => $this->request->getPost('kategori') ?: 'Umum',
            'stok'        => $stokQty,
            'satuan'      => $this->request->getPost('satuan') ?: 'Pcs',
            'lokasi'      => $this->request->getPost('lokasi') ?: 'Gudang Utama',
            'status_stok' => $statusStok,
            'updated_at'  => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('admin/inventaris/stok-atk'))->with('success', 'Data stok ATK berhasil diperbarui.');
    }

    public function deleteStokAtk($id)
    {
        if ($r = $this->checkAccess()) return $r;

        $this->db->table('stok_atk')->where('id', $id)->delete();
        return redirect()->to(base_url('admin/inventaris/stok-atk'))->with('success', 'Item stok ATK berhasil dihapus.');
    }

    // =========================================================================
    // 3. PENGADAAN ASET / INVENTARIS KANTOR
    // =========================================================================
    public function aset()
    {
        if ($r = $this->checkAccess()) return $r;

        if (!$this->db->tableExists('pengadaan_aset')) {
            $forge = \Config\Database::forge();
            $forge->addField([
                'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'kode_pengadaan'    => ['type' => 'VARCHAR', 'constraint' => 50],
                'nama_aset'         => ['type' => 'VARCHAR', 'constraint' => 255],
                'kategori'          => ['type' => 'VARCHAR', 'constraint' => 100],
                'estimasi_harga'    => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
                'jumlah'            => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
                'alasan_pengadaan'  => ['type' => 'TEXT', 'null' => true],
                'status'            => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'menunggu'],
                'komentar_direktur' => ['type' => 'TEXT', 'null' => true],
                'created_at'        => ['type' => 'DATETIME', 'null' => true],
                'updated_at'        => ['type' => 'DATETIME', 'null' => true],
            ]);
            $forge->addKey('id', true);
            $forge->createTable('pengadaan_aset', true);

            $defaultAset = [
                ['kode_pengadaan' => 'AST-202608001', 'nama_aset' => 'Laptop Asus ExpertBook B5 (Core i7 / 16GB)', 'kategori' => 'Elektronik & IT', 'estimasi_harga' => 18500000, 'jumlah' => 2, 'alasan_pengadaan' => 'Peremajaan laptop tim Engineering & Lead Dev', 'status' => 'menunggu'],
                ['kode_pengadaan' => 'AST-202608002', 'nama_aset' => 'Printer Multifungsi Epson EcoTank L6270', 'kategori' => 'Peralatan Kantor', 'estimasi_harga' => 5200000, 'jumlah' => 1, 'alasan_pengadaan' => 'Pengganti printer kantor lantai 2 yang rusak', 'status' => 'disetujui'],
                ['kode_pengadaan' => 'AST-202608003', 'nama_aset' => 'Kursi Kerja Ergonomis Mesh Premium', 'kategori' => 'Furniture', 'estimasi_harga' => 2100000, 'jumlah' => 5, 'alasan_pengadaan' => 'Fasilitas kursi ergonomis karyawan baru', 'status' => 'disetujui'],
                ['kode_pengadaan' => 'AST-202608004', 'nama_aset' => 'AC Inverter Panasonic 1.5 PK', 'kategori' => 'Elektronik & IT', 'estimasi_harga' => 6400000, 'jumlah' => 1, 'alasan_pengadaan' => 'Pendingin tambahan ruang meeting direksi', 'status' => 'menunggu'],
            ];

            foreach ($defaultAset as $item) {
                $item['created_at'] = date('Y-m-d H:i:s');
                $this->db->table('pengadaan_aset')->insert($item);
            }
        }

        $aset = $this->db->table('pengadaan_aset')->orderBy('id', 'DESC')->get()->getResultArray();

        $data = [
            'title'  => 'Pengadaan Aset Perusahaan',
            'active' => 'aset',
            'aset'   => $aset
        ];

        return view('admin/inventaris/aset', $data);
    }

    public function inventarisKantor()
    {
        return $this->aset();
    }

    public function reviewAset($id)
    {
        if ($r = $this->checkAccess()) return $r;

        $aset = $this->db->table('pengadaan_aset')->where('id', $id)->get()->getRowArray();
        if (!$aset) {
            return redirect()->to(base_url('admin/inventaris/aset'))->with('error', 'Data pengadaan aset tidak ditemukan.');
        }

        $data = [
            'title'  => 'Review Pengadaan Aset',
            'active' => 'aset',
            'a'      => $aset
        ];

        return view('admin/inventaris/review_aset', $data);
    }

    public function cetakAset()
    {
        if ($r = $this->checkAccess()) return $r;

        $aset = $this->db->table('pengadaan_aset')->orderBy('id', 'DESC')->get()->getResultArray();

        $data = [
            'aset'         => $aset,
            'tanggalCetak' => date('d F Y')
        ];

        return view('admin/inventaris/cetak_aset', $data);
    }

    public function simpanAset()
    {
        if ($r = $this->checkAccess()) return $r;

        $kode = 'AST-' . date('Ym') . sprintf('%03d', rand(1, 999));
        $estimasiHarga = preg_replace('/[^\d]/', '', $this->request->getPost('estimasi_harga') ?? '0');

        $this->db->table('pengadaan_aset')->insert([
            'kode_pengadaan'   => $kode,
            'nama_aset'        => $this->request->getPost('nama_aset'),
            'kategori'         => $this->request->getPost('kategori'),
            'estimasi_harga'   => floatval($estimasiHarga),
            'jumlah'           => $this->request->getPost('jumlah') ?: 1,
            'alasan_pengadaan' => $this->request->getPost('alasan_pengadaan'),
            'status'           => 'menunggu',
            'created_at'       => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('admin/inventaris/aset'))->with('success', 'Pengadaan aset berhasil diajukan.');
    }

    public function updateAset()
    {
        if ($r = $this->checkAccess()) return $r;

        $id = $this->request->getPost('id');
        $namaAset = $this->request->getPost('nama_aset');
        $kategori = $this->request->getPost('kategori');
        $estimasiHarga = preg_replace('/[^\d]/', '', $this->request->getPost('estimasi_harga') ?? '0');
        $jumlah = $this->request->getPost('jumlah') ?: 1;
        $alasan = $this->request->getPost('alasan_pengadaan');
        $status = $this->request->getPost('status') ?: 'menunggu';

        $this->db->table('pengadaan_aset')->where('id', $id)->update([
            'nama_aset'        => $namaAset,
            'kategori'         => $kategori,
            'estimasi_harga'   => floatval($estimasiHarga),
            'jumlah'           => intval($jumlah),
            'alasan_pengadaan' => $alasan,
            'status'           => $status,
            'updated_at'       => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('admin/inventaris/aset'))->with('success', 'Data pengadaan aset berhasil diperbarui.');
    }

    public function approveAset()
    {
        if ($r = $this->checkAccess()) return $r;

        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');
        $komentar = $this->request->getPost('komentar');

        $this->db->table('pengadaan_aset')->where('id', $id)->update([
            'status' => $status,
            'komentar_direktur' => $komentar,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if ($status === 'disetujui' && $this->db->tableExists('form_pembelian')) {
            $a = $this->db->table('pengadaan_aset')->where('id', $id)->get()->getRowArray();
            if ($a) {
                $nomorPr = 'PR-AST-' . date('Ym') . sprintf('%03d', $id);
                $existing = $this->db->table('form_pembelian')->where('nomor_pr', $nomorPr)->get()->getRowArray();
                if (!$existing) {
                    $total = floatval($a['estimasi_harga']) * intval($a['jumlah'] ?: 1);
                    $this->db->table('form_pembelian')->insert([
                        'nomor_pr'          => $nomorPr,
                        'karyawan_id'       => $this->getValidKaryawanId(),
                        'tanggal_pengajuan' => date('Y-m-d'),
                        'tanggal_dibutuhkan'=> date('Y-m-d', strtotime('+7 days')),
                        'prioritas'         => 'Urgent',
                        'alasan_pembelian'  => '[Pengadaan Aset Disetujui] ' . ($a['alasan_pengadaan'] ?: $a['nama_aset']),
                        'total_estimasi'    => $total,
                        'tipe_pembelian'    => 'Online',
                        'status_direktur'   => 'Disetujui',
                        'status_hrd'        => 'Disetujui',
                        'status_keseluruhan'=> 'Disetujui',
                        'status_pembayaran' => 'Belum Dibayar',
                        'status_penerimaan' => 'Belum Dibeli',
                        'created_at'        => date('Y-m-d H:i:s')
                    ]);
                    $prId = $this->db->insertID();

                    if ($this->db->tableExists('form_pembelian_item')) {
                        $itemFields = $this->db->getFieldNames('form_pembelian_item');
                        $itemRow = [
                            'form_pembelian_id' => $prId,
                            'nama_barang'       => $a['nama_aset'],
                            'spesifikasi'       => 'Kategori: ' . ($a['kategori'] ?: 'Elektronik')
                        ];

                        if (in_array('jumlah', $itemFields)) {
                            $itemRow['jumlah'] = $a['jumlah'] ?: 1;
                        } elseif (in_array('qty', $itemFields)) {
                            $itemRow['qty'] = $a['jumlah'] ?: 1;
                        } elseif (in_array('quantity', $itemFields)) {
                            $itemRow['quantity'] = $a['jumlah'] ?: 1;
                        }

                        if (in_array('harga_satuan', $itemFields)) {
                            $itemRow['harga_satuan'] = $a['estimasi_harga'];
                        } elseif (in_array('harga_estimasi', $itemFields)) {
                            $itemRow['harga_estimasi'] = $a['estimasi_harga'];
                        }

                        if (in_array('subtotal', $itemFields)) {
                            $itemRow['subtotal'] = $total;
                        } elseif (in_array('total_estimasi', $itemFields)) {
                            $itemRow['total_estimasi'] = $total;
                        }

                        $this->db->table('form_pembelian_item')->insert($itemRow);
                    }
                }
            }
        }

        return redirect()->to(base_url('admin/inventaris/aset'))->with('success', 'Status pengadaan aset berhasil diperbarui & diteruskan ke Form Pembelian PR.');
    }

    public function deleteAset($id)
    {
        if ($r = $this->checkAccess()) return $r;

        if ($this->db->tableExists('form_pembelian')) {
            $formattedId = sprintf('%03d', $id);
            $prList = $this->db->table('form_pembelian')
                ->like('nomor_pr', 'PR-AST-')
                ->where("RIGHT(nomor_pr, 3) = '$formattedId'", null, false)
                ->get()->getResultArray();

            foreach ($prList as $pr) {
                if ($this->db->tableExists('form_pembelian_item')) {
                    $this->db->table('form_pembelian_item')->where('form_pembelian_id', $pr['id'])->delete();
                }
                $this->db->table('form_pembelian')->where('id', $pr['id'])->delete();
            }
        }

        $this->db->table('pengadaan_aset')->where('id', $id)->delete();
        return redirect()->to(base_url('admin/inventaris/aset'))->with('success', 'Data pengadaan aset & data pencatatan pembelian terkait berhasil dihapus.');
    }

    // =========================================================================
    // 4. PENCATATAN & TRACKING PEMBELIAN (PR)
    // =========================================================================
    public function pembelian()
    {
        if ($r = $this->checkAccess()) return $r;

        $status = $this->request->getGet('status');
        $tipe = $this->request->getGet('tipe');
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $pembelian = [];
        try {
            $builder = $this->db->table('form_pembelian')
                ->select('
                    form_pembelian.*,
                    karyawan.nik,
                    karyawan.nama_lengkap,
                    karyawan.jabatan,
                    karyawan.departemen,
                    hrd.nama_lengkap as hrd_nama,
                    direktur.nama_lengkap as direktur_nama
                ')
                ->join('karyawan', 'karyawan.id = form_pembelian.karyawan_id', 'left')
                ->join('karyawan as hrd', 'hrd.id = form_pembelian.disetujui_hrd_oleh', 'left')
                ->join('karyawan as direktur', 'direktur.id = form_pembelian.disetujui_direktur_oleh', 'left')
                ->where('form_pembelian.deleted_at', null);

            if ($status) {
                if ($status === 'pending') {
                    $builder->where('form_pembelian.status_direktur', 'Menunggu');
                } elseif ($status === 'approved') {
                    $builder->where('form_pembelian.status_direktur', 'Disetujui');
                } elseif ($status === 'rejected') {
                    $builder->where('form_pembelian.status_direktur', 'Ditolak');
                } else {
                    $builder->where('form_pembelian.status_direktur', $status);
                }
            }

            if ($tipe) {
                $builder->where('form_pembelian.tipe_pembelian', $tipe);
            }

            if ($bulan && $tahun) {
                $startDate = $tahun . '-' . sprintf('%02d', $bulan) . '-01';
                $endDate = date('Y-m-t', strtotime($startDate));
                $builder->where('form_pembelian.tanggal_pengajuan >=', $startDate);
                $builder->where('form_pembelian.tanggal_pengajuan <=', $endDate . ' 23:59:59');
            }

            $pembelian = $builder->orderBy('form_pembelian.tanggal_pengajuan', 'DESC')->get()->getResultArray();

            foreach ($pembelian as &$pr) {
                $pr['items'] = $this->pembelianModel->getItems($pr['id']);
            }
        } catch (\Exception $e) {
            $pembelian = [];
        }

        $totalNominal = 0;
        $totalMenunggu = 0;
        $totalOnline = 0;
        $totalOffline = 0;

        foreach ($pembelian as $p) {
            $totalNominal += floatval($p['total_estimasi'] ?? 0);
            if (($p['status_direktur'] ?? '') === 'Menunggu') {
                $totalMenunggu++;
            }
            if (($p['tipe_pembelian'] ?? 'Online') === 'Online') {
                $totalOnline++;
            } else {
                $totalOffline++;
            }
        }

        $karyawanList = [];
        if ($this->db->tableExists('karyawan')) {
            $karyawanList = $this->db->table('karyawan')
                ->select('id, nik, nama_lengkap, jabatan, departemen')
                ->where('deleted_at', null)
                ->orderBy('nama_lengkap', 'ASC')
                ->get()->getResultArray();
        }

        $data = [
            'title'         => 'Pencatatan & Tracking Pembelian (PR)',
            'active'        => 'pembelian',
            'pembelian'     => $pembelian,
            'karyawanList'  => $karyawanList,
            'filterStatus'  => $status,
            'filterTipe'    => $tipe,
            'bulan'         => $bulan,
            'tahun'         => $tahun,
            'totalNominal'  => $totalNominal,
            'totalMenunggu' => $totalMenunggu,
            'totalOnline'   => $totalOnline,
            'totalOffline'  => $totalOffline
        ];

        return view('admin/inventaris/pembelian', $data);
    }

    public function tambahPembelian()
    {
        if ($r = $this->checkAccess()) return $r;

        $karyawanList = [];
        if ($this->db->tableExists('karyawan')) {
            $karyawanList = $this->db->table('karyawan')
                ->select('id, nik, nama_lengkap, jabatan, departemen')
                ->where('deleted_at', null)
                ->orderBy('nama_lengkap', 'ASC')
                ->get()->getResultArray();
        }

        $data = [
            'title'        => 'Tambah Pencatatan Pembelian (PR)',
            'active'       => 'pembelian',
            'karyawanList' => $karyawanList
        ];

        return view('admin/inventaris/pembelian_tambah', $data);
    }

    public function detailPembelian($id)
    {
        if ($r = $this->checkAccess()) return $r;

        $pr = $this->pembelianModel
            ->select('form_pembelian.*, karyawan.nama_lengkap, karyawan.nik, karyawan.jabatan, karyawan.departemen')
            ->join('karyawan', 'karyawan.id = form_pembelian.karyawan_id', 'left')
            ->where('form_pembelian.id', $id)
            ->first();

        if (!$pr) {
            return redirect()->to(base_url('admin/inventaris/pembelian'))->with('error', 'Data pembelian (PR) tidak ditemukan.');
        }

        $pr['items'] = $this->pembelianModel->getItems($id);

        $data = [
            'title'  => 'Detail Pencatatan Pembelian (PR)',
            'active' => 'pembelian',
            'pr'     => $pr
        ];

        return view('admin/inventaris/pembelian_detail', $data);
    }

    public function editPembelian($id)
    {
        if ($r = $this->checkAccess()) return $r;

        $pr = $this->pembelianModel->find($id);

        if (!$pr) {
            return redirect()->to(base_url('admin/inventaris/pembelian'))->with('error', 'Data pembelian (PR) tidak ditemukan.');
        }

        $pr['items'] = $this->pembelianModel->getItems($id);

        $karyawanList = [];
        if ($this->db->tableExists('karyawan')) {
            $karyawanList = $this->db->table('karyawan')
                ->select('id, nik, nama_lengkap, jabatan, departemen')
                ->where('deleted_at', null)
                ->orderBy('nama_lengkap', 'ASC')
                ->get()->getResultArray();
        }

        $data = [
            'title'        => 'Edit Pencatatan Pembelian (PR)',
            'active'       => 'pembelian',
            'pr'           => $pr,
            'karyawanList' => $karyawanList
        ];

        return view('admin/inventaris/pembelian_edit', $data);
    }

    public function cetakPembelian($id = null)
    {
        if ($r = $this->checkAccess()) return $r;

        if ($id) {
            $pr = $this->pembelianModel
                ->select('form_pembelian.*, karyawan.nama_lengkap, karyawan.nik, karyawan.jabatan, karyawan.departemen')
                ->join('karyawan', 'karyawan.id = form_pembelian.karyawan_id', 'left')
                ->where('form_pembelian.id', $id)
                ->first();

            if (!$pr) {
                return redirect()->to(base_url('admin/inventaris/pembelian'))->with('error', 'Data pembelian (PR) tidak ditemukan.');
            }

            $pr['items'] = $this->pembelianModel->getItems($id);

            $data = [
                'title' => 'Cetak Purchase Requisition (PR)',
                'pr'    => $pr
            ];

            return view('admin/inventaris/cetak_pembelian', $data);
        }

        $status = $this->request->getGet('status');
        $tipe = $this->request->getGet('tipe');
        $pembelian = [];
        try {
            $builder = $this->db->table('form_pembelian p')
                ->select('p.*, k.nama_lengkap, k.nik, k.jabatan, k.departemen')
                ->join('karyawan k', 'k.id = p.karyawan_id', 'left')
                ->where('p.deleted_at', null);

            if ($status) {
                if ($status === 'pending') {
                    $builder->where('p.status_direktur', 'Menunggu');
                } elseif ($status === 'approved') {
                    $builder->where('p.status_direktur', 'Disetujui');
                } elseif ($status === 'rejected') {
                    $builder->where('p.status_direktur', 'Ditolak');
                } else {
                    $builder->where('p.status_direktur', $status);
                }
            }

            if ($tipe) {
                $builder->where('p.tipe_pembelian', $tipe);
            }

            $pembelian = $builder->orderBy('p.tanggal_pengajuan', 'DESC')->get()->getResultArray();
        } catch (\Exception $e) {
            $pembelian = [];
        }

        $data = [
            'title'        => 'Rekapitulasi Pencatatan Pembelian',
            'pembelian'    => $pembelian,
            'filterStatus' => $status,
            'filterTipe'   => $tipe,
            'tanggalCetak' => date('d F Y')
        ];

        return view('direktur/keuangan/cetak_pembelian', $data);
    }

    public function simpanPembelian()
    {
        if ($r = $this->checkAccess()) return $r;

        $karyawanId = $this->request->getPost('karyawan_id') ?: $this->getValidKaryawanId();
        $nomorPr = 'PR-' . date('Ym') . sprintf('%03d', rand(1, 999));

        $totalEstimasi = 0;
        $itemsNama = $this->request->getPost('items_nama') ?: [];
        $itemsQty  = $this->request->getPost('items_qty') ?: [];
        $itemsHarga= $this->request->getPost('items_harga') ?: [];

        for ($i = 0; $i < count($itemsNama); $i++) {
            $qty = floatval($itemsQty[$i] ?? 1);
            $harga = floatval(str_replace(['Rp', '.', ' '], '', $itemsHarga[$i] ?? 0));
            $totalEstimasi += ($qty * $harga);
        }

        $prData = [
            'nomor_pr'          => $nomorPr,
            'karyawan_id'       => $karyawanId,
            'tanggal_pengajuan' => $this->request->getPost('tanggal_pengajuan') ?: date('Y-m-d'),
            'tanggal_dibutuhkan'=> $this->request->getPost('tanggal_dibutuhkan') ?: date('Y-m-d', strtotime('+3 days')),
            'prioritas'         => $this->request->getPost('prioritas') ?: 'Normal',
            'alasan_pembelian'  => $this->request->getPost('alasan_pembelian'),
            'total_estimasi'    => $totalEstimasi,
            'supplier'          => $this->request->getPost('supplier'),
            'tipe_pembelian'    => $this->request->getPost('tipe_pembelian') ?: 'Online',
            'platform_pembelian'=> $this->request->getPost('platform_pembelian'),
            'metode_pembayaran' => $this->request->getPost('metode_pembayaran'),
            'status_direktur'   => 'Disetujui',
            'status_hrd'        => 'Disetujui',
            'status_keseluruhan'=> 'Disetujui',
            'status_pembayaran' => $this->request->getPost('status_pembayaran') ?: 'Belum Dibayar',
            'status_penerimaan' => $this->request->getPost('status_penerimaan') ?: 'Belum Dibeli',
            'created_at'        => date('Y-m-d H:i:s')
        ];

        $this->db->table('form_pembelian')->insert($prData);
        $prId = $this->db->insertID();

        if ($this->db->tableExists('form_pembelian_item')) {
            $itemFields = $this->db->getFieldNames('form_pembelian_item');
            for ($i = 0; $i < count($itemsNama); $i++) {
                if (empty(trim($itemsNama[$i]))) continue;

                $qty = floatval($itemsQty[$i] ?? 1);
                $harga = floatval(str_replace(['Rp', '.', ' '], '', $itemsHarga[$i] ?? 0));
                $subtotal = $qty * $harga;

                $itemRow = [
                    'form_pembelian_id' => $prId,
                    'nama_barang'       => trim($itemsNama[$i])
                ];

                if (in_array('jumlah', $itemFields)) $itemRow['jumlah'] = $qty;
                elseif (in_array('qty', $itemFields)) $itemRow['qty'] = $qty;
                elseif (in_array('quantity', $itemFields)) $itemRow['quantity'] = $qty;

                if (in_array('harga_satuan', $itemFields)) $itemRow['harga_satuan'] = $harga;
                elseif (in_array('harga_estimasi', $itemFields)) $itemRow['harga_estimasi'] = $harga;

                if (in_array('subtotal', $itemFields)) $itemRow['subtotal'] = $subtotal;
                elseif (in_array('total_estimasi', $itemFields)) $itemRow['total_estimasi'] = $subtotal;

                $this->db->table('form_pembelian_item')->insert($itemRow);
            }
        }

        return redirect()->to(base_url('admin/inventaris/pembelian'))->with('success', 'Data pencatatan pembelian (PR) berhasil ditambahkan.');
    }

    public function updatePembelian()
    {
        if ($r = $this->checkAccess()) return $r;

        $id = $this->request->getPost('id');
        $pr = $this->pembelianModel->find($id);

        if (!$pr) {
            return redirect()->to(base_url('admin/inventaris/pembelian'))->with('error', 'Data pembelian (PR) tidak ditemukan.');
        }

        $karyawanId        = $this->request->getPost('karyawan_id') ?: $pr['karyawan_id'];
        $tipePembelian     = $this->request->getPost('tipe_pembelian') ?: 'Online';
        $platformPembelian = $this->request->getPost('platform_pembelian');
        $metodePembayaran  = $this->request->getPost('metode_pembayaran');
        $linkProduk        = $this->request->getPost('link_produk');
        $noResi            = $this->request->getPost('no_resi_transaksi');
        $tanggalPengajuan  = $this->request->getPost('tanggal_pengajuan') ?: date('Y-m-d');
        $tanggalDibutuhkan = $this->request->getPost('tanggal_dibutuhkan') ?: date('Y-m-d');
        $prioritas         = $this->request->getPost('prioritas') ?: 'Normal';
        $alasanPembelian   = $this->request->getPost('alasan_pembelian');
        $statusDirektur    = $this->request->getPost('status_direktur') ?: ($pr['status_direktur'] ?? 'Disetujui');
        $statusPembayaran  = $this->request->getPost('status_pembayaran') ?: 'Belum Dibayar';
        $statusPenerimaan  = $this->request->getPost('status_penerimaan') ?: 'Belum Dibeli';
        $statusKeseluruhan = $this->request->getPost('status_keseluruhan') ?: ($pr['status_keseluruhan'] ?? 'Dipesan');

        $namaItems        = $this->request->getPost('item_nama') ?: $this->request->getPost('items_nama') ?: [];
        $spesifikasiItems = $this->request->getPost('item_spesifikasi') ?: [];
        $qtyItems         = $this->request->getPost('item_jumlah') ?: $this->request->getPost('items_qty') ?: [];
        $satuanItems      = $this->request->getPost('item_satuan') ?: [];
        $hargaItems       = $this->request->getPost('item_harga') ?: $this->request->getPost('items_harga') ?: [];

        $totalEstimasi = 0;
        $itemsData = [];
        for ($i = 0; $i < count($namaItems); $i++) {
            if (empty(trim($namaItems[$i]))) continue;
            $qty = floatval($qtyItems[$i] ?? 1);
            $harga = floatval(preg_replace('/[^\d]/', '', $hargaItems[$i] ?? '0'));
            $subtotal = $qty * $harga;
            $totalEstimasi += $subtotal;

            $itemsData[] = [
                'nama_barang'    => trim($namaItems[$i]),
                'spesifikasi'    => trim($spesifikasiItems[$i] ?? ''),
                'jumlah'         => $qty,
                'satuan'         => trim($satuanItems[$i] ?? 'Pcs'),
                'harga_estimasi' => $harga,
                'total_estimasi' => $subtotal
            ];
        }

        $updateData = [
            'karyawan_id'        => $karyawanId,
            'tanggal_pengajuan'  => $tanggalPengajuan,
            'tanggal_dibutuhkan' => $tanggalDibutuhkan,
            'prioritas'          => $prioritas,
            'alasan_pembelian'   => $alasanPembelian,
            'tipe_pembelian'     => $tipePembelian,
            'platform_pembelian' => $platformPembelian,
            'metode_pembayaran'  => $metodePembayaran,
            'status_pembayaran'  => $statusPembayaran,
            'status_penerimaan'  => $statusPenerimaan,
            'link_produk'        => $linkProduk,
            'no_resi_transaksi'  => $noResi,
            'total_estimasi'     => $totalEstimasi,
            'status_direktur'    => $statusDirektur,
            'status_keseluruhan' => $statusKeseluruhan,
            'updated_at'         => date('Y-m-d H:i:s')
        ];

        // File uploads
        $fileBukti = $this->request->getFile('bukti_pembelian');
        if ($fileBukti && $fileBukti->isValid() && !$fileBukti->hasMoved()) {
            $newName = $fileBukti->getRandomName();
            $fileBukti->move(FCPATH . 'uploads/pembelian', $newName);
            $updateData['bukti_pembelian'] = 'uploads/pembelian/' . $newName;
        }

        $fileBayar = $this->request->getFile('bukti_pembayaran');
        if ($fileBayar && $fileBayar->isValid() && !$fileBayar->hasMoved()) {
            $newName = $fileBayar->getRandomName();
            $fileBayar->move(FCPATH . 'uploads/pembelian', $newName);
            $updateData['bukti_pembayaran'] = 'uploads/pembelian/' . $newName;
        }

        $fileBarang = $this->request->getFile('bukti_barang');
        if ($fileBarang && $fileBarang->isValid() && !$fileBarang->hasMoved()) {
            $newName = $fileBarang->getRandomName();
            $fileBarang->move(FCPATH . 'uploads/pembelian', $newName);
            $updateData['bukti_barang'] = 'uploads/pembelian/' . $newName;
        }

        $this->db->table('form_pembelian')->where('id', $id)->update($updateData);

        if (!empty($itemsData) && $this->db->tableExists('form_pembelian_item')) {
            $this->db->table('form_pembelian_item')->where('form_pembelian_id', $id)->delete();
            $existingItemFields = $this->db->getFieldNames('form_pembelian_item');
            $validBatch = [];

            foreach ($itemsData as $item) {
                $row = [
                    'form_pembelian_id' => $id,
                    'nama_barang'       => $item['nama_barang']
                ];

                if (in_array('jumlah', $existingItemFields)) $row['jumlah'] = $item['jumlah'];
                elseif (in_array('qty', $existingItemFields)) $row['qty'] = $item['jumlah'];
                elseif (in_array('quantity', $existingItemFields)) $row['quantity'] = $item['jumlah'];

                if (in_array('satuan', $existingItemFields)) $row['satuan'] = $item['satuan'];
                if (in_array('spesifikasi', $existingItemFields)) $row['spesifikasi'] = $item['spesifikasi'];

                if (in_array('harga_estimasi', $existingItemFields)) $row['harga_estimasi'] = $item['harga_estimasi'];
                elseif (in_array('harga_satuan', $existingItemFields)) $row['harga_satuan'] = $item['harga_estimasi'];

                if (in_array('total_estimasi', $existingItemFields)) $row['total_estimasi'] = $item['total_estimasi'];
                elseif (in_array('subtotal', $existingItemFields)) $row['subtotal'] = $item['total_estimasi'];

                $validBatch[] = $row;
            }

            if (!empty($validBatch)) {
                $this->db->table('form_pembelian_item')->insertBatch($validBatch);
            }
        }

        return redirect()->to(base_url('admin/inventaris/pembelian'))->with('success', 'Data pencatatan pembelian (PR) berhasil diperbarui.');
    }

    public function deletePembelian($id)
    {
        if ($r = $this->checkAccess()) return $r;

        if ($this->db->tableExists('form_pembelian_item')) {
            $this->db->table('form_pembelian_item')->where('form_pembelian_id', $id)->delete();
        }
        $this->db->table('form_pembelian')->where('id', $id)->delete();

        return redirect()->to(base_url('admin/inventaris/pembelian'))->with('success', 'Data pencatatan pembelian (PR) berhasil dihapus.');
    }

    public function exportExcelPembelian()
    {
        if ($r = $this->checkAccess()) return $r;

        $status = $this->request->getGet('status');
        $pembelian = [];
        try {
            $builder = $this->db->table('form_pembelian p')
                ->select('p.*, k.nama_lengkap, k.nik, k.jabatan, k.departemen')
                ->join('karyawan k', 'k.id = p.karyawan_id', 'left')
                ->where('p.deleted_at', null);

            if ($status) {
                if ($status === 'pending') {
                    $builder->where('p.status_direktur', 'Menunggu');
                } elseif ($status === 'approved') {
                    $builder->where('p.status_direktur', 'Disetujui');
                } elseif ($status === 'rejected') {
                    $builder->where('p.status_direktur', 'Ditolak');
                } else {
                    $builder->where('p.status_direktur', $status);
                }
            }

            $pembelian = $builder->orderBy('p.tanggal_pengajuan', 'DESC')->get()->getResultArray();
        } catch (\Exception $e) {
            $pembelian = [];
        }

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Rekap_Pencatatan_Pembelian.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "<table border='1'>";
        echo "<tr>
                <th>No PR</th>
                <th>Pemohon</th>
                <th>Tipe</th>
                <th>Platform / Toko</th>
                <th>Metode Bayar</th>
                <th>Tanggal Pengajuan</th>
                <th>Alasan Pembelian</th>
                <th>Total Estimasi</th>
                <th>Status Direktur</th>
              </tr>";

        foreach ($pembelian as $p) {
            echo "<tr>";
            echo "<td>" . esc($p['nomor_pr']) . "</td>";
            echo "<td>" . esc($p['nama_lengkap'] ?? 'Admin Panel') . "</td>";
            echo "<td>" . esc($p['tipe_pembelian'] ?? 'Online') . "</td>";
            echo "<td>" . esc($p['platform_pembelian'] ?? '-') . "</td>";
            echo "<td>" . esc($p['metode_pembayaran'] ?? '-') . "</td>";
            echo "<td>" . esc($p['tanggal_pengajuan']) . "</td>";
            echo "<td>" . esc($p['alasan_pembelian']) . "</td>";
            echo "<td>Rp " . number_format($p['total_estimasi'] ?? 0, 0, ',', '.') . "</td>";
            echo "<td>" . esc($p['status_direktur'] ?? 'Menunggu') . "</td>";
            echo "</tr>";
        }

        echo "</table>";
        exit;
    }

    // =========================================================================
    // 5. KERUSAKAN ALAT
    // =========================================================================
    private function ensureKerusakanTable()
    {
        if (!$this->db->tableExists('laporan_kerusakan')) {
            $forge = \Config\Database::forge();
            $forge->addField([
                'id'                  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'kode_laporan'        => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'nama_alat'           => ['type' => 'VARCHAR', 'constraint' => 255],
                'lokasi_alat'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'pelapor_id'          => ['type' => 'INT', 'constraint' => 11, 'null' => true],
                'deskripsi_kerusakan' => ['type' => 'TEXT', 'null' => true],
                'tingkat_kerusakan'   => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'sedang'],
                'teknisi_pengurus'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'lokasi_perbaikan'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'petugas_pembawa'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'catatan_perbaikan'   => ['type' => 'TEXT', 'null' => true],
                'status_tindakan'     => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'dilaporkan'],
                'created_at'          => ['type' => 'DATETIME', 'null' => true],
                'updated_at'          => ['type' => 'DATETIME', 'null' => true],
            ]);
            $forge->addKey('id', true);
            $forge->createTable('laporan_kerusakan', true);

            $defaultData = [
                [
                    'kode_laporan'        => 'KRK-202608001',
                    'nama_alat'           => 'Printer Epson EcoTank L3210 (HRD)',
                    'lokasi_alat'         => 'Ruang HRD & GA Lt. 2',
                    'deskripsi_kerusakan' => 'Hasil cetak garis-garis dan tinta merah tidak keluar walau sudah head cleaning',
                    'tingkat_kerusakan'   => 'sedang',
                    'teknisi_pengurus'    => 'Rian (Teknisi IT Staff)',
                    'lokasi_perbaikan'    => 'Service Center Resmi Epson (Kelapa Gading)',
                    'petugas_pembawa'     => 'Doni (Driver Operational)',
                    'catatan_perbaikan'   => 'Unit sudah dibawa ke service center resmi, estimasi selesai 3 hari',
                    'status_tindakan'     => 'dalam_perbaikan',
                    'created_at'          => date('Y-m-d H:i:s')
                ],
                [
                    'kode_laporan'        => 'KRK-202608002',
                    'nama_alat'           => 'Proyektor BenQ MX560 Meeting Room',
                    'lokasi_alat'         => 'Ruang Meeting Utama Lt. 1',
                    'deskripsi_kerusakan' => 'Lampu proyektor redup dan suka mati sendiri setelah 10 menit digunakan',
                    'tingkat_kerusakan'   => 'berat',
                    'teknisi_pengurus'    => 'Budi (Supervisor IT)',
                    'lokasi_perbaikan'    => 'Ruang Workshop IT Lt. 1',
                    'petugas_pembawa'     => 'Budi (Self Deliver)',
                    'catatan_perbaikan'   => 'Menunggu penggantian suku cadang bohlam lampu proyektor baru',
                    'status_tindakan'     => 'dilaporkan',
                    'created_at'          => date('Y-m-d H:i:s')
                ]
            ];
            foreach ($defaultData as $item) {
                $this->db->table('laporan_kerusakan')->insert($item);
            }
        }
    }

    public function kerusakan()
    {
        if ($r = $this->checkAccess()) return $r;

        $this->ensureKerusakanTable();

        $builder = $this->db->table('laporan_kerusakan l')
            ->select('l.*, k.nama_lengkap as pelapor')
            ->join('karyawan k', 'k.id = l.pelapor_id', 'left')
            ->orderBy('l.id', 'DESC');

        $data = [
            'title'     => 'Laporan Kerusakan Alat',
            'active'    => 'kerusakan',
            'kerusakan' => $builder->get()->getResultArray(),
            'karyawan'  => $this->karyawanModel->findAll()
        ];

        return view('admin/inventaris/kerusakan', $data);
    }

    public function tambahKerusakan()
    {
        if ($r = $this->checkAccess()) return $r;

        $data = [
            'title'    => 'Laporkan Kerusakan Alat Baru',
            'active'   => 'kerusakan',
            'karyawan' => $this->karyawanModel->findAll()
        ];

        return view('admin/inventaris/kerusakan_tambah', $data);
    }

    public function editKerusakan($id)
    {
        if ($r = $this->checkAccess()) return $r;

        $k = $this->db->table('laporan_kerusakan l')
            ->select('l.*, k.nama_lengkap as pelapor')
            ->join('karyawan k', 'k.id = l.pelapor_id', 'left')
            ->where('l.id', $id)
            ->get()->getRowArray();

        if (!$k) {
            return redirect()->to(base_url('admin/inventaris/kerusakan'))->with('error', 'Laporan kerusakan alat tidak ditemukan.');
        }

        $data = [
            'title'    => 'Edit Laporan Kerusakan Alat',
            'active'   => 'kerusakan',
            'k'        => $k,
            'karyawan' => $this->karyawanModel->findAll()
        ];

        return view('admin/inventaris/kerusakan_edit', $data);
    }

    public function detailKerusakan($id)
    {
        if ($r = $this->checkAccess()) return $r;

        $k = $this->db->table('laporan_kerusakan l')
            ->select('l.*, k.nama_lengkap as pelapor, k.jabatan as pelapor_jabatan, k.divisi as pelapor_divisi')
            ->join('karyawan k', 'k.id = l.pelapor_id', 'left')
            ->where('l.id', $id)
            ->get()->getRowArray();

        if (!$k) {
            return redirect()->to(base_url('admin/inventaris/kerusakan'))->with('error', 'Laporan kerusakan alat tidak ditemukan.');
        }

        $data = [
            'title'  => 'Detail Laporan Kerusakan Alat',
            'active' => 'kerusakan',
            'k'      => $k
        ];

        return view('admin/inventaris/kerusakan_detail', $data);
    }

    public function simpanKerusakan()
    {
        if ($r = $this->checkAccess()) return $r;

        $kode = 'KRK-' . date('Ym') . sprintf('%03d', rand(1, 999));
        $pelaporId = session()->get('karyawan_id') ?? session()->get('id');
        if (!$pelaporId || !$this->db->table('karyawan')->where('id', $pelaporId)->countAllResults()) {
            $firstK = $this->db->table('karyawan')->select('id')->get()->getRowArray();
            $pelaporId = $firstK['id'] ?? 1;
        }

        $this->db->table('laporan_kerusakan')->insert([
            'kode_laporan'        => $kode,
            'nama_alat'           => $this->request->getPost('nama_alat'),
            'lokasi_alat'         => $this->request->getPost('lokasi_alat'),
            'pelapor_id'          => $pelaporId,
            'deskripsi_kerusakan' => $this->request->getPost('deskripsi_kerusakan'),
            'tingkat_kerusakan'   => $this->request->getPost('tingkat_kerusakan') ?: 'sedang',
            'teknisi_pengurus'    => $this->request->getPost('teknisi_pengurus') ?: 'Teknisi IT / GA',
            'lokasi_perbaikan'    => $this->request->getPost('lokasi_perbaikan') ?: 'Workshop IT',
            'petugas_pembawa'     => $this->request->getPost('petugas_pembawa') ?: 'Petugas GA',
            'catatan_perbaikan'   => $this->request->getPost('catatan_perbaikan'),
            'status_tindakan'     => $this->request->getPost('status_tindakan') ?: 'dilaporkan',
            'created_at'          => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('admin/inventaris/kerusakan'))->with('success', 'Laporan kerusakan alat berhasil ditambahkan.');
    }

    public function updateKerusakan()
    {
        if ($r = $this->checkAccess()) return $r;

        $id = $this->request->getPost('id');

        $this->db->table('laporan_kerusakan')->where('id', $id)->update([
            'nama_alat'           => $this->request->getPost('nama_alat'),
            'lokasi_alat'         => $this->request->getPost('lokasi_alat'),
            'deskripsi_kerusakan' => $this->request->getPost('deskripsi_kerusakan'),
            'tingkat_kerusakan'   => $this->request->getPost('tingkat_kerusakan'),
            'teknisi_pengurus'    => $this->request->getPost('teknisi_pengurus'),
            'lokasi_perbaikan'    => $this->request->getPost('lokasi_perbaikan'),
            'petugas_pembawa'     => $this->request->getPost('petugas_pembawa'),
            'catatan_perbaikan'   => $this->request->getPost('catatan_perbaikan'),
            'status_tindakan'     => $this->request->getPost('status_tindakan'),
            'updated_at'          => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('admin/inventaris/kerusakan'))->with('success', 'Data laporan kerusakan alat berhasil diperbarui.');
    }

    public function deleteKerusakan($id)
    {
        if ($r = $this->checkAccess()) return $r;

        $this->db->table('laporan_kerusakan')->where('id', $id)->delete();
        return redirect()->to(base_url('admin/inventaris/kerusakan'))->with('success', 'Laporan kerusakan alat berhasil dihapus.');
    }

    // =========================================================================
    // 6. MONITORING GUDANG & MATERIAL
    // =========================================================================
    private function ensureGudangColumns()
    {
        if (!$this->db->tableExists('monitoring_gudang')) {
            $forge = \Config\Database::forge();
            $forge->addField([
                'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'kode_barang'   => ['type' => 'VARCHAR', 'constraint' => 50],
                'nama_barang'   => ['type' => 'VARCHAR', 'constraint' => 255],
                'kategori'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'stok_tersedia' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'satuan'        => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Pcs'],
                'lokasi_gudang' => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'Gudang Blok K'],
                'lokasi_rak'    => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'foto_barang'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'status'        => ['type' => 'ENUM', 'constraint' => ['tersedia', 'indent', 'kosong'], 'default' => 'tersedia'],
                'created_at'    => ['type' => 'DATETIME', 'null' => true],
                'updated_at'    => ['type' => 'DATETIME', 'null' => true]
            ]);
            $forge->addKey('id', true);
            $forge->createTable('monitoring_gudang', true);

            $defaultData = [
                [
                    'kode_barang'   => 'MTR-202608-001',
                    'nama_barang'   => 'Semen Tiga Roda 50kg',
                    'kategori'      => 'Material Konstruksi',
                    'stok_tersedia' => 120,
                    'satuan'        => 'Sak',
                    'lokasi_gudang' => 'Gudang Blok K',
                    'lokasi_rak'    => 'Sektor A - Rak 01',
                    'status'        => 'tersedia',
                    'created_at'    => date('Y-m-d H:i:s')
                ],
                [
                    'kode_barang'   => 'MTR-202608-002',
                    'nama_barang'   => 'Besi Beton 12mm Polos',
                    'kategori'      => 'Material Konstruksi',
                    'stok_tersedia' => 45,
                    'satuan'        => 'Batang',
                    'lokasi_gudang' => 'Gudang Blok K',
                    'lokasi_rak'    => 'Sektor A - Rak 04',
                    'status'        => 'tersedia',
                    'created_at'    => date('Y-m-d H:i:s')
                ],
                [
                    'kode_barang'   => 'MTR-202608-003',
                    'nama_barang'   => 'Kabel NYM 3x2.5mm (100m)',
                    'kategori'      => 'Kelistrikan',
                    'stok_tersedia' => 15,
                    'satuan'        => 'Roll',
                    'lokasi_gudang' => 'Kantor',
                    'lokasi_rak'    => 'Sektor B - Rak 02',
                    'status'        => 'tersedia',
                    'created_at'    => date('Y-m-d H:i:s')
                ],
                [
                    'kode_barang'   => 'MTR-202608-004',
                    'nama_barang'   => 'Pipa PVC Wavin 4 Inch',
                    'kategori'      => 'Plumbing',
                    'stok_tersedia' => 8,
                    'satuan'        => 'Batang',
                    'lokasi_gudang' => 'Gudang Blok I',
                    'lokasi_rak'    => 'Sektor C - Rak 01',
                    'status'        => 'indent',
                    'created_at'    => date('Y-m-d H:i:s')
                ],
                [
                    'kode_barang'   => 'MTR-202608-005',
                    'nama_barang'   => 'Cat Tembok Dulux White 20L',
                    'kategori'      => 'Finishing',
                    'stok_tersedia' => 0,
                    'satuan'        => 'Pail',
                    'lokasi_gudang' => 'Gudang Blok I',
                    'lokasi_rak'    => 'Sektor D - Rak 02',
                    'status'        => 'kosong',
                    'created_at'    => date('Y-m-d H:i:s')
                ]
            ];
            foreach ($defaultData as $item) {
                $this->db->table('monitoring_gudang')->insert($item);
            }
        }
    }

    public function gudang()
    {
        if ($r = $this->checkAccess()) return $r;

        $this->ensureGudangColumns();

        $gudang = $this->db->table('monitoring_gudang')->orderBy('id', 'DESC')->get()->getResultArray();

        $data = [
            'title'  => 'Monitoring Gudang & Stok Material',
            'active' => 'gudang',
            'gudang' => $gudang
        ];

        return view('admin/inventaris/gudang', $data);
    }

    public function tambahGudang()
    {
        if ($r = $this->checkAccess()) return $r;

        $this->ensureGudangColumns();

        $count = $this->db->table('monitoring_gudang')->countAllResults() + 1;
        $autoKode = 'MTR-' . date('Ym') . '-' . sprintf('%03d', $count);

        $kategoriList = $this->db->table('monitoring_gudang')
            ->select('kategori')
            ->distinct()
            ->where('kategori IS NOT NULL AND kategori != ""')
            ->get()->getResultArray();

        $categories = array_values(array_unique(array_merge(
            ['Material Konstruksi', 'Kelistrikan', 'Plumbing', 'Finishing', 'Sparepart Mesin', 'Lainnya'],
            array_column($kategoriList, 'kategori')
        )));

        $data = [
            'title'      => 'Tambah Stok Barang Gudang Baru',
            'active'     => 'gudang',
            'autoKode'   => $autoKode,
            'categories' => $categories
        ];

        return view('admin/inventaris/gudang_tambah', $data);
    }

    public function editGudang($id)
    {
        if ($r = $this->checkAccess()) return $r;

        $this->ensureGudangColumns();

        $g = $this->db->table('monitoring_gudang')->where('id', $id)->get()->getRowArray();
        if (!$g) {
            return redirect()->to(base_url('admin/inventaris/gudang'))->with('error', 'Data barang gudang tidak ditemukan.');
        }

        $kategoriList = $this->db->table('monitoring_gudang')
            ->select('kategori')
            ->distinct()
            ->where('kategori IS NOT NULL AND kategori != ""')
            ->get()->getResultArray();

        $categories = array_values(array_unique(array_merge(
            ['Material Konstruksi', 'Kelistrikan', 'Plumbing', 'Finishing', 'Sparepart Mesin', 'Lainnya'],
            array_column($kategoriList, 'kategori')
        )));

        $data = [
            'title'      => 'Edit Barang Gudang',
            'active'     => 'gudang',
            'g'          => $g,
            'categories' => $categories
        ];

        return view('admin/inventaris/gudang_edit', $data);
    }

    public function detailGudang($id)
    {
        if ($r = $this->checkAccess()) return $r;

        $this->ensureGudangColumns();

        $g = $this->db->table('monitoring_gudang')->where('id', $id)->get()->getRowArray();
        if (!$g) {
            return redirect()->to(base_url('admin/inventaris/gudang'))->with('error', 'Data barang gudang tidak ditemukan.');
        }

        $data = [
            'title'  => 'Detail Barang Gudang',
            'active' => 'gudang',
            'g'      => $g
        ];

        return view('admin/inventaris/gudang_detail', $data);
    }

    public function simpanGudang()
    {
        if ($r = $this->checkAccess()) return $r;

        $this->ensureGudangColumns();

        $stok = (int) $this->request->getPost('stok_tersedia');
        $status = 'tersedia';
        if ($stok <= 0) {
            $status = 'kosong';
        } elseif ($this->request->getPost('status')) {
            $status = $this->request->getPost('status');
        }

        $count = $this->db->table('monitoring_gudang')->countAllResults() + 1;
        $kode = 'MTR-' . date('Ym') . '-' . sprintf('%03d', $count);

        $fotoName = null;
        $file = $this->request->getFile('foto_barang');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fotoName = $file->getRandomName();
            $targetDir = ROOTPATH . 'public/uploads/gudang';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $file->move($targetDir, $fotoName);
            $this->compressImage($targetDir . '/' . $fotoName);
        }

        $this->db->table('monitoring_gudang')->insert([
            'kode_barang'   => $kode,
            'nama_barang'   => $this->request->getPost('nama_barang'),
            'kategori'      => $this->request->getPost('kategori') ?: 'Material',
            'stok_tersedia' => $stok,
            'satuan'        => $this->request->getPost('satuan') ?: 'Pcs',
            'lokasi_gudang' => $this->request->getPost('lokasi_gudang') ?: 'Gudang Blok K',
            'lokasi_rak'    => $this->request->getPost('lokasi_rak') ?: 'Rak A-1',
            'foto_barang'   => $fotoName,
            'status'        => $status,
            'created_at'    => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('admin/inventaris/gudang'))->with('success', 'Barang/material gudang baru berhasil ditambahkan.');
    }

    public function updateGudang()
    {
        if ($r = $this->checkAccess()) return $r;

        $this->ensureGudangColumns();
        $id = $this->request->getPost('id');
        $oldData = $this->db->table('monitoring_gudang')->where('id', $id)->get()->getRowArray();
        $stok = (int) $this->request->getPost('stok_tersedia');
        $status = $this->request->getPost('status');
        if ($stok <= 0 && $status !== 'indent') {
            $status = 'kosong';
        }

        $fotoName = $oldData['foto_barang'] ?? null;
        $file = $this->request->getFile('foto_barang');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newFoto = $file->getRandomName();
            $targetDir = ROOTPATH . 'public/uploads/gudang';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $file->move($targetDir, $newFoto);
            $this->compressImage($targetDir . '/' . $newFoto);
            
            if (!empty($fotoName) && file_exists($targetDir . '/' . $fotoName)) {
                @unlink($targetDir . '/' . $fotoName);
            }
            $fotoName = $newFoto;
        }

        $this->db->table('monitoring_gudang')->where('id', $id)->update([
            'nama_barang'   => $this->request->getPost('nama_barang'),
            'kategori'      => $this->request->getPost('kategori'),
            'stok_tersedia' => $stok,
            'satuan'        => $this->request->getPost('satuan'),
            'lokasi_gudang' => $this->request->getPost('lokasi_gudang'),
            'lokasi_rak'    => $this->request->getPost('lokasi_rak'),
            'foto_barang'   => $fotoName,
            'status'        => $status,
            'updated_at'    => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('admin/inventaris/gudang'))->with('success', 'Data barang/material gudang berhasil diperbarui.');
    }

    public function deleteGudang($id)
    {
        if ($r = $this->checkAccess()) return $r;

        $oldData = $this->db->table('monitoring_gudang')->where('id', $id)->get()->getRowArray();
        if ($oldData && !empty($oldData['foto_barang'])) {
            $file = ROOTPATH . 'public/uploads/gudang/' . $oldData['foto_barang'];
            if (file_exists($file)) {
                @unlink($file);
            }
        }

        $this->db->table('monitoring_gudang')->where('id', $id)->delete();
        return redirect()->to(base_url('admin/inventaris/gudang'))->with('success', 'Barang gudang berhasil dihapus.');
    }

    private function compressImage($filePath)
    {
        if (!file_exists($filePath)) return;

        try {
            \Config\Services::image()
                ->withFile($filePath)
                ->resize(800, 800, true, 'height')
                ->save($filePath, 70);
            return;
        } catch (\Throwable $e) {}

        try {
            $info = getimagesize($filePath);
            if (!$info) return;

            $mime = $info['mime'];
            if ($mime == 'image/jpeg') {
                $image = imagecreatefromjpeg($filePath);
                if ($image) {
                    imagejpeg($image, $filePath, 70);
                    imagedestroy($image);
                }
            } elseif ($mime == 'image/png') {
                $image = imagecreatefrompng($filePath);
                if ($image) {
                    imagealphablending($image, false);
                    imagesavealpha($image, true);
                    imagepng($image, $filePath, 7);
                    imagedestroy($image);
                }
            } elseif ($mime == 'image/webp') {
                $image = imagecreatefromwebp($filePath);
                if ($image) {
                    imagewebp($image, $filePath, 70);
                    imagedestroy($image);
                }
            }
        } catch (\Throwable $e) {}
    }
}
