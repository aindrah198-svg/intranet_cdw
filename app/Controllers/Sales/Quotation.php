<?php

namespace App\Controllers\Sales;

class Quotation extends BaseSalesController
{
    public function index()
    {
        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status');

        $builder = $this->db->table('sales_quotation')->where('deleted_at IS NULL');
        if ($search) {
            $builder->groupStart()
                ->like('nomor_quotation', $search)
                ->orLike('nama_klien', $search)
                ->orLike('perusahaan', $search)
            ->groupEnd();
        }
        if ($status) {
            $builder->where('status', $status);
        }

        $quotations = $builder->orderBy('id', 'DESC')->get()->getResultArray();

        $data = [
            'title' => 'Riwayat Quotation',
            'active' => 'quotation',
            'quotations' => $quotations,
            'search' => $search,
            'status' => $status
        ];

        return view('sales/templates/header', $data)
             . view('sales/templates/sidebar', $data)
             . view('sales/quotation/index', $data)
             . view('sales/templates/footer', $data);
    }

    public function create()
    {
        $leads = $this->db->table('sales_leads')->where('deleted_at IS NULL')->orderBy('nama_lead', 'ASC')->get()->getResultArray();
        $kliens = $this->db->table('sales_klien')->where('deleted_at IS NULL')->orderBy('nama_klien', 'ASC')->get()->getResultArray();

        $data = [
            'title' => 'Buat Quotation Baru',
            'active' => 'quotation-create',
            'leads' => $leads,
            'kliens' => $kliens
        ];

        return view('sales/templates/header', $data)
             . view('sales/templates/sidebar', $data)
             . view('sales/quotation/create', $data)
             . view('sales/templates/footer', $data);
    }

    public function store()
    {
        $nomorQuotation = 'QUO/' . date('Ym') . '/' . rand(1000, 9999);
        $subtotal = (float)$this->request->getPost('subtotal');
        $diskon = (float)$this->request->getPost('diskon');
        $ppn = (float)$this->request->getPost('ppn');
        $total = $subtotal - $diskon + $ppn;

        $insertData = [
            'nomor_quotation' => $nomorQuotation,
            'lead_id' => $this->request->getPost('lead_id') ?: null,
            'klien_id' => $this->request->getPost('klien_id') ?: null,
            'nama_klien' => $this->request->getPost('nama_klien'),
            'perusahaan' => $this->request->getPost('perusahaan'),
            'tanggal_quotation' => $this->request->getPost('tanggal_quotation') ?: date('Y-m-d'),
            'berlaku_hingga' => $this->request->getPost('berlaku_hingga') ?: date('Y-m-d', strtotime('+30 days')),
            'subtotal' => $subtotal,
            'diskon' => $diskon,
            'ppn' => $ppn,
            'total' => $total,
            'status' => 'Draft',
            'versi' => 1,
            'catatan' => $this->request->getPost('catatan'),
            'created_by' => session()->get('user_id'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->table('sales_quotation')->insert($insertData);
        $quotationId = $this->db->insertID();

        // Save items
        $deskripsiArr = $this->request->getPost('item_deskripsi') ?? [];
        $qtyArr = $this->request->getPost('item_qty') ?? [];
        $hargaArr = $this->request->getPost('item_harga') ?? [];

        foreach ($deskripsiArr as $idx => $desc) {
            if (!empty($desc)) {
                $qty = (int)($qtyArr[$idx] ?? 1);
                $harga = (float)($hargaArr[$idx] ?? 0);
                $this->db->table('sales_quotation_item')->insert([
                    'quotation_id' => $quotationId,
                    'deskripsi' => $desc,
                    'qty' => $qty,
                    'harga_satuan' => $harga,
                    'total_harga' => $qty * $harga,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        return redirect()->to(site_url('sales/quotation'))->with('success', 'Quotation berhasil dibuat dengan nomor ' . $nomorQuotation);
    }

    public function detail($id)
    {
        $quotation = $this->db->table('sales_quotation')->where('id', $id)->where('deleted_at IS NULL')->get()->getRowArray();
        if (!$quotation) {
            return redirect()->to(site_url('sales/quotation'))->with('error', 'Quotation tidak ditemukan');
        }

        $items = $this->db->table('sales_quotation_item')->where('quotation_id', $id)->get()->getResultArray();

        $data = [
            'title' => 'Detail Quotation ' . $quotation['nomor_quotation'],
            'active' => 'quotation',
            'quotation' => $quotation,
            'items' => $items
        ];

        return view('sales/templates/header', $data)
             . view('sales/templates/sidebar', $data)
             . view('sales/quotation/detail', $data)
             . view('sales/templates/footer', $data);
    }

    public function delete($id)
    {
        $this->db->table('sales_quotation')->where('id', $id)->update(['deleted_at' => date('Y-m-d H:i:s')]);
        return redirect()->to(site_url('sales/quotation'))->with('success', 'Quotation berhasil dihapus');
    }
}
