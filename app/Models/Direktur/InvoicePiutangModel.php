<?php

namespace App\Models\Direktur;

use CodeIgniter\Model;

class InvoicePiutangModel extends Model
{
    protected $table = 'invoice_piutang';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'nomor_invoice',
        'client_id',
        'project_id',
        'tanggal_invoice',
        'tanggal_jatuh_tempo',
        'deskripsi',
        'subtotal',
        'ppn',
        'total',
        'sisa_piutang',
        'status',
        'keterangan',
        'created_by'
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    
    // Validation rules
    protected $validationRules = [
        'nomor_invoice' => 'required|is_unique[invoice_piutang.nomor_invoice,id,{id}]',
        'client_id' => 'required|integer',
        'tanggal_invoice' => 'required|valid_date',
        'tanggal_jatuh_tempo' => 'required|valid_date',
        'subtotal' => 'required|numeric',
        'total' => 'required|numeric',
        'sisa_piutang' => 'required|numeric',
        'status' => 'required|in_list[draft,sent,partial,paid,overdue,cancelled]'
    ];
    
    protected $validationMessages = [
        'nomor_invoice' => [
            'required' => 'Nomor invoice harus diisi',
            'is_unique' => 'Nomor invoice sudah digunakan'
        ],
        'client_id' => [
            'required' => 'Client harus dipilih',
            'integer' => 'Client ID harus berupa angka'
        ],
        'tanggal_invoice' => [
            'required' => 'Tanggal invoice harus diisi',
            'valid_date' => 'Format tanggal invoice tidak valid'
        ],
        'tanggal_jatuh_tempo' => [
            'required' => 'Tanggal jatuh tempo harus diisi',
            'valid_date' => 'Format tanggal jatuh tempo tidak valid'
        ]
    ];
    
    protected $skipValidation = false;
    
    /**
     * Status options
     */
    public static $statusOptions = [
        'draft' => ['label' => 'Draft', 'class' => 'secondary'],
        'sent' => ['label' => 'Dikirim', 'class' => 'info'],
        'partial' => ['label' => 'Sebagian Dibayar', 'class' => 'warning'],
        'paid' => ['label' => 'Lunas', 'class' => 'success'],
        'overdue' => ['label' => 'Overdue', 'class' => 'danger'],
        'cancelled' => ['label' => 'Dibatalkan', 'class' => 'dark']
    ];
    
    /**
     * Get all invoices with client info
     */
    public function getAllWithClient($status = null, $startDate = null, $endDate = null)
    {
        $builder = $this->db->table($this->table)
            ->select('invoice_piutang.*, client.nama_perusahaan, client.nama_kontak, client.telepon, client.alamat')
            ->join('client', 'client.id = invoice_piutang.client_id')
            ->where('invoice_piutang.deleted_at', null);
        
        if ($status) {
            $builder->where('invoice_piutang.status', $status);
        }
        
        if ($startDate) {
            $builder->where('invoice_piutang.tanggal_invoice >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('invoice_piutang.tanggal_invoice <=', $endDate);
        }
        
        $builder->orderBy('invoice_piutang.tanggal_invoice', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get invoice by ID with client info
     */
    public function getWithClient($id)
    {
        return $this->db->table($this->table)
            ->select('invoice_piutang.*, client.nama_perusahaan, client.nama_kontak, client.telepon, client.alamat, client.email_client')
            ->join('client', 'client.id = invoice_piutang.client_id')
            ->where('invoice_piutang.id', $id)
            ->where('invoice_piutang.deleted_at', null)
            ->get()
            ->getRowArray();
    }
    
    /**
     * Get invoices by client
     */
    public function getByClient($client_id)
    {
        return $this->where('client_id', $client_id)
                    ->where('deleted_at', null)
                    ->orderBy('tanggal_invoice', 'DESC')
                    ->findAll();
    }
    
    /**
     * Get overdue invoices
     */
    public function getOverdueInvoices()
    {
        $today = date('Y-m-d');
        
        return $this->db->table($this->table)
            ->select('invoice_piutang.*, client.nama_perusahaan, client.nama_kontak, client.telepon')
            ->join('client', 'client.id = invoice_piutang.client_id')
            ->where('invoice_piutang.status', 'sent')
            ->where('invoice_piutang.tanggal_jatuh_tempo <', $today)
            ->where('invoice_piutang.sisa_piutang >', 0)
            ->where('invoice_piutang.deleted_at', null)
            ->orderBy('invoice_piutang.tanggal_jatuh_tempo', 'ASC')
            ->get()
            ->getResultArray();
    }
    
    /**
     * Get invoices nearing due date (7 days)
     */
    public function getNearingDueInvoices($days = 7)
    {
        $today = date('Y-m-d');
        $nearingDate = date('Y-m-d', strtotime("+{$days} days"));
        
        return $this->db->table($this->table)
            ->select('invoice_piutang.*, client.nama_perusahaan, client.nama_kontak, client.telepon')
            ->join('client', 'client.id = invoice_piutang.client_id')
            ->where('invoice_piutang.status', 'sent')
            ->where('invoice_piutang.tanggal_jatuh_tempo >=', $today)
            ->where('invoice_piutang.tanggal_jatuh_tempo <=', $nearingDate)
            ->where('invoice_piutang.sisa_piutang >', 0)
            ->where('invoice_piutang.deleted_at', null)
            ->orderBy('invoice_piutang.tanggal_jatuh_tempo', 'ASC')
            ->get()
            ->getResultArray();
    }
    
    /**
     * Get summary statistics for dashboard
     */
    public function getSummaryStats()
    {
        $result = $this->db->table($this->table)
            ->select("
                COUNT(*) as total_invoice,
                SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as total_draft,
                SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as total_sent,
                SUM(CASE WHEN status = 'partial' THEN 1 ELSE 0 END) as total_partial,
                SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as total_paid,
                SUM(CASE WHEN status = 'overdue' THEN 1 ELSE 0 END) as total_overdue,
                SUM(total) as total_nilai_invoice,
                SUM(sisa_piutang) as total_piutang,
                SUM(CASE WHEN status IN ('draft','sent','partial','overdue') THEN sisa_piutang ELSE 0 END) as total_piutang_belum_dibayar,
                SUM(CASE WHEN status = 'paid' THEN total ELSE 0 END) as total_terbayar
            ")
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();
        
        return $result ?: [
            'total_invoice' => 0,
            'total_draft' => 0,
            'total_sent' => 0,
            'total_partial' => 0,
            'total_paid' => 0,
            'total_overdue' => 0,
            'total_nilai_invoice' => 0,
            'total_piutang' => 0,
            'total_piutang_belum_dibayar' => 0,
            'total_terbayar' => 0
        ];
    }
    
    /**
     * Get summary by client
     */
    public function getSummaryByClient()
    {
        return $this->db->table($this->table)
            ->select("
                client.id as client_id,
                client.nama_perusahaan,
                COUNT(invoice_piutang.id) as jumlah_invoice,
                SUM(invoice_piutang.total) as total_nilai,
                SUM(invoice_piutang.sisa_piutang) as total_piutang,
                MAX(invoice_piutang.tanggal_jatuh_tempo) as terakhir_jatuh_tempo
            ")
            ->join('client', 'client.id = invoice_piutang.client_id')
            ->where('invoice_piutang.deleted_at', null)
            ->where('invoice_piutang.status !=', 'paid')
            ->where('invoice_piutang.status !=', 'cancelled')
            ->groupBy('client.id')
            ->orderBy('total_piutang', 'DESC')
            ->get()
            ->getResultArray();
    }
    
    /**
     * Get monthly summary
     */
    public function getMonthlySummary($tahun = null)
    {
        if (!$tahun) {
            $tahun = date('Y');
        }
        
        $result = $this->db->table($this->table)
            ->select("
                MONTH(tanggal_invoice) as bulan,
                COUNT(*) as jumlah_invoice,
                SUM(total) as total_nilai,
                SUM(CASE WHEN status = 'paid' THEN total ELSE 0 END) as total_terbayar,
                SUM(CASE WHEN status IN ('sent','partial','overdue') THEN sisa_piutang ELSE 0 END) as total_piutang
            ")
            ->where('YEAR(tanggal_invoice)', $tahun)
            ->where('deleted_at', null)
            ->groupBy('MONTH(tanggal_invoice)')
            ->orderBy('bulan', 'ASC')
            ->get()
            ->getResultArray();
        
        // Fill missing months
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $found = false;
            foreach ($result as $row) {
                if ((int)$row['bulan'] === $i) {
                    $months[$i] = $row;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $months[$i] = [
                    'bulan' => $i,
                    'jumlah_invoice' => 0,
                    'total_nilai' => 0,
                    'total_terbayar' => 0,
                    'total_piutang' => 0
                ];
            }
        }
        
        return $months;
    }
    
    /**
     * Update sisa piutang after payment
     */
    public function updateSisaPiutang($invoice_id)
    {
        // Get total payments for this invoice
        $paymentModel = new \App\Models\Direktur\PembayaranPiutangModel();
        $totalPaid = $paymentModel->where('invoice_id', $invoice_id)
                                  ->where('deleted_at', null)
                                  ->selectSum('jumlah_bayar')
                                  ->get()
                                  ->getRowArray();
        
        $totalPaidAmount = $totalPaid['jumlah_bayar'] ?? 0;
        
        // Get invoice
        $invoice = $this->find($invoice_id);
        if (!$invoice) {
            return false;
        }
        
        $sisa_piutang = $invoice['total'] - $totalPaidAmount;
        
        // Determine new status
        $status = $invoice['status'];
        if ($sisa_piutang <= 0) {
            $status = 'paid';
            $sisa_piutang = 0;
        } elseif ($totalPaidAmount > 0) {
            $status = 'partial';
        }
        
        // Also check if overdue
        if ($status != 'paid' && strtotime($invoice['tanggal_jatuh_tempo']) < strtotime(date('Y-m-d'))) {
            $status = 'overdue';
        }
        
        return $this->update($invoice_id, [
            'sisa_piutang' => $sisa_piutang,
            'status' => $status
        ]);
    }
    
    /**
     * Generate invoice number
     */
    public function generateInvoiceNumber()
    {
        $date = date('Ymd');
        $prefix = 'INV-' . $date . '-';
        
        // Get last invoice number for today
        $lastInvoice = $this->db->table($this->table)
            ->select('nomor_invoice')
            ->like('nomor_invoice', $prefix, 'after')
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();
        
        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice['nomor_invoice'], -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $newNumber;
    }
    
    /**
     * Calculate total with PPN (11%)
     */
    public function calculateTotal($subtotal, $ppn_percentage = 11)
    {
        $ppn = $subtotal * ($ppn_percentage / 100);
        $total = $subtotal + $ppn;
        
        return [
            'ppn' => round($ppn, 2),
            'total' => round($total, 2)
        ];
    }
    
    /**
     * Create invoice with auto calculation
     */
    public function createInvoice($data, $userId = null)
    {
        // Generate invoice number if not provided
        if (empty($data['nomor_invoice'])) {
            $data['nomor_invoice'] = $this->generateInvoiceNumber();
        }
        
        // Calculate PPN and total
        $subtotal = $data['subtotal'] ?? 0;
        $calculations = $this->calculateTotal($subtotal);
        $data['ppn'] = $calculations['ppn'];
        $data['total'] = $calculations['total'];
        $data['sisa_piutang'] = $calculations['total'];
        
        // Set default status
        if (empty($data['status'])) {
            $data['status'] = 'draft';
        }
        
        // Add created_by
        if ($userId) {
            $data['created_by'] = $userId;
        }
        
        return $this->insert($data);
    }
    
    /**
     * Get aging report (30, 60, 90 days)
     */
    public function getAgingReport()
    {
        $today = date('Y-m-d');
        
        $builder = $this->db->table($this->table)
            ->select("
                invoice_piutang.*,
                client.nama_perusahaan,
                client.nama_kontak,
                client.telepon,
                DATEDIFF('{$today}', tanggal_jatuh_tempo) as days_overdue
            ")
            ->join('client', 'client.id = invoice_piutang.client_id')
            ->where('invoice_piutang.status !=', 'paid')
            ->where('invoice_piutang.status !=', 'cancelled')
            ->where('invoice_piutang.sisa_piutang >', 0)
            ->where('invoice_piutang.deleted_at', null)
            ->orderBy('invoice_piutang.tanggal_jatuh_tempo', 'ASC');
        
        $invoices = $builder->get()->getResultArray();
        
        $aging = [
            'current' => [],      // 0-30 days
            '31_60' => [],        // 31-60 days
            '61_90' => [],        // 61-90 days
            '90_plus' => []       // > 90 days
        ];
        
        foreach ($invoices as $invoice) {
            $daysOverdue = (int)($invoice['days_overdue'] ?? 0);
            
            if ($daysOverdue <= 0) {
                $aging['current'][] = $invoice;
            } elseif ($daysOverdue <= 30) {
                $aging['31_60'][] = $invoice;
            } elseif ($daysOverdue <= 60) {
                $aging['61_90'][] = $invoice;
            } else {
                $aging['90_plus'][] = $invoice;
            }
        }
        
        return $aging;
    }
    
    /**
     * Get status label with badge class
     */
    public function getStatusLabel($status)
    {
        return self::$statusOptions[$status]['label'] ?? $status;
    }
    
    /**
     * Get status badge class
     */
    public function getStatusClass($status)
    {
        return self::$statusOptions[$status]['class'] ?? 'secondary';
    }
    
    /**
     * Format currency to Rupiah
     */
    public function formatRupiah($amount)
    {
        return 'Rp ' . number_format((float)$amount, 0, ',', '.');
    }
}