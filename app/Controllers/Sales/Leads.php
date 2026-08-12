<?php

namespace App\Controllers\Sales;

class Leads extends BaseSalesController
{
    public function index()
    {
        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status');

        $builder = $this->db->table('sales_leads')->where('deleted_at IS NULL');
        if ($search) {
            $builder->groupStart()
                ->like('nama_lead', $search)
                ->orLike('perusahaan', $search)
                ->orLike('email', $search)
                ->orLike('telepon', $search)
            ->groupEnd();
        }
        if ($status) {
            $builder->where('status', $status);
        }

        $leads = $builder->orderBy('id', 'DESC')->get()->getResultArray();

        $data = [
            'title' => 'Daftar Leads',
            'active' => 'leads',
            'leads' => $leads,
            'search' => $search,
            'status' => $status
        ];

        return view('sales/templates/header', $data)
             . view('sales/templates/sidebar', $data)
             . view('sales/leads/index', $data)
             . view('sales/templates/footer', $data);
    }

    public function pipeline()
    {
        $statuses = ['Baru', 'Follow Up', 'Negosiasi', 'Closing', 'Hilang'];
        $pipeline = [];

        foreach ($statuses as $st) {
            $pipeline[$st] = $this->db->table('sales_leads')
                ->where('deleted_at IS NULL')
                ->where('status', $st)
                ->orderBy('id', 'DESC')
                ->get()->getResultArray();
        }

        $data = [
            'title' => 'Pipeline (Kanban)',
            'active' => 'pipeline',
            'pipeline' => $pipeline,
            'statuses' => $statuses
        ];

        return view('sales/templates/header', $data)
             . view('sales/templates/sidebar', $data)
             . view('sales/leads/pipeline', $data)
             . view('sales/templates/footer', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Lead Baru',
            'active' => 'leads'
        ];

        return view('sales/templates/header', $data)
             . view('sales/templates/sidebar', $data)
             . view('sales/leads/create', $data)
             . view('sales/templates/footer', $data);
    }

    public function store()
    {
        $kodeLead = 'LD-' . date('Ymd') . '-' . rand(100, 999);
        $insertData = [
            'kode_lead' => $kodeLead,
            'nama_lead' => $this->request->getPost('nama_lead'),
            'perusahaan' => $this->request->getPost('perusahaan'),
            'email' => $this->request->getPost('email'),
            'telepon' => $this->request->getPost('telepon'),
            'sumber_lead' => $this->request->getPost('sumber_lead') ?? 'Website',
            'nilai_potensi' => $this->request->getPost('nilai_potensi') ?? 0,
            'status' => $this->request->getPost('status') ?? 'Baru',
            'tgl_follow_up' => $this->request->getPost('tgl_follow_up') ?: null,
            'catatan' => $this->request->getPost('catatan'),
            'created_by' => session()->get('user_id'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->table('sales_leads')->insert($insertData);
        return redirect()->to(site_url('sales/leads'))->with('success', 'Data lead berhasil ditambahkan');
    }

    public function edit($id)
    {
        $lead = $this->db->table('sales_leads')->where('id', $id)->where('deleted_at IS NULL')->get()->getRowArray();
        if (!$lead) {
            return redirect()->to(site_url('sales/leads'))->with('error', 'Data lead tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Lead',
            'active' => 'leads',
            'lead' => $lead
        ];

        return view('sales/templates/header', $data)
             . view('sales/templates/sidebar', $data)
             . view('sales/leads/edit', $data)
             . view('sales/templates/footer', $data);
    }

    public function update($id)
    {
        $updateData = [
            'nama_lead' => $this->request->getPost('nama_lead'),
            'perusahaan' => $this->request->getPost('perusahaan'),
            'email' => $this->request->getPost('email'),
            'telepon' => $this->request->getPost('telepon'),
            'sumber_lead' => $this->request->getPost('sumber_lead'),
            'nilai_potensi' => $this->request->getPost('nilai_potensi'),
            'status' => $this->request->getPost('status'),
            'tgl_follow_up' => $this->request->getPost('tgl_follow_up') ?: null,
            'catatan' => $this->request->getPost('catatan'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->table('sales_leads')->where('id', $id)->update($updateData);
        return redirect()->to(site_url('sales/leads'))->with('success', 'Data lead berhasil diperbarui');
    }

    public function delete($id)
    {
        $this->db->table('sales_leads')->where('id', $id)->update(['deleted_at' => date('Y-m-d H:i:s')]);
        return redirect()->to(site_url('sales/leads'))->with('success', 'Data lead berhasil dihapus');
    }

    public function updateStatus()
    {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');

        if ($id && $status) {
            $this->db->table('sales_leads')->where('id', $id)->update([
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            return $this->response->setJSON(['status' => 'success']);
        }
        return $this->response->setJSON(['status' => 'error'], 400);
    }
}
