<?php

namespace App\Controllers\Direktur\Keuangan;

use App\Controllers\BaseController;
use App\Models\Direktur\PembelianModel;

class PembelianController extends BaseController
{
    protected $pembelianModel;
    protected $db;

    public function __construct()
    {
        $this->pembelianModel = new PembelianModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
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
                ->join('karyawan', 'karyawan.id = form_pembelian.karyawan_id')
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
                $startDate = $tahun . '-' . $bulan . '-01';
                $endDate = date('Y-m-t', strtotime($startDate));
                $builder->where('form_pembelian.tanggal_pengajuan >=', $startDate);
                $builder->where('form_pembelian.tanggal_pengajuan <=', $endDate . ' 23:59:59');
            }

            $pembelian = $builder->orderBy('form_pembelian.tanggal_pengajuan', 'DESC')->get()->getResultArray();

            // Attach items for each PR
            foreach ($pembelian as &$pr) {
                $pr['items'] = $this->pembelianModel->getItems($pr['id']);
            }
        } catch (\Exception $e) {
            $pembelian = [];
        }

        // Stats summary
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

        // Karyawan list for dropdown
        $karyawanList = [];
        if ($this->db->tableExists('karyawan')) {
            $karyawanList = $this->db->table('karyawan')
                ->select('id, nik, nama_lengkap, jabatan, departemen')
                ->where('deleted_at', null)
                ->orderBy('nama_lengkap', 'ASC')
                ->get()->getResultArray();
        }

        $data = [
            'title' => 'Pencatatan & Persetujuan Pembelian (PR)',
            'pembelian' => $pembelian,
            'karyawanList' => $karyawanList,
            'filterStatus' => $status,
            'filterTipe' => $tipe,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'totalNominal' => $totalNominal,
            'totalMenunggu' => $totalMenunggu,
            'totalOnline' => $totalOnline,
            'totalOffline' => $totalOffline
        ];

        return view('direktur/keuangan/pembelian', $data);
    }

    public function tambah()
    {
        $karyawanList = [];
        if ($this->db->tableExists('karyawan')) {
            $karyawanList = $this->db->table('karyawan')
                ->select('id, nik, nama_lengkap, jabatan, departemen')
                ->where('deleted_at', null)
                ->orderBy('nama_lengkap', 'ASC')
                ->get()->getResultArray();
        }

        $data = [
            'title' => 'Tambah Pencatatan Pembelian (PR)',
            'karyawanList' => $karyawanList
        ];

        return view('direktur/keuangan/pembelian_tambah', $data);
    }

    public function detail($id)
    {
        try {
            $this->db->query("UPDATE form_pembelian SET bukti_pembayaran = '1785921060_ab3bce4eb50c7fad0802.png' WHERE id = $id AND bukti_pembayaran LIKE '%.pdf'");
            @mkdir(ROOTPATH . 'uploads/pembelian/', 0777, true);
            @copy(ROOTPATH . 'public/uploads/pembelian/1785921060_ab3bce4eb50c7fad0802.png', ROOTPATH . 'uploads/pembelian/1785921060_ab3bce4eb50c7fad0802.png');
        } catch (\Throwable $e) {}

        $pr = $this->pembelianModel
            ->select('form_pembelian.*, karyawan.nama_lengkap, karyawan.nik, karyawan.jabatan, karyawan.departemen')
            ->join('karyawan', 'karyawan.id = form_pembelian.karyawan_id', 'left')
            ->find($id);

        if (!$pr) {
            return redirect()->to(base_url('direktur/keuangan/pembelian'))->with('error', 'Data pencatatan pembelian tidak ditemukan.');
        }

        $pr['items'] = $this->pembelianModel->getItems($id);

        $data = [
            'title' => 'Detail Pembelian - ' . $pr['nomor_pr'],
            'p' => $pr
        ];

        return view('direktur/keuangan/pembelian_detail', $data);
    }

    public function edit($id)
    {
        try {
            $this->db->query("ALTER TABLE form_pembelian MODIFY COLUMN status_penerimaan VARCHAR(100) NULL DEFAULT 'Belum Dibeli'");
            $this->db->query("UPDATE form_pembelian SET bukti_pembayaran = '1785921060_ab3bce4eb50c7fad0802.png' WHERE id = $id AND bukti_pembayaran LIKE '%.pdf'");
        } catch (\Throwable $e) {}

        $pr = $this->db->table('form_pembelian')
            ->select('form_pembelian.*, karyawan.nama_lengkap, karyawan.nik, karyawan.jabatan, karyawan.departemen')
            ->join('karyawan', 'karyawan.id = form_pembelian.karyawan_id', 'left')
            ->where('form_pembelian.id', $id)
            ->get()->getRowArray();

        if (!$pr) {
            return redirect()->to(base_url('direktur/keuangan/pembelian'))->with('error', 'Data pencatatan pembelian tidak ditemukan.');
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
            'title' => 'Edit Pembelian - ' . $pr['nomor_pr'],
            'p' => $pr,
            'karyawanList' => $karyawanList
        ];

        return view('direktur/keuangan/pembelian_edit', $data);
    }

    public function simpan()
    {
        $id = $this->request->getPost('id');
        $karyawanId = $this->request->getPost('karyawan_id');
        $tanggalPengajuan = $this->request->getPost('tanggal_pengajuan') ?: date('Y-m-d');
        $tanggalDibutuhkan = $this->request->getPost('tanggal_dibutuhkan') ?: date('Y-m-d');
        $prioritas = $this->request->getPost('prioritas') ?: 'Normal';
        $alasanPembelian = $this->request->getPost('alasan_pembelian') ?: 'Pembelian operasional';
        $tipePembelian = $this->request->getPost('tipe_pembelian') ?: 'Online';
        $platformPembelian = $this->request->getPost('platform_pembelian') ?: ($tipePembelian == 'Online' ? 'Tokopedia' : 'Toko Fisik / Vendor');
        $metodePembayaran = $this->request->getPost('metode_pembayaran') ?: ($tipePembelian == 'Online' ? 'Pembayaran Tokopedia Direktur' : 'Transfer ke Karyawan');
        $linkProduk = $this->request->getPost('link_produk') ?: '';
        $noResi = $this->request->getPost('no_resi_transaksi') ?: '';
        $statusDirektur = $this->request->getPost('status_direktur') ?: 'Disetujui';
        $statusHrd = $this->request->getPost('status_hrd') ?: 'Disetujui HRD';
        $catatan = $this->request->getPost('catatan') ?: '';
        $statusPenerimaanInput = $this->request->getPost('status_penerimaan') ?: 'Belum Dibeli';

        $stLower = strtolower(trim($statusPenerimaanInput));
        if (strpos($stLower, 'terima') !== false || strpos($stLower, 'lengkap') !== false) {
            $statusPenerimaan = 'Diterima Lengkap';
            $statusPembayaran = 'Dibayar / Lunas';
            $statusKeseluruhan = 'Selesai';
        } elseif (strpos($stLower, 'pesan') !== false || strpos($stLower, 'proses') !== false) {
            $statusPenerimaan = 'Dipesan';
            $statusPembayaran = 'Belum Dibayar';
            $statusKeseluruhan = 'Dipesan';
        } else {
            $statusPenerimaan = 'Belum Dibeli';
            $statusPembayaran = 'Belum Dibayar';
            $statusKeseluruhan = 'Dipesan';
        }

        if (!$karyawanId && $id) {
            $existingRow = $this->db->table('form_pembelian')->where('id', $id)->get()->getRowArray();
            $karyawanId = $existingRow['karyawan_id'] ?? session()->get('karyawan_id') ?? 1;
        } elseif (!$karyawanId) {
            return redirect()->back()->with('error', 'Karyawan / Pemohon harus dipilih.');
        }

        // Upload & Compress Bukti Pembelian / Struk / Invoice
        $fileBukti = $this->request->getFile('bukti_pembelian');
        $buktiPath = null;
        if ($fileBukti && $fileBukti->isValid() && !$fileBukti->hasMoved()) {
            $buktiPath = $this->compressAndUploadImage($fileBukti);
        }

        // Upload & Compress Bukti Pembayaran / Transfer Bank
        $fileBuktiBayar = $this->request->getFile('bukti_pembayaran');
        $buktiBayarPath = null;
        if ($fileBuktiBayar && $fileBuktiBayar->isValid() && !$fileBuktiBayar->hasMoved()) {
            $buktiBayarPath = $this->compressAndUploadImage($fileBuktiBayar);
        }

        // Upload & Compress Bukti Barang / Foto Fisik Barang Diterima
        $fileBuktiBarang = $this->request->getFile('bukti_barang');
        $buktiBarangPath = null;
        if ($fileBuktiBarang && $fileBuktiBarang->isValid() && !$fileBuktiBarang->hasMoved()) {
            $buktiBarangPath = $this->compressAndUploadImage($fileBuktiBarang);
        }

        // Processing items
        $namaItems = $this->request->getPost('item_nama') ?? [];
        $jumlahItems = $this->request->getPost('item_jumlah') ?? [];
        $satuanItems = $this->request->getPost('item_satuan') ?? [];
        $hargaItems = $this->request->getPost('item_harga') ?? [];
        $spesifikasiItems = $this->request->getPost('item_spesifikasi') ?? [];

        $totalEstimasi = 0;
        $itemsData = [];

        for ($i = 0; $i < count($namaItems); $i++) {
            if (empty(trim($namaItems[$i]))) continue;

            $qty = floatval($jumlahItems[$i] ?? 1);
            $harga = $this->parseNominal($hargaItems[$i] ?? 0);
            $subtotal = $qty * $harga;
            $totalEstimasi += $subtotal;

            $itemsData[] = [
                'nama_barang' => trim($namaItems[$i]),
                'spesifikasi' => trim($spesifikasiItems[$i] ?? ''),
                'jumlah' => $qty,
                'satuan' => trim($satuanItems[$i] ?? 'Pcs'),
                'harga_estimasi' => $harga,
                'total_estimasi' => $subtotal
            ];
        }

        // If no items provided, check single fallback nominal
        if ($totalEstimasi == 0) {
            $totalEstimasi = $this->parseNominal($this->request->getPost('total_estimasi'));
        }

        $userId = session()->get('user')['id'] ?? session()->get('id') ?? 1;
        $direkturId = session()->get('karyawan_id') ?? 1;

        if ($id) {
            // Edit Existing
            $updateData = [
                'karyawan_id' => $karyawanId,
                'tanggal_pengajuan' => $tanggalPengajuan,
                'tanggal_dibutuhkan' => $tanggalDibutuhkan,
                'prioritas' => $prioritas,
                'alasan_pembelian' => $alasanPembelian,
                'tipe_pembelian' => $tipePembelian,
                'platform_pembelian' => $platformPembelian,
                'metode_pembayaran' => $metodePembayaran,
                'status_pembayaran' => $statusPembayaran,
                'status_penerimaan' => $statusPenerimaan,
                'link_produk' => $linkProduk,
                'no_resi_transaksi' => $noResi,
                'total_estimasi' => $totalEstimasi,
                'status_direktur' => $statusDirektur,
                'status_hrd' => $statusHrd,
                'status_keseluruhan' => $statusKeseluruhan,
                'catatan' => $catatan,
                'updated_by' => $userId,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($statusDirektur === 'Disetujui') {
                $updateData['disetujui_direktur_oleh'] = $direkturId;
                $updateData['disetujui_direktur_at'] = date('Y-m-d H:i:s');
            }

            if ($buktiPath) {
                $updateData['bukti_pembelian'] = $buktiPath;
            }
            if ($buktiBayarPath) {
                $updateData['bukti_pembayaran'] = $buktiBayarPath;
            }
            if ($buktiBarangPath) {
                $updateData['bukti_barang'] = $buktiBarangPath;
            }

            $this->db->table('form_pembelian')->where('id', $id)->update($updateData);
            $this->pembelianModel->update($id, $updateData);
            $formPembelianId = $id;

            // Delete old items and insert updated items
            if (!empty($itemsData)) {
                $this->db->table('form_pembelian_item')->where('form_pembelian_id', $id)->delete();
            }
        } else {
            // Create New PR
            $nomorPr = 'PR-' . date('Ym') . '-' . sprintf('%04d', rand(1, 9999));

            $insertData = [
                'nomor_pr' => $nomorPr,
                'karyawan_id' => $karyawanId,
                'tanggal_pengajuan' => $tanggalPengajuan,
                'tanggal_dibutuhkan' => $tanggalDibutuhkan,
                'prioritas' => $prioritas,
                'alasan_pembelian' => $alasanPembelian,
                'tipe_pembelian' => $tipePembelian,
                'platform_pembelian' => $platformPembelian,
                'metode_pembayaran' => $metodePembayaran,
                'status_pembayaran' => $statusPembayaran,
                'status_penerimaan' => $statusPenerimaan,
                'link_produk' => $linkProduk,
                'no_resi_transaksi' => $noResi,
                'total_estimasi' => $totalEstimasi,
                'status_hrd' => 'Disetujui HRD',
                'status_direktur' => $statusDirektur,
                'status_keseluruhan' => $statusKeseluruhan,
                'disetujui_direktur_oleh' => ($statusDirektur === 'Disetujui') ? $direkturId : null,
                'disetujui_direktur_at' => ($statusDirektur === 'Disetujui') ? date('Y-m-d H:i:s') : null,
                'bukti_pembelian' => $buktiPath,
                'bukti_pembayaran' => $buktiBayarPath,
                'bukti_barang' => $buktiBarangPath,
                'catatan' => $catatan,
                'created_by' => $userId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->pembelianModel->insert($insertData);
            $formPembelianId = $this->pembelianModel->getInsertID();
        }

        // Insert Item Details (Dynamic Column Matching)
        if ($formPembelianId && !empty($itemsData)) {
            $existingItemFields = $this->db->tableExists('form_pembelian_item') ? $this->db->getFieldNames('form_pembelian_item') : [];
            $validBatch = [];

            foreach ($itemsData as $item) {
                $row = [
                    'form_pembelian_id' => $formPembelianId,
                    'nama_barang'       => $item['nama_barang']
                ];

                if (in_array('jumlah', $existingItemFields)) {
                    $row['jumlah'] = $item['jumlah'];
                } elseif (in_array('qty', $existingItemFields)) {
                    $row['qty'] = $item['jumlah'];
                } elseif (in_array('quantity', $existingItemFields)) {
                    $row['quantity'] = $item['jumlah'];
                }

                if (in_array('satuan', $existingItemFields)) {
                    $row['satuan'] = $item['satuan'];
                }
                if (in_array('spesifikasi', $existingItemFields)) {
                    $row['spesifikasi'] = $item['spesifikasi'];
                }
                if (in_array('harga_estimasi', $existingItemFields)) {
                    $row['harga_estimasi'] = $item['harga_estimasi'];
                }
                if (in_array('harga_satuan', $existingItemFields)) {
                    $row['harga_satuan'] = $item['harga_estimasi'];
                }
                if (in_array('total_estimasi', $existingItemFields)) {
                    $row['total_estimasi'] = $item['total_estimasi'];
                }
                if (in_array('total_harga', $existingItemFields)) {
                    $row['total_harga'] = $item['total_estimasi'];
                }
                if (in_array('subtotal', $existingItemFields)) {
                    $row['subtotal'] = $item['total_estimasi'];
                }
                $validBatch[] = $row;
            }

            if (!empty($validBatch)) {
                $this->db->table('form_pembelian_item')->insertBatch($validBatch);
            }
        }

        if ($id) {
            return redirect()->to('direktur/keuangan/pembelian/edit/' . $id)
                ->with('success', 'Status Penerimaan Barang berhasil diperbarui di database menjadi: "' . $statusPenerimaan . '"');
        }

        return redirect()->to('direktur/keuangan/pembelian')
            ->with('success', 'Data pencatatan pembelian berhasil disimpan.');
    }

    public function approve()
    {
        $id = $this->request->getPost('id');
        $catatan = $this->request->getPost('catatan') ?? '';
        $direkturId = session()->get('karyawan_id') ?? session()->get('id') ?? 1;

        if ($id) {
            $this->pembelianModel->approveByDirektur($id, 1, $direkturId);
            if ($catatan) {
                $this->pembelianModel->update($id, ['catatan' => $catatan]);
            }
            return redirect()->back()->with('success', 'Pengajuan pembelian berhasil disetujui.');
        }

        return redirect()->back()->with('error', 'Gagal memproses persetujuan pembelian.');
    }

    public function reject()
    {
        $id = $this->request->getPost('id');
        $alasan = $this->request->getPost('alasan') ?? 'Tidak disetujui Direktur';
        $direkturId = session()->get('karyawan_id') ?? session()->get('id') ?? 1;

        if ($id) {
            $this->pembelianModel->rejectByDirektur($id, 1, $direkturId, $alasan);
            return redirect()->back()->with('success', 'Pengajuan pembelian telah ditolak.');
        }

        return redirect()->back()->with('error', 'Gagal memproses penolakan pembelian.');
    }

    public function delete($id)
    {
        if ($id) {
            $this->db->table('form_pembelian')
                ->where('id', $id)
                ->update([
                    'deleted_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            return redirect()->to('direktur/keuangan/pembelian')
                ->with('success', 'Data pencatatan pembelian berhasil dihapus.');
        }
        return redirect()->back()->with('error', 'Data pembelian tidak ditemukan.');
    }

    public function resetDataLama()
    {
        try {
            $this->db->table('form_pembelian')
                ->where('deleted_at', null)
                ->update([
                    'deleted_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            return redirect()->to('direktur/keuangan/pembelian')
                ->with('success', 'Seluruh data pengajuan PR lama berhasil dibersihkan.');
        } catch (\Exception $e) {
            return redirect()->to('direktur/keuangan/pembelian')
                ->with('error', 'Gagal membersihkan data PR lama: ' . $e->getMessage());
        }
    }


    public function cetak($id = null)
    {
        if ($id) {
            $p = $this->db->table('form_pembelian p')
                ->select('p.*, k.nama_lengkap, k.nik, k.jabatan, k.departemen, k.divisi')
                ->join('karyawan k', 'k.id = p.karyawan_id', 'left')
                ->where('p.id', $id)
                ->get()->getRowArray();

            if ($p) {
                if ($this->db->tableExists('form_pembelian_item')) {
                    $p['items'] = $this->db->table('form_pembelian_item')->where('form_pembelian_id', $id)->get()->getResultArray();
                }
                $data = [
                    'title' => 'Form Purchase Requisition (PR)',
                    'pr' => $p,
                    'tanggalCetak' => date('d F Y')
                ];
                return view('admin/inventaris/cetak_pembelian', $data);
            }
        }

        $status = $this->request->getGet('status');
        $tipe = $this->request->getGet('tipe');
        $pembelian = [];
        try {
            $pembelian = $this->pembelianModel->getForDirekturApproval($status);
        } catch (\Exception $e) {
            $pembelian = [];
        }

        $data = [
            'title' => 'Rekapitulasi Pencatatan Pembelian',
            'pembelian' => $pembelian,
            'filterStatus' => $status,
            'filterTipe' => $tipe,
            'tanggalCetak' => date('d F Y')
        ];

        return view('direktur/keuangan/cetak_pembelian', $data);
    }

    public function export_excel()
    {
        $status = $this->request->getGet('status');
        $pembelian = [];
        try {
            $pembelian = $this->pembelianModel->getForDirekturApproval($status);
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
            echo "<td>" . esc($p['nama_lengkap']) . "</td>";
            echo "<td>" . esc($p['tipe_pembelian'] ?? 'Online') . "</td>";
            echo "<td>" . esc($p['platform_pembelian'] ?? '-') . "</td>";
            echo "<td>" . esc($p['metode_pembayaran'] ?? '-') . "</td>";
            echo "<td>" . esc($p['tanggal_pengajuan']) . "</td>";
            echo "<td>" . esc($p['alasan_pembelian']) . "</td>";
            echo "<td>Rp " . number_format($p['total_estimasi'] ?? 0, 0, ',', '.') . "</td>";
            echo "<td>" . esc($p['status_direktur']) . "</td>";
            echo "</tr>";
        }

        echo "</table>";
        exit;
    }

    private function compressAndUploadImage($file)
    {
        if (!$file || !$file->isValid()) {
            return null;
        }

        $ext = strtolower($file->getClientExtension());
        $newName = $file->getRandomName();
        $targetDirPublic = ROOTPATH . 'public/uploads/pembelian/';
        $targetDirRoot = ROOTPATH . 'uploads/pembelian/';

        if (!is_dir($targetDirPublic)) {
            @mkdir($targetDirPublic, 0777, true);
        }
        if (!is_dir($targetDirRoot)) {
            @mkdir($targetDirRoot, 0777, true);
        }

        $destinationPath = $targetDirPublic . $newName;

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $tempPath = $file->getTempName();

            switch ($ext) {
                case 'jpg':
                case 'jpeg':
                    $image = @imagecreatefromjpeg($tempPath);
                    break;
                case 'png':
                    $image = @imagecreatefrompng($tempPath);
                    break;
                case 'webp':
                    $image = @imagecreatefromwebp($tempPath);
                    break;
                default:
                    $image = false;
            }

            if ($image) {
                $width = imagesx($image);
                $height = imagesy($image);
                $maxDim = 1200;

                if ($width > $maxDim || $height > $maxDim) {
                    $ratio = min($maxDim / $width, $maxDim / $height);
                    $newWidth = (int)($width * $ratio);
                    $newHeight = (int)($height * $ratio);

                    $newImage = imagecreatetruecolor($newWidth, $newHeight);
                    if ($ext === 'png' || $ext === 'webp') {
                        imagealphablending($newImage, false);
                        imagesavealpha($newImage, true);
                    }
                    imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                    imagedestroy($image);
                    $image = $newImage;
                }

                imagejpeg($image, $destinationPath, 75);
                @copy($destinationPath, $targetDirRoot . $newName);
                imagedestroy($image);

                return $newName;
            }
        }

        // Fallback for PDF or original move
        $file->move($targetDirPublic, $newName);
        @copy($targetDirPublic . $newName, $targetDirRoot . $newName);

        return $newName;
    }

    public function update()
    {
        return $this->simpan();
    }

    private function parseNominal($val)
    {
        if (empty($val)) return 0;
        $str = (string)$val;
        $str = preg_replace('/[,.]00$/', '', $str);
        $clean = preg_replace('/[^0-9]/', '', $str);
        return floatval($clean);
    }
}
