<?php

namespace App\Controllers\Sales;

class Deal extends BaseSalesController
{
    public function index()
    {
        $deals = $this->db->table('sales_deal')
            ->where('deleted_at IS NULL')
            ->orderBy('id', 'DESC')
            ->get()->getResultArray();

        $data = [
            'title' => 'Closing Deal & Integrasi',
            'active' => 'deal',
            'deals' => $deals
        ];

        return view('sales/templates/header', $data)
             . view('sales/templates/sidebar', $data)
             . view('sales/deal/index', $data)
             . view('sales/templates/footer', $data);
    }

    public function create()
    {
        $leads = $this->db->table('sales_leads')->where('deleted_at IS NULL')->orderBy('nama_lead', 'ASC')->get()->getResultArray();
        $quotations = $this->db->table('sales_quotation')->where('deleted_at IS NULL')->orderBy('nomor_quotation', 'DESC')->get()->getResultArray();
        $kliens = $this->db->table('sales_klien')->where('deleted_at IS NULL')->orderBy('nama_klien', 'ASC')->get()->getResultArray();

        $data = [
            'title' => 'Catat Closing Deal Baru',
            'active' => 'deal',
            'leads' => $leads,
            'quotations' => $quotations,
            'kliens' => $kliens
        ];

        return view('sales/templates/header', $data)
             . view('sales/templates/sidebar', $data)
             . view('sales/deal/create', $data)
             . view('sales/templates/footer', $data);
    }

    public function store()
    {
        $kodeDeal = 'DEAL-' . date('Ymd') . '-' . rand(100, 999);
        $insertData = [
            'kode_deal' => $kodeDeal,
            'lead_id' => $this->request->getPost('lead_id') ?: null,
            'quotation_id' => $this->request->getPost('quotation_id') ?: null,
            'klien_id' => $this->request->getPost('klien_id') ?: null,
            'nama_deal' => $this->request->getPost('nama_deal'),
            'perusahaan' => $this->request->getPost('perusahaan'),
            'nilai_deal' => (float)$this->request->getPost('nilai_deal'),
            'tanggal_closing' => $this->request->getPost('tanggal_closing') ?: date('Y-m-d'),
            'status_invoice' => 'Belum',
            'status_project' => 'Belum',
            'catatan' => $this->request->getPost('catatan'),
            'created_by' => session()->get('user_id'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->table('sales_deal')->insert($insertData);
        $dealId = $this->db->insertID();

        // Update Lead status to Closing if lead_id exists
        if ($insertData['lead_id']) {
            $this->db->table('sales_leads')->where('id', $insertData['lead_id'])->update([
                'status' => 'Closing',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        return redirect()->to(site_url('sales/deal'))->with('success', 'Closing Deal berhasil dicatat. Anda sekarang dapat menyeberangkan data ini ke Invoice / Project.');
    }

    /**
     * Trigger semi-otomatis: Deal -> Draft Invoice di Accounting
     */
    public function createInvoice($id)
    {
        $deal = $this->db->table('sales_deal')->where('id', $id)->where('deleted_at IS NULL')->get()->getRowArray();
        if (!$deal) {
            return redirect()->to(site_url('sales/deal'))->with('error', 'Data deal tidak ditemukan');
        }

        // Cek jika tabel faktur_pajak / invoice ada
        $invoiceNo = 'INV/SALES/' . date('Ym') . '/' . rand(1000, 9999);
        $ppnNilai = $deal['nilai_deal'] * 0.11;

        if ($this->db->tableExists('faktur_pajak')) {
            $this->db->table('faktur_pajak')->insert([
                'nomor_faktur' => $invoiceNo,
                'tanggal_faktur' => date('Y-m-d'),
                'jenis_faktur' => 'Keluaran',
                'nama_pengusaha' => $deal['perusahaan'] ?? $deal['nama_deal'],
                'nilai_transaksi' => $deal['nilai_deal'],
                'nilai_ppn' => $ppnNilai,
                'tarif_ppn' => 11.00,
                'status_approval' => 'Draft',
                'status_lapor' => 'Belum',
                'masa_pajak' => (int)date('m'),
                'tahun_pajak' => (int)date('Y'),
                'keterangan' => 'Draft Invoice dari Closing Deal: ' . $deal['kode_deal'] . ' - ' . $deal['nama_deal'],
                'created_by' => session()->get('user_id'),
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $invoiceId = $this->db->insertID();

            // Update Deal status
            $this->db->table('sales_deal')->where('id', $id)->update([
                'status_invoice' => 'Draft',
                'invoice_id' => $invoiceId,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return redirect()->to(site_url('sales/deal'))->with('success', 'Draft Invoice (' . $invoiceNo . ') berhasil dikirim ke Accounting untuk dikonfirmasi.');
        }

        return redirect()->to(site_url('sales/deal'))->with('info', 'Model Faktur/Invoice disiapkan');
    }

    /**
     * Trigger semi-otomatis: Deal -> Draft Project di Direktur / Monitoring Project
     */
    public function createProject($id)
    {
        $deal = $this->db->table('sales_deal')->where('id', $id)->where('deleted_at IS NULL')->get()->getRowArray();
        if (!$deal) {
            return redirect()->to(site_url('sales/deal'))->with('error', 'Data deal tidak ditemukan');
        }

        $kodeProject = 'PRJ-' . date('Ymd') . '-' . rand(100, 999);

        if ($this->db->tableExists('projects')) {
            $this->db->table('projects')->insert([
                'kode_project' => $kodeProject,
                'nama_project' => $deal['nama_deal'],
                'klien' => $deal['perusahaan'] ?? $deal['nama_deal'],
                'nilai_project' => $deal['nilai_deal'],
                'tanggal_mulai' => date('Y-m-d'),
                'status' => 'Perencanaan',
                'created_by' => session()->get('user_id'),
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $projectId = $this->db->insertID();

            // Update Deal status
            $this->db->table('sales_deal')->where('id', $id)->update([
                'status_project' => 'Draft',
                'project_id' => $projectId,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return redirect()->to(site_url('sales/deal'))->with('success', 'Draft Project (' . $kodeProject . ') berhasil disalurkan ke Manajemen Project Direktur.');
        }

        return redirect()->to(site_url('sales/deal'))->with('info', 'Modul Project disiapkan');
    }

    public function delete($id)
    {
        $this->db->table('sales_deal')->where('id', $id)->update(['deleted_at' => date('Y-m-d H:i:s')]);
        return redirect()->to(site_url('sales/deal'))->with('success', 'Data deal berhasil dihapus');
    }
}
