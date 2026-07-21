<?php

namespace App\Models\Direktur;

use CodeIgniter\Model;

class PembayaranPiutangModel extends Model
{
    protected $table = 'pembayaran_piutang';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'invoice_id',
        'nomor_pembayaran',
        'tanggal_bayar',
        'jumlah_bayar',
        'metode_bayar',
        'bank_pengirim',
        'nomor_referensi',
        'bukti_bayar',
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
        'invoice_id' => 'required|integer',
        'nomor_pembayaran' => 'required|is_unique[pembayaran_piutang.nomor_pembayaran,id,{id}]',
        'tanggal_bayar' => 'required|valid_date',
        'jumlah_bayar' => 'required|numeric|greater_than[0]',
        'metode_bayar' => 'required|in_list[transfer,tunai,cek,giro]'
    ];
    
    protected $validationMessages = [
        'invoice_id' => [
            'required' => 'Invoice harus dipilih',
            'integer' => 'Invoice ID harus berupa angka'
        ],
        'nomor_pembayaran' => [
            'required' => 'Nomor pembayaran harus diisi',
            'is_unique' => 'Nomor pembayaran sudah digunakan'
        ],
        'tanggal_bayar' => [
            'required' => 'Tanggal bayar harus diisi',
            'valid_date' => 'Format tanggal bayar tidak valid'
        ],
        'jumlah_bayar' => [
            'required' => 'Jumlah bayar harus diisi',
            'numeric' => 'Jumlah bayar harus berupa angka',
            'greater_than' => 'Jumlah bayar harus lebih dari 0'
        ],
        'metode_bayar' => [
            'required' => 'Metode bayar harus dipilih',
            'in_list' => 'Metode bayar tidak valid'
        ]
    ];
    
    protected $skipValidation = false;
    
    /**
     * Payment method options
     */
    public static $metodeOptions = [
        'transfer' => ['label' => 'Transfer Bank', 'class' => 'primary'],
        'tunai' => ['label' => 'Tunai', 'class' => 'success'],
        'cek' => ['label' => 'Cek', 'class' => 'warning'],
        'giro' => ['label' => 'Giro', 'class' => 'info']
    ];
    
    /**
     * Get all payments with invoice and client info
     */
    public function getAllWithDetails($invoice_id = null, $startDate = null, $endDate = null)
    {
        $builder = $this->db->table($this->table)
            ->select('
                pembayaran_piutang.*,
                invoice_piutang.nomor_invoice,
                invoice_piutang.total as total_invoice,
                invoice_piutang.sisa_piutang as sisa_piutang,
                client.nama_perusahaan,
                client.nama_kontak,
                client.telepon
            ')
            ->join('invoice_piutang', 'invoice_piutang.id = pembayaran_piutang.invoice_id')
            ->join('client', 'client.id = invoice_piutang.client_id')
            ->where('pembayaran_piutang.deleted_at', null);
        
        if ($invoice_id) {
            $builder->where('pembayaran_piutang.invoice_id', $invoice_id);
        }
        
        if ($startDate) {
            $builder->where('pembayaran_piutang.tanggal_bayar >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('pembayaran_piutang.tanggal_bayar <=', $endDate);
        }
        
        $builder->orderBy('pembayaran_piutang.tanggal_bayar', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get payments by invoice
     */
    public function getByInvoice($invoice_id)
    {
        return $this->where('invoice_id', $invoice_id)
                    ->where('deleted_at', null)
                    ->orderBy('tanggal_bayar', 'DESC')
                    ->findAll();
    }
    
    /**
     * Get total payments for an invoice
     */
    public function getTotalPaidByInvoice($invoice_id)
    {
        $result = $this->db->table($this->table)
            ->select('SUM(jumlah_bayar) as total_paid')
            ->where('invoice_id', $invoice_id)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();
        
        return (float)($result['total_paid'] ?? 0);
    }
    
    /**
     * Get payment summary by date range
     */
    public function getSummaryByDateRange($startDate, $endDate)
    {
        $result = $this->db->table($this->table)
            ->select("
                COUNT(*) as jumlah_transaksi,
                SUM(jumlah_bayar) as total_pembayaran,
                SUM(CASE WHEN metode_bayar = 'transfer' THEN jumlah_bayar ELSE 0 END) as total_transfer,
                SUM(CASE WHEN metode_bayar = 'tunai' THEN jumlah_bayar ELSE 0 END) as total_tunai,
                SUM(CASE WHEN metode_bayar = 'cek' THEN jumlah_bayar ELSE 0 END) as total_cek,
                SUM(CASE WHEN metode_bayar = 'giro' THEN jumlah_bayar ELSE 0 END) as total_giro
            ")
            ->where('tanggal_bayar >=', $startDate)
            ->where('tanggal_bayar <=', $endDate)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();
        
        return $result ?: [
            'jumlah_transaksi' => 0,
            'total_pembayaran' => 0,
            'total_transfer' => 0,
            'total_tunai' => 0,
            'total_cek' => 0,
            'total_giro' => 0
        ];
    }
    
    /**
     * Get payment summary by month
     */
    public function getMonthlySummary($tahun = null)
    {
        if (!$tahun) {
            $tahun = date('Y');
        }
        
        $result = $this->db->table($this->table)
            ->select("
                MONTH(tanggal_bayar) as bulan,
                COUNT(*) as jumlah_transaksi,
                SUM(jumlah_bayar) as total_pembayaran
            ")
            ->where('YEAR(tanggal_bayar)', $tahun)
            ->where('deleted_at', null)
            ->groupBy('MONTH(tanggal_bayar)')
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
                    'jumlah_transaksi' => 0,
                    'total_pembayaran' => 0
                ];
            }
        }
        
        return $months;
    }
    
    /**
     * Get latest payments (for dashboard)
     */
    public function getLatestPayments($limit = 10)
    {
        return $this->db->table($this->table)
            ->select('
                pembayaran_piutang.*,
                invoice_piutang.nomor_invoice,
                client.nama_perusahaan
            ')
            ->join('invoice_piutang', 'invoice_piutang.id = pembayaran_piutang.invoice_id')
            ->join('client', 'client.id = invoice_piutang.client_id')
            ->where('pembayaran_piutang.deleted_at', null)
            ->orderBy('pembayaran_piutang.tanggal_bayar', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
    
    /**
     * Generate payment number
     */
    public function generatePaymentNumber()
    {
        $date = date('Ymd');
        $prefix = 'PMB-' . $date . '-';
        
        // Get last payment number for today
        $lastPayment = $this->db->table($this->table)
            ->select('nomor_pembayaran')
            ->like('nomor_pembayaran', $prefix, 'after')
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();
        
        if ($lastPayment) {
            $lastNumber = (int) substr($lastPayment['nomor_pembayaran'], -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $newNumber;
    }
    
    /**
     * Create payment and update invoice sisa piutang
     */
    public function createPayment($data, $userId = null)
    {
        // Generate payment number if not provided
        if (empty($data['nomor_pembayaran'])) {
            $data['nomor_pembayaran'] = $this->generatePaymentNumber();
        }
        
        // Add created_by
        if ($userId) {
            $data['created_by'] = $userId;
        }
        
        // Start transaction
        $this->db->transStart();
        
        // Insert payment
        $paymentId = $this->insert($data);
        
        if ($paymentId) {
            // Update invoice sisa piutang
            $invoiceModel = new InvoicePiutangModel();
            $invoiceModel->updateSisaPiutang($data['invoice_id']);
        }
        
        $this->db->transComplete();
        
        if ($this->db->transStatus() === false) {
            return false;
        }
        
        return $paymentId;
    }
    
    /**
     * Delete payment and update invoice sisa piutang
     */
    public function deletePayment($id, $purge = false)
    {
        // Get payment data before delete
        $payment = $this->find($id);
        if (!$payment) {
            return false;
        }
        
        $invoice_id = $payment['invoice_id'];
        
        // Start transaction
        $this->db->transStart();
        
        // Delete payment
        $result = $purge ? $this->purgeDeleted($id) : $this->delete($id);
        
        if ($result) {
            // Update invoice sisa piutang
            $invoiceModel = new InvoicePiutangModel();
            $invoiceModel->updateSisaPiutang($invoice_id);
        }
        
        $this->db->transComplete();
        
        return $result;
    }
    
    /**
     * Get payment method label
     */
    public function getMetodeLabel($metode)
    {
        return self::$metodeOptions[$metode]['label'] ?? $metode;
    }
    
    /**
     * Get payment method class
     */
    public function getMetodeClass($metode)
    {
        return self::$metodeOptions[$metode]['class'] ?? 'secondary';
    }
    
    /**
     * Format currency to Rupiah
     */
    public function formatRupiah($amount)
    {
        return 'Rp ' . number_format((float)$amount, 0, ',', '.');
    }
    
    /**
     * Validate payment amount does not exceed invoice remaining balance
     */
    public function validatePaymentAmount($invoice_id, $jumlah_bayar, $exclude_payment_id = null)
    {
        $invoiceModel = new InvoicePiutangModel();
        $invoice = $invoiceModel->find($invoice_id);
        
        if (!$invoice) {
            return false;
        }
        
        // Get total paid (excluding current payment if editing)
        $builder = $this->db->table($this->table)
            ->select('SUM(jumlah_bayar) as total_paid')
            ->where('invoice_id', $invoice_id)
            ->where('deleted_at', null);
        
        if ($exclude_payment_id) {
            $builder->where('id !=', $exclude_payment_id);
        }
        
        $result = $builder->get()->getRowArray();
        $totalPaid = (float)($result['total_paid'] ?? 0);
        
        $remaining = $invoice['total'] - $totalPaid;
        
        return $jumlah_bayar <= $remaining;
    }
    
    /**
     * Get remaining balance for an invoice
     */
    public function getRemainingBalance($invoice_id)
    {
        $invoiceModel = new InvoicePiutangModel();
        $invoice = $invoiceModel->find($invoice_id);
        
        if (!$invoice) {
            return 0;
        }
        
        $totalPaid = $this->getTotalPaidByInvoice($invoice_id);
        
        return $invoice['total'] - $totalPaid;
    }
}