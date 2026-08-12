<?php

namespace App\Controllers\Direktur;

use App\Controllers\BaseController;
use App\Models\KaryawanModel;

class PengadaanController extends BaseController
{
    protected $db;
    protected $karyawanModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->karyawanModel = new KaryawanModel();
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
                'nama_lengkap' => 'Direktur Utama',
                'jabatan'      => 'Direktur',
                'divisi'       => 'Manajemen',
                'created_at'   => date('Y-m-d H:i:s')
            ]);
            return intval($this->db->insertID());
        } catch (\Throwable $e) {
            return 1;
        }
    }

    // 1. Pengajuan ATK
    public function pengajuan_atk()
    {
        // Auto-clean exact duplicate rows created within 10 seconds of each other
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
            'title' => 'Pengajuan ATK Karyawan',
            'pengajuan' => $builder->get()->getResultArray()
        ];

        return view('direktur/pengadaan/pengajuan_atk', $data);
    }

    public function simpan_atk()
    {
        $karyawanId = session()->get('karyawan_id') ?? session()->get('id') ?? 1;
        $namaBarang = trim($this->request->getPost('nama_barang'));
        $jumlah     = $this->request->getPost('jumlah') ?: 1;
        $satuan     = $this->request->getPost('satuan') ?: 'Pcs';
        $alasan     = trim($this->request->getPost('alasan'));

        // Prevent duplicate submission within 5 seconds
        $fiveSecAgo = date('Y-m-d H:i:s', time() - 5);
        $existing = $this->db->table('pengajuan_atk')
            ->where('karyawan_id', $karyawanId)
            ->where('nama_barang', $namaBarang)
            ->where('jumlah', $jumlah)
            ->where('created_at >=', $fiveSecAgo)
            ->get()->getRowArray();

        if ($existing) {
            return redirect()->to(base_url('direktur/pengadaan/pengajuan-atk'))
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

        return redirect()->to(base_url('direktur/pengadaan/pengajuan-atk'))->with('success', 'Pengajuan ATK berhasil ditambahkan.');
    }

    public function update_atk()
    {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status') ?: 'menunggu';
        $data = [
            'nama_barang' => $this->request->getPost('nama_barang'),
            'jumlah'      => $this->request->getPost('jumlah') ?: 1,
            'satuan'      => $this->request->getPost('satuan') ?: 'Pcs',
            'alasan'      => $this->request->getPost('alasan'),
            'status'      => $status,
            'updated_at'  => date('Y-m-d H:i:s')
        ];

        $this->db->table('pengajuan_atk')->where('id', $id)->update($data);

        // Otomatis teruskan ke Form Pembelian (PR) jika disetujui
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
                    $pemohonName = !empty($p['nama_lengkap']) ? $p['nama_lengkap'] : 'Direktur';
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
                        'metode_pembayaran' => 'Dibayar Langsung Tokopedia Direktur',
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

                        if (in_array('jumlah', $itemFields)) $itemRow['jumlah'] = $p['jumlah'] ?: 1;
                        elseif (in_array('qty', $itemFields)) $itemRow['qty'] = $p['jumlah'] ?: 1;
                        elseif (in_array('quantity', $itemFields)) $itemRow['quantity'] = $p['jumlah'] ?: 1;

                        if (in_array('satuan', $itemFields)) $itemRow['satuan'] = $p['satuan'] ?: 'Pcs';

                        $this->db->table('form_pembelian_item')->insert($itemRow);
                    }
                }
            }
        }

        return redirect()->to(base_url('direktur/pengadaan/pengajuan-atk'))->with('success', 'Data pengajuan ATK berhasil diperbarui.');
    }

    public function delete_atk($id)
    {
        // Hapus transaksi PR di form_pembelian & items jika ada yang terkait
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
        return redirect()->to(base_url('direktur/pengadaan/pengajuan-atk'))->with('success', 'Data pengajuan ATK & data pencatatan pembelian terkait berhasil dihapus.');
    }

    public function review_atk($id)
    {
        $pengajuan = $this->db->table('pengajuan_atk p')
            ->select('p.*, k.nama_lengkap, k.jabatan, k.divisi')
            ->join('karyawan k', 'k.id = p.karyawan_id', 'left')
            ->where('p.id', $id)
            ->get()->getRowArray();

        if (!$pengajuan) {
            return redirect()->to(base_url('direktur/pengadaan/pengajuan-atk'))->with('error', 'Data pengajuan ATK tidak ditemukan.');
        }

        $data = [
            'title' => 'Review Pengajuan ATK',
            'p' => $pengajuan
        ];

        return view('direktur/pengadaan/review_atk', $data);
    }

    public function approve_atk()
    {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');
        $komentar = $this->request->getPost('komentar');

        $this->db->table('pengajuan_atk')->where('id', $id)->update([
            'status' => $status,
            'komentar_direktur' => $komentar,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Otomatis teruskan ke Form Pembelian (PR) jika disetujui
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
                    $pemohonName = !empty($p['nama_lengkap']) ? $p['nama_lengkap'] : 'Direktur';
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
                        'metode_pembayaran' => 'Dibayar Langsung Tokopedia Direktur',
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

        return redirect()->to(base_url('direktur/pengadaan/pengajuan-atk'))->with('success', 'Status pengajuan ATK berhasil diperbarui & diteruskan ke Form Pembelian PR.');
    }

    // 2. Monitoring Stok ATK
    public function stok_atk()
    {
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
            'title' => 'Monitoring Stok ATK',
            'stok'  => $stok
        ];

        return view('direktur/pengadaan/stok_atk', $data);
    }

    public function detail_stok_atk($id)
    {
        $item = $this->db->table('stok_atk')->where('id', $id)->get()->getRowArray();
        if (!$item) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data stok ATK tidak ditemukan']);
        }
        return $this->response->setJSON(['status' => 'success', 'data' => $item]);
    }

    public function simpan_stok_atk()
    {
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

        return redirect()->to(base_url('direktur/pengadaan/stok-atk'))->with('success', 'Item inventaris stok ATK berhasil ditambahkan.');
    }

    public function update_stok_atk()
    {
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

        return redirect()->to(base_url('direktur/pengadaan/stok-atk'))->with('success', 'Data stok ATK berhasil diperbarui.');
    }

    public function delete_stok_atk($id)
    {
        $this->db->table('stok_atk')->where('id', $id)->delete();
        return redirect()->to(base_url('direktur/pengadaan/stok-atk'))->with('success', 'Item stok ATK berhasil dihapus.');
    }

    // 3. Pengadaan Aset
    public function aset()
    {
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
            'title' => 'Pengadaan Aset Perusahaan',
            'aset'  => $aset
        ];

        return view('direktur/pengadaan/aset', $data);
    }

    public function review_aset($id)
    {
        $aset = $this->db->table('pengadaan_aset')->where('id', $id)->get()->getRowArray();

        if (!$aset) {
            return redirect()->to(base_url('direktur/pengadaan/aset'))->with('error', 'Data pengadaan aset tidak ditemukan.');
        }

        $data = [
            'title' => 'Review Pengadaan Aset',
            'a' => $aset
        ];

        return view('direktur/pengadaan/review_aset', $data);
    }

    public function cetak_aset()
    {
        $aset = $this->db->table('pengadaan_aset')->orderBy('id', 'DESC')->get()->getResultArray();

        $data = [
            'aset' => $aset,
            'tanggalCetak' => date('d F Y')
        ];

        return view('direktur/pengadaan/cetak_aset', $data);
    }

    public function simpan_aset()
    {
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

        return redirect()->to(base_url('direktur/pengadaan/aset'))->with('success', 'Usulan pengadaan aset berhasil dikirim.');
    }

    public function update_aset()
    {
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

        return redirect()->to(base_url('direktur/pengadaan/aset'))->with('success', 'Data pengadaan aset berhasil diperbarui.');
    }

    public function approve_aset()
    {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');
        $komentar = $this->request->getPost('komentar');

        $this->db->table('pengadaan_aset')->where('id', $id)->update([
            'status' => $status,
            'komentar_direktur' => $komentar,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Otomatis teruskan ke Form Pembelian (PR) jika disetujui
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

        return redirect()->to(base_url('direktur/pengadaan/aset'))->with('success', 'Status pengadaan aset berhasil diperbarui & diteruskan ke Form Pembelian PR.');
    }

    public function delete_aset($id)
    {
        // Hapus transaksi PR di form_pembelian & items jika ada yang terkait
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
        return redirect()->to(base_url('direktur/pengadaan/aset'))->with('success', 'Data pengadaan aset & data pencatatan pembelian terkait berhasil dihapus.');
    }

    // 4. Kerusakan Alat
    public function kerusakan()
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
                    'kode_laporan' => 'KRK-202608001',
                    'nama_alat' => 'Printer Epson EcoTank L3210 (HRD)',
                    'lokasi_alat' => 'Ruang HRD & GA Lt. 2',
                    'deskripsi_kerusakan' => 'Hasil cetak garis-garis dan tinta merah tidak keluar walau sudah head cleaning',
                    'tingkat_kerusakan' => 'sedang',
                    'teknisi_pengurus' => 'Rian (Teknisi IT Staff)',
                    'lokasi_perbaikan' => 'Service Center Resmi Epson (Kelapa Gading)',
                    'petugas_pembawa' => 'Doni (Driver Operational)',
                    'catatan_perbaikan' => 'Unit sudah dibawa ke service center resmi, estimasi selesai 3 hari',
                    'status_tindakan' => 'dalam_perbaikan',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'kode_laporan' => 'KRK-202608002',
                    'nama_alat' => 'Proyektor BenQ MX560 Meeting Room',
                    'lokasi_alat' => 'Ruang Meeting Utama Lt. 1',
                    'deskripsi_kerusakan' => 'Lampu proyektor redup dan suka mati sendiri setelah 10 menit digunakan',
                    'tingkat_kerusakan' => 'berat',
                    'teknisi_pengurus' => 'Budi (Supervisor IT)',
                    'lokasi_perbaikan' => 'Ruang Workshop IT Lt. 1',
                    'petugas_pembawa' => 'Budi (Self Deliver)',
                    'catatan_perbaikan' => 'Menunggu penggantian suku cadang bohlam lampu proyektor baru',
                    'status_tindakan' => 'dilaporkan',
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ];
            foreach ($defaultData as $item) {
                $this->db->table('laporan_kerusakan')->insert($item);
            }
        } else {
            $fields = $this->db->getFieldNames('laporan_kerusakan');
            $forge = \Config\Database::forge();
            if (!in_array('teknisi_pengurus', $fields)) {
                $forge->addColumn('laporan_kerusakan', ['teknisi_pengurus' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true]]);
            }
            if (!in_array('lokasi_perbaikan', $fields)) {
                $forge->addColumn('laporan_kerusakan', ['lokasi_perbaikan' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true]]);
            }
            if (!in_array('petugas_pembawa', $fields)) {
                $forge->addColumn('laporan_kerusakan', ['petugas_pembawa' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true]]);
            }
            if (!in_array('catatan_perbaikan', $fields)) {
                $forge->addColumn('laporan_kerusakan', ['catatan_perbaikan' => ['type' => 'TEXT', 'null' => true]]);
            }
        }

        $builder = $this->db->table('laporan_kerusakan l')
            ->select('l.*, k.nama_lengkap as pelapor')
            ->join('karyawan k', 'k.id = l.pelapor_id', 'left')
            ->orderBy('l.id', 'DESC');

        $data = [
            'title' => 'Laporan Kerusakan Alat',
            'kerusakan' => $builder->get()->getResultArray(),
            'karyawan' => $this->karyawanModel->findAll()
        ];

        return view('direktur/pengadaan/kerusakan', $data);
    }

    public function tambah_kerusakan()
    {
        $data = [
            'title' => 'Laporkan Kerusakan Alat Baru',
            'karyawan' => $this->karyawanModel->findAll()
        ];

        return view('direktur/pengadaan/kerusakan_tambah', $data);
    }

    public function edit_kerusakan($id)
    {
        $k = $this->db->table('laporan_kerusakan l')
            ->select('l.*, k.nama_lengkap as pelapor')
            ->join('karyawan k', 'k.id = l.pelapor_id', 'left')
            ->where('l.id', $id)
            ->get()->getRowArray();

        if (!$k) {
            return redirect()->to(base_url('direktur/pengadaan/kerusakan'))->with('error', 'Laporan kerusakan alat tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Laporan Kerusakan Alat',
            'k' => $k,
            'karyawan' => $this->karyawanModel->findAll()
        ];

        return view('direktur/pengadaan/kerusakan_edit', $data);
    }

    public function detail_kerusakan($id)
    {
        $k = $this->db->table('laporan_kerusakan l')
            ->select('l.*, k.nama_lengkap as pelapor, k.jabatan as pelapor_jabatan, k.divisi as pelapor_divisi')
            ->join('karyawan k', 'k.id = l.pelapor_id', 'left')
            ->where('l.id', $id)
            ->get()->getRowArray();

        if (!$k) {
            return redirect()->to(base_url('direktur/pengadaan/kerusakan'))->with('error', 'Laporan kerusakan alat tidak ditemukan.');
        }

        $data = [
            'title' => 'Detail Laporan Kerusakan Alat',
            'k' => $k
        ];

        return view('direktur/pengadaan/kerusakan_detail', $data);
    }

    public function simpan_kerusakan()
    {
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

        return redirect()->to(base_url('direktur/pengadaan/kerusakan'))->with('success', 'Laporan kerusakan alat berhasil ditambahkan.');
    }

    public function update_kerusakan()
    {
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

        return redirect()->to(base_url('direktur/pengadaan/kerusakan'))->with('success', 'Data laporan kerusakan alat berhasil diperbarui.');
    }

    public function delete_kerusakan($id)
    {
        $this->db->table('laporan_kerusakan')->where('id', $id)->delete();
        return redirect()->to(base_url('direktur/pengadaan/kerusakan'))->with('success', 'Laporan kerusakan alat berhasil dihapus.');
    }

    // 5. Monitoring Gudang & Material
    private function ensureGudangColumns()
    {
        if (!$this->db->tableExists('monitoring_gudang')) {
            $forge = \Config\Database::forge();
            $forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'kode_barang' => ['type' => 'VARCHAR', 'constraint' => 50],
                'nama_barang' => ['type' => 'VARCHAR', 'constraint' => 255],
                'kategori' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'stok_tersedia' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'satuan' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Pcs'],
                'lokasi_gudang' => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'Gudang Blok K'],
                'lokasi_rak' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'foto_barang' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'status' => ['type' => 'ENUM', 'constraint' => ['tersedia', 'indent', 'kosong'], 'default' => 'tersedia'],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true]
            ]);
            $forge->addKey('id', true);
            $forge->createTable('monitoring_gudang', true);

            $defaultData = [
                [
                    'kode_barang' => 'MTR-202608-001',
                    'nama_barang' => 'Semen Tiga Roda 50kg',
                    'kategori' => 'Material Konstruksi',
                    'stok_tersedia' => 120,
                    'satuan' => 'Sak',
                    'lokasi_gudang' => 'Gudang Blok K',
                    'lokasi_rak' => 'Sektor A - Rak 01',
                    'status' => 'tersedia',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'kode_barang' => 'MTR-202608-002',
                    'nama_barang' => 'Besi Beton 12mm Polos',
                    'kategori' => 'Material Konstruksi',
                    'stok_tersedia' => 45,
                    'satuan' => 'Batang',
                    'lokasi_gudang' => 'Gudang Blok K',
                    'lokasi_rak' => 'Sektor A - Rak 04',
                    'status' => 'tersedia',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'kode_barang' => 'MTR-202608-003',
                    'nama_barang' => 'Kabel NYM 3x2.5mm (100m)',
                    'kategori' => 'Kelistrikan',
                    'stok_tersedia' => 15,
                    'satuan' => 'Roll',
                    'lokasi_gudang' => 'Kantor',
                    'lokasi_rak' => 'Sektor B - Rak 02',
                    'status' => 'tersedia',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'kode_barang' => 'MTR-202608-004',
                    'nama_barang' => 'Pipa PVC Wavin 4 Inch',
                    'kategori' => 'Plumbing',
                    'stok_tersedia' => 8,
                    'satuan' => 'Batang',
                    'lokasi_gudang' => 'Gudang Blok I',
                    'lokasi_rak' => 'Sektor C - Rak 01',
                    'status' => 'indent',
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'kode_barang' => 'MTR-202608-005',
                    'nama_barang' => 'Cat Tembok Dulux White 20L',
                    'kategori' => 'Finishing',
                    'stok_tersedia' => 0,
                    'satuan' => 'Pail',
                    'lokasi_gudang' => 'Gudang Blok I',
                    'lokasi_rak' => 'Sektor D - Rak 02',
                    'status' => 'kosong',
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ];
            foreach ($defaultData as $item) {
                $this->db->table('monitoring_gudang')->insert($item);
            }
        } else {
            $fields = $this->db->getFieldNames('monitoring_gudang');
            $forge = \Config\Database::forge();
            if (!in_array('lokasi_gudang', $fields)) {
                $forge->addColumn('monitoring_gudang', [
                    'lokasi_gudang' => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'Gudang Blok K']
                ]);
            }
            if (!in_array('foto_barang', $fields)) {
                $forge->addColumn('monitoring_gudang', [
                    'foto_barang' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true]
                ]);
            }
        }
    }

    public function gudang()
    {
        $this->ensureGudangColumns();

        $gudang = $this->db->table('monitoring_gudang')->orderBy('id', 'DESC')->get()->getResultArray();

        $data = [
            'title' => 'Monitoring Gudang & Stok Material',
            'gudang' => $gudang
        ];

        return view('direktur/pengadaan/gudang', $data);
    }

    public function tambah_gudang()
    {
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
            'title' => 'Tambah Stok Barang Gudang Baru',
            'autoKode' => $autoKode,
            'categories' => $categories
        ];

        return view('direktur/pengadaan/gudang_tambah', $data);
    }

    public function edit_gudang($id)
    {
        $this->ensureGudangColumns();

        $g = $this->db->table('monitoring_gudang')->where('id', $id)->get()->getRowArray();
        if (!$g) {
            return redirect()->to(base_url('direktur/pengadaan/gudang'))->with('error', 'Data barang gudang tidak ditemukan.');
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
            'title' => 'Edit Barang Gudang',
            'g' => $g,
            'categories' => $categories
        ];

        return view('direktur/pengadaan/gudang_edit', $data);
    }

    public function detail_gudang($id)
    {
        $this->ensureGudangColumns();

        $g = $this->db->table('monitoring_gudang')->where('id', $id)->get()->getRowArray();
        if (!$g) {
            return redirect()->to(base_url('direktur/pengadaan/gudang'))->with('error', 'Data barang gudang tidak ditemukan.');
        }

        $data = [
            'title' => 'Detail Barang Gudang',
            'g' => $g
        ];

        return view('direktur/pengadaan/gudang_detail', $data);
    }

    public function simpan_gudang()
    {
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

        return redirect()->to(base_url('direktur/pengadaan/gudang'))->with('success', 'Barang/material gudang baru berhasil ditambahkan.');
    }

    public function update_gudang()
    {
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
            
            // Delete old foto if exists
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

        return redirect()->to(base_url('direktur/pengadaan/gudang'))->with('success', 'Data barang/material gudang berhasil diperbarui.');
    }

    public function delete_gudang($id)
    {
        $oldData = $this->db->table('monitoring_gudang')->where('id', $id)->get()->getRowArray();
        if ($oldData && !empty($oldData['foto_barang'])) {
            $file = ROOTPATH . 'public/uploads/gudang/' . $oldData['foto_barang'];
            if (file_exists($file)) {
                @unlink($file);
            }
        }

        $this->db->table('monitoring_gudang')->where('id', $id)->delete();
        return redirect()->to(base_url('direktur/pengadaan/gudang'))->with('success', 'Barang gudang berhasil dihapus.');
    }

    private function compressImage($filePath)
    {
        if (!file_exists($filePath)) return;

        // Try CodeIgniter Image Service
        try {
            \Config\Services::image()
                ->withFile($filePath)
                ->resize(800, 800, true, 'height')
                ->save($filePath, 70);
            return;
        } catch (\Throwable $e) {}

        // Fallback to Native GD
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
