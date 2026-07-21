<?php

namespace App\Models;

use CodeIgniter\Model;

class PembayaranModel extends Model
{
    protected $table = 'pembayaran';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'invoice_id', 'nomor_pembayaran', 'tanggal_bayar', 'jumlah_bayar',
        'metode_bayar', 'bank', 'no_referensi', 'keterangan', 'created_by'
    ];
    
    // PERBAIKAN: Hanya gunakan created_at
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = ''; // Kosongkan karena tidak ada updated_at
    
  public function generateNomorPembayaran()
    {
        $year = date('Y');
        $month = date('m');
        
        $lastPayment = $this->select('nomor_pembayaran')
            ->where('YEAR(created_at)', $year)
            ->where('MONTH(created_at)', $month)
            ->orderBy('id', 'DESC')
            ->first();
        
        if ($lastPayment) {
            $lastNumber = explode('/', $lastPayment['nomor_pembayaran']);
            $sequence = intval($lastNumber[0]) + 1;
        } else {
            $sequence = 1;
        }
        
        $sequenceFormatted = str_pad($sequence, 4, '0', STR_PAD_LEFT);
        
        return sprintf('%04d/PAY/%s/%s', 
            $sequenceFormatted, 
            date('m'), 
            date('Y')
        );
    }
    
   /**
     * Get payments by invoice
     */
    public function getPaymentsByInvoice($invoiceId)
    {
        $builder = $this->db->table('pembayaran p');
        $builder->select('p.*, u.name as created_by_name')
            ->join('users u', 'u.id = p.created_by', 'left')
            ->where('p.invoice_id', $invoiceId)
            ->orderBy('p.tanggal_bayar', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get total payment for invoice
     */
    public function getTotalPayment($invoiceId)
    {
        $builder = $this->db->table($this->table);
        $builder->selectSum('jumlah_bayar');
        $builder->where('invoice_id', $invoiceId);
        $result = $builder->get()->getRowArray();
        
        return $result ? floatval($result['jumlah_bayar']) : 0;
    }
    
       /**
     * Check payment number exists
     */
    public function isPaymentNumberExists($nomorPembayaran, $excludeId = null)
    {
        $builder = $this->db->table($this->table);
        $builder->where('nomor_pembayaran', $nomorPembayaran);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }
}