<?php

namespace App\Controllers\Sales;

class Kontak extends BaseSalesController
{
    public function index()
    {
        $search = $this->request->getGet('search');

        $builder = $this->db->table('sales_klien')->where('deleted_at IS NULL');
        if ($search) {
            $builder->groupStart()
                ->like('nama_klien', $search)
                ->orLike('perusahaan', $search)
                ->orLike('email', $search)
                ->orLike('telepon', $search)
            ->groupEnd();
        }

        $kliens = $builder->orderBy('id', 'DESC')->get()->getResultArray();

        $data = [
            'title' => 'Kontak Klien',
            'active' => 'kontak',
            'kliens' => $kliens,
            'search' => $search
        ];

        return view('sales/templates/header', $data)
             . view('sales/templates/sidebar', $data)
             . view('sales/kontak/index', $data)
             . view('sales/templates/footer', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Kontak Klien',
            'active' => 'kontak'
        ];

        return view('sales/templates/header', $data)
             . view('sales/templates/sidebar', $data)
             . view('sales/kontak/create', $data)
             . view('sales/templates/footer', $data);
    }

    public function store()
    {
        $kodeKlien = 'KLN-' . date('Ym') . '-' . rand(100, 999);
        $insertData = [
            'kode_klien' => $kodeKlien,
            'nama_klien' => $this->request->getPost('nama_klien'),
            'perusahaan' => $this->request->getPost('perusahaan'),
            'email' => $this->request->getPost('email'),
            'telepon' => $this->request->getPost('telepon'),
            'alamat' => $this->request->getPost('alamat'),
            'industri' => $this->request->getPost('industri'),
            'status' => $this->request->getPost('status') ?? 'Aktif',
            'created_by' => session()->get('user_id'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->table('sales_klien')->insert($insertData);
        return redirect()->to(site_url('sales/kontak'))->with('success', 'Kontak klien berhasil disimpan');
    }

    public function detail($id)
    {
        $klien = $this->db->table('sales_klien')->where('id', $id)->where('deleted_at IS NULL')->get()->getRowArray();
        if (!$klien) {
            return redirect()->to(site_url('sales/kontak'))->with('error', 'Kontak klien tidak ditemukan');
        }

        $interaksi = $this->db->table('sales_klien_interaksi')->where('klien_id', $id)->orderBy('tanggal', 'DESC')->get()->getResultArray();
        $deals = $this->db->table('sales_deal')->where('klien_id', $id)->where('deleted_at IS NULL')->get()->getResultArray();

        $data = [
            'title' => 'Detail Kontak Klien - ' . $klien['nama_klien'],
            'active' => 'kontak',
            'klien' => $klien,
            'interaksi' => $interaksi,
            'deals' => $deals
        ];

        return view('sales/templates/header', $data)
             . view('sales/templates/sidebar', $data)
             . view('sales/kontak/detail', $data)
             . view('sales/templates/footer', $data);
    }

    public function edit($id)
    {
        $klien = $this->db->table('sales_klien')->where('id', $id)->where('deleted_at IS NULL')->get()->getRowArray();
        if (!$klien) {
            return redirect()->to(site_url('sales/kontak'))->with('error', 'Kontak klien tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Kontak Klien',
            'active' => 'kontak',
            'klien' => $klien
        ];

        return view('sales/templates/header', $data)
             . view('sales/templates/sidebar', $data)
             . view('sales/kontak/edit', $data)
             . view('sales/templates/footer', $data);
    }

    public function update($id)
    {
        $updateData = [
            'nama_klien' => $this->request->getPost('nama_klien'),
            'perusahaan' => $this->request->getPost('perusahaan'),
            'email' => $this->request->getPost('email'),
            'telepon' => $this->request->getPost('telepon'),
            'alamat' => $this->request->getPost('alamat'),
            'industri' => $this->request->getPost('industri'),
            'status' => $this->request->getPost('status'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->table('sales_klien')->where('id', $id)->update($updateData);
        return redirect()->to(site_url('sales/kontak'))->with('success', 'Kontak klien berhasil diperbarui');
    }

    public function delete($id)
    {
        $this->db->table('sales_klien')->where('id', $id)->update(['deleted_at' => date('Y-m-d H:i:s')]);
        return redirect()->to(site_url('sales/kontak'))->with('success', 'Kontak klien berhasil dihapus');
    }

    public function storeInteraksi()
    {
        $klienId = $this->request->getPost('klien_id');
        $insertData = [
            'klien_id' => $klienId,
            'tanggal' => $this->request->getPost('tanggal') ?: date('Y-m-d H:i:s'),
            'jenis_interaksi' => $this->request->getPost('jenis_interaksi'),
            'ringkasan' => $this->request->getPost('ringkasan'),
            'follow_up_note' => $this->request->getPost('follow_up_note'),
            'created_by' => session()->get('user_id'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->table('sales_klien_interaksi')->insert($insertData);
        return redirect()->to(site_url('sales/kontak/detail/' . $klienId))->with('success', 'Riwayat interaksi berhasil ditambahkan');
    }
}
