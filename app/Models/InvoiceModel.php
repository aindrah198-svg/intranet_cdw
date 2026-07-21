<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceModel extends Model
{
    protected $table = 'invoice';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false; // Tidak ada soft delete di tabel invoice
    protected $allowedFields = [
        'nomor_invoice', 'project_id', 'penawaran_id', 'tanggal_invoice',
        'tanggal_jatuh_tempo', 'status_pembayaran', 'metode_pembayaran',
        'keterangan', 'created_by'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    // Validation rules
    protected $validationRules = [
        'nomor_invoice' => 'required|is_unique[invoice.nomor_invoice,id,{id}]',
        'project_id' => 'required|integer',
        'tanggal_invoice' => 'required|valid_date',
        'tanggal_jatuh_tempo' => 'required|valid_date',
        'status_pembayaran' => 'required|in_list[belum_bayar,sebagian,lunas,overdue]',
        'created_by' => 'required|integer'
    ];
    
    protected $validationMessages = [];
    protected $skipValidation = false;
    
    /**
     * Generate nomor invoice
     */
    public function generateNomorInvoice()
    {
        $year = date('Y');
        $month = date('m');
        
        // Cari invoice terakhir bulan ini
        $lastInvoice = $this->select('nomor_invoice')
            ->where('YEAR(created_at)', $year)
            ->where('MONTH(created_at)', $month)
            ->orderBy('id', 'DESC')
            ->first();
        
        if ($lastInvoice) {
            $lastNumber = explode('/', $lastInvoice['nomor_invoice']);
            $sequence = intval($lastNumber[0]) + 1;
        } else {
            $sequence = 1;
        }
        
        $sequenceFormatted = str_pad($sequence, 3, '0', STR_PAD_LEFT);
        
        return sprintf('%03d/INV/CDW/%s/%s', 
            $sequenceFormatted, 
            date('m'), 
            date('Y')
        );
    }
    
    /**
     * Get invoice with details
     */
    public function getInvoiceWithDetails($id)
    {
        $builder = $this->db->table('invoice i');
        $builder->select('i.*, 
            p.nama_project, p.kode_project, p.nilai_project,
            c.nama_perusahaan, c.alamat as alamat_client, c.telepon, c.email, c.nama_kontak,
            u.name as created_by_name,
            pen.nomor_penawaran')
            ->join('project p', 'p.id = i.project_id')
            ->join('client c', 'c.id = p.client_id')
            ->join('users u', 'u.id = i.created_by', 'left')
            ->join('penawaran pen', 'pen.id = i.penawaran_id', 'left')
            ->where('i.id', $id);
        
        return $builder->get()->getRowArray();
    }
    
    /**
     * Get invoices by sales
     */
    public function getInvoiceBySales($salesId)
    {
        $builder = $this->db->table('invoice i');
        $builder->select('i.*, 
            p.nama_project, p.kode_project,
            c.nama_perusahaan,
            u.name as created_by_name')
            ->join('project p', 'p.id = i.project_id')
            ->join('client c', 'c.id = p.client_id')
            ->join('users u', 'u.id = i.created_by', 'left')
            ->join('penawaran pen', 'pen.id = i.penawaran_id', 'left')
            ->where('c.sales_id', $salesId)
            ->orderBy('i.tanggal_invoice', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get invoice items
     */
    public function getInvoiceItems($invoiceId)
    {
        $builder = $this->db->table('invoice_item');
        $builder->where('invoice_id', $invoiceId);
        return $builder->get()->getResultArray();
    }
    
    /**
     * Calculate invoice total
     */
    public function calculateTotal($invoiceId)
    {
        $builder = $this->db->table('invoice_item');
        $builder->selectSum('subtotal');
        $builder->where('invoice_id', $invoiceId);
        $result = $builder->get()->getRowArray();
        
        return $result ? floatval($result['subtotal']) : 0;
    }
    
    /**
     * Get project options for invoice
     */
    public function getProjectOptions($salesId = null)
    {
        $builder = $this->db->table('project p');
        $builder->select('p.id, p.kode_project, p.nama_project, 
            c.nama_perusahaan, p.status, p.nilai_project')
            ->join('client c', 'c.id = p.client_id')
            ->where('p.status', 'deal'); // Hanya project yang sudah deal
        
        if ($salesId) {
            $builder->where('c.sales_id', $salesId);
        }
        
        $builder->orderBy('p.nama_project', 'ASC');
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get penawaran options for invoice
     */
    public function getPenawaranOptions($projectId = null)
    {
        $builder = $this->db->table('penawaran p');
        $builder->select('p.id, p.nomor_penawaran, p.project_id, pr.nama_project')
            ->join('project pr', 'pr.id = p.project_id')
            ->where('p.status', 'diterima'); // Hanya penawaran yang diterima
        
        if ($projectId) {
            $builder->where('p.project_id', $projectId);
        }
        
        $builder->orderBy('p.tanggal_penawaran', 'DESC');
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get payment summary for invoice
     */
    public function getPaymentSummary($invoiceId)
    {
        $builder = $this->db->table('pembayaran');
        $builder->select('SUM(jumlah_bayar) as total_bayar');
        $builder->where('invoice_id', $invoiceId);
        $result = $builder->get()->getRowArray();
        
        return $result ? floatval($result['total_bayar']) : 0;
    }
    
    /**
     * Get invoice status count
     */
    public function getStatusCount($salesId = null)
    {
        $builder = $this->db->table('invoice i');
        $builder->select('i.status_pembayaran, COUNT(*) as count')
            ->join('project p', 'p.id = i.project_id')
            ->join('client c', 'c.id = p.client_id');
        
        if ($salesId) {
            $builder->where('c.sales_id', $salesId);
        }
        
        $builder->groupBy('i.status_pembayaran');
        
        $results = $builder->get()->getResultArray();
        
        $statusCount = [
            'belum_bayar' => 0,
            'sebagian' => 0,
            'lunas' => 0,
            'overdue' => 0
        ];
        
        foreach ($results as $row) {
            $statusCount[$row['status_pembayaran']] = intval($row['count']);
        }
        
        return $statusCount;
    }
    
    /**
     * Get all invoices for export
     */
    public function getAllInvoices($salesId = null)
    {
        $builder = $this->db->table('invoice i');
        $builder->select('i.*, 
            p.nama_project, p.kode_project,
            c.nama_perusahaan,
            u.name as created_by_name')
            ->join('project p', 'p.id = i.project_id')
            ->join('client c', 'c.id = p.client_id')
            ->join('users u', 'u.id = i.created_by', 'left');
        
        if ($salesId) {
            $builder->where('c.sales_id', $salesId);
        }
        
        $builder->orderBy('i.tanggal_invoice', 'DESC');
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get overdue invoices
     */
    public function getOverdueInvoices($salesId = null)
    {
        $builder = $this->db->table('invoice i');
        $builder->select('i.*, 
            p.nama_project,
            c.nama_perusahaan,
            DATEDIFF(CURDATE(), i.tanggal_jatuh_tempo) as days_overdue')
            ->join('project p', 'p.id = i.project_id')
            ->join('client c', 'c.id = p.client_id')
            ->where('i.status_pembayaran !=', 'lunas')
            ->where('i.tanggal_jatuh_tempo <', date('Y-m-d'));
        
        if ($salesId) {
            $builder->where('c.sales_id', $salesId);
        }
        
        $builder->orderBy('i.tanggal_jatuh_tempo', 'ASC');
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get invoice statistics
     */
    public function getInvoiceStatistics($salesId = null)
    {
        $builder = $this->db->table('invoice i');
        $builder->select('
            COUNT(*) as total_invoices,
            SUM(CASE WHEN i.status_pembayaran = "lunas" THEN 1 ELSE 0 END) as paid_invoices,
            SUM(CASE WHEN i.status_pembayaran = "belum_bayar" THEN 1 ELSE 0 END) as unpaid_invoices,
            SUM(CASE WHEN i.status_pembayaran = "sebagian" THEN 1 ELSE 0 END) as partial_invoices,
            SUM(CASE WHEN i.status_pembayaran = "overdue" THEN 1 ELSE 0 END) as overdue_invoices
        ')
        ->join('project p', 'p.id = i.project_id')
        ->join('client c', 'c.id = p.client_id');
        
        if ($salesId) {
            $builder->where('c.sales_id', $salesId);
        }
        
        return $builder->get()->getRowArray();
    }
    
    /**
     * Get monthly invoice summary
     */
    public function getMonthlySummary($year = null, $salesId = null)
    {
        if (!$year) {
            $year = date('Y');
        }
        
        $builder = $this->db->table('invoice i');
        $builder->select('
            MONTH(i.tanggal_invoice) as month,
            COUNT(*) as invoice_count,
            SUM(ii.subtotal) as total_amount
        ')
        ->join('project p', 'p.id = i.project_id')
        ->join('client c', 'c.id = p.client_id')
        ->join('invoice_item ii', 'ii.invoice_id = i.id', 'left')
        ->where('YEAR(i.tanggal_invoice)', $year);
        
        if ($salesId) {
            $builder->where('c.sales_id', $salesId);
        }
        
        $builder->groupBy('MONTH(i.tanggal_invoice)')
                ->orderBy('month', 'ASC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get top clients by invoice amount
     */
    public function getTopClients($limit = 5, $salesId = null)
    {
        $builder = $this->db->table('invoice i');
        $builder->select('
            c.nama_perusahaan,
            COUNT(i.id) as invoice_count,
            SUM(ii.subtotal) as total_amount
        ')
        ->join('project p', 'p.id = i.project_id')
        ->join('client c', 'c.id = p.client_id')
        ->join('invoice_item ii', 'ii.invoice_id = i.id', 'left');
        
        if ($salesId) {
            $builder->where('c.sales_id', $salesId);
        }
        
        $builder->groupBy('c.id')
                ->orderBy('total_amount', 'DESC')
                ->limit($limit);
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Check if invoice number exists
     */
    public function isInvoiceNumberExists($nomorInvoice, $excludeId = null)
    {
        $builder = $this->db->table('invoice');
        $builder->where('nomor_invoice', $nomorInvoice);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }
    
    /**
     * Update invoice status based on payments
     */
    public function updateInvoiceStatus($invoiceId)
    {
        $invoice = $this->find($invoiceId);
        if (!$invoice) {
            return false;
        }
        
        $total = $this->calculateTotal($invoiceId);
        $paid = $this->getPaymentSummary($invoiceId);
        
        $newStatus = 'belum_bayar';
        
        if ($paid >= $total) {
            $newStatus = 'lunas';
        } elseif ($paid > 0) {
            $newStatus = 'sebagian';
        }
        
        // Check if overdue
        if ($newStatus != 'lunas' && strtotime($invoice['tanggal_jatuh_tempo']) < time()) {
            $newStatus = 'overdue';
        }
        
        return $this->update($invoiceId, ['status_pembayaran' => $newStatus]);
    }
    // Tambahkan method ini di InvoiceModel.php
public function createProjectFromModal($data)
{
    $projectData = [
        'kode_project' => $data['kode_project'],
        'nama_project' => $data['nama_project'],
        'client_id' => $data['client_id'],
        'deskripsi' => $data['deskripsi'] ?? null,
        'nilai_project' => $data['nilai_project'] ?? 0,
        'tanggal_mulai' => $data['tanggal_mulai'] ?? date('Y-m-d'),
        'status' => 'deal' // Otomatis deal jika dibuat dari invoice
    ];
    
    $projectModel = new \App\Models\ProjectModel();
    return $projectModel->insert($projectData, true);
}

public function getProjectsForSuratJalan($salesId = null)
{
    $builder = $this->db->table('project p');
    $builder->select('p.id, p.kode_project, p.nama_project, 
        c.nama_perusahaan, c.alamat, c.telepon, c.nama_kontak,
        i.id as invoice_id, i.nomor_invoice')
        ->join('client c', 'c.id = p.client_id')
        ->join('invoice i', 'i.project_id = p.id', 'left')
        ->where('p.status', 'deal')
        ->groupBy('p.id')
        ->orderBy('p.nama_project', 'ASC');
    
    if ($salesId) {
        $builder->where('c.sales_id', $salesId);
    }
    
    return $builder->get()->getResultArray();
}
}