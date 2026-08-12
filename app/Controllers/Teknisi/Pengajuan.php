<?php

namespace App\Controllers\Teknisi;

class Pengajuan extends TeknisiController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $teknisiId = $this->karyawanData['id'] ?? $this->userId;

        $biayaList = $db->table('biaya_lapangan')->where('teknisi_id', $teknisiId)->orderBy('id', 'DESC')->get()->getResultArray();
        
        $cutiList = [];
        if ($db->tableExists('cuti')) {
            $cutiList = $db->table('cuti')->where('karyawan_id', $teknisiId)->orderBy('id', 'DESC')->get()->getResultArray();
        }

        $data = [
            'title' => 'Semua Pengajuan Staf Teknisi',
            'active' => 'pengajuan',
            'biayaList' => $biayaList,
            'cutiList' => $cutiList
        ];

        return $this->renderView('teknisi/pengajuan/index', $data);
    }

    public function permintaanPembelian()
    {
        $data = [
            'title' => 'Pengajuan Permintaan Pembelian Sparepart / Material',
            'active' => 'pengajuan-pembelian'
        ];

        return $this->renderView('teknisi/pengajuan/permintaan_pembelian', $data);
    }

    public function biayaLapangan()
    {
        $db = \Config\Database::connect();
        $teknisiId = $this->karyawanData['id'] ?? $this->userId;

        $list = $db->table('biaya_lapangan')
            ->where('teknisi_id', $teknisiId)
            ->orderBy('id', 'DESC')
            ->get()->getResultArray();

        $data = [
            'title' => 'Pengajuan Biaya Lapangan (Reimbursement)',
            'active' => 'pengajuan-biaya-lapangan',
            'list' => $list
        ];

        return $this->renderView('teknisi/pengajuan/biaya_lapangan', $data);
    }

    public function storeBiayaLapangan()
    {
        $db = \Config\Database::connect();
        $teknisiId = $this->karyawanData['id'] ?? $this->userId;
        $kode = 'REIM-' . date('Ymd') . '-' . rand(100, 999);

        $db->table('biaya_lapangan')->insert([
            'kode_pengajuan' => $kode,
            'teknisi_id' => $teknisiId,
            'nama_proyek' => $this->request->getPost('nama_proyek'),
            'tgl_pengajuan' => $this->request->getPost('tgl_pengajuan') ?: date('Y-m-d'),
            'kategori_biaya' => $this->request->getPost('kategori_biaya') ?? 'Transport',
            'nominal' => (float)$this->request->getPost('nominal'),
            'keterangan' => $this->request->getPost('keterangan'),
            'status' => 'Pending',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(site_url('teknisi/pengajuan/biaya-lapangan'))->with('success', 'Pengajuan reimbursement biaya lapangan berhasil dikirim ke Accounting.');
    }

    public function cuti()
    {
        $db = \Config\Database::connect();
        $teknisiId = $this->karyawanData['id'] ?? $this->userId;

        $cutiList = [];
        if ($db->tableExists('cuti')) {
            $cutiList = $db->table('cuti')->where('karyawan_id', $teknisiId)->orderBy('id', 'DESC')->get()->getResultArray();
        }

        $data = [
            'title' => 'Form Pengajuan Cuti / Izin',
            'active' => 'pengajuan-cuti',
            'cutiList' => $cutiList
        ];

        return $this->renderView('teknisi/pengajuan/cuti', $data);
    }
}
