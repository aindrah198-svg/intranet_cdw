<?php

namespace App\Controllers\Teknisi;

class Gudang extends TeknisiController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        $totalStok = 0;
        $totalPinjam = $db->table('peralatan_dipinjam')->where('status', 'Dipinjam')->countAllResults();
        $totalPerawatan = $db->table('perawatan_alat')->where('status', 'Dijadwalkan')->countAllResults();

        $peralatanPinjamList = $db->table('peralatan_dipinjam')
            ->orderBy('id', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        $data = [
            'title' => 'Dashboard Gudang & Inventory',
            'active' => 'gudang',
            'totalStok' => $totalStok,
            'totalPinjam' => $totalPinjam,
            'totalPerawatan' => $totalPerawatan,
            'peralatanPinjamList' => $peralatanPinjamList
        ];

        return $this->renderView('teknisi/gudang/index', $data);
    }

    public function penyimpanan()
    {
        $db = \Config\Database::connect();
        $items = [];
        if ($db->tableExists('barang')) {
            $items = $db->table('barang')->get()->getResultArray();
        } elseif ($db->tableExists('gudang_stok')) {
            $items = $db->table('gudang_stok')->get()->getResultArray();
        }

        $data = [
            'title' => 'Penyimpanan & Stok Gudang',
            'active' => 'gudang-penyimpanan',
            'items' => $items
        ];

        return $this->renderView('teknisi/gudang/penyimpanan', $data);
    }

    public function peralatanDipinjam()
    {
        $db = \Config\Database::connect();
        
        $list = $db->table('peralatan_dipinjam')
            ->orderBy('id', 'DESC')
            ->get()->getResultArray();

        $data = [
            'title' => 'Peralatan Dipinjam',
            'active' => 'gudang-peralatan-dipinjam',
            'list' => $list
        ];

        return $this->renderView('teknisi/gudang/peralatan_dipinjam', $data);
    }

    public function pinjamAlat()
    {
        $db = \Config\Database::connect();
        $kode = 'PINJAM-' . date('Ymd') . '-' . rand(100, 999);
        
        $teknisiId = $this->karyawanData['id'] ?? $this->userId;
        $namaTeknisi = $this->karyawanData['nama_lengkap'] ?? $this->userData['name'] ?? 'Teknisi';

        $db->table('peralatan_dipinjam')->insert([
            'kode_peminjaman' => $kode,
            'teknisi_id' => $teknisiId,
            'nama_teknisi' => $namaTeknisi,
            'nama_alat' => $this->request->getPost('nama_alat'),
            'kode_alat' => $this->request->getPost('kode_alat') ?: 'ALT-' . rand(100, 999),
            'qty' => (int)$this->request->getPost('qty') ?: 1,
            'tgl_pinjam' => $this->request->getPost('tgl_pinjam') ?: date('Y-m-d'),
            'tgl_kembali_rencana' => $this->request->getPost('tgl_kembali_rencana') ?: date('Y-m-d', strtotime('+7 days')),
            'kondisi_pinjam' => $this->request->getPost('kondisi_pinjam') ?? 'Baik',
            'status' => 'Dipinjam',
            'nama_proyek' => $this->request->getPost('nama_proyek'),
            'catatan' => $this->request->getPost('catatan'),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(site_url('teknisi/gudang/peralatan-dipinjam'))->with('success', 'Peminjaman alat berhasil dicatat.');
    }

    public function kembalikanAlat($id)
    {
        $db = \Config\Database::connect();
        $kondisiKembali = $this->request->getPost('kondisi_kembali') ?? 'Baik';

        $db->table('peralatan_dipinjam')->where('id', $id)->update([
            'status' => 'Dikembalikan',
            'tgl_kembali_realisasi' => date('Y-m-d'),
            'kondisi_kembali' => $kondisiKembali,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(site_url('teknisi/gudang/peralatan-dipinjam'))->with('success', 'Pengembalian peralatan telah dikonfirmasi.');
    }

    public function perawatanAlat()
    {
        $db = \Config\Database::connect();
        $list = $db->table('perawatan_alat')->orderBy('id', 'DESC')->get()->getResultArray();

        $data = [
            'title' => 'Perawatan & Maintenance Alat Gudang',
            'active' => 'gudang-perawatan-alat',
            'list' => $list
        ];

        return $this->renderView('teknisi/gudang/perawatan_alat', $data);
    }
}
